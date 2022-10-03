<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Planilla_Cabecera_Labs extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'planilla_cabecera_labs';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function detalles(){
       return $this->hasMany('Sis_medico\Planilla_Detalle_Labs', 'id_planilla_cabecera', 'id');
    }


}
 