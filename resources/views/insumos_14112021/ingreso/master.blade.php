@extends('insumos.producto.base')

@section('action-content')
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-sm-4">
                    <h3 class="box-title">Ingreso de Factura</h3>
                </div>
                <div class="col-md-8" style="text-align: right;">
                    <a type="button" href="{{route('codigo.barra')}}" class="btn btn-primary btn-gray">
                        <span class="glyphicon glyphicon-arrow-left"></span>
                    </a>
                    <button onclick="return window.location.href = window.location.href" type="button" class="btn btn-primary  btn-gray">
                                                <i class="fa fa-refresh"></i>
                                            </button>
                </div>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="panel panel-default">
                <form method="POST" name="frm" id="frm">
                    <div class="panel-heading">
                        <div class="row">
                            <!-- Fecha -->
                            <div class="form-group col-md-6">
                                <label for="fecha" class="col-md-4 control-label">Fecha Pedido</label>
                                <div class="col-md-8">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" value="" name="fecha" class="form-control" id="fecha" placeholder="AAAA/MM/DD">
                                    </div>
                                </div>
                            </div>
                            <!-- Numero de Pedido -->



                            <div class="form-group cl_pedido col-md-6 {{ $errors->has('num_factura') ? ' has-error' : '' }}">
                                <label for="num_factura" class="col-md-4 control-label">Número de factura</label>
                                <div class="col-md-8">
                                    <input id="num_factura" type="text" class="form-control" name="num_factura" value="{{ old('num_factura') }}" required autofocus>
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
                                        <input type="text" value="" name="vencimiento" class="form-control" id="vencimiento" placeholder="AAAA/MM/DD">
                                    </div>
                                </div>
                            </div>
                            <!-- Proveedor -->
                            <div class="form-group col-md-6{{ $errors->has('id_proveedor') ? ' has-error' : '' }}">
                                <label for="id_proveedor" class="col-md-4 control-label">Proveedor</label>
                                <div class="col-md-8">
                                    <select name="id_proveedor" style="width: 100%;" class="form-control select2" required="" name="id_proveedor">
                                        <option value="">Seleccione..</option>
                                        @foreach($proveedores as $value)
                                        <option value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- MULTIPLE EMPRESA-->
                            <div class="form-group col-md-6{{ $errors->has('id_empresa') ? ' has-error' : '' }}">
                                <label for="id_empresa" class="col-md-4 control-label">Empresa</label>
                                <div class="col-md-8">
                                    <select id="id_empresa" class="form-control select2" style="width: 100%;" name="id_empresa">
                                        <option value="">Seleccione..</option>
                                        @foreach($empresa as $value)
                                        <option value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- MULTIPLE EMPRESA-->
                            <!--div class="form-group col-md-6{{ $errors->has('consecion') ? ' has-error' : '' }}">
                        <label for="consecion" class="col-md-4 control-label">Posee Consecion</label>
                        <div class="col-md-8">
                          <input id="consecion" name="consecion" type="checkbox" value="1" class="flat-blue"  style="position: absolute; opacity: 0;">
                        </div>
                    </div-->
                            <!-- Observaciones -->
                            <div class="form-group  col-md-12 {{ $errors->has('observaciones') ? ' has-error' : '' }}">
                                <label for="observaciones" class="col-md-2 control-label">Observaciones</label>
                                <div class="col-md-10">
                                    <input id="observaciones" type="text" class="form-control" name="observaciones" value="{{ old('observaciones') }}" autofocus>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="col-md-3 control-label"> Buscador </label>
                                <div class="col-md-9">
                                    <input class="form-control " type="text" name="findpedido" id="findpedido" placeholder="Ingrese número de pedido">

                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <button onclick="getLoader(this.value)" class="btn btn-success btn-gray" type="button"> <i class="fa fa-search"></i> </button>
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
                        <div class="table-responsive col-md-12" id="contes" style=" height: 300px; overflow-y: scroll;">
                            <input name='contador' type="hidden" value="0" id="contador">
                        </div>
                    </div>
                    <table class="table table-bordered table-hover dataTable">
                        <tfoot class='well'>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">Subtotal 12%</td>
                                <td id="subtotal_12" class="text-right px-1">0.00</td>
                                <input type="hidden" name="subtotal_121" id="subtotal_121" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">Subtotal 0%</td>
                                <td id="subtotal_0" class="text-right px-1">0.00</td>
                                <input type="hidden" name="subtotal_01" id="subtotal_01" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">Descuento</td>
                                <td id="descuento" class="text-right px-1">0.00</td>
                                <input type="hidden" name="descuento1" id="descuento1" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">Subtotal sin Impuesto</td>
                                <td id="base" class="text-right px-1">0.00</td>
                                <input type="hidden" name="base1" id="base1" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">Tarifa Iva 12%</td>
                                <td id="tarifa_iva" class="text-right px-1">0.00</td>
                                <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right"><strong>Total</strong></td>
                                <td id="total" class="text-right px-1">0.00</td>
                                <input type="hidden" name="total1" id="total1" class="hidden">
                            </tr>
                        </tfoot>
                    </table>
                    <div class="box-footer" style="text-align: center;">
                        <button type="button" class="btn btn-primary btn_add" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando Informacion">
                            <i class="fa fa-save"></i> &nbsp;&nbsp;Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
        <script>
            $(document).ready(function() {
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
                        });
                    });

                        function getLoader(pedido) {

                            $.ajax({
                                type: "get",
                                url: "{{route('ingreso.details')}}",
                                data: {
                                    'pedido': $('#findpedido').val(),

                                },
                                datatype: "html",
                                success: function(datahtml, data) {
                                    console.log(data);
                                    $('#findpedido').val('');
                                    $("#contes").append(datahtml);
                                    totales(0)
                                },
                                error: function() {
                                    alert('Error, ingrese otro pedido');
                                }
                            });
                        }

                        function verificar(e) {
                            var iva = $('option:selected', e).data("iva");
                            var codigo = $(e).val(); //$('option:selected',e).data("codigo");
                            var usadescuento = $('option:selected', e).data("descuento");
                            var max = $('option:selected', e).data("maxdesc");
                            var modPrecio = $('option:selected', e).data("precio");

                            $(e).parent().children().closest(".codigo_producto").val($('option:selected', e).data("name"));
                            //console.log('codigo', $(e).val());
                            //console.log($(e).parent().next().next().children().closest(".cp"));
                            /*
                            if (modPrecio) {
                                //$(e).parent().next().next().closest(".cp");
                                console.log("modifica precio");
                                $(e).parent().next().next().children().closest(".cp").attr("disabled", "disabled");
                            } else {
                                console.log("no modifca el precio");
                                $(e).parent().next().next().children().closest(".cp").removeAttr("disabled");
                            }*/
                            if (!usadescuento) {
                                $(e).parent().next().next().next().next().next().children().attr("readonly", "readonly");
                                $(e).parent().next().next().next().next().children().attr("readonly", "readonly");
                                $(e).parent().next().next().next().next().children().val(0);
                                $(e).parent().next().next().next().next().next().children().val(0);
                            } else {
                                $(e).parent().next().next().next().next().next().children().removeAttr("readonly");
                                $(e).parent().next().next().next().next().children().removeAttr("readonly");
                                $(e).parent().next().next().next().next().next().children().val(0);
                                $(e).parent().next().next().next().next().children().val(0);
                            }
                            $(e).parent().next().next().next().next().children().closest(".maxdesc").val(max);
                            if (iva == '1') {
                                $(e).parent().next().next().next().next().next().next().next().children().attr("checked", "checked");
                            } else {
                                $(e).parent().next().next().next().next().next().next().next().children().removeAttr("checked");
                            }

                            //cargarPrecios
                            var tipo = $("#tipo_cliente").val();
                            var selected = "";
                            $.ajax({
                                type: 'post',
                                url: "{{route('precios')}}",
                                headers: {
                                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                                },
                                datatype: 'json',
                                data: {
                                    id: codigo
                                },
                                success: function(data) {
                                    $(e).parent().next().next().children().find('option').remove();
                                    $.each(data, function(key, value) {
                                        if (tipo == value.nivel) {
                                            selected = "selected";
                                        } else {
                                            selected = "";
                                        }
                                        $(e).parent().next().next().children().closest(".pneto").append('<option value=' + value.precio + ' ' + selected + '>' + value.precio + '</option>');
                                    });

                                },
                                error: function(data) {
                                    console.log(data);
                                }
                            });
                        }


                        //cantidad
                        //precio
                        //copago
                        //%descuento
                        //descuento
                        //precioneto
                        $('body').on('blur', '.pneto', function() {
                            // verificar(this);
                            var cant = $(this).parent().prev().prev().prev().prev().prev().children().val();
                            var copago = 0;
                            var descuento = $(this).parent().next().next().next().children().val();
                            var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
                            total = redondeafinal(total);
                            $(this).parent().next().next().next().children().val(total);
                            totales(0);
                        });
                        $('body').on('active', '.pneto', function() {
                            // verificar(this);
                            var cant = $(this).parent().prev().prev().prev().prev().prev().children().val();
                            var copago = 0;
                            var descuento = $(this).parent().next().next().children().val();
                            var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
                            total = redondeafinal(total);
                            $(this).parent().next().next().next().children().val(total);
                            totales(0);
                        });
                        $('body').on('change', '.pneto', function() {
                            // verificar(this);
                            var cant = $(this).parent().prev().children().val();
                            var copago = $(this).parent().next().children().val();
                            var descuento = $(this).parent().next().next().next().children().val();
                            var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
                            total = redondeafinal(total);
                            $(this).parent().next().next().next().next().children().val(total);
                            totales(0);
                        });
                        $('body').on('change', '.cneto', function() {
                            // verificar(this);
                            var cant = $(this).val();
                            var precio = $(this).parent().next().next().next().next().next().next().children().val();
                            // console.log("this", $(this).parent().next().children().val());
                            var copago = 0;
                            //console.log("copago", copago);
                            var descuento = $(this).parent().next().next().next().next().next().next().next().next().children().val();
                            var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
                            total = redondeafinal(total);
                            $(this).parent().next().next().next().next().next().next().next().next().next().children().val(total);

                            totales(0);
                        });

                        function cnx(e) {
                            var cant = $(e).val();
                            var precio = $(e).parent().next().children().val();
                            // console.log("e", $(e).parent().next().children().val());
                            var copago = $(e).parent().next().next().children().val();
                            //console.log("copago", copago);
                            var descuento = $(e).parent().next().next().next().next().children().val();
                            var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
                            total = redondeafinal(total);
                            $(e).parent().next().next().next().next().next().children().val(total);
                        }

                        function redondeafinal(num, decimales = 2) {
                            var signo = (num >= 0 ? 1 : -1);
                            num = num * signo;
                            if (decimales === 0) //con 0 decimales
                                return signo * Math.round(num); // to fixed num 
                            // round(x * 10 ^ decimales)
                            num = num.toString().split('e');
                            num = Math.round(+(num[0] + 'e' + (num[1] ? (+num[1] + decimales) : decimales)));
                            // x * 10 ^ (-decimales)
                            num = num.toString().split('e');
                            return signo * (num[0] + 'e' + (num[1] ? (+num[1] - decimales) : -decimales));
                        }
                        function comprueba_exist(){
                            $('.cneto').each(function (i){
                                var ths= $(this).val();
                                if(isNaN(ths)){
                                    ths=0;
                                }
                            });
                        }

                        $('body').on('change', '.copago', function() {
                            verificar(this);
                            var cant = $(this).parent().prev().prev().children().val();
                            var precio = $(this).parent().prev().children().val();

                            var copago = $(this).val();
                            //console.log("copago", copago);
                            var descuento = $(this).parent().next().next().children().val();
                            var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
                            //console.log(total);
                            total = redondeafinal(total);
                            $(this).parent().next().next().next().children().val(total);

                            totales(0);
                        });


                        $('body').on('change', '.pdesc', function() {

                            var m = $(this).next().val();
                            var cant = $(this).parent().prev().prev().prev().prev().prev().prev().prev().children().val();
                            var precio = $(this).parent().prev().prev().children().val();
                            var pdesc = $(this).val();

                            var descuento = (parseInt(cant) * parseFloat(precio)) * pdesc / 100; //;
                            $(this).parent().next().children().val(descuento.toFixed(2));
                            var copago = $(this).parent().prev().children().val();
                            var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
                            total = redondeafinal(total);
                            $(this).parent().next().next().children().val(total);
                            totales(0);
                        });
                        $('body').on('change', '.desc', function() {
                            var m = verificar(this);
                            var cant = $(this).parent().prev().prev().prev().prev().prev().prev().prev().prev().children().val();
                            var precio = $(this).parent().prev().prev().children().val();
                            /*if(pdesc> m){
                                swal("El descuento no puede ser mayor a "+m+"%");
                                $(this).val(m);
                            }*/
                            var descuento = $(this).val();
                            verificar(this);
                            console.log(cant, precio);
                            var pdesc = 0;
                            if (cant == 0 || precio == 0) {
                                pdesc = 0;
                            } else {
                                pdesc = (descuento * 100) / (parseInt(cant) * parseFloat(precio));
                            }
                            //(parseInt(cant)* parseFloat(precio)) * pdesc /100;//;
                            $(this).parent().prev().children().val(pdesc);
                            var copago = $(this).parent().prev().prev().children().val();
                            var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
                            total = redondeafinal(total);
                            $(this).parent().next().children().val(total);
                            totales(0);
                        });
                        $('body').on('click', '.delete', function() {
                            console.log($(this));

                            $(this).parent().parent().remove();
                            totales(0);
                        });

                        $('body').on('click', '.des', function() {
                            console.log($(this));
                            //alert('eliminando')
                            $(this).parent().parent().parent().parent().hide('slow').remove()
                            totales(0);
                        });

                        function totales(e) {
                            var subt12 = [];
                            var subt0 = [];
                            var descuento = [];
                            var sb12 = 0;
                            var sb0 = 0;
                            var descuentosub0 = 0;
                            var descuentosub12 = 0;
                            var d = 0;
                            var copagoTotal = 0;

                            if (e == 0) {
                                //$(".cneto").change();
                                //console.log("sda");
                                $('.cneto').each(function(i, obj) {
                                    var cant = $(this).val();
                                    $(this).parent().prev().find('.productos').attr('name', 'codigo[]');
                                    $(this).parent().prev().find('.codigo_producto').attr('name', 'nombre[]');

                                    //$(this).attr('name', 'cantidad[]');
                                    //var e = $(this).parent().prev().children().closest(".select2_cuentas");
                                    var precio1 = 0;
                                    var precio2 = 0;
                                    var precio3 = 0;
                                    var precio4 = 0;
                                    var precio5 = 0;
                                    var precioAut = 0;
                                    var tipo = $("#tipo_cliente").val();
                                    //console.log("el e es: ", e.val());
                                    var precio = $(this).parent().next().next().next().next().next().next().children().val();
                                    //$(this).parent().next().children().attr('name', 'precio[]');
                                    var copago = 0;
                                    //$(this).parent().next().next().children().attr('name', 'copago[]');
                                    var descuento = $(this).parent().next().next().next().next().next().next().next().children().val();
                                    //$(this).parent().next().next().next().next().children().attr('name', 'descuento[]');
                                    d = parseFloat(d) + parseFloat(descuento);
                                    var iva = $(this).parent().next().next().next().next().next().next().next().next().next().next().children().prop('checked');
                                    //console.log(iva);
                                    precio = precio != null ? precio : 0;
                                    var total = (parseInt(cant) * parseFloat(precio)) - parseFloat(0) - parseFloat(copago);
                                    //console.log("precio y cantidad" + total);

                                    if (iva == 1) {
                                        subt12.push(total);
                                        sb12 = sb12 + total;
                                        descuentosub12 += parseFloat(descuento);

                                    } else {
                                        subt0.push(total);
                                        sb0 = sb0 + total;
                                        descuentosub0 += parseFloat(descuento);
                                    }
                                    copagoTotal = parseFloat(copagoTotal) + parseFloat(copago);
                                    //aqui falta
                                    //console.log("subtotal12" + sb12);
                                    $("#subtotal_12").html(sb12.toFixed(2));
                                    $("#subtotal_0").html(sb0.toFixed(2));
                                    $("#descuento").html(d.toFixed(2));
                                    var descuento_total = descuentosub12 + descuentosub0;
                                    var sum = sb12 + sb0 - descuento_total;
                                    $("#base").html(sum.toFixed(2));
                                    var iva = 0.12;
                                    var ti = iva * sb12;
                                    if (d > 0) {
                                        if (sb12 > 0) {
                                            ti = iva * (sb12 - descuentosub12);
                                        }

                                    }
                                    ti = redondeafinal(ti);
                                    $("#tarifa_iva").html(ti.toFixed(2, 2));
                                    var t = sb12 + sb0 + ti - d;
                                    //console.log(t);
                                    var totax = sum + ti;
                                    totax = redondeafinal(totax);
                                    copagoTotal = redondeafinal(copagoTotal);
                                    sb12 = redondeafinal(sb12);
                                    sb0 = redondeafinal(sb0);
                                    d = redondeafinal(d);

                                    $("#total").html(totax.toFixed(2, 2));
                                    $("#copagoTotal").html(copagoTotal.toFixed(2, 2));
                                    $("#subtotal_121").val(sb12);
                                    $("#subtotal_01").val(sb0);
                                    $("#descuento1").val(d);
                                    $("#tarifa_iva1").val(ti);
                                    $("#total1").val(totax);
                                    $("#totalc").val(copagoTotal);

                                });

                            }
                        }

                        function isNumberKey(evt) {
                            var charCode = (evt.which) ? evt.which : event.keyCode
                            if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
                                return false;

                            return true;
                        }

                        function nuevo(id, data, patient) {
                            var nuevafila = $("#mifila").html();
                            var rowk = document.getElementById("entrega" + id).insertRow(-1);
                            //$('#mifila tr:last').before("<tr class='well'>"+nuevafila+"</tr>")
                            var input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = "id_orden[]";
                            input.value = data;
                            rowk.innerHTML = fila;
                            rowk.append(input);
                            var input2 = document.createElement('input');
                            input2.type = 'hidden';
                            input2.name = "paciente[]";
                            input2.value = patient;
                            rowk.append(input);
                            rowk.className = "well";
                            $('.select2').select2({
                                tags: false
                            });
                        }
                        /*$(".anadir").click(function(){
                            alert("dsadada");
                        });*/
                        $(".btn_add").click(function() {

                            if ($("#frm").valid()) {
                                //$(".print").css('visibility', 'visible');
                                /* $(".btn_add").attr("disabled", true);*/
                                $('.btn_add').prop('disabled','disabled');
                                $.ajax({
                                    type: "POST",
                                    url: "{{route('ingreso.store_new')}}",
                                    headers: {
                                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                                    },
                                    data: $("#frm").serialize(),
                                    datatype: "json",
                                    success: function(data) {
                                        //console.log(data);
                                       alert("Guardado correctamente")
                                      

                                    },
                                    error: function(data) {
                                        alert(data);
                                    }
                                });
                            } else {
                                alert("Existen campos vacios");
                            }

                        });
        </script>
</section>
@endsection