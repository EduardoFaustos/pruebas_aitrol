<!DOCTYPE html>
<html>
<head>
	<title>PLANILLA PUBLICA</title>
	<style>
		@page {
                margin: 0 0;
            }	
        body {
            margin-top:     0.5cm;
            margin-left:    0.5cm;
            margin-right:   0.5cm;
            margin-bottom:  0.5cm;
			font-size: 9px;
        }  
        header {
                position: fixed;
                top:        0cm;
                left:       1cm;
                right:      0cm;
                height:     3cm;
            	align-content:  center;
            }    	

        .table{
        	
        	margin-right:0px;
        	margin-top: 0px; 
        	margin-left: 0px; 
        	margin-bottom: 0px; 
        	font-size: 10px;
        	width: 100%;

        }  
        .number{
        
        	padding-right: 10px;
        	text-align:right;
        }  

        /** Define the footer rules **/
        footer {
            position: fixed; 
            bottom:     0cm; 
            left:       0cm; 
            right:      0cm;
            height:     2cm;
        } 
        .ftr{
                height:     2cm;  
            } 
        div{
        	padding: 1px;
        }    
        .css_descripcion{
        	font-size: 8px;
       	}  
       	.css_fecha{
       		font-size: 8px;
       	}
       	.titulo_css{

       		text-align: center;
       		background-color: #b3b3b3;
       	}	
       	.total_css{

       		text-align: center;
  			font-weight: bold;
       	}	
	</style>
       
</head>
<body>
	<header>
		
	</header>
	<footer>
        <div style="text-align: center;width: 100%;" class="ftr">
            
        </div>
    </footer>

	<main>                
		<table border="1" cellspacing="0" cellpadding="0" class="table">
			<tbody>
				<tr >
					<td colspan="3"> <b>Nombre del Prestador :</b> </td>
					<td colspan="7">{{ $archivo_plano->empresa->razonsocial }}</td>				
				</tr>
				<tr>
					<td colspan="3"> <b>Seguro</b> </td>
					<td colspan="7">INSTITUTO ECUATORIANO DE SEGURIDAD SOCIAL</td>
						
				</tr>
				<tr>
					<td colspan="3"><b>Nombre del Paciente</b></td>	
					<td colspan="7">{{ $archivo_plano->paciente->apellido1 . ' ' . $archivo_plano->paciente->apellido2 . ' ' . $archivo_plano->paciente->nombre1 . ' ' . $archivo_plano->paciente->nombre2 }}</td>
				</tr>
				<tr>
					<td colspan="3"><b>Cedula de identidad</b></td>	
					<td colspan="7">{{ $archivo_plano->id_paciente }}</td>
				</tr>
				<tr>
					<td colspan="3"><b>Historia Clinica</b></td>	
					<td colspan="7">{{ $archivo_plano->id_paciente }}</td>
				</tr>
				<tr>
					<td colspan="3"><b>Fecha de Ingreso</b></td>	
					<td colspan="7">{{ substr($archivo_plano->fecha_ing, 0, 10) }}</td>
				</tr>
				<tr>
					<td colspan="3"><b>Fecha de Egreso</b></td>	
					<td colspan="7">{{ substr($archivo_plano->fecha_alt, 0, 10) }}</td>
				</tr>
				<tr>
					<td colspan="3"><b>Procedimiento</b></td>	
					<td colspan="7">{{ $archivo_plano->nom_procedimiento }}</td>
				</tr>
				<tr>
					<td colspan="3"><b>Diagnostico</b></td>	
					<td colspan="7">{{ $txt_cie10 }}</td>
				</tr>
				<tr>
				<th colspan="10" style="text-align: center;">PLANILLA DE CARGOS DEL PROVEEDOR(CONSULTA EXTERNA, HOSPITALIZACION Y EMERGENCIA)</th>
			    </tr>	
			    
			</tbody>
		</table>	
		<table border="1" cellspacing="0" cellpadding="0" class="table">
			<thead>
			    <tr>
				   <th colspan="9" class="titulo_css" >HONORARIOS MEDICOS</th>
				</tr>
			</thead>
			<tbody>
				<tr>
				   <td width="5%">Fecha</td>
				   <td width="5%">Codigo</td>
				   <td width="30%">Descripcion</td>
				   <td width="5%">Cantidad</td>
				   <td width="10%">Valor Unitario</td>
				   <td width="10%">Subtotal</td>
				   <td width="10%">10%</td>
				   <td width="10%">Iva</td>
				   <td width="10%">Total</td>
			    </tr>
			  	@php $total = 0; @endphp
			    @foreach ($honor_medicos as $value)
			    	@php $total += $value->total; @endphp
                    <tr>
                    	<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
						<td >{{ $value->codigo }}</td>
						<td class="css_descripcion">{{ $value->descripcion }}</td>
						<td class="number">{{ $value->cantidad }}</td>
						<td class="number">${{ number_format($value->valor, 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($value->subtotal, 2), 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($value->porcentaje10, 2), 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($value->iva, 2), 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($value->total, 2), 2, ',', ' ') }}</td>
					</tr>	
	            @endforeach            
			    <tr>
			    	<td colspan="4" class="total_css">Total</td>
			    	<td colspan="4" class="total_css">HONORARIOS MEDICOS</td>
			    	<td colspan="1" class="number">${{ number_format($total, 2, ',', ' ') }}</td>
			    </tr>
			</tbody>
		</table>	
		<table border="1" cellspacing="0" cellpadding="0" class="table">	
			<thead>
			    <tr>
				   <th colspan="9" class="titulo_css">MEDICINAS - VALOR AL ORIGEN</th>
				</tr>
			</thead>
			<tbody>
				<tr>
				   <td width="5%">Fecha</td>
				   <td width="5%">Codigo</td>
				   <td width="30%">Descripcion</td>
				   <td width="5%">Cantidad</td>
				   <td width="10%">Valor Unitario</td>
				   <td width="10%">Subtotal</td>
				   <td width="10%">10%</td>
				   <td width="10%">Iva</td>
				   <td width="10%">Total</td>
			    </tr>
			    @php $total2 = 0; @endphp
			    @foreach ($medicinas as $value)
			    	@php $total2 += $value->total; @endphp
                    <tr>
                    	<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
						<td ></td>
						<td class="css_descripcion">{{ $value->descripcion }}</td>
						<td class="number">{{ $value->cantidad }}</td>
						<td class="number">${{ number_format($value->valor, 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($value->subtotal, 2), 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($value->porcentaje10, 2), 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($value->iva, 2), 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($value->total, 2), 2, ',', ' ') }}</td>
					</tr>	
	            @endforeach
			    <tr>
			    	<td width="5%" colspan="4" class="total_css">Total</td>
			    	<td width="5%" colspan="4" class="total_css">MEDICINAS - VALOR AL ORIGEN</td>
			    	<td width="5%" colspan="1" class="number">${{ number_format($total2, 2, ',', ' ') }}</td>
			    </tr>
			</tbody>
		</table>	
		<table border="1" cellspacing="0" cellpadding="0" class="table">	
			<thead>
			    <tr>
				   <th colspan="9" class="titulo_css">INSUMOS - VALOR AL ORIGEN</th>
				</tr>
			</thead>
			<tbody>
				<tr>
				   <td width="5%">Fecha</td>
				   <td width="5%">Codigo</td>
				   <td width="30%">Descripcion</td>
				   <td width="5%">Cantidad</td>
				   <td width="10%">Valor Unitario</td>
				   <td width="10%">Subtotal</td>
				   <td width="10%">10%</td>
				   <td width="10%">Iva</td>
				   <td width="10%">Total</td>
			    </tr>
			    @php $totalins = 0; @endphp
			    @foreach ($insumos as $value)
			    	@php 
			    		$total_item = round($value->subtotal, 2) + round($value->porcentaje10, 2) + ($value->subtotal * $value->porcentaje_iva);
			    		$totalins += $total_item; 
			    	@endphp
                    <tr>
                    	<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
						<td ></td>
						<td class="css_descripcion">{{ $value->descripcion }}</td>
						<td class="number">{{ $value->cantidad }}</td>
						<td class="number">${{ number_format($value->valor_unitario, 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($value->subtotal, 2), 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($value->porcentaje10, 2), 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($value->subtotal * $value->porcentaje_iva, 2), 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($total_item, 2), 2, ',', ' ') }}</td>
					</tr>	
	            @endforeach
			    <tr>
			    	<td width="5%" colspan="4" class="total_css">Total</td>
			    	<td width="5%" colspan="4" class="total_css">INSUMOS - VALOR AL ORIGEN</td>
			    	<td width="5%" colspan="1" class="number">${{ number_format($totalins, 2, ',', ' ') }}</td>
			    </tr>
			</tbody>
		</table>	
		<table border="1" cellspacing="0" cellpadding="0" class="table">	
			<thead>
			    <tr>
				   <th colspan="9" class="titulo_css">LABORATORIO</th>
				</tr>
			</thead>
			<tbody>
				<tr>
				   <td width="5%">Fecha</td>
				   <td width="5%">Codigo</td>
				   <td width="30%">Descripcion</td>
				   <td width="5%">Cantidad</td>
				   <td width="10%">Valor Unitario</td>
				   <td width="10%">Subtotal</td>
				   <td width="10%">10%</td>
				   <td width="10%">Iva</td>
				   <td width="10%">Total</td>
			    </tr>
			    @php $total_lab = 0; @endphp
			    @foreach ($laboratorio as $value)
			    	@php 
			    		$total_lab += $value->total; 
			    	@endphp
                    <tr>
                    	<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
						<td >{{ $value->codigo }}</td>
						<td class="css_descripcion">{{ $value->descripcion }}</td>
						<td class="number">{{ $value->cantidad }}</td>
						<td class="number">${{ number_format($value->valor, 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($value->subtotal, 2), 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($value->porcentaje10, 2), 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($value->iva, 2), 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($value->total, 2), 2, ',', ' ') }}</td>
					</tr>	
	            @endforeach
			    <tr>
			    	<td width="5%" colspan="4" class="total_css">Total</td>
			    	<td width="5%" colspan="4" class="total_css">LABORATORIO</td>
			    	<td width="5%" colspan="1" class="number">${{ number_format($total_lab, 2, ',', ' ') }}</td>
			    </tr>
			</tbody>
		</table>	
		<table border="1" cellspacing="0" cellpadding="0" class="table">	
			<thead>
			    <tr>
				   <th colspan="9">IMAGEN(*)</th>
				</tr>
			</thead>
			<tbody>
				<tr>
				   <td width="5%">Fecha</td>
				   <td width="5%">Codigo</td>
				   <td width="30%">Descripcion</td>
				   <td width="5%">Cantidad</td>
				   <td width="10%">Valor Unitario</td>
				   <td width="10%">Subtotal</td>
				   <td width="10%">10%</td>
				   <td width="10%">Iva</td>
				   <td width="10%">Total</td>
			    </tr>
			    @php $total_imagen = 0; @endphp
			    @foreach ($imagen as $value)
			    	@php 
			    		$total_imagen += $value->total; 
			    	@endphp
                    <tr>
                    	<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
						<td >{{ $value->codigo }}</td>
						<td class="css_descripcion">{{ $value->descripcion }}</td>
						<td class="number">{{ $value->cantidad }}</td>
						<td class="number">${{ number_format($value->valor, 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($value->subtotal, 2), 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($value->porcentaje10, 2), 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($value->iva, 2), 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($value->total, 2), 2, ',', ' ') }}</td>
					</tr>	
	            @endforeach
			    <tr>
			    	<td width="5%" colspan="4" class="total_css">Total</td>
			    	<td width="5%" colspan="4" class="total_css">IMAGEN(*)</td>
			    	<td width="5%" colspan="1" class="number">${{ number_format($total_imagen, 2, ',', ' ') }}</td>
			    </tr>
			</tbody>
		</table>	
		<table border="1" cellspacing="0" cellpadding="0" class="table">
			<thead>
			    <tr>
				   <th colspan="9" class="titulo_css">SERVICIOS INSTITUCIONALES</th>
				</tr>
			</thead>
			<tbody>
				<tr>
				   <td width="5%">Fecha</td>
				   <td width="5%">Codigo</td>
				   <td width="30%">Descripcion</td>
				   <td width="5%">Cantidad</td>
				   <td width="10%">Valor Unitario</td>
				   <td width="10%">Subtotal</td>
				   <td width="10%">10%</td>
				   <td width="10%">Iva</td>
				   <td width="10%">Total</td>
			    </tr>
			    @php $total_serv = 0; @endphp
			    @foreach ($servicios_ins as $value)
			    	@php 
			    		$total_serv += $value->total; 
			    	@endphp
                    <tr>
                    	<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
						<td >{{ $value->codigo }}</td>
						<td class="css_descripcion">{{ $value->descripcion }}</td>
						<td class="number">{{ $value->cantidad }}</td>
						<td class="number">${{ number_format($value->valor, 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($value->subtotal, 2), 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($value->porcentaje10, 2), 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($value->iva, 2), 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($value->total, 2), 2, ',', ' ') }}</td>
					</tr>	
	            @endforeach
			    <tr>
			    	<td width="5%" colspan="4" class="total_css">Total</td>
			    	<td width="5%" colspan="4" class="total_css">SERVICIOS INSTITUCIONALES</td>
			    	<td width="5%" colspan="1" class="number">${{ number_format($total_serv, 2, ',', ' ') }}</td>
			    </tr>
			</tbody>
		</table>
		<table border="1" cellspacing="0" cellpadding="0" class="table">
			<thead>
			    <tr>
				   <th colspan="9" class="titulo_css">EQUIPOS ESPECIALES</th>
				</tr>
			</thead>
			<tbody>
				<tr>
				   <td width="5%">Fecha</td>
				   <td width="5%">Codigo</td>
				   <td width="30%">Descripcion</td>
				   <td width="5%">Cantidad</td>
				   <td width="10%">Valor Unitario</td>
				   <td width="10%">Subtotal</td>
				   <td width="10%">10%</td>
				   <td width="10%">Iva</td>
				   <td width="10%">Total</td>
			    </tr>
			    @php $total_equi = 0; @endphp
			    @foreach ($equipos as $value)
			    	@php 
			    		$total_equi += $value->total; 
			    	@endphp
                    <tr>
                    	<td class="css_fecha">{{ substr($value->fecha, 0, 10) }}</td>
						<td >{{ $value->codigo }}</td>
						<td class="css_descripcion">{{ $value->descripcion }}</td>
						<td class="number">{{ $value->cantidad }}</td>
						<td class="number">${{ number_format($value->valor, 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($value->subtotal, 2), 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($value->porcentaje10, 2), 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($value->iva, 2), 2, ',', ' ') }}</td>
						<td class="number">${{ number_format(round($value->total, 2), 2, ',', ' ') }}</td>
					</tr>	
	            @endforeach
			    <tr>
			    	<td width="5%" colspan="4" class="total_css">Total</td>
			    	<td width="5%" colspan="4" class="total_css">EQUIPOS ESPECIALES</td>
			    	<td width="5%" colspan="1" class="number">${{ number_format($total_equi, 2, ',', ' ') }}</td>
			    </tr>
			    @php 
			    	$liquidacion_total = $total_equi + $total_serv + $total_imagen + $total_lab + $totalins + $total2 + $total;	
			    @endphp
			    <tr>
			    	<td width="5%" colspan="8" class="total_css">TOTAL LIQUIDACION</td>
			    	<td width="5%" colspan="1" class="number">${{ number_format($liquidacion_total, 2, ',', ' ') }}</td>
			    </tr>
			</tbody>
		</table>
	
	</main>
	
	
	
</body>
</html>	