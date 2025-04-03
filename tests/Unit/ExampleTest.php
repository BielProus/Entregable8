<?php

namespace Tests\Feature;

use App\Models\Cuenta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CuentaTest extends TestCase
{
    use RefreshDatabase;

    public function crea_una_cuenta_con_saldo_cero()
    {
        $cuenta = Cuenta::create();
        $this->assertEquals(0, $cuenta->saldo);
    }

    public function permite_depositos_validos()
    {
        $cuenta = Cuenta::create();
        $cuenta->depositar(100.45);
        $this->assertEquals(100.45, $cuenta->saldo);
    }

    public function no_permite_depositos_invalidos()
    {
        $cuenta = Cuenta::create();
        $cuenta->depositar(-100);
        $this->assertEquals(0, $cuenta->saldo);

        $cuenta->depositar(100.457);
        $this->assertEquals(0, $cuenta->saldo);

        $cuenta->depositar(6000.01);
        $this->assertEquals(0, $cuenta->saldo);
    }

    public function permite_retiros_validos()
    {
        $cuenta = Cuenta::create(['saldo' => 500]);
        $cuenta->retirar(100.45);
        $this->assertEquals(399.55, $cuenta->saldo);
    }

    public function no_permite_retiros_invalidos()
    {
        $cuenta = Cuenta::create(['saldo' => 500]);
        $cuenta->retirar(-100);
        $this->assertEquals(500, $cuenta->saldo);

        $cuenta->retirar(6000.01);
        $this->assertEquals(500, $cuenta->saldo);
    }

    public function permite_transferencias_validas()
    {
        $desde = Cuenta::create(['saldo' => 500]);
        $hacia = Cuenta::create(['saldo' => 50]);
        $desde->transferir($hacia, 100);

        $this->assertEquals(400, $desde->saldo);
        $this->assertEquals(150, $hacia->saldo);
    }

    public function no_permite_transferencias_invalidas()
    {
        $desde = Cuenta::create(['saldo' => 500]);
        $hacia = Cuenta::create(['saldo' => 50]);

        $desde->transferir($hacia, -100);
        $this->assertEquals(500, $desde->saldo);
        $this->assertEquals(50, $hacia->saldo);
    }
}