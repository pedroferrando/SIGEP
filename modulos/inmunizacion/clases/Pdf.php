<?php
class Pdf extends FPDF {
    private $beneficiario;
    private $logo;
    //put your code here
    function setBeneficiairo($beneficiario)
        {
        $this->beneficiario=$beneficiario;
        }
    function Header()
        {
        $this->Image('../../imagenes/sin_imagen1.jpg',176,24,17);
        $this->SetFont('Arial','B',15);                                     
        
        $this->Cell(185,10,'Inmunizacion',1,0,'C');
        $this->Ln(12);
        $this->SetFont('Arial','',10);
        
        $this->Cell(20,7,'Apellido:','LT','','R');
        $this->Cell(55,7,  $this->beneficiario->fields['apellido_benef'],'T','L');
        $this->Cell(25,7,'F. Nacimiento:','T','','R');
        $this->Cell(85,7,$this->beneficiario->fields['fecha_nacimiento_benef'],'RT','','L');
        $this->Ln();
        $this->Cell(20,7,'Nombres:','L','','R');
        $this->Cell(55,7,$this->beneficiario->fields['nombre_benef'],'','','L');
        $this->Cell(25,7,'F. Inscripcion:','','','R');
        $this->Cell(85,7,substr($this->beneficiario->fields['fecha_inscripcion'],0,10),'R','','L');
        $this->Ln();
        $this->Cell(20,7,'DNI:','LB','','R');
        $this->Cell(55,7,$this->beneficiario->fields['numero_doc'],'B','','L');
        $this->Cell(25,7,'Localidad:','B','','R');
        $this->Cell(85,7,$this->beneficiario->fields['localidad_nac'],'RB','','L');
        $this->Ln(9);
        }
   function Footer()
        {
        $this->SetY(-10);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
        }
    function TablaColores($header,$fields,$res_pdf)
        {
        $ancho=array(17,45,49,25,17,15,17);
        $alto=array(6,6,6,6,6,6,6);
        $corte=array(10,51,40,21,14,10,10);
        $alineacion=array("C","L","L","L","L","L","C");
               
        $this->SetFillColor(224,235,255);
        //$this->SetFont('','B');
        $this->SetFont('Arial','I',6);
        
        //Cabecera
        for($i=0;$i<count($header);$i++)
        $this->Cell($ancho[$i],$alto[$i],$header[$i],1,0,'C',1);
        $this->Ln();
        
        $this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        $this->SetFont('');

        $fill=false;
        while (!$res_pdf->EOF) {
            for($i=0;$i<count($fields);$i++){
                $campo=$fields[$i];
                $numero=strlen($res_pdf->fields[$campo]);
                if(strlen($res_pdf->fields[$campo])>$corte[$i]){
                    $valor=substr($res_pdf->fields[$campo],0,$corte[$i]);
                    if($i!=0 & $i!=6){
                        $valor=$valor."...";
                    }
                    
                    
                }else{
                    $valor=$res_pdf->fields[$campo];
                }
                if($valor==""|$valor=='9999-01-01'|$valor=='0'){
                    $valor="-";
                }
                $this->Cell($ancho[$i],$alto[$i],$valor,'LR',0,$alineacion[$i],$fill);
            }
            if($fill==false){
                $fill=true;
            }else{
                $fill=false;
            }
            $this->Ln();
            $res_pdf->movenext();
        }
        for($i=0;$i<count($ancho);$i++){
            $linea=$linea+$ancho[$i];
        } 
        $this->Cell($linea,0,'','T');
        } 


}

?>
