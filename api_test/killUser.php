<?php
$session = '760edaf7762b9191c8939fad18bc3b9e';
$match   = '1';
$user    = '2';
$lat     = '51.20';
$lng     = '19.20';

$c = curl_init();
curl_setopt($c, CURLOPT_URL, 'http://localhost/smartkill/app_dev.php/api/killUser');
curl_setopt($c, CURLOPT_POST, 1);
curl_setopt($c, CURLOPT_POSTFIELDS, 'id='.$session.'&match='.$match.'&user='.$user.'&lat='.$lat.'&lng='.$lng);
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
$page = curl_exec($c);
curl_close($c);

echo '<pre>'.htmlspecialchars($page);
