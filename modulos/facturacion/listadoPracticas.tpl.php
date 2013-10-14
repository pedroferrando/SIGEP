<div width="95%" style='margin: 20px;float: left;'>

    <font style='float: left;font-size: medium'><b>Expediente: </b></font>
    <input style="float: left;" type="text" value="<?php echo $nro_exp ?>" name="nro_exp" id="nro_exp"/>
    <input src="../../imagenes/lupa.gif" name="btn_nro_exp" id="btn_nro_exp" type="image" style="float: left;margin-left: 2px;width: 15px;height: 15px"/>


    <font style='float: left;font-size: medium;margin-left: 10px'><b>Efector: </b></font>
    <div style="float: left;" id="efector_div">
        <select id="efector" name=efector Style="float: left;width:400px">
            <option value=-1>Seleccione</option>
            <?php
            if (!is_null($res_efectores)) {
                while (!$res_efectores->EOF) {
                    $cuie = $res_efectores->fields['cuie'];
                    $nombre_efector = $res_efectores->fields['nombre'];
                    ?>
                    <option value="<?php echo $cuie; ?>"
                    <?php
                    if ($cuie == $cuie_elegido) {
                        echo "selected";
                    }
                    ?> ><?php echo $cuie . " - " . $nombre_efector ?></option>
                            <?php
                            $res_efectores->movenext();
                        }
                    }
                    ?>
        </select>
    </div>

    <font style='float: left;font-size: medium;margin-left: 10px'><b>ver debitados</b></font>
    <input style='float: left' type="checkbox" id="debitados_chk" name="debitados_chk" value="SI"/>

    <input src="../../imagenes/lupa.gif" name="filtro_btn" id="filtro_btn" type="image" style="float: right;margin-left: 2px;width: 15px;height: 15px"/>
    <input style='float: right' type="text" id="filtro_txt" name="filtro_txt" placeholder='filtro por clavebeneficiario'/>

    <div id="img_load" align="center" style="float: left;display: none;width: 100%">Cargando<br><img src="../../imagenes/wait.gif"></div>

    <table id="practicas" class="tablagenerica" width="100%" cellspacing="2" cellpadding="2" align="center" style="float: left;margin-top: 5px">
        <tr>
            <th id="mo" style="width: 30px">Nro. Prestacion</th>
            <th id="mo" style="width: 130px">Clave Beneficiario</th>
            <th id="mo" style="width: 200px">Apellido y Nombre</th>
            <th id="mo" style="width: 80px">Codigo Prestacion</th>
            <th id="mo" style="width: 80px">Nomenclador</th>
            <th id="mo" style="width: 80px">Importe</th>
            <th id="mo" style="width: 130px">Fecha</th>
        </tr>
        <?php
        if ($listado_registros) {
            foreach ($listado_registros as $prestacion) {

                $comprobante = $prestacion->getComprobante();
                $beneficiario = $comprobante->getBeneficiarioSMI();

                $nomenclador = $prestacion->getNomenclador();

                if ($con == 'con') {
                    $con = '';
                } else {
                    $con = 'con';
                }
                ?>
                <tr <?php
                if ($ver_debitados == 'true') {
                    $id_debito = $prestacion->getDebito()->getIdDebito();
                    echo "onclick='quitarDebito($id_debito);'";
                } else {
                    echo "onclick='toggleDebito(this.id);'";
                }
                ?> class="<?php echo $con ?>" id="<?php echo $prestacion->getIdPrestacion() ?>">                    
                    <td><?php echo $prestacion->getIdPrestacion() ?></td>
                    <td><?php echo $beneficiario->getClavebeneficiario() ?></td>
                    <td><?php echo $beneficiario->getAfiapellido() . " " . $beneficiario->getAfinombre() ?></td>
                    <td><?php echo $nomenclador->getCodigo() . " " . $nomenclador->getDiagnostico() ?></td>
                    <td><?php echo $comprobante->getTipoNomenclador() ?></td>
                    <td><?php echo number_format($prestacion->getPrecioPrestacion(), 2, '.', ',') ?></td>
                    <td><?php echo $comprobante->getFechaComprobante() ?></td>
                </tr>
                <tr>
                    <td class='debito' id="debito_<?php echo $prestacion->getIdPrestacion() ?>" colspan="7" style="height: 100px;display: none">
                        <div id="debitoform_<?php echo $prestacion->getIdPrestacion() ?>" style="padding-top:10px;padding-bottom:20px;overflow: hidden;display: none">Solo un Ejemplo</div>
                    </td>
                </tr>
                <?php
            }
        }
        ?>
    </table>
    <?php
    echo $paginador->getHTML();
    ?>
</div>
<?php
echo fin_pagina(); // aca termino ?>