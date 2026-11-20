<?php

namespace App\Contracts;

/**
 * Contract for AI Social & Constituent Relationship Analysis Providers.
 * Enables zero-code-change switching between Gemini, OpenAI/ChatGPT, Qwen, DeepSeek, and Local LLMs.
 */
interface AIServiceInterface
{
    /**
     * Check if the AI provider credentials and configurations are set.
     */
    public function isConfigured(): bool;

    /**
     * Get the unique identifier of the provider (e.g. 'gemini', 'openai', 'qwen').
     */
    public function getProviderName(): string;

    /**
     * Perform deep constituent comment analysis and generate smart leader draft reply.
     *
     * @param string $commentText Raw message text from the constituent
     * @param array $context [
     *     'commenter_name' => ?string,
     *     'post_caption'   => ?string,
     *     'account_name'   => ?string,
     *     'tone'           => ?string,
     *     'language'       => ?string,
     * ]
     * @return array|null Returns standardized dictionary on success, or null on failure:
     * [
     *     'sentiment'        => 'positive'|'neutral'|'negative',
     *     'sentiment_score'  => float (-1.00 to +1.00),
     *     'category'         => 'appreciation'|'complaint'|'inquiry'|'troll'|'feedback',
     *     'is_abusive'       => bool,
     *     'abuse_confidence' => float (0.0 to 1.0),
     *     'is_complaint'     => bool,
     *     'is_favor'         => bool,
     *     'is_against'       => bool,
     *     'ai_reply'         => ?string,
     * ]
     */
    public function analyzeAndDraftReply(string $commentText, array $context = []): ?array;

    /**
     * Generate an SEO-friendly social/video description based on a title and target platform.
     *
     * @param string $title
     * @param array $options ['platform' => 'youtube', 'tone' => ?string, 'language' => ?string]
     * @return string|null
     */
    public function generateVideoDescription(string $title, array $options = []): ?string;
}

