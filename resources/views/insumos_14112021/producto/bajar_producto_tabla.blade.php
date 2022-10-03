
@if($productos == '[]')
<label >No se encontro el producto</label>
@else
<div class="table-responsive col-md-12">
    <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
      <thead>
        <tr >
          <th >Codigo</th>
          <th >Nombre</th>
          <th >Descripcion</th>
          <th >Cantidad</th>
          <th >Bodega/Transito</th>
          <th >Acción</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($productos as $value)
          <tr>
            <td >{{ $value->codigo }}</td>
            <td >{{ $value->nombre_producto }}</td>
            <td >{{ $value->descripcion }}</td>
            <td >{{ $value->cantidad_total }} </td>
            <td >@if($value->tipo == '1') Bodega @endif @if($value->tipo == '2') Transito @endif</td>
            <td>
              <a href="{{ route('producto.descontar', ['cant' => $value->cantidad_total, 'tipo' => $value->tipo, 'id_producto' => $value->id_producto, 'serie' => $value->serie, 'id_bodega' => $value->id_bodega, 'id_pedido' => $value->id_pedido, 'f_vencimiento' => $value->fecha_vencimiento, 'lote' => $value->lote])}}" class="btn btn-warning col-md-6 col-xs-6 btn-margin" data-toggle="modal" data-target="#foto">
              Descontar
              </a>
              <!--data-toggle="modal" data-target="#foto"-->
            </td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
      </tfoot>
    </table>
  </div>
@endif