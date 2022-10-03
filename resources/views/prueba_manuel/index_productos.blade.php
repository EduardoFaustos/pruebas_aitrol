@extends('prueba_manuel/base')
@section('action-content')
<section class="content">
<div class="box">
<div class="box-header">
         <div class="col-md-2"> 
        <a class="btn btn-primary" href="{{route('crear.manuel')}}">Crear</a>
          </div>
        
                  
        <div class="box-body">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap ">
                <div class="table-responsive col-md-12 col-xs-12">
                    <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>nombre</th>
                                <th>descripcion</th>
                                <th>valor</th>
                                </tr>
                        </thead>
                        <tbody>
                        @foreach ($productos as $producto)
                        <tr>
                            <td>{{$producto->id}}</td>
                            <td>{{$producto->nombre}}</td>
                            <td>{{$producto->descripcion}}</td>
                            <td>{{$producto->valor}}</td>
                        
                          <td><a class="btn btn-primary" href="{{route('editar.manuel',['id' => $producto->id])}}">Editar</a></td>
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