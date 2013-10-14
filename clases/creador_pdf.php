<?php
define('FPDF_FONTPATH', 'font/');

    class CreadorPDF extends FPDF{
        var $cx;
        var $cy;
        var $widths;
        var $aligns;
        /* nombre del programa. Util al momento de colocar el logo en la esquina sup. derecha
            * valores posibles: sumar | remediar | nacion | incluirsalud
        */
        var $programa; 

        function __construct($orientation='P', $unit='mm', $size='A4'){
            return parent::FPDF($orientation,$unit,$size);
        }

        function GetPrograma(){
            return $this->programa;
        }

        function SetWidths($w){
            //Set the array of column widths
            $this->widths=$w;
        }

        function SetAligns($a){
            //Set the array of column alignments
            $this->aligns=$a;
        }

        //valores posibles: sumar | remediar | nacion | incluirsalud
        function SetPrograma($p){
            $this->programa = $p;
        }

        function Row($data){
            //Calculate the height of the row
            $nb=0;
            for($i=0;$i<count($data);$i++)
                $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
            $h=5*$nb;
            //Issue a page break first if needed
            $this->CheckPageBreak($h);
            //Draw the cells of the row
            for($i=0;$i<count($data);$i++)
            {
                $w=$this->widths[$i];
                $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
                //Save the current position
                $x=$this->GetX();
                $y=$this->GetY();
                //Draw the border
                $this->Rect($x,$y,$w,$h);
                //Print the text
                $this->MultiCell($w,5,$data[$i],0,$a);
                //Put the position to the right of the cell
                $this->SetXY($x+$w,$y);
            }
            //Go to the next line
            $this->Ln($h);
        }

        function CheckPageBreak($h){
            //If the height h would cause an overflow, add a new page immediately
            if($this->GetY()+$h>$this->PageBreakTrigger){
                $this->AddPage($this->CurOrientation);
                $this->SetX($this->cx);
            }
        }

        function NbLines($w,$txt){
            //Computes the number of lines a MultiCell of width w will take
            $cw=&$this->CurrentFont['cw'];
            if($w==0)
                $w=$this->w-$this->rMargin-$this->cx;
            $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
            $s=str_replace("\r",'',$txt);
            $nb=strlen($s);
            if($nb>0 and $s[$nb-1]=="\n")
                $nb--;
            $sep=-1;
            $i=0;
            $j=0;
            $l=0;
            $nl=1;
            while($i<$nb)
            {
                $c=$s[$i];
                if($c=="\n")
                {
                    $i++;
                    $sep=-1;
                    $j=$i;
                    $l=0;
                    $nl++;
                    continue;
                }
                if($c==' ')
                    $sep=$i;
                $l+=$cw[$c];
                if($l>$wmax)
                {
                    if($sep==-1)
                    {
                        if($i==$j)
                            $i++;
                    }
                    else
                        $i=$sep+1;
                    $sep=-1;
                    $j=$i;
                    $l=0;
                    $nl++;
                }
                else
                    $i++;
            }
            return $nl;
        }

        function Header(){
            // Logo Ministerio margen izq
            $this->Image('../../imagenes/logos/logo_misiones.jpg', 5, 5, null, 22);
            // Logo Programa margen izq
            //se tiene que comprobar que esté seteado el atributo de img de programa
            if( $this->GetPrograma()!="" ){
                $programa = $this->GetPrograma();
                if($programa=="sumar")
                    $filename = '../../imagenes/logos/logo_sumar.jpg';
                if($programa=="incluirsalud")
                    $filename = '../../imagenes/logos/logo_incluirsalud.jpg';
                if($programa=="nacion")
                    $filename = '../../imagenes/logos/logo_nacion.jpg';
                if($programa=="remediar")
                    $filename = '../../imagenes/logos/logo_remediar.jpg';
                $data = getimagesize($filename);
                $width = $data[0]; $height = $data[1];
                $coordX = $this->w - $width/4 - 5;
                $this->Image($filename, $coordX, 5, null, 20);
            }
            // Salto de línea
            $this->Ln(18);
        }

        function Footer(){
            global $_ses_user;
            // Posición: a 1 cm del final
            $this->SetXY(5,-10);
            // Arial italic 8
            $this->SetFont('Arial','I',8);
            // login y fecha de impresion
            $login_fecha = "[".$_ses_user[login]." - ".date('d/m/Y H:i')." hs.]";
            $this->Cell(75,5,$login_fecha,0,0,'L');
            // Número de página
            $this->Cell(125,5,$this->PageNo().'/{nb}',0,0,'R');
        }

    }
        
?>
