@extends('contable.mantenimiento_prestamos.base')
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
                    <h3 class="box-title">Crear Nuevo Prestamo</h3>
                </div>
            </div>
        </div>
        <br>

       <form method="post"  action="{{route('mantenimientoprestamos.guardar')}}" >

        {{ csrf_field() }}



        <div class="box-body">
        
             <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="nombre_p" class="col-md-20 control-label">{{trans('contableM.id')}}</label>
                        <input type="number" name="id" size="20" class="form-control" value="{{ old('id_p') }}" required maxlength="100">
                </div>
            </div>

             <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="nombre_p" class="col-md-20 control-label">prestamos</label>
                        <input type="number" name="prestamos" size="20" class="form-control" value="{{ old('id_ct_rh_prestamos_p') }}" required maxlength="100">
                </div>
            </div>

             <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="nombre_p" class="col-md-20 control-label">{{trans('contableM.Anio')}}</label>
                        <input type="number" name="anio" size="20" class="form-control" value="{{ old('anio_p') }}" required maxlength="100">
                </div>
            </div>

             <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="nombre_p" class="col-md-20 control-label">{{trans('contableM.mes')}}</label>
                        <input type="number" name="mes" size="20" class="form-control" value="{{ old('mes_p') }}" required maxlength="100">
                </div>
            </div>

             <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="nombre_p" class="col-md-20 control-label">{{trans('contableM.fecha')}}</label>
                        <input type="number" name="fecha" size="20" class="form-control" value="{{ old('fecha_p') }}" required maxlength="100">
                </div>
            </div>

             <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="nombre_p" class="col-md-20 control-label">cuota</label>
                        <input type="number" name="cuota" size="20" class="form-control" value="{{ old('cuota_p') }}" required maxlength="100">
                </div>
            </div>
            
             <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="nombre_p" class="col-md-20 control-label">valor de la cuota</label>
                        <input type="number" name="valor_cuota" size="20" class="form-control" value="{{ old('valor_cuota_p') }}" required maxlength="100">
                </div>
            </div>

             <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="nombre_p" class="col-md-20 control-label">rol de pagos</label>
                        <input type="number" name="id_ct_rol_pagos" size="20" class="form-control" value="{{ old('id_ct_rol_pagos_p') }}" required maxlength="100">
                </div>
            </div>

             <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="nombre_p" class="col-md-20 control-label">estados</label>
                        <input type="number" name="estado" size="20" class="form-control" value="{{ old('estado_p') }}" required maxlength="100">
                </div>
            </div>

             <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="nombre_p" class="col-md-20 control-label">estado de pago</label>
                        <input type="number" name="estado_pago" size="20" class="form-control" value="{{ old('estado_pago_p') }}" required maxlength="100">
                </div>
            </div>

             <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="nombre_p" class="col-md-20 control-label">fecha de pago</label>
                        <input type="number" name="fecha_pago" size="20" class="form-control" value="{{ old('fecha_pago_p') }}" required maxlength="100">
                </div>
            </div>

             <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="nombre_p" class="col-md-20 control-label">id de usuario</label>
                        <input type="number" name="id_usuariocrea" size="20" class="form-control" value="{{ old('id_usuariocrea_p') }}" required maxlength="100">
                </div>
            </div>

             <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="nombre_p" class="col-md-20 control-label">id usuario mod</label>
                        <input type="number" name="id_usuariomod" size="20" class="form-control" value="{{ old('id_usuariomod_p') }}" required maxlength="100">
                </div>
            </div>

             <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="nombre_p" class="col-md-20 control-label">ip de creación</label>
                        <input type="number" name="ip_creacion" size="20" class="form-control" value="{{ old('ip_creacion_p') }}" required maxlength="100">
                </div>
            </div>

             <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="nombre_p" class="col-md-20 control-label">ip de modificacion</label>
                        <input type="number" name="ip_modificacion" size="20" class="form-control" value="{{ old('ip_modificacion_p') }}" required maxlength="100">
                </div>
            </div>
             <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="nombre_p" class="col-md-20 control-label">creado a las</label>
                        <input type="number" name="created_at" size="20" class="form-control" value="{{ old('created_at_p') }}" required maxlength="100">
                </div>
            </div>

             <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="nombre_p" class="col-md-20 control-label">subido a las</label>
                        <input type="number" name="updated_at" size="20" class="form-control" value="{{ old('updated_at_p') }}" required maxlength="100">
                </div>
            </div>


            <div class="container">
                <div class="form-group col-md-5 ">
                    <button type="submit" class="btn btn-primary btn-sm">{{trans('contableM.guardar')}}</button>
                </div>
            </div>

        </form>

</section>

</html>
@endsection