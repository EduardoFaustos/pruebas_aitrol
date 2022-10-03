<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Route;
use Sis_medico\Empresa;
use Sis_medico\InvDetMovimientos;
use Sis_medico\InvInventarioSerie;
use Sis_medico\Movimiento;
use Sis_medico\User;
use Sis_medico\UsuarioEmpresa;

class DashboardController extends Controller
{
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $rolUsuario = Auth::user()->id_tipo_usuario;

        $empresa_hos     = Empresa::where('prioridad', 2)->first();
        $usuario_empresa = UsuarioEmpresa::where('id_usuario', Auth::user()->id)->first();
        if (!is_null($usuario_empresa) && !is_null($empresa_hos)) {
            if ($usuario_empresa->id_empresa == $empresa_hos->id) {
                return redirect()->route("hospital.index");
            }
        }
        if (in_array($rolUsuario, array(23)) == true) {
            return redirect()->route("hospital.index");
        }
        if (in_array($rolUsuario, array(3)) == true) {
            return redirect()->route("nuevo.diseño");
        }

        $id_empresa = $request->session()->get('id_empresa');
        if (is_null($id_empresa)) {
            if ($rolUsuario == 10 || $rolUsuario == 12) {
                $request->session()->put('id_empresa', '0993075000001');
            } else {
                $request->session()->put('id_empresa', '0992704152001');
            }
        }

        if (in_array($rolUsuario, array(2)) == true) {
            return redirect()->route("paciente.historial_orden_lab_paciente");
        }

        /*
        $variable   = DB::table('vade_principio')->get();
        foreach ($variable as $value) {
        $existe = DB::table('principio_activo')->where('nombre', 'like', "'%" . $value->nombre . "%'")->get();
        if ($existe == '[]') {
        DB::table('principio_activo')->insert([
        'nombre'          => strtoupper($value->nombre),
        'descripcion'     => strtoupper($value->nombre),
        'estado'          => 1,
        'ip_creacion'     => $ip_cliente,
        'ip_modificacion' => $ip_cliente,
        'id_usuariocrea'  => $idusuario,
        'id_usuariomod'   => $idusuario,
        ]);
        }
        }
        dd('salio');*/

        /*$planes = Plan_Cuentas::all();
        dd($planes);*/
        $month    = date('m');
        $year     = date('Y');
        $fecha    = date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
        $fecha2   = date('m-d');
        $fecha3   = $fecha;
        $fecha3   = strtotime('+1 month', strtotime($fecha3));
        $variable = date('m', $fecha3);
        $fecha3   = date('m', $fecha3);
        $fecha    = date('m');

        $ch = DB::select("SELECT *
            FROM users
            WHERE
            id_tipo_usuario <> 2 AND
            estado = 1 AND
            fecha_nacimiento LIKE '%-" . $fecha2 . "'
            ORDER BY DAY(fecha_nacimiento) ASC");

        $ca = DB::select("SELECT *
            FROM users
            WHERE
            id_tipo_usuario <> 2 AND
            estado = 1 AND
            fecha_nacimiento LIKE '%-" . $fecha . "-%' AND
            fecha_nacimiento NOT LIKE '%" . $fecha2 . "'
            ORDER BY DAY(fecha_nacimiento) ASC");
        $pm = DB::select("SELECT *
            FROM users
            WHERE
            id_tipo_usuario <> 2 AND
            estado = 1 AND
            fecha_nacimiento LIKE '%-" . $fecha3 . "-%'
            ORDER BY DAY(fecha_nacimiento) ASC");
        $faltantes = DB::select("SELECT a.*, p.nombre1 as pnombre1, p.nombre2 as pnombre2, p.apellido1 as papellido1, p.apellido2 as papellido2, d.nombre1 as dnombre1, d.nombre2 as dnombre2, d.apellido1 as dapellido1, d.apellido2 as dapellido2
            FROM agenda a, paciente p, users d
            WHERE
            a.estado_cita Like '2' AND
            a.id_paciente = p.id AND
            a.id_doctor1 = d.id AND
            a.estado like '-1' ");
        $agregados = DB::select("SELECT a.*, u.nombre1 as pnombre1, u.nombre2 as pnombre2, u.apellido1 as papellido1, u.apellido2 as papellido2, d.nombre1 as dnombre1, d.nombre2 as dnombre2, d.apellido1 as dapellido1, d.apellido2 as dapellido2
            FROM agenda a, users u, users d
            WHERE
            a.estado_cita = 2 AND
            a.id_paciente = u.id AND
            a.id_doctor1 = d.id AND
            a.estado = 1");

        $stock = DB::select("SELECT * from producto p where p.cantidad < p.minimo");

        if ($rolUsuario == 1) {

        }

        $id_empresa = $request->session()->get('id_empresa');
        /*  C    M   L   */
        if(Auth::user()->id == "5599887744"){
            //dd($id_empresa);

        }
        $caducado = InvDetMovimientos::where(function ($query) {
            $query->where('fecha_vence', '<', 'now()')
                ->orWhereRaw('fecha_vence < DATE_ADD(NOW(), INTERVAL 3 MONTH)');
        })
        ->leftJoin('inv_inventario', 'inv_inventario.id', '=', 'inv_det_movimientos.id_inv_inventario')
        ->where('inv_inventario.id_empresa', $id_empresa)
        ->where('inv_inventario.existencia', '>=', 1)
        ->paginate(10);

        $inventario = DB::select("SELECT * from inv_inventario i join producto p on i.id_producto = p.id where i.existencia < i.existencia_min group by id_producto");
        // dd($inventario);

        $fecha_hoy = date('Y-m-d');
        //dd($fecha_cadu);

        //redireccionar para el paciente
        /*if ($rolUsuario == 2) {
        return redirect()->route('agenda.index');
        }*/
        
        

        return view('dashboard', [
            'ch'         => $ch, 'ca'                => $ca, 'pm'              => $pm, 'faltantes'        => $faltantes, 'agregados' => $agregados, 'stock' => $stock, 'fecha_hoy' => $fecha_hoy, 'caducado' => $caducado, 'inventario' => $inventario,
        ]);
    }
    public function modal_revisar($id)
    {
        $fecha = InvInventarioSerie::where('id', $id)->first();

        //dd($fecha);
        return view('modal_revisar', ['fecha' => $fecha]);
    }
    public function consulta(Request $request)
    {

        // dd($request->all());
        $msj = '';
        try {
            $variable1 = InvDetMovimientos::where('serie', $request['serie'])->update([
                'fecha_vence' => $request['fecha'],
            ]);
            $variable2 = InvInventarioSerie::where('serie', $request['serie'])->update([
                'fecha_vence' => $request['fecha'],
            ]);

            $variable3 = Movimiento::where('serie', $request['serie'])->update([
                'fecha_vencimiento' => $request['fecha'],
            ]);
            $variablem = Movimiento::where('id_producto', $request['id_producto'])->update([
                'fecha_vencimiento' => $request['fecha'],
            ]);
            $variablem1 = InvInventarioSerie::where('id_producto', $request['id_producto'])->update([
                'fecha_vence' => $request['fecha'],
            ]);
            $variablem2 = InvDetMovimientos::where('id_producto', $request['id_producto'])->update([
                'fecha_vence' => $request['fecha'],
            ]);

            $msj = 'ok';
        } catch (Exception $e) {
            return json_encode(strval($e));
        }

        return json_encode($msj);
    }
}
