<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Festival;
use App\Models\FestivalTemplate;
use App\Models\PoliticalParty;
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
            'partyFilter'
        ));
    }
}
