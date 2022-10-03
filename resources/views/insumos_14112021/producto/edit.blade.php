@extends('insumos.producto.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<section class="content">
    <div class="box " style="background-color: white;">
        <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';border-bottom: #3c8dbc; ">
            <div class="col-md-9">
                <h3 class="box-title">Editar Producto</h3>
            </div>
            <div class="col-md-3" style="text-align: right;">
                <button onclick="goBack()" class="btn btn-danger" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;">
                   <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <div class="box-body" style="background-color: #ffffff;">
            <form class="form-vertical" role="form" method="POST" action="{{ route('producto.update', ['id' => $producto->id]) }}">
                <div class="box-body col-xs-24">
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="{{ $producto->id }}">
                    <!--Codigo-->
                    <div class="form-group col-xs-6{{ $errors->has('codigo') ? ' has-error' : '' }}">
                        <label for="codigo" class="col-md-4 control-label">Codigo</label>
                        <div class="col-md-7">
                        <input id="codigo" type="text" class="form-control" name="codigo" value="{{ $producto->codigo }}" required autofocus>
                            @if ($errors->has('codigo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('codigo') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--Codigo IESS-->
                    <div class="form-group col-xs-6{{ $errors->has('codigo_iess') ? ' has-error' : '' }}">
                        <label for="codigo_iess" class="col-md-4 control-label">Codigo IESS</label>
                        <div class="col-md-7">
                        <input id="codigo_iess" type="text" class="form-control" name="codigo_iess" value="{{ $producto->codigo_iess }}"  autofocus>
                            @if ($errors->has('codigo_iess'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('codigo_iess') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--Nombre-->
                    <div class="form-group col-xs-6{{ $errors->has('nombre') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-4 control-label">Nombre</label>
                        <div class="col-md-7">
                            <input id="nombre" type="text" class="form-control" name="nombre" value="{{ $producto->nombre }}" required autofocus>
                            @if ($errors->has('nombre'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--Descripcion-->
                    <div class="form-group col-xs-6{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                        <label for="descripcion" class="col-md-4 control-label">Descripcion</label>
                        <div class="col-md-7">
                        <input id="descripcion" type="text" class="form-control" name="descripcion" value="{{ $producto->descripcion }}" required autofocus>
                            @if ($errors->has('descripcion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('descripcion') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--Cantidad de medida-->
                    <div class="form-group col-xs-6{{ $errors->has('cantidad_unidad') ? ' has-error' : '' }}">
                        <label for="cantidad_unidad" class="col-md-4 control-label">Cantidad de medida:</label>
                        <div class="col-md-7">
                            <input id="cantidad_unidad" type="text" class="form-control" name="cantidad_unidad" value="{{ $producto->cantidad_unidad }}" required autofocus>
                            @if ($errors->has('cantidad_unidad'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('cantidad_unidad') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--Medida-->
                    <div class="form-group col-xs-6{{ $errors->has('medida') ? ' has-error' : '' }}">
                        <label for="medida" class="col-md-4 control-label">Medida</label>
                        <div class="col-md-7">
                            <select id="medida" name="medida" class="form-control" required="required">
                                <option {{ $producto->medida == 'Uni' ? 'selected' : ''}} value="Uni">Unidad</option>
                                <option {{ $producto->medida == 'Kg' ? 'selected' : ''}} value="Kg">Kilogramos</option>
                                <option {{ $producto->medida == 'G' ? 'selected' : ''}} value="G">Gramos</option>
                                <option {{ $producto->medida == 'Mg' ? 'selected' : ''}} value="Mg">Miligramos</option>
                                <option {{ $producto->medida == 'Ml' ? 'selected' : ''}} value="Ml">Mililitros</option>
                                <option {{ $producto->medida == 'L' ? 'selected' : ''}}  value="L">Litros</option>
                                <option {{ $producto->medida == 'Lb' ? 'selected' : ''}} value="Lb">Libras</option>
                                <option {{ $producto->medida == 'm' ? 'selected' : ''}}  value="m">Metros</option>
                                <option {{ $producto->medida == 'cm' ? 'selected' : ''}}  value="cm">Centimetros</option>
                                <option {{ $producto->medida == 'mm' ? 'selected' : ''}} value="mm">Milimetros</option>
                            </select>
                            @if ($errors->has('medida'))
                            <span class="help-block">
                                <strong>{{ $errors->first('medida') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!--Forma de Despacho-->
                    <div class="form-group col-xs-6{{ $errors->has('despacho') ? ' has-error' : '' }}">
                        <label for="despacho" class="col-md-4 control-label">Forma de Despacho</label>
                        <div class="col-md-7">
                            <select id="despacho" name="despacho" class="form-control" required="required">
                                <option {{ $producto->despacho == 1 ? 'selected' : ''}}  value="1">codigo de serie</option>
                                <option {{ $producto->despacho == 0 ? 'selected' : ''}} value="0">codigo de producto</option>
                            </select>
                            @if ($errors->has('despacho'))
                            <span class="help-block">
                                <strong>{{ $errors->first('despacho') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!--Stock Minimo-->
                    <div class="form-group col-xs-6{{ $errors->has('minimo') ? ' has-error' : '' }}">
                        <label for="minimo" class="col-md-4 control-label">Stock Minimo</label>
                        <div class="col-md-7">
                        <input id="minimo" onchange="return validarRango(this);" type="number" class="form-control" name="minimo" value="{{ $producto->minimo }}" required>
                            @if ($errors->has('minimo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('minimo') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--Registro Sanitario-->
                    <div class="form-group col-xs-6{{ $errors->has('registro_sanitario') ? ' has-error' : '' }}">
                        <label for="registro_sanitario" class="col-md-4 control-label">Registro Sanitario</label>
                        <div class="col-md-7">
                            <input id="registro_sanitario" type="registro_sanitario" class="form-control" name="registro_sanitario" value="{{ $producto->registro_sanitario }}" required>
                            @if ($errors->has('registro_sanitario'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('registro_sanitario') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group col-xs-6{{ $errors->has('id_marca') ? ' has-error' : '' }}">
                        <label for="id_marca" class="col-md-4 control-label">Marcas</label>
                        <div class="col-md-7">
                            <select id="id_marca" name="id_marca" class="form-control" required="required">
                                <option value="">Seleccione..</option>
                                @foreach($marcas as $marca)
                                    <option @if($marca->id==$producto->marca->id) selected @endif value="{{$marca->id}}"> {{$marca->nombre}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('id_marca'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_marca') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--Tipo de Producto-->
                    <div class="form-group col-xs-6{{ $errors->has('id_tipo') ? ' has-error' : '' }}">
                        <label for="id_tipo" class="col-md-4 control-label">Tipo de Producto</label>
                        <div class="col-md-7">
                            <select id="id_tipo" name="id_tipo" class="form-control" required="required">
                                <option value="">Seleccione..</option>
                                @foreach($tipos as $tipo)
                                    <option @if($tipo->id== $producto->tipo_producto ) selected @endif value="{{ $tipo->id }}">{{ $tipo->nombre}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('id_tipo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_tipo') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--Cantidad de Usos-->
                    <div class="form-group col-xs-6{{ $errors->has('uso') ? ' has-error' : '' }}">
                        <label for="uso" class="col-md-4 control-label">Cantidad de Usos</label>
                        <div class="col-md-7">
                            <input id="uso" onchange="return validarRango_uso(this);" type="number" class="form-control" name="uso" value="{{ $producto->usos }}" required autofocus>
                            @if ($errors->has('uso'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('uso') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!--Forma de Despacho-->
                    <div class="form-group col-xs-6{{ $errors->has('iva') ? ' has-error' : '' }}">
                        <label for="iva" class="col-md-4 control-label">Posee Iva</label>
                        <div class="col-md-7">
                            <select id="iva" name="iva" class="form-control" required="required">
                                <option {{ $producto->iva == 1 ? 'selected' : ''}} value="1">Si</option>
                                <option {{ $producto->iva == 0 ? 'selected' : ''}} value="0">No</option>
                            </select>
                            @if ($errors->has('iva'))
                            <span class="help-block">
                                <strong>{{ $errors->first('iva') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <!--estado-->
                    <div class="form-group col-xs-6{{ $errors->has('estado') ? ' has-error' : '' }}">
                        <label for="estado" class="col-md-4 control-label">Estado</label>
                        <div class="col-md-7">
                            <select id="estado" name="estado" class="form-control" required="required">
                                <option {{ $producto->estado == 1 ? 'selected' : ''}} value="1">Activo</option>
                                <option {{ $producto->estado == 0 ? 'selected' : ''}} value="0">Inactivo</option>
                            </select>
                            @if ($errors->has('estado'))
                            <span class="help-block">
                                <strong>{{ $errors->first('estado') }}</strong>
                            </span>
                            @endif
                        </div> 
                    </div>

                    <div class="form-group col-xs-6">
                        <label class="col-md-4 control-label" >Usar mismo codigo</label>
                        <input class="col-md-7" id="codigo_siempre" name="codigo_siempre" type="checkbox" value="1" class="flat-blue"  style="position: absolute; opacity: 0;" {{ $producto->codigo_siempre == '1' ? 'checked' : ''}}> 
                    </div>
                    <div class="form-group col-xs-6{{ $errors->has('cod_general') ? ' has-error' : '' }}">
                        <label for="cod_general" class="col-md-4 control-label" >Codigo General</label>
                        <div class="col-md-7">
                            <select class="form-control select2" name="cod_general" id="cod_general" style="width: 100%;" >
                                <option  value="">Seleccione...</option>
                                @foreach($ct_productos as $value)
                                    @php $equipos= DB::table('ct_productos_insumos')->where('id_insumo',$producto->id)->where('id_producto',$value->id)->first(); @endphp
                                  <option @if(!is_null($equipos)) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="clearfix">
                        <span></span>
                    </div>
                    <br><br>
                    <!--Precios-->
                    
                    <!--Button Actualizar-->
                    <div class="form-group col-xs-10" style="text-align: center;">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                            Actualizar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="{{ asset ("/js/icheck.js") }}"></script>
  <script>
    $(document).ready(function(){
      $('#codigo_siempre').iCheck({
        checkboxClass: 'icheckbox_flat-blue',
        increaseArea: '20%' // optional
      });
    });
  </script>

<script type="text/javascript">
var fila = '<td><input class="form-control input-sm" type="text" style="width: 80%;height:20px;" placeholder="#" name="nivel[]" required>'+
            '</td><td><input class="form-control input-sm" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" placeholder="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precio[]" required>'+
            '</td><td><button type="button" class="btn btn-danger btn-gray delete"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i>'+
            '</button></td>';
       function validarRango(elemento){
        var numero = parseInt(elemento.value,10);
          //Validamos que se cumpla el rango
        if(numero<1 ){
            alert("La cantidad debe ser mayor o igual a 1");
            $('#minimo').val("1");
            return false;
        }
        return true;
    }


       function validarRango_uso(elemento){
        var numero = parseInt(elemento.value,10);
          //Validamos que se cumpla el rango
        if(numero<1 ){
            alert("La cantidad debe ser mayor o igual a 1");
            $('#uso').val("1");
            return false;
        }
        return true;
    }

    function goBack() {
      window.history.back();
    }
    $('.select2').select2({
            tags: false
        });

    $('body').on('click', '.delete', function () {
        $(this).parent().parent().remove();
    });
    function nuevo(){
        var nuevafila = fila;
        var rowk = document.getElementById("agregar_cuentas").insertRow(-1);
        rowk.innerHTML = fila;
        //rowk.className="well";
     

    }
</script>

</section>
@endsection
