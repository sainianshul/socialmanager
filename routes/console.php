<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 1. Background automatic publisher for scheduled social posts (every minute)
Schedule::command('posts:publish-scheduled')->everyMinute()->withoutOverlapping();

// 2. Automatically sync latest YouTube comments via Smart Adaptive Polling (inspects every 15 minutes)
Schedule::command('sync:youtube-comments --queue-ai')->everyFifteenMinutes()->withoutOverlapping();

// 3. Automatically sync latest Facebook Page & Instagram comments via Smart Adaptive Polling (inspects every 15 minutes)
Schedule::command('sync:meta-comments')->everyFifteenMinutes()->withoutOverlapping();

// 4. Automatically sync latest YouTube videos and analytics (every hour)
Schedule::command('sync:youtube-videos')->hourly()->withoutOverlapping();

// 5. Proactively refresh expiring OAuth access tokens (YouTube/Google every 30 minutes)
Schedule::command('accounts:refresh-tokens')->everyThirtyMinutes()->withoutOverlapping();

// 6. Automatically prune old system activity logs (Retains last 30 days, runs daily at 2:00 AM)
Schedule::call(function () {
    \App\Services\ActivityLogger::prune(30);
})->dailyAt('02:00')->name('prune-activity-logs')->withoutOverlapping();


