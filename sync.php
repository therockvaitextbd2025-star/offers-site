<?php
$cpagripUrl = "https://www.cpagrip.com/common/offer_feed_json.php?user_id=2441114&key=7f0b09da3b2d682f1189d1d6abbf24fc&showall=1";

$feed = file_get_contents($cpagripUrl);
$data = json_decode($feed, true);

echo "<pre>";
print_r($data);
echo "</pre>";
?>
