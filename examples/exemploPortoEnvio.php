<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once '../bootstrap.php';

use NFePHP\Averbacao\Porto;

$user = "11111111111111";
$pass = "1111";
$filecontent = file_get_contents('cte.xml');

try {
    $averb = new Porto($user, $pass);
    $averb->login();
    $response = $averb->send($filecontent);
    echo "<pre>";
    print_r($response);
    echo "</pre>";
} catch (\Exception $e) {
     echo "Ocorreu um erro: {$e->message}.";
}
