@extends('insumos.producto.base')
@section('action-content')

<script type="text/javascript">
    function check(e){
        tecla = (document.all) ? e.keyCode : e.which;

        //Tecla de retroceso para borrar, siempre la permite
        if (tecla == 8) {
            return true;
        }

        // Patron de entrada, en este caso solo acepta numeros y letras
        patron = /[A-Za-z0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    }

    function goBack() {
      window.history.back();
    }

</script>
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<section class="content">
    <div class="box " style="background-color: white;">
        <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';border-bottom: #3c8dbc; ">
            <div class="col-md-9">
              <h3 class="box-title">{{trans('winsumos.agregar_producto')}}</h3>
            </div>
            <div class="col-md-3" style="text-align: right;">
                <button onclick="goBack()" class="btn btn-danger" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;">
                   <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('winsumos.regresar')}}
                </button>
            </div>
        </div>
        <div class="box-body" style="background-color: #ffffff;">
            <form class="form-vertical" enctype="multipart/form-data" role="form" method="POST" action="{{ route('producto.store') }}">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">
                    <!--Codigo-->
                    <div class="form-group col-xs-6{{ $errors->has('codigo') ? ' has-error' : '' }}">
                        <label for="codigo" class="col-md-4 control-label">{{trans('winsumos.codigo')}}</label>
                        <div class="col-md-7">
                            <input id="codigo" type="text" class="form-control" name="codigo" value="{{ old('codigo') }}" style="text-transform:uppercase;"  maxlength="25"  required autofocus >
                            @if ($errors->has('codigo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('codigo') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--Codigo IESS-->
                    <div class="form-group col-xs-6{{ $errors->has('codigo_iess') ? ' has-error' : '' }}">
                        <label for="codigo_iess" class="col-md-4 control-label">{{trans('winsumos.codigo')}} {{trans('winsumos.iess')}}</label>
                        <div class="col-md-7">
                            <input id="codigo_iess" type="text" class="form-control" onkeypress="return check(event)" name="codigo_iess" value="{{ old('codigo_iess') }}" style="text-transform:uppercase;"  maxlength="25"   autofocus >
                            @if ($errors->has('codigo_iess'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('codigo_iess') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--Nombre-->
                    <div class="form-group col-xs-6{{ $errors->has('nombre') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-4 control-label">{{trans('winsumos.nombre')}}</label>
                        <div class="col-md-7">
                            <input id="nombre" type="text" class="form-control" name="nombre" value="{{ old('nombre') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                            @if ($errors->has('nombre'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nombre') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!--Descripcion-->
                    <div class="form-group col-xs-6{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                        <label for="descripcion" class="col-md-4 control-label">{{trans('winsumos.descripcion')}}</label>
                        <div class="col-md-7">
                            <input id="descripcion" type="text" class="form-control" name="descripcion" value="{{ old('descripcion') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                            @if ($errors->has('descripcion'))
                            <span class="help-block">
                                <strong>{{ $errors->first('descripcion') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!--Cantidad de medida-->
                    <div class="form-group col-xs-6{{ $errors->has('cantidad_unidad') ? ' has-error' : '' }}">
                        <label for="cantidad_unidad" class="col-md-4 control-label">{{trans('winsumos.cantidad')}}</label>
                        <div class="col-md-7">
                            <input id="cantidad_unidad" type="number" class="form-control" name="cantidad_unidad" value="{{ old('cantidad_unidad') }}" style="text-transform:uppercase;"  required autofocus>
                            @if ($errors->has('cantidad_unidad'))
                            <span class="help-block">
                                <strong>{{ $errors->first('cantidad_unidad') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!--Medida-->
                    <div class="form-group col-xs-6{{ $errors->has('medida') ? ' has-error' : '' }}">
                        <label for="medida" class="col-md-4 control-label">{{trans('winsumos.medida')}}</label>
                        <div class="col-md-7">
                            <select id="medida" name="medida" class="form-control" required="required">
                                <option value="">{{trans('winsumos.seleccione')}}</option>
                                <option @if(old('medida') == 'Uni') selected @endif value="Uni">{{trans('winsumos.unidad')}}</option>
                                <option @if(old('medida') == 'Kg') selected @endif value="Kg">{{trans('winsumos.kilogramo')}}</option>
                                <option @if(old('medida') == 'G') selected @endif value="G">{{trans('winsumos.gramos')}}</option>
                                <option @if(old('medida') == 'Mg') selected @endif value="Mg">{{trans('winsumos.miligramos')}}</option>
                                <option @if(old('medida') == 'Ml') selected @endif value="Ml">{{trans('winsumos.mililitros')}}</option>
                                <option @if(old('medida') == 'L') selected @endif value="L">{{trans('winsumos.litros')}}</option>
                                <option @if(old('medida') == 'Lb') selected @endif value="Lb">{{trans('winsumos.libras')}}</option>
                                <option @if(old('medida') == 'm') selected @endif value="m">{{trans('winsumos.metros')}}</option>
                                <option @if(old('medida') == 'cm') selected @endif value="cm">{{trans('winsumos.centimetros')}}</option>
                                <option @if(old('medida') == 'mm') selected @endif value="mm">{{trans('winsumos.milimetros')}}</option>
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
                        <label for="despacho" class="col-md-4 control-label">{{trans('winsumos.forma_despacho')}}</label>
                        <div class="col-md-7">
                            <select id="despacho" name="despacho" class="form-control" required="required">
                                <option value="">{{trans('winsumos.seleccione')}}</option>
                                <option @if(old('despacho') == 1) selected @endif value="1">{{trans('winsumos.codigo_serie')}}</option>
                                <option @if(old('despacho') == 0) selected @endif value="0">{{trans('winsumos.codigo_producto')}}</option>
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
                        <label for="minimo" class="col-md-4 control-label" >{{trans('winsumos.stock_minimo')}}</label>
                        <div class="col-md-7">
                            <input id="minimo" type="text" class="form-control" name="minimo" value="{{ old('minimo') }}" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" required autofocus>
                            @if ($errors->has('minimo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('minimo') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--Registro Sanitario-->
                    <div class="form-group col-xs-6{{ $errors->has('registro_sanitario') ? ' has-error' : '' }}">
                        <label for="registro_sanitario" class="col-md-4 control-label">{{trans('winsumos.registro_sanitario')}}</label>
                        <div class="col-md-7">
                            <input id="registro_sanitario"  type="registro_sanitario" class="form-control" name="registro_sanitario" value="{{ old('registro_sanitario') }}" required>
                            @if ($errors->has('registro_sanitario'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('registro_sanitario') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--precio_venta
                    <div class="form-group col-xs-6{{ $errors->has('precio_venta') ? ' has-error' : '' }}">
                        <label for="precio_venta" class="col-md-4 control-label">Precio de Venta</label>

                        <div class="col-md-7">
                            <input id="precio_venta" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"  type="text" class="form-control" name="precio_venta" value="{{ old('precio_venta') }}" required autofocus>

                            @if ($errors->has('precio_venta'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('precio_venta') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>-->
                    <!--Proveedor-->
                    <!--<div class="form-group col-xs-6{{ $errors->has('id_proveedor') ? ' has-error' : '' }}">
                        <label for="id_proveedor" class="col-md-4 control-label">Proveedor</label>
                        <div class="col-md-7">
                            <select id="id_proveedor" name="id_proveedor" class="form-control" required="required">
                                <option value="">Seleccione..</option>
                                @foreach($proveedores as $value)
                                    @if ($value->estado != 0)
                                        <option value="{{$value->id}}"> {{$value->nombrecomercial}}</option>
                                    @endif
                                @endforeach
                            </select>
                            @if ($errors->has('id_proveedor'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_proveedor') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>-->
                    <!--Marcas-->
                    <div class="form-group col-xs-6{{ $errors->has('id_marca') ? ' has-error' : '' }}">
                        <label for="id_marca" class="col-md-4 control-label">{{trans('winsumos.marcas')}}</label>
                        <div class="col-md-7">
                            <select id="id_marca" name="id_marca" class="form-control" required="required">
                                <option value="">{{trans('winsumos.seleccione')}}</option>
                                @foreach($marcas as $marca)
                                    <option @if(old('id_marca') == $marca->id) selected @endif value="{{$marca->id}}"> {{$marca->nombre}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('id_marca'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_marca') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group col-xs-6{{ $errors->has('id_tipo') ? ' has-error' : '' }}">
                        <label for="id_tipo" class="col-md-4 control-label">{{trans('winsumos.tipo_producto')}}</label>
                        <div class="col-md-7">
                            <select id="id_tipo" name="id_tipo" class="form-control" required="required">
                                <option value="">{{trans('winsumos.seleccione')}}</option>

                                @foreach($tipos as $value)
                                    <option @if(old('id_tipo') == $value->id) selected @endif value="{{$value->id}}"> {{$value->nombre}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('id_tipo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_tipo') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group col-xs-6{{ $errors->has('usos') ? ' has-error' : '' }}">
                        <label for="usos" class="col-md-4 control-label" >{{trans('winsumos.cant_usos')}}</label>
                        <div class="col-md-7">
                            <input id="usos" type="number" class="form-control" name="usos" value="@if(old('usos') != ''){{old('usos')}}@else{{'1'}}@endif" onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" required autofocus>
                            @if ($errors->has('usos'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('usos') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group col-xs-6{{ $errors->has('iva') ? ' has-error' : '' }}">
                        <label for="iva" class="col-md-4 control-label">{{trans('winsumos.posee_iva')}}</label>
                        <div class="col-md-7">
                            <select id="iva" name="iva" class="form-control" required>
                                <option @if(old('iva') == 1) selected @endif value="1">{{trans('winsumos.si')}}</option>
                                <option @if(old('iva') == 2) selected @endif value="0">{{trans('winsumos.no')}}</option>
                            </select>
                            @if ($errors->has('iva'))
                            <span class="help-block">
                                <strong>{{ $errors->first('iva') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group col-xs-6">
                        <label style="padding-left: 15px">{{trans('winsumos.usar_mismo_codigo')}}&nbsp;&nbsp;<input id="codigo_siempre" name="codigo_siempre" type="checkbox" value="1" class="flat-blue"  style="position: absolute; opacity: 0;"></label>
                    </div>

                    <div class="form-group col-xs-6{{ $errors->has('cod_general') ? ' has-error' : '' }}">
                        <label for="cod_general" class="col-md-4 control-label" >{{trans('winsumos.codigo_general')}}</label>
                        <div class="col-md-7">
                            <select class="form-control select2_productos" required name="cod_general" id="cod_general" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-xs-6{{ $errors->has('tipo') ? ' has-error' : '' }}">
                        <label for="tipo" class="col-md-4 control-label">{{trans('winsumos.Tipo')}}</label>
                        <div class="col-md-7">
                            <select id="tipo" name="tipo" class="form-control" required>
                                <option value="0">{{trans('winsumos.otros')}}</option>
                                @foreach($tipo_plantilla as $tp)
                                    <option value="{{$tp->id}}">{{$tp->nombre}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('tipo'))
                            <span class="help-block">
                                <strong>{{ $errors->first('tipo') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group col-xs-6{{ $errors->has('imagen_producto') ? ' has-error' : '' }}">
                        <label for="imagen_producto" class="col-md-4 control-label">{{trans('winsumos.Foto')}} </label>
                        <div class="col-md-7">
                        <input type="file" name="imagen_producto" id="imagen_producto" class="imagen_producto form-control"  accept="aplication/vnd.openxmformats-officedocument.spreadsheetml.sheet" value="old('imagen_producto') }}">  
                        </div>
                    </div>
              
                    <div class="clearfix">
                        <span></span>
                    </div>
                    <br><br>

                    <div class="form-group col-xs-10" style="text-align: center;margin-top:20px">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                {{trans('winsumos.guardar')}}
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
  var fila = $("#mifila").html();
    $(document).ready(function(){
      $('#codigo_siempre').iCheck({
        checkboxClass: 'icheckbox_flat-blue',
        increaseArea: '20%' // optional
      });
    });

     $('body').on('click', '.delete', function () {
       /* var borrar = $(this).parent().prev().prev().children().closest('.debe').val();
        var total = $("#valor").val();
        var dc = $("#debe_contable").val();

        total = parseFloat(total) - parseFloat(borrar);
        total = parseFloat(total).toFixed(2)
        $("#valor").val(total);

        dc = parseFloat(dc) - parseFloat(borrar);
        dc = parseFloat(dc).toFixed(2)
        $("#dc").val(total);
*/
        $(this).parent().parent().remove();
    });

     function nuevo(){
        var nuevafila = $("#mifila").html();
        var rowk = document.getElementById("agregar_cuentas").insertRow(-1);
        rowk.innerHTML = fila;
        //rowk.className="well";


    }  $('.select2').select2({
            tags: false
        });

    $(document).ready(function() {
        $('#cod_general').select2({
            placeholder: "{{trans('winsumos.ingrese_nombre_producto')}}...",
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
                    return {
                        results: data
                    };
                }
            }
        });
    });



  </script>


</section>
@endsection
