<?php

use App\Classes\Calculation;

require('vendor/autoload.php');

$result = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (
        empty($_POST['unit']) || $_POST['unit'] <= 0 ||
        empty($_POST['unitDepth']) || $_POST['unitDepth'] <= 0 ||
        empty($_POST['length']) || $_POST['length'] <= 0 ||
        empty($_POST['width']) || $_POST['width'] <= 0 ||
        empty($_POST['depth']) || $_POST['depth'] <= 0
    ) {
        $result['errors'][] = 'Please insert all the required values.';
    } elseif ($_POST['csrf'] === hash_hmac('sha256', 'sending-calculator-value', 'qwerty123')) {
        $calculation = new Calculation();

        if (!$calculation->setUnitId((int) $_POST['unitDepth'], true)) {
            $result['errors'][] = 'Could not find the specified depth unit.';
        }

        if(!$calculation->setUnitId((int) $_POST['unit'])) {
            $result['errors'][] = 'Could not find the specified measurements unit.';
        }

        try {
            $calculation->setDimensions((float) $_POST['width'], (float) $_POST['length'], (float) $_POST['depth']);
            $calculation->saveCalculation();
            $result['bags'][] = $calculation->getBagsNumber();
            $result['price'][] = $calculation->getBagsPrice();
        } catch (Exception $e) {
            $result['errors'][] = 'Error occurred while processing the request.';
        }
    } else {
        $result['errors'][] = 'Error occurred while processing the csrf token.';
    }

    echo json_encode($result);
}