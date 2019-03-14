<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movimientohistorialcaja extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'fecha', 'descripcion', 'monto', 'tipo', 'user_change', 'historialcaja_id', 'created_at', 'updated_at'
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

    public function historialcaja() {
        return $this->belongsTo('App\Historialcaja');
    }

}
