@extends('insumos.ingreso.base')
@section('action-content')
<style type="text/css">
        .ui-corner-all
        {
            -moz-border-radius: 4px 4px 4px 4px;
        }

        .ui-widget
        {
            font-family: Verdana,Arial,sans-serif;
            font-size: 15px;
        }
        .ui-menu
        {
            display: block;
            float: left;
            list-style: none outside none;
            margin: 0;
            padding: 2px;
        }
        .ui-autocomplete
        {
             overflow-x: hidden;
              max-height: 200px;
              width:1px;
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
        .ui-menu .ui-menu-item
        {
            clear: left;
            float: left;
            margin: 0;
            padding: 0;
            width: 100%;
        }
        .ui-menu .ui-menu-item a
        {
            display: block;
            padding: 3px 3px 3px 3px;
            text-decoration: none;
            cursor: pointer;
            background-color: #ffffff;
        }
        .ui-menu .ui-menu-item a:hover
        {
            display: block;
            padding: 3px 3px 3px 3px;
            text-decoration: none;
            color: White;
            cursor: pointer;
            background-color: #006699;
        }
        .ui-widget-content a
        {
            color: #222222;
        }
        .derecha{
          text-align: right;
        }
</style>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
          <div class="col-sm-4">
            <h3 class="box-title">EDITAR PEDIDO</h3>
          </div>
          <div class="col-md-8" style="text-align: right;">
            <a type="button" href="{{URL::previous()}}" class="btn btn-primary btn-sm btn-gray">
              <span class="glyphicon glyphicon-arrow-left"> Regresar</span>
            </a>
          </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <!--form method="POST" id="ingreso">

          <div class="box box-default">
            <div class="box-header with-border">
              <h3 class="box-title">Ingreso de producto</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                      <label for="inputid" class="col-sm-3 control-label">C??digo</label>
                      <div class="col-sm-9">
                        <input value="" type="text" class="form-control" name="codigo" id="codigo" placeholder="Codigo"  style="text-transform:uppercase;">
                      </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                      <label for="inputapellido" class="col-sm-3 control-label">Nombre</label>
                      <div class="col-sm-9">
                        <input value="" type="text" class="form-control"  id="nombre" name="nombre" id="inputapellido" placeholder="Nombre" style="text-transform:uppercase;">
                      </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="box-footer">
              <button type="button" id="busqueda" class="btn btn-primary btn-gray">
                Agregar
              </button>
            </div>
          </div>
      </form-->
      <div class="box">
        <form method="POST"  name="frm" id="frm">
        {{ csrf_field() }}
          <input type="hidden" name="id_pedido" value="{{$pedido->id}}">
          <div class="box-header">
            <div class="row">
                <!-- Fecha -->
                <div class="form-group col-md-6">
                    <label for="fecha" class="col-md-4 control-label">Fecha Pedido</label>
                    <div class="col-md-8">
                      <div class="input-group date">
                        <div class="input-group-addon">
                          <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" value="{{$pedido->fecha}}" name="fecha" class="form-control" id="fecha"  placeholder="AAAA/MM/DD">
                      </div>
                    </div>
                </div>
                <!-- Numero de Pedido -->
                <div class="form-group cl_pedido col-md-6 {{ $errors->has('pedido') ? ' has-error' : '' }}">
                    <label for="pedido" class="col-md-4 control-label">N??mero de pedido</label>
                    <div class="col-md-8">
                      <input id="pedido" type="text" class="form-control" name="pedido" value="{{$pedido->pedido}}" onkeyup="valida(event);" required autofocus>
                    </div>
                    <span class="help-block">
                      <strong id="str_pedido"></strong>
                    </span>
                </div>

                <div class="form-group cl_pedido col-md-6 {{ $errors->has('num_factura') ? ' has-error' : '' }}">
                    <label for="num_factura" class="col-md-4 control-label">N??mero de factura</label>
                    <div class="col-md-8">
                      <input id="num_factura" type="text" class="form-control" name="num_factura" value="{{$pedido->factura}}" required autofocus>
                    </div>
                </div>

                <!-- Vencimiento -->
                <div class="form-group col-md-6{{ $errors->has('vencimiento') ? ' has-error' : '' }}">
                    <label for="vencimiento" class="col-md-4 control-label">Fecha de Vencimiento</label>
                    <div class="col-md-8">
                      <div class="input-group date">
                        <div class="input-group-addon">
                          <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" value="{{$pedido->vencimiento}}" name="vencimiento" class="form-control" id="vencimiento"  placeholder="AAAA/MM/DD">
                      </div>
                    </div>
                </div>
                <div class="form-group col-md-6{{ $errors->has('id_proveedor') ? ' has-error' : '' }}">
                  <label for="tipo_" class="col-md-4 control-label">Tipo</label>
                  <div class="col-md-8">
                    {{-- <select name="tipo_" class="form-control select2 " style="width: 100%;" required="" name="tipo_" id="tipo_">
                        <option value="">Seleccione..</option>
                        <option value="1">Guia de Remision</option>
                        <option value="2">Factura Contra Entrega</option>
                        <option value="3">Factura</option>
                    </select> --}}
                    <select name="tipo_" class="form-control select2 " style="width: 100%;" required="" name="tipo_" id="tipo_">
                      <option value="">Seleccione..</option>
                      @foreach($doc_bodega as $value)
                      <option value="{{$value->id}}" @if($value->id == $pedido->tipo) selected @endif >{{$value->documento}}</option>
                      @endforeach
                  </select>
                  </div>
              </div>
                <!-- Proveedor -->
                <div class="form-group col-md-6{{ $errors->has('id_proveedor') ? ' has-error' : '' }}">
                    <label for="id_proveedor" class="col-md-4 control-label">Proveedor</label>
                    <div class="col-md-8">
                      <select name="id_proveedor" class="form-control select2" required="" name="id_proveedor">
                          <option value="">Seleccione..</option>
                        @foreach($proveedores as $value)
                          <option value="{{$value->id}}" @if($value->id == $pedido->id_proveedor) selected @endif >{{$value->nombrecomercial}}</option>
                        @endforeach
                      </select>
                    </div>
                </div>

                <div class="form-group col-md-6{{ $errors->has('id_proveedor') ? ' has-error' : '' }}">
                  <label for="bodega_recibe" class="col-md-4 control-label">Bodega Recibe</label>
                  <div class="col-md-8">
                    <select name="bodega_recibe" class="form-control select2 " style="width: 100%;" required="" name="bodega_recibe" id="bodega_recibe">
                      <option value="">Seleccione..</option>
                      @foreach($bodegas as $value)
                      <option value="{{$value->id}}" @if($value->id == $pedido->id_bodega) selected @endif >{{$value->nombre}}&nbsp;@if(isset($value->empresa))-&nbsp;{{$value->empresa->nombrecomercial}}@endif</option>
                      @endforeach
                  </select>
                  </div>
              </div>
                <!-- MULTIPLE EMPRESA-->
                {{-- <div class="form-group col-md-6{{ $errors->has('id_empresa') ? ' has-error' : '' }}">
                    <label for="id_empresa" class="col-md-4 control-label">Empresa</label>
                    <div class="col-md-8">
                      <select id="id_empresa" class="form-control select2" name="id_empresa">
                          <option value="">Seleccione..</option>
                        @foreach($empresa as $value)
                          <option value="{{$value->id}}" @if($pedido->id_empresa == $value->id) selected @endif >{{$value->nombrecomercial}}</option>
                        @endforeach
                      </select>
                    </div>
                </div> --}}


                <!-- MULTIPLE EMPRESA-->
                <!--div class="form-group col-md-6{{ $errors->has('consecion') ? ' has-error' : '' }}">
                    <label for="consecion" class="col-md-4 control-label">Posee Consecion</label>
                    <div class="col-md-8">
                      <input id="consecion" name="consecion" type="checkbox" @if($pedido->consecion == 1 ) checked @endif value="1" class="flat-blue"  style="position: absolute;">
                    </div>
                </div-->

                <!-- Observaciones -->
                <div class="form-group  col-md-12 {{ $errors->has('observaciones') ? ' has-error' : '' }}">
                    <label for="observaciones" class="col-md-2 control-label">Observaciones</label>
                    <div class="col-md-10">
                      <input id="observaciones" type="text" class="form-control" name="observaciones" value="{{ $pedido->observaciones }}" autofocus>
                    </div>
                </div>
            </div>
          </div>
          <div class="general form-group ">
            <label class="col-md-4 control-label"></label>
            <div class="col-md-8">
            </div>
            <span class="help-block">
              <strong id="lote_errores"></strong>
            </span>
          </div>
          <div class="box-body">
            <div class="table-responsive col-md-12">
              <input name='contador' type="hidden" value="{{$cantidad_pedido->count()}}" id="contador">
              <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                <thead>
                  <tr role="row">
                  <th width="7.69%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">C??digo</th>
                        <th width="7.69%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Nombre: activate to sort column descending" aria-sort="ascending">Nombre</th>
                        <th width="7.69%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Cantidad: activate to sort column descending" aria-sort="sorting">Cantidad</th>
                        <th width="7.69%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Serie: activate to sort column descending" aria-sort="sorting">Serie</th>
                        <!--th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Precio de compra</th-->
                        <th width="7.69%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Bodega: activate to sort column ascending">Bodega</th>
                        <th width="7.69%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Serie: activate to sort column descending" aria-sort="sorting">Lote</th>
                        <th width="7.69%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Serie: activate to sort column descending" aria-sort="sorting">Registro Sanitario</th>
                        <th width="12.69%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Fecha de Vecimiento: activate to sort column ascending">Fecha de Vecimiento</th>
                        <th width="10" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Fecha de Vecimiento: activate to sort column ascending">
                          Descuento %
                        </th>
                        <th width="7.69%%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Fecha de Vecimiento: activate to sort column ascending">Precio Unitario</th>
                        <th width="7.69%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Fecha de Vecimiento: activate to sort column ascending">Precio Final</th>
                        <!--<th width="7.69%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Consecion: activate to sort column ascending">Consecion</th>-->
                        <!--th width="2.69%" tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Acci??n</th-->

                  </tr>
                </thead>
                <tbody id="crear">
                @php
                  $i = 0;
                  $subtotal_12 = 0;
                  $subtotal_0 = 0;

                @endphp
                @foreach($cantidad_pedido as $detalle_pedido)
                  @php
                    if($detalle_pedido->iva == '1'){
                      $subtotal_12 = $subtotal_12 + (($detalle_pedido->precio * $detalle_pedido->cantidad)) - $detalle_pedido->descuento ;
                    }else{
                      $subtotal_0 = $subtotal_0 + (($detalle_pedido->precio * $detalle_pedido->cantidad)) - $detalle_pedido->descuento;
                    }
                  @endphp
                @endforeach
                @php
                  
                  $iva = $subtotal_12 * 0.12;
                  $total_final = ($subtotal_12*1.12) + $subtotal_0 ;
                @endphp

                @foreach($cantidad_pedido as $detalle_pedido)
                @php
                if($detalle_pedido->id_producto!=null) {
                  $producto = \Sis_medico\Producto::find($detalle_pedido->id_producto);
                  $disable = ""; $existencia ="";
                  if ($detalle_pedido->serie!=null) {
                    $descargo = \Sis_medico\Pedido::egreso_paciente($detalle_pedido->serie);
                    $existencia = \Sis_medico\InvInventarioSerie::inventarioSerie($detalle_pedido->serie);
                    if ($descargo and $existencia ==0) {
                      $disable = "disabled='disabled'";
                    }
                  }

                }
                @endphp
                <tr id="dato{{$i}}" >
                  <td>{{$detalle_pedido->codigo}} <input type='hidden' id='id_movimiento{{$i}}' name='id_movimiento{{$i}}' value='{{$detalle_pedido->id}}' > <input type='hidden' id='visibilidad{{$i}}' name='visibilidad{{$i}}' value='1' > <input type='hidden' id='iva{{$i}}' value='{{$detalle_pedido->iva}}' > <input type="hidden" name="id{{$i}}" value="{{$detalle_pedido->id_producto}}"> <input type="hidden" name="usos{{$i}}" value="{{$detalle_pedido->usos}}"> </td>
                  <td>{{$detalle_pedido->nombre_producto}}</td>
                  <td class="derecha" ><input class="derecha" style="width: 60px" onkeyup ='total_calculo({{$i}})' @if($producto->despacho == 0) type="text" @else type="hidden" @endif name="cantidad{{$i}}" id="cantidad{{$i}}" value="{{$detalle_pedido->cantidad}}" {{$disable}} > {{$detalle_pedido->cantidad}} {{-- @if($producto->despacho == 1) 1 @endif --}}</td>
                  <td>{{$detalle_pedido->serie}} <input type="hidden" name="serie{{$i}}" value="{{$detalle_pedido->serie}}"></td>
                  <td>
                    <input type='text' size='10' value='@if($bodega_principal!=null){{$bodega_principal->nombre}}@endif' readonly>
                    <input type='hidden' name='id_bodega{{$i}}' id='id_bodega{{$i}}' value='@if($bodega_principal!=null){{$bodega_principal->id}}@endif' >
                    <!--select name="id_bodega{{$i}}" >
                      @foreach($bodegas as $value)
                        <option value="{{$value->id}}" @if($detalle_pedido->nombre_bodega == $value->nombre) selected @endif >{{$value->nombre}}</option>
                      @endforeach
                    </select-->
                  </td>
                  <td><div class='form-group  lote_error{{$i}}'><input type="text" name="lote{{$i}}" id="lote{{$i}}" value="{{$detalle_pedido->lote}}" {{$disable}}></div></td>
                  <td>{{$detalle_pedido->registro_sanitario}}</td>
                  <td><input type="date" name="fecha_vencimiento{{$i}}" id="f_venci" value="{{$detalle_pedido->fecha_vencimiento}}" {{$disable}}></td>
                  <td><input style="width: 60px" onkeyup ='total_calculo({{$i}})' value='{{$detalle_pedido->descuentop}}' id='descuento{{$i}}' type='text' name='descuento{{$i}}' readonly {{$disable}} > <input type='hidden' name="descuentof{{$i}}" id="descuentof{{$i}}" value="{{$detalle_pedido->descuento}}" </td>
                  <td class="derecha" ><input class="derecha" style="width: 60px" onkeyup ='total_calculo({{$i}})' value='{{$detalle_pedido->precio}}' readonly id='precio{{$i}}' type='number' name='precio{{$i}}' {{$disable}} ></td>
                  <td class="derecha"> <span id='precio_final{{$i}}' name='precio_final{{$i}}'>{{($detalle_pedido->precio * $detalle_pedido->cantidad) - $detalle_pedido->descuento }}</span>  </td>
                  <!--<td> <input id='consecion_det{{$i}}' name='consecion_det{{$i}}' type='checkbox' @if($detalle_pedido->consecion_det == '1' ) checked @endif value='1' class='flat-blue' style='position: absolute;'> </td>-->
                  <!--td><button type='button' onclick='eliminardato({{$i}})' class='btn btn-warning btn-margin' {{$disable}}>Eliminar</button></td-->
                  @php $i++; @endphp
                </tr>
                @endforeach
                </tbody>


                <tfoot>
                  <tr>
                    <td colspan="9"></td>
                    <td>Subtotal 12%:</td>
                    <td><input type="hidden" name="subtotal_12" id="subtotal_12" value="{{$subtotal_12}}"> <input class="derecha"  style="width: 55px;" type="text" readonly id="subtotal_12_1" value="{{$subtotal_12}}"></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td colspan="9"></td>
                    <td>Subtotal 0%:</td>
                    <td><input type="hidden" name="subtotal_0" id="subtotal_0" value="{{$subtotal_0}}"> <input class="derecha"  style="width: 55px;" type="text" readonly id="subtotal_0_1" value="{{$subtotal_0}}"></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td colspan="9"></td>
                    <td>Descuento :</td>
                    <td><input type="hidden" name="descuent" id="descuent" value="{{$pedido->descuento}}"> <input class="derecha"  style="width: 55px;" type="text" name="descuentx" readonly id="descuentx" value="{{$pedido->descuento}}"></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td colspan="9"></td>
                    <td>IVA:</td>
                    <td><input type="hidden" name="iva" id="iva" value="{{$iva}}"> <input class="derecha" style="width: 55px;" type="text" readonly id="iva_1" value="{{$iva}}"></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td colspan="9"></td>
                    <td>Total:</td>
                    <td><input type="hidden" name="total" id="total" value="{{$total_final}}"> <input class="derecha" style="width: 55px;" type="text" readonly id="total_1" value="{{$total_final}}" ></td>
                    <td></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
          <div class="box-footer" style="text-align: center;">
            <button type="button" id="envio" class="btn btn-primary btn-gray" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando Informacion">
                 <i class="fa fa-pencil-square"></i>  Actualizar
            </button>
            @if($pedido->tipo!=3)
            <button type="submit" class="btn btn-success btn-gray" formaction="{{route('ingreso.generar_facturas')}}" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando Informacion"> <i class="fa fa-save"></i> Generar Pedido</button>
            @endif
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
<!-- /.content -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
  <script src="{{ asset ("/js/icheck.js") }}"></script>
  <script>
    $(document).ready(function(){
      $('#consecion').iCheck({
        checkboxClass: 'icheckbox_flat-blue',
        increaseArea: '20%' // optional
      });
      $('#consecion_det').iCheck({
        checkboxClass: 'icheckbox_flat-blue',
        increaseArea: '20%' // optional
      });
      $('.select2').select2({
      tags: false
    });
    });
  </script>
<script type="text/javascript">
    function total_calculo(id){
      cantidad = parseInt($('#cantidad'+id).val());
      valor = parseFloat($('#precio'+id).val());
      descuento= parseFloat($('#descuento'+id).val());
      total = cantidad * valor;
      
      if(descuento>0){
        descf=(total*descuento/100);
        total = (cantidad * valor) - descf;
        console.log("el valor del descuento es"+descf);
              $("#descuentof"+id).val(descf.toFixed(2,2));
      }else{
        $("#descuentof"+id).val(0);
      }


      $('#precio_final'+id).text(total);
      suma_totales();
    }


  function suma_totales(){
      contador  =  0;
      subtotal_0 = 0;
      subtotal_12 = 0;
      iva = 0;
      total = 0;
      desc=0;
      $("#crear tr").each(function(){
        $(this).find('td')[0];
        visibilidad = $(this).find('#visibilidad'+contador).val();
        if(visibilidad == 1){
          cantidad = parseInt($(this).find('#cantidad'+contador).val());
          valor = parseFloat($(this).find('#precio'+contador).val());
          descuento = parseFloat($(this).find('#descuentof'+contador).val());
          total = (cantidad * valor) - descuento;
          
          desc+=descuento;
          console.log(descuento);
          iva = $(this).find('#iva'+contador).val();
          if(iva == 1){
            subtotal_12 = subtotal_12 + total;
          }else{
            subtotal_0 = subtotal_0 + total;
          }

        }
        contador = contador+1;
      });
      iva = subtotal_12 * 0.12;
      if(desc>0){
        total= subtotal_12+ subtotal_0 +iva ;
      }else{
        total = subtotal_12 + subtotal_0 + iva;
      }
      $('#subtotal_12_1').val(subtotal_12);
      $('#subtotal_0_1').val(subtotal_0);
      $('#iva_1').val(iva);
      $('#total_1').val(total);
      $("#descuentx").val(desc.toFixed(2,2));
      $('#subtotal_12').val(subtotal_12);
      $('#subtotal_0').val(subtotal_0);
      $('#iva').val(iva);
      $('#total').val(total);
    }

  $(document).ready(function()
  {
    $('#fecha').datetimepicker({
              useCurrent: false,
              format: 'YYYY/MM/DD',
              defaultDate: new Date()
               //Important! See issue #1075

          });
    $('#vencimiento').datetimepicker({
              useCurrent: false,
              format: 'YYYY/MM/DD',
              defaultDate: new Date()

               //Important! See issue #1075

          });
    src = "{{route('producto.codigo')}}";

    $("#codigo").autocomplete({
        source: function( request, response ) {
          $.ajax( {
            url: src,
            dataType: "json",
            data: {
              term: request.term
            },
            success: function( data ) {
              response(data);
            }
          } );
        },
        minLength: 1,
    });

    $("#codigo").change( function(){
        $.ajax({
          type: 'post',
          url:'{{route('producto.codigo2')}}',
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          type: 'POST',
          datatype: 'json',
          data: $("#codigo"),
          success: function(data){
              $('#nombre').val(data);
          },
          error: function(data){
              console.log(data);
          }
      })
    });
    var bodegas

    $('#busqueda').click(function(event){

      $.ajax({
          type: 'post',
          url:"{{route('ingreso.formulario')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

          datatype: 'json',
          data: $("#ingreso").serialize(),
          success: function(data){
            //console.log(data);
            id= document.getElementById('contador').value;
            var midiv = document.createElement("tr");
              midiv.setAttribute("id","dato"+id);
              if(data[0].despacho == 0)
              {
                var f = new Date();
                var dia = ('0' + (f.getDate())).slice(-2);
                var mes = ('0' + (f.getMonth()+1)).slice(-2);
                var segundos = ('0' + (f.getMilliseconds())).slice(-1);
                var id2 = id.slice(-1);

                var serie = f.getFullYear().toString()+mes.toString()+dia.toString()+f.getHours().toString()+f.getMinutes().toString()+f.getSeconds().toString()+segundos.toString()+id2;

                midiv.innerHTML = `
                                      <input type='hidden' id='iva${id}' value="${data[0].iva}">
                                      <td>
                                          <input type='hidden' id='id_movimiento${id}' name='id_movimiento${id}'>
                                          <input type='hidden' id='visibilidad${id}' name='visibilidad${id}' value='1' >
                                          <input name='id${id}' type='hidden' value="${data[0].id}" >
                                          <input name='usos${id}' type='hidden' value="${data[0].usos}" >
                                          ${data[0].codigo}"
                                      </td>
                                      <td>
                                          ${data[0].nombre}
                                      </td>
                                      <td class='derecha'>
                                          <input type='text' value='1' class='input-number derecha' onkeypress='return valida(event)' onkeyup='total_calculo(${id})' style='width: 60px;' id='cantidad${id}' name='cantidad${id}' >
                                      </td>
                                      <td>
                                          <input type='hidden' value='${serie}' name='serie${id}' id='s${id}'>${serie}
                                      </td>
                                      <td>
                                      <input type='text' size='10' value='@if($bodega_principal!=null){{$bodega_principal->nombre}}@endif' readonly>
                                      <input type='hidden' name='id_bodega${id}' id='id_bodega${id}' value='@if($bodega_principal!=null){{$bodega_principal->id}}@endif' >
                                        <!-- select name='id_bodega${id}' >
                                          @foreach($bodegas as $value)
                                            <option value='{{$value->id}}' >{{$value->nombre}}</option>
                                          @endforeach
                                      </select -->
                                      </td>
                                      <td>
                                          <div class='form-group  lote_error${id}'>
                                            <input type='text' name='lote${id}'  required>
                                          </div>
                                      </td>
                                      <td>
                                          <div class='form-group'>${data[0].registro_sanitario}"</div>
                                      </td>
                                      <td>
                                          <input type='date' class='input-number' value='{{date("Y-m-d")}}' name='fecha_vencimiento${id}' >
                                      </td>
                                      <td>
                                          <input name='descuento${id}' id='descuento${id}' style='width: 60px;'  value='0.00' onkeyup='total_calculo(${id})'>
                                          <input type='hidden' name='descuentof${id}' id='descuentof${id}' value='0.00'>
                                      </td>
                                      <td>
                                          <input class="derecha" onkeyup='total_calculo(${id})' value='0' id='precio${id}' type='number' style='width: 60px;' name='precio${id}' >
                                      </td>
                                      <td id='precio_final${id}' >
                                      </td>
                                      <td>
                                        <button type='button' onclick='eliminardato(${id})' class='btn btn-warning btn-margin'>Eliminar</button>
                                      </td>`;
              }
              if(data[0].despacho == 1)
              {
                var f = new Date();
                var dia = ('0' + (f.getDate())).slice(-2);
                var mes = ('0' + (f.getMonth()+1)).slice(-2);
                var segundos = ('0' + (f.getMilliseconds())).slice(-1);
                var id2 = id.slice(-1);

                var serie = f.getFullYear().toString()+mes.toString()+dia.toString()+f.getHours().toString()+f.getMinutes().toString()+f.getSeconds().toString()+segundos.toString()+id2;

                midiv.innerHTML = `  <input type='hidden' id="iva${id}" value="${data[0].iva}">
                                      <td>
                                          <input type='hidden' id='id_movimiento${id}' name='id_movimiento${id}' >
                                          <input type='hidden' id='visibilidad${id}' name='visibilidad${id}' value='1' >
                                          <input name="id${id}" type='hidden' value="${data[0].id}" >
                                          <input name='usos${id}' type='hidden' value="${data[0].usos}" >
                                          ${data[0].codigo}
                                      </td>
                                      <td>${data[0].nombre}</td>
                                      <td class='derecha'>
                                        <input type='hidden' value='1' id='cantidad${id}' class='derecha' name='cantidad${id}'>
                                         1
                                      </td>
                                      <td>
                                        <input type='hidden' value="${serie}" name="serie${id}" id="s${id}" >
                                        ${serie}
                                      </td>
                                      <td>
                                      <input type='text' size='10' value='@if($bodega_principal!=null){{$bodega_principal->nombre}}@endif' readonly >
                                      <input type='hidden' name='id_bodega${id}' id='id_bodega${id}' value='@if($bodega_principal!=null){{$bodega_principal->id}}@endif' >
                                        <!--select name="id_bodega${id}">
                                          @foreach($bodegas as $value)
                                            <option value='{{$value->id}}'>{{$value->nombre}}</option>
                                          @endforeach
                                        </select-->
                                      </td>
                                      <td>
                                        <div class='form-group  lote_error${id}'>
                                          <input type='text' name="lote${id}" id= required>
                                        </div>
                                      </td>
                                      <td>
                                        <div class='form-group'>${data[0].registro_sanitario}</div>
                                      </td>
                                      <td>
                                        <input type='date' class='input-number' value='{{date("Y-m-d")}}' name='fecha_vencimiento${id}' >
                                      </td>
                                      <td>
                                        <input name='descuento${id}' id='descuento${id}' style='width: 60px;'  value='0.00' onkeyup='total_calculo(${id})'>
                                        <input type='hidden' name='descuentof${id}' id='descuentof${id}' value='0.00'>
                                      </td>
                                      <td class="derecha" >
                                        <input class="derecha" type='number' name='precio${id}' id='precio${id}' style='width: 60px;' value='0' onkeyup='total_calculo(${id})' >
                                      </td>
                                      <td class="derecha" id='precio_final${id}'>
                                      </td>
                                      <td>
                                        <button type='button' onclick='eliminardato(${id})' class='btn btn-warning btn-margin'>Eliminar</button>
                                      </td>`;

              }

              document.getElementById('crear').appendChild(midiv);
              window.onbeforeunload = confirmarSalida;
              id = parseInt(id);
              id = id+1;
              document.getElementById('contador').value = id;
              $("#codigo").val("");
              $("#nombre").val("");
          },
          error: function(data){
              console.log(data);
          }
      })
    });

    $('#envio').click(function(event){
      $('#envio').button('loading');

      var formulario = document.forms["frm"];
      var pedido = formulario.pedido.value;
      var num_factura = formulario.num_factura.value;
      var fecha = formulario.fecha.value;
      var vencimiento = formulario.vencimiento.value;
      var id_proveedor = formulario.id_proveedor.value;
      // var id_empresa = formulario.id_empresa.value;
      var contador = formulario.contador.value;
              var tipo_= formulario.tipo_.value;
      var msj = "";
      if(pedido == "")
          msj += "Por favor, ingrese el numero del Pedido\n";
      if(num_factura == "")
          msj += "Por favor, ingrese el numero del Factura\n";
      if(fecha == "")
          msj += "Por favor, ingrese la fecha de la Orden\n";
      if(vencimiento == "")
          msj += "Por favor, ingrese la fecha de vencimiento\n";
      if(id_proveedor == "")
          msj += "Por favor, seleccione el proveedor\n";
          if(tipo_ == "")
            msj += "Por favor, seleccione el tipo <br/>";
      //  if(id_empresa == "")
      //     msj += "Por favor, seleccione la empresa\n";
      if(contador == 0)
          msj += "Por favor, ingrese al menos un producto\n";
      if(msj == "")
      {
          $.ajax({
            type: 'post',
            url:"{{route('ingreso.actualizar_pedido')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

            datatype: 'json',
            data: $("#frm").serialize(),
            success: function(data){
              console.log(data);
              $('#envio').button('reset');
              var dato_url = "{{route('producto.index')}}";
              window.onbeforeunload = beforeVoid;
              Swal.fire('Correcto!','Datos Actualizados Correctamente','success')
              setTimeout(function(){ location.href ="{{ route('codigo.barra')}}"; }, 3000);
              // location.href ="{{ route('codigo.barra')}}";

            },
            error: function(data){
               console.log(data);
               $('#envio').button('reset');
                var errores = data.responseJSON;
                var contador = document.getElementById('contador').value;
                var error ;
                for(i=0; i<contador; i++) {
                  if(errores['lote'+i] != undefined){
                    error = errores['lote'+i];
                    $('.lote_error'+i).addClass("has-error");
                    $('.general').addClass("has-error");
                    $('#lote_errores').empty().html(errores['lote'+i]);
                  }
                }
                if(data.responseJSON.pedido!=null){
                    $(".cl_pedido").addClass("has-error");
                    $('#str_pedido').empty().html(data.responseJSON.pedido);
                }
                if(data.responseJSON.id_proveedor!=null){
                    $(".cl_id_proveedor").addClass("has-error");
                    $('#str_id_proveedor').empty().html(data.responseJSON.pedido);
                }
            }
          })
      }
      else{
        Swal.fire("Mensaje: ",msj,"error");
      }
      $('#envio').button('reset');

    });
    src2 = "{{route('producto.nombre')}}";

    $("#nombre").autocomplete({
        source: function( request, response ) {
          $.ajax( {
            url: src2,
            dataType: "json",
            data: {
              term: request.term
            },
            success: function( data ) {
              response(data);
            }
          } );
        },
        minLength: 3,
      } );

    $("#nombre").change( function(){
        $.ajax({
          type: 'post',
          url:"{{route('producto.nombre2')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          type: 'POST',
          datatype: 'json',
          data: $("#nombre"),
          success: function(data){
              $('#codigo').val(data);
          },
          error: function(data){
              console.log(data);
          }
      })
    });
  });


  function confirmarSalida()
  {
      return "Va a abandonar esta p??gina. Cualquier cambio no guardado se perder??";
  }

  function beforeVoid()
  {}

  function valida(e){
      tecla = (document.all) ? e.keyCode : e.which;

      //Tecla de retroceso para borrar, siempre la permite
      if (tecla==8){
          return true;
      }

      // Patron de entrada, en este caso solo acepta numeros
      patron =/[0-9]/;
      tecla_final = String.fromCharCode(tecla);
      return patron.test(tecla_final);
  }

  function eliminardato(valor)
  {
    var nombre1 = "dato"+valor;
    var nombre2 = 'visibilidad'+valor;
    document.getElementById(nombre1).style.display='none';
    document.getElementById(nombre2).value = 0;
    suma_totales();
  }

  function calcular() {
      // obtenemos todas las filas del tbody
    var filas=document.querySelectorAll("#example2 tbody tr");

    var total=0;

    // recorremos cada una de las filas
    filas.forEach(function(e) {

        // obtenemos las columnas de cada fila
        var columnas=e.querySelectorAll("td");

        // obtenemos los valores de la cantidad y importe
        var cantidad=parseFloat(columnas[1].textContent);
        var importe=parseFloat(columnas[2].textContent);

        // mostramos el total por fila
        columnas[3].textContent=(cantidad*importe).toFixed(2);

        total+=cantidad*importe;
    });

    // mostramos la suma total
    var filas=document.querySelectorAll("#example2 tfoot tr td");
    filas[1].textContent=total.toFixed(2);
  }
</script>
@endsection
