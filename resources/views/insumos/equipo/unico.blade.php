
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
        <div style="text-align: center; padding-top: 30px"> <img style="width: 70%;" src="data:image/png;base64, {{ DNS1D::getBarcodePNG("$data->serie", "C128",2,30)}}" alt="barcode"/></div>
        <div style="text-align: center;" class="no">{{ $data->nombre }} ({{ $data->marca }}) <br>
        <b>{{trans('winsumos.serie')}}:</b>{{$data->serie}}
        </div>
  </body>
</html>
