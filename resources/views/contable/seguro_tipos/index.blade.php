@extends('contable.seguro_tipos.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-body">
            <form id="form_pro_agrup" method="post" action="">
                {{ csrf_field() }}
                
                <div class="col-md-8">
                    <h3 class="box-title"> Tipos de Seguros</h3>
                </div>
                <div class="col-md-12" style="text-align: right">
                    <a href="{{route('seguroTipos.create')}}" class="btn btn-primary" >Crear </a>
                </div>
                
                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row" id="listado_pro_agrup">
                        <div class="table-responsive col-md-12">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th> Nombre</th>
                                        <th> Detalle</th>
                                        <th> Estado</th>
                                        <th> Accion</th>
                                       
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($seguro_tipos as $tipo)

                                    <tr>
                                        <td>{{$tipo->id}}</td>
                                        <td>{{$tipo->nombre}}</td>
                                        <td>{{$tipo->detalle}}</td>
                                        <td>
                                            @if($tipo->estado == 1)
                                                Activo
                                            @elseif($tipo->estado == 0)
                                                Inactivo
                                            @endif

                                        </td>
                                      
                                        <td>
                                            <a href="{{route('seguroTipos.edit' ,['id'=>$tipo->id])}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
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