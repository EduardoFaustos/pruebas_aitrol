<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Insumo_General extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'insumo_general';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    
}