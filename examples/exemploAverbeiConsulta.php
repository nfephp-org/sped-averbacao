<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once '../bootstrap.php';

use NFePHP\Averbacao\Averbei;

/*
 * token de acesso fornecido pela Averbei
 */
$token = 'SEU_TOKEN';
/*
 * ambiente: 1 - Produção | 2 - Homologação
 */
$tpAmb = 2;
/*
 * file_id (averbação) ou canceled_file_id (cancelamento) retornado no envio
 */
$fileId = 'FILE_ID_RETORNADO_NO_ENVIO';

try {
    $averbei = new Averbei($token, $tpAmb);
    $response = $averbei->consult($fileId);
    $std = json_decode($response);
    echo "HTTP: {$averbei->httpCode} <br>";
    echo "<pre>";
    print_r($std !== null ? $std : $response);
    echo "</pre>";
} catch (\Exception $e) {
    echo "Ocorreu um erro: {$e->getMessage()}.";
}
