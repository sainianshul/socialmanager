<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'smartping' => [
        'user' => env('SMARTPING_USER'),
        'pass' => env('SMARTPING_PASS'),
        'from' => env('SMARTPING_FROM'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_YOUTUBE_REDIRECT_URI', 'https://socialmanager.rezel.online/auth/youtube/callback'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_APP_ID'),
        'client_secret' => env('FACEBOOK_APP_KEY'),
        'webhook_verify_token' => env('META_VERIFY_TOKEN'),
    ],

    'instagram' => [
        'client_id' => env('INSTAGRAM_APP_ID', env('FACEBOOK_APP_ID')),
        'client_secret' => env('INSTAGRAM_APP_SECRET', env('FACEBOOK_APP_KEY')),
    ],

    'canva' => [
        'client_id' => env('CANVA_CLIENT_ID'),
        'client_secret' => env('CANVA_CLIENT_SECRET'),
        'redirect' => env('CANVA_REDIRECT_URI', 'https://socialmanager.rezel.online/admin/canva/callback'),
    ],

    // Universal AI Providers for Political Sentiment Analysis & Smart Replies
    'ai' => [
        'default' => env('AI_PROVIDER', 'gemini'),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-flash-lite-latest'),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
    ],

    'qwen' => [
        'api_key' => env('QWEN_API_KEY'),
        'model' => env('QWEN_MODEL', 'qwen-plus'),
        'base_url' => env('QWEN_BASE_URL', 'https://dashscope.aliyuncs.com/compatible-mode/v1'),
    ],

    'deepseek' => [
        'api_key' => env('DEEPSEEK_API_KEY'),
        'model' => env('DEEPSEEK_MODEL', 'deepseek-chat'),
        'base_url' => env('DEEPSEEK_BASE_URL', 'https://api.deepseek.com/v1'),
    ],

    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
        'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
        'base_url' => env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1'),
    ],

    'ollama' => [
        'api_key' => env('OLLAMA_API_KEY', 'ollama-local'),
        'model' => env('OLLAMA_MODEL', 'llama3.2'),
        'base_url' => env('OLLAMA_BASE_URL', 'http://localhost:11434/v1'),
    ],

];
