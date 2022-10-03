@extends('archivo_plano.procedimientos.base')
@section('action-content')


<!-- Main content -->
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="box-title" style="margin-top: 7px">Editar Items</h3>
                </div><br>
                <hr>

            </div>
        </div>
        <!-- /.box-header -->
        <div class="content">
            <form method="POST" action="{{route('procedimientos_editar.actualizar')}}">
                {{ csrf_field() }}
                <div class="row">
                    <input type="hidden" name="id_procedimiento" id="id_procedimiento" value="{{$editar_procedimiento->id}}">
                    <div class="col-md-4">
                        <label>Tipo</label><br>
                        <select name="txttipo" class="form-control">
                            <option value="{{$editar_procedimiento->id}}">{{$editar_procedimiento->tipo}}</option>
                            <option value="EQ">EQ</option>
                            <option value="EX">EX</option>
                            <option value="I">I</option>
                            <option value="IM">IM</option>
                            <option value="IV">IV</option>
                            <option value="M">M</option>
                            <option value="P">P</option>
                            <option value="PA">PA</option>
                            <option value="S">S</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Código</label><br>
                        <input type="text" name="txtcodigo" placeholder="Código" class="form-control" value="{{$editar_procedimiento->codigo}}">
                    </div>
                    <div class="col-md-4">
                        <label>Descripción</label><br>
                        <input type="text" name="txtdescripcion" placeholder="Descripción" class="form-control" value="{{$editar_procedimiento->descripcion}}">
                    </div>
                    <div class="col-md-4"><br>
                        <label>Cantidad</label><br>
                        <input type="text" name="txtcantidad" placeholder="Cantidad" class="form-control" value="{{$editar_procedimiento->cantidad}}">
                    </div>
                    <div class="col-md-4"><br>
                        <label>Valor</label><br>
                        <input type="text" name="txtvalor" placeholder="Valor" class="form-control" value="{{$editar_procedimiento->valor}}">
                    </div>
                    <div class="col-md-4"><br>
                        <label>IVA</label><br>
                        <input type="text" name="txtiva" placeholder="IVA" class="form-control" value="{{$editar_procedimiento->iva}}">
                    </div>
                    <div class="col-md-6"><br>
                        <label>Estado</label><br>
                        <select name="txtestado" class="form-control">
                            <option {{ $editar_procedimiento->estado == 1 ? 'selected' : ''}} value="1">Activo</option>
                            <option {{ $editar_procedimiento->estado == 0 ? 'selected' : ''}} value="0">Inactivo</option>
                        </select>
                    </div>
                    <div class="col-md-12"><br>
                        <input type="submit" name="" value="Editar" class="btn btn-success">
                    </div>
                </div>
            </form>

        </div>
        <!-- /.box-body -->
    </div>
</section>
<!-- /.content -->
</div>

@endsection