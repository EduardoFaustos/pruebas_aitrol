@extends('procedimiento.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header">
                <h3 class="box-title">{{trans('procedimiento.editarprocedimiento')}}</h3>
            </div>
            <form class="form-vertical" role="form" method="POST" action="{{ route('procedimiento.update', ['id' => $procedimiento->id]) }}">
                <div class="box-body col-xs-24">
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <!--nombre-->
                    <div class="form-group col-xs-12{{ $errors->has('nombre') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-2 control-label">{{trans('procedimiento.nombre')}}</label>
                        <div class="col-md-7">
                            <input id="nombre" type="text" class="form-control" name="nombre" value="{{ $procedimiento->nombre }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                            @if ($errors->has('nombre'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nombre') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <!--observacion-->
                    <div class="form-group col-xs-12{{ $errors->has('observacion') ? ' has-error' : '' }}">
                        <label for="observacion" class="col-md-2 control-label">{{trans('procedimiento.observacion')}}</label>
                        <div class="col-md-7">
                            <input id="observacion" type="text" class="form-control" name="observacion" value="{{ $procedimiento->observacion }}" required autofocus>
                            @if ($errors->has('observacion'))
                            <span class="help-block">
                                <strong>{{ $errors->first('observacion') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group col-xs-12{{ $errors->has('id_grupo_procedimiento') ? ' has-error' : '' }}">
                        <label for="id_grupo_procedimiento" class="col-md-2 control-label"> {{trans('procedimiento.tipoprocedimiento')}} </label>
                        <div class="col-md-7">
                            <select id="id_grupo_procedimiento" name="id_grupo_procedimiento" class="form-control" value="{{$procedimiento->id_grupo_procedimiento}}">
                                @foreach($tprocedimientos as $tprocedimiento)
                                <option value="{{$tprocedimiento->id}}>{{$tprocedimiento->nombre}}">
                                    {{$tprocedimiento->nombre}}
                                </option>
                                @endforeach
                            </select>
                            @if ($errors->has('id_grupo_procedimiento'))
                            <span class="help-block">
                                <strong>{{ $errors->id_grupo_procedimiento}}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <!--estado-->
                    <div class="form-group col-xs-12{{ $errors->has('estado') ? ' has-error' : '' }}">
                        <label for="estado" class="col-md-2 control-label">{{trans('procedimiento.estado')}}</label>
                        <div class="col-md-7">
                            <select id="estado" name="estado" class="form-control">
                                <option {{$procedimiento->estado == 0 ? 'selected' : ''}} value="0">{{trans('procedimiento.inactivo')}}</option>
                                <option {{$procedimiento->estado == 1 ? 'selected' : ''}} value="1">{{trans('procedimiento.activo')}}</option>
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
                            <button type="submit" class="btn btn-primary">
                                {{trans('procedimiento.actualizar')}}
                            </button>
                        </div>
                    </div>

                </div>
            </form>

        </div>

    </div>
</div>

@endsection