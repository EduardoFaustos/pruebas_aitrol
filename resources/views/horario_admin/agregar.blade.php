<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
  <h4 class="modal-title" id="myModalLabel">{{trans('horarioadmin.agregarhorariorecurrente')}}</h4>
</div>
<div class="modal-body">
  <form method="post" action="{{route('horario.agregar_enviar2')}}">
    {{csrf_field()}}
    <input type="hidden" name="inicio" value="{{$start}}">
    <input type="hidden" name="fin" value="{{$end}}">
    <input type="hidden" name="id_doctor" value="{{$id}}">
    <div class="form-group col-md-6">
      <label for="" class="col-md-12 control-label">{{trans('horarioadmin.inicio')}}</label>
      <label for="" class="col-md-12 control-label">{{$inicio}}</label>
    </div>
    <div class="form-group col-md-6">
      <label for="" class="col-md-12 control-label">Fin</label>
      <label for="" class="col-md-12 control-label">{{$fin}}</label>
    </div>

    <div class="form-group {{ $errors->has('tipo') ? ' has-error' : '' }}">
      <label for="tipo" class="col-md-4 control-label">{{trans('horarioadmin.tipodehorario')}}</label>
      <div class="col-md-6">
        <select id="tipo" name="tipo" class="form-control" onchange="edad();">
          <option value="0">{{trans('horarioadmin.todos')}}</option>
          <option value="1">{{trans('horarioadmin.consulta')}}</option>
          <option value="2">{{trans('horarioadmin.procedimiento')}}</option>
          <option value="3">IESS</option>
        </select>
      </div>
    </div>
    <button type="submit" class="btn btn-primary">{{trans('horarioadmin.guardar')}}</button>
  </form>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('horarioadmin.cerrar')}}</button>
</div>