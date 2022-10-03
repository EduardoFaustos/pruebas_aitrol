@extends('mantenimientos_botones_labs.mantenimiento_nivel.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-body">
            <form id="form_pro_agrup" method="post" action="">
                {{ csrf_field() }}
                
                <div class="col-md-8">
                    <h3 class="box-title"> {{(trans('nivel.MantenimientoNivel'))}}</h3>
                </div>
                <div class="col-md-12" style="text-align: right">
                    <a href="{{route('nivel.crear')}}" class="btn btn-primary" >{{(trans('nivel.Crear'))}} </a>
                </div>
                
                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row" id="listado_pro_agrup">
                        <div class="table-responsive col-md-12">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th> {{(trans('nivel.Nombre'))}} </th>
                                        <th> {{(trans('nivel.NombreCorto'))}} </th>
                                        <th> {{(trans('nivel.Estado'))}} </th>
                                        <th> {{(trans('nivel.Grupo'))}} </th>
                                        <th> {{(trans('nivel.Accion'))}} </th>
                                       
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($niveles as $nivel)

                                    <tr>
                                        <td>{{$nivel->id}}</td>
                                        <td>{{$nivel->nombre}}</td>
                                        <td>{{$nivel->nombre_corto}}</td>
                                        <td>
                                            @if($nivel->estado == 1)
                                                {{(trans('nivel.Activo'))}}
                                            @elseif($nivel->estado == 0)
                                            {{(trans('nivel.Inactivo'))}}
                                            @endif

                                        </td>
                                        <td>{{$nivel->grupo}}</td>
                                      
                                        <td>
                                            <a href="{{route('nivel.editar' ,['id'=>$nivel->id])}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
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




























@endsection