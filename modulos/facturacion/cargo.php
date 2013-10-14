<?php

require ("../../lib/fpdf/fpdf.php");

class PDF extends FPDF {

// Columna actual
    var $col = 0;
// Ordenada de comienzo de la columna
    var $y0;
    var $nro_exp;
    var $cuie;
    var $cordenaday = 0;
    //
    var $efectory;

    function Header() {
        $this->SetFont('Arial', '', 6);
        $fecha = date('d-m-Y');
        $this->sety(4);
        $this->setx(15);
        $this->Cell(20, 0, 'Fecha de impresion ' . $fecha, 0, 0, 'C');
        $this->SetLineWidth(0.1);
        $this->Line(5, 8, 205, 8);
        $this->Image('./nacer-logo.png', 180, 1, 7, 5);
        $this->setxy(185, 2);
        $this->SetFont('Arial', 'B', 6);
        $this->MultiCell(20, 2, 'Plan Sumar Misiones', 0, 'C');
        $this->Ln(5);
    }

    function Titulo() {
        global $title, $nro_exp;
        $this->SetFont('Arial', 'B', 12);
        $w = $this->GetStringWidth($title) + 6;
        $this->SetY(10);
        $this->SetX((210 - $w) / 2);
        //$this->SetDrawColor(0, 80, 180);
        // $this->SetFillColor(255, 255, 255);
        $this->SetTextColor(0, 0, 0);
        //$this->SetLineWidth(1);
        $this->Cell($w, 9, $title, 1, 1, 'C');
        //$this->Ln(10);
        // Guardar ordenada
        $this->y0 = $this->GetY();
    }

    function Footer() {
        // Pie de pÃ¡gina        
        $this->SetLineWidth(0.1);
        $this->Line(5, 290, 205, 290);
        $this->SetY(-9);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128);
        $this->Cell(0, 10, 'Página ' . $this->PageNo() . ' de {nb}', 0, 0, 'C');
    }

    function SetExpediente($nro_exp, $isUTF8 = false) {
        // Title of document
        if ($isUTF8)
            $nro_exp = $this->_UTF8toUTF16($nro_exp);
        $this->nro_exp = $nro_exp;
    }

    function Efector($cuie, $nombre) {
        $this->Ln();
        $this->cuie = $cuie;
        //$this->SetY($this->y0);
        $this->SetFont('TIMES', '', 8);
        $this->SetTextColor(0);
        $this->Cell(0, 1, $cuie . " - " . $nombre, 0, 0, 'C');
        $this->Ln();
        //$this->efectory = $this->GetY();
    }

    function facturaCabecera() {
        $this->SetFont('TIMES', 'B', 6);
        $this->SetX(105);
        $this->Cell(40, 4, "Rechazos", 1, 0, 'C');
        $this->SetX(145);
        $this->Cell(40, 4, "Pagos", 1, 1, 'C');
        $this->SetX(25);
        $this->Cell(20, 4, "Codigos", 1, 0, 'C');
        // if (($tiponomenclador != "PERINATAL_CATASTROFICO") && ($tiponomenclador != "PERINATAL_NO_CATASTROFICO")) {
        $this->Cell(20, 4, "Precio Unitario", 1, 0, 'C');
        // }
        $this->Cell(20, 4, "Cantidad", 1, 0, 'C');
        $this->Cell(20, 4, "Total Liquidado", 1, 0, 'C');
        $this->Cell(20, 4, "Cantidad", 1, 0, 'C');
        $this->Cell(20, 4, "Total", 1, 0, 'C');
        $this->Cell(20, 4, "Cantidad", 1, 0, 'C');
        $this->Cell(20, 4, "Total", 1, 0, 'C');
        $this->Ln();
    }

    function facturaFila($codigo, $precio, $cantidad, $cant_debitos, $tiponomenclador) {
        $this->SetFont('TIMES', '', 6);
        $this->SetX(25);
        $this->Cell(20, 4, $codigo, 1, 0, 'C');
        if (($tiponomenclador != "PERINATAL_CATASTROFICO") && ($tiponomenclador != "PERINATAL_NO_CATASTROFICO")) {
            $this->Cell(20, 4, "$ " . number_format($precio, 2, ",", "."), 1, 0, 'C');
        } else {
            $this->Cell(20, 4, "-", 1, 0, 'C');
        }
        $this->Cell(20, 4, $cantidad, 1, 0, 'C');
        $this->Cell(20, 4, "$ " . number_format($precio * $cantidad, 2, ",", "."), 1, 0, 'C');
        $this->Cell(20, 4, $cant_debitos, 1, 0, 'C');
        $this->Cell(20, 4, "$ " . number_format($precio * $cant_debitos, 2, ",", "."), 1, 0, 'C');
        $pagos = $cantidad - $cant_debitos;
        $this->Cell(20, 4, $pagos, 1, 0, 'C');
        $this->Cell(20, 4, "$ " . number_format($pagos * $precio, 2, ",", "."), 1, 0, 'C');
        $this->Ln();
    }

    function facturaResumen($sumaparaelresumendefactura) {
        $this->SetFont('TIMES', '', 5);
        $this->SetX(25);
        $this->Cell(40, 4, "Cantidad Total: " . $sumaparaelresumendefactura['cant_practicas'], 1, 0, 'C');
        $this->Cell(40, 4, "Monto Total Efectuado: $" . number_format($sumaparaelresumendefactura['monto_prefactura'], 2, ",", "."), 1, 0, 'C');
        $this->Cell(40, 4, "Cantidad Rechazado: " . $sumaparaelresumendefactura['cant_practicas_deb'], 1, 0, 'C');
        $this->Cell(40, 4, "Monto Total Rechazado: $" . number_format($sumaparaelresumendefactura['monto_deb'], 2, ",", "."), 1, 0, 'C');
        $this->Ln();
    }

    function Factura($nro_factura, $fecha, $mes_prestacion, $mes_liquidado, $tipo, $tiponomenclador) {
        $this->Ln();
        $this->SetFont('TIMES', '', 8);
        $this->SetTextColor(0);
        $this->setx(10); //, $this->efectory + 10);
        $this->Cell(40, 4, "Factura Nº " . $nro_factura, 0, 0, 'L');
        $this->Cell(20, 4, "Fecha " . $fecha, 0, 0, 'L');
        $this->Ln();
        $this->setx(10); //, $this->efectory + 15);
        $this->Cell(20, 4, "Mes Prestacion ", 0, 0, 'L');
        while (!$mes_prestacion->EOF) {
            $this->Cell(10, 4, $mes_prestacion->fields['mes'] . "/" . $mes_prestacion->fields['ano'], 0, 0, 'L');
            $mes_prestacion->movenext();
        }
        $this->Cell(20, 4, "Liquidacion " . $tipo . ' - Nom. ' . $tiponomenclador, 0, 0, 'L');

        $this->Ln();
        $this->setx(10); //, $this->efectory + 20);
        $this->Cell(20, 4, "Mes Correspondiente " . $mes_liquidado, 0, 0, 'L');
        $this->Ln();
    }

    function objetivosCabecera($periodo) {
        $this->Ln();
        $this->SetFont('TIMES', 'B', 6);
        $this->SetX(25);
        $this->Cell(160, 4, "Metas cumplidas al " . $periodo, 1, 0, 'C');
        $this->Ln();
        $this->SetX(25);
        $this->Cell(40, 4, "Objetivos", 1, 0, 'C');
        $this->Cell(20, 4, "Meta (%)", 1, 0, 'C');
        $this->Cell(40, 4, "Cumplimiento", 1, 0, 'C');
        $this->Cell(20, 4, "Informado", 1, 0, 'C');
        $this->Cell(20, 4, "Cumplido", 1, 0, 'C');
        $this->Cell(20, 4, "Puntos", 1, 0, 'C');
        $this->Ln();
        $this->SetX(25);
        $this->Cell(40, 4, "", 1, 0, 'C');
        $this->Cell(20, 4, "", 1, 0, 'C');
        $this->Cell(20, 4, "Porcentaje", 1, 0, 'C');
        $this->Cell(20, 4, "Absoluto", 1, 0, 'C');
        $this->Cell(20, 4, "", 1, 0, 'C');
        $this->Cell(20, 4, "", 1, 0, 'C');
        $this->Cell(20, 4, "", 1, 0, 'C');
        $this->Ln();
    }

    function cadaObjetivo($objetivos) {

        if ($objetivos[0]['encontro']) {
            $this->SetX(25);
            $this->SetFont('TIMES', 'B', 6);
            $this->Cell(40, 4, "Captacion embarazada", 1, 0, 'L');
            $this->Cell(20, 4, $objetivos[0]['meta'], 1, 0, 'C');
            $this->SetFont('TIMES', '', 6);
            $this->Cell(20, 4, number_format($objetivos[0]['total_perc'], 2, ',', '.'), 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[0]['numerador'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[0]['denominador'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[0]['cumplido'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[0]['puntos'], 1, 0, 'C');
            $this->Ln();
        }
        if ($objetivos[1]['encontro']) {
            $this->SetX(25);
            $this->SetFont('TIMES', 'B', 6);
            $this->Cell(40, 4, "Apgar'", 1, 0, 'L');
            $this->SetFont('TIMES', '', 6);
            $this->Cell(20, 4, $objetivos[1]['meta'], 1, 0, 'C');
            $this->Cell(20, 4, number_format($objetivos[1]['total_perc'], 2, ',', '.'), 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[1]['numerador'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[1]['denominador'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[1]['cumplido'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[1]['puntos'], 1, 0, 'C');
            $this->Ln();
        }
        if ($objetivos[2]['encontro']) {
            $this->SetX(25);
            $this->SetFont('TIMES', 'B', 6);
            $this->Cell(40, 4, "Peso Al Nacer > 2500gr.", 1, 0, 'L');
            $this->SetFont('TIMES', '', 6);
            $this->Cell(20, 4, $objetivos[2]['meta'], 1, 0, 'C');
            $this->Cell(20, 4, number_format($objetivos[2]['total_perc'], 2, ',', '.'), 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[2]['numerador'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[2]['denominador'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[2]['cumplido'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[2]['puntos'], 1, 0, 'C');
            $this->Ln();
        }
        if ($objetivos[3]['encontro']) {
            $this->SetX(25);
            $this->SetFont('TIMES', 'B', 6);
            $this->Cell(40, 4, "VDRL y ATT en Embarazo", 1, 0, 'L');
            $this->SetFont('TIMES', '', 6);
            $this->Cell(20, 4, $objetivos[3]['meta'], 1, 0, 'C');
            $this->Cell(20, 4, number_format($objetivos[3]['total_perc'], 2, ',', '.'), 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[3]['numerador'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[3]['denominador'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[3]['cumplido'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[3]['puntos'], 1, 0, 'C');
            $this->Ln();
        }
        if ($objetivos[4]['encontro']) {
            $this->SetX(25);
            $this->SetFont('TIMES', 'B', 6);
            $this->Cell(40, 4, "VDRL y ATT Previa al Parto", 1, 0, 'L');
            $this->SetFont('TIMES', '', 6);
            $this->Cell(20, 4, $objetivos[4]['meta'], 1, 0, 'C');
            $this->Cell(20, 4, number_format($objetivos[4]['total_perc'], 2, ',', '.'), 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[4]['numerador'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[4]['denominador'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[4]['cumplido'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[4]['puntos'], 1, 0, 'C');
            $this->Ln();
        }
        if ($objetivos[5]['encontro']) {
            $this->SetX(25);
            $this->SetFont('TIMES', 'B', 6);
            $this->Cell(40, 4, "Atencion de Muertes Materno/Infantiles", 1, 0, 'L');
            $this->SetFont('TIMES', '', 6);
            $this->Cell(20, 4, $objetivos[5]['meta'], 1, 0, 'C');
            $this->Cell(20, 4, number_format($objetivos[5]['total_perc'], 2, ',', '.'), 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[5]['numerador'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[5]['denominador'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[5]['cumplido'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[5]['puntos'], 1, 0, 'C');
            $this->Ln();
        }
        if ($objetivos[6]['encontro']) {
            $this->SetX(25);
            $this->SetFont('TIMES', 'B', 6);
            $this->Cell(40, 4, "Cob. Inmunizaciones", 1, 0, 'L');
            $this->SetFont('TIMES', '', 6);
            $this->Cell(20, 4, $objetivos[6]['meta'], 1, 0, 'C');
            $this->Cell(20, 4, number_format($objetivos[6]['total_perc'], 2, ',', '.'), 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[6]['numerador'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[6]['denominador'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[6]['cumplido'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[6]['puntos'], 1, 0, 'C');
            $this->Ln();
        }
        if ($objetivos[7]['encontro']) {
            $this->SetX(25);
            $this->SetFont('TIMES', 'B', 6);
            $this->Cell(40, 4, "Consejeria", 1, 0, 'L');
            $this->SetFont('TIMES', '', 6);
            $this->Cell(20, 4, $objetivos[7]['meta'], 1, 0, 'C');
            $this->Cell(20, 4, number_format($objetivos[7]['total_perc'], 2, ',', '.'), 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[7]['numerador'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[7]['denominador'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[7]['cumplido'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[7]['puntos'], 1, 0, 'C');
            $this->Ln();
        }
        if ($objetivos[8]['encontro']) {
            $this->SetX(25);
            $this->SetFont('TIMES', 'B', 6);
            $this->Cell(40, 4, "Seguimiento de Niño < 1 año", 1, 0, 'L');
            $this->SetFont('TIMES', '', 6);
            $this->Cell(20, 4, $objetivos[8]['meta'], 1, 0, 'C');
            $this->Cell(20, 4, number_format($objetivos[8]['total_perc'], 2, ',', '.'), 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[8]['numerador'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[8]['denominador'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[8]['cumplido'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[8]['puntos'], 1, 0, 'C');
            $this->Ln();
        }
        if ($objetivos[9]['encontro']) {
            $this->SetX(25);
            $this->SetFont('TIMES', 'B', 6);
            $this->Cell(40, 4, "Seguimiento de Niño >= 1 año", 1, 0, 'L');
            $this->SetFont('TIMES', '', 6);
            $this->Cell(20, 4, $objetivos[9]['meta'], 1, 0, 'C');
            $this->Cell(20, 4, number_format($objetivos[9]['total_perc'], 2, ',', '.'), 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[9]['numerador'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[9]['denominador'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[9]['cumplido'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[9]['puntos'], 1, 0, 'C');
            $this->Ln();
        }
        if ($objetivos[10]['encontro']) {
            $this->SetX(25);
            $this->SetFont('TIMES', 'B', 6);
            $this->Cell(140, 4, "Puntos Estaticos para el efector", 1, 0, 'L');
            $this->SetFont('TIMES', '', 6);
            $this->Cell(20, 4, $objetivos[10]['puntos'], 1, 0, 'C');
            $this->Ln();
        }
        if ($objetivos[11]['encontro']) {
            $this->SetX(25);
            $this->SetFont('TIMES', 'B', 6);
            $this->Cell(140, 4, "Base", 1, 0, 'L');
            $this->SetFont('TIMES', '', 6);
            $this->Cell(20, 4, $objetivos[11]['puntos'], 1, 0, 'C');
            $this->Ln();
        }
        if ($objetivos[12]['encontro']) {
            $this->SetX(25);
            $this->SetFont('TIMES', 'B', 6);
            $this->Cell(40, 4, "Cobertura Efectiva Basica", 1, 0, 'L');
            $this->SetFont('TIMES', '', 6);
            $this->Cell(20, 4, $objetivos[12]['meta'], 1, 0, 'C');
            $this->Cell(20, 4, number_format($objetivos[12]['total_perc'], 2, ',', '.'), 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[12]['numerador'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[12]['denominador'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[12]['cumplido'], 1, 0, 'C');
            $this->Cell(20, 4, $objetivos[12]['puntos'], 1, 0, 'C');
            $this->Ln();
        }
        $this->SetX(25);
        $puntosentotal = 0;
        if ($objetivos != null) {
            foreach ($objetivos as $unobjetivo) {
                $puntosentotal += $unobjetivo['puntos'];
            }
        }
        $this->SetFont('TIMES', 'B', 6);
        $this->Cell(140, 4, "Puntos en Total", 1, 0, 'R');
        $this->SetFont('TIMES', '', 6);
        $this->Cell(20, 4, $puntosentotal, 1, 0, 'C');
        $this->Ln();
        return $puntosentotal;
    }

    function totalDelEfector($monto_efector, $monto_estimulos) {
        $this->SetFont('TIMES', 'B', 6);
        $this->Ln();
        $this->SetX(115);
        $this->SetFillColor(210, 210, 210);
        $this->Cell(70, 4, "Total del Efector " . $this->cuie, 1, 0, 'C', 1);
        $this->Ln();
        $this->SetX(115);
        $this->Cell(20, 4, "Total a Pagar", 1, 0, 'C');
        $this->Cell(25, 4, "Fondos para el Efector", 1, 0, 'C');
        $this->Cell(25, 4, "Fondos para Estimulos", 1, 0, 'C');
        $this->Ln();
        $this->SetFont('TIMES', '', 6);
        $this->SetX(115);
        $this->Cell(20, 4, '$ ' . number_format($monto_efector + $monto_estimulos, 2, ',', '.'), 1, 0, 'C');
        $this->Cell(25, 4, '$ ' . number_format($monto_efector, 2, ',', '.'), 1, 0, 'C');
        $this->Cell(25, 4, '$ ' . number_format($monto_estimulos, 2, ',', '.'), 1, 0, 'C');
        $this->Ln(5);
        $this->y0 = $this->GetY() + 10;
    }

    function debitoRetroactivo($cantidad,$monto) {
        $this->Ln();
        $this->SetX(45);
        $this->Cell(50, 4, "Debito Retroactivo", 1, 0, 'C');
        $this->Ln();
        $this->SetX(45);
        $this->Cell(25, 4, "Cantidad", 1, 0, 'C');
        $this->Cell(25, 4, "Monto", 1, 0, 'C');

        $this->Ln();
        $this->SetX(45);
        $this->Cell(25, 4, $cantidad, 1, 0, 'L');
        $this->Cell(25, 4, "$ ".number_format($monto,2, ',', ''), 1, 0, 'R');
        $this->Ln();
        $this->Ln();
    }

    function debitoAuditado($debitos) {
        $total_auditado = 0;
        $this->Ln();
        $this->SetX(45);
        $this->Cell(100, 4, "Debito Auditado", 1, 0, 'C');
        $this->Ln();
        $this->SetX(45);
        $this->Cell(25, 4, "Monto", 1, 0, 'C');
        $this->Cell(25, 4, "Nro. Exp. Relacionado", 1, 0, 'C');
        $this->Cell(50, 4, "Descripcion", 1, 0, 'C');

        $this->Ln();
        while (!$debitos->EOF) {
            $this->SetX(45);
            $this->Cell(25, 4, number_format($debitos->fields['monto'], 2, ',', ''), 1, 0, 'L');
            $this->Cell(25, 4, $debitos->fields['nro_exp_relac'], 1, 0, 'L');
            $this->Cell(50, 4, $debitos->fields['descripcion'], 1, 0, 'L');
            $this->Ln();
            $total_auditado+=$debitos->fields['monto'];
            $debitos->movenext();
        }
        return $total_auditado;
    }

    function creditoAuditado($creditos) {
        $total_auditado = 0;
        $this->Ln();
        $this->SetX(65);
        $this->Cell(70, 4, "Credito Auditado", 1, 0, 'C');
        $this->Ln();
        $this->SetX(65);
        $this->Cell(25, 4, "Nro. Exp. Relacionado", 1, 0, 'C');
        $this->Cell(25, 4, "Descripcion", 1, 0, 'C');
        $this->Cell(20, 4, "Monto", 1, 0, 'C');
        $this->Ln();
        while (!$creditos->EOF) {
            $this->SetX(65);
            $this->Cell(25, 4, number_format($creditos->fields['monto'], 2, ',', ''), 1, 0, 'L');
            $this->Cell(25, 4, $creditos->fields['nro_exp_relac'], 1, 0, 'L');
            $this->Cell(20, 4, $creditos->fields['descripcion'], 1, 0, 'L');
            $this->Ln();
            $total_acreditado+=$creditos->fields['monto'];
            $creditos->movenext();
        }
        return $total_acreditado;
    }

    function observaciones($observacionesencontradas) {
        $total_auditado = 0;
        $this->Ln();
        $this->SetX(65);
        $this->Cell(70, 4, "OBSERVACIONES DE LA FACTURACION", 1, 0, 'C');
        $this->Ln();
        while (!$observacionesencontradas->EOF) {
            $this->SetX(65);
            $this->Cell(70, 4, $observacionesencontradas->fields['observacion'], 1, 0, 'C');
            $this->Ln();
            $observacionesencontradas->movenext();
        }
    }

    function totalDelExpediente($cant_practicas, $total_liquidado, $cant_practicas_deb, $total_debitado, $total_auditado, $total_retroactivo, $total_para_estimulos) {
        if ($this->GetY() < 255) {
            $this->Ln(5);
        } else {
            $this->AddPage();
        }
        $this->SetFont('TIMES', 'B', 8);
        $this->MultiCell(0, 5, "Del Total del Expediente Nº $this->nro_exp\nsurge la siguiente liquidacion", 0, 'C');
        $this->SetX(15);
        $y = $this->GetY();
        $this->SetFont('TIMES', 'B', 6);
        $this->MultiCell(20, 4, "Cantidad de \n codigos \n liquidados", 1, "C");
        $this->SetY($y);
        $this->SetX(35);
        $this->Cell(20, 12, "Total liquidado", 1, 0, 'C');
        $this->Cell(40, 8, "Debitos", 1, 0, 'C');
        $this->MultiCell(20, 4, "Debitos\nAuditados / \nRetroactivos", 1, "C");
        $this->SetY($y);
        $this->SetX(115);
        $this->Cell(40, 8, "Pagos", 1, 0, 'C');
        $this->Cell(40, 4, "Utilizacion de fondos", 1, 0, 'C');
        $this->Ln();
        $this->SetY($y + 8);
        $this->SetX(55);
        $this->Cell(20, 4, "Cantidad codigos", 1, 0, 'C');
        $this->Cell(20, 4, "Total", 1, 0, 'C');
        $this->SetX(115);
        $this->Cell(20, 4, "Cantidad codigos", 1, 0, 'C');
        $this->Cell(20, 4, "Total", 1, 0, 'C');
        //$y = $this->GetY();
        $this->SetY($y + 4);
        $this->SetX(155);
        $this->MultiCell(20, 4, "P/ Fortalecimiento del Efector", 1, "C");
        $this->SetY($y + 4);
        $this->SetX(175);
        $this->MultiCell(20, 4, "P/ Distribucion de Estimulos", 1, "C");
        //$this->Ln();
        $this->SetX(15);
        $this->Cell(20, 4, $cant_practicas, 1, 0, "C");
        $this->Cell(20, 4, '$ ' . number_format($total_liquidado, 2, ',', '.'), 1, 0, "C");
        $this->Cell(20, 4, $cant_practicas_deb, 1, 0, 'C');
        $this->Cell(20, 4, '$ ' . number_format($total_debitado, 2, ',', '.'), 1, 0, 'C');
        $this->Cell(20, 4, '$ ' . number_format($total_auditado+$total_retroactivo, 2, ',', '.'), 1, 0, 'C');
        $this->Cell(20, 4, $cant_practicas - $cant_practicas_deb, 1, 0, 'C');
        $pagos_total = $total_liquidado - $total_debitado - $total_auditado - $total_retroactivo;
        $this->Cell(20, 4, '$ ' . number_format($pagos_total, 2, ',', '.'), 1, 0, 'C');

        $estimulacion_formateada = floatval(number_format($total_para_estimulos, 2, '.', ''));
        $total_formateada = floatval(number_format($pagos_total, 2, '.', ''));
        $total_paraefector = $total_formateada - $estimulacion_formateada;

        $this->Cell(20, 4, '$ ' . number_format($total_paraefector, 2, ',', '.'), 1, 0, 'C');
        $this->Cell(20, 4, '$ ' . number_format($total_para_estimulos, 2, ',', '.'), 1, 0, 'C');
    }
    
    function datosPago($pago){
        $cx = 14;
        $this->SetX(65);
        $this->Ln(10);
        $y = $this->GetY();
        $this->SetFont('TIMES', 'B', 8);
        $this->MultiCell(0, 5, "Datos del Pago", 0, 'C');
        $y += 5;
        $this->Ln(); $this->SetX($cx);
        $this->SetFont('TIMES', '', 8);
        $importe = number_format($pago->fields['importe'], 2, ".", ",");
        $this->Cell(100, 8, "Importe Transferencia:  $  ".$importe, 0, 0, 'L');
        $y += 5;
        $this->Ln(); $this->SetX($cx); 
        $importe_txt = new NroEnLetra($importe, 2);
        $cifra = $importe_txt->getLetra();
        $this->Cell(15, 8, "Son pesos: ", 0, 0, 'L');
        $this->MultiCell(150, 8, strtoupper($cifra), 0, 'L');
        $y += 5;
         $this->SetX($cx);
        $this->Cell(100, 8, "A: ".$pago->fields['administrador'], 0, 0, 'L');
        $y += 5;
        $this->Ln(); $this->SetX($cx);
        $this->Cell(100, 8, "Nº Cheque: ".$pago->fields['nro_cheque'], 0, 0, 'L');
        $y += 5;
        $this->Ln(); $this->SetX($cx);
        $this->Cell(100, 8, "Orden de Cargo nº: ".$pago->fields['nro_orden'], 0, 0, 'L');
        $y += 5;
        $this->Ln(); $this->SetX($cx);
        $fecha_orden_de_cargo = $pago->fields['fecha_orden_de_cargo']!="" ? date('d/m/Y',strtotime($pago->fields['fecha_orden_de_cargo'])) : "";
        $this->Cell(100, 8, "Fecha de Orden de Cargo: ".$fecha_orden_de_cargo, 0, 0, 'L');
        $y += 5;
        $this->Ln(); $this->SetX($cx);
        $this->Cell(100, 8, "Responsable Administrador: ".$pago->fields['responsable_administrador'], 0, 0, 'L');
        $y += 5;
        $this->Ln(); $this->SetX($cx);
        $fecha_pago_efectivo = $pago->fields['fecha_pago_efectivo']!="" ? date('d/m/Y',strtotime($pago->fields['fecha_pago_efectivo'])) : "";
        if($fecha_pago_efectivo!=""){
            $this->Cell(100, 8, "Fecha de Pago Efectivo: ".$fecha_pago_efectivo, 0, 0, 'L');
        }
   }

}

?>
