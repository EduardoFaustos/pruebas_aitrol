<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Pruebap extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'producto_prueba';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

}