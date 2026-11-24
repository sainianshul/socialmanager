<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the active AI service provider (Gemini, OpenAI, Qwen, DeepSeek)
        $this->app->bind(\App\Contracts\AIServiceInterface::class, function ($app) {
            $provider = strtolower(config('services.ai.default', 'gemini'));

            return match ($provider) {
                'openai'   => new \App\Services\AI\OpenAICompatibleAIService('openai'),
                'qwen'     => new \App\Services\AI\OpenAICompatibleAIService('qwen'),
                'deepseek' => new \App\Services\AI\OpenAICompatibleAIService('deepseek'),
                'groq'     => new \App\Services\AI\OpenAICompatibleAIService('groq'),
                'ollama'   => new \App\Services\AI\OpenAICompatibleAIService('ollama'),
                default    => $app->make(\App\Services\GeminiAIService::class),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Notifications\Events\NotificationSent::class,
            \App\Listeners\LogNotificationSent::class
        );
    }
}
