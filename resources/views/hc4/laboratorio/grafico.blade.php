


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
            <h3 class="box-title">Estadísticas Laboratorio</h3>

          </div>


      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <div style="text-align: center">
        <div id="example1_wrapper" >
          <div class="row">
            <div class="table-responsive">
              <h4 style="background-color: #009999;text-align: center;color: white;">EXÁMENES POR AÑO</h4>
              <table id="example1" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example1_info" style="font-size: 11px;">
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
                    <td style="background-color: #ffe6e6;color: red;">$ @if(isset($arr_anio_tipo[$value->anio.'-0'])){{number_format(round($arr_anio_tipo[$value->anio.'-0'][1],2),2) }}@else 0 @endif</td>
                    <td style="background-color: #ffe6e6;color: red;">$ {{number_format(round($vpart + $vpriv,2),2) }}</td>
                    <td style="background-color: #ffe6e6;color: red;">$ {{number_format(round($value->valor,2),2) }}</td>

                  </tr>
                @endforeach
                </tbody>
              </table>
            </div>
            <div class="col-md-12 col-sm-12">
              <center>
                <canvas id="canvas" style="max-width: 70%;"></canvas>
              </center>
            </div>

          </div>

        </div>
      </div>  
      <form id="anio_mes">
        <div class="row">
          <div class="col-1">
            <label><b>Ingrese año a consultar</b></label>
            
          </div>
          <div class="col-2">
            <!--input type="number" name="anio" value="{{$anio}}" class="form-control" onchange="ver_grafico2();"-->
            <select class="form-control" name="anio" value="{{$anio}}" onchange="ver_grafico2();">
                @php $x=2018; $anio_actual=date('Y'); @endphp 
                @for($x=2018;$x<=$anio_actual;$x++)
                <option @if($x==$anio) selected @endif>{{$x}}</option>
                @endfor
            </select>
          </div>
            
        </div>
        
      </form>
      <div id="examplea_wrapper" >
        <div class="row">
          <div class="table-responsive">
            <h4 style="background-color: #009999;text-align: center;color: white;">EXÁMENES POR MES DEL AÑO {{$anio}}</h4>
            <table id="examplea" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="examplea_info" style="font-size: 11px;">
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
                   <td style="background-color: #ffe6e6;color: red;">$ @if(isset($arr_aniomes_tipo[$anio.'-'.$value->mes.'-0'])){{number_format(round($arr_aniomes_tipo[$anio.'-'.$value->mes.'-0'][1],2),2) }}@else 0 @endif</td>
                  <td style="background-color: #ffe6e6;color: red;">$ {{number_format(round($vpart2 + $vpriv2,2),2) }}</td>
                  <td style="background-color: #ffe6e6;color: red;">$ {{number_format(round($value->valor,2),2) }}</td>
                  <td><center><button class="btn btn-info btn-sm" onclick="ver_mes_doctor('{{$anio}}','{{$value->mes}}')">Por Doctor</button></center></td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
          <div class="col-md-12">
            <center>
              <canvas id="canvas2" style="max-width: 70%;"></canvas>
            </center>
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

  function ver_mes_doctor(anio, mes){
    $.ajax({
            type: 'get',
            url:"{{url('laboratorio/estadistico/hc4')}}/"+anio+'/'+mes,
            success: function(datahtml){
                $("#div_grafico").html(datahtml);
            },
            error: function(data){
                console.log(data);
            }
        })  
  } 


</script>
<script>
    @php
      $array_publico = array();
      $array_particular = array();
      $array_total = array();
    @endphp
    @foreach($or_anio as $value)


      @php $pub = 0; $part = 0; $priv = 0; $vpart = 0; $vpriv = 0;
        if(isset($arr_anio_tipo[$value->anio.'-0'])){
          $vpub = round($arr_anio_tipo[$value->anio.'-0'][1],2);
          array_push($array_publico, $vpub);
        }
        if(isset($arr_anio_tipo[$value->anio.'-1'])){
          $vpriv = round($arr_anio_tipo[$value->anio.'-1'][1],2);
        }
        if(isset($arr_anio_tipo[$value->anio.'-2'])){
          $vpart = round($arr_anio_tipo[$value->anio.'-2'][1],2);
        }
        array_push($array_total, round($value->valor,2));
        array_push($array_particular, $vpart+$vpriv);
      @endphp

    @endforeach
    var color = Chart.helpers.color;
    var barChartData = {
      labels: [@foreach($or_anio as $value) '{{$value->anio}}',  @endforeach],
      datasets: [
      {
        label: 'Total',
        backgroundColor: color(window.chartColors.blue).rgbString(),
        borderColor: window.chartColors.blue,
        borderWidth: 1,
        data: [@foreach($array_total as $arr_total)
          {{$arr_total}},
          @endforeach
        ]
      },
      {
        label: 'Publico',
        backgroundColor: color(window.chartColors.red).rgbString(),
        borderColor: window.chartColors.red,
        borderWidth: 1,
        data: [@foreach($array_publico as $arr_pu)
          {{$arr_pu}},
          @endforeach
        ]
      },

      {
        label: 'Privado',
        backgroundColor: color(window.chartColors.yellow).rgbString(),
        borderColor: window.chartColors.yellow,
        borderWidth: 1,
        data: [@foreach($array_particular as $arr_pri)
          {{$arr_pri}},
          @endforeach
        ]
      },



      ]

    };

    var ctx = document.getElementById('canvas').getContext('2d');
      window.myBar = new Chart(ctx, {
        type: 'bar',
        data: barChartData,
        options: {
          responsive: true,
          legend: {
            position: 'top',
          },
          title: {
            display: true,
            text: 'Examenes por AÑO'
          }
        }
      });
</script>

<script>
    @php
      $array_publico_mes = array();
      $array_particular_mes = array();
      $array_total_mes = array();
    @endphp

    @foreach($or_anio_mes as $value)
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
          array_push($array_publico_mes, round($vpub2,2));
          array_push($array_total_mes, round($value->valor,2));
          array_push($array_particular_mes, round($vpriv2 + $vpart2,2));
        @endphp
    @endforeach
    var color = Chart.helpers.color;
    var barChartData_2 = {
      labels: ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'],
      datasets: [
      {
        label: 'Total',
        backgroundColor: color(window.chartColors.blue).rgbString(),
        borderColor: window.chartColors.blue,
        borderWidth: 1,
        data: [@foreach($array_total_mes as $arr_total)
          {{$arr_total}},
          @endforeach
        ]
      },
      {
        label: 'Publico',
        backgroundColor: color(window.chartColors.red).rgbString(),
        borderColor: window.chartColors.red,
        borderWidth: 1,
        data: [@foreach($array_publico_mes as $arr_pu)
          {{$arr_pu}},
          @endforeach
        ]
      },

      {
        label: 'Privado',
        backgroundColor: color(window.chartColors.yellow).rgbString(),
        borderColor: window.chartColors.yellow,
        borderWidth: 1,
        data: [@foreach($array_particular_mes as $arr_pri)
          {{$arr_pri}},
          @endforeach
        ]
      },



      ]

    };

    var ctx = document.getElementById('canvas2').getContext('2d');
      window.myBar = new Chart(ctx, {
        type: 'bar',
        data: barChartData_2,
        options: {
          responsive: true,
          legend: {
            position: 'top',
          },
          title: {
            display: true,
            text: 'Examenes por Mes'
          }
        }
      });

      function ver_grafico2(){
        
          $.ajax({
            type: 'post',
            url:"{{route('hc4/laboratorio.grafico')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            data: $("#anio_mes").serialize(),
            datatype: "html",
            success: function(datahtml){

                $("#div_grafico").html(datahtml);
            },
            error: function(data){
                console.log(data);
            }
        })
      }
</script>
