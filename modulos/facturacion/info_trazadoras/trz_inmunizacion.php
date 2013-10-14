<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
    <tr>
        <td id="mo">Fecha Inmun.</td>
        <td id="mo">Lugar</td>
        <td id="mo">C&oacute;digo</td>
        <td id="mo">Dosis</td>
        <td id="mo">Leboratorio</td>
        <td id="mo">Presentaci&oacute;n</td>
        <td id="mo">Lote</td>
        <td id="mo">Fecha Venc.</td>
        <td id="mo">Grupo Riesgo</td>
    </tr>
    <?php
        foreach($value as $v){ 
            if($v!=$sql_prev){
                $result = sql($v) or die;
                if($result){
                    while(!$result->EOF){
                        ?>
                        <tr <?php echo atrib_tr()?>>
                            <td><?php echo date('d/m/Y',strtotime($result->fields['fecha_inmunizacion']));?></td>
                            <td><?php echo $result->fields['efector'];?></td>
                            <td>
                                <?php 
                                    echo $result->fields['categoria']." ".
                                         $result->fields['codigo']." ".
                                         $result->fields['patologia']; 
                                ?>
                            </td>
                            <td><?php echo $result->fields['desc_dosis'];?></td>
                            <td><?php echo $result->fields['laboratorio'];?></td>
                            <td><?php echo $result->fields['desc_presentacion'];?></td>
                            <td><?php echo $result->fields['lote'];?></td>
                            <td>
                                <?php 
                                if($result->fields['fecha_vencimiento']!='9999-01-01 00:00:00'){
                                    echo date('d/m/Y',strtotime($result->fields['fecha_vencimiento']));
                                }
                                ?>
                            </td>
                            <td><?php echo $result->fields['desc_grupo_riesgo'];?></td>
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
                            <td><?php echo $datos['cod_nomenclador'];?></td>
                            <td colspan="20" align="center">
                                Sin datos
                            </td>
                        </tr>     
                    <?php }
                }
            }
            $sql_prev = $v;
            $c++;
        }
    ?>
</table>