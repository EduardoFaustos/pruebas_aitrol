<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class SubidaTarifarios extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subida_tarifario';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
   
    
}