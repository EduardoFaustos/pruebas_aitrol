<div class="modal-content" style="width:auto">
    <div class="modal-body">
        <div class="box-body">
            <form method="post"  id=formulario action="{{route('save_pentax_limpieza')}}" accept-charset="UTF-8" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group col-md-12 col-xs-12">
                    <label class="label label-danger" style="font-size: 16px;">Paciente: {{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}}</label>
                </div>
                <div class="form-group col-md-6 col-xs-6" style="margin-top:5px;">
                    <input type="hidden" name="id_paciente" value="{{$paciente->id}}">
                    <input type="hidden" name="id_pentax" value="{{$id_pentax}}">
                    <input type="hidden" name="id_sala" value="{{$id_sala}}">
                    <label for="tipo_desinfeccion" class="col-md-3 control-label" style="font-size:12px;">Tipo de Desinfección</label>
                    <div class="col-md-9">
                        <select id="tipo_desinfeccion" name="tipo_desinfeccion" class="form-control input-sm">
                            <option value="">Seleccione...</option>
                            <option value="1">Concurrente</option>
                            <option value="2">Terminal</option>
                        </select>

                    </div>
                </div>
                <div class="form-group col-md-6 col-xs-6">
                    <label for="nom_detergente" class="col-md-3 control-label" style="font-size:12px;">Nombre del Detergente / Desinfectante</label>
                    <div class="col-md-9">
                        <input type="text" name="nom_detergente" class="form-control input-sm">
                    </div>
                </div>
                <div class="form-group col-md-12 col-xs-6"  style="margin-top:5px">
                    <div class="col-md-4">
                        <label for="piso" class="col-md-3 control-label" style="font-size:12px;">Piso</label>
                        <div class="col-md-9">
                            <select id="piso" name="piso" class="form-control input-sm">
                                <option value="">Seleccione...</option>
                                <option value="1">Limpieza</option>
                                <option value="2">Desinfeccion</option>
                                <option value="3">Limpieza y Desinfeccion</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="velador" class="col-md-3 control-label" style="font-size:12px;">Techo</label>
                        <div class="col-md-9">
                            <select id="techo" name="techo" class="form-control input-sm">
                                <option value="">Seleccione...</option>
                                <option value="1">Limpieza</option>
                                <option value="2">Desinfeccion</option>
                                <option value="3">Limpieza y Desinfeccion</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="sop_monitor" class="col-md-3 control-label" style="font-size:12px;">Paredes</label>
                        <div class="col-md-9">
                            <select id="paredes" name="paredes" class="form-control input-sm">
                                <option value="">Seleccione...</option>
                                <option value="1">Limpieza</option>
                                <option value="2">Desinfeccion</option>
                                <option value="3">Limpieza y Desinfeccion</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="monitor" class="col-md-3 control-label" style="font-size:12px;">Otros Equipos</label>
                    <div class="col-md-9">
                        <select id="otros_equipos" name="otros_equipos" class="form-control input-sm">
                            <option value="">Seleccione...</option>
                            <option value="1">Limpieza</option>
                            <option value="2">Desinfeccion</option>
                            <option value="3">Limpieza y Desinfeccion</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-md-8 col-xs-6">
                    <div class="col-md-2">
                        <label for="observacion" class="col-md-3 control-label" style="font-size:12px;">Obsevación</label>
                    </div>
                    <div class="col-md-10">
                        <input type="text" name="observacion" class="form-control" style="width:100%">
                    </div>
                </div>

                <div class="form-group col-md-12 col-xs-6" style="margin-top:5px;">
                    <label for="antes" class="col-md-3 control-label" style="font-size:12px;">Antes</label>
                    <div class="col-md-6">
                        <input type="file" name="imagen_antes" id="file-input-antes" accept="image/*">
                    </div>
                </div>
                <div class="form-group col-md-12" style="text-align:center">
                        <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-disk"> Guardar</span> </button>
                </div>
            </form>
        </div>
    </div>
</div>
