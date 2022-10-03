<div class="container-fluid" id="area_cambiar">
  <div class="box-header">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-2" style="font-family: Helvetica; text-align: center;">
        <b>GESTI&Oacute;N DE CUARTO</b>
        </div>
        <div class="col-md-10" style="border-bottom-style: dashed; border-bottom-width: 3px; margin-bottom: 12px; opacity: : 0.7;">
        </div>
      </div>
    </div>
  
  </div>
  <div class="box-body">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-4" style="font-family: Helvetica; font-weight: bold; text-align: left;">
          <b>ESTADO DEL CUARTO</b>          
          <div class="col-md-6" style="text-align: left;  right: 15px;">
            <div class="row">
              <hr style="height: 2px; background-color: black; ">
              <div class="col-md-3" style="width: 40px; height: 40px;border: 1px solid #555; background: #00b034;">
              </div>
              <div class="col-md-9" style=" left: 10px; font-size: 15px; padding: 10px; font-family: Helvetica; font-weight: bold; text-align: left;">
                <b>LIBRES</b>
              </div>
              <div class="col-md-3" style="width: 40px; height: 40px;border: 1px solid #555; background: #face03; top: 3px;">
              </div>
             <div class="col-md-9" style="left: 10px; font-size: 15px; padding: 10px; font-family: Helvetica; font-weight: bold; text-align: left;">
              <b>PREPARACIÓN</b>
             </div>
             <div class="col-md-3" style="width: 40px; height: 40px;border: 1px solid #555; background: #d31e02; top: 6px;">  
             </div>
             <div class="col-md-9" style=" left: 10px;font-size: 15px; padding: 10px; font-family: Helvetica; font-weight: bold; text-align: left;">
              <b>OCUPADAS</b>
             </div>
             <div class="col-md-3" style="width: 40px; height: 40px;border: 1px solid #555; background: #9a9a9a; top: 9px;">     
             </div>
             <div class="col-md-9" style="left: 10px; font-size: 15px; padding: 10px; font-family: Helvetica; font-weight: bold; text-align: left;">
              <b>NO DISPONIBLE</b>
             </div>
             <!---Empieza el otro lado -->
            
             <div class="col-md-12" style="top: 10px;">
              <b>TIPO DE CUARTO</b>
              <hr style="height: 2px; background-color: black;"> 
             </div>
            <div class="col-md-3" style="width: 40px; height: 40px;border: 1px solid #555;">
              <img src="{{asset('/')}}hc4/img/simple_block.png" style="width: 20px;">
            </div>
            <div class="col-md-3" style="right: 3px;">
              <b>SIMPLE</b>
            </div>
             <div class="col-md-3" style="width: 40px; height: 40px;border: 1px solid #555;">
              <img src="{{asset('/')}}hc4/img/Doble_Bloqueda.png" style="width: 40px;">
            </div>
            <div class="col-md-3">
              <b>DOBLES</b>
            </div>
            <div class="col-md-3" style="top: 6px; width: 40px; height: 40px;border: 1px solid #555;">
              <img src="{{asset('/')}}hc4/img/Suite.png" style="width: 40px;">
            </div>
            <div class="col-md-3" style="top: 6px;">
              <b>SUITE</b>
            </div>
             <div class="col-md-3" style="top: 6px; width: 40px; height: 40px; border: 1px solid #555;">
              <img src="{{asset('/')}}hc4/img/Triple_Bloqueada.png" style="width: 50px;">
            </div>
            <div class="col-md-3" style="top: 6px;">
              <b>TRIPLE</b>
            </div>
             <div class="col-md-3" style="top: 12px; width: 40px; height: 40px;border: 1px solid #555;">
              <img src="{{asset('/')}}hc4/img/Ejecutiva_Bloqueada.png" style="width: 35px;">
            </div>
            <div class="col-md-3" style="top: 15px;">
              <b>EJECUTIVA</b>
            </div>
           
          
       
            </div>
          </div>
        </div>
        <!-- Contedor del cuadrado  -->
        <div class="col-md-8" style = "text-align: left; border: 2px solid #004AC1; height: 950px;">
            <div class="col-md-12">
                <div class="row">
                  <div class="col-md-5" style="font-family: Helvetica;">
                    <select name="estado" class="form-control col-md-4" style="left: 300px; top: 20px;">
                                    <option value="">TIPO DE HABITACI&Oacute;N</option>
                                    <option>SIMPLE</option>
                                    <option >SUITE</option>
                                    <option>DOBLE</option>
                                    <option>TRIPLE</option>
                    </select>
                    
                  </div>
                  <div class="col-md-5" style="font-family: Helvetica;">
                    <select name="estado" class="form-control col-md-4" style="top: 20px;">
                                    <option value="">PISO</option>
                                    <option>PISO 1</option>
                                    <option >PISO 2</option>
                                    <option>PISO 3</option>
                    </select>
                  </div>
                  <div class="col-md-2" style="right:220px; font-family: Helvetica; color: white; font-weight: bold;">
                    <a class="btn btn-primary boton-proce" style="background-color: #004AC1; border-radius: 10px; width: 200px; margin: 18px;" >FILTRAR</a>
                  </div>
                </div>
            </div>
            <div class="col-md-12">
              <div class="row">
                <div class="col-md-12">
                  <div class="col-md-12" style="font-family: Helvetica;">
                    <b>PISO 1</b>
                  </div>                  
                  <img src="{{asset('/')}}hc4/img/1.png" style="width: 100px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/2.png" style="width: 70px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/3.png" style="width: 70px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/4.png" style="width: 70px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/5.png" style="width: 70px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/1.png" style="width: 100px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/2.png" style="width: 70px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/3.png" style="width: 70px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/4.png" style="width: 70px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/5.png" style="width: 70px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/11.png" style="width: 70px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/12.png" style="width: 100px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/13.png" style="width: 70px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/1.png" style="width: 70px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/2-2.png" style="width: 70px; margin-left: 10px; margin-top: 10px;">
                  <img src="{{asset('/')}}hc4/img/2-3.png" style="width: 70px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/2-4.png" style="width: 70px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/13.png" style="width: 70px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/2-8.png" style="width: 70px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/11.png" style="width: 70px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/12.png" style="width: 100px; margin-left: 10px;">
                  <div class="col-md-12">
                    <b>PISO 2</b>
                  </div>
                  <img src="{{asset('/')}}hc4/img/12.png" style="width: 100px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/1.png" style="width: 100px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/2.png" style="width: 70px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/3.png" style="width: 70px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/4.png" style="width: 70px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/5.png" style="width: 70px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/1.png" style="width: 100px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/2.png" style="width: 70px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/3.png" style="width: 70px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/4.png" style="width: 70px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/5.png" style="width: 70px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/11.png" style="width: 70px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/12.png" style="width: 100px; margin-left: 10px;">
                  <img src="{{asset('/')}}hc4/img/13.png" style="width: 70px; margin-left: 10px;"> 
                </div>
              </div>
            </div>
        </div>
        <!---termina aqui--->
      </div>
    </div>
  
  </div>

</div>
<script type="text/javascript" src="{{ asset ("/librerias/moment.min.js")}}"></script>
<script type="text/javascript" src="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.js")}}"></script>