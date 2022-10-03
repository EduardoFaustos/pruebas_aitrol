@extends('activosfijos.documentos.factura.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=PT+Sans&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<style>
    .input-number {
        width: 80%;
        height: 20px;
    }

    .content {
        font-family: 'PT Sans', sans-serif !important;
    }

    .table-responsive .form-control {
        border-radius: 5px !important;
        /* padding: 5px; */
    }

    .block {
        display: block;
    }

    .none {
        display: none;
    }
</style>


<div class="modal fade" id="visualizar_estado" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Activos Fijos</a></li>
            <li class="breadcrumb-item"><a href="#">Documentos</a></li>
            <li class="breadcrumb-item"><a href="javascript:goBack();">Factura Activo Fijo</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nueva</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-6">
                        <h3 class="box-title">Nueva Factura de Activo Fijos</h3>
                    </div>
                    <div class="col-6" style="text-align: center;">
                        <div class="row">

                            @if ($cabecera->estado != 0)
                            <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',[$cabecera->id_asiento])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
                                <i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i>&nbsp;&nbsp;Visualizar Asiento diaro
                            </a>
                            <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="{{route('librodiario.edit',['id'=>$cabecera->id_asiento])}}" target="_blank">
                                <i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;&nbsp;Editar Asiento diaro
                            </a>
                            @endif
                            <button onclick="goBack()" class="btn btn-default btn-gray">
                                <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body dobra">
        <form class="form-vertical" id="form">
            {{ csrf_field() }}
            <div class="header row">
                <div class="form-group col-xs-6 col-md-2 px-1">
                    <div class="col-md-12 px-0">
                        <label for="id" class=" label_header">Id</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input type="text" class="form-control" name="id" id="id" value="{{ $cabecera->id }}" readonly>
                        @if ($errors->has('id'))
                        <span class="help-block">
                            <strong>{{ $errors->first('id') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-6 col-md-2 px-1">
                    <div class="col-md-12 px-0">
                        <label for="numero" class=" label_header">Número</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input type="text" class="form-control" name="numero" id="numero" value="{{$cabecera->numero}}" readonly>
                        @if ($errors->has('numero'))
                        <span class="help-block">
                            <strong>{{ $errors->first('numero') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="tipo" class="label_header">Tipo</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input type="text" class="form-control" name="tipo_transaccion" id="tipo_transaccion" value="ACT-FA" readonly>
                        @if ($errors->has('tipo'))
                        <span class="help-block">
                            <strong>{{ $errors->first('tipo') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-6 col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="asiento_id" class="label_header">Asiento</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input type="text" class="form-control" name="asiento_id" id="asiento_id" value="{{ $cabecera->id_asiento }}" readonly>
                        @if ($errors->has('asiento_id'))
                        <span class="help-block">
                            <strong>{{ $errors->first('asiento_id') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="fecha_asiento" class="label_header">Fecha Asiento</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input id="fecha_asiento" type="text" class="form-control" name="fecha_asiento" value="{{ date('d/m/Y', strtotime($cabecera->fecha_asiento)) }}" readonly>
                        @if ($errors->has('fecha_asiento'))
                        <span class="help-block">
                            <strong>{{ $errors->first('fecha_asiento') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="fecha_caduca" class="label_header">Fecha Caduca</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input id="fecha_caduca" type="text" class="form-control" name="fecha_caduca" value="{{ date('d/m/Y', strtotime($cabecera->fecha_caduca)) }}" readonly>
                        @if ($errors->has('fecha_caduca'))
                        <span class="help-block">
                            <strong>{{ $errors->first('fecha_caduca') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>


                @php $proveedor = \Sis_medico\proveedor::where('id', $cabecera->proveedor)->first();  @endphp
                <div class="form-group col-xs-6  col-md-6  px-1">
                    <div class="col-md-12 px-0">
                        <label for="proveedor" class="label_header">Proveedor</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <select class="form-control" id="proveedor" name="proveedor" disabled>
                            <option value="{{ $proveedor->id }}" >{{ $proveedor->razonsocial }}</option>
                        </select>
                        @if ($errors->has('proveedor'))
                        <span class="help-block">
                            <strong>{{ $errors->first('proveedor') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="divisas" class="label_header">Divisas</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <select id="divisas" name="divisas" class="form-control" disabled>
                            @foreach($divisas as $value)
                            <option value="{{$value->id}}">{{$value->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="termino" class="label_header">Término</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <select id="termino" name="termino" class="form-control" disabled>
                            <option value="">Seleccione...</option>
                            <option value="1" @if('1'==$cabecera->termino) selected @endif >30 Dias</option>
                            <option value="2" @if('2'==$cabecera->termino) selected @endif >60 Dias</option>
                        </select>
                        <input type="hidden" class="form-control input-sm" name="termino" id="termino" value="{{ old('termino')}}" placeholder="Termino" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label class="label_header">Credito Tributario</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <select name="credito_tributario" id="cred_tributario" class="form-control " disabled style="width: 100%">
                            <option value="">Seleccione...</option>
                            @foreach($c_tributario as $value)
                            <option @if($cabecera->credito_tributario==$value->codigo) selected="selected" @endif value="{{$value->codigo}}">{{$value->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <div class="form-group col-xs-6 col-md-2 px-1">
                    <div class="col-md-12 px-0">
                        <label for="ord_compra" class=" label_header">O. Compra</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input type="text" class="form-control" name="ord_compra" id="ord_compra" value="{{ $cabecera->ord_compra }}" readonly>
                        @if ($errors->has('ord_compra'))
                        <span class="help-block">
                            <strong>{{ $errors->first('ord_compra') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group col-xs-6 col-md-2 px-1">
                    <div class="col-md-12 px-0">
                        <label for="nro_autorizacion" class=" label_header">Autorización</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input type="text" class="form-control" name="nro_autorizacion" id="nro_autorizacion" value="{{ $cabecera->nro_autorizacion }}" readonly>
                        @if ($errors->has('nro_autorizacion'))
                        <span class="help-block">
                            <strong>{{ $errors->first('nro_autorizacion') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group col-xs-6 col-md-2 px-1">
                    <div class="col-md-12 px-0">
                        <label for="fecha_compra" class=" label_header">Fecha</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input type="text" class="form-control col-md-12" name="fecha_compra" id="fecha_compra" value="{{ date('d/m/Y', strtotime($cabecera->fecha_compra)) }}" readonly>

                        @if ($errors->has('fecha_compra'))
                        <span class="help-block">
                            <strong>{{ $errors->first('fecha_compra') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group col-xs-6 col-md-2 px-1">
                    <div class="col-md-12 px-0">
                        <label for="serie_factura" class=" label_header">Serie</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input type="text" class="form-control" name="serie_factura" id="serie_factura" value="{{ $cabecera->serie }}" maxlength="7" readonly>
                        @if ($errors->has('serie_factura'))
                        <span class="help-block">
                            <strong>{{ $errors->first('serie_factura') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group col-xs-6 col-md-2 px-1">
                    <div class="col-md-12 px-0">
                        <label for="secuencia" class=" label_header">Secuencia</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input type="text" class="form-control" name="secuencia" id="secuencia" value="{{ $cabecera->secuencia }}" readonly>
                        @if ($errors->has('secuencia'))
                        <span class="help-block">
                            <strong>{{ $errors->first('secuencia') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="tipo_comprobante" class="label_header">Tipo comprobante</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <select class="form-control" id="tipo_comprobante" name="tipo_comprobante" disabled>
                            <option></option>
                            @foreach ($tipos_comp as $value)
                            <option value="{{ $value->codigo }}" @if($value->codigo == $cabecera->tipo_comprobante) selected @endif >{{ $value->nombre }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('tipo_comprobante'))
                        <span class="help-block">
                            <strong>{{ $errors->first('tipo_comprobante') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-12 table-responsive">
                <input type="hidden" name="contador" id="contador" value="0">
                <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                        <tr class=''>
                            <th width="5%" class="" tabindex="0">&nbsp;</th>
                            <th width="%" class="" tabindex="0">Codigo</th>
                            <th width="35%" class="" tabindex="0">Descripción del Activo</th>
                            <th width="10%" class="" tabindex="0">Cantidad</th>
                            <th width="10%" class="" tabindex="0">Costo</th>
                            <th width="10%" class="" tabindex="0">% Desc</th>
                            <th width="10%" class="" tabindex="0">Descuento</th>
                            <th width="10%" class="" tabindex="0">Total</th>
                            <th width="10%" class="" tabindex="0">Iva</th>


                        </tr>
                    </thead>
                    <tbody id="agregar_cuentas">

                        @foreach ($detalles as $value)
                        <tr id='mifila'>
                            <td style='max-width:100px;'>
                                @if(!is_null($value->activo_id))
                                <button onclick="md_activo({{$value->activo_id}})" type='button' class='btn btn-info btn-gray btn-xs'>
                                    <i class='glyphicon glyphicon-search' aria-hidden='true'></i>
                                </button>
                                @else 
                                <button type='button' class='btn btn-danger btn-gray btn-xs'>
                                    <i class='glyphicon glyphicon-remove' aria-hidden='true'></i>
                                </button>
                                @endif
                            </td>

                            <td>
                                <input class='form-control text-right' name='codigo[]' type='text' style='width: 70%;height:20px;' @if(!is_null($value->activo)) value="{{ $value->activo->codigo }}" @endif disabled>
                            </td>
                            <td>
                            @php
                            
                            $nombre ='';
                            $descri = '';

                            if(is_null($value->nombre)){
                                $nombre = $value->activo->nombre;
                            }else{
                                $nombre = $value->nombre;
                            }


                            if(is_null($value->observacion)){

                                


                            }

                            @endphp
                            <input class="form-control" type="text" style="width: 93%;height:20px;" value="{{ $nombre }}"  disabled>
                                <textarea rows='3' name='descrip_prod[]' disabled class="form-control" style="width: 95%;" disabled>{{$value->observacion}}</textarea>

                            </td>
                            <td>
                                <input class='form-control text-right' name='cantidad[]' type='text' style='width: 80%;height:20px;' value="{{ $value->cantidad }}" disabled>
                            </td>
                            <td>
                                <input class='form-control text-right' name='costo[]' type='text' style='width: 80%;height:20px;' value="{{ $value->costo }}" disabled>
                            </td>
                            <td>
                                <input class='form-control text-right' name='descpor[]' type='text' style='width: 80%;height:20px;' value="{{ $value->porc_descuento }}" disabled>
                            </td>
                            <td>
                                <input class='form-control text-right' name='desc[]' type='text' style='width: 80%;height:20px;' value="{{ $value->descuento }}" disabled>
                            </td>
                            <td>
                                <input class='form-control px-1 text-right' name='total[]' type='text' style='height:20px;' value="{{ $value->total }}" disabled>
                            </td>
                            <td>
                                <input class="form" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]" id="valoriva" value="1" @if($value->iva == 1) checked @endif disabled>
                            </td>
                        <tr>
                            @endforeach
                    </tbody>
                    <tfoot>

                        <tr>
                            <td colspan="7"></td>
                            <td colspan="2" class="text-right">Subtotal</td>
                            <td id="base" class="text-right px-1">{{ $cabecera->subtotal }}</td>

                            <input type="hidden" name="base1" id="base1" value="{{ $cabecera->subtotal }}" class="hidden">
                        </tr>

                        <tr>
                            <td colspan="7"></td>
                            <td colspan="2" class="text-right">Descuento</td>
                            <td id="descuento" class="text-right px-1">{{ $cabecera->descuento }}</td>
                            <input type="hidden" name="descuento1" id="descuento1" value="{{ $cabecera->descuento }}" class="hidden">
                        </tr>

                        <tr>
                            <td colspan="7"></td>
                            <td colspan="2" class="text-right">Tarifa Iva 12%</td>
                            <td id="tarifa_iva" class="text-right px-1">{{ $cabecera->impuesto }}</td>
                            <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" value="{{ $cabecera->impuesto }}" class="hidden">
                        </tr>

                        <tr>
                            <td colspan="7"></td>
                            <td colspan="2" class="text-right"><strong>Total</strong></td>
                            <td id="total" class="text-right px-1">{{ $cabecera->total }}</td>
                            <input type="hidden" name="total1" id="total1" value="{{ $cabecera->total }}" class="hidden">
                        </tr>

                    </tfoot>
                </table>
            </div>
            <div class="form-group col-xs-10 text-center">
                {{--<button class="btn btn-success btn-gray" onclick="guardar(event)" id="boton_guardar"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                    </button>--}}
            </div>

            <div class="modal fade" id="md-activo-fijo">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Activo Fijos</h4>

                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Codigo</label>
                                <div class="col-xs-4">
                                    <input class="form-control" type="text" id="mdcodigo" disabled>
                                </div>
                                <div class="col-xs-1"><span>-</span></div>
                                <div class="col-xs-4">
                                    <input class="form-control" type="text" id="mdcodigo_num" disabled>
                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Nombre</label>
                                <div class="col-xs-10">
                                    <input class="form-control" type="text" id="mdnombre" disabled>
                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Descripción</label>
                                <div class="col-xs-10">
                                    <input class="form-control" type="text" id="mddescripcion" name="mddescripcion" disabled>
                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Tipo</label>
                                <div class="col-xs-10">
                                    <input class="form-control" type="text" id="mdtipo" name="mdtipo" disabled>
                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Categoria</label>
                                <div class="col-xs-10">
                                    <input class="form-control" type="text" id="mdgrupo" name="mdgrupo" disabled>
                                </div>
                            </div> <br>

                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Responsable</label>
                                <div class="col-xs-10">
                                    <input class="form-control" type="text" id="mdresponsable" name="mdresponsable" disabled>

                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Ubicación</label>
                                <div class="col-xs-10">
                                    <input type="text" name="mdubicacion" id="mdubicacion" class="form-control" disabled>
                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Marca</label>
                                <div class="col-xs-10">
                                    <input type="text" name="mdmarca" id="mdmarca" class="form-control" disabled>
                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Color</label>
                                <div class="col-xs-10">
                                    <input type="text" name="mdcolor" id="mdcolor" class="form-control" disabled>
                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Modelo</label>
                                <div class="col-xs-10">
                                    <input class="form-control" type="text" id="mdmodelo" name="mdmodelo" disabled>
                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Serie</label>
                                <div class="col-xs-10">
                                    <input class="form-control" type="text" id="mdserie" name="mdserie" disabled>

                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Procedencia</label>
                                <div class="col-xs-10">
                                    <input class="form-control" type="text" id="mdprocedencia" name="mdprocedencia" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="box box-default">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Datos del activo fijo</h3>
                                </div>
                                <div class="box-body">
                                    <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                                        <thead>
                                            <tr class=''>
                                                <th width="25%" class="" tabindex="0">Nombre</th>
                                                <th width="25%" class="" tabindex="0">Marca</th>
                                                <th width="25%" class="" tabindex="0">Modelo</th>
                                                <th width="25%" class="" tabindex="0">Serie</th>
                                            </tr>
                                        </thead>
                                        <tbody id="agregar_accesorios">
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-danget" style="margin:0px" data-dismiss="modal">Cerrar</button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
    </div>
    </form>


</section>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>


<script type="text/javascript">

    $('#md-activo-fijo').on('hidden.bs.modal', function(){
        location.reload();
      $(this).removeData('bs.modal');
    });
    function goBack() {
        window.history.back();
    }
    let contView = 0

    function md_activo(id) {
        $.ajax({
            type: 'GET',
            url: "{{route('documentofactura.search_acive')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {

                'id': id
            },
            success: function(data) {

                for (let index = 0; index < data.detalles.length; index++) {

                    if (contView < data.detalles.length) {
                        contView++;
                        var tr = `<tr class="columnas"> 
                            <td>
                                <input required name="nombre_ac${index}[]" value="${data.detalles[index]['nombre']}" id="nombre_ac${index}" class="form-control" style="height:25px;width:90%;" autocomplete="off" disabled>
                            </td>

                            <td>
                            <input required id="marca_ac${index}" name="marca_ac${index}[]" value="${data.detalles[index]['marca']}"  class="form-control" style="height:25px;width:90%;" autocomplete="off" disabled >
                            </td>

                            <td> 
                                <input type="text" name="modelo_ac${id}[]" id="modelo_ac${index}"  value="${data.detalles[index]['modelo']}" class="form-control cant" style="height:25px;width:75%;" autocomplete="off" disabled >
                            </td>

                            <td>
                            <input type="text" name="serie_ac${id}[]" id="serie_ac${index}"  value="${data.detalles[index]['serie']}" class="form-control cant" style="height:25px;width:75%;" autocomplete="off" disabled >
                            </td>
                        </tr> `;
                        $("#agregar_accesorios").append(tr);

                    }
                }
                let datos = data.res;
                document.getElementById("mdcodigo").value = datos.codigo_text;
                document.getElementById("mdcodigo_num").value = datos.codigo_num;




                document.getElementById("mdnombre").value = datos.nombre;
                document.getElementById("mddescripcion").value = datos.descripcion;
                document.getElementById("mdtipo").value = data.tipo;

                document.getElementById("mdgrupo").value = data.subtipo;


                document.getElementById("mdresponsable").value = datos.responsable;

                document.getElementById("mdubicacion").value = datos.ubicacion;
                document.getElementById("mdmarca").value = datos.marca;

                document.getElementById("mdcolor").value = datos.color;

                document.getElementById("mdmodelo").value = datos.modelo;

                document.getElementById("mdserie").value = datos.serie;

                document.getElementById("mdprocedencia").value = datos.procedencia;



                $('#md-activo-fijo').modal('show');
            },
            error: function(data) {

            }
        });
    }
</script>

@endsection