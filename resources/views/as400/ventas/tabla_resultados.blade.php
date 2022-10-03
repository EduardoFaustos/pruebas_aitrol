
@foreach($searchinsumos as $key => $value){
    @php dd($key); @endphp

    <input type="hidden" class="ids" value="{{$value->codigo}}">
    <input type='hidden' name='id_hc_proc[]' value='{{$value->id_procedimiento}}'>
    <div class="panel panel-default">
        <div class="panel-heading"><input type="checkbox" name="activo[]" class="relactivo" checked />
            <input type="hidden" name="veractivo[]" class="veractivo" value="1">
            <input type="hidden" name="hc_procedimiento[]" value="'+id_procedimiento+'">&nbsp;&nbsp;&nbsp; {{$value->nombre1}} {{$value->apellido1}} {{$value->apellido2}}
        </div>
            <div class="panel-body" style="padding:0;">
                <div class="col-md-12 table-responsive" style="padding:0 !important;">
                    <table class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="margin-top:0 !important;">
                        <thead>
                          <tr class="well-dark"><th width="35%" class="" tabindex="0">Descripci&oacute;n del Producto</th>
                            <th width="10%" class="" tabindex="0">Cantidad</th><th width="10%" class="" tabindex="0">Precio</th>
                            <th width="10%" class="" tabindex="0">Cobrar Seguro</th><th width="10%" class="" tabindex="0">% Desc</th>
                            <th width="10%" class="" tabindex="0">Descuento</th><th width="10%" class="" tabindex="0">Precio Neto</th>
                            <th width="5%" class="" tabindex="0">IVA</th><th width="10%" class="" tabindex="0">
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                            <tr  id="mifila">
                                <td style="max-width:100px;">
                                <input type="hidden" name="nombre[]" class="codigo_producto" />
                                    <select name="codigo[]" class="form-control select2" style="width:95%; height:20px;" required onchange="verificar(this)" >
                                        <option> </option>
                                        @foreach($productos as $value)
                                            <option value="{{$value->codigo}}" data-name="{{$value->nombre}}" data-codigo="{{$value->nombre}}" data-descuento="{{$value->mod_desc}}"  data-precio="{{$value->mod_precio}}" data-maxdesc="{{$value->descuento}}"  data-iva="{{$value->iva}}">{{$value->codigo}} | {{$value->descripcion}}</option>
                                        @endforeach
                                          
                                    </select>
                                    <textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea>
                                    <input type="hidden" name="iva[]" class="iva" />
                                </td>
                                <td>
                                    <input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="1" name="cantidad[]" required >
                                </td>
                                <td>
                                    <select  name="precio[]" class="form-control select2_precio pneto" style="width:70%;display:inline;" required  >
                                        <option value="0"> </option>  
                                    </select>
                                    <button type="button" class="btn btn-info btn-gray btn-xs cp"  >
                                        <i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>
                                    </button>
                                </td>
                                <td>
                                    <input class="form-control text-right copago" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="copago[]" required>
                                </td>
                                <td>
                                    <input class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor[]" required>
                                    <input class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" required>
                                </td>
                                <td>
                                    <input class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc[]" required>
                                </td>
                                <td>
                                    <input class="form-control px-1 text-right" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" required>
                                </td>
                                <td>
                                    <input class="form" type="checkbox" style="width: 80%;height:20px;"  name="valoriva[]" disabled>
                                   
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-gray delete" >
                                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <input type="hidden" name="nom_paciente[]" value="'+nombre+'" />
                    <textarea rows="2" name="obs_paciente[]" maxlength="150" class="form-control px-1 desc_producto" placeholder="Observacion del Paciente"></textarea>
                </div>
            </div>
        </div>
    </div>

@endforeach