<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaderStrip;
use App\Models\LeaderStripItem;
use App\Models\PoliticalLeader;
use App\Models\PoliticalParty;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LeaderStripController extends Controller
{
    /**
     * List all custom strips for authenticated user plus system strips for their party
     */
    public function index(Request $request): mixed
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin() || $user->isManager();

        if ($isAdmin) {
            $partyId = $request->query('party_id');
            $userStrips = collect();
            $systemStrips = LeaderStrip::where('is_system', true)
                ->when($partyId, fn($q) => $q->where('party_id', $partyId))
                ->with(['leaders', 'party'])
                ->orderByDesc('is_default')
                ->latest()
                ->get();
            $activeStrip = $systemStrips->firstWhere('is_default', true) ?? $systemStrips->first();
            $existingPartyIdsWithStrips = [];
        } else {
            $partyId = $request->query('party_id') ?? $user->defaultBranding?->party_id;

            $userStrips = LeaderStrip::where('user_id', $user->id)
                ->with(['leaders', 'party'])
                ->orderByDesc('is_default')
                ->latest()
                ->get();

            $systemStrips = LeaderStrip::where('is_system', true)
                ->when($partyId, fn($q) => $q->where('party_id', $partyId))
                ->with(['leaders', 'party'])
                ->orderByDesc('is_default')
                ->get();

            $activeStrip = $userStrips->firstWhere('is_default', true) 
                ?? $systemStrips->firstWhere('is_default', true) 
                ?? $systemStrips->first();

            $existingPartyIdsWithStrips = [];
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'user_strips' => $userStrips,
                'system_strips' => $systemStrips,
                'active_strip' => $activeStrip,
                'existing_party_ids' => $existingPartyIdsWithStrips,
            ]);
        }

        $parties = PoliticalParty::where('is_active', true)->orderBy('name')->get();
        $partyLeaders = $partyId ? PoliticalLeader::where('party_id', $partyId)->where('is_active', true)->orderBy('name')->get() : collect();
        $branding = $user->defaultBranding;
        $currentPartyId = $partyId;

        return view('admin.leader_strips.index', compact(
            'userStrips',
            'systemStrips',
            'activeStrip',
            'parties',
            'partyLeaders',
            'branding',
            'currentPartyId',
            'isAdmin',
            'existingPartyIdsWithStrips'
        ));
    }

    /**
     * Get available leaders for a party with optional search and state filter
     */
    public function leaders(Request $request): JsonResponse
    {
        $user = auth()->user();
        $partyId = $request->query('party_id') ?? $user->defaultBranding?->party_id;

        $query = PoliticalLeader::query()->where('is_active', true);

        if ($partyId) {
            $query->where('party_id', $partyId);
        }

        // Global leaders (user_id IS NULL) + this user's local custom leaders (user_id = $user->id)
        $query->where(function ($q) use ($user) {
            $q->whereNull('user_id');
            if ($user) {
                $q->orWhere('user_id', $user->id);
            }
        });

        if ($search = trim((string)$request->query('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('designation', 'like', "%{$search}%");
            });
        }

        if ($state = trim((string)$request->query('state', ''))) {
            if ($state === 'Local') {
                $query->whereNotNull('user_id');
            } else {
                $query->where('state', $state);
            }
        }

        // Show user's local leaders first, then by name
        $leaders = $query->orderByRaw('CASE WHEN user_id IS NOT NULL THEN 0 ELSE 1 END')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'count' => $leaders->count(),
            'leaders' => $leaders->map(fn($l) => [
                'id' => $l->id,
                'party_id' => $l->party_id,
                'name' => $l->name,
                'designation' => $l->designation,
                'state' => $l->state,
                'image_url' => $l->image_url,
                'is_custom' => !is_null($l->user_id),
            ]),
        ]);
    }

    /**
     * Quickly create a custom/local leader for the authenticated user
     */
    public function storeCustomLeader(Request $request): JsonResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'party_id' => 'required|exists:political_parties,id',
            'designation' => 'nullable|string|max:100',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $file = $request->file('image');
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('uploads/leaders/custom', $filename, 'public');

        $leader = PoliticalLeader::create([
            'user_id' => $user->id,
            'party_id' => $validated['party_id'],
            'name' => trim($validated['name']),
            'designation' => !empty($validated['designation']) ? trim($validated['designation']) : null,
            'image' => $path,
            'state' => 'Local',
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Local leader added successfully.',
            'leader' => [
                'id' => $leader->id,
                'party_id' => $leader->party_id,
                'name' => $leader->name,
                'designation' => $leader->designation,
                'state' => $leader->state,
                'image_url' => $leader->image_url,
                'is_custom' => true,
            ],
        ], 201);
    }

    /**
     * Delete a custom leader owned by the authenticated user
     */
    public function destroyCustomLeader(int $id): JsonResponse
    {
        $user = auth()->user();
        $leader = PoliticalLeader::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Delete photo file if present
        if ($leader->image && Storage::disk('public')->exists($leader->image)) {
            Storage::disk('public')->delete($leader->image);
        }

        $leader->delete();

        return response()->json([
            'success' => true,
            'message' => 'Local leader removed successfully.',
        ]);
    }

    /**
     * Create a new custom leader strip with ordered leaders
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin() || $user->isManager();

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'party_id' => 'required|exists:political_parties,id',
            'is_default' => 'nullable|boolean',
            'items' => 'nullable|array|min:1|max:7',
            'items.*.leader_id' => 'required_with:items|exists:political_leaders,id',
            'items.*.order' => 'required_with:items|integer|min:1',
            'leader_ids' => 'nullable|array|min:1|max:7',
            'leader_ids.*' => 'exists:political_leaders,id',
        ]);

        $normalizedItems = $this->normalizeItems($validated);

        if (empty($normalizedItems)) {
            throw ValidationException::withMessages([
                'items' => ['At least one leader must be selected for the strip.'],
            ]);
        }

        $isDefault = !empty($validated['is_default']);

        $strip = DB::transaction(function () use ($user, $validated, $normalizedItems, $isDefault, $isAdmin) {
            if ($isAdmin) {
                // If set as default or if this is the first official strip for this party, make it default
                $existingCount = LeaderStrip::where('party_id', $validated['party_id'])->where('is_system', true)->count();
                $shouldBeDefault = $isDefault || ($existingCount === 0);

                if ($shouldBeDefault) {
                    LeaderStrip::where('party_id', $validated['party_id'])
                        ->where('is_system', true)
                        ->update(['is_default' => false]);
                }

                $newStrip = LeaderStrip::create([
                    'user_id' => null,
                    'party_id' => $validated['party_id'],
                    'name' => $validated['name'],
                    'is_default' => $shouldBeDefault,
                    'is_system' => true,
                ]);
            } else {
                if ($isDefault) {
                    LeaderStrip::where('user_id', $user->id)->update(['is_default' => false]);
                }

                // If user has no existing strips, automatically make this one default
                $existingCount = LeaderStrip::where('user_id', $user->id)->count();
                $shouldBeDefault = $isDefault || ($existingCount === 0);

                $newStrip = LeaderStrip::create([
                    'user_id' => $user->id,
                    'party_id' => $validated['party_id'],
                    'name' => $validated['name'],
                    'is_default' => $shouldBeDefault,
                    'is_system' => false,
                ]);
            }

            foreach ($normalizedItems as $item) {
                LeaderStripItem::create([
                    'leader_strip_id' => $newStrip->id,
                    'political_leader_id' => $item['leader_id'],
                    'order' => $item['order'],
                ]);
            }

            return $newStrip;
        });

        return response()->json([
            'success' => true,
            'message' => $isAdmin ? 'Official party leadership strip created successfully.' : 'Header strip created successfully.',
            'strip' => $strip->load(['leaders', 'party']),
        ], 201);
    }

    /**
     * Retrieve single strip details with ordered leaders
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin() || $user->isManager();

        $query = LeaderStrip::query()->with(['leaders', 'party']);
        if (!$isAdmin) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('is_system', true);
            });
        }

        $strip = $query->findOrFail($id);

        return response()->json([
            'success' => true,
            'strip' => $strip,
        ]);
    }

    /**
     * Update an existing custom leader strip
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin() || $user->isManager();

        if ($isAdmin) {
            $strip = LeaderStrip::findOrFail($id);
        } else {
            // Ensure user can only edit their own strips (system strips cannot be modified by regular users)
            $strip = LeaderStrip::where('user_id', $user->id)->findOrFail($id);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'party_id' => 'nullable|exists:political_parties,id',
            'is_default' => 'nullable|boolean',
            'items' => 'nullable|array|min:1|max:7',
            'items.*.leader_id' => 'required_with:items|exists:political_leaders,id',
            'items.*.order' => 'required_with:items|integer|min:1',
            'leader_ids' => 'nullable|array|min:1|max:7',
            'leader_ids.*' => 'exists:political_leaders,id',
        ]);

        $normalizedItems = $this->normalizeItems($validated);

        if (empty($normalizedItems)) {
            throw ValidationException::withMessages([
                'items' => ['At least one leader must be selected for the strip.'],
            ]);
        }

        $isDefault = !empty($validated['is_default']);

        DB::transaction(function () use ($user, $strip, $validated, $normalizedItems, $isDefault, $isAdmin) {
            $targetPartyId = (!empty($validated['party_id'])) ? $validated['party_id'] : $strip->party_id;
            $updateData = ['name' => $validated['name']];

            if ($isAdmin && !empty($validated['party_id'])) {
                $updateData['party_id'] = $validated['party_id'];
            }

            if ($isAdmin) {
                if ($isDefault) {
                    LeaderStrip::where('party_id', $targetPartyId)
                        ->where('is_system', true)
                        ->where('id', '!=', $strip->id)
                        ->update(['is_default' => false]);
                    $updateData['is_default'] = true;
                }
            } else {
                if ($isDefault) {
                    LeaderStrip::where('user_id', $user->id)
                        ->where('id', '!=', $strip->id)
                        ->update(['is_default' => false]);
                }
                $updateData['is_default'] = $isDefault ? true : $strip->is_default;
            }

            $strip->update($updateData);

            // Recreate items with updated sequence
            $strip->items()->delete();

            foreach ($normalizedItems as $item) {
                LeaderStripItem::create([
                    'leader_strip_id' => $strip->id,
                    'political_leader_id' => $item['leader_id'],
                    'order' => $item['order'],
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Header strip updated successfully.',
            'strip' => $strip->fresh(['leaders', 'party']),
        ]);
    }

    /**
     * Set a strip as active default strip (supports user and system strips)
     */
    public function makeDefault(Request $request, int $id): JsonResponse
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin() || $user->isManager();

        $strip = LeaderStrip::where(function ($q) use ($user, $isAdmin) {
                if ($isAdmin) {
                    $q->where('is_system', true);
                } else {
                    $q->where('user_id', $user->id)
                      ->orWhere('is_system', true);
                }
            })
            ->with(['items', 'leaders', 'party'])
            ->findOrFail($id);

        // If admin is setting an official strip as default for that party
        if ($isAdmin && $strip->is_system) {
            LeaderStrip::where('party_id', $strip->party_id)
                ->where('is_system', true)
                ->where('id', '!=', $strip->id)
                ->update(['is_default' => false]);
            $strip->update(['is_default' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Official leadership strip set as default for ' . ($strip->party?->name ?? 'Party') . '.',
                'strip' => $strip->fresh(['leaders', 'party']),
            ]);
        }

        // If it's already a user strip, simply toggle default flag
        if ($strip->user_id === $user->id) {
            LeaderStrip::where('user_id', $user->id)->update(['is_default' => false]);
            $strip->update(['is_default' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Header strip set as default.',
                'strip' => $strip->fresh(['leaders', 'party']),
            ]);
        }

        // If user selects a system strip, clone it as their custom default strip
        $clonedStrip = DB::transaction(function () use ($user, $strip) {
            LeaderStrip::where('user_id', $user->id)->update(['is_default' => false]);

            $newStrip = LeaderStrip::create([
                'user_id' => $user->id,
                'party_id' => $strip->party_id,
                'name' => $strip->name,
                'is_default' => true,
                'is_system' => false,
            ]);

            foreach ($strip->items as $item) {
                LeaderStripItem::create([
                    'leader_strip_id' => $newStrip->id,
                    'political_leader_id' => $item->political_leader_id,
                    'order' => $item->order,
                ]);
            }

            return $newStrip;
        });

        return response()->json([
            'success' => true,
            'message' => 'System strip activated as your default strip.',
            'strip' => $clonedStrip->load(['leaders', 'party']),
        ]);
    }

    /**
     * Delete a custom leader strip
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin() || $user->isManager();

        if ($isAdmin) {
            $strip = LeaderStrip::findOrFail($id);
        } else {
            // Ensure regular user can only delete their own strip (system strips cannot be deleted)
            $strip = LeaderStrip::where('user_id', $user->id)->findOrFail($id);
        }

        $wasDefault = $strip->is_default;

        DB::transaction(function () use ($user, $strip, $wasDefault, $isAdmin) {
            $partyId = $strip->party_id;
            $strip->items()->delete();
            $strip->delete();

            // If deleted strip was default, fallback to another strip
            if ($isAdmin && $wasDefault) {
                $fallback = LeaderStrip::where('party_id', $partyId)
                    ->where('is_system', true)
                    ->latest()
                    ->first();
                if ($fallback) {
                    $fallback->update(['is_default' => true]);
                }
            } elseif (!$isAdmin && $wasDefault) {
                $fallback = LeaderStrip::where('user_id', $user->id)->latest()->first();
                if ($fallback) {
                    $fallback->update(['is_default' => true]);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Header strip deleted successfully.',
        ]);
    }

    /**
     * Helper to normalize either [{leader_id, order}] or [leader_id_1, leader_id_2...] format
     */
    private function normalizeItems(array $validated): array
    {
        if (!empty($validated['items'])) {
            return collect($validated['items'])
                ->sortBy('order')
                ->values()
                ->all();
        }

        if (!empty($validated['leader_ids'])) {
            $items = [];
            foreach ($validated['leader_ids'] as $index => $leaderId) {
                $items[] = [
                    'leader_id' => (int)$leaderId,
                    'order' => $index + 1,
                ];
            }
            return $items;
        }

        return [];
    }
}