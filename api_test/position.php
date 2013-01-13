<?php
$session = 'ddc20b1a7bb1eb20b5eefd3a59f458ff';
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
 * {"status":"success","user":{"username":"szakal","user":1,"match":3,"type":"hunter","lat":"51.20","lng":"19.20","offense":0,"disqualification":false,"updated_at":"2013-01-13T23:36:50+0100","points_prey":0,"points_hunter":0,"alive":true},"positions":[{"username":"kiler","user":2,"match":3,"type":"prey","lat":"51.20","lng":"19.20","offense":0,"disqualification":false,"updated_at":"2013-01-13T23:13:57+0100","points_prey":0,"points_hunter":0,"alive":false}]}
 */