<?php
$session = 'ddc20b1a7bb1eb20b5eefd3a59f458ff';

$c = curl_init();
curl_setopt($c, CURLOPT_URL, 'http://localhost/smartkill/app_dev.php/api/userGames');
curl_setopt($c, CURLOPT_POST, 1);
curl_setopt($c, CURLOPT_POSTFIELDS, 'id='.$session);
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
$page = curl_exec($c);
curl_close($c);

echo '<pre>'.htmlspecialchars($page);

/*
 * example:
 * {"status":"success","games":[{"id":1,"status":"goingon","name":"Meczyk","descr":"Taki sobie mecz","password":"marek","lat":"51.110000","lng":"17.060000","size":500,"length":60,"due_date":"2013-01-10T00:08:00+0100","start_date":"2013-01-09T09:24:17+0100","max_players":2,"created_at":"2013-01-09T09:08:43+0100","created_by":1,"players":{"count":1},"density":10,"pkg_time":true,"pkg_shield":false,"pkg_snipe":true,"pkg_switch":false},{"id":2,"status":"planed","name":"Nowy mecz","descr":"Jaki\u015b tam mecz","password":"meczmecz","lat":"51.110000","lng":"17.060000","size":500,"length":60,"due_date":"2013-01-10T00:32:00+0100","max_players":2,"created_at":"2013-01-09T09:33:03+0100","created_by":1,"players":{"count":2},"density":10,"pkg_time":true,"pkg_shield":true,"pkg_snipe":true,"pkg_switch":true}]}
 */