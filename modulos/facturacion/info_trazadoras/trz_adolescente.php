<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
    <tr>
        <td id="mo">Fecha de Control</td>
        <td id="mo">Lugar</td>
        <td id="mo">C&oacute;digo</td>
        <td id="mo">Sexo</td>
        <td id="mo">Peso</td>
        <td id="mo">Talla</td>
        <td id="mo">Imc</td>
        <td id="mo">Percen Imc Edad</td>
        <td id="mo">TA Min</td>
        <td id="mo">TA Max</td>
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
                            <td><?php echo $result->fields['peso'];?></td>
                            <td><?php echo $result->fields['talla'];?></td>
                            <td><?php echo $result->fields['imc'];?></td>
                            <td><?php echo $result->fields['percen_imc_edad'];?></td>
                            <td><?php echo $result->fields['tamin'];?></td>
                            <td><?php echo $result->fields['tamax'];?></td>
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
