    @if(!is_null($agenda))
        @if($agenda->proc_consul!=2)
        <table id="tbajax" class="table table-bordered table-hover dataTable" >
            <thead>
                <tr>
                    <th>
                       {{trans('edisponibilidad.Paciente:')}}  
                        
                    </th>
                    <td>
                        {{$agenda->paciente->apellido1}}
                        {{$agenda->paciente->nombre1}}
                    </td>
                </tr>
                    <tr>
                        <th>
                       {{trans('edisponibilidad.Doctor:')}}   
                        
                    </th>
                    <td>
                    {{$agenda->doctor1->apellido1}} 
                        {{$agenda->doctor1->nombre1}}  
                    </td> 
                    </tr>
                <tr>
                    <th>
                        {{trans('edisponibilidad.Consulta/Procedimiento:')}} 
                        
                    </th>   
                    <td>
                        @if($agenda->pro_consult==0)
                       {{trans('edisponibilidad.Consulta')}}  
                        @elseif($agenda->pro_consult==1)
                        {{$agenda->Procedimiento->nombre}}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>
                       {{trans('edisponibilidad.Observaciones:')}}   
                    </th>
                    <td>
                        {{$agenda->observacion_proc}}
                    </td>
                </tr>
                
            </thead>
        </table>
        @else
        @php
        $salas = DB::table('sala')->where('id', '=', $agenda->id_sala)->first();
        @endphp
        <table id="tbajax" class="table table-bordered table-hover dataTable" >
            <thead>
                    <tr>
                        <th>
                        {{trans('edisponibilidad.Doctor:')}} 
                        
                    </th>
                    <td>
                    {{$agenda->doctor1->apellido1}} 
                        {{$agenda->doctor1->nombre1}}  
                    </td> 
                    </tr>
                    <tr>
                        <th>
                        {{trans('edisponibilidad.Fechadeinicio:')}} 
                        
                    </th>
                    <td>
                    {{$agenda->fechaini}} 
                    
                    </td> 
                    </tr>
                    <tr>
                        <th>
                        {{trans('edisponibilidad.Fechadefinal:')}} 
                        
                    </th>
                    <td>
                    {{$agenda->fechafin}} 
                    
                    </td> 
                    </tr>

            
                <tr>
                    <th>
                        {{trans('edisponibilidad.Ubicación:')}} 
                    </th>
                    <td>
                        {{$salas->nombre_sala}}
                    </td>
                </tr>
                
            </thead>
        </table>
        @endif
        
    @else

    @endif