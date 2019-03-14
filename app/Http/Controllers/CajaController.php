<?php

namespace App\Http\Controllers;

use App\Caja;
use Illuminate\Http\Request;
use App\Historialcaja;
use App\User;

class CajaController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //not implemented
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //not implemented
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //not implemented
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Caja  $caja
     * @return \Illuminate\Http\Response
     */
    public function show(Caja $caja) {
        //not implemented
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Caja  $caja
     * @return \Illuminate\Http\Response
     */
    public function edit(Caja $caja) {
        //not implemented
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Caja  $caja
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Caja $caja) {
        //not implemented
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Caja  $caja
     * @return \Illuminate\Http\Response
     */
    public function destroy(Caja $caja) {
        //not implemented
    }

    /**
     * dinero en caja abierta
     *
     * @param
     * @return \Illuminate\Http\Response
     */
    public function getDineroCaja() {
        $caja = Caja::all();
        if (count($caja) > 0) {
            return response()->json(['data' => $caja[0]->dineroCaja, 'mensaje' => 'Datos encontrados'], 200);
        } else {
            return response()->json(['data' => 'null', 'mensaje' => 'No hay caja abierta.'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error inesperado'], 500);
    }

    /**
     * validar si la apertura de caja es primera vez
     *
     * @param
     * @return \Illuminate\Http\Response
     */
    public function validarPrimeraVez() {
        $h = Historialcaja::all();
        if (count($h) > 0) {
            return response()->json(['data' => 'null', 'mensaje' => 'No es primera vez'], 200);
        } else {
            return response()->json(['data' => 'null', 'mensaje' => 'Primera vez'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error inesperado'], 500);
    }

    /**
     * abrir caja primera vez
     *
     * @param Request $request {dineroCaja, api_token}
     * @return \Illuminate\Http\Response
     */
    public function abrirPrimeraVez(Request $request) {
        $c = Caja::all();
        if (count($c) > 0) {
            return response()->json(['data' => 'null', 'mensaje' => 'La caja ya se encuentra abierta.'], 200);
        }
        $hoy = getdate();
        $caja = new Caja();
        $caja->dineroCaja = $request->dineroCaja;
        $caja->dineroGenerado = 0;
        $caja->egresos = 0;
        $caja->ingresos = 0;
        $caja->fechaApertura = $hoy["year"] . "-" . $hoy["mon"] . "-" . $hoy["mday"] . " " . $hoy["hours"] . ":" . $hoy["minutes"] . ":" . $hoy["seconds"];
        $caja->gananciaLocal = 0;
        $caja->montoInicial = $request->dineroCaja;
        $caja->montoAgregado = $request->dineroCaja;
        $caja->montoConfirmado = $request->dineroCaja;
        $caja->user_change = $this->getApitokenAuthenticated($request->api_token)->identificacion;
        if ($caja->save()) {
            return response()->json(['data' => 'null', 'mensaje' => 'Caja abierta'], 200);
        } else {
            return response()->json(['data' => 'null', 'mensaje' => 'No se pudo abrir la caja'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error inesperado'], 500);
    }

    /**
     * abrir caja demás veces (no la primera vez)
     *
     * @param Request $request {dineroCaja, api_token, }
     * @return \Illuminate\Http\Response
     */
    public function abrirCaja(Request $request) {
        $c = Caja::all();
        if (count($c) > 0) {
            return response()->json(['data' => 'null', 'mensaje' => 'La caja ya se encuentra abierta.'], 200);
        }
        $hoy = getdate();
        $caja = new Caja();
        $oldCaja = Historialcaja::where('anterior', 'SI')->first();
        if (!$oldCaja) {
            return response()->json(['data' => 'null', 'mensaje' => 'El anterior cierre de caja fue erróneo, la caja no puede ser abierta hasta no darle solución: contácte al administrador del sistema.'], 200);
        }
    }

    /**
     * get a user authenticated
     */
    public function getApitokenAuthenticated($api_token) {
        $user = User::where('api_token', $api_token)->first();
        return $user;
    }

    /**
     * cerrar caja
     *
     * @param Request $request {dinero a dejar en caja: montoInicial}
     * @return \Illuminate\Http\Response
     */
    public function cerrarCaja(Request $request) {
        $cajas = Caja::all();
        if (count($cajas) > 0) {
//            $caja = $cajas[0];
//            $caja->movimientocajas;
//            $hisCaja = new Historialcaja();
//            $caja->dineroCaja = $request->dineroCaja;
//            $caja->dineroGenerado = 0;
//            $caja->egresos = 0;
//            $caja->ingresos = 0;
//            $caja->fechaApertura = $hoy["year"] . "-" . $hoy["mon"] . "-" . $hoy["mday"] . " " . $hoy["hours"] . ":" . $hoy["minutes"] . ":" . $hoy["seconds"];
//            $caja>fechaCierre
//                    inconsistencia
//                    anterior
//                    observaciones
//            $caja->gananciaLocal = 0;
//            $caja->montoInicial = $request->dineroCaja;
//            $caja->montoAgregado = $request->dineroCaja;
//            $caja->montoConfirmado = $request->dineroCaja;
            $caja->user_change = $this->getApitokenAuthenticated($request->api_token)->identificacion;
            return response()->json(['data' => $hisCaja, 'mensaje' => 'Caja'], 200);
        } else {
            return response()->json(['data' => 'null', 'mensaje' => 'No hay caja abierta para realizar cierre, verifique.'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error inesperado'], 500);
    }

    /**
     * obtener caja abierta
     *
     * @param
     * @return \Illuminate\Http\Response
     */
    public function getOpenCaja() {
        $caja = Caja::all();
        if (count($caja) > 0) {
            return response()->json(['data' => $caja[0], 'mensaje' => 'Datos encontrados'], 200);
        } else {
            return response()->json(['data' => 'null', 'mensaje' => 'No hay caja abierta.'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error inesperado'], 500);
    }

}
