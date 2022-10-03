@extends('vistan.base')
@section('action-content')

<style>
    .form-control {
        border-radius: 7px;
    }

    .dropdown-menu li a {
        font-size: 15px;
        color: white;

    }
</style>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<section class="content">
    <!-- <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="#">Compras</a></li>
            <li class="breadcrumb-item active" aria-current="page">Registro<a href="#">
        </ol>
    </nav> -->
    <div class="box">
        <div class="box-header header_new">
            <!-- <div class="col-md-7">
                <h3 class="box-title">FLEX</h3>
            </div> -->

            {{-- <div class="col-md-2 text-right">
                <a href="#" class="btn btn-success btn-gray">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i>Nuevo pedido
                </a>
            </div>

            <div class="col-md-2 text-left">
                <a href="#" class="btn btn-success btn-gray">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i>Agrupar Pedidos
                </a>
            </div>  --}}
        </div>

        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" for="title">DATOS A BUSCAR</label>
            </div>
        </div>

        <div class="box-body dobra">
            <form method="POST" id="form_nuevo" action="#">
                {{csrf_field()}}
                <div class="form-group col-md-2 col-xs-2">
                    <label class="texto" form="id">Numero de Cédula: </label>
                </div>
                <div class="form-group col-md-3 col-xs-10 container-4">
                    <input class="form-control" type="text" id="id" name="id" value="" placeholder="Ingrese su número de cédula..." />
                </div>

        <div class="col-md-6 col-xs-6 col-xs-10 container-4">
            <button type="submit" id="buscarEmpleado" class="btn btn-primary btn-gray">
                <span class="glyphicon glyphicon-search" aria-hidden="true"></span> BUSCAR
            </button>
        </div>
        </form>
    </div>

    <div class="row head-title">
        <div class="col-md-12 cabecera">
            <label class="color_texto">DATOS DEL PACIENTE</label>
        </div>
    </div>

    <div class="box-body dobra">
        <div class="form-group col-md-12">
            <div class="form-row">
                <div id="resultados">
                </div>
                <div id="contenedor">
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                        <div class="row">
                            <div class="col-md-12">
                                <table id="tabla" class="table table-hover dataTable" role="grid" aria-describedby="example_info">
                                    <thead>
                                        <tr class='well-dark'>
                                            <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Apellidos</th>
                                            <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Nombres</th>
                                            <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Ciudad</th>
                                            <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Dirección</th>
                                            <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Estado Civil</th>
                                            <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Sexo</th>
                                            <th width="40%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Historial Clinico</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($paciente as $value)
                                            <tr >
                                                <td>{{$value->apellido1.'  '.$value->apellido2}}</td>
                                                <td>{{$value->nombre1.'  '.$value->nombre2}}</td>
                                                <td>{{$value->ciudad}}</td>
                                                <td>{{$value->direccion}}</td>
                                                <td>@if(($value->estadocivil)==1) Soltero @elseif(($value->estadocivil)==2) Casado @elseif(($value->estadocivil)==3) Viudo @elseif(($value->estadocivil)==4) Divorciado @elseif(($value->estadocivil)==5) Unión Libre  @elseif(($value->estadocivil)==6) Unión de Hecho @endif</td>
                                                <td>@if(($value->sexo)==1) Hombre @elseif(($value->sexo)==2) Mujer @endif</td>
                                                <td>{{$value->historia_clinica}}</td>
                                                
                                                {{-- <td>{{$value->telefono1.' / '.$value->telefono2.' /'.$value->telefono3 }}</td> --}}
                                                {{-- <td>{{$value->nombre1}} @if($value->nombre2!='(N/A)'){{$value->nombre2}}@endif {{$value->apellido1}} @if($value->apellido2!='(N/A)'){{$value->apellido2}}@endif</td> --}}
                                                {{-- <!-- <td>{{$value->estadocivil}}</td> --> --}}
                                                {{-- <td>{{substr($value->created_at,0,10) }}</td> --}}

                                            <!-- <div class="btn group" style="width:100%;">
                                                <button style="width:50%;" type="button" class="btn btn-success btn-xs"><span style="font-size: 12px;">ACCIONES</span></button>
                                                <button style="width:10%;" type="button" class="btn btn-success btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false" style="padding-left: 2px;padding-right: 2px">
                                                    <span class="caret"></span>
                                                    <span class="sr-only">Toogle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu cot" role="menu" style="background-color: #00a65a;padding: 2px;min-width: 80px;">
                                                    <li><a href="#" target="_blank"> <i class="fas fa-plus"></i> Factura</a></li>
                                                    <li><a href="#" target="_blank"> <i class="fas fa-plus"></i> Recibo</a></li>
                                                    <li><a href="#" target="_blank"> <i class="fas fa-plus"></i> Orden</a></li>
                                                    <li><a href="#" target="_blank"> <i class="fas fa-plus"></i> LIQ/SENAE</a></li>
                                                    <li><a href="#" target="_blank"><i class="far fa-eye"></i> View</a></li>
                                                    <li><a href="#" target="_blank">Resumen</a></li>
                                                    <li><a href="#" target="_blank">Pdf</a></li>
                                                    <li><a href="#" target="_blank">Excel</a></li>
                                                    <li><button onclick="kardex();" style="width: 78%;" type="button" class="btn btn-warning" target="_blank">Kardex</button></li>
                                                    <li><a class="btn btn-danger" href="" target="_blank"> Eliminar</a></li>
                                            </div> -->
                                            </tr>
                                        @endforeach
                                        </td>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($paciente->currentPage() - 1) * $paciente->perPage())}} / {{count($paciente) + (($paciente->currentPage() - 1) * $paciente->perPage())}} de {{$paciente->total()}} registros</div>
                            </div>
                            <div class="col-sm-7">
                                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                {{ $paciente->appends(Request::only([]))->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>

@endsection