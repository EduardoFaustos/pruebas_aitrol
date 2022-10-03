@extends('laboratorio.orden.base')
@section('action-content')
@php
   $id_auth    = Auth::user()->id;
@endphp


<style type="text/css">
  .parent{
      overflow-y:scroll;
      height: 100%;
  }
  .parent::-webkit-scrollbar {
      width: 8px;
  } /* this targets the default scrollbar (compulsory) */
  .parent::-webkit-scrollbar-thumb {
      background: #3c8dbc;
      border-radius: 10px;
  }
  .parent::-webkit-scrollbar-track {
    width: 10px;
      background-color: #3c8dbc;
      box-shadow: inset 0px 0px 0px 3px #3c8dbc;
  }
  .contenedor2{
        padding-left: 15px;
        padding-right: 60px;
        padding-top: 20px;
        padding-bottom: 0px;
        background-color: #FFFFFF;
        margin-left: 0px;
    }
    p {
      font-size: 11px;
    }
  @media screen and (min-width: 601px) {
    .xnombre{
      font-size:14px;
      text-align: left;
    }
  }

  /* If the screen size is 600px wide or less, set the font-size of <div> to 30px */
  @media screen and (max-width: 600px) {
    .xnombre{
      font-size:10px;
      overflow:hidden;
      white-space:nowrap;
      text-overflow: ellipsis;
    }
  }
  .hidden-paginator {

          display: none;

  }

</style>

<div class="container-fluid">
  <div class="row ">
    <div class="col-md-12" style="padding-left: 0.;padding-left: 0px;padding-right: 9px;margin-left: 5px;margin-right: 0px;border-radius: 10px;">
      <div class="col-md-12" style="border: 0px solid #000000;margin-left: 8px;margin-right: 0px;margin-left: 4px;padding-right: 0px;padding-left: 0px;background-color:#4682B4;">
        <div class="row">
          <div class="col-md-12" style="text-align: center;">
              <h4 style="color: white;">
                <b>{{trans('ehistorialexam.BIENVENIDOALSISTEMADERESULTADOSDEEXÁMENESDELABORATORIO')}}</b>
              </h4>
          </div>
          @if(!is_null($paciente))
          <div class="row">
            <div class="col-md-12" style="text-align: center;">
              <h4 style="color: white;" >
                <b>{{trans('ehistorialexam.USUARIO:')}} {{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif
                  {{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif
                  </b>
              </h4>
            </div>
          </div>
          @endif
          <br>
          <div class="row">
            <div class="col-md-12" style="text-align: center;">
              <h4 style="color: white;" >
              <a href="{{route('formato.encuestalabs')}}"class="btn btn-default" target="_blank"><i class="fa fa-pencil-square-o"></i>{{trans('ehistorialexam.Ayudanosamejorar')}}</a>
            </div>
          </div>
        </div>
        <div>
          <div style="border-left-width: 20px; padding-left: 15px; padding-right: 10px; padding-top: 20px;padding-bottom: 0px; background-color: #ffffff; margin-left: 0px">
            <div class="parent" style="background-color: #ffffff">
              <div class="infinite-scroll">
              <div style=" margin-right: 30px;">
                @php $xcant = $ordenes->count(); @endphp
                @foreach($ordenes as $value)
                  @php
                    $value = Sis_medico\Examen_Orden::find($value->id);
                    $fecha_orden = $value->fecha_orden;
                    if(!is_null($value->fecha_orden)){
                          $fecha_r =  Date('Y-m-d',strtotime($value->fecha_orden));
                    }
                    $resultados = $value->resultados;
                    $xedad = Carbon\Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                  @endphp

                  <div class="box @if($xcant>1) collapsed-box @endif" style="border: 2px solid #4682B4; border-radius: 10px; background-color: white; font-size: 13px; font-family: Helvetica; margin-bottom: 10px;margin-top: 0px;">
                    <div class="box-header with-border" style=" text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
                      <div class="col-md-5">
                        <span> <b style="font-family: 'Helvetica';" class="box-title">
                       {{trans('ehistorialexam.PACIENTE:')}}  {{$value->paciente->apellido1}} @if($value->paciente->apellido2!='(N/A)') {{$value->paciente->apellido2}} @endif {{$value->paciente->nombre1}} @if($value->paciente->nombre2!='(N/A)') {{$value->paciente->nombre2}} @endif
                        </b></span>
                      </div>
                      <div class="col-md-5">
                        @if(!is_null($value))
                          @php
                            $dia =  Date('N',strtotime($value->fecha_orden));
                            $mes =  Date('n',strtotime($value->fecha_orden));
                          @endphp
                            <span> <b style="font-family: 'Helvetica';" class="box-title">
                          @if($dia == '1') {{trans('ehistorialexam.Lunes')}}
                             @elseif($dia == '2') {{trans('ehistorialexam.Martes')}} 
                             @elseif($dia == '3') {{trans('ehistorialexam.Miércoles')}}
                             @elseif($dia == '4') {{trans('ehistorialexam.Jueves')}} 
                             @elseif($dia == '5') {{trans('ehistorialexam.Viernes')}}
                             @elseif($dia == '6') {{trans('ehistorialexam.Sábado')}}
                             @elseif($dia == '7') {{trans('ehistorialexam.Domingo')}}
                          @endif
                            {{substr($value->fecha_orden,8,2)}} {{trans('ehistorialexam.de')}}
                          @if($mes == '1') {{trans('ehistorialexam.Enero')}}
                             @elseif($mes == '2') {{trans('ehistorialexam.Febrero')}}
                             @elseif($mes == '3') {{trans('ehistorialexam.Marzo')}}
                             @elseif($mes == '4') {{trans('ehistorialexam.Abril')}}
                             @elseif($mes == '5') {{trans('ehistorialexam.Mayo')}}
                             @elseif($mes == '6') {{trans('ehistorialexam.Junio')}}
                             @elseif($mes == '7') {{trans('ehistorialexam.Julio')}}
                             @elseif($mes == '8') {{trans('ehistorialexam.Agosto')}}
                             @elseif($mes == '9') {{trans('ehistorialexam.Septiembre')}}
                             @elseif($mes == '10') {{trans('ehistorialexam.Octubre')}}
                             @elseif($mes == '11') {{trans('ehistorialexam.Noviembre')}}
                             @elseif($mes == '12') {{trans('ehistorialexam.Diciembre')}}
                          @endif
                            {{trans('ehistorialexam.del')}} {{substr($value->fecha_orden,0,4)}}</b></span>
                        @endif
                      </div>

                      <div class="pull-right box-tools ">
                        <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili" style="background-color: #3c8dbc;">
                        <i class="fa @if($xcant>1) fa-plus @else fa-minus @endif"></i>
                        </button>
                      </div>
                    </div>
                    <div class="box-body">
                      <div style="margin-left: 0px;margin-bottom: 10px; margin-right: 10px">
                        <div class="col-12">
                          <div class="row">
                            <div class="col-md-8" style="border: 2px solid #4682B4;margin-left: 10px;margin-right: 10px;padding-right: 0px;padding-left: 0px;border-radius: 10px;background-color: white; height: 30%; margin-bottom: 10px">
                              <div class="col-md-12"  style="background-color: #4682B4; color: white; font-family: 'Helvetica general3';border-bottom: #4682B4; text-align: center ">
                                  <label class="box-title" style="background-color: #4682B4;  font-size: 16px;">
                                  {{trans('ehistorialexam.DetalledelaOrdendeLaboratorio')}}
                                  </label>
                              </div>
                              <div class="col-md-12">
                                <div class="col-md-3" ><b>{{trans('ehistorialexam.Cédula:')}}</b></div>
                                <div class="col-md-3" >{{$value->id_paciente}}</div>
                                <div class="col-md-3"><b>{{trans('ehistorialexam.Parentesco')}}</b></div>
                                @if(!is_null($paciente))
                                  @if($value->id_paciente != $paciente->id)
                                  <div class="col-md-3">{{trans('ehistorialexam.Familiar')}}</div>
                                  @else
                                  <div class="col-md-3">{{trans('ehistorialexam.Titular')}}</div>
                                  @endif
                                @else
                                  <div class="col-md-3">{{trans('ehistorialexam.Familiar')}}</div>
                                @endif
                                <div class="col-md-3" ><b>{{trans('ehistorialexam.Edad:')}}</b></div>
                                <div class="col-md-3" >{{$xedad}} {{trans('ehistorialexam.años')}}</div>
                                <div class="col-md-3" ><b>{{trans('ehistorialexam.Estado:')}}</b></div>
                                <div class="col-md-3" ><span id="spa{{$value->id}}" style="color: ##00c0ef;"></span></div>
                              </div>
                            </div>
                            <div class="col-md-3">
                                <!--span>Progreso:</span-->

                                <!--div class="progress progress">
                                  <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" id="td{{$value->id}}">
                                  <span id="sp{{$value->id}}" style="color: ##00c0ef;"></span>

                                  </div>
                                </div-->
                                <center>
                                  <div class="col-md-12 col-8 oculto" id="descargar{{$value->id}}">
                                    <a target="_blank" onclick="descargar({{$value->id}});" style="width: 100%; color: white; background-color:#4682B4 ; border-radius: 5px; border: 2px solid white;" class="btn btn-info boton-lab">
                                      <span style="color: white" class="glyphicon glyphicon-download-alt"></span> {{trans('ehistorialexam.Resultados')}} 
                                    </a>
                                  </div>
                                </center>
                            </div>
                          </div>

                        </div>
                      </div>
                      <div class="col-12" style="border-radius: 0px">
                        <div style="border: 2px solid #4682B4;background-color:#FFFFFF; border-radius: 10px;" id="{{$value->id}}">
                          <div class="col-12" class="box-header " style="background-color: #4682B4; color: white; font-family: 'Helvetica general3';border-bottom: #4682B4; height: 35px; text-align: center;">
                                <label class="box-title" style="background-color: #4682B4;  font-size: 16px">
                                  {{trans('ehistorialexam.Listadodeexamenesdelaboratorio')}}
                                  
                                </label>
                          </div>
                          <div class="col-12" style="padding: 7px;">
                            <div class="row">
                              @php
                                $orden = Sis_medico\Examen_Orden::find($value->id);
                                $detalle = Sis_medico\Examen_Detalle::where('id_examen_orden',$orden->id)->join('examen as e','e.id','id_examen')->select('examen_detalle.*','e.secuencia')->orderBy('e.secuencia')->get();
                                if($orden->seguro->tipo=='0'){
                                    $agrupador = Sis_medico\Examen_Agrupador::all();
                                }else{
                                  $agrupador = Sis_medico\Examen_Agrupador_labs::all();
                                }
                                $parametros = Sis_medico\Examen_Parametro::orderBy('orden')->get();
                                $yresultado = $orden->resultados
                                  ->where('certificado','1')
                                  ->first();
                              @endphp
                              <br>
                              @if(!is_null($yresultado))
                              @foreach($agrupador as $value)
                                @php
                                  $i_agrupador=0;
                                  $xycont = 0;
                                @endphp
                                @foreach($detalle as $value_detalle)
                                  @php
                                    $i=0;
                                    $xycont ++;
                                    if($orden->seguro->tipo == '0' ){
                                       $id_agrupador = $value_detalle->examen->id_agrupador;
                                    }else{
                                      $agrupador_part = DB::table('examen_agrupador_sabana')->where('id_examen',$value_detalle->examen->id)->first();
                                      $id_agrupador = 0;

                                      if(!is_null($agrupador_part)){
                                        $id_agrupador = $agrupador_part->id_examen_agrupador_labs;
                                      }
                                    }
                                  @endphp
                                  @if($value_detalle->examen->no_resultado=='0')
                                    @if($id_agrupador == $value->id)
                                      @php
                                        if($value_detalle->examen->sexo_n_s=='0'){
                                          $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo','3');
                                        }else{
                                          $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo',$orden->paciente->sexo);
                                        }
                                        $xfmostrar = false;
                                        if($parametro_nuevo->count()>0){
                                          foreach($parametro_nuevo as $xpar){
                                            $xresultado = $orden->resultados
                                              ->where('id_parametro', $xpar->id)
                                              ->where('certificado','1')
                                              ->first();
                                            if(!is_null($xresultado)){
                                              $xfmostrar = true; break;
                                            }
                                          }
                                        }
                                      @endphp
                                      @if($xfmostrar)
                                      <div class="col-md-12" style="width: 99%;padding-left: 12px;">
                                          @if($i_agrupador == 0)
                                          <!--Contenedor Nombre Agrupador-->
                                          <div class="col-md-12" style="text-align: center;border-bottom: 1px solid #4682B4;padding: 0px;background-color: #4682B4;">
                                            <!--Nombre Agrupador-->
                                            <label style="color: white; padding-top: 2px;font-size: 12px;"> {{$value->nombre}}</label>
                                            @php
                                              $i_agrupador=1;
                                            @endphp
                                          </div>
                                          <div class="col-md-12">
                                            <div class="row">
                                            <div class="xnombre" style="width: 25%;float:left;color: #4682B4;"><b>{{trans('ehistorialexam.NOMBRE')}}</b></div>
                                            <div class="xnombre" style="width: 20%;float:left;color: #4682B4;"><b>{{trans('ehistorialexam.RESULTADO')}}</b></div>
                                            <div class="xnombre" style="width: 10%;float:left;color: #4682B4;"><b>{{trans('ehistorialexam.UNIDADES')}}</b></div>
                                            <div class="xnombre" style="width: 45%;float:left;color: #4682B4;"><b>{{trans('ehistorialexam.REFERENCIA')}}</b></div>
                                            </div>
                                          </div>
                                          <div class="xnombre" style="clear:both;"></div>
                                          @endif
                                          @if($i == 0)
                                            @if($parametro_nuevo->count() > 1 && $xfmostrar)
                                              @if($value_detalle->id_examen!='639')
                                              <div class="col-12" style="border-bottom: 1px solid #4682B4;padding: 0px">
                                                <b >{{$value_detalle->examen->descripcion}}</b>
                                              </div>
                                              @endif
                                            @endif
                                            @php
                                               $i = 1;
                                            @endphp
                                          @endif
                                          <div style="clear:both;"></div>
                                          <div class="col-md-12" style="padding-left: 0px;">
                                            <table id="example2" style="font-size: 12px;  width: 96%">
                                              <tbody>
                                                @if($value_detalle->id_examen=='661')
                                                  <tr style="font-size: 12px;">
                                                      <td style="width: 30%;"><b>{{trans('ehistorialexam.GRADO')}}</b></td>
                                                      <td style="width: 20%;"><b>{{trans('ehistorialexam.%CELULAS')}}</b></td>
                                                      <td style="width: 10  %;"><b>{{trans('ehistorialexam.L.A.P.PUNTOS')}}</b></td>
                                                      <td style="width: 40%;"><b>{{trans('ehistorialexam.REFERENCIA')}}</b></td>
                                                  </tr>
                                                @endif
                                                  @if($value_detalle->id_examen=='661')
                                                    @foreach($parametro_nuevo as $value_agrupador)
                                                      @if($value_agrupador->unidad1=='L.A.P. PUNTOS')
                                                        @php
                                                         $resultado = $orden->resultados->where('id_parametro', $value_agrupador->id)->first();
                                                        @endphp
                                                          <tr role="row">
                                                              <td style="width: 30%;">
                                                                {{$value_agrupador->nombre}}
                                                              </td>
                                                              @php
                                                                $hermano = DB::table('examen_parametro')->where('id_examen',$value_detalle->id_examen)->where('orden',$value_agrupador->orden)->where('unidad1','% CELULAS')->first();
                                                                $resultado_2 = null;
                                                                if(!is_null($hermano)){
                                                                $resultado_2 = $orden->resultados->where('id_parametro', $hermano->id)->first();
                                                                }
                                                              @endphp
                                                              <td style="width: 20%;">
                                                                  <div style="word-wrap: break-word;">
                                                                    @if(!is_null($hermano))
                                                                      @if(!is_null($resultado_2))
                                                                        @if($resultado_2->certificado=='1')
                                                                          {{$resultado_2->valor}}
                                                                        @else
                                                                          {{"0"}}
                                                                        @endif
                                                                      @endif
                                                                    @endif
                                                                  </div>
                                                              </td>
                                                              <td style="width: 10%;">
                                                                  @if(!is_null($resultado))
                                                                    {{$resultado->valor}}
                                                                  @else
                                                                    {{"0"}}
                                                                  @endif
                                                              </td>
                                                              <td style="width: 40%;">
                                                                  @if($value_agrupador->texto_referencia == "")
                                                                    {{$value_agrupador->valor1}} - {{$value_agrupador->valor1g}}
                                                                  @else
                                                                    <?php echo $value_agrupador->texto_referencia;?>
                                                                  @endif
                                                              </td>
                                                            </tr>
                                                      @endif
                                                    @endforeach

                                                  @elseif($value_detalle->id_examen=='639' && $xfmostrar)
                                                    <br>
                                                    <b>{{trans('ehistorialexam.ALERGIASAALIM.PORIGG-59')}}</b>
                                                    <br>
                                                    <div class="col-md-9" style="padding:0;">
                                                      @foreach($parametro_nuevo as $value_agrupador)
                                                        @php
                                                          $rvalor=0;
                                                          $resultado = $resultados->where('id_parametro',$value_agrupador->id)->first();
                                                          if(!is_null($resultado)){
                                                            $rvalor=$resultado->valor;
                                                          }
                                                        @endphp
                                                        @if($value_agrupador->orden=='46' || $value_agrupador->orden=='48')
                                                          <div class="col-md-6" style="padding:0;">
                                                            <div class="col-md-1"><p style=" font-size: 10px;">{{$value_agrupador->orden}}</p></div>
                                                            <div class="col-md-5"><p style=" font-size: 10px;">{{$value_agrupador->nombre}}</p></div>
                                                            <div class="col-md-1" style="background-color:#ACCBEE ; border: 1px solid #FFF;text-align: center;">
                                                            </div>
                                                            <div class="col-md-1" style="background-color:#8AB0DB; border: 1px solid #FFF;;text-align: center;">
                                                            </div>
                                                            <div class="col-md-1"  style="background-color:#A8A8A8; border: 1px solid #FFF;text-align: center;"> @if($value_agrupador->orden=='48')  <span style="color: white">X</span> @else &nbsp; @endif
                                                            </div>

                                                          </div>
                                                        @else
                                                          <div class="col-md-6" style="padding:0;">
                                                              <div class="col-md-1"><p style=" font-size: 10px;">{{$value_agrupador->orden}}</p></div>

                                                              <div class="col-md-5"><p style=" font-size: 10px;">{{$value_agrupador->nombre}}</p></div>

                                                              <div class="col-md-1" style="background-color:#ACCBEE ; border: 1px solid #FFF;text-align: center;">
                                                                @if($rvalor==1) @if($resultado->certificado=='1') X @else &nbsp; @endif @else &nbsp; @endif
                                                              </div>
                                                              <div class="col-md-1" style="background-color:#8AB0DB; border: 1px solid #FFF;;text-align: center;">@if($rvalor==2) @if($resultado->certificado=='1') X @else &nbsp; @endif @else &nbsp; @endif
                                                              </div>
                                                              <div class="col-md-1"  style="background-color:#376EAC; border: 1px solid #FFF;text-align: center;" >@if($rvalor==3) <span style="color: white">@if($resultado->certificado=='1') X @else &nbsp; @endif</span> @else &nbsp; @endif
                                                              </div>

                                                          </div>
                                                        @endif
                                                      @endforeach
                                                    </div>
                                                    <div class="col-md-3" style="padding:0;">
                                                    <div class="col-md-11" style="background-color: #ACCBEE">{{trans('ehistorialexam.ReacciónLeve')}}</div>
                                                    <div class="col-md-11" style="background-color: #8AB0DB">{{trans('ehistorialexam.ReacciónModerada')}}</div>
                                                    <div class="col-md-11" style="background-color: #376EAC;color: white;">{{trans('ehistorialexam.ReacciónFuerte')}}</div>
                                                    <div class="col-md-11" >&nbsp;</div>
                                                    <div class="col-md-11" style="padding:0px;">
                                                        <p>{{trans('ehistorialexam.*37PescadoBlancoMix:BacalaoyLenguado')}}</p>
                                                        <p>{{trans('ehistorialexam.*39PescadodeAguaDulceMix:SalmónyTrucha')}}</p>
                                                        <p>{{trans('ehistorialexam.*43MariscoMix:Camarón,Langostino,Cangrejo,Langosta,Mejillones')}}</p>
                                                        <p>{{trans('ehistorialexam.*12Mezclasdepimientos:rojo,verdeyamarillo')}}</p>
                                                        <p>{{trans('ehistorialexam.*14Leguminosas:Arverjas,lentejas,fréjol,habas')}}</p>
                                                        <p>{{trans('ehistorialexam.*18MelónMix:MelónySandía')}}</p>
                                                        <p align="justify">{{trans('ehistorialexam.Sisusresultadosindicanunareacciónelevadaalgluten,lerecomendamosqueevitetodoslosalimentosquecontengangliadina/gluten,aunqueestosalimentosnomuestrenunarespuestapositivacomoeltrigo,elcenteno,lacebada,espelta,kamut,malta,esenciademalta,vinagredemalta,salvado,triticale,dextrina')}} </p>
                                                        <br> <p> {{trans('ehistorialexam.Algunaspersonasconintoleranciaalglutensonsensiblestambiénalaavena')}} </p>
                                                    </div>
                                                  </div>

                                                  @else
                                                    @if(!(($value_detalle->id_examen == '414' || $value_detalle->id_examen == '412' || $value_detalle->id_examen == '680') && $orden->created_at<'2018-12-17' && $orden->id!='1543' && $orden->id!='1291'))
                                                      @foreach($parametro_nuevo as $value_agrupador)
                                                        @php
                                                          $resultado = $orden->resultados
                                                            ->where('id_parametro', $value_agrupador->id)
                                                            ->first();
                                                        @endphp
                                                        @if(!is_null($resultado))
                                                          @if($resultado->certificado=='1')
                                                            <tr role="row">
                                                              <td style="width: 30%;@if($parametro_nuevo->count() == 1) font-weight: 700;padding-left: -3px;font-size: 14px; @else padding-left: 10px; @endif">
                                                                {{$value_agrupador->nombre}}
                                                              </td>
                                                              <td style="width: 20%;">
                                                                <div style="word-wrap: break-word;">
                                                                  @if(!is_null($resultado))
                                                                   {{$resultado->valor}}
                                                                  @else
                                                                   {{"0"}}
                                                                  @endif
                                                                </div>
                                                              </td>
                                                              @php
                                                                $fl_ref = 0;
                                                                $ref_ant = Sis_medico\Examen_Parametro_Referencia_Anterior::where('id_parametro',$value_agrupador->id)->where('fecha_valida','>',$orden->fecha_orden)->orderBy('fecha_valida','asc')->first();
                                                                if(!is_null($ref_ant)){
                                                                  $fl_ref = 1;
                                                                }
                                                              @endphp
                                                                <td style="width: 10%;text-align: left;">@if($fl_ref){{$ref_ant->unidad1}} @else {{$value_agrupador->unidad1}} @endif
                                                                </td>
                                                                <td style="width: 40%;">
                                                                  @if($fl_ref)
                                                                    @if($ref_ant->texto_referencia == "")
                                                                     {{$ref_ant->valor1}} - {{$ref_ant->valor1g}}
                                                                    @else
                                                                      <?php echo $ref_ant->texto_referencia; ?>
                                                                    @endif
                                                                  @else
                                                                    @if($value_agrupador->texto_referencia == "")
                                                                     {{$value_agrupador->valor1}} - {{$value_agrupador->valor1g}}
                                                                    @else
                                                                      <?php echo $value_agrupador->texto_referencia; ?>
                                                                    @endif
                                                                  @endif
                                                                </td>
                                                            </tr>
                                                          @endif
                                                        @endif
                                                      @endforeach

                                                      @if($value_detalle->id_examen == '610' || $value_detalle->id_examen == '615')
                                                        @php
                                                          $sub_resultados = DB::table('examen_sub_resultado')
                                                           ->where('id_orden',$orden->id)
                                                           ->where('estado','1')
                                                           ->where('id_examen',$value_detalle
                                                           ->id_examen)->get();
                                                        @endphp
                                                        <tr>
                                                          <td colspan="4" id="sub_tabla">
                                                            <table class="table table-bordered table-hover dataTable">
                                                              <tbody>
                                                                @foreach($sub_resultados as $sub)
                                                                <tr>
                                                                  <td style="width: 300px !important;">{{$sub->campo1}}</td>
                                                                  <td style="width: 300px !important;">{{$sub->campo2}}</td>
                                                                  <td style="width: 300px !important;">{{$sub->campo3}}</td>
                                                                </tr>
                                                                @endforeach


                                                              </tbody>
                                                            </table>
                                                          </td>
                                                        </tr>
                                                      @endif
                                                    @endif
                                                @endif
                                                </tbody>
                                            </table>
                                          </div>
                                      </div>
                                      @endif
                                    @endif
                                  @endif
                                @endforeach
                              @endforeach
                              @endif
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
                <div id="paginationLinks" class="hidden-paginator">{{ $ordenes->appends(Request::all())->render() }}</div>
              </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script type="text/javascript">
   $(function() {
        $('.infinite-scroll').jscroll({
            autoTrigger: true,
            loadingHtml: '<img class="center-block" src="{{asset("/loading.gif")}}" width="50px" alt="Loading..." />',
            padding: 0,
            nextSelector: '.pagination li.active + li a',
            contentSelector: 'div.infinite-scroll',
            callback: function() {

                $('div.paginationLinks').remove();

            }
        });
    });
  $(document).ready(function($){

    @foreach ($ordenes as $value)

      $.ajax({
        type: 'get',
        url:"{{ route('resultados.puede_imprimir',['id' => $value->id]) }}",

        success: function(data){

            if(data.cant_par==0){
              var pct = 0;
            }else{
              var pct = data.certificados/data.cant_par*100;
            }
            if(){
              $('#descargar{{$value->id}}').removeClass("oculto");
            }
            if(pct >0){

            }
            if(pct<100){
              var ptexto = 'EN PROCESO';
            }else{
              var ptexto = 'COMPLETO';
            }

            $('#td{{$value->id}}').css("width", Math.round(pct)+"%");
            $('#sp{{$value->id}}').text(Math.round(pct)+"%");
            $('#spa{{$value->id}}').text(ptexto);
            if(pct < 10){
              $('#td{{$value->id}}').addClass("progress-bar-danger");
            }else if(pct >=10 && pct<90){
              $('#td{{$value->id}}').addClass("progress-bar-warning");
            }else{
              $('#td{{$value->id}}').addClass("progress-bar-success");
            }


        },


        error: function(data){


        }
      });

    @endforeach


  });

  function descargar(id_or){

    var cert = $('#sp'+id_or).text();

    if(cert=='0%'){

      alert("Sin Exámenes Ingresados");

    }else{

      window.open('{{url('resultados/imprimir')}}/'+id_or,'_blank');
    }

  }

</script>

@endsection