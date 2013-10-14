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
        $this->MultiCell(20, 2, 'Plan Nacer Misiones', 0, 'C');
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
        $this->SetX(15);
        $this->Cell(13, 4, "Codigo", 1, 0, 'C');
        $this->Cell(80, 4, "Motivo", 1, 0, 'C');
        $this->Cell(13, 4, "Dni", 1, 0, 'C');
        $this->Cell(20, 4, "Apellido", 1, 0, 'C');
        $this->Cell(30, 4, "Nombre", 1, 0, 'C');
        $this->Cell(6, 4, "Cant.", 1, 0, 'C');
        $this->Cell(10, 4, "Valor U.", 1, 0, 'C');
        $this->Cell(10, 4, "Total", 1, 0, 'C');
        $this->Ln();
    }

    function facturaFila($codigo_deb, $mensaje_baja, $documento_deb, $apellido_deb, $nombre_deb, $cantidad, $monto, $tiponomenclador) {
        $this->SetFont('TIMES', '', 6);
        $this->SetX(15);
        $this->Cell(13, 4, $codigo_deb, 1, 0, 'C');
        $this->SetX(28);
        $this->Cell(80, 4, $mensaje_baja, 1, 0, 'L');
        $this->Cell(13, 4, $documento_deb, 1, 0, 'C');
        $this->Cell(20, 4, $apellido_deb, 1, 0, 'C');
        $this->Cell(30, 4, $nombre_deb, 1, 0, 'C');
        $this->Cell(6, 4, $cantidad, 1, 0, 'C');
        $total = $cantidad * $monto;
        if (($tiponomenclador != "PERINATAL_CATASTROFICO") && ($tiponomenclador != "PERINATAL_NO_CATASTROFICO")) {
            $this->Cell(10, 4, "$ " . number_format($monto, 2, ",", "."), 1, 0, 'C');
        } else {
            $this->Cell(10, 4, "-", 1, 0, 'C');
        }

        $this->Cell(10, 4, "$ " . number_format($total, 2, ",", "."), 1, 0, 'C');
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
        $this->Cell(30, 4, "Liquidacion " . $tipo, 0, 0, 'L');
        if ($tiponomenclador != '') {
            $this->Cell(20, 4, "Nom.  $tiponomenclador", 0, 0, 'L');
        }
        $this->Ln();
        $this->setx(10); //, $this->efectory + 20);
        $this->Cell(20, 4, "Mes Correspondiente " . $mes_liquidado, 0, 0, 'L');
        $this->Ln();
    }

    function totalDelEfector($monto_debitado, $cantidad) {
        $this->SetFont('TIMES', 'B', 6);
        $this->Ln();
        $this->SetX(-55);
        $this->SetFillColor(210, 210, 210);
        $this->Cell(40, 4, "Total debitado para el Efector " . $this->cuie, 1, 0, 'C', 1);
        $this->Ln();
        $this->SetX(-55);
        $this->Cell(20, 4, "Cantidad", 1, 0, 'C');
        $this->Cell(20, 4, "Monto", 1, 0, 'C');
        $this->Ln();
        $this->SetFont('TIMES', '', 6);
        $this->SetX(-55);
        $this->Cell(20, 4, $cantidad, 1, 0, 'C');
        $this->Cell(20, 4, '$ ' . number_format($monto_debitado, 2, ',', '.'), 1, 0, 'C');
        $this->Ln(5);
        $this->y0 = $this->GetY() + 10;
    }

    function totalDelExpediente($cant_practicas, $total_liquidado, $cant_practicas_deb, $total_debitado, $total_auditado, $total_para_estimulos) {
        $this->Ln(5);
        $this->SetFont('TIMES', 'B', 8);
        $this->MultiCell(0, 5, "Del Total del Expediente Nº $this->nro_exp\nsurgen los siguientes rechazos", 0, 'C');
        $this->SetX(15);
        $y = $this->GetY();
        $this->SetFont('TIMES', 'B', 6);
        $this->MultiCell(20, 4, "Cantidad de codigos", 1, "C");
        $this->SetY($y);
        $this->SetX(35);
        $this->Cell(20, 12, "Total liquidado", 1, 0, 'C');
        $this->Cell(40, 8, "Debitos", 1, 0, 'C');
        $this->MultiCell(20, 4, "\nDebitos\nAuditados", 1, "C");
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
        $this->Cell(20, 4, '$ ' . number_format($total_auditado, 2, ',', '.'), 1, 0, 'C');
        $this->Cell(20, 4, $cant_practicas - $cant_practicas_deb, 1, 0, 'C');
        $pagos_total = $total_liquidado - $total_debitado - $total_auditado;
        $this->Cell(20, 4, '$ ' . number_format($pagos_total, 2, ',', '.'), 1, 0, 'C');

        $estimulacion_formateada = floatval(number_format($total_para_estimulos, 2, '.', ''));
        $total_formateada = floatval(number_format($pagos_total, 2, '.', ''));
        $total_paraefector = $total_formateada - $estimulacion_formateada;

        $this->Cell(20, 4, '$ ' . number_format($total_paraefector, 2, ',', '.'), 1, 0, 'C');
        $this->Cell(20, 4, '$ ' . number_format($total_para_estimulos, 2, ',', '.'), 1, 0, 'C');
    }

}

?>
