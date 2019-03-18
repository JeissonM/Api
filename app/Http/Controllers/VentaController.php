<?php

namespace App\Http\Controllers;

use App\Venta;
use App\Detalle;
use App\User;
use App\Service;
use App\Caja;
use App\Movimientocaja;
use App\Empleado;
use Illuminate\Http\Request;

class VentaController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $turnos = Venta::where('estado', 'PENDIENTE')->get();
        if (count($turnos) > 0) {
            foreach ($turnos as $t) {
                if (count($t->detalles) > 0) {
                    $t->detalles->each(function($item) {
                        $item->empleado;
                        $item->service;
                    });
                }
            }
            return response()->json(['data' => $turnos, 'mensaje' => 'Datos encontrados'], 200);
        } else {
            return response()->json(['data' => 'null', 'mensaje' => 'No hay turnos pendientes'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error Inesperado'], 500);
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
     * @params {cliente,detalles:array (empleado_id, service_id), api_token}
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $cajas = Caja::all();
        if (count($cajas) > 0) {
            $caja = $cajas[0];
            if (!$caja) {
                return response()->json(['data' => 'null', 'mensaje' => 'No hay caja abierta para realizar la venta.'], 200);
            }
            $venta = new Venta();
            $base = $gananciaNegocio = $gananciaEmpleado = 0;
            $servicios = null;
            foreach ($request->detalles as $item) {
                $service = Service::find($item['service_id']);
                $base = $base + $service->precio;
                $porc = ($service->precio * $service->ganancia_empleado) / 100;
                $gananciaEmpleado = $gananciaEmpleado + $porc;
                $gananciaNegocio = $gananciaNegocio + ($service->precio - $porc);
                $d = new Detalle();
                $d->valorServicio = $service->precio;
                $d->gananciaEmpleado = $porc;
                $d->totalServicio = $service->precio;
                $d->empleado_id = $item['empleado_id'];
                $d->service_id = $item['service_id'];
                $servicios[] = $d;
            }
            $venta->base = $base;
            $venta->descuentoAplicado = 0;
            $venta->gananciaNegocio = $gananciaNegocio;
            $venta->gananciaEmpleado = $gananciaEmpleado;
            $venta->valorAgregado = 0;
            $venta->cliente = $request->cliente;
            $venta->total = $base;
            $venta->estado = "PENDIENTE";
            $venta->user_change = $this->getApitokenAuthenticated($request->api_token)->identificacion;
            if ($venta->save()) {
                //detalles
                foreach ($servicios as $s) {
                    $s->venta_id = $venta->id;
                    $s->save();
                }
                return response()->json(['data' => 'null', 'mensaje' => 'Datos guardados'], 200);
            } else {
                return response()->json(['data' => 'null', 'mensaje' => 'Datos no guardados'], 200);
            }
        } else {
            return response()->json(['data' => 'null', 'mensaje' => 'No hay caja abierta, primero debe abrir la caja'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error Inesperado'], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Venta  $venta
     * @return \Illuminate\Http\Response
     */
    public function show(Venta $venta) {
        //not implemented
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Venta  $venta
     * @return \Illuminate\Http\Response
     */
    public function edit(Venta $venta) {
        //not implemented
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Venta  $venta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Venta $venta) {
        //not implemented
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Venta  $venta
     * @return \Illuminate\Http\Response
     */
    public function destroy(Venta $venta) {
        //not implemented
    }

    /**
     * get a user authenticeted
     */
    public function getApitokenAuthenticated($api_token) {
        $user = User::where('api_token', $api_token)->first();
        return $user;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function cancelarTurno($id) {
        $venta = Venta::find($id);
        if (!$venta) {
            return response()->json(['data' => 'null', 'mensaje' => 'Datos no encontrados'], 200);
        } else {
            if ($venta->delete()) {
                return response()->json(['data' => 'null', 'mensaje' => 'Datos eliminados'], 200);
            } else {
                return response()->json(['data' => 'null', 'mensaje' => 'Datos no eliminados'], 200);
            }
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error Inesperado'], 500);
    }

    /**
     * load added value
     *
     * @param  $venta_id, $valor
     * @return \Illuminate\Http\Response
     */
    public function valorAgregado($venta_id, $valor) {
        $venta = Venta::find($venta_id);
        if (!$venta) {
            return response()->json(['data' => 'null', 'mensaje' => 'Datos no encontrados'], 200);
        } else {
            $venta->valorAgregado = $venta->valorAgregado + $valor;
            $venta->total = ($venta->base + $venta->valorAgregado) - $venta->descuentoAplicado;
            if ($venta->save()) {
                return response()->json(['data' => 'null', 'mensaje' => 'Datos almacenados'], 200);
            } else {
                return response()->json(['data' => 'null', 'mensaje' => 'Datos no almacenados'], 200);
            }
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error Inesperado'], 500);
    }

    /**
     * discount on service and sale
     *
     * @param  $detalle_id, $porcentaje
     * @return \Illuminate\Http\Response
     */
    public function descuento($detalle_id, $porcentaje) {
        $detalle = Detalle::find($detalle_id);
        if (!$detalle) {
            return response()->json(['data' => 'null', 'mensaje' => 'Datos no encontrados'], 200);
        } else {
            $detalle->descuento = $porcentaje;
            $detalle->valorDescontado = ($detalle->valorServicio * $porcentaje) / 100;
            $detalle->totalServicio = $detalle->valorServicio - $detalle->valorDescontado;
            $venta = $detalle->venta;
            $venta->descuentoAplicado = $venta->descuentoAplicado + $detalle->valorDescontado;
            $venta->total = ($venta->base + $venta->valorAgregado) - $venta->descuentoAplicado;
            if ($detalle->save()) {
                if ($venta->save()) {
                    return response()->json(['data' => 'null', 'mensaje' => 'Datos almacenados'], 200);
                } else {
                    return response()->json(['data' => 'null', 'mensaje' => 'Se guardo el descuento pero no se descontó de la venta, error fatal'], 200);
                }
            } else {
                return response()->json(['data' => 'null', 'mensaje' => 'Datos no almacenados'], 200);
            }
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error Inesperado'], 500);
    }

    /**
     * pay a sale
     *
     * @param  $venta_id, $api_token
     * @return \Illuminate\Http\Response
     */
    public function pagar($id, $api_token) {
        $venta = Venta::find($id);
        if (!$venta) {
            return response()->json(['data' => 'null', 'mensaje' => 'Datos no encontrados'], 200);
        } else {
            $cajas = Caja::all();
            if (count($cajas) > 0) {
                $caja = $cajas[0];
                $caja->dineroCaja = $caja->dineroCaja + $venta->total;
                $caja->dineroGenerado = $caja->dineroGenerado + $venta->total;
                $caja->gananciaLocal = $caja->gananciaLocal + $venta->gananciaNegocio;
                $venta->estado = "PAGADO";
                $ingreso = new Movimientocaja();
                $hoy = getdate();
                $ingreso->fecha = $hoy["year"] . "-" . $hoy["mon"] . "-" . $hoy["mday"] . " " . $hoy["hours"] . ":" . $hoy["minutes"] . ":" . $hoy["seconds"];
                $ingreso->descripcion = "PAGO DE VENTA";
                $ingreso->monto = $venta->total;
                $ingreso->tipo = "INGRESO";
                $ingreso->caja_id = $caja->id;
                $ingreso->user_change = $this->getApitokenAuthenticated($api_token)->identificacion;
                $caja->ingresos = $caja->ingresos + $venta->total;
                if ($venta->save()) {
                    if ($caja->save()) {
                        if ($ingreso->save()) {
                            foreach ($venta->detalles as $d) {
                                $e = $d->empleado;
                                $e->saldofavor = $e->saldofavor + $d->gananciaEmpleado;
                                $e->turnos = $e->turnos + 1;
                                $e->save();
                            }
                            return response()->json(['data' => 'null', 'mensaje' => 'Venta pagada con exito.'], 200);
                        } else {
                            return response()->json(['data' => 'null', 'mensaje' => 'Se pagó la venta se registró a caja, pero no se pudo registrar el movimiento de caja INGRESO.'], 200);
                        }
                    } else {
                        return response()->json(['data' => 'null', 'mensaje' => 'Se pagó la venta pero no se registró a caja.'], 200);
                    }
                } else {
                    return response()->json(['data' => 'null', 'mensaje' => 'No se pudo pagar la venta'], 200);
                }
            } else {
                return response()->json(['data' => 'null', 'mensaje' => 'No hay caja abierta, primero debe abrir la caja'], 200);
            }
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error Inesperado'], 500);
    }

}
