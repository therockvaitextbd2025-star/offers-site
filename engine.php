<?php

function load_offers($country, $user_id) {
    global $cpagripKey, $ogadsKey;
    $offers = [];

    // আপনার বর্তমান $url লাইনটি মুছে দিয়ে এটি বসান:
$url = "https://www.cpagrip.com/common/offer_feed_json.php?user_id=2441114&key=7f0b09da3b2d682f1189d1d6abbf24fc&country=$country&tracking_id=$user_id&showall=1&ip=1.1.1.1";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $feed = curl_exec($ch);
    curl_close($ch);
    
    // --- লগ চেক করার জন্য এই অংশটুকু যোগ করুন ---
    if(empty($feed)) {
        error_log("DEBUG: CPAGrip feed is empty for country: $country");
    } else {
        error_log("DEBUG: CPAGrip returned data: " . substr($feed, 0, 200)); // লগে প্রথম ২০০ ক্যারেক্টার দেখাবে
    }
    // ------------------------------------------
    
    $data = json_decode($feed, true);

    
    $data = json_decode($feed, true);

    if (is_array($data)) {
        foreach ($data as $o) {
            if (isset($o['title'])) {
                $offers[] = [
                    "title"  => $o['title'],
                    "link"   => $o['offerlink'] ?? '#',
                    "payout" => $o['payout'] ?? '0',
                    "image"  => $o['offerphoto'] ?? '' 
                ];
            }
        }
    }

    /* OGAds */
    if ($ogadsKey) {
        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, "https://api.ogads.com/v1/offers?api_key=$ogadsKey&country=$country");
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        $feed2 = curl_exec($ch2);
        curl_close($ch2);
        
        $data2 = json_decode($feed2, true);
        if (isset($data2['offers'])) {
            foreach ($data2['offers'] as $o) {
                $offers[] = [
                    "title"  => $o['name'],
                    "link"   => $o['link'],
                    "payout" => $o['payout'],
                    "image"  => $o['image']
                ];
            }
        }
    }

    /* FILTER, REMOVE DUPLICATE, SORT, LIMIT */
    $filtered = [];
    foreach ($offers as $o) {
        if ($o['payout'] < 0.10) continue;
        if (empty($o['link'])) continue;
        $filtered[] = $o;
    }

    $unique = [];
    foreach ($filtered as $o) {
        $key = md5($o['title']);
        $unique[$key] = $o;
    }
    $filtered = array_values($unique);

    usort($filtered, function ($a, $b) {
        return $b['payout'] <=> $a['payout'];
    });

    return array_slice($filtered, 0, 50);
}
