@extends('archivo_plano/mantenimientoinsumos/base')
@section('action-content')

<section class="content">
<div class="box">
<div class="box-header">
         <div class="col-md-2"> 
        <a class="btn btn-primary" href="{{route('crear_insumos')}}">Crear</a>
          </div>
          <form method="POST" action="{{route('buscar.insumos')}}">
                {{ csrf_field() }}
          <div class="col-md-6">
                    <div class="col-md-3">
                        <label class="control-label">Descripcion:</label>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="descripcion" class="form-control" required />
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary" type="submit">Buscar</button>
                    </div>
                </div>
                  
        <div class="box-body">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap ">
                <div class="table-responsive col-md-12 col-xs-12">
                    <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Codigo</th>
                                <th>Descripcion</th>
                                <th>Valor</th>
                                <th>Accion</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($insumos as $insumo)
                        <tr>
                            <td>{{$insumo->tipo}}</td>
                            <td>{{$insumo->codigo}}</td>
                            <td>{{$insumo->descripcion}}</td>
                            <td>{{$insumo->valor}}</td>
                            <td><a class="btn btn-primary" href="{{route('editar_insumos',['id' => $insumo->id])}}">Editar</a></td>
                      

                        </tr>
                        @endforeach
                        
                        </tbody>
                    
                    </table>    
                   </div>
                   <div class="row">
                            <div class="col-sm-5">
                                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($insumos->currentPage() - 1) * $insumos->perPage())}} / {{count($insumos) + (($insumos->currentPage() - 1) * $insumos->perPage())}} de {{$insumos->total()}} registros</div>
                            </div>
                            <div class="col-sm-7">
                                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                    {{ $insumos->appends(Request::only(['descripcion']))->links() }}
                                </div>
                            </div>
                        </div>
            </div>
         </div>
        </div>
        </div>
        </form>
        </section>

<script type="text/javascript">
    $('#seguimiento').on('hidden.bs.modal', function() {
        $(this).removeData('bs.modal');
    });

    $(document).ready(function() {

        $('#example2').DataTable({
            'paging': false,
            'lengthChange': false,
            'searching': false,
            'ordering': true,
            'info': false,
            'autoWidth': false
        });

    });
</script>
        
     
       
        
   
@endsection