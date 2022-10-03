<div class="panel panel-default">
    <div class="panel-heading">
            <div class="row">
            <label class="col-md-10">{{trans('contableM.Pedido')}}  </label>
            
            <div class="col-md-2" style="text-align: right;">
                <button type="button" class="btn btn-danger btn-gray des">
                    <i class="fa fa-remove"></i>
                </button>
            </div>
            </div>

    </div>
    <div class="panel-body">
        <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">

            <thead>
                <tr>
                    <!--<th width="10%" class="" tabindex="0">{{trans('contableM.codigo')}}</th>-->
                    <th width="35%" tabindex="0">{{trans('contableM.DescripciondelProducto')}}</th>
                    <th width="10%" tabindex="0">{{trans('contableM.cantidad')}}</th>
                    <th width="10%" tabindex="0">{{trans('contableM.precio')}}</th>
                    <th width="10%" tabindex="0">{{trans('contableM.cobrarseguro')}}</th>
                    <th width="10%" tabindex="0">% {{trans('contableM.prctdesc')}}</th>
                    <th width="10%" tabindex="0">{{trans('contableM.descuento')}}</th>
                    <th width="10%" tabindex="0">{{trans('contableM.precioneto')}}</th>
                    <th width="5%" tabindex="0">{{trans('contableM.iva')}}</th>
                    <th width="10%" tabindex="0">
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
                            @foreach($ap_agrupado as $value)
                            <option value="{{$value->cod_proceso}}" data-iva="0" data-codigo="{{$value->cod_proceso}}" data-mes="{{$value->mes_plano}}" data-precio="{{$value->valor_cobrado}}"> Codigo Proceso: {{$value->cod_proceso}} Mes Plano: {{$value->mes_plano}}</option>
                            @endforeach

                        </select>
                        <textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea>
                        <input type="hidden" name="iva[]" class="iva" />
                    </td>
                    <td>
                        <input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="cantidad[]" onchange="verificar_stock(this)" required>
                    </td>
                    <td id="tprecio">
                        <select name="precio[]" class="form-control select2_precio pneto" style="width:60%;height:20px;display:inline;" required>
                            <option value="0"></option>


                        </select>
                        <button type="button" class="btn btn-info btn-gray btn-xs cp">
                            <i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>
                        </button>
                    </td>
                    <td>
                        <input class="form-control text-right copago" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="copago[]" readonly required>
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
                            @foreach($ap_agrupado as $value)
                            <option value="{{$value->cod_proceso}}" @if(($value->base_iva)>0) data-iva="1" @else data-iva="0" @endif data-codigo="{{$value->cod_proceso}}" data-mes="{{$value->mes_plano}}" data-precio="{{$value->valor_cobrado}}" > Codigo Proceso: {{$value->cod_proceso}} Mes Plano: {{$value->mes_plano}}</option>
                            @endforeach

                        </select>
                        <textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea>
                        <input type="hidden" name="iva[]" class="iva" />
                    </td>
                    <td>
                        <input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="cantidad[]" onchange="verificar_stock(this)" required>

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
                        <input class="form-control text-right copago" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="copago[]" readonly required>
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
                        <input class="form" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]" disabled>

                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-gray delete">
                            <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>


</div>