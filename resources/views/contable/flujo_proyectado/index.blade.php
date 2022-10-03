@extends('contable.flujo_proyectado.base')
@section('action-content')

<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.Contabilidad')}}</a></li>
            <li class="breadcrumb-item"><a href="../">{{trans('contableM.FlujodeEfectivoProyectado')}}</a></li>
        </ol>
    </nav>

    <div class="box">
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" for="title">{{trans('contableM.Buscador')}}</label>
            </div>
        </div>

        <div class="box-body dobra">

            <form method="POST" id="form_flujo" action="{{route('reporteflujo.excel_flujo')}}">
                {{ csrf_field() }}
                <div class="form-group col-md-4 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
                    <label for="anio" class="texto col-md-3 control-label">{{trans('contableM.Anio')}}</label>
                    <div class="col-md-9">
                        @php $x = date('Y'); @endphp
                        <select id="anio" name="anio" class="form-control">
                            @for($i=2021;$i<=$x;$i++) <option @if($anio==$i) selected @endif value='{{$i}}'>{{$i}}</option>
                                @endfor

                        </select>
                    </div>
                </div>
                <div class="form-group col-md-4 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
                    <label for="mes" class="texto col-md-3 control-label">{{trans('contableM.mes')}}</label>
                    <div class="col-md-9">
                        @php
                            $meses = array('Seleccione','Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio','Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
                        @endphp
                        <select id="mes" name="mes" class="form-control">
                            @foreach($meses as $key => $imes)
                            <option @if($mes==$key) selected @endif value='{{$key}}'>{{$imes}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-md-4 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
                    <button type="submit" class="btn btn-primary" id="boton_buscar">Exportar</button>
                    
                </div>
            </form>
        </div>
    </div>
</section>
@endsection