@extends('mantenimientos_botones_labs.mantenimiento_protocolo.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header"><h3 class="box-title">{{(trans('protocolo.CrearProtocolo'))}}</h3>
        </div>
        <div class="box-body">
            <form id="crear_protocolo" method="post" action="{{route('protocolo.store')}}">
                {{ csrf_field() }}
            
                             
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('nombre') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-20 control-label"> {{(trans('protocolo.Nombre'))}}</label>
                        <input type="text" name="nombre" size="20" class="form-control" value=" " required maxlength="100">
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('est_ambu') ? ' has-error' : '' }}">
                        <label for="est_ambu" class="col-md-20 control-label"> {{(trans('protocolo.EstadoAmbulatorio'))}}</label>
                        <input type="text" name="est_ambu" size="20" class="form-control" value="Ambulatorio" readonly>
                    </div>
                </div>
   
                
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                        {{(trans('protocolo.Agregar'))}}
                        </button>
                    </div>
                </div>   
                
                
            </form>
        </div>
    </div>
</section>

@endsection