@extends('ciudad.base')
@section('action-content')


<!-- Main content -->
<section class="content">
    <div class="box">
        <!-- /.box-header -->
        <div class="box-body">
            <form class="form-vertical" role="form" method="POST" action="{{route('ciudad.guardar_actualizar')}}">
                {{ csrf_field() }}
                <input name="id" type="hidden" value="{{$ciudad->id}}">
                <div class="box-body col-xs-24">

                    <!--Nombre-->
                    <div class="form-group col-xs-12 {{ $errors->has('nombre') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-4 control-label">{{trans('pacientebiopsia.nombre')}} </label>
                        <div class="col-md-7">
                            <input id="nombre" type="text" class="form-control" name="nombre" value="{{$ciudad->nombre}}" required autofocus>
                            @if ($errors->has('nombre'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nombre') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-12 {{ $errors->has('descripcion') ? ' has-error' : '' }}">
                        <label for="descripcion" class="col-md-4 control-label">{{trans('pacientebiopsia.descripcion')}} </label>
                        <div class="col-md-7">
                            <input id="descripcion" type="text" class="form-control" name="descripcion" value="{{$ciudad->descripcion}}" required autofocus>
                            @if ($errors->has('descripcion'))
                            <span class="help-block">
                                <strong>{{ $errors->first('descripcion') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-12 {{ $errors->has('id_provincia') ? ' has-error' : '' }}">
                        <label for="id_provincia" class="col-md-4 control-label">{{trans('pacientebiopsia.provincia')}} </label>
                        <div class="col-md-7">
                            <select id="id_provincia" name="id_provincia" class="form-control" required="">
                                @foreach($provincias as $value)
                                <option @if($value->id == $ciudad->id_provincia) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('id_provincia'))
                            <span class="help-block">
                                <strong>{{ $errors->first('id_provincia') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-12 {{ $errors->has('estado') ? ' has-error' : '' }}">
                        <label for="estado" class="col-md-4 control-label">{{trans('pacientebiopsia.estado')}} </label>
                        <div class="col-md-7">
                            <select id="estado" name="estado" class="form-control col-md-9">
                                <option value="">{{trans('pacientebiopsia.seleccionar')}}..</option>
                                <option @if($ciudad->estado == 1) Selected @endif value="1">{{trans('pacientebiopsia.activo')}}</option>
                                <option @if($ciudad->estado == 0) Selected @endif value="0">{{trans('pacientebiopsia.inactivo')}}</option>
                            </select>
                            @if ($errors->has('estado'))
                            <span class="help-block">
                                <strong>{{ $errors->first('estado') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                {{trans('pacientebiopsia.agregar')}}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- /.box-body -->
    </div>
</section>
<!-- /.content -->
</div>
@endsection