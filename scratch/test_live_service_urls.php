<?php

$urls = [
    'https://lifeguarddatarecovery.com/hard-drive-data-recovery-dubai/',
    'https://lifeguarddatarecovery.com/service/hard-drive-data-recovery-dubai/',
    'https://lifeguarddatarecovery.com/cctv-footage-recovery-dubai/',
];
foreach ($urls as $u) {
    $ch = curl_init($u);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $redirect = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    echo "$code: $u -> $redirect\n";
}
