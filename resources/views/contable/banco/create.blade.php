@extends('contable.caja_banco.base')
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

    function obtener_detalle_grupo(){

      var valor= $("#grupo_plan_cuenta").val();

        $.ajax({
          type: 'post',
          url:"{{route('caja_banco.detallegrupo')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: {'opcion': valor},
          success: function(data){

            if(data.value!='no'){

              if(valor!=0){
                
                $("#detalle_grupo").empty();
                $.each(data,function(key, registro) {
                  $("#detalle_grupo").append('<option value='+registro.id+'>'+registro.nombre+'</option>');
                });
            
              }else{
                  $("#detalle_grupo").empty();
              }

            }
          },
          error: function(data){
            console.log(data);
          }

        })

    }
 
  </script>

  <section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
        <li class="breadcrumb-item"><a href="../banco">{{trans('contableM.banco')}}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.crear')}}</li>
      </ol>
    </nav>
    <div class="box">
        <div class="box-header">
            <div class="col-md-9">
              <h3 class="box-title">Crear Banco</h3>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="goBack()" class="btn btn-default btn-gray" >
                   <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                </button>
            </div>
        </div>
        <div class="box-body" style="background-color: #D4D0C8;">
          <form id="enviar_caja_banco" class="form-vertical" role="form" method="POST" action="{{route('banco_clientes.store')}}">
            {{ csrf_field() }}
            
            <div class="separator"></div>
            <div class="box-body col-xs-24">
              <!--NOMBRE CAJA BANCO-->
              <div class="form-group  col-xs-6">
                  <label for="nombre_caja_banco" class="col-md-4 texto">{{trans('contableM.nombre')}}:</label>
                  <div class="col-md-7">
                     <input id="nombre_caja_banco" name="nombre_caja_banco" type="text" class="form-control" placeholder="Nombre" required autofocus>
                  </div>
              </div>
              <!--NUMERO DE CUENTA CAJA BANCO
              <div class="form-group  col-xs-6">
                <label for="numero_cuenta" class="col-md-4 texto">Número de Cuenta:</label>
                <div class="col-md-7">
                  <input id="numero_cuenta" name="numero_cuenta" type="number" class="form-control" placeholder="Número de cuenta">
                </div>
              </div>-->
              <!--ESTADO CAJA BANCO-->
              <div class="form-group col-md-6">
                  <label for="estado_caj_banco" class="col-md-4 texto">{{trans('contableM.estado')}}</label>
                  <div class="col-md-7">
                    <select id="estado_caj_banco" name="estado_caj_banco" class="form-control" required>
                      <option>Seleccione...</option>
                      <option value="1">{{trans('contableM.activo')}}</option>
                      <option value="0">{{trans('contableM.inactivo')}}</option>
                    </select>
                  </div>
              </div>
              <div class="form-group col-xs-10 text-center">
                <div class="col-md-6 col-md-offset-4">
                    <button type="submit"  class="btn btn-default btn-gray btn_add">
                      <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.agregar')}}
                    </button>
                </div>
              </div>
            </div>
          </form>
        </div>
    </div>
  </section>
     
@endsection
