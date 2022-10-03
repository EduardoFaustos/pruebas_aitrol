<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class LabsProcesoFacturacioncc extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'labs_proceso_facturacion_cc';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function detalles()
    {
        return $this->hasMany('Sis_medico\LabsDetalleFacturacioncc','id_proceso');
    }

    
}
 