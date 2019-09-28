<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once '../bootstrap.php';

use NFePHP\Averbacao\Porto;

$user = "11111111111111";
$pass = "1111";

try {
    $averb = new Porto($user, $pass);
    $averb->login();
    $response = $averb->consult('31180758818022000224570010467456121401366424');
    $std = json_decode($response);
    if ($std->success) {
        foreach($std->S as $info) {
            echo "Chave : {$info->chave} <br>";
            echo "Protocolo : {$info->protocolo} <br>";
        }
    } else {
        echo "Sua consulta falhou. <br>";
        echo "<pre>";
        print_r($std);
        echo "</pre>";
    }
} catch (\Exception $e) {
    echo "Ocorreu um erro: {$e->message}.";
}
  