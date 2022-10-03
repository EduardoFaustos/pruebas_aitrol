<script src="{{ asset ("/js/jquery-ui.js")}}"></script>

<style type="text/css">
    @media screen and (max-width: 1500px) {
      label#peri.control-label {
        font-size: 13px;
      } 
    }
    
    .table>tbody>tr>td, .table>tbody>tr>th {
      padding: 0.4% ;
    } 

    .ui-corner-all 
    {
      -moz-border-radius: 4px 4px 4px 4px;
    }
       
    .ui-widget
    {
      font-family: Verdana,Arial,sans-serif;
      font-size: 15px;
    }
    
    .ui-menu
    {
      display: block;
      float: left;
      list-style: none outside none;
      margin: 0;
      padding: 2px;
    }
      
    .ui-autocomplete
    {
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
      _width: 160px;
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
        
    .ui-widget-content a
    {
      color: #222222; 
    }

    .mce-edit-focus,
    .mce-content-body:hover {
            outline: 2px solid #2276d2 !important;
    }

    .select2-selection--multiple{
      background-color: white !important;
    }
   
    .centered{
      text-align: center;
    }

    .select2-selection__choice{
     background-color: red !important;
     border-color: red !important;
    }
  
</style>

<div class="box " style="border: 2px solid #004AC1; background-color: white;">
  <div class="box-header with-border" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004AC1; ">
    <div class="row">
      <div class="col-md-9">
        <h1 style="font-size: 15px; margin:0; background-color: #004AC1; color: white;" >
              <img style="width: 35px; margin-left: 5px; margin-bottom: 5px" src="{{asset('/')}}hc4/img/iconos/receta.png"> 
              <b>NUEVA RECETA</b>
        </h1>   
      </div>
    </div>
    @if(!is_null($paciente)) 
      <center> 
        <div class="col-12" style="padding-bottom: 10px;">
          <h1 style="font-size: 14px; margin:0; background-color: #004AC1; color: white;padding-left: 20px" >
          <b>PACIENTE : {{$paciente->apellido1}} {{$paciente->apellido2}}
                    {{$paciente->nombre1}} {{$paciente->nombre2}}
          </b>
          </h1>
        </div> 
      </center>
    @endif  
  </div>

  <div class="box-body" style="background-color: #56ABE3" >
    <div class="box" style="border: 2px solid #004AC1;border-radius: 10px;background-color: white;font-size: 13px;font-family: Helvetica;margin-bottom: 10px;margin-top: 0px;padding-left: 20px;padding-right: 20px;padding-top: 20px;padding-bottom: 20px;">
        <input type="hidden" name="id_paciente" id="id_paciente" value="{{$paciente->id}}">
        <div class="col-12" >

          <div class="col-9">
            <div class="row">
              <div class="col-6" style="text-align: right;" >
                  <label style="font-family: 'Helvetica general';" >Seguro:</label>
              </div>
              <div class="col-6">
                @if(!is_null($hc_receta->nombre_seguro))
                  {{$hc_receta->nombre_seguro}} 
                @endif 
              </div>
            </div>
          </div>

          <div class="form-group">
            <label style="font-family: 'Helvetica general';" for="inputid" class="control-label">Medicina</label>
            <div class="row"> 
              <div class="col-10">
                <input value="" type="text" class="form-control" name="nombre_generico" id="nombre_generico" placeholder="Nombre">
              </div>&nbsp;&nbsp;&nbsp;
              <div class="centered">
                <button id="limpiar" class="btn btn-primary" style="background-color: #004AC1;"
                  onClick="buscar_nombre_medicina()">
                  <span class="fa fa-plus"></span> Agregar
                </button>
              </div>
            </div>
          </div> 
        </div>                        
      <div style="font-family: 'Helvetica general';" class="col-md-1">Alergias:</div>
      <div class="col-md-12" style="margin-bottom: 10px">
        @if($alergiasxpac->count()==0) 
          <b>NO TIENE </b>
        @else 
          @foreach($alergiasxpac  as $ale)<span style="margin-bottom: 20px; padding-left: 10px; padding-right: 10px; border-radius: 5px;background-color: red;color: white"> {{$ale->principio_activo->nombre}}</span>&nbsp;&nbsp;
          @endforeach 
        @endif
      </div>
      <div id="index">
      </div>                
      <form id="final_receta" method="POST">
      <input type="hidden" name="id_receta" value="{{$hc_receta->id}}"> 
        <div class="col-md-12">
          <div class="row">
            <div class="col-md-6">
              <span><b style="font-family: 'Helvetica general';" class="box-title">Rp</b></span>
              <div id="trp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>" style="border: solid 1px;min-height: 200px;border-radius:3px;border: 2px solid #004AC1;"> 
                <?php if(!is_null($hc_receta)): ?>
                  <?php echo $hc_receta->rp ?>
                <?php endif; ?>
              </div>
              <input type="hidden" name="rp" id="rp<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>">
            </div>
            <div class="col-md-6" >
              <span><b style="font-family: 'Helvetica general';" class="box-title">Prescripcion</b></span>
              <div id="tprescripcion<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>" style="border: solid 1px;min-height: 200px;border-radius:3px;border: 2px solid #004AC1;">
                  <?php if(!is_null($hc_receta)): ?>        
                    <?php echo $hc_receta->prescripcion ?>
                  <?php endif; ?>
              </div> 
              <input type="hidden" name="prescripcion" id="prescripcion<?php echo e($hc_receta->id); ?><?php echo e(date('his')); ?>"> 
            </div>
          </div>
          <br>
          <div class="centered">
            <button type="button" class="btn btn-primary" onClick="#" style="background-color: #004AC1;">
              <span class="fa fa-floppy-o"></span>&nbsp;&nbsp;Guardar
            </button>
          </div>     
        </div>
      </form>
</div>

<script type="text/javascript">


  
</script>
