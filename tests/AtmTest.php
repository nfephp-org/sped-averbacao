<?php

namespace NFePHP\Averbacao;

use NFePHP\Averbacao\Atm;
use PHPUnit_Framework_TestCase;

class AtmTest extends PHPUnit_Framework_TestCase
{
    /**
     * object
     */
    private $atm;

    public function testDadosParaConexao()
    {
        $cUsuario = 'WS';
        $cSenha = 'base';
        $cCodigo = '99999999';
        $tpAmb = 2;

        $this->assertEquals($cUsuario, $this->atm->cUsuario);
        $this->assertEquals($cSenha, $this->atm->cSenha);
        $this->assertEquals($cCodigo, $this->atm->cCodigo);
        $this->assertEquals($tpAmb, $this->atm->tpAmb);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->atm = new Atm('WS', 'base', '99999999', 2);
    }

    protected function tearDown()
    {
        parent::tearDown();
        unset($this->atm);
    }
}
