<?php

$bearerToken = 'd5166958-c131-3bf0-b0e0-ff0ccff78af8';

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'https://gw.fbr.gov.pk/pdi/v1/uom',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $bearerToken
    ],
]);
$uoms = json_decode(curl_exec($curl), true);
curl_close($curl);

print_r($uoms);
