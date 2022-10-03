@extends('archivo_plano.procedimientos.base')
@section('action-content')


  <!-- Main content -->
  <section class="content">
  <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-6">
          <h3 class="box-title">Lista de Items</h3>
        </div>
        <div class="col-sm-3">
          <a class="btn btn-primary" href="{{route('procedimientos.crear')}}">Agregar Nuevo Items</a>
        </div>
        <!--div class="col-sm-3">
          <a class="btn btn-primary" href="{{route('procedimientos.nivel')}}">Asignar Nivel a Items</a>
        </div-->
    </div>
  </div> 
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
    <!--AQUI VA EL BUSCADOR-->
<form method="POST" action="{{route('procedimientos.buscar')}}">
  {{ csrf_field() }}
  <div class="col-md-6">
    <div class="col-md-3">
      <label class="control-label">Nombre:</label>
    </div>
    <div class="col-md-4">
      <input type="text" name="descripcion" value="{{$procedimiento}}" class="form-control" required/>
    </div>
    <div class="col-md-4">
      <button class="btn btn-primary" type="submit">Buscar</button>
    </div>
  </div>
    
</form>

    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="frmpro" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
            
                <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">Tipo</th>
                <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">Código</th>
                <th width="60%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Descripción</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Valor</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">IVA</th>
                <th  width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Acción</th>
                
              </tr>
            </thead>
            <tbody>
            @foreach ($procedimientos as $procedimiento)
                <tr role="row" class="odd">
                  
                  <td class="sorting_1">{{ $procedimiento->tipo }}</td>
                  <td> {{ $procedimiento->codigo }}</td>
                  <td> {{ $procedimiento->descripcion }}</td>
                  <td> {{ $procedimiento->valor }}</td>
                  <td> {{ $procedimiento->iva }}</td>
                  <td>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('procedimientos_editar', ['id' => $procedimiento->id]) }}" class="btn btn-warning col-md-8 col-sm-8 col-xs-8 btn-margin">
                        Actualizar
                        </a>
                        <a href="{{route('ap_procedimiento.nivel',['id' =>$procedimiento->id])}}" class="btn btn-warning col-md-8 col-sm-8 col-xs-8 btn-margin">Asignar Nivel</a>
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
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / {{count($procedimientos)}} de {{count($procedimientos)}} registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $procedimientos->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  </div>

@endsection