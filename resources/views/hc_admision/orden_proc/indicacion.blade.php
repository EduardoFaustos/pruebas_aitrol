

            <table class="table table-striped" border="2">
                <thead>
                    <th width="1%">{{trans('ehistorialexam.Nro.')}}</th>
                    <th width="99%">{{trans('ehistorialexam.Indicación')}}</th>    
                </thead>
                <tbody>
                @if($indicaciones!='[]')
                @foreach($indicaciones as $value)
                    <tr>
                        <td>{{$value->secuencia}}</td>
                        <td>{{$value->descripcion}}</td>
                    </tr>
                @endforeach
                @endif    
                </tbody>
            </table>