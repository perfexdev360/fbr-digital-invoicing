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
        if ($k === 'scenarioId') {
            if ($v === null) {
                unset($invoiceData['scenarioId']);
            } else {
                $invoiceData['scenarioId'] = $v;
            }
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

// Test SN012 with Liters
test('SN012', [
    'hsCode' => '2710.1210',
    'uoM' => 'Liters',
    'petroleumLevyOn' => 'Value',
    'petroleumLevyRate' => 1,
    'petroleumLevyAmount' => 1
]);

test('SN012', [
    'hsCode' => '2710.1210',
    'uoM' => 'Litres',
    'petroleumLevyOn' => 'Quantity',
    'petroleumLevyRate' => 1,
    'petroleumLevyAmount' => 1
]);

// Test SN011 without scenarioId
test('SN011', [
    'scenarioId' => null
]);
