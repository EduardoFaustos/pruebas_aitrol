@extends('contable.tipo_comprobante.base')
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
        <li class="breadcrumb-item"><a href="{{route('tipo_comprobante.index')}}">{{trans('contableM.tipocomprobante')}}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.actualizar')}}</li>
      </ol>
    </nav>
    <form  class="form-vertical" role="form" method="POST" action="{{route('tipo_comprobante.update')}}">
      {{ csrf_field() }}
      <input  name="id_tip_comprobante" id="id_tip_comprobante" type="text" class="hidden" value="@if(!is_null($tip_comprobante)){{$tip_comprobante->id}}@endif">
      <div class="box">
        <div class="box-header color_cab">
          <div class="col-md-9">
            <h5><b>DETALLE TIPO COMPROBANTE</b></h5>
          </div>
          <div class="col-md-1 text-right">
              <button onclick="goBack()" class="btn btn-default btn-gray" >
                  <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
              </button>
          </div>
        </div>  
        <div class="separator"></div>
        <div class="box-body dobra">
          <!--NOMBRE COMPROBANTE-->
          <div class="form-group  col-xs-6">
            <label for="nomb_comprobante" class="col-md-4 texto">Nombre Comprobante:</label>
            <div class="col-md-7">
              <input id="nomb_comprobante" name="nomb_comprobante" type="text" class="form-control" value="@if(!is_null($tip_comprobante)){{$tip_comprobante->nombre_comprobante}}@endif" autocomplete="off" required autofocus>
            </div>
          </div>
          <!--CODIGO COMPROBANTE-->
          <div class="form-group  col-xs-6">
              <label for="codig_comprobante" class="col-md-4 texto">{{trans('contableM.codigo')}}:</label>
              <div class="col-md-7">
                <input id="codig_comprobante" name="codig_comprobante" type="text" maxlength="2" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="@if(!is_null($tip_comprobante)){{$tip_comprobante->codigo}}@endif"  autocomplete="off" required autofocus>
              </div>
          </div>
          <!--ESTADO TIPO COMPROBANTE-->
          <div class="form-group col-md-6">
              <label for="estado_tip_comprobante" class="col-md-4 texto">{{trans('contableM.estado')}}</label>
              <div class="col-md-7">
                <select id="estado_tip_comprobante" name="estado_tip_comprobante" class="form-control" required>
                  <option {{ $tip_comprobante->estado == 1 ? 'selected' : ''}} value="1">{{trans('contableM.activo')}}</option>
                  <option {{ $tip_comprobante->estado == 0 ? 'selected' : ''}} value="0">{{trans('contableM.inactivo')}}</option>
                </select>
              </div>
          </div>
          <div class="form-group col-xs-10 text-center">
            <div class="col-md-6 col-md-offset-4">
                <button type="submit"  class="btn btn-default btn-gray btn_add">
                  <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Actualizar
                </button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </section>
@endsection
