<div class="modal-content">
  <div class="modal-header">
    <h3 style="margin:0;">{{trans('contableM.activosfijos')}}</h3>
  </div>
  <div class="modal-body">
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{trans('contableM.codigo')}}</label>
        <div class="col-xs-4">
            <input class="form-control" type="text" id="mdcodigo" name="mdcodigo" placeholder="Codigo" maxlength="16">
            <input type="hidden" id="mdid" name="mdid">
        </div>
        <div class="col-xs-1"><span>-</span></div>
        <div class="col-xs-4">
            <input class="form-control" type="text" id="mdcodigo_num" name="mdcodigo_num" placeholder="Codigo" maxlength="16" onchange="ingresar_cero2();">
        </div>
    </div> <br>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{trans('contableM.nombre')}}</label>
        <div class="col-xs-10">
            <input class="form-control" type="text" id="mdnombre" name="mdnombre" placeholder="Nombre">
        </div>
    </div> <br>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{trans('contableM.Descripcion')}}</label>
        <div class="col-xs-10">
            <input class="form-control" type="text" id="mddescripcion" name="mddescripcion" placeholder="Descripción">
        </div>
    </div> <br>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{trans('contableM.tipo')}}</label>
        <div class="col-xs-10">
            <select id="mdtipo" name="mdtipo" class="form-control form-control-sm select2_cuentas2" style="width: 100%;" required>
                <option value="">{{trans('contableM.seleccione')}}...</option>
                @foreach($tipos as $value)
                <option value="{{$value->id}}">{{$value->nombre}}</option>
                @endforeach
            </select>
        </div>
    </div> <br>
    <div class="form-group">
      <label for="" class="col-sm-2 control-label">{{trans('contableM.categoria')}}</label>
      <div class="col-xs-10">
          <select id="mdgrupo" name="mdgrupo" class="form-control select2_cuentas2" style="width: 100%;" required>
              <option value="">{{trans('contableM.seleccione')}}...</option>
              @foreach($sub_tipos as $value)
              <option value="{{$value->id}}">{{$value->nombre}}</option>
              @endforeach
          </select>
      </div>
    </div> <br>

    <div class="form-group">
      <label for="" class="col-sm-2 control-label">{{trans('contableM.responsable')}}</label>
      <div class="col-xs-10">
          <select id="mdresponsable" name="mdresponsable" class="form-control form-control-sm select2_color" style="width: 100%;"  onchange="guardar_responsable();">
              <option value="">{{trans('contableM.seleccione')}}...</option>
              @foreach($af_responsables as $responsable)
                  <option value="{{$responsable->nombre}}">{{$responsable->nombre}}</option>
              @endforeach
              
          </select>
      </div>
    </div> <br>
    <div class="form-group">
      <label for="" class="col-sm-2 control-label">{{trans('contableM.ubicacion')}}</label>
      <div class="col-xs-10">
          <input type="text" name="mdubicacion" id="mdubicacion" class="form-control" placeholder="Ubicación">
      </div>
    </div> <br>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{trans('contableM.marca')}}</label>
        <div class="col-xs-10">
            <select id="mdmarca" name="mdmarca" class="form-control select2_color" style="width: 100%;" required onchange="guardar_marca();" >
                <option value="">{{trans('contableM.seleccione')}}...</option>
                @foreach($marcas as $value)
                <option value="{{$value->nombre}}">{{$value->nombre}}</option>
                @endforeach
            </select>
        </div>
    </div> <br>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{trans('contableM.color')}}</label>
        <div class="col-xs-10">
            <select id="mdcolor" name="mdcolor" class="form-control select2_color" style="width:100%;" required onchange="guardar_color();" >
                <option value="">{{trans('contableM.seleccione')}}...</option>
                @foreach($af_colores as $colores)
                    <option value="{{$colores->nombre}}">{{$colores->nombre}}</option>
                @endforeach
            </select>
        </div>
    </div> <br>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{trans('contableM.modelo')}}</label>
        <div class="col-xs-10">
            <input class="form-control" type="text" id="mdmodelo" name="mdmodelo" placeholder="Modelo">
        </div>
    </div> <br>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{trans('contableM.serie')}}</label>
        <div class="col-xs-10">
            <select id="mdserie" name="mdserie" class="form-control select2_color" style="width:100%;" required onchange="guardar_serie();">
                <option value="">{{trans('contableM.seleccione')}}...</option>
                @foreach($af_series as $series)
                    <option value="{{$series->nombre}}">{{$series->nombre}}</option>
                @endforeach
            </select>
        </div>
    </div> <br>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{trans('contableM.procedencia')}}</label>
        <div class="col-xs-10">
            <input class="form-control" type="text" id="mdprocedencia" name="mdprocedencia" placeholder="Procedencia">
        </div>
    </div> <br>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{trans('contableM.cerrar')}}</button>
  </div>
</div>

<script type="text/javascript">
  

  $(document).ready(function() {
    $('.select2_cuentas2').select2({
        tags: false
    });
    
    $('.select2_color').select2({
        tags: true
    });
      
  });
</script>
