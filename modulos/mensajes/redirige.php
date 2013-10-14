<?php
require_once("../../config.php");
switch ($_POST['Submit']){
	case "Cancelar":{ header('location: ./mensajes.php');
	                  break;}
    case "Reenviar mensaje":{include_once "./guardar_mens.php";
                              break;}
    default:{ 
?>
<html>
<head>
<title>Nuevo Mensaje</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? cargar_calendario(); ?>
<SCRIPT language='JavaScript'>
function comprueba()
{if(document.form.venc.value=='') {
 alert("Debe seleccionar fecha de vencimiento.");
 return false;
 }
 if(document.form.para.value=='?') {
 alert("Debe seleccionar usuario.");
 return false;
 }
 if(document.form.nota.value=='') {
 alert("El mensaje está en blanco.");
 return false;
 }
 return true;
}
</SCRIPT>
<!--<link rel=stylesheet type='text/css' href='../layout/css/win.css'>-->
</head>
<body bgcolor="#E0E0E0">



<fieldset style="
          -webkit-border-radius:10px;
          -moz-border-radius:10px;
          border-radius:10px;">
  <legend>Reenviar Mensaje</legend>
  <form name="form" action="redirige.php" method="post">
    <?php
      $id_mensaje=$_POST['radio']; 
      $ssql_busca="select numero, nro_orden,usuario_destino,comentario,fecha_vencimiento from mensajes where id_mensaje=".$id_mensaje;
      db_tipo_res('a');
      $result=$db->Execute($ssql_busca) or die($db->ErrorMsg());
    ?>
    <table align="center">
      <tr bgcolor="#c0c6c9">
        <td colspan="2" align="center" >REENVIAR MENSAJE</td>
        
      </tr>

      <tr>
        <td>Destinatario:
            <select name="para">
              <option value='?'></option>
              <?php
                  $ssql1="select * from usuarios where nombre!='root' order by apellido,nombre;";
                  db_tipo_res('a');
                          $result1=$db->Execute($ssql1) or die($db->ErrorMsg());
                  while(!$result1->EOF){
                 ?>
                  <option> 
                    <? echo $result1->fields['apellido'].", ". $result1->fields['nombre']." [".$result1->fields['login']."]";?>
                  </option>
                  <?php 
                 $result1->MoveNext();
                }//while?>
              <option selected> 
                <? echo $result->fields['usuario_destino']; ?>
              </option>
              <option>Todos</option>
            </select>
        </td>
        <td>Fecha Vencimiento: 
          <? 
            $fech=substr($result->fields['fecha_vencimiento'],0,10);
            $hora=substr($result->fields['fecha_vencimiento'],11,16);
          ?>
          
          <input name="venc" value="<?php echo fecha($fech);?>" type=text >
          <?php echo link_calendario("venc"); ?>
        </td>
      </tr>
      
      <tr>
        <td colspan="2">
          <textarea name="nota" cols="90" rows="10" >Reenvío de Mensaje: 
<?php echo $result->fields['comentario']; ?>  
[Remitente:  <?php echo $result->fields['usuario_destino'];?>] 
          </textarea>
        </td>
      </tr>

      <tr>
        <td colspan="2">
          <input type="submit" name="Submit" value="Reenviar mensaje" onClick="return comprueba();">
          <input type="submit" name="Submit" value="Cancelar">
        </td>
        
      </tr>

    </table>
    <input type="hidden" name="anterior" value="<?php echo $result->fields['comentario']; ?>">
    <input type="hidden" name="nro_ord" value="<?php echo $result->fields['nro_orden'] +1;?>">
    <input type="hidden" name="id_m" value="<? echo $id_mensaje;?>">
    <input type="hidden" name="tipo_m" value=0>
    <input type="hidden" name="tipo2" value='MRU'>

  </form>
</fieldset>


<?php
 }//default
} //swuitch
?>
</body>
</html>