<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

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

<div class="modal-content" style="width: 100%;">
  <div class="modal-header" style="padding-top: 5px; padding-bottom: 1px;">
   
    <div class="row" style="border-bottom: 1px solid black;">
      <div class="col-md-2">
        <a class="btn btn-light" data-dismiss="modal">
          <h1 style="font-size: 12px; margin:0;">
              <img style="width: 30px;height:23px" src="{{asset('/')}}hc4/img/exit.png">
              <label style="font-size: 14px">Cerrar</label>
          </h1> 
        </a>
      </div>
      <div class="col-md-6">
      </div>
      <div class="col-md-4">
        <span id="Label8" style="color:#003366;font-family:Arial;font-size:14pt;font-weight:bold;">Codigo Proceso Objetar</span>
      </div>
    </div>
  </div>
  
  <div class="modal-body">
    <div class="box-body">
      <div class="form-group col-md-6 col-xs-6">
        <label> Ingrese Codigo Proceso</label>
        <div class="col-md-9">
          <input type="number" id="codigo_{{$id}}" name="codigo" class="form-control ">
        </div>
        
      </div>
      <div class="form-group col-md-6 col-xs-6">
        
        <div class="col-md-6">
          
          <button id="guardar_agrupado" type="submit" class="btn btn-info btn-xs" onclick="guardar_objetar('{{$id}}')">GUARDAR</button>
        </div>
        
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <!--<button type="button" class="btn btn-primary"  data-dismiss="modal">Close</button>-->
  </div>
</div> 