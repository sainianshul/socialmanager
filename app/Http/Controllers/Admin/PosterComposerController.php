<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FestivalTemplate;
use App\Models\LeaderStrip;
use App\Models\UserBranding;
use App\Services\PosterComposer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PosterComposerController extends Controller
{
    protected PosterComposer $composer;

    public function __construct(PosterComposer $composer)
    {
        $this->composer = $composer;
    }

    /**
     * Get list of all available layout presets with rich metadata.
     */
    public function presets(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'presets' => $this->composer->getAvailablePresets(),
        ]);
    }

    /**
     * Compose high-res branded poster dynamically on the server.
     */
    public function compose(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'template_id'           => 'required|integer|exists:festival_templates,id',
            'preset_key'            => 'nullable|string|max:60',
            'include_strip'         => 'nullable|boolean',
            'show_social_handles'   => 'nullable|boolean',
            'strip_id'              => 'nullable|integer|exists:leader_strips,id',
            'branding_id'           => 'nullable|integer|exists:user_brandings,id',
            'cutout_url'            => 'nullable|string',
            'candidate_name'        => 'nullable|string|max:100',
            'candidate_designation' => 'nullable|string|max:150',
            'leader_shape'          => 'nullable|string|max:30',
            'leader_border_color'   => 'nullable|string|max:20',
            'leader_size'           => 'nullable|integer|min:50|max:200',
            'candidate_scale'       => 'nullable|integer|min:50|max:200',
            'custom_config'         => 'nullable|array',
        ]);

        $user = auth()->user();

        // 1. Resolve Template with Party
        $template = FestivalTemplate::with('party')->findOrFail($validated['template_id']);

        // 2. Resolve User Branding Profile
        $branding = null;
        if (!empty($validated['branding_id'])) {
            $branding = UserBranding::where('id', $validated['branding_id'])
                ->when(!$user->isAdmin(), fn($q) => $q->where('user_id', $user->id))
                ->first();
        }
        if (!$branding && $user) {
            $branding = $user->defaultBranding ?? $user->brandings()->first();
        }

        // 3. Resolve Leadership Strip (User Custom First, then Party Official Default)
        $strip = LeaderStrip::resolveFor($user, $template, $branding, !empty($validated['strip_id']) ? (int)$validated['strip_id'] : null);

        // 4. Compose Options
        $options = [
            'preset_key'            => $validated['preset_key'] ?? $template->preset_key,
            'include_strip'         => $request->has('include_strip') ? $request->boolean('include_strip') : true,
            'show_social_handles'   => $request->has('show_social_handles') ? $request->boolean('show_social_handles') : true,
            'cutout_url'            => $validated['cutout_url'] ?? null,
            'candidate_name'        => $validated['candidate_name'] ?? null,
            'candidate_designation' => $validated['candidate_designation'] ?? null,
            'layout_config'         => [
                'footer' => [
                    'show_social_handles' => $request->has('show_social_handles') ? $request->boolean('show_social_handles') : true,
                ],
            ],
        ];

        if ($request->filled('footer_style')) {
            $options['layout_config']['footer']['style'] = $request->input('footer_style');
        }

        if ($request->filled('header_layout')) {
            $hLayout = $request->input('header_layout');
            if ($hLayout === 'none') {
                $options['include_strip'] = false;
                $options['layout_config']['header']['enabled'] = false;
            } else {
                $options['include_strip'] = true;
                $options['layout_config']['header']['enabled'] = true;
                $options['layout_config']['header']['layout'] = $hLayout;
                $options['layout_config']['header']['mode'] = ($hLayout === 'party_group_cutout') ? 'party_group_cutout' : 'individual_chips';
            }
        }

        if ($request->filled('leader_shape')) {
            $options['layout_config']['header']['chip_shape'] = $request->input('leader_shape');
        }

        if ($request->filled('leader_border_color')) {
            $options['layout_config']['header']['chip_border_color'] = $request->input('leader_border_color');
        }

        if ($request->filled('leader_size')) {
            $options['layout_config']['header']['chip_size'] = (int) $request->input('leader_size');
        }

        if ($request->filled('candidate_scale')) {
            $options['candidate_scale'] = (int) $request->input('candidate_scale');
        }

        if ($request->filled('layout_config') && is_array($request->input('layout_config'))) {
            $options['layout_config'] = array_replace_recursive($options['layout_config'], $request->input('layout_config'));
        }

        try {
            $result = $this->composer->composeToFile(
                $template,
                $branding,
                $strip,
                $options
            );

            return response()->json([
                'success'       => true,
                'message'       => 'Poster composed successfully.',
                'image_url'     => $result['url'],
                'relative_path' => $result['relative_path'],
                'filename'      => $result['filename'],
                'preset_used'   => $options['preset_key'] ?: 'classic_political_right',
                'template_id'   => $template->id,
                'title'         => $template->title,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to compose poster: ' . $e->getMessage(),
            ], 500);
        }
    }
}
