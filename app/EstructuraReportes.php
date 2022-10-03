<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class EstructuraReportes extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'estructura_reportes';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function plan()
    {
        return $this->belongsTo('Sis_medico\Plan_Cuentas','id_plan', 'id');
    }

    public function grupo()
    {
        return $this->belongsTo('Sis_medico\GrupoReportes','id_grupo', 'id');
    }

}
