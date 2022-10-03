<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Medidas_generales extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'medidas_generales';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

   public function scopeMedico_general($query,$medico_general){
     if($medico_general)

        return $query->where('medico_general','LIKE',"%$medico_general%");
    }
    public function scopeDescripcion_general($query,$descripcion_general){
     if($descripcion_general)

    return $query->where('descripcion_general','LIKE',"%$descripcion_general%");
    }

}
 