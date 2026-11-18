<?php

namespace App\DataTables\Comments;

use App\Models\Account;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Yajra\DataTables\Services\DataTable;

class CommentDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)

            // ── Modern Social Inbox Card (Feed View) ───────────────────
            ->addColumn('card', function (Comment $comment) {
                return $this->renderCardHtml($comment);
            })

            // ── S.No / Row Index ──────────────────────────────────────
            ->addIndexColumn()

            // ── Parent Post Context ───────────────────────────────────
            ->filterColumn('post', function ($query, $keyword) {
                $query->whereHas('post', function ($q) use ($keyword) {
                    $q->where('message', 'like', "%{$keyword}%")
                      ->orWhere('display_title', 'like', "%{$keyword}%");
                });
            })
            ->addColumn('post', function (Comment $comment) {
                $post = $comment->post;
                if (!$post) {
                    return '<span class="text-secondary">—</span>';
                }

                $thumbSrc = $post->thumbnail_url ?: $post->media_url;
                $thumb = '';
                if (!empty($thumbSrc)) {
                    $thumb = '<img src="' . e($thumbSrc) . '" class="rounded me-2 border flex-shrink-0" style="width: 40px; height: 32px; object-fit: cover;" alt="post media" onerror="this.style.display=\'none\';">';
                } else {
                    $icon = $post->account?->isYouTube() ? 'ti ti-brand-youtube text-danger' : ($post->account?->isInstagram() ? 'ti ti-brand-instagram text-pink' : 'ti ti-brand-facebook text-primary');
                    $thumb = '<div class="avatar avatar-sm rounded bg-light text-secondary me-2 flex-shrink-0"><i class="' . $icon . '"></i></div>';
                }

                $caption = !empty($post->message) ? Str::limit($post->message, 38) : (!empty($post->display_title) ? Str::limit($post->display_title, 38) : 'Post #' . substr($post->platform_post_id ?? $post->meta_post_id ?? '', -6));
                $postDate = $post->published_at ? $post->published_at->format('d M, H:i') : '';

                $platformName = $post->account?->isYouTube() ? 'YouTube' : ($post->account?->isInstagram() ? 'Instagram' : 'Facebook');
                $extUrl = $post->permalink_url ?? ($post->platform_post_id ? "https://youtube.com/watch?v={$post->platform_post_id}" : null);
                $externalLink = $extUrl ? '<a href="' . e($extUrl) . '" target="_blank" class="text-secondary ms-1" data-bs-toggle="tooltip" title="View on ' . $platformName . '"><i class="ti ti-external-link"></i></a>' : '';

                return '
                    <div class="d-flex align-items-center">
                        ' . $thumb . '
                        <div class="text-truncate" style="max-width: 190px;">
                            <div class="fw-medium text-dark text-truncate" title="' . e($post->display_title ?? $post->message ?? '') . '">' . e($caption) . $externalLink . '</div>
                            <small class="text-muted fs-7">' . e($postDate) . '</small>
                        </div>
                    </div>
                ';
            })

            // ── Commenter Profile ─────────────────────────────────────
            ->filterColumn('commenter', function ($query, $keyword) {
                $query->where('commenter_name', 'like', "%{$keyword}%");
            })
            ->addColumn('commenter', function (Comment $comment) {
                $avatar = '';
                if (!empty($comment->commenter_photo_url)) {
                    $avatar = '<span class="avatar avatar-sm rounded-circle me-2 flex-shrink-0" style="background-image: url(' . e($comment->commenter_photo_url) . ')"></span>';
                } else {
                    $initial = strtoupper(substr($comment->commenter_name ?? 'U', 0, 1));
                    $avatar = '<span class="avatar avatar-sm rounded-circle bg-light text-dark fw-bold border me-2 flex-shrink-0">' . e($initial) . '</span>';
                }

                $timeAgo = $comment->commented_at ? $comment->commented_at->diffForHumans() : $comment->created_at->diffForHumans();
                $defaultName = match ($comment->account?->account_type) {
                    Account::TYPE_INSTAGRAM => 'Instagram User',
                    Account::TYPE_YOUTUBE => 'YouTube User',
                    default => 'Facebook User',
                };
                $displayName = $comment->commenter_name ?: $defaultName;

                return '
                    <div class="d-flex align-items-center">
                        ' . $avatar . '
                        <div class="min-w-0">
                            <div class="fw-semibold text-dark text-truncate" style="max-width: 140px;">' . e($displayName) . '</div>
                            <small class="text-muted fs-7">' . e($timeAgo) . '</small>
                        </div>
                    </div>
                ';
            })

            // ── Real Social Thread (User Comment + Indented AI Reply) ─
            ->filterColumn('message', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('message', 'like', "%{$keyword}%")
                      ->orWhere('ai_reply', 'like', "%{$keyword}%")
                      ->orWhere('commenter_name', 'like', "%{$keyword}%");
                });
            })
            ->editColumn('message', function (Comment $comment) {
                $timeAgo = $comment->commented_at ? $comment->commented_at->diffForHumans() : $comment->created_at->diffForHumans();
                $defaultName = match ($comment->account?->account_type) {
                    Account::TYPE_INSTAGRAM => 'Instagram User',
                    Account::TYPE_YOUTUBE => 'YouTube User',
                    default => 'Facebook User',
                };
                $displayName = $comment->commenter_name ?: $defaultName;

                // User Avatar
                if (!empty($comment->commenter_photo_url)) {
                    $userAvatar = '<span class="avatar avatar-sm rounded-circle border shadow-xs flex-shrink-0" style="background-image: url(' . e($comment->commenter_photo_url) . '); width: 34px; height: 34px;"></span>';
                } else {
                    $initial = strtoupper(substr($displayName, 0, 1));
                    $userAvatar = '<span class="avatar avatar-sm rounded-circle bg-light text-dark fw-bold border shadow-xs flex-shrink-0" style="width: 34px; height: 34px; font-size: 0.85rem;">' . e($initial) . '</span>';
                }

                $likesHtml = '';
                if ($comment->likes_count > 0) {
                    $likesHtml = ' <span class="badge badge-outline text-danger ms-1 py-0 px-1 fs-7" title="' . $comment->likes_count . ' likes"><i class="ti ti-heart me-1"></i>' . $comment->likes_count . '</span>';
                }

                // AI Reply Thread (Indented with left margin, AI DP & Answer text)
                $replyHtml = '';
                if (!empty($comment->ai_reply)) {
                    $aiAvatar = '';
                    if (!empty($comment->account?->profile_pic_url)) {
                        $aiAvatar = '<span class="avatar avatar-xs rounded-circle border flex-shrink-0" style="background-image: url(' . e($comment->account->profile_pic_url) . '); width: 28px; height: 28px;"></span>';
                    } else {
                        $aiAvatar = '<span class="avatar avatar-xs rounded-circle bg-primary-lt text-primary border flex-shrink-0" style="width: 28px; height: 28px;"><i class="ti ti-robot fs-3"></i></span>';
                    }

                    $accountName = $comment->account?->name ?: 'DevApp';
                    $isReplied = ($comment->status === Comment::STATUS_REPLIED);
                    $statusPill = $isReplied
                        ? '<span class="badge badge-outline text-success py-0 px-1.5" style="font-size: 0.68rem;"><i class="ti ti-check me-0.5"></i>Sent</span>'
                        : '<span class="badge badge-outline text-warning py-0 px-1.5" style="font-size: 0.68rem;"><i class="ti ti-clock me-0.5"></i>Draft</span>';

                    $replyTextHtml = $this->formatExpandableText((string) $comment->ai_reply, 150, 'text-secondary', '0.85rem');

                    $replyHtml = '
                        <div class="d-flex align-items-start gap-2 ms-4 ps-2.5 mt-2" style="border-left: 2px solid #e2e8f0;">
                            ' . $aiAvatar . '
                            <div class="bg-light-subtle rounded-3 p-2 px-3 border w-100">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="fw-semibold text-dark small">' . e($accountName) . '</span>
                                        <span class="text-muted fs-7"><i class="ti ti-sparkles text-primary me-0.5"></i>AI</span>
                                    </div>
                                    ' . $statusPill . '
                                </div>
                                ' . $replyTextHtml . '
                            </div>
                        </div>
                    ';
                }

                $commentMessageHtml = $this->formatExpandableText((string) ($comment->message ?? ''), 140, 'text-dark', '0.88rem', $likesHtml);

                return '
                    <div class="comment-thread py-1" style="min-width: 300px; max-width: 580px;">
                        <div class="d-flex align-items-start gap-2">
                            ' . $userAvatar . '
                            <div class="min-w-0 w-100">
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-0.5">
                                    <span class="fw-bold text-dark fs-4">' . e($displayName) . '</span>
                                    <span class="text-muted small fs-7">' . e($timeAgo) . '</span>
                                </div>
                                ' . $commentMessageHtml . '
                            </div>
                        </div>
                        ' . $replyHtml . '
                    </div>
                ';
            })

            // ── Sentiment Badge (Clean Transparent Outline) ───────────
            ->addColumn('sentiment', function (Comment $comment) {
                return match (strtolower($comment->sentiment ?? '')) {
                    'positive' => '<span class="badge badge-outline text-success py-1 px-2"><i class="ti ti-mood-smile me-1"></i>Positive</span>',
                    'negative' => '<span class="badge badge-outline text-danger py-1 px-2"><i class="ti ti-mood-sad me-1"></i>Negative</span>',
                    'neutral'  => '<span class="badge badge-outline text-secondary py-1 px-2"><i class="ti ti-minus me-1"></i>Neutral</span>',
                    default    => '<span class="text-muted small">—</span>',
                };
            })

            // ── Status Badge (Clean Transparent Outline) ──────────────
            ->addColumn('status', function (Comment $comment) {
                return match ($comment->status) {
                    Comment::STATUS_PENDING_REVIEW, Comment::STATUS_NEW => '<span class="badge badge-outline text-warning py-1 px-2"><span class="status-dot bg-warning me-1.5" style="width: 6px; height: 6px;"></span>Pending Review</span>',
                    Comment::STATUS_REPLIED                             => '<span class="badge badge-outline text-success py-1 px-2"><i class="ti ti-check me-1"></i>Replied</span>',
                    Comment::STATUS_HIDDEN                              => '<span class="badge badge-outline text-secondary py-1 px-2"><i class="ti ti-eye-off me-1"></i>Hidden</span>',
                    Comment::STATUS_FLAGGED                             => '<span class="badge badge-outline text-danger py-1 px-2"><i class="ti ti-flag me-1"></i>Flagged</span>',
                    default                                             => '<span class="badge badge-outline text-secondary py-1 px-2">Ignored</span>',
                };
            })

            // ── Clean Primary Action + Three-Dots (⋮) Menu ────────────
            ->addColumn('actions', function (Comment $comment) {
                $primaryBtn = '';
                if ($comment->status !== Comment::STATUS_REPLIED) {
                    if (!empty($comment->ai_reply)) {
                        $primaryBtn = '
                            <form method="POST" action="' . route('admin.comments.approve', $comment->id) . '" class="d-inline m-0">
                                ' . csrf_field() . '
                                <button type="submit" class="btn btn-sm btn-outline-success px-2.5 d-inline-flex align-items-center gap-1" title="Approve & Send AI Draft Reply" style="height: 32px;">
                                    <i class="ti ti-check"></i>
                                    <span>Approve</span>
                                </button>
                            </form>
                        ';
                    } else {
                        $primaryBtn = '
                            <button type="button" class="btn btn-sm btn-outline-primary btn-reply-comment px-2.5 d-inline-flex align-items-center gap-1"
                                data-id="' . $comment->id . '"
                                data-name="' . e($comment->commenter_name ?? 'User') . '"
                                data-comment="' . e($comment->message ?? '') . '"
                                data-reply="' . e($comment->ai_reply ?? '') . '"
                                data-action="' . route('admin.comments.reply', $comment->id) . '"
                                style="height: 32px;">
                                <i class="ti ti-arrow-back-up"></i>
                                <span>Reply</span>
                            </button>
                        ';
                    }
                } else {
                    $primaryBtn = '
                        <button type="button" class="btn btn-sm btn-ghost-secondary btn-reply-comment px-2.5 d-inline-flex align-items-center gap-1"
                            data-id="' . $comment->id . '"
                            data-name="' . e($comment->commenter_name ?? 'User') . '"
                            data-comment="' . e($comment->message ?? '') . '"
                            data-reply="' . e($comment->ai_reply ?? '') . '"
                            data-action="' . route('admin.comments.reply', $comment->id) . '"
                            style="height: 32px;" title="Reply again">
                            <i class="ti ti-arrow-back-up"></i>
                            <span>Reply</span>
                        </button>
                    ';
                }

                // Platform External Link
                $platformName = 'Facebook';
                $platformUrl = null;
                if ($comment->account?->isInstagram()) {
                    $platformName = 'Instagram';
                    $platformUrl = $comment->post?->permalink_url ?? 'https://instagram.com';
                } elseif ($comment->account?->isYouTube()) {
                    $platformName = 'YouTube';
                    $ytId = $comment->post?->platform_post_id;
                    $platformUrl = $comment->post?->permalink_url ?? ($ytId ? "https://youtube.com/watch?v={$ytId}" : 'https://youtube.com');
                } else {
                    $platformName = 'Facebook';
                    $fbId = $comment->meta_comment_id;
                    $platformUrl = !empty($fbId) ? "https://facebook.com/{$fbId}" : ($comment->post?->permalink_url ?? 'https://facebook.com');
                }

                $hideTitle = $comment->status === Comment::STATUS_HIDDEN ? 'Unhide Comment' : 'Hide from Public';
                $hideIcon = $comment->status === Comment::STATUS_HIDDEN ? 'ti ti-eye' : 'ti ti-eye-off';
                $hideClass = $comment->status === Comment::STATUS_HIDDEN ? 'text-secondary' : 'text-danger';

                $dropdownMenu = '
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-sm btn-icon btn-ghost-secondary border" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 32px; height: 32px;" title="More Options">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end shadow-sm border py-1" style="min-width: 175px;">
                            <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2 btn-reply-comment"
                                data-id="' . $comment->id . '"
                                data-name="' . e($comment->commenter_name ?? 'User') . '"
                                data-comment="' . e($comment->message ?? '') . '"
                                data-reply="' . e($comment->ai_reply ?? '') . '"
                                data-action="' . route('admin.comments.reply', $comment->id) . '">
                                <i class="ti ti-edit"></i>
                                <span>Edit & Reply</span>
                            </button>
                            ' . ($platformUrl ? '
                            <a href="' . e($platformUrl) . '" target="_blank" class="dropdown-item d-flex align-items-center gap-2 py-2">
                                <i class="ti ti-external-link"></i>
                                <span>Open on ' . $platformName . '</span>
                            </a>' : '') . '
                            <div class="dropdown-divider my-1"></div>
                            <form method="POST" action="' . route('admin.comments.toggle-hide', $comment->id) . '" class="m-0">
                                ' . csrf_field() . '
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 ' . $hideClass . '">
                                    <i class="' . $hideIcon . '"></i>
                                    <span>' . $hideTitle . '</span>
                                </button>
                            </form>
                        </div>
                    </div>
                ';

                return '
                    <div class="d-inline-flex align-items-center gap-1 justify-content-end">
                        ' . $primaryBtn . '
                        ' . $dropdownMenu . '
                    </div>
                ';
            })

            ->rawColumns(['card', 'post', 'commenter', 'message', 'sentiment', 'status', 'actions']);
    }

    /**
     * Render the Modern Social Inbox Card (Feed View) for a Comment.
     * Aligned with Tabler design system: Clean 3px status stripe, native outline badges,
     * post context header, real commenter identity, threaded reply and minimal footer actions.
     */
    public function renderCardHtml(Comment $comment): string
    {
        $account = $comment->account;
        $post = $comment->post;
        $isInstagram = $account?->isInstagram();
        $isYouTube = $account?->isYouTube();
        $isFacebook = !$isInstagram && !$isYouTube;

        $platformName = $isYouTube ? 'YouTube' : ($isInstagram ? 'Instagram' : 'Facebook');
        $platformIcon = $isYouTube ? 'ti ti-brand-youtube text-danger' : ($isInstagram ? 'ti ti-brand-instagram text-pink' : 'ti ti-brand-facebook text-primary');

        // External Link to Post / Comment
        $platformUrl = null;
        if ($isInstagram) {
            $platformUrl = $post?->permalink_url ?? 'https://instagram.com';
        } elseif ($isYouTube) {
            $ytId = $post?->platform_post_id;
            $platformUrl = $post?->permalink_url ?? ($ytId ? "https://youtube.com/watch?v={$ytId}" : 'https://youtube.com');
        } else {
            $fbId = $comment->meta_comment_id;
            $platformUrl = !empty($fbId) ? "https://facebook.com/{$fbId}" : ($post?->permalink_url ?? 'https://facebook.com');
        }

        // Status Stripe Color & Badges (Vibrant High-Contrast Badges)
        $isReplied = ($comment->status === Comment::STATUS_REPLIED);
        $isPending = ($comment->status === Comment::STATUS_PENDING_REVIEW || $comment->status === Comment::STATUS_NEW);
        $isHidden = ($comment->status === Comment::STATUS_HIDDEN);
        $isFlagged = ($comment->status === Comment::STATUS_FLAGGED);

        $stripeColor = match (true) {
            $isReplied => '#2fb344', // Tabler Green
            $isPending => '#f59f00', // Tabler Yellow/Amber
            $isFlagged => '#d63939', // Tabler Red
            $isHidden  => '#626976', // Tabler Slate
            default    => '#929dab',
        };

        $statusBadge = match (true) {
            $isReplied => '<span class="badge bg-success-lt text-success px-2.5 py-1 fw-bold fs-7" style="border: 1px solid rgba(47, 179, 68, 0.35);"><i class="ti ti-check me-1"></i>Replied</span>',
            $isPending => '<span class="badge bg-warning-lt text-warning px-2.5 py-1 fw-bold fs-7" style="border: 1px solid rgba(245, 159, 0, 0.35);"><span class="status-dot bg-warning me-1.5" style="width: 7px; height: 7px; border-radius: 50%; display: inline-block;"></span>Pending Review</span>',
            $isFlagged => '<span class="badge bg-danger-lt text-danger px-2.5 py-1 fw-bold fs-7" style="border: 1px solid rgba(214, 57, 57, 0.35);"><i class="ti ti-flag me-1"></i>Flagged</span>',
            $isHidden  => '<span class="badge bg-secondary-lt text-secondary px-2.5 py-1 fw-bold fs-7" style="border: 1px solid rgba(98, 105, 118, 0.35);"><i class="ti ti-eye-off me-1"></i>Hidden</span>',
            default    => '<span class="badge bg-secondary-lt text-secondary px-2 py-1">Ignored</span>',
        };

        // Sentiment Badge (Soft-tinted with clean border)
        $sentimentBadge = match (strtolower($comment->sentiment ?? '')) {
            'positive' => '<span class="badge bg-success-lt text-success px-2 py-1 fs-7" style="border: 1px solid rgba(47, 179, 68, 0.25);"><i class="ti ti-mood-smile me-1"></i>Positive</span>',
            'negative' => '<span class="badge bg-danger-lt text-danger px-2 py-1 fs-7" style="border: 1px solid rgba(214, 57, 57, 0.25);"><i class="ti ti-mood-sad me-1"></i>Negative</span>',
            'neutral'  => '<span class="badge bg-secondary-lt text-secondary px-2 py-1 fs-7" style="border: 1px solid rgba(98, 105, 118, 0.25);"><i class="ti ti-minus me-1"></i>Neutral</span>',
            default    => '',
        };

        // Post Context Bar (Clean Thumbnail + Title + Date, no redundant platform badge)
        $postThumbHtml = '';
        $postCaption = 'Recent Post';
        $postDate = '';
        if ($post) {
            $thumbSrc = $post->thumbnail_url ?: $post->media_url;
            if (!empty($thumbSrc)) {
                $postThumbHtml = '<img src="' . e($thumbSrc) . '" class="rounded flex-shrink-0 border me-1.5" style="width: 26px; height: 26px; object-fit: cover;" alt="post media" onerror="this.style.display=\'none\';">';
            }
            $postCaption = !empty($post->message) ? Str::limit($post->message, 45) : (!empty($post->display_title) ? Str::limit($post->display_title, 45) : 'Post #' . substr($post->platform_post_id ?? $post->meta_post_id ?? '', -6));
            $postDate = $post->published_at ? $post->published_at->format('d M, H:i') : '';
        }

        // Commenter Identity & Avatar
        $defaultName = $isInstagram ? 'Instagram User' : ($isYouTube ? 'YouTube User' : 'Facebook User');
        $displayName = (!empty($comment->commenter_name) && strtolower($comment->commenter_name) !== 'user') ? $comment->commenter_name : $defaultName;
        $initial = strtoupper(substr($displayName, 0, 1));

        if (!empty($comment->commenter_photo_url)) {
            $userAvatar = '<span class="avatar avatar-sm rounded-circle border shadow-xs flex-shrink-0" style="background-image: url(' . e($comment->commenter_photo_url) . '); width: 36px; height: 36px;"></span>';
        } else {
            $userAvatar = '<span class="avatar avatar-sm rounded-circle bg-light text-dark fw-bold border flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.9rem;">' . e($initial) . '</span>';
        }

        $timeAgo = $comment->commented_at ? $comment->commented_at->diffForHumans() : $comment->created_at->diffForHumans();

        $likesHtml = '';
        if ($comment->likes_count > 0) {
            $likesHtml = '<span class="badge badge-outline text-danger ms-1 py-0 px-1.5 fs-7" title="' . $comment->likes_count . ' likes"><i class="ti ti-heart me-1"></i>' . $comment->likes_count . '</span>';
        }

        $commentMessageHtml = $this->formatExpandableText((string) ($comment->message ?? ''), 250, 'text-dark', '0.92rem');

        // AI Reply / Conversation Thread
        $replyThreadHtml = '';
        $accountName = $account?->name ?: 'Official Page';

        if ($isReplied && !empty($comment->ai_reply)) {
            // Channel Avatar
            $pageAvatar = '';
            if (!empty($account?->profile_pic_url)) {
                $pageAvatar = '<span class="avatar avatar-xs rounded-circle border flex-shrink-0" style="background-image: url(' . e($account->profile_pic_url) . '); width: 24px; height: 24px;"></span>';
            } else {
                $pageAvatar = '<span class="avatar avatar-xs rounded-circle bg-primary-lt text-primary border flex-shrink-0" style="width: 24px; height: 24px;"><i class="ti ti-robot fs-4"></i></span>';
            }

            $repliedAgo = $comment->replied_at ? $comment->replied_at->diffForHumans() : 'Replied';
            $replyBodyHtml = $this->formatExpandableText((string) $comment->ai_reply, 200, 'text-dark', '0.90rem');

            $replyThreadHtml = '
                <div class="mt-3 p-3 rounded-2" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-left: 3px solid #2fb344;">
                    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            ' . $pageAvatar . '
                            <span class="fw-bold text-dark small">' . e($accountName) . '</span>
                            <span class="badge bg-success-lt text-success px-2 py-0.5 fw-semibold fs-7" style="border: 1px solid rgba(47, 179, 68, 0.35);">
                                <i class="ti ti-check me-0.5"></i>Official Reply Sent
                            </span>
                        </div>
                        <span class="text-muted small fs-7"><i class="ti ti-clock me-0.5"></i>' . e($repliedAgo) . '</span>
                    </div>
                    <div class="text-dark" style="line-height: 1.5; font-size: 0.90rem;">
                        ' . $replyBodyHtml . '
                    </div>
                </div>
            ';
        } elseif ($isPending && !empty($comment->ai_reply)) {
            // Ready-to-approve AI Draft
            $replyBodyHtml = $this->formatExpandableText((string) $comment->ai_reply, 220, 'text-dark', '0.90rem');

            $replyThreadHtml = '
                <div class="mt-3 p-3 rounded-2" style="background-color: #fffbeb; border: 1px dashed #fcd34d; border-left: 3px solid #f59f00;">
                    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-1.5">
                            <span class="avatar avatar-xs rounded-circle bg-warning-lt text-warning flex-shrink-0" style="width: 22px; height: 22px;">
                                <i class="ti ti-sparkles fs-4"></i>
                            </span>
                            <span class="fw-bold text-dark small">AI Suggested Draft</span>
                        </div>
                        <span class="badge bg-warning-lt text-warning py-0.5 px-2 fs-7 fw-semibold" style="border: 1px solid rgba(245, 159, 0, 0.35);">
                            <span class="status-dot bg-warning me-1"></span>Action Required
                        </span>
                    </div>
                    <div class="p-2.5 bg-white rounded border mb-2.5 text-dark" style="line-height: 1.5; font-size: 0.90rem;">
                        ' . $replyBodyHtml . '
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <form method="POST" action="' . route('admin.comments.approve', $comment->id) . '" class="d-inline m-0 form-approve-reply">
                            ' . csrf_field() . '
                            <button type="submit" class="btn btn-sm btn-success px-3 d-inline-flex align-items-center gap-1.5 shadow-xs btn-approve-action" style="height: 32px;">
                                <i class="ti ti-check"></i>
                                <span class="fw-semibold">Approve & Send</span>
                            </button>
                        </form>
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3 d-inline-flex align-items-center gap-1.5 btn-reply-comment"
                            data-id="' . $comment->id . '"
                            data-name="' . e($displayName) . '"
                            data-comment="' . e($comment->message ?? '') . '"
                            data-reply="' . e($comment->ai_reply ?? '') . '"
                            data-action="' . route('admin.comments.reply', $comment->id) . '"
                            style="height: 32px;">
                            <i class="ti ti-edit"></i>
                            <span>Edit</span>
                        </button>
                    </div>
                </div>
            ';
        }

        // Hide / Unhide action metadata
        $hideTitle = $isHidden ? 'Unhide Comment' : 'Hide from Public';
        $hideIcon = $isHidden ? 'ti ti-eye' : 'ti ti-eye-off';
        $hideLabel = $isHidden ? 'Unhide' : 'Hide';

        return '
            <div class="card social-inbox-card mb-3 border shadow-xs" style="border-left: 4px solid ' . $stripeColor . ' !important; background: #ffffff;">
                <div class="card-header py-2 px-3 bg-light-subtle d-flex align-items-center justify-content-between flex-wrap gap-2 border-bottom">
                    <div class="d-flex align-items-center gap-1 min-w-0">
                        ' . $postThumbHtml . '
                        <div class="text-truncate" style="max-width: 360px;">
                            <span class="fw-medium text-dark small" title="' . e($post?->message ?? $post?->display_title ?? '') . '">' . e($postCaption) . '</span>
                            ' . ($postDate ? '<span class="text-muted fs-7 ms-1">(' . e($postDate) . ')</span>' : '') . '
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 ms-auto">
                        ' . $sentimentBadge . '
                        ' . $statusBadge . '
                        <span class="text-muted small d-inline-flex align-items-center gap-1 ms-1 fs-7">
                            <i class="ti ti-clock"></i> ' . e($timeAgo) . '
                        </span>
                    </div>
                </div>

                <div class="card-body p-3">
                    <div class="d-flex align-items-start gap-2.5">
                        ' . $userAvatar . '
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex align-items-center gap-1 mb-0.5 flex-wrap">
                                <span class="fw-semibold text-dark fs-4">' . e($displayName) . '</span>
                                ' . $likesHtml . '
                            </div>
                            <div class="text-dark" style="line-height: 1.5;">
                                ' . $commentMessageHtml . '
                            </div>
                        </div>
                    </div>
                    ' . $replyThreadHtml . '
                </div>

                <div class="card-footer py-2 px-3 bg-light-subtle d-flex align-items-center justify-content-end gap-2 border-top">
                    ' . ($platformUrl ? '
                    <a href="' . e($platformUrl) . '" target="_blank" class="btn btn-sm btn-action-platform d-inline-flex align-items-center gap-1.5" style="height: 32px;" title="View on ' . $platformName . '">
                        <i class="' . $platformIcon . '"></i>
                        <span>View on ' . $platformName . '</span>
                        <i class="ti ti-external-link fs-7"></i>
                    </a>' : '') . '
                    <button type="button" class="btn btn-sm btn-outline-primary btn-action-reply d-inline-flex align-items-center gap-1.5 btn-reply-comment"
                        data-id="' . $comment->id . '"
                        data-name="' . e($displayName) . '"
                        data-comment="' . e($comment->message ?? '') . '"
                        data-reply="' . e($comment->ai_reply ?? '') . '"
                        data-action="' . route('admin.comments.reply', $comment->id) . '"
                        style="height: 32px;">
                        <i class="ti ti-arrow-back-up"></i>
                        <span>' . ($isReplied ? 'Reply Again' : 'Reply') . '</span>
                    </button>
                    <form method="POST" action="' . route('admin.comments.toggle-hide', $comment->id) . '" class="d-inline m-0 form-toggle-hide">
                        ' . csrf_field() . '
                        <button type="submit" class="btn btn-sm ' . ($isHidden ? 'btn-outline-secondary' : 'btn-outline-danger btn-action-hide') . ' d-inline-flex align-items-center gap-1.5" style="height: 32px;" title="' . $hideTitle . '">
                            <i class="' . $hideIcon . '"></i>
                            <span>' . $hideLabel . '</span>
                        </button>
                    </form>
                </div>
            </div>
        ';
    }

    public function query(Comment $model): Builder
    {
        $user = auth()->user();

        $query = $model->newQuery()
            ->with(['post', 'account'])
            ->select('comments.*')
            ->whereNull('parent_comment_id')
            ->where('commenter_name', 'not like', '%(Page)%')
            ->latest('commented_at');

        // Scope to user's accounts if not admin
        if (!$user->isAdmin()) {
            $userAccountIds = Account::where('user_id', $user->id)->pluck('id')->toArray();
            $query->whereIn('account_id', $userAccountIds);
        }

        // Filter by Account Platform / Type (e.g. 1=FB, 2=IG, 3=YT)
        if (request()->filled('account_type')) {
            $type = (int) request('account_type');
            $query->whereHas('account', fn($q) => $q->where('account_type', $type));
        } elseif (request()->filled('platform')) {
            $type = (int) request('platform');
            $query->whereHas('account', fn($q) => $q->where('account_type', $type));
        }

        // Filter by Account
        if (request()->filled('account_id')) {
            $query->where('account_id', request('account_id'));
        }

        // Filter by Status (supports integer codes and friendly tab strings)
        if (request()->filled('status')) {
            $status = request('status');
            if ($status === 'pending') {
                $query->whereIn('status', [Comment::STATUS_NEW, Comment::STATUS_PENDING_REVIEW]);
            } elseif ($status === 'replied') {
                $query->where('status', Comment::STATUS_REPLIED);
            } elseif ($status === 'hidden') {
                $query->where('status', Comment::STATUS_HIDDEN);
            } elseif ($status === 'flagged') {
                $query->where('status', Comment::STATUS_FLAGGED);
            } elseif (is_numeric($status)) {
                $query->where('status', (int) $status);
            }
        }

        // Filter by Sentiment
        if (request()->filled('sentiment')) {
            $query->where('sentiment', request('sentiment'));
        }

        return $query;
    }

    /**
     * Format text with interactive inline 'more' / 'less' toggle when exceeding character limit.
     */
    protected function formatExpandableText(string $text, int $limit = 140, string $colorClass = 'text-dark', string $fontSize = '0.88rem', string $suffixHtml = ''): string
    {
        $text = trim($text);
        if (mb_strlen($text) <= $limit) {
            return '<div class="' . $colorClass . '" style="line-height: 1.45; word-break: break-word; font-size: ' . $fontSize . ';">' . nl2br(e($text)) . $suffixHtml . '</div>';
        }

        $short = mb_substr($text, 0, $limit);
        $lastSpace = mb_strrpos($short, ' ');
        if ($lastSpace !== false && $lastSpace > ($limit - 30)) {
            $short = mb_substr($short, 0, $lastSpace);
        }

        return '
            <div class="comment-text-expandable ' . $colorClass . '" style="line-height: 1.45; word-break: break-word; font-size: ' . $fontSize . ';">
                <span class="comment-text-short">' . nl2br(e($short)) . '... <a href="javascript:void(0)" class="text-primary fw-semibold text-decoration-none btn-comment-more">more</a>' . $suffixHtml . '</span>
                <span class="comment-text-full d-none">' . nl2br(e($text)) . ' <a href="javascript:void(0)" class="text-muted small text-decoration-none btn-comment-less ms-1"><i class="ti ti-chevron-up"></i> less</a>' . $suffixHtml . '</span>
            </div>
        ';
    }

    public function filename(): string
    {
        return 'Comments_' . date('Y_m_d_His');
    }
}

