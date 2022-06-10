<?php

namespace NFePHP\Averbacao;

/**
 * Classes para averbação de seguro nos transportes
 *
 * - AT&\M
 *
 * @category  Library
 * @package   NFePHP\Averbacao
 * @copyright NFePHP Copyright (c) 2008-2019
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-averbacao for the canonical source repository
 */

use NFePHP\Averbacao\Common\Tools as ToolsCommon;

class EltSeg extends ToolsCommon
{
    /**
     * Set Information
     * @param string $cUsuario of user
     * @param string $cSenha of password
     * @param string $cCodigo of number
     * @param int $tpAmb of number
     */
    public function __construct()
    {
    }

    /**
     * Request authorization to issue XML  in batch with one or more documents
     * @param $cXml of CTe/MDFe or NFe
     * @param string $cFileName file name
     * @param string $cCNPJ CNPJ issuer
     * @return string soap response xml
     */
    public function averbaXml($cXml, $cFileName, $cCNPJ)
    {
        if (empty($cXml)) {
            throw new \InvalidArgumentException('Um XML do (CTe,MDFe,NFe), protocolado deve ser passado.');
        }
        $nSize = strlen($cXml);
        $cUrl = "http://www.eltseg05.com.br/eltws/eltws.svc/FileUploadXML?"
            . "FileName=$cFileName"
            . "&CNPJ=$cCNPJ"
            . "&Length=$nSize";
        $this->lastResponse = $this->sendRequestELTSEG($cUrl, $cXml);
        return $this->lastResponse;
    }
}
