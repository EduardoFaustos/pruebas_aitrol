<div class="row head-title">
  <div class="col-md-12 cabecera">
    <label class="color_texto" >LISTADO ROL PAGO</label>
  </div>
</div>
<div class="box-body dobra">
  <div class="form-group col-md-12">
    <div class="form-row">
      <div id="contenedor">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
          <div class="row">
            <div class="table-responsive col-md-12">
              <table id="example2" class="table table-bordered table-hover">
                <thead>
                  <tr class='well-dark'>
                    <th >Cedula</th>
                    <th >Nombres</th>
                    <th>Sueldo Mensual</th>
                    <th >Dias Laborados</th>
                    <th>Cantidad HR al 50%</th>
                    <th>Horas al 50%</th>
                    <th>Cantidad HR al 100%</th>
                    <th>Horas al 100%</th>
                    <th>Bono</th>
                    <th>Bono Imputable</th>
                    <th>Alimentación</th>
                    <th>Transporte</th>
                    <th>Parqueo</th>
                    <th>Fondo Reserva M/A</th>
                    <th>Prop Fondo Reserva</th>
                    <!--<th>Beneficios Sociales M/A</th>-->
                    <th>Decimo Tercero</th>
                    <th>Valor Decimo III</th>
                    <th>Decimo Cuarto</th>
                    <th>Valor Decimo IV</th>
                    <th>Total Ingreso</th>
                    <th>Aportes 9.45% Iess</th>
                    <th>Multa</th>
                    <th>Fondo Reserva Cobrar Trabajadores</th>
                    <th>Otros Egresos</th>
                    <th>Seguro Médico</th>
                    <th>Examen Laboratorio</th>
                    <th>Impuesto Renta</th>
                    <th>Prestamo Empresa</th>
                    <th>{{trans('contableM.SaldoInicial')}}</th>
                    <th>Anticipo 1RA Quincena</th>
                    <th>Otro Anticipo</th>
                    <th>{{trans('contableM.totalegreso')}}</th>
                    <th>Prestamo Hipotecario</th>
                    <th>Prestamo Quirografario</th>
                    <th>Neto Recibido</th>
                    <th>{{trans('contableM.accion')}}</th>
                  </tr>
                </thead>
                <tbody>
                  @php 
                    $total_sueldo = 0;$total_cantidad_50 = 0;   
                    $total_cantidad_100 = 0;$total_horas_50  = 0;   
                    $total_horas_100 = 0; $total_bono = 0; 
                    $total_bonoimp = 0; $total_exlaboratorio = 0;
                    $total_alimentacion = 0; 
                    $total_transporte = 0;
                    $total_parqueo = 0;
                    $total_fond_reserva = 0;
                    $total_decimo_tercero = 0; $total_decimo_cuarto = 0;
                    $total_ingreso = 0; $total_iess = 0;
                    $total_multa = 0; $total_seguro_privado = 0;$total_reserva_cobrar = 0;$total_otro_egreso = 0;$total_imp_renta = 0; $total_prestamos = 0;$total_sald_ini= 0;
                    $total_anticipos = 0;
                    $total_otro_anticipos = 0;$total_egresos = 0;$total_cuot_quir = 0;$total_cuot_hipot = 0;
                    $total_neto_recibido = 0;
                  @endphp
                  @foreach ($rol_det_consulta as $value)
                    @php
                      $total_sueldo += $value->sueldo;
                      $total_cantidad_50 += $value->cantidad_horas_50;
                      $total_horas_50 += $value->valor_horas_50;
                      $total_cantidad_100 += $value->cantidad_horas_100;
                      $total_horas_100 += $value->valor_horas_100;
                      $total_bono += $value->bonificacion;
                      $total_transporte += $value->transporte;
                      $total_bonoimp += $value->bono_imputable;
                      $total_alimentacion += $value->alimentacion;
                      $total_parqueo += $value->parqueo;
                      $total_fond_reserva += $value->fondo_reserva;
                      $total_decimo_tercero += $value->decimo_tercero;
                      $total_decimo_cuarto += $value->decimo_cuarto;
                      $total_ingreso += $value->total_ingreso;
                      $total_iess += $value->porcentaje_iess;
                      $total_multa += $value->multa;
                      $total_reserva_cobrar += $value->fond_reserv_cobr;
                      $total_otro_egreso += $value->otro_egres;
                      $total_seguro_privado += $value->seguro_privado;
                      $total_exlaboratorio += $value->exam_laboratorio;
                      $total_imp_renta += $value->impuesto_renta;
                      $total_prestamos += $value->prestamo_empleado;
                      $total_sald_ini += $value->saldo_inicial;
                      $total_anticipos += $value->anticipo_quincena;
                      $total_otro_anticipos += $value->otro_anticipo;
                      $total_egresos += $value->total_egreso;
                      $total_cuot_quir += $value->total_quir;
                      $total_cuot_hipot += $value->total_hip;
                      $total_neto_recibido += $value->neto_recibido;
                      $user = Sis_medico\User::find($value->usuario);
                      $dat_nomina = Sis_medico\Ct_Nomina::find($value->id_nomina);
                    @endphp
                    <tr class="well">
                      <td>@if(!is_null($value->usuario)) {{$value->usuario}} @endif</td>
                      <td>{{$user->apellido1}} @if($user->apellido2!='(N/A)'){{$user->apellido2}}@endif {{$user->nombre1}} @if($user->nombre2!='(N/A)'){{$user->nombre2}}@endif</td>
                      <td>@if(!is_null($value->sueldo)){{$value->sueldo}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->dias_laborados)) {{$value->dias_laborados}} @endif</td>
                      <td>@if(!is_null($value->cantidad_horas_50)){{$value->cantidad_horas_50}}@endif</td>
                      <td>@if(!is_null($value->valor_horas_50)){{$value->valor_horas_50}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->cantidad_horas_100)){{$value->cantidad_horas_100}}@endif</td>
                      <td>@if(!is_null($value->valor_horas_100)){{$value->valor_horas_100}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->bonificacion)){{$value->bonificacion}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->bono_imputable)){{$value->bono_imputable}} @else 0.00 @endif</td>
                      
                      <td>@if(!is_null($value->alimentacion)){{$value->alimentacion}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->transporte)){{$value->transporte}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->parqueo)){{$value->parqueo}} @else 0.00 @endif</td>
                      <td>
                          @if($dat_nomina->pago_fondo_reserva == '1') Acumula
                          @elseif($dat_nomina->pago_fondo_reserva == '2') Mensualiza
                          @endif
                      </td>
                      <td>@if(!is_null($value->fondo_reserva)){{$value->fondo_reserva}} @else 0.00 @endif</td>
                      <td>
                          @if($dat_nomina->decimo_tercero == '1') Acumula
                          @elseif($dat_nomina->decimo_tercero == '2') Mensualiza
                          @endif
                      </td>
                      <td>@if(!is_null($value->decimo_tercero)){{$value->decimo_tercero}} @else 0.00 @endif</td>
                      <td>
                          @if($dat_nomina->decimo_cuarto == '1') Acumula
                          @elseif($dat_nomina->decimo_cuarto == '2') Mensualiza
                          @endif
                      </td>
                      <td>@if(!is_null($value->decimo_cuarto)){{$value->decimo_cuarto}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->total_ingreso)){{$value->total_ingreso}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->porcentaje_iess)){{$value->porcentaje_iess}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->multa)){{$value->multa}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->fond_reserv_cobr)){{$value->fond_reserv_cobr}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->otro_egres)){{$value->otro_egres}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->seguro_privado)){{$value->seguro_privado}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->exam_laboratorio)){{$value->exam_laboratorio}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->impuesto_renta)){{$value->impuesto_renta}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->prestamo_empleado)){{$value->prestamo_empleado}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->saldo_inicial)){{$value->saldo_inicial}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->anticipo_quincena)){{$value->anticipo_quincena}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->otro_anticipo)){{$value->otro_anticipo}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->total_egreso)){{$value->total_egreso}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->total_hip)){{$value->total_hip}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->total_quir)){{$value->total_quir}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->neto_recibido)){{$value->neto_recibido}} @else 0.00 @endif</td>
                      <td>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        @if($value->estado_rol == '1')
                          <a href="{{route('rol_pago.editar', ['id' => $value->id_rol])}}" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-eye-open"></span></a>
                          <a href="{{route('rol_pago.imprimir', ['id' => $value->id_rol])}}" target="_blank" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-download-alt"></span></a>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>

                <tfoot>
                  
                </tfoot>
              </table>
              <label style="padding-left: 15px;font-size: 15px">{{trans('contableM.TotalRegistros')}}: {{$rol_det_consulta->count()}}</label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> 
</div>

<script type="text/javascript">
$('#example2').DataTable({
        responsive: true,
        info:false,
        "searching": false
    });
   /* $('#example2').DataTable({
    'paging'      : false,
    'lengthChange': true,
    'searching'   : false,
    'ordering'    : true,
    'info'        : true,
    'autoWidth'   : true,
    'sInfoEmpty':  true,
    'sInfoFiltered': true,
    'language': {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
    }
    });*/

   

</script>