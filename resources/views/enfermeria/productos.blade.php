  <div class="box">
    <div class="box-body">
        <div class="box-header">
          <b>{{trans('eenfermeria.PRODUCTOS')}}</b>
        </div>
        <div class="box-body">
          @foreach($productos as $producto)
          <a class="btn btn-primary" onClick="enviar('{{$producto->codigo}}');">{{$producto->nombre}}</a>
          @endforeach
        </div>
    </div>
  </div>
 

