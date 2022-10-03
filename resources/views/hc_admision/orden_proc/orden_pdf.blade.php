
<style type="text/css">
.table{
    font-size: 15px;
}
td{
    padding: 0px;
    padding-bottom: -5px;
}

</style>



<div class="container-fluid" style="margin-top: -45px;">
    <div class="row ">
        <div style="float: left;width: 50%;">
            <img width=300 height=100 src="{{base_path().'/storage/app/logo/logo1391707460001.png'}}">
        </div>
        <div style="float: left;width: 50%;">
            <table style="font-size: 14px;border: 1.1px solid black;border-radius: 15px;padding: 5px;">
                <tr>
                    <td style="padding-bottom: 0px;"><b>{{trans('ehistorialexam.Fecha:')}}</b></td><td colspan="2" style="border-bottom: 1px solid black;">{{Date('d/n/Y')}}</td><td style="text-align: right;"><b>Edad:</b></td><td style="border-bottom: 1px solid black;">{{$age}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 0px;"><b>{{trans('ehistorialexam.Paciente:')}}</b></td><td colspan="4" style="border-bottom: 1px solid black;font-size: 11px;">{{$agenda->papellido1}} @if($agenda->papellido2 != "(N/A)"){{ $agenda->papellido2}}@endif {{ $agenda->pnombre1}} @if($agenda->pnombre2 != "(N/A)"){{ $agenda->pnombre2}}@endif</td>
                </tr>
                
                <tr>
                    <td style="padding-bottom: 0px;"><b>{{trans('ehistorialexam.Seguro:')}}</b></td><td colspan="4" style="border-bottom: 1px solid black;font-size: 12px;">{{$agenda->hsnombre}}</td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-bottom: 0px;"><b>{{trans('ehistorialexam.MedicoSolicitante:')}}</b></td><td colspan="3" style="border-bottom: 1px solid black;font-size: 12px;">@if(!is_null($orden)){{$agenda->udnombre}} {{$agenda->udapellido}}@endif</td>
                </tr>
                
                
            </table>
        </div> 
        <div style="clear:both;">
            <table>

                <tr>
                    <td style="padding-bottom: 0px;"><b>{{trans('ehistorialexam.MotivoEstudio:')}}</b></td><td colspan="1" style="border-bottom: 1px solid black;font-size: 12px;">@if(!is_null($orden)){{substr($orden->motivo,0,100)}} @endif </td>
                </tr>
                
            </table>
        </div> 
        <div style="clear:both;height: 2px;">&nbsp;</div>                      

        <div class="col-md-12" style="background: #007777;font-size: 14px;"><b style="color: white;">{{$tipos->find('1')->nombre}}</b>
        </div>
        <div class="col-md-12" >    
            <div class="col-md-7" style="padding: 0px;float: left;width: 55%;padding-right: -5px;">
                <table class="table table-striped">
                    <tr>
                        <td><span style="width: 20px;">&nbsp;</span></td><td><b style="color: #007777;font-size: 17px;">{{$tipos->find('1')->ubicacion}}</b></td>
                    </tr>    
                @foreach($detalles as $detalle)
                    @if($detalle->id_tipo_procedimiento=='1')
                    <tr>
                        <td><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked style="color: #007777;" style="color: #007777;" @endif @endif type="checkbox" class="flat-green"></td><td>{{$detalle->nombre}}</td>    
                    </tr>    
                    @endif
                @endforeach
                    <tr>
                        <td><span style="width: 20px;">&nbsp;</span></td><td><b style="color: #007777;font-size: 17px;">{{$tipos->find('3')->ubicacion}}</b></td>
                    </tr>    
                @foreach($detalles as $detalle)
                    @if($detalle->id_tipo_procedimiento=='3')
                    <tr>
                        <td><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked style="color: #007777;"@endif @endif type="checkbox" class="flat-green"></td><td>{{$detalle->nombre}}</td>    
                    </tr>    
                    @endif
                @endforeach
                    <tr>
                        <td><span style="width: 20px;">&nbsp;</span></td><td><b style="color: #007777;font-size: 17px;">{{$tipos->find('5')->ubicacion}}</b></td>
                    </tr>    
                @foreach($detalles as $detalle)
                    @if($detalle->id_tipo_procedimiento=='5')
                    <tr>
                        <td><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked style="color: #007777;"@endif @endif type="checkbox" class="flat-green"></td><td>{{$detalle->nombre}}</td>    
                    </tr>    
                    @endif
                @endforeach        
                </table>
            </div>
            <div class="col-md-5" style="padding: 0px;float: left;width: 45%;padding-left: -5px;">
                <table class="table table-striped">
                    <tr>
                        <td><span style="width: 20px;">&nbsp;</span></td><td><b style="color: #007777;font-size: 17px;">{{$tipos->find('2')->ubicacion}}</b></td>
                    </tr>    
                @foreach($detalles as $detalle)
                    @if($detalle->id_tipo_procedimiento=='2')
                    <tr>
                        <td><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked style="color: #007777;"@endif @endif type="checkbox" class="flat-green"></td><td>{{$detalle->nombre}}</td>    
                    </tr>    
                    @endif
                @endforeach
                    <tr>
                        <td><span style="width: 20px;">&nbsp;</span></td><td><b style="color: #007777;font-size: 17px;">{{$tipos->find('4')->ubicacion}}</b></td>
                    </tr>    
                @foreach($detalles as $detalle)
                    @if($detalle->id_tipo_procedimiento=='4')
                    <tr>
                        <td><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked style="color: #007777;"@endif @endif type="checkbox" class="flat-green"></td><td>{{$detalle->nombre}}</td>    
                    </tr>    
                    @endif
                @endforeach
                    <tr>
                        <td></td><td>{{trans('ehistorialexam.Otros:')}}<span style="border-bottom: 1px solid black;">@if(!is_null($orden)){{$orden->endoscopia_urgencia}}@endif</span></td>
                    </tr>
                </table>
            </div>
            <div style="clear:both;height: 1px;">&nbsp;</div> 
            
            
            
        </div>
        <div style="clear:both;height: 2px;">&nbsp;</div> 
        <div class="col-md-12" style="background: #007777;font-size: 14px;"><b style="color: white;">{{$tipos->find('6')->nombre}}</b>
        </div>
        <div class="col-md-12" >    
            <div class="col-md-7" style="padding: 0px;float: left;width: 55%;padding-right: -5px;">
                <table class="table table-striped">  
                @foreach($detalles as $detalle)
                    @if($detalle->id_tipo_procedimiento=='6' && $detalle->orden < '7')
                    <tr>
                        <td><span style="width: 20px;"><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked style="color: #007777;"@endif @endif type="checkbox" class="flat-green"></span></td><td>{{$detalle->nombre}}</td>    
                    </tr>    
                    @endif
                @endforeach
                </table>
            </div>
            <div class="col-md-5" style="padding: 0px;float: left;width: 45%;padding-left: -5px;">
                <table class="table table-striped">    
                @foreach($detalles as $detalle)
                    @if($detalle->id_tipo_procedimiento=='6' && $detalle->orden >= '7')
                    <tr>
                        <td><span style="width: 20px;"><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked style="color: #007777;"@endif @endif type="checkbox" class="flat-green"></span></td><td>{{$detalle->nombre}}</td>    
                    </tr>    
                    @endif
                @endforeach
                    <tr>
                        <td></td><td>{{trans('ehistorialexam.Otros:')}}<span style="border-bottom: 1px solid black;">@if(!is_null($orden)){{$orden->endoscopia_terapeutica}}@endif</span></td>
                    </tr>
                </table>
            </div>
        </div> 
        
        <div style="clear:both;height: 2px;">&nbsp;</div> 
        <div class="col-md-12" style="background: #007777;clear:both;font-size: 14px;">
            <b style="color: white;">{{$tipos->find('7')->nombre}}</b>
        </div>
        <div style="clear:both;height: 2px;">&nbsp;</div>
        <div class="col-md-12" >    
            <div class="col-md-7" style="padding: 0px;">
                <table class="table table-striped">  
                @foreach($detalles as $detalle)
                    @if($detalle->id_tipo_procedimiento=='7')
                    <tr>
                        <td><span style="width: 20px;"><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked style="color: #007777;"@endif @endif type="checkbox" class="flat-green"></span></td><td>{{$detalle->nombre}}</td>    
                    </tr>    
                    @endif
                @endforeach
                </table>
            </div>
        </div>
        <div style="clear:both;height: 2px;">&nbsp;</div>
        <div class="col-md-12" style="background: #007777;font-size: 14px;">
            <b style="color: white;">{{$tipos->find('8')->nombre}}</b>
        </div>
        <div style="clear:both;height: 2px;">&nbsp;</div>
        <div class="col-md-12" >    
            <div class="col-md-5" style="padding: 0px;">
                <table class="table table-striped">    
                @foreach($detalles as $detalle)
                    @if($detalle->id_tipo_procedimiento=='8')
                    <tr>
                        <td><span style="width: 20px;"><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked style="color: #007777;"@endif @endif type="checkbox" class="flat-green"></span></td><td>{{$detalle->nombre}}</td>    
                    </tr>    
                    @endif
                @endforeach
                </table>
            </div>
        </div> 
        <div style="clear:both;height: 2px;">&nbsp;</div>
        <div class="col-md-12" style="background: #007777;font-size: 14px;"><b style="color: white;">{{$tipos->find('9')->nombre}}</b>
        </div>
        <div style="clear:both;height: 2px;">&nbsp;</div>
        <div class="col-md-12" >    
            <div class="col-md-7" style="padding: 0px;float: left;width: 55%;padding-right: -5px;">
                <table class="table table-striped">
                    <tr>
                        <td><span style="width: 20px;">&nbsp;</span></td><td><b style="color: #007777;font-size: 17px;">{{$tipos->find('9')->ubicacion}}</b></td>
                    </tr>    
                @foreach($detalles as $detalle)
                    @if($detalle->id_tipo_procedimiento=='9')
                    <tr>
                        <td><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked style="color: #007777;"@endif @endif type="checkbox" class="flat-green"></td><td>{{$detalle->nombre}}</td>    
                    </tr>    
                    @endif
                @endforeach
                </table>
            </div>
            <div class="col-md-5" style="padding: 0px;float: left;width: 45%;padding-left: -5px;">
                <table class="table table-striped">
                    <tr>
                        <td><span style="width: 20px;">&nbsp;</span></td><td><b style="color: #007777;font-size: 17px;">Eco Doppler de</b></td>
                    </tr>
                    <tr>
                        <td><span style="width: 20px;">&nbsp;</span></td><td><span style="border-bottom: 1px solid black;">@if(!is_null($orden)){{$orden->eco_doppler}}@endif</span></td>
                    </tr>
                    <tr>
                        <td><span style="width: 20px;">&nbsp;</span></td><td><b style="color: #007777;font-size: 17px;">{{$tipos->find('10')->ubicacion}}</b></td>
                    </tr>    
                @foreach($detalles as $detalle)
                    @if($detalle->id_tipo_procedimiento=='10')
                    <tr>
                        <td><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked style="color: #007777;"@endif @endif type="checkbox" class="flat-green"></td><td>{{$detalle->nombre}}</td>    
                    </tr>    
                    @endif
                @endforeach
                    <tr>
                        <td></td><td>{{trans('ehistorialexam.Otros:')}}<span style="border-bottom: 1px solid black;">@if(!is_null($orden)){{$orden->ecografia}}@endif</span></td>
                    </tr>
                </table>
            </div>
        </div>  
        
        <div style="clear:both;height: 1px;">&nbsp;</div> 

        <div class="col-md-6" style="padding-left: 1px;padding-right: 1px;">

            
            <div class="col-md-12" style="background: #007777;font-size: 14px;"><b style="color: white;">{{$tipos->find('11')->nombre}}</b>
            </div>
            <div class="col-md-12" >    
                <div class="col-md-6" style="padding: 0px;float: left;width: 55%;padding-right: -5px;">
                    <table class="table table-striped">  
                    @foreach($detalles as $detalle)
                        @if($detalle->id_tipo_procedimiento=='11' && $detalle->orden < '6')
                        <tr>
                            <td><span style="width: 20px;"><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked style="color: #007777;"@endif @endif type="checkbox" class="flat-green"></span></td><td>{{$detalle->nombre}}</td>    
                        </tr>    
                        @endif
                    @endforeach
                    </table>
                </div>
                <div class="col-md-6" style="padding: 0px;float: left;width: 45%;padding-left: -5px;">
                    <table class="table table-striped">    
                    @foreach($detalles as $detalle)
                        @if($detalle->id_tipo_procedimiento=='11' && $detalle->orden >= '6')
                        <tr>
                            <td><span style="width: 20px;"><input id="ch{{$detalle->id}}" @if(!is_null($detalle_orden))@if(!is_null($detalle_orden->where('id_tipo_detalle_orden',$detalle->id)->first())) checked style="color: #007777;"@endif @endif type="checkbox" class="flat-green"></span></td><td>{{$detalle->nombre}}</td>    
                        </tr>    
                        @endif
                    @endforeach
                        <tr>
                            <td><span style="width: 20px;">&nbsp;</span></td><td>{{trans('ehistorialexam.Otros:')}}<span style="border-bottom: 1px solid black;">@if(!is_null($orden)){{$orden->prueba_func}}@endif</span></td>
                        </tr>
                    </table>
                </div>
            </div> 
            <div style="clear:both;height: 1px;">&nbsp;</div> 

            <!--SEGUNDA HOJA--> 
            <div class="col-md-12" style="background: #007777;font-size: 14px;"><b style="color: white;">{{trans('ehistorialexam.INSTRUCTIVOSPARALOSPACIENTESYRECOMENDACIONES')}}</b>
            </div>
            <div class="col-md-12" style="padding: 0px;"> 
                <table class="table table-striped">
                    <tr><td style="padding-bottom: 1px;"><b style="color: #007777;">{{trans('ehistorialexam.FechadelExamen:')}}</b></td><td style="margin-bottom: 1px solid black">@if(!is_null($orden)){{substr($orden->fecha_examen,0,10)}}@endif</td><td><b style="color: #007777;">{{trans('ehistorialexam.Hora:')}}</b></td><td style="margin-bottom: 1px solid black">@if(!is_null($orden)){{substr($orden->fecha_examen,10,10)}}@endif</td></tr>
                    <tr><td style="padding-bottom: 1px;" colspan="4"><b style="color: #007777;">{{trans('ehistorialexam.Observacion:')}}</b><span>{{trans('ehistorialexam.Veniracompañado(a),debetraerlaordenmédica')}}</span></td></tr>
                    <tr><td style="padding-bottom: 1px;border-bottom:  1px solid black;" colspan="4">@if(!is_null($orden)){{$orden->observacion}}@endif</td></tr>
                </table>    
            </div>
            <div style="clear:both;height: 15px;">&nbsp;</div> 
                
            <div class="col-md-12" style="background: #007777;font-size: 14px;"><b style="color: white;">{{trans('ehistorialexam.ENDOSCOPIADIGESTIVAALTA.CPRE.ENDOSCOPIA.LAPAROSCOPIA')}}</b>
            </div> 
            <div class="col-md-12" style="padding: 0px;"> 
                <table class="table table-striped" style="width: 100%">
                
                    <tr><td style="padding-bottom: 1px;" colspan="2">{{trans('ehistorialexam.1.Díaanteriordelexamen:meriendadietablanda')}}</td></tr> 
                    <tr><td style="padding-bottom: 1px;" colspan="2">{{trans('ehistorialexam.2.Díadelexamen:venircompletamenteenayunas')}}</td></tr> 
                    <tr><td style="padding-bottom: 1px;" width="10%">{{trans('ehistorialexam.3.Puede')}}</td><td width="90%" style="border-bottom: 1px solid black">@if(!is_null($orden)){{$orden->campo1}}@endif</td></tr> 
                    <tr><td style="padding-bottom: 1px;" colspan="2">{{trans('ehistorialexam.4.Posterioralexamen:Encasodepremedicación,nodebeconducirvehículosalgunashorasdespuésdelestudio')}}</td></tr>
                
                </table>    
            </div>
            <div style="clear:both;height: 10px;">&nbsp;</div> 
            <div class="col-md-12" style="background: #007777;"><b style="color: white;">{{trans('ehistorialexam.ENDOSCOPIADIGESTIVABAJA')}}</b>
            </div> 
            <div class="col-md-12" style="padding: 0px;"> 
                <table class="table table-striped" style="width: 100%;">
                    <tr><td style="padding-bottom: 1px;"><b style="color: #007777;">{{trans('ehistorialexam.AnuscopiayRectoscopia')}}</b></td></tr>
                    <tr><td style="padding-bottom: 1px;">{{trans('ehistorialexam.Aplicar')}} <span style="border-bottom: 1px solid black">@if(!is_null($orden)){{$orden->campo2}}@endif </span>{{trans('ehistorialexam.,3y2horasantesdelexamen')}}</td></tr>
                </table>
                <div style="height: 1px;">&nbsp;</div> 
                <table class="table table-striped" style="width: 100%;">
                    <tr><td style="padding-bottom: 1px;" ><b style="color: #007777;">{{trans('ehistorialexam.Sigmoidoscopia')}}</b></td></tr>
                    <tr><td style="padding-bottom: 1px;" >{{trans('ehistorialexam.1.Meriendadietalíquida,posteriormentetomar')}} <span style="border-bottom: 1px solid black">@if(!is_null($orden)){{$orden->campo3}}@endif</span></td></tr>
                    <tr><td style="padding-bottom: 1px;" >2. <span style="border-bottom: 1px solid black">@if(!is_null($orden)){{$orden->campo4}}@endif</span>{{trans('ehistorialexam.aplicarenelrecto3y2horasantesdelexamen')}} </td></tr>
                </table>
                <div style="clear:both;height: 1px;">&nbsp;</div> 
                <table class="table table-striped" style="width: 100%;">
                    <tr><td style="padding-bottom: 1px;" colspan="4"><b style="color: #007777;">{{trans('ehistorialexam.Colonoscopia')}}</b></td></tr>
                    <tr><td style="padding-bottom: 1px;" colspan="4">{{trans('ehistorialexam.1.Dosdíasanteriores,nofrutasylegumbres(excepto extractos).1tabletadeDULCOLAXenlanoche')}}</td></tr>
                    <tr><td style="padding-bottom: 1px;" colspan="4">{{trans('ehistorialexam.2.Dietalíquidaeldíaanterioralexamen')}}</td></tr>
                    <tr><td style="padding-bottom: 1px;" colspan="4">{{trans('ehistorialexam.3.Tomarunatabletade')}} <span style="border-bottom: 1px solid black">@if(!is_null($orden)){{$orden->campo5}}@endif</span>{{trans('ehistorialexam.30minutosantesdeingerirelCOLAX')}}</td></tr>
                    <tr><td style="padding-bottom: 1px;" colspan="4">4. <span style="border-bottom: 1px solid black">@if(!is_null($orden)){{$orden->campo6}}@endif</span> {{trans('ehistorialexam.Diluircadaunoenunlitrodeagua,tomarlosentrelas')}} <span style="border-bottom: 1px solid black">@if(!is_null($orden)){{$orden->campo7}}@endif</span></td></tr>
                    <tr><td style="padding-bottom: 1px;" colspan="4"> {{trans('ehistorialexam.eldía')}} <span style="border-bottom: 1px solid black">@if(!is_null($orden)){{$orden->campo8}}@endif</span>{{trans('ehistorialexam.Aproximadamente1vasocada10a15minutos')}}</td></tr>
                    <tr><td style="padding-bottom: 1px;" colspan="4">{{trans('ehistorialexam.5.Tomar1frascodeFLEETFOSFODAenunvasodeagua')}}</td></tr>
                    <tr><td style="padding-bottom: 1px;" colspan="4">{{trans('ehistorialexam.6.Posterioralexamen:Encasodepremedicación,nodebeconducirvehículosalgunashorasdespuésdelestudio')}}</td></tr>
                </table>    
            </div>
            <div style="clear:both;height: 10px;">&nbsp;</div>  
            <div class="col-md-12" style="background: #007777;"><b style="color: white;">{{trans('ehistorialexam.ECOGRAFÍAS')}}</b>
            </div> 
            <div class="col-md-12" style="padding: 0px;"> 
                <table class="table table-striped">
                    <tr><td style="padding-bottom: 1px;"><b style="color: #007777;">{{trans('ehistorialexam.Ecografíaabdominalsuperioryrenal')}}</b></td></tr>
                    <tr><td style="padding-bottom: 1px;">{{trans('ehistorialexam.1.Veniralexamencompletamenteenayunas')}}</td></tr>
                    <tr><td style="padding-bottom: 1px;">{{trans('ehistorialexam.2.Enlascitasporlatarde,nodebeingeriralimentosylíquidos6horasantesdelestudio')}}</td></tr>
                </table>
                <div style="clear:both;height: 1px;">&nbsp;</div> 
                <table class="table table-striped">
                    <tr><td style="padding-bottom: 1px;"><b style="color: #007777;">{{trans('ehistorialexam.Ecografíagineco-obstétrica')}}</b></td></tr>
                    <tr><td style="padding-bottom: 1px;">{{trans('ehistorialexam.1.Pacientesparaecoginecológicauobstétricaconmenora14semanas,noorinar2horasantesdelexamenytomar2botellasde1/2litrodeagua1horaantesdelexamen')}}</td></tr>
                    <tr><td style="padding-bottom: 1px;">{{trans('ehistorialexam.2.Pacientesconembarazomayora14semanas,noorinar1horaantesdelexamen')}}</td></tr>
                </table>
            </div>
            <div class="col-md-12" style="background: #007777;"><b style="color: white;">{{trans('ehistorialexam.OTROSESTUDIOS')}}</b>
            </div> 
            <div class="col-md-12" style="padding: 0px;"> 
                <table class="table table-striped">
                    <tr><td style="padding-bottom: 1px;">{{trans('ehistorialexam.SolicitarelinstructivoenlasecretaríadelIECEDoen')}} www.ieced.com.ec</td></tr>
                </table>
            </div>
            <div style="clear:both;height: 100px;">&nbsp;</div> 
            <div align="center" class="col-md-12" style="padding: 0px;text-align: center;"> 
                <p style="color: #007777">_________________________________</p>
                <p style="color: #007777"><b>{{trans('ehistorialexam.MédicoSolicitante')}}</b></p>
            </div>      

        </div>    

                    
                
    </div>
</div>



