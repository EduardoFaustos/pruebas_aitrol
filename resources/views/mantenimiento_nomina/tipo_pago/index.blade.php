@extends('mantenimiento_nomina.tipo_pago.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-body">
            <form id="form_pro_agrup" method="post" action="">
                {{ csrf_field() }}
                
                <div class="col-md-8">
                    <h3 class="box-title"> Mantenimiento Tipo Pago </h3>
                </div>
                <div class="col-md-12" style="text-align: right">
                    <a href="{{route('tipo_pago.crear')}}" class="btn btn-primary" >Crear </a>
                </div>

                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row" id="listado_pro_agrup">
                        <div class="table-responsive col-md-12">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                <thead>
                                    <tr>
                                         <th>Id</th>
                                        <th> Tipo </th>
                                        <th> Acción </th>
                                       
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($tipos_pagos as $tipo_pago)

                                    <tr>
                                        <td>{{$tipo_pago->id}}</td>
                                        <td>{{$tipo_pago->tipo}}</td>
                                      
                                        <td>
                                            
                                            <a href="{{route('tipo_pago.editar' ,['id'=>$tipo_pago->id])}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
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