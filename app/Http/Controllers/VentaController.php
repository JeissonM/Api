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
        //
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
     * @params {cliente,services:array,empleado:array,descuento:null}
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        dd($request);
        $cajas = Caja::all();
        if (count($cajas) > 0) {
            $caja = $cajas[0];
            $venta = new Venta();
            $total = 0;
            $gananciaNegocio = 0;
            $gananciaEmpleado = 0;
            $servicios = null;
            foreach ($request->service as $item) {
                $service = Service::find($item);
                $total = $total + $service->precio;
                $gananciaEmpleado = $gananciaEmpleado + ( ($service->precio * $service->gananciaEmpleado) / 100);
                $gananciaNegocio = $gananciaNegocio + ( $service->precio - (($service->precio * $service->gananciaEmpleado) / 100));
            }
            $venta->base = $total;
            $venta->gananciaNegocio = $gananciaNegocio;
            $venta->gananciaEmpleado = $gananciaEmpleado;
        } else {
            return response()->json(['data' => $empleado, 'mensaje' => 'No hay caja abierta, primero debe abrir la caja'], 200);
        }
        return response()->json(['data' => $empleado, 'mensaje' => 'Error Inesperado'], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Venta  $venta
     * @return \Illuminate\Http\Response
     */
    public function show(Venta $venta) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Venta  $venta
     * @return \Illuminate\Http\Response
     */
    public function edit(Venta $venta) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Venta  $venta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Venta $venta) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Venta  $venta
     * @return \Illuminate\Http\Response
     */
    public function destroy(Venta $venta) {
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
