<style type="text/css">

  .btn-detalle{
      font-size: 10px ;
      width: 100%;
      height: 100%;
      background-color: #124574;
      color: white;
      border-radius: 5px;
  }

  .btn-cita{
      font-size: 10px ;
      width: 100%;
      height: 100%;
      background-color: #F41919;
      color: white;
      border-radius: 5px;
  }

  .boton_doctor{
    width: 95%;
  }
  .barra{
    padding: 5px;
  }
</style>



<style>

  body {
    margin: 0;
    padding: 0;
    font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
    font-size: 14px;
  }

  #calendar {
    /*max-width: 900px;*/
    margin: 50px auto;
  }

  .table-hover>tbody>tr:hover{
          background-color: #ccffff !important;
  }

</style>

  <div class="box-header with-border" style="background-color: #124574; font-size: 14px; padding: 8px;">
    <div class="row">
          <div class="col-lg-2 col-3" style="padding-top: 8px; color: #124574;">
            <span style="color: white;">RESULTADO DE LA B&Uacute;SQUEDA 111111</span>
          </div>
          <div class="barra col-lg-2 col-3">
            <button type="button" id="agenda_semana" class="btn btn-danger boton_doctor" style="background-color:#124574; border-radius: 5px; border: 2px solid;"> Agenda por semanas\mes</button>
          </div>
          <div class="barra col-lg-2 col-3" style="text-align: center;">
            <a class="btn btn-danger boton_doctor" onclick="cargar_nuevopaciente();" style="color:white; background-color:#124574 ; border-radius: 5px; border: 2px solid;">  Agregar Nuevo Paciente</a>
          </div>
          <div class="barra col-lg-2 col-3" style="text-align: center;" id="ex_excel">
            <a class="btn btn-danger boton_doctor" onclick="descargar_hc_reporte();" style="color:white; background-color:#124574; border-radius: 5px; border: 2px solid;"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Exportar a Excel</a>
          </div>
          <div class="barra col-lg-2 col-4 @if(Auth::user()->id == '1307189140') oculto @endif" id="ex_revision">
            <a class="btn btn-danger boton_doctor" style="color:white; background-color:#124574 ; border-radius: 5px; border: 2px solid white;" onclick="exportar_revision();"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Exportar Revision</a>
          </div>

          @if(Auth::user()->id == '1307189140' || Auth::user()->id == '9666666666' || Auth::user()->id=='1316262193')
          <div class="barra col-lg-2 col-3 " style="text-align: center;">
            <a class="btn btn-danger boton_doctor" onclick="estadisticos_master_hc();" style="color:white; background-color:#124574; border-radius: 5px; border: 2px solid;"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span> Ver Estadisticas</a>
          </div>
          <div class="barra col-lg-2 col-6 " style="text-align: center;">
            <a class="btn btn-danger boton_doctor" onclick="estadisticos_factura();" style="color:white; background-color:#124574; border-radius: 5px; border: 2px solid;"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span> Ver E. Factura</a>
          </div>
          <div class="barra col-lg-2 col-6 " style="text-align: center;">
            <a class="btn btn-danger boton_doctor" onclick="estadisticos_reales();" style="color:white; background-color:#124574; border-radius: 5px; border: 2px solid;"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span> Ver E. Seguros Privados</a>
          </div>

          <div class="barra col-lg-2 col-3 " style="text-align: center;">
            <a class="btn btn-danger boton_doctor" onclick="estadisticos_estimado();" style="color:white; background-color:#124574 ; border-radius: 5px; border: 2px solid;"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span> Ver Ganancia Estimada</a>
          </div>

          <div class="barra col-lg-2 col-5 estadisticos " style="text-align: center;">
            <a class="btn btn-danger boton_doctor" onclick="estadisticos_efectivo();" style="color:white; background-color:#124574; border-radius: 5px; border: 2px solid white;"> Ganancia Efectiva</a>
          </div>

          <div class="barra col-lg-2 col-5 estadisticos " style="text-align: center;">
            <a class="btn btn-danger boton_doctor" onclick="estadisticos_produccion();" style="color:white; background-color:#124574; border-radius: 5px; border: 2px solid white;"> Producción Médicos</a>
          </div>
          @endif
          <div class="col-lg-1 col-3 @if(Auth::user()->id == '1307189140') oculto @endif" style="text-align: center;">
            <a class="btn btn-danger boton_doctor" href="{{route('consultam.index')}}" target="_blank" style="color:white; background-color:#124574 ; border-radius: 5px; border: 2px solid;">  Master</a>
          </div>
          <div class="barra col-lg-2 col-4 @if(Auth::user()->id == '1307189140') oculto @endif" style="text-align: center;">
            <a class="btn btn-danger boton_doctor" onclick="revisar_procedimientos();" style="color:white; background-color:#124574 ; border-radius: 5px; border: 2px solid white;"> Revisar Procedimientos</a>

          </div>

    </div>
  </div>
  <div class="box-body" style="border: 2px solid ;padding-left: 0px;padding-right: 0px;" id="modificar">
    <div class="content col-md-12" style="padding: 0">
      <div class="table-responsive" id="div_grafico">
        @php
          $aleatorio = rand();
        @endphp
        <table  id="example2{{$aleatorio}}" class="table " cellspacing="0" width="100%" style="font-size: 12px;">
          @if($agendas_pac!=[])
          <thead style="">
            <tr style=" ">
                <th scope="col" class="color titulo" >C&eacute;dula</th>
                <th scope="col" class="color titulo" >Apellidos</th>
                <th scope="col" class="color titulo" >Nombres</th>
                <th scope="col" class="color titulo" >Fecha Nacimiento</th>
                <th scope="col" class="color titulo" >Seguro/Convenio</th>
                <th scope="col" class="color titulo" >Doctor</th>
                <th scope="col" class="color titulo" >Hora</th>
                <th scope="col" class="color titulo" >Tipo</th>
                <th scope="col" class="color titulo" >Cortesia</th>
                <th scope="col" class="color titulo" >Estado</th>
                <th scope="col" class="color titulo" >Acci&oacute;n</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($agendas_pac as $pac)

              <tr>
              <td class="color">{{$pac->id}}</td>
              <td class="color">{{$pac->apellido1}} {{$pac->apellido2}}</td>
              <td class="color">{{$pac->nombre1}} {{$pac->nombre2}}</td>
              <td class="color">{{$pac->fecha_nacimiento}}</td>
              <td class="color">
                @if(!is_null($pac->seguro_nombre))
                  {{$pac->seguro_nombre}}/{{$pac->empresa_nombre}}
                @endif
              </td>
              <td class="color">{{$pac->dnombre1}} {{$pac->dapellido1}}</td>
              <td class="color">
                {{substr($pac->fechaini,10,10)}} - {{substr($pac->fechafin,10,10)}}
              </td>
              <td class="color">
                @if($pac->proc_consul=='0')
                  @if($pac->tc)
                  <span style="padding: 2px;">TELECONSULTA:</span><br>
                  <span style="padding: 2px;">{{$pac->teleconsulta}}</span>
                  @else
                    CONSULTA
                  @endif
                @elseif($pac->proc_consul=='1')
                  @if(isset($agendas_proc[$pac->id_agenda])) {{$agendas_proc[$pac->id_agenda]['0']}}
                  @else
                    PROCEDIMIENTO
                  @endif
                @endif
              </td>
              <td>
                <b>
                  @if($pac->cortesia=='SI')
                   <span style="color: red">
                    SI
                   </span>
                  @else
                   <span class="color">
                    NO
                   </span>
                  @endif
                </b>
              </td>
              @php

                $contador_cie10 = 0;

                $verificar = \Sis_medico\Historiaclinica::where('id_agenda', $pac->id_agenda)->first();
                $nueva_agenda = \Sis_medico\Agenda::find($pac->id_agenda)->first();

                $contador_cie10 = DB::table('hc_cie10 as c')->where('hcid',$pac->hcid)->get()->count();

                //dd($pac);

                if($pac->proc_consul == 0){

                  $hcproc = Sis_medico\Agenda::where('agenda.id_paciente',$pac->id)
                  ->where('agenda.estado_cita', 4)
                  ->where('agenda.proc_consul', 0)
                  ->join('historiaclinica as h','h.id_agenda','agenda.id')
                  ->whereBetween('agenda.fechaini',array(date('Y-m-d').' 00:00:00', date('Y-m-d').' 23:59:59'))
                  ->join('hc_evolucion as hc_proto', 'hc_proto.hcid', 'h.hcid')
                  ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.hc_id_procedimiento')
                  ->select('hc_p.*')
                  ->first();
                  //dd($hcproc);
                }





              @endphp
              <td class="color">
                  @if($pac->omni=='OM')
                    @if($pac->estado_cita==4)
                      Ingresado
                    @elseif($pac->estado_cita==5)
                      Alta
                    @elseif($pac->estado_cita==6)
                      Emergencia
                    @endif
                  @elseif($contador_cie10 >'1')
                    ATENDIDO
                  @elseif(!is_null($verificar))
                    ADMISIONADO
                  @else
                    @if($nueva_agenda->estado_cita == 0)
                      Por Confirmar
                    @elseif($nueva_agenda->estado_cita == 1)
                      Confirmada
                    @elseif($nueva_agenda->estado_cita == 2)
                      Reagendado
                    @endif
                  @endif
              </td>

              <td>@if(!is_null($verificar))
                <a class="btn btn-info btn-detalle" style="color: white; padding-right: 0px; padding-left: 0px" href="{{route('nd.buscador', ['id_paciente' => $pac->id])}}">
                Ver Detalle Completo</a>@endif
                @if($pac->proc_consul == 0)
                  @if(!is_null($hcproc))
                    @if(is_null($hcproc->hora_fin) && !is_null($hcproc->hora_inicio))
                      <a class="btn btn-danger btn-cita" style="color: white; padding-right: 0px; padding-left: 0px">Consulta sin Finalizar </a>
                    @endif
                  @endif
                @endif
              </td>

            </tr>
            @endforeach
          </tbody>
          @endif
        </table>

        <label class="color" style="padding-left: 15px;font-size: 15px">Total de Registros: {{$agendas_pac->count()}}</label>
      </div>
    </div>
  </div>

  <script type="text/javascript">
    $('#agenda_semana').click(function(){

      $.ajax({
          type: 'get',
          url:"{{route('hc4.calendario_fullcalendar')}}",
          success: function(data){
            //console.log(data);
            //alert("ok");
            $("#modificar").html(data);
            //$("#agenda_semana").addClass('oculto');

          },
          error: function(data){
            console.log(data);
          }
      });

    });

      $('#example2{{$aleatorio}}').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    });

//Busqueda de estadisticos
    function estadisticos_master_hc(){
        $.ajax({
          type: 'post',
          url:"{{route('hc4_consulta.pasteles_hc4')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'html',
          data: $("#form_buscador").serialize(),
          success: function(datahtml){
            //console.log(data);
            $("#div_grafico").html(datahtml);
            //console.log(data);
          },
          error:  function(){
            alert('error al cargar');
          }
        });
    }


//Busqueda de estadisticos
    function estadisticos_estimado(){
        $.ajax({
          type: 'post',
          url:"{{route('hc4_consulta.ganancia_hc4')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'html',
          data: $("#form_buscador").serialize(),
          success: function(datahtml){
            //console.log(data);
            $("#div_grafico").html(datahtml);
            //console.log(data);
          },
          error:  function(){
            alert('error al cargar');
          }
        });
    }
    function estadisticos_factura(){
      //estadisticos_hc4.privados
      $.ajax({
          type: 'post',
          url:"{{route('venta.estadisticoshc4')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'html',
          data: $("#form").serialize(),
          success: function(datahtml){
            //console.log(data);
            $("#div_grafico").html(datahtml);
            //console.log(data);
          },
          error:  function(){
            alert('error al cargar');
          }
        });
    }
    function revisar_procedimientos(){
        $.ajax({
          type: 'post',
          url:"{{route('hc4_revisar.procedimientos')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'html',
          data: $("#form_buscador").serialize(),
          success: function(datahtml){
            //console.log(data);
            $("#div_grafico").html(datahtml);
            //console.log(data);
          },
          error:  function(){
            alert('error al cargar');
          }
        });
    }
    function estadisticos_reales(){
      //estadisticos_hc4.privados
      $.ajax({
          type: 'post',
          url:"{{route('estadisticos_hc4_s.privados')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'html',
          data: $("#form").serialize(),
          success: function(datahtml){
            //console.log(data);
            $("#div_grafico").html(datahtml);
            //console.log(data);
          },
          error:  function(){
            alert('error al cargar');
          }
        });
    }
    function exportar_revisionxx(){
        $.ajax({
          type: 'post',
          url:"{{route('hc4controller.exportar_revision')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'html',
          data: $("#form_buscador").serialize(),
          success: function(datahtml){
            console.log("hola");
            //$("#div_grafico").html(datahtml);
            //console.log(data);
          },
          error:  function(){
            alert('error al cargar');
          }
        });
    }
    function exportar_revision(){
      $('#exp_rev').click();
    }

</script>
<script type="text/javascript">
  $(document).ready(function() {
  if ( $("#ex_revision").length > 0 ) {
    $('#ex_revision').hide();
  }
  });
</script>
