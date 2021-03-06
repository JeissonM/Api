<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'nombre', 'user_change', 'created_at', 'updated_at'
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

    public function services() {
        return $this->hasMany('App\Service');
    }

    public function empleados(){
        return $this->belongsToMany('App\Empleado');
    }
}
