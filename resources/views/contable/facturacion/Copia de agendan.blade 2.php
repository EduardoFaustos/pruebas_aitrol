@extends('contable.facturacion.base')
@section('action-content')
<style type="text/css">
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
<!-- 
http://192.168.75.125/sis_medico_prb/public/contable/facturacion/agenda/21526 -->
<section class="content">
    <div id="alerta_guardado" class="alert alert-success alerta_guardado alert-dismissable" role="alert"
        style="display:none;">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{trans('contableM.GuardadoCorrectamente')}}
    </div>
    <div id="msj_ingreso" class="alert alert-success alerta_correcto alert-dismissable col-10" role="alert"
        style="display:none;font-size: 14px">
        Ingrese Cedula y Ruc
    </div>
    @php
    $sp= 'Consulta';
    if($tipo=='0'){
    $sp= 'Consulta';
    }else{

    $sp= 'Procedimiento';
    }
    @endphp
    <div class="box box s">
        <div class="header box-header with-border">
            <div class="box-title col-md-12">
                <b style="font-size: 16px;">Crear Recibo de Cobro {{$sp}}</b>
            </div>
        </div>
        @php
        $stringarm="agenda/".$agenda->id."/edit/".$agenda->id_doctor1;

        @endphp
        <div class="box-body">
            <a href="#" target="_blank" id="ride"></a>
            <form class="form-vertical" id="crear_form" method="POST" autocomplete="off">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="valor_totalPagos" id="valor_totalPagos" value="0.00">
                <input type="hidden" name="empresa" id="id_empresa" value="{{$empresa->id}}">
                <input type="hidden" name="id_agenda" id="id_agenda" value="{{$agenda->id}}">
                <input type="hidden" name="id_doctor" id="id_doctor" value="{{$agenda->id_doctor1}}">
                <input type="hidden" name="tipo_dato" value="{{$tipo}}">
                <input type="hidden" value="{{$iva->iva}}" name="ivareal" id="ivareal" class="form-control">
                <div class="col-md-12">
                    <div class="row dobra">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="row">
                                        <label class="col-md-8">Datos Principales</label>
                                        <div class="col-md-2" style="text-align: center;">
                                            <button class="btn btn-primary btn-gray visible" id="printer" type="button"
                                                onclick="fun()"> <i class="fa fa-print"></i> </button>
                                        </div>
                                        <div class="col-md-2" style="text-align: right;">
                                            <button onclick="return location.href='{{url($stringarm)}}'" type="button"
                                                class="btn btn-primary  btn-gray">
                                                <i class="fa fa-arrow-left"></i>
                                            </button>
                                            <button onclick="return window.location.href = window.location.href"
                                                type="button" class="btn btn-primary  btn-gray">
                                                <i class="fa fa-refresh"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <img src="{{asset('/logo').'/'.$empresa->logo}}"
                                                style="width:210px;height:70px">
                                            <div> &nbsp; </div>
                                            <label> &nbsp; </label>
                                            <label>{{$empresa->nombrecomercial}}</label>
                                            <label>{{$empresa->direccion}}</label>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="input-group input-group-sm mb-6">
                                                <label for="numero">Fecha de Procedimiento</label>
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroup-sizing-sm"> <i
                                                            class="fa fa-date"></i> </span>
                                                </div>
                                                <input type="date" name="f_procedimiento"
                                                    value="{{date('Y-m-d',strtotime($agenda->fechaini))}}" readonly
                                                    class="form-control" aria-label="Small"
                                                    aria-describedby="inputGroup-sizing-sm">
                                            </div>
                                            <label for="numero">Fecha de Emision</label>
                                            <div class="input-group input-group-sm mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroup-sizing-sm"> <i
                                                            class="fa fa-date"></i> </span>
                                                </div>
                                                <input type="date" name="f_emision" value="{{date('Y-m-d')}}"
                                                    class="form-control" aria-label="Small"
                                                    aria-describedby="inputGroup-sizing-sm">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Concepto</label>
                                        </div>
                                        <div class="col-md-6">
                                            <textarea class="form-control" name="observacion" id="observacion" cols="3"
                                                rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <label> Datos</label>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="form-group col-md-12"
                                            style="background-color: #3d7ba8; color: white;">
                                            <label> Datos de Paciente</label>
                                            <input type="hidden" id="id_store" value="0">
                                        </div>
                                        <div class="form-group col-md-5 col-xs-12">
                                            <label for="idpaciente" class="col-md-4">Cédula</label>
                                            <div class="input-group  col-md-8">
                                                <input id="idpaciente" maxlength="10" type="text"
                                                    class="form-control input-sm" name="idpaciente"
                                                    value="@if($paciente != Array() && !is_null($paciente)){{$paciente->id}}@elseif($id != 0){{$id}}@else{{old('idpaciente')}}@endif"
                                                    placeholder="Cédula" onchange="verificar_numero_cedula()">
                                                <div class="input-group-addon"
                                                    style="padding-left: 2px;padding-right: 2px;">
                                                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;"
                                                        onclick="document.getElementById('idpaciente').value = '';"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-7 col-xs-12">
                                            <label for="nombres" class="col-md-3">{{trans('contableM.paciente')}}</label>
                                            <div class="input-group col-md-9">
                                                <input type="text" class="form-control input-sm" name="nombres"
                                                    id="nombres"
                                                    value="@if($paciente != Array()){{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif {{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif @endif"
                                                    placeholder="Apellidos y Nombres" style="text-transform:uppercase;"
                                                    onkeyup="javascript:this.value=this.value.toUpperCase();">
                                                <div class="input-group-addon"
                                                    style="padding-left: 2px;padding-right: 2px;">
                                                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;"
                                                        onclick="document.getElementById('nombres').value = '';"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-5 col-xs-12">
                                            <label for="id_seguro" class="col-md-4 control-label">{{trans('contableM.Seguro')}}</label>
                                            <div class="input-group col-md-8">
                                                <select class="form-control input-sm" name="id_seguro" id="id_seguro"
                                                    required onchange="seguro(),cargar_nivel()">
                                                    <option value="">Seleccione ...</option>
                                                    @foreach($seguros as $seguro)
                                                    <option @if($agenda->id_seguro == $seguro->id) selected @endif
                                                        @if(old('id_seguro')==$seguro->id) selected @endif
                                                        value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div id="ident_nivel" class="col-md-3 col-xs-5">
                                            <label for="id_nivel" class="col-md-3 control-label">Nivel</label>
                                            <div class="input-group col-md-9">
                                                <select name="id_nivel" id="id_nivel" class="form-control input-sm">
                                                    <option value="">Seleccione...</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-4 col-xs-12">
                                            <div class="col-md-7">
                                                <label>¿Posee ODA?</label>
                                                <input type="radio" name="interesado" value="si"
                                                    id="interesadoPositivo"> Sí
                                                <input type="radio" name="interesado" value="no"
                                                    id="interesadoNegativo"> No
                                            </div>

                                            <div class="col-md-5">
                                                <input type="text" name="oda" class="form-control input-sm"
                                                    placeholder="# ODA" id="oda">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            &nbsp;
                                        </div>
                                        <div class="form-group col-md-12"
                                            style="background-color: #d4850e; color: white;">
                                            <label> Datos de cliente</label>
                                        </div>
                                        <div class="form-group col-md-6 col-xs-12">
                                            <label for="cedula" class="col-md-5 control-label">{{trans('contableM.identificacion')}}</label>
                                            <div class="input-group col-md-7">
                                                <select id="tipo_identificacion" name="tipo_identificacion"
                                                    class="form-control input-sm" required autofocus="">
                                                    <option @if(!is_null($ct_cliente) && $ct_cliente!=Array())
                                                        @if($ct_cliente->tipo=='4') selected='selected' @endif @endif
                                                        value="4">{{trans('contableM.ruc')}}</option>
                                                    <option @if(!is_null($ct_cliente) && $ct_cliente!=Array())
                                                        @if($ct_cliente->tipo=='5' || $ct_cliente->tipo=='05')
                                                        selected='selected' @endif @endif value="5">{{trans('contableM.cedula')}}</option>
                                                    <option @if(!is_null($ct_cliente) && $ct_cliente!=Array())
                                                        @if($ct_cliente->tipo=='6') selected='selected' @endif @endif
                                                        value="6">{{trans('contableM.pasaporte')}}</option>
                                                    <option @if(!is_null($ct_cliente) && $ct_cliente!=Array())
                                                        @if($ct_cliente->tipo=='8') selected='selected' @endif @endif
                                                        value="8">CEDULA EXTRANJERA</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!--Ruc/Cedula-->
                                        <div class="form-group col-md-6 col-xs-12">
                                            <label for="cedula" class="col-md-5 control-label">Ruc/Cédula</label>
                                            <div class="input-group col-md-7">
                                                <input id="cedula" maxlength="13" type="text" class="form-control"
                                                    name="cedula"
                                                    value="@if($ct_cliente != Array() && !is_null($ct_cliente)){{$ct_cliente->identificacion}}@else{{old('cedula')}}@endif"
                                                    placeholder="Ruc/Cédula" style="text-transform:uppercase;"
                                                    onkeyup="javascript:this.value=this.value.toUpperCase();"
                                                    onchange="buscar();" required>
                                                <div class="input-group-addon"
                                                    style="padding-left: 2px;padding-right: 2px;">
                                                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;"
                                                        onclick="document.getElementById('cedula').value = '';"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!--Razon Social-->
                                        <div class="form-group col-md-6 col-xs-12">
                                            <label for="razon_social" class="col-md-5 control-label">Razon
                                                Social</label>
                                            <div class="input-group col-md-7">
                                                <input type="text" class="form-control input-sm" name="razon_social"
                                                    id="razon_social"
                                                    value="@if(!is_null($ct_cliente)){{$ct_cliente->nombre}}@endif"
                                                    placeholder="Razon Social" style="text-transform:uppercase;"
                                                    onkeyup="javascript:this.value=this.value.toUpperCase();" required>
                                                <div class="input-group-addon"
                                                    style="padding-left: 2px;padding-right: 2px;">
                                                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;"
                                                        onclick="document.getElementById('razon_social').value = '';"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!--Ciudad-->
                                        <div class="form-group col-md-6 col-xs-12">
                                            <label for="ciudad" class="col-md-5 control-label">{{trans('contableM.ciudad')}}</label>
                                            <div class="input-group col-md-7">
                                                <input type="text" class="form-control input-sm" name="ciudad"
                                                    id="ciudad"
                                                    value="@if(!is_null($ct_cliente)){{$ct_cliente->ciudad_representante}}@endif"
                                                    placeholder="Ciudad" style="text-transform:uppercase;"
                                                    onkeyup="javascript:this.value=this.value.toUpperCase();">
                                                <div class="input-group-addon"
                                                    style="padding-left: 2px;padding-right: 2px;">
                                                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;"
                                                        onclick="document.getElementById('ciudad').value = '';"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!--Direccion-->
                                        <div class="form-group col-md-66 col-xs-6">
                                            <label for="direccion" class="col-md-5 control-label">{{trans('contableM.direccion')}}</label>
                                            <div class="input-group col-md-7">
                                                <input type="text" class="form-control input-sm" name="direccion"
                                                    id="direccion"
                                                    value="@if(!is_null($ct_cliente)){{$ct_cliente->direccion_representante}}@endif"
                                                    placeholder="Dirección" style="text-transform:uppercase;"
                                                    onkeyup="javascript:this.value=this.value.toUpperCase();">
                                                <div class="input-group-addon"
                                                    style="padding-left: 2px;padding-right: 2px;">
                                                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;"
                                                        onclick="document.getElementById('direccion').value = '';"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!--Telefono-->
                                        <div class="form-group col-md-6 col-xs-12">
                                            <label for="telefono" class="col-md-5 control-label">Teléfono</label>
                                            <div class="input-group col-md-7">
                                                <input type="text" class="form-control input-sm" name="telefono"
                                                    id="telefono"
                                                    value="@if(!is_null($ct_cliente)){{$ct_cliente->telefono1_representante}}@endif"
                                                    placeholder="Teléfono" style="text-transform:uppercase;"
                                                    onkeyup="javascript:this.value=this.value.toUpperCase();">
                                                <div class="input-group-addon"
                                                    style="padding-left: 2px;padding-right: 2px;">
                                                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;"
                                                        onclick="document.getElementById('telefono').value = '';"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!--Email-->
                                        <div class="form-group col-md-6 col-xs-12">
                                            <label for="email" class="col-md-5 control-label">Mail</label>
                                            <div class="input-group col-md-7">
                                                <input type="text" class="form-control input-sm" name="email" id="email"
                                                    value="@if(!is_null($ct_cliente)){{$ct_cliente->email_representante}}@endif"
                                                    placeholder="Mail">
                                                <div class="input-group-addon"
                                                    style="padding-left: 2px;padding-right: 2px;">
                                                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;"
                                                        onclick="document.getElementById('email').value = '';"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--<div class="col-md-12" style="background-color: #028484;height: 10px;">
                        </div> -->
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-body" style="padding:0;">

                                    <table id="example2" class="table table-bordered table-hover dataTable" role="grid"
                                        aria-describedby="example2_info">
                                        <thead>
                                            <tr class='well' style="color: black;">
                                                <th width="35%" tabindex="0">{{trans('contableM.DescripciondelProducto')}}</th>
                                                <th width="10%" tabindex="0">{{trans('contableM.cantidad')}}</th>
                                                <th width="10%" tabindex="0">{{trans('contableM.precio')}}</th>
                                                <th width="10%" tabindex="0">% {{trans('contableM.prctdesc')}}</th>
                                                <th width="10%" tabindex="0">Desc.</th>
                                                <th width="10%" tabindex="0">{{trans('contableM.total')}}</th>
                                                <th width="5%" tabindex="0" bgcolor="#cfcfcf">% SEG</th>
                                                <th width="5%" tabindex="0" bgcolor="#cfcfcf">% PAC</th>
                                                <th width="5%" tabindex="0" bgcolor="#cfcfcf">DEDU</th>
                                                <th width="5%" tabindex="0" bgcolor="#cfcfcf">FEE</th>
                                                <th width="10%" tabindex="0">Cobrar Paciente</th>
                                                <th width="10%" tabindex="0">{{trans('contableM.cobrarseguro')}}</th>
                                                <th width="5%" tabindex="0">{{trans('contableM.iva')}}</th>
                                                <th width="10%" tabindex="0">
                                                    <button onclick="nuevoa()" type="button"
                                                        class="btn btn-success btn-gray">
                                                        <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                                    </button>
                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody id="agregar_cuentas">
                                            <tr>
                                                <td style="max-width:100px;">
                                                    @if($tipo==0)
                                                    <input type="hidden" name="codigo[]" class="codigo_producto" />
                                                    @else
                                                    <input type="hidden" name="codigo[]" class="codigo_producto"
                                                        value="CONSULTA" />
                                                    @endif
                                                    <select name="nombre[]" class="form-control select2"
                                                        style="width:90%" required onchange="verificar(this)">
                                                        <option> </option>
                                                        @foreach($productos as $value)
                                                        <option @if($tipo==0) @if($value->codigo=='CONSULTA')
                                                            selected='selected' @endif @endif value="{{$value->codigo}}"
                                                            data-name="{{$value->nombre}}"
                                                            data-codigo="{{$value->codigo}}"
                                                            data-descuento="{{$value->mod_desc}}"
                                                            data-precio="{{$value->mod_precio}}"
                                                            data-maxdesc="{{$value->descuento}}"
                                                            data-iva="{{$value->iva}}">{{$value->codigo}} |
                                                            {{$value->descripcion}}</option>
                                                        @endforeach
                                                    </select>
                                                    <textarea wrap="hard" rows="3" name="descrip_prod[]"
                                                        class="form-control px-1 desc_producto"
                                                        placeholder="Detalle del producto"></textarea>
                                                    <input type="hidden" class="precioOriginal" value="0">
                                                    <input type="hidden" name="iva[]" class="iva" />
                                                </td>
                                                <td>
                                                    <input class="form-control text-right cneto" type="text"
                                                        style="width: 50px;height:20px;"
                                                        onkeypress="return isNumberKey(event)"
                                                        onblur="this.value=parseFloat(this.value).toFixed(0);" value="1"
                                                        name="cantidad[]" required>
                                                </td>
                                                <td>
                                                    <input class="form-control text-right pneto" type="text"
                                                        style="width: 80%;height:20px;"
                                                        onkeypress="return isNumberKey(event)"
                                                        onblur="this.value=parseFloat(this.value).toFixed(2);"
                                                        name="precio[]" value="0.00" required>
                                                </td>
                                                <td>
                                                    <input class="form-control text-right pdesc" type="text"
                                                        style="width: 50px;height:20px;"
                                                        onkeypress="return isNumberKey(event)"
                                                        onblur="this.value=parseFloat(this.value).toFixed(0);" value="0"
                                                        name="descpor[]" required>
                                                    <input class="form-control text-right maxdesc" type="hidden"
                                                        style="width: 80%;height:20px;"
                                                        onkeypress="return isNumberKey(event)"
                                                        onblur="this.value=parseFloat(this.value).toFixed(0);" value="0"
                                                        name="maxdesc[]" required>
                                                </td>
                                                <td>
                                                    <input class="form-control text-right desc" type="text"
                                                        style="width: 50px;height:20px;"
                                                        onkeypress="return isNumberKey(event)"
                                                        onblur="this.value=parseFloat(this.value).toFixed(2);" value="0"
                                                        name="desc[]" required>
                                                </td>
                                                <td>
                                                    <input class="form-control px-1 text-right tneto" type="text"
                                                        style="height:20px;" onkeypress="return isNumberKey(event)"
                                                        value="0.00"
                                                        onblur="this.value=parseFloat(this.value).toFixed(2);"
                                                        name="precioneto[]" readonly="readonly" required>
                                                </td>
                                                <td>
                                                    <input class="form-control px-1 text-right psegu" type="text"
                                                        style="height:20px;" onkeypress="return isNumberKey(event)"
                                                        value="80.00"
                                                        onblur="this.value=parseFloat(this.value).toFixed(2);"
                                                        name="porc_dedu_segu[]" bgcolor="#cfcfcf" readonly="readonly" required>
                                                </td>
                                                </td>
                                                <td>
                                                    <input class="form-control px-1 text-right ppaci" type="text"
                                                        style="height:20px;" onkeypress="return isNumberKey(event)"
                                                        value="20.00"
                                                        onblur="this.value=parseFloat(this.value).toFixed(2);"
                                                        name="porc_dedu_paci[]" bgcolor="#cfcfcf" required>
                                                </td>
                                                <td>
                                                    <input class="form-control px-1 text-right pdeducible" type="text"
                                                        style="height:20px;" onkeypress="return isNumberKey(event)"
                                                        value="0.00"
                                                        onblur="this.value=parseFloat(this.value).toFixed(2);"
                                                        name="deducible[]" bgcolor="#cfcfcf" required>
                                                </td>
                                                <td>
                                                    <input class="form-control px-1 text-right pfee" type="text"
                                                        style="height:20px;" onkeypress="return isNumberKey(event)"
                                                        value="0.00"
                                                        onblur="this.value=parseFloat(this.value).toFixed(2);"
                                                        name="fee[]" bgcolor="#cfcfcf" required>
                                                </td>
                                                <td>
                                                    <input class="form-control text-right copago" type="text"
                                                        style="width: 80%;height:20px;"
                                                        onkeypress="return isNumberKey(event)"
                                                        onblur="this.value=parseFloat(this.value).toFixed(2);" value="0"
                                                        name="copago[]" readonly="readonly" required>
                                                    <input class="copaged" type="hidden" name="copaged[]" value="0.00">
                                                </td>
                                                <td>
                                                    <input class="form-control text-right" type="text"
                                                        style="width: 80%;height:20px;"
                                                        onkeypress="return isNumberKey(event)"
                                                        onblur="this.value=parseFloat(this.value).toFixed(2);" value="0"
                                                        name="cobrar_seguro[]" readonly="readonly" required>
                                                </td>
                                                <td>
                                                    <input class="form chx" type="checkbox"
                                                        style="width: 80%;height:20px;" value="0" name="valoriva[]">
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-xs btn-gray delete">
                                                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-xs btn-gray showme"
                                                        disabled>
                                                        <i class="glyphicon glyphicon-info-sign" aria-hidden="true"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table id="example" class="table table-bordered table-hover dataTable" role="grid"
                                        aria-describedby="example2_info">
                                        <thead>
                                            <tr style="display: none;">

                                                <th width="35%" tabindex="0"></th>
                                                <th width="10%" tabindex="0"></th>
                                                <th width="10%" tabindex="0"></th>
                                                <th width="10%" tabindex="0"></th>
                                                <th width="10%" tabindex="0"></th>
                                                <th width="10%" tabindex="0"></th>
                                                <th width="10%" tabindex="0"></th>
                                                <th width="5%" tabindex="0"></th>
                                                <th width="10%" tabindex="0">

                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <input type="hidden" name="ti_factura" value="{{$tipo}}" />
                                        </tbody>
                                        <tfoot class='well'>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td colspan="2" class="text-right">{{trans('contableM.subtotal12')}}%</td>
                                                <td id="subtotal_12" class="text-right px-1">0.00</td>
                                                <input type="hidden" name="subtotal_121" id="subtotal_121" value="0.00"
                                                    class="hidden">
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td colspan="2" class="text-right">{{trans('contableM.subtotal0')}}%</td>
                                                <td id="subtotal_0" class="text-right px-1">0.00</td>
                                                <input type="hidden" name="subtotal_01" id="subtotal_01" value="0.00"
                                                    class="hidden">
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td colspan="2" class="text-right">{{trans('contableM.descuento')}}</td>
                                                <td id="descuento" class="text-right px-1">0.00</td>
                                                <input type="hidden" name="descuento1" id="descuento1" value="0.00"
                                                    class="hidden">
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td colspan="2" class="text-right">{{trans('contableM.SubtotalsinImpuesto')}}</td>
                                                <td id="base" class="text-right px-1">0.00</td>
                                                <input type="hidden" name="base1" id="base1" value="0.00"
                                                    class="hidden">
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td colspan="2" class="text-right">{{trans('contableM.tarifaiva')}}</td>
                                                <td id="tarifa_iva" class="text-right px-1">0.00</td>
                                                <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" value="0.00"
                                                    class="hidden">
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td colspan="2" class="text-right"><strong>{{trans('contableM.total')}}</strong></td>
                                                <td id="total" class="text-right px-1">0.00</td>
                                                <input type="hidden" name="total1" id="total1" value="0.00"
                                                    class="hidden">
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td colspan="2" class="text-right"><strong>{{trans('contableM.PorCobrarSeguro')}}</strong>
                                                </td>
                                                <td id="copagoTotal" class="text-right px-1">0.00</td>
                                                <input type="hidden" name="totalc" id="totalc" value="0.00"
                                                    class="hidden">
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="pago" class="col-md-6 control-label">Caja de Cobro:</label>
                            <div class="input-group col-md-6">
                                <select id="caja" name="caja" class="form-control" required>

                                    <option value="">Seleccione...</option>
                                    <option value="Torre 1">Torre 1</option>
                                    <option value="Torre 2">Torre 2</option>
                                    <option value="Pentax">PENTAX</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 table-responsive ">

                            <div class="col-md-12">
                                <label> {{trans('contableM.formasdepago')}}</label>

                                <button id="btn_pago" type="button" class="btn btn-success btn-xs btn-gray"
                                    style="margin-left: 10px;">
                                    <i class="glyphicon glyphicon-plus" aria-hidden="true"></i></button>
                            </div>
                            <input name="contador_pago" id="contador_pago" type="hidden" value="0">
                            <table id="example1" role="grid" class="table table-hover dataTable"
                                aria-describedby="example1_info" style="margin-top:0 !important">

                                <thead>
                                    <tr>
                                        <th style="text-align: center;">{{trans('contableM.Metodo')}}</th>
                                        <th style="text-align: center;">{{trans('contableM.fecha')}}</th>
                                        <th style="text-align: center;">{{trans('contableM.tipo')}}</th>
                                        <th style="text-align: center;">{{trans('contableM.numero')}}</th>
                                        <th style="text-align: center;">{{trans('contableM.banco')}}</th>
                                        <th style="text-align: center;">Posee Fi</th>
                                        <th style="text-align: center;">{{trans('contableM.Cuenta')}}</th>
                                        <th style="text-align: center;">Girado</th>
                                        <th style="text-align: center;">{{trans('contableM.valor')}}</th>
                                        <th style="text-align: center;">{{trans('contableM.ValorB')}}</th>
                                        <th style="text-align: center;">

                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="agregar_pago">
                                    <tr style="display:none" id="mifila">

                                        <td style="max-width:100px;">
                                            <input type="hidden" name="codigo[]" class="codigo_producto" />
                                            <select name="nombre[]" class="form-control select2" style="width:90%;"
                                                required onchange="verificar(this)">
                                                <option> </option>
                                                @foreach($productos as $value)
                                                <option value="{{$value->nombre}}" data-name="{{$value->nombre}}"
                                                    data-codigo="{{$value->codigo}}"
                                                    data-descuento="{{$value->mod_desc}}"
                                                    data-precio="{{$value->mod_precio}}"
                                                    data-maxdesc="{{$value->descuento}}" data-iva="{{$value->iva}}">
                                                    {{$value->codigo}} | {{$value->descripcion}}</option>
                                                @endforeach

                                            </select>
                                            <textarea wrap="hard" rows="3" name="descrip_prod[]"
                                                class="form-control px-1 desc_producto"
                                                placeholder="Detalle del producto"></textarea>
                                            <input type="hidden" name="iva[]" class="iva" />
                                        </td>
                                        <td>
                                            <input class="form-control text-right cneto" type="text"
                                                style="width: 50px;height:20px;" onkeypress="return isNumberKey(event)"
                                                onblur="this.value=parseFloat(this.value).toFixed(0);" value="0"
                                                name="cantidad[]" required>
                                        </td>
                                        <td>
                                            <input class="form-control text-right pneto" type="text"
                                                style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)"
                                                onblur="this.value=parseFloat(this.value).toFixed(2);" value="0"
                                                name="precio[]" required>
                                            <!--   <select name="precio[]" class="form-control select2_precio pneto"
                                                                style="width:60%;height:20px;display:inline;" required>
                                                                <option value="0"> </option>
                                                            </select>
                                                            <button type="button" class="btn btn-info btn-gray boton_desh btn-xs cp">
                                                                <i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>
                                                            </button> --> 
                                        </td>
                                        <td>
                                            <input class="form-control text-right pdesc" type="text"
                                                style="width: 50px;height:20px;" onkeypress="return isNumberKey(event)"
                                                onblur="this.value=parseFloat(this.value).toFixed(0);" value="0"
                                                name="descpor[]" required>
                                            <input class="form-control text-right maxdesc" type="hidden"
                                                style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)"
                                                onblur="this.value=parseFloat(this.value).toFixed(0);" value="0"
                                                name="maxdesc[]" required>
                                        </td>
                                        <td>
                                            <input class="form-control text-right desc" type="text"
                                                style="width: 50px;height:20px;" onkeypress="return isNumberKey(event)"
                                                onblur="this.value=parseFloat(this.value).toFixed(2);" value="0"
                                                name="desc[]" required>
                                        </td>
                                        <td>
                                            <input class="form-control px-1 text-right tneto" type="text"
                                                style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00"
                                                onblur="this.value=parseFloat(this.value).toFixed(2);"
                                                name="precioneto[]" readonly="readonly" required>
                                        </td>
                                        <td>
                                            <input class="form-control px-1 text-right psegu" type="text"
                                                style="height:20px;" onkeypress="return isNumberKey(event)"
                                                value="80.00" onblur="this.value=parseFloat(this.value).toFixed(2);"
                                                name="porc_dedu_segu[]" readonly="readonly" bgcolor="#cfcfcf" required>
                                        </td>
                                        </td>
                                        <td>
                                            <input class="form-control px-1 text-right ppaci" type="text"
                                                style="height:20px;" onkeypress="return isNumberKey(event)"
                                                value="20.00" onblur="this.value=parseFloat(this.value).toFixed(2);"
                                                name="porc_dedu_paci[]" bgcolor="#cfcfcf" required>
                                        </td>
                                        <td>
                                            <input class="form-control px-1 text-right pdeducible" type="text"
                                                style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00"
                                                onblur="this.value=parseFloat(this.value).toFixed(2);"
                                                name="deducible[]" bgcolor="#cfcfcf" required>
                                        </td>
                                        <td>
                                            <input class="form-control px-1 text-right pfee" type="text"
                                                style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00"
                                                onblur="this.value=parseFloat(this.value).toFixed(2);" name="fee[]"
                                                bgcolor="#cfcfcf" required>
                                        </td>
                                        {{-- <td>
                                                            <input class="form-control px-1 text-right" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="deducible[]" required>
                                                        </td> --}}
                                        <td>
                                            <input class="form-control text-right copago" type="text"
                                                style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)"
                                                onblur="this.value=parseFloat(this.value).toFixed(2);" value="0"
                                                name="copago[]" readonly="readonly" required>
                                            <input class="copaged" type="hidden" name="copaged[]" value="0.00">
                                            {{-- <button type="button" class="btn btn-info btn-gray btn-xs cp" disabled>
                                                                <i class="fa fa-percent" aria-hidden="true"></i>
                                                            </button> --}}
                                        </td>
                                        <td>
                                            <input class="form-control text-right copago" type="text"
                                                style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)"
                                                onblur="this.value=parseFloat(this.value).toFixed(2);" value="0"
                                                name="cobrar_seguro[]" readonly="readonly" required>
                                        </td>
                                        <td>
                                            <input class="form chx" type="checkbox" style="width: 80%;height:20px;"
                                                value="0" name="valoriva[]">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-gray btn-xs delete">
                                                <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-xs btn-gray showme"
                                                disabled="disabled">
                                                <i class="glyphicon glyphicon-info-sign" aria-hidden="true"></i>
                                            </button>

                                        </td>
                                    </tr>
                                    <tr style="display:none" id="mifilaf"> 
                                        <td style="max-width:100px;">
                                            <input type="hidden" name="codigo[]" class="codigo_producto"
                                                value="" />
                                            <input class="form-control day inpu-sm" style="width: 80%;height:20px;"
                                                type="text" name="nombre[]" value="">
                                            <textarea wrap="hard" rows="3" name="descrip_prod[]"
                                                class="form-control px-1 desc_producto input-sm"
                                                placeholder="Detalle del producto"></textarea>
                                            <input type="hidden" name="iva[]" class="iva" />
                                        </td>
                                        <td>
                                            <input class="form-control text-right cnetox cneto input-sm" type="text"
                                                style="width: 50px;height:20px;" onkeypress="return isNumberKey(event)"
                                                onblur="this.value=parseFloat(this.value).toFixed(0);" value="0"
                                                name="cantidad[]" required>
                                        </td>
                                        <td>
                                            <input style="width: 80%; height: 20px;"
                                                class="pnetox form-control input-sm text-right"
                                                onblur="this.value=parseFloat(this.value).toFixed(3);" name="precio[]"
                                                type="text" value="0.00">
                                        </td>
                                        <td>
                                            <input class="form-control text-right copago input-sm" type="text"
                                                style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)"
                                                onblur="this.value=parseFloat(this.value).toFixed(2);" value="0"
                                                name="copago[]" readonly>
                                            <input class="copaged" type="hidden" name="copaged[]" value="0.00">
                                        </td>
                                        <td>
                                            <input class="form-control text-right pdesc input-sm" type="text"
                                                style="width: 50px;height:20px;" onkeypress="return isNumberKey(event)"
                                                onblur="this.value=parseFloat(this.value).toFixed(0);" value="0"
                                                name="descpor[]" required readonly>
                                            <input class="form-control text-right maxdesc" type="hidden"
                                                style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)"
                                                onblur="this.value=parseFloat(this.value).toFixed(0);" value="0"
                                                name="maxdesc[]" readonly>
                                        </td>
                                        <td>
                                            <input class="form-control text-right desc input-sm" type="text"
                                                style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)"
                                                onblur="this.value=parseFloat(this.value).toFixed(2);" value="0"
                                                name="desc[]" readonly>
                                        </td>
                                        <td>
                                            <input class="form-control px-1 text-right input-sm" type="text"
                                                style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00"
                                                onblur="this.value=parseFloat(this.value).toFixed(2);"
                                                name="precioneto[]" readonly>
                                        </td>
                                        <td>
                                            <input class="form chx" type="checkbox" style="width: 80%;height:20px;"
                                                value="0" name="valoriva[]" disabled>

                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-xs btn-gray delete">
                                                <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                            </button>

                                        </td>
                                    </tr>
                                </tbody>
                            </table>


                        </div>
                        <div class="col-md-12" style="text-align:center; margin-top:10px;">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Valor Total: </label>
                                    <input class="form-control" type="text" id="valorTotals" value="0.00" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label>Valor Pagado: </label>
                                    <input class="form-control" type="text" id="sobrante"
                                        onchange="changeSobrante(this)" placeholder="$">
                                </div>
                                <div class="col-md-4">
                                    <label>Diferencia: </label>
                                    <input class="form-control" type="text" id="diferencia" readonly value="0.00">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="text-align: center; margin-top: 10px;">
                            <button class="btn btn-success btn-gray saves" type="button" onclick="guardar(this)"> <i
                                    class="fa fa-save"></i> </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

<script src="{{ asset ("/hospital/cleave/dist/cleave.min.js")}}"></script>
{{-- @include('contable.facturacion.partial') --}}
@include('contable.facturacion.partial1')

@endsection