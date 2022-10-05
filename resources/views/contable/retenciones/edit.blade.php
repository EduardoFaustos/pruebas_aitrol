@extends('contable.retenciones.base')
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

    #datos_a {
        text-align: center;
    }
</style>
<script type="text/javascript">
    function goBack() {
        location.href = "{{ route('retenciones_index') }}";
    }
</script>
<div class="modal fade" id="visualizar_estado" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="#">Retenciones Proveedores</a></li>
            <li class="breadcrumb-item"><a href="{{ route('retenciones_index') }}">{{trans('contableM.retencion')}} </a></li>
            <li class="breadcrumb-item active" aria-current="page">Visualizar Retención</li>
        </ol>
    </nav>

    <form class="form-vertical" method="post" id="form_guardado">
        <div class="box">
            <div class="box-header header_new">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-3">
                            <div class="box-title "><b>VISUALIZADOR ACREEDORES-COMP. DE RETENCIONES</b></div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',[$retenciones->id_asiento_cabecera])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
                                    <i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.visualizarasiento')}}
                                </a>
                                <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="{{route('librodiario.edit',['id'=>$retenciones->id_asiento_cabecera])}}" target="_blank">
                                    <i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;&nbsp;Editar Asiento diario
                                </a>

                                <button type="button" onclick="goBack()" class="btn btn-success btn-xs btn-gray" style="padding:7px 27px">
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                                </button>


                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box-body  dobra">


                <form class="form" action="" id="form_guardado" method="post">

                    <div class="header row">
                        <input type="text" name="cont" id="cont" value="1" class="hidden">


                        <div class="form-group col-md-12 ">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="control-label ">{{trans('contableM.DATOSDELAFACTURA')}}</label>
                                </div>
                                <div class="col-md-4 col-xs-2 px-1">
                                    <label class="label_header" style="padding-left: 0px">{{trans('contableM.estado')}}</label>
                                    <input class="col-md-12 col-xs-12 form-control" style="@if(($retenciones->estado!=0)) background-color: green; @else background-color: red;  @endif" <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>>
                                </div>
                                <div class="col-md-4 col-xs-2 px-1">
                                    <label class="label_header">{{trans('contableM.asiento')}}</label>
                                    <input type="text" class="col-md-12 form-control" id="id_asiento" value="{{$retenciones->id_asiento_cabecera}}" readonly <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>>
                                </div>
                                <div class="col-md-4 px-1">
                                    <label class="control-label label_header">{{trans('contableM.fecha')}}</label>
                                    <input type="date" class="form-control " name="fecha_cambio" onchange="cambio()" value="{{$retenciones->fecha}}" id="fecha_cambio" <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>>
                                </div>
                                <div class="col-md-4 px-1">
                                    <label class="control-label label_header">{{trans('contableM.secuencia')}}</label>
                                    <input type="text" class="form-control " name="secuencial" value="@if(!is_null($retenciones)) {{$retenciones->secuencia}} @endif" id="secuencial" readonly <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>>
                                </div>

                                <div class="col-md-4 px-1">
                                    <label class="control-label label_header">{{trans('contableM.NVALORDELAFACTURA')}}</label>
                                    <input type="text" class="form-control " value="@if(isset($compras)) {{$compras->total_final}} @endif" name="valor_factura" id="valor_factura" readonly <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>>
                                </div>
                                <div class="col-md-4 px-1">
                                    <label class="control-label label_header">{{trans('contableM.proveedor')}}</label>
                                    <input type="text" class="form-control " name="proveedor_modal" value="@if(!is_null($retenciones)) {{$retenciones->id_proveedor}} @endif" id="proveedor_modal" readonly <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>>
                                </div>
                                <div class="col-md-4 px-1">
                                    <label class="control-label label_header">{{trans('contableM.concepto')}}</label>
                                    <input type="text" class="form-control " name="concepto" value="@if(isset($compras)) {{$compras->observacion}} @endif" id="concepto" readonly <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>>
                                </div>
                                <div class="col-md-4 px-1">
                                    <label class="control-label label_header">N° AUTORIZACIÓN DE RETENCIÓN</label>
                                    <input type="text" class="form-control " name="nro_autorizacion" id="nro_autorizacion" value="@if(!is_null($retenciones)) {{$retenciones->autorizacion}} @endif" <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive col-md-12 px-1">
                            <table id="example2" style="width: 100%;" class="table table-sm table table-condensed" role="grid" aria-describedby="example2_info">
                                <thead class="well-dark">
                                    <tr style="position: relative;">
                                        <th style=" text-align: center;">BASE IMP RET</th>
                                        <th style=" text-align: center;">{{trans('contableM.tipo')}}</th>
                                        <th style=" text-align: center;">{{trans('contableM.COD')}}</th>
                                        <th style=" text-align: center;">% DE RET</th>
                                        <th style=" text-align: center;">{{trans('contableM.VALORRETENIDO')}}</th>

                                    </tr>
                                </thead>
                                <tbody id="datos_a">
                                    @if(isset($detalle))
                                    @foreach($detalle as $value )
                                    <tr class="well">
                                        <td> <input style="width: 102%;" type="text" class="form-control" value="{{$value->base_imponible}}" readonly <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>> </td>
                                        <td> <input style="width: 102%;" type="text" class="form-control" value="{{$value->tipo}}" readonly <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>> </td>
                                        <td> <input style="width: 102%;" type="text" class="form-control" value="{{$value->porcentajer->codigo}}" readonly <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>></td>
                                        <td> <input style="width: 102%;" type="text" class="form-control" value="{{$value->porcentajer->valor}} %" readonly <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>> </td>
                                        <td> <input style="width: 102%;" type="text" class="form-control" value="{{$value->totales}}" readonly <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>> </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                        <input type="hidden" name="id_rentecion" id="id_retencion" value={{$id}} <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>>
                        <input type="hidden" name="cuenta_renta" id="cuenta_renta" <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>>
                        <input type="hidden" name="cuenta_iva" id="cuenta_iva">
                        <input type="hidden" name="eliminados" id="eliminados" value="0" <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>>
                        <input type="hidden" name="id_proveedor" id="id_proveedor" <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>>
                        <input type="hidden" name="id_compra" id="id_compra" value="0" <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>>
                        <input type="hidden" name="id_fact_contable" id="id_fact_contable" value="0" <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>>
                </form>
                <div class="col-md-12" style="margin-top: 10px;">
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            &nbsp;
                        </div>
                        <div class="form-group col-md-3">
                            &nbsp;
                        </div>
                        <div class="form-group col-md-3 px-0">
                            <label class="label_header">{{trans('contableM.totalrfir')}}</label>
                            <input class="form-control " value="@if(!is_null($retenciones)) {{$retenciones->valor_fuente}} @endif" type="text" name="total_rfirt" id="total_rfirt" <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>>

                        </div>
                        <div class="form-group col-md-3 px-0">
                            <label for="total_abonos" class="label_header">{{trans('contableM.totalrfiva')}}</label>
                            <input class="form-control " value="@if(!is_null($retenciones)) {{$retenciones->valor_iva}} @endif" type="text" name="total_rfivat" id="total_rfivat" <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>>

                        </div>
                    </div>
                </div>
                <div class="col-md-12" style="margin-top:20px">
                    <label class="control-label">{{trans('contableM.DetallededeudasdelProveedor')}}</label>
                    <input type="hidden" name="total_factura" id="total_factura" <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>>

                </div>
                <div class="col-12 ">
                    <div class="table-responsive col-md-12">
                        <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                            <thead class='well-dark'>
                                <tr style="position: relative;">

                                    <th style="width: 8%; text-align: center;">{{trans('contableM.vence')}}</th>
                                    <th style="width: 10%; text-align: center;">{{trans('contableM.tipo')}}</th>
                                    <th style="width: 10%; text-align: center;">{{trans('contableM.numero')}}</th>
                                    <th style="width: 10%; text-align: center;">{{trans('contableM.concepto')}}</th>
                                    <th style="width: 6%; text-align: center;">{{trans('contableM.div')}}</th>
                                    <th style="width: 6%; text-align: center;">{{trans('contableM.saldo')}}</th>
                                    <th style="width: 6%; text-align: center;">{{trans('contableM.abono')}}</th>
                                    <th style="width: 6%; text-align: center;">{{trans('contableM.saldobase')}}</th>
                                </tr>
                            </thead>
                            <tbody id="crear">



                                @php $cont=$retenciones->valor_fuente+$retenciones->valor_iva; $tot=0; if(isset($compras)){ $tot= $compras->total_final - $cont; } @endphp

                                <tr class="well">
                                    <!--AQUI VA EL DETALLE DE LAS RETENCIONES -->
                                    <td> <input class="form-control input-sm" type="text" name="vence{{$cont}}" value="@if(isset($compras)) {{$compras->f_caducidad}} @endif" id="vence{{$cont}}" readonly <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>> </td>
                                    <td> <input class="form-control input-sm" type="text" name="tipo{{$cont}}" value="@if(isset($compras)) @if(($compras->tipo)==1) COM-FA @else COM-FACT @endif @endif" id="tipo{{$cont}}" readonly <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>> </td>
                                    <td> <input class="form-control input-sm" type="text" name="numero{{$cont}}" value="@if(isset($compras)) {{$compras->numero}} @endif" id="numero{{$cont}}" readonly <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>> </td>
                                    <td> <input class="form-control input-sm" type="text" name="concepto{{$cont}}" value="@if(isset($compras)) {{$compras->observacion}} @endif" id="concepto{{$cont}}" readonly <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>> </td>
                                    <td> <input class="form-control input-sm" style="background-color: #c9ffe5; " type="text" name="div{{$cont}}" id="div{{$cont}}" value="$" readonly <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>> </td>
                                    <td> <input class="form-control input-sm" style="background-color: #c9ffe5;" type="text" name="saldo{{$cont}}" value="@if(isset($compras)) {{$compras->total_final}} @endif" id="saldo{{$cont}}" readonly <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>> </td>
                                    <td> <input class="form-control input-sm" style="background-color: #c9ffe5;  text-align: center;" value="{{number_format($cont,2)}}" type="text" name="abono{{$cont}}" id="abono{{$cont}}" readonly <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>></td>
                                    <td> <input class="form-control input-sm" style="text-align: center; width: 85%;" type="text" value="{{number_format($tot,2)}}" name="abono_base{{$cont}}" id="abono_base{{$cont}}" readonly <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>> </td>
                                </tr>



                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    </div>

                    <div class="col-md-12">
                        <div class="form-row">
                            <div class="form-group col-md-2 px-0">

                                <label for="total_ingresos" class="label_header">{{trans('contableM.totalegreso')}}</label>
                                <input class="form-control input-sm" type="text" name="total_egreso" value="{{number_format($cont,2)}}" id="total_egreso" readonly <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>>

                            </div>
                            <div class="form-group col-md-2 px-0">

                                <label for="credito_aplicado" class="label_header">{{trans('contableM.debitoaplicado')}}</label>
                                <input class="form-control input-sm" olor: red; text-align: right;" value="{{number_format($cont,2)}}" type="text" name="debito_aplicado" value="0.00" id="debito_aplicado" readonly <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>>

                            </div>
                            <div class="form-group col-md-2 px-0">

                                <label for="total_deudas" class="label_header">{{trans('contableM.totaldeudas')}}</label>
                                <input class="form-control input-sm" type="text" name="total_deudas" value="{{number_format($tot,2)}}" id="total_deudas" readonly <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>>

                            </div>
                            <div class="form-group col-md-2 px-0">

                                <label for="total_abonos" class="label_header">{{trans('contableM.totalabonos')}}</label>
                                <input class="form-control input-sm" type="text" name="total_abonos" value="{{number_format($tot,2)}}" id="total_abonos" readonly <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>>
                            </div>
                        </div>
                        <div class="form-group col-md-2 px-0">

                            <label for="nuevo_saldo" class="label_header">{{trans('contableM.nuevosaldo')}}</label>
                            <input class="form-control input-sm" type="text" name="nuevo_saldo" value="{{number_format($cont,2)}}" id="nuevo_saldo" readonly <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>>

                        </div>
                        <div class="form-group col-md-2">
                            <input type="hidden" name="retencion_fuente" id="retencion_fuente" <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>>
                            <input type="hidden" name="retencion_ivas" id="retencion_ivas" <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>>
                            <input type="hidden" name="retencion_totales" id="retencion_totales" <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>>
                            <input type="hidden" name="electronica" id="electronica" value="{{$retenciones->electronica}}" <?= $retenciones->estado_electronica == 0 ? 'disabled' : ($retenciones->estado_electronica == 5 ? 'disabled' : ''); ?>>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-12">
                &nbsp;
            </div>
        </div>






        </div>

    </form>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script type="text/javascript">
    function cambio() {
        var verificar = $("#electronica").val();
        console.log(verificar);
        var fecha = $("#fecha_cambio").val();
        var id_asiento = $("#id_asiento").val();
        var id_retencion = $("#id_retencion").val();
        var opcion = confirm("Desea cambiale la fecha ?");
        if (verificar == '0') {

            if (opcion == true) {
                $.ajax({
                    url: "{{route('actualizar_fecha_nueva')}}",
                    data: {
                        'fecha': fecha,
                        'id_asiento': id_asiento,
                        'id_rentecion': id_retencion,
                    },
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data == 'ok') {
                            swal(`{{trans('contableM.correcto')}}!`, "Editado", "success");
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        //alert('Existió un problema');
                        console.log(xhr);
                    },
                });
            }

        } else {
            swal("Incorrecto!", "Es electronico", "error");
        }
    }
</script>
@endsection