<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Festival;
use App\Models\FestivalTemplate;
use App\Models\LeaderStrip;
use App\Models\PoliticalParty;
use App\Models\ScheduledPost;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FestivalStudioController extends Controller
{
    public function index(Request $request)
    {
        $upcomingFestivals = Festival::active()
            ->where('festival_date', '>=', now()->toDateString())
            ->orderBy('festival_date', 'asc')
            ->take(30)
            ->get();

        $todayFestival = $upcomingFestivals->first(fn($f) => $f->festival_date->isToday());
        $selectedFestivalId = $request->query('festival_id', $todayFestival?->id ?? $upcomingFestivals->first()?->id);
        $selectedFestival = $upcomingFestivals->firstWhere('id', (int) $selectedFestivalId) ?? $upcomingFestivals->first();

        $partyFilter = $request->query('party', 'all');

        $templatesQuery = $selectedFestival
            ? FestivalTemplate::where('festival_id', $selectedFestival->id)
                ->where('is_active', true)
                ->with('party')
            : FestivalTemplate::whereRaw('1 = 0');

        if (!empty($partyFilter) && $partyFilter !== 'all') {
            $party = PoliticalParty::where('short_code', strtoupper($partyFilter))->first();
            if ($party) {
                $templatesQuery->where(function ($q) use ($party) {
                    $q->where('party_id', $party->id)->orWhereNull('party_id');
                });
            }
        }

        $templates = $templatesQuery->get();

        $user = auth()->user();

        // Get IDs of festivals and templates already scheduled by this user
        $scheduledFestivalIds = [];
        $scheduledTemplateIds = [];
        if ($user) {
            $scheduledPosts = ScheduledPost::where('user_id', $user->id)
                ->whereIn('status', [ScheduledPost::STATUS_SCHEDULED, ScheduledPost::STATUS_PUBLISHED])
                ->get();

            $scheduledFestivalIds = $scheduledPosts->pluck('festival_id')->filter()->unique()->toArray();
            $scheduledTemplateIds = $scheduledPosts->pluck('template_id')->filter()->unique()->toArray();

            // Match by scheduled date for any posts scheduled on that festival date
            $scheduledDates = $scheduledPosts->pluck('scheduled_at')->map(fn($d) => $d?->toDateString())->filter()->unique()->toArray();
            foreach ($upcomingFestivals as $f) {
                if (in_array($f->festival_date->toDateString(), $scheduledDates)) {
                    $scheduledFestivalIds[] = $f->id;
                }
            }
            $scheduledFestivalIds = array_values(array_unique($scheduledFestivalIds));
        }

        $userBranding = $user ? ($user->defaultBranding ?? $user->brandings()->first()) : null;
        $partyId = $userBranding?->party_id;

        $userStrips = $user 
            ? $user->leaderStrips()->with(['leaders', 'party'])->orderByDesc('is_default')->get() 
            : collect();

        $systemStrip = null;
        if ($partyId) {
            $systemStrip = LeaderStrip::where('party_id', $partyId)
                ->where('is_system', true)
                ->where('is_default', true)
                ->with(['leaders', 'party'])
                ->first();
        }

        $activeStrip = $user?->defaultLeaderStrip ?? $systemStrip;
        $allStrips = $userStrips->merge($systemStrip ? collect([$systemStrip]) : collect())->unique('id');

        $userCutout = $userBranding?->defaultImage?->cutout_url 
            ?? ($user?->profile_photo ? asset('storage/' . $user->profile_photo) : null)
            ?? 'https://ui-avatars.com/api/?name=' . urlencode($user?->name ?? 'User') . '&background=1db8ab&color=fff&bold=true&size=128';

        // Support seamless AJAX date switching without full page refresh
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'festival_id' => $selectedFestival?->id,
                'title' => $selectedFestival?->title,
                'festival_date' => $selectedFestival?->festival_date?->format('d M Y'),
                'is_today' => $selectedFestival?->festival_date?->isToday() ?? false,
                'caption' => $selectedFestival?->ready_caption,
                'hashtags' => $selectedFestival?->hashtags,
                'html' => view('admin.festival_studio.partials._content', compact(
                    'selectedFestival',
                    'templates',
                    'scheduledTemplateIds',
                    'activeStrip',
                    'allStrips',
                    'userBranding',
                    'userCutout'
                ))->render(),
            ]);
        }

        $parties = PoliticalParty::where('is_active', true)->get();

        $accountsQuery = Account::query()
            ->where('is_active', true)
            ->whereIn('account_type', [Account::TYPE_FACEBOOK, Account::TYPE_INSTAGRAM]);
        if ($user && !$user->isAdmin()) {
            $accountsQuery->where('user_id', $user->id);
        }
        $accounts = $accountsQuery->orderBy('account_type')->orderBy('name')->get();

        return view('admin.festival_studio.index', compact(
            'upcomingFestivals',
            'selectedFestival',
            'templates',
            'userBranding',
            'userCutout',
            'parties',
            'partyFilter',
            'accounts',
            'scheduledFestivalIds',
            'scheduledTemplateIds',
            'activeStrip',
            'allStrips'
        ));
    }

    /**
     * One-Click Quick Schedule Festival Poster to Connected Social Channels
     */
    public function quickSchedule(Request $request)
    {
        $validated = $request->validate([
            'template_id'   => 'required|exists:festival_templates,id',
            'festival_id'   => 'nullable|exists:festivals,id',
            'account_ids'   => 'required|array|min:1',
            'account_ids.*' => 'integer|exists:accounts,id',
            'schedule_date'  => 'required|date',
            'schedule_time'  => 'required|string',
            'content'        => 'nullable|string|max:5000',
            'composed_image' => 'nullable|string',
            'include_strip'  => 'nullable|boolean',
        ]);

        $user = auth()->user();
        $accounts = Account::whereIn('id', $validated['account_ids'])->get();

        if ($accounts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Please select at least one active social media channel.',
            ], 422);
        }

        if (!$user->isAdmin()) {
            foreach ($accounts as $account) {
                if ($account->user_id !== $user->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized account selection.',
                    ], 403);
                }
            }
        }

        $template = FestivalTemplate::with('party')->findOrFail($validated['template_id']);

        $footerStyle = $request->input('footer_style');
        $headerLayout = $request->input('header_layout');

        if (!empty($validated['composed_image'])) {
            $mediaUrls = [$validated['composed_image']];
        } else {
            try {
                $composer = app(\App\Services\PosterComposer::class);
                $branding = $user->defaultBranding ?? $user->brandings()->first();
                $strip = $user->defaultLeaderStrip;
                $includeStrip = $request->boolean('include_strip', true);

                $options = [
                    'include_strip' => $includeStrip,
                    'layout_config' => [],
                ];

                $templateConfig = $template->layout_config ?? [];

                // If footer is fixed on template, enforce it
                if (!empty($templateConfig['footer']['is_fixed']) && !empty($templateConfig['footer']['style'])) {
                    $footerStyle = $templateConfig['footer']['style'];
                }
                if (!empty($footerStyle)) {
                    $options['layout_config']['footer']['style'] = $footerStyle;
                }

                // If header is fixed on template, enforce it
                if (!empty($templateConfig['header']['is_fixed']) && isset($templateConfig['header']['layout'])) {
                    $headerLayout = $templateConfig['header']['layout'];
                }
                if (!empty($headerLayout)) {
                    if ($headerLayout === 'none') {
                        $options['include_strip'] = false;
                        $options['layout_config']['header']['enabled'] = false;
                    } else {
                        $options['include_strip'] = true;
                        $options['layout_config']['header']['enabled'] = true;
                        $options['layout_config']['header']['layout'] = $headerLayout;
                        $options['layout_config']['header']['mode'] = ($headerLayout === 'party_group_cutout') ? 'party_group_cutout' : 'individual_chips';
                    }
                }

                $leaderShape = $request->input('leader_shape') ?: ($templateConfig['header']['chip_shape'] ?? 'circle');
                $leaderBorder = $request->input('leader_border_color') ?: ($templateConfig['header']['chip_border_color'] ?? '#FF9933');
                $options['layout_config']['header']['chip_shape'] = $leaderShape;
                $options['layout_config']['header']['chip_border_color'] = $leaderBorder;

                $composed = $composer->composeToFile($template, $branding, $strip, $options);
                $mediaUrls = [$composed['relative_path']];
            } catch (\Throwable $e) {
                $mediaUrls = [$template->template_image_path];
            }
        }

        $timeString = !empty($validated['schedule_time']) ? $validated['schedule_time'] : '08:00';
        try {
            $scheduledAt = Carbon::parse($validated['schedule_date'] . ' ' . $timeString);
        } catch (\Throwable $e) {
            $scheduledAt = Carbon::parse($validated['schedule_date'])->setTime(8, 0);
        }

        // If scheduled for today and time already passed, schedule 5 minutes from now
        if ($scheduledAt->isPast()) {
            $scheduledAt = now()->addMinutes(5);
        }

        $festivalId = $validated['festival_id'] ?? $template->festival_id;

        $createdPosts = [];
        foreach ($accounts as $account) {
            $platform = (int) $account->account_type;

            $scheduledPost = ScheduledPost::create([
                'user_id'        => $user->id,
                'account_id'     => $account->id,
                'festival_id'    => $festivalId,
                'template_id'    => $template->id,
                'platform'       => $platform,
                'post_type'      => ScheduledPost::TYPE_PHOTO,
                'title'          => $template->title,
                'content'        => $validated['content'] ?? '',
                'media_urls'     => $mediaUrls,
                'thumbnail_path' => $mediaUrls[0] ?? null,
                'scheduled_at'   => $scheduledAt,
                'status'         => ScheduledPost::STATUS_SCHEDULED,
                'privacy_status' => 'public',
            ]);

            $createdPosts[] = $scheduledPost;
        }

        return response()->json([
            'success' => true,
            'message' => 'Poster scheduled successfully for ' . $scheduledAt->format('d M Y, h:i A') . ' across ' . count($createdPosts) . ' account(s).',
            'template_id' => $template->id,
            'festival_id' => $festivalId,
            'scheduled_formatted' => $scheduledAt->format('d M, h:i A'),
            'posts_count' => count($createdPosts),
        ]);
    }
}
