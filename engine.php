<?php
function get_user_country($user_id) {
    $supabaseUrl = getenv('SUPABASE_URL');
    $supabaseKey = getenv('SUPABASE_KEY');
    
    // কান্ট্রি কোড চেক
    $url = "$supabaseUrl/rest/v1/user_data?id=eq.$user_id&select=country_code";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["apikey: $supabaseKey", "Authorization: Bearer $supabaseKey"]);
    $result = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($result, true);
    return $data[0]['country_code'] ?? 'BD'; // ডিফল্ট BD বা US রাখুন
}

function load_offers($user_id) {
    $supabaseUrl = getenv('SUPABASE_URL');
    $supabaseKey = getenv('SUPABASE_KEY');
    
    $country = get_user_country($user_id);

    // কুয়েরি: দেশ এবং শুধু অ্যান্ড্রয়েড অফার ফিল্টার করা
    $url = "$supabaseUrl/rest/v1/all_offers?country=ilike.*$country*&device=eq.Android&select=*";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["apikey: $supabaseKey", "Authorization: Bearer $supabaseKey"]);
    $offers = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($offers, true);
    
    // টেস্ট করার জন্য (এটি একবার রান করে দেখুন পেজে কী দেখাচ্ছে)
    // echo "User Country: $country <br>";
    // print_r($data); die(); 

    return $data;
}
?>
