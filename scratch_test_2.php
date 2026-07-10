<?php

require_once __DIR__ . '/src/Constants/Scenarios.php';
use Fbr\DigitalInvoicing\Constants\Scenarios;

$bearerToken = 'd5166958-c131-3bf0-b0e0-ff0ccff78af8';
$workingNTN = '5076033';

function testScenarioCustom($scenarioId, $callback) {
    global $bearerToken, $workingNTN;
    $invoiceData = Scenarios::generateScenarioInvoice($scenarioId);
    $invoiceData['sellerNTNCNIC'] = $workingNTN;
    $invoiceData['invoiceRefNo'] = $scenarioId . '-' . date('YmdHis');
    $invoiceData['invoiceDate'] = date('Y-m-d');
    
    $invoiceData = $callback($invoiceData);

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://gw.fbr.gov.pk/di_data/v1/di/validateinvoicedata_sb',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($invoiceData),
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $bearerToken
        ],
    ]);

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    
    echo "$scenarioId Response ($httpCode): $response\n";
}

// Test SN005 by removing the keys entirely from JSON
testScenarioCustom('SN005', function($data) {
    unset($data['items'][0]['furtherTax']);
    unset($data['items'][0]['fedPayable']);
    unset($data['items'][0]['discount']);
    unset($data['items'][0]['salesTaxWithheldAtSource']);
    unset($data['items'][0]['extraTax']);
    return $data;
});

testScenarioCustom('SN028', function($data) {
    $data['buyerRegistrationType'] = 'Unregistered';
    unset($data['items'][0]['furtherTax']);
    unset($data['items'][0]['fedPayable']);
    unset($data['items'][0]['discount']);
    unset($data['items'][0]['salesTaxWithheldAtSource']);
    unset($data['items'][0]['extraTax']);
    return $data;
});

// For SN011: maybe saleType is different?
testScenarioCustom('SN011', function($data) {
    $data['items'][0]['saleType'] = 'Toll manufacturing';
    return $data;
});
testScenarioCustom('SN011', function($data) {
    $data['items'][0]['saleType'] = 'Toll Manufacturing';
    return $data;
});

// For SN012:
testScenarioCustom('SN012', function($data) {
    $data['items'][0]['petroleumLevyOn'] = 'Value';
    return $data;
});
testScenarioCustom('SN012', function($data) {
    $data['items'][0]['petroleumLevyOn'] = 'Quantity';
    return $data;
});

