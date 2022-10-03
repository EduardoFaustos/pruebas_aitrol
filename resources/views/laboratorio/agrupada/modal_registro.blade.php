@php
$rolUsuario = Auth::user()->id_tipo_usuario;
@endphp

<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
  <h3 style="margin:0;">{{trans('dtraduccion.IngreseDatosFactura')}}</h3>
</div>
<div class="modal-body">
  <form id="form_agrupada">
    <div class="box-body">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <!--cedula factura-->
      <div class="form-group col-md-3">
        <label for="anio" class="control-label">{{trans('dtraduccion.Año')}}</label>
        <select id="anio" name="anio" class="form-control">
          @php $x=2020; $anio_actual=date('Y'); @endphp
          @for($x=2020;$x<=$anio_actual;$x++) <option @if($x==$anio) selected @endif>{{$x}}</option>
            @endfor
        </select>
      </div>
      <div class="form-group col-md-3">
        <label for="mes" class="control-label">{{trans('dtraduccion.Mes')}}</label>
        <select id="mes" name="mes" class="form-control">
          <option value="1" @if($mes==1) selected @endif>{{trans('dtraduccion.Enero')}}</option>
          <option value="2" @if($mes==2) selected @endif>{{trans('dtraduccion.Febrero')}}</option>
          <option value="3" @if($mes==3) selected @endif>{{trans('dtraduccion.Marzo')}}</option>
          <option value="4" @if($mes==4) selected @endif>{{trans('dtraduccion.Abril')}}</option>
          <option value="5" @if($mes==5) selected @endif>{{trans('dtraduccion.Mayo')}}</option>
          <option value="6" @if($mes==6) selected @endif>{{trans('dtraduccion.Junio')}}</option>
          <option value="7" @if($mes==7) selected @endif>{{trans('dtraduccion.Julio')}}</option>
          <option value="8" @if($mes==8) selected @endif>{{trans('dtraduccion.Agosto')}}</option>
          <option value="9" @if($mes==9) selected @endif>{{trans('dtraduccion.Septiembre')}}</option>
          <option value="10" @if($mes==10) selected @endif>{{trans('dtraduccion.Octubre')}}</option>
          <option value="11" @if($mes==11) selected @endif>{{trans('dtraduccion.Noviembre')}}</option>
          <option value="12" @if($mes==12) selected @endif>{{trans('dtraduccion.Diciembre')}}</option>
        </select>
      </div>
      <div class="form-group col-md-3">
        <label for="cedula_factura" class="control-label">{{trans('dtraduccion.Documento')}}</label>
        <select id="documento" name="documento" class="form-control" onchange="busca_clientes()">
          <option value="5">{{trans('dtraduccion.Cédula')}}</option>
          <option value="4">{{trans('dtraduccion.RUC')}}</option>
          <option value="6">{{trans('dtraduccion.Pasaporte')}}</option>
          <option value="8">{{trans('dtraduccion.IdentificaciónExtranjera')}}</option>
        </select>
      </div>
      <div class="form-group col-md-3">
        <label for="cedula_factura" class="control-label">{{trans('dtraduccion.Cédula')}} / {{trans('dtraduccion.RUC')}}</label>
        <input id="cedula_factura" maxlength="13" type="text" class="form-control" name="cedula_factura" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus autocomplete="off" onchange="busca_clientes(this.value);">
      </div>
      <!--primer nombre-->
      <div class="form-group col-md-6">
        <label for="nombre_factura" class="control-label">{{trans('dtraduccion.Nombre')}}</label>
        <input id="nombre_factura" class="form-control" maxlength="100" type="text" name="nombre_factura" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="">
      </div>
      <!--Direccion-->
      <div class="form-group col-md-3">
        <label for="direccion_factura" class="control-label">{{trans('dtraduccion.Dirección')}}</label>
        <input id="direccion_factura" type="text" class="form-control" name="direccion_factura" maxlength="70" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="">
      </div>
      <!--ciudad-->
      <div class="form-group col-md-3">
        <label for="ciudad_factura" class="control-label">{{trans('dtraduccion.Ciudad')}}</label>
        <input id="ciudad_factura" type="text" class="form-control" name="ciudad_factura" maxlength="70" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="">
      </div>
      <!--email-->
      <div class="form-group col-md-6">
        <label for="email_factura" class="control-label">{{trans('dtraduccion.Email')}}</label>
        <input id="email_factura" type="text" class="form-control" name="email_factura" maxlength="70" required autofocus onchange="">
      </div>
      <!--telefono-->
      <div class="form-group col-md-3">
        <label for="telefono_factura" class="control-label">{{trans('dtraduccion.Teléfono')}}</label>
        <input id="telefono_factura" type="text" class="form-control" maxlength="10" name="telefono_factura" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="">
      </div>
      <div class="form-group col-md-3">
        <button type="button" class="btn btn-info" id="boton_sri" onclick="guardar_info_cab()">Guardar</button>
      </div>
    </div>
  </form>
</div>


<script type="text/javascript">
  /*var cedula_factura = $('#cedula_factura').val();
  cedula_factura = cedula_factura.trim();
  busca_clientes(cedula_factura);*/

  function busca_clientes(cedula) {
    console.log(cedula);
    var documento = $('#documento').val();
    if (documento == 4 || documento == 5) {
      if (!validarCedula(cedula)) {
        alert("Error en la cedula/Ruc");
        $('#boton_sri').attr("disabled", true);
      } else {
        $('#boton_sri').removeAttr("disabled");
      }
    } else {
      $('#boton_sri').removeAttr("disabled");
    }

    $.ajax({
      type: 'get',
      url: "{{ url('laboratorio/externo/web/buscar_clientes') }}/" + cedula,
      datatype: 'json',
      success: function(data) {
        console.log(data);
        if (data != 'no') {
          $('#nombre_factura').val(data.nombre);
          $('#direccion_factura').val(data.direccion);
          $('#ciudad_factura').val(data.ciudad);
          $('#email_factura').val(data.email);
          $('#telefono_factura').val(data.telefono);
        }
        console.log(data);
      },

      error: function(data) {

        if (data.responseJSON.valor != null) {
          $('#dvalor').addClass('has-error');
          alert(data.responseJSON.valor[0]);
        }


      }
    });
  }

  function guardar_info_cab() {

    var cedula_factura = $('#cedula_factura').val();
    var nombre_factura = $('#nombre_factura').val();
    var direccion_factura = $('#direccion_factura').val();
    var ciudad_factura = $('#ciudad_factura').val();
    var email_factura = $('#email_factura').val();
    var telefono_factura = $('#telefono_factura').val();
    var error = '';
    if (cedula_factura == '') {
      error = error + 'Ingrese la cedula';
    }
    if (nombre_factura == '') {
      error = error + 'Ingrese el nombre de la factura - ';
    }
    if (direccion_factura == '') {
      error = error + 'Ingrese la direccion de la factura - ';
    }
    if (ciudad_factura == '') {
      error = error + 'Ingrese la ciudad de la factura - ';
    }
    if (email_factura == '') {
      error = error + 'Ingrese el email de la factura - ';
    }
    if (telefono_factura == '') {
      error = error + 'Ingrese el telefono de la factura - ';
    }
    if (error == '') {
      $.ajax({
        type: 'post',
        url: "{{ route('factura_agrupada.guardar_datos_agrupada_cab') }}",
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        datatype: 'json',
        data: $("#form_agrupada").serialize(),
        success: function(data) {
          $('#modal_registro').modal('hide');
          location.reload();

        },

        error: function(data) {

          if (data.responseJSON.valor != null) {

            alert(data.responseJSON.valor[0]);
          }

        }
      });
    } else {
      alert(error);
    }

  }
</script>