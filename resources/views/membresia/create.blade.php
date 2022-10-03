@extends('membresia.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header"><h3 class="box-title">Agregar Nueva Membresia </h3></div>
            <form class="form-vertical" role="form" id="formulario">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">
                   
                
                    <!--Area-->
                    <div class="form-group col-xs-12{{ $errors->has('empresa_id') ? ' has-error' : '' }}">
                        <label for="empresa_id" class="col-md-3 control-label">Identificacion de la Empresa</label>
                        <div class="col-md-7">
                            <select class="form-control input-sm" name="empresa_id" id="empresa_id" required>
                                <option value="">Seleccione ...</option>
                                @foreach($empresas as $empr)
                                <option value= {{$empr->id}}>{{$empr->razonsocial}}</option>
                                @endforeach
                                @if ($errors->has('empresa_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('empresa_id') }}</strong>
                                    </span>
                                @endif
                            </select>
                        </div>
                    </div>
                    <!--
                    <div class="form-group col-xs-12{{ $errors->has('empresa_id') ? ' has-error' : '' }}">
                        <label for="empresa_id" class="col-md-3 control-label">Identificacion de la Empresa</label>
                        <div class="col-md-7">
                            <input  id="empresa_id" type="selec" class="form-control" name="empresa_id" value="{{ old('empresa_id') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                             @if ($errors->has('empresa_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('empresa_id') }}</strong>
                                    </span>
                                @endif
                        </div>
                    </div> -->
                    <div class="form-group col-xs-12{{ $errors->has('nombre') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-3 control-label">Nombre</label>
                        <div class="col-md-7">
                            <input  id="nombre" type="text" class="form-control" name="nombre" value="{{ old('nombre') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                             @if ($errors->has('nombre'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre') }}</strong>
                                    </span>
                                @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-12{{ $errors->has('precio_mensual') ? ' has-error' : '' }}">
                        <label for="precio_mensual" class="col-md-3 control-label">Precio Mensual</label>
                        <div class="col-md-7">
                            <input  id="precio_mensual" type="text" class="form-control" name="precio_mensual" value="{{ old('precio_mensual') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                             @if ($errors->has('precio_mensual'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('precio_mensual') }}</strong>
                                    </span>
                                @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-12{{ $errors->has('precio_anual') ? ' has-error' : '' }}">
                        <label for="precio_anual" class="col-md-3 control-label">Precio Anual</label>
                        <div class="col-md-7">
                            <input  id="precio_anual" type="text" class="form-control" name="precio_anual" value="{{ old('precio_anual') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                             @if ($errors->has('precio_anual'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('precio_anual') }}</strong>
                                    </span>
                                @endif
                        </div>
                    </div>
                     <div class="form-group col-xs-12{{ $errors->has('url') ? ' has-error' : '' }}">
                        <label for="url" class="col-md-3 control-label">URL</label>
                        <div class="col-md-7">
                            <input  id="url" type="text" class="form-control" name="url" value="{{ old('url') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                             @if ($errors->has('url'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('url') }}</strong>
                                    </span>
                                @endif
                        </div>
                    </div>
                    <!--ESTADO 
                        <div class="form-group col-xs-12">
                            <label for="estado_titulo" class="col-md-3 texto">Estado</label>
                            <div class="col-md-7">
                            <select id="estado_titulo" name="estado_titulo" class="form-control" required>
                                <option>Seleccione...</option>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                            </div>
                        </div> -->
               
                        
                    <div class="form-group col-xs-6">
                        <div class="col-md-6 col-md-offset-4">
                            <button onclick="guardar()" type="button" class="btn btn-primary btn-gray">
                                Agregar
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
    function guardar(){
  
        var empresa = $("#empresa_id").val();
        var nombre = $("#nombre").val();
        var preciom = $("#precio_mensual").val();
        var precioa = $("#precio_anual").val();
        var url = $("#url").val();
        if(empresa == "" || nombre == "" || preciom=="" || precioa == "" || url=="" ){
            swal("Error!", "Campos Vacios", "error");
        }else{
               $.ajax({
                url: "{{route('membresia.store')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#formulario').serialize(),
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    var url = "{{route('membresia.index')}}"
                    if (data == 'ok') {
                        setTimeout(function() {
                            swal("Guardado!", "Correcto", "success");
                            window.location = url;
                        }, 1000);
                    }
                },
                error: function(xhr, status) {
                    alert('Existió un problema');
                    //console.log(xhr);
                },
            });
        }

    } 
</script>
@endsection