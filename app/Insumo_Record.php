<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Insumo_Record extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'insumo_record';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    
    public function insumo()
    {
        return $this->belongsTo('Sis_medico\Insumo_General','id_insumo');   
    }   
    
}