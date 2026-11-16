<?php

namespace App\Services\AI;

use App\Contracts\AIServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Universal OpenAI-Compatible AI Provider.
 * Works seamlessly with ChatGPT (OpenAI), Alibaba Qwen, DeepSeek, Groq, and Ollama.
 */
class OpenAICompatibleAIService implements AIServiceInterface
{
    protected ?string $apiKey;
    protected string $model;
    protected string $baseUrl;
    protected string $providerName;

    public function __construct(?string $providerName = null)
    {
        $this->providerName = $providerName ?: config('services.ai.default', 'openai');

        $upper = strtoupper($this->providerName);
        $this->apiKey = config("services.{$this->providerName}.api_key")
            ?? config("services.ai.{$this->providerName}.api_key")
            ?? env("{$upper}_API_KEY");

        $defaultModel = match ($this->providerName) {
            'qwen'     => 'qwen-plus',
            'deepseek' => 'deepseek-chat',
            default    => 'gpt-4o-mini',
        };

        $defaultBaseUrl = match ($this->providerName) {
            'qwen'     => 'https://dashscope.aliyuncs.com/compatible-mode/v1',
            'deepseek' => 'https://api.deepseek.com/v1',
            default    => 'https://api.openai.com/v1',
        };

        $this->model = config("services.{$this->providerName}.model")
            ?? config("services.ai.{$this->providerName}.model")
            ?? env("{$upper}_MODEL", $defaultModel);

        $this->baseUrl = config("services.{$this->providerName}.base_url")
            ?? config("services.ai.{$this->providerName}.base_url")
            ?? env("{$upper}_BASE_URL", $defaultBaseUrl);
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function getProviderName(): string
    {
        return $this->providerName;
    }

    public function analyzeAndDraftReply(string $commentText, array $context = []): ?array
    {
        if (!$this->isConfigured() || empty(trim($commentText))) {
            return null;
        }

        $commenterName = $context['commenter_name'] ?? 'Constituent';
        $postCaption = $context['post_caption'] ?? 'Political / Social Update';
        $accountName = $context['account_name'] ?? 'Official Page';
        $tone = $context['tone'] ?? 'Humble, Respectful, and Solution-Oriented';
        $language = $context['language'] ?? 'Hindi and Hinglish';

        $systemPrompt = <<<EOT
You are an expert Political Social Media & Citizen Relationship AI Manager for '{$accountName}'.
Analyze constituent social media comments and ALWAYS respond with pure, valid JSON only (no markdown, no backticks).

RESPONSE JSON FORMAT:
{
  "sentiment": "positive|neutral|negative",
  "sentiment_score": 0.75,
  "category": "appreciation|complaint|inquiry|troll|feedback",
  "is_abusive": false,
  "abuse_confidence": 0.05,
  "is_complaint": false,
  "is_favor": true,
  "is_against": false,
  "ai_reply": "Draft reply text here"
}

GUIDELINES:
1. sentiment_score must range from -1.00 (hostile) to +1.00 (supportive).
2. is_complaint: true if citizen highlights road, water, electricity, corruption, or delays.
3. is_favor: true if praise for leader/initiative. is_against: true if opposing leader.
4. ai_reply: If abusive or troll, keep null. If clean, craft a warm, caring, empathetic leader reply in {$language} with {$tone} tone.
EOT;

        $userPrompt = "Commenter: {$commenterName}\nPost Topic: {$postCaption}\nComment Text: \"{$commentText}\"";

        try {
            $endpoint = rtrim($this->baseUrl, '/') . '/chat/completions';

            $response = Http::timeout(15)
                ->withToken($this->apiKey)
                ->post($endpoint, [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                    'temperature' => 0.2,
                    'response_format' => ['type' => 'json_object'],
                ]);

            if (!$response->successful()) {
                Log::warning("{$this->providerName} API call failed with status " . $response->status(), [
                    'body' => $response->body()
                ]);
                return null;
            }

            $responseData = $response->json();
            $content = $responseData['choices'][0]['message']['content'] ?? '';
            $content = trim($content);

            // Strip any accidental markdown formatting if present
            if (str_starts_with($content, '```json')) {
                $content = substr($content, 7);
            }
            if (str_starts_with($content, '```')) {
                $content = substr($content, 3);
            }
            if (str_ends_with($content, '```')) {
                $content = substr($content, 0, -3);
            }

            $parsed = json_decode(trim($content), true);
            if (!is_array($parsed) || !isset($parsed['sentiment'])) {
                Log::warning("{$this->providerName} output could not be parsed as valid JSON: " . $content);
                return null;
            }

            return [
                'sentiment'        => in_array($parsed['sentiment'], ['positive', 'neutral', 'negative']) ? $parsed['sentiment'] : 'neutral',
                'sentiment_score'  => (float) ($parsed['sentiment_score'] ?? 0.0),
                'category'         => (string) ($parsed['category'] ?? 'feedback'),
                'is_abusive'       => (bool) ($parsed['is_abusive'] ?? false),
                'abuse_confidence' => (float) ($parsed['abuse_confidence'] ?? 0.0),
                'is_complaint'     => (bool) ($parsed['is_complaint'] ?? false),
                'is_favor'         => (bool) ($parsed['is_favor'] ?? false),
                'is_against'       => (bool) ($parsed['is_against'] ?? false),
                'ai_reply'         => !empty($parsed['ai_reply']) ? (string) $parsed['ai_reply'] : null,
            ];

        } catch (\Throwable $e) {
            Log::warning("{$this->providerName} API exception during comment analysis: " . $e->getMessage());
            return null;
        }
    }
}
