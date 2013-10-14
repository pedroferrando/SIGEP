<?php

require_once("../../config.php");
require_once("./bibiliotecaExpediente.php");
require_once("../../lib/nrosenletras/class.nroenletraver2.php");
require 'pago.php';

$nro_expediente = $parametros['nro_expediente'];

$pdf = new PDF();
$title = 'Expediente Nro: ' . $nro_expediente;
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 15);
$pdf->AliasNbPages();
$pdf->SetTitle($title);
$pdf->SetExpediente($nro_expediente);
$pdf->Titulo();

$resul = buscarDatosExpediente($nro_expediente);
$total_liquidado = number_format($resul->fields['importe'], 2, ".", ",");
$total_liquidado_txt = new NroEnLetra($total_liquidado, 2);
$cifra = $total_liquidado_txt->getLetra();
$administrador = strtoupper($resul->fields['administrador']);
$nro_cheque = $resul->fields['nro_cheque'];
$nro_expediente = $resul->fields['nro_expediente'];
$nro_orden = $resul->fields['nro_orden'];
$fecha_orden_de_cargo = substr($resul->fields['fecha_orden_de_cargo'], 0, 10);
$responsable_administrador = $resul->fields['responsable_administrador'];
if($resul->fields['fecha_pago_efectivo']!=""){
    $fecha_pago_efectivo = date('d/m/Y',  strtotime($resul->fields['fecha_pago_efectivo']));
}


$pdf->pago($total_liquidado, $cifra, $administrador, $nro_cheque, $nro_orden, $fecha_orden_de_cargo, $fecha_orden_de_cargo, $responsable_administrador,$fecha_pago_efectivo);
$pdf->Output("pago.pdf", "D");
?>
