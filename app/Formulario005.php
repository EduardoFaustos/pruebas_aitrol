<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Formulario005 extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'formulario005';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

   public function scopeMedicamento($query,$medicamento){
     if($medicamento)

        return $query->where('medicamento','LIKE',"%$medicamento%");
    }
}
 