<!-- CONTINUA AQUI NANO -->
@extends('ordenes_gestion.base')
@section('action-content')
<style>
    th,
    td {
        text-align: center;
    }
    
</style>

<!--Empieza codigo modal-->

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<div class="modal fade" id="modificar_registro" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<!--Termina codigo modal-->

<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="col-md-12 col-lg-12">
                <div class="col-md-10 col-lg-9">
                    <h4 style="text-align: left;">Gestión de Ordenes Medicas</h4>
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <form method="POST" id="form" action="{{route('gestionarorden.index')}}">
                        {{ csrf_field() }}
                        <div class="form-group col-md-4">
                            <label for="fecha" class="col-md-3 texto">Fecha Desde</label>
                            <div class="col-md-5">
                                <input style="text-align: center;line-height:10px;" type="date" name="desde" id="desde" class="form-control" value="{{$desde}}">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="fecha" class="col-md-3 texto">Fecha Hasta</label>
                            <div class="col-md-5">
                                <input style="text-align: center;line-height:10px;" type="date" name="hasta" id="hasta" class="form-control" value="{{$hasta}}">
                            </div>
                        </div>
                        <!--Insertando nombre del paciente-->
                        <div class="form-group col-md-4{{ $errors->has('id') ? ' has-error' : '' }}">
                            <label for="id" class="col-md-3 control-label">Nombre</label>
                            <div class="col-md-5">
                                <input id="id" type="text" class="form-control input-sm" name="id" value="{{$nombre_paciente}}" autofocus>
                                @if ($errors->has('id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <!--aqui termina para el buscador de nombre del paciente -->
                        <div class="form-group col-md-3">
                            <button type="submit" id="buscar" class="btn btn-primary">BUSCAR</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                <div class="row" id="listado">
                    <div class="table-responsive col-md-12">
                        <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 8%;">Fecha Orden</th>
                                    <th style="width: 15%;">Paciente</th>
                                    <th style="width: 20%;">Doctor</th>
                                    <th style="width: 8;">Orden</th>
                                    <th style="width: 15%;">Procedimientos</th>
                                    <!--th style="width: 20%;">Estado</th-->
                                    <!--th style="width: 20%;">Grupo de Procedimientos<th-->
                                    <!--<th style="width: 20%;">Fecha</th>-->
                                    <th style="width: 20%;">Grupo Procedimientos</th>
                                    <th style="width: 10%;">Estado de la Orden</th>
                                    
                                    <th style="width: 20%;">Acciones</th>
                                </tr>
                            </thead>
                            @foreach($ordenes as $value)
                            @php
                            $lopez="";
                            $nombreProc = [];
                            $tipoProcedimiento = Sis_medico\Orden_Procedimiento::where('id_orden_tipo',$value->tiporden)->get();
                            //dd($tipoProcedimiento);
                            //dd($procedi);
                            foreach($tipoProcedimiento as $val){
                            $nombreProce=Sis_medico\Procedimiento::where('id',$val->id_procedimiento)->get();
                            //dd($nombreProce);
                            foreach($nombreProce as $nom_pro){
                            $lopez = $nom_pro->nombre."+".$lopez;
                            }
                            }
                            $paciente = Sis_medico\User::where('id',$value->id_paciente)->first();
                            $doctor = Sis_medico\User::where('id',$value->id_doctor)->first();
                            $fecha_orden = $value->fecha_orden;
                            //dd($value);
                            @endphp

                            <tr>
                                <td>{{ substr($fecha_orden,0,10) }}</td>
                                <!--<td>{{$value->fecha_orden}}</td>-->
                                <!--<td>{{$value->id_paciente}}</td>-->
                                <td>@if(empty($paciente)) @else{{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}} {{$paciente->apellido2}}@endif</td>
                                <td>@if(empty($doctor)) @else{{$doctor->nombre1}} {{$doctor->nombre2}} {{$doctor->apellido1}} {{$doctor->apellido2}}@endif</td>
                                <td>{{$value->id}}</td>
                                <td>@if(($value->tipo_procedimiento)==0) Endoscopico @elseif(($value->tipo_procedimiento)==1) Funcional @elseif(($value->tipo_procedimiento)==2) Imagenes @endif</td>
                                <!-- Leer el dato del array push -->
                                <td>{{$lopez}}</td>
                                <th>@if(($value->estado)==0) Por gestionar @elseif(($value->estado)==1)Enviado al Seguro @endif</th>
                                <td>
                                    <a href="{{ route('imprimir.ordenes_hc3',['id' => $value->id])}}" class="btn btn-success btn-gray" target="_blank">
                                        <i class="glyphicon glyphicon-download-alt"></i>
                                    </a>
                                    <!-- Boton Modal-->
                                    <div>
                                        <a href="{{route('gestionarorden.editar_gestion', ['id'=>$value->id])}}" class="btn btn-warning btn-gray" data-toggle="modal" data-target="#modificar_registro">
                                            <i class="glyphicon glyphicon-edit"></i>
                                        </a>
                                    </div>
                                    <!--Termina boton modal-->
                                </td>
                            </tr>
                            @endforeach
                        </table>
                        
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($ordenes->currentPage() - 1) * $ordenes->perPage())}} / {{count($ordenes) + (($ordenes->currentPage() - 1) * $ordenes->perPage())}} de {{$ordenes->total()}} registros
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                    {{$ordenes->appends(['tipo'=>$tipo,'desde'=>$desde,'hasta'=>$hasta,'id'=>$nombre_paciente])->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $("#body2").addClass('sidebar-collapse');
</script>
@endsection