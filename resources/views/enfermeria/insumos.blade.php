@extends('enfermeria.base2')
@section('action-content')
<style type="text/css">
  .alerta_correcto {
    position: absolute;
    z-index: 9999;
    top: 100px;
    right: 10px;
  }

  .ui-autocomplete {
    opacity: 1;
    overflow-x: hidden;
    max-height: 200px;
    width: 1px;
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    float: left;
    display: none;
    min-width: 160px;
    _width: 470px !important;
    padding: 4px 0;
    margin: 2px 0 0 0;
    list-style: none;
    background-color: #fff;
    border-color: #ccc;
    border-color: rgba(0, 0, 0, 0.2);
    border-style: solid;
    border-width: 1px;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -webkit-background-clip: padding-box;
    -moz-background-clip: padding;
    background-clip: padding-box;
    *border-right-width: 2px;
    *border-bottom-width: 2px;
  }

  .eliminar_producto {
    width: 20px;
    height: 20px;
    margin-top: 10px !important;
    margin-left: 10px !important;

  }

  .eliminar_producto_css {
    width: 20px;
    height: 20px;
    margin-top: 10px !important;
    margin-left: 72px !important;
    margin-right: 15px !important;
  }

  .btn-procedimientos {
    background: transparent;
    border: 0px;
    color: white;
    font-weight: 400;
  }

  .btn-sheyla {
    background-color: pink !important;
    border-color: pink !important;
    color: black !important;
  }
</style>
<!-- Main content -->


<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
  <div id="alerta_datos" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none;">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    {{trans('eenfermeria.ObservaciónGuardadaCorrectamente')}}
  </div>
  <div class="box">
    <input type="hidden" id="boing">
    <div class="box-header">
      <div class="row">
        <div class="col-md-12">
          <div class="col-md-10">
            <h4 class="box-title"><b>{{trans('eenfermeria.Paciente')}}: </b><span style="color: red;">{{$agenda->id_paciente}}-{{$agenda->paciente->apellido1}} @if($agenda->paciente->apellido2!='(N/A)'){{$agenda->paciente->apellido2}}@endif {{$agenda->paciente->nombre1}} @if($agenda->paciente->nombre2!='(N/A)'){{$agenda->paciente->nombre2}}@endif </span></h4>
          </div>
          <div class="col-md-5">
            <h4 class="box-title"><b>{{trans('econsultam.Seguro')}}: </b> {{$agenda->seguro->nombre}}</h4>
          </div>
          <div class="col-md-5">
            <h4 class="box-title"><b>{{trans('eenfermeria.Fecha')}}: </b> {{$agenda->fechaini}}</h4>
          </div>
        </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>

      <div class="box box-success">
        <div class="box-header">
        </div>
        <div class="box-body">
          <div class="table-responsive col-md-12">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
              <thead>
                <tr role="row">
                  <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">{{trans('eenfermeria.Código')}}</th>
                  <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">{{trans('ecamilla.Nombre')}}</th>
                  <th width="8%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">{{trans('eenfermeria.Fecha')}}</th>
                  <th width="8%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">{{trans('econsultam.Usuario')}}</th>
                  <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">{{trans('erol.Acción')}}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($equipos as $value)
                <tr>
                  <td>{{$value->equipo->serie}}</td>
                  <td>{{$value->equipo->nombre}}</td>
                  <td>{{$value->created_at}}</td>
                  <td>{{$value->usuario_crea->apellido1}} {{$value->usuario_crea->nombre1}}</td>
                  <td><a onclick="eliminar({{$value->id}})" class="btn btn-danger col-md-8 col-sm-8 col-xs-8 btn-margin">{{trans('eenfermeria.Eliminar')}}</a>
                  </td>
                </tr>
                @endforeach
               
                <form id="codigo_enviar" onsubmit="return false;">
                  <input type="hidden" name="id_historia" value="{{$hcid}}">

                  <input type="hidden" id="p_hcid" name="p_hcid" value="{{$hcid}}">
                  <tr>
                    <td><input id="recibir_equipo" type="text" name="codigo" class="form-control input-sm" style="width: 90%;background-color: #e6f9ff;" onchange="enviar2()"></td>

                    <td id="agregar_respuesta" colspan="3"></td>
                  </tr>
                </form>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="box box-info">
        <div class="box-header">
          <b>{{trans('eenfermeria.Observaciones')}}</b>
        </div>
        <div class="box-body">
          <form id="hc_observacion">
            <input type="hidden" name="id_historia2" value="{{$hcid}}">
            <textarea name="observaciones_enfermeria" id="observaciones_enfermeria" onchange="guardar_observacion();" style="width: 100%">@if(isset($agenda->historia_clinica)){{$agenda->historia_clinica->observaciones_enfermeria}}@endif</textarea>
          </form>
          <button class="btn btn-success btn-xs" onclick="guardar_msn();"><span class="glyphicon glyphicon-floppy-disk">&nbsp;</span>{{trans('econtrolsintomas.Guardar')}}</button>
        </div>
      </div>




      @php
      $contador = 0;
      $procedimientosx=[]; 
      $procd =[];
      $cont_checks = 0;
      $check_eliminado = 0;
      if(Auth::user()->id == "0957258056"){
      }
      @endphp
      @foreach($procedimientos as $value)
      @php 
      $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->id)->get();

      $mas = true;
      $texto = "";

      foreach($adicionales as $value2)
      {
        if($mas == true){
          $texto = $texto.$value2->procedimiento->nombre ;
          $mas = false;
        }
        else{
          $texto = $texto.' + '. $value2->procedimiento->nombre ;
        }
      }
        $data['id']=$value->id;
        $data['nombre']=$texto;
        if($texto==null){
        $data['nombre']= $value->nombre_general;
        }
        array_push($procedimientosx,$data);
      @endphp
      @endforeach
      @foreach($procedimientos as $value)
      @if($value->nombre_general==null)
      @php
      $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->id)->get();

      $mas = true;
      $texto = "";

      foreach($adicionales as $value2)
      {
        if($mas == true){
          $texto = $texto.$value2->procedimiento->nombre ;
          $mas = false;
        }
        else{
          $texto = $texto.' + '. $value2->procedimiento->nombre ;
        }
      }
      /* $data['id']=$value->id;
      $data['nombre']=$texto;
      if($texto==null){
      $data['nombre']= $value->nombre_general;
      }
      array_push($procedimientosx,$data); */
      @endphp
      @endif
      @php
      $productos = \Sis_medico\Movimiento_Paciente::where('id_hc_procedimientos', $value->id)->get();


      @endphp
      <div class="box box-warning">
        <div class="row">
          </span>
          <div class="col-md-5">
            <h3><b>{{trans('eenfermeria.Procedimiento')}}: </b>@if($value->nombre_general==null) @if(!is_null($texto)) @php if(Auth::user()->id == "0957258056"){ } @endphp {{$texto}} @else NO INGRESADO @endif @else{{$value->nombre_general}} @endif</h3>
          </div>

          <div class="col-md-5" style="margin-top: 10px; text-align: right;">
            <a class="btn btn-info btn-xs" href="{{route('anestesiologia.mostrar',['id'=>$value->id])}}"> <i class="fa fa-medkit"></i> {{trans('eenfermeria.RécordAnestésico')}} </a>
            @if(Auth::user()->id_tipo_usuario == 1 or Auth::user()->id == "0931563241")
            <input class="btn btn-success btn-xs btn-sheyla" style="margin-top: -20px;padding: 10px 29px;" onchange="subirexcel(this,{{$contador}})" id="excelsubir{{$contador}}" type="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" value="Subir Excel">
            <!--a class="btn btn-info btn-xs" href="{{route('productos.comparar',['id'=>$value->id,'ix'=>'1'])}}"> <i class="fa fa-check"></i> Revision </a-->
            @endif
          </div>
          <div class="table-responsive col-md-12">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
              <thead>
                <tr role="row">
                  <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">{{trans('eenfermeria.Código')}}</th>
                  <th width="8%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">{{trans('ecamilla.Nombre')}}</th>
                  <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">{{trans('eenfermeria.Fecha')}}</th>
                  <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">{{trans('eenfermeria.Cantidad')}}</th>
                  <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">{{trans('econsultam.Usuario')}}</th>
                  <th style="text-align: end;" width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">
                    Accion
                    @if(Auth::user()->id_tipo_usuario == 1 or Auth::user()->id_tipo_usuario == 7)
                    @php $check_eliminado++; @endphp
                    <input class="eliminar_producto_css" onclick="checkAll({{$check_eliminado}})" id="checktodo{{$check_eliminado}}" type="checkbox" value="1" />

                    @endif
                  </th>
                </tr>
              </thead>
              <tbody>
                <!--Array de Productos-->
                @php $resumen = array(); @endphp
                @foreach($productos as $producto)

                if(isset($producto->movimiento) and !isset($resumen[$producto->movimiento->producto->nombre]) ) {
                $resumen[$producto->movimiento->producto->nombre] = 1;
                } else {
                if (isset($producto->movimiento->producto)) {
                $resumen[$producto->movimiento->producto->nombre] += 1;
                }
                }
                $cont_checks++;

                @endphp
                <?php
                switch($producto->movimiento->producto->tipo){
                  case 1 :
                    echo '<label for="">HONORARIOS MEDICOS</label>
                    <tr>
                      <td>'.isset($producto->movimiento)?$producto->movimiento->serie:''.'</td>
                      <td>@if(isset($producto->movimiento)) {{$producto->movimiento->producto->nombre}}@endif</td>
                      <td>{{$producto->created_at}}</td>
                      <td>{{$producto->cantidad}}</td>
                      <td>{{$producto->usuario_crea->apellido1}} {{$producto->usuario_crea->nombre1}}</td>
                      <td><a onclick="eliminar_producto({{$producto->id}})" class="btn btn-danger col-md-8 col-sm-8 col-xs-8 btn-margin">Eliminar</a>
                        @if(Auth::user()->id_tipo_usuario == 1 or Auth::user()->id_tipo_usuario == 7)
                        <input class="eliminar_producto{{$check_eliminado}}" type="checkbox" value="{{$producto->id}}" />
                        @endif
                      </td>
                      </td>
                    </tr>';
                    break;
                }
                                ?>

             
                @endforeach
                <form id="codigo_enviar{{$contador}}" onsubmit="return false;">
                  <a id="redirigir{{$contador}}" href="{{route('enfermeria.insumos',['id'=>$agenda->id])}}#recibir" class="oculto"></a>
                  <input type="hidden" id="id_hc_procedimientos" name="id_hc_procedimientos" value="{{$value->id}}">
                  <input type="hidden" id="id_agenda" name="id_agenda" value="{{$agenda->id}}">
                  <tr style="text-align: center">
                    <input type="hidden" id="excel{{$contador}}" value="0">
                    <td><input id="recibir{{$contador}}" type="text" name="codigo" class="form-control input-sm" style="width: 90%;background-color: #e6f9ff;" onchange="enviar('{{$contador}}')"></td>
                    <td id="agregar_respuesta{{$contador}}" colspan="3"></td>
                    @if(Auth::user()->id_tipo_usuario == 1 or Auth::user()->id_tipo_usuario == 7)
                    <td>

                    </td>
                    <td>
                      @if($cont_checks > 0)
                      <button type="button" class="btn btn-danger" style="padding: 6px 50px;" onclick="verificarChecks(event, {{$check_eliminado}})">{{trans('eenfermeria.EliminarCheck')}}</button>
                      <button type="button" class="btn btn-warning" style="padding: 6px 50px;" data-toggle="modal" data-target="#md_mover_insumo_{{$value->id}}"> {{trans('eenfermeria.MoverCheck')}}</button>
                      @endif
                    </td>
                    @endif
                  </tr>
                </form>
              </tbody>
            </table>

            <div class="modal fade" id="md_mover_insumo_{{$value->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{trans('eenfermeria.MoverInsumo')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    
                  <div class="row"> 
                    <div class="col-md-4">
                      <label>{{trans('eenfermeria.SeleccioneProcedimiento')}}</label>
                    </div>
                    <div class="col-md-8">
                      <select name="modal_hc_procedimientos_{{$value->id}}" id="modal_hc_procedimientos_{{$value->id}}" class="form-control select2">
                        @foreach($procedimientosx as $p)
                        <option value="{{$p['id']}}">{{$p['nombre']}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
          
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{trans('eenfermeria.Cancelar')}}</button>
                    <button type="button" class="btn btn-primary" onclick="moverChecks({{$check_eliminado}}, {{$value->id}})" >{{trans('eenfermeria.Mover')}}</button>
                  </div>
                </div>
              </div>
            </div>

          </div>
          <div class="col-md-12">
            @if (Auth::user()->id=='0931563241' or Auth::user()->id_tipo_usuario == 1)
            {{-- resumen --}}
            <div class="box-group" id="accordion_{{$contador}}">
              <div class="col-md-12">
                <a data-toggle="collapse" data-parent="#accordion_{{$contador}}" href="#collapse_{{$contador}}" aria-expanded="false" class="collapsed">
                  <h3><b>{{trans('econsultam.ResumenProcedimiento')}}: </b>@if($value->nombre_general==null) @if(!is_null($texto)) {{$texto}} @else NO INGRESADO @endif @else{{$value->nombre_general}}@endif</h3>
                </a>
              </div>
            </div>
            <div id="collapse_{{$contador}}" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
              <div class="box-body">
                <div class="table-responsive col-md-12">
                  <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                      <tr role="row">
                        <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">#</th>
                        <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">{{trans('eenfermeria.Producto')}}</th>
                        <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">{{trans('eenfermeria.Cantidad')}}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php $linea = 1; @endphp
                      @foreach($resumen as $key => $value)
                      <tr>
                        <td>{{$linea}}</td>
                        <td>{{$key}}</td>
                        <td>{{$value}}</td>
                      </tr>
                      @php $linea++; @endphp
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          @endif


        </div>
      </div>




      {{-- <div class="box-group" id="accordion_{{$contador}}">
      <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
      <div class="panel box">
        <div class="box-header">
          <h4 class="box-title">
            <a data-toggle="collapse" data-parent="#accordion_{{$contador}}" href="#collapse_{{$contador}}" aria-expanded="false" class="collapsed">
              <h3><b>{{trans('econsultam.ResumenProcedimiento')}}: </b></h3>
            </a>
          </h4>
        </div>
        <div id="collapse_{{$contador}}" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
          <div class="box-body">
            <div class="table-responsive col-md-12">
              <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                <thead>
                  <tr role="row">
                    <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">#</th>
                    <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">{{trans('eenfermeria.Producto')}}</th>
                    <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">{{trans('eenfermeria.Cantidad')}}</th>
                  </tr>
                </thead>
                <tbody>
                  @php $linea = 1; @endphp
                  @foreach($resumen as $key => $value)
                  <tr>
                    <td>{{$linea}}</td>
                    <td>{{$key}}</td>
                    <td>{{$value}}</td>
                  </tr>
                  @php $linea++; @endphp
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div> --}}

    @php
    $contador = $contador+1;
    @endphp
    @endforeach


    <form id="frmAgregarMedicamento" onsubmit="return false;">
      <div class="box box-info">
        <div class="row">
          <div class="col-md-12">
            <h3><b>{{trans('eenfermeria.MedicamentosProcedimiento')}}: </b></h3>
          </div>
          <div class="col-md-4">
            <label>{{trans('eenfermeria.SeleccioneProcedimiento')}}</label>
          </div>
          <div class="col-md-4">
            <label>{{trans('eenfermeria.SeleccioneMedicamento')}}</label>
          </div>
          <div class="col-md-4">
            <label>{{trans('eenfermeria.Cantidad')}}</label>
          </div>
          <div class="col-md-4">
            <select name="md_hc_procedimientos" id="md_hc_procedimientos" class="form-control select2">
              <option value="">{{trans('econtrolsintomas.Seleccione')}} ...</option>
              @foreach($procedimientosx as $p)
              <option value="{{$p['id']}}">{{$p['nombre']}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <select class="form-control" id="id_insumo" name="id_insumo">
              <option value="428">{{trans('eenfermeria.DERMABOND')}}</option>
              <option value="188">{{trans('eenfermeria.BIO-GLO10%')}}</option>
              <option value="822">{{trans('eenfermeria.EPINEFRINA')}}</option>
              <option value="799">{{trans('eenfermeria.SONOVUE')}}</option>
              <option value="103">{{trans('eenfermeria.GADOBUTROLINYECTABLE')}}</option>
            </select>
          </div>
          <div class="input-group input-group col-md-3">
            <input type="text" class="form-control" id="cantidad" name="cantidad" value="1">
            <span class="input-group-btn">
              <button type="button" class="btn btn-info btn-flat" onclick="enviarMedicamento()">{{trans('eenfermeria.Cargar')}}</button>
            </span>
          </div>
        </div>
      </div>
    </form>



    <div class="box box-info">
      <div class="row">
        <div class="col-md-12">
          <h3><b>{{trans('eenfermeria.MedicamentosProcedimiento')}}: </b></h3>

        </div>
        <div class="col-md-4">
          <label>{{trans('eenfermeria.SeleccioneProcedimiento')}}</label>
        </div>
        <div class="col-md-6">
          <label>{{trans('eenfermeria.SeleccioneMedicamento')}}</label>
        </div>
        <div class="col-md-4">
          <select name="hc_procedimientos" id="hc_procedimientos" class="form-control select2">
            <option value="">{{trans('eenfermeria.Cantidad')}} ...</option>
            @foreach($procedimientosx as $p)
            <option value="{{$p['id']}}">{{$p['nombre']}}</option>
            @endforeach
          </select>
        </div>
        {{-- <div class="col-md-4">
            <div class="col-md-10">
              <input type="hidden" class="form-control input-sm" id="id_plantilla" name="id_plantilla">
              <input type="text" class="form-control input-sm" id="nom_plantilla" name="nom_plantilla">
            </div>
          </div>
          <div class="col-md-2">
            <button type="button" class="btn btn-success" onclick="mostrar_planilla();"> {{trans('erol.Buscar')}}</button>
            <input type="hidden" id="id_plantilla_2" name="id_plantilla_2" >
          </div> --}}
        <div class="input-group input-group col-md-4">
          <input type="hidden" id="id_plantilla" name="id_plantilla">
          <input type="hidden" id="id_plantilla_2" name="id_plantilla_2">
          <input type="text" class="form-control" id="nom_plantilla" name="nom_plantilla" required>
          <span class="input-group-btn">
            <button type="button" class="btn btn-info btn-flat" onclick="mostrar_planilla();">{{trans('erol.Buscar')}}</button>
          </span>
        </div>
        <br>
        <div class="input-group input-group col-md-12">
          <center>
            <button type="button" onclick="guardar_plantilla_basica()" class="btn btn-success">{{trans('econtrolsintomas.Guardar')}}</button>
          </center>
        </div>
        <div class="col-md-12" id="detalle"></div>
      </div>
    </div>


    {{-- <div class="col-md-12">
        <div class="row">
              <div class="col-xs-3">
                <select class="form-control" id="id_insumo" name="id_insumo">
                  <option value="428">{{trans('eenfermeria.DERMABOND')}}</option>
                  <option value="188">{{trans('eenfermeria.BIO-GLO10%')}}</option>
                  <option value="97">{{trans('eenfermeria.EPINEFRINA1GR')}}</option>
                </select>
              </div>
              <div class="input-group input-group col-xs-4">
                <input type="text" class="form-control" id="cantidad" name="cantidad" value="1">
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-info btn-flat">{{trans('eenfermeria.Cargar')}}</button>
                    </span>
              </div>
        </div>
      </div> --}}

  </div>
  </div>
  <!-- /.box-body -->

  

</section>
<!-- /.content -->
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ('/js/calendario/moment.min.js') }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">

$( document ).ready(function() { 

});
  
function moverChecks(id, id_hc_proc) { 
var respuesta = false;
Swal.fire({
  title: 'Esta seguro que desea mover estos registros',
  icon: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#3085d6',
  cancelButtonColor: '#d33',
  confirmButtonText: 'Si'
}).then((result) => {
  if (result.isConfirmed) {
    ejecutaMoverCheck(id, id_hc_proc);
  }
})

return respuesta;
}

function ejecutaMoverCheck (id, id_hc_proc) {
  let checks = document.querySelectorAll(".eliminar_producto" + id);
  let contCheck = 0;
  let contChecksExito = 0;
  let contChecksError = 0;
  var id_hc_procedimiento =  $("#modal_hc_procedimientos_"+id_hc_proc).val(); 
  for (let i = 0; i < checks.length; i++) {
    if (checks[i].checked) {
      contCheck++;
      $.ajax({
        type: 'get',
        url: "{{asset('enfermeria/uso/paciente_insumo/mover')}}/" + checks[i].value+"/"+id_hc_procedimiento,
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        datatype: 'json',
        data: '',
        success: function(data) {
          if (data == 'ok') {
            contChecksExito++;
          } else {
            contChecksError++;
          }
        },
        error: function(data) {
          console.log(data);
        }
      });
    }
  }
  if (contCheck > 0) {
    Swal.fire({
      icon: 'success',
      title: `Exito..`,
      text: `Se movieron los registros correctamente`,
    });
    setTimeout(() => {
      window.location.reload();
    }, 2500)
  }


}


  $(".oculto").hide();

  function enviarMedicamento() {
    if ($("#md_hc_procedimientos").val() == "") {
      Swal.fire({
        icon: 'error',
        title: 'Mensaje!',
        text: 'Seleccione Procedimiento',
      });
      return;
    }
    $.ajax({
      type: 'post',
      url: "{{route('transito.agregar.medicamento.paciente.dia')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: $("#frmAgregarMedicamento").serialize(),
      success: function(data) {
        if (data.status == 'error') {
          Swal.fire({
            icon: 'error',
            title: 'Que raro!',
            text: data.message,
          });
        } else if (data.status == 'ok') {
          window.location.reload();
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Mensaje:',
            text: 'Que raro, error inesperado!',
          });
        }
        // if (data == 'No se encontraron resultados') {
        //   $('#agregar_respuesta').html('No se encontraron resultados');
        // } else if (data == 'caducado') {
        //   alert('No se puede usar el medicamento, se encuentra caducado');
        // } else {
        //   window.location.reload();
        // }
        $('#frmAgregarMedicamento')[0].reset();
      },
      error: function(data) {
        console.log(data);
      }
    });
  }

  function mensaje(data) {
    Swal.fire({
      icon: 'error',
      title: 'Error:',
      text: data,
    });

  }
  var array = [];

  function enviar(nombre) {

    let excel = $("#excel" + nombre).val();
    $.ajax({
      type: 'post',
      url: "{{route('transito.serie_enfermero')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: $("#codigo_enviar" + nombre).serialize(),
      success: function(data) {
        if (excel == 1) {
          // if (data == 'ok') {
          window.location.replace("{{route('enfermeria.insumos',['id'=>$agenda->id])}}#recibir" + nombre);
          //}
          location.reload();
        } else {
          if (data == 'ok') {
            //console.log("{{route('enfermeria.insumos',['id'=>$agenda->id])}}#recibir"+nombre)
            window.location.replace("{{route('enfermeria.insumos',['id'=>$agenda->id])}}#recibir" + nombre);
            //console.log(window.location.replace("{{route('enfermeria.insumos',['id'=>$agenda->id])}}#recibir" + nombre));

            location.reload();
          } else {
            mensaje(data);
          }
        }

      },
      error: function(data) {
        console.log(data);
      }
    });
  }

  function enviar2() {
    $.ajax({
      type: 'post',
      url: "{{route('transito.serie_enfermero_equipo')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: $("#codigo_enviar").serialize(),
      success: function(data) {
        // console.log(data);
        if (data == 'No se encontraron resultados') {
          $('#agregar_respuesta').html('No se encontraron resultados');
        } else if (data == 'caducado') {
          alert('No se puede usar el medicamento, se encuentra caducado');
        } else {
          window.location.reload();
        }
        $('#codigo_enviar')[0].reset();
      },
      error: function(data) {
        console.log(data);
      }
    });
  }

  function eliminar_producto(id) {
    var r = confirm("Esta seguro de eliminar el registro?");
    if (r == true) {
      $.ajax({
        type: 'get',
        url: "{{asset('enfermeria/uso/paciente_insumo/eliminar')}}/" + id,
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        datatype: 'json',
        data: '',
        success: function(data) {
          if (data == 'ok') {
            window.location.reload();
          } else {
            alert('No se puede eliminar el registro');
          }
        },
        error: function(data) {
          console.log(data);
        }
      });
    }

  }

  function guardar_observacion() {

    $.ajax({
      type: 'post',
      url: "{{route('enfermeria.guardar_observacion')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: $("#hc_observacion").serialize(),
      success: function(data) {
        $("#alerta_datos").fadeIn(1000);
        $("#alerta_datos").fadeOut(3000);

      },
      error: function(data) {
        console.log(data);
      }
    });
  }

  function eliminar(id) {
    var r = confirm("Esta seguro de eliminar el registro?");
    if (r == true) {
      $.ajax({
        type: 'get',
        url: "{{asset('producto/uso/paciente_equipo/eliminar')}}/" + id,
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        datatype: 'json',
        data: '',
        success: function(data) {
          //console.log(data);
          window.location.reload();
        },
        error: function(data) {
          console.log(data);
        }
      });
    }
  }

  function pulsar(e) {
    tecla = (document.all) ? e.keyCode : e.which;
    return (tecla != 13);
  }

  function guardar_msn() {
    $("#alerta_datos").fadeIn(1000);
    $("#alerta_datos").fadeOut(3000);
  }

  $("#nom_plantilla").autocomplete({
    source: function(request, response) {

      $.ajax({
        url: "{{route('enfermeria.nombre_plantilla')}}",
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        data: {
          term: request.term
        },
        dataType: "json",
        type: 'post',
        success: function(data) {
          response(data);

        }
      })
    },
    minLength: 2,
    select: function(data, ui) {
      //console.log("select",ui.item.id);
      $('#id_plantilla').val(ui.item.id);
      $('#id_plantilla_2').val(ui.item.id);

    }
  });

  $("#nom_plantilla").change(function() {
    $.ajax({
      type: 'post',
      url: "{{route('enfermeria.nombre_plantilla2')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: $("#nom_plantilla"),
      success: function(data) {
        if (data != '0') {
          $('#id_plantilla').val(data.id);
          $('#id_plantilla_2').val(data.id);

        }
      },
      error: function(data) {

      }
    })
  });

  function mostrar_planilla() {
    var plantilla = document.getElementById("id_plantilla").value;
    var plantilla = document.getElementById("id_plantilla").value;
    if (plantilla=="") {
      Swal.fire({
            icon: 'error',
            title: 'Mensaje!!',
            text: 'Digite la planilla que desea agregar',
          });
        return;
    }
    $.ajax({
      type: 'get',
      url: "{{ url('insumos/plantillas/item_lista2')}}/" + plantilla + "/{{$hcid}}/1",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      success: function(data) {
        $('#detalle').empty().html(data);
        $(".oculto").show();
      },
      error: function(data) {
        console.log(data);
      }
    });
  }

  function guardar_plantilla_basica() {
    console.log($('#hc_procedimientos').val())
    console.log("aqui");
    if ($('#hc_procedimientos').val() != "") {
      Swal.fire({
        title: 'Desea aplicar el Kit Básico: ' + $('#nom_plantilla').val(),
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'Enviar',
        denyButtonText: 'No Enviar',
        showLoaderOnConfirm: true,
      }).then((result) => {

        if (result.isConfirmed) {
          $.ajax({
            type: 'post',
            url: "{{route('enfermeria.vhguardar_plantilla_basica')}}",
            headers: {
              'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $('#plantilla_basica').serialize() + "&hc_procedimientos=" + $('#hc_procedimientos').val() + "&p_hcid=" + $('#p_hcid').val(),
            success: function(data) {

              location.reload();
            },
            error: function(data) {
              console.log(data);
            }
          });

        }
      });
    } else {
      alert('Falta seleccionar procedimiento');
    }

  }
</script>

<script>
  const checkAll = (id) => {
    let checktodo = document.getElementById('checktodo' + id);
    console.log(checktodo.checked);
    let checks = document.querySelectorAll(".eliminar_producto" + id);

    if (checktodo.checked) {
      for (let i = 0; i < checks.length; i++) {
        checks[i].checked = true;
      }
    } else {
      for (let i = 0; i < checks.length; i++) {
        checks[i].checked = false;
      }
    }

  }
  const eliminarCheck = (id) => {
    let checks = document.querySelectorAll(".eliminar_producto" + id);
    let contCheck = 0;
    let contChecksExito = 0;
    let contChecksError = 0;
    for (let i = 0; i < checks.length; i++) {
      if (checks[i].checked) {
        contCheck++;
        $.ajax({
          type: 'get',
          url: "{{asset('enfermeria/uso/paciente_insumo/eliminar')}}/" + checks[i].value,
          headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
          },
          datatype: 'json',
          data: '',
          success: function(data) {
            if (data == 'ok') {
              contChecksExito++;
            } else {
              contChecksError++;
            }
          },
          error: function(data) {
            console.log(data);
          }
        });
      }
    }
    if (contCheck > 0) {
      Swal.fire({
        icon: 'success',
        title: `Exito..`,
        text: `Se eliminarion correctamente`,
      });
      setTimeout(() => {
        window.location.reload();
      }, 2500)
    }


  }
  

  const verificarChecks = (e, id) => {
    e.preventDefault();
    let respuesta = false;
    Swal.fire({
      title: 'Esta seguro que desea eliminar estos registros',
      text: "No se revertira esta accion",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si'
    }).then((result) => {
      if (result.isConfirmed) {
        eliminarCheck(id);
      }
    })

    return respuesta;
  }

  const moverInsumos = () => {
    let checks = document.querySelectorAll(".eliminar_producto");
    $.ajax({
      type: 'get',
      url: "",
      datatype: 'json',
      data: {

      },
      success: function(data) {

      },
      error: function(data) {
        console.log(data);
      }
    })
  }

  function subirexcel(req, cont) {
    let excel = $("#excel" + cont).val(1)
    var formData = new FormData();
    var uploadFiles = document.getElementById('excelsubir' + cont).files[0];
    formData.append("file", uploadFiles);
    $.ajax({
      type: 'post',
      contentType: false,
      processData: false,
      url: "{{route('enfermeria.subir_excel')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: formData,
      success: function(data) {
        for (let index = 0; index < data.array.length; index++) {
          for (let indey = 0; indey < data.array[index]['cantidad']; indey++) {
            $("#recibir" + cont).val(data.array[index]['codigo']).trigger('change');
          }
        }
      },
      error: function(data) {
        throw data;
      }
    });
  }
</script>

@endsection

{{-- clinico / protocolo / registro de anestecia / reimpresion [ protocolo y anestecia ] --}}

{{-- clinico / protocolo / anestecia [ reimpresion ] --}}

{{-- 
Fecha: 2022-03-04 18:00:00

0922729587-FAUSTOS NIVELO EDUARDO FAUSTOS --}}