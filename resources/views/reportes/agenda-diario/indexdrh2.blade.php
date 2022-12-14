

@extends('reportes.agenda-diario.base')
@section('action-content')
<?php function CalculaEdad( $fecha2 ) {
    list($Y,$m,$d) = explode("-",$fecha2);
    return( date("md") < $m.$d ? date("Y")-$Y-1 : date("Y")-$Y );
}?>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
      <form method="POST" action="{{route('consultam.reporteagenda2')}}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
        <div class="col-md-3 {{ $errors->has('fecha') ? ' has-error' : '' }} " >
          <label class="col-md-4 control-label">Fecha</label>
          <div class="input-group date col-md-8">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" value="{{$fecha}} " name="fecha" class="form-control" id="fecha" required >
          </div>
        </div>
        <div class="col-md-3 {{ $errors->has('doctor') ? ' has-error' : '' }} " >
          <label class="col-md-4 control-label">Doctor</label>
          <div class="form-group col-md-8">
            <select class="form-control" name="doctor" id="doctor" required>
              @foreach($users as $value)  
              <option @if($doctor == $value->id) selected @endif value="{{$value->id}}">{{$value->nombre1}} {{$value->apellido1}}</option>
              @endforeach  
            </select>
            
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-3">
            <button type="submit" id="buscar3" class="btn btn-primary">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
            </button>
          </div> 
        </div>  
      </form>
     
        <form method="POST" action="{{route('consultam.excel2')}}">
          <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
          <input type="hidden" value="{{$fecha}}" name="fecha" class="form-control" id="fecha2" >
          <input type="hidden" value="{{$doctor}}" name="doctor" class="form-control" id="doctor2" >
          <div class="col-md-3">
            <button type="submit" class="btn btn-primary">
              <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Exportar a excel
            </button>
          </div>  
        </form>
    </div>
      <div class="col-sm-12 text-center">
        <h2>Agendamiento Procedimientos</h2>
        <h4>FECHA: {{substr($fecha, 8, 2)}} de <?php $mes = substr($fecha, 5, 2); if($mes == 01){ echo "ENERO";} if($mes == 02){ echo "FEBRERO";} if($mes == 03){ echo "MARZO";} if($mes == 04){ echo "ABRIL";} if($mes == 05){ echo "MAYO";} if($mes == 06){ echo "JUNIO";} if($mes == 07){ echo "JULIO";} if($mes == '08'){ echo "AGOSTO";}  if($mes == '09'){ echo "SEPTIEMBRE";} if($mes == '10'){ echo "OCTUBRE";} if($mes == '11'){ echo "NOVIEMBRE";} if($mes == '12'){ echo "DICIEMBRE";} ?> DEL {{substr($fecha, 0, 4)}}</h4>
      </div>
    </div>
    <div class="box-body">
      <div class="row">
      <div class="col-sm-12">
        <h4>PROCEDIMIENTOS</h4>
      </div>
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
          <div class="table-responsive col-md-12">
      <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
        <thead>
          <tr role="row">
            <th style="width:15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Apellidos</th>
            <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Nombres</th> 
            <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Edad</th>    
            <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Birthdate: activate to sort column ascending">Confirmaci??n</th>
            <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Procedimientos</th>
            <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Hora</th>
            <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Sala</th>
            <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Entrada</th>
            <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1">M??dico P.</th>              
            <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Asistente 1</th>
            <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Seguro</th>
            <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Ubicaci??n</th>
            <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Procedencia</th>
            <th   tabindex="0" aria-controls="example2" rowspan="1" colspan="1"><span data-toggle="tooltip" title="Documentos Pendientes">D.P.</span></th>
          </tr>
        </thead>
        <tbody style="font-size: 12px;">
          @foreach($procedimientos as $value)
          @php $agprocedimientos = Sis_medico\AgendaProcedimiento::where('id_agenda',$value->id)->get() @endphp 
          <tr @if($value->estado_cita != 0) @if($value->paciente_dr == 1) style="color: {{$value->d1color}};" @else style="color: {{$value->color}};" @endif @endif>
            <td>{{$value->papellido1}} {{$value->papellido2}}</td>
            <td>{{$value->pnombre1}} {{$value->pnombre2}}</td>
            <td> <?php echo CalculaEdad($value->pfecha_nacimiento) ?></td>        
            @if($value->estado_cita == 0)
            <td>Por Confirmar</td>
             @elseif($value->estado_cita == 3)
            <td>SUSPENDIDA</td>
            @else
            <td>{{substr($value->unombre1, 0,1)}}. {{$value->uapellido1}}</td>
            @endif
            <td>{{$value->probservacion}}@if(!$agprocedimientos->isEmpty()) @foreach($agprocedimientos as $agendaproc) - {{Sis_medico\Procedimiento::find($agendaproc->id_procedimiento)->observacion}} @endforeach @endif</td>
            <td>{{substr($value->fechaini, 11, 5)}}</td>
            <td>{{$value->nombre_sala}}</td>
            @if($value->est_amb_hos == 0)
            <td>Ambulatorio</td>
            @else
            <td>Hospitalizado</td>
            @endif
            <td>@if($value->id_doctor1 != ""){{$value->d1nombre1}} {{$value->d1apellido1}}@endif</td>      
            <td>@if($value->id_doctor2 != ""){{$value->d2nombre1}} {{$value->d2apellido1}} @endif</td>
            <td>{{$value->nombre_seguro}}</td>
            <td>{{$value->nombre_hospital}}</td>
            <td>{{$value->procedencia}}</td>
            <td>@if(array_has($dp_proc, $value->id)){{$dp_proc[$value->id]}}@else 0 @endif</td>
            </tr>
            @endforeach
        </tbody>
      </table>
    </div>
  </div>
      </div>    
    </div>
</div>
</section>
  
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>


<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">

$('#fecha').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
             //Important! See issue #1075
            
        });

$('#fecha').on('dp.change', function(e){ 

      buscar();
  })

function buscar()
{
  var obj = document.getElementById("buscar3");
  obj.click();
}
</script>



@endsection