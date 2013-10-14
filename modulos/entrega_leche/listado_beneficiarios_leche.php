<?php
require_once("../../config.php");
require_once("../../lib/bibliotecaTraeme.php");

variables_form_busqueda("listado_beneficiarios_leche");

$fecha_hoy = date("Y-m-d H:i:s");
$fecha_hoy = fecha($fecha_hoy);

if ($_POST['buscar']) {
    $keyword = $_POST['keyword'];
    if ($_POST['filter'] == 'DNI') {
        $aux_where = "WHERE c ILIKE '%" . $keyword . "%'";
    } elseif ($_POST['filter'] == 'APELLIDO') {
        $aux_where = "WHERE a ILIKE '%" . $keyword . "%'";
    } else {
        $aux_where = "WHERE c ILIKE '%" . $keyword . "%' OR a ILIKE '%" . $keyword . "%'";
    }
    $sql = "select * from (
                select 
                uad.beneficiarios.clave_beneficiario as id,
                trim(uad.beneficiarios.apellido_benef||' '||CASE WHEN uad.beneficiarios.apellido_benef_otro is null THEN '' else uad.beneficiarios.apellido_benef_otro end) as a,
                trim(uad.beneficiarios.nombre_benef||' '||CASE WHEN uad.beneficiarios.nombre_benef_otro is null THEN '' else uad.beneficiarios.nombre_benef_otro end) as b,
                trim(uad.beneficiarios.numero_doc) as c,
                uad.beneficiarios.fecha_nacimiento_benef as d,
                uad.beneficiarios.calle as e,
                uad.beneficiarios.localidad as h,
                uad.beneficiarios.id_beneficiarios as planilla
                from uad.beneficiarios
            )as cc
            " . $aux_where . "
            ORDER BY a ASC
            LIMIT 50 OFFSET 0";

    $result = sql($sql) or die;
}

echo $html_header;
?>
<link rel=stylesheet type='text/css' href='../../lib/css/general.css'>
<link rel=stylesheet type='text/css' href='../../lib/css/sprites.css'>
<!--[if IE]>
    <link rel="stylesheet" type="text/css" href="../../lib/css/general.IE.css?333" />
<![endif]-->

<form name=form1 action="listado_beneficiarios_leche.php" method=POST>
    <table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
        <tr>
            <td align=center>
                <input type=hidden name=form_busqueda value=1>
                <b>Buscar:&nbsp</b>
                <input type='text' name='keyword' value='<?= $_POST['keyword'] ?>' size=20 maxlength=150>
                <b>&nbsp;en:&nbsp;</b>
                <select name='filter'>&nbsp
                    <option value='all'>Todos los campos</option>
                    <option value='dni'>DNI</option>
                    <option value='apellido'>APELLIDO</option>
                </select>

                &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
            </td>
        </tr>
    </table>


    <table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?= $bgcolor3 ?>' align=center>
        <tr>
            <td colspan=12 align=left id=ma>
                <table width=100%>
                    <tr id=ma>
                        <td width=30% align=left><b>Total:</b> <?= $total_muletos ?></td>       
                        <td width=40% align=right><?= $link_pagina ?></td>
                    </tr>
                </table>

            </td>
        </tr>


        <tr>
            <td align=right id=mo>
                <a id=mo >Apellido</a>
            </td>      	
            <td align=right id=mo>
                <a id=mo >Nombre</a>
            </td>
            <td align=right id=mo>
                <a id=mo >DNI</a>
            </td>
            <td width=10% align=right id=mo>
                <a id=mo >Fecha Nacimiento</a>
            </td>
            <td align=right id=mo>
                <a id=mo >Domicilio</a>
            </td>        
            <td align=right id=mo>
                <a id=mo >Localidad</a>
            </td> 
            <td width=6% align=right id=mo>Estado en Padron</td>
            <td width=4% align=right id=mo>Clasif R+R</td>   
            <td id=mo>&nbsp;</td>
        </tr>
        <?
        if (($_POST['buscar']) && ($result->RecordCount() > 0)) {
            while (!$result->EOF) {
                if ($colordefondo == '#CFE8DD') {
                    $colordefondo = '#AFE8DD';
                } else {
                    $colordefondo = '#CFE8DD';
                }
                $ref = encode_link("../facturacion/comprobante_admin_total.php", array("clavebeneficiario" => $result->fields['id'], "entidad_alta" => 'in', "pagina_listado" => "listado_beneficiarios_leche.php"));
                $onclick_elegir = "location.href='$ref'";
                // seteo la clase css segun su estado en Padron
                    $activoenpadron = estadoDelAfiliado($result->fields['id']);
                    if ($activoenpadron > 0) {
                        $classP = "icon-check-alt";
                        $titleP = "Beneficiario Activo";
                    } elseif ($activoenpadron == 0) {
                        $classP = "icon-x-altx-alt";
                        $titleP = "Beneficiario Inactivo";
                    } else {
                        $classP = "icon-minus-alt";
                        $titleP = "Beneficiario No Empadronado";
                    }
                // seteo la clase css segun su estado en RR
                    if (clasificacionPorClaveBeneficiario($result->fields['id'])) {
                        $classRR = "icon-check-alt";
                        $titleRR = "Beneficiario Activo";
                    } else {
                        $classRR = "icon-x-alt";
                        $titleRR = "Beneficiario Inactivo";
                    }
                // seteo link de modificacion a pagina de incripcion
                    $lnkInscripcion = encode_link("../inscripcion/ins_admin.php", array("id_planilla" => $result->fields['planilla'], 
                                                                                        "tapa_ver" => 'block'));
                ?>
                <tr <?= atrib_tr($colordefondo) ?>>     
                    <td onclick="<?= $onclick_elegir ?>"><?= $result->fields['a'] ?></td>
                    <td onclick="<?= $onclick_elegir ?>"><?= $result->fields['b'] ?></td>
                    <td onclick="<?= $onclick_elegir ?>"><?= $result->fields['c'] ?></td>     
                    <td onclick="<?= $onclick_elegir ?>"><?= Fecha($result->fields['d']) ?></td> 
                    <td onclick="<?= $onclick_elegir ?>"><?= $result->fields['e'] ?></td>
                    <td onclick="<?= $onclick_elegir ?>"><?= $result->fields['h'] ?></td>
                    <td align="center">
                        <div class="sprite-gral <?php echo $classP; ?>" title="<?php echo $titleP; ?>"></div>
                    </td> 
                    <td align="center">
                        <div class="sprite-gral <?php echo $classRR; ?>" title="<?php echo $titleRR; ?>"></div>
                    </td>
                    <td align=center>
                        <a class="sprite-gral icon-profile"
                           title="Modificar - Ir a la Planilla de Inscripcion"
                           href="<?php echo $lnkInscripcion; ?>" ></a>
                    </td>
                </tr>
                <?
                $result->MoveNext();
            }
        }
        ?>
    </table>
    <p>&nbsp;</p>
    <!-- cuadro de referencia -->
    <div class="referencia" id="position-fixed-bottom">
        <div>
            <span class="sprite-gral icon-check-alt"></span>
            <label>Beneficiario Activo</label>
        </div>
        <div>
            <span class="sprite-gral icon-x-altx-alt"></span>
            <label>Beneficiario Inactivo</label>
        </div>
        <div>
            <span class="sprite-gral icon-minus-alt"></span>
            <label>Beneficiario No Empadronado</label>
        </div>
    </div>
    <br style="clear: both;">
</form>
</body>
</html>
<?
echo fin_pagina(); // aca termino ?>