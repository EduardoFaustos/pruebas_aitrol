<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class FarmaCategorias extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'farma_categorias';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function subcategorias()
    {
        return $this->hasMany('Sis_medico\FarmaSubcategoria','id_categoria'); 
    }


}