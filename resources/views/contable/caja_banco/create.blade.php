@extends('contable.caja_banco.base')
@section('action-content')

<style type="text/css">
  .separator {
    width: 100%;
    height: 30px;
    clear: both;
  }
</style>


<script type="text/javascript">
  //Valida que solo ingrese numeros
  function check(e) {
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

  function obtener_detalle_grupo() {

    var valor = $("#grupo_plan_cuenta").val();

    $.ajax({
      type: 'post',
      url: "{{route('caja_banco.detallegrupo')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: {
        'opcion': valor
      },
      success: function(data) {

        if (data.value != 'no') {

          if (valor != 0) {

            $("#detalle_grupo").empty();
            $.each(data, function(key, registro) {
              $("#detalle_grupo").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
            });

          } else {
            $("#detalle_grupo").empty();
          }

        }
      },
      error: function(data) {
        console.log(data);
      }

    })

  }
</script>

<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('mcaja_banco.contable')}}</a></li>
      <li class="breadcrumb-item"><a href="../banco">{{trans('mcaja_banco.cajabanco')}}</a></li>
      <li class="breadcrumb-item active" aria-current="page">{{trans('mcaja_banco.crear')}}</li>
    </ol>
  </nav>
  <form id="enviar_caja_banco" class="form-vertical" role="form" method="POST" action="{{route('caja_banco.store')}}">
    {{ csrf_field() }}
    <div class="box">
      <input type="text" name="id_empresa" id="id_empresa" class="hidden" value="{{$empresa->id}}">
      <div class="box-header color_cab">
        <div class="col-md-9">
          <h5><b>{{trans('mcaja_banco.crearcajaybanco')}}</b></h5>
        </div>
        <div class="col-md-1 text-right">
          <button onclick="goBack()" class="btn btn-default btn-gray">
            <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('mcaja_banco.regresar')}}
          </button>
        </div>
      </div>
      <div class="separator"></div>
      <div class="box-body dobra">
        <!--CODIGO CAJA BANCO-->
        <div class="form-group  col-xs-6">
          <label for="cod_caja_banco" class="col-md-4 texto">{{trans('mcaja_banco.codigo')}}:</label>
          <div class="col-md-7">
            <input id="cod_caja_banco" name="cod_caja_banco" type="text" class="form-control" placeholder="Código" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" autocomplete="off" required autofocus>
          </div>
        </div>
        <!--NOMBRE CAJA BANCO-->
        <div class="form-group  col-xs-6">
          <label for="nombre_caja_banco" class="col-md-4 texto">{{trans('mcaja_banco.nombre')}}:</label>
          <div class="col-md-7">
            <input id="nombre_caja_banco" name="nombre_caja_banco" type="text" class="form-control" placeholder="{{trans('mcaja_banco.nombre')}}" autocomplete="off" required autofocus>
          </div>
        </div>
        <!--NUMERO DE CUENTA CAJA BANCO-->
        <div class="form-group  col-xs-6">
          <label for="numero_cuenta" class="col-md-4 texto">{{trans('mcaja_banco.numerodecuenta')}}:</label>
          <div class="col-md-7">
            <input id="numero_cuenta" name="numero_cuenta" type="number" class="form-control" placeholder="{{trans('mcaja_banco.numerodecuenta')}}" autocomplete="off">
          </div>
        </div>
        <!--CLASE CAJA BANCO-->
        <div class="form-group col-md-6">
          <label for="clase" class="col-md-4 texto">{{trans('mcaja_banco.clase')}}:</label>
          <div class="col-md-7">
            <select id="clase" name="clase" class="form-control" required>
              <option>{{trans('mcaja_banco.seleccione')}}...</option>
              <option value="1">{{trans('mcaja_banco.cuentabancaria')}}</option>
              <option value="2">{{trans('mcaja_banco.cuentadecaja')}}</option>
            </select>
          </div>
        </div>
        <!--GRUPO CAJA BANCO-->
        <div class="form-group  col-xs-6">
          <label for="grupo_plan_cuenta" class="col-md-4 texto">{{trans('mcaja_banco.grupo')}}:</label>
          <div class="col-md-7">
            <select id="grupo_plan_cuenta" name="grupo_plan_cuenta" class="form-control" onchange="obtener_detalle_grupo()" required>
              <option value="">{{trans('mcaja_banco.seleccione')}}...</option>
              @foreach($plan_cuenta as $value)
              <option value="{{$value->id}}">{{$value->nombre}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <!--CUENTA MAYOR CAJA Y BANCO -->
        <div class="form-group  col-xs-6">
          <label for="detalle_grupo" class="col-md-4 texto">{{trans('mcaja_banco.mayorcuenta')}}:</label>
          <div class="col-md-7">
            <select id="detalle_grupo" name="detalle_grupo" class="form-control" required>
              <option value="">{{trans('mcaja_banco.seleccione')}}...</option>
            </select>
          </div>
        </div>
        <!--SUCURSAL CAJA Y BANCO-->
        <div class="form-group  col-xs-6">
          <label for="nomb_sucursal" class="col-md-4 texto">{{trans('mcaja_banco.sucursal')}}:</label>
          <div class="col-md-7">
            <select id="nomb_sucursal" name="nomb_sucursal" class="form-control" required>
              <option value="">{{trans('mcaja_banco.seleccione')}}...</option>
              @foreach($sucursales as $value)
              <option value="{{$value->id}}">{{$value->nombre_sucursal}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <!--DIVISA CAJA Y BANCO-->
        <div class="form-group  col-xs-6">
          <label for="divisa" class="col-md-4 texto">{{trans('mcaja_banco.divisa')}}:</label>
          <div class="col-md-7">
            <select id="divisa" name="divisa" class="form-control" required>
              <option value="">{{trans('mcaja_banco.seleccione')}}...</option>
              @foreach($divisas as $value)
              <option value="{{$value->id}}">{{$value->descripcion}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <!--TIPO PAGO CAJA Y BANCO-->
        <div class="form-group  col-xs-6">
          <label for="forma_pago" class="col-md-4 texto">{{trans('mcaja_banco.formadepago')}}:</label>
          <div class="col-md-7">
            <select id="forma_pago" name="forma_pago" class="form-control" required>
              <option value="">{{trans('mcaja_banco.seleccione')}}...</option>
              @foreach($form_pago as $value)
              <option value="{{$value->id}}">{{$value->nombre}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <!--ULTIMO CHEQUE CAJA BANCO-->
        <div class="form-group  col-xs-6">
          <label for="ultimo_cheque" class="col-md-4 texto">{{trans('mcaja_banco.ultimocheque')}}:</label>
          <div class="col-md-7">
            <input id="ultimo_cheque" name="ultimo_cheque" type="text" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" placeholder="{{trans('mcaja_banco.ultimocheque')}}" autocomplete="off">
          </div>
        </div>
        <!--COMENTARIOS CAJA BANCO-->
        <div class="form-group  col-xs-6">
          <label for="comentario" class="col-md-4 texto">{{trans('mcaja_banco.comentarios')}}:</label>
          <div class="col-md-7">
            <textarea id="comentario" name="comentario" rows="3" cols="50" placeholder="{{trans('mcaja_banco.comentario')}}"></textarea>
          </div>
        </div>
        <!--ESTADO CAJA BANCO-->
        <div class="form-group col-md-6">
          <label for="estado_caj_banco" class="col-md-4 texto">{{trans('mcaja_banco.estado')}}</label>
          <div class="col-md-7">
            <select id="estado_caj_banco" name="estado_caj_banco" class="form-control" required>
              <option>{{trans('mcaja_banco.seleccione')}}...</option>
              <option value="1">{{trans('mcaja_banco.activo')}}</option>
              <option value="0">{{trans('mcaja_banco.inactivo')}}</option>
            </select>
          </div>
        </div>
        <div class="form-group col-xs-10 text-center">
          <div class="col-md-6 col-md-offset-4">
            <button type="submit" class="btn btn-default btn-gray btn_add">
              <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('mcaja_banco.agregar')}}
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>
</section>

@endsection