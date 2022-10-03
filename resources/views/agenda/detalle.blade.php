
@extends('agenda.base')

@section('action-content')

<style type="text/css">

.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
}


</style>

<div class="modal fade fullscreen-modal" id="favoritesModal2" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document"  >
    <div class="modal-content"  id="imprimir3">

    </div>
  </div>
</div>

<div class="container-fluid" >
    <div class="row">

        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h4 class="form-group col-md-3 col-sm-3 col-xs-3">DETALLE DEL PACIENTE</h4>
                     @if(!is_null($historiaclinica))
                    <a class="form-group col-md-3 col-sm-3 col-xs-3" href="{{route('historia.historia',['id' => $agenda->id])}}"><button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-level-up"></span> Historia Clinica</button>
                    </a>
                    <!--a class="form-group col-md-3 col-sm-3 col-xs-3" href="{{route('agenda.detalle2', ['id' => $agenda->id])}}"><button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-level-up"></span> Atención Procedimiento</button>
                    </a>
                    <a class="form-group col-md-3 col-sm-3 col-xs-3" href="{{route('agenda.detalle3', ['id' => $agenda->id])}}"><button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-level-up"></span> Atención Procedimiento vt</button>
                    </a-->
                    <a class="form-group col-md-3 col-sm-3 col-xs-3" href="{{route('procedimientos_historia.mostrar', ['id' => $agenda->id])}}"><button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-level-up"></span> Atención Procedimiento</button>
                    </a>
                    @endif
                    <h4 class="form-group col-md-3 col-sm-3 col-xs-3" style="text-align:right; @if($cant_cortesias>1) color:red; @endif">Cortesias en el día: {{$cant_cortesias}}</h4>
                </div>
                <div class="box-body">
                    <div class="form-group col-md-6 {{ $errors->has('cortesia') ? ' has-error' : '' }}" >
                        <label for="cortesia" class="col-md-3 control-label">Editar Cortesia</label>
                        <div class="col-md-3">
                            <select id="cortesia" name="cortesia" class="form-control input-sm" required onchange="actualiza(event);">
                                <option @if($agenda->cortesia=='NO'){{'selected '}}@endif value="NO">NO</option>
                                <option @if($agenda->cortesia=='SI'){{'selected '}}@endif value="SI">SI</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive col-md-12">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td><b>Paciente:</b></td>
                                    <td>{{ $agenda->pnombre1}} @if($agenda->pnombre2 != "(N/A)"){{ $agenda->pnombre2}}@endif {{ $agenda->papellido1}} @if($agenda->papellido2 != "(N/A)"){{ $agenda->papellido2}}@endif</td>
                                    <td><b>Cédula: </b></td>
                                    <td>{{$agenda->id_paciente}}</td>
                                    <td><b>Edad:</b></td>
                                    <td><span id="edad"></span></td>
                                </tr>
                                <tr>
                                    <td><b>Ocupación:</b></td>
                                    <td>@if($agenda->ocupacion!=''){{$agenda->ocupacion}}@else{{"No ingresado"}}@endif</td>
                                    <td><b>Estado Civil: </b></td>
                                    <td>@if($agenda->estadocivil==1) {{"SOLTERO(A)"}} @elseif($agenda->estadocivil==2)
                                    {{"CASADO(A)"}} @elseif($agenda->estadocivil==3) {{"VIUDO(A)"}} @elseif($agenda->estadocivil==4) {{"DIVORCIADO(A)"}} @elseif($agenda->estadocivil==5)
                                    {{"UNION LIBRE"}} @elseif($agenda->estadocivil==6) {{"UNION DE HECHO"}} @else {{"No ingresado"}} @endif</td>
                                    <td><b>Parentesco:</b></td>
                                    <td>@if(!is_null($agenda->hparentesco)){{$agenda->hparentesco}}@else{{$agenda->pparentesco}}@endif</td>
                                </tr>
                                <tr>
                                    <td><b>Ciudad:</b></td>
                                    <td >@if($agenda->ciudad!=''){{$agenda->ciudad}}@else{{"No ingresado"}}@endif</td>
                                    <td ><b>Nacimiento:</b></td>
                                    <td colspan="3">@if($agenda->lugar_nacimiento!=''){{$agenda->lugar_nacimiento}}@else{{"No ingresado"}}@endif</td>
                                </tr>
                                <tr>
                                    <td><b>Dirección:</b></td>
                                    <td colspan="5">@if($agenda->direccion!=''){{$agenda->direccion}}@else{{"No ingresado"}}@endif</td>
                                </tr>
                                <tr>
                                    <td><b>Teléfono:</b></td>
                                    <td colspan="2">@if($agenda->telefono1!=''){{$agenda->telefono1}}@else{{"No ingresado"}}@endif</td>
                                    <td ><b>Celular:</b></td>
                                    <td colspan="2">@if($agenda->telefono2!=''){{$agenda->telefono2}}@else{{"No ingresado"}}@endif</td>
                                </tr>
                                @if(!is_null($agenda->palergias)&&$agenda->palergias!='')
                                <tr>
                                    <td><b>Alergias:</b></td>
                                    <td colspan="5"> {{$agenda->palergias}}</td>
                                </tr>
                                @endif
                                @if(!is_null($agenda->pantecedentes_pat))
                                <tr>
                                    <td><b>Antecedentes Patológicos:</b></td>
                                    <td colspan="5"> {{$agenda->pantecedentes_pat}}</td>
                                </tr>
                                @endif
                                @if(!is_null($agenda->pantecedentes_fam))
                                <tr>
                                    <td><b>Antecedentes Familiares:</b></td>
                                    <td colspan="5"> {{$agenda->pantecedentes_fam}}</td>
                                </tr>
                                @endif
                                @if(!is_null($agenda->pantecedentes_quir))
                                <tr>
                                    <td><b>Antecedentes Quirurgicos:</b></td>
                                    <td colspan="5"> {{$agenda->pantecedentes_quir}}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border"><h4 class="col-md-6">DETALLE AGENDA</h4></div>
                <div class="box-body">
                    @if($agenda->nro_reagenda>'0')
                    <div class="form-group col-md-12">
                    <p><b style="color: red;">** La cita ya ha sido reagendada : {{$agenda->nro_reagenda}} @if($agenda->nro_reagenda<='1'){{"vez"}}@else{{"veces"}}@endif</b></p>
                    </div>
                     @endif
                    <div class="table-responsive col-md-12">
                        <table class="table table-striped">
                            <tbody>

                                <tr>
                                    <td><b>Estado:  </b></td>
                                    <td>@if ($agenda->estado_cita=='0')
                                        Por Confirmar
                                    @endif
                                    @if ($agenda->estado_cita=='1')
                                        Confirmada
                                    @endif
                                    @if ($agenda->estado_cita=='2')
                                        Reagendada
                                    @endif
                                    @if ($agenda->estado_cita=='3')
                                        Suspendida
                                    @endif
                                    @if ($agenda->estado_cita=='4')
                                        ASISTIÓ
                                    @endif</td>
                                    <td><b>Tipo:</b></td>
                                    <td>@if($agenda->proc_consul=='0'){{'Consulta'}}
                                        @elseif($agenda->proc_consul=='1'){{'Procedimiento'}}
                                        @else{{'Reuniones'}}
                                        @endif</td>
                                    <td><b>Seguro:  </b></td>
                                    <td><b>{{$agenda->snombre}}</b></td>
                                </tr>
                                <tr>
                                    <td><b>Fecha Cita:</b></td>
                                    <td>{{ substr($agenda->fechaini, 0, 10)}}</td>
                                    <td><b>Hora:</b></td>
                                    <td>{{substr($agenda->fechaini, 11, 5)}} - {{substr($agenda->fechafin, 11, 5)}}</td>
                                    <td><b>Cortesia:</b></td>
                                    <td>@if($agenda->cortesia=='SI') SI @else NO @endif</td>

                                </tr>
                                <tr>
                                    <td><b>Ingreso:</b></td>
                                    <td>@if($agenda->est_amb_hos=='0'){{'Ambulatorio'}}@else{{'Hospitalizado'}}@endif</td>
                                     <td><b>Sala:</b></td>
                                    <td colspan="2">@if(!is_null($agenda->slnombre)){{$agenda->slnombre}}/{{$agenda->hsnombre}}@else No Ingresada @endif</td>
                                    <td><b>@if($agenda->tipo_cita=='0')PRIMERA VEZ @else CONSECUTIVO @endif</b></td>

                                </tr>
                                <tr>
                                    <td><b>Doctor:</b></td>
                                    <td colspan="3">@if(!is_null($agenda->id_doctor1)){{$agenda->udnombre}} {{$agenda->udapellido}} @else No asignado @endif</td>
                                    <td><b>Especialidad:</b></td>
                                    <td>@if(!is_null($agenda->id_doctor1)){{$agenda->esnombre}}@else No asignado @endif</td>
                                </tr>
                                @if($agenda->proc_consul=='1')
                                <tr>
                                    <td><b>Procedimientos:</b></td>
                                    <td colspan="5">{{$agenda->pnombre}} @if(!is_null($agendaprocs)) @foreach($agendaprocs as $agendaproc) + {{$agendaproc->nombre}} @endforeach @endif</td>
                                </tr>
                                @endif
                                <tr>
                                    <td><b>Observaciones: </b></td>
                                    <td colspan="5">{{$agenda->observaciones}}</td>
                                </tr>
                                <tr>
                                    <td><b>Creado:</b></td>
                                    <td>{{$agenda->created_at}}</td>
                                    <td>{{substr($agenda->ucnombre,0,1)}}{{$agenda->ucapellido}}</td>
                                    <td><b>Modificado:</b></td>
                                    <td>{{$agenda->updated_at}}</td>
                                    <td>{{substr($agenda->umnombre,0,1)}}{{$agenda->umapellido}}</td>
                                </tr>

                            </tbody>
                    </table>
                    </div>

                </div>
            </div>
        </div>

        @if(!is_null($historiaclinica))
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h4>DETALLE ADMISION</h4>
                    <div class="table-responsive col-md-12">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    @if(!is_null($agenda->procedencia))
                                    <td><b>Seguro:</b></td>
                                    <td><b>{{$historiaclinica->snombre}} @if(!is_null($historiaclinica->sbnombre)) - {{$historiaclinica->sbnombre}} @endif</b></td>
                                    <td><b>Procedencia:</b></td>
                                    <td>{{$agenda->procedencia}}</td>
                                    <td><b>Parentesco:</b></td>
                                    <td>{{$historiaclinica->parentesco}}</td>
                                    @else
                                    <td><b>Seguro:</b></td>
                                    <td colspan="3"><b>{{$historiaclinica->snombre}} @if(!is_null($historiaclinica->sbnombre)) - {{$historiaclinica->sbnombre}} @endif</b></td>
                                    <td><b>Parentesco:</b></td>
                                    <td>{{$historiaclinica->parentesco}}</td>
                                    @endif
                                </tr>
                                @if($historiaclinica->parentesco!="Principal")
                                <tr>
                                    <td><b>Principal:</b></td>
                                    <td colspan="5">{{$historiaclinica->id_usuario}} - {{$historiaclinica->upapellido1}} @if($historiaclinica->upapellido2!='(N/A)'){{$historiaclinica->upapellido2}}@endif {{$historiaclinica->upnombre1}} @if($historiaclinica->upnombre2!='(N/A)'){{$historiaclinica->upnombre2}}@endif</td>

                                </tr>
                                @endif
                                <tr>
                                    <td><b>Doctor:</b></td>
                                    <td colspan="2">{{$historiaclinica->d1nombre1}} {{$historiaclinica->d1apellido1}}</td>
                                    <td><b>Empresa:</b></td>
                                    <td colspan="2">{{$agenda->id_empresa}} - {{$agenda->nombrecomercial}}</td>
                                </tr>

                                 @if(!is_null($pentaxprocs))
                                <tr>

                                    <td><b>Procedimientos:</b></td>
                                    @php $flag=false; @endphp
                                    <td colspan="5"> @foreach($pentaxprocs as $pentaxproc) @if($flag) + @endif @php $flag=true; @endphp {{$pentaxproc->nombre}} @endforeach</td>
                                </tr>
                                @endif

                                @if(!is_null($historiaclinica->d2apellido1)||!is_null($historiaclinica->d3apellido1))
                                <tr>
                                    <td><b>Asistentes:</b></td>
                                    <td>{{$historiaclinica->d2nombre1}} {{$historiaclinica->d2apellido1}} @if(!is_null($historiaclinica->d2apellido1) && !is_null($historiaclinica->d3apellido1) ) + @endif {{$historiaclinica->d3nombre1}} {{$historiaclinica->d3apellido1}}</td>
                                </tr>
                                @endif

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @if(!is_null($historiaclinica->id_pentax))
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h4>DETALLE PENTAX</h4>
                    <div class="table-responsive col-md-12">
                        <table id="example2" class="table table-bordered table-hover" style="font-size: 12px;">
            <thead>
              <tr  >
                <th >Fecha - hora</th>
                <th >Cambio</th>
                <th >Descripción</th>
                <!--th >Doctor</th>
                <th >Asistentes</th>
                <th >Procedimientos</th-->
                <th >Seguro</th>
                <th >Sala</th>
                <th >Modifica</th>
                <th >Observación</th>
              </tr>
            </thead>
            <tbody >
            @foreach ($pentax_logs as $value)
              <tr >
                  <td >{{ $value->created_at}}</td>
                  <td >{{ $value->tipo_cambio}}</td>
                  <td >{{ $value->descripcion}}</td>
                  <!--td >{{ $value->d1nombre1}} {{ $value->d1apellido1}}</td>
                  <td >{{ $value->d2nombre1}} {{ $value->d2apellido1}} @if(!is_null($value->d2nombre1)) + @endif {{ $value->d3nombre1}} {{ $value->d3apellido1}}</td>
                  <td >@php
                        $id_procs = explode('+',$value->procedimientos);
                        $list_procs="";
                        $flag=0;
                        foreach($id_procs as $id_proc){
                          if($flag==0){
                            $list_procs=Sis_medico\Procedimiento::find($id_proc)->observacion;
                            $flag=1;
                          }
                          else{
                            $list_procs=$list_procs."+".Sis_medico\Procedimiento::find($id_proc)->observacion;
                          }
                        }
                      @endphp
                      {{$list_procs}}
                  </td-->
                  <td >{{ $value->snombre}}</td>
                  <td >{{ $value->nbrsala}}</td>
                  <td >{{substr($value->umnombre1,0,1)}}{{ $value->umapellido1}}</td>
                  <td >{{ $value->observacion}}</td>
              </tr>
            @endforeach
            </tbody>
          </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endif

    </div>
</div>



<script type="text/javascript">


$(document).ready(function(){

    $(".breadcrumb").append('<li><a href="{{ route('agenda.agenda2') }}"></i> Agenda</a></li>');
        $(".breadcrumb").append('<li class="active">Detalle</li>');

    $('#example').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,

    });

    var edad;
                edad = calcularEdad('<?php echo $agenda->fecha_nacimiento; ?>')+ " años";
                $('#edad').text(edad);



});
 /*
    $(document).ready(function(){

$("#example").DataTable({
    'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,


    });

});*/


function actualiza(e){
    cortesia = document.getElementById("cortesia").value;

    if (cortesia == "SI"){

        location.href ="{{ route('vdoctor.cortesia', ['id' => $agenda->id, 'c' => 1])}}";

    }
    else if(cortesia == "NO"){
        location.href ="{{ route('vdoctor.cortesia', ['id' => $agenda->id, 'c' => 0])}}";
    }

}




</script>












</section>

@include('sweet::alert')
@endsection
