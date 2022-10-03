<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class De_Info_Tributaria extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'de_info_tributaria';


    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public static function updateNumDocumento($idEm, $numDocumento, $secuancialDocumento, $id_sucursal, $id_caja, $id_maestro)
    {
        $array = [
            'numero_factura' => $numDocumento,
            'secuencial_nro' => $secuancialDocumento,
        ];
        De_Info_Tributaria::where('id_maestro_documentos', $id_maestro)
            ->where('id_sucursal', $id_sucursal)
            ->where('id_caja', $id_caja)
            ->where('id_empresa', $idEm)
            ->update($array);
    }

    public static function getDatos($id, $esta, $emis, $idDoc)
    {
        //DB::enableQueryLog();
        return De_Info_Tributaria::where('id_empresa', $id)
            ->where('cod_sucursal', $esta)
            ->where('cod_caja', $emis)
            ->where('id_maestro_documentos', $idDoc)
            ->first([
                'secuencial_nro',
                'id_sucursal',
                'id_caja',
                'id_maestro_documentos'
            ]);
        /*echo '<pre>';
        print_r(DB::getQueryLog());
        exit;*/
    }
}
