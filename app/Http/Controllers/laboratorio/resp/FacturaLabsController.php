<?php
namespace Sis_medico\Http\Controllers\laboratorio;
use Sis_medico\Http\Controllers\Controller;
use Carbon\Carbon;
use Cookie;
use Excel;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mail;
use Sis_medico\Agenda;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Agenda_archivo;
use Sis_medico\Agenda_Permiso;
use Sis_medico\Archivo_historico;
use Sis_medico\ControlDocController;
use Sis_medico\Cortesia_paciente;
use Sis_medico\Empresa;
use Sis_medico\Especialidad;
use Sis_medico\Examen_Obligatorio;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Orden_Agenda;
use Sis_medico\Excepcion_Horario;
use Sis_medico\Hc4_Biopsias;
use Sis_medico\hc_child_pugh;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_protocolo;
use Sis_medico\hc_receta;
use Sis_medico\Historiaclinica;
use Sis_medico\Hospital;
use Sis_medico\Log_Agenda;
use Sis_medico\Max_Procedimiento;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Paciente_Biopsia;
use Sis_medico\Paciente_Doctor;
use Sis_medico\Paciente_Familia;
use Sis_medico\Pais;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\Pentax_log;
use Sis_medico\Procedimiento;
use Sis_medico\procedimiento_completo;
use Sis_medico\Sala;
use Sis_medico\Seguro;
use Sis_medico\TipoUsuario;
use Sis_medico\User;
use Sis_medico\Excepcion_Sala;
use Sis_medico\Horario_Sala;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Examen_Detalle_Forma_Pago;
use Sis_medico\Ct_Tipo_Tarjeta;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_productos;
use Sis_medico\Examen;
use Sis_medico\Http\Controllers\ApiFacturacionController;
use Sis_medico\Log_usuario;
use Sis_medico\Examen_Detalle;


date_default_timezone_set('America/Guayaquil');

class FacturaLabsController extends Controller
{
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

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
        if (in_array($rolUsuario, array(1, 4, 5, 11, 20)) == false) {
            return true;
        }
    }

    public function datos_factura($id){

        $orden    = Examen_Orden::find($id);
        $cliente  = Ct_Clientes::where('identificacion', $id)->where('estado', '1')->first();

        $recargo_valor =$orden->detalle_forma_pago->sum('p_fi');
        $valor_forma = $orden->detalle_forma_pago->sum('valor');
        $total_forma = $valor_forma+$recargo_valor;

        return view('laboratorio/facturalabs/datos_facturas', ['orden' => $orden, 'cliente' => $cliente, 'total_forma' =>$total_forma]);

    }

    public function humanlabs_enviar_sri($id){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $orden                = Examen_Orden::find($id);
        //$data['empresa']      = '0993075000001'; // humanlabs
        $data['empresa']      = '0992704152001';   //gastroclinica 
        $cliente['cedula']    = $orden->cedula_factura;
        $cliente['tipo']      = $orden->tipo_documento; //eduardo dice q el lo calcula 
        $cliente['nombre']    = $orden->nombre_factura;
        $cliente['apellido']  = '';
        $explode              = explode(" ",$orden->nombre_factura);
        if(count($explode)>=4){
            $cliente['nombre']    = $explode[0].' '.$explode[1];
            for ($i=2; $i < count($explode) ; $i++) { 
                $cliente['apellido']  = $cliente['apellido'].' '.$explode[$i];       
            }   
        }
        if(count($explode)==3){
            $cliente['nombre']    = $explode[0];   
            $cliente['apellido']  = $explode[1].' '.$explode[2]; 
        }
        if(count($explode)==2){
            $cliente['nombre']    = $explode[0];   
            $cliente['apellido']  = $explode[1]; 
        }
        //dd($cliente);
        
        $cliente['email']     = $orden->email_factura;
        $cliente['telefono']  = $orden->telefono_factura;
        $direccion['calle']   = $orden->direccion_factura;
        $direccion['ciudad']  = $orden->ciudad_factura;
        $cliente['direccion'] = $direccion;
        $data['cliente']      = $cliente;

        $msn_error='';
        $flag_error=false;
        if($cliente['cedula']==null){
            $flag_error=true;
            $msn_error='Error en cedula';
        }
        
        if($cliente['nombre']==null){
            $flag_error=true;
            $msn_error='Error en Nombre';
        }
        if($cliente['email']==null){
            $flag_error=true;
            $msn_error='Error en email';
        } 
        if($cliente['telefono']==null){
            $flag_error=true;
            $msn_error='Error en telefono';
        }
        if($direccion['calle']==null){
            $flag_error=true;
            $msn_error='Error en calle';
        }
        if($direccion['ciudad']==null){
            $flag_error=true;
            $msn_error='Error en Ciudad';
        }

        $cant = 0;
        foreach ($orden->detalles as $value) {
           //se envian los productos
            $producto['sku']       = "LABS-".$value->examen->id;//ID EXAMEN
            $producto['nombre']    = $value->examen->nombre;// NOMBRE DEL EXAMEN
            $producto['cantidad']  = "1";
            $producto['precio']    = $value->valor;//DETALLE
            $producto['descuento'] = $value->valor_descuento;
            $producto['subtotal']  = $value->valor - $value->valor_descuento;//precio-descuento
            $producto['tax']       = "0";
            $producto['total']     = $value->valor - $value->valor_descuento;//SUBTOTAL
            $producto['copago']    = "0"; 
            $productos[$cant] = $producto;
            $cant ++;
        }

        if($orden->recargo_valor>0){
            $producto['sku']       = "LABS-FEE";
            $producto['nombre']    = 'FEE-ADMINISTRATIVO';
            $producto['cantidad']  = "1";
            $producto['precio']    = $orden->recargo_valor;//DETALLE
            $producto['descuento'] = '0';
            $producto['subtotal']  = $orden->recargo_valor;//precio-descuento
            $producto['tax']       = "0";
            $producto['total']     = $orden->recargo_valor;//SUBTOTAL
            $productos[$cant] = $producto;
            $cant ++;
        }

        $data['productos'] = $productos;
         /*01  SIN UTILIZACION DEL SISTEMA FINANCIERO
        15  COMPENSACIÓN DE DEUDAS
        16  TARJETA DE DÉBITO
        17  DINERO ELECTRÓNICO
        18  TARJETA PREPAGO
        19  TARJETA DE CRÉDITO
        20  OTROS CON UTILIZACION DEL SISTEMA FINANCIERO
        21  ENDOSO DE TÍTULOS
        */
        $info_adicional['nombre']      = "AGENTES_RETENCION";
        $info_adicional['valor']       = "Resolucion 1";
        $info[0]                       = $info_adicional;

        $info_adicional['nombre']      = "PACIENTE";
        $info_adicional['valor']       = $orden->id_paciente.' '.$orden->paciente->apellido1.' '.$orden->paciente->nombre1;
        $info[1]                       = $info_adicional; 

        $info_adicional['nombre']      = "MAIL";
        $info_adicional['valor']       = $orden->email_factura;//EMAIL
        $info[2]                       = $info_adicional;

        $info_adicional['nombre']      = "CIUDAD";
        $info_adicional['valor']       = $orden->ciudad_factura;//EMAIL
        $info[3]                       = $info_adicional;

        $info_adicional['nombre']      = "DIRECCION";
        $info_adicional['valor']       = $orden->direccion_factura;//EMAIL
        $info[4]                       = $info_adicional;

        $info_adicional['nombre']      = "ORDEN";
        $info_adicional['valor']       = ''.$orden->id.'';//EMAIL
        $info[5]                       = $info_adicional;

        $info_adicional['nombre']      = "SEGURO";
        $info_adicional['valor']       = $orden->seguro->nombre;//SEGURO
        $info[6]                       = $info_adicional;



        
        $cuenta_forma = $orden->detalle_forma_pago->count();
        //dd($cuenta_forma);
        if($cuenta_forma > 1) {
            $pago['forma_pago']     = '20';
            $info_adicional['nombre']      = "FORMA_PAGO";
            $texto = '';

            foreach($orden->detalle_forma_pago as $fp){
                $total =$fp->valor + $fp->p_fi;
                $total =round($total,2);
                $texto      = $texto.' '.$fp->tipo_pago->nombre.': '.$total;
            }
            $info_adicional['valor'] = $texto;
            $info[7]  = $info_adicional;
        }else{
            $forma_pago = $orden->detalle_forma_pago->first();
            $tipo = $forma_pago->id_tipo_pago;
            if($tipo=='1'){
                $pago['forma_pago'] = '01';    
            }elseif($tipo=='2'){
                $pago['forma_pago'] = '20';     
            }elseif($tipo=='3'){
                $pago['forma_pago'] = '20'; 
            }elseif($tipo=='4'){
                $pago['forma_pago'] = '19'; 
            }elseif($tipo=='5'){
                $pago['forma_pago'] = '20'; 
            }else{
                $pago['forma_pago'] = '16';
            }

        }
        $pago['informacion_adicional'] = $info;
        $pago['dias_plazo']            = '10';
        $data['pago']                  = $pago;
        $data['contable']              = 1; //si se marca 1 crea factura, 0 no crea factura
        $data['laboratorio']           = 1;
        $data['paciente']              = $orden->id_paciente;
        $data['concepto']              = 'Factura Electronica -'.$orden->paciente->apellido1.' '.$orden->paciente->nombre1;
        $data['copago']                = 0;
        $data['id_seguro']             = '1'; //si se marca 1 se crea un solo producto por id de laboratorioo, 0 graba detalle de datos
        $data['total_factura']         = $orden->total_valor;

        $fp_cant = 0;
        foreach($orden->detalle_forma_pago as $fp){
            
            $tipos_pago['id_tipo']            = $fp->id_tipo_pago; //metodo de pago efectivo, tarjeta, etc
            $tipos_pago['fecha']              = substr($orden->fecha_orden,0,10);
            $tipos_pago['tipo_tarjeta']       = $fp->tipo_tarjeta; //si es efectivo no se envia
            $tipos_pago['numero_transaccion'] = $fp->numero; //si es efectivo no se envia
            $tipos_pago['id_banco']           = $fp->banco; //si es efectivo no se envia
            $tipos_pago['cuenta']             = $fp->cuenta; //si es efectivo no se envia
            $tipos_pago['giradoa']            = null; //si es efectivo no se envia
            $tipos_pago['valor']              = $fp->valor + $fp->p_fi; //valor a pagar de total
            $tipos_pago['valor_base']         = $fp->valor + $fp->p_fi; //valor a pagar de base

            $pagos[$fp_cant]                  = $tipos_pago;
            $fp_cant++;
        }

            
        $data['formas_pago']              = $pagos;

        if($orden->fecha_envio!=null){
            $flag_error=true;
            $msn_error='Ya enviado al SRI';
        }

        if($flag_error){
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "LABORATORIO",
                'dato_ant1'   => $orden->id,
                'dato1'       => $orden->id_paciente,
                'dato_ant2'   => "ERROR AL ENVIAR AL SRI",
                'dato_ant4'   => $msn_error,
            ]); 
            return "error";   
        }

        $orden->update([
            'fecha_envio' => date('Y-m-d H:i:s'),  
        ]);

        $envio = ApiFacturacionController::envio_factura($data);
        //dd($envio);

        $orden->update([
            'comprobante' => $envio->comprobante,
            'fecha_envio' => date('Y-m-d H:i:s'),  
        ]);

        $manage = $envio->status->status.'-'.$envio->status->message.'-'.$envio->status->reason;

        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "LABORATORIO",
            'dato_ant1'   => $orden->id,
            'dato1'       => $orden->id_paciente,
            'dato_ant2'   => "ENVIAR AL SRI",
            'dato_ant4'   => $manage,
        ]);

        return "ok";
        //dd($envio->comprobante);
    }

    public function pagoenlinea_factura(){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        
        $año = '2020'; $mes = '12';
        $ordenes    = Examen_Orden::where('estado','1')->where('estado_pago','1')->where('pago_online','1')->whereNull('comprobante')->whereNull('fecha_envio')->where('anio',$año)->where('mes',$mes)->get();
        //dd($ordenes);
        foreach ($ordenes as $orden) {

            $pagoonline = DB::table('pagosenlinea')->where('clave',$orden->id)->first();
            if(!is_null($pagoonline)){

                $data['empresa']      = '0993075000001';
                $data['fecha']        = date('Y-m-d',strtotime($pagoonline->fecha));
                //dd($data);
                $cliente['cedula']    = $orden->cedula_factura;
                //ruc 4 - cedula 5 - pass 6
                $cliente['tipo']     = '6'; //eduardo dice q el lo calcula y luego se
                if(strlen($orden->cedula_factura)==13 && substr($orden->cedula_factura,-3)=='001'){
                    $cliente['tipo'] = '4';    
                }elseif(strlen($orden->cedula_factura)==10){
                    $cliente['tipo'] = '5';
                }
                
                $cliente['nombre']    = $orden->nombre_factura;
                $cliente['apellido']  = '';    
            
                //dd($orden);
                    
                $explode              = explode(" ",$orden->nombre_factura);
                if(count($explode)>=4){
                    $cliente['nombre']    = $explode[0].' '.$explode[1];
                    for ($i=2; $i < count($explode) ; $i++) { 
                        $cliente['apellido']  = $cliente['apellido'].' '.$explode[$i];       
                    }   
                }
                if(count($explode)==3){
                    $cliente['nombre']    = $explode[0];   
                    $cliente['apellido']  = $explode[1].' '.$explode[2]; 
                }
                if(count($explode)==2){
                    $cliente['nombre']    = $explode[0];   
                    $cliente['apellido']  = $explode[1]; 
                }
                //dd($cliente);
                $cliente['email']     = $orden->email_factura;
                $cliente['telefono']  = $orden->telefono_factura;
                $direccion['calle']   = $orden->direccion_factura;
                $direccion['ciudad']  = $orden->ciudad_factura;
                $cliente['direccion'] = $direccion;
                $cliente['nro_autorizacion'] = null;
                $data['cliente']      = $cliente;
                

                $msn_error='';
                $flag_error=false;
                if($cliente['cedula']==null){
                    $flag_error=true;
                    $msn_error='Error en cedula';
                }
                
                if($cliente['nombre']==null){
                    $flag_error=true;
                    $msn_error='Error en Nombre';
                }
                if($cliente['email']==null){
                    $flag_error=true;
                    $msn_error='Error en email';
                } 
                if($cliente['telefono']==null){
                    $flag_error=true;
                    $msn_error='Error en telefono';
                }
                if($direccion['calle']==null){
                    $flag_error=true;
                    $msn_error='Error en calle';
                }
                if($direccion['ciudad']==null){
                    $flag_error=true;
                    $msn_error='Error en Ciudad';
                }
                //dd($orden->detalles);

                $cant = 0;
                foreach ($orden->detalles as $value) {
                   //se envian los productos
                    $producto['sku']       = "LABS-".$value->examen->id;//ID EXAMEN
                    $producto['nombre']    = $value->examen->nombre;// NOMBRE DEL EXAMEN
                    $producto['cantidad']  = "1";
                    $producto['precio']    = $value->valor;//DETALLE
                    $producto['descuento'] = $value->valor_descuento;
                    $producto['subtotal']  = $value->valor - $value->valor_descuento;//precio-descuento
                    $producto['tax']       = "0";
                    $producto['total']     = $value->valor - $value->valor_descuento;//SUBTOTAL
                    $producto['copago']    = "0"; 
                    $productos[$cant] = $producto;
                    $cant ++;
                }

                $data['productos'] = $productos;
                 /*01  SIN UTILIZACION DEL SISTEMA FINANCIERO
                15  COMPENSACIÓN DE DEUDAS
                16  TARJETA DE DÉBITO
                17  DINERO ELECTRÓNICO
                18  TARJETA PREPAGO
                19  TARJETA DE CRÉDITO
                20  OTROS CON UTILIZACION DEL SISTEMA FINANCIERO
                21  ENDOSO DE TÍTULOS
                */
                $info_adicional['nombre']      = "AGENTES_RETENCION";
                $info_adicional['valor']       = "Resolucion 1";
                $info[0]                       = $info_adicional;

                $info_adicional['nombre']      = "PACIENTE";
                $info_adicional['valor']       = $orden->id_paciente.' '.$orden->paciente->apellido1.' '.$orden->paciente->nombre1;
                $info[1]                       = $info_adicional; 

                $info_adicional['nombre']      = "MAIL";
                $info_adicional['valor']       = $orden->email_factura;//EMAIL
                $info[2]                       = $info_adicional;

                $info_adicional['nombre']      = "CIUDAD";
                $info_adicional['valor']       = $orden->ciudad_factura;//EMAIL
                $info[3]                       = $info_adicional;

                $info_adicional['nombre']      = "DIRECCION";
                $info_adicional['valor']       = $orden->direccion_factura;//EMAIL
                $info[4]                       = $info_adicional;

                $info_adicional['nombre']      = "ORDEN";
                $info_adicional['valor']       = ''.$orden->id.'';//EMAIL
                $info[5]                       = $info_adicional;

                $info_adicional['nombre']      = "SEGURO";
                $info_adicional['valor']       = $orden->seguro->nombre;//SEGURO
                $info[6]                       = $info_adicional;

                $info_adicional['nombre']      = "FORMA_PAGO";
                $info_adicional['valor']       = '';
                $info[7]                       = $info_adicional;

                $pago['forma_pago']            = '01';
                $pago['informacion_adicional'] = $info;
                $pago['dias_plazo']            = '10';
                $data['pago']                  = $pago;
                $data['contable']              = 1; //si se marca 1 crea factura, 0 no crea factura
                $data['laboratorio']           = 1;
                $data['paciente']              = $orden->id_paciente;
                $data['concepto']              = 'Ingreso de Factura Electronica por Pago en Linea';
                $data['copago']                = 0;
                $data['id_seguro']             = '1'; //si se marca 1 se crea un solo producto por id de laboratorioo, 0 graba detalle de datos
                $data['total_factura']         = $orden->total_valor;

                $tipos_pago['id_tipo']            = 5; //metodo de pago efectivo, tarjeta, etc
                $tipos_pago['fecha']              = substr($orden->fecha_orden,0,10);
                $tipos_pago['tipo_tarjeta']       = null; //si es efectivo no se envia
                $tipos_pago['numero_transaccion'] = $pagoonline->pago_auth; //si es efectivo no se envia
                $banco = Ct_Bancos::where('nombre',$pagoonline->issuer)->first();
                $id_banco = '1';
                if(!is_null($banco)){
                    $id_banco = $banco->id;
                }
                $tipos_pago['id_banco']           = $id_banco; //si es efectivo no se envia
                $tipos_pago['cuenta']             = $pagoonline->credittype.'-'.$pagoonline->paymentmethod; //si es efectivo no se envia
                $tipos_pago['giradoa']            = null; //si es efectivo no se envia
                $tipos_pago['valor']              = $orden->total_valor; //valor a pagar de total
                $tipos_pago['valor_base']         = $orden->total_valor; //valor a pagar de base
                $pagos['tipos_pago']              = $tipos_pago;
                $data['formas_pago']              = $pagos;

                if($orden->fecha_envio!=null){
                    $flag_error=true;
                    $msn_error='Ya enviado al SRI';
                }

                if($flag_error){
                    Log_usuario::create([
                        'id_usuario'  => $idusuario,
                        'ip_usuario'  => $ip_cliente,
                        'descripcion' => "LABORATORIO",
                        'dato_ant1'   => $orden->id,
                        'dato1'       => $orden->id_paciente,
                        'dato_ant2'   => "ERROR FACTURA PAGO EN LINEA",
                        'dato_ant4'   => $msn_error,
                    ]); 
                    return "error";   
                }
                //dd($data);
               
                $orden->update([
                    'fecha_envio' => date('Y-m-d H:i:s'),  
                ]);

                $envio = ApiFacturacionController::crea_factura_noelec($data, $pagoonline->nro_comprobante);

                $orden->update([
                    'comprobante' => $pagoonline->nro_comprobante,
                    'fecha_envio' => date('Y-m-d H:i:s'),  
                ]);

                Log_usuario::create([
                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "LABORATORIO",
                    'dato_ant1'   => $orden->id,
                    'dato1'       => $orden->id_paciente,
                    'dato_ant2'   => "FACTURA PAGO EN LINEA",
                    'dato_ant4'   => ' ',
                ]);
            }

    
                
        }    

        return "ok";
    }

    public function cuadrar($id){

        $orden    = Examen_Orden::find($id);
        $recargo_valor =$orden->detalle_forma_pago->sum('p_fi');
        $valor_forma = $orden->detalle_forma_pago->sum('valor');
        $total_forma = $valor_forma+$recargo_valor;
        //dd($orden->total_valor,$total_forma);
        if (round($orden->total_valor,2) == round($total_forma,2)) {
           return 'ok';
        }
        else{
            return 'no';
        }

    }


    public function forma_pago($id_orden){
        $orden =Examen_Orden::find($id_orden);
        $pagos = Ct_Tipo_Pago::where('estado','1')->get();
        $forma_pago = Examen_Detalle_Forma_Pago::where('id_examen_orden',$id_orden)->where('estado','1')->get();

        return view('laboratorio/orden/forma_pago',['pagos' =>$pagos, 'id_orden' =>$id_orden, 'forma_pago' =>$forma_pago, 'orden' =>$orden]);

    }

    public function forma_pago_ajax($id_orden){
        $orden =Examen_Orden::find($id_orden);
        $pagos = Ct_Tipo_Pago::where('estado','1')->get();
        $forma_pago = Examen_Detalle_Forma_Pago::where('id_examen_orden',$id_orden)->where('estado','1')->get();

        return view('laboratorio/orden/forma_pago_ajax',['pagos' =>$pagos, 'id_orden' =>$id_orden, 'forma_pago' =>$forma_pago, 'orden' =>$orden]);

    }

    public function guardar_forma(Request $request){
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $fecha = date('Y-m-d');
        $id_examen_orden = $request['id_orden'];
        //FALTA CUENTA, P_FI
        $fi = 0;
        $fi_valor =0;
            if ($request['id_pago'] == '4') {
                $fi= 0.07;

            }
            if ($request['id_pago'] == '6') {
                $fi= 0.045;
            }
            $fi_valor = round($request['valor']*$fi,2);
            $arr=[
                'id_examen_orden'=> $id_examen_orden,
                'id_tipo_pago' => $request['id_pago'],
                'banco' => $request['id_banco'],
                'tipo_tarjeta' =>$request['id_tipo_tarjeta'],
                'numero' =>$request['transaccion'],
                'valor' =>$request['valor'],
                'posee_fi' =>$fi,
                'p_fi' =>$fi_valor,
                'fecha' => $fecha,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];

            Examen_Detalle_Forma_Pago::create($arr);

        $orden = Examen_Orden::find($id_examen_orden);
        $recargo_valor =$orden->detalle_forma_pago->sum('p_fi');
        $total = $orden->valor+$recargo_valor;
        $total = $orden->valor+$recargo_valor-$orden->descuento_valor;

        $arr2 =[
            'recargo_valor' => $recargo_valor,
            'total_valor' => $total,
        ];
        $orden->update($arr2);

        return ['estado' => 'ok','id_orden' => $request['id_orden']];


    }

    public function revisa_forma($id_orden){

        $forma_pago = Examen_Detalle_Forma_Pago::where('id_examen_orden',$id_orden)->where('estado','1')->get();

        return "ok";

    }

    public function eliminar_forma($id_orden, $id_forma){
        //dd($id_orden, $id_forma);
        $forma_pago = Examen_Detalle_Forma_Pago::where('id',$id_forma)->delete();
        $orden = Examen_Orden::find($id_orden);
        $recargo_valor =$orden->detalle_forma_pago->sum('p_fi');
        $total = $orden->valor+$recargo_valor-$orden->descuento_valor;

        $arr2 =[
            'recargo_valor' => $recargo_valor,
            'total_valor' => $total,
        ];
        $orden->update($arr2);
        //return "ok";
        return ['estado' => 'ok','id_or' => $id_orden];
    }



    public function datos_forma($id_orden){
        $pagos = Ct_Tipo_Pago::where('estado','1')->get();
        $tarjetas = Ct_Tipo_Tarjeta::where('estado','1')->get();
        $bancos =Ct_Bancos::where('estado','1')->get();
        return view('laboratorio/orden/datos_forma',['pagos' =>$pagos, 'id_orden' => $id_orden, 'tarjetas' =>$tarjetas, 'bancos' =>$bancos]);

    }

    public function guardar_info_factura(Request $request){
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $orden = Examen_Orden::find($request->id_orden);

        $arr_orden = [
            'tipo_documento'    => $request['documento'],
            'cedula_factura'    => $request['cedula_factura'],
            'nombre_factura'    => $request['nombre_factura'],
            'direccion_factura' => $request['direccion_factura'],
            'ciudad_factura'    => $request['ciudad_factura'],
            'email_factura'     => $request['email_factura'],
            'telefono_factura'  => $request['telefono_factura'],
        ];

        $input_cli_crea = [
            'identificacion'          => $request['cedula_factura'],
            'nombre'                  => $request['nombre_factura'],
            'ciudad_representante'    => $request['ciudad_factura'],
            'direccion_representante' => $request['direccion_factura'],
            'telefono1_representante' => $request['telefono_factura'],
            'email_representante'     => $request['email_factura'],
            'tipo'                    => $request['documento'],
            'clase'                   => '1',
            'cedula_representante'    => $request['cedula_factura'],
            'estado'                  => '1',
            'id_usuariocrea'          => $idusuario,
            'id_usuariomod'           => $idusuario,
            'ip_creacion'             => $ip_cliente,
            'ip_modificacion'         => $ip_cliente,
            'nombre_representante'    => $request['nombre_factura'],
            'pais'                    => 'Ecuador',

        ];

        $input_cli_mod = [
            'identificacion'          => $request['cedula_factura'],
            'nombre'                  => $request['nombre_factura'],
            'ciudad_representante'    => $request['ciudad_factura'],
            'direccion_representante' => $request['direccion_factura'],
            'telefono1_representante' => $request['telefono_factura'],
            'email_representante'     => $request['email_factura'],
            'tipo'                    => $request['documento'],
            'clase'                   => '1',
            'cedula_representante'    => $request['cedula_factura'],
            'estado'                  => '1',
            'id_usuariomod'           => $idusuario,
            'ip_modificacion'         => $ip_cliente,
            'nombre_representante'    => $request['nombre_factura'],
            'pais'                    => 'Ecuador',

        ];

        $cliente = Ct_Clientes::where('identificacion', $request['cedula_factura'])->where('estado', '1')->first();

        if (!is_null($cliente)) {
            Ct_Clientes::where('identificacion', $request['cedula_factura'])->update($input_cli_mod);
        } else {
            Ct_Clientes::create($input_cli_crea);
        }

        $orden->update($arr_orden);

        return ['estado' => 'ok','id_orden' => $request['id_orden']];

    }

    public function crear_producto_labs($id_examen,$id_empresa,$cta_gastos,$cta_ventas,$cta_costos,$cta_devolucion,$impuesto_iva_compras,$impuesto_iva_ventas,$impuesto_servicio,$impuesto_ice){
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $modulo   = 'LABS-';
        $clave    = $modulo.$id_examen;

        $nombre   = '';
        $examen   = Examen::find($id_examen);
        if(!is_null($examen)){
            $nombre = $examen->nombre;
        }

        $producto = Ct_productos::where('codigo',$clave)->first();
        if(is_null($producto)){//Create
            $p_id = Ct_productos::insertGetId([
                'codigo'                     => $clave,
                'nombre'                     => $nombre,
                'codigo_barra'               => null,
                'descripcion'                => $nombre,
                'id_empresa'                 => $id_empresa,
                'clase'                      => null,
                'grupo'                      => '3',
                'proveedor'                  => null,
                'cta_gastos'                 => $cta_gastos,
                'cta_ventas'                 => $cta_ventas,
                'cta_costos'                 => $cta_costos,
                'cta_devolucion'             => $cta_devolucion,
                'reg_serie'                  => null,
                'mod_precio'                 => 0,
                'mod_desc'                   => 1,
                'iva'                        => 0,
                'promedio'                   => 0,
                'reposicion'                 => 0,
                'lista'                      => 0,
                'ultima_compra'              => 0,
                'descuento'                  => 0,
                'financiero'                 => 0,
                'marca'                      => 'LABS',
                'modelo'                     => 'LABS',
                'stock_minimo'               => 0,
                'fecha_expiracion'           => null,
                'impuesto_iva_compras'       => $impuesto_iva_compras,
                'impuesto_iva_ventas'        => $impuesto_iva_ventas,
                'impuesto_servicio'          => $impuesto_servicio,
                'impuesto_ice'               => $impuesto_ice,
                'clasificacion_impuesto_ice' => null,
                'ip_creacion'                => $ip_cliente,
                'ip_modificacion'            => $ip_cliente,
                'id_usuariocrea'             => $idusuario,
                'id_usuariomod'              => $idusuario,
            ]);

        }

    }

    public function crear_producto_labs_masivo(){

        
        $id_empresa            = '0993075000001'; 
        $cta_gastos            = null;
        $cta_ventas            = null;
        $cta_costos            = null; 
        $cta_devolucion        = null;
        $impuesto_iva_compras  = null; 
        $impuesto_iva_ventas   = null;
        $impuesto_servicio     = null;
        $impuesto_ice          = null;

        $examenes = Examen::all();
        foreach ($examenes as $value) {
            $id_examen         = $value->id;
            $this->crear_producto_labs($id_examen,$id_empresa,$cta_gastos,$cta_ventas,$cta_costos,$cta_devolucion,$impuesto_iva_compras,$impuesto_iva_ventas,$impuesto_servicio,$impuesto_ice);    
        }
            

    }

    public function modal_factura_agrupada(){
        
        $agrup2 = session()->get('a_orden');
        $cuenta = count($agrup2);
        //dd($cuenta);
       
        return view('laboratorio/orden/modal_factura_agrupada',['agrup2' =>$agrup2, 'cuenta' =>$cuenta]);

    }

    

    public function añadir_factura($id_orden, Request $request){
        $agrup2 = session()->get('a_orden');
        $orden = Examen_Orden::find($id_orden);

        if ($agrup2 == null) {
            $agrup2[0] = $id_orden;

        }else{
            $orden1 = Examen_Orden::find($agrup2[0]);
            
            if($orden1->id_seguro==$orden->id_seguro && $orden1->id_nivel==$orden->id_nivel){
                if(!in_array($id_orden, $agrup2)){

                    $i = count($agrup2);
                    $agrup2[$i] = $id_orden;
                }
            }else{
                return "no";
            }    
        }
        

        $request->session()->put('a_orden', $agrup2);

        return "ok";


    }

    public function añadir_factura_contabilidad($id_orden, Request $request){
        $rol = Auth::user()->id_tipo_usuario;

        $agrup2 = session()->get('a_orden');
        $orden = Examen_Orden::find($id_orden);

        if ($agrup2 == null) {
            $agrup2[0] = $id_orden;

        }else{
            $orden1 = Examen_Orden::find($agrup2[0]);
            if(!in_array($id_orden, $agrup2)){

                $i = count($agrup2);
                $agrup2[$i] = $id_orden;
            }
                
                    
        }
        

        $request->session()->put('a_orden', $agrup2);

        return "ok";


    }

    public function eliminar_orden_sesion($id_orden, Request $request){
        

        $agrup2 = session()->get('a_orden');
        $request->session()->forget('a_orden');
        $i=0;
        foreach ($agrup2 as $value) {
            if($value!=$id_orden){
                $agrup[$i] = $value;
                $i++;
            }
        }
        $request->session()->put('a_orden', $agrup);

        return $agrup;


    }

    public function leeañadir_factura($id_orden, Request $request){

        //$orden= Examen_Orden::find($id_orden);
        $agrup2 = session()->get('a_orden');
        dd($agrup2);
        return $agrup2;

    }

    public function eliminar_sesion(Request $request){

        $request->session()->forget('a_orden');
        
        return "ok";

    }

    public function guardar_agrupada(Request $request){
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $agrup2 = session()->get('a_orden');
        $fecha = date('Y-m-d');
        //dd($request->cedula_factura);
        //crea orden falsa
        $orden_id = Examen_Orden::insertGetId([
            'id_paciente'                =>'LabsAgrup', 
            'estado'                     => '-2',
            'anio'                       => substr(date('Y-m-d'), 0, 4),
            'mes'                        => substr(date('Y-m-d'), 5, 2),
            'fecha_orden'                => date('Y-m-d'),
            'ip_creacion'                => $ip_cliente,
            'ip_modificacion'            => $ip_cliente,
            'id_usuariocrea'             => $idusuario,
            'id_usuariomod'              => $idusuario,
        ]);

        foreach ($agrup2 as $value) {
            $orden= Examen_Orden::find($value);

            $arr2 = [
                'orden_agrupado'        => $orden_id,
            ];
            $orden->update($arr2);

            foreach ($orden->detalles as $detalle) {
                $orden_falsa = Examen_orden::find($orden_id);
                $detalle_falsa = $orden_falsa->detalles->where('id_examen', $detalle->id_examen)->first();
                //dd($detalle_falsa);
                if (is_null($detalle_falsa)) {
                    $arr_det = [
                        'id_examen_orden'   => $orden_id,
                        'id_examen'         => $detalle->id_examen,
                        'valor'             => $detalle->valor,
                        'p_descuento'       => $detalle->p_descuento,
                        'valor_descuento'   => $detalle->valor_descuento,
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,
                        'id_usuariocrea'    => $idusuario,
                        'id_usuariomod'     => $idusuario,
                    ];
                    Examen_Detalle::create($arr_det);    
                }else{
                    $arr_det_act = [
                        'cantidad'          => $detalle_falsa->cantidad + 1,
                        'valor_descuento'   => $detalle->valor_descuento + $detalle_falsa->valor_descuento,
                    ];
                    //dd($detalle_falsa);
                    $detalle_falsa->update($arr_det_act);
                }
            }
        } 

        $recargo = 0;
        $valor_descuento = 0;
        foreach ($agrup2 as $orden_ori) {
            $orden= Examen_Orden::find($orden_ori);
            $recargo = $recargo+$orden->recargo_valor;
            $valor_descuento = $valor_descuento+$orden->descuento_valor;
        }

        $orden_f = Examen_Orden::find($orden_id);
        $valor = 0;
        $cantidad = 0;
        foreach ($orden_f->detalles as $det_falsa) {
            
            $valor = $valor+($det_falsa->cantidad * $det_falsa->valor);
            $total = $valor-$valor_descuento+$recargo;
            $cantidad = $cantidad+$det_falsa->cantidad;
            
            $arr_cab= [
                'cantidad'          => $cantidad,
                'valor'             => $valor,
                'descuento_p'       => $det_falsa->p_descuento,
                'descuento_valor'   => $valor_descuento,
                'total_valor'       => $total,
                'recargo_valor'     => $recargo,
            ];
        }
        $orden_f->update($arr_cab);

        //datos factura
        $input_cli_crea = [
            'identificacion'          => $request['cedula_factura'],
            'nombre'                  => $request['nombre_factura'],
            'ciudad_representante'    => $request['ciudad_factura'],
            'direccion_representante' => $request['direccion_factura'],
            'telefono1_representante' => $request['telefono_factura'],
            'email_representante'     => $request['email_factura'],
            'tipo'                    => $request['documento'],
            'clase'                   => '1',
            'cedula_representante'    => $request['cedula_factura'],
            'estado'                  => '1',
            'id_usuariocrea'          => $idusuario,
            'id_usuariomod'           => $idusuario,
            'ip_creacion'             => $ip_cliente,
            'ip_modificacion'         => $ip_cliente,
            'nombre_representante'    => $request['nombre_factura'],
            'pais'                    => 'Ecuador',

        ];

        $input_cli_mod = [
            'identificacion'          => $request['cedula_factura'],
            'nombre'                  => $request['nombre_factura'],
            'ciudad_representante'    => $request['ciudad_factura'],
            'direccion_representante' => $request['direccion_factura'],
            'telefono1_representante' => $request['telefono_factura'],
            'email_representante'     => $request['email_factura'],
            'tipo'                    => $request['documento'],
            'clase'                   => '1',
            'cedula_representante'    => $request['cedula_factura'],
            'estado'                  => '1',
            'id_usuariomod'           => $idusuario,
            'ip_modificacion'         => $ip_cliente,
            'nombre_representante'    => $request['nombre_factura'],
            'pais'                    => 'Ecuador',

        ];

        $arr_orden = [
            'tipo_documento'    => $request['documento'],
            'cedula_factura'    => $request['cedula_factura'],
            'nombre_factura'    => $request['nombre_factura'],
            'direccion_factura' => $request['direccion_factura'],
            'ciudad_factura'    => $request['ciudad_factura'],
            'email_factura'     => $request['email_factura'],
            'telefono_factura'  => $request['telefono_factura'],
        ];

        foreach ($agrup2 as $ordenes) {
            $ord_ori = Examen_Orden::find($ordenes);
            $ord_ori->update($arr_orden);
        }

        $cliente = Ct_Clientes::where('identificacion', $request['cedula_factura'])->where('estado', '1')->first();

        if (!is_null($cliente)) {
            Ct_Clientes::where('identificacion', $request['cedula_factura'])->update($input_cli_mod);
        } else {
            Ct_Clientes::create($input_cli_crea);
        }

        $ord_falsa = Examen_Orden::find($orden_id);
        $ord_falsa->update($arr_orden);

        $agrupada_sri = $this->agrupada_sri($orden_id);

        return "ok";

    }
    
    public function guardar_agrupada_contabilidad(Request $request){
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $agrup2 = session()->get('a_orden');
        $fecha = date('Y-m-d');
        //dd($request->cedula_factura);
        //crea orden falsa
        $orden_id = Examen_Orden::insertGetId([
            'id_paciente'                =>'LabsAgrup', 
            'estado'                     => '-3',
            'anio'                       => substr(date('Y-m-d'), 0, 4),
            'mes'                        => substr(date('Y-m-d'), 5, 2),
            'fecha_orden'                => date('Y-m-d'),
            'ip_creacion'                => $ip_cliente,
            'ip_modificacion'            => $ip_cliente,
            'id_usuariocrea'             => $idusuario,
            'id_usuariomod'              => $idusuario,
        ]);

        $acum_publico = 0;$acum_privado = 0;
        foreach ($agrup2 as $value) {
            $orden= Examen_Orden::find($value);
            $orden_falsa = Examen_Orden::find($orden_id);

            $arr2 = [
                'orden_agrupado'        => $orden_id,
            ];
            $orden->update($arr2);

            
            if($orden->seguro->tipo=='0'){
                $valor = $orden->total_nivel2 * 100;
                $valor = round($valor);
                $valor = $valor/100;

                $acum_publico += $valor;
            }else{
                $valor2 = $orden->total_valor * 100;
                $valor2 = round($valor2);
                $valor2 = $valor2/100;
                $acum_privado += $valor2;
            }


            /*if($orden->seguro->tipo=='0'){
                $det_pub = $orden_falsa->detalles->where('id_examen','1216')->first();
                if(is_null($det_pub)){
                    $arr_det = [
                        'id_examen_orden'   => $orden_id,
                        'id_examen'         => '1216',
                        'valor'             => round($orden->total_nivel2,2,PHP_ROUND_HALF_DOWN),
                        'valor_descuento'   => round($orden->descuento_valor,2,PHP_ROUND_HALF_DOWN),
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,
                        'id_usuariocrea'    => $idusuario,
                        'id_usuariomod'     => $idusuario,
                    ];
                    Examen_Detalle::create($arr_det); 
                }else{
                    $arr_det_act = [
                        'valor'             => round($orden->total_nivel2,2,PHP_ROUND_HALF_DOWN) + $det_pub->valor,
                        'valor_descuento'   => $orden->descuento_valor + $det_pub->valor_descuento,
                    ];
                    //dd($detalle_falsa);
                    $det_pub->update($arr_det_act);   
                }
            }else{
                $det_priv = $orden_falsa->detalles->where('id_examen','1217')->first();
                if(is_null($det_priv)){
                    $arr_det = [
                        'id_examen_orden'   => $orden_id,
                        'id_examen'         => '1217',
                        'valor'             => $orden->valor,
                        'valor_descuento'   => $orden->descuento_valor,
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,
                        'id_usuariocrea'    => $idusuario,
                        'id_usuariomod'     => $idusuario,
                    ];
                    Examen_Detalle::create($arr_det); 
                }else{
                    $arr_det_act = [
                        'valor'             => $orden->total_valor + $det_priv->valor,
                        'valor_descuento'   => $orden->descuento_valor + $det_priv->valor_descuento,
                    ];
                    //dd($detalle_falsa);
                    $det_priv->update($arr_det_act);   
                }
            }*/

        }
        //dd($acum_publico);

        $arr_det = [
            'id_examen_orden'   => $orden_id,
            'id_examen'         => '1216',
            'valor'             => $acum_publico,
            'valor_descuento'   => 0,
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,
            'id_usuariocrea'    => $idusuario,
            'id_usuariomod'     => $idusuario,
        ];
        Examen_Detalle::create($arr_det); 
        $arr_det = [
            'id_examen_orden'   => $orden_id,
            'id_examen'         => '1217',
            'valor'             => $acum_privado,
            'valor_descuento'   => 0,
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,
            'id_usuariocrea'    => $idusuario,
            'id_usuariomod'     => $idusuario,
        ];
        Examen_Detalle::create($arr_det);  

        $orden_falsa = Examen_Orden::find($orden_id);

        $valor_descuento = $orden_falsa->detalles->sum('valor_descuento');
        $valor           = $orden_falsa->detalles->sum('valor');
        $total_valor     = $valor - $valor_descuento;
        $cantidad        = $orden_falsa->detalles->count();

        $arr_cab= [
            'cantidad'          => $cantidad,
            'valor'             => $valor,
            'descuento_p'       => 0,
            'descuento_valor'   => $valor_descuento,
            'total_valor'       => $total_valor,
            'recargo_valor'     => 0,
        ];

        $orden_falsa->update($arr_cab);

        //datos factura
        $input_cli_crea = [
            'identificacion'          => $request['cedula_factura'],
            'nombre'                  => $request['nombre_factura'],
            'ciudad_representante'    => $request['ciudad_factura'],
            'direccion_representante' => $request['direccion_factura'],
            'telefono1_representante' => $request['telefono_factura'],
            'email_representante'     => $request['email_factura'],
            'tipo'                    => $request['documento'],
            'clase'                   => '1',
            'cedula_representante'    => $request['cedula_factura'],
            'estado'                  => '1',
            'id_usuariocrea'          => $idusuario,
            'id_usuariomod'           => $idusuario,
            'ip_creacion'             => $ip_cliente,
            'ip_modificacion'         => $ip_cliente,
            'nombre_representante'    => $request['nombre_factura'],
            'pais'                    => 'Ecuador',

        ];

        $input_cli_mod = [
            'identificacion'          => $request['cedula_factura'],
            'nombre'                  => $request['nombre_factura'],
            'ciudad_representante'    => $request['ciudad_factura'],
            'direccion_representante' => $request['direccion_factura'],
            'telefono1_representante' => $request['telefono_factura'],
            'email_representante'     => $request['email_factura'],
            'tipo'                    => $request['documento'],
            'clase'                   => '1',
            'cedula_representante'    => $request['cedula_factura'],
            'estado'                  => '1',
            'id_usuariomod'           => $idusuario,
            'ip_modificacion'         => $ip_cliente,
            'nombre_representante'    => $request['nombre_factura'],
            'pais'                    => 'Ecuador',

        ];

        $arr_orden = [
            'tipo_documento'    => $request['documento'],
            'cedula_factura'    => $request['cedula_factura'],
            'nombre_factura'    => $request['nombre_factura'],
            'direccion_factura' => $request['direccion_factura'],
            'ciudad_factura'    => $request['ciudad_factura'],
            'email_factura'     => $request['email_factura'],
            'telefono_factura'  => $request['telefono_factura'],
        ];

        foreach ($agrup2 as $ordenes) {
            $ord_ori = Examen_Orden::find($ordenes);
            $ord_ori->update($arr_orden);
        }

        $cliente = Ct_Clientes::where('identificacion', $request['cedula_factura'])->where('estado', '1')->first();

        if (!is_null($cliente)) {
            Ct_Clientes::where('identificacion', $request['cedula_factura'])->update($input_cli_mod);
        } else {
            Ct_Clientes::create($input_cli_crea);
        }

        $ord_falsa = Examen_Orden::find($orden_id);
        $ord_falsa->update($arr_orden);

        //$agrupada_sri = $this->agrupada_sri($orden_id);

        return "ok";

    }

    public function datos_factura_agrupada(){


        return view('laboratorio/facturalabs/datos_agrupada');

    }

    public function agrupada_sri($id_falsa){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $orden                = Examen_Orden::find($id_falsa);
        //$data['empresa']      = '0993075000001'; // humanlabs
        $data['empresa']      = '0992704152001';   //gastroclinica     
        $cliente['cedula']    = $orden->cedula_factura;
        $cliente['tipo']      = $orden->tipo_documento; //eduardo dice q el lo calcula 
        $cliente['nombre']    = $orden->nombre_factura;
        $cliente['apellido']  = '';
        $explode              = explode(" ",$orden->nombre_factura);
        if(count($explode)>=4){
            $cliente['nombre']    = $explode[0].' '.$explode[1];
            for ($i=2; $i < count($explode) ; $i++) { 
                $cliente['apellido']  = $cliente['apellido'].' '.$explode[$i];       
            }   
        }
        if(count($explode)==3){
            $cliente['nombre']    = $explode[0];   
            $cliente['apellido']  = $explode[1].' '.$explode[2]; 
        }
        if(count($explode)==2){
            $cliente['nombre']    = $explode[0];   
            $cliente['apellido']  = $explode[1]; 
        }
        //dd($cliente);
        
        $cliente['email']     = $orden->email_factura;
        $cliente['telefono']  = $orden->telefono_factura;
        $direccion['calle']   = $orden->direccion_factura;
        $direccion['ciudad']  = $orden->ciudad_factura;
        $cliente['direccion'] = $direccion;
        $data['cliente']      = $cliente;

        $msn_error='';
        $flag_error=false;
        if($cliente['cedula']==null){
            $flag_error=true;
            $msn_error='Error en cedula';
        }
        
        if($cliente['nombre']==null){
            $flag_error=true;
            $msn_error='Error en Nombre';
        }
        if($cliente['email']==null){
            $flag_error=true;
            $msn_error='Error en email';
        } 
        if($cliente['telefono']==null){
            $flag_error=true;
            $msn_error='Error en telefono';
        }
        if($direccion['calle']==null){
            $flag_error=true;
            $msn_error='Error en calle';
        }
        if($direccion['ciudad']==null){
            $flag_error=true;
            $msn_error='Error en Ciudad';
        }

        $cant = 0;
        foreach ($orden->detalles as $value) {
           //se envian los productos
            $valor = $value->valor * $value->cantidad;
            $subtotal = $valor - $value->valor_descuento;
            $producto['sku']       = "LABS-".$value->examen->id;//ID EXAMEN
            $producto['nombre']    = $value->examen->nombre;// NOMBRE DEL EXAMEN
            $producto['cantidad']  = $value->cantidad;
            $producto['precio']    = $value->valor;//DETALLE
            $producto['descuento'] = $value->valor_descuento;
            $producto['subtotal']  = $subtotal;//precio-descuento
            $producto['tax']       = "0";
            $producto['total']     = $subtotal;//SUBTOTAL
            $producto['copago']    = "0"; 
            $productos[$cant] = $producto;
            $cant ++;
        }

        if($orden->recargo_valor>0){
            $producto['sku']       = "LABS-FEE";
            $producto['nombre']    = 'FEE-ADMINISTRATIVO';
            $producto['cantidad']  = "1";
            $producto['precio']    = $orden->recargo_valor;//DETALLE
            $producto['descuento'] = '0';
            $producto['subtotal']  = $orden->recargo_valor;//precio-descuento
            $producto['tax']       = "0";
            $producto['total']     = $orden->recargo_valor;//SUBTOTAL
            $productos[$cant] = $producto;
            $cant ++;
        }

        $data['productos'] = $productos;
         /*01  SIN UTILIZACION DEL SISTEMA FINANCIERO
        15  COMPENSACIÓN DE DEUDAS
        16  TARJETA DE DÉBITO
        17  DINERO ELECTRÓNICO
        18  TARJETA PREPAGO
        19  TARJETA DE CRÉDITO
        20  OTROS CON UTILIZACION DEL SISTEMA FINANCIERO
        21  ENDOSO DE TÍTULOS
        */
        $info_adicional['nombre']      = "AGENTES_RETENCION";
        $info_adicional['valor']       = "Resolucion 1";
        $info[0]                       = $info_adicional;

        $info_adicional['nombre']      = "PACIENTE";
        $info_adicional['valor']       = "";//eduardo dice q null
        $info[1]                       = $info_adicional; 

        $info_adicional['nombre']      = "MAIL";
        $info_adicional['valor']       = $orden->email_factura;//EMAIL
        $info[2]                       = $info_adicional;

        $info_adicional['nombre']      = "CIUDAD";
        $info_adicional['valor']       = $orden->ciudad_factura;//EMAIL
        $info[3]                       = $info_adicional;

        $info_adicional['nombre']      = "DIRECCION";
        $info_adicional['valor']       = $orden->direccion_factura;//EMAIL
        $info[4]                       = $info_adicional;

        $info_adicional['nombre']      = "ORDEN";
        $info_adicional['valor']       = ''.$orden->id.'';//EMAIL
        $info[5]                       = $info_adicional;

        $info_adicional['nombre']      = "SEGURO";
        $info_adicional['valor']       = 'PARTICULAR';//SEGURO ???
        $info[6]                       = $info_adicional;
        
        $pago['forma_pago']     = '20';
        $info_adicional['nombre']      = "FORMA_PAGO";
        $texto = '';

        $agrup2 = session()->get('a_orden');
        $fp_cant = 0;

        if($orden->estado=='-3'){
            $texto = 'PENDIENTE PAGO :'.$orden->total_valor;

            $tipos_pago['id_tipo']            = 7; //metodo de pago efectivo, tarjeta, etc
            $tipos_pago['fecha']              = date('Y-m-d H:i:s');
            $tipos_pago['tipo_tarjeta']       = ''; //si es efectivo no se envia
            $tipos_pago['numero_transaccion'] = ''; //si es efectivo no se envia
            $tipos_pago['id_banco']           = ''; //si es efectivo no se envia
            $tipos_pago['cuenta']             = ''; //si es efectivo no se envia
            $tipos_pago['giradoa']            = null; //si es efectivo no se envia
            $tipos_pago['valor']              = $orden->total_valor; //valor a pagar de total
            $tipos_pago['valor_base']         = $orden->total_valor; //valor a pagar de base

            $pagos[$fp_cant]                  = $tipos_pago;
            $fp_cant++;

        }else{
            foreach ($agrup2 as $ordenes) {
                $orden_ori = Examen_Orden::find($ordenes);
                //$cuenta_forma = $orden_ori->detalle_forma_pago->count();
                //dd($cuenta_forma);

                foreach($orden_ori->detalle_forma_pago as $fp){
                    $total =$fp->valor + $fp->p_fi;
                    $total =round($total,2);
                    $texto      = $texto.' '.$fp->tipo_pago->nombre.': '.$total;
                } 
            
                foreach($orden_ori->detalle_forma_pago as $fp){
                    
                    $tipos_pago['id_tipo']            = $fp->id_tipo_pago; //metodo de pago efectivo, tarjeta, etc
                    $tipos_pago['fecha']              = substr($orden->fecha_orden,0,10);
                    $tipos_pago['tipo_tarjeta']       = $fp->tipo_tarjeta; //si es efectivo no se envia
                    $tipos_pago['numero_transaccion'] = $fp->numero; //si es efectivo no se envia
                    $tipos_pago['id_banco']           = $fp->banco; //si es efectivo no se envia
                    $tipos_pago['cuenta']             = $fp->cuenta; //si es efectivo no se envia
                    $tipos_pago['giradoa']            = null; //si es efectivo no se envia
                    $tipos_pago['valor']              = $fp->valor + $fp->p_fi; //valor a pagar de total
                    $tipos_pago['valor_base']         = $fp->valor + $fp->p_fi; //valor a pagar de base

                    $pagos[$fp_cant]                  = $tipos_pago;
                    $fp_cant++;
                }  

            }
        }
            
        $info_adicional['valor'] = $texto;
        $info[7]  = $info_adicional;
        $pago['informacion_adicional'] = $info;
        $pago['dias_plazo']            = '10';
        $data['pago']                  = $pago;
        $data['contable']              = 1; //si se marca 1 crea factura, 0 no crea factura
        $data['laboratorio']           = 1;
        $data['paciente']              = '';
        $data['concepto']              = 'Factura Electronica - '.$orden->nombre_factura;
        $data['copago']                = 0;
        $data['id_seguro']             = '1'; //si se marca 1 se crea un solo producto por id de laboratorioo, 0 graba detalle de datos
        $data['total_factura']         = $orden->total_valor;
        $data['formas_pago']           = $pagos;
        //dd($orden_ori);
        //dd($data);

        if($orden->fecha_envio!=null){
            $flag_error=true;
            $msn_error='Ya enviado al SRI';
        }

        if($flag_error){
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "LABORATORIO",
                'dato_ant1'   => $orden->id,
                'dato1'       => $orden->id_paciente,
                'dato_ant2'   => "ERROR AL ENVIAR AL SRI AGRUPADA",
                'dato_ant4'   => $msn_error,
            ]); 
            return "error";   
        }

        $orden->update([
            'fecha_envio' => date('Y-m-d H:i:s'),  
        ]);

        /*foreach ($agrup2 as $value) {
            $o_ori = Examen_Orden::find($value);
            $o_ori->update([
                'fecha_envio' => date('Y-m-d H:i:s'),  
            ]);
        }*/
        $hijas = Examen_Orden::where('orden_agrupado',$id_falsa)->where('estado','1')->get();
        foreach ($hijas as $hija) {
            $hija->update([
                'fecha_envio' => date('Y-m-d H:i:s'),  
            ]);   
        }

        //$this->eliminar_sesion($request);
        //dd($data);
        $envio = ApiFacturacionController::envio_factura($data);
        //dd($envio);
        $orden->update([
            'comprobante' => $envio->comprobante,
            'fecha_envio' => date('Y-m-d H:i:s'),  
        ]);

        /*foreach ($agrup2 as $ord) {
            $ord_orig = Examen_Orden::find($ord);
            $ord_orig->update([
                'comprobante' => $envio->comprobante,
                'fecha_envio' => date('Y-m-d H:i:s'),  
            ]);
        }*/

        foreach ($hijas as $hija) {
            $hija->update([
                'comprobante' => $envio->comprobante,
                'fecha_envio' => date('Y-m-d H:i:s'),  
            ]);
        }    

        $manage = $envio->status->status.'-'.$envio->status->message.'-'.$envio->status->reason;

        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "LABORATORIO",
            'dato_ant1'   => $orden->id,
            'dato1'       => $orden->id_paciente,
            'dato_ant2'   => "ENVIAR AL SRI AGRUPADA",
            'dato_ant4'   => $manage,
        ]);

        return "ok";
        //dd($envio->comprobante);
    }

    public function recupera_ordenes()
    {

        $ordenes = DB::table('examen_orden as eo')->join('paciente as p', 'p.id', 'eo.id_paciente')->join('seguros as s', 's.id', 'eo.id_seguro')->leftjoin('empresa as em', 'em.id', 'eo.id_empresa')->leftjoin('nivel as n', 'n.id', 'eo.id_nivel')->leftjoin('protocolo as proto', 'proto.id', 'eo.id_protocolo')->leftjoin('users as d', 'd.id', 'eo.id_doctor_ieced')->join('users as cu', 'cu.id', 'eo.id_usuariocrea')->join('users as mu', 'mu.id', 'eo.id_usuariomod')->select('eo.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 's.nombre as snombre', 'n.nombre as nnombre', 'em.nombrecomercial', 'cu.nombre1 as cnombre1', 'cu.apellido1 as capellido1', 'mu.nombre1 as mnombre1', 'mu.apellido1 as mapellido1', 'em.nombre_corto', 'proto.pre_post', 's.tipo as stipo', 'p.sexo');

        return $ordenes;

    }

    public function carga_masivo(Request $request)
    {

        $seguro      = $request['seguro'];
        $nombres     = $request['nombres'];
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $facturadas  = $request['facturadas'];

        $ordenes = $this->recupera_ordenes();
        $ses_ordenes = $this->recupera_ordenes();

        if ($facturadas == 'FACTURADAS') {

            $ordenes = $ordenes->whereNotNull('eo.fecha_envio');
            $ses_ordenes = $ses_ordenes->whereNotNull('eo.fecha_envio');
        }
        if ($facturadas == 'NO FACTURADAS') {

            $ordenes = $ordenes->whereNull('eo.fecha_envio');
            $ses_ordenes = $ses_ordenes->whereNull('eo.fecha_envio');
        }
        if ($fecha != null) {

            $ordenes = $ordenes->whereBetween('eo.fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
            $ses_ordenes = $ses_ordenes->whereBetween('eo.fecha_orden', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
        }
        if ($seguro != null) {

            $ordenes = $ordenes->where('eo.id_seguro', $seguro);
            $ses_ordenes = $ses_ordenes->where('s.tipo', 0);
        }
        if ($nombres != null) {

            $nombres2    = explode(" ", $nombres);
            $nombres_sql = '';
            $cantidad    = count($nombres2);
            foreach ($nombres2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }
            $nombres_sql = $nombres_sql . '%';

            if ($cantidad == '2' || $cantidad == '3') {
                $ordenes = $ordenes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });

                $ses_ordenes = $ses_ordenes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });               

            } else {

                $ordenes = $ordenes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });
                $ses_ordenes = $ses_ordenes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });
                
            }

        }

        $ordenes = $ordenes->where('eo.estado', '<>', '0')->paginate(40);
        $ses_ordenes = $ses_ordenes->where('eo.estado', '1')->whereNull('eo.fecha_envio')->where('pago_online','0')->get();
        $ordenes2 = [];

        $ex_det = [];
        foreach ($ordenes as $orden) {
            $txt_examen = "";
            $detalle    = DB::table('examen_detalle as ed')->where('ed.id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'ed.id_examen')->select('ed.*', 'e.nombre')->get();
            $bandera    = 0;
            foreach ($detalle as $value) {
                if ($bandera == 0) {
                    $txt_examen = $value->nombre;
                    $bandera    = 1;
                } else {
                    $txt_examen = $txt_examen . '+' . $value->nombre;
                }
            }
            $ex_det[$orden->id] = $txt_examen;
        }
        $seguros = Seguro::where('inactivo', '1')->get();

        $gestiones = Examen_Orden::where('pago_online', '1')->where('estado', '1')->whereNull('fecha_orden')->where('gestion', '0')->where('estado_pago', '1')->get();

        foreach ($ses_ordenes as $value) {
            $id_orden = $value->id;
            $agrup2 = session()->get('a_orden');
            $orden = Examen_Orden::find($id_orden);

            if ($agrup2 == null) {
                $agrup2[0] = $id_orden;

            }else{
                $orden1 = Examen_Orden::find($agrup2[0]);
                if(!in_array($id_orden, $agrup2)){

                    $i = count($agrup2);
                    $agrup2[$i] = $id_orden;
                }
                    
                        
            }
            

            $request->session()->put('a_orden', $agrup2);
        }
        

        //dd($ses_ordenes);

        return view('laboratorio/orden/index', ['ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'ex_det' => $ex_det, 'nombres' => $nombres, 'seguro' => $seguro, 'seguros' => $seguros, 'ordenes2' => $ordenes2, 'gestiones' => $gestiones, 'facturadas' => $facturadas]);

    }  

    public function temporal_factura_agrupada($id_falsa){

        $agrupada_sri = $this->agrupada_sri($id_falsa);

        return "Gracias Amigo";


    }  




}    