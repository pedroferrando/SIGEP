<?php
require_once ("../../config.php");

$facturasabiertas = $parametros["facturas"];
?>
<div style="width: 240px;margin:0 auto;">
    <table border=0 cellspacing=2 cellpadding=2 align=center style="border: #000000 solid thin;float: left;margin-left:30px">
        <tr style="margin: 10px 20px;background-color:lightcoral">
            <td ><b>Facturas Abiertas</b></td> 
            <td ><b>Efector</b></td>  
        </tr>
        <? foreach ($facturasabiertas as $unafabierta) { ?>                        
            <tr>
                <td align="center">
                    <?
                    echo $unafabierta['nrof'];
                    ?>
                </td>
                <td align="center">
                    <?
                    echo $unafabierta['cuie'];
                    ?>
                </td>
            </tr>
        <? } ?>
    </table>
</div>
