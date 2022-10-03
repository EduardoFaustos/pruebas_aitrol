
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<style type="text/css">
    
    .select2-container--default .select2-results__option[aria-disabled=true] {
        display: none;
    }
     
    .boton-proce{
      font-size: 15px ;
      width: 20%;
      background-color: #004AC1; 
      color: white;
      text-align: center;
      height: 35px;
      padding-left: 5px;
      padding-right: 5px;
      padding-bottom: 0px;
      padding-top: 7px; 
      margin-bottom: 5px; 
    } 
     
    .parent{
     height: 462px;
    }
    .titulo{
    font-family: 'Helvetica general' !important;
    font-family: 'Monserrat-Bold' !important;
    border-bottom:  solid 1px #004AC1 !important;
   }

</style>

<div class="box-header">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-2" style=" text-align: center;">
        <b>FARMACIA</b>
        </div>
        <div class="col-md-10" style="border-bottom-style: dashed; border-bottom-width: 3px; margin-bottom: 12px; opacity: : 0.7;">
        </div>
      </div>
    </div>
  
  </div>

<section style=" margin-left: 4px;margin-right: 4px;">  
<div style="border: 2px solid #004AC1;border-radius:8px;margin-left: 4px;margin-right: 4px;">

    <div class="col-md-12" style="padding-left: 0px; padding-right: 0px">
      <div class="row">
       <div class="col-md-3">
         <label style="margin:50px ">
           LISTA DE PRODUCTO
         </label>
       </div>
       <div class="col-md-9">
         <div class="col-md-12">
           <button class="btn btn-primary boton-proce" style="border-radius: 10px; width: 200px; margin: 20px" id="boton" onclick="guardar_procedimiento();">Marcas
            </button>

            <button class="btn btn-primary boton-proce" style="border-radius: 10px; width: 200px; margin: 20px" id="boton" onclick="guardar_procedimiento();">Tipo de productos
            </button>

            <button class="btn btn-primary boton-proce" style="border-radius: 10px; width: 200px; margin: 20px" id="boton" onclick="agregarp();">Agregar productos
            </button>

            <button class="btn btn-primary boton-proce" style="border-radius: 10px; width: 200px; margin: 20px" id="boton" onclick="guardar_procedimiento();">Ingresos de productos
            </button>
         </div>
         <div class="col-md-12" style="margin-left: 40px">
            <button class="btn btn-primary boton-proce" style="border-radius: 10px; width: 200px; margin: 20px" id="boton" onclick="guardar_procedimiento();">Pedidos realizados
            </button>

            <button class="btn btn-primary boton-proce" style="border-radius: 10px; width: 200px; margin: 20px" id="boton" onclick="guardar_procedimiento();">En transito 
            </button>

            <button class="btn btn-primary boton-proce" style="border-radius: 10px; width: 200px; margin: 20px" id="boton" onclick="guardar_procedimiento();">Descargue reporte
            </button>
         </div>
       </div>
           
      </div>
    </div>  

</div>
<div class="container-fluid" id="info" style="padding-left: 0px; padding-right: 0px">      
    <div class="col-md-12" style=" color: white; margin-top: 5px; padding: 8px; border-radius: 30px; background-image: linear-gradient(to right, #004AC1,#004AC1,#004AC1); margin-bottom: 10px">   
      <form method="POST" id="form_buscador" action="">
        {{ csrf_field() }}
        <div class="row">

          <div class="col-md-12 col-sm-12 col-12"> 
            <div class="col-md-12">
              <div class="row">
                <div class="col-md-5">
                    <h1 class="col-md-10" style="font-size: 15px; margin: 0px; padding: 15px;">
                      <b>RESULTADOS DE LA B&Uacute;SQUEDA
                      </b>
                    </h1> 
                </div>
                <div class="col-md-5" style="margin-top: 5px">
                   <input required maxlength="30" placeholder="CODIGO" style=" width: 250px; height: 40px; text-align: center; border: #004AC1 2px solid; border-radius: 30px;">
               
               
                  <input required maxlength="30" placeholder="APELLIDOS" style=" width: 250px; height: 40px; text-align: center; border: #004AC1 2px solid; border-radius: 30px;">
                </div>
                <div class="col-md-2" style="margin-top: 5px">
                  <button type="button" style=" background-color: #004AC1; margin: 2px;color: white; text-align: center; padding: 10px; border-radius: 30px; margin-bottom: 15px;height:40px; width: 150px;">
                        <img src="{{asset('/')}}hc4/img/busqueda.png" style="width: 20px; text-align:right ;"> &nbsp;&nbsp;<b>BUSCAR</b>
                         </button>
                </div>

            
              <!-- IMAGEN 
              <img style="width: 49px;" src="{{asset('/')}}hc4/img/hc_ima.png"> -->
                
            </div>
            </div>
             
          </div>
        </div>
      </form>


      
    </div>


  </div>
  <div id="area_cambiar2" class="col-md-12" style = "border: 2px solid #004AC1; height: 900px;">
    
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="
         margin-right: 850px;">
            <thead>
              <tr role="row">
                <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Codigo</th>
                <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Marca</th>
                <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Nombre</th>
                <th  width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Cantidad</th>
                <th  width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Stock</th>
                <th  width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Accion</th>
              </tr>
            </thead>
            <tbody>
                <tr role="row" class="odd">
                  <td>Anthony Chilán</td>
                  <td>Desarrollo</td>
                  <td>Activado</td> 
                  <td></td> 
                  <td></td> 
                  <td><a type="button" class="btn btn-primary">Ver</a></td>
                </tr>
                <tr role="row" class="odd">
                  <td>Miguel Poveda</td>
                  <td>Desarrollo</td>
                  <td>Activado</td> 
                  <td></td> 
                  <td></td> 
                  <td><a type="button" class="btn btn-primary">Ver</a></td>
                </tr>
                <tr role="row" class="odd">
                  <td>Fausto Javier</td>
                  <td>Desarrollo</td>
                  <td>Activado</td> 
                  <td></td> 
                  <td></td> 
                  <td><a type="button" class="btn btn-primary">Ver</a></td>
                </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>  
    </div> 

    <script type="text/javascript">
  $("#fecha").change(function(){
    alert($("#fecha").val());
  });
  function enviar_enter(e){
    //alert('entra1');
      tecla = (document.all) ? e.keyCode : e.which;
      if (tecla==13){
        buscador_paciente_fecha();
      };
  }
  function cambio_fecha(){
    alert('cambio');
  }


</script>
<script type="text/javascript" src="{{ asset ("/librerias/moment.min.js")}}"></script>
<script type="text/javascript" src="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.js")}}"></script>
<script type="text/javascript">
    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })
  </script>

  <script type="text/javascript">
    function agregarp(){
      $.ajax({
        type: "GET",
        url: "{{route('hc4.agregarp')}}", 
        data: "",
        datatype: "html",
        success: function(datahtml){
          $("#area_cambiar").html(datahtml);
          
          
        },
        error:  function(){
          alert('error al cargar');
        }
      });
    }
</script>    

</section>

