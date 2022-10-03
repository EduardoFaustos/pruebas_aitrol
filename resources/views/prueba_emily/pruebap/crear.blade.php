@extends('prueba_emily/pruebap/base')
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

       <form method="post"  action="{{route('guardarpro')}}" >

        {{ csrf_field() }}



        <div class="box-body">
        
             <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="nombrep" class="col-md-20 control-label">Codigo</label>
                        <input type="text" name="codigo" size="20" class="form-control" value="{{ old('codigop') }}" required maxlength="100">
                </div>
            </div>

         

             <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="nombrep" class="col-md-20 control-label">Nombre</label>
                        <input type="text" name="nombre" size="20" class="form-control" value="{{ old('nombrep') }}" required maxlength="100">
                </div>
            </div>


            <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="descripcionp" class="col-md-20 control-label">Descripcion</label>
                        <input type="text" name="descripcion" size="20" class="form-control" value="{{ old('descripcionp') }}" required maxlength="100">
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