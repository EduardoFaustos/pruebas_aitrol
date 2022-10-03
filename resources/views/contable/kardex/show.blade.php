@extends('contable.balance_general.base')
@section('action-content')
<style>
  p.s1 {
    margin-left: 10px;
    font-size: 14px;
    font-weight: bold;
  }

  p.s2 {
    margin-left: 20px;
    font-size: 12px;
    font-weight: bold;
  }

  p.s3 {
    margin-left: 30px;
    font-size: 10px;
    font-weight: bold;
  }

  p.s4 {
    margin-left: 60px;
    font-size: 10px;
  }

  p.t1 {
    font-size: 14px;
    font-weight: bold;
  }

  p.t2 {
    font-size: 12px;
    font-weight: bold;
  }

  p.t3 {
    font-size: 10px;
  }

  .table-condensed>thead>tr>th>td,
  .table-condensed>tbody>tr>th>td,
  .table-condensed>tfoot>tr>th>td,
  .table-condensed>thead>tr>td,
  .table-condensed>tbody>tr>td,
  .table-condensed>tfoot>tr>td {

    padding: 0;
    font-size: 14px !important;
    line-height: 1;
  }
</style>

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
      <form method="POST" id="reporte_master" action="{{ route('kardex.show') }}">
        {{ csrf_field() }}

        <div class="form-group col-md-4 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">{{trans('contableM.producto')}}</label>
          <div class="col-md-9">
            <select id="producto_id" name="producto_id" class="form-control select2_cuentas" style="width: 100%;" required>
              <option> </option>
              @foreach($productos as $value)
              <option value="{{$value->id}}"> {{$value->codigo}} {{$value->nombre}}</option>
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
              <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" value="@if(isset($fecha_hasta)) {{date('d/m/Y',strtotime($fecha_hasta))}} @else {{ date('d/m/Y') }} @endif" required autocomplete="off">
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
            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> {{trans('contableM.Exportar')}}
          </button>
        </div>
      </form>
    </div>
    <!-- /.box-body -->
    <b>Compras del período:</b>
    <div class="content" id="contenedor">
      <div id="tbl_compras_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
        <div class="row">
          <div class="table-responsive col-md-12">
            <table id="tbl_compras" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="tbl_compras_info">
              <thead>
                <tr class="well-dark">
                  <th colspan="2">{{trans('contableM.detalles')}}</th>
                  <th colspan="3" style="text-align:center">Entradas</th>
                  <th colspan="3" style="text-align:center">Salidas</th>
                  <th colspan="3" style="text-align:center">{{trans('contableM.Saldos')}}</th>
                  <th colspan="2" style="text-align:center">{{trans('contableM.observaciones')}}</th>
                </tr>
                <tr class="well-dark">
                  <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.fecha')}}</th>
                  <th width="30%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.producto')}}</th>
                  <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.cantidad')}}</th>
                  <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor unitario</th>
                  <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.total')}}</th>
                  <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.cantidad')}}</th>
                  <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor unitario</th>
                  <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.total')}}</th>
                  <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.cantidad')}}</th>
                  <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor unitario</th>
                  <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.total')}}</th>
                  <th width="20%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.detalle')}}</th>
                </tr>
              </thead>
              <tbody>
                <tr class="well">
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                 
                  <td style="font-weight: bold;">Saldo Anterior</td>
                  <td>{{trans('contableM.fecha')}}</td>
                  <td > @if($getAnterior->fecha!=null) {{ date('d-m-Y',strtotime($getAnterior->fecha))}} @endif </td>
                 
                  <td>&nbsp;</td>
                  <td>{{$getAnterior->cantidad}}</td>
                  <td>{{$getAnterior->valor_unitario}}</td>
                  <td>{{$getAnterior->total}}</td>
                  <td>&nbsp;</td>
                </tr>
                @php
                $getPrice=0;
                $getPriceant=0;
                $getCount=0;
                $getTotal=0;
                $cantidadant=0;
                $anterior= $getAnterior->cantidad;
                if(is_null($anterior)){$anterior=0;}
                $cantidad=$anterior;
                $anteriorprecio= $getAnterior->valor_unitario;
                if(is_null($anteriorprecio)){$anteriorprecio=0;}
                $anteriortotal=$getAnterior->total;
                if(is_null($anteriortotal)){$anteriortotal=0;}
                //dd($anteriorprecio);
                $totalCosto=$anteriortotal;
                $precioCosto=$anteriorprecio;
                $contador=0;
                @endphp
                @foreach ($kardex as $value)
                @php
                $observ="";
                //$cantidad=$value->cantidad; apache_child_terminate
                if($value->movimiento==1){

                $cantidad+=$value->cantidad;

                }else{

                $cantidad= $cantidad-$value->cantidad;

                }

                $getPrice+=$value->valor_unitario;
                $getTotal+=$value->total;






                @endphp

                <tr class="well">
                  <td>{{ date('d-m-Y', strtotime($value->fecha))}}</td>
                  <td>{{ $value->product->nombre}} <br> {{$value->tipo}} {{$value->numero}}</td>
                  @if($value->movimiento==1)
                  @php
                  $observ= DB::table('ct_compras')->find($value->id_movimiento);
                  $totalCosto+=$value->total;
                  if($cantidad>0){
                  $precioCosto=$totalCosto/$cantidad;
                  }else{
                  $precioCosto=0;
                  }


                  @endphp
                  <td>{{ $value->cantidad}}</td>
                  <td>{{ $value->valor_unitario}}</td>
                  <td>{{ $value->total }}</td>

                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  @else
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  @php
                  $totalCosto= $precioCosto*$cantidad;
                  $tots=$precioCosto*$value->cantidad;
                  $observ= DB::table('ct_ventas')->find($value->id_movimiento);
                  
                  @endphp
                  <td>{{ $value->cantidad}}</td>
                  <td>{{ number_format($precioCosto,2) }}</td>
                  <td>{{ number_format($tots,2) }}</td>
                  @endif
                  <td @if($cantidad<0) style="color:red;" @endif> {{ number_format($cantidad,2)}}</td>
                  <td>{{ number_format($precioCosto,2)}}</td>
                  <td @if($totalCosto<0) style="color: red;" @endif>{{ number_format($totalCosto,2)}}</td>
                  <td>@if($value->movimiento==1) @if($observ!="") {{$observ->observacion}} @endif @else @if($observ!="") {{$observ->nombres_paciente}} procedimiento: {{$observ->procedimientos}} @endif @endif</td>
                </tr>
                @php
                $observ="";
                //$cantidad=$value->cantidad;
                if($value->movimiento==1){

                $cantidadant+=$value->cantidad;

                }else{

                $cantidadant= $cantidad-$value->cantidad;

                }
                $getTotalant=0;
                $getPriceant+=$value->valor_unitario;
                $getTotalant+=$value->total;






                @endphp
                @php
                $contador++;
                //dd($getTotal);
                @endphp
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <script type="text/javascript">
      $(document).ready(function() {

        $('#tbl_compras').DataTable({
          'paging': false,
          'lengthChange': false,
          'searching': false,
          'ordering': false,
          'info': false,
          'autoWidth': false,
          "scrollY": 300,
          "scrollX": true,
          'scrollCollapse': true,
        });

        tinymce.init({
          selector: '#hc'
        });


      });
    </script>
  </div>
</section>

<form method="POST" id="print_reporte_master" action="{{ route('kardex.exportar') }}" target="_blank">
  {{ csrf_field() }}
  <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="{{@$fecha_desde}}">
  <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{@$fecha_hasta}}">
  <input type="hidden" name="filproducto_id" id="filproducto_id" value="{{@$producto_id}}">
  <input type="hidden" name="exportar" id="exportar" value="0">
</form>
<!-- /.content -->
<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('.select2_cuentas').select2({
      tags: false
    });
  });

  $('#seguimiento').on('hidden.bs.modal', function() {
    $(this).removeData('bs.modal');
  });


  $('#cuenta').on('select2:select', function(e) {
    var cuenta = $('#cuenta').val();
    $('#nombre').val(cuenta);
    $('#nombre').select2().trigger('change');
  });


  $('#nombre').on('select2:select', function(e) {
    var nombre = $('#nombre').val();
    $('#cuenta').val(nombre);
    $('#cuenta').select2().trigger('change');
  });

  $("#btn_imprimir").click(function() {
    $("#filfecha_desde").val($("#fecha_desde").val());
    $("#filfecha_hasta").val($("#fecha_hasta").val());
    $("#filcuentas_detalle").val($("#cuentas_detalle").val());
    $("#filmostrar_detalles").val($("#mostrar_detalles").val());
    $("#print_reporte_master").submit();
  });

  $("#btn_exportar").click(function() {
    alert();
    $("#filfecha_desde").val($("#fecha_desde").val());
    $("#filfecha_hasta").val($("#fecha_hasta").val());
    $("#filproducto_id").val($("#producto_id").val());
    //alert($("#cuentas_detalle").prop("checked"));
    $("#exportar").val(1);
    $("#print_reporte_master").submit();
  });

  $(document).ready(function() {

    $('#producto_id').val({{$producto_id}});
    $('#producto_id').select2().trigger('change');

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
    $("#fecha_desde").on("dp.change", function(e) {
      verifica_fechas();
    });

    $("#fecha_hasta").on("dp.change", function(e) {
      verifica_fechas();
    });

    //alert('{{$fecha_hasta}}');

  });

  function verifica_fechas() {
    if (Date.parse($("#fecha_desde").val()) > Date.parse($("#fecha_hasta").val())) {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Verifique el rango de fechas y vuelva consultar'
      });
    }
  }

  function buscar() {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }
  /*function imprimir(){ alert("imprimir");
    $( "print_reporte_master" ).submit();
  }*/
</script>
@endsection