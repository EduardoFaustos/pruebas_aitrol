@extends('contable.rol_pago.base')
@section('action-content')

<style type="text/css">

    .separator{
      width:100%;
      height:30px;
      clear: both;
    }

    .alerta_guardado{
      position: absolute;
      z-index: 9999;
      bottom: 100px;
      right: 20px;
    }

    .titulo-wrapper{
        width: 100%;
        text-align: center;
    }

    .head-title{
      background-color: #888;
      margin-left: 0px;
      margin-right: 0px;
      height: 30px;
      line-height: 30px;
      color: #cccccc;
      text-align: center;
    }

    .separator1{
      width:100%;
      height:8px;
      clear: both;
    }

    .card-header{
      border-radius: 6px 6px 0 0;
      background-color: #3c8dbc;
      border-color: #b2b2b2;
      padding: 8px;
      font-family: 'Roboto', sans-serif;
    }

    .div_load{
      display: none;
    }


  </style>

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<section class="content">

  <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
        <li class="breadcrumb-item"><a href="#">Nomina</a></li>
        <li class="breadcrumb-item"><a href="{{route('nomina.index')}}">Empleado</a></li>
        <li class="breadcrumb-item active" aria-current="page">Rol de Pago</li>
      </ol>
  </nav>
  <div class="box">
    <div class="box-header color_cab">
      <div class="col-md-8">
        <h5><b>EDITAR ROL DE PAGO</b></h5>
      </div>
      <div class="col-md-2 text-right" id="act_rol">
          <button id="crear_rol_pago" onclick="actualiza_rol();" class="btn btn-primary btn-gray">
            Actualizar Rol
          </button>
      </div>
      <div class="col-md-1 text-right">
          <button onclick="goBack()" class="btn btn-default btn-gray" >
              <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
          </button>
      </div>
    </div>
    <div class="box-body dobra">
      <div class="row head-title">
        <div class="col-md-12">
         <label class="color_texto" for="title">DETALLES ROL PAGO</label>
        </div>
      </div>
      <form id="actualiza_rol_pago" method="post">
        <input  name="id_nomina" id="id_nomina" type="text" class="hidden" value="@if(!is_null($inf_nomina->id)){{$inf_nomina->id}}@endif">
        <input  name="id_user" id="id_user" type="text" class="hidden" value="@if(!is_null($inf_nomina->id_user)){{$inf_nomina->id_user}}@endif">
        <input  name="id_empresa" id="id_empresa" type="text" class="hidden" value="@if(!is_null($inf_nomina->id_empresa)){{$inf_nomina->id_empresa}}@endif">
        <input  name="fech_ing" id="fech_ing" type="text" class="hidden" value="@if(!is_null($inf_nomina->fecha_ingreso)){{$inf_nomina->fecha_ingreso}}@endif">
        <input  name="val_fond_reser" id="val_fond_reser" type="text" class="hidden" value="@if(!is_null($val_fond_reserv->valor)){{$val_fond_reserv->valor}}@endif">
        <input  name="val_aport_personal" id="val_aport_personal" type="text" class="hidden" value="@if(!is_null($val_aport_pers->valor)){{$val_aport_pers->valor}}@endif">
        <input  name="val_salar_unif" id="val_salar_unif" type="text" class="hidden" value="@if(!is_null($val_sal_basico->valor)){{$val_sal_basico->valor}}@endif">
        <input  name="mens_acumu_decim_cuart" id="mens_acumu_decim_cuart" type="text" class="hidden" value="@if(isset($inf_nomina->decimo_cuarto)){{$inf_nomina->decimo_cuarto}}@endif">
        
        <input  name="val_multa" id="val_multa" type="text" class="hidden" value="@if(isset($deta_rol)){{$deta_rol->multa}}@endif">
        <input  name="id_rol" id="id_rol" type="text" class="hidden" value="@if(!is_null($rol_pag)){{$rol_pag->id}}@endif">
        <input type="text" name="prestamo_emp" id="prestamo_emp" class="hidden" value="@if(isset($deta_rol)){{$deta_rol->prestamos_empleado}}@endif">
        <input  name="total_val_quot_quir" id="total_val_quot_quir" type="text" class="hidden" value="">
        <input  name="total_val_quot_hip" id="total_val_quot_hip" type="text" class="hidden" value="">

        <input  name="id_tipo_pago" id="id_tipo_pago" type="text" class="hidden" value="@if(!is_null($form_pago))@if(!is_null($form_pago->id_tipo_pago)){{$form_pago->id_tipo_pago}} @endif @endif">
        <input  name="id_tipo_cuenta" id="id_tipo_cuenta" type="text" class="hidden" value="@if(!is_null($form_pago))@if(!is_null($form_pago->id_tipo_cuenta)){{$form_pago->id_tipo_cuenta}}@endif @endif">
        <input  name="id_banco" id="id_banco" type="text" class="hidden" value="@if(!is_null($form_pago))@if(!is_null($form_pago->banco)){{$form_pago->banco}}@endif @endif">
        <input  name="nu_cuenta" id="nu_cuenta" type="text" class="hidden" value="@if(!is_null($form_pago))@if(!is_null($form_pago->numero_cuenta)){{$form_pago->numero_cuenta}}@endif @endif">
        <input  name="nu_cheque" id="nu_cheque" type="text" class="hidden" value="@if(!is_null($form_pago))@if(!is_null($form_pago->num_cheque)){{$form_pago->num_cheque}}@endif @endif">
      
        <!--Anio-->
        <div class="form-group  col-xs-4">
          <label for="year" class="col-md-2 texto">{{trans('contableM.Anio')}}</label>
            <div class="col-md-12">
              <select id="year" name="year" class="form-control"  required>
                <option value="">Seleccione...</option>
                  <?php
for ($i = 2019; $i <= 2030; $i++) {
    //echo "<option value='".$i."'>".$i."</option>";
    if ($rol_pag->anio == $i) {
        echo "<option value='" . $i . "'selected>" . $i . "</option>";
    }

}
?>
              </select>
            </div>
        </div>
        <!--Mes-->
        <div class="form-group  col-xs-4">
          <label for="mes" class="col-md-2 texto">{{trans('contableM.mes')}}</label>
          <div class="col-md-12">
            <select id="mes" name="mes" class="form-control" onchange="buscar_anticipo();buscar_otro_anticipo();buscar_prestamos()" required>
              <option value="">Seleccione...</option>
                <?php
                  $Meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio','Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

                  for ($i = 1; $i <= 12; $i++) {
                      if ($rol_pag->mes == $i) {
                          echo '<option value="' . $i . '"selected>' . $Meses[($i) - 1] . '</option>';
                      }

                  }
                  ?>
            </select>
          </div>
        </div>
        <!--Tipo Rol -->
        <div class="form-group  col-xs-4">
          <label for="tipo_rol" class="col-md-2 texto">{{trans('contableM.tipo')}}</label>
          <div class="col-md-12">
            <select id="tipo_rol" name="tipo_rol" class="form-control" required>
              @foreach($ct_tipo_rol as $value)
                <option value="{{$value->id}}">{{$value->descripcion}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <!--Sueldo Mensual-->
        <div class="form-group  col-xs-4">
          <label for="sueldo_mensual" class=" col-md-2 texto">Sueldo:</label>
          <div class="col-md-10">
            <input  id="sueldo_mensual" name="sueldo_mensual" type="text" class="form-control" value="@if(!is_null($inf_nomina)){{$inf_nomina->sueldo_neto}}@endif"  readonly>
          </div>
        </div>
        <!--Dias Laborados-->
        <div class="form-group  col-xs-4">
                  <label for="dias_laborados" class=" col-md-2 texto">Dias Lab:</label>
                  <div class="col-md-10">
                    <input onblur="caculos_todos()"   id="dias_laborados" name="dias_laborados" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->dias_laborados}}@endif" onkeypress="return isNumberKey(event)" autocomplete="off">
                  </div>
        </div>
        <!--Sueldo Recibir-->
        <div class="form-group  col-xs-4">
          <label for="sueldo_recibir" class=" col-md-2 texto">Sueldo Recib:</label>
          <div class="col-md-10">
            <input  id="sueldo_recibir" name="sueldo_recibir" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->sueldo_mensual}}@endif">
          </div>
        </div>
        <div class="separator1"></div>
                <div class="row head-title">
                  <div class="col-md-12">
                  <label class="color_texto" for="title">INGRESOS</label>
                  <div class="col-md-12" style="height: 15px;">&nbsp;</div>
                  <div class= "col-md-12">
                  </div>
                  </div>
                </div>
        <!--Cantidad Horas al 50%-->
        <div class="form-group  col-xs-6">
          <label for="cant_horas_50" class="col-md-5 texto">Cantidad Horas 50%:</label>
          <div class="col-md-6">
            <input  id="cant_horas_50" name="cant_horas_50" type="text" class="form-control" value="@if(!is_null($deta_rol)and $deta_rol->cantidad_horas50 != null){{$deta_rol->cantidad_horas50}}@else 0.00 @endif"  onkeypress="return isNumberKey(event)"  autocomplete="off" onblur="calculo_horas_50();" onchange="calculo_porcentaje_iess(); calculo_fondo_reserva();">
          </div>
        </div>
        <!--Sobre Tiempo 50%-->
        <div class="form-group  col-xs-6">
          <label for="sobre_tiempo_50" class="col-md-5 texto">Horas al 50%:</label>
          <div class="col-md-7">
            <input  id="sobre_tiempo_50" name="sobre_tiempo_50" type="text" class="form-control" value="@if(!is_null($deta_rol) and $deta_rol->sobre_tiempo_50 != null){{$deta_rol->sobre_tiempo50}}@else 0.00 @endif"  onkeypress="return isNumberKey(event)" onblur="checkformat(this);" onchange="calculo_porcentaje_iess(); calculo_fondo_reserva();">
          </div>
        </div>
        <!--Cantidad Horas al 100%-->
        <div class="form-group  col-xs-6">
          <label for="cant_horas_100" class="col-md-5 texto">Cantidad Horas 100%:</label>
          <div class="col-md-6">
            <input  id="cant_horas_100" name="cant_horas_100" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->cantidad_horas100}}@else 0.00 @endif"  onkeypress="return isNumberKey(event)" autocomplete="off" onblur="calculo_horas_100();" onchange="calculo_porcentaje_iess(); calculo_fondo_reserva();">
          </div>
        </div>
        <!--Sobre Tiempo 100%-->
        <div class="form-group  col-xs-6">
          <label for="sobre_tiempo_100" class="col-md-5 texto">Horas al 100%:</label>
          <div class="col-md-7">
            <input  id="sobre_tiempo_100" name="sobre_tiempo_100" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->sobre_tiempo100}}@else 0.00 @endif"  onkeypress="return isNumberKey(event)" onblur="checkformat(this);" onchange="calculo_porcentaje_iess(); calculo_fondo_reserva();">
          </div>
        </div>
         <!--Bonificacion-->
         @if(!is_null($deta_rol) and $deta_rol->bonificacion > 0)
          <div class="form-group  col-xs-6">
            <label for="valor_bono" class="col-md-5 texto">Bono:</label>
            <div class="col-md-6">
              <input  id="valor_bono" name="valor_bono" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->bonificacion}}@endif" onkeypress="return isNumberKey(event)" onblur="checkformat(this)" onchange="calculo_porcentaje_iess(); calculo_fondo_reserva();">
            </div>
          </div>
          <!--Observacion_Bono-->
          <div class="form-group col-xs-6">
            <label for="observacion_bono" class="col-md-2 texto">Observación:</label>
            <div class="col-md-10">
            <input  id="observacion_bono" name="observacion_bono" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->observacion_bono}}@endif">
            </div>
          </div>
          @else
            <div class="form-group  col-xs-6">
              <label for="valor_bono" class="col-md-5 texto">Bono:</label>
              <div class="col-md-6">
                <input  id="valor_bono" name="valor_bono" type="text" class="form-control" value="0.00" onkeypress="return isNumberKey(event)" onblur="checkformat(this)" onchange="calculo_porcentaje_iess(); calculo_fondo_reserva();">
              </div>
            </div>
            <!--Observacion_Bono-->
            <div class="form-group col-xs-6">
              <label for="observacion_bono" class="col-md-2 texto">Observación:</label>
              <div class="col-md-10">
                <input  id="observacion_bono" name="observacion_bono" type="text" class="form-control" value="">
              </div>
            </div>
          @endif
          <!--Bono Imoputable-->
          @if(!is_null($deta_rol) and $deta_rol->bono_imputable > 0)
            <div class="form-group  col-xs-6">
              <label for="bono_imputable" class="col-md-5 texto">Bono Imputable:</label>
              <div class="col-md-7">
                <input id="bono_imputable" name="bono_imputable" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->bono_imputable}}@endif" onkeypress="return isNumberKey(event)" onblur="checkformat(this)" onchange="calculo_porcentaje_iess(); calculo_fondo_reserva();">
              </div>
            </div>
            <!--Observacion_Bono imputable-->
            <div class="form-group col-xs-6">
              <label for="observacion_bonoimp" class="col-md-2 texto">Observación:</label>
              <div class="col-md-10">
                <input id="observacion_bonoimp" name="observacion_bonoimp" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->observacion_bonoimp}}@endif" required>
              </div>
            </div>
          @else
          <!--Bono Imputable-->
          <div class="form-group  col-xs-6">
            <label for="bono_imputable" class="col-md-5 texto">Bono Imputable:</label>
            <div class="col-md-6">
              <input id="bono_imputable" name="bono_imputable" type="text" class="form-control" value="0.00" onkeypress="return isNumberKey(event)" onblur="checkformat(this)" onchange="calculo_porcentaje_iess(); calculo_fondo_reserva();">
            </div>
          </div>
          <!--Observacion_Bono imputable-->
          <div class="form-group col-xs-6">
            <label for="observacion_bonoimp" class="col-md-2 texto">Observación:</label>
            <div class="col-md-10">
              <input id="observacion_bonoimp" name="observacion_bonoimp" type="text" class="form-control" value="" requiered>
            </div>
          </div>
          @endif

          <!--Alimentacion-->
          @if(!is_null($deta_rol) and $deta_rol->alimentacion > 0)
            <div class="form-group  col-xs-6">
              <label for="alimentacion" class="col-md-5 texto">Alimentacion:</label>
              <div class="col-md-6">
                <input id="alimentacion" name="alimentacion" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->alimentacion}}@endif"  onkeypress="return isNumberKey(event)" onblur="checkformat(this)" onchange="calculo_porcentaje_iess(); calculo_fondo_reserva();">
              </div>
            </div>
            <!--Observacion_Alimentacion-->
            <div class="form-group col-xs-6">
              <label for="observacion_alimentacion" class="col-md-2 texto">Observación:</label>
              <div class="col-md-10">
                <input id="observacion_alimentacion" name="observacion_alimentacion" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->observacion_alimentacion}}@endif">
              </div>
            </div>
          @else
            <div class="form-group  col-xs-6">
              <label for="alimentacion" class="col-md-5 texto">Alimentacion:</label>
              <div class="col-md-6">
                <input id="alimentacion" name="alimentacion" type="text" class="form-control" value="0.00" onkeypress="return isNumberKey(event)" onblur="checkformat(this)" onchange="calculo_porcentaje_iess(); calculo_fondo_reserva();">
              </div>
            </div>
            <!--Observacion_Alimentacion-->
            <div class="form-group col-xs-6">
              <label for="observacion_alimentacion" class="col-md-2 texto">Observación:</label>
              <div class="col-md-10">
                <input id="observacion_alimentacion" name="observacion_alimentacion" type="text" class="form-control" value="">
              </div>
            </div>
          @endif
          <!--Transporte-->
          @if(!is_null($deta_rol) and $deta_rol->transporte > 0)
            <div class="form-group  col-xs-6">
              <label for="transporte" class="col-md-5 texto">Transporte:</label>
              <div class="col-md-7">
                <input id="transporte" name="transporte" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->transporte}}@endif"  onkeypress="return isNumberKey(event)" onblur="checkformat(this)" onchange="calculo_porcentaje_iess(); calculo_fondo_reserva();">
              </div>
            </div>
            <!--Observacion_Transporte-->
            <div class="form-group col-xs-6">
              <label for="observacion_transporte" class="col-md-2 texto">Observación:</label>
              <div class="col-md-10">
                <input id="observacion_transporte" name="observacion_transporte" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->observacion_transporte}}@endif">
              </div>
            </div>
          @else
            <div class="form-group  col-xs-6">
              <label for="transporte" class="col-md-5 texto">Transporte:</label>
              <div class="col-md-7">
                <input id="transporte" name="transporte" type="text" class="form-control" value="0.00" onkeypress="return isNumberKey(event)" onblur="checkformat(this)" onchange="calculo_porcentaje_iess(); calculo_fondo_reserva();">
              </div>
            </div>
            <!--Observacion_Transporte-->
            <div class="form-group col-xs-6">
              <label for="observacion_transporte" class="col-md-2 texto">Observación:</label>
              <div class="col-md-10">
                <input id="observacion_transporte" name="observacion_transporte" type="text" class="form-control" value="">
              </div>
            </div>
          @endif
          <div class="separator1"></div>
          <!--Mensualiza Fondo de Reserva-->
          @if($inf_nomina->pago_fondo_reserva == 2)
          <div id="f_res">
            <div class="form-group  col-xs-7">
              <div class="col-md-4">
               <!-- <label for="fondo_reserva" class="col-md-2 texto">Fondo Reserva Mens</label>-->
                <label><span>Fondo Reserva Mens</span></label>
              </div>
              <div class="col-md-5">
                <input id="fondo_reserva" name="fondo_reserva" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->fondo_reserva}}@endif"  onkeypress="return isNumberKey(event)" onblur="checkformat(this)" onchange="calculo_porcentaje_iess(); calculo_fondo_reserva();">
              </div>
            </div>
          </div>
          @endif
          <!--Acumula Fondo de Reserva-->
          @if($inf_nomina->pago_fondo_reserva == 1)
          <div class="form-group  col-xs-12">
            <label for="fondo_reserva" class="col-md-2 texto">Fondo Reserva Acum:</label>
            <div class="col-md-5">
              <input id="fondo_reserva" name="fondo_reserva" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->fondo_reserva}}@endif"  onkeypress="return isNumberKey(event)" onblur="checkformat(this)" onchange="calculo_porcentaje_iess(); calculo_fondo_reserva();">
            </div>
          </div>
          @endif
          <!--Mensualiza Decimo Tercero-->
          @if($inf_nomina->decimo_tercero == 2)
            <div class="form-group  col-xs-7">
              <div class="col-md-4">
                  <label > <span>Decimo Tercer Mens</span></label>
              </div>
              <div class="col-md-5">
                <input id="decimo_tercero" name="decimo_tercero" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->decimo_tercero}}@endif"  onkeypress="return isNumberKey(event)" onblur="checkformat(this)" onchange="calculo_porcentaje_iess(); calculo_fondo_reserva();">
              </div>
            </div>
          @endif
          <!--Acumula Decimo Tercero  -->
          @if($inf_nomina->decimo_tercero == 1)
          <div class="form-group  col-xs-12">
            <label for="decimo_tercero" class="col-md-2 texto">Decimo Tercer Acum:</label>
            <div class="col-md-6">
              <input id="decimo_tercero" name="decimo_tercero" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->decimo_tercero}}@endif"  onkeypress="return isNumberKey(event)" onblur="checkformat(this)" onchange="calculo_porcentaje_iess(); calculo_fondo_reserva();">
            </div>
          </div>
          @endif
          <!--Mensualiza Decimo Cuarto -->
          @if($inf_nomina->decimo_cuarto == 2)
          <div class="form-group  col-xs-6">
            <label for="decimo_cuarto" class="col-md-5 texto">Decimo Cuart Mens:</label>
            <div class="col-md-6">
              <input id="decimo_cuarto" name="decimo_cuarto" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->decimo_cuarto}}@endif"  onkeypress="return isNumberKey(event)" onblur="checkformat(this)" onchange="calculo_porcentaje_iess(); calculo_fondo_reserva();">
            </div>
          </div>
          @endif
          <!--Acumula Decimo Cuarto-->
          @if($inf_nomina->decimo_cuarto == 1)
          <div class="form-group  col-xs-12">
            <label for="decimo_cuarto" class="col-md-2 texto">Decimo Cuarto Acum:</label>
            <div class="col-md-6">
              <input id="decimo_cuarto" name="decimo_cuarto" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->decimo_cuarto}}@endif"  onkeypress="return isNumberKey(event)" onblur="checkformat(this)" onchange="calculo_porcentaje_iess(); calculo_fondo_reserva();">
            </div>
          </div>
          @endif
          <div class="separator1"></div>
                <div class="row head-title">
                  <div class="col-md-12">
                  <label class="color_texto" for="title">EGRESOS</label>
                  <div class="col-md-12" style="height: 15px;">&nbsp;</div>
                  <div class= "col-md-12">
                  </div>
                  </div>
                </div>
          <!--Calculo de Aporte al IESS-->
          <div class="form-group  col-xs-7">
            <div class="col-md-4">
              <label><span>Aporte IESS:</span></label>
            </div>
            <div class="col-md-5">
              <input onchange="calculo_porcentaje_iess();" id="iess" name="iess" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->porcentaje_iess}}@endif" onkeypress="return isNumberKey(event)" onblur="checkformat(this)">
            </div>
          </div>
          <!--Seguro Privado-->
          @if(!is_null($deta_rol) and $deta_rol->seguro_privado > 0)
          <div class="form-group  col-xs-6">
            <label for="seguro_privado" class="col-md-4 texto">Seguro Privado:</label>
            <div class="col-md-6">
              <input onchange="calculo_porcentaje_iess();"  id="seguro_privado" name="seguro_privado" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->seguro_privado}}@endif" onchange="calculo_porcentaje_iess();" onkeypress="return isNumberKey(event)" onblur="checkformat(this)">
            </div>
          </div>
          <!--observacion_seguro_privado-->
          <div class="form-group col-xs-6">
              <label for="observacion_seg_priv" class="col-md-2 texto">Observación:</label>
              <div class="col-md-10">
                <input onchange="calculo_porcentaje_iess();"  id="observacion_seg_priv" name="observacion_seg_priv" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->observ_seg_privado}}@endif">
              </div>
          </div>
          @else
          <div class="form-group  col-xs-6">
            <label for="seguro_privado" class="col-md-4 texto">Seguro Privado:</label>
            <div class="col-md-6">
              <input onchange="calculo_porcentaje_iess();"  id="seguro_privado" name="seguro_privado" type="text" class="form-control" value="0.00" onchange="calculo_porcentaje_iess();" onkeypress="return isNumberKey(event)" onblur="checkformat(this)" >
            </div>
          </div>
          <!--observacion_seguro_privado-->
          <div class="form-group col-xs-6">
            <label for="observacion_seg_priv" class="col-md-2 texto">Observación:</label>
              <div class="col-md-10">
              <input onchange="calculo_porcentaje_iess();"  id="observacion_seg_priv" name="observacion_seg_priv" type="text" class="form-control" value="">
            </div>
          </div>
          @endif
          <!--Impuesto a la Renta-->
          @if(!is_null($deta_rol) and $deta_rol->impuesto_renta > 0)
          <div class="form-group  col-xs-6">
            <label for="impuesto_renta" class="col-md-5 texto">Impuesto a la Renta:</label>
            <div class="col-md-6">
              <input id="impuesto_renta" name="impuesto_renta" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->impuesto_renta}}@endif" onchange="calculo_porcentaje_iess();" onkeypress="return isNumberKey(event)" onblur="checkformat(this)" >
            </div>
          </div>
          <!--observacion_impuesto_renta-->
          <div class="form-group col-xs-6">
            <label for="observacion_imp_rent" class="col-md-2 texto">Observación:</label>
            <div class="col-md-10">
              <input onchange="calculo_porcentaje_iess();"  id="observacion_imp_rent" name="observacion_imp_rent" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->observ_imp_renta}}@endif">
            </div>
          </div>
          @else
          <div class="form-group  col-xs-6">
            <label for="impuesto_renta" class="col-md-4 texto">Impuesto a la Renta:</label>
            <div class="col-md-6">
              <input id="impuesto_renta" name="impuesto_renta" type="text" class="form-control" value="0.00" onchange="calculo_porcentaje_iess();" onkeypress="return isNumberKey(event)" onblur="checkformat(this)" >
            </div>
          </div>
          <!--observacion_impuesto_renta-->
          <div class="form-group col-xs-6">
            <label for="observacion_imp_rent" class="col-md-2 texto">Observación:</label>
            <div class="col-md-10">
              <input onchange="calculo_porcentaje_iess();"  id="observacion_imp_rent" name="observacion_imp_rent" type="text" class="form-control" value="">
            </div>
          </div>
          @endif
          <!--Check Multa-->
          <div class="form-group col-xs-3">
            <label for="multa" class="col-md-6 texto">Multa</label>
            <div class="col-md-2">
                <input onchange="calculo_porcentaje_iess();"  style="width:17px;height:17px;" type="checkbox" id="multa" class="checkVal_multa" name="multa" value="1"
                @if(old('multa')=="1")
                  checked
                @elseif(!is_null($deta_rol) and $deta_rol->multa > 0)
                  checked
                @endif>
            </div>
          </div>
          <!--Valor Multa-->
          <div class="form-group  col-xs-3" id="dato_multa">
            <label for="valor_multa" class="col-md-4 texto">Valor M:</label>
            <div class="col-md-8">
              <input id="valor_multa" name="valor_multa" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->multa}}@endif" onchange="calculo_porcentaje_iess(); return calculo_multa(this);" onkeypress="return isNumberKey(event)" onblur="checkformat(this)">
            </div>
          </div>
          <!--Observacion Multa-->
          <div class="form-group col-xs-6" id="observacion_multa">
            <label for="observ_multa" class="col-md-2 texto">Observación:</label>
            <div class="col-md-10">
              <input id="observ_multa" name="observ_multa" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->observacion_multa}}@endif">
            </div>
          </div>
          <!--Fondo de Reserva Por Cobrar Trabajadores-->
          <div class="form-group  col-xs-6">
          <label for="fond_res_cobrar_trab" class="col-md-4 texto">Fondo de Res Cobrar:</label>
            <div class="col-md-6">


              <?php ?>

              <input id="fond_res_cobrar_trab" onload="calculo_porcentaje_iess(); return isNumberKey(event); checkformat(this)" name="fond_res_cobrar_trab" type="text" class="form-control" value="<?php if(!is_null($deta_rol) and $deta_rol->fond_reserv_cobrar!=null){echo($deta_rol->fond_reserv_cobrar);}else{echo "0.00";}?>" onchange="calculo_porcentaje_iess();" onkeypress="return isNumberKey(event)" onblur="checkformat(this)">
            </div>
          </div>
          <!--observacion_Fondo de Reserva Por Cobrar Trabajadores-->
          <div class="form-group col-xs-6">
            <label for="obs_fond_cob_trab" class="col-md-2 texto">Observación:</label>
            <div class="col-md-10">
              <input onchange="calculo_porcentaje_iess();"  id="obs_fond_cob_trab" name="obs_fond_cob_trab" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->observacion_fondo_cobrar}}@endif">
            </div>
          </div>
          <!--Otros Egresos-->
          <div class="form-group  col-xs-6">
            <label for="otros_egresos_trab" class="col-md-4 texto">Otros Egresos:</label>
            <div class="col-md-6">
              <input  id="otros_egresos_trab" name="otros_egresos_trab" type="text" class="form-control" value="<?php if(!is_null($deta_rol) and $deta_rol->otros_egresos!=null){echo($deta_rol->otros_egresos);}else{echo "0.00";}?>" onchange="calculo_porcentaje_iess();" onkeypress="return isNumberKey(event)" onblur="checkformat(this)">
            </div>
          </div>
          <!--observacion Otros Egresos-->
          <div class="form-group col-xs-6">
            <label for="obs_otros_egres_trab" class="col-md-2 texto">Observación:</label>
            <div class="col-md-10">
            <input onchange="calculo_porcentaje_iess();" id="obs_otros_egres_trab" name="obs_otros_egres_trab" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->observacion_otro_egreso}}@endif">
            </div>
          </div>
          <!--Examen de laboratorio-->
          <div class="form-group  col-xs-6">
            <label for="exam_laboratorio" class="col-md-4 texto">Examen de laboratorio:</label>
            <div class="col-md-6">
              <input id="exam_laboratorio" name="exam_laboratorio" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->exam_laboratorio}}@endif" onchange="calculo_porcentaje_iess();" onkeypress="return isNumberKey(event)" onblur="checkformat(this)">
            </div>
          </div>
          <!--observacion Otros Egresos-->
          <div class="form-group col-xs-6">
            <label for="observ_examlaboratorio" class="col-md-2 texto">Observación:</label>
            <div class="col-md-10">
            <input onchange="calculo_porcentaje_iess();"  id="observ_examlaboratorio" name="observ_examlaboratorio" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->observ_examlaboratorio}}@endif">
            </div>
          </div>
          <!--Prestamo a Empresa-->
          <div class="form-group  col-xs-6">
            <label for="prestamo_empleado" class="col-md-4 texto">Prestamo a Empresa:</label>
            <div class="col-md-6">
              <input id="prestamo_empleado" name="prestamo_empleado" type="text" class="form-control" value="<?php if(!is_null($deta_rol) and $deta_rol->prestamos_empleado!=null){echo($deta_rol->prestamos_empleado);}else{echo "0.00";}?>" onchange="calculo_porcentaje_iess();"  onkeypress="return isNumberKey(event)" onblur="checkformat(this)">
            </div>
          </div>
          <!--Concepto_Prestamo-->
          <div class="form-group  col-xs-6">
            <label for="concepto_prestamo" class="col-md-2 texto">Observación:</label>
            <div class="col-md-10">
              <input onchange="calculo_porcentaje_iess();" id="concepto_prestamo" name="concepto_prestamo" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->observacion_prestamo}}@endif">
            </div>
          </div>
          <!--Saldo Inicial-->
          <div class="form-group  col-xs-6" id="dato_saldo">
              <label for="saldo_inicial" class="col-md-4 texto">Saldo Prestamo:</label>
              <div class="col-md-6">
                <input id="saldo_inicial" name="saldo_inicial" type="text" class="form-control" value="<?php if(!is_null($deta_rol) and $deta_rol->saldo_inicial_prestamo!=null){echo($deta_rol->saldo_inicial_prestamo);}else{echo "0.00";}?>" onchange="calculo_porcentaje_iess();"  onkeypress="return isNumberKey(event)" onblur="checkformat(this)">
              </div>
          </div>
          <!--Observacion_Saldo_Inicial-->
          <div class="form-group  col-xs-6" id="observacion_saldo">
            <label for="obser_saldo_inicial" class="col-md-2 texto">Observación:</label>
            <div class="col-md-10">
              <input onchange="calculo_porcentaje_iess();"  id="obser_saldo_inicial" name="obser_saldo_inicial" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->observacion_saldo_inicial}}@endif">
            </div>
          </div>
          <!--Anticipo_quincena-->
          <div class="form-group  col-xs-6">
            <label for="anticipo_quincena" class="col-md-4 texto">Anticipo 1era Quinc:</label>
            <div class="col-md-6">
              <input id="anticipo_quincena" name="anticipo_quincena" type="text" class="form-control" value="<?php if(!is_null($deta_rol) and $deta_rol->anticipo_quincena!=null){echo($deta_rol->anticipo_quincena);}else{echo "0.00";}?>" onchange="calculo_porcentaje_iess();" onkeypress="return isNumberKey(event)" onblur="checkformat(this)">
            </div>
          </div>
          <!--Concepto_Anticipo_quincena-->
          <div class="form-group  col-xs-6">
            <label for="concepto_quincena" class="col-md-2 texto">Observación:</label>
            <div class="col-md-10">
              <input onchange="calculo_porcentaje_iess();"  id="concepto_quincena" name="concepto_quincena" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->observacion_anticip_quinc}}@endif" readonly>
            </div>
          </div>
          <!--Otros_Anticipos-->
          <div class="form-group  col-xs-6">
            <label for="otro_anticipo" class="col-md-4 texto">Otros Anticipos:</label>
            <div class="col-md-6">
              <input id="otro_anticipo" name="otro_anticipo" type="text" class="form-control" value="<?php if(!is_null($deta_rol) and $deta_rol->otro_anticipo!=null){echo($deta_rol->otro_anticipo);}else{echo "0.00";}?>" onchange="calculo_porcentaje_iess();" onkeypress="return isNumberKey(event)" onblur="checkformat(this)">
            </div>
          </div>
          <!--Concepto_Otros_Anticipos-->
          <div class="form-group  col-xs-6">
            <label for="concep_otros_anticipos" class="col-md-2 texto">Observación:</label>
            <div class="col-md-10">
              <input onchange="calculo_porcentaje_iess();"  id="concep_otros_anticipos" name="concep_otros_anticipos" type="text" class="form-control" value="@if(!is_null($deta_rol)){{$deta_rol->observacion_otro_anticip}}@endif" readonly>
            </div>
          </div>
          <div class="separator1"></div>
          <div class="card-header">
            <b style="font-size: 12px;color: white">CUOTA PRESTAMO QUIROGRAFARIO</b>
            <div class="col-md-12" style="height: 15px;">&nbsp;</div>
            <div class= "col-md-12">
              <div class="table-responsive col-md-12" style="width:55%">
                @php
                  $cantidad_quiro = \Sis_medico\Ct_Rh_Cuotas_Quirografario::where('id_rol', $rol_pag->id)->where('estado', '1')->count();
                @endphp
                <input name="contador_quiro" id="contador_quiro" type="hidden" value="{{$cantidad_quiro}}">
                <input name="contad_cuot_quir" id="contad_cuot_quir" type="hidden" value="{{$cantidad_quiro + 1}}">
                <table id="example1" role="grid" aria-describedby="example2_info">
                  <thead style="background-color: #FFF3E3">
                    <tr>
                      <th style="width: 10%; text-align: center;">Valor Cuota</th>
                      <th style="width: 10%; text-align: center;">{{trans('contableM.detalle')}}</th>
                      <th style="width: 10%; text-align: center;">{{trans('contableM.accion')}}</th>
                    </tr>
                  </thead>
                  <tbody id="agregar_cuot_quir">
                  </tbody>
                </table>
              </div>
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-2 col-xs-2">
                    <div class="box-footer" >
                      <button type="button" id="btn_quirografario" class="btn btn-primary size_text">
                        Agregar
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="separator1"></div>
          <div class="card-header">
            <b style="font-size: 12px;color: white">CUOTA PRESTAMO HIPOTECARIO</b>
            <div class="col-md-12" style="height: 15px;">&nbsp;</div>
            <div class= "col-md-12">
              <div class="table-responsive col-md-12" style="width:55%">
                @php
                  $cantidad_hipo = \Sis_medico\Ct_Rh_Cuotas_Hipotecarios::where('id_rol', $rol_pag->id)->where('estado', '1')->count();
                @endphp
                <input name="contador_hip" id="contador_hip" type="hidden" value="{{$cantidad_hipo}}">
                <input name="contad_cuot_hip" id="contad_cuot_hip" type="hidden" value="{{$cantidad_hipo + 1}}">
                <table id="example1" role="grid" aria-describedby="example2_info">
                  <thead style="background-color: #FFF3E3">
                    <tr>
                      <th style="width: 10%; text-align: center;">Valor Cuota</th>
                      <th style="width: 10%; text-align: center;">{{trans('contableM.detalle')}}</th>
                      <th style="width: 10%; text-align: center;">{{trans('contableM.accion')}}</th>
                    </tr>
                  </thead>
                  <tbody id="agregar_cuot_hipot">
                  </tbody>
                </table>
              </div>
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-2 col-xs-2">
                    <div class="box-footer" >
                      <button type="button" id="btn_hipotecario" class="btn btn-primary size_text">
                        Agregar
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="separator1"></div>
          <div class="col-md-12" style="height:30px;">
                  <div class="row head-title">
                      <div class="col-md-12 cabecera">
                          <label class="color_texto">TOTAL A PAGAR</label>
                      </div>
                  </div>
          </div>
          <div class="separator1"></div>
          <div class="separator1"></div>
          <div class="col-md-1 text-right">
            <a onclick="calculo_fondo_reserva();calculo_porcentaje_iess();calculo_monto(); " ><span class="btn btn-primary btn-gray">Calculo</span></a>
          </div>
          <!--Monto-->
          <div id="monto_pagar">
            <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="monto">Pagar: </label>
            </div>
            <div class="form-group col-md-4 col-xs-10 container-4">
                <input id="monto" type="text" class="form-control" name="monto" value="@if(!is_null($deta_rol)){{$deta_rol->neto_recibido}}@endif" readonly>
            </div>
          </div>

          <!--<div class="separator1"></div>
          <div class="row head-title">
            <div class="col-md-12 cabecera">
              <label class="color_texto" for="title">FORMA DE PAGO ROL</label>
                <div class="col-md-12" style="height: 15px;">&nbsp;</div>
                <div class= "col-md-12"></div>
            </div>
          </div>-->
          <!--<div class="separator1"></div>
          <div class="form-group col-md-2 col-xs-2">
            <label class="texto" for="tipo_pago">{{trans('contableM.TIPODEPAGO')}}</label>
          </div>
          <div class="form-group col-md-4 col-xs-10 container-4">
            <select class="form-control" id="tipo_pago" name="tipo_pago" onchange="revisar_seleccion()">
              @foreach($tipo_pago_rol as $value)
                <option value="{{$value->id}}">{{$value->tipo}}</option>
              @endforeach
            </select>
          </div>-->
          <!--<div id="id_tip_cuenta">
            <div class="form-group col-md-2 col-xs-2">
              <label class="texto" for="tipo_cuenta">Tipo Cuenta: </label>
            </div>
            <div class="form-group col-md-4 col-xs-10 container-4">
              <select class="form-control" id="tipo_cuenta" name="tipo_cuenta">
                <option value="">Seleccione...</option>
                @foreach($tipo_cuenta as $value)
                  <option value="{{$value->id}}">{{$value->tipo_cuenta}}</option>
                @endforeach
              </select>
            </div>
          </div>-->
          <!--<div id="id_banco">
              <div class="form-group col-md-2 col-xs-2">
                <label class="texto" for="banco">{{trans('contableM.banco')}}: </label>
              </div>
              <div class="form-group col-md-4 col-xs-10 container-4">
                <select class="form-control" id="banco" name="banco">
                  <option value="">Seleccione...</option>
                  @foreach($lista_banco as $value)
                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                  @endforeach
                </select>
              </div>
          </div>-->
          <!--Numero de Cuenta-->
          <!--<div id="num_cuenta">
            <div class="form-group col-md-2 col-xs-2">
              <label class="texto" for="numero_cuenta">N# Cuenta: </label>
            </div>
            <div class="form-group col-md-4 col-xs-10 container-4">
              <input id="numero_cuenta" type="text" class="form-control" name="numero_cuenta" value="" autocomplete="off">
            </div>
          </div>-->
          <!--Numero de Cheque-->
          <!--<div id="num_che">
              <div class="form-group col-md-2 col-xs-2">
                <label class="texto" for="num_cheque">N# cheque: </label>
              </div>
              <div class="form-group col-md-4 col-xs-10 container-4">
                  <input id="num_cheque" type="text" class="form-control" name="num_cheque" value="{{ old('num_cheque') }}" onkeypress="return isNumberKey(event)">
              </div>
          </div>-->

          @php
            $x=0;
          @endphp

          <div class="col-md-12" style="height:30px;">
            <div class="row head-title">
                <div class="col-md-12 cabecera">
                    <label class="color_texto">{{trans('contableM.formadepago')}}</label>
                </div>
            </div>
          </div>
          <div class="col-md-12 table-responsive ">
            <input name="contador_pago" id="contador_pago" type="hidden" value="{{$forma_pago->count()}}">
            <table id="example1" role="grid" class="table table-bordered table-hover dataTable" aria-describedby="example1_info" style="margin-top:0 !important">
              <thead >
                <tr class='well-dark'>
                  <th width="20%" style="text-align: center;">{{trans('contableM.Metodo')}}</th>
                  <th width="20%" style="text-align: center;">{{trans('contableM.banco')}}</th>
                  <th width="20%" style="text-align: center;">N# Cuenta</th>
                  <th width="20%" style="text-align: center;">N# Cheque</th>
                  <th width="15%" style="text-align: center;">{{trans('contableM.valor')}}</th>
                  <th width="5%" style="text-align: center;">
                    <button id="btn_pago" type="button" class="btn btn-success btn-gray">
                      <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                    </button>
                  </th>
                </tr>
              </thead>
                <tbody id="agregar_pago">
                @foreach($forma_pago as $pago)
                <tr class='well' id="dato_pago{{$x}}">
                  <td width="20%" style="text-align: center;">
                    <select class="form-control" name="id_tip_pago{{$x}}"  id="id_tip_pago{{$x}}" style="width: 170px;height:25px">
                        <option value="">Seleccione</option>
                        @foreach($tipo_pago_rol as $val)
                          <option
                             @if($pago->id_tipo_pago==$val->id) selected @endif value="{{$val->id}}">{{$val->tipo}}
                          </option>
                        @endforeach
                    </select>
                    <input required type="hidden" id="visibilidad_pago{{$x}}" name="visibilidad_pago{{$x}}" value="1">
                  </td>
                  <td width="20%" style="text-align: center;">
                      <select class="form-control" name ="id_banco_pago{{$x}}" id ="id_banco_pago{{$x}}" style="width: 170px;height:25px">
                          <option value="">Seleccione</option>
                          @foreach($lista_banco as $val)
                            <option
                                @if($pago->banco == $val->id) selected @endif value="{{$val->id}}">{{$val->nombre}}
                            </option>
                          @endforeach
                      </select>
                  </td>
                  <td>
                    <input required type="text" id="id_cuenta_pago{{$x}}" name="id_cuenta_pago{{$x}}" style="width: 170px;height:28px;" value="{{$pago->numero_cuenta}}">
                  </td>
                  <td>
                    <input required type="text" id="numero_pago{{$x}}" name="numero_pago{{$x}}" style="width: 170px;height:28px" value="{{$pago->num_cheque}}">
                  </td>
                  <td>
                    <input required type="text" id="valor{{$x}}" name="valor{{$x}}" style="width: 190px;height:28px" value="{{$pago->valor}}"  onblur="this.value=parseFloat(this.value).toFixed(2);">
                  </td>
                  <td>
                    <button type="button" onclick="eliminar_form_pag('{{$x}}')" class="btn btn-danger btn-margin btn-lg"><span class="glyphicon glyphicon-trash"></span></button>
                  </td>
                </tr>
                  @php $x ++; @endphp
                @endforeach
                </tbody>
            </table>
          </div>
      </form>
    </div>
  </div>

  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="{{ asset ('/js/jquery-ui.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>

  <script type="text/javascript">

    window.onload = setTimeout(()=> {
      // calculo_porcentaje_iess();
       //calculo_monto();
       
       //buscar_prestamos();
 
    } ,2000);

    $(document).ready(function(){
      
      var prest = $('#prestamo_emp').val();
      if (isNaN(prest)) {
        //alert("entra");
        buscar_prestamos();
      }
      buscar_anticipo();
      buscar_otro_anticipo();
      buscar_prestamos();
     // buscar_saldo();
      calculo_multa();
      calculo_horas_50();
      calculo_horas_100();
      //calculo_porcentaje_iess();



      //var id_tipo_pag = $('#id_tipo_pago').val();

      var mens_acum_dec_cuart = $('#mens_acumu_decim_cuart').val();

      var valor_multa = parseInt($('#val_multa').val());

      if(valor_multa > 0){

         document.getElementById("dato_multa").style.visibility = "visible";
         document.getElementById("observacion_multa").style.visibility = "visible";

      }else{

        document.getElementById("dato_multa").style.visibility = "hidden";
        document.getElementById("observacion_multa").style.visibility = "hidden";

        $('#valor_multa').val("0.00");
        $('#observacion_multa').val("");

      }

      if(mens_acum_dec_cuart == 2){
        //alert("entra3");
        calculo_decimo_cuarto();
      }


      //Cargar Tabla Prestamos Quirografario
      cargar_tabla_quirografario();


      //Cargar Tabla Prestamos Hipotecarios
      cargar_tabla_hipotecario();
      //Muestra Formas de Pago
      /*if(id_tipo_pag == 1){

        var id_tip_pag = $("#id_tipo_pago").val();
        var id_tip_cuenta = $("#id_tipo_cuenta").val();
        var id_ban = $("#id_banco").val();
        var num_cuent = $("#nu_cuenta").val();

        document.ready = document.getElementById("tipo_pago").value = id_tip_pag;
        document.ready = document.getElementById("tipo_cuenta").value = id_tip_cuenta;
        document.ready = document.getElementById("banco").value = id_ban;
        document.ready = document.getElementById("numero_cuenta").value = num_cuent;

        document.getElementById("id_tip_cuenta").style.visibility = "visible";
        document.getElementById("id_banco").style.visibility = "visible";
        document.getElementById("num_cuenta").style.visibility = "visible";
        document.getElementById("num_che").style.visibility = "hidden";
        $('#num_cheque').val("");

      }else if(id_tipo_pag == 2){

        document.getElementById("num_cuenta").style.visibility = "hidden";
        document.getElementById("id_tip_cuenta").style.visibility = "hidden";
        document.getElementById("id_banco").style.visibility = "hidden";
        document.getElementById("num_che").style.visibility = "hidden";


        $('#num_cheque').val("");
        $('#tipo_cuenta').val("");
        $('#banco').val("");
        $('#numero_cuenta').val("");

      }else if(id_tipo_pag == 3){

        document.getElementById("num_che").style.visibility = "visible";
        document.getElementById("id_tip_cuenta").style.visibility = "hidden";
        document.getElementById("id_banco").style.visibility = "hidden";
        document.getElementById("num_cuenta").style.visibility = "hidden";
        $('#num_cheque').val("");
        $('#tipo_cuenta').val("");
        $('#banco').val("");
        $('#numero_cuenta').val("");
        $('#numero_divisa').val("");
        $('divisas').val();


      }*/

     setTimeout(function saludo(){
       //  calculo_monto();
      },2000);

    });

    //Agrega Nuevas Forma de Pago
    $('#btn_pago').click(function(event) {

            id = document.getElementById('contador_pago').value;


            var midiv_pago = document.createElement("tr")
            midiv_pago.setAttribute("id", "dato_pago" + id);


            midiv_pago.innerHTML = '<td><select class="form-control" name="id_tip_pago' + id + '" id="id_tip_pago' + id + '" style="width: 170px;height:20px" onchange="revisar_componentes(this,' + id + ');"><option value="">Seleccione</option>@foreach($tipo_pago_rol as $value)<option value="{{$value->id}}">{{$value->tipo}}</option>@endforeach</select><input type="hidden" id="visibilidad_pago' + id + '" name="visibilidad_pago' + id + '" value="1"></td><td><select class="form-control" name="id_banco_pago' + id + '" id="id_banco_pago' + id + '" style="width: 170px;height:20px"><option value="">Seleccione...</option>@foreach($lista_banco as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select></td><td><input  style="width: 80%;height:20px;" autocomplete="off" class="form-control" name="id_cuenta_pago' + id + '" id="id_cuenta_pago' + id + '" ></td><td><input  type="text" name="numero_pago' + id + '" id="numero_pago' + id + '" style="width: 170px;height:26px" ></td><td><input class="form-control text-right input-number fpago" type="text" id="valor' + id + '" name="valor' + id + '" style="width: 170px;height:20px" onblur="this.value=parseFloat(this.value).toFixed(2);"  value="0.00" onkeypress="return soloNumeros(this);"></td><td><button style="text-align:center;" type="button" onclick="eliminar_form_pag(' + id + ')" class="btn btn-danger btn-margin btn-lg"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';

            document.getElementById('agregar_pago').appendChild(midiv_pago);
            id = parseInt(id);
            id = id + 1;
            document.getElementById('contador_pago').value = id;

    });


    //Elimina Registro de la Tabla Forma de Pago
    function eliminar_form_pag(valor) {
      //alert(valor);
      var dato_pago1 = "dato_pago" + valor;
      var nombre_pago2 = 'visibilidad_pago' + valor;
      document.getElementById(dato_pago1).style.display = 'none';
      document.getElementById(nombre_pago2).value = 0;
    }

    //Muestra segun el Tipo de Pago Seleccionado
    function revisar_componentes(e, id) {
        metodo = $('#id_tip_pago' + id).val();
        if (metodo == 1) {
            $("#numero_pago" + id).prop('disabled', true);
            $("#id_banco_pago" + id).prop('disabled', false);
            $("#id_cuenta_pago" + id).prop('disabled', false);
            $("#valor" + id).prop('disabled', false);
            //revision_total(id);
        } else if (metodo == 2) {
            $("#numero_pago" + id).prop('disabled', true);
            $("#id_banco_pago" + id).prop('disabled', true);
            $("#id_cuenta_pago" + id).prop('disabled', true);
            $("#valor" + id).prop('disabled', false);
            //revision_total(id);
        } else if (metodo == 3) {
            $("#numero_pago" + id).prop('disabled', false);
            $("#id_banco_pago" + id).prop('disabled', false);
            $("#id_cuenta_pago" + id).prop('disabled', false);
            //revision_total(id);
        }
    }


    //Valida que solo pueda  Ingresar Numero
    function soloNumeros(e)
    {

        var teclaPulsada=window.event ? window.event.keyCode:e.which;


        var valor=e.value;


        console.log("indexof",valor);
        if(teclaPulsada==45 && valor.indexOf("-")==-1)
        {
            document.getElementById("inputNumero").value="-"+valor;
        }


        if(teclaPulsada==13 || (teclaPulsada==46 && valor.indexOf(".")==-1))
        {
            return true;
        }


        return /\d/.test(String.fromCharCode(teclaPulsada));
    }


    function goBack() {
      window.history.back();
    }

    function isNumberKey(evt)
    {
     var charCode = (evt.which) ? evt.which : event.keyCode
     if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
        return false;

     return true;
    }

    function calculo_sueldo_dias(){

      sueldo_mensual = parseFloat($('#sueldo_mensual').val());

      sueldo_dia = (sueldo_mensual)/30;

      dias_laborados = $('#dias_laborados').val();

      sueld_mens_recib = (sueldo_dia)*(dias_laborados);

      if(!isNaN(sueld_mens_recib))
      {
        $('#sueldo_recibir').val(sueld_mens_recib.toFixed(2));
      }

    }

    function calculo_horas_50(){

      sueldo_recib  =  0;
      cal_hor_50 = 0;
      cant_hor_50 = 0;

      sueldo_recib = parseFloat($('#sueldo_recibir').val());

      cant_hor_50 = parseFloat($('#cant_horas_50').val());
      
      //Calculo de Horas Extras al 50%
     
 
         cal_hor_50 = (((sueldo_recib/30)/8) *(1.50))*(cant_hor_50);
        $('#sobre_tiempo_50').val(cal_hor_50.toFixed(2));
      

    }

    function buscar_saldo(){

      $.ajax({
            type: 'post',
            url:"{{route('existe_saldo_inicial.prestamo')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            type: 'POST',
            datatype: 'json',
            data: {'identificacion': $("#id_user").val(),
             'id_empresa': $("#id_empresa").val(),'anio': $("#year").val(),'mes': $("#mes").val()},
            success: function(data){

              if(data.existe_mes == '1'){

                document.getElementById("dato_saldo").style.display='block';
                document.getElementById("observacion_saldo").style.display='block';

                $("#saldo_inicial").val(data.val_cuot);
                $("#obser_saldo_inicial").val(data.obser_prest);

              }else{

                document.getElementById("dato_saldo").style.display='none';
                document.getElementById("observacion_saldo").style.display='none';

                $("#saldo_inicial").val("0.00");
                $("#obser_saldo_inicial").val("");
              }

            },
            error: function(data){
                console.log(data);
            }
      });

    }

    function calculo_horas_100(){

        sueldo_recib =  0;
        cal_hor_100 = 0;
        cal_hor_100 = 0;


        sueldo_recib = parseFloat($('#sueldo_recibir').val());

        cant_hor_100 = parseFloat($('#cant_horas_100').val());


        //Calculo de Horas Extras al 100%
        
        if(!isNaN(cal_hor_100))
        {
          cal_hor_100 = (((sueldo_recib/30)/8) *(2))*(cant_hor_100);
          $('#sobre_tiempo_100').val(cal_hor_100.toFixed(2));
        }else{
          cal_hor_100 = 0;
          $('#sobre_tiempo_100').val(cal_hor_100.toFixed(2));
        }

    }

    function checkformat(entry) {

        var test = entry.value;

        if (!isNaN(test)) {
            entry.value=parseFloat(entry.value).toFixed(2);
        }

        if (isNaN(entry.value) == true){
            entry.value='0.00';
        }
        if (test < 0) {

            entry.value = '0.00';
        }

    }

    //Calculo de Decimo Cuarto  Mensualizado
    function calculo_fondo_reserva(){

      sueldo_mensual = 0;
      Sobre_Tiempo_50  =  0;
      Sobre_Tiempo_100  =  0;
      valor_bono  =  0;
      valor_bonoimp= 0;
      val_fon_reserv  =  0;

      sum_total = 0;
      calculo_fond_reserv  = 0;

      var fech_ing= new Date();
      fech_ing.setHours(0,0,0);
      var fechafin = new Date();
      fechafin.setHours(23,59,0);

      if(ValidarFondoReserva()==true)
      {

        var f = $('#fech_ing').val();
        f=f.split('-');
        fech_ing.setFullYear(f[0],f[1]-1,f[2]);

        sueldo_recib = parseFloat($('#sueldo_recibir').val());
        Sobre_Tiempo_50 = parseFloat($('#sobre_tiempo_50').val());
        Sobre_Tiempo_100 = parseFloat($('#sobre_tiempo_100').val());
        valor_bono = parseFloat($('#valor_bono').val());
        val_fon_reserv = parseFloat($('#val_fond_reser').val());
        valor_bonoimp= parseFloat($('#bono_imputable').val());

        //Calculos
        var diasperiodo=DateDiff.inDays(fech_ing,fechafin);

        sum_total = (sueldo_recib)+(Sobre_Tiempo_50)+(Sobre_Tiempo_100)+(valor_bonoimp);

        calculo_fond_reserv = ((sum_total)*(val_fon_reserv))/100;

        if((!isNaN(calculo_fond_reserv))&&(diasperiodo>365))
        {
          $('#fondo_reserva').val(calculo_fond_reserv.toFixed(2));
        }

      }

    }

    function ValidarFondoReserva()
    {

      var fech_ing= new Date();
      //Tue Apr 14 2020 13:57:41 GMT-0500 (hora de Ecuador)

      fech_ing.setHours(0,0,0);
      var fechafin = new Date();


      //Validar Salario
      var salario = $("#sueldo_recibir").val();

      if (IsNumeric(salario)== false)
      {
        swal("Error!","El campo Sueldo Recibido debe ser igual o mayor a 292 ");
      }else if(salario<292)
      {
        swal("Error!","El campo Sueldo Recibido debe ser igual o mayor a 292 ");
      }

      //Validar fecha Inicio
      if($("#fech_ing").val() !=null && $("#fech_ing").val()!='' )
      {
        var f = $('#fech_ing').val();

        //El método split() en Javascript permite dividir en varios bloques una cadena de texto, formando un array.
        //Validar(f);
        if(Validar(f)==true)
        {
          f=f.split('-');
          fech_ing.setFullYear(f[0],f[1]-1,f[2]);
        }else
        {

          swal("Error!","Fecha inicio incorrecta");
        }
      }else
      {
          swal("Error!","No ha asignado un valor en Fecha inicio");
      }

      var diasperiodo = DateDiff.inDays(fech_ing,fechafin);

      /*if(diasperiodo<365)
      {
        swal("Error!","De acuerdo a su Fecha ingreso a un no ha cumplido 1 año de trabajo con su empleador actual");
      }*/
      if(diasperiodo>365)
      {
        document.getElementById("f_res").style.display = "block";
      }else if(diasperiodo < 365){
        document.getElementById("f_res").style.display = "hidden";
        $('#fondo_reserva').val("0.00");

      }

      return true;

    }

    function IsNumeric(input)
    {
      return (input - 0) == input && input.length > 0;
    }

    var DateDiff = {

      inDays: function(d1, d2) {
          var t2 = d2.getTime();
          var t1 = d1.getTime();

          return parseInt((t2-t1)/(24*3600*1000));
      },

      inWeeks: function(d1, d2) {
          var t2 = d2.getTime();
          var t1 = d1.getTime();

          return parseInt((t2-t1)/(24*3600*1000*7));
      },

      inMonths: function(d1, d2) {
          var d1Y = d1.getFullYear();
          var d2Y = d2.getFullYear();
          var d1M = d1.getMonth();
          var d2M = d2.getMonth();

          return (d2M+12*d2Y)-(d1M+12*d1Y);
      },

      inYears: function(d1, d2) {
          return d2.getFullYear()-d1.getFullYear();
      }

    }

    function Validar(Cadena){

        //alert(Cadena);

        var Fecha = new String(Cadena)
        //2016-04-07
        //alert(Fecha);

        var RealFecha = new Date()   // Para sacar la fecha de hoy
        //Cadena dia
        var Dia = new String(Fecha.substring(Fecha.lastIndexOf("-")+1,Fecha.length));

        //Cadena Mes
        var Mes= new String(Fecha.substring(Fecha.indexOf("-")+1,Fecha.lastIndexOf("-")));

        //Cadena Ano
        var Ano= new String(Fecha.substring(0,Fecha.indexOf("-")));


        //Valido el año
        if (isNaN(Ano) || Ano.length<4 || parseFloat(Ano)<1900){

            return false
        }
        //Valido el Mes
        if (isNaN(Mes) || parseFloat(Mes)<1 || parseFloat(Mes)>12){

            return false
        }
        // Valido el Dia
        if (isNaN(Dia) || parseInt(Dia, 10)<1 || parseInt(Dia, 10)>31){

            return false
        }
        if (Mes==4 || Mes==6 || Mes==9 || Mes==11 || Mes==2) {
            if (Mes==2 && Dia > 28 || Dia>30) {

                return false
            }
        }
        return true;
    }

    function ConvertToFloatStr(input)
    {
      return String(input).replace(".",",")
    }

    function DateFormat(d)
    {
      var curr_date = d.getDate();
      var curr_month = d.getMonth();
      curr_month++;
      var curr_year = d.getFullYear();
      return curr_month + "/" + curr_date + "/" + curr_year;
    }

    function Trim(myString)
    {
      return myString.replace(/^\s+/g,'').replace(/\s+$/g,'')
    }

    //Calculo de Decimo Tercero Menzualizado
    function calculo_decimo_tercero(){

        sueldo_mensual  =  0;
        Sobre_Tiempo_50  =  0;
        Sobre_Tiempo_100  =  0;
        valor_bono  =  0;

        sum_total = 0;
        cal_dec_terc  = 0;

        sueldo_mensual = parseFloat($('#sueldo_recibir').val());
        Sobre_Tiempo_50 = parseFloat($('#sobre_tiempo_50').val());
        Sobre_Tiempo_100 = parseFloat($('#sobre_tiempo_100').val());
        valor_bono = parseFloat($('#valor_bono').val());

        sum_total = (sueldo_mensual)+(Sobre_Tiempo_50)+(Sobre_Tiempo_100)+(valor_bono);

        cal_dec_terc = (sum_total)/12;

        if(!isNaN(cal_dec_terc))
        {
          $('#decimo_tercero').val(cal_dec_terc.toFixed(2));
        }

    }


    //Calculo de Decimo Cuarto  Mensualizado
    function calculo_decimo_cuarto(){

      val_sal_bas  =  0;
      cal_dec_cuarto  =  0;

      val_sal_bas = parseFloat($('#val_salar_unif').val());

      cal_dec_cuarto = (val_sal_bas)/12;

      if(!isNaN(cal_dec_cuarto))
      {
        $('#decimo_cuarto').val(cal_dec_cuarto.toFixed(2));
      }

    }
    window.onload = ()=>{
       document.getElementById('fond_res_cobrar_trab').click();
    };

    //Calculo Porcentaje IESS
    function calculo_porcentaje_iess(){
      //alert("ok");

      sueldo_mensual  =  0;
      Sobre_Tiempo_50  =  0;
      Sobre_Tiempo_100  =  0;
      val_aport_per  =  0;
      valor_bonoimp  =  0;

      sum_total = 0;

      calculo_aporte_iess  = 0;

      sueldo_mensual = parseFloat($('#sueldo_recibir').val());
      Sobre_Tiempo_50 = parseFloat($('#sobre_tiempo_50').val());
      Sobre_Tiempo_100 = parseFloat($('#sobre_tiempo_100').val());
      valor_bono = parseFloat($('#valor_bono').val());
      valor_bonoimp = parseFloat($('#bono_imputable').val());
      val_aport_per = parseFloat($('#val_aport_personal').val());

      sum_total = (sueldo_mensual)+(Sobre_Tiempo_50)+(Sobre_Tiempo_100)+(valor_bonoimp);

      calculo_aporte_iess = ((sum_total)*(val_aport_per))/100;

      if(!isNaN(calculo_aporte_iess))
      {
        $('#iess').val(calculo_aporte_iess.toFixed(2));
      }

    }

    //Muestra y Oculta Casilla Multas
    $(".checkVal_multa").click(function(){

      if ($(this).is(':checked')){

        document.getElementById("dato_multa").style.visibility = "visible";
        document.getElementById("observacion_multa").style.visibility = "visible";

      }else{

        document.getElementById("dato_multa").style.visibility = "hidden";
        document.getElementById("observacion_multa").style.visibility = "hidden";
        $("#valor_multa").val("0.00");
        $('#observacion_multa').val("");

      }

    });

    //Valida la Multa Ingresada
    function calculo_multa(elemento){

        sueldo_mensual  =  0;
        calculo_10_sueldo  = 0;

        sueldo_mensual = parseFloat($('#sueldo_recibir').val());
        var valor_multa = $('#valor_multa').val();
        if (IsNumeric(valor_multa)) {
            valor_multa = parseFloat($('#valor_multa').val());
            console.log('es num');
        }else{
            $("#valor_multa").val("0.00");
            valor_multa = 0;
        }

        calculo_10_sueldo = (sueldo_mensual*10)/100;

        if(valor_multa > calculo_10_sueldo){

          swal("Error!","La Multa no debe ser mayor al 10% del Sueldo"+" : "+sueldo_mensual);

          $("#valor_multa").val("0.00");

        }

    }


    //Verifica Seleccion Forma de Pago
    function revisar_seleccion(){

      var id_tipo = $("#tipo_pago").val();

      if (id_tipo == 2){

        document.getElementById("num_che").style.visibility = "hidden";
        document.getElementById("id_tip_cuenta").style.visibility = "hidden";
        document.getElementById("id_banco").style.visibility = "hidden";
        document.getElementById("num_cuenta").style.visibility = "hidden";
        $('#num_cheque').val("");
        $('#tipo_cuenta').val("");
        $('#banco').val("");
        $('#numero_cuenta').val("");

      } else if (id_tipo == 3) {

        document.getElementById("num_che").style.visibility = "visible";
        document.getElementById("id_tip_cuenta").style.visibility = "hidden";
        document.getElementById("id_banco").style.visibility = "hidden";
        document.getElementById("num_cuenta").style.visibility = "hidden";
        $('#num_cheque').val("");
        $('#tipo_cuenta').val("");
        $('#banco').val("");
        $('#numero_cuenta').val("");

      } else if(id_tipo == 1){

        var id_bac = $('#id_ban').val();
        var num_c = $('#num_cuent').val();

        document.ready = document.getElementById("banco").value = id_bac;
        document.ready = document.getElementById("numero_cuenta").value = num_c;
        document.getElementById("id_tip_cuenta").style.visibility = "visible";
        document.getElementById("id_banco").style.visibility = "visible";
        document.getElementById("num_cuenta").style.visibility = "visible";
        document.getElementById("num_che").style.visibility = "hidden";
        $('#num_cheque').val("");

      }
    }


    //Calculo de Monto
    function calculo_monto(){
     // alert("entra");

      sueldo_recib  =  0;
      total_ingreso  = 0;
      total_egreso = 0;
      $neto_recibir = 0;
      sobre_tiempo_50 = 0;
      sobre_tiempo_100 = 0;
      valor_bono =  0;
      valor_bonoimp =  0;
      valor_transporte = 0;
      valor_multa =  0;
      valor_alimentacion =  0;
      sum_val_quot_quir = 0;
      sum_val_quot_hip = 0;
      total_cuotas_hip_quir = 0;

      valor_fond_reserva  =  0;
      valor_dec_tercero  =  0;
      valor_dec_cuarto  =  0;

      iess = 0;
      valor_prestamo = 0;
      valor_saldo_inicial = 0;
      anticipo_quincena = 0;
      otro_anticipo = 0;
      seg_privado = 0
      imp_renta = 0
      neto_recibir = 0;
      valor_exlaboratorio = 0;

      sueldo_recib = parseFloat($('#sueldo_recibir').val());
      sobre_tiempo_50 = parseFloat($('#sobre_tiempo_50').val());
      sobre_tiempo_100 = parseFloat($('#sobre_tiempo_100').val());
      valor_bono = parseFloat($('#valor_bono').val());
      valor_bonoimp = parseFloat($('#bono_imputable').val());

      valor_fond_reserva = parseFloat($('#fondo_reserva').val());
      valor_dec_tercero = parseFloat($('#decimo_tercero').val());
      valor_dec_cuarto = parseFloat($('#decimo_cuarto').val());
      valor_alimentacion = parseFloat($('#alimentacion').val());
      valor_transporte = parseFloat($('#transporte').val());

      iess = parseFloat($('#iess').val());

      valor_multa = parseFloat($('#valor_multa').val());
      valor_prestamo = parseFloat($('#prestamo_empleado').val());
      valor_saldo_inicial = parseFloat($('#saldo_inicial').val());
      anticipo_quincena = parseFloat($('#anticipo_quincena').val());
      otro_anticipo = parseFloat($('#otro_anticipo').val());
      seg_privado = parseFloat($('#seguro_privado').val());
      imp_renta = parseFloat($('#impuesto_renta').val());
      valor_exlaboratorio = parseFloat($('#exam_laboratorio').val());


      val_fond_rese_cobr = parseFloat($('#fond_res_cobrar_trab').val());

      val_otros_egresos = parseFloat($('#otros_egresos_trab').val());

      //Aqui Calculo La suma de los Prestamos Hipotecarios y Quirografario
      //Llamada a las Funciones
      suma_totales_quirografario();
      suma_totales_hipotecarios();

      sum_val_quot_quir = parseFloat($('#total_val_quot_quir').val());
      sum_val_quot_hip = parseFloat($('#total_val_quot_hip').val());

      total_cuotas_hip_quir = sum_val_quot_quir+sum_val_quot_hip;

      if((sum_val_quot_quir)>0 || (sum_val_quot_hip>0)){

        total_cuotas_hip_quir = sum_val_quot_quir+sum_val_quot_hip;

      }

      if ((!isNaN(sueldo_recib))&&(valor_fond_reserva >0)&&(valor_dec_tercero >0)&&(valor_dec_cuarto >0)) {

        total_ingreso  = (sueldo_recib)+(sobre_tiempo_50)+(sobre_tiempo_100)+(valor_bono)+(valor_bonoimp)+(valor_fond_reserva)+(valor_dec_tercero)+(valor_dec_cuarto);
        console.log(total_ingreso, "aqui");

      }else if((!isNaN(sueldo_recib))&&(valor_fond_reserva >0)){

        total_ingreso  = (sueldo_recib)+(sobre_tiempo_50)+(sobre_tiempo_100)+(valor_bono)+(valor_bonoimp)+(valor_fond_reserva);
        console.log(total_ingreso, "aqui2");

      }else if((!isNaN(sueldo_recib))&&(valor_dec_tercero >0)&&(valor_dec_cuarto >0)){

        total_ingreso  = (sueldo_recib)+(sobre_tiempo_50)+(sobre_tiempo_100)+(valor_bono)+(valor_bonoimp)+(valor_dec_tercero)+(valor_dec_cuarto);
        console.log(total_ingreso, "aqui3");

      }else if(!isNaN(sueldo_recib)){
        total_ingreso  = (sueldo_recib)+(sobre_tiempo_50)+(sobre_tiempo_100)+(valor_bono)+(valor_bonoimp);
        console.log(total_ingreso, "aqui4", valor_dec_cuarto);
      }


      //Calculo Total Egresos
      if (valor_prestamo > 0){

        total_egreso  =  (iess)+(valor_multa)+(valor_prestamo)+(valor_saldo_inicial)+(anticipo_quincena)+(otro_anticipo)+(seg_privado)+(imp_renta)+(total_cuotas_hip_quir)+(val_fond_rese_cobr)+(val_otros_egresos)+(valor_exlaboratorio);
        console.log(total_egreso, '1');

      }else{

        total_egreso  =  (iess)+(valor_multa)+(anticipo_quincena)+(valor_saldo_inicial)+(otro_anticipo)+(seg_privado)+(imp_renta)+(total_cuotas_hip_quir)+(val_fond_rese_cobr)+(val_otros_egresos)+(valor_exlaboratorio);
         console.log(total_egreso, '2');

      }
      //console.log(total_egreso, iess, valor_multa, valor_prestamo, valor_saldo_inicial, anticipo_quincena, otro_anticipo, seg_privado, imp_renta, total_cuotas_hip_quir, val_fond_rese_cobr, val_otros_egresos, valor_exlaboratorio);
      //console.log(total_egreso);

      //Calculo Neto Recibido
      if (total_ingreso > total_egreso){
        neto_recibir  =  (total_ingreso + valor_alimentacion + valor_transporte)-(total_egreso);
      }
      //console.log($neto_recibir);

      if(!isNaN(neto_recibir))
      {
        $('#monto').val(neto_recibir.toFixed(2));
      }
      console.log(neto_recibir, sueldo_recib, total_ingreso, total_egreso, iess, );
    }

    function cargar_tabla_quirografario(){

      $.ajax({
            type: 'get',
            url:"{{route('carga_listado.quirografario',['id' => $rol_pag->id])}}",
            datatype: 'json',
            success: function(data){
                $('#agregar_cuot_quir').empty().html(data);
            },
            error: function(data){
                console.log(data);
            }
      });

    }

    function cargar_tabla_hipotecario(){

      $.ajax({
            type: 'get',
            url:"{{route('carga_listado.hipotecario',['id' => $rol_pag->id])}}",
            datatype: 'json',
            success: function(data){
                $('#agregar_cuot_hipot').empty().html(data);

            },
            error: function(data){
                console.log(data);

            }
      });

    }

    $('#btn_quirografario').click(function(event){

      id  = document.getElementById('contador_quiro').value;
      id_2 = document.getElementById('contad_cuot_quir').value;

      var midiv_pago = document.createElement("tr")
      midiv_pago.setAttribute("id","dato_quiro"+id);

      midiv_pago.innerHTML = '<td><input type="text" name="valor_cuota_quir'+id+'" id="valor_cuota_quir'+id+'" style="width: 240px;height:25px" onkeypress="return soloNumeros(event);" onblur="this.value=parseFloat(this.value).toFixed(2);"><input required type="hidden" id="visibilidad_quiro'+id+'" name="visibilidad_quiro'+id+'" value="1"></td><td><input required type="text" name="detalle_cuota_quir'+id+'" id="detalle_cuota_quir'+id+'" style="width: 240px;height:25px" value="Prest Quirografario Cuota '+id_2+'" required></td><td><button type="button" onclick="eliminar_cuot_quiro('+id+')" class="btn btn-warning btn-margin">Eliminar</button></td>';

      document.getElementById('agregar_cuot_quir').appendChild(midiv_pago);
      id = parseInt(id);
      id = id+1;
      id_2= parseInt(id_2);
      id_2= id_2+1;

      $('#contador_quiro').val(id);
      $('#contad_cuot_quir').val(id_2);

    });

    $('#btn_hipotecario').click(function(event){

      id= document.getElementById('contador_hip').value;
      id_3= document.getElementById('contad_cuot_hip').value;

      var midiv_pago = document.createElement("tr")
      midiv_pago.setAttribute("id","dato_hip"+id);

      midiv_pago.innerHTML = '<td><input type="text" name="valor_cuota_hip'+id+'" id="valor_cuota_hip'+id+'" style="width: 240px;height:25px" onkeypress="return soloNumeros(event);" onblur="this.value=parseFloat(this.value).toFixed(2);"><input required type="hidden" id="visibilidad_hip'+id+'" name="visibilidad_hip'+id+'" value="1"></td><td><input required type="text" name="detalle_cuota_hip'+id+'" id="detalle_cuota_hip'+id+'" style="width: 240px;height:25px" value="Prest Hipotecario Cuota '+id_3+'" required></td><td><button type="button" onclick="eliminar_cuot_hip('+id+')" class="btn btn-warning btn-margin">Eliminar</button></td>';

      document.getElementById('agregar_cuot_hipot').appendChild(midiv_pago);
      id = parseInt(id);
      id = id+1;
      id_3= parseInt(id_3);
      id_3= id_3+1;
      document.getElementById('contador_hip').value = id;
      document.getElementById('contad_cuot_hip').value = id_3;

    });

    //Elimina Registro de la Tabla Forma de Pago
    function eliminar_cuot_quiro(valor)
    {
      console.log(valor);
        var dato_cuota_quir = "dato_quiro"+valor;
        var nombre_cuota_quir = 'visibilidad_quiro'+valor;
        document.getElementById(dato_cuota_quir).style.display='none';
        document.getElementById(nombre_cuota_quir).value = 0;

        //Decrece el numero de Cuota
        id_2= document.getElementById('contad_cuot_quir').value;
        id_2= id_2-1;
        document.getElementById('contad_cuot_quir').value = id_2;
        $.ajax({
            url: "{{route('rol_log_eliminar')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data:{
              detalle: "prestamos quirografario",
            },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
            },
            error: function(xhr, status) {
                alert('Existió un problema');
                //console.log(xhr);
            },
        });


    }

    //Elimina Registro de la Tabla Forma de Pago
    function eliminar_cuot_hip(valor)
    {
      var dato_cuota_hip  = "dato_hip"+valor;
      var nombre_cuota_hip = 'visibilidad_hip'+valor;
      document.getElementById(dato_cuota_hip).style.display='none';
      document.getElementById(nombre_cuota_hip).value = 0;

      //Decrece el numero de Cuota
      id_3= document.getElementById('contad_cuot_hip').value;
      id_3= id_3-1;
      document.getElementById('contad_cuot_hip').value = id_3;
      $.ajax({
            url: "{{route('rol_log_eliminar')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data:{
              detalle: "prestamos hipotecario",
            },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
            },
            error: function(xhr, status) {
                alert('Existió un problema');
                //console.log(xhr);
            },
        });

    }

    function suma_totales_hipotecarios(){

      contador_hipot  =  0;
      sb12_hipot = 0;
      valor_quota_hip = 0;

      $("#agregar_cuot_hipot tr").each(function(){
        $(this).find('td')[0];
        visibilidad_hip = $(this).find('#visibilidad_hip'+contador_hipot).val();


        if(visibilidad_hip == 1){

          valor_quota_hip = parseFloat($(this).find('#valor_cuota_hip'+contador_hipot).val());
          if(!isNaN(valor_quota_hip)){
            sb12_hipot = sb12_hipot+valor_quota_hip;
          }

        }

        contador_hipot = contador_hipot+1;

      });

      $('#total_val_quot_hip').val(sb12_hipot);

    }

    function suma_totales_quirografario(){

      contador_quir =  0;
      sb12_quir = 0;
      valor_quota_quir = 0;

      $("#agregar_cuot_quir tr").each(function(){
        $(this).find('td')[0];
        visibilidad_quiro = $(this).find('#visibilidad_quiro'+contador_quir).val();

        if(visibilidad_quiro == 1){


          valor_quota_quir = parseFloat($(this).find('#valor_cuota_quir'+contador_quir).val());
          if(!isNaN(valor_quota_quir)){
            sb12_quir = sb12_quir+valor_quota_quir;
          }


        }

        contador_quir = contador_quir+1;

      });

      $('#total_val_quot_quir').val(sb12_quir);

    }

    //ACTUALIZA ROL DE PAGO
    function actualiza_rol(){
      calculo_monto();

      var desc_mes = 0;
      $('#act_rol').addClass('oculto');
      $('#div_load').removeClass('oculto');
      var formulario = document.forms["actualiza_rol_pago"];
      var sueldo_mensual = formulario.sueldo_recibir.value;

      var year = formulario.year.value;

      var mes = parseInt(formulario.mes.value);

      var tipo_rol = formulario.tipo_rol.value;

      //Formas de Pago
      //var tip_pa = formulario.tipo_pago.value;

      //Calculo del Valor Neto a Recibir Empleado
      var val_recib = formulario.monto.value;

      switch(mes){

        case 1:
          desc_mes = 'Enero';
          break;

        case 2:
          desc_mes = 'Febrero';
          break;

        case 3:
          desc_mes = 'Marzo';
          break;

        case 4:
          desc_mes = 'Abril';
          break;

        case 5:
          desc_mes = 'Mayo';
          break;

        case 6:
          desc_mes = 'Junio';
          break;

        case 7:
          desc_mes = 'Julio';
          break;

        case 8:
          desc_mes = 'Agosto';
          break;

        case 9:
          desc_mes = 'Septiembre';
          break;

        case 10:
          desc_mes = 'Octubre';
          break;

        case 11:
          desc_mes = 'Noviembre';
          break;

        case 12:
          desc_mes = 'Diciembre';
          break;

      }

      var msj = "";

      if(sueldo_mensual == ""){

        msj = msj + "Por favor, Ingrese el Sueldo a Recibir<br/>";

      }

      if(year == ""){
        msj = msj + "Por favor, Seleccione el año<br/>";
      }

      if(desc_mes == ""){
        msj = msj + "Por favor, Seleccione el mes<br/>";
      }

      if(tipo_rol == ""){
        msj = msj + "Por favor, Seleccione el tipo de Rol<br/>";
      }

      /*if(tip_pa == ""){
        msj = msj + "Por favor, seleccione el tipo de Pago<br/>";
      }*/

      if(val_recib == ""){
        msj = msj + "Por favor, Calcule el Neto a Recibir por el Empleado<br/>";
      }

      valor_bonoimp = parseFloat($('#bono_imputable').val());
          observacion_bonoimpu=  Boolean($('#observacion_bonoimp').val());
          if(valor_bonoimp >= "1"){
             if(observacion_bonoimpu == false){
                msj = msj + "Por favor, Ingrese la Descripcion del Bono Imputable<br/>";
             }
          }

          valor_exlaboratorio = parseFloat($('#exam_laboratorio').val());
          observacion_exlaboratorio=  Boolean($('#observ_examlaboratorio').val());
          if(valor_exlaboratorio >= "1"){
             if(observacion_exlaboratorio == false){
                msj = msj + "Por favor, Ingrese la Descripcion del Examen de Laboratorio<br/>";
             }
          }


      //Obtenemos el valor del Contador de Forma de Pago
      //var cont_pag = formulario.contador_pago.value;
      var contador = $('#contador_pago').val();
      var sum_pag = 0;

      for (i = 0; i < contador; i++){

        if($('#visibilidad_pago'+i).val() == 1){

          sum_pag = sum_pag+parseFloat($('#valor'+i).val());

        }
      }

      var mont_pag = $('#monto').val();

      //if(mont_pag != sum_pag){

          //msj += "El Total de las Formas de Pago debe ser Igual al Total a recibir por el Empleado<br/>";

      //}

      //alert(sum_pag);

      if(msj != ""){

        swal({
              title: "Error!",
              type: "error",
              html: msj
            });
          $('#act_rol').removeClass('oculto');
          $('#div_load').addClass('oculto');
        return false;
      }

      $.ajax({
          type: 'post',
          url:"{{route('rol_pago.update')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $('#actualiza_rol_pago').serialize(),
          success: function(data){

            if(data.msj =='ok'){

                Swal.fire({
                    title: 'Desea Enviar el Rol de Pago?',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: `Enviar`,
                    denyButtonText: `No Enviar`,
                    showLoaderOnConfirm: true,
                }).then((result) => {

                  if (result.isConfirmed) {
                            $.ajax({
                              url:"{{asset('contable/rol/pago/envio/correo/')}}/"+data.id_rol_pago,
                              headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                              type: 'GET',
                              success: function(data){
                                  if(data == 'ok'){
                                    Swal.fire('Enviado Correctamente!', '', 'success');
                                    //window.open("{{asset('/comprobante/rol/pago')}}/"+data.id_rol_pago, '_blank ');
                                    location.href="{{route('nomina.index')}}";
                                    limpiar_datos();
                                    $('#act_rol').removeClass('oculto');
                                    $('#div_load').addClass('oculto');
                                  }
                              },
                              error: function(data){
                                $('#act_rol').removeClass('oculto');
                                $('#div_load').addClass('oculto');
                                  Swal.fire('Error al Enviar el Correo!', '', 'error');
                              }
                            });

                  }else{
                      location.href="{{route('nomina.index')}}";
                      limpiar_datos();
                      $('#act_rol').removeClass('oculto');
                      $('#div_load').addClass('oculto');
                  }
                  window.open("{{asset('/comprobante/rol/pago')}}/"+data.id_rol_pago, '_blank ');
                });
            }

          },
          error: function(data){
              console.log(data);
          }
      })

    }


    function limpiar_datos(){
      //$('#sueldo_mensual').val(" ");
      //$('#sobre_tiempo_50').val(" ");
      //$('#sobre_tiempo_100').val(" ");
      //$('#iess').val(" ");
      //$('#atrasos').val(" ");
      //$('#anticipo_quincena').val(" ");
    }


    function buscar_anticipo(){

      $.ajax({
            type: 'post',
            url:"{{route('existe_anticipo.empleado')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            type: 'POST',
            datatype: 'json',
            data: {'identificacion': $("#id_user").val(),
             'id_empresa': $("#id_empresa").val(),'anio': $("#year").val(),'mes': $("#mes").val()},
            success: function(data){

              //console.log(data.existe_anticipo);

              if(data.existe_mes == '1'){

                $("#anticipo_quincena").val(data.mont_anticip);
                $("#concepto_quincena").val(data.concept_anticip);

              }else{

                $("#anticipo_quincena").val("0.00");
                $("#concepto_quincena").val("");
              }

            },
            error: function(data){
                console.log(data);
            }
      });

    }

    /*Otros Anticipos */
    function buscar_otro_anticipo(){

      $.ajax({
            type: 'post',
            url:"{{route('existe_otros_anticipo.empleado')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            type: 'POST',
            datatype: 'json',
            data: {'identificacion': $("#id_user").val(),
            'id_empresa': $("#id_empresa").val(),'anio': $("#year").val(),'mes': $("#mes").val()},
            success: function(data){

              //console.log(data.existe_anticipo);

              if(data.existe_mes == '1'){

                $("#otro_anticipo").val(data.total_sum);
                $("#concep_otros_anticipos").val(data.acum_obs);

              }else{

                $("#otro_anticipo").val("0.00");
                $("#concep_otros_anticipos").val("");
              }

            },
            error: function(data){
                console.log(data);
            }
      });

    }

    function buscar_prestamos(){

      $.ajax({
            type: 'post',
            url:"{{route('rol_pago.existe_prestamos')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            type: 'POST',
            datatype: 'json',
            data: {'identificacion': $("#id_user").val(),
            'id_empresa': $("#id_empresa").val(),'anio': $("#year").val(),'mes': $("#mes").val()},
            success: function(data){
              console.log(data);
              if(data.existe_mes == '1'){

                $("#prestamo_empleado").val(data.val_cuot);
                $("#concepto_prestamo").val(data.obser_prest);

              }else{

                $("#prestamo_empleado").val("0.00");
                $("#concepto_prestamo").val("");
              }

            },
            error: function(data){
                console.log(data);
            }
      });

    }

    window.onload = ()=>{
      let sueldo_recibir = document.getElementById('sueldo_recibir').value;
       var mens_acum_dec_terc = $('#mens_acumu_decim_terc').val();
       var mens_f_res = $('#mens_fond_reser').val();
      //console.log(sueldo_recibir)
      if(sueldo_recibir.length>0){
        if(mens_acum_dec_terc == 2){
          calculo_decimo_tercero();
        }
        if(mens_f_res == 2){
          calculo_fondo_reserva();
        }
        
        calculo_porcentaje_iess();
        //calculo_fondo_reserva();
      }
    }

    function caculos_todos (){

      var mens_acum_dec_terc = $('#mens_acumu_decim_terc').val();
      var mens_f_res = $('#mens_fond_reser').val();
      var mens_acum_dec_cuart = $('#mens_acumu_decim_cuart').val();
      
      calculo_sueldo_dias();
      calculo_porcentaje_iess();
      if(mens_acum_dec_terc == 2){
        calculo_decimo_tercero();
      }
      if(mens_f_res == 2){
        calculo_fondo_reserva();
      }

      if(mens_f_res == 1){
        calculo_fondo_reserva_acumulado();
      }

      if(mens_acum_dec_cuart == 1){
        calculo_decimo_cuarto_acumulado();
      }
    }

  </script>

</section>
@endsection