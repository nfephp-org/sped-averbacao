<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once '../bootstrap.php';

use NFePHP\Averbacao\Tools;
use NFePHP\Averbacao\Common\Standardize;

$chave = 'xml';
$cXml = file_get_contents("{$chave}-procCTe.xml");
/*
 * usuario na AT&M 
 */
$cUsuario = 'WS';
/*
 * senha na AT&M 
 */
$cSenha = 'base';
/*
 * código na AT&M
 */
$cCodigo = '99999999';
/*
 * ambiente
 */
$tpAmb = 2;
/*
 * tipo do xml
 */
$cTipo = 'CTE';

$tools = new NFePHP\Averbacao\Tools($cUsuario,$cSenha,$cCodigo,$tpAmb);

$response = $tools->sefazAverbaXml($cXml,$cTipo);

$stdCl = new Standardize($response);
$arr = $stdCl->toArray();
$std = $stdCl->toStd();
print_r($std);
