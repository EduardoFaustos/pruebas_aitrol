@extends('contable.compras_pedidos.base')
@section('action-content')
<style type="text/css">
    /**************************************************************************************************************************************************************** */

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
        min-width: 460px;
        _width: 460px !important;
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

    .select2-search__field {
        text-transform: uppercase;
    }

    .select2-results__option {
        text-transform: uppercase;
    }

    .my_scroll_div {
        overflow-y: auto;
        max-height: 600px;
    }

    .block {
        display: block;
    }

    .none {
        display: none;
    }


    /* CSS CARGANDO */
    .loader-wrapper {
        width: 220px;
        height: 220px;
    }

    .swal2-container {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: horizontal;
        -webkit-box-direction: normal;
        -ms-flex-direction: row;
        flex-direction: row;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        padding: 10px;
        background-color: transparent;
        z-index: 1060;
    }

    .loader {
        box-sizing: border-box;
        width: 100%;
        height: 100%;
        border: 34px solid #162534;
        border-top-color: #4bc8eb;
        border-bottom-color: #f13a8f;
        border-radius: 50%;
        animation: rotate 5s linear infinite;
    }

    .loader-inner {
        border-top-color: #36f372;
        border-bottom-color: #fff;
        animation-duration: 2.5s;
    }

    @keyframes rotate {
        0% {
            transform: scale(1) rotate(360deg);
        }

        50% {
            transform: scale(.8) rotate(-360deg);
        }

        100% {
            transform: scale(1) rotate(360deg);
        }
    }

    .cargando {
        text-align: center;
        color: white;
        font-size: 38px;
        text-transform: uppercase;
        font-weight: 700;
        font-family: inherit;
    }

    /* FIN CSS CARGANDO */
</style>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=PT+Sans&display=swap" rel="stylesheet">
<style>
    .content {
        font-family: 'PT Sans', sans-serif !important;
    }

    .table-responsive .form-control {
        border-radius: 5px !important;
        /* padding: 5px; */
    }
</style>

<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.COMPRA')}}</a></li>
            <li class="breadcrumb-item"><a href="{{route('importaciones.index')}}">Registro Importaciones</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nueva Pedido de Importación</li>
        </ol>
    </nav>
    <!-- ANIMACION DE CARGANDO-->
    <div id="cargando" class="swal2-container swal2-center swal2-backdrop-show" style="overflow-y: auto; display:none;">
        <div id="cargando_hijo" class="loader-wrapper">
            <div class="loader">
                <div class="loader loader-inner"></div>
            </div>
            <div class="cargando">{{trans('contableM.guardando')}}...</div>
        </div>
    </div>
    <!-- FIN DE ANIMACION DE CARGANDO-->
    <form class="form-vertical " id="crear_factura" role="form" method="post">
        {{ csrf_field() }}
        <div class="box box-solid">
            <div class="box-header header_new">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-9 col-sm-9 col-6">

                            <div class="box-title"><b>{{trans('contableM.PedidosCompras')}}</b></div>
                        </div>
                        <div class="col-3" style="text-align:center">
                            <div class="row">
                                <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="{{route('importaciones.index')}}">
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--**********************************Incio de cambio de ordenes***********************************-->
            @if(Auth::user()->id == "0957258056")
            <div class="box-body dobra">
                <div class="header row">
                    <input type="hidden" id="id_estado" name="id_estado">
                    <div class="col-md-12 col-xs-12 px-1">
                        <label class="">Buscador De Orden Importacion</label>
                        <br>
                        <select id="selecOrden" name="pre_orden" style="width:50%!important" class="form-control select2_cuentas select2-hidden-accessible" onchange="selectOrden()">
                            <option value="">Seleccione...</option>
                            @foreach($ordenes as $orden)
                            @if(isset($orden->importacion))
                            <option value="{{$orden->id}}">{{$orden->importacion->observacion}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
                <!--**********************************Fin del cambio de ordenes***********************************-->

                <input type="hidden" name="modulo" value="importacion">
                <div class="box-body dobra">
                    <div class="header row">
                        <div class="col-md-12">
                            <div class="row">
                                <!--*********************Nueno cambio************-->
                                <div class="form-group col-xs-7  col-md-2 px-1">
                                    <div class="col-md-12 px-0">
                                        <label for="sucursal" class="label_header">{{trans('contableM.sucursal')}} ({{trans('contableM.empresa')}})</label>
                                    </div>
                                    <div class="col-md-12 px-0">
                                        <select class="form-control validar" name="sucursal" id="sucursal" onchange="obtener_caja();" required>
                                            <option value="">Seleccione...</option>
                                            @foreach ($sucursales as $value)
                                            <option value="{{ $value->id }}">{{ $value->codigo_sucursal }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-xs-6  col-md-2  px-1">
                                    <div class="col-md-12 px-0">
                                        <label for="punto_emision" class="label_header">{{trans('contableM.pemision')}} ({{trans('contableM.empresa')}})</label>
                                        <input type="hidden" id="electronica" name="electronica" value="0">
                                    </div>
                                    <div class="col-md-12 px-0">
                                        <select class="form-control validar" name="punto_emision" id="punto_emision" required>
                                            <option value="">Seleccione...</option>

                                        </select>
                                    </div>
                                </div>

                                <!--********************Fin del nuevo cambio************-->
                                <div class="col-md-3 col-xs-3 px-1">
                                    <label class=" label_header">{{trans('contableM.proveedor')}}</label>
                                    <select class="form-control select2_proveedor validar" style="width: 100%;" onchange="llenarCampo(); buscarDireccion();" name="proveedor" id="proveedor">
                                        <option value="">Seleccione...</option>

                                    </select>
                                </div>

                                <div class="col-md-3 col-xs-3 px-1">
                                    <label class=" label_header">{{trans('contableM.direccion')}}</label>
                                    <div class="input-group">
                                        <input id="direccion_proveedor" type="text" class="form-control validar" name="direccion_proveedor">
                                        <div class="input-group-addon ">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('direccion_proveedor').value = '';"></i>
                                        </div>
                                    </div>
                                </div>




                                <div class="col-md-2 col-xs-2 px-1">
                                    <label class=" label_header">{{trans('contableM.fechapedido')}}</label>
                                    <div class="input-group col-md-12">
                                        <input id="f_autorizacion" type="date" class="form-control   col-md-12" name="f_autorizacion" value="@php echo date('Y-m-d');@endphp">
                                    </div>
                                </div>
                                <div id="content"></div>
                                <div class="col-md-2 col-xs-2 px-1">
                                    <label class=" label_header">{{trans('contableM.pais')}}</label>
                                    <select onchange="paisProcedencia();guardarpais();" class="form-control select2_pais validar" style="width: 100%;" name="pais" id="pais">


                                    </select>
                                </div>

                                <div class="col-md-2 col-xs-2 px-1">
                                    <label class=" label_header">{{trans('contableM.paisprocedencia')}}</label>
                                    <div class="input-group">
                                        <input id="pais_procedencia" type="text" class="form-control validar" name="pais_procedencia">
                                        <div class="input-group-addon ">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('pais_procedencia').value = '';"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-xs-2 px-1">
                                    <label class=" label_header">{{trans('contableM.serie')}}</label>
                                    <div class="input-group">
                                        <input id="serie" maxlength="25" type="text" class="form-control validar " name="serie" onkeyup="agregar_serie(); llenarCampo();">
                                        <div class="input-group-addon ">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('serie').value = '';"></i>
                                        </div>
                                    </div>
                                </div>




                                <div class="col-md-3 col-xs-3 px-1">
                                    <label class=" label_header">{{trans('contableM.secuencia')}}</label>
                                    <div class="input-group">
                                        <input id="secuencia_factura" maxlength="30" type="text" class="form-control validar  " name="secuencia_factura" onchange="ingresar_cero('secuencia_factura',10); llenarCampo();">
                                        <div class="input-group-addon ">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('secuencia_factura').value = '';"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 col-xs-3 px-1">
                                    <label class=" label_header">Secuencia Importación</label>
                                    <div class="input-group">
                                        <input id="secuencia_importacion" maxlength="30" type="text" class="form-control validar" name="secuencia_importacion">
                                        <div class="input-group-addon ">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('secuencia_importacion').value = '';"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2 col-xs-2 px-1">
                                    <label class=" label_header">{{trans('contableM.asiento')}}</label>
                                    <div class="input-group">
                                        <input id="id_asiento" disabled maxlength="30" type="text" class="form-control" name="id_asiento">
                                        <div class="input-group-addon ">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('id_asiento').value = '';"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-10 px-1">
                                    <label class=" label_header">{{trans('contableM.concepto')}}</label>
                                    <input autocomplete="off" type="text" class="form-control col-md-12 validar" name="observacion_2" id="observacion_2">
                                </div>
                                <input type="hidden" name="id_empresa" id="id_empresa" value="{{$empresa->id}}">
                                <input type="hidden" name="sucursal_final" id="sucursal_final">
                            </div>

                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row  ">
                            <input type="text" name="ivareal" id="ivareal" class="hidden" value="0.12">



                        </div>
                        <div id="output">
                        </div>

                    </div>
                    <div class="col-md-12 table-responsive" style="width: 100%;">
                        <input type="hidden" name="contador" id="contador" value="0">
                        <input name='contador_items' id='contador_items' type='hidden' value="1">
                        <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                            <thead>
                                <tr>
                                    <!--<th width="10%" class="" tabindex="0">{{trans('contableM.codigo')}}</th>-->
                                    <th width="35%" class="" tabindex="0">Descripción del Producto</th>
                                    <th width="10%" class="" tabindex="0">{{trans('contableM.cantidad')}}</th>
                                    <th width="10%" class="" tabindex="0">{{trans('contableM.precio')}}</th>
                                    <th width="10%" class="" tabindex="0">{{trans('contableM.peso')}} (Kg)</th>
                                    <th width="10%" class="" tabindex="0">% {{trans('contableM.prctdesc')}}</th>
                                    <th width="10%" class="" tabindex="0">{{trans('contableM.descuento')}}</th>
                                    <!-- <th width="10%" class="" tabindex="0">Precio Desc.</th> -->
                                    <th width="10%" class="" tabindex="0">{{trans('contableM.precioneto')}}</th>
                                    <th width="10%" class="" tabindex="0">AF</th>
                                    <th width="10%" class="" tabindex="0"></th>
                                    <th width="10%" class="" tabindex="0">
                                        <button onclick="crearFila(0, {})" type="button" class="btn btn-success btn-gray">
                                            <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" onclick="crearTransporte()" class="btn btn-primary btn-sm">
                                            <i class="fa fa-truck" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="agregar_cuentas">
                                <!-- Se crean  -->
                            </tbody>
                            <tfoot class=''>
                                <!-- <tr>
                                <td colspan="7"></td>
                                <td colspan="2" class="text-right">{{trans('contableM.subtotal12')}}%</td>
                                <td id="subtotal_12" class="text-right px-1">0.00</td>
                                <input type="hidden" name="subtotal_121" id="subtotal_121" class="hidden">
                            </tr> -->
                                <tr>
                                    <td colspan="7"></td>
                                    <td colspan="2" class="text-right">{{trans('contableM.subtotal0')}}%</td>
                                    <td id="subtotal_0" class="text-right px-1">0.00</td>
                                    <input type="hidden" name="subtotal_01" id="subtotal_01" class="hidden">
                                </tr>
                                <tr>
                                    <td colspan="7"></td>
                                    <td colspan="2" class="text-right">{{trans('contableM.descuento')}}</td>
                                    <td id="descuento" class="text-right px-1">0.00</td>
                                    <input type="hidden" name="descuento1" id="descuento1" class="hidden">
                                </tr>
                                <tr>
                                    <td colspan="7"></td>
                                    <td colspan="2" class="text-right">{{trans('contableM.subtotal')}}</td>
                                    <td id="base" class="text-right px-1">0.00</td>

                                    <input type="hidden" name="base1" id="base1" class="hidden">
                                </tr>
                                <!-- <tr>
                                <td colspan="7"></td>
                                <td colspan="2" class="text-right">{{trans('contableM.tarifaiva')}}</td>
                                <td id="tarifa_iva" class="text-right px-1">0.00</td>
                                <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" class="hidden">
                            </tr> -->
                                <!--<tr>
                                <td></td><td></td><td></td><td></td><td></td>
                                <td colspan="2" class="text-right">Transporte</td>
                                <td id="transporte" class="text-right px-1">0.00</td>
                                <input type="hidden" name="transporte1" id="transporte1" class="hidden">
                            </tr>-->
                                <tr>
                                    <td colspan="7"></td>
                                    <td colspan="2" class="text-right"><strong>{{trans('contableM.total')}}</strong></td>
                                    <td id="total" class="text-right px-1">0.00</td>
                                    <input type="hidden" name="total1" id="total1" class="hidden">
                                </tr>
                                <tr>
                                    <td colspan="7"></td>
                                    <td colspan="2" class="text-right"></td>
                                    <td id="copagoTotal" class="text-right px-1"></td>
                                    <input type="hidden" name="totalc" id="totalc" class="hidden">
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col-md-12" style="text-align: center;">
                        <button class="btn btn-success btn-gray" onclick="guardarImportacion(event)" id="boton_guardar"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.guardar')}}
                        </button>
                    </div>

                </div>
            </div>
            <div class="modal fade bs-example-modal-lg " id="modal_datos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content my_scroll_div" id="datos_activo">

                    </div>
                </div>
            </div>
    </form>
</section>
@if(Auth::user()->id == "0957258056" or Auth::user()->id == "0953905999")
@include('activosfijos.documentos.factura.mdactivo2')
@else
@include('activosfijos.documentos.factura.mdactivo')
@endif


<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>s

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const selectOrden = () => {
        let id_orden = document.getElementById("selecOrden").value;

        $.ajax({
            type: "get",
            url: `{{ route('importaciones.preOrdenMostrar') }}`,
            datatype: 'json',
            data: {
                id: id_orden
            },
            success: function(data) {
            //    console.log(data);
                llenarInputs(data.cab);
            },
            error: function(data) {

            }
        })

    }

    const llenarInputs = (data) => {
        document.getElementById("serie").value = data.serie;
        document.getElementById("secuencia_factura").value = data.secuencia;
        document.getElementById("secuencia_importacion").value = data.secuencia_importacion;
        document.getElementById("pais_procedencia").value = data.pais_procedencia;
        document.getElementById("observacion_2").value = data.observacion;

        $('#proveedor').empty();
        $('#proveedor').append(`<option selected value="${data.id_proveedor}" >${data.nombre_proveedor}</option>`);
        $("#proveedor").change();

        $('#pais').empty();
        $('#pais').append(`<option selected value="${data.id_pais}" >${data.nombre_pais}</option>`);

        $('#agregar_cuentas').empty();
        document.getElementById('contador_items').value = 1;

        $('#datos_activo').empty();

        document.getElementById('subtotal_0').innerHTML = data.subtotal
        document.getElementById('subtotal_01').value = data.subtotal

        document.getElementById('descuento').innerHTML = data.descuento == null ? '0.00' : data.descuento
        document.getElementById('descuento1').value =  data.descuento == null ? '0.00' : data.descuento

        document.getElementById('base').innerHTML = data.total
        document.getElementById('base1').value = data.total

        document.getElementById('total').innerHTML = data.total
        document.getElementById('total1').value = data.total


        const details = data.detalles.map(detalle => {
            crearFila(1, detalle);
        });
    }


    $('#modal_datos').on('hidden.bs.modal', function() {
        $(this).removeData('bs.modal');
    });


    $(function() {
        $("form").keypress(function(e) {
            var key;
            if (window.event)
                key = window.event.keyCode; //IE
            else
                key = e.which; //firefox     
            return (key != 13);
        });
    });

    // $('.select2_pais').select2({
    //     tags: true
    // });

    $('.select2_pais').select2({
        tags: true,
        placeholder: "Seleccione Pais...",
        allowClear: true,
        cache: true,
        ajax: {
            url: '{{route("importaciones.pais")}}',
            data: function(params) {
                var query = {
                    search: params.term,
                    type: 'public'
                }
                return query;
            },
            processResults: function(data) {
                return {
                    results: data
                };
            }
        }
    });

    $('.select2_proveedor').select2({
        placeholder: "Seleccione...",
        allowClear: true,
        ajax: {
            url: '{{route("importaciones.proveedores")}}',
            data: function(params) {
                var query = {
                    search: params.term,
                    type: 'public'
                }
                return query;
            },
            processResults: function(data) {
                return {
                    results: data
                };
            }
        }
    });

    function mensaje(data) {
        setTimeout(function() {
            for (x of data) {}
        }, 200)
    }

    const cargando = accion => {
        let divCargando = document.getElementById("cargando"); //padre
        // if (accion == 1) {
        //     let content = `<div id="cargando_hijo" class="loader-wrapper">
        //                         <div class="loader">
        //                             <div class="loader loader-inner"></div>
        //                         </div>
        //                         <div class="cargando">{{trans('contableM.guardando')}}...</div>
        //                     </div>`;


        //     divCargando.classList.add("swal2-container", "swal2-center", "swal2-backdrop-show")

        //     $('#cargando').append(content);
        // } else {
        //     let hijo = document.getElementById("cargando_hijo");
        //     divCargando.removeChild(hijo)
        //     divCargando.classList.remove("swal2-container", "swal2-center", "swal2-backdrop-show")
        // }

        if (accion == 1) {
            divCargando.style.display = "flex";
        } else {
            divCargando.style.display = "none";
        }
    }


    const crearFila = (req = 0, data = {}) => {
       // console.log(data);
        var id = document.getElementById('contador_items').value;
        let fila = `
                <tr >
                                <td style="max-width:100px;">
                                    <Input id="codigo${id}" type="hidden" name="codigo[]" class="codigo_producto " />
                                    <select id="producto${id}" name="producto[]" class="form-control select2_productos" style="width:100%" required onchange="verificar(this);">
                                        
                                    </select>
                                    <textarea rows="3" name="descrip_prod[]" id="descrip_prod${id}" class="form-control px-1 desc_producto " placeholder="Detalle del producto"></textarea>
                                    <input type="hidden" name="iva[]" class="iva" />
                                    <input type="hidden" rows="3" name="observacion[]" id="observacion${id}" class="form-control px-1 " placeholder="Observacion">
                                </td>
                                <td>
                                    <input onchange="totalProducto(${id})" id="cantidad${id}" class="form-control text-right cneto validar" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0); if(isNaN(this.value)){this.value=0}" value="${req === 1 ? data.cantidad : 0}" name="cantidad[]" required>
                                    <select id="bodega${id}" name="bodega[]" class="form-control select2_bodega bodega validar" style="width: 80%;margin-top: 5px;" required>
                                        <option> </option>
                                        @foreach($bodega as $value)
                                            <option @if($value->tipo == 2) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input onblur="this.value=parseFloat(this.value).toFixed(2); if(isNaN(this.value)){this.value=(0).toFixed(2)}" onchange="totalProducto(${id})" id="precio${id}" value="${req === 1 ? data.precio : 0.00}" type="text" class="pneto form-control validar" name="precio[]" style="width: 80%;height:20px;" placeholder="0.00">
                                </td>
                                <td>
                                    <input onchange="totalProducto(${id})" id="peso${id}" value="${req === 1 ? data.peso : 0.00}" type="text" class="pneto form-control" name="peso[]" style="width: 80%;height:20px;" placeholder="0.00">
                                </td>
                                <td>
                                    <input onchange="totalProducto(${id})" id="descpor${id}" class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2); if(isNaN(this.value)){this.value=(0).toFixed(2)}" value="${req === 1 ? data.porct_descuento : '0.00'}" name="descpor[]" required>
                                    <input onchange="totalProducto(${id})" id="maxdesc${id}" class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" required>
                                </td>
                                <td>
                                    <input onchange="valorDescuento(${id})" id="desc${id}" class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2); if(isNaN(this.value)){this.value=(0).toFixed(2)}" value="0" name="desc[]" required >
                                </td>
                                <!-- <td>
                                     <input onchange="totalProducto(${id})" id="precio_des${id}" class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="precio_desc[]" required readonly>
                                </td>-->
                                <td>
                                    <input onchange="totalProducto(${id})" id="precioneto${id}" class="form-control px-1 text-right precioneto validar" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="${req === 1 ? data.precio_neto : '0.00'}" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" required readonly>
                                </td>
                                <td>
                                    <input id="af${id}" type="checkbox" name="af[]" style="width: 80%;height:20px;" onchange="muestra_boton(${id})" ${req === 1 ? data.af === 1 ? 'checked' : '' : ''}>
                                    <input id="check_af${id}" type="hidden" name="check_af[]" value="${req === 1 ? data.af : '0'}">
                                </td>
                                <td>
                                    <button style="display: ${req === 1 ? data.af === 1 ? 'block' : 'none' : 'none'};" type="button" id="btn_ac${id}" class="btn btn-xs btn-info" onclick="modal_activo(${id}, event);  cargarDatos(${id})"> <i class="glyphicon glyphicon-edit" ></i></button>
                                </td>
                                <td>
                                    <button onclick="eliminarModalUni(${id})" type="button" class="btn btn-danger delete">
                                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                    </button>
                                </td>
                </tr>
        `

        $('#agregar_cuentas').append(fila);
        $('.select2_productos').select2({
            tags: false
        });
        $('.select2_productos').select2({
            placeholder: "Seleccione un producto...",
            allowClear: true,
            minimumInputLength: 3,
            cache: true,
            ajax: {
                url: '{{route("importaciones.productos")}}',
                data: function(params) {
                    var query = {
                        search: params.term,
                        type: 'public'
                    }
                    return query;
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                }
            }
        });
        if (req === 1) {
            if (data.af === 1) {
                $("#datos_activo").append(modal_crear(id, req, data.activo_fijo));
                let modal_datos_a = document.getElementById("modal_datos_a" + id);
                modal_datos_a.classList.remove("block");
                modal_datos_a.classList.add("none");
                console.log("Activo fijo", data.activo_fijo);

                //console.log(document.getElementById('mdtipo'+id))

                $(`#mdtipo${id} option[value="${data.activo_fijo.tipo_id}"]`).attr("selected", true);
                $(`#mdtipo${id}`).change();
                $(`#mdmarca${id} option[value="${data.activo_fijo.marca}"]`).attr("selected", true);
                $(`#mdcolor${id} option[value="${data.activo_fijo.color}"]`).attr("selected", true);
                $(`#mdserie${id} option[value="${data.activo_fijo.serie}"]`).attr("selected", true);
                $(`#mdresponsable${id} option[value="${data.activo_fijo.responsable}"]`).attr("selected", true);
                $(`#mdubicacion${id} option[value="${data.activo_fijo.ubicacion}"]`).attr("selected", true);

                setTimeout(() => {
                    $(`#mdgrupo${id} option[value="${data.activo_fijo.subtipo_id}"]`).attr("selected", true);
                }, 1000);


                if (data.activo_fijo.accesorios.length > 0) {
                    const accesorio = data.activo_fijo.accesorios.map(access => {
                        agregar_items(id, req, access);

                    });
                }

            }
            $('#producto' + id).empty();
            $('#producto' + id).append(`<option selected value="${data.id_producto}" >${data.nombre_producto}</option>`);
            $("#producto" + id).change();
            $(`#bodega${id} option[value="${data.id_bodega}"]`).attr("selected", true);

        }
        id++;
        document.getElementById('contador_items').value = id;
    }



    const buscarDireccion = () => {
        let proveedor = document.getElementById('proveedor').value;
        $.ajax({
            type: 'get',
            url: "{{route('importaciones.direccion')}}",
            datatype: 'json',
            data: {
                'proveedor': proveedor
            },
            success: function(data) {
                if (data.status == "success") {
                    document.getElementById('direccion_proveedor').value = data.direccion;
                }
            },
            error: function(data) {

            }
        })
    }

    function muestra_boton(id) {
        var but = document.getElementById("btn_ac" + id);
        var check = document.getElementById("af" + id);
        var check_af = document.getElementById("check_af" + id);

        if (check.checked) {
            but.style.display = 'block';
            check_af.value = 1;
        } else {
            but.style.display = 'none';
            check_af.value = 0;
        }
    }

    function verifica_af(id) {
        var check = document.getElementById("af" + id);
        var md_nombre = document.getElementById("mdnombre" + id);

        if (md_nombre != null) {
            cambia_nombre(id);
        }
    }

    const cargarDatos = (id) => {
        setTimeout(function() {
            let producto = document.getElementById("producto" + id);

            let nombre = document.getElementById("mdnombre" + id);
            let descripcion = document.getElementById("mddescripcion" + id);

            if (producto.value > 0) {
                let select = producto.options[producto.selectedIndex].text;


                let nombre_separado = select.split('|');

                nombre.value = nombre_separado[1].trim();
                nombre.readOnly = true;
                descripcion.value = nombre_separado[1].trim();
                descripcion.readOnly = true;
            } else {
                nombre.value = "Seleccione un producto";
                nombre.readOnly = true;
                descripcion.value = "Seleccione un producto";
                descripcion.readOnly = true;
            }

        }, 500)
    }

    function cambia_nombre(id) {

        var nombre_prod = document.getElementById('producto' + id);
        var nom = nombre_prod.dataset.name;
        document.getElementById('mdnombre' + id).value = nom;
        document.getElementById('mddescripcion' + id).value = nom;


    }

    function totalProducto(id) {
        let cantidad = document.getElementById("cantidad" + id).value;
        let precio = document.getElementById("precio" + id).value;
        let descpor = document.getElementById("descpor" + id).value;
        let desc = document.getElementById("desc" + id)
        let precioneto = document.getElementById("precioneto" + id)


        let prec_desc = precio - ((descpor / 100) * precio);
        let preXcantidad = (precio * cantidad)
        let porcDescuento = (descpor / 100) * preXcantidad;
        let precioTotal = (preXcantidad) - porcDescuento;
        desc.value = porcDescuento.toFixed(2)
        precioneto.value = precioTotal.toFixed(2);

        totalGlobal();

    }

    function valorDescuento(id) {
        let precio = document.getElementById("precio" + id).value;
        let cantidad = document.getElementById("cantidad" + id).value
        let porcDescuento = document.getElementById("descpor" + id)
        let desc = document.getElementById("desc" + id).value

        let precioXcantidad = precio * cantidad;

        let descuento = (desc * 100) / precioXcantidad;

        porcDescuento.value = descuento.toFixed(2);

        totalProducto(id);

    }



    function totalGlobal() {
        let precioneto = document.querySelectorAll(".precioneto");
        let desc = document.querySelectorAll(".desc");
        let total = 0;
        let descuento = 0;
        for (let i = 0; i < precioneto.length; i++) {
            total += parseFloat(precioneto[i].value)
            descuento += parseFloat(desc[i].value)
        }

        document.getElementById('subtotal_0').innerHTML = parseFloat(total).toFixed(2);
        document.getElementById('subtotal_01').value = parseFloat(total).toFixed(2);

        document.getElementById('base').innerHTML = parseFloat(total).toFixed(2);
        document.getElementById('base1').value = parseFloat(total).toFixed(2);

        document.getElementById('total').innerHTML = parseFloat(total).toFixed(2);
        document.getElementById('total1').value = parseFloat(total).toFixed(2);

        document.getElementById('descuento').innerHTML = parseFloat(descuento).toFixed(2);
        document.getElementById('descuento1').value = parseFloat(descuento).toFixed(2);

    }

    function paisProcedencia() {
        let pais = document.getElementById('pais')
        let pais_nombre = pais.options[pais.selectedIndex].text

        let pais_procedencia = document.getElementById('pais_procedencia');
        pais_procedencia.value = pais_nombre + " - "
    }

    function guardarpais() {
        let guardpais = document.getElementById('pais').value;

        $.ajax({
            type: 'get',
            url: "{{route('importaciones.store_pais')}}",
            // headers: {
            //     'X-CSRF-TOKEN': $('input[name=_token]').val()
            // },
            datatype: 'json',
            data: {
                'pais': guardpais
            },
            success: function(data) {

            },
            error: function(data) {

            }
        })
    }

    function guardarImportacion(e) {
        cargando(1);
        e.preventDefault();
        var formulario = document.forms["crear_factura"];

        let btn_guardar = document.getElementById('boton_guardar');
        btn_guardar.style.display = 'none'
        //$('#boton_guardar').prop("disabled", true);
        if (!validar_campos()) {
            var formulario = document.forms["crear_factura"];
            var observacion = formulario.observacion_2.value;

            var proveedor = formulario.proveedor.value;
            var pais = document.getElementById('pais').value;
            var secuencia = formulario.secuencia_factura.value;
            // var serie = formulario.serie.value;
            var msj = "";
            if (observacion == "") {
                msj += "Por favor, Llene el campo observación<br/>";
            }
            if (secuencia == "") {
                msj += "Por favor, Llene el campo de secuencia<br/>";
            }
            if (pais == "") {
                msj += "Por favor, Seleccione un pais<br/>";
            }
            // if (serie == "") {
            //     msj += "Por favor, Llene la serie de la factura<br/>";
            // }
            if (msj != "") {
                cargando(0);
                alertas('error', 'Error!..', msj)
                btn_guardar.style.display = 'initial'
                //  $('#boton_guardar').prop("disabled", false);
            } else {
                if (!validarProducto()) {
                    $.ajax({
                        type: 'post',
                        url: "{{route('importaciones.store')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('input[name=_token]').val()
                        },
                        datatype: 'json',
                        data: $('#crear_factura').serialize(),
                        success: function(data) {


                            $('#content').fadeIn(1000).html(data);
                            cargando(0);
                            alertas(data.respuesta, `${data.respuesta.toUpperCase()}...`, data.msj)
                            if (data.respuesta == "success") {
                                document.getElementById('id_asiento').value = data.id_asiento;
                                setTimeout(function() {
                                    window.location.href = "{{route('importaciones.index')}}";
                                }, 1500)
                            } else {
                                // $('#boton_guardar').prop("disabled", false);
                                btn_guardar.style.display = 'initial'
                            }

                        },
                        error: function(data) {
                            //$('#boton_guardar').prop("disabled", false);
                            cargando(0);
                            btn_guardar.style.display = 'initial'
                        }
                    });
                }
            }
        } else {
            cargando(0);
            $('#boton_guardar').prop("disabled", false);
            btn_guardar.style.display = 'initial'
            alertas('error', 'ERROR', `{{trans('proforma.camposvacios')}}`)
        }
    }

    const validarProducto = () => {
        let msj = "";
        let validate = false;
        let selectProductos = document.querySelectorAll(".select2_productos");
        let btn_guardar = document.getElementById('boton_guardar');
        if (selectProductos.length <= 0) {
            cargando(0);
            alertas('error', 'Error!..', "Debe tener un producto")
            $('#boton_guardar').prop("disabled", false);
            btn_guardar.style.display = 'initial'
            validate = true;
        } else {
            for (let i = 0; i < selectProductos.length; i++) {
                let td = selectProductos[i].parentElement;
                if (selectProductos[i].value == "") {
                    cargando(0);
                    td.children[2].style.border = '1px solid red';
                    msj = "No ha seleccionado producto";
                    alertas('error', 'Error!..', msj)
                    $('#boton_guardar').prop("disabled", false);
                    btn_guardar.style.display = 'initial'
                    validate = true;
                }
            }
        }

        return validate;
    }

    function guardar_color(id) {

        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            url: "{{url('activosfijos/documentofactura/guardar_color')}}/" + id,
            data: $("#crear_factura").serialize(),
            datatype: 'json',
            success: function(data) {

            },
            error: function(data) {

                //alert(data)
            }
        });

    }

    function guardar_marca(id) {

        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            url: "{{url('activosfijos/documentofactura/guardar_marca')}}/" + id,
            data: $("#crear_factura").serialize(),
            datatype: 'json',
            success: function(data) {

                //alert(data)
            },
            error: function(data) {

                //alert(data)
            }
        });
    }

    function guardar_serie(id) {

        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            url: "{{url('activosfijos/documentofactura/guardar_serie')}}/" + id,
            data: $("#crear_factura").serialize(),
            datatype: 'json',
            success: function(data) {

                //alert(data)
            },
            error: function(data) {

                //alert(data)
            }
        });
    }

    function guardar_responsable(id) {
        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            url: "{{url('activosfijos/documentofactura/guardar_responsable')}}/" + id,
            data: $("#crear_factura").serialize(),
            datatype: 'json',
            success: function(data) {

                //alert(data)
            },
            error: function(data) {

                //alert(data)
            }
        });
    }

    function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
    }

    const crearTransporte = () => {
        var id = document.getElementById('contador_items').value;

        let fila = `
                            <tr >
                                <input id="check_af${id}" type="hidden" name="check_af[]" value="0"> 
                                <td style="max-width:100px;">
                                    <Input id="codigo${id}" type="hidden" name="codigo[]" class="codigo_producto" value="TRANS"/>
                                    <input type="hidden" id="producto${id}" name="producto[]" class="form-control" value="367"/>
                                    <input id="nombre_producto${id}" name="transporte" class="form-control"  style="width: 94%;height: 20px;" readonly value="TRANSPORTE"/>
                                    <textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto " placeholder="Detalle del producto"></textarea>
                                </td>
                                <td>
                                    <input onchange="totalProducto(${id})" id="cantidad${id}" class="form-control text-right cneto validar" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="cantidad[]" required>
                                    <select  id="bodega${id}" name="bodega[]" class="form-control select2_bodega bodega" style="display:none;">
                                        <option value=""> </option>
                                        @foreach($bodega as $value)
                                            <option value="{{$value->id}}">{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input onblur="this.value=parseFloat(this.value).toFixed(2);" onchange="totalProducto(${id})" id="precio${id}" value="0.00" type="text" class="pneto form-control validar" name="precio[]" style="width: 80%;height:20px;" placeholder="0.00">
                                </td>
                                <td>
                                    <input readonly onchange="totalProducto(${id})" id="peso${id}" value="0.00" type="text" class="pneto form-control" name="peso[]" style="width: 80%;height:20px;" placeholder="0.00">
                                </td>
                                <td>
                                    <input onchange="totalProducto(${id})" id="descpor${id}" class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor[]" required>
                                    <input onchange="totalProducto(${id})" id="maxdesc${id}" class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" required>
                                </td>
                                <td>
                                    <input onchange="valorDescuento(${id})" id="desc${id}" class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc[]" required>
                                </td>
                                <!--td>
                                    <input onchange="totalProducto(${id})" id="precio_des${id}" class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="precio_desc[]" required>
                                </td-->
                                
                                <td>
                                    
                                    <input onchange="totalProducto(${id})" id="precioneto${id}" class="form-control px-1 text-right precioneto validar" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" required>
                                </td>
                                <!--<td>
                                    <input readonly id="porcentaje${id}" style="height:20px;" class="form-control px-1 text-right" type="text" name="porcentaje[]">
                                </td>-->
                                <td>
                                    
                                </td>
                                <td> </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-gray delete">
                                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>`

        $('#agregar_cuentas').append(fila);
        $('.select2_cuentas').select2({
            tags: false
        });

        id++;
        document.getElementById('contador_items').value = id;
    }

    function validar_campos() {
        let campo = document.querySelectorAll(".validar")
        let validar = false;

        for (let i = 0; i < campo.length; i++) {

            if (campo[i].value.trim() <= 0) {
                campo[i].style.border = '2px solid #CD6155';
                campo[i].style.borderRadius = '4px';
                validar = true;
            } else {
                campo[i].style.border = '1px solid #d2d6de';
                campo[i].style.borderRadius = '0px';
            }
        }
        return validar;
    }

    function obtener_caja() {

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
                            $("#punto_emision").append('<option value=' + registro.id + '>' +
                                registro.codigo_sucursal + '-' + registro.codigo_caja +
                                '</option>');

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

    const llenarCampo = () => {
        let proveedor = document.getElementById("proveedor");

        //let serie = document.getElementById("punto_emision");
        let serie = document.getElementById("serie").value;

        //let serie_option = serie;

        //serie.value != '' ? serie = serie.options[serie.selectedIndex].text : '';

        let secuencia = document.getElementById("secuencia_factura").value;
        let value = '';

        proveedor.value != '' ? value = proveedor.options[proveedor.selectedIndex].text : '';

        if (value != '') {
            document.getElementById("observacion_2").value = `IMPORTACION # ${proveedor.value != ''? value : ''} ${serie != ''? serie : ''}-${secuencia}`
            //document.getElementById("observacion").value = 'FACT-COMPRA' + ' ' + value + ' ' + serie + ' ' + secuencia;
        }
        //llenarSerie();
    }

    const llenarSerie = () => {
        let serie = document.getElementById("serie");
        let punto_emision = document.getElementById("punto_emision");
        punto_emision = punto_emision.options[punto_emision.selectedIndex].text;

    }
</script>

<script type="text/javascript">
    var fila = $("#mifila").html();
    $(document).ready(function() {

        limpiar();

        crearFila(0, {});

        //$('#myform')[0].reset(); PARA LIMPIAR TODOS LOS INPUTS DENTRO DEL FORM
        $('.select2_cuentas').select2({
            tags: false
        });


        // $('.select2_pais').select2({
        //     tags: true
        // });


        $('.iva').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            increaseArea: '20%' // optional
        });

        $('.ice').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            increaseArea: '20%' // optional
        });

    });
    $('.smr').on('ifChecked', function(event) {
        $("#archivosri").val(1);
    });
    $('.smr').on('ifUnchecked', function(event) {
        $("#archivosri").val(0);
    });

    function verificar(e) {
        var iva = $('option:selected', e).data("iva");
        var codigo = $('option:selected', e).data("codigo");
        var usadescuento = $('option:selected', e).data("descuento");
        var max = $('option:selected', e).data("maxdesc");
        var modPrecio = $('option:selected', e).data("precio");

        $(e).parent().children().closest(".codigo_producto").val(codigo);
        $(e).parent().children().closest(".iva").val(iva);

        /*
        if (modPrecio) {
            //$(e).parent().next().next().closest(".cp");
            
            $(e).parent().next().next().children().find(".cp").removeAttr("disabled");
        } else {
            
            $(e).parent().next().next().children().find(".cp").attr("disabled", "disabled");
        }
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
        */

        if (iva == '1') {
            $(e).parent().next().next().next().next().next().next().children().attr("checked", "checked");
        } else {
            $(e).parent().next().next().next().next().next().next().children().removeAttr("checked");
        }
    }

    function redondeafinal(num, decimales = 2) {
        var signo = (num >= 0 ? 1 : -1);
        num = num * signo;

        if (decimales === 0) //con 0 decimales
            return signo * Math.round(num);
        // round(x * 10 ^ decimales)
        num = num.toString().split('e');
        num = Math.round(+(num[0] + 'e' + (num[1] ? (+num[1] + decimales) : decimales)));
        // x * 10 ^ (-decimales)
        num = num.toString().split('e');
        return signo * (num[0] + 'e' + (num[1] ? (+num[1] - decimales) : -decimales));
    }

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;

        return true;
    }


    $('body').on('click', '.delete', function() {


        $(this).parent().parent().remove();
        //totales(0);
    });
    $('body').on('click', '.form', function() {


        //$(this).parent().parent().remove();
        totales(0);
    });
    //cantidad
    //precio
    //copago
    //%descuento
    //descuento
    //precioneto



    //new change to round be
    //eduardo me dijo que quemara esas bodegas en totbodega
    function nuevo() {
        var nuevafila = $("#mifila").html();
        var rowk = document.getElementById("agregar_cuentas").insertRow(-1);
        //$('#mifila tr:last').before("<tr class='well'>"+nuevafila+"</tr>")
        rowk.innerHTML = fila;
        //rowk.className="well";
        $('.select2_cuentas').select2({
            tags: false
        });
    }



    function limpiar() {
        $("#datos_tarjeta_credito").hide();
        $("#datos_tarjeta_debito").hide();
        $("#datos_cheque").hide();
        $("#valor_tarjetadebito").val('');
        $("#valor_cheque").val('');
        $("#valor_efectivo").val('');
        $("#valor_tarjetacredito").val('');
        $("#retenciones").val('0.00');
        $("#tabla_rubros").hide();
    }

    function eliminar_registro(valor) {
        var nombre2 = "visibilidad" + valor;
        var dato1 = "dato" + valor;
        $("#visibilidad" + valor).val(0);
        //document.getElementById(nombre2).value = 0;
        document.getElementById(dato1).style.display = 'none';
        suma_totales();
    }

    function crear_compra(e) {
        var valor_total = $('#total_final1').val();
        var val_efe = parseInt($("#valor_efectivo").val());
        var val_che = parseInt($("#valor_cheque").val());
        var val_tarj_cred = parseInt($("#valor_tarjetacredito").val());
        var val_tarj_debi = parseInt($("#valor_tarjetadebito").val());
        var val_cal = verificatotales();
        var formulario = document.forms["crear_factura"];
        var observacion = formulario.observacion.value;
        var proveedor = formulario.proveedor.value;
        var secuencia = formulario.secuencia_factura.value;
        var serie = formulario.serie.value;
        var totaliva = $("#tarifa_iva1").val();
        var msj = "";
        if (observacion == "") {
            msj += "Por favor, Llene el campo observación<br/>";
        }
        if (secuencia == "") {
            msj += "Por favor, Llene el campo de secuencia<br/>";
        }
        if (serie == "") {
            msj += "Por favor, Llene la serie de la factura<br/>";
        }
        if (msj == "") {
            if ($("#crear_factura").valid()) {
                $(e).hide();
                $.ajax({
                    type: 'post',
                    url: "{{route('contable.compra_pedido_store.create')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: $('#crear_factura').serialize(),
                    success: function(data) {

                        swal("Exito!", 'Guardado con Exito', "success");
                        setTimeout(function() {

                            location.href = "{{route('contable.compraspedidos.index')}}";
                        }, 1000);


                    },
                    error: function(data) {

                        swal("Error!", data, "error");
                    }
                });
            }

        } else {
            swal({
                title: "Error!",
                type: "error",
                html: msj
            });
        }

    }

    function goBack() {
        window.history.back();
    }

    function verificatotales() {
        var valor_totalfinal = parseFloat($("#total_final1").val());
        if (isNaN(valor_totalfinal)) {
            valor_totalfinal = 0;
        }
        var valor_credito = parseFloat($("#valor_tarjetacredito").val());
        if (isNaN(valor_credito)) {
            valor_credito = 0;
        }
        var valor_efectivo = parseFloat($("#valor_efectivo").val());
        if (isNaN(valor_efectivo)) {
            valor_efectivo = 0;
        }
        var valor_cheque = parseFloat($("#valor_cheque").val());
        if (isNaN(valor_cheque)) {
            valor_cheque = 0;
        }
        var valor_debito = parseFloat($("#valor_tarjetadebito").val());
        if (isNaN(valor_debito)) {
            valor_debito = 0;
        }
        var totales = valor_credito + valor_efectivo + valor_cheque + valor_debito;
        if ((totales) != NaN) {
            if (totales == valor_totalfinal) {
                return 'ok';
            } else {
                return false;
            }
        }

    }

    function ivas(id) {
        $("#ivaver" + id).val(1);

    }

    function validarcantidad(elemento, id) {

        var cod = $('#codigo' + id).val();
        var nomb = $('#nombre' + id).val()
        if ((cod.length == 0) && (nomb.length == 0)) {
            swal("Debe ingresar el código", "Nombre del producto", "error");
            $('#cantidad' + id).val("0");
            $('#desc' + id).val("0");
            $('#extendido' + id).val("0");
            return false;
        }
        var num = elemento.value;
        if (num.length == 0) {
            swal("Cantidad no Permitida", "Por favor ingrese de nuevo", "error");
            $('#cantidad' + id).val("0");
            $('#desc' + id).val("0");
            $('#extendido' + id).val("0");
            suma_totales();
            return false;
        }


        var numero = parseInt(elemento.value, 10);
        if (numero < -1 || numero > 999999999) {
            swal("Cantidad no Permitida", "Por favor ingrese de nuevo", "error");
            $('#cantidad' + id).val("0");
            return false;
        }
        return true;
    }

    function validartotal(elemento, id) {
        var numero = parseInt(elemento.value, 10);
        //Validamos que se cumpla el rango
        if (numero < -1 || numero > 999999999) {
            swal("Total no Permitido");
            $('#total' + id).val("0");
            return false;
        }
        return true;
    }

    function validarprecio(elemento, id) {

        var cod = $('#codigo' + id).val();
        var nomb = $('#nombre' + id).val()

        if ((cod.length == 0) && (nomb.length == 0)) {
            swal("Error!", "Debe ingresar el código, nombre, cantidad del producto", "error");
            $('#precio' + id).val("0");
            $('#desc' + id).val("0");
            $('#extendido' + id).val("0");
            return false;
        }

        var prec = elemento.value;
        if (prec.length == 0) {

            swal("¡Error!", "Ingrese de nuevo", "error");
            $('#precio' + id).val("0");
            $('#desc' + id).val("0");
            $('#extendido' + id).val("0");
            total_calculo(id);
            //suma_totales();
            return false;
        }

        var numero = parseInt(elemento.value, 10);
        //Validamos que se cumpla el rango
        if (numero < -1 || numero > 999999999) {
            swal("Precio no Permitido");
            $('#precio' + id).val("0");
            return false;
        }
        return true;
    }

    function validardescuento(elemento, id) {
        var numero = parseInt(elemento.value, 10);
        //Validamos que se cumpla el rango
        if (numero < 0 || numero > 100) {
            swal("Rango de descuento permitido (0% - 100%)", "Por favor ingrese de nuevo", "error");
            $('#desc_porcentaje' + id).val("0");
            return false;
        }
        return true;
    }


    function validartransporte(elemento) {
        var numero = parseInt(elemento.value, 10);
        //Validamos que se cumpla el rango
        if (numero < -1 || numero > 999999999) {
            swal("Transporte no Permitido", "Por favor ingrese de nuevo", "error");
            $('#transporte').val("0");
            return false;
        }
        return true;
    }
    $('input[name=numero_debito]').change(function() {
        swal(this);
    });

    function redondea_precio(elemento, id, nDec) {

        var n = parseFloat(elemento.value);
        var s;
        var d = elemento.value;
        var en = String(d);
        arr = en.split("."); // declaro el array
        entero = arr[0];
        decimal = arr[1];
        //alert(decimal);
        if (decimal != null) {
            if ((decimal.length) > 2) {
                n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
                s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
                s = s.substr(0, s.indexOf(".") + nDec + 1);
                $('#precio' + id).val(s);
            }
        } else {
            $('#precio' + id).val(n.toFixed(2, 2));
        }
    }

    function redondea_cantidad(elemento, id, nDec) {

        var n = parseFloat(elemento.value);
        var s;
        var d = elemento.value;
        var en = String(d);
        arr = en.split("."); // declaro el array
        entero = arr[0];
        decimal = arr[1];
        n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
        s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
        s = s.substr(0, s.indexOf(".") + nDec + 1);
        if (decimal != null) {
            if ((decimal.length) > 2) {
                n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
                s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
                s = s.substr(0, s.indexOf(".") + nDec + 1);
                $('#cantidad' + id).val(s);
            } else {
                $('#cantidad' + id).val(n.toFixed(2, 2));
            }
        }
        $('#cantidad' + id).val(s);
    }

    function redondea_descuento(elemento, id, nDec) {
        var n = parseFloat(elemento.value);
        var s;
        var d = elemento.value;
        var en = String(d);
        arr = en.split("."); // declaro el array
        entero = arr[0];
        decimal = arr[1];
        n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
        s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
        s = s.substr(0, s.indexOf(".") + nDec + 1);
        if (decimal != null) {
            if ((decimal.length) > 2) {
                n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
                s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
                s = s.substr(0, s.indexOf(".") + nDec + 1);
                $('#desc_porcentaje' + id).val(s);
            } else {
                $('#desc_porcentaje' + id).val(n.toFixed(2, 2));
            }
        } else {
            $('#desc_porcentaje' + id).val(s);
        }

    }

    function validar_retenciones(elemento) {
        var numero = parseInt(elemento.value, 10);
        var total_final = parseFloat($('#total_final').val());
        //Validamos que se cumpla el rango
        if (numero < 0 || numero > 999999999) {
            swal("Valor de Retención no Permitido", "Por favor ingrese de nuevo", "error");
            $('#retenciones').val("0");
            return false;
        }
        if (numero > total_final) {
            swal("No puedes ingresar el monto superior al valor de la factura", "Por favor ingrese de nuevo", "error");
            $('#retenciones').val("0");
            return false;
        }
        return true;
    }


    function total_calculo(id) {

        total = 0;
        descuento_total = 0;
        dec = 0;
        cantidad = parseInt($("#cantidad" + id).val());
        precio = parseFloat($("#precio" + id).val());
        descuento = parseFloat($("#desc_porcentaje" + id).val());

        //alert(descuento);
        total = cantidad * precio;
        descuento_total = (total * descuento) / 100;
        $('#desc_' + id).val(descuento_total.toFixed(2));
        dec = parseFloat($("#desc_" + id).val());
        //alert(descuento_total);
        $('#desc' + id).val(descuento_total.toFixed(2));
        $('#extendido' + id).val(total.toFixed(2));
        $('#extendido_' + id).val(total.toFixed(2));

        suma_totales();
    }

    function suma_totales() {

        contador = 0;
        iva1 = 0;
        iva = 0;
        total = 0;
        sub = 0;
        descu1 = 0;
        total_fin = 0;
        descu = 0;
        trans = 0;
        subtotal_0 = 0;
        subtotal_12 = 0;
        base_imponible = 0;
        var iva_par = $('#iva_par').val();
        //var iva_par = $('#iva_par').val();
        var prop = "";
        $("#det_recibido tr").each(function() {
            //$(this).find('td')[0];

            cantidad = parseInt($(this).find('#cantidad' + contador).val());
            valor = parseFloat($(this).find('#precio' + contador).val());
            descu = parseFloat($(this).find('#desc_' + contador).val());
            pre_neto = parseFloat($(this).find('#extendido_' + contador).val());
            visibilidad = parseInt($('#visibilidad' + contador).val());
            total = pre_neto;


            if (visibilidad == 1) {
                if ($('#iva' + contador).prop('checked')) {
                    var iva_par = 0.12;
                    var cod = $('#codigo' + contador).val();
                    var nomb = $('#nombre' + contador).val();
                    if ((cod.length == 0) && (nomb.length == 0)) {
                        swal("Error!", "Debe ingresar el código, nombre del Producto o Servicio", "error");
                        $('#iva' + contador).prop('checked', false);
                    }
                    if (total > 0) {
                        sub = sub + total;
                    }
                    //iva = sub * 0.12;

                    iva1 = pre_neto * iva_par;
                    iva = iva + iva1;

                } else {

                    prop = +"no entra en el iva";

                    if (total > 0) {
                        sub = sub + total;
                    }
                }

                /*if(iva == 1){
                    sub = sub + total;
                    iva = sub * 0.12;
                }*/
                //sub = sub + iva;
                if (descu > 0) {
                    descu1 = descu1 + descu;
                }




            }
            contador = contador + 1;

        });






        //iva = (sub- descu) * iva_par;

        base_imponible = sub;

        //iva = sub * iva_par;
        //iva = subtotal_12 * iva_par;

        trans = parseInt($('#transporte').val());
        //total_fin = (sub - descu1)+trans+iva;


        //iva = (sub - descu1) * iva_par;




        //total_fin = (sub - descu1)+trans+iva;


        //total_fin = (sub - descu1);
        //iva = sub * 0.12;
        //total = sub + iva;

        /*if(iva == 1){
            subtotal_12 = subtotal_12 + total;
        }else{
            subtotal_0 = subtotal_0 + total;
        }*/

        if (trans > 0) {
            total_fin = sub + trans + iva;
        } else {
            total_fin = sub + iva;
        }
        if (descu1 > 0) {
            total_fin = total_fin - descu1;
        }

        $('#iva_final').val(iva.toFixed(2, 2));


        $('#subtotal_final').val(sub.toFixed(2, 2));


        $('#descuento').val(descu1.toFixed(2, 2));


        $('#total_final').val(total_fin.toFixed(2, 2));


        $('#base_imponible').val(base_imponible);


        //Campos Oculto
        $('#subtotal1').val(sub);
        $("#total_b").html(total_fin.toFixed(2, 2));
        $("#subtotal_b").html(sub.toFixed(2, 2));
        $("#iva_b").html(iva.toFixed(2, 2));
        $("#descuento_b").html(descu1.toFixed(2, 2));
        $('#descuento1').val(descu1);
        $('#total1').val(total_fin);
        $('#base_imponible1').val(base_imponible);
        $('#transporte1').val(trans);
        //$('#total1').val(total_fin);

        //evaluar(); 401


    }

    function suma_totales2() {

        contador = 0;
        iva = 0;
        total = 0;
        sub = 0;
        descu1 = 0;
        total_fin = 0;
        descu = 0;
        cantidad = 0;

        $("#det_recibido tr").each(function() {
            $(this).find('td')[0];
            visibilidad = $(this).find('#visibilidad' + contador).val();
            if (visibilidad == 1) {
                cantidad = parseFloat($(this).find('#cantidad' + contador).val());
                valor = parseFloat($(this).find('#precio' + contador).val());
                descu = parseFloat($(this).find('#desc' + contador).val());
                pre_neto = parseFloat($(this).find('#extendido1' + contador).val());
                total = cantidad * valor;
                if ($('#iva' + contador).prop('checked')) {
                    $('#ivaver' + contador).val(1);
                    var iva_par = $('#iva_par').val();
                    var cod = $('#codigo' + contador).val();
                    var nomb = $('#nombre' + contador).val();
                    if ((cod.length == 0) && (nomb.length == 0)) {
                        swal("Error!", "Debe ingresar el código, nombre del Producto o Servicio", "error");
                        $('#iva' + contador).prop('checked', false);
                    }
                    if (total > 0) {
                        sub = sub + total;
                    }

                    if (descu > 0) {
                        iva1 = (pre_neto - descu) * (0.12);

                        iva = iva + iva1;
                    } else {
                        iva1 = pre_neto * 0.12;
                        iva = iva + iva1;
                    }

                } else {
                    if (total > 0) {
                        sub = sub + total;

                    }
                }
                if (descu > 0) {
                    descu1 = descu1 + descu;

                }
                //aask to change discount to values
                // discount values with level headers
                // why i dont use values the function laravl view
                //



            }
            //contador = contador + 1;
        });
        var ivaf = parseFloat($("#iva_par").val());

        var dsiva = sub;
        if (iva > 0) {
            dsiva = sub * ivaf;
        }
        if (descu1 > 0) {
            if (iva > 0) {
                dsiva = (sub - descu1) * ivaf;

            } else {
                dsiva = (sub - descu1);

            }

        }
        subt = sub;

        trans = parseFloat($('#transporte').val());
        total_fin = parseFloat((sub - descu1) + dsiva);
        if (descu1 > 0) {
            subt = sub - descu1;
        }
        if (!isNaN(sub)) {
            $('#subtotal_final').val(sub.toFixed(2));
        }
        if (!isNaN(descu1)) {
            $('#descuento').val(descu1.toFixed(2));
        }
        if (!isNaN(dsiva)) {
            $('#iva_final').val(dsiva.toFixed(2));
        }
        if (!isNaN(total_fin)) {
            $('#total_final').val(total_fin.toFixed(2));
        }
        $('#subtotal_final1').val(sub.toFixed(2));
        if (descu1 == NaN) {
            decu1 = 0;
        }
        $('#descuento1').val(descu1.toFixed(2));
        $('#iva_final1').val(dsiva.toFixed(2));
        $('#total_final1').val(total_fin.toFixed(2));


    }


    function cambiar_nombre(id) {
        $.ajax({
            type: 'post',
            url: "{{route('compra_nombre2')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'nombre': $("#nombre" + id).val()
            },
            success: function(data) {
                if (data.value != "no") {
                    $('#codigo' + id).val(data.value);
                    if (data.iva == '1') {
                        $('#ivaver' + id).val(1);
                        document.getElementById('iva' + id).checked = true;
                        document.getElementById('iva' + id).disabled = true;
                    }
                } else {
                    $('#codigo' + id).val(" ");
                }
            },
            error: function(data) {

            }
        })
    }



    function validar_cheque() {
        var valor_efectivo = parseInt($("#valor_cheque").val());
        var valor_totalfinal = parseInt($("#total_final1").val());
        var valor_efec = $("#valor_cheque").val();
        var validacion = calculartotales();

        if (valor_efectivo > valor_totalfinal) {

            swal("El valor ingresado no puede ser mayor al valor Total", "Por favor ingrese de nuevo", "error");
            $('#valor_cheque').val('');
            $("#datos_cheque").hide('slow');
        }
        if (isNaN(valor_efectivo)) {
            $("#datos_cheque").hide();
        }

        if ((valor_efectivo > 0) && (valor_efectivo <= valor_totalfinal)) {
            if (validacion == 'ok') {
                $("#datos_cheque").show();
                //alert(validacion);
            } else {
                swal("Error!", "No cumple la suma con el total de la factura", "error");
                //alert(validacion);
                $('#valor_cheque').val('');
            }

        }
        if ((valor_efectivo < 0)) {
            swal("El valor ingresado es incorrecto");
            $('#valor_cheque').val('');
            $("#datos_cheque").hide('slow');
        }
    }

    function validar_efectivo() {
        var valor_efectivo = parseInt($("#valor_efectivo").val());
        var valor_totalfinal = parseInt($("#total_final1").val());
        var validacion = calculartotales();
        if (validacion == 'ok') {
            if ((valor_efectivo > 0) && (valor_totalfinal > 0)) {

                if (valor_efectivo > valor_totalfinal) {

                    swal("Error!", "El valor ingresado no puede ser mayor al valor Total", "error");

                    $('#valor_efectivo').val('');

                }
            }
        } else {
            swal("Error!", "No cumple la suma con el total de la factura", "error");
            $('#valor_efectivo').val('');
        }
    }

    function validar_tarj_credito() {
        var valor_efectivo = parseInt($("#valor_tarjetacredito").val());
        var valor_totalfinal = parseInt($("#total_final1").val());
        var validacion = calculartotales();
        if ((valor_efectivo < 0)) {
            swal("Error!", "El valor ingresado es incorrecto", "error");
            $('#valor_tarjetacredito').val('');
            $("#datos_tarjeta_credito").hide();

        }
        if (isNaN(valor_efectivo)) {
            $("#datos_tarjeta_credito").hide();
        }
        if (valor_efectivo > valor_totalfinal) {

            swal("Error!", "El valor ingresado no puede ser mayor al valor Total", "error");

            $('#valor_tarjetacredito').val('');

        }
        if ((valor_efectivo > 0) && (valor_efectivo <= valor_totalfinal)) {
            if (validacion == 'ok') {
                $("#datos_tarjeta_credito").show();

            } else {
                swal("Error!", "No cumple la suma con el total de la factura", "error");
                $('#valor_tarjetacredito').val('');
            }
        }
    }

    function validar_tarj_debito() {
        var valor_efectivo = parseInt($("#valor_tarjetadebito").val());
        var valor_totalfinal = parseInt($("#total_final1").val());
        var validacion = calculartotales();
        if ((valor_efectivo < 0)) {
            swal("Error!", "El valor ingresado es incorrecto", "error");
            $('#valor_tarjetadebito').val('');
            $("#datos_tarjeta_debito").hide();
        }
        if (isNaN(valor_efectivo)) {
            $("#datos_tarjeta_debito").hide();
        }
        if (valor_efectivo > valor_totalfinal) {

            swal("Error!", "El valor ingresado no puede ser mayor al valor Total", "error");

            $('#valor_tarjetadebito').val('');

        }
        if ((valor_efectivo > 0) && (valor_efectivo <= valor_totalfinal)) {
            if (validacion == 'ok') {
                $("#datos_tarjeta_debito").show();

            } else {
                swal("Error!", "No cumple la suma con el total de la factura", "error");
                $('#valor_tarjetadebito').val('');
                //swal(validacion);
            }
        }
    }

    function calculartotales() {
        var valor_totalfinal = parseFloat($("#total_final1").val());
        if (isNaN(valor_totalfinal)) {
            valor_totalfinal = 0;
        }
        var valor_credito = parseFloat($("#valor_tarjetacredito").val());
        if (isNaN(valor_credito)) {
            valor_credito = 0;
        }
        var valor_efectivo = parseFloat($("#valor_efectivo").val());
        if (isNaN(valor_efectivo)) {
            valor_efectivo = 0;
        }
        var valor_cheque = parseFloat($("#valor_cheque").val());
        if (isNaN(valor_cheque)) {
            valor_cheque = 0;
        }
        var valor_debito = parseFloat($("#valor_tarjetadebito").val());
        if (isNaN(valor_debito)) {
            valor_debito = 0;
        }
        var totales = valor_credito + valor_efectivo + valor_cheque + valor_debito;
        if ((totales) != NaN) {
            if (totales > valor_totalfinal) {
                return false;
            } else {
                return 'ok';
            }
        }
    }

    function cambiar_codigo(id) {
        $.ajax({
            type: 'post',
            url: "{{route('compra_codigo2')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'codigo': $("#codigo" + id).val(),
                'id_empresa': $("#id_empresa").val(),
            },
            success: function(data) {

                if (data.value != "no") {
                    $('#nombre' + id).val(data.value);
                    if (data.iva == '1') {
                        document.getElementById('iva' + id).checked = true;
                        document.getElementById('iva' + id).disabled = true;
                        $('#ivaver' + id).val(1);
                    }
                } else {
                    $('#nombre' + id).val(" ");
                }
            },
            error: function(data) {

            }
        })
    }
    // $("#proveedor").autocomplete({
    //     source: function(request, response) {
    //         $.ajax({
    //             url: "{{route('compra_identificacion')}}",
    //             dataType: "json",
    //             data: {
    //                 term: request.term
    //             },
    //             success: function(data) {
    //                 response(data);
    //             }
    //         });
    //     },
    //     minLength: 1,
    // });

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

                if (data.value != "no") {
                    $('#nombre_proveedor').val(data.value);
                    $('#direccion_proveedor').val(data.direccion);
                    //  $('#serie').val(data.serie);
                    $('#autorizacion').val(data.autorizacion);
                } else {
                    $('#nombre_proveedor').val(" ");
                    $('#direccion_proveedor').val("");
                    //    $('#serie').val("");
                    $('#autorizacion').val("");
                }
            },
            error: function(data) {

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

    function cambiar_nombre_proveedor(e) {
        /* array_d =$('#nombre_proveedor').select2('data');
        for(i in array_d){
            
            text_e = array_d[i]['text'];
        }
        $.ajax({
            type: 'post',
            url: "{{route('compra_buscar_proveedor')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'nombre': $("#proveedor").val()
            },
            success: function(data) {
                if (data.value != "no") {
                    $('#proveedor').val(data.value);
                    $('#direccion_proveedor').val(data.direccion);
                    $('#serie').val(data.serie);
                    $('#f_caducidad').val(data.caducidad);
                    $('#autorizacion').val(data.autorizacion);

                } else {
                    $('#proveedor').val("");
                    $('#direccion_proveedor').val("");
                    $('#serie').val("");
                    $('#f_caducidad').val("");
                    $('#autorizacion').val("");
                }

            },
            error: function(data) {
                
            }
        }); */
        var direccion = $('option:selected', e).data("direccion")
        $('#direccion_proveedor').val(direccion);
    }

    function aaingresar_cero() {
        let secuencia = $('#secuencia_factura').val(); //obtener el valor ingresado
        let numero = ""; //se encargara agregar los 0
        let final_secuencia = ""; //la concatenacion de ambas variabes 

        if (parseInt(secuencia) > 0) {
            if (secuencia.length > 10) {
                alertas("error", "Error!", "Valor no permitido");
                $('#secuencia_factura').val('');
            } else {
                if (secuencia.length < 10) {
                    while (final_secuencia.length != 10) {
                        numero = "0" + numero
                        final_secuencia = numero + secuencia
                    }
                    $('#secuencia_factura').val(final_secuencia);
                } else {
                    $('#secuencia_factura').val(secuencia);
                }
            }
        } else {
            alertas("error", "Error!", "Valor no permitido", );
            $('#secuencia_factura').val('');
        }
    }


    function ingresar_cero(ids, longitud) {

        let id = document.getElementById(ids);
        let cero = "";
        let concat = "";
        if (parseInt(id.value) > 0) {
            if (id.value.length < longitud) {
                while (concat.length != longitud) {
                    cero = "0" + cero;
                    concat = cero + id.value;

                }
                id.value = concat;
            } else {
                alertas("Error!", "error", "Valor incorrecto");
                id.value = "";
            }
        } else {
            alertas("Error!", "error", "Valor incorrecto");
            id.value = "";
        }
    }


    function _ingresar_cero() {
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

    function secuencia_f(secuencia_factura) {
        var digitos = 9;
        var ceros = 0;
        var varos = '0';
        var secuencia = 0;
        if (secuencia_factura > 0) {
            var longitud = parseInt(secuencia_factura.length);
            if (longitud > 10) {
                swal("Error!", "Valor no permitido", "error");
                $('#id_fc').val('');

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
                $('#id_fc').val(secuencia + secuencia_factura);
            }


        } else {
            swal("Error!", "Valor no permitido", "error");
            $('#id_fc').val('');
        }
    }

    function agregar_serie() {
        var serie = $('#serie').val();
        if ((serie.length) == 3) {
            $('#serie').val(serie + '-');
        } else if ((serie.length) > 7) {
            $('#serie').val('');
            alertas('error', 'Error!..', `{{trans('proforma.seriecorrectamente')}}`)

            // alertas("Error!", `{{trans('proforma.seriecorrectamente')}}`, "error");
        }
    }
</script>


@endsection