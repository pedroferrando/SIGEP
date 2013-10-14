<div style="float:left; margin: 0 1% 0 1%; width: 47%;">
    <p style="margin:0;">
        <b><?php echo $vacunas_reporte[$i]['nombre']; ?></b>
    </p>
    <table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=left>
        <tr>
            <td id=mo width="20%" >Edad/Cond</td>
            <?php 
                while(!$fil_primera->EOF){ ?>
                    <td id=mo >
                        <?php 
                        if($fil_primera->fields['descripcion_abreviada']!=""){
                            echo $fil_primera->fields['descripcion_abreviada']; 
                        }else{
                            echo $fil_primera->fields['descripcion']; 
                        }
                        ?>
                    </td>
                    <?php 
                    $fil_primera->MoveNext();
                } 
            ?>
        </tr>
        <?php for($k=0;$k<count($col_primera);$k++){ ?>
                <tr <?=atrib_tr()?>>
                    <td align="center"><?php echo $col_primera[$k]; ?></td>
                    <?php
                        $fil_primera->MoveFirst();
                        while(!$fil_primera->EOF){
                            ?>
                            <td align="center">
                                &nbsp;<?php echo $matriz[$k][$fil_primera->fields['id_vacuna_dosis']] ?>
                            </td>
                            <?php
                            $fil_primera->MoveNext();
                        }
                    ?>
                </tr>    
        <?php } ?>
    </table>
<p style="clear:both;">&nbsp;</p>
</div>