@extends('contable.rol_otros_anticipo_empleado.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<!-- Ventana modal Otros Anticipos Empleado-->

<div class="modal fade" id="visualizar_anticipos" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>

  <!-- Main content -->
  <section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Otros Anticipos Empleados</li>
      </ol>
    </nav>
      <div class="box">
        <div class="box-header">
            <div class="col-md-9">
              <h5><b>OTROS ANTICIPOS EMPLEADOS</b></h5>
            </div>
        </div>
        <div class="row head-title">
          <div class="col-md-12 cabecera">
              <label class="color_texto" for="title">BUSCADOR DE OTROS ANTICIPOS EMPLEADOS</label>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body dobra">
          <form method="POST" id="buscar_prestamo" action="{{ route('otros_anticipo_empleado.search') }}">
            {{ csrf_field() }}
                <div class="form-group col-md-1 col-xs-2">
                    <label class="texto" for="identificacion">Identificaci&oacute;n:</label>
                </div>
                <div class="form-group col-md-4 col-xs-10 container-4" style="padding-left: 15px;">
                      <input class="form-control" type="text" id="identificacion" name="identificacion"  placeholder="Ingrese Identificación..."  value="@if(isset($searchingVals)){{$searchingVals['id_empl']}}@endif" autocomplete="off" />
                </div>
                <div class="form-group col-md-1 col-xs-2">
                    <label class="texto" for="nombre">Nombres:</label>
                </div>
                <div class="form-group col-md-4 col-xs-10 container-4" style="padding-left: 15px;">
                      <input class="form-control" type="text" id="nombre" name="nombre"  placeholder="Ingrese nombres..."  value="@if(isset($searchingVals)){{$searchingVals['nombres']}}@endif" autocomplete="off" />
                </div>
                <div class="col-xs-2">
                  <button type="submit" id="buscaranticipos" class="btn btn-primary">
                      <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
                  </button>
                </div>
          </form>
        </div>
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" >LISTADO DE OTROS ANTICIPOS</label>
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
                              <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Cédula</th>
                                <!--<th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.empresa')}}</th>-->
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Empleado</th>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.montoanticipo')}}</th>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha Creación</th>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tipo Rol</th>
                                <!--th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor Cuota</th-->
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Mes Inicio Cobro</th>                        
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Año Inicio Cobro</th>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.asiento')}}</th>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tipo de Pago</th>

                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Banco Beneficiario</th>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.estado')}}</th>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.accion')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                              
                              @foreach ($principales as $value)

                                @php 
                                  $nom_cuenta = Sis_medico\Plan_Cuentas::where('id',$value->cuenta_saliente)->first();
                                  $inf_usuario = Sis_medico\User::where('id',$value->id_empl)->first();
                                  //dd($value);
                                @endphp

                                <tr class="well">
                                    <td >@if(!is_null($value->id_empl)){{$value->id_empl}}@endif</td>
                                    <td >{{$inf_usuario->nombre1}}  @if($inf_usuario->nombre2!='(N/A)'){{ $inf_usuario->nombre2 }}@endif {{ $inf_usuario->apellido1 }} @if($inf_usuario->apellido2!='(N/A)'){{$inf_usuario->apellido2}}@endif</td>
                                    <td >@if(!is_null($value->monto_anticipo)){{$value->monto_anticipo}}@endif</td>
                                    <td >@if(!is_null($value->fecha_creacion)){{$value->fecha_creacion}}@endif</td>
                                    <td >@if(!is_null($value->tipo_rol)){{$value->tipo_rol}}@endif</td>
                                    <!--td >@if(!is_null($value->valor_cuota)){{$value->valor_cuota}}@endif</td-->
                                    <td >@if($value->mes_cobro_anticipo == '1') 
                                         Enero 
                                       @elseif($value->mes_cobro_anticipo == '2') 
                                         Febrero
                                       @elseif($value->mes_cobro_anticipo == '3')
                                         Marzo
                                       @elseif($value->mes_cobro_anticipo == '4')
                                         Abril
                                       @elseif($value->mes_cobro_anticipo == '5')
                                         Mayo
                                       @elseif($value->mes_cobro_anticipo == '6')
                                         Junio
                                       @elseif($value->mes_cobro_anticipo == '7')
                                         Julio
                                       @elseif($value->mes_cobro_anticipo == '8')
                                         Agosto
                                       @elseif($value->mes_cobro_anticipo == '9')
                                         Septiembre
                                       @elseif($value->mes_cobro_anticipo == '10')
                                         Octubre 
                                       @elseif($value->mes_cobro_anticipo == '11') 
                                         Noviembre
                                       @elseif($value->mes_cobro_anticipo == '12')
                                         Diciembre
                                       @endif  
                                    </td>
                                    <td >@if(!is_null($value->anio_cobro_anticipo)){{$value->anio_cobro_anticipo}}@endif</td>
                                    <td>@if(!is_null($value->id_asiento_cabecera)) {{$value->id_asiento_cabecera}}  @endif</td>
                                    <td >@if(!is_null($nom_cuenta->nombre)){{$nom_cuenta->nombre}}@endif</td>
                                    <td >@if(!is_null($value->banco_beneficiario)){{$value->banco->nombre}}@endif</td>
                                    <td >@if($value->estado == '1') {{trans('contableM.activo')}} @elseif($value->estado =='0') Anulada @else Activo @endif</td>
                                    <td > 
                                     @if($value->estado!=0)
                                      <a class="btn btn-success btn-gray " data-remote="{{route('modal_anticipo.modal_ver_anticipos', ['id' => $value->id, 'id_i'=>$value->id_empl])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_anticipos">
                                      <i class="glyphicon glyphicon-eye-open"  aria-hidden="true"></i></a>
                                      <a class="btn btn-primary btn-gray " data-remote="{{route('modal_anticipo.modal_editar_anticipos', ['id' => $value->id, 'id_i'=>$value->id_empl])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_anticipos">
                                      <i class="glyphicon glyphicon-pencil"  aria-hidden="true"></i></a>
                                      <a class="btn btn-danger btn-gray " href="javascript:anular({{$value->id}});" class="btn btn-info btn-sm">
                                      <i class="glyphicon glyphicon-trash"  aria-hidden="true"></i></a>
                                      <a class="btn btn-info btn-gray " data-remote="{{route('modal_anticipo.modal_ver_anticipos', ['id' => $value->id, 'id_i'=>$value->id_empl])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_anticipos">
                                      <i class="glyphicon glyphicon-download-alt"  aria-hidden="true"></i></a>
                                      <a class="btn btn-success btn-gray" target="_blank" href="{{route('pdf_otros_anticipo_egreso', ['id'=>$value->id])}}"><i class="fa fa-file-text" aria-hidden="true"></i></a>
                                     @endif
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

  
  function anular(id) {

    Swal.fire({
      title: '¿Desea anular este Asiento?',
      text: `{{trans('contableM.norevertiraccion')}}!`,
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si'
    }).then((result) => {
      if (result.isConfirmed) {
        test(id);
      }
    })


  }
  async function test(id) {
    try {
      const {
        value: text
      } = await Swal.fire({
        title: 'Ingresa tu contraseña',
        input: 'password',
        inputPlaceholder: 'Ingrese contraseña',
        inputAttributes: {
          maxlength: 18,
          autocapitalize: 'off',
          autocorrect: 'off'
        },
        showCancelButton: true
      })

      if (text) {
        $.ajax({
          type: 'get',
          url: "{{ route('librodiario.checkpass')}}",
          datatype: 'json',
          data: {
            'userpass': text,
          },
          success: function(data) {
            //console.log(data+" dsada "+id);
            console.log(data);
            //console.log(acumulate);
            if (data == 'ok') {
              $.ajax({
                type: 'get',
                url: "{{ url('contable/contabilidad/libro/diario/anular/asiento/')}}/" + id,
                datatype: 'json',
                data: {
                  'concepto': text,
                },
                success: function(data) {
                  Swal.fire(`{{trans('contableM.correcto')}}!`, `{{trans('contableM.anulacioncorrecta')}}`, "success");
                  location.reload();
                },
                error: function(data) {
                  console.log(data);
                }
              });
            } else {
              Swal.fire("Mensaje","Error contraseña incorrecta, intente de nuevo...","error");
            }

          },
          error: function(data) {
            console.log(data);
          }
        });


      }

    } catch (err) {
      console.log(err);
    }
  }
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
  $('#visualizar_anticipos').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');

  	});

</script>
@endsection