@extends('mantenimientos_botones_labs.mantenimiento_nivel.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header"><h3 class="box-title">{{(trans('nivel.EditarNivel'))}}</h3>
        </div>
        <div class="box-body">
            <form id="editar_nivel" method="post" action="{{route('nivel.update')}}">
                {{ csrf_field() }}
            
                <input type="hidden" name="id" id="id" value="{{$id}}">

                    
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('nombre') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-20 control-label">{{(trans('nivel.Nombre'))}}</label>
                        <input type="text" name="nombre" size="20" class="form-control" value="{{$nivel->nombre}}" required maxlength="100">
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('nombre_corto') ? ' has-error' : '' }}">
                        <label for="nombre_corto" class="col-md-20 control-label"> {{(trans('nivel.NombreCorto'))}} </label>
                        <input type="text" name="nombre_corto" size="20" class="form-control" value="{{$nivel->nombre_corto}}" required maxlength="100">
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('grupo') ? ' has-error' : '' }}">
                        <label for="grupo" class="col-md-20 control-label"> {{(trans('nivel.Grupo'))}} </label>
                        <input type="text" name="grupo" size="20" class="form-control" value="{{$nivel->grupo}}" required maxlength="3">
                    </div>
                </div>                            

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            {{(trans('nivel.Actualizar'))}}
                        </button>
                    </div>
                </div>   
                
                
            </form>
        </div>
    </div>
</section>

@endsection