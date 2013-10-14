<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
    <tr>
        <td id="mo">Fecha de Control</td>
        <td id="mo">Lugar</td>
        <td id="mo">C&oacute;digo</td>
        <td id="mo">Sexo</td>
        <td id="mo">Tal</td>
    </tr>
    <?php
        foreach($value as $v){
            if($v!=$sql_prev){
                $result = sql($v) or die;
                if($result){
                    while(!$result->EOF){ 
                        ?>
                        <tr <?php echo atrib_tr()?>>
                            <td><?php echo date('d/m/Y',strtotime($result->fields['fecha_control']));?></td>
                            <td><?php echo $result->fields['efector'];?></td>
                            <td><?php echo $result->fields['codnomenclador']; ?></td>
                            <td><?php echo $result->fields['sexo']; ?></td>
                            <td><?php echo $result->fields['tal'];?></td>
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