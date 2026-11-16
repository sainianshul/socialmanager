<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Models\ScheduledPost;
use PHPUnit\Framework\TestCase;

class PostAndScheduleModelTest extends TestCase
{
    public function test_post_formatted_duration(): void
    {
        $p1 = new Post(['duration' => 0]);
        $this->assertEquals('0:00', $p1->formatted_duration);

        $p2 = new Post(['duration' => 135]); // 2m 15s
        $this->assertEquals('02:15', $p2->formatted_duration);

        $p3 = new Post(['duration' => 3665]); // 1h 1m 5s
        $this->assertEquals('01:01:05', $p3->formatted_duration);
    }

    public function test_post_formatted_views(): void
    {
        $p1 = new Post(['views_count' => 850]);
        $this->assertEquals('850', $p1->formatted_views);

        $p2 = new Post(['views_count' => 12500]);
        $this->assertEquals('12.5K', $p2->formatted_views);

        $p3 = new Post(['views_count' => 2500000]);
        $this->assertEquals('2.5M', $p3->formatted_views);
    }

    public function test_post_display_title_fallback(): void
    {
        $video = new Post(['title' => 'My YouTube Demo Video']);
        $this->assertEquals('My YouTube Demo Video', $video->display_title);

        $fbPost = new Post(['message' => 'Check out our new launch announcement today!']);
        $this->assertEquals('Check out our new launch announcement today!', $fbPost->display_title);
    }

    public function test_scheduled_post_platform_helpers(): void
    {
        $sp = new ScheduledPost(['platform' => ScheduledPost::PLATFORM_YOUTUBE]);

        $this->assertTrue($sp->isYouTube());
        $this->assertFalse($sp->isFacebook());
        $this->assertFalse($sp->isInstagram());
        $this->assertEquals('YouTube', $sp->platform_name);
        $this->assertEquals('ti ti-brand-youtube', $sp->platform_icon);
        $this->assertStringContainsString('YouTube', $sp->platform_badge);
    }
}

