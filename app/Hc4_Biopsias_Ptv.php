<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Hc4_Biopsias_Ptv extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hc4_biopsia_ptv';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function paciente()
    {
        return $this->belongsTo('Sis_medico\Paciente', 'id_paciente');
    }

    public function doctor()
    {
        return $this->belongsTo('Sis_medico\User', 'id_doctor_solicita');
    }

    public function hc_procedimientos()
    {
        return $this->belongsTo('Sis_medico\hc_procedimientos', 'id_hc_procedimientos');
    }

    public function user_crea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }

    public function detalles()
    {
        return $this->hasMany('Sis_medico\Hc4_Biopsias_Ptv_Detalle','id_hc4_biopsia_ptv');   
    }

    public function tipo()
    {
        return $this->belongsTo('Sis_medico\Hc4_Tipo_Biopsias_Ptv', 'id_hc4_tipo_biopsias_ptv');
    }
    public function historiaclinica()
    {
        return $this->belongsTo('Sis_medico\hc_procedimientos', 'hcid');
    }

    
}
 