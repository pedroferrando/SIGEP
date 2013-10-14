<?php

include("funciones_generales.php");
//Los Usarios Alicia Clapier (clapier), Cuse Ezequiel(ezequiel),
//Graciela Tedeschi (graciela), Valentino Sergio (serval)
//NO SE REGISTRAN 
$consulta="select u.apellido||', '||u.nombre||' ('||u.login||')' as usuario
		from sistema.usuarios u
		join permisos.phpss_account on phpss_account.username=u.login   
		where active='true' and pcia_ubicacion=2 and 
                     login <>'clapier' and login <> 'ezequiel' and login <>'graciela' and login<>'serval'
   except

		select u.apellido||', '||u.nombre||' ('||u.login||')' as usuario
		from sistema.usuarios u
		join personal.asistencia using (id_usuario)
		where fecha='".date("Y-m-d")."'";
$rta_consulta=$db->Execute($consulta) or die("c23: Error al traer los usuarios que no se registraron: ".$consulta);
$i=0;
if ($rta_consulta->recordCount()>0){
	$contenido="<b>Listado de usuarios en Bs. As. que no han registrado ingreso hasta las ".date("G:i")." del ".date("d/m/Y").":</b><br><br>";

	while (!$rta_consulta->EOF){
		if ($rta_consulta->fields["usuario"])	$contenido.=$rta_consulta->fields["usuario"]."<br>";
		$rta_consulta->moveNext();
	}
	
	enviar_mail_html('', $asunto, $contenido, "", "", 0);
	
}
?>