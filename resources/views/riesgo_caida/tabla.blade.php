<table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
    <thead style="background-color: #337ab7;color:white;">
        <tr>
            <th scope="col">{{trans('tecnicof.hospital')}}</th>
            <th scope="col">{{trans('tecnicof.bed')}}</th>
            <th scope="col">{{trans('tecnicof.patiet')}}</th>
            <th scope="col">{{trans('tecnicof.action')}}</th>
        </tr>
    </thead>
    <tbody>
        @php
        $calc = count($paciente_agenda);
        @endphp
        @if($calc < 1 ) @foreach($db1 as $value) @php $camilla_gestion=Sis_medico\Camilla_Gestion::where('id_paciente',$value->id_paciente)->where('id_agenda',$value->id)->where('num_atencion',1)->first();
            $nombre = Sis_medico\Paciente::where('id',$value->id_paciente)->first();
            $edad = $nombre->fecha_nacimiento;
            $edad2 = new DateTime($edad);
            $fecha= date("Y-m-d");
            $fecha1 = new DateTime($fecha);
            $edad_nueva = $fecha1->diff($edad2);
            $edad1 = $edad_nueva->y;
            $camilla_num = Sis_medico\Camilla::where('id', $camilla)->first();
            $hospi_num = Sis_medico\Hospital::where('id', $hospi)->first();
            @endphp
            @if(is_null($camilla_gestion))
            <tr style="text-align: center;">
                <td>{{$hospi_num->nombre_hospital}}</td>
                <td>{{$camilla_num->nombre_camilla}}</td>
                <td>{{$nombre->nombre1}} {{$nombre->nombre2}} {{$nombre->apellido1}} {{$nombre->apellido2}}</td>
                <td>@if (($edad1 >=13))
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <a href="{{route('riesgo.form_mayor',[$value->id_paciente,$camilla_num->id,$value->id])}}" class="btn btn-primary">
                        <i class="glyphicon glyphicon-edit" aria-hidden="true"> {{trans('tecnicof.form')}}</i>
                    </a>
                    @elseif(($edad1 < 13)) <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{route('riesgo.form_menor',[$value->id_paciente,$camilla_num->id,$value->id])}}" class="btn btn-success">
                            <i class="glyphicon glyphicon-edit" aria-hidden="true"> {{trans('tecnicof.form')}}</i>
                        </a>
                        @endif
                </td>
            </tr>
            @endif
            @endforeach
            @endif
            @if($calc >= 1)
            @foreach($db as $value)
            @php
            $nuevaCon = [];
            $camilla_gestion=Sis_medico\Camilla_Gestion::where('id_paciente',$value->id_paciente)->where('id_agenda',$value->id)->where('num_atencion',1)->where('sala','')->first();
            if(is_null($camilla_gestion)){
            $nuevaCon = Sis_medico\Camilla_Gestion::where('id_paciente',$value->id_paciente)->where('id_agenda',$value->id)->where('alta',1)->first();
            }
            if(is_null($nuevaCon)){
            $nuevaCon = Sis_medico\Camilla_Gestion::where('id_paciente',$value->id_paciente)->where('id_agenda',$value->id)->where('sala',1)->first();
            }
            $nombre = Sis_medico\Paciente::where('id',$value->id_paciente)->first();
            $edad = $nombre->fecha_nacimiento;
            $edad2 = new DateTime($edad);
            $fecha= date("Y-m-d");
            $fecha1 = new DateTime($fecha);
            $edad_nueva = $fecha1->diff($edad2);
            $edad1 = $edad_nueva->y;
            $camilla_num = Sis_medico\Camilla::where('id', $camilla)->first();
            $hospi_num = Sis_medico\Hospital::where('id', $hospi)->first();
            @endphp
            @if(is_null($camilla_gestion))
            <tr style="text-align: center;">
                <td>{{$hospi_num->nombre_hospital}}</td>
                <td>{{$camilla_num->nombre_camilla}}</td>
                <td>{{$nombre->nombre1}} {{$nombre->nombre2}} {{$nombre->apellido1}} {{$nombre->apellido2}}</td>
                <td>
                    @if(is_null($nuevaCon))
                    @if (($edad1 >=13))
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <a href="{{route('riesgo.form_mayor',[$value->id_paciente,$camilla_num->id,$value->id])}}" class="btn btn-primary">
                        <i class="glyphicon glyphicon-edit" aria-hidden="true"> {{trans('tecnicof.form')}}</i>
                    </a>
                    @elseif(($edad1 < 13)) <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{route('riesgo.form_menor',[$value->id_paciente,$camilla_num->id,$value->id])}}" class="btn btn-success">
                            <i class="glyphicon glyphicon-edit" aria-hidden="true"> {{trans('tecnicof.form')}}</i>
                        </a>
                        @endif
                        @else
                        <a onclick="desabilitar()" id="botonG" href="{{route('camilla.guardar_sinriesgo',[$value->id_paciente,$camilla_num->id,$value->id])}}" class="btn btn-warning">
                            <i class="glyphicon glyphicon-edit" aria-hidden="true"> {{trans('tecnicof.occupy')}}</i>
                        </a>
                        @endif
                </td>
            </tr>
            @endif
            @endforeach
            @endif
    </tbody>
</table>
<script text="text/javascript">
    function desabilitar() {
        $("#botonG").attr("disabled", "disabled");
    }

    $(document).ready(function() {
        $('#example2').DataTable({
            'paging': true,
            'lengthChange': false,
            'searching': true,
            'responsive': true,
            'ordering': true,
            'info': true,
            'autoWidth': false,
            'sInfoEmpty': false,
            'sInfoFiltered': true,
            'language': {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            }
        });
    });
</script>