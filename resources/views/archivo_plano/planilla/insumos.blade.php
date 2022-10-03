

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

@php
  $rolUsuario = Auth::user()->id_tipo_usuario;
@endphp

<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
} 
.dropdown-menu>li>a{
    color:white !important;
    padding-left: 3px !important;
    padding-right: 3px !important;
    font-size: 12px !important;
  }
 
  .dropdown-menu>li>a:hover{
    background-color:#008d4c !important;
  }
  .cot>li>a:hover{
    background-color:#00acd6 !important;
  }
</style>

<div class="modal-header" style="padding-top: 5px; padding-bottom: 1px;">
  <!--<button type="button" class="close" data-dismiss="modal">&times;</button>
  <h4 class="modal-title">Órdenes de Exámenes de Laboratorio del {{$seguro->nombre}}</h4>-->
  <div class="row" style="border-bottom: 1px solid black;">
    <div class="col-md-2">
      <a class="btn btn-light" data-dismiss="modal">
        <h1 style="font-size: 12px; margin:0;">
            <img style="width: 30px;height:23px" src="{{asset('/')}}hc4/img/exit.png">
            <label style="font-size: 14px">Cerrar</label>
        </h1> 
      </a>
    </div>
    <div class="col-md-4">
    </div>
    <div class="col-md-6">
      <span id="Label8" style="color:#003366;font-family:Arial;font-size:14pt;font-weight:bold;">Insumos utilizados en el procedimiento {{$seguro->nombre}}</span>
    </div>
  </div>
</div>
<div class="modal-body">
  <form id="form_labs">
    {{ csrf_field() }}
    <input type="hidden" name="id" value="{{$ap->id}}">
    <div class="form-group col-md-4 col-xs-6">
      <label for="fecha" class="col-md-3 control-label">Desde</label>
      <div class="col-md-9">
        <div class="input-group date">
          <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </div>
          <input type="text" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off">
          <div class="input-group-addon">
            <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar_labs();"></i>
          </div>   
        </div>
      </div>  
    </div>
    <div class="form-group col-md-4 col-xs-6">
      <label for="fecha_hasta" class="col-md-3 control-label">Hasta</label>
      <div class="col-md-9">
        <div class="input-group date">
          <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </div>
          <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
          <div class="input-group-addon">
            <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar_labs();"></i>
          </div>   
        </div>
      </div>  
    </div>       
  </form>
  <button type="button" class="btn btn-primary" id="boton_buscar">
    <span class="glyphicon glyphicon-search" aria-hidden="true"></span> 
  </button>
  <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap" >
    <div class="row" id="listado">
      <div class="table-responsive col-md-12" style="min-height: 210px;">
        <table id="example2_labs" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;overflow: none;">
          <thead>
            <tr role="row">
              <th width="10">Sel.</th>
              <th width="10">Fecha</th>
              <th width="10">Descripcion</th>
              <th width="10">Cant.</th>
              <th width="10">Valor</th>               
            </tr>
          </thead>
          <tbody>
            <tr role="row">
              <td style="font-size: 11px;"><button type="button" class="btn btn-xs btn-success" onclick="seleccionar_insumo('1','{{$ap->id}}')"><span class="glyphicon glyphicon-ok-sign"></span></button></td>
              <th width="10">{{substr($ap->fecha_ing,0,10)}}</th>
              <th width="10">HILO GUIA HYDRA 0.035</th>
              <th width="10">1</th>
              <th width="10">$ 15,00</th>               
            </tr>
            <tr role="row">
              <td style="font-size: 11px;"><button type="button" class="btn btn-xs btn-success" onclick="seleccionar_insumo('2','{{$ap->id}}')"><span class="glyphicon glyphicon-ok-sign"></span></button></td>
              <th width="10">{{substr($ap->fecha_ing,0,10)}}</th>
              <th width="10">SPYSCOPE DIGITAL</th>
              <th width="10">1</th>
              <th width="10">$ 75,00</th>               
            </tr>
            <tr role="row">
              <td style="font-size: 11px;"><button type="button" class="btn btn-xs btn-success" onclick="seleccionar_insumo('3','{{$ap->id}}')"><span class="glyphicon glyphicon-ok-sign"></span></button></td>
              <th width="10">{{substr($ap->fecha_ing,0,10)}}</th>
              <th width="10">PROPOFOL</th>
              <th width="10">50</th>
              <th width="10">$ 34,00</th>               
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<div class="modal-footer">
  <!--<button type="button" class="btn btn-primary" id="laboratorio_cerrar" data-dismiss="modal">Close</button>-->
</div>
   
  
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>

<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">

  $(document).ready(function($){

    @foreach ($ordenes as $value)

      $.ajax({
        type: 'get',
        url:"{{ route('resultados.puede_imprimir',['id' => $value->id]) }}", 
        
        success: function(data){
          
            if(data.cant_par==0){
              var pct = 0;  
            }else{
              var pct = data.certificados/data.cant_par*100;  
            }
            //alert(pct);
            $('#td{{$value->id}}').css("width", Math.round(pct)+"%");
            $('#sp{{$value->id}}').text(Math.round(pct)+"%");
            if(pct < 10){
              $('#td{{$value->id}}').addClass("progress-bar-danger");
            }else if(pct >=10 && pct<90){
              $('#td{{$value->id}}').addClass("progress-bar-warning");  
            }else{
              $('#td{{$value->id}}').addClass("progress-bar-success");
            }
          

        },


        error: function(data){
          
           
        }
      });

    @endforeach

    $('#fecha').datetimepicker({
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fecha}}',
    });
        
    $('#fecha_hasta').datetimepicker({
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fecha_hasta}}',
    });

    $("#fecha").on("dp.change", function (e) {
        buscar_labs();
    });

     $("#fecha_hasta").on("dp.change", function (e) {
        buscar_labs();
    });

    $('#example2_labs').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })



  });

   $('#doctor').on('hidden.bs.modal', function(){
                location.reload();
                $(this).removeData('bs.modal');
            }); 


  
  function descargar(id_or){
    var cert = $('#sp'+id_or).text();
    if(cert=='0%'){
      alert("Sin Exámenes Ingresados");
    }else{
      //location.href = '{{url('resultados/imprimir')}}/'+id_or;
      window.open('{{url('resultados/imprimir')}}/'+id_or,'_blank');  
    }
    
  }
  function buscar_labs(){
        $.ajax({
          type: 'post',
          url:"{{ route('planilla.buscar_labs') }}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#form_labs").serialize(),
          success: function(data){
            
            console.log(data);
            $('#listado').empty().html(data);
          },
          error: function(data){
             console.log(data);
          }
        });
    }

</script>  

