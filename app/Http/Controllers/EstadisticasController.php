<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Caja;
use App\Empleado;

class EstadisticasController extends Controller {

    /**
     * @return \Illuminate\Http\Response
     */
    public function Datosnumericoscaja() {
        $cajas = Caja::all();
        if (count($cajas) > 0) {
            $data = null;
            $caja = $cajas[0];
            $data = [
                'dinerocaja' => $caja->dineroCaja,
                'dinerogenerado' => $caja->dineroGenerado,
                'ingresos' => $caja->ingresos,
                'egresos' => $caja->egresos,
                'ganancialocal' => $caja->gananciaLocal
            ];
            return response()->json(['data' => $data, 'mensaje' => 'Datos encontrados'], 200);
        } else {
            return response()->json(['data' => 'null', 'mensaje' => 'Datos no encontrados'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error inesperado'], 500);
    }

}
