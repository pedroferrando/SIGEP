<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
    <tr>
        <td id="mo">Fecha de Control</td>
        <td id="mo">Lugar</td>
        <td id="mo">Sem. Gestac.</td>
        <td id="mo">Fum</td>
        <td id="mo">Fpp</td>
        <td id="mo">F. 1&deg; Ctrl</td>
        <td id="mo">Alt Ut.</td>
        <td id="mo">Peso</td>
        <td id="mo">Talla</td>
        <td id="mo">TA Min</td>
        <td id="mo">TA Max</td>
    </tr>
    <?php
        foreach($value as $v){
            //if($v!=$sql_prev){
                $result = sql($v) or die;
                if($result){
                    while(!$result->EOF){ 
                        ?>
                        <tr <?php echo atrib_tr()?>>
                            <td><?php echo date('d/m/Y',strtotime($result->fields['fecha_control']));?></td>
                            <td><?php echo $result->fields['efector'];?></td>
                            <td><?php echo number_format($result->fields['sem_gestacion'],2); ?></td>
                            <td><?php echo date('d/m/Y',strtotime($result->fields['fum']));?></td>
                            <td><?php echo date('d/m/Y',strtotime($result->fields['fpp']));?></td>
                            <td><?php echo date('d/m/Y',strtotime($result->fields['fpcp']));?></td>
                            <td><?php echo $result->fields['altura_uterina'];?></td>
                            <td><?php echo $result->fields['peso_embarazada'];?></td>
                            <td><?php echo $result->fields['talla'];?></td>
                            <td>
                                <?php 
                                    if($result->fields['tension_arterial_minima']!="")
                                        echo $result->fields['tension_arterial_minima'];
                                    elseif($result->fields['tamin']!="")
                                        echo $result->fields['tamin'];
                                ?>
                            </td>
                            <td>
                                <?php 
                                    if($result->fields['tension_arterial_maxima']!="")
                                        echo $result->fields['tension_arterial_maxima'];
                                    elseif($result->fields['tamax'])
                                        echo $result->fields['tamax'];
                                ?>
                            </td>
                        </tr>
                        <?php
                        $result->MoveNext();
                    }
                    if($result->_numOfRows==0){ 
                        $datos = $arr_alt[$key][$c];
                        ?>
                        <tr <?php echo atrib_tr()?>>
                            <td><?php echo date('d/m/Y',strtotime($datos['fecha_prestacion']));?></td>
                            <td><?php echo $datos['efector'];?></td>
                            <td colspan="20" align="center">
                                Sin datos
                            </td>
                        </tr> 
                    <?php }
                }
            //}
            $sql_prev = $v;
            $c++;
        }
    ?>
</table>