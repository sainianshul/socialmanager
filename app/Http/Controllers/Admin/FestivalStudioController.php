<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Festival;
use App\Models\FestivalTemplate;
use App\Models\PoliticalParty;
use App\Models\ScheduledPost;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FestivalStudioController extends Controller
{
    public function index(Request $request)
    {
        $upcomingFestivals = Festival::active()
            ->where('festival_date', '>=', now()->subDays(14)->toDateString())
            ->orderBy('festival_date', 'asc')
            ->take(20)
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

        // Support seamless AJAX date switching without full page refresh
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'festival_id' => $selectedFestival?->id,
                'title' => $selectedFestival?->title,
                'festival_date' => $selectedFestival?->festival_date?->format('d M Y'),
                'caption' => $selectedFestival?->ready_caption,
                'hashtags' => $selectedFestival?->hashtags,
                'html' => view('admin.festival_studio.partials._content', compact(
                    'selectedFestival',
                    'templates'
                ))->render(),
            ]);
        }

        $user = auth()->user();
        $userBranding = $user ? ($user->defaultBranding ?? $user->brandings()->first()) : null;
        $parties = PoliticalParty::where('is_active', true)->get();

        $accountsQuery = Account::query()
            ->where('is_active', true)
            ->whereIn('account_type', [Account::TYPE_FACEBOOK, Account::TYPE_INSTAGRAM]);
        if ($user && !$user->isAdmin()) {
            $accountsQuery->where('user_id', $user->id);
        }
        $accounts = $accountsQuery->orderBy('account_type')->orderBy('name')->get();

        $userCutout = $userBranding?->defaultImage?->cutout_url 
            ?? ($user?->profile_photo ? asset('storage/' . $user->profile_photo) : null)
            ?? 'https://ui-avatars.com/api/?name=' . urlencode($user?->name ?? 'User') . '&background=1db8ab&color=fff&bold=true&size=128';

        return view('admin.festival_studio.index', compact(
            'upcomingFestivals',
            'selectedFestival',
            'templates',
            'userBranding',
            'userCutout',
            'parties',
            'partyFilter',
            'accounts'
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
            'schedule_date' => 'required|date',
            'schedule_time' => 'required|string',
            'content'       => 'nullable|string|max:5000',
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

        $template = FestivalTemplate::findOrFail($validated['template_id']);
        $mediaUrls = [$template->template_image_path];

        try {
            $scheduledAt = Carbon::parse($validated['schedule_date'] . ' ' . $validated['schedule_time']);
        } catch (\Throwable $e) {
            $scheduledAt = Carbon::parse($validated['schedule_date'])->setTime(8, 0);
        }

        $createdPosts = [];
        foreach ($accounts as $account) {
            $platform = (int) $account->account_type;

            $scheduledPost = ScheduledPost::create([
                'user_id'        => $user->id,
                'account_id'     => $account->id,
                'platform'       => $platform,
                'post_type'      => ScheduledPost::TYPE_PHOTO,
                'title'          => $template->title,
                'content'        => $validated['content'] ?? '',
                'media_urls'     => $mediaUrls,
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
            'scheduled_formatted' => $scheduledAt->format('d M, h:i A'),
            'posts_count' => count($createdPosts),
        ]);
    }
}
