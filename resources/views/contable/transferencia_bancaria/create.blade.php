@extends('contable.transferencia_bancaria.base')
@section('action-content')
<script type="text/javascript">
    function check(e) {
        tecla = (document.all) ? e.keyCode : e.which;

        //Tecla de retroceso para borrar, siempre la permite
        if (tecla == 8) {
            return true;
        }

        // Patron de entrada, en este caso solo acepta numeros y letras
        patron = /[A-Za-z0-9]/;
        tecla_final = String.fromCharCode(tecla);
        å
        return patron.test(tecla_final);
    }

    function goBack() {
        window.history.back();
        //window.location.reload(history.back());
    }

    function goNew() {
        $(".btn_add").attr("disabled", false);

        $("#asiento").val("");
        $("#id").val("");
        $("#numero").val("");
        $(".print").css('visibility', 'hidden');
    }
</script>
<style>
    .text_der {
        text-align: right;
    }
</style>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.banco')}}</a></li>
            <li class="breadcrumb-item"><a href="../notacredito">Transferencia Bancaria</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.crear')}}</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-8">
                <h3 class="box-title">Crear Transferencia Bancaria</h3>
            </div>
            <div class="col-md-1 print" id="imprimir">
                <a target="_blank" href="{{ route('notadebito.imprimir', ['id' => 3]) }}" class="btn btn-info btn-gray">
                    <i class="glyphicon glyphicon-print" aria-hidden="true"></i>
                    <!--&nbsp;&nbsp; Revisar Nota-->
                </a>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="location.href='{{ route('transferenciabancaria.create') }}'" class="btn btn-primary btn-gray">
                    {{trans('contableM.nuevo')}}
                </button>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="goBack()" class="btn btn-success btn-gray">
                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                </button>
            </div>
        </div>

        <form id="form" class="form-vertical" role="form">
            <div class="box-body dobra">
                {{ csrf_field() }}
                <!--header row-->
                <div class="col-md-1">
                </div>
                <div class="header row col-md-11">
                    <div class="form-group col-xs-1 px-1">
                        <div class="col-md-12 px-0">
                            <label for="estado" class="label_header">{{trans('contableM.estado')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="estado" type="text" class="form-control" name="estado" value="Activa" readonly autofocus>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id" class=" label_header">{{trans('contableM.id')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" name="id" id="id" value="" readonly autofocus>
                            @if ($errors->has('id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('id') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="numero" class=" label_header">{{trans('contableM.numero')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" name="numero" id="numero" value="" readonly autofocus>
                            @if ($errors->has('numero'))
                            <span class="help-block">
                                <strong>{{ $errors->first('numero') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="asiento" class="label_header">{{trans('contableM.asiento')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" name="asiento" id="asiento" value="" readonly autofocus>
                            @if ($errors->has('asiento'))
                            <span class="help-block">
                                <strong>{{ $errors->first('asiento') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="tipo" class="label_header">{{trans('contableM.tipo')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" name="tipo" id="tipo" value="BAN-TR" readonly autofocus>
                            @if ($errors->has('tipo'))
                            <span class="help-block">
                                <strong>{{ $errors->first('tipo') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="fecha_asiento" class="label_header">{{trans('contableM.fecha')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="fecha_asiento" type="date" class="form-control" name="fecha_asiento" value="{{ date('Y-m-d') }}" required>
                            @if ($errors->has('fecha_asiento'))
                            <span class="help-block">
                                <strong>{{ $errors->first('fecha_asiento') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-1 px-1">
                        <div class="col-md-12 px-0">
                            <label for="asiento" class="label_header">{{trans('contableM.proyecto')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" value="0000" name="proyecto" id="proyecto" value="" readonly autofocus>
                            @if ($errors->has('proyecto'))
                            <span class="help-block">
                                <strong>{{ $errors->first('proyecto') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!-- 2 row -->
                    <div class="form-group col-xs-12 px-1">
                        <div class="col-md-12 px-0">
                            <label for="concepto" class="label_header">{{trans('contableM.concepto')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="concepto" type="text" class="form-control" name="concepto" value="{{ old('concepto') }}" required autofocus>
                            @if ($errors->has('concepto'))
                            <span class="help-block">
                                <strong>{{ $errors->first('concepto') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!-- 3 row -->
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="numcheque" class="label_header">N&uacute;m. Cheque</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="numcheque" type="text" class="form-control" name="numcheque" value="">
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="fecha_cheque" class="label_header">Fecha Ch.</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="fecha_cheque" type="date" class="form-control" name="fecha_cheque" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="form-group col-xs-6 px-1">
                        <div class="col-md-12 px-0">
                            <label for="beneficiario" class="label_header">{{trans('contableM.beneficiario')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="observacion" type="text" class="form-control" name="beneficiario" value="{{ old('beneficiario') }}" maxlength="50" autofocus>
                            @if ($errors->has('beneficiario'))
                            <span class="help-block">
                                <strong>{{ $errors->first('beneficiario') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="no_increm_cheque" class="label_header">No incrementar cheque</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="checkbox" id="no_increm_cheque" class="form-check-input" name="no_increm_cheque" value="1" @if(@$no_increm_cheque=="1" ) checked @endif>
                        </div>
                    </div>
                    <!-- 4 row -->
                    <div class="form-group col-xs-6 px-1">
                        <div class="col-md-12 px-0">
                            <label for="direccion" class="label_header">Direcci&oacute;n</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="direccion" type="text" class="form-control" name="direccion" value="{{ old('direccion') }}" maxlength="150" autofocus>
                        </div>
                    </div>
                    <div class="form-group col-xs-6 px-1">
                        <div class="col-md-12 px-0">
                            <label for="ruc" class="label_header">R.U.C / C.I</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="ruc" type="text" class="form-control" name="ruc" value="{{ old('ruc') }}" maxlength="13" autofocus>
                            @if ($errors->has('ruc'))
                            <span class="help-block">
                                <strong>{{ $errors->first('ruc') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!-- 5 row -->
                    <div class="form-group col-xs-6 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_cuenta_origen" class="label_header">{{trans('contableM.CuentaOrigen')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select class="form-control" name="id_cuenta_origen" id="id_cuenta_origen" required>
                                <option value="">Seleccione...</option>
                                @foreach($bancos as $value)
                                <option value="{{$value->cuenta_mayor}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_divisa" class="label_header">{{trans('contableM.divisas')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select class="form-control" name="id_divisa_origen" id="id_divisa_origen" required>
                                @foreach($divisas as $value)
                                <option value="{{$value->id}}">{{$value->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_cambio_origen" class="label_header">{{trans('contableM.cambio')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="id_cambio_origen" type="text" class="form-control text_der number" value="1.00" name="id_cambio_origen" required autofocus>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="valor_origen" class="label_header">{{trans('contableM.valor')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="valor_origen" type="text" class="form-control text_der" onchange="calcular()" value="0.00" name="valor_origen" autofocus>
                        </div>
                    </div>
                    <!-- 5 row -->
                    <div class="form-group col-xs-6 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_cuenta_destino" class="label_header">{{trans('contableM.CuentaDestino')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select class="form-control" style="width: 100%;" name="id_cuenta_destino" id="id_cuenta_destino" required>
                            <option value="">Seleccione...</option>
                                @foreach($bancos as $value)
                                <option value="{{$value->cuenta_mayor}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_divisa_destino" class="label_header">{{trans('contableM.divisas')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select class="form-control" name="id_divisa_destino" id="id_divisa_destino" required>
                                @foreach($divisas as $value)
                                <option value="{{$value->id}}">{{$value->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_cambio_destino" class="label_header">{{trans('contableM.cambio')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="id_cambio_destino" type="text" class="form-control text_der number" value="1.00" name="id_cambio_destino" required autofocus>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="valor_destino" class="label_header">{{trans('contableM.valor')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="valor_destino" type="text" class="form-control text_der number" value="0.00" name="valor_destino" required readonly autofocus>
                        </div>
                    </div>
                </div>

                {{-- <div class="col-md-12 table-responsive">
                    <input type="hidden" name="contador" id="contador" value="0">
                    <input type="hidden" name="total" id="total" value="0">
                    <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                        <thead>
                        <tr class='well-dark'>
                                <th width="5%"  tabindex="0"></th>
                                <th width="55%" class="" tabindex="0">{{trans('contableM.nombre')}}</th>
                                <th width="20%" class="" tabindex="0">{{trans('contableM.Debe')}}</th>
                                <th width="20%" class="" tabindex="0">{{trans('contableM.Haber')}}</th>
                                <th width="5%" class="" tabindex="0">
                                    <button onclick="nuevo()" type="button" class="btn btn-success btn-gray" >
                                        <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="agregar_cuentas">
                        <tr class="well">
                                <td>
                                    <input type="hidden" name="no_no[]" class="no_no" />
                                </td>
                                <td>

                                    <select id="nombre[]" name="nombre[]" class="form-control select2_cuentas" style="width:100%" required  >
                                        <option> </option>
                                        @foreach($cuentas as $value)
                                            <option value="{{$value->id}}" data-name="{{$value->nombre}}">{{$value->id}} - {{$value->nombre}}</option>
                @endforeach
                </select>
                </td>
                <td>
                    <input class="form-control input-sm debe" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="debe[]" required>
                </td>
                <td>
                    <input class="form-control input-sm haber" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);addvalue();" name="haber[]" required>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-gray delete">
                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                    </button>
                </td>
                </tr>
                <tr style="display:none" id="mifila">
                    <td>
                        <input type="hidden" name="no_no[]" class="no_no" value="qw" />
                    </td>

                    <td>
                        <select name="nombre[]" class="form-control select2_cuentas class_nombre" style="width:100%">
                            <option> </option>
                            @foreach($cuentas as $value)
                            <option value="{{$value->id}}" data-name="{{$value->nombre}}"> {{$value->id}} - {{$value->nombre}}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input class="form-control input-sm debe" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="debe[]">
                    </td>
                    <td>
                        <input class="form-control input-sm haber" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);addvalue();" name="haber[]">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-gray delete">
                            <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                        </button>
                    </td>
                </tr>
                </tbody>
                <tfoot class='well'>
                    <td></td>
                    <td class="text-right">{{trans('contableM.totales')}}</td>
                    <td id="debe_contable">
                        0.00
                    </td>
                    <td id="haber_contable"></td>
                    <td></td>
                </tfoot>
                </table>

            </div> --}}

    </div>

    <div class="box-body dobra">
        <!--header row-->
        <div class="col-md-1">
        </div>
        <div class="header row  col-md-11">

            <!-- 1 row -->
            <div class="form-group col-xs-6 px-1">
                <div class="col-md-12 px-0">
                    <label for="glosa" class="label_header">Glosa</label>
                </div>
                <div class="col-md-12 px-0">
                    <textarea id="glosa" rows="10" class="form-control" name="glosa" value="{{ old('glosa') }}" maxlength="250" required autofocus></textarea>
                    @if ($errors->has('glosa'))
                    <span class="help-block">
                        <strong>{{ $errors->first('glosa') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
            <!-- 2 row -->
            <div class="form-group col-xs-2 px-1">
                <div class="col-md-12 px-0">
                    <label for="comision" class="label_header">Comisi&oacute;n</label>
                </div>
                <div class="col-md-12 px-0">
                    <select class="form-control" name="id_comision" id="id_comision">
                        <option value="">Seleccione...</option>
                        <option value="0">(Ninguno)</option>
                        <option value="5">5 %</option>
                        <option value="7">7 %</option>
                        <option value="8">8 %</option>
                    </select>
                </div>
            </div>
            <div class="form-group col-xs-2 px-1">
                <div class="col-md-12 px-0">
                    <label for="valor_comision" class="label_header">&nbsp;</label>
                </div>
                <div class="col-md-12 px-0">
                    <input id="valor_comision" type="text" class="form-control text_der " value="0.00" name="valor_comision" autofocus readonly>
                </div>
            </div>
            <div class="form-group col-xs-2 px-1">
                <div class="col-md-12 px-0">
                    <label for="comision_tarjeta" class="label_header">Comisi&oacute;n de Tarjeta C</label>
                </div>
                <div class="col-md-12 px-0">
                    <input id="comision_tarjeta" type="text" class="form-control text_der " onchange="calcular()" value="0.00" name="comision_tarjeta" autofocus>
                </div>
            </div>
            <!-- 3 row -->
            <div class="form-group col-xs-2 px-1">
                <div class="col-md-12 px-0">
                    <label for="baseiva" class="label_header">Base IVA</label>
                </div>
                <div class="col-md-12 px-0">
                    <input id="baseiva" type="text" class="form-control text_der " value="0.00" name="baseiva" onchange="calcular()" autofocus>
                </div>
            </div>
            <div class="form-group col-xs-2 px-1">
                <div class="col-md-12 px-0">
                    <label for="id_base_iva" class="label_header">&nbsp;</label>
                </div>
                <div class="col-md-12 px-0">
                    <select class="form-control" name="id_base_iva" id="id_base_iva" onchange="calcular()">
                        <option value="">Seleccione...</option>
                        <option value="0" data-value="0">(Ninguno)</option>
                        @foreach($retencionesR as $value)
                        <option value="{{$value->cuenta_deudora}}" data-value="{{$value->valor}}">{{$value->valor}} %</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group col-xs-2 px-1">
                <div class="col-md-12 px-0">
                    <label for="iva_retenido" class="label_header">IVA Retenido</label>
                </div>
                <div class="col-md-12 px-0">
                    <input id="iva_retenido" type="text" class="form-control text_der " value="0.00" name="iva_retenido" autofocus>
                </div>
            </div>
            <!-- 4 row -->
            <div class="form-group col-xs-2 px-1">
                <div class="col-md-12 px-0">
                    <label for="base_ret_fuente" class="label_header">Base Ret. Fuente</label>
                </div>
                <div class="col-md-12 px-0">
                    <input id="base_ret_fuente" type="text" class="form-control text_der " onchange="calcular();cambiar_estado();" value="0.00" name="base_ret_fuente" autofocus>
                </div>
            </div>
            <div class="form-group col-xs-2 px-1">
                <div class="col-md-12 px-0">
                    <label for="id_base_ret_fuente" class="label_header">&nbsp;</label>
                </div>
                <div class="col-md-12 px-0">
                    <select class="form-control" name="id_base_ret_fuente" id="id_base_ret_fuente" onchange="calcular(); ">
                        <option value="">Seleccione...</option>
                        <option value="0" data-value="0">(Ninguno)</option>
                        @foreach($retenciones as $value)
                        <option value="{{$value->cuenta_deudora}}" data-value="{{$value->valor}}">{{$value->valor}} %</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group col-xs-2 px-1">
                <div class="col-md-12 px-0">
                    <label for="retencion_fuente" class="label_header">Retenci&oacute;n en la Fuente</label>
                </div>
                <div class="col-md-12 px-0">
                    <input id="retencion_fuente" type="text" class="form-control text_der " value="0.00" name="retencion_fuente" readonly autofocus>
                </div>
            </div>
            <div class="form-group col-xs-2 px-1">
                <div class="col-md-12 px-0">
                    <label for="" class="label_header">&nbsp;</label>
                </div>
                <div class="col-md-12 px-0">
                    <label for="val_adicionales">Valores Adicionales</label>
                </div>
            </div>
            <div class="form-group col-xs-2 px-1">
                <div class="col-md-12 px-0">
                    <label for="valores_adicionales" class="label_header">&nbsp;</label>
                </div>
                <div class="col-md-12 px-0">
                    <input id="valores_adicionales" type="text" class="form-control text_der " onchange="calcular()" value="0.00" name="valores_adicionales" autofocus>
                </div>
            </div>
            <div class="form-group col-xs-2 px-1">
                <div class="col-md-12 px-0">
                    <label for="total" class="label_header">{{trans('contableM.total')}}</label>
                </div>
                <div class="col-md-12 px-0">
                    <input id="total" type="text" class="form-control text_der " value="0.00" name="total" readonly autofocus>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group col-xs-10 text-center">
        <div class="col-md-6 col-md-offset-4">
            <button type="button" class="btn btn-default btn-gray btn_add">
                <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.guardar')}}
            </button>
        </div>
    </div>

    </form>
    </div>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="{{ asset ("/js/jquery-ui.js")}}"></script>
    <script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
    <script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

    <script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

    <script type="text/javascript">


        $('.select2_cuenta_destino').select2({
        placeholder: 'Seleccione Cuenta Destino',
        allowClear: true, 
        ajax: {
            url: '{{route("transferenciabancaria.buscar_cuenta_destino")}}',
            data: function(params) {
            var query = {
                search: params.term,
                type: 'public'
            }
            return query;
            },
            processResults: function(data) {
            // Transforms the top-level key of the response object from 'items' to 'results'
            console.log(data);
            return {
                results: data
            };
            }
        },
        
        });



        function cambiar_estado() {
            datos = parseFloat($('#base_ret_fuente').val());
            if (datos > 0) {
                $("#id_base_ret_fuente").prop('required', true);
            } else {
                $("#id_base_ret_fuente").prop('required', false);
            }

        }
        var fila = $("#mifila").html();
        $(".print").css('visibility', 'hidden');
        $(document).ready(function() {

            $('.select2_cuentas').select2({
                tags: false
            });

            $('input[type="checkbox"].flat-green').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });

            $('input[type="checkbox"].flat-red').iCheck({
                checkboxClass: 'icheckbox_flat-red',
                radioClass: 'iradio_flat-red'
            });
        });

        $(function() {

            $('.number').keypress(function(event) {

                if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
                    event.preventDefault(); //stop character from entering input
                }

            });

        });

        function nuevo() {
            // var nuevafila = $("#mifila").html();
            // var rowk = document.getElementById("agregar_cuentas").insertRow(-1);
            $("#form")[0].reset();

            $('.select2_cuentas').select2({
                tags: false
            });

        }

        function calcular() {
            var valor_origen = $("#valor_origen").val();
            var valor_destino = $("#valor_destino").val();
            var comision_tarjeta = $("#comision_tarjeta").val();
            var iva_retenido = $("#iva_retenido").val();
            var retencion_fuente = $("#retencion_fuente").val();
            var valores_adicionales = $("#valores_adicionales").val();
            var valores_destino = 0;

            var total = 0;
            var subtotal = 0;

            var base_ret_fuente = $("#base_ret_fuente").val();
            var base_iva = $("#baseiva").val();
            var id_base_ret_fuente = $("#id_base_ret_fuente option:selected").data('value');
            var id_base_iva = $("#id_base_iva option:selected").data('value');
            var retencion_iva = 0;
            var retencion_fuente = 0;
            if (base_ret_fuente != null && id_base_ret_fuente != null) {
                retencion_fuente = (parseFloat(base_ret_fuente) * parseFloat(id_base_ret_fuente)) / 100;
                retencion_fuente = redondea_precio(retencion_fuente, 2); //alert(retencion_fuente);
                $("#retencion_fuente").val(retencion_fuente);
            }
            if (id_base_iva != null && base_iva!=null) {
                retencion_iva = (parseFloat(base_iva) * parseFloat(id_base_iva)) / 100;
                retencion_iva =redondea_precio(retencion_iva,2);
                $("#iva_retenido").val(retencion_iva);
            }

            subtotal = redondea_precio(parseFloat(comision_tarjeta) + parseFloat(retencion_fuente) + parseFloat(retencion_iva) + parseFloat(valores_adicionales), 2);
            total = redondea_precio(parseFloat(valor_origen) - parseFloat(subtotal), 2);
            if (total == 'NaN') {
                total = 0;
            }
            // alert((retencion_fuente));
            $("#valor_destino").val(redondea_precio(total, 2));
            valores_destino = parseFloat(subtotal) + parseFloat(total);
            valores_destino = redondea_precio(valores_destino, 2);
            $("#total").val(redondea_precio(valores_destino, 2));
        }


        function devuelvefloat(cant, decimales) {
            var tmp = null;
            $.ajax({
                url: "{{route('transferenciabancaria.devuelvefloat')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                type: 'POST',
                datatype: 'json',
                async: false,
                data: {
                    cantidad: cant,
                    decimales: decimales
                },
                success: function(data) {
                    tmp = data.valor;
                },
                error: function(data) {
                    console.error(data.responseText);
                }
            });
            return tmp;
        }


        $(".btn_add").click(function() {
            document.querySelector(".btn_add").style.display = "none";

            if ($("#form").valid()) {
                $(".print").css('visibility', 'visible');
                $(".btn_add").attr("disabled", true);
                $.ajax({
                    url: "{{route('transferenciabancaria.store')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    type: 'POST',
                    datatype: 'json',
                    data: $("#form").serialize(),
                    success: function(data) {
                        console.log(data);
                        $("#asiento").val(data.id_asiento);
                        $("#id").val(data.id);
                        $("#numero").val(data.numero);

                        Swal.fire(
                            data.status.toUpperCase(),
                            data.msj,
                            data.status
                        )
                        if(data.status == "error"){
                                document.querySelector(".btn_add").style.display = "initial";
                        }else{
                            setTimeout(() => {
                                location.href ="{{route('transferenciabancaria.index')}}";
                                
                            }, 2000);
                        }
                    },
                    error: function(data) {
                        console.error(data.responseText);
                    }
                });
            }else{
                document.querySelector(".btn_add").style.display = "initial";
            }
        });
        function redondea_precio(value, decimales = 3) {
            value = +value;
            if (isNaN(value)) return NaN; // Shift 
            value = value.toString().split('e'); 
            value = Math.round(+(value[0] + 'e' + (value[1] ? (+value[1] + 2) : 2))); // Shift back 
            value = value.toString().split('e'); 
            return (+(value[0] + 'e' + (value[1] ? (+value[1] - 2) : -2))).toFixed(2);
        }
    </script>

</section>
@endsection