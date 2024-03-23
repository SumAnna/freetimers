<?php

require('vendor/autoload.php');

use App\Classes\Calculation;

$calculation = new Calculation();
$connection = $calculation->getConnection();
$units = $connection->findAll('units');
$unitsMeasurement = [];
$unitsDepth = [];
$errors = '';

foreach ($units as $unit) {
    if ($unit['depth_unit']) {
        $unitsDepth[] = $unit;
    }

    if ($unit['measurement_unit']) {
        $unitsMeasurement[] = $unit;
    }
}

$csrf = hash_hmac('sha256', 'sending-calculator-value', 'qwerty123');

$csrfBasket = hash_hmac('sha256', 'sending-basket-value','qwerty123');

if (empty($_COOKIE['basket'])) {
    setcookie('basket', md5(uniqid()), time() + (3600 * 1000), '/');
}

$basketCounter = $calculation->getUserBasket($_COOKIE['basket']);

require_once('views/layout.php');