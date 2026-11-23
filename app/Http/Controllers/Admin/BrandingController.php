<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BrandingImage;
use App\Models\LeaderStrip;
use App\Models\PoliticalLeader;
use App\Models\PoliticalParty;
use App\Models\User;
use App\Models\UserBranding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandingController extends Controller
{
    /**
     * Display the user's branding customization studio
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $branding = $user->defaultBranding ?? $user->brandings()->first();

        if (!$branding) {
            $branding = new UserBranding([
                'user_id' => $user->id,
                'display_name' => $user->name,
                'client_type' => $user->client_type ?? User::CLIENT_TYPE_POLITICIAN,
                'phone_number' => $user->phone,
            ]);
        }

        $parties = PoliticalParty::where('is_active', true)->orderBy('name')->get();

        $defaultImage = $branding->defaultImage 
            ?? $user->brandingImages()->where('is_default', true)->first();

        $currentPhotoUrl = $defaultImage?->cutout_url
            ?? ($user->profile_photo ? asset('storage/' . $user->profile_photo) : null)
            ?? 'https://ui-avatars.com/api/?name=' . urlencode($branding->display_name ?: $user->name) . '&background=1db8ab&color=fff&bold=true&size=180';

        $selectedPartyId = old('party_id', $branding->party_id);

        $userStrips = $user->leaderStrips()
            ->with(['leaders' => function ($q) {
                $q->orderBy('leader_strip_items.order', 'asc');
            }, 'party'])
            ->orderByDesc('is_default')
            ->latest()
            ->get();
        $systemStrip = null;
        if ($selectedPartyId) {
            $systemStrip = LeaderStrip::where('party_id', $selectedPartyId)
                ->where('is_system', true)
                ->where('is_default', true)
                ->with(['leaders' => function ($q) {
                    $q->orderBy('leader_strip_items.order', 'asc');
                }])
                ->first();
        }

        $activeStrip = $user->defaultLeaderStrip ?? $systemStrip;
        $partyLeaders = $selectedPartyId 
            ? PoliticalLeader::where('party_id', $selectedPartyId)->where('is_active', true)->get() 
            : collect();

        $brandingImages = $user->brandingImages()->orderByDesc('is_default')->latest()->get();

        return view('admin.branding.index', compact(
            'branding',
            'parties',
            'defaultImage',
            'currentPhotoUrl',
            'user',
            'userStrips',
            'systemStrip',
            'activeStrip',
            'partyLeaders',
            'brandingImages'
        ));
    }

    /**
     * Update the user's branding profile and photo
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'display_name'     => 'required|string|max:150',
            'hindi_name'       => 'nullable|string|max:150',
            'designation'      => 'nullable|string|max:150',
            'client_type'      => 'nullable|in:politician,business,personal',
            'party_id'         => 'nullable|exists:political_parties,id',
            'phone_number'     => 'nullable|string|max:30',
            'facebook_handle'  => 'nullable|string|max:100',
            'instagram_handle' => 'nullable|string|max:100',
            'twitter_handle'   => 'nullable|string|max:100',
            'branding_image'   => 'nullable|image|mimes:jpeg,jpg,png,webp|max:10240',
        ]);

        $user = auth()->user();

        $branding = $user->defaultBranding ?? $user->brandings()->first();

        if (!$branding) {
            $branding = new UserBranding();
            $branding->user_id = $user->id;
            $branding->is_default = true;
        }

        $clientType = $validated['client_type'] ?? $branding->client_type ?? $user->client_type ?? User::CLIENT_TYPE_POLITICIAN;

        $branding->display_name = $validated['display_name'];
        $branding->hindi_name = $validated['hindi_name'] ?? null;
        $branding->designation = $validated['designation'] ?? null;
        $branding->client_type = $clientType;
        $branding->party_id = $validated['party_id'] ?? null;
        $branding->phone_number = $validated['phone_number'] ?? null;
        $branding->facebook_handle = $validated['facebook_handle'] ?? null;
        $branding->instagram_handle = $validated['instagram_handle'] ?? null;
        $branding->twitter_handle = $validated['twitter_handle'] ?? null;
        $branding->save();

        // Keep User's client_type synced
        if ($user->client_type !== $clientType) {
            $user->update(['client_type' => $clientType]);
        }

        // Process image upload
        if ($request->hasFile('branding_image')) {
            $file = $request->file('branding_image');
            $filename = 'branding_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/branding', $filename, 'public');

            // Unset previous default images
            $user->brandingImages()->update(['is_default' => false]);

            BrandingImage::create([
                'user_id' => $user->id,
                'user_branding_id' => $branding->id,
                'title' => $validated['display_name'],
                'original_image_path' => $path,
                'cutout_image_path' => $path,
                'is_default' => true,
            ]);
        }

        return redirect()->route('admin.branding.index')->with('success', 'Branding profile updated successfully.');
    }

    /**
     * Upload an additional branding photo to user's gallery
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'branding_image' => 'required|image|mimes:jpeg,jpg,png,webp|max:10240',
            'is_default'     => 'nullable|boolean',
        ]);

        $user = auth()->user();
        $branding = $user->defaultBranding ?? $user->brandings()->first();

        $file = $request->file('branding_image');
        $filename = 'branding_' . $user->id . '_' . time() . '_' . Str::random(4) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('uploads/branding', $filename, 'public');

        $isDefault = $request->boolean('is_default', false);
        if ($user->brandingImages()->count() === 0 || $isDefault) {
            $user->brandingImages()->update(['is_default' => false]);
            $isDefault = true;
        }

        $image = BrandingImage::create([
            'user_id'             => $user->id,
            'user_branding_id'    => $branding?->id,
            'title'               => $branding?->display_name ?: $user->name,
            'original_image_path' => $path,
            'cutout_image_path'   => $path,
            'is_default'          => $isDefault,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Branding photo uploaded successfully.',
                'image'   => [
                    'id'         => $image->id,
                    'url'        => $image->cutout_url,
                    'is_default' => $image->is_default,
                ],
            ]);
        }

        return redirect()->route('admin.branding.index', ['#pane-cutout'])->with('success', 'Branding photo uploaded successfully.');
    }

    /**
     * Set a specific photo as the active default for posters
     */
    public function setDefaultImage(BrandingImage $image)
    {
        $user = auth()->user();
        if ($image->user_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        $user->brandingImages()->update(['is_default' => false]);
        $image->update(['is_default' => true]);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success'   => true,
                'message'   => 'Default branding photo updated successfully.',
                'image_id'  => $image->id,
                'image_url' => $image->cutout_url,
            ]);
        }

        return redirect()->route('admin.branding.index', ['#pane-cutout'])->with('success', 'Default photo updated.');
    }

    /**
     * Delete a branding photo from user's gallery
     */
    public function destroyImage(BrandingImage $image)
    {
        $user = auth()->user();
        if ($image->user_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        $wasDefault = $image->is_default;

        if ($image->original_image_path && Storage::disk('public')->exists($image->original_image_path)) {
            Storage::disk('public')->delete($image->original_image_path);
        }
        if ($image->cutout_image_path && $image->cutout_image_path !== $image->original_image_path && Storage::disk('public')->exists($image->cutout_image_path)) {
            Storage::disk('public')->delete($image->cutout_image_path);
        }

        $image->delete();

        $nextImage = null;
        if ($wasDefault) {
            $nextImage = $user->brandingImages()->latest()->first();
            if ($nextImage) {
                $nextImage->update(['is_default' => true]);
            }
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success'        => true,
                'message'        => 'Photo removed successfully.',
                'new_default_id' => $nextImage?->id ?? null,
                'new_default_url'=> $nextImage?->cutout_url ?? null,
            ]);
        }

        return redirect()->route('admin.branding.index', ['#pane-cutout'])->with('success', 'Photo removed.');
    }
}
