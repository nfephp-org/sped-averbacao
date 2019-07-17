<?php

namespace NFePHP\Averbacao\Common\Soap;

/**
 * SoapClient based in cURL class
 *
 * @category  NFePHP
 * @package   NFePHP\Averbacao\Common\Soap\SoapCurl
 * @copyright NFePHP Copyright (c) 2016-2019
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-common for the canonical source repository
 */

use NFePHP\Common\Exception\SoapException;
use Psr\Log\LoggerInterface;

class SoapCurl
{
    /**
     * @var string
     */
    public $responseBody;
    /**
     * @var string
     */
    public $soaperror;
    /**
     * @var string
     */
    public $soapinfo;

    /**
     * Send soap message to url
     * @param string $cUrl
     * @param string $cXmlSoap
     * @param string $cAction
     * @param int $nSize
     * @param string $cHost
     * @param \SoapHeader $soapheader
     * @throws \NFePHP\Common\Exception\SoapException
     */
    public function send(
        $cUrl = '',
        $cXmlSoap = '',
        $cAction = '',
        $nSize = 0,
        $cHost = ''
    ) {
        try {
            $oCurl = curl_init();
            curl_setopt($oCurl, CURLOPT_URL, $cUrl);
            curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($oCurl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            curl_setopt($oCurl, CURLOPT_HEADER, 0);
            curl_setopt($oCurl, CURLOPT_TIMEOUT, 5);
            curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($oCurl, CURLOPT_POST, 1);
            curl_setopt($oCurl, CURLOPT_POSTFIELDS, $cXmlSoap);
            curl_setopt($oCurl, CURLOPT_HTTPHEADER, [
                "Content-Type: text/xml;charset=UTF-8",
                "SOAPAction: '$cAction'",
                "Content-Length: $nSize",
                "Host: $cHost",
                "Connection: Keep-Alive",
                "User-Agent: Apache-HttpClient/4.1.1 (java 1.5)"]);
            $response = curl_exec($oCurl);
            $this->soaperror = curl_error($oCurl);
            $ainfo = curl_getinfo($oCurl);
            if (is_array($ainfo)) {
                $this->soapinfo = $ainfo;
            }
            curl_close($oCurl);
            $this->responseBody = trim($response);
        } catch (\Exception $e) {
            throw SoapException::unableToLoadCurl($e->getMessage());
        }
        if ($this->soaperror != '') {
            throw SoapException::soapFault($this->soaperror . " [$cUrl]");
        }
        return $this->responseBody;
    }
}
