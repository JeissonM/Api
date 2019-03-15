<?php

namespace App\Http\Controllers;

use App\Category;
use App\Service;
use App\User;
use App\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $categories = Category::all();
        if (count($categories) > 0) {
            return response()->json(['data' => $categories, 'mensaje' => 'Datos encontrados'], 200);
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
     * @params {nombre}
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $category = new Category($request->all());
        $category->user_change = $this->getApitokenAuthenticated($request->api_token)->identificacion;
        if ($category->save()) {
            return response()->json(['data' => 'null', 'mensaje' => 'Datos guardados'], 200);
        } else {
            return response()->json(['data' => 'null', 'mensaje' => 'Datos no guardados'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error Inesperado'], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category) {
        if (!$category) {
            return response()->json(['data' => 'null', 'mensaje' => 'Datos no encontrados'], 200);
        } else {
            return response()->json(['data' => $category, 'mensaje' => 'Datos encontrados'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error Inesperado'], 500);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category) {
        //not implemented
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @params {nombre}
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category) {
        if (!$category) {
            return response()->json(['data' => 'null', 'mensaje' => 'Recurso no encontrado, datos no actualizados'], 200);
        } else {
            foreach ($category->attributesToArray() as $key => $value) {
                if (isset($request->$key)) {
                    $category->$key = $request->$key;
                }
            }
            $category->user_change = $this->getApitokenAuthenticated($request->api_token)->identificacion;
            if ($category->save()) {
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
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category) {
        if (!$category) {
            return response()->json(['data' => 'null', 'mensaje' => 'Datos no encontrados'], 200);
        } else {
            $services = Service::where('categorie_id', $category->id)->get();
            $empleados = DB::table('category_empleado')->where('category_id', $category->id)->get();
            if (count($services) > 0 || count($empleados) > 0) {
                return response()->json(['data' => 'null', 'mensaje' => 'El recurso que quiere eliminar tiene datos asociados, no se puede eliminar'], 200);
            } else {
                if ($category->delete()) {
                    return response()->json(['data' => 'null', 'mensaje' => 'Datos eliminados'], 200);
                } else {
                    return response()->json(['data' => 'null', 'mensaje' => 'Datos no eliminados'], 200);
                }
            }
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error Inesperado'], 500);
    }

    /**
     * get all the services of a specific category
     * @params {category}
     */
    public function getServices($categorie_id) {
        $services = Service::where('categorie_id', $categorie_id)->get();
        if (count($services) > 0) {
            return response()->json(['data' => $services, 'mensaje' => 'Datos encontrados'], 200);
        } else {
            return response()->json(['data' => 'null', 'mensaje' => 'Datos no encontrados'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error Inesperado'], 500);
    }

    /**
     * get all the employees of a specific category
     * @params {category}
     */
    public function getEmpleados($category_id) {
        $employees = DB::table('category_empleado')->where('category_id', $category_id)->get();
        if (count($employees) > 0) {
            $empleados = null;
            foreach ($employees as $item) {
                $e = null;
                $e = Empleado::find($item->empleado_id);
                $empleados[] = $e;
            }
            return response()->json(['data' => $empleados, 'mensaje' => 'Datos encontrados'], 200);
        } else {
            return response()->json(['data' => 'null', 'mensaje' => 'Datos no encontrados'], 200);
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
