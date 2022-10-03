@extends('contable.compra.base')
@section('action-content')
<style>
  .ui-autocomplete {
    overflow-x: hidden;
    max-height: 200px;
    width: 1px;
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

  .swal-wide {
    width: 250px !important;
  }
</style>
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<!-- Ventana modal editar -->
<div class="modal fade" id="modal_devoluciones" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
<!-- Main content -->
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.Compras')}}</a></li>
      <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.RegistroFacturadeCompra')}}</li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header header_new">
      <div class="col-md-9">
        <!--<h8 class="box-title size_text">Empleados</h8>-->
        <!--<label class="size_text" for="title">EMPLEADOS</label>-->
        <h3 class="box-title">{{trans('contableM.COMPRA')}}</h3>
      </div>

      <div class="col-md-1 text-right">
        <button onclick="location.href='{{route('compra_crear')}}'" class="btn btn-success btn-gray">
          <i class="fa fa-file"></i>
        </button>
      </div>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">{{trans('contableM.BUSCADORDECOMPRAS')}}</label>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('compras.buscar') }}">
        {{ csrf_field() }}
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="id">{{trans('contableM.id')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10">
          <input class="form-control" type="text" id="id" name="id" value="@if(isset($searchingVals)){{$searchingVals['ct_c.id']}}@endif" placeholder="Ingrese Id..." />

        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="ct_c.id_asiento_cabecera">{{trans('contableM.asiento')}}: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10">
          <input class="form-control" type="text" id="ct_c.id_asiento_cabecera" name="id_asiento_cabecera" value="@if(isset($searchingVals)){{$searchingVals['ct_c.id_asiento_cabecera']}}@endif" placeholder="Ingrese Asiento..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="nombre_proveedor">{{trans('contableM.proveedor')}}: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10">
          <select class="form-control select2_find_proveedor" name="proveedor" id="proveedor" style="width: 100%;">

          </select>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="detalle">{{trans('contableM.detalle')}}: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10">
          <input class="form-control" type="text" id="detalle" name="detalle" value="@if(isset($searchingVals)){{$searchingVals['observacion']}}@endif" placeholder="Ingrese detalle..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="secuencia_f">{{trans('contableM.secuencia')}}: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10">
          <input class="form-control" type="text" id="secuencia_f" name="secuencia_f" value="@if(isset($searchingVals)){{$searchingVals['secuencia_factura']}}@endif" placeholder="Ingrese Secuencia..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="tipo">{{trans('contableM.tipo')}}: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10">
          <select class="form-control select2" type="text" style="width: 100%;" id="tipo" name="tipo" value="@if(isset($searchingVals)){{$searchingVals['ct_c.tipo_comprobante']}}@endif">
            <option value="">Seleccione...</option>
            @foreach($tipo_comprobante as $value)
            <option value="{{$value->codigo}}">{{$value->nombre}}</option>
            @endforeach

          </select>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="fecha">{{trans('contableM.fecha')}}: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10">
          <div class="input-group date">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" name="fecha" class="form-control fecha" id="fecha" value="@if(isset($searchingVals)){{$searchingVals['fecha']}}@endif">
          </div>
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
                        <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">#</th>
                        <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending"># {{trans('contableM.asiento')}}</th>
                        <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.serie')}}</th>
                        <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.fecha')}}</th>
                        <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.proveedor')}}</th>
                        <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.autorizacion')}}</th>
                        <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.tipocomprobante')}}</th>
                        <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.detalle')}}</th>
                        <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.creadopor')}}</th>
                        <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.estado')}}</th>
                        <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.accion')}}</th>
                      </tr>
                    </thead>
                    <tbody>

                      @foreach ($compras as $value)
                      @if(!is_null($value))
                      @php

                      $nombre_comprobante = "";
                      $nombre_comprobante = \Sis_medico\Ct_master_tipos::where('codigo', $value->tipo_comprobante)->where('tipo', '1')->where('estado', '1')->first();

                      @endphp
                      <tr class="well">
                        <td> {{$value->id}}</td>
                        <td>{{$value->id_asiento_cabecera}}</td>
                        <td>{{$value->numero}}</td>


                        <td>{{$value->fecha}}</td>
                        <td>{{$value->razonsocial}}</td>
                        <td>{{$value->autorizacion}}</td>
                        <td>@if(!is_null($nombre_comprobante)) {{$nombre_comprobante->nombre}} @endif</td>
                        <td>@if(!is_null($value->observacion)) {{$value->observacion}} @endif</td>
                        <td>{{$value->nombre1}} {{$value->apellido1}}</td>
                        <td>@if($value->estado == '1') {{trans('contableM.activo')}} @elseif($value->estado =='0') Anulada @else Activo @endif</td>
                        <td>
                          @if($value->estado!=0)
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a href="javascript:anular({{$value->id}});" class="btn btn-danger btn-gray col-md-3"><i class="fa fa-trash"> </i></a>
                          <input type="hidden" name="_token" value="{{ csrf_token()}}">
                          @else
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a class="btn btn-danger btn-gray col-md-3" disabled><b>X</b></a>
                          @endif
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a href="{{ route('compra_editar', ['id' => $value->id]) }}" class="btn btn-success btn-gray col-md-3"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></a>
                          @if(!is_null($value->rutapdf))
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a target="_blank" href="{{ route('compras.visualizar', ['id' => $value->id]) }}" class="btn btn-success btn-gray col-md-3"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>
                          <a href="javascript:anularpdf({{$value->id}});" class="btn btn-success btn-gray col-md-3"><i class="fa fa-remove" aria-hidden="true"></i></a>
                          @endif
                        </td>
                      </tr>
                      @endif
                      @endforeach
                    </tbody>
                    <tfoot>
                    </tfoot>
                  </table>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-5">
                  <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('contableM.mostrando')}} {{1 + (($compras->currentPage() - 1) * $compras->perPage())}} / {{count($compras) + (($compras->currentPage() - 1) * $compras->perPage())}} de {{$compras->total()}} {{trans('contableM.registros')}}</div>
                </div>
                <div class="col-sm-7">
                  <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                    {{ $compras->appends(Request::only(['proveedor', 'observacion', 'secuencia_factura','ct_c.tipo_comprobante','fecha','id_asiento_cabecera','id','detalle','secuencia_f']))->links() }}
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
    $('#example22').DataTable({
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
          url: "{{ route('compras.verificar_anulacion')}}",
          datatype: 'json',
          data: {
            /*'verificar':'1',
                             'id_compra': id*/
            'verificar': '4',
            'id_compra': id
          },
          success: function(data) {
            if (data.respuesta == "si") {
              //swal("Error!",`Existen algunas ${data.tabla} generados con esta factura`,"error");
              let id_com_egreso = data.id_egreso;
              let enlace = `<a target="_blank" href="{{ url('contable/acreedores/documentos/comprobante/egreso/comprobante/buscar?id="${data.ids[0]}')}}"><b>${data.tablas[0]}</b></a>`;
              let enlace2 = `<a target="_blank" href="{{ url('contable/Banco/debito/acreedores/buscar?id=${data.ids[1]}')}}"> <b>${data.tablas[1]}</b> </a>`;
              let enlace3 = `<a target="_blank" href="{{ url('contable/cruce/valores?id=${data.ids[2]}')}}"><b>${data.tablas[2]}</b></a>`;
              let enlace4 = `<a target="_blank" href="{{ url('contable/acreedores/documentos/retenciones/buscar?id=${data.ids[3]}')}}"><b>${data.tablas[3]}</b></a>`;
              let enlace5 = `<a target="_blank" href="{{ url('contable/comp_egreso_masivo/buscar?id=${data.ids[4]}')}}"><b>${data.tablas[4]}</b></a>`;
              let enlace6 = `<a target="_blank" href="{{ url('contable/cruce_cuentas/valores/buscar?id=${data.ids[5]}')}}"><b>${data.tablas[5]}</b></a>`;
              let texto = `Existen algunos ${enlace} ${enlace2} ${enlace3} ${enlace4} ${enlace5} ${enlace6} generados con esta factura`;

              alertas("error", "Error", texto);

            } else {
              test(id);
            }
            /*if(data[1]!=0){
              acumulate+="Existe egresos, con el id : "+data[1]+" <br> ";
            }
            if(data[2]!=0){
              acumulate+="Existe retenciones, con el id : "+data[2]+" <br> ";
            }
            if(acumulate!=""){
              swal("Error!","Existen algunos comprobantes generados con esta factura, observaciones encontradas: <br> "+acumulate,"error");
            }else{
                console.log("entra aqui"+id);
                test(id);                                
            }*/
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
          url: "{{ url('contable/compras/factura/')}}/" + id,
          datatype: 'json',
          data: {
            'observacion': text
          },
          success: function(data) {
            swal(`{{trans('contableM.correcto')}}!`, `{{trans('contableM.anulacioncorrecta')}}`, "success");
            location.href = "{{route('compras_index')}}";
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

  function alertas(icon, title, text) {
    Swal.fire({
      icon: `${icon}`,
      title: `${title}`,
      html: `${text}`
    })
  }

  function anularpdf(id) {
    Swal.fire({
      title: '¿Desea eliminar este documento?',
      text: `{{trans('contableM.norevertiraccion')}}!`,
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si, Eliminar!'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: 'get',
          url: `{{ url('contable/anularpdf/compra/${id}/1')}}`,
          datatype: 'json',
          data: {

          },
          success: function(data) {
            alertas("success", "Exito..", "Se eliminó con exito");
            location.href = "{{route('compras_index')}}";
          },
          error: function(data) {
            console.log(data);
          }
        });
      }
    })
  }

  $('.select2_find_proveedor').select2({
    placeholder: "Escriba el nombre del proveedor",
    allowClear: true,
    ajax: {
      url: '{{route("compras.proveedorsearch")}}',
      data: function(params) {
        var query = {
          search: params.term,
          type: 'public'
        }
        return query;
      },
      processResults: function(data) {
        // Transforms the top-level key of the response object from 'items' to 'results'
        console.log(data);
        return {
          results: data
        };
      }
    }
  });
</script>
@endsection