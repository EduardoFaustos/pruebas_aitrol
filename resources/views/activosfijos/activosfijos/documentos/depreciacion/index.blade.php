<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
@extends('activosfijos.documentos.depreciacion.base')
@section('action-content')
<style>
  div.dataTables_wrapper {
    width: 95%;
    margin: 0 auto;
  }
</style>
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<!-- Main content -->

<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Activos Fijos</a></li>
      <li class="breadcrumb-item"><a href="#">Documentos</a></li>
      <li class="breadcrumb-item"><a href="#">Depreciaci&oacute;n Activo Fijo</a></li>
    </ol>
  </nav>
  <div class="box">
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">BUSCADOR</label>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" name="frm_buscar" id="frm_buscar" action="{{ route('activosfijos.depreciacion.buscar') }}">
        {{ csrf_field() }}

        <div class="form-group col-md-3 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
          <label for="tipo" class="texto col-md-3 control-label">Tipo:</label>
          <div class="col-md-9 px-0">
            <select class="form-control" id="tipo" name="tipo">
              <option value="">Seleccione...</option>
              @foreach ($tipos as $value)
              <option value="{{ $value->id }}">{{ $value->nombre }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group col-md-3 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
          <label for="desde" class="texto col-md-3 control-label">Desde:</label>
          <div class="col-md-9 px-0">
            <input type="date" name="desde" id="desde" class="form-control" value="{{$fecha_desde}}">
          </div>
        </div>

        <div class="form-group col-md-3 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
          <label for="hasta" class="texto col-md-3 control-label">Hasta:</label>
          <div class="col-md-9 px-0">
            <input type="date" name="hasta" id="hasta" class="form-control" value="{{$fecha_hasta}}">
          </div>
        </div>
        <div class="form-group col-md-1" style="padding-left: 13px;padding-right: 5px;">
          <button type="submit" class="btn btn-primary">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
          </button>
        </div>
      
        <div class="form-group col-md-1" style="padding-left: 5px;padding-right: 5px;">
          <button type="submit" class="btn btn-primary" formaction="{{route('activofjo.excel_depreciacion')}}"> <span class="fa fa-download">
            </span> Exportar</button>
        </div>
        <div class="form-group col-md-1" style="padding-left: 5px;padding-right: 5px;">
          <button type="submit" class="btn btn-danger" formtarget="_blank" formaction="{{route('activofjo.depreciacion.pdf_depreciacionesmen')}}">
            <span class="fa fa-file-pdf-o"></span> PDF</button>
        </div>


      </form>

    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">DEPRECIACIÓN DE ACTIVOS FIJOS</label>
      </div>
    </div>

    <div class="box-body dobra">
      <div class="form-group col-md-12">
        <div class="form-row">
          <div id="contenedor">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
              <div class="row">



                @include('activosfijos.documentos.depreciacion.depreciaciones')

                <!-- /.col -->

                <!-- /.row -->

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  {{-- <input type="hidden" name="filfecha" id="filfecha" value="{{$fecha}}"> --}}


</section>

<!-- /.content -->
<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
  $(document).ready(function() {



    $('#tbl_depreciacion').DataTable({
      'paging': false,
      'lengthChange': false,
      'searching': false,
      'ordering': false,
      'info': false,
      'autoWidth': false,
      'scrollCollapse': true,
    });

    tinymce.init({
      selector: '#hc'
    });

    $('input[type="checkbox"].flat-green').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass: 'iradio_flat-green'
    });

    $('input[type="checkbox"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-red',
      radioClass: 'iradio_flat-red'
    });

  });

  $(function() {
    $('#fecha').datetimepicker({
      format: 'DD/MM/YYYY',
    });
    // $("#fecha").on("dp.change", function (e) {
    //     //buscar();
    // });

  });


  $("#btn_guardar").click(function() {
    var miTabla = document.getElementById("tbl_detalles");
    var valida = 0;
    for (i = 0; i < miTabla.rows.length; i++) {
      if (miTabla.rows[i].getElementsByTagName("input")[0].checked) {
        valida = 1;
        break;
      }
    }
    if (valida == 0) {
      // alert("Ningun activo ha sido seleccionado.");
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Ningún activo ha sido seleccionado.'
      });
    } else {
      $("#store_reporte_master").submit();
    }
  });


  $("#btn_buscar").click(function() {
    // $("#filtipo").val($("#tipo").val()); 
    alert("consultar");
    $("#frm_buscar").submit();
  });

  function seleccionar_todo(checked, tablename) {
    var miTabla = document.getElementById(tablename);
    for (i = 0; i < miTabla.rows.length; i++) {
      miTabla.rows[i].getElementsByTagName("input")[0].checked = checked;
    }
  }

  // window.addEventListener('load', () => {

  //   //Finire
  //   let dia = document.getElementById('hasta').value;
  //   let day = new Date(dia);
  //   var last = new Date(new Date(new Date().setMonth(day.getMonth())).setDate(0)).getDate();

  //   let sd = new Date();
  //   let daySetting =  (`${sd.getFullYear()}-${sd.getMonth() + 1 }-${sd.getDate()}`);
  //   document.getElementById("hasta").value = daySetting;

  // });
</script>
@endsection