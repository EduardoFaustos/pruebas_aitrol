<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Detalle_Rubro_Credito extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_detalle_rubro_credito';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public static function getDetalles($id)
    {
        return Ct_Detalle_Rubro_Credito::where('id_nt_cred_client', $id)->get();
    }
}
