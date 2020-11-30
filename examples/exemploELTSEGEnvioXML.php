<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once '../bootstrap.php';

use NFePHP\Averbacao\EltSeg;
use NFePHP\Averbacao\Common\Standardize;

$chave = '35201111222333000155570000000307171736327622';
$cXml = file_get_contents("{$chave}-procCTe.xml");

$atm = new EltSeg();

$response = $atm->averbaXml($cXml, $chave.'-procCTe.xml', strlen($cXml), substr($chave,6,14));
$stdCl = new Standardize($response);
$arr = $stdCl->toArray();
$std = $stdCl->toStd();
print_r($std);
