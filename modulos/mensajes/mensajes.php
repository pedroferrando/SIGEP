<?php
require_once("../../config.php");
require_once("funciones.php");

switch ($_POST['boton'])
{case "Enviar Nuevo Mensaje":{require_once("../mensajes/nuevo_mens.php");
							  break;
							 }
 case "Reenviar Mensaje": {require_once("../mensajes/redirige.php");
							break;
						   }
 case "Borrar Mensaje":{require_once("../mensajes/borrar.php");
						break;
					   }
 case "Mensajeria":{require_once('../mensajes/mensajeria.php');
					exit;
					break;
					   }

default:{ $filas_encontradas=0;
echo $html_header;
?>
<script>
// funciones que iluminan las filas de la tabla
function sobre(src,color_entrada) {
	src.style.backgroundColor=color_entrada;src.style.cursor="hand";
}
function bajo(src,color_default) {
	src.style.backgroundColor=color_default;src.style.cursor="default";
}
function comprueba()
{var i;
if (document.form.cantr.value==0)
{alert("Debe seleccionar un mensaje");
 return false;
}
 if (document.form.cantr.value==1)
 {if(document.form.radio.checked)
  return true;
 }
 for(i=0;i<document.form.radio.length;i++)
 {if(document.form.radio[i].checked)
  return true;
 }//for
alert("Debe seleccionar un mensaje");
return false;
}

function borrar()
{var valor;
 if (!comprueba())
 return false;
 else
 {valor=prompt('Dime el motivo por el cual desestimas este mensaje','');
  if ((valor==null) || (valor==""))
   return false;
  else
  {window.document.form.mensaje.value=valor;
   return true;
  }
 }
}
function controlnoc(){
  alert("Hay ordenes de compra que están por vencer.");
}
function controlvenc(){
  alert("Hay ordenes de compra vencidas.");
}

</script>
<?php
//este codigo es el que controla si ordenes de compra por vencer o mensajes que venecen hoy
if(($_POST['boton1']!='Mensajeria')&&($_ses_mensajes_primera_v != 1)){
   phpss_svars_set("_ses_mensajes_primera_v",1);
   $fecha_actual=date("Y-m-d H:m:s");
   $fecha_proxima=date("Y-m-d",mktime(0,0,0,date("m"),(date("d")+7),date("Y")));
   $sql = "select count(id_mensaje) as cant from mensajes where tipo1='LIC' and tipo2='NOC' and terminado='f' and desestimado='f' and usuario_destino='".$_ses_user['login']."' and fecha_vencimiento < '$fecha_actual'";
   $result=$db->Execute($sql) or die($db->ErrorMsg());
   $vench=$result->fields["cant"];
   $sql = "select count(id_mensaje) as cant from mensajes where tipo1='LIC' and tipo2='NOC' and terminado='f' and desestimado='f' and usuario_destino='".$_ses_user['login']."' and fecha_vencimiento <= '$fecha_proxima'";
   $result=$db->Execute($sql) or die($db->ErrorMsg());
   $noc=$result->fields["cant"];

 if($noc){
 ?>
 <script language='JavaScript'>
  controlnoc();
 </script>
 <?
 }//if noc

 if($vench){
 	?>
   <script language='JavaScript'>
	 controlvenc();
   </script>
 <? }//vence hoy
 }//if

 // actualizo fecha de recibido y bit de recibido de los que no lo tienen
$sql1="select usuario_origen,id_mensaje,fecha_recibo from mensajes where recibido='f' or fecha_recibo is null and usuario_destino='".$_ses_user['login']."'";
$result1=$db->Execute($sql1) or die($db->ErrorMsg());
$fecha_r=date("Y-m-d H:i:s");
while (!$result1->EOF)
{
 if($result1->fields['fecha_recibo']==''){
   $sql="update mensajes set fecha_recibo='".$fecha_r."' where id_mensaje=".$result1->fields['id_mensaje'];
   $result=$db->Execute($sql) or die($db->ErrorMsg());
   }
$result1->MoveNext();      
}
 ?>
<fieldset style="-webkit-border-radius:16px;-moz-border-radius:16px;border-radius:16px;">
  <legend>Sistema de mensajer&iacute;a</legend>
  <form name="form" method="post" action="mensajes.php">
  <center>
      <table width="70%" border="0">
        <tr bgcolor="#c0c6c9"> 
          
          <td width="52" valign="top"> 
            <div align="right">
            	<input style="border-style: outset;	border-width: 1px; border-color: #000000; background-color: #F5F5F5;
  			color: #000000;font-size=8pt;text-align: center;cursor:pointer;" type="submit" name="boton" value="Mensajeria">
            </div>
          </td>
        </tr>
      </table>

      <table border="0" cellspacing="2" cellpadding="0" width="70%">
  	  <tr bgcolor="#006699">
          <td width="24" height="19" valign="top">&nbsp;</td>
          <td width="129" valign="top"> 
            <center>
              <a style="text-decoration:none" href=<?php echo "mensajes.php?est=0"; ?>><font size="2" family="helvetica, sans-serif" color="#c0c6c9"><b>Fecha entrega</b></font></a> 
            </center>
          </td>
          <td width="391"> 
            <center>
              <a  style="text-decoration:none" href=<?php echo "mensajes.php?est=1"; ?>><font size="2" family="helvetica, sans-serif" color="#c0c6c9"><b>Mensaje</b></font></a> 
            </center>
          </td>
          <td width="145"> 
            <a  style="text-decoration:none " href=<?php echo "mensajes.php?est=2"; ?>><div align="center"><font size="2" family="helvetica, sans-serif" color="#c0c6c9"><b>Vencimiento</b></font></div></a>
          </td>
          <td width="300"> 
            <a  style="text-decoration:none " href=<?php echo "mensajes.php?est=2"; ?>><div align="center"><font size="2" family="helvetica, sans-serif" color="#c0c6c9"><b>Remitente</b></font></div></a>
          </td>
      </tr>
        
        <?php
           //$fecha_actual=date("Y/m/d H:i:s");
           $fecha_actual=date("Y/m/d");
           $hora_actual=date("H:i");
           //$fecha_a=substr($fecha_actual,0,10);
           //$hora_a=substr($fecha_actual,10,16);
          list($aa,$ma,$da) = explode("/",$fecha_actual);
          list($ha,$mia)= explode(":",$hora_actual);
          switch($est){
            case "0": $orden=" order by fecha_entrega";break;
            case "1": $orden=" order by comentario";break;
            case "2": $orden=" order by fecha_vencimiento";break;
            default:$orden=" order by fecha_recibo desc";break;
          }
                         
          $sql="SELECT usuario_origen,tipo1,recibido,id_mensaje,comentario ,titulo, fecha_entrega, fecha_vencimiento,fecha_recibo 
                                  FROM mensajes.mensajes 
                                  WHERE fecha_vencimiento::date >= current_date and terminado='f' and desestimado='f' and usuario_destino='".$_ses_user['login']."'".$orden;
          $result=$db->Execute($sql) or die($db->ErrorMsg());
              $cantidad+=$result->RecordCount();
              $i=0;
              while(!$result->EOF){ 
                    if ($i==0)
                {$color=$bgcolor2;
                 $color2=$bgcolor1;
                 $i=1;
                }
              else
              {$color=$bgcolor1;
               $color2=$bgcolor2;
               $i=0;
              }
           $fecha_v=substr($result->fields['fecha_vencimiento'],0,10);
                 $hora_v=substr($result->fields['fecha_vencimiento'],11,16);
           list($a,$m,$d) = explode("-",$fecha_v);
                 list($hv,$miv,$sv)= explode(":",$hora_v);
           if (($aa>$a) || (($aa==$a) && ($ma>$m)) || (($aa==$a) && ($ma==$m) && ($da-$d>=1)))
            $color1="FF0000";//rojo
           elseif((($aa==$a)&&($ma==$m)&&($da==$d)&&($ha>$hv))||(($aa==$a)&&($ma==$m)&&($da==$d)&&($ha==$hv)&&($mia>$miv))) $color1="FF0000";  // saque esto &&($mia>$miv) porque no funciona con la condicion and
           else $color1=''; //rojo
           if ((($aa==$a)&&($ma==$m)&&(($da+1)==$d))||(($aa==$a)&&($ma+1==$m)&&($d==1)&&(($da==30)||($da==31)))) $color1="FFFF00"; //amarillo
           elseif (($aa==$a)&&($ma==$m)&&($da==$d)&&(($ha<$hv)||(($ha==$hv)&&($mia<=$miv)))) $color1="FFFF00"; //amarillo

                 if  ($result->fields['recibido']=='f') $color1="00FF00";  //verde
           if ($result->fields['fecha_recibo']=="")
              {$filas_encontradas++;
                 $id[]=$result->fields['id_mensaje'];
              }
            
             ?>
        <input type="hidden" name="tipo1" value="<?php echo $result->fields['tipo1']; ?>">
            <a href="ver_mens.php?id_mensaje=<? echo $result->fields['id_mensaje'];?>&donde=0"> 
            <tr bgcolor="<?php echo $color; ?>"  title="<?php echo $result->fields['titulo']; ?>" onMouseOver="sobre(this,'#FFFFFF');" onMouseOut="bajo(this,'<? echo $color;?>' );"> 
              <td width="24" height="18" valign="top"  > 
                <input type="radio" name="radio" value="<?php echo $result->fields['id_mensaje'] ?>">
              </td>
              <td width="126" valign="top" > 
                <center>
                  <font size=2 color="<?php echo $color2; ?>"> 
                  <?php 
                  $fecha1=fecha(substr($result->fields['fecha_entrega'],0,10));
                  $tiempo1=substr($result->fields['fecha_entrega'],10,18);
                  echo $fecha1;//$fecha1.$tiempo1;
                  ?>
                  </font> 
                </center>
        </td>
              <td width="391" valign="left"> <font size=2 color="<?php echo $color2; ?>"> 
                
                  <?php echo $result->fields['comentario'];?>
                
                </font> </td>
              <td width="145" valign="top" bgcolor="<?php echo $colorfv;?>"><font size=2 color="<?php echo $color2; ?>"> 
                <center>
                  <?php 
                    $fecha=fecha(substr($result->fields['fecha_vencimiento'],0,10));
                    $tiempo=substr($result->fields['fecha_vencimiento'],10,18);
                    echo $fecha;//$fecha.$tiempo;?>
                </center>
                </font>
              </td>
              <td width="300" valign="top"> 
                <font size=2 color="<?php echo $color2; ?>">   
                <b><? //obtengo el nombre del usuario dado el login 
               $sql="select nombre,apellido from usuarios where login='".$result->fields['usuario_origen']."';";
               $nombre_o=$db->Execute($sql) or die($db->ErrorMsg());
               echo $nombre_o->fields['apellido'].", ". $nombre_o->fields['nombre']." [".$result->fields['usuario_origen']."]";
                   //echo $result->fields['usuario_origen'];?></b>
                </font> 
                
              </td>
            </tr>
            </a> 
            <input type="hidden" name="comentario[<?php echo $result->fields['id_mensaje']; ?>]" value="<?php echo $resultado['comentario']; ?>">
            
        <?   
       $result->MoveNext();
      }//while 
    ?>  


      </table>
  		<br />
      <div style="position:relative; width:70%; height:5%; overflow:auto;" align="left">

          <b>*- Los mensajes se visualizar&aacute;n unicamente hasta la fecha de vencimiento inclusive.</b>
      </div>
      
      <br />

      <div style="position:relative; width:70%; height:2%; overflow:auto;"> 
        <hr>
      </div>
		  <div style="position:relative; width:70%; height:10%; overflow:auto;">
          <fieldset style="-webkit-border-radius:10px;-moz-border-radius:10px;border-radius:10px;background-color:rgba(166, 173, 255, 0.360784);">
            <legend><b>Acciones sobre el mensaje</legend>
            <input type="hidden" name="cantr" value="<?PHP echo $cantidad; ?>">
            <input type="hidden" name="mensaje" value="">
            <input type="submit" name="boton" value="Enviar Nuevo Mensaje" 
              style="-webkit-text-size-adjust:none;text-align:center;
                    border-width:3px;-webkit-border-radius:11px;-moz-border-radius:11px;">

            &nbsp&nbsp&nbsp<input type="submit" name="boton" value="Reenviar Mensaje" onClick="return comprueba();"
                    style="-webkit-text-size-adjust:none;text-align:center;
                    border-width:3px;-webkit-border-radius:11px;-moz-border-radius:11px;">
            
            &nbsp&nbsp&nbsp<input type="submit" name="boton" value="Borrar Mensaje" onClick="return borrar();"
                    style="-webkit-text-size-adjust:none;text-align:center;
                    border-width:3px;-webkit-border-radius:11px;-moz-border-radius:11px;">
                    </fieldset>
      </div>


  
  </center>
  </form>
</fieldset>
</body>
</html>
<?php
 }
}// fin switch

?>