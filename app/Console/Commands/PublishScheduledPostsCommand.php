<?php

namespace App\Console\Commands;

use App\Models\ScheduledPost;
use App\Services\MetaPublishingService;
use App\Services\YouTubePublishingService;
use Illuminate\Console\Command;

class PublishScheduledPostsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish due scheduled posts across connected Meta and YouTube channels';

    /**
     * Execute the console command.
     */
    public function handle(MetaPublishingService $publishingService, YouTubePublishingService $youtubePublishingService): int
    {
        $duePosts = ScheduledPost::with('account')
            ->dueForPublishing()
            ->get();

        if ($duePosts->isEmpty()) {
            $this->info('No scheduled posts due for publishing.');
            return Command::SUCCESS;
        }

        $this->info("Found {$duePosts->count()} scheduled post(s) due for publishing.");

        $successCount = 0;
        $failedCount = 0;

        foreach ($duePosts as $index => $post) {
            // Anti-ban Multi-Account Jitter: Add 1.5s - 3.0s micro-delay between consecutive posts to prevent IP burst rate flags
            if ($index > 0 && app()->environment() !== 'testing') {
                usleep(rand(1500000, 3000000));
            }

            $this->line("Publishing Post #{$post->id} ({$post->post_type}) to channel: {$post->account->name}...");

            if ($post->isYouTube() || $post->account->isYouTube()) {
                $result = $youtubePublishingService->publish($post);
                $idLabel = 'YouTube ID';
                $idVal = $result['video_id'] ?? ($result['meta_post_id'] ?? '');
            } else {
                $result = $publishingService->publish($post);
                $idLabel = 'Meta ID';
                $idVal = $result['meta_post_id'] ?? '';
            }

            if ($result['success']) {
                $successCount++;
                $this->info("✓ Post #{$post->id} published successfully! {$idLabel}: {$idVal}");
                \App\Services\ActivityLogger::log(
                    \App\Models\SystemActivityLog::EVENT_POST_PUBLISH,
                    "Published scheduled post #{$post->id} ({$post->post_type}) to {$post->account->name}",
                    \App\Models\SystemActivityLog::STATUS_SUCCESS,
                    ['post_id' => $post->id, 'account_id' => $post->account_id, 'type' => $post->post_type],
                    $result,
                    null,
                    $post->account_id,
                    $post->user_id
                );
            } else {
                $failedCount++;
                $this->error("✗ Post #{$post->id} failed: {$result['message']}");
                \App\Services\ActivityLogger::log(
                    \App\Models\SystemActivityLog::EVENT_POST_PUBLISH,
                    "Failed publishing scheduled post #{$post->id} ({$post->post_type}) to {$post->account->name}",
                    \App\Models\SystemActivityLog::STATUS_FAILED,
                    ['post_id' => $post->id, 'account_id' => $post->account_id],
                    $result['message'],
                    null,
                    $post->account_id,
                    $post->user_id
                );
            }
        }

        $this->info("Completed publishing. Success: {$successCount}, Failed: {$failedCount}");
        return Command::SUCCESS;
    }
}

