@extends('insumos.reporte.buscador_master.base')
@section('action-content')
    <!-- Main content -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<section class="content">
  <div class="box">
      <div class="box-header">
      </div>
       <div class="box-body">
          <form method="POST" id="reporte_master" action="{{ route('reporte.reporte_master') }}" >
            {{ csrf_field() }}

              <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                  <label for="fecha" class="col-md-3 control-label">Desde</label>
                  <div class="col-md-9">
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <input type="text" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off">
                      <div class="input-group-addon">
                        <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                  <label for="fecha_hasta" class="col-md-3 control-label">Hasta</label>
                  <div class="col-md-9">
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
                      <div class="input-group-addon">
                        <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
                      </div>
                    </div>
                  </div>
                </div>

            <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="cedula" class="col-md-3 control-label">Código Producto</label>
              <div class="col-md-9">
                <div class="input-group">
                  <input value="@if($codigo!=''){{$codigo}}@endif" type="text" class="form-control input-sm" name="codigo" id="codigo" placeholder="CODIGO PRODUCTO" >
	                <div class="input-group-addon">
	                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('codigo').value = ''; buscar();"></i>
	                  </div>
	                </div>
              </div>
            </div>

            <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="nombres" class="col-md-3 control-label">Nombre Producto</label>
              <div class="col-md-9">
                <div class="input-group">
                  <input value="@if($nombre!=''){{$nombre}}@endif" type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="Nombre del Producto" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                  <div class="input-group-addon">
                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('nombres').value = ''; buscar();"></i>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="bodega" class="col-md-3 control-label">Bodega</label>
              <div class="col-md-9">
                <select class="form-control input-sm" name="bodega" id="bodega" onchange="buscar();">
                  <option value="">Seleccione ...</option>
                  	@foreach($bodega_nombre as $value)
	                  <option @if($value->id==$bodega) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
	                @endforeach
                </select>
              </div>
            </div>

            <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="proveedor" class="col-md-3 control-label">Proveedor</label>
              <div class="col-md-9">
                <select class="form-control input-sm" name="proveedor" id="proveedor" onchange="buscar();">
                  <option value="">Seleccione ...</option>
                    @foreach($proveedor as $value)
                    <option @if($value->id==$proveedores) selected @endif value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="marca" class="col-md-3 control-label">Marca</label>
              <div class="col-md-9">
                <select class="form-control input-sm" name="marca" id="marca" onchange="buscar();">
                  	<option value="">Seleccione ...</option>
                	@foreach($marca_nombre as $value)
	                  <option @if($value->id==$marca) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
	                @endforeach
                </select>
              </div>
            </div>

            <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="tipo_producto" class="col-md-3 control-label">Tipo Producto</label>
              <div class="col-md-9">
                <select class="form-control input-sm" name="tipo_producto" id="tipo_producto" onchange="buscar();">
                    <option value="">Seleccione ...</option>
                  @foreach($tipo_producto as $value)
                    <option @if($value->id==$tipo_prod) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                  @endforeach
                </select>
              </div>
            </div>
          
            <div class="form-group col-md-3 col-xs-3" style="padding-left: 0px;padding-right: 0px;">
              <label for="tipo" class="col-md-3 control-label">Tipo</label>
              <div class="col-md-9">
                <select class="form-control input-sm" name="tipo" id="tipo" onchange="buscar();">
                    <option value="">Seleccione ...</option>
                    <option @if($tipo==1) selected @endif value="1">Guia de Remision</option>
                    <option @if($tipo==2) selected @endif value="2">Factura contra entrega</option>
                    <option @if($tipo==3) selected @endif value="3">Factura</option>
                </select>
              </div>
            </div>

            <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="tipo" class="col-md-3 control-label">Fecha</label>
              <div class="col-md-9">
                <select class="form-control input-sm" name="caducados" id="caducados" onchange="buscar();">
                    <option  value="">Seleccione ...</option>
                    <option  value="1">Caducados</option>
                    <option  value="2">No caducados</option>
                </select>
              </div>
            </div>

            <div class="form-group col-md-2 col-xs-5">
              <button type="submit" formaction="{{ route('reporte.buscador_master')}}" class="btn btn-primary btn-sm" id="boton_buscar">
                <span class="glyphicon glyphicon-search" aria-hidden="true" style="font-size: 16px">&nbsp;Buscar&nbsp;</span></button>
            </div>

            <div class="form-group col-md-4 col-xs-4" >
              <button type="button" onclick="desc_reporte_master();" class="btn btn-primary btn-sm" id="boton_buscar">
                <span class="glyphicon glyphicon-download-alt" aria-hidden="true" style="font-size: 16px">&nbsp;Descargar&nbsp;</span></button>
            </div>

            <div class="table-responsive col-md-12">
  			      <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
  			        <thead>
  			          <tr>
  			            <th>Codigo</th>
  			            <th>Nombre</th>
  			            <th>Descripción</th>
                    <th>Serie</th>
  			            <th>Pedido</th>
  			            <th>Bodega</th>
                    <th>Proveedor</th>
  			            <th>Marca</th>
                    <th>Tipo Producto</th>
  			            <th>Cantidad</th>
  			            <th>Fecha Vencimiento</th>
  			            <th>Estado</th>
  			          </tr>
  			        </thead>
  			        <tbody>
  			          @foreach ($productos as $value)
                      <tr>
                        <td >{{$value->codigo}}</td>
                        <td >{{$value->nombre_producto}}</td>
                        <td >{{$value->descripcion}}</td>
                        <td >{{$value->serie}}</td>
                        <td >{{$value->pedido}}</td>
                        <td >{{$value->nombre_bodega}}</td>
                        <td >{{$value->nombrecomercial}}</td>
                        <td >{{$value->nombre_marca}}</td>
                        <td >{{$value->nombre_tipoproducto}}</td>
                        <td >{{$value->cantidad}}</td>
                        <td >{{$value->fecha_vencimiento}}</td>
                        <td >@if($value->fecha_vencimiento <= $fecha_hoy) Producto Caducado @endif</td>
                      </tr>
  		            @endforeach
  			        </tbody>
  			        <tfoot>
  			        </tfoot>
  			      </table>
			      </div>

      			<div class="row">
      	          <div class="col-sm-5">
      	            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 al {{count($productos)}} de {{$productos->total()}} Registros</div>
      	          </div>
      	          <div class="col-sm-7">
      	            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
      	              {{ $productos->appends(Request::only(['desde_inicio', 'hasta_fin', 'codigo', 'nombres', 'bodega', 'marca', 'tipo_producto', 'proveedor']))->links() }}
      	            </div>
      	          </div>
      	    </div>
          </form>
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap ">
          </div>
        </div>
  </div>
</section>


<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

  <script type="text/javascript">

  	function desc_reporte_master (){
      $('#reporte_master').submit();
    }

    $(function () {
      $('#fecha').datetimepicker({
          format: 'YYYY/MM/DD',
          defaultDate: '{{$fecha}}',

          });
      $('#fecha_hasta').datetimepicker({
          format: 'YYYY/MM/DD',
          defaultDate: '{{$fecha_hasta}}',

          });
      $("#fecha").on("dp.change", function (e) {
          //buscar();
      });

       $("#fecha_hasta").on("dp.change", function (e) {
          //buscar();
      });
  });

  $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      'order'       : [[ 1, "asc" ]]
    });

function buscar()
{
  var obj = document.getElementById("boton_buscar");
  obj.click();
}

 </script>
@endsection