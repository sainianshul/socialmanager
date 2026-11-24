<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCommentJob;
use App\Models\Account;
use App\Models\Comment;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
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
            $verifyToken = config('services.facebook.webhook_verify_token');

            $mode = $request->query('hub_mode', $request->query('hub.mode'));
            $token = $request->query('hub_verify_token', $request->query('hub.verify_token'));
            $challenge = $request->query('hub_challenge', $request->query('hub.challenge'));

            $isVerified = ($mode === 'subscribe' && $token === $verifyToken);

            $this->logWebhook($isVerified ? 'info' : 'warning', 'Meta Webhook Handshake Request (GET)', [
                'ip'           => $request->ip(),
                'user_agent'   => $request->userAgent(),
                'hub_mode'     => $mode,
                'hub_token'    => $token,
                'hub_challenge'=> $challenge,
                'verified'     => $isVerified,
            ]);

            if ($isVerified) {
                return response($challenge, 200)->header('Content-Type', 'text/plain');
            }

            return response('Forbidden', 403);
        }

        // 2. Incoming Event Notification (POST)
        if (!$this->validateSignature($request)) {
            $this->logWebhook('warning', 'Meta Webhook Rejected: Invalid or missing X-Hub-Signature-256', [
                'ip'         => $request->ip(),
                'user_agent' => $request->userAgent(),
                'signature'  => $request->header('X-Hub-Signature-256'),
            ]);
            return response('Unauthorized: Invalid Signature', 401);
        }

        $payload = $request->all();
        $object = $payload['object'] ?? null;

        $this->logWebhook('info', 'Meta Webhook Event Received (POST)', [
            'ip'            => $request->ip(),
            'user_agent'    => $request->userAgent(),
            'object'        => $object,
            'entries_count' => isset($payload['entry']) ? count($payload['entry']) : 0,
            'payload'       => $payload,
        ]);

        if (!in_array($object, ['page', 'instagram'])) {
            $this->logWebhook('info', "Meta Webhook: Ignored unsupported object type [{$object}]");
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
                $this->logWebhook('warning', "Meta Webhook: Untracked account ID [{$metaPageId}]. Not connected in Socialmanager.");
                continue;
            }

            $this->logWebhook('info', "Meta Webhook: Matched Account [{$account->name}] (ID: {$account->id}, Platform: {$account->account_type_name})");

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
     * Parse and persist incoming comment, then dispatch AI moderation and Auto DM
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

        // Fresh activity detected: reset consecutive empty syncs counter
        $account->update(['consecutive_empty_syncs' => 0]);

        // Dispatch AI processing & Auto DM job asynchronously
        ProcessCommentJob::dispatch($comment);

        $this->logWebhook('info', "Meta Webhook Comment Processed: #{$metaCommentId} by [{$commenterName}] on account [{$account->name}]", [
            'comment_id'   => $comment->id,
            'meta_id'      => $metaCommentId,
            'commenter'    => $commenterName,
            'message'      => $message,
            'account'      => $account->name,
            'post_id'      => $post?->id,
            'is_new'       => $isNew,
        ]);
    }

    /**
     * Dedicated Webhook Log helper
     */
    protected function logWebhook(string $level, string $message, array $context = []): void
    {
        try {
            Log::channel('webhook')->{$level}($message, $context);
        } catch (\Throwable $e) {
            Log::{$level}("[Webhook] " . $message, $context);
        }
    }

    /**
     * Validate Meta X-Hub-Signature-256 HMAC cryptographic signature
     */
    protected function validateSignature(Request $request): bool
    {
        $appSecret = config('services.facebook.client_secret') ?: env('FACEBOOK_APP_KEY');
        if (empty($appSecret)) {
            Log::warning('Meta Webhook: Missing FACEBOOK_APP_KEY in config, signature check skipped.');
            return true;
        }

        $signatureHeader = $request->header('X-Hub-Signature-256') ?: $request->header('x-hub-signature-256');

        // Allow test environments or local manual curl to omit signature if desired
        if (empty($signatureHeader) && (app()->environment('testing') || (app()->isLocal() && config('app.debug')))) {
            return true;
        }

        if (empty($signatureHeader)) {
            return false;
        }

        $rawBody = $request->getContent();
        $expectedSignature = 'sha256=' . hash_hmac('sha256', $rawBody, $appSecret);

        return hash_equals($expectedSignature, $signatureHeader);
    }

    /**
     * Admin Webhook Logs Viewer (For interactive testing & inspection)
     */
    public function viewLogs(Request $request)
    {
        $user = auth()->user();
        if (!$user || (!$user->isAdmin() && !$user->isManager())) {
            abort(403, 'Unauthorized access to webhook activity logs.');
        }

        $logPath = storage_path('logs/webhook.log');
        $rawLogs = '';
        $fileSize = '0 KB';
        $lastModified = 'Never';

        if (File::exists($logPath)) {
            $fileSize = round(File::size($logPath) / 1024, 2) . ' KB';
            $lastModified = Carbon::createFromTimestamp(File::lastModified($logPath))->diffForHumans();

            // Read last 250 lines
            $fileLines = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if ($fileLines !== false) {
                $lastLines = array_slice($fileLines, -250);
                $rawLogs = implode("\n", $lastLines);
            }
        }

        return view('admin.webhooks.logs', compact('rawLogs', 'fileSize', 'lastModified', 'logPath'));
    }

    /**
     * Clear Webhook Logs
     */
    public function clearLogs()
    {
        $user = auth()->user();
        if (!$user || (!$user->isAdmin() && !$user->isManager())) {
            abort(403, 'Unauthorized access to webhook activity logs.');
        }

        $logPath = storage_path('logs/webhook.log');
        if (File::exists($logPath)) {
            File::put($logPath, '');
        }

        return redirect()->back()->with('success', 'Webhook log file cleared successfully.');
    }
}


