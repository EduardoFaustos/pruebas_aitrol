<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
class Modulo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'modulo';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    
}