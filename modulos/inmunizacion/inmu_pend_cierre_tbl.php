<?php
    if($res_inmu->RecordCount()>0){ ?>
        <h3><?php echo "Periodo ".$unperiodo; ?></h3>
        <div>
            <table style="width: 100%; padding-bottom: 20px; border-collapse:collapse;">
                <tr>
                    <th style="border-bottom: solid black thin;width:  3%"><b>#</b></th>
                    <th style="border-bottom: solid black thin;width: 15%"><b>Fecha</b></th>
                    <th style="border-bottom: solid black thin;width: 20%"><b>Clave Beneficiario</b></th>
                    <th style="border-bottom: solid black thin;width: 30%"><b>Vacuna</b></th>
                    <th style="border-bottom: solid black thin;width: 30%"><b>Dosis</b></th>
                    <th style="border-bottom: solid black thin;width:  2%">
                        <input type="checkbox" onclick="select_prestaciones_inmu(this);"/>
                    </th>
                </tr>

                <?php while(!$res_inmu->EOF){ ?>
                        <tr <?php echo atrib_tr("transparent");?>>
                            <td align="center"><?php echo $contadordepracticas; ?></td>
                            <td align="center"><?php echo date('d/m/Y',strtotime($res_inmu->fields['fecha_inmunizacion']));  ?></td>
                            <td align="center"><?php echo $res_inmu->fields['clave_beneficiario']; ?></td>
                            <td><?php echo $res_inmu->fields['vacuna']; ?></td>
                            <td><?php echo $res_inmu->fields['dosis']; ?></td>
                            <td align="center">
                                <input type="checkbox" name="prestacion_inmu[]" 
                                    value="<?php echo $res_inmu->fields['id_prestacion_inmu']; ?>"/>
                                <input type="hidden" value="<?php echo $unperiodo; ?>" 
                                       name="periodo_inmu[<?php echo $res_inmu->fields['id_prestacion_inmu']; ?>]" 
                                       />
                            </td>
                        </tr>
                        <?php
                        $contadordepracticas++;
                        $res_inmu->MoveNext();
                      }
                ?>                                    
            </table>
        </div>                             
        <?
    } else {
        echo '<h3>Periodo ' . $unperiodo . ' - No hay inmunizaciones pendientes para dar cierre</h3><div></div>';
    }
?>
