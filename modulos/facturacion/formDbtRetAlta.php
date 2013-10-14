<form id="formAltaDebitoRetro">
    <table width="100%">
        <tr valign="top">
            <td width="10%">
                <label>Auditoria(*)</label> <br>
                <input type="radio" value="I" name="dbt_tipo_auditoria">Interna<br>
                <input type="radio" value="E" name="dbt_tipo_auditoria">Externa
            </td>
            <td width="20%">
                <label>Motivo(*)</label><br>
                <select name="dbt_motivo">
                    <option value=""></option>
                    <?php if($motAud && $motAud->NumRows()>0){ ?>
                        <?php while(!$motAud->EOF){ ?>
                            <option value="<?php echo $motAud->fields['id_motivo'];?>">
                                <?php echo $motAud->fields['descripcion']; ?>
                            </option>
                            <?php $motAud->MoveNext(); ?>
                        <?php } ?>
                    <?php } ?>
                </select>
            </td>
            <td width="35%">
                <label>Observaciones</label><br>
                <textarea name="dbt_observaciones" cols="50"></textarea>
            </td>
            <td width="35%">
                <label>Identificacion</label><br>
                <input type="text" name="dbt_identificacion" size="50">
            </td>
            <td align="center" valign="middle">
                <a class="sprite-gral icon-floppy" title="Guardar Debito"
                   href="javascript:void(0);" onclick="saveDebitoRetroactivo(this);"></a>
            </td>
        </tr>
    </table>
</form>