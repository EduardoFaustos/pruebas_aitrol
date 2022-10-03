<?php

namespace Sis_medico\Http\Controllers\servicios_generales;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Sis_medico\Mantenimientos_Oficinas;
use Sis_medico\Mantenimientos_Insumos_Limpieza;
use Sis_medico\Mantenimientos_Dotacion;
use Sis_medico\LimpiezaArea;
use Sis_medico\Insumos_area_nuevo;
use Sis_medico\ProductosUtilizadosArea;
use Sis_medico\Http\Controllers\limpieza_pentax\LimpiezaPentaxController;

class LimpiezaAreaController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 24, 4)) == false) {
            return true;
        }
    }

    public function created($id, $request)
    {

        $oficinas = Mantenimientos_Oficinas::where('id_unidad', $id)->get();
        $productos = Mantenimientos_Insumos_Limpieza::where('estado', 1)->get();
        $insumos = Mantenimientos_Dotacion::where('estado', 1)->get();

        return view('servicios_generales/limpieza_area/create', ['oficinas' => $oficinas, 'productos' => $productos, 'insumos' => $insumos]);
    }

    public function save(Request $request)
    {
        //dd($request->all());

        $subir_imagen = new LimpiezaPentaxController;
        $idusuario  = Auth::user()->id;
        $imagen = $subir_imagen->uploading($request['evidencia_antes'], $idusuario);
        try {
            $area = new LimpiezaArea;
            $area->id_oficina = $request['eleccion'];
            $area->tipo_desinfeccion = $request['tipo_desinfeccion'];
            $area->observaciones =   $request['observaciones'];
            $area->path_antes = $imagen['data'];
            $area->id_usuariocrea = $idusuario;
            $area->id_usuariomod = $idusuario;
            $area->save();
            foreach ($request['insumos'] as $val) {

                $sav = new ProductosUtilizadosArea;

                $sav->id_limpieza_area = $area->id;
                $sav->id_producto_uiti = $val;
                $sav->save();
            }
            foreach ($request['product_utilizados'] as $val) {
                $sav1 = new Insumos_area_nuevo;
                $sav1->id_limpieza_area = $area->id;
                $sav1->id_insumos = $val;
                $sav1->save();
            }
            return redirect()->route('index_limpieza_area');
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function index()
    {

        $limpieza = LimpiezaArea::where('estado', 1)->paginate(10);

        return view('servicios_generales/limpieza_area/index', ['limpieza' => $limpieza]);
    }
    public function edit(Request $request)
    {

        $id = $request['id'];
        $registro = LimpiezaArea::find($id);
        $oficinas = Mantenimientos_Oficinas::where('estado', 1)->get();
        $productos = Mantenimientos_Insumos_Limpieza::where('estado', 1)->get();
        $insumos = Mantenimientos_Dotacion::where('estado', 1)->get();



        return view('servicios_generales/limpieza_area/edit', ['registro' => $registro, 'oficinas' => $oficinas, 'productos' => $productos, 'insumos' => $insumos]);
    }

    public function edit_save(Request $request)
    {


        try {
            $subir_imagen = new LimpiezaPentaxController;
            $idusuario  = Auth::user()->id;
            $imagen = $subir_imagen->uploading($request['evidencia_despues'], $idusuario);
            $registro = LimpiezaArea::find($request['id']);
            $registro->path_despues =  $imagen['data'];
            $registro->save();
            return redirect()->route('index_limpieza_area');
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function buscador(Request $request)
    {
        $limpieza = LimpiezaArea::where('estado', 1)->whereBetween('created_at', [$request['desde'] . ' 00:00:00', $request['hasta'] . ' 23:59:59'])->paginate(10);
        return view('servicios_generales/limpieza_area/index', ['limpieza' => $limpieza]);
    }
}
