@php

$id_auth = Auth::user()->id_tipo_usuario;
$contador=0;
//dd($pacientes);
@endphp

@if(isset($finalArray))
@if($t_factura==1)
<div class="col-md-12 table-responsive" style=" height: 400px; overflow-y: scroll;">

    <input type="hidden" name="tipo_factura" value="1">
    @if(count($finalArray)>0)
    @foreach($finalArray as $key=>$x)
    @php
    //dd($key,$x);
    //list($nombre, $id_paciente, $hc_procedimiento) = explode('/', $key);
    $paciente=Sis_medico\Paciente::find($key);
    //dd($paciente);
    //dd($x);
    //dd($x);
    //dd($hc_procedimiento);

    @endphp
    @foreach($x as $p)

    <div class="panel panel-default">
        <div style="margin-top: 5px;">

            <span style="font-family: Helvetica; font-weight: bold; font-style: italic; padding-top:10px; margin-left: 4px; color: #534f4f;">{{$p['nombre_principal']}} {{date('d-m-Y',strtotime($p['fecha']))}}</span>
            <br />
            <span style="font-family: Helvetica; font-weight: bold; font-style: italic; padding-top:10px; margin-left: 10px; color: #534f4f;">{{$paciente->nombre1}} {{$paciente->apellido1}} {{$paciente->apellido2}}</span>
        </div>
        <!-- <input type="hidden" name="paciente[]" value="{{$paciente->id}}">-->
        <input type="hidden" name="id_principal[]" value="{{$p['nombre_principal']}}">
        <div class="panel-body" style="padding:0;">

            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                <thead>
                    <tr class='well-dark'>

                        <th width="35%" tabindex="0">{{trans('contableM.DescripciondelProducto')}}</th>
                        <th width="10%" tabindex="0">{{trans('contableM.cantidad')}}</th>
                        <th width="10%" tabindex="0">{{trans('contableM.precio')}}</th>
                        <th width="10%" tabindex="0">{{trans('contableM.cobrarseguro')}}</th>
                        <th width="10%" tabindex="0">% {{trans('contableM.prctdesc')}}</th>
                        <th width="10%" tabindex="0">{{trans('contableM.descuento')}}</th>
                        <th width="10%" tabindex="0">{{trans('contableM.precioneto')}}</th>
                        <th width="5%" tabindex="0">{{trans('contableM.iva')}}</th>
                        <th width="10%" tabindex="0">
                            <button onclick="nuevo({{$contador}},'{{$paciente->id}}','{{$p['hc_procedimiento']}}','{{$p['agenda']}}')" type="button" class="btn btn-success btn-gray">
                                <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                            </button>
                        </th>
                    </tr>
                </thead>

                <tbody id="agregar_cuentas{{$contador}}">
                    @php
                    $contadore=1;
                    $finds= count($p['productos']);
                    @endphp
                    @foreach($p['productos'] as $keys=>$z)
                    @php
                    //dd($z);
                    @endphp

                    @php
                    //dd($v);
                    @endphp
                    <tr class="well">

                        <td style="max-width:100px;">
                            <input type="hidden" name="codigo[]" class="codigo_producto" />
                            <input type="hidden" name="paciente[]" value="{{$paciente->id}}">
                            <input type="hidden" name="fecha_procedimiento[]" value="{{date('Y-m-d',strtotime($p['fecha']))}}">
                            <input type="hidden" name="id_agenda[]" value="{{$p['agenda']}}">
                            <input type="hidden" name="hc_procedimiento[]" value="{{$p['hc_procedimiento']}}">

                            <select name="nombre[]" class="form-control select" style="width:100%" required onchange="verificar(this)">
                                    <option> </option>
                              @php
                                    $prod = DB::table('ct_productos')->where("codigo", $z->codigo)->where("id_empresa", $empresa->id)->first();
                                    //dd($prod);
                                    
                              @endphp
                                    @if(!is_null($prod))
                                    <option value="{{$prod->nombre}}"  selected="selected"  data-name="{{$prod->nombre}}" data-codigo="{{$prod->codigo}}" data-descuento="{{$prod->mod_desc}}" data-precio="{{$prod->mod_precio}}" data-maxdesc="{{$prod->descuento}}" data-iva="{{$prod->iva}}">{{$prod->codigo}} | {{$prod->descripcion}}</option>
                                    @endif
                            </select>
                            <textarea rows="3" wrap="hard" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto">@if($contadore==$finds){{$paciente->apellido1}} {{$paciente->nombre1}}  {{$p['nombre_principal']}} {{date('d-m-Y',strtotime($p['fecha']))}}@endif</textarea>
                            <input type="hidden" name="iva[]" class="iva" />
                        </td>
                        <td>
                            <input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="{{$z->cantidad}}" name="cantidad[]" required>
                        </td>
                        <td id="tprecio">
                            <select name="precio[]" class="form-control select2_precio pneto" style="width:60%;height:20px;display:inline;" required>
                                <option value="0"> </option>


                            </select>
                            <button type="button" class="btn btn-info btn-gray btn-xs cp">
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
                            <input class="form chx" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]">

                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-gray delete">
                                <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                            </button>
                        </td>
                    </tr>
                    @php
                    $contadore++;
                    @endphp
                    @endforeach


                </tbody>

            </table>

        </div>
    </div>
    @php
    $contador++;
    @endphp
    @endforeach
    @endforeach
    @else
    No hay datos.
    @endif

</div>
@elseif($t_factura==2)
<div class="col-md-12 table-responsive" style=" height: 400px; overflow-y: scroll;">

    <input type="hidden" name="tipo_factura" value="2">
    @if(count($finalArray)>0)
    @foreach($finalArray as $key=>$x)
    @php
    //dd($key,$x);
    //list($nombre, $id_paciente, $hc_procedimiento) = explode('/', $key);
    $paciente=Sis_medico\Paciente::find($key);
    //dd($paciente);
    //dd($x);
    //dd($x);
    //dd($hc_procedimiento);

    @endphp
    @foreach($x as $p)
    <div class="panel panel-default">
        <div style="margin-top: 5px;">

            <span style="font-family: Helvetica; font-weight: bold; font-style: italic; padding-top:10px; margin-left: 4px; color: #6e6b6b;">{{$p['nombre_principal']}} {{date('d-m-Y',strtotime($p['fecha']))}}</span>
            <br />
            <span style="font-family: Helvetica; font-weight: bold; font-style: italic; padding-top:10px; margin-left: 10px; color: #534f4f;">{{$paciente->nombre1}} {{$paciente->apellido1}} {{$paciente->apellido2}}</span>
        </div>
        <!-- <input type="hidden" name="paciente[]" value="{{$paciente->id}}">-->
        <input type="hidden" name="id_principal[]" value="{{$p['nombre_principal']}}">
        <div class="panel-body" style="padding:0;">

            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                <thead>
                    <tr class='well-dark'>

                        <th width="35%" tabindex="0">{{trans('contableM.DescripciondelProducto')}}</th>
                        <th width="10%" tabindex="0">{{trans('contableM.cantidad')}}</th>
                        <th width="10%" tabindex="0">{{trans('contableM.precio')}}</th>
                        <th width="10%" tabindex="0">{{trans('contableM.cobrarseguro')}}</th>
                        <th width="10%" tabindex="0">% {{trans('contableM.prctdesc')}}</th>
                        <th width="10%" tabindex="0">{{trans('contableM.descuento')}}</th>
                        <th width="10%" tabindex="0">{{trans('contableM.precioneto')}}</th>
                        <th width="5%" tabindex="0">{{trans('contableM.iva')}}</th>
                        <th width="10%" tabindex="0">
                            <button onclick="nuevo({{$contador}},'{{$paciente->id}}','{{$p['hc_procedimiento']}}','{{$p['agenda']}}')" type="button" class="btn btn-success btn-gray">
                                <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                            </button>
                        </th>
                    </tr>
                </thead>
                @php
                // ParametersConglomerada ahi esta todo
                $dataCode="";
                //particulares
                if(in_array($p['seguros'], $sec_part)){
                $dataCode= \Sis_medico\ParametersConglomerada::getUses($p['nombre_principal'],'particulares');
                }
                //privados
                if(in_array($p['seguros'],$sec_private)){
                $dataCode= \Sis_medico\ParametersConglomerada::getUses($p['nombre_principal'],'privados');
                }
                //publicos
                if(in_array($p['seguros'],$last_public)){

                $dataCode= \Sis_medico\ParametersConglomerada::getUses($p['nombre_principal'],'publicos');

                }
                $productoname= DB::table('ct_productos')->where('codigo',$dataCode)->first();
                $paquetes=[];
                if(count($productoname)>0){
                $paquetes= DB::table('ct_productos_paquete')->where('id_producto',$productoname->id)->get();
                }

                @endphp
                <tbody id="agregar_cuentas{{$contador}}">
                    @if(count($paquetes)>0)
                    @foreach($paquetes as $paquetes)
                    <tr class="well">
                        <td style="max-width:100px;">
                            <input type="hidden" name="codigo[]" class="codigo_producto" />
                            <input type="hidden" name="paciente[]" value="{{$paciente->id}}">
                            <input type="hidden" name="fecha_procedimiento[]" value="{{date('Y-m-d',strtotime($p['fecha']))}}">
                            <input type="hidden" name="id_agenda[]" value="{{$p['agenda']}}">
                            <input type="hidden" name="hc_procedimiento[]" value="{{$p['hc_procedimiento']}}">

                            <select name="nombre[]" class="form-control select" style="width:100%" required onchange="verificar(this)">
                                <option> </option>
                                @foreach($productos as $value)
                                <option value="{{$value->nombre}}" @if($paquetes->id_paquete==$value->id) selected="selected" @endif data-name="{{$value->nombre}}" data-codigo="{{$value->codigo}}" data-descuento="{{$value->mod_desc}}" data-precio="{{$value->mod_precio}}" data-maxdesc="{{$value->descuento}}" data-iva="{{$value->iva}}">{{$value->codigo}} | {{$value->descripcion}}</option>
                                @endforeach
                            </select>
                            <textarea wrap="hard" rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea>
                            <input type="hidden" name="iva[]" class="iva" />
                        </td>
                        <td>
                            <input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="1" name="cantidad[]" required>
                        </td>
                        <td id="tprecio">
                            <select name="precio[]" class="form-control select2_precio pneto" style="width:60%;height:20px;display:inline;" required>
                                <option value="0"> </option>


                            </select>
                            <button type="button" class="btn btn-info btn-gray btn-xs cp">
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
                            <input class="form chx" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]">

                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-gray delete">
                                <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr class="well">

                        <td style="max-width:100px;">
                            <input type="hidden" name="codigo[]" class="codigo_producto" />
                            <input type="hidden" name="paciente[]" value="{{$paciente->id}}">
                            <input type="hidden" name="fecha_procedimiento[]" value="{{date('Y-m-d',strtotime($p['fecha']))}}">
                            <input type="hidden" name="id_agenda[]" value="{{$p['agenda']}}">
                            <input type="hidden" name="hc_procedimiento[]" value="{{$p['hc_procedimiento']}}">

                            <select name="nombre[]" class="form-control select" style="width:100%" required onchange="verificar(this)">
                                <option> </option>
                                @foreach($productos as $value)
                                <option value="{{$value->nombre}}" @if($dataCode==$value->codigo) selected="selected" @endif data-name="{{$value->nombre}}" data-codigo="{{$value->codigo}}" data-descuento="{{$value->mod_desc}}" data-precio="{{$value->mod_precio}}" data-maxdesc="{{$value->descuento}}" data-iva="{{$value->iva}}">{{$value->codigo}} | {{$value->descripcion}}</option>
                                @endforeach
                            </select>
                            <textarea wrap="hard" rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto">{{$paciente->apellido1}} {{$paciente->nombre1}} {{$p['nombre_principal']}} {{date('d-m-Y',strtotime($p['fecha']))}}</textarea>
                            <input type="hidden" name="iva[]" class="iva" />
                        </td>
                        <td>
                            <input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="1" name="cantidad[]" required>
                        </td>
                        <td id="tprecio">
                            <select name="precio[]" class="form-control select2_precio pneto" style="width:60%;height:20px;display:inline;" required>
                                <option value="0"> </option>


                            </select>
                            <button type="button" class="btn btn-info btn-gray btn-xs cp">
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
                            <input class="form chx" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]">

                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-gray delete">
                                <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                            </button>
                        </td>
                    </tr>
                    @endif


                </tbody>

            </table>

        </div>
    </div>
    @php
    $contador++;
    @endphp
    @endforeach




    @endforeach

    @endif

</div>
@endif
@else


@endif