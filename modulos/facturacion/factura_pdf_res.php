<?php

define('FPDF_FONTPATH', 'font/');

require_once("../../config.php");
include_once("factura_clasepdf.php");

require_once("../../lib/funciones_misiones.php");
require_once("../../lib/bibliotecaTraeme.php");

//$func_nroFactura = nro_factura_misiones();
//if ($func_nroFactura) {
//    $nroF = NRO_FACTURA_MISIONES;
//} else {
//    $nroF = '';
//}
//generacion de pdf
$pdf = new orden_compra();
if ($parametros['id_factura'])
    $id_factura = $parametros['id_factura'];

//$query = "SELECT 
//  sum(facturacion.prestacion.cantidad) AS cantidad,
//  facturacion.nomenclador.codigo,
//  facturacion.nomenclador.descripcion,
//  facturacion.prestacion.precio_prestacion,
//  facturacion.prestacion.precio_prestacion*sum(facturacion.prestacion.cantidad) as precio_total,
//  facturacion.smiefectores.nombreefector,
//  facturacion.smiefectores.cuie,
//  facturacion.factura.periodo,
//  facturacion.factura.periodo_actual,
//  facturacion.factura.observaciones,
//  facturacion.factura.fecha_carga,
//  facturacion.factura.fecha_factura
//FROM
//  facturacion.nomenclador
//  INNER JOIN facturacion.prestacion ON (facturacion.nomenclador.id_nomenclador = facturacion.prestacion.id_nomenclador)
//  INNER JOIN facturacion.comprobante ON (facturacion.prestacion.id_comprobante = facturacion.comprobante.id_comprobante)
//  INNER JOIN facturacion.factura ON (facturacion.comprobante.id_factura = facturacion.factura.id_factura)  
//  INNER JOIN facturacion.smiefectores ON (facturacion.comprobante.cuie = facturacion.smiefectores.cuie)
//WHERE  (factura.id_factura='$id_factura')
//  GROUP BY
//  facturacion.nomenclador.id_nomenclador,
//  facturacion.nomenclador.codigo,
//  facturacion.nomenclador.descripcion,
//  facturacion.prestacion.precio_prestacion,
//  facturacion.smiefectores.nombreefector,
//  facturacion.smiefectores.cuie,
//  facturacion.factura.periodo,
//  facturacion.factura.periodo_actual,
//  facturacion.factura.observaciones,
//  facturacion.factura.fecha_carga,
//  facturacion.factura.fecha_factura
//  order by codigo";
//
//$f_res = $db->Execute($query) or die($db->ErrorMsg());

$query1 = "SELECT 
		  facturacion.nomenclador.codigo,
                  facturacion.nomenclador.diagnostico,
                  facturacion.factura.tipo_liquidacion,
                  d.descripcion as practica,
                  nomenclador.patologias.descripcion as desdiagnostico,
		  sum(b.cantidad) AS cantidad,
		  precio_prestacion precio,
		  facturacion.smiefectores.nombreefector,
		  facturacion.smiefectores.cuie,
		  facturacion.factura.periodo,
		  facturacion.factura.periodo_actual,
		  facturacion.factura.observaciones,
		  facturacion.factura.fecha_carga,
		  facturacion.factura.fecha_factura,
                  facturacion.factura.nro_fact_offline
		FROM facturacion.comprobante
		INNER JOIN facturacion.prestacion b ON (facturacion.comprobante.id_comprobante = b.id_comprobante)
		INNER JOIN facturacion.nomenclador ON (b.id_nomenclador = facturacion.nomenclador.id_nomenclador)
		LEFT JOIN nomenclador.patologias ON (facturacion.nomenclador.diagnostico=nomenclador.patologias.codigo)
		LEFT JOIN nomenclador.descripciones d ON (d.codigo = nomenclador.codigo and d.grupo_etareo = comprobante.grupo_etario  and d.diagnostico = nomenclador.diagnostico)
                INNER JOIN facturacion.smiefectores ON (comprobante.cuie = facturacion.smiefectores.cuie)
		INNER JOIN facturacion.factura ON (facturacion.comprobante.id_factura = facturacion.factura.id_factura)  
		WHERE  (factura.id_factura='$id_factura')              
		GROUP BY
		  facturacion.nomenclador.codigo,
                  nomenclador.patologias.descripcion,
                  facturacion.nomenclador.diagnostico,
				  facturacion.factura.tipo_liquidacion,
                  d.descripcion,
                  b.precio_prestacion,
		  facturacion.smiefectores.nombreefector,
		  facturacion.smiefectores.cuie,
		  facturacion.factura.periodo,
		  facturacion.factura.periodo_actual,
		  facturacion.factura.observaciones,
		  facturacion.factura.fecha_carga,
		  facturacion.factura.fecha_factura,
                  facturacion.factura.nro_fact_offline
		ORDER BY 
		  facturacion.nomenclador.codigo,
                  facturacion.nomenclador.diagnostico";
$f_res1 = $db->Execute($query1) or die($db->ErrorMsg());

$pdf->dibujar_planilla();

//if ($func_nroFactura) {
//    $query = "SELECT
//              " . NRO_FACTURA_MISIONES . " 1
//            FROM facturacion.factura
//            where id_factura='$id_factura'";
//    $result_nro_fact = $db->Execute($query) or die($db->ErrorMsg());
//}
//
//if ($f_res->recordcount() > 0) {
//    $pdf->nro_orden_compra($f_res->fields['periodo']);
//    $pdf->nro_orden_compra1($f_res->fields['periodo_actual']);
//    $pdf->proveedor($f_res->fields['nombreefector']);
//    $pdf->fecha(Fecha($f_res->fields['fecha_factura']));
//    //$pdf->fecha_carga(Fecha($f_res->fields['fecha_carga']));
//    $pdf->vendedor($f_res->fields['cuie']);
//    $pdf->pasa_id_licitacion($result_nro_fact->fields['nro_fact_offline']);
//    $pdf->lugar_entrega($f_res->fields['observaciones']);
//}
if ($f_res1->recordcount() > 0) {
    $pdf->nro_orden_compra($f_res1->fields['periodo_actual']);
    $pdf->nro_orden_compra1($f_res1->fields['periodo']);
    $pdf->proveedor($f_res1->fields['nombreefector']);
    $pdf->fecha(Fecha($f_res1->fields['fecha_factura']));
    //$pdf->fecha_carga(Fecha($f_res1->fields['fecha_carga']));
    $pdf->vendedor($f_res1->fields['cuie']);
    $pdf->tipodeliquidacion($f_res1->fields['tipo_liquidacion']);

    $contrato = buscarContrato($f_res1->fields['cuie']);
    $pdf->contrato($contrato);
    $cuenta = buscarCuenta($f_res1->fields['cuie']);
    $pdf->cuenta($cuenta->fields['nombre'], $cuenta->fields['nrocta']);
    $pdf->pasa_id_licitacion($f_res1->fields['nro_fact_offline']);
    $pdf->lugar_entrega($f_res1->fields['observaciones']);
}

//traemos los productos para agregar al pdf desde la tabla filas
$total = 0;

//if ($f_res->recordcount() > 0) {
//    while (!$f_res->EOF) {
//        $pdf->producto($f_res->fields['codigo'] . ": " . $f_res->fields['descripcion'], $f_res->fields['cantidad'], $f_res->fields['precio_prestacion'], "$");
//        $f_res->MoveNext();
//    }
//}

if ($f_res1->recordcount() > 0) {
    while (!$f_res1->EOF) {
        if ($f_res1->fields['practica']) {
            $codigo = $f_res1->fields['codigo'] . " " . $f_res1->fields['diagnostico'] . " [" . $f_res1->fields['practica'] . " - " . $f_res1->fields['desdiagnostico'] . "]";
        } else {
            $codigo = $f_res1->fields['codigo'] . " " . $f_res1->fields['diagnostico'];
        }
        $pdf->producto($codigo, $f_res1->fields['cantidad'], $f_res1->fields['precio'], "$");
        $f_res1->MoveNext();
    }
}

$query_t = "SELECT sum (facturacion.prestacion.cantidad*facturacion.prestacion.precio_prestacion) as total
	    FROM facturacion.factura
	    INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
	    INNER JOIN facturacion.prestacion ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)
	    WHERE factura.id_factura=$id_factura";
$total = sql($query_t, "NO se pudo calcular el total, reintente nuevamente o avise al Administrador");

$total = $total->fields['total'];

$pdf->_final($total, "$", $_ses_user['name'], $firma2, $firma3);
$pdf->Footer();

$id_factura = $f_res1->fields['nro_fact_offline'];
$pdf->guardar_servidor("factura_$id_factura.pdf");
?>