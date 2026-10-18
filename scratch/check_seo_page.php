<?php

$ch = curl_init('https://lifeguarddatarecovery.com/raid-data-recovery-dubai/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
$headers = curl_exec($ch);
curl_close($ch);

echo $headers;
