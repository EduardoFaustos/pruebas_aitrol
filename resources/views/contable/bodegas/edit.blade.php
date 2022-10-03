@extends('contable.bodegas.base')
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
        <li class="breadcrumb-item"><a href="{{route('bodegas.index')}}">{{trans('contableM.BODEGAS')}}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.actualizar')}}</li>
      </ol>
    </nav>
    <form class="form-vertical" role="form" method="POST" action="{{route('bodegas.update')}}">
        {{ csrf_field() }}
        <div class="box">
          <div class="box-header color_cab">
            <div class="col-md-9">
              <!--<h3 class="box-title">Detalle Establecimiento</h3>-->
              <h5><b>DETALLE BODEGA</b></h5>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="goBack()" class="btn btn-default btn-gray" >
                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                </button>
            </div>
          </div>  
          <input  name="id_bodeg" id="id_bodeg" type="text" class="hidden" value="@if(!is_null($bodegas)){{$bodegas->id}}@endif">
          <div class="separator"></div>
          <div class="box-body dobra">
            <!--NOMBRE DE LA BODEGA-->
            <div class="form-group  col-xs-6">
              <label for="nombre" class="col-md-4 texto">{{trans('contableM.nombre')}}:</label>
              <div class="col-md-7">
                  <input id="nombre" name="nombre" type="text" class="form-control" placeholder="Nombre" value="@if(!is_null($bodegas)){{$bodegas->nombre}}@endif" autocomplete="off" style="text-transform:uppercase;" required autofocus>
              </div>
            </div>
            <!--HOSPITAL-->
            <div class="form-group col-md-6">
              <label for="hopd" class="col-md-4 texto">{{trans('contableM.Hospital')}}</label>
              <div class="col-md-7">
                <select id="id_hospital" name="id_hospital" class="form-control" required>
                  <option value="">Seleccione...</option>
                  @foreach($hospital as $value)
                    <option value="{{$value->id}}" {{ $bodegas->id_hospital == $value->id ? 'selected' : ''}}>{{$value->nombre_hospital}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <!--DEPARTAMENTO-->
            <div class="form-group col-md-6">
              <label for="departamento" class="col-md-4 texto">{{trans('contableM.Departamento')}}</label>
              <div class="col-md-7">
                <select id="departamento" name="departamento" class="form-control" required>
                  <option {{ $bodegas->departamento == 1 ? 'selected' : ''}} value="1">{{trans('contableM.COMPRA')}}</option>
                  <option {{ $bodegas->departamento == 2 ? 'selected' : ''}} value="2">{{trans('contableM.Contabilidad')}}</option>
                </select>
              </div>
            </div>
            <!--COLOR DE LA ETIQUETA-->
            <div class=" form-group col-md-6">
              <label for="color" class="col-md-4 control-label">{{trans('contableM.ColordelaEtiqueta')}}</label>
              <div class="col-md-7 colorpicker">
                  <input id="color" type="hidden" type="text" class="form-control" name="color"  value="@if(!is_null($bodegas)){{$bodegas->color}}@endif" required >
                  <span class="input-group-addon colorpicker-2x"><i style="width: 50px; height: 50px;"></i></span> 
              </div>
            </div> 
            <!--ESTADO BODEGA-->
            <div class="form-group col-md-6">
                <label for="estado_bodega" class="col-md-4 texto">{{trans('contableM.estado')}}</label>
                <div class="col-md-7">
                  <select id="estado_bodega" name="estado_bodega" class="form-control" required>
                    <option {{ $bodegas->estado == 1 ? 'selected' : ''}} value="1">{{trans('contableM.activo')}}</option>
                    <option {{ $bodegas->estado == 0 ? 'selected' : ''}} value="0">{{trans('contableM.inactivo')}}</option>
                  </select>
                </div>
            </div>
            <div class="box-body col-xs-24">
              <div class="form-group col-xs-10 text-center">
                <div class="col-md-6 col-md-offset-4">
                    <button type="submit" id="btn_add" class="btn btn-default btn-gray btn_add">
                      <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Actualizar
                    </button>
                </div>
              </div>
            </div>
          </div>
        </div>
    </form>
  </section>
@endsection
