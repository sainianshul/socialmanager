<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PosterCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PosterCategoryController extends Controller
{
    /**
     * Display a listing of poster categories.
     */
    public function index(Request $request)
    {
        $query = PosterCategory::query()->withCount('posters');

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === '1');
        }

        if ($request->filled('collection')) {
            $query->where('show_as_collection', $request->input('collection') === '1');
        }

        $categories = $query->orderBy('sort_order')->orderBy('id')->paginate(20)->withQueryString();

        return view('admin.poster_categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new poster category.
     */
    public function create()
    {
        return view('admin.poster_categories.create');
    }

    /**
     * Store a newly created poster category in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'slug' => 'nullable|string|max:191|unique:poster_categories,slug',
            'icon' => 'nullable|string|max:100',
            'show_as_collection' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        // Ensure unique slug fallback
        if (PosterCategory::where('slug', $slug)->exists()) {
            $slug .= '-' . time();
        }

        $category = PosterCategory::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'icon' => $validated['icon'] ?? 'ti ti-template',
            'show_as_collection' => $request->boolean('show_as_collection', false),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.poster-categories.index')
            ->with('success', "Category '{$category->name}' added successfully.");
    }

    /**
     * Show the form for editing the specified poster category.
     */
    public function edit(PosterCategory $posterCategory)
    {
        return view('admin.poster_categories.edit', compact('posterCategory'));
    }

    /**
     * Update the specified poster category in storage.
     */
    public function update(Request $request, PosterCategory $posterCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'slug' => 'nullable|string|max:191|unique:poster_categories,slug,' . $posterCategory->id,
            'icon' => 'nullable|string|max:100',
            'show_as_collection' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : $posterCategory->slug;

        $posterCategory->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'icon' => $validated['icon'] ?? $posterCategory->icon,
            'show_as_collection' => $request->boolean('show_as_collection', false),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.poster-categories.index')
            ->with('success', "Category '{$posterCategory->name}' updated successfully.");
    }

    /**
     * Remove the specified poster category from storage.
     */
    public function destroy(PosterCategory $posterCategory)
    {
        $name = $posterCategory->name;
        $posterCategory->delete();

        return redirect()->route('admin.poster-categories.index')
            ->with('success', "Category '{$name}' deleted successfully.");
    }
}
