<div class="container-fluid" id="area_cambiar">
  <div class="box-header">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-2" style="font-family: Helvetica; text-align: center;">
        <b>FARMACIA</b>
        </div>
        <div class="col-md-10" style="border-bottom-style: dashed; border-bottom-width: 3px; margin-bottom: 12px; opacity: : 0.7;">
        </div>
      </div>
    </div>
  
  </div>
  <div class="box-body">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-12" style = "text-align: left; border-radius: 10px; border: 2px solid #BFC9CA; height: 350px;">
	        <div class="row">
	        	<!--Etiqueta nnumero 1 --->
			  <div class="col-md-4">
			  	<div class="row">
			  		<div class="col-md-6" style="padding-top: 20px">Codigo</div>
			  		<div class="col-md-6"><input type="text" name="codigo" required maxlength="10" style="border-radius: 10px; border: 1px solid #BFC9CA; margin-top: 20px"></div>

			  		<div class="col-md-6" style="padding-top: 20px">Cantidad de medidas</div>
			  		<div class="col-md-6"><input type="text" name="cantmedi" required maxlength="10" style="border-radius: 10px; border: 1px solid #BFC9CA; margin-top: 20px"></div>

			  		<div class="col-md-6" style="padding-top: 20px">Registro Sanitario</div>
			  		<div class="col-md-6"><input type="text" name="registro" required maxlength="10" style="border-radius: 10px; border: 1px solid #BFC9CA; margin-top: 20px"></div>

			  		<div class="col-md-6" style="padding-top: 20px">Laboratorio</div>
			  		<div class="col-md-6"><input type="text" name="laboratorio" required maxlength="10" style="border-radius: 10px; border: 1px solid #BFC9CA; margin-top: 20px"></div>

			  		<div class="col-md-6" style="padding-top: 20px">Cantidad de Usos</div>
			  		<div class="col-md-6"><input type="text" name="cantusos" required maxlength="10" style="border-radius: 10px; border: 1px solid #BFC9CA; margin-top: 20px"></div>
			  	</div>

			  </div>
			  <!--Etiqueta nnumero 2 --->
			  <div class="col-md-4">
			  	<div class="row">
			  		<div class="col-md-4" style="padding-top: 20px">Nombre</div>
			  		<div class="col-md-8"><input type="text" name="nombre" required maxlength="30" style="border-radius: 10px; border: 1px solid #BFC9CA; width: 300px; height: 35px; margin-top: 20px"></div>

			  		<div class="col-md-4" style="padding-top: 20px">Descripcion</div>
			  		<div class="col-md-8"><input type="text" name="descripcion" style="border-radius: 10px; border: 1px solid #BFC9CA; width: 300px; height: 150px; margin-top: 15px"></div>

			  	</div>

			  </div>
			  <!--Etiqueta nnumero 3 --->
			  <div class="col-md-4">
			  	<div class="row">
			  		<div class="col-md-6" style="padding-top: 20px">Stock minimo</div>
			  		<div class="col-md-6"><input type="text" name="stock" required maxlength="10" style="border-radius: 10px; border: 1px solid #BFC9CA; margin-top: 20px"></div>

			  		<div class="col-md-6" style="padding-top: 20px">Proveedor</div>
			  		<div class="col-md-6"><input type="text" name="proveedor" required maxlength="10" style="border-radius: 10px; border: 1px solid #BFC9CA; margin-top: 20px"></div>

			  		<div class="col-md-6" style="padding-top: 20px">Tipo de Producto</div>
			  		<div class="col-md-6"><input type="text" name="tiproduct" required maxlength="10" style="border-radius: 10px; border: 1px solid #BFC9CA; margin-top: 20px"></div>

			  		<div class="col-md-6" style="padding-top: 20px">Forma de despacho</div>
			  		<div class="col-md-6"><input type="text" name="despacho" required maxlength="10" style="border-radius: 10px; border: 1px solid #BFC9CA; margin-top: 20px"></div>

			  	</div>
			  </div>
			  
			</div>

			<center>
			  	<button class="btn btn-primary boton-proce" style="border-radius: 10px; width: 200px; margin: 20px" id="boton" onclick="guardar_procedimiento();">AGREGAR
            		</button>
			  </center>
        </div>
       
      </div>
    </div>
  
  </div>

</div>
<script type="text/javascript" src="{{ asset ("/librerias/moment.min.js")}}"></script>
<script type="text/javascript" src="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.js")}}"></script>
</html>