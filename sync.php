<?php
// রেন্ডার এনভায়রনমেন্ট থেকে ডাটা নিচ্ছে
$supabaseUrl = getenv('SUPABASE_URL');
$supabaseKey = getenv('SUPABASE_KEY');

$cpagripUrl = "https://www.cpagrip.com/common/offer_feed_json.php?user_id=2441114&key=7f0b09da3b2d682f1189d1d6abbf24fc&showall=1";

$feed = file_get_contents($cpagripUrl);
$data = json_decode($feed, true);

$offers = $data['offers'] ?? []; 

if (empty($offers)) {
    die("CPAGrip থেকে অফার পাওয়া যায়নি!");
}

foreach ($offers as $o) {
    $link = $o['offerlink'] ?? '#';
    $cat  = $o['category'] ?? 'General Offer';
    
    // ❌ সার্ভে অফার বাদ দেওয়ার লজিক
    // যদি ক্যাটাগরিতে 'survey' শব্দটি থাকে, তবে এই অফারটি স্কিপ করে পরের অফারে চলে যাবে
    if (stripos($cat, 'survey') !== false) {
        continue; 
    }

    $taskLabel = $cat; 

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
            "title"     => $o['title'] ?? 'No Title',
            "link"      => $link,
            "payout"    => (float)($o['payout'] ?? 0),
            "image"     => $o['offerphoto'] ?? '',
            "country"   => $o['accepted_countries'] ?? 'Unknown',
            "platform"  => "CPAGrip",
            "task_type" => $taskLabel 
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

echo "Sync Complete! সার্ভে ছাড়া সব অফার সফলভাবে সেভ হয়েছে।";
?>
