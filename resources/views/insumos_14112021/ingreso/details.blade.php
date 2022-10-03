@php 
    $pd= DB::table('pedido')->where('pedido',$numero_pedido)->first();
    $comprobar= DB::table('detalle_pedido')->where('id_pedido',$pd->id)->first();
@endphp

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="row">
            <label class="col-md-10">Pedido # {{$numero_pedido}} Proveedor: {{$proveedor->razonsocial}} </label>
            <input type="hidden" name="invoice[]" value="{{$numero_pedido}}">
            <div class="col-md-2" style="text-align: right;">
                <button type="button" class="btn btn-danger des">
                    <i class="fa fa-remove"></i>
                </button>
            </div>

        </div>
    </div>
    @if(is_null($comprobar))
    <div class="panel-body" style="padding:0;">
        <div class="col-md-12 table-responsive " style="padding:0 !important;">
            <table class="table table-bordered table-hover dataTable noacti"  role="grid" aria-describedby="example2_info" style="margin-top:0 !important; width: 100%!important;">
                <thead>
                    <tr>
                        <th tabindex="0">Descripci&oacute;n del Producto</th>
                        <th tabindex="0">Cantidad</th>
                        <th tabindex="0">Serie</th>
                        <th tabindex="0">Bodega</th>
                        <th tabindex="0">Lote</th>
                        <th tabindex="0">Registro Sanitario</th>
                        <th tabindex="0">Fecha Vencimiento</th>
                        <th tabindex="0">Precio</th>
                        <th tabindex="0">% Desc</th>
                        <th tabindex="0">Descuento</th>
                        <th tabindex="0">Precio Neto</th>
                        <th tabindex="0">IVA</th>
                        <th tabindex="0">Accion</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $contador=0;
                    @endphp
                    @foreach($productos as $x)
                    @php
                        $dateYear= date('Y');
                        $dateMonth= date('m');
                        $dateMilisecon= date('i');
                        $dateSeconds= date('s');
                        $dateHours= date('h');
                        $dateDay= date('d');
                        $serie= $dateYear.$dateMonth.$dateDay.$dateHours.$dateMilisecon.$dateSeconds.$dateSeconds.$contador;
                        $contador++;
                        //$proveedor= DB::table('proveedor')->find($x->id_proveedor);
                        $precio_neto= $x->cantidad * $x->precio;
                    @endphp
                    <tr>
                        <td> <input type="text" style="width: 80%;height:20px;" class="form-control" name="nombre[]" value="{{$x->nombre}}"> 
                            <input type="hidden" name="id[]" value="{{$x->id_producto}}">
                           <!--  <input type="hidden" name="pedido[]" value="{{$x->id}}"> -->
                        </td>
                        <td> <input type="text" style="width: 80%;height:20px; text-align:right;" class="form-control cneto" name="cantidad[]" readonly required value="{{$x->cantidad}}"> </td>
                        <td> <input type="text" style="width: 80%;height:20px;" class="form-control" name="serie[]" value="{{$x->serie}}" readonly> </td>
                        <td> <select class="form-control" style="width: 80%;height:20px;" name="bodega[]" required> @foreach($bodegas as $value) <option @if($value->id==$x->id_bodega) selected='selected' @endif value='{{$value->id}}'>{{$value->nombre}}</option> @endforeach </select> </td>
                        <td> <input type="text" style="width: 80%;height:20px;" class="form-control" name="lote[]" value="{{$x->lote}}"> </td>
                        <td> <input type="text" style="width: 80%;height:20px;" class="form-control" name="registro_sanitario[]" value=""> </td>
                        <td> <input type="date" style="width: 80%;height:20px;" name="fecha_vencimiento[]" class="form-control" value="{{$x->fecha_vencimiento}}"> </td>
                        <td> <input type="text" style="width: 80%;height:20px; text-align:right;" class="form-control pneto" onkeypress="return isNumberKey(event)" style="width:40%;display:inline;height:20px;" name="precio[]" value="{{number_format($x->precio,2)}}"> </td>
                        <td>
                            <input class="form-control text-right pdesc" type="text" style="width: 80%;height:20px; text-align:right;" name="pDescuento[]" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" @if($x->descuentop!=null) value="{{$x->descuentop}}" @else value="0.00" @endif required>
                            <input class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px; text-align:right;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" required>
                        </td>
                        <td>
                            <input class="form-control text-right desc" type="text" style="width: 80%;height:20px; text-align:right;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" required>
                        </td>
                        <td>

                            <input class="form-control px-1 text-right"  type="text" style="height:20px; text-align:right;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="{{number_format($precio_neto,2)}}" required>
                        </td>
                        <td>
                            <input class="form" type="checkbox" name="valor_iva[]" @if($x->iva==1) checked="checked" @endif value="1">
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger  delete">
                                <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                            </button>
                        </td>
                    </tr>

                    @endforeach
                </tbody>
            </table>
            
        </div>
    </div>
    @endif
</div>
