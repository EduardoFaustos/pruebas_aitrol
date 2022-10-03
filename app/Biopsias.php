<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Biopsias extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hc4_biopsias';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    protected $keyType = 'string';
    
    
    public function paciente()
    {
        return $this->belongsTo('Sis_medico\Paciente','id_paciente');
    }

    public function doctor()
    {
        return $this->belongsTo('Sis_medico\User','id_doctor');
    }

}

 