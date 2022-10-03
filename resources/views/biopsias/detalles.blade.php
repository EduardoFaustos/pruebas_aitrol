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
          <h3 class="box-title">trans{{('biopsias.ListadoBiopsiasEnviadasHoy')}}</h3>
        </div>
        <!--<div class="form-group col-md-3">
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
        <div class="form-group col-md-6">
          <label for="examen" class="col-md-2 control-label">trans{{('biopsias.NombreCorto')}}</label>
          <div class="col-md-10">
            <input id="nombre" type="text" class="form-control input-sm" name="nombre" value="" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
          </div>
        </div> 
        <button type="submit" class="btn btn-primary" id="boton_buscar">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span> trans{{('biopsias.Buscar')}}</button>
      </form>
      <div class="form-group col-md-10">
          <label for="examen" class="col-md-2 control-label">trans{{('biopsias.Paciente')}}:</label>
          <div class="col-md-10">
           <label for="examen" class="col-md-6 control-label">{{$paciente->paciente->nombre1}} {{$paciente->paciente->apellido1}}</label>
          </div>
        </div> 
        <div class="form-group col-md-10">
          <label for="examen" class="col-md-2 control-label">trans{{('biopsias.DoctorSolicitante')}}:</label>
          <div class="col-md-10">
           <label for="examen" class="col-md-6 control-label">{{strtoupper("Dr. ").$doctor->doctor->nombre1}} {{$doctor->doctor->apellido1}}</label>
          </div>
        </div>
        <div class="form-group col-md-10">
          <label for="examen" class="col-md-2 control-label">trans{{('biopsias.ProcedimientoRealizado')}}:</label>
          <div class="col-md-10">
           <label for="examen" class="col-md-2 control-label">
            
             @foreach ($procs as $value1)
          {{$value1->procedimiento->nombre}}
            @endforeach
           </label>
          </div>
        </div>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
            <thead>
              <tr role="row">
                <th align='center'>trans{{('biopsias.FECHA')}}</th>
                <th align='center'>trans{{('biopsias.FRASCO')}} #</th>
                <th align='center'>trans{{('biopsias.OBSERVACIONMUESTRA')}}</th>              
                <th >trans{{('biopsias.Acción')}}</th>
              </tr>
            </thead>
            <tbody>
              <?php $contador = 0; ?>
            @foreach ($detalle_frascos as $value)
            <?php $contador++; 
            ?>
              <tr role="row">
                <td align='center'>{{$value->created_at}}</td>
                <td align='center'>{{$contador}}</td>
                <td>{{$value->observacion}}</td>
                
                <td>

                  @if ($value->subido == 0)
                  <div class="form-group col-md-3">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('biopsias.registro', ['id' => $value->id]) }}" class="btn btn-warning" >
                        <span class="glyphicon glyphicon-edit"></span> trans{{('biopsias.CargarResultados')}}
                        </a>

                      </div>
                    @elseif($value->subido == 1)

                      <div class="form-group col-md-3">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('biopsias.edit', ['id' => $value->id]) }}" class="btn btn-success" >
                        <span class="glyphicon glyphicon-edit"></span> trans{{('biopsias.EditarResultados')}}
                        </a>
                
                      </div>
                      @endif
                </td> 
                          
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
      
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite"> trans{{('biopsias.Mostrando')}} 1  trans{{('biopsias.al')}} {{count($detalle_frascos)}}  trans{{('biopsias.deRegistros')}}</div>
        </div>
        
      </div>
    
    </div>
  </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  

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