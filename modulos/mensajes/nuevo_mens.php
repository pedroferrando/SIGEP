<?php
require_once("../../config.php");
switch ($_POST['bot']){
	case "Cancelar":{ header('location: ./mensajes.php');
	                  break;}
    case "Enviar mensaje":{require "../mensajes/guardar_mens.php";
                           break;
                          }
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
 alert("El mansaje está en blanco.");
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
  <legend>Nuevo Mensaje</legend>
  <form name="form" action="nuevo_mens.php" method="post">
    <table align="center">
      <tr bgcolor="#c0c6c9">
        <td colspan="2" align="center" >NUEVO MENSAJE</td>
        
      </tr>

      <tr>
        <td>Destinatario:
            <select name="para">
              <option value='?'>Seleccione</option>
              <?php $ssql1="select login, nombre, apellido from usuarios where nombre!='root' and login!='admin' and login!='muleto' order by apellido;";
                  db_tipo_res('a');
                  $result1=$db->Execute($ssql1) or die($db->ErrorMsg());
                  while(!$result1->EOF){?>
                                  <option value='<?=$result1->fields['login']?>'>
                                      <?php echo $result1->fields['apellido']. ' '.$result1->fields['nombre']. ' ['.$result1->fields['login']. ']';?>
                                  </option>
                                  <?php $result1->MoveNext();
                              }//while?>
              <option value='Todos'>Todos</option>
          </select>
        </td>
        <td>Fecha Vencimiento: 
            <input name="venc" type=text >
            <?php echo link_calendario("venc"); ?>
        </td>
      </tr>
      
      <tr>
        <td colspan="2"><textarea name="nota" cols="90" rows="5"></textarea></td>
      </tr>

      <tr>
        <td colspan="2">
          <input type="submit" name="bot" value="Enviar mensaje" onClick="return comprueba();">
          <input type="submit" name="bot" value="Cancelar">
        </td>
        
      </tr>

    </table>
    <input type="hidden" name="tipo_m" value=1>

  </form>
</fieldset>




<?php
 }//default
} //fin switch
?>
</body>
</html>