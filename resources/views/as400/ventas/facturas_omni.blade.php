@extends('contable.ventas.base')
@section('action-content')
<style type="text/css">
  table tr td {
    font-size: 10px;
  }

  th {
    font-size: 12px;
  }

  .sticky {
    position: fixed;
    top: 0;
    left: 100px;
    width: 100%;
    max-height: 125%;

  }

  .sticky+.content {
    padding-top: 55px;
  }

  @media screen and (max-width:700px) {
    /* reglas CSS */

    .cambiar {
      display: none !important;
    }

    .sticky {
      position: relative !important;
    }

  }
</style>
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<div class="modal fade" id="buscador" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document" style="width: 60%;">
    <div class="modal-content">

    </div>
  </div>
</div>

<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item"><a href="#">Ventas</a></li>
      <li class="breadcrumb-item active" aria-current="page">Facturación OMNI</li>

    </ol>
  </nav>

  <div class="box">
    <div class="box-header">
    </div>
    <div class="box-body">
      <form method="POST" id="reporte_master">
        {{ csrf_field() }}
        <div class="form-group col-md-3 col-xs-5" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="col-md-2 control-label">Desde</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group col-md-3 col-xs-5" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha_hasta" class="col-md-2 control-label">Hasta</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>
        <div class="form-group col-md-6 col-xs-6">
          <label for="nombres" class="col-md-3 control-label">Paciente</label>
          <div class="col-md-9">
            <div class="input-group">
              <input @if(isset($request['nombres'])) value="{{$request['nombres']}}" @endif type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="APELLIDOS - NOMBRES" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('nombres').value = '';"></i>
              </div>
            </div>
          </div>
        </div>

        <!--div class="form-group col-md-3 col-xs-5" style="padding-left: 0px;padding-right: 0px;">
          <label class="col-md-2 control-label">Caja</label>
          <div class="col-md-9">
              <select class="form-control input-sm" name="caja" id="caja">
               <option value="" >Todas</option>
               <option @if($request['caja'] == "Torre 1") selected="selected" @endif  value="Torre 1" >Torre 1</option>
               <option @if($request['caja'] == "Torre 2") selected="selected" @endif value="Torre 2">Torre 2</option>
              </select>
          </div>
        </div-->
        @if(isset($doctores))
        <div class="form-group col-md-3 col-xs-3" style="padding-left: 0px;padding-right: 0px;">
          <label class="col-md-2 control-label">Doctor</label>
          <div class="col-md-9">
            <select class="form-control input-sm" name="doctor" id="doctor">
              <option value="">Seleccione ...</option>
              @foreach($doctores as $doctor)
              <option @if($doctor->id==$request->doctor) selected @endif value="{{$doctor->id}}">{{$doctor->apellido1}} {{$doctor->apellido2}} {{$doctor->nombre1}}</option>
              @endforeach
            </select>
          </div>
        </div>
        @endif
        <!--
        <div class="form-group col-md-3 col-xs-5" style="padding-left: 0px;padding-right: 0px;">
          <label class="col-md-2 control-label">Tipo</label>
          <div class="col-md-9">
            <select class="form-control input-sm" name="tipo" id="tipo">
              <option value="">Seleccione ...</option>
              <option @if($request->tipo=='4') selected @endif value="4">VISITAS</option>
              <option @if($request->tipo=='1') selected @endif value="1">PROCEDIMIENTOS</option>
            </select>
          </div>
        </div>-->
        <div class="form-group col-md-3">
          <label for="tipo" class="control-label col-md-3">Tipo</label>

          <div class="col-md-9">
            <select name="tipo" id="tipo" class="form-control">
              <option @if($request->tipo==null) selected="selected" @endif value="">Seleccione...</option>
              <option value="0">Consulta</option>
              <option @if($request->tipo==1) selected="selected" @endif value="1">Procedimientos</option>
              <option @if($request->tipo==3) selected="selected" @endif value="3">Hospitalizados</option>
              <option @if($request->tipo==4) selected="selected" @endif value="4">Visitas</option>
            </select>
          </div>
        </div>
        <div class="form-group col-md-3">
          <label for="tipo" class="control-label col-md-3">Omni</label>

          <div class="col-md-9">
            <select name="omni" id="omni" class="form-control">
              <option value="">Seleccione...</option>
              <option @if($request->omni=="SI") selected="selected" @endif value="SI">SI</option>
              <option @if($request->omni=="NO") selected="selected" @endif value="NO">NO</option>

            </select>
          </div>
        </div>
        <div class="form-group col-md-3 px-0">
          <label class="col-md-3 control-label">Seguro</label>
          <div class="col-md-9">
            <select class="form-control" name="id_seguro[]" id="id_seguro" multiple="multiple">
              <option value="">Seleccione ...</option>
              @foreach($seguros as $seguro)
              @if(is_array($request->id_seguro))
              <option @if(in_array($seguro->id, $request->id_seguro)) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
              @else
              <option value="{{$seguro->id}}">{{$seguro->nombre}}</option>
              @endif
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group col-md-3 px-0">
          <label class="col-md-5 control-label">Procedimientos</label>
          <div class="col-md-7">
            <select class="form-control" name="procedimientos[]" id="procedimientos" multiple="multiple">
              <option value="">Seleccione ...</option>
              @foreach($procedimiento as $procedimientos2)
              @if(is_array($request->procedimientos2))
              <option @if(in_array($procedimientos2->id, $request->procedimiento)) selected @endif value="{{$procedimientos2->id}}">{{$procedimientos2->nombre}}</option>
              @else
              <option value="{{$procedimientos2->id}}">{{$procedimientos2->nombre}}</option>
              @endif
              @endforeach
            </select>
          </div>
        </div>




        <div class="form-group col-md-3 col-xs-3" style="text-align: left;">
          <button type="submit" formaction="{{ route('ventas.omni')}}" class="btn btn-primary btn-sm" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true" style="font-size: 16px">&nbsp;Buscar&nbsp;</span></button>
        </div>
        <div class="form-group col-md-12" style="text-align: center;">
          <div class="col-md-12" style="text-align: center;">
            <label>INFORME DE USO</label>
          </div>

          <button formaction="{{route('ventas.getReportUses')}}" class="btn btn-info btn-gray" formtarget="_blank"> <i class="fa fa-file-pdf-o"></i> </button>
          <button formaction="{{route('ventas.getReportUsesExcel')}}" class="btn btn-info btn-gray" formtarget="_blank"> <i class="fa fa-file-excel-o"></i> </button>

        </div>

        <div class="form-group col-xs-12 text-center" id="ok" style="z-index: 999">

          <div class="col-md-12" style="text-align: center;">
            <label>ACCIONES</label>
          </div>


          <button type="button" class="btn btn-warning btn-gray btn_fact1 ">
            <i class="fa fa-medkit" aria-hidden="true"></i>&nbsp;&nbsp;Facturar Insumos
          </button>


          <button type="button" class="btn btn-success btn-gray btn_fact2">
            <i class="fa fa-stethoscope" aria-hidden="true"></i>&nbsp;&nbsp;Facturar Equipos
          </button>
          <!-- Only Dr. Robles -->
          @if($empresa->id=="1307189140001" || $empresa->id=="1314490929001" || $empresa->id=="32222222222")
           
          <button type="button" class="btn btn-default btn-gray btn_fact3">
            <i class="fa fa-user-md" aria-hidden="true"></i>&nbsp;&nbsp;Honorarios Medicos
          </button>
          @endif

        </div>

        <!--<div class="form-group col-md-2 col-xs-2" >
          
          <a type="button" href="{{route('cierrecaja.imprimir_excel')}}" class="btn btn-primary btn-sm" formtarget="_blank" >
            <span class="glyphicon glyphicon-download-alt" aria-hidden="true" style="font-size: 16px">&nbsp;Excel&nbsp;</span></a
          <button type="submit" formaction="{{route('cierrecaja.imprimir_excel')}}" class="btn btn-primary btn-sm" id="boton_buscar">
          <span class="glyphicon glyphicon-search" aria-hidden="true" style="font-size: 16px">&nbsp;Excel&nbsp;</span></button>
        </div>-->
      </form>

      <div class="form-group col-md-12">
        <div class="form-row">

          <div id="resultados">
          </div>
          <div id="contenedor">
            <form class="form-vertical" method="POST" id="form" action="{{route('ventas_viewOmni')}}">
              {{ csrf_field() }}
              <input type="hidden" id="tipo_fact" name="tipo_fact" value="1">
              <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap size_text">
                <div class="row">
                  <div class="table-responsive col-md-12">
                    <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                      <thead>
                        <tr>
                          <th width="14.28%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha Cita</th>
                          <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Paciente</th>
                          <th width="14.28%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Doctor</th>
                          <th width="14.28%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Procedimientos</th>
                          <th width="14.28%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Omni</th>
                          <th width="14.28%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tipo</th>
                          <th width="14.28%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Seguro</th>
                          <th><input type="checkbox" id="allItems" /></th>
                          <th>Facturado</th>

                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($procedimientos as $value)
                            @php
                            $verificar_insumo = DB::table('ct_factura_omni')->where('tipo_factura','1')->where('id_agenda',$value->id)->where('estado','<>','0')->where('id_empresa',$empresa->id)->first();
                            $verificar_equipo= DB::table('ct_factura_omni')->where('tipo_factura','2')->where('id_agenda',$value->id)->where('estado','<>','0')->where('id_empresa',$empresa->id)->first();
                            $verificar_honorarios= DB::table('ct_factura_omni')->where('tipo_factura','3')->where('id_agenda',$value->id)->where('estado','<>','0')->where('id_empresa',$empresa->id)->first();
                            @endphp
                            <tr>
                              <td>{{date('d-m-Y',strtotime($value->fechaini))}}</td>

                              <td> {{$value->paciente->apellido1}} {{$value->paciente->apellido2}} {{$value->paciente->nombre1}} {{$value->paciente->nombre2}} </td>
                              <td>Dr. {{$value->doctor1->nombre1}} {{$value->doctor1->apellido1}}</td>
                              <td>
                                @if(isset($value->historia_clinica))
                                @if(isset($value->historia_clinica->procedimiento))
                                @foreach($value->historia_clinica->procedimiento as $ps)
                                @if(isset($ps->hc_procedimiento_final->procedimiento))
                                {{$ps->hc_procedimiento_final->procedimiento->nombre}}+
                                @endif
                                @endforeach
                                @endif
                                @endif

                              </td>
                              <td> @if($value->omni!=null) @if($value->omni=="OM") SI  @else {{$value->omni}} @endif @elseif($value->proc_consul==4) SI @else NO @endif</td>
                              <td>
                                @if($value->proc_consul==0)
                                CONSULTA
                                @elseif($value->proc_consul==1)
                                PROCEDIMIENTO
                                @elseif($value->proc_consul==3)
                                HOSPITALIZADOS
                                @elseif($value->proc_consul==4)
                                VISITAS
                                @endif
                              </td>
                              @php 
                                $s= DB::table('seguros')->where('id',$value->seguro_final)->first();
                              @endphp
                              <td>{{$s->nombre}}</td>
                              <td> <input type="checkbox" class="form-col facturar" @if($verificar_equipo!=null && $verificar_insumo!=null) disabled @endif />
                                <input type="hidden" class="facturars" value="{{$value->id}}" />
                                <input type="hidden" class="pacientes" value="{{$value->paciente->id}}">
                              </td>
                              <td>

                                @if(!is_null($verificar_insumo))

                                @php
                                $ventas= DB::table('ct_ventas')->where('id',$verificar_insumo->id_ct_ventas)->first();
                                @endphp

                                <!--<div class="col-md-12">
                              if u want whatever icon 
                              
                              <i class="fa fa-medkit mr-1 green" aria-hidden="true"></i>
                              </div>-->
                                <div class="col-md-12" style="background-color: salmon; color: white;">
                                  <span> {{$ventas->nro_comprobante}} </span>

                                </div>

                                @endif
                                @if(!is_null($verificar_equipo))
                                @php
                                //dd($verificar_equipo);
                                $ventas2= DB::table('ct_ventas')->where('id',$verificar_equipo->id_ct_ventas)->first();
                                @endphp
                                  <div class="col-md-12" style=" background-color:seagreen; color: white;">
                                    <span> {{$ventas2->nro_comprobante}} </span>

                                  </div>
                                @endif

                                @if(!is_null($verificar_honorarios))
                                @php
                                //dd($verificar_equipo);
                                $ventas2= DB::table('ct_ventas')->where('id',$verificar_honorarios->id_ct_ventas)->first();
                                @endphp
                                  <div class="col-md-12" style="background-color:slateblue; color: white;">
                                    <span> {{$ventas2->nro_comprobante}} </span>

                                  </div>
                                @endif


                              </td>
                            </tr>

                            @endforeach

                      </tbody>
                      <tfoot>
                      </tfoot>
                    </table>

                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-5">
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{sizeof($procedimientos)}} registros</div>
                  </div>

                </div>

                <!--
                  <div class="col-md-3">
                    <button type="button" class="btn btn-default btn-gray btn_fact3">
                      <i class="fa fa-user-md" aria-hidden="true"></i>&nbsp;&nbsp;Honorarios Medicos
                    </button>
                  </div>-->

            </form>
          </div>
          <div class="row">
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('#example2').DataTable({
      'paging': false,
      'lengthChange': true,
      'searching': false,
      'ordering': false,
      'info': false,
      'autoWidth': false,
      'sInfoEmpty': true,
      'sInfoFiltered': true,
      'language': {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
      }
    });
  });
  $(function() {
    $('#fecha').datetimepicker({
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fecha}}',

    });
    $('#fecha_hasta').datetimepicker({
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fecha_hasta}}',
    });

    $("#fecha").on("dp.change", function(e) {
      $('#fecha_hasta').data("DateTimePicker").minDate(e.date);
    });

    $("#fecha_hasta").on("dp.change", function(e) {});


    $(function() {

      $(document).ready(function() {
        $('#id_seguro').select2();
        $('#procedimientos').select2();
      });
    });

    $(".btn_fact2").click(function(e) {
      $("#tipo_fact").val(2);
      $("#form").submit();

    });
    $(".btn_fact1").click(function(e) {
      //$("#tipo_fact").val(1);
      console.log($("#tipo_fact").val());
      //alert($("#tipo_fact").val());
      $("#form").submit();

    });

    $(".btn_fact3").click(function(e) {
      $("#tipo_fact").val(3);
      var noexisteProcedimiento = false;
      $('.facturar').each(function(i, obj) {
        //total_pagos = parseFloat(total_pagos) + parseFloat($(this).val());
        if ($(this).prop('checked')) {
          var copro = $(this).find('.t_fact').val();
          if (copro == 1) {
            noexisteProcedimiento = true;
          }
        }
      });
      // console.log(noexisteProcedimiento);
      if (noexisteProcedimiento) {
        $("#form").submit();
        console.log('submit');
      } else {
        console.log('no submit');

        Swal.fire({
          html: 'Esta seguro que desea facturar <br/>' +
            'Honorarios Médicos a ' +
            '{{$empresa->nombrecomercial}}',
          showCloseButton: true,
          showCancelButton: true,
          focusConfirm: false,
          confirmButtonText: 'Si',
          confirmButtonAriaLabel: 'Si',
          cancelButtonText: 'No',
          cancelButtonAriaLabel: 'Thumbs down'
        }).then((result) => {
          if (result.value) {
            /*Swal.fire(
              'Deleted!',
              'Your file has been deleted.',
              'success'
            )*/
            $("#form").submit();
          }
        });
      }


    });

    $('body').on('click', '#allItems', function() {
      if ($(this).prop('checked')) {

        //$('.fact_id').val(1);
        $('.facturar').each(function(i) {
          //aqui preguntar si esta disabled
          if ($(this).prop('disabled')) {

          } else {
            $(this).prop("checked", true);
            $(this).next().attr('name', 'id_agenda[]');
            $(this).next().next().attr('name', 'paciente[]');
          }

        });
        /*
        $('.facturars').attr('name', 'id_agenda[]');
        $('.pacientes').attr('name', 'paciente[]');*/
      } else {
        $('.facturar').prop("checked", false);
        //$('.fact_id').val(0);
      }
    });

    $('body').on('change', '.facturar', function() {
      //console.log($(this));
      if ($(this).prop('checked')) {
        //console.log("entra");
        $(this).parent().find('.facturars').attr('name', 'id_agenda[]');
        $(this).parent().find('.pacientes').attr('name', 'paciente[]');
      } else {
        $(this).parent().find('.facturars').attr('name', '');
        $(this).parent().find('.pacientes').attr('name', '');
      }
    });

  });
  window.onscroll = function() {
    myFunction()
  };

  var navbar = document.getElementById("ok");
  var sticky = navbar.offsetTop;

  function myFunction() {
    if (window.pageYOffset >= sticky) {
      navbar.classList.add("sticky")
    } else {
      navbar.classList.remove("sticky");
    }

  }
</script>
@endsection