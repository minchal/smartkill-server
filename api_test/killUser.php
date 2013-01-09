<?php
$session = 'e4df15925581e3db9b40c2f5a399d29e';
$match   = '2';
$user    = '1';
$lat     = '51.20';
$lng     = '19.20';

$c = curl_init();
curl_setopt($c, CURLOPT_URL, 'http://localhost/smartkill/app_dev.php/api/killUser');
curl_setopt($c, CURLOPT_POST, 1);
curl_setopt($c, CURLOPT_POSTFIELDS, 'id='.$session.'&match='.$match.'&hunter='.$user.'&lat='.$lat.'&lng='.$lng);
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
$page = curl_exec($c);
curl_close($c);

echo '<pre>'.htmlspecialchars($page);

/*
 * example:
 * {"status":"success"}
 */