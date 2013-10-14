<?php
define('FPDF_FONTPATH', 'font/');

class CreadorPDFInmunizacion extends CreadorPDF{
    var $aux;
    var $x2;
    var $y2;
    
    function __construct($orientation='P', $unit='mm', $size='A4'){
        return parent::FPDF($orientation,$unit,$size);
    }
    
    function initPDFInmunizacion($periodo,$efector="",$fecha_liq=""){
        $this->aux = array();
        $this->cx = 5;
        $this->x2 = 100;
        $this->cy = 25;
        $this->y2 = 100;
        $this->Open();
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(1,8);

        $this->SetXY($this->cx, $this->cy);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(80, 20, "Resumen Mensual de Inmunizaciones - ".$periodo);
        $this->cy+=15;  
        $this->SetFont('Arial', '', 9);
        $this->SetXY($this->cx, $this->cy);
        $this->Cell(85, 6, "Departamento: ".$efector->getDepartamentoNombre(), 0, 0, 'L', 0);
        $this->Cell(75, 6, "Localidad: ".$efector->getLocalidadNombre(), 0, 0, 'L', 0);
        if($fecha_liq!="")
            $this->Cell(20, 6, "Fecha liq: ".date('d/m/Y',strtotime($fecha_liq)), 0, 0, 'L', 0);
        $this->SetXY($this->cx,$this->cy+6);
        $this->Cell(85, 6, "Establecimiento: ".$efector->getNombreefector(), 0, 0, 'L', 0);
        $this->SetXY($this->cx,$this->cy+12);
        $this->Cell(85, 6, "Zona de Salud: ".$efector->getZonaSanitaria(), 0, 0, 'L', 0);
        
        $this->Ln(15);
        
    }
    
    function graficarTablaInmunizacion($i,$vacuna,$fil_primera,$col_primera,$matriz){
        if($i>0 && ($this->aux['cols_prev'][$i-1] + $fil_primera->RecordCount()) <= 5 && $fil_primera->RecordCount()<=3 ){
            $coordX = 100;
            if($this->aux['side'][$i-1] == "left"){
                $this->SetY($this->aux['y_prev_start']);
                if($this->aux['cols_prev'][$i-1]>2){
                    $coordX += 27;
                }
            }elseif($this->aux['side'][$i-2] == "left" && $this->aux['cols_prev'][$i-2]>2){
                $coordX += 27;
            }
            $this->aux['side'][$i] = "right";
        }else{
            $coordX = 5;
            $this->aux['side'][$i] = "left";
            if($i>0){
                if($fil_primera->RecordCount()==4 && $this->aux['side'][$i-1] == "left" && $this->aux['cols_prev'][$i-1]==1){
                    $coordX = 100 - 27;
                    $this->aux['side'][$i] = "right";
                    $this->SetY($this->aux['y_prev_start']);
                }
            }
        }
        if($this->aux['side'][$i]=="left"){
            $this->x = 5;
        }else{
            $this->x = 100;
        }
        $width = 27;
        $arr_w = array();
        $arr_c = array();
        
        $this->aux['cols_prev'][$i] = $fil_primera->RecordCount();
        $this->aux['y_prev_start'] = $this->GetY();
        $this->SetX($coordX);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(100, 5, $vacuna[$i]['nombre'], 0, 0);
        $this->Ln(5);
        $this->SetX($coordX);
        $this->SetFont('Arial', 'B', 8);
        $arr_w[] = $width;
        $arr_c[] = "Edad/Cond ";
        while(!$fil_primera->EOF){ 
                if($fil_primera->fields['descripcion_abreviada']!=""){
                    $desc = $fil_primera->fields['descripcion_abreviada']; 
                }else{
                    $desc = $fil_primera->fields['descripcion'];
                }
                $arr_w[] = $width;
                $arr_c[] = $desc;
            $fil_primera->MoveNext();
        }
        $this->SetX($coordX);
        $this->SetWidths($arr_w);
        $this->Row($arr_c);
        unset($arr_c);
        for($k=0;$k<count($col_primera);$k++){ 
            $fil_primera->MoveFirst();
            $arr_c[] = $col_primera[$k];
            while(!$fil_primera->EOF){
                $arr_c[] = $matriz[$k][$fil_primera->fields['id_vacuna_dosis']];
                $fil_primera->MoveNext();
            }
            $this->SetX($coordX);
            $this->SetWidths($arr_w);
            $this->Row($arr_c);
            unset($arr_c);
        } 
        $this->y2 = $coordY;
        $this->aux['y_prev_end'] = $this->GetY();
        $this->Ln(5);
        $this->SetX($coordX);
    }
    
    function pieReporteInmunizacion(){
        $this->SetFont('Arial','',8);
        $this->SetXY(150,-27);
        $this->Cell(50,5,'........................................',0,0,'C');
        $this->SetXY(150,-23);
        $this->Cell(50,5,'Responsable',0,0,'C');
        $this->SetXY(150,-20);
        $this->Cell(50,5,'Firma y Aclaración',0,0,'C');
    }
    
}
?>
