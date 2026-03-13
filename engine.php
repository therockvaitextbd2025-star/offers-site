<?php

function load_offers($country,$user_id){

global $cpagripKey,$ogadsKey;

$offers=[];

/* CPAGrip */
// এখানে তোমার পাবলিক কি এবং প্রাইভেট কি দুটোই একসাথে ব্যবহার করছি
$url = "https://www.cpagrip.com/common/offer_feed_json.php?pubkey=e90d61c5da43075bef08de5cb528bce2&key=7f0b09da3b2d682f1189d1d6abbf24fc&country=$country&tracking_id=$user_id";

$feed = @file_get_contents($url);
$data = json_decode($feed, true);

if(isset($data['offers'])){
    foreach($data['offers'] as $o){
        $offers[]=[
            "title"=>$o['title'],
            "link"=>$o['offerlink'],
            "payout"=>$o['payout'],
            "image"=>$o['thumbnail']
        ];
    }
}



/* OGAds */

if($ogadsKey){

$feed2=@file_get_contents(
"https://api.ogads.com/v1/offers?api_key=$ogadsKey&country=$country"
);

$data2=json_decode($feed2,true);

if(isset($data2['offers'])){

foreach($data2['offers'] as $o){

$offers[]=[
"title"=>$o['name'],
"link"=>$o['link'],
"payout"=>$o['payout'],
"image"=>$o['image']
];

}

}

}

/* FILTER */

$filtered=[];

foreach($offers as $o){

if($o['payout'] < 0.10) continue;

if(empty($o['link'])) continue;

$filtered[]=$o;

}

/* REMOVE DUPLICATE */

$unique=[];

foreach($filtered as $o){

$key=md5($o['title']);

$unique[$key]=$o;

}

$filtered=array_values($unique);

/* SORT */

usort($filtered,function($a,$b){

return $b['payout'] <=> $a['payout'];

});

/* LIMIT */

return array_slice($filtered,0,50);

}
