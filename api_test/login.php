<?php

$c = curl_init();
curl_setopt($c, CURLOPT_URL, 'http://localhost/smartkill/app_dev.php/api/login');
curl_setopt($c, CURLOPT_POST, 1);
curl_setopt($c, CURLOPT_POSTFIELDS, 'username=kiler&password=qwert');
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
$page = curl_exec($c);
curl_close($c);

echo '<pre>'.htmlspecialchars($page);

/*
 * example:
 * {"status":"success","id":"ddc20b1a7bb1eb20b5eefd3a59f458ff","user":{"id":1,"username":"szakal","email":"szakal@ikillyou.com","is_active":true,"admin":false,"registered_at":"2013-01-09T00:49:26+0100","points_prey":0,"points_hunter":0,"matches_prey":0,"matches_hunter":0}}
 */