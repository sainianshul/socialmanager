<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Festival;
use App\Models\FestivalTemplate;
use App\Models\LeaderStrip;
use App\Models\PoliticalLeader;
use App\Models\PoliticalParty;
use App\Models\UserBranding;
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
            'preset_key' => 'nullable|string|max:60',
            'photo_position' => 'nullable|in:bottom-right,bottom-left,footer-strip',
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

        $layoutConfig = [];
        if ($request->filled('leader_shape') || $request->filled('leader_border_color')) {
            $layoutConfig['header']['chip_shape'] = $request->input('leader_shape', 'circle');
            $layoutConfig['header']['chip_border_color'] = $request->input('leader_border_color', '#FF9933');
            $layoutConfig['header']['enabled'] = $request->boolean('show_top_strip', true);
        }
        if ($request->filled('header_layout')) {
            $headerLayout = $request->input('header_layout');
            if ($headerLayout === 'none') {
                $layoutConfig['header']['enabled'] = false;
            } else {
                $layoutConfig['header']['enabled'] = true;
                $layoutConfig['header']['layout'] = $headerLayout;
                $layoutConfig['header']['mode'] = ($headerLayout === 'party_group_cutout') ? 'party_group_cutout' : 'individual_chips';
            }
        }
        if ($request->filled('footer_style')) {
            $layoutConfig['footer']['style'] = $request->input('footer_style');
        }
        if ($request->filled('name_color')) {
            $layoutConfig['footer']['name_color'] = $request->input('name_color');
        }
        if ($request->filled('footer_bg')) {
            $layoutConfig['footer']['main_bar_bg'] = $request->input('footer_bg');
        }

        $presetKey = $validated['preset_key'] ?? ($request->input('photo_position') === 'bottom-left' ? 'classic_political_left' : 'classic_political_right');
        $photoPosition = $validated['photo_position'] ?? ($request->input('footer_style') === 'candidate_left_ribbon' ? 'bottom-left' : 'bottom-right');

        $template = FestivalTemplate::create([
            'festival_id' => $festivalId,
            'party_id' => $validated['party_id'] ?? null,
            'title' => $validated['title'],
            'template_image_path' => $imagePath,
            'thumbnail_path' => $imagePath,
            'preset_key' => $presetKey,
            'photo_position' => $photoPosition,
            'layout_config' => !empty($layoutConfig) ? $layoutConfig : null,
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
        $headerLayout = $festivalTemplate->layout_config['header']['layout'] ?? 'full_width_strip';

        return view('admin.festival_templates.edit', compact('festivalTemplate', 'festivals', 'parties', 'presets', 'headerLayout'));
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

        $layoutConfig = $festivalTemplate->layout_config ?? [];
        if ($request->filled('leader_shape') || $request->filled('leader_border_color')) {
            $layoutConfig['header']['chip_shape'] = $request->input('leader_shape', 'circle');
            $layoutConfig['header']['chip_border_color'] = $request->input('leader_border_color', '#FF9933');
            $layoutConfig['header']['enabled'] = $request->boolean('show_top_strip', true);
        }
        if ($request->filled('header_layout')) {
            $layoutConfig['header']['layout'] = $request->input('header_layout');
            $layoutConfig['header']['mode'] = ($request->input('header_layout') === 'party_group_cutout') ? 'party_group_cutout' : 'individual_chips';
        }
        if ($request->filled('footer_style')) {
            $layoutConfig['footer']['style'] = $request->input('footer_style');
        }
        if ($request->filled('name_color')) {
            $layoutConfig['footer']['name_color'] = $request->input('name_color');
        }
        if ($request->filled('footer_bg')) {
            $layoutConfig['footer']['main_bar_bg'] = $request->input('footer_bg');
        }

        $data = [
            'festival_id' => $validated['festival_id'],
            'party_id' => $validated['party_id'] ?? null,
            'title' => $validated['title'],
            'preset_key' => $validated['preset_key'],
            'photo_position' => $validated['photo_position'],
            'layout_config' => !empty($layoutConfig) ? $layoutConfig : null,
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
     * Generate dynamic realistic preview of festival template with sample political overlay.
     */
    public function preview(Request $request)
    {
        $composer = app(PosterComposer::class);

        $bgPath = null;
        if ($request->hasFile('template_image')) {
            $bgPath = $request->file('template_image')->getRealPath();
        } elseif ($request->filled('template_id')) {
            $tpl = FestivalTemplate::find($request->input('template_id'));
            $bgPath = $tpl?->template_image_path;
        }

        if (!$bgPath || (!file_exists($bgPath) && !file_exists(public_path($bgPath)) && !file_exists(storage_path('app/public/' . $bgPath)))) {
            // Default sample background: prioritize Devotthan Ekadashi Sacred Frame as requested by user
            $candidates = [
                public_path('images/templates/devotthan-ekadashi.jpg'),
                public_path('images/templates/bhai-dooj.jpg'),
                public_path('images/templates/tulsi-vivah.jpg'),
                public_path('images/templates/khatu-shyam-janmotsav.jpg'),
                public_path('images/festival-templates-sample.jpg'),
            ];
            foreach ($candidates as $cand) {
                if (file_exists($cand)) {
                    $bgPath = $cand;
                    break;
                }
            }
        }

        $party = null;
        if ($request->filled('party_id')) {
            $party = PoliticalParty::find($request->input('party_id'));
        }
        if (!$party) {
            $party = PoliticalParty::where('short_code', 'BJP')->first() ?? PoliticalParty::first();
        }

        $presetKey = $request->input('preset_key', 'classic_political_right');
        $layoutConfig = [];

        if ($request->filled('leader_shape')) {
            $layoutConfig['header']['chip_shape'] = $request->input('leader_shape');
        }
        if ($request->filled('leader_border_color')) {
            $layoutConfig['header']['chip_border_color'] = $request->input('leader_border_color');
        }
        if ($request->has('show_top_strip')) {
            $layoutConfig['header']['enabled'] = $request->boolean('show_top_strip', true);
        }
        if ($request->filled('header_layout')) {
            $headerLayout = $request->input('header_layout');
            if ($headerLayout === 'none') {
                $layoutConfig['header']['enabled'] = false;
            } else {
                $layoutConfig['header']['enabled'] = true;
                $layoutConfig['header']['layout'] = $headerLayout;
                $layoutConfig['header']['mode'] = ($headerLayout === 'party_group_cutout') ? 'party_group_cutout' : 'individual_chips';
            }
        }
        if ($request->filled('footer_style')) {
            $layoutConfig['footer']['style'] = $request->input('footer_style');
        }
        if ($request->filled('name_color')) {
            $layoutConfig['footer']['name_color'] = $request->input('name_color');
        }
        if ($request->filled('footer_bg')) {
            $layoutConfig['footer']['main_bar_bg'] = $request->input('footer_bg');
        }
        if ($request->filled('photo_position')) {
            $layoutConfig['candidate']['position'] = $request->input('photo_position');
        }

        // Mock realistic sample politician branding
        $sampleBranding = new UserBranding([
            'display_name' => 'डॉ. धर्म सिंह सैनी',
            'designation' => 'पूर्व कैबिनेट मंत्री (उत्तर प्रदेश)',
            'facebook_handle' => 'Dharam.singhsaini',
            'instagram_handle' => 'Drdharamsinghsaini',
            'phone_number' => '94122xxxxx',
        ]);
        $sampleBranding->setRelation('party', $party);

        // Retrieve official party strip for leaders preview
        $strip = $party ? LeaderStrip::where('party_id', $party->id)->where('is_system', true)->with('leaders')->first() : null;
        if (!$strip || $strip->leaders->isEmpty()) {
            $strip = LeaderStrip::where('is_system', true)->with('leaders')->first();
        }

        // Mock leaders if strip still empty
        if (!$strip || $strip->leaders->isEmpty()) {
            $mockLeaders = collect([
                new PoliticalLeader(['name' => 'Narendra Modi', 'image' => 'images/leaders/modiji.png']),
                new PoliticalLeader(['name' => 'Amit Shah', 'image' => 'images/leaders/amitshah.jpg']),
                new PoliticalLeader(['name' => 'Yogi Adityanath', 'image' => 'images/leaders/yogiadityanath.jpg']),
                new PoliticalLeader(['name' => 'Keshav Prasad Maurya', 'image' => 'images/leaders/keshavprshadmorya.jpg']),
                new PoliticalLeader(['name' => 'Atal Bihari Vajpayee', 'image' => 'images/leaders/atalbiharibajpyee.jpg']),
                new PoliticalLeader(['name' => 'J.P. Nadda', 'image' => 'images/leaders/modiji.png']),
            ]);
            $strip = new LeaderStrip(['name' => 'Official Leadership Strip']);
            $strip->setRelation('leaders', $mockLeaders);
        }

        $sampleCutout = file_exists(public_path('images/leaders/modiji.png')) ? 'images/leaders/modiji.png' : null;

        try {
            $canvas = $composer->compose($bgPath, $sampleBranding, $strip, [
                'preset_key' => $presetKey,
                'layout_config' => $layoutConfig,
                'include_strip' => $layoutConfig['header']['enabled'] ?? true,
                'cutout_url' => $sampleCutout,
            ]);

            $jpegData = $canvas->toJpeg(92)->toString();

            return response()->json([
                'success' => true,
                'preview_url' => 'data:image/jpeg;base64,' . base64_encode($jpegData),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Preview generation failed: ' . $e->getMessage(),
            ], 500);
        }
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
