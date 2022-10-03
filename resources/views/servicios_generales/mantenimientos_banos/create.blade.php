@extends('servicios_generales.mantenimientos_banos.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header">
                <h3 class="box-title">Agregar Nuevo Baño</h3>
            </div>
            <form class="form-vertical" role="form" id="formulario">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">
                    <!--id_unidad-->
                    <div class="form-group col-xs-12{{ $errors->has('id_unidad') ? ' has-error' : '' }}">
                        <label for="id_unidad" class="col-md-3 control-label">Identificación de la unidad</label>
                        <div class="col-md-7">
                            <select class="form-control input-sm" name="id_unidad" id="id_unidad" required>
                                <option value="">Seleccione ...</option>
                                @foreach($generales as $gn)
                                <option value= {{$gn->id}}>{{$gn->nombre}}</option>
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
                        <label for="nombre" class="col-md-3 control-label">{{trans('sala-mgmt.nombre')}}</label>
                        <div class="col-md-7">
                            <input id="nombre" type="text" class="form-control" name="nombre" value="{{ old('nombre') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                            @if ($errors->has('nombre'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nombre') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <!--descripcion-->
                    <div class="form-group col-xs-12{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                        <label for="descripcion" class="col-md-3 control-label">{{trans('sala-mgmt.descripcion')}}</label>
                        <div class="col-md-7">
                            <input id="descripcion" type="text" class="form-control" name="descripcion" value="{{ old('descripcion') }}" required autofocus>
                            @if ($errors->has('descripcion'))
                            <span class="help-block">
                                <strong>{{ $errors->first('descripcion') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button onclick="guardar()" type="button" class="btn btn-primary btn-gray">
                                {{trans('sala-mgmt.agregar')}}
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
  
        var nombre = $("#nombre").val();
        if(nombre == "" ){
            swal("Error!", "Campos Vacios", "error");
        }else{
               $.ajax({
                url: "{{route('mantenimientos_banos.store')}}",
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