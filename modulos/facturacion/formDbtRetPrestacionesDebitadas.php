<table class="tablagenerica" width=100% align="center">
    <tr>
        <th>Nro Exp</th>
        <th>NroFact.</th>
        <th width="15%">Apellido y Nombre</th>
        <th>F. Prest.</th>
        <th width="10%">C&oacute;digo</th>
        <th>Precio</th>
        <th width="15%">Observaciones</th>
        <th width="15%">Motivo</th>
        <th width="15%">Identificaci&oacute;n</th>
        <th>&nbsp;</th>
    </tr>
    <?php if($prestDeb && $prestDeb->NumRows()>0){ $i=0; ?>
        <?php while(!$prestDeb->EOF){ 
                if($i%2==0){
                    $classRow = "con";
                }else{
                    unset($classRow);
                }
                ?>
                <tr class="<?php echo $classRow;?>">
                    <td><?php echo $prestDeb->fields['exp_prest']; ?></td>
                    <td><?php echo $prestDeb->fields['nro_fact_offline']; ?></td>
                    <td><?php echo $prestDeb->fields['apellido_benef']." ".
                                   $prestDeb->fields['apellido_benef_otro']." ".
                                   $prestDeb->fields['nombre_benef']." ".
                                   $prestDeb->fields['nombre_benef_otro']; 
                        ?>
                    </td>
                    <td><?php echo date('d/m/Y',  strtotime($prestDeb->fields['fecha_comprobante'])); ?></td>
                    <td><?php echo $prestDeb->fields['codigo']." ".$prestDeb->fields['diagnostico']; ?></td>
                    <td align="right"> 
                        $ <label><?php echo $prestDeb->fields['precio_prestacion']*$prestDeb->fields['cantidad']; ?></label>
                    </td>
                    <td><?php echo $prestDeb->fields['observaciones']; ?></td>
                    <td><?php echo $prestDeb->fields['motivo']; ?></td>
                    <td><?php echo $prestDeb->fields['identificacion']; ?></td>
                    <td align="center">
                        <a class="sprite-gral icon-remove" title="Eliminar Debito" href="javascript:void(0);" 
                           onclick="if(confirm('Desea eliminar el debito?')){
                                        deleteDebitoRetroactivo(this,'<?php echo $prestDeb->fields['id']; ?>');
                                    }else{
                                        return false;
                                    }"></a>
                    </td>
                </tr>
                <?php $i++; $prestDeb->MoveNext(); ?>
        <?php } ?>
    <?php }else{ ?>
                <tr class="con">
                    <td align="center" colspan="10">No se encontraron d&eacute;bitos</td>
                </tr>
    <?php } ?>
</table>
