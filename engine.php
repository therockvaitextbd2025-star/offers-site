<?php
function load_offers($country, $user_id) {
    global $cpagripKey, $ogadsKey;
    $offers = [];

    // --- DEBUG START ---
    // এই লিঙ্কটি কপি করে ব্রাউজারে চেক করুন অফার আছে কি না
    $test_url = "https://www.cpagrip.com/common/offer_feed_json.php?pubkey=$cpagripKey&country=$country&tracking_id=$user_id";
    // --- DEBUG END ---

    $feed = @file_get_contents($test_url);
    $data = json_decode($feed, true);

    // ডিবাগিংয়ের জন্য: যদি ডাটা না আসে তবে এখানে এরর দেখাবে
    if (!$data) {
        return [["title" => "DEBUG: Error connecting to feed. URL: " . $test_url, "link" => "#", "payout" => "0", "image" => ""]];
    }

    if (isset($data['offers'])) {
        foreach ($data['offers'] as $o) {
            $offers[] = [
                "title" => $o['title'] ?? 'No Title',
                "link" => $o['offerlink'] ?? '#',
                "payout" => $o['payout'] ?? '0',
                "image" => $o['thumbnail'] ?? ''
            ];
        }
    } else {
        // যদি ডাটা আসে কিন্তু অফার না থাকে
        return [["title" => "No offers found for country: " . $country, "link" => "#", "payout" => "0", "image" => ""]];
    }

    return array_slice($offers, 0, 50);
}
?>
