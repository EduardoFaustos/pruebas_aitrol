@extends('contable.caja.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header"><h3 class="box-title">Escoger </h3>
        </div>
        <div class="box-body">
            <form id="crear_protocolo" method="post" action="{{route('deinfotributaria.store')}}">
                {{ csrf_field() }}
            
                             
                <div class="form-group col-md-6">
                <label for="estado_punto" class="col-md-4 texto">Escoge uno</label>
                <div class="col-md-7">
                    <select id="estado_punto" name="estado_punto" class="form-control" required>
                    <option {{ $caja->estado == 1 ? 'selected' : ''}} value="1">Factura</option>
                    <option {{ $caja->estado == 0 ? 'selected' : ''}} value="0">Nota de Crédito</option>
                    </select>
                </div>
                </div>  
   
                
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                        Agregar 
                        </button>
                    </div>
                </div>   
                
                
            </form>
        </div>
    </div>
</section>



@endsection