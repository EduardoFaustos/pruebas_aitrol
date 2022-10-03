@extends('vacunas.base')
@section('action-content')
<section class="content">
  <div class="box">
   
      <div class="box-body">
        <div class="row">
          <div class="form-group col-md-6 ">
              <div class="row" >
                <div class="form-group col-md-10 ">
                              
                      </div>
              </div>
              </div>
        </div>

            <form method="POST" action="{{route('vacunas.buscar_empleados')}}">
            {{ csrf_field() }}
            <div class="form-group col-md-4 ">
            <div class="row" >
            <div class="form-group col-md-10 ">
                        <label for="cedula" class="col-md-4 control-label">Cédula:</label>
                        <div class="col-md-7">
                            <input id="cedula" maxlength="13" type="text" class="form-control input-sm" name="cedula" value="{{$cedula}}">
                        </div>
                  </div>
            </div>
            </div>

            <div class="form-group col-md-4 ">
            <div class="row" >
            <div class="form-group col-md-10 ">
                        <label for="usuario" class="col-md-4 control-label">Usuario:</label>
                        <div class="col-md-8">
                            <input id="usuario" maxlength="100" type="text" class="form-control input-sm" name="usuario" value="{{$nombres}}" placeholder="Nombres y Apellidos">
                        </div>
                  </div>
            </div>
            </div>

            <div class="form-group col-md-2 ">
                <div class="col-md-7">
                    <button id="buscar" type="submit" class="btn btn-primary"> <span class="glyphicon glyphicon-search"> Buscar</span></button>
                </div>
            </div>
          </form>

          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row" id="listado">
              <div class="table-responsive col-md-12">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                  <thead>
                    <tr>
	                    <th>Cédula</th>
	                    <th>Nombres</th>
                      <th>Apellidos</th>
	                    <th>Acción</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($usuarios as $value)
                    <tr>
                    	<td>{{$value->id}}</td>
                    	<td>{{$value->nombre1}} {{$value->nombre2}}</td>
                      <td>{{$value->apellido1}} {{$value->apellido2}}</td>
                    	<td>
                    		<a href="{{route('vacunas.revisar',['id' => $value->id])}}" class="btn btn-success  btn-xs">Revisar Vacunacion</a>
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
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($usuarios->currentPage() - 1) * $usuarios->perPage())}} / {{count($usuarios) + (($usuarios->currentPage() - 1) * $usuarios->perPage())}} de {{$usuarios->total()}} registros</div>
              </div>
              <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                  {{ $usuarios->appends(Request::only(['id', 'apellidos', 'nombres']))->links() }}
                </div>
              </div>
            </div>
        </div>

        </div>
    </div>
</section>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
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
@endsection