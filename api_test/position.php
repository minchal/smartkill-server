<?php
$session = 'ddc20b1a7bb1eb20b5eefd3a59f458ff';//7918c9784e0a21ea5e72b35cfa9303b1
$match   = '3';
$lat     = '51.20';
$lng     = '19.20';

$c = curl_init();
curl_setopt($c, CURLOPT_URL, 'http://localhost/smartkill/app_dev.php/api/position');
curl_setopt($c, CURLOPT_POST, 1);
curl_setopt($c, CURLOPT_POSTFIELDS, 'id='.$session.'&match='.$match.'&lat='.$lat.'&lng='.$lng);
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
$page = curl_exec($c);
curl_close($c);

echo '<pre>'.htmlspecialchars($page);

/*
 * example:
 * {"status":"success","positions":[{"user":2,"match":2,"type":"prey","offense":0,"disqualification":false,"points_prey":0,"points_hunter":0,"alive":false}]}
 */