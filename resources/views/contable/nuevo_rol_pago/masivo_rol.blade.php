@extends('contable.rh_reporte_roles.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<style type="text/css">
  .table-bordered>tbody>tr>td{
    font-size: 9px;
  }
  .form-group{
    margin-bottom: 1px;
  }
  .treeview-menu>li.active>a, .skin-blue .treeview-menu>li>a:hover
  {
    color: blue !important;
  }
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
  
<section class="content">
  <!-- Ventana modal editar -->
  <div class="modal fade" id="detalle_proceso" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">

      </div>
    </div>
  </div>
  <div class="box">
    <div class="row head-title">
      <div class="col-md-12 cabecera">
          <label class="color_texto" for="title">{{trans('nomina.control_rol_empresa')}}</label>
      </div>
    </div>
    <span style="color: red;">{{$err}}</span>
    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="roles_pago"  action="{{route('nuevo_rol.masivo_search')}}"> 
        {{ csrf_field() }}
          
        <div class="form-group col-md-3 col-xs-2" style="padding: 1px;">
          <label class="form-group col-md-12" for="id_empresa">{{trans('nomina.empresa')}}: </label>
          <div class="form-group col-md-12 col-xs-10">
            <select class="form-control input-sm" id="id_empresa" name="id_empresa">
                <!--option value="">Seleccione...</option--> 
                @foreach($empresas as $value)
                  <option value="{{$value->id}}" @if($id_empresa==$value->id) selected @endif>{{$value->nombrecomercial}}</option>
                @endforeach
            </select>
          </div>
        </div>  
        <div class="form-group col-md-1 col-xs-2" style="padding: 1px;">
          <label class="form-group col-md-12" for="anio">{{trans('nomina.anio')}}</label>
          <div class="form-group col-md-12 col-xs-10">
            <select id="anio" name="anio" class="form-control input-sm">
              <!--option value="">Seleccione...</option-->  
                @for($i=2019;$i<=date('Y');$i++)
                  <option value="{{$i}}" @if($anio==$i) selected @endif>{{$i}}</option>"
                @endfor
            </select>
          </div>
        </div>  
        @php $meses = [ trans('nomina.enero'), trans('nomina.febrero'), trans('nomina.marzo'), trans('nomina.abril'), trans('nomina.mayo'), trans('nomina.junio'), trans('nomina.julio'), trans('nomina.agosto'), trans('nomina.septiembre'), trans('nomina.octubre'), trans('nomina.noviembre'), trans('nomina.diciembre') ]; @endphp
        <div class="form-group col-md-2 col-xs-2" style="padding: 1px;">
          <label class="form-group col-md-12" for="mes">{{trans('contableM.mes')}}</label>
          <div class="form-group col-md-12 col-xs-10">
            <select id="mes" name="mes" class="form-control input-sm">
              <!--option value="">Seleccione...</option-->  
                @for ($i=1; $i<=12; $i++)
                  <option value="{{$i}}" @if($i==$mes) selected @endif>{{$meses[($i)-1]}}</option>
                @endfor
            </select>
          </div>
        </div>
        <div class="form-group col-md-3 col-xs-2" style="padding: 1px;">
          <label class="form-group col-md-12" for="mes">{{trans('nomina.empleado')}}</label>
          <div class="form-group col-md-12 col-xs-10">
            <input class="form-control input-sm" type="text" name="empleado" id="empleado" value="{{$empleado}}">
          </div>
        </div>  
        <div class="form-group col-md-1 col-xs-2"> 
          <br>
          <button type="submit"> {{trans('nomina.buscar')}} </button>
          <!--button type="button" onclick="descargar_reporte();"  class="btn btn-primary" id="btn_exportar">
            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar Rol Pago
          </button-->
        </div>
        <div class="form-group col-md-2 col-xs-2"> 
          <br>
          <button type="button" onclick="generar_roles();"><span class="glyphicon glyphicon-triangle-right" aria-hidden="true"></span> {{trans('nomina.generar_roles')}}</button>
        </div>
        <div class="form-group col-md-12 col-xs-2"> </div>
        <div class="form-group col-md-3 col-xs-2" style="padding: 1px;">
          <label class="form-group col-md-12" for="mes">{{trans('nomina.archivo')}}</label>
          <div class="form-group col-md-12 col-xs-10">
            <input name="archivo" id="archivo" type="file" class="archivo form-control input-sm" />
          </div>
        </div> 
        <div class="form-group col-md-2 col-xs-2" > 
          <br>
          <button type="button" onclick="subir_horas_extras();"><span class="glyphicon glyphicon-triangle-right" aria-hidden="true"></span> {{trans('nomina.subir_horas_extras')}}</button>
        </div>
        
        <div class="form-group col-md-2 col-xs-2" style="padding: 1px;">
          <label for="prestamos" class="form-group col-md-12">{{trans('nomina.prestamos')}}</label>
          <div class="form-group col-md-12 col-xs-10">
            <select class="form-control input-sm" name="prestamos" id="prestamos">
              <option value="1">{{trans('nomina.quirografario')}}</option>
              <option value="2">{{trans('nomina.hipotecario')}}</option>
            </select>
          </div>
        </div> 

        <div class="form-group col-md-2 col-xs-2" > 
          <br>
          <button type="button" onclick="subir_prestamos();"><span class="glyphicon glyphicon-triangle-right" aria-hidden="true"></span> {{trans('nomina.subir_prestamos')}}</button>
        </div>
      </form> 
    </div>
       
      
      @if($anio!=null && $mes!=null)

        <ul data-widget="tree">
          <li class="treeview">
            <!--a href="#">{{$anio}}-{{$meses[$mes - 1]}}</a-->
            <a href="javascript:mostrar_detalle_prop()" id="id_det" class="ocultar">{{$anio}}-{{$meses[$mes - 1]}}</b></a>
            <ul class="treeview-menu">
              @foreach($procesos as $proceso)
              <li class="detail" style="display: none;">
                @if($proceso->tipo_proceso=='HORAS_EXTRAS_POR_EMPRESA')
                <a href="{{route('nuevo_rol.detalle_he_rol',['id' => $proceso->id])}}" data-toggle="modal" data-target="#detalle_proceso" >
                @elseif($proceso->tipo_proceso=='PRESTAMOS_QUIRO_POR_EMPRESA')
                <a href="{{route('nuevo_rol.detalle_iess_rol',['id' => $proceso->id])}}" data-toggle="modal" data-target="#detalle_proceso" >
                @elseif($proceso->tipo_proceso=='PRESTAMOS_HIPOT_POR_EMPRESA')
                <a href="{{route('nuevo_rol.detalle_iess_rol',['id' => $proceso->id])}}" data-toggle="modal" data-target="#detalle_proceso" >  
                @else
                <a href="#" >
                @endif 
                {{$proceso->tipo_proceso}}:</a> Procesados:{{$proceso->procesados}} - No Procesados:{{$proceso->no_procesados}} {{$proceso->observacion}}
              </li>
              @endforeach
            </ul>
          </li>
        </ul>
      @endif

      <div class="box box" style="border-radius: 8px;" id="area_trabajo">
        
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
          <div class="row">
            <div class="table-responsive col-md-12">
              <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info" style="width: 100%">
                <thead>
                  <tr class='well-dark'>
                    <th width="10%">{{trans('nomina.empresa')}}</th>
                    <th width="5%">{{trans('nomina.anio')}}/{{trans('nomina.mes')}}</th>
                    <th width="20%">{{trans('nomina.empleado')}}</th>
                    <th width="5%">{{trans('nomina.sueldo')}}</th>
                    <th width="5%">{{trans('nomina.extras')}} 50%</th>
                    <th width="5%">{{trans('nomina.extras')}} 100%</th>
                    <th width="5%">{{trans('nomina.ingresos')}}</th>
                    <th width="5%">{{trans('nomina.prestamos')}}</th>
                    <th width="5%">{{trans('nomina.saldos')}}</th>
                    <th width="5%">{{trans('nomina.quirografario')}}</th>
                    <th width="5%">{{trans('nomina.hipotecario')}}</th>
                    <th width="5%">{{trans('nomina.egresos')}}</th>
                    <th width="5%">{{trans('nomina.neto')}}</th>
                    <th width="5%" style="text-align: center;">{{trans('nomina.aprobado')}} <input type="checkbox" name="cert_mas" id="cert_mas" onclick="certificar_masivo()"></th>
                    <th width="10%" style="text-align: center;">{{trans('nomina.accion')}} &nbsp;<a  class="btn btn-primary  btn-xs" onclick="reenviar_mail_masivo()"><span class="fa fa-envelope"></span></a></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($roles as $rol)
                    @php 
                      $detalle = $rol->detalle->first(); 
                      if($rol->mes == '1') $rmes = trans('nomina.enero');
                      elseif($rol->mes == '2') $rmes = trans('nomina.febrero');
                      elseif($rol->mes == '3') $rmes = trans('nomina.marzo');
                      elseif($rol->mes == '4') $rmes = trans('nomina.abril');
                      elseif($rol->mes == '5') $rmes = trans('nomina.mayo');
                      elseif($rol->mes == '6') $rmes = trans('nomina.junio');
                      elseif($rol->mes == '7') $rmes = trans('nomina.julio');
                      elseif($rol->mes == '8') $rmes = trans('nomina.agosto');
                      elseif($rol->mes == '9') $rmes = trans('nomina.septiembre');
                      elseif($rol->mes == '10') $rmes = trans('nomina.octubre');
                      elseif($rol->mes == '11') $rmes = trans('nomina.noviembre');
                      elseif($rol->mes == '12') $rmes = trans('nomina.diciembre');
                    @endphp
                    <tr>
                      <td>{{$rol->empresa->nombrecomercial}}</td>
                      <td>{{$rol->anio}}-{{$rmes}}</td>
                      <td>{{$rol->usuario->apellido1}} {{$rol->usuario->apellido2}} {{$rol->usuario->nombre1}} {{$rol->usuario->nombre2}} <span style="font-size: 9px;color: blue;">mail:@if(is_null($rol->ct_nomina->mail_opcional)){{$rol->usuario->email}}@else{{$rol->ct_nomina->mail_opcional}}@endif</span></td>
                      <td style="text-align: right;">@if(!is_null($detalle)){{$detalle->sueldo_mensual}}@endif</td>
                      <td style="text-align: right;">@if(!is_null($detalle)){{$detalle->sobre_tiempo50}}@endif</td>
                      <td style="text-align: right;">@if(!is_null($detalle)){{$detalle->sobre_tiempo100}}@endif</td>
                      <td style="text-align: right;">@if(!is_null($detalle)){{$detalle->total_ingresos}}@endif</td>
                      <td style="text-align: right;">@if(!is_null($detalle)){{$detalle->prestamos_empleado}}@endif</td>
                      <td style="text-align: right;">@if(!is_null($detalle)){{$detalle->saldo_inicial_prestamo}}@endif</td>
                      <td style="text-align: right;">@if(!is_null($detalle)){{$detalle->total_quota_quirog}}@endif</td>
                      <td style="text-align: right;">@if(!is_null($detalle)){{$detalle->total_quota_hipot}}@endif</td>
                      <td style="text-align: right;">@if(!is_null($detalle)){{$detalle->total_egresos}}@endif</td>
                      <td style="text-align: right;">@if(!is_null($detalle)){{$detalle->neto_recibido}}@endif</td>
                      <td style="text-align: center;"><input type="checkbox" name="certificado{{$rol->id}}" id="certificado{{$rol->id}}" @if($rol->certificado) checked @endif onclick="certificar('{{$rol->id}}')">
                      </td>
                      <td>
                        @if($rol->estado == '1')
                          <!--a href="{{route('rol_pago.editar', ['id' => $rol->id])}}" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-eye-open"></span></a-->
                          <a target="_blank" href="{{route('rol_pago.imprimir', ['id' => $rol->id])}}" class="btn btn-success  btn-xs"><span class="glyphicon glyphicon-download-alt"></span></a>
                          <button class="btn btn-danger btn-xs" onclick="eliminar_rol('{{$rol->id}}')"><span class="glyphicon glyphicon-trash"></span></button>
                          <a  class="btn btn-primary  btn-xs" onclick="reenviar_mail('{{$rol->id}}')"><span class="fa fa-envelope"></span></a>
                          <a href="{{route('nuevo_rol.editar_nuevo_rol', ['id' => $rol->id])}}" class="btn btn-warning btn-xs"><span class="glyphicon glyphicon-eye-open"></span></a>
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
            
          </div>
        </div>
      </div>
    </div>
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
  <script src="{{ asset ("/js/jquery-ui.js")}}"></script>

  <script type="text/javascript">

    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      'order'       : [[ 1, "desc" ]]
    })

    function mostrar_detalle_prop() {
      var clase = $('#id_det').hasClass('ocultar');
      if(clase){
        $('.detail').show();
        $('#id_det').removeClass('ocultar');
        $('#id_det').addClass('muestralo');
      }else{
        $('.detail').hide();
        $('#id_det').removeClass('muestralo');
        $('#id_det').addClass('ocultar');
      }
      
      
    }

    function roles_empleados(){

        var formulario = document.forms["roles_pago"];

        var id_emp = formulario.id_empresa.value;
        var id_anio = formulario.year.value;
        var id_mes = formulario.mes.value;

        //Mensaje 
        var msj = "";

        if(id_emp == ""){
          msj = msj + "{{trans('nomina.porfavor')}}, {{trans('nomina.seleccione')}} {{trans('nomina.empresa')}}<br/>";
        }

        if(id_anio == ""){
          msj = msj + "{{trans('nomina.porfavor')}}, {{trans('nomina.seleccione')}} {{trans('nomina.anio')}}<br/>";
        }
        
        if(id_mes == ""){
          msj = msj + "{{trans('nomina.porfavor')}}, {{trans('nomina.seleccione')}} {{trans('nomina.mes')}}<br/>";
        }

        if(msj != ""){
            swal({
                  title: "Error!",
                  type: "error",
                  html: msj
                });
            return false;
        }

        $.ajax({
          type: 'post',
          url:"{{route('buscador_roles.pago')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#roles_pago").serialize(),
          success: function(data){
            $("#area_trabajo").html(data);
            //console.log(data);
          },
          error: function(data){
            alert("{{trans('nomina.error')}}");
          }
        });

    }

    function descargar_reporte() {
      $( "#roles_pago" ).submit(); 
    }

    function certificar(id){
      if ($('#certificado'+id).is(':checked')) {
        var cert = '1';
      } else {
        var cert = '0';
      }
      //alert(cert);

      $.ajax({
        type: 'get',
        url:"{{url('masivos_nuevo_rol/certificar')}}/"+id+'/'+cert,
        
        datatype: 'json',
        
        success: function(data){
          
        },
        error: function(data){
          alert("{{trans('nomina.error')}}"); 
        }
      });

    }

    function generar_roles() {
      var mes = $('#mes option:selected').text();
      var anio = $("#anio").val();
      var empresa = $('#id_empresa option:selected').text();
      var mx|err = '';
      if(mes == ''){
        msnerr = msnerr + "{{trans('nomina.ingrese')}} {{trans('nomina.mes')}}\n";
      }
      if(anio == ''){
        msnerr = msnerr + "I{{trans('nomina.ingrese')}} {{trans('nomina.anio')}}\n";
      }
      if(msnerr==''){
        Swal.fire({
          title: '{{trans('nomina.generar_roles')}} : '+ empresa +': '+ anio +' - '+ mes,
          showDenyButton: true,
          showCancelButton: true,
          confirmButtonText: '{{trans('nomina.enviar')}}',
          denyButtonText: '{{trans('nomina.no_enviar')}}',
          showLoaderOnConfirm: true,
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
              $.ajax({
                type: 'post',
                url:"{{route('nuevo_rol.masivo_genera_roles')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: { id_empresa : $('#id_empresa').val(), anio : $('#anio').val(), mes : $('#mes').val()},
                success: function(data){
                  location.reload();
                  //console.log(data);
                },
                error: function(data){
                  alert("{{trans('nomina.error')}}");
                }
              });   
            }
        });
        
      }else{
        alert(msnerr);
      }  
    }

    function subir_horas_extras(){
      var form = $('#roles_pago')[0];
      var data = new FormData(form);
      $.ajax({
        enctype: 'multipart/form-data',
        type: "post",
        url: "{{ route('nuevo_rol.he_valida_ejecutado')}}",
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        datatype: "html",
        data: data,
        processData: false,
        contentType: false,
        cache: false,
        timeout: 600000,
        success: function(datahtml) {
          //console.log(datahtml);
          if (datahtml.estado == 'ok') {
            subir_horas_extras2();
          }
          if (datahtml.estado == 'wrn') { 
            var confirmar = confirm(datahtml.mensaje);
            if(confirmar){
              subir_horas_extras2();  
            }  
          }
        },
        error: function(datahtml) {
          alert("ocurrio un error");
        }
      });  
    }
    
    function subir_horas_extras2() {
      
      var file = $('#archivo').val();
      var mes = $('#mes option:selected').text();
      var anio = $("#anio").val();
      var empresa = $('#id_empresa option:selected').text();
      var msnerr = '';
      if(mes == ''){
        msnerr = msnerr + "{{trans('nomina.ingrese')}} {{trans('nomina.mes')}}\n";
      }
      if(anio == ''){
        msnerr = msnerr + "{{trans('nomina.ingrese')}} {{trans('nomina.anio')}}\n";
      }
      if(file == ''){
        msnerr = msnerr + "{{trans('nomina.archivo_no_subido')}}\n"
      }
      if(msnerr==''){
        Swal.fire({
          title: '{{trans('nomina.subir_horas_extras')}} : '+ empresa +': '+ anio +' - '+ mes,
          showDenyButton: true,
          showCancelButton: true,
          confirmButtonText: '{{trans('nomina.enviar')}}',
          denyButtonText: '{{trans('nomina.no_enviar')}}',
          showLoaderOnConfirm: true,
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
              event.preventDefault();
              var form = $('#roles_pago')[0];
              var data = new FormData(form);
              $.ajax({
                enctype: 'multipart/form-data',
                type: "post",
                url: "{{ route('nuevo_rol.masivos_horario_extra')}}",
                headers: {
                  'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: "html",
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                success: function(datahtml) {
                  //console.log(datahtml);
                  if (datahtml.tipo == 'ok') {
                    swal(datahtml.mensaje, "{{trans('nomina.correcto')}}", "success");
                    setTimeout(function() {
                      location.reload();
                    }, 2000);
                  }
                  if (datahtml.tipo == 'err') {
                    swal(datahtml.mensaje, "Atencion", "warning");
                    setTimeout(function() {
                      location.reload();
                    }, 2000);
                    
                  }
                },
                error: function(datahtml) {
                  //console.log(datahtml);
                }
              });  
            }
        });
        
      }else{
        alert(msnerr);
      }  
     
    }

    function subir_prestamos(){
      var form = $('#roles_pago')[0];
      var data = new FormData(form);
      $.ajax({
        enctype: 'multipart/form-data',
        type: "post",
        url: "{{ route('nuevo_rol.p_valida_ejecutado')}}",
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        datatype: "html",
        data: data,
        processData: false,
        contentType: false,
        cache: false,
        timeout: 600000,
        success: function(datahtml) {
          //console.log(datahtml);
          if (datahtml.estado == 'ok') {
            subir_prestamos2();
          }
          if (datahtml.estado == 'wrn') { 
            var confirmar = confirm(datahtml.mensaje);
            if(confirmar){
              subir_prestamos2();  
            }  
          }
        },
        error: function(datahtml) {
          alert("ocurrio un error");
        }
      });  
    }

    function subir_prestamos2() {
      var file = $('#archivo').val();
      var mes = $('#mes option:selected').text();
      var anio = $("#anio").val();
      var empresa = $('#id_empresa option:selected').text();
      var prestamo = $('#prestamos option:selected').text();
      
      var msnerr = '';
      if(mes == ''){
        msnerr = msnerr + "{{trans('nomina.ingrese')}} {{trans('nomina.mes')}}\n";
      }
      if(anio == ''){
        msnerr = msnerr + "{{trans('nomina.ingrese')}} {{trans('nomina.anio')}}\n";
      }
      if(file == ''){
        msnerr = msnerr + "{{trans('nomina.archivo_no_subido')}}\n"
      }
      if(msnerr==''){
        Swal.fire({
          title: '{{trans('nomina.subir_prestamo')}}' + prestamo + ' {{trans('nomina.para')}} : ' + empresa +': '+ anio +' - '+ mes,
          showDenyButton: true,
          showCancelButton: true,
          confirmButtonText: '{{trans('nomina.enviar')}}',
          denyButtonText: '{{trans('nomina.no_enviar')}}',
          showLoaderOnConfirm: true,
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
              event.preventDefault();
              var form = $('#roles_pago')[0];
              var data = new FormData(form);
              $.ajax({
                enctype: 'multipart/form-data',
                type: "post",
                url: "{{ route('nuevo_rol.masivos_prestamos')}}",
                headers: {
                  'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: "html",
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                success: function(datahtml) {
                  //console.log(datahtml);
                  if (datahtml.tipo == 'ok') {
                    swal(datahtml.mensaje, "Correcto", "success");
                    setTimeout(function() {
                      location.reload();
                    }, 2000);
                  }
                  if (datahtml.tipo == 'err') {
                    swal(datahtml.mensaje, "Atencion", "warning");
                    setTimeout(function() {
                      location.reload();
                    }, 2000);
                    
                  }
                },
                error: function(datahtml) {
                  //console.log(datahtml);
                }
              });  
            }
        });
        
      }else{
        alert(msnerr);
      }  
    }

    function certificar_masivo(){

      var mes = $('#mes option:selected').text();
      var anio = $("#anio").val();
      var empresa = $('#id_empresa option:selected').text();
      var msnerr = '';
      if(mes == ''){
        msnerr = msnerr + "Ingrese el mes\n";
      }
      if(anio == ''){
        msnerr = msnerr + "Ingrese el año\n";
      }
      if(msnerr==''){
        Swal.fire({
          title: 'Desea Certificar los Roles para : '+ empresa +': '+ anio +' - '+ mes,
          showDenyButton: true,
          showCancelButton: true,
          confirmButtonText: 'Enviar',
          denyButtonText: 'No Enviar',
          showLoaderOnConfirm: true,
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
              $.ajax({
                type: 'post',
                url:"{{route('nuevo_rol.masivo_certificar_mes')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: { id_empresa : $('#id_empresa').val(), anio : $('#anio').val(), mes : $('#mes').val()},
                success: function(data){
                  location.reload();
                  //console.log(data);
                },
                error: function(data){
                  alert("Ha ocurrido un error");
                }
              });   
            }
        });

      }  
    }

    function reenviar_mail(id){
      Swal.fire({
        title: 'Estas Seguro de Reenviar el Mail de Rol?',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'Enviar',
        denyButtonText: 'No Enviar',
        showLoaderOnConfirm: true,
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
          $.ajax({
            url:"{{asset('contable/rol/pago/envio/correo/')}}/"+id,
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            type: 'GET',
            success: function(data){
                if(data == 'ok'){
                  Swal.fire('Enviado Correctamente!', '', 'success');
                }

            },
            error: function(data){
                Swal.fire('Error al Enviar el Correo!', '', 'error');
            }
          });

        }
      });
    }

    function reenviar_mail_sin_nt(id){
      
      $.ajax({
        url:"{{asset('contable/rol/pago/envio/correo/')}}/"+id,
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        type: 'GET',
        success: function(data){

        },
        error: function(data){
            Swal.fire('Error al Enviar el Correo!', '', 'error');
        }
      });

       
    }

    function reenviar_mail_masivo(){

      var mes = $('#mes option:selected').text();
      var anio = $("#anio").val();
      var empresa = $('#id_empresa option:selected').text();

      Swal.fire({
        title: 'Estas Seguro de Reenviar el Mail a ' + empresa + ': '+ anio + '-' + mes + '?',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'Enviar',
        denyButtonText: 'No Enviar',
        showLoaderOnConfirm: true,
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
          @foreach ($roles as $rol)
            reenviar_mail_sin_nt('{{$rol->id}}');
          @endforeach  

        }
      });
    }

    

    function eliminar_rol(id){
      Swal.fire({
        title: 'Estas Seguro de Eliminar el Rol?',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'Enviar',
        denyButtonText: 'No Enviar',
        showLoaderOnConfirm: true,
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
          $.ajax({
            url:"{{asset('nuevo_rol_e/pago/eliminar')}}/"+id,
            type: 'GET',
            success: function(data){
                location.reload();

            },
            error: function(data){
                Swal.fire('Error al Enviar el Correo!', '', 'error');
            }
          });

        }
      });
    }
  
  </script>

  
  </section>
@endsection
