@extends('archivo_plano/mantenimientomedicamentos/base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header">
            

            <form method="POST" action="{{route('buscar.medicamentos')}}">
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

                <div class="col-sm-6">
                    <a class="btn btn-primary" href="{{route('crear.medicamentos') }}">Crear</a>
                </div>

                <div class="box-body">
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap ">

                        <div class="table-responsive col-md-12 col-xs-12">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                                <thead>
                                    <tr>
                                        <th>Codigo</th>
                                        <th>Descripcion</th>
                                        <th>Valor</th>
                                        <th>Accion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($medicamentos as $medicamento)
                                    <tr>
                                        <td>{{$medicamento->codigo}}</td>
                                        <td>{{$medicamento->descripcion}}</td>
                                        <td>{{$medicamento->valor}}</td>
                                        <td><a class="btn btn-success" href="{{route('editar.medicamentos',['id' => $medicamento->id])}}">Editar</a></td>


                                    </tr>
                                    @endforeach

                                </tbody>

                            </table>

                        </div>
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($medicamentos->currentPage() - 1) * $medicamentos->perPage())}} / {{count($medicamentos) + (($medicamentos->currentPage() - 1) * $medicamentos->perPage())}} de {{$medicamentos->total()}} registros</div>
                            </div>
                            <div class="col-sm-7">
                                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                    {{ $medicamentos->appends(Request::only(['descripcion']))->links() }}
                                </div>
                            </div>
                        </div>


                    </div>

                </div>
            </form>
        </div>
    </div>
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