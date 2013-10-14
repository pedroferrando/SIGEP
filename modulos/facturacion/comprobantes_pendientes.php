<?php
require_once ("../../config.php");
require_once ("../../lib/bibliotecaTraeme.php");


extract($_POST, EXTR_SKIP);
if ($parametros)
    extract($parametros, EXTR_OVERWRITE);



echo $html_header;

echo "<script src='../../lib/jquery.min.js' type='text/javascript'></script>";
echo "<script src='../../lib/jquery/jquery-ui.js' type='text/javascript'></script>";
echo "<link rel='stylesheet' href='../../lib/jquery/ui/jquery-ui.css'/>";
echo "<script src='../../lib/jquery/ui/jquery.ui.datepicker-es.js' type='text/javascript'></script>";
?>
<script>
    function muestra_practicas(nro) {
        var obj = $("#practicas" + nro);

        if (obj.css("display") == 'none') {
            $(".practica").css("display", "none");
            obj.css("display", "table-row");
        }
        else {
            obj.css("display", "none");
        }
    }

    $(document).ready(function() {
        $('#periodosh').accordion({heightStyle: "content"});

        $('#cuie').on('change', function() {

            var cuie = $('#cuie').val();
            var imgPaht = "../../imagenes/wait.gif";
            $('#periodosh').empty();
            $('<img src="' + imgPaht + '">').width(90).height(20).appendTo('#img_load');
            $.post("comprobantes_pendientes.php", {'cuie': cuie}, function(data) {
                var lospendientes = $(data).find('#periodosh');
                $('#periodosh').replaceWith(lospendientes);
                $('#periodosh').accordion({heightStyle: "content"});
                $('#img_load').empty();
            });
        });


        $('#cuieBtn').on('click', function() {

            var cuie = $('#cuie').val();
            var imgPaht = "../../imagenes/wait.gif";
            $('#periodosh').empty();
            $('<img src="' + imgPaht + '">').width(90).height(20).appendTo('#img_load');
            $.post("comprobantes_pendientes.php", {'cuie': cuie}, function(data) {
                var lospendientes = $(data).find('#periodosh');
                $('#periodosh').replaceWith(lospendientes);
                $('#periodosh').accordion({heightStyle: "content"});
                $('#img_load').empty();
            });
        });


    });
</script>

<fieldset style="width: 98%;border: solid grey thin;border-radius:15px;margin: 0 auto 0 auto;padding-top: 5px;margin-top: 10px">
    <legend><b><?= "Comprobantes Pendientes de Facturación" ?></b></legend>
    <table style="width: 90%; margin: 0 auto 0 auto;">
        <tr>
            <td align="right" style="width: 40%">
                <b>Efector:</b>
            </td>
            <td style="width: 60%">
                <select id="cuie" name=cuie Style="width:400px" 
                        onKeypress="buscar_combo(this);"
                        onblur="borrar_buffer();"
                        onchange="borrar_buffer();">
                            <?
                            $cuieses = traeCuiesPorUsuario($_ses_user['id']);
                            $sql = "select n.cuie, nombreefector, upper(trim(com_gestion)) as com_gestion 
                                                                        from nacer.efe_conv n 
                                                                        inner join nacer.conv_nom cn using(id_efe_conv)
                                                                        inner join facturacion.smiefectores s on n.cuie=s.cuie
                                                                        where n.cuie in $cuieses
                                                                        and cn.activo='t' and n.activo='t'
                                                                        order by n.cuie";
                            $res_efectores = sql($sql) or fin_pagina();
                            while (!$res_efectores->EOF) {
                                $com_gestion = $res_efectores->fields['com_gestion'];
                                $cuiel = $res_efectores->fields['cuie'];
                                if ($cuie == null) {
                                    $cuie = $cuiel;
                                }
                                $nombre_efector = $res_efectores->fields['nombreefector'];
                                ($com_gestion == 'FALSO') ? $color_style = '#F78181' : $color_style = '';
                                ?>
                        <option value='<?= $cuiel ?>' <? if ($cuie == $cuiel)
                                echo "selected"
                                ?> Style="background-color: <?= $color_style ?>;"><?= $cuiel . " - " . $nombre_efector ?></option>
                                <?
                                $res_efectores->movenext();
                            }
                            ?>
                </select>
                <input id="cuieBtn" type="button" value="Actualizar">
            </td>
        </tr>
        <tr>
            <td align="center" colspan="2">
                <br />
                <div id="img_load" align="center"></div>
            </td>
        </tr>
        <tr>
            <?
            $sql_periodos = "select periodo 
                            from facturacion.comprobante
                            where comprobante.id_factura is null 
                            and comprobante.cuie='$cuie' 
                            and marca=0 
                            and (comprobante.activo='S' or comprobante.activo is NULL)
                            and periodo in (select periodo from facturacion.periodo where tipo<>'')
                            and fecha_comprobante>='2013-01-01' 
                        group by periodo order by periodo desc";
            $periodos_result = sql($sql_periodos);
            ?>
            <td colspan="2">
                <div id="periodosh" style="padding-top: 10px">
                    <?
                    if ($_POST['cuie']) {
                        if (!$periodos_result->EOF) {
                            $contador = 1;
                            $contadordepracticas = 1;
                            while (!$periodos_result->EOF) {
                                $unperiodo = $periodos_result->fields['periodo'];
                                $anio = substr($unperiodo, 0, 4);
                                $mes = substr($unperiodo, 5, 2);
                                $fecha_desde = ereg_replace('/', '-', $unperiodo) . '-01';
                                $fecha_hasta = ereg_replace('/', '-', $unperiodo) . '-' . ultimoDia($mes, $anio);
                                $sql_practicas = "select * 
                                            from facturacion.comprobante
                                            where comprobante.id_factura is null 
                                            and comprobante.cuie='$cuie' 
                                            and marca=0 and periodo='$unperiodo' 
                                            and (comprobante.activo='S' or comprobante.activo is NULL)
                                            and fecha_comprobante between '$fecha_desde' and '$fecha_hasta'";
                                $comprobante_result = sql($sql_practicas);
                                $sepuede[0] = false;
                                while (!$comprobante_result->EOF) {
                                    $sepuede = sePuedeAgregarComprobante($cuie, $comprobante_result->fields['id_comprobante'], $comprobante_result->fields['id_periodo'], $comprobante_result->fields['fecha_comprobante'], $comprobante_result->fields['clavebeneficiario'], $cantidadprestacion);
                                    if ($sepuede[0])
                                        break;
                                    $comprobante_result->movenext();
                                }
                                if ($sepuede[0]) {
                                    ?>
                                    <h3><?= "Periodo " . $unperiodo ?></h3>
                                    <div>
                                        <table style="width: 100%;padding-bottom: 20px">
                                            <tr>
                                                <th style="border-bottom: solid black thin;width: 20px"><b>#</b></th>
                                                <th style="border-bottom: solid black thin;width: 90px"><b>Comprobante</b></th>
                                                <th style="border-bottom: solid black thin;width: 90px"><b>Fecha</b></th>
                                                <th style="border-bottom: solid black thin;width: 140px"><b>Clave Beneficiario</b></th>
                                                <th style="border-bottom: solid black thin;width: 150px"><b>Apellido</b></th>
                                                <th style="border-bottom: solid black thin;width: 150px"><b>Nombre</b></th>
                                                <th style="border-bottom: solid black thin;width: 90px"><b>Nomenclador</b></th>
                                            </tr>

                                            <?
                                            $sql_practicas = "select * 
                                            from facturacion.comprobante
                                            where comprobante.id_factura is null 
                                            and comprobante.cuie='$cuie' 
                                            and marca=0 and periodo='$unperiodo' 
                                            and (comprobante.activo='S' or comprobante.activo is NULL)
                                        and fecha_comprobante between '$fecha_desde' and '$fecha_hasta'";
                                            if ($_POST['cuie']) {
                                                $practicas_result = sql($sql_practicas);
                                            }

                                            while (!$practicas_result->EOF) {
                                                if ($_POST['cuie']) {
                                                    $sepuede = sePuedeAgregarComprobante($cuie, $practicas_result->fields['id_comprobante'], $practicas_result->fields['id_periodo'], $practicas_result->fields['fecha_comprobante'], $practicas_result->fields['clavebeneficiario'], $cantidadprestacion);
                                                }
                                                if ($sepuede[0]) {
                                                    ?>
                                                    <tr onclick=" muestra_practicas(<?= $contador ?>)">
                                                        <td align="center">                    <?= $contadordepracticas ?>
                                                        </td>
                                                        <td align="center">
                                                            <?= $practicas_result->fields['id_comprobante'] ?>
                                                        </td>
                                                        <td align="center">
                                                            <?= substr($practicas_result->fields['fecha_comprobante'], 0, 10) ?>
                                                        </td>
                                                        <td align="center">
                                                            <?= $practicas_result->fields['clavebeneficiario'] ?>
                                                        </td>
                                                        <td align="center"> 
                                                            <?= $sepuede['beneficiario']['afiapellido'] ?>
                                                        </td>   
                                                        <td align="center">
                                                            <?= $sepuede['beneficiario']['afinombre'] ?>
                                                        </td>   
                                                        <td align="center">
                                                            <?= $practicas_result->fields['tipo_nomenclador'] ?>
                                                        </td>
                                                    </tr>
                                                    <tr class="practica" id="<?= 'practicas' . $contador ?>" style="display: none">
                                                        <td colspan="8" align="center">
                                                            <table style="border: #000 thin solid; background-color: #E0EBFF;width: 95%">
                                                                <tr style="background-color: #B1C5E0;">
                                                                    <td width="20%"><b>Codigo</b></td>
                                                                    <td width="50%"><b>Descripción</b></td>
                                                                    <td width="15%">Cant. Practicas</td>
                                                                    <td width="15%"><b>Precio</b></td>
                                                                </tr>
                                                                <? $prestaciones = prestacionesEnComprobante($practicas_result->fields['id_comprobante']);
                                                                while (!$prestaciones->EOF) {
                                                                    ?>
                                                                    <tr>			                                 
                                                                        <td style='border-bottom: thin black solid;border-left: thin black solid;border-right: thin black solid'><?= $prestaciones->fields["codigo"] . " " . $prestaciones->fields["diagnostico"] ?></td>
                            <? $grupo_etareo = calcularGrupoEtareo($fechanac, $res_comprobante->fields['fecha_comprobante']) ?>
                                                                        <td style='border-bottom: thin black solid;border-right: thin black solid'><?= descripcionDeDiagnostico($prestaciones->fields["codigo"], $prestaciones->fields["diagnostico"], $grupo_etareo['categoria']) ?></td>
                                                                        <td style="border-bottom: thin black solid;border-right: thin black solid"><?= $prestaciones->fields["cantidad"] ?></td>
                                                                        <td style='border-bottom: thin black solid;border-right: thin black solid'><?= number_format($prestaciones->fields["precio_prestacion"] * $prestaciones->fields["cantidad"], 2, ',', '.') ?></td>
                                                                    </tr>
                                                                    <?
                                                                    $prestaciones->movenext();
                                                                }
                                                                ?>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <?
                                                    $contador++;
                                                    $contadordepracticas++;
                                                }
                                                $practicas_result->movenext();
                                            }
                                            ?>                                    
                                        </table>
                                    </div>                             
                                    <?
                                } else {
                                    echo '<h3>Periodo ' . $unperiodo . ' No hay comprobantes pendientes para facturar</h3><div></div>';
                                }
                                $periodos_result->movenext();
                                $contadordepracticas = 1;
                            }
                        } else {
                            ?>
                            <h3>No hay comprobantes pendientes para facturar</h3>
                            <div></div>
                        <?
                        }
                    }
                    ?>
                </div>
            </td>
        </tr>
    </table>
</fieldset>
