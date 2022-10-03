@php
$rolUsuario = Auth::user()->id_tipo_usuario;
@endphp

<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
  <h3 style="margin:0;">{{trans('dtraduccion.EditarDatosFactura')}}</h3>
</div>
<div class="modal-body">
  <form id="form_agrupada">
    <div class="box-body">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <!--cedula factura-->
      <div class="form-group col-md-3">
        <label for="anio" class="control-label">{{trans('dtraduccion.Año')}}</label>
        <select id="anio" name="anio" class="form-control">
          @php $x=2020; $anio_actual=date('Y'); echo $anio_actual; @endphp
          @for($x=2020;$x<=$anio_actual;$x++) <option @if($x==$cabecera->anio) selected @endif>{{$x}}</option>
            @endfor
        </select>
      </div>
      @php $meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"]; @endphp

      <div class="form-group col-md-3">
        <label for="mes" class="control-label">{{trans('dtraduccion.Mes')}}</label>
        <select id="mes" name="mes" class="form-control">
          @for($ids =0; $ids<sizeof($meses); $ids++) @if($ids==($cabecera->mes)-1){
            <option value="<?php echo $ids + 1; ?>" selected><?php echo $meses[$ids]; ?></option>
            @else
            <option value="<?php echo $ids + 1; ?>"><?php echo $meses[$ids]; ?></option>
            @endif
            @endfor
        </select>
      </div>
      <div class="form-group col-md-3">
        <label for="cedula_factura" class="control-label">{{trans('dtraduccion.Documento')}}</label>
        <select id="documento" name="documento" class="form-control" onchange="busca_clientes()">
          <option @php if($cabecera->tipo_documento == 5){ echo "selected";} @endphp value="5">{{trans('dtraduccion.Cédula')}}</option>
          <option @php if($cabecera->tipo_documento == 4){ echo "selected";} @endphp value="4">{{trans('dtraduccion.RUC')}}</option>
          <option @php if($cabecera->tipo_documento == 6){ echo "selected";} @endphp value="6">{{trans('dtraduccion.Pasaporte')}}</option>
          <option @php if($cabecera->tipo_documento == 8){ echo "selected";} @endphp value="8">{{trans('dtraduccion.IdentificaciónExtranjera')}}</option>
        </select>
      </div>
      <div class="form-group col-md-3">
        <label for="cedula_factura" class="control-label">{{trans('dtraduccion.Cédula')}} / {{trans('dtraduccion.RUC')}}</label>
        <input value="{{$cabecera->cedula_factura}} " id="cedula_factura" maxlength="13" type="text" class="form-control" name="cedula_factura" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus autocomplete="off" onchange="busca_clientes(this.value);">
      </div>
      <!--primer nombre-->
      <div class="form-group col-md-6">
        <label for="nombre_factura" class="control-label">{{trans('dtraduccion.Nombre')}}</label>
        <input value="{{$cabecera->nombre_factura}}" id="nombre_factura" class="form-control" maxlength="100" type="text" name="nombre_factura" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="">
      </div>
      <!--Direccion-->
      <div class="form-group col-md-3">
        <label for="direccion_factura" class="control-label">{{trans('dtraduccion.Dirección')}}</label>
        <input value="{{$cabecera->direccion_factura}} " id="direccion_factura" type="text" class="form-control" name="direccion_factura" maxlength="70" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="">
      </div>
      <!--ciudad-->
      <div class="form-group col-md-3">
        <label for="ciudad_factura" class="control-label">{{trans('dtraduccion.Ciudad')}}</label>
        <input value="{{$cabecera->ciudad_factura}}" id="ciudad_factura" type="text" class="form-control" name="ciudad_factura" maxlength="70" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="">
      </div>
      <!--email-->
      <div class="form-group col-md-6">
        <label for="email_factura" class="control-label">{{trans('dtraduccion.Email')}}</label>
        <input value="{{$cabecera->email_factura}} " id="email_factura" type="text" class="form-control" name="email_factura" maxlength="70" required autofocus onchange="">
      </div>
      <!--telefono-->
      <div class="form-group col-md-3">
        <label for="telefono_factura" class="control-label">{{trans('dtraduccion.Teléfono')}}</label>
        <input value="{{$cabecera->telefono_factura}} " id="telefono_factura" type="text" class="form-control" maxlength="10" name="telefono_factura" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="">
      </div>
      <input type="hidden" name="id_cambio" value="{{$cabecera->id}} ">
      <div class="form-group col-md-3">
        <button type="button" class="btn btn-info" id="boton_sri" onclick="validar()">{{trans('dtraduccion.EDITAR')}}</button>
      </div>
    </div>
  </form>
</div>


<script type="text/javascript">
  function validar() {
    var cedula_factura = document.getElementById('cedula_factura').value;
    var nombre_factura = document.getElementById('nombre_factura').value;
    var direccion_factura = document.getElementById('direccion_factura').value;
    var ciudad_factura = document.getElementById('ciudad_factura').value;
    var email_factura = document.getElementById('email_factura').value;
    var telefono_factura = document.getElementById('telefono_factura').value;
    var error = "";
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
        url: "{{ route('factura_agrupada.editar_datos_agrupada_cab') }}",
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        datatype: 'json',
        data: $("#form_agrupada").serialize(),
        success: function(data) {
          $('#modal_editar').modal('hide');;
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

    var cedula_factura = document.getElementById('cedula_factura').value;
    var nombre_factura = document.getElementById('nombre_factura').value;
    var direccion_factura = document.getElementById('direccion_factura').value;
    var ciudad_factura = document.getElementById('ciudad_factura').value;
    var email_factura = document.getElementById('email_factura').value;
    var telefono_factura = document.getElementById('telefono_factura').value;
    var error = '';
    //console.log(cedula_factura)
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
        url: "{{ route('factura_agrupada.editar_datos_agrupada_cab') }}",
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        datatype: 'json',
        data: $("#form_agrupada").serialize(),
        success: function(data) {
          $('#modal_editar').modal('hide');;
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