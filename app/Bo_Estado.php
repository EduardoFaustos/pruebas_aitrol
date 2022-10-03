<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Bo_Estado extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bo_estado';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
