@extends('contable.configuracion_pdf.base')
@section('action-content')

<script>
    function goBack() {
        location.href = "{{route('configuraciones_pdf_index')}}";
    }
</script>

<section class="content">
    <div class="box" style="background-color: white;">
        <div class="box-header color_cab with-border" style="color: black; font-family: 'Helvetica general3';border-style:none;margin:none;">
            <div class="col-md-11">
                <h5><b>{{trans('contableM.ConfiguracionPDF')}}</h5>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="goBack()" class="btn btn-primary btn-gray">
                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                </button>
            </div>
        </div>
        
        <div class="box-body" style="background-color: #ffffff;">
            <form class="form-vertical" role="form" method="POST" action="{{ route('configuraciones.guardar') }}">
                {{ csrf_field() }}
                <div class="box-body dobra">
                    <div class="form-group  col-md-12">
                        <label for="fecha" class="col-md-1 texto">{{trans('contableM.fecha')}}</label>
                        <div class="col-md-3">
                            <input style="width:100%" id="fecha" name="fecha" type="date" class="form-control" required>
                        </div>
                        <label for="detalle" class="col-md-1 texto">{{trans('contableM.empresa')}}</label>
                        <div class="col-md-3">
                            <input class="form-control" require name="empresa_nombre" id="empresa_nombre" disabled value="{{$nombre_empresa->nombrecomercial}}" style="height: 35px;width:100%;">
                            <input type="hidden" name="empresa" value="{{$nombre_empresa->id}}">
                        </div>
                        <label for="detalle" class="col-md-1 texto">{{trans('contableM.autorizacion')}}:</label>
                        <div class="col-md-3">
                            <input class="form-control" name="autorizacion" id="autorizacion" style="height: 35px;width:100%;" required>
                        </div>
                    </div>
                    <div class="form-group  col-md-12" style="display: block;float:left !important">
                        <label for="empresa" class="col-md-1 texto">{{trans('contableM.estado')}}:</label>
                        <div class="col-md-3">
                            <select name="estado" id="estado" class="form-control" style="width:100%;" required>
                                <option value="">Seleccione</option>
                                <option value="1">{{trans('contableM.activo')}}</option>
                                <option value="0">{{trans('contableM.inactivo')}}</option>
                            </select>
                        </div>
                        <label for="detalle" class="col-md-1 texto">{{trans('contableM.detalle')}}</label>
                        <div class="col-md-3">
                            <textarea class="form-control" name="detalle" id="detalle" style="height: 35px;width:100%;" required></textarea>
                        </div>
                        <div class="col-md-2 ">
                            <button type="submit" class="btn btn-default btn-gray btn_add">
                                <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.guardar')}}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>


@endsection