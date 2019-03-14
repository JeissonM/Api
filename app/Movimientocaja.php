<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movimientocaja extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'fecha', 'descripcion', 'monto', 'tipo', 'user_change', 'caja_id', 'created_at', 'updated_at'
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

    public function caja() {
        return $this->belongsTo('App\Caja');
    }

}
