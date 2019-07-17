<?php

namespace NFePHP\Averbacao;

/**
 * Class responsible for communication with SEFAZ extends
 * NFePHP\CTe\Common\Tools
 *
 * @category  NFePHP
 * @package   NFePHP\Averbacao\Tools
 * @copyright NFePHP Copyright (c) 2008-2019
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-cte for the canonical source repository
 */

use NFePHP\Common\Strings;
use NFePHP\Common\Signer;
use NFePHP\Averbacao\Common\Tools as ToolsCommon;
use RuntimeException;
use InvalidArgumentException;

class Tools extends ToolsCommon
{
    /**
     * @var string
     */
    public $cUsuario = '';
	
    /**
     * @var string
     */
    public $cSenha = '';

    /**
     * @var string
     */
    public $cCodigo = '';

    /**
     * @var int
     */
    public $tpAmb = 1;

    /**
     * Set Information
     * @param string $cUsuario of user
     * @param string $cSenha of password
     * @param string $cCodigo of number
     * @param int $tpAmb of number
     */
    public function __construct($cUsuario,$cSenha,$cCodigo,$tpAmb)
    {
        $this->cUsuario = $cUsuario;
        $this->cSenha = $cSenha;
        $this->cCodigo = $cCodigo;
        $this->tpAmb = $tpAmb;
    }
 
    /**
     * Request authorization to issue XML  in batch with one or more documents
     * @param $cXml of CTe or MDFe
     * @param int $cTipo of type CTE,MDFe or NFe
     * @return string soap response xml
     */
    public function sefazAverbaXml($cXml,$cTipo)
    {
        if (empty($cXml)) {
            throw new \InvalidArgumentException('Um XML do (CTe,MDFe,NFe), protocolado deve ser passado.');
        }
        if (empty($this->cUsuario)) {
            throw new \InvalidArgumentException('O usuário da AT&M deve ser passado.');
        }
        if (empty($this->cSenha)) {
            throw new \InvalidArgumentException('A senha da AT&M deve ser passada.');
        }
        if (empty($this->cCodigo)) {
            throw new \InvalidArgumentException('O código da AT&M deve ser passada.');
        }
        switch($cTipo) {
            case 'CTE':
                $cTag = 'averbaCTe';
                $this->cAction = 'urn:ATMWebSvr#averbaCTe';
                break;
            case 'MDFE':
                $cTag = 'averbaMDFe';
                $this->cAction = 'urn:ATMWebSvr#declaraMDFe';
                break;
            case 'NFE':
                $cTag = 'averbaNFe';
                $this->cAction = 'urn:ATMWebSvr#declaraNFe';
                break;
            default:
				throw new \InvalidArgumentException('O tipo do XML foi informado errado.');
        }
        if ($this->tpAmb == 1) {
            $this->cUrl = 'http://webserver.averba.com.br/20/index.soap?wsdl';
            $this->cHost = 'ws.averba.com.br';
        } else {
            $this->cUrl = 'http://homologaws.averba.com.br/20/index.soap?wsdl';
            $this->cHost = 'homologaws.averba.com.br';
        }
        $request = "<$cTag>"
            . "<usuario>$this->cUsuario</usuario>"
            . "<senha>$this->cSenha</senha>"
            . "<codatm>$this->cCodigo</codatm>"
            . "<xmlCTe><![CDATA[$cXml]]></xmlCTe>"
            . "</$cTag>";
        $request = Strings::clearXmlString($request, true);
        $cXmlSoap     = '<?xml version="1.0" encoding="utf-8"?>';
        $cXmlSoap    .= '<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
						 xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
						 xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
						 xmlns:urn="urn:ATMWebSvr">';
        $cXmlSoap    .= '<soapenv:Body>';
        $cXmlSoap    .= $request;
        $cXmlSoap    .= '</soapenv:Body>';
        $cXmlSoap    .= '</soapenv:Envelope>';
        $this->lastResponse = $this->sendRequest($cXmlSoap);
        return $this->lastResponse;
    }
}
