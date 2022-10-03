@extends('contable.ventas.reporte.base')
@section('action-content')
<link rel="stylesheet" href="css/stilo.css" />
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" rel="stylesheet" />
<style>
    .abs-center {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 0vh;
    }

    .form {
        width: 0px;
    }
</style>
<section class="content">
    <div class="box">

        <form action="{{route('estadistica.formulario')}}" method="POST">
            {{ csrf_field() }}
            <div class="panel-heading">Lista de Datos</div>
            <div class="abs-left">

                <form action="#" class="border p-3 form">

                    <div class="panel-heading" style="text-align: right;">
                        <button style="position: right;top: 0%;" type="submit" class="btn btn-primary">Ingreso de Datos</button>
                    </div>
                </form>
            </div>

            <div class="panel-heading">Buscar</div>
            <div class="container">
                <div class="col-md-3">
                    <div class="row">

                        <div class="panel-body">
                            <label class="label-control">Apellido</label>
                            <input type="text" name="" class="form-control" placeholder="ingresar el apellido del cliente" required="required">
                            <br>
                        </div>

                    </div>
                </div>
            </div>
            <div class="container">
                <div class="col-md-3">
                    <div class="row">
                        <div class="panel-body">
                            <label class="label-control">Tipo</label>
                            <select class="form-control" name="seguros" id="seguros">
                                @foreach($seguro as $value)
                                <option value="{{$value->id}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="abs-left">

                    <form action="#" class="border p-3 form">

                        <button type="submit" class="btn btn-primary">Buscar</button>
                    </form>
                </div>

            </div>
            <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                <thead>
                    <tr class='well-dark'>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">id_empresa</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">nro_comprobante</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">id_cliente</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">ruc_cliente</th>
                        <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Telefono</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">email</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Nombre</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Procedimiento</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Anulado por</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Estado</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ct_ventas as $value)
                    <tr class="well">
                        <td>{{$value->id_empresa}}</td>
                        <td>{{$value->nro_comprobante}}</td>
                        <td>{{$value->id_cliente}}</td>
                        <td>{{$value->direccion_cliente}}</td>
                        <td>{{$value->ruc_id_cliente}}</td>
                        <td>{{$value->telefono_cliente}}</td>
                        <td>{{$value->email_cliente}}</td>
                        <td>{{$value->nombre_cliente}}</td>
                        <td>{{$value->procedimientos}}</td>
                    </tr>

                    @endforeach
                </tbody>
                <tfoot>
                </tfoot>
            </table>
        </form>
        <div class="row">
            <div class="col-sm-5">
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($ct_ventas->currentPage() - 1) * $ct_ventas->perPage())}} / {{count($ct_ventas) + (($ct_ventas->currentPage() - 1) * $ct_ventas->perPage())}} de {{$ct_ventas->total()}} registros</div>
            </div>
            <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                    {{ $ct_ventas->appends(Request::only(['id', 'apellidos', 'nombres']))->links() }}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection