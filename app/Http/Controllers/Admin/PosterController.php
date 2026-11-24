<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PoliticalParty;
use App\Models\Poster;
use App\Models\PosterCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PosterController extends Controller
{
    /**
     * Display a listing of readymade posters.
     */
    public function index(Request $request)
    {
        $query = Poster::query()->with(['category', 'party']);

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('default_caption', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($request->filled('party_id')) {
            $partyId = $request->input('party_id');
            if ($partyId === 'universal') {
                $query->whereNull('party_id');
            } else {
                $query->where('party_id', $partyId);
            }
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === '1');
        }

        if ($request->filled('top')) {
            $query->where('show_on_top', true);
        }

        $posters = $query->orderByDesc('show_on_top')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $categories = PosterCategory::active()->get();
        $parties = PoliticalParty::orderBy('name')->get();

        return view('admin.posters.index', compact('posters', 'categories', 'parties'));
    }

    /**
     * Show the form for creating a new readymade poster.
     */
    public function create()
    {
        $categories = PosterCategory::active()->get();
        $parties = PoliticalParty::orderBy('name')->get();

        return view('admin.posters.create', compact('categories', 'parties'));
    }

    /**
     * Store a newly created readymade poster in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:poster_categories,id',
            'party_id' => 'nullable|exists:political_parties,id',
            'name' => 'required|string|max:191',
            'slug' => 'nullable|string|max:191',
            'poster_image' => 'required|image|mimes:png,jpg,jpeg,webp|max:12288',
            'default_caption' => 'nullable|string',
            'show_on_top' => 'nullable|boolean',
            'auto_show_date' => 'nullable|date',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        $imagePath = '';
        if ($request->hasFile('poster_image')) {
            $file = $request->file('poster_image');
            $filename = 'poster_' . Str::slug($validated['name']) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $imagePath = $file->storeAs('uploads/posters', $filename, 'public');
        }

        $poster = Poster::create([
            'category_id' => $validated['category_id'],
            'party_id' => !empty($validated['party_id']) ? $validated['party_id'] : null,
            'name' => $validated['name'],
            'slug' => $slug,
            'image_path' => $imagePath,
            'thumbnail_path' => $imagePath,
            'default_caption' => $validated['default_caption'] ?? null,
            'show_on_top' => $request->boolean('show_on_top', false),
            'auto_show_date' => $validated['auto_show_date'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.posters.index', ['category_id' => $poster->category_id])
            ->with('success', "Poster '{$poster->name}' added successfully.");
    }

    /**
     * Show the form for editing the specified readymade poster.
     */
    public function edit(Poster $poster)
    {
        $categories = PosterCategory::active()->get();
        $parties = PoliticalParty::orderBy('name')->get();

        return view('admin.posters.edit', compact('poster', 'categories', 'parties'));
    }

    /**
     * Update the specified readymade poster in storage.
     */
    public function update(Request $request, Poster $poster)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:poster_categories,id',
            'party_id' => 'nullable|exists:political_parties,id',
            'name' => 'required|string|max:191',
            'slug' => 'nullable|string|max:191',
            'poster_image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:12288',
            'default_caption' => 'nullable|string',
            'show_on_top' => 'nullable|boolean',
            'auto_show_date' => 'nullable|date',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $data = [
            'category_id' => $validated['category_id'],
            'party_id' => !empty($validated['party_id']) ? $validated['party_id'] : null,
            'name' => $validated['name'],
            'slug' => !empty($validated['slug']) ? Str::slug($validated['slug']) : $poster->slug,
            'default_caption' => $validated['default_caption'] ?? null,
            'show_on_top' => $request->boolean('show_on_top', false),
            'auto_show_date' => $validated['auto_show_date'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('poster_image')) {
            // Delete old file if exists
            if ($poster->image_path && Storage::disk('public')->exists($poster->image_path)) {
                Storage::disk('public')->delete($poster->image_path);
            }

            $file = $request->file('poster_image');
            $filename = 'poster_' . Str::slug($validated['name']) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $data['image_path'] = $file->storeAs('uploads/posters', $filename, 'public');
            $data['thumbnail_path'] = $data['image_path'];
        }

        $poster->update($data);

        return redirect()->route('admin.posters.index', ['category_id' => $poster->category_id])
            ->with('success', "Poster '{$poster->name}' updated successfully.");
    }

    /**
     * Remove the specified readymade poster from storage.
     */
    public function destroy(Poster $poster)
    {
        $name = $poster->name;

        if ($poster->image_path && Storage::disk('public')->exists($poster->image_path)) {
            Storage::disk('public')->delete($poster->image_path);
        }

        $poster->delete();

        return redirect()->route('admin.posters.index')
            ->with('success', "Poster '{$name}' deleted successfully.");
    }

    /**
     * API endpoint to list active categories and posters for user composer modal/drawer.
     */
    public function apiList(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        $branding = $user?->defaultBranding ?? $user?->brandings()->first();
        $userPartyId = $branding?->party_id;

        $categories = PosterCategory::active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'icon', 'show_as_collection']);

        $query = Poster::query()
            ->active()
            ->with(['category:id,name,slug', 'party:id,name,short_code']);

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('default_caption', 'like', "%{$search}%");
            });
        }

        if ($request->filled('party_id')) {
            $pId = $request->input('party_id');
            if ($pId === 'universal') {
                $query->whereNull('party_id');
            } elseif ($pId !== 'all') {
                $query->where('party_id', $pId);
            }
        } else {
            $query->forParty($userPartyId);
        }

        $posters = $query->orderByDesc('show_on_top')
            ->orderBy('sort_order')
            ->orderByDesc('usage_count')
            ->orderByDesc('id')
            ->get();

        $posters->transform(function ($poster) {
            return [
                'id'              => $poster->id,
                'name'            => $poster->name,
                'category_id'     => $poster->category_id,
                'category_name'   => $poster->category?->name,
                'party_id'        => $poster->party_id,
                'party_name'      => $poster->party?->name,
                'party_code'      => $poster->party?->short_code,
                'image_path'      => $poster->image_path,
                'image_url'       => asset('storage/' . $poster->image_path),
                'thumbnail_url'   => $poster->thumbnail_path ? asset('storage/' . $poster->thumbnail_path) : asset('storage/' . $poster->image_path),
                'default_caption' => $poster->default_caption,
                'usage_count'     => $poster->usage_count,
                'show_on_top'     => (bool) $poster->show_on_top,
            ];
        });

        return response()->json([
            'success'    => true,
            'categories' => $categories,
            'posters'    => $posters,
        ]);
    }
}
