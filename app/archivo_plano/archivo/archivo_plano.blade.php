@extends('archivo_plano.archivo.base')
@section('action-content')
<style >
    .table>tbody>tr>td{
    padding: 0;
    }
    .control-label{
        padding: 1;
        align-content: left;
        font-size: 14px;
    }
    .form-group{
        padding: 0;
        margin-bottom: 4px;
        font-size: 14px
    }
    table.dataTable thead > tr > th {
    padding-right: 10px;
    } 
    td{
        font-size: 12px;
    }

    .ui-corner-all
    {
        -moz-border-radius: 4px 4px 4px 4px;
    }

    .ui-widget
    {
        font-family: Verdana,Arial,sans-serif;
        font-size: 12px;
    }
    .ui-menu
    {
        display: block;
        float: left;
        list-style: none outside none;
        margin: 0;
        padding: 2px;
        opacity : 1;
    }
    .ui-autocomplete
    {
        opacity : 1;
        overflow-x: hidden;
        max-height: 200px;
        width:1px;
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
    .ui-menu .ui-menu-item
    {
        clear: left;
        float: left;
        margin: 0;
        padding: 0;
        width: 100%;
    }
    .ui-menu .ui-menu-item a
    {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        cursor: pointer;
        background-color: #ffffff;
    }
    .ui-menu .ui-menu-item a:hover
    {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        color: White;
        cursor: pointer;
        background-color: #006699;
    }
</style>
<section class="content">
	<div class="box">
		<div class="box-header">
		    <div class="row">
		        <div class="col-sm-8">
		          <h3 class="box-title">Generación Archivo Plano IESS</h3>
		        </div>
		    </div>
		</div>
	  	<div class="box-body">
            <form method="POST" action="{{route('planilla.genera_ap_excel')}}">
            {{ csrf_field() }}
	  		<div class="row" >
                <div class="form-group col-md-4 ">
                    <label for="mes_plano" class="col-md-4 control-label">Mes Plano:</label>
                    <div class="col-md-7">
                        <input id="mes_plano"  type="text" class="form-control input-sm" name="mes_plano">
                    </div>
                </div>
                <div class="form-group col-md-4 ">
                        <label for="seguro" class="col-md-4 control-label">Seguro:</label>
                        <div class="col-md-7">
                            <select id="seguro" name="seguro" class="form-control input-sm" >
                               @foreach($seguro as $value)
                                    @if($value->id == '2')
                                        <option  @if($seg == $value->id) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endif
                                @endforeach
                                
                            </select>
                        </div>
                </div>
                <div class="form-group col-md-4 ">
                        <label for="id_tipo_seguro" class="col-md-4 control-label">Tipo Seguro:</label>
                        <div class="col-md-7">
                            <select id="id_tipo_seguro" name="id_tipo_seguro" class="form-control input-sm" >
                                @foreach($tipo_seguros as $value)
                                    <option  @if($tipo_seg == $value->id) selected @endif value="{{$value->id}}">{{$value->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                </div>
                <div class="form-group col-md-4 ">
                        <label for="id_cobertura_comp" class="col-md-4 control-label">Cobertura Compartida:</label>
                        <div class="col-md-7">
                            <select id="id_cobertura_comp" name="id_cobertura_comp" class="form-control input-sm" >
                                <option value="">NINGUNO</option>
                                @foreach($seguros_publicos as $value)
                                    @if($value->id == '3' || $value->id == '6')
                                    <option @if($cob_compar == $value->id) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                </div>
                <div class="form-group col-md-4 ">
                        <label for="id_empresa" class="col-md-4 control-label">Empresa:</label>
                        <div class="col-md-7">
                            <select id="id_empresa" name="id_empresa" class="form-control input-sm" >
                                @foreach($empresas as $value)
                                    @if($value->id == '0992704152001' || $value->id == '1307189140001')
                                        <option @if($empresa == $value->id) selected @endif value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                </div>
                <div class="form-group col-md-2 ">                     
                        <div class="col-md-7">
                            <button type="submit" formaction="{{ route('planilla.genera_ap')}}" class="btn btn-primary" id="boton_buscar"><span class="glyphicon glyphicon-search" > Buscar</span></button>
                        </div>
                </div>
                <div class="form-group col-md-1 ">                     
                        <div class="col-md-7">
                            <button type="submit" class="btn btn-primary"> <span class="glyphicon glyphicon-download-alt"> Exportar-Iess</span></button>
                        </div>
                </div>

	  		</div>
            <div class="row">
                
                <!--<div class="form-group col-md-2 ">                     
                        <div class="col-md-7">
                            <button type="submit" formaction="#" class="btn btn-primary" id="exportar_msp"><span class="glyphicon glyphicon-download-alt"> Exportar-Msp</span></button>
                        </div>
                </div>-->
                </div>
            </form>

            <div class="table-responsive col-md-12">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="text-align: center;">
            <thead style="background-color: #4682B4">
                    <tr >
                      <!--<th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">N° Pacientes</th>-->
                      <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">Fecha de Ingreso</th>
                      <!--<th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">Tipo Seguro</th>-->
                      <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">Cédula Paciente</th>
                      <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">Nombre Paciente</th>
                      <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">Servicio</th>
                      <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">Codigo Tarifario</th>
                      <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">Descripcion</th>
                      <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">Cantidad</th>
                      <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">Valor + 10%</th>
                      <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">Parentesco</th>
                      <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">Cédula Beneficiario</th>
                      <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">Nombre Beneficiario</th>
                      <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">Porcentaje Iva</th>
                      <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">Iva Unitario</th>
                    </tr>
                  </thead>
            <tbody>
                @php $x=0; $id_temporal=0; @endphp
                @foreach($archivo_plano as $value)
                    @php
                        if($value->paciente->fecha_nacimiento==null){
                            $edad=0;
                        }else{
                            $edad=  Carbon\Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;    
                        }
                        if($value->id_paciente != $id_temporal) {
                            $id_temporal=$value->id_paciente;
                            $x++;
                        } 

                        $parentesco = substr($value->parentesco,0,1);
                        $descripcion = substr($value->descripcion,0,40);
                        $fech  = substr($value->fecha_ing, 0, 10);
                        $fech_inver = date("d/m/Y",strtotime($fech));
                    @endphp
                    <tr>
                        <!--<td>{{$x}}</td>-->
                        <td>{{$fech_inver}}</td>
                        <!--<td>{{$value->tiposeg}}</td>-->
                        <td>{{$value->id_paciente}}</td>
                        <td>{{$value->paciente->apellido1}} {{$value->paciente->apellido2}} {{$value->paciente->nombre1}} {{$value->paciente->nombre2}}</td>
                        <td>@if($value->tipo == 'P' || $value->tipo == 'AN' || $value->tipo == 'TA') {{'HME'}} @elseif($value->tipo == 'M') {{'FAR'}} @elseif($value->tipo == 'S') {{'HOSP/QUIR'}} @elseif ($value->tipo == 'IM') {{'IMA'}} @elseif($value->tipo == 'EX') {{'LAB'}} @elseif($value->tipo == 'I' || $value->tipo == 'IV' || $value->tipo == 'IF') {{'IMM'}} @elseif($value->tipo == 'PA') {{'PAQUE'}} @elseif ($value->tipo == 'EQ') {{'PRO/ESP'}} @endif</td>
                        <td>@if(($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M'))  &nbsp; @else {{$value->codigo}} @endif</td>
                        <td>{{$descripcion}}</td>
                        <td>{{$value->cantidad}}</td>
                        <td>{{round($value->valor+$value->porcentaje10,2)}}</td>
                        <td>{{$parentesco}}</td>
                        <td>{{$value->id_usuario}}</td>
                        <td>{{$value->usuario->apellido1}} {{$value->usuario->apellido2}} {{$value->usuario->nombre1}} {{$value->usuario->nombre2}}</td>
                        <td>{{$value->porcentaje_iva}}</td>
                        <td>{{round($value->iva,2)}}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
            </tfoot>
        </table>
      </div>


	  	</div>
	  </div>
</section>


<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>

<script type="text/javascript">
   

    $("#mes_plano").autocomplete({
        source: function( request, response ) {
            
            $.ajax({
                url:"{{route('search.mes_plano')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                data: {term: request.term},
                dataType: "json",
                type: 'post',
                success: function(data){
                    response(data);
                    console.log(data);

                }
            })
        },
        minLength: 2,
    } );
    
    
    /*$(document).ready(function(){

      $('#example2').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : false,
        'autoWidth'   : false
      });

    });*/
    
</script>
@endsection