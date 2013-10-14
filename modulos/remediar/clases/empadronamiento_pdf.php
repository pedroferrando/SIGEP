<?php 


"Copyright (C) 2013 <Pezzarini Pedro Jose (jose2190@gmail.com)>

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.";


# Configuracion y acceso al sistema
#require_once ("../../config.php");

# Generadores de PDF
require_once("../../clases/creador_pdf.php");
require_once('../../lib/fpdf.php');

/**
* 
*/
class PDFEmpadronamiento extends CreadorPDF
{
    var $pdf;
    var $x;
    var $y;
    var $widths;
    var $aligns;
	
	# Documentacion para metodo "getReport" con  como parámetros
	public function getReportZonas($chartImage){
        $arrayMeses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
           'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

        $arrayDias = array( 'Domingo', 'Lunes', 'Martes',
           'Miercoles', 'Jueves', 'Viernes', 'Sabado');

         
        # Pre reset de coordenadas
        $this->pdf->Open();
        $this->pdf->AliasNbPages();
        $this->pdf->AddPage();
        $this->pdf->SetAutoPageBreak(0);

        # Crea la cabecera
        $this->MakeHeader();

        $this->x = 10;
        $this->y = 32;

        # Reasignación de coordenadas
        #$this->y -=7;
        $this->pdf->SetFont('Arial', 'B', 13);
        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->Rect($this->x, $this->y, 180, 25);
        $this->pdf->Cell(10, $this->y - 24, "Informe Filtrado:");
        
        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->SetFont('');
        $this->pdf->Cell(0, $this->y - 15, "Zonas de Salud, Areas Programaticas y Efectores");
        
        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->SetFont('Arial', 'B', 13);
        $this->pdf->Cell(0, $this->y, "Generado:");

        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->SetFont('');


        $this->pdf->Cell(0, $this->y + 10, $this->getActualDate());

        $this->y += 15;
        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->SetFont('Arial', 'B', 13);
        $this->pdf->Cell(0, $this->y + 5, "Empadronamiento agrupado por Zonas de Salud:");
        
        $this->y += 30;
        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->Image($chartImage, 5, $this->y, 190);



        $this->y += 35;
        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->Cell(0, $this->y, "Datos agrupados por Zonas de Salud:");
        
        $this->y += 63;
        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(130, 4, "Zona", 1, 0, 'C', 0);
        $this->pdf->Cell(50, 4, "Cantidad", 1, 0, 'C', 0);
        

        $this->y += 4;
        $this->pdf->SetXY($this->x, $this->y);
        
        foreach ($arrayMeses as $mes) {
            $this->pdf->SetX($this->x);
            $this->SetWidths(array(130, 50));
            $this->Row(array($mes, 10));
        }

        $this->makeFooter();
        
	}




    # Documentacion para metodo "getReport" con  como parámetros
    public function getReportAreas($chartImage){
        $arrayMeses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
           'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

        $arrayDias = array( 'Domingo', 'Lunes', 'Martes',
           'Miercoles', 'Jueves', 'Viernes', 'Sabado');

         
        # Pre reset de coordenadas
        $this->pdf->Open();
        $this->pdf->AliasNbPages();
        $this->pdf->AddPage();
        $this->pdf->SetAutoPageBreak(0);

        # Crea la cabecera
        $this->MakeHeader();

        $this->x = 10;
        $this->y = 32;

        # Reasignación de coordenadas
        #$this->y -=7;
        $this->pdf->SetFont('Arial', 'B', 13);
        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->Rect($this->x, $this->y, 180, 25);
        $this->pdf->Cell(10, $this->y - 24, "Informe Filtrado:");
        
        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->SetFont('');
        $this->pdf->Cell(0, $this->y - 15, "Zonas de Salud, Areas Programaticas y Efectores");
        
        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->SetFont('Arial', 'B', 13);
        $this->pdf->Cell(0, $this->y, "Generado:");

        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->SetFont('');


        $this->pdf->Cell(0, $this->y + 10, $this->getActualDate());

        $this->y += 15;
        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->SetFont('Arial', 'B', 13);
        $this->pdf->Cell(0, $this->y + 5, "Empadronamiento agrupado por Areas Programaticas:");
        
        $this->y += 30;
        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->Image($chartImage, 5, $this->y, 190);



        $this->y += 35;
        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->Cell(0, $this->y, "Datos agrupados por Areas Programaticas:");
        
        $this->y += 63;
        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(130, 4, "Zona", 1, 0, 'C', 0);
        $this->pdf->Cell(50, 4, "Cantidad", 1, 0, 'C', 0);
        

        $this->y += 4;
        $this->pdf->SetXY($this->x, $this->y);
        
        foreach ($arrayMeses as $mes) {
            $this->pdf->SetX($this->x);
            $this->SetWidths(array(130, 50));
            $this->Row(array($mes, 10));
        }

        $this->makeFooter();
        
    }

    # Documentacion para metodo "getReport" con  como parámetros
    public function getReportEfectores($chartImage){
        $arrayMeses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
           'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

        $arrayDias = array( 'Domingo', 'Lunes', 'Martes',
           'Miercoles', 'Jueves', 'Viernes', 'Sabado');

         
        # Pre reset de coordenadas
        $this->pdf->Open();
        $this->pdf->AliasNbPages();
        $this->pdf->AddPage();
        $this->pdf->SetAutoPageBreak(0);

        # Crea la cabecera
        $this->MakeHeader();

        $this->x = 10;
        $this->y = 32;

        # Reasignación de coordenadas
        #$this->y -=7;
        $this->pdf->SetFont('Arial', 'B', 13);
        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->Rect($this->x, $this->y, 180, 25);
        $this->pdf->Cell(10, $this->y - 24, "Informe Filtrado:");
        
        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->SetFont('');
        $this->pdf->Cell(0, $this->y - 15, "Zonas de Salud, Areas Programaticas y Efectores");
        
        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->SetFont('Arial', 'B', 13);
        $this->pdf->Cell(0, $this->y, "Generado:");

        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->SetFont('');


        $this->pdf->Cell(0, $this->y + 10, $this->getActualDate());

        $this->y += 15;
        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->SetFont('Arial', 'B', 13);
        $this->pdf->Cell(0, $this->y + 5, "Empadronamiento agrupado por Efectores de Salud:");
        
        $this->y += 30;
        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->Image($chartImage, 5, $this->y, 190);



        $this->y += 35;
        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->Cell(0, $this->y, "Datos agrupados por Efectores de Salud:");
        
        $this->y += 63;
        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(130, 4, "Zona", 1, 0, 'C', 0);
        $this->pdf->Cell(50, 4, "Cantidad", 1, 0, 'C', 0);
        

        $this->y += 4;
        $this->pdf->SetXY($this->x, $this->y);
        
        foreach ($arrayMeses as $mes) {
            $this->pdf->SetX($this->x);
            $this->SetWidths(array(130, 50));
            $this->Row(array($mes, 10));
        }

        $this->makeFooter();
        
    }






    # Documentacion para metodo "getPDF" con  como parámetros
    public function getPDF(){
        $this->pdf->Output();
    }


    # Documentacion para metodo "MakeHeader" con  como parámetros
    public function MakeHeader(){
        # Fuente de la cabecera
        $this->pdf->SetFont('Arial', 'B', 19);
        
        # Cabecera
        $this->pdf->Cell(80, 10, "Informes de empadronamiento");

        # Logos de los programas y ministerio
        $this->pdf->Image('./clases/logo_remediar.jpg', 115, 10, 55);
        $this->pdf->Image('./clases/misiones_salud.jpg', 175, 10, 25);

        # Lineas divisoria de cabecera
        $this->pdf->Line(0, 28, 250, 28);
    }

    # Documentacion para metodo "makeFooter" con  como parámetros
    public function makeFooter(){
        # Pie
        $this->pdf->SetXY(40, 290);
        $this->pdf->Line(0, 290, 250, 290);
        $this->pdf->SetFont('Arial', 'B', 7);
        $this->pdf->Cell(15, 7, "Email: remediarmasredes@gmail.com / (3764) 447967 / centrex 7967 / Tucuman 2174  Posadas, Misiones");
    }


    # Documentacion para metodo "getActualDate" con  como parámetros
    public function getActualDate(){
        $arrayMeses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
           'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
     
        $arrayDias = array( 'Domingo', 'Lunes', 'Martes',
           'Miercoles', 'Jueves', 'Viernes', 'Sabado');

        return($arrayDias[date('w')].", ".date('d')." de ".$arrayMeses[date('m')-1]." de ".date('Y'));

    }




}




?>