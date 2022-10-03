@extends('laboratorio.orden.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<script type="text/javascript" src="http://www.google.com/jsapi"></script>

<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
    text-align: right;
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
  <div class="box box-success">
    <div class="box-header">
      <div class="row">
          <div class="col-md-9">
            <!--h3 class="box-title">Estadistica de Exámenes de Laboratorio del año {{$anio}} por mes</h3-->
            <h3 class="box-title">Estadísticas LABS</h3>

          </div>
          <a class="btn btn-primary" onclick="goBack()"><span class="glyphicon glyphicon-arrow-left"></span> Regresar</a>

      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">

      <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="table-responsive col-md-10">
            <h4 style="background-color: #009999;text-align: center;color: white;">EXÁMENES POR AÑO</h4>
            <table id="example1" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example1_info" style="font-size: 12px;">
              <thead>
                <tr role="row">
                  <th >Año</th>
                  <th >Cant. Público</th>
                  <th >Cant. Privado</th>
                  <th >Cantidad Total</th>
                  <th style="background-color: #ffe6e6">Val. Público</th>
                  <th style="background-color: #ffe6e6">Val. Privado</th>
                  <th style="background-color: #ffe6e6">Valor Total</th>
                </tr>
              </thead>
              <tbody>
              @foreach($or_anio as $value)
                <tr role="row" >
                  <td><b>{{$value->anio}}</b></td>
                  <td>@if(isset($arr_anio_tipo[$value->anio.'-0'])){{$arr_anio_tipo[$value->anio.'-0'][0]}}@else 0 @endif</td>
                  @php $part = 0; $priv = 0; $vpart = 0; $vpriv = 0;
                    if(isset($arr_anio_tipo[$value->anio.'-1'])){
                      $priv = $arr_anio_tipo[$value->anio.'-1'][0];
                      $vpriv = $arr_anio_tipo[$value->anio.'-1'][1];
                    }
                    if(isset($arr_anio_tipo[$value->anio.'-2'])){
                      $part = $arr_anio_tipo[$value->anio.'-2'][0];
                      $vpart = $arr_anio_tipo[$value->anio.'-2'][1];
                    }
                  @endphp
                  <td>{{$priv + $part}}</td>
                  <td>{{$value->cantidad}}</td>
                  <td style="background-color: #ffe6e6;color: red;font-size: 15px;">$ @if(isset($arr_anio_tipo[$value->anio.'-0'])){{number_format(round($arr_anio_tipo[$value->anio.'-0'][1],2),2) }}@else 0 @endif</td>
                  <td style="background-color: #ffe6e6;color: red;font-size: 15px;">$ {{number_format(round($vpart + $vpriv,2),2) }}</td>
                  <td style="background-color: #ffe6e6;color: red;font-size: 15px;">$ {{number_format(round($value->valor,2),2) }}</td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
          <div class="col-md-10">
            <div id="chart"></div>
          </div>

        </div>

      </div>

      <form method="POST" action="{{ route('orden.estad_mes') }}" >
        {{ csrf_field() }}
        <div class="row">
          <div class="form-group">
            <label class="col-md-1" style="text-align: center">Año</label>
              <!--<input type="text" name="anio" value="{{$anio}}" class="form-control"-->
              <div class="col-md-2">
              <select class="form-control" name="anio" value="{{$anio}}">
                @php $x=2018; $anio_actual=date('Y'); @endphp
                @for($x=2018;$x<=$anio_actual;$x++)
                <option @if($x==$anio) selected @endif>{{$x}}</option>
                @endfor
              </select>
              </div>
              <button type="submit" class="btn btn-primary">Buscar</button>
          </div>
        </div>

      </form>

      <div id="examplea_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="table-responsive col-md-10">
            <h4 style="background-color: #009999;text-align: center;color: white;">EXÁMENES POR MES DEL AÑO {{$anio}}</h4>
            <table id="examplea" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="examplea_info" style="font-size: 12px;">
              <thead>
                <tr role="row">
                  @php $mes = ['', 'ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];@endphp
                  <th >N.</th>
                  <th >Mes</th>
                  <th >Cant. Público</th>
                  <th >Cant. Privado</th>
                  <th >Cantidad Total</th>
                  <th style="background-color: #ffe6e6">Val. Público</th>
                  <th style="background-color: #ffe6e6">Val. Privado</th>
                  <th style="background-color: #ffe6e6">Valor Total</th>
                  <th >Acción</th>
                </tr>
              </thead>
              <tbody>
              @foreach($or_anio_mes as $value)
                <tr role="row" >
                  <td><b>{{$value->mes}}</b></td>
                  <td><b>{{$mes[$value->mes]}}</b></td>
                  <td>@if(isset($arr_aniomes_tipo[$anio.'-'.$value->mes.'-0'])){{$arr_aniomes_tipo[$anio.'-'.$value->mes.'-0'][0]}}@else 0  @endif</td>
                  @php $part2 = 0; $priv2 = 0; $vpart2 = 0; $vpriv2 = 0;
                    if(isset($arr_aniomes_tipo[$anio.'-'.$value->mes.'-1'])){
                      $priv2 = $arr_aniomes_tipo[$anio.'-'.$value->mes.'-1'][0];
                      $vpriv2 = $arr_aniomes_tipo[$anio.'-'.$value->mes.'-1'][1];
                    }
                    if(isset($arr_aniomes_tipo[$anio.'-'.$value->mes.'-2'])){
                      $part2 = $arr_aniomes_tipo[$anio.'-'.$value->mes.'-2'][0];
                      $vpart2 = $arr_aniomes_tipo[$anio.'-'.$value->mes.'-2'][1];
                    }
                  @endphp
                  <td>{{$priv2 + $part2}}</td>
                  <td>{{$value->cantidad}}</td>
                   <td style="background-color: #ffe6e6;color: red;font-size: 15px;">$ @if(isset($arr_aniomes_tipo[$anio.'-'.$value->mes.'-0'])){{number_format(round($arr_aniomes_tipo[$anio.'-'.$value->mes.'-0'][1],2),2) }}@else 0 @endif</td>
                  <td style="background-color: #ffe6e6;color: red;font-size: 15px;">$ {{number_format(round($vpart2 + $vpriv2,2),2) }}</td>
                  <td style="background-color: #ffe6e6;color: red;font-size: 15px;">$ {{number_format(round($value->valor,2),2) }}</td>
                  <td >
                    <a href="{{route('labs_estad.doctor_mes',['anio' => $value->anio, 'mes' => $value->mes ])}}" class="btn btn-block btn-info btn-xs" style="padding: 0px;">
                      <span >Por Doctor</span>
                    </a>
                    <!--@if($anio=='2020' && $value->mes >=4)
                    <a href="{{route('estadistico.covid',['anio' => $value->anio, 'mes' => $value->mes ])}}" class="btn btn-block btn-success btn-xs" style="padding: 0px;">
                      <span >Covid</span>
                    </a>
                    @endif-->
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
          <div class="col-md-10">
            <div id="chart1"></div>
          </div>
        </div>

      </div>

      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="table-responsive col-md-12">
            <h4 style="background-color: cyan;text-align: center;">ÓRDENES</h4>
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
              <thead>
                <tr role="row">
                  <th >N.</th>
                  <th >Mes</th>
                  @foreach($convenios as $convenio)
                  <th >{{$convenio->nombre}}-{{$convenio->nombre_corto}}</th>
                  @endforeach
                  <th >PART</th>
                  <th style="background-color: #ffe6e6">TOTAL</th>

                </tr>
              </thead>
              <tbody>
              @foreach ($estadistico_total as $value)
                <tr role="row" data-href="{{route('orden.estad_examen',['mes' => $value['mes'], 'anio' => $anio])}}">
                  @php $mes = ['', 'ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];@endphp
                  <td><b>{{$value['mes']}}</b></td>
                  <td><b>{{$mes[$value['mes']]}}</b></td>
                  @foreach($value['convenios'] as $convenio)
                  <td>{{$convenio['ordenes']}}</td>
                  @endforeach
                  <td >{{$estad_part[$value['mes']]['ordenes']}}</td>
                  <td style="background-color: #ffe6e6">{{$value['ordenes']}}</td>

                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        </div>

      </div>
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="table-responsive col-md-12">
            <h4 style="background-color: cyan;text-align: center;">EXÁMENES</h4>
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
              <thead>
                <tr role="row">
                  <th >N.</th>
                  <th >Mes</th>
                  @foreach($convenios as $convenio)
                  <th >{{$convenio->nombre}}-{{$convenio->nombre_corto}}</th>
                  @endforeach
                  <th >PART</th>
                  <th style="background-color: #ffe6e6;color; red;">TOTAL</th>

                </tr>
              </thead>
              <tbody>
              @foreach ($estadistico_total as $value)
                <tr role="row" data-href="{{route('orden.estad_examen',['mes' => $value['mes'], 'anio' => $anio])}}">
                  @php $mes = ['', 'ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];@endphp
                  <td><b>{{$value['mes']}}</b></td>
                  <td><b>{{$mes[$value['mes']]}}</b></td>
                  @foreach($value['convenios'] as $convenio)
                  <td>{{$convenio['cantidad']}}</td>
                  @endforeach
                  <td >{{$estad_part[$value['mes']]['cantidad']}}</td>
                  <td style="background-color: #ffe6e6;color; red;">{{$value['examenes']}}</td>

                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        </div>

      </div>
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="table-responsive col-md-12">
            <h4 style="background-color: cyan;text-align: center;">VALORES</h4>
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
              <thead>

                <tr role="row">
                  <th >N.</th>
                  <th >Mes</th>
                   @foreach($convenios as $convenio)
                  <th >{{$convenio->nombre}}-{{$convenio->nombre_corto}}</th>
                  @endforeach
                  <th >PART</th>
                  <th style="background-color: #ffe6e6">TOTAL</th>
                </tr>
              </thead>
              <tbody>
              @foreach ($estadistico_total as $value)
                <tr role="row" data-href="{{route('orden.estad_examen',['mes' => $value['mes'], 'anio' => $anio])}}">
                  @php $mes = ['', 'ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];@endphp
                  <td><b>{{$value['mes']}}</b></td>
                  <td><b>{{$mes[$value['mes']]}}</b></td>

                  @foreach($value['convenios'] as $convenio)
                  <td>$ {{number_format(round($convenio['valor'],2),2) }}</td>
                  @endforeach
                  <td >$ {{number_format(round($estad_part[$value['mes']]['valor'],2),2) }}</td>
                  <td style="background-color: #ffe6e6;color; red;">$ {{number_format(round($value['valor'],2),2) }}</td>
                </tr>
              @endforeach
              </tbody>
            </table>
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

    $('tr[data-href]').on("click", function() {
      document.location = $(this).data('href');
    });

    $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',




            });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY/MM/DD',




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
    });

    $('#example1').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    });

    $('#examplea').DataTable({
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

  function goBack() {
    window.history.back();
  }

  google.load('visualization', '1', {'packages': ['corechart']});
  google.setOnLoadCallback (createChart);
  function createChart() {

      //create data table object
      var dataTable = new google.visualization.DataTable();

      //define columns
      dataTable.addColumn('string','Año');
      dataTable.addColumn('number', 'Total');
      dataTable.addColumn('number', 'Publico');
      dataTable.addColumn('number', 'Privado');



      //define rows of data
      @php $pub = 0; $part = 0; $priv = 0; $vpart = 0; $vpriv = 0; $vpub = 0; @endphp
      dataTable.addRows([@foreach($or_anio as $value)
        @php $pub = 0; $part = 0; $priv = 0; $vpart = 0; $vpriv = 0; $vpub = 0;
          if(isset($arr_anio_tipo[$value->anio.'-0'])){
            $vpub = $arr_anio_tipo[$value->anio.'-0'][1];
          }
          if(isset($arr_anio_tipo[$value->anio.'-1'])){
            $vpriv = $arr_anio_tipo[$value->anio.'-1'][1];
          }
          if(isset($arr_anio_tipo[$value->anio.'-2'])){
            $vpart = $arr_anio_tipo[$value->anio.'-2'][1];
          }
        @endphp
        ['{{$value->anio}}',{{round($value->valor,2)}},{{round($vpub,2)}}, {{round($vpart + $vpriv,2)}}], @endforeach ]);

      //instantiate our chart object
      var chart = new google.visualization.ColumnChart (document.getElementById('chart'));

      //define options for visualization
      var options = {width: '100%' , height: 240, is3D: true, title: 'Valor de Exámenes por año'};

      //draw our chart
      chart.draw(dataTable, options);

  }


  google.setOnLoadCallback (createChart1);
  function createChart1() {

      //create data table object
      var dataTable = new google.visualization.DataTable();

      //define columns
      dataTable.addColumn('string','Mes');
      dataTable.addColumn('number', 'Total');
      dataTable.addColumn('number', 'Publico');
      dataTable.addColumn('number', 'Privado');



      //define rows of data
      @php $mes = ['', 'ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];@endphp
      dataTable.addRows([@foreach($or_anio_mes as $value)
        @php $vpub2 = 0; $part2 = 0; $priv2 = 0; $vpart2 = 0; $vpriv2 = 0;
          if(isset($arr_aniomes_tipo[$anio.'-'.$value->mes.'-0'])){
            $vpub2 = $arr_aniomes_tipo[$anio.'-'.$value->mes.'-0'][1];
          }
          if(isset($arr_aniomes_tipo[$anio.'-'.$value->mes.'-1'])){
            $vpriv2 = $arr_aniomes_tipo[$anio.'-'.$value->mes.'-1'][1];
          }
          if(isset($arr_aniomes_tipo[$anio.'-'.$value->mes.'-2'])){
            $vpart2 = $arr_aniomes_tipo[$anio.'-'.$value->mes.'-2'][1];
          }
        @endphp
        ['{{$mes[$value->mes]}}',{{round($value->valor,2)}},{{round($vpub2,2)}},{{round($vpriv2 + $vpart2,2)}}], @endforeach ]);

      //instantiate our chart object
      var chart = new google.visualization.ColumnChart (document.getElementById('chart1'));

      //define options for visualization
      var options = {width: '100%', height: 240, is3D: true, title: 'Valor de Exámenes por mes'};

      //draw our chart
      chart.draw(dataTable, options);

  }

</script>

@endsection
