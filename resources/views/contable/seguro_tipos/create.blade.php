@extends('contable.seguro_tipos.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header"><h3 class="box-title">Crear Tipo de Seguro</h3>
        </div>
        <div class="box-body">
            <form id="crear_tipo" method="post" action="{{route('seguroTipos.store')}}">
                {{ csrf_field() }}
                        
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('nombre') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-20 control-label">Nombre</label>
                        <input type="text" name="nombre" size="20" class="form-control" value=" " required maxlength="100">
                    </div>
                </div>
                            
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('detalle') ? ' has-error' : '' }}">
                        <label for="detalle" class="col-md-20 control-label">Detalle</label>
                        <input type="text" name="detalle" size="20" class="form-control" value=" " maxlength="200">
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            Agregar
                        </button>
                    </div>
                </div>   
            </form>
        </div>
    </div>
</section>

@endsection