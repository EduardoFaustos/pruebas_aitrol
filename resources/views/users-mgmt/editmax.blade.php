<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
  <h4 class="modal-title" id="myModalLabel">{{trans('adminusuarios.actualizarpacientes')}}</h4>
</div>
<div class="modal-body">
  <form method="post" action="{{route('user-management.max', ['id' => $usuario->id])}}">
    {{csrf_field()}}
    <div class="form-group col-md-6">
      <label for="max_consulta" class="col-md-12 control-label">{{trans('adminusuarios.maximoconsultas')}}</label>
      <select id="max_consulta" name="max_consulta" class="form-control">
        @for ($i = 1; $i <= 30; $i++) <option {{$usuario->max_consulta == $i ? 'selected' : ''}} value={{$i}}>{{$i}}</option>
          @endfor
      </select>
    </div>

    <div class="form-group col-md-6">
      <label for="max_procedimiento" class="col-md-12 control-label">{{trans('adminusuarios.maximoprocedimiento')}}</label>
      <select id="max_procedimiento" name="max_procedimiento" class="form-control">
        @for ($i = 1; $i <= 30; $i++) <option {{$usuario->max_procedimiento == $i ? 'selected' : ''}} value={{$i}}>{{$i}}</option>
          @endfor
      </select>
    </div>

    <button type="submit" class="btn btn-primary">{{trans('adminusuarios.editar')}}</button>
  </form>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('adminusuarios.cerrar')}}</button>
</div>