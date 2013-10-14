<?php echo $title; ?>
    <table align="center" width="50%">
        <tr>
            <td id="mo">&nbsp;</td>
            <td id="mo">Total</td>
        </tr>
        <tr <?php echo atrib_tr()?>>
            <td><b>Total de personas activas con CEB</b></td>
            <td align="right" bgcolor="#77B96C"><b><?php echo $result->fields['verde_activo']; ?></b></td>
        </tr>
            <?php filasHijas($result,'verde_activo'); ?>
        <tr <?php echo atrib_tr()?>>
            <td><b>Total de personas no activas con CEB</b></td>
            <td align="right" bgcolor="#77B96C"><b><?php echo $result->fields['verde_inactivo']; ?></b></td>
        </tr>
            <?php filasHijas($result,'verde_inactivo'); ?>
        <tr <?php echo atrib_tr()?>>
            <td><b>Total de personas activas con CEB vencida</b></td>
            <td align="right" bgcolor="#FF0B2F"><b><?php echo $result->fields['rojo_activo']; ?></b></td>
        </tr>
        </tr>
            <?php filasHijas($result,'rojo_activo'); ?>
        <tr <?php echo atrib_tr()?>>
            <td><b>Total de personas no activas con CEB vencida</b></td>
            <td align="right" bgcolor="#FF0B2F"><b><?php echo $result->fields['rojo_inactivo']; ?></b></td>
        </tr>
            <?php filasHijas($result,'rojo_inactivo'); ?>
        <tr <?php echo atrib_tr()?>>
            <td><b>Total de personas activas con CEB proximo a vencerse</b></td>
            <td align="right" bgcolor="#F2F58B"><b><?php echo $result->fields['amarillo_activo']; ?></b></td>
        </tr>
            <?php filasHijas($result,'amarillo_activo'); ?>
        <tr <?php echo atrib_tr()?>>
            <td><b>Total de personas no activas con CEB proximo a vencerse</b></td>
            <td align="right" bgcolor="#F2F58B"><b><?php echo $result->fields['amarillo_inactivo']; ?></b></td>
        </tr>
            <?php filasHijas($result,'amarillo_inactivo'); ?>
    </table>

<?php
    function filasHijas($result,$campo){
        ?>
            <tr <?php echo atrib_tr()?>>
                <td class="child">menores a 6</td>
                <td align="right"><?php echo$result->fields[$campo.'_a']; ?></td>
            </tr>
            <tr <?php echo atrib_tr()?>>
                <td class="child">entre 6 y 9</td>
                <td align="right"><?php echo$result->fields[$campo.'_b']; ?></td>
            </tr>
            <tr <?php echo atrib_tr()?>>
                <td class="child">adolescentes</td>
                <td align="right"><?php echo$result->fields[$campo.'_c']; ?></td>
            </tr>
            <tr <?php echo atrib_tr()?>>
                <td class="child">mujeres de 20 a 69</td>
                <td align="right"><?php echo$result->fields[$campo.'_d']; ?></td>
            </tr>
            <tr <?php echo atrib_tr()?>>
                <td class="child">sin clasificar</td>
                <td align="right"><?php echo$result->fields[$campo.'_vacio']; ?></td>
            </tr>
        <?php
    }
?>