<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PoliticalParty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PoliticalPartyController extends Controller
{
    /**
     * Display a listing of political parties.
     */
    public function index(Request $request)
    {
        $query = PoliticalParty::query()->withCount(['leaders', 'templates', 'strips']);

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('hindi_name', 'like', "%{$search}%")
                  ->orWhere('short_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === '1');
        }

        $parties = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.political_parties.index', compact('parties'));
    }

    /**
     * Show the form for creating a new political party.
     */
    public function create()
    {
        return view('admin.political_parties.create');
    }

    /**
     * Store a newly created political party in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191|unique:political_parties,name',
            'hindi_name' => 'nullable|string|max:191',
            'short_code' => 'required|string|max:20|unique:political_parties,short_code',
            'primary_color' => 'required|string|max:20',
            'secondary_color' => 'nullable|string|max:20',
            'symbol_logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:5120',
            'group_cutout_image' => 'nullable|image|mimes:png,webp|max:10240',
            'top_leaders_strip' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:10240',
            'is_active' => 'nullable|boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'hindi_name' => $validated['hindi_name'] ?? null,
            'short_code' => strtoupper($validated['short_code']),
            'primary_color' => $validated['primary_color'],
            'secondary_color' => $validated['secondary_color'] ?? '#ffffff',
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('symbol_logo')) {
            $file = $request->file('symbol_logo');
            $filename = 'party_symbol_' . Str::slug($validated['short_code']) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $data['symbol_logo'] = $file->storeAs('uploads/parties', $filename, 'public');
        }

        if ($request->hasFile('group_cutout_image')) {
            $file = $request->file('group_cutout_image');
            $filename = 'party_cutout_' . Str::slug($validated['short_code']) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $data['group_cutout_image'] = $file->storeAs('uploads/parties', $filename, 'public');
        }

        if ($request->hasFile('top_leaders_strip')) {
            $file = $request->file('top_leaders_strip');
            $filename = 'party_strip_' . Str::slug($validated['short_code']) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $data['top_leaders_strip'] = $file->storeAs('uploads/parties', $filename, 'public');
        }

        $party = PoliticalParty::create($data);

        return redirect()->route('admin.political-parties.index')
            ->with('success', "Political party '{$party->name}' created successfully.");
    }

    /**
     * Show the form for editing the specified political party.
     */
    public function edit(PoliticalParty $politicalParty)
    {
        return view('admin.political_parties.edit', compact('politicalParty'));
    }

    /**
     * Update the specified political party in storage.
     */
    public function update(Request $request, PoliticalParty $politicalParty)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191|unique:political_parties,name,' . $politicalParty->id,
            'hindi_name' => 'nullable|string|max:191',
            'short_code' => 'required|string|max:20|unique:political_parties,short_code,' . $politicalParty->id,
            'primary_color' => 'required|string|max:20',
            'secondary_color' => 'nullable|string|max:20',
            'symbol_logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:5120',
            'group_cutout_image' => 'nullable|image|mimes:png,webp|max:10240',
            'top_leaders_strip' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:10240',
            'is_active' => 'nullable|boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'hindi_name' => $validated['hindi_name'] ?? null,
            'short_code' => strtoupper($validated['short_code']),
            'primary_color' => $validated['primary_color'],
            'secondary_color' => $validated['secondary_color'] ?? '#ffffff',
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('symbol_logo')) {
            if ($politicalParty->symbol_logo && Storage::disk('public')->exists($politicalParty->symbol_logo)) {
                Storage::disk('public')->delete($politicalParty->symbol_logo);
            }
            $file = $request->file('symbol_logo');
            $filename = 'party_symbol_' . Str::slug($validated['short_code']) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $data['symbol_logo'] = $file->storeAs('uploads/parties', $filename, 'public');
        }

        if ($request->hasFile('group_cutout_image')) {
            if ($politicalParty->group_cutout_image && Storage::disk('public')->exists($politicalParty->group_cutout_image)) {
                Storage::disk('public')->delete($politicalParty->group_cutout_image);
            }
            $file = $request->file('group_cutout_image');
            $filename = 'party_cutout_' . Str::slug($validated['short_code']) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $data['group_cutout_image'] = $file->storeAs('uploads/parties', $filename, 'public');
        }

        if ($request->hasFile('top_leaders_strip')) {
            if ($politicalParty->top_leaders_strip && Storage::disk('public')->exists($politicalParty->top_leaders_strip)) {
                Storage::disk('public')->delete($politicalParty->top_leaders_strip);
            }
            $file = $request->file('top_leaders_strip');
            $filename = 'party_strip_' . Str::slug($validated['short_code']) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $data['top_leaders_strip'] = $file->storeAs('uploads/parties', $filename, 'public');
        }

        $politicalParty->update($data);

        return redirect()->route('admin.political-parties.index')
            ->with('success', "Political party '{$politicalParty->name}' updated successfully.");
    }

    /**
     * Remove the specified political party from storage.
     */
    public function destroy(PoliticalParty $politicalParty)
    {
        $name = $politicalParty->name;
        $politicalParty->delete();

        return redirect()->route('admin.political-parties.index')
            ->with('success', "Party '{$name}' deleted successfully.");
    }
}
