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
                    <h3 class="box-title">Actualizacion del producto</h3>
                </div>
            </div>
        </div>
        <br>
        <form action="{{route('actualizar.manuel')}}" method="POST">
            {{ csrf_field() }}
              <input type="hidden" value="{{$productos->id}}" name="id_producto">           
            <div class="box-body">
            <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="nombre_p" class="col-md-20 control-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="{{ $productos->nombre }}">
                    </div>
            </div>

            <div class="box-body">
            <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="descripcion_p" class="col-md-20 control-label">Descripcion</label>
                        <input type="text" name="descripcion" class="form-control" value="{{ $productos->descripcion}}">
                </div>
            </div>

            <div class="box-body">
            <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="valor_p" class="col-md-20 control-label">valor</label>
                        <input type="number" name="valor" class="form-control" value="{{ $productos->valor}}">
                </div>
            </div>

            <div class="container">
                <div class="form-group col-md-5">
                    <button type="submit" class="btn btn-primary btn-sm">Actualizar</button>
                </div>
            </div>


        </form>

</section>

</html>

@endsection