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

  .align-middle{
    position: absolute;
    top: 60%;
    left: 40%;
    transform: translate(-50%, -50%);
    color: blue;
  }

  h5, p{
  }
  span b{
    font-size: 50px;
  }
  span p{
    font-size: 30px;
  }
  .box-title{
    color: white;
    
  }
  /*colapse*/
  .panel-group .panel {
    border-radius: 0;
    box-shadow: none;
    border-color: #EEEEEE;
  }
  .box{
    border-color: #FDFEFE; border-radius: 30px;
  }
  .label1{
      font-size: 14px; color:white;
    }

  @media all and (max-width: 420px){
    h5, p{
      font-size: 12px;
      margin-top: 0px;
      margin-bottom: 0px;
    }
    span{
      font-size: 10px;
    }
    span b{
      font-size: 20px;
    }
    span p{
      font-size: 12px;
    }
  }

  @media all and (max-width: 850px){
    .col-md-6{
      margin-top: 0px;
    }
    h5, p{
      font-size: 12px;
      margin-top: 0px;
      margin-bottom: 0px;
    }
    span{
      font-size: 10px;
    }
    span b{
      font-size: 20px;
    }
    span p{
      font-size: 12px;
    }
    .label1{
      font-size: 10px; color:white;
    }
    .text1,.text2{
      margin-top:5px
    }
  }
  @media all and (max-width: 1030px){
    .col-md-6{
      margin-top: 0px;
    }
    h5, p{
      font-size: 12px;
      margin-top: 0px;
      margin-bottom: 0px;
    }
    span{
      font-size: 10px;
    }
    span b{
      font-size: 20px;
    }
    span p{
      font-size: 12px;
    }
  }
  
</style>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.css")}}" />
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<section class="content">
  <br> <br>
    <div class="card card-solid">
      <div class="row">
        <div class="col-md-12">
          <div class="row">
            <div class="col-md-6">
              <br>
              <div class="panel text-blue" >
                <img src="{{asset('/')}}hc4/img/agenda_quirofano.png" class="card-img" alt="" style = "width: 100%;">
                <div class="card-img-overlay">
                  <h5 style = "border-bottom: 1px solid blue; margin-top: 25px" class="card-title"><img src="{{asset('/')}}hc4/img/bt_ca2.png" style = "width:5%; margin-left: 5%"> AGENDA DE QUIR&Oacute;FANO DEL D&Iacute;A</h5>
                  <p class="card-text" style = "margin-left: 15%">{{trans('hospital.CALENDARIODEAGENDA')}}</p>
                  <div class="row">
                    <div class="col-12">
                      <div class="row">
                        <span>
                          <b style = "margin-left: 75px">{{date('d')}}</b>
                        </span>
                        <span>
                          <p style = "margin-left: 75px">
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
                        <p style = "margin-left: 75px">
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
                  <p style = "margin-left: 60px"> 
                    {{$totales}}  {{trans('hospital.OPERACIONESPROGRAMADAS')}}
                  </p>
                </div>
              </div>
            </div>
            
            <!-- Material form register -->
            <div class="col-md-6 my-3">

              <h5 class="card info-color py-3" style = "border-radius:30px;background: linear-gradient(to right, #3352ff 0%, #051eff 100%)">
                <div class="row">
                  <div class="col-md-8">
                    <strong class = "text-white">
                      <img src="{{asset('/')}}hc4/img/historiaclinic.png" class = "float-left mr-3 ml-3" style="width: 25px;">
                      {{trans('hospital.HistoriaClinicaporPaciente')}}
                    </strong>
                  </div>
                  <div class="col-md-3">
                    <a class="btn btn-primary" style="border: 2px solid white !important;" href="{{asset('/')}}">Sistema Ambulatorio</a>    
                  </div>
                </div>
              </h5>

              <!--Card content-->
              <div class="card-body" style="border: blue 2px solid; border-radius: 20px;">
                    <!-- <div class="form-group">
                      <label>{{trans('hospital.Buscar')}}</label>
                      <input id="filtrar" type="text" class="form-control" placeholder="Nombre y Apellido">
                      <small class="form-text text-muted">{{trans('hospital.BuscarporNombredelPaciente')}}</small>
                    </div> -->
                  <button onclick="location.href='{{route('hospital.agregarpa')}}'" class="btn btn-primary" type="submit">
                        <i class="material-icons float-left">person_add</i>{{trans('hospital.AgregarNuevoPaciente')}}
                    </button>
                  
              </div>

            </div>
          </div>
        </div>
      </div>

      <!--
      <div class="col-md-12 my-3">
        <div  class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        
          <div class="card">
            <div class="card-header with-border"style="border-radius: 30px;  margin-bottom: 5px">
              <h1 class="card-title" style="font-size: 16px">{{trans('hospital.ResultadosdelaBusqueda')}}</h1>

              
            </div>
            <div  class="card-body table-responsive">
              <table class="table table-hover">
                              <thead class="table-primary">
                                  <tr> 
                                    <th scope="col">{{trans('hospital.CeduladelPaciente')}}</th>
                                      <th scope="col">{{trans('hospital.NombresdelPacienteIngresado')}}</th>
                                      <th scope="col">{{trans('hospital.ApellidosdelPaciente')}} </th>
                                      <th scope="col">{{TRANS('hospital.FechadeIngresodelPaciente')}}</th>
                                      <th scope="col">{{trans('hospital.NumerodeCama')}}</th>
                                  </tr>
                              </thead>
                               @foreach($nombres as $value)
                              <tbody class="buscar">
                                  <tr>
                                   
                                      <td>{{$value->id_paciente}}</td>
                                      <td>{{$value->nombre1}} {{$value->nombre2}}</td>
                                      <td>{{$value->apellido1}} {{$value ->nombre2}}</td>
                                      <td>{{$value->created_at}}</td>
                                      <td>{{$value->id_cama}}</td>
                                    

                                  </tr>
                              </tbody>
                               @endforeach
                          </table>
                          </div>
                        </div>
                        <!--FINAL DEL 1.-COLLAPSE--RESULTADO DE LA BUSQUEDA-->
                      </div>
               
                    <!--FIN DE COLLAPSE-->
                  </div>
                </div>
</section>

<!--RESULTADO DE BUSQUEDA-->
<script type="text/javascript">
  $("#fecha").change(function(){
    alert($("#fecha").val());
  });
  function enviar_enter(e){
    //alert('entra1');
      tecla = (document.all) ? e.keyCode : e.which;
      if (tecla==13){
        buscador_paciente_fecha();
      };
  }
  function cambio_fecha(){
    alert('cambio'); 
  } 
</script>
  
<script type="text/javascript" src="{{ asset ("/librerias/moment.min.js")}}"></script>
<script type="text/javascript" src="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.js")}}"></script>
  
<script type="text/javascript">
  $('#example2').DataTable({
    'paging'      : false,
    'lengthChange': false,
    'searching'   : false,
    'ordering'    : true,
    'info'        : false,
    'autoWidth'   : false
  })
 </script>
<script type="text/javascript">
$(document).ready(function () {
 
    (function ($) {
 
        $('#filtrar').keyup(function () {
 
             var rex = new RegExp($(this).val(), 'i');
 
             $('.buscar tr').hide();
 
             $('.buscar tr').filter(function () {
               return rex.test($(this).text());
             }).show();
 
        })
 
    }(jQuery));
 
});
</script>
@endsection