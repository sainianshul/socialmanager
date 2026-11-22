<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CardTemplate;
use App\Models\PoliticalParty;
use App\Services\PosterComposer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CardTemplateController extends Controller
{
    /**
     * Display a listing of card templates.
     */
    public function index(Request $request)
    {
        $query = CardTemplate::query()->with('party');

        if ($search = trim((string) $request->input('search'))) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        if ($partyId = $request->input('party_id')) {
            $query->where('party_id', $partyId);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === '1');
        }

        $templates = $query->latest()->paginate(24)->withQueryString();
        $categories = CardTemplate::categories();
        $parties = PoliticalParty::orderBy('name')->get();

        return view('admin.card_templates.index', compact('templates', 'categories', 'parties'));
    }

    /**
     * Show the form for creating a new card template.
     */
    public function create()
    {
        $categories = CardTemplate::categories();
        $parties = PoliticalParty::orderBy('name')->get();
        $presets = app(PosterComposer::class)->getAvailablePresets();

        return view('admin.card_templates.create', compact('categories', 'parties', 'presets'));
    }

    /**
     * Store a newly created card template in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:191',
            'category' => 'required|string|max:60',
            'party_id' => 'nullable|exists:political_parties,id',
            'template_image' => 'required|image|mimes:png,jpg,jpeg,webp|max:15360',
            'preset_key' => 'required|string|max:60',
            'photo_position' => 'required|in:bottom-right,bottom-left,footer-strip',
            'is_active' => 'nullable|boolean',
        ]);

        $file = $request->file('template_image');
        $filename = 'card_' . Str::slug($validated['title']) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $imagePath = $file->storeAs('uploads/card_templates', $filename, 'public');

        $template = CardTemplate::create([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'party_id' => $validated['party_id'] ?? null,
            'template_image_path' => $imagePath,
            'thumbnail_path' => $imagePath,
            'preset_key' => $validated['preset_key'],
            'photo_position' => $validated['photo_position'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.card-templates.index')
            ->with('success', "Card template '{$template->title}' added successfully.");
    }

    /**
     * Show the form for editing the specified card template.
     */
    public function edit(CardTemplate $cardTemplate)
    {
        $categories = CardTemplate::categories();
        $parties = PoliticalParty::orderBy('name')->get();
        $presets = app(PosterComposer::class)->getAvailablePresets();

        return view('admin.card_templates.edit', compact('cardTemplate', 'categories', 'parties', 'presets'));
    }

    /**
     * Update the specified card template in storage.
     */
    public function update(Request $request, CardTemplate $cardTemplate)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:191',
            'category' => 'required|string|max:60',
            'party_id' => 'nullable|exists:political_parties,id',
            'template_image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:15360',
            'preset_key' => 'required|string|max:60',
            'photo_position' => 'required|in:bottom-right,bottom-left,footer-strip',
            'is_active' => 'nullable|boolean',
        ]);

        $data = [
            'title' => $validated['title'],
            'category' => $validated['category'],
            'party_id' => $validated['party_id'] ?? null,
            'preset_key' => $validated['preset_key'],
            'photo_position' => $validated['photo_position'],
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('template_image')) {
            if ($cardTemplate->template_image_path && Storage::disk('public')->exists($cardTemplate->template_image_path)) {
                Storage::disk('public')->delete($cardTemplate->template_image_path);
            }
            $file = $request->file('template_image');
            $filename = 'card_' . Str::slug($validated['title']) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $data['template_image_path'] = $file->storeAs('uploads/card_templates', $filename, 'public');
            $data['thumbnail_path'] = $data['template_image_path'];
        }

        $cardTemplate->update($data);

        return redirect()->route('admin.card-templates.index')
            ->with('success', "Card template '{$cardTemplate->title}' updated successfully.");
    }

    /**
     * Remove the specified card template from storage.
     */
    public function destroy(CardTemplate $cardTemplate)
    {
        $title = $cardTemplate->title;

        if ($cardTemplate->template_image_path && Storage::disk('public')->exists($cardTemplate->template_image_path)) {
            Storage::disk('public')->delete($cardTemplate->template_image_path);
        }

        $cardTemplate->delete();

        return redirect()->route('admin.card-templates.index')
            ->with('success', "Card template '{$title}' deleted successfully.");
    }
}
