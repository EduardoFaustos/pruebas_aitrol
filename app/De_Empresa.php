<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class De_Empresa extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'de_empresa';


    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public static function getDatos($id)
    {
        return De_Empresa::where('id_empresa', $id)->first();
    }
}
