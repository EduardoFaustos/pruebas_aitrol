@extends('contable.compras_pedidos.base')
@section('action-content')

<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.CompraPedido')}}</a></li>
            <li class="breadcrumb-item"><a href="../compras">{{trans('contableM.RegistroDeCompraPedido')}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nueva Factura de Compra Pedido</li>
        </ol>
    </nav>
    <form class="form-vertical " id="formulario" role="form" method="post" action="{{ route('contable.pedidos_update.create',['id'=>$compraPedido->id]) }}">
        {{ csrf_field() }}
        <div class="box box-solid">
            <div class="box-header header_new">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-6">
                            <div class="box-title"><b>{{trans('contableM.REGISTRODEPEDIDODECOMPRA')}}</b></div>
                        </div>
                        <div class="col-6" style="text-align: center;">
                            <div class="row">
                                <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="javascript:goBack()">
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                                </a>
                                @if($compraPedido->estado ==1)
                                <a class="btn btn-success btn-gray " style="margin-left: 3px;" onclick="editar()">
                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;&nbsp;Editar
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-body dobra">
            <div class="header row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-2 col-xs-2 px-1">
                            <label class=" label_header">{{trans('contableM.id')}}</label>
                            <div class="input-group">
                                <input id="proveedor" type="text" disabled class="form-control" id="proveedor" name="proveedor" value="{{$compraPedido->id}}" onchange="cambiar_proveedor()">
                                <div class="input-group-addon ">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('proveedor').value = ''; cambiar_proveedor()"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-3 px-1">
                            <label class=" label_header">{{trans('contableM.proveedor')}}</label>
                            <select class="form-control select2_cuentas" style="width: 100%;" onchange="cambiar_nombre_proveedor()" name="nombre_proveedor" id="nombre_proveedor">
                                <option value="">Seleccione...</option>
                                @foreach($proveedor as $value)
                                <option {{ $value->id == $compraPedido->proveedor ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 col-xs-4 px-1">
                            <label class=" label_header">{{trans('contableM.direccion')}}</label>
                            <div class="input-group">
                                <input id="direccion_proveedor" type="text" value="{{$compraPedido->direccion_proveedor}}" class="form-control" name="direccion_proveedor">
                                <div class="input-group-addon ">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('direccion_proveedor').value = ''; cambiar_nombre_proveedor()"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-3 px-1">
                            <label class=" label_header">{{trans('contableM.autorizacion')}}</label>
                            <div class="input-group">
                                <input id="autorizacion" type="text" class="form-control  " name="autorizacion" value="{{$compraPedido->autorizacion}}">
                                <div class="input-group-addon ">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('autorizacion').value = '';"></i>
                                </div>
                            </div>

                        </div>
                       
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="row  ">
                        @if(!is_null($iva_param))
                        <input type="text" name="ivareal" id="ivareal" class="hidden" value="{{$iva_param->iva}}">
                        @endif

                        <div class="col-md-3 col-xs-3 px-1">
                            <label class=" label_header">{{trans('contableM.fechafacturacion')}}</label>
                            <div class="input-group col-md-12">
                                <input id="f_autorizacion" type="date" class="form-control   col-md-12" name="f_autorizacion" value="{{$compraPedido->fecha}}">
                            </div>
                        </div>
                        <!-- <div class="col-md-2 col-xs-2 px-1">
                            <label class=" label_header">{{trans('contableM.serie')}}</label>
                            <div class="input-group">
                                <input id="serie" maxlength="25" value="$compraPedido->serie" type="text" class="form-control  " name="serie" onkeyup="agregar_serie()">
                                <div class="input-group-addon ">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('serie').value = '';"></i>
                                </div>
                            </div>
                        </div> -->

                          <!--*********************Nueno cambio************-->
                            <div class="form-group col-xs-8  col-md-3  px-1">
                                <div class="col-md-12 px-0">
                                    <label for="sucursal" class="label_header">{{trans('contableM.sucursal')}}</label>
                                </div>
                                <div class="col-md-12 px-0">

                                    <select class="form-control" name="sucursal" id="sucursal" onchange="obtener_caja()"  required>
                                        <option value="">Seleccione...</option>
                                        @foreach ($sucursales as $value)
                                        <option {{ $value->codigo_sucursal == $compraPedido->sucursal ? 'selected' : ''}} value="{{ $value->id }}">{{ $value->codigo_sucursal }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-xs-7  col-md-3  px-1">
                                <div class="col-md-12 px-0">
                                    <label for="punto_emision" class="label_header">{{trans('contableM.pemision')}}</label>
                                    <input type="hidden" id="electronica" name="electronica" value="0">
                                </div>
                                <div class="col-md-12 px-0">
                                    <select class="form-control" name="punto_emision" id="punto_emision" required>
                                        <option value="">Seleccione...</option>

                                    </select>
                                </div>
                            </div>

                            <!--********************Fin del nuevo cambio************-->

                        <div class="col-md-3 col-xs-3 px-1">
                            <label class=" label_header">{{trans('contableM.secuenciafactura')}}</label>
                            <div class="input-group">
                                <input id="secuencia_factura" maxlength="30" value="{{$compraPedido->secuencia_factura}}" type="text" class="form-control  " name="secuencia_factura" onchange="ingresar_cero()">
                                <div class="input-group-addon ">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('secuencia_factura').value = '';"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 px-1">
                            <label class=" label_header">{{trans('contableM.concepto')}}</label>
                            <input autocomplete="off" type="text" value="{{$compraPedido->observacion}}" class="form-control col-md-12" name="observacion" id="observacion">
                        </div>
                        <input type="hidden" name="id_empresa" id="id_empresa" value="{{$empresa->id}}">
                    </div>
                </div>
                <div id="output">
                </div>
            </div>
            <div class="col-md-12 table-responsive" style="width: 100%;">
                <input type="hidden" name="contador" id="contador" value="0">
                <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                        <tr>
                            <!--<th width="10%" class="" tabindex="0">{{trans('contableM.codigo')}}</th>-->
                            <th width="35%" class="" tabindex="0">{{trans('contableM.DescripciondelProducto')}}</th>
                            <th width="10%" class="" tabindex="0">{{trans('contableM.cantidad')}}</th>
                            <th width="10%" class="" tabindex="0">{{trans('contableM.precio')}}</th>
                            <th width="10%" class="" tabindex="0">% {{trans('contableM.prctdesc')}}</th>
                            <th width="10%" class="" tabindex="0">{{trans('contableM.descuento')}}</th>
                            <th width="10%" class="" tabindex="0">{{trans('contableM.precioneto')}}</th>
                            <th width="5%" class="" tabindex="0">{{trans('contableM.iva')}}</th>
                        </tr>
                    </thead>
                    <tbody id="agregar_cuentas">
                        @foreach($compraPedido->detalles as $x)
                        <tr class="wello">
                            <td style="max-width:100px;">
                                <Input type="hidden" name="codigo[]" class="codigo_producto" />
                                <select name="nombre[]" class="form-control select2_cuentas" style="width:100%" disabled onchange="verificar(this)">
                                    <option> </option>
                                    @foreach($productos as $value)
                                    <option @if($x->codigo==$value->codigo) selected="selected" @endif value="{{$value->nombre}}">{{$value->codigo}} | {{$value->nombre}}</option>
                                    @endforeach
                                </select>
                                <textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto">{{$x->detalle}}</textarea>
                                <input type="hidden" name="iva[]" class="iva" />
                            </td>
                            <td>
                                <input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="{{$x->cantidad}}" name="cantidad[]" required>
                                <select name="bodega[]" class="form-control select2_bodega bodega" style="width: 80%;margin-top: 5px;">
                                    <option> </option>
                                    @foreach($bodega as $value)
                                    @if(!is_null($value))
                                    <option @if($value->id==$x->bodega) selected="selected" @endif value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </td>
                            <td id="tprecio" style="max-width:100px;">

                                <input type="text" disabled class="form-control pneto" style="width: 80%;height:20px;" name="precio[]" value="{{$x->precio}}" readonly>
                            </td>
                            <td>
                                <input class="form-control text-right pdesc"  type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="{{$x->descuento_porcentaje}}" name="descpor[]" readonly>
                            </td>
                            <td>
                                <input class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="{{$x->descuento}}" name="desc[]" readonly>
                            </td>
                            <td>
                                <input class="form-control px-1 text-right" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="{{$x->extendido}}" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" readonly>
                            </td>
                            <td>
                                <input class="form" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]" @if($x->iva==1) checked @endif disabled>

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class=''>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>

                            <td colspan="2" class="text-right">{{trans('contableM.subtotal12')}}%</td>
                            <td id="subtotal_12" class="text-right px-1">@if(!is_null($compraPedido)){{$compraPedido->subtotal_12}}@endif</td>
                            <input type="hidden" name="subtotal_121" id="subtotal_121" class="hidden">
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>

                            <td colspan="2" class="text-right">{{trans('contableM.subtotal0')}}%</td>
                            <td id="subtotal_0" class="text-right px-1">@if(!is_null($compraPedido)){{$compraPedido->subtotal_0}}@endif</td>
                            <input type="hidden" name="subtotal_01" id="subtotal_01" class="hidden">
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>

                            <td colspan="2" class="text-right">{{trans('contableM.descuento')}}</td>
                            <td id="descuento" class="text-right px-1">@if(!is_null($compraPedido)){{$compraPedido->descuento}}@endif</td>
                            <input type="hidden" name="descuento1" id="descuento1" class="hidden">
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" class="text-right">{{trans('contableM.subtotal')}}</td>
                            <td id="base" class="text-right px-1">@if(!is_null($compraPedido)){{$compraPedido->subtotal}}@endif</td>

                            <input type="hidden" name="base1" id="base1" class="hidden">
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>

                            <td colspan="2" class="text-right">{{trans('contableM.tarifaiva')}}</td>
                            <td id="tarifa_iva" class="text-right px-1">@if(!is_null($compraPedido)){{$compraPedido->iva_total}}@endif</td>
                            <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" class="hidden">
                        </tr>
                        <!--<tr>
                            <td></td><td></td><td></td><td></td><td></td>
                            <td colspan="2" class="text-right">Transporte</td>
                            <td id="transporte" class="text-right px-1">0.00</td>
                            <input type="hidden" name="transporte1" id="transporte1" class="hidden">
                        </tr>-->
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" class="text-right"><strong>{{trans('contableM.total')}}</strong></td>
                            <td id="total" class="text-right px-1">@if(!is_null($compraPedido)){{$compraPedido->total_final}}@endif</td>
                            <input type="hidden" name="total1" id="total1" class="hidden">
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>

                            <td colspan="2" class="text-right"></td>
                            <td id="copagoTotal" class="text-right px-1"></td>
                            <input type="hidden" name="totalc" id="totalc" class="hidden">
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </form>
    <script type="text/javascript">
        $('.select2_cuentas').select2({
            tags: false
        });
        
        window.onload = function() {
            obtener_caja();
        };

        // const serieConcat = () => {
        //     let punto_emision = document.getElementById(serie);
        //     punto_emision = punto_emision.options[punto_emision.selectedIndex].text;

        // }

        function goBack() {
            window.history.back();
        }

        function editar() {
            document.getElementById("formulario").submit();
        }

        function ingresar_cero() {
            var secuencia_factura = $('#secuencia_factura').val();
            var digitos = 9;
            var ceros = 0;
            var varos = '0';
            var secuencia = 0;
            if (secuencia_factura > 0) {
                var longitud = parseInt(secuencia_factura.length);
                if (longitud > 10) {
                    swal("Error!", "Valor no permitido", "error");
                    $('#secuencia_factura').val('');

                } else {
                    var concadenate = parseInt(digitos - longitud);
                    switch (longitud) {
                        case 1:
                            secuencia = '000000000';
                            break;
                        case 2:
                            secuencia = '00000000';
                            break;
                        case 3:
                            secuencia = '0000000';
                            break;
                        case 4:
                            secuencia = '000000';
                            break;
                        case 5:
                            secuencia = '00000';
                            break;
                        case 6:
                            secuencia = '0000';
                            break;
                        case 7:
                            secuencia = '000';
                            break;
                        case 8:
                            secuencia = '00';
                            break;
                        case 9:
                            secuencia = '0';
                    }
                    $('#secuencia_factura').val(secuencia + secuencia_factura);
                }


            } else {
                swal("Error!", "Valor no permitido", "error");
                $('#secuencia_factura').val('');
            }
        }

        const obtener_caja = () => {

        var id_sucursal = $("#sucursal").val();

            $.ajax({
                type: 'post',
                url: "{{ route('caja.sucursal') }}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'id_sucur': id_sucursal
                },
                success: function(data) {
                    

                    if (data.value != 'no') {
                        if (id_sucursal != 0) {
                            $("#punto_emision").empty();

                            $.each(data, function(key, registro) {
                                $("#punto_emision").append(`<option @if($compraPedido->punto_emision == '${registro.codigo_caja}') selected @endif value="${registro.id}">
                                                                ${registro.codigo_sucursal}-${registro.codigo_caja}
                                                            </option>`);

                            });
                        } else {
                            $("#punto_emision").empty();

                        }

                    }
                },
                error: function(data) {

                }
            })

        }


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
                        $('#proveedor').val(data.value);
                        $('#direccion_proveedor').val(data.direccion);
                       // $('#serie').val(data.serie);
                        $('#f_caducidad').val(data.caducidad);
                        $('#autorizacion').val(data.autorizacion);

                    } else {
                        $('#proveedor').val("");
                        $('#direccion_proveedor').val("");
                      //  $('#serie').val("");
                        $('#f_caducidad').val("");
                        $('#autorizacion').val("");
                    }

                },
                error: function(data) {
                    console.log(data);
                }
            });
        }
        $("#proveedor").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "{{route('compra_identificacion')}}",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 1,
        });

        function cambiar_proveedor() {
            $.ajax({
                type: 'post',
                url: "{{route('compra_buscar_proveedor')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'codigo': $("#proveedor").val()
                },
                success: function(data) {
                    console.log(data);
                    if (data.value != "no") {
                        $('#nombre_proveedor').val(data.value);
                        $('#direccion_proveedor').val(data.direccion);
                    //    $('#serie').val(data.serie);
                        $('#autorizacion').val(data.autorizacion);
                    } else {
                        $('#nombre_proveedor').val(" ");
                        $('#direccion_proveedor').val("");
                      //  $('#serie').val("");
                        $('#autorizacion').val("");
                    }
                },
                error: function(data) {
                    // console.log(data);
                }
            })
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

        function agregar_serie() {
            var serie = $('#serie').val();
            if ((serie.length) == 3) {
                $('#serie').val(serie + '-');
            } else if ((serie.length) > 7) {
                $('#serie').val('');
                swal("Error!", `{{trans('proforma.seriecorrectamente')}}`, "error");
            }
        }
    </script>
    @endsection