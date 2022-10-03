@extends('riesgo_caida.base')
@section('action-content')
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-sm-10">
                    <h3 class="box-title">{{trans('tecnicof.stretchers')}}:</h3>
                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn btn-danger btn-gray" onclick="regresar()">{{trans('tecnicof.return')}}</button>
                </div>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="col-md-12">
                <div class="row">
                    <form method="POST" id="form" action="#">
                        {{ csrf_field() }}
                        <div class="form-group col-md-6">
                            <label for="fecha" class="col-md-4 texto">{{trans('tecnicof.datefrom')}}</label>
                            <div class="col-md-5">
                                <input style="text-align: center;line-height:10px;" type="date" onchange="tabla()" name="desde" id="desde" class="form-control">
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="fecha" class="col-md-4 texto">{{trans('tecnicof.dateto')}}</label>
                            <div class="col-md-5">
                                <input style="text-align: center;line-height:10px;" type="date" onchange="tabla()" name="hasta" id="hasta" class="form-control">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                <div class="row">
                    <div class="col-sm-12">
                        <table id="tabla" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                            <thead style="background-color: #337ab7;color:white;">
                                <tr role="row" id="cabezera">
                                    <th>{{trans('tecnicof.bed')}}</th>
                                    <th>{{trans('tecnicof.patientname')}}</th>
                                    <th>{{trans('tecnicof.hospital')}}</th>
                                    <th>{{trans('tecnicof.from')}}</th>
                                    <th>{{trans('tecnicof.to')}}</th>
                                    <th>{{trans('tecnicof.bedstatus')}}</th>
                                    <th>{{trans('tecnicof.download')}}</th>
                                </tr>
                            </thead>
                            <tbody id="cuerpo">
                                @php $cont = 1; @endphp
                                @foreach($registro as $val)
                                @php
                                $paciente = Sis_medico\Camilla_Gestion::where('id',$val->id_camagestion)->first();
                                $nombre = Sis_medico\Paciente::where('id',$paciente->id_paciente)->first();
                                $edad = $nombre->fecha_nacimiento;
                                $edad2 = new DateTime($edad);
                                $fecha= date("Y-m-d");
                                $fecha1 = new DateTime($fecha);
                                $edad_nueva = $fecha1->diff($edad2);
                                $edad1 = $edad_nueva->y;
                                $cama = Sis_medico\Camilla::where('id',$paciente->camilla)->first();
                                $hospital = Sis_medico\Hospital::where('id',2)->first();
                                @endphp
                                <tr style="text-align: center;">
                                    <td>{{$cont}}</td>
                                    <td>@if(empty($nombre)) @else {{$nombre->nombre1}} {{$nombre->nombre2}} {{$nombre->apellido1}} {{$nombre->apellido2}} @endif</td>
                                    <td>{{$hospital->nombre_hospital}}</td>
                                    <td>{{$val->created_at}}</td>
                                    <td>{{$val->updated_at}}</td>
                                    <td @if($val->estado == 0 ) style="text-align:center;color:white;background:cadetblue" @else style="text-align:center;color:white;background:red" @endif>@if($val->estado == 0 ) {{trans('tecnicof.high')}} @else {{trans('tecnicof.process')}} @endif</td>
                                    <td>@if (($edad1 >=13))
                                        <a href="{{route('riesgo.pdf',['id'=>$paciente->id_agenda])}}" target="_blank" class="btn btn-danger btn-gray"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>
                                        @elseif(($edad1 < 13)) <a href="{{route('riesgo_menor.pdf',['id'=>$paciente->id_agenda])}}" target="_blank" class="btn btn-danger btn-gray"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>
                                            @endif
                                    </td>
                                </tr>
                                @php $cont ++; @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    function regresar() {
        var url = "{{route('camilla.index')}}";
        window.location = url;
    }

    function tabla() {
        $.ajax({
            url: "{{route('riesgo.tabla')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: $('#form').serialize(),
            type: 'GET',
            dataType: 'html',
            success: function(datahtml, data) {
                console.log(data);
                $("#tabla").html(datahtml);

            },
            error: function(xhr, status) {
                alert('Existió un problema');
                //console.log(xhr);
            },
        });
    }
    window.addEventListener('load', function() {

        $('#tabla').DataTable({
            'paging': false,
            'lengthChange': true,
            'searching': true,
            'ordering': true,
            'info': false,
            'autoWidth': false,
            'sInfoEmpty': true,
            'sInfoFiltered': true,
            'language': {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            }
        });
    });
</script>
@endsection