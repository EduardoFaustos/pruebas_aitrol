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

    .bodega{
        display: none;
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
    .s{
        font-size: 17px!important;
    }
    .visible{
        display: none;
    }
</style>
<!-- 
http://192.168.75.125/sis_medico_prb/public/contable/facturacion/agenda/21526 -->
<section class="content">
    <div id="alerta_guardado" class="alert alert-success alerta_guardado alert-dismissable" role="alert" style="display:none;">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{trans('contableM.GuardadoCorrectamente')}}
    </div>
    <div id="msj_ingreso" class="alert alert-success alerta_correcto alert-dismissable col-10" role="alert" style="display:none;font-size: 14px">
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
                                            <button class="btn btn-primary btn-gray visible" id="printer" type="button" onclick="fun()"> <i class="fa fa-print"></i> </button>
                                        </div>
                                        <div class="col-md-2" style="text-align: right;">
                                        <button onclick="return location.href='{{url($stringarm)}}'" type="button" class="btn btn-primary  btn-gray">
                                                <i class="fa fa-arrow-left"></i>
                                            </button>
                                            <button onclick="return window.location.href = window.location.href" type="button" class="btn btn-primary  btn-gray">
                                                <i class="fa fa-refresh"></i>
                                            </button>
                                        </div>

                                    </div>

                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <img src="{{asset('/logo').'/'.$empresa->logo}}" style="width:210px;height:70px">
                                            <div> &nbsp; </div>
                                            <label> &nbsp; </label>
                                            <label>{{$empresa->nombrecomercial}}</label>
                                            <label>{{$empresa->direccion}}</label>

                                        </div>
                                        <div class="col-md-2">

                                            <div class="input-group input-group-sm mb-6">
                                                <label for="numero">Fecha de Procedimiento</label>
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroup-sizing-sm"> <i class="fa fa-date"></i> </span>
                                                </div>
                                                <input type="date" name="f_procedimiento" value="{{date('Y-m-d',strtotime($agenda->fechaini))}}" readonly class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm">
                                            </div>
                                            <label for="numero">Fecha de Emision</label>
                                            <div class="input-group input-group-sm mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroup-sizing-sm"> <i class="fa fa-date"></i> </span>
                                                </div>
                                                <input type="date" name="f_emision" value="{{date('Y-m-d')}}" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm">
                                            </div>
                                        </div>

                                         <div class="col-md-6">
                                            <label>Concepto</label>
                                        </div>
                                        <div class="col-md-6">
                                            <textarea class="form-control" name="observacion" id="observacion" cols="3" rows="3"></textarea>
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
                                        <div class="form-group col-md-12" style="background-color: #3d7ba8; color: white;">
                                            <label> Datos de Paciente</label>
                                            <input type="hidden" id="id_store" value="0">
                                        </div>
                                        <div class="form-group col-md-5 col-xs-12">
                                            <label for="idpaciente" class="col-md-4">C??dula</label>
                                            <div class="input-group  col-md-8">
                                                <input id="idpaciente" maxlength="10" type="text" class="form-control input-sm" name="idpaciente" value="@if($paciente != Array() && !is_null($paciente)){{$paciente->id}}@elseif($id != 0){{$id}}@else{{old('idpaciente')}}@endif" placeholder="C??dula" onchange="verificar_numero_cedula()">
                                                <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('idpaciente').value = '';"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-7 col-xs-12">
                                            <label for="nombres" class="col-md-3">{{trans('contableM.paciente')}}</label>
                                            <div class="input-group col-md-9">
                                                <input type="text" class="form-control input-sm" name="nombres" id="nombres" value="@if($paciente != Array()){{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif {{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif @endif" placeholder="Apellidos y Nombres" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                                <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('nombres').value = '';"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-5 col-xs-12">
                                            <label for="id_seguro" class="col-md-4 control-label">{{trans('contableM.Seguro')}}</label>
                                            <div class="input-group col-md-8">
                                                <select class="form-control input-sm" name="id_seguro" id="id_seguro" required onchange="seguro(),cargar_nivel()">
                                                    <option value="">Seleccione ...</option>
                                                    @foreach($seguros as $seguro)
                                                    <option @if($agenda->id_seguro == $seguro->id) selected @endif @if(old('id_seguro')==$seguro->id) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
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
                                                <label>??Posee ODA?</label>
                                                <input type="radio" name="interesado" value="si"  id="interesadoPositivo"> S??
                                                <input type="radio" name="interesado" value="no" id="interesadoNegativo" > No
                                            </div>

                                            <div class="col-md-5">
                                                <input type="text" name="oda" class="form-control input-sm"  placeholder="# ODA" id="oda">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            &nbsp;
                                        </div>
                                        <div class="form-group col-md-12" style="background-color: #d4850e; color: white;">
                                            <label> Datos de cliente</label>
                                        </div>
                                        <div class="form-group col-md-6 col-xs-12">
                                            <label for="cedula" class="col-md-5 control-label">{{trans('contableM.identificacion')}}</label>
                                            <div class="input-group col-md-7">
                                                <select id="tipo_identificacion" name="tipo_identificacion" onchange="borrar()" class="form-control input-sm" required autofocus="">
                                                    <option @if(!is_null($ct_cliente) && $ct_cliente!=Array()) @if($ct_cliente->tipo=='4') selected='selected' @endif @endif value="4">{{trans('contableM.ruc')}}</option>
                                                    <option  @if(!is_null($ct_cliente) && $ct_cliente!=Array()) @if($ct_cliente->tipo=='5' || $ct_cliente->tipo=='05') selected='selected' @endif @endif value="5">{{trans('contableM.cedula')}}</option>
                                                    <option @if(!is_null($ct_cliente) && $ct_cliente!=Array()) @if($ct_cliente->tipo=='6') selected='selected' @endif @endif  value="6">{{trans('contableM.pasaporte')}}</option>
                                                    <option @if(!is_null($ct_cliente) && $ct_cliente!=Array()) @if($ct_cliente->tipo=='8') selected='selected' @endif @endif value="8">CEDULA EXTRANJERA</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!--Ruc/Cedula-->
                                        <div class="form-group col-md-6 col-xs-12">
                                            <label for="cedula" class="col-md-5 control-label">Ruc/C??dula</label>
                                            <div class="input-group col-md-7">
                                                <input id="cedula" maxlength="13" type="text" class="form-control" name="cedula" value="@if($ct_cliente != Array() && !is_null($ct_cliente)){{$ct_cliente->identificacion}}@else{{old('cedula')}}@endif" placeholder="Ruc/C??dula" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onchange="buscar();" required>
                                                <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('cedula').value = '';"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!--Razon Social-->
                                        <div class="form-group col-md-6 col-xs-12">
                                            <label for="razon_social" class="col-md-5 control-label">Razon Social</label>
                                            <div class="input-group col-md-7">
                                                <input type="text" class="form-control input-sm" name="razon_social" id="razon_social" value="@if(!is_null($ct_cliente)){{$ct_cliente->nombre}}@endif" placeholder="Razon Social" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required> 
                                                <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('razon_social').value = '';"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!--Ciudad-->
                                        
                                        <div class="form-group col-md-6 col-xs-12">
                                            <label for="ciudad" class="col-md-5 control-label">{{trans('contableM.ciudad')}}</label>
                                            <div class="input-group col-md-7">
                                                <input type="text" class="form-control input-sm" name="ciudad" id="ciudad" value="@if(!is_null($ct_cliente)){{$ct_cliente->ciudad_representante}}@endif" placeholder="Ciudad" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                                <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('ciudad').value = '';"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!--Direccion-->
                                        <div class="form-group col-md-66 col-xs-6">
                                            <label for="direccion" class="col-md-5 control-label">{{trans('contableM.direccion')}}</label>
                                            <div class="input-group col-md-7">
                                                <input type="text" class="form-control input-sm" name="direccion" id="direccion" value="@if(!is_null($ct_cliente)){{$ct_cliente->direccion_representante}}@endif" placeholder="Direcci??n" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                                <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('direccion').value = '';"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!--Telefono-->
                                        <div class="form-group col-md-6 col-xs-12">
                                            <label for="telefono" class="col-md-5 control-label">Tel??fono</label>
                                            <div class="input-group col-md-7">
                                                <input type="text" class="form-control input-sm" name="telefono" id="telefono" value="@if(!is_null($ct_cliente)){{$ct_cliente->telefono1_representante}}@endif" placeholder="Tel??fono" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                                <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('telefono').value = '';"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!--Email-->
                                        <div class="form-group col-md-6 col-xs-12">
                                            <label for="email" class="col-md-5 control-label">Mail</label>
                                            <div class="input-group col-md-7">
                                                <input type="text" class="form-control input-sm" name="email" id="email" value="@if(!is_null($ct_cliente)){{$ct_cliente->email_representante}}@endif" placeholder="Mail">
                                                <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('email').value = '';"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                        <!--<div class="col-md-12" style="background-color: #028484;height: 10px;">
                        </div> -->






































            <div class="col-md-12 table-responsive">
                <input type="hidden" name="contador" id="contador" value="0">
                <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                        <tr>
                            <!--<th width="10%" class="" tabindex="0">{{trans('contableM.codigo')}}</th>-->
                            <th width="35%" class="" tabindex="0">{{trans('contableM.DescripciondelProducto')}}</th>
                            <th width="10%" class="" tabindex="0">{{trans('contableM.cantidad')}}</th>
                            <th width="10%" class="" tabindex="0">{{trans('contableM.precio')}}</th>
                            <th width="10%" class="" tabindex="0">{{trans('contableM.cobrarseguro')}}</th>
                            <th width="10%" class="" tabindex="0">% {{trans('contableM.prctdesc')}}</th>
                            <th width="10%" class="" tabindex="0">{{trans('contableM.descuento')}}</th>
                            <th width="10%" class="" tabindex="0">{{trans('contableM.precioneto')}}</th>
                            <th width="5%" class="" tabindex="0">{{trans('contableM.iva')}}</th>
                            <th width="10%" class="" tabindex="0">
                                <button onclick="nuevo()" type="button" class="btn btn-success btn-gray">
                                    <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="agregar_cuentas">
                        <tr class="wello">
                            <td style="max-width:100px;">
                                <Input type="hidden" name="codigo[]" class="codigo_producto" />
                                <select name="nombre[]" class="form-control select2_cuentas" style="width:100%" required onchange="verificar(this)">
                                    <option> </option>
                                    @foreach($productos as $value)
                                        <option value="{{$value->nombre}}" data-name="{{$value->nombre}}" data-codigo="{{$value->codigo}}" data-descuento="{{$value->mod_desc}}" data-precio="{{$value->mod_precio}}" data-maxdesc="{{$value->descuento}}" data-iva="{{$value->iva}}">{{$value->codigo}} | {{$value->nombre}}</option>
                                    @endforeach

                                </select>
                                <textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea>
                                <input type="hidden" name="iva[]" class="iva" />
                            </td>
                            <td>
                                <input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="cantidad[]" onchange="verificar_stock(this)" required>
                                <select name="bodega[]" class="form-control select2_bodega bodega" style="width: 80%;margin-top: 5px;" onchange="verificar_stock(this)">
                                    <option> </option>
                                    @foreach($bodega as $value)
                                    @if(!is_null($value))
                                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </td>
                            <td id="tprecio">
                                <select name="precio[]" class="form-control select2_precio pneto" style="width:60%;height:20px;display:inline;" required>
                                    <option value="0"> </option>


                                </select>
                                <button type="button" class="btn btn-info btn-gray btn-xs cp">
                                    <i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>
                                </button>
                            </td>
                            <td>
                                <input class="form-control text-right copago" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="copago[]" required>
                            </td>
                            <td>
                                <input class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor[]" required>
                                <input class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" required>
                            </td>
                            <td>
                                <input class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc[]" required>
                            </td>
                            <td>
                                <input class="form-control px-1 text-right luffy" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00" name="precioneto[]" required>
                            </td>
                            <td>
                                <input class="form" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]" disabled>

                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-gray delete">
                                    <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                </button>
                            </td>
                        </tr>
                        <tr style="display:none" id="mifila">
                            <td style="max-width:100px;">
                                <Input type="hidden" name="codigo[]" class="codigo_producto" />
                                <select name="nombre[]" class="form-control select2_cuentas" style="width:100%" required onchange="verificar(this)">
                                    <option> </option>
                                    @foreach($productos as $value)
                                    <option value="{{$value->nombre}}" data-name="{{$value->nombre}}" data-codigo="{{$value->codigo}}" data-descuento="{{$value->mod_desc}}" data-precio="{{$value->mod_precio}}" data-maxdesc="{{$value->descuento}}" data-iva="{{$value->iva}}">{{$value->codigo}} | {{$value->nombre}}</option>
                                    @endforeach

                                </select>
                                <textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea>
                                <input type="hidden" name="iva[]" class="iva" />
                            </td>
                            <td>
                                <input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="cantidad[]" onchange="verificar_stock(this)" required>
                                <select name="bodega[]" class="form-control select2_bodega bodega" style="width: 80%;margin-top: 5px;" onchange="verificar_stock(this)">
                                    <option> </option>

                                    @foreach($bodega as $value)
                                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach


                                </select>
                            </td>
                            <td>
                                <select name="precio[]" class="form-control select2_precio pneto" style="width:60%;height:20px;display:inline;" required>
                                    <option value="0"> </option>
                                </select>
                                <button type="button" class="btn btn-info btn-gray btn-xs cp">
                                    <i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>
                                </button>
                            </td>
                            <td>
                                <input class="form-control text-right copago" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="copago[]" required>
                            </td>
                            <td>
                                <input class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor[]" required>
                                <input class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" required>
                            </td>
                            <td>
                                <input class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc[]" required>
                            </td>
                            <td>
                                <input class="form-control px-1 text-right luffy" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00" name="precioneto[]" required>
                            </td>
                            <td>
                                <input class="form" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]" disabled>

                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-gray delete">
                                    <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class=''>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" class="text-right">{{trans('contableM.subtotal12')}}%</td>
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
                            <td colspan="2" class="text-right">{{trans('contableM.subtotal0')}}%</td>
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
                            <td colspan="2" class="text-right">{{trans('contableM.descuento')}}</td>
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
                            <td colspan="2" class="text-right">{{trans('contableM.subtotal')}}</td>
                            <td id="base" class="text-right px-1">0.00</td>
                            <!--<input type="hidden" name="base1" id="base1" class="hidden">-->
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
                            <td></td>
                            <td colspan="2" class="text-right"><strong>{{trans('contableM.total')}}</strong></td>
                            <td id="total" class="text-right px-1">0.00</td>
                            <input type="hidden" name="total1" id="total1" class="hidden">
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" class="text-right"><strong>{{trans('contableM.PorCobrarSeguro')}}</strong></td>
                            <td id="copagoTotal" class="text-right px-1">0.00</td>
                            <input type="hidden" name="totalc" id="totalc" class="hidden">
                        </tr>
                    </tfoot>


                </table>
            </div>










































                        <div class="form-group col-md-12">
                                    <label for="pago" class="col-md-6 control-label">Caja de Cobro:</label>
                                    <div class="input-group col-md-6">
                                        <select id="caja" name="caja" class="form-control" required>
                                            
                                          <option value="">Seleccione...</option>
                                          <option value="Torre 1">Torre 1</option>
                                          <option value="Torre 2">Torre 2</option>
                                        </select>
                                      </div>
                                  </div>
                        <div class="col-md-12 table-responsive ">
                                           
                                            <div class="col-md-12">
                                            <label> {{trans('contableM.formasdepago')}}</label>
                                            
                                            <button id="btn_pago" type="button" class="btn btn-success btn-xs btn-gray" style="margin-left: 10px;">
                                            <i class="glyphicon glyphicon-plus" aria-hidden="true"></i></button>
                                            </div>
                                            <input name="contador_pago" id="contador_pago" type="hidden" value="0">
                                            <table id="example1" role="grid" class="table table-hover dataTable" aria-describedby="example1_info" style="margin-top:0 !important">

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
                                                            <select name="nombre[]" class="form-control select2" style="width:100%;" required onchange="verificar(this)">
                                                                <option> </option>
                                                                @foreach($productos as $value)
                                                                    <option value="{{$value->nombre}}" data-name="{{$value->nombre}}" data-codigo="{{$value->codigo}}" data-descuento="{{$value->mod_desc}}" data-precio="{{$value->mod_precio}}" data-maxdesc="{{$value->descuento}}" data-iva="{{$value->iva}}">{{$value->codigo}} | {{$value->descripcion}}</option>
                                                                @endforeach

                                                            </select>
                                                            <textarea wrap="hard" rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea>
                                                            <input type="hidden" name="iva[]" class="iva" />
                                                        </td>
                                                        <td>
                                                            <input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="cantidad[]" required>
                                                        </td>
                                                        <td>
                                                            <input class="form-control text-right pneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="precio[]" required>
                                                        </td>
                                                        <td>
                                                        <input class="form-control text-right copago" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="100" name="copago[]" required>
                                                        <input class="copaged" type="hidden" name="copaged[]"  value="0.00">
                                                    <button type="button" class="btn btn-info btn-gray btn-xs cp" disabled>
                                                        <i class="fa fa-percent" aria-hidden="true"></i>
                                                    </button>
                                                    <!-- <input type="hidden" name="sources[]" class="noFetch" value="$"> -->
                                                        </td>

                                                        <td>
                                                            <input class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor[]" required>
                                                            <input class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" required>
                                                        </td>
                                                        <td>
                                                            <input class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc[]" required>
                                                        </td>
                                                        <td>
                                                            <input class="form-control px-1 text-right" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" required>
                                                        </td>
                                                        <td>
                                                            <input class="form chx" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]">

                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-danger btn-gray btn-xs delete">
                                                                <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-xs btn-gray showme">
                                                                <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                                            </button>

                                                        </td>
                                                    </tr>
                                                    <tr style="display:none" id="mifilaf">

                                                        <td style="max-width:100px;">
                                                            <input type="hidden" name="codigo[]" class="codigo_producto" value="deducible" />
                                                            <input class="form-control day inpu-sm" style="width: 80%;height:20px;" type="text" name="nombre[]" value="DEDUCIBLE">
                                                            <textarea wrap="hard" rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto input-sm" placeholder="Detalle del producto"></textarea>
                                                            <input type="hidden" name="iva[]" class="iva" />
                                                        </td>
                                                        <td>
                                                            <input class="form-control text-right cnetox cneto input-sm" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="cantidad[]" required>
                                                        </td>
                                                        <td>
                                                            <input style="width: 80%; height: 20px;" class="pnetox form-control input-sm text-right" onblur="this.value=parseFloat(this.value).toFixed(3);" name="precio[]" type="text" value="0.00">
                                                        </td>
                                                        <td>
                                                            <input class="form-control text-right copago input-sm" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="copago[]" readonly>
                                                            <input class="copaged" type="hidden" name="copaged[]"  value="0.00">
                                                        </td>
                                                        <td>
                                                            <input class="form-control text-right pdesc input-sm" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor[]" required readonly>
                                                            <input class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" readonly>
                                                        </td>
                                                        <td>
                                                            <input class="form-control text-right desc input-sm" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc[]" readonly>
                                                        </td>
                                                        <td>
                                                            <input class="form-control px-1 text-right input-sm" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" readonly>
                                                        </td>
                                                        <td>
                                                            <input class="form chx" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]" disabled>

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
                                    <input class="form-control" type="text" id="sobrante" onchange="changeSobrante(this)"  placeholder="$" >
                                </div>
                                <div class="col-md-4">
                                    <label>Diferencia: </label>
                                    <input class="form-control" type="text" id="diferencia" readonly value="0.00" >
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="text-align: center; margin-top: 10px;">
                            <button class="btn btn-success btn-gray saves" type="button" onclick="guardar(this)"> <i class="fa fa-save"></i> </button>
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
<script type="text/javascript">
    function verificar_numero_cedula() {
        cedula = validarCedula($('idpaciente').val());
    }
    function changeSobrante(e){
        console.log("sobrantes")
        var total= parseFloat($("#valorTotals").val());
        var nuevo= parseFloat($(e).val());
        tot=0;
        if(total>0){
            if(nuevo<=total){
                var tot= (total-nuevo);
            }else{
                var tot= (total-nuevo) * (-1);
            }
            
            $('#diferencia').val(tot.toFixed(2,2));
        }else{
            $(e).val('0.00');
            swal('Ingrese valores en los productos');
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
                console.log(data);

                if (data.value != 'no') {
                    //if(data.count()>0){

                    //alert("Prueba Ingreso");

                    if (xseguro != 0) {

                        //Muestra el Nivel del Seguro
                        //document.getElementById("ident_nivel").style.visibility = "visible";  
                        $("#id_nivel").empty();
                        $.each(data, function(key, registro) {
                            $("#id_nivel").append('<option value=' + registro.id_nivel + '>' + registro.nombre + '</option>');
                        });

                    } else {

                        $("#id_nivel").empty();

                    }

                } else {

                    //alert("Prueba Ingreso 2");
                    //Oculta el Nivel del Seguro
                    document.getElementById("ident_nivel").style.visibility = "hidden";

                }

            },
            error: function(data) {

            }
        });

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
    var fila = $("#mifila").html();
    var fila2 = $("#mifilaf").html();

    function nuevoa() {
        var nuevafila = $("#mifila").html();
        
        var rowk = document.getElementById("agregar_cuentas").insertRow(-1);

        //$('#mifila tr:last').before("<tr class='well'>"+nuevafila+"</tr>")
        rowk.innerHTML = fila;
        $('.select2').select2({
            tags: false
        });
    }

    function nuevoa() {
        var nuevafila = $("#mifila").html();
        var rowk = document.getElementById("agregar_cuentas").insertRow(-1);

        //$('#mifila tr:last').before("<tr class='well'>"+nuevafila+"</tr>")
        rowk.innerHTML = fila;
        $('.select2').select2({
            tags: false
        });
    }

    function verificar(e) {
        var iva = $('option:selected', e).data("iva");
        var codigo = $('option:selected', e).data("codigo");
        var usadescuento = $('option:selected', e).data("descuento");
        var max = $('option:selected', e).data("maxdesc");
        var modPrecio = $('option:selected', e).data("precio");

        $(e).parent().children().closest(".codigo_producto").val(codigo);
        $(e).parent().children().closest(".iva").val(iva);
        //console.log($(e).parent().next().next().children().closest(".cp"));
         /* if (!usadescuento) {
            $(e).parent().next().next().next().next().next().children().attr("readonly", "readonly");
            $(e).parent().next().next().next().next().children().attr("readonly", "readonly");
            $(e).parent().next().next().next().next().children().val(0);
            $(e).parent().next().next().next().next().next().children().val(0);
        } else {
            $(e).parent().next().next().next().next().next().children().removeAttr("readonly");
            $(e).parent().next().next().next().next().children().removeAttr("readonly");
            $(e).parent().next().next().next().next().next().children().val(0);
            $(e).parent().next().next().next().next().children().val(0);
        } */
        //$(e).parent().next().next().next().next().children().closest(".maxdesc").val(max);

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
            url: "{{route('contable_precio_prod.tarifario_codigo')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_emp': '{{$empresa->id}}',
                'id_prod': codigo,
                'id_seg': $("#id_seguro").val(),
                'id_niv': $("#id_nivel").val(),
                'posicion': '1'
            },
            success: function(data) {

                //alert(data.nivel);
                console.log(data);
                //$(e).parent().next().next().children().find('option').remove();
                //$(e).parent().next().next().children().closest(".pneto").append('<option value=' + data.precio_prod + ' ' + selected + '>' + data.precio_prod + '</option>');
                //$(e).parent().next().next().children().val(data.precio_prod);
                //$(e).parent().find('.precioOriginal').val(data.precio_prod);
                //totales(0);
                if(data.length>0){
                    $.each(data, function(key, value) {
                    //console.log(value)
                        $(e).parent().next().next().children().closest(".pneto").append('<option value=' + value.precio_prod + ' ' + selected + '>' + value.precio_prod + '</option>');

                    });
                }
                
 
            },
            error: function(data) {
                console.log(data.responseText);
            }
        });
        totales(0);

    }

    //cantidad
    //precio
    //copago
    //%descuento
    //descuento
    //precioneto

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;

        return true;
    }
    
    $('body').on('change', '.fpago', function() {
        var total_pagos = 0;
        $('.fpago').each(function(i, obj) {
            total_pagos = parseFloat(total_pagos) + parseFloat($(this).val());
        });
        $("#valor_totalPagos").val(total_pagos);
    });
    $('body').on('change', '.fbase', function() {
        var total_pagos = 0;
        $('.fbase').each(function(i, obj) {
            total_pagos = parseFloat(total_pagos) + parseFloat($(this).val());
        });
        $("#valor_totalPagos").val(total_pagos);
    });

    function totales(e) {
        var subt12 = [];
        var subt0 = [];
        var descuento = [];
        var sb12 = 0;
        var sb0 = 0;
        var finaly=0;
        var d = 0;
        var final=0;
        var copas=0;
        var s=0;
        var total=0;
        var copagoTotal = 0;
        console.log("Hola")
        if (e == 0) {
            //console.log('sumar')
            $('.cneto').each(function(i, obj) {
                var cant = $(this).val();
                var e = $(this).parent().prev().children().closest(".select");
                var precio1 = 0;
                var precio2 = 0;
                var precio3 = 0;
                var precio4 = 0;
                var precio5 = 0;
                var precioAut = 0;
                var tipo = $("#tipo_cliente").val();
                //console.log("el e es: ", e.val());
                var precio = $(this).parent().next().children().val();
                //console.log(precio + " el precio es ");
                if (precio == null) {
                    precio = 0;
                }
                var copago = $(this).parent().next().next().children().val();
                var st= parseFloat(copago)/100;
                if(st<=0){
                    st= 1;
                }
                var copage = $(this).parent().next().next().find('.copaged').val();
                if(copage==undefined){
                    copage=0;
                }
                var descuento = $(this).parent().next().next().next().next().children().val();
                d = parseFloat(d) + parseFloat(descuento);
                var iva = $(this).parent().next().next().next().next().next().next().children().prop('checked');
                //console.log(iva);
                /* finaly += (parseInt(cant) * parseFloat(precio)) - parseFloat(0); */
                var total = ((parseInt(cant) * parseFloat(precio)) - parseFloat(0)) * (st);
                if($("#oda").val()!='0' && $("#oda").val()!=''){
                    if(copago==0){
                     total= ((parseInt(cant) * parseFloat(precio)) - parseFloat(0)) - ((parseInt(cant) * parseFloat(precio)) - parseFloat(0));
                    }
                }

                if (iva == 1) {
                    //console.log(subt12);
                    subt12.push(total);
                    sb12 = sb12 + total;
                } else {
                    subt0.push(total);
                    sb0 = sb0 + total;
                }
                console.log(sb0)
                if($("#oda").val()!='0' && $("#oda").val()!=''){
                   
                    if(copago==0){
                        copagoTotal= parseFloat(copagoTotal)+((parseInt(cant) * parseFloat(precio)) - parseFloat(0));
                    }else{
                        copagoTotal = parseFloat(copagoTotal) + parseFloat(copage);
                    }
                }
                console.log(sb0,total);
                $("#subtotal_12").html(sb12.toFixed(2));
                $("#subtotal_0").html(sb0.toFixed(2));
                $("#descuento").html(d.toFixed(2));
                $("#base").html(sb12.toFixed(2));
                var iva = $("#ivareal").val();
                var ti = iva * sb12;
                $("#tarifa_iva").html(ti.toFixed(2));
                var t = (sb12 + sb0 + ti - d);
                $("#total").html(t.toFixed(2));
                $('#valorTotals').val(t.toFixed(2,2));
                $("#copagoTotal").html(copagoTotal.toFixed(2));
                $("#subtotal_121").val(sb12.toFixed(2));
                $("#subtotal_01").val(sb0.toFixed(2));
                $("#descuento1").val(d.toFixed(2));
                $("#tarifa_iva1").val(ti.toFixed(2));
                $("#total1").val(t.toFixed(2));
                $("#totalc").val(copagoTotal.toFixed(2));
            });
        }else{
            //console.log("entra aqui");

            /*   
             YA SE COMO AHCERLO PUEDES PREGUNTAR SI TIENE COPAGO LA COSA ES DISTINTA EN LA SUMA DEL SUBTOTAL
             
            */
        }
    }
    $(document).ready(function() {
        limpiar();
        obtener_fecha();
        $('.select2').select2({
            tags: false
        });
        cargar_nivel();
        @if($tipo=='0')
            $('.select2').trigger('change');
        @endif


    });
    // Accedemos al bot??n
    var emailInput = document.getElementById('oda');

    // evento para el input radio del "si"
    document.getElementById('interesadoPositivo').addEventListener('click', function(e) {
        console.log('Vamos a habilitar el input text');
        emailInput.disabled = false;
        $('.copago').prop('disabled', false);
        $('.copago').each(function(){
            $(this).val(100);
        });
        emailInput.value = "";
    });

    // evento para el input radio del "no"
    document.getElementById('interesadoNegativo').addEventListener('click', function(e) {
        console.log('Vamos a deshabilitar el input text');
        emailInput.disabled = "disabled";
        $('.copago').prop('disabled', 'disabled');
        emailInput.value = "";
    });
    
    function limpiar() {
        $("#datos_tarjeta_credito").hide();
        $("#datos_tarjeta_debito").hide();
        $("#datos_cheque").hide();
        $("#valor_tarjetadebito").val('');
        $("#valor_cheque").val('');
        $("#valor_efectivo").val('');
        $("#valor_tarjetacredito").val('');
        $("#numero_oda").val('0');

    }
    $('body').on('click', '.delete', function() {
        //console.log($(this));
        $(this).parent().parent().remove();
        totales(0);
    });
    $('body').on('click', '.chx', function() {
        //console.log($(this));
        $(this).parent().parent().find('.iva').val(1);
        totales(0);
    });
    $('#btn_pago').click(function(event) {
        id = document.getElementById('contador_pago').value;
        var midiv_pago = document.createElement("tr")
        midiv_pago.setAttribute("id", "dato_pago" + id);
        midiv_pago.innerHTML = '<td><select class="dogde" name="id_tip_pago' + id + '" id="id_tip_pago' + id + '" style="width: 100px;height:20px" onchange="revisar_componentes(this,' + id + ');" required><option value="">Seleccione</option>@foreach($tipo_pago as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select><input type="hidden" id="visibilidad_pago' + id + '" name="visibilidad_pago' + id + '" value="1"></td><td><input type="date" class="dogde input-number" value="{{date('Y-m-d')}}" name="fecha_pago' + id + '" id="fecha_pago' + id + '" style="width: 120px;"></td><td><select  id="tipo_tarjeta' + id + '"  class="dogde" name="tipo_tarjeta' + id + '" style="width: 175px;height:20px"><option value="">Seleccione...</option> @foreach($tipo_tarjeta as $tipo_t) <option value="{{$tipo_t->id}}">{{$tipo_t->nombre}}@endforeach</select></td><td><input  type="text" name="numero_pago' + id + '" id="numero_pago' + id + '" style="width: 100px;" ></td><td><select class="dogde" name="id_banco_pago' + id + '" id="id_banco_pago' + id + '" style="width: 90px;height:20px"><option value="">Seleccione...</option>@foreach($lista_banco as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select></td><td><input  style="text-align:center;" type="checkbox" name="fi'+id+'" id="fi'+id+'" onchange="revision_total('+id+')" value="0" ></td><td><input  autocomplete="off" class="dogde" name="id_cuenta_pago' + id + '" id="id_cuenta_pago' + id + '" ></td><td><input class="dogde"  type="text" id="giradoa' + id + '" name="giradoa' + id + '"></td><td><input class="dogde text-right input-number fpago" type="text" id="valor' + id + '" name="valor' + id + '" style="width: 100px;" onblur="this.value=parseFloat(this.value).toFixed(2);"  value="0" onchange="revision_total(' + id + ')" onkeypress="return soloNumeros(this);" required></td><td><input class="dogde input-number fbase" type="text" readonly id="valor_base' + id + '" name="valor_base' + id + '" required onkeypress="return soloNumeros(event);" ></td><td><button style="text-align:center;" type="button" onclick="eliminar_form_pag(' + id + ')" class="btn btn-danger btn-gray delete btn-xs"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';

        document.getElementById('agregar_pago').appendChild(midiv_pago);
        id = parseInt(id);
        id = id + 1;
        document.getElementById('contador_pago').value = id;

    });

    function revision_total(id) {
        var fi = document.getElementById("fi" + id);
        var valor = $('#valor' + id).val();
        if(valor>0){
            if (fi.checked == true) {
            tipo = $('#id_tip_pago' + id).val();
            if (tipo == '4') {
                ntotal = valor * 1.07;
            } else if (tipo == '6') {
                ntotal = valor * 1.02;
            } else {
                ntotal = valor * 1;
            }
            $('#valor_base' + id).val(ntotal.toFixed(2));
            var tos= parseFloat($('#total1').val());
            var permiso= ntotal- valor;
            if(permiso<0){
                permiso= permiso * -1;
            }
            permiso= permiso.toFixed(2,2);
            if(valor>0){
                snew(permiso);
            }

            } else {
                ntotal = valor * 1;
                $('#valor_base' + id).val(ntotal.toFixed(2));
            }
            suma_total();
        }

    }
    function snew(a){
        var poe='<tr>'+'<td style="max-width:100px;"><input type="hidden" name="codigo[]" class="codigo_producto" value="FEE-" /><select name="nombre[]" class="form-control select2" style="width:100%;" required onchange="verificar(this)"><option> </option>@foreach($productos as $value) @if($value->codigo=="FEE-")<option @if($value->codigo=="FEE-") selected="selected" @endif>{{$value->codigo}} | {{$value->descripcion}}</option> @endif @endforeach</select><textarea wrap="hard" rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea><input type="hidden" name="iva[]" class="iva" /></td>'+'<td><input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="1" name="cantidad[]" required></td>'+'<td><input class="form-control text-right pneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="'+a+'" name="precio[]" required></td>'+'<td><input class="form-control text-right copago" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="100" name="copago[]" required><input class="copaged" type="hidden" name="copaged[]"  value="0.00"><button type="button" class="btn btn-info btn-gray btn-xs cp" disabled><i class="fa fa-percent" aria-hidden="true"></i></button></td>'+'<td><input class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor[]" required><input class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" required></td>'+'<td><input class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc[]" required></td>'+'<td><input class="form-control px-1 text-right" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" required></td>'+'<td><input class="form chx" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]"></td>'+'<td><button type="button" class="btn btn-danger btn-gray btn-xs delete"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button><button type="button" class="btn btn-danger btn-xs btn-gray showme"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i></button></td>'+'</tr>';
        var rowk = document.getElementById("agregar_cuentas").insertRow(-1);
        //$('#mifila tr:last').before("<tr class='well'>"+nuevafila+"</tr>")
        rowk.innerHTML = poe;
        $('.select2').select2({
            tags: false
        });
        totales(0);
    }

    function suma_total() {
        var contador = $('#contador_pago').val();
        var sumador = 0;
        var sumador_sin = 0;

        for (var i = 0; i < contador; i++) {
            if ($('#visibilidad_pago' + i).val() == 1) {
                sumador_sin = sumador_sin + parseFloat($('#valor_base' + i).val());
                sumador = sumador + parseFloat($('#total' + i).val());
            }
        }

        $('#total_sin_tarjeta').val(sumador_sin.toFixed(2));
        $('#total_final1').val(sumador.toFixed(2));
        
    }

    function revisar_componentes(e, id) {
        metodo = $('#id_tip_pago' + id).val();
        if (metodo == 1) {
            $("#tipo_tarjeta" + id).prop('disabled', true);
            $("#numero" + id).prop('disabled', true);
            $("#tipo_tarjeta" + id).prop('disabled', true);
            $("#id_banco" + id).prop('disabled', true);
            $("#fi" + id).prop('disabled', true);
            revision_total(id);
        } else if (metodo == 2) {
            $("#tipo_tarjeta" + id).prop('disabled', true);
            $("#numero" + id).prop('disabled', false);
            $("#tipo_tarjeta" + id).prop('disabled', true);
            $("#id_banco" + id).prop('disabled', false);
            $("#fi" + id).prop('disabled', true);
            revision_total(id);
        } else if (metodo == 3) {
            $("#tipo_tarjeta" + id).prop('disabled', true);
            $("#numero" + id).prop('disabled', false);
            $("#tipo_tarjeta" + id).prop('disabled', true);
            $("#id_banco" + id).prop('disabled', false);
            $("#fi" + id).prop('disabled', true);
            revision_total(id);
        } else if (metodo == 4) {
            $("#tipo_tarjeta" + id).prop('disabled', false);
            $("#numero" + id).prop('disabled', false);
            $("#tipo_tarjeta" + id).prop('disabled', false);
            $("#id_banco" + id).prop('disabled', false);
            $("#fi" + id).prop('disabled', false);
            revision_total(id);
        } else if (metodo == 5) {
            $("#tipo_tarjeta" + id).prop('disabled', false);
            $("#numero" + id).prop('disabled', false);
            $("#tipo_tarjeta" + id).prop('disabled', false);
            $("#id_banco" + id).prop('disabled', false);
            $("#fi" + id).prop('disabled', false);
            revision_total(id);
        } else if (metodo == 6) {
            $("#tipo_tarjeta" + id).prop('disabled', false);
            $("#numero" + id).prop('disabled', false);
            $("#tipo_tarjeta" + id).prop('disabled', false);
            $("#id_banco" + id).prop('disabled', false);
            $("#fi" + id).prop('disabled', false);
            revision_total(id);
        }
    }

    function validar_copago() {
        // Get the checkbox
        var checkBox = document.getElementById("copago");
        // Get the output text
        var text = document.getElementById("dato_copago");

        // If the checkbox is checked, display the output text
        if (checkBox.checked == true) {
            document.getElementById("div_copago2").style.display = "block";
            $('#valor_copago').val('0');
            text.style.display = "block";
        } else {
            $('#valor_copago').val('0');
            document.getElementById("div_copago2").style.display = "none";
            text.style.display = "none";
        }
    }

    //Elimina Registro de la Tabla Forma de Pago
    function eliminar_form_pag(valor) {
        var dato_pago1 = "dato_pago" + valor;
        var nombre_pago2 = 'visibilidad_pago' + valor;
        document.getElementById(dato_pago1).style.display = 'none';
        document.getElementById(nombre_pago2).value = 0;
        suma_total();

    }

    function obtener_fecha() {

        //obtenemos la fecha actual
        var now = new Date();
        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2);
        var today = now.getFullYear() + "-" + (month) + "-" + (day);
        $("#fecha").val(today);

    }


    /*function obtener_num_factura(){
        $.ajax({
            url:"{{route('num_fact.consulta')}}",
            type: 'get',
            datatype: 'json',
            success: function(data){
               console.log(data);
               $('#nfactura').val(data);
            },
            error: function(data){
                console.log(data);
            }
        })
    }*/

    //Sucursal Empresa Agenda
    /*function obtener_sucursal(){

        var id_seleccionado = $("#id_empresa").val();

        $.ajax({
            type: 'post',
            url:"{{route('sucursal.empresa')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'id_emp': id_seleccionado},
            success: function(data){
                //console.log(data);

                if(data.value!='no'){
                    if(id_seleccionado!=0){
                        $("#sucursal").empty();

                        $.each(data,function(key, registro) {
                            $("#sucursal").append('<option value='+registro.id+'>'+registro.codigo_sucursal+'</option>');

                        });
                    }else{
                        $("#sucursal").empty();

                    }

                }
            },
            error: function(data){
                console.log(data);
            }
        })

    }

    //obtener Caja
    function obtener_caja(){

        var id_sucursal = $("#sucursal").val();

        $.ajax({
            type: 'post',
            url:"{{route('caja.sucursal')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'id_sucur': id_sucursal},
            success: function(data){
                //console.log(data);

                if(data.value!='no'){
                    if(id_sucursal!=0){
                        $("#punto_emision").empty();

                        $.each(data,function(key, registro) {
                            $("#punto_emision").append('<option value='+registro.id+'>'+registro.codigo_sucursal+'-'+registro.codigo_caja+'</option>');

                        });
                    }else{
                        $("#punto_emision").empty();

                    }

                }
            },
            error: function(data){
                console.log(data);
            }
        })

    }*/

    function crear_factura() {
        $('#crear_recibo').button('loading');

        var formulario = document.forms["crear_form"];

        //Datos Generales
        var id_emp = formulario.id_empresa.value;
        /*var sucurs = formulario.sucursal.value;
        var punt_emision = formulario.punto_emision.value;*/


        //Datos Paciente
        var cedula = formulario.idpaciente.value;
        var nombre_paciente = formulario.nombres.value;
        var seguro_paciente = formulario.id_seguro.value;


        //Datos Clientes
        var tipo_identicacion = formulario.tipo_identificacion.value;
        var ced_cliente = formulario.cedula.value;
        var raz_social = formulario.razon_social.value;
        var ciud_cliente = formulario.ciudad.value;
        var dire_cliente = formulario.direccion.value;
        var telf_cliente = formulario.telefono.value;
        var email_cliente = formulario.email.value;
        var numero_oda = formulario.numero_oda.value;
        var copago = formulario.copago;




        //Concepto y Nota

        //var concepto = formulario.concepto.value;
        //var nota = formulario.nota.value;

        //Pago
        var pago = formulario.pago.value;
        var caja_pago = formulario.caja.value;

        var msj = "";
        var msj2 = "";

        //Datos Generales

        if (id_emp == "") {

            msj = msj + "Por favor, Seleccione la Empresa<br/>";
        }
        //alert(copago.checked);
        if (copago.checked == true) {

            if (numero_oda == '0' || numero_oda == '') {
                msj = msj + "Por favor, Ingrese el n??mero de Oda<br/>";
            }
        }

        /*if(sucurs == ""){

           msj = msj + "Por favor, Seleccione la Sucursal<br/>";
        }

        if(punt_emision == ""){
           msj = msj + "Por favor, Seleccione el Punto de Emision<br/>";
        }*/

        //Paciente
        if (cedula == "") {
            msj += "Por favor,Ingrese la cedula del paciente<br/>";
        }
        if (nombre_paciente == "") {
            msj += "Por favor,Ingrese el nombre del paciente<br/>";
        }
        if (seguro_paciente == "") {
            msj += "Por favor,Seleccione el seguro paciente<br/>";
        }

        //Cliente
        if (tipo_identicacion == "") {
            msj += "Por favor,Selecione el Tipo de Identificaci??n<br/>";
        }
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


        //Pago
        if (pago == "") {
            msj += "Por favor,Ingrese el pago\n";
        }
        if (caja_pago == "") {
            msj += "Por favor,Ingrese la caja a la cual se va a pagar<br/>";
        }

        var i;
        var max = document.getElementById('agregar_pago').rows.length;
        for (i = 0; i < max; i++) {
            var tipo_pago = document.getElementById('id_tip_pago' + i).value;
            var numero = document.getElementById('numero' + i).value;
            var id_banco = document.getElementById('id_banco' + i).value;
            var tipo_tarjeta = document.getElementById('tipo_tarjeta' + i).value;
            if (tipo_pago == '2') {

                if (numero == "") {
                    msj += "Por favor,Ingrese el n??mero del cheque<br/>";
                }
                if (id_banco == "") {
                    msj += "Por favor,Seleccione el Banco<br/>";
                }
            }
            if (tipo_pago == '4') {
                if (tipo_tarjeta == "") {
                    msj += "Por favor,Ingrese el Tipo de tarjeta<br/>";
                }

                if (id_banco == "") {
                    msj += "Por favor,Seleccione el Banco<br/>";
                }
            }
        }

        if (msj != "") {
            $('#crear_recibo').button('reset');
            swal({
                title: "Error!",
                type: "error",
                html: msj
            });
            return false;
        }

        if (msj2 != "") {
            $('#crear_recibo').button('reset');
            swal({
                title: "Error!",
                type: "error",
                html: msj2
            });
            return false;
        }

        var fecha = document.getElementById('fecha').value;

        var unix = Math.round(new Date(fecha).getTime() / 1000);

        $.ajax({
            type: 'post',
            url: "{{route('facturacion.guardar_orden')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $("#crear_form").serialize(),
            success: function(data) {

                //console.log(data);
                $("#ride").attr("href", );
                window.open("{{asset('/comprobante/orden/venta')}}/" + data.id_orden, '_blank ');
                window.location.href = "{{asset('/agenda/calendario/')}}/{{$agenda->id_doctor1}}/" + unix;
                $('#crear_recibo').button('reset');

            },
            error: function(data) {
                console.log(data);

            }
        });
    }

    function seguro() {
        var seguro = $('#id_seguro').val();
        $.ajax({
            type: 'get',
            url: "{{asset('contable/facturacion/verificar/seguro/')}}" + '/' + seguro,
            datatype: 'html',
            success: function(data) {
                if (data == 1) {

                } else {

                }
                pagar();

            },
            error: function() {
                //alert('error al cargar');
            }
        });
    }

    function pagar() {
        var seguro = $('#id_seguro').val();
        var doctor = $('#id_doctor').val();
        $.ajax({
            type: 'get',
            url: "{{asset('contable/facturacion/verificar/pago/')}}" + '/' + seguro + '/' + doctor,
            datatype: 'html',
            data: $("#obs_id").serialize(),
            success: function(data) {
                $('#pago').val(data.trim());
            },
            error: function() {
                //alert('error al cargar');
            }
        });
    }

    $("#cedula").autocomplete({
        source: function(request, response) {
            $.ajax({
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                url: "{{route('facturacion.buscar_cliente')}}",
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        minLength: 3,
    });

    function buscar() {
        var cedula_1 = $('#cedula').val();
        var pasaporte = parseInt($('#tipo_identificacion').val());
        var alerta = 0;
        if (cedula_1.length < 10) {
            alert('La Cantidad de Digitos no es Correcta');
        }
        var cedula = validarCedula($('#cedula').val());
        if (cedula == false && pasaporte != 6 && pasaporte != 8) {
            $('#cedula').val('');
            $('#razon_social').val('');
            $('#ciudad').val('');
            $('#direccion').val('');
            $('#telefono').val('');
            $('#email').val('');
            $('#caja').val('');

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
                        $('#caja').val(data.caja);
                    }
                    console.log(data);
                },
                error: function(data) {
                    console.log(data);
                }
            });

        }

    }
    function guardar(e){
       if($('#crear_form').valid()){
        $('.fbase').change();
        if(parseFloat($("#valor_totalPagos").val())==parseFloat($("#total1").val())){
            $.ajax({
                type: 'post',
                url: "{{route('facturacion.store_new')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#crear_form").serialize(),
                success: function(data) {
                    console.log(data);
                    swal('Mensaje',`{{trans('proforma.GuardadoCorrectamente')}}`,'success');
                    $(e).attr('disabled','disabled');
                    $('#printer').removeClass('visible');
                    $('#id_store').val(data.id);
                    window.open('{{url("comprobante/orden/venta/")}}/'+data.id,'_blank');

                },
                error: function(data) {
                    console.log(data);
                }
            });
        }else{
            swal('Mensaje','No coinciden los valores de pago con el total','error');
        }
       
       }
        
                
    }
    function fun(){
        var id= $("#id_store").val();
        if(id!=0){
            window.open('{{url("comprobante/orden/venta/")}}/'+id,'_blank');
        }else{
            swal("Compruebe si el guardado fue correcto");
        }
        
    }

</script>













<script type="text/javascript">
        //Carga Nivel del Seguro Seleccionado
        /*$(document).ready(function(){
        
          cargar_nivel(); 
        
        });*/
        // var input = document.getElementById('toggleswitch');
        // //var outputtext = document.getElementById('status');

        // input.addEventListener('change', function() {
        //     if (this.checked) {
        //        $("#electronica").val(1);
        //        $('#numero_autorizacion').attr('readonly', true);
        //        $('#numero').attr('readonly', true);
        //     } else {
        //        $("#electronica").val(0);
        //        $('#numero_autorizacion').attr('readonly', false);
        //        $('#numero').attr('readonly', false);
        //     }
        // });

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
                    console.log(data);

                    if (data.value != 'no') {
                        if (xseguro != 0) {
                            $("#id_nivel").empty();
                            $.each(data, function(key, registro) {
                                $("#id_nivel").append('<option value=' + registro.id_nivel + '>' + registro.nombre + '</option>');
                            });
                        } else {

                            $("#id_nivel").empty();

                        }

                    } else {
                        document.getElementById("ident_nivel").style.visibility = "hidden";
                    }

                },
                error: function(data) {

                }
            });
        }

        var fila = $("#mifila").html();
        var existeCliente = false;
        $('.select2_cuentas').select2({
            tags: false
        });
        $(document).on("focus", "#nombre_cliente", function() {
            $("#nombre_cliente").autocomplete({
                classes: {
                    "ui-autocomplete": "dob_autocomplete",
                },
                source: function(request, response) {
                    $.ajax({
                        type: 'GET',
                        url: "{{route('ventas.buscarcliente')}}",
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                change: function(event, ui) {
                    $("#identificacion_cliente").val(ui.item.id);

                    $("#direccion_cliente").val(ui.item.direccion);
                    $("#ciudad_cliente").val(ui.item.ciudad);
                    $("#mail_cliente").val(ui.item.mail);
                    $("#telefono_cliente").val(ui.item.telefono);
                    $("#tipo_cliente").val(ui.item.tipo);
                    totales(0);
                },
                selectFirst: true,
                minLength: 1,
            });

        });

        $(document).on("focus", "#identificacion_cliente", function() {
            $("#identificacion_cliente").autocomplete({
                classes: {
                    "ui-autocomplete": "dob_autocomplete",
                },
                source: function(request, response) {
                    $.ajax({
                        type: 'GET',
                        url: "{{route('ventas.buscarclientexid')}}",
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                            //console.log("identificacion_cliente", data.id);
                            if (data.id == "") {
                                //swal("el cliente no existe");
                                existeCliente = false;
                            } else {
                                existeCliente = true;
                            }
                        }
                    });
                },
                change: function(event, ui) {
                    $("#nombre_cliente").val(ui.item.nombre);

                    $("#direccion_cliente").val(ui.item.direccion);
                    $("#ciudad_cliente").val(ui.item.ciudad);
                    $("#mail_cliente").val(ui.item.mail);
                    $("#telefono_cliente").val(ui.item.telefono);
                    $("#tipo_cliente").val(ui.item.tipo);
                    totales(0);
                },
                selectFirst: true,
                minLength: 1,
            });
        });
        $(document).on("focus", "#identificacion_paciente", function() {
            $("#identificacion_paciente").autocomplete({
                classes: {
                    "ui-autocomplete": "dob_autocomplete",
                },
                source: function(request, response) {
                    $.ajax({
                        type: 'GET',
                        url: "{{route('ventas.buscarpaciente')}}",
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                            if (data.id == "") {
                                //swal("el cliente no existe");
                                existeCliente = false;
                            }
                        }
                    });
                },
                change: function(event, ui) {
                    if (ui.item != null) {
                        //console.log(ui.item);
                        $("#nombre_paciente").val(ui.item.nombre);
                        $("#id_seguro option[value=" + ui.item.seguro + "]").attr('selected', 'selected');
                    } else {
                        swal("No Existe el paciente");
                    }
                    /*$("#direccion_cliente").val(ui.item.direccion);
                    $("#ciudad_cliente").val(ui.item.ciudad);
                    $("#mail_cliente").val(ui.item.mail);
                    $("#telefono_cliente").val(ui.item.telefono);*/
                },
                selectFirst: true,
                minLength: 3,
            });
        });
        $(document).on("focus", "#nombre_paciente", function() {
            $("#nombre_paciente").autocomplete({
                classes: {
                    "ui-autocomplete": "dob_autocomplete",
                },
                source: function(request, response) {
                    $.ajax({
                        type: 'GET',
                        url: "{{route('ventas.buscarpaciente_nombre')}}",
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                            if (data.id == "") {
                                //swal("el cliente no existe");
                                existeCliente = false;
                            }
                        }
                    });
                },
                change: function(event, ui) {
                    if (ui.item != null) {
                        console.log(ui.item);
                        $("#identificacion_paciente").val(ui.item.nombre);
                        $("#id_seguro option[value=" + ui.item.seguro + "]").attr('selected', 'selected');
                    } else {
                        swal("No Existe el paciente");
                    }
                    /*$("#direccion_cliente").val(ui.item.direccion);
                    $("#ciudad_cliente").val(ui.item.ciudad);
                    $("#mail_cliente").val(ui.item.mail);
                    $("#telefono_cliente").val(ui.item.telefono);*/
                },
                selectFirst: true,
                minLength: 3,
            });
        });

        $('body').on('click', '.delete', function() {
            console.log($(this));

            $(this).parent().parent().remove();
            totales(0);
        });

        $(document).ready(function() {


            limpiar();

            $('.select2_cliente').select2({
                tags: false
            });

            $('#iva').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                increaseArea: '20%'
            });

        });

        $('body').on('click', '.cp', function() {
            console.log($(this));
            console.log($(this).prev().attr('class'));
            var clase = $(this).prev().attr('class');
            var html = '<input type="text" class="form-control pneto"  name="precio[]" style="width:40%;display:inline;height:20px;">' +
                '<button type="button" class="btn btn-info btn-gray btn-xs cp">' +
                '<i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>' +
                '</button>';
            console.log($(this).parent());
            if (clase.includes('select2_precio')) {
                $(this).parent().append(html);
                $(this).prev().remove();
                $(this).remove();

            } else {
                html = '<select  name="precio[]"  class="form-control select2_precio pneto" style="width:60%;height:20px;display:inline;" autofocus active required>' +
                    '<option value="0"> </option></select>' +
                    '<button type="button" class="btn btn-info btn-gray btn-xs cp" >' +
                    '<i class="glyphicon glyphicon-pencil" aria-hidden="true"></i></button>';
                // $(this).parent().empty();
                // $(this).parent().append(html);
                $(this).parent().append(html);
                verificar($(this).parent().prev().prev().children().closest('.select2_cuentas'));
                $(this).prev().remove();
                $(this).remove();

            }


        });
        /*function changePrecio(i, e){
            var html = '<input type="text" class="form-control pneto" style="width:40%;display:inline;height:20px;">'+
                        '<button type="button" class="btn btn-info btn-gray btn-xs" onclick="changePrecio(this,2)">'+
                        '<i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>'+
                        '</button>';
                        console.log(i.parent());
            if(e==1){

                $("#tprecio").empty();
                $("#tprecio").append(html);
            }else{
                html = '<select  name="precio[]"  class="form-control select2_precio pneto" style="width:60%;display:inline;" required>'+
                        '<option value="0"> </option></select>'+
                        '<button type="button" class="btn btn-info btn-gray btn-xs" onclick="changePrecio(this,1)">'+
                        '<i class="glyphicon glyphicon-pencil" aria-hidden="true"></i></button>';
                $("#tprecio").empty();
                $("#tprecio").append(html);
                console.log($("#tprecio").prev().prev().children().closest('.select2_cuentas'));
                verificar($("#tprecio").prev().prev().children().closest('.select2_cuentas'));
            }
        }*/

        function goNew() {
            $(".btn_add").attr("disabled", false);
            location.href = '{{route('ventas_crear')}}';
            $("#asiento").val("");
            $("#id").val("");
            $("#numero").val("");

        }

        //Desabilita Componentes
        function desabilita_componente(elemento, id) {

            var id_tipo = parseInt(elemento.value);

            if ((id_tipo == 1) || (id_tipo == 4) || (id_tipo == 5) || (id_tipo == 6)) {

                $('#fecha' + id).val("");

                document.getElementById('fecha' + id).disabled = true;
                document.getElementById('numero' + id).disabled = true;
                document.getElementById('id_banco' + id).disabled = true;
                document.getElementById('id_cuenta_pago' + id).disabled = true;
                document.getElementById('giradoa' + id).disabled = true;

            } else {

                if ((id_tipo == 2) || (id_tipo == 3)) {

                    document.getElementById('fecha' + id).disabled = false;
                    document.getElementById('numero' + id).disabled = false;
                    document.getElementById('id_banco' + id).disabled = false;
                    document.getElementById('id_cuenta_pago' + id).disabled = false;
                    document.getElementById('giradoa' + id).disabled = false;

                }

            }
        }


        function verificar(e) {
            var iva = $('option:selected', e).data("iva");
            var codigo = $('option:selected', e).data("codigo");
            var usadescuento = $('option:selected', e).data("descuento");
            var max = $('option:selected', e).data("maxdesc");
            var modPrecio = $('option:selected', e).data("precio");

            $(e).parent().children().closest(".codigo_producto").val(codigo);
            $(e).parent().children().closest(".iva").val(iva);
            //console.log($(e).parent().next().next().children().closest(".cp"));

            if (modPrecio) {
                //$(e).parent().next().next().closest(".cp");
                //console.log("modifica precio");
                $(e).parent().next().next().children().find(".cp").removeAttr("disabled");
            } else {
                //console.log("no modifca el precio");
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
            if (iva == '1') {
                $(e).parent().next().next().next().next().next().next().next().children().attr("checked", "checked");
            } else {
                $(e).parent().next().next().next().next().next().next().next().children().removeAttr("checked");
            }

            //cargarPrecios
            var tipo = $("#tipo_cliente").val();
            var selected = "";

            //Obtiene Id_Seguro y Id_Nivel
            var id_seg = $("#id_seguro").val();
            var id_niv = $("#id_nivel").val();

            $.ajax({
                type: 'post',
                url: "{{route('precios')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    id: codigo,
                    id_seguro: id_seg,
                    id_nivel: id_niv
                },
                success: function(data) {

                    //alert(data.nivel);

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
                    console.log(data.responseText);
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
            var cant = $(this).parent().prev().children().val();
            var copago = $(this).parent().next().children().val();
            var descuento = $(this).parent().next().next().next().children().val();
            var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
            total = redondeafinal(total);
            $(this).parent().next().next().next().next().children().val(total);
            totales(0);
        });
        $('body').on('active', '.pneto', function() {
            // verificar(this);
            var cant = $(this).parent().prev().children().val();
            var copago = $(this).parent().next().children().val();
            var descuento = $(this).parent().next().next().next().children().val();
            var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
            total = redondeafinal(total);
            $(this).parent().next().next().next().next().children().val(total);
            totales(0);
        });
        $('body').on('change', '.luffy', function() {
            var cant = parseFloat($(this).parent().prev().prev().prev().prev().prev().children().val());
            var precio = parseFloat($(this).val());
            var totales = precio / cant;
            totales = redondeafinal(totales);
            $(this).parent().prev().prev().prev().prev().find('.cp').click();
            $(this).parent().prev().prev().prev().prev().children().val(totales);
            totales(0);

            //$(this).val(totales);
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
            var precio = $(this).parent().next().children().val();
            // console.log("this", $(this).parent().next().children().val());
            var copago = $(this).parent().next().next().children().val();
            //console.log("copago", copago);
            var descuento = $(this).parent().next().next().next().next().children().val();
            var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
            total = redondeafinal(total);
            $(this).parent().next().next().next().next().next().children().val(total);

            totales(0);
        });

        $('body').on('change', '.copago', function() {
            verificar(this);
            var cant = $(this).parent().prev().prev().children().val();
            var precio = $(this).parent().prev().children().val();

            var copago = $(this).val();
            console.log("copago", copago);
            var descuento = $(this).parent().next().next().children().val();
            var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
            console.log(total);
            total = redondeafinal(total);
            $(this).parent().next().next().next().children().val(total);

            totales(0);
        });


        $('body').on('change', '.pdesc', function() {

            var m = $(this).next().val();
            var cant = $(this).parent().prev().prev().prev().children().val();
            var precio = $(this).parent().prev().prev().children().val();
            var pdesc = $(this).val();
            //console.log("el descuento maximo debe de ser", m, pdesc);
            if (parseFloat(pdesc) > parseFloat(m)) {
                swal("El descuento no puede ser mayor a " + m + "%");
                $(this).val(m).focus();
            }
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
            var cant = $(this).parent().prev().prev().prev().prev().children().val();
            var precio = $(this).parent().prev().prev().prev().children().val();
            /*if(pdesc> m){
                swal("El descuento no puede ser mayor a "+m+"%");
                $(this).val(m);
            }*/
            var descuento = $(this).val();
            verificar(this);
            //console.log(cant, precio);
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

        $('body').on('change', '.fpago', function() {
            var total_pagos = 0;
            $('.fpago').each(function(i, obj) {
                total_pagos = parseFloat(total_pagos) + parseFloat($(this).val());
            });
            $("#valor_totalPagos").val(total_pagos);
        });

        function totales(e) {
            var subt12 = [];
            var subt0 = [];
            var descuento = [];
            var descuentosub0 = 0;
            var descuentosub12 = 0;
            var sb12 = 0;
            var sb0 = 0;
            var d = 0;
            var copagoTotal = 0;
            if (e == 0) {
                $('.cneto').each(function(i, obj) {
                    var cant = $(this).val();
                    var e = $(this).parent().prev().children().closest(".select2_cuentas");
                    var precio1 = 0;
                    var precio2 = 0;
                    var precio3 = 0;
                    var precio4 = 0;
                    var precio5 = 0;
                    var precioAut = 0;
                    var tipo = $("#tipo_cliente").val();
                    //console.log("el e es: ", e.val());

                    var precio = $(this).parent().next().children().val();
                    var copago = $(this).parent().next().next().children().val();
                    var descuento = $(this).parent().next().next().next().next().children().val();
                    d = parseFloat(d) + parseFloat(descuento);
                    var iva = $(this).parent().next().next().next().next().next().next().children().prop('checked');
                    //console.log(iva);
                    precio = precio != null ? precio : 0;
                    var total = (parseInt(cant) * (precio)) - parseFloat(0) - parseFloat(copago);
                    //console.log("precio y cantidad"+total);
                    if (iva) {
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
                    //console.log("subtotal12"+sb12);
                    $("#subtotal_12").html(sb12.toFixed(2));
                    $("#subtotal_0").html(sb0.toFixed(2));
                    $("#descuento").html(d.toFixed(2));
                    var descuento_total = descuentosub12 + descuentosub0;
                    var sum = sb12 + sb0 - descuento_total;
                    $("#base").html(sum.toFixed(2,2));
                    sum = redondeafinal(sum);
                   
                    var iva = $("#ivareal").val();
                    var ti = iva * sb12;
                    //console.log(ti);
                    if (d > 0) {
                        if (sb12 > 0) {
                            ti = iva * (sb12 - descuentosub12);
                        }
                    }
                    ti = redondeafinal(ti);
                    $("#tarifa_iva").html(ti);
                    var t = sb12 + sb0 + ti - d;
                    var totax = sum + ti;
                    totax = redondeafinal(totax);
                    $("#total").html(totax);
                    $("#copagoTotal").html(copagoTotal.toFixed(2));
                    sb12 = redondeafinal(sb12);
                    $("#subtotal_121").val(sb12);
                    sb0 = redondeafinal(sb0);
                    d = redondeafinal(d);
                    $("#subtotal_01").val(sb0);
                    $("#descuento1").val(d);
                    $("#tarifa_iva1").val(ti);
                    $("#total1").val(totax);
                    $("#totalc").val(copagoTotal.toFixed(2));
                    
                });
            }
        }
        //new change to round best because no square values date: 30 Nov 2020 10:19 AM
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


        function ingresar_cero() {
            var secuencia_factura = $('#numero').val();
            secuencia_factura =  secuencia_factura.trim()
            console.log(secuencia_factura);
            var digitos = 9;
            var ceros = 0;
            var varos = '0';
            var secuencia = 0;
            if (secuencia_factura > 0) {
                var longitud = parseInt(secuencia_factura.length);
                if (longitud > 10) {
                    swal("Error!", "Valor no permitido", "error");
                    $('#numero').val('');

                } else {
                    var concadenate = parseInt(digitos - longitud);
                   
                    console.log(secuencia_factura.length)
                    switch (longitud) {
                        case 1:
                            secuencia = '00000000';
                            break;
                        case 2:
                            secuencia = '0000000';
                            break;
                        case 3:
                            secuencia = '000000';
                            break;
                        case 4:
                            secuencia = '00000';
                            break;
                        case 5:
                            secuencia = '0000';
                            break;
                        case 6:
                            secuencia = '000';
                            break;
                        case 7:
                            secuencia = '00';
                            break;
                        case 8:
                            secuencia = '0';
                            break;
                    }
                    $('#numero').val(secuencia + secuencia_factura);
                }


            } else {
                swal("Error!", "Valor no permitido", "error");
                $('#numero').val('');
            }
        }
        //aqui ando
        function alertas(text) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: `${text}`
            })
        }

        $(".btn_add").click(function() {

            if ($("#form").valid()) {
                var contador_pago = parseInt($("#contador_pago").val());
                $(".print").css('visibility', 'visible');
                var contador = 0;
                $(".pago").each(function() {
                    contador++;
                });
                var se=$("#electronica").val();
                if ( se== "0" || se=="") {
                    contador++;
                }
                if (contador > 0) {
                    $(".btn_add").attr("disabled", true);
                    $("#mifila").html("");
                    $.ajax({
                        url: "{{route('ventas_store')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('input[name=_token]').val()
                        },
                        type: 'POST',
                        datatype: 'json',
                        data: $("#form").serialize(),
                        success: function(data) {
                            console.log(data);
                            
                            if (data != "error") {
                                $("#asiento").val(data.idasiento);
                                $("#id").val(data.idventa);
                                $("#numero").val(data.num_vent);
                                
                                if(data.respuesta=="existe"){
                                    alertas(data.mensaje);
                                }else{
                                    if(data.getSri=="ok" || data.getSri=="No"){
                                        swal("Mensaje: ","Guardado con Exito!","success");
                                    }else{
                                        swal(data.getSri);
                                    }
                                }

                               
                            } else {
                                swal("Error! No guardo correctamente, intente de nuevo");
                            }

                        },
                        error: function(data) {
                            console.log(data.responseText);
                        }
                    });
                } else {

                    swal("Mensaje:", "Faltan pagos por ingresar", "error")
                }

            } else {
                swal("Tiene campos vacios");
                console.log($("#form").serialize());
            }

        });


        //Elimina Registro de la Tabla Forma de Pago
        function eliminar_form_pag(valor) {
            var dato_pago1 = "dato_pago" + valor;
            var nombre_pago2 = 'visibilidad_pago' + valor;
            alert("entra");
            document.getElementById(dato_pago1).style.display = 'none';
            document.getElementById(nombre_pago2).value = 0;
        }

        function revisar_componentes(e, id) {
            metodo = $('#id_tip_pago' + id).val();
            if (metodo == 1) {
                $("#tipo_tarjeta" + id).prop('disabled', true);
                $("#numero_pago" + id).prop('disabled', true);
                $("#id_banco_pago" + id).prop('disabled', true);
                $("#id_cuenta_pago" + id).prop('disabled', true);
                //$("#fi"+id).prop('disabled', true);
                revision_total(id);
            } else if (metodo == 2) {
                $("#tipo_tarjeta" + id).prop('disabled', true);
                $("#numero_pago" + id).prop('disabled', false);
                $("#id_banco_pago" + id).prop('disabled', false);
                $("#id_cuenta_pago" + id).prop('disabled', false);
                //$("#fi"+id).prop('disabled', true);
                revision_total(id);
            } else if (metodo == 3) {
                $("#tipo_tarjeta" + id).prop('disabled', true);
                $("#numero_pago" + id).prop('disabled', false);
                $("#id_banco_pago" + id).prop('disabled', false);
                $("#id_cuenta_pago" + id).prop('disabled', false);
                //$("#fi"+id).prop('disabled', true);
                revision_total(id);
            } else if (metodo == 4) {
                $("#tipo_tarjeta" + id).prop('disabled', false);
                $("#numero_pago" + id).prop('disabled', false);
                $("#id_banco_pago" + id).prop('disabled', false);
                $("#id_cuenta_pago" + id).prop('disabled', false);
                //$("#fi"+id).prop('disabled', false);
                revision_total(id);
            } else if (metodo == 5) {
                $("#tipo_tarjeta" + id).prop('disabled', false);
                $("#numero_pago" + id).prop('disabled', false);
                $("#id_banco_pago" + id).prop('disabled', false);
                $("#id_cuenta_pago" + id).prop('disabled', false);
                //$("#fi"+id).prop('disabled', false);
                revision_total(id);
            } else if (metodo == 6) {
                $("#tipo_tarjeta" + id).prop('disabled', false);
                $("#numero_pago" + id).prop('disabled', false);
                $("#id_banco_pago" + id).prop('disabled', false);
                $("#id_cuenta_pago" + id).prop('disabled', false);
                //$("#fi"+id).prop('disabled', false);
                revision_total(id);
            }
        }


        function soloNumeros(e) {
            // capturamos la tecla pulsada
            var teclaPulsada = window.event ? window.event.keyCode : e.which;

            // capturamos el contenido del input
            var valor = e.value;

            // 45 = tecla simbolo menos (-)
            // Si el usuario pulsa la tecla menos, y no se ha pulsado anteriormente
            // Modificamos el contenido del mismo a??adiendo el simbolo menos al
            // inicio
            //console.log("indexof", valor);
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

        function revision_total(id) {

            var valor = $('#valor' + id).val();
            ntotal = valor * 1;
            $('#valor_base' + id).val(ntotal.toFixed(2));

        }

        function redondea_valor_base(e, id, val) {
            return parseFloat(e.value).toFixed(val);
        }

        function reloadCliente() {
            if (!existeCliente) {
                //swal("epa");
            }
        }

        function goBack() {
            location.href = "{{ route('venta_index') }}";
        }

        function crear_factura_venta() {

            //$('#crear_factura').submit();
            var valor_total = $('#total1').val();

            var formulario = document.forms["crear_factura"];

            //Datos Cabecera Factura
            var divisas = formulario.divisas.value;

            //Datos Generales
            var id_emp = formulario.id_empresa.value;
            var sucurs = formulario.sucursal.value;
            var punt_emision = formulario.punto_emision.value;


            //Datos Paciente
            var cedula = formulario.ced_paciente.value;
            var nombre_paciente = formulario.nomb_paciente.value;
            var seguro_paciente = formulario.id_seguro.value;
            var proced = formulario.procedimiento.value;
            var fech_proced = formulario.fecha_proced.value;


            //Datos Clientes
            var cliente = formulario.cliente.value;
            var ruc_ced_cliente = formulario.ruc_cedula.value;
            var direccion = formulario.direccion.value;
            var ciud_cliente = formulario.ciudad.value;
            var email_cliente = formulario.email.value;
            var telf_cliente = formulario.telefono.value;


            //Datos Recaudador
            var recaud = formulario.recaudador.value;
            var ced_recaudador = formulario.cedula_recaudador.value;

            //Detalle de Asiento
            var det_asiento = formulario.nota.value;

            var msj = "";


            if (divisas == "") {
                msj += "Por favor,Selecione la divisa\n";
            }


            //Datos Generales

            if (id_emp == "") {
                msj += "Por favor, Seleccione la Empresa\n";
            }

            if (sucurs == "") {
                msj += "Por favor, Seleccione la Sucursal\n";
            }

            if (punt_emision == "") {
                msj += "Por favor, Seleccione el Punto de Emision\n";
            }


            //Paciente
            if (cedula == "") {
                msj += "Por favor,Ingrese la cedula del paciente\n";
            }
            if (nombre_paciente == "") {
                msj += "Por favor,Ingrese el nombre del paciente\n";
            }
            if (seguro_paciente == "") {
                msj += "Por favor,Seleccione el seguro paciente\n";
            }
            if (proced == "") {
                msj += "Por favor,Ingrese los Procedimientos del paciente\n";
            }
            if (fech_proced == "") {
                msj += "Por favor,Seleccione la fecha de procedimientos\n";
            }

            //Cliente
            if (cliente == "") {
                msj += "Por favor,Selecione el cliente\n";
            }
            if (ruc_ced_cliente == "") {
                msj += "Por favor,Ingrese el ruc del cliente\n";
            }
            if (direccion == "") {
                msj += "Por favor,Ingrese la direccion del cliente\n";
            }
            if (ciud_cliente == "") {
                msj += "Por favor,Ingrese la ciudad del cliente\n";
            }
            if (email_cliente == "") {
                msj += "Por favor,Ingrese el email del cliente\n";
            }
            if (telf_cliente == "") {
                msj += "Por favor,Ingrese el telefono del cliente\n";
            }

            //Vendedor/Recaudador
            if (recaud == "") {
                msj += "Por favor,Selecione el recaudador\n";
            }
            if (ced_recaudador == "") {
                msj += "Por favor,Ingrese la cedula del recaudador\n";
            }

            //Detalle Asiento
            if (det_asiento == "") {
                msj += "Por favor,Ingrese el detalle del asiento\n";
            }

            if (msj == "") {

                if (valor_total > 0) {
                    $.ajax({
                        type: 'post',
                        url: "{{route('ventas_store')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('input[name=_token]').val()
                        },
                        datatype: 'json',
                        data: $("#crear_factura").serialize(),
                        success: function(data) {
                            //console.log(data);

                            swal({
                                    title: `{{trans('proforma.GuardadoCorrectamente')}}`,
                                    buttons: true,
                                })
                                .then((value) => {
                                    location.href = "{{route('venta_index')}}";
                                });

                        },
                        error: function(data) {
                            console.log(data);
                        }
                    })
                } else {

                    swal({
                        title: "Calcular el total a pagar",
                        buttons: true,
                    });

                }
            } else {
                alert(msj);
            }

        }


        //Completa 2 Decimales a la izquierda
        function completa_decimales(elemento, id, nDec) {

            var cod = $('#codigo' + id).val();
            var nomb = $('#nombre' + id).val();
            var num = elemento.value;

            var n = parseFloat(elemento.value);
            var s;
            //n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
            //s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
            s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
            s = s.substr(0, s.indexOf(".") + nDec + 1);


            if ((cod.length == 0) && (nomb.length == 0)) {
                alert("Debe ingresar el c??digo, nombre del producto");
                $('#cantidad' + id).val("0.00");
                $('#desc' + id).val("0.00");
                $('#extendido' + id).val("0.00");
                return false;
            }


            if (num.length == 0) {

                alert("Cantidad no permitida");
                $('#cantidad' + id).val("0");
                $('#desc' + id).val("0");
                $('#extendido' + id).val("0");

                return false;
            }

            $('#cantidad' + id).val(s);

        }

        //Completa 2 decimales a la izquierda y redondea el valor a dos decimales
        function redondea_precio(elemento, id, nDec) {

            var n = parseFloat(elemento.value);
            var s;

            n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
            s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
            s = s.substr(0, s.indexOf(".") + nDec + 1);

            $('#precio' + id).val(s);


        }

        //Redondea descuento a 2 decimales
        function redondea_descuento(elemento, id, nDec) {

            var n = parseFloat(elemento.value);
            var s;

            n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
            s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
            s = s.substr(0, s.indexOf(".") + nDec + 1);

            $('#desc_porcentaje' + id).val(s);
        }

        //Valida el valor Ingresado en el Campo Cantidad
        function validarcantidad(elemento, id) {

            var cod = $('#codigo' + id).val();
            var nomb = $('#nombre' + id).val()

            if ((cod.length == 0) && (nomb.length == 0)) {
                alert("Debe ingresar el c??digo, nombre del producto");
                $('#cantidad' + id).val("0");
                $('#desc' + id).val("0");
                $('#extendido' + id).val("0");
                return false;
            }

            var num = elemento.value;
            if (num.length == 0) {

                alert("Cantidad no permitida");
                $('#cantidad' + id).val("0");
                $('#desc' + id).val("0");
                $('#extendido' + id).val("0");

                return false;
            }

            var st = $('#stock' + id).val();
            var nu = parseInt(elemento.value, 10);

            if (nu > st) {
                alert("Cantidad no debe ser mayor al stock");
                $('#cantidad' + id).val("0");
                $('#total_acum' + id).val('');
            }


            var numero = parseInt(elemento.value, 10);
            //Validamos que se cumpla el rango
            if (numero < 1 || numero > 999999999) {
                alert("Cantidad no permitida");
                $('#cantidad' + id).val("0");
                return false;
            }

            $('#total_acum' + id).val(numero);

            return true;
        }

        //No usada por el momento
        function validartotal(elemento, id) {
            var numero = parseInt(elemento.value, 10);
            //Validamos que se cumpla el rango
            if (numero < 1 || numero > 999999999) {
                alert("Total no permitido");
                $('#total' + id).val("");
                return false;
            }
            return true;
        }


        //Calcula el Total
    

        function validardescuento(elemento, id) {

            var desc = elemento.value;
            if (desc.length == 0) {

                alert("Rango de descuento permitido (0% - 100%)");
                $('#desc_porcentaje' + id).val("0");
                $('#desc' + id).val("0");
                return false;
            }

            var numero = parseInt(elemento.value, 10);
            //Validamos que se cumpla el rango
            if (numero < 0 || numero > 100) {
                alert("Rango de descuento permitido (0% - 100%)");
                $('#desc_porcentaje' + id).val("0");
                $('#desc' + id).val("0");
                return false;
            }
            return true;
        }

        //Obtengo el Ruc o Cedula del Cliente Seleccionado
        $("#cliente").change(function() {
            $.ajax({
                type: 'post',
                url: "{{route('ventas_buscar_identificacion')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#cliente"),
                success: function(data) {
                    $('#ruc_cedula').val(data.client_identificacion);
                    $('#direccion').removeAttr('disabled');
                    $('#direccion').val(data.client_direccion);
                    $('#identif_cliente').val(data.client_identificacion);
                    $('#telefono').val(data.client_telefono);
                    $('#email').val(data.client_email);
                    $('#ciudad').val(data.client_ciudad);
                },
                error: function(data) {
                    console.log(data);
                }
            })
        });

        //Obtengo datos del vendedor de la Tabla Usuarios
        /*$("#vendedor").change(function(){
            $.ajax({
                type: 'post',
                url:"{{route('vendedor.identificacion')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: $("#vendedor"),
                success: function(data){
                    $('#cedula_vendedor').val(data.vendedor_cedula);
                },
                error: function(data){
                    console.log(data);
                }
            })
        });*/

        $("#vendedor").change(function() {
            $('#cedula_vendedor').val($('option:selected', $(this)).data("id"));
        });
        //Obtengo datos del Recaudador de la Tabla Usuarios
        $("#recaudador").change(function() {
            $.ajax({
                type: 'post',
                url: "{{route('recaudador.identificacion')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#recaudador"),
                success: function(data) {
                    $('#cedula_recaudador').val(data.recaudador_cedula);
                },
                error: function(data) {
                    console.log(data);
                }
            })
        });

        //Actualiza la direccion del Cliente
        $("#direccion").change(function() {
            $.ajax({
                type: 'post',
                url: "{{route('update_direccion_client')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                //data: $("#identif_cliente"),
                data: {
                    'ident_cliente': $("#identif_cliente").val(),
                    'direc_cliente': $("#direccion").val()
                },
                success: function(data) {
                    //console.log(data);
                },
                error: function(data) {
                    console.log(data);
                }
            })
        });

        function verificar_stock(e) {

            //console.log($(e).parent().parent().find('.cneto').val());
            // var cantidad = $(e).parent().parent().find('.cneto').val();
            // var bodega = $(e).parent().parent().find('.bodega').val();
            // var producto = $(e).parent().parent().find('.codigo_producto').val();
            // console.log("Hola");

            // $.ajax({
            //     type: 'post',
            //     url: "{{route('ventas.inventariable')}}",
            //     headers: {
            //         'X-CSRF-TOKEN': $('input[name=_token]').val()
            //     },
            //     datatype: 'json',
            //     data: {
            //         producto: producto,
            //     },
            //     success: function(data) {
            //         if (data >= 1 && bodega != "" && cantidad > 0) {
            //             $.ajax({
            //                 type: 'post',
            //                 url: "{{route('ventas.stock')}}",
            //                 headers: {
            //                     'X-CSRF-TOKEN': $('input[name=_token]').val()
            //                 },
            //                 datatype: 'json',
            //                 data: {
            //                     producto: producto,
            //                     bodega: bodega,
            //                 },
            //                 success: function(data) {
            //                     //console.log('data stock',data.length);
            //                     if (data.length > 0) {
            //                         //console.log(parseInt(data[0].existencia), parseInt(cantidad));
            //                         if (parseInt(data[0].existencia) < parseInt(cantidad)) {
            //                             swal("No tiene existencia en dicha bodega");
            //                             //$(".btn_add").attr("disabled", true);
            //                         } else {
            //                             // $(".btn_add").attr("disabled", false);
            //                         }
            //                     } else {
            //                         swal("El producto no existe en dicha bodega");
            //                         // $(".btn_add").attr("disabled", true);
            //                     }
            //                 },
            //                 error: function(data) {
            //                     console.log(data.responseText);
            //                 }
            //             })
            //         }
            //     },
            //     error: function(data) {
            //         console.log(data.responseText);
            //     }
            // });
        }


        //Obtengo el Codigo del Producto por el nombre del producto Ingresado
        function cambiar_nombre(id) {

            $.ajax({
                type: 'post',
                url: "{{route('ventas_buscar_codigo')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'nombre': $("#nombre" + id).val()
                },
                success: function(data) {
                    //console.log(data);
                    $('#codigo' + id).val(data.cod_product);
                    $('#cost_prod' + id).val(data.cost_vent);
                    //$('#iva_obt'+id).val(data.iva_prod);
                    if (data.iva_prod == '1') {
                        $('#iva' + id).prop("checked", true);
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            })
        }


        //Obtengo el Nombre del Producto por el codigo del producto Ingresado
        function cambiar_codigo(id) {
            $.ajax({
                type: 'post',
                url: "{{route('ventas_buscar_nombre2')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'codigo': $("#codigo" + id).val()
                },
                success: function(data) {
                    $('#nombre' + id).val(data);
                },
                error: function(data) {
                    console.log(data);
                }
            })
        }


        /* function obtener_num_factura(){
             $.ajax({
                 url:"{{route('numero_factura')}}",
                 type: 'get',
                 datatype: 'json',
                 success: function(data){
                    //console.log(data);
                    $('#nfactura').val(data);
                 },
                 error: function(data){
                     console.log(data);
                 }
             })
         }*/


        function limpiar() {

            //obtenemos la fecha actual
            var now = new Date();
            var day = ("0" + now.getDate()).slice(-2);
            var month = ("0" + (now.getMonth() + 1)).slice(-2);
            var today = now.getFullYear() + "-" + (month) + "-" + (day);
            var fe_proced = new Date('Y-m-d H:i:s');
            $("#fecha").val(today);
            $("#fecha_proced").val(fe_proced);


        }

        //Completa la cedula del Paciente por minimo 3 digitos
        $(".ced_paciente").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "{{route('autocomple_paciente_cedula')}}",
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


        //Obtengo datos del paciente por la cedula
        function obtener_datos_paciente() {
            $.ajax({
                type: 'post',
                url: "{{route('obtener_info_paciente')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                type: 'POST',
                datatype: 'json',
                data: {
                    'ced_paciente': $("#ced_paciente").val()
                },
                success: function(data) {
                    //console.log(data);
                    if (data.texto != '') {
                        $('#nomb_paciente').val(data.texto);
                    }

                    if (data.id_seg != '') {
                        $('#id_seguro').val(data.id_seg);
                    }

                    /*if(data.trim() == 'error'){
                      alert("No existe el Paciente");
                      $('#ced_paciente').val('');
                      $('#nomb_paciente').val('');
                      $('#id_seguro').val('');
                    }*/

                },
                error: function(data) {
                    console.log(data);
                }
            })
        }

        function obtener_sucursal() {

            var id_seleccionado = $("#id_empresa").val();

            $.ajax({
                type: 'post',
                url: "{{route('sucursal.empresa')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'id_emp': id_seleccionado
                },
                success: function(data) {
                    //console.log(data);

                    if (data.value != 'no') {
                        if (id_seleccionado != 0) {
                            $("#sucursal").empty();

                            $.each(data, function(key, registro) {
                                $("#sucursal").append('<option value=' + registro.id + '>' + registro.codigo_sucursal + '</option>');

                            });
                        } else {
                            $("#sucursal").empty();

                        }

                    }
                },
                error: function(data) {
                    console.log(data);
                }
            })



        }

        function obtener_caja() {

            var id_sucursal = $("#sucursal").val();

            $.ajax({
                type: 'post',
                url: "{{route('caja.sucursal')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'id_sucur': id_sucursal
                },
                success: function(data) {
                    //console.log(data);

                    if (data.value != 'no') {
                        if (id_sucursal != 0) {
                            $("#punto_emision").empty();

                            $.each(data, function(key, registro) {
                                $("#punto_emision").append('<option value=' + registro.id + '>' + registro.codigo_sucursal + '-' + registro.codigo_caja + '</option>');

                            });
                        } else {
                            $("#punto_emision").empty();

                        }

                    }
                },
                error: function(data) {
                    console.log(data);
                }
            })

        }


        $('#busqueda').click(function(event) {

            id = document.getElementById('contador').value;

            var midiv = document.createElement("tr")
            midiv.setAttribute("id", "dato" + id);


            midiv.innerHTML = '<td><input name="codigo' + id + '" class="codigo" id="codigo' + id + '" style="width: 110px;" onchange="cambiar_codigo(' + id + ')"/><input type="hidden" id="visibilidad' + id + '" name="visibilidad' + id + '" value="1"><input type="text" class="hidden" id="id_prod' + id + '" name="id_prod' + id + '"><input type="text" class="hidden" name="cost_prod' + id + '" id="cost_prod' + id + '"></td><td><input name="nombre' + id + '" class="nombre" id="nombre' + id + '"  onchange="cambiar_nombre(' + id + ')"></td><td> <input type="text" style="width: 110px;" id="cantidad' + id + '" value="0.00" onkeyup="total_calculo(' + id + ')" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" onchange="return completa_decimales(this,' + id + ',2);" name="cantidad' + id + '"></td><td><input type="text" style="width: 110px;" id="precio' + id + '" name="precio' + id + '" value="0.00" onkeyup="total_calculo(' + id + ')" onchange="return redondea_precio(this,' + id + ',2);"></td><td><input type="text" style="width: 110px;" id="desc_porcentaje' + id + '" name="desc_porcentaje' + id + '" value="0.00" onkeyup="total_calculo(' + id + ')" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"  onchange="return redondea_descuento(this,' + id + ',2);"></td><td><input type="text" style="width: 110px;" id="desc' + id + '" name="desc' + id + '"  value="0.00" disabled><input type="text" class="hidden" name="desc1' + id + '" id="desc1' + id + '"></td><td><input type="text" name="extendido' + id + '" id="extendido' + id + '" value="0.00"  value="" disabled><input type="text" class="hidden" name="extendido1' + id + '" id="extendido1' + id + '"></td><td><input type="checkbox" id="iva' + id + '" name="iva' + id + '" disabled></td><td><button type="button" onclick="eliminar_registro(' + id + ')" class="btn btn-warning btn-margin">Eliminar</button></td>';
            document.getElementById('crear').appendChild(midiv);
            id = parseInt(id);
            id = id + 1;
            document.getElementById('contador').value = id;

            //Completa el codigo del Producto al Ingresar
            $(".codigo").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "{{route('ventas_completa_codigo')}}",
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                selectFirst: true,
                minLength: 1,
            });
            //Completa el nombre  del Producto al Ingresar
            $(".nombre").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "{{route('ventas_buscar_nombre')}}",
                        //headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                        dataType: "json",
                        //type: 'post',
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                selectFirst: true,
                minLength: 3,
            });


        });

        //Elimina Registro de la Tabla Productos

    </script>











@endsection