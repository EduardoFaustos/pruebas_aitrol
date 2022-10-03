@extends('membresia.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header"><h3 class="box-title">Editar Membresias</h3></div>
             <form class="form-vertical" role="form" id="formulario">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">
                    
                    <input type="hidden" id="id" name="id" value="{{$id}}">       
                        
                    <!--Nombre del area-->
                    <div class="form-group col-xs-12{{ $errors->has('empresa_id') ? ' has-error' : '' }}">
                                        <label for="empresa_id" class="col-md-2 control-label">Identificacion de la Empresa</label>
                                        <div class="input-group col-md-7">
                                            <select class="form-control input-sm" name="empresa_id" id="empresa_id" required>
                                                <!--option value="">Seleccione ...</option-->
                                                @foreach($empresas as $empresa)
                                                <option @if($membresia->empresa_id == $empresa->id) selected @endif 
                                                @if(old('empresa_id')==$empresa->id) selected @endif value="{{$empresa->id}}">{{$empresa->razonsocial}}</option>
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
                        <label for="empresa_id" class="col-md-2 control-label">Identificacion de la Empresa</label>
                        <div class="col-md-7">
                            <input id="empresa_id" type="text" class="form-control" name="empresa_id" value="{{ $membresia->empresa_id }}"  style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"required autofocus>
                            @if ($errors->has('empresa_id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('empresa_id') }}</strong>
                                </span>
                            @endif
                        </div>    
                    </div> -->
                    <div class="form-group col-xs-12{{ $errors->has('nombre') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-2 control-label">Nombre de Membresia</label>
                        <div class="col-md-7">
                            <input id="nombre" type="text" class="form-control" name="nombre" value="{{ $membresia->nombre }}"  style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"required autofocus>
                            @if ($errors->has('nombre'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre') }}</strong>
                                </span>
                            @endif
                        </div>    
                    </div>
                    <div class="form-group col-xs-12{{ $errors->has('precio_mensual') ? ' has-error' : '' }}">
                        <label for="precio_mensual" class="col-md-2 control-label">Precio Mensual</label>
                        <div class="col-md-7">
                            <input id="precio_mensual" type="text" class="form-control" name="precio_mensual" value="{{ $membresia->precio_mensual }}"  style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"required autofocus>
                            @if ($errors->has('precio_mensual'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('precio_mensual') }}</strong>
                                </span>
                            @endif
                        </div>    
                    </div>
                    <div class="form-group col-xs-12{{ $errors->has('precio_anual') ? ' has-error' : '' }}">
                        <label for="precio_anual" class="col-md-2 control-label">Precio Anual</label>
                        <div class="col-md-7">
                            <input id="precio_anual" type="text" class="form-control" name="precio_anual" value="{{ $membresia->precio_anual }}"  style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"required autofocus>
                            @if ($errors->has('precio_anual'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('precio_anual') }}</strong>
                                </span>
                            @endif
                        </div>    
                    </div>
                    <div class="form-group col-xs-12{{ $errors->has('url') ? ' has-error' : '' }}">
                        <label for="url" class="col-md-2 control-label">URL</label>
                        <div class="col-md-7">
                            <input id="url" type="text" class="form-control" name="url" value="{{ $membresia->url }}"  style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"required autofocus>
                            @if ($errors->has('url'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('url') }}</strong>
                                </span>
                            @endif
                        </div>    
                    </div>
                                            
                    <!--estado
                    <div class="form-group col-xs-12{{ $errors->has('estado_titulo') ? ' has-error' : '' }}">
                        <label for="estado_titulo" class="col-md-2 control-label">Estado</label>
                        <div class="col-md-7">
                            <select id="estado_titulo" name="estado_titulo" class="form-control">
                                <option {{$membresia->estado == 0 ? 'selected' : ''}} value="0">INACTIVO</option>
                                <option {{$membresia->estado == 1 ? 'selected' : ''}} value="1">ACTIVO</option>            
                            </select>  
                            @if ($errors->has('estado_titulo'))
                            <span class="help-block">
                                    <strong>{{ $errors->first('estado_titulo') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div> -->

                    <div class="form-group col-xs-6">
                        <div class="col-md-6 col-md-offset-4">
                            <button onclick="editar()" type="button" class="btn btn-success btn-gray">
                            Actualizar
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
<script>
  function editar() {
    var confirmar = confirm('¿seguro quiere editar?');
    if(confirmar) {
            $.ajax({
                url: "{{route('membresia.update')}}",
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
                            swal("Editado!", "Correcto", "success");
                            window.location = url;
                        }, 3000);
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