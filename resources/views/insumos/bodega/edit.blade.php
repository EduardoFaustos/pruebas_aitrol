@extends('insumos.bodega.base')
@section('action-content')

<section class="content">
    <div class="row">
        <div class="col-md-12 col-xs-24">
            <div class="box box-primary">
                <div class="box-header">
                    <div class="col-md-9">
                      <h3 class="box-title">{{trans('winsumos.actualizar_bodega')}}</h3>
                    </div>
                    <div class="col-md-3" style="text-align: right;">   
                        <a type="button" onclick="goBack()" class="btn btn-primary btn-sm">
                        <span class="glyphicon glyphicon-arrow-left">{{trans('winsumos.regresar')}}</span>
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('bodega.update', ['id' => $bodega->id]) }}" enctype="multipart/form-data">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="col-md-4 control-label">{{trans('winsumos.nombre')}}</label>
                            <div class="col-md-6">
                                <input id="nombre" type="text" class="form-control" name="nombre" value="{{ $bodega->nombre }}" required autofocus style="text-transform:uppercase;">
                                @if ($errors->has('nombre'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('id_hospital') ? ' has-error' : '' }}">
                            <label for="id_hospital" class="col-md-4 control-label">{{trans('winsumos.hospital')}}</label>
                            <div class="col-md-6">
                                <select class="form-control" name="id_hospital">
                                    @foreach($hospital as $value)
                                        <option value="{{$value->id}}" @if($bodega->id_hospital == $value->id) selected @endif >{{$value->nombre_hospital}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('estado') ? ' has-error' : '' }}">
                            <label for="estado" class="col-md-4 control-label">{{trans('winsumos.estado')}}</label>
                            <div class="col-md-6">
                                <select class="form-control" name="estado">
                                    <option {{$bodega->estado == 0 ? 'selected' : ''}} value="0">{{trans('winsumos.inactivo')}}</option>
                                    <option {{$bodega->estado == 1 ? 'selected' : ''}} value="1">{{trans('winsumos.activo')}}</option>
                                </select>
                            </div>
                        </div>

                        <div class=" form-group{{ $errors->has('color') ? ' has-error' : '' }}">
                            <label for="color" class="col-md-4 control-label">{{trans('winsumos.color_etiqueta')}}</label>
                            <div class="col-md-6 colorpicker">
                                <input id="color" type="hidden" type="text" class="form-control" name="color" value="{{ $bodega->color }}" required>
                                <span class="input-group-addon colorpicker-2x"><i style="width: 50px; height: 50px;"></i></span> 
                                @if ($errors->has('color'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('color') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    {{trans('winsumos.actualizar')}}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div> 
        </div>
    </div>
</section>

@endsection
<script type="text/javascript">
    function goBack() {
        window.history.back()
    }
</script>

<style>
    .colorpicker-2x .colorpicker-saturation {
        width: 200px;
        height: 200px;
    }

    .colorpicker-2x .colorpicker-hue,
    .colorpicker-2x .colorpicker-alpha {
        width: 30px;
        height: 200px;
    }

    .colorpicker-2x .colorpicker-color,
    .colorpicker-2x .colorpicker-color div {
        height: 30px;
    }
</style>