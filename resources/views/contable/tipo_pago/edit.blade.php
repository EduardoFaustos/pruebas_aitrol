@extends('contable.tipo_pago.base')
@section('action-content')

<style type="text/css">
  .separator{
    width:100%;
    height:30px;
    clear: both;
  }
</style>

  <script type="text/javascript">
    
    //Valida que solo ingrese numeros
    function check(e){
        tecla = (document.all) ? e.keyCode : e.which;

        //Tecla de retroceso para borrar, siempre la permite
        if (tecla == 8) {
            return true;
        }

        // Patron de entrada, en este caso solo acepta numeros y letras
        patron = /[A-Za-z0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    }

    //Retorna a la pagina anterior
    function goBack() {
      window.history.back();
    }

  </script>

  <section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
        <li class="breadcrumb-item"><a href="../ambiente">Tipo Ambiente</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.actualizar')}}</li>
      </ol>
    </nav>
    <form  class="form-vertical" role="form" method="POST" action="{{route('tipo_ambiente.update')}}">
      {{csrf_field()}}
      <input  name="id_tipo_ambiente" id="id_tipo_ambiente" type="text" class="hidden" value="@if(!is_null($tip_ambiente)){{$tip_ambiente->id}}@endif">
      <div class="box">

      </div>  
    
    </form>
  </section>
     
@endsection
