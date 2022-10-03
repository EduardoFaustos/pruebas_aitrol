@extends('laboratorio.orden.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

@php
  $rolUsuario = Auth::user()->id_tipo_usuario;
@endphp

<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 1px;
} 
.dropdown-menu>li>a{
    color:white !important;
    padding-left: 3px !important;
    padding-right: 3px !important;
    font-size: 12px !important;
  }
 
  .dropdown-menu>li>a:hover{
    background-color:#008d4c !important;
  }
  .cot>li>a:hover{
    background-color:#00acd6 !important;
  }
  .form-group{
    margin-bottom: 2px;
  }
  .hovers:hover{
    cursor: pointer;
  }
</style>

<div class="modal fade bs-example-modal-lg" id="modal_datosfacturas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="datos_factura">
            
        </div>
    </div>
</div>
<!--  Modal recibo   --->
<div class="modal fade bs-example-modal-lg" id="modalrecibo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="datosrecibo">
            
        </div>
    </div>
</div>
<!-- Ventana Modal Pago -->
<div class="modal fade" id="pago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="width: 95%;">
      </div>
    </div>  
</div>


<!-- Ventana Modal Reenvio Email -->
<div class="modal fade" id="reenvio_email" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="width: 95%;">
      </div>
    </div>  
</div>


<!-- Ventana Modal Reseteo Clave -->
<div class="modal fade" id="reseteo_clave" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="width: 95%;">
      </div>
    </div>  
</div>

<!-- Ventana modal editar -->
<div class="modal fade fullscreen-modal" id="doctor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<!-- Ventana modal pagoenlinea -->
<div class="modal fade fullscreen-modal" id="pago_online" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<!-- Ventana modal gestion -->
<div class="modal fade" id="gestionar_orden" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<!-- Ventana modal factura agrupada -->
<div class="modal fade fullscreen-modal" id="modal_agrupada" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content" id="datos_agrupada">
    </div>
  </div>
</div>

<!--EXAMENES PENDIENTES -->
<div class="modal fade bs-example-modal-lg" id="modal_examenes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="div_examenes_pendientes">
            
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
        <div class="col-md-6">
          <h3 class="box-title">Órdenes de Exámenes de Laboratorio</h3>
        </div>
        <div class="col-md-3">
          <a class="btn btn-warning" href="{{ route('orden.create')}}"><span class="ionicons ion-ios-flask"></span> Orden Pública</a>
        </div>
        <div class="col-md-3">
          <a id="pago_on" data-toggle="modal" data-target="#pago_online" @if($gestiones->count()>0) class="btn btn-danger" @else class="btn btn-success" @endif  href="{{ route('orden.pagoenlinea_gestionar')}}">
            <span class="glyphicon glyphicon-credit-card"></span> Pagos On Line
          </a>
        </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <form method="POST" action="{{route('orden.search')}}">
        {{ csrf_field() }}
        <div class="form-row">
          <div class="form-group col-md-3 col-xs-6">
            <label for="fecha" class="col-md-12 control-label">Desde</label>
            <div class="col-md-12">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
                </div>   
              </div>
            </div>  
          </div>

          <div class="form-group col-md-3 col-xs-6">
            <label for="fecha_hasta" class="col-md-12 control-label">Hasta</label>
            <div class="col-md-12">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
                </div>   
              </div>
            </div>  
          </div>

          <div class="form-group col-md-3 col-xs-6">
            <label for="seguro" class="col-md-12 control-label">Seguro</label>
            <div class="col-md-12"> 
              <select id="seguro" name="seguro" class="form-control input-sm" onchange="buscar();">
                <option value="">TODOS</option>
                @foreach ($seguros as $value)
                  <option @if(!is_null($seguro))@if($seguro == $value->id) selected @endif @endif value="{{$value->id}}">{{$value->nombre}}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group col-md-3 col-xs-6">
            <label for="facturadas" class="col-md-12 control-label">Facturadas</label>
            <div class="col-md-12"> 
              <select id="facturadas" name="facturadas" class="form-control input-sm" onchange="buscar();">
                <option @if($facturadas == "TODAS") selected @endif value="TODAS">TODAS</option>
                <option @if($facturadas == "FACTURADAS") selected @endif value="FACTURADAS">FACTURADAS</option>
                <option @if($facturadas == "NO FACTURADAS") selected @endif value="NO FACTURADAS">NO FACTURADAS</option>
              </select>
            </div>
          </div>
          <div class="col-md-12"></div>

          <div class="form-group col-md-3 col-xs-6">
            <label for="nombres" class="col-md-12 control-label">Paciente</label>
            <div class="col-md-12">
              <div class="input-group">
                <input value="@if($nombres!=''){{$nombres}}@endif" type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="Nombres y Apellidos" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('nombres').value = '';"></i>
                </div>
              </div>  
            </div>
          </div>

          <div class="form-group col-md-1 col-xs-2">
            <button type="submit" class="btn btn-primary btn-xs" id="boton_buscar">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
            </button>
            <button type="submit" class="btn btn-primary btn-xs" formaction="{{route('orden.reporte_index')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Reporte
            </button>
          </div>      
          <div class="form-group col-md-2 col-xs-2">
            <button type="submit" class="btn btn-primary btn-xs" formaction="{{route('orden.reporte_mail')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Envio a mail
            </button>
            <button type="submit" class="btn btn-primary btn-xs" formaction="{{route('orden.reporte_detalle')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Detalle de Ordenes
            </button>
          </div> 
         
          <div class="form-group col-md-2 col-xs-2">  
            <button type="submit" class="btn btn-success btn-xs" formaction="{{route('orden.reporte_detalle_covid')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Covid
            </button>
            <button type="submit" class="btn btn-danger btn-xs" formaction="{{route('orden_labs.resultados_pendientes')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Exámenes Pendientes
            </button>
          </div>   
          
          <div class="form-group col-md-2 col-xs-2">  
            
            <a class="btn btn-success btn-xs" href="{{ route('orden_particular.crear_particular')}}"><span class="ionicons ion-ios-flask"></span> Orden Privada</a>
          </div> 

          <div class="form-group col-md-2 col-xs-2">  
          
            <a class="btn btn-primary btn-xs" onclick="factura_agrupada();">Factura Agrupada</a>
          </div>  
          <div class="form-group col-md-2 col-xs-2">  
          
          <a class="btn btn-primary btn-xs" href="{{route('c_caja.index')}}">Cierre Caja</a>
          </div>  
          
          @if($rolUsuario=='20' || $rolUsuario =='1')
            <!--button type="submit" class="btn btn-danger btn-xs" formaction="{{route('facturalabs.carga_masivo')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Facturar masivo
            </button-->
          @endif 
        </div>        
      </form>
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="table-responsive col-md-12" style="min-height: 210px;">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;overflow: none;">
              <thead>
                <tr role="row">
                  <th width="5">Id</th>
                  <th width="5">Fecha</th>
                  <th width="5">Muestra</th>
                  <th width="10">Nombres</th>
                  <th width="5">Convenio</th>
                  <th width="5">Tipo</th>
                  <th width="5">Creada</th>
                  <th width="5">Modific.</th>
                  <th width="5">Cant.</th>
                  <th width="5">Valor</th>
                  <th width="5">Resultados(%)</th>
                  <th width="10">Resultados</th> 
                  <th width="20">Compro.</th>                
                  <th width="10">Acción</th>
                </tr>
              </thead>
              <tbody>
              @foreach ($ordenes2 as $value) <!--FOR EACH PARA LOS PENDIENTES PUBLICOS ABAJO HAY OTRO-->
                  @php 
                    $user = Sis_medico\User::find($value->id_usuariocrea); 
                  @endphp
                  <tr role="row" @if($user->id_tipo_usuario =='3') style="background-color: #ffe0cc;" @endif>
                    <td>{{$value->id}}</td>
                    <td style="font-size: 11px;">{{substr($value->fecha_orden,0,10)}}</td>
                    <td>{{$value->toma_muestra}}</td>
                    <td style="font-size: 11px;">{{$value->papellido1}} @if($value->papellido2=='N/A'||$value->papellido2=='(N/A)') @else{{ $value->papellido2 }} @endif {{$value->pnombre1}} @if($value->pnombre2=='N/A'||$value->pnombre2=='(N/A)') @else{{ $value->pnombre2 }} @endif </td>
                    <td style="font-size: 11px;">{{$value->snombre}} / {{$value->nombre_corto}}</td>
                    <td>
                      @if($value->estado=='-1')
                        @if($value->stipo!='0')
                          <span class="label pull-right bg-red" style="font-size: 10px">Cotiz.</span>
                        @else
                          <span class="label pull-right bg-red" style="font-size: 10px">Pendiente</span>
                        @endif 
                      @else  
                        @if($value->estado_pago =='1')
                          <span class="label pull-right bg-green" style="font-size: 10px">Pag. @if($value->pago_online=='1')Online @endif</span>
                        @endif
                        @if($value->pre_post!=null)
                          <span class="label pull-right bg-primary" style="font-size: 10px">{{$value->pre_post}}</span>
                        @endif
                      @endif
                    </td>
                    <td style="font-size: 11px;@if($user->id_tipo_usuario =='3') color: red; @endif">{{substr($value->cnombre1,0,1)}}{{$value->capellido1}}</td>
                    <td style="font-size: 11px;">{{substr($value->mnombre1,0,1)}}{{$value->mapellido1}}</td>
                    <td>{{$value->cantidad}}</td>
                    <td>{{$value->total_valor}}</td>
                    <td>
                      <div class="progress progress">
                        <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" id="td{{$value->id}}">
                          <span id="sp{{$value->id}}" style="color: black;"></span>
                        </div>
                      </div>
                    </td>
                    <td>
                      @if($value->estado=='1')
                      <div class="col-md-12" style="padding-left: 0px">
                        <div class="btn-group">
                          <button type="button" class="btn btn-success btn-xs" onclick="descargar({{$value->id}});"><span style=" font-size: 10px">Resultados</span></button>
                          <button type="button" class="btn btn-success btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false" style="padding-left: 3px;padding-right: 3px">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                          </button>
                          <ul class="dropdown-menu" role="menu" style="background-color: #00a65a;padding: 2px;min-width: 80px;">
                            <li ><a  href="{{ route('resultados.imprimir3',['id' => $value->id]) }}" ><span style=" font-size: 10px">Formato Gastro</span></a></li>
                          </ul>
                        </div>
                      </div>
                      @endif  
                    </td>
                    <td>
                      @if($value->stipo!='0')
                        <div class="col-md-12" style="padding-left: 0px"> 
                          <div class="btn-group" >
                              <button type="button" class="btn btn-info btn-xs" onclick="window.open('{{ route('cotizador.imprimir', ['id' => $value->id]) }}','_blank');"><span style=" font-size: 9px">COTIZ</span></button>
                              <button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false"  style="padding-left: 2px;padding-right: 2px">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                              </button>
                              <ul class="dropdown-menu cot" role="menu" style="background-color: #00c0ef;padding: 2px;min-width: 80px;">
                                <li ><a  href="{{ route('cotizador.imprimir_gastro', ['id' => $value->id]) }}" target="_blank"><span style=" font-size: 10px">Formato Gastro</span></a></li>
                                <li ><a  href="{{ route('cotizador.imprimir_orden', ['id' => $value->id]) }}" target="_blank"><span style="font-size: 10px">Orden</span></a></li>
                                
                              </ul>
                          </div>
                        </div>
                      @else
                        <div class="col-md-10" style="padding-left: 0px">
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a href="{{ route('orden.descargar', ['id' => $value->id]) }}" target="_blank" class="btn btn-block btn-info btn-xs" style="padding: 0px;">
                          <span >Orden</span> 
                          </a>   
                        </div>
                          
                      @endif   
                    </td>
                    <td>
                      
                      @if($value->stipo!='0')
                        @if($value->realizado=='0' && $value->estado_pago=='0')
                        <div class="col-md-3" style="padding: 3px;">
                          
                          <a href="{{ route('cotizador.editar', ['id' => $value->id]) }}" class="btn btn-block btn-warning btn-xs" style="padding: 0px;">
                          <!--a href="{{ route('orden.edit2', ['id' => $value->id,'dir' => 'rec']) }}" class="btn btn-block btn-warning btn-xs" style="padding: 0px;"-->
                          <span class="glyphicon glyphicon-edit"></span> 
                          </a>  
                        </div>
                        @endif  

                      @else
                        @if($value->realizado=='0')
                          <div class="col-md-3" style="padding: 3px;">
                            
                            <a href="{{ route('orden.edit', ['id' => $value->id]) }}" class="btn btn-block btn-warning btn-xs" style="padding: 0px;">
                            <span class="glyphicon glyphicon-edit"></span>
                            </a>  
                          </div>
                        @endif  
                          
                      @endif 
                      @if($value->realizado=='0' && $value->estado_pago=='0')
                          <div class="col-md-2" style="padding: 3px;">
                            
                            <a href="{{ route('orden.eliminar',['id' => $value->id]) }}" class="btn btn-block btn-danger btn-xs" >
                            <span class="glyphicon glyphicon-trash"></span>
                            </a>  
                          </div>
                      @endif
                      <!--Editar Modal Pago-->
                      @if($value->stipo!='0')
                        @if($value->estado=='1')
                          @if($value->estado_pago =='0')
                            <div class="col-md-3" style="padding: 1px;">
                              <a data-toggle="modal" data-target="#pago" data-remote="{{route('modal.pago_paciente', ['id_paciente' => $value->id_paciente,'id_exa_orden' => $value->id])}}" class="btn btn-danger btn-xs">Pago
                              </a>
                            </div>
                          @endif
                        @endif
                      @endif
                      
                      @if(in_array($rolUsuario, array(1)) == true)
                        @if($value->estado_pago =='1')
                        <div class="col-md-3" style="padding: 3px;">
                          <a data-toggle="modal" data-target="#reseteo_clave" data-remote="{{route('paciente_reseteo_clave', ['id_paciente' => $value->id_paciente,'id_exa_orden' => $value->id])}}" class="btn btn-block btn-warning btn-xs" style="padding: 0px;">
                            <span ></span>R
                          </a>  
                        </div>
                        @endif
                      @endif
                      @if($value->estado_pago =='1')
                        <div class="col-md-3" style="padding: 3px;">
                          <a data-toggle="modal" data-target="#reenvio_email" data-remote="{{route('paciente_reenvio_email', ['id_paciente' => $value->id_paciente,'id_exa_orden' => $value->id])}}" class="btn btn-warning  btn-xs">
                            <span class="glyphicon glyphicon-envelope"></span>
                          </a>
                        </div>
                      @endif
                      
                    </td>          
                  </tr>
              @endforeach
              @php
                $agrup2 = session()->get('a_orden'); 

              @endphp
              @foreach ($ordenes as $value)
                @php 
                 $user = Sis_medico\User::find($value->id_usuariocrea);
                 $pac = DB::table('paciente')->where('id',$value->id_paciente)->first();
                @endphp
                <tr role="row" @if($user->id_tipo_usuario =='3') style="background-color: #ffe0cc;" @endif>
                  <td>
                    @if($value->estado == 1)
                      @if($value->fecha_envio == null)
                        @if($value->stipo!='0')
                          @if($value->pago_online == 0)
                            @if($rolUsuario=='5' || $rolUsuario=='1')
                            <a id="btn{{$value->id}}"  class="btn btn-default btn-xs" onclick="guarda_sesion_factura('{{$value->id}}');"><i class="fa fa-plus"></i></a>
                            @endif
                            @if($rolUsuario=='20' || $rolUsuario=='1')
                            <a id="btn_contab{{$value->id}}"  class="btn btn-default btn-xs" onclick="guarda_sesion_factura_contab('{{$value->id}}');" style="background-color: cyan;"><i class="fa fa-plus"></i></a>
                            @endif
                          @endif
                        @else
                          @if($value->realizado == 1)
                            @if($rolUsuario=='20' || $rolUsuario=='1')
                              <a id="btn_contab{{$value->id}}"  class="btn btn-default btn-xs" onclick="guarda_sesion_factura_contab('{{$value->id}}');" style="background-color: cyan;"><i class="fa fa-plus"></i></a>
                            @endif    
                          @endif   
                        @endif
                      @endif
                    @endif    
                    {{$value->id}}
                  </td>
                  <td style="font-size: 11px;">{{substr($value->fecha_orden,0,10)}}</td>
                  <td>{{$value->toma_muestra}}</td>
                  <td style="font-size: 11px;">{{$value->papellido1}} @if($value->papellido2=='N/A'||$value->papellido2=='(N/A)') @else{{ $value->papellido2 }} @endif {{$value->pnombre1}} @if($value->pnombre2=='N/A'||$value->pnombre2=='(N/A)') @else{{ $value->pnombre2 }} @endif </td>
                  <td style="font-size: 11px;">{{$value->snombre}} / {{$value->nombre_corto}}</td>
                  <td>
                    @if($value->estado=='-1')
                      @if($value->stipo!='0')
                        <span class="label pull-right bg-red" style="font-size: 10px">Cotiz.</span>
                      @else
                        <span class="label pull-right bg-red" style="font-size: 10px">{{$value->pre_post}}-Pendiente</span>
                      @endif 
                    @else  
                      @if($value->estado_pago =='1')
                        <span class="label pull-right bg-green" style="font-size: 10px">Pag. @if($value->pago_online=='1')Online @endif</span>
                      @endif
                      @if($value->pre_post!=null)
                        <span class="label pull-right bg-primary" style="font-size: 10px">{{$value->pre_post}}</span>
                      @endif
                    @endif
                  </td>
                  <td style="font-size: 11px;@if($user->id_tipo_usuario =='3') color: red; @endif">{{substr($value->cnombre1,0,1)}}{{$value->capellido1}}</td>
                  <td style="font-size: 11px;">{{substr($value->mnombre1,0,1)}}{{$value->mapellido1}}</td>
                  <td>{{$value->cantidad}}</td>
                  <td>{{$value->total_valor}}</td>
                  <td>
                    <div class="progress progress">
                      <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" id="td{{$value->id}}">
                        <span id="sp{{$value->id}}" style="color: black;"></span>
                      </div>
                    </div>
                  </td>
                  <td>
                    @if($value->estado=='1')
                    <div class="col-md-12" style="padding-left: 0px">
                      <div class="btn-group">
                        <button type="button" class="btn btn-success btn-xs" id="result{{$value->id}}" onclick="descargar({{$value->id}});"><span style=" font-size: 10px">Resultados</span></button>
                        <button type="button" class="btn btn-success btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false" style="padding-left: 3px;padding-right: 3px">
                          <span class="caret"></span>
                          <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu" role="menu" style="background-color: #00a65a;padding: 2px;min-width: 80px;">
                          <li ><a  href="{{ route('resultados.imprimir3',['id' => $value->id]) }}" ><span style=" font-size: 10px">Formato Gastro</span></a></li>
                          <li ><a  href="javascript:ver_pendientes({{$value->id}})" ><span style=" font-size: 10px">Pendientes</span></a></li>
                        </ul>
                      </div>
                    </div>
                    @endif  
                  </td>
                  <td>
                    @if($value->stipo!='0')
                      <div class="col-md-12" style="padding-left: 0px"> 
                        <div class="btn-group" >
                            <button type="button" class="btn btn-info btn-xs" 
                            onclick="window.open('{{ route('cotizador.imprimir', ['id' => $value->id]) }}','_blank');"><span style=" font-size: 9px">COTIZ</span></button>
                            <button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false"  style="padding-left: 2px;padding-right: 2px">
                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu cot" role="menu" style="background-color: #00c0ef;padding: 2px;min-width: 80px;">
                              <li ><a  href="{{ route('cotizador.imprimir_gastro', ['id' => $value->id]) }}" target="_blank"><span style=" font-size: 10px">Formato Gastro</span></a></li>
                              <li ><a  href="{{ route('cotizador.imprimir_orden', ['id' => $value->id]) }}" target="_blank"><span style=" font-size: 10px">Orden</span></a></li>
                              <li ><a  href="{{ route('pdf_tributario', ['id' => $value->id]) }}" target="_blank"><span style=" font-size: 10px">Comprobante</span></a></li>
                              <li ><a  href="{{ route('pdf_cotizacion', ['id' => $value->id]) }}" target="_blank"><span style=" font-size: 10px">Recibo de Cobro</span></a></li>
                               <li ><a class="hovers" onclick="window.open('{{ route('tiempos.imprimir', ['id' => $value->id]) }}','_blank');" target="_blank"><span style=" font-size: 10px" class="hovers">Tiempos</span></a></li>
                            </ul>
                        </div>
                      </div>
                    @else
                    <div class="col-md-12" style="padding-left: 0px"> 
                        <div class="btn-group" >
                            <button type="button" class="btn btn-info btn-xs"
                            onclick="window.open('{{ route('orden.descargar', ['id' => $value->id]) }}','_blank');"
                            ><span style=" font-size: 9px">Orden</span></button>
                            <button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false"  style="padding-left: 2px;padding-right: 2px">
                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu cot" role="menu" style="background-color: #00c0ef;padding: 2px;min-width: 80px;">
                              <li ><a class="hovers" onclick="window.open('{{ route('tiempos.imprimir', ['id' => $value->id]) }}','_blank');" target="_blank"><span style=" font-size: 10px" class="hovers">Tiempos</span></a></li>
                            </ul>
                        </div>
                      </div>
                      <!--<div class="col-md-3" style="padding-left: 0px">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('orden.descargar', ['id' => $value->id]) }}" target="_blank" class="btn btn-block btn-info btn-xs" style="padding: 0px;">
                        <span class="glyphicon glyphicon-arrow-down">sdfsdfsd</span>
                        </a>   
                      </div>-->
                      @php 
                        $agendas = Sis_medico\Examen_Orden_Agenda::where('id_orden',$value->id)->get();
                      @endphp 
                      @if($agendas->count()=='1') 
                      <div class="col-md-3" style="padding-left: 0px">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('agenda.edit2', ['id' => $agendas->first()->id_agenda, 'doctor' => '4444444444']) }}" target="_blank" class="btn btn-block btn-info btn-xs" style="padding: 0px;">
                        <span class="glyphicon glyphicon-calendar"></span>
                        </a>   
                      </div>
                      @endif
                        
                    @endif   
                  </td>
                  <td>
                    
                    @if($value->stipo!='0')
                      @if($value->realizado=='0' && $value->estado_pago =='0')
                      <div class="col-md-3" style="padding: 3px;">
                        
                        <a href="{{ route('cotizador.editar', ['id' => $value->id]) }}" class="btn btn-block btn-warning btn-xs" style="padding: 0px;">
                        <!--a href="{{ route('orden.edit2', ['id' => $value->id,'dir' => 'rec']) }}" class="btn btn-block btn-warning btn-xs" style="padding: 0px;"-->
                        <span class="glyphicon glyphicon-edit"></span> 
                        </a>  
                      </div>
                      @endif  

                    @else
                      @if($value->realizado=='0')
                        <div class="col-md-3" style="padding: 3px;">
                          
                          <a href="{{ route('orden.edit', ['id' => $value->id]) }}" class="btn btn-block btn-warning btn-xs" style="padding: 0px;">
                          <span class="glyphicon glyphicon-edit"></span>
                          </a>  
                        </div>
                      @endif  
                        
                    @endif 
                    @if($value->realizado=='0' && $value->estado_pago =='0')
                        <div class="col-md-2" style="padding: 3px;">
                          
                          <a href="{{ route('orden.eliminar',['id' => $value->id]) }}" class="btn btn-block btn-danger btn-xs" >
                          <span class="glyphicon glyphicon-trash"></span>
                          </a>  
                        </div>
                    @endif
                    <!--Editar Modal Pago-->
                    @if($value->stipo!='0')
                      @if($value->estado=='1')
                        @if($value->estado_pago =='0')
                          <div class="col-md-3" style="padding: 1px;">
                            <a data-toggle="modal" data-target="#pago" data-remote="{{route('modal.pago_paciente', ['id_paciente' => $value->id_paciente,'id_exa_orden' => $value->id])}}" class="btn btn-danger btn-xs">Pago
                            </a>
                          </div>
                        @endif
                      @endif
                    @endif
                    
                    @if(in_array($rolUsuario, array(1)) == true)
                      @if($value->estado_pago =='1')
                        @if($pac->id == $pac->id_usuario)
                        <div class="col-md-3" style="padding: 3px;">
                          <a data-toggle="modal" data-target="#reseteo_clave" data-remote="{{route('paciente_reseteo_clave', ['id_paciente' => $value->id_paciente,'id_exa_orden' => $value->id])}}" class="btn btn-block btn-warning btn-xs" style="padding: 0px;">
                            <span ></span>R
                          </a>  
                        </div>
                        @endif 
                      @endif
                    @endif
                    @if($value->estado_pago =='1')
                      <div class="col-md-3" style="padding: 3px;">
                        <a data-toggle="modal" data-target="#reenvio_email" data-remote="{{route('paciente_reenvio_email', ['id_paciente' => $value->id_paciente,'id_exa_orden' => $value->id])}}" class="btn btn-warning  btn-xs">
                          <span class="glyphicon glyphicon-envelope"></span>
                        </a>
                      </div>
                    @endif
                    
                    @if($value->stipo!='0')
                      @if($value->estado=='1')
                        
                          @if($value->fecha_envio!=null)
                            <div class="col-md-4 ">
                              <a class="btn btn-primary btn-xs" href="{{ route('ventas.comprobante_publico', ['comprobante' => $value->comprobante, 'id_empresa' => '0993075000001', 'tipo' => 'pdf']) }}">RIDE</a>
                            </div>
                          @else
                         <div class="col-md-4 ">
                            <button class="btn btn-danger btn-xs" onclick="emitir_sri('{{$value->id}}')">SRI</button>
                          </div> 
                           
                          @endif
                        
                      @endif
                    @endif
                  </td>          
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-5">
            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1+($ordenes->currentPage()-1)*$ordenes->perPage()}}  / @if(($ordenes->currentPage()*$ordenes->perPage())<$ordenes->total()){{($ordenes->currentPage()*$ordenes->perPage())}} @else {{$ordenes->total()}} @endif de {{$ordenes->total()}} registros</div>
          </div>
          <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
              {{ $ordenes->appends(Request::only(['fecha', 'fecha_hasta', 'nombres', 'seguro', 'facturadas']))->links()}}
            </div>
          </div>
        </div>
      </div>
    </div>
  <!-- /.box-body -->
  </div>
</section>
    <!-- /.content -->
  
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>

<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset ('plugins/sweetalert2_6_11/sweetalert2.js') }}"></script>
<script src="{{ asset ('plugins/sweetalert2_6_11/sweetalert2.all.min.js') }}"></script>

<script type="text/javascript">

  $('#pago').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });

  $('#reenvio_email').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });

  $('#reseteo_clave').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });

  $('#reseteo_clave').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });

  $('#pago_online').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });

  $('#gestionar_orden').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });

  $('#modal_agrupada').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });


  $(document).ready(function($){

    @foreach ($ordenes as $value)

      $.ajax({
        type: 'get',
        url:"{{ route('resultados.puede_imprimir',['id' => $value->id]) }}", 
        
        success: function(data){
          
            if(data.cant_par==0){
              var pct = 0;  
            }else{
              var pct = data.certificados/data.cant_par*100;  
            }
            //alert(pct);
            $('#td{{$value->id}}').css("width", Math.round(pct)+"%");
            $('#sp{{$value->id}}').text(Math.round(pct)+"%");
            if(pct < 10){
              $('#td{{$value->id}}').addClass("progress-bar-danger");
              $('#result{{$value->id}}').removeClass("btn-success");
              $('#result{{$value->id}}').addClass("btn-danger");
            }else if(pct >=10 && pct<90){
              $('#td{{$value->id}}').addClass("progress-bar-warning");
              $('#result{{$value->id}}').removeClass("btn-success");
              $('#result{{$value->id}}').addClass("btn-warning");  
            }else{
              $('#td{{$value->id}}').addClass("progress-bar-success");
            }
          

        },


        error: function(data){
          
           
        }
      });

    @endforeach

    $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',
            
            
            defaultDate: '{{$fecha}}',
            
            });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY/MM/DD',
            
            
            defaultDate: '{{$fecha_hasta}}',
            
            });
        $("#fecha").on("dp.change", function (e) {
            buscar();
        });

         $("#fecha_hasta").on("dp.change", function (e) {
            buscar();
        });

    $(".breadcrumb").append('<li class="active">Órdenes</li>');

    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      'order'       : [[ 1, "desc" ]]
    })



  });

   $('#doctor').on('hidden.bs.modal', function(){
                location.reload();
                $(this).removeData('bs.modal');
            }); 
   $('#modal_datosfacturas').on('hidden.bs.modal', function(){
                location.reload();
                $(this).removeData('bs.modal');
            }); 


  function buscar()
  {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }
  function descargar(id_or){
    var cert = $('#sp'+id_or).text();
    if(cert=='0%'){
      alert("Sin Exámenes Ingresados");
    }else{
      //location.href = '{{url('resultados/imprimir')}}/'+id_or;
      window.open('{{url('resultados/imprimir')}}/'+id_or,'_blank');  
    }
    
  }
  function emitir_sri(id_orden){
    $.ajax({
      type: 'get',
      url:"{{url('facturacion_labs/info_factura')}}/"+id_orden,
      datatype: 'json',
      success: function(data){
        $('#datos_factura').empty().html(data);
        $('#modal_datosfacturas').modal();
      },
      error: function(data){
        //console.log(data);
      }
    });   
  } 
  function sendInvoice(id_orden){
    $.ajax({
      type: 'get',
      url:"{{url('contable/cierre_caja/recibo/')}}/"+id_orden,
      datatype: 'json',
      success: function(data){
        $('#datosrecibo').empty().html(data);
        $('#modalrecibo').modal();
      },
      error: function(data){
        //console.log(data);
      }
    });  
  }

  function ver_pendientes(id_orden){
    $.ajax({
      type: 'get',
      url:"{{url('examenes_pendientes')}}/"+id_orden,
      datatype: 'json',
      success: function(data){
        
        $('#div_examenes_pendientes').empty().html(data);
        $('#modal_examenes').modal();
      },
      error: function(data){
        //console.log(data);
      }
    });   
  } 

  function factura_agrupada(){
    $.ajax({
      type: 'get',
      url:"{{url('facturacion_labs/modal_factura_agrupada')}}",
      datatype: 'json',
      success: function(data){
        $('#datos_agrupada').empty().html(data);
        $('#modal_agrupada').modal();
      },
      error: function(data){
        //console.log(data);
      }
    }); 

  }

  function eliminar(){
      //alert("hola");
      $.ajax({
            type: 'post',
            url: "{{ url('facturacion_labs/factura_agrup/eliminar_sesion')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
      success: function(data){
          //alert("sucess");
          if(data=='ok'){
            factura_agrupada();
          };
            
      },
      error:  function(){
        alert('error al cargar');
      }
    });
  }

  function guarda_sesion_factura(id_orden){
    $.ajax({
      type: 'get',
      url:"{{url('facturacion_labs/añadir_factura')}}/"+id_orden,
      datatype: 'json',
      success: function(data){
        if(data=='ok'){
          $("#btn"+id_orden).css("background-color", "green"); 
        }else{
          alert("No se puede agregar la orden, seguro o nivel diferente");
        }   
      },
      error: function(data){
        alert("Error no se pudo agregar orden");
      }
    }); 
  }
  
  function guarda_sesion_factura_contab(id_orden){
    $.ajax({
      type: 'get',
      url:"{{url('facturacion_labs/añadir_factura/contabilidad')}}/"+id_orden,
      datatype: 'json',
      success: function(data){
        if(data=='ok'){
          $("#btn_contab"+id_orden).css("background-color", "green"); 
        }  
      },
      error: function(data){
        alert("Error no se pudo agregar orden");
      }
    }); 
  }

  function datos_factura_agrupada(){
      //alert(cuenta);
      $.ajax({
            type: 'get',
            url: "{{ url('facturacion_labs/datos_factura_agrupada')}}",
            datatype: 'json',
      success: function(datahtml){
          //alert("sucess");
          $("#datos_factura_agrup").empty().html(datahtml);
            
      },
      error:  function(){
        alert('error al cargar');
      }
    });
    }
  
</script>  

@endsection