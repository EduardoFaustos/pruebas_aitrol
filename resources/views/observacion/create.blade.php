<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
  <h4 class="modal-title" id="myModalLabel">{{trans('observacion.crearobservacion')}}</h4>
</div>
<div class="modal-body">
  <form method="post" action="{{route('observacion.store')}}">
    {{csrf_field()}}
    <div class="form-group col-md-12">
      <label for="max_consulta" class="col-md-12 control-label">{{trans('observacion.observacion')}}</label>
      <textarea name="observacion" class="form-group col-md-12"></textarea>
    </div>



    <button type="submit" class="btn btn-primary">{{trans('observacion.agregar')}}</button>
  </form>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('observacion.cerrar')}}</button>
</div>

<script type="text/javascript">



</script>