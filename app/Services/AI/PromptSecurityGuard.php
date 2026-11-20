<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;

/**
 * Enterprise AI Prompt Injection & Anti-Jailbreak Defense Guard.
 * Protects LLMs from prompt leakage, jailbreak attempts, and adversarial instruction injection.
 */
class PromptSecurityGuard
{
    /**
     * Maximum allowed character length for comment input to prevent buffer-stuffing attacks.
     */
    public const MAX_COMMENT_LENGTH = 1000;

    /**
     * Regex patterns representing direct prompt injection and jailbreak attempts.
     */
    protected static array $injectionPatterns = [
        '/ignore\s+(all\s+)?(previous|prior|above)\s+(instructions|prompts|rules|commands)/i',
        '/forget\s+(all\s+)?(your\s+)?(persona|system\s+prompt|identity|rules|instructions)/i',
        '/you\s+are\s+now\s+(an?\s+)?(unrestricted|dan|jailbroken|evil|adversary|opposition|hacker)/i',
        '/system\s*[:=]\s*(override|reset|instruction|prompt)/i',
        '/output\s+json\s+with\s+(ai_reply|corrupt|bhrasht|ghotala|confession)/i',
        '/hum\s+maante\s+hain\s+ki\s+(hum\s+)?(bhrasht|chor|ghotale|neta)/i',
        '/(disregard|bypass|disable)\s+(all\s+)?(safety|system|guardrail|security|content\s+filter)/i',
        '/act\s+as\s+if\s+you\s+(are\s+not|have\s+no)\s+(safety|rules|instructions)/i',
        '/repeat\s+(the\s+)?(system\s+prompt|exact\s+instructions\s+above)/i',
        '/reveal\s+(your\s+)?(system\s+prompt|developer\s+mode)/i',
    ];

    /**
     * Phrases that indicate the LLM was manipulated or is echoing self-incriminating/adversarial outputs.
     */
    protected static array $bannedReplyPhrases = [
        'as an ai language model',
        'ignore previous instructions',
        'hum maante hain ki',
        'humne ghotale kiye',
        'hamara neta bhrasht hai',
        'i am a jailbroken ai',
        'dan mode enabled',
        'system prompt:',
    ];

    /**
     * Sanitize untrusted comment input:
     * 1. Strips dangerous HTML/XML and markdown enclosure tags.
     * 2. Normalizes whitespace.
     * 3. Truncates to safe length.
     */
    public static function sanitizeInput(string $text): string
    {
        // Strip XML/HTML tags that could be used to break out of delimiters
        $cleaned = strip_tags($text);

        // Strip backticks and specific enclosure tags
        $cleaned = str_replace(['```json', '```', '<user_comment>', '</user_comment>', '<system>', '</system>'], '', $cleaned);

        // Normalize whitespace
        $cleaned = preg_replace('/\s+/', ' ', trim($cleaned));

        // Enforce max length
        if (mb_strlen($cleaned) > self::MAX_COMMENT_LENGTH) {
            $cleaned = mb_substr($cleaned, 0, self::MAX_COMMENT_LENGTH);
        }

        return $cleaned;
    }

    /**
     * Check if comment text matches overt prompt injection / jailbreak patterns.
     */
    public static function detectPromptInjection(string $text): bool
    {
        $normalized = strtolower(trim($text));

        foreach (self::$injectionPatterns as $pattern) {
            if (preg_match($pattern, $normalized)) {
                Log::channel('security')->warning('Prompt Injection Attack Detected and Blocked', [
                    'pattern' => $pattern,
                    'excerpt' => mb_substr($text, 0, 150),
                ]);
                return true;
            }
        }

        return false;
    }

    /**
     * Standardized safe response for identified prompt injection attacks.
     * Flags the comment as an abusive troll attack and sets ai_reply strictly to null.
     */
    public static function getInjectionThreatResponse(): array
    {
        return [
            'sentiment'        => 'negative',
            'sentiment_score'  => -0.95,
            'category'         => 'troll',
            'is_abusive'       => true,
            'abuse_confidence' => 0.99,
            'is_complaint'     => false,
            'is_favor'         => false,
            'is_against'       => true,
            'ai_reply'         => null,
        ];
    }

    /**
     * Post-generation safety verification on the draft reply.
     * Ensures no prompt leaks, self-incriminating admissions, or malicious echoes reach the public page.
     */
    public static function validateAndFilterReply(?string $aiReply, string $rawComment): ?string
    {
        if (empty($aiReply)) {
            return null;
        }

        $replyLower = strtolower(trim($aiReply));

        // Check for banned phrases / jailbreak echoes
        foreach (self::$bannedReplyPhrases as $banned) {
            if (str_contains($replyLower, $banned)) {
                Log::channel('security')->warning('Generated AI reply blocked due to safety violation', [
                    'banned_phrase' => $banned,
                    'ai_reply'      => $aiReply,
                ]);
                return null;
            }
        }

        // Check reflection attack: If the reply is an exact repetition of the user comment
        if (mb_strlen($rawComment) > 15 && strcasecmp(trim($aiReply), trim($rawComment)) === 0) {
            Log::channel('security')->warning('Generated AI reply blocked due to reflection echo');
            return null;
        }

        return trim($aiReply);
    }
}
