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

// 1. Layer 1: Dual Ribbon Badge
$cfg1 = $composer->getDefaultPresetConfig('dual_ribbon_badge');
$canvas1 = $composer->compose($bgPath, $branding, $strip, [
    'preset_key' => 'dual_ribbon_badge',
    'layout_config' => $cfg1,
    'cutout_url' => $cutout,
]);
$out1 = storage_path('app/public/preview_devotthan_layer1.jpg');
$canvas1->toJpeg(92)->save($out1);
echo "SUCCESS_LAYER_1: " . $out1 . " (" . filesize($out1) . " bytes)\n";

// 2. Layer 2: Solid White Banner
$cfg2 = $composer->getDefaultPresetConfig('solid_white_banner');
$canvas2 = $composer->compose($bgPath, $branding, $strip, [
    'preset_key' => 'solid_white_banner',
    'layout_config' => $cfg2,
    'cutout_url' => $cutout,
]);
$out2 = storage_path('app/public/preview_devotthan_layer2.jpg');
$canvas2->toJpeg(92)->save($out2);
echo "SUCCESS_LAYER_2: " . $out2 . " (" . filesize($out2) . " bytes)\n";
