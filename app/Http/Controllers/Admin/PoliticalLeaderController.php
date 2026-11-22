<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PoliticalLeader;
use App\Models\PoliticalParty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PoliticalLeaderController extends Controller
{
    /**
     * Display a listing of political leaders.
     */
    public function index(Request $request)
    {
        $query = PoliticalLeader::query()->with('party');

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('designation', 'like', "%{$search}%");
            });
        }

        if ($partyId = $request->input('party_id')) {
            $query->where('party_id', $partyId);
        }

        if ($state = trim((string) $request->input('state'))) {
            $query->where('state', $state);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === '1');
        }

        $leaders = $query->orderBy('name')->paginate(20)->withQueryString();
        $parties = PoliticalParty::orderBy('name')->get();
        $states = PoliticalLeader::whereNotNull('state')->where('state', '!=', '')->distinct()->pluck('state')->sort()->values();

        return view('admin.political_leaders.index', compact('leaders', 'parties', 'states'));
    }

    /**
     * Show the form for creating a new political leader.
     */
    public function create()
    {
        $parties = PoliticalParty::orderBy('name')->get();
        return view('admin.political_leaders.create', compact('parties'));
    }

    /**
     * Store a newly created political leader in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'party_id' => 'required|exists:political_parties,id',
            'name' => 'required|string|max:191',
            'designation' => 'nullable|string|max:191',
            'state' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:8192',
            'is_active' => 'nullable|boolean',
        ]);

        $data = [
            'party_id' => $validated['party_id'],
            'name' => $validated['name'],
            'designation' => $validated['designation'] ?? null,
            'state' => $validated['state'] ?? 'National',
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'leader_' . Str::slug($validated['name']) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $data['image'] = $file->storeAs('uploads/leaders', $filename, 'public');
        }

        $leader = PoliticalLeader::create($data);

        return redirect()->route('admin.political-leaders.index', ['party_id' => $leader->party_id])
            ->with('success', "Leader '{$leader->name}' added successfully.");
    }

    /**
     * Show the form for editing the specified political leader.
     */
    public function edit(PoliticalLeader $politicalLeader)
    {
        $parties = PoliticalParty::orderBy('name')->get();
        return view('admin.political_leaders.edit', compact('politicalLeader', 'parties'));
    }

    /**
     * Update the specified political leader in storage.
     */
    public function update(Request $request, PoliticalLeader $politicalLeader)
    {
        $validated = $request->validate([
            'party_id' => 'required|exists:political_parties,id',
            'name' => 'required|string|max:191',
            'designation' => 'nullable|string|max:191',
            'state' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:8192',
            'is_active' => 'nullable|boolean',
        ]);

        $data = [
            'party_id' => $validated['party_id'],
            'name' => $validated['name'],
            'designation' => $validated['designation'] ?? null,
            'state' => $validated['state'] ?? 'National',
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('image')) {
            if ($politicalLeader->image && Storage::disk('public')->exists($politicalLeader->image)) {
                Storage::disk('public')->delete($politicalLeader->image);
            }
            $file = $request->file('image');
            $filename = 'leader_' . Str::slug($validated['name']) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $data['image'] = $file->storeAs('uploads/leaders', $filename, 'public');
        }

        $politicalLeader->update($data);

        return redirect()->route('admin.political-leaders.index', ['party_id' => $politicalLeader->party_id])
            ->with('success', "Leader '{$politicalLeader->name}' updated successfully.");
    }

    /**
     * Remove the specified political leader from storage.
     */
    public function destroy(PoliticalLeader $politicalLeader)
    {
        $name = $politicalLeader->name;
        $partyId = $politicalLeader->party_id;

        if ($politicalLeader->image && Storage::disk('public')->exists($politicalLeader->image)) {
            Storage::disk('public')->delete($politicalLeader->image);
        }

        $politicalLeader->delete();

        return redirect()->route('admin.political-leaders.index', ['party_id' => $partyId])
            ->with('success', "Leader '{$name}' deleted successfully.");
    }
}
