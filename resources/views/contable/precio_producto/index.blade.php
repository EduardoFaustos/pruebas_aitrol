@extends('contable.precio_producto.base')
@section('action-content')
<style>
    .swal2-icon .swal2-icon-content {
        font-size: 4em !important;
    }

    .swal2-icon.swal2-warning {
        font-size: 1.8rem;
    }
</style>
<section class="content">
    <div class="caja">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
                <li class="breadcrumb-item"><a href="#">{{trans('contableM.precio_aprobado')}}</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">{{trans('contableM.inicio')}}</a></li>
                </li>
            </ol>
        </nav>

        <div>
            <div>

            </div>
            <a href="{{ route('importaciones.PrecioProductoAprobado.create') }}" class="btn btn-success">Crear</a>
        </div>
    </div>
    <br>
    <div class="caja">
        <table class="table">
            <thead>
                <tr>
                    <th>Codigo</th>
                    <th>Nombre</th>
                    <th>Observacion</th>
                    <th>Precio</th>
                    <th>Accion</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productos as $value)
                <tr>
                    @php $importante = Sis_medico\Producto_Precio_Aprobado::where('id_producto', $value->id_producto)->where('importante', 1)->first(); @endphp
                    <td>{{ $value->codigo }}</td>
                    <td>{{ $value->nombre }}</td>
                    <td>{{ $value->observacion }}</td>
                    <td>{{ !is_null($importante) ?  "$ " . $importante->precio : '$ 0.00' }}</td>
                    <td>
                        <a href="#" class="btn btn-warning"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                        <a href="#" class="btn btn-danger" onclick="validar({{$value->id_producto}})"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

<script>
    const validar = id => {
        Swal.fire({
            title: 'Esta seguro?',
            text: "Si elimina no se podra revertir",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, Eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                eliminar(id)
              
            }
        })

    }

    const eliminar = id => {
        $.ajax({
            url: `{{ route('importaciones.PrecioProductoAprobado.delete') }}`,
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                'id_producto': id
            },
            datatype: 'json',
            success: function(data) {
                if(status == 'success'){
                    Swal.fire('Eliminado','Se ha eliminado con exito.','success')
                }else{
                    Swal.fire('Error... !','No se pudo eliminar.','error')

                }
            },
            error: function(data) {

            }
        })
    }
</script>


@endsection