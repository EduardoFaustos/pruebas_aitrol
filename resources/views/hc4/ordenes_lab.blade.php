<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">


<style type="text/css">

  .table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
  }

  .dropdown-menu>li>a{
    color:white !important;
    padding-left: 3px !important;
    padding-right: 3px !important;
    font-size: 12px !important;
  }

  .dropdown-menu>li>a:hover{
    background-color:#008d4c !important;
  }

  .cot>li>a:hover{
    background-color:#00acd6 !important;
  }

  .titulo{
    font-family: 'Helvetica general' !important;
    border-bottom:  solid 1px #124574 !important;
  }

  .boton-2{
    font-size: 10px ;
    width: 100%;
    height: 100%;
    background-color: #124574;
    color: white;
    border-radius: 5px;
  }

  .color{
    font-size: 12px;
    color: #124574;
  }

  #example2_lab_wrapper .row{
    width: 100% !important;
  }

</style>

<section class="content" style="padding-left: 0px; padding-right: 0px;padding-top: 0px;">
  <div class="box box" style="border-radius:20px;">
    {{ csrf_field() }}
    <div class="box-header" style="background-color: #124574; color: white; font-family: 'Helvetica general3';border-bottom: #124574;text-align: center;padding: 5px;">
      <div class="row">
        <div class="col-6">
          <h4 align="center">&Oacute;RDENES DE LABORATORIO</h4>
        </div>
        @php $crm = Auth::user()->id; @endphp
        @if($crm == '1307189140' || $crm == '9666666666')
        <div class="col-6">
          <button type="button" onclick="ver_grafico();" id="agenda_semana" class="btn btn-danger" style="color:white; background-color: #124574; border-radius: 5px; border: 2px solid white;"> Visualizar Estadistico</button>
        </div>
        @endif
      </div>
    </div>

    <div class="box-body" style="border: 2px solid #124574;border-radius:5px;" id="area_trabajo">
      <div id="div_grafico" class="col-12 table-responsive"  style="min-height: 210px;">
        <table id="example2_lab" class="table " role="grid" aria-describedby="example2_info" style="font-size: 11px;overflow: none; width: 100%;">
          <thead>
            <tr>
              <th class="color titulo">Fecha</th>
              <th class="color titulo">Nombres</th>
              <th class="color titulo">Seguro/Convenio</th>
              <th class="color titulo">Tipo</th>
              <th class="color titulo">Creada</th>
              <th class="color titulo">Modificada</th>
              <th class="color titulo">Cantidad</th>
              <th class="color titulo">Ex&aacutemenes Ingresados(%)</th>
              <th class="color titulo">Resultados</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($ordenes_lab as $value)
            <tr role="row">
              <td class="color">{{substr($value->fecha_orden,0,10)}}</td>
              <td class="color">
                  {{$value->papellido1}}
                @if($value->papellido2=='N/A'||$value->papellido2=='(N/A)')
                @else
                  {{ $value->papellido2 }}
                @endif
                  {{$value->pnombre1}}
                @if($value->pnombre2=='N/A'||$value->pnombre2=='(N/A)')
                @else
                  {{ $value->pnombre2 }}
                @endif </td>
              <td class="color">{{$value->snombre}}/{{$value->nombre_corto}}</td>
              <td class="color">
                @if($value->estado=='-1')
                  <span style="color: red;">COTIZ.</span>
                @else
                  {{$value->pre_post}}
                @endif
              </td>
              <td class="color">{{substr($value->cnombre1,0,1)}}{{$value->capellido1}}</td>
              <td class="color">{{substr($value->mnombre1,0,1)}}{{$value->mapellido1}}</td>
              <td class="color">{{$value->cantidad}}</td>
              <!--<td class="color">{{$value->total_valor}}</td>-->
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
                    <div class="btn-group" >
                      <button type="button" class="btn btn-info boton-2"
                      onclick="descargar({{$value->id}});"><span >Resultados</span>
                      </button>
                      <!--<button type="button" class="btn btn-info boton-2 dropdown-toggle"
                       data-toggle="dropdown" aria-expanded="false">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                      </button>
                      <ul class="dropdown-menu" role="menu" style="background-color: #124574;padding: 2px;min-width: 100px;">
                        <li ><a  href="{{ route('resultados.imprimir3',['id' => $value->id]) }}" >Formato Gastro</a></li>
                      </ul>-->
                    </div>
                  </div>
                @endif
              </td>
              <!--<td>
                @if($value->stipo!='0')
                  <div class="col-md-12">
                    <div class="btn-group" >
                        <button type="button" class="btn btn-info boton-2" onclick="window.open('{{ route('cotizador.imprimir', ['id' => $value->id]) }}','_blank');"><span >Cotización</span></button>
                        <button type="button" class="btn btn-info boton-2 dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                          <span class="caret"></span>
                          <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu cot" role="menu" style="background-color: #124574;padding: 2px;min-width: 100px;">
                          <li ><a style="width: 100%; height: 100%"  href="{{ route('cotizador.imprimir_gastro', ['id' => $value->id]) }}" target="_blank">Formato Gastro</a></li>
                        </ul>
                    </div>
                  </div>
                @else
                  <div class="col-12" >
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <a style="width: 100%; height: 100%" href="{{ route('orden.descargar', ['id' => $value->id]) }}" target="_blank" class="btn btn-info boton-2">
                    <span >Orden</span>
                    </a>
                  </div>
                @endif
              </td>-->
              <!--<td>
                @if($value->id_empresa != null && $value->id_empresa != '9999999999')
                  @if($value->stipo!='0')

                    <div class="form-group col-md-6" style="padding: 2px;">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <a target="_blank"  href="{{ route('cotizador.imprimir_orden', ['id' => $value->id]) }}" class="btn btn-info boton-2" style="padding: 0px;">
                    <span class="fa fa-download"></span> Orden
                    </a>

                  </div>
                  @else
                    <div class="form-group col-md-6" style="padding: 5px;">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">

                      <a href="{{ route('orden.detalle', ['id' => $value->id, 'dir' => 'sup']) }}" class="btn btn-info boton-2" style="padding: 0px;">
                      <span class="fa fa-download"></span> Orden
                      </a>

                    </div>
                  @endif
                @endif
              </td> -->
            </tr>
            @endforeach
          </tbody>
        </table>
         <label class="color" style="padding-left: 15px;font-size: 15px">Total de Registros: {{$ordenes_lab->count()}}</label>
      </div>
    </div>
  </div>
</section>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">
  $('#example2_lab').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : true,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : true,
      'order'       : [[ 1, "asc" ]]
    });

   $(document).ready(function($){
      @foreach ($ordenes_lab as $value)
          $.ajax({
          type: 'get',
          url:"{{ route('barra.progress_imprimir',['id' => $value->id]) }}",

          success: function(data){
            if(data.cant_par==0){
              var pct = 0;
            }else{

              var pct = data.certificados/data.cant_par*100;
              //alert(pct);


            }
            //alert(pct);
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
    });

    function descargar(id_or){

      var cert = $('#sp'+id_or).text();
      //alert(id_or);

      if(cert=='0%'){
        alert("Sin Exámenes Ingresados");
      }else{
        window.open('{{url('resultados/imprimir')}}/'+id_or,'_blank');
      }
    }

</script>
<script type="text/javascript">
      function ver_grafico(){

          $.ajax({
            type: 'post',
            url:"{{route('hc4/laboratorio.grafico')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            data: '',
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
