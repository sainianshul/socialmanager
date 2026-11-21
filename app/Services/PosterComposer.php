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

        $config = array_replace_recursive($this->getDefaultPresetConfig($presetKey), $customConfig);
        $canvasWidth = $config['canvas']['width'] ?? 1080;
        $canvasHeight = $config['canvas']['height'] ?? 1080;

        // 1. Base Canvas & Background
        $canvas = $this->createBaseCanvas($bgPath, $canvasWidth, $canvasHeight);

        // 2. Leadership Header Section
        $includeStrip = $options['include_strip'] ?? true;
        if ($includeStrip && ($config['header']['enabled'] ?? true)) {
            $this->renderHeaderSection($canvas, $config['header'] ?? [], $party, $strip);
        }

        // 3. Candidate Cutout Photo
        $this->renderCandidatePhoto($canvas, $config['candidate'] ?? [], $branding, $options);

        // 4. Political Footer Branding Frame (Name, Designation, Socials)
        if ($branding) {
            $this->renderFooterFrame($canvas, $config['footer'] ?? [], $branding, $party);
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

    /**
     * Render Top Leadership Header (Group Cutout, Individual Chips, or None)
     */
    protected function renderHeaderSection(
        ImageInterface $canvas,
        array $headerConfig,
        ?PoliticalParty $party,
        ?LeaderStrip $strip
    ): void {
        $mode = $headerConfig['mode'] ?? 'party_group_cutout';

        // A) Group Cutout Mode (Image 1 & 2 Style)
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

        // B) Individual Leader Chips Mode
        elseif ($mode === 'individual_chips') {
            $leaders = $strip ? $strip->leaders->take(6) : ($party?->leaders()->take(6)->get() ?? collect());
            if ($leaders->isNotEmpty()) {
                $startX = $headerConfig['chips_start_x'] ?? 30;
                $startY = $headerConfig['chips_start_y'] ?? 25;
                $chipSize = $headerConfig['chip_size'] ?? 52;
                $gap = $headerConfig['chip_gap'] ?? 10;
                $shape = $headerConfig['chip_shape'] ?? 'circle';

                // Optional frosted background bar
                if (!empty($headerConfig['bar_bg'])) {
                    $barW = ($chipSize * $leaders->count()) + ($gap * ($leaders->count() - 1)) + 24;
                    $canvas->drawRectangle($startX - 12, $startY - 6, function ($rect) use ($barW, $chipSize, $headerConfig) {
                        $rect->size($barW, $chipSize + 12);
                        $rect->background($headerConfig['bar_bg']);
                    });
                }

                $currX = $startX;
                foreach ($leaders as $leader) {
                    $leaderImgPath = $this->resolveFilePath($leader->image);
                    if ($leaderImgPath && file_exists($leaderImgPath)) {
                        try {
                            $avatar = $this->manager->read($leaderImgPath)->cover($chipSize, $chipSize);
                            if ($shape === 'circle') {
                                $avatar = $this->makeCircularAvatar($avatar, $chipSize);
                            }
                            $canvas->place($avatar, 'top-left', $currX, $startY);
                        } catch (\Throwable $e) {
                            // Skip leader on error
                        }
                    }
                    $currX += $chipSize + $gap;
                }
            }
        }

        // Top Right Party Official Logo
        if ($headerConfig['show_party_logo'] ?? true) {
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

    /**
     * Render Candidate Cutout Photo Anchored to Corner/Bottom
     */
    protected function renderCandidatePhoto(
        ImageInterface $canvas,
        array $candidateConfig,
        ?UserBranding $branding,
        array $options
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
            $maxW = $candidateConfig['max_width'] ?? 340;
            $maxH = $candidateConfig['max_height'] ?? 420;
            $photoImg->scaleDown(width: $maxW, height: $maxH);

            $position = $candidateConfig['position'] ?? 'bottom-right';
            $offsetBottom = $candidateConfig['offset_bottom'] ?? 30;

            $pw = $photoImg->width();
            $ph = $photoImg->height();

            if ($position === 'bottom-right') {
                $x = 1080 - $pw - ($candidateConfig['offset_right'] ?? 20);
                $y = 1080 - $ph - $offsetBottom;
            } elseif ($position === 'bottom-left') {
                $x = $candidateConfig['offset_left'] ?? 20;
                $y = 1080 - $ph - $offsetBottom;
            } else { // bottom-center
                $x = (int) ((1080 - $pw) / 2);
                $y = 1080 - $ph - $offsetBottom;
            }

            $canvas->place($photoImg, 'top-left', $x, $y);
        } catch (\Throwable $e) {
            // Ignore candidate load error
        }
    }

    /**
     * Render Political Footer Frame (Dual Ribbon or Royal Bar + Socials)
     */
    protected function renderFooterFrame(
        ImageInterface $canvas,
        array $footerConfig,
        UserBranding $branding,
        ?PoliticalParty $party
    ): void {
        $style = $footerConfig['style'] ?? 'dual_ribbon';

        if ($style === 'royal_bar') {
            $this->renderRoyalBarFooter($canvas, $footerConfig, $branding, $party);
        } else {
            $this->renderDualRibbonFooter($canvas, $footerConfig, $branding, $party);
        }
    }

    /**
     * Style A: Dual Ribbon Footer (Image 2 Ram Navami Style)
     * Green Designation Bar + Saffron Name Box + Clean Social Row
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

        // 1. Designation Ribbon (Left Bottom Box: y: 940 to 985)
        if (!empty($branding->designation)) {
            $canvas->drawRectangle(0, 942, function ($rect) use ($desigColor) {
                $rect->size(540, 48);
                $rect->background($desigColor);
            });

            $desigText = Str::limit($branding->designation, 42);
            $canvas->text($desigText, 25, 974, function ($font) use ($fontHindiRegular) {
                $font->file($fontHindiRegular);
                $font->size(20);
                $font->color('#ffffff');
            });
        }

        // 2. Name Ribbon (Saffron Box: y: 885 to 940)
        $nameBoxW = 520;
        $nameBoxH = 55;
        $nameX = 540;
        $canvas->drawRectangle($nameX, 935, function ($rect) use ($nameColor, $nameBoxW, $nameBoxH) {
            $rect->size($nameBoxW, $nameBoxH);
            $rect->background($nameColor);
        });

        $canvas->text($branding->display_name, $nameX + 30, 972, function ($font) use ($fontHindiBold) {
            $font->file($fontHindiBold);
            $font->size(26);
            $font->color('#ffffff');
        });

        // 3. Social Media Handle Bar (y: 990 to 1080)
        $canvas->drawRectangle(0, 990, function ($rect) {
            $rect->size(1080, 90);
            $rect->background('#ffffff');
        });

        // Social Icons and Handles
        $startX = 30;
        $socialY = 1045;

        // Facebook
        if ($branding->facebook_handle) {
            $this->drawSocialIconPill($canvas, $startX, $socialY - 26, 'fb', '#1877F2');
            $canvas->text($branding->facebook_handle, $startX + 30, $socialY - 8, function ($font) use ($fontEngRegular) {
                $font->file($fontEngRegular);
                $font->size(17);
                $font->color('#333333');
            });
            $startX += 280;
        }

        // Instagram
        if ($branding->instagram_handle) {
            $this->drawSocialIconPill($canvas, $startX, $socialY - 26, 'ig', '#E4405F');
            $canvas->text($branding->instagram_handle, $startX + 30, $socialY - 8, function ($font) use ($fontEngRegular) {
                $font->file($fontEngRegular);
                $font->size(17);
                $font->color('#333333');
            });
            $startX += 280;
        }

        // WhatsApp / Phone
        $phone = $branding->phone_number;
        if ($phone) {
            $this->drawSocialIconPill($canvas, $startX, $socialY - 26, 'wa', '#25D366');
            $canvas->text($phone, $startX + 30, $socialY - 8, function ($font) use ($fontEngRegular) {
                $font->file($fontEngRegular);
                $font->size(17);
                $font->color('#333333');
            });
        }
    }

    /**
     * Style B: Royal Bar Footer (Image 3 Modi 11-Years Style)
     * Maroon/Red Bar with circular BJP logo on left, Name & Subtitle in Gold/White
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

        $barBg = $footerConfig['bar_bg'] ?? '#7A0C0E'; // Deep Maroon
        $goldColor = '#FFE57F';

        // 1. Deep Colored Maroon Main Bar (y: 890 to 1030)
        $canvas->drawRectangle(0, 890, function ($rect) use ($barBg) {
            $rect->size(1080, 140);
            $rect->background($barBg);
        });

        // 2. Circular Party Logo on Left (inside maroon bar)
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
        $canvas->text($branding->display_name, $textStartX, 955, function ($font) use ($fontHindiBold, $goldColor) {
            $font->file($fontHindiBold);
            $font->size(34);
            $font->color($goldColor);
        });

        // 4. Designation Text in White Subtitle
        if (!empty($branding->designation)) {
            $desigFormatted = '(' . $branding->designation . ')';
            $canvas->text($desigFormatted, $textStartX, 995, function ($font) use ($fontHindiRegular) {
                $font->file($fontHindiRegular);
                $font->size(22);
                $font->color('#ffffff');
            });
        }

        // 5. Clean White Bottom Strip with Social Handles (y: 1030 to 1080)
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
     * Circular avatar cropper using GD alpha transparency.
     */
    protected function makeCircularAvatar(ImageInterface $image, int $size, string $borderColor = '#ffffff', int $borderWidth = 0): ImageInterface
    {
        $image->cover($size, $size);
        $srcGd = $image->toGdImage();

        $destGd = imagecreatetruecolor($size, $size);
        imagealphablending($destGd, false);
        imagesavealpha($destGd, true);
        $transparent = imagecolorallocatealpha($destGd, 0, 0, 0, 127);
        imagefill($destGd, 0, 0, $transparent);

        $radius = $size / 2;
        for ($x = 0; $x < $size; $x++) {
            for ($y = 0; $y < $size; $y++) {
                $dx = $x - $radius + 0.5;
                $dy = $y - $radius + 0.5;
                if (($dx * $dx + $dy * $dy) <= ($radius * $radius)) {
                    $color = imagecolorat($srcGd, $x, $y);
                    imagesetpixel($destGd, $x, $y, $color);
                }
            }
        }

        // Draw border if requested
        if ($borderWidth > 0) {
            // Allocate border color
            $r = hexdec(substr($borderColor, 1, 2));
            $g = hexdec(substr($borderColor, 3, 2));
            $b = hexdec(substr($borderColor, 5, 2));
            $borderAlloc = imagecolorallocate($destGd, $r, $g, $b);
            imagesetthickness($destGd, $borderWidth);
            imageellipse($destGd, (int) $radius, (int) $radius, $size - $borderWidth, $size - $borderWidth, $borderAlloc);
        }

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

        // Fallback to any available font
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
                'key'                => 'classic_political_right',
                'name'               => 'Classic Political Right (Ram Navami Style)',
                'description'        => 'Official top-left leaders group cutout, top-right party logo, candidate cutout on bottom-right, dual-color saffron & green footer bar with social handles.',
                'badge'              => 'Dual Ribbon',
                'leadership_mode'    => 'party_group_cutout',
                'candidate_position' => 'bottom-right',
                'footer_style'       => 'dual_ribbon',
            ],
            [
                'key'                => 'leader_tribute_royal',
                'name'               => 'Leader Tribute Royal (Modi 11-Years Style)',
                'description'        => 'No top strip (for tribute & campaign art), candidate cutout on bottom-right, deep maroon footer bar with circular party logo on left and gold text.',
                'badge'              => 'Royal Bar',
                'leadership_mode'    => 'none',
                'candidate_position' => 'bottom-right',
                'footer_style'       => 'royal_bar',
            ],
            [
                'key'                => 'individual_circle_strip',
                'name'               => 'Custom 6-Leader Circle Strip',
                'description'        => 'Frosted top banner displaying up to 6 custom circular leaders with crisp borders, party logo top-right, dual ribbon footer with social handles.',
                'badge'              => 'Circle Chips',
                'leadership_mode'    => 'individual_chips',
                'candidate_position' => 'bottom-right',
                'footer_style'       => 'dual_ribbon',
            ],
            [
                'key'                => 'clean_no_leaders',
                'name'               => 'Clean Minimal (Festival Focus)',
                'description'        => 'No top senior leadership strip, focuses on festival greeting with candidate cutout on bottom and modern dark ribbon.',
                'badge'              => 'Minimal',
                'leadership_mode'    => 'none',
                'candidate_position' => 'bottom-right',
                'footer_style'       => 'dual_ribbon',
            ],
        ];
    }

    /**
     * Built-in Presets Catalog ensuring 10+ years stability & zero math for admin.
     */
    public function getDefaultPresetConfig(string $key): array
    {
        return match ($key) {
            // Style 2: Royal Maroon / Gold Bar (Modi 11 Years style)
            'leader_tribute_royal' => [
                'canvas' => ['width' => 1080, 'height' => 1080],
                'header' => [
                    'enabled' => false, // Tribute poster has main leader art
                    'mode'    => 'none',
                ],
                'candidate' => [
                    'position'      => 'bottom-right',
                    'max_width'     => 330,
                    'max_height'    => 400,
                    'offset_bottom' => 35,
                    'offset_right'  => 15,
                ],
                'footer' => [
                    'style'  => 'royal_bar',
                    'bar_bg' => '#7A0C0E',
                ],
            ],

            // Style 3: Individual 6 Circle Leaders Strip on Top
            'individual_circle_strip' => [
                'canvas' => ['width' => 1080, 'height' => 1080],
                'header' => [
                    'enabled'          => true,
                    'mode'             => 'individual_chips',
                    'chip_shape'       => 'circle',
                    'chip_size'        => 54,
                    'chip_gap'         => 10,
                    'chips_start_x'    => 35,
                    'chips_start_y'    => 25,
                    'bar_bg'           => 'rgba(255, 255, 255, 0.90)',
                    'show_party_logo'  => true,
                    'logo_size'        => 70,
                ],
                'candidate' => [
                    'position'      => 'bottom-right',
                    'max_width'     => 320,
                    'max_height'    => 380,
                    'offset_bottom' => 50,
                    'offset_right'  => 20,
                ],
                'footer' => [
                    'style'    => 'dual_ribbon',
                    'desig_bg' => '#00873E',
                    'name_bg'  => '#FF9933',
                ],
            ],

            // Style 4: Minimal Clean (No Top Leaders)
            'clean_no_leaders' => [
                'canvas' => ['width' => 1080, 'height' => 1080],
                'header' => [
                    'enabled' => false,
                    'mode'    => 'none',
                ],
                'candidate' => [
                    'position'      => 'bottom-right',
                    'max_width'     => 340,
                    'max_height'    => 400,
                    'offset_bottom' => 45,
                    'offset_right'  => 20,
                ],
                'footer' => [
                    'style'    => 'dual_ribbon',
                    'desig_bg' => '#0d9488',
                    'name_bg'  => '#1e293b',
                ],
            ],

            // Default Style 1: Classic Political Right (Ram Navami Style)
            default => [
                'canvas' => ['width' => 1080, 'height' => 1080],
                'header' => [
                    'enabled'             => true,
                    'mode'                => 'party_group_cutout', // Top-left transparent group cutout
                    'group_cutout_width'  => 360,
                    'group_cutout_x'      => 30,
                    'group_cutout_y'      => 25,
                    'show_party_logo'     => true,
                    'logo_size'           => 75,
                ],
                'candidate' => [
                    'position'      => 'bottom-right',
                    'max_width'     => 330,
                    'max_height'    => 400,
                    'offset_bottom' => 40,
                    'offset_right'  => 20,
                ],
                'footer' => [
                    'style'    => 'dual_ribbon',
                    'desig_bg' => '#00873E', // Party Green
                    'name_bg'  => '#FF9933', // Saffron
                ],
            ],
        };
    }
}
