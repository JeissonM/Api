<?php

namespace App\Http\Controllers;

use App\Movimientosempleado;
use App\Caja;
use App\Empleado;
use App\Movimientocaja;
use App\User;
use Illuminate\Http\Request;

class MovimientosempleadoController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @params {origen,tipo,monto,empleado_id,caja_id} para el caso de PAGO TRABAJO el monto debe venir en 0
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $movempleado = new Movimientosempleado($request->all());
        $user = $this->getApitokenAuthenticated($request->api_token)->identificacion;
        $movempleado->user_change = $user;
        $descripcion = null;
        $tipo = null;
        $mensaje = null;
        $monto = null;
        if ($movempleado->save()) {
            $empleado = Empleado::find($request->empleado_id);
            $caja = Caja::find($request->caja_id);
            switch ($request->tipo) {
                case "ADELANTO": if ($empleado->saldofavor > 0) {
                        $movempleado->delete();
                        return response()->json(['data' => 'null', 'mensaje' => 'El empleado cuenta con saldo a favor por tal motivo no se puede realizar el adelanto'], 200);
                    } else {
                        if ($movempleado->origen == "CAJA") {
                            if ($caja->dineroCaja >= $movempleado->monto) {
                                $caja->dineroCaja = $caja->dineroCaja - $movempleado->monto;
                                $descripcion = "ADELANTO A EMPLEADO";
                                $tipo = "EGRESO";
                                //$empleado->saldocontra = $empleado->saldocontra + $movempleado->monto;
                                $mensaje = "El adelanto fue realizado de forma correcta";
                                $descripcion = "ADELANTO A EMPLEADO";
                                $tipo = "EGRESO";
                            } else {
                                $movempleado->delete();
                                return response()->json(['data' => 'null', 'mensaje' => 'Fondos insuficientes, no se pudo realizar la operación'], 200);
                            }
                        }
                        $empleado->saldocontra = $empleado->saldocontra + $movempleado->monto;
                        $mensaje = "El adelanto fue realizado de forma correcta";
                    }
                    break;
                case "ABONO": if ($empleado->saldofavor <= 0) {
                        $movempleado->delete();
                        return response()->json(['data' => 'null', 'mensaje' => 'El empleado no tiene saldo a favor por tal motivo no se puede realizar el abono'], 200);
                    } else {
                        if ($movempleado->origen == "CAJA") {
                            if ($caja->dineroCaja >= $movempleado->monto) {
                                $caja->dineroCaja = $caja->dineroCaja - $movempleado->monto;
                                $descripcion = "ABONO A EMPLEADO";
                                $tipo = "EGRESO";
                            } else {
                                $movempleado->delete();
                                return response()->json(['data' => 'null', 'mensaje' => 'Fondos insuficientes, no se pudo realizar la operación'], 200);
                            }
                        }
                        $empleado->saldofavor = $empleado->saldofavor - $movempleado->monto;
                        $mensaje = "El abono fue realizado de forma correcta";
                    }
                    break;
                case "PAGO DEUDA": if ($movempleado->origen == "SALDO FAVOR") {
                        if ($empleado->saldofavor < $movempleado->monto) {
                            $movempleado->delete();
                            return response()->json(['data' => 'null', 'mensaje' => 'El saldo a favor del empleado es menor que el monto que desea pagar'], 200);
                        }
                        $empleado->saldofavor = $empleado->saldofavor - $movempleado->monto;
                        $empleado->saldocontra = $empleado->saldocontra - $movempleado->monto;
                        $empleado->save();
                        return response()->json(['data' => 'null', 'mensaje' => 'El pago del empleado se realizo con exito'], 200);
                    } else {
                        $empleado->saldocontra = $empleado->saldocontra - $movempleado->monto;
                        $caja->dineroCaja = $caja->dineroCaja + $movempleado->monto;
                        $mensaje = "El pago del empleado fue realizado con exito";
                        $descripcion = "PAGO DEUDA DE EMPLEADO";
                        $tipo = "INGRESO";
                    }
                    break;
                case "PAGO TRABAJO":if ($movempleado->origen == "CAJA") {
                        if ($caja->dineroCaja >= $empleado->saldofavor) {
                            $movempleado->monto = $empleado->saldofavor;
                            $monto = $empleado->saldofavor;
                            $caja->dineroCaja = $caja->dineroCaja - $empleado->saldofavor;
                            $empleado->saldofavor = 0;
                            $movempleado->save();
                            $descripcion = "PAGO A EMPLEADO";
                            $tipo = "EGRESO";
                            $mensaje = "El pago al empleado fue realizado con exito";
                        } else {
                            $movempleado->delete();
                            return response()->json(['data' => 'null', 'mensaje' => 'Fondos insuficientes, no se pudo realizar la operación'], 200);
                        }
                    } else {
                        $mensaje = "El pago al empleado fue realizado con exito";
                        $empleado->saldofavor = 0;
                    }
                    break;
                default :
                    break;
            }
            $empleado->save();
            $caja->save();
            if ($descripcion != null && $tipo != null) {
                $hoy = getdate();
                $movcaja = new Movimientocaja();
                $movcaja->descripcion = $descripcion;
                if ($monto != null) {
                    $movcaja->monto = $monto;
                } else {
                    $movcaja->monto = $movempleado->monto;
                }
                $movcaja->tipo = $tipo;
                $movcaja->fecha = $hoy["year"] . "-" . $hoy["mon"] . "-" . $hoy["mday"] . " " . $hoy["hours"] . ":" . $hoy["minutes"] . ":" . $hoy["seconds"];
                $movcaja->user_change = $user;
                $movcaja->caja_id = $caja->id;
                $movcaja->save();
            }
            return response()->json(['data' => 'null', 'mensaje' => $mensaje], 200);
        } else {
            return response()->json(['data' => 'null', 'mensaje' => 'No se pudo realizar el movimiento'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error inesperado'], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Movimientosempleado  $movimientosempleado
     * @return \Illuminate\Http\Response
     */
    public function show(Movimientosempleado $movimientosempleado) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Movimientosempleado  $movimientosempleado
     * @return \Illuminate\Http\Response
     */
    public function edit(Movimientosempleado $movimientosempleado) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Movimientosempleado  $movimientosempleado
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Movimientosempleado $movimientosempleado) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Movimientosempleado  $movimientosempleado
     * @return \Illuminate\Http\Response
     */
    public function destroy(Movimientosempleado $movimientosempleado) {
        //
    }

    /**
     * get a user authenticeted
     */
    public function getApitokenAuthenticated($api_token) {
        $user = User::where('api_token', $api_token)->first();
        return $user;
    }

}
