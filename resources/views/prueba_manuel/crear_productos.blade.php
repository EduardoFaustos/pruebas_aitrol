@extends('prueba_manuel/base')
@section('action-content')
<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        @page {
            margin: 45px 80px;
        }

        .table>tbody>tr>td {
            padding: 0;
        }

        #paginacion {
            border: 1px solid #CCC;
            background-color: #E0E0E0;
            padding: .5em;
            overflow: hidden;
        }

        .texto {
            font-size: 15.0px;
            font-family: Arial;
        }
    </style>
</head>
<div class="modal fade" id="codigo_proceso" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-sm-11">
                    <h3 class="box-title">Crear Nuevo Producto</h3>
                </div>
            </div>
        </div>
        <br>

       <form method="post"  action="{{route('guardar.manuel')}}" >

        {{ csrf_field() }}



        <div class="box-body">
        
             <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="nombre_p" class="col-md-20 control-label">Nombre del producto</label>
                        <input type="text" name="nombre" size="20" class="form-control" value="{{ old('nombre_p') }}" required maxlength="100">
                </div>
            </div>


            <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="descripcion_p" class="col-md-20 control-label">Descripción</label>
                        <input type="text" name="descripcion" size="20" class="form-control" value="{{ old('descripcion_p') }}" required maxlength="100">
                </div>
            </div>
          
            <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="valor_p" class="col-md-20 control-label">valor</label>
                        <input type="number" name="valor" size="20" class="form-control" value="{{ old('valor_p') }}" required maxlength="100">
                </div>
            </div>



            <div class="container">
                <div class="form-group col-md-5 ">
                    <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
                </div>
            </div>

        </form>

</section>

</html>
@endsection