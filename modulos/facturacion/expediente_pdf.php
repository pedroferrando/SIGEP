<?php

require_once("../../config.php");
require_once("./bibiliotecaExpediente.php");
require_once("./calculoObjetivos2012.php");
require_once("../../clases/Smiefectores.php");
require_once("../../clases/DebitoRetroactivo.php");
require_once("../../clases/Expediente.php");
require_once("../../clases/Factura.php");
require_once("../../lib/nrosenletras/class.nroenletraver2.php");
require 'cargo.php';

$nro_expediente = $parametros['nro_expediente'];
$expediente = ExpedienteCollecion::Filtrar("nro_exp='$nro_expediente'");
$pago = buscarDatosExpediente($nro_expediente);
//$expedienteid = buscarIDExpediente();

$pdf = new PDF();
$title = 'Expediente Nro: ' . $nro_expediente;
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 15);
$pdf->AliasNbPages();
$pdf->SetTitle($title);
$pdf->SetExpediente($nro_expediente);
$pdf->Titulo();

$efectores = $expediente->getEfectores(); //buscarExpediente($nro_expediente);
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

    $pdf->Efector($efector->getCuie(), $efector->getNombreefector());
    $facturasdelefector = buscarFacturasPersistidas($expediente->getExpedienteId(), $efector->getCuie());

    $montoefector = 0;
    while (!$facturasdelefector->EOF) {
        $id_factura = $facturasdelefector->fields['id_factura'];
        $tiponomenclador = $facturasdelefector->fields['tipo_nomenclador'];
        $datosdelafactura = buscarDatosDeFactura($id_factura);
        $nrofactura = $datosdelafactura->fields['nro_fact_offline'];

        $montofacturaaux = $facturasdelefector->fields['total_liquidado'];
        $totaldebitadoaux = $facturasdelefector->fields['total_rechazado'];

        $liq = $montofacturaaux - $totaldebitadoaux;


        $tipofactura = tipoDeFactura($id_factura);
        $fechasfacturadas = fechasFacturadas($id_factura);
        $fecha_factura = $facturasdelefector->fields['fecha_factura'];
        $periodo = $datosdelafactura->fields['periodo'];
        //imprime los datos de la factura
        $pdf->Factura($nrofactura, substr($fecha_factura, 0, 10), $fechasfacturadas, $periodo, $tipofactura, $tiponomenclador);
        //imprime el cuadro donde se veran las practicas
        $pdf->facturaCabecera();

        $practicasenfactura = buscarPracticasEnFacturaPersistida($facturasdelefector->fields['id_factura_persistida']);

        $totalpracticasdebitadasenfactura = 0;
        while (!$practicasenfactura->EOF) {
            //imprime una linea, correspondiente a un codigo de practica de la factura totalizado.
            $pdf->facturaFila($practicasenfactura->fields['codigo'], $practicasenfactura->fields['precio'], $practicasenfactura->fields['cantidad_total'], $practicasenfactura->fields['cantidad_rechazos'], $tiponomenclador);
            $practicasenfactura->MoveNext();
        }

        $sumaparaelresumendefactura['monto_prefactura'] = $facturasdelefector->fields['total_liquidado'];
        $sumaparaelresumendefactura['cant_practicas'] = $facturasdelefector->fields['cant_de_practicas'];
        $sumaparaelresumendefactura['cant_practicas_deb'] = $facturasdelefector->fields['cant_de_practicas_rechazadas'];
        $sumaparaelresumendefactura['monto_deb'] = $facturasdelefector->fields['total_rechazado'];

        $montoefector+=$facturasdelefector->fields['total_liquidado'] - $facturasdelefector->fields['total_rechazado'];
        $total_liquidado+=$facturasdelefector->fields['total_liquidado'];
        $total_debitado+=$facturasdelefector->fields['total_rechazado'];
        $totalpracticasdebitadasenfactura += $facturasdelefector->fields['cant_de_practicas_rechazadas'];
        $cant_practicas+=$facturasdelefector->fields['cant_de_practicas'];
        $cant_practicas_deb[$i] += $totalpracticasdebitadasenfactura;
        $pdf->facturaResumen($sumaparaelresumendefactura);
        unset($sumaparaelresumendefactura);
        $facturasdelefector->MoveNext();
    }
    $objetivosResultado = buscarObjetivosPersistidos($efector->getCuie(), $expediente->getExpedienteId());
    $facturasdelefector->MoveFirst();
    $pdf->objetivosCabecera(Fecha($facturasdelefector->fields['fecha_entrada']));
    $puntosdelefector = $pdf->cadaObjetivo($objetivosResultado);
    $debitosencontrados = debitoAuditado($nro_expediente, $efector->getCuie());
    if ($debitosencontrados->rowcount() > 0) {
        $debitoauditado = $pdf->debitoAuditado($debitosencontrados);
        $montoefector = $montoefector - $debitoauditado;
        $total_debito_auditado+=$debitoauditado;
    }
    //info de debitos retroactivos
    $total_debito_retro = 0;
    $debitosRetro = DebitoRetroactivoColeccion::getResumenDebitoRetroactivo($efector->getCuie());
    if($debitosRetro->rowcount()>0){
        $pdf->debitoRetroactivo($debitosRetro->fields['total'],$debitosRetro->fields['monto']);
        $total_debito_retro = $debitosRetro->fields['monto'];
        $montoefector = $montoefector - $total_debito_retro;
    }
    $observacionesencontradas = obsRegistradas($nro_expediente, $efector->getCuie());
    if ($observacionesencontradas->rowcount() > 0) {
        $pdf->observaciones($observacionesencontradas);
    }
    $paraestimulos = ($montoefector * $puntosdelefector) / 100;

    $estimulacion_formateada = floatval(number_format($paraestimulos, 2, '.', ''));
    $total_formateada = floatval(number_format($montoefector, 2, '.', ''));
    $paraefector = $total_formateada - $estimulacion_formateada;

    $pdf->totalDelEfector($paraefector, $paraestimulos);
    $total_para_estimulos+=$paraestimulos;
}
if ($cant_practicas_deb > 0) {
    foreach ($cant_practicas_deb as $value) {
        $suma_de_rechazos+=$value;
    }
}
$pdf->totalDelExpediente($cant_practicas, $total_liquidado, $suma_de_rechazos, $total_debitado, $total_debito_auditado, $total_debito_retro, $total_para_estimulos);
if ($pago->RecordCount() > 0) {
    $pdf->datosPago($pago);
}
$pdf->Output();
?>
