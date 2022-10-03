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
        <div class="col-md-12" id="pentax">

        </div>

        <div class="col-md-12">
          <h3 class="box-title"><b>Control de Exámenes de Laboratorio</b></h3>
        </div>

    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">

      <form method="POST" action="{{route('orden.search_control')}}" id="lab_control">
        {{ csrf_field() }}
        <div class="form-group col-md-2 col-xs-6">
            <label for="orden" class="col-md-12 control-label">Orden</label>
            <div class="col-md-12">
                <input type="text" class="form-control input-sm" name="orden" id="orden" autocomplete="off" value="{{$norden}}">
            </div>
        </div>

        <div class="form-group col-md-2 col-xs-6">
            <label for="fecha" class="col-md-12 control-label">Desde</label>
            <div class="col-md-12">
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

          <div class="form-group col-md-2 col-xs-6">
            <label for="fecha_hasta" class="col-md-12 control-label">Hasta</label>
            <div class="col-md-12">
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

          <div class="form-group col-md-2 col-xs-6">
            <label for="seguro" class="col-md-12 control-label">Seguro</label>
              <div class="col-md-12">
                <select id="seguro" name="seguro" class="form-control input-sm" onchange="buscar();">
                  <option value="">TODOS</option>
                  @foreach ($seguros as $value)
                    <option @if(!is_null($seguro))@if($seguro == $value->id) selected @endif @endif value="{{$value->id}}">{{$value->nombre}}</option>
                  @endforeach
                </select>
              </div>
          </div>

          <div class="form-group col-md-4 col-xs-6">
            <label for="nombres" class="col-md-12 control-label">Paciente</label>
            <div class="col-md-12">
              <div class="input-group">
                <input value="@if($nombres!=''){{$nombres}}@endif" type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="Nombres y Apellidos" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('nombres').value = '';"></i>
                </div>
              </div>
            </div>
          </div>

        <div class="form-group col-md-2 col-xs-6">
        <button type="submit" class="btn btn-primary" id="boton_buscar">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar</button>
        </div>

        <div class="form-group col-md-2 col-xs-6">
          <button type="submit" class="btn btn-primary" formaction="{{route('orden.reporte_index')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Reporte</button>
        </div>

        <button type="submit" class="btn btn-success" formaction="{{route('orden.reporte_detalle_covid')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Covid</button>


        <!--div class="form-group col-md-3">
          <a class="btn btn-primary" href="{{ route('orden_particular.crear_particular')}}"><span class="ionicons ion-ios-flask"></span> Orden Particular</a>
        </div-->
      </form>


    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
            <thead>
              <tr role="row">
                <th width="5">Orden</th>
                <th width="5">Fecha</th>
                <th width="20">Nombres</th>
                <th width="5">Seguro</th>
                <th width="5">Tipo</th>
                <th width="5">Creada</th>
                <th width="10">Toma Muestra</th>
                <th width="10">Exámenes Ingresados(%)</th>
                <th width="5">Biometria</th>
                <th width="5">Bioquímica</th>
                <th width="5">Manual</th>
                <th width="20">Acción</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($ordenes as $value)
              <tr role="row" >
                <td>{{$value->id}}</td>
                <td>{{substr($value->fecha_orden,0,10)}}</td>
                <td>{{$value->paciente->apellido1}} @if($value->paciente->apellido2=='N/A'||$value->paciente->apellido2=='(N/A)') @else{{ $value->paciente->apellido2 }} @endif {{$value->paciente->nombre1}} @if($value->paciente->nombre2=='N/A'||$value->paciente->nombre2=='(N/A)') @else{{ $value->paciente->nombre2 }} @endif </td>
                <td>{{$value->seguro->nombre}}</td>
                <td>{{$value->pre_post}}</td>
                <td>{{substr($value->crea->nombre1,0,1)}}{{$value->crea->apellido1}}</td>
                <td>{{$value->toma_muestra}}</td>
                <!--td>{{substr($value->modifica->nombre1,0,1)}}{{$value->modifica->apellido1}}</td-->
                <td>
                  <div class="progress progress">
                    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" id="td{{$value->id}}">
                      <span id="sp{{$value->id}}" style="color: black;"></span>
                    </div>
                  </div>
                </td>
                <td>
                  @if($value->er_biometria=='2')
                    @php $texto = 'LISTO'; $color = 'btn btn-success btn-xs'; @endphp
                  @elseif($value->er_biometria=='1')
                    @php $texto = 'CERTIFICAR'; $color = 'btn btn-warning btn-xs'; @endphp
                  @else
                    @php $texto = 'PENDIENTE'; $color = 'btn btn-danger btn-xs'; @endphp
                  @endif

                  @if($arr_or[$value->id]['hema']>0)
                  <button class="{{$color}}" onclick="resultado({{$value->id}},'1');">
                    <span class="glyphicon glyphicon-check" style="font-size: 10px;"> {{$texto}}</span>
                  </button>
                  @endif
                </td>
                <td>
                  @if($value->er_bioquimica=='2')
                    @php $texto = 'LISTO'; $color = 'btn btn-success btn-xs'; @endphp
                  @elseif($value->er_bioquimica=='1')
                    @php $texto = 'CERTIFICAR'; $color = 'btn btn-warning btn-xs'; @endphp
                  @else
                    @php $texto = 'PENDIENTE'; $color = 'btn btn-danger btn-xs'; @endphp
                  @endif
                  @if($arr_or[$value->id]['bio']>0)
                  <button class="{{$color}}" onclick="resultado({{$value->id}},'2');">
                    <span class="glyphicon glyphicon-check" style="font-size: 10px;"> {{$texto}}</span>
                  </button>
                  @endif
                </td>
                <td>
                  @if($value->er_manual=='2')
                    @php $texto = 'LISTO'; $color = 'btn btn-success btn-xs'; @endphp
                  @elseif($value->er_manual=='1')
                    @php $texto = 'CERTIFICAR'; $color = 'btn btn-warning btn-xs'; @endphp
                  @else
                    @php $texto = 'PENDIENTE'; $color = 'btn btn-danger btn-xs'; @endphp
                  @endif
                  @if($arr_or[$value->id]['man']>0)
                  <button class="{{$color}}" onclick="resultado({{$value->id}},'0');">
                    <span class="glyphicon glyphicon-check" style="font-size: 10px;"> {{$texto}}</span>
                  </button>
                  @endif
                </td>


                <td>
                  <div class="col-md-2" style="padding: 2px;">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    @if($value->toma_muestra!=null)
                    <a href="{{ route('orden.codigo_barras', ['id' => $value->id]) }}" target="_blank" class="btn btn-block btn-success btn-xs" style="padding: 0px;">
                      <span class="fa fa-barcode"></span>
                    </a>
                    @else
                    <button class="btn btn-block btn-danger btn-xs" onclick="codigo_barras('{{$value->id}}')" style="padding: 0px;"><span class="fa fa-barcode"></span></button>
                    @endif
                  </div>

                  @if($value->seguro->tipo!='0')
                    @if(Auth::user()->id_tipo_usuario=='1')
                    <div class="col-md-4" style="padding: 2px;">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      <a href="{{ route('cotizador.editar', ['id' => $value->id]) }}" class="btn btn-block btn-warning btn-xs" style="padding: 0px;">
                      <!--a href="{{ route('orden.edit2', ['id' => $value->id,'dir' => 'rec']) }}" class="btn btn-block btn-warning btn-xs" style="padding: 0px;"-->
                      <span class="glyphicon glyphicon-edit"></span>
                      </a>
                    </div>
                    @endif

                    <div class="form-group col-md-4" style="padding: 2px;">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      <!--a href="{{ route('orden.detalle', ['id' => $value->id,'dir' => 'rec']) }}" class="btn btn-block btn-success btn-xs" style="padding: 0px;"-->
                      <a target="_blank" href="{{ route('cotizador.imprimir_orden', ['id' => $value->id]) }}" class="btn btn-block btn-success btn-xs" style="padding: 0px;">
                      <span class="fa fa-download"></span> Orden
                      </a>
                    </div>
                  @else
                    @if(Auth::user()->id_tipo_usuario=='1')
                      <div class="col-md-2" style="padding: 2px;">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('orden.edit1_c', ['id' => $value->id]) }}" class="btn btn-block btn-warning btn-xs" style="padding: 0px;">
                        <span class="glyphicon glyphicon-edit"></span>
                        </a>
                      </div>
                    @endif
                  <div class="form-group col-md-2" style="padding: 2px;">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <!--a href="{{ route('orden.descargar', ['id' => $value->id]) }}" target="_blanck" class="btn btn-block btn-success btn-xs" -->
                    <a href="{{ route('orden.detalle', ['id' => $value->id, 'dir' => 'CON']) }}" class="btn btn-block btn-success btn-xs" style="padding: 0px;">
                    <span class="fa fa-download"></span>
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
            {{ $ordenes->appends(Request::only(['fecha', 'fecha_hasta', 'nombres', 'seguro']))->links()}}
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


            @if($fecha!=null) defaultDate: '{{$fecha}}', @endif

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

    pentax_semaforo();

    $(".breadcrumb").append('<li class="active">Órdenes</li>');

    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
       'order'       : [[ 1, "desc" ]]
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


function pentax_semaforo(){

  $.ajax({
    type: 'get',
    url:"{{route('orden.pentax_semaforo')}}",
    //headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
    //datatype: 'json',
    //data: $("#cie10"),
    success: function(data){
      //alert(data);
      $("#pentax").empty().html(data);

    },
    error: function(data){

    }
  })


}

function resultado(id, maq){
  //alert(maq);

  $('#lab_control').attr("action", "{{url('orden/realizar')}}/"+id+"/"+maq);
  //console.log($('#lab_control'));
  $( "#lab_control" ).submit();

}

function codigo_barras(id){

  var confirmar = confirm("Al generar el código se tomará como tomada la muestra al paciente");
  if(confirmar){
    $.ajax({
      type: 'get',
      url:"{{url('examen_orden/toma_muestra')}}/"+id,
      
      success: function(data){

        window.open("{{url('imprimir/codigo_barras/laboratorio')}}/"+id, "_blank");
        location.reload();  

      },
      error: function(data){

      }
    })

    
  }
  

}






vartiempo = setInterval(function(){ pentax_semaforo(); }, 5000);

vartiempo2 = setInterval(function(){ buscar(); }, 500000);

</script>

@endsection
