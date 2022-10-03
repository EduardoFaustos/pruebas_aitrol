@extends('archivo_plano/mantenimientoinsumos/base')
@section('action-content')
<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        @page {
            margin: 45px 80px;
        }

        .table>tbody>tr>td {
            padding: 0;
        }

        #paginacion {
            border: 1px solid #CCC;
            background-color: #E0E0E0;
            padding: .5em;
            overflow: hidden;
        }

        .texto {
            font-size: 15.0px;
            font-family: Arial;
        }
    </style>
</head>
<div class="modal fade" id="codigo_proceso" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<section class="content" >
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-sm-11">
                    <h3 class="box-title">Crear Nuevo Insumo</h3>
               
        </div>

<form method="post"  action="{{route('guardar_insumos')}}" >
{{ csrf_field() }}

<div class="row justify-content-center">
	    <div class="card-body">
	    	<div class="form-group col-md-10">
                <label for="tipo">Tipo</label>
				<input type="text" name="tipo" size="20" class="form-control" value="" required  maxlength="100">
			</div>
		</div>
     </div>  



 <div class="row justify-content-center">
	    <div class="card-body">
	    	<div class="form-group col-md-10">
                <label for="codigo">Codigo</label>
				<input type="number" name="codigo" size="20" class="form-control" value="{{$insu}}" onlyread required  maxlength="100">
			</div>
		</div>
     </div>  
        

   

        <div class="row justify-content-center">
	    <div class="card-body">
	    	<div class="form-group col-md-10">
                <label for="descripcion">Descripcion</label>
				<input type="text" name="descripcion" size="20" class="form-control" value="" required  maxlength="100">
			</div>
		</div>
     </div>

     <div class="row justify-content-center">
	    <div class="card-body">
	    	<div class="form-group col-md-10">
                <label for="valor">Valor</label>
				<input type="text" name="valor" size="20" class="form-control" value="" required  maxlength="100">
			</div>
		</div>
     </div>
 
     <div class="row justify-content-center">
	    <div class="card-body">
	    	<div class="form-group col-md-10">
                <label for="estado">Estado</label>
				<input type="text" name="estado" size="20" class="form-control" value="" required  maxlength="100">
			</div>
		</div>
     </div> 

     <div class="row justify-content-center">
	    <div class="card-body">
	    	<div class="form-group col-md-10">
                <label for="usuarioc">Id Usuario</label>
				<input type="text" name="id_usuariocrea" size="20" class="form-control" value="" required  maxlength="100">
			</div>
		</div>
     </div>

     <div class="row justify-content-center">
	    <div class="card-body">
	    	<div class="form-group col-md-10">
                <label for="ipcreacion">Ip Creacion</label>
				<input type="text" name="ip_creacion" size="20" class="form-control" value="" required  maxlength="100">
			</div>
		</div>
     </div>
     

     <div class="container">
    <div class="form-group col-md-5 " >
    <button  type="submit" class="btn btn-primary btn-sm">Guardar</button>         
    </div>
 </div>

 </form>
</section>
    
@endsection
