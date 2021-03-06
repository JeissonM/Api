<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'identificacion', 'nombres', 'apellidos', 'celular', 'email', 'sexo', 'saldofavor', 'saldocontra', 'turnos', 'created_at', 'updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
            /*
             * 
             */
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
            /*
             * 
             */
    ];

    public function categories() {
        return $this->belongsToMany('App\Category');
    }

    public function movimientostransaccions() {
        return $this->hasMany('App\Movimientosempleado');
    }

    public function detalles() {
        return $this->hasMany('App\Detalle');
    }

}
