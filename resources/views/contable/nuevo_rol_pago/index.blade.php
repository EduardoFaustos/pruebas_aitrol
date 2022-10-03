@extends('contable.nuevo_rol_pago.base')
@section('action-content')

<!-- Ventana modal Rol Pago-->
<div class="modal fade" id="modal_rol_pago" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
<section class="content">
      <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">{{trans('nomina.contable')}}</a></li>
        <li class="breadcrumb-item"><a href="#">{{trans('nomina.nomina')}}</a></li>
        <li class="breadcrumb-item"><a href="{{route('nomina.index')}}">{{trans('nomina.empleado')}}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{trans('nomina.roles_pago')}}o</li>
      </ol>
    </nav>
    <div class="box">
      <div class="box-header">
          <div class="col-md-7">
            <h5><b>{{trans('nomina.roles_pago')}} {{$empleado->user->apellido1}} {{$empleado->user->apellido2}} {{$empleado->user->nombre1}} {{$empleado->user->nombre2}}</b></h5>
          </div>
          <div class="col-md-3 text-right">
            <a href="{{route('nuevo_rol.crear', ['id' => $empleado->id])}}" class="btn btn-success btn-gray">{{trans('nomina.crear_rol')}}</a>
          </div>
          <div class="col-md-1 text-right">
            <button  onclick="goBack()" class="btn btn-default btn-gray">
              <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('nomina.regresar')}}
            </button>
          </div>
      </div>
      <div class="row head-title">
        <div class="col-md-12 cabecera">
          <label class="color_texto" for="title">{{trans('nomina.buscador_rol_pago')}}</label>
        </div>
      </div>
      <div class="box-body dobra">
        <form method="POST" id="buscad_rol_pago" action="{{route('nuevo_rol.index', ['id' => $empleado->id])}}">
          {{ csrf_field() }}
          <input  name="id_nomina" id="id_nomina" type="text" class="hidden" value="{{$empleado->id}}">

          <div class="form-group col-md-1 col-xs-2">
            <label class="texto" for="year">{{trans('nomina.anio')}}</label>
          </div>
          <div class="form-group col-md-2 col-xs-10 container-4">
            <select id="year" name="year" class="form-control">
              <option value="">{{trans('nomina.seleccione')}}...</option>
              @for($i = 2019; $i <= 2030; $i++ )
                <option @if($anio==$i) selected @endif value="{{$i}}">{{$i}}</option>
              @endfor  
            </select>
          </div>
          <div class="form-group col-md-1 col-xs-2">
            <label class="texto" for="mes">{{trans('nomina.mes')}}</label>
          </div>
          @php $Meses = array('{{trans('nomina.enero')}}', '{{trans('nomina.febrero')}}', '{{trans('nomina.marzo')}}', '{{trans('nomina.abril')}}', '{{trans('nomina.mayo')}}', '{{trans('nomina.junio')}}', '{{trans('nomina.julio')}}', '{{trans('nomina.agosto')}}', '{{trans('nomina.septiembre')}}', '{{trans('nomina.octubre')}}', '{{trans('nomina.noviembre')}}', '{{trans('nomina.diciembre')}}'); @endphp
          <div class="form-group col-md-2 col-xs-10 container-4">
            <select id="mes" name="mes" class="form-control">
              <option value="">{{trans('nomina.selecciones')}}...</option>
              @for($i = 1; $i <= 12; $i++)
                <option @if($mes==$i) selected @endif value="{{$i}}">{{$Meses[$i - 1]}}</option>';
              @endfor
            </select>
          </div>
          <div class="col-xs-2">
            <button type="submit" class="btn btn-primary">
                <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
            </button>
          </div>
        </form>
      </div>
      <div class="row head-title">
        <div class="col-md-12 cabecera">
          <label class="color_texto" >{{trans('nomina.listado_rol')}}</label>
        </div>
      </div>
      <div class="box-body dobra">
        <div class="form-group col-md-12">
          <div class="form-row">
            <div id="contenedor">
              <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                <div class="row">
                  <div class="table-responsive col-md-12">
                    <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info" style="width: 100%">
                      <thead>
                        <tr class='well-dark'>
                          <!--th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Nombres </th>
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cedula</th-->
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('nomina.anio')}}</th>
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('nomina.mes')}}</th>
                          <!--<th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Empresa</th>-->
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('nomina.sueldo')}}</th>
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending"> {{trans('nomina.ingresos')}}</th>
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending"> {{trans('nomina.egresos')}}</th>
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('nomina.neto')}}</th>
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.estado')}}</th>
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.accion')}}</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($roles as $value)
                          @php
                            $user = Sis_medico\User::find($value->id_user);
                            $obtener_nombre = Sis_medico\Empresa::find($value->id_empresa);
                            $obtener_detalle = Sis_medico\Ct_Detalle_Rol::where('id_rol',$value->id)->first();
                          @endphp
                          <tr>
                            <!--td>{{$user->apellido1}} @if($user->apellido2!='(N/A)'){{$user->apellido2}}@endif {{$user->nombre1}} @if($user->nombre2!='(N/A)'){{$user->nombre2}}@endif</td>
                            <td>@if(!is_null($user->id)){{$user->id}}@endif</td-->
                            <td>@if(!is_null($value->anio)){{$value->anio}}@endif</td>
                            <td>
                              @if($value->mes == '1') {{trans('nomina.enero')}}
                                @elseif($value->mes == '2') {{trans('nomina.ferero')}}
                                @elseif($value->mes == '3') {{trans('nomina.marzo')}}
                                @elseif($value->mes == '4') {{trans('nomina.abril')}}
                                @elseif($value->mes == '5') {{trans('nomina.mayo')}}
                                @elseif($value->mes == '6') {{trans('nomina.junio')}}
                                @elseif($value->mes == '7') {{trans('nomina.julio')}}
                                @elseif($value->mes == '8') {{trans('nomina.agosto')}}
                                @elseif($value->mes == '9') {{trans('nomina.septiembre')}}
                                @elseif($value->mes == '10') {{trans('nomina.octubre')}}
                                @elseif($value->mes == '11') {{trans('nomina.noviembre')}}
                                @elseif($value->mes == '12') {{trans('nomina.diciembre')}}
                              @endif

                            </td>
                            <!--<td>@if(!is_null($obtener_nombre->razonsocial)){{$obtener_nombre->razonsocial}}@endif</td>-->
                            <td>@if(!is_null($obtener_detalle)){{$obtener_detalle->sueldo_mensual}}@endif</td>
                            <td>@if(!is_null($obtener_detalle)){{$obtener_detalle->total_ingresos}}@endif</td>
                            <td>@if(!is_null($obtener_detalle)){{$obtener_detalle->total_egresos}}@endif</td>
                            <td>@if(!is_null($obtener_detalle)){{$obtener_detalle->neto_recibido}}@endif</td>
                            <td>@if($value->estado == '1') {{trans('nomina.activo')}} @elseif($value->estado =='0') {{trans('nomina.anulada')}} @else {{trans('nomina.activo')}} @endif
                            </td>
                            <td>
                              <input type="hidden" name="_token" value="{{ csrf_token() }}">
                              @if($value->estado == '1')
                                <!--a href="{{route('rol_pago.editar', ['id' => $value->id])}}" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-eye-open"></span></a-->
                                <a target="_blank" href="{{route('rol_pago.imprimir', ['id' => $value->id])}}" class="btn btn-success  btn-xs"><span class="glyphicon glyphicon-download-alt"></span></a>
                                <button class="btn btn-danger btn-xs" onclick="eliminar_rol('{{$value->id}}')"><span class="glyphicon glyphicon-trash"></span></button>
                                <a  class="btn btn-primary  btn-xs" onclick="reenviar_mail('{{$value->id}}')"><span class="fa fa-envelope"></span></a>
                                <a href="{{route('nuevo_rol.editar_nuevo_rol', ['id' => $value->id])}}" class="btn btn-warning btn-xs"><span class="glyphicon glyphicon-eye-open"></span></a>
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
                <div class="row">
                  
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
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

    function goBack() {
      window.history.back();
    }

    function reenviar_mail(id){
      Swal.fire({
        title: '{{trans('nomina.seguro_envio_mail')}}?',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: `{{trans('nomina.enviar')}}`,
        denyButtonText: `{{trans('nomina.no_enviar')}}`,
        showLoaderOnConfirm: true,
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
          $.ajax({
            url:"{{asset('contable/rol/pago/envio/correo/')}}/"+id,
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            type: 'GET',
            success: function(data){
                if(data == 'ok'){
                  Swal.fire('{{trans('nomina.enviado_correctamente')}}!', '', 'success');
                }

            },
            error: function(data){
                Swal.fire('Error al Enviar el Correo!', '', 'error');
            }
          });

        }
      });
    }

    function eliminar_rol(id){
      Swal.fire({
        title: '{{trans('nomina.seguro_eliminar_rol')}}?',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: '{{trans('nomina.enviar')}}',
        denyButtonText: '{{trans('nomina.no_enviar')}}',
        showLoaderOnConfirm: true,
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
          $.ajax({
            url:"{{asset('nuevo_rol_e/pago/eliminar')}}/"+id,
            type: 'GET',
            success: function(data){
                location.reload();

            },
            error: function(data){
                Swal.fire('{{trans('nomina.error_enviar')}}!', '', 'error');
            }
          });

        }
      });
    }

  </script>

@endsection