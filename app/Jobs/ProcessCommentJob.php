<?php

namespace App\Jobs;

use App\Contracts\AIServiceInterface;
use App\Models\Account;
use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
     * Execute the job.
     */
    public function handle(): void
    {
        $comment = $this->comment->fresh();
        if (!$comment) {
            return;
        }

        $account = $comment->account;
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

        // Mode 2: Auto comment & Auto approved -> Publish live immediately to social platform
        if ($autoReplyMode === Account::AUTO_REPLY_AUTO_APPROVED && !$shouldHide && !empty($aiReply) && $status !== Comment::STATUS_HIDDEN) {
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
                    Log::warning("ProcessCommentJob: Auto-reply publish failed for comment #{$comment->id}, kept in pending review", [
                        'message' => $publishRes['message'] ?? 'Unknown publishing error'
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
}
