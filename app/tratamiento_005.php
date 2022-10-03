<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Tratamiento_005 extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tratamiento_005';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function scopeMedico_tratamiento($query,$medico_tratamiento){
     if($medico_tratamiento)

        return $query->where('medico_tratamiento','LIKE',"%$medico_tratamiento%");
    }
    public function scopeDescripcion_tratamiento($query,$descripcion_tratamiento){
     if($descripcion_tratamiento)

    return $query->where('descripcion_tratamiento','LIKE',"%$descripcion_tratamiento%");
    }

}
 