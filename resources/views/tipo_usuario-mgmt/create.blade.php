@extends('tipo_usuario-mgmt.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header">
                <h3 class="box-title">{{trans('tipo_usuario-mgmt.agregarnuevotipodeusuario')}}</h3>
            </div>
            <form class="form-vertical" role="form" method="POST" action="{{ route('tipo_usuario-management.store') }}">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">

                    <!--nombre-->
                    <div class="form-group col-xs-12{{ $errors->has('nombre') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-2 control-label">{{trans('tipo_usuario-mgmt.nombre')}}</label>
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
                        <label for="descripcion" class="col-md-2 control-label">{{trans('tipo_usuario-mgmt.descripcion')}}</label>
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
                            <button type="submit" class="btn btn-primary">
                                {{trans('tipo_usuario-mgmt.agregar')}}
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
@endsection