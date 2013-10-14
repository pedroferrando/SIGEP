<?php
/*
  Author: ferni

  modificada por
  $Author: ferni $
  $Revision: 1.30 $
  $Date: 2006/07/20 15:22:40 $
 */
require_once("../../config.php");

variables_form_busqueda("efec_nom_listado");

$fecha_hoy = date("Y-m-d H:i:s");
$fecha_hoy = fecha($fecha_hoy);

$sql_tmp = "select distinct(tipoefector) from facturacion.smiefectores order by tipoefector";

echo $html_header;
?>
<form name=form1 action="efec_nom_listado.php" method=POST style="padding-top:10px">

    <? $result = sql($sql_tmp) or die; ?>

    <table border=0 width=80% cellspacing=2 cellpadding=2 bgcolor='<?= $bgcolor3 ?>' align=center>
        <tr>     	
            <td align=right id=mo><a id=mo>Tipos de Efector</a></td>
        </tr>
        <?
        while (!$result->EOF) {
            $ref = encode_link("tipoefec_nom_admin.php", array("tipoefector" => $result->fields['tipoefector']));
            $onclick_elegir = "location.href='$ref'";
            ?>

            <tr <?= atrib_tr() ?>>     
                <td align=center onclick="<?= $onclick_elegir ?>"><?= $result->fields['tipoefector'] ?></td>     
            </tr>
            <? $result->MoveNext();
        } ?>

    </table>
</form>
</body>
</html>
<?
echo fin_pagina(); // aca termino ?>