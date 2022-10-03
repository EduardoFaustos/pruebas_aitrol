<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_BodegaInterna extends Model
{
    //
    protected $table = 'ct_bodega_interna';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /*public function empresa()
    {
    return $this->belongsTo('Sis_medico\Empresa','id_empresa')->with('empresa');

    }*/

    /*public function usuario()
    {
    return $this->belongsTo('Sis_medico\User','id_user')->with('usuario');

    }*/

    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa', 'id_empresa');
    }

}
