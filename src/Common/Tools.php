<?php

namespace NFePHP\Averbacao\Common;

use NFePHP\Averbacao\Common\Soap\SoapCurl;
use NFePHP\Common\Soap\SoapInterface;

class Tools
{
    /**
     * @var string
     */
    public $cAction;
    /**
     * @var string
     */
    public $cUrl;
    /**
     * @var string
     */
    public $cHost;
    /**
     * soap class
     * @var SoapInterface
     */
    public $soap;
    /**
     * @var string
     */
    public $lastRequest;
    /**
     * @var string
     */
    public $lastResponse;

    /**
     * Send request message to webservice
     * @param array $parameters
     * @param string $request
     * @return string
     */
    protected function sendRequest($request)
    {
        $this->checkSoap();
        return (string)$this->soap->send(
            $this->cUrl,
            $request,
            $this->cAction,
            strlen($request),
            $this->cHost
        );
    }

    protected function checkSoap()
    {
        if (empty($this->soap)) {
            $this->soap = new SoapCurl();
        }
    }

    protected function sendRequestELTSEG($cUrl, $cXml)
    {
        $this->soap = new SoapCurl();
        return (string)$this->soap->sendELTSEG(
            $cUrl,
            $cXml
        );
    }
}
