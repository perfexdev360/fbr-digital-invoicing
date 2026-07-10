<?php

require_once __DIR__ . '/src/Constants/Scenarios.php';
use Fbr\DigitalInvoicing\Constants\Scenarios;

$bearerToken = 'd5166958-c131-3bf0-b0e0-ff0ccff78af8';
$workingNTN = '5076033';

function testScenario($scenarioId, $overrides = []) {
    global $bearerToken, $workingNTN;
    $invoiceData = Scenarios::generateScenarioInvoice($scenarioId);
    $invoiceData['sellerNTNCNIC'] = $workingNTN;
    $invoiceData['invoiceRefNo'] = $scenarioId . '-' . date('YmdHis');
    $invoiceData['invoiceDate'] = date('Y-m-d');
    
    foreach ($overrides as $k => $v) {
        if (isset($invoiceData['items'][0][$k])) {
            $invoiceData['items'][0][$k] = $v;
        } else {
            // merge into items[0]
            $invoiceData['items'][0] = array_merge($invoiceData['items'][0], [$k => $v]);
        }
    }
    
    if (isset($overrides['buyerRegistrationType'])) {
        $invoiceData['buyerRegistrationType'] = $overrides['buyerRegistrationType'];
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

// Test SN003
testScenario('SN003', ['valueSalesExcludingST' => 208000, 'salesTaxApplicable' => 37440]);

// Test SN005 without extra taxes
testScenario('SN005', [
    'furtherTax' => 0, 
    'fedPayable' => 0, 
    'discount' => 0, 
    'salesTaxWithheldAtSource' => 0
]);

// Test SN008 (What happens if we make valueSalesExcludingST 0? or 1000.00 string?)
testScenario('SN008', ['valueSalesExcludingST' => 1000]); // Baseline
testScenario('SN008', ['valueSalesExcludingST' => 0]); 
testScenario('SN008', ['valueSalesExcludingST' => 1000.5]); 
testScenario('SN008', ['valueSalesExcludingST' => (float)1000.00]); 

// Test SN011 (Toll manufacturing case sensitivity)
testScenario('SN011', ['saleType' => 'Toll manufacturing']);

// Test SN012 (Petroleum Levy On)
testScenario('SN012', ['petroleumLevyOn' => 'Quantity']);
testScenario('SN012', ['petroleumLevyOn' => 'Value']);

// Test SN026
testScenario('SN026', ['buyerRegistrationType' => 'Unregistered']);

// Test SN027
testScenario('SN027', ['buyerRegistrationType' => 'Unregistered']);

// Test SN028
testScenario('SN028', ['buyerRegistrationType' => 'Unregistered']);
