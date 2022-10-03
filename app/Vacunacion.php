<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Vacunacion extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vacunacion';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

}