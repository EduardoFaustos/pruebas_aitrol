@extends('contable.arqueo_caja.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header"><h3 class="box-title">Editar Denominación</h3></div>
             <form class="form-vertical" role="form" id="formulario">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">
                    
                    <input type="hidden" id="id" name="id" value="{{$id}}">       
                        
                    <!--Nombre del area-->

                    <div class="form-group col-xs-12{{ $errors->has('nombre') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-2 control-label">Nombre de Denominación</label>
                        <div class="col-md-7">
                            <input id="nombre" type="text" class="form-control" name="nombre" value="{{ $denominacion->nombre }}"  style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"required autofocus>
                            @if ($errors->has('nombre'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre') }}</strong>
                                </span>
                            @endif
                        </div>    
                    </div>
                    <div class="form-group col-xs-12{{ $errors->has('valor') ? ' has-error' : '' }}">
                        <label for="valor" class="col-md-2 control-label">Valor</label>
                        <div class="col-md-7">
                            <input id="valor" type="text" class="form-control" name="valor" value="{{ $denominacion->valor }}"  style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"required autofocus>
                            @if ($errors->has('valor'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('valor') }}</strong>
                                </span>
                            @endif
                        </div>    
                    </div>
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
                url: "{{route('ct_denominacion.update')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#formulario').serialize(),
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    var url = "{{route('ct_denominacion.index')}}"
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