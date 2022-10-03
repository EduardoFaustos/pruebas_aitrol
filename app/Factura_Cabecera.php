<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Factura_Cabecera extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'factura_cabecera';

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

    public function seguro()
    {
        return $this->belongsTo('Sis_medico\Seguro', 'id_seguro');
    }
    
    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa', 'id_empresa');
    }

    public function factura_informacion_factura()
    {
        return $this->belongsTo('Sis_medico\Factura_Informacion_Factura', 'id_factura_info_factura');
    }
}
 