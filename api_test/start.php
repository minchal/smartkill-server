<?php
$session = 'ddc20b1a7bb1eb20b5eefd3a59f458ff';
$match   = '1';

$c = curl_init();
curl_setopt($c, CURLOPT_URL, 'http://localhost/smartkill/app_dev.php/api/start');
curl_setopt($c, CURLOPT_POST, 1);
curl_setopt($c, CURLOPT_POSTFIELDS, 'id='.$session.'&match='.$match);
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
$page = curl_exec($c);
curl_close($c);

echo $page;

/*
 * example:
 * {"status":"success"}
 */