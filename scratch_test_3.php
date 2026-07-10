<?php

require_once __DIR__ . '/src/Constants/Scenarios.php';
use Fbr\DigitalInvoicing\Constants\Scenarios;

$bearerToken = 'd5166958-c131-3bf0-b0e0-ff0ccff78af8';
$workingNTN = '5076033';

function test($scenarioId, $overrides = []) {
    global $bearerToken, $workingNTN;
    $invoiceData = Scenarios::generateScenarioInvoice($scenarioId);
    $invoiceData['sellerNTNCNIC'] = $workingNTN;
    $invoiceData['invoiceRefNo'] = $scenarioId . '-' . date('YmdHis') . rand(100, 999);
    $invoiceData['invoiceDate'] = date('Y-m-d');
    
    foreach ($overrides as $k => $v) {
        if ($k === 'buyerRegistrationType') {
            $invoiceData[$k] = $v;
            continue;
        }
        $invoiceData['items'][0][$k] = $v;
    }

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

// Test SN005 with lowercase saleType
test('SN005', [
    'saleType' => 'Goods at reduced rate',
    'furtherTax' => 0, 
    'fedPayable' => 0, 
    'discount' => 0, 
    'salesTaxWithheldAtSource' => 0,
    'extraTax' => 0
]);

// Test SN028 with lowercase saleType
test('SN028', [
    'saleType' => 'Goods at reduced rate',
    'buyerRegistrationType' => 'Unregistered'
]);

// Test SN011 with no scenarioId in the payload?
// test('SN011', ['scenarioId' => '']); 
// Actually Fbr API uses scenarioId 'SN011' and it says it doesn't exist?
// Let's try SN011 with SN003's saleType
test('SN011', [
    'saleType' => 'Steel melting and re-rolling'
]);

// Test SN012 with correct HS Code
test('SN012', [
    'hsCode' => '2710.1210',
    'petroleumLevyOn' => 'Value',
    'petroleumLevyRate' => 1,
    'petroleumLevyAmount' => 1
]);
test('SN012', [
    'hsCode' => '2710.1931',
    'petroleumLevyOn' => 'Quantity',
    'petroleumLevyRate' => 1,
    'petroleumLevyAmount' => 1
]);
