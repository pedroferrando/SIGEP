<?php
require_once("../../config.php");
require_once("./calculoObjetivos2012.php");
require_once("./bibiliotecaExpediente.php");
require_once("../../clases/DebitoRetroactivo.php");
require_once("../../clases/Expediente.php");
require_once("../../clases/Factura.php");
require_once("../../clases/Smiefectores.php");
require_once("../../clases/Debito.php");
require_once("../../clases/Comprobante.php");
require_once("../../clases/Prestacion.php");
require_once("../../clases/Nomenclador.php");

$img_ext = '../../imagenes/rigth2.gif';
$img_quitar = '../../imagenes/salir2.gif';
$puntosfijos = 10;
$sepuedeestimular = '';
$sumatotaldedebitos = 0;
unset($prac_total);
unset($montofactura);
unset($totaldebitado);
unset($paraefector);
unset($deb_total);
unset($estimulacion);
unset($total_liq);
$fa = 0; //contador de facturas abiertas

$fecha_hoy = date("Y-m-d H:i:s");
$usuario[0] = $_ses_user['id'];
$usuario[1] = $_ses_user['name'];
$permiso = permisos_check("inicio", "ordenes_de_cargo");
$permisoDebitoRet = permisos_check("inicio", "reg_debitos_retroactivos");

if ($_POST['estimular']) {
    //Datos para el expediente
    $elexpediente = trim($_POST['expedientebuscado']);
    $m_total_liquidado = $_POST['totaltotal_liq'];
    $prac_ac = $_POST['prac_total_total'];
    $prac_deb = $_POST['deb_prac_total'];
    $m_total_estimulo = $_POST['total_estimulado'];
    $m_total_rechazado = $_POST['total_rechazado'];

    //Datos de objetivos liquidados
    $lospuntos = unserialize($_POST['puntos']);
    $liquidaciones = unserialize($_POST['liq_total']);

    $elano = $_POST['ano'];
    $elmes = $_POST['mes'];

    sql('BEGIN');
    $queryupdateexpediente = "UPDATE facturacion.expediente
                            SET total_monto_liquidado='$m_total_liquidado',
                            total_practicas='$prac_ac',
                            total_prac_rechazadas='$prac_deb',
                            monto_total_rechazado='$m_total_rechazado',
                            total_estimulo='$m_total_estimulo',
                            estado='C',
                            fecha_cierre=current_timestamp
                            WHERE nro_exp='$elexpediente'";
    sql($queryupdateexpediente) or die;

    persistirPracticasDelExpediente($elexpediente);
    sql("COMMIT");
    $_POST['buscar_expediente'] = "Buscar Expediente";
}

if ($_POST['buscar_expediente']) {
    $expedientebuscado = str_replace(" ", "", $_POST['expedientebuscado']);
    if ($expedientebuscado != '') {
        $expediente = ExpedienteCollecion::Filtrar("nro_exp='$expedientebuscado'");
        if (isset($expediente)) {
            if ($permiso && $expediente->getEstado() == 'C') {
                include_once 'expediente_persistido.php';
                echo fin_pagina();
            } else {
                //obtengo los datos del pago del expte
                $resPago = buscarDatosExpediente($expedientebuscado, "estado");
                if ($resPago->RecordCount() > 0) {
                    $tienePago = true;
                    $estadoPago = $resPago->fields['estado'];
                }
                if ($expediente->getEstado() == 'C' && $tienePago && $estadoPago == 'C') {
                    include_once 'expediente_persistido.php';
                    echo fin_pagina();
                } else {
                    $msje = "<b>El expediente se encuentra en proceso</b>";
                }
            }
        } else {
            $msje = "<b>No se encuentra el expediente</b>";
        }
        //$facturasdelexpediente = $expediente->getFacturas();
    }
}

if ($_POST['consultar_practicas']) {
    $factura = FacturaColeccion::buscar($_POST['consultar_practicas']);
    $nomencladoresfacturados = $factura->getPracticasConCodigoDiferente();
    ?>
    <tr>
        <td style="background-color:#CCCC99;text-align: center;"><b>Codigo</b></td>                                                                                                
        <td style="background-color:#CCCC99;text-align: center;"><b>Cantidad</b></td>
        <?php
        if (($factura->getTipoNomenclador() != "PERINATAL_CATASTROFICO") && ($factura->getTipoNomenclador() != "PERINATAL_NO_CATASTROFICO")) {
            echo "<td style='background-color:#CCCC99;text-align: center;'><b>Precio U</b></td>";
        }
        ?>
        <td style="background-color:#CCCC99;text-align: center;"><b>Total Liquidado</b></td>
        <td style="background-color:#CCCC99;text-align: center;"><b>Rechazados</b></td>
        <td style="background-color:#CCCC99;text-align: center;"><b>Monto Rechazado</b></td>
        <td style="background-color:#CCCC99;text-align: center;"><b>Aceptados</b></td>
        <td style="background-color:#CCCC99;text-align: center;"><b>Monto Aceptado</b></td>
    </tr>
    <?php
    foreach ($nomencladoresfacturados as $nomencladorfacturado) {
        if ($otrocolorstyle == '#AACC99')
            $otrocolorstyle = '#AACCBB';
        else
            $otrocolorstyle = '#AACC99';
        ?>
        <tr style="background-color: <?php echo $otrocolorstyle ?>">
            <td><?php echo $nomencladorfacturado['codigo'] ?>   </td>
            <td><?php echo $nomencladorfacturado['cantidad'] ?></td>
            <?php
            if (($factura->getTipoNomenclador() != "PERINATAL_CATASTROFICO") && ($factura->getTipoNomenclador() != "PERINATAL_NO_CATASTROFICO")) {
                echo "<td>$ " . number_format($nomencladorfacturado['precio_unitario'], 2, ",", ".") . "</td>";
            }
            ?>
            <td><?php echo "$ " . number_format($nomencladorfacturado['monto'], 2, ",", ".") ?></td>
            <td><?php echo $nomencladorfacturado['cantidad_debitados'] ?></td>
            <td><?php echo "$ " . number_format($nomencladorfacturado['monto_debito'], 2, ",", ".") ?></td>
            <td><?php echo $nomencladorfacturado['cantidad'] - $nomencladorfacturado['cantidad_debitados'] ?></td>
            <td><?php echo "$ " . number_format($nomencladorfacturado['monto'] - $nomencladorfacturado['monto_debito'], 2, ",", ".") ?></td>
        </tr>
        <?php
        $sumaparaelresumendefactura['monto_prefactura']+=$nomencladorfacturado['monto'];
        $sumaparaelresumendefactura['cant_practicas']+=$nomencladorfacturado['cantidad'];
        $sumaparaelresumendefactura['cant_practicas_deb']+=$nomencladorfacturado['cantidad_debitados'];
        $sumaparaelresumendefactura['monto_deb']+=$nomencladorfacturado['monto_debito'];
    }
    ?>
    <tr style="background-color: <?php echo $otrocolorstyle ?>;border:#000000 solid thin">
        <td colspan="2" style="border:#000000 solid thin">
            Cantidad Total<br/>
            <?php echo $sumaparaelresumendefactura['cant_practicas'] ?>
        </td>
        <td  colspan="2" style="border:#000000 solid thin">
            Monto Total Efectuado<br/> $ <?php echo number_format($sumaparaelresumendefactura['monto_prefactura'], 2, ",", ".") ?>
        </td>
        <td  colspan="2" style="border:#000000 solid thin">
            Cantidad Rechazado<br/> <?php echo $sumaparaelresumendefactura['cant_practicas_deb'] ?>
        </td>
        <td  colspan="2" style="border:#000000 solid thin">
            Monto Total Rechazado<br/> $ <?php echo number_format($sumaparaelresumendefactura['monto_deb'], 2, ",", ".") ?>
        </td>
    </tr>
    <?php
    die();
}

if ($_POST['debitar']) {
    $total_debitado_aux = $_POST['total_debitado'];
    $exprelac_aux = $_POST['exprelacaldebito'];
    $elexpediente_aux = $_POST['expedientebuscado'];
    $descripcion_aux = $_POST['descripcion_deb'];
    $cuie_aux = $_POST['cuie_deb'];
    $querydebitar = "INSERT INTO facturacion.debito_auditoria (nro_exp,descripcion,monto,usuario,nro_exp_relac,fecha,cuie)
                     VALUES('$elexpediente_aux','$descripcion_aux','$total_debitado_aux','$usuario[0]','$exprelac_aux','$fecha_hoy','$cuie_aux')";
    sql($querydebitar) or die;
}

if ($_POST['acreditar']) {
    $total_debitado_aux = $_POST['total_acreditado'];
    $exprelac_aux = $_POST['exprelacalcredito'];
    $elexpediente_aux = $_POST['expedientebuscado'];
    $descripcion_aux = $_POST['descripcion_cred'];
    $cuie_aux = $_POST['cuie_cred'];
    $queryacreditar = "INSERT INTO facturacion.credito_auditoria (nro_exp,descripcion,monto,usuario,nro_exp_relac,fecha,cuie)
                     VALUES('$elexpediente_aux','$descripcion_aux','$total_debitado_aux','$usuario[0]','$exprelac_aux','$fecha_hoy','$cuie_aux')";
    sql($queryacreditar) or die;
}

if ($_POST['observar']) {
    $elexpediente = $_POST['expedientebuscado'];
    $descripcion = $_POST['observacion'];
    $cuie_obs = $_POST['cuie_obs'];
    $query = "INSERT INTO facturacion.observaciones (nro_exp,cuie,observacion,usuario,fecha)
                     VALUES('$elexpediente','$cuie_obs','$descripcion','$usuario[0]','$fecha_hoy')";
    sql($query) or die;
}

if ($_POST['debito']) {
    $tablaseleccionada = $_POST['tablaseleccionada'];
    $total_debitado[$tablaseleccionada] = $_POST['debito'];
    $exprelac[$tablaseleccionada] = $_POST['exprelac'];
    $desc[$tablaseleccionada] = $_POST['descripcion_deb'];
}

if ($_POST['credito']) {
    $tablaseleccionada = $_POST['tablaseleccionada'];
    $total_acreditado[$tablaseleccionada] = $_POST['credito'];
    $exprelacalcredito[$tablaseleccionada] = $_POST['exprelacalcredito'];
    $desc[$tablaseleccionada] = $_POST['descripcion_cred'];
}

if ($_POST['observacion']) {
    $tablaseleccionada = $_POST['tablaseleccionada'];
    $descobs[$tablaseleccionada] = $_POST['descripcion'];
}

if ($_POST['quitar_debito']) {
    $exprelac_aux = $_POST['exprelac'];
    $cuie_aux = $_POST['cuie_deb'];
    $queryquitardebito = "DELETE FROM facturacion.debito_auditoria
                      WHERE cuie='$cuie_aux' 
                        AND nro_exp_relac='$exprelac_aux'
                        AND nro_exp='$elexpediente'";
    sql($queryquitardebito) or die;
}

if ($_POST['quitar_credito']) {
    $exprelac_aux = $_POST['exprelac'];
    $cuie_aux = $_POST['cuie_deb'];
    $queryquitarcredito = "DELETE FROM facturacion.credito_auditoria
                        WHERE cuie='$cuie_aux' 
                        AND nro_exp_relac='$exprelac_aux'
                        AND nro_exp='$elexpediente'";
    sql($queryquitarcredito) or die;
}

if ($_POST['quitar_observacion']) {
    $cuie_obs = $_POST['cuie_obs'];
    $queryquitarobs = "DELETE FROM facturacion.observaciones
                          WHERE cuie='$cuie_obs' 
                          AND nro_exp='$elexpediente'";
    sql($queryquitarobs) or die;
}

echo $html_header;
?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<link rel='stylesheet' href='../../lib/jquery/ui/jquery-ui.css'/>
<script src='../../lib/jquery/ui/jquery.ui.datepicker-es.js' type='text/javascript'></script>
<script src='funcionesDebitosRetro.js' type='text/javascript'></script>
<link rel='stylesheet' type='text/css' href='../../lib/css/general.css'/>
<link rel='stylesheet' type='text/css' href='../../lib/css/estilosFacturacion.css'/>
<link rel='stylesheet' type='text/css' href='../../lib/css/sprites.css'>
<!--[if IE]>
    <link rel="stylesheet" type="text/css" href="../../lib/css/general.IE.css" />
<![endif]-->

<script>
    var img_ext = '<?= $img_ext = '../../imagenes/rigth2.gif' ?>';//imagen extendido
    var img_cont = '<?= $img_cont = '../../imagenes/down2.gif' ?>';//imagen contraido

    function muestra_tabla_facturas(nro) {
        oimg = $("#imagen_ver_factura" + nro);//objeto tipo IMG

        var obj = $(".efector_" + nro);
        var otroobj = $("#totalapagar_" + nro);
        if (obj.css("display") == 'none') {
            obj.css("display", "inline-table");
            otroobj.css("display", "inline-table");
            oimg.attr('src', img_cont);

        } else {
            obj.css("display", "none");
            otroobj.css("display", "none");
            oimg.attr('src', img_ext);
        }
    }

    function muestra_tabla_practicas(nro) {
        oimg = $("#imagen_ver_practicas" + nro);//objeto tipo IMG        
        var obj = $(".factura_" + nro);


        if (obj.css("display") == 'none') {
            $.post("listado_expedientes.php", {consultar_practicas: $(oimg).attr('id_factura')}, function(data) {
                $(obj).find('tbody').html(data);
                obj.css("display", "inline-table");
                oimg.attr('src', img_cont);
            });
        } else {
            obj.css("display", "none");
            // $("#imagen_ver_"+nro)
            oimg.attr('src', img_ext);
        }
    }

    function ingresarDebito(nro)
    {
<? if ($estimulocalculado[0]) { ?>
            alert('Expediente Cerrado, no se pueden ingresar debitos.');
            return false;
<? } ?>
        var debito = prompt("Ingrese el Monto del Debito", "0");
        if (debito != null && debito != 0)
        {
            var exprelacaldebito = prompt("Ingrese el Nro. del Exp. Relacionado", "");

            var desc = prompt("Ingrese una Descripcion del Debito", "");
            var otroobj = $("#totalapagar_" + nro);
            var obj = $("#form_debito_" + nro);
            exp = $("#expedientebuscado").val();
            //alert('Exp Relac.:' + exprelacaldebito + ' Debito: ' + debito);
            $.post("listado_expedientes.php", {'descripcion_deb': desc, 'exprelac': exprelacaldebito, 'tablaseleccionada': nro, 'expedientebuscado': exp, 'debito': debito, buscar_expediente: "Buscar Expediente"}, function(data) {
                var audito = $(data).find("#form_debito_" + nro);
                obj.empty();
                obj.append(audito);
                var totalapagar = $(data).find("#totalapagar_" + nro + " tbody");
                otroobj.empty();
                otroobj.append(totalapagar);
                $('#debitar_' + nro).removeAttr('disabled');
                var totalexp = $(data).find('#totaldelexpediente tbody');
                $('#totaldelexpediente').empty();
                $('#totaldelexpediente').append(totalexp);
            });
        }
    }

    function ingresarCredito(nro)
    {
<? if ($estimulocalculado[0]) { ?>
            alert('Expediente Cerrado, no se pueden ingresar creditos.');
            return false;
<? } ?>
        var credito = prompt("Ingrese el Monto del Credito", "0");
        if (credito != null && credito != 0)
        {
            var exprelacalcredito = prompt("Ingrese el Nro. del Exp. Relacionado", "");
            var desc = prompt("Ingrese una Descripcion del Credito", "");
            var otroobj = $("#totalapagar_" + nro + " tbody");
            var obj = $("#form_credito_" + nro);
            exp = $("#expedientebuscado").val();
            $.post("listado_expedientes.php", {'descripcion_cred': desc, 'exprelacalcredito': exprelacalcredito, 'tablaseleccionada': nro, 'expedientebuscado': exp, 'credito': credito, buscar_expediente: "Buscar Expediente"}, function(data) {
                var audito = $(data).find("#form_credito_" + nro);
                obj.empty();
                obj.append(audito);
                var totalapagar = $(data).find("#totalapagar_" + nro + " tbody");
                otroobj.empty();
                otroobj.append(totalapagar);
                $('#acreditar_' + nro).removeAttr('disabled');
                var totalexp = $(data).find('#totaldelexpediente tbody');
                $('#totaldelexpediente').empty();
                $('#totaldelexpediente').append(totalexp);
            });
        }
    }

    function ingresarObs(nro)
    {
<? if ($estimulocalculado[0]) { ?>
            alert('Expediente Cerrado, no se pueden ingresar Observaciones.');
            return false;
<? } ?>
        var desc = prompt("Ingrese la Observación", "");
        if (desc != null && desc != '')
        {
            var obj = $("#form_obs_" + nro);
            exp = $("#expedientebuscado").val();
            $.post("listado_expedientes.php", {'descripcion': desc, 'tablaseleccionada': nro, 'expedientebuscado': exp, observacion: "observacion", buscar_expediente: "Buscar Expediente"}, function(data) {
                var obs = $(data).find("#form_obs_" + nro);
                obj.empty();
                obj.append(obs);
                $('#observar_' + nro).removeAttr('disabled');
            });
        }
    }

    function quitar_debito(nro, cuie, exp_relac)
    {
        var otroobj = $("#totalapagar_" + nro + " tbody");
        var obj = $("#form_debito_" + nro);
        var exp = $("#expedientebuscado").val();
        $.post("listado_expedientes.php", {'cuie_deb': cuie, 'exprelac': exp_relac, 'tablaseleccionada': nro, 'expedientebuscado': exp, quitar_debito: "quitar_debito", buscar_expediente: "Buscar Expediente"}, function(data) {
            var audito = $(data).find("#form_debito_" + nro);
            obj.empty();
            obj.append(audito);
            var totalapagar = $(data).find("#totalapagar_" + nro + " tbody");
            otroobj.empty();
            otroobj.append(totalapagar);
            var totalexp = $(data).find('#totaldelexpediente tbody');
            $('#totaldelexpediente').empty();
            $('#totaldelexpediente').append(totalexp);
        });
    }

    function quitar_observacion(nro, cuie)
    {
        var obj = $("#form_obs_" + nro);
        var exp = $("#expedientebuscado").val();
        $.post("listado_expedientes.php", {'cuie_obs': cuie, 'tablaseleccionada': nro, 'expedientebuscado': exp, quitar_observacion: "quitar_observacion", buscar_expediente: "Buscar Expediente"}, function(data) {
            var audito = $(data).find("#form_obs_" + nro);
            obj.empty();
            obj.append(audito);
        });
    }

</script>
<?php
if (is_null($expediente)) {
    $mostrar = false;
    if ($_POST['buscar_expediente']) {
        $msje = "<b>Expediente Nro. : </b>" . $expedientebuscado . " no encontrado";
    }
} else {
    if ($permiso) {
        $mostrar = true;
    } elseif ($tienePago && $estadoPago == 'C') {
        $mostrar = true;
    } else {
        $mostrar = false;
        $msje = "<b>El expediente se encuentra en proceso</b>";
    }
}
if ($_POST['buscar_expediente'] && $_POST['expedientebuscado']!="") {
    if($permisoDebitoRet && $expediente->getEstado() == 'A'){
        $mostrarBtnDbtRet = true;
    }
}

include_once './listado_expedientes.tpl.php';
?>