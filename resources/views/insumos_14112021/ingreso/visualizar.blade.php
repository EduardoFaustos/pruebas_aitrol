@extends('insumos.ingreso.base')
@section('action-content')
<style type="text/css">
  .ui-corner-all
        {
            -moz-border-radius: 4px 4px 4px 4px;
        }

        .ui-widget
        {
            font-family: Verdana,Arial,sans-serif;
            font-size: 15px;
        }
        .ui-menu
        {
            display: block;
            float: left;
            list-style: none outside none;
            margin: 0;
            padding: 2px;
        }
        .ui-autocomplete
        {
             overflow-x: hidden;
              max-height: 200px;
              width:1px;
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
        .ui-menu .ui-menu-item
        {
            clear: left;
            float: left;
            margin: 0;
            padding: 0;
            width: 100%;
        }
        .ui-menu .ui-menu-item a
        {
            display: block;
            padding: 3px 3px 3px 3px;
            text-decoration: none;
            cursor: pointer;
            background-color: #ffffff;
        }
        .ui-menu .ui-menu-item a:hover
        {
            display: block;
            padding: 3px 3px 3px 3px;
            text-decoration: none;
            color: White;
            cursor: pointer;
            background-color: #006699;
        }
        .ui-widget-content a
        {
            color: #222222;
        }
</style>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
          <div class="col-sm-4">
            <h3 class="box-title">VISUALIZAR PEDIDO</h3>
          </div>
          <div class="col-md-8" style="text-align: right;">
            <a type="button" href="{{URL::previous()}}" class="btn btn-primary btn-sm">
              <span class="glyphicon glyphicon-arrow-left"> Regresar</span>
            </a>
          </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <div class="box">
          <div class="box-header">
            <div class="row">
                <!-- Fecha -->
                <div class="form-group col-md-6">
                    <label for="fecha" class="col-md-4 control-label">Fecha Pedido</label>
                    <div class="col-md-8">
                        <span>{{$pedido->fecha}}</span>
                    </div>
                </div>
                <!-- Numero de Pedido -->
                <div class="form-group cl_pedido col-md-6 {{ $errors->has('pedido') ? ' has-error' : '' }}">
                    <label for="pedido" class="col-md-4 control-label">Número de pedido</label>
                    <div class="col-md-8">
                      <span>{{$pedido->pedido}}</span>
                    </div>
                    <span class="help-block">
                      <strong id="str_pedido"></strong>
                    </span>
                </div>

                <div class="form-group cl_pedido col-md-6 {{ $errors->has('num_factura') ? ' has-error' : '' }}">
                    <label for="num_factura" class="col-md-4 control-label">Número de factura</label>
                    <div class="col-md-8">
                      <span>{{$pedido->factura}}</span>
                    </div>
                </div>

                <!-- Vencimiento -->
                <div class="form-group col-md-6{{ $errors->has('vencimiento') ? ' has-error' : '' }}">
                    <label for="vencimiento" class="col-md-4 control-label">Fecha de Vencimiento</label>
                    <div class="col-md-8">
                      <span>{{$pedido->vencimiento}}</span>
                    </div>
                </div>
                <!-- Proveedor -->
                <div class="form-group col-md-6{{ $errors->has('id_proveedor') ? ' has-error' : '' }}">
                    <label for="id_proveedor" class="col-md-4 control-label">Proveedor</label>
                    <div class="col-md-8">
                      <span>{{$pedido->proveedor->nombrecomercial}}</span>
                    </div>
                </div>
                <!-- MULTIPLE EMPRESA-->
                <div class="form-group col-md-6{{ $errors->has('id_empresa') ? ' has-error' : '' }}">
                    <label for="id_empresa" class="col-md-4 control-label">Empresa</label>
                    <div class="col-md-8">
                      <span>{{$pedido->empresa->nombrecomercial}}</span>
                    </div>
                </div>


                <!-- MULTIPLE EMPRESA-->
                <!--div class="form-group col-md-6{{ $errors->has('consecion') ? ' has-error' : '' }}">
                    <label for="consecion" class="col-md-4 control-label">Posee Consecion</label>
                    <div class="col-md-8">
                      <span>@if(($pedido->consecion == 0) || (is_null($pedido->consecion))) No @else Si @endif</span>
                    </div>
                </div-->

                <!-- Observaciones -->
                <div class="form-group  col-md-12 {{ $errors->has('observaciones') ? ' has-error' : '' }}">
                    <label for="observaciones" class="col-md-2 control-label">Observaciones</label>
                    <div class="col-md-10">
                      <span>{{$pedido->observaciones}}</span>
                    </div>
                </div>
            </div>
          </div>
          <div class="general form-group ">
            <div class="col-md-12">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                  <thead>
                    <tr role="row">
                       <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Código</th>
                      <th width="25%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Nombre: activate to sort column descending" aria-sort="ascending">Nombre</th>
                      <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Cantidad: activate to sort column descending" aria-sort="sorting">Cantidad</th>
                      <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Serie: activate to sort column descending" aria-sort="sorting">Serie</th>
                      <!--th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Precio de compra</th-->
                      <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Bodega: activate to sort column ascending">Bodega</th>
                      <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Serie: activate to sort column descending" aria-sort="sorting">Lote</th>
                      <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Serie: activate to sort column descending" aria-sort="sorting">Registro Sanitario</th>
                      <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Fecha de Vecimiento: activate to sort column ascending">Fecha de Vecimiento</th>
                      <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Fecha de Vecimiento: activate to sort column ascending">Precio Unitario</th>
                      <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Fecha de Vecimiento: activate to sort column ascending">Precio Final</th>
                      <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Consecion: activate to sort column ascending">Consecion</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($detalle as $value)
                      <tr>
                        <td>{{$value->codigo}}</td>
                        <td>{{$value->nombre_producto}}</td>
                        <td>{{$value->cantidad}}</td>
                        <td>{{$value->serie}}</td>
                        <td>{{$value->nombre_bodega}}</td>
                        <td>{{$value->lote}}</td>
                        <td>{{$value->registro_sanitario}}</td>
                        <td>{{$value->fecha_vencimiento}}</td>
                        <td>{{$value->precio}}</td>
                        <td>{{round(($value->precio * $value->cantidad),2)}}</td>
                        <td>@if($value->consecion_det == '1') Si @else No @endif</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
            </div>
            <span class="help-block">
              <strong id="lote_errores"></strong>
            </span>
          </div>
      </div>
    </div>
  </div>
</section>
<!-- /.content -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
  <script src="{{ asset ("/js/icheck.js") }}"></script>
  <script>
    $(document).ready(function(){
      $('#consecion').iCheck({
        checkboxClass: 'icheckbox_flat-blue',
        increaseArea: '20%' // optional
      });
    });
  </script>
<script type="text/javascript">

  function valida(e){
      tecla = (document.all) ? e.keyCode : e.which;

      //Tecla de retroceso para borrar, siempre la permite
      if (tecla==8){
          return true;
      }

      // Patron de entrada, en este caso solo acepta numeros
      patron =/[0-9]/;
      tecla_final = String.fromCharCode(tecla);
      return patron.test(tecla_final);
  }

</script>
@endsection
