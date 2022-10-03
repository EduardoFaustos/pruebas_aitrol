@extends('contable.nuevo_rol_pago.base')
@section('action-content')

<!-- Ventana modal Rol Pago-->
<div class="modal fade" id="modal_rol_pago" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<div class="modal fade" id="visualizar_estado" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="col-md-7">@php $txt_mes = ['', 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE']; @endphp
        <h5><b>CUENTAS DE LOS ROLES DEL PERIODO {{ $cuentas_rol->anio }} - {{ $txt_mes[$cuentas_rol->mes] }}</b></h5>
      </div>
          
      <div class="col-md-1 text-right">
        <button  onclick="goBack()" class="btn btn-default btn-gray">
          <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
        </button>
      </div>
    </div>
    <div class="box-body dobra">
      <div class="row head-title">
        <div class="col-md-12 cabecera">
          <label class="color_texto" >CUENTAS DEL ROL</label>
        </div>
      </div>
      <div class="box-body dobra">
        <div class="form-group col-md-12">
          <div class="form-row">
            <div id="contenedor">
              <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                <div class="row">
                  @if($cuentas_rol->id_asiento != null)
                  <div class="col-md-8 alert-success" >
                    ASIENTOS YA GENERADOS         
                  </div>
                  @endif
                  <div class="table-responsive col-md-12">
                    <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info" style="width: 100%">
                      <thead>
                        <tr class='well-dark'>
                          <th width="20%">{{trans('contableM.Cuenta')}}</th>
                          <th width="20%" >Item</th>
                          <th width="20%" >Debe</th>
                          <th width="20%" >Haber</th>
                          <th width="20%" style="padding-left: 10px;" >{{trans('contableM.accion')}}</th>
                        </tr>
                      </thead>
                      <tbody>@php  $debe = 0;$haber = 0;  @endphp
                        @foreach ($cuentas as $value)
                          @php  
                            $debe += $value->debe; $haber += $value->haber; 
                            $plan_empresa = Sis_medico\Plan_Cuentas_Empresa::where('id_plan', $value->id_plan_cuentas)->where('id_empresa',$cuentas_rol->id_empresa)->first();
                          @endphp
                          <tr>
                            <td>{{ $plan_empresa->plan }}</td>
                            <td>{{ $value->item }}</td>
                            <td style="text-align: right;">$ {{ number_format($value->debe, 2, ',', ' ') }}</td>
                            <td style="text-align: right;">$ {{ number_format($value->haber, 2, ',', ' ') }}</td>
                            <td></td>
                          </tr>
                        @endforeach
                      </tbody>
                      <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="text-align: right;">$ {{ number_format($debe, 2, ',', ' ') }}</td>
                            <td style="text-align: right;">$ {{ number_format($haber, 2, ',', ' ') }}</td>
                            <td></td>
                          </tr>
                      </tfoot>

                    </table>
                  </div>
                </div>
                <div class="row">
                  @if($cuentas_rol->id_asiento == null)
                  <button type="button" onclick="generar_asiento()" class="btn btn-warning">Generar Asientos</button>
                  @else
                  <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',['id' => $cuentas_rol->id_asiento ])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
                      <i class="glyphicon glyphicon-eye-open"  aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.visualizarasiento')}}
                  </a>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      @if($cuentas_rol->id_asiento != null)
      <div class="row head-title">
        <div class="col-md-12 cabecera">
          <label class="color_texto" >PAGO DE SUELDOS Y SALARIOS</label>
        </div>
      </div>
      <div class="box-body dobra">
        <div class="form-group col-md-12">
          <div class="form-row">
            <div id="contenedor">
              <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                <div class="row">
                  <form class="form-horizontal" role="form" id="form_pagos_roles">
                    {{ csrf_field() }}
                    <div class="col-md-12">
                        <input type="hidden" name="rol_asientos" id="rol_asientos" value="{{$cuentas_rol->id}}">
                        
                        <div class="form-group col-md-4 col-xs-3">
                            <label for="fecha_creacion" class="col-md-4 texto">{{trans('contableM.fecha')}}</label>
                            <div class="col-md-7">
                                <input id="fecha_creacion" type="date" class="form-control validar" name="fecha_creacion" value="" required autofocus>
                            </div>
                        </div>

                        <div class="form-group col-md-4 col-xs-5">
                            <label for="tipo_pago" class="col-md-3 texto">Tipo de Pago</label>
                            <div class="col-md-8">
                                <select class="form-control validar" id="tipo_pago" name="tipo_pago" onchange="obtener_seleccion()">
                                    <option value="">Seleccione...</option>
                                    @foreach($tipo_pago_rol as $value)
                                    <option value="{{$value->id}}">{{$value->tipo}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Numero de Cuenta Beneficiario Cobra-->
                        <div id="num_cuenta" class="form-group col-md-4 col-xs-4">
                            <label for="numero_cuenta" class="col-md-4 texto ">N# Cuenta</label>
                            <div class="col-md-8">
                                <input id="numero_cuenta" type="text" class="form-control" name="numero_cuenta" value="" autocomplete="off">
                            </div>
                        </div>

                        
                        <!--Banco Beneficiario Cobra-->
                        <div id="id_banco" class="form-group col-md-4 col-xs-4">
                            <label for="banco" class="col-md-4 texto">{{trans('contableM.banco')}}</label>
                            <div class="col-md-8">
                                <select class="form-control " id="banco" name="banco">
                                    <option value="">Seleccione...</option>
                                    @foreach($lista_banco as $value)
                                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!--Fecha_Cheque-->
                        <div id="fech_cheq" class="form-group col-md-4 col-xs-4">
                            <label for="fecha_cheque" class="col-md-4 texto">{{trans('contableM.fechacheque')}}:</label>
                            <div class="col-md-8">
                                <input id="fecha_cheque" type="date" class="form-control " name="fecha_cheque" value="{{ old('fecha_cheque') }}" required autofocus>
                            </div>
                        </div>
                        <!--Numero de Cheque-->
                        <div id="num_che" class="form-group col-md-4 col-xs-4">
                            <label for="numero_cheque" class="col-md-4 texto">N # Cheque:</label>
                            <div class="col-md-8">
                                <input id="numero_cheque" type="text" class="form-control " name="numero_cheque" value="{{ old('numero_cheque') }}" onkeypress="return isNumberKey(event)" autocomplete="off">
                            </div>
                        </div>
                        <!--Cuenta Saliente Paga-->
                        <div id="id_cuenta_saliente" class="form-group col-md-4 col-xs-4">
                            <label for="cuenta_saliente" class="col-md-4 texto">Cuent Saliente</label>
                            <div class="col-md-8">
                                <select class="form-control " id="cuenta_saliente" name="cuenta_saliente">
                                    <option value="">Seleccione...</option>
                                    @foreach($bancos as $value)
                                    <option value="{{$value->cuenta_mayor}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12" ><br></div>
                    <div class="col-md-12" id="tabla_detalle">
                        <div class="table-responsive col-md-12">
                            Todos <input type="checkbox" name="ctodos" id="ctodos" onclick="sel_todos()" >
                            <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info" style="width: 100%">
                                <thead>
                                    <tr class='well-dark'>
                                        <th width="10%">Sel.</th>
                                        <th width="10%">{{trans('contableM.id')}}</th>
                                        <th width="20%">{{trans('contableM.identificacion')}}</th>
                                        <th width="30%">Nombres</th>
                                        <th width="15%">SUELDOS Y SALARIOS</th>
                                        <th width="15%">{{trans('contableM.accion')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $acum = 0; @endphp
                                    @foreach($roles as $rol)
                                    @php 
                                      $detalle = $rol->detalle->first();
                                      $acum   += $detalle->neto_recibido;
                                    @endphp
                                    <tr>
                                        <td>@if($rol->id_asiento_pago == null)<input type="checkbox" name="roles[]" value="{{$rol->id}}">@endif</td> 
                                        <td>{{$rol->id}}</td>
                                        <td>{{$rol->id_user}}</td>
                                        <td>{{$rol->ct_nomina->nombres}} </td>
                                        <td style="text-align: right;">$ {{number_format($detalle->neto_recibido,2, '.', '')}}</td>
                                        <td>
                                          @if($rol->id_asiento_pago != null)
                                            <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',['id' => $rol->id_asiento_pago ])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
                                              Asiento
                                            </a>
                                          @endif    
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td><b> Total Registros:</b></td>
                                        <td>{{ $roles->count() }}</td>
                                        <td></td>
                                        <td><b>{{trans('contableM.total')}}</b></td>
                                        <td style="text-align: right;">$ {{number_format($acum,2, '.', '')}}</td>
                                        <td>
                                          
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="form-group col-md-12">
                        <a class="btn btn-info" name="boton_guardar" id="boton_guardar" onclick="guardar_pago(event);">Generar Pago</a>
                    </div>

                  </form>
                  
                </div>
                
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row head-title">
        <div class="col-md-12 cabecera">
          <label class="color_texto" >ASIENTOS DE APORTE PATRONAL Y SECAP</label>
        </div>
      </div>
      <div class="box-body dobra">
        <div class="form-group col-md-12">
          <div class="form-row">
            <div id="contenedor">
              <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                <div class="row">
                  @if($cuentas_rol->id_asiento_aporte != null)
                  <div class="col-md-8 alert-success" >
                    ASIENTOS YA GENERADOS         
                  </div>
                  @endif
                  <div class="table-responsive col-md-12">
                    <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info" style="width: 100%">
                      <thead>
                        <tr class='well-dark'>
                          <th width="20%">{{trans('contableM.Cuenta')}}</th>
                          <th width="20%" >Item</th>
                          <th width="20%">{{trans('contableM.valor')}}</th>
                          <th width="20%" style="padding-left: 10px;" >{{trans('contableM.accion')}}</th>
                        </tr>
                      </thead>
                      <tbody>@php  $debe = 0;$haber = 0;  @endphp
                        @foreach ($cuentas_iess as $value)
                          @php  
                            $debe += $value->debe; $haber += $value->haber; 
                            $plan_empresa = Sis_medico\Plan_Cuentas_Empresa::where('id_plan', $value->id_plan_cuentas)->where('id_empresa',$cuentas_rol->id_empresa)->first();
                          @endphp
                          <tr>
                            <td>{{ $plan_empresa->plan }}</td>
                            <td>{{ $value->item }}</td>
                            <td style="text-align: right;">$ {{ number_format($value->debe, 2, ',', ' ') }}</td>
                            <td></td>
                          </tr>
                        @endforeach
                      </tbody>
                      <tfoot>
                        <tr>
                          <td></td>
                          <td></td>
                          <td style="text-align: right;">$ {{ number_format($debe, 2, ',', ' ') }}</td>
                          <td></td>
                        </tr>
                        <tr>
                          <td style="text-align: right;">APORTE PATRONAL</td>
                          <td style="text-align: right;">{{ number_format($aporte_patronal, 2, ',', ' ') }}</td>
                          <td style="text-align: right;">$ {{ number_format($valor_aporte, 2, ',', ' ') }}</td>
                          <td></td>
                        </tr>
                        <tr>
                          <td style="text-align: right;">APORTE IECE - SECAP</td>
                          <td style="text-align: right;"> {{ number_format($secap, 2, ',', ' ') }}</td>
                          <td style="text-align: right;">$ {{ number_format($valor_secap, 2, ',', ' ') }}</td>
                          <td></td>
                        </tr>
                      </tfoot>

                    </table>
                    <form class="form-horizontal" role="form" id="form_pago_aporte">
                      {{ csrf_field() }}
                      <div class="col-md-12">
                          <input type="hidden" name="rol_asientos_aporte" id="rol_asientos_aporte" value="{{$cuentas_rol->id}}">

                          <input type="hidden" name="total_aporte" id="total_aporte" value="{{$total_aporte}}">
                          <input type="hidden" name="p_patronal" id="p_patronal" value="{{$aporte_patronal}}">
                          <input type="hidden" name="p_secap" id="p_secap" value="{{$secap}}">
                          <input type="hidden" name="aporte_patronal" id="aporte_patronal" value="{{$valor_aporte}}">
                          <input type="hidden" name="aporte_secap" id="aporte_secap" value="{{$valor_secap}}">
                          
                          <div class="form-group col-md-4 col-xs-3">
                              <label for="fecha_asiento_aporte" class="col-md-4 texto">{{trans('contableM.fecha')}}</label>
                              <div class="col-md-7">
                                  <input id="fecha_asiento_aporte" type="date" class="form-control validar" name="fecha_asiento_aporte" value="" required autofocus>
                              </div>
                          </div>

                      </div>
                      
                    </form>
                  </div>
                </div>
                <div class="row">
                  @if($cuentas_rol->id_asiento_aporte == null)
                  <button type="button" onclick="generar_asiento_aporte()" class="btn btn-warning">Generar Asientos</button>
                  @else
                  <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',['id' => $cuentas_rol->id_asiento_aporte ])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
                      <i class="glyphicon glyphicon-eye-open"  aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.visualizarasiento')}}
                  </a>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      @if($cuentas_rol->id_asiento_aporte != null)
      <div class="row head-title">
        <div class="col-md-12 cabecera">
          <label class="color_texto" >PAGO DE PLANILLAS</label>
        </div>
      </div>
      <div class="box-body dobra">
       
          <div class="form-row">
            <div id="contenedor">
                  @if($cuentas_rol->id_asiento_aporte != null)
                  <div class="col-md-8 alert-success" >
                    ASIENTOS YA GENERADOS         
                  </div>
                  @endif
                
                  <form class="form-horizontal" role="form" id="form_pagos_planillas">
                    {{ csrf_field() }}
                    <div class="col-md-12">
                        <input type="hidden" name="rol_planillas" id="rol_planillas" value="{{$cuentas_rol->id}}">
                        
                        <div class="form-group col-md-4 col-xs-3">
                            <label for="fecha_planilla" class="col-md-4 texto">{{trans('contableM.fecha')}}</label>
                            <div class="col-md-7">
                                <input id="fecha_planilla" type="date" class="form-control validar" name="fecha_planilla" value="" required autofocus>
                            </div>
                        </div>

                        <div class="form-group col-md-4 col-xs-5">
                            <label for="id_tipo_pago_planilla" class="col-md-3 texto">Tipo de Pago</label>
                            <div class="col-md-8">
                                <select class="form-control validar" id="id_tipo_pago_planilla" name="id_tipo_pago_planilla" onchange="obtener_seleccion_planilla()">
                                    <option value="">Seleccione...</option>
                                    @foreach($tipo_pago_rol as $value)
                                    <option value="{{$value->id}}">{{$value->tipo}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Numero de Cuenta Beneficiario Cobra-->
                        <div id="pnum_cuenta" class="form-group col-md-4 col-xs-4">
                            <label for="numero_cuenta_planilla" class="col-md-4 texto ">N# Cuenta</label>
                            <div class="col-md-8">
                                <input id="numero_cuenta_planilla" type="text" class="form-control" name="numero_cuenta_planilla" value="" autocomplete="off">
                            </div>
                        </div>

                        
                        <!--Banco Beneficiario Cobra-->
                        <div id="pid_banco" class="form-group col-md-4 col-xs-4">
                            <label for="id_banco_planilla" class="col-md-4 texto">{{trans('contableM.banco')}}</label>
                            <div class="col-md-8">
                                <select class="form-control " id="id_banco_planilla" name="id_banco_planilla">
                                    <option value="">Seleccione...</option>
                                    @foreach($lista_banco as $value)
                                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!--Fecha_Cheque-->
                        <div id="pfech_cheq" class="form-group col-md-4 col-xs-4">
                            <label for="fecha_cheque_planilla" class="col-md-4 texto">{{trans('contableM.fechacheque')}}:</label>
                            <div class="col-md-8">
                                <input id="fecha_cheque_planilla" type="date" class="form-control " name="fecha_cheque_planilla" value="{{ old('fecha_cheque') }}" required autofocus>
                            </div>
                        </div>
                        <!--Numero de Cheque-->
                        <div id="pnum_che" class="form-group col-md-4 col-xs-4">
                            <label for="numero_cheque_planilla" class="col-md-4 texto">N # Cheque:</label>
                            <div class="col-md-8">
                                <input id="numero_cheque_planilla" type="text" class="form-control " name="numero_cheque_planilla" value="{{ old('numero_cheque') }}" onkeypress="return isNumberKey(event)" autocomplete="off">
                            </div>
                        </div>
                        <!--Cuenta Saliente Paga-->
                        <div id="pid_cuenta_saliente" class="form-group col-md-4 col-xs-4">
                            <label for="id_cuenta_planilla" class="col-md-4 texto">Cuent Saliente</label>
                            <div class="col-md-8">
                                <select class="form-control " id="id_cuenta_planilla" name="id_cuenta_planilla">
                                    <option value="">Seleccione...</option>
                                    @foreach($bancos as $value)
                                    <option value="{{$value->cuenta_mayor}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12" ><br></div>
                    <div class="col-md-12" id="tabla_detalle_plantilla">
                        <div class="table-responsive col-md-12">
                          <div class="table-responsive col-md-12">
                            <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info" style="width: 100%">
                              <thead>
                                <tr class='well-dark'>
                                  <!--th width="20%">{{trans('contableM.Cuenta')}}</th-->
                                  <th width="20%" >Item</th>
                                  <th width="20%">{{trans('contableM.valor')}}</th>
                                  <th width="20%" style="padding-left: 10px;" >{{trans('contableM.accion')}}</th>
                                </tr>
                              </thead>
                              <tbody>@php  $debe = 0;$haber = 0;$planilla = 0;  @endphp
                                @foreach ($cuentas_iess2 as $value)
                                  @php  
                                    $debe += $value->debe; $haber += $value->haber; 
                                    $plan_empresa = Sis_medico\Plan_Cuentas_Empresa::where('id_plan', $value->id_plan_cuentas)->where('id_empresa',$cuentas_rol->id_empresa)->first();
                                  @endphp
                                  <tr>
                                    <!--td>{{ $plan_empresa->plan }}</td-->
                                    <td>{{ $value->item }}</td>
                                    <td style="text-align: right;">$ {{ number_format($value->haber, 2, ',', ' ') }}</td>
                                    <td></td>
                                  </tr>
                                @endforeach
                                @php 
                                  $planilla += $haber; 

                                @endphp
                                <tr>
                                    <td>APORTE PATRONAL 11.15%</td>
                                    <td style="text-align: right;">$ {{ number_format($cuentas_rol->aporte_patronal, 2, ',', ' ') }}</td>
                                    <td></td>
                                  </tr>
                                  <tr>
                                    <td>APORTE SECAP 1%</td>
                                    <td style="text-align: right;">$ {{ number_format($cuentas_rol->aporte_secap, 2, ',', ' ') }}</td>
                                    <td></td>
                                  </tr>
                              </tbody>
                              <tfoot>
                                <tr>@php $planilla = $planilla + $cuentas_rol->aporte_patronal + $cuentas_rol->aporte_secap @endphp
                                  <td></td>
                                  <td style="text-align: right;">$ {{ number_format($planilla, 2, ',', ' ') }}</td>
                                  <td></td>
                                </tr>
                              </tfoot>

                            </table>
                    
                          </div> 
                          
                        </div>
                    </div>
                    <input type="hidden" name="total_planilla" id="total_planilla" value="{{$planilla}}">
                    @if($cuentas_rol->id_asiento_planilla == null)
                    <div class="form-group col-md-12">
                        <a class="btn btn-info" name="boton_guardar" id="boton_guardar" onclick="guardar_pago_planilla(event);">Generar Pago</a>
                    </div>
                    @else
                    <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',['id' => $cuentas_rol->id_asiento_planilla ])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
                        <i class="glyphicon glyphicon-eye-open"  aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.visualizarasiento')}}
                    </a>
                    @endif
                    

                  </form>
                  
                
                
              
            </div>
          </div>
       
      </div>
      @endif

      @endif
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
  <script type="text/javascript">

    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : false,
      'info'        : false,
      'autoWidth'   : false
    })

    $('#modal_rol_pago').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
    });

    
    $('#visualizar_estado').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
    });

    function goBack() {
      window.history.back();
    }

    function generar_asiento(){
      var confirmar = confirm("Desea Generar Asiento para el Periodo Actual");
      if(confirmar){
        $.ajax({
          type: 'get',
          url:"{{route('nuevo_rol.generar_asientos',[ 'id' => $cuentas_rol->id ])}}",
          
          datatype: 'json',
          
          success: function(data){
            if(data.msj=='ok'){
              location.reload();
            }
            if(data.msj=='error'){
              alert(data.mensaje);
            }

            
          },
          error: function(data){
            alert("ocurrio un error en el proceso");
          }
        })

      }else{
        location.reload();
      }
    }

    function generar_asiento_aporte(){
      let fecha_asiento = document.getElementById('fecha_asiento_aporte').value;
        

      let msj = "";
      if (fecha_asiento == "") {
          msj += "Seleccione la Fecha de Creacion <br>";
      }

      if (msj != "") {
          alertas('error', 'Error!..', msj)
      } else {
        var confirmar = confirm("Desea Generar Asiento del Aporte Patronal y Secap");
        if(confirmar){
          $.ajax({
                type: 'post',
                url: "{{ route('nuevo_rol.aportes_patronales') }}",
               
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#form_pago_aporte").serialize(),
                success: function(data) {
                    alertas(data.respuesta, data.titulos, data.msj);
                    if(data.respuesta == 'success'){
                        setTimeout(function(){
                            location.reload();
                        }, 1000);
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            });

        }else{
          location.reload();
        }
      }  
    }

    function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
    }

    function guardar_pago(e) {
        e.preventDefault();
        let fecha_asiento = document.getElementById('fecha_creacion').value;
        let tipo_pago = document.getElementById('tipo_pago').value;
        let cuenta_saliente = document.getElementById('cuenta_saliente').value;
        let fecha_cheque = document.getElementById('fecha_cheque').value;
        let numero_cheque = document.getElementById('numero_cheque').value;
        let numero_cuenta = document.getElementById('numero_cuenta').value;
        let banco = document.getElementById('banco').value;

        let msj = "";
        if (fecha_asiento == "") {
            msj += "Seleccione la Fecha de Creacion <br>";
        }
        if (tipo_pago == "") {
            msj += "Seleccione el Tipo de Pago <br>";
        }
        if (cuenta_saliente == "") {
            msj += "Seleccione la Cuenta Saliente <br>";
        }
        if(tipo_pago == '3'){ //CHEQUE
            if (fecha_cheque == "") {
                msj += "Seleccione la Fecha del Cheque <br>";
            }
            if (numero_cheque == "") {
                msj += "Escriba el Numero de Cheque <br>";
            }
        }

        if(tipo_pago == '1'){ //ACREDITACION 
            if (numero_cuenta == "") {
                msj += "Escriba el Numero de Cuenta <br>";
            }
            if (banco == "") {
                msj += "Seleccione el banco <br>";
            }
        }    


        if (msj != "") {
            alertas('error', 'Error!..', msj)
        } else {
          var confirmar = confirm('Desea Realizar el Pago de Sueldos');
          if(confirmar){
            $.ajax({
                type: 'post',
                url: "{{ route('nuevo_rol.pago_de_roles') }}",
               
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#form_pagos_roles").serialize(),
                success: function(data) {
                    alertas(data.respuesta, data.titulos, data.msj);
                    if(data.respuesta == 'success'){
                        setTimeout(function(){
                            location.reload();
                        }, 1000);
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            });
          }
        }
    }

    function guardar_pago_planilla(e) {
        e.preventDefault();
        let fecha_asiento = document.getElementById('fecha_planilla').value;
        let tipo_pago = document.getElementById('id_tipo_pago_planilla').value;
        let cuenta_saliente = document.getElementById('id_cuenta_planilla').value;
        let fecha_cheque = document.getElementById('fecha_cheque_planilla').value;
        let numero_cheque = document.getElementById('numero_cheque_planilla').value;
        let numero_cuenta = document.getElementById('numero_cuenta_planilla').value;
        let banco = document.getElementById('id_banco_planilla').value;

        let msj = "";
        if (fecha_asiento == "") {
            msj += "Seleccione la Fecha de Creacion <br>";
        }
        if (tipo_pago == "") {
            msj += "Seleccione el Tipo de Pago <br>";
        }
        if (cuenta_saliente == "") {
            msj += "Seleccione la Cuenta Saliente <br>";
        }
        if(tipo_pago == '3'){ //CHEQUE
            if (fecha_cheque == "") {
                msj += "Seleccione la Fecha del Cheque <br>";
            }
            if (numero_cheque == "") {
                msj += "Escriba el Numero de Cheque <br>";
            }
        }

        if(tipo_pago == '1'){ //ACREDITACION 
            if (numero_cuenta == "") {
                msj += "Escriba el Numero de Cuenta <br>";
            }
            if (banco == "") {
                msj += "Seleccione el banco <br>";
            }
        }    


        if (msj != "") {
            alertas('error', 'Error!..', msj)
        } else {
          var confirmar = confirm('Desea Realizar el Pago la Planilla');
          if(confirmar){
            $.ajax({
                type: 'post',
                url: "{{ route('nuevo_rol.pago_aportes_patronales') }}",
               
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#form_pagos_planillas").serialize(),
                success: function(data) {
                    alertas(data.respuesta, data.titulos, data.msj);
                    if(data.respuesta == 'success'){
                        setTimeout(function(){
                            location.reload();
                        }, 1000);
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            });
          }
        }
    }

    function obtener_seleccion() {

        var id_tipo = $("#tipo_pago").val();
        if (id_tipo == 1) { //ACREDITACION

            document.getElementById("id_cuenta_saliente").style.display = 'block';
            document.getElementById("id_banco").style.display = 'block';
            document.getElementById("num_cuenta").style.display = 'block';
            document.getElementById("num_che").style.display = 'none';
            document.getElementById("fech_cheq").style.display = 'none';

            $('#banco').val("");
            $('#numero_cuenta').val("");
            $('#numero_cheque').val("");
            $('#fecha_cheque').val("");

        } else if (id_tipo == 2) { //EFECTIVO

            document.getElementById("id_banco").style.display = 'none';
            document.getElementById("num_cuenta").style.display = 'none';
            document.getElementById("num_che").style.display = 'none';
            document.getElementById("fech_cheq").style.display = 'none';

            $('#numero_cheque').val("");
            $('#numero_cuenta').val("");
            $('#banco').val("");
            $('#cuenta_saliente').val("");

        } else if (id_tipo == 3) { //CHEQUE

            document.getElementById("id_banco").style.display = 'none';
            document.getElementById("num_cuenta").style.display = 'none';
            document.getElementById("num_che").style.display = 'block';
            document.getElementById("fech_cheq").style.display = 'block';

            $('#numero_cheque').val("");
            $('#numero_cuenta').val("");
            $('#banco').val("");
            $('#cuenta_saliente').val("");
            $('#fecha_cheque').val("");

        }

    }

    function obtener_seleccion_planilla() {

        var id_tipo = $("#id_tipo_pago_planilla").val();
        if (id_tipo == 1) { //ACREDITACION

            document.getElementById("pid_cuenta_saliente").style.display = 'block';
            document.getElementById("pid_banco").style.display = 'block';
            document.getElementById("pnum_cuenta").style.display = 'block';
            document.getElementById("pnum_che").style.display = 'none';
            document.getElementById("pfech_cheq").style.display = 'none';

            $('#id_banco_planilla').val("");
            $('#numero_cuenta_planilla').val("");
            $('#numero_cheque_planilla').val("");
            $('#fecha_cheque_planilla').val("");

        } else if (id_tipo == 2) { //EFECTIVO

            document.getElementById("pid_banco").style.display = 'none';
            document.getElementById("pnum_cuenta").style.display = 'none';
            document.getElementById("pnum_che").style.display = 'none';
            document.getElementById("pfech_cheq").style.display = 'none';

            $('#numero_cheque_planilla').val("");
            $('#numero_cuenta_planilla').val("");
            $('#id_banco_planilla').val("");
            $('#id_cuenta_planilla').val("");

        } else if (id_tipo == 3) { //CHEQUE

            document.getElementById("pid_banco").style.display = 'none';
            document.getElementById("pnum_cuenta").style.display = 'none';
            document.getElementById("pnum_che").style.display = 'block';
            document.getElementById("pfech_cheq").style.display = 'block';

            $('#numero_cheque_planilla').val("");
            $('#numero_cuenta_planilla').val("");
            $('#id_banco_planilla').val("");
            $('#id_cuenta_planilla').val("");
            $('#fecha_cheque_planilla').val("");

        }

    }

    

    function sel_todos(){
        // Get the checkbox
        var checkBox = document.getElementById("ctodos");

        // If the checkbox is checked, display the output text
        if (checkBox.checked == true){
            $(':checkbox').prop('checked', true);    
        } else {
            $(':checkbox').prop('checked', false);    
        }
    }

  </script>

@endsection