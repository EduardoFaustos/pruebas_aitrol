@extends('mantenimientos_botones_labs.mantenimiento_protocolo.base')
@section('action-content')

<section class="content">
    <div class="modal fade" id="examen" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content" style="width: 95%;" id="ax_mail">
          </div>
        </div>  
    </div>
    <div class="box">
        <div class="box-body">
            <form id="form_pro_agrup" method="post" action="">
                {{ csrf_field() }}
                
                <div class="col-md-8">
                    <h3 class="box-title"> {{(trans('protocolo.MantenimientoProtocolo'))}}</h3>
                </div>
                <div class="col-md-12" style="text-align: right">
                    <a href="{{route('protocolo.crear')}}" class="btn btn-primary" >{{(trans('protocolo.Crear'))}} </a>
                </div>
                
                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row" id="listado_pro_agrup">
                        <div class="table-responsive col-md-12">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th> {{(trans('protocolo.Nombre'))}} </th>
                                        <th> {{(trans('protocolo.EstadoAmbulatorio'))}} </th>
                                        <th> Pre/Post </th>
                                        <th> {{(trans('protocolo.Accion'))}} </th>
                                       
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($protocolos as $protocolo)

                                    <tr>
                                        <td>{{$protocolo->id}}</td>
                                        <td>{{$protocolo->nombre}}</td>
                                        <td>
                                            @if($protocolo->est_amb_hos == 0)
                                             Ambulatorio
                                            @elseif($protocolo->est_amb_hos == 1)
                                             Hospitalizado
                                            @endif

                                        </td>
                                        <td>{{$protocolo->pre_post}}</td>
                                      
                                        <td>
                                            <a href="{{route('protocolo.editar' ,['id'=>$protocolo->id])}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                                            <a class="btn btn-primary" data-toggle="modal" data-target="#modalExamen"> {{(trans('protocolo.Examen'))}} </a>
                                            
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
    @include('mantenimientos_botones_labs.mantenimiento_protocolo.modal.examen')
</section>
@endsection