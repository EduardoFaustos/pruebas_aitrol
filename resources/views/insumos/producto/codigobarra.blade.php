@extends('insumos.producto.base')
@section('action-content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">

<style type="text/css">
  .code {
    height: 80px !important;
  }
</style>
<!-- Ventana modal editar -->
<div class="modal fade" id="seguimiento" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

@php
$id_empresa= Session::get('id_empresa');
$empresa_1 = Sis_medico\Empresa::where('prioridad', '1')->first();
@endphp

<!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
        <div class="col-md-12">
          <div class="col-md-6 ">
            <h3 class="box-title">{{trans('winsumos.lista_productos')}}</h3>
          </div>
          <div class="col-md-2 ">
            <a class="btn btn-primary" href="{{ route('ingreso_producto.index') }}" style="width: 100%;">{{trans('winsumos.ingreso_pedido')}}</a>
          </div>
          @if($id_empresa==$empresa_1->id)
          <div class="col-md-2 ">
            <a class="btn btn-primary" href="{{ route('ingreso.conglomerada.anterior') }}" style="width: 100%;">{{trans('winsumos.ingreso_conglomerada_anterior')}}</a>
          </div>
          @endif
          <div class="col-md-2 ">
            <a class="btn btn-primary" href="{{ route('ingreso.conglomerada') }}" style="width: 100%;">{{trans('winsumos.ingreso_conglomerada')}}</a>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-12 ">
      <h4 class="box-title">{{trans('winsumos.Buscar')}}</h4>
    </div>

    <!-- /.box-header -->
    <div class="box-body">
      <form method="POST" action="{{ route('codigo.barra') }}">
        {{ csrf_field() }}
        <div class="row">
          <div class="col-md-5">
            <div class="form-group col-md-4 ">
              <label class="texto" for="numerodepedido">{{trans('winsumos.numero_pedido')}}</label>
            </div>
            <div class="form-group col-md-8  container-4" style="padding-left: 1px;">
              <input class="form-control" type="text" id="numerodepedido" name="numerodepedido" placeholder="{{trans('winsumos.numero_pedido')}}" />
            </div>
          </div>
          <div class="col-md-5">
            <div class="form-group col-md-4 ">
              <label class="texto" for="razonsocial">{{trans('winsumos.ingrese_nombre_proveedor')}}</label>
            </div>
            <div class="form-group col-md-8  container-4" style="padding-left: 1px;">

              <select class="form-control select2_find_proveedor" style="width: 100%;" name="razonsocial" id="razonsocial">
              </select>

            </div>
          </div>
          <div class="col-md-5">
            <div class="form-group col-md-4 ">
              <label class="texto" for="razonsocial">Producto</label>
            </div>
            <div class="form-group col-md-8  container-4" style="padding-left: 1px;">
              <select id="producto" name="producto" class="form-control select2_productos" style="width:100%" required">
              </select>
            </div>
          </div>


        </div>
        <div class="col-md-2">
          <button type="submit" id="buscarEmpleado" class="btn btn-primary ">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('winsumos.Buscar')}}
          </button>
        </div>
      </form>
    </div>

    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="tbl_detalles" class="display compact responsive" role="grid" aria-describedby="example2_info" style="margin-top:0 !important; width: 100%!important;">
            <thead>
              <tr>
                <th width="15%">{{trans('winsumos.fecha_ingreso')}}</th>
                <th width="10%">{{trans('winsumos.Tipo')}}</th>
                <th width="15%">{{trans('winsumos.proveedores')}}</th>
                <th width="15%">{{trans('winsumos.numero_pedido')}}</th>
                <th width="15%">{{trans('winsumos.num_fact')}}</th>
                <th width="10%">{{trans('winsumos.creado_por')}}</th>
                <th width="10%">{{trans('winsumos.item_totales')}}</th>
                <th width="10%">{{trans('winsumos.total_productos_restantes')}}</th>
                <!--<th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Precio de compra</th>-->
                <th>{{trans('winsumos.accion')}}</th>
              </tr>
            </thead>
            <tbody>
              @php
              $i=0;
              @endphp
              @foreach ($pedidos as $value)
              @php
              $movimiento = \Sis_medico\Movimiento::where('id_pedido', $value->id)->get();
              $valida = 0;
              foreach ($movimiento as $item){
              $elemento = \Sis_medico\Pedido::cant_traslado($item->serie);
              if($elemento > 0){
              $valida =1 ;
              break;
              }
              }
              @endphp
              <tr>
                <td> {{ $value->created_at }}</td>
                <td> @if(($value->tipo)==1) {{trans('winsumos.guia_remision')}} @elseif(($value->tipo)==2) {{trans('winsumos.fact_contra_entrega')}} @elseif(($value->tipo==3)) {{trans('contableM.factura')}} @endif</td>
                <td>{{ $value->nombrecomercial }}</td>
                <td>{{ $value->pedido }}</td>
                <td>{{ $value->factura }}</td>
                <td>{{ $value->nombre1}} {{ $value->apellido1}}</td>
                <td>@if($cantidades[$i][0] != null){{$cantidades[$i][0]}}@else 0 @endif</td>
                <td>@if($cantidades[$i][1] != null){{$cantidades[$i][1]}}@else 0 @endif</td>
                <td>
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <a href="{{ route('producto.revisar_pedido', ['id' => $value->id]) }}" class="btn btn-primary col-md-9 col-xs-9 btn-margin">
                    {{trans('winsumos.revisar_pedido')}}
                  </a>
                  <a href="{{ route('producto.editar_pedido_new', ['id' => $value->id]) }}" class="btn btn-warning col-md-9 col-xs-9 btn-margin">
                      Edit Lote - Fecha Exp
                  </a>
                 
                  @if ($value->tipo==3 && $value->estado_contable == 0)
                  <a href="{{ route('ingreso.editar_conglomerada', ['id' => $value->id]) }}" class="btn btn-success col-md-9 col-xs-9 btn-margin">
                    {{trans('winsumos.editar_fact_conglomerada')}}
                  </a>
                  <a href="{{ route('barra.generar', ['id' => $value->id]) }}" target="_blank" class="btn btn-warning col-md-9 col-xs-9 btn-margin">
                    {{trans('winsumos.imprimir_codigo')}}
                    </a>
                    @if ($value->orden_conglomerada!=1)
                      @if($valida == 0)
                      <a href="{{ route('ingreso.eliminar.conglomerada', ['id' => $value->id]) }}" data-toggle="modal" data-target="#seguimiento" class="btn btn-danger col-md-9 col-xs-9 btn-margin">
                        {{trans('winsumos.eliminar_fact_conglomerada')}}
                      </a>
                      @endif
                    @endif
                  @else
                  <a href="{{ route('barra.generar', ['id' => $value->id]) }}" target="_blank" class="btn btn-warning col-md-9 col-xs-9 btn-margin">
                    {{trans('winsumos.imprimir_codigo')}}
                  </a>
                  @if($value->estado_contable == 0)
                    @if($valida == 0)
                    <a href="{{ route('ingreso.eliminar_pedido', ['id' => $value->id]) }}" data-toggle="modal" data-target="#seguimiento" class="btn btn-danger col-md-9 col-xs-9 btn-margin">
                      {{trans('winsumos.eliminar_pedido')}}
                    </a>
                    @endif
                    @if($value->tipo!=1 )
                        @if($value->tipo!=2)
                        <a href="{{ route('ingreso.editar_pedido', ['id' => $value->id]) }}" class="btn btn-success col-md-9 col-xs-9 btn-margin">
                          {{trans('winsumos.editar_pedido')}}
                        </a>
                        @endif
                      @endif
                    @endif
                  @endif
                  <a class="btn btn-success col-md-9 col-xs-9 btn-margin" href="{{ url('codigo/barras/reportePedido/excel') }}/{{$value->id}}"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel</a>
                </td>
              </tr>
              @php
              $i =$i+1;
              @endphp
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('winsumos.mostrando')}} 1 / {{count($pedidos)}} {{trans('winsumos.de')}} {{$pedidos->total()}} {{trans('winsumos.registros')}}</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $pedidos->appends(Request::only(['numerodepedido', 'razonsocial','ingresenombreproveeedor', 'producto']))->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  <!-- /.box-body -->
</section>
<!-- /.content -->
<script type="text/javascript">
  $('#seguimiento').on('hidden.bs.modal', function() {
    $(this).removeData('bs.modal');
  });

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


  $(document).ready(function() {


    $('.select2_productos').select2({
      tags: false
    });
    $('.select2_productos').select2({
      placeholder: "Seleccione un producto...",
      allowClear: true,
      minimumInputLength: 3,
      cache: true,
      ajax: {
        url: '{{route("pedido_realizados.buscarProductos")}}',
        data: function(params) {
          var query = {
            search: params.term,
            type: 'public'
          }
          return query;
        },
        processResults: function(data) {
          return {
            results: data
          };
        }
      }
    });

    $('#tbl_detalles').DataTable({
      'paging': false,
      dom: 'lBrtip',
      'lengthChange': false,
      'searching': true,
      'ordering': false,
      'responsive': true,
      'info': false,
      'autoWidth': true,
      'columnDefs': [{
          "width": "5%",
          "targets": 0
        },
        {
          "width": "5%",
          "targets": 2
        },
        {
          "width": "10%",
          "targets": 6
        },
        {
          "width": "5%",
          "targets": 8
        }
      ],
      language: {
        zeroRecords: "{{trans('winsumos.actualizar')}}"
      },
      buttons: [{
          extend: 'copyHtml5',
          footer: true
        },

        {
          extend: 'excelHtml5',
          footer: true,
          title: "{{trans('winsumos.pedidos')}}"
        },
        {
          extend: 'csvHtml5',
          footer: true
        },
        {
          extend: 'pdfHtml5',
          orientation: 'landscape',
          pageSize: 'LEGAL',
          footer: true,
          title: "{{trans('winsumos.pedidos')}}",
          customize: function(doc) {
            doc.styles.title = {
              color: 'black',
              fontSize: '17',
              alignment: 'center'
            }
          }
        }
      ],
    });

  });
</script>
@endsection