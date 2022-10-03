<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'paciente';

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public function usuario()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuario');
    }
    public function seguro()
    {
        return $this->belongsTo('Sis_medico\Seguro', 'id_seguro');
    }

    //AGREGADO 08/04/2019

    public function agenda()
    {
        return $this->hasMany('Sis_medico\Agenda', 'id_paciente');
    }

    public function historia_clinica()
    {
        return $this->hasMany('Sis_medico\Historiaclinica', 'id_paciente');
    }

    public function paciente_biopsia()
    {
        return $this->belongsTo('Sis_medico\Paciente_Biopsia', 'id', 'id_paciente');
    }

    public function paciente_doctor()
    {
        return $this->belongsTo('Sis_medico\Paciente_Doctor', 'id', 'id_paciente');
    }

    public function ho_datos_paciente()
    {
        return $this->belongsTo('Sis_medico\Ho_Datos_Paciente', 'id', 'id_paciente');
    }

    public function a_alergias()
    {
        return $this->hasMany('Sis_medico\Paciente_Alergia', 'id_paciente');
    }

}
