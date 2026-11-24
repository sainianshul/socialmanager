<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AutoDmRecipient;
use App\Models\AutoDmRule;
use App\Models\Comment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaMessagingService
{
    private string $graphApiVersion;

    public function __construct(string $version = 'v20.0')
    {
        $this->graphApiVersion = $version;
    }

    /**
     * Execute an Auto DM rule for a matching comment:
     * 1. Sends private message to commenter's inbox (Messenger / Instagram Direct).
     * 2. If rule has public_reply configured, publishes public reply on the comment.
     * 3. Records trigger on the rule and updates comment.
     *
     * @param AutoDmRule $rule
     * @param Comment $comment
     * @return array [
     *     'success' => bool,
     *     'message' => string,
     *     'dm_sent' => bool,
     *     'dm_id' => ?string,
     *     'public_reply_sent' => bool,
     *     'public_reply_id' => ?string
     * ]
     */
    public function sendAutoDm(AutoDmRule $rule, Comment $comment): array
    {
        $account = $rule->account ?? $comment->account;

        if (!$account || empty($account->access_token)) {
            Log::warning("Auto DM: Account {$account?->id} has no valid access token.");
            return [
                'success' => false,
                'message' => 'No active access token for this social channel.',
                'dm_sent' => false,
                'public_reply_sent' => false,
            ];
        }

        $commentId = $comment->effective_comment_id ?: $comment->meta_comment_id;
        if (empty($commentId)) {
            return [
                'success' => false,
                'message' => 'Comment has no valid external Meta ID.',
                'dm_sent' => false,
                'public_reply_sent' => false,
            ];
        }

        $commenterId = $comment->commenter_meta_id ?: $comment->commenter_name;

        // Meta Platform Policy Compliance Guard: Private reply (DM) must be to a comment posted within 7 days
        if (!empty($comment->commented_at) && $comment->commented_at->lt(now()->subDays(7))) {
            Log::info("Auto DM Policy Guard: Skipped DM for comment #{$comment->id} because comment is older than 7 days ({$comment->commented_at->diffForHumans()}).");
            return [
                'success'           => false,
                'message'           => 'Meta Platform Policy restriction: Cannot send private reply to comments older than 7 days.',
                'dm_sent'           => false,
                'public_reply_sent' => false,
            ];
        }

        // Frequency Capping: Maximum 1 Auto-DM per recipient per account within 24 hours
        // Protects against Instagram & Facebook spam blocks caused by repeated DMs to the same user
        if (!empty($commenterId) && AutoDmRecipient::isCapped($account->id, (string) $commenterId, 24)) {
            Log::info("Auto DM Frequency Cap: Skipped DM to commenter [{$commenterId}] on account #{$account->id} (already sent within 24 hours).");
            return [
                'success'           => false,
                'message'           => 'Frequency capped: Auto-DM already sent to this user within the last 24 hours.',
                'dm_sent'           => false,
                'frequency_capped'  => true,
                'public_reply_sent' => false,
            ];
        }

        // Personalize message with commenter name placeholder if present ({name}, {first_name})
        $commenterName = trim((string) ($comment->commenter_name ?: ''));
        $firstName = $commenterName !== '' ? (explode(' ', $commenterName)[0] ?? '') : '';

        $personalizedDm = $rule->dm_message;
        if ($commenterName !== '') {
            $personalizedDm = str_ireplace(['{name}', '{first_name}'], [$commenterName, $firstName], $personalizedDm);
        } else {
            $personalizedDm = str_ireplace(['{name}', '{first_name}'], ['friend', 'friend'], $personalizedDm);
        }

        // Spintax Resolver: Automatically resolves {Namaste|Hello|Hi} or any {option1|option2}
        // Guarantees unique wording per recipient, completely preventing Instagram repetitive message hash blocks
        $personalizedDm = self::resolveSpintax($personalizedDm);

        // 1. Send the Private Direct Message (DM)
        $dmResult = $this->sendPrivateReply($account, $commentId, $personalizedDm);

        // 2. Optionally publish Public Comment Reply if configured
        $publicReplyResult = ['success' => false, 'reply_id' => null];
        if (!empty($rule->public_reply)) {
            $personalizedPublicReply = $rule->public_reply;
            if ($commenterName !== '') {
                $personalizedPublicReply = str_ireplace(['{name}', '{first_name}'], [$commenterName, $firstName], $personalizedPublicReply);
            } else {
                $personalizedPublicReply = str_ireplace(['{name}', '{first_name}'], ['ji', ''], $personalizedPublicReply);
            }
            $personalizedPublicReply = self::resolveSpintax($personalizedPublicReply);
            $publicReplyResult = $this->sendPublicReply($account, $commentId, $personalizedPublicReply);
        }

        // 3. If DM succeeded, record trigger on rule, track frequency cap, and update comment status
        if ($dmResult['success']) {
            if (!empty($commenterId)) {
                AutoDmRecipient::recordDispatch($account->id, (string) $commenterId);
            }

            $rule->recordTrigger();

            $updateData = [
                'replied_at' => now(),
            ];

            if ($publicReplyResult['success']) {
                $updateData['status'] = Comment::STATUS_REPLIED;
                $updateData['ai_reply'] = $personalizedPublicReply ?? $rule->public_reply;
                $updateData['meta_reply_id'] = $publicReplyResult['reply_id'];
            }

            $comment->update($updateData);

            Log::info("Auto DM rule [{$rule->name}] triggered successfully for comment #{$comment->id}");

            return [
                'success' => true,
                'message' => 'Auto DM delivered successfully.',
                'dm_sent' => true,
                'dm_id' => $dmResult['message_id'] ?? null,
                'public_reply_sent' => $publicReplyResult['success'],
                'public_reply_id' => $publicReplyResult['reply_id'] ?? null,
                'public_reply' => $personalizedPublicReply ?? $rule->public_reply,
            ];
        }

        return [
            'success' => false,
            'action_blocked' => !empty($dmResult['action_blocked']),
            'message' => $dmResult['message'] ?? 'Failed to send private direct message.',
            'dm_sent' => false,
            'public_reply_sent' => $publicReplyResult['success'],
        ];
    }

    /**
     * Send a private reply to a Facebook or Instagram comment
     *
     * @param Account $account
     * @param string $commentId
     * @param string $message
     * @return array ['success' => bool, 'message' => string, 'message_id' => ?string]
     */
    public function sendPrivateReply(Account $account, string $commentId, string $message): array
    {
        if (empty($account->access_token)) {
            return ['success' => false, 'message' => 'Missing access token for account.'];
        }

        if ($account->isInstagram()) {
            return $this->sendInstagramPrivateReply($account, $commentId, $message);
        }

        return $this->sendFacebookPrivateReply($account, $commentId, $message);
    }

    /**
     * Facebook Page private reply to comment
     * Endpoint: POST https://graph.facebook.com/{version}/{comment_id}/private_replies
     */
    protected function sendFacebookPrivateReply(Account $account, string $commentId, string $message): array
    {
        $url = "https://graph.facebook.com/{$this->graphApiVersion}/{$commentId}/private_replies";

        try {
            $response = Http::timeout(20)->post($url, [
                'message' => $message,
                'access_token' => $account->access_token,
            ])->json();

            if (isset($response['error'])) {
                $errorMsg = $response['error']['message'] ?? 'Meta API error sending private reply.';
                $errorCode = $response['error']['code'] ?? null;
                $errorSubcode = $response['error']['error_subcode'] ?? null;

                Log::warning("Facebook Private Reply error [{$errorCode}/{$errorSubcode}]: {$errorMsg}", [
                    'comment_id' => $commentId,
                    'account_id' => $account->id,
                ]);

                // Code 10900: A private reply has already been sent to this comment
                if ($errorCode == 10900 || str_contains($errorMsg, 'private reply was already sent')) {
                    return [
                        'success' => false,
                        'already_sent' => true,
                        'message' => 'A private reply was already sent to this comment.',
                    ];
                }

                // Code 368: Meta temporary action block (going too fast)
                if ($errorCode == 368 || str_contains($errorMsg, 'temporarily blocked')) {
                    return [
                        'success' => false,
                        'action_blocked' => true,
                        'message' => 'Meta temporary action block (#368): ' . $errorMsg,
                    ];
                }

                return ['success' => false, 'message' => $errorMsg];
            }

            $messageId = (string) ($response['id'] ?? null);

            return [
                'success' => true,
                'message' => 'Facebook private reply sent successfully.',
                'message_id' => $messageId,
            ];
        } catch (\Throwable $e) {
            Log::error("Facebook private reply exception: {$e->getMessage()}");
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Instagram Business private reply to comment
     * Primary: POST https://graph.facebook.com/{version}/{ig_user_id}/messages with recipient: {"comment_id": "..."}
     * Fallback: POST https://graph.facebook.com/{version}/{comment_id}/private_replies
     */
    protected function sendInstagramPrivateReply(Account $account, string $commentId, string $message): array
    {
        $igId = $account->instagram_business_account_id ?? $account->meta_account_id;
        $url = "https://graph.facebook.com/{$this->graphApiVersion}/{$igId}/messages";

        try {
            $response = Http::timeout(20)->post($url, [
                'recipient' => [
                    'comment_id' => $commentId,
                ],
                'message' => [
                    'text' => $message,
                ],
                'access_token' => $account->access_token,
            ])->json();

            if (isset($response['error'])) {
                // Try fallback endpoint: POST /{comment_id}/private_replies
                $fallbackUrl = "https://graph.facebook.com/{$this->graphApiVersion}/{$commentId}/private_replies";
                $fallbackResp = Http::timeout(20)->post($fallbackUrl, [
                    'message' => $message,
                    'access_token' => $account->access_token,
                ])->json();

                if (!isset($fallbackResp['error'])) {
                    return [
                        'success' => true,
                        'message' => 'Instagram private reply sent successfully via fallback.',
                        'message_id' => (string) ($fallbackResp['id'] ?? null),
                    ];
                }

                $errorMsg = $response['error']['message'] ?? 'Instagram API error sending private message.';
                $errorCode = $response['error']['code'] ?? null;
                Log::warning("Instagram Private Reply error: {$errorMsg}", [
                    'comment_id' => $commentId,
                    'ig_id' => $igId,
                    'response' => $response,
                ]);

                if ($errorCode == 368 || str_contains($errorMsg, 'temporarily blocked') || $errorCode == 613) {
                    return [
                        'success' => false,
                        'action_blocked' => true,
                        'message' => 'Instagram temporary action block: ' . $errorMsg,
                    ];
                }

                return ['success' => false, 'message' => $errorMsg];
            }

            $messageId = (string) ($response['message_id'] ?? ($response['id'] ?? null));

            return [
                'success' => true,
                'message' => 'Instagram private reply sent successfully.',
                'message_id' => $messageId,
            ];
        } catch (\Throwable $e) {
            Log::error("Instagram private reply exception: {$e->getMessage()}");
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Send a public reply to a comment on Meta (Facebook Page or Instagram)
     *
     * @param Account $account
     * @param string $commentId
     * @param string $replyMessage
     * @return array ['success' => bool, 'message' => string, 'reply_id' => ?string]
     */
    public function sendPublicReply(Account $account, string $commentId, string $replyMessage): array
    {
        if (empty($account->access_token)) {
            return ['success' => false, 'message' => 'Missing access token for account.'];
        }

        $endpoint = $account->isInstagram() ? 'replies' : 'comments';
        $url = "https://graph.facebook.com/{$this->graphApiVersion}/{$commentId}/{$endpoint}";

        try {
            $response = Http::timeout(20)->post($url, [
                'message' => $replyMessage,
                'access_token' => $account->access_token,
            ])->json();

            if (isset($response['error'])) {
                $msg = $response['error']['message'] ?? 'Meta Graph API public reply error.';
                Log::warning("Public reply failed for comment {$commentId}: {$msg}");
                return ['success' => false, 'message' => $msg];
            }

            $replyId = (string) ($response['id'] ?? null);

            return [
                'success' => true,
                'message' => 'Public comment reply posted successfully.',
                'reply_id' => $replyId,
            ];
        } catch (\Throwable $e) {
            Log::error("Public reply exception: {$e->getMessage()}");
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Resolve Spintax syntax formatted as {option1|option2|option3} into a random natural variant.
     */
    public static function resolveSpintax(string $text): string
    {
        return preg_replace_callback('/\{([^{}]+)\}/u', function ($matches) {
            $inner = trim($matches[1]);
            if (in_array(strtolower($inner), ['name', 'first_name'])) {
                return $matches[0];
            }
            $parts = explode('|', $inner);
            return trim($parts[array_rand($parts)]);
        }, $text);
    }
}
