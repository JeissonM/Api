<?php

namespace App\Http\Controllers;

use App\Service;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $servicies = Service::all();
        if (count($servicies) > 0) {
            $servicies->each(function($item) {
                $item->categorie;
            });
            return response()->json(['data' => $servicies, 'mensaje' => 'Datos encontrados'], 200);
        } else {
            return response()->json(['data' => 'null', 'mensaje' => 'Datos no encontrados'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error inesperado'], 500);
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
     * @params {descripcion,precio,ganancia_empleado,categorie_id}
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $service = new Service($request->all());
        $u = $this->getApitokenAuthenticated($request->api_token);
        $service->user_change = $u->identificacion;
        if ($service->save()) {
            return response()->json(['data' => 'null', 'mensaje' => 'Datos guardados'], 200);
        } else {
            return response()->json(['data' => 'null', 'mensaje' => 'Datos no guardados'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error Inesperado'], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service) {
        if (!$service) {
            return response()->json(['data' => 'null', 'mensaje' => 'Datos no encontrados'], 200);
        } else {
            return response()->json(['data' => $service, 'mensaje' => 'Datos encontrados'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error Inesperado'], 500);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function edit(Service $service) {
        //not implementd
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Service  $service
     * @params {descripcion,precio,ganancia_empleado,categorie_id}
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Service $service) {
        if (!$service) {
            return response()->json(['data' => 'null', 'mensaje' => 'Recurso no encontrado, datos no actualizados'], 200);
        } else {
            foreach ($service->attributesToArray() as $key => $value) {
                if (isset($request->$key)) {
                    $service->$key = $request->$key;
                }
            }
            $service->user_change = $this->getApitokenAuthenticated($request->api_token)->identificacion;
            if ($service->save()) {
                return response()->json(['data' => 'null', 'mensaje' => 'Datos actualizados'], 200);
            } else {
                return response()->json(['data' => 'null', 'mensaje' => 'Datos no actualizados'], 200);
            }
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error Inesperado'], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service) {
        if (!$service) {
            return response()->json(['data' => 'null', 'mensaje' => 'Datos no encontrados'], 200);
        } else {
            if ($service->delete()) {
                return response()->json(['data' => 'null', 'mensaje' => 'Datos eliminados'], 200);
            } else {
                return response()->json(['data' => 'null', 'mensaje' => 'Datos no eliminados'], 200);
            }
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error Inesperado'], 500);
    }

    /**
     * get a user authenticeted
     */
    public function getApitokenAuthenticated($api_token) {
        $user = User::where('api_token', $api_token)->first();
        return $user;
    }

}
