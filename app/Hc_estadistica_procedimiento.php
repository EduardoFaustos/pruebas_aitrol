<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Hc_estadistica_procedimiento extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hc_estadistica_procedimiento';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    public function historia_orden()
    {
        return $this->belongsTo('Sis_medico\Historiaclinica','hcid_orden');
    }
    public function historia_proc()
    {
        return $this->belongsTo('Sis_medico\Historiaclinica','hcid_proc');
    }
    public function seguro()
    {
        return $this->belongsTo('Sis_medico\Seguro','id_seguro');   
    }
    public function doctor()
    {
        return $this->belongsTo('Sis_medico\User','id_doctor1');   
    }
   
}
