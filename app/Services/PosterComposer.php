<?php

namespace App\Services;

use App\Models\FestivalTemplate;
use App\Models\LeaderStrip;
use App\Models\PoliticalParty;
use App\Models\UserBranding;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;

class PosterComposer
{
    protected ImageManager $manager;

    public function __construct(?ImageManager $manager = null)
    {
        $this->manager = $manager ?? new ImageManager(new Driver());
    }

    /**
     * Main composition method returning Intervention ImageInterface.
     */
    public function compose(
        FestivalTemplate|string $template,
        ?UserBranding $branding = null,
        ?LeaderStrip $strip = null,
        array $options = []
    ): ImageInterface {
        $presetKey = $options['preset_key'] ?? null;
        $customConfig = [];

        if ($template instanceof FestivalTemplate) {
            $presetKey = $presetKey ?: ($template->preset_key ?: 'classic_political_right');
            $customConfig = $template->layout_config ?: [];
            $bgPath = $template->template_image_path;
            $party = $template->party ?: ($branding?->party);
        } else {
            $presetKey = $presetKey ?: 'classic_political_right';
            $bgPath = $template;
            $party = $branding?->party;
        }

        $runtimeConfig = $options['layout_config'] ?? [];
        $config = array_replace_recursive($this->getDefaultPresetConfig($presetKey), $customConfig, $runtimeConfig);
        $canvasWidth = $config['canvas']['width'] ?? 1080;
        $canvasHeight = $config['canvas']['height'] ?? 1080;

        // 1. Base Canvas & Background
        $canvas = $this->createBaseCanvas($bgPath, $canvasWidth, $canvasHeight);

        // 2. Leadership Header Section (Top Senior Leaders)
        $includeStrip = $options['include_strip'] ?? true;
        if ($includeStrip && ($config['header']['enabled'] ?? true)) {
            $this->renderHeaderSection($canvas, $config['header'] ?? [], $party, $strip);
        }

        // 3. Unified Branding Kit Layer (Candidate Photo + Footer Frame)
        $footerStyle = $config['footer']['style'] ?? 'standard_political';

        if ($footerStyle === 'dual_ribbon_badge' || $footerStyle === 'dual_ribbon') {
            // Layer 1 (Bhai Dooj Style): Candidate Photo is rendered first, then the Angled Ribbon Badge sits OVER candidate lower chest!
            $this->renderCandidatePhoto(
                $canvas,
                $config['candidate'] ?? [],
                $branding,
                $options,
                $config['footer'] ?? []
            );

            if ($branding) {
                $this->renderDualRibbonBadgeFooter(
                    $canvas,
                    $config['footer'] ?? [],
                    $branding,
                    $party,
                    $config['candidate'] ?? []
                );
            }
        } elseif ($footerStyle === 'solid_white_banner') {
            // Layer 2 (Milkha Singh Style): Solid white card + ethnic pattern, then candidate photo
            if ($branding) {
                $this->renderSolidWhiteBannerFooter(
                    $canvas,
                    $config['footer'] ?? [],
                    $branding,
                    $party,
                    $config['candidate'] ?? []
                );
            }

            $this->renderCandidatePhoto(
                $canvas,
                $config['candidate'] ?? [],
                $branding,
                $options,
                $config['footer'] ?? []
            );
        } elseif ($footerStyle === 'floating_tricolor_badge') {
            // Layer 3 (Diwali Reference Style): Candidate photo rendered first, floating logo + name + tricolor line over it
            $this->renderCandidatePhoto(
                $canvas,
                $config['candidate'] ?? [],
                $branding,
                $options,
                $config['footer'] ?? []
            );

            if ($branding) {
                $this->renderFloatingTricolorBadgeFooter(
                    $canvas,
                    $config['footer'] ?? [],
                    $branding,
                    $party,
                    $config['candidate'] ?? []
                );
            }
        } elseif ($footerStyle === 'royal_gold_dock') {
            // Layer 4 (Royal Gold Style): Midnight card first, candidate photo grounded over card
            if ($branding) {
                $this->renderRoyalGoldDockFooter(
                    $canvas,
                    $config['footer'] ?? [],
                    $branding,
                    $party,
                    $config['candidate'] ?? []
                );
            }

            $this->renderCandidatePhoto(
                $canvas,
                $config['candidate'] ?? [],
                $branding,
                $options,
                $config['footer'] ?? []
            );
        } elseif ($footerStyle === 'candidate_left_ribbon') {
            // Layer 5 (Candidate Left Ribbon): Candidate on left, photo first, angled badge over chest
            $leftCandConfig = array_merge($config['candidate'] ?? [], [
                'position' => 'bottom-left',
                'offset_left' => 10,
            ]);
            $this->renderCandidatePhoto(
                $canvas,
                $leftCandConfig,
                $branding,
                $options,
                $config['footer'] ?? []
            );

            if ($branding) {
                $this->renderCandidateLeftRibbonFooter(
                    $canvas,
                    $config['footer'] ?? [],
                    $branding,
                    $party,
                    $leftCandConfig
                );
            }
        } elseif ($footerStyle === 'modern_capsule_dock') {
            // Layer 6 (Modern Capsule Dock): Floating dock card first, candidate grounded over card
            if ($branding) {
                $this->renderModernCapsuleDockFooter(
                    $canvas,
                    $config['footer'] ?? [],
                    $branding,
                    $party,
                    $config['candidate'] ?? []
                );
            }

            $this->renderCandidatePhoto(
                $canvas,
                $config['candidate'] ?? [],
                $branding,
                $options,
                $config['footer'] ?? []
            );
        } elseif ($footerStyle === 'circular_medallion_badge') {
            // Layer 7 (Circular Medallion Badge): Medallion card first, then circular framed candidate avatar
            if ($branding) {
                $this->renderCircularMedallionBadgeFooter(
                    $canvas,
                    $config['footer'] ?? [],
                    $branding,
                    $party,
                    $config['candidate'] ?? []
                );
            }

            // Render candidate as circular avatar overlapping the card
            $medallionConfig = array_merge($config['candidate'] ?? [], [
                'shape' => 'circle',
                'circle_size' => 220,
                'border_color' => $config['footer']['medallion_border'] ?? ($party?->primary_color ?: '#FF9933'),
                'border_width' => 6,
                'position' => 'bottom-right',
                'offset_right' => 25,
                'offset_bottom' => 15,
            ]);
            $this->renderCandidatePhoto(
                $canvas,
                $medallionConfig,
                $branding,
                $options,
                $config['footer'] ?? []
            );
        } elseif ($footerStyle === 'saffron_wave_banner') {
            // Layer 8 (Saffron Wave Banner): Candidate photo rendered first, then dynamic wave banner over lower chest
            $this->renderCandidatePhoto(
                $canvas,
                $config['candidate'] ?? [],
                $branding,
                $options,
                $config['footer'] ?? []
            );

            if ($branding) {
                $this->renderSaffronWaveBannerFooter(
                    $canvas,
                    $config['footer'] ?? [],
                    $branding,
                    $party,
                    $config['candidate'] ?? []
                );
            }
        } elseif ($footerStyle === 'tricolor_angled_blade') {
            // Layer 9 (Tricolor Angled Blade): Candidate photo rendered first, then dynamic angled 3-tier tricolor sash over lower torso
            $this->renderCandidatePhoto(
                $canvas,
                $config['candidate'] ?? [],
                $branding,
                $options,
                $config['footer'] ?? []
            );

            if ($branding) {
                $this->renderTricolorAngledBladeFooter(
                    $canvas,
                    $config['footer'] ?? [],
                    $branding,
                    $party,
                    $config['candidate'] ?? []
                );
            }
        } elseif ($footerStyle === 'royal_maroon_heritage') {
            // Layer 10 (Royal Maroon Heritage): Deep temple maroon & 24K gold filigree card first, then candidate photo over card
            if ($branding) {
                $this->renderRoyalMaroonHeritageFooter(
                    $canvas,
                    $config['footer'] ?? [],
                    $branding,
                    $party,
                    $config['candidate'] ?? []
                );
            }

            $this->renderCandidatePhoto(
                $canvas,
                $config['candidate'] ?? [],
                $branding,
                $options,
                $config['footer'] ?? []
            );
        } else {
            // Default standard political footer
            if ($branding) {
                $this->renderFooterFrame(
                    $canvas,
                    $config['footer'] ?? [],
                    $branding,
                    $party,
                    $config['candidate'] ?? []
                );
            }

            $this->renderCandidatePhoto(
                $canvas,
                $config['candidate'] ?? [],
                $branding,
                $options,
                $config['footer'] ?? []
            );
        }

        return $canvas;
    }

    /**
     * Compose and persist to public storage, returning public relative path & URL.
     */
    public function composeToFile(
        FestivalTemplate|string $template,
        ?UserBranding $branding = null,
        ?LeaderStrip $strip = null,
        array $options = [],
        ?string $filename = null
    ): array {
        $canvas = $this->compose($template, $branding, $strip, $options);

        $filename = $filename ?: ('composed_' . Str::uuid() . '.jpg');
        $relativePath = 'posters/' . date('Y-m') . '/' . $filename;

        // Ensure directory exists in public disk
        Storage::disk('public')->makeDirectory('posters/' . date('Y-m'));

        $fullStoragePath = Storage::disk('public')->path($relativePath);
        $canvas->toJpeg(92)->save($fullStoragePath);

        return [
            'relative_path' => $relativePath,
            'full_path'     => $fullStoragePath,
            'url'           => asset('storage/' . $relativePath),
            'filename'      => $filename,
        ];
    }

    /**
     * Create base 1080x1080 canvas and fit background image.
     */
    protected function createBaseCanvas(string $bgPath, int $width, int $height): ImageInterface
    {
        $canvas = $this->manager->create($width, $height)->fill('#ffffff');

        $resolvedPath = $this->resolveFilePath($bgPath);
        if ($resolvedPath && file_exists($resolvedPath)) {
            try {
                $bgImg = $this->manager->read($resolvedPath);
                $bgImg->cover($width, $height);
                $canvas->place($bgImg, 'top-left', 0, 0);
            } catch (\Throwable $e) {
                // Fallback to solid canvas if corrupt image
            }
        }

        return $canvas;
    }

    // ================================================================
    // HEADER SECTION — Top Leadership Strip
    // ================================================================

    /**
     * Render Top Leadership Header.
     * Mode A: party_group_cutout — transparent PNG group photo at top-left
     * Mode B: individual_chips — evenly-spaced circle avatars across full width (Dr. Dharam Singh style)
     * Mode C: none — no header
     */
    protected function renderHeaderSection(
        ImageInterface $canvas,
        array $headerConfig,
        ?PoliticalParty $party,
        ?LeaderStrip $strip
    ): void {
        $mode = $headerConfig['mode'] ?? 'party_group_cutout';

        // A) Group Cutout Mode (Ram Navami Style)
        if ($mode === 'party_group_cutout') {
            $cutoutPath = $party?->group_cutout_image ?: 'images/parties/bjp-group-cutout.png';
            $resolvedCutout = $this->resolveFilePath($cutoutPath);

            if ($resolvedCutout && file_exists($resolvedCutout)) {
                try {
                    $cutoutImg = $this->manager->read($resolvedCutout);
                    $targetWidth = $headerConfig['group_cutout_width'] ?? 360;
                    $cutoutImg->scaleDown(width: $targetWidth);

                    $x = $headerConfig['group_cutout_x'] ?? 30;
                    $y = $headerConfig['group_cutout_y'] ?? 25;
                    $canvas->place($cutoutImg, 'top-left', $x, $y);
                } catch (\Throwable $e) {
                    // Ignore load error
                }
            }
        }

        // B) Individual Leader Circle / Shaped Chips Mode — Reference Gold Standard
        // Layout: 5-7 avatars with customizable border (saffron, white, etc.), evenly spaced across canvas
        elseif ($mode === 'individual_chips') {
            $leaders = $strip ? $strip->leaders->take(7) : ($party?->leaders()->take(7)->get() ?? collect());
            if ($leaders->isNotEmpty()) {
                $chipSize = $headerConfig['chip_size'] ?? 95;
                $borderWidth = $headerConfig['chip_border_width'] ?? 4;
                $borderColor = $headerConfig['chip_border_color'] ?? '#FF9933';
                $shape = $headerConfig['chip_shape'] ?? 'circle';
                $stripY = $headerConfig['chips_start_y'] ?? 10;
                $leaderCount = $leaders->count();

                $canvasW = 1080;
                $totalChipWidth = $leaderCount * $chipSize;

                // If 5 or more leaders, distribute evenly across full canvas width
                if ($leaderCount >= 5) {
                    $totalGaps = $leaderCount + 1;
                    $gap = (int) (($canvasW - $totalChipWidth) / $totalGaps);
                    if ($gap < 6) $gap = 6;
                    $currX = $gap;
                } else {
                    // When fewer than 5 leaders (e.g. 2 or 3), never stretch with huge ugly gap!
                    // Center the cluster cleanly with an elegant 30px gap.
                    $gap = 30;
                    $clusterWidth = ($leaderCount * $chipSize) + (($leaderCount - 1) * $gap);
                    $currX = (int) (($canvasW - $clusterWidth) / 2);
                }

                foreach ($leaders as $leader) {
                    $leaderImgPath = $this->resolveFilePath($leader->image);
                    if (!$leaderImgPath || !file_exists($leaderImgPath)) {
                        $modiFallback = public_path('images/leaders/modiji.png');
                        if (file_exists($modiFallback)) {
                            $leaderImgPath = $modiFallback;
                        }
                    }
                    if ($leaderImgPath && file_exists($leaderImgPath)) {
                        try {
                            $avatar = $this->manager->read($leaderImgPath)->cover($chipSize, $chipSize);
                            $avatar = $this->makeShapedAvatar($avatar, $chipSize, $shape, $borderColor, $borderWidth);
                            $canvas->place($avatar, 'top-left', $currX, $stripY);
                        } catch (\Throwable $e) {
                            // Skip leader on error
                        }
                    }
                    $currX += $chipSize + $gap;
                }
            }
        }

        // Top Right Party Official Logo (only for group_cutout mode — chips mode already fills top)
        if ($mode === 'party_group_cutout' && ($headerConfig['show_party_logo'] ?? true)) {
            $logoPath = $party?->symbol_logo ?: 'images/parties/bjp.jpeg';
            $resolvedLogo = $this->resolveFilePath($logoPath);
            if ($resolvedLogo && file_exists($resolvedLogo)) {
                try {
                    $logoSize = $headerConfig['logo_size'] ?? 75;
                    $logoImg = $this->manager->read($resolvedLogo)->cover($logoSize, $logoSize);
                    $logoImg = $this->makeCircularAvatar($logoImg, $logoSize, '#ffffff', 2);

                    $logoX = $headerConfig['logo_x'] ?? (1080 - $logoSize - 35);
                    $logoY = $headerConfig['logo_y'] ?? 25;
                    $canvas->place($logoImg, 'top-left', $logoX, $logoY);
                } catch (\Throwable $e) {
                    // Ignore logo error
                }
            }
        }
    }

    // ================================================================
    // CANDIDATE PHOTO — Cutout (Large) or Circular Framed Avatar
    // ================================================================

    /**
     * Render Candidate Photo.
     * Supports:
     *   - Shape: 'cutout' (large transparent portrait chest-to-head, anchored to bottom)
     *   - Shape: 'circle' (circular framed portrait with customizable border and shadow)
     *   - Position: 'bottom-right' | 'bottom-left' | 'bottom-center'
     */
    protected function renderCandidatePhoto(
        ImageInterface $canvas,
        array $candidateConfig,
        ?UserBranding $branding,
        array $options,
        array $footerConfig = []
    ): void {
        $photoPath = $options['cutout_url']
            ?? $branding?->defaultImage?->cutout_image_path
            ?? $branding?->defaultImage?->original_image_path
            ?? $branding?->user?->profile_photo;

        if (empty($photoPath)) {
            return;
        }

        $resolvedPhoto = $this->resolveFilePath($photoPath);
        if (!$resolvedPhoto || !file_exists($resolvedPhoto)) {
            return;
        }

        try {
            $photoImg = $this->manager->read($resolvedPhoto);
            $shape = $candidateConfig['shape'] ?? 'cutout';
            $position = $candidateConfig['position'] ?? 'bottom-right';

            // ====================================================
            // 1. CIRCULAR AVATAR MODE
            // ====================================================
            if ($shape === 'circle') {
                $circleSize = $candidateConfig['circle_size'] ?? 240;
                $borderColor = $candidateConfig['circle_border_color'] ?? '#ffffff';
                $borderWidth = $candidateConfig['circle_border_width'] ?? 5;

                $avatar = $this->makeCircularAvatar($photoImg, $circleSize, $borderColor, $borderWidth);

                $footerTopY = $candidateConfig['footer_top_y'] ?? 910;
                $overlap = $candidateConfig['footer_overlap'] ?? 30;

                if ($position === 'bottom-right') {
                    $x = 1080 - $circleSize - ($candidateConfig['offset_right'] ?? 30);
                    $y = $footerTopY - $circleSize + $overlap;
                } elseif ($position === 'bottom-left') {
                    $x = $candidateConfig['offset_left'] ?? 30;
                    $y = $footerTopY - $circleSize + $overlap;
                } else { // bottom-center
                    $x = (int) ((1080 - $circleSize) / 2);
                    $y = $footerTopY - $circleSize + $overlap;
                }

                $canvas->place($avatar, 'top-left', $x, $y);
                return;
            }

            // ====================================================
            // 2. TRANSPARENT CUTOUT MODE (Dr. Dharam Singh Gold Standard)
            // ====================================================
            $maxW = $candidateConfig['max_width'] ?? 420;
            $maxH = $candidateConfig['max_height'] ?? 480;

            $origW = $photoImg->width();
            $origH = $photoImg->height();

            // Smart sizing: if landscape photo uploaded as cutout, crop center-top so face stays commanding
            if ($origW > ($origH * 1.15)) {
                $photoImg->cover($maxW, $maxH, 'top');
            } else {
                // Portrait cutout: scale proportionally to fill height nicely
                $targetH = min($maxH, 460);
                $scale = $targetH / $origH;

                // Ensure width doesn't exceed maxW
                if (($origW * $scale) > $maxW) {
                    $scale = $maxW / $origW;
                }

                $newW = (int) ($origW * $scale);
                $newH = (int) ($origH * $scale);
                $photoImg->resize($newW, $newH);
            }

            $pw = $photoImg->width();
            $ph = $photoImg->height();

            // Candidate bottom edge touches the very bottom of the poster (y: 1080) so shoulders/kurta extend grounded
            $anchorBottomY = $candidateConfig['anchor_bottom_y'] ?? 1080;
            $y = $anchorBottomY - $ph;

            if ($position === 'bottom-right') {
                $x = 1080 - $pw - ($candidateConfig['offset_right'] ?? 10);
            } elseif ($position === 'bottom-left') {
                $x = $candidateConfig['offset_left'] ?? 10;
            } else { // bottom-center
                $x = (int) ((1080 - $pw) / 2);
            }

            // Ensure candidate doesn't cover top leader strip
            $minY = $candidateConfig['min_y'] ?? 115;
            if ($y < $minY) {
                $y = $minY;
            }

            $canvas->place($photoImg, 'top-left', $x, $y);
        } catch (\Throwable $e) {
            // Ignore candidate load error
        }
    }

    // ================================================================
    // FOOTER SECTION — Political Branding
    // ================================================================

    /**
     * Render Political Footer Frame (Standard Political, Dual Ribbon, or Royal Bar)
     */
    protected function renderFooterFrame(
        ImageInterface $canvas,
        array $footerConfig,
        UserBranding $branding,
        ?PoliticalParty $party,
        array $candidateConfig = []
    ): void {
        $style = $footerConfig['style'] ?? 'standard_political';

        if ($style === 'dual_ribbon_badge') {
            $this->renderDualRibbonBadgeFooter($canvas, $footerConfig, $branding, $party, $candidateConfig);
        } elseif ($style === 'solid_white_banner') {
            $this->renderSolidWhiteBannerFooter($canvas, $footerConfig, $branding, $party, $candidateConfig);
        } elseif ($style === 'floating_tricolor_badge') {
            $this->renderFloatingTricolorBadgeFooter($canvas, $footerConfig, $branding, $party, $candidateConfig);
        } elseif ($style === 'royal_gold_dock') {
            $this->renderRoyalGoldDockFooter($canvas, $footerConfig, $branding, $party, $candidateConfig);
        } elseif ($style === 'candidate_left_ribbon') {
            $this->renderCandidateLeftRibbonFooter($canvas, $footerConfig, $branding, $party, $candidateConfig);
        } elseif ($style === 'modern_capsule_dock') {
            $this->renderModernCapsuleDockFooter($canvas, $footerConfig, $branding, $party, $candidateConfig);
        } elseif ($style === 'circular_medallion_badge') {
            $this->renderCircularMedallionBadgeFooter($canvas, $footerConfig, $branding, $party, $candidateConfig);
        } elseif ($style === 'saffron_wave_banner') {
            $this->renderSaffronWaveBannerFooter($canvas, $footerConfig, $branding, $party, $candidateConfig);
        } elseif ($style === 'tricolor_angled_blade') {
            $this->renderTricolorAngledBladeFooter($canvas, $footerConfig, $branding, $party, $candidateConfig);
        } elseif ($style === 'royal_maroon_heritage') {
            $this->renderRoyalMaroonHeritageFooter($canvas, $footerConfig, $branding, $party, $candidateConfig);
        } elseif ($style === 'royal_bar') {
            $this->renderRoyalBarFooter($canvas, $footerConfig, $branding, $party);
        } elseif ($style === 'dual_ribbon') {
            $this->renderDualRibbonFooter($canvas, $footerConfig, $branding, $party);
        } else {
            $this->renderStandardPoliticalFooter($canvas, $footerConfig, $branding, $party, $candidateConfig);
        }
    }

    /**
     * Layer 1: Dual Ribbon Badge (Bhai Dooj Reference Style - Image 2)
     * - Candidate cutout sits anchored to bottom-right.
     * - Left side: Party secondary color ribbon (Green) with Lotus Logo + Party Hindi Name in white.
     * - Right side: Party primary color ribbon (Saffron) with angled cut sitting OVER candidate lower chest,
     *   containing Candidate Name in crisp White bold and Designation in warm golden yellow.
     * - Bottom: Clean white social strip.
     */
    protected function renderDualRibbonBadgeFooter(
        ImageInterface $canvas,
        array $footerConfig,
        UserBranding $branding,
        ?PoliticalParty $party,
        array $candidateConfig = []
    ): void {
        $fontHindiBold = $this->getFontPath('hindi_bold');
        $fontHindiRegular = $this->getFontPath('hindi_regular');
        $fontEngRegular = $this->getFontPath('english_regular');

        $partyPrimary = $footerConfig['primary_color'] ?? ($party?->primary_color ?: '#FF9933');
        $partySecondary = $footerConfig['secondary_color'] ?? ($party?->secondary_color ?: '#00873E');

        $nativeGd = $canvas->core()->native();

        // 1. Left Party Ribbon (Green Bar)
        // y: 948 to 1025, from x: 20 to x: 550
        $partyBarY = 948;
        $partyBarH = 75;
        $partyBarW = 525;

        $canvas->drawRectangle(20, $partyBarY, function ($rect) use ($partySecondary, $partyBarW, $partyBarH) {
            $rect->size($partyBarW, $partyBarH);
            $rect->background($partySecondary);
        });

        // Party Logo on left of party strip
        $logoPath = $party?->symbol_logo ?: 'images/parties/bjp.jpeg';
        $resolvedLogo = $this->resolveFilePath($logoPath);
        $logoSize = 56;
        $partyTextX = 30;

        if ($resolvedLogo && file_exists($resolvedLogo)) {
            try {
                $logoImg = $this->manager->read($resolvedLogo)->cover($logoSize, $logoSize);
                $logoImg = $this->makeCircularAvatar($logoImg, $logoSize, '#ffffff', 2);
                $logoY = $partyBarY + (int)(($partyBarH - $logoSize) / 2);
                $canvas->place($logoImg, 'top-left', 30, $logoY);
                $partyTextX = 30 + $logoSize + 12;
            } catch (\Throwable $e) {}
        }

        $partyName = $this->shapeDevanagari($party?->hindi_name ?: ($party?->name ?: 'भारतीय जनता पार्टी'));
        $partyNameY = $partyBarY + (int)($partyBarH / 2) + 8;
        $canvas->text($partyName, $partyTextX, $partyNameY, function ($font) use ($fontHindiBold) {
            $font->file($fontHindiBold);
            $font->size(23);
            $font->color('#ffffff');
        });

        // 2. Right Candidate Name Badge (Saffron Angled Box placed OVER candidate photo)
        $badgeTopY = 925;
        $badgeBottomY = 1025;
        $badgeStartX = 535;
        $badgeAngleX = 575; // Angled top-left

        $sHex = ltrim($partyPrimary, '#');
        $sr = hexdec(substr($sHex, 0, 2));
        $sg = hexdec(substr($sHex, 2, 2));
        $sb = hexdec(substr($sHex, 4, 2));
        $saffronAlloc = imagecolorallocate($nativeGd, $sr, $sg, $sb);

        imagefilledpolygon($nativeGd, [
            $badgeAngleX, $badgeTopY,     // Top left (slanted inward)
            1080, $badgeTopY,            // Top right
            1080, $badgeBottomY,         // Bottom right
            $badgeStartX, $badgeBottomY  // Bottom left
        ], $saffronAlloc);

        // Thin white top accent line for the badge
        $whiteAlloc = imagecolorallocate($nativeGd, 255, 255, 255);
        imageline($nativeGd, $badgeAngleX, $badgeTopY, 1080, $badgeTopY, $whiteAlloc);
        imageline($nativeGd, $badgeAngleX, $badgeTopY + 1, 1080, $badgeTopY + 1, $whiteAlloc);

        // Candidate Name in crisp White bold font
        $nameTextX = $badgeAngleX + 25;
        $nameTextY = $badgeTopY + 45;
        $maxNameW = 1080 - $nameTextX - 20;

        $displayName = $this->shapeDevanagari($branding->display_name);
        $this->renderAutoFitText(
            $canvas,
            $displayName,
            $nameTextX,
            $nameTextY,
            $maxNameW,
            32,
            18,
            $fontHindiBold,
            '#ffffff',
            'left'
        );

        // Candidate Designation
        if (!empty($branding->designation)) {
            $desigText = $this->shapeDevanagari($branding->designation);
            $desigY = $nameTextY + 34;
            $this->renderAutoFitText(
                $canvas,
                $desigText,
                $nameTextX,
                $desigY,
                $maxNameW,
                19,
                12,
                $fontHindiRegular,
                '#FFE57F', // Warm Golden Yellow
                'left'
            );
        }

        // 3. Bottom Clean White Social Handles Strip (y: 1025 to 1080)
        $socialY = 1025;
        $socialH = 55;
        $canvas->drawRectangle(0, $socialY, function ($rect) use ($socialH) {
            $rect->size(1080, $socialH);
            $rect->background('#ffffff');
        });

        $grayAlloc = imagecolorallocate($nativeGd, 226, 232, 240);
        imageline($nativeGd, 0, $socialY, 1080, $socialY, $grayAlloc);

        $this->renderSocialIconsRow($canvas, $branding, 30, $socialY + 14, $socialY + 34, $fontEngRegular);
    }

    /**
     * Layer 2: Solid White Card Banner (Milkha Singh Reference Style - Image 3)
     * - Full-width solid white card from y: 910 to 1080.
     * - Ethnic geometric motif along the top border line (y: 900 to 910).
     * - Left side: Circular lotus logo flanked by tricolor wave flags.
     * - Candidate Name in bold Saffron/Party primary color + dark grey designation.
     * - Candidate cutout sits grounded on bottom-right.
     * - Bottom: Micro social handles strip.
     */
    protected function renderSolidWhiteBannerFooter(
        ImageInterface $canvas,
        array $footerConfig,
        UserBranding $branding,
        ?PoliticalParty $party,
        array $candidateConfig = []
    ): void {
        $fontHindiBold = $this->getFontPath('hindi_bold');
        $fontHindiRegular = $this->getFontPath('hindi_regular');
        $fontEngRegular = $this->getFontPath('english_regular');

        $partyPrimary = $footerConfig['name_color'] ?? ($party?->primary_color ?: '#E65100');
        $nativeGd = $canvas->core()->native();

        $footerTop = 910;
        $footerHeight = 170;

        // 1. Full White Card Background
        $canvas->drawRectangle(0, $footerTop, function ($rect) use ($footerHeight) {
            $rect->size(1080, $footerHeight);
            $rect->background('#ffffff');
        });

        // 2. Ethnic Ornamental Geometric Motif along the top edge (y: 900 to 910)
        $canvas->drawRectangle(0, 900, function ($rect) {
            $rect->size(1080, 10);
            $rect->background('#fbf8f2');
        });

        $saffronAlloc = imagecolorallocate($nativeGd, 255, 153, 51);
        $greenAlloc = imagecolorallocate($nativeGd, 19, 136, 8);
        $redAlloc = imagecolorallocate($nativeGd, 217, 83, 79);

        imageline($nativeGd, 0, 900, 1080, 900, $saffronAlloc);
        imageline($nativeGd, 0, 901, 1080, 901, $saffronAlloc);

        // Repeated ornamental motif ticks (diamonds / small chevrons every 16px)
        for ($px = 8; $px < 1075; $px += 16) {
            imagesetpixel($nativeGd, $px, 905, $greenAlloc);
            imagesetpixel($nativeGd, $px + 1, 904, $saffronAlloc);
            imagesetpixel($nativeGd, $px + 1, 906, $saffronAlloc);
            imagesetpixel($nativeGd, $px + 2, 905, $redAlloc);
        }

        imageline($nativeGd, 0, 910, 1080, 910, $greenAlloc);
        imageline($nativeGd, 0, 911, 1080, 911, $greenAlloc);

        // 3. Left Party Logo with Tricolor Wave Flag Accents
        $logoX = 35;
        $logoY = 926;
        $logoSize = 72;

        $canvas->drawRectangle($logoX - 10, $logoY + 20, function ($rect) {
            $rect->size(10, 32);
            $rect->background('#FF9933'); // Saffron flag wave
        });
        $canvas->drawRectangle($logoX + $logoSize, $logoY + 20, function ($rect) {
            $rect->size(10, 32);
            $rect->background('#138808'); // Green flag wave
        });

        $logoPath = $party?->symbol_logo ?: 'images/parties/bjp.jpeg';
        $resolvedLogo = $this->resolveFilePath($logoPath);
        if ($resolvedLogo && file_exists($resolvedLogo)) {
            try {
                $logoImg = $this->manager->read($resolvedLogo)->cover($logoSize, $logoSize);
                $logoImg = $this->makeCircularAvatar($logoImg, $logoSize, '#ffffff', 3);
                $canvas->place($logoImg, 'top-left', $logoX, $logoY);
            } catch (\Throwable $e) {}
        }

        // 4. Candidate Name & Designation (Left of candidate photo)
        $nameX = $logoX + $logoSize + 25;
        $nameY = 968;
        $maxNameW = 460;

        $displayName = $this->shapeDevanagari($branding->display_name);
        $this->renderAutoFitText(
            $canvas,
            $displayName,
            $nameX,
            $nameY,
            $maxNameW,
            34,
            18,
            $fontHindiBold,
            $partyPrimary,
            'left'
        );

        if (!empty($branding->designation)) {
            $desigText = $this->shapeDevanagari($branding->designation);
            $this->renderAutoFitText(
                $canvas,
                $desigText,
                $nameX,
                $nameY + 32,
                $maxNameW,
                18,
                12,
                $fontHindiRegular,
                '#4a5568',
                'left'
            );
        }

        // 5. Bottom Social Media Strip with Orange Dividing Line (y: 1040 to 1080)
        $socialY = 1040;
        $socialH = 40;

        imageline($nativeGd, 0, $socialY, 1080, $socialY, $saffronAlloc);

        $canvas->drawRectangle(0, $socialY + 1, function ($rect) use ($socialH) {
            $rect->size(1080, $socialH - 1);
            $rect->background('#fffaf5');
        });

        $this->renderSocialIconsRow($canvas, $branding, 25, $socialY + 8, $socialY + 26, $fontEngRegular);
    }

    /**
     * Layer 3: Floating Tricolor Badge (Diwali Reference Style - Image 1)
     * - Transparent/Floating layout directly on festival artwork (no big card blocking background art).
     * - Subtle bottom gradient protection so white/colored text is crisp on any background.
     * - Left side: Circular party logo badge with white backdrop and thin border.
     * - Next to logo: Candidate Name in bold bright color (auto-fit with Devanagari shaping).
     * - Under Name: Indian Tricolor ribbon line (Saffron #FF9933, White #FFFFFF, Green #138808).
     * - Under Tricolor: Designation in crisp white text with subtle shadow.
     * - Candidate cutout: Anchored to bottom right.
     * - Very bottom: Sleek white/light-tint social strip (y: 1042-1080) with social pills.
     */
    protected function renderFloatingTricolorBadgeFooter(
        ImageInterface $canvas,
        array $footerConfig,
        UserBranding $branding,
        ?PoliticalParty $party,
        array $candidateConfig = []
    ): void {
        $fontHindiBold = $this->getFontPath('hindi_bold');
        $fontHindiRegular = $this->getFontPath('hindi_regular');
        $fontEngRegular = $this->getFontPath('english_regular');

        $nameColor = $footerConfig['name_color'] ?? '#ffffff';
        $partyPrimary = $footerConfig['primary_color'] ?? ($party?->primary_color ?: '#FF9933');

        // 1. Subtle dark vignette / gradient at bottom for contrast so text always pops
        $gradientH = 200;
        $startY = 1080 - $gradientH;
        $nativeGd = $canvas->core()->native();
        imagealphablending($nativeGd, true);
        for ($i = 0; $i < $gradientH; $i++) {
            $alpha = (int) (127 - ($i / $gradientH) * 85);
            if ($alpha > 127) $alpha = 127;
            if ($alpha < 0) $alpha = 0;
            $color = imagecolorallocatealpha($nativeGd, 0, 0, 0, $alpha);
            imageline($nativeGd, 0, $startY + $i, 1080, $startY + $i, $color);
        }

        // 2. Circular Party Logo on bottom left
        $logoPath = $party?->symbol_logo ?: 'images/parties/bjp.jpeg';
        $resolvedLogo = $this->resolveFilePath($logoPath);
        $logoSize = $footerConfig['logo_size'] ?? 76;
        $logoX = 25;
        $logoY = 920;

        if ($resolvedLogo && file_exists($resolvedLogo)) {
            try {
                $logoImg = $this->manager->read($resolvedLogo)->cover($logoSize, $logoSize);
                $logoImg = $this->makeCircularAvatar($logoImg, $logoSize, '#ffffff', 3);
                $canvas->place($logoImg, 'top-left', $logoX, $logoY);
            } catch (\Throwable $e) {}
        }

        $textStartX = $logoX + $logoSize + 15;
        $maxTextW = 550;

        // 3. Candidate Name (Bold Auto-Fit with shadow for high contrast)
        $displayName = $this->shapeDevanagari($branding->display_name);
        $nameY = 942;
        $nameFontSize = $footerConfig['name_font_size'] ?? 34;

        $this->renderAutoFitText(
            $canvas,
            $displayName,
            $textStartX + 2,
            $nameY + 2,
            $maxTextW,
            $nameFontSize,
            18,
            $fontHindiBold,
            '#000000',
            'left'
        );
        $this->renderAutoFitText(
            $canvas,
            $displayName,
            $textStartX,
            $nameY,
            $maxTextW,
            $nameFontSize,
            18,
            $fontHindiBold,
            $nameColor,
            'left'
        );

        // 4. Indian Tricolor Ribbon Underline under Name
        $triY = $nameY + 14;
        $triWidth = min(380, $maxTextW);
        $segW = (int) ($triWidth / 3);

        $canvas->drawRectangle($textStartX, $triY, function ($rect) use ($segW) {
            $rect->size($segW, 5);
            $rect->background('#FF9933'); // Saffron
        });
        $canvas->drawRectangle($textStartX + $segW, $triY, function ($rect) use ($segW) {
            $rect->size($segW, 5);
            $rect->background('#ffffff'); // White
        });
        $canvas->drawRectangle($textStartX + ($segW * 2), $triY, function ($rect) use ($segW) {
            $rect->size($segW, 5);
            $rect->background('#138808'); // Green
        });

        // 5. Designation Text (Under Tricolor Line)
        if (!empty($branding->designation)) {
            $desigText = $this->shapeDevanagari($branding->designation);
            $desigY = $triY + 28;
            $desigColor = $footerConfig['desig_color'] ?? '#ffffff';

            $this->renderAutoFitText(
                $canvas,
                $desigText,
                $textStartX + 1,
                $desigY + 1,
                $maxTextW,
                19,
                12,
                $fontHindiRegular,
                '#000000',
                'left'
            );
            $this->renderAutoFitText(
                $canvas,
                $desigText,
                $textStartX,
                $desigY,
                $maxTextW,
                19,
                12,
                $fontHindiRegular,
                $desigColor,
                'left'
            );
        }

        // 6. Sleek Bottom Social Handles Bar (y: 1042-1080)
        $socialY = 1042;
        $socialH = 38;
        $canvas->drawRectangle(0, $socialY, function ($rect) use ($socialH) {
            $rect->size(1080, $socialH);
            $rect->background('#ffffff');
        });

        $this->renderSocialIconsRow(
            $canvas,
            $branding,
            25,
            $socialY + 7,
            $socialY + 25,
            $fontEngRegular,
            '#333333'
        );
    }

    /**
     * Layer 4: Royal Gold Banner (Deep Midnight Card with Dual Gold Borders)
     * - Deep obsidian/navy card at bottom (y: 915 to 1080).
     * - Dual parallel golden accent lines along top edge (#D4AF37).
     * - Gold-ring circular party emblem on left.
     * - Pure white/champagne-gold bold auto-fit candidate name.
     * - Warm gold designation text.
     * - Dark luxury social handles bar with white text.
     */
    protected function renderRoyalGoldDockFooter(
        ImageInterface $canvas,
        array $footerConfig,
        UserBranding $branding,
        ?PoliticalParty $party,
        array $candidateConfig = []
    ): void {
        $fontHindiBold = $this->getFontPath('hindi_bold');
        $fontHindiRegular = $this->getFontPath('hindi_regular');
        $fontEngRegular = $this->getFontPath('english_regular');

        $goldColor = '#D4AF37'; // Antique Gold
        $cardBg = $footerConfig['card_bg'] ?? '#0f172a'; // Deep Midnight Slate
        $nameColor = $footerConfig['name_color'] ?? '#ffffff';
        $desigColor = $footerConfig['desig_color'] ?? '#E5C158'; // Champagne Gold

        $cardTopY = 915;
        $cardHeight = 165;

        // 1. Solid Deep Midnight Card
        $canvas->drawRectangle(0, $cardTopY, function ($rect) use ($cardBg, $cardHeight) {
            $rect->size(1080, $cardHeight);
            $rect->background($cardBg);
        });

        // 2. Dual Golden Accent Lines along Top Edge
        $canvas->drawRectangle(0, $cardTopY, function ($rect) use ($goldColor) {
            $rect->size(1080, 3);
            $rect->background($goldColor);
        });
        $canvas->drawRectangle(0, $cardTopY + 5, function ($rect) use ($goldColor) {
            $rect->size(1080, 1);
            $rect->background($goldColor);
        });

        // 3. Circular Party Emblem with Gold Ring
        $logoPath = $party?->symbol_logo ?: 'images/parties/bjp.jpeg';
        $resolvedLogo = $this->resolveFilePath($logoPath);
        $logoSize = $footerConfig['logo_size'] ?? 64;
        $logoX = 30;
        $logoY = $cardTopY + 16;

        if ($resolvedLogo && file_exists($resolvedLogo)) {
            try {
                $logoImg = $this->manager->read($resolvedLogo)->cover($logoSize, $logoSize);
                $logoImg = $this->makeCircularAvatar($logoImg, $logoSize, $goldColor, 3);
                $canvas->place($logoImg, 'top-left', $logoX, $logoY);
            } catch (\Throwable $e) {}
        }

        $textStartX = $logoX + $logoSize + 16;
        $maxTextW = 540;

        // 4. Candidate Name (Bold White Auto-Fit)
        $displayName = $this->shapeDevanagari($branding->display_name);
        $nameY = $cardTopY + 48;
        $nameFontSize = $footerConfig['name_font_size'] ?? 32;

        $this->renderAutoFitText(
            $canvas,
            $displayName,
            $textStartX,
            $nameY,
            $maxTextW,
            $nameFontSize,
            16,
            $fontHindiBold,
            $nameColor,
            'left'
        );

        // 5. Designation (Champagne Gold Auto-Fit)
        if (!empty($branding->designation)) {
            $desigText = $this->shapeDevanagari($branding->designation);
            $desigY = $nameY + 28;
            $this->renderAutoFitText(
                $canvas,
                $desigText,
                $textStartX,
                $desigY,
                $maxTextW,
                18,
                12,
                $fontHindiRegular,
                $desigColor,
                'left'
            );
        }

        // 6. Thin Gold Divider Line above Social Row
        $socialTopY = 1032;
        $canvas->drawRectangle(0, $socialTopY, function ($rect) {
            $rect->size(1080, 1);
            $rect->background('#334155');
        });

        // 7. Dark Luxury Social Row
        $this->renderSocialIconsRow(
            $canvas,
            $branding,
            30,
            $socialTopY + 8,
            $socialTopY + 27,
            $fontEngRegular,
            '#e2e8f0'
        );
    }

    /**
     * Layer 5: Candidate Left Ribbon (Mirrored Angled Badge for Right-Focused Artwork)
     * - Candidate cutout sits anchored to bottom-left (x: 10 to 420).
     * - Middle/Right: Saffron angled ribbon badge sitting over candidate's right lower chest/arm.
     * - Far Right: Party secondary color strip (Green) with circular party logo and Hindi name.
     * - Bottom: Full-width clean white social handles strip.
     */
    protected function renderCandidateLeftRibbonFooter(
        ImageInterface $canvas,
        array $footerConfig,
        UserBranding $branding,
        ?PoliticalParty $party,
        array $candidateConfig = []
    ): void {
        $fontHindiBold = $this->getFontPath('hindi_bold');
        $fontHindiRegular = $this->getFontPath('hindi_regular');
        $fontEngRegular = $this->getFontPath('english_regular');

        $partyPrimary = $footerConfig['primary_color'] ?? ($party?->primary_color ?: '#FF9933');
        $partySecondary = $footerConfig['secondary_color'] ?? ($party?->secondary_color ?: '#00873E');

        $nativeGd = $canvas->core()->native();

        // 1. Right Party Ribbon (Green Bar)
        // y: 948 to 1025, from x: 530 to 1060
        $partyBarY = 948;
        $partyBarH = 75;
        $partyBarW = 530;
        $partyBarX = 530;

        $canvas->drawRectangle($partyBarX, $partyBarY, function ($rect) use ($partySecondary, $partyBarW, $partyBarH) {
            $rect->size($partyBarW, $partyBarH);
            $rect->background($partySecondary);
        });

        // Party Logo on far right
        $logoPath = $party?->symbol_logo ?: 'images/parties/bjp.jpeg';
        $resolvedLogo = $this->resolveFilePath($logoPath);
        $logoSize = 56;
        $logoX = 1080 - $logoSize - 25;

        if ($resolvedLogo && file_exists($resolvedLogo)) {
            try {
                $logoImg = $this->manager->read($resolvedLogo)->cover($logoSize, $logoSize);
                $logoImg = $this->makeCircularAvatar($logoImg, $logoSize, '#ffffff', 2);
                $logoY = $partyBarY + (int)(($partyBarH - $logoSize) / 2);
                $canvas->place($logoImg, 'top-left', $logoX, $logoY);
            } catch (\Throwable $e) {}
        }

        // Party Hindi Name next to logo
        $partyName = $this->shapeDevanagari($party?->hindi_name ?: ($party?->name ?: 'भारतीय जनता पार्टी'));
        $partyNameY = $partyBarY + (int)($partyBarH / 2) + 8;
        $canvas->text($partyName, $partyBarX + 25, $partyNameY, function ($font) use ($fontHindiBold) {
            $font->file($fontHindiBold);
            $font->size(22);
            $font->color('#ffffff');
        });

        // 2. Candidate Name Angled Badge (Saffron Polygon over Candidate's right chest)
        // From x: 260 to x: 680, with angled right side
        $badgeTopY = 925;
        $badgeBottomY = 1025;
        $badgeStartX = 260;
        $badgeEndX = 680;
        $badgeAngleX = 640;

        $sHex = ltrim($partyPrimary, '#');
        $sr = hexdec(substr($sHex, 0, 2));
        $sg = hexdec(substr($sHex, 2, 2));
        $sb = hexdec(substr($sHex, 4, 2));
        $badgeAlloc = imagecolorallocate($nativeGd, $sr, $sg, $sb);

        $badgePoints = [
            $badgeStartX, $badgeTopY,
            $badgeEndX, $badgeTopY,
            $badgeAngleX, $badgeBottomY,
            $badgeStartX, $badgeBottomY,
        ];
        imagefilledpolygon($nativeGd, $badgePoints, $badgeAlloc);

        // Subtle top shadow border
        $whiteBorder = imagecolorallocatealpha($nativeGd, 255, 255, 255, 70);
        imageline($nativeGd, $badgeStartX, $badgeTopY, $badgeEndX, $badgeTopY, $whiteBorder);

        // Candidate Name & Designation inside Saffron Badge
        $displayName = $this->shapeDevanagari($branding->display_name);
        $nameX = $badgeStartX + 25;
        $nameY = $badgeTopY + 36;
        $maxNameW = $badgeAngleX - $nameX - 20;

        $this->renderAutoFitText(
            $canvas,
            $displayName,
            $nameX,
            $nameY,
            $maxNameW,
            27,
            16,
            $fontHindiBold,
            '#ffffff',
            'left'
        );

        if (!empty($branding->designation)) {
            $desigText = $this->shapeDevanagari($branding->designation);
            $desigY = $nameY + 28;
            $this->renderAutoFitText(
                $canvas,
                $desigText,
                $nameX,
                $desigY,
                $maxNameW,
                17,
                12,
                $fontHindiRegular,
                '#FFF275',
                'left'
            );
        }

        // 3. Clean White Social Media Strip at Bottom (y: 1035 to 1080)
        $socialY = 1035;
        $socialH = 45;

        $canvas->drawRectangle(0, $socialY, function ($rect) use ($socialH) {
            $rect->size(1080, $socialH);
            $rect->background('#ffffff');
        });

        // Thin separator line
        $separatorAlloc = imagecolorallocate($nativeGd, 230, 230, 230);
        imageline($nativeGd, 0, $socialY, 1080, $socialY, $separatorAlloc);

        $this->renderSocialIconsRow($canvas, $branding, 25, $socialY + 10, $socialY + 28, $fontEngRegular, '#333333');
    }

    /**
     * Layer 6: Modern Capsule Dock (Floating Rounded Pill Card with Inset Socials)
     * - Floating rounded card inset from edges (x: 25 to 1055, y: 920 to 1060).
     * - Smooth rounded corners (radius: 20px).
     * - Top border accent in party primary color.
     * - Circular party emblem on left.
     * - Candidate name & designation in crisp auto-fit typography.
     * - Embedded social row inside the card.
     * - Candidate photo stands on bottom-right naturally overlapping the dock.
     */
    protected function renderModernCapsuleDockFooter(
        ImageInterface $canvas,
        array $footerConfig,
        UserBranding $branding,
        ?PoliticalParty $party,
        array $candidateConfig = []
    ): void {
        $fontHindiBold = $this->getFontPath('hindi_bold');
        $fontHindiRegular = $this->getFontPath('hindi_regular');
        $fontEngRegular = $this->getFontPath('english_regular');

        $primaryColor = $footerConfig['primary_color'] ?? ($party?->primary_color ?: '#FF9933');
        $cardBg = $footerConfig['card_bg'] ?? '#ffffff';
        $nameColor = $footerConfig['name_color'] ?? '#1e293b';

        $nativeGd = $canvas->core()->native();
        imagealphablending($nativeGd, true);

        $dockX = 25;
        $dockY = 920;
        $dockW = 1030;
        $dockH = 142;
        $radius = 20;

        // 1. Draw subtle soft shadow behind rounded dock
        $shadowColor = imagecolorallocatealpha($nativeGd, 0, 0, 0, 110);
        $this->drawRoundedCard($nativeGd, $dockX, $dockY + 3, $dockW, $dockH, $radius, $shadowColor);

        // 2. Draw solid white/light rounded card
        $cHex = ltrim($cardBg, '#');
        $cr = hexdec(substr($cHex, 0, 2));
        $cg = hexdec(substr($cHex, 2, 2));
        $cb = hexdec(substr($cHex, 4, 2));
        $cardAlloc = imagecolorallocate($nativeGd, $cr, $cg, $cb);
        $borderAlloc = imagecolorallocate($nativeGd, 226, 232, 240);

        $this->drawRoundedCard($nativeGd, $dockX, $dockY, $dockW, $dockH, $radius, $cardAlloc, $borderAlloc);

        // 3. Top Accent Line along top inside edge
        $pHex = ltrim($primaryColor, '#');
        $pr = hexdec(substr($pHex, 0, 2));
        $pg = hexdec(substr($pHex, 2, 2));
        $pb = hexdec(substr($pHex, 4, 2));
        $accentAlloc = imagecolorallocate($nativeGd, $pr, $pg, $pb);
        imagefilledrectangle($nativeGd, $dockX + $radius, $dockY, $dockX + $dockW - $radius - 1, $dockY + 4, $accentAlloc);

        // 4. Circular Party Logo on left inside capsule
        $logoPath = $party?->symbol_logo ?: 'images/parties/bjp.jpeg';
        $resolvedLogo = $this->resolveFilePath($logoPath);
        $logoSize = $footerConfig['logo_size'] ?? 66;
        $logoX = $dockX + 22;
        $logoY = $dockY + (int)(($dockH - $logoSize) / 2) - 4;

        if ($resolvedLogo && file_exists($resolvedLogo)) {
            try {
                $logoImg = $this->manager->read($resolvedLogo)->cover($logoSize, $logoSize);
                $logoImg = $this->makeCircularAvatar($logoImg, $logoSize, $primaryColor, 3);
                $canvas->place($logoImg, 'top-left', $logoX, $logoY);
            } catch (\Throwable $e) {}
        }

        $textStartX = $logoX + $logoSize + 18;
        $maxTextW = 500;

        // 5. Candidate Name (Bold Auto-Fit)
        $displayName = $this->shapeDevanagari($branding->display_name);
        $nameY = $dockY + 44;
        $nameFontSize = $footerConfig['name_font_size'] ?? 30;

        $this->renderAutoFitText(
            $canvas,
            $displayName,
            $textStartX,
            $nameY,
            $maxTextW,
            $nameFontSize,
            16,
            $fontHindiBold,
            $nameColor,
            'left'
        );

        // 6. Designation
        if (!empty($branding->designation)) {
            $desigText = $this->shapeDevanagari($branding->designation);
            $desigY = $nameY + 28;
            $desigColor = $footerConfig['desig_color'] ?? '#64748b';
            $this->renderAutoFitText(
                $canvas,
                $desigText,
                $textStartX,
                $desigY,
                $maxTextW,
                17,
                12,
                $fontHindiRegular,
                $desigColor,
                'left'
            );
        }

        // 7. Embedded Social Row inside the floating capsule
        $socialPillY = $dockY + 98;
        $socialTextY = $dockY + 115;
        $this->renderSocialIconsRow(
            $canvas,
            $branding,
            $textStartX,
            $socialPillY,
            $socialTextY,
            $fontEngRegular,
            '#475569'
        );
    }

    /**
     * Helper to draw a filled rounded rectangle card in PHP GD.
     */
    protected function drawRoundedCard(
        $gd,
        int $x,
        int $y,
        int $w,
        int $h,
        int $radius,
        int $fillColor,
        ?int $borderColor = null
    ): void {
        imagefilledrectangle($gd, $x + $radius, $y, $x + $w - $radius - 1, $y + $h - 1, $fillColor);
        imagefilledrectangle($gd, $x, $y + $radius, $x + $radius - 1, $y + $h - $radius - 1, $fillColor);
        imagefilledrectangle($gd, $x + $w - $radius, $y + $radius, $x + $w - 1, $y + $h - $radius - 1, $fillColor);

        $d = $radius * 2;
        imagefilledarc($gd, $x + $radius, $y + $radius, $d, $d, 180, 270, $fillColor, IMG_ARC_PIE);
        imagefilledarc($gd, $x + $w - $radius - 1, $y + $radius, $d, $d, 270, 360, $fillColor, IMG_ARC_PIE);
        imagefilledarc($gd, $x + $w - $radius - 1, $y + $h - $radius - 1, $d, $d, 0, 90, $fillColor, IMG_ARC_PIE);
        imagefilledarc($gd, $x + $radius, $y + $h - $radius - 1, $d, $d, 90, 180, $fillColor, IMG_ARC_PIE);

        if ($borderColor !== null) {
            imageline($gd, $x + $radius, $y, $x + $w - $radius - 1, $y, $borderColor);
            imageline($gd, $x + $radius, $y + $h - 1, $x + $w - $radius - 1, $y + $h - 1, $borderColor);
            imageline($gd, $x, $y + $radius, $x, $y + $h - $radius - 1, $borderColor);
            imageline($gd, $x + $w - 1, $y + $radius, $x + $w - 1, $y + $h - $radius - 1, $borderColor);
            imagearc($gd, $x + $radius, $y + $radius, $d, $d, 180, 270, $borderColor);
            imagearc($gd, $x + $w - $radius - 1, $y + $radius, $d, $d, 270, 360, $borderColor);
            imagearc($gd, $x + $w - $radius - 1, $y + $h - $radius - 1, $d, $d, 0, 90, $borderColor);
            imagearc($gd, $x + $radius, $y + $h - $radius - 1, $d, $d, 90, 180, $borderColor);
        }
    }

    /**
     * Layer 7: Circular Medallion Badge (Dual-Tier Card & Circular Framed Avatar)
     * - White/Cream card at bottom (y: 925 to 1080).
     * - Circular party logo on left.
     * - Auto-fit Candidate Name (bold crimson/saffron) + Designation (slate).
     * - Space on right reserved for the 220px circular framed medallion avatar (x: 825 to 1055).
     * - Bottom clean social strip (y: 1035 to 1080).
     */
    protected function renderCircularMedallionBadgeFooter(
        ImageInterface $canvas,
        array $footerConfig,
        UserBranding $branding,
        ?PoliticalParty $party,
        array $candidateConfig = []
    ): void {
        $fontHindiBold = $this->getFontPath('hindi_bold');
        $fontHindiRegular = $this->getFontPath('hindi_regular');
        $fontEngRegular = $this->getFontPath('english_regular');

        $primaryColor = $footerConfig['primary_color'] ?? ($party?->primary_color ?: '#FF9933');
        $cardBg = $footerConfig['card_bg'] ?? '#fdfbf7';
        $nameColor = $footerConfig['name_color'] ?? '#c0392b';

        $cardTopY = 925;
        $cardHeight = 155;

        // 1. Draw Card Background
        $canvas->drawRectangle(0, $cardTopY, function ($rect) use ($cardBg, $cardHeight) {
            $rect->size(1080, $cardHeight);
            $rect->background($cardBg);
        });

        // 2. Top Saffron Decorative Line (4px)
        $canvas->drawRectangle(0, $cardTopY, function ($rect) use ($primaryColor) {
            $rect->size(1080, 4);
            $rect->background($primaryColor);
        });

        // 3. Circular Party Logo on Left
        $logoPath = $party?->symbol_logo ?: 'images/parties/bjp.jpeg';
        $resolvedLogo = $this->resolveFilePath($logoPath);
        $logoSize = $footerConfig['logo_size'] ?? 64;
        $logoX = 30;
        $logoY = $cardTopY + 16;

        if ($resolvedLogo && file_exists($resolvedLogo)) {
            try {
                $logoImg = $this->manager->read($resolvedLogo)->cover($logoSize, $logoSize);
                $logoImg = $this->makeCircularAvatar($logoImg, $logoSize, '#ffffff', 2);
                $canvas->place($logoImg, 'top-left', $logoX, $logoY);
            } catch (\Throwable $e) {}
        }

        $textStartX = $logoX + $logoSize + 18;
        $maxTextW = 680;

        // 4. Candidate Name (Bold Auto-Fit)
        $displayName = $this->shapeDevanagari($branding->display_name);
        $nameY = $cardTopY + 46;
        $nameFontSize = $footerConfig['name_font_size'] ?? 30;

        $this->renderAutoFitText(
            $canvas,
            $displayName,
            $textStartX,
            $nameY,
            $maxTextW,
            $nameFontSize,
            16,
            $fontHindiBold,
            $nameColor,
            'left'
        );

        // 5. Designation (Slate / Party secondary)
        if (!empty($branding->designation)) {
            $desigText = $this->shapeDevanagari($branding->designation);
            $desigY = $nameY + 28;
            $desigColor = $footerConfig['desig_color'] ?? '#475569';
            $this->renderAutoFitText(
                $canvas,
                $desigText,
                $textStartX,
                $desigY,
                $maxTextW,
                17,
                12,
                $fontHindiRegular,
                $desigColor,
                'left'
            );
        }

        // 6. Social Handles Row at Bottom
        $socialTopY = 1035;
        $socialH = 45;
        $canvas->drawRectangle(0, $socialTopY, function ($rect) use ($socialH) {
            $rect->size(1080, $socialH);
            $rect->background('#ffffff');
        });

        // Thin separator
        $nativeGd = $canvas->core()->native();
        $sepAlloc = imagecolorallocate($nativeGd, 235, 235, 235);
        imageline($nativeGd, 0, $socialTopY, 1080, $socialTopY, $sepAlloc);

        $this->renderSocialIconsRow(
            $canvas,
            $branding,
            30,
            $socialTopY + 10,
            $socialTopY + 28,
            $fontEngRegular,
            '#333333'
        );
    }

    /**
     * Layer 8: Saffron Wave Banner (Dynamic Diagonal Wave & Tricolor Accent)
     * - Dynamic angled banner sloping from left (y: 918) to right (y: 942) over candidate lower chest.
     * - Top white/gold highlight line along diagonal edge.
     * - Indian Tricolor 3-color vertical accent block on left edge.
     * - Circular party emblem with white ring.
     * - Candidate Name in crisp bold white text + Designation in warm golden yellow.
     * - Full-width clean white social handles strip.
     */
    protected function renderSaffronWaveBannerFooter(
        ImageInterface $canvas,
        array $footerConfig,
        UserBranding $branding,
        ?PoliticalParty $party,
        array $candidateConfig = []
    ): void {
        $fontHindiBold = $this->getFontPath('hindi_bold');
        $fontHindiRegular = $this->getFontPath('hindi_regular');
        $fontEngRegular = $this->getFontPath('english_regular');

        $primaryColor = $footerConfig['primary_color'] ?? ($party?->primary_color ?: '#FF9933');
        $nameColor = $footerConfig['name_color'] ?? '#ffffff';
        $desigColor = $footerConfig['desig_color'] ?? '#FFF275';

        $nativeGd = $canvas->core()->native();

        $leftY = 918;
        $rightY = 942;
        $bottomY = 1036;

        // 1. Dynamic Diagonal Saffron Wave Banner (imagefilledpolygon)
        $pHex = ltrim($primaryColor, '#');
        $pr = hexdec(substr($pHex, 0, 2));
        $pg = hexdec(substr($pHex, 2, 2));
        $pb = hexdec(substr($pHex, 4, 2));
        $waveAlloc = imagecolorallocate($nativeGd, $pr, $pg, $pb);

        $wavePoints = [
            0, $leftY,
            1080, $rightY,
            1080, $bottomY,
            0, $bottomY,
        ];
        imagefilledpolygon($nativeGd, $wavePoints, $waveAlloc);

        // Top Highlight line along diagonal
        $hiAlloc = imagecolorallocatealpha($nativeGd, 255, 255, 255, 50);
        imageline($nativeGd, 0, $leftY, 1080, $rightY, $hiAlloc);
        imageline($nativeGd, 0, $leftY + 1, 1080, $rightY + 1, $hiAlloc);

        // 2. Vertical Indian Tricolor accent strip on left edge (x: 18)
        $triX = 18;
        $canvas->drawRectangle($triX, $leftY + 12, function ($rect) {
            $rect->size(6, 26);
            $rect->background('#FF9933');
        });
        $canvas->drawRectangle($triX, $leftY + 38, function ($rect) {
            $rect->size(6, 26);
            $rect->background('#ffffff');
        });
        $canvas->drawRectangle($triX, $leftY + 64, function ($rect) {
            $rect->size(6, 26);
            $rect->background('#138808');
        });

        // 3. Circular Party Logo on Left
        $logoPath = $party?->symbol_logo ?: 'images/parties/bjp.jpeg';
        $resolvedLogo = $this->resolveFilePath($logoPath);
        $logoSize = $footerConfig['logo_size'] ?? 64;
        $logoX = $triX + 18;
        $logoY = $leftY + 18;

        if ($resolvedLogo && file_exists($resolvedLogo)) {
            try {
                $logoImg = $this->manager->read($resolvedLogo)->cover($logoSize, $logoSize);
                $logoImg = $this->makeCircularAvatar($logoImg, $logoSize, '#ffffff', 2);
                $canvas->place($logoImg, 'top-left', $logoX, $logoY);
            } catch (\Throwable $e) {}
        }

        $textStartX = $logoX + $logoSize + 16;
        $maxTextW = 540;

        // 4. Candidate Name (Bold White Auto-Fit)
        $displayName = $this->shapeDevanagari($branding->display_name);
        $nameY = $leftY + 44;
        $nameFontSize = $footerConfig['name_font_size'] ?? 32;

        $this->renderAutoFitText(
            $canvas,
            $displayName,
            $textStartX,
            $nameY,
            $maxTextW,
            $nameFontSize,
            16,
            $fontHindiBold,
            $nameColor,
            'left'
        );

        // 5. Designation (Warm Yellow Auto-Fit)
        if (!empty($branding->designation)) {
            $desigText = $this->shapeDevanagari($branding->designation);
            $desigY = $nameY + 28;
            $this->renderAutoFitText(
                $canvas,
                $desigText,
                $textStartX,
                $desigY,
                $maxTextW,
                18,
                12,
                $fontHindiRegular,
                $desigColor,
                'left'
            );
        }

        // 6. Clean White Social Media Strip at Bottom (y: 1036 to 1080)
        $socialY = $bottomY;
        $socialH = 1080 - $socialY;

        $canvas->drawRectangle(0, $socialY, function ($rect) use ($socialH) {
            $rect->size(1080, $socialH);
            $rect->background('#ffffff');
        });

        // Thin separator line
        $sepAlloc = imagecolorallocate($nativeGd, 230, 230, 230);
        imageline($nativeGd, 0, $socialY, 1080, $socialY, $sepAlloc);

        $this->renderSocialIconsRow(
            $canvas,
            $branding,
            25,
            $socialY + 10,
            $socialY + 28,
            $fontEngRegular,
            '#333333'
        );
    }

    /**
     * Layer 9: Tricolor Angled Blade (Dynamic Patriotic Sash & Sharp Contrast)
     * - Slanted 3-tier tricolor ribbon blade across lower torso.
     * - Top Saffron polygon with candidate Name in pure bold white.
     * - Middle White polygon with candidate Designation in deep navy blue.
     * - Bottom Green band providing the tricolor completion.
     * - Clean crisp white bottom social strip with branded icons.
     * - Circular party logo on left at x: 24, y: 914.
     */
    protected function renderTricolorAngledBladeFooter(
        ImageInterface $canvas,
        array $footerConfig,
        UserBranding $branding,
        ?PoliticalParty $party,
        array $candidateConfig = []
    ): void {
        $fontHindiBold = $this->getFontPath('hindi_bold');
        $fontHindiRegular = $this->getFontPath('hindi_regular');
        $fontEngRegular = $this->getFontPath('english_regular');

        $saffronColor = $footerConfig['primary_color'] ?? ($party?->primary_color ?: '#FF8800');
        $nameColor = $footerConfig['name_color'] ?? '#ffffff';
        $desigColor = $footerConfig['desig_color'] ?? '#0A2540'; // Deep Royal Navy for stark contrast on white

        $nativeGd = $canvas->core()->native();

        // 1. Top Saffron Angled Blade Polygon
        $sHex = ltrim($saffronColor, '#');
        $sr = hexdec(substr($sHex, 0, 2));
        $sg = hexdec(substr($sHex, 2, 2));
        $sb = hexdec(substr($sHex, 4, 2));
        $saffronAlloc = imagecolorallocate($nativeGd, $sr, $sg, $sb);

        // Blade slopes across candidate chest
        $saffronPoints = [
            0, 904,
            775, 920,
            730, 966,
            0, 966,
        ];
        imagefilledpolygon($nativeGd, $saffronPoints, $saffronAlloc);

        // Top Gold Highlight Line on Saffron Blade
        $goldHiAlloc = imagecolorallocate($nativeGd, 255, 215, 0);
        imageline($nativeGd, 0, 904, 775, 920, $goldHiAlloc);
        imageline($nativeGd, 0, 905, 775, 921, $goldHiAlloc);

        // 2. Middle Pure White Angled Blade Polygon
        $whiteAlloc = imagecolorallocate($nativeGd, 255, 255, 255);
        $whitePoints = [
            0, 966,
            730, 966,
            685, 1014,
            0, 1014,
        ];
        imagefilledpolygon($nativeGd, $whitePoints, $whiteAlloc);

        // Angled right edge highlight on white blade
        $saffEdge = imagecolorallocatealpha($nativeGd, $sr, $sg, $sb, 40);
        imageline($nativeGd, 730, 966, 685, 1014, $saffEdge);

        // 3. Bottom Emerald Green Ribbon
        $greenAlloc = imagecolorallocate($nativeGd, 19, 136, 8); // India Green #138808
        imagefilledrectangle($nativeGd, 0, 1014, 1080, 1040, $greenAlloc);

        // Thin gold separator between green & white
        $goldSepAlloc = imagecolorallocate($nativeGd, 255, 215, 0);
        imageline($nativeGd, 0, 1014, 685, 1014, $goldSepAlloc);

        // 4. Circular Party Logo on Left (Anchored across Saffron & White blade)
        $logoPath = $party?->symbol_logo ?: 'images/parties/bjp.jpeg';
        $resolvedLogo = $this->resolveFilePath($logoPath);
        $logoSize = $footerConfig['logo_size'] ?? 68;
        $logoX = 24;
        $logoY = 918;

        if ($resolvedLogo && file_exists($resolvedLogo)) {
            try {
                $logoImg = $this->manager->read($resolvedLogo)->cover($logoSize, $logoSize);
                $logoImg = $this->makeCircularAvatar($logoImg, $logoSize, '#ffffff', 3);
                $canvas->place($logoImg, 'top-left', $logoX, $logoY);
            } catch (\Throwable $e) {}
        }

        $textStartX = $logoX + $logoSize + 16;
        $maxTextW = 560;

        // 5. Candidate Name on Saffron Band (Bold Crisp White)
        $displayName = $this->shapeDevanagari($branding->display_name);
        $nameY = 948;
        $nameFontSize = $footerConfig['name_font_size'] ?? 32;

        // Subtle dark shadow for 3D punch
        $shadowAlloc = imagecolorallocatealpha($nativeGd, 40, 20, 0, 60);
        imagettftext($nativeGd, $nameFontSize, 0, $textStartX + 2, $nameY + 2, $shadowAlloc, $fontHindiBold, $displayName);

        $this->renderAutoFitText(
            $canvas,
            $displayName,
            $textStartX,
            $nameY,
            $maxTextW,
            $nameFontSize,
            18,
            $fontHindiBold,
            $nameColor,
            'left'
        );

        // 6. Designation on White Band (Deep Navy Bold)
        if (!empty($branding->designation)) {
            $desigText = $this->shapeDevanagari($branding->designation);
            $desigY = 998;
            $this->renderAutoFitText(
                $canvas,
                $desigText,
                $textStartX,
                $desigY,
                $maxTextW,
                20,
                13,
                $fontHindiBold,
                $desigColor,
                'left'
            );
        }

        // 7. Clean Social Media Row at Bottom (y: 1040 to 1080)
        $canvas->drawRectangle(0, 1040, function ($rect) {
            $rect->size(1080, 40);
            $rect->background('#ffffff');
        });

        $this->renderSocialIconsRow(
            $canvas,
            $branding,
            24,
            1048,
            1066,
            $fontEngRegular,
            '#1e293b'
        );
    }

    /**
     * Layer 10: Royal Maroon Heritage (Temple Vermilion & 24K Gold Filigree)
     * - Auspicious temple maroon card (y: 915 to 1080).
     * - Intricate 24K gold filigree jewel border along top edge (interspaced diamond & bead pattern).
     * - Circular party emblem with dual gold ring border on left.
     * - Candidate Name in 24K Gilded Champagne Gold with 3D embossed shadow.
     * - Designation in Pearl Ivory.
     * - Bottom velvet maroon social strip with 1px gold separator and bright champagne icons.
     */
    protected function renderRoyalMaroonHeritageFooter(
        ImageInterface $canvas,
        array $footerConfig,
        UserBranding $branding,
        ?PoliticalParty $party,
        array $candidateConfig = []
    ): void {
        $fontHindiBold = $this->getFontPath('hindi_bold');
        $fontHindiRegular = $this->getFontPath('hindi_regular');
        $fontEngRegular = $this->getFontPath('english_regular');

        $goldColor = '#D4AF37'; // 24K Antique Gold
        $cardBg = $footerConfig['card_bg'] ?? '#6B0F1A'; // Sacred Temple Maroon
        $nameColor = $footerConfig['name_color'] ?? '#FFE66D';
        $desigColor = $footerConfig['desig_color'] ?? '#F5EBE1'; // Pearl Ivory

        $cardTopY = 915;
        $cardHeight = 165;
        $nativeGd = $canvas->core()->native();

        // 1. Solid Deep Temple Maroon Card
        $canvas->drawRectangle(0, $cardTopY, function ($rect) use ($cardBg, $cardHeight) {
            $rect->size(1080, $cardHeight);
            $rect->background($cardBg);
        });

        // 2. 24K Gold Filigree Jewel Top Border
        $goldAlloc = imagecolorallocate($nativeGd, 212, 175, 55);       // #D4AF37
        $brightGoldAlloc = imagecolorallocate($nativeGd, 255, 230, 109); // #FFE66D

        // Top solid gold line (3px)
        imagefilledrectangle($nativeGd, 0, $cardTopY, 1080, $cardTopY + 2, $goldAlloc);

        // Filigree jewel row (interspaced diamonds & beads)
        for ($x = 8; $x < 1075; $x += 18) {
            $diamondPoints = [
                $x, $cardTopY + 6,
                $x + 4, $cardTopY + 3,
                $x + 8, $cardTopY + 6,
                $x + 4, $cardTopY + 9,
            ];
            imagefilledpolygon($nativeGd, $diamondPoints, $brightGoldAlloc);
            imagefilledellipse($nativeGd, $x + 13, $cardTopY + 6, 2, 2, $goldAlloc);
        }

        // Secondary gold line (2px)
        imagefilledrectangle($nativeGd, 0, $cardTopY + 11, 1080, $cardTopY + 12, $goldAlloc);

        // 3. Circular Party Emblem with Dual Gold Ring
        $logoPath = $party?->symbol_logo ?: 'images/parties/bjp.jpeg';
        $resolvedLogo = $this->resolveFilePath($logoPath);
        $logoSize = $footerConfig['logo_size'] ?? 68;
        $logoX = 28;
        $logoY = $cardTopY + 20;

        if ($resolvedLogo && file_exists($resolvedLogo)) {
            try {
                $logoImg = $this->manager->read($resolvedLogo)->cover($logoSize, $logoSize);
                $logoImg = $this->makeCircularAvatar($logoImg, $logoSize, '#D4AF37', 3);
                $canvas->place($logoImg, 'top-left', $logoX, $logoY);
            } catch (\Throwable $e) {}
        }

        $textStartX = $logoX + $logoSize + 18;
        $maxTextW = 550;

        // 4. Candidate Name (24K Gilded Gold with 3D Embossed Effect)
        $displayName = $this->shapeDevanagari($branding->display_name);
        $nameY = $cardTopY + 56;
        $nameFontSize = $footerConfig['name_font_size'] ?? 34;

        // Dark maroon drop shadow for 3D gold embossed appearance
        $maroonShadowAlloc = imagecolorallocate($nativeGd, 50, 4, 8);
        imagettftext($nativeGd, $nameFontSize, 0, $textStartX + 2, $nameY + 2, $maroonShadowAlloc, $fontHindiBold, $displayName);

        $this->renderAutoFitText(
            $canvas,
            $displayName,
            $textStartX,
            $nameY,
            $maxTextW,
            $nameFontSize,
            18,
            $fontHindiBold,
            $nameColor,
            'left'
        );

        // 5. Designation (Pearl Ivory)
        if (!empty($branding->designation)) {
            $desigText = $this->shapeDevanagari($branding->designation);
            $desigY = $nameY + 30;
            $this->renderAutoFitText(
                $canvas,
                $desigText,
                $textStartX,
                $desigY,
                $maxTextW,
                18,
                12,
                $fontHindiRegular,
                $desigColor,
                'left'
            );
        }

        // 6. Deep Velvet Maroon Social Bar at Bottom (y: 1032 to 1080)
        $socialY = 1032;
        $socialH = 1080 - $socialY;

        $canvas->drawRectangle(0, $socialY, function ($rect) use ($socialH) {
            $rect->size(1080, $socialH);
            $rect->background('#3D050E');
        });

        // 1px gold separator line
        imagefilledrectangle($nativeGd, 0, $socialY, 1080, $socialY, $goldAlloc);

        $this->renderSocialIconsRow(
            $canvas,
            $branding,
            28,
            $socialY + 9,
            $socialY + 27,
            $fontEngRegular,
            '#FFE66D'
        );
    }

    /**
     * Render bottom row of social media icons (FB, IG, WA/Phone).
     */
    protected function renderSocialIconsRow(
        ImageInterface $canvas,
        UserBranding $branding,
        int $startX,
        int $pillY,
        int $textY,
        string $fontPath,
        string $textColor = '#333333'
    ): void {
        $currX = $startX;

        if ($branding->facebook_handle) {
            $this->drawSocialIconPill($canvas, $currX, $pillY, 'fb', '#1877F2');
            $canvas->text($branding->facebook_handle, $currX + 28, $textY, function ($font) use ($fontPath, $textColor) {
                $font->file($fontPath);
                $font->size(14);
                $font->color($textColor);
            });
            $currX += 290;
        }

        if ($branding->instagram_handle) {
            $this->drawSocialIconPill($canvas, $currX, $pillY, 'ig', '#E4405F');
            $canvas->text($branding->instagram_handle, $currX + 28, $textY, function ($font) use ($fontPath, $textColor) {
                $font->file($fontPath);
                $font->size(14);
                $font->color($textColor);
            });
            $currX += 290;
        }

        if ($branding->phone_number) {
            $this->drawSocialIconPill($canvas, $currX, $pillY, 'wa', '#25D366');
            $canvas->text($branding->phone_number, $currX + 28, $textY, function ($font) use ($fontPath, $textColor) {
                $font->file($fontPath);
                $font->size(14);
                $font->color($textColor);
            });
        }
    }

    /**
     * Style: Standard Political Footer — MATCHES REFERENCE EXACTLY
     * Layout (1080x1080 canvas):
     *   y: 910–975  → Main bar: Left=BJP Lotus + Party Name, Center/Right=Candidate Name + Designation
     *   y: 975–980  → Decorative tricolor line (saffron-white-green)
     *   y: 980–1080 → Social handles bar (white bg, icons + handles)
     */
    protected function renderStandardPoliticalFooter(
        ImageInterface $canvas,
        array $footerConfig,
        UserBranding $branding,
        ?PoliticalParty $party,
        array $candidateConfig = []
    ): void {
        $fontHindiBold = $this->getFontPath('hindi_bold');
        $fontHindiRegular = $this->getFontPath('hindi_regular');
        $fontEngRegular = $this->getFontPath('english_regular');

        $footerTop = $footerConfig['footer_top_y'] ?? 910;
        $mainBarHeight = $footerConfig['main_bar_height'] ?? 65;
        $tricolorHeight = 5;
        $socialBarHeight = $footerConfig['social_bar_height'] ?? 45;

        $candidatePos = $candidateConfig['position'] ?? 'bottom-right';
        $isCandidateLeft = ($candidatePos === 'bottom-left');

        // ---- 1. Main Bar Background (warm light cream) ----
        $mainBarBg = $footerConfig['main_bar_bg'] ?? '#f8f5ee';
        $canvas->drawRectangle(0, $footerTop, function ($rect) use ($mainBarBg, $mainBarHeight) {
            $rect->size(1080, $mainBarHeight);
            $rect->background($mainBarBg);
        });

        // ---- 2. Party Logo & Name Placement ----
        $logoPath = $party?->symbol_logo ?: 'images/parties/bjp.jpeg';
        $resolvedLogo = $this->resolveFilePath($logoPath);
        $partyName = $this->shapeDevanagari($party?->hindi_name ?: ($party?->name ?: 'भारतीय जनता पार्टी'));
        $logoSize = $footerConfig['footer_logo_size'] ?? 52;

        if (!$isCandidateLeft) {
            // Standard: Party Logo on Left (x: 20)
            $partyTextX = 25;
            if ($resolvedLogo && file_exists($resolvedLogo)) {
                try {
                    $logoImg = $this->manager->read($resolvedLogo)->cover($logoSize, $logoSize);
                    $logoImg = $this->makeCircularAvatar($logoImg, $logoSize, '#ffffff', 2);
                    $logoY = $footerTop + (int)(($mainBarHeight - $logoSize) / 2);
                    $canvas->place($logoImg, 'top-left', 20, $logoY);
                    $partyTextX = 20 + $logoSize + 12;
                } catch (\Throwable $e) {}
            }

            $partyNameY = $footerTop + (int)($mainBarHeight / 2) + 8;
            $canvas->text($partyName, $partyTextX, $partyNameY, function ($font) use ($fontHindiBold) {
                $font->file($fontHindiBold);
                $font->size(22);
                $font->color('#2d3748');
            });

            // Candidate Name in Middle (x: 400 to 720, candidate photo is at 720+)
            $nameX = $footerConfig['name_x'] ?? 415;
            $nameY = $footerTop + 33;
            $nameSize = $footerConfig['name_font_size'] ?? 30;
            $nameColor = $footerConfig['name_color'] ?? '#c0392b';
            $maxNameWidth = $footerConfig['max_name_width'] ?? 310;

            $displayName = $this->shapeDevanagari($branding->display_name);
            $this->renderAutoFitText(
                $canvas,
                $displayName,
                $nameX,
                $nameY,
                $maxNameWidth,
                $nameSize,
                16,
                $fontHindiBold,
                $nameColor,
                $footerConfig['name_align'] ?? 'left'
            );

            if (!empty($branding->designation)) {
                $desigText = $this->shapeDevanagari($branding->designation);
                $desigY = $nameY + 27;
                $desigColor = $footerConfig['desig_color'] ?? '#4a5568';
                $this->renderAutoFitText(
                    $canvas,
                    $desigText,
                    $nameX,
                    $desigY,
                    $maxNameWidth,
                    17,
                    12,
                    $fontHindiRegular,
                    $desigColor,
                    $footerConfig['name_align'] ?? 'left'
                );
            }
        } else {
            // Reverse: Candidate on Left -> Party Logo on Right
            if ($resolvedLogo && file_exists($resolvedLogo)) {
                try {
                    $logoImg = $this->manager->read($resolvedLogo)->cover($logoSize, $logoSize);
                    $logoImg = $this->makeCircularAvatar($logoImg, $logoSize, '#ffffff', 2);
                    $logoX = 1080 - $logoSize - 20;
                    $logoY = $footerTop + (int)(($mainBarHeight - $logoSize) / 2);
                    $canvas->place($logoImg, 'top-left', $logoX, $logoY);
                } catch (\Throwable $e) {}
            }

            // Candidate Name (x: 420 to 980)
            $nameX = 420;
            $nameY = $footerTop + 33;
            $nameColor = $footerConfig['name_color'] ?? '#c0392b';
            $maxNameWidth = $footerConfig['max_name_width'] ?? 520;

            $displayName = $this->shapeDevanagari($branding->display_name);
            $this->renderAutoFitText(
                $canvas,
                $displayName,
                $nameX,
                $nameY,
                $maxNameWidth,
                30,
                16,
                $fontHindiBold,
                $nameColor,
                $footerConfig['name_align'] ?? 'left'
            );

            if (!empty($branding->designation)) {
                $desigText = $this->shapeDevanagari($branding->designation);
                $desigColor = $footerConfig['desig_color'] ?? '#4a5568';
                $this->renderAutoFitText(
                    $canvas,
                    $desigText,
                    $nameX,
                    $nameY + 27,
                    $maxNameWidth,
                    17,
                    12,
                    $fontHindiRegular,
                    $desigColor,
                    $footerConfig['name_align'] ?? 'left'
                );
            }
        }

        // ---- 3. Tricolor Decorative Line ----
        $triY = $footerTop + $mainBarHeight;
        $thirdWidth = 360;

        $canvas->drawRectangle(0, $triY, function ($rect) use ($thirdWidth, $tricolorHeight) {
            $rect->size($thirdWidth, $tricolorHeight);
            $rect->background('#FF9933'); // Saffron
        });
        $canvas->drawRectangle($thirdWidth, $triY, function ($rect) use ($thirdWidth, $tricolorHeight) {
            $rect->size($thirdWidth, $tricolorHeight);
            $rect->background('#ffffff'); // White
        });
        $canvas->drawRectangle($thirdWidth * 2, $triY, function ($rect) use ($thirdWidth, $tricolorHeight) {
            $rect->size($thirdWidth, $tricolorHeight);
            $rect->background('#138808'); // Green
        });

        // ---- 4. Social Handles Bar ----
        $socialBarY = $triY + $tricolorHeight;
        $canvas->drawRectangle(0, $socialBarY, function ($rect) use ($socialBarHeight) {
            $rect->size(1080, $socialBarHeight);
            $rect->background('#ffffff');
        });

        $startX = 25;
        $socialTextY = $socialBarY + 30;

        // Facebook
        if ($branding->facebook_handle) {
            $this->drawSocialIconPill($canvas, $startX, $socialBarY + 10, 'fb', '#1877F2');
            $canvas->text($branding->facebook_handle, $startX + 28, $socialTextY, function ($font) use ($fontEngRegular) {
                $font->file($fontEngRegular);
                $font->size(14);
                $font->color('#444444');
            });
            $startX += 290;
        }

        // Instagram
        if ($branding->instagram_handle) {
            $this->drawSocialIconPill($canvas, $startX, $socialBarY + 10, 'ig', '#E4405F');
            $canvas->text($branding->instagram_handle, $startX + 28, $socialTextY, function ($font) use ($fontEngRegular) {
                $font->file($fontEngRegular);
                $font->size(14);
                $font->color('#444444');
            });
            $startX += 290;
        }

        // WhatsApp / Phone
        if ($branding->phone_number) {
            $this->drawSocialIconPill($canvas, $startX, $socialBarY + 10, 'wa', '#25D366');
            $canvas->text($branding->phone_number, $startX + 28, $socialTextY, function ($font) use ($fontEngRegular) {
                $font->file($fontEngRegular);
                $font->size(14);
                $font->color('#444444');
            });
        }
    }

    /**
     * Style A: Dual Ribbon Footer (saffron name + green designation style)
     */
    protected function renderDualRibbonFooter(
        ImageInterface $canvas,
        array $footerConfig,
        UserBranding $branding,
        ?PoliticalParty $party
    ): void {
        $fontHindiBold = $this->getFontPath('hindi_bold');
        $fontHindiRegular = $this->getFontPath('hindi_regular');
        $fontEngRegular = $this->getFontPath('english_regular');

        $desigColor = $footerConfig['desig_bg'] ?? ($party?->secondary_color ?: '#00873E');
        $nameColor = $footerConfig['name_bg'] ?? ($party?->primary_color ?: '#FF9933');

        // 1. Designation Ribbon
        if (!empty($branding->designation)) {
            $canvas->drawRectangle(0, 942, function ($rect) use ($desigColor) {
                $rect->size(540, 48);
                $rect->background($desigColor);
            });

            $desigText = $this->shapeDevanagari(Str::limit($branding->designation, 42));
            $canvas->text($desigText, 25, 974, function ($font) use ($fontHindiRegular) {
                $font->file($fontHindiRegular);
                $font->size(20);
                $font->color('#ffffff');
            });
        }

        // 2. Name Ribbon
        $nameBoxW = 520;
        $nameBoxH = 55;
        $nameX = 540;
        $canvas->drawRectangle($nameX, 935, function ($rect) use ($nameColor, $nameBoxW, $nameBoxH) {
            $rect->size($nameBoxW, $nameBoxH);
            $rect->background($nameColor);
        });

        $displayName = $this->shapeDevanagari($branding->display_name);
        $canvas->text($displayName, $nameX + 30, 972, function ($font) use ($fontHindiBold) {
            $font->file($fontHindiBold);
            $font->size(26);
            $font->color('#ffffff');
        });

        // 3. Social Media Handle Bar
        $canvas->drawRectangle(0, 990, function ($rect) {
            $rect->size(1080, 90);
            $rect->background('#ffffff');
        });

        $startX = 30;
        $socialY = 1045;

        if ($branding->facebook_handle) {
            $this->drawSocialIconPill($canvas, $startX, $socialY - 26, 'fb', '#1877F2');
            $canvas->text($branding->facebook_handle, $startX + 30, $socialY - 8, function ($font) use ($fontEngRegular) {
                $font->file($fontEngRegular);
                $font->size(17);
                $font->color('#333333');
            });
            $startX += 280;
        }

        if ($branding->instagram_handle) {
            $this->drawSocialIconPill($canvas, $startX, $socialY - 26, 'ig', '#E4405F');
            $canvas->text($branding->instagram_handle, $startX + 30, $socialY - 8, function ($font) use ($fontEngRegular) {
                $font->file($fontEngRegular);
                $font->size(17);
                $font->color('#333333');
            });
            $startX += 280;
        }

        if ($branding->phone_number) {
            $this->drawSocialIconPill($canvas, $startX, $socialY - 26, 'wa', '#25D366');
            $canvas->text($branding->phone_number, $startX + 30, $socialY - 8, function ($font) use ($fontEngRegular) {
                $font->file($fontEngRegular);
                $font->size(17);
                $font->color('#333333');
            });
        }
    }

    /**
     * Style B: Royal Bar Footer (Maroon/Red Bar with Gold text)
     */
    protected function renderRoyalBarFooter(
        ImageInterface $canvas,
        array $footerConfig,
        UserBranding $branding,
        ?PoliticalParty $party
    ): void {
        $fontHindiBold = $this->getFontPath('hindi_bold');
        $fontHindiRegular = $this->getFontPath('hindi_regular');
        $fontEngRegular = $this->getFontPath('english_regular');

        $barBg = $footerConfig['bar_bg'] ?? '#7A0C0E';
        $goldColor = '#FFE57F';

        // 1. Deep Colored Maroon Main Bar
        $canvas->drawRectangle(0, 890, function ($rect) use ($barBg) {
            $rect->size(1080, 140);
            $rect->background($barBg);
        });

        // 2. Circular Party Logo on Left
        $logoPath = $party?->symbol_logo ?: 'images/parties/bjp.jpeg';
        $resolvedLogo = $this->resolveFilePath($logoPath);
        $textStartX = 180;

        if ($resolvedLogo && file_exists($resolvedLogo)) {
            try {
                $logoSize = 100;
                $logoImg = $this->manager->read($resolvedLogo)->cover($logoSize, $logoSize);
                $logoImg = $this->makeCircularAvatar($logoImg, $logoSize, '#ffffff', 4);
                $canvas->place($logoImg, 'top-left', 35, 910);
            } catch (\Throwable $e) {
                $textStartX = 40;
            }
        } else {
            $textStartX = 40;
        }

        // 3. Name Text in Gold
        $displayName = $this->shapeDevanagari($branding->display_name);
        $canvas->text($displayName, $textStartX, 955, function ($font) use ($fontHindiBold, $goldColor) {
            $font->file($fontHindiBold);
            $font->size(34);
            $font->color($goldColor);
        });

        // 4. Designation Text
        if (!empty($branding->designation)) {
            $desigFormatted = $this->shapeDevanagari('(' . $branding->designation . ')');
            $canvas->text($desigFormatted, $textStartX, 995, function ($font) use ($fontHindiRegular) {
                $font->file($fontHindiRegular);
                $font->size(22);
                $font->color('#ffffff');
            });
        }

        // 5. Clean White Bottom Strip with Social Handles
        $canvas->drawRectangle(0, 1030, function ($rect) {
            $rect->size(1080, 50);
            $rect->background('#ffffff');
        });

        $startX = 35;
        $socialY = 1065;

        if ($branding->facebook_handle) {
            $this->drawSocialIconPill($canvas, $startX, $socialY - 20, 'fb', '#1877F2');
            $canvas->text($branding->facebook_handle, $startX + 28, $socialY - 4, function ($font) use ($fontEngRegular) {
                $font->file($fontEngRegular);
                $font->size(15);
                $font->color('#444444');
            });
            $startX += 270;
        }

        if ($branding->instagram_handle) {
            $this->drawSocialIconPill($canvas, $startX, $socialY - 20, 'ig', '#E4405F');
            $canvas->text($branding->instagram_handle, $startX + 28, $socialY - 4, function ($font) use ($fontEngRegular) {
                $font->file($fontEngRegular);
                $font->size(15);
                $font->color('#444444');
            });
            $startX += 270;
        }

        if ($branding->phone_number) {
            $this->drawSocialIconPill($canvas, $startX, $socialY - 20, 'wa', '#25D366');
            $canvas->text($branding->phone_number, $startX + 28, $socialY - 4, function ($font) use ($fontEngRegular) {
                $font->file($fontEngRegular);
                $font->size(15);
                $font->color('#444444');
            });
        }
    }

    // ================================================================
    // HELPER METHODS
    // ================================================================

    /**
     * Shape Devanagari text for GD FreeType rendering.
     * Moves pre-base matra \u093F (chhoti ee) before its consonant cluster.
     */
    protected function shapeDevanagari(?string $text): string
    {
        if (empty($text)) {
            return '';
        }

        $matraI = mb_chr(0x093F, 'UTF-8');
        $pattern = "/([\x{0915}-\x{0939}](?:\x{094D}[\x{0915}-\x{0939}])*)\x{093F}/u";

        return preg_replace($pattern, $matraI . '$1', $text);
    }

    /**
     * Draw small circular social media platform badge (FB, IG, WA)
     */
    protected function drawSocialIconPill(ImageInterface $canvas, int $x, int $y, string $type, string $color): void
    {
        $radius = 11;
        $canvas->drawCircle($x + $radius, $y + $radius, function ($circle) use ($radius, $color) {
            $circle->radius($radius);
            $circle->background($color);
        });

        $fontEngBold = $this->getFontPath('english_bold');
        $letter = match ($type) {
            'fb' => 'f',
            'ig' => 'ig',
            'wa' => 'w',
            default => 's',
        };

        $canvas->text($letter, $x + ($type === 'ig' ? 4 : 7), $y + 16, function ($font) use ($fontEngBold) {
            $font->file($fontEngBold);
            $font->size(12);
            $font->color('#ffffff');
        });
    }

    /**
     * Auto-fitting dynamic text renderer.
     * Computes the exact text bounding box with imagettfbbox and smoothly scales down font size
     * so that politician names and designations never overflow or clip, regardless of length.
     */
    protected function renderAutoFitText(
        ImageInterface $canvas,
        string $text,
        int $startX,
        int $baselineY,
        int $maxWidth,
        int $maxFontSize,
        int $minFontSize,
        string $fontPath,
        string $color = '#2d3748',
        string $align = 'left'
    ): int {
        if (empty($text)) {
            return $maxFontSize;
        }

        $size = $maxFontSize;
        if (file_exists($fontPath) && function_exists('imagettfbbox')) {
            while ($size > $minFontSize) {
                $bbox = @imagettfbbox($size, 0, $fontPath, $text);
                if ($bbox) {
                    $width = abs($bbox[2] - $bbox[0]);
                    if ($width <= $maxWidth) {
                        break;
                    }
                }
                $size -= 1;
            }
        }

        $finalX = $startX;
        if ($align === 'center' && file_exists($fontPath) && function_exists('imagettfbbox')) {
            $bbox = @imagettfbbox($size, 0, $fontPath, $text);
            $width = $bbox ? abs($bbox[2] - $bbox[0]) : 0;
            $finalX = $startX + (int) (($maxWidth - $width) / 2);
        } elseif ($align === 'right' && file_exists($fontPath) && function_exists('imagettfbbox')) {
            $bbox = @imagettfbbox($size, 0, $fontPath, $text);
            $width = $bbox ? abs($bbox[2] - $bbox[0]) : 0;
            $finalX = $startX + $maxWidth - $width;
        }

        $canvas->text($text, $finalX, $baselineY, function ($font) use ($fontPath, $size, $color) {
            $font->file($fontPath);
            $font->size($size);
            $font->color($color);
        });

        return $size;
    }

    /**
     * Avatar cropper supporting 'circle', 'rounded_square', and 'square' shapes with customizable border.
     */
    protected function makeShapedAvatar(
        ImageInterface $image,
        int $size,
        string $shape = 'circle',
        string $borderColor = '#FF9933',
        int $borderWidth = 0
    ): ImageInterface {
        if ($shape === 'circle') {
            return $this->makeCircularAvatar($image, $size, $borderColor, $borderWidth);
        }

        $image->cover($size, $size);
        $srcGd = $image->core()->native();

        $destGd = imagecreatetruecolor($size, $size);
        imagealphablending($destGd, false);
        imagesavealpha($destGd, true);
        $transparent = imagecolorallocatealpha($destGd, 0, 0, 0, 127);
        imagefill($destGd, 0, 0, $transparent);

        // Parse border color
        $borderAlloc = null;
        if ($borderWidth > 0 && !empty($borderColor) && $borderColor !== 'none') {
            $hex = ltrim($borderColor, '#');
            if (strlen($hex) === 6) {
                $br = hexdec(substr($hex, 0, 2));
                $bg = hexdec(substr($hex, 2, 2));
                $bb = hexdec(substr($hex, 4, 2));
                $borderAlloc = imagecolorallocate($destGd, $br, $bg, $bb);
            }
        }

        // 1. Plain Square Avatar
        if ($shape === 'square') {
            $whiteBg = imagecolorallocate($destGd, 255, 255, 255);
            imagefill($destGd, 0, 0, $whiteBg);
            imagealphablending($destGd, true);
            imagecopyresampled($destGd, $srcGd, 0, 0, 0, 0, $size, $size, $size, $size);
            if ($borderAlloc !== null && $borderWidth > 0) {
                for ($b = 0; $b < $borderWidth; $b++) {
                    imagerectangle($destGd, $b, $b, $size - 1 - $b, $size - 1 - $b, $borderAlloc);
                }
            }
            $result = $this->manager->read($destGd);
            imagedestroy($destGd);
            return $result;
        }

        // 2. Rounded Square Avatar (with smooth corners and border)
        $cornerRadius = (int) ($size * 0.18);
        $innerSize = $size - ($borderWidth * 2);
        if ($innerSize < 10) $innerSize = $size;
        $innerRadius = max(4, $cornerRadius - $borderWidth);

        $innerGd = imagecreatetruecolor($innerSize, $innerSize);
        $whiteBg = imagecolorallocate($innerGd, 255, 255, 255);
        imagefill($innerGd, 0, 0, $whiteBg);
        imagealphablending($innerGd, true);
        imagecopyresampled($innerGd, $srcGd, 0, 0, 0, 0, $innerSize, $innerSize, $size, $size);

        if ($borderAlloc !== null && $borderWidth > 0) {
            imagealphablending($destGd, true);
            for ($x = 0; $x < $size; $x++) {
                for ($y = 0; $y < $size; $y++) {
                    if ($this->isInsideRoundedRect($x, $y, $size, $size, $cornerRadius)) {
                        imagesetpixel($destGd, $x, $y, $borderAlloc);
                    }
                }
            }
        }

        imagealphablending($destGd, true);
        for ($x = 0; $x < $innerSize; $x++) {
            for ($y = 0; $y < $innerSize; $y++) {
                if ($this->isInsideRoundedRect($x, $y, $innerSize, $innerSize, $innerRadius)) {
                    $color = imagecolorat($innerGd, $x, $y);
                    imagesetpixel($destGd, $x + $borderWidth, $y + $borderWidth, $color);
                }
            }
        }

        imagedestroy($innerGd);
        $result = $this->manager->read($destGd);
        imagedestroy($destGd);
        return $result;
    }

    /**
     * Check if (x, y) is inside rounded rectangle
     */
    protected function isInsideRoundedRect(int $x, int $y, int $w, int $h, int $r): bool
    {
        if ($x >= $r && $x < ($w - $r)) return true;
        if ($y >= $r && $y < ($h - $r)) return true;

        if ($x < $r && $y < $r) {
            $dx = $x - $r + 0.5;
            $dy = $y - $r + 0.5;
            return ($dx * $dx + $dy * $dy) <= ($r * $r);
        }
        if ($x >= ($w - $r) && $y < $r) {
            $dx = $x - ($w - $r) + 0.5;
            $dy = $y - $r + 0.5;
            return ($dx * $dx + $dy * $dy) <= ($r * $r);
        }
        if ($x < $r && $y >= ($h - $r)) {
            $dx = $x - $r + 0.5;
            $dy = $y - ($h - $r) + 0.5;
            return ($dx * $dx + $dy * $dy) <= ($r * $r);
        }
        if ($x >= ($w - $r) && $y >= ($h - $r)) {
            $dx = $x - ($w - $r) + 0.5;
            $dy = $y - ($h - $r) + 0.5;
            return ($dx * $dx + $dy * $dy) <= ($r * $r);
        }

        return false;
    }

    /**
     * Circular avatar cropper using GD alpha transparency.
     * FIXED: Uses $image->core()->native() compatible with Intervention Image v3 GD Driver.
     */
    protected function makeCircularAvatar(ImageInterface $image, int $size, string $borderColor = '#ffffff', int $borderWidth = 0): ImageInterface
    {
        $image->cover($size, $size);
        $srcGd = $image->core()->native();

        $totalSize = $size;
        $innerSize = $size - ($borderWidth * 2);
        if ($innerSize < 10) $innerSize = $size;

        $destGd = imagecreatetruecolor($totalSize, $totalSize);
        imagealphablending($destGd, false);
        imagesavealpha($destGd, true);
        $transparent = imagecolorallocatealpha($destGd, 0, 0, 0, 127);
        imagefill($destGd, 0, 0, $transparent);

        $radius = $totalSize / 2;

        // Draw border circle first
        if ($borderWidth > 0) {
            $br = hexdec(substr($borderColor, 1, 2));
            $bg = hexdec(substr($borderColor, 3, 2));
            $bb = hexdec(substr($borderColor, 5, 2));
            $borderAlloc = imagecolorallocate($destGd, $br, $bg, $bb);

            imagealphablending($destGd, true);
            for ($x = 0; $x < $totalSize; $x++) {
                for ($y = 0; $y < $totalSize; $y++) {
                    $dx = $x - $radius + 0.5;
                    $dy = $y - $radius + 0.5;
                    if (($dx * $dx + $dy * $dy) <= ($radius * $radius)) {
                        imagesetpixel($destGd, $x, $y, $borderAlloc);
                    }
                }
            }
        }

        // Draw inner image circle
        $innerRadius = $innerSize / 2;
        $innerGd = imagecreatetruecolor($innerSize, $innerSize);
        $whiteBg = imagecolorallocate($innerGd, 255, 255, 255);
        imagefill($innerGd, 0, 0, $whiteBg);
        imagealphablending($innerGd, true);
        imagecopyresampled($innerGd, $srcGd, 0, 0, 0, 0, $innerSize, $innerSize, $size, $size);

        for ($x = 0; $x < $innerSize; $x++) {
            for ($y = 0; $y < $innerSize; $y++) {
                $dx = $x - $innerRadius + 0.5;
                $dy = $y - $innerRadius + 0.5;
                if (($dx * $dx + $dy * $dy) <= ($innerRadius * $innerRadius)) {
                    $color = imagecolorat($innerGd, $x, $y);
                    imagesetpixel($destGd, $x + $borderWidth, $y + $borderWidth, $color);
                }
            }
        }

        imagedestroy($innerGd);

        $result = $this->manager->read($destGd);
        imagedestroy($destGd);

        return $result;
    }

    /**
     * Resolve font path with clean fallbacks.
     */
    protected function getFontPath(string $key): string
    {
        $map = [
            'hindi_bold'      => storage_path('fonts/NotoSansDevanagari-Bold.ttf'),
            'hindi_regular'   => storage_path('fonts/NotoSansDevanagari-Regular.ttf'),
            'english_bold'    => storage_path('fonts/Poppins-Bold.ttf'),
            'english_regular' => storage_path('fonts/Poppins-Regular.ttf'),
        ];

        $path = $map[$key] ?? $map['hindi_bold'];
        if (file_exists($path)) {
            return $path;
        }

        foreach ($map as $candidate) {
            if (file_exists($candidate)) {
                return $candidate;
            }
        }

        return $path;
    }

    /**
     * Resolve relative, storage, or public file path to an absolute path.
     */
    protected function resolveFilePath(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (file_exists($path)) {
            return $path;
        }

        $cleanPath = ltrim($path, '/\\');

        if (file_exists(public_path($cleanPath))) {
            return public_path($cleanPath);
        }

        if (file_exists(storage_path('app/public/' . $cleanPath))) {
            return storage_path('app/public/' . $cleanPath);
        }

        if (file_exists(storage_path($cleanPath))) {
            return storage_path($cleanPath);
        }

        return null;
    }

    /**
     * Get list of all available presets with metadata for UI dropdowns and admin selectors.
     */
    public function getAvailablePresets(): array
    {
        return [
            [
                'key'                => 'dual_ribbon_badge',
                'name'               => 'Layer 1: Dual Ribbon Badge (Orange & Green Box over Photo)',
                'description'        => 'Angled saffron ribbon badge across candidate photo with bold white text + green party strip on left + bottom social strip. Matches Bhai Dooj reference.',
                'badge'              => 'Layer 1 - Ribbon Badge',
                'leadership_mode'    => 'individual_chips',
                'candidate_position' => 'bottom-right',
                'candidate_shape'    => 'cutout',
                'footer_style'       => 'dual_ribbon_badge',
            ],
            [
                'key'                => 'solid_white_banner',
                'name'               => 'Layer 2: Solid White Banner (Ethnic Motif & Saffron Text)',
                'description'        => 'Full-width solid white card with ornamental ethnic top border, saffron bold name, flag logo. Matches Milkha Singh reference.',
                'badge'              => 'Layer 2 - White Card',
                'leadership_mode'    => 'individual_chips',
                'candidate_position' => 'bottom-right',
                'candidate_shape'    => 'cutout',
                'footer_style'       => 'solid_white_banner',
            ],
            [
                'key'                => 'floating_tricolor_badge',
                'name'               => 'Layer 3: Floating Tricolor Badge (Minimalist & Indian Flag Accent)',
                'description'        => 'Floating transparent branding directly on festival art with circular logo, bold text, tricolor divider, and bottom social strip. Matches Diwali reference.',
                'badge'              => 'Layer 3 - Floating Tricolor',
                'leadership_mode'    => 'individual_chips',
                'candidate_position' => 'bottom-right',
                'candidate_shape'    => 'cutout',
                'footer_style'       => 'floating_tricolor_badge',
            ],
            [
                'key'                => 'royal_gold_dock',
                'name'               => 'Layer 4: Royal Gold Banner (Deep Midnight Card & Gold Borders)',
                'description'        => 'Deep midnight card with dual golden filigree borders, circular party crest, gold typography, and luxury social strip. Perfect for Jayantis & official state posters.',
                'badge'              => 'Layer 4 - Royal Gold',
                'leadership_mode'    => 'individual_chips',
                'candidate_position' => 'bottom-right',
                'candidate_shape'    => 'cutout',
                'footer_style'       => 'royal_gold_dock',
            ],
            [
                'key'                => 'candidate_left_ribbon',
                'name'               => 'Layer 5: Candidate Left Ribbon (Mirrored for Right-Focus Artwork)',
                'description'        => 'Candidate cutout anchored on bottom-left with angled ribbon badge across chest and party logo on right. Perfect when deity or festival art is on the right.',
                'badge'              => 'Layer 5 - Left Candidate',
                'leadership_mode'    => 'individual_chips',
                'candidate_position' => 'bottom-left',
                'candidate_shape'    => 'cutout',
                'footer_style'       => 'candidate_left_ribbon',
            ],
            [
                'key'                => 'modern_capsule_dock',
                'name'               => 'Layer 6: Modern Capsule Dock (Floating Rounded Card & Inset Socials)',
                'description'        => 'Floating rounded card inset from edges with subtle top accent, embedded social row, and candidate photo overlapping right wing. Modern 2026 aesthetic.',
                'badge'              => 'Layer 6 - Capsule Dock',
                'leadership_mode'    => 'individual_chips',
                'candidate_position' => 'bottom-right',
                'candidate_shape'    => 'cutout',
                'footer_style'       => 'modern_capsule_dock',
            ],
            [
                'key'                => 'circular_medallion_badge',
                'name'               => 'Layer 7: Circular Medallion Badge (Framed Portrait & Dual-Tier Card)',
                'description'        => 'Dual-tier card with 220px circular framed medallion avatar on right. Ideal for standard non-cutout portraits and dignitary styling.',
                'badge'              => 'Layer 7 - Medallion Avatar',
                'leadership_mode'    => 'individual_chips',
                'candidate_position' => 'bottom-right',
                'candidate_shape'    => 'circle',
                'footer_style'       => 'circular_medallion_badge',
            ],
            [
                'key'                => 'saffron_wave_banner',
                'name'               => 'Layer 8: Saffron Wave Banner (Dynamic Diagonal Wave & Tricolor Accent)',
                'description'        => 'Dynamic diagonal wave banner sloping across candidate chest with vertical tricolor side accent and bold white typography.',
                'badge'              => 'Layer 8 - Saffron Wave',
                'leadership_mode'    => 'individual_chips',
                'candidate_position' => 'bottom-right',
                'candidate_shape'    => 'cutout',
                'footer_style'       => 'saffron_wave_banner',
            ],
            [
                'key'                => 'tricolor_angled_blade',
                'name'               => 'Layer 9: Tricolor Angled Blade (Dynamic Patriotic Sash & Sharp Contrast)',
                'description'        => 'Dynamic 3-tier angled tricolor sash sloping across candidate torso, crisp navy designation, and clean bottom social strip. High-energy patriotic aesthetic.',
                'badge'              => 'Layer 9 - Tricolor Blade',
                'leadership_mode'    => 'individual_chips',
                'candidate_position' => 'bottom-right',
                'candidate_shape'    => 'cutout',
                'footer_style'       => 'tricolor_angled_blade',
            ],
            [
                'key'                => 'royal_maroon_heritage',
                'name'               => 'Layer 10: Royal Maroon Heritage (Temple Vermilion & 24K Gold Filigree)',
                'description'        => 'Auspicious temple maroon card with intricate 24K gold filigree jewel border, gilded gold 3D typography, and velvet social dock. Supreme sacred festival elegance.',
                'badge'              => 'Layer 10 - Maroon Heritage',
                'leadership_mode'    => 'individual_chips',
                'candidate_position' => 'bottom-right',
                'candidate_shape'    => 'cutout',
                'footer_style'       => 'royal_maroon_heritage',
            ],
            [
                'key'                => 'classic_political_right',
                'name'               => 'Classic Political (Dr. Dharam Singh Style)',
                'description'        => '6-7 circle leaders at top with saffron border, large candidate cutout bottom-right, standard political footer with BJP logo + name + social handles.',
                'badge'              => 'Standard Right',
                'leadership_mode'    => 'individual_chips',
                'candidate_position' => 'bottom-right',
                'candidate_shape'    => 'cutout',
                'footer_style'       => 'standard_political',
            ],
            [
                'key'                => 'classic_political_rounded_square',
                'name'               => 'Modern Political (Rounded Square Leaders)',
                'description'        => 'Rounded square leader avatars with saffron border at top, large candidate cutout bottom-right.',
                'badge'              => 'Rounded Leaders',
                'leadership_mode'    => 'individual_chips',
                'candidate_position' => 'bottom-right',
                'candidate_shape'    => 'cutout',
                'footer_style'       => 'standard_political',
            ],
            [
                'key'                => 'classic_political_white_borders',
                'name'               => 'Clean Political (White Border Circles)',
                'description'        => 'Circle avatars with clean white border at top, large candidate cutout bottom-right.',
                'badge'              => 'White Circles',
                'leadership_mode'    => 'individual_chips',
                'candidate_position' => 'bottom-right',
                'candidate_shape'    => 'cutout',
                'footer_style'       => 'standard_political',
            ],
            [
                'key'                => 'candidate_left_classic',
                'name'               => 'Classic Political Left (Artwork on Right)',
                'description'        => 'Leaders at top, large candidate cutout anchored to bottom-left, footer with party logo on right and name on left.',
                'badge'              => 'Standard Left',
                'leadership_mode'    => 'individual_chips',
                'candidate_position' => 'bottom-left',
                'candidate_shape'    => 'cutout',
                'footer_style'       => 'standard_political',
            ],
            [
                'key'                => 'circle_badge_right',
                'name'               => 'Circle Photo Frame (Bottom-Right)',
                'description'        => 'Circular candidate photo frame on bottom-right with crisp white/gold border, perfect when non-cutout photos are uploaded.',
                'badge'              => 'Circle Badge',
                'leadership_mode'    => 'individual_chips',
                'candidate_position' => 'bottom-right',
                'candidate_shape'    => 'circle',
                'footer_style'       => 'standard_political',
            ],
            [
                'key'                => 'circle_badge_center',
                'name'               => 'Centered Circle Avatar (Announcement / Greeting)',
                'description'        => 'Centered circular candidate photo with balanced borders, ideal for personal wishes and greetings.',
                'badge'              => 'Circle Center',
                'leadership_mode'    => 'individual_chips',
                'candidate_position' => 'bottom-center',
                'candidate_shape'    => 'circle',
                'footer_style'       => 'standard_political',
            ],
            [
                'key'                => 'group_cutout_classic',
                'name'               => 'Group Cutout (Ram Navami Style)',
                'description'        => 'Transparent group photo top-left, party logo top-right, candidate bottom-right, standard political footer.',
                'badge'              => 'Group Cutout',
                'leadership_mode'    => 'party_group_cutout',
                'candidate_position' => 'bottom-right',
                'candidate_shape'    => 'cutout',
                'footer_style'       => 'standard_political',
            ],
            [
                'key'                => 'leader_tribute_royal',
                'name'               => 'Leader Tribute Royal (Modi 11-Years Style)',
                'description'        => 'No top strip (for tribute & campaign art), candidate cutout on bottom-right, deep maroon footer bar with circular party logo on left and gold text.',
                'badge'              => 'Royal Bar',
                'leadership_mode'    => 'none',
                'candidate_position' => 'bottom-right',
                'candidate_shape'    => 'cutout',
                'footer_style'       => 'royal_bar',
            ],
            [
                'key'                => 'clean_no_leaders',
                'name'               => 'Clean Minimal (Festival Focus)',
                'description'        => 'No top senior leadership strip, focuses on festival greeting with candidate cutout on bottom and standard footer.',
                'badge'              => 'Minimal',
                'leadership_mode'    => 'none',
                'candidate_position' => 'bottom-right',
                'candidate_shape'    => 'cutout',
                'footer_style'       => 'standard_political',
            ],
        ];
    }

    /**
     * Built-in Presets Catalog ensuring 10+ years stability & zero math for admin.
     * All pixel values tuned to reference Dr. Dharam Singh poster measurements.
     */
    public function getDefaultPresetConfig(string $key): array
    {
        return match ($key) {
            // Layer 1: Dual Ribbon Badge (Bhai Dooj Reference Style - Image 2)
            'dual_ribbon_badge' => [
                'canvas' => ['width' => 1080, 'height' => 1080],
                'header' => [
                    'enabled'              => true,
                    'mode'                 => 'individual_chips',
                    'chip_shape'           => 'circle',
                    'chip_size'            => 95,
                    'chip_border_width'    => 4,
                    'chip_border_color'    => '#FF9933',
                    'chips_start_y'        => 10,
                ],
                'candidate' => [
                    'shape'            => 'cutout',
                    'position'         => 'bottom-right',
                    'max_width'        => 420,
                    'max_height'       => 480,
                    'offset_right'     => 10,
                    'anchor_bottom_y'  => 1080,
                    'min_y'            => 115,
                ],
                'footer' => [
                    'style' => 'dual_ribbon_badge',
                ],
            ],

            // Layer 2: Solid White Card Banner (Milkha Singh Reference Style - Image 3)
            'solid_white_banner' => [
                'canvas' => ['width' => 1080, 'height' => 1080],
                'header' => [
                    'enabled'              => true,
                    'mode'                 => 'individual_chips',
                    'chip_shape'           => 'circle',
                    'chip_size'            => 95,
                    'chip_border_width'    => 4,
                    'chip_border_color'    => '#FF9933',
                    'chips_start_y'        => 10,
                ],
                'candidate' => [
                    'shape'            => 'cutout',
                    'position'         => 'bottom-right',
                    'max_width'        => 420,
                    'max_height'       => 480,
                    'offset_right'     => 10,
                    'anchor_bottom_y'  => 1080,
                    'min_y'            => 115,
                ],
                'footer' => [
                    'style' => 'solid_white_banner',
                ],
            ],

            // Layer 3: Floating Tricolor Badge (Diwali Reference Style - Image 1)
            'floating_tricolor_badge' => [
                'canvas' => ['width' => 1080, 'height' => 1080],
                'header' => [
                    'enabled'              => true,
                    'mode'                 => 'individual_chips',
                    'chip_shape'           => 'rounded_square',
                    'chip_size'            => 95,
                    'chip_border_width'    => 4,
                    'chip_border_color'    => '#FF9933',
                    'chips_start_y'        => 10,
                ],
                'candidate' => [
                    'shape'            => 'cutout',
                    'position'         => 'bottom-right',
                    'max_width'        => 420,
                    'max_height'       => 480,
                    'offset_right'     => 10,
                    'anchor_bottom_y'  => 1080,
                    'min_y'            => 115,
                ],
                'footer' => [
                    'style' => 'floating_tricolor_badge',
                ],
            ],

            // Layer 4: Royal Gold Banner (Deep Midnight Card with Gold Borders)
            'royal_gold_dock' => [
                'canvas' => ['width' => 1080, 'height' => 1080],
                'header' => [
                    'enabled'              => true,
                    'mode'                 => 'individual_chips',
                    'chip_shape'           => 'square',
                    'chip_size'            => 95,
                    'chip_border_width'    => 3,
                    'chip_border_color'    => '#D4AF37',
                    'chips_start_y'        => 10,
                ],
                'candidate' => [
                    'shape'            => 'cutout',
                    'position'         => 'bottom-right',
                    'max_width'        => 420,
                    'max_height'       => 480,
                    'offset_right'     => 10,
                    'anchor_bottom_y'  => 1080,
                    'min_y'            => 115,
                ],
                'footer' => [
                    'style' => 'royal_gold_dock',
                ],
            ],

            // Layer 5: Candidate Left Ribbon (Mirrored for Right-Focus Artwork)
            'candidate_left_ribbon' => [
                'canvas' => ['width' => 1080, 'height' => 1080],
                'header' => [
                    'enabled'              => true,
                    'mode'                 => 'individual_chips',
                    'chip_shape'           => 'circle',
                    'chip_size'            => 95,
                    'chip_border_width'    => 4,
                    'chip_border_color'    => '#FF9933',
                    'chips_start_y'        => 10,
                ],
                'candidate' => [
                    'shape'            => 'cutout',
                    'position'         => 'bottom-left',
                    'max_width'        => 420,
                    'max_height'       => 480,
                    'offset_left'      => 10,
                    'anchor_bottom_y'  => 1080,
                    'min_y'            => 115,
                ],
                'footer' => [
                    'style' => 'candidate_left_ribbon',
                ],
            ],

            // Layer 6: Modern Capsule Dock (Floating Rounded Card & Inset Socials)
            'modern_capsule_dock' => [
                'canvas' => ['width' => 1080, 'height' => 1080],
                'header' => [
                    'enabled'              => true,
                    'mode'                 => 'individual_chips',
                    'chip_shape'           => 'rounded_square',
                    'chip_size'            => 95,
                    'chip_border_width'    => 4,
                    'chip_border_color'    => '#FF9933',
                    'chips_start_y'        => 10,
                ],
                'candidate' => [
                    'shape'            => 'cutout',
                    'position'         => 'bottom-right',
                    'max_width'        => 420,
                    'max_height'       => 480,
                    'offset_right'     => 10,
                    'anchor_bottom_y'  => 1080,
                    'min_y'            => 115,
                ],
                'footer' => [
                    'style' => 'modern_capsule_dock',
                ],
            ],

            // Layer 7: Circular Medallion Badge (Framed Portrait & Dual-Tier Card)
            'circular_medallion_badge' => [
                'canvas' => ['width' => 1080, 'height' => 1080],
                'header' => [
                    'enabled'              => true,
                    'mode'                 => 'individual_chips',
                    'chip_shape'           => 'circle',
                    'chip_size'            => 95,
                    'chip_border_width'    => 4,
                    'chip_border_color'    => '#FF9933',
                    'chips_start_y'        => 10,
                ],
                'candidate' => [
                    'shape'            => 'circle',
                    'circle_size'      => 220,
                    'position'         => 'bottom-right',
                    'max_width'        => 220,
                    'max_height'       => 220,
                    'offset_right'     => 25,
                    'anchor_bottom_y'  => 1065,
                    'min_y'            => 115,
                ],
                'footer' => [
                    'style' => 'circular_medallion_badge',
                ],
            ],

            // Layer 8: Saffron Wave Banner (Dynamic Diagonal Wave & Tricolor Accent)
            'saffron_wave_banner' => [
                'canvas' => ['width' => 1080, 'height' => 1080],
                'header' => [
                    'enabled'              => true,
                    'mode'                 => 'individual_chips',
                    'chip_shape'           => 'circle',
                    'chip_size'            => 95,
                    'chip_border_width'    => 4,
                    'chip_border_color'    => '#FF9933',
                    'chips_start_y'        => 10,
                ],
                'candidate' => [
                    'shape'            => 'cutout',
                    'position'         => 'bottom-right',
                    'max_width'        => 420,
                    'max_height'       => 480,
                    'offset_right'     => 10,
                    'anchor_bottom_y'  => 1080,
                    'min_y'            => 115,
                ],
                'footer' => [
                    'style' => 'saffron_wave_banner',
                ],
            ],

            // Layer 9: Tricolor Angled Blade (Dynamic Patriotic Sash & Sharp Contrast)
            'tricolor_angled_blade' => [
                'canvas' => ['width' => 1080, 'height' => 1080],
                'header' => [
                    'enabled'              => true,
                    'mode'                 => 'individual_chips',
                    'chip_shape'           => 'circle',
                    'chip_size'            => 95,
                    'chip_border_width'    => 4,
                    'chip_border_color'    => '#FF9933',
                    'chips_start_y'        => 10,
                ],
                'candidate' => [
                    'shape'            => 'cutout',
                    'position'         => 'bottom-right',
                    'max_width'        => 420,
                    'max_height'       => 480,
                    'offset_right'     => 10,
                    'anchor_bottom_y'  => 1080,
                    'min_y'            => 115,
                ],
                'footer' => [
                    'style' => 'tricolor_angled_blade',
                ],
            ],

            // Layer 10: Royal Maroon Heritage (Temple Vermilion & 24K Gold Filigree)
            'royal_maroon_heritage' => [
                'canvas' => ['width' => 1080, 'height' => 1080],
                'header' => [
                    'enabled'              => true,
                    'mode'                 => 'individual_chips',
                    'chip_shape'           => 'circle',
                    'chip_size'            => 95,
                    'chip_border_width'    => 4,
                    'chip_border_color'    => '#D4AF37',
                    'chips_start_y'        => 10,
                ],
                'candidate' => [
                    'shape'            => 'cutout',
                    'position'         => 'bottom-right',
                    'max_width'        => 420,
                    'max_height'       => 480,
                    'offset_right'     => 10,
                    'anchor_bottom_y'  => 1080,
                    'min_y'            => 115,
                ],
                'footer' => [
                    'style' => 'royal_maroon_heritage',
                ],
            ],

            // Style: Modern Political (Rounded Square Leaders)
            'classic_political_rounded_square' => [
                'canvas' => ['width' => 1080, 'height' => 1080],
                'header' => [
                    'enabled'              => true,
                    'mode'                 => 'individual_chips',
                    'chip_shape'           => 'rounded_square',
                    'chip_size'            => 95,
                    'chip_border_width'    => 4,
                    'chip_border_color'    => '#FF9933',
                    'chips_start_y'        => 10,
                ],
                'candidate' => [
                    'shape'            => 'cutout',
                    'position'         => 'bottom-right',
                    'max_width'        => 420,
                    'max_height'       => 480,
                    'offset_right'     => 10,
                    'anchor_bottom_y'  => 1080,
                    'min_y'            => 115,
                ],
                'footer' => [
                    'style' => 'standard_political',
                ],
            ],

            // Style: Clean Political (White Border Circles)
            'classic_political_white_borders' => [
                'canvas' => ['width' => 1080, 'height' => 1080],
                'header' => [
                    'enabled'              => true,
                    'mode'                 => 'individual_chips',
                    'chip_shape'           => 'circle',
                    'chip_size'            => 95,
                    'chip_border_width'    => 4,
                    'chip_border_color'    => '#ffffff',
                    'chips_start_y'        => 10,
                ],
                'candidate' => [
                    'shape'            => 'cutout',
                    'position'         => 'bottom-right',
                    'max_width'        => 420,
                    'max_height'       => 480,
                    'offset_right'     => 10,
                    'anchor_bottom_y'  => 1080,
                    'min_y'            => 115,
                ],
                'footer' => [
                    'style' => 'standard_political',
                ],
            ],

            // Style: Candidate on Left (when artwork / deity is on right)
            'candidate_left_classic' => [
                'canvas' => ['width' => 1080, 'height' => 1080],
                'header' => [
                    'enabled'              => true,
                    'mode'                 => 'individual_chips',
                    'chip_shape'           => 'circle',
                    'chip_size'            => 95,
                    'chip_border_width'    => 4,
                    'chip_border_color'    => '#FF9933',
                    'chips_start_y'        => 10,
                ],
                'candidate' => [
                    'shape'            => 'cutout',
                    'position'         => 'bottom-left',
                    'max_width'        => 420,
                    'max_height'       => 480,
                    'offset_left'      => 15,
                    'anchor_bottom_y'  => 1035,
                    'min_y'            => 115,
                ],
                'footer' => [
                    'style' => 'standard_political',
                ],
            ],

            // Style: Circular Photo Badge on Right (For non-cutout or framed templates)
            'circle_badge_right' => [
                'canvas' => ['width' => 1080, 'height' => 1080],
                'header' => [
                    'enabled'              => true,
                    'mode'                 => 'individual_chips',
                    'chip_shape'           => 'circle',
                    'chip_size'            => 95,
                    'chip_border_width'    => 4,
                    'chip_border_color'    => '#FF9933',
                    'chips_start_y'        => 10,
                ],
                'candidate' => [
                    'shape'               => 'circle',
                    'circle_size'         => 240,
                    'circle_border_color' => '#ffffff',
                    'circle_border_width' => 5,
                    'position'            => 'bottom-right',
                    'offset_right'        => 30,
                    'footer_top_y'        => 910,
                    'footer_overlap'      => 30,
                ],
                'footer' => [
                    'style' => 'standard_political',
                ],
            ],

            // Style: Centered Circular Avatar
            'circle_badge_center' => [
                'canvas' => ['width' => 1080, 'height' => 1080],
                'header' => [
                    'enabled'              => true,
                    'mode'                 => 'individual_chips',
                    'chip_shape'           => 'circle',
                    'chip_size'            => 95,
                    'chip_border_width'    => 4,
                    'chip_border_color'    => '#FF9933',
                    'chips_start_y'        => 10,
                ],
                'candidate' => [
                    'shape'               => 'circle',
                    'circle_size'         => 220,
                    'circle_border_color' => '#ffffff',
                    'circle_border_width' => 5,
                    'position'            => 'bottom-center',
                    'footer_top_y'        => 910,
                    'footer_overlap'      => 30,
                ],
                'footer' => [
                    'style' => 'standard_political',
                ],
            ],

            // Style: Royal Maroon / Gold Bar (Modi 11 Years style)
            'leader_tribute_royal' => [
                'canvas' => ['width' => 1080, 'height' => 1080],
                'header' => [
                    'enabled' => false,
                    'mode'    => 'none',
                ],
                'candidate' => [
                    'shape'            => 'cutout',
                    'position'         => 'bottom-right',
                    'max_width'        => 420,
                    'max_height'       => 480,
                    'offset_right'     => 10,
                    'anchor_bottom_y'  => 1030,
                    'min_y'            => 100,
                ],
                'footer' => [
                    'style'  => 'royal_bar',
                    'bar_bg' => '#7A0C0E',
                ],
            ],

            // Style: Group Cutout at top-left (Ram Navami style)
            'group_cutout_classic' => [
                'canvas' => ['width' => 1080, 'height' => 1080],
                'header' => [
                    'enabled'             => true,
                    'mode'                => 'party_group_cutout',
                    'group_cutout_width'  => 360,
                    'group_cutout_x'      => 10,
                    'group_cutout_y'      => 10,
                    'show_party_logo'     => true,
                    'logo_size'           => 70,
                ],
                'candidate' => [
                    'shape'            => 'cutout',
                    'position'         => 'bottom-right',
                    'max_width'        => 420,
                    'max_height'       => 480,
                    'offset_right'     => 10,
                    'anchor_bottom_y'  => 1035,
                    'min_y'            => 110,
                ],
                'footer' => [
                    'style'  => 'standard_political',
                ],
            ],

            // Style: Minimal Clean (No Top Leaders)
            'clean_no_leaders' => [
                'canvas' => ['width' => 1080, 'height' => 1080],
                'header' => [
                    'enabled' => false,
                    'mode'    => 'none',
                ],
                'candidate' => [
                    'shape'            => 'cutout',
                    'position'         => 'bottom-right',
                    'max_width'        => 420,
                    'max_height'       => 480,
                    'offset_right'     => 10,
                    'anchor_bottom_y'  => 1035,
                    'min_y'            => 50,
                ],
                'footer' => [
                    'style' => 'standard_political',
                ],
            ],

            // DEFAULT: Classic Political Right — Dr. Dharam Singh Gold Standard
            default => [
                'canvas' => ['width' => 1080, 'height' => 1080],
                'header' => [
                    'enabled'              => true,
                    'mode'                 => 'individual_chips',
                    'chip_shape'           => 'circle',
                    'chip_size'            => 95,
                    'chip_border_width'    => 4,
                    'chip_border_color'    => '#FF9933',
                    'chips_start_y'        => 10,
                ],
                'candidate' => [
                    'shape'            => 'cutout',
                    'position'         => 'bottom-right',
                    'max_width'        => 420,
                    'max_height'       => 480,
                    'offset_right'     => 10,
                    'anchor_bottom_y'  => 1080,
                    'min_y'            => 115,
                ],
                'footer' => [
                    'style' => 'standard_political',
                ],
            ],
        };
    }
}
