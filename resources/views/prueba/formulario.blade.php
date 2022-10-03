@extends('prueba.base')
@section('action-content')
<form action="{{route('formulario.guardar')}}" method="POST">
{{ csrf_field() }}
    <div class="container">
        <div class="col-md-6">
            <div class="row">
                <div class="form-group col-md-3">
                    &nbsp;
                </div>
                <div class="form-floating mb-3">
                    <input type="Date" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" name="fecha" .requiered />
                    <label for="floatingInput">Fecha</label>
                </div>
                <br>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" name="paciente" .requiered />
                    <label for="floatingInput">Paciente</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" name="procedimiento" .requiered />
                    <label for="floatingInput">Procedimiento</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" name="seguro" .requiered />
                    <label for="floatingInput">Seguro</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="Text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" name="factura" .requiered />
                    <label for="floatingInput">Factura</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="Text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" name="cobradopcte" .requiered />
                    <label for="floatingInput">Cobrado PCTE</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="Text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" name="cxccliente" .requiered />
                    <label for="floatingInput">CXC cliente</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="Text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" name="xfcaseg" .requiered />
                    <label for="floatingInput">XFC ASEG</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" name="valortotal" .requiered />
                    <label for="floatingInput">Valor Total</label>
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