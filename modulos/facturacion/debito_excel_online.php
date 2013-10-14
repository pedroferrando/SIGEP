<?php
require_once ("../../config.php");
require_once("../../lib/funciones_misiones.php");
require_once("../../lib/bibliotecaTraeme.php");

$func_nroFactura = nro_factura_misiones();

if ($func_nroFactura) {
    $nroF = NRO_FACTURA_MISIONES;
} else {
    $nroF = '';
}

$id_factura = $parametros["id_factura"];
$estado = isset($parametros["estado"]) ? $parametros["estado"] : '';

/**
 * Consulta las pracitcas en debito
 * * */
$query = "SELECT   *
        FROM  facturacion.debito d 
        LEFT JOIN facturacion.comprobante using(id_comprobante)
        where d.id_factura='$id_factura'";

$result = $db->Execute($query) or die($db->ErrorMsg());

$query = "SELECT $nroF *
FROM  facturacion.factura
left JOIN facturacion.recepcion r on r.idrecepcion=facturacion.factura.recepcion_id
  left join facturacion.smiefectores using (cuie) 
  where id_factura='$id_factura' ";

$result1 = $db->Execute($query) or die($db->ErrorMsg());

$querysolonomenclador = "SELECT modo_facturacion,c.id_nomenclador_detalle 
                        FROM facturacion.comprobante c
			INNER JOIN facturacion.prestacion p using (id_comprobante)
                        INNER JOIN facturacion.nomenclador_detalle d on c.id_nomenclador_detalle=d.id_nomenclador_detalle
                        where id_factura='$id_factura' and p.id_nomenclador > 0";
$resultnomenclador = $db->Execute($querysolonomenclador) or die($db->ErrorMsg());
$modo_facturacion = $resultnomenclador->fields['modo_facturacion'];

echo $html_header;

echo "<script src='../../lib/jquery.min.js' type='text/javascript'></script>";
?>

<script>
    var img_ext='<?= $img_ext = '../../imagenes/rigth2.gif' ?>';//imagen extendido
    var img_cont='<?= $img_cont = '../../imagenes/down2.gif' ?>';//imagen contraido
    
    function muestra_tabla(obj_tabla){
        var obj=$(".tr"+obj_tabla);
        if (obj.css("display")=='none'){
            obj.css("display","table-row");
        }else{
            obj.css("display","none");
        }
    }

</script>

<form name=form1 method=post action="debito_excel.php">

    <table width="100%" align=center cellspacing="0" cellpadding="5" class="bordes">

        <tr>
            <td align=center>
                <img src="../../imagenes/membrete_debito_credito.JPG">
            </td>
        </tr>

        <tr id="sub_tabla">
            <td>
                <font face="arial" size="3"><b>
                    INFORME DE PRESTACIONES <?php
if ($estado == 'A')
    echo '<span style="color:red;">(FACTURA ABIERTA)</span>';
?> 
                </b></font>
            </td>
        <tr id="sub_tabla">
            <td>
                <font face="arial" size="3"><b>
                    <?php
                    if ($result1->fields['ctrl'] != "S")
                        echo "*** Resultado previo a controles ***";
                    ?> 
                </b></font>
            </td>
        </tr>
        <tr bgcolor="#D0EBE9">
            <td>
                <font face="arial" size="2"><b>NOTA DE AUTORIZADO/ NO AUTORIZADO</b></font>
            </td>
        </tr>
        <tr bgcolor="#D0EBE9">
            <td>
                <font face="arial" size="2"><b>ATTE</b></font>
            </td>
        </tr>

        <tr>
            <td>
                <font face="arial" size="2"><b>Por medio de la Presente le informo el estado de saldo del siguiente Efector</b></font>
            </td>
        </tr>

        <tr>
            <td>
                <font face="arial" size="2">Efector: <b><?= $result1->fields['nombreefector'] ?></b></font>
            </td>
        </tr>
        <tr>
            <td>
                <font face="arial" size="2">Cuie: <b><?= $result1->fields['cuie'] ?></b></font>
            </td>
        </tr>

        <tr>
            <td>
                <font face="arial" size="2">Numero de Factura: <b><?=
                    $func_nroFactura ? $result1->fields['numero_factura'] : $result1->fields['id_factura'];
                    if ($result1->fields['recepcion_id'])
                        echo " (IMPORTADA) ";
                    $tipodefacturacion = substr($result1->fields['nombrearchivo'], 15, 1);
                    if ($tipodefacturacion == "R")
                        echo " (REFACTURACION) ";
                    ?> - [Fec. Factura: <? echo substr($result1->fields['fecha_factura'],0,10); ?>
                     / Registrada: <? echo substr($result1->fields['fecha_carga'],0,10); ?>]</b>
                </font>
            </td>
        </tr>

        <tr>
            <td>
                <? $periodo_ordenado = split("/", $result1->fields['periodo']) ?>
                <font face="arial" size="2">Mes Facturado: <b><?= $periodo_ordenado[1] . "/" . $periodo_ordenado[0] ?></b></font>
            </td>
        </tr>

        <tr>
            <td bgcolor="#f6bebe">
                <font face="arial" size="2">Importe Total Facturado: <b><?= number_format($result1->fields['monto_prefactura'], 2, ',', '.') ?></b></font>
            </td>
        </tr>

        <tr>
            <td>
                <? $query = " SELECT sum(cantidad*monto) as total FROM
  			facturacion.debito  			
  			where id_factura='$id_factura'";
                $result_t_debitado = $db->Execute($query) or die($db->ErrorMsg()); ?>

                <font face="arial" size="2">Total No Autorizado: <b><?= number_format($result_t_debitado->fields['total'], 2, ',', '.') ?></b></font>
            </td>
        </tr>
        <tr>
            <td bgcolor="Gray">
                <?
                $query = "SELECT (SELECT sum(p.precio_prestacion*p.cantidad) FROM  facturacion.factura f
                       INNER JOIN facturacion.comprobante c ON (f.id_factura = c.id_factura)
                       INNER JOIN facturacion.prestacion p ON (c.id_comprobante = p.id_comprobante)
                       WHERE f.id_factura=$id_factura)
                       -
                      (SELECT CASE WHEN SUM(monto*cantidad) IS NULL THEN 0 ELSE SUM(monto*cantidad) END AS total
				       FROM facturacion.debito 
				       WHERE id_factura=$id_factura) as total";

                $result_t_debitado = $db->Execute($query) or die($db->ErrorMsg());

                $total_a_pagar = $result_t_debitado->fields['total'];
                ?>
                <font face="arial" size="2">Total a Pagar: <b><?
                echo number_format($total_a_pagar, 2, ',', '.');
                ?></b></font>
            </td>
        </tr>
    </table>



    <?
    //Consulta todas las practicas que no tienen debito y las agrupa segun nomenclador
    //TODO: funcion traer practicas
    $q = "SELECT p.id_nomenclador,n.codigo,p.precio_prestacion precio,
                     sum(p.cantidad) cantidad,n.diagnostico,t.descripcion,c.grupo_etario
                        FROM facturacion.prestacion p
                        INNER JOIN facturacion.comprobante c USING (id_comprobante)
                        INNER JOIN facturacion.nomenclador n USING (id_nomenclador)
                        LEFT JOIN facturacion.anexo a USING (id_anexo)
                        LEFT JOIN nomenclador.patologias t on(t.codigo=n.diagnostico)
                        where p.id_prestacion NOT IN(SELECT id_prestacion 
                                                    FROM facturacion.debito 
                                                    WHERE id_factura='$id_factura')
                        AND c.id_factura='$id_factura'
                    GROUP BY p.id_nomenclador,n.codigo,p.precio_prestacion,n.diagnostico,t.descripcion,c.grupo_etario
                    ORDER BY n.codigo";

    $otro_result = sql($q);
    ?>
    <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5">
        <tr bgcolor="#d3d3cd">
            <td align="center" colspan="6" style="padding-top: 20px;"><b>Autorizado</b></td>
        </tr>
        <tr bgcolor=#C0C0FF>
            <td align=center >Nº</td>
            <td align=center >Codigo</td>
            <td align=center >Cantidad</td>
            <td align=center >Valor Unit</td>
            <td align=center >Valor Total</td>
        </tr>
        <?
        $aux = 1;
        if ($otro_result->RecordCount() != 0) {
            while (!$otro_result->EOF) {
                if ($bgcolor == '#DDD0EB') {
                    $bgcolor = '#ECDCFF';
                } else {
                    $bgcolor = '#DDD0EB';
                }
                $unit = $otro_result->fields['precio'];
                $cantidad = $otro_result->fields['cantidad'];
                //if ($otro_result->fields['grupo_etario']) {
                $grupo_etario = $otro_result->fields['grupo_etario'];
                $consultagrupoetario = "AND c.grupo_etario='$grupo_etario'";
                // } else {
                //    $consultagrupoetario = "AND c.grupo_etario is NULL";
                // }
                $total = $unit * $cantidad;

                $codigoarmado = $otro_result->fields['codigo'];
                $id_nomenclador = $otro_result->fields['id_nomenclador'];

                $consultanumero = "";
                $patologia = $otro_result->fields['descripcion']; //descripciondepatologia();
                if ($patologia) {
                    $codigomuestra = $codigoarmado . " " . $otro_result->fields['diagnostico'] . " - " . $patologia;
                } else {
                    $codigomuestra = $codigoarmado . " " . $otro_result->fields['diagnostico'];
                }

                $codigocss = "C" . $aux;
                ?>
                <tr bgcolor=<?= $bgcolor ?> onclick="return muestra_tabla(<?= "'" . $codigocss . "'" ?>);">
                    <td><?= $aux ?></td>
                    <td><?= $codigomuestra . " [" . descripcionDeCategoriaEtaria($grupo_etario) . "]" ?></td>     
                    <td><?= $cantidad ?></td>
                    <td><?= number_format($unit, 2, ',', '.') ?></td>
                    <td><?= number_format($total, 2, ',', '.') ?></td>
                </tr>
                <?
                //Consulta individualmente cada practica por nomenclador.

                $query = "SELECT c.id_comprobante,to_char(c.fecha_comprobante,'YYYY-MM-DD') as fecha_comprobante,p.precio_prestacion*p.cantidad suma,
                            s.afiapellido, s.afinombre, s.afidni, s.clavebeneficiario
                            FROM facturacion.prestacion p
                            LEFT JOIN facturacion.anexo a using (id_anexo)
                            INNER JOIN facturacion.comprobante c USING (id_comprobante)
                            INNER JOIN facturacion.nomenclador n on n.id_nomenclador=p.id_nomenclador
                            LEFT JOIN nacer.smiafiliados s USING(clavebeneficiario)
                            WHERE id_prestacion NOT IN(SELECT id_prestacion FROM facturacion.debito WHERE id_factura='$id_factura')
                            AND c.id_factura='$id_factura'
                            AND p.id_nomenclador='$id_nomenclador'
                            AND p.precio_prestacion='$unit'
                            " . $consultagrupoetario . "
                            " . $consultanumero;

                $result_detalle = $db->Execute($query) or die($db->ErrorMsg());
                ?>
                <tr style="display:none"  class="tr<?= $codigocss ?>" bgcolor=#COF0FF>
                    <td align=center >Nro. Beneficiario</td>
                    <td align=center >Apellido y Nombre</td>
                    <td align=center >DNI</td>
                    <td align=center >Fecha Comprobante</td>
                    <td align=center >Monto</td>
                </tr><?
        while (!$result_detalle->EOF) {
                    ?><tr style="display:none" class="tr<?= $codigocss ?>">
                        <td align=right ><?= $result_detalle->fields['clavebeneficiario'] ?></td>
                        <td align=right ><?= $result_detalle->fields['afiapellido'] . ", " . $result_detalle->fields['afinombre'] ?></td>
                        <td align=right ><?= $result_detalle->fields['afidni'] ?></td>
                        <td align=right ><?= $result_detalle->fields['fecha_comprobante'] ?></td>
                        <td align=right ><?= number_format($result_detalle->fields['suma'], 2, ',', '.') ?></td>
                    </tr> <?
            $result_detalle->MoveNext();
        }
                ?>
                <?
                $aux++;
                $otro_result->MoveNext();
            }
        }
        ?>
    </table>

    <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5">
        <tr bgcolor="#d3d3cd">
            <td align="center" colspan="9" style="padding-top: 20px;"><b>No Autorizado</b></td>
        </tr>
        <tr bgcolor=#C0C0FF>
            <td align=center >Nº</td>
            <td align=center >Codigo</td>            
            <td align=center >Motivo</td>
            <td align=center >Dni</td>
            <td align=center >Apellido</td>
            <td align=center >Nombre</td>
            <td align=center >Fecha</td>
            <td align=center >Cantidad</td>
            <td align=center >Valor Unit</td>
            <td align=center >Valor Total</td>
        </tr>
        <?
        $aux = 1;
        while (!$result->EOF) {
            ?>
            <tr>
                <td><?= $aux ?></td>
                <td><?= $result->fields['codigo_deb'] ?></td>                
                <td><?= $result->fields['mensaje_baja'] ?></td>
                <td><?= $result->fields['documento_deb'] ?></td>
                <td><?= $result->fields['apellido_deb'] ?></td>
                <td><?= $result->fields['nombre_deb'] ?></td>
                <td><?= substr($result->fields['fecha_comprobante'],0,10) ?></td>
                <td><?= number_format($result->fields['cantidad'], 0, ',', '.') ?></td>
                <td><?= number_format($result->fields['monto'], 2, ',', '.') ?></td>
                <td><?= number_format($result->fields['cantidad'] * $result->fields['monto'], 2, ',', '.') ?></td>
            </tr>
            <?
            $aux++;
            $result->MoveNext();
        }
        ?>
    </table>
</form>