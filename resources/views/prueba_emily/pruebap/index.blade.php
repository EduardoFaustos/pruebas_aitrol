@extends('prueba_emily/pruebap/base')
@section('action-content')

<section class="content">
<div class="box">
<div class="box-header">
         <div class="col-md-2"> 
        <a class="btn btn-primary" href="{{route('crearpro')}}">Crear</a>
          </div>
        
                  
        <div class="box-body">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap ">
                <div class="table-responsive col-md-12 col-xs-12">
                    <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                        <thead>
                            <tr>
                                <th>id producto</th>
                                <th>codigo</th>
                                <th>nombre</th>
                                <th>descripcion</th>
                                <th>categoria</th>
                                </tr>
                        </thead>
                        <tbody>
                        @foreach ($productos as $producto)
                        <tr>
                            <td>{{$producto->id}}</td>
                            <td>{{$producto->codigop}}</td>
                            <td>{{$producto->nombrep}}</td>
                            <td>{{$producto->descripcionp}}</td>


                          <td><a class="btn btn-primary" href="{{route('editarpro',['id' => $producto->id_producto])}}">Editar</a></td>
                      

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