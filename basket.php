<?php

use App\Classes\Calculation;

require('vendor/autoload.php');

$result = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['csrf'] === hash_hmac('sha256', 'sending-basket-value', 'qwerty123')) {
        if (empty($_POST['bagsNumber']) || $_POST['bagsNumber'] <= 0) {
            $result['errors'][] = 'You can\'t add less than 1 bag of topsoil to your basket.';
        } else {
            if (empty($_COOKIE['basket'])) {
                setcookie('basket', md5(uniqid()), time() + (3600 * 1000), '/');
            }
            $calculation = new Calculation();
            try {
                $calculation->addToBasket($_POST['bagsNumber'], $_COOKIE['basket']);
                $result['newBasket'][] = $calculation->getUserBasket($_COOKIE['basket']);
            } catch (Exception $e) {
                $result['errors'][] = 'Error occurred while processing the request.';
            }
        }


    } else {
        $result['errors'][] = 'Error occurred while processing the csrf token.';
    }

    echo json_encode($result);
}