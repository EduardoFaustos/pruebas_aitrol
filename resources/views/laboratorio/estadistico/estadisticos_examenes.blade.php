<div class="row">
  <div class="col-md-12">
    <div class="table-responsive">
      <h4 style="background-color: #009999;text-align: center;color: white;">PRODUCCION DE EXAMENES DEL AÑO {{$anio}}</h4>
      <table id="tabla_examenes" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="examplea_info" style="font-size: 11px;">
        <thead>
          <tr role="row">
            
            <th style="width: 60%">Exámenes</th>
            <th style="background-color: #1affff;width: 10%">Cantidad</th>
            <th style="background-color: #1affff:width: 10%">Valor</th>
          </tr>
        </thead>
        <tbody>
        @php $num = 0; @endphp
        @foreach($or_anio as $value)
          <tr role="row" >@php $num ++; @endphp
            
            <td style="text-align: left;">{{$value->nombre}}</td>
            <td>{{$value->cantidad}}</td>
            <td>$ {{number_format(round($value->valor,2))}}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div> 
  @for($i=1;$i<=12;$i++) 
  <div class="col-md-12">
    <center>
      <canvas id="canvas_ex{{$i}}" style="max-width: 70%;height: 250px;"></canvas>
    </center>
  </div>
  @endfor
</div>
<script type="text/javascript">
  $('#tabla_examenes').DataTable({
    'paging'      : true,
    'lengthChange': true,
    'searching'   : true,
    'ordering'    : true,
    'info'        : true,
    'autoWidth'   : false,
    'order'       : [[ 2, "desc" ]],
    "language": {
      "search": "Buscar:",
      "lengthMenu": "Mostrar _MENU_ Examenes" 
    },
    
  });
</script>
<script>
    @php $mes = [0, 'ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC']
    var color = Chart.helpers.color;
    @for($i=1;$i<=12;$i++)
      @php
        $examenes_mes = $or_anio_mes->where('eo.mes',$i)->get(); 
      @endphp
      var barChartData_2 = {
      labels: ['{{@mes[$i]}}'],
      datasets: 
      @foreach( $examenes_mes as $value)
      [
      {
        label: 'Producción',
        backgroundColor: color(window.chartColors.blue).rgbString(),
        borderColor: window.chartColors.blue,
        borderWidth: 1,
        data: [@foreach($array_total_mes as $arr_total)
          {{$arr_total}},
          @endforeach
        ]
      },
      {
        label: 'Facturación',
        backgroundColor: color(window.chartColors.red).rgbString(),
        borderColor: window.chartColors.red,
        borderWidth: 1,
        data: [@foreach($array_facturado as $fact)
          {{$fact}},
          @endforeach
        ]
      },

      {
        label: 'Pagado',
        backgroundColor: color(window.chartColors.yellow).rgbString(),
        borderColor: window.chartColors.yellow,
        borderWidth: 1,
        data: [@foreach($array_pagado as $pago)
          {{$pago}},
          @endforeach
        ]
      },



      ]

    };
      
        
      @endforeach  
    @endfor
      
    
    

    var ctx = document.getElementById('canvas3').getContext('2d');
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

</script>
