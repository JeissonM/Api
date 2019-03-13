<?php

namespace App\Http\Controllers;

use App\Empleado;
use App\User;
use Illuminate\Http\Request;

class EmpleadoController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $empleados = Empleado::all();
        $empleados->each(function($e) {
            $e->categories;
        });
        if (count($empleados) > 0) {
            return response()->json(['data' => $empleados, 'mensaje' => 'Datos encontrados'], 200);
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
     * @params {identificacion,nombres,apellidos:null,celular,email,sexo,categories:array}
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $empleado = new Empleado($request->all());
        $empleado->user_change = $this->getApitokenAuthenticated($request->api_token)->identificacion;
        if ($empleado->save()) {
            $empleado->categories()->sync($request->categories);
            return response()->json(['data' => 'null', 'mensaje' => 'Datos guardados'], 200);
        } else {
            return response()->json(['data' => 'null', 'mensaje' => 'Datos no guardados'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error Inesperado'], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function show(Empleado $empleado) {
        if (!$empleado) {
            return response()->json(['data' => 'null', 'mensaje' => 'Datos no encontrados'], 200);
        } else {
            $empleado->categories;
            return response()->json(['data' => $empleado, 'mensaje' => 'Datos encontrados'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error Inesperado'], 500);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function edit(Empleado $empleado) {
        //not implemented
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Empleado $empleado) {
        if (!$empleado) {
            return response()->json(['data' => 'null', 'mensaje' => 'Recurso no encontrado, datos no actualizados'], 200);
        } else {
            foreach ($empleado->attributesToArray() as $key => $value) {
                if (isset($request->$key)) {
                    $empleado->$key = $request->$key;
                }
            }
            $empleado->user_change = $this->getApitokenAuthenticated($request->api_token)->identificacion;
            if ($empleado->save()) {
                $empleado->categories()->sync($request->categories);
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
     * @param  \App\Empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function destroy(Empleado $empleado) {
        if (!$empleado) {
            return response()->json(['data' => 'null', 'mensaje' => 'Datos no encontrados'], 200);
        } else {
            if ($empleado->delete()) {
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
