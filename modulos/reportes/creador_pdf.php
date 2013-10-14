<?php
define('FPDF_FONTPATH', 'font/');

    class CreadorPDFReportes extends CreadorPDF{

        function __construct($orientation='P', $unit='mm', $size='A4'){
            return parent::__construct($orientation,$unit,$size);
        }

        public function getReporteCoberturaCEB($request,$result){
            /* 
            request son los parametros de $_REQUEST
            result es un result set
            */
            $this->SetMargins(5, 12, 5);
            $this->cx = 5;
            $this->cy = 25;
            $this->Open();
            $this->AliasNbPages();
            $this->AddPage();
            $this->SetAutoPageBreak(1,8);

            $this->SetFont('Arial', 'B', 9);

            switch($request[solapa]){
                case "rojo":
                    $title = "Listado de beneficiarios con CEB vencida - Total: ".$result->RecordCount()."";
                    break;
                case "amarillo":
                    $title = "Listado de beneficiarios con CEB a vencerse - Total: ".$result->RecordCount()."";
                    break;
                case "verde":
                    $title = "Listado de beneficiarios con CEB - Total: ".$result->RecordCount()."";
                    break;
                case "estadistica":
                    $title = "Datos Estadisticos";
                    break;
                default:
                    break;
            }
            $title .= " - CUIE: ".$request[cuie];
            $this->SetXY($this->cx, $this->cy);
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(80, 20, $title);
            $this->cy+=7;
            $this->SetXY($this->cx, $this->cy);

            if($request[solapa]=="rojo" || $request[solapa]=="amarillo" || $request[solapa]=="verde"){
                //grafico tabla con datos de beneficiarios
                $this->graficarListadoCoberturaCEB($result);
            }else{
                //grafico tabla de datos estadisticos
                $this->graficarEstadisticaCoberturaCEB($result);
            }        

            $this->Output();
        }

        public function graficarFilasEstadisticaCoberturaCEB($result,$campo){
            $arr_col_width = array(175,25);
            $arr_col_cont = array("menores a 6","entre 6 y 9","adolescentes","mujeres de 20 a 69","sin clasificar");
            $arr_campo = array("a","b","c","d","vacio");
            $this->SetFont('Arial', '', 8);
            $c=0;
            foreach($arr_col_cont as $v){
                $this->SetX($this->cx);
                $this->SetWidths($arr_col_width);
                $this->SetAligns(array('L','R'));
                $this->Row(array($v,$result->fields[$campo."_".$arr_campo[$c]]));
                $c++;
            }
        }

        public function graficarEstadisticaCoberturaCEB($result){
            $arr_col_width = array(175,25);
            //cabecera de la tabla
            $this->cy+=8;
            $this->SetXY($this->cx, $this->cy);
            $this->SetFont('Arial', 'B', 8);
            $this->SetWidths($arr_col_width);
            $this->SetAligns(array('C','C'));
            $this->Row(array("","Total"));
            unset($this->aligns);

            $arr_col_cont[] = array("Total de personas activas con CEB","verde_activo");
            $arr_col_cont[] = array("Total de personas no activas con CEB","verde_inactivo");
            $arr_col_cont[] = array("Total de personas activas con CEB vencida","rojo_activo");
            $arr_col_cont[] = array("Total de personas no activas con CEB vencida","rojo_inactivo");
            $arr_col_cont[] = array("Total de personas activas con CEB proximo a vencerse","amarillo_activo");
            $arr_col_cont[] = array("Total de personas no activas con CEB proximo a vencerse","amarillo_inactivo");

            foreach($arr_col_cont as $k => $v){
                $this->SetFont('Arial', 'B', 8);
                $this->SetX($this->cx);
                $this->SetWidths($arr_col_width);
                $this->SetAligns(array('L','R'));
                $this->Row(array($v[0],$result->fields[$v[1]]));
                //grafico los renglones de valores poblacionales
                $this->graficarFilasEstadisticaCoberturaCEB($result,$v[1]);
            }
        }

        public function graficarListadoCoberturaCEB($result){
            //cabecera de la tabla
            $this->cy+=8;
            $this->SetXY($this->cx, $this->cy);
            $this->SetFont('Arial', 'B', 8);
            $this->SetWidths(array(38,12,18,31,12,12,17,32,6,22));
            $this->SetAligns(array('C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C'));
            $this->Row(array("Apellido y Nombre","Fecha Nac","Tipo y Nro Doc","Direccion","Fecha Insc",
                            "Fecha Ult. Prest.","Cod. de Prest.","Lugar de Prestacion","Ac","Mensaje Baja"));
            unset($this->aligns);

            //cuerpo de la tabla
            if(isset($result) && $result->RecordCount()>0){
                $this->SetFont('Arial', '', 7);
                $arr_col_width = array(38,12,18,31,12,12,17,32,6,22);
                $arr_col_cont = array();
                while(!$result->EOF){
                    $this->SetX($this->cx);
                    $arr_col_cont[] = $result->fields['afiapellido']." ".$result->fields['afinombre']." ".$this->pie;
                    $arr_col_cont[] = date('d/m/y',strtotime($result->fields['afifechanac']));
                    $arr_col_cont[] = $result->fields['afitipodoc']." ".$result->fields['afidni'];
                    $arr_col_cont[] = "CALLE: ".utf8_decode($result->fields['afidomcalle'])." Nº: ".$result->fields['afidomnro']." ".$result->fields['afidompiso']." ".$result->fields['afidompiso']; 
                    $arr_col_cont[] = date('d/m/y',strtotime($result->fields['fechainscripcion']));
                    $arr_col_cont[] = $result->fields['fechaultimaprestacion']!="" ? date('d/m/y',strtotime($result->fields['fechaultimaprestacion'])) : "";
                    $arr_col_cont[] = $result->fields['codigoprestacion'];
                    $arr_col_cont[] = $result->fields['cuie']!="" ? $result->fields['cuie']." - ".$result->fields['lugar_prestacion'] : "";
                    $arr_col_cont[] = $result->fields['activo']=='S' ? 'SI' : 'NO';
                    $arr_col_cont[] = $result->fields['mensajebaja'];
                    $this->SetWidths($arr_col_width);
                    $this->Row($arr_col_cont);
                    unset($arr_col_cont);
                    $result->MoveNext();
                }
            }
        }


    }

?>
