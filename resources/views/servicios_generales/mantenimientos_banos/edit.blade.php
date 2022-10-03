@extends('servicios_generales.mantenimientos_banos.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header"><h3 class="box-title">Editar Baños</h3></div>
            <form class="form-vertical" role="form" id="formulario">
            {{ csrf_field() }}
                <div class="box-body col-xs-24">
                    
                    <input type="hidden" id="id" name="id" value="{{$id}}">       
                        
                    <!--id_unidad-->
                    <div class="form-group col-xs-12{{ $errors->has('id_unidad') ? ' has-error' : '' }}">
                                        <label for="id_unidad" class="col-md-2 control-label">Identificacion de la Unidad</label>
                                        <div class="col-md-7">
                                            <select class="form-control input-sm" name="id_unidad" id="id_unidad" required>
                                                <!--option value="">Seleccione ...</option-->
                                                @foreach($general as $gn)
                                                <option @if($mantenimiento_banos->id_unidad == $gn->id) selected @endif 
                                                @if(old('id_unidad')==$gn->id) selected @endif value="{{$gn->id}}">{{$gn->nombre}}</option>
                                                @endforeach
                                                @if ($errors->has('id_unidad'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('id_unidad') }}</strong>
                                                    </span>
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                    <!--nombre-->
                    <div class="form-group col-xs-12{{ $errors->has('nombre') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-2 control-label">{{trans('sala-mgmt.nombre')}}</label>
                        <div class="col-md-7">
                            <input id="nombre" type="text" class="form-control" name="nombre" value="{{ $mantenimiento_banos->nombre }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                            <input id="nombre_db" type="hidden" class="form-control" name="nombre_db" value="{{ $mantenimiento_banos->nombre }}" style="text-transform:uppercase;">
                            @if ($errors->has('nombre'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nombre') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <!--descripcion-->
                    <div class="form-group col-xs-12{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                        <label for="descripcion" class="col-md-2 control-label">{{trans('sala-mgmt.descripcion')}}</label>
                        <div class="col-md-7">
                            <input id="descripcion" type="text" class="form-control" name="descripcion" value="{{ $mantenimiento_banos->descripcion }}" required autofocus>
                            @if ($errors->has('descripcion'))
                            <span class="help-block">
                                <strong>{{ $errors->first('descripcion') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <!--estado-->
                    <div class="form-group col-xs-12{{ $errors->has('estado') ? ' has-error' : '' }}">
                        <label for="estado" class="col-md-2 control-label">{{trans('sala-mgmt.estado')}}</label>
                        <div class="col-md-7">
                            <select id="estado" name="estado" class="form-control">
                                <option {{$mantenimiento_banos->estado == 0 ? 'selected' : ''}} value="0">INACTIVO</option>
                                <option {{$mantenimiento_banos->estado == 1 ? 'selected' : ''}} value="1">ACTIVO</option>
                            </select>
                            @if ($errors->has('estado'))
                            <span class="help-block">
                                <strong>{{ $errors->first('estado') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group col-xs-6">
                        <div class="col-md-6 col-md-offset-4">
                        <button onclick="editar()" type="button" class="btn btn-success btn-gray">
                                {{trans('sala-mgmt.actualizar')}}
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
                url: "{{route('mantenimientos_banos.update')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#formulario').serialize(),
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    var url = "{{route('mantenimientos_banos.index')}}"
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