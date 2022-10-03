@extends('insumos.producto.base')
@section('action-content')

<style type="text/css">
  .code{
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

    <!-- Main content -->
    <section class="content">
      <div class="box">
        <div class="box-header">
          <div class="row">
            <div class="col-md-12">
                <div class="col-md-6 ">
                  <h3 class="box-title">Lista de Productos</h3>
                </div>
                <div class="col-md-2 ">
                  <a class="btn btn-primary" href="{{ route('ingreso_producto.index') }}" style="width: 100%;">Ingreso de Pedido</a>
                </div>
                <div class="col-md-2 ">
                  <a class="btn btn-primary" href="{{ route('ingreso.conglomerada') }}" style="width: 100%;">Ingreso Conglomerada</a>
                </div>
            </div>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <form method="POST" action="{{ route('codigo.barra') }}">
            {{ csrf_field() }}
            @component('layouts.search', ['title' => 'Buscar'])
              @component('layouts.two-cols-search-row', ['items' => ['Numero de Pedido','Ingrese nombre proveeedor'],
              'oldVals' => [isset($searchingVals) ? $searchingVals['numerodepedido'] : '',isset($searchingVals) ? $searchingVals['razonsocial'] : '']])
              @endcomponent
            @endcomponent
          </form>
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
              <div class="table-responsive col-md-12">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                  <thead>
                    <tr >
                      <th width="15%">Fecha</th>
                      <th width="10%">Tipo</th>
                      <th width="15%">Proveedor</th>
                      <th width="15%">Numero de Pedido</th>
                      <th width="15%">Numero de Factura</th>
                      <th width="10%">Realizado por</th>
                      <th width="10%">Items Totales</th>
                      <th width="10%">Total de Productos Restantes</th>
                      <!--<th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Precio de compra</th>-->
                      <th >Acción</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php
                    $i=0;
                    @endphp
                    @foreach ($pedidos as $value)
                      <tr>
                        <td >{{ $value->created_at }}</td>
                        <td > @if(($value->tipo)==1) Guia de Remisión @elseif(($value->tipo)==2) Factura contra entrega @elseif(($value->tipo==3)) Factura @endif</td>
                        <td >{{ $value->nombrecomercial }}</td>
                        <td >{{ $value->pedido }}</td>
                        <td >{{ $value->factura }}</td>
                        <td >{{ $value->nombre1}} {{ $value->apellido1}}</td>
                        <td >@if($cantidades[$i][0] != null){{$cantidades[$i][0]}}@else 0 @endif</td>
                        <td >@if($cantidades[$i][1] != null){{$cantidades[$i][1]}}@else 0 @endif</td>
                        <td>
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <a href="{{ route('producto.revisar_pedido', ['id' => $value->id]) }}"  class="btn btn-primary col-md-9 col-xs-9 btn-margin">
                            Revisar Pedido
                            </a>
                            <a href="{{ route('barra.generar', ['id' => $value->id]) }}" target="_blank" class="btn btn-warning col-md-9 col-xs-9 btn-margin">
                            Imprimir Codigos
                            </a>
                            <a href="{{ route('pedido.seguimiento', ['id' => $value->id]) }}" data-toggle="modal" data-target="#seguimiento" class="btn btn-warning col-md-9 col-xs-9 btn-margin">
                            Movimientos
                            </a>
                            <a href="{{ route('ingreso.eliminar_pedido', ['id' => $value->id]) }}" data-toggle="modal" data-target="#seguimiento" class="btn btn-danger col-md-9 col-xs-9 btn-margin">
                            Eliminar Pedido
                            </a>
                            <a href="{{ route('ingreso.editar_pedido', ['id' => $value->id]) }}" class="btn btn-success col-md-9 col-xs-9 btn-margin">
                            Editar Pedido
                            </a>
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
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / {{count($pedidos)}} de {{$pedidos->total()}} registros</div>
              </div>
              <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                {{ $pedidos->appends(Request::only(['numeropedido', 'proveedor','ingresenombreproveeedor']))->links() }}
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
    $('#seguimiento').on('hidden.bs.modal', function(){
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
