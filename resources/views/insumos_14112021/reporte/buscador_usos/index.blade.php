@extends('insumos.reporte.buscador_usos.base')
@section('action-content')
    <!-- Main content -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<div class="modal fade" id="buscador" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document" style="width: 60%;">
    <div class="modal-content">

    </div>
  </div>
</div>

<section class="content">
  <div class="box">
    <div class="box-header">
    </div>
    <div class="box-body">
      <form method="POST" id="reporte_master" action="{{ route('reporte.buscador_usos_excel') }}" >
        {{ csrf_field() }}
        <div class="form-group col-md-4" style="padding-left: 0px;padding-right: 0px;">
            <label class="control-label col-md-6">Paciente: </label>
            <div class="col-md-6">
              <input type="text" name="pciente" id="pciente" class="form-control autocom input-sm" placeholder="Ingrese nombre paciente" value="@if(isset($paciente)){{$paciente}}@endif">
            </div>
           
        </div>
        <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
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

        <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
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

        <div class="form-group col-md-6 col-xs-6" style="text-align: right;">
          <button type="submit" formaction="{{ route('reporte.buscador_usos')}}" class="btn btn-primary btn-sm" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true" style="font-size: 16px">&nbsp;Buscar&nbsp;</span></button>
        </div>

        <div class="form-group col-md-6 col-xs-6" >
          <button type="button" onclick="desc_reporte_master();" class="btn btn-primary btn-sm" id="boton_buscar">
            <span class="glyphicon glyphicon-download-alt" aria-hidden="true" style="font-size: 16px">&nbsp;Descargar&nbsp;</span></button>
        </div>
      </form>

      <div class="table-responsive col-md-12">
        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="text-align: center;">
            <thead>
              <tr>
                <th>Codigo</th>
                <th>Nombre</th>
                <th>Tipos de Productos</th>
                <th>Serie</th>
                <th>Pedido</th>
                <th>Bodega</th>
                <th>Marca</th>
                <th>Fecha Procedimiento</th>
                <th>Paciente</th>
                <th>Procedimiento</th>
                <th>Fecha de Vencimiento</th>
              </tr>
            </thead>
            <tbody>

              @foreach ($productos as $value)
                @php
                  $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->id)->get();
                  $mas = true;
                  $texto = "";
                  foreach($adicionales as $value2)
                    {
                      if($mas == true){
                       $texto = $texto.$value2->procedimiento->nombre  ;
                       $mas = false;
                      }
                      else{
                        $texto = $texto.' + '.  $value2->procedimiento->nombre  ;
                      }
                    }
                @endphp
                  <tr>
                    <td >@if(!is_null($value->codigo)){{$value->codigo}}@endif</td>
                    <td style="text-align: left;">@if(!is_null($value->nombre_producto)){{$value->nombre_producto}}@endif</td>
                    <td>{{$value->nombre_tipo}}</td>
                    <td >@if(!is_null($value->codigo)){{$value->serie}}@endif</td>
                    <td >@if(!is_null($value->pedido)){{$value->pedido}}@endif</td>
                    <td >@if(!is_null($value->nombre_bodega)){{$value->nombre_bodega}}@endif</td>
                    <td >@if(!is_null($value->nombre_marca)){{$value->nombre_marca}}@endif</td>
                    <td >@if(!is_null($value->fecha_atencion)){{substr($value->fecha_atencion,0,-9)}}@endif</td>
                    <td >@if(!is_null($value->apellido1)){{$value->apellido1}} {{$value->apellido2}} {{$value->nombre1}} {{$value->nombre2}}@endif</td>
                    <td >@if(!is_null($texto)) {{$texto}} @else NO INGRESADO @endif</td>
                    <td>{{$value->fecha_vencimiento}}</td>
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
            {{ $productos->appends(Request::only(['fecha', 'fecha_hasta','pciente']))->links() }}
          </div>
        </div>
      </div>

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
    $('#buscador').on('hidden.bs.modal', function(){
        $(this).removeData('bs.modal');
    });

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
