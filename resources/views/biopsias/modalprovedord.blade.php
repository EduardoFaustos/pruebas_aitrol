<div class="modal-content">
  <div  class="modal-header" style="color: white; padding-top: 5px; padding-bottom: 1px; background-image: linear-gradient(to right, #004AC1,#004AC1,#004AC1);">
     <button type="button" class="close" data-dismiss="modal" style="color: white;">&times;</button>
    <h4 class="modal-title">trans{{('biopsias.AGREGAR')}} trans{{('biopsias.Proveedores')}}</h4>
  </div>
  <div class="modal-body">
    <form  action="{{route('hospital_admin.registropro')}}" enctype="multipart/form-data" method="POST">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
        
          <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-4" style="padding-top: 20px">trans{{('biopsias.Nombre')}}</div>
                      <div class="col-md-8"><input type="text" name="name" required maxlength="200" style="border-radius: 10px; border: 1px solid #BFC9CA; margin-top: 20px;"></div>

                    </div>
          </div>
                    <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-4" style="padding-top: 20px">trans{{('biopsias.Descripción')}}</div>
                      <div class="col-md-8"><input type="text" name="descri" required maxlength="200" style="border-radius: 10px; border: 1px solid #BFC9CA; margin-top: 20px;"></div>

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