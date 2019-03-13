<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $users = User::all();
        if (count($users) > 0) {
            return response()->json(['data' => $users, 'mensaje' => 'Datos encontrados'], 200);
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
     * @params {identificacion, nombres, apellidos:null, email, password}
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $user = new User($request->all());
        $user->password = Hash::make($request->password);
        $user->api_token = Str::random(60);
        $user->user_change = $this->getApitokenAuthenticated($request->api_token)->identificacion;
        if ($user->save()) {
            return response()->json(['data' => 'null', 'mensaje' => 'Datos guardados'], 200);
        } else {
            return response()->json(['data' => 'null', 'mensaje' => 'Datos no guardados'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error inesperado'], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['data' => 'null', 'mensaje' => 'Datos no encontrados'], 200);
        } else {
            return response()->json(['data' => $user, 'mensaje' => 'Datos encontrados'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error inesperado'], 500);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        // not implemented
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @params {identificacion, nombres, apellidos:null, email, estado}
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['data' => 'null', 'mensaje' => 'Recurso no encontrado, datos no actualizados'], 200);
        } else {
            foreach ($user->attributesToArray() as $key => $value) {
                if (isset($request->$key)) {
                    if ($key == 'api_token' || $key == 'password') {
                        //not implemented
                    } else {
                        $user->$key = $request->$key;
                    }
                }
            }
            $u = Auth::user();
            $user->user_change = $this->getApitokenAuthenticated($request->api_token)->identificacion;
            if ($user->save()) {
                return response()->json(['data' => 'null', 'mensaje' => 'Datos actualizados'], 200);
            } else {
                return response()->json(['data' => 'null', 'mensaje' => 'Datos no actualizados'], 200);
            }
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error inesperado'], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['data' => 'null', 'mensaje' => 'Recurso no encontrado, datos no eliminados'], 200);
        } else {
            if ($user->delete()) {
                return response()->json(['data' => 'null', 'mensaje' => 'Datos eliminados'], 200);
            } else {
                return response()->json(['data' => 'null', 'mensaje' => 'Datos no eliminados'], 200);
            }
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error inesperado'], 500);
    }

    /**
     * get api_token of the user logged for authentication of the transactions to API 
     * 
     * @param {identificacion}
     * @return String api_token
     */
    public function getApitoken($identificacion) {
        $user = User::where('identificacion',$identificacion)->first();
        if (!$user) {
            return response()->json(['data' => 'null', 'mensaje' => 'No existe un usuario autenticado'], 200);
        } else {
            return response()->json(['data' => $user->api_token, 'mensaje' => 'api_token del usuario autenticado en la sessión'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error inesperado'], 500);
    }

    /**
     * get the authenticated user 
     * 
     * @param {api_token}
     * @return \Illuminate\Http\Response
     */
    public function getAuthenticatedUser($api_token) {
        $user = $this->getApitokenAuthenticated($api_token);
        if (!$user) {
            return response()->json(['data' => 'null', 'mensaje' => 'No existe un usuario autenticado'], 200);
        } else {
            return response()->json(['data' => $user, 'mensaje' => 'Usuario autenticado en la sessión'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error inesperado'], 500);
    }

    /**
     * password change for the user indicated
     * 
     * @param String identificacion, String newpassword
     * @return \Illuminate\Http\Response
     */
    public function passwordChange($identificacion, $newpassword) {
        $user = User::where('identificacion', $identificacion)->first();
        if (!$user) {
            return response()->json(['data' => 'null', 'mensaje' => 'No existe un usuario para la identificación ' . $identificacion], 200);
        } else {
            //cambiar
            $user->password = Hash::make($newpassword);
            if ($user->save()) {
                return response()->json(['data' => 'null', 'mensaje' => 'Contraseña actualizada con exito'], 200);
            } else {
                return response()->json(['data' => 'null', 'mensaje' => 'Contraseña no actualizada'], 200);
            }
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error inesperado'], 500);
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     * @params {email, password}
     * @return Response
     */
    public function authenticate(Request $request) {
        $credentials = $request->only('identificacion', 'password');
        if ($this->attemptLogin($request)) {
            return response()->json(['data' => $this->guard()->user()->api_token, 'mensaje' => 'Sessión iniciada'], 200);
        } else {
            return response()->json(['data' => 'null', 'mensaje' => 'Sessión no iniciada'], 200);
        }
        return response()->json(['data' => 'null', 'mensaje' => 'Error inesperado'], 500);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request) {
        return $this->guard()->attempt(
                        $this->credentials($request), $request->filled('remember')
        );
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard() {
        return Auth::guard();
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request) {
        return $request->only('identificacion', 'password');
    }

    /*
     * logout
     */

    public function logout() {
        Auth::logout();
        return response()->json(['data' => 'null', 'mensaje' => 'Sessión cerrada'], 200);
    }

    /**
     * get a user authenticated
     */
    public function getApitokenAuthenticated($api_token) {
        $user = User::where('api_token', $api_token)->first();
        return $user;
    }

}
