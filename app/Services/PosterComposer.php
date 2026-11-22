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
     * Render bottom row of social media icons (FB, IG, WA/Phone).
     */
    protected function renderSocialIconsRow(
        ImageInterface $canvas,
        UserBranding $branding,
        int $startX,
        int $pillY,
        int $textY,
        string $fontPath
    ): void {
        $currX = $startX;

        if ($branding->facebook_handle) {
            $this->drawSocialIconPill($canvas, $currX, $pillY, 'fb', '#1877F2');
            $canvas->text($branding->facebook_handle, $currX + 28, $textY, function ($font) use ($fontPath) {
                $font->file($fontPath);
                $font->size(14);
                $font->color('#333333');
            });
            $currX += 290;
        }

        if ($branding->instagram_handle) {
            $this->drawSocialIconPill($canvas, $currX, $pillY, 'ig', '#E4405F');
            $canvas->text($branding->instagram_handle, $currX + 28, $textY, function ($font) use ($fontPath) {
                $font->file($fontPath);
                $font->size(14);
                $font->color('#333333');
            });
            $currX += 290;
        }

        if ($branding->phone_number) {
            $this->drawSocialIconPill($canvas, $currX, $pillY, 'wa', '#25D366');
            $canvas->text($branding->phone_number, $currX + 28, $textY, function ($font) use ($fontPath) {
                $font->file($fontPath);
                $font->size(14);
                $font->color('#333333');
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
        imagealphablending($innerGd, false);
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
