<?php
$bearerToken = 'd5166958-c131-3bf0-b0e0-ff0ccff78af8';

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'https://gw.fbr.gov.pk/pdi/v1/transtypes', // maybe?
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $bearerToken
    ],
]);
$transTypes = curl_exec($curl);
curl_close($curl);
echo "transtypes:\n";
print_r(json_decode($transTypes, true));

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'https://gw.fbr.gov.pk/pdi/v1/saletypes', // guess
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $bearerToken
    ],
]);
$saleTypes = curl_exec($curl);
curl_close($curl);
echo "saletypes:\n";
print_r(json_decode($saleTypes, true));
