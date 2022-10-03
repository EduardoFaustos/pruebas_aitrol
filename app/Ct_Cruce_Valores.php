<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Cruce_Valores extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_cruce_valores';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa', 'id_empresa');
    }
    public function proveedor()
    {
        return $this->belongsTo('Sis_medico\Ct_Acreedores', 'id_proveedor', 'id_proveedor');
    }

    public function proveedor_cruce_valores()
    {
        return $this->belongsTo('Sis_medico\Proveedor', 'id_proveedor', 'id');
    }

    public function usuario(){
        
        return $this->belongsTo('Sis_medico\User','id_usuariocrea');
    }


}
