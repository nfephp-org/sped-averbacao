<?php

namespace NFePHP\Averbacao;

/**
 * Classes para averbação de seguro nos transportes
 *
 * - Averbei (API REST: https://averbei.com.br/documentacao/averbacao_integracao/docs.html)
 *
 * @category  Library
 * @package   NFePHP\Averbacao
 * @copyright NFePHP Copyright (c) 2008-2019
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @link      http://github.com/nfephp-org/sped-averbacao for the canonical source repository
 */
class Averbei
{
    /**
     * Código HTTP da última requisição (0 quando não houve resposta)
     * @var int
     */
    public $httpCode = 0;
    /**
     * @var string
     */
    public $lastRequest;
    /**
     * @var string
     */
    public $lastResponse;
    /**
     * @var array
     */
    public $debug = [];
    /**
     * @var int
     */
    public $timeout = 60;
    /**
     * @var string
     */
    protected $token;
    /**
     * 1 - Produção | 2 - Homologação
     * @var int
     */
    protected $tpAmb;
    /**
     * @var array
     */
    protected $urls = [
        1 => 'https://integracao.averbei.com.br/averbacoes',
        2 => 'https://qa-integracao.averbei.com.br/averbacoes',
    ];

    /**
     * Cria o cliente da API de averbação da Averbei
     * @param string $token token de acesso fornecido pela Averbei
     * @param int $tpAmb 1 - Produção | 2 - Homologação
     */
    public function __construct($token, $tpAmb = 1)
    {
        $this->token = $token;
        $this->tpAmb = $tpAmb == 2 ? 2 : 1;
    }

    /**
     * Envia o XML autorizado (CT-e, NF-e, MDF-e ou documento não fiscal) para averbação,
     * ou o XML do evento de cancelamento para cancelar a averbação.
     * O resultado é identificado pelo código HTTP ($this->httpCode) e pela chave "name" do JSON retornado.
     * @param string $xml
     * @return string JSON retornado pela Averbei
     * @throws \InvalidArgumentException XML ou token não informado
     * @throws \RuntimeException falha de comunicação
     */
    public function send($xml)
    {
        if (empty($xml)) {
            throw new \InvalidArgumentException(
                'Um XML do (CTe, MDFe, NFe) protocolado ou do evento de cancelamento deve ser passado.'
            );
        }
        return $this->conn('POST', $this->urls[$this->tpAmb], $xml);
    }

    /**
     * Consulta um arquivo averbado ou cancelado
     * @param string $fileId file_id ou canceled_file_id retornado no envio
     * @return string JSON retornado pela Averbei
     * @throws \InvalidArgumentException identificador ou token não informado
     * @throws \RuntimeException falha de comunicação
     */
    public function consult($fileId)
    {
        if (empty($fileId)) {
            throw new \InvalidArgumentException('O identificador do arquivo (file_id) deve ser passado.');
        }
        return $this->conn('GET', $this->urls[$this->tpAmb] . '/' . rawurlencode($fileId));
    }

    /**
     * Executa a requisição HTTP na API da Averbei e guarda o código HTTP em $this->httpCode
     * @param string $method GET ou POST
     * @param string $url
     * @param string|null $body XML enviado no POST
     * @return string corpo da resposta
     * @throws \InvalidArgumentException token não informado
     * @throws \RuntimeException falha de comunicação (erro do cURL)
     */
    protected function conn($method, $url, $body = null)
    {
        if (empty($this->token)) {
            throw new \InvalidArgumentException('O token de acesso da Averbei deve ser passado.');
        }
        $this->httpCode = 0;
        $this->lastRequest = $body;
        $this->lastResponse = null;

        $oCurl = curl_init();
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($oCurl, CURLOPT_HEADER, false);
        curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($oCurl, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($oCurl, CURLOPT_HTTPHEADER, [
            'token: ' . $this->token,
            'Content-Type: application/xml',
            'Accept: application/json',
        ]);
        if ($method === 'POST') {
            curl_setopt($oCurl, CURLOPT_POST, true);
            curl_setopt($oCurl, CURLOPT_POSTFIELDS, $body);
        }
        $result = curl_exec($oCurl);
        $errno = curl_errno($oCurl);
        $this->debug = curl_getinfo($oCurl);
        $this->httpCode = (int) curl_getinfo($oCurl, CURLINFO_HTTP_CODE);
        curl_close($oCurl);
        if ($errno) {
            throw new \RuntimeException(curl_strerror($errno), $errno);
        }
        $this->lastResponse = (string) $result;
        return $this->lastResponse;
    }
}
