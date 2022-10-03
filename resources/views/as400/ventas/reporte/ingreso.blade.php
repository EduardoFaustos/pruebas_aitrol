@extends('reporte.base')
@section('action-content')
<form action="{{route('reporte_comisiones.ingreso')}}" method="POST">
{{ csrf_field() }}
    <div class="container">
        <div class="col-md-6">
            <div class="row">
                <div class="form-group col-md-3">
                    &nbsp;
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" name="nombre" .requiered />
                    <label for="floatingInput">Nombre</label>
                </div>
                <br>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" name="apellido" .requiered />
                    <label for="floatingInput">Apellido</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="Date" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" name="fechadenacimiento" .requiered />
                    <label for="floatingInput">Fecha de Nacimiento</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" name="edad" .requiered />
                    <label for="floatingInput">Edad</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" name="estatura" .requiered />
                    <label for="floatingInput">Estatura</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" name="peso" .requiered />
                    <label for="floatingInput">Peso</label>
                    <br>
                    <div class="d-grid gap-2 col-6 mx-auto">
                        <button type="submit" class="btn btn-primary">Enviar</button>
                    </div>
                </div>
               
            </div>
        </div>
    </div>
</form>


@endsection