<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Historialcaja extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'dineroCaja', 'egresos', 'fechaApertura', 'fechaCierre', 'gananciaLocal', 'inconsistencia', 'ingresos', 'montoInicial', 'montoAgregado', 'montoConfirmado', 'user_change', 'anterior', 'created_at', 'updated_at'
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

}
