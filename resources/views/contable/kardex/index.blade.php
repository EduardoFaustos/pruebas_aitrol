@extends('contable.balance_general.base')
@section('action-content')

<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
  <!-- Main content -->
  <section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.Compras')}}</a></li>
      <li class="breadcrumb-item"><a href="../">Kardex</a></li>
    </ol>
  </nav>
    <div class="box">

      <div class="row head-title">
        <div class="col-md-12 cabecera">
            <label class="color_texto" for="title">{{trans('contableM.Buscador')}}</label>
        </div>
      </div>

      <!-- /.box-header -->
      <div class="box-body dobra">
        <form method="POST" id="reporte_master" action="{{ route('kardex.show') }}" >
          {{ csrf_field() }}

          <div class="form-group col-md-4 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha" class="texto col-md-3 control-label">{{trans('contableM.producto')}}</label>
            <div class="col-md-9">
                <select id="producto_id" name="producto_id"  class="form-control select2_cuentas" style="width: 100%;" required>
                    <option> </option>
                    @foreach($productos as $value)
                        <option value="{{$value->id}}"> {{$value->codigo}} -  {{$value->nombre}}</option>
                    @endforeach
                </select>
            </div>
          </div>



          <div class="form-group col-md-4 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha_hasta" class="texto col-md-3 control-label">{{trans('contableM.Fechahasta')}}</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta"  value="@if(isset($fecha_hasta)) {{@$fecha_hasta}} @else {{ date('d/m/Y') }} @endif" required autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
                </div>
              </div>
            </div>
          </div>


          <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
            <button type="submit" class="btn btn-primary" id="boton_buscar">
                  <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
            </button>
            <button type="button" class="btn btn-primary" id="btn_exportar">
              <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
            </button>
          </div>
        </form>
        <form method="POST" id="print_reporte_master" action="{{ route('kardex.exportar') }}" target="_blank">
          {{ csrf_field() }}
          <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="{{@$fecha_desde}}">
          <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{@$fecha_hasta}}">
          <input type="hidden" name="filproducto_id" id="filproducto_id" value="{{@$producto_id}}">
          <input type="hidden" name="exportar" id="exportar" value="0">
        </form>

      </div>
      <!-- /.box-body -->
    </div>
  </section>


  <!-- /.content -->
<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">

    $(document).ready(function(){
        $('.select2_cuentas').select2({
            tags: false
        });

        $('#producto_id').val({{ $producto_id }});
        $('#producto_id').select2().trigger('change');

    });

    $('#seguimiento').on('hidden.bs.modal', function(){
        $(this).removeData('bs.modal');
    });


    $('#cuenta').on('select2:select', function (e) {
        var cuenta = $('#cuenta').val();
        $('#nombre').val(cuenta);
        $('#nombre').select2().trigger('change');
    });


    $('#nombre').on('select2:select', function (e) {
        var nombre = $('#nombre').val();
        $('#cuenta').val(nombre);
        $('#cuenta').select2().trigger('change');
      });

    $( "#btn_imprimir" ).click(function() {
        $("#filfecha_desde").val($("#fecha_desde").val());
        $("#filfecha_hasta").val($("#fecha_hasta").val());
        $("#filcuentas_detalle").val($("#cuentas_detalle").val());
        $("#filmostrar_detalles").val($("#mostrar_detalles").val());
        $("#print_reporte_master" ).submit();
    });

    $( "#btn_exportar" ).click(function() { alert();
        $("#filfecha_desde").val($("#fecha_desde").val());
        $("#filfecha_hasta").val($("#fecha_hasta").val());
        $("#filproducto_id").val($("#producto_id").val());
        $("#exportar").val(1);
        $("#print_reporte_master" ).submit();
    });

    $(document).ready(function(){

      tinymce.init({
        selector: '#hc'
      });

      $('input[type="checkbox"].flat-green').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass   : 'iradio_flat-green'
      });

      $('input[type="checkbox"].flat-red').iCheck({
        checkboxClass: 'icheckbox_flat-red',
        radioClass   : 'iradio_flat-red'
      });


    });

    $(function () {
        $('#fecha_desde').datetimepicker({
            // format: 'YYYY/MM/DD',
            format: 'DD/MM/YYYY',
            // defaultDate: '{{$fecha_desde}}',
            });
        $('#fecha_hasta').datetimepicker({
            // format: 'YYYY/MM/DD',
            format: 'DD/MM/YYYY',
            // defaultDate: '{{$fecha_hasta}}',

            });
        $("#fecha_desde").on("dp.change", function (e) {
          verifica_fechas();
        });

         $("#fecha_hasta").on("dp.change", function (e) {
          verifica_fechas();
        });

        //alert('{{$fecha_hasta}}');

  });
  function verifica_fechas(){
    if(Date.parse($("#fecha_desde").val()) > Date.parse($("#fecha_hasta").val())){
      Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Verifique el rango de fechas y vuelva consultar'
      });
    }
  }
  function buscar()
  {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }
</script>
@endsection
