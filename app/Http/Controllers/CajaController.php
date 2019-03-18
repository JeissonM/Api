<?php

namespace App\Http\Controllers;

use App\Caja;
use Illuminate\Http\Request;
use App\Historialcaja;
use App\User;
use App\Movimientohistorialcaja;
use App\Movimientocaja;

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
     * @param Request $request {montoConfirmado, montoAgregado, api_token}
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
        $response = null;
        if ($request->montoConfirmado > $oldCaja->montoInicial) {
            $response = [
                'inconsistencia' => 'SI',
                'mensaje' => 'Existe una inconsistencia, en caja debe haber $' . $oldCaja->montoInicial . " y usted indica que hay $" . $request->montoConfirmado . ", hay más dinero del que debería haber, sobran $" . ($request->montoConfirmado - $oldCaja->montoInicial) . ".",
                'proceder' => 'Especifique los detalles de lo ocurrido con la caja en la pantalla indicada para ello, la caja se abrirá de todos modos.',
                'historialcaja_id' => $oldCaja->id
            ];
        }
        if ($request->montoConfirmado < $oldCaja->montoInicial) {
            $response = [
                'inconsistencia' => 'SI',
                'mensaje' => 'Existe una inconsistencia, en caja debe haber $' . $oldCaja->montoInicial . " y usted indica que hay $" . $request->montoConfirmado . ", faltan $" . ($request->montoConfirmado - $oldCaja->montoInicial) . " en la caja.",
                'proceder' => 'Especifique los detalles de lo ocurrido con la caja en la pantalla indicada para ello, la caja se abrirá de todos modos.',
                'historialcaja_id' => $oldCaja->id
            ];
        }
        $caja = new Caja();
        $caja->dineroCaja = $oldCaja->montoInicial + $request->montoAgregado;
        $caja->dineroGenerado = 0;
        $caja->egresos = 0;
        $caja->ingresos = 0;
        $caja->fechaApertura = $hoy["year"] . "-" . $hoy["mon"] . "-" . $hoy["mday"] . " " . $hoy["hours"] . ":" . $hoy["minutes"] . ":" . $hoy["seconds"];
        $caja->gananciaLocal = 0;
        $caja->montoInicial = $oldCaja->montoInicial + $request->montoAgregado;
        $caja->montoAgregado = $request->montoAgregado;
        $caja->montoConfirmado = $request->montoConfirmado;
        $caja->user_change = $this->getApitokenAuthenticated($request->api_token)->identificacion;
        if ($caja->save()) {
            return response()->json(['data' => $response, 'mensaje' => 'Caja abierta'], 200);
        } else {
            return response()->json(['data' => 'null', 'mensaje' => 'No se pudo abrir la caja'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error inesperado'], 500);
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
            $caja = $cajas[0];
            $movs = $caja->movimientocajas;
            $hoy = getdate();
            $hisCaja = new Historialcaja();
            $hisCaja->dineroCaja = $caja->dineroCaja;
            $hisCaja->dineroGenerado = $caja->dineroGenerado;
            $hisCaja->egresos = $caja->egresos;
            $hisCaja->ingresos = $caja->ingresos;
            $hisCaja->fechaApertura = $caja->fechaApertura;
            $hisCaja->fechaCierre = $hoy["year"] . "-" . $hoy["mon"] . "-" . $hoy["mday"] . " " . $hoy["hours"] . ":" . $hoy["minutes"] . ":" . $hoy["seconds"];
            $hisCaja->inconsistencia = false;
            $hisCaja->anterior = "SI";
            $hisCaja->observaciones = "";
            $hisCaja->gananciaLocal = $caja->gananciaLocal;
            $hisCaja->montoInicial = $request->montoInicial;
            $hisCaja->montoAgregado = $caja->montoAgregado;
            $hisCaja->montoConfirmado = $caja->montoConfirmado;
            $hisCaja->user_change = $this->getApitokenAuthenticated($request->api_token)->identificacion;
            $hold = Historialcaja::where('anterior', 'SI')->first();
            if (!$hold) {
                //
            } else {
                $hold->anterior = "NO";
                $hold->save();
            }
            if ($hisCaja->save()) {
                if ($caja->delete()) {
                    if (count($movs) > 0) {
                        foreach ($movs as $m) {
                            $mhc = new Movimientohistorialcaja();
                            $mhc->fecha = $m->fecha;
                            $mhc->descripcion = $m->descripcion;
                            $mhc->monto = $m->monto;
                            $mhc->tipo = $m->tipo;
                            $mhc->user_change = $hisCaja->user_change;
                            $mhc->historialcaja_id = $hisCaja->id;
                            $mhc->save();
                            $m->delete();
                        }
                    }
                    return response()->json(['data' => 'null', 'mensaje' => 'Cierre de caja exitoso.'], 200);
                } else {
                    $hisCaja->delete();
                    return response()->json(['data' => 'null', 'mensaje' => 'No se pudo realizar el cierre de caja porque ésta no pudo ser reiniciada, verifique.'], 200);
                }
            } else {
                return response()->json(['data' => 'null', 'mensaje' => 'No se pudo realizar el cierre de caja, verifique.'], 200);
            }
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

    /**
     * ingreso en caja
     *
     * @param Request $request {monto, descripcion, caja_id}
     * @return \Illuminate\Http\Response
     */
    public function ingreso(Request $request) {
        $ingreso = new Movimientocaja();
        $hoy = getdate();
        $ingreso->fecha = $hoy["year"] . "-" . $hoy["mon"] . "-" . $hoy["mday"] . " " . $hoy["hours"] . ":" . $hoy["minutes"] . ":" . $hoy["seconds"];
        $ingreso->descripcion = $request->descripcion;
        $ingreso->monto = $request->monto;
        $ingreso->tipo = "INGRESO";
        $ingreso->caja_id = $request->caja_id;
        $ingreso->user_change = $this->getApitokenAuthenticated($request->api_token)->identificacion;
        if ($ingreso->save()) {
            $caja = Caja::find($request->caja_id);
            $caja->dineroCaja = $caja->dineroCaja + $request->monto;
            $caja->ingresos = $caja->ingresos + $request->monto;
            if ($caja->save()) {
                return response()->json(['data' => 'null', 'mensaje' => 'Ingreso registrado con exito'], 200);
            } else {
                $ingreso->delete();
                return response()->json(['data' => 'null', 'mensaje' => 'El ingreso no pudo ser registrado'], 200);
            }
        } else {
            return response()->json(['data' => 'null', 'mensaje' => 'No se pudo registrar el ingreso'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error inesperado'], 500);
    }

    /**
     * egreso en caja
     *
     * @param Request $request {monto, descripcion, caja_id}
     * @return \Illuminate\Http\Response
     */
    public function egreso(Request $request) {
        $caja = Caja::find($request->caja_id);
        if ($caja->dineroCaja < $request->monto) {
            return response()->json(['data' => 'null', 'mensaje' => 'No se pudo registrar el egreso, no hay suficiente dinero en caja para realizar la operación'], 200);
        }
        $ingreso = new Movimientocaja();
        $hoy = getdate();
        $ingreso->fecha = $hoy["year"] . "-" . $hoy["mon"] . "-" . $hoy["mday"] . " " . $hoy["hours"] . ":" . $hoy["minutes"] . ":" . $hoy["seconds"];
        $ingreso->descripcion = $request->descripcion;
        $ingreso->monto = $request->monto;
        $ingreso->tipo = "EGRESO";
        $ingreso->caja_id = $request->caja_id;
        $ingreso->user_change = $this->getApitokenAuthenticated($request->api_token)->identificacion;
        if ($ingreso->save()) {
            $caja->dineroCaja = $caja->dineroCaja - $request->monto;
            $caja->egresos = $caja->egresos + $request->monto;
            if ($caja->save()) {
                return response()->json(['data' => 'null', 'mensaje' => 'Egreso registrado con exito'], 200);
            } else {
                $ingreso->delete();
                return response()->json(['data' => 'null', 'mensaje' => 'El egreso no pudo ser registrado'], 200);
            }
        } else {
            return response()->json(['data' => 'null', 'mensaje' => 'No se pudo registrar el egreso'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error inesperado'], 500);
    }

    /**
     * especifica la inconsistencia en historial de caja
     *
     * @param Request $request {observaciones, api_token, historialcaja_id}
     * @return \Illuminate\Http\Response
     */
    public function setInconsistencia(Request $request) {
        $h = Historialcaja::find($request->historialcaja_id);
        $h->inconsistencia = true;
        $h->observaciones = $request->observaciones;
        if ($h->save()) {
            return response()->json(['data' => 'null', 'mensaje' => 'Inconsistencia guardáda con exito'], 200);
        } else {
            return response()->json(['data' => 'null', 'mensaje' => 'No se pudo guardar la inconsistencia'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error inesperado'], 500);
    }

}
