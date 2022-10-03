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
        <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
            <label class="control-label col-md-3">{{trans('winsumos.Paciente')}}</label>
            <div class="col-md-9">
              <input type="text" name="pciente" id="pciente" class="form-control autocom input-sm" placeholder="{{trans('winsumos.Ingrese_paciente')}}" value="@if(isset($paciente)){{$paciente}}@endif">
            </div>
           
        </div>

        <div class="form-group col-md-3" >
          <label class="control-label col-md-3">{{trans('winsumos.Tipo')}} </label>
            <div class="col-md-9">
                <select name="tipo_" class="form-control select2" style="width: 100%;" name="tipo_" id="tipo_">
                  <option value="">{{trans('winsumos.seleccione')}}</option>
                    @foreach($doc_bodega as $value)
                      
                      @if ($value->id == '1') <option @if(isset($data['tipo_'])) @if(!is_null($data['tipo_']) and $data['tipo_'] == 1) selected @endif @endif value="{{$value->id}}"> {{$value->documento}} </option> @endif
                      @if ($value->id == '3') <option @if(isset($data['tipo_'])) @if(!is_null($data['tipo_']) and $data['tipo_'] == 3) selected @endif @endif value="{{$value->id}}"> {{$value->documento}} </option> @endif
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
              <label for="cedula" class="col-md-3 control-label">{{trans('winsumos.codigo_producto')}}</label>
              <div class="col-md-9">
                <div class="input-group">
                  <input value="@if($codigo!=''){{$codigo}}@endif" type="text" class="form-control input-sm" name="codigo" id="codigo" placeholder="{{trans('winsumos.codigo_producto')}}" >
	                <div class="input-group-addon">
	                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('codigo').value = ''; buscar();"></i>
	                  </div>
	                </div>
              </div>
        </div>

        <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
              <label for="nombres" class="col-md-3 control-label">{{trans('winsumos.ingrese_nombre_producto')}}</label>
              <div class="col-md-9">
                <div class="input-group">
                  <input value="@if($nombres!=''){{$nombres}}@endif" type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="ingrese_nombre_producto" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                  <div class="input-group-addon">
                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('nombres').value = ''; buscar();"></i>
                  </div>
                </div>
              </div>
        </div>

        <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
              <label for="marca" class="col-md-3 control-label">{{trans('winsumos.marca')}}</label>
              <div class="col-md-9">
                <select class="form-control input-sm" name="marca" id="marca" onchange="buscar();">
                  	<option value="">{{trans('winsumos.seleccione')}}</option>
                	@foreach($marca_nombre as $value)
	                  <option @if($value->id==$marca) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
	                @endforeach
                </select>
              </div>
        </div>
        
        <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
              <label for="proveedor" class="col-md-3 control-label">{{trans('winsumos.proveedores')}}</label>
              <div class="col-md-9">
                <select class="form-control input-sm" name="proveedor" id="proveedor" onchange="buscar();">
                  <option value="">{{trans('winsumos.seleccione')}}</option>
                    @foreach($proveedor as $value)
                    <option @if($value->id==$proveedores) selected @endif value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                  @endforeach
                </select>
              </div>
        </div>

        
        <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="col-md-3 control-label">{{trans('winsumos.fecha_desde')}}</label>
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
            
        <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha_hasta" class="col-md-3 control-label">{{trans('winsumos.fecha_hasta')}}</label>
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

        <div class="form-group col-md-2 col-xs-6" style="text-align: right;">
          <button type="submit" formaction="{{ route('reporte.buscador_usos')}}" class="btn btn-primary btn-sm" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true" style="font-size: 16px">&nbsp;{{trans('winsumos.Buscar')}}&nbsp;</span></button>
        </div>

        <div class="form-group col-md-2 col-xs-6" >
          <button type="submit" formaction="{{route('reporte.reporte_usos_productos')}}" class="btn btn-primary btn-sm" id="boton_buscar">
            <span class="glyphicon glyphicon-download-alt" aria-hidden="true" style="font-size: 16px">&nbsp;Descargar&nbsp;</span></button>
        </div>
      </form>

      <div class="table-responsive col-md-12">
        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="text-align: center;">
            <thead>
              <tr>
                <th>{{trans('winsumos.codigo')}}</th>
                <th >{{trans('winsumos.ingrese_nombre_producto')}}
                <th>{{trans('winsumos.fecha_procedimiento')}}</th>
                <th>{{trans('winsumos.Paciente')}}</th>
                <th>{{trans('winsumos.marca')}}</th>
                <th> {{trans('winsumos.descripcion')}} </th>
                <th >{{trans('winsumos.proveedores')}} </th>
              
                <th> {{trans('winsumos.cantidad')}} </th>
                <th> {{trans('winsumos.lote')}} </th>
                <th>{{trans('winsumos.fecha_vencimiento')}}</th>
                <th>{{trans('winsumos.pedidos')}}</th>
                <th>{{trans('winsumos.serie')}}</th>
	              <th>  {{trans('winsumos.valorpedido')}} </th>
                <th> {{trans('winsumos.valorsalidakardex')}} </th>
                <th> Valor Total Uso Pac. </th>
                <th> {{trans('winsumos.facturaconsigna')}} </th>
		            <th>{{trans('winsumos.Procedimiento')}}</th>
                
              </tr>
            </thead>
            <tbody>

              @foreach ($productos as $value)
               
                  <tr>
                    <td >@if(!is_null($value->codigo)){{$value->codigo}}@endif</td>
                    <td >@if(!is_null($value->nombre_producto)){{$value->nombre_producto}}@endif</td>
                    <td >@if(!is_null($value->fechaini)){{substr($value->fechaini,0,-9)}}@endif</td>
                    <td >@if(!is_null($value->apellido1)){{$value->apellido1}} {{$value->apellido2}} {{$value->nombre1}} {{$value->nombre2}}@endif</td>
                    <td >@if(!is_null($value->nombre_m)){{$value->nombre_m}}@endif</td>
                    <td >@if(!is_null($value->nombre_producto)){{$value->nombre_producto}}@endif</td>
                    <td >@if(!is_null($value->nombrecomercial)){{$value->nombrecomercial}}@endif </td>
              
                    <td >@if(!is_null($value->cantidad)){{$value->cantidad}}@endif</td>
                    <td >@if(!is_null($value->lote)){{$value->lote}}@endif</td>
                    <td >@if(!is_null($value->vencimiento)){{$value->vencimiento}}@endif</td>
                    <td >@if(!is_null($value->pedido)){{$value->pedido}}@endif</td>
                    <td >@if(!is_null($value->codigo)){{$value->serie}}@endif</td>
                    <td>@if(!is_null($value->total)){{$value->total}}@endif</td>
                    <td> @if(!is_null($value->precio_k)){{$value->precio_k}}@endif </td>
                    <td> {{number_format($value->cantidad)*($value->total) }}</td>
                    <td>@if(!is_null($value->documento)){{$value->documento}}@endif</td>                   
                    
                    @php 
                    $procedimiento_final = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->hc_id_procedimiento)->first();
                    @endphp
                    <td>@if(!is_null($procedimiento_final->procedimiento->nombre)){{$procedimiento_final->procedimiento->nombre}}@endif</td>
                    
                  </tr>
              @endforeach
            </tbody>
            <tfoot>
            </tfoot>
        </table>
      </div>

      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('winsumos.mostrando')}} 1 {{trans('winsumos.a')}} {{count($productos)}} {{trans('winsumos.de')}} {{$productos->total()}} {{trans('winsumos.registros')}}</div>
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

    $(document).ready(function(){
      $('.select2').select2({
      tags: false
    });
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
