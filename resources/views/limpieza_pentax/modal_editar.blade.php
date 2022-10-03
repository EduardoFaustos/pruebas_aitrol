<div class="modal-content" style="width:auto">
    <div class="modal-body">
        <div class="box-body">
            <form method="post"  id=formulario action="{{route('edit_pentax_limpieza')}}" accept-charset="UTF-8" enctype="multipart/form-data">
                {{ csrf_field() }}
                <input type="hidden" name="id_registro" value="{{$id->id}}">
                <div class="form-group col-md-12 col-xs-12 text-center">
                    <label  style="font-size: 16px;">Editar</label>
                </div>
                <div class="form-group col-md-12 col-xs-12">
                    <label class="label label-danger" style="font-size: 16px;">Paciente: {{$id->paciente->apellido1}} {{$id->paciente->apellido2}} {{$id->paciente->nombre1}} {{$id->paciente->nombre2}}</label>
                </div>
                <div class="form-group col-md-6 col-xs-6" style="margin-top:5px;">
                    <label for="tipo_desinfeccion" class="col-md-3 control-label" style="font-size:12px;">Tipo de Desinfección</label>
                    <div class="col-md-9">
                        <select id="tipo_desinfeccion" name="tipo_desinfeccion" class="form-control input-sm">
                            <option value="">Seleccione...</option>
                            <option {{$id->tipo_desinfeccion == '1' ? 'selected' : ''}} value="1">Concurrente</option>
                            <option {{$id->tipo_desinfeccion == '2' ? 'selected' : ''}} value="2">Terminal</option>
                        </select>

                    </div>
                </div>
                <div class="form-group col-md-6 col-xs-6">
                    <label for="nom_detergente" class="col-md-3 control-label"  style="font-size:12px;">Nombre del Detergente / Desinfectante</label>
                    <div class="col-md-9">
                        <input type="text" name="nom_detergente" value="{{$id->nombre_detergente}}" class="form-control input-sm">
                    </div>
                </div>
                <div class="form-group col-md-12 col-xs-6"  style="margin-top:5px">
                    <div class="col-md-4">
                        <label for="piso" class="col-md-3 control-label" style="font-size:12px;">Piso</label>
                        <div class="col-md-9">
                            <select id="piso" name="piso" class="form-control input-sm">
                                <option value="">Seleccione...</option>
                                <option {{$id->piso == '1' ? 'selected' : ''}} value="1">Limpieza</option>
                                <option {{$id->piso == '2' ? 'selected' : ''}} value="2">Desinfeccion</option>
                                <option {{$id->piso == '3' ? 'selected' : ''}} value="3">Limpieza y Desinfeccion</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="velador" class="col-md-3 control-label" style="font-size:12px;">Techo</label>
                        <div class="col-md-9">
                            <select id="techo" name="techo" class="form-control input-sm">
                                <option value="">Seleccione...</option>
                                <option {{$id->techo == '1' ? 'selected' : ''}} value="1">Limpieza</option>
                                <option {{$id->techo == '2' ? 'selected' : ''}} value="2">Desinfeccion</option>
                                <option {{$id->techo == '3' ? 'selected' : ''}} value="3">Limpieza y Desinfeccion</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="sop_monitor" class="col-md-3 control-label" style="font-size:12px;">Paredes</label>
                        <div class="col-md-9">
                            <select id="paredes" name="paredes" class="form-control input-sm">
                                <option  value="">Seleccione...</option>
                                <option {{$id->paredes == '1' ? 'selected' : ''}} value="1">Limpieza</option>
                                <option {{$id->paredes == '2' ? 'selected' : ''}} value="2">Desinfeccion</option>
                                <option {{$id->paredes == '3' ? 'selected' : ''}} value="3">Limpieza y Desinfeccion</option>
                            </select>
                        </div>
                    </div>

                </div>
                <div class="col-md-4">
                    <label for="monitor" class="col-md-3 control-label" style="font-size:12px;">Otros Equipos</label>
                    <div class="col-md-9">
                        <select id="otros_equipos" name="otros_equipos" class="form-control input-sm">
                            <option value="">Seleccione...</option>
                            <option {{$id->otros_equipos == '1' ? 'selected' : ''}} value="1">Limpieza</option>
                            <option {{$id->otros_equipos == '2' ? 'selected' : ''}} value="2">Desinfeccion</option>
                            <option {{$id->otros_equipos == '3' ? 'selected' : ''}} value="3">Limpieza y Desinfeccion</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-md-8 col-xs-6">
                    <div class="col-md-2">
                        <label for="observacion" class="col-md-3 control-label" style="font-size:12px;">Obsevación</label>
                    </div>
                    <div class="col-md-10">
                        <input type="text" value="{{$id->observaciones}}" name="observacion" class="form-control" style="width:100%">
                    </div>
                </div>

                @if(is_null($id->path_despues))
                <div class="form-group col-md-12 col-xs-6" style="margin-top:5px;">
                    <label for="antes" class="col-md-3 control-label" style="font-size:12px;">Despues</label>
                    <div class="col-md-6">
                        <input  type="file" name="imagen_despues" id="file-input-despues" accept="image/*">
                    </div>
                </div>
                @endif
                <div class="form-group col-md-12" style="text-align:center">
                        <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-disk"> Guardar</span> </button>
                </div>
            </form>
        </div>
    </div>
</div>
