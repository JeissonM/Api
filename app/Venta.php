<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'base', 'descuentoAplicado', 'fecha', 'gananciaNegocio', 'gananciaEmpleado', 'valorAgregado', 'cliente', 'total', 'estado', 'user_change', 'created_at', 'updated_at'
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

    public function detalles() {
        return $this->hasMany('App\Detalle');
    }

}
