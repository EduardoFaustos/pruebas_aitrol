<div class="card">
    <div class="card-header bg bg-primary">
        <div class="row">
            <div class="d-flex align-items-center col-md-12">

               <span class="sradio">1</span>
                <h4 class="card-title ml-25 colorbasic">
                {{trans('pasos.RegistrodeAdmision')}} 
                </h4>

            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <b>  {{trans('pasos.apellidosynombres')}} </b>
            </div>
            <div class="col-md-12">
                <span>{{ $solicitud->paciente->apellido1}} {{ $solicitud->paciente->apellido2}} {{ $solicitud->paciente->nombre1}} {{ $solicitud->paciente->nombre2}} </span>
            </div>
            <div class="col-md-4">
                <b>   {{trans('pasos.cedula')}} </b>
            </div>
            <div class="col-md-4">
                <b>{{trans('pasos.ciudad')}} </b>

            </div>
            <div class="col-md-4">
                <b> {{trans('pasos.telefono')}}</b>
            </div>
            <div class="col-md-4">
                <span>{{ $solicitud->id_paciente }} </span>
            </div>

            <div class="col-md-4">
                <span>{{ $solicitud->paciente->ciudad }} </span>
            </div>

            <div class="col-md-4">
                <span>{{ $solicitud->paciente->telefono1 }} </span>
            </div>
            <div class="col-md-4">
                <b> {{trans('pasos.celular')}}</b>
            </div>
            <div class="col-md-4">
                <b> {{trans('pasos.barrio')}}</b>
            </div>
            <div class="col-md-4">
                <b> {{trans('pasos.parroquia')}}</b>
            </div>
            <div class="col-md-4">
                <span>{{ $solicitud->paciente->telefono2 }} </span>
            </div>

            <div class="col-md-4">
                <span>{{ $solicitud->paciente->ho_datos_paciente->barrio }} </span>
            </div>

            <div class="col-md-4">
                <span>{{ $solicitud->paciente->ho_datos_paciente->parroquia }} </span>
            </div>
             <div class="col-md-12">
                <b> {{trans('pasos.direccion')}}</b>
            </div>
            <div class="col-md-12">
                <span>{{ $solicitud->paciente->direccion }} </span>
            </div>
            <div class="col-md-4">
                <b>{{trans('pasos.canton')}} </b>
            </div>
            <div class="col-md-4">
                <b>{{trans('pasos.provincia')}} </b>
            </div>
            <div class="col-md-4">
                <b> {{trans('pasos.zona')}}</b>
            </div>
            <div class="col-md-4">
                <span>{{ $solicitud->paciente->ho_datos_paciente->canton }} </span>
            </div>

            <div class="col-md-4">
                <span>{{ $solicitud->paciente->ho_datos_paciente->provincia }} </span>
            </div>

            <div class="col-md-4">
                <span>{{ $solicitud->paciente->ho_datos_paciente->zona_ur }} </span>
            </div>
            <div class="col-md-4">
                <b>{{trans('pasos.fechaNacimiento')}}</b>
            </div>
            <div class="col-md-4">
                <b>{{trans('pasos.nacionalidad')}}</b>
            </div>
            <div class="col-md-4">
                <b> {{trans('pasos.grCultural')}}</b>
            </div>
            <div class="col-md-4">
                <span>{{ $solicitud->paciente->fecha_nacimiento }} </span>
            </div>

            <div class="col-md-4">
                <span>{{ $solicitud->paciente->ho_datos_paciente->nacionalidad }} </span>
            </div>

            <div class="col-md-4">
                <span>{{ $solicitud->paciente->ho_datos_paciente->grupo_cultural }} </span>
            </div>
            <div class="col-md-4">
                <b> {{trans('pasos.edad')}}</b>
            </div>
            <div class="col-md-4">
                <b>{{trans('pasos.sexo')}}</b>
            </div>
            <div class="col-md-4">
                <b>{{trans('pasos.estadoCivil')}}</b>
            </div>
            <div class="col-md-4">
                <span id="edad"> </span>
            </div>

            <div class="col-md-4">
                <span>@if($solicitud->paciente->sexo =='1') HOMBRE @else MUJER @endif </span>
            </div>

            <div class="col-md-4">
                @if($solicitud->paciente->estadocivil == 1)<span> SOLTERO(A)</span>
                @elseif($solicitud->paciente->estadocivil == 2)<span> CASADO(A)</span>
                @elseif($solicitud->paciente->estadocivil == 3)<span> VIUDO(A)</span>
                @elseif($solicitud->paciente->estadocivil == 4)<span> DIVORCIADO(A)</span>
                @elseif($solicitud->paciente->estadocivil == 5)<span> UNION LIBRE</span>
                @endif
            </div>
            <div class="col-md-4">
                <b>{{trans('pasos.instruccion')}}</b>
            </div>
            <div class="col-md-4">
                <b>{{trans('pasos.ocupacion')}}</b>
            </div>
            <div class="col-md-4">
                <b>{{trans("pasos.Empresa")}}</b>
            </div>
            <div class="col-md-4">
                <span>{{ $solicitud->paciente->ho_datos_paciente->instruccion }} </span>
            </div>

            <div class="col-md-4">
                <span>{{ $solicitud->paciente->ocupacion }} </span>
            </div>

            <div class="col-md-4">
                <span>{{ $solicitud->paciente->ho_datos_paciente->empresa_trabajo }} </span>
            </div>

          
            <div class="col-md-6">
                <b> {{trans('pasos.seguro')}}</b>
            </div>
            <div class="col-md-6">
                <b> {{trans('pasos.referido')}}</b>
            </div>

        

            <div class="col-md-6">
                <span>@if($solicitud->id_seguro != null) {{ $solicitud->seguro->nombre}} @endif </span>
            </div>

            <div class="col-md-6">
                <span>{{ $solicitud->paciente->referido }} </span>
            </div>

            <div class="col-md-12">
                <b>{{trans('pasos.llamara')}}</b>
            </div>
            <div class="col-md-12">
                <span>{{ $solicitud->paciente->ho_datos_paciente->llamar_a }} </span>
            </div>

            <div class="col-md-4">
                <b> {{trans('pasos.parentesco')}}</b>
            </div>
            <div class="col-md-4">
                <b> {{trans('pasos.telefono')}}</b>
            </div>
            
             <div class="col-md-4">
                <span> </span>
            </div>

            <div class="col-md-4">
                <span>{{ $solicitud->paciente->ho_datos_paciente->telefono_inst_per_paci }}</span>
            </div>

            <div class="col-md-12">
                <b> {{trans('pasos.familiar')}}</b>
            </div>

            <div class="col-md-12">
                <span>{{ $solicitud->paciente->ho_datos_paciente->direccion_familiar }} </span>
            </div>

            <div class="col-md-12">
                <b> {{trans('pasos.llegada')}}</b>
            </div>

            <div class="col-md-12">
                <span>{{ $solicitud->paciente->ho_datos_paciente->forma_llegada }}</span> </span>
            </div>

            <div class="col-md-12">
                <b>{{trans('pasos.informacion')}}</b>
            </div>

            <div class="col-md-12">
                <span>{{ $solicitud->paciente->ho_datos_paciente->direccion_familiar }} </span>
            </div>

            <div class="col-md-4">
                <b> {{trans('pasos.telefono')}}</b>
            </div>

            <div class="col-md-4">
                <span>{{ $solicitud->paciente->telefono3 }} </span>
            </div>


        </div>
    </div>

</div>
<script type="text/javascript">
    Edad('{{$solicitud->paciente->fecha_nacimiento}}','{{date('Y-m-d',strtotime($solicitud->created_at))}}');
    function Edad(FechaNacimiento, Fechasolicitud) {

        var fechaNace = new Date(FechaNacimiento);
        var fechaActual = new Date(Fechasolicitud)

        var mes = fechaActual.getMonth();
        var dia = fechaActual.getDate();
        var año = fechaActual.getFullYear();

        fechaActual.setDate(dia);
        fechaActual.setMonth(mes);
        fechaActual.setFullYear(año);

        edad = Math.floor(((fechaActual - fechaNace) / (1000 * 60 * 60 * 24) / 365));
        $('#edad').text(edad);
        //return edad;
        //alert(edad);
    }
</script>
