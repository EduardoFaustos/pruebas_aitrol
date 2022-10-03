@extends('archivo_plano.procedimientos.base')
@section('action-content')
	<section class="content">
		<div class="box">
			<div class="box-header">
			    <div class="row">
			        <div class="col-sm-6">
			          <h3 class="box-title" style="margin-top: 7px">Asignar Procedimientos a Nivel</h3>
			        </div>
			    </div>
  			</div>
		
		<div class="content">
          <form method="POST" action="{{route('ap_procedimiento.update_nivel')}}">
               {{ csrf_field() }}
            <div class="row">
              <div class="col-md-12">
                <label>Procedimiento</label><br>
                <h4>{{$procedimiento->descripcion}}</h4>
                <input type="hidden" name="id_procedimiento" value="{{$id}}">
                <input type="hidden" name="codigo" value="{{$procedimiento->codigo}}">
              </div>
              <div class="col-md-2"><br>
                <label>Nivel de Convenio</label><br>
                <input type="text" name="txtnivel" class="form-control" value="{{$nivel}}" readonly>
              </div>
              <div class="col-md-2"><br>
                <label>Des Conv</label><br>
                <select name="txtdes" class="form-control">
                  <option @if($procedimiento->desc_conv == 'SNSENE2015N1' ) selected @endif value="SNSENE2015N1">SNSENE2015N1</option>
                  <option @if($procedimiento->desc_conv == 'SNSENE2015N2' ) selected @endif value="SNSENE2015N2">SNSENE2015N2</option>
                  <option @if($procedimiento->desc_conv == 'SNSENE2015N3' ) selected @endif value="SNSENE2015N3">SNSENE2015N3</option>
                </select>
              </div>
              <div class="col-md-2"><br>
                <label>Anexo</label><br>
                <select name="txtanexo" class="form-control">
                  <option @if($procedimiento->anexo == 'SNSENE2015N1') selected @endif value="SNSENE2015N1">SNSENE2015N1</option>
                  <option @if($procedimiento->anexo == 'SNSENE2015N2') selected @endif value="SNSENE2015N2">SNSENE2015N2</option>
                  <option @if($procedimiento->anexo == 'SNSENE2015N3') selected @endif value="SNSENE2015N3">SNSENE2015N3</option>
                  <option @if($procedimiento->anexo == 'ANEXO6') selected @endif value="ANEXO6">ANEXO6</option>
                </select>
              </div>

              <div class="col-md-2"><br>
                <label>UVR1</label><br>
                <input type="text" name="uvr1" placeholder="Valor UVR1" class="form-control" value="{{$procedimiento->uvr1}}">
              </div>
              <div class="col-md-2"><br>
                <label>UVR2</label><br>
                <input type="text" name="uvr2" placeholder="Valor UVR2" class="form-control" value="{{$procedimiento->uvr2}}">
              </div>
              <div class="col-md-2"><br>
                <label>UVR3</label><br>
                <input type="text" name="uvr3" placeholder="Valor UVR3" class="form-control" value="{{$procedimiento->uvr3}}">
              </div>

              <div class="col-md-2"><br>
                <label>PRC1</label><br>
                <input type="text" name="prc1" placeholder="Valor PRC1" class="form-control" value="{{$procedimiento->prc1}}">
              </div>
              <div class="col-md-2"><br>
                <label>PRC2</label><br>
                <input type="text" name="prc2" placeholder="Valor PRC2" class="form-control" value="{{$procedimiento->prc2}}">
              </div>
              <div class="col-md-2"><br>
                <label>PRC3</label><br>
                <input type="text" name="prc3" placeholder="Valor PRC3" class="form-control" value="{{$procedimiento->prc3}}">
              </div>

              <div class="col-md-2"><br>
                <label>UVR1A</label><br>
                <input type="text" name="uvr1a" placeholder="Valor UVR1A" class="form-control" value="{{$procedimiento->uvr1a}}">
              </div>
              <div class="col-md-2"><br>
                <label>UVR2A</label><br>
                <input type="text" name="uvr2a" placeholder="Valor UVR2A" class="form-control" value="{{$procedimiento->uvr2a}}">
              </div>
              <div class="col-md-2"><br>
                <label>UVR3A</label><br>
                <input type="text" name="uvr3a" placeholder="Valor UVR3A" class="form-control" value="{{$procedimiento->uvr3a}}">
              </div>

              <div class="col-md-2"><br>
                <label>PRC1A</label><br>
                <input type="text" name="prc1a" placeholder="Valor UVR3A" class="form-control" value="{{$procedimiento->prc1a}}">
              </div>
              <div class="col-md-2"><br>
                <label>PRC2A</label><br>
                <input type="text" name="prc2a" placeholder="Valor PRC2A" class="form-control" value="{{$procedimiento->prc2a}}">
              </div>
              <div class="col-md-2"><br>
                <label>PRC3A</label><br>
                <input type="text" name="prc3a" placeholder="Valor PRC3A" class="form-control" value="{{$procedimiento->prc3a}}">
              </div>

              <div class="col-md-2"><br>
                <label>Separado</label><br>
                <select name="txtseparado" class="form-control">
                  <option value="F">F</option>
                </select>
              </div>
              <div class="col-md-2"><br>
                <label>Estado</label><br>
                <select name="txtestado" class="form-control">
                  <option @if($procedimiento->estado == '1') selected @endif value="1">Activo</option>
                  <option @if($procedimiento->estado == '0') selected @endif value="0">Inactivo</option>
                </select>
              </div>

              <div class="col-md-6"><br>
               <input type="submit" name="" value="Guardar" class="btn btn-success">
              </div>
            </div>
          </form>     
    	</div>
    </div>
	</section>
@endsection