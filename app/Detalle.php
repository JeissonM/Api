<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detalle extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'descuento', 'valorDescontado', 'valorServicio', 'gananciaEmpleado', 'totalServicio', 'empleado_id', 'service_id', 'venta_id', 'created_at', 'updated_at'
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

    public function empleado() {
        return $this->belongsTo('App\Empleado');
    }

    public function service() {
        return $this->belongsTo('App\Service');
    }

    public function venta() {
        return $this->belongsTo('App\Venta');
    }

}
