@extends('layouts.app-template-h')
@section('content')
<style>
h3, h5{

}
h5{
  color: black;
}
#example2 td{
  color: black;
  font-weight: bold;
  text-align: center;
}
#example2 td, #example2 th {
  border: 1px solid #e0e0e0;
  padding: 5px;
}
#example2 th {
  padding-top: 6px;
  padding-bottom: 8px;
  text-align: center;
  background: linear-gradient(135deg, #3352ff 0%, #051eff 100%);
  color: white;
}
#exaple2 tr:nth-child(even){background-color: white;}
#example2 tr:hover {background-color: white  ;}
</style>

<div class="content">
  <div class="card-header">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-10 col-sm-6 col-6">
        <h3 style="border-bottom: 2px solid #F39C12"> MARCAS</h3>
        </div>
        <div class="col-md-2 col-6 my-2">
          <a type="button" href="{{route('hospital.farmacia') }}" class="form-control btn btn-primary btn-sm">
            <span style="width: 100%;">Regresar</span>
          </a>
        </div>
      </div>
    </div>
  </div>
  <section class="content">
    <div class="card">
      <div class="col-12" >
        <form method="POST" id="form_buscador">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-md-9 col-sm-6">
              <h5>B&Uacute;SQUEDA DE MARCAS</h5>
            </div>
              <div class="col-md-3 col-sm-6 my-2">
                <input type = "text" class ="form-control" required maxlength="30" placeholder="Nombre de Marcas" name="nombre" id="nombre">
              </div>
          </div>
        </form>
      </div>
      <div id="resultados">
      
    </div>
    <div id="dada">
      <div class="card-body table-responsive" >
        <table class="table table-hover" aria-describedby="example2_info">
          <thead class="table-primary">
            <tr>
              <th scope="col">Nombre</th>
              <th scope="col">Descripci&oacute;n</th>
              <th scope="col">Estado</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($marcas as $value)
              <tr role="row" class="odd">
                <td> {{ $value->nombre}}</td>
                <td>{{ $value->descripcion }}</td>
                <td @if($value->estado==1) bgcolor='#69f0ae'  @elseif($value->estado==2) bgcolor='#d32f2f' @endif> @if($value->estado==1)  ACTIVO   @elseif($value->estado==2)  INACTIVO @endif</td> 
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<script>

  window.addEventListener('load',function(){
    document.getElementById("nombre").addEventListener("keyup", () => {
      if((document.getElementById("nombre").value.length)>0)
      fetch(`tablamarca/buscador?nombre=${document.getElementById("nombre").value}`,{ method:'get' })
      .then(response  =>  response.text() )
      .then(html      =>  {   document.getElementById("resultados").innerHTML = html  })
      else
       document.getElementById("resultados").innerHTML= document.getElementById("dada").innerHTML;
       $("#dada").hide();
    })  
  }); 
   
</script>
@endsection