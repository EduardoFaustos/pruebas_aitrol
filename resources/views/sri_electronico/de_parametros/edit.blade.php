@extends('sri_electronico.de_parametros.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">{{trans('deParametros.Editar_Parametros')}}</h3>
        </div>
        <div class="box-body">
            <form id="editar_protocolo" method="post" action="{{route('deParametros.update')}}">
                {{ csrf_field() }}

                <input type="hidden" name="id" id="id" value="{{$id}}">
               
                

                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('nombre') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-20 control-label"> {{trans('deParametros.Nombre')}}</label>
                        <input type="text" name="nombre" size="20" class="form-control" value="{{$de_parametros->nombre}}">
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('valor') ? ' has-error' : '' }}">
                        <label for="valor" class="col-md-20 control-label"> {{trans('deParametros.Valor')}}</label>
                        <input type="number" name="valor" size="20" class="form-control" value="{{$de_parametros->valor}}">
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('estado') ? ' has-error' : '' }}">
                        <label for="estado" class="col-md-20 control-label"> {{trans('deParametros.Estado')}}</label>
                        <select class="form-control validar" name="estado" id="estado">
                    <option {{$de_parametros->estado == 1 ? 'selected' : ''}} value="1">{{trans('deParametros.Si')}}</option>
                    <option {{$de_parametros->estado == 0 ? 'selected' : ''}} value="0">{{trans('deParametros.No')}}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                        {{trans('deParametros.Actualizar')}}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

@endsection