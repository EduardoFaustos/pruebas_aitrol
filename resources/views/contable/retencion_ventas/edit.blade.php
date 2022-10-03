@extends('contable.retenciones.base')
@section('action-content')
<script type="text/javascript">
    
    function goBack() {
      window.history.back();
    }
</script>
<section class="content">
    
    <form class="form-vertical" method="post" id="form_guardado">
        <div class="box box-default box-solid "  style=" background-color: white;">
                <div class="header box-header with-border">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-9 col-sm-9 col-6">
                                <div class="box-title" ><b style="font-size: 16px; size_text">ACREEDORES-COMP. DE RETENCIONES</b></div>
                            </div>
                            <div class="col-3">
                                <div class="row">
                                    <button type="button" onclick="crear_retenciones()" id="boton_guardar" class="btn btn-primary" style="color:white; border-radius: 5px; border: 2px solid white;"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.guardar')}}
                                    </button>
                                    <button type="button" onclick="goBack()" class="btn btn-primary" style="color:white; border-radius: 5px; border: 2px solid white;">
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-body" style="background-color: #ffffff; size_text">
                    <div class="col-12 col-xs-12">
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label for="buscar" class = "col-form-label-sm">{{trans('contableM.buscar)}}</label>
                                <input disabled type="text" value="@if(!is_null($retenciones)){{$retenciones->secuencia}} @endif" id = "buscar" name="buscar" class = "form-control form-control-sm buscar" disabled onchange="buscar_factura()">
                                
                            </div>
                            <div class="form-group col-md-1">
                                <label style="padding-left: 0px">{{trans('contableM.estado')}}</label>
                                <div style="background-color: green; " class="form-control col-md-1"></div>           
                            </div>
                            <div class="form-group col-md-2">
                                <div class="input-group">
                                    <label class="col-md-12" for="id_factura">{{trans('contableM.id')}}:</label>
                                    <input  style="width: 80%;" type="text" name="id_factura" id="id_factura" disabled>    
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <div class="input-group">
                                    <label for="numero_factura">{{trans('contableM.numero')}}</label>
                                    <input style="width: 80%;" type="text" id="numero_factura" name="numero_factura" value="@if(!is_null($retenciones)){{$retenciones->secuencia}} @endif" disabled>
                                </div>
                            </div>
                            <div class="form-group col-md-1">
                                <div class="input-group">
                                    <label for="asiento">{{trans('contableM.asiento')}}</label>
                                    <input type="text" id="asiento" name="asiento" value="@if(!is_null($retenciones)){{$retenciones->id}} @endif" style="width: 125%;" disabled>
                                </div>
                               
                            </div>
                            <div class="form-group col-md-2" style="padding-left: 58px;">
                                <div class="input-group">
                                    <label class="col-md-12" for="fecha_hoy">{{trans('contableM.fecha')}}: </label>
                                    <input type="date" name="fecha_hoy" id="fecha_hoy" value="{{date('Y-m-d')}}" disabled>
                                </div>
                            </div>
                            <div class="form-group col-md-1" style="padding-left: 20px;">
                                <label for="tipo">{{trans('contableM.tipo')}}: </label>
                                <select name="tipo" id="tipo" disabled="disabled">
                                <option value="0">CLI-RT</option>
                                </select>
                            </div>
                            <div class="form-group col-md-1" style="padding-left: 20px;">
                                <div class="input-group">
                                    <label class="col-md-12" for="proyecto">{{trans('contableM.proyecto')}}: </label>
                                    <select name="proyecto" id="proyecto" disabled="disabled">
                                        <option value="0">0000</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                               &nbsp;
                            </div>
                            <div class="form-group col-md-3">
                                <div class="input-group">
                                    <label class="col-md-12" for="acreedor">{{trans('contableM.acreedor')}}:</label>
                                    <input type="text" name="acreedor" id="acreedor" value="@if(!is_null($retenciones)){{$retenciones->id_proveedor}} @endif" disabled>
                                </div>
                            </div>
                            @php
                                $retenciiones_direccion= DB::table('proveedor')->where('id',$retenciones->id_proveedor)->first();
                            @endphp
                            <div class="form-group col-md-3">
                                <div class="input-group">
                                    <label class="col-md-12" for="direccion">{{trans('contableM.direccion')}}:</label>
                                    <input type="text" name="direccion" id="direccion" value="@if(!is_null($retenciiones_direccion)){{$retenciiones_direccion->direccion}} @endif" disabled>
                                </div>
                            
                            </div>
                            <div class="form-group col-md-2">
                                <div class="input-group">
                                    <label class="col-md-12" for="ruc">{{trans('contableM.ruc')}}:</label>
                                    <input type="text" name="ruc" id="ruc" value="@if(!is_null($retenciiones_direccion)){{$retenciiones_direccion->direccion}} @endif" style="width:115%;" disabled>
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <div class="input-group">   
                                    <label class="col-md-12" for="serie">Serie:</label>
                                    <input type="text" name="serie" id="serie" style="width:115%;" disabled>
                                </div> 
                            </div>
                            <div class="form-group col-md-2">
                                <div class="input-group">
                                    <label class="col-md-12" for="secuencia">{{trans('contableM.secuencia')}}</label>
                                    <input type="text" name="secuencia" id="secuencia" value="@if(!is_null($retenciones)){{$retenciones->secuencia}} @endif" style="width:115%;" disabled>
                                </div>
                            </div>
                            <div class="form-group col-md-8">
                                <div class="input-group">
                                    <label  class="col-md-12" for="concepto">{{trans('contableM.concepto')}}:</label>
                                    <input type="text" name="concepto" id="concepto" value="@if(!is_null($retenciones)){{$retenciones->descripcion}} @endif" style="width:200%;" disabled> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 ">
                        <input type="hidden" name="id_compra" id="id_compra">
                        <div class="table-responsive col-md-12" style="min-height: 250px; max-height: 250px; top: 20px;">
                                                <table id="example2" role="grid" aria-describedby="example2_info">
                                                    <thead style="background-color: #FFF3E3">
                                                    <tr style="position: relative;">
                                                        <th style="width: 8%; text-align: center;">{{trans('contableM.factura')}}</th>
                                                        <th style="width: 8%; text-align: center;">{{trans('contableM.divisas')}}</th>
                                                        <th style="width: 8%; text-align: center;">{{trans('contableM.basefuente')}}</th>
                                                        <th style="width: 8%; text-align: center;">{{trans('contableM.tiporfir')}}</th>
                                                        <th style="width: 8%; text-align: center;">{{trans('contableM.totalrfir')}}</th>
                                                        <th style="width: 8%; text-align: center;">{{trans('contableM.baseiva')}}</th>
                                                        <th style="width: 8%; text-align: center;{{trans('contableM.tiporfiva')}}th>
                                                        <th style="width: 8%; text-align: center;">{{trans('contableM.totalrfiva')}}</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="crear">
                                                    @php $cont=0; @endphp
                                                    @foreach (range(1, 2) as $i)
                                                        <tr>
                                                            <td> <input style="width: 90%;" type="text" name="numero_referencia{{$cont}}" id="numero_referencia{{$cont}}" disabled> </td>
                                                            <td> 
                                                            <select name="divisas{{$cont}}" id="divisas{{$cont}}" disabled>
                                                                <option value="0">Seleccione...</option>
                                                                @foreach($divisas as $value)
                                                                <option value="{{$value->id}}">{{$value->descripcion}}</option>
                                                                @endforeach
                                                            </select> 
                                                            
                                                            </td>
                                                            <td> <input style="width: 90%; text-align: center;" type="text" name="base_fuente{{$cont}}" id="base_fuente{{$cont}}" disabled></td>
                                                            <td> <select name="tipo_rfir{{$cont}}" id="tipo_rfir{{$cont}}" onchange="lista_valores({{$cont}})" disabled>
                                                                
                                                                 @foreach($rfir as $value)
                                                                    <option {{ $retenciones->rfir == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option>
                                                                 @endforeach
                                                                </select> </td>
                                                            <td> <input disabled  style="width: 90%; text-align: center;"type="text" name="total_rfir{{$cont}}" id="total_rfir{{$cont}}" > </td>
                                                            <td> <input disabled style="width: 90%; text-align: center;" type="text" name="base_iva{{$cont}}" id="base_iva{{$cont}}"> </td>
                                                            <td> <select  name="tipo_rfiva{{$cont}}"  id="tipo_rfiva{{$cont}}" onchange="lista_valores2({{$cont}})" disabled>
                                                                    
                                                                    @foreach($rfiva as $value)
                                                                        <option {{ $retenciones->rfiva == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option>
                                                                    @endforeach
                                                                 </select> 
                                                            </td>
                                                            <td> <input disabled style="width: 90%; text-align: center;" disabled type="text" name="total_rfiva{{$cont}}" id="total_rfiva{{$cont}}" value="{{$retenciones->total}}"> </td>
                                                        </tr>
                                                        @php $cont = $cont +1; @endphp
                                                    @endforeach
                                                        
                                                    </tbody>
                                                </table>
                        </div>
                        <div class="col-md-12" style="margin-top: 30px;">
                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <label for="retencion_impuesto">{{trans('contableM.RetImpRenta')}}</label>
                                </div>
                                <div class="form-group col-md-2">
                                    <input type="text" name="retencion_impuesto" id="retencion_impuesto" disabled>
                                </div>
                                <div class="form-group col-md-3" style="text-align: right;">
                                    <label for="">{{trans('contableM.RetIVA')}}</label>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <input type="text" name="retencion_iva" id="retencion_iva" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 ">
                        <label>{{trans('contableM.DetallededeudasdelProveedor')}}</label>
                        <input type="hidden" name="total_factura" id="total_factura">
                    </div>
                    <div class="col-12 ">
                        <div class="table-responsive col-md-12" style="min-height: 250px; max-height: 250px; top: 20px;">
                                                
                                                <table id="example2" role="grid" aria-describedby="example2_info">
                                                    <thead style="background-color: #FFF3E3">
                                                    <tr style="position: relative;">
                                                        
                                                        <th style="width: 8%; text-align: center;">{{trans('contableM.vence')}}</th>
                                                        <th style="width: 10%; text-align: center;">{{trans('contableM.tipo')}}</th>
                                                        <th style="width: 10%; text-align: center;">{{trans('contableM.numero')}}</th>
                                                        <th style="width: 10%; text-align: center;">{{trans('contableM.concepto')}}</th>
                                                        <th style="width: 6%; text-align: center;">{{trans('contableM.div')}}</th>
                                                        <th style="width: 6%; text-align: center;">{{trans('contableM.saldo')}}</th>
                                                        <th style="width: 6%; text-align: center;">{{trans('contableM.abono')}}</th>
                                                        <th style="width: 6%; text-align: center;">{{trans('contableM.saldobase')}}</th>
                                                        <th style="width: 6%; text-align: center;">{{trans('contableM.abonobase')}}</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="crear">
                                                    @php $cont=0; @endphp
                                                    @foreach (range(1, 2) as $i)
                                                        <tr>
                                                            <!-- GEORGE AQUI VA EL DETALLE DE LAS RETENCIONES -->
                                                            <td> <input type="text" name="vence{{$cont}}" id="vence{{$cont}}" readonly> </td>
                                                            <td> <input type="text" name="tipo{{$cont}}" id="tipo{{$cont}}" readonly> </td>
                                                            <td> <input style="width: 90%;" type="text" name="numero{{$cont}}" id="numero{{$cont}}" readonly> </td>
                                                            <td> <input type="text" name="concepto{{$cont}}" id="concepto{{$cont}}" readonly> </td>
                                                            <td> <input style="background-color: #c9ffe5; width: 150%;" type="text" name="div{{$cont}}" id="div{{$cont}}" value="$" readonly> </td>
                                                            <td> <input style="background-color: #c9ffe5; width: 150% " type="text" name="saldo{{$cont}}" id="saldo{{$cont}}" readonly> </td>
                                                            <td> <input style="background-color: #c9ffe5; width: 150%; text-align: right;" type="text" name="abono{{$cont}}" id="abono{{$cont}}" readonly></td>
                                                            <td> <input style="width: 150%; text-align: left;" type="text" name="nuevo_saldo{{$cont}}" id="nuevo_saldo{{$cont}}" readonly></td>
                                                            <td> <input style="width: 150%; text-align: center;" type="text" name="abono_base{{$cont}}" id="abono_base{{$cont}}" readonly> </td>
                                                        </tr>
                                                        @php $cont = $cont +1; @endphp
                                                    @endforeach
                                                        
                                                    </tbody>
                                                    <tfoot>
                                                    </tfoot>
                                                </table>
                        </div>
                        
                        <div class="col-md-12" style="margin-top: 20px;">
                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label for="total_ingresos">{{trans('contableM.totalegreso')}}</label>
                                        <input style="width: 90%;" type="text" name="total_egreso" id="total_egreso" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label for="credito_aplicado">{{trans('contableM.debitoaplicado')}}</label>
                                        <input style="width: 90%; color: red; text-align: right;" type="text" name="debito_aplicado" value="0.00" id="debito_aplicado" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label for="total_deudas">{{trans('contableM.totaldeudas')}}</label>
                                        <input style="width: 90%;" type="text" name="total_deudas" id="total_deudas" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label for="total_abonos">{{trans('contableM.totalabonos')}}</label>
                                        <input style="width: 90%;" type="text" name="total_abonos" id="total_abonos" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label for="nuevo_saldo">{{trans('contableM.nuevosaldo')}}</label>
                                        <input style="width: 90%;" type="text" name="nuevo_saldo" id="nuevo_saldo" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label for="deficit">{{trans('contableM.deficit')}}</label>
                                        <input style="width: 90%; color: red;" type="text" name="deficit" id="deficit" value="0.00" readonly>                                    
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label for="credito_favo">{{trans('contableM.creditoafavor')}}</label>
                                        <input style="width: 90%;" type="text" name="credito_favor" id="credito_favor" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="col-md-12">
                        &nbsp;
                    </div>
                    <div class="col-md-12">
                        <div class="input-group">
                            <label class="col-md-12" style="background-color: #bbb0ad;" for="nota">{{trans('contableM.nota')}}:</label>
                            <textarea class="col-md-12" name="nota" id="nota" cols="200" rows="5" readonly value="{{$retenciones->descripcion}}"></textarea>
                        </div>
                    </div>
                </div>
               
        </div>

    </form>
</section>



@endsection