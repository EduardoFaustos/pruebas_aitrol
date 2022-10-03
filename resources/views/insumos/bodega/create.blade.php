@extends('insumos.bodega.base')
@section('action-content')

<section class="content">
    <div class="row">
        <div class="col-md-12 col-xs-24" >
            <div class="box box-primary">
                <div class="box-header">
                    <div class="col-md-9">
                      <h3 class="box-title">{{trans('winsumos.agregar_nueva_bodega')}}</h3>
                    </div>
                    <div class="col-md-3" style="text-align: right;">   
                        <a type="button" onclick="goBack()"  class="btn btn-primary btn-sm">
                        <span class="glyphicon glyphicon-arrow-left">{{trans('winsumos.regresar')}}</span>
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('bodega.store') }}">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="col-md-4 control-label">{{trans('winsumos.nombre_bodega')}}</label>
                            <div class="col-md-6">
                                <input id="nombre" type="text" class="form-control" name="nombre" value="{{ old('nombre') }}" required autofocus style="text-transform:uppercase;">
                                @if ($errors->has('nombre'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('id_hospital') ? ' has-error' : '' }}">
                            <label for="id_hospital" class="col-md-4 control-label">{{trans('winsumos.hospital')}}</label>
                            <div class="col-md-6">
                                <select id="id_hospital" name="id_hospital" class="form-control" required >
                                        <option value="">{{trans('winsumos.seleccione')}}</option>
                                        @foreach ($hospital as $hospital)
                                            <option @if(old('id_hospital')==$hospital->id){{"selected"}}@endif value="{{$hospital->id}}">{{$hospital->nombre_hospital}} </option>
                                        @endforeach
                                </select>      
                                @if ($errors->has('id_hospital'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_hospital') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class=" form-group{{ $errors->has('color') ? ' has-error' : '' }}">
                            <label for="color" class="col-md-4 control-label">{{trans('winsumos.color_etiqueta')}}</label>
                            <div class="col-md-6 colorpicker">
                                <input id="color" type="hidden" type="text" class="form-control" name="color"  value="@if(!is_null(old('color'))) {{ old('color') }} @else #000000 @endif" required >
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
                                    {{trans('winsumos.guardar')}}
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
