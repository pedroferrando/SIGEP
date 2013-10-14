<?
require_once ("../../config.php");

extract($_POST, EXTR_SKIP);

if ($parametros) {
	extract($parametros, EXTR_OVERWRITE);
}



#   Parametros POST
($_POST['fechaempadronamiento'] == '') ? '' : $fechaempadronamiento = fecha_db($_POST['fechaempadronamiento']);
($_POST['num_form_remediar'] == '') ? '' : $num_form_remediar = $_POST['num_form_remediar'];
($_POST['clavebeneficiario'] == '') ? '' : $clave_beneficiario = $_POST['clavebeneficiario'];
($_POST['puntaje_final'] == '') ? '' : $puntaje_final = $_POST['puntaje_final'];




#   Carga de calendario
cargar_calendario();

#    Clases
require_once ("../../clases/remediar/remediar.php");
require_once ("../../clases/beneficiarios.php");


#   Id del usuario
$user_cuie = substr($_ses_user['login'], 0, 6);


$desabil_guardar = '';



$beneficiario = new Beneficiario();
$beneficiario->Automata("clave_beneficiario = '".$clave_beneficiario."'");

$empadronamiento = new Empadronamiento();
$empadronamiento->Automata("clavebeneficiario = '".$beneficiario->getClave_beneficiario()."'");





if ($_POST['guardar_editar'] == "Guardar") {

	#   datosAgente[0] = Apellido, datosAgente[1] = Nombre
	$datosAgente = explode("-", $_POST['datosAgente']);
	$apellidoagente = $datosAgente[0];
	$nombreagente = $datosAgente[1];
	$num_doc_agente = $_POST['num_doc_agente'];


	$fecha_carga = date("Y-m-d");
	$usuario = $_ses_user['id'];
	$usuario = substr($usuario, 0, 9);
	$clave_beneficiario = $_POST['clavebeneficiario'];
	$edad = $_POST['edad'];
	$sexo = $_POST['sexo'];
	$fecha_nac = $_POST['fecha_nac'];
	$num_form_remediar = $_POST['num_form_remediar'];
	$puntaje_final = $_POST['puntaje_final'];
	$fechaempadronamiento = Fecha_db($_POST['fechaempadronamiento']);
		
	
	$cuie = $_POST['cuie'];
	$os = $_POST['os'];
	$cual_os = $_POST['cual_os'];

	if (rtrim($_POST['factorriesgo']) != '') {
		$factorriesgo = $_POST['factorriesgo'];
	}
	
	else {
		$accion = "No se pudo calcular el Factor de Riesgo.";
		echo "<SCRIPT Language='Javascript'>
						location.href='" . encode_link("remediar_admin.php", array("estado_envio" => $estado_envio, "clave_beneficiario" => $clave_beneficiario, "sexo" => $sexo, "fecha_nac" => $fecha_nac, "edad" => $edad, "vremediar" => 'n', "accion" => $accion)) . "';
			 </SCRIPT>";
	}
	
	$factorriesgo_p = $_POST['puntos_1'];
	if ($factorriesgo_p == '') {
		$factorriesgo_p = 0;
	}

	list($hta2, $p1) = explode("_", $_POST['hta2']);
	if ($hta2 == '') {
		$hta2 = 0;
		$p1 = 0;
	}

	list($hta3, $p2) = explode("_", $_POST['hta3']);
	if ($hta3 == '') {
		$hta3 = 0;
		$p2 = 0;
	}
	list($colesterol4, $p3) = explode("_", $_POST['colesterol4']);

	if ($colesterol4 == '') {
		$colesterol4 = 0;
		$p3 = 0;
	}

	list($colesterol5, $p4) = explode("_", $_POST['colesterol5']);
	if ($colesterol5 == '') {
		$colesterol5 = 0;
		$p4 = 0;
	}

	list($dmt26, $p5) = explode("_", $_POST['dmt26']);

	if ($dmt26 == '') {
		$dmt26 = 0;
		$p5 = 0;
	}
	list($dmt27, $p6) = explode("_", $_POST['dmt27']);
	if ($dmt27 == '') {
		$dmt27 = 0;
		$p6 = 0;
	}

	list($tabaco9, $p7) = explode("_", $_POST['tabaco9']);
	if ($tabaco9 == '') {
		$tabaco9 = 0;
		$p7 = 0;
	}

	list($ecv8, $p8) = explode("_", $_POST['ecv8']);
	if ($ecv8 == '') {
		$ecv8 = 0;
		$p8 = 0;
	}
	if ($puntaje_final == '') {
		$puntaje_final = $factorriesgo_p + $p1 + $p2 + $p3 + $p4 + $p5 + $p6 + $p7 + $p8;
	}

	if ($num_form_remediar == "0") {
		$cuie = $res_remediar->fields['cuie_ea'];
	}

	if($empadronamiento->enPadron()){
		$tipoTransaccion = "Registro Editado";

		$formulario = new Formulario();

		$formulario->setId_formulario($empadronamiento->formularioGetId_formulario());
		$formulario->setNroformulario($empadronamiento->formularioGetNroformulario());
		$formulario->setFactores_riesgo($factorriesgo);
		$formulario->setHta2($hta2);
		$formulario->setHta3($hta3);
		$formulario->setColesterol4($colesterol4);
		$formulario->setColesterol5($colesterol5);
		$formulario->setDmt26($dmt26);
		$formulario->setDmt27($dmt27);
		$formulario->setEcv8($ecv8);
		$formulario->setTabaco9($tabaco9);
		$formulario->setPuntaje_final($puntaje_final);
		$formulario->setApellidoagente($apellidoagente);
		$formulario->setNombreagente($nombreagente);
		$formulario->setCentro_inscriptor($cuie);
		$formulario->setOs($os);
		$formulario->setDni_agente($num_doc_agente);
		$formulario->setCual_os($cual_os);

		$formulario->Validar();


		$puntaje_final = $empadronamiento->formularioGetPuntaje_final();
		$nroFormRemediar = $empadronamiento->formularioGetNroformularioPresentacion();
		#$fechaempadronamiento = $empadronamiento->getFechaempadronamiento();

		$empadronamiento->setNroformulario($formulario->getNroformulario());
		$empadronamiento->setFechaempadronamiento($fechaempadronamiento);
		$empadronamiento->setClavebeneficiario($beneficiario->getClave_beneficiario());
		$empadronamiento->setUsuario_carga($usuario);

		#$empadronamiento->setFecha_carga($fecha_carga);

		$empadronamiento->ActualizarCBenef();
		$formulario->Actualizar();
		$beneficiario->pasarAPendiente();

	}

	# Nuevo Beneficiario
	else{
		$empadronamiento = new Empadronamiento();
		$tipoTransaccion = "Nuevo Registro Guardado";

		$formulario = new Formulario();

		$formulario->setId_formulario($Id_formulario);
		$formulario->setNroformulario($num_form_remediar);
		$formulario->setFactores_riesgo($factorriesgo);
		$formulario->setHta2($hta2);
		$formulario->setHta3($hta3);
		$formulario->setColesterol4($colesterol4);
		$formulario->setColesterol5($colesterol5);
		$formulario->setDmt26($dmt26);
		$formulario->setDmt27($dmt27);
		$formulario->setEcv8($ecv8);
		$formulario->setTabaco9($tabaco9);
		$formulario->setPuntaje_final($puntaje_final);
		$formulario->setApellidoagente($apellidoagente);
		$formulario->setNombreagente($nombreagente);
		$formulario->setCentro_inscriptor($cuie);
		$formulario->setOs($os);
		$formulario->setDni_agente($num_doc_agente);
		$formulario->setCual_os($cual_os);


		# SIGUIENTE NRO FORMULARIO
		$formulario->NuevoNumero();

		$formulario->Validar();

		$empadronamiento->setNroformulario($formulario->getNroformulario());
		$empadronamiento->setFechaempadronamiento($fechaempadronamiento);
		$empadronamiento->setClavebeneficiario($beneficiario->getClave_beneficiario());
		$empadronamiento->setUsuario_carga($usuario);
		$empadronamiento->setFecha_carga($fecha_carga);


		$empadronamiento->Insertar();
		$formulario->Insertar();





		$empadronamiento->Automata("clavebeneficiario = '".$beneficiario->getClave_beneficiario()."'");
		$nroFormRemediar =$empadronamiento->formularioGetNroformularioPresentacion();
		$fechaempadronamiento = $empadronamiento->getFechaempadronamiento();
		$puntaje_final = $empadronamiento->formularioGetPuntaje_final();

	}
}
else{

	if($empadronamiento->enPadron()){
		#Traer los datos del beneficiario

		$nroFormRemediar = $empadronamiento->formularioGetNroformularioPresentacion();
		$fechaempadronamiento = $empadronamiento->getFechaempadronamiento();
		$puntaje_final = $empadronamiento->formularioGetPuntaje_final();

	}else{
		# Nuevo Beneficiario
		$nroFormRemediar = "Aun no generado";
	}


}



echo "<SCRIPT Language='Javascript'>
			function nuevo_remediar(){
			location.href='" . encode_link("remediar_admin.php", array("estado_envio" => 'p', "clave_beneficiario" => $clave_beneficiario, "sexo" => $sexo, "fecha_nac" => $fecha_nac, "edad" => 0, "vremediar" => 'n')) . "';
		}
	 </SCRIPT>";





#   Sql de promotores, desde la tabla de promotores
$promotores_sql = "select TRIM(nombre) as nombre,TRIM(apellido) as apellido,dni from remediar.promotores order by apellido";
$promotores_result = sql($promotores_sql) or die();

#   Carga de promotores



#   Header HTML
echo $html_header;
?>


<!-- Importacion de librerias para interaccion grafica y de funcionamiento en HTML -->
<script src='../../lib/jquery.min.js' type='text/javascript'></script>
<script src='../../lib/jquery/jquery-ui.js' type='text/javascript'></script>
<link rel="stylesheet" href="../../lib/jquery/ui/jquery-ui.css" />
<!-- ---------------------------------------------------------------------------- -->



<!-- Funciones para interaccion grafica y funcionamiento en HTML-->
<script>
	$(function() {
		 $( "#accordion" ).accordion({
			heightStyle: "content"
		});
	});
</script>
<!-- ---------------------------------------------------------------------------- -->


<script>
	//controlan que ingresen todos los datos necesarios par el muleto
	function control_nuevos()
	{
		
		if(document.all.fechaempadronamiento.value==""){
			alert("Debe completar el campo fecha de empadronamiento");
			return false;
		}
		if(document.all.num_form_remediar.value!=""){
			var edad=form1.edad.value;
		 
			if(document.all.factorriesgo.value=="" || document.all.factorriesgo.value.replace(/^\s*|\s*$/g,"") ==""){
				alert("No se calculo Factor de Riesgo");
				return false;
			}
		   
			if(edad>20){
				if(document.all.hta2.value=="-1"){
					alert("Debe completar el campo HTA 2)");
					document.all.hta2.focus();
					return false;
				}
			}
			if(document.all.hta3.value=="-1"){
				alert("Debe completar el campo HTA 3)");
				document.all.hta3.focus();
				return false;
			}
			if(edad>20){
				if(document.all.colesterol4.value=="-1"){
					alert("Debe completar el campo COLESTEROL 4)");
					document.all.colesterol4.focus();
					return false;
				}
			}
			if(document.all.colesterol5.value=="-1"){
				alert("Debe completar el campo COLESTEROL 5)");
				document.all.colesterol5.focus();
				return false;
			}
			if(edad>40){
				if(document.all.dmt26.value=="-1"){
					alert("Debe completar el campo DMT2 6)");
					document.all.dmt26.focus();
					return false;
				}
			}
			if(document.all.dmt27.value=="-1"){
				alert("Debe completar el campo DMT2 7)");
				document.all.dmt27.focus();
				return false;
			}
			if(document.all.ecv8.value=="-1"){
				alert("Debe completar el campo ECV 8)");
				document.all.ecv8.focus();
				return false;
			}
			if(document.all.tabaco9.value=="-1"){
				alert("Debe completar el campo TABACO 9)");
				document.all.tabaco9.focus();
				return false;
			}
			if(document.all.cuie.value=="-1"){
				alert("Debe elegir un centro inscriptor");
				document.all.cuie.focus();
				return false;
			}

			if(document.all.datosAgente.value == "NOTOSH"){
				alert("Debe seleccionar un agente inscriptor");
				document.all.datosAgente.focus();
				return false;
			}

			if(document.all.num_doc_agente.value == "NOTOSH"){
				alert("Debe seleccionar un dni para el agente inscriptor");
				document.all.num_doc_agente.focus();
				return false;
			}


		}
	
	}

	/**********************************************************/
	//funciones para busqueda abreviada utilizando teclas en la lista que muestra los clientes.
	var digitos=10; //cantidad de digitos buscados
	var puntero=0;
	var buffer=new Array(digitos); //declaraci?n del array Buffer
	var cadena="";

	function buscar_combo(obj)
	{
		var letra = String.fromCharCode(event.keyCode)
		if(puntero >= digitos)
		{
			cadena="";
			puntero=0;
		}
		//sino busco la cadena tipeada dentro del combo...
		else
		{
			buffer[puntero]=letra;
			//guardo en la posicion puntero la letra tipeada
			cadena=cadena+buffer[puntero]; //armo una cadena con los datos que van ingresando al array
			puntero++;

			//barro todas las opciones que contiene el combo y las comparo la cadena...
			//en el indice cero la opcion no es valida
			for (var opcombo=1;opcombo < obj.length;opcombo++){
				if(obj[opcombo].text.substr(0,puntero).toLowerCase()==cadena.toLowerCase()){
					obj.selectedIndex=opcombo;break;
				}
			}
		}//del else de if (event.keyCode == 13)
		event.returnValue = false; //invalida la acci?n de pulsado de tecla para evitar busqueda del primer caracter
	}//de function buscar_op_submit(obj)

	//Validar Fechas
	function esFechaValida(fecha){
		if (fecha != undefined && fecha.value != "" ){
			if (!/^\d{2}\/\d{2}\/\d{4}$/.test(fecha.value)){
				alert("formato de fecha no v\ufffdlido (dd/mm/aaaa)");
				return false;
			}
			var dia  =  parseInt(fecha.value.substring(0,2),10);
			var mes  =  parseInt(fecha.value.substring(3,5),10);
			var anio =  parseInt(fecha.value.substring(6),10);
 
			switch(mes){
				case 1:
				case 3:
				case 5:
				case 7:
				case 8:
				case 10:
				case 12:
					numDias=31;
					break;
				case 4: case 6: case 9: case 11:
								numDias=30;
								break;
							case 2:
								if (comprobarSiBisisesto(anio)){ numDias=29 }else{ numDias=28};
								break;
							default:
								alert("Fecha introducida err�nea");
								return false;
						}
 
						if (dia>numDias || dia==0){
							alert("Fecha introducida err�nea");
							return false;
						}
						return true;
					}
				}
				var patron = new Array(2,2,4)
				var patron2 = new Array(5,16)
				function mascara(d,sep,pat,nums){
					if(d.valant != d.value){
						val = d.value
						largo = val.length
						val = val.split(sep)
						val2 = ''
						for(r=0;r<val.length;r++){
							val2 += val[r]
						}
						if(nums){
							for(z=0;z<val2.length;z++){
								if(isNaN(val2.charAt(z))){
									letra = new RegExp(val2.charAt(z),"g")
									val2 = val2.replace(letra,"")
								}
							}
						}
						val = ''
						val3 = new Array()
						for(s=0; s<pat.length; s++){
							val3[s] = val2.substring(0,pat[s])
							val2 = val2.substr(pat[s])
						}
						for(q=0;q<val3.length; q++){
							if(q ==0){
								val = val3[q]

							}
							else{
								if(val3[q] != ""){
									val += sep + val3[q]
								}
							}
						}
						d.value = val
						d.valant = val
					}
				}


	function SincronizarPromotores(selectObj){
		var idx = selectObj.selectedIndex; 
		$('#datosAgente option')[idx].selected = true;
		$('#num_doc_agente option')[idx].selected = true;
	}


</script>

<div align="center" id="mo"><h2>Programa Nacional Remediar + Redes</h2></div>

<form name='form1' action='remediar_admin.php' method='POST'>
	<input type="hidden" value="<?= $beneficiario->getClave_beneficiario() ?>" name="clavebeneficiario">
	<input type="hidden" value="<?= $beneficiario->getClave_beneficiario() ?>" name="clave_beneficiario">
	<input type="hidden" value="<?= $beneficiario->getFecha_nacimiento_benef() ?>" name="fecha_nac">
	<input type="hidden" value="<?= $beneficiario->getSexo() ?>" name="sexo">
	<input type="hidden" value="<?= $vremediar ?>" name="vremediar">
	<input type="hidden" value="<?= $campo_actual ?>" name="campo_actual">
	<input type="hidden"  value="<?= $beneficiario->getEdadFrom($fechaempadronamiento) ?>" name="edad">
	<input type="hidden"  value="<?= $estado_envio ?>" name="estado_envio">


<?
if ($beneficiario->getEdadFrom($fechaempadronamiento) < 0) {
	$desabil_guardar = 'disabled';
	if ($num_form_remediar && $accion2 != "No se encuentra formulario") {
		echo "<SCRIPT Language='Javascript'> alert('La Fecha de Empadronamiento no puede ser anterior a la Fecha de Nacimiento ".$fechaempadronamiento."'); </SCRIPT>";
	}
}
?>
<? echo "<center><b><font size='+1' color='red'>".$tipoTransaccion."</font></b></center>"; ?>
<? echo "<center><b><font size='+1' color='Blue'>$accion2</font></b></center>"; ?>


<div id="accordion">
			
			
			<!-- Datos del Empadronamiento -->
			<h3>Datos del Beneficiario: <?=$beneficiario->getApellido_benef().", ".$beneficiario->getNombre_benef()?></h3>
			<div>
				<table width="100%">
					<tr id="mo">
						<td>Datos:</td>
					</tr>
					
					<tr bgcolor="#D6EBFF">
						<td><b>Apellido: </b> <?=$beneficiario->getNombre_benef()?></td>
						<td><b>Nombre: </b> <?=$beneficiario->getApellido_benef()?></td>
						<td><b>Tipo Documento: </b> <?=$beneficiario->getTipo_documento()?></td>
						<td><b>Num Documento: </b> <?=$beneficiario->getNumero_doc()?></td>
						<td><b>Fecha Nac: </b> <?=$beneficiario->getFecha_nacimiento_benef()?></td>
					</tr>
					<tr bgcolor="#E0FFD6">
						
						<td><b>Barrio: </b><?php echo $beneficiario->getBarrio();?> </td>
						<td><b>Calle: </b><?php echo $beneficiario->getCalle();?> </td>
						<td><b>Nro: </b><?php echo $beneficiario->getNumero_calle();?> </td>
						<td><b>Localidad: </b><?php echo $beneficiario->getLocalidad();?> </td>
						<td><b>Municipio: </b><?php echo $beneficiario->getMunicipio();?> </td>
					</tr>
					
				</table>
				<br />
				<?php 
					if ($empadronamiento->enPadron()) {
						?>
						<table width="100%">
							<tr id="mo">
								<?php 
									if ($empadronamiento->validaEnviado()) {
										?>
											<td bgcolor="#FF6347" colspan="1">Inscripcion: Formulario [<?php echo $empadronamiento->formularioGetNroformularioPresentacion(); ?>] - [BENEFICIARIO ENVIADO]</td>
										<?php
									} else {
										?>
											<td>Inscripcion: Formulario [<?php echo $empadronamiento->formularioGetNroformularioPresentacion(); ?>]</td>
										<?php
									}
									
								?>
								
							</tr>
							
							<tr bgcolor="#D6EBFF">
								<td><b>Efector: </b><?php echo $empadronamiento->efectorGetNombreefector(); ?> </td>
								<td><b>Cuie Efector: <?php echo $empadronamiento->relacionCodigosGetCodremediar(); ?></b> </td>
								<td><b>Promotor: <?php echo $empadronamiento->formularioGetAgente(); ?></b></td>
							</tr>
							
							<tr bgcolor="#E0FFD6">    
								
								<td><b>Score de Riesgo: <?php echo $empadronamiento->formularioGetPuntaje_final(); ?></b> </td>
								<td><b>Empadronado el: <?php echo fecha($empadronamiento->getFechaempadronamiento()); ?></b> </td>
								<!-- <td><b>Cargado el: <?php #echo fecha($empadronamiento->getFecha_carga()); ?></b> </td> -->
								<td></td>
							</tr>
							
						</table>
					
					<?php
					} else {
						?>
						<table width="100%">
							<tr id="mo">
								<td>Inscripcion: Beneficiario no Inscripto</td>
							</tr>
						</table>


						<?php
					}
					

				?>
				
				<?php  ?>
			</div>
			

	<h3>Formulario de Analisis de Riesgo</h3>
		<div>    
	
		<table width="97%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?= $bgcolor_out ?>' class="bordes">
			<tr id="mo">
				<td>
					<font size=+0><b>Formulario</b></font>
				</td>
			</tr>
			<tr>
				<td>
					<table width=100% align="center" class="bordes">
						<tr>
							<td>
								<table class="bordes" align="center">


							</td>
						</tr>



						<tr>

						<tr id="mo">
							<td align="center" colspan="4" >
								<b> N&uacute;mero de Formulario Remediar + Redes </b><input type="text" maxlength="10" name="num_form_remediar" value="<?=$nroFormRemediar?>" readonly>
							</td>
						</tr>
						<tr id="ma">
							<td align="left" colspan="4">
								<b>Fecha de Empadronamiento:</b>

								<input type=text name=fechaempadronamiento value='<?= fecha($fechaempadronamiento); ?>' size=15 onblur="esFechaValida(this); sumatoria();
									<?php 
										if (!($empadronamiento->validaFechaempadronamiento())) {
											?>
												cambioFecha();

											<?php
										}

									 ?>



								" onKeyUp="mascara(this,'/',patron,true);">
	<? //if (!$num_form_remediar){ echo link_calendario("fechaempadronamiento");} ?>
							</td>
						</tr>
						<tr id="mo" hidden>
							<td align="center" colspan="4" >
								<b> Datos Cobertura </b>
							</td>
						</tr>

						<tr id="mo">
							<td align="left" colspan="4">
								<b>Factores de Riesgo</b>
							</td>
						</tr>
						<tr align="center">
							<td  colspan="4">
								<table width="100%" border="0" cellspacing="0" bordercolor="#006699" style="border:thin groove;">
									<tr >
										<td style=" padding-left: 40px"><b> 1) Sexo y edad </b></td>
										<td align="center"><? if ($sexo == 'F') {
												echo 'Femenino';
											}
											else {
												echo 'Masculino';
											} ?></td>
										<td align="center"><?
											if ($id_factorriesgo != '') {
												$mas_slq = "or (id_factor=$id_factorriesgo)";
											}
											$sql = "select * from remediar.factores_riesgo where (substring(sexo,1,1)=upper('$sexo') and ".$beneficiario->getEdadFrom($fechaempadronamiento)." between edadini and edadfin) " . $mas_slq;
											$refrescar = 'document.forms[0].submit()';
										?>
									  <!--<select name=factorriesgo Style="width=200px"
									  onKeypress="buscar_combo(this);"
											  onblur="borrar_buffer(); "
											  >
									   -->


									<?
									$res_efectores = sql($sql) or fin_pagina();
									while (!$res_efectores->EOF) {
										$id_factorriesgo = $res_efectores->fields['id_factor'];
										echo $descripcion = $res_efectores->fields['descripcion'];
										$edadini = $res_efectores->fields['edadini'];
										$edadfin = $res_efectores->fields['edadfin'];
										$puntos_1 = $res_efectores->fields['puntaje'];
										?>
												<input type="hidden"  value="<?= $id_factorriesgo ?>" name="factorriesgo">
												<input type="hidden"  value="<?= $puntos_1 ?>" name="puntos_1">
													<!--<option value='<? //=$idl?>' <? //if (($descripcion1_remediar==$descripcion)||($edad>=$edadini && $edad<$edadfin )) echo "selected"?> ><? //=$descripcion?></option>-->
													<?
													$res_efectores->movenext();
												}
												?>
												<!--</select>--></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr id="ma">
							<td align="left" colspan="4">
								<b>HTA</b>
							</td>
						</tr>
						<tr align="center">
							<td  colspan="4">
								<table width="100%" border="1" cellspacing="0" bordercolor="#006699" style="border:thin groove;">
												<? if ($beneficiario->getEdadFrom($fechaempadronamiento) > 20) { ?>
										<tr>
											<td style=" padding-left: 40px" width="72%"><b> 2) En los &uacute;limos 2 a&ntilde;os, &iquest;le tomaron la presi&oacute;n arterial?<br>
													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(s&oacute;lo para mayores de 20 a&ntilde;os)</b></td>
											<td align="center"><?
												$sql = "select * from remediar.hta where cual=2 order by id_hta";
												$refrescar = 'document.forms[0].submit()';
													?>
												<select name=hta2 Style="width:200px"
														onKeypress="buscar_combo(this);"
														onblur="borrar_buffer(); sumatoria();"
														onchange="sumatoria();">
													


													<?
													$res_efectores = sql($sql) or fin_pagina();
													while (!$res_efectores->EOF) {
														$idl = $res_efectores->fields['id_hta'];
														$descripcion = $res_efectores->fields['descripcion2'];
														$puntos_2 = $res_efectores->fields['puntaje'];
														list($hta2, $p) = explode("_", $hta2);
														?>
														<option value='<?= $idl . '_' . $puntos_2 ?>' <? if ($hta2 == $idl)
													echo "selected" ?> ><?= $descripcion ?></option>
														<?
														$res_efectores->movenext();
													}
													?>
												</select></td>
										</tr><? } ?>
									<tr>
										<td style=" padding-left: 40px" width="72%"><b> 3) &iquest;Cuantas veces un m&eacute;dico, una enfermera u otro profesional de la salud<br>
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;le dijo que ten&iacute;a la presi&oacute;n alta?</b></td>
										<td align="center"><?
											$sql = "select *
					from remediar.hta
									 where cual=3";
											$refrescar = 'document.forms[0].submit()';
												?>
											<select name=hta3 Style="width:200px"
													onKeypress="buscar_combo(this);"
													onblur="borrar_buffer(); sumatoria();"
													onchange="sumatoria();">
												


											<?
											$res_efectores = sql($sql) or fin_pagina();
											while (!$res_efectores->EOF) {
												$idl = $res_efectores->fields['id_hta'];
												$descripcion = $res_efectores->fields['descripcion2'];
												$puntos_3 = $res_efectores->fields['puntaje'];
												list($hta3, $p) = explode("_", $hta3);
												?>
													<option value='<?= $idl . '_' . $puntos_3 ?>' <? if ($empadronamiento->formularioGetHta2() == $idl)
													echo "selected" ?>><?= $descripcion ?></option>
													<?
													$res_efectores->movenext();
												}
												?>
											</select></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr id="ma">
							<td align="left" colspan="4">
								<b>COLESTEROL</b>
							</td>
						</tr>
						<tr align="center">
							<td  colspan="4">
								<table width="100%" border="1" cellspacing="0" bordercolor="#006699" style="border:thin groove;">
									<? if ($beneficiario->getEdadFrom($fechaempadronamiento) > 20) { ?>
										<tr>
											<td style=" padding-left: 40px" width="72%"><b> 4) En los &uacute;limos 5 a&ntilde;os, &iquest;le midieron el colesterol?<br>
													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(s&oacute;lo para mayores de 20 a&ntilde;os)</b></td>
											<td align="center"><?
											$sql = "select * from remediar.colesterol where cual=4";
											$refrescar = 'document.forms[0].submit()';
										?>
											<select name=colesterol4 Style="width:200px"
													onKeypress="buscar_combo(this);"
													onblur="borrar_buffer(); sumatoria();"
													onchange="sumatoria();">
												


												<?
												$res_efectores = sql($sql) or fin_pagina();
												while (!$res_efectores->EOF) {
													$idl = $res_efectores->fields['id_colesterol'];
													$descripcion = $res_efectores->fields['descripcion2'];
													$puntos_4 = $res_efectores->fields['puntaje'];
													list($colesterol4, $p) = explode("_", $colesterol4);
													?>
															<option value='<?= $idl . '_' . $puntos_4 ?>' <? if ($empadronamiento->formularioGetColesterol4() == $idl)
														echo "selected" ?> ><?= $descripcion ?></option>
															<?
															$res_efectores->movenext();
														}
														?>
												</select></td>
											</tr>
											<? } ?>
																<tr>
										<td style=" padding-left: 40px" width="72%"><b> 5) &iquest;Alguna vez un m&eacute;dico, una enfermera u otro profesional <br>
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;de la salud le dijo que ten&iacute;a colesterol alto?</b></td>
										<td align="center"><?
												$sql = "select * from remediar.colesterol where cual=5";
												$refrescar = 'document.forms[0].submit()';
												?>
											<select name=colesterol5 Style="width:200px"
													onKeypress="buscar_combo(this);"
													onblur="borrar_buffer(); sumatoria();"
													onchange="sumatoria();">
												


											<?
											$res_efectores = sql($sql) or fin_pagina();
											while (!$res_efectores->EOF) {
												$idl = $res_efectores->fields['id_colesterol'];
												$descripcion = $res_efectores->fields['descripcion2'];
												$puntos_5 = $res_efectores->fields['puntaje'];
												list($colesterol5, $p) = explode("_", $colesterol5);
												?>
													<option value='<?= $idl . '_' . $puntos_5 ?>' <? if ($empadronamiento->formularioGetColesterol5() == $idl)
													echo "selected" ?> ><?= $descripcion ?></option>
													<?
													$res_efectores->movenext();
												}
												?>
											</select></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr id="ma">
							<td align="left" colspan="4">
								<b>DMT2</b>
							</td>
						</tr>
						<tr align="center">
							<td  colspan="4">
								<table width="100%" border="1" cellspacing="0" bordercolor="#006699" style="border:thin groove;">
	<? if ($beneficiario->getEdadFrom($fechaempadronamiento) > 40) { ?>
										<tr>
											<td style=" padding-left: 40px" width="72%"><b> 6) En los &uacute;limos 3 a&ntilde;os, &iquest;le midieron glucemia/az&uacute;car en sangre?<br>
													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(s&oacute;lo para mayores de 40 a&ntilde;os)</b></td>
											<td align="center"><?
		$sql = "select *
					from remediar.dmt2
									 where cual=6";
		$refrescar = 'document.forms[0].submit()';
		?>
												<select name=dmt26 Style="width:200px"
														onKeypress="buscar_combo(this);"
														onblur="borrar_buffer(); sumatoria();"
														onchange="sumatoria();">
													


													<?
													$res_efectores = sql($sql) or fin_pagina();
													while (!$res_efectores->EOF) {
														$idl = $res_efectores->fields['id_dmt2'];
														$descripcion = $res_efectores->fields['descripcion2'];
														$puntos_6 = $res_efectores->fields['puntaje'];
														list($dmt26, $p) = explode("_", $dmt26);
														?>
														<option value='<?= $idl . '_' . $puntos_6 ?>' <? if ($empadronamiento->formularioGetDmt26() == $idl)
													echo "selected" ?> ><?= $descripcion ?></option>
														<?
														$res_efectores->movenext();
													}
													?>
												</select></td>
										</tr>
	<? } ?>
									<tr>
										<td style=" padding-left: 40px" width="72%"><b> 7) &iquest;Alguna vez un doctor, una enfermera u otro profesional de la salud le<br>
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;dijo que ten&iacute;a diabetes o az&uacute;car alta en la sangre?</b></td>
										<td align="center"><?
	$sql = "select *
					from remediar.dmt2
									 where cual=7";
	$refrescar = 'document.forms[0].submit()';
	?>
											<select name=dmt27 Style="width:200px"
													onKeypress="buscar_combo(this);"
													onblur="borrar_buffer(); sumatoria();"
													onchange="sumatoria();">
												


											<?
											$res_efectores = sql($sql) or fin_pagina();
											while (!$res_efectores->EOF) {
												$idl = $res_efectores->fields['id_dmt2'];
												$descripcion = $res_efectores->fields['descripcion2'];
												$puntos_7 = $res_efectores->fields['puntaje'];
												list($dmt27, $p) = explode("_", $dmt27);
												?>
													<option value='<?= $idl . '_' . $puntos_7 ?>' <? if ($empadronamiento->formularioGetDmt27() == $idl)
													echo "selected" ?> ><?= $descripcion ?></option>
													<?
													$res_efectores->movenext();
												}
												?>
											</select></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr id="ma">
							<td align="left" colspan="4">
								<b>ECV</b>
							</td>
						</tr>
						<tr align="center">
							<td  colspan="4">
								<table width="100%" border="1" cellspacing="0" bordercolor="#006699" style="border:thin groove;">
									<tr>
										<td style=" padding-left: 40px" width="72%"><b> 8) &iquest;Ud. o alg&uacute;n familiar directo (padre, madre) tuvo un infarto, ACV<br>
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(ataque cerebral) o problema card&iacute;aco?</b></td>
										<td align="center"><?
												$sql = "select *
					from remediar.ecv
									order by id_ecv";
												$refrescar = 'document.forms[0].submit()';
												?>
											<select name=ecv8 Style="width:200px"
													onKeypress="buscar_combo(this);"
													onblur="borrar_buffer(); sumatoria();"
													onchange="sumatoria();">
												


												<?
												$res_efectores = sql($sql) or fin_pagina();
												while (!$res_efectores->EOF) {
													$idl = $res_efectores->fields['id_ecv'];
													$descripcion = $res_efectores->fields['descripcion'];
													$puntos_8 = $res_efectores->fields['puntaje'];
													list($ecv8, $p) = explode("_", $ecv8);
													?>
													<option value='<?= $idl . '_' . $puntos_8 ?>' <? if ($empadronamiento->formularioGetEcv8() == $idl)
													echo "selected" ?> ><?= $descripcion ?></option>
													<?
													$res_efectores->movenext();
												}
												?>
											</select></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr id="ma">
							<td align="left" colspan="4">
								<b>TABACO</b>
							</td>
						</tr>
						<tr align="center">
							<td  colspan="4">
								<table width="100%" border="1" cellspacing="0" bordercolor="#006699" style="border:thin groove;">
									<tr>
										<td style=" padding-left: 40px" width="72%"><b> 9) &iquest;Ud. fum&oacute; al menos un cigarrillo en los &uacute;ltimos 30 d&iacute;as?</b></td>
										<td align="center"><?
												$sql = "select *
					from remediar.tabaco";
												$refrescar = 'document.forms[0].submit()';
												?>
											<select name=tabaco9 Style="width:200px"
													onKeypress="buscar_combo(this);"
													onblur="borrar_buffer(); sumatoria();"
													onchange="sumatoria();">
												
												<?
												$res_efectores = sql($sql) or fin_pagina();
												while (!$res_efectores->EOF) {
													$idl = $res_efectores->fields['id_tabaco'];
													$descripcion = $res_efectores->fields['descripcion'];
													$puntos_9 = $res_efectores->fields['puntaje'];
													list($tabaco9, $p) = explode("_", $tabaco9);
		?>
													<option value='<?= $idl . '_' . $puntos_9 ?>' <? if ($empadronamiento->formularioGetTabaco9() == $idl)
										echo "selected" ?> ><?= $descripcion ?></option>
										<?
										$res_efectores->movenext();
									}
									?>
											</select></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr id="ma">
							<td  colspan="4" id="mo">
								<b style=" margin-left: 60%">SUMATORIA: &nbsp;</b>
								<input type="text"  value="<?= $puntaje_final ?>" name="puntaje_final" readonly size="31" />
								
							</td>
						</tr>
						<tr id="ma">
							<td align="center" colspan="4">

								<b>Promotor de salud</b>
							</td>
						</tr>



						<tr>
							<td align="right">
								<b>Apellido:</b>
							</td>
							<td align='left'>

								<select name="datosAgente" id="datosAgente" onchange="SincronizarPromotores(this);">
									<option value="NOTOSH">Seleccione un promotor</option>
									<?php
									while (!$promotores_result->EOF) {
										$promotores_guiOption = 'value="' . ($promotores_result->fields['apellido'] . "-" . $promotores_result->fields['nombre']) . '"';
										if (($empadronamiento->formularioGetApellidoagente() . "-" . $empadronamiento->formularioGetNombreagente()) == ($promotores_result->fields['apellido'] . "-" . $promotores_result->fields['nombre'])) {
											$promotores_guiOption = $promotores_guiOption . ' selected="selected"';
										}
										?>
																		<option <?= $promotores_guiOption ?>>
										<?= ($promotores_result->fields['apellido'] . ", " . $promotores_result->fields['nombre']) ?>
																		</option>
										<?php
										$promotores_result->MoveNext();
									}
									?>

								</select>

							</td>

							<td align="right">
								<b>Nro. Doc.:</b>
							</td>
							<td align='left'>

								<select name="num_doc_agente" id="num_doc_agente" onchange="SincronizarPromotores(this);">
									<option value="NOTOSH">Seleccione un promotor</option>
									<?php
									$promotores_result->MoveFirst();
									while (!$promotores_result->EOF) {
										$option = 'value="' . $promotores_result->fields['dni'] . '"';

										if ($empadronamiento->formularioGetDni_agente() == $promotores_result->fields['dni']) {
											$option = $option . ' selected="selected"';
										}
										?>
									
										<option <?= $option ?>><?= $promotores_result->fields['dni'] ?></option>

										<?php
										$promotores_result->MoveNext();
									}
									?>
								</select>

							</td>
						</tr>




						<tr>
							<td align="center" colspan="6" id="ma">
								<b> Centro Inscriptor </b>
							</td>
						</tr>

						<tr>
						  
							<td align="center" width="90%" colspan="4">
								Lugar: <select name=cuie Style="width:300px"
										onKeypress="buscar_combo(this);"
										onblur="borrar_buffer();"
										onchange="borrar_buffer();">
									<option value=-1>Seleccione</option>
						<?
						$sql = "select f.cuie as cuie, rl.codremediar as codremediar,  f.nombreefector as nombreefector
										from facturacion.smiefectores f
										inner join general.relacioncodigos rl on f.cuie = rl.cuie
										where rl.codremediar is not null
										and rl.codremediar <> ''
										order by nombreefector";
						$res_efectores = sql($sql) or fin_pagina();
						while (!$res_efectores->EOF) {
							$cuiec = $res_efectores->fields['cuie'];
							$nombre_efector = $res_efectores->fields['nombreefector'];
							?>
										<option value='<?= $cuiec ?>' <? if ($empadronamiento->formularioGetCentro_inscriptor() == $cuiec)
							echo "selected" ?> ><?= $res_efectores->fields['codremediar'] . " - " . $nombre_efector ?></option>
							<?
							$res_efectores->movenext();
						}
						?>
								</select>
								&nbsp;
								<button onclick="window.open('../inscripcion/busca_efector.php?qkmpo=cuie','Buscar','dependent:yes,width=900,height=700,top=1,left=60,scrollbars=yes');">Buscar</button>
							</td>

						</tr>
						<?
						echo "<SCRIPT Language='Javascript'> 
					 if(form1.num_form_remediar.value=='' && form1.edad.value>0){
						form1.puntaje_final.value=form1.puntos_1.value;
					  }
					  function cambioFecha(){
					  	document.forms[0].submit();
					  }

				  function sumatoria(){
					  var edad=form1.edad.value; 
					  if(edad<0){
						document.forms[0].submit();
					  }
					  var p_hta2=0;
					  var p_hta3=0;
					  var p_colesterol4=0;
					  var p_colesterol5=0;
					  var p_dmt26=0;
					  var p_dmt27=0;
					  var p_tabaco9=0;
					  var p_ecv8=0;
					  if(edad>20){
						  var hta2=form1.hta2.value;
						  if(hta2!='-1'){
							p_hta2=hta2.split('_');
							p_hta2=p_hta2[1]
						  }
					   }
					  var hta3=form1.hta3.value;
					  if(hta3!='-1'){
						p_hta3=hta3.split('_');
						p_hta3=p_hta3[1]
					  }
					  if(edad>20){
						  var colesterol4=form1.colesterol4.value;
						  if(colesterol4!='-1'){
							p_colesterol4=colesterol4.split('_');
							p_colesterol4=p_colesterol4[1]
						  }
					   }
					  var colesterol5=form1.colesterol5.value;
					  if(colesterol5!='-1'){
						p_colesterol5=colesterol5.split('_');
						p_colesterol5=p_colesterol5[1]
					  }
					  if(edad>40){
						  var dmt26=form1.dmt26.value;
						  if(dmt26!='-1'){
							p_dmt26=dmt26.split('_');
							p_dmt26=p_dmt26[1]
						  }
					   }
					  var dmt27=form1.dmt27.value;
					  if(dmt27!='-1'){
						p_dmt27=dmt27.split('_');
						p_dmt27=p_dmt27[1]
					  }
					  var ecv8=form1.ecv8.value;
					  if(ecv8!='-1'){
						p_ecv8=ecv8.split('_');
						p_ecv8=p_ecv8[1]
					  }
					  var tabaco9=form1.tabaco9.value;
					  if(tabaco9!='-1'){
						p_tabaco9=tabaco9.split('_');
						p_tabaco9=p_tabaco9[1]
					  }
					  /*alert(p_colesterol4);*/
					  form1.puntaje_final.value=parseInt(form1.puntos_1.value)+parseInt(p_hta2)+parseInt(p_hta3)+parseInt(p_colesterol4)+parseInt(p_colesterol5)+parseInt(p_dmt26)+parseInt(p_dmt27)+parseInt(p_ecv8)+parseInt(p_tabaco9);
				  }
		</SCRIPT>";
						?>
					</table>
				</td>
			</tr>



			<tr id="mo">
				<td align=center colspan="2">
					<b>Guardar Planilla</b>
				</td>
			</tr>
			<tr align="center">
				<td>
					<b><font size="0" color="Red">Nota: Verifique todos los datos antes de guardar</font> </b>
				</td>
			</tr>
			<tr align="center">
				<td>
	<?php /* if ( $estado_envio=='p') $permiso=''; else $permiso='disabled'; */ ?>
					<input type='submit' name='guardar_editar' value='Guardar' onclick="return control_nuevos()"
						   title="Guardar datos de la Planilla" <?= $desabil_guardar ?>>
				</td>
			</tr>

		</table>

		<input type="hidden" name="TipoTransaccion" value="<?=$tipoTransaccion?>">
	</form>
</div>
</div>

<script>
				var campo_focus=document.all.campo_actual.value;
				if(campo_focus==''){
					document.getElementById('campo_actual').value='num_form_remediar';
					campo_focus='num_form_remediar';
				}else{
					if(campo_focus=='num_form_remediar'){
						campo_focus='fechaempadronamiento';
						document.getElementById('campo_actual').value='fechaempadronamiento';
					}else{
						campo_focus='os';
					}
				}
				document.getElementById(campo_focus).focus();
</script>
 <!--<input type=button name="carga_remediar" value="Remediar+Redes" onclick="document.all.guardar.disabled=false; window.open('<?= encode_link("remediar_admin.php", array("clave_beneficiario" => $clave_beneficiario, "sexo" => $sexo, "fecha_nac" => $fecha_nac, "pagina" => "ins_admin.php")) ?>','Remediar','dependent:yes,width=900,height=700,top=1,left=60,scrollbars=yes');" title="Carga Remediar + Redes" >-->
<div align="center" id="mo"><h4>Los datos aqui expuestos son de suma importancia, recuerde verificarlos antes de guardar la planilla.</h4></div>
</body>
</html>
