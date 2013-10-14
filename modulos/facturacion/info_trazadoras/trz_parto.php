<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
    <tr>
        <td id="mo">Fecha de Parto</td>
        <td id="mo">Lugar</td>
        <td id="mo">Apgar</td>
        <td id="mo">Peso</td>
        <td id="mo">Vdrl</td>
        <td id="mo">Talla RN</td>
        <td id="mo">Perim Cef RN</td>
    </tr>
    <?php
        foreach($value as $v){
            if($v!=$sql_prev){
                $result = sql($v) or die;
                if($result){
                    while(!$result->EOF){ 
                        ?>
                        <tr <?php echo atrib_tr()?>>
                            <td><?php echo date('d/m/Y',strtotime($result->fields['fecha_parto']));?></td>
                            <td><?php echo $result->fields['efector'];?></td>
                            <td><?php echo $result->fields['apgar']; ?></td>
                            <td><?php echo $result->fields['peso']; ?></td>
                            <td><?php echo $result->fields['vdrl'];?></td>
                            <td><?php echo $result->fields['talla_rn'];?></td>
                            <td><?php echo $result->fields['perimcef_rn'];?></td>
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
            }
            $sql_prev = $v;
            $c++;
        }
    ?>
</table>