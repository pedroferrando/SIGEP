<div class="contenido">
    <div id='monitor' align='center' style="font-size: small;display: none;color:#900000 "></div>
    <div class="titulo_pagina" align='center'>Listado de Talleres Pendientes</div>
    <font style='font-size: medium'><b>Efector:</b></font> 
    <select id="efector" name=efector Style="width:450px;margin-bottom: 5px;">
        <option value=-1>Seleccione</option>
        <?php
        while (!$res_efectores->EOF) {
            $cuie = $res_efectores->fields['cuie'];
            $nombre_efector = $res_efectores->fields['nombreefector'];
            ?>
            <option value="<?php echo $cuie; ?>"
            <?php
            if ($cuie == $cuie_elegido) {
                echo "selected";
            }
            ?> ><?php echo $cuie . " - " . $nombre_efector ?></option>
                    <?php
                    $res_efectores->movenext();
                }
                ?>
    </select><div id="loading" style="display: none" class="sprite-gral icon-spinner"></div>
<table class='tablagenerica' id="comprobantes_taller">
    <tr>
        <th>Comprobante</td>
        <th style="width: 60px">Inscriptos Activos/Total</th>            
        <th>Codigo Prestacion</th>
        <th>Monto Unitario</th>
        <!--th>Cantidad</th-->
        <th>Total</th>
        <th>Fecha Comprobante</th>
        <th style="width: 60px">Facturar</th>
        <th style="width: 60px">Detalles</th>
    </tr>
</table>
</div>
<?php
echo fin_pagina(); // aca termino ?>
