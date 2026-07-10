<?php
require 'src/Constants/Scenarios.php';
$i = Fbr\DigitalInvoicing\Constants\Scenarios::generateScenarioInvoice('SN005');
echo json_encode($i, JSON_PRETTY_PRINT);
