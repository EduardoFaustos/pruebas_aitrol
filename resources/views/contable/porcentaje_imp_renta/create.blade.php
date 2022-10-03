@extends('contable.tipo_emision.base')
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
        <li class="breadcrumb-item"><a href="{{route('porcentaje_imp_renta.index')}}">Porcentaje IR</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.crear')}}</li>
      </ol>
    </nav>
    <form id="enviar_porcentaje_imp_renta" class="form-vertical" role="form" method="POST" action="{{route('porcentaje_imp_renta.store')}}">
        {{ csrf_field() }}
        <div class="box">
          <div class="box-header color_cab">
            <div class="col-md-9">
              <h5><b>CREAR PORCENTAJE IR</b></h5>
            </div>
            <div class="col-md-1 text-right">
              <button onclick="goBack()" class="btn btn-primary btn-gray" >
                  <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
              </button>
            </div>  
          </div>
          <div class="separator"></div>
          <div class="box-body dobra">  
            <!--Porcentaje IR-->
            <div class="form-group  col-xs-6">
              <label for="porcentaje" class="col-md-4 texto">Porcentaje IR:</label> 
              <div class="col-md-7">
                <input id="porcentaje" name="porcentaje" type="text" class="form-control" placeholder="Porcentaje IR" autocomplete="off" required autofocus>
              </div>
              <label for="porcentaje" class="col-md-1 texto">%</label> 
            </div>
            <!--Año-->
            <div class="form-group  col-xs-6">
                <label for="anio_porcentaje_ir" class="col-md-4 texto">Año:</label>
                <div class="col-md-7">
                  <input id="anio_porcentaje_ir" name="anio_porcentaje_ir" type="text" maxlength="4" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" placeholder="año" autocomplete="off" required autofocus>
                </div>
            </div>
            <!--ESTADO Porcentaje IR-->
            <div class="form-group col-md-6">
                <label for="estado_porcentaje_ir" class="col-md-4 texto">{{trans('contableM.estado')}}</label>
                <div class="col-md-7">
                  <select id="estado_porcentaje_ir" name="estado_porcentaje_ir" class="form-control" required>
                    <option>Seleccione...</option>
                    <option value="1">{{trans('contableM.activo')}}</option>
                    <option value="0">{{trans('contableM.inactivo')}}</option>
                  </select>
                </div>
            </div>
            <!--BUTTON AGREGAR Porcentaje IR-->
            <div class="form-group col-xs-10 text-center">
              <div class="col-md-6 col-md-offset-4">
                  <button type="submit"  class="btn btn-success btn-gray btn_add">
                    <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.agregar')}}
                  </button>
              </div>
            </div>
          </div>
        </div>
    </form>
    
    
  </section>
     
@endsection
