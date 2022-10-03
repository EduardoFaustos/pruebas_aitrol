@extends('contable.rh_prestamos_empleados.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<!-- modal ver prestamos-->
<div class="modal fade" id="modal_ver_prestamos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>
  <!-- Main content -->
  <section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">{{trans('nomina.contable')}}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{trans('nomina.prestamos_empleados')}}</li>
      </ol>
    </nav>
      <div class="box">
        <div class="box-header">
            <div class="col-md-9">
              <h5><b>{{trans('nomina.prestamos_empleados')}}</b></h5>
            </div>
        </div>
        <div class="row head-title">
          <div class="col-md-12 cabecera">
              <label class="color_texto" for="title">{{trans('nomina.buscador')}}</label>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body dobra">
          <form method="POST" id="buscar_prestamo" action="{{ route('prestamo_empleado.search') }}">
            {{ csrf_field() }}
                <div class="form-group col-md-1 col-xs-2">
                    <label class="texto" for="identificacion">{{trans('nomina.identificacion')}}:</label>
                </div>
                <div class="form-group col-md-4 col-xs-10 container-4" style="padding-left: 15px;">
                      <input class="form-control" type="text" id="identificacion" name="identificacion"  placeholder="I{{trans('nomina.identificacion')}}..."  value="@if(isset($searchingVals)){{$searchingVals['id_empl']}}@endif" autocomplete="off" />
                </div>
                <div class="form-group col-md-1 col-xs-2">
                    <label class="texto" for="nombre">{{trans('nomina.nombres')}}:</label>
                </div>
                <div class="form-group col-md-4 col-xs-10 container-4" style="padding-left: 15px;">
                      <input class="form-control" type="text" id="nombre" name="nombre"  placeholder="{{trans('nomina.nombres')}}..."  value="@if(isset($searchingVals)){{$searchingVals['nombres']}}@endif" autocomplete="off" />
                </div>
                <div class="col-xs-2">
                  <button type="submit" id="buscarprestamo" class="btn btn-primary">
                      <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
                  </button>
                </div>
          </form>
        </div>
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" >{{trans('nomina.prestamos')}}</label>
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
                              <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >{{trans('nomina.cedula')}}</th>
                                <!--<th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Empresa</th>-->
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('nomina.empleado')}}</th>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('nomina.monto_prestamo')}}</th>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('nomina.fecha_creacion')}}</th>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('nomina.tipo')}}</th>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('nomina.numero_cuota')}}</th>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('nomina.valor_cuota')}}</th>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('nomina.mes')}}/{{trans('nomina.anio')}} {{trans('nomina.inicio_cobro')}}</th>                        
                                
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('nomina.mes')}}/{{trans('nomina.anio')}} {{trans('nomina.fin_cobro')}}</th>
                                
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('nomina.tipo_pago')}}</th>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('nomina.numero_cuenta_beneficiario')}}</th>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('nomina.banco_beneficiario')}}</th>
                                <!--th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cuenta Empresa</th-->
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('nomina.numero_cheque')}}</th>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.estado')}}</th>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.accion')}}</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach ($registros as $value)

                                @php 
                                  $nom_cuenta  = Sis_medico\Plan_Cuentas::where('id',$value->cuenta_saliente)->first();
                                //  dd($nom_cuenta);

                                  $inf_usuario = Sis_medico\User::where('id',$value->id_empl)->first();
                                @endphp

                                <tr class="well">
                                    <td >@if(!is_null($value->id_empl)){{$value->id_empl}}@endif</td>
                                    <td >{{$inf_usuario->apellido1}}  @if($inf_usuario->apellido2!='(N/A)'){{ $inf_usuario->apellido2 }}@endif {{ $inf_usuario->nombre1 }} @if($inf_usuario->nombre2!='(N/A)'){{$inf_usuario->nombre2}}@endif</td>
                                    <td >@if(!is_null($value->monto_prestamo)){{$value->monto_prestamo}}@endif</td>
                                    <td >@if(!is_null($value->fecha_creacion)){{$value->fecha_creacion}}@endif</td>
                                    <td >@if(!is_null($value->tipo_rol)){{$value->tipo_rol}}@endif</td>
                                    <td >@if(!is_null($value->num_cuotas)){{$value->num_cuotas}}@endif</td>
                                    <td >@if(!is_null($value->valor_cuota)){{$value->valor_cuota}}@endif</td>
                                    <td >@if($value->mes_inicio_cobro == '1') 
                                         {{trans('nomina.enero')}} 
                                       @elseif($value->mes_inicio_cobro == '2') 
                                         {{trans('nomina.febrero')}}
                                       @elseif($value->mes_inicio_cobro == '3')
                                         {{trans('nomina.marzo')}}
                                       @elseif($value->mes_inicio_cobro == '4')
                                         {{trans('nomina.abril')}}
                                       @elseif($value->mes_inicio_cobro == '5')
                                         {{trans('nomina.mayo')}}
                                       @elseif($value->mes_inicio_cobro == '6')
                                         {{trans('nomina.junio')}}
                                       @elseif($value->mes_inicio_cobro == '7')
                                         {{trans('nomina.julio')}}
                                       @elseif($value->mes_inicio_cobro == '8')
                                         {{trans('nomina.agosto')}}
                                       @elseif($value->mes_inicio_cobro == '9')
                                         {{trans('nomina.septiembre')}}
                                       @elseif($value->mes_inicio_cobro == '10')
                                         {{trans('nomina.octubre')}} 
                                       @elseif($value->mes_inicio_cobro == '11') 
                                         {{trans('nomina.noviembre')}}
                                       @elseif($value->mes_inicio_cobro == '12')
                                         {{trans('nomina.diciembre')}}
                                       @endif  

                                       @if(!is_null($value->anio_inicio_cobro)){{$value->anio_inicio_cobro}}@endif
                                    </td>
                                    <td >
                                      @if($value->mes_fin_cobro == '1') 
                                         {{trans('nomina.enero')}} 
                                       @elseif($value->mes_fin_cobro == '2') 
                                         {{trans('nomina.febrero')}}
                                       @elseif($value->mes_fin_cobro == '3')
                                         {{trans('nomina.marzo')}}
                                       @elseif($value->mes_fin_cobro == '4')
                                         {{trans('nomina.abril')}}
                                       @elseif($value->mes_fin_cobro == '5')
                                         {{trans('nomina.mayo')}}
                                       @elseif($value->mes_fin_cobro == '6')
                                         {{trans('nomina.junio')}}
                                       @elseif($value->mes_fin_cobro == '7')
                                         {{trans('nomina.julio')}}
                                       @elseif($value->mes_fin_cobro == '8')
                                         {{trans('nomina.agosto')}}
                                       @elseif($value->mes_fin_cobro == '9')
                                         {{trans('nomina.septiembre')}}
                                       @elseif($value->mes_fin_cobro == '10')
                                         {{trans('nomina.octubre')}} 
                                       @elseif($value->mes_fin_cobro == '11') 
                                         {{trans('nomina.noviembre')}}
                                       @elseif($value->mes_fin_cobro == '12')
                                         {{trans('nomina.diciembre')}}
                                       @endif   

                                       @if(!is_null($value->anio_fin_cobro)){{$value->anio_fin_cobro}}@endif
                                    </td>
                                    <td >
                                       @if($value->id_tipo_pago == '1') 
                                         ACREDITACION 
                                       @elseif($value->id_tipo_pago == '2') 
                                         EFECTIVO
                                       @elseif($value->id_tipo_pago == '3')
                                         CHEQUE
                                       @endif  
                                    </td>
                                    <td >@if(!is_null($value->num_cuenta_benef)){{$value->num_cuenta_benef}}@endif</td>
                                    <td >@if(isset($value->ct_bancos)) {{$value->ct_bancos->nombre}} @endif</td>
                                    @php /*<td >@if(!is_null($nom_cuenta->nombre)) {{$nom_cuenta->nombre}} @endif</td>*/@endphp
                                    <td >
                                      @if(!is_null($value->num_cheque))
                                        {{$value->num_cheque}}
                                      @else
                                        <a onclick="#" class="btn btn-success btn-xs boton_egreso">
                                          <span id="p_egreso" style="font-size: 9px">NO POSEE</span>
                                        </a>
                                      @endif
                                    </td>
                                    <!-- <td >@if($value->estado == '1') {{trans('contableM.activo')}} @elseif($value->estado =='0') Anulada @else Activo @endif</td> -->
                                    <td style="text-align: center;">
                                      @if($value->estado == '1') 
                                        @if($value->prest_cobrad == '0')
                                          <span >ACTIVO</span> 
                                        @else <span class="label pull bg-green">PAGADO</span> 
                                        @endif 
                                      @elseif($value->estado =='0') Inactivo 
                                      @else Activo 
                                      @endif
                                    </td>
                                    <td>
                                      <a href="{{route('prestamos_empleados.modal_prestamos', ['id_prestamo' => $value->id])}}" data-toggle="modal" data-target="#modal_ver_prestamos" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-eye-open"></span></a>
                                      <a href="{{route('pdf_prestamos_egreso',['id' => $value->id])}}" target="_blank" class="btn btn-success  btn-xs"><span class="glyphicon glyphicon-download-alt"></span></a>
                                    </td>
                                    
                                </tr>
                              @endforeach
                            </tbody>
                         </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
 
  </section>
  
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>

<script type="text/javascript">

  $('#modal_ver_prestamos').on('hidden.bs.modal', function() {
    location.reload();
    $(this).removeData('bs.modal');
  });
  
  $(document).ready(function(){
 
      $('#example2').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : false,
        'info'        : false,
        'autoWidth'   : false
      });

  });

</script>
@endsection
