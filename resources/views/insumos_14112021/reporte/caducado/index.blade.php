@extends('insumos.reporte.caducado.base')
@section('action-content')
    <!-- Main content -->
<section class="content">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">

  <div class="box">
    <form method="POST" id="reporte_caducado" action="{{route('reporte.reporte_producto_caducado')}}" >
      {{ csrf_field() }}
      <div class="box-header">
        <div class="row">
            <div class="col-sm-10">
            </div>
            <div class="col-sm-2">
              <button type="button" onclick="desc_reporte();" class="btn btn-danger" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;"><span class="glyphicon glyphicon-download-alt" aria-hidden="true" style="font-size: 16px">&nbsp;Descargar&nbsp;</span>
              </button>
            </div>
        </div> 
      </div>
    
      <div class="box-body">
        <h4 class="col-md-12" style="text-align: center;"><b>REPORTE DE PRODUCTOS CADUCADOS / CADUCAR</b></h4>
        <div class="col-md-12" style="padding-left: 20px">
          <div class="row">
            <div class="col-md-8" style="padding-top: 5px;">
              <div class="row">
                <div class="form-group col-md-5 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                  <label for="fecha" class="col-md-3 control-label">Desde</label>
                  <div class="col-md-9">
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <input type="text" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off">
                      <div class="input-group-addon">
                        <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscador_paciente_fecha();"></i>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group col-md-5 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                  <label for="fecha_hasta" class="col-md-3 control-label">Hasta</label>
                  <div class="col-md-9">
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
                      <div class="input-group-addon">
                        <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscador_paciente_fecha();"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-md-4" style="padding-top: 5px;">
              <div class="row">
                <div class="col-md-8" >
                  <div class="">
                    <input value="@if($nombres_prod!=''){{$nombres_prod}}@endif" type="text" class="form-control form-control-sm " name="nombres" id="nombres" placeholder="Nombre" style="text-transform:uppercase;" >
                  </div>
                </div>
                <div class="col-md-4" >
                  <button type="submit" formaction="{{ route('reporte.reporte_caducado') }}" id="btn_caducado" class="btn btn-primary" style="color:white;  border-radius: 5px; border: 2px solid white;"> <i class="fa fa-search" aria-hidden="true">
                  </i> &nbsp;BUSCAR&nbsp;</button>
                </div>
              </div>
            </div>
          </div>
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
              @foreach ($fecha_cadu as $value)
                <tr>
                  <td >{{$value->codigo}}</td>
                  <td >{{$value->nombre}}</td>
                  <td >{{$value->descripcion}}</td>
                  <td >{{$value->serie}}</td>
                  <td >{{$value->pedido}}</td>
                  <td >{{$value->nombre_bodega}}</td>
                  <td >{{$value->nombrecomercial}}</td>
                  <td >{{$value->nombre_marca}}</td>
                  <td >{{$value->nombre_tipoproducto}}</td>
                  <td >{{$value->cantidad}}</td>
                  <td >{{$value->fecha_vencimiento}}</td>
                  <td >@if($value->fecha_vencimiento < $fecha_hoy) Producto Caducado @else Producto Proximo a Caducar @endif</td>
                </tr>
              @endforeach
            </tbody>
            <tfoot>
            </tfoot>
          </table>
        </div>
        <div class="row">
          <div class="col-sm-5">
            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 al {{count($fecha_cadu)}} de {{$fecha_cadu->total()}} Registros</div>
          </div>
          <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
              {{ $fecha_cadu->appends(Request::only(['nombres', 'desde_inicio', 'hasta_fin']))->links() }}
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>  
</section>
    
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">


    function desc_reporte (){
      $('#reporte_caducado').submit();
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

    function buscador_paciente_fecha()
{
  var obj = document.getElementById("btn_caducado");
  obj.click();
}
</script>

@endsection