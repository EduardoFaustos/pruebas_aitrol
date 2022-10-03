<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Insumos_area_nuevo extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'insumos_area_nuevo';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public $timestamps= false;
}