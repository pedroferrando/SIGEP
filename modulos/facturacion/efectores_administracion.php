<?php 
	#	Configuracion del sistema
	require_once ("../../config.php");

	#	Funciones
	require_once("./efectores_administracion_funciones.php");

	#	Usuario loggeado
	$usuario = $_ses_user["id"];

	#	Listado de efectores por usuario
	$usuarioSql = "select * from sistema.usu_efec where id_usuario = ".$usuario."";
	$usuarioResult = sql($usuarioSql);


	#	Listado de efectores
	$efectores = array();
	$efectoresMenu = array();

	#	Parametros por POST
	$filtroFactor = $_POST['filtroFactor'];

	#	Listado de efectores para menu
	while (!$usuarioResult->EOF) {
		#	Efector inicial
		$efector = new Efector();
		$efector->setCuie($usuarioResult->fields["cuie"]);
		$efector->construirResult(sql($efector->getSqlSelect()));
		$efectoresMenu[] = $efector;
		$usuarioResult->MoveNext();
	}


	#	Filtrado por busqueda
	if ($_POST["busqueda"]) {
		if ($filtroFactor == "-1") {
			$efectores = $efectoresMenu;
		}else{
			$efector = new Efector();
			$efector->setCuie($filtroFactor);
			$efector->construirResult(sql($efector->getSqlSelect()));
			$efectores[] = $efector;
		}

	}else{
		#	Si la busqueda no fue realizada
		$efectores = $efectoresMenu;
	}

	echo $html_header;

 ?>

	<!-- Librerias Jquery -->
	<script src='../../lib/jquery.min.js' type='text/javascript'></script>
	
	<!-- Librerias Jquery UI-->
	<script src='../../lib/jquery/jquery-ui.js' type='text/javascript'></script>
	<link rel="stylesheet" href="../../lib/jquery/ui/jquery-ui.css" />



<body>
	<fieldset>
		<legend>Consola de busqueda</legend>
	
		<div width="80%" align="center">
			<form action="./efectores_administracion.php" name="busqueda" method="POST">
				<table>
					<tr>

						<td colspan="4">Efector: <select name="filtroFactor" id="">
							<option value="-1">Todos</option>
							<?php 

							foreach ($efectoresMenu as $efect) {
								#	Predispone la propiedad de seleccion
								if ($filtroFactor == $efect->getCuie()) {
									$propiedad = "Selected";
								}else{
									$propiedad = "";
								}

								?>
								
								<option value="<?=$efect->getCuie()?>" <?=$propiedad?>><?=$efect->getPresentacion()?></option>

								<?php
							}
						 	?>

						</select></td>
					</tr>
					<tr>
						<td colspan="2"><input type="submit" value="Filtrar"></td>
						<!-- <td colspan="2"><input type="reset" value="Borrar"></td> -->
					</tr>
				</table>
				<input type="hidden" name="busqueda" value="busqueda">
			</form>
		</div>
	</fieldset>

	<br />

	<div width="90%" aign="center">
		<table cellspacing="2px" cellpadding="2px" border="0px" width="90%" align="center">
			<tr id="mo">
				<td>Cuie</td>
				<td>Nombre</td>
				<td>Banco</td>
				<td>Nro. de Cuenta</td>
				<td>Editar</td>
				
			</tr>
			
				<?php 
					if ($_POST['busqueda']) {
						#	Inicializa el color de la tabla
						$trBgColor = "#C1DAD6";
						?>
							<?php 

							foreach ($efectores as $efect) {
								$link = encode_link("efectores_administracion_edicion.php", 
									array("cuie" => $efect->getCuie()));
								$caller = "window.open('".$link."')";
							?>
							<tr bgcolor="<?=$trBgColor?>">
								<td><?=$efect->getCuie()?></td>
								<td><?=$efect->getNombre()?></td>
								<td><?=$efect->getNombreBanco()?></td>
								<td><?=$efect->getNroCuenta()?></td>
								<td align="center"><input type="button" value="Editar" onclick="<?=$caller?>"></td>
							</tr>
						<?php
								if ($trBgColor == "#F5FAFA") {
						        	$trBgColor = "#C1DAD6";
						        }else{  
						        	$trBgColor = "#F5FAFA";   
						        }
							} 
					}else{
						?>
							<tr id="mo">
								<td colspan="7">Nada para mostrar</td>
							</tr>
						<?php
					}
				 ?>

			
		</table>
	</div>

</body>
</html>