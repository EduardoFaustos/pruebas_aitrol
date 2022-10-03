<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'proveedor';
    
    protected $keyType = 'string';

    public $incrementing = false;

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function acreedor()
    {
        return $this->belongsTo('Sis_medico\Ct_Debito_bancario', 'id_acreedor');
    }
    public function compras()
    {
        return $this->hasMany('Sis_medico\Ct_compras', 'proveedor','id');
    }
}
