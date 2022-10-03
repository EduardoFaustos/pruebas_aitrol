<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Conciliacion_Bancaria extends Model
{
    //
    protected $table = 'ct_conciliacion_bancaria';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function empresas()
    {
        return $this->belongsTo('Sis_medico\Empresa','empresa');
    }
}
