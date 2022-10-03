<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Detalle_Cruce_Cuentas_Clientes extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_detalle_cruce_cuentas_clientes';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public function cabecera()
    {
        return $this->belongsTo('Sis_medico\Ct_Cruce_Cuentas_Clientes','id_comprobante');
    }

}
