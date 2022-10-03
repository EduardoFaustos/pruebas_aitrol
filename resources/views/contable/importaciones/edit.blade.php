@extends('contable.compras_pedidos.base')
@section('action-content')
<style type="text/css">
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
            <li class="breadcrumb-item"><a href="../compras">Registro Importaciones</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar Pedido de Importación</li>
        </ol>
    </nav>
    <form class="form-vertical " id="crear_factura" role="form" method="post">
        {{ csrf_field() }}
        <input type="hidden" id="id_cabecera" name="id_cabecera" value="{{$datos_importaciones->id}}">
        <div class="box box-solid">
            <div class="box-header header_new">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-9 col-sm-9 col-6">

                            <div class="box-title"><b>EDITAR PEDIDO DE COMPRA</b></div>
                        </div>
                        <div class="col-3" style="text-align:center">
                            <div class="row">
                                <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="javascript:goBack()">
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body dobra">
                <div class="header row">
                    <div class="col-md-12">
                        <div class="row">
                            @php
                            $seleccion="";
                            @endphp
                            <div class="col-md-3 col-xs-3 px-1">
                                <label class=" label_header">{{trans('contableM.proveedor')}}</label>
                                <select class="form-control select2_cuentas" style="width: 100%;" onchange="cambiar_nombre_proveedor(this)" name="proveedor" id="proveedor">
                                    <option value="{{$datos_importaciones->proveedor_da->direccion}}">{{$datos_importaciones->proveedor_da->razonsocial}}</option>

                                    @foreach($proveedor as $value)
                                    @php
                                    $seleccion="";
                                    @endphp
                                    @if($datos_importaciones->id_proveedor == $value->id)
                                    $seleccion ="selected";
                                    @endif
                                    <option {{$seleccion}} data-direccion="{{$value->direccion}}" value="{{$value->id}}">
                                        {{$value->id}} - {{$value->razonsocial}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 col-xs-3 px-1">
                                <label class=" label_header">{{trans('contableM.direccion')}}</label>
                                <div class="input-group">
                                    <input id="direccion_proveedor" type="text" class="form-control" name="direccion_proveedor" value={{$datos_importaciones->proveedor_da->direccion}}>
                                    <div class="input-group-addon ">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('direccion_proveedor').value = '';"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-xs-3 px-1">
                                <label class=" label_header">{{trans('contableM.pais')}}</label>
                                <select onchange="paisProcedencia()" class="form-control select2_cuentas" style="width: 100%;"  name="pais" id="pais">
                                    <option value="">Seleccione...</option>
                                    @foreach($pais as $value)
                                    <option data-direccion="{{ $value->id }}" value="{{$value->id}}">
                                         {{ $value->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 col-xs-3 px-1">
                                <label class=" label_header">{{trans('contableM.paisprocedencia')}}</label>
                                <div class="input-group">
                                    <input id="pais_procedencia" type="text" class="form-control" name="pais_procedencia">
                                    <div class="input-group-addon ">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: black;"
                                            onclick="document.getElementById('pais_procedencia').value = '';"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class=" label_header">{{trans('contableM.fechapedido')}}</label>
                                <div class="input-group col-md-12">
                                    <input id="f_autorizacion" type="date" class="form-control   col-md-12" name="f_autorizacion" value="@php echo date('Y-m-d');@endphp">
                                </div>
                            </div>
                         
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class=" label_header">{{trans('contableM.serie')}}</label>
                                <div class="input-group">
                                    <input id="serie" maxlength="25" type="text" class="form-control" value="{{ $datos_importaciones->serie }}" name="serie"
                                        onkeyup="agregar_serie()">
                                    <div class="input-group-addon ">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: black;"
                                            onclick="document.getElementById('serie').value = '';"></i>
                                    </div>
                                </div>
                            </div>

                           
                            <div class="col-md-3 col-xs-2 px-1">
                                <label class=" label_header">{{trans('contableM.serie')}}</label>
                                <div class="input-group">
                                    <input id="secuencia_factura" maxlength="30" type="text" value="{{ $datos_importaciones->secuencia_factura }}" class="form-control  "
                                        name="secuencia_factura" onchange="ingresar_cero()">
                                    <div class="input-group-addon ">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: black;"
                                            onclick="document.getElementById('secuencia_factura').value = '';"></i>
                                    </div>
                                </div>
                            </div>
                            

                            <div class="col-md-3 col-xs-2 px-1">
                                <label class=" label_header">{{trans('contableM.SecuenciaPedido')}}</label>
                                <div class="input-group">
                                    <input id="secuencia_factura" maxlength="30" type="text" class="form-control  " name="secuencia_factura" onchange="ingresar_cero()" value={{$datos_importaciones->secuencia_factura}}>
                                    <div class="input-group-addon ">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('secuencia_factura').value = '';"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-2 px-1">
                                <label class=" label_header">{{trans('contableM.asiento')}}</label>
                                <div class="input-group">
                                    <input id="id_asiento" disabled  maxlength="30" type="text" class="form-control" value="{{ $datos_importaciones->id_asiento_cabecera }}" name="id_asiento">
                                    <div class="input-group-addon ">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: black;"
                                            onclick="document.getElementById('id_asiento').value = '';"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row  ">
                            <input type="text" name="ivareal" id="ivareal" class="hidden" value="0.12">
                            <div class="col-md-12 px-1">
                                <label class=" label_header">{{trans('contableM.concepto')}}</label>
                                <input autocomplete="off" type="text" class="form-control col-md-12" name="observacion" id="observacion" value="{{$datos_importaciones->observacion}}">
                            </div>
                            <input type="hidden" name="id_empresa" id="id_empresa" value="{{$empresa->id}}">
                            <input type="hidden" name="sucursal_final" id="sucursal_final">
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
                                <th width="10%" class="" tabindex="0">{{trans('contableM.peso')}}</th>
                                <th width="10%" class="" tabindex="0">% {{trans('contableM.prctdesc')}}</th>
                                <th width="10%" class="" tabindex="0">{{trans('contableM.descuento')}}</th>
                                <th width="10%" class="" tabindex="0">{{trans('contableM.precioneto')}}</th>
                                <th width="10%" class="" tabindex="0">Porcentaje</th>
                                <!--<th width="5%" class="" tabindex="0">{{trans('contableM.iva')}}</th>-->
                                <th width="10%" class="" tabindex="0">
                                    <button onclick="crearFila()" type="button" class="btn btn-success btn-gray">
                                        <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        @php
                        $contador_items=1;
                        $selected = "";
                        $selected2 = "";
                        $a = 1;
                        @endphp
                        <tbody id="agregar_cuentas">
                            @foreach($datos_importaciones->detalles as $value)
                           
                            <tr>
                                <td style="max-width:100px;">
                                    <Input id="codigo{{$contador_items}}" type="hidden" name="codigo[]" class="codigo_producto" />
                                    <select id="producto{{$contador_items}}" name="producto[]" class="form-control select2_cuentas" style="width:100%" required onchange="verificar(this)">
                                        @foreach($productos as $p)
                                        @php   $selected =""; @endphp
                                        @php if($value->id_producto == $p->id){
                                            $selected = "selected";
                                        }
                                        @endphp
                                        <option {{$selected}} value="{{$p->id}}" data-name="{{$p->nombre}}" data-codigo="{{$p->codigo}}" data-descuento="{{$p->mod_desc}}" data-precio="{{$p->mod_precio}}" data-maxdesc="{{$p->descuento}}" data-iva="{{$p->iva}}">{{$p->codigo}} | {{$p->nombre}}</option>
                                        @endforeach

                                    </select>
                                    <textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea>
                                    <input type="hidden" name="iva[]" class="iva" />
                                </td>
                                <td>
                                    <input  id="cantidad{{$contador_items}}"  value="{{$value->cantidad}}" class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" name="cantidad[]" required>
                                    <select id="bodega{{$contador_items}}" name="bodega[]" class="form-control select2_bodega bodega" style="width: 80%;margin-top: 5px;" required>

                                        @foreach($bodega as $b)
                                        <option value="{{$b->id}}">{{$b->nombre}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input id="precio{{$contador_items}}" value="{{$value->precio}}" type="text" class="pneto form-control" name="precio[]" style="width: 80%;height:20px;" placeholder="0.00">
                                </td>
                                <td>
                                    <input id="precio{{$contador_items}}" id="peso${id}" value="{{$value->peso}}" type="text" class="pneto form-control" name="peso[]" style="width: 80%;height:20px;" placeholder="0.00">
                                </td>
                                <td>
                                    <input id="descpor{{$contador_items}}" value="{{$value->descuento_porcentaje}}" class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor[]" required>
                                    <input  id="maxdesc{{$contador_items}}"value="{{$value->descuento}}"  class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" required>
                                </td>
                                <td>
                                    <input  id="desc{{$contador_items}}" value="{{$value->descuento}}" class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc[]" required>
                                </td>
                                <td>
                                    <input  id="precioneto{{$contador_items}}"  value="{{$value->subtotal}}"  class="form-control px-1 text-right precioneto" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" required>
                                </td>

                                <td>
                                    <input readonly id="porcentaje${id}" style="height:20px;" class="form-control px-1 text-right" type="text" name="porcentaje[]">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-gray delete">
                                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                            @php
                            $contador_items++;
                            @endphp
                            @endforeach
                            <input name='contador_items' id='contador_items' type='hidden' value="{{$contador_items}}">
                            <!-- Se crean  -->
                        </tbody>
                        <tfoot class=''>
                            <tr>
                                <td colspan="6"></td>
                                <td colspan="2" class="text-right">{{trans('contableM.subtotal12')}}%</td>
                                <td id="subtotal_12" class="text-right px-1">0.00</td>
                                <input type="hidden" name="subtotal_121" id="subtotal_121" class="hidden">
                            </tr>
                            <tr>
                                <td colspan="6"></td>
                                <td colspan="2" class="text-right">{{trans('contableM.subtotal0')}}%</td>
                                <td id="subtotal_0" class="text-right px-1">0.00</td>
                                <input type="hidden" name="subtotal_01" id="subtotal_01" class="hidden">
                            </tr>
                            <tr>
                                <td colspan="6"></td>
                                <td colspan="2" class="text-right">{{trans('contableM.descuento')}}</td>
                                <td id="descuento" class="text-right px-1">0.00</td>
                                <input type="hidden" name="descuento1" id="descuento1" class="hidden">
                            </tr>
                            <tr>
                                <td colspan="6"></td>
                                <td colspan="2" class="text-right">{{trans('contableM.subtotal')}}</td>
                                <td id="base" class="text-right px-1">0.00</td>

                                <input type="hidden" name="base1" id="base1" class="hidden">
                            </tr>
                            <tr>
                                <td colspan="6"></td>
                                <td colspan="2" class="text-right">{{trans('contableM.tarifaiva')}}</td>
                                <td id="tarifa_iva" class="text-right px-1">0.00</td>
                                <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" class="hidden">
                            </tr>

                            <tr>
                                <td colspan="6"></td>
                                <td colspan="2" class="text-right"><strong>{{trans('contableM.total')}}</strong></td>
                                <td id="total" class="text-right px-1">0.00</td>
                                <input type="hidden" name="total1" id="total1" class="hidden">
                            </tr>
                            <tr>
                                <td colspan="6"></td>
                                <td colspan="2" class="text-right"></td>
                                <td id="copagoTotal" class="text-right px-1"></td>
                                <input type="hidden" name="totalc" id="totalc" class="hidden">
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="col-md-12" style="text-align: center;">
                    <!--<button class="btn btn-success btn-gray" onclick="guardarImportacion(event)" id="boton_guardar"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.guardar')}}
                    </button>-->
                </div>

            </div>
        </div>
    </form>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>s

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const crearFila = () => {
        var id = document.getElementById('contador_items').value;
        let fila = `
        <tr >
                                <td style="max-width:100px;">
                                    <Input id="codigo${id}" type="hidden" name="codigo[]" class="codigo_producto" />
                                    <select id="producto${id}" name="producto[]" class="form-control select2_cuentas" style="width:100%" required onchange="verificar(this)">
                                        <option> </option>
                                        @foreach($productos as $value)
                                        <option value="{{$value->id}}" data-name="{{$value->nombre}}" data-codigo="{{$value->codigo}}" data-descuento="{{$value->mod_desc}}" data-precio="{{$value->mod_precio}}" data-maxdesc="{{$value->descuento}}" data-iva="{{$value->iva}}">{{$value->codigo}} | {{$value->nombre}}</option>
                                        @endforeach

                                    </select>
                                    <textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea>
                                    <input type="hidden" name="iva[]" class="iva" />
                                </td>
                                <td>
                                    <input onchange="totalProducto(${id})" id="cantidad${id}" class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="{{$datos_importaciones->cantidad}}" name="cantidad[]" required>
                                    <select id="bodega${id}" name="bodega[]" class="form-control select2_bodega bodega" style="width: 80%;margin-top: 5px;" required>
                                        <option> </option>
                                        @foreach($bodega as $value)
                                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input onchange="totalProducto(${id})" id="precio${id}" value="0.00" type="text" class="pneto form-control" name="precio[]" style="width: 80%;height:20px;" placeholder="0.00">
                                </td>
                                <td>
                                    <input onchange="totalProducto(${id})" id="peso${id}" value="0.00" type="text" class="pneto form-control" name="peso[]" style="width: 80%;height:20px;" placeholder="0.00">
                                </td>
                                <td>
                                    <input onchange="totalProducto(${id})" id="descpor${id}" class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor[]" required>
                                    <input onchange="totalProducto(${id})" id="maxdesc${id}" class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" required>
                                </td>
                                <td>
                                    <input onchange="totalProducto(${id})" id="desc${id}" class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc[]" required>
                                </td>
                                <td>
                                    <input onchange="totalProducto(${id})" id="precioneto${id}" class="form-control px-1 text-right precioneto" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" required>
                                </td>
                                <!--<td>
                                    <input class="form" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]">

                                </td>-->
                                <td>
                                    <input readonly id="porcentaje${id}" style="height:20px;" class="form-control px-1 text-right" type="text" name="porcentaje[]">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-gray delete">
                                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                    </button>
                                </td>
                </tr>
        `

        $('#agregar_cuentas').append(fila);
        $('.select2_cuentas').select2({
            tags: false
        });
        id++;
        document.getElementById('contador_items').value = id;
    }

    function totalProducto(id) {
        let cantidad = document.getElementById("cantidad" + id).value;
        let precio = document.getElementById("precio" + id).value;
        let descpor = document.getElementById("descpor" + id).value;
        let desc = document.getElementById("desc" + id)
        let precioneto = document.getElementById("precioneto" + id)

        let preXcantidad = (precio * cantidad)
        let porcDescuento = (descpor / 100) * preXcantidad;
        let precioTotal = (preXcantidad) - porcDescuento;
        desc.value = porcDescuento.toFixed(2)
        precioneto.value = precioTotal.toFixed(2);
        totalGlobal();

    }

    function totalGlobal() {
        let precioneto = document.querySelectorAll(".precioneto");
        let desc = document.querySelectorAll(".desc");
        let total = 0;
        let descuento = 0;
        for (let i = 0; i < precioneto.length; i++) {
            total += parseFloat(precioneto[i].value)
            console.log(total)
            descuento += parseFloat(desc[i].value)
        }

        document.getElementById('total').innerHTML = parseFloat(total).toFixed(2);
        document.getElementById('total1').value = parseFloat(total).toFixed(2);

        document.getElementById('descuento').innerHTML = parseFloat(descuento).toFixed(2);
        document.getElementById('descuento1').value = parseFloat(descuento).toFixed(2);

    }

    function guardarImportacion(e) {
        e.preventDefault();
        var formulario = document.forms["crear_factura"];
        var observacion = formulario.observacion.value;
        var proveedor = formulario.proveedor.value;
        var secuencia = formulario.secuencia_factura.value;
        var serie = formulario.serie.value;
        var id_cabecera=document.getElementById("id_cabecera").value;
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
        if (msj != "") {
            alertas('error', 'Error!..', msj)
        } else {
            $.ajax({
                type: 'post',
                url: "{{route('importaciones.update', ['id'=>"+id_cabecera+"])}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $('#crear_factura').serialize(),
                success: function(data) {
                    console.log(data);
                    alertas(data.respuesta, data.titulos, data.msj)
                },
                error: function(data) {

                }
            });
        }


    }

    function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
    }
</script>

<script type="text/javascript">
    var fila = $("#mifila").html();
    $(document).ready(function() {

        limpiar();

        //crearFila();

        //$('#myform')[0].reset(); PARA LIMPIAR TODOS LOS INPUTS DENTRO DEL FORM
        $('.select2_cuentas').select2({
            tags: false
        });



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

        if (iva == '1') {
            $(e).parent().next().next().next().next().next().next().children().attr("checked", "checked");
        } else {
            $(e).parent().next().next().next().next().next().next().children().removeAttr("checked");
        }
    }

    function redondeafinal(num, decimales = 2) {
        var signo = (num >= 0 ? 1 : -1);
        num = num * signo;
        //console.log("eduardo maricon");
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

    function agregarElement(data) {
        var id = "";
        var contador = 0;
        console.log(data[3]);
        for (i = 0; i < data[3].length; i++) {
            //console.log(data[3][i].codigo);
            var midiv = document.createElement("tr");
            var extendido = parseFloat(data[3][i].precio) * parseFloat(data[3][i].cantidad_total);
            midiv.innerHTML =
                '<tr style="display:none" id="mifila"><td style="max-width:100px;"><input type="hidden" name="codigo[]" class="codigo_producto" /><select  id="f' +
                contador +
                '" name="nombre[]" class="form-control select2s" style="width:100%" required onchange="verificar(this)"><option> </option>@foreach($productos as $value)<option value="{{$value->codigo}}" data-name="{{$value->nombre}}" data-codigo="{{$value->codigo}}"data-descuento="{{$value->mod_desc}}" data-precio="{{$value->mod_precio}}" data-maxdesc="{{$value->descuento}}" data-iva="{{$value->iva}}" >{{$value->codigo}} | {{$value->nombre}}</option>@endforeach</select><textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea><input type="hidden" name="iva[]" class="iva" /></td><td><input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="' +
                data[3][i].cantidad_total +
                '" name="cantidad[]"  required><select name="bodega[]" class="form-control select2_bodega bodega" style="width: 80%;margin-top: 5px;" required ><option> </option>@foreach($bodega as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select></td><td><input type="text" class="pneto form-control" name="precio[]" style="width: 80%;height:20px;" value="' +
                data[3][i].precio +
                '" placeholder="0.00"></td><td><input class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor[]" required><input class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" required></td><td><input class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc[]" required></td><td><input class="form-control px-1 text-right" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" value="' +
                extendido +
                '" required></td><td><input class="form" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]" disabled></td><td><button type="button" class="btn btn-danger btn-gray delete"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td></tr>';

            document.getElementById('agregar_cuentas').appendChild(midiv);
            //$(".select2s").val(data[3][i].codigo);
            //$(".select2s").select2().trigger('change');
            $("#f" + contador).val(data[3][i].codigo);
            $("#f" + contador).select2().trigger('change');
            contador++;
        }


        totales(0);

    }

    $('body').on('click', '.delete', function() {
        //console.log($(this));

        $(this).parent().parent().remove();
        totales(0);
    });
    $('body').on('click', '.form', function() {
        //console.log($(this));

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

    function crea_td(contador) {

        id = document.getElementById('contador').value;
        var midiv = document.createElement("tr")
        midiv.setAttribute("id", "dato" + id);
        midiv.innerHTML = `
                        <td>
                            <input   style=" width: 100%; height: 80%;" name="codigo${id}" class="codigo form-control" id="codigo${id}" onchange="cambiar_codigo(${id})"/>
                        </td>
                        <td>
                            <input   style=" width: 100%; height: 80%;" name="codigoref${id}" class="form-control" id="codigoref${id}"/>
                        </td> 
                        <td>
                            <input type="hidden" id="visibilidad${id}" name="visibilidad${id}" value="1">
                            <input name="nombre${id}"  class="nombre form-control" id="nombre${id}" onchange="cambiar_nombre(${id})" style="width: 100%; height: 80%;">
                        </td>
                        <td>
                            <select class="form-control select2" id="bodega${id}" required name="bodega${id}" style="width: 100%; height: 78%;"> 
                            @foreach($bodega as $value) 
                                <option value="{{$value->id}}">{{$value->nombre}}</option>  
                            @endforeach
                            </select>
                        </td>
                        <td> 
                            <input class="form-control" style=" width: 100%; height: 80%;" type="text" id="cantidad${id}" required value="0.00" onchange="total_calculo(${id}); redondea_cantidad(this, ${id},2);" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="cantidad${id}"> 
                        </td>
                        <td> 
                            <select class="form-control" id="empaque${id}" name="empaque${id}" style="width: 100%; height: 80%;"> 
                                <option value="unidad">Unidad</option> 
                            </select>
                        </td>
                        <td>
                            <input class="form-control" type="text" id="precio${id}" name="precio${id}" value="0.00" onchange="total_calculo(${id}); validarprecio(this,${id}); " onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" style="width: 100%; height: 80%;">
                        </td>
                        <td> 
                            <input class="form-control" type="text" name="desc_porcentaje${id}" id="desc_porcentaje${id}" onchange="total_calculo(${id})" value="0.00" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" onchange="redondea_descuento(this,${id},2);" style="width: 100%; height: 80%;">
                        <td> 
                            <input class="form-control" type="text" name="desc${id}" id="desc${id}" value="0" style="width: 100%; height: 80%;" disabled>
                            <input type="text" class="hidden" name="desc_${id}" id="desc_${id}" >
                        </td> 
                        <td>
                            <input class="form-control" name="extendido${id}" id="extendido${id}" value="0" style="width: 100%; height: 80%;" disabled>
                            <input type="text" class="hidden" name="extendido_${id}" id="extendido_${id}"> 
                            <input type="hidden" name="ivaver${id}" id="ivaver${id}"> 
                        </td>
                        <!--<td> 
                            <input type="checkbox" id="iva${id}" name="iva${id}" onchange="suma_totales(${id}); ivas(${id});" value="1"> 
                        </td> -->
                        <td> 
                            <input type="checkbox" id="ice${id}" name="ice${id}" value="1">
                        </td>
                        <td>
                            <button id="eliminar${id}" type="button" onclick="javascript:eliminar_registro(${id})" class="btn btn-danger btn-gray delete btn-xs">
                                <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                            </button>
                        </td>`;
        document.getElementById('det_recibido').appendChild(midiv);
        id = parseInt(id);
        id = id + 1;
        document.getElementById('contador').value = id;

        $(".nombre").autocomplete({
            source: function(request, response) {
                var id_empresa = $("#id_empresa").val();
                $.ajax({
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    url: "{{route('compra_nombre')}}",
                    dataType: "json",
                    data: {
                        term: request.term,
                        'id_empresa': id_empresa,
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 2,
        });

        $(".codigo").autocomplete({

            source: function(request, response) {
                var id_empresa = $("#id_empresa").val();
                $.ajax({
                    url: "{{route('compra_codigo')}}",
                    dataType: "json",
                    data: {
                        term: request.term,
                        'id_empresa': id_empresa,
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 1,
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
                        console.log(data);
                        swal("Exito!", 'Guardado con Exito', "success");
                        setTimeout(function() {

                            location.href = "{{route('contable.compraspedidos.index')}}";
                        }, 1000);


                    },
                    error: function(data) {
                        console.log(data);
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
        //console.log("entra ivas");
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

    function buscar_factura() {
        var contador = parseInt($("#contador").val());
        if (isNaN(contador)) {
            contador = 0;
        }
        if (!isNaN($("#factura_nombre").val())) {
            $.ajax({
                type: 'post',
                url: "{{route('compra_buscar_factura')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'id_factura': $("#factura_nombre").val()
                },
                success: function(data) {
                    //console.log(data.value);
                    if (data.value != 'no resultados') {
                        if ($("#proveedor").val() != "") {
                            if (data[0] != $("#proveedor").val()) {
                                swal("Error", "Ingreso un pedido de otro");
                            } else {
                                $('#proveedor').val(data[0]);
                            }
                        }
                        //$('#proveedor').val(data[0]);
                        $('#direccion_proveedor').val(data[4]);
                        $('#fecha').val(data[1]);
                        $('#f_caducidad').val(data[2]);
                        $('#id_empresa').val(data[6]);
                        for (var i = 0; i < data[3].length; i++) {
                            crea_td(i);
                            $('#codigo' + contador).val(data[3][i].codigo);
                            $('#nombre' + contador).val(data[3][i].nombre);
                            /* $('#bodega'+i).val(data[3][i].id_bodega);*/
                            $('#cantidad' + contador).val(data[3][i].cantidad_total);
                            $('#total' + contador).val(data[3][i].cantidad_total);
                            $('#precio' + contador).val(data[3][i].precio);
                            if (data[3][i].iva == '1') {
                                document.getElementById('iva' + i).checked = true;
                            }
                            total_calculo(contador);
                            cambiar_proveedor();
                            contador++;
                        }
                        suma_totales()
                        $("#contador").val(data[3].length);
                        $('#nombre_proveedor').val(data[0]).trigger("change");
                    } else {
                        $('#proveedor').val('');
                    }
                },
                error: function(data) {
                    console.log(data);
                    swal("Error!", "No coinciden los productos del pedido con los productos en stock. <br>",
                        "error");
                }
            })
        }

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
        //console.log("entra sistemas");
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

            //console.log(visibilidad+"prueba visibilidad");
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

        if (trans > 0) {
            total_fin = sub + trans + iva;
        } else {
            total_fin = sub + iva;
        }
        if (descu1 > 0) {
            total_fin = total_fin - descu1;
        }

        $('#iva_final').val(iva.toFixed(2, 2));

        //console.log(iva);
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
        //console.log("con inv" + iva + " y descuento " + descu1);
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
                console.log(data);
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
                console.log(data);
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
                console.log(data);
            }
        })
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
                    $('#serie').val(data.serie);
                    $('#autorizacion').val(data.autorizacion);
                } else {
                    $('#nombre_proveedor').val(" ");
                    $('#direccion_proveedor').val("");
                    $('#serie').val("");
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

    function cambiar_nombre_proveedor(e) {

        var direccion = $('option:selected', e).data("direccion")
        console.log(direccion);
        $('#direccion_proveedor').val(direccion);
    }

    function ingresar_cero() {
        let secuencia = $('#secuencia_factura').val(); //obtener el valor ingresado
        let numero = ""; //se encargara agregar los 0
        let final_secuencia = ""; //la concatenacion de ambas variabes 
        if (secuencia.length > 10) {
            alertas("error", "Error!", "Valor no permitido", );
            $('#secuencia_factura').val('');

        } else {
            while (final_secuencia.length != 10) {
                numero = "0" + numero
                final_secuencia = numero + secuencia
            }
        }
        $('#secuencia_factura').val(final_secuencia);
    }


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