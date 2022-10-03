@extends('camilla-mgmt.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header"><h3 class="box-title">{{trans('ecamilla.AgregarNuevaCamillaparaUnidad:')}} {{ $hospital->nombre_hospital }}</h3></div>
            <form class="form-vertical" role="form" method="POST" action="{{ route('camilla-management.grabar',['id' => $hospital->id]) }}">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">
                   
                    <!--nombre_camilla-->
                    <div class="form-group col-xs-10{{ $errors->has('nombre_camilla') ? ' has-error' : '' }}">
                        <label for="nombre_camilla" class="col-md-2 control-label">{{trans('ecamilla.NombreCamilla')}}</label>
                        <div class="col-md-7">
                            <input id="nombre_camilla" type="text" class="form-control" name="nombre_camilla" value="{{ old('nombre_camilla') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('nombre_camilla'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre_camilla') }}</strong>
                                    </span>
                                @endif
                        </div>
                    </div>

                    <input id="id_hospital" type="hidden" class="form-control" name="id_hospital" value="{{ $hospital->id }}" >
               
                        
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                               {{trans('ecamilla.Agregar')}}
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
@endsection
