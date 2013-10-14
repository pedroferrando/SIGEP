<?php
require_once("../../config.php");
require_once("../../lib/funciones_misiones.php");

$cuie = $parametros['cuie'] or $cuie = $_POST['cuie'];
$id_factura = $parametros['id_factura'] or $id_factura = $_POST['id_factura'];
$periodo_prestacion = $parametros['periodo_prestacion'] or $periodo_prestacion = $_POST['periodo_prestacion'];
$tipo_nomenclador = $parametros['tipo_nomenclador'];
$img_ext = '../../imagenes/rigth2.gif';
$img_cont = '../../imagenes/down2.gif';

$sql_parametro = "select valor from nacer.parametros where parametro='periodo_vinculacion_efectores'";
$res_parametro = sql($sql_parametro, "Error") or fin_pagina();
$res_parametro = $res_parametro->fields['valor'];

$res_parametro1 = 90;

$sql_fecha_factura = "select fecha_factura from facturacion.factura where id_factura='$id_factura'";
$res_fecha_factura = sql($sql_fecha_factura, "Error") or fin_pagina();
$res_fecha_factura = $res_fecha_factura->fields['fecha_factura'];

if ($parametros["vincula_compro"] == "ok") {
    $id_comprobante = $parametros["id_comprobante"];
    $fecha_carga = date("Y-m-d");
    $db->StartTrans();

    $query = "update facturacion.comprobante set id_factura='$id_factura'
   			where id_comprobante=$id_comprobante";
    sql($query, "Error al vincular comprobante") or fin_pagina();

    $total_factura = montoFactura($id_factura);
    actualizarMontoFactura($id_factura, $total_factura['total']);

    /* cargo los log */
    $usuario = $_ses_user['name'];
    $log = "insert into facturacion.log_factura
		   (id_factura, fecha, tipo, descripcion, usuario) 
	values ($id_factura, '$fecha_carga','Vinculacion','Se vinculo comrobante: $id_comprobante', '$usuario')";
    sql($log) or fin_pagina();

    $db->CompleteTrans();
    $accion = "Se Vinculo el Comprobante $id_comprobante a la Factura $id_factura";
}

if ($_POST["vincular_todo"] == "Vincular Todo") {
    $fecha_carga = date("Y-m-d");
    $db->StartTrans();

    $query = "update facturacion.comprobante set id_factura='$id_factura' ";

//    if (es_cuie($_ses_user['login']) && ($res_parametro == 'si')) {
    $anio = substr($periodo_prestacion, 0, 4);
    $mes = substr($periodo_prestacion, 5, 2);
    $fecha_desde = ereg_replace('/', '-', $periodo_prestacion) . '-01';
    $fecha_hasta = ereg_replace('/', '-', $periodo_prestacion) . '-' . ultimoDia($mes, $anio);
    $query.=" where id_comprobante in " . $_POST['todosloscomprobantesaptos'];
//    } else {
//        $query.=" where comprobante.id_factura is null and comprobante.cuie='$cuie' and marca=0 and (comprobante.activo='S' or comprobante.activo is NULL) and ((comprobante.fecha_comprobante + '$res_parametro1 days') > '$res_fecha_factura') ";
//    }
    sql($query, "Error al vincular comprobante") or fin_pagina();

    /* cargo los log */
    $usuario = $_ses_user['name'];
    $log = "insert into facturacion.log_factura
		   (id_factura, fecha, tipo, descripcion, usuario) 
	values ($id_factura, '$fecha_carga','Vinculacion','Vinculacion Masiva de Comprobantes', '$usuario')";
    sql($log) or fin_pagina();

    $db->CompleteTrans();
    $ref = encode_link("factura_admin.php", array("id_factura" => $id_factura));
}

variables_form_busqueda("listado_comp_fact");

$orden = array(
    "default" => "1",
    "1" => "fecha_comprobante",
    "2" => "clavebeneficiario",
    "3" => "id_comprobante"
);
$filtro = array(
    "afidni" => "DNI",
    "afiapellido" => "Apellido",
    "afinombre" => "Nombre"
);
$sql_tmp = "select * 
	    from facturacion.comprobante
	    left join facturacion.smiefectores using (cuie) ";

$anio = substr($periodo_prestacion, 0, 4);
$mes = substr($periodo_prestacion, 5, 2);
$fecha_desde = ereg_replace('/', '-', $periodo_prestacion) . '-01';
$fecha_hasta = ereg_replace('/', '-', $periodo_prestacion) . '-' . ultimoDia($mes, $anio);

$sql_tmp .= " where comprobante.id_factura is null 
              and comprobante.cuie='$cuie' 
              and comprobante.tipo_nomenclador='$tipo_nomenclador'
              and marca=0 and periodo='$periodo_prestacion'
              and fecha_comprobante between '$fecha_desde' and '$fecha_hasta'";

echo $html_header;
echo "<script src='../../lib/jquery.min.js' type='text/javascript'></script>";
?>

<script>
    var img_ext = '<?= $img_ext ?>';//imagen extendido
    var img_cont = '<?= $img_cont ?>';//imagen contraido

    function muestra_tabla(obj_tabla) {
        oimg = eval(obj_tabla);//objeto tipo IMG
        if (obj_tabla.style.display == 'none') {
            obj_tabla.style.display = 'inline';
            oimg.show = 0;
            oimg.src = img_ext;
        }
        else {
            obj_tabla.style.display = 'none';
            oimg.show = 1;
            oimg.src = img_cont;
        }
    }

    function confirmartodo() {
        if ($('#cantidaddecomprobantesaptos').val() != 0) {
            return confirm('¿Esta seguro que desea vincular TODO?');
        } else {
            alert('No hay Comprobantes aptos para facturar');
            return false;
        }
    }
</script>

<form name=form1 action="listado_comp_fact.php" method=POST>
    <input type="hidden" name="cuie" value="<?= $cuie ?>">
    <input type="hidden" name="id_factura" value="<?= $id_factura ?>">
    <input type="hidden" name="periodo_prestacion" value="<?= $periodo_prestacion ?>">
    <? echo "<center><b><font size='+1' color='red'>$accion</font></b></center>"; ?>


    <? $result = sql($sql_tmp) or die; ?>
    <center style="padding-top: 20px;font-size: large;color: black"><b>Comprobantes habilitados para la Factura en el Periodo <?= $mes . "/" . $anio ?></b></center>
    <table border=0 width=98% cellspacing=2 cellpadding=2 bgcolor='<?= $bgcolor3 ?>' align=center>
        <tr>
            <td colspan=10 align=left id=ma>
                <table width=100%>
                    <tr id=ma>
                        <td width=30% align=left style="font-size: x-small;color: red">Presione sobre el comprobante que desea asociar a la factura.</td> 
                    </tr>
                    <tr id=ma>
                        <td width=30% align=left style="font-size: x-small;color: red">Presione aqui para asociar todos <input type="submit" value="Vincular Todo" name="vincular_todo" onclick="return confirmartodo()"></td> 
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td align=right id=mo><a id=mo>Nro Comp</a></td>      	
            <td align=right id=mo><a id=mo>Apellido</a></td>  	
            <td align=right id=mo><a id=mo>Nombre</a></td>
            <td align=right id=mo><a id=mo>DNI</a></td>
            <td align=right id=mo><a id=mo>Fecha Prestación</a></td>       
            <td align=right id=mo><a id=mo>Clave Beneficiario</a></td>
            <td align=right id=mo>Total Prestaciones</td>
        </tr>
        <?
        $todosloscomprobantesnoaptos = null;
        $todosloscomprobantesaptos = "(";
        $cantidaddecomprobantesaptos = 0;
        $cantidaddecomprobantesnoaptos = 0;
        while (!$result->EOF) {
            $sepuede = sePuedeAgregarComprobante($cuie, $result->fields['id_comprobante'], $result->fields['idperiodo'], $result->fields['fecha_comprobante'], $result->fields['clavebeneficiario'], $cantidadprestacion);
            if ($sepuede[0]) {
                $ref = encode_link("listado_comp_fact.php", array("id_comprobante" => $result->fields['id_comprobante'], "id_factura" => $id_factura, "cuie" => $cuie, "periodo_prestacion" => $periodo_prestacion, "tipo_nomenclador" => $tipo_nomenclador, "vincula_compro" => "ok"));
                $onclick_elegir = "location.href='$ref'";
                if ($colordefondo == '#CFE8DD') {
                    $colordefondo = '#AFE8DD';
                } else {
                    $colordefondo = '#CFE8DD';
                }
                ?>
                <tr <?= atrib_tr($colordefondo) ?>>    
                    <td style="border-bottom: solid thin black" onclick="if (confirm('Esta Seguro que desea Vincular el Comprobante seleccionado')) {<?= $onclick_elegir ?>
                }"><?= $result->fields['id_comprobante'] ?></td>
                    <td style="border-bottom: solid thin black" onclick="if (confirm('Esta Seguro que desea Vincular el Comprobante seleccionado')) {<?= $onclick_elegir ?>
                }"><?= $sepuede['beneficiario']['afiapellido'] ?></td>
                    <td style="border-bottom: solid thin black" onclick="if (confirm('Esta Seguro que desea Vincular el Comprobante seleccionado')) {<?= $onclick_elegir ?>
                }"><?= $sepuede['beneficiario']['afinombre'] ?></td>
                    <td style="border-bottom: solid thin black" onclick="if (confirm('Esta Seguro que desea Vincular el Comprobante seleccionado')) {<?= $onclick_elegir ?>
                }"><?= $sepuede['beneficiario']['afidni'] ?></td>     
                    <td style="border-bottom: solid thin black" onclick="if (confirm('Esta Seguro que desea Vincular el Comprobante seleccionado')) {<?= $onclick_elegir ?>
                }"><?= Fecha($result->fields['fecha_comprobante']) ?></td>             
                    <td style="border-bottom: solid thin black" onclick="if (confirm('Esta Seguro que desea Vincular el Comprobante seleccionado')) {<?= $onclick_elegir ?>
                }"><?= $sepuede['beneficiario']['clavebeneficiario'] ?></td> 
                    <td style="border-bottom: solid thin black" onclick="if (confirm('Esta Seguro que desea Vincular el Comprobante seleccionado')) {<?= $onclick_elegir ?>
                }"><?= $cantidadprestacion ?></td> 
                </tr>
                <?
                if ($cantidaddecomprobantesaptos > 0) {
                    $todosloscomprobantesaptos.=',';
                }
                $todosloscomprobantesaptos = $todosloscomprobantesaptos . $result->fields['id_comprobante'];
                $cantidaddecomprobantesaptos++;
            } else {
                $todosloscomprobantesnoaptos[$cantidaddecomprobantesnoaptos]['id_comprobante'] = $result->fields['id_comprobante'];
                $todosloscomprobantesnoaptos[$cantidaddecomprobantesnoaptos]['motivo'] = $sepuede[1];
                $cantidaddecomprobantesnoaptos++;
            }
            $result->MoveNext();
        }
        $todosloscomprobantesaptos.=")";
        ?>
    </table>
    <div align=right>
        <b>Total de Registros: </b><?= $cantidaddecomprobantesaptos ?>
    </div>
    <input type="hidden" id="cantidaddecomprobantesaptos" name="cantidaddecomprobantesaptos" value="<?= $cantidaddecomprobantesaptos ?>">
    <input type="hidden" name="todosloscomprobantesaptos" value="<?= $todosloscomprobantesaptos ?>">

    <? if ($todosloscomprobantesnoaptos) { ?>
        <div align=center style="margin-top: 20px">
            <b style="font-size: large;color: #D00000">Comprobantes No Habilitados</b>
        </div>

        <table border=0 width=98% cellspacing=2 cellpadding=2 bgcolor='<?= $bgcolor3 ?>' align=center>
            <tr>
                <td width=3% align=right id=mo><a id=mo>Nro Comp</a></td>      	
                <td width=10% align=right id=mo><a id=mo>Apellido</a></td>  	
                <td width=10% align=right id=mo><a id=mo>Nombre</a></td>
                <td width=5% align=right id=mo><a id=mo>DNI</a></td>
                <td width=5% id=mo><a id=mo>Fecha Prestación</a></td>       
                <td width=10% align=right id=mo><a id=mo>Clave Beneficiario</a></td>
                <td width=3% align=right id=mo>Total Prestaciones</td>
                <td width=20% align=right id=mo>Motivo</td>
            </tr>
            <?
            foreach ($todosloscomprobantesnoaptos as $uncomprobantenoapto) {

                $sql_no_aptos = "select * from facturacion.comprobante
            left join facturacion.smiefectores using (cuie) 
            WHERE id_comprobante =" . $uncomprobantenoapto['id_comprobante'] . "
            ORDER BY fecha_comprobante ASC";

                $result_noaptos = sql($sql_no_aptos);

                $beneficiario = datosAfiliadoEnVigente($result_noaptos->fields['clavebeneficiario']);
                $cantidad = cantidadDePrestaciones($result_noaptos->fields['id_comprobante']);
                if ($colordefondo == '#CFE8DD') {
                    $colordefondo = '#AFE8DD';
                } else {
                    $colordefondo = '#CFE8DD';
                }
                ?>
                <tr <?= atrib_tr($colordefondo) ?>>     
                    <td style="border-bottom: solid thin black" ><?= $result_noaptos->fields['id_comprobante'] ?></td>
                    <td style="border-bottom: solid thin black" ><?= $beneficiario['afiapellido'] ?></td>
                    <td style="border-bottom: solid thin black" ><?= $beneficiario['afinombre'] ?></td>
                    <td style="border-bottom: solid thin black" ><?= $beneficiario['afidni'] ?></td>     
                    <td style="border-bottom: solid thin black" ><?= Fecha($result_noaptos->fields['fecha_comprobante']) ?></td>             
                    <td style="border-bottom: solid thin black" ><?= $result_noaptos->fields['clavebeneficiario'] ?></td> 
                    <td style="border-bottom: solid thin black" ><?= $cantidad ?></td> 
                    <td style="border-bottom: solid thin black" ><?= $uncomprobantenoapto['motivo'] ?></td> 
                </tr>
                <?
            }
            ?>
        </table> 
        <table align=center width=100%>
            <tr>
                <td>
                    <div  align=right >
                        <b>Total de Registros: </b><?= $cantidaddecomprobantesnoaptos ?>
                    </div>
                </td>
            </tr>
        </table>
    <? } ?>
    <table style="padding-top: 20px"cellspacing=2 cellpadding=2 border=0 width=100% align=center>
        <tr>
            <td align=center>
                <? //list($sql, $total_muletos, $link_pagina, $up) = form_busqueda($sql_tmp, $orden, $filtro, $link_tmp, $where_tmp, "buscar");   ?>
                &nbsp;&nbsp;<!--input type=submit name="buscar" value='Buscar'-->
                <? $ref = encode_link("factura_admin.php", array("id_factura" => $id_factura)); ?>
                &nbsp;&nbsp;&nbsp;&nbsp;<input style="width:130px;height: 28px;"type="button" name="volver" value='Volver a la Factura' onclick="window.opener.location.href = '<?= $ref ?>';
        window.close();" >
            </td>
        </tr>
    </table>
</form>
<br />
<br />
<?
echo fin_pagina(); // aca termino ?>