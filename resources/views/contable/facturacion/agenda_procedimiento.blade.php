@extends('contable.facturacion.base')
@section('action-content')

<style type="text/css">
        .ui-autocomplete
        {
            overflow-x: hidden;
            max-height: 400px;
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
        .disableds{
            display: none;
        }
        .disableds2{
            display: none;
        }
        .disableds3{
            display: none;
        }
        .has-cc span img{
            width:2.775rem;
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
        .has-cc .form-control-cc2{
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
        .cvc_help{
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
        .cabecera{
            background-color: #3c8dbc;
            border-radius: 8px;
            color: white;
        }
        .borde{
            border:2px solid #3c8dbc;
        }
        .card-header{
            border-radius: 6px 6px 0 0;
            background-color: #3c8dbc;
            border-color: #b2b2b2;
            padding: 8px;
            font-family: 'Roboto', sans-serif;
        }
</style>

    <section class="content">

    <div class="modal fade" id="seguro" tabindex="1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog modal-lg" role="document">
       <div class="modal-content" style="width: 95%;">
       </div>
      </div>
    </div>

    <div id="alerta_guardado" class="alert alert-success alerta_guardado alert-dismissable" role="alert" style="display:none;">
         <button type="button" class="close" data-dismiss="alert">&times;</button>
           Guardado Correctamente
    </div>
    
    <div id="msj_ingreso" class="alert alert-success alerta_correcto alert-dismissable col-10" role="alert" style="display:none;font-size: 14px">
            Ingrese Cedula y Ruc
    </div>
    
    
    <div class="box box-primary box-solid">
        <div class="header box-header with-border" >
            <div class="box-title col-md-12" >
                <b style="font-size: 16px;">Crear Recibo de Cobro</b>
            </div>
        </div>
        <div class="box-body">
            <a href="#" target="_blank" id="ride"></a>
            <form class="form-vertical size_text" id="crear_form" method="POST" autocomplete="off">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="empresa" id="empresa" value="{{$empresa->id}}">
                <input type="hidden" name="id_agenda" id="id_agenda" value="{{$agenda->id}}">
                <input type="hidden" name="id_doctor" id="id_doctor" value="{{$agenda->id_doctor1}}">
                <input type="hidden" name="tipo_dato" value="{{$tipo}}">
                <input type="hidden" value="{{$iva->iva}}" name="ivareal" id="ivareal" class="form-control" >


                <div class="col-md-12">&nbsp;</div>

                <div class="col-sm-12">
                    <div class="card-header">
                        <b style="color: white">{{trans('contableM.DATOSGENERALES')}}</b>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">&nbsp;</div>
                        <!--Listado de Empresas a Facturar-->
                        <div class="col-md-5 col-xs-6">
                            <label for="id_empresa" class="control-label">{{trans('contableM.empresa')}}</label>
                             <select class="form-control input-sm" name="id_empresa" id="id_empresa"  onchange="" required>
                                @foreach($empresas as $value)
                                    <option  @if($agenda->id_empresa == $value->id) selected @endif @if(old('id_empresa')==$value->id) selected @endif value="{{$value->id}}">{{$value->razonsocial}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 col-xs-2" style="padding-left: 12px;padding-right: 2px;">
                            <label  for="fecha_emision" class="control-label">Fecha de Emisión:</label>
                            <div class="input-group date">
                                <input  type="date" class="form-control" name="fecha" id="fecha"
                                value="" autocomplete="off">
                                <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('fecha').value = '';"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-12 col-xs-12"></div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-sm-12" style="padding-left: 30px">
                            <div class="card-header">
                              <b style="font-size: 12px;color: white">DATOS PACIENTE</b>
                            </div>
                            <div class="card-body">
                                <div class="col-md-12">&nbsp;</div>
                                <div class="col-md-6 col-xs-6">
                                    <label for="id" class="col-md-2">Cédula</label>
                                    <div class="input-group  col-md-9">
                                        <input id="idpaciente" maxlength="10" type="text" class="form-control" name="idpaciente"
                                        value="@if($paciente != Array() && !is_null($paciente)){{$paciente->id}}@elseif($id != 0){{$id}}@else{{old('idpaciente')}}@endif" placeholder="Cédula" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                        <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('idpaciente').value = '';"></i>
                                        </div>
                                    </div>
                                </div>
                                <!--Nombre Paciente-->
                                <div class="col-md-6 col-xs-6">
                                    <label for="nombres" class="col-md-2">{{trans('contableM.paciente')}}</label>
                                    <div class="input-group col-md-9">
                                        <input  type="text" class="form-control input-sm" name="nombres" id="nombres"
                                        value="@if($paciente != Array()){{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif {{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif @endif" placeholder="Apellidos y Nombres" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                        <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('nombres').value = '';"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" style="padding-top: 3px"></div>
                                <!--seguro-->
                                <div class="col-md-6 col-xs-6" >
                                    <label for="id_seguro" class="col-md-2 control-label">{{trans('contableM.Seguro')}}</label>
                                    <div class="input-group col-md-9">
                                        <select class="form-control input-sm" name="id_seguro" id="id_seguro" onchange="seguro(),cargar_nivel()">
                                            <option value="">Seleccione ...</option>
                                            @foreach($seguros as $seguro)
                                                <option  @if($agenda->id_seguro == $seguro->id) selected @endif @if(old('id_seguro')==$seguro->id) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!--Nivel Seguro-->
                                <div id="ident_nivel" class="col-md-6 col-xs-3" >
                                    <label for="id_nivel" class="col-md-2 control-label">Nivel</label>
                                    <div class="input-group col-md-9">
                                        <select name="id_nivel" id="id_nivel" class="form-control">
                                           <option value="">Seleccione...</option>
		                                </select>
                                    </div>
                                </div>
                                <div  id="div_copago" class="col-md-6 col-xs-3" style="@if($agenda->seguro->copago == 0) display: none; @endif">
                                    <label for="copago" class="col-md-2 control-label">Posee ODA</label>
                                    <div class="input-group col-md-9">
                                        <input id="copago"  type="checkbox"  name="copago" placeholder="Posee ODA" onclick="validar_copago();" >
                                    </div>
                                </div>
                                <div class="col-md-12" style="padding-top: 3px"></div>
                                <div id="dato_copago" class="col-md-6 col-xs-6 oculto ">
                                    <label for="valor_copago" class="col-md-2">Valor Copago($)</label>
                                    <div class="input-group col-md-9">
                                        <input  type="text" class="form-control input-sm" name="valor_copago" id="valor_copago"
                                        value="" placeholder="Valor de Copago" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloNumeros(event);">
                                        <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('valor_copago').value = '';"></i>
                                        </div>
                                    </div>
                                </div>

                                <div id="div_copago2" class="col-md-6 col-xs-3" style="@if($agenda->seguro->copago == 0) display: none; @endif">
                                    <label for="numero_oda" class="col-md-2 control-label">No. ODA(#)</label>
                                    <div class="input-group col-md-9">
                                        <input  type="text" class="form-control input-sm" name="numero_oda" id="numero_oda"  value="{{old('numero_oda')}}" placeholder="numero_oda" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloNumeros(event);">
                                        <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('numero_oda').value = '';"></i>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-12 col-xs-12"></div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-sm-12" style="padding-left: 30px">
                            <div class="card-header">
                              <b style="font-size: 12px;color: white">DATOS CLIENTE</b>
                            </div>
                            <div class="card-body">
                                <div class="col-md-12">&nbsp;</div>
                                <div class="col-md-6 col-xs-6">
                                    <label for="cedula" class="col-md-2 control-label">{{trans('contableM.identificacion')}}</label>
                                    <div class="input-group col-md-9">
                                        <select id="tipo_identificacion" name="tipo_identificacion" onchange="borrar()" class="form-control" autofocus="">
                                            <option value="4">{{trans('contableM.ruc')}}</option>
                                            <option selected="selected" value="5">{{trans('contableM.cedula')}}</option>
                                            <option value="6">{{trans('contableM.pasaporte')}}</option>
                                            <option value="8">CEDULA EXTRANJERA</option>
                                        </select>
                                    </div>
                                </div>
                                <!--Ruc/Cedula-->
                                <div class="col-md-6 col-xs-6">
                                    <label for="cedula" class="col-md-2 control-label">Ruc/Cédula</label>
                                    <div class="input-group col-md-9">
                                        <input id="cedula" maxlength="13" type="text" class="form-control" name="cedula"
                                        value="@if($ct_cliente != Array() && !is_null($ct_cliente)){{$ct_cliente->identificacion}}@else{{old('cedula')}}@endif" placeholder="Ruc/Cédula" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onchange="buscar();" required>
                                        <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('cedula').value = '';"></i>
                                        </div>
                                    </div>
                                </div>
                                <!--Razon Social-->
                                <div class="col-md-6 col-xs-6">
                                    <label for="razon_social" class="col-md-2 control-label">Razon Social</label>
                                    <div class="input-group col-md-9">
                                        <input type="text" class="form-control input-sm" name="razon_social" id="razon_social"
                                        value="@if(!is_null($ct_cliente)){{$ct_cliente->nombre}}@endif" placeholder="Razon Social" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                        <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('razon_social').value = '';"></i>
                                        </div>
                                    </div>
                                </div>
                                <!--Ciudad-->
                                <div class="col-md-6 col-xs-6">
                                    <label for="ciudad" class="col-md-2 control-label">{{trans('contableM.ciudad')}}</label>
                                    <div class="input-group col-md-9">
                                        <input  type="text" class="form-control input-sm" name="ciudad" id="ciudad" value="@if(!is_null($ct_cliente)){{$ct_cliente->ciudad_representante}}@endif" placeholder="Ciudad" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                        <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('ciudad').value = '';"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" style="padding-top: 3px"></div>
                                <!--Direccion-->
                                <div class="col-md-6 col-xs-6">
                                    <label for="direccion" class="col-md-2 control-label">{{trans('contableM.direccion')}}</label>
                                    <div class="input-group col-md-9">
                                        <input  type="text" class="form-control input-sm" name="direccion" id="direccion" value="@if(!is_null($ct_cliente)){{$ct_cliente->direccion_representante}}@endif" placeholder="Dirección" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                        <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('direccion').value = '';"></i>
                                        </div>
                                    </div>
                                </div>
                                <!--Telefono-->
                                <div class="col-md-6 col-xs-6">
                                    <label for="telefono" class="col-md-2 control-label">Teléfono</label>
                                    <div class="input-group col-md-9">
                                        <input type="text" class="form-control input-sm" name="telefono" id="telefono" value="@if(!is_null($ct_cliente)){{$ct_cliente->telefono1_representante}}@endif" placeholder="Teléfono" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                        <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('telefono').value = '';"></i>
                                        </div>
                                    </div>
                                </div>
                                <!--Email-->
                                <div class="col-md-6 col-xs-6">
                                    <label for="email" class="col-md-2 control-label">Mail</label>
                                    <div class="input-group col-md-9">
                                        <input type="text" class="form-control input-sm" name="email" id="email" value="@if(!is_null($ct_cliente)){{$ct_cliente->email_representante}}@endif" placeholder="Mail" >
                                        <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('email').value = '';"></i>
                                        </div>
                                    </div>
                                </div>
                                <!--Observacion-->
                                <div class="col-md-12" style="margin-top: 5px;">
                                    <label for="observacion" class="col-md-1 control-label">{{trans('contableM.observaciones')}}</label>
                                    <div class="input-group col-md-10">
                                        <textarea class="form-control input-md" name="observacion" id="observacion"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-12 col-xs-12"></div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-sm-12" style="padding-left: 30px">
                            <div class="card-header">
                              <b style="font-size: 12px;color: white">PROCEDIMIENTOS</b>
                            </div>

                            <div class="card-body">
                                <div class="col-md-12">&nbsp;</div>
                                <div class="table-responsive col-md-12">
                                <!--input name="contador_procedimiento" id="contador_procedimiento" type="hidden" value="{{sizeof($procedimientos)}}"-->
                                <input name="contador_procedimiento" id="contador_procedimiento" type="hidden" value="0">
                                <input name="contador_temporal" id="contador_temporal" type="hidden" value="0">

                                <table id="example1" role="grid" aria-describedby="example2_info" style="width: 100%;">
                                    <thead style="background-color: #FFF3E3">
                                        <tr>
                                           <th style="width: 20%; text-align: center;">{{trans('contableM.codigo')}}</th>
                                           <th style="width: 20%; text-align: center;">{{trans('contableM.Procedimiento')}}</th>
                                           <th style="width: 10%; text-align: center;">{{trans('contableM.cantidad')}}</th>
                                           <th style="width: 10%; text-align: center;">{{trans('contableM.precio')}}</th>
                                           <th style="width: 5%; text-align: center;">% - $</th>
                                           <th style="width: 5%; text-align: center;">Cobrar Paciente</th>
                                           <th style="width: 5%; text-align: center;">% - $</th>
                                           <th style="width: 5%; text-align: center;">{{trans('contableM.descuento')}}</th>
                                           <th style="width: 10%; text-align: center;">{{trans('contableM.iva')}}</th>
                                           <th style="width: 10%; text-align: center;">{{trans('contableM.precioneto')}}</th>
                                           <th style="width: 10%; text-align: center;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="agregar_procedimiento" style="text-align: center;">
                                    <!-- aqui van los procedimientos, debo de traerlos por query los producots y todo -->
                                    </tbody>
                                    <tfoot style="text-align: right;">
                                        <tr>
                                            <td colspan="8"><b>{{trans('contableM.total')}}</b></td>
                                            <td><input type="text" name="total_pro" id="total_pro" readonly="readonly" ></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-2 col-xs-2">
                                        <div class="box-footer" >
                                          <button type="button" id="btn_procedimiento" class="btn btn-primary size_text">
                                          Agregar
                                          </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-12 col-xs-12"></div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-sm-12" style="padding-left: 30px">
                            <div class="card-header">
                              <b style="font-size: 12px;color: white">{{trans('contableM.formasdepago')}}</b>
                            </div>
                            <div class="card-body">
                                <div class="col-md-12">&nbsp;</div>
                                <div class="col-md-4 col-xs-6">
                                    <label for="pago" class="col-md-6 control-label">Valor a Pagar</label>
                                    <div class="input-group col-md-6">
                                        <input type="hidden" name="total_sin_tarjeta" id="total_sin_tarjeta" value="0">
                                        <input readonly="readonly" onkeypress="return soloNumeros(event);" type="text" class="form-control input-sm" name="pago" id="total_final1" value="0" placeholder="Pago">
                                        <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('pago').value = '';"></i>
                                        </div>
                                    </div>
                                </div>
                                 <div class="form-group col-md-6">
                                    <label for="pago" class="col-md-2 control-label">Caja de Cobro:</label>
                                    <div class="input-group col-md-10">
                                        <select  id="caja" name="caja" class="form-control"required>
                                            <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('caja').value = '';"></i>
                                          <option value="">Seleccione...</option>
                                          <option value="Torre 1">Torre 1</option>
                                          <option value="Torre 2">Torre 2</option>
                                        </select>
                                      </div>
                                  </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="height: 15px;">&nbsp;</div>
                        <div class="col-md-12">
                            <div class="table-responsive col-md-12">
                                <input name="contador_pago" id="contador_pago" type="hidden" value="0">
                                <table id="example1" role="grid" aria-describedby="example2_info">
                                    <thead style="background-color: #FFF3E3">
                                        <tr>
                                           <th style="width: 4%; text-align: center;">{{trans('contableM.Metodo')}}</th>
                                           <th style="width: 10%; text-align: center;">{{trans('contableM.fecha')}}</th>
                                           <th style="width: 7%; text-align: center;">{{trans('contableM.tipo')}}</th>
                                           <th style="width: 10%; text-align: center;">Número Transaccion</th>
                                           <th style="width: 7%; text-align: center;">{{trans('contableM.banco')}}</th>
                                           <th style="width: 7%; text-align: center;">Posee Fee</th>
                                           <th style="width: 7%; text-align: center;">{{trans('contableM.ValorBase')}}</th>
                                           <th style="width: 7%; text-align: center;">{{trans('contableM.valor')}}</th>
                                           <th style="width: 7%; text-align: center;">{{trans('contableM.accion')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="agregar_pago">
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-2 col-xs-2">
                                        <div class="box-footer" >
                                          <button type="button" id="btn_pago" class="btn btn-primary size_text">
                                          Agregar
                                          </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="form-group col-md-12 col-xs-12"></div>
            <div class="form-group col-xs-6" style="text-align: center;">
                <div class="col-md-6 col-md-offset-4">
                    <button id="crear_recibo" data-loading-text="Generando..." class="btn btn-primary" onclick="crear_factura()">
                    Crear Recibo
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script src="{{ asset ("/hospital/cleave/dist/cleave.min.js")}}"></script>
<script type="text/javascript">

    //Carga Nivel del Seguro Seleccionado
    $(document).ready(function(){
        
        cargar_nivel(); 
        
    });

    function cargar_nivel(){

        var id_emp = $('#id_empresa').val();
        var xseguro = $('#id_seguro').val();
        
        $.ajax({
            type: 'post',
            url:"{{route('lista_nivel.seguro')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'id_seguro': xseguro,'id_empresa': id_emp},
            success: function(data){
                console.log(data);

                if(data.value!='no'){
                //if(data.count()>0){
                    
                    //alert("Prueba Ingreso");
                    
                    if(xseguro!= 0){

                        //Muestra el Nivel del Seguro
                        //document.getElementById("ident_nivel").style.visibility = "visible";  
                        $("#id_nivel").empty();
                        $.each(data,function(key, registro) {
                          $("#id_nivel").append('<option value='+registro.id_nivel+'>'+registro.nombre+'</option>');
                        });

                    }else{

                        $("#id_nivel").empty();
                      
                    }    

                }else{

                    //alert("Prueba Ingreso 2");
                    //Oculta el Nivel del Seguro
                    document.getElementById("ident_nivel").style.visibility = "hidden";
                    
                }
            
            },
            error: function(data){

            }
        });

    }
    
    function deducible(id){

        var nomb_ded =  $("#procedimiento"+id).val();

        //alert("paso por qui");
        id= document.getElementById('contador_procedimiento').value;
        //id= document.getElementById('contador_temporal').value;

        var midiv_pago = document.createElement("tr")
            midiv_pago.setAttribute("id","dato_procedimiento"+id);

        var txt_td = '<td><input type="text" name="cod_prod'+id+'" id="cod_prod'+id+'" style="width:80%;"></td>';
        txt_td =  txt_td +'<td><input required type="hidden" id="visibilidad_procedimiento'+id+'" name="visibilidad_procedimiento'+id+'" value="1"><input type="text" name="procedimiento'+id+'" id="procedimiento'+id+'" value="DEDUCIBLE '+'-'+' '+nomb_ded+'" style="width:100%;text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" readonly="readonly"></td>';
        txt_td = txt_td + '<td><input required type="text" value="1" name="cantidad'+id+'" id="cantidad'+id+'" style="width: 100px;" onkeypress="return soloNumeros(event);" readonly="readonly" required></td>';
        txt_td = txt_td + '<td><input required type="hidden" value="0.00" name="id_prod'+id+'" id="id_prod'+id+'" style="width: 100px;display:none;"><input required type="hidden" value="0.00" name="val_deducible'+id+'" id="val_deducible'+id+'" style="width: 100px;display:none;"><input required type="hidden" value="0.00" name="deducible_id'+id+'" id="deducible_id'+id+'" style="width: 100px;display:none;" onchange="revision_total_pro('+id+') "><input required type="text" id="valor_pro'+id+'" name="valor_pro'+id+'" style="width: 100px;"  value="0.00" onchange="resta_deducible('+id+');revision_total_pro('+id+')" onkeypress="return soloNumeros(event);"></td>';
        txt_td = txt_td + '<td><select id="tipo_cob_seguro'+id+'" name="tipo_cob_seguro'+id+'" onchange="revision_total_pro('+id+') " readonly="readonly"><option value="%">%</option><option value="$">$</option></select></td>';
        txt_td = txt_td + '<td><input required type="hidden" value="0.00" name="copago'+id+'" id="copago'+id+'" style="width: 100px;display:none;" required onchange="revision_total_pro('+id+') "><input required type="text" value="0.00" name="copagom'+id+'" id="copagom'+id+'" style="width: 100px;display:none;" required onchange="revision_total_pro('+id+') "><input required type="text" id="p_oda'+id+'" name="p_oda'+id+'" value="0.00" onchange="revision_total_pro('+id+') " onkeypress="return soloNumeros(event);" readonly="readonly"></td>';
        txt_td = txt_td + '<td><select id="tipo_desc'+id+'" name="tipo_desc'+id+'" onchange="revision_total_pro('+id+') " readonly="readonly"><option value="%">%</option><option value="$">$</option></select></td>';
        txt_td = txt_td + '<td><input required type="text" id="desc'+id+'" name="desc'+id+'" style="width: 100px;display:none;"  value="0.00" onchange="revision_total_pro('+id+') " onkeypress="return soloNumeros(event);"><input required type="text" id="p_dcto'+id+'" name="p_dcto'+id+'" value="0.00" onchange="revision_total_pro('+id+') " onkeypress="return soloNumeros(event);" readonly="readonly"></td>';
        txt_td = txt_td + '<td><input required style="text-align:center;" type="checkbox" name="iva_item'+id+'" id="iva_item'+id+'" onchange="revision_total_pro('+id+')" value="1" disabled></td>';
        txt_td = txt_td + '<td><input required type="text" id="total_procedimiento'+id+'" name="total_procedimiento'+id+'" style="width: 100px;" onkeypress="return soloNumeros(event);" onchange="return redondea_valor_base(this,'+id+',2);" readonly="readonly"></td>';
        txt_td = txt_td + '<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" onclick="eliminar_form_procedimiento('+id+')" class="btn btn-danger btn-margin btn-lg"><span class="glyphicon glyphicon-trash"></span></button></td>'

        midiv_pago.innerHTML = txt_td;

        document.getElementById('agregar_procedimiento').appendChild(midiv_pago);
        id = parseInt(id);
        id = id+1;
        document.getElementById('contador_procedimiento').value = id;
        //document.getElementById('contador_temporal').value = id;

    }


    function resta_deducible(id){

        var cant_ded  = $("#cantidad"+id).val();
        var prec_ded  = $("#valor_pro"+id).val();
        var tot_ded = cant_ded*prec_ded;
        $("#deducible_id"+id).val(tot_ded);

    }

    function soloNumeros(e)
    {
        // capturamos la tecla pulsada
        var teclaPulsada=window.event ? window.event.keyCode:e.which;

        // capturamos el contenido del input
        var valor=e.value;

        // 45 = tecla simbolo menos (-)
        // Si el usuario pulsa la tecla menos, y no se ha pulsado anteriormente
        // Modificamos el contenido del mismo añadiendo el simbolo menos al
        // inicio
        console.log("indexof",valor);
        if(teclaPulsada==45 && valor.indexOf("-")==-1)
        {
            document.getElementById("inputNumero").value="-"+valor;
        }

        // 13 = tecla enter
        // 46 = tecla punto (.)
        // Si el usuario pulsa la tecla enter o el punto y no hay ningun otro
        // punto
        if(teclaPulsada==13 || (teclaPulsada==46 && valor.indexOf(".")==-1))
        {
            return true;
        }

        // devolvemos true o false dependiendo de si es numerico o no
        return /\d/.test(String.fromCharCode(teclaPulsada));
    }


    $(document).ready(function(){
        limpiar();
        obtener_fecha();
        corregir_valores();
        totales();

    });

    function corregir_valores(){
        $(".productoid").each(function() {
            //console.log($(this).val());
            var values = valores_seguro($(this), $(this).val());
            //console.log(values);
        });
    }

    function valores_seguro(el, id_producto){
        var seguro = $('#id_seguro').val();
        $.ajax({
            type: 'post',
            url:"{{route('facturacion.valores_seguro')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'id_seguro': seguro, 'id_producto': id_producto},
            success: function(data){
                console.log(data);
                //console.log($(el).val());
                $.each(data, function (key, value) {
                    console.log("data", value);
                    console.log($(el).parent().find(".precioid").append($('<option>',
                        {
                            value: value.precio_producto,
                            text : value.precio_producto
                        }
                    )
                    ));
                    //.append(new Option("option text", "value");
                    totales();
                });
                return data;

            },
            error: function(data){
                console.log(data.responseText);

            }
        });
    }


    function totales(){
        var contador = $("#contador_procedimiento").val();
        for (let index = 0; index < contador; index++) {
            //const element = array[index];
            //
            revision_total_pro(index);
        }
    }

    function limpiar(){
        $("#datos_tarjeta_credito").hide();
        $("#datos_tarjeta_debito").hide();
        $("#datos_cheque").hide();
        $("#valor_tarjetadebito").val('');
        $("#valor_cheque").val('');
        $("#valor_efectivo").val('');
        $("#valor_tarjetacredito").val('');
        $("#numero_oda").val('0');

    }

    $('#btn_pago').click(function(event){

        id= document.getElementById('contador_pago').value;


        var midiv_pago = document.createElement("tr")
            midiv_pago.setAttribute("id","dato_pago"+id);

        var txt_td_1 =  '<td><select required name="id_tip_pago'+id+'" id="id_tip_pago'+id+'" style="width: 175px;height:25px" onchange="revisar_componentes(this,'+id+');"><option value="">Seleccione</option>@foreach($tipo_pago as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select><input required type="hidden" id="visibilidad_pago'+id+'" name="visibilidad_pago'+id+'" value="1"></td>';
        txt_td_1 = txt_td_1 + '<td><input required type="date" class="input-number" value="{{date('Y-m-d')}}" name="fecha'+id+'" id="fecha'+id+'" style="width: 110px;"></td>';
        txt_td_1 = txt_td_1 + '<td><select required id="tipo_tarjeta'+id+'" name="tipo_tarjeta'+id+'" style="width: 175px;height:25px"><option value="">Seleccione...</option> @foreach($tipo_tarjeta as $tipo_t) <option value="{{$tipo_t->id}}">{{$tipo_t->nombre}}@endforeach</select></td>';
        txt_td_1 = txt_td_1 + '<td><input required type="text" name="numero'+id+'" id="numero'+id+'" style="width: 100px;" required></td>';
        txt_td_1 = txt_td_1 + '<td><select required name="id_banco'+id+'" id="id_banco'+id+'" style="width: 175px;height:25px"><option value="">Seleccione...</option>@foreach($lista_banco as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select></td>';
        txt_td_1 = txt_td_1 + '<td><input required style="text-align:center;" type="checkbox" name="fi'+id+'" id="fi'+id+'" class="fee" data-id="'+id+'" onchange="revision_total('+id+')" value="0" ></td>';
        txt_td_1 = txt_td_1 + '<td><input required type="text" id="valor_base'+id+'" name="valor_base'+id+'" style="width: 100px;" onblur="this.value=parseFloat(this.value).toFixed(2);"  value="0" onchange="revision_total('+id+')" onkeypress="return soloNumeros(this);"></td>';
        txt_td_1 = txt_td_1 + '<td><input required type="text" id="total'+id+'" name="total'+id+'" style="width: 100px;" onkeypress="return soloNumeros(event);" onchange="return redondea_valor_base(this,'+id+',2);" readonly></td>';
        txt_td_1 = txt_td_1 + '<td><button type="button" onclick="eliminar_form_pag('+id+')" class="btn btn-danger btn-margin btn-lg"><span class="glyphicon glyphicon-trash"></span></button></td>';

        midiv_pago.innerHTML = txt_td_1;

        document.getElementById('agregar_pago').appendChild(midiv_pago);
        id = parseInt(id);
        id = id+1;
        document.getElementById('contador_pago').value = id;
    });



    $('body').on('change', '.fee', function () {
        suma_total();
    });


    $('#btn_procedimiento').click(function(event){
        //alert("paso por qui");

        id= document.getElementById('contador_procedimiento').value;
        id_temporal= document.getElementById('contador_temporal').value;

        //alert(id);

        var midiv_pago = document.createElement("tr")
            midiv_pago.setAttribute("id","dato_procedimiento"+id);

        var txt_td = '<td><input type="text" name="cod_prod'+id+'" id="cod_prod'+id+'" class="completa_cod form-control" style="width:80%"></td>';
        txt_td = txt_td +'<td><input required type="hidden" id="visibilidad_procedimiento'+id+'" name="visibilidad_procedimiento'+id+'" value="1"><input required type="hidden" id="ident_paquete'+id+'" name="ident_paquete'+id+'"><input type="text" name="procedimiento'+id+'" id="procedimiento'+id+'" class="buscador_paquetes form-control" style="width:100%;text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"></td>';
        txt_td = txt_td + '<td><input required type="text" value="1" name="cantidad'+id+'" id="cantidad'+id+'" style="width: 100px;" onkeypress="return soloNumeros(event);" onchange="revision_total_pro('+id+')" required></td>';
        txt_td = txt_td + '<td><input required type="hidden" value="0.00" name="id_prod'+id+'" id="id_prod'+id+'" style="width: 100px;display:none;"><input required type="hidden" value="0.00" name="val_deducible'+id+'" id="val_deducible'+id+'" style="width: 100px;display:none;"><input required type="text" id="valor_pro'+id+'" name="valor_pro'+id+'" style="width: 100px;"  value="0.00" onchange="revision_total_pro('+id+');" onkeypress="return soloNumeros(event);"></td>';
        txt_td = txt_td + '<td><select id="tipo_cob_seguro'+id+'" name="tipo_cob_seguro'+id+'" onchange="revision_total_pro('+id+')"><option value="%">%</option><option value="$">$</option></select></td>';
        txt_td = txt_td + '<td><input required type="hidden" value="0.00" name="copago'+id+'" id="copago'+id+'" style="width: 100px;display:none;" required onchange="revision_total_pro('+id+') "><input required type="text" value="0.00" name="copagom'+id+'" id="copagom'+id+'" style="width: 100px;display:none;" required onchange="revision_total_pro('+id+') "><input required type="text" id="p_oda'+id+'" name="p_oda'+id+'" value="100" onchange="revision_total_pro('+id+') " onkeypress="return soloNumeros(event);"></td>';
        txt_td = txt_td + '<td><select id="tipo_desc'+id+'" name="tipo_desc'+id+'" onchange="revision_total_pro('+id+') "><option value="%">%</option><option value="$">$</option></select></td>';
        txt_td = txt_td + '<td><input required type="text" id="desc'+id+'" name="desc'+id+'" style="width: 100px;display:none;"  value="0.00" onchange="revision_total_pro('+id+') " onkeypress="return soloNumeros(event);"><input required type="text" id="p_dcto'+id+'" name="p_dcto'+id+'" value="0.00" onchange="revision_total_pro('+id+') " onkeypress="return soloNumeros(event);"></td>';
        txt_td = txt_td + '<td><input required style="text-align:center;" type="checkbox" name="iva_item'+id+'" id="iva_item'+id+'" onchange="revision_total_pro('+id+')" value="1" ></td>';
        txt_td = txt_td + '<td><input required type="text" id="total_procedimiento'+id+'" name="total_procedimiento'+id+'" style="width: 100px;" onkeypress="return soloNumeros(event);" onchange="return redondea_valor_base(this,'+id+',2);" readonly="readonly"></td>';
        txt_td = txt_td + '<td><button onclick="deducible('+id+')" type="button" class="btn btn-success btn-xs"><span id="b'+id+'" class="glyphicon glyphicon-plus"></span></button><button type="button" onclick="eliminar_form_procedimiento('+id+')" class="btn btn-danger btn-margin btn-lg"><span class="glyphicon glyphicon-trash"></span></button></td>'

        midiv_pago.innerHTML = txt_td;

        document.getElementById('agregar_procedimiento').appendChild(midiv_pago);

        id = parseInt(id);

        //Buscador de Productos Por Nombre
        var ax = id; 
        $("#procedimiento"+ax).autocomplete({
            source: function(request, response) {

                var id_emp = $('#id_empresa').val();
                var id_seg = $('#id_seguro').val();
                var id_nivel = $('#id_nivel').val();
                //var id_cont = $('#contador_procedimiento').val();

                $.ajax({
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    url: "{{route('contable_producto_paquete')}}",
                    dataType: "json",
                    data: {term: request.term,'posicion':0,'id_empresa': id_emp,'id_seguro': id_seg,'id_nivel': id_nivel},
                    success: function(data) {
                        response(data);
                        //console.log(data);
                    }
                });
            },
            
            change: function(event, ui){
                //alert(ui.item.id_producto);
                obtener_precio_producto_tarifario_nombre(ui.item.id_empresa,ui.item.id_producto,ax,ui.item.id_seguro,ui.item.id_nivel);
                verifica_producto_paquete(ui.item.id_producto,ax);
                //id_temporal = parseInt(id_temporal);
                //id_temporal = id_temporal+1;
                //document.getElementById('contador_temporal').value = id_temporal;
            },
            minLength: 2,

        });

        var ix = id;    
        $("#cod_prod"+ix).autocomplete({
           
            source: function(request, response){
              
               var id_emp = $('#id_empresa').val();
               var id_seg = $('#id_seguro').val();
               var id_nivel = $('#id_nivel').val();
               //var id_cont = $('#contador_procedimiento').val();
                $.ajax({
                   type: 'post',
                   headers: {
                       'X-CSRF-TOKEN': $('input[name=_token]').val()
                   },
                   url: "{{route('contable_codigo_paquete')}}",
                   dataType: "json",
                   data: {term: request.term,'posicion':0,'id_empresa': id_emp,'id_seguro': id_seg,'id_nivel': id_nivel},
                   success: function(data) {
                       console.log(data);
                       response(data);
                       
                      
                   }
               });
           
            },

            change: function(event, ui){
                //alert(ui.item.posicion);
               
                obtener_precio_producto_tarifario_codigo(ui.item.id_empresa,ui.item.id_producto,ix,ui.item.id_seguro,ui.item.id_nivel);
                verifica_producto_paquete(ui.item.id_producto,ix);
                
            
            },

           minLength: 2,

        });

        id = id+1;
        document.getElementById('contador_procedimiento').value = id;
       


    });

    /*function  autocompletar(){
    }*/

    
    function obtener_precio_producto_tarifario_nombre(id_emp,id_prod,pos,id_seg,id_niv){

        $.ajax({
           type:'post',
           headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
           url:"{{route('contable_precio_prod.tarifario_nombre')}}",
           datatype: 'json',
           data: {'id_emp': id_emp,'id_prod': id_prod,'id_seg': id_seg,'id_niv': id_niv,'posicion': pos},
           success: function(data){
            //alert(data.id_producto);
            if(data.no_data!='no'){
              //alert(data.precio_prod);
              $('#valor_pro'+data.posic).val(data.precio_prod);
              revision_total_pro(data.posic);
              $('#id_prod'+data.posic).val(data.id_producto);
              $('#cod_prod'+data.posic).val(data.cod_prod);
            }
           },
           error: function(data){
            console.log(data);
           }
        
        });
    
    }


    function verifica_producto_paquete(id_prod,pos){

        $.ajax({
           type:'post',
           headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
           url:"{{route('contable_existe_prod.paquete')}}",
           datatype: 'json',
           data: {'id_prod': id_prod,'posic': pos},
           success: function(data){
              
            if(data.no_data!='no'){
              
              $('#ident_paquete'+data.posic).val(data.existe_paq);
             
            }
           
           },
           error: function(data){
            console.log(data);
           }
        
        });

    }


    function obtener_precio_producto_tarifario_codigo(id_emp,id_prod,pos,id_seg,id_niv){
        //alert(pos);
        $.ajax({
           type:'post',
           headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
           url:"{{route('contable_precio_prod.tarifario_codigo')}}",
           datatype: 'json',
           data: {'id_emp': id_emp,'id_prod': id_prod,'id_seg': id_seg,'id_niv': id_niv,'posicion': pos},
           success: function(data){
            //alert(data.posic);
            if(data.no_data!='no'){
              $('#valor_pro'+data.posic).val(data.precio_prod);
              revision_total_pro(data.posic);
              $('#id_prod'+data.posic).val(data.id_producto);
              $('#procedimiento'+data.posic).val(data.nomb_prod);
            }
           },
           error: function(data){
            console.log(data);
           }
        
        });


    }

</script>
<script type="text/javascript">

    function revision_total(id){
        var fi = document.getElementById("fi"+id);
        var valor = $('#valor_base'+id).val();
        var valorfee = $('#valor_base'+id).val();

        //Verifica si es seguro Humana
        var id_seg_hum = $('#id_seguro').val();

        if(fi.checked == true){
            tipo = $('#id_tip_pago'+id).val();

            if(tipo == '4'){//Tarjeta Credito
                ntotal = valor *1.07;
                valorfee = valorfee * 0.07;
            }else if(tipo == '6'){//Tarjeta Debito
                ntotal = valor *1.02;
                valorfee = valorfee * 0.02;
            }else{
                ntotal = valor*1;
            }
            //agregar producto fee

            suma_total();
        }else{
            //borrar fee
            //var restar = $("#valor_pro"+id).val();

            ntotal = valor*1;
            //ntotal = ntotal - restar;
            $('#total'+id).val(ntotal.toFixed(2));
            suma_total();
        }
        suma_total();
        //Verificamos que no sea Seguro Humana
        //if(id_seg_hum != 4){
          revision_total_pro(0);
        //}
        
    }

    function verificar_valores(){

        var fee = 0;
        var unfee = false;
        var restar = 0;
        var band = 1;
        $(".fee").each(function() {
            var res = parseFloat($('#valor_base'+$(this).data("id")).val()) - parseFloat($('#total'+$(this).data("id")).val());
            if(res==0){
                band = band*1;
            }else{
                band = band*0;
            }
            console.log("restar ",restar);
            if($(this).prop('checked')){
                unfee = true;
                //console.log("check");
                var valorfee = $('#valor_base'+$(this).data("id")).val();
                var tipo = $('#id_tip_pago'+$(this).data("id")).val();
                //console.log("1.- "+ valorfee);
                if(tipo == '4'){
                    valorfee = parseFloat(valorfee) * 0.07;
                    fee = fee + valorfee;
                }else if(tipo == '6'){
                    valorfee = parseFloat(valorfee) * 0.02;
                    fee = fee + valorfee;
                }else{
                    valorfee = 0;
                    fee = fee + valorfee;
                    $(this).prop('checked',false);
                }
                if($("input[data-tipo='fee']").val()>0){

                    //console.log("- existe");

                    var val_fee = (valorfee.toFixed(2));
                    //alert(val_fee);
                    
                    //var total = parseFloat($('#valor_base'+$(this).data("id")).val())+ parseFloat(valorfee);
                    var total = parseFloat($('#valor_base'+$(this).data("id")).val())+parseFloat(val_fee);
                  
                    //var total = $('#valor_base'+$(this).data("id")).val()+valorfee;
                    $("#total"+$(this).data("id")).val(parseFloat(total).toFixed(2));
                    //$("#total"+$(this).data("id")).val(total.round(2));
                    //$("#total"+$(this).data("id")).val(0);
                    var i= $("input[data-tipo='fee']").data('idrel');
                    $("#dato_procedimiento"+i).show();
                    var valoractual = $("#total_procedimiento"+i).val();
                    //console.log(valoractual);
                    valoractual = parseFloat(valoractual)+ parseFloat(valorfee);
                    $("#total_procedimiento"+i).val(parseFloat(fee).toFixed(2));
                    $("#valor_pro"+i).val(parseFloat(fee).toFixed(2));
                    //alert(parseFloat(fee).toFixed(2));
                   // revision_total_pro(i);
                    //var pago =
                
                }else{

                    //alert("AGREGA FEE");
                    //console.log($(this).data("id"));
                    var total = parseFloat($('#valor_base'+$(this).data("id")).val())+ parseFloat(valorfee);
                    //console.log(total);
                    $('#total'+$(this).data("id")).val(parseFloat(total).toFixed(2));
                    id= document.getElementById('contador_procedimiento').value;

                    var midiv_pago = document.createElement("tr")
                    midiv_pago.setAttribute("id","dato_procedimiento"+id);

                    var txt_td_3 = '<td><input required type="hidden" id="visibilidad_procedimiento'+id+'" data-idrel="'+id+'" data-tipo="fee" name="visibilidad_procedimiento'+id+'" value="1"><input type="text" name="procedimiento'+id+'" id="procedimiento'+id+'" value="FEE ADMINISTRATIVO" class="feead" style="width:100%"></td>';
                    txt_td_3 = txt_td_3 + '<td><input required type="text" value="1" name="cantidad'+id+'" id="cantidad'+id+'" style="width: 100px;" onchange="revision_total_pro('+id+') " onkeypress="return soloNumeros(event);" required readonly></td>';
                    txt_td_3 = txt_td_3 + '<td><input required type="text" id="valor_pro'+id+'" name="valor_pro'+id+'" style="width: 100px;"  value="'+parseFloat(fee).toFixed(2)+'" onchange="revision_total_pro('+id+') " onkeypress="return soloNumeros(event);"></td>';
                    txt_td_3 = txt_td_3 + '<td><select id="tipo_cob_seguro'+id+'" name="tipo_cob_seguro'+id+'" onchange="revision_total_pro('+id+') "><option value="%">%</option><option value="$">$</option></select></td>';
                    txt_td_3 = txt_td_3 + '<td><input required type="hidden" value="0.00" name="copago'+id+'" id="copago'+id+'" style="width: 100px;display:none;" required onchange="revision_total_pro('+id+') "><input required type="text" value="0.00" name="copagom'+id+'" id="copagom'+id+'" style="width: 100px;display:none;" required onchange="revision_total_pro('+id+') "><input required type="text" id="p_oda'+id+'" name="p_oda'+id+'" value="0.00" onchange="revision_total_pro('+id+') " onkeypress="return soloNumeros(event);"></td>';
                    txt_td_3 = txt_td_3 + '<td><select id="tipo_desc'+id+'" name="tipo_desc'+id+'" onchange="revision_total_pro('+id+') "><option value="%">%</option><option value="$">$</option></select></td>';
                    txt_td_3 = txt_td_3 + '<td><input required type="text" id="desc'+id+'" name="desc'+id+'" style="width: 100px;display:none;"  value="0.00" onchange="revision_total_pro('+id+') " onkeypress="return soloNumeros(event);"><input required type="text" id="p_dcto'+id+'" name="p_dcto'+id+'" value="0.00" onchange="revision_total_pro('+id+') " onkeypress="return soloNumeros(event);"></td>';
                    txt_td_3 = txt_td_3 + '<td><input required style="text-align:center;" type="checkbox" name="iva'+id+'" id="iva'+id+'" onchange="revision_total_pro('+id+')" value="0" ></td>';

                    txt_td_3 = txt_td_3 + '<td><input required type="text" id="total_procedimiento'+id+'" name="total_procedimiento'+id+'" style="width: 100px;" onkeypress="return soloNumeros(event);" value="'+parseFloat(valorfee).toFixed(2)+'" onchange="return redondea_valor_base(this,'+id+',2);"></td>';
                    txt_td_3 = txt_td_3 + '<td><button type="button" onclick="eliminar_form_procedimiento('+id+')" class="btn btn-danger btn-margin btn-lg"><span class="glyphicon glyphicon-trash"></span> </button></td>'

                    midiv_pago.innerHTML = txt_td_3;

                    document.getElementById('agregar_procedimiento').appendChild(midiv_pago);
                    id = parseInt(id);
                    id = id+1;
                    document.getElementById('contador_procedimiento').value = id;

                    //revision_total_pro(id-1);
                }
            }else{
                //alert("Ingreso");
                console.log("unckeck");
                console.log(fee);
                if(!unfee){
                    var i= $("input[data-tipo='fee']").data('idrel');
                    var valoractual = $("#total_procedimiento"+i).val();
                    //console.log(valoractual);
                    valoractual = parseFloat(valoractual)+ parseFloat(valorfee);
                    $("#total_procedimiento"+i).val(parseFloat(0).toFixed(2));
                    $("#valor_pro"+i).val(parseFloat(0).toFixed(2));

                    //$("#dato_procedimiento"+i).hide();
                    if(band){
                        console.log("hide"+i);
                        $("#dato_procedimiento"+i).hide();
                    }else{
                        console.log("adss");
                        $("#dato_procedimiento"+i).show();
                    }
                }
            }
        });
    }

    function checkearFee(e){
        var fi = document.getElementById("fi"+id);
        if(fi.checked == false){
            //alert("falso");
        }

    }

    function revision_total_pro(id){
        //alert(id);
        var iva       = $("#iva_item"+id);
        var valor     = $('#valor_pro'+id).val();
        //var nvalor  = valor *1;
        var tipo_desc = $("#tipo_desc"+id).val();
        var p_dcto    = $("#p_dcto"+id).val();
        var cantidad  = $('#cantidad'+id).val();
        var tipo_cob  = $("#tipo_cob_seguro"+id).val();
        var p_oda     = $("#p_oda"+id).val();//alert(p_oda);
        var valor_cob = $("#copagom"+id).val();
        var descuento = $('#desc'+id).val();

        valor = valor * cantidad;
        if(tipo_cob=='%'){
            $("#p_oda"+id).show();
            $("#copagom"+id).hide();
            $("#copagom"+id).val(0);
            if(p_oda > 100){
                alert("Valor a Cobrar del seguro no puede ser mayor al 100%");
                $("#p_oda"+id).val('100');
                p_oda = 100;
            }
        }else{
            $("#p_oda"+id).val(0);
            $("#p_oda"+id).hide();
            $("#copagom"+id).show();
        }
        if(tipo_desc=='%'){
            $("#p_dcto"+id).show();
            $("#desc"+id).hide();
            if(p_dcto > 100){
                alert("Valor a Cobrar del seguro no puede ser mayor al 100%");
                $("#p_dcto"+id).val('100');
                p_dcto = 100;
            }
        }else{
            $("#p_dcto"+id).val(0);
            $("#p_dcto"+id).hide();
            $("#desc"+id).show();
        }
        if(tipo_cob=='%'){
            if(p_oda > 0){
                p_oda = 100 - p_oda;
            }
            //p_oda = 100 - p_oda;
            valor_cob = p_oda * valor / 100;
            valor_cob = valor_cob.toFixed(2);
            $("#copago"+id).val(valor_cob);
        }else{

            valor_cob = valor - valor_cob;
            valor_cob = valor_cob.toFixed(2);
            $("#copago"+id).val(valor_cob);
        }
        valor -= valor_cob;
        if(valor_cob > 0){
            $("#p_dcto"+id).attr('disabled',true);
            $('#desc'+id).attr('disabled',true);
            $("#p_dcto"+id).val('0');
            $('#desc'+id).val('0');
        }else{
            $("#p_dcto"+id).attr('disabled',false);
            $('#desc'+id).attr('disabled',false);
        }
        if(tipo_desc=='%'){
            descuento = p_dcto * valor / 100;
            descuento = descuento.toFixed(2);
            $("#desc"+id).val(descuento);
        }
        valor -= descuento;
        if(iva.prop('checked')){
            ntotal = ((valor) *(1 + {{$iva->iva}}) );
            if(isNaN(ntotal)){
                ntotal=0;
            }
            $('#total_procedimiento'+id).val(ntotal.toFixed(2));
        }else{
            ntotal = ((valor)*1);
            if(isNaN(ntotal)){
                ntotal=0;
            }
            $('#total_procedimiento'+id).val(ntotal.toFixed(2));
        }
        suma_total_pro(id);
        //verificar_valores();
    }

    function suma_total_pro(id){
        var id_seguro = $('#id_seguro').val();
        var contador = $('#contador_procedimiento').val();
        var sumador = 0;
        var copago = 0;
        //Nuevo
        var sumador_ded = 0;
        var total_copago = 0;

        var val_oda = 0;
        var valor_porcent_oda = 0;
        var valor_porcent = 0;

        var val_pro = 0;

       
        //Seguro Humana
        if(id_seguro == 4){

          
            for (var i = 0; i < id ; i++) {
                
                    if($('#deducible_id'+id).val()>0){

                      valor_ant = $('#valor_pro'+i).val();
                      nuevo_val =  ((valor_ant)-($('#valor_pro'+id).val())).toFixed(2);
                      
                      //Redondear a 2 decimales
                      $('#valor_pro'+i).val(nuevo_val);
                      valor_porcent_oda= $("#p_oda"+i).val(); 
                      valor_tot_proced = (nuevo_val*valor_porcent_oda)/100;

                      //Valor
                      val_pro = $('#valor_pro'+id).val();
                      
                      $('#total_procedimiento'+i).val((valor_tot_proced).toFixed(2));
                      $('#total_procedimiento'+id).val($('#valor_pro'+id).val());
                      //$('#total_procedimiento'+id).val((val_pro).toFixed(2));
                      
                      valor_oda = nuevo_val - valor_tot_proced;
                      $('#val_deducible'+i).val(valor_oda);
                      
                    }
            }

            
            for (var i = 0; i < contador; i++) {

                if($('#visibilidad_procedimiento'+i).val() == 1){
 
                    sumador = sumador + parseFloat($('#total_procedimiento'+i).val());
                    copago = copago+parseFloat($('#copago'+i).val());
                  
                    if($('#val_deducible'+i).val()>0){
                        sumador_ded = sumador_ded+parseFloat($('#val_deducible'+i).val());
                    }
                }
            
            }
     

            if(sumador_ded>0){
                $('#total_pro').val(sumador.toFixed(2));
                $('#valor_copago').val(sumador_ded.toFixed(2));
            }else{
                $('#total_pro').val(sumador.toFixed(2));
                $('#valor_copago').val(copago.toFixed(2));
            }

         
           
        }else{


            for (var i = 0; i < id ; i++) {


                if($('#deducible_id'+id).val()>0){

                    valor_ant = $('#valor_pro'+i).val();
                    nuevo_val = (valor_ant).toFixed(2);
                    
                    $('#valor_pro'+i).val(nuevo_val);//900
                    
                    valor_porcent_oda= $("#p_oda"+i).val(); 
                    valor_tot_proced = (nuevo_val*valor_porcent_oda)/100;
                    sum_total = parseFloat(valor_tot_proced) + parseFloat($('#valor_pro'+id).val());

                    $('#total_procedimiento'+i).val((valor_tot_proced).toFixed(2));
                    $('#total_procedimiento'+id).val($('#valor_pro'+id).val());
                      
                    valor_oda =  valor_ant - sum_total;
                 
                    $('#val_deducible'+i).val(valor_oda);
                }

            }


            for (var i = 0; i < contador; i++) {
                if($('#visibilidad_procedimiento'+i).val() == 1){
                    sumador = sumador + parseFloat($('#total_procedimiento'+i).val());
                    copago = copago+parseFloat($('#copago'+i).val());
                   
                    if($('#val_deducible'+i).val()>0){
                    
                      sumador_ded = sumador_ded+parseFloat($('#val_deducible'+i).val());
                    
                    }
                }
               
            }

            if(sumador_ded>0){
              total_copago = sumador_ded;
              $('#total_pro').val(sumador.toFixed(2));
              $('#valor_copago').val(total_copago.toFixed(2));
            }else{
              total_copago = copago;
              $('#total_pro').val(sumador.toFixed(2));
              $('#valor_copago').val(total_copago.toFixed(2));
            }

        }

    }

    function suma_total(){
        var contador = $('#contador_pago').val();
        var sumador = 0;
        var sumador_sin = 0;

        for (var i = 0; i < contador; i++) {
            if($('#visibilidad_pago'+i).val() == 1){

                sumador_sin = sumador_sin + parseFloat($('#valor_base'+i).val());
                sumador = sumador + parseFloat($('#total'+i).val());
                //sumador = sumador + parseFloat($('#total_procedimiento'+i).val());
            }
            //console.log('i',i);
        }
        verificar_valores();
        //console.log("sumador",sumador);
        //console.log("sumador_sin",sumador_sin);

        $('#total_sin_tarjeta').val(sumador_sin.toFixed(2));
        $('#total_final1').val(sumador.toFixed(2));
    }

    function revisar_componentes(e, id){
        metodo = $('#id_tip_pago'+id).val();
        if(metodo == 1){
            $("#tipo_tarjeta"+id).prop('disabled', true);
            $("#numero"+id).prop('disabled', true);
            $("#tipo_tarjeta"+id).prop('disabled', true);
            $("#id_banco"+id).prop('disabled', true);
            $("#fi"+id).prop('disabled', true);
            revision_total(id);
        }else if(metodo == 2){
            $("#tipo_tarjeta"+id).prop('disabled', true);
            $("#numero"+id).prop('disabled', false);
            $("#tipo_tarjeta"+id).prop('disabled', true);
            $("#id_banco"+id).prop('disabled', false);
            $("#fi"+id).prop('disabled', true);
            revision_total(id);
        }else if(metodo == 3){
            $("#tipo_tarjeta"+id).prop('disabled', true);
            $("#numero"+id).prop('disabled', false);
            $("#tipo_tarjeta"+id).prop('disabled', true);
            $("#id_banco"+id).prop('disabled', false);
            $("#fi"+id).prop('disabled', true);
            revision_total(id);
        }else if(metodo == 4){
            $("#tipo_tarjeta"+id).prop('disabled', false);
            $("#numero"+id).prop('disabled', false);
            $("#tipo_tarjeta"+id).prop('disabled', false);
            $("#id_banco"+id).prop('disabled', false);
            $("#fi"+id).prop('disabled', false);
            revision_total(id);
        }else if(metodo == 5){
            $("#tipo_tarjeta"+id).prop('disabled', true);
            $("#numero"+id).prop('disabled', false);
            //$("#tipo_tarjeta"+id).prop('disabled', false);
            $("#id_banco"+id).prop('disabled', false);
            $("#fi"+id).prop('disabled', false);
            revision_total(id);
        }else if(metodo == 6){
            $("#tipo_tarjeta"+id).prop('disabled', false);
            $("#numero"+id).prop('disabled', false);
            $("#tipo_tarjeta"+id).prop('disabled', false);
            $("#id_banco"+id).prop('disabled', false);
            $("#fi"+id).prop('disabled', false);
            revision_total(id);
        }
    }
    function validar_copago() {
      // Get the checkbox
      var checkBox = document.getElementById("copago");
      // Get the output text
      var text = document.getElementById("dato_copago");

      // If the checkbox is checked, display the output text
      if (checkBox.checked == true){
        document.getElementById("div_copago2").style.display="block";
        $('#valor_copago').val('0');
        text.style.display = "block";
      } else {
        $('#valor_copago').val('0');
        document.getElementById("div_copago2").style.display="none";
        text.style.display = "none";
      }
    }

    function redondea_valor_base(e,id,val){
        return parseFloat(e.value).toFixed(val);
    }

    //Elimina Registro de la Tabla Forma de Pago
    function eliminar_form_pag(valor)
    {
        var dato_pago1 = "dato_pago"+valor;
        var nombre_pago2 = 'visibilidad_pago'+valor;
        document.getElementById(dato_pago1).style.display='none';
        document.getElementById(nombre_pago2).value = 0;
        suma_total();

    }

    function eliminar_form_procedimiento(valor)
    {
        //alert("eliminar");

        var dato_pago1 = "dato_procedimiento"+valor;

        var nombre_pago2 = 'visibilidad_procedimiento'+valor;

        document.getElementById(dato_pago1).style.display='none';
        document.getElementById(nombre_pago2).value = 0;
        document.getElementById(nombre_pago2).removeAttribute('data-tipo');
        console.log(document.getElementById(nombre_pago2));
        //alert($("#procedimiento"+valor).val());
        if($("#procedimiento"+valor).val()=='FEE ADMINISTRATIVO'){
            var contador = $('#contador_pago').val();
            for (var i = 0; i < contador; i++) {
                if($('#visibilidad_pago'+i).val() == 1){
                    $('#fi'+i).prop( "checked", false );
                }
                //console.log('i',i);
            }

        }
        suma_total();
        suma_total_pro(valor);
    }
    function obtener_fecha(){

        //obtenemos la fecha actual
        var now = new Date();
        var day =("0"+now.getDate()).slice(-2);
        var month=("0"+(now.getMonth()+1)).slice(-2);
        var today=now.getFullYear()+"-"+(month)+"-"+(day);
        $("#fecha").val(today);

    }
</script>
<script type="text/javascript">

    function crear_factura(){
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
        var total_procedimiento = formulario.total_pro.value;
        var pago = formulario.pago.value;
        console.log(total_procedimiento, pago);
        var caja_pago = formulario.caja.value;

        var msj = "";
        var msj2 = "";

        //Datos Generales

        if(id_emp == ""){

           msj = msj + "Por favor, Seleccione la Empresa<br/>";
        }
       //alert(copago.checked);
        if(copago.checked==true){

            if(numero_oda =='0' || numero_oda ==''){
                msj = msj + "Por favor, Ingrese el número de Oda<br/>";
            }
        }

        /*if(sucurs == ""){

           msj = msj + "Por favor, Seleccione la Sucursal<br/>";
        }

        if(punt_emision == ""){
           msj = msj + "Por favor, Seleccione el Punto de Emision<br/>";
        }*/

        //Paciente
        
        /*if(total_procedimiento != pago){
           msj += "Por favor, Falta pagos por ingresar<br/>";
        }*/
        
        
        if(cedula == ""){
           msj += "Por favor,Ingrese la cedula del paciente<br/>";
        }
        if(nombre_paciente == ""){
           msj += "Por favor,Ingrese el nombre del paciente<br/>";
        }
        if(seguro_paciente == ""){
           msj += "Por favor,Seleccione el seguro paciente<br/>";
        }

        //Cliente
        if(tipo_identicacion == ""){
           msj += "Por favor,Selecione el Tipo de Identificaciòn<br/>";
        }
        if(ced_cliente == ""){
           msj += "Por favor,Ingrese la cedula del cliente<br/>";
        }
        if(raz_social == ""){
           msj += "Por favor,Ingrese la razon social<br/>";
        }
        if(ciud_cliente == ""){
           msj += "Por favor,Ingrese la ciudad del cliente<br/>";
        }

        if(dire_cliente == ""){
           msj += "Por favor,Ingrese la direccion cliente<br/>";
        }
        if(telf_cliente == ""){
           msj += "Por favor,Ingrese el telefono del cliente<br/>";
        }
        if(email_cliente == ""){
           msj += "Por favor,Ingrese el email del cliente<br/>";
        }


        //Pago
        if(pago == ""){
           msj += "Por favor,Ingrese el pago\n";
        }
        if(caja_pago == ""){
           msj += "Por favor,Ingrese la caja a la cual se va a pagar<br/>";
        }

        var i;var max=document.getElementById('agregar_pago').rows.length;
        for (i = 0; i < max; i++) {
          var tipo_pago = document.getElementById('id_tip_pago'+i).value;
          var numero = document.getElementById('numero'+i).value;
          var id_banco = document.getElementById('id_banco'+i).value;
          var tipo_tarjeta = document.getElementById('tipo_tarjeta'+i).value;
          if(tipo_pago=='2'){

            if(numero==""){
                msj += "Por favor,Ingrese el número del cheque<br/>";
            }
            if(id_banco==""){
                msj += "Por favor,Seleccione el Banco<br/>";
            }
          }
           if(tipo_pago=='4'){
            if(tipo_tarjeta==""){
                msj += "Por favor,Ingrese el Tipo de tarjeta<br/>";
            }

            if(id_banco==""){
                msj += "Por favor,Seleccione el Banco<br/>";
            }
          }
        }

        if(msj != ""){
            $('#crear_recibo').button('reset');
            swal({
                  title: "Error!",
                  type: "error",
                  html: msj
                });
            return false;
        }

        if(msj2 != ""){
            $('#crear_recibo').button('reset');
            swal({
                  title: "Error!",
                  type: "error",
                  html: msj2
                });
            return false;
        }

        var fecha = document.getElementById('fecha').value;

        var unix =  Math.round(new Date(fecha).getTime()/1000);
        //console.log($("#crear_form").serialize());

        $.ajax({
            type: 'post',
            url:"{{route('facturacion.guardar_orden')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#crear_form").serialize(),
            success: function(data){

                if(data.val_dif == 1){

                  window.location.href = "{{asset('/contable/facturacion/agenda_a/editar')}}/"+data.id_orden+'/'+data.valor;
                    
                }else{

                  if(data.val_dif == 0){
                   
                    $("#ride").attr("href", );
                    window.open("{{asset('/comprobante/orden/venta')}}/"+data.id_orden, '_blank ');
                    @if($agenda->id_doctor!= null)
                            window.location.href = "{{asset('/agenda/calendario/')}}/{{$agenda->id_doctor1}}/"+unix;
                    @else
                            window.location.href = "{{route('preagenda.edit',['id' => $agenda->id])}}";
                    @endif
                    $('#crear_recibo').button('reset');
                  }

                }
            
            },
            error: function(data){
                console.log(data.responseText);

            }
        });
    }

    function seguro(){
        var seguro =  $('#id_seguro').val();

        $.ajax({
            type: 'get',
            url:"{{asset('contable/facturacion/verificar/seguro/')}}"+'/'+seguro,
            datatype: 'html',
            success: function(data){
                if(data==1 ){
                    document.getElementById("div_copago").style.display="block";

                }else{
                    document.getElementById("div_copago").style.display="none";
                    document.getElementById("div_copago2").style.display="none";
                    document.getElementById("dato_copago").style.display="none";
                    $('#valor_copago').val('');
                    $('#valor_copago2').val('');
                    $('#numero_oda').val('0');
                    $("#copago").prop("checked", false);
                }
                pagar();

            },
            error:  function(){
                alert('error al cargar');
            }
        });
    }

    function pagar(){
        var seguro =  $('#id_seguro').val();
        var doctor =  $('#id_doctor').val();
        $.ajax({
            type: 'get',
            url:"{{asset('contable/facturacion/verificar/pago/')}}"+'/'+seguro+'/'+doctor,
            datatype: 'html',
            data: $("#obs_id").serialize(),
            success: function(data){
                $('#pago').val(data.trim());
            },
            error:  function(){
                alert('error al cargar');
            }
        });
    }

    $("#cedula").autocomplete({
        source: function( request, response ) {
          $.ajax( {
            type: 'post',
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            url: "{{route('facturacion.buscar_cliente')}}",
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
    });
    function buscar(){
        var cedula_1 = $('#cedula').val();
        var pasaporte = parseInt($('#tipo_identificacion').val());
        var alerta = 0;
        if(cedula_1.length < 10){
          alert('La Cantidad de Digitos no es Correcta');
        }
        var cedula = validarCedula($('#cedula').val());
        if(cedula == false && pasaporte !=  6 && pasaporte !=  8){
            $('#cedula').val('');
            $('#razon_social').val('');
            $('#ciudad').val('');
            $('#direccion').val('');
            $('#telefono').val('');
            $('#email').val('');
            $('#caja').val('');
        }else{
            $.ajax({
                type: 'post',
                url:"{{route('facturacion.cliente')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                type: 'POST',
                datatype: 'json',
                data: $("#cedula"),
                success: function(data){
                    if(data.value != 'no'){
                        $('#razon_social').val(data.nombre);
                        $('#ciudad').val(data.ciudad);
                        $('#direccion').val(data.direccion);
                        $('#telefono').val(data.telefono);
                        $('#email').val(data.email);
                        $('#caja').val(data.caja);
                    }
                    console.log(data);
                },
                error: function(data){
                    console.log(data);
                }
            });
        }

    }
</script>
@endsection
