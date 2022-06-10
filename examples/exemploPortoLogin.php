<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once '../bootstrap.php';

use NFePHP\Averbacao\Porto;

$user = "11111111111111";
$pass = "1111";

try {
    $averb = new Porto($user, $pass);
    if ($averb->login()) {
        echo "Você está logado";
    }
} catch (\Exception $e) {
    echo $e->getMessage();
}
