<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Festival;
use Illuminate\Http\Request;

class FestivalController extends Controller
{
    public static array $categories = [
        'religious_festival' => 'Religious Festival (धार्मिक पर्व)',
        'national_day' => 'National Day (राष्ट्रीय दिवस)',
        'jayanti_punyatithi' => 'Jayanti / Memorial (जयंती / पुण्यतिथि)',
        'international_day' => 'International Day (अंतर्राष्ट्रीय दिवस)',
        'general' => 'General (सामान्य)',
    ];

    /**
     * Display a listing of festivals and special days.
     */
    public function index(Request $request)
    {
        $query = Festival::query()->withCount('templates');

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('ready_caption', 'like', "%{$search}%")
                  ->orWhere('hashtags', 'like', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === '1');
        }

        if ($timeFilter = $request->input('time')) {
            if ($timeFilter === 'upcoming') {
                $query->where('festival_date', '>=', now()->toDateString());
            } elseif ($timeFilter === 'past') {
                $query->where('festival_date', '<', now()->toDateString());
            }
        }

        $festivals = $query->orderBy('festival_date', 'asc')->paginate(15)->withQueryString();
        $categories = self::$categories;

        return view('admin.festivals.index', compact('festivals', 'categories'));
    }

    /**
     * Show the form for creating a new festival.
     */
    public function create()
    {
        $categories = self::$categories;
        return view('admin.festivals.create', compact('categories'));
    }

    /**
     * Store a newly created festival in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:191',
            'festival_date' => 'required|date',
            'category' => 'required|string|max:50',
            'ready_caption' => 'nullable|string',
            'hashtags' => 'nullable|string',
            'is_active' => 'required|in:0,1',
        ]);

        $validated['is_active'] = (bool) $validated['is_active'];

        $festival = Festival::create($validated);

        return redirect()->route('admin.festivals.index')
            ->with('success', "Festival '{$festival->title}' added successfully.");
    }

    /**
     * Show the form for editing the specified festival.
     */
    public function edit(Festival $festival)
    {
        $categories = self::$categories;
        return view('admin.festivals.edit', compact('festival', 'categories'));
    }

    /**
     * Update the specified festival in storage.
     */
    public function update(Request $request, Festival $festival)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:191',
            'festival_date' => 'required|date',
            'category' => 'required|string|max:50',
            'ready_caption' => 'nullable|string',
            'hashtags' => 'nullable|string',
            'is_active' => 'required|in:0,1',
        ]);

        $validated['is_active'] = (bool) $validated['is_active'];

        $festival->update($validated);

        return redirect()->route('admin.festivals.index')
            ->with('success', "Festival '{$festival->title}' updated successfully.");
    }

    /**
     * Remove the specified festival from storage.
     */
    public function destroy(Festival $festival)
    {
        $title = $festival->title;
        $festival->delete();

        return redirect()->route('admin.festivals.index')
            ->with('success', "Festival '{$title}' deleted successfully.");
    }
}
