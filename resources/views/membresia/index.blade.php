@extends('membresia.base')
@section('action-content')

    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title"> Mantenimiento de Membresias  </h3>
        </div>
        <div class="col-sm-4">
           <a  href="{{route('membresia.create')}}"  class="btn btn-primary btn-gray"><i aria-hidden="true"></i> Agregar Nueva Membresia</a>
        </div>
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

      </form>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
            <tr class="well-dark" >
                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">Empresa</th>
                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">Nombre</th>
                <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Precio Mensual</th>
                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">Precio Anual</th>
                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">URL</th>
                <th width="5%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">Estado</th>
                <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Acción</th>
                
              </tr>
            </thead>
            <tbody>
                @foreach ($membresia as $mem)
                <tr role="row" class="odd">
                  
                  <td class="sorting_1">
                  @foreach($empresas as $emp)
                    @if($mem->empresa_id == $emp->id)
                    {{$emp->razonsocial}}
                    @endif
                  @endforeach </td>
                  <td class="sorting_2">{{ $mem->nombre }}</td>
                  <td class="sorting_3">{{ $mem->precio_mensual }}</td>
                  <td class="sorting_4">{{ $mem->precio_anual }}</td>
                  <td class="sorting_5">{{ $mem->url }}</td>
                  <td class="sorting_7">
                  @if($mem->estado == 1)
                                  Activo
                                @elseif($mem->estado == 0)
                                  Inactivo
                                @endif
                  <td align="center">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('membresia.edit', ['id' => $mem->id])}}" class="btn btn-success btn-gray">
                        <i class="glyphicon glyphicon-edit" aria-hidden="true"></i>
                        </a>
                        <button onclick="eliminar()" type="button" class="btn btn-danger btn-red">
                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>          
                        </button> 
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
    <div class="row">
                  <div class="col-sm-5">
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($membresia->currentPage() - 1) * $membresia->perPage())}} / {{count($membresia) + (($membresia->currentPage() - 1) * $membresia->perPage())}} de {{$membresia->total()}} registros
                     </div>
                  </div>
                  <div class="col-sm-7">
                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                      {{$membresia->appends(Request::only(['descripcion','estado']))->links() }}
                    </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
  function eliminar() {
    var confirmar = confirm('¿seguro quiere eliminar?');
    if(confirmar) {
            $.ajax({
                url: "{{route('membresia.delete', ['id' => $mem->id])}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#formulario').serialize(),
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    var url = "{{route('membresia.index')}}"
                    if (data == 'ok') {
                        setTimeout(function() {
                            swal("Eliminado!", "Correcto", "success");
                            window.location = url;
                        }, 2000);
                    }     
                },
                error: function(xhr, status) {
                    alert('Existió un problema');
                    //console.log(xhr);
                },
            });
        }
    }  
</script>
@endsection