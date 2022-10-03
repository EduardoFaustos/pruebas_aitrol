<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_productos extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_productos';
    //protected $primaryKey = null; //REVISAR MUCHO OJO
    public $incrementing = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public static function getNombreProducto($id_producto, $id_empresa)
    {
        $producto = Ct_productos::where('id', $id_producto)
            //->where('id_empresa', $id_empresa)
            ->first();
        if ($producto != '') {
            return $producto->nombre;
        }
    }

    public function tarifarios()
    {
        return $this->hasMany('Sis_medico\Ct_Productos_Tarifario', 'id_producto');
    }

    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa', 'id_empresa', 'id');
    }

    public function paquetes()
    {
        return $this->hasMany('Sis_medico\Ct_productos_paquete', 'id_producto');
    }

    public function precio_aprobado()
    {
        return $this->hasMany('Sis_medico\Producto_Precio_Aprobado', 'id_producto');
    }
}
