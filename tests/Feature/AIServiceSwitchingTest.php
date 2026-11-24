<?php

namespace Tests\Feature;

use App\Contracts\AIServiceInterface;
use App\Services\AI\OpenAICompatibleAIService;
use App\Services\GeminiAIService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AIServiceSwitchingTest extends TestCase
{
    public function test_default_provider_resolves_gemini(): void
    {
        config(['services.ai.default' => 'gemini']);

        $service = app(AIServiceInterface::class);

        $this->assertInstanceOf(GeminiAIService::class, $service);
        $this->assertEquals('gemini', $service->getProviderName());
    }

    public function test_openai_provider_resolves_openai_compatible_service(): void
    {
        config(['services.ai.default' => 'openai']);

        $service = app(AIServiceInterface::class);

        $this->assertInstanceOf(OpenAICompatibleAIService::class, $service);
        $this->assertEquals('openai', $service->getProviderName());
    }

    public function test_qwen_provider_resolves_qwen_compatible_service(): void
    {
        config(['services.ai.default' => 'qwen']);

        $service = app(AIServiceInterface::class);

        $this->assertInstanceOf(OpenAICompatibleAIService::class, $service);
        $this->assertEquals('qwen', $service->getProviderName());
    }

    public function test_deepseek_provider_resolves_deepseek_compatible_service(): void
    {
        config(['services.ai.default' => 'deepseek']);

        $service = app(AIServiceInterface::class);

        $this->assertInstanceOf(OpenAICompatibleAIService::class, $service);
        $this->assertEquals('deepseek', $service->getProviderName());
    }

    public function test_groq_provider_resolves_groq_compatible_service(): void
    {
        config(['services.ai.default' => 'groq']);

        $service = app(AIServiceInterface::class);

        $this->assertInstanceOf(OpenAICompatibleAIService::class, $service);
        $this->assertEquals('groq', $service->getProviderName());
    }

    public function test_ollama_provider_resolves_ollama_compatible_service(): void
    {
        config(['services.ai.default' => 'ollama']);

        $service = app(AIServiceInterface::class);

        $this->assertInstanceOf(OpenAICompatibleAIService::class, $service);
        $this->assertEquals('ollama', $service->getProviderName());
        $this->assertTrue($service->isConfigured());
    }

    public function test_openai_compatible_service_analyzes_and_drafts_reply(): void
    {
        config([
            'services.ai.default' => 'qwen',
            'services.qwen.api_key' => 'test-qwen-key',
            'services.qwen.base_url' => 'https://dashscope.aliyuncs.com/compatible-mode/v1',
            'services.qwen.model' => 'qwen-max',
        ]);

        Http::fake([
            '*/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'sentiment' => 'positive',
                                'sentiment_score' => 0.85,
                                'category' => 'appreciation',
                                'is_abusive' => false,
                                'abuse_confidence' => 0.0,
                                'is_complaint' => false,
                                'is_favor' => true,
                                'is_against' => false,
                                'ai_reply' => 'आपके समर्थन और विश्वास के लिए बहुत-बहुत धन्यवाद!',
                            ]),
                        ],
                    ],
                ],
            ], 200),
        ]);

        /** @var AIServiceInterface $service */
        $service = app(AIServiceInterface::class);

        $this->assertTrue($service->isConfigured());

        $analysis = $service->analyzeAndDraftReply('बहुत ही शानदार कार्य, नेता जी! आगे बढ़ते रहिए।', [
            'commenter_name' => 'राकेश शर्मा',
            'account_name' => 'विकास मंच',
        ]);

        $this->assertNotNull($analysis);
        $this->assertEquals('positive', $analysis['sentiment']);
        $this->assertEquals(0.85, $analysis['sentiment_score']);
        $this->assertEquals('appreciation', $analysis['category']);
        $this->assertFalse($analysis['is_abusive']);
        $this->assertTrue($analysis['is_favor']);
        $this->assertEquals('आपके समर्थन और विश्वास के लिए बहुत-बहुत धन्यवाद!', $analysis['ai_reply']);
    }
}
