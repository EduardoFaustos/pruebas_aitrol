<link href="{{ asset("/bower_components/select2/dist/css/select2.min.css")}}" rel="stylesheet" type="text/css" />
<style type="text/css">
    .vtdobra {
        background-color: #eafcff;
    
        padding: 0;
    }
    .vtdobra2 {
        background-color: #eafcff;
    
        padding: 0;
    }
    .select2 {
        width: 100% !important;
        background-color: #eafcff !important;
    }
    .vt_middle{
        vertical-align: middle;
    }
</style>

<div class="panel panel-default">
    <div class="panel-body" style="padding:0;">
        <div class="row">
            <div class="col-md-2">
                <div class="form-group" style="padding-left: 10px;">
                    <br>
                    <label>{{trans('new_recibo.Plantilla')}}</label>
                </div>
            </div>   
            <div class="col-md-6">
                <div class="form-group">
                    <select id="agrupador" name="agrupador" class="form-control select2_agrupador" required placeholder="{{trans('new_recibo.Plantilla')}}" onchange="seleccionar_agrupador();">     
                    </select>
                </div>
            </div>    
        </div>    
        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
                <tr class='well' style="color: black;">
                    <th width="50%" tabindex="0">Descripcion del Producto</th>
                    <th width="5%" tabindex="0">Cant</th>
                    <th width="5%" tabindex="0">{{trans('contableM.precio')}}</th>
                    <th width="5%" tabindex="0">% <br>CP</th>
                    <th width="5%" tabindex="0">CoP</th>
                    <th width="5%" tabindex="0">Dedu</th>
                    <th width="5%" tabindex="0">% {{trans('contableM.prctdesc')}}</th>
                    <th width="5%" tabindex="0">Desc</th>
                    <th width="5%" tabindex="0">{{trans('contableM.precioneto')}}</th>
                    <th width="5%" tabindex="0">{{trans('contableM.iva')}}</th>
                    <th width="5%" tabindex="0">{{trans('contableM.accion')}}</th>
                </tr>
            </thead>
            <tbody >
                @foreach($detalles as $detalle)
                    <tr>
                        <td>
                            <div class="form-group" style="margin-bottom: 1px;">
                                <label>{{ $detalle->producto->nombre }}</label><br>
                                <input class="id_producto" type="hidden" name="id_producto" id="id_producto{{$detalle->id}}" value="{{ $detalle->id_producto}}">
                                <label style="font-size: 9px;">Detalle del Producto</label>
                                <input type="text" class="form-control vtdobra" name="descripcion{{$detalle->id}}" id="descripcion{{$detalle->id}}" value="{{$detalle->descripcion}}" onchange="actualizar_descripcion('{{$detalle->id}}')">
                            </div>
                        </td>
                        <td style="vertical-align: middle;">
                            <div class="form-group">
                                <input type="text" class="form-control vtdobra" name="cantidad{{$detalle->id}}" id="cantidad{{$detalle->id}}" value="{{$detalle->cantidad}}" onchange="actualizar_valor('{{$detalle->id}}')" onkeypress="return soloNumeros(this);">
                            </div>
                        </td>
                        <td style="vertical-align: middle;">
                            <div class="form-group">
                                <input type="text" class="form-control vtdobra" name="precio{{$detalle->id}}" id="precio{{$detalle->id}}" value="{{number_format($detalle->precio, 2, ',', ' ')}}" onchange="actualizar_valor('{{$detalle->id}}')" onkeypress="return soloNumeros(this);">
                            </div>
                        </td>
                        <td style="vertical-align: middle;">
                            <div class="form-group">@php $p_cpac = 100 - $detalle->p_oda; @endphp
                                <input type="text" class="form-control vtdobra" name="p_cpac{{$detalle->id}}" id="p_cpac{{$detalle->id}}" value="{{number_format($p_cpac, 2, ',', ' ')}}" onchange="actualizar_valor('{{$detalle->id}}')" onkeypress="return soloNumeros(this);">
                            </div>
                        </td>
                        <td style="vertical-align: middle;">
                            <div class="form-group">
                                <input type="text" class="form-control vtdobra" name="cobrar_paciente{{$detalle->id}}" id="cobrar_paciente{{$detalle->id}}" value="{{number_format($detalle->cobrar_paciente, 2, ',', ' ')}}" onchange="actualizar_p_cobro('{{$detalle->id}}')" onkeypress="return soloNumeros(this);">
                            </div>
                        </td>
                        <td style="vertical-align: middle;">
                            <div class="form-group">
                                <input type="text" class="form-control vtdobra" name="valor_deducible{{$detalle->id}}" id="valor_deducible{{$detalle->id}}" value="{{number_format($detalle->valor_deducible, 2, ',', ' ')}}" onchange="actualizar_valor('{{$detalle->id}}')" onkeypress="return soloNumeros(this);">
                            </div>
                        </td>
                        <td style="vertical-align: middle;">
                            <div class="form-group">
                                <input type="text" class="form-control vtdobra" name="p_dcto{{$detalle->id}}" id="p_dcto{{$detalle->id}}" value="{{number_format($detalle->p_dcto, 2, ',', ' ')}}" onchange="actualizar_valor('{{$detalle->id}}')" onkeypress="return soloNumeros(this);">
                            </div>
                        </td>
                        
                        <td style="vertical-align: middle;">
                            <div class="form-group">
                                <input type="text" class="form-control vtdobra" name="descuento{{$detalle->id}}" id="descuento{{$detalle->id}}" value="{{number_format($detalle->descuento, 2, ',', ' ')}}" onchange="actualizar_p_dcto('{{$detalle->id}}')" onkeypress="return soloNumeros(this);">
                            </div>
                        </td>
                        <td style="vertical-align: middle;">
                            <span id="valor_neto{{$detalle->id}}">{{number_format($detalle->total, 2, ',', ' ')}}</span>
                        </td>
                        <td style="vertical-align: middle;">
                            <input type="hidden" name="iva{{$detalle->id}}" id="iva{{$detalle->id}}" value="{{$detalle->iva}}" >
                            <span id="valor_iva{{$detalle->id}}">{{number_format($detalle->valor_iva, 2, ',', ' ')}}</span>
                        </td>
                        <td style="vertical-align: middle;">
                            <button type="button" class="btn btn-danger" onclick="eliminar_detalle('{{ $detalle->id }}')"><i class="fa fa-trash"></i></button>
                            <button type="button" class="btn btn-primary" onclick="agregar_deducible('{{ $detalle->id }}')"><span data-toggle="tooltip" title="Ingreso deducible"><i class="fa fa-plus"></i></span></button>
                        </td>   
                    </tr>
                @endforeach
                <tr>
                    <td>
                        <div class="form-group">
                            <select id="producto_nuevo" name="producto_nuevo" class="form-control select2_productos" required placeholder="{{trans('new_recibo.IngreseProducto')}}" onchange="seleccionar_producto();">     
                            </select>
                        </div>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>   
                </tr>
            </tbody>
        </table>


        <table id="example" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
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
                    <td id="subtotal_12" class="text-right px-1">{{ number_format($orden->subtotal_12, 2, ',', ' ') }}</td>
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
                    <td id="subtotal_0" class="text-right px-1">{{ number_format($orden->subtotal_0, 2, ',', ' ') }}</td>
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
                    <td id="descuento" class="text-right px-1">{{ number_format($orden->descuento, 2, ',', ' ') }}</td>
                    <input type="hidden" name="descuento1" id="descuento1" class="hidden">
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>@php $sub = $orden->subtotal_0 + $orden->subtotal_12; @endphp
                    <td colspan="2" class="text-right">{{trans('contableM.SubtotalsinImpuesto')}}</td>
                    <td id="base" class="text-right px-1">{{ number_format($sub, 2, ',', ' ') }}</td>
                    <input type="hidden" name="base1" id="base1" class="hidden">
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="2" class="text-right">{{trans('contableM.tarifaiva')}}</td>
                    <td id="tarifa_iva" class="text-right px-1">{{ number_format($orden->iva, 2, ',', ' ') }}</td>
                    <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" class="hidden">
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="2" class="text-right"><strong>{{trans('contableM.total')}}</strong></td>
                    <td id="total" class="text-right px-1">{{ number_format($orden->total, 2, ',', ' ') }}</td>
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
                    <td id="copagoTotal" class="text-right px-1">{{ number_format($orden->valor_oda, 2, ',', ' ') }}</td>
                    <input type="hidden" name="totalc" id="totalc" class="hidden">
                </tr>
            </tfoot>
        </table>
    </div>

</div>

<script src="{{ asset ("/bower_components/select2/dist/js/select2.full.js") }}"></script>


<script type="text/javascript">
    //comentarioo
   
    $('.select2_productos').select2();
   
    $('.select2_productos').select2({
        placeholder: "Seleccione un producto...",
        allowClear: true,
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
                console.log(data);
                return {
                    results: data
                };
            }
        }
    });
    
    $('.select2_agrupador').select2();
   
    $('.select2_agrupador').select2({
        placeholder: "Seleccione un producto...",
        allowClear: true,
        cache: true,
        ajax: {
            url: '{{route("proforma.mostrar_agrupador_proforma")}}',
            data: function(params) {
                var query = {
                    search: params.term,
                    type: 'public'
                }
                return query;
            },
            processResults: function(data) {
                console.log(data);
                return {
                    results: data
                };
            }
        }
    });
</script>
