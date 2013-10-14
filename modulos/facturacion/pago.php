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

    function pago($total_liquidado, $total_liquidado_txt, $administrador, $nro_cheque, $nro_orden, $fecha_orden_de_cargo, $fecha_orden_de_cargo, $responsable_administrador,$fecha_pago_efectivo) {
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(60, 8, "IMPORTE TRANSFERENCIA: $ ", 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 8, $total_liquidado, 0, 0, 'L');
        $this->Ln();
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(60, 8, "SON PESOS: ", 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->MultiCell(130, 8, strtoupper($total_liquidado_txt), 0, 'L');
        //$this->Ln();
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(60, 8, "A: ", 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 8, $administrador, 0, 0, 'L');
        $this->Ln();
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(60, 8, "Nº CHEQUE: ", 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 8, $nro_cheque, 0, 0, 'L');
        $this->Ln();
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(60, 8, "ORDEN DE CARGO Nº: ", 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 8, $nro_orden, 0, 0, 'L');
        $this->Ln();
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(60, 8, "FECHA ORDEN DE CARGO: ", 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 8, $fecha_orden_de_cargo, 0, 0, 'L');
        $this->Ln();
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(60, 8, "RESPONSABLE ADMINISTRADOR: ", 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 8, $responsable_administrador, 0, 0, 'L');
        $this->Ln();
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(60, 8, "FECHA DE PAGO EFECTIVO: ", 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 8, $fecha_pago_efectivo, 0, 0, 'L');
        $this->Ln();
    }

    function SetExpediente($nro_exp, $isUTF8=false) {
        // Title of document
        if ($isUTF8)
            $nro_exp = $this->_UTF8toUTF16($nro_exp);
        $this->nro_exp = $nro_exp;
    }

}

?>
