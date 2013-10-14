<?php echo $title; ?>
    <table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?php echo $bgcolor3;?>' align=center>
        <tr align="center" id="ma">
            <td colspan="13">
                <?php $paginacion->generaPaginacion($total, $back='<', $next='>', "reporte_beneficiarios_ceb.php", $arr_param_url, $classCss='linkPage');?>
            </td>
        </tr>
    </table>
    <table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?php echo $bgcolor3?>' align=center>
        <tr>
            <td align=right id=mo></td>
            <td align=right id=mo>Apellido</td>
            <td align=right id=mo>Nombre</td>
            <td align=right id=mo>Fecha Nac.</td>
            <td align=right id=mo>Tipo Doc.</td>
            <td align=right id=mo>Nro Doc.</td>
            <td align=right id=mo>Direcci&oacute;n</td>
            <td align=right id=mo>Fecha Inscripci&oacute;n</td>
            <td align=right id=mo>Fecha Ultima Prestaci&oacute;n</td>
            <td align=right id=mo>C&oacute;d. de Prestaci&oacute;n</td>
            <td align=right id=mo>Lugar de Prestaci&oacute;n</td>
            <td align=right id=mo>Activo</td>
            <td align=right id=mo>Mensaje Baja</td>
        </tr>
        <?php if($result){ $c=$dsd+1;//$c=1; ?>
            <?php while(!$result->EOF){ ?>
                    <tr <?php echo atrib_tr()?>>    
                        <td align="center"><?php echo $c; ?></td>  
                        <td><?php echo $result->fields['afiapellido'];?></td>
                        <td><?php echo $result->fields['afinombre'];?></td>  
                        <td align="center"><?php echo date('d/m/Y',strtotime($result->fields['afifechanac']));?></td>
                        <td align="center"><?php echo $result->fields['afitipodoc']; ?></td>
                        <td><?php echo $result->fields['afidni']; ?></td>
                        <td><?php 
                                  echo "CALLE: ".utf8_decode($result->fields['afidomcalle'])." Nº: ".$result->fields['afidomnro']." ";
                                  echo $result->fields['afidompiso']." ".$result->fields['afidompiso']; 
                            ?>
                        </td>
                        <td align="center"><?php echo date('d/m/Y',strtotime($result->fields['fechainscripcion']));?></td>
                        <td align="center">
                            <?php echo $result->fields['fechaultimaprestacion']!="" ? date('d/m/Y',strtotime($result->fields['fechaultimaprestacion'])) : "";?>
                        </td>
                        <td align="center"><?php echo $result->fields['codigoprestacion']; ?></td>
                        <td>
                            <?php if($result->fields['cuie']!=""){ echo $result->fields['cuie']." - ".$result->fields['lugar_prestacion'];} ?>
                        </td>
                        <td align="center"><?php echo $result->fields['activo']=='S' ? 'SI' : 'NO'; ?></td>
                        <td><?php echo $result->fields['mensajebaja']; ?></td>
                    </tr>
                    <?php $result->MoveNext(); $c++; ?>
            <?php }?>
                    <tfoot>
                        <tr align="center" id="ma">
                            <td colspan="13">
                                <?php $paginacion->generaPaginacion($total, $back='<', $next='>', "reporte_beneficiarios_ceb.php", $arr_param_url, $classCss);?>
                            </td>
                        </tr>
                    </tfoot>
        <?php }?>
    </table>