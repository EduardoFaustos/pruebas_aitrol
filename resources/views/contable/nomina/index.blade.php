@extends('contable.nomina.base')
@section('action-content')

<!-- Ventana modal Egreso Empleado-->
<div class="modal fade" id="modal_egreso_empleado" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>
<!-- Ventana modal Prestamos Empleado-->
<div class="modal fade" id="modal_prestamos_empleado" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>
<!-- Ventana modal Anticipos Empleado-->
<div class="modal fade" id="modal_anticipos_empleado" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>
<!-- Ventana modal Otros Anticipos Empleado-->
<div class="modal fade" id="modal_otros_anticipos_empleado" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>
<!-- Ventana modal Saldos Iniciales-->
<div class="modal fade" id="modal_saldos_iniciales" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>


<!--<style type="text/css">
        .ui-autocomplete
        {
            overflow-x: hidden;
            max-height: 200px;
            width:1px;
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            float: left;
            display: none;
            min-width: 160px;
            _width: 160px;
            padding: 4px 0;
            margin: 2px 0 0 0;
            list-style: none;
            background-color: #fff;
            border-color: #ccc;
            border-color: rgba(0, 0, 0, 0.2);
            border-style: solid;
            border-width: 1px;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
            -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            -webkit-background-clip: padding-box;
            -moz-background-clip: padding;
            background-clip: padding-box;
            *border-right-width: 2px;
            *border-bottom-width: 2px;
        }
        .container-4{
              overflow: hidden;
              vertical-align: middle;
              white-space: nowrap;
        }
        .container-4 input#nombre_proveedor{
              height: 40px;
              background: #fff;
              border-radius: 5px;
              font-size: 10pt;
              float: left;
              color: black;
              border-color: #ececed;
              padding-left: 15px;
              -webkit-border-radius: 5px;
              -moz-border-radius: 5px;
              border-radius: 5px;
        }
        .container-4 input#nombre_proveedor::-webkit-input-placeholder {
          color: #65737e;
        }

        .container-4 input#nombre_proveedor:-moz-placeholder { /* Firefox 18- */
          color: #65737e;
        }

        .container-4 input#nombre_proveedor::-moz-placeholder {  /* Firefox 19+ */
          color: #65737e;
        }

        .container-4 input#nombre_proveedor:-ms-input-placeholder {
          color: #65737e;
        }
        .container-4 button.icon{
          -webkit-border-top-right-radius: 5px;
          -webkit-border-bottom-right-radius: 5px;
          -moz-border-radius-topright: 5px;
          -moz-border-radius-bottomright: 5px;
          border-top-right-radius: 5px;
          border-bottom-right-radius: 5px;
          border: none;
          background: #232833;
          height: 40px;
          width: 50px;
          color: #4f5b66;
          opacity: 0;
          font-size: 12px;

          -webkit-transition: all .55s ease;
          -moz-transition: all .55s ease;
          -ms-transition: all .55s ease;
          -o-transition: all .55s ease;
          transition: all .55s ease;
        }
        .container-4:hover button.icon, .container-4:active button.icon, .container-4:focus button.icon{
          outline: none;
          opacity: 1;
          margin-left: -50px;
        }

        .container-4:hover button.icon:hover{
          background: white;
        }
        .container-4 input#buscar_secuencia::-webkit-input-placeholder {
          color: #65737e;
        }

        .container-4 input#buscar_secuencia:-moz-placeholder { /* Firefox 18- */
          color: #65737e;
        }

        .container-4 input#buscar_secuencia::-moz-placeholder {  /* Firefox 19+ */
          color: #65737e;
        }

        .container-4 input#buscar_secuencia:-ms-input-placeholder {
          color: #65737e;
        }
        .container-4 input{
              height: 40px;
              background: #fff;
              border-radius: 5px;
              font-size: 10pt;
              float: left;
              color: black;
              border-color: #ececed;
              padding-left: 15px;
              -webkit-border-radius: 5px;
              -moz-border-radius: 5px;
              border-radius: 5px;
        }
        .cabecera{
            background-color:  #D4D0C8;
            border-radius: 1px;

        }
        .color_texto{
            color: #ffffff;
        }

        .label_header{
          background-color: #555555;
          width: 100%;
          height: 40px;
          margin: 0 auto;
          line-height: 25px;
          color: #FFF;
        }

        table{

          font-size: 7pt;
          font-family: 'arial';
          width: 100%;
          border: black 1px solid;
        }

        tbody {
          border: black 1px solid;
        }

        table th{
         text-align: center;
         background-color: #cccccc;
         font-size: 12px;
         border: black 1px solid;
         color: black;

         font-family: 'BrixSansBlack';
        }

        table td{
          padding: 4px;
          background-color: #FFFFFF;
        }

        .table>thead>tr>th, .table>tbody>tr>th, .table>tfoot>tr>th, .table>thead>tr>td, .table>tbody>tr>td, .table>tfoot>tr>td {
            border-top: 1px solid #f4f4f4;
            border: solid 1px #CCC;
        }
        table.dataTable thead > tr > th {
            padding-right: 30px;
            border-bottom: 1px solid #111;
            border-left: 1px solid #111;
            border-right: 1px solid #111;
        }
        .table>thead>tr>td:nth-child(1), .table>tbody>tr>td:nth-child(1), .table>tfoot>tr>td:nth-child(1) {
              border-left: 1px solid #111;
        }
        /* CAMBIAR EL INDICE 5 POR EL ULTIMO INDICE DE LA TABLA SEGUN LA CANTIDAD DE COLUMNAS QUE TENGAS*/
        .table>thead>tr>td:nth-child(9), .table>tbody>tr>td:nth-child(9), .table>tfoot>tr>td:nth-child(9) {
              border-right: 1px solid #111;
        }
        table.dataTable {
            border-bottom: solid 2px #222;
        }

</style>-->

<style type="text/css">
  .boton_egreso {
    color: white;
    border-radius: 3px;
    padding: 5px;
    height: 10%;

    margin: 2px;
    -moz-animation: 2s bote 1;
    animation: 2s bote 1;
    -webkit-transform: 2s bote 1;
  }

  .container {

    width: 100%;
    height: 40px;

  }
</style>



<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<!-- Main content -->
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('nomina.contable')}}</a></li>
      <li class="breadcrumb-item"><a href="#">{{trans('nomina.nomina')}}</a></li>
      <li class="breadcrumb-item active" aria-current="page">{{trans('nomina.empleado')}}</li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header">
      <div class="col-md-9">
        <!--<h8 class="box-title size_text">Empleados</h8>-->
        <!--<label class="size_text" for="title">EMPLEADOS</label>-->
        <h5><b>{{trans('nomina.lista_nomina')}}</b></h5>
      </div>
      <div class="col-md-1 text-right">
        <button onclick="location.href='{{route('nomina.crear')}}'" class="btn btn-success btn-gray">
          <i aria-hidden="true"></i>{{trans('nomina.agregar_empleado')}}
        </button>
      </div>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">{{trans('nomina.buscador_empleado')}}</label>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('nomina.buscar') }}">
        {{ csrf_field() }}
       
        <div class="form-group col-md-2 col-xs-10 container-4" style="padding-left: 10px;">
          <div class="row">
            <div class="col-md-12">
              <label class="texto" for="estado">{{trans('contableM.estado')}}:</label>
            </div>  
            <div class="col-md-12">
              <select class="form-control" type="text" id="estado" name="estado" placeholder="Elija Estado..." autocomplete="off">
                <option value="">{{trans('contableM.todos')}}</option>
                <option @if(isset($searchingVals['estado'])) @if($searchingVals['estado']==1) selected="selected" @endif @else selected @endif value="1" >{{trans('nomina.activo')}}</option>
                <option @if(isset($searchingVals['estado'])) @if($searchingVals['estado']==0) selected="selected" @endif @endif value="0">{{trans('nomina.inactivo')}}</option>
              </select>
            </div>
          </div>    
        </div>
        
        <div class="form-group col-md-2 col-xs-10 container-4" style="padding-left: 10px;">
          <div class="row">
            <div class="col-md-12">
              <label class="texto" for="estado">{{trans('nomina.identificacion')}}:</label>
            </div>  
            <div class="col-md-12">  
              <input class="form-control" type="text" id="identificacion" name="identificacion" placeholder="{{trans('nomina.ingrese')}} {{trans('nomina.identificacion')}}..." value="@if(isset($searchingVals)){{$searchingVals['id_user']}}@endif" autocomplete="off" />
            </div>
          </div>    
        </div>
       
        <div class="form-group col-md-2 col-xs-10 container-4" style="padding-left: 10px;">
          <div class="row">
            <div class="col-md-12">
              <label class="texto" for="estado">{{trans('nomina.empleado')}}:</label>
            </div>  
            <div class="col-md-12">
              <input class="form-control" type="text" id="buscar_nombre" name="buscar_nombre" placeholder="{{trans('nomina.ingrese')}} {{trans('nomina.empleado')}}..." value="@if(isset($searchingVals)){{$searchingVals['nombres']}}@endif" autocomplete="off" />
            </div>
          </div>    
        </div>

        <div class="form-group col-md-4 col-xs-10 container-4" style="padding-left: 10px;">
          <div class="row">
            <div class="col-md-12">
              <label class="texto" for="estado">{{trans('nomina.caja_sucursal')}}:</label>
            </div>
            <div class="col-md-12"> 
              <select id="id_caja" name="id_caja" class="form-control" required>
                <option>{{trans('nomina.seleccione')}} ... </option>
                @foreach($cajas as $caja)
                  <option value="{{$caja->id}}">{{$caja->sucursal->codigo_sucursal}}:{{$caja->sucursal->nombre_sucursal}} => {{$caja->codigo_caja}}:{{$caja->nombre_caja}}</option>
                @endforeach
              </select>
            </div>
          </div>    
        </div>


        <div class="col-xs-2">
          <br>
          <button type="submit" id="buscarEmpleado" class="btn btn-primary">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
          </button>
        </div>
      </form>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">{{trans('nomina.lista_empleado')}}</label>
      </div>
    </div>
    <div class="box-body dobra">
      <div class="form-group col-md-12">
        <div class="form-row">
          <div id="resultados">
          </div>
          <div id="contenedor" class="container" style="padding: 0;">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
  
                <div class="col-md-12" style="padding: 0;">
                  <table id="example2" class="table table-bordered table-hover">
                    <thead>
                      <tr class='well-dark'>
                        <th width="10%"  >{{trans('nomina.caja_sucursal')}}</th>
                        <th width="10%"  >{{trans('nomina.cedula')}}</th>
                        <th width="20%"  >{{trans('nomina.empleado')}}</th>
                        <th width="10%"  >{{trans('nomina.cargo')}}</th>
                        <th width="10%"  >{{trans('nomina.sueldo_fijo')}}</th>
                        <th width="10%"  >{{trans('nomina.fecha_ingreso')}}</th>
                        <th width="5%"   >{{trans('nomina.estado')}}</th>
                        <th width="5%"   >{{trans('nomina.saldos')}}</th>
                        <th width="10%"  >{{trans('nomina.saldos_prestamos')}}</th>
                        <th width="10%"  >{{trans('nomina.saldo_inicial')}}</th>
                        <th width="10%"  >{{trans('nomina.acciones')}}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php
                        $fecha=date("Y-m-d");
                        $fec=new DateTime($fecha);
                        $cont =0;
                        
                      @endphp
                      @foreach ($registros as $value)
                      @php
                        $estilo = "";
                        $texto ="";
                        $inf_usuario = Sis_medico\User::where('id',$value->id_user)->first();

                        $fec2=new DateTime($value->fecha_ingreso);
                        $diff = $fec->diff($fec2);
                        $days= $diff->days;

                        $intervalMeses=$diff->format("%m");      
                        $intervalAnos = $diff->format("%y")*12;
                        $intervalDias = $diff->format("%d");
                        
                        $meses_totales = $intervalMeses + $intervalAnos;

                        $anioActual = date("Y");
                        $mesActual = date("n");
                        $cantidadDias = cal_days_in_month(CAL_GREGORIAN, $mesActual, $anioActual);
                        //$cantidadDias;
                        
                        $faltanDias = intval($cantidadDias) - intval($intervalDias);
                        //dd("{$cantidadDias} - {$intervalDias}");

                        if($meses_totales>= 11 && $meses_totales <= 12){
                          if(intval($intervalDias) == 0){
                            $estilo = "color: red; font-weight:bold";
                            $texto = "Tiene 1 año";
                          }else{
                            $estilo = "color: green; font-weight:bold";
                            $texto = "Faltan {$faltanDias} días para el año ";
                          }
                        }
                        $prestamos = Sis_medico\Ct_Rh_Prestamos::where('id_empl',$value->id_user)->where('estado','1')->get()->sum('saldo_total');
                        $saldos    = Sis_medico\Ct_Rh_Saldos_Iniciales::where('id_empl',$value->id_user)->where('estado','1')->get()->sum('saldo_res');
                      @endphp

                      <tr class="well">
                        <td>{{$value->codigo_sucursal}}-{{$value->codigo_caja}}</td>
                        <td>@if(!is_null($value->id_user)){{$value->id_user}}@endif</td>
                        <td> {{ $inf_usuario->apellido1 }} @if($inf_usuario->apellido2!='(N/A)'){{$inf_usuario->apellido2}}@endif {{$inf_usuario->nombre1}} @if($inf_usuario->nombre2!='(N/A)'){{ $inf_usuario->nombre2 }}@endif</td>
                        <!--<td >@if(!is_null($value->cargo)){{$value->cargo}}@endif</td>-->
                        <td>@if(!is_null($value->cargo)){{$value->cargo}}@endif</td>
                        <td>@if(!is_null($value->sueldo_neto)){{$value->sueldo_neto}}@endif</td>
                        <td style="{{$estilo}}" >@if(!is_null($value->fecha_ingreso)){{$value->fecha_ingreso}}@endif <br> <span class="label label-info">{{$texto}}</span>  </td>
                        <td>@if($value->estado == '1') Activo @elseif($value->estado =='0') {{trans('nomina.anulada')}} @else {{trans('nomina.activo')}} @endif</td>
                        <td >@if(($saldos + $prestamos) != 0 )<a href="{{route('nuevo_rol.index_prestamos_saldos', ['id_user' => $value->id_user])}}" class="btn  btn-success btn-xs" target="_blank"><span style=" font-size: 10px;color: white;" >DET</span></a><a href="{{route('nuevo_rol.excel_prestamos_saldos', ['id_user' => $value->id_user])}}" class="btn  btn-success btn-xs" target="_blank"><span style=" font-size: 10px;color: white;" >XLS</span></a>@endif</td>
                        <td @if($prestamos != 0) style="color: red;text-align: right;" @endif style="text-align: right;">$ {{ number_format($prestamos, 2, ',', ' ') }} </td>
                        <td @if($saldos != 0) style="color: red;text-align: right;" @endif style="text-align: right;">$ {{ number_format($saldos, 2, ',', ' ') }} </td>
                        <td>
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          @if($value->estado == '1')
                          <div class="btn-group">
                            <button type="button" class="btn btn-success btn-xs btn-gray"><span style=" font-size: 10px">{{trans('nomina.opciones')}}</span></button>
                            <button type="button" class="btn btn-success btn-xs btn-gray dropdown-toggle" data-toggle="dropdown" aria-expanded="false" style="padding-left: 3px;padding-right: 3px">
                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu" style="background-color: #ffffff;padding: 2px;min-width: 80px;">
                              <li><a href="{{route('nomina.revisar', ['id' => $value->id]) }}" class="btn btn-success btn-xs"><span style=" font-size: 10px"><b>{{trans('nomina.visualizar')}}</b></span></a></li>
                              <li><a href="{{route('nomina.anular', ['id' => $value->id]) }}"><span style=" font-size: 10px">{{trans('nomina.eliminar')}}</span></a></li>
                              <!--li><a href="{{route('rol_pago.index', ['id' => $value->id,'id_empresa' => $empresa->id])}}"><span style=" font-size: 10px">Crear Rol</span></a></li-->
                              <li><a href="{{route('nuevo_rol.index', ['id' => $value->id])}}"><span style=" font-size: 10px;color: red;">{{trans('nomina.nuevo_rol')}}</span></a></li>
                              <li><a href="{{route('nuevo_rol.index_prestamos_saldos', ['id_user' => $value->id_user])}}"><span style=" font-size: 10px;color: red;">{{trans('nomina.reporte_saldo_prestamos')}}</span></a></li>
                              <li><a href="{{route('prestamos_empleado.crear', ['id' => $value->id,$value->id_user])}}" data-toggle="modal" data-target="#modal_prestamos_empleado"><span style=" font-size: 10px">{{trans('nomina.prestamos')}}</span></a></li>
                              <!--<li><a href="{{route('anticipo_empleado.crear', ['id' => $value->id,$inf_usuario->nombre1,$inf_usuario->nombre2,$inf_usuario->apellido1,$inf_usuario->apellido2,$value->id_user])}}" data-toggle="modal" data-target="#modal_anticipos_empleado"><span style=" font-size: 10px">Anticipos 1era Quinc</span></a></li>-->
                              <li><a href="{{route('otros_anticipo_empleado.crear', ['id' => $value->id,$value->id_user])}}" data-toggle="modal" data-target="#modal_otros_anticipos_empleado"><span style=" font-size: 10px">{{trans('nomina.otros_anticipos')}}</span></a></li>
                              <li><a href="{{route('saldoinicial_empleado.crear', ['id' => $value->id,$value->id_user])}}" data-toggle="modal" data-target="#modal_saldos_iniciales"><span style=" font-size: 10px">{{trans('nomina.saldos_iniciales')}}</span></a></li>
                            </ul>
                          </div>
                          @endif
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                    <tfoot>
                    </tfoot>
                  </table>
                  
                </div>
             

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /.box-body -->
</section>
<!-- /.content -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">
  $('#modal_egreso_empleado').on('hidden.bs.modal', function() {
    location.reload();
    $(this).removeData('bs.modal');
  });

  $('#modal_prestamos_empleado').on('hidden.bs.modal', function() {
    location.reload();
    $(this).removeData('bs.modal');
  });

  $('#modal_anticipos_empleado').on('hidden.bs.modal', function() {
    location.reload();
    $(this).removeData('bs.modal');
  });

  $('#modal_otros_anticipos_empleado').on('hidden.bs.modal', function() {
    location.reload();
    $(this).removeData('bs.modal');
  });

  $('#modal_saldos_iniciales').on('hidden.bs.modal', function() {
    location.reload();
    $(this).removeData('bs.modal');
  });




  $(document).ready(function($){
    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      'order'       : [[ 1, "asc" ]]
    })
  });  
</script>
@endsection