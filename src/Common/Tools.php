<?php

namespace NFePHP\Averbacao\Common;

/**
 * Class base responsible for communication with SEFAZ
 *
 * @category  NFePHP
 * @package   NFePHP\Averbacao\Common\Tools
 * @copyright NFePHP Copyright (c) 2008-2019
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfe for the canonical source repository
 */

use DOMDocument;
use InvalidArgumentException;
use RuntimeException;
use NFePHP\Common\Signer;
use NFePHP\Averbacao\Common\Soap\SoapCurl ;
use NFePHP\Common\Strings;

class Tools
{
    /**
     * @var string
     */
    public $cAction ;
    /**
     * @var string
     */
    public $cUrl ;
    /**
     * @var string
     */
    public $cHost ;
    /**
     * soap class
     * @var SoapInterface
     */
    public $soap;
    
    /**
     * Send request message to webservice
     * @param array $parameters
     * @param string $request
     * @return string
     */
    protected function sendRequest($request)
    {
        $this->checkSoap();
        return (string) $this->soap->send(
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
}
