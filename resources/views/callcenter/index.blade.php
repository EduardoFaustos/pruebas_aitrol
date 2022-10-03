@extends('callcenter.base')
@section('action-content')
<!-- Main content -->

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
<div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">{{trans('ecallcenter.ReportedellamadasCallCenter')}}</h3>
        </div>
        <div class="col-md-4" style="text-align: right;">
                        <a type="button" href="{{url('agenda') }}" class="btn btn-primary btn-sm">
                        <span class="glyphicon glyphicon-arrow-left">{{trans('ecallcenter.Regresar')}}</span>
                        </a>
        </div>
        <form  method="POST" accept-charset="utf-8" action="{{route('callcenter.descargar_reporte')}}">
          {{ csrf_field() }}
          <div class="form-group col-md-5 {{ $errors->has('fecha') ? ' has-error' : '' }} {{ $errors->has('id_doctor1') ? ' has-error' : '' }}" >
          
          <label class="col-md-3 control-label">{{trans('ecallcenter.FechaInicio')}}</label>
          <div class="col-md-4">
              <div class="input-group date">
                  <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" value="" name="fecha" class="form-control" id="fecha" onchange="fechacalendario();"  required>
              </div>
              @if ($errors->has('fecha'))
              <span class="help-block">
                  <strong>{{ $errors->first('fecha') }}</strong>
              </span>
              @endif
          </div>
        </div>
        <div class="form-group col-md-6 {{ $errors->has('fechafin') ? ' has-error' : '' }} {{ $errors->has('id_doctor1') ? ' has-error' : '' }}" >
          <label class="col-md-2 control-label">{{trans('ecallcenter.FechaFin')}} </label>
          <div class="col-md-4">
              <div class="input-group date">
                  <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" value="" name="fechafin" class="form-control" id="fechafin" onchange="fechacalendario();"  required>
              </div>
              @if ($errors->has('fechafin'))
              <span class="help-block">
                  <strong>{{ $errors->first('fechafin') }}</strong>
              </span>
              @endif
          </div>
          <div class="form-group col-md-4 col-sm-8">  
            <input type="submit" class="btn btn-primary col-md-10" value="Descargar Reporte">
        </div>
      </div>
      
 
          
        </form>
        
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
  
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">{{trans('econsultam.Cédula')}}</th>
                <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">{{trans('ecallcenter.NombredePaciente')}}</th>
                <th width="5%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >{{trans('econsultam.Seguro')}}</th>
                <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Birthdate: activate to sort column ascending">{{trans('econsultam.Fecha')}}</th>
                <th width="5%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Birthdate: activate to sort column ascending">{{trans('econsultam.Tipo')}}</th>
                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Birthdate: activate to sort column ascending">{{trans('ecallcenter.Teléfonoallamar')}}</th>
                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Birthdate: activate to sort column ascending">{{trans('ecallcenter.Teléfono1')}}</th>
                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Birthdate: activate to sort column ascending">{{trans('ecallcenter.Teléfono2')}}</th> 
              </tr>
            </thead>
            <tbody>
              @foreach ($variable1 as $value)
                <tr role="row" class="odd">
                  
                  <td>{{ $value->id_paciente}}</td>
                  <td>{{$value->paciente->apellido1}} {{$value->paciente->apellido2}} {{$value->paciente->nombre1}} {{$value->paciente->nombre2}}</td> 
                  <td>{{$value->seguro->nombre}}</td>
                  <td>{{$value->fechaini}}</td>
                  <td>@if($value->proc_consul==0) no asistió @else dada @endif</td>
                  <td> <form action="index_submit" method="get" accept-charset="utf-8" id="{{$value->id}}">
                    <input type="hidden" name="id_paciente" value="{{$value->id_paciente}}">
                    <input onkeypress="return validarNumero(event)" maxlength="10" minlength="9" id="numero{{$value->id}}" onchange="cambiarnumero('{{$value->id}}');" name="telefono_call" value="@if(!is_null($value->paciente->telefono_llamar) && (strlen($value->paciente->telefono_llamar)>=9)){{$value->paciente->telefono_llamar}} @elseif(is_numeric($value->paciente->telefono1) && (strlen($value->paciente->telefono1)>=9) ){{$value->paciente->telefono1}}  @endif">
                  </form>
                    </td>
                  <td>{{$value->paciente->telefono1}}</td>
                  <td>{{$value->paciente->telefono2}}</td>


                </tr>
               @endforeach

            </tbody>
          </table>
        </div>
      </div>
      <div class="row">
      </div>
    </div>
  </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  </div>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ('/js/calendario/moment.min.js') }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

  <script type="text/javascript">
    $(function () {
        $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',
            @if($fecha == '0')
                defaultDate: '{{date("Y/m/d")}}'
            @else
              <?php 
                $fecha  = substr($fecha, 0,10);
                $fecha2 = date('Y/m/d', $fecha);
              ?>
                defaultDate: '{{$fecha2}}'
            @endif
        });
        $("#fecha").on("dp.change", function (e) {

            //alert("hola");
            fecha_buscador();
            $('#fechafin').data("DateTimePicker").minDate(e.date);
        });

        $('#fechafin').datetimepicker({
            format: 'YYYY/MM/DD',
            @if($fechafin == '0')
                defaultDate: '{{date("Y/m/d")}}',
                minDate: '{{date("Y/m/d")}}',
            @else
              <?php 
                date_default_timezone_set('Europe/London');
                $fechafin  = substr($fechafin, 0,10);
                $fechafin1 = date('Y/m/d', $fechafin);
              ?>
                defaultDate: '{{$fechafin1}}',
                minDate: '{{$fechafin1}}', 
            @endif           
        });
        $("#fechafin").on("dp.change", function (e) {
            fecha_buscador();
        });
    });


</script>
<script type="text/javascript" >
    function fecha_buscador() {
       var fecha = document.getElementById('fecha').value;    
       var unix =  Math.round(new Date(fecha).getTime()/1000);
       
       var fechafin = document.getElementById('fechafin').value;    
       var unix2 =  Math.round(new Date(fechafin).getTime()/1000);
       if((fecha=='' ||fecha==' ') && (fechafin=='' ||fechafin==' ')){
       }else{
          
          location.href ="{{ route('callcenter.buscador')}}/"+unix+'/'+unix2;
       }
     
    }
</script>
<script type="text/javascript">
    function cambiarnumero(id_formulario){
      var doc= document.getElementById('numero'+id_formulario).value;
      if(doc.length<9){
         alert("No es ningún número de telefono");
      }else{
        
        $.ajax({
            type: 'post',
            url:"{{route('callcenter.actualizarpaciente')}}", //ingreso de la ruta
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            
            datatype: 'json',
            data: $("#"+id_formulario).serialize(),
            success: function(data){
              console.log(data);
              alert("El cambio ha sido realizado");  
            },
            error: function(data){

              console.log(data.responseJSON);
               
            }
          });

      }
      
    
    }  
    function validarNumero(e) {
      tecla = (document.all) ? e.keyCode : e.which;
      if (tecla==8) return true; 
      patron =/[0-9]/;
      te = String.fromCharCode(tecla); 
      return patron.test(te); 
   }

</script>
@endsection