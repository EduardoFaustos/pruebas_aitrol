@extends('contable.comprobante_ingreso_varios.base')
@section('action-content')
<style type="text/css">
  .autocomplete {
    z-index: 999999 !important;
    z-index: 999999999 !important;
    z-index: 99999999999999 !important;
    position: absolute;
    top: 0px;
    left: 0px;
    float: left;
    display: block;
    min-width: 160px;
    padding: 4px 0;
    margin: 0 0 10px 25px;
    list-style: none;
    background-color: #ffffff;
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
  }

  .ui-autocomplete {
    z-index: 5000;
  }

  .ui-autocomplete {
    z-index: 999999;
    list-style: none;
    background-color: #FFFFFF;
    width: 300px;
    border: solid 1px #EEE;
    border-radius: 5px;
    padding-left: 10px;
    line-height: 2em;
  }
</style>

<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<script type="text/javascript">
  $(function() {
    $(".clickable-row").click(function() {
      window.location = $(this).data("href");
    });
  });
</script>
<div class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.ComprobantedeIngresoVarios')}}</a></li>
      <li class="breadcrumb-item active" aria-current="page">Registro Comprobante de Ingreso Varios</li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header header_new">
      <div class="col-md-9">
        <!--<h8 class="box-title size_text">Empleados</h8>-->
        <!--<label class="size_text" for="title">EMPLEADOS</label>-->
        <h3 class="box-title">{{trans('contableM.ComprobantedeIngresoVarios')}}</h3>
      </div>

      <div class="col-md-1 text-right">
        <button onclick="location.href='{{route('comprobante_ingreso_v.create')}}'" class="btn btn-success btn-gray">
          <i class="fa fa-file"></i>
        </button>
      </div>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">{{trans('contableM.BUSCADORDECOMPDEINGRESOVARIOS')}}</label>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('comprobante_ingreso_v.buscar') }}">
        {{ csrf_field() }}
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="id">{{trans('contableM.id')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="id" name="id" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif" placeholder="Ingrese Id..." />

        </div>

        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="cliente">{{trans('contableM.banco')}}: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <select class="form-control select2" style="width: 100%;" name="id_banco" id="id_banco" value="@if(isset($searchingVals)){{$searchingVals['id_banco']}}@endif">
            <option value="">Seleccione...</option>
            @foreach($bancos as $value)
            <option @if(isset($searchingVals)) {{ $value->id == $searchingVals['id_banco'] ? 'selected' : ''}} @endif value="{{$value->id}}">{{$value->nombre}}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="secuencia">{{trans('contableM.secuencia')}}: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="secuencia" name="secuencia" value="@if(isset($searchingVals)){{$searchingVals['secuencia']}}@endif" placeholder="Ingrese Secuencia..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="detalle">{{trans('contableM.detalle')}}: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" autocomplete="off" type="text" id="concepto" name="concepto" value="@if(isset($searchingVals)){{$searchingVals['concepto']}}@endif" placeholder="Ingrese detalle..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="fecha">{{trans('contableM.fecha')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <div class="input-group date">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" name="fecha" class="form-control fecha" id="fecha" value="@if(isset($searchingVals)){{$searchingVals['fecha']}}@endif">
          </div>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="id_asiento" for="id_asiento">{{trans('contableM.Idasiento')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 ">
          <input class="form-control" type="text" id="id_asiento" name="id_asiento" value="@if(isset($searchingVals)){{$searchingVals['id_asiento_cabecera']}}@endif" placeholder="Ingrese el Id asiento..." />
        </div>
        <div class="col-xs-1">
          <button type="submit" id="buscarEmpleado" class="btn btn-primary btn-gray">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
          </button>
        </div>
      </form>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">{{trans('contableM.LISTADODECOMPROBANTES')}}</label>
      </div>
    </div>
    <div class="box-body dobra">
      <div class="form-group col-md-12">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
          <div class="row">
            <div class="table-responsive col-md-12">
              <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                <thead>
                  <tr class='well-dark'>
                    <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.serie')}}</th>
                    <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Id_asiento</th>
                    <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.fecha')}}</th>
                    <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.banco')}}</th>
                    <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.detalle')}}</th>
                    <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.total')}}</th>
                    <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.creadopor')}}</th>
                    <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.estado')}}</th>
                    <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.accion')}}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($comp_ingreso as $value)
                  <tr>
                    <td>{{$value->secuencia}}</td>
                    <td>{{$value->id_asiento_cabecera}}</td>
                    <td>{{$value->fecha}}</td>
                    <td> CAJA GENERAL</td>
                    <td>{{$value->observaciones}}</td>
                    <td>{{$value->total_ingreso}}</td>
                    <td>@if(isset($value->usuario)) {{$value->usuario->nombre1}} {{$value->usuario->nombre2}} @endif</td>
                    <td> @if(($value->estado)==1) {{trans('contableM.activo')}} @else {{trans('contableM.inactivo')}} @endif </td>
                    <td>
                      <a href="{{route('comprobante_ingreso_v.pdf',['id'=>$value->id])}}" class="btn btn-warning btn-gray" target="_blank" rel="noopener noreferrer"><i class="fa fa-file-pdf-o "></i></a>
                      <!-- @if(($value->estado)==1)
                      <a class="btn btn-danger btn-gray" href="{{route('comprobante_ingreso_v.anular',['id'=>$value->id])}}"><i class="fa fa-trash"></i></a>
                      @endif -->
                      @if(($value->estado)==1)
                        <a class="btn btn-danger btn-gray" href="javascript:anular('{{$value->id}}')"><i class="fa fa-trash"></i></a>
                      @endif
                      <a class="btn btn-success btn-gray" href="{{route('comprobante_ingreso_v.edit',['id'=>$value->id])}}"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></a>

                    </td>
                  </tr>
                  @endforeach
                </tbody>
                <tfoot>
                </tfoot>
              </table>
              <div class="row">
                <div class="col-sm-5">
                  <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('contableM.mostrando')}} {{1 + (($comp_ingreso->currentPage() - 1) * $comp_ingreso->perPage())}} / {{count($comp_ingreso) + (($comp_ingreso->currentPage() - 1) * $comp_ingreso->perPage())}} de {{$comp_ingreso->total()}} {{trans('contableM.registros')}}</div>
                </div>
                <div class="col-sm-7">
                  <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                    {{ $comp_ingreso->appends(Request::only(['id', 'id_cliente', 'secuencia','detalle','fecha']))->links() }}
                  </div>
                </div>
              </div>
            </div>
          </div>


        </div>

      </div>
    </div>
  </div>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/hospital/cleave/dist/cleave.min.js")}}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">
  $(document).ready(function() {
    $('#example2').DataTable({
      'paging': false,
      'lengthChange': true,
      'searching': false,
      'ordering': false,
      'info': false,
      'autoWidth': true,
      'sInfoEmpty': true,
      'sInfoFiltered': true,
      'language': {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
      }
    });

  });
  $('.select2').select2({
    tags: false
  });
  $('#fecha').datetimepicker({
    format: 'YYYY-MM-DD',
  });
  $("#nombre_cliente").autocomplete({
    source: function(request, response) {
      $.ajax({
        url: "{{route('clientes.nombre_clientes')}}",
        dataType: "json",
        data: {
          term: request.term
        },
        success: function(data) {
          response(data);
        }
      });
    },
    minLength: 2,
  });

  function cambiar_nombre() {
    $.ajax({
      type: 'post',
      url: "{{route('clientes.datos_cliente2')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: {
        'nombre': $("#nombre_cliente").val()
      },
      success: function(data) {
        if (data.value != "no") {
          $('#id_cliente').val(data.value);
          $('#direccion').val(data.direccion);
          buscar_vendedor()
        } else {
          $('#id_cliente').val("");
          $('#direccion').val("");
        }

      },
      error: function(data) {
        console.log(data);
      }
    });
  }


    function anular(id) {

    Swal.fire({
      title: '¿Desea Anular esta comprobante?',
      text: `{{trans('contableM.norevertiraccion')}}!`,
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si'
    }).then((result) => {
      if (result.isConfirmed) {
        var acumulate = "";
          test(id);

      }
    })


  }
  async function test(id) {
    try {
      const {
        value: text
      } = await Swal.fire({
        input: 'textarea',
        inputPlaceholder: 'Ingrese motivo de anulación...',
        inputAttributes: {
          'aria-label': 'Ingrese motivo de anulación'
        },
        showCancelButton: true
      })

      if (text) {
        $.ajax({
          type: 'get',
          url: "{{ url('contable/cliente/comprobante/ingreso_varios/anular/')}}/" + id,
          datatype: 'json',
          data: {
            'concepto': text
          },
          success: function(data) {
            Swal.fire(`{{trans('contableM.correcto')}}!`, `{{trans('contableM.anulacioncorrecta')}}`, "success");
            location.reload();
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
</script>

@endsection