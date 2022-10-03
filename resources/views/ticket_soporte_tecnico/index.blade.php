@extends('ticket_soporte_tecnico.base')
@section('action-content')
<style>
    .stilos:focus {

        background: whitesmoke;
    }

    #estilos:hover {
        color: blanchedalmond;
    }
</style>
@php
$rolUsuario = Auth::user()->id_tipo_usuario;
$id_fellow = Auth::user()->id;

@endphp
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="box-title">{{trans('tecnicof.technical')}}</h3>
                </div>
                <form action="{{url('ticket_soporte_tecnico/buscador')}}" id="formi" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="excel_bool" id="excel_bool" value="0">
                    <div class="col-md-1">
                        <label for="">{{trans('tecnicof.state')}}</label>
                    </div>
                    <div class="col-md-2">
                        <select class="form-control" name="estado" id="estado">
                            <option value="">{{trans('tecnicof.state')}}</option>
                            <option value="0">{{trans('tecnicof.initial')}}</option>
                            <option value="1">{{trans('tecnicof.process')}}</option>
                            <option value="2">{{trans('tecnicof.process')}}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="fecha" class="col-md-6 control-label">{{trans('tecnicof.from')}}</label>
                        <div class="col-md-6">
                            <input style="text-align: center;line-height:10px;" type="date" name="desde" id="desde" class="form-control" value="{{$fecha_desde}}">
                        </div>
                    </div>


                    <div class="col-md-4">
                        <label for="fecha" class="col-md-6 control-label">{{trans('tecnicof.to')}}</label>
                        <div class="col-md-6">
                            <input style="text-align: center;line-height:10px;" type="date" name="hasta" id="hasta" class="form-control" value="{{$fecha_hasta}}">
                        </div>
                    </div>
                    <div class="form-group col-md-1 col-xs-1">
                        <button type="submit" class="btn btn-primary btn-sm" id="boton_buscar">
                            <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                    </div>

                </form>
            </div>
            <div class="col-md-12 text-right">
                @if($rolUsuario == 1)
                <button onclick="boolchange()" class="btn btn-primary"><i aria-hidden="true"></i> {{trans('tecnicof.reports')}}</button>
                @endif
                <a href="{{route('ticket_soporte_tecnico.create')}}" class="btn btn-primary"><i aria-hidden="true"></i> {{trans('tecnicof.register')}}</a>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                <div class="row">
                    <div class="col-sm-12">
                        <table id="example" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                            <thead style="background-color: #337ab7;color:white;">
                                <tr role="row" id="cabezera">
                                    <th>{{trans('tecnicof.request')}}</th>
                                    <th>{{trans('tecnicof.area')}}</th>
                                    <th>{{trans('tecnicof.requirement')}}</th>
                                    <th>{{trans('tecnicof.state')}}</th>
                                    @if($rolUsuario==1)<th>{{trans('tecnicof.dater')}}</th>@endif
                                    @if($rolUsuario==1)<th>{{trans('tecnicof.starttime')}}</th>@endif
                                    @if($rolUsuario==1)<th>{{trans('tecnicof.createdby')}}</th>@endif
                                    @if($rolUsuario==1)<th>{{trans('tecnicof.action')}}</th>@endif
                                </tr>
                            </thead>
                            <tbody id="cuerpo">
                                @foreach ($soportes as $soporte)
                                @php
                                //dd($soportes);
                                $horai = substr($soporte->created_at, 11, 18);
                                $horaf = substr($soporte->updated_at, 11, 18);
                                $fcreada = substr($soporte->updated_at, 2, 9);
                                $factualizada = substr($soporte->updated_at, 2, 9);
                                $nombr = Sis_medico\User::where('id',$soporte->usuario_solicitante)->first();
                                @endphp
                                <tr>
                                    <td>{{$soporte->id}}</td>
                                    <td>{{$soporte->area}}</td>
                                    <td>{{$soporte->requerimientos}}</td>
                                    <td @if($soporte->estado == 0 ) bgcolor='#1eb067'
                                        @elseif ($soporte->estado == 1) bgcolor='#e9fc3a'
                                        @elseif($soporte->estado == 2) bgcolor='#fc3a3a'
                                        @endif> @if($soporte->estado == 0) {{trans('tecnicof.initial')}}
                                        @elseif($soporte->estado == 1) {{trans('tecnicof.process')}}
                                        @elseif($soporte->estado == 2) {{trans('tecnicof.completed')}} @endif</td>
                                    <td>{{substr($soporte->created_at,0,10)}}</td>
                                    @if($rolUsuario==1)<td>{{$horai}}</td>@endif
                                    @if($rolUsuario==1)<td>@if(!is_null($soporte->usuario_solicitante) && !empty($soporte->usuario_solicitante)) {{$nombr->nombre1}} {{$nombr->nombre2}} {{$nombr->apellido1}} {{$nombr->apellido2}} @else @if (isset($soporte->nombre1)) {{$soporte->nombre1->nombre1}} {{$soporte->nombre1->nombre2}} {{$soporte->nombre1->apellido1}} {{$soporte->nombre1->apellido2}} @endif @endif</td>@endif
                                    <input type="hidden" value="{{$soporte->observacion}}" name="soporte_ob">
                                    @if($rolUsuario==1)<td id="buton{{$soporte->id}}">@if($soporte->estado == 0 || $soporte->estado == 1)<a href="{{route('ticket_soporte_tecnico.control_req',['id' => $soporte->id])}}" onclick="gestionar(<?= $soporte->id ?>)" id="gestionar{{$soporte->id}}" class="btn btn-primary" type="button"> {{trans('tecnicof.managed')}} </a>@elseif(($soporte->estado)==2)<button type="button" class="btn btn-primary">{{trans('tecnicof.completed')}}</button> @endif</td>@endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="col-md-12 text-right">
                            {{ $soportes->appends(request()->except('page'))->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    function boolchange() {
        document.getElementById("excel_bool").value = 1;
        document.getElementById("formi").submit();
        document.getElementById("excel_bool").value = 0;
    }
</script>

@endsection