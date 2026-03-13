<?php
// ১. ইউজার ডাটাবেজ থেকে কান্ট্রি কোড পাওয়ার ফাংশন
function get_user_country($user_id) {
    $supabaseUrl = getenv('SUPABASE_URL');
    $supabaseKey = getenv('SUPABASE_KEY');
    
    $url = "$supabaseUrl/rest/v1/user_data?id=eq.$user_id&select=country_code";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["apikey: $supabaseKey", "Authorization: Bearer $supabaseKey"]);
    $result = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($result, true);
    return $data[0]['country_code'] ?? 'US'; // যদি কিছু না পায় তবে 'US' ডিফল্ট হিসেবে থাকবে
}

// ২. কান্ট্রি অনুযায়ী অফার লোড করার ফাংশন
function load_offers($user_id) {
    $supabaseUrl = getenv('SUPABASE_URL');
    $supabaseKey = getenv('SUPABASE_KEY');
    
    // প্রথমে কান্ট্রি কোড বের করুন
    $country = get_user_country($user_id);

    // Supabase থেকে অফারগুলো আনুন
    $url = "$supabaseUrl/rest/v1/all_offers?country=ilike.*$country*&select=*";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["apikey: $supabaseKey", "Authorization: Bearer $supabaseKey"]);
    $offers = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($offers, true);
}
?>
