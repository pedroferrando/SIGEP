<?php

#	Configuracion del sistema
require_once ("../../config.php");

#	Funciones
require_once("./efectores_administracion_funciones.php");


#	Carga de bancos
$bancosSql = "Select idbanco, nombre from general.bancos";
$bancosResult = sql($bancosSql);


#	Cuando el formulario fue enviado
if ($_POST["guardar"]) {
	#	Valores desde POST
	$banco = $_POST["banco"];
	$nroCta = $_POST["nroCuenta"];
	$cuie = $_POST["cuie"];
	
	if ($banco == "NOTOSH") {
		$banco = "NULL";
		$nroCta = "";
	}

	#	Objeto Efector
	$efector = new Efector();
	$efector->setCuie($cuie);
	$efector->construirResult(sql($efector->getSqlSelect()));
	$efector->setNroCuenta($nroCta);
	$efector->setBanco($banco);

	#	Actualiza el registro
	sql($efector->getSqlUpdate());

	#	Mensaje de actualizacion
	$mensaje = "Registro actualizado correctamente";

}else{

	#	Valores recibidos por parametros
	$cuie = $parametros["cuie"];
	$efector = new Efector();
	$efector->construirResult(sql($efector->getSqlSelectGenerico("'".$cuie."'", "cuie = ")));
}




#	Cuerpo Html
echo $html_header;

?>


<!-- Librerias Jquery -->
<script src='../../lib/jquery.min.js' type='text/javascript'></script>
	
<!-- Librerias Jquery UI-->
<script src='../../lib/jquery/jquery-ui.js' type='text/javascript'></script>
<link rel="stylesheet" href="../../lib/jquery/ui/jquery-ui.css" />

<div align="center"><h1>Datos del efector: <?=$efector->getNombre()?></h1></div>
<div align="center"><h2><?=$mensaje?></h2></div>
<fieldset>
	<legend>Datos del Efector</legend>
	<form action="./efectores_administracion_edicion.php" method="POST">
		<table width="80%" align="center">
			<tr id="mo">
				<td>Cuie</td>
				<td>Nombre</td>
				<td>Banco</td>
				<td>Nro. de Cuenta</td>
			</tr>

			<tr>
				<td><?=$efector->getCuie()?></td>
				<td><?=$efector->getNombre()?></td>
				<td>
					<select name="banco" id="">
						<option value="NOTOSH">Seleccione</option>
						<?php 

						while (!$bancosResult->EOF) {
							if ($bancosResult->fields['idbanco'] == $efector->getBanco()) {
								$propiedad = "Selected";
							}else{
								$propiedad = "";
							}

							?>
								<option value="<?=$bancosResult->fields['idbanco']?>" <?=$propiedad?>><?=$bancosResult->fields['nombre']?></option>
							<?php
							$bancosResult->MoveNext();
						}
						?>
						
					</select>
				</td>
				<td><input type="text" name="nroCuenta" id="" value="<?=$efector->getNroCuenta()?>"></td>
			</tr>

			<tr id="mo">
				<td>Referente</td>
				<td>Telefono</td>
				<td>Domicilio</td>
			</tr>

			<tr>
				<td><?=$efector->getReferente()?></td>
				<td><?=$efector->getTelefono()?></td>
				<td><?=$efector->getDomicilio()?></td>
			</tr>
		</table>

		<br>
		<br>
		<br>

		<table width="80%" align="center">
			<tr>
				<td>
					<input type="submit" value="Guardar">
					<input type="submit" value="Cancelar">
				</td>
				
			</tr>
		</table>

		<!-- Campo de guardar oculto -->
		<input type="hidden" name="guardar" value="guardar">
		<!-- Campo oculto del cuie que corresponde al efector -->
		<input type="hidden" name="cuie" value="<?=$cuie?>">

	</form>
	<hr>
	<div align="center">* En caso de diferir la informaci&oacute;n aqui detallada, comunicarse al &aacute;rea legal del programa sumar</div>
</fieldset>




<?php
#	Pie Html
echo $html_footer;
?>


