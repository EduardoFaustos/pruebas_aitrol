@extends('contable.sucursales.base')
@section('action-content')
<section class="content">
    
        <div class="form-inline">
            <form id="editar_plantilla" method="post" action="{{route('establecimiento.update_datos')}}">
                {{ csrf_field() }}
            
                <input type="hidden" name="id" id="id" value="{{$id}}">

                    
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('nombre_comercial') ? ' has-error' : '' }}">
                        <label for="nombre_comercial" class="col-md-20 control-label">Editar el Nombre Comercial</label>
                        <div class="col-md-5 col-md-offset-3">
                        <input type="text" name="nombre_comercial" size="20" class="form-control" value="{{$empresa->nombrecomercial}}" required maxlength="100">
                        </div>             
                    </div>
                </div>

                

                <div class ="box-body">
                    <div class="form-group col-xs-6 {{ $errors->has('razon_social') ? ' has-error' : '' }}">
                        <label for="razon_social" class="col-md-20 control-label">Editar Razón Social</label>
                        <div class="col-md-6 col-md-offset-3">
                        <input type="text" name="razon_social" size="80" class="form-control" value="{{$empresa->razonsocial}}" required maxlength="100">
                        </div>                
                    </div>    
                </div>   

                <div class ="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('direccion') ? ' has-error' : '' }}">
                        <label for="direccion" class="col-md-20 control-label">Editar Dirección</label>
                        <div class="col-md-7 col-md-offset-3">
                        <input type="text" name="direccion" size="50" class="form-control" value="{{$empresa->direccion}}" required maxlength="100">
                        </div>                    
                    </div>    
                </div>   

                <div class ="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('telefono1') ? ' has-error' : '' }}">
                        <label for="telefono1" class="col-md-20 control-label">Editar Teléfono</label>
                        <div class="col-md-8 col-md-offset-3">
                        <input type="text" name="telefono1" size="20" class="form-control" value="{{$empresa->telefono1}}" required maxlength="100">
                        </div>
                    </div>    
                </div> 

                <div class ="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="email" class="col-md-20 control-label">Editar Email</label>
                        <div class="col-md-9 col-md-offset-3">
                        <input type="text" name="email" size="20" class="form-control" value="{{$empresa->email}}" required maxlength="100">
                        </div>
                    </div>    
                </div> 
                
                         

                <div class="box-body">
                    <div class="col-md-10 col-md-offset-5">
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