@extends('trabajo_campo.base')
@section('action-content')
@php
$idusuario = Auth::user()->id;
$rolUsuario = Auth::user()->id_tipo_usuario;
@endphp

<style>
    th,
    td {
        text-align: center;
    }
</style>
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-md-10">
                    <h3 class="box-title">{{trans('tecnicof.fieldwork')}}</h3>
                </div>
                <div class="col-md-1" style="margin-bottom: 5px;">
                    <a href="{{route('trabajo_campo_create')}}" class="btn btn-primary">{{trans('tecnicof.creation')}}</a>
                </div>
                <form action="{{url('trabajo/campo/buscador')}}" method="post">
                    {{ csrf_field() }}
                    <div class="col-md-1" style="margin-bottom: 5px;">
                        <input type="hidden" name="excel" id="excel" value="0">
                        <button type="submit" onclick="cambiarExcel('e')" class="btn btn-primary">Excel</button>
                    </div>
                    <div class="col-md-4">
                        <label for="fecha" class="col-md-2 control-label">{{trans('tecnicof.from')}}</label>
                        <div class="col-md-10">
                            <input type="date" name="desde" id="desde" class="form-control" value="{{$fechaDesde}}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="fecha" class="col-md-2 control-label">{{trans('tecnicof.to')}}</label>
                        <div class="col-md-10">
                            <input type="date" name="hasta" id="hasta" class="form-control" value="{{$fechaHasta}}">
                        </div>
                    </div>
                    @if ($rolUsuario == 1 || $rolUsuario == 8 || $idusuario == '0925690851')
                    <div class=" col-md-4">
                        <label for="fecha" class="col-md-2 control-label">{{trans('tecnicof.user')}}</label>
                        <div class="col-md-10">
                            <select name="usuarios" class="js-data-example-ajax form-control"></select>
                        </div>
                    </div>
                    @endif
                    <div class=" form-group col-md-12">
                        <div class="text-right">
                            <button type="submit" onclick="cambiarExcel('b')" class="btn btn-primary btn-sm" id="boton_buscar">
                                <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                <div class="row">
                    <div class="col-sm-12">
                        <table id="example" class="table table-bordered table-responsive" role="grid" aria-describedby="example2_info">
                            <thead style="background-color: #337ab7;color:white;">
                                <tr role="row" id="cabezera">
                                    <th>{{trans('tecnicof.from')}}</th>
                                    <th>{{trans('tecnicof.to')}}</th>
                                    <th>{{trans('tecnicof.id')}}</th>
                                    <th>{{trans('tecnicof.names')}}</th>
                                    <th>{{trans('tecnicof.location')}}</th>
                                    <th>{{trans('tecnicof.observations')}}</th>
                                    <th>{{trans('tecnicof.action')}}</th>
                                </tr>
                            </thead>
                            <tbody id="cuerpo">
                                @foreach($datos as $value)
                                <tr>
                                    <td>
                                        {{$value->fecha_desde}}
                                    </td>
                                    <td>
                                        {{$value->fecha_hasta}}
                                    </td>
                                    <td>{{$value->usuario->id}}</td>
                                    <td>{{$value->usuario->apellido1}} {{$value->usuario->nombre1}}</td>

                                    <td>
                                        {{$value->lugar}}
                                    </td>
                                    <td>
                                        {{$value->observaciones}}
                                    </td>
                                    <td>
                                        <a href="{{route('trabajo_campo_editar',['id'=>$value->id])}}" class="btn btn-danger">{{trans('tecnicof.edit')}}</a>
                                    </td>
                                </tr>


                                @endforeach
                            </tbody>
                        </table>
                        <div class="col-md-12 text-right">
                            {{ $datos->appends(request()->except('page'))->links() }}.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">

    function cambiarExcel(l){
        if(l == 'e'){
            document.getElementById("excel").value = 1;
        }else{
            document.getElementById("excel").value = 0;
        }
    }

    $(document).ready(function() {
        var studentSelect = $('.js-data-example-ajax');
        $('.js-data-example-ajax').select2({
            tags: true,
            tokenSeparators: [','],
            minimumInputLength: 4,
            ajax: {
                url: "{{route('trabajo_campo_usuarios')}}",
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term
                    }
                },
                processResults: function(data, page) {

                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.nombreappe,
                                id: item.id
                            }
                            var option = new Option(item.nombreappe, item.id, true, true);
                            studentSelect.append(option).trigger('change');
                        })
                    };
                },
            },
        });
    });
</script>

@endsection