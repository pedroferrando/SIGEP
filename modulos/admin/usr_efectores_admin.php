<?
/* Modulo para Gestion de Relacion de Usuarios y efectores */

require_once ("../../config.php");

extract ( $_POST, EXTR_SKIP );
if ($parametros)
	extract ( $parametros, EXTR_OVERWRITE );
cargar_calendario ();

if ($borra_efec == 'borra_efec') {
	
	$query = "DELETE FROM sistema.usu_efec  
	          WHERE id_usuario=$id_usuario and cuie='$cuie'";
	
	sql ( $query, "Error al eliminar la relación con el Efector" ) or fin_pagina ();
	$accion = "Los datos se han borrado";
}

if ($id_usuario) {
	$query = " SELECT *
		   FROM sistema.usuarios  
		   WHERE id_usuario=$id_usuario";
	
	$res_usuario = sql ( $query, "Error al traer el Usuario" ) or fin_pagina ();
	$login = $res_usuario->fields ['login']. ' [' .$res_usuario->fields ['nombre'].' '.$res_usuario->fields ['apellido'].']'  ;
	$login = strtoupper ( $login );
}

if ($_POST ['guardar_relacion'] == 'Guardar') {
	$db->StartTrans ();
	
	for($i = 0; $i < count ( $cuie ); $i ++) {
		$efector = $cuie [$i];
		
		$query = "INSERT INTO sistema.usu_efec
			  (cuie, id_usuario,fecha_alta)
			  values
		          ('$efector', '$id_usuario',CURRENT_TIMESTAMP)";
		
		sql ( $query, "Error al insertar la relación con el Efector" ) or fin_pagina ();
	
	}
	
	$accion = "Los datos se han guardado correctamente";
	
	$db->CompleteTrans ();
}
//---------------------fin provincia------------------------------


echo $html_header;

?>
<script>
function editar_campos()
{	
	document.all.login.disabled=false;
	document.all.guardar_editar.disabled=false;
	document.all.cancelar_editar.disabled=false;
	document.all.borrar.disabled=false;
	document.all.guardar.enaible=false;
	return true;
}
//fin de function control_nuevos()
//empieza funcion mostrar tabla
var img_ext='<?=$img_ext = '../../imagenes/rigth2.gif'?>';//imagen extendido
var img_cont='<?=$img_cont = '../../imagenes/down2.gif'?>';//imagen contraido

function muestra_tabla(obj_tabla,nro){
 oimg=eval("document.all.imagen_"+nro);//objeto tipo IMG
 if (obj_tabla.style.display=='none'){
 	obj_tabla.style.display='inline';
    oimg.show=0;
    oimg.src=img_ext;
 }
 else{
 	obj_tabla.style.display='none';
    oimg.show=1;
	oimg.src=img_cont;
 }
}//termina muestra tabla

//---------------------scrip para provincia------------------------------

function control_nuevo_provincia(){ 
  if(document.all.cod_provincia.value==""){
  		alert('Debe ingresar un codigo de provincia');
  return false;
 } 
  if(document.all.nom_provincia.value==""){
  alert('Debe ingresar una Provincia');
  return false;
 } 
 } 
 
 
//---------------------fin scrip para provincia---------------------------

</script>
<form name='form1' action='usr_efectores_admin.php' method='POST'><input
	type="hidden" value="<?=$id_usuario?>" name="id_usuario">

<?
    echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";
?>
<table width="85%" cellspacing=0 border=1 bordercolor=#E0E0E0
align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
    <tr id="mo">
        <td>
            <? if (! $id_usuario) { ?>  
            <font size=+1><b>Nuevo Dato</b></font>   
            <? } else {	?>
            <font size=+1><b>ASIGNACION DE EFECTORES A USUARIOS</b></font>   
            <? } ?>       
        </td>
    </tr>
    <tr>
	<td>
        	<table width=90% align="center" class="bordes">
                    <tr>
                        <td id=mo colspan="2">
                            <b> USUARIO: <?=$login?> </b>
                        </td>
                    </tr>
                    <tr>
			<td>
                            <table>
				<tr>
                                    <td align="center" colspan="2">
                                        <b> ID Usuario: <font size="+1" color="Red"> 
                                        <?=($id_usuario) ? $id_usuario : "Nuevo Dato"?></font></b>
                                    </td>
				</tr>
                                <tr>
                                    <td align="right">
                                        <b>Login:</b>
                                    </td>
                                    <td align='left'>
                                        <input type="text" size="40" value="<?=$login;?>" name="login" 
                                        <? if ($id_usuario) echo "disabled"?>>
                                    </td>
				</tr>
                            </table>
                            <tr>
				<td>
                                    <table width=100% align="center" class="bordes">
                                        <tr align="center">
                                            
                                        </tr>
                                    </table>
				</td>
                            </tr>
                    <?	if ($id_usuario) {?>
                    <tr>
			<td>
                            <table width="100%" class="bordes" align="center">
				<tr align="center" id="mo">
                                    <td align="center">
                                        <b>Agregar Efector</b>
                                    </td>
				</tr>
                                <tr>
                                    <td>
                                        <table width="100%" align="center">
                                            <tr>
                                                <td align="right">
                                                    <b>Efectores:</b>
                                                </td>
						<td align='left'>
                                                    <select multiple name="cuie[]" Style=""
                                                    size="20" onKeypress="buscar_combo(this);"
                                                    onblur="borrar_buffer();" onchange="borrar_buffer();"
                                                        <? if (($id_planilla) and ($tipo_transaccion != "M")) echo "disabled"?>>
                                                        <?
                                                            $sql = "select cuie,nombreefector 
                                                                    from facturacion.smiefectores 
                                                                    where cuie not in (select cuie from sistema.usu_efec where id_usuario = '$id_usuario' ) 
                                                                    order by cuie";
                                                            $res_efectores = sql ( $sql ) or fin_pagina ();
                                                            while ( ! $res_efectores->EOF ) {
                                                                $cuiel = $res_efectores->fields ['cuie'];
                                                                $nombre_efector = $res_efectores->fields ['nombreefector'];
                                                        ?>
                                                        <option value='<?=$cuiel?>' 
                                                            <? if ($cuie == $cuiel)	echo "selected"?>>
                                                            <?=$cuiel . " - " . $nombre_efector?>
                                                        </option>
                                                            <? $res_efectores->movenext ();
                                                            }?>
                                                     </select>
                                                </td>
                                            </tr>
                                            <tr>
						<td align="center" colspan="5" class="bordes">
                                                    <input type="submit" name="guardar_relacion" value="Guardar"
                                                    title="Guardar" style=""
                                                    >&nbsp;&nbsp;
                                                </td>
                                            </tr>
        				</table>
                                    </td>
				</tr>
			</table>
                    </td>
		</tr>
		
	
 <? //--------------------- lista efectores------------------------------		?>
 
                <tr>
                    <td>
                        <table width="100%" class="bordes" align="center">
                            <tr align="center" id="mo">
				<td align="center" width="3%">
                                    <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar" align="left"
                                    style="cursor: pointer;" onclick="muestra_tabla(document.all.prueba_vida,2);">
                                </td>
				<td align="center">
                                    <b>Efectores Relacionados</b>
                                </td>
                            </tr>
			</table>
                    </td>
		</tr>
		<tr>
                    <td>
			<table id="prueba_vida" border="1" width="100%"
			style="display: none; border: thin groove">
			<? //tabla de comprobantes
                        $query = "SELECT e.nombreefector, e.cuie 
                                  FROM facturacion.smiefectores e
                                  INNER JOIN sistema.usu_efec ue on (e.cuie = ue.cuie) 
			          INNER JOIN sistema.usuarios u on (ue.id_usuario = u.id_usuario) 
			          WHERE u.id_usuario = '$id_usuario' 
                                  ORDER BY cuie";
		
                        $res_comprobante = sql ( $query, "<br>Error al traer los Efectores relacionados<br>" ) or fin_pagina ();
                        if ($res_comprobante->RecordCount () == 0) { ?>
                            <tr>
				<td align="center">
                                    <font size="2" color="Red"><b>No existe ningun Efector relacionado con este Usuario</b></font>
                                </td>
                            </tr>
			<? } else {?>
                            <tr id="sub_tabla">
				<td width=1%>&nbsp;</td>
				<td width="20%">CUIE</td>
				<td width="30%">Efector</td>
				<td width=1%>Borrar</td>
                            </tr>
                            <? $res_comprobante->movefirst ();
                            while ( ! $res_comprobante->EOF ) {

                                    $ref = encode_link ( " ", array ("cuie" => $res_comprobante->fields ['cuie'], "nombreefector" => $res_comprobante->fields ['nombreefector'] ) );
                                    $onclick_elegir = "location.href='$ref'";

                                    $id_tabla = "tabla_" . $res_comprobante->fields ['cuie'];
                                    $onclick_check = " javascript:(this.checked)?Mostrar('$id_tabla'):Ocultar('$id_tabla')";
                                ?>

                                <tr <?=atrib_tr ()?>>
                                    <td>
                                        <input type=checkbox name=check_prestacion value=""
                                        onclick="<?=$onclick_check?>" class="estilos_check">
                                    </td>
                                    <td onclick="<?=$onclick_elegir?>">
                                        <?=$res_comprobante->fields ['cuie']?>
                                    </td>
                                    <td onclick="<?=$onclick_elegir?>">
                                        <?=$res_comprobante->fields ['nombreefector']?>
                                    </td>
                                    <?
                                    $ref = encode_link ( "usr_efectores_admin.php", array ("cuie" => $res_comprobante->fields ['cuie'], "borra_efec" => "borra_efec", "id_usuario" => $id_usuario ) );
                                    $onclick_provincia = "if (confirm('Seguro que desea eliminar la relación con el Efector?')) location.href='$ref'";
                                    ?>
                                    <td align="center">
                                        <img src='../../imagenes/salir.gif'	style='cursor: pointer;' onclick="<?=$onclick_provincia?>">
                                    </td>
                                </tr>
                                <? $res_comprobante->movenext ();
                                } // fin while
                            } //fin del else		?>	 	
                        </table>
                    </td>
		</tr>
		<?php }?>
                <tr>
                    <td>
                        <table width=100% align="center" class="bordes">
                            <tr align="center">
                                <td>
                                    <input type=button name="volver" value="Volver"
                                    onclick="document.location='usr_efectores_listado.php'"
                                    title="Volver al Listado" style="">
                                </td>
                            </tr>
			</table>
                    </td>
                </tr>
	</table>
      </td>
    </tr>
</table>
</form>

<?=fin_pagina ();// aca termino ?>
