<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Ct_productos;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Producto_Precio_Aprobado;

class PrecioProductoAprobadoController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    // private function rol()
    // {
    //     $rolUsuario = Auth::user()->id_tipo_usuario;
    //     $id_auth    = Auth::user()->id;
    //     if (in_array($rolUsuario, array(1, 4, 5, 20, 22)) == false) {
    //         return true;
    //     }
    // }

    public function index(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $productos = Ct_productos::join('producto_precio_aprobado as pa', 'pa.id_producto', 'ct_productos.id')
                        ->where('ct_productos.id_empresa', $id_empresa)
                        ->select('ct_productos.codigo', 'ct_productos.nombre', 'pa.*')
                        ->where('pa.estado', 1)
                        ->groupBy('pa.id_producto')->get();
        //dd($productos);
        return view('contable/precio_producto/index', ['productos'=> $productos]);
    }

    public function create(Request $request){
        return view('contable/precio_producto/create');
    }

    public function buscarTabla(Request $request){
        $precios = Producto_Precio_Aprobado::where('id_producto', $request->id_producto)->get();
        $contador = 0;
        $table = "";
        foreach ($precios as $value){
            $importante = "";
            $aprobado = "";
            $button = "<button onclick='' type='button' class='btn btn-success'>Aprobar precio</button>";
            if($value->importante == 1){
                $importante = "checked";
            }
            if($value->aprobado == 1){
                $aprobado = "checked";
                $button = '';
            }
            $table .= "
            <tr>
                <td>{$value->precio}</td>
                <td>{$value->observacion}</td>
                <td> <input type='checkbox' {$importante}> </td>
                <td> <input type='checkbox' {$aprobado}> </td>
                <th> {$button} </th>
            </tr>";
        }
        return ["table" =>$table];

    }

    public function store(Request $request){
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"]; 
        DB::beginTransaction();
        try {
            for($i = 0; $i < count($request->precio) ; $i++){
                $data = [
                    'precio'                => $request->precio[$i],
                    'importante'            => $request->importante[$i],
                    'aprobado'              => $request->aprobado[$i],
                    'estado'                => 1,
                    'id_usuariocrea'        => $id_usuario,
                    'id_usuariomod'         => $id_usuario,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    'observacion'           => $request->descripcion[$i],
                    'id_producto'           => $request->producto,
                    'observacion_divisa'    => ""
                ];
                Producto_Precio_Aprobado::create($data);
            }
            
            DB::commit();
            return ["status"=> "success", "msj"=> "Guardado Correctamente"];
        } catch (\Exception $e) {

            DB::commit();
            return ["status"=> "error", "msj"=> "Ocurrio un error al guardar",  "exp"=> $e->getMessage()];
        }
    }

    public function delete(Request $request){

        DB::beginTransaction();

        try{
            Producto_Precio_Aprobado::where('estado', 1)
            ->where('id_producto', $request->id_producto)
            ->update(['estado' => 0]);
            
            DB::commit();
            return ['status' => 'success', 'msj' => 'Guardado Correctamente'];
        }catch(\Exception $e){

            DB::rollback();
            return ['status' => 'error', 'msj' => 'No se pudo eliminar', 'exp' => $e->getMessage()];
        }
        
    }

}
