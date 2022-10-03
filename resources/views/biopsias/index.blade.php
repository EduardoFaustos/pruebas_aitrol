@extends('biopsias.base')
@section('action-content')

<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
} 
</style>
<!-- Ventana modal editar -->
<div class="modal fade fullscreen-modal" id="doctor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-md-6">
          <h3 class="box-title">trans{{('biopsias.BuscarPorFecha')}}:</h3>
        </div>
        <!--
        <div class="form-group col-md-3">
          <a class="btn btn-primary" href="{{ route('cie_10_4.create')}}"> Agregar</a>
        </div>-->
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="">
        {{ csrf_field() }}
        <div class="col-md-4">
                <div class="row">
                  <div class="col-md-4" style="padding-top: 20px"><strong>trans{{('biopsias.Desde')}}:</strong></div>
                  <div class="col-md-8" style="margin-top: 20px;">
                     <div class="form-group">

                      
                       <div class='col-sm-8'>
                        <input type='text' name="obtenido" class="form-control" id='datetimepicker6' />
                    </div>
                    <script type="text/javascript">
                        $(function () {
                            $('#datetimepicker6').datetimepicker({
                                format: 'YYYY-MM-DD'
                              });
                        });
                    </script>

                  </div>
                    
                </div>
              </div>
            </div>
            <div class="col-md-4">
                <div class="row">
                  <div class="col-md-4" style="padding-top: 25px"><strong>trans{{('biopsias.Hasta')}}:</strong></div>
                  <div class="col-md-8" style="margin-top: 25px;">
                     <div class="form-group">

                       <div class='col-sm-8'>
                        <input type='text' name="recibido" class="form-control" id='datetimepicker8' />
                    </div>
                    <script type="text/javascript">
                        $(function () {
                            $('#datetimepicker8').datetimepicker({
                                format: 'YYYY-MM-DD'
                              });
                        });
                    </script>

                  </div>
                    
                </div>
              </div>
            </div>
        <button type="submit" class="btn btn-primary" id="boton_buscar">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span>trans{{('biopsias.BuscarporFechaSeleccionada')}}</button>
      </form>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
            <thead>
              <tr role="row">
                <th >trans{{('biopsias.FECHA')}}</th>
                <th >trans{{('biopsias.NOMBREPACIENTE')}}</th>
                <th >trans{{('biopsias.DOCTORSOLICITANTE')}}</th>
                              
                <th >trans{{('biopsias.Acción')}}</th>
              </tr>
            </thead>
            <tbody>
            
            @foreach ($biopsias as $value)
            
              <tr role="row">
                <td>{{$value->created_at}}</td>
                <td>{{$value->paciente->nombre1}} {{$value->paciente->apellido1}}</td>
                <td>{{strtoupper("Dr. ").$value->doctor->nombre1}} {{$value->doctor->apellido1}}</td>
                
                <td><div class="form-group col-md-3">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('biopsias.detalles', ['hc_id_procedimiento' => $value->hc_id_procedimiento]) }}" class="btn btn-warning" >
                        <span class="glyphicon glyphicon-edit"></span> trans{{('biopsias.DetalleFrascos')}}
                        </a>  
                      </div>
                </td>          
              </tr>
             
            @endforeach

            </tbody>
          </table>
        </div>
      </div>
      
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">trans{{('biopsias.Mostrando')}} 1 trans{{('biopsias.al')}} {{count($biopsias)}} trans{{('biopsias.deRegistros')}}</div>
        </div>
        
      </div>
    
    </div>
  </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>


<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">

  $(document).ready(function($){

    $(".breadcrumb").append('<li class="active">Exámenes</li>');

    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })



  });

   $('#doctor').on('hidden.bs.modal', function(){
                location.reload();
                $(this).removeData('bs.modal');
            }); 

</script>  

@endsection