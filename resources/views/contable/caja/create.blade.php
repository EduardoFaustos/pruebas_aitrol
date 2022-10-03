@extends('contable.caja.base')
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
</script>

<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('mcaja.contable')}}</a></li>
      <li class="breadcrumb-item"><a href="../emision">{{trans('mcaja.buscadorpuntoemision')}}</a></li>
      <li class="breadcrumb-item active" aria-current="page">{{trans('mcaja.crear')}}</li>
    </ol>
  </nav>
  <form id="enviar_punto_emision" class="form-vertical" role="form" method="POST" action="{{route('puntoemision.store')}}">
    {{ csrf_field() }}
    <div class="box">
      <div class="box-header color_cab">
        <div class="col-md-9">
          <!--<h3 class="box-title">Crear Punto Emision</h3>-->
          <h5><b>{{trans('mcaja.crearpuntoemision')}}</b></h5>
        </div>
        <div class="col-md-1 text-right">
          <button onclick="goBack()" class="btn btn-default btn-gray">
            <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('mcaja.regresar')}}
          </button>
        </div>
      </div>
      <div class="separator"></div>
      <div class="box-body dobra">
        <!--CODIGO PUNTO EMISION-->
        <div class="form-group  col-xs-6">
          <label for="codigo_punto" class="col-md-4 texto">{{trans('mcaja.codigo')}}:</label>
          <div class="col-md-7">
            <input id="codigo_punto" name="codigo_punto" type="text" class="form-control" placeholder="{{trans('mcaja.codigo')}}" maxlength="3" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" autocomplete="off" required autofocus>
          </div>
        </div>
        <!--NOMBRE PUNTO EMISION-->
        <div class="form-group  col-xs-6">
          <label for="nombre_punto" class="col-md-4 texto">{{trans('mcaja.nombre')}}:</label>
          <div class="col-md-7">
            <input id="nombre_punto" name="nombre_punto" type="text" class="form-control" placeholder="{{trans('mcaja.nombre')}}" autocomplete="off" required autofocus>
          </div>
        </div>
        <!--ESTADO PUNTO DE EMISION-->
        <div class="form-group col-md-6">
          <label for="estado_punto" class="col-md-4 texto">{{trans('mcaja.estado')}}</label>
          <div class="col-md-7">
            <select id="estado_punto" name="estado_punto" class="form-control" required>
              <option>{{trans('mcaja.seleccione')}}...</option>
              <option value="1">{{trans('mcaja.activo')}}</option>
              <option value="0">{{trans('mcaja.inactivo')}}</option>
            </select>
          </div>
        </div>
        <!--NOMBRE SUCURSAL A QUE SE LE VA ASIGNAR EL PUNTO DE EMISION-->
        <div class="form-group  col-xs-6">
          <label for="id_sucursal" class="col-md-4 texto">{{trans('mcaja.establecimiento')}}</label>
          <div class="col-md-7">
            <select id="id_sucursal" name="id_sucursal" class="form-control" required>
              <option value="">{{trans('mcaja.seleccione')}}...</option>
              @foreach($sucursales as $value)
              <option value="{{$value->id}}">{{$value->nombre_sucursal}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group col-xs-10 text-center">
          <div class="col-md-6 col-md-offset-4">
            <button type="submit" class="btn btn-default btn-gray btn_add">
              <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('mcaja.guardar')}}
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>
</section>
@endsection