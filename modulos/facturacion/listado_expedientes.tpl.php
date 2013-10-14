<div class="modal" id="position-fixed-top" style="display:none;">
    <a href="javascript:;" style="float:right;" onclick="$(this).parent().hide();"><b>x</b></a>
    <ul class="tabs">
        <li><a href="javascript:;" onclick="switchTabDebitoRetro(this,'dbt_lst');">Debitos Aplicados</a></li>
        <li><a href="javascript:;" onclick="switchTabDebitoRetro(this,'dbt_new');">Nuevo Debito</a></li>
    </ul>
    <br style="clear:both;"/><hr>
    <input type="hidden" name="cuie_actual"/>
    <input type="hidden" name="expte_actual" value="<?php echo trim($expedientebuscado); ?>"/>
    <div id="cnt_frm_busqueda"></div>
    <div id="cnt_res_busqueda"></div>
</div>
<?php if ($mostrar) { ?>
    <div id="principal" name='principal' align="center">
        <table border=0 width=90% cellspacing=2 cellpadding=2 align=center style="margin-top: 10px">
            <tr>
                <td colspan=12 align=left id=ma>
                    <table width=100%>
                        <tr id=ma >
                            <td colspan="2" align="center" width=30% style="padding: 10px 0;height: 25px; font-size:larger; border-top:thin solid #000000;">
                                <b>Expediente Nro. : </b><?php echo $expediente->getNroExp(); ?>                            
                            </td>
                        </tr>
                        <?
                        $i = 0;
                        $y = 0;
                        $efectores = $expediente->getEfectores();
                        foreach ($efectores as $efector) {
                            $colorstyle = '#CCCCBB';
                            $facturasdelefector = $expediente->facturasDelEfector($efector->getCuie());
                            ?>
                            <tr>
                                <td>
                                    <table width=100%>
                                        <tr >
                                            <td align="center" width="5%" style="height: 25px;border-top:thin solid #000000;">
                                                <img id="imagen_ver_factura<?php echo $i ?>" src="<?php echo $img_ext ?>" border=0 title="Mostrar Facturas" align="left" style="cursor:hand;" onclick="muestra_tabla_facturas(<?php echo $i ?>);" >
                                            </td>
                                            <td align="center" width=95% align=left style="height: 25px;border-top:thin solid #000000;font-size:medium;padding-top: 20px;"><b>Efector: </b><?php echo $efector->getNombreefector() ?>  <b>CUIE:</b> <?php echo $efector->getCuie() ?></td>
                                        </tr>
                                        <? /*
                                         *      Las Facturas
                                         */ ?>
                                        <tr >
                                            <td></td>
                                            <td>
                                                <table width=100% class="efector_<?php echo $i ?>" style="display:none;padding-bottom: 50px;">
                                                    <tr bgcolor="#6C94C7">
                                                        <td></td>
                                                        <td align="center"><b>Nro. Factura</b></td>
                                                        <td align="center"><b>Cantidad</b></td>                                        
                                                        <td align="center"><b>Monto Prefactura</b></td>
                                                        <td align="center"><b>Rechazados</b></td>
                                                        <td align="center"><b>Total Rechazado</b></td>
                                                        <td align="center"><b>Cant. Aceptados</b></td>
                                                        <td align="center"><b>Total Liq.</b></td>
                                                    </tr>
                                                    <?
                                                    foreach ($facturasdelefector as $factura) {

                                                        if ($colorstyle == '#CCCC99')
                                                            $colorstyle = '#CCCCBB';
                                                        else
                                                            $colorstyle = '#CCCC99';

                                                        //Carga un array con facturas que estan abiertas
                                                        //por las cueles no se puede estimular el expediente 
                                                        if ($factura->getEstado() == 'A') {
                                                            $colorstyle = "#bf5461";
                                                            $facturasabiertas[$fa]['nrof'] = $factura->getNroFactOffline();
                                                            $facturasabiertas[$fa]['cuie'] = $factura->getCuie();
                                                            $sepuedeestimular = 'DISABLED';
                                                            $hayfacturasabiertas = true;
                                                            $fa++;
                                                        }
                                                        ?>
                                                        <tr bgcolor="<?php echo $colorstyle ?>">
                                                            <td align="center" width="1%" style="height: 25px;border-top:thin solid #000000;">
                                                                <img id_factura="<?php echo $factura->getIdFactura() ?>" id="imagen_ver_practicas<?php echo $y ?>" src="<?php echo $img_ext ?>" border=0 title="Mostrar Practica" align="left" style="cursor:hand;" onclick="muestra_tabla_practicas(<?php echo $y ?>);" >
                                                            </td>
                                                            <td align="center">
                                                                <?php echo $factura->getNroFactOffline() . " (" . $factura->getTipoLiquidacion() . ")" ?>
                                                            </td>
                                                            <td align="center">
                                                                <?php echo $factura->getCantidadPracticas() ?>
                                                            </td>
                                                            <td align="center">
                                                                <?php echo "$ " . number_format($factura->getMontoPrefactura(), 2, '.', ',') ?>
                                                            </td>
                                                            <td align="center">
                                                                <? echo $factura->getCantidadDebitos() ?>
                                                            </td>
                                                            <td align="center">
                                                                <?php echo "$ " . number_format($factura->getMontoDebitos(), 2, '.', ',') ?>
                                                            </td>
                                                            <td align="center">
                                                                <?php echo $factura->getCantidadPracticas() - $factura->getCantidadDebitos() ?>
                                                            </td>
                                                            <td align="center">
                                                                <?php echo "$ " . number_format($factura->getLiquidacion(), 2, '.', ',') ?>
                                                            </td>
                                                        </tr>

                                                        <? /*                                                         * ***************************
                                                         * 
                                                         *    Detalle de las Practicas
                                                         * 
                                                         * ******************************* */ ?>
                                                        <tr >
                                                            <td colspan=8 align=center>
                                                                <table width=100% class="factura_<?php echo $y ?>" style="margin:10px auto 10px;display:none;border: #000000 solid thin; text-align: center;">
                                                                    <tr>
                                                                        <td align="center" bgcolor="#CCCC99"><b>Codigo</b></td>                                                                                                
                                                                        <td align="center" bgcolor="#CCCC99"><b>Cantidad</b></td>
                                                                        <?php
                                                                        if (($factura->getTipoNomenclador() != "PERINATAL_CATASTROFICO") && ($factura->getTipoNomenclador() != "PERINATAL_NO_CATASTROFICO")) {
                                                                            echo '<td align="center" bgcolor="#CCCC99"><b>Precio U</b></td>';
                                                                        }
                                                                        ?>
                                                                        <td align="center" bgcolor="#CCCC99"><b>Total Liquidado</b></td>
                                                                        <td align="center" bgcolor="#CCCC99"><b>Rechazados</b></td>
                                                                        <td align="center" bgcolor="#CCCC99"><b>Monto Rechazado</b></td>
                                                                        <td align="center" bgcolor="#CCCC99"><b>Aceptados</b></td>
                                                                        <td align="center" bgcolor="#CCCC99"><b>Monto Aceptado</b></td>
                                                                    </tr>
                                                                    <?

                                                                    $total_liq[$i]+=$factura->getLiquidacion();
                                                                    $montofactura[$i] += $factura->getMontoPrefactura();
                                                                    $deb_total[$i] += $factura->getCantidadDebitos();
                                                                    $totaldebitado[$i] += $factura->getMontoDebitos();
                                                                    $prac_total[$i] += $factura->getCantidadPracticas();
                                                                    ;
                                                                    $y++;
                                                                    ?>                                                    
                                                                </table>
                                                            </td>
                                                        </tr>   
                                                        <?
                                                    }
                                                    $periodoestimulo = split("/", $facturasdelefector[0]->getPeriodoActual());
                                                    $ano_estimulo = $periodoestimulo[0];
                                                    $mes_estimulo = $periodoestimulo[1];
                                                    $objetivosResultado = calcularObjetivos($efector->getCuie(), $ano_estimulo, $mes_estimulo, $efector->getTipoefector(), $facturasdelefector[0]->getTipoNomenclador(), $facturasdelefector[0]->getFechaEntrada());

                                                    if ($objetivosResultado == 0) {
//                                                        $para = "ferrando.pedro@gmail.com";
//                                                        $paracc = "bepetrella@hotmail.com";

                                                        $asunto = "Orden de Cargo: No se encontraron objetivos para el efector " . $facturasdelefector[0]->getCuie();
                                                        $contenido = "<b>Datos del Prestador: </b>" . $efector->getCuie() . " " . $efector->getNombreefector();
                                                        $contenido .="<BR><BR>";
                                                        $contenido .= "<b>Nro de Expediente: </b>" . $expediente->getNroExp();
                                                        $contenido .="<BR><BR>";
                                                        $contenido .= "<b>Periodo: </b>" . $mes_estimulo . "/" . $ano_estimulo;
                                                        $contenido .="<BR><BR>";
                                                        $contenido .= "<b>Fecha de Recepcion: </b>" . $facturasdelefector[0]->getFechaEntrada();
                                                        $contenido .="<BR><BR>";
                                                        $fecha_carga = date("Y-m-d H:i:s");
                                                        $contenido .="<b>Datos de Carga: Usuario:</b> $usuario[0]-$usuario[1]    <b>Fecha:</b>    $fecha_carga ";
//                                                        enviar_mail($para, $paracc, null, $asunto, $contenido, null, null, '0');
                                                        $sepuedeestimular = 'DISABLED';
                                                    }

                                                    /*
                                                     *      Objetivos
                                                     */
                                                    ?> 
                                                    <tr ><td colspan=7 align=center>
                                                            <table class="efector_<?php echo $i ?>" border=0 width=60% cellspacing=2 cellpadding=2  style="margin:10px auto 0;display:none;border: #000000 solid thin; text-align: center;">
                                                                <tr bgcolor="#BDBDBD">
                                                                    <td align="left" style="width: 245px;"><b>Objetivos</b></td>
                                                                    <td style="width: 100px"><b>Meta (%)</b></td>
                                                                    <td><b>Asignado</b></td>
                                                                    <td><b>Informado</b></td>
                                                                    <td style="width: 158px"><b>Cumplimiento (%)</b></td>
                                                                    <td><b>Cumplido</b></td>
                                                                    <td><b>Puntos</b></td>
                                                                </tr>
                                                                <? if ($objetivosResultado[12]['encontro']) { ?>
                                                                    <tr >
                                                                        <td align="left" bgcolor="#DDDDDD">Cobertura Efectiva Basica</td>
                                                                        <td> - </td>
                                                                        <td><?php echo $objetivosResultado[12]['numerador'] ?></td>
                                                                        <td><?php echo $objetivosResultado[12]['denominador'] ?></td>
                                                                        <td><?php echo number_format($objetivosResultado[12]['total_perc'], 2) ?></td>
                                                                        <td><?php echo $objetivosResultado[12]['cumplido'] ?></td>
                                                                        <td><?php echo $objetivosResultado[12]['puntos'] ?></td>
                                                                    </tr>
                                                                <? }if ($objetivosResultado[0]['encontro']) { ?>
                                                                    <tr >
                                                                        <td align="left" bgcolor="#DDDDDD">Captacion Embarazada</td>
                                                                        <td><?php echo $objetivosResultado[0]['meta'] ?></td>
                                                                        <td><?php echo $objetivosResultado[0]['numerador'] ?></td>
                                                                        <td><?php echo $objetivosResultado[0]['denominador'] ?></td>
                                                                        <td><?php echo number_format($objetivosResultado[0]['total_perc'], 2) ?></td>
                                                                        <td><?php echo $objetivosResultado[0]['cumplido'] ?></td>
                                                                        <td><?php echo $objetivosResultado[0]['puntos'] ?></td>
                                                                    </tr>
                                                                <? }if ($objetivosResultado[1]['encontro']) { ?>
                                                                    <tr>
                                                                        <td align="left" bgcolor="#DDDDDD">Apgar'>5</td>
                                                                        <td><?php echo $objetivosResultado[1]['meta'] ?></td>
                                                                        <td><?php echo $objetivosResultado[1]['numerador'] ?></td>
                                                                        <td><?php echo $objetivosResultado[1]['denominador'] ?></td>
                                                                        <td><?php echo number_format($objetivosResultado[1]['total_perc'], 2) ?></td>
                                                                        <td><?php echo $objetivosResultado[1]['cumplido'] ?></td>
                                                                        <td><?php echo $objetivosResultado[1]['puntos'] ?></td>
                                                                    </tr>
                                                                <? }if ($objetivosResultado[2]['encontro']) { ?>
                                                                    <tr>
                                                                        <td align="left" bgcolor="#DDDDDD">Peso Al Nacer > 2500gr.</td>
                                                                        <td><?php echo $objetivosResultado[2]['meta'] ?></td>
                                                                        <td><?php echo $objetivosResultado[2]['numerador'] ?></td>
                                                                        <td><?php echo $objetivosResultado[2]['denominador'] ?></td>
                                                                        <td><?php echo number_format($objetivosResultado[2]['total_perc'], 2) ?></td>
                                                                        <td><?php echo $objetivosResultado[2]['cumplido'] ?></td>
                                                                        <td><?php echo $objetivosResultado[2]['puntos'] ?></td>
                                                                    </tr>
                                                                <? }if ($objetivosResultado[3]['encontro']) { ?>
                                                                    <tr>
                                                                        <td align="left" bgcolor="#DDDDDD">VDRL y ATT en Embarazo</td>
                                                                        <td><?php echo $objetivosResultado[3]['meta'] ?></td>
                                                                        <td><?php echo $objetivosResultado[3]['numerador'] ?></td>
                                                                        <td><?php echo $objetivosResultado[3]['denominador'] ?></td>
                                                                        <td><?php echo number_format($objetivosResultado[3]['total_perc'], 2) ?></td>
                                                                        <td><?php echo $objetivosResultado[3]['cumplido'] ?></td>
                                                                        <td><?php echo $objetivosResultado[3]['puntos'] ?></td>
                                                                    </tr>
                                                                <? }if ($objetivosResultado[4]['encontro']) { ?>
                                                                    <tr>
                                                                        <td align="left" bgcolor="#DDDDDD">VDRL y ATT Previa al Parto</td>
                                                                        <td><?php echo $objetivosResultado[4]['meta'] ?></td>
                                                                        <td><?php echo $objetivosResultado[4]['numerador'] ?></td>
                                                                        <td><?php echo $objetivosResultado[4]['denominador'] ?></td>
                                                                        <td><?php echo number_format($objetivosResultado[4]['total_perc'], 2) ?></td>
                                                                        <td><?php echo $objetivosResultado[4]['cumplido'] ?></td>
                                                                        <td><?php echo $objetivosResultado[4]['puntos'] ?></td>
                                                                    </tr>
                                                                <? }if ($objetivosResultado[5]['encontro']) { ?>
                                                                    <tr>
                                                                        <td align="left" bgcolor="#DDDDDD">Atencion de Muertes Materno/Infantiles</td>
                                                                        <td><?php echo $objetivosResultado[5]['meta'] ?></td>
                                                                        <td><?php echo $objetivosResultado[5]['numerador'] ?></td>
                                                                        <td><?php echo $objetivosResultado[5]['denominador'] ?></td>
                                                                        <td><?php echo number_format($objetivosResultado[5]['total_perc'], 2) ?></td>
                                                                        <td><?php echo $objetivosResultado[5]['cumplido'] ?></td>
                                                                        <td><?php echo $objetivosResultado[5]['puntos'] ?></td>
                                                                    </tr>
                                                                <? }if ($objetivosResultado[6]['encontro']) { ?>
                                                                    <tr>
                                                                        <td align="left" bgcolor="#DDDDDD">Cob. Inmunizaciones</td>
                                                                        <td><?php echo $objetivosResultado[6]['meta'] ?></td>
                                                                        <td><?php echo $objetivosResultado[6]['numerador'] ?></td>
                                                                        <td><?php echo $objetivosResultado[6]['denominador'] ?></td>
                                                                        <td><?php echo number_format($objetivosResultado[6]['total_perc'], 2) ?></td>
                                                                        <td><?php echo $objetivosResultado[6]['cumplido'] ?></td>
                                                                        <td><?php echo $objetivosResultado[6]['puntos'] ?></td>
                                                                    </tr>
                                                                <? }if ($objetivosResultado[7]['encontro']) { ?>
                                                                    <tr>
                                                                        <td align="left" bgcolor="#DDDDDD">Consejeria</td>
                                                                        <td><?php echo $objetivosResultado[7]['meta'] ?></td>
                                                                        <td><?php echo $objetivosResultado[7]['numerador'] ?></td>
                                                                        <td><?php echo $objetivosResultado[7]['denominador'] ?></td>
                                                                        <td><?php echo number_format($objetivosResultado[7]['total_perc'], 2) ?></td>
                                                                        <td><?php echo $objetivosResultado[7]['cumplido'] ?></td>
                                                                        <td><?php echo $objetivosResultado[7]['puntos'] ?></td>
                                                                    </tr>
                                                                <? }if ($objetivosResultado[8]['encontro']) { ?>
                                                                    <tr>
                                                                        <td align="left" bgcolor="#DDDDDD">Seguimiento de Niño < 1 año</td>
                                                                        <td><?php echo $objetivosResultado[8]['meta'] ?></td>
                                                                        <td><?php echo $objetivosResultado[8]['numerador'] ?></td>
                                                                        <td><?php echo $objetivosResultado[8]['denominador'] ?></td>
                                                                        <td><?php echo number_format($objetivosResultado[8]['total_perc'], 2) ?></td>
                                                                        <td><?php echo $objetivosResultado[8]['cumplido'] ?></td>
                                                                        <td><?php echo $objetivosResultado[8]['puntos'] ?></td>
                                                                    </tr>
                                                                <? }if ($objetivosResultado[9]['encontro']) { ?>
                                                                    <tr>
                                                                        <td align="left" bgcolor="#DDDDDD">Seguimiento de Niño >= 1 año</td>
                                                                        <td><?php echo $objetivosResultado[9]['meta'] ?></td>
                                                                        <td><?php echo $objetivosResultado[9]['numerador'] ?></td>
                                                                        <td><?php echo $objetivosResultado[9]['denominador'] ?></td>
                                                                        <td><?php echo number_format($objetivosResultado[9]['total_perc'], 2) ?></td>
                                                                        <td><?php echo $objetivosResultado[9]['cumplido'] ?></td>
                                                                        <td><?php echo $objetivosResultado[9]['puntos'] ?></td>
                                                                    </tr>
                                                                <? }if ($objetivosResultado[10]['encontro']) { ?>
                                                                    <tr >
                                                                        <td align="left" bgcolor="#DDDDDD">Fijo</td>
                                                                        <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
                                                                        <td><?php echo $objetivosResultado[10]['puntos'] ?></td>
                                                                    </tr>
                                                                <? } if ($objetivosResultado[11]['encontro']) { ?>
                                                                    <tr >
                                                                        <td align="left" bgcolor="#DDDDDD">Base</td>
                                                                        <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
                                                                        <td><?php echo $objetivosResultado[11]['puntos'] ?></td>
                                                                    </tr>
                                                                <? } ?>
                                                                <tr align=right>
                                                                    <td colspan=6 style="border-top: #000000 solid thin"><b>Total Puntos</b></td>
                                                                    <td style="border-top: #000000 solid thin">
                                                                        <?php echo $puntosentotal[$i] = $objetivosResultado[12]['puntos'] + $objetivosResultado[11]['puntos'] + $objetivosResultado[0]['puntos'] + $objetivosResultado[1]['puntos'] + $objetivosResultado[2]['puntos'] + $objetivosResultado[3]['puntos'] + $objetivosResultado[4]['puntos'] + $objetivosResultado[5]['puntos'] + $objetivosResultado[6]['puntos'] + $objetivosResultado[7]['puntos'] + $objetivosResultado[8]['puntos'] + $objetivosResultado[9]['puntos'] + $objetivosResultado[10]['puntos'] ?>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>

                                                    <? /*                                                     * ******************
                                                     *      Debito x Auditoria
                                                     * ************************* */ ?>

                                                    <tr >
                                                        <td colspan=7 align=center>
                                                            <div style="width: 75%;margin:0 auto;padding-left: 40px;">
                                                                <form id="form_debito_<?php echo $i ?>" name=form_debito action="listado_expedientes.php" method=POST>
                                                                    <table id="auditoria_<?php echo $i ?>" style="width: 94%;margin:20px auto 0;border: #000000 solid thin; text-align: center;float: left">
                                                                        <tr>
                                                                            <td colspan=5 style="background-color:#CCCC99;text-align: center;padding-top: 10px;padding-bottom: 5px;"><b>DEBITOS POR AUDITORIA</b></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td style="background-color:#CCCC99;text-align: center;padding-bottom: 5px;padding-top: 5px;width: 25%"><b>Debito Auditado</b></td>
                                                                            <td style="background-color:#CCCC99;text-align: center;padding-bottom: 5px;padding-top: 5px;width: 25%"><b>Nro. Exp. Relacionado</b></td>
                                                                            <td style="background-color:#CCCC99;text-align: center;padding-bottom: 5px;padding-top: 5px;width: 50%"><b>Descripcion</b></td>
                                                                        </tr>

                                                                        <?
                                                                        $sumadedebitos = 0;
                                                                        $debitosencontrados = debitoAuditado($expediente->getNroExp(), $facturasdelefector[0]->getCuie());
                                                                        if ($debitosencontrados->rowcount() > 0) {
                                                                            while (!$debitosencontrados->EOF) {
                                                                                ?>
                                                                                <tr >
                                                                                    <td style="font-size: smaller;border-bottom: #000000 solid thin;"><?php echo "$ " . number_format($debitosencontrados->fields['monto'], 2, ',', '.') ?></td>
                                                                                    <td style="font-size: smaller;border-bottom: #000000 solid thin;"><?php echo $debitosencontrados->fields['nro_exp_relac'] ?></td>
                                                                                    <td style="font-size: smaller;border-bottom: #000000 solid thin;"><?php echo $debitosencontrados->fields['descripcion'] ?>
                                                                                        <? if ($expediente->getEstado() != 'C') { ?>
                                                                                            <img id="imagen_ver_factura<?php echo $i ?>" src="<?php echo $img_quitar ?>" border=0 title="Quitar Debito" align="left" onclick="quitar_debito(<?php echo $i . ",'" . $facturasdelefector[0]->getCuie() . "','" . $debitosencontrados->fields['nro_exp_relac'] . "'" ?>);" style="cursor:pointer;float: right">
                                                                                        <? } ?>
                                                                                    </td>
                                                                                </tr>
                                                                                <?
                                                                                $sumadedebitos += $debitosencontrados->fields['monto'];
                                                                                $debitosencontrados->movenext();
                                                                            }
                                                                        }
                                                                        ?>

                                                                        <tr>  
                                                                            <?
                                                                            if ($total_debitado[$i] == null) {
                                                                                if ($expediente->getEstado() != 'C') {
                                                                                    ?> 
                                                                                    <? if (permisos_check("inicio", "reg_debitos")) { ?>
                                                                                        <td  colspan="3" style="font-size: smaller;cursor:pointer;text-decoration: underline;" onclick="ingresarDebito(<?php echo $i ?>)" >Presione aqui para ingresar un debito</td>
                                                                                    <? } ?>   

                                                                                <? } else { ?>
                                                                                    <td >-</td>
                                                                                    <td >-</td>
                                                                                    <td >-</td>
                                                                                <? } ?>
                                                                            <? } else { ?>
                                                                                <td id="debito_<?php echo $i ?>" style="cursor:pointer;text-decoration: underline;font-size: smaller" onclick="ingresarDebito(<?php echo $i ?>);"> 
                                                                                    <?
                                                                                    echo "$ " . number_format($total_debitado[$i], 2, ',', '.');
                                                                                    $sumadedebitos +=$total_debitado[$i];
                                                                                    ?>
                                                                                </td>
                                                                                <td id="debito_exp_<?php echo $i ?>" style="cursor:pointer;text-decoration: underline;font-size: smaller" onclick="ingresarDebito(<?php echo $i ?>);">
                                                                                    <?
                                                                                    if ($exprelac[$i] == null) {
                                                                                        $exprelac[$i] = 0;
                                                                                    }
                                                                                    echo $exprelac[$i];
                                                                                    ?>
                                                                                </td> 
                                                                                <td style="font-size: smaller">
                                                                                    <?
                                                                                    if ($desc[$i] == null) {
                                                                                        $desc[$i] = '-';
                                                                                    }
                                                                                    echo $desc[$i];
                                                                                    ?>
                                                                                </td>
                                                                            <? } ?>
                                                                        </tr>
                                                                        <tr>                                                                
                                                                            <td colspan="3" style="border-top:thin solid #000000;padding-top: 10px">
                                                                                <table style="width: 100%">
                                                                                    <tr style="background-color:#CCCCBB">
                                                                                        <? $sumatotaldedebitos+=$sumadedebitos; ?>
                                                                                        <td style="font-size: small"><b>Total Liquidado  </b><?php echo "$ " . number_format($total_liq[$i], 2, ',', '.') ?></td>
                                                                                        <td style="font-size: small"><b>Total Debitado  </b><?php echo "$ " . number_format($sumadedebitos, 2, ',', '.') ?></td>
                                                                                        <? $total_liq[$i] = $total_liq[$i] - $sumadedebitos ?>
                                                                                        <td style="font-size: small"><b>Saldo Total  </b><? echo "$ " . number_format($total_liq[$i], 2, ',', '.') ?></td>
                                                                                    </tr>
                                                                                </table>

                                                                        </tr>
                                                                    </table> 
                                                                    <input name="exprelacaldebito" type="hidden" value="<?php echo $exprelac[$i] ?>"/>
                                                                    <input name="total_debitado" type="hidden" value="<?php echo $total_debitado[$i] ?>"/>
                                                                    <input name="expedientebuscado" type="hidden" value="<?php echo $expediente->getNroExp() ?>"/>
                                                                    <input name="descripcion_deb" type="hidden" value="<?php echo $desc[$i] ?>"/>
                                                                    <input name="cuie_deb" type="hidden" value="<?php echo $facturasdelefector[0]->getCuie() ?>"/>
                                                                    <input name="buscar_expediente" type="hidden" value="Buscar Expediente"/>
                                                                    <input name="tablaseleccionada" type="hidden" value="<?php echo $i ?>"/>
                                                                    <? if ($expediente->getEstado() != 'C') { ?>

                                                                        <? if (permisos_check("inicio", "reg_debitos")) { ?>
                                                                            <button id="debitar_<?php echo $i ?>" name="debitar" value="debitar" type="submit" title="Confirmar el Debito" style="float: left;cursor:default;height: 35px;width:35px;text-align: center;margin-top: 40px;margin-left: 5px;" disabled="disabled" >
                                                                                <img src="../../imagenes/check1.gif"></img>
                                                                            </button>
                                                                        <? } ?>  
                                                                    <? } ?>
                                                                </form>
                                                            </div>                                                
                                                        </td>
                                                    </tr> 

                                                    <!-- OBSERVACIONES -->
                                                    <tr >
                                                        <td colspan=7 align=center>
                                                            <div style="width: 75%;margin:0 auto;padding-left: 40px;">
                                                                <form id="form_obs_<?php echo $i ?>" name=form_obs action="listado_expedientes.php" method=POST>
                                                                    <table id="obs_<?php echo $i ?>" style="width: 94%;margin:20px auto 0;border: #000000 solid thin; text-align: center;float: left">
                                                                        <tr>
                                                                            <td colspan=5 style="background-color:#CCCC99;text-align: center;padding-top: 10px;padding-bottom: 5px;"><b>OBSERVACIONES DE LA FACTURACION</b></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan=5 style="background-color:#CCCC99;text-align: center; padding-bottom: 5px;padding-top: 5px;"><b>Observaciones</b></td>
                                                                        </tr>

                                                                        <?
                                                                        $obsencontrados = obsRegistradas($expediente->getNroExp(), $facturasdelefector[0]->getCuie());
                                                                        if ($obsencontrados->rowcount() > 0) {
                                                                            while (!$obsencontrados->EOF) {
                                                                                ?>
                                                                                <tr >
                                                                                    <td style="font-size: smaller;border-bottom: #000000 solid thin;"><?php echo $obsencontrados->fields['observacion'] ?>
                                                                                        <? if ($expediente->getEstado() != 'C') { ?>
                                                                                            <? if (permisos_check("inicio", "reg_obs")) { ?>
                                                                                                <img id="imagen_ver_factura<?php echo $i ?>" src="<?php echo $img_quitar ?>" border=0 title="Quitar Observación" align="left" onclick="quitar_observacion(<?php echo $i . ",'" . $facturasdelefector[0]->getCuie() . "'" ?>);" style="cursor:pointer;float: right">
                                                                                            <? } ?>    
                                                                                        <? } ?>
                                                                                    </td>
                                                                                </tr>
                                                                                <?
                                                                                $obsencontrados->movenext();
                                                                            }
                                                                        }
                                                                        ?>

                                                                        <tr>  
                                                                            <? if ($descobs[$i] == null) { ?>  
                                                                                <? if ($expediente->getEstado() != 'C') { ?>
                                                                                    <? if (permisos_check("inicio", "reg_obs")) { ?>
                                                                                        <td  colspan="3" style="font-size: smaller;cursor:pointer;text-decoration: underline;" onclick="ingresarObs(<?php echo $i ?>)" >Presione aqu&iacute; para ingresar una Observaci&oacute;n</td>
                                                                                    <? } ?>   
                                                                                <? } else { ?>
                                                                                    <td >-</td>
                                                                                <? } ?>
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
                                                                    <input name="observacion" type="hidden" value="<?php echo $descobs[$i] ?>"/>
                                                                    <input name="expedientebuscado" type="hidden" value="<?php echo $expediente->getNroExp() ?>"/>
                                                                    <input name="cuie_obs" type="hidden" value="<?php echo $facturasdelefector[0]->getCuie() ?>"/>   
                                                                    <input name="buscar_expediente" type="hidden" value="Buscar Expediente"/>
                                                                    <? if ($expediente->getEstado() != 'C') { ?>

                                                                        <? if (permisos_check("inicio", "reg_obs")) { ?>
                                                                            <button id="observar_<?php echo $i ?>" name="observar" value="observar" type="submit" title="Confirmar la Observaci&oacute;n" style="float: left;cursor:default;height: 35px;width:35px;text-align: center;margin-top: 40px;margin-left: 5px;" disabled="disabled" >
                                                                                <img src="../../imagenes/check1.gif"></img>
                                                                            </button>
                                                                        <? } ?>  
                                                                    <? } ?>

                                                                </form>
                                                            </div>                                                
                                                        </td>
                                                    </tr> 
                                                    <tr><td>&nbsp;</td></tr>
                                                    <!-- debitos retroactivos -->
                                                    <tr>
                                                        <td colspan="5">
                                                            <?php 
                                                                $debitosRetro = DebitoRetroactivoColeccion::getResumenDebitoRetroactivo($efector->getCuie(),$expediente->getNroExp()); 
                                                                $sumaDebitosRetro += $debitosRetro->fields['monto'];
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
                                                                        <label id="lbl_cant_dbt_ret_<?php echo $efector->getCuie();?>">
                                                                        <?php echo $debitosRetro->fields['total']!="" ? $debitosRetro->fields['total'] : 0 ;?>
                                                                        </label>
                                                                    </td>
                                                                    <td align="center">
                                                                        $ <label id="lbl_monto_dbt_ret_<?php echo $efector->getCuie();?>">
                                                                        <?php echo $debitosRetro->fields['monto']!="" ? number_format($debitosRetro->fields['monto'],2) : 0 ;?>
                                                                        </label>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td> 
                                                        <td>
                                                            <!-- btn debito retro -->
                                                            <?php if($mostrarBtnDbtRet){ ?>
                                                                <button type="button" onclick="switchTabDebitoRetro('','dbt_lst','<?php echo $efector->getCuie(); ?>');">
                                                                    Ver / Aplicar
                                                                </button>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                    <tr><td>&nbsp;</td></tr>

                                                    <? /*                                                     * ***************
                                                     *      Total a pagar
                                                     * ****************** */ ?>

                                                    <tr>
                                                        <td colspan=7 align=center>
                                                            <table id="totalapagar_<?php echo $i ?>" border=0 width=60% cellspacing=2 cellpadding=2  style="margin:20px auto 0;display:none;border: #000000 solid thin; text-align: center;">
                                                                <tr bgcolor="thistle">
                                                                    <td><b>Total a Pagar</b></td>
                                                                    <td><b>Puntos</b></td>
                                                                    <td><b>Fondos para el Efector</b></td>
                                                                    <td><b>Fondos para Estimulos</b></td>
                                                                </tr>
                                                                <tr><?php
                                                                        $total_liq[$i] = $total_liq[$i] - $debitosRetro->fields['monto'];
                                                                        $aux1 = $total_liq[$i];
                                                                        $aux2 = $puntosentotal[$i];
                                                                        if ($aux2 > 0) {
                                                                            $estimulacion[$i] = ($aux1 * $aux2) / 100;
                                                                        } else {
                                                                            $estimulacion[$i] = 0;
                                                                        }
                                                                        $estimulacion_formateada = floatval(number_format($estimulacion[$i], 3, '.', ''));
                                                                        $total_formateada = floatval(number_format($aux1, 2, '.', ''));
                                                                        $paraefector[$i] = $total_formateada - $estimulacion_formateada;
                                                                     ?>
                                                                    <td>
                                                                        <label id="lbl_total_a_pagar_<?php echo $efector->getCuie();?>">
                                                                            <?php echo "$ " . number_format($total_liq[$i], 2); ?>
                                                                        </label>
                                                                        <input type="hidden" name="total_a_pagar_<?php echo $efector->getCuie();?>" 
                                                                               value="<?php echo $total_liq[$i];?>"/>
                                                                    </td>
                                                                    <td>
                                                                        <?php echo $aux2 ?>
                                                                        <input type="hidden" name="puntos_<?php echo $efector->getCuie();?>" 
                                                                               value="<?php echo $puntosentotal[$i];?>"/>
                                                                    </td>
                                                                    <td>
                                                                        <label id="lbl_fondos_efector_<?php echo $efector->getCuie();?>">
                                                                            <?php echo "$ " . number_format($paraefector[$i], 2); ?>
                                                                        </label>
                                                                        <input type="hidden" name="fondos_efector_<?php echo $efector->getCuie();?>" 
                                                                               value="<?php echo $paraefector[$i];?>"/>
                                                                    </td>
                                                                    <td>
                                                                        <label id="lbl_fondos_estimulo_<?php echo $efector->getCuie();?>">
                                                                            <?php echo "$ " . number_format($estimulacion[$i], 2) ?>
                                                                        </label>
                                                                        <input type="hidden" name="fondos_estimulo_<?php echo $efector->getCuie();?>" 
                                                                               value="<?php echo $estimulacion[$i];?>"/>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <?
                                    $i++;
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <? /*         * ***************
         *      Total del expediente
         * ****************** */ ?>

        <br></br>
        <div style="margin: 0 auto;font-size: medium;width: 25%;padding-bottom: 5px" ><b>Total del Expediente:</b></div>
        <div style="width: 70%;margin:0 auto">
            <table id="totaldelexpediente" bgcolor="#FFFFFF" border=0 cellspacing=2 cellpadding=2 align=center 
                   style="border: #000000 solid thin;float:left;margin-left: 30px;">
                <tr style="margin: 10px 20px;background-color:lightcoral">
                    <td ><b>Cant. Practicas</b></td>                                        
                    <td ><b>Monto Prefactura</b></td>
                    <td ><b>Rechazados</b></td>
                    <td ><b>Monto Rechazado</b></td>
                    <td><b>Cant. Aceptados</b></td>
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
                        <?php echo $prac_total_total - $deb_prac_total ?>
                    </td>
                    <td align="center"> <!-- monto debitos -->
                       <?php $totalDebitos = $sumatotaldedebitos + $sumaDebitosRetro; ?>
                        <label id="lbl_total_debitos">
                            <?php echo "$ " . number_format($totalDebitos, 2, ",", "."); ?>
                        </label>
                        <input type="hidden" name="total_debitos" value="<?php echo $totalDebitos;?>"/>
                    </td>
                    <td align="center">
                        <?php $totaltotal_liq = 0;
                            foreach ($total_liq as $unaliquidacion) {
                                $totaltotal_liq+=$unaliquidacion;
                            }
                        ?>
                        <label id="lbl_total_liquidado">
                            <?php echo "$ " . number_format($totaltotal_liq, 2, ",", "."); ?>
                        </label>
                        <input type="hidden" name="total_liquidado" value="<?php echo $totaltotal_liq;?>"/>
                    </td>
                    <td align="center" style="width: 80px">
                        <?php $totaltotal_efector = 0;
                            foreach ($paraefector as $unefector) {
                                $totaltotal_efector+=$unefector;
                            }
                        ?>
                        <label id="lbl_total_efector">
                            <?php echo "$ " . number_format($totaltotal_efector, 2, ",", "."); ?>
                        </label>
                        <input type="hidden" name="total_efector" value="<?php echo $totaltotal_efector;?>"/>
                    </td>
                    <td align="center" style="width: 80px">
                        <?php $totaltotal_estimulado = 0;
                            foreach ($estimulacion as $unaestimulacion) {
                                $totaltotal_estimulado+=$unaestimulacion;
                            }
                        ?>
                        <label id="lbl_total_estimulo">
                            <?php echo "$ " . number_format($totaltotal_estimulado, 2, ",", "."); ?>
                        </label>
                        <input type="hidden" name="total_estimulo" value="<?php echo $totaltotal_estimulado;?>"/>
                    </td>            
                </tr>
            </table>
            <form name="formestimul" action="listado_expedientes.php" method=POST>  
                <input name="expedientebuscado" type="hidden" value="<?php echo $expediente->getNroExp() ?>"/>

                <input name="totaltotal_liq" type="hidden" value="<? print_r($totaltotal_liq) ?>"/>
                <input name="prac_total_total" type="hidden" value="<? print_r($prac_total_total) ?>"/>
                <input name="deb_prac_total" type="hidden" value="<? print_r($deb_prac_total) ?>"/>
                <input name="total_estimulado" type="hidden" value="<? print_r($totaltotal_estimulado) ?>"/>
                <input name="total_rechazado" type="hidden" value="<? print_r($total_totaldebitado) ?>"/>

                <input name="liq_total" type="hidden" value="<? print_r(serialize($total_liq)) ?>"/>
                <input name="puntos" type="hidden" value="<? print_r(serialize($puntosentotal)) ?>"/>                

                <? if ($expediente->getEstado() != 'C') { ?>
                    <? if (permisos_check("inicio", "reg_estimulos")) { ?>
                        <button name="estimular" value="estimular" type="submit" title="Confirmar los estimulos" style="cursor: default;height: 30px;width:35px;text-align: center;margin-top: 5px;margin-left: 5px;" <?php echo $sepuedeestimular ?> >
                            <img src="../../imagenes/check1.gif"></img>
                        </button>
                    <? } ?>
                    <?
                }
                if ($hayfacturasabiertas) {
                    ?>
                    <? $link = encode_link("lista_facturas_abiertas.php", array("facturas" => $facturasabiertas)); ?>
                    <div style="font-size: smaller; float: none; padding-top: 5px;padding-right: 70px"><a style="color:red;text-decoration: underline" target='_blank' href="<?php echo $link ?>" title='Facturas Abiertas'>No se puede proceder con estimulos mientras existan facturas abiertas</a></div>
                <? } ?>
            </form>
        </div>

        <br></br>

    <?php } elseif ($msje) { ?>
        <table border=0 width=90% cellspacing=2 cellpadding=2 bgcolor='<?php echo $bgcolor3 ?>' align=center style="margin-top: 10px">
            <tr>
                <td colspan=12 align=left id=ma>
                    <table width=100%>
                        <tr id=ma>
                            <td align="center" width=30% align=left style="font-size:small;"><?php echo $msje; ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    <?php } ?>


    <div align="center">
        <form id="formulario" name=form1 action="listado_expedientes.php" method=POST align=center style="margin:0 auto;width: 500px;">
            <div align=center style="margin:0 auto; font-size:larger; margin-top: 20px; background-color: rgba(108,148,199, 0.6); border: thin solid #000000;padding:10px 20px;">
                <div style="font-size:smaller;margin:0 auto;">(Cod. de Org. - Nº Correlativo - Año) </div>
                <b>Buscar expediente: </b><input id="expedientebuscado" value="<?php echo $expedientebuscado ?>"name="expedientebuscado"></input>
                <button type="submit" name="buscar_expediente" value="Buscar Expediente" style="cursor: default;height: 25px;width:100px;text-align: center;">Buscar</button> <br />

                <div align=center style="font-size:smaller; color: #BC131A; margin: 0 auto">Ej. 0012-03-12</div>
            </div>
        </form>
    </div>

    <div style="width:300px;">
        <? echo fin_pagina(); // aca termino ?>
    </div>

</div>
