@extends('servicios_generales.mantenimientos_dotacion.base')
@section('action-content')


<!-- Main content -->
<section class="content">
    <div class="box">
        <div class="box-body">
            <form id="form_pro_agrup" method="post" action="">
                {{ csrf_field() }}
                
                <div class="col-md-8">
                    <h3 class="box-title"> Mantenimiento Dotación </h3>
                </div>
                <div class="col-md-12" style="text-align: right">
                    <a href="{{route('mantenimientos_d.crear')}}" class="btn btn-primary" >Crear </a>
                </div>

                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row" id="listado_pro_agrup">
                        <div class="table-responsive col-md-12">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                <thead>
                                <tr>

                                         <th>Nombre</th>
                                        <th> Descripción </th>
                                        <th> Estado </th>
                                        <th> Editar </th>
                                       
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($mantenimientos_dotaciones as $value)

                                    <tr>

                                        <td>{{$value->nombre}}</td>
                                        <td>{{$value->descripcion}}</td>
                                        <td> @if($value->estado == 1)
                                                Activo
                                            @elseif($value->estado == 0)
                                            Inactivo
                                            @endif
                                        </td>
                                       
                                        <td>
                                            <a href="{{Route('mantenimientos_d.editar', ['id'=>$value->id])}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                                        
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