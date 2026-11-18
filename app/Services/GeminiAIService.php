<?php

namespace App\Services;

use App\Contracts\AIServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAIService implements AIServiceInterface
{
    protected ?string $apiKey;
    protected string $model;
    protected string $endpoint;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key') ?: env('GEMINI_API_KEY');
        $this->model = config('services.gemini.model') ?: env('GEMINI_MODEL', 'gemini-flash-lite-latest');
        $this->endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
    }

    /**
     * Check if Gemini API is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Provider identification
     */
    public function getProviderName(): string
    {
        return 'gemini';
    }

    /**
     * Perform deep constituent/user comment analysis and generate smart reply
     *
     * @param string $commentText
     * @param array $context [commenter_name, post_caption, account_name, tone, language, party_ideology]
     * @return array|null Returns parsed analysis array or null on failure
     */
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

        $prompt = <<<EOT
You are an expert Political Social Media & Citizen Relationship AI Manager for '{$accountName}'.
Analyze the following constituent social media comment and return a strictly valid JSON response.

COMMENT CONTEXT:
- Commenter Name: {$commenterName}
- Post Context / Caption: {$postCaption}
- Brand / Leader Tone: {$tone}
- Preferred Response Language: {$language}

COMMENT TEXT:
"{$commentText}"

TASKS:
1. Classify sentiment: 'positive', 'neutral', or 'negative'.
2. Assign sentiment_score from -1.00 (extremely negative/hostile) to +1.00 (extremely positive/supportive).
3. Classify category into one of: 'appreciation', 'complaint', 'inquiry', 'troll', 'feedback'.
4. Detect if abusive / troll attack: true or false.
5. If abusive, provide abuse_confidence (0.0 to 1.0).
6. Detect constituent intent:
   - is_complaint: true if citizen is highlighting a public problem (water, road, electricity, delays, corruption).
   - is_favor: true if praising leader, party, or initiative.
   - is_against: true if opposing or attacking the leader/party.
7. Generate ai_reply:
   - If comment is abusive or a troll, leave ai_reply null (they will be auto-hidden).
   - If not abusive, craft a natural, respectful, dignified, and empathetic response in the requested language ({$language}).
   - Never sound robotic. Sound like a caring public leader/spokesperson.

OUTPUT FORMAT (JSON ONLY, NO MARKDOWN, NO BACKTICKS):
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
EOT;

        try {
            $response = $this->callGemini($this->endpoint, $prompt);

            // If temporary high demand (503) or not found (404), fallback to gemini-flash-lite-latest
            if (!$response->successful() && $this->model !== 'gemini-flash-lite-latest') {
                Log::info("Gemini {$this->model} returned status {$response->status()}, falling back to gemini-flash-lite-latest");
                $fallbackEndpoint = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-lite-latest:generateContent";
                $response = $this->callGemini($fallbackEndpoint, $prompt);
            }

            if (!$response->successful()) {
                Log::warning('Gemini API call failed with status ' . $response->status(), [
                    'body' => $response->body()
                ]);
                return null;
            }

            $responseData = $response->json();
            $candidates = $responseData['candidates'] ?? [];
            if (empty($candidates)) {
                return null;
            }

            $rawText = $candidates[0]['content']['parts'][0]['text'] ?? '';
            $rawText = trim($rawText);

            // Strip any accidental markdown formatting if present
            if (str_starts_with($rawText, '```json')) {
                $rawText = substr($rawText, 7);
            }
            if (str_starts_with($rawText, '```')) {
                $rawText = substr($rawText, 3);
            }
            if (str_ends_with($rawText, '```')) {
                $rawText = substr($rawText, 0, -3);
            }

            $parsed = json_decode(trim($rawText), true);
            if (!is_array($parsed) || !isset($parsed['sentiment'])) {
                Log::warning('Gemini output could not be parsed as valid JSON: ' . $rawText);
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
            Log::warning('Gemini API exception during comment analysis: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Dispatch HTTP POST request to Google Gemini API
     */
    protected function callGemini(string $endpoint, string $prompt)
    {
        return Http::timeout(15)
            ->withHeaders([
                'X-goog-api-key' => $this->apiKey,
                'Content-Type'   => 'application/json',
            ])
            ->post("{$endpoint}?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 600,
                    'responseMimeType' => 'application/json',
                ]
            ]);
    }
}

