@extends('riesgo_caida.base')
@section('action-content')

<style>
    .stilos:focus {

        background: whitesmoke;
    }

    #estilos:hover {
        color: blanchedalmond;
    }
</style>

<div class="modal fade" id="documentos_riesgo_caida" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<div class="modal fade" id="documentos_riesgo_cambio" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<div class="modal fade" id="documentos_cambio_estado" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-xs" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<div class="modal fade" id="ocupar_sala" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-xs" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="box-title">{{trans('tecnicof.stretchers')}} :</h3>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary" id="estilos" target="_blank" onclick="registro()"><i class="fa fa-book" aria-hidden="true"></i> {{trans('tecnicof.register')}}
                    </button>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary" data-remote="{{ route('camilla.ocupar_por_sala')}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#ocupar_sala"></i>  {{trans('tecnicof.room')}}
                    </button>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary" onclick="masivo()" class="btn btn-info btn-sm"></i> {{trans('tecnicof.massive')}}
                    </button>
                </div>

            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <form method="POST" id="form" action="#">
                        {{ csrf_field() }}
                        <div class="col-md-2">
                            <div class="col">
                                <label for="fecha" class="texto">{{trans('tecnicof.hospital')}}</label>
                            </div>
                            <div class="col">
                                <select name="hospital_tipo" disabled id="hospital_tipo" class="form-control stilos" value="@if(isset($searchingVals)){{$searchingVals['nombre_camilla']}}@endif" onchange="camas()">
                                    <option id="valor" value="2">Pentax</option>
                                    @foreach($hospital as $value)
                                    @if($value->id != 2)
                                    <option @if($request->hospital_tipo==$value->id) selected="selected" @endif value="{{$value->id}}">{{$value->nombre_hospital}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-10" id="camas">
                            <div class="col-md-12">
                                @foreach($cuadro as $value)
                                @php
                                $camita = $estado = Sis_medico\Camilla_Gestion::where('camilla',$value->id)->where('num_atencion',1)->first();
                                @endphp
                                @if(empty($camita->estado_uso))
                                <a target="_blank" class="btn btn-primary" style="margin: 8px;" href="{{route('background_estado',[$value->id])}}">
                                    <div id="cuadrado" style="margin-left:8px;padding:2px;">
                                        <span style="font-weight: bold;font-size:10px;">{{$value->nombre_camilla}}</span>
                                        <img src="{{asset('/')}}hc4/img/hospital_disponible.png" alt="">
                                    </div>
                                </a>
                                @elseif(($camita->estado_uso)==2)
                                <a target="_blank" class="btn btn-primary" style="margin: 8px;" href="{{route('background_estado',[$value->id])}}">
                                    <div id="cuadrado" style="margin-left:8px;padding:2px;">
                                        <span style="font-weight: bold;font-size:10px;">{{$value->nombre_camilla}}</span>
                                        <img src="{{asset('/')}}hc4/img/hospital_ocupada.png" alt="">

                                    </div>
                                </a>
                                @elseif(($camita->estado_uso)==3)
                                <a target="_blank" class="btn btn-primary" style="margin: 8px;" href="{{route('background_estado',[$value->id])}}">
                                    <div id="cuadrado" style="margin-left:8px;padding:2px;">
                                        <span style="font-weight: bold;font-size:10px;">{{$value->nombre_camilla}}</span>
                                        <img src="{{asset('/')}}hc4/img/hospital_preparacion.png" alt="">
                                    </div>
                                </a>
                                @elseif(($camita->estado_uso)==1)
                                <a target="_blank" class="btn btn-primary" style="margin: 8px;" href="{{route('background_estado',[$value->id])}}">
                                    <div id="cuadrado" style="margin-left:8px;padding:2px;">
                                        <span style="font-weight: bold;font-size:10px;">{{$value->nombre_camilla}}</span>

                                        <img src="{{asset('/')}}hc4/img/hospital_disponible.png" alt="">

                                    </div>
                                </a>
                                @elseif(($camita->estado_uso)==4)
                                <a target="_blank" class="btn btn-primary" style="margin: 8px;" href="{{route('background_estado',[$value->id])}}">
                                    <div id="cuadrado" style="margin-left:8px;padding:2px;">
                                        <span style="font-weight: bold;font-size:10px;">{{$value->nombre_camilla}}</span>

                                        <img src="{{asset('/')}}hc4/img/simple_block.png" alt="">

                                    </div>
                                </a>
                                @endif
                                @endforeach
                            </div>
                        </div>
                    </form>
                </div>
                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="example" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                <thead style="background-color: #337ab7;color:white;">
                                    <tr role="row" id="cabezera">
                                        <th>{{trans('tecnicof.bed')}}</th>
                                        <th>{{trans('tecnicof.hospital')}}</th>
                                        <th>{{trans('tecnicof.status')}}</th>
                                        <th>{{trans('tecnicof.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody id="cuerpo">
                                    @foreach($camilla as $value)
                                    @php
                                    $nombre = Sis_medico\Hospital::where('id',$value->id_hospital)->first();
                                    $estado = Sis_medico\Camilla_Gestion::where('camilla',$value->id)->whereBetween('estado_uso',[0, 4])->where('num_atencion','<>','0')->first();
                                        $fecha_hoy = date("d-m-Y");

                                        @endphp
                                        <tr>
                                            <td>
                                                {{$value->nombre_camilla}}
                                            </td>
                                            <td>
                                                {{$nombre->nombre_hospital}}
                                            </td>
                                            <td style="text-align:center;color:white;" @if(empty($estado->estado_uso)) bgcolor='#2ECC71' @elseif(($estado->estado_uso)==1) bgcolor='#2ECC71' @elseif(($estado->estado_uso)==3) bgcolor='#FEE34A' @elseif(($estado->estado_uso)==4) bgcolor='#D516EF' @elseif(($estado->estado_uso)==2) bgcolor='#F41717' @endif>@if(empty($estado->estado_uso)) LIBRE @elseif(($estado->estado_uso)==1) LIBRE @elseif(($estado->estado_uso) == 2) OCUPADO @elseif(($estado->estado_uso) == 3) PREPARACIÒN @elseif(($estado->estado_uso) == 4) DESINFECCIÒN @endif
                                            </td>
                                            <td style="text-align: center;">
                                                @if(empty($estado->estado_uso))
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <a class="btn btn-primary btn-xs agbtn" data-remote="{{ route('riesgo_caida.modal',[$value->id])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#documentos_riesgo_caida"> {{trans('tecnicof.form')}}</a>
                                                @elseif(($estado->estado_uso)==1)
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <a class="btn btn-primary btn-xs agbtn" data-remote="{{ route('riesgo_caida.modal',[$value->id])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#documentos_riesgo_caida"> {{trans('tecnicof.form')}}</a>
                                                @elseif(($estado->estado_uso)==2)
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <a class="btn btn-primary btn-xs agbtn" data-remote="{{ route('riesgo_cambio.modal',[$value->id])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#documentos_riesgo_cambio">{{trans('tecnicof.change')}}</a>
                                                @elseif(($estado->estado_uso)== 3 || 4 )
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <a class="btn btn-primary btn-xs agbtn" data-remote="{{ route('riesgo_cambio.modal_estado',[$value->id])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#documentos_cambio_estado">{{trans('tecnicof.change')}}</a>
                                                @endif
                                            </td>
                                        </tr>


                                        @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('tecnicof.showing')}} {{1 + (($camilla->currentPage() - 1) * $camilla->perPage())}} / {{count($camilla) + (($camilla->currentPage() - 1) * $camilla->perPage())}} {{trans('tecnicof.of')}} {{$camilla->total()}} {{trans('tecnicof.records')}}
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                {{$camilla->appends(Request::only(['id','nombre']))->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>
<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2({
            tags: true
        });

        function actualizar() {
            $.ajax({
                url: "{{route('camilla.comprobar_sesion')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#hospital_tipo').serialize(),
                type: 'GET',
                dataType: 'html',
                success: function(data) {
                    console.log(data);
                },
                error: function(xhr, status) {
                    Swal.fire('Sesion Expirada!', '', 'error');
                    location.reload();
                },
            });
        }

        setInterval(actualizar, 25000);
    });

    jQuery(document).ready(function() {
        jQuery('#documentos_riesgo_caida').on('hidden.bs.modal', function(e) {
            jQuery(this).removeData('bs.modal');
            jQuery(this).find('.modal-content').empty();
        })
    })
    jQuery(document).ready(function() {
        jQuery('#documentos_riesgo_cambio').on('hidden.bs.modal', function(e) {
            jQuery(this).removeData('bs.modal');
            jQuery(this).find('.modal-content').empty();
        })
    })

    function camas() {
        $.ajax({
            url: "{{route('camas_estado')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: $('#hospital_tipo').serialize(),
            type: 'GET',
            dataType: 'html',
            success: function(datahtml, data) {
                $("#camas").html(datahtml);
                $.ajax({
                    url: "{{route('camas_estado_hab')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    data: $('#hospital_tipo').serialize(),
                    type: 'GET',
                    dataType: 'html',
                    success: function(datahtml, data) {
                        $("#cuerpo").html(datahtml).fadeIn();
                    },
                    error: function(xhr, status) {
                        alert('Existió un problema en la Tabla');
                        //console.log(xhr);
                    },
                });
            },
            error: function(xhr, status) {
                alert('Existió un problema en la Cama');
                //console.log(xhr);
            },
        });
    }

    function registro() {
        var url = '{{url("gestion/camilla/registro")}}';
        window.location = url;

    }

    function masivo() {

        Swal.fire({
            title: 'Quiere hacer el masivo?',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: 'Si',
            denyButtonText: `No`,
        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({
                    url: "{{route('camilla.actualizar_masivo')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    data: $('#hospital_tipo').serialize(),
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data == 'ok') {
                            Swal.fire('Saved!', '', 'success')
                            location.reload();
                        } else {
                            Swal.fire('Error!', '', 'error')
                            location.reload();
                        }
                    },
                    error: function(xhr, status) {
                        alert('Existió un problema');
                        //console.log(xhr);
                    },
                });
            } else if (result.isDenied) {
                Swal.fire('OK!', '', 'error')
            }
        })
    }
</script>
@endsection