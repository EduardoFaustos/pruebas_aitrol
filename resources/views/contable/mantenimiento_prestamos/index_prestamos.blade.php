@extends('contable.mantenimiento_prestamos.base')
@section('action-content')
<section class="con tent">
<div class="box">
<div class="box-header">
         <div class="col-md-2"> 
        <a class="btn btn-primary" href="{{route('mantenimientoprestamos.crear')}}">{{trans('contableM.crear')}}</a>
         </div>
  
        <div class="box-body">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap ">
                <div class="table-responsive col-md-12 col-xs-12">
                    <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>id_ct_rh_prestamos</th>
                                <th>anio</th>
                                <th>mes</th>
                                <th>{{trans('contableM.fecha')}}</th>
                                <th>cuota</th>
                                <th>valor_cuota</th>
                                <th>id_ct_rol_pagos</th>
                                <th>{{trans('contableM.estado')}}</th>
                                <th>estado_pago</th>
                                <th>fecha_pago</th>
                                <th>id_usuariocrea</th>
                                <th>id_usuariomod</th>
                                <th>ip_creacion</th>
                                <th>ip_modificacion</th>
                                <th>created_at</th>
                                <th>updated_at</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($prestamos as $prestamo)
                        <tr>
                            <td>{{$prestamo->id}}</td>
                            <td>{{$prestamo->id_ct_rh_prestamos}}</td>
                            <td>{{$prestamo->anio}}</td>
                            <td>{{$prestamo->mes}}</td>
                            <td>{{$prestamo->fecha}}</td>
                            <td>{{$prestamo->cuota}}</td>
                            <td>{{$prestamo->valor_cuota}}</td>
                            <td>{{$prestamo->id_ct_rol_pagos}}</td>
                            <td>{{$prestamo->estado}}</td>
                            <td>{{$prestamo->estado_pago}}</td>
                            <td>{{$prestamo->fecha_pago}}</td>
                            <td>{{$prestamo->id_usuariocrea}}</td>
                            <td>{{$prestamo->id_usuariomod}}</td>
                            <td>{{$prestamo->ip_creacion}}</td>
                            <td>{{$prestamo->ip_modificacion}}</td>
                            <td>{{$prestamo->created_at}}</td>
                            <td>{{$prestamo->updated_at}}</td>

                          <td><a class="btn btn-primary" href="">Editar</a></td>
                      

                        </tr>
                        @endforeach
                        
                        </tbody>
                    
                    </table>    
                   </div>
              
            </div>
         </div>
        </div>
        </div>
        </form>
</section>

@endsection