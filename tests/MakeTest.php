<?php

/**
 * Class MakeAverbacaoTest
 * @author Roberto L. Machado <linux.rlm at gmail dot com>
 */
use NFePHP\Averbacao\Tools;

class MakeTest extends PHPUnit_Framework_TestCase
{
    /**
     * object 
     */
    private $tools;

    public function testDadosParaConexao()
    {
        $cUsuario = 'WS';
        $cSenha = 'base'; 
        $cCodigo = '99999999';
        $tpAmb = 2;

        $this->assertEquals($cUsuario, $this->tools->cUsuario);
        $this->assertEquals($cSenha, $this->tools->cSenha);
        $this->assertEquals($cCodigo, $this->tools->cCodigo);
        $this->assertEquals($tpAmb, $this->tools->tpAmb);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->tools = new Tools('WS','base','99999999',2);
    }

    protected function tearDown()
    {
        parent::tearDown();
        unset($this->tools);
    }
}
