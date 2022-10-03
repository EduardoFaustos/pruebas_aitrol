<?php

namespace Sis_medico\Http\Controllers\archivo_plano;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\ApPlantilla;
use Sis_medico\ApPlantillaItem;
use Sis_medico\ApProcedimiento;
use Sis_medico\Http\Controllers\Controller;
use Validator;

class ApPlantillaController extends Controller
{
    protected $redirectTo = '/';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;
        if (in_array($rolUsuario, array(1,11,22)) == false) {
            return true;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index2()
    {
        return view('archivo_plano/plantillas/frmcrear2');
    }

    public function index(Request $request)
    {
        //$plantilla = $request->input('descripcion');

        /*$plantillas = ApPlantilla::select('id','codigo','descripcion','desc_comp','estado')
        ->where('descripcion', 'LIKE', '%' . $plantilla . '%')
        ->groupBy('codigo')
        ->paginate(5);*/

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $plantillas = ApPlantilla::where('estado', '1')->paginate(10);

        //$plantillas->appends(['search' => $plantilla]);
        return view('archivo_plano/plantillas/index', ['plantillas' => $plantillas, 'plantilla' => '']);
    }

    public function buscar_plantillas(Request $request)
    {
        $plantilla = $request['descripcion'];

        $plantillas = ApPlantilla::where('estado', '1');

        if ($plantilla != null) {
            $plantillas = $plantillas->where(function ($jq1) use ($plantilla) {
                $jq1->orwhereraw('descripcion LIKE ?', ['%' . $plantilla . '%']);
            });
        }

        $plantillas = $plantillas->paginate(10);

        //$plantillas->appends(['search' => $plantilla]);
        return view('archivo_plano/plantillas/index', ['plantillas' => $plantillas, 'plantilla' => $plantilla]);
    }

    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $pro                  = new ApProcedimiento();
        $pro->tipo            = $request->txttipo;
        $pro->codigo          = $request->txtcodigo;
        $pro->descripcion     = $request->txtdescripcion;
        $pro->estado          = $request->txtestado;
        $pro->id_usuariocrea  = $idusuario;
        $pro->id_usuariomod   = $idusuario;
        $pro->ip_creacion     = $ip_cliente;
        $pro->ip_modificacion = $ip_cliente;
        $pro->save();
        return redirect('administracion/procedimientos');
    }

//create nivel
    public function store2(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $pro                   = new ApProcedimientoNivel();
        $pro->id_procedimiento = $request->id_procedimiento;
        $pro->cod_conv         = $request->txtnivel;
        $pro->desc_conv        = $request->txtdes;
        $pro->anexo            = $request->txtanexo;
        $pro->uvr1             = $request->uvr1;
        $pro->uvr2             = $request->uvr2;
        $pro->uvr3             = $request->uvr3;
        $pro->prc1             = $request->prc1;
        $pro->prc2             = $request->prc2;
        $pro->prc3             = $request->prc3;
        $pro->uvr1a            = $request->uvr1a;
        $pro->uvr2a            = $request->uvr2a;
        $pro->uvr3a            = $request->uvr3a;
        $pro->prc1a            = $request->prc1a;
        $pro->prc2a            = $request->prc2a;
        $pro->prc3a            = $request->prc3a;
        $pro->separado         = $request->txtseparado;
        $pro->estado           = $request->txtestado;

        $pro->id_usuariocrea  = $idusuario;
        $pro->id_usuariomod   = $idusuario;
        $pro->ip_creacion     = $ip_cliente;
        $pro->ip_modificacion = $ip_cliente;
        $pro->save();
        return redirect('administracion/procedimientos');
    }

    public function index4()
    {
        return view('archivo_plano/procedimientos/frmasignonivel');
    }

    public function fetch(Request $request)
    {
        if ($request->get('query')) {
            $query  = $request->get('query');
            $data   = ApProcedimiento::where('descripcion', 'LIKE', "%{$query}%")->get();
            $output = '<ul id="cierrate" class="dropdown-menu" style="display:block; position:relative">';
            foreach ($data as $row) {
                $output .= '
       <li><a href="#"> Codigo: ' . $row->codigo . '    |    Descripción: ' . $row->descripcion . '</a></li>
       <input type="hidden" name="id_procedimiento" value="' . $row->id . '">
       ';
            }
            $output .= '</ul>';
            echo $output;
        }
    }

    public function insert(Request $request)
    {

        if ($request->ajax()) {
            $rules = array(
                'id_procedimiento.*' => 'required',
                'cantidad.*'         => 'required',
                'honorario.*'        => 'required',
                'orden.*'            => 'required',
                'separado.*'         => 'required',
            );
            $error = Validator::make($request->all(), $rules);
            if ($error->fails()) {
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $id_procedimiento = $request->id_procedimiento;
            $cantidad         = $request->cantidad;
            $honorario        = $request->honorario;
            $orden            = $request->orden;
            $separado         = $request->separado;

            $ip_cliente           = $_SERVER["REMOTE_ADDR"];
            $idusuario            = Auth::user()->id;
            $pla                  = new ApPlantilla();
            $pla->codigo          = $request->txtcodigo;
            $pla->descripcion     = $request->txtdescripcion;
            $pla->desc_comp       = $request->txtdescripcioncompleta;
            $pla->estado          = $request->txtestado;
            $pla->id_usuariocrea  = $idusuario;
            $pla->id_usuariomod   = $idusuario;
            $pla->ip_creacion     = $ip_cliente;
            $pla->ip_modificacion = $ip_cliente;
            $pla->save();
            $insertedId  = $pla->codigo;
            $insert_data = null;

            $count = 0;

            foreach ($request->codigo as $value) {
                if (!is_null($value)) {
                    $porciones   = explode("|", $value);
                    $porciones_2 = explode(":", $porciones[0]);
                    $codigo_2    = str_replace(" ", "", $porciones_2[1]);
                    $tipo        = ApProcedimiento::where('codigo', $codigo_2)->first();

                    if (!is_null($tipo)) {
                        $data = array(

                            'cod_plantilla'    => $insertedId,
                            'id_procedimiento' => $codigo_2,
                            'cantidad'         => $cantidad[$count],
                            'honorario'        => $honorario[$count],
                            'orden'            => $orden[$count],
                            'separado'         => $separado[$count],
                            'tipo_item'        => $tipo->tipo,
                            'id_usuariocrea'   => $idusuario,
                            'id_usuariomod'    => $idusuario,
                            'ip_creacion'      => $ip_cliente,
                            'ip_modificacion'  => $ip_cliente,
                        );
                        $insert_data[] = $data;
                    }
                }

                $count++;
                # code...
            }

            if (!is_null($insert_data)) {
                ApPlantillaItem::insert($insert_data);
            }

            return response()->json([
                'success' => 'Plantilla e Items Grabados.',
            ]);
        }
    }

    public function search(Request $request)
    {
        $nombres = ApProcedimiento::where('estado', 1);

        $nombres = $nombres->where(function ($jq1) use ($request) {
            $jq1->orwhere('descripcion', 'LIKE', '%' . $request->term . '%')
                ->orwhere('codigo', 'LIKE', '%' . $request->term . '%');
        })->get();
        $data = null;

        foreach ($nombres as $nombre) {
            $data[] = array('value' => 'Codigo: ' . $nombre->codigo . '|    Descripción: ' . $nombre->descripcion);
        }

        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    //edito plantilla
    public function edit($id)
    {
        $fila = ApPlantilla::where('codigo', $id)->first();
        //   $item = ApPlantillaItem::select('id_procedimiento','cantidad','honorario','orden','separado')
        //  ->where('cod_plantilla',$id)->get();

        $item = ApPlantillaItem::join('ap_procedimiento', 'ap_plantilla_items.id_procedimiento', '=', 'ap_procedimiento.codigo')
            ->select('ap_procedimiento.codigo', 'ap_procedimiento.descripcion', 'ap_plantilla_items.cantidad', 'ap_plantilla_items.honorario', 'ap_plantilla_items.orden',
                'ap_plantilla_items.separado')
            ->where('ap_plantilla_items.cod_plantilla', $id)->get();
        //dd($item);
        $itemc = ApPlantillaItem::select('id_procedimiento', 'cantidad', 'honorario', 'orden', 'separado')
            ->where('cod_plantilla', $id)->count();
        return view('archivo_plano/plantillas/editar', compact('item', 'fila', 'itemc'));
    }

    public function veritem($id)
    {
        $fila = ApPlantilla::where('codigo', $id)->first();
        //   $item = ApPlantillaItem::select('id_procedimiento','cantidad','honorario','orden','separado')
        //  ->where('cod_plantilla',$id)->get();
        $item = ApPlantillaItem::join('ap_procedimiento', 'ap_plantilla_items.id_procedimiento', '=', 'ap_procedimiento.codigo')
            ->select('ap_procedimiento.codigo', 'ap_procedimiento.descripcion', 'ap_plantilla_items.cantidad', 'ap_plantilla_items.honorario', 'ap_plantilla_items.orden',
                'ap_plantilla_items.separado')
            ->where('ap_plantilla_items.cod_plantilla', $id)->get();
        $itemc = ApPlantillaItem::select('id_procedimiento', 'cantidad', 'honorario', 'orden', 'separado')
            ->where('cod_plantilla', $id)->count();
        return view('archivo_plano/plantillas/veritem', compact('item', 'fila', 'itemc'));
    }

    public function update(Request $request)
    {
        if ($request->ajax()) {

            $id_procedimiento = $request->id_procedimiento;
            $cantidad         = $request->cantidad;
            $honorario        = $request->honorario;
            $orden            = $request->orden;
            $separado         = $request->separado;

            $ip_cliente           = $_SERVER["REMOTE_ADDR"];
            $idusuario            = Auth::user()->id;
            $id                   = $request->chivo;
            $codigopla            = $request->txtcodigo;
            $pla                  = ApPlantilla::find($id);
            $pla->codigo          = $request->txtcodigo;
            $pla->descripcion     = $request->txtdescripcion;
            $pla->desc_comp       = $request->txtdescripcioncompleta;
            $pla->estado          = $request->txtestado;
            $pla->id_usuariocrea  = $idusuario;
            $pla->id_usuariomod   = $idusuario;
            $pla->ip_creacion     = $ip_cliente;
            $pla->ip_modificacion = $ip_cliente;
            $pla->save();

            $insert_data = null;
            $count = 0;


            DB::table('ap_plantilla_items')->where('cod_plantilla', $codigopla)->delete();

            
            
            //count($id_procedimiento)
              foreach ($request->codigo as $value) {
                  if (!is_null($value)) {
                      $porciones   = explode("|", $value);
                      $porciones_2 = explode(":", $porciones[0]);
                      $codigo_2    = str_replace(" ", "", $porciones_2[1]);
                      $tipo        = ApProcedimiento::where('codigo', $codigo_2)->first();

                      if (!is_null($tipo)) {
                          $data = array(

                              'cod_plantilla'    => $codigopla,
                              'id_procedimiento' => $codigo_2,
                              'cantidad'         => $cantidad[$count],
                              'honorario'        => $honorario[$count],
                              'orden'            => $orden[$count],
                              'separado'         => $separado[$count],
                              'tipo_item'        => $tipo->tipo,
                              'id_usuariocrea'   => $idusuario,
                              'id_usuariomod'    => $idusuario,
                              'ip_creacion'      => $ip_cliente,
                              'ip_modificacion'  => $ip_cliente,
                          );
                          $insert_data[] = $data;
                      }
                  }

                  $count++;
                  # code...
              }
          

            if (!is_null($insert_data)) {
                ApPlantillaItem::insert($insert_data);
            }

            return response()->json([
                'success' => 'Plantilla e Items Grabados.',
            ]);

        }
    }

}
