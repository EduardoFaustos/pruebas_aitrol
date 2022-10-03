<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
class SubModulo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'submodulos';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    
}