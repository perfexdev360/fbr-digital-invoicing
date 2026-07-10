<?php

require __DIR__ . '/vendor/autoload.php';

// Wait, this is a laravel package, so I need to bootstrap Laravel.
// Let's just create a new app instance if possible or directly call the API.
// Better yet, use curl to call the API directly.

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

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'https://gw.fbr.gov.pk/pdi/v1/hscodes',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $bearerToken
    ],
]);
$hscodes = json_decode(curl_exec($curl), true);
curl_close($curl);

echo "Total UOMs: " . count($uoms) . "\n";
foreach($uoms as $u) {
    if (stripos($u['name'] ?? '', 'liter') !== false || stripos($u['name'] ?? '', 'lit') !== false) {
        echo "UOM: " . ($u['name'] ?? '') . "\n";
    }
}

// Find petroleum HS codes
$foundHs = [];
foreach($hscodes as $hs) {
    $desc = strtolower($hs['name'] ?? '');
    if (strpos($desc, 'petroleum') !== false || strpos($desc, 'diesel') !== false || strpos($desc, 'petrol') !== false) {
        $foundHs[] = $hs;
        if (count($foundHs) > 5) break;
    }
}
print_r($foundHs);
