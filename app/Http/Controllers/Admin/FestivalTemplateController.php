<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Festival;
use App\Models\FestivalTemplate;
use App\Models\PoliticalParty;
use App\Services\PosterComposer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FestivalTemplateController extends Controller
{
    /**
     * Display a listing of festival templates.
     */
    public function index(Request $request)
    {
        $query = FestivalTemplate::query()->with(['festival', 'party']);

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('festival', fn($f) => $f->where('title', 'like', "%{$search}%"));
            });
        }

        if ($festivalId = $request->input('festival_id')) {
            $query->where('festival_id', $festivalId);
        }

        if ($partyId = $request->input('party_id')) {
            $query->where('party_id', $partyId);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === '1');
        }

        $templates = $query->latest()->paginate(24)->withQueryString();
        $festivals = Festival::orderBy('festival_date', 'desc')->get();
        $parties = PoliticalParty::orderBy('name')->get();

        return view('admin.festival_templates.index', compact('templates', 'festivals', 'parties'));
    }

    /**
     * Show the form for creating a new festival template.
     */
    public function create()
    {
        $festivals = Festival::orderBy('festival_date', 'desc')->get();
        $parties = PoliticalParty::orderBy('name')->get();
        $presets = app(PosterComposer::class)->getAvailablePresets();

        return view('admin.festival_templates.create', compact('festivals', 'parties', 'presets'));
    }

    /**
     * Store a newly created festival template in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'festival_id' => 'nullable|exists:festivals,id',
            'new_festival_title' => 'nullable|string|max:191',
            'new_festival_date' => 'nullable|date',
            'party_id' => 'nullable|exists:political_parties,id',
            'title' => 'required|string|max:191',
            'template_image' => 'required|image|mimes:png,jpg,jpeg,webp|max:15360',
            'preset_key' => 'required|string|max:60',
            'photo_position' => 'required|in:bottom-right,bottom-left,footer-strip',
            'is_active' => 'nullable|boolean',
        ]);

        $festivalId = $validated['festival_id'] ?? null;

        // If inline new festival is given
        if (!empty($validated['new_festival_title'])) {
            $festival = Festival::create([
                'title' => $validated['new_festival_title'],
                'festival_date' => $validated['new_festival_date'] ?? now()->toDateString(),
                'category' => 'general',
                'is_active' => true,
            ]);
            $festivalId = $festival->id;
        }

        if (!$festivalId) {
            return back()->withInput()->withErrors(['festival_id' => 'Please select an existing festival or provide a new festival title.']);
        }

        $file = $request->file('template_image');
        $filename = 'template_' . Str::slug($validated['title']) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $imagePath = $file->storeAs('uploads/festival_templates', $filename, 'public');

        $template = FestivalTemplate::create([
            'festival_id' => $festivalId,
            'party_id' => $validated['party_id'] ?? null,
            'title' => $validated['title'],
            'template_image_path' => $imagePath,
            'thumbnail_path' => $imagePath,
            'preset_key' => $validated['preset_key'],
            'photo_position' => $validated['photo_position'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.festival-templates.index')
            ->with('success', "Festival template '{$template->title}' added successfully.");
    }

    /**
     * Show the form for editing the specified festival template.
     */
    public function edit(FestivalTemplate $festivalTemplate)
    {
        $festivals = Festival::orderBy('festival_date', 'desc')->get();
        $parties = PoliticalParty::orderBy('name')->get();
        $presets = app(PosterComposer::class)->getAvailablePresets();

        return view('admin.festival_templates.edit', compact('festivalTemplate', 'festivals', 'parties', 'presets'));
    }

    /**
     * Update the specified festival template in storage.
     */
    public function update(Request $request, FestivalTemplate $festivalTemplate)
    {
        $validated = $request->validate([
            'festival_id' => 'required|exists:festivals,id',
            'party_id' => 'nullable|exists:political_parties,id',
            'title' => 'required|string|max:191',
            'template_image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:15360',
            'preset_key' => 'required|string|max:60',
            'photo_position' => 'required|in:bottom-right,bottom-left,footer-strip',
            'is_active' => 'nullable|boolean',
        ]);

        $data = [
            'festival_id' => $validated['festival_id'],
            'party_id' => $validated['party_id'] ?? null,
            'title' => $validated['title'],
            'preset_key' => $validated['preset_key'],
            'photo_position' => $validated['photo_position'],
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('template_image')) {
            if ($festivalTemplate->template_image_path && Storage::disk('public')->exists($festivalTemplate->template_image_path)) {
                Storage::disk('public')->delete($festivalTemplate->template_image_path);
            }
            $file = $request->file('template_image');
            $filename = 'template_' . Str::slug($validated['title']) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $data['template_image_path'] = $file->storeAs('uploads/festival_templates', $filename, 'public');
            $data['thumbnail_path'] = $data['template_image_path'];
        }

        $festivalTemplate->update($data);

        return redirect()->route('admin.festival-templates.index')
            ->with('success', "Festival template '{$festivalTemplate->title}' updated successfully.");
    }

    /**
     * Remove the specified festival template from storage.
     */
    public function destroy(FestivalTemplate $festivalTemplate)
    {
        $title = $festivalTemplate->title;

        if ($festivalTemplate->template_image_path && Storage::disk('public')->exists($festivalTemplate->template_image_path)) {
            Storage::disk('public')->delete($festivalTemplate->template_image_path);
        }

        $festivalTemplate->delete();

        return redirect()->route('admin.festival-templates.index')
            ->with('success', "Template '{$title}' deleted successfully.");
    }
}
