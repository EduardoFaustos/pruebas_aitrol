@extends('contable.balance_comprobacion.base')
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
    margin-left: 40px;
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

  .table-striped>thead>tr>th>td,
  .table-striped>tbody>tr>th>td,
  .table-striped>tfoot>tr>th>td,
  .table-striped>thead>tr>td,
  .table-striped>tbody>tr>td,
  .table-striped>tfoot>tr>td {
    padding: 0.5px;
    line-height: 1;
  }

  .secundario {
    left: 10px;
  }

  .table {
    margin-bottom: -10px;
  }

  .ui-autocomplete {
    overflow-x: hidden;
    max-height: 200px;
    width: 1px;
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    float: left;
    display: none;
    min-width: 160px;
    _width: 160px;
    padding: 4px 0;
    margin: 2px 0 0 0;
    list-style: none;
    background-color: #fff;
    border-color: #ccc;
    border-color: rgba(0, 0, 0, 0.2);
    border-style: solid;
    border-width: 1px;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -webkit-background-clip: padding-box;
    -moz-background-clip: padding;
    background-clip: padding-box;
    *border-right-width: 2px;
    *border-bottom-width: 2px;
  }

  .hidden-paginator {

    display: none;

  }

  .removethe {
    display: none;
  }

  .text-left {
    text-align: left;
  }
</style>
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<!-- Main content -->
<section class="content">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.Contabilidad')}}</a></li>
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.Clientes')}}</a></li>
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.DeudasPendientes')}}</a></li>
    </ol>
  </nav>

  <div class="box" style=" background-color: white;">
    <!-- <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">
            <div class="col-md-6">
              <h3 class="box-title">Criterios de búsqueda</h3>
            </div>
        </div> -->
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">{{trans('contableM.Buscador')}}</label>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('clientes.deudas.pendientes') }}">
        {{ csrf_field() }}

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">{{trans('contableM.FechaDesde')}}</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha_desde" id="fecha_desde" autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_desde').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha_hasta" class="texto col-md-3 control-label">{{trans('contableM.Fechahasta')}}</label>
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
        <div class="form-group col-md-2 col-xs-2">
          <label class="texto" for="nombre_cliente">{{trans('contableM.cliente')}}: </label>
        </div>
        <div class="form-group col-md-4 col-xs-4 container-4">
          <select name="id_cliente" id="id_cliente" class="form-control select2">
            <option value="">Seleccione ...</option>
          </select>

        </div>
        <div class="form-group col-md-2 col-xs-2">
          <label class="texto" for="tipo">{{trans('contableM.tipo')}}: </label>
        </div>
        <div class="form-group col-md-4 col-xs-4 container-4">
          <select class="form-control" onchange="tipo_factura();" name="tipo" id="tipo">
            <option value="0">Seleccione...</option>
            <option value="1">VEN-FA</option>
            {{-- <option value="2">COM-FA-CT</option> --}}
          </select>

        </div>
        <div class="form-group col-md-2 col-xs-2">
          <label class="texto" for="concepto">{{trans('contableM.concepto')}}: </label>
        </div>
        <div class="form-group col-md-4 col-xs-4 container-4">
          <input class="form-control" type="text" id="concepto" onchange="observacion()" name="concepto" placeholder="Ingrese concepto..." />


        </div>



        <!-- <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
            <label for="mostrar_detalles" class="texto col-md-5 control-label" >{{trans('contableM.Mostrarresumen')}}</label>
            <input type="checkbox" id="mostrar_detalles" class="flat-green" name="mostrar_detalles" value="1"  @if(old('mostrar_detalles')=="1") checked @endif>
        </div> -->

        <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
          <!-- <button type="submit" class="btn btn-primary btn-sm" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true" style="font-size: 16px">&nbsp;Buscar&nbsp;</span>
          </button>
          <button type="button" class="btn btn-primary btn-sm" id="btn_imprimir" name="btn_imprimir">
            <span class="glyphicon glyphicon-print" aria-hidden="true" style="font-size: 16px">&nbsp;{{trans('contableM.Imprimir')}}&nbsp;</span>
          </button> -->
          <button type="submit" class="btn btn-primary" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
          </button>
          <button type="button" class="btn btn-primary" onclick="excel();" id="btn_exportar">
            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> {{trans('contableM.Exportar')}}
          </button>
        </div>

        <div class="form-group col-md-6 col-xs-9" style="text-align: right;">

        </div>
      </form>
    </div>
    <!-- /.box-body -->
    <form method="POST" id="print_reporte_master" action="{{ route('clientes.deudas.pendientes.excel') }}" target="_blank">
      {{ csrf_field() }}
      <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="{{$fecha_desde}}">
      <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{$fecha_hasta}}">
      <input type="hidden" name="id_cliente2" id="id_cliente2" value="{{$id_cliente}}">
      <input type="hidden" name="tipo2" id="tipo2" value="{{$tipo}}">
      <input type="hidden" name="observacion2" id="observacion2" value="{{$observacion}}">
    </form>

    @if(count($informe)!='[]')
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
            </div>
            <!-- /.box-header -->
            {{-- <div class="box-body">
                <div class="col-md-4">
                  <dl> 
                    <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image"
            style="width:80px;height:80px;" id="logo_empresa"></dd>
            <dd>&nbsp; {{$empresa->nombrecomercial}}</dd>
            </dl>
          </div>
          <div class="col-md-4">
            <h4 style="text-align: center;">{{trans('contableM.DeudasPendientes')}}</h4>
            <h4 style="text-align: center;">{{$fecha_desde}} - {{$fecha_hasta}}</h4>
          </div>
          <div class="col-md-4">
            <dl>
              <dd style="text-align:right">{{$empresa->direccion}} &nbsp; <i class="fa fa-building"></i></dd>
              <dd style="text-align:right">{{trans('contableM.telefono')}}: {{$empresa->telefono1}} - {{$empresa->telefono2}}&nbsp;<i class="fa fa-phone"></i> </dd>
              <dd style="text-align:right"> {{$empresa->email}} &nbsp;<i class="fa fa-envelope-o"></i></dd>
            </dl>
          </div>
        </div> --}}

        <div class="box-body">
          <div class="col-md-1">
            <dl>
              <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa"></dd>
              {{-- <dd>&nbsp; {{$empresa->nombrecomercial}}</dd> --}}
            </dl>
          </div>
          <div class="col-md-3">
            <dl>
              <dd><strong>{{$empresa->nombrecomercial}}</strong></dd>
              <dd>&nbsp; {{$empresa->id}}</dd>
            </dl>
          </div>
          <div class="col-md-4">
            <h4 style="text-align: center;">{{trans('contableM.INFORMEDEUDASPENDIENTES')}}</h4>
            @if(($fecha_desde!=null))
            <h5 style="text-align: center;">Desde {{date("d/m/Y", strtotime($fecha_desde))}} - Hasta
              {{date("d/m/Y", strtotime($fecha_hasta))}}
            </h5>
            @else
            <h5 style="text-align: center;">Al {{date("d/m/Y", strtotime($fecha_hasta))}}</h5>
            @endif
          </div>
          @php $total1=0; $total2=0; $total3=0; @endphp
          @foreach($informe as $val)
          @php $acumvalor=0; $acumabono=0; $acumsaldo=0; @endphp
          @if(($val->facturas!="[]"))
          <div class="col-md-12"><span> <b>{{$val->identificacion}} | {{$val->nombre }} </b> </span> </div>
          <div class="col-md-12"> &nbsp; </div>
          <table id="example2" class="table table-striped" role="grid" aria-describedby="example2_info" style="font-size: 12px; text-align:center;">
            <thead>
              <tr class='well-dark'>
                <th width="6.25%" style="text-align:left;" tabindex="0" aria-controls="example2" rowspan="1">{{trans('contableM.fecha')}}</th>
                <th width="6.25%" style="text-align:left;" tabindex="0" aria-controls="example2" rowspan="1">{{trans('contableM.Vencimiento')}}</th>
                <th width="6.25%" style="text-align:left;" tabindex="0" aria-controls="example2" colspan="1">{{trans('contableM.tipo')}}</th>
                <th width="6.25%" style="text-align:left;" tabindex="0" aria-controls="example2" colspan="1">{{trans('contableM.numero')}}</th>
                <th width="8%" style="text-align:left;" tabindex="0" aria-controls="example2" colspan="1">{{trans('contableM.cliente')}}</th>
                <th width="20%" style="text-align:left;" tabindex="0" aria-controls="example2" colspan="1">{{trans('contableM.detalle')}}</th>
                <th width="3.25%" style="text-align:left;" tabindex="0" aria-controls="example2" colspan="1">Dias</th>
                <th width="12.5%" style="text-align:right;" tabindex="0" aria-controls="example2" colspan="1">{{trans('contableM.valor')}}</th>
                <th width="12.5%" style="text-align:right;" tabindex="0" aria-controls="example2" colspan="1">{{trans('contableM.abono')}}</th>
                <th width="12.5%" style="text-align:right;" tabindex="0" aria-controls="example2" colspan="1">{{trans('contableM.saldo')}}</th>
              </tr>
            </thead>
          </table>
          @foreach($val->facturas as $value)
          @if(!is_null($value))
          @php $abono = 0; @endphp
          @php $abono = ($value->total_final - $value->valor_contable) @endphp
          @php $acumvalor+=$value->total_final; $acumabono+=$abono; $acumsaldo+=$value->valor_contable; @endphp
          <table id="example2" class="table table-striped" role="grid" aria-describedby="example2_info" style="font-size: 12px; text-align: center;">
            <thead>
              <tr>
                <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" rowspan="1"></th>
                <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" rowspan="1"></th>
                <th width="6.25%" style="text-align:left;" tabindex="0" aria-controls="example2" colspan="1"></th>
                <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1"></th>
                <th width="8%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1"></th>
                <th width="20%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1"></th>
                <th width="3.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1"></th>
                <th width="12.5%" style="text-align:right;" tabindex="0" aria-controls="example2" colspan="1"></th>
                <th width="12.5%" style="text-align:right;" tabindex="0" aria-controls="example2" colspan="1"></th>
                <th width="12.5%" style="text-align:right;" tabindex="0" aria-controls="example2" colspan="1"></th>
              </tr>
            </thead>
            <tbody>
              @if(!is_null($value))

              <tr>
                <td>{{ date("d/m/Y", strtotime($value->fecha))}} </td>
                @php
                $fechaActual = date('Y-m-d');
                $currentDate = \Carbon\Carbon::createFromFormat('Y-m-d', $fechaActual);
                $shippingDate = \Carbon\Carbon::createFromFormat('Y-m-d', $value->fecha);
                $diferencia_en_dias = $currentDate->diffInDays($shippingDate);
                @endphp
                
                <td>{{ $diferencia_en_dias }} dias </td>
                <td style="text-align:left;"> {{ $value->tipo }} </td>
                <td>@if(($value)!=null) {{$value->nro_comprobante}}  -- {{$value->id}} @endif</td>
                <td style="text-align:left;">@if(($value->id_cliente)!=null) {{$value->cliente->nombre}} @endif</td>
                <td class="text-left">@if(($value)!=null) Fact: # {{$value->nro_comprobante}} <br> <br> <label style="font-size: 11px" class="label label-success">Id: # {{$value->id}} Asientos: # {{$value->id_asiento}}</label> @endif</td>
                <td>$</td>
                <td style="text-align: right">{{ number_format($value->total_final,2,'.','') }}</td>
                <td style="text-align: right">{{ number_format($abono,2,'.','') }}</td>
                <td style="text-align: right">{{ number_format($value->valor_contable,2,'.','') }}</td>
              </tr>
              @endif
            </tbody>
            <tfoot>


            </tfoot>
          </table>
          @endif
          @endforeach
          <table id="example2" class="table table-striped" role="grid" aria-describedby="example2_info" style="font-size: 12px; text-align: center;">
            <thead>
              <tr>
                <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" rowspan="1"></th>
                <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" rowspan="1"></th>
                <th width="6.25%" style="text-align:left;" tabindex="0" aria-controls="example2" colspan="1"></th>
                <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1"></th>
                <th width="8%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1"></th>
                <th width="20%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1"></th>
                <th width="3.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1"></th>
                <th width="12.5%" style="text-align:right;" tabindex="0" aria-controls="example2" colspan="1"></th>
                <th width="12.5%" style="text-align:right;" tabindex="0" aria-controls="example2" colspan="1"></th>
                <th width="12.5%" style="text-align:right;" tabindex="0" aria-controls="example2" colspan="1"></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td></td>
                <td> </td>
                <td></td>
                <td> </td>
                <td> </td>
                <td style="text-align:center;"></td>
                <td><label>{{trans('contableM.total')}}</label></td>
                <td style="text-align:right; font-weight: bold;"> {{number_format($acumvalor,2,'.','')}} </td>
                <td style="text-align:right; font-weight: bold;"> {{number_format($acumabono,2,'.','')}} </td>
                <td style="text-align:right; font-weight: bold;"> {{number_format($acumsaldo,2,'.','')}} </td>
              </tr>
              @php $total1 += $acumvalor; $total2 += $acumabono; $total3+=$acumsaldo; @endphp
            </tbody>

          </table>
          @endif
          @endforeach
          <table id="example2" class="table table-striped" role="grid" aria-describedby="example2_info" style="font-size: 12px; text-align: center;">
            <thead>
              <tr>
                <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" rowspan="1"></th>
                <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" rowspan="1"></th>
                <th width="6.25%" style="text-align:left;" tabindex="0" aria-controls="example2" colspan="1"></th>
                <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1"></th>
                <th width="8%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1"></th>
                <th width="20%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1"></th>
                <th width="3.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1"></th>
                <th width="12.5%" style="text-align:right;" tabindex="0" aria-controls="example2" colspan="1"></th>
                <th width="12.5%" style="text-align:right;" tabindex="0" aria-controls="example2" colspan="1"></th>
                <th width="12.5%" style="text-align:right;" tabindex="0" aria-controls="example2" colspan="1"></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td></td>
                <td> </td>
                <td></td>
                <td> </td>
                <td> </td>
                <td style="text-align:center;"><label>GRAN TOTAL: </label></td>
                <td></td>
                <td style="text-align:right; font-weight: bold;">{{number_format($total1,2,'.','')}} </td>
                <td style="text-align:right; font-weight: bold;">{{number_format($total2,2,'.','')}} </td>
                <td style="text-align:right; font-weight: bold;">{{number_format($total3,2,'.','')}} </td>
              </tr>

            </tbody>

          </table>

        </div>
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
  </div>


  </div>
  @endif

  </div>
</section>
<!-- /.content -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>

<script type="text/javascript">
  $(document).ready(function() {
    $('.select2_cuentas').select2({
      tags: false
    });
    $('#fact_contable_check').iCheck({
      checkboxClass: 'icheckbox_flat-blue',
      increaseArea: '20%' // optional
    });
    $('.select2').select2({
      ajax: {
        url: '{{route("get_sources.cliente")}}',
        data: function(params) {
          var query = {
            search: params.term,
            type: 'public'
          }
          return query;
        },
        processResults: function(data) {
          // Transforms the top-level key of the response object from 'items' to 'results'
          console.log(data);
          return {
            results: data
          };
        }
      }
    });

  });
  $(function() {
    $('.infinite-scroll').jscroll({
      autoTrigger: true,
      loadingHtml: '<img class="center-block" src="{{asset("/loading.gif")}}" width="50px" alt="Loading..." />',
      padding: 0,
      nextSelector: '.pagination li.active + li a',
      contentSelector: 'div.infinite-scroll',
      callback: function() {
        $('div.paginationLinks').remove();

      }
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
    $("#print_reporte_master").submit();
    // document.getElementById("print_reporte_master").submit(); 
  });


  $(document).ready(function() {

    $('input[type="checkbox"].flat-green').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass: 'iradio_flat-green'
    });

    $('input[type="checkbox"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-red',
      radioClass: 'iradio_flat-red'
    });

  });

  function excel() {
    $("#print_reporte_master").submit();
  }
  $("#nombre_cliente").autocomplete({
    source: function(request, response) {
      $.ajax({
        url: "{{route('retenciones.autocompletar.cliente')}}",
        dataType: "json",
        data: {
          term: request.term
        },
        success: function(data) {
          response(data);
        }
      });
    },
    minLength: 2,
  });
  $('#fact_contable_check').on('ifChanged', function(event) {
    //aqui funciona si cambio el input time
    if ($(this).prop("checked")) {
      $("#esfac_contable").val(1);
      $("#es_fact_dos").val(1);
    } else {
      $("#esfac_contable").val(0);
    }

  });

  function cambiar_nombre_cliente() {
    $.ajax({
      type: 'post',
      url: "{{route('retenciones.autocompletar.cliente')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: {
        'nombre': $("#nombre_cliente").val()
      },
      success: function(data) {
        if (data.value != "no") {
          $('#id_cliente').val(data.value);
          $('#id_cliente2').val(data.value);
          $('#direccion_id_cliente').val(data.direccion);
        } else {
          $('#id_cliente').val("");
          $('#id_cliente2').val("");
          $('#direccion_cliente').val("");
        }

      },
      error: function(data) {
        console.log(data);
      }
    });
  }

  $(function() {
    $('#fecha_desde').datetimepicker({
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fecha_desde}}',
    });
    $('#fecha_hasta').datetimepicker({
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fecha_hasta}}',

    });
    $("#fecha_desde").on("dp.change", function(e) {
      verifica_fechas();
    });

    $("#fecha_hasta").on("dp.change", function(e) {
      verifica_fechas();
    });

  });

  function buscar() {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }

  function tipo_factura() {
    var tipo = $("#tipo").val();
    if (isNaN(tipo)) {
      tipo = 0;
    }
    $("#tipo2").val(tipo);
  }

  function observacion() {
    var observacion = $("#concepto").val();

    $("#observacion2").val(observacion);
  }

  function verifica_fechas() {
    if (Date.parse($("#fecha_desde").val()) > Date.parse($("#fecha_hasta").val())) {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Verifique el rango de fechas y vuelva consultar'
      });
    }
  }
</script>
@endsection