<?php

$supabaseUrl = "https://iskidvorfxqtefgolcbx.supabase.co";
$supabaseKey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Imlza2lkdm9yZnhxdGVmZ29sY2J4Iiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc2NTUxOTMyOCwiZXhwIjoyMDgxMDk1MzI4fQ.lRsW_G1U3Hob6hMD9pQBNTqeTeeNtDviJiKJd_H7OEc"; // আপনার কী এখানে দিন

$cpagripUrl = "https://www.cpagrip.com/common/offer_feed_json.php?user_id=2441114&key=7f0b09da3b2d682f1189d1d6abbf24fc&showall=1";

$feed = file_get_contents($cpagripUrl);
$data = json_decode($feed, true);

// আসল অফারগুলো এখন এই $offers ভেরিয়েবলে থাকবে
$offers = $data['offers'] ?? []; 

if (empty($offers)) {
    die("CPAGrip থেকে অফার পাওয়া যায়নি!");
}

foreach ($offers as $o) {
    $link = $o['offerlink'] ?? '#';

    // ডাটাবেজে চেক করা
    $checkUrl = "$supabaseUrl/rest/v1/all_offers?link=eq." . urlencode($link);
    $ch = curl_init($checkUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["apikey: $supabaseKey", "Authorization: Bearer $supabaseKey"]);
    $result = curl_exec($ch);
    curl_close($ch);

    $existing = json_decode($result, true);

    if (empty($existing)) {
        $insertData = [
            "title"    => $o['title'] ?? 'No Title',
            "link"     => $link,
            "payout"   => (float)($o['payout'] ?? 0),
            "image"    => $o['offerphoto'] ?? '',
            "country"  => $o['accepted_countries'] ?? 'Unknown', // কান্ট্রির সঠিক কী (Key) ব্যবহার করলাম
            "platform" => "CPAGrip"
        ];

        $ch = curl_init("$supabaseUrl/rest/v1/all_offers");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($insertData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "apikey: $supabaseKey",
            "Authorization: Bearer $supabaseKey",
            "Content-Type: application/json"
        ]);
        curl_exec($ch);
        curl_close($ch);
    }
}

echo "Sync Complete! সব অফার সফলভাবে সেভ হয়েছে।";
?>
