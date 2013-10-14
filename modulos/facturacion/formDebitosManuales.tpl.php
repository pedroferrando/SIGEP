<div style="width:400px;margin:0 auto">
    <font style='float: left;font-size: medium'><b>Motivos: </b></font>
    <input type="text" id="nuevomotivo_<?php echo $prestacion ?>" name="nuevomotivo" Style="float: left;width:275px;margin-bottom: 5px;display:none"/>
    <select id="motivo_<?php echo $prestacion ?>" name=motivo Style="float: left;width:290px;margin-bottom: 5px;">
        <option value=-1 selected="selected">Seleccione</option>
        <?php
        if (!is_null($res_motivos)) {
            while (!$res_motivos->EOF) {
                $descripcion = $res_motivos->fields['descripcion'];
                $id_motivo = $res_motivos->fields['id_motivo'];
                ?>
                <option value="<?php echo $id_motivo; ?>"
                        ><?php echo $descripcion ?></option>
                        <?php
                        $res_motivos->movenext();
                    }
                }
                ?>
    </select>
    <img id="masmotivo_<?php echo $prestacion ?>"  Style="cursor: pointer" src="../../imagenes/mas_g.gif"></img>
    <img id="loading_<?php echo $prestacion ?>"  Style="display: none" src="../../imagenes/mini_loading.gif"></img>
    <img id="confirmarmotivo_<?php echo $prestacion ?>" Style="display:none;cursor: pointer" src="../../imagenes/okey.gif"></img>
    <img id="cancelarmotivo_<?php echo $prestacion ?>" Style="display:none;cursor: pointer" src="../../imagenes/cancelar.gif"></img>
    <br>
    <font style='float: left;font-size: medium'><b>Observaciones: </b></font>
    <textarea style="float: left;" cols="50" rows="2" value="<?php echo $observaciones ?>" name="observaciones" id="observaciones"/>
    <br>
    <div style='width: 160px;margin:60px auto 0 auto'>
        <input style='float: left;width: 80px' type="button" id='btn_cancelar_<?php echo $prestacion ?>' value="CANCELAR"></input>
        <input style='float: left;width: 80px' type="button" id='btn_aceptar_<?php echo $prestacion ?>' value="ACEPTAR"></input>
    </div>
</div>