<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CanvaService
{
    protected ?string $clientId;
    protected ?string $clientSecret;
    protected string $redirectUri;

    public function __construct()
    {
        $this->clientId = config('services.canva.client_id');
        $this->clientSecret = config('services.canva.client_secret');
        $this->redirectUri = config('services.canva.redirect', url('/admin/canva/callback'));
    }

    /**
     * Check if Canva credentials are configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->clientId) && !empty($this->clientSecret);
    }

    /**
     * Generate OAuth 2.0 PKCE Authorization URL.
     */
    public function getAuthorizationUrl(?string $redirectUri = null): array
    {
        $redirect = $redirectUri ?: $this->redirectUri;
        
        // PKCE Code Verifier (43-128 random chars)
        $codeVerifier = bin2hex(random_bytes(32));
        
        // Code Challenge: Base64URL(SHA256(verifier))
        $codeChallenge = rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');
        
        $state = Str::random(32);

        $params = http_build_query([
            'response_type'         => 'code',
            'client_id'             => $this->clientId,
            'redirect_uri'          => $redirect,
            'scope'                 => 'design:content:read design:content:write design:meta:read asset:write',
            'code_challenge'        => $codeChallenge,
            'code_challenge_method' => 'S256',
            'state'                 => $state,
        ]);

        $authUrl = 'https://www.canva.com/api/oauth/authorize?' . $params;

        return [
            'url'           => $authUrl,
            'code_verifier' => $codeVerifier,
            'state'         => $state,
        ];
    }

    /**
     * Exchange OAuth authorization code for Access & Refresh Tokens.
     */
    public function exchangeCodeForToken(string $code, string $codeVerifier, ?string $redirectUri = null): array
    {
        $redirect = $redirectUri ?: $this->redirectUri;

        $response = Http::asForm()
            ->withBasicAuth($this->clientId, $this->clientSecret)
            ->timeout(15)
            ->post('https://api.canva.com/rest/v1/oauth/token', [
                'grant_type'    => 'authorization_code',
                'code'          => $code,
                'code_verifier' => $codeVerifier,
                'redirect_uri'  => $redirect,
            ]);

        if (!$response->successful()) {
            Log::error('Canva Token Exchange Failed: ' . $response->body());
            throw new \RuntimeException('Failed to exchange code with Canva: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Refresh an expired access token using the refresh token.
     */
    public function refreshToken(string $refreshToken): array
    {
        $response = Http::asForm()
            ->withBasicAuth($this->clientId, $this->clientSecret)
            ->timeout(15)
            ->post('https://api.canva.com/rest/v1/oauth/token', [
                'grant_type'    => 'refresh_token',
                'refresh_token' => $refreshToken,
            ]);

        if (!$response->successful()) {
            Log::error('Canva Token Refresh Failed: ' . $response->body());
            throw new \RuntimeException('Failed to refresh Canva token.');
        }

        return $response->json();
    }

    /**
     * Create a new design in Canva and return its edit URL.
     */
    public function createDesign(string $accessToken, string $title = 'SocialManager Graphic', int $width = 1080, int $height = 1080, ?string $preset = null): array
    {
        $payload = [
            'title' => Str::limit($title, 100),
        ];

        // Canva Connect API only accepts these specific preset names: doc, email, presentation, whiteboard.
        // All others (e.g., youtube_thumbnail, instagram_post) require custom width & height.
        $validPresets = ['doc', 'email', 'presentation', 'whiteboard'];
        $normalizedPreset = $preset ? strtolower(trim($preset)) : null;

        if ($normalizedPreset && in_array($normalizedPreset, $validPresets, true)) {
            $payload['design_type'] = [
                'type' => 'preset',
                'name' => $normalizedPreset,
            ];
        } else {
            $payload['design_type'] = [
                'type'   => 'custom',
                'width'  => max(40, min(8000, $width)),
                'height' => max(40, min(8000, $height)),
            ];
        }

        $response = Http::withToken($accessToken)
            ->timeout(20)
            ->post('https://api.canva.com/rest/v1/designs', $payload);

        // If preset request fails, fallback safely to custom dimensions
        if (!$response->successful() && $normalizedPreset && in_array($normalizedPreset, $validPresets, true)) {
            Log::warning('Canva preset failed (' . $preset . '), falling back to custom dimensions: ' . $response->body());
            $fallbackPayload = [
                'title' => Str::limit($title, 100),
                'design_type' => [
                    'type'   => 'custom',
                    'width'  => max(40, min(8000, $width)),
                    'height' => max(40, min(8000, $height)),
                ],
            ];
            $response = Http::withToken($accessToken)
                ->timeout(20)
                ->post('https://api.canva.com/rest/v1/designs', $fallbackPayload);
        }

        if (!$response->successful()) {
            Log::error('Canva Create Design Failed: ' . $response->body());
            throw new \RuntimeException('Could not create design on Canva: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Export a design from Canva and download it to local storage.
     */
    public function exportAndDownloadDesign(string $accessToken, string $designId, string $format = 'jpg'): array
    {
        $normalizedFormat = in_array(strtolower($format), ['png', 'jpg', 'jpeg']) ? strtolower($format) : 'jpg';
        if ($normalizedFormat === 'jpeg') {
            $normalizedFormat = 'jpg';
        }

        $formatConfig = [
            'type'           => $normalizedFormat,
            'export_quality' => 'regular',
        ];

        // Canva Connect API requires an integer quality (1-100) when format is jpg
        if ($normalizedFormat === 'jpg') {
            $formatConfig['quality'] = 90;
        }

        // 1. Initiate export job
        $jobResponse = Http::withToken($accessToken)
            ->timeout(15)
            ->post('https://api.canva.com/rest/v1/exports', [
                'design_id' => $designId,
                'format'    => $formatConfig,
            ]);

        if (!$jobResponse->successful()) {
            Log::error('Canva Export Job Failed: ' . $jobResponse->body());
            throw new \RuntimeException('Failed to initiate Canva export: ' . $jobResponse->body());
        }

        $jobData = $jobResponse->json();
        $jobId = $jobData['job']['id'] ?? null;
        $downloadUrl = null;

        if (($jobData['job']['status'] ?? '') === 'success' && !empty($jobData['job']['urls'])) {
            $downloadUrl = $jobData['job']['urls'][0];
        }

        if (!$jobId && !$downloadUrl) {
            throw new \RuntimeException('No job ID returned by Canva export API.');
        }

        // 2. Poll export job until ready (max 10 attempts) if not already finished
        if (!$downloadUrl) {
            for ($i = 0; $i < 10; $i++) {
                $statusResponse = Http::withToken($accessToken)
                    ->timeout(10)
                    ->get("https://api.canva.com/rest/v1/exports/{$jobId}");

                if ($statusResponse->successful()) {
                    $statusData = $statusResponse->json();
                    $status = $statusData['job']['status'] ?? 'pending';

                    if ($status === 'success' && !empty($statusData['job']['urls'])) {
                        $downloadUrl = $statusData['job']['urls'][0];
                        break;
                    }

                    if ($status === 'failed') {
                        throw new \RuntimeException('Canva export job failed on remote server.');
                    }
                }

                usleep(1500000); // 1.5 seconds wait
            }
        }

        if (!$downloadUrl) {
            throw new \RuntimeException('Canva design export timed out. Please try again.');
        }

        // 3. Download image and save to local storage
        $fileContents = Http::timeout(30)->get($downloadUrl)->body();
        if (empty($fileContents)) {
            throw new \RuntimeException('Failed to download exported image file.');
        }

        // If Canva returned a ZIP for a multi-page design, extract the first image
        if (substr($fileContents, 0, 4) === "PK\x03\x04" && class_exists('\ZipArchive')) {
            $zip = new \ZipArchive();
            $tempZip = tempnam(sys_get_temp_dir(), 'canva_zip_');
            file_put_contents($tempZip, $fileContents);
            if ($zip->open($tempZip) === true) {
                for ($idx = 0; $idx < $zip->numFiles; $idx++) {
                    $entryName = $zip->getNameIndex($idx);
                    if (preg_match('/\.(jpe?g|png)$/i', $entryName)) {
                        $extracted = $zip->getFromIndex($idx);
                        if (!empty($extracted)) {
                            $fileContents = $extracted;
                            break;
                        }
                    }
                }
                $zip->close();
            }
            @unlink($tempZip);
        }

        $fileName = 'canva_' . Str::random(20) . '.' . $normalizedFormat;
        $relativePath = 'posts/canva/' . $fileName;

        Storage::disk('public')->put($relativePath, $fileContents);

        return [
            'relative_path' => $relativePath,
            'url'           => Storage::disk('public')->url($relativePath),
            'filename'      => $fileName,
            'size'          => strlen($fileContents),
        ];
    }

    /**
     * Pre-configured Quick Templates with exact social dimensions.
     */
    public function getQuickTemplates(): array
    {
        return [
            [
                'id'          => 'ig_square',
                'name'        => 'Square Post',
                'badge'       => 'Instagram & Facebook',
                'dimensions'  => '1080 × 1080 px',
                'aspect'      => '1:1',
                'width'       => 1080,
                'height'      => 1080,
                'canva_url'   => 'https://www.canva.com/create/instagram-posts/',
                'description' => 'Ideal for feed posts, announcements, and multi-image carousels.',
                'icon'        => 'ti-brand-instagram',
                'color'       => '#e1306c',
            ],
            [
                'id'          => 'ig_story',
                'name'        => 'Story / Reel',
                'badge'       => 'Vertical 9:16',
                'dimensions'  => '1080 × 1920 px',
                'aspect'      => '9:16',
                'width'       => 1080,
                'height'      => 1920,
                'canva_url'   => 'https://www.canva.com/create/instagram-stories/',
                'description' => 'Full-screen mobile story graphics and Reel cover backgrounds.',
                'icon'        => 'ti-device-mobile',
                'color'       => '#833ab4',
            ],
            [
                'id'          => 'fb_banner',
                'name'        => 'Landscape Post',
                'badge'       => 'Facebook & Web',
                'dimensions'  => '1200 × 630 px',
                'aspect'      => '1.91:1',
                'width'       => 1200,
                'height'      => 630,
                'canva_url'   => 'https://www.canva.com/create/facebook-posts/',
                'description' => 'Optimized for Facebook timeline banners and link preview cards.',
                'icon'        => 'ti-brand-facebook',
                'color'       => '#1877f2',
            ],
            [
                'id'          => 'yt_thumbnail',
                'name'        => 'Video Thumbnail',
                'badge'       => 'YouTube 16:9',
                'dimensions'  => '1280 × 720 px',
                'aspect'      => '16:9',
                'width'       => 1280,
                'height'      => 720,
                'canva_url'   => 'https://www.canva.com/create/youtube-thumbnails/',
                'description' => 'High click-through rate thumbnails for video broadcasts.',
                'icon'        => 'ti-brand-youtube',
                'color'       => '#ff0000',
            ],
        ];
    }
}
