<div class="modal-content">
  <div  class="modal-header" style="color: white; padding-top: 5px; padding-bottom: 1px; background-image: linear-gradient(to right, #004AC1,#004AC1,#004AC1);">
    <button type="button" class="close" data-dismiss="modal" style="color: white;">&times;</button>
    <h4 class="modal-title">trans{{('biopsias.AGREGAR')}} trans{{('biopsias.Proveedores')}}</h4>
  </div>
  <div class="modal-body">

  <form enctype="multipart/form-data" method="POST" action="{{route('hospital_admin.updatep', ['id' => $proovedorid->id])}}">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
             <div class="col-md-12">
                    <div class="row"> 
                    <div class="col-md-4" style="padding-top: 10px">trans{{('biopsias.Logo')}}</div>
                      <div class="col-md-8">                    
                      <input type="hidden" name="logo" value="">    
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">       
                           <input name="imagen" id="imagen"  type="file"   class="archivo form-control"  required>
                                @if ($errors->has('archivo'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('archivo') }}</strong>
                                        </span>
                                @endif
                            
                        </div>
                   

                    </div>

          </div>

                      <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-4" style="padding-top: 20px">trans{{('biopsias.Ruc')}}</div>
                      <div class="col-md-8"><input type="text" value="{{$proovedorid->ruc}}" name="ruc" required maxlength="10" style="border-radius: 10px; border: 1px solid #BFC9CA; margin-top: 20px;"></div>

                    </div>
          </div>
                    <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-4" style="padding-top: 20px">trans{{('biopsias.RazónSocial')}}</div>
                      <div class="col-md-8"><input type="text" value="{{$proovedorid->razonsocial}}" name="razon" required maxlength="20" style="border-radius: 10px; border: 1px solid #BFC9CA; margin-top: 20px;"></div>

                    </div>
          </div>
                     <div class="col-md-12">
                     <div class="row"> 

                      <div class="col-md-4" style="padding-top: 25px">trans{{('biopsias.NombreComercial')}}</div>
                      <div class="col-md-8"><input type="text" value="{{$proovedorid->nombrecomercial}}" name="nombre" required maxlength="10" style="border-radius: 15px; border: 1px solid #BFC9CA; margin-top: 25px"></div>

                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="row"> 

                      <div class="col-md-4" style="padding-top: 20px">trans{{('biopsias.Emails')}}</div>
                      <div class="col-md-8"><input type="text" name="emails" value="{{$proovedorid->email}}" required maxlength="10" style="border-radius: 10px; border: 1px solid #BFC9CA; margin-top: 15px"></div>
                   </div>
                    </div>

                     <div class="col-md-12">
                     <div class="row"> 
                      <div class="col-md-4" style="padding-top: 20px">trans{{('biopsias.TipoProveedor')}}</div>
                      <div class="col-md-8"><select style="margin-top:10px;width:180px;" class="select form-control" name="tipop" onchange="cargarinput(this.value);" required>
                      <option @if(($proovedorid->tipop)==1) Selected @endif value="1">Takeda Mexico</option>
                      <option @if(($proovedorid->tipop)==2) Selected @endif value="2">Roche</option>
                      <option @if(($proovedorid->tipop)==3) Selected @endif value="3">ICN Farmacéutica</option>
                       <option @if(($proovedorid->tipop)==4) Selected @endif value="4"> ICN Farmacéutica</option>
                      
                     
                      </select>
                    </div>
                     </div>
                      </div>

   
    
     
      <div class="col-md-12" style="text-align: center; margin-top: 20px;">
          <button type="submit" class="btn btn-primary active" style=" border-radius: 10px;">trans{{('biopsias.AGREGAR')}}
          </button>
      </div>
    </form>
  </div>
  <div class="modal-footer"> 
  </div>
</div>

