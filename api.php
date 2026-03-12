<?php

include "config.php";
include "engine.php";
if(!is_dir('cache')) {
    mkdir('cache', 0777, true);
}

header("Content-Type: application/json");

$user_id=$_GET['user_id'] ?? '';

if(!$user_id){

echo json_encode(["status"=>"error"]);
exit;

}

/* GET COUNTRY */

$opts=[
"http"=>[
"header"=>"apikey: $supabaseKey\r\nAuthorization: Bearer $supabaseKey\r\n"
]
];

$context=stream_context_create($opts);

$res=file_get_contents(
"$supabaseUrl/rest/v1/user_data?user_id=eq.$user_id&select=country",
false,
$context
);

$data=json_decode($res,true);

$country=$data[0]['country'] ?? "BD";

/* CACHE */

$cache_file="cache/".md5($country).".json";

if(file_exists($cache_file) && (time()-filemtime($cache_file))<$CACHE_TIME){

echo file_get_contents($cache_file);

exit;

}

/* LOAD OFFERS */

$offers=load_offers($country,$user_id);

$result=json_encode([
"status"=>"success",
"country"=>$country,
"offers"=>$offers
]);

file_put_contents($cache_file,$result);

echo $result;
