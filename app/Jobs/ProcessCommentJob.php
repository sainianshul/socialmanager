<?php

namespace App\Jobs;

use App\Contracts\AIServiceInterface;
use App\Models\Account;
use App\Models\AutoDmRule;
use App\Models\Comment;
use App\Services\MetaMessagingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Redis;

class ProcessCommentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Comment $comment;

    /**
     * Create a new job instance.
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Execute the job with account-level rate throttling to protect Meta & YouTube API quotas.
     */
    public function handle(): void
    {
        $comment = $this->comment->fresh();
        if (!$comment) {
            return;
        }

        $account = $comment->account;

        // Defensive: Do not process or reply to the channel's own comments/replies
        if ($account && (
            ($comment->commenter_meta_id && $comment->commenter_meta_id === (string) $account->meta_account_id) ||
            stripos((string) $comment->commenter_name, '(Page)') !== false ||
            ($account->name && strcasecmp((string) $comment->commenter_name, $account->name) === 0)
        )) {
            return;
        }

        // Apply account-level throttling (25 API requests / 60 seconds per account)
        // Prevents Meta OAuthException: (#32) Page request limit reached during viral post spikes
        if ($account) {
            $this->executeThrottled($account, function () use ($comment, $account) {
                $this->processComment($comment, $account);
            });
            return;
        }

        $this->processComment($comment, null);
    }

    /**
     * Account-level rate limiter to prevent Meta Graph API & YouTube Data API throttling during viral spikes.
     */
    protected function executeThrottled(Account $account, callable $callback): void
    {
        $throttleKey = 'meta_api_rate_' . $account->id;

        // 1. Try Redis throttling if Redis queue connection is active
        try {
            if (config('queue.default') === 'redis' && class_exists(Redis::class)) {
                Redis::throttle($throttleKey)
                    ->allow(25)
                    ->every(60)
                    ->then(function () use ($callback) {
                        $callback();
                    }, function () {
                        Log::info("ProcessCommentJob: Account #{$this->comment->account_id} rate limit reached (Redis throttle), releasing job back to queue with 30s delay.");
                        if ($this->job) {
                            $this->release(30);
                        }
                    });
                return;
            }
        } catch (\Throwable $redisEx) {
            Log::debug("ProcessCommentJob: Redis throttle unavailable, falling back to RateLimiter: " . $redisEx->getMessage());
        }

        // 2. Resilient fallback using Laravel RateLimiter (works in database/sync queue and test environments)
        $executed = RateLimiter::attempt(
            $throttleKey,
            25,
            function () use ($callback) {
                $callback();
            },
            60
        );

        if (!$executed) {
            Log::info("ProcessCommentJob: Account #{$this->comment->account_id} rate limit reached (RateLimiter), releasing job back to queue with 30s delay.");
            if ($this->job) {
                $this->release(30);
            }
        }
    }

    /**
     * Core comment moderation, AI analysis, and auto-reply dispatch
     */
    protected function processComment(Comment $comment, ?Account $account): void
    {
        $post = $comment->post;
        $rawText = trim($comment->message ?? '');
        $text = strtolower($rawText);

        $aiTone = $account?->ai_tone ?: 'Humble & Respectful';
        $aiLang = $account?->ai_language ?: 'Hindi + Hinglish';

        // 1. Try Deep Analysis via Active AI Service (Gemini, OpenAI, Qwen, DeepSeek)
        $aiService = app(AIServiceInterface::class);
        $aiResult = null;

        if ($aiService->isConfigured() && !empty($rawText)) {
            $aiResult = $aiService->analyzeAndDraftReply($rawText, [
                'commenter_name' => $comment->commenter_name,
                'post_caption'   => $post?->caption ?: $post?->title ?: '',
                'account_name'   => $account?->name ?: 'Official Page',
                'tone'           => $aiTone,
                'language'       => $aiLang,
            ]);
        }

        // 2. Fallback to Local Smart Rules if Gemini is unconfigured or unavailable
        if ($aiResult) {
            $sentiment = $aiResult['sentiment'];
            $sentimentScore = $aiResult['sentiment_score'];
            $category = $aiResult['category'];
            $isAbusive = $aiResult['is_abusive'];
            $abuseConfidence = $aiResult['abuse_confidence'];
            $isComplaint = $aiResult['is_complaint'];
            $isFavor = $aiResult['is_favor'];
            $isAgainst = $aiResult['is_against'];
            $aiReply = $aiResult['ai_reply'];
        } else {
            $analysis = $this->analyzeMessage($text);

            $sentiment = $analysis['sentiment'];
            $sentimentScore = $analysis['sentiment_score'];
            $category = $analysis['category'];
            $isAbusive = $analysis['is_abusive'];
            $abuseConfidence = $analysis['abuse_confidence'];
            $isComplaint = $analysis['is_complaint'];
            $isFavor = $analysis['is_favor'];
            $isAgainst = $analysis['is_against'];

            $aiReply = null;
            if (!$isAbusive) {
                $aiReply = $this->generateDraftReply($category, $aiTone, $aiLang, $comment->commenter_name);
            }
        }

        $shouldHide = $isAbusive || ($category === 'troll');
        $replyNeeded = ($category === 'inquiry' || $isComplaint || $isFavor) && !$isAbusive;

        // Auto Reply Mode: 0 = Off, 1 = With Approval (Draft), 2 = Auto Approved (Instant Live)
        $autoReplyMode = $account ? (int) $account->auto_reply_mode : Account::AUTO_REPLY_WITH_APPROVAL;

        // Mode 0: Auto comment off -> Do not retain or formulate any reply
        if ($autoReplyMode === Account::AUTO_REPLY_OFF) {
            $aiReply = null;
        }

        // 3. Status & Auto-Moderation Decision
        $status = $comment->status;
        $hiddenAt = $comment->hidden_at;
        $repliedAt = $comment->replied_at;

        if ($account?->auto_hide_trolls && $shouldHide) {
            $status = Comment::STATUS_HIDDEN;
            $hiddenAt = now();

            // Also hide on Meta / YouTube via live API
            try {
                if ($account->isYouTube()) {
                    app(\App\Services\YouTubeCommentSyncService::class)->setModerationStatus($comment, true);
                } else {
                    app(\App\Services\MetaCommentSyncService::class)->toggleHideComment($comment, true);
                }
            } catch (\Throwable $e) {
                Log::warning("ProcessCommentJob: Failed to auto-hide comment #{$comment->id} on live platform: " . $e->getMessage());
            }
        } elseif ($status === Comment::STATUS_NEW) {
            $status = Comment::STATUS_PENDING_REVIEW;
        }

        // 3.5 Auto DM Trigger (Comment-to-DM Automation for Facebook & Instagram)
        $autoDmTriggered = false;
        if (!$shouldHide && empty($repliedAt) && $account && ($account->isFacebook() || $account->isInstagram())) {
            $matchedRule = $this->findMatchingAutoDmRule($account, $comment);
            if ($matchedRule) {
                // Rule matched: Handled by Auto DM flow, prevents generic AI auto-comment from clashing
                $autoDmTriggered = true;
                try {
                    $messagingService = app(MetaMessagingService::class);
                    $dmResult = $messagingService->sendAutoDm($matchedRule, $comment);
                    if ($dmResult['success']) {
                        $repliedAt = now();
                        if (!empty($dmResult['public_reply_sent'])) {
                            $status = Comment::STATUS_REPLIED;
                            $aiReply = $dmResult['public_reply'] ?? $matchedRule->public_reply;
                        }

                        \App\Services\ActivityLogger::log(
                            \App\Models\SystemActivityLog::EVENT_AUTO_DM,
                            "Auto DM triggered for rule '{$matchedRule->name}' to {$comment->commenter_name}",
                            \App\Models\SystemActivityLog::STATUS_SUCCESS,
                            ['comment_id' => $comment->id, 'rule_id' => $matchedRule->id, 'trigger_keyword' => $matchedRule->trigger_keyword],
                            $dmResult,
                            null,
                            $account->id,
                            $account->user_id
                        );

                        try {
                            Log::channel('webhook')->info("Auto DM Executed: Delivered to commenter for comment #{$comment->id}", [
                                'rule_id'           => $matchedRule->id,
                                'rule_name'         => $matchedRule->name,
                                'account'           => $account->name,
                                'commenter'         => $comment->commenter_name,
                                'dm_sent'           => $dmResult['dm_sent'] ?? true,
                                'public_reply_sent' => $dmResult['public_reply_sent'] ?? false,
                            ]);
                        } catch (\Throwable $logEx) {}

                        Log::info("ProcessCommentJob: Auto DM delivered to commenter for comment #{$comment->id} via rule #{$matchedRule->id} [{$matchedRule->name}]");
                    } else {
                        \App\Services\ActivityLogger::log(
                            \App\Models\SystemActivityLog::EVENT_AUTO_DM,
                            "Auto DM delivery failed for rule '{$matchedRule->name}'",
                            \App\Models\SystemActivityLog::STATUS_FAILED,
                            ['comment_id' => $comment->id, 'rule_id' => $matchedRule->id],
                            $dmResult['message'] ?? 'Unknown error',
                            null,
                            $account->id,
                            $account->user_id
                        );
                    }
                } catch (\Throwable $dmEx) {
                    \App\Services\ActivityLogger::log(
                        \App\Models\SystemActivityLog::EVENT_AUTO_DM,
                        "Auto DM exception for rule '{$matchedRule->name}'",
                        \App\Models\SystemActivityLog::STATUS_FAILED,
                        ['comment_id' => $comment->id, 'rule_id' => $matchedRule->id],
                        $dmEx->getMessage(),
                        null,
                        $account->id,
                        $account->user_id
                    );
                    try {
                        Log::channel('webhook')->warning("Auto DM Failed: Comment #{$comment->id} - " . $dmEx->getMessage());
                    } catch (\Throwable $logEx) {}

                    Log::warning("ProcessCommentJob: Auto DM dispatch error for comment #{$comment->id}: " . $dmEx->getMessage());
                }
            }
        }

        // Mode 2: Auto comment & Auto approved -> Publish live immediately to social platform
        $alreadyReplied = ($status === Comment::STATUS_REPLIED || !empty($comment->meta_reply_id));
        // Safety: Only auto-reply live to fresh comments (within last 48 hours) to prevent replying to months/years-old comments
        $isFreshComment = empty($comment->commented_at) || $comment->commented_at->greaterThanOrEqualTo(now()->subDays(2));

        if (!$autoDmTriggered && $autoReplyMode === Account::AUTO_REPLY_AUTO_APPROVED && !$shouldHide && !empty($aiReply) && $status !== Comment::STATUS_HIDDEN && !$alreadyReplied && $isFreshComment) {
            try {
                $publishSuccess = false;
                if ($account->isYouTube()) {
                    $ytService = app(\App\Services\YouTubeCommentSyncService::class);
                    $publishRes = $ytService->replyToComment($comment, $aiReply);
                    $publishSuccess = $publishRes['success'] ?? false;
                } else {
                    $metaService = app(\App\Services\MetaCommentSyncService::class);
                    $publishRes = $metaService->replyToComment($comment, $aiReply);
                    $publishSuccess = $publishRes['success'] ?? false;
                }

                if ($publishSuccess) {
                    $status = Comment::STATUS_REPLIED;
                    $repliedAt = now();
                    Log::info("ProcessCommentJob: Auto-approved live reply published to comment #{$comment->id}");
                } else {
                    $errorMsg = $publishRes['message'] ?? 'Unknown publishing error';

                    // Detect Meta API rate limit (#32 Page request limit, #4 Application request limit, #17 User request limit)
                    if (str_contains($errorMsg, '(#32)') || str_contains($errorMsg, '(#4)') || str_contains($errorMsg, 'request limit reached')) {
                        Log::warning("ProcessCommentJob: Meta rate limit hit (#32) for account #{$account->id}. Backing off for 60s.");
                        if ($this->job) {
                            $this->release(60);
                            return;
                        }
                    }

                    Log::warning("ProcessCommentJob: Auto-reply publish failed for comment #{$comment->id}, kept in pending review", [
                        'message' => $errorMsg
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error("ProcessCommentJob: Auto-reply publish exception for comment #{$comment->id}: " . $e->getMessage());
            }
        }

        // 4. Update Comment
        $comment->update([
            'sentiment'        => $sentiment,
            'sentiment_score'  => $sentimentScore,
            'category'         => $category,
            'is_abusive'       => $isAbusive,
            'abuse_confidence' => $abuseConfidence,
            'is_favor'         => $isFavor,
            'is_against'       => $isAgainst,
            'is_complaint'     => $isComplaint,
            'should_hide'      => $shouldHide,
            'reply_needed'     => $replyNeeded,
            'ai_reply'         => $aiReply,
            'status'           => $status,
            'hidden_at'        => $hiddenAt,
            'replied_at'       => $repliedAt,
        ]);
    }

    /**
     * Analyze message text for sentiment, category, and toxicity
     */
    protected function analyzeMessage(string $text): array
    {
        if (empty($text)) {
            return [
                'sentiment' => 'neutral',
                'sentiment_score' => 0.50,
                'category' => 'general',
                'is_abusive' => false,
                'abuse_confidence' => 0.00,
                'is_complaint' => false,
                'is_favor' => false,
                'is_against' => false,
            ];
        }

        $abusiveKeywords = [
            'idiot', 'stupid', 'fraud', 'scam', 'fake', 'cheat', 'bastard', 'hate',
            'corrupt', 'bakwas', 'chutiya', 'lutera', 'chor', 'bullshit', 'loser',
        ];

        $complaintKeywords = [
            'issue', 'problem', 'delay', 'fail', 'worst', 'broken', 'not working',
            'disappointed', 'kharab', 'dikkat', 'shikayat', 'trouble', 'horrible', 'refund',
        ];

        $appreciationKeywords = [
            'great', 'awesome', 'excellent', 'congrats', 'congratulations', 'proud',
            'good work', 'helpful', 'dhanyawad', 'shandar', 'badhiya', 'kudos', 'love this',
            'superb', 'best', 'thank you', 'thanks', 'wonderful',
        ];

        $inquiryKeywords = [
            'when', 'where', 'how', 'what', 'why', 'price', 'timing', 'link',
            'details', 'kab', 'kaha', 'kaise', 'help', 'contact', 'location',
        ];

        // Check Abusive
        foreach ($abusiveKeywords as $word) {
            if (str_contains($text, $word)) {
                return [
                    'sentiment' => 'negative',
                    'sentiment_score' => 0.90,
                    'category' => 'troll',
                    'is_abusive' => true,
                    'abuse_confidence' => 0.95,
                    'is_complaint' => false,
                    'is_favor' => false,
                    'is_against' => true,
                ];
            }
        }

        // Check Complaint
        foreach ($complaintKeywords as $word) {
            if (str_contains($text, $word)) {
                return [
                    'sentiment' => 'negative',
                    'sentiment_score' => 0.70,
                    'category' => 'complaint',
                    'is_abusive' => false,
                    'abuse_confidence' => 0.10,
                    'is_complaint' => true,
                    'is_favor' => false,
                    'is_against' => true,
                ];
            }
        }

        // Check Appreciation
        foreach ($appreciationKeywords as $word) {
            if (str_contains($text, $word)) {
                return [
                    'sentiment' => 'positive',
                    'sentiment_score' => 0.85,
                    'category' => 'appreciation',
                    'is_abusive' => false,
                    'abuse_confidence' => 0.05,
                    'is_complaint' => false,
                    'is_favor' => true,
                    'is_against' => false,
                ];
            }
        }

        // Check Inquiry
        foreach ($inquiryKeywords as $word) {
            if (str_contains($text, $word)) {
                return [
                    'sentiment' => 'neutral',
                    'sentiment_score' => 0.50,
                    'category' => 'inquiry',
                    'is_abusive' => false,
                    'abuse_confidence' => 0.05,
                    'is_complaint' => false,
                    'is_favor' => false,
                    'is_against' => false,
                ];
            }
        }

        return [
            'sentiment' => 'neutral',
            'sentiment_score' => 0.50,
            'category' => 'feedback',
            'is_abusive' => false,
            'abuse_confidence' => 0.05,
            'is_complaint' => false,
            'is_favor' => false,
            'is_against' => false,
        ];
    }

    /**
     * Generate dynamic smart AI draft reply tailored to category, tone, and language
     */
    protected function generateDraftReply(string $category, string $tone, string $language, ?string $commenterName): string
    {
        $nameGreeting = !empty($commenterName) ? "Hello {$commenterName}, " : "";

        if (strtolower($language) === 'hindi' || str_contains(strtolower($language), 'hindi')) {
            return match ($category) {
                'appreciation' => "{$nameGreeting}Aapke samarthan aur sakaratmak feedback ke liye bohot-bohot dhanyawad! Hum hamesha janta ki seva ke liye samarpit hain.",
                'complaint'    => "{$nameGreeting}Asuvidha ke liye humein khed hai. Hamari team is samasya ke nivaaran mein lagi hai. Kripya humein direct message (DM) karein.",
                'inquiry'      => "{$nameGreeting}Sampark karne ke liye dhanyawad! Is vishay mein vistaar se jaankari ke liye official portal dekhein ya humein DM karein.",
                default        => "{$nameGreeting}Aapka sujhav hamare liye mahatvapurna hai. Saath judne ke liye dhanyawad!",
            };
        }

        if (strtolower($language) === 'hinglish') {
            return match ($category) {
                'appreciation' => "{$nameGreeting}Aapke support aur positive feedback ke liye bohot bohot shukriya! Hum hamesha serve karne ke liye committed hain.",
                'complaint'    => "{$nameGreeting}Asuvidha ke liye humein khed hai. Hamari team is issue ko resolve karne me lagi hai. Please humein DM karein taaki jaldi solve kar sakein.",
                'inquiry'      => "{$nameGreeting}Contact karne ke liye thanks! Iske baare me poori jaankari ke liye official portal visit karein ya humein DM karein.",
                default        => "{$nameGreeting}Aapka feedback hamare liye valuable hai. Saath judne ke liye thanks!",
            };
        }

        // Default English
        return match ($category) {
            'appreciation' => "{$nameGreeting}Thank you for your warm encouragement and positive feedback! We remain dedicated to serving our community.",
            'complaint'    => "{$nameGreeting}We apologize for any inconvenience caused. Our team is actively reviewing this matter. Please send us a direct message with details so we can resolve it promptly.",
            'inquiry'      => "{$nameGreeting}Thank you for reaching out. For complete details and official guidance on this topic, please visit our official website or DM our support team.",
            default        => "{$nameGreeting}Thank you for sharing your perspective with us. We appreciate your valuable feedback!",
        };
    }

    /**
     * Find the highest priority active AutoDmRule that matches this comment
     */
    protected function findMatchingAutoDmRule(Account $account, Comment $comment): ?AutoDmRule
    {
        $message = trim((string) $comment->message);
        if ($message === '') {
            return null;
        }

        // Defensive: Do not send Auto DM if comment is from page/channel itself
        if ($comment->commenter_meta_id && (
            $comment->commenter_meta_id === (string) $account->meta_account_id ||
            $comment->commenter_meta_id === (string) $account->instagram_business_account_id
        )) {
            return null;
        }

        // Fetch active rules for this account, prioritizing specific post rules over global rules
        $rules = AutoDmRule::active()
            ->where('account_id', $account->id)
            ->where(function ($q) use ($comment) {
                $q->whereNull('post_id');
                if (!empty($comment->post_id)) {
                    $q->orWhere('post_id', $comment->post_id);
                }
            })
            ->orderByRaw('post_id IS NULL ASC')
            ->get();

        foreach ($rules as $rule) {
            if ($rule->matchesText($message)) {
                return $rule;
            }
        }

        return null;
    }
}
