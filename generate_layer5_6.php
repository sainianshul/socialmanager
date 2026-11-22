<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$composer = app(App\Services\PosterComposer::class);
$party = App\Models\PoliticalParty::where('short_code', 'BJP')->first() ?? App\Models\PoliticalParty::first();
$branding = new App\Models\UserBranding([
    'display_name' => 'डॉ. धर्म सिंह सैनी',
    'designation' => 'पूर्व कैबिनेट मंत्री (उत्तर प्रदेश)',
    'facebook_handle' => 'Dharam.singhsaini',
    'instagram_handle' => 'Drdharamsinghsaini',
    'phone_number' => '94122xxxxx',
]);
$branding->setRelation('party', $party);

$strip = App\Models\LeaderStrip::where('party_id', $party->id)->where('is_system', true)->with('leaders')->first();
if (!$strip || $strip->leaders->isEmpty()) {
    $mockLeaders = collect([
        new App\Models\PoliticalLeader(['name' => 'Narendra Modi', 'image' => 'images/leaders/modiji.png']),
        new App\Models\PoliticalLeader(['name' => 'Amit Shah', 'image' => 'images/leaders/amitshah.jpg']),
        new App\Models\PoliticalLeader(['name' => 'Yogi Adityanath', 'image' => 'images/leaders/yogiadityanath.jpg']),
        new App\Models\PoliticalLeader(['name' => 'Keshav Prasad Maurya', 'image' => 'images/leaders/keshavprshadmorya.jpg']),
        new App\Models\PoliticalLeader(['name' => 'Atal Bihari Vajpayee', 'image' => 'images/leaders/atalbiharibajpyee.jpg']),
        new App\Models\PoliticalLeader(['name' => 'J.P. Nadda', 'image' => 'images/leaders/modiji.png']),
    ]);
    $strip = new App\Models\LeaderStrip(['name' => 'Official Leadership Strip']);
    $strip->setRelation('leaders', $mockLeaders);
}

$bgPath = public_path('images/templates/devotthan-ekadashi.jpg');
$cutout = public_path('images/leaders/modiji.png');

// 1. Layer 5: Candidate Left Ribbon
$cfg5 = $composer->getDefaultPresetConfig('candidate_left_ribbon');
$canvas5 = $composer->compose($bgPath, $branding, $strip, [
    'preset_key' => 'candidate_left_ribbon',
    'layout_config' => $cfg5,
    'cutout_url' => $cutout,
]);
$out5 = storage_path('app/public/preview_devotthan_layer5.jpg');
$canvas5->toJpeg(92)->save($out5);
echo "SUCCESS_LAYER_5: " . $out5 . " (" . filesize($out5) . " bytes)\n";

// 2. Layer 6: Modern Capsule Dock
$cfg6 = $composer->getDefaultPresetConfig('modern_capsule_dock');
$canvas6 = $composer->compose($bgPath, $branding, $strip, [
    'preset_key' => 'modern_capsule_dock',
    'layout_config' => $cfg6,
    'cutout_url' => $cutout,
]);
$out6 = storage_path('app/public/preview_devotthan_layer6.jpg');
$canvas6->toJpeg(92)->save($out6);
echo "SUCCESS_LAYER_6: " . $out6 . " (" . filesize($out6) . " bytes)\n";
