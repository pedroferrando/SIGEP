<form id="dbt_frm_busqueda">
    <table align="center">
        <tr>
            <td>
               CUIE: <input type="text" name="dbt_cuie" value="<?php echo $cuie; ?>" readonly="readonly" size="10"/> 
            </td>
            <td>
                Nro Expte: <select name="dbt_expte" onchange="getFacturasDebitoRetro();">
                                <option value=""></option>
                                <?php if($exptesEfector): ?>
                                    <?php while(!$exptesEfector->EOF){ ?>
                                        <option value="<?php echo $exptesEfector->fields['nro_exp']; ?>">
                                            <?php echo $exptesEfector->fields['nro_exp']; ?>
                                        </option>
                                        <?php $exptesEfector->MoveNext(); ?>
                                    <?php } ?>
                                <?php endif; ?>
                           </select>
            </td>
            <td id="cnt_factura">
                Nro Factura: <input type="text" name="dbt_factura" value=""/>
            </td>
            <td>
                Clave Benef - Nro Doc: <input type="text" name="dbt_clave_doc" maxlength="16" value=""/>
            </td>
            <td>
                <button type="button" id="dbt_btn_buscar" onclick="getPrestacionesDebitoRetro();">Buscar</button>
            </td>
        </tr>
        
    </table>
</form>