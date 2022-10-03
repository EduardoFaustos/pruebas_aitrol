<table id="examples2" class="display compact responsive"  role="grid" aria-describedby="example2_info" style="margin-top:20px !important; width: 100%!important;">

<thead>
@php 
$ids_nuevo = "";
$acumt = 0;  
$cabeceras = array(1=>'HONORARIOS MEDICOS', 2=>'MEDICINAS VALOR AL ORIGEN', 3=>'INSUMOS VALOR AL ORIGEN', 4=>'IMAGEN (*)', 5=>'SERVICIOS INSTITUCIONALES',  0=>'OTROS'); 
$acum_honorarios = 0;

@endphp
    <tr>
        <th><b>SERIE</b></th>
        <th><b>NOMBRE DEL INSUMO</b></th>
        <th><b>DESCRIPCION DEL INSUMO</b></th>
        <th><b>FECHA</b></th>
        <th><b>COSTO </b> </th>
    </tr>
</thead>

<tbody>
@foreach($insumos as $value)

@php
if(Auth::user()->id == "0957258056"){

}
    $invcosto = null;
    $costo_ed = 0;
    $px= \Sis_medico\InvInventarioSerie::where('serie',$value->serie)->first();
if (is_null($px)) {
    $px = \Sis_medico\Movimiento::where('serie',$value->serie)->first();
}
if (!is_null($px)) {
    $invcosto = Sis_medico\InvKardex::where('inv_kardex.id_producto', $px->id_producto)->leftJoin('inv_det_movimientos', 'inv_kardex.id_inv_det_movimientos', '=', 'inv_det_movimientos.id')->where('inv_det_movimientos.id_procedimiento', $protocolo->id)
        ->select('inv_kardex.*')
        ->first();
    if(!is_null($invcosto)){
        $costo_ed = $invcosto->cantidad* $invcosto->valor_unitario;
    }
}
    $product=null;
    $x=[];
    $last=[];
if($px!=null){

    $product= \Sis_medico\Ct_productos_insumos::where('id_insumo',$px->id_producto)->first();

}
$noligado = "";
$nombre_producto = "";

if(!is_null($value)){
    $nombre_producto = $value->nombre;
}

if (is_null($product)){
    $noligado = " | AUN NO ESTA LIGADO";
}else {
    $noligado = "";
    $producto_contable = Sis_medico\Ct_productos::find($product->id_producto);

    if(!is_null($producto_contable)){
        $nombre_producto = $producto_contable->nombre;
        $ids_nuevo = " {$producto_contable->id}, {$ids_nuevo}";
    }
}
@endphp
<tr>
    <td><span>@if(!is_null($value)){{$value->serie}} {{$noligado}} @endif</span></td>
    <td><span>{{ $nombre_producto }}</span></td>
    <td><span>@if(!is_null($value)){{$value->descripcion}}@endif</span></td>
    <td><span>@if(!is_null($value)){{$value->updated_at}}@endif</span></td>
    <td> {{$costo_ed}} </td>
</tr>
@endforeach

</tbody>

</table>

<div style="display:none;">
    
</div>