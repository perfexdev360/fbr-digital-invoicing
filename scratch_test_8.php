<?php

require_once __DIR__ . '/src/Constants/Scenarios.php';
use Fbr\DigitalInvoicing\Constants\Scenarios;

$bearerToken = 'd5166958-c131-3bf0-b0e0-ff0ccff78af8';
$workingNTN = '5076033';
$scenarioId = 'SN005';

$invoiceData = Scenarios::generateScenarioInvoice($scenarioId);
$invoiceData['sellerNTNCNIC'] = $workingNTN;
$invoiceData['invoiceRefNo'] = $scenarioId . '-' . date('YmdHis') . rand(100, 999);
$invoiceData['invoiceDate'] = date('Y-m-d');

// Only unset extraTax
unset($invoiceData['items'][0]['extraTax']);

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'https://gw.fbr.gov.pk/di_data/v1/di/validateinvoicedata_sb',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 20,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode($invoiceData, JSON_PRESERVE_ZERO_FRACTION),
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $bearerToken
    ],
]);
$response = curl_exec($curl);
echo "Result when only unsetting extraTax: $response\n";

// Only unset furtherTax
$invoiceData = Scenarios::generateScenarioInvoice($scenarioId);
$invoiceData['sellerNTNCNIC'] = $workingNTN;
$invoiceData['invoiceRefNo'] = $scenarioId . '-' . date('YmdHis') . rand(100, 999);
$invoiceData['invoiceDate'] = date('Y-m-d');
unset($invoiceData['items'][0]['furtherTax']);
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'https://gw.fbr.gov.pk/di_data/v1/di/validateinvoicedata_sb',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 20,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode($invoiceData, JSON_PRESERVE_ZERO_FRACTION),
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $bearerToken
    ],
]);
$response = curl_exec($curl);
echo "Result when only unsetting furtherTax: $response\n";

