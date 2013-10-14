<div id="principal" name='principal' align="center">
    <table border=0 width=90% cellspacing=2 cellpadding=2 style="margin-top: 10px">
        <tr>
            <td colspan=12 align=left id=ma>
                <table width=100%>
                    <tr id=ma >
                        <td colspan="2" align="center" width=30% style="padding: 10px 0;height: 25px; font-size:larger; border-top:thin solid #000000;">
                            <b>Expediente Nro. : </b><?= $expediente ?> 
                        </td>
                    </tr>
                    <?
                    $i = 0;
                    $y = 0;
                    while (!$efectores->EOF) {
                        $cuie[$i] = $efectores->fields['cuie'];
                        $facturasdelefector = buscarFacturasPersistidas($expedienteid, $cuie[$i]);
                        $nombreefector = buscarNombreEfector($cuie[$i]);
                        ?>
                        <tr>
                            <td>
                                <table width=100%>
                                    <tr >
                                        <td align="center" width="5%" style="height: 25px;border-top:thin solid #000000;">
                                            <img id="imagen_ver_factura<?= $i ?>" src="<?= $img_ext ?>" border=0 title="Mostrar Facturas" align="left" style="cursor:hand;" onclick="muestra_tabla_facturas(<?= $i ?>);" >
                                        </td>
                                        <td align="center" width=95% align=left style="height: 25px;border-top:thin solid #000000;font-size:medium;padding-top: 20px;"><b>Efector: </b><?= $nombreefector ?>  <b>CUIE:</b> <?= $cuie[$i] ?></td>
                                    </tr>
                                    <? /*
                                     *      Las Facturas
                                     */ ?>
                                    <tr >
                                        <td></td>
                                        <td>
                                            <table width=100% class="efector_<?= $i ?>" style="display:none;padding-bottom: 50px;">
                                                <tr>
                                                    <td></td>
                                                    <td style="background-color:rgb(108,148,199);text-align: center;"><b>Nro. Factura</b></td>
                                                    <td style="background-color:rgb(108,148,199);text-align: center;"><b>Cantidad</b></td>                                        
                                                    <td style="background-color:rgb(108,148,199);text-align: center;"><b>Monto Prefactura</b></td>
                                                    <td style="background-color:rgb(108,148,199);text-align: center;"><b>Rechazadas</b></td>
                                                    <td style="background-color:rgb(108,148,199);text-align: center;"><b>Monto Rechazado</b></td>
                                                    <td style="background-color:rgb(108,148,199);text-align: center;"><b>Aceptadas</b></td>
                                                    <td style="background-color:rgb(108,148,199);text-align: center;"><b>Total Liq.</b></td>
                                                </tr>

                                                <?
                                                $colorstyle = '#CCAACC';
                                                //$datosdelafactura = buscarDatosDeFactura($facturasdelefector->fields['id_factura']);
//                                                $periodoestimulo = PeriodoObjetivo::calcularPeriodo($datosdelafactura->fields['fecha_entrada']);
//                                                $periodoestimulo = split("/", $periodoestimulo->getPeriodo());

                                                while (!$facturasdelefector->EOF) {
                                                    $datosdelafactura = buscarDatosDeFactura($facturasdelefector->fields['id_factura']);
                                                    $nrofactura = $datosdelafactura->fields['nro_fact_offline'] . ' (' . $datosdelafactura->fields['tipo_liquidacion'] . ')';


                                                    $tipoefector = $datosdelafactura->fields['tipoefector'];
                                                    $estadofactura = $datosdelafactura->fields['estado'];

                                                    $montofacturaaux = $facturasdelefector->fields['total_liquidado'];
                                                    $totaldebitadoaux = $facturasdelefector->fields['total_rechazado'];

                                                    $liq = $montofacturaaux - $totaldebitadoaux;
                                                    if ($colorstyle == '#CCCC99')
                                                        $colorstyle = '#CCCCBB';
                                                    else
                                                        $colorstyle = '#CCCC99';
                                                    ?>
                                                    <tr style="background-color:<?= $colorstyle ?>;text-align: center">
                                                        <td align="center" width="1%" style="height: 25px;border-top:thin solid #000000;">
                                                            <img id="imagen_ver_practicas<?= $y ?>" src="<?= $img_ext ?>" border=0 title="Mostrar Practica" align="left" style="cursor:hand;" onclick="muestra_tabla_practicas(<?= $y ?>);" >
                                                        </td>
                                                        <td <?
                                            $id_factura = $facturasdelefector->fields['id_factura'];
                                            $practicasenfactura = buscarPracticasEnFacturaPersistida($facturasdelefector->fields['id_factura_persistida']);

                                            if ($facturasdelefector->fields['cant_de_practicas'] > 0)
                                                $prac_totalaux = $facturasdelefector->fields['cant_de_practicas'];
                                            else
                                                $prac_totalaux = 0;

                                            if ($facturasdelefector->fields['cant_de_practicas_rechazadas'])
                                                $deb_totalaux = $facturasdelefector->fields['cant_de_practicas_rechazadas'];
                                            else
                                                $deb_totalaux = 0;
                                                    ?>>
                                                            <?= $nrofactura ?>
                                                        </td>
                                                        <td>
                                                            <?= $prac_totalaux ?>
                                                        </td>
                                                        <td>
                                                            <?= "$ " . number_format($montofacturaaux, 2, '.', ',') ?>
                                                        </td>
                                                        <td>
                                                            <? echo $deb_totalaux ?>
                                                        </td>
                                                        <td>
                                                            <?= "$ " . number_format($totaldebitadoaux, 2, '.', ',') ?>
                                                        </td>
                                                        <td>
                                                            <?= $prac_totalaux - $deb_totalaux ?>
                                                        </td>
                                                        <td>
                                                            <?= "$ " . number_format($liq, 2, '.', ',') ?>
                                                        </td>
                                                    </tr>
                                                    <? /*
                                                     *      Las Practicas
                                                     */
                                                    ?>
                                                    <tr >
                                                        <td colspan=8 align=center>
                                                            <table width=100% class="factura_<?= $y ?>" style="margin:10px auto 10px;display:none;border: #000000 solid thin; text-align: center;">
                                                                <tr>
                                                                    <td style="background-color:#CCCC99;text-align: center;"><b>Codigo</b></td>                                                                                                
                                                                    <td style="background-color:#CCCC99;text-align: center;"><b>Cantidad</b></td>
                                                                    <?php if (($datosdelafactura->fields['tipo_nomenclador'] != "PERINATAL_CATASTROFICO") && ($datosdelafactura->fields['tipo_nomenclador'] != "PERINATAL_NO_CATASTROFICO")) { ?>
                                                                        <td style="background-color:#CCCC99;text-align: center;"><b>Precio U</b></td>
                                                                    <?php } ?>
                                                                    <td style="background-color:#CCCC99;text-align: center;"><b>Total Liquidado</b></td>
                                                                    <td style="background-color:#CCCC99;text-align: center;"><b>Rechazadas</b></td>
                                                                    <td style="background-color:#CCCC99;text-align: center;"><b>Monto Rechazado</b></td>
                                                                    <td style="background-color:#CCCC99;text-align: center;"><b>Aceptadas</b></td>
                                                                    <td style="background-color:#CCCC99;text-align: center;"><b>Monto Aceptado</b></td>
                                                                </tr>
                                                                <?
                                                                $practicasenfactura->movefirst();
                                                                //calcula el total debitado para ese nomenclador
                                                                while (!$practicasenfactura->EOF) {
                                                                    if ($otrocolorstyle == '#AACC99')
                                                                        $otrocolorstyle = '#AACCBB';
                                                                    else
                                                                        $otrocolorstyle = '#AACC99';
                                                                    ?>
                                                                    <tr style="background-color: <?= $otrocolorstyle ?>">
                                                                        <td><? echo $practicasenfactura->fields['codigo']; ?></td>
                                                                        <td><?= $practicasenfactura->fields['cantidad_total'] ?></td>
                                                                        <?php if (($datosdelafactura->fields['tipo_nomenclador'] != "PERINATAL_CATASTROFICO") && ($datosdelafactura->fields['tipo_nomenclador'] != "PERINATAL_NO_CATASTROFICO")) { ?>
                                                                            <td><?= "$ " . number_format($practicasenfactura->fields['precio'], 2, ",", ".") ?></td>  
                                                                        <?php } ?>    
                                                                        <? $sumatotal = $practicasenfactura->fields['precio'] * $practicasenfactura->fields['cantidad_total'] ?>
                                                                        <td><?= "$ " . number_format($sumatotal, 2, ",", ".") ?></td>
                                                                        <td>
                                                                            <?
                                                                            if ($practicasenfactura->fields['cantidad_rechazos'] > 0) {
                                                                                echo $practicasenfactura->fields['cantidad_rechazos'];
                                                                            } else {
                                                                                echo 0;
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                        <? $sumatotaldeb = $practicasenfactura->fields['precio'] * $practicasenfactura->fields['cantidad_rechazos'] ?>
                                                                        <td><?= "$ " . number_format($sumatotaldeb, 2, ",", ".") ?></td>
                                                                        <td><?= $practicasenfactura->fields['cantidad_total'] - $practicasenfactura->fields['cantidad_rechazos'] ?></td>
                                                                        <td><?= "$ " . number_format($sumatotal - $sumatotaldeb, 2, ",", ".") ?></td>
                                                                    </tr>
                                                                    <?
                                                                    $sumaparaelresumendefactura['monto_prefactura']+=$sumatotal;
                                                                    $sumaparaelresumendefactura['cant_practicas']+=$practicasenfactura->fields['cantidad_total'];
                                                                    $sumaparaelresumendefactura['cant_practicas_deb']+=$practicasenfactura->fields['cantidad_rechazos'];
                                                                    $sumaparaelresumendefactura['monto_deb']+=$sumatotaldeb;

                                                                    $practicasenfactura->MoveNext();
                                                                }
                                                                if ($otrocolorstyle == '#AACC99')
                                                                    $otrocolorstyle = '#AACCBB';
                                                                else
                                                                    $otrocolorstyle = '#AACC99';
                                                                ?>
                                                                <tr style="background-color: <?= $otrocolorstyle ?>;border:#000000 solid thin">
                                                                    <td colspan="2" style="border:#000000 solid thin">
                                                                        Cantidad Total<br/>
                                                                        <?= $sumaparaelresumendefactura['cant_practicas'] ?>
                                                                    </td>
                                                                    <td  colspan="2" style="border:#000000 solid thin">
                                                                        Monto Total Efectuado<br/> $ <?= number_format($sumaparaelresumendefactura['monto_prefactura'], 2, ",", ".") ?>
                                                                    </td>
                                                                    <td  colspan="2" style="border:#000000 solid thin">
                                                                        Cantidad Rechazado<br/> <?= $sumaparaelresumendefactura['cant_practicas_deb'] ?>
                                                                    </td>
                                                                    <td  colspan="2" style="border:#000000 solid thin">
                                                                        Monto Total Rechazado<br/> $ <?= number_format($sumaparaelresumendefactura['monto_deb'], 2, ",", ".") ?>
                                                                    </td>
                                                                </tr>
                                                                <?
                                                                unset($sumaparaelresumendefactura);

                                                                $total_liq[$i]+=$liq;
                                                                $montofactura[$i] += $montofacturaaux;
                                                                $deb_total[$i] += $deb_totalaux;
                                                                $totaldebitado[$i] += $totaldebitadoaux;
                                                                $prac_total[$i] += $prac_totalaux;
                                                                $y++;
                                                                $facturasdelefector->MoveNext();
                                                                ?>                                                    
                                                            </table>
                                                        </td>
                                                    </tr>   
                                                    <?
                                                }
                                                
                                                $objetivosResultado = buscarObjetivosPersistidos($cuie[$i], $expedienteid);
                                                /*
                                                 *      Objetivos
                                                 */
                                                ?> 
                                                <tr ><td colspan=7 align=center>
                                                        <table class="efector_<?= $i ?>" border=0 width=60% cellspacing=2 cellpadding=2  style="margin:10px auto 0;display:none;border: #000000 solid thin; text-align: center;">
                                                            <tr style="background-color:#BDBDBD;">
                                                                <td style="width: 245px; text-align: left"><b>Objetivos</b></td><td style="width: 100px"><b>Meta (%)</b></td><td><b>Asignado</b></td><td><b>Informado</b></td><td style="width: 158px"><b>Cumplimiento (%)</b></td><td><b>Cumplido</b></td><td><b>Puntos</b></td>
                                                            </tr>
                                                            <? if ($objetivosResultado[12]['encontro']) { ?>
                                                                <tr >
                                                                    <td style="background-color:#DDDDDD;text-align: left;">Cobertura Efectiva Basica</td><td><?= $objetivosResultado[12]['meta'] ?></td><td><?= $objetivosResultado[12]['numerador'] ?></td><td><?= $objetivosResultado[12]['denominador'] ?></td><td><?= number_format($objetivosResultado[12]['total_perc'], 2) ?></td><td><?= $objetivosResultado[12]['cumplido'] ?></td><td><?= $objetivosResultado[12]['puntos'] ?></td>
                                                                </tr>
                                                            <? }if ($objetivosResultado[0]['encontro']) { ?>
                                                                <tr >
                                                                    <td style="background-color:#DDDDDD;text-align: left;">Captacion Embarazada</td><td><?= $objetivosResultado[0]['meta'] ?></td><td><?= $objetivosResultado[0]['numerador'] ?></td><td><?= $objetivosResultado[0]['denominador'] ?></td><td><?= number_format($objetivosResultado[0]['total_perc'], 2) ?></td><td><?= $objetivosResultado[0]['cumplido'] ?></td><td><?= $objetivosResultado[0]['puntos'] ?></td>
                                                                </tr>
                                                            <? }if ($objetivosResultado[1]['encontro']) { ?>
                                                                <tr>
                                                                    <td style="background-color:#DDDDDD;text-align: left">Apgar'>5</td><td><?= $objetivosResultado[1]['meta'] ?></td><td><?= $objetivosResultado[1]['numerador'] ?></td><td><?= $objetivosResultado[1]['denominador'] ?></td><td><?= number_format($objetivosResultado[1]['total_perc'], 2) ?></td><td><?= $objetivosResultado[1]['cumplido'] ?></td><td><?= $objetivosResultado[1]['puntos'] ?></td>
                                                                </tr>
                                                            <? }if ($objetivosResultado[2]['encontro']) { ?>
                                                                <tr>
                                                                    <td style="background-color:#DDDDDD;text-align: left">Peso Al Nacer > 2500gr.</td><td><?= $objetivosResultado[2]['meta'] ?></td><td><?= $objetivosResultado[2]['numerador'] ?></td><td><?= $objetivosResultado[2]['denominador'] ?></td><td><?= number_format($objetivosResultado[2]['total_perc'], 2) ?></td><td><?= $objetivosResultado[2]['cumplido'] ?></td><td><?= $objetivosResultado[2]['puntos'] ?></td>
                                                                </tr>
                                                            <? }if ($objetivosResultado[3]['encontro']) { ?>
                                                                <tr>
                                                                    <td style="background-color:#DDDDDD;text-align: left">VDRL y ATT en Embarazo</td><td><?= $objetivosResultado[3]['meta'] ?></td><td><?= $objetivosResultado[3]['numerador'] ?></td><td><?= $objetivosResultado[3]['denominador'] ?></td><td><?= number_format($objetivosResultado[3]['total_perc'], 2) ?></td><td><?= $objetivosResultado[3]['cumplido'] ?></td><td><?= $objetivosResultado[3]['puntos'] ?></td>
                                                                </tr>
                                                            <? }if ($objetivosResultado[4]['encontro']) { ?>
                                                                <tr>
                                                                    <td style="background-color:#DDDDDD;text-align: left">VDRL y ATT Previa al Parto</td><td><?= $objetivosResultado[4]['meta'] ?></td><td><?= $objetivosResultado[4]['numerador'] ?></td><td><?= $objetivosResultado[4]['denominador'] ?></td><td><?= number_format($objetivosResultado[4]['total_perc'], 2) ?></td><td><?= $objetivosResultado[4]['cumplido'] ?></td><td><?= $objetivosResultado[4]['puntos'] ?></td>
                                                                </tr>
                                                            <? }if ($objetivosResultado[5]['encontro']) { ?>
                                                                <tr>
                                                                    <td style="background-color:#DDDDDD;text-align: left">Atencion de Muertes Materno/Infantiles</td><td><?= $objetivosResultado[5]['meta'] ?></td><td><?= $objetivosResultado[5]['numerador'] ?></td><td><?= $objetivosResultado[5]['denominador'] ?></td><td><?= number_format($objetivosResultado[5]['total_perc'], 2) ?></td><td><?= $objetivosResultado[5]['cumplido'] ?></td><td><?= $objetivosResultado[5]['puntos'] ?></td>
                                                                </tr>
                                                            <? }if ($objetivosResultado[6]['encontro']) { ?>
                                                                <tr>
                                                                    <td style="background-color:#DDDDDD;text-align: left">Cob. Inmunizaciones</td><td><?= $objetivosResultado[6]['meta'] ?></td><td><?= $objetivosResultado[6]['numerador'] ?></td><td><?= $objetivosResultado[6]['denominador'] ?></td><td><?= number_format($objetivosResultado[6]['total_perc'], 2) ?></td><td><?= $objetivosResultado[6]['cumplido'] ?></td><td><?= $objetivosResultado[6]['puntos'] ?></td>
                                                                </tr>
                                                            <? }if ($objetivosResultado[7]['encontro']) { ?>
                                                                <tr>
                                                                    <td style="background-color:#DDDDDD;text-align: left">Consejeria</td><td><?= $objetivosResultado[7]['meta'] ?></td><td><?= $objetivosResultado[7]['numerador'] ?></td><td><?= $objetivosResultado[7]['denominador'] ?></td><td><?= number_format($objetivosResultado[7]['total_perc'], 2) ?></td><td><?= $objetivosResultado[7]['cumplido'] ?></td><td><?= $objetivosResultado[7]['puntos'] ?></td>
                                                                </tr>
                                                            <? }if ($objetivosResultado[8]['encontro']) { ?>
                                                                <tr>
                                                                    <td style="background-color:#DDDDDD;text-align: left">Seguimiento de Niño < 1 año</td><td><?= $objetivosResultado[8]['meta'] ?></td><td><?= $objetivosResultado[8]['numerador'] ?></td><td><?= $objetivosResultado[8]['denominador'] ?></td><td><?= number_format($objetivosResultado[8]['total_perc'], 2) ?></td><td><?= $objetivosResultado[8]['cumplido'] ?></td><td><?= $objetivosResultado[8]['puntos'] ?></td>
                                                                </tr>
                                                            <? }if ($objetivosResultado[9]['encontro']) { ?>
                                                                <tr>
                                                                    <td style="background-color:#DDDDDD;text-align: left">Seguimiento de Niño >= 1 año</td><td><?= $objetivosResultado[9]['meta'] ?></td><td><?= $objetivosResultado[9]['numerador'] ?></td><td><?= $objetivosResultado[9]['denominador'] ?></td><td><?= number_format($objetivosResultado[9]['total_perc'], 2) ?></td><td><?= $objetivosResultado[9]['cumplido'] ?></td><td><?= $objetivosResultado[9]['puntos'] ?></td>
                                                                </tr>
                                                            <? }if ($objetivosResultado[10]['encontro']) { ?>
                                                                <tr >
                                                                    <td style="background-color:#DDDDDD;text-align: left;">Fijo</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td><?= $objetivosResultado[10]['puntos'] ?></td>
                                                                </tr>
                                                            <? } if ($objetivosResultado[11]['encontro']) { ?>
                                                                <tr >
                                                                    <td style="background-color:#DDDDDD;text-align: left;">Base</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td><?= $objetivosResultado[11]['puntos'] ?></td>
                                                                </tr>
                                                            <? } ?>
                                                            <tr align=right>
                                                                <td colspan=6 style="border-top: #000000 solid thin"><b>Total Puntos</b></td>
                                                                <td style="border-top: #000000 solid thin"><?= $puntosentotal[$i] = $objetivosResultado[12]['puntos'] + $objetivosResultado[11]['puntos'] + $objetivosResultado[0]['puntos'] + $objetivosResultado[1]['puntos'] + $objetivosResultado[2]['puntos'] + $objetivosResultado[3]['puntos'] + $objetivosResultado[4]['puntos'] + $objetivosResultado[5]['puntos'] + $objetivosResultado[6]['puntos'] + $objetivosResultado[7]['puntos'] + $objetivosResultado[8]['puntos'] + $objetivosResultado[9]['puntos'] + $objetivosResultado[10]['puntos'] ?></td>
                                                            </tr>

                                                        </table>
                                                    </td>
                                                </tr>

                                                <?
                                                /*                                                 * ******************
                                                 *      Debito x Auditoria
                                                 * ************************* */
                                                $sumadedebitos = 0;
                                                $debitosencontrados = debitoAuditado($expediente, $cuie[$i]);
                                                if (!$debitosencontrados->EOF) {
                                                    ?> 
                                                    <tr >
                                                        <td colspan=7 align=center>
                                                            <div style="width: 75%;margin:0 auto;padding-left: 40px;">
                                                                <form id="form_debito_<?= $i ?>" name=form_debito action="listado_expedientes.php" method=POST>
                                                                    <table id="auditoria_<?= $i ?>" style="width: 94%;margin:20px auto 0;border: #000000 solid thin; text-align: center;float: left">
                                                                        <tr>
                                                                            <td colspan=5 style="background-color:#CCCC99;text-align: center;padding-top: 10px;padding-bottom: 5px;"><b>DEBITOS POR AUDITORIA</b></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <!--td style="background-color:#CCCC99;text-align: left;padding-bottom: 5px;padding-top: 5px;width: 85px"><b>Total Liquidado</b></td-->
                                                                            <td style="background-color:#CCCC99;text-align: center;padding-bottom: 5px;padding-top: 5px;width: 25%"><b>Debito Auditado</b></td>
                                                                            <td style="background-color:#CCCC99;text-align: center;padding-bottom: 5px;padding-top: 5px;width: 25%"><b>Nro. Exp. Relacionado</b></td>
                                                                            <td style="background-color:#CCCC99;text-align: center;padding-bottom: 5px;padding-top: 5px;width: 50%"><b>Descripcion</b></td>
                                                                        </tr>

                                                                        <?
                                                                        $sumadedebitos = 0;
                                                                        $debitosencontrados = debitoAuditado($expediente, $cuie[$i]);
                                                                        if ($debitosencontrados->rowcount() > 0) {
                                                                            while (!$debitosencontrados->EOF) {
                                                                                ?>
                                                                                <tr >
                                                                                    <td style="font-size: smaller;border-bottom: #000000 solid thin;"><?= "$ " . number_format($debitosencontrados->fields['monto'], 2, ',', '.') ?></td>
                                                                                    <td style="font-size: smaller;border-bottom: #000000 solid thin;"><?= $debitosencontrados->fields['nro_exp_relac'] ?></td>
                                                                                    <td style="font-size: smaller;border-bottom: #000000 solid thin;"><?= $debitosencontrados->fields['descripcion'] ?> </td>
                                                                                </tr>
                                                                                <?
                                                                                $sumadedebitos += $debitosencontrados->fields['monto'];
                                                                                $debitosencontrados->movenext();
                                                                            }
                                                                        }
                                                                        ?>

                                                                        <tr>                                                                
                                                                            <td colspan="3" style="border-top:thin solid #000000;padding-top: 10px">
                                                                                <table style="width: 100%">
                                                                                    <tr style="background-color:#CCCCBB">
                                                                                        <? $sumatotaldedebitos+=$sumadedebitos; ?>
                                                                                        <td style="font-size: small"><b>Total Liquidado  </b><?= "$ " . number_format($total_liq[$i], 2, ',', '.') ?></td>
                                                                                        <td style="font-size: small"><b>Total Debitado  </b><?= "$ " . number_format($sumadedebitos, 2, ',', '.') ?></td>
                                                                                        <? //$total_liq[$i] = $total_liq[$i] - $sumadedebitos        ?>
                                                                                        <td style="font-size: small"><b>Saldo Total  </b><? echo "$ " . number_format($total_liq[$i] - $sumadedebitos, 2, ',', '.') ?></td>
                                                                                    </tr>
                                                                                </table>

                                                                        </tr>
                                                                    </table> 

                                                                </form>
                                                            </div>                                                
                                                        </td>
                                                    </tr> 
                                                <? } ?>

                                                <!-- OBSERVACIONES -->
                                                <?
                                                $obsencontrados = obsRegistradas($expediente, $cuie[$i]);
                                                if (!$obsencontrados->EOF) {
                                                    ?>
                                                    <tr >
                                                        <td colspan=7 align=center>
                                                            <div style="width: 75%;margin:0 auto;padding-left: 40px;">
                                                                <form id="form_obs_<?= $i ?>" name=form_obs action="listado_expedientes.php" method=POST>
                                                                    <table id="obs_<?= $i ?>" style="width: 94%;margin:20px auto 0;border: #000000 solid thin; text-align: center;float: left">
                                                                        <tr>
                                                                            <td colspan=5 style="background-color:#CCCC99;text-align: center;padding-top: 10px;padding-bottom: 5px;"><b>OBSERVACIONES DE LA FACTURACION</b></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <!--td style="background-color:#CCCC99;text-align: left;padding-bottom: 5px;padding-top: 5px;width: 85px"><b>Total Liquidado</b></td-->
                                                                            <td colspan=5 style="background-color:#CCCC99;text-align: center; padding-bottom: 5px;padding-top: 5px;"><b>Observaciones</b></td>
                                                                        </tr>

                                                                        <?
                                                                        if ($obsencontrados->rowcount() > 0) {
                                                                            while (!$obsencontrados->EOF) {
                                                                                ?>
                                                                                <tr >
                                                                                    <td style="font-size: smaller;border-bottom: #000000 solid thin;"><?= $obsencontrados->fields['observacion'] ?>

                                                                                    </td>
                                                                                </tr>
                                                                                <?
                                                                                $obsencontrados->movenext();
                                                                            }
                                                                        }
                                                                        ?>

                                                                        <tr>  
                                                                            <? if ($descobs[$i] == null) { ?>                                                                                 
                                                                                <td >-</td>
                                                                            <? } else { ?>
                                                                                <td style="font-size: smaller">
                                                                                    <?
                                                                                    if ($descobs[$i] == null) {
                                                                                        $descobs[$i] = '-';
                                                                                    }
                                                                                    echo $descobs[$i];
                                                                                    ?>
                                                                                </td>     

                                                                            <? } ?>      
                                                                        </tr>
                                                                    </table> 
                                                                </form>
                                                            </div>                                                
                                                        </td>
                                                    </tr> 
                                                <? } ?>
                                                    <tr><td>&nbsp;</td></tr>
                                                    <!-- debitos retroactivos -->
                                                    <tr>
                                                        <td colspan="5">
                                                            <?php 
                                                                $debitosRetro = DebitoRetroactivoColeccion::getResumenDebitoRetroactivo($efectores->fields['cuie'],$elexpediente); 
                                                                $sumaDebitosRetro += $debitosRetro->fields['monto'];
                                                                if($debitosRetro->fields['total']!=""){
                                                                    $arr_cuie[] = $efectores->fields['cuie'];
                                                                }
                                                            ?>
                                                            <table align="right" width="50%" style="border: 1px solid black;">
                                                                <tr bgcolor="#CCCC99">
                                                                    <th align="center" colspan="2">
                                                                        <font size="2">D&Eacute;BITOS RETROACTIVOS</font>
                                                                    </th>
                                                                </tr>
                                                                <tr bgcolor="#CCCC99">
                                                                    <td align="center" width="50%"><b>Cantidad</b></td>
                                                                    <td align="center" width="50%"><b>Monto</b></td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="center">
                                                                        <label id="lbl_cant_dbt_ret_<?php echo $efectores->fields['cuie'];?>">
                                                                        <?php echo $debitosRetro->fields['total']!="" ? $debitosRetro->fields['total'] : 0 ;?>
                                                                        </label>
                                                                    </td>
                                                                    <td align="center">
                                                                        $ <label id="lbl_monto_dbt_ret_<?php echo $efectores->fields['cuie'];?>">
                                                                        <?php echo $debitosRetro->fields['monto']!="" ? number_format($debitosRetro->fields['monto'],2) : 0 ;?>
                                                                        </label>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr><td>&nbsp;</td></tr>
                                                <? /*                                                 * ***************
                                                 *      Total a pagar
                                                 * ****************** */ ?>

                                                <tr >
                                                    <td colspan=7 align=center>
                                                        <table id="totalapagar_<?= $i ?>" border=0 width=60% cellspacing=2 cellpadding=2  style="margin:20px auto 0;display:none;border: #000000 solid thin; text-align: center;">
                                                            <tr style="background-color:thistle;">
                                                                <td ><b>Total a Pagar</b></td>
                                                                <td><b>Puntos</b></td>
                                                                <td><b>Fondos para el Efector</b></td>
                                                                <td><b>Fondos para Estimulos</b></td>
                                                            </tr>
                                                            <tr>
                                                                <?
                                                                $total_liq[$i] = $total_liq[$i] - $sumadedebitos - $debitosRetro->fields['monto'];
                                                                $aux1 = $total_liq[$i];
                                                                $aux2 = $puntosentotal[$i];

                                                                $estimulacion[$i] = ($aux1 * $aux2) / 100;
                                                                $estimulacion_formateada = floatval(number_format($estimulacion[$i], 3, '.', ''));
                                                                $total_formateada = floatval(number_format($aux1, 2, '.', ''));
                                                                $paraefector[$i] = $total_formateada - $estimulacion_formateada;
                                                                ?>
                                                                <td><?= "$ " . number_format($total_liq[$i], 2) ?></td>
                                                                <td><?= $aux2 ?></td>
                                                                <td><?= "$ " . number_format($paraefector[$i], 2) ?></td>
                                                                <td><?= "$ " . number_format($estimulacion[$i], 2) ?></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                <?
                                $efectores->MoveNext();
                                $i++;
                            }
                            ?>
                        </td>
                    </tr>
                </table>
    </table>

    <br></br>
    <? /*
     *      Total del expediente
     */ ?>
    <div style="margin: 0 auto;font-size: medium;width: 15%;padding-bottom: 5px" ><b>Total del Expediente:</b></div>
    <div style="width: 70%;margin:0 auto;">
        <table bgcolor="#FFFFFF" id="totaldelexpediente" border=0 cellspacing=2 cellpadding=2 align=center style="border: #000000 solid thin;margin:0 auto">
            <tr style="margin: 10px 20px;background-color:lightcoral">
                <td ><b>Cant. Practicas</b></td>                                        
                <td ><b>Monto Prefactura</b></td>
                <td ><b>Cant. Rechazadas</b></td>
                <td ><b>Monto Rechazado</b></td>
                <td><b>Cant. Aceptadas</b></td>
                <td><b>Debitos</b></td>
                <td ><b>Total Liquidado</b></td>
                <td ><b>P/Efector</b></td>
                <td ><b>P/Estimulo</b></td>
            </tr>
            <tr>
                <td align="center">
                    <?
                    $prac_total_total = 0;
                    foreach ($prac_total as $prac) {
                        $prac_total_total+=$prac;
                    }
                    echo $prac_total_total;
                    ?>
                </td>
                <td align="center">
                    <?
                    $total_montofactura = 0;
                    foreach ($montofactura as $fact) {
                        $total_montofactura+=$fact;
                    }
                    echo "$ " . number_format($total_montofactura, 2, ",", ".");
                    ?>
                </td>
                <td align="center">
                    <?
                    $deb_prac_total = 0;
                    foreach ($deb_total as $deb) {
                        $deb_prac_total+=$deb;
                    }
                    echo $deb_prac_total;
                    ?>
                </td>
                <td align="center">
                    <?
                    $total_totaldebitado = 0;
                    foreach ($totaldebitado as $montodebito) {
                        $total_totaldebitado+=$montodebito;
                    }
                    echo "$ " . number_format($total_totaldebitado, 2, ",", ".");
                    ?>
                </td>
                <td align="center">
                    <?= $prac_total_total - $deb_prac_total ?>
                </td>
                <td align="center">
                    <?php $totalDebitos = $sumatotaldedebitos + $sumaDebitosRetro; ?>
                    <?= "$ " . number_format($totalDebitos, 2, ",", "."); ?>
                </td>
                <td align="center">
                    <?
                    $totaltotal_liq = 0;
                    foreach ($total_liq as $unaliquidacion) {
                        $totaltotal_liq+=$unaliquidacion;
                    }
                    echo "$ " . number_format($totaltotal_liq, 2, ",", ".");
                    ?>
                </td>
                <td align="center" style="width: 80px">
                    <?
                    $totaltotal_efector = 0;
                    foreach ($paraefector as $unefector) {
                        $totaltotal_efector+=$unefector;
                    }
                    echo "$ " . number_format($totaltotal_efector, 2, ",", ".");
                    ?>
                </td>
                <td align="center" style="width: 80px">
                    <?
                    $totaltotal_estimulado = 0;
                    foreach ($estimulacion as $unaestimulacion) {
                        $totaltotal_estimulado+=$unaestimulacion;
                    }
                    echo "$ " . number_format($totaltotal_estimulado, 2, ",", ".");
                    ?>
                </td>            
            </tr>
        </table>
        <form name="formestimul" action="listado_expedientes.php" method=POST style="margin:0 auto">  
            <input name="expedientebuscado" type="hidden" value="<?= $expediente ?>"/>          

            <div style="width: 200px;float: none;margin-top: 5px">                
                <?php
                $link = encode_link("expediente_pdf.php", array("nro_expediente" => $elexpediente));
                echo "<a target='_blank' href='" . $link . "' title='Imprime Expediente'><div class='sprite pdf_logo'></div></a>";
                
                $link = encode_link("creditos_pdf.php", array("nro_expediente" => $elexpediente));
                echo "<a target='_blank' href='" . $link . "' title='Imprime Aceptados'><div class='sprite billete-c'></div></a>";
                
                $link = encode_link("debitos_pdf.php", array("nro_expediente" => $elexpediente));
                echo "<a target='_blank' href='" . $link . "' title='Imprime Rechazos'><div class='sprite billete-d'></div></a>";
                
                if (permisos_check("inicio", "reg_pagos")) {
                    $link = encode_link("pago_orden_de_cargo.php", array("nro_expediente" => $elexpediente, "total_liquidado" => $totaltotal_liq));
                    echo "<a target='_blank' href='" . $link . "' title='Efectuar Pago'><div class='sprite billete'></div></a>";
                }
                if(count($arr_cuie)>0){
                    $link = encode_link("debitos_retro_pdf.php", array("nro_expediente" => $elexpediente, "id_expediente" => $expedienteid, "arr_cuie"=>$arr_cuie));
                    echo "<a target='_blank' href='" . $link . "' title='Imprime Debitos Retroactivos'><div class='sprite pdf_logo'></div></a>";
                }
                ?>
            </div>

        </form>
    </div>
    <br></br>  

    <div align="center">
        <form id="formulario" name=form1 action="listado_expedientes.php" method=POST align=center style="margin:0 auto;width: 500px;">
            <div align=center style="margin:0 auto; font-size:larger; margin-top: 20px; background-color: rgba(108,148,199, 0.6); border: thin solid #000000;padding:10px 20px;">
                <div style="font-size:smaller;margin:0 auto;">(Cod. de Org. - Nº Correlativo - Año) </div>
                <b>Buscar expediente: </b><input id="expedientebuscado" value="<?= $elexpediente ?>"name="expedientebuscado"></input>
                <button type="submit" name="buscar_expediente" value="Buscar Expediente" style="cursor: default;height: 25px;width:100px;text-align: center;">Buscar</button> <br />

                <div align=center style="font-size:smaller; color: #BC131A; margin: 0 auto">Ej. 0012-03-12</div>
            </div>
        </form>
    </div>

    <div style="width:300px;">
        <? echo fin_pagina(); // aca termino ?>
    </div>

</div>

