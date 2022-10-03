@extends('plantillas_labs.mantenimiento_derivados.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-body">
            <form id="form_pro_agrup" method="post" action="">
                {{ csrf_field() }}
                
                <div class="col-md-8">
                    <h3 class="box-title"> Mantenimiento Examenes Derivados </h3>
                </div>
                <div class="col-md-12" style="text-align: right">
                    <a href="" class="btn btn-primary" >Crear </a>
                </div>

                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row" id="listado_pro_agrup">
                        <div class="table-responsive col-md-12">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                <thead>
                                    <tr>
                                         <th>Id</th>
                                        <th> Examen </th>
                                        <th> Valor </th>
                                        <th> Tipo  </th>
                                        <th> Estado </th>
                                        <th>  Acción  </th>
                                       
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($examenes_derivados as $exam_derivado)

                                    <tr>
                                        <td>{{$exam_derivado->id}}</td>
                                        <td>{{$exam_derivado->examen->nombre}}</td>
                                        <td>  {{$exam_derivado->valor}} </td>
                                        <td>  {{$exam_derivado->tipo_derivado->nombre}} </td>
                                        @if ($exam_derivado->estado == 1)
                                        <td>  Activo </td>
                                        @endif
                                        <td>
                                            
                                            <a href="" class="btn btn-warning"><i class="fa fa-edit"></i></a>
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