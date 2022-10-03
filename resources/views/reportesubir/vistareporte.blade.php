
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span></button>
    
    <h4 class="modal-title" id="myModalLabel">Subir Reportes de Llamadas</h4>
</div>
<div class="modal-body">
  
    <div class="col-sm-12">
                <div class="box box-info">
                    <form action="{{route('reportesubir.vistasubida')}}" enctype="multipart/form-data" method="POST">     
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">   
                        <div class="box-body">
                            <div class="form-group col-xs-12{{ $errors->has('archivo') ? ' has-error' : '' }}">
                                <input name="archivo" id="archivo" type="file" accept=".csv"   class="archivo form-control"  required/><br /><br />
                                @if ($errors->has('archivo'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('archivo') }}</strong>
                                        </span>
                                @endif
                            </div>  
                          <div class="col-sm-2">
                              <button type="submit" class="btn btn-primary" >
                                    Subir
                              </button>
                          </div> 
                        </div>

                     </form>
                </div>                       
                  

              </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
