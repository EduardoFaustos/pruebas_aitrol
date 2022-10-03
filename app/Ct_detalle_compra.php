<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_detalle_compra extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_detalle_compra';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function termino(){
        
        return $this->belongsTo('Sis_medico\Ct_compras', 'id_ct_compras', 'id');
    }

    public function afActivo(){
        return $this->belongsTo('Sis_medico\AfActivo', 'codigo', 'codigo');
    }

    public function cabecera(){
        
        return $this->belongsTo('Sis_medico\Ct_compras', 'id_ct_compras', 'id');
    }
}
