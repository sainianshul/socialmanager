<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Festival;
use App\Models\FestivalTemplate;
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

        $templates = $selectedFestival
            ? FestivalTemplate::where('festival_id', $selectedFestival->id)
                ->where('is_active', true)
                ->with('party')
                ->get()
            : collect();

        $userBranding = auth()->user()->defaultBranding ?? auth()->user()->brandings()->first();

        return view('admin.festival_studio.index', compact(
            'upcomingFestivals',
            'selectedFestival',
            'templates',
            'userBranding'
        ));
    }
}
