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
            ->upcoming()
            ->with(['templates' => function ($query) {
                $query->where('is_active', true);
            }])
            ->take(15)
            ->get();

        $selectedFestivalId = $request->query('festival_id', $upcomingFestivals->first()?->id);
        $selectedFestival = $upcomingFestivals->firstWhere('id', $selectedFestivalId) ?? $upcomingFestivals->first();

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
