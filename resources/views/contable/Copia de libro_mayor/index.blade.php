@extends('contable.libro_mayor.base')
@section('action-content')

<style>
p.s1 {
  margin-left:  10px;
  font-size:    14px;
  font-weight:  bold;
} 
p.s2 {
  margin-left:  20px;
  font-size:    12px;
  font-weight:  bold;
} 
p.s3 {
  margin-left:  30px;
  font-size:    10px;
  font-weight:  bold;
} 
p.s4 {
  margin-left:  40px;
  font-size:    10px;
} 
p.t1 { 
  font-size:    14px;
  font-weight:  bold;
} 
p.t2 { 
  font-size:    12px;
  font-weight:  bold;
} 
p.t3 { 
  font-size:    10px;
}
.table-condensed>thead>tr>th>td, .table-condensed>tbody>tr>th>td, .table-condensed>tfoot>tr>th>td, .table-condensed>thead>tr>td, .table-condensed>tbody>tr>td, .table-condensed>tfoot>tr>td {
  padding: 1 px;
  line-height: 1;
}
</style>

<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
  <!-- Main content -->
  <section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item"><a href="#">Contabilidad</a></li> 
      <li class="breadcrumb-item"><a href="../">Libro mayor</a></li> 
    </ol>
  </nav>
    <div class="box" style=" background-color: white;">
        <!-- <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">
            <div class="col-md-6">
              <h3 class="box-title">Criterios de búsqueda</h3>
            </div>
        </div> -->

        <div class="row head-title">
          <div class="col-md-12 cabecera">
              <label class="color_texto" for="title">BUSCADOR</label>
          </div>
        </div>

      <!-- /.box-header -->
      <div class="box-body dobra">
        <form method="POST" id="reporte_master" action="{{ route('libro_mayor.index') }}" >
        {{ csrf_field() }}

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-4 control-label">Número de Cuenta:</label>
          <div class="col-md-9">
              <select id="cuenta" name="cuenta[]"  class="form-control select2_cuentas" style="width: 100%;" multiple="multiple">
                  <option> </option>
                  @foreach($scuentas as $value)
                      <option value="{{$value->id}}">{{$value->id}}</option>
                  @endforeach
              </select>
          </div>
        </div>

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-4 control-label">Nombe:</label>
          <div class="col-md-9">
              <select id="nombre" name="nombre[]"  class="form-control select2_cuentas" style="width: 100%;" multiple="multiple">
                  <option> </option>
                  @foreach($scuentas as $value)
                      <option value="{{$value->id}}">{{$value->nombre}}</option>
                  @endforeach
              </select>
          </div>
        </div>

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-4 control-label">Fecha desde:</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha" id="fecha" value="@if(@$fecha) {{@$fecha}}@endif" required  autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha_hasta" class="texto col-md-4 control-label">Fecha hasta:</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" value="@if(@$fecha_hasta) {{@$fecha_hasta}}@endif" required autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
            <!-- <button type="submit" class="btn btn-primary btn-sm" id="boton_buscar">
              <span class="glyphicon glyphicon-search" aria-hidden="true" style="font-size: 16px">&nbsp;Buscar&nbsp;</span>
            </button>
             <button type="button" class="btn btn-primary btn-sm" id="btn_imprimir" name="btn_imprimir">
              <span class="glyphicon glyphicon-print" aria-hidden="true" style="font-size: 16px">&nbsp;Imprimir&nbsp;</span>
            </button> -->
            <button type="submit" class="btn btn-primary" id="boton_buscar">
                  <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
            </button>
            <button type="button" class="btn btn-primary" id="btn_imprimir">
                  <span class="glyphicon glyphicon-print" aria-hidden="true"></span> Imprimir
            </button> 
            <button type="button" class="btn btn-primary" id="btn_exportar">
              <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
            </button> 
        </div>

      </form>
     
      </div>
      <!-- /.box-body -->

      @if(count(@$cuentas)>0)

        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
          <div class="row">
            <div class="col-md-12">
              <div class="box box-solid">
                <div class="box-header with-border">
                </div>
                <!-- /.box-header --> 

                <div class="box-body">
                  <div class="col-md-1">
                    <dl> 
                      <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa"></dd>
                      {{-- <dd>&nbsp; {{$empresa->nombrecomercial}}</dd> --}}
                    </dl>
                  </div>
                  <div class="col-md-3">
                    <dl> 
                      <dd><strong>{{$empresa->nombrecomercial}}</strong></dd>
                      <dd>&nbsp; {{$empresa->id}}</dd> 
                    </dl>
                  </div>
                  <div class="col-md-4"> 
                    <h4>Mayor de cuenta</h4>
                    <h5>del {{$fecha}} al {{$fecha_hasta}} </h5>
                  </div>
                  <div class="col-md-4"> 
                  </div>  
              </div>
                <!-- /.box-body -->
              </div>
              <!-- /.box -->
            </div>
          </div> 
          @foreach($cuentas as $cuenta)
          
            @php
            //echo ($cuenta);
              $registros = \Sis_medico\Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $cuenta->id.'%')
              ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
              ->whereBetween('ct_asientos_detalle.fecha', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:59'])
              ->where('c.id_empresa', $id_empresa)
              ->get(); 

               $saldo = 0;
            @endphp
            
          @if(count($registros)>0)
            <div class="row">
              <div class="table-responsive col-md-12">
                <h4>Cuenta: {{$cuenta->nombre}}</h4>
                <table id="example2" class="table table-condensed table-striped" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                  <thead>
                    <tr class='well-dark'>
                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha</th>
                      <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Asiento</th>
                      <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cuenta</th>
                      <th width="40%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Detalle</th>
                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor</th>
                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Debe</th>
                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Haber</th>
                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Saldo</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($registros as $value)
                      @php
                         $saldo = ($saldo +$value->debe) - $value->haber;
                         if($value->debe!=0){ $valor = $value->debe; }else{ $valor = $value->haber; }
                      @endphp
                      <tr>
                        <td>{{$value->fecha}}</td>
                        <td>{{$value->cabecera->id}}</td>
                        <td>{{$value->id_plan_cuenta}}</td>
                        <td>Cuenta: {{$value->descripcion}} / Detalle: {{$value->cabecera->observacion}}</td>
                        <td>$ {{$valor}}</td>
                        <td>$ {{$value->debe}}</td>
                        <td>$ {{$value->haber}}</td>
                        <td @if($saldo < 0) style="color:red;" @endif >$ {{$saldo}}</td>
                      </tr>
                      @endforeach 
                      <tr>
                        <td colspan="3">{{$value->id_plan_cuenta}}</td>
                        <td colspan="4">{{$cuenta->nombre}}</td>
                        <td colspan="3"></td>
                        <td @if($saldo < 0) style="color:red;" @endif >$ {{$saldo}}</td>
                      </tr>
                  </tbody>
                </table>
              </div>
            </div>
          @endif
        
        <br>
        @endforeach 

      @endif

    </div>
  </section>

  <form method="POST" id="print_reporte_master" action="{{ route('libro_mayor.index') }}" target="_blank">
          {{ csrf_field() }}
        <input type="hidden" name="filfecha" id="filfecha_desde" value="{{$fecha}}">
        <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{$fecha_hasta}}">
        <input type="hidden" name="filcuenta" id="filcuenta" value="">
        <input type="hidden" name="exportar" id="exportar" value="0">
        <input type="hidden" name="imprimir" id="imprimir" value="">
  </form>
  <!-- /.content -->
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script type="text/javascript">

    $(document).ready(function(){
            $('.select2_cuentas').select2({
                tags: false
              });
          });

    $('#seguimiento').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
            });


    $('#cuenta').on('select2:select', function (e) {
        var cuenta = $('#cuenta').val();
        $('#nombre').val(cuenta);
        $('#nombre').select2().trigger('change');
      });


    $('#nombre').on('select2:select', function (e) {
        var nombre = $('#nombre').val();
        $('#cuenta').val(nombre);
        $('#cuenta').select2().trigger('change');
      });

    $( "#btn_imprimir" ).click(function() { 
      $( "#print_reporte_master" ).submit();
      // document.getElementById("print_reporte_master").submit(); 
    });

    $(document).ready(function(){


    });

    $(function () {

      var selectedValues = new Array();
      selectedValues[0] = "1";
      selectedValues[1] = "1.01.01.1"; 

        $('#fecha').datetimepicker({
            format: 'DD/MM/YYYY',
            // defaultDate: '{{$fecha}}',
            });
        $('#fecha_hasta').datetimepicker({
            format: 'DD/MM/YYYY',
            // defaultDate: '{{$fecha_hasta}}',

            });
        $("#fecha").on("dp.change", function (e) {
            verifica_fechas();
        });

         $("#fecha_hasta").on("dp.change", function (e) {
            verifica_fechas();
        }); 

        // $('#cuenta').val(['1', '1.01.01.1']);
        // $('#cuenta').trigger('change');
        @php 
            $values = "";    $coma = "";
        @endphp

        
        
        @if(@$filcuenta!='[]')
          @foreach(@$filcuenta as $value)
          
          @php 
            $value=trim($value);
            $values .= "$coma '$value'";   $coma = ",";
          @endphp
          
          @endforeach  
        @endif
        
        
        @if(@$values!='') 
          $('#cuenta').val(@php echo "[$values]" @endphp );
          $('#cuenta').select2().trigger('change');

          $('#nombre').val(@php echo "[$values]" @endphp);
          $('#nombre').select2().trigger('change');
        @endif
        /* $('#cuenta').val('');
        $('#cuenta').select2().trigger('change');

        $('#nombre').val('');
        $('#nombre').select2().trigger('change');
        */
  });
  function buscar()
  {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }  
  function verifica_fechas(){
    if(Date.parse($("#fecha").val()) > Date.parse($("#fecha_hasta").val())){
      Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Verifique el rango de fechas y vuelva consultar'
      });
    } 
  }
  /*function imprimir(){ alert("imprimir");
    $( "print_reporte_master" ).submit();
  }*/
  $( "#btn_exportar").click(function() { 
        $("#filfecha_desde").val($("#fecha").val());
        $("#filfecha_hasta").val($("#fecha_hasta").val());
        $("#filcuenta").val($("#cuenta").val());
        
        $("#exportar").val(1);  
        $("#print_reporte_master" ).submit();
    });
</script>
@endsection
