<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

@php
$rolUsuario = Auth::user()->id_tipo_usuario;
@endphp

<style type="text/css">
  .table>tbody>tr>td,
  .table>tbody>tr>th {
    padding: 0.4%;
  }

  .dropdown-menu>li>a {
    color: white !important;
    padding-left: 3px !important;
    padding-right: 3px !important;
    font-size: 12px !important;
  }

  .dropdown-menu>li>a:hover {
    background-color: #008d4c !important;
  }

  .cot>li>a:hover {
    background-color: #00acd6 !important;
  }

  .p {
    font-size: 14px;
  }
</style>

<div class="modal-header" style="padding-top: 5px; padding-bottom: 1px;">
  <!--<button type="button" class="close" data-dismiss="modal">&times;</button>
  <h4 class="modal-title">Órdenes de Exámenes de Laboratorio del {{$seguro->nombre}}</h4>-->
  <div class="row" style="border-bottom: 1px solid black;">
    <div class="col-md-2">
      <a class="btn btn-light" data-dismiss="modal">
        <h1 style="font-size: 12px; margin:0;">
          <img style="width: 30px;height:23px" src="{{asset('/')}}hc4/img/exit.png">
          <label style="font-size: 14px">Cerrar</label>
        </h1>
      </a>
    </div>
    <div class="col-md-4">
    </div>
    <div class="col-md-6">
      <span id="Label8" style="color:#003366;font-family:Arial;font-size:14pt;font-weight:bold;">Órdenes de Exámenes de Laboratorio del {{$seguro->nombre}}</span>
    </div>
  </div>
</div>
<div class="modal-body">
  <form id="form_labs">
    {{ csrf_field() }}
    <input type="hidden" name="id" value="{{$ap->id}}">
    <div class="form-group col-md-4 col-xs-6">
      <label for="fecha" class="col-md-3 control-label">Desde</label>
      <div class="col-md-9">
        <div class="input-group date">
          <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </div>
          <input type="text" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off">
          <div class="input-group-addon">
            <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar_labs();"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="form-group col-md-4 col-xs-6">
      <label for="fecha_hasta" class="col-md-3 control-label">Hasta</label>
      <div class="col-md-9">
        <div class="input-group date">
          <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </div>
          <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
          <div class="input-group-addon">
            <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar_labs();"></i>
          </div>
        </div>
      </div>
    </div>
  </form>
  <button type="button" class="btn btn-primary" id="boton_buscar">
    <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
  </button>
  <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
    <div class="row" id="listado">
      <div class="col-md-12">
        &nbsp;
      </div>
      <div class="table-responsive col-md-12" style="min-height: 210px;">
        <div class="col-md-2">
          <label for="">Sel.</label>
          
        </div>
        <div class="col-md-2">
          <label style="text-align: center;" for="">Fecha</label>

        </div>
        <div class="col-md-2">
          <label for="">Covenio</label>
        </div>
        <!--div class="col-md-1">
          <label for="">Tipo</label>
        </div>
        <div class="col-md-1">
          <label for="">Cant.</label>
        </div>
        <div class="col-md-1">
          <label for="">Valor</label>
        </div-->
        <div class="col-md-2">
          <label for="">Resultados(%)</label>
        </div>
        <div class="col-md-2">
          <label for="">Resultados</label>
        </div>

        <div class="col-md-2">
          <label for="">Acción</label>
        </div>


        @foreach ($ordenes as $value)
        <div class="col-md-12">
        &nbsp;
      </div>

        <div class="col-md-2">
          <button type="button" class="btn btn-xs btn-success" onclick="detalles_examenes('{{$value->id}}')"><span class="glyphicon glyphicon-ok-sign"></span></button>

        </div>
        <div class="col-md-2">
          <p class="p"> {{substr($value->fecha_orden,0,10)}}</p>

        </div>
        <div class="col-md-2">
          <p class="p"> {{$value->seguro->nombre}} / {{$value->empresa->nombre_corto}}</p>
        </div>
        <!--div class="col-md-1">
          @if($value->protocolo!=null)
          <span class="label pull-right bg-primary" style="font-size: 10px">{{$value->protocolo->pre_post}}</span>
          @endif
        </div>
        <div class="col-md-1">
          {{$value->cantidad}}
        </div>
        <div class="col-md-1">
          {{$value->total_valor}}
        </div-->
        <div class="col-md-2">
          <div class="progress progress">
            <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" id="td{{$value->id}}">
              <span id="sp{{$value->id}}" style="color: black;"></span>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <button type="button" class="btn btn-success btn-xs" onclick="descargar({{$value->id}});">
            <span style=" font-size: 10px">Resultados</span>
          </button>
        </div>

        <div class="col-md-2">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <a href="{{ route('orden.descargar', ['id' => $value->id]) }}" target="_blank" class="btn btn-block btn-info btn-xs" style="padding: 0px;">
            <span>Orden</span>
          </a>
        </div>
        <div class="col-md-12" id="detalle_orden{{$value->id}}">
  
      </div>
      @endforeach
      <div class="col-md-12">
        &nbsp;
      </div>
    </div>
  </div>
 
</div>
</div>


<div class="modal-footer">
  <!--<button type="button" class="btn btn-primary" id="laboratorio_cerrar" data-dismiss="modal">Close</button>-->
</div>


<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>

<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">
  $(document).ready(function($) {

    @foreach($ordenes as $value)

    $.ajax({
      type: 'get',
      url: "{{ route('resultados.puede_imprimir',['id' => $value->id]) }}",

      success: function(data) {

        if (data.cant_par == 0) {
          var pct = 0;
        } else {
          var pct = data.certificados / data.cant_par * 100;
        }
        //alert(pct);
        $('#td{{$value->id}}').css("width", Math.round(pct) + "%");
        $('#sp{{$value->id}}').text(Math.round(pct) + "%");
        if (pct < 10) {
          $('#td{{$value->id}}').addClass("progress-bar-danger");
        } else if (pct >= 10 && pct < 90) {
          $('#td{{$value->id}}').addClass("progress-bar-warning");
        } else {
          $('#td{{$value->id}}').addClass("progress-bar-success");
        }


      },


      error: function(data) {


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

    $("#fecha").on("dp.change", function(e) {
      buscar_labs();
    });

    $("#fecha_hasta").on("dp.change", function(e) {
      buscar_labs();
    });

    $('#example2_labs').DataTable({
      'paging': false,
      'lengthChange': false,
      'searching': false,
      'ordering': true,
      'info': false,
      'autoWidth': false
    })



  });

  $('#doctor').on('hidden.bs.modal', function() {
    location.reload();
    $(this).removeData('bs.modal');
  });



  function descargar(id_or) {
    var cert = $('#sp' + id_or).text();
    if (cert == '0%') {
      alert("Sin Exámenes Ingresados");
    } else {
      //location.href = '{{url('resultados/imprimir')}}/'+id_or;
      window.open('{{url('
        resultados / imprimir ')}}/' + id_or, '_blank');
    }

  }

  function buscar_labs() {
    $.ajax({
      type: 'post',
      url: "{{ route('planilla.buscar_labs') }}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: $("#form_labs").serialize(),
      success: function(data) {

        console.log(data);
        $('#listado').empty().html(data);
      },
      error: function(data) {
        console.log(data);
      }
    });
  }
</script>