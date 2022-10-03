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
              <h3 class="box-title">Agregar Nuevo Producto</h3>
            </div>
            <div class="col-md-3" style="text-align: right;">
                <button onclick="goBack()" class="btn btn-danger" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;">
                   <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <div class="box-body" style="background-color: #ffffff;">
            <form class="form-vertical" role="form" method="POST" action="{{ route('producto.store') }}">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">
                    <!--Codigo-->
                    <div class="form-group col-xs-6{{ $errors->has('codigo') ? ' has-error' : '' }}">
                        <label for="codigo" class="col-md-4 control-label">Codigo</label>
                        <div class="col-md-7">
                            <input id="codigo" type="text" class="form-control" onkeypress="return check(event)" name="codigo" value="{{ old('codigo') }}" style="text-transform:uppercase;"  maxlength="25"  required autofocus >
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
                        <label for="nombre" class="col-md-4 control-label">Nombre </label>
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
                        <label for="descripcion" class="col-md-4 control-label">Descripci&oacuten</label>
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
                        <label for="cantidad_unidad" class="col-md-4 control-label">Cantidad de medida:</label>
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
                        <label for="medida" class="col-md-4 control-label">Medida</label>
                        <div class="col-md-7">
                            <select id="medida" name="medida" class="form-control" required="required">
                                <option value="">Seleccione..</option>
                                <option @if(old('medida') == 'Uni') selected @endif value="Uni">Unidad</option>
                                <option @if(old('medida') == 'Kg') selected @endif value="Kg">Kilogramos</option>
                                <option @if(old('medida') == 'G') selected @endif value="G">Gramos</option>
                                <option @if(old('medida') == 'Mg') selected @endif value="Mg">Miligramos</option>
                                <option @if(old('medida') == 'Ml') selected @endif value="Ml">Mililitros</option>
                                <option @if(old('medida') == 'L') selected @endif value="L">Litros</option>
                                <option @if(old('medida') == 'Lb') selected @endif value="Lb">Libras</option>
                                <option @if(old('medida') == 'm') selected @endif value="m">Metros</option>
                                <option @if(old('medida') == 'cm') selected @endif value="cm">Centimetros</option>
                                <option @if(old('medida') == 'mm') selected @endif value="mm">Milimetros</option>
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
                                <option value="">Seleccione..</option>
                                <option @if(old('despacho') == 1) selected @endif value="1">codigo de serie</option>
                                <option @if(old('despacho') == 0) selected @endif value="0">codigo de producto</option>
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
                        <label for="minimo" class="col-md-4 control-label" >Stock Minimo</label>
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
                        <label for="registro_sanitario" class="col-md-4 control-label">Registro Sanitario</label>
                        <div class="col-md-7">
                            <input id="registro_sanitario" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" type="registro_sanitario" class="form-control" name="registro_sanitario" value="{{ old('registro_sanitario') }}" required>
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
                        <label for="id_marca" class="col-md-4 control-label">Marcas</label>
                        <div class="col-md-7">
                            <select id="id_marca" name="id_marca" class="form-control" required="required">
                                <option value="">Seleccione..</option>
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
                        <label for="id_tipo" class="col-md-4 control-label">Tipo de Producto</label>
                        <div class="col-md-7">
                            <select id="id_tipo" name="id_tipo" class="form-control" required="required">
                                <option value="">Seleccione..</option>

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
                        <label for="usos" class="col-md-4 control-label" >Cantidad de Usos</label>
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
                        <label for="iva" class="col-md-4 control-label">Posee Iva</label>
                        <div class="col-md-7">
                            <select id="iva" name="iva" class="form-control" required>
                                <option @if(old('iva') == 1) selected @endif value="1">Si</option>
                                <option @if(old('iva') == 2) selected @endif value="0">No</option>
                            </select>
                            @if ($errors->has('iva'))
                            <span class="help-block">
                                <strong>{{ $errors->first('iva') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group col-xs-6">
                        <label style="padding-left: 15px">Usar mismo codigo&nbsp;&nbsp;<input id="codigo_siempre" name="codigo_siempre" type="checkbox" value="1" class="flat-blue"  style="position: absolute; opacity: 0;"></label>
                    </div>

                    <div class="form-group col-xs-6{{ $errors->has('cod_general') ? ' has-error' : '' }}">
                        <label for="cod_general" class="col-md-4 control-label" >Codigo General</label>
                        <div class="col-md-7">
                            <select class="form-control select2_productos" name="cod_general" id="cod_general" style="width: 100%;">
                                <option value="">Seleccione...</option>
                                @foreach($ct_productos as $value)
                                <option value="{{$value->id}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="clearfix">
                        <span></span>
                    </div>
                    <br><br>
                    <!--Precios-->

                    <!--<div class="form-group col-xs-6{{ $errors->has('precio1') ? ' has-error' : '' }}">
                        <label for="precio1" class="col-md-4 control-label">Precio 1</label>
                        <div class="col-md-7">
                            <input id="precio1" type="precio1" class="form-control" name="precio1" value="" required>
                            @if ($errors->has('precio1'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('precio1') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6{{ $errors->has('precio2') ? ' has-error' : '' }}">
                        <label for="precio2" class="col-md-4 control-label">Precio 2</label>
                        <div class="col-md-7">
                            <input id="precio2" type="precio2" class="form-control" name="precio2" value="" required>
                            @if ($errors->has('precio2'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('precio2') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6{{ $errors->has('precio3') ? ' has-error' : '' }}">
                        <label for="precio3" class="col-md-4 control-label">Precio 3</label>
                        <div class="col-md-7">
                            <input id="precio3" type="precio3" class="form-control" name="precio3" value="" required>
                            @if ($errors->has('precio3'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('precio3') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6{{ $errors->has('precio4') ? ' has-error' : '' }}">
                        <label for="precio4" class="col-md-4 control-label">Precio 4</label>
                        <div class="col-md-7">
                            <input id="precio4" type="precio4" class="form-control" name="precio4" value="" required>
                            @if ($errors->has('precio4'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('precio4') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6{{ $errors->has('precio5') ? ' has-error' : '' }}">
                        <label for="precio5" class="col-md-4 control-label">Precio 5</label>
                        <div class="col-md-7">
                            <input id="precio5" type="precio5" class="form-control" name="precio5" value="" required>
                            @if ($errors->has('precio5'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('precio5') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>-->
<!--
                    <h4>Detalle de Precios</h4>
                    <div class="col-md-12 table-responsive">
                    <input type="hidden" name="contador" id="contador" value="0">
                    <input type="hidden" name="total" id="total" value="0">
                    <table id="example2" class="table  table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                        <thead class="thead-dark">
                            <tr class='well-darks'>
                                <th width="55%" class="" tabindex="0">Nivel</th>
                                <th width="20%" class="" tabindex="0">Precio</th>
                                <th width="5%" class="" tabindex="0">
                                    <button onclick="nuevo()" type="button" class="btn btn-success btn-gray" >
                                        <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="agregar_cuentas">
                            <tr class="wells" id="mifila">
                                <td>
                                    <input class="form-control input-sm" type="text" style="width: 80%;height:20px;" placeholder="#" name="nivel[]" required>
                                </td>
                                <td>
                                    <input class="form-control input-sm" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" placeholder="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precio[]" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-gray delete" >
                                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                           
                        </tbody>
                       
                    </table>
                    <div class="input-group col-xs-6 col-md-4">
                        <label  class="form-control" style="padding-left: 15px" aria-label="...">Acepta Descuento</label>
                        <span class="input-group-addon">
                            <input type="checkbox" aria-label="..." id="descuento" name="descuento">
                        </span>
                    </div>
                </div>-->
               
                

                    <div class="form-group col-xs-10" style="text-align: center;margin-top:20px">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                Agregar
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
            $('.select2_productos').select2({
                tags: false
            });
        });

  </script>


</section>
@endsection
