<?php
 require_once ("../../config.php");
 extract($_POST,EXTR_SKIP);
 if ($parametros) extract($parametros,EXTR_OVERWRITE);
 echo $html_header;
?>


<!-- Funcion para capturar el "enter" en el teclado -->
<script>
	function iSubmitEnter(oEvento, oFormulario){
	     var iAscii;

	     if (oEvento.keyCode)
		 iAscii = oEvento.keyCode;
	     else if (oEvento.which)
		 iAscii = oEvento.which;
	     else
		 return false;

	     /*if (iAscii == 13)*/ oFormulario.submit();

	     return true;
	}

</script>
<!-- Funcion para capturar el "enter" en el teclado -->




<!-- Formulario para el ingreso de datos --> 
	<FORM METHOD="get" ACTION="" name="form1" id="form1">
	<font size=2><b>Ingrese Nombre, Apellido o Nro. Documento del promotor</b> </font>
	 &nbsp; <input type="text" style="font-size:9" name="valor_buscar" size="20"   maxlength="40" onkeyup="iSubmitEnter(event, document.form1)">

	</FORM>
<!-- Formulario para el ingreso de datos --> 


<!-- Script para enfocar el campo:valor_buscar en el formulario --> 
<script>
	document.getElementById('valor_buscar').focus();
</script>
<!-- Script para enfocar el campo:valor_buscar en el formulario --> 



<?php
	if ($_GET['valor_buscar']){
	$vremediar='n';
	$valor_buscar= $_GET['valor_buscar'];

	//echo $cual_os;
	/*$sql=  "select trim(upper(apellidoagente))as apellidoagente,trim(upper(nombreagente))as nombreagente,trim(dni_agente)as dni_agente
	from remediar.formulario
	WHERE trim(upper(dni_agente)) = upper('$valor_buscar') or trim(upper(nombreagente)) like upper('%$valor_buscar%')  or trim(upper(dni_agente)) 		like upper('%$valor_buscar%') or trim(upper(apellidoagente)) like upper('%$valor_buscar%')
	group by trim(upper(apellidoagente)),trim(upper(nombreagente)),trim(dni_agente)";*/

	
	// Sentencia SQL para consultar en la base de datos
	$sql=  "(select trim(upper(apellidoagente))as apellidoagente,trim(upper(nombreagente))as nombreagente,trim(dni_agente)as dni_agente from remediar.formulario WHERE trim(upper(dni_agente)) = upper('$valor_buscar') or trim(upper(nombreagente)) like upper('%$valor_buscar%')  or trim(upper(dni_agente)) like upper('%$valor_buscar%') or trim(upper(apellidoagente)) like upper('%$valor_buscar%') group by trim(upper(apellidoagente)),trim(upper(nombreagente)),trim(dni_agente)) UNION (select trim(upper(apellidoagente))as apellidoagente,trim(upper(nombreagente))as nombreagente,trim(dni_agente)as dni_agente from uad.beneficiarios WHERE trim(upper(dni_agente)) = upper('$valor_buscar') or trim(upper(nombreagente)) like upper('%$valor_buscar%')  or trim(upper(dni_agente)) like upper('%$valor_buscar%') or trim(upper(apellidoagente)) like upper('%$valor_buscar%') group by trim(upper(apellidoagente)),trim(upper(nombreagente)),trim(dni_agente))";
	// Sentencia SQL para consultar en la base de datos


	// Consulta a la base de datos
	$res_efectores=sql($sql) or fin_pagina();
	// Consulta a la base de datos
		
?>



<!-- Script para cambiar el campo:valor_buscar en el formulario por su valor eniado en el formulario al php --> 
			<script>
				document.getElementById('valor_buscar').value='<?=$valor_buscar?>';
			</script>
<!-- Script para cambiar el campo:valor_buscar en el formulario por su valor eniado en el formulario al php --> 


			
			  <tr>
			  <td class="titulo_consulta">&nbsp;<h2><U>Resultados de la Busqueda</U></h2></td>
			 </tr>
			  <table border=1 cellspacing=0 cellpadding=0 height=10% align="center" width=80%>
			 <tr>
			   <td align="center">&nbsp;<h5>Doc. Agente</h5></td>
			  <td align="center">&nbsp;<h5>Apellido Agente</h5></td>
			 <td align="center">&nbsp;<h5>Nombre Agente</h5></td>
			 </tr>
<?
			while (!$res_efectores->EOF){ if(rtrim($res_efectores->fields['dni_agente'])==''){$dni_agente='S/D';}else{$dni_agente=rtrim($res_efectores->fields['dni_agente']);}?>
					<tr>
					<td>&nbsp;<a href="#" onclick="opener.document.forms.form1.num_doc_agente.value = '<?=$dni_agente?>'; opener.document.forms.form1.nombreagente.value = '<?=$res_efectores->fields['nombreagente']?>'; opener.document.forms.form1.apellidoagente.value = '<?=$res_efectores->fields['apellidoagente']?>'; window.close();" style="text-decoration:none;"><font size=2><?=$dni_agente?></font></a></td>
					<td>&nbsp;<font size=2><? echo $res_efectores->fields['apellidoagente']; ?></font>&nbsp;</td>
                    <td>&nbsp;<font size=2><?=$res_efectores->fields['nombreagente']?></font>&nbsp;</td>
			<? $res_efectores->movenext();
			    }?> </table>
			 
			  <BR> <a href="javascript:close()" ><font size=2>CERRAR CONSULTAR</font></a>
<? }
echo fin_pagina();// aca termino?>
