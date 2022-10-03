<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Diagnostico_005 extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'diagnostico_005';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

     public function scopeMedico_diagnostico($query,$medico_diagnostico){
     if($medico_diagnostico)

        return $query->where('medico_diagnostico','LIKE',"%$medico_diagnostico%");
    }
    public function scopeOperacion_diagnostico($query,$operacion_diagnostico){
     if($operacion_diagnostico)

    return $query->where('operacion_diagnostico','LIKE',"%$operacion_diagnostico%");
    }
    public function scopeCie_diagnostico($query,$cie_diagnostico){
     if($cie_diagnostico)

    return $query->where('cie_diagnostico','LIKE',"%$cie_diagnostico%");
    }

}
 