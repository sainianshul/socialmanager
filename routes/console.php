<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 1. Background automatic publisher for scheduled social posts (every minute)
Schedule::command('posts:publish-scheduled')->everyMinute()->withoutOverlapping();

// 2. Automatically sync latest YouTube comments & queue AI analysis (every 10 minutes)
Schedule::command('sync:youtube-comments --queue-ai')->everyTenMinutes()->withoutOverlapping();

// 3. Automatically sync latest YouTube videos and analytics (every hour)
Schedule::command('sync:youtube-videos')->hourly()->withoutOverlapping();
