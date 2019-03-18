<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Caja;
use App\Empleado;
use App\Detalle;
use App\Service;
use App\Venta;
use App\Historialcaja;

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

    /**
     * Historial de ventas
     */
    public function historicoVentas() {
        $ventas = Venta::all();
        if (count($ventas) > 0) {
            $data = null;
            foreach ($ventas as $item) {
                $data["cliente"] = $item->cliente;
                $data["total"] = $item->total;
                $data["fecha"] = $item->fecha;
                $detalles = $item->detalles;
                $d = null;
                if (count($detalles) > 0) {
                    foreach ($detalles as $de) {
                        $detalles = null;
                        $detalles["sevicio"] = $de->service->descripcion;
                        $detalles["empleado"] = $de->empleado->nombres . " " . $de->empleado->apellidos;
                        $d[] = $detalles;
                    }
                    $data["deatalle"] = $d;
                }
                $response[] = $data;
            }
            if (count($response) > 0) {
                return response()->json(['data' => $response, 'mensaje' => 'Datos encontrados'], 200);
            } else {
                return response()->json(['data' => 'null', 'mensaje' => 'Datos no encontrados'], 200);
            }
        } else {
            return response()->json(['data' => 'null', 'mensaje' => 'Datos no encontrados'], 200);
        }
    }

    /**
     * Bucar histrico de ventas por nombre de empleado
     * @params {cadena}
     */
    public function buscarHistoricoVenta(string $cadena) {
        $empleados = Empleado::where('nombres', 'like', '%' . $cadena . '%')
                        ->orWhere('apellidos', 'like', '%' . $cadena . '%')->get();
        if (count($empleados) > 0) {
            $ventas = Venta::all();
            if (count($ventas) > 0) {
                foreach ($empleados as $em) {
                    $data = null;
                    foreach ($ventas as $item) {
                        $detalles = Detalle::where([['venta_id', $item->id], ['empleado_id', $em->id]])->get();
                        if (count($detalles) > 0) {
                            $data["cliente"] = $item->cliente;
                            $data["total"] = $item->total;
                            $data["fecha"] = $item->fecha;
                            $detalles = $item->detalles;
                            $d = null;
                            foreach ($detalles as $de) {
                                $del = null;
                                $del["servicio"] = $de->service->descripcion;
                                $del["empleado"] = $de->empleado->nombres . " " . $de->empleado->apellidos;
                                $d[] = $del;
                            }
                            $data["deatalle"] = $d;
                        }
                        $response[] = $data;
                    }
                }
                dd($response);
                if (count($response) > 0) {
                    return response()->json(['data' => $response, 'mensaje' => 'Datos encontrados'], 200);
                } else {
                    return response()->json(['data' => 'null', 'mensaje' => 'Datos no encontrados'], 200);
                }
            } else {
                return response()->json(['data' => 'null', 'mensaje' => 'Datos no encontrados'], 200);
            }
        } else {
            return response()->json(['data' => 'null', 'mensaje' => 'Datos no encontrados'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error Inesperado'], 500);
    }

    public function topServicios() {
        $detalles = Detalle::all();
        if (count($detalles) > 0) {
            $servicios = [];
            foreach ($detalles as $item) {
                if (isset($servicios[$item->service_id])) {
                    $servicios[$item->service_id]["total"] = $servicios[$item->service_id]["total"] + 1;
                } else {
                    $servicios[$item->service_id]["total"] = 1;
                    $servicios[$item->service_id]["servicio"] = $item->service->descripcion;
                }
            }
            if (count($servicios) > 0) {
                $arror = $this->orderMultiDimensionalArray($servicios, 'total', true);
                return response()->json(['data' => $arror, 'mensaje' => 'Datos encontrados'], 200);
            } else {
                return response()->json(['data' => 'null', 'mensaje' => 'Datos no encontrados'], 200);
            }
        } else {
            return response()->json(['data' => 'null', 'mensaje' => 'Datos no encontrados'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error Inesperado'], 500);
    }

    public function topCategoria() {
        $detalles = Detalle::all();
        if (count($detalles) > 0) {
            $servicios = [];
            foreach ($detalles as $item) {
                if (isset($servicios[$item->service->categorie_id])) {
                    $servicios[$item->service->categorie_id]["total"] = $servicios[$item->service->categorie_id]["total"] + 1;
                } else {
                    $servicios[$item->service->categorie_id]["total"] = 1;
                    $servicios[$item->service->categorie_id]["categoria"] = $item->service->categorie->nombre;
                }
            }
            if (count($servicios) > 0) {
                $arror = $this->orderMultiDimensionalArray($servicios, 'total', true);
                return response()->json(['data' => $arror, 'mensaje' => 'Datos encontrados'], 200);
            } else {
                return response()->json(['data' => 'null', 'mensaje' => 'Datos no encontrados'], 200);
            }
        } else {
            return response()->json(['data' => 'null', 'mensaje' => 'Datos no encontrados'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error Inesperado'], 500);
    }

    public function topEmpleados() {
        $detalles = Detalle::all();
        if (count($detalles) > 0) {
            $empleados = [];
            foreach ($detalles as $item) {
                if (isset($empleados[$item->empleado_id])) {
                    $empleados[$item->empleado_id]["total"] = $empleados[$item->empleado_id]["total"] + 1;
                } else {
                    $empleados[$item->empleado_id]["total"] = 1;
                    $empleados[$item->empleado_id]["empleado"] = $item->empleado->nombres . " " . $item->empleado->apellidos;
                }
            }
            if (count($empleados) > 0) {
                $arror = $this->orderMultiDimensionalArray($empleados, 'total', true);
                return response()->json(['data' => $arror, 'mensaje' => 'Datos encontrados'], 200);
            } else {
                return response()->json(['data' => 'null', 'mensaje' => 'Datos no encontrados'], 200);
            }
        } else {
            return response()->json(['data' => 'null', 'mensaje' => 'Datos no encontrados'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error Inesperado'], 500);
    }

    public function rendimiento() {
        $fecha = date('Y-m-j');
        $nuevafecha = strtotime('-6 day', strtotime($fecha));
        $nuevafecha = date('Y-m-j', $nuevafecha);
        for ($i = 1; $i <= 7; $i++) {
            $ventas[$nuevafecha] = Venta::where([['created_at', $nuevafecha], ['estado', 'PAGADO']])->count();
            $nuevafecha = strtotime('+1 day', strtotime($nuevafecha));
            $nuevafecha = date('Y-m-j', $nuevafecha);
        }
        if (count($ventas) > 0) {
            return response()->json(['data' => $ventas, 'mensaje' => 'Datos encontrados'], 200);
        } else {
            return response()->json(['data' => 'null', 'mensaje' => 'Datos no encontrados'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error Inesperado'], 500);
    }

    function orderMultiDimensionalArray($toOrderArray, $field, $inverse = false) {
        $position = array();
        $newRow = array();
        foreach ($toOrderArray as $key => $row) {
            $position[$key] = $row[$field];
            $newRow[$key] = $row;
        }
        if ($inverse) {
            arsort($position);
        } else {
            asort($position);
        }
        $returnArray = array();
        foreach ($position as $key => $pos) {
            $returnArray[] = $newRow[$key];
        }
        return $returnArray;
    }

}
