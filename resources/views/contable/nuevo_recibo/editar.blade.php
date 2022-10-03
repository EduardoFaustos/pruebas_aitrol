@extends('contable.facturacion.base')
@section('action-content')
<style type="text/css">
    td {
        padding-top: 2px !important;
        padding-bottom: 2px !important;
    }

    .alerta_correcto {
        position: absolute;
        z-index: 9999;
        bottom: 100px;
        right: 20px;
    }

    .alerta_guardado {
        position: absolute;
        z-index: 9999;
        bottom: 100px;
        right: 20px;
    }

    .disableds {
        display: none;
    }

    .disableds2 {
        display: none;
    }

    .disableds3 {
        display: none;
    }

    .has-cc span img {
        width: 2.775rem;
    }

    .has-cc .form-control-cc {
        z-index: 2;
        display: block;
        width: 2.375rem;
        height: 2.375rem;
        line-height: 1.8rem;
        text-align: center;
        pointer-events: none;
        color: #444;
        font-size: 1.5em;
        float: right;
        margin-right: 1px;
    }

    .has-cc .form-control-cc2 {
        z-index: 2;
        display: block;
        width: 2.375rem;
        height: 2.375rem;
        line-height: 1.8rem;
        text-align: center;
        pointer-events: none;
        color: #444;
        font-size: 1.5em;
        float: right;
        margin-right: 1px;
    }

    .cvc_help {
        cursor: pointer;
    }

    .card {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        padding: 16px;
        text-align: center;
        background-color: white;
    }

    .card2 {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        padding: 16px;
        text-align: center;
        background-color: #f1f1f1;
    }

    .swal-title {
        margin: 0px;
        font-size: 16px;
        box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.21);
        margin-bottom: 28px;
    }

    .card-header {
        border-radius: 6px 6px 0 0;
        background-color: #3c8dbc;
        border-color: #b2b2b2;
        padding: 8px;
        font-family: 'Roboto', sans-serif;
    }

    .col-md-6 {
        margin-top: 7px;
    }
</style>
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

    .disableds {
        display: none;
    }

    .dogde {
        width: 100%;
        height: 20px;
    }

    .disableds2 {
        display: none;
    }

    .wells {
        background-color: #E3F2FD;
    }

    .disableds3 {
        display: none;
    }

    .has-cc span img {
        width: 2.775rem;
    }

    .has-cc .form-control-cc {
        z-index: 2;
        display: block;
        width: 2.375rem;
        height: 2.375rem;
        line-height: 1.8rem;
        text-align: center;
        pointer-events: none;
        color: #444;
        font-size: 1.5em;
        float: right;
        margin-right: 1px;
    }

    .has-cc .form-control-cc2 {
        z-index: 2;
        display: block;
        width: 2.375rem;
        height: 2.375rem;
        line-height: 1.8rem;
        text-align: center;
        pointer-events: none;
        color: #444;
        font-size: 1.5em;
        float: right;
        margin-right: 1px;
    }

    .cvc_help {
        cursor: pointer;
    }

    .card {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        padding: 16px;
        text-align: center;
        background-color: white;
    }

    .card2 {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        padding: 16px;
        text-align: center;
        background-color: #f1f1f1;
    }

    .swal-title {
        margin: 0px;
        font-size: 16px;
        box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.21);
        margin-bottom: 28px;
    }

    .cabecera {
        background-color: #3c8dbc;
        border-radius: 8px;
        color: white;
    }

    .borde {
        border: 2px solid #3c8dbc;
    }

    .s {
        font-size: 17px !important;
    }

    .visible {
        display: none;
    }
</style>

<div class="modal fade" id="tarifario_nivel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content ">
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-lg" id="modal_datosfacturas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" id="datos_factura">
      </div>
    </div>
</div>

<!-- 
http://192.168.75.125/sis_medico_prb/public/contable/facturacion/agenda/21526 -->
<section class="content">
    <div id="alerta_guardado" class="alert alert-success alerta_guardado alert-dismissable" role="alert" style="display:none;">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{trans('new_recibo.GuardadoCorrectamente')}}
    </div>

    <div class="box box s">
        <div class="header box-header with-border">
            <div class="box-title col-md-9">
                <b style="font-size: 16px;">{{trans('new_recibo.ReciboCobro')}} #{{$orden->id}}</b>
            </div>
            @if($orden->estado == 1)<div class="col-md-3 alert-success" style="text-align: right;"> Orden Ya Emitida !!! </div>@endif
        </div>

        <div class="box-body">
            <a href="#" target="_blank" id="ride"></a>
            <form class="form-vertical" id="form_actualizar_cabecera" method="POST" autocomplete="off">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="id_orden" id="id_orden" value="{{$orden->id}}">
                <input type="hidden" name="empresa" id="id_empresa" value="{{$empresa->id}}">
                <input type="hidden" name="id_agenda" id="id_agenda" value="{{$agenda->id}}">
                <input type="hidden" name="id_doctor" id="id_doctor" value="{{$agenda->id_doctor1}}">
                <div class="col-md-12">
                    <div class="row dobra">
                        @php 
                            if($agenda->id_doctor1 == null){
                                $stringarm="preagenda/".$agenda->id."/edit";
                            }else{
                                $stringarm="agenda/".$agenda->id."/edit/".$agenda->id_doctor1;
                            }
                        @endphp
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="row">
                                        <label class="col-md-8">{{trans('new_recibo.DatosPrincipales')}}</label>
                                        <div class="col-md-2" style="text-align: center;">
                                            @if($orden->estado == -1)
                                            <button class="btn btn-success" type="button" onclick="emitir()">Emitir</button>
                                            @endif
                                            @if($orden->estado == 1)
                                            <a href="{{route('facturacion.imprimir_ride',['id' => $orden->id])}}" class="btn btn-primary btn-gray" id="printer" type="button" target="_blank" > <i class="fa fa-print"></i> </a>
                                            @endif
                                        </div>
                                       
                                        <div class="col-md-2" style="text-align: center;">
                                           
                                            <button onclick="return location.href='{{url($stringarm)}}'" type="button" class="btn btn-primary  btn-gray">
                                                <i class="fa fa-arrow-left"></i>
                                            </button>

                                        </div>      
                                        <!--div class="col-md-2" style="text-align: right;">
                                            <button onclick="return window.location.href = window.location.href" type="button" class="btn btn-primary  btn-gray">
                                                <i class="fa fa-refresh"></i>
                                            </button>
                                        </div-->
                                    </div>

                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <img src="{{asset('/logo').'/'.$empresa->logo}}" style="width:210px;height:70px">
                                        </div>
                                        <div class="col-md-9">
                                            <label>{{$empresa->nombrecomercial}}</label><br>
                                            <label>{{$empresa->direccion}}</label>
                                        </div>
                                        <div class="col-md-12">&nbsp;</div>
                                        <div class="col-md-2">
                                            <label for="numero">{{trans('new_recibo.FechaAgenda')}}</label>
                                            <div class="form-group input-group-sm">

                                                <input type="date" name="f_procedimiento" value="{{date('Y-m-d',strtotime($agenda->fechaini))}}" readonly class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="numero">{{trans('new_recibo.FechaDeEmision')}}</label>
                                            <div class="form-group input-group-sm">

                                                <input type="datetime-local" name="f_emision" value="{{date('Y-m-d',strtotime($orden->nueva_fecha))}}T{{date('H:i',strtotime($orden->nueva_fecha))}}" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm">
                                            </div>
                                        </div>

                                        <div class="col-md-8">
                                            <label for="observacion">{{trans('new_recibo.Concepto')}}</label>
                                            <div class="form-group">
                                                <textarea class="form-control" name="observacion" id="observacion" cols="3" rows="1">{{ $orden->observacion}}</textarea>
                                            </div>
                                        </div>


                                    </div>

                                </div>

                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <label>{{trans('new_recibo.Datos')}}</label>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="form-group col-md-5 col-xs-12">
                                            <label for="idpaciente" class="col-md-4">{{trans('new_recibo.Cedula')}}</label>
                                            <div class="input-group  col-md-8">
                                                <label>{{$agenda->id_paciente}}</label>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-7 col-xs-12">
                                            <label for="nombres" class="col-md-3">{{trans('new_recibo.Paciente')}}</label>
                                            <div class="input-group col-md-9">
                                                <label>{{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif
                                                    {{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4 col-xs-12">
                                        <label for="id_tipo" class="col-md-4 control-label">Tipo</label>
                                        <div class="input-group col-md-8">
                                            <select class="form-control input-sm" name="id_tipo" id="id_tipo" required onchange="cargar_seguro()">
                                                <!--option value="">Seleccione ...</option-->
                                                @foreach($tipos as $tipo)
                                                    <option> {{$tipo->nombre}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4 col-xs-12">
                                        <label for="id_seguro" class="col-md-4 control-label">{{trans('new_recibo.Seguro')}}</label>
                                        <div class="input-group col-md-8">
                                            <select class="form-control input-sm" name="id_seguro" id="id_seguro" required onchange="cargar_nivel()">
                                                <!-- <option value="">Seleccione ...</option> -->
                                            </select>
                                        </div>
                                    </div>
                                    <div id="ident_nivel" class="col-md-3 col-xs-5">
                                        <label for="id_nivel" class="col-md-3 control-label">{{trans('new_recibo.Nivel')}}</label>
                                        <div class="input-group col-md-9">
                                            <select name="id_nivel" id="id_nivel" class="form-control input-sm">
                                                <option value="">Seleccione...</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3 col-xs-12">
                                        <div class="col-md-5">
                                            <label>{{trans('new_recibo.PoseeOda')}}</label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="oda" class="form-control input-sm" placeholder="# ODA" id="oda" value="{{ $orden->numero_oda }}">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2 col-xs-12" style="text-align: center; margin-top: 10px;">
                                        <button class="btn btn-success btn-success saves" type="button" onclick="guardar_cabecera()">
                                            <i class="fa fa-save"></i>
                                        </button>
                                    </div>
                                    <div class="col-md-12">
                                        &nbsp;
                                    </div>
                                    <div class="form-group col-md-12" style="background-color: #d4850e; color: white;">
                                        <label> {{trans('new_recibo.DatosDeCliente')}}</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <button class="btn btn-success btn-success saves" type="button" onclick="actualizar_cliente()" style="margin: 5px">
                                            <i class="fa fa-save"> Crear/Actualizar Cliente</i>
                                        </button>
                                    </div>
                                    <div class="form-group col-md-6 col-xs-12">
                                        <label for="cedula" class="col-md-5 control-label">{{trans('new_recibo.Tipo')}}</label>
                                        <div class="input-group col-md-7">
                                            <select id="tipo_identificacion" name="tipo_identificacion" class="form-control input-sm" required autofocus="">
                                                <option @if($orden->tipo_identificacion=='4') selected='selected' @endif value="4">{{trans('contableM.ruc')}}</option>
                                                <option @if($orden->tipo_identificacion=='5') selected='selected' @endif value="5">{{trans('contableM.cedula')}}</option>
                                                <option @if($orden->tipo_identificacion=='6') selected='selected' @endif value="6">{{trans('contableM.pasaporte')}}</option>
                                                <option @if($orden->tipo_identificacion=='8') selected='selected' @endif value="8">CEDULA EXTRANJERA</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!--Ruc/Cedula-->
                                    <div class="form-group col-md-6 col-xs-12">
                                        <label for="cedula" class="col-md-5 control-label">{{trans('new_recibo.RucCedula')}}</label>
                                        <div class="input-group col-md-7">
                                            <input id="cedula" maxlength="13" type="text" class="form-control" name="cedula" value="{{$orden->identificacion}}" placeholder="Ruc/Cédula" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onchange="buscar();" required>
                                            <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                                <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('cedula').value = '';"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <!--Razon Social-->
                                    <div class="form-group col-md-6 col-xs-12">
                                        <label for="razon_social" class="col-md-5 control-label">{{trans('new_recibo.RazonSocial')}}</label>
                                        <div class="input-group col-md-7">
                                            <input type="text" class="form-control input-sm" name="razon_social" id="razon_social" value="{{$orden->razon_social}}" placeholder="Razon Social" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required>
                                            <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                                <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('razon_social').value = '';"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <!--Ciudad-->

                                    <div class="form-group col-md-6 col-xs-12">
                                        <label for="ciudad" class="col-md-5 control-label">{{trans('new_recibo.Ciudad')}}</label>
                                        <div class="input-group col-md-7">
                                            <input type="text" class="form-control input-sm" name="ciudad" id="ciudad" value="{{$orden->ciudad}}" placeholder="Ciudad" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                            <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                                <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('ciudad').value = '';"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <!--Direccion-->
                                    <div class="form-group col-md-66 col-xs-6">
                                        <label for="direccion" class="col-md-5 control-label">{{trans('new_recibo.Direccion')}}</label>
                                        <div class="input-group col-md-7">
                                            <input type="text" class="form-control input-sm" name="direccion" id="direccion" value="{{$orden->direccion}}" placeholder="Dirección" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                            <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                                <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('direccion').value = '';"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <!--Telefono-->
                                    <div class="form-group col-md-6 col-xs-12">
                                        <label for="telefono" class="col-md-5 control-label">{{trans('new_recibo.Telefono')}}</label>
                                        <div class="input-group col-md-7">
                                            <input type="text" class="form-control input-sm" name="telefono" id="telefono" value="{{$orden->telefono}}" placeholder="Teléfono" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                            <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                                <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('telefono').value = '';"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <!--Email-->
                                    <div class="form-group col-md-6 col-xs-12">
                                        <label for="email" class="col-md-5 control-label">{{trans('new_recibo.Mail')}}</label>
                                        <div class="input-group col-md-7">
                                            <input type="text" class="form-control input-sm" name="email" id="email" value="{{$orden->email}}" placeholder="Mail">
                                            <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                                <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('email').value = '';"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6 col-xs-12">
                                        <label for="pago" class="col-md-5 control-label">{{trans('new_recibo.CajaCobro')}}</label>
                                        <div class="input-group col-md-7">
                                            <select id="caja" name="caja" class="form-control" required>
                                                <option value="">Seleccione...</option>
                                                @foreach($cajas as $caja)
                                                <option @if($orden->caja == $caja->id) selected @endif value="{{$caja->id}}">{{$caja->descripcion}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!--div class="form-group col-md-2 ">
                                        <div class="col-md-7">
                                            <a class="btn btn-primary" data-remote="{{route('comercial.proforma.proformaModal',['id_paciente' => $agenda->id_paciente])}}" data-toggle="modal" data-target="#tarifario_nivel">{{trans('contableM.crear')}}</a>
                                        </div>
                                    </div-->
                                    <!-- <div class="form-group col-md-6 col-xs-12" style="text-align: center; margin-top: 10px;">
                                        <button class="btn btn-success btn-success saves" type="button" onclick="guardar_cabecera()">
                                            <i class="fa fa-save">{{trans('contableM.actualizar')}}</i>
                                        </button>
                                    </div> -->
                                    <div class="form-group col-md-6 col-xs-12" style="text-align: center; margin-top: 10px;">
                                        <button class="btn btn-success btn-success saves" type="button" onclick="actualizar_cliente()">
                                            <i class="fa fa-save"> Crear/Actualizar Cliente</i>
                                        </button>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>


                    <div class="col-md-12" style="padding: 0;">
                        <div id="detalles_orden">

                        </div>
                    </div>
                    <div class="col-md-2" style="text-align: center;">
                        @if($orden->estado == -1)
                        <button class="btn btn-success" type="button" onclick="emitir()">Emitir</button>
                        @endif
                    </div>

                    <div class="col-md-12" style="padding: 0;">
                        <div id="detalles_forma_pago">

                        </div>
                    </div>

                </div>

        </div>
        </form>
    </div>
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>

<script type="text/javascript">
    //////////////// NUEVAS FUNCIONES DE INGRESO DE PRODUCTOS
    lista_productos();
    lista_formas_pago();
    cargar_nivel();
    function actualizar_cliente() {
        //alert(id_cliente);
        $.ajax({
            type: 'get',
            url:"{{ route('nuevorecibocobro.modal_actualiza_cliente', ['id_cliente' => $orden->identificacion, 'id_orden' => $orden->id])}}",
            datatype: 'json',
            success: function(data) {
              $('#datos_factura').empty().html(data);
              $('#modal_datosfacturas').modal();
            },
            error: function(data) {
              //console.log(data);
            }
        });
    }

    function lista_formas_pago() {
        $.ajax({
            url: "{{route('nuevorecibocobro.formas_pago',['id' => $orden->id])}}",
            type: 'get',
            datatype: 'html',
            success: function(data) {
                //console.log(data);
                $('#detalles_forma_pago').empty().html(data);
            },
            error: function(data) {
                console.log(data);
            }
        })
    }

    function lista_productos() {
        $.ajax({
            url: "{{route('nuevorecibocobro.detalles',['id' => $orden->id])}}",
            type: 'get',
            datatype: 'html',
            success: function(data) {
                //console.log(data);
                $('#detalles_orden').empty().html(data);
            },
            error: function(data) {
                console.log(data);
            }
        })
    }

    function seleccionar_producto() {
        var producto_nuevo = $('#producto_nuevo').val();

        $.ajax({
            type: 'post',
            url: "{{route('nuevorecibocobro.guardar_producto')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'producto_nuevo': producto_nuevo,
                'id': '{{ $orden->id }}'
            },
            success: function(data) {
                lista_productos();
                console.log(data);
            },
            error: function(data) {
                console.log(data);
                alert("No se pudo agregar el producto");
            }
        });

    }

    function seleccionar_metodo() {
        var fp_metodo_nuevo = $('#fp_metodo_nuevo').val();
        var fp_fecha_nueva = $('#fp_fecha_nueva').val();
        var fp_tarjetanueva = $('#fp_tarjetanueva').val();
        var fp_numero_nuevo = $('#fp_numero_nuevo').val();
        var fp_banco = $('#fp_banco').val();
        var fp_cuenta_nueva = $('#fp_cuenta_nueva').val();
        var fp_girado_nuevo = $('#fp_girado_nuevo').val();
        var fp_valor_nuevo = $('#fp_valor_nuevo').val();

        var msj = "";

        if(fp_metodo_nuevo == 2 ){
        
            
            if (fp_numero_nuevo == "") {
                msj += "Por favor,Ingrese Numero<br/>";
            }
            if (fp_banco == "") {
                msj += "Por favor,Ingrese Banco<br/>";
            }
            
            
        }

        if(fp_metodo_nuevo > 2 && fp_metodo_nuevo < 7){
        
            if (fp_tarjetanueva == "") {
                msj += "Por favor,Ingrese la Tarjeta<br/>";
            }
            if (fp_numero_nuevo == "") {
                msj += "Por favor,Ingrese Numero<br/>";
            }
            if (fp_banco == "") {
                msj += "Por favor,Ingrese Banco<br/>";
            }
            if (fp_cuenta_nueva == "") {
                msj += "Por favor,Ingrese la Cuenta<br/>";
            }
            
        }

        if (fp_fecha_nueva == "") {
            msj += "Por favor,Ingrese la Fecha<br/>";
        }
        if (fp_valor_nuevo == "") {
            msj += "Por favor,Ingrese el Valor<br/>";
        }
        if (fp_metodo_nuevo == "") {
            msj += "Por favor,Seleccione el método<br/>";
        }


        if(msj == ''){

            $.ajax({
                type: 'post',
                url: "{{route('nuevorecibocobro.guardar_formapago')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'id': '{{ $orden->id }}',
                    'fp_metodo_nuevo': fp_metodo_nuevo,
                    'fp_fecha_nueva': fp_fecha_nueva,
                    'fp_tarjetanueva': fp_tarjetanueva,
                    'fp_numero_nuevo': fp_numero_nuevo,
                    'fp_banco': fp_banco,
                    'fp_cuenta_nueva': fp_cuenta_nueva,
                    'fp_girado_nuevo': fp_girado_nuevo,
                    'fp_valor_nuevo': fp_valor_nuevo
                },
                success: function(data) {
                    lista_formas_pago();
                    console.log(data);
                },
                error: function(data) {
                    console.log(data);
                    alert("No se pudo agregar el producto");
                }
            });

        }else{

            swal({
                title: "Error!",
                type: "error",
                html: msj
            });
            
        }

            

    }

    function actualizar_valor(id) {


        var cantidad    = $('#cantidad' + id).val();
        var precio      = $('#precio' + id).val();
        var p_cpac      = $('#p_cpac' + id).val();
        var cobrar_paciente = $('#cobrar_paciente' + id).val();
        var p_dcto          = $('#p_dcto' + id).val();
        var descuento       = $('#descuento' + id).val();
        var deducible       = $('#valor_deducible' + id).val();
        //var iva = $('#iva'+id).val();
        var id_producto     = $('#id_producto' + id).val();

        var msj = "";
        
        if (cantidad == "") {
            msj += "Por favor,Ingrese la cantidad<br/>";
        }

        if (precio == "") {
            msj += "Por favor,Ingrese el precio<br/>";
        }

        if (p_cpac == "") {
            msj += "Por favor,Ingrese el porcentaje cobrar paciente<br/>";
        }

        if (cobrar_paciente == "") {
            msj += "Por favor,Ingrese el valor cobrar paciente<br/>";
        }

        if (p_dcto == "") {
            msj += "Por favor,Ingrese el porcentaje descuento<br/>";
        }

        if (descuento == "") {
            msj += "Por favor,Ingrese el valor descuetno<br/>";
        }

        if (deducible == "") {
            msj += "Por favor,Ingrese el valor deducible<br/>";
        }

        if( msj == '' ){

            cantidad = parseFloat(cantidad);
            precio = parseFloat(precio);
            p_cpac = parseFloat(p_cpac);
            cobrar_paciente = parseFloat(cobrar_paciente);
            p_dcto = parseFloat(p_dcto);
            descuento = parseFloat(descuento);
            deducible = parseFloat(deducible);
            //iva = parseFloat(iva);
            $.ajax({
                type: 'post',
                url: "{{route('nuevorecibocobro.actualizar_producto')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'id': id,
                    'id_producto': id_producto,
                    'cantidad': cantidad,
                    'precio': precio,
                    'p_cpac': p_cpac,
                    'cobrar_paciente': cobrar_paciente,
                    'p_dcto': p_dcto,
                    'descuento': descuento,
                    'deducible': deducible
                },
                success: function(data) {
                    lista_productos();
                    console.log(data);
                },
                error: function(data) {
                    console.log(data);
                    alert("No se pudo agregar el producto");
                }
            });

        }else{

            swal({
                title: "Error!",
                type: "error",
                html: msj
            });

        }    

            
    }

    function actualizar_p_cobro(id) {
        var cantidad = $('#cantidad' + id).val();
        var precio = $('#precio' + id).val();
        var cobrar_paciente = $('#cobrar_paciente' + id).val();
        cantidad = parseFloat(cantidad);
        precio = parseFloat(precio);
        cobrar_paciente = parseFloat(cobrar_paciente);
        var neto = cantidad * precio;
        var pct_cobra = cobrar_paciente / neto;
        pct_cobra = pct_cobra * 100;
        pct_cobra = Math.round(pct_cobra * 100) / 100;

        $('#p_cpac' + id).val(pct_cobra);
        actualizar_valor(id);
    }

    function actualizar_p_dcto(id) {
        var cantidad = $('#cantidad' + id).val();
        var precio = $('#precio' + id).val();
        var descuento = $('#descuento' + id).val();
        cantidad = parseFloat(cantidad);
        precio = parseFloat(precio);
        descuento = parseFloat(descuento);
        var neto = cantidad * precio;
        var pct_dcto = descuento / neto;
        pct_dcto = pct_dcto * 100;
        pct_dcto = Math.round(pct_dcto * 100) / 100;

        $('#p_dcto' + id).val(pct_dcto);
        actualizar_valor(id);
    }

    function actualizar_descripcion(id) {
        var descripcion = $('#descripcion' + id).val();
        var id_producto = $('#id_producto' + id).val();
        id_producto = parseFloat(id_producto);
        $.ajax({
            type: 'post',
            url: "{{route('nuevorecibocobro.actualizar_descripcion')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id': id,
                'id_producto': id_producto,
                'descripcion': descripcion,
                'id_producto': id_producto
            },
            success: function(data) {
                lista_productos();
                console.log(data);
            },
            error: function(data) {
                console.log(data);
                alert("No se pudo agregar el producto");
            }
        });
    }

    function eliminar_detalle(id) {
        $.ajax({
            type: 'post',
            url: "{{route('nuevorecibocobro.eliminar_producto')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id': id
            },
            success: function(data) {
                lista_productos();
                console.log(data);
            },
            error: function(data) {
                console.log(data);
                alert("No se pudo agregar el producto");
            }
        });
    }

    function eliminar_forma_pago(id) {
        $.ajax({
            type: 'post',
            url: "{{route('nuevorecibocobro.eliminar_pago')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id': id
            },
            success: function(data) {
                lista_formas_pago();
                console.log(data);
            },
            error: function(data) {
                console.log(data);
                alert("No se pudo agregar el producto");
            }
        });
    }


    function guardar_cabecera() {

        var msn = validar_formulario();

        if( msn == '' ){

            $.ajax({
                type: 'post',
                url: "{{route('nuevorecibocobro.actualizar_cabecera')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#form_actualizar_cabecera").serialize(),
                success: function(data) {
                    //console.log(data);
                    swal({
                        title: "Guardado",
                        type: "success",
                        html: 'Guardado Con Exito'
                    });
                },
                error: function(data) {
                    console.log(data);
                }
            });

        }else{
            swal({
                title: "Error!",
                type: "error",
                html: msn
            });
        }

            

    }

    function cargar_nivel() {
        var id_emp = $('#id_empresa').val();
        var xseguro = $('#id_seguro').val();
        $.ajax({
            type: 'post',
            url: "{{route('lista_nivel.seguro')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_seguro': xseguro,
                'id_empresa': id_emp
            },
            success: function(data) {
                //console.log("vt:");
                //console.log(data);
                if (data.value != 'no') {
                    if (xseguro != 0) {
                        //Muestra el Nivel del Seguro  
                        $("#id_nivel").empty();
                        $.each(data, function(key, registro) {
                            $("#id_nivel").append('<option value=' + registro.id_nivel + '>' + registro.nombre + '</option>');
                        });
                    } else {
                        $("#id_nivel").empty();
                    }
                } else {
                    //Oculta el Nivel del Seguro
                    document.getElementById("ident_nivel").style.visibility = "hidden";
                }
            },
            error: function(data) {}
        });
    }

    function buscar() {
        var cedula_1 = $('#cedula').val();
        var pasaporte = parseInt($('#tipo_identificacion').val());
        var alerta = 0;
        if (cedula_1.length < 10) {
            alert('La Cantidad de Digitos no es Correcta');
        }
        var cedula = validarCedula($('#cedula').val());
        if (cedula == false && pasaporte != 6 && pasaporte != 8) {
            
        } else {
            $.ajax({
                type: 'post',
                url: "{{route('facturacion.cliente')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                type: 'POST',
                datatype: 'json',
                data: $("#cedula"),
                success: function(data) {
                    if (data.value != 'no') {
                        $('#razon_social').val(data.nombre);
                        $('#ciudad').val(data.ciudad);
                        $('#direccion').val(data.direccion);
                        $('#telefono').val(data.telefono);
                        $('#email').val(data.email);
                        //$('#caja').val(data.caja);
                    }
                    console.log(data);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }
    }

    function soloNumeros(e) {
        // capturamos la tecla pulsada
        var teclaPulsada = window.event ? window.event.keyCode : e.which;
        // capturamos el contenido del input
        var valor = e.value;
        if (teclaPulsada == 45 && valor.indexOf("-") == -1) {
            document.getElementById("inputNumero").value = "-" + valor;
        }
        // 13 = tecla enter
        // 46 = tecla punto (.)
        // Si el usuario pulsa la tecla enter o el punto y no hay ningun otro
        // punto
        if (teclaPulsada == 13 || (teclaPulsada == 46 && valor.indexOf(".") == -1)) {
            return true;
        }
        // devolvemos true o false dependiendo de si es numerico o no
        return /\d/.test(String.fromCharCode(teclaPulsada));
    }

    function validar_formulario() {
        
        var formulario = document.forms["form_actualizar_cabecera"];
        //Datos Generales
        
        var ced_cliente = formulario.cedula.value;
        var raz_social = formulario.razon_social.value;
        var ciud_cliente = formulario.ciudad.value;
        var dire_cliente = formulario.direccion.value;
        var telf_cliente = formulario.telefono.value;
        var email_cliente = formulario.email.value;
        var caja_pago = formulario.caja.value;

        var msj = "";
        var msj2 = "";
        
        if (ced_cliente == "") {
            msj += "Por favor,Ingrese la cedula del cliente<br/>";
        }
        if (raz_social == "") {
            msj += "Por favor,Ingrese la razon social<br/>";
        }
        if (ciud_cliente == "") {
            msj += "Por favor,Ingrese la ciudad del cliente<br/>";
        }
        if (dire_cliente == "") {
            msj += "Por favor,Ingrese la direccion cliente<br/>";
        }
        if (telf_cliente == "") {
            msj += "Por favor,Ingrese el telefono del cliente<br/>";
        }
        if (email_cliente == "") {
            msj += "Por favor,Ingrese el email del cliente<br/>";
        }
        
        if (caja_pago == "") {
            msj += "Por favor,Ingrese la caja a la cual se va a pagar<br/>";
        }

        return msj;
        
    }

    function emitir(){

        var msn = validar_formulario();

        if(msn == ''){

            Swal.fire({
                title: 'Al emitir se enviará al Cierre de Caja',
                text: "Esta seguro que desa Emitir?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, Emitir!'
            }).then((result) => {
                if (result.isConfirmed){
                    validar_cuadre();
                    
                }else{
                    
                }
            }) 

        }else{

            swal({
                title: "Error!",
                type: "error",
                html: msn
            });

        }

    }

    function validar_cuadre(){

        $.ajax({
            url: "{{route('nuevorecibocobro.validar_valores',['id' => $orden->id])}}",
            type: 'get',
            datatype: 'html',
            success: function(data) {
                if(data.estado != 'ok'){
                    swal({
                        title: "Error!",
                        type: "error",
                        html: data.mensaje
                    });
                }else{
                    guardar_cabecera();
                    emitir_recibo_cobro();

                }    
            
            },
            error: function(data) {
                console.log(data);
            }
        })

    }

    function emitir_recibo_cobro(){

        $.ajax({
            url: "{{route('nuevorecibocobro.emitir_recibo',['id' => $orden->id])}}",
            type: 'get',
            datatype: 'html',
            success: function(data) {
                swal({
                    title: "Emision Correcta!",
                    type: "success",
                    html: 'Su Recibo de Cobro ya se encuentra en el cierre de caja'
                });
                location.reload();
               
            },
            error: function(data) {
                console.log(data);
            }
        })

    }

    function agregar_deducible( id ){

        $.ajax({
            url: "{{url('nuevo_rc/deducible/crear/item/xseguro')}}/" + id,
            type: 'get',
            datatype: 'html',
            success: function(data) {
                lista_productos();
                
            },
            error: function(data) {
                console.log(data);
            }
        })            

    }

    //Jorge
    function cargar_seguro() {
        var id_emp = $('#id_empresa').val();
        var xtipo = $('#id_seguro_tipos').val();
        alert(xtipo);
        $.ajax({
            type: 'post',
            url: "{{route('lista_seguros.tipos')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_seguro_tipos': xtipo,
                'id_empresa': id_emp
            },
            
            success: function(data) {
                console.log(data);
                if (data.value != 'no') {
                    if (xtipo != 0) {
                        //Muestra el seguro por tipo  
                        $("#id_seguro").empty();
                        $.each(data, function(key, registro) {
                            $("#id_seguro").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
                        });
                    } else {
                        $("#id_seguro").empty();
                    }
                } else {
                    //Oculta el Nivel del Seguro
                    //document.getElementById("ident_nivel").style.visibility = "hidden";
                }
            },
            error: function(data) {}
        });
    }



    //////////////


</script>
@endsection