<!doctype html>
<html lang="en">
  <head>
  <title>CERTIFICADO {{$agenda->id_paciente}}</title>

    <style>	
        @page { margin: 20px 20px;}
        #header { position: fixed; left: 20px; top: -10px; right: 20px; height: 150px; text-align: center;}
        #content { position: fixed; left: 20px; top: 200px; right: 20px; height: 150px; }
        #footer { position: fixed; left: 20px; bottom: -90px; right: 20px; height: 150px; }
        p {
        font-family: Comic Sans MS, cursive, sans-serif;
        font-size: 16px;
        text-align:justify;
        font-style: italic;
        }
        .dg > p {
        font-family: Comic Sans MS, cursive, sans-serif;
        font-size: 12px;
        text-align:justify;
        font-style: italic;
        }
  
    </style>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>prueba</title>
  </head>
  <body>
	<form method="post" action="recibido.php">
		<p> Acta de Entrega Informe Médico </p>
		<p> Guayaquil,
			<input type="text" name="fecha"> de 2019
        
		</p>
		<p>Yo, 
			<input type="text" name="nombre" size="25">
			(nombres y apellidos del paciente o acompanante) con número de cédula <input type="text" name="cedula" size="10">, mediante la presente
			dejo constancia que he recibido el informe médico físico y a su vez se me ha explicado de manera detallada y he comprendido toda la información recibida.
		</p>
		<p>
		Adicional se deja constancia de lo siguiente:
		</p>
		<div class="box-body table-responsive no-padding col-md-5">
			<table class="table table-bordered ">
				<thead>
				</thead>
				<tbody>
					<tr>
						<td>Se recibi&oacute biopsia</td>
						<td></td>
					</tr>
					<tr>
						<td>No se requiri&oacute biopsia</td>
						<td></td>
					</tr>
					<tr>
						<td>IECED har&aacute el estudio de la biopsia</td>
						<td></td>
					</tr>
				</tbody>
			</table>
		</div>
        <div style="width:1250px; padding:3px;">
            <div style="width:625px; float:left;">
            
		        <p>____________________</p>
                <p>Firma del Paciente</p>
		        <p> Nombres y Apellidos:</p>
            </div>
            <div style="width:625px; float:right;">
		        <p>_____________________</p>
                <p>Firma del acompanante</p>
		        <p>Nombres y Apellidos:</p>
            </div>
        </div>
	</form>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>