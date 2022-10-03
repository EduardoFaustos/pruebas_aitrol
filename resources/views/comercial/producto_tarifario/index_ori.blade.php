@extends('comercial.producto_tarifario.base')
@section('action-content')
<section class="content">
    <div class="box">
        <div class="box-header">
        </div>
        <div class="box-body">
            <form id="form_producto" method="post" action="{{route('prodtarifario.buscar')}}">
                {{ csrf_field() }}
                <div class="form-group col-md-6 ">
                    <div class="row">
                        <div class="form-group col-md-10 ">
                            <label for="nombre" class="col-md-4 control-label">{{trans('prod_tar.productos')}}:</label>
                            <div class="col-md-8">
                                <select id="producto" name="producto" class="form-control select2_productos" style="width:100%">
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-2 ">
                    <div class="col-md-7">
                        <button type="submit" class="btn btn-primary" id="boton_buscar">
                            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('prod_tar.buscar')}}</button>
                    </div>
                </div>

                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row" id="listado">
                        <div class="table-responsive col-md-12">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                <thead>
                                    <tr>
                                        <th>{{trans('prod_tar.codigo')}}</th>
                                        <th>{{trans('prod_tar.producto')}}</th>
                                        <th>{{trans('prod_tar.accion')}}</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($productos as $prod)

                                    <tr>
                                        <td>{{$prod->codigo}}</td>
                                        <td>{{$prod->nombre}} </td>
                                        <td>
                                            <a href="{{ route('prodtarifario.index_tarifario', ['id_producto' => $prod->id])}}" class="btn btn-warning"><i class="fa fa-plus"></i></a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<script type="text/javascript">
    $('#example2').DataTable({
        'paging': false,
        'lengthChange': false,
        'searching': false,
        'ordering': true,
        'info': false,
        'autoWidth': false
    })

    $('.select2_productos').select2({
        placeholder: "Seleccione un producto...",
        allowClear: true,
        cache: true,
        ajax: {
            url: '{{route("importaciones.productos")}}',
            data: function(params) {
                var query = {
                    search: params.term,
                    type: 'public'
                }
                return query;
            },
            processResults: function(data) {
                return {
                    results: data
                };
            }
        }
    });
</script>

@endsection