@extends('contable.compra.base')
@section('action-content')
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<!-- Main content -->
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.factura')}}</a></li>
      <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.RegistrodeFactura')}}</li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header header_new">
      <div class="col-md-9">
        <!--<h8 class="box-title size_text">Empleados</h8>-->
        <!--<label class="size_text" for="title">EMPLEADOS</label>-->
        <h3 class="box-title">{{trans('contableM.FACTURACONVENIOS')}}</h3>
      </div>

      <div class="col-md-1 text-right">
        <button onclick="location.href='{{route('factura_convenios.create')}}'" class="btn btn-success btn-gray">
          <i class="fa fa-file"></i>
        </button>
      </div>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">{{trans('contableM.BUSCADORDEFACTURAS')}}</label>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('factura_convenios.index') }}">
        {{ csrf_field() }}
        <div class="form-group col-md-1 col-xs-1">
          <label class="texto" for="id">{{trans('contableM.id')}}</label>
        </div>
        <div class="form-group col-md-2 col-xs-2 container-1">
          <input class="form-control" type="text" id="id" name="id" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif" placeholder="Ingrese Id..." />

        </div>

        <div class="form-group col-md-1 col-xs-1">
          <label class="texto" for="nombre_proveedor">{{trans('contableM.cliente')}}: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <select style="width: 100%;" class="form-control select2" name="id_cliente" id="id_cliente">
            <option value="">Seleccione...</option>
            @foreach($clientes as $value)
            <option @if(isset($searchingVals)) @if($searchingVals['id_cliente']==$value->identificacion) selected="selected" @endif @endif  value="{{$value->identificacion}}">{{$value->nombre}}</option>
            @endforeach

          </select>
        </div>
        <div class="form-group col-md-2 col-xs-2">
          <label class="texto" for="nro_comprobante">{{trans('contableM.Nrocomprobante')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="nro_comprobante" name="nro_comprobante" value="@if(isset($searchingVals)){{$searchingVals['nro_comprobante']}}@endif" placeholder="Ingrese numero de comprobante..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="fecha">{{trans('contableM.fecha')}}: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <div class="input-group date">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" name="fecha" class="form-control fecha" id="fecha" value="@if(isset($searchingVals)){{$searchingVals['fecha']}}@endif">
          </div>
        </div>
        <div class="form-group col-md-2 col-xs-2">
          <label class="texto" for="id_asiento">{{trans('contableM.NroAsiento')}}</label>
        </div>
        <div class="form-group col-md-2 col-xs-2 container-1">
          <input class="form-control" type="text" id="id_asiento" name="id_asiento" value="@if(isset($searchingVals)){{$searchingVals['id_asiento']}}@endif" placeholder="Ingrese # Asiento..." />

        </div>
        <div class="col-md-offset-9 col-xs-2">
          <button type="submit" id="buscarEmpleado" class="btn btn-primary btn-gray">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
          </button>
        </div>
      </form>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">{{trans('contableM.listadorfactura')}}</label>
      </div>
    </div>
    <div class="box-body dobra">
      <div class="form-group col-md-12">
        <div class="form-row">
          <div id="resultados">
          </div>
          <div id="contenedor">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
              <div class="row">
                <div class="table-responsive col-md-12">
                  <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                      <tr class='well-dark'>
                        <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">#</th>
                        <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending"># {{trans('contableM.asiento')}}</th>
                        <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.Nrocomprobante')}}</th>
                        <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.fecha')}}</th>
                        <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.cliente')}}</th>
                        <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.total')}}</th>
                        <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.creadopor')}}</th>
                        <!--<th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Egresos</th>-->
                        <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.estado')}}</th>
                        <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.accion')}}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($ventas as $value)
                      <tr>
                        <td>{{$value->id}}</td>
                        <td>{{$value->id_asiento}}</td>
                        <td>{{$value->nro_comprobante}}</td>
                        <td>{{$value->fecha}}</td>
                        <td>{{$value->cliente->nombre}}</td>
                        <td>{{$value->total_final}}</td>
                        <td>{{$value->usuario->nombre1}} {{$value->usuario->apellido1}}</td>
                        <td>@if($value->estado==1) {{trans('contableM.activo')}} @else {{trans('contableM.inactivo')}} @endif</td>
                        <td style="padding-left: 1px;padding-right: 1px;">
                          @if($value->estado == 1)
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a href="javascript:anular({{$value->id}});" class="btn btn-success col-md-3 col-xs-3 btn-xs btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">&nbsp;&nbsp;Anular</a>
                          @elseif($value->estado == 0)
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a class="btn btn-danger col-md-3 col-xs-3 btn-xs" disabled style="font-size: 10px;padding-left: 2px;padding-right: 20px;">&nbsp;&nbsp;Anular</a>
                          @endif
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a href="{{ route('ventas_editar', ['id' => $value->id]) }}" class="btn btn-warning col-md-3 col-xs-3 btn-xs btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Visualizar</a>
                          <a target="_blank" href="{{ route('pdf_comprobante_no.tributario', ['id' => $value->id]) }}" class="btn btn-success col-md-3 col-xs-3 btn-xs btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Pdf Fact</a>
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
                <div class="col-sm-5">
                  <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('contableM.mostrando')}} {{1 + (($ventas->currentPage() - 1) * $ventas->perPage())}} / {{count($ventas) + (($ventas->currentPage() - 1) * $ventas->perPage())}} de {{$ventas->total()}} {{trans('contableM.registros')}}</div>
                </div>
                <div class="col-sm-7">
                  <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                    {{ $ventas->appends(Request::only(['id_cliente', 'id', 'nro_comprobante','fecha']))->links() }}
                  </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
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
      'autoWidth': false,
      'sInfoEmpty': true,
      'sInfoFiltered': true,
      'language': {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
      }
    });

  });
  $('#modal_devoluciones').on('hidden.bs.modal', function() {
    $(this).removeData('bs.modal');

  });

  function anular(id) {

    Swal.fire({
      title: `¿{{trans('contableM.anularfactura')}}?`,
      text: `{{trans('contableM.norevertiraccion')}}!`,
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si'
    }).then((result) => {
      if (result.isConfirmed) {
        var acumulate = "";

        $.ajax({
          type: 'get',
          url: "{{ route('ventas.verificar')}}",
          datatype: 'json',
          data: {
            'verificacion': '1',
            'id_venta': id
          },
          success: function(data) {
            //console.log(data+" dsada "+id);
            console.log(data);
            if (data[1] != 0) {
              acumulate += "Existe formas de pago, con el id : " + data[1] + " <br> ";
            }
            if (data[2] != 0) {
              acumulate += "Existe formas de pago, con el id : " + data[2] + " <br> ";
            }
            if (acumulate != "") {
              Swal.fire("Error!", "Existen algunos comprobantes generados con esta factura, observaciones encontradas: <br> " + acumulate, "error");
            } else {
              console.log("entra aqui" + id);
              test(id);
            }
          },
          error: function(data) {
            console.log(data);
          }
        });

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
          url: "{{ url('contable/ventas/factura/')}}/" + id,
          datatype: 'json',
          data: {
            'observacion': text
          },
          success: function(data) {
            Swal.fire(`{{trans('contableM.correcto')}}!`, `{{trans('contableM.anulacioncorrecta')}}`, "success");
            location.href = "{{route('factura_convenios.index')}}";
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
  $("#nombre_proveedor").autocomplete({
    source: function(request, response) {
      $.ajax({
        url: "{{route('compra_buscar_nombreproveedor')}}",
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
  $('#fecha').datetimepicker({
    format: 'YYYY-MM-DD',
  });
  $('.select2').select2({
    tags: false
  });
</script>
@endsection