@extends('mantenimiento_nomina.tipo_pago.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header"><h3 class="box-title">Editar Tipo Pago</h3>
        </div>
        <div class="box-body">
            <form id="editar_plantilla" method="post" action="{{route('tipo_pago.update')}}">
                {{ csrf_field() }}
            
                <input type="hidden" name="id" id="id" value="{{$id}}">

                    
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('descripcion_tipo') ? ' has-error' : '' }}">
                        <label for="descripcion_tipo" class="col-md-20 control-label"> Descripción Pago </label>
                        <input type="text" name="descripcion_tipo" size="20" class="form-control" value="{{$tipos_pagos->tipo}}" required maxlength="100">
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('estado') ? ' has-error' : ''}}">
                        <label for="estado" class="col-md-20 control-label">Estado</label>
                        <select class="form-control" id="estado" name="estado"> 
                        <option {{$tipos_pagos->estado == 0 ? 'selected' : ''}} value="0">INACTIVO</option>
                        <option {{$tipos_pagos->estado == 1 ? 'selected' : ''}} value="1">ACTIVO</option> 
                        </select >
                    </div>
                </div>
                            

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            Actualizar
                        </button>
                    </div>
                </div>   
                
                
            </form>
        </div>
    </div>
</section>

@endsection