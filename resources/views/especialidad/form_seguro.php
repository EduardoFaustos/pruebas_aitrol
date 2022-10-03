 <div class="row">
   <div class="box box-primary col-xs-12">
     <div class="box-header with-border">
       <h3 class="box-title">{{trans('especialidad.crearnuevoseguro')}}</h3>
     </div>
     <!-- /.box-header -->
     <div id="notificacion_resul_fans">
     </div>
     <form id="f_nuevo_seguro" action="agregar_nuevo_seguro" class="form_entrada form-horizontal" method="post">
       <input type="hidden" name="_token" id="_token" value="<?= csrf_token(); ?>">
       <div class="box-body">
         <div class="form-group col-md-10">
           <label for="nombre">{{trans('especialidad.nombre')}}</label>
           <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre del Seguro">
         </div>
         <div class="form-group col-md-10">
           <label for="descripcion">{{trans('especialidad.descripcion')}}</label>
           <input type="text" class="form-control" id="descripcion" name="descripcion" placeholder="Descripcion">
         </div>
         <div class="form-group col-md-10">
           <label for="Tipo">{{trans('especialidad.tipo')}}</label>
           <select id="tipo" name="tipo" class="form-control">
             <option value="0" selected="selected">{{trans('especialidad.publico')}}</option>
             <option value="1">{{trans('especialidad.privado')}}</option>
           </select>
         </div>
         <div id="cp2" class="form-group col-xs-4 colorpicker2 ">
           <label for="Color">{{trans('especialidad.colordelaetiqueta')}}</label>
           <input id="color" name="color" type="hidden" value="#00AABB" class="form-control" />
           <span class="input-group-addon colorpicker-2x"><i style="width: 50px; height: 50px;"></i></span>
         </div>
       </div><!-- /.box-body -->
       <div class="box-footer">
         <div class="pull-right">
           <button type="submit" class="btn btn-primary">{{trans('especialidad.guardar')}}</button>
         </div>
         <br />
       </div><!-- /.box-footer -->
       <!-- /. box -->
     </form>
   </div><!-- /.col -->
 </div><!-- /.row -->

 <script type="text/javascript">
   $('.colorpicker2').colorpicker(

   );
 </script>

 <style>
   .colorpicker-2x .colorpicker-saturation {
     width: 200px;
     height: 200px;
   }

   .colorpicker-2x .colorpicker-hue,
   .colorpicker-2x .colorpicker-alpha {
     width: 30px;
     height: 200px;
   }

   .colorpicker-2x .colorpicker-color,
   .colorpicker-2x .colorpicker-color div {
     height: 30px;
   }
 </style>