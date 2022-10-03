@extends('contable.balance_comprobacion.base')
@section('action-content')
<style>
  p.s1 {
    margin-left: 10px;
    font-size: 12px;
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
</style>
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
<!-- Main content -->
<section class="content">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item"><a href="#">Contabilidad</a></li>
      <li class="breadcrumb-item"><a href="#">Acreedores</a></li>
    </ol>
  </nav>

  <div class="box dobra" style=" background-color: white;">
    <!-- <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">
            <div class="col-md-6">
              <h3 class="box-title">Criterios de búsqueda</h3>
            </div>
        </div> -->
    <div class="row head-title">
      <div class="col-md-12 header">
        <label class="color_texto" for="title">BUSCADOR</label>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body ">
      <form method="POST" id="reporte_master" action="{{ route('compras.informe') }}">
        {{ csrf_field() }}

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">Fecha desde:</label>
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
          <label for="fecha_hasta" class="texto col-md-3 control-label">Fecha hasta:</label>
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
          <label class="texto" for="nombre_proveedor">Proveedor: </label>
        </div>
        <div class="form-group col-md-4 col-xs-4 container-4">
          <select name="id_proveedor" id="id_proveedor" class="form-control select2_cuentas" style="width: 100%;">
            <option value="">Seleccione...</option>
            @foreach($proveedores as $value)
            <option @if($id_proveedor==$value->id) selected="selected" @endif value="{{$value->id}}">{{$value->nombrecomercial}}</option>
            @endforeach
          </select>

        </div>
        <div class="form-group col-md-2 col-xs-2">
          <label class="texto" for="nombre_proveedor">Tipo: </label>
        </div>
        @php 
          $sel0 = "";
          $sel1 = "";

          $tipo1 = 0;
          $tipo2 = 0;
           if(isset($busq)){
            if(!is_null($busq['estado'])){
              if($busq['estado'] == 1){
                $sel1="selected";
              }else if($busq['estado'] == 0){
                $sel0 = "selected";
              }
            }
            if(!is_null($busq['tipo'])){
              if($busq['tipo'] == 1){
                $tipo1="selected";
              }else if($busq['tipo'] == 2){
                $tipo2 = "selected";
              }
            }
           }  
          @endphp
        <div class="form-group col-md-4 col-xs-4 container-4">
          <select class="form-control" onchange="tipo_factura();" name="tipo" id="tipo">
            <option @if($tipo == 0) selected @endif value="0">Seleccione...</option>
            <option @if($tipo == 1) selected @endif  value="1">COM-FA</option>
            <option @if($tipo == 2) selected @endif  value="2">COM-FACT</option>
            <option @if($tipo == 3) selected @endif  value="3">COM-AF</option>
          </select>
        </div>

        <div class="form-group col-md-2 col-xs-2">
          <label class="texto" for="nombre_proveedor">Estado: </label>
        </div>
        <div class="form-group col-md-4 col-xs-4 container-4">
         
          <select name="estado" class="form-control">
            <option  value="">TODOS</option>
            <option {{$sel1}} value="1">ACTIVO</option>
            <option {{$sel0}} value="0">ANULADA</option>
          </select>
        </div>

        <!-- <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
            <label for="mostrar_detalles" class="texto col-md-5 control-label" >Mostrar resumen</label>
            <input type="checkbox" id="mostrar_detalles" class="flat-green" name="mostrar_detalles" value="1"  @if(old('mostrar_detalles')=="1") checked @endif>
        </div> -->
        <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
          <!-- <button type="submit" class="btn btn-primary btn-sm" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true" style="font-size: 16px">&nbsp;Buscar&nbsp;</span>
          </button>
          <button type="button" class="btn btn-primary btn-sm" id="btn_imprimir" name="btn_imprimir">
            <span class="glyphicon glyphicon-print" aria-hidden="true" style="font-size: 16px">&nbsp;Imprimir&nbsp;</span>
          </button> -->
          <button type="submit" class="btn btn-primary" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
          </button>

          <!--<button type="button" class="btn btn-primary" onclick="printDiv()" id="btn_imprimir">
            <span class="glyphicon glyphicon-print" aria-hidden="true"></span> Imprimir
          </button>-->
          <!--<button type="button" class="btn btn-primary" onclick="excel();" id="btn_exportar">
            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
          </button>-->
        </div>
        <div class="form-group col-md-6 col-xs-9" style="text-align: right;">
        </div>
      </form>
    </div>
    <!-- /.box-body -->
    <form method="POST" id="print_reporte_master" action="{{ route('compras.excel') }}" target="_blank">
      {{ csrf_field() }}
      <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="{{$fecha_desde}}">
      <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{$fecha_hasta}}">
      <input type="hidden" name="id_proveedor2" id="id_proveedor2" value="{{$id_proveedor}}">
      <input type="hidden" name="tipo2" id="tipo2" value="{{$tipo}}">
    </form>

    @if(count($informe)>0)
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap" style="overflow:scroll;">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
            </div>
            <!-- /.box-header -->
            {{-- <div class="box-body">
                <div class="col-md-4">
                  <dl> 
                    <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa"></dd>
            <dd>&nbsp; {{$empresa->nombrecomercial}}</dd>
            </dl>
          </div>
          <div class="col-md-4">
            <h4 style="text-align: center;">INFORME COMPRAS INVENTARIO Y FACTURA GASTOS</h4>
            <h4 style="text-align: center;">{{$fecha_desde}} - {{$fecha_hasta}}</h4>
          </div>
          <div class="col-md-4">
            <dl>
              <dd style="text-align:right">{{$empresa->direccion}} &nbsp; <i class="fa fa-building"></i></dd>
              <dd style="text-align:right">Telf: {{$empresa->telefono1}} - {{$empresa->telefono2}}&nbsp;<i class="fa fa-phone"></i> </dd>
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
          <div id="imprimir">
            <div class="col-md-3">
              <dl>
                <dd><strong>{{$empresa->nombrecomercial}}</strong></dd>
                <dd>&nbsp; {{$empresa->id}}</dd>
              </dl>
            </div>
            <div class="col-md-4">
              <h4 style="text-align: center;">INFORME COMPRAS INVENTARIO Y FACTURA GASTOS</h4>
              <h5 style="text-align: center;"> @if(($fecha_desde!=null)) Desde {{date("d-m-Y", strtotime($fecha_desde))}} - Hasta {{date("d-m-Y", strtotime($fecha_hasta))}} @else AL - {{date("d-m-Y", strtotime($fecha_hasta))}} @endif</h5>
            </div>
            <div class="col-md-4">
            </div>

            <table id="example2" class="display compact" role="grid" aria-describedby="example2_info" style="font-size: 12px; text-align:center; width:100%;">
              <thead>
                <tr >
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Fecha</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Número</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Ruc</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Proveedor</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Tipo Comprobante</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Tipo</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Detalle</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">SubTotal12</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">SubTotal0</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">SubTotal</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">SubTotal12 con Descuento</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">SubTotal0 con Descuento</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Descuento</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Impuesto</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Total</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Estado</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Creado Por</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Anulado Por</th>
                </tr>
              </thead>
              <tbody>

                @php
                 $nuevo=0; 
                 $subtotal= 0;
                 $subtotal0=0;
                 $subtotal12=0;
                 $impuesto=0;
                 $descuento=0;
                 $totales=0;
                 
                $totalSub0Desc = 0;
                $totalSub12Desc = 0;

                @endphp
                @foreach($informe as $value)
                @php
                $subto0=0;
                $subto12=0;
                $estados = 1; 
                $subtotal12Descuento =  $value->detalles->where('iva', 1)->sum('descuento');
                $subtotal0Descuento = $value->detalles->where('iva', 0)->sum('descuento');

                if($subtotal12Descuento > 0){
                 // dd($value);
                  $subtotal12Descuento = $value->subtotal_12 - $subtotal12Descuento;
                }else{
                  $subtotal12Descuento = $value->subtotal_12;
                }

                if($subtotal0Descuento > 0){
                  $subtotal0Descuento = $value->subtotal_0 - $subtotal0Descuento;
                }else{
                  $subtotal0Descuento = $value->subtotal_0;
                }
                $nuevo_tipo = "";
                  $activo = explode(" ", $value->observacion);
                  if($activo[0] == "ACT-FA"){
                    $nuevo_tipo = "COM-AF";
                  }
                if($value->estado != 0 or $sel0 == "selected"){
                    //dd("aquii");
                    $subto12+=$value->subtotal_12;
                    $subto0+=$value->subtotal_0;
                    $subtotal0+=$value->subtotal_0;
                    $subtotal12+=$value->subtotal_12;
                    $subtotal+=$value->subtotal;
                    $descuento+=$value->descuento;
                    $impuesto+=$value->iva_total;
                    $totales+=$value->total_final;
                    
                    $totalSub0Desc += $subtotal0Descuento;
                    $totalSub12Desc += $subtotal12Descuento;

                }
                @endphp
                <tr @if($value->estado == 0) color: red; @endif> 
                  <td style="text-align:left;">@if(($value)!=null) {{date("d-m-Y", strtotime($value->f_autorizacion))}} @endif</td>
                  <td style="text-align:left;">@if(($value)!=null) {{$value->numero}} @endif</td>
                  <td style="text-align:left;">@if(!is_null($value->proveedor)) {{$value->proveedor}} @endif</td>
                  <td style="text-align:left;">@if(isset($value->proveedorf)) @if(($value->proveedorf!=null)) {{$value->proveedorf->nombrecomercial}} @endif @endif</td>
                  <td style="text-align:left;">{{$value->master_tipos->nombre}}</td>
                  <td style="text-align:left;">@if($nuevo_tipo != "") {{$nuevo_tipo}} @else @if(($value->tipo)==1) COM-FA @else COM-FACT @endif  @endif</td>
                  <td style="text-align:left;"> @if(($value)!=null){{$value->observacion}} Asiento # {{$value->id_asiento_cabecera}} @endif</td>
                  <td style="text-align:center;">@if(($value)!=null) {{number_format($value->subtotal_12,2,'.',',')}} @endif</td>
                  <td style="text-align:center;">@if(($value)!=null) {{number_format($value->subtotal_0,2,'.',',')}} @endif </td>
                  <td style="text-align:center;">@if(($value)!=null) {{$value->subtotal}} @endif </td>
                  <td style="text-align:center;"> {{ number_format($subtotal12Descuento,2,'.',',')}} </td>
                  <td style="text-align:center;"> {{ number_format($subtotal0Descuento,2,'.',',')}} </td>
                  <td style="text-align:center;">@if(($value)!=null) @if(($value->descuento)>0) {{$value->descuento}} @else 0.00 @endif @endif </td>
                  <td style="text-align:center;">@if(($value)!=null) @if(($value->iva_total)>0) {{$value->iva_total}} @else 0.00 @endif @endif </td>
                  <td style="text-align:center;">@if(($value)!=null) {{$value->total_final}} @endif </td>
                  <td>@if(($value)!=null) @if(($value->estado)==0) ANULADA @else ACTIVA @endif @endif </td>
                  <td>@if(($value)!=null) @if(($value->usuario)!=null) {{$value->usuario->nombre1}} {{$value->usuario->apellido1}} @endif @endif </td>
                  <td>@if(($value)!=null) @if(($value->usuario)!=null) @if(($value->estado)==0) {{$value->usuario->nombre1}} {{$value->usuario->apellido1}} @endif @endif @endif </td>
                </tr>
               


                @endforeach
                
              </tbody>
              <tfoot>
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><label>TOTAL:</label></td>
                  <td style="text-align:center; font-weight: bold; ">
                    <p>{{number_format($subtotal12,2,'.',',')}}</p>
                  </td>
                  <td style="text-align:center; font-weight: bold; ">
                    <p>{{number_format($subtotal0,2,'.',',')}}</p>
                  </td>
                  <td style="text-align:center; font-weight: bold; ">
                    <p>{{number_format($subtotal,2,'.',',')}}</p>
                  </td>
                  <td style="text-align:center; font-weight: bold; ">
                    <p>{{number_format($totalSub12Desc,2,'.',',')}}</p>
                  </td>
                  <td style="text-align:center; font-weight: bold; ">
                    <p>{{number_format($totalSub0Desc,2,'.',',')}}</p>
                  </td>
                  <td style="text-align:center; font-weight: bold; ">
                    <p>{{number_format($descuento,2,'.',',')}}</p>
                  </td>
                  <td style="text-align:center; font-weight: bold; ">
                    <p>{{number_format($impuesto,2,'.',',')}}</p>
                  </td>
                  <td style="text-align:center; font-weight: bold; ">
                    <p>{{number_format($totales,2,'.',',')}}</p>
                  </td>
                  <td> </td>
                  <td> </td>
                  <td> </td>
                </tr>
              </tfoot>
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
</div>
<!-- /.content -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>

<script type="text/javascript">
  $(document).ready(function() {
    $('.select2_cuentas').select2({
      tags: false
    });
    $('#fact_contable_check').iCheck({
      checkboxClass: 'icheckbox_flat-blue',
      increaseArea: '20%' // optional
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

  function printDiv(nombreDiv) {
    Popup($('<div/>').append($("#imprimir").clone()).html());
  }

  function PrintElem(elem) {

  }
  // WORKS AT NOVEMBER 23 2020
  function Popup(data) {
    var mywindow = window.open('', 'my div', 'height=400,width=600');
    mywindow.document.write('<html><head><title>SISTEMA MÉDICO</title>');
    mywindow.document.write('<link rel="stylesheet" href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" type="text/css" />');
    mywindow.document.write('<style>   .hidden-paginator {display: none;} </style>');
    mywindow.document.write('</head><body >');
    mywindow.document.write(data);
    mywindow.document.write('</body></html>');
    mywindow.print();
    //  mywindow.close();
    return true;
  }
  $('#seguimiento').on('hidden.bs.modal', function() {
    $(this).removeData('bs.modal');
  });
  $('#example2').DataTable({
        'paging': false,
         dom: 'lBrtip',
        'lengthChange': false,
        'searching': true,
        'ordering': false,
        responsive: true,
        'info': false,
        'autoWidth': true,
        buttons: [{
          extend: 'copyHtml5',
          footer: true
        },
        {
          extend: 'excelHtml5',
          footer: true,
          title: 'INFORME COMPRAS INVENTARIO Y FACTURA GASTOS'
        },
        {
          extend: 'csvHtml5',
          footer: true
        },
        {
          extend: 'pdfHtml5',
          orientation: 'landscape',
          pageSize: 'TABLOID',
          footer: true,
          title: 'INFORME COMPRAS INVENTARIO Y FACTURA GASTOS',
          customize: function(doc) {
            doc.styles.title = {
              color: 'black',
              fontSize: '16',
              alignment: 'center'
            }
          }
        }
      ],
    })

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
  $("#nombre_proveedor").autocomplete({
    source: function(request, response) {
      $.ajax({
        url: "{{route('compra_buscar_nombreproveedor')}}",
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

  function cambiar_nombre_proveedor() {
    $.ajax({
      type: 'post',
      url: "{{route('compra_buscar_proveedornombre')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: {
        'nombre': $("#nombre_proveedor").val()
      },
      success: function(data) {
        if (data.value != "no") {
          $('#id_proveedor').val(data.value);
          $('#id_proveedor2').val(data.value);
          $('#direccion_id_proveedor').val(data.direccion);
        } else {
          $('#id_proveedor').val("");
          $('#id_proveedor2').val("");
          $('#direccion_proveedor').val("");
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