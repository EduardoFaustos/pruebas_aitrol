@extends('turnero.base')
@section('action-content')
@php
$eleccion = Session::get('session');
@endphp
<style type="text/css">
    td,
    th {
        font-weight: bold;
        padding: 4px !important;
        text-align: left;
    }

    .text {
        font-size: 15px;
        font-family: helvetica;
        font-weight: bold;
        color: red;
        text-transform: uppercase;
    }

    .parpadea {

        animation-name: parpadeo;
        animation-duration: 4s;
        animation-timing-function: linear;
        animation-iteration-count: infinite;

        -webkit-animation-name: parpadeo;
        -webkit-animation-duration: 1s;
        -webkit-animation-timing-function: linear;
        -webkit-animation-iteration-count: infinite;
    }

    @-moz-keyframes parpadeo {
        0% {
            opacity: 1.0;
        }

        50% {
            opacity: 0.0;
        }

        100% {
            opacity: 1.0;
        }
    }

    @-webkit-keyframes parpadeo {
        0% {
            opacity: 1.0;
        }

        50% {
            opacity: 0.0;
        }

        100% {
            opacity: 1.0;
        }
    }

    @keyframes parpadeo {
        0% {
            opacity: 1.0;
        }

        50% {
            opacity: 0.0;
        }

        100% {
            opacity: 1.0;
        }
    }
</style>
<div class="modal fade" id="crear_trasportista" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="width: 80%;">
        </div>
    </div>
</div>
<section class="content">
    <input type="hidden" id="eleccion" value="{{$eleccion}}">
    <div class="box">
        <div class="box-header">
            <div class="row" style="text-align: center;">
                <label style="font-size: 30px;">Administrar turnos</label>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="row">
                <div class="col-md-12" style="text-align: right">
                    <a class="btn btn-success" href="{{route('turnero.excel_buscarturnero')}}">Reporte</a>
                </div>
                <div class="col-md-10">
                    <label style="font-size: 20px;">Estado de Turnos</label>
                </div>

                <div class="col-md-2">
                    <label style="font-size: 20px;">@if($eleccion == 1) MÓDULO 1 @elseif($eleccion == 2) MÓDULO 2 @elseif($eleccion == 3) MÓDULO 3 @elseif($eleccion == null) MÓDULO VACIO @ENDIF</label>
                </div>
            </div>
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                <div class="row">
                    <div class="table-responsive col-md-12">
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                                <tr>

                                    <th style="width: 14,28%;">{{trans('turnerof.hour')}}sssssssssss</th>
                                    <th style="width: 14,28%;">{{trans('turnerof.number')}}</th>
                                    <th style="width: 14,28%;">Seguro</th>
                                    <th style="width: 14,28%;">{{trans('turnerof.patient')}}</th>
                                    <th style="width: 14,28%;">{{trans('turnerof.identification')}}</th>
                                    <th style="width: 14,28%;">{{trans('turnerof.module')}}</th>
                                    <th id="esconder" style="width: 14,28%;">{{trans('turnerof.state')}}</th>
                                    <th id="esconder" style="width: 14,28%;">Tiempo de Espera</th>
                                    <th style="width: 14,28%;">{{trans('turnerof.advance')}}</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($turnero as $value)
                                @php
                                $FechaActual = new DateTime();
                                $FechaTurno = new DateTime($value->created_at);
                                $dateDifference = $FechaActual->diff($FechaTurno);
                                @endphp
                                <tr>
                                    <th>{{substr($value->created_at,10,15)}}</th>
                                    <th>{{strtoupper(substr($value->letraproc, 0, 1))}} - {{$value->turno}}</th>
                                    <th>@if(isset($value->paciente)) @if(isset($value->paciente->agenda->last()->seguro)){{$value->paciente->agenda->last()->seguro->nombre}} @endif @endif</th>
                                    <th>@if(isset($value->paciente) || !is_null($value->paciente)) {{$value->paciente->apellido1}} {{$value->paciente->apellido2}} {{$value->paciente->nombre1}} {{$value->paciente->nombre2}} @else {{$value->cedula}} @endif</th>
                                    <th>{{$value->cedula}}</th>
                                    <th>@if(is_null($value->modulo)) No atendido @elseif(!is_null($value->modulo)) Módulo {{$value->modulo}} @endif</th>
                                    <th>@if(is_null($value->modulo))<button value="{{$value->id}}" id="atender{{$value->id}}" onclick="accion(this.value)" class="btn btn-warning ">Atender</button> @else <button type="button" onclick="location.reload()" class="btn btn-success">Atendidose</button> @endif</th>
                                    <th> @if($dateDifference->i < 15 ) @elseif($dateDifference->h == 0) <span id="esconder{{$value->id}}" style="visibility: visible; " class="parpadea text">{{$dateDifference->i}} MINUTOS DE ESPERA </span> @else <span id="esconder{{$value->id}}" style="visibility: visible; " class="parpadea text">{{$dateDifference->h}} HORA Y {{$dateDifference->i}} MINUTOS DE ESPERA </span> @endif</th>
                                    <th style="text-align: center;"><button id="avanzar{{$value->id}}" value="{{$value->id}}" style="@if(!is_null($value->modulo)) @if($value->modulo != $eleccion) visibility: hidden; @endif @else visibility: hidden; @endif margin-left:22%" onclick="avanzar_turno(this.value)" class="btn btn-danger">Avanzar Turno</button> </span></th>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-5">
                        <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($turnero->currentPage() - 1) * $turnero->perPage())}} / {{count($turnero) + (($turnero->currentPage() - 1) * $turnero->perPage())}} de {{$turnero->total()}} registros
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                            {{$turnero->appends(Request::only(['id','nombre']))->links() }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="myModal" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" style="text-align: center;">Comprobación de Módulo</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="eleccion">Elección de Módulo</label>

                                </div>
                                <div class="col-md-6">
                                    <select name="eleccion" id="eleccion2" class="form-control">
                                        <option value="1">Módulo 1</option>
                                        <option value="2">Módulo 2</option>
                                        <option value="3">Módulo 3</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" onclick="enviar()" data-dismiss="modal">Guardar</button>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>
<script type="text/javascript">
    $(window).on('load', function() {
        var eleccion = document.getElementById("eleccion").value;
        if (eleccion == '' || eleccion == null) {
            $('#myModal').modal({
                backdrop: 'static',
                keyboard: false
            })
            $('#myModal').modal('show');
        }
    });

    function accion(id) {
        $.ajax({
            url: "{{route('verficacion_turno')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                'cedula': id,
            },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                console.log(data);
                if (data == 'ok') {
                    Swal.fire({
                        title: 'Confirma la acción?',
                        showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonText: `Ok`,
                        denyButtonText: `Cancelar`,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{route('turnero_cambio_estado')}}",
                                headers: {
                                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                                },
                                data: {
                                    "id": id,
                                },
                                type: 'get',
                                dataType: 'json',
                                success: function(data) {
                                    document.getElementById("esconder" + id).style.visibility = "hidden";
                                    document.getElementById("atender" + id).disabled = true;
                                    document.getElementById("avanzar" + id).style.visibility = "visible";

                                },
                                error: function(xhr, status) {
                                    alert('Existió un problema');
                                    //console.log(xhr);
                                },
                            });
                            Swal.fire('Saved!', '', 'success')
                        } else if (result.isDenied) {
                            Swal.fire('Los cambios no se veran reflejados')
                        }
                    }, )
                } else {
                    location.reload();
                }
            },
            error: function(xhr, status) {
                alert('Existió un problema');
            },
        });
    }

    function enviar() {
        var opc = document.getElementById("eleccion2").value;
        console.log(opc);
        $.ajax({
            url: "{{route('guardar_cache')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                "eleccion": opc,
            },
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#myModal').modal({
                    backdrop: 'static',
                    keyboard: true
                })
                location.reload();
            },
            error: function(xhr, status) {
                alert('Existió un problema');
                //console.log(xhr);
            },
        });
    }

    function avanzar_turno(id) {
        $.ajax({
            url: "{{route('turnero_finalizar')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                "id": id,
            },
            type: 'get',
            dataType: 'json',
            success: function(data) {
                if (data == 'ok') {
                    location.reload();
                }
            },
            error: function(xhr, status) {
                alert('Existió un problema');
                //console.log(xhr);
            },
        });
    }
</script>
@endsection