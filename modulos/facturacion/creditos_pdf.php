<?php

require_once("../../config.php");
require_once("./bibiliotecaExpediente.php");
require_once ("../../clases/BeneficiariosSmi.php");
require_once("../../clases/Smiefectores.php");
require_once("../../clases/Expediente.php");
require_once("../../clases/Factura.php");
require 'creditos.php';

$nro_expediente = $parametros['nro_expediente'];

$pdf = new PDF();
$title = 'Expediente Nro: ' . $nro_expediente;
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 15);
$pdf->AliasNbPages();
$pdf->SetTitle($title);
$pdf->SetExpediente($nro_expediente);
$pdf->Titulo();

$expediente = ExpedienteCollecion::Filtrar("nro_exp='$nro_expediente'");
$efectores = $expediente->getEfectores();
$i = 0;
$y = 0;
$suma_de_rechazos = 0;
$total_para_estimulos = 0;
$cant_practicas = 0;
$total_liquidado = 0;
$total_debitado = 0;
$total_auditado = 0;
$practicasdebitadasenfactura = 0;


foreach ($efectores as $efector) {
    $montodebitado = 0;
    $cant_prac_deb = 0;
    $pdf->Efector($efector->getCuie(), $efector->getNombreefector());
    $facturasdelefector = $expediente->facturasDelEfector($efector->getCuie());

    foreach ($facturasdelefector as $factura) {
        //imprime los datos de la factura
        $pdf->Factura($factura->getNroFactOffline(), substr($factura->getFechaFactura(), 0, 10), $factura->getFechasFacturadas(), $factura->getPeriodo(), $factura->getTipoDeFactura(), $factura->getTipoNomenclador());
        $tiponomenclador = $factura->getTipoNomenclador();
        //imprime el cuadro donde se veran las practicas
        $pdf->facturaCabecera();
        if ($factura->getNomencladorDetalle() < 14) {
            $practicasdebitadasdelafactura = practicasEnCreditoViejo($factura->getIdFactura());
        } else {
            $practicasdebitadasdelafactura = practicasEnCredito($factura->getIdFactura());
        }
        $totalpracticasdebitadasenfactura = 0;
        while (!$practicasdebitadasdelafactura->EOF) {
            //imprime una linea, correspondiente a un codigo de practica de la factura totalizado.
            $codigo_deb = $practicasdebitadasdelafactura->fields['codigo'] . " " . $practicasdebitadasdelafactura->fields['diagnostico'];
            $benef = BeneficiarioSmi::buscarPorClaveBeneficiario($practicasdebitadasdelafactura->fields['clavebeneficiario']);
            $documento_deb = $benef->getAfidni();
            $apellido_deb = $benef->getAfiapellido();
            $nombre_deb = $benef->getAfinombre();
            $cantidad = number_format($practicasdebitadasdelafactura->fields['cantidad'], 0, ',', '.');
            $monto = number_format($practicasdebitadasdelafactura->fields['precio_prestacion'], 2, ',', '.');
            $total = number_format($practicasdebitadasdelafactura->fields['cantidad'] * $practicasdebitadasdelafactura->fields['precio_prestacion'], 2, ',', '.');
            $fecha = $practicasdebitadasdelafactura->fields['fecha_comprobante'];
            $pdf->facturaFila($codigo_deb, $documento_deb, $apellido_deb, $nombre_deb, $monto, $cantidad, $fecha, $tiponomenclador);
            $cant_prac_deb+=$cantidad;
            $montodebitado+=$total;
            $practicasdebitadasdelafactura->MoveNext();
        }
    }
    $pdf->totalDelEfector($montodebitado, $cant_prac_deb);
}
$pdf->Output();
?>
