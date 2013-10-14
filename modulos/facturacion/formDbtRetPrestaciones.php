<table class="tablagenerica" width=100% align="center">
    <tr>
        <th>Nro Exp</th>
        <th>Nro Factura</th>
        <th>Apellido y Nombre</th>
        <th>Fecha Prest.</th>
        <th>C&oacute;digo</th>
        <th>Precio</th>
        <th>&nbsp;</th>
    </tr>
    <?php if($prestaciones && $prestaciones->NumRows()>0){ $i=0; ?>
        <?php while(!$prestaciones->EOF){ 
                if($i%2==0){
                    $classRow = "con";
                }else{
                    unset($classRow);
                }
                $id = $prestaciones->fields['id_prestacion'];
                ?>
                <tr class="<?php echo $classRow;?>">
                    <td><?php echo $prestaciones->fields['nro_exp']; ?></td>
                    <td><?php echo $prestaciones->fields['nro_fact_offline']; ?></td>
                    <td><?php echo $prestaciones->fields['apellido_benef']." ".
                                   $prestaciones->fields['apellido_benef_otro']." ".
                                   $prestaciones->fields['nombre_benef']." ".
                                   $prestaciones->fields['nombre_benef_otro']; 
                        ?>
                    </td>
                    <td><?php echo date('d/m/Y',  strtotime($prestaciones->fields['fecha_comprobante'])); ?></td>
                    <td><?php echo $prestaciones->fields['codigo']." ".$prestaciones->fields['diagnostico']; ?></td>
                    <td align="right">
                        $ <label><?php echo $prestaciones->fields['precio_prestacion']*$prestaciones->fields['cantidad']; ?></label>
                    </td>
                    <td align="center">
                        <a href="javascript:;" onclick="$('#tr_<?php echo $id;?>').toggle('slow',addFormAltaDebitoRetro('<?php echo $id;?>'));"> 
                            &#9660; 
                        </a>
                    </td>
                </tr>
                
                <tr height="90" style="display: none;" bgcolor="#F5F6CE"
                    id="tr_<?php echo $prestaciones->fields['id_prestacion']; ?>">
                    <td colspan="7" valign="top"></td>
                </tr>
                
                <?php $i++; $prestaciones->MoveNext(); ?>
        <?php } ?>
    <?php }else{ ?>
                <tr class="con">
                    <td align="center" colspan="7">No se encontraron prestaciones</td>
                </tr>
    <?php } ?>
</table>

