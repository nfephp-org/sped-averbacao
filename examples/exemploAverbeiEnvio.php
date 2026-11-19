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
 * XML autorizado (CT-e, NF-e, MDF-e ou documento não fiscal) para averbação/declaração,
 * ou XML do evento: cancelamento (CT-e, NF-e e MDF-e), encerramento ou inclusão de condutor (MDF-e).
 * Os eventos do MDF-e devem ter a raiz procEventoMDFe, outras raízes são rejeitadas como arquivo inválido.
 */
$xml = file_get_contents('cte.xml');

try {
    $averbei = new Averbei($token, $tpAmb);
    $response = $averbei->send($xml);
    $std = json_decode($response);
    $name = $std->name ?? '';
    /*
     * o resultado é identificado pelo código HTTP e pela chave "name" do JSON
     */
    if ($averbei->httpCode === 201 || ($averbei->httpCode === 422 && $name === 'AlreadyAssuredError')) {
        //SuccessfullyAverbation ou AlreadyAssuredError (já averbado antes, retorna a averbação existente)
        echo "Averbação: {$std->averbation_number} <br>";
        echo "Protocolo: {$std->protocol} <br>";
        echo "file_id (usado na consulta): {$std->file_id} <br>";
    } elseif ($averbei->httpCode === 200 || ($averbei->httpCode === 422 && $name === 'AlreadyCanceledError')) {
        //evento aceito: SuccessfullyCancellation, encerramento/inclusão de condutor do MDF-e
        //ou AlreadyCanceledError (cancelamento já recebido antes)
        echo "Evento aceito. <br>";
        echo "Protocolo: {$std->protocol} <br>";
        if (isset($std->canceled_file_id)) {
            echo "canceled_file_id (usado na consulta): {$std->canceled_file_id} <br>";
        }
    } else {
        //ex.: InvalidDocumentError, ContentDocumentError (indica a tag), SessionError, UnauthorizedError
        echo "Falha no envio (HTTP {$averbei->httpCode}). <br>";
        echo "<pre>";
        print_r($std !== null ? $std : $response);
        echo "</pre>";
    }
} catch (\Exception $e) {
    echo "Ocorreu um erro: {$e->getMessage()}.";
}
