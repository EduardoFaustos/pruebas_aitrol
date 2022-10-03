
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{trans('winsumos.codigo_barra')}}</title>
    {!! Html::style('assets/css/pdf.css') !!}
  </head>
  <body>
  <style type="text/css">
    *{
      margin: 0
      ;
      padding: 0;
    }
  </style>

    <main>
      @php
        $ultimo = end($data);
        $ultimo_id = $ultimo->id;
        $ultimo_id_pedido = $ultimo->id_pedido;
      @endphp
      @foreach($data as $value)
        @php
          $cantidad = $value->cantidad;
          if($cantidad > 20){
            $cantidad = 20;
          }
          
          for($i=0; $i < $cantidad; $i++){
        @endphp
          <div style="text-align: center; padding-top: 40px"> <img style="width: 80%;" src="data:image/png;base64, {{ DNS1D::getBarcodePNG("$value->serie", "C128",2,30)}}" alt="barcode"/></div>
          <div style="text-align: center;" class="no">{{ $value->serie }}
          <br>P: {{$numero}}
          <br> {{$value->nombreproducto}}
          </div>
          @php
            $elemento_final = $i - 1;
          @endphp
          @if(($ultimo_id !=  $value->id) && ($elemento_final > $cantidad ))
          <div style="page-break-after:always;"></div>
          @endif
        @php
          }
        @endphp
      @endforeach
  </body>
</html>
