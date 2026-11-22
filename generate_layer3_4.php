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

// 1. Layer 3: Floating Tricolor Badge
$cfg3 = $composer->getDefaultPresetConfig('floating_tricolor_badge');
$canvas3 = $composer->compose($bgPath, $branding, $strip, [
    'preset_key' => 'floating_tricolor_badge',
    'layout_config' => $cfg3,
    'cutout_url' => $cutout,
]);
$out3 = storage_path('app/public/preview_devotthan_layer3.jpg');
$canvas3->toJpeg(92)->save($out3);
echo "SUCCESS_LAYER_3: " . $out3 . " (" . filesize($out3) . " bytes)\n";

// 2. Layer 4: Royal Gold Banner
$cfg4 = $composer->getDefaultPresetConfig('royal_gold_dock');
$canvas4 = $composer->compose($bgPath, $branding, $strip, [
    'preset_key' => 'royal_gold_dock',
    'layout_config' => $cfg4,
    'cutout_url' => $cutout,
]);
$out4 = storage_path('app/public/preview_devotthan_layer4.jpg');
$canvas4->toJpeg(92)->save($out4);
echo "SUCCESS_LAYER_4: " . $out4 . " (" . filesize($out4) . " bytes)\n";
