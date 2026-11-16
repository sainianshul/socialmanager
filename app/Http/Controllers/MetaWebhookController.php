<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCommentJob;
use App\Models\Account;
use App\Models\Comment;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class MetaWebhookController extends Controller
{
    /**
     * Handle Meta Webhook Verification and Event Dispatcher
     */
    public function handle(Request $request): Response
    {
        // 1. Meta Webhook Verification Challenge (GET)
        if ($request->isMethod('get')) {
            $verifyToken = config('services.facebook.webhook_verify_token', 'warroom_secret_token_123');

            $mode = $request->query('hub_mode', $request->query('hub.mode'));
            $token = $request->query('hub_verify_token', $request->query('hub.verify_token'));
            $challenge = $request->query('hub_challenge', $request->query('hub.challenge'));

            if ($mode === 'subscribe' && $token === $verifyToken) {
                Log::info('Meta webhook verification handshake successful.');
                return response($challenge, 200)->header('Content-Type', 'text/plain');
            }

            Log::warning('Meta webhook handshake failed. Invalid verify token provided.');
            return response('Forbidden', 403);
        }

        // 2. Incoming Event Notification (POST)
        $payload = $request->all();
        $object = $payload['object'] ?? null;

        if (!in_array($object, ['page', 'instagram'])) {
            return response('EVENT_RECEIVED', 200);
        }

        $entries = $payload['entry'] ?? [];

        foreach ($entries as $entry) {
            $metaPageId = (string) ($entry['id'] ?? '');
            if (empty($metaPageId)) {
                continue;
            }

            // Find matching Account in our system
            $account = Account::where('meta_account_id', $metaPageId)
                ->orWhere('instagram_business_account_id', $metaPageId)
                ->first();

            if (!$account) {
                Log::info("Webhook received for untracked account ID: {$metaPageId}");
                continue;
            }

            $changes = $entry['changes'] ?? [];
            foreach ($changes as $change) {
                $field = $change['field'] ?? '';
                $val = $change['value'] ?? [];

                // We handle 'feed' (Facebook) and 'comments' (Instagram)
                if ($field === 'feed' || $field === 'comments') {
                    $item = $val['item'] ?? ($field === 'comments' ? 'comment' : null);
                    $verb = $val['verb'] ?? 'add';

                    if ($item === 'comment' && ($verb === 'add' || $verb === 'edited')) {
                        $this->handleIncomingComment($account, $val);
                    }
                }
            }
        }

        // Always acknowledge Meta with 200 OK within 5 seconds
        return response('EVENT_RECEIVED', 200);
    }

    /**
     * Parse and persist incoming comment, then dispatch AI moderation
     */
    protected function handleIncomingComment(Account $account, array $val): void
    {
        $metaCommentId = (string) ($val['comment_id'] ?? ($val['id'] ?? ''));
        if (empty($metaCommentId)) {
            return;
        }

        $metaPostId = (string) ($val['post_id'] ?? ($val['media']['id'] ?? ''));
        $message = (string) ($val['message'] ?? ($val['text'] ?? ''));
        $createdTime = isset($val['created_time']) ? Carbon::createFromTimestamp($val['created_time']) : now();
        $from = $val['from'] ?? [];
        $commenterName = $from['name'] ?? ($from['username'] ?? 'Constituent');
        $commenterMetaId = isset($from['id']) ? (string) $from['id'] : null;

        // Find or create local Post record to anchor the comment
        $post = null;
        if (!empty($metaPostId)) {
            $post = Post::firstOrCreate(
                [
                    'account_id' => $account->id,
                    'meta_post_id' => $metaPostId,
                ],
                [
                    'platform' => $account->account_type,
                    'caption' => 'Post ' . substr($metaPostId, -8),
                    'published_at' => $createdTime,
                ]
            );
        }

        // Upsert comment (idempotent protection against duplicate webhook deliveries)
        $comment = Comment::firstOrNew([
            'meta_comment_id' => $metaCommentId,
        ]);

        $isNew = !$comment->exists;

        $comment->account_id = $account->id;
        if ($post) {
            $comment->post_id = $post->id;
        }
        $comment->commenter_name = $commenterName;
        $comment->commenter_meta_id = $commenterMetaId;
        $comment->message = $message;
        $comment->commented_at = $createdTime;

        if ($isNew) {
            $comment->status = Comment::STATUS_NEW;
        }

        $comment->save();

        // Dispatch AI processing job asynchronously
        ProcessCommentJob::dispatch($comment);

        Log::info("Webhook processed comment {$metaCommentId} on account {$account->name}. Job dispatched.");
    }
}

