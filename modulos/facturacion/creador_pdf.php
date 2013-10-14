<?php
define('FPDF_FONTPATH', 'font/');

    class CreadorPDFFacturacion extends CreadorPDF{
                
        function __construct($orientation='P', $unit='mm', $size='A4'){
            return parent::__construct($orientation,$unit,$size);
        }
        
        /* $params es un array con una estructura de 3 campos:
         * campo 1 -> cuie de efector
         * campo 2 -> nombre de efector
         * campo 3 -> resultSet de debitos retroactivos del efector
        */
        public function getReporteDebitosRetroactivos($nroExpte,$params){
            $this->SetMargins(5, 12, 5);
            $this->cx = 5;
            $this->cy = 25;
            $this->Open();
            $this->AliasNbPages();
            $this->AddPage();
            $this->SetAutoPageBreak(1,8);

            $title .= " Debitos Retroactivos - Expediente Nro: ".$nroExpte;
            $this->SetXY($this->cx, $this->cy);
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(80, 20, $title);
            
            foreach($params as $p){
                $this->Ln();
                $this->SetFont('Arial', 'B', 10);
                $this->SetX($this->x);
                $this->Cell(200, 7, "CUIE: ".$p[cuie]." - ".$p[nombre], 0, 0, 'L', 0);
                $this->Ln();
                
                $this->SetFont('Arial', 'B', 9);
                $this->SetWidths(array(20,18,35,15,20,12,40,40));
                $this->SetAligns(array('C','C','C','C','C','C','C','C'));
                $this->Row(array("Nro Exp","NroFact.","Ap. y Nombre","F. Prest.","Codigo","Precio","Observaciones","Motivo"));
                $this->SetFont('Arial', '', 8);
                $prestDeb = $p['result'];
                while(!$prestDeb->EOF){
                    $this->SetX($this->x);
                    $this->SetWidths(array(20,18,35,15,20,12,40,40));
                    $this->SetAligns(array('L','L','L','C','C','R','L','L'));
                    $arrDatos[] = $prestDeb->fields['exp_prest'];
                    $arrDatos[] = $prestDeb->fields['nro_fact_offline'];
                    $arrDatos[] = $prestDeb->fields['apellido_benef']." ".$prestDeb->fields['apellido_benef_otro']." ".$prestDeb->fields['nombre_benef']." ".$prestDeb->fields['nombre_benef_otro'];
                    $arrDatos[] = date('d/m/y', strtotime($prestDeb->fields['fecha_comprobante']));
                    $arrDatos[] = $prestDeb->fields['codigo']." ".$prestDeb->fields['diagnostico'];
                    $arrDatos[] = '$ '.$prestDeb->fields['precio_prestacion']*$prestDeb->fields['cantidad']; 
                    $arrDatos[] = $prestDeb->fields['observaciones'];
                    $arrDatos[] = $prestDeb->fields['motivo'];
                    $this->Row($arrDatos);
                    unset($arrDatos);
                    $prestDeb->MoveNext();
                }
                
            }
            
            $this->Output();
        }

        function getReportePrestacionesDOMs($beneficiario,$prestaciones,$sql_trazadoras="",$data_alt=""){
            /* 
            beneficiario es un obj Beneficiario
            prestaciones es un result set
            sql_trazadoras es un array con querys de trazadoras
            */
            $this->SetMargins(5, 12, 5);
            $this->cx = 5;
            $this->cy = 25;
            $this->Open();
            $this->AliasNbPages();
            $this->AddPage();
            $this->SetAutoPageBreak(1,8);

            $this->SetXY($this->cx, $this->cy);
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(80, 20, "Reporte de Prestaciones y DOMs");
            $this->cy+=15;
            $this->SetXY($this->cx, $this->cy);
            
            $this->SetFont('Arial', 'B', 9);
            
            $datos_benef = "Benef: [".$beneficiario->clave."] ".$beneficiario->nombre;
            $datos_benef .= " - DNI: ".$beneficiario->nro_doc;
            $datos_benef .= " - Fec. Nac: ".$beneficiario->fechaNac;
            $this->Cell(200, 8, $datos_benef, 1, 0, 'L', 0);
            $this->Ln(0);

            $this->cy+=8;
            $this->SetXY($this->cx, $this->cy);
            $this->Cell(20, 6, "Fecha Prest.", 1, 0, 'C', 0);
            $this->Cell(74, 6, "Lugar de Realización", 1, 0, 'C', 0);
            $this->Cell(22, 6, "Código", 1, 0, 'C', 0);
            $this->Cell(84, 6, "Descripción", 1, 0, 'C', 0);

            $this->SetFont('Arial', '', 8);
            $this->Ln();
            $c_prest = 0;
            while(!$prestaciones->EOF){
                $this->SetX($this->x);
                $fecha = date('d/m/Y',strtotime($prestaciones->fields['fecha_comprobante']));
                $lugar =  $prestaciones->fields['nombreefector'];
                $nomenclador = $prestaciones->fields['cod_nomenclador'];
                if($prestaciones->fields['desc_descripcion']!=""){
                    $descripcion =  trim($prestaciones->fields['desc_descripcion']);
                }elseif($prestaciones->fields['descripcion']!=""){
                    $descripcion =  trim(strtoupper($prestaciones->fields['descripcion']));
                }else{
                    $descripcion = getNombreGenericoPrestacion($prestaciones->fields['trz']);
                }
                $this->SetWidths(array(20,74,22,84));
                $this->Row(array($fecha,$lugar,$nomenclador,$descripcion));
                unset($descripcion);
                $prestaciones->MoveNext();
                $c_prest++;
            }
            $this->SetX($this->x);
            $this->Cell(200, 6, "TOTAL: ".$c_prest, 1, 0, 'L', 0);
            $this->Ln();

            if(isset($sql_trazadoras) && $sql_trazadoras!=""){
                $this->Ln();
                $this->SetFont('Arial', 'B', 10);
                $this->SetX($this->x);
                $this->Cell(200, 7, "Informe de DOMs", 1, 0, 'L', 0);

                $c = 0;
                foreach($sql_trazadoras as $key => $value){
                    $this->SetX($this->x);
                    $this->SetFont('Arial', 'B', 9);
                    //$this->pdf->Cell(200, 6, $key, 1, 0, 'L', 0); <- Titulo de la Trazadora
                    $this->graficarTablaInfoTrazadora($key,$value,$data_alt,$c);
                    //$c++;
                    if($key!=$key_prev){
                        $c = 0;
                    }
                    $key_prev = $key;
                }
            }

            $this->Output();
        }

        function graficarTablaInfoTrazadora($trazadora,$queries,$data_alt,$idx){
            switch($trazadora){
                case 'ADOLESCENTE':
                    $this->graficarTablaInfoAdolescente($queries,$data_alt,$idx);
                    break;
                case 'ADULTO':
                    $this->graficarTablaInfoAdulto($queries,$data_alt,$idx);
                    break;
                case 'EMB':
                    $this->graficarTablaInfoEmbarazo($queries,$data_alt,$idx);
                    break;
                case 'INMU':
                    $this->graficarTablaInfoInmunizacion($queries,$data_alt,$idx);
                    break;
                case 'NINO':
                case 'NINO_PESO':
                    $this->graficarTablaInfoNino($queries,$data_alt,$idx);
                    break;            
                case 'PARTO':
                    $this->graficarTablaInfoParto($queries,$data_alt,$idx);
                    break;
                case 'TAL':
                    $this->graficarTablaInfoTal($queries,$data_alt,$idx);
                    break;
            }
            //$this->pdf->Ln();
        }

        function graficarTablaInfoAdolescente($q,$alt,$idx){
            $this->Ln();
            $this->SetX($this->x);
            $this->SetFont('Arial', 'B', 9);
            $this->SetWidths(array(18,72,22,10,10,12,10,19,13,14));
            $this->SetAligns(array('C','C','C','C','C','C','C','C','C','C'));
            $this->Row(array("F. Control","Lugar","Código","Sexo","Peso","Talla","Imc","Perc. Imc Edad","TA Min","TA Max"));
            unset($this->aligns);
            foreach($q as $v){
                if($v!=$sql_prev){
                    $result = sql($v) or die;
                    if($result){
                        $this->SetFont('Arial', '', 8);
                            while(!$result->EOF){
                                $this->SetX($this->x);
                                $fecha = date('d/m/Y',strtotime($result->fields['fecha_control']));
                                $lugar =  $result->fields['efector'];
                                $nomenclador = $result->fields['codnomenclador'];
                                $sexo = $result->fields['sexo'];
                                $peso = number_format($result->fields['peso'],2);
                                $talla = number_format($result->fields['talla'],2);
                                $imc = $result->fields['imc'];
                                $perc_imc_edad = $result->fields['percen_imc_edad'];
                                $tamin = $result->fields['tamin'];
                                $tamax = $result->fields['tamax'];
                                $observaciones = $result->fields['observaciones'];
                                $this->SetWidths(array(18,72,22,10,10,12,10,19,13,14));
                                $this->Row(array($fecha,$lugar,$nomenclador,$sexo,$peso,$talla,$imc,$perc_imc_edad,$tamin,$tamax,$observaciones));
                                $result->MoveNext();
                            }
                            if($result->_numOfRows==0){
                                $this->SetX($this->x);
                                $datos = $alt["ADOLESCENTE"][$idx];
                                $fecha = date('d/m/Y',strtotime($datos['fecha_prestacion']));
                                $lugar =  $datos['efector'];
                                $nomenclador =  $datos['cod_nomenclador'];
                                $this->SetWidths(array(18,72,22,88));
                                $this->Row(array($fecha,$lugar,$nomenclador,"Sin datos"));
                            }
                    }
                }
                $sql_prev = $v;
                $idx++;
            }
        }

        function graficarTablaInfoAdulto($q,$alt,$idx){
            $this->Ln();
            $this->SetX($this->x);
            $this->SetFont('Arial', 'B', 9);
            $this->Cell(18, 6, "F. Control", 1, 0, 'C', 0);
            $this->Cell(77, 6, "Lugar", 1, 0, 'C', 0);
            $this->Cell(22, 6, "Código", 1, 0, 'C', 0);
            $this->Cell(9, 6, "Sexo", 1, 0, 'C', 0);
            $this->Cell(9, 6, "Peso", 1, 0, 'C', 0);
            $this->Cell(14, 6, "TA Min", 1, 0, 'C', 0);
            $this->Cell(14, 6, "TA Max", 1, 0, 'C', 0);
            $this->Cell(37, 6, "Observac.", 1, 0, 'C', 0);
            $this->Ln();
            foreach($q as $v){
                if($v!=$sql_prev){
                    $result = sql($v) or die;
                    if($result){
                        $this->SetFont('Arial', '', 8);
                            while(!$result->EOF){
                                $this->SetX($this->x);
                                $fecha = date('d/m/Y',strtotime($result->fields['fecha_control']));
                                $lugar =  $result->fields['efector'];
                                $nomenclador = $result->fields['codnomenclador'];
                                $sexo = $result->fields['sexo'];
                                $peso = $result->fields['peso'];
                                $tamin = $result->fields['tamin'];
                                $tamax = $result->fields['tamax'];
                                $observaciones = $result->fields['observaciones'];
                                $this->SetWidths(array(18,77,22,9,9,14,14,37));
                                $this->Row(array($fecha,$lugar,$nomenclador,$sexo,$peso,$tamin,$tamax,$observaciones));
                                $result->MoveNext();
                            }
                            if($result->_numOfRows==0){
                                $this->SetX($this->x);
                                $datos = $alt["ADULTO"][$idx];
                                $fecha = date('d/m/Y',strtotime($datos['fecha_prestacion']));
                                $lugar =  $datos['efector'];
                                $nomenclador =  $datos['cod_nomenclador'];
                                $this->SetWidths(array(18,77,22,83));
                                $this->Row(array($fecha,$lugar,$nomenclador,"Sin datos"));
                            }
                    }
                }
                $sql_prev = $v;
                $idx++;
            }
        }

        function graficarTablaInfoInmunizacion($q,$alt,$idx){
            $this->Ln();
            $this->SetX($this->x);
            $this->SetFont('Arial', 'B', 8);
            $this->Cell(17, 6, "F. Inmun", 1, 0, 'C', 0);
            $this->Cell(40, 6, "Lugar", 1, 0, 'C', 0);
            $this->Cell(19, 6, "Código", 1, 0, 'C', 0);
            $this->Cell(40, 6, "Dosis", 1, 0, 'C', 0);
            $this->Cell(20, 6, "Laboratorio", 1, 0, 'C', 0);
            $this->Cell(18, 6, "Presentac", 1, 0, 'C', 0);
            $this->Cell(10, 6, "Lote", 1, 0, 'C', 0);
            $this->Cell(17, 6, "Fecha Venc", 1, 0, 'C', 0);
            $this->Cell(19, 6, "G R", 1, 0, 'C', 0);
            $this->Ln();
            foreach($q as $v){
                if($v!=$sql_prev){
                    $result = sql($v) or die;
                    if($result){
                        $this->SetFont('Arial', '', 8);
                            while(!$result->EOF){
                                $this->SetX($this->x);
                                $fecha = date('d/m/Y',strtotime($result->fields['fecha_inmunizacion']));
                                $lugar =  $result->fields['efector'];
                                $nomenclador = $result->fields['categoria']." ".$result->fields['codigo']." ".$result->fields['patologia'];
                                $dosis = $result->fields['desc_dosis'];
                                $laboratorio = $result->fields['laboratorio'];
                                $presentacion = $result->fields['desc_presentacion'];
                                $lote = $result->fields['lote'];
                                if($result->fields['fecha_vencimiento']!='9999-01-01 00:00:00'){
                                    $fecha_venc = date('d/m/Y',strtotime($result->fields['fecha_vencimiento']));
                                }else{
                                    unset($fecha_venc);
                                }
                                $grupo_riesgo = $result->fields['desc_grupo_riesgo'];
                                $this->SetWidths(array(17,40,19,40,20,18,10,17,19));
                                $this->Row(array($fecha,$lugar,$nomenclador,$dosis,$laboratorio,$presentacion,$lote,$fecha_venc,$grupo_riesgo));
                                $result->MoveNext();
                            }
                            if($result->_numOfRows==0){
                                $this->SetX($this->x);
                                $datos = $alt["INMU"][$idx];
                                $fecha = date('d/m/Y',strtotime($datos['fecha_prestacion']));
                                $lugar =  $datos['efector'];
                                $nomenclador =  $datos['cod_nomenclador'];
                                $this->SetWidths(array(17,40,19,124));
                                $this->Row(array($fecha,$lugar,$nomenclador,"Sin datos"));
                            }
                    }
                }
                $sql_prev = $v;
                $idx++;
            }
        }

        function graficarTablaInfoEmbarazo($q,$alt,$idx){
            $this->Ln();
            $this->SetX($this->x);
            $this->SetFont('Arial', 'B', 9);
            $this->Cell(18, 6, "F. Control", 1, 0, 'C', 0);
            $this->Cell(60, 6, "Lugar", 1, 0, 'C', 0);
            $this->Cell(15, 6, "Sem Gest", 1, 0, 'C', 0);
            $this->Cell(18, 6, "Fum", 1, 0, 'C', 0);
            $this->Cell(18, 6, "Fpp", 1, 0, 'C', 0);
            $this->Cell(18, 6, "F. 1º Ctrl", 1, 0, 'C', 0);
            $this->Cell(10, 6, "Alt Ut.", 1, 0, 'C', 0);
            $this->Cell(10, 6, "Peso", 1, 0, 'C', 0);
            $this->Cell(9, 6, "Talla", 1, 0, 'C', 0);
            $this->Cell(12, 6, "TA Min", 1, 0, 'C', 0);
            $this->Cell(12, 6, "TA Max", 1, 0, 'C', 0);
            $this->Ln();
            foreach($q as $v){
                if($v!=$sql_prev){
                    $result = sql($v) or die;
                    if($result){
                        $this->SetFont('Arial', '', 8);
                            while(!$result->EOF){
                                $this->SetX($this->x);
                                $fecha = date('d/m/Y',strtotime($result->fields['fecha_control']));
                                $lugar =  $result->fields['efector'];
                                $sem_gest = number_format($result->fields['sem_gestacion'],2);
                                $fum = date('d/m/Y',strtotime($result->fields['fum']));
                                $fpp = date('d/m/Y',strtotime($result->fields['fpp']));
                                $fpcp = date('d/m/Y',strtotime($result->fields['fpcp']));
                                $talla_uterina = $result->fields['altura_uterina'];
                                $peso = $result->fields['peso_embarazada'];
                                $talla = $result->fields['talla'];
                                if($result->fields['tension_arterial_minima']!=""){
                                    $tamin = $result->fields['tension_arterial_minima'];
                                }elseif($result->fields['tamin']!=""){
                                    $tamin = $result->fields['tamin'];
                                }
                                if($result->fields['tension_arterial_maxima']!=""){
                                    $tamax = $result->fields['tension_arterial_maxima'];
                                }elseif($result->fields['tamax']){
                                    $tamax = $result->fields['tamax'];
                                }
                                $this->SetWidths(array(18,60,15,18,18,18,10,10,9,12,12));
                                $this->Row(array($fecha,$lugar,$sem_gest,$fum,$fpp,$fpcp,$talla_uterina,$peso,$talla,$tamin,$tamax));
                                $result->MoveNext();
                            }
                            if($result->_numOfRows==0){
                                $this->SetX($this->x);
                                $datos = $alt["EMB"][$idx];
                                $fecha = date('d/m/Y',strtotime($datos['fecha_prestacion']));
                                $lugar =  $datos['efector'];
                                $this->SetWidths(array(18,60,122));
                                $this->Row(array($fecha,$lugar,"Sin datos"));
                            }
                    }
                }
                $sql_prev = $v;
                $idx++;
            }
        }

        function graficarTablaInfoNino($q,$alt,$idx){
            $this->Ln();
            $this->SetX($this->x);
            $this->SetFont('Arial', 'B', 8);
            $this->SetWidths(array(17,40,20,9,9,9,10,10,10,10,10,10,10,10,8,8));
            $this->SetAligns(array('C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C'));
            $this->Row(array("F. Control","Lugar","Código","Edad","Peso","Talla",
                            "Perc Peso Edad","Perc Talla Edad","Perim Cef",
                            "Perc Perim Cef Edad","Imc","Perc. Imc Edad",
                            "Perc Peso Talla","Perc Imc","TA Min","TA Max"));
            unset($this->aligns);
            foreach($q as $v){
                if($v!=$sql_prev){
                    $result = sql($v) or die;
                    if($result){
                        $this->SetFont('Arial', '', 8);
                            while(!$result->EOF){
                                $this->SetX($this->x);
                                $fecha = date('d/m/Y',strtotime($result->fields['fecha_control']));
                                $lugar =  $result->fields['efector'];
                                $nomenclador = $result->fields['cod_nomenclador'];
                                $edad = $result->fields['fecha_control']-$result->fields['fecha_nac']." A"; //$result->fields['nino_edad'];
                                $peso = number_format($result->fields['peso'],2);
                                $talla = number_format($result->fields['talla'],2);
                                $perc_peso_edad = $result->fields['percen_peso_edad'];
                                $perc_talla_edad = $result->fields['percen_talla_edad'];
                                $perim_cef = number_format($result->fields['perim_cefalico'],2);
                                $perc_perim_cef_edad = $result->fields['percen_perim_cefali_edad'];
                                $imc = $result->fields['imc'];
                                $perc_imc_edad = $result->fields['percen_imc_edad'];
                                $perc_peso_talla = $result->fields['percen_peso_talla'];
                                $perc_imc = $result->fields['percentilo_imc'];
                                $tamin = $result->fields['tamin'];
                                $tamax = $result->fields['tamax'];
                                $this->SetWidths(array(17,40,20,9,9,9,10,10,10,10,10,10,10,10,8,8));
                                $this->Row(array($fecha,$lugar,$nomenclador,$edad,$peso,$talla,$perc_peso_edad,$perc_talla_edad,$perim_cef,
                                                $perc_perim_cef_edad,$imc,$perc_imc_edad,$perc_peso_talla,$perc_imc,$tamin,$tamax));
                                $result->MoveNext();
                            }
                            if($result->_numOfRows==0){
                                $this->SetX($this->x);
                                $datos = $alt["NINO"][$idx];
                                $fecha = date('d/m/Y',strtotime($datos['fecha_prestacion']));
                                $lugar =  $datos['efector'];
                                $nomenclador = $datos['cod_nomenclador'];
                                $this->SetWidths(array(17,40,20,123));
                                $this->Row(array($fecha,$lugar,$nomenclador,"Sin datos"));
                            }
                    }
                }
                $sql_prev = $v;
                $idx++;
            }
        }

        function graficarTablaInfoParto($q,$alt,$idx){
            $this->Ln();
            $this->SetX($this->x);
            $this->SetFont('Arial', 'B', 9);
            $this->Cell(20, 6, "F. Parto", 1, 0, 'C', 0);
            $this->Cell(80, 6, "Lugar", 1, 0, 'C', 0);
            $this->Cell(20, 6, "Apgar", 1, 0, 'C', 0);
            $this->Cell(20, 6, "Peso", 1, 0, 'C', 0);
            $this->Cell(18, 6, "Vdrl", 1, 0, 'C', 0);
            $this->Cell(20, 6, "Talla RN", 1, 0, 'C', 0);
            $this->Cell(22, 6, "Perim Cef RN", 1, 0, 'C', 0);
            $this->Ln();
            foreach($q as $v){
                if($v!=$sql_prev){
                    $result = sql($v) or die;
                    if($result){
                        $this->SetFont('Arial', '', 8);
                            while(!$result->EOF){
                                $this->SetX($this->x);
                                $fecha = date('d/m/Y',strtotime($result->fields['fecha_parto']));
                                $lugar =  $result->fields['efector'];
                                $apgar = number_format($result->fields['apgar'],2);
                                $peso = number_format($result->fields['peso'],3);
                                $vdrl = $result->fields['vdrl'];
                                if($result->fields['talla_rn']!="")
                                    $talla_rn = number_format($result->fields['talla_rn'],2);
                                if($result->fields['perimcef_rn']!="")
                                    $perim_cef_rn = number_format($result->fields['perimcef_rn'],2);
                                $this->SetWidths(array(20,80,20,20,18,20,22));
                                $this->Row(array($fecha,$lugar,$apgar,$peso,$vdrl,$talla_rn,$perim_cef_rn));
                                $result->MoveNext();
                            }
                            if($result->_numOfRows==0){
                                $this->SetX($this->x);
                                $datos = $alt["PARTO"][$idx];
                                $fecha = date('d/m/Y',strtotime($datos['fecha_prestacion']));
                                $lugar =  $datos['efector'];
                                $this->SetWidths(array(20,80,100));
                                $this->Row(array($fecha,$lugar,"Sin datos"));
                            }
                    }
                }
                $sql_prev = $v;
            }
        }

        function graficarTablaInfoTal($q){
            $this->Ln();
            $this->SetX($this->x);
            $this->SetFont('Arial', 'B', 9);
            $this->Cell(20, 6, "F. Control", 1, 0, 'C', 0);
            $this->Cell(120, 6, "Lugar", 1, 0, 'C', 0);
            $this->Cell(20, 6, "Código", 1, 0, 'C', 0);
            $this->Cell(20, 6, "Sexo", 1, 0, 'C', 0);
            $this->Cell(20, 6, "Tal", 1, 0, 'C', 0);
            $this->Ln();
            foreach($q as $v){
                if($v!=$sql_prev){
                    $result = sql($v) or die;
                    if($result){
                        $this->SetFont('Arial', '', 8);
                            while(!$result->EOF){
                                $this->SetX($this->x);
                                $fecha = date('d/m/Y',strtotime($result->fields['fecha_control']));
                                $lugar =  $result->fields['efector'];
                                $nomenclador = $result->fields['codnomenclador'];
                                $sexo = $result->fields['sexo'];
                                $tal = $result->fields['tal'];
                                $talla_rn = number_format($result->fields['talla_rn'],2);
                                $perim_cef_rn = number_format($result->fields['perim_cef_rn'],2);
                                $this->SetWidths(array(20,120,20,20,20,20));
                                $this->Row(array($fecha,$lugar,$nomenclador,$sexo,$tal));
                                $result->MoveNext();
                            }
                            if($result->_numOfRows==0){
                                $this->SetX($this->x);
                                $datos = $alt["TAL"][$idx];
                                $fecha = date('d/m/Y',strtotime($datos['fecha_prestacion']));
                                $lugar =  $datos['efector'];
                                $nomenclador =  $datos['cod_nomenclador'];
                                $this->SetWidths(array(20,120,20,40));
                                $this->Row(array($fecha,$lugar,$nomenclador,"Sin datos"));
                            }
                    }
                }
                $sql_prev = $v;
            }
        }  

    }

?>
