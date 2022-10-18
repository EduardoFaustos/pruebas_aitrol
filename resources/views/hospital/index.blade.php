@extends('layouts.app-template-h')
@section('content')

<style type="text/css">
  @media screen and (max-width:640px) {

    /* reglas CSS */
    .example-8 .navbar-brand {
      background: none;
      width: 200px;
      height: 50px;
      transform: translateX(-60%);
      left: 43%;
      position: absolute;
    }
  }

  .align-middle {
    position: absolute;
    top: 60%;
    left: 40%;
    transform: translate(-50%, -50%);
    color: blue;
  }

  h5,
  p {}

  span b {
    font-size: 50px;
  }

  span p {
    font-size: 30px;
  }

  .box-title {
    color: white;

  }

  .panel{
    padding:10px;
  }

  /*colapse*/
  .panel-group .panel {
    border-radius: 0;
    box-shadow: none;
    border-color: #EEEEEE;
  }

  .flexbox {
      display: flex;
      flex-direction: row !important;
      flex-wrap: wrap;
      gap:1
  }  
  
  .flexbox > div {
    display:flex;
    margin: 1em;
    align-items: center;
    height: 160px;
    width: calc(100% / 6);
}

  .box {
    border-color: #FDFEFE;
    border-radius: 30px;
  }

  .card-img{ 
      background: url({{asset('/hc4/img/agenda_quirofano.png')}}) no-repeat center center fixed;
      background-size: cover;
      object-fit: cover;
  }

  .label1 {
    font-size: 14px;
    color: white;
  }

  .plantilla {
    padding-top: 14px;
  }

  @media all and (max-width: 420px) {

    h5,
    p {
      font-size: 12px;
      margin-top: 0px;
      margin-bottom: 0px;
    }

    span {
      font-size: 10px;
    }

    span b {
      font-size: 20px;
    }

    span p {
      font-size: 12px;
    }
  }

  @media all and (max-width: 850px) {
    .col-md-6 {
      margin-top: 0px;
    }

    h5,
    p {
      font-size: 12px;
      margin-top: 0px;
      margin-bottom: 0px;
    }

    span {
      font-size: 10px;
    }

    span b {
      font-size: 20px;
    }

    span p {
      font-size: 12px;
    }

    .label1 {
      font-size: 10px;
      color: white;
    }

    .text1,
    .text2 {
      margin-top: 5px
    }
  }

  @media all and (max-width: 1030px) {
    .col-md-6 {
      margin-top: 0px;
    }

    h5,
    p {
      font-size: 12px;
      margin-top: 0px;
      margin-bottom: 0px;
    }

    span {
      font-size: 10px;
    }

    span b {
      font-size: 20px;
    }

    span p {
      font-size: 12px;
    }
  }
</style>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.css")}}" />
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<section class="content">
  <div class="card card-solid" style="display: none">
    <div class="row">
      <div class="col-md-12">
        <div class="row">
          <div class="col-md-6">
            <br>
            <div class="panel card-img"  style="height:340px;border-radius:10px;">
              <div class="card-img-overlay">
                <h5 style="border-bottom: 1px solid blue; margin-top: 25px" class="card-title"><img src="{{asset('/')}}hc4/img/bt_ca2.png" style="width:5%; margin-left: 5%"> AGENDA DE QUIR&Oacute;FANO DEL D&Iacute;A</h5>
                <p class="card-text" style="margin-left: 15%">{{trans('hospital.CALENDARIODEAGENDA')}}</p>
                <div class="row">
                  <div class="col-12">
                    <div class="row">
                      <span>
                        <b style="margin-left: 75px">{{date('d')}}</b>
                      </span>
                      <span>
                        <p style="margin-left: 75px">
                          @if(date('m') == 1) Enero
                          @elseif(date('m') == 2) Febrero
                          @elseif(date('m') == 3) Marzo
                          @elseif(date('m') == 4) Abril
                          @elseif(date('m') == 5) Mayo
                          @elseif(date('m') == 6) Junio
                          @elseif(date('m') == 7) Julio
                          @elseif(date('m') == 8) Agosto
                          @elseif(date('m') == 9) Septiembre
                          @elseif(date('m') == 10) Octubre
                          @elseif(date('m') == 11) Noviembre
                          @elseif(date('m') == 12) Diciembre
                          @endif {{date('Y')}}
                        </p>
                      </span>
                    </div>
                  </div>
                  <span>
                    <p style="margin-left: 75px">
                      @if(date('N') == 1)
                      Lunes @elseif(date('N') == 2)
                      Martes @elseif(date('N') == 3)
                      Miercoles @elseif(date('N') == 4)
                      Jueves @elseif(date('N') == 5)
                      Viernes @elseif(date('N') == 6)
                      Sabado @elseif(date('N') == 7)
                      Domingo
                      @endif
                    </p>
                  </span>
                </div>
                <p style="margin-left: 60px">
                  {{$totales}} {{trans('hospital.OPERACIONESPROGRAMADAS')}}
                </p>
              </div>
            </div>
          </div>
          <div class="col-md-6 my-3">
            <h5 class="card info-color py-3" style="border-radius:30px;background: linear-gradient(to right, #3352ff 0%, #051eff 100%)">
              <div class="row">
                <div class="col-md-8">
                  <strong class="text-white">
                    <img src="{{asset('/')}}hc4/img/historiaclinic.png" class="float-left mr-3 ml-3" style="width: 25px;">
                    {{trans('hospital.HistoriaClinicaporPaciente')}}
                  </strong>
                </div>
                <div class="col-md-3">
                  <a class="btn btn-primary" style="border: 2px solid white !important;" href="{{asset('/')}}">Sistema Ambulatorio</a>
                </div>
              </div>
            </h5>
            <div class="card-body" style="border: blue 2px solid; border-radius: 20px;">
              <button onclick="location.href='{{route('hospital.agregarpa')}}'" class="btn btn-primary" type="submit">
                <i class="material-icons float-left">person_add</i>{{trans('hospital.AgregarNuevoPaciente')}}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="card card-solid">
    <div id="navbar" class="row" style="z-index: 99999">
      <div class="col-lg-12 col-md-12">
        <div class="row row_datos">
          <div class="col-lg-4 col-sm-12 col-12">
            <div class="row">
              <div class="col-12" style="padding: 5px 2px; padding-left: 30px; height: 240px;">
                <a onclick="" class="boton calendario" style="color: #124574; height: 230px">
                  <div class="row">
                    <div class="col-12" style="height: 42px; text-align: left;color: #124574;">
                      <img src="{{asset('/')}}hc4/img/bt_ca2.png" style="background-color: none;height: 46px;">
                      <span></span>
                    </div>
                    <hr style="background-color: #124574;">
                    <div class="col-12">
                      <div class="row">
                        <div class="col-md-6 col-12" style="padding: 5px;color:#124574;">
                          <div class="row">
                            <div class="col-12">
                              <div class="row">
                                <div class="col-1">&nbsp;</div>
                                <div class="col-12" style="text-align: left;">
                                  <span style="color: #124574; font-size: 14px;"><b>@lang('hospital.Recepcion')</b></span>
                                </div>
                              </div>
                            </div>
                            <div class="col-12" style="height: 4px; ">
                            </div>
                            <div class="col-12">
                              <div class="row">
                                <div class="col-1"></div>
                                <div class="col-4">
                                  <p class="align-middle" style='text-align: center;margin:0;line-height: 1;color: #124574;'><span><b style="font-size: 30px;">{{date('d')}}</b></span><br><span style="line-height: 1; font-size: 16px; text-align: center"> @if(date('N') == 1) @lang('hospital.lunes') @elseif(date('N') == 2) @lang('hospital.martes') @elseif(date('N') == 3) @lang('hospital.miercoles') @elseif(date('N') == 4) @lang('hospital.jueves') @elseif(date('N') == 5) @lang('hospital.viernes') @elseif(date('N') == 6) @lang('hospital.sabado') @elseif(date('N') == 7) @lang('hospital.domingo') @endif</span></p>
                                </div>
                                <div class="col-6" style="text-align: left;">
                                  <div style="height: 8px;"></div>
                                  <span style="font-size:14px; text-align: left; color:#124574;">@if(date('m') == 1) @lang('hospital.enero') @elseif(date('m') == 2) @lang('hospital.febrero') @elseif(date('m') == 3) @lang('hospital.marzo') @elseif(date('m') == 4) @lang('hospital.abril') @elseif(date('m') == 5) @lang('hospital.mayo') @elseif(date('m') == 6) @lang('hospital.junio') @elseif(date('m') == 7) @lang('hospital.julio') @elseif(date('m') == 8) @lang('hospital.agosto') @elseif(date('m') == 9) @lang('hospital.septiembre') @elseif(date('m') == 10) @lang('hospital.octubre') @elseif(date('m') == 11) @lang('hospital.noviembre') @elseif(date('m') == 12) @lang('hospital.diciembre') @endif {{date('Y')}}</span>
                                </div>
                              </div>
                            </div>
                            <hr style="height: 0px;">
                            <div class="col-12">
                              <div class="row">
                                <div class="col-1"></div>
                                <div class="col-12">
                                  <p style="font-size: 12px; text-align: left;"> </p>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-8 cambiar" style="line-height: 1;">
                        </div>
                      </div>
                    </div>
                  </div>
                  <span>
                    <p style="margin-left: 75px">
                      @if(date('N') == 1)
                      Lunes @elseif(date('N') == 2)
                      Martes @elseif(date('N') == 3)
                      Miercoles @elseif(date('N') == 4)
                      Jueves @elseif(date('N') == 5)
                      Viernes @elseif(date('N') == 6)
                      Sabado @elseif(date('N') == 7)
                      Domingo
                      @endif
                    </p>
                  </span>
                </div>
                <p style="margin-left: 60px">
                  {{$totales}} {{trans('hospital.OPERACIONESPROGRAMADAS')}}
                </p>
              </div>
            </div>
         
        </div>
        <div class="col-md-7">
           <div class="flexbox">
            <div style="color: #124574; background-image: linear-gradient(to right, #FFFFFF,#FFFFFF,#d1d1d1);border-radius: 10px;">
                <div class="row">
                    <div class="col-md-5 col-xs-6" style="margin-top:6px;padding-right: 5px;position: relative;margin-lef:0;margin-right:0">
                    <img class="image_baner" src="{{asset('/')}}hc4/img/icono_ emergencia.png" style="width: 95px;">
                  </div>
<<<<<<< HEAD
                  <div class="col-md-7 col-xs-6" style="color: #124574;display:flex;flex-direction:column;justify-content:center">
                   <p class="text_2" style="font-size: 15px"><b>@lang('hospital.Emergencia')</b></p>
                     
=======
                </a>
                <a href="{{route('uci.index')}}" class="boton" style="margin-top: 5px;  height: 112px; color: #124574; background-image: linear-gradient(to right, #FFFFFF,#FFFFFF,#d1d1d1);border-radius: 10px;">
                  <div class="row plantilla" style="text-align: center;">
                    <div class="col-md-5 col-xs-6" style="width: 100%;padding-left: 0;padding-right: 5px;">
                      <img class="image_baner" src="{{asset('/')}}hc4/img/exams_favoritos.png" style="width: 75px;">
                    </div>
                    <div class="col-md-7 col-xs-6" style="width: 100%; color: #124574;padding-left: 0;">
                      <p style="font-size: 13px" class="text_2"><b>@lang('hospital.UCI')</b></p>
                    </div>
>>>>>>> cca234f58bebb76c88befa610be9fdafc9520570
                  </div>
                </div>
            </div>
             <div style="color: #124574; background-image: linear-gradient(to right, #FFFFFF,#FFFFFF,#d1d1d1);border-radius: 10px;">
                <div class="row">
                  <div class="col-md-5 col-xs-6" style="margin-top:6px;padding-right: 5px;position: relative;margin-lef:0;margin-right:0">
                    <img class="image_baner" src="{{asset('/')}}hc4/img/icono cirugia.png" style="width: 95px;">
                    </div>
                    <div class="col-md-7 col-xs-6" style="color: #124574;display:flex;flex-direction:column;justify-content:center">
                      <p style="font-size: 13px" class="text_2"><b>&nbsp;&nbsp;@lang('hospital.Cirugia')</b></p>
                  </div>
                </div>
            </div>

            <div style="color: #124574; background-image: linear-gradient(to right, #FFFFFF,#FFFFFF,#d1d1d1);border-radius: 10px;">
                <div class="row">
                    <div class="col-md-5 col-xs-6" style="margin-top:6px;padding-right: 5px;position: relative;margin-lef:0;margin-right:0">
                    <img class="image_baner" src="{{asset('/')}}hc4/img/icono laboratorio.png" style="width: 95px;">
                    </div>
                   <div class="col-md-7 col-xs-6" style="color: #124574;display:flex;flex-direction:column;justify-content:center">
                      <p class="text_2" style="font-size: 15px"><b>@lang('hospital.Laboratorio')</b></p>
                   
                  </div>
                </div>
            </div>

            <div style="color: #124574; background-image: linear-gradient(to right, #FFFFFF,#FFFFFF,#d1d1d1);border-radius: 10px;">
                <div class="row">
                    <div class="col-md-5 col-xs-6" style="margin-top:6px;padding-right: 5px;position: relative;margin-lef:0;margin-right:0">
                   <img class="image_baner" src="{{asset('/')}}hc4/img/icono farmacia.png" style="width: 95px;">
                    </div>
                   <div class="col-md-7 col-xs-6" style="color: #124574;display:flex;flex-direction:column;justify-content:center">
                      <p style="font-size: 13px" class="text_2"><b>@lang('hospital.Farmacia')</b></p>
                  </div>
                </div>
            </div>

            <div style="color: #124574; background-image: linear-gradient(to right, #FFFFFF,#FFFFFF,#d1d1d1);border-radius: 10px;">
                <div class="row">
                    <div class="col-md-5 col-xs-6" style="margin-top:6px;padding-right: 5px;position: relative;margin-lef:0;margin-right:0">
                    <img class="image_baner" src="{{asset('/')}}hc4/img/icono imagenes.png" style="width: 95px;">
                    </div>
                   <div class="col-md-7 col-xs-6" style="color: #124574;display:flex;flex-direction:column;justify-content:center">
                      <p class="text_2" style="font-size: 15px"><b>@lang('hospital.Imagenes')</b></p>
                      <!-- <p class="text_2" style="font-size: 15px"><b></b> @lang('hc4.ordenes')</p> -->
                  </div>
                </div>
            </div>

            <div  style="color: #124574; background-image: linear-gradient(to right, #FFFFFF,#FFFFFF,#d1d1d1);border-radius: 10px;">
                <div class="row">
                    <div class="col-md-5 col-xs-6" style="margin-top:6px;padding-right: 5px;position: relative;margin-lef:0;margin-right:0">
                   <img class="image_baner" src="{{asset('/')}}hc4/img/icono UCIN.png" style="width: 95px;">
                    </div>
                    <div class="col-md-7 col-xs-6" style="color: #124574;display:flex;flex-direction:column;justify-content:center">
                      <p style="font-size: 13px" class="text_2"><b>&nbsp;&nbsp;&nbsp;&nbsp;@lang('hospital.UCIN')</b></p>
                  </div>
                </div>
            </div>

             <div  style="color: #124574; background-image: linear-gradient(to right, #FFFFFF,#FFFFFF,#d1d1d1);border-radius: 10px;">
                <div class="row">
                    <div class="col-md-5 col-xs-6" style="margin-top:6px;padding-right: 5px;position: relative;margin-lef:0;margin-right:0">
                     <img class="image_baner" src="{{asset('/')}}hc4/img/icono UCI.png" style="width: 95px;">
                </div>
                <div class="col-md-7 col-xs-6" style="color: #124574;display:flex;flex-direction:column;justify-content:center">
                  <p style="font-size: 13px" class="text_2"><b>&nbsp;&nbsp;&nbsp;&nbsp;@lang('hospital.UCI')</b></p>
                  </div>
                </div>
            </div>

             <div  style="color: #124574; background-image: linear-gradient(to right, #FFFFFF,#FFFFFF,#d1d1d1);border-radius: 10px;">
                <div class="row">
                    <div class="col-md-5 col-xs-6" style="margin-top:6px;padding-right: 5px;position: relative;margin-lef:0;margin-right:0">
                    <img class="image_baner" src="{{asset('/')}}hc4/img/icono recepcion.png" style="width: 95px;">
                  </div>
                  <div class="col-md-7 col-xs-6" style="color: #124574;display:flex;flex-direction:column;justify-content:center">
                    <p class="text_2" style="font-size: 15px"><b>@lang('hospital.Recepcion')</b></p>
       
                  </div>
                </div>
            </div>

             <div  style="color: #124574; background-image: linear-gradient(to right, #FFFFFF,#FFFFFF,#d1d1d1);border-radius: 10px;">
                <div class="row">
                    <div class="col-md-5 col-xs-6" style="margin-top:6px;padding-right: 5px;position: relative;margin-lef:0;margin-right:0">
                    <img class="image_baner" src="{{asset('/')}}hc4/img/icono hospitalizacion.png" style="width: 95px;">
                  </div>
                  <div class="col-md-7 col-xs-6" style="color: #124574;display:flex;flex-direction:column;justify-content:center">
                    <p class="text_2" style="font-size: 15px"><b>@lang('hospital.Hospitalizacion')</b></p>
                  
                  </div>
                </div>
            </div>

             <div  style="color: #124574; background-image: linear-gradient(to right, #FFFFFF,#FFFFFF,#d1d1d1);border-radius: 10px;">
                <div class="row">
                    <div class="col-md-5 col-xs-6" style="margin-top:6px;padding-right: 5px;position: relative;margin-lef:0;margin-right:0">
                    <img class="image_baner" src="{{asset('/')}}hc4/img/icono_omni.png" style="width: 95px;">
                  </div>
                  <div class="col-md-7 col-xs-6" style="color: #124574;display:flex;flex-direction:column;justify-content:center">
                    <p class="text_2" style="font-size: 15px"><b>&nbsp;&nbsp;&nbsp;Omni</b></p>
                   
                  </div>
                </div>
            </div>
           </div>

          
        </div>
      </div>
   </div>
</section>


<script type="text/javascript">
  $("#fecha").change(function() {
    alert($("#fecha").val());
  });

  function enviar_enter(e) {
    //alert('entra1');
    tecla = (document.all) ? e.keyCode : e.which;
    if (tecla == 13) {
      buscador_paciente_fecha();
    };
  }

  function cambio_fecha() {
    alert('cambio');
  }
</script>

<script type="text/javascript" src="{{ asset ("/librerias/moment.min.js")}}"></script>
<script type="text/javascript" src="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.js")}}"></script>

<script type="text/javascript">
  $('#example2').DataTable({
    'paging': false,
    'lengthChange': false,
    'searching': false,
    'ordering': true,
    'info': false,
    'autoWidth': false
  })
</script>
<script type="text/javascript">
  $(document).ready(function() {

    (function($) {

      $('#filtrar').keyup(function() {

        var rex = new RegExp($(this).val(), 'i');

        $('.buscar tr').hide();

        $('.buscar tr').filter(function() {
          return rex.test($(this).text());
        }).show();

      })

    }(jQuery));

  });

 
</script>
@endsection