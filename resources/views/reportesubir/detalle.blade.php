@extends('reportesubir.base')
@section('action-content')
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
  
 
<div class="box">
  <div class="box-header">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-12" style="text-align: right;">
                        <a type="button" href="{{route('reportesubir.index')}}" class="btn btn-primary btn-sm">
                        <span class="glyphicon glyphicon-arrow-left">Regresar</span>
                        </a>
        </div>
        
      </div>
      
      
     <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                <th width="10%">Id</th>
                <th width="20%">Nombre del paciente</th>
                <th width="20%">Teléfono</th>
                <th width="20%">Respuesta</th>
                <th width="10%">Estado Agenda</th>
                <th width="20%">Estado correo</th>
                <th width="10%">Acción</th>
               
          </tr>
          </thead>
          <tbody>
              @foreach ($consultas as $value)
                
                <tr role="row" class="odd">
                  
                  <td>{{$value->id_agenda}}</td>
                  <td>{{($value->agenda->paciente->apellido1)}} {{($value->agenda->paciente->apellido2)}} {{($value->agenda->paciente->nombre1)}} </td>
                  <td>{{$value->telefono}}</td>
                  <td>@if(($value->estado)==1) asisto @elseif(($value->estado)==2) no responde @elseif(($value->estado)==0) no asisto @elseif(($value->estado)==3) no procesado @endif</td>
                  <td>@if(($value->agenda->estado_cita)==1) confirmada
                   @elseif(($value->agenda->estado_cita)==0) por confirmar
                   @elseif(($value->agenda->estado_cita)==2) reagendar 
                   @elseif(($value->agenda->estado_cita)==3) suspendido
                   @elseif(($value->agenda->estado_cita)==4) admisionado
                   @endif</td>
                  <td>@if(($value->estado)==2) @elseif(($value->estado_correo)==0) Correo no enviado @else Correo enviado @endif</td>
                  <td>@if(($value->estado)!==0 &&($value->estado)!==2 &&($value->agenda->estado_cita)!=3 && ($value->estado_correo)==0)<input type="hidden" >
                              <a href="{{ route('reportesubir.correo',['id' => $value->id_agenda,'id_detalle'=>$value->id]) }}" class="btn btn-success col-md-10 col-xs-6 btn-margin">
                              Enviar correo
                  
                              </a>
                              @elseif(($value->estado)==0 && ($value->agenda->estado_cita ==2)) <a href="javascript:gestion({{$value->id_agenda}},{{$value->agenda->id_doctor1}});" class="btn btn-warning col-md-9 col-sm-9 col-xs-9 btn-margin">Reagendar</a>
                  @endif</td>
                </tr>
                  @endforeach

                  </tr>
                </tbody>
              </table>

              <!---Paginador-->
              
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      'order'       : [[ 0, "asc" ]]
    });
</script>
<script type="text/javascript">
  
  function gestion(id, doctor){
    console.log('{{asset("/agenda/edit/pre/")}}/'+id+'/'+doctor);
    //alert(VariableJS);
    $.ajax({
        type: 'get',
        url: '{{asset("/agenda/edit/pre/")}}/'+id+'/'+doctor,
        success: function(data){
          //alert(data);
          console.log(data);
          if(data=='ok'){
            location.href ="{{asset('agenda/')}}/"+id+'/edit/'+doctor;  
          }else{
            alert("Ya se gestionó esta cita");
            document.location.reload();
          }
              
        }
    });
  
  }
</script>
               

@endsection
