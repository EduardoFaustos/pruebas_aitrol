@php
  $rolUsuario = Auth::user()->id_tipo_usuario;
@endphp

<div class="box-header">
  <h3 style="margin:0;">Ingrese Datos de Factura</h3>
</div>
<div class="box-body">
  <form id="frm_datos_agrupada">
    <div class="box-body">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <!--cedula factura-->
     
      <div class="form-group col-md-3">
          <label for="cedula_factura" class="control-label">Documento</label>
          <select id="documento" name="documento" class="form-control" onchange="busca_clientes()">
            <option value="5">Cedula</option>
            <option value="4">Ruc</option>
            <option value="6">Pasaporte</option>
            <option value="8">Identificación Extranjera</option>
          </select>
      </div>
      <div class="form-group col-md-3">
          <label for="cedula_factura" class="control-label">Cédula / RUC</label>
          <input id="cedula_factura" maxlength="13" type="text" class="form-control" name="cedula_factura" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"  required autofocus autocomplete="off" onchange="busca_clientes(this.value);" >
      </div>
      <!--primer nombre-->
      <div class="form-group col-md-6">
          <label for="nombre_factura" class="control-label">Nombre</label>
          <input id="nombre_factura" class="form-control" maxlength="100" type="text" name="nombre_factura"  style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="" >
      </div>
      <!--Direccion-->
        <div class="form-group col-md-3">
          <label for="direccion_factura" class="control-label">Dirección</label>
          <input id="direccion_factura" type="text" class="form-control" name="direccion_factura" maxlength="70" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="" >
        </div>
        <!--ciudad-->
        <div class="form-group col-md-3">
            <label for="ciudad_factura" class="control-label">Ciudad</label>
            <input id="ciudad_factura" type="text" class="form-control" name="ciudad_factura" maxlength="70"  style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="" >
        </div>
        <!--email-->
        <div class="form-group col-md-6">
            <label for="email_factura" class="control-label">E-mail</label>
            <input id="email_factura" type="text" class="form-control" name="email_factura" maxlength="70"  required autofocus onchange="" >
        </div>
        <!--telefono-->
        <div class="form-group col-md-3">
            <label for="telefono_factura" class="control-label">Teléfono</label>
            <input id="telefono_factura" type="text" class="form-control" maxlength="10" name="telefono_factura" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="" >
        </div>
        @if($rolUsuario=='5' || $rolUsuario=='1' || $rolUsuario=='20')
        <div class="form-group col-md-3">
          <img id="imagen_espera" src="{{asset('/images/espera.gif')}}" style="width: 40%; display: none;">
          <button type="button" class="btn btn-info" id="boton_sri" onclick="guardar_agrupada();">Guardar Y Enviar al SRI</button>
        </div>
        @endif
        <!-- @if($rolUsuario=='20' || $rolUsuario=='1')
        <div class="form-group col-md-3">
          <button type="button" class="btn btn-info" id="boton_sri_contabilidad" onclick="guardar_agrupada_contabilidad();">Guardar Y Enviar al SRI (contabilidad)</button>
        </div>
        @endif -->
    </div>
  </form>
</div>


<script type="text/javascript">

  /*var cedula_factura = $('#cedula_factura').val();
  cedula_factura = cedula_factura.trim();
  busca_clientes(cedula_factura);*/

  function busca_clientes(cedula){
    console.log(cedula);
    var documento = $('#documento').val();
      if (documento == 4 || documento == 5) {
        if(!validarCedula(cedula)){
            alert("Error en la cedula/Ruc");
            $('#boton_sri').attr("disabled", true);
            $('#boton_sri_contabilidad').attr("disabled", true);
          }else{
          $('#boton_sri').removeAttr("disabled");
          $('#boton_sri_contabilidad').removeAttr("disabled");
        }
      }else{
        $('#boton_sri').removeAttr("disabled");
        $('#boton_sri_contabilidad').removeAttr("disabled");
      }

    $.ajax({
      type: 'get',
      url:"{{ url('laboratorio/externo/web/buscar_clientes') }}/"+cedula,
      datatype: 'json',
      success: function(data){
        console.log(data);
        if(data!='no'){
          $('#nombre_factura').val(data.nombre);
          $('#direccion_factura').val(data.direccion);
          $('#ciudad_factura').val(data.ciudad);
          $('#email_factura').val(data.email);
          $('#telefono_factura').val(data.telefono);
        }
        console.log(data);
      },


      error: function(data){

        if(data.responseJSON.valor!=null){
            $('#dvalor').addClass('has-error');
            alert(data.responseJSON.valor[0]);
        }


      }
    });
  }

  

</script>
