@extends('laboratorio.orden.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
}
</style>
<!-- Ventana modal editar -->
<div class="modal fade fullscreen-modal" id="doctor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-md-6">
          <h3 class="box-title">{{trans('laboratorio.supervision_ordenes_laboratorio')}}</h3>
        </div>

    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="{{route('orden.search_supervision')}}">
        {{ csrf_field() }}
        <div class="form-group col-md-4 col-xs-6">
            <label for="fecha" class="col-md-3 control-label">{{trans('laboratorio.desde')}}</label>
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

          <div class="form-group col-md-4 col-xs-6">
            <label for="fecha_hasta" class="col-md-3 control-label">{{trans('laboratorio.hasta')}}</label>
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

          <div class="form-group col-md-4 col-xs-6">
            <label for="seguro" class="col-md-3 control-label">{{trans('laboratorio.seguro')}}</label>
              <div class="col-md-9">
                <select id="seguro" name="seguro" class="form-control input-sm" onchange="buscar();">
                  <option value="">{{trans('laboratorio.todos')}}</option>
                  @foreach ($seguros as $value)
                    <option @if(!is_null($seguro))@if($seguro == $value->id) selected @endif @endif value="{{$value->id}}">{{$value->nombre}}</option>
                  @endforeach
                </select>
              </div>
          </div>

          <div class="form-group col-md-4 col-xs-6">
            <label for="nombres" class="col-md-3 control-label">{{trans('laboratorio.paciente')}}</label>
            <div class="col-md-9">
              <div class="input-group">
                <input value="@if($nombres!=''){{$nombres}}@endif" type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="Nombres y Apellidos" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('nombres').value = '';"></i>
                </div>
              </div>
            </div>
          </div>

        <div class="form-group col-md-1 col-xs-3">
          <button type="submit" style="width: 100%;" class="btn btn-primary btn-xs" id="boton_buscar">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
        </div>
        @if(Auth::user()->id=='1307189140'||Auth::user()->id_tipo_usuario=='1'||Auth::user()->id_tipo_usuario=='12'||Auth::user()->id_tipo_usuario=='11'||Auth::user()->id_tipo_usuario=='22')
        <div class="form-group col-md-1 col-xs-3">
          <button type="submit" style="width: 100%;" class="btn btn-primary btn-xs" formaction="{{route('orden.reporte_index')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> {{trans('laboratorio.reporte')}}</button>
        </div>

        <div class="form-group col-md-2 col-xs-4">
          <a type="submit" style="width: 100%;" class="btn btn-primary btn-xs" href="{{route('facturalabs.reporte_anual')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> {{trans('laboratorio.reporte_anual')}}</a>
        </div>
        @endif
        @if(Auth::user()->id=='1307189140'||Auth::user()->id_tipo_usuario=='1'||Auth::user()->id_tipo_usuario=='12'||Auth::user()->id_tipo_usuario=='16'||Auth::user()->id_tipo_usuario=='22')
        <div class="form-group col-md-2 col-xs-3">
          <button type="submit" style="width: 100%;" class="btn btn-primary btn-xs" formaction="{{route('orden.estad_mes')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> {{trans('laboratorio.estadistico')}}</button>
        </div>
        @endif
        <div class="form-group col-md-2 col-xs-6">
          <button type="submit" style="width: 100%;" class="btn btn-primary btn-xs" formaction="{{route('orden.reporte_detalle')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> {{trans('laboratorio.detalle')}}</button>
        </div>
        <div class="form-group col-md-2 col-xs-6">
          <button type="submit" style="width: 100%;" class="btn btn-danger btn-xs" formaction="{{route('orden_labs.resultados_pendientes')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> {{trans('laboratorio.examenes_pendientes')}}</button>
        </div>
        <div class="form-group col-md-2 col-xs-6">
          <a style="width: 100%;" class="btn btn-success btn-xs" href="{{route('orden_labs.cuponera_index')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Control Cupones</a>
        </div>
        <div class="form-group col-md-2 col-xs-6">
          <a class="btn btn-success" href="{{route('labscomisiones.comisiones')}}">{{trans('laboratorio.reporte_comisiones')}}</a>
        </div>
      </form>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
            <thead>
              <tr role="row">
                <th width="5%">{{trans('laboratorio.fecha')}}</th>
                <th width="20%">{{trans('laboratorio.nombres')}}</th>
                <th width="10%">{{trans('laboratorio.convenio')}}</th>
                <th width="5%">{{trans('laboratorio.tipo')}}</th>
                <th width="5%">{{trans('laboratorio.modificada')}}</th>
                <th width="5%">{{trans('laboratorio.cantidad')}}</th>
                <th width="10%">{{trans('laboratorio.subtotal')}}</th>
                <th width="5%">{{trans('laboratorio.descuento')}}</th>
                <th width="5%">{{trans('laboratorio.recargo')}}</th>
                <th width="10%">{{trans('laboratorio.total')}}</th>
                <th width="5%" >{{trans('laboratorio.resultados')}}(%)</th>
                <th width="5%" >{{trans('laboratorio.resultados')}}</th>
                <th width="5%">{{trans('laboratorio.ver')}}</th>
                <th width="5%" >{{trans('laboratorio.detalle')}}</th>

              </tr>
            </thead>
            <tbody>
            @foreach ($ordenes as $value)
              <tr role="row">
                <td>{{substr($value->fecha_orden,0,10)}}</td>
                <td>{{$value->papellido1}} @if($value->papellido2=='N/A'||$value->papellido2=='(N/A)') @else{{ $value->papellido2 }} @endif {{$value->pnombre1}} @if($value->pnombre2=='N/A'||$value->pnombre2=='(N/A)') @else{{ $value->pnombre2 }} @endif </td>
                <td>{{$value->snombre}}-{{$value->nombre_corto}}</td>
                <td>{{$value->pre_post}}</td>
                <td>{{substr($value->mnombre1,0,1)}}{{$value->mapellido1}}</td>
                <td style="text-align: right;">{{$value->cantidad}}</td>
                <td style="text-align: right;">$ {{number_format($value->valor,2)}}</td>
                <td style="text-align: right;">$ {{number_format($value->descuento_valor,2)}}</td>
                <td style="text-align: right;">$ {{number_format($value->recargo_valor,2)}}</td>
                <td style="text-align: right;">$ {{number_format($value->total_valor,2)}}</td>
                <td>
                  <div class="progress progress">
                    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" id="td{{$value->id}}">
                      <span id="sp{{$value->id}}" style="color: black;"></span>
                    </div>
                  </div>
                </td>
                <td>
                  @if($value->estado=='1')
                  <div class="col-md-12">
                    <button type="button" class="btn btn-success btn-xs" onclick="descargar({{$value->id}});"><span >Resultados</span></button>
                  </div>
                  @endif
                </td>
                <td>
                  @if($value->id_empresa != null && $value->id_empresa != '9999999999')
                    @if($value->stipo!='0')

                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      <a target="_blank" href="{{ route('cotizador.imprimir', ['id' => $value->id]) }}" class="btn btn-block btn-success btn-xs" style="padding: 0px;">
                      <span class="fa fa-download"></span> Cotización
                      </a>




                    @else

                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <!--a href="{{ route('orden.descargar', ['id' => $value->id]) }}" target="_blanck" class="btn btn-block btn-success btn-xs" -->
                        <a href="{{ route('orden.detalle', ['id' => $value->id, 'dir' => 'sup']) }}" class="btn btn-block btn-success btn-xs" style="padding: 0px;" >
                        <span class="fa fa-download"></span> Orden
                        </a>


                    @endif
                  @endif
                </td>
                <td>
                  @if($value->stipo=='0')
                    @php $xtipo = Auth::user()->id_tipo_usuario; @endphp
                    @if($xtipo == '1' || $xtipo=='11')
                    <!--div class="col-md-4" style="padding: 2px;">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      <a href="{{ route('orden.edit1_c', ['id' => $value->id]) }}" class="btn btn-block btn-warning btn-xs" style="padding: 0px;">
                      <span class="glyphicon glyphicon-edit"></span>
                      </a>
                    </div-->
                    @endif
                    <div class="col-md-4" style="padding: 2px;">
                      <a href="{{ route('detalle.valor',['id' => $value->id]) }}" class="btn btn-block btn-primary btn-xs" style="padding: 0px;" >
                      <span class="fa fa-plus" ></span>
                      </a>
                    </div>


                  @endif
                </td>

              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1+($ordenes->currentPage()-1)*$ordenes->perPage()}}  / @if(($ordenes->currentPage()*$ordenes->perPage())<$ordenes->total()){{($ordenes->currentPage()*$ordenes->perPage())}} @else {{$ordenes->total()}} @endif de {{$ordenes->total()}} registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $ordenes->appends(Request::only(['fecha', 'fecha_hasta', 'nombres']))->links()}}
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>

<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">

  $(document).ready(function($){

    @foreach ($ordenes as $value)

      $.ajax({
        type: 'get',
        url:"{{ route('resultados.puede_imprimir',['id' => $value->id]) }}",

        success: function(data){

            if(data.cant_par==0){
              var pct = 0;
            }else{
              var pct = data.certificados/data.cant_par*100;
            }

            console.log(data);
            $('#td{{$value->id}}').css("width", Math.round(pct)+"%");
            $('#sp{{$value->id}}').text(Math.round(pct)+"%");
            if(pct < 10){
              $('#td{{$value->id}}').addClass("progress-bar-danger");
            }else if(pct >=10 && pct<90){
              $('#td{{$value->id}}').addClass("progress-bar-warning");
            }else{
              $('#td{{$value->id}}').addClass("progress-bar-success");
            }


        },


        error: function(data){


        }
      });

    @endforeach

    $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',


            defaultDate: '{{$fecha}}',

            });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY/MM/DD',


            defaultDate: '{{$fecha_hasta}}',

            });
        $("#fecha").on("dp.change", function (e) {
            buscar();
        });

         $("#fecha_hasta").on("dp.change", function (e) {
            buscar();
        });

    $(".breadcrumb").append('<li class="active">Órdenes</li>');

    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })



  });

   $('#doctor').on('hidden.bs.modal', function(){
                location.reload();
                $(this).removeData('bs.modal');
            });


   function buscar()
{
  var obj = document.getElementById("boton_buscar");
  obj.click();
}
  function descargar(id_or){
    var cert = $('#sp'+id_or).text();
    if(cert=='0%'){
      alert("Sin Exámenes Ingresados");
    }else{
      //location.href = '{{url('resultados/imprimir')}}/'+id_or;
      window.open('{{url('resultados/imprimir')}}/'+id_or,'_blank');
    }

  }

</script>

@endsection
