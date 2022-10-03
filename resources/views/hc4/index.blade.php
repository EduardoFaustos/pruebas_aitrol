@extends('hc4.base')
@section('action-content')

<style type="text/css">

  .boton-1{
    font-size: 10px ;
    width: 20%;
    background-color: #004AC1;
    color: white;
    border-radius: 5px;
   }

   .boton-2{
    font-size: 10px ;
    width: 60%;
    background-color: #004AC1;
    color: white;
    border-radius: 5px;
   }

   .color{
    font-size: 12px;
    color: #004AC1;
   }
   .color2{
    color: white !important;
    background-color:  #004AC1 !important;
   }
   .titulo{
    font-family: 'Helvetica general' !important;
    border-bottom:  solid 1px white !important;
   }
   .oculto{
    display: none;

   }

</style>
<link rel="stylesheet" href="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.css")}}" />
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<div class="container-fluid" id="area_cambiar" >

  <div class="col-md-12">
    <div id="navbar" class="row"  style="z-index: 99999">
      <div class="col-lg-12 col-md-12 col-xs-6">
        <div class="row ">
          <div class="col-md-6 col-sm-12 col-12">
            <div class="row">
              <div class="col-sm-12 col-xs-1">
                <a onclick="" class="boton calendario"  style="color: #004AC1; height: 296px; width: 580px">
                  <div class="row">
                    <div class="col-12" style="height: 42px; text-align: left;">
                      <img src="{{asset('/')}}hc4/img/bt_ca2.png" style=" background-color: none;height: 46px; font-weight: bold;"><b>AGENDA DE QUIR&#211;FANO DEL D&#205A</b>
                      <span></span>
                    </div>
                    <hr style="background-color: #004AC1;">
                    <div class="col-12" >
                      <div class="row">
                        <div class="col-md-12 col-12" style="padding: 5px;">
                          <div class="row">
                            <div class="col-12">
                              <div class="row">
                                <div class="col-1">&nbsp;</div>
                                <div class="col-6" style="text-align: left;">
                                  <span style="color: #004AC1; font-size: 14  px; font-weight: bold;"><b>CALENDARIO DE AGENDA</b>
                                  </span>
                                </div>
                              </div>
                            </div>

                            <div class="col-12">
                              <div class="row">
                                <div class="col-1"></div>
                                  <div class="col-4">
                                    <p class="align-middle" style='text-align: center;margin:0;line-height: 1;color: #004AC1;'><span><b style="font-weight: bold; font-size: 60px; font-family: Helvetica;">{{date('d')}}</b></span><br><span style="line-height: 1; font-weight: bold; font-size: 16px; text-align: center"> @if(date('N') == 1) Lunes @elseif(date('N') == 2) Martes @elseif(date('N') == 3) Miercoles @elseif(date('N') == 4) Jueves @elseif(date('N') == 5) Viernes @elseif(date('N') == 6) Sabado @elseif(date('N') == 7) Domingo @endif</span></p>
                                  </div>
                                  <div class="col-6" style="text-align: left;">
                                    <div style="height: 8px;"></div>
                                    <span style="font-size:26px; font-weight: bold; font-family: Monserrat Medium; text-align: left; color: #004AC1;">@if(date('m') == 1) Enero @elseif(date('m') == 2) Febrero @elseif(date('m') == 3) Marzo @elseif(date('m') == 4) Abril @elseif(date('m') == 5) Mayo @elseif(date('m') == 6) Junio @elseif(date('m') == 7) Julio @elseif(date('m') == 8) Agosto @elseif(date('m') == 9) Septiembre @elseif(date('m') == 10) Octubre @elseif(date('m') == 11) Noviembre @elseif(date('m') == 12) Diciembre @endif {{date('Y')}}</span>
                                  </div>
                              </div>
                            </div>
                            <hr style="height: 0px;">
                            <div class="col-12">
                              <div class="row">
                                <div class="col-1"></div>
                                <div class="col-10">
                                  <p style="font-size: 15px; font-weight: bold; font-family: Monserrat Bold; text-align: left;">5 OPERACIONES PROGRAMADAS</p>
                                </div>
                              </div>
                            </div>

                          </div>
                        </div>
                        <div class="col-8" style="line-height: 1;">
                        </div>
                      </div>
                    </div>
                  </div>
                </a>
              </div>
            </div>
          </div>
          <!-- ETIQUETA DE HISTORIA CLINICA DE PACIENTE --->
            <div class="col-md-6  col-sm-6 col-6">
                <div class="row" style="position: relative;">
              <div class="col-12">
                <div class="col-sm-12" style="text-align: left; margin-top: 50px;">
                  <section  style=" font-family: Helvetica; margin: 2px; color: white; text-align:left; padding: 10px; border-radius: 30px; background-image: linear-gradient(to right,#004AC1,#0C8BEC,#004AC1);  margin-bottom: 15px;">
                    <h8><img src="{{asset('/')}}hc4/img/historiaclinic.png "  style=" font-family: Helvetica; text-align:left; border-radius: 30px; height:30px; width: 30px;"> &nbsp;&nbsp;<b>HISTORIA CL&Iacute;NICA POR PACIENTE</b></h8>
                     </section>
                </div>
              </div>
            </div>
              <div class="col-md-12 col-sm-12 col-12" style="border: #004AC1 2px solid; border-radius: 25px;">
                  <div class="row" style="position: relative;">
                    <div class="col-md-6">
                      <div class="col-6" style="text-align: center; margin-top: 30px;">
                        <input required maxlength="30" placeholder="APELLIDOS" style="font-family: Helvetica; width: 250px; height: 40px; text-align: center; border: #004AC1 2px solid; border-radius: 20px;">
                      </div>

                      <div class="col-6" style="text-align: center; margin-top: 30px;">
                         <input required maxlength="30" placeholder="NOMBRES" style="font-family: Helvetica; width: 250px; height: 40px; text-align: center; border: #004AC1 2px solid; border-radius: 20px;">
                      </div>
                    </div>

                    <div class="col-md-6">
                     <div class="col-md-12" style =" margin-top: 20px;">
                       <button type="button" style=" font-family: Monserrat Medium; background-color: #004AC1; font-family: Monserrat Medium; margin: 2px;color: white; text-align: center; padding: 10px; border-radius: 30px; margin-bottom: 15px;height:50px; width: 200px;">
                        <img src="{{asset('/')}}hc4/img/busqueda.png" style="width: 20px; text-align:right ;"> &nbsp;&nbsp;<b>BUSCAR</b>
                         </button>
                     </div>

                       <!--AGREGAR UN NUEVO PACIENTE-->
                      <div class="col-md-12" style =" margin-top: 5px;">
                       <button type="button" style=" font-family: Monserrat Bold; background-color: #004AC1; padding: 12px; margin: 2px; color: white; text-align: center; padding: 1px; border-radius: 30px;   margin-bottom: 15px; height: 50px; width: 200px;">
                        <b>AGREGAR NUEVO PACIENTE</b>
                         </button>
                     </div>
                     
                    </div>


                  </div>
                </div>
            </div>
            </div>
        </div>

<section class="content">
  <div class="box">
    <div class="box-header with-border" style="font-family: Helvetica;color: white; margin-top: 5px; padding: 10px; border-radius: 30px; background-image: linear-gradient(to right, #004AC1,#004AC1,#004AC1); margin-bottom: 5px">
              <h3 class="box-title">RESULTADOS DE LA B&Uacute;SQUEDA</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
                  <i class="fa fa-minus"></i></button>
              </div>
    </div>
        <div class="box-body" style="">
          <div id="area_cambiar2" class="col-md-12" style = " border: 2px solid #004AC1; height: 900px;">
                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap" >
                <div class="row">
                  <div class="table-responsive col-md-12">
                    <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="
                   margin-right: 1400px;">
                      <thead>
                        <tr role="row">
                          <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Nombre</th>
                          <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Descripcion</th>
                          <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Estado</th>
                          <th  width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Acción</th>
                        </tr>
                      </thead>
                      <tbody>
                          <tr role="row" class="odd">
                            <td>Anthony Chilán</td>
                            <td>Desarrollo</td>
                            <td>Activado</td>
                            <td><a type="button" class="btn btn-primary">Ver</a></td>
                          </tr>
                          <tr role="row" class="odd">
                            <td>Anthony Chilán</td>
                            <td>Desarrollo</td>
                            <td>Activado</td>
                            <td><a type="button" class="btn btn-primary">Ver</a></td>
                          </tr>
                          <tr role="row" class="odd">
                            <td>Fausto Javier</td>
                            <td>Desarrollo</td>
                            <td>Activado</td>
                            <td><a type="button" class="btn btn-primary">Ver</a></td>
                          </tr>
                      </tbody>
                    </table>
                  </div>
                 </div>
                </div>
          </div>
        </div>
  </div>
</section>


   </div>
   <!-- <div class="box box" style="border-radius: 8px;" id="area_trabajo">

    </div>-->
    </div>
  </div>

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
    function cargarfarmacia(){
      $.ajax({
        type: "GET",
        url: "{{route('hc4.farmacia')}}",
        data: "",
        datatype: "html",
        success: function(datahtml){
          $("#area_cambiar").html(datahtml);


        },
        error:  function(){
          alert('error al cargar');
        }
      });
    }
</script>
 <script type="text/javascript">
    function cargargcuartos(){
      $.ajax({
        type: "GET",
        url: "{{route('hc4.gcuartos')}}",
        data: "",
        datatype: "html",
        success: function(datahtml){
          $("#area_cambiar").html(datahtml);


        },
        error:  function(){
          alert('error al cargar');
        }
      });
    }
</script>
<script type="text/javascript">
    function cargaradmcuarto(){
      $.ajax({
        type: "GET",
        url: "{{route('hc4.admcuarto')}}",
        data: "",
        datatype: "html",
        success: function(datahtml){
          $("#area_cambiar").html(datahtml);


        },
        error:  function(){
          alert('error al cargar');
        }
      });
    }
</script>


</section>
@endsection
