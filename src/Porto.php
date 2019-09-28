<?php

namespace NFePHP\Averbacao;

use \CURLFile;

class Porto
{
    public $debug = '';
    protected $cc;
    protected $user;
    protected $pass;
    protected $cockie;
    protected $environment = null;
    protected $logged = false;
    protected $useragent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2';
    protected $url = 'https://www.averbeporto.com.br/websys/php/conn.php';
    
    /**
     * Construct
     * @param type $user
     * @param type $pass
     */
    public function __construct($user = null, $pass = null)
    {
        $this->user = $user ?? null;
        $this->pass = $pass ?? null;
    }
    
    /**
     * clear files
     */
    public function __destruct()
    {
        if (is_file($this->cockie)) {
            unlink($this->cockie);
        }
    }
    
    /**
     * Set environment variable to tests in webservice
     * @param int $env
     * @return int | null
     */
    public function environment($env = null)
    {
        if ($env === 1 || $env === 2 || $env === null) {
            $this->environment = $env;
        }
        return $this->environment;
    }
    
    /**
     * Login with webservice
     * @param string $user
     * @param string $pass
     * @return boolean
     */
    public function login($user = null, $pass = null)
    {
        $this->logged = false;
        $this->user = $user ?? $this->user;
        $this->pass = $pass ?? $this->pass;
        if (!$this->user || !$this->pass) {
            return false;
        }
        $post = [
            'mod'=> 'login',
            'comp' => '5',
            'user' => $this->user,
            'pass' => $this->pass,
        ];
        if ($this->environment != null) {
            $post['dump'] = $this->environment;
        }
        $response = $this->conn($post);
        $std = json_decode($response);
        if (!isset($std->success) && !isset($std->C)) {
            return false;
        }
        if ($std->success && $std->C) {
            $this->logged = true;
        }
        return $this->logged;
    }
    
    /**
     * Send a file for insurance registration
     * @param string $filecontent
     * @return string
     */
    public function send($filecontent = null)
    {
        if (!$filecontent) {
            return false;
        }
        if (!$this->logged) {
            return false;
        }
        
        $post = [
            'mod'=> 'Upload',
            'comp' => '5',
            'path'   => 'eguarda/php/',
            'cookie' => $this->cockie,
            'recipient' => ''
        ];
        
        if ($this->environment != null) {
            $post['dump'] = $this->environment;
        }
        $filename = tempnam(sys_get_temp_dir(), 'file_');
        file_put_contents($filename, $filecontent);
        $mime = mime_content_type($filename);
        $post['file'] = new CURLFile($filename, $mime);
        $response = $this->conn($post);
        return $response;
    }
    
    /**
     * Send a consultation for get ANTT protocol using key
     * @param string $chave
     * @param string $protocolo
     * @return string
     */
    public function consult($chave = null, $protocolo = null)
    {
        if (empty($chave) && empty($protocolo)) {
            return false;
        }
        if (!$this->logged) {
            return false;
        }
        $post = [
            'comp' => 5,
            'mod' => 'Protocolo',
            'path' => 'atwe/php/'
        ];
        if (isset($chave)) {
            $post['chave[]'] = $chave;
        } elseif (isset($protocolo)) {
            $post['protocolo[]'] = $protocolo;
        }
        if ($this->environment != null) {
            $post['dump'] = $this->environment;
        }
        $response = $this->conn($post);
        return $response;
    }
    
    /**
     * Send a file for insurance registration
     * @param string $user
     * @param string $pass
     * @param string $filecontent
     * @return string
     */
    public static function sendStatic($user, $pass, $filecontent)
    {
        $class = new static($user, $pass);
        $class->login();
        return $class->send($filecontent);
    }
    
    /**
     * Send a consultation for get ANTT protocol using key
     * @param string $user
     * @param string $pass
     * @param string $chave
     * @param string $protocolo
     * @return string
     */
    public static function consultStatic($user, $pass, $chave = null, $protocolo = null)
    {
        $class = new static($user, $pass);
        $class->login();
        return $class->consult($chave, $protocolo);
    }
    
    /**
     * Send message to webservice
     * @param array $post
     * @return string
     * @throws \Exception
     */
    public function conn($post)
    {
        $message = null;
        $oCurl = curl_init();
        curl_setopt($oCurl, CURLOPT_URL, $this->url);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($oCurl, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($oCurl, CURLOPT_HEADER, 0);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, true);
        if (!$this->logged) {
            $this->cockie = tempnam(sys_get_temp_dir(), 'porto_');
            curl_setopt($oCurl, CURLOPT_COOKIEJAR, $this->cockie);
            curl_setopt($oCurl, CURLOPT_COOKIEFILE, $this->cockie);
        } else {
            curl_setopt($oCurl, CURLOPT_COOKIEFILE, $this->cockie);
        }
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $post);

        $result = curl_exec($oCurl);
        $errno = curl_errno($oCurl);
        $errmsg = curl_error($oCurl);
        $this->debug = curl_getinfo($oCurl);
        curl_close($oCurl);
        if ($errno) {
            $message = curl_strerror($errno);
            throw new \Exception($message);
        }
        return $result;
    }
}
