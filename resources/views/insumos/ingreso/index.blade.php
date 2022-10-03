@extends('insumos.ingreso.base')
@section('action-content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
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
        .td_der{
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
                <h3 class="box-title">{{trans('winsumos.ingreso_bodega_producto')}}</h3>
              </div>
              <div class="col-md-8" style="text-align: right;">
                        <a type="button" href="{{URL::previous()}}" class="btn btn-primary btn-sm">
                          <span class="glyphicon glyphicon-arrow-left">{{trans('winsumos.regresar')}}</span>
                        </a>
              </div>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <form method="POST" id="ingreso">
              <div class="box box-default">
                <div class="box-header with-border">
                  <h3 class="box-title">{{trans('winsumos.ingreso_producto')}}</h3>
                  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                  </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                          <label for="inputid" class="col-sm-3 control-label">{{trans('winsumos.codigo')}}</label>
                          <div class="col-sm-9">
                            <input value="" type="text" class="form-control" name="codigo" id="codigo" placeholder="{{trans('winsumos.codigo')}}"  style="text-transform:uppercase;">
                          </div>
                      </div>
                    </div>
                    <div class="col-md-1">
                      <button type="button" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-search"></span></button>    
                    </div> 
                    <div class="col-md-6">
                      <div class="form-group">
                          <label for="inputapellido" class="col-sm-3 control-label">{{trans('winsumos.nombre')}}</label>
                          <div class="col-sm-9">
                            <input value="" type="text" class="form-control"  id="nombre" name="nombre" id="inputapellido" placeholder="{{trans('winsumos.nombre')}}" style="text-transform:uppercase;">
                          </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                  <button type="button" id="busqueda" class="btn btn-primary">
                    {{trans('winsumos.guardar')}}
                  </button>
                </div>
              </div>
          </form>
          <div class="panel panel-default">
            <form method="POST"  name="frm" id="frm">
              <div class="panel-heading">
                <div class="row">
                    <!-- Fecha -->
                    <div class="form-group col-md-6">
                        <label for="fecha" class="col-md-4 control-label">{{trans('winsumos.fecha_pedido')}}</label>
                        <div class="col-md-8">
                          <div class="input-group date">
                            <div class="input-group-addon">
                              <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" value="" name="fecha" class="form-control" id="fecha"  placeholder="AAAA/MM/DD">
                          </div>
                        </div>
                    </div>
                    <!-- Numero de Pedido -->
                    <div class="form-group cl_pedido col-md-6 {{ $errors->has('pedido') ? ' has-error' : '' }}">
                        <label for="pedido" class="col-md-4 control-label">{{trans('winsumos.numero_pedido')}}</label>
                        <div class="col-md-8">
                          <input id="pedido" type="text" class="form-control" name="pedido" value="{{ old('pedido') }}" onchange="validarNoPedido()" onkeyup="valida(event);" required autofocus>
                        </div>
                        <span class="help-block">
                          <strong id="str_pedido"></strong>
                        </span>
                    </div>

                    <div class="form-group cl_pedido col-md-6 {{ $errors->has('num_factura') ? ' has-error' : '' }}">
                        <label for="num_factura" class="col-md-4 control-label">{{trans('winsumos.numero_documento')}}</label>
                        <div class="col-md-8">
                          <input id="num_factura" type="text" class="form-control" name="num_factura" value="{{ old('num_factura') }}" required autofocus>
                        </div>
                    </div>
                    <!-- Vencimiento -->
                    <div class="form-group col-md-6{{ $errors->has('vencimiento') ? ' has-error' : '' }}">
                        <label for="vencimiento" class="col-md-4 control-label">{{trans('contableM.fechacaducidad')}}</label>
                        <div class="col-md-8">
                          <div class="input-group date">
                            <div class="input-group-addon">
                              <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" value="" name="vencimiento" class="form-control" id="vencimiento"  placeholder="AAAA/MM/DD">
                          </div>
                        </div>
                    </div>
                    <!-- Proveedor -->
                    <div class="form-group col-md-6{{ $errors->has('id_proveedor') ? ' has-error' : '' }}">
                        <label for="id_proveedor" class="col-md-4 control-label">{{trans('winsumos.proveedores')}}</label>
                        <div class="col-md-8">
                          <select name="id_proveedor" style="width: 100%;" class="form-control select2" required="" name="id_proveedor">
                              <option value="">{{trans('winsumos.seleccione')}}</option>
                            @foreach($proveedores as $value)
                              <option value="{{$value->id}}" >{{$value->nombrecomercial}}</option>
                            @endforeach
                          </select>
                        </div>
                    </div>
                    <!-- update headers type date: 17 Nov 2020  -->
                    <div class="form-group col-md-6{{ $errors->has('id_proveedor') ? ' has-error' : '' }}">
                      <label for="bodega_recibe" class="col-md-4 control-label">{{trans('winsumos.bodega_recibe')}}</label>
                      <div class="col-md-8">
                        <select name="bodega_recibe" class="form-control select2 " style="width: 100%;" required="required" name="bodega_recibe" id="bodega_recibe">
                          <!--l-->
                          <option value="">{{trans('winsumos.seleccione')}}</option>
                          @foreach($bodegas as $value)
                          
                          <option value="{{$value->id}}" @if(env('BODEGA_PRINCIPAL',19)==$value->id) selected @endif>{{$value->nombre}}&nbsp;@if(isset($value->empresa))-&nbsp;{{$value->empresa->nombrecomercial}}@endif
                          
                          @endforeach
                          <!-- termina l-->
                          <!-- codigo antiguo
                          <option value="">Seleccione..</option>
                          @foreach($bodegas as $value)
                          <option value="{{$value->id}}" @if(env('BODEGA_PRINCIPAL',19)==$value->id) selected @endif>{{$value->nombre}}&nbsp;@if(isset($value->empresa))-&nbsp;{{$value->empresa->nombrecomercial}}@endif</option>
                          @endforeach
                          termina antiguo -->
                      </select>
                      </div>
                  </div>
                    <div class="form-group col-md-6{{ $errors->has('id_proveedor') ? ' has-error' : '' }}">
                        <label for="tipo_" class="col-md-4 control-label">{{trans('winsumos.Tipo')}} xd </label>
                        <div class="col-md-8">
                          {{-- <select name="tipo_" class="form-control select2 " style="width: 100%;" required="" name="tipo_" id="tipo_">
                              <option value="">{{trans('winsumos.seleccione')}}</option>
                              <option value="1">{{trans('winsumos.guia_remision')}}</option>
                              <option value="2">{{trans('winsumos.fact_contra_entrega')}}</option>
                              <option value="3">{{trans('contableM.factura')}}</option>
                          </select> --}}
                          <select name="tipo_" class="form-control select2 " style="width: 100%;" required="" name="tipo_" id="tipo_">
                            <option value="">{{trans('winsumos.seleccione')}}</option>
                            @foreach($doc_bodega as $value)
                            <option value="{{$value->id}}" >{{$value->documento}} @if($value->tipo == 'C') ({{trans('winsumos.concesion')}}) @endif</option>
                            @endforeach
                        </select>
                        </div>
                    </div>
                    <!-- MULTIPLE EMPRESA
                    <div class="form-group col-md-6{{ $errors->has('id_empresa') ? ' has-error' : '' }}">
                        <label for="id_empresa" class="col-md-4 control-label">Empresa</label>
                        <div class="col-md-8">
                          <select id="id_empresa" class="form-control select2" style="width: 100%;" name="id_empresa">
                              <option value="">Seleccione..</option>
                            @foreach($empresa as $value)
                              <option value="{{$value->id}}" >{{$value->nombrecomercial}}</option>
                            @endforeach
                          </select>
                        </div>
                    </div> -->
                    <!-- MULTIPLE EMPRESA-->
                    <!--div class="form-group col-md-6{{ $errors->has('consecion') ? ' has-error' : '' }}">
                        <label for="consecion" class="col-md-4 control-label">Posee Consecion</label>
                        <div class="col-md-8">
                          <input id="consecion" name="consecion" type="checkbox" value="1" class="flat-blue"  style="position: absolute; opacity: 0;">
                        </div>
                    </div-->
                    <!-- Observaciones -->
                    <div class="form-group  col-md-12 {{ $errors->has('observaciones') ? ' has-error' : '' }}">
                        <label for="observaciones" class="col-md-2 control-label">{{trans('winsumos.observacion')}}</label>
                        <div class="col-md-10">
                          <input id="observaciones" type="text" class="form-control" name="observaciones" value="{{ old('observaciones') }}" autofocus>
                        </div>
                    </div>
                </div>
              </div>

              <div class="general form-group col-md-12 ">
                <div class="row">
                <div class="col-md-6">
                <span class="help-block">
                  <strong id="lote_errores"></strong>
                </span>
                </div>
                <div class="col-md-6">

                <span class="help-block">
                  <strong id="fecha_errores"></strong>
                </span>
                </div>

                </div>

              </div>

              <div class="box-body">
                <div class="table-responsive col-md-12">
                  <input name='contador' type="hidden" value="0" id="contador">
                  <table id="example2" class="display compact responsive"  role="grid" aria-describedby="example2_info" style="margin-top:0 !important; width: 100%!important;">
                    <thead>
                      <tr role="row">
                        <th>{{trans('winsumos.codigo')}}</th>
                        <th>{{trans('winsumos.nombre')}}</th>
                        <th>{{trans('winsumos.cantidad')}}</th>
                        <th>{{trans('winsumos.serie')}}</th>
                        <!--th width="7.69%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Serie: activate to sort column descending" aria-sort="sorting">Estado Producto</th-->
                        <!--th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Precio de compra</th-->
                        <th>{{trans('winsumos.bodegas')}}</th>
                        <th>{{trans('winsumos.lote')}}</th>
                        <th>{{trans('winsumos.registro_sanitario')}}</th>
                        <th>{{trans('contableM.fechacaducidad')}}</th>
                        <th>% {{trans('winsumos.descuento')}}</th>
                        <th>{{trans('winsumos.precio_unitario')}}</th>
                        <th>{{trans('winsumos.precio_final')}}</th>
                        {{-- <th width="7.69%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Consecion: activate to sort column ascending">Consecion</th> --}}
                        <th>{{trans('winsumos.accion')}}</th>
                      </tr>
                    </thead>
                    <tbody id="crear">

                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="8"></td>
                        <td colspan="2" class="td_der">{{trans('winsumos.subtotal')}} 12%:</td>
                        <td><input type="hidden" name="subtotal_12" id="subtotal_12"> <input  class="td_der" style="width: 55px;border: 0px;" type="text" readonly id="subtotal_12_1"></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td colspan="8"></td>
                        <td colspan="2" class="td_der">{{trans('winsumos.subtotal')}} 0%:</td>
                        <td><input type="hidden" name="subtotal_0" id="subtotal_0"> <input class="td_der" style="width: 55px;border: 0px;" type="text" readonly id="subtotal_0_1"></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td colspan="8"></td>
                        <td colspan="2" class="td_der">{{trans('winsumos.descuento')}}</td>
                        <td><input type="hidden" name="descuent" id="descuent"> <input  class="td_der" style="width: 55px;border: 0px;" type="text" readonly id="descuentx" name="descuentx"></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td colspan="8"></td>
                        <td colspan="2" class="td_der">{{trans('winsumos.iva')}}</td>
                        <td><input type="hidden" name="iva" id="iva"> <input class="td_der" style="width: 55px;border: 0px;" type="text" readonly id="iva_1"></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td colspan="8"></td>
                        <td colspan="2" class="td_der">{{trans('winsumos.total')}}</td>
                        <td><input type="hidden" name="total" id="total"> <input class="td_der" style="width: 55px;border: 0px;" type="text" readonly id="total_1"></td>
                        <td></td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
              <div class="box-footer" style="text-align: center;">
                <button type="button" id="envio" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando Informacion">
                <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('winsumos.guardar')}}
                </button>
              </div>
            </form>
          </div>
        </div>
    </section>
    <!-- /.content -->
                           

  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
  <script src="{{ asset ("/js/icheck.js") }}"></script>
  <script>
    const validarNoPedido = () =>{
      let pedido = document.getElementById("pedido").value;
      $.ajax({
        url: `{{ route('insumos.ingreso.validarNumeroPedido') }}`,
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        type: 'POST',
        datatype: 'json',
        data: {
          'pedido': pedido
        },
        success: function(data){
          console.log(data);
        },
        error: function(data){

        }
      })
    }

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


    $('#example2').DataTable({
            'paging': false,
            dom: 'lBrtip',
            'lengthChange': false,
            'searching': true,
            'ordering': false,
            'responsive': true,
            'info': false,
            'autoWidth': true,
            'columnDefs': [
                { "width": "5%", "targets": 0 },
                { "width": "5%", "targets": 2 },
                { "width": "10%", "targets": 6 },
                { "width": "5%", "targets": 8 }
            ],
            language: {
                zeroRecords: " "
            },
            buttons: [{
            extend: 'copyHtml5',
            footer: true
            },

            {
            extend: 'excelHtml5',
            footer: true,
            title: "{{trans('winsumos.pedidos')}}"
            },
            {
            extend: 'csvHtml5',
            footer: true
            },
            {
            extend: 'pdfHtml5',
            orientation: 'landscape',
            pageSize: 'LEGAL',
            footer: true,
            title: "{{trans('winsumos.pedidos')}}",
            customize: function(doc) {
                doc.styles.title = {
                color: 'black',
                fontSize: '17',
                alignment: 'center'
                }
            }
            }
        ],
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
        // visibilidad = $(this).find('#visibilidad'+contador).val();
        visibilidad = $('#visibilidad'+contador).val();
        if(visibilidad == 1){
          cantidad = parseInt($('#cantidad'+contador).val());
          valor = parseFloat($('#precio'+contador).val());
          descuento = parseFloat($('#descuentof'+contador).val());
          total = (cantidad * valor) - descuento;
          desc+=descuento;
          console.log(descuento);
          iva = $('#iva'+contador).val();
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
      src = "{{route('producto.listar')}}";

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
          select: function(data, ui) {
          
          actualiza_nombre_al_seleccionar();

        }
        } );

      $("#codigo").change( function(){
          $.ajax({
            type: 'GET',
            url:"{{route('producto.codigo2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            type: 'POST',
            datatype: 'json',
            data: $("#codigo"),
            success: function(data){
               console.log(data.trim());
               if(data.trim() != 'error')
               {
                $('#nombre').val(data);
              }else{
                $('#nombre').val("{{trans('winsumos.producto_no_encontrado')}}");
               }
            },
            error: function(data){
                console.log(data);
            }
          });
      });
      var bodegas

      $('#busqueda').click(function(event){
        if($('#codigo').val()!=""){
          $.ajax({
              type: 'post',
              url:"{{route('ingreso.formulario')}}",
              headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

              datatype: 'json',
              data: $("#ingreso").serialize(),
              success: function(data){
                console.log(data);
                id= document.getElementById('contador').value;
                var midiv = document.createElement("tr");
                  midiv.setAttribute("id","dato"+id);

                  var costo_promedio = 0;
                  // if (data[0].inv_costo.costo_promedio != null) {
                  //   costo_promedio = parseFloat(data[0].inv_costo.costo_promedio);
                  // }

                  if(data[0].despacho == 0)//Código de producto
                  {
                    var f = new Date();
                    var dia = ('0' + (f.getDate())).slice(-2);
                    var mes = ('0' + (f.getMonth()+1)).slice(-2);
                    var segundos = ('0' + (f.getMilliseconds())).slice(-1);
                    var id2 = id.slice(-1);

                    var serie = f.getFullYear().toString()+mes.toString()+dia.toString()+f.getHours().toString()+f.getMinutes().toString()+f.getSeconds().toString()+segundos.toString()+id2;

                    // midiv.innerHTML = "<input type='hidden' id='iva"+id+"' value='"+data[0].iva+"' ><td><input type='hidden' id='visibilidad"+id+"' name='visibilidad"+id+"' value='1' >   <input name='id"+id+"' type='hidden' value='"+data[0].id+"' > <input name='usos"+id+"' type='hidden' value='"+data[0].usos+"' >"+data[0].codigo+"</td> <td>"+data[0].nombre+"</td> <td> <input type='text' value='1' class='input-number' onkeypress='return valida(event)' onkeyup='total_calculo("+id+")' style='width: 60px;' id='cantidad"+id+"' name='cantidad"+id+"' ></td> <td> <input type='hidden' value='"+serie+"' name='serie"+id+"' id='s"+id+"' >"+serie+"</td> "+" <td><select name='id_bodega"+id+"' > @foreach($bodegas as $value) <option value='{{$value->id}}' >{{$value->nombre}}</option> @endforeach</select></td> <td><div class='form-group  lote_error"+id+"'> <input   type='text' name='lote"+id+"'  required> </div> </td> <td><div class='form-group' >"+data[0].registro_sanitario+"</div> </td> <td> <input type='date' class='input-number' value='{{date("Y-m-d")}}' name='fecha_vencimiento"+id+"' ></td><td> <input name='descuento"+id+"' id='descuento"+id+"' style='width: 50%;'  value='0.00' onkeyup='total_calculo("+id+")'> <input type='hidden' name='descuentof"+id+"' id='descuentof"+id+"' value='0.00'> </td><td><input onkeyup='total_calculo("+id+")' value='0' id='precio"+id+"' type='number' style='width: 60px;' name='precio"+id+"' ></td><td id='precio_final"+id+"' ></td> <td> <input id='consecion_det"+id+"' name='consecion_det"+id+"' type='checkbox' value='1' class='flat-blue' style='position: absolute;'> </td> <td> <button type='button' onclick='eliminardato("+id+")' class='btn btn-warning btn-margin'>Eliminar</button></td> ";
                    midiv.innerHTML = "<input type='hidden' id='iva"+id+"' value='"+data[0].iva+"' ><td><input type='hidden' id='visibilidad"+id+"' name='visibilidad"+id+"' value='1' >  <input name='id"+id+"' type='hidden' value='"+data[0].id+"' > <input name='usos"+id+"' type='hidden' value='"+data[0].usos+"' >"+data[0].codigo+"</td> "
                    +" <td>"+data[0].nombre+"</td> <td> <input type='text' value='1' class='input-number td_der'  onkeypress='return valida(event)' onkeyup='total_calculo("+id+")' style='width: 60px;text-align: right;' id='cantidad"+id+"' name='cantidad"+id+"' ></td> "
                    +" <td> <input type='hidden' value='"+serie+"' name='serie"+id+"' id='s"+id+"' >"+serie+"</td> "
                    // +" <td><select name='id_bodega"+id+"' > @foreach($bodegas as $value) <option value='{{$value->id}}' @if(env('BODEGA_PRINCIPAL',13)==$value->id) selected @endif>{{$value->nombre}}</option> @endforeach</select></td>
                    +" <td><input type='text' size='10' value='@if (isset($bodega_principal->id )) {{$bodega_principal->nombre}} @endif' readonly> <input type='hidden' id='id_bodega"+id+"' name='id_bodega"+id+"' value='@if(isset($bodega_principal->id )){{$bodega_principal->id}}@endif' >"
                    +" <td><div class='form-group  lote_error"+id+"'> <input style='width: 90px;' type='text' name='lote"+id+"'  required> </div> </td> "
                    +" <td><div class='form-group' >"+data[0].registro_sanitario+"</div> </td> "
                    +" <td> <div class='form-group fecha_vencimiento_error"+id+"'> <input type='date' class='input-number' value='{{date("Y-m-d")}}' name='fecha_vencimiento"+id+"' > </div></td> "
                    +" <td> <input name='descuento"+id+"' id='descuento"+id+"' class='input-number td_der' style='width: 50%;'  value='0.00' onkeyup='total_calculo("+id+")'> <input type='hidden' name='descuentof"+id+"' id='descuentof"+id+"' value='0.00'> </td> "
                    +" <td><input class='td_der' onkeyup='total_calculo("+id+")' value='"+costo_promedio+"' id='precio"+id+"' type='text' style='width: 60px;' name='precio"+id+"' ></td> "
                    +" <td id='precio_final"+id+"' ></td> <button type='button' onclick='eliminardato("+id+")' class='btn btn-warning btn-margin'>{{trans('winsumos.eliminar')}}</button></td> ";
                  }
                  if(data[0].despacho == 1)//Código de serie
                  {
                      var f = new Date();
                      var dia = ('0' + (f.getDate())).slice(-2);
                      var mes = ('0' + (f.getMonth()+1)).slice(-2);
                      var segundos = ('0' + (f.getMilliseconds())).slice(-1);
                      var id2 = id.slice(-1);

                      var serie = f.getFullYear().toString()+mes.toString()+dia.toString()+f.getHours().toString()+f.getMinutes().toString()+f.getSeconds().toString()+segundos.toString()+id2;

                      midiv.innerHTML = "<input type='hidden' id='iva"+id+"' value='"+data[0].iva+"' ><td> <input type='hidden' id='visibilidad"+id+"' name='visibilidad"+id+"' value='1' >   <input name='id"+id+"' type='hidden' value='"+data[0].id+"' > <input name='usos"+id+"' type='hidden' value='"+data[0].usos+"' >"+data[0].codigo+"</td> <td>"+data[0].nombre+"</td> "
                      +" <td  style='text-align: right;'> <input type='hidden'  value='1' id='cantidad"+id+"' name='cantidad"+id+"' > 1 </td> "
                      +" <td> <input type='hidden' value='"+serie+"' name='serie"+id+"' id='s"+id+"' >"+serie+"</td>  "
                      // +" <td><select name='id_bodega"+id+"' > @foreach($bodegas as $value) <option value='{{$value->id}}' >{{$value->nombre}}</option> @endforeach</select></td> "
                      +" <td><input type='text' size='10' value='@if (isset($bodega_principal->id )) {{$bodega_principal->nombre}} @endif' readonly> <input type='hidden' id='id_bodega"+id+"'  name='id_bodega"+id+"' value='@if(isset($bodega_principal->id )){{$bodega_principal->id}}@endif' >"
                      +" <td> <div class='form-group  lote_error"+id+"'><input style='width: 90px;' type='text' name='lote"+id+"' id='lote"+id+"' required></div></td> "
                      +" <td> <div class='form-group'>"+data[0].registro_sanitario+"</div></td> "
                      +" <td> <div class='form-group fecha_vencimiento_error"+id+"'><input type='date' class='input-number' value='{{date("Y-m-d")}}' name='fecha_vencimiento"+id+"' ></div></td> "
                      +" <td> <input style='width: 50%;' class='td_der' name='descuento"+id+"' id='descuento"+id+"' onkeyup='total_calculo("+id+")' value='0.00'> <input type='hidden' name='descuentof"+id+"' id='descuentof"+id+"' value='0.00'></td> "
                      +" <td><input type='text' name='precio"+id+"' class='td_der' id='precio"+id+"' style='width: 60px;' value='"+costo_promedio+"' onkeyup='total_calculo("+id+")' ></td> "
                      +" <td id='precio_final"+id+"' ></td> <td> <button type='button' onclick='eliminardato("+id+")' class='btn btn-warning btn-margin'>{{trans('winsumos.eliminar')}}</button></td>  ";
                  }

                  /*
                  if($('#tipo_').val()!="1")//Mientras sea distinto a guia de remisión
                  {
                    if(data[0].despacho == 0)//Código de producto
                    {
                    var f = new Date();
                    var dia = ('0' + (f.getDate())).slice(-2);
                    var mes = ('0' + (f.getMonth()+1)).slice(-2);
                    var segundos = ('0' + (f.getMilliseconds())).slice(-1);
                    var id2 = id.slice(-1);

                    var serie = f.getFullYear().toString()+mes.toString()+dia.toString()+f.getHours().toString()+f.getMinutes().toString()+f.getSeconds().toString()+segundos.toString()+id2;

                    midiv.innerHTML = "<input type='hidden' id='iva"+id+"' value='"+data[0].iva+"' ><td><input type='hidden' id='visibilidad"+id+"' name='visibilidad"+id+"' value='1' >   <input name='id"+id+"' type='hidden' value='"+data[0].id+"' > <input name='usos"+id+"' type='hidden' value='"+data[0].usos+"' >"+data[0].codigo+"</td> <td>"+data[0].nombre+"</td> <td> <input type='text' value='1' class='input-number' onkeypress='return valida(event)' onkeyup='total_calculo("+id+")' style='width: 60px;' id='cantidad"+id+"' name='cantidad"+id+"' ></td> <td> <input type='hidden' value='"+serie+"' name='serie"+id+"' id='s"+id+"' >"+serie+"</td> "+" <td><input type='hidden' id='entrada"+id+"' name='entrada"+id+"' value='1'><select name='estado_prod"+id+"' > <option value='1' >Por Consignación</option> <option value='2' selected>Ingreso Directo</option></select></td> <td><select name='id_bodega"+id+"' > @foreach($bodegas as $value) <option value='{{$value->id}}' >{{$value->nombre}}</option> @endforeach</select></td> <td><div class='form-group  lote_error"+id+"'> <input   type='text' name='lote"+id+"'  required> </div> </td> <td><div class='form-group' >"+data[0].registro_sanitario+"</div> </td> <td> <input type='date' class='input-number' value='{{date("Y-m-d")}}' name='fecha_vencimiento"+id+"' ></td><td> <input name='descuento"+id+"' id='descuento"+id+"' style='width: 50%;'  value='0.00' onkeyup='total_calculo("+id+")'> <input type='hidden' name='descuentof"+id+"' id='descuentof"+id+"' value='0.00'> </td><td><input onkeyup='total_calculo("+id+")' value='0' id='precio"+id+"' type='number' style='width: 60px;' name='precio"+id+"' ></td><td id='precio_final"+id+"' ></td> <td> <input id='consecion_det"+id+"' name='consecion_det"+id+"' type='checkbox' value='1' class='flat-blue' style='position: absolute;'> </td> <td> <button type='button' onclick='eliminardato("+id+")' class='btn btn-warning btn-margin'>Eliminar</button></td> ";
                    }
                    if(data[0].despacho == 1)//Código de serie
                    {
                      var f = new Date();
                      var dia = ('0' + (f.getDate())).slice(-2);
                      var mes = ('0' + (f.getMonth()+1)).slice(-2);
                      var segundos = ('0' + (f.getMilliseconds())).slice(-1);
                      var id2 = id.slice(-1);

                      var serie = f.getFullYear().toString()+mes.toString()+dia.toString()+f.getHours().toString()+f.getMinutes().toString()+f.getSeconds().toString()+segundos.toString()+id2;

                      midiv.innerHTML = "<input type='hidden' id='iva"+id+"' value='"+data[0].iva+"' ><td> <input type='hidden' id='visibilidad"+id+"' name='visibilidad"+id+"' value='1' >   <input name='id"+id+"' type='hidden' value='"+data[0].id+"' > <input name='usos"+id+"' type='hidden' value='"+data[0].usos+"' >"+data[0].codigo+"</td> <td>"+data[0].nombre+"</td> <td> <input type='hidden'  value='1' id='cantidad"+id+"' name='cantidad"+id+"' > 1 </td> <td> <input type='hidden' value='"+serie+"' name='serie"+id+"' id='s"+id+"' >"+serie+"</td>  "+" <td><input type='hidden' id='entrada"+id+"' name='entrada"+id+"' value='1'><select name='estado_prod"+id+"' > <option value='1' >Por Consignación</option> <option value='2' selected>Ingreso Directo</option></select></td> <td><select name='id_bodega"+id+"' > @foreach($bodegas as $value) <option value='{{$value->id}}' >{{$value->nombre}}</option> @endforeach</select></td> <td> <div class='form-group  lote_error"+id+"'><input type='text' name='lote"+id+"' id= required></div></td> <td> <div class='form-group'>"+data[0].registro_sanitario+"</div></td> <td> <input type='date' class='input-number' value='{{date("Y-m-d")}}' name='fecha_vencimiento"+id+"' ></td><td> <input style='width: 50%;' name='descuento"+id+"' id='descuento"+id+"' onkeyup='total_calculo("+id+")' value='0.00'> <input type='hidden' name='descuentof"+id+"' id='descuentof"+id+"' value='0.00'></td><td><input type='number' name='precio"+id+"' id='precio"+id+"' style='width: 60px;' value='0' onkeyup='total_calculo("+id+")' ></td><td id='precio_final"+id+"' ></td> <td> <input id='consecion_det"+id+"' name='consecion_det"+id+"' type='checkbox' value='1' class='flat-blue' style='position: absolute;'> </td> <td> <button type='button' onclick='eliminardato("+id+")' class='btn btn-warning btn-margin'>Eliminar</button></td>  ";
                    }
                  }
                  if($('#tipo_').val()=="1")//Si el documento es guía de remisión
                  {
                    if(data[0].despacho == 0)//Código de producto
                    {
                    var f = new Date();
                    var dia = ('0' + (f.getDate())).slice(-2);
                    var mes = ('0' + (f.getMonth()+1)).slice(-2);
                    var segundos = ('0' + (f.getMilliseconds())).slice(-1);
                    var id2 = id.slice(-1);

                    var serie = f.getFullYear().toString()+mes.toString()+dia.toString()+f.getHours().toString()+f.getMinutes().toString()+f.getSeconds().toString()+segundos.toString()+id2;

                    midiv.innerHTML = "<input type='hidden' id='iva"+id+"' value='"+data[0].iva+"' ><td><input type='hidden' id='visibilidad"+id+"' name='visibilidad"+id+"' value='1' >   <input name='id"+id+"' type='hidden' value='"+data[0].id+"' > <input name='usos"+id+"' type='hidden' value='"+data[0].usos+"' >"+data[0].codigo+"</td> <td>"+data[0].nombre+"</td> <td> <input type='text' value='1' class='input-number' onkeypress='return valida(event)' onkeyup='total_calculo("+id+")' style='width: 60px;' id='cantidad"+id+"' name='cantidad"+id+"' ></td> <td> <input type='hidden' value='"+serie+"' name='serie"+id+"' id='s"+id+"' >"+serie+"</td> "+" <td><input type='hidden' id='entrada"+id+"' name='entrada"+id+"' value='1'><select name='estado_prod"+id+"' > <option value='1' >Por Consignación</option> <option value='2'>Ingreso Directo</option></select></td> <td><select name='id_bodega"+id+"' > @foreach($bodegas as $value) <option value='{{$value->id}}' >{{$value->nombre}}</option> @endforeach</select></td> <td><div class='form-group  lote_error"+id+"'> <input   type='text' name='lote"+id+"'  required> </div> </td> <td><div class='form-group' >"+data[0].registro_sanitario+"</div> </td> <td> <input type='date' class='input-number' value='{{date("Y-m-d")}}' name='fecha_vencimiento"+id+"' ></td><td> <input name='descuento"+id+"' id='descuento"+id+"' style='width: 50%;'  value='0.00' onkeyup='total_calculo("+id+")'> <input type='hidden' name='descuentof"+id+"' id='descuentof"+id+"' value='0.00'> </td><td><input onkeyup='total_calculo("+id+")' value='0' id='precio"+id+"' type='number' style='width: 60px;' name='precio"+id+"' ></td><td id='precio_final"+id+"' ></td> <td> <input id='consecion_det"+id+"' name='consecion_det"+id+"' type='checkbox' value='1' class='flat-blue' style='position: absolute;'> </td> <td> <button type='button' onclick='eliminardato("+id+")' class='btn btn-warning btn-margin'>Eliminar</button></td> ";
                    }
                    if(data[0].despacho == 1)//Código de serie
                    {
                      var f = new Date();
                      var dia = ('0' + (f.getDate())).slice(-2);
                      var mes = ('0' + (f.getMonth()+1)).slice(-2);
                      var segundos = ('0' + (f.getMilliseconds())).slice(-1);
                      var id2 = id.slice(-1);

                      var serie = f.getFullYear().toString()+mes.toString()+dia.toString()+f.getHours().toString()+f.getMinutes().toString()+f.getSeconds().toString()+segundos.toString()+id2;

                      midiv.innerHTML = "<input type='hidden' id='iva"+id+"' value='"+data[0].iva+"' ><td> <input type='hidden' id='visibilidad"+id+"' name='visibilidad"+id+"' value='1' >   <input name='id"+id+"' type='hidden' value='"+data[0].id+"' > <input name='usos"+id+"' type='hidden' value='"+data[0].usos+"' >"+data[0].codigo+"</td> <td>"+data[0].nombre+"</td> <td> <input type='hidden'  value='1' id='cantidad"+id+"' name='cantidad"+id+"' > 1 </td> <td> <input type='hidden' value='"+serie+"' name='serie"+id+"' id='s"+id+"' >"+serie+"</td>  "+" <td><input type='hidden' id='entrada"+id+"' name='entrada"+id+"' value='1'><select name='estado_prod"+id+"' > <option value='1' >Por Consignación</option> <option value='2'>Ingreso Directo</option></select></td> <td><select name='id_bodega"+id+"' > @foreach($bodegas as $value) <option value='{{$value->id}}' >{{$value->nombre}}</option> @endforeach</select></td> <td> <div class='form-group  lote_error"+id+"'><input type='text' name='lote"+id+"' id= required></div></td> <td> <div class='form-group'>"+data[0].registro_sanitario+"</div></td> <td> <input type='date' class='input-number' value='{{date("Y-m-d")}}' name='fecha_vencimiento"+id+"' ></td><td> <input style='width: 50%;' name='descuento"+id+"' id='descuento"+id+"' onkeyup='total_calculo("+id+")' value='0.00'> <input type='hidden' name='descuentof"+id+"' id='descuentof"+id+"' value='0.00'></td><td><input type='number' name='precio"+id+"' id='precio"+id+"' style='width: 60px;' value='0' onkeyup='total_calculo("+id+")' ></td><td id='precio_final"+id+"' ></td> <td> <input id='consecion_det"+id+"' name='consecion_det"+id+"' type='checkbox' value='1' class='flat-blue' style='position: absolute;'> </td> <td> <button type='button' onclick='eliminardato("+id+")' class='btn btn-warning btn-margin'>Eliminar</button></td>  ";
                    }
                  }
                  */
                  document.getElementById('crear').appendChild(midiv);
                  window.onbeforeunload = confirmarSalida;
                  id = parseInt(id);
                  id = id+1;
                  document.getElementById('contador').value = id;
                  // $("#codigo").val("");
                  // $("#nombre").val("");
              },
              error: function(data){
                  console.log(data);
              }
          })
        }

      });

      $('#envio').click(function(event){
        $('#envio').button('loading');
        console.log($("#frm"));
        var formulario = document.forms["frm"];
        var pedido = formulario.pedido.value;
        var num_factura = formulario.num_factura.value;
        var fecha = formulario.fecha.value;
        var vencimiento = formulario.vencimiento.value;
        var id_proveedor = formulario.id_proveedor.value;
        var bodega_recibe = formulario.bodega_recibe.value;
        // var id_empresa = formulario.id_empresa.value;
        var contador = formulario.contador.value;
        var tipo_= formulario.tipo_.value;
        var msj = "";
        if(pedido == "")
            msj += "{{trans('winsumos.ingrese_numero_pedido')}}<br/>";
        if(num_factura == "")
            msj += "{{trans('winsumos.ingrese_numero_factura')}}<br/>";
        if(fecha == "")
            msj += "{{trans('winsumos.ingrese_fecha_orden')}}<br/>";
        if(vencimiento == "")
            msj += "{{trans('winsumos.ingrese_fecha_vencimiento')}}<br/>";
        if(id_proveedor == "")
            msj += "{{trans('winsumos.ingrese_nombre_proveedor')}}<br/>";
        if(bodega_recibe == "")
            msj += "{{trans('winsumos.ingrese_bodega')}}<br/>";
        //  if(id_empresa == "")
        //     msj += "Por favor, seleccione la empresa <br/>";
        if(tipo_ == "")
            msj += "{{trans('winsumos.ingrese_tipo')}}<br/>".tipo_;
        if(contador == 0)
            msj += "{{trans('winsumos.agregar_producto')}}<br/>";

        if(msj == "")
        {
            $.ajax({
              type: 'post',
              url:"{{route('ingreso.guardar')}}",
              headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

              datatype: 'json',
              data: $("#frm").serialize(),
              beforeSend: function(){
                @if(Auth::user()->id=='0931563241' ||Auth::user()->id=='0924383631')
                Swal.fire({
                  title: 'Loading...',
                  width: 600,
                  padding: '3em',
                  background: '#fff',
                  backdrop: `
                    rgba(0,0,123,0.4)
                    url("https://c.tenor.com/DHkIdy0a-UkAAAAC/loading-cat.gif")
                    top
                    no-repeat
                  `
                })
                @endif
              },
              success: function(data){
                console.log(data);
                if(data.msj=='error'){
                  Swal.fire("{{trans('winsumos.error')}}","{{trans('winsumos.error_guardar_pedido')}} <br> "+data.error ,'error');
                }else{
                  $('#envio').button('reset');
                  //var dato_url = "{{route('producto.index')}}";
                  // /window.onbeforeunload = beforeVoid;
                  Swal.fire("{{trans('winsumos.correcto')}}","{{trans('winsumos.guardado_exito')}}",'success');
                 // setTimeout(function(){ location.href ="{{ route('codigo.barra')}}"; }, 3000);
                }

              },
              error: function(data){
                 Swal.close();
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
                    console.log(errores['fecha_vencimiento'+i]);
                    if(errores['fecha_vencimiento'+i]!= undefined){
                      //console.log('aqui');
                      $('.general').addClass("has-error");
                      $('.fecha_vencimiento_error'+i).addClass("has-error");
                      $('#fecha_errores').empty().html(errores['fecha_vencimiento'+i]);
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
          $('#envio').button('reset');
          Swal.fire("{{trans('winsumos.mensaje')}}",msj,"error");
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
        select: function(data, ui) {
          
          actualiza_codigo_al_seleccionar();

        }
      });

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

    function actualiza_codigo_al_seleccionar(){
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
    }

    function actualiza_nombre_al_seleccionar(){
        $.ajax({
          type: 'post',
          url:"{{route('producto.codigo2')}}",
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
    }


    function confirmarSalida()
    {
        return "{{trans('winsumos.abandonar_pagina')}}";
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
      // document.getElementById(nombre1).remove();
      suma_totales();
    }

    function calcular() {
        // obtenemos todas las filas del tbody
      var filas=document.querySelectorAll("#tbl_detalles tbody tr");

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
      var filas=document.querySelectorAll("#tbl_detalles tfoot tr td");
      filas[1].textContent=total.toFixed(2);
    }

  </script>
@endsection
