<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'descripcion', 'precio', 'ganancia_empleado', 'user_change', 'categorie_id', 'created_at', 'updated_at'
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

    public function categorie() {
        return $this->belongsTo('App\Category');
    }

    public function detalles() {
        return $this->hasMany('App\Detalle');
    }

}
