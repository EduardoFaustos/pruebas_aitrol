@extends('insumos.proveedor.base')
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

<section class="content">
    <div class="row">
       <div class="col-md-12 col-xs-24" >
            <div class="box box-primary">
                <div class="box-header">
                    <div class="col-md-9">
                        <h3 class="box-title">Agregar Nuevo Proveedor</h3>
                    </div>
                    <div class="col-md-3" style="text-align: right;">
                        <a type="button" href="{{URL::previous()}}" class="btn btn-primary btn-sm">
                          <span class="glyphicon glyphicon-arrow-left"> Regresar</span>
                        </a>
                    </div>
                </div>
                <form class="form-vertical" role="form" method="POST" action="{{ route('proveedor.store') }}">
                {{ csrf_field() }}
                    <div class="box-body col-xs-24">
                        <!--RUC-->
                        <div class="form-group col-xs-6{{ $errors->has('id') ? ' has-error' : '' }}">
                            <label for="id" class="col-md-4 control-label">RUC</label>
                            <div class="col-md-7">
                                <input id="id" type="text" class="form-control" onkeypress="return check(event)" name="id" value="{{ old('id') }}" style="text-transform:uppercase;"  maxlength="13" required autofocus onchange="validarRuc(this.value);">
                                @if ($errors->has('id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--Razon Social-->
                        <div class="form-group col-xs-6{{ $errors->has('razonsocial') ? ' has-error' : '' }}">
                            <label for="razonsocial" class="col-md-4 control-label">Razón Social</label>
                            <div class="col-md-7">
                                <input id="razonsocial" type="text" class="form-control" name="razonsocial" value="{{ old('razonsocial') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                    @if ($errors->has('razonsocial'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('razonsocial') }}</strong>
                                    </span>
                                    @endif
                                </div>
                        </div>
                        <!--Nombre Comercial-->
                        <div class="form-group col-xs-6{{ $errors->has('nombrecomercial') ? ' has-error' : '' }}">
                            <label for="nombrecomercial" class="col-md-4 control-label">Nombre Comercial</label>
                            <div class="col-md-7">
                                <input id="nombrecomercial" type="text" class="form-control" name="nombrecomercial" value="{{ old('nombrecomercial') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('nombrecomercial'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombrecomercial') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <!--Ciudad-->
                        <div class="form-group col-xs-6{{ $errors->has('ciudad') ? ' has-error' : '' }}">
                            <label for="ciudad" class="col-md-4 control-label">Ciudad</label>
                            <div class="col-md-7">
                                <input id="ciudad" type="text" class="form-control" name="ciudad" value="{{ old('ciudad') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('ciudad'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('ciudad') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <!--Direccion-->
                        <div class="form-group col-xs-6{{ $errors->has('direccion') ? ' has-error' : '' }}">
                            <label for="direccion" class="col-md-4 control-label">Direccion</label>
                            <div class="col-md-7">
                                <input id="direccion" type="text" class="form-control" name="direccion" value="{{ old('direccion') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('direccion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('direccion') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--email-->                        
                        <div class="form-group col-xs-6{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail</label>
                            <div class="col-md-7">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--telefono1-->
                        <div class="form-group col-xs-6{{ $errors->has('telefono1') ? ' has-error' : '' }}">
                            <label for="telefono1" class="col-md-4 control-label">Telefono Domicilio</label>
                            <div class="col-md-7">
                                <input id="telefono1" type="number" class="form-control" name="telefono1" value="{{ old('telefono1') }}" required autofocus>
                                @if ($errors->has('telefono1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono1') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--telefono2-->
                        <div class="form-group col-xs-6{{ $errors->has('telefono2') ? ' has-error' : '' }}">
                            <label for="telefono2" class="col-md-4 control-label">Telefono Celular</label>
                            <div class="col-md-7">
                                <input id="telefono2" type="number" class="form-control" name="telefono2" value="{{ old('telefono2') }}" required autofocus>
                                @if ($errors->has('telefono2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono2') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--id_tipo_usuario-->
                        <div class="form-group col-xs-6{{ $errors->has('id_tipo_proveedor') ? ' has-error' : '' }}">
                            <label for="id_tipo_proveedor" class="col-md-4 control-label">Tipo Proveedor</label>
                            <div class="col-md-7">
                                <select id="id_tipo_proveedor" name="id_tipo_proveedor" class="form-control" required="required">
                                    <option value="">Seleccione..</option>
                                    @foreach($tipos as $value)
                                        @if ($value->estado != 0)
                                                <option value="{{$value->id}}">{{$value->nombre}}</option>
                                        @endif    
                                    @endforeach
                                </select>  
                                @if ($errors->has('id_tipo_proveedor'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_tipo_proveedor') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
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
    </div>
</section>
@endsection


<script type="text/javascript">
   
   function validarRuc(val){

    ruc = document.getElementById('id').value;

   /* $proveedor_id = \Sis_medico\Proveedor::where('id', $request['id'])->first();
    dd($proveedor_id);*/

    /* Verifico que el campo no contenga letras */                  
        var ok=1;
        for (i=0; i<ruc.length && ok==1 ; i++){
            var n = parseInt(ruc.charAt(i));
            if (isNaN(n)) ok=0;
        }

        if (ok==0){
         alert("No puede ingresar caracteres en el número");         
         return false;
        }

        if (ruc.length<13){              
         alert('El número ingresado no es válido');                  
         return true;
        }


   } 

</script>
