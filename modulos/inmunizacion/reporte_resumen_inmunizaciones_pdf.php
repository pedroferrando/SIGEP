<?php
    require_once("../../config.php");
    require_once('../../lib/fpdf.php');
    require_once('../../lib/funciones_misiones.php');
    require_once("../../clases/beneficiarios.php");
    require_once("../../clases/creador_pdf.php");
    require_once("../../clases/Efector.php");
    require_once("./clases/Vacuna.php");
    require_once("./creador_pdf.php");
    require_once("./inmunizacion_funciones.php");
    
    header("Cache-Control: no-cache, must-revalidate");
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    
    $request = decode_link($_REQUEST['p']);
    
    $efector = new Efector();
    $vacuna = new Vacuna();
    
    $vacunas_reporte = getVacunasReporte();
    $mes_selected = $request[mes];
    $anio_selected = $request[anio];
    $periodo = getNombreMes($mes_selected)." ".$anio_selected;
    $sql = getSQLFechaLiquidacion($request[liquidacion]);
    $r_fecha_liq = sql($sql);
    if($r_fecha_liq){
        $r_fecha_liq->MoveFirst();
        $fecha_liq = $r_fecha_liq->fields['fecha'];
    }
    
    if(isset($request[cuie]) && $request[cuie]!=""){
        $pdf = new CreadorPDFInmunizacion();
        $efector->FiltrarCuie(strtoupper($request[cuie]));
        $pdf->initPDFInmunizacion($periodo,$efector,$fecha_liq);
        
                for($i=0;$i<count($vacunas_reporte);$i++){
                    $sql = getSQLResumenInmunizacion($request[cuie],$vacunas_reporte[$i]['id'],$mes_selected,$anio_selected,$request[liquidacion]);
                    $result = sql($sql) or die;
                    $fil_primera = $vacuna->getDosisVacuna($vacunas_reporte[$i]['id']);
                    list($col_primera,$rangos,$condiciones,$un_edad,$restr) = getCriteriosClasificacion($vacunas_reporte[$i]['nombre']);
                    if($result){
                        $prestaciones = array();
                        $edades_reservadas = array();
                        while(!$result->EOF){
                            for($j=0;$j<count($col_primera);$j++){
                                if(isset($rangos[$j])&&$rangos!=null){
                                    $edades_reservadas = array_merge($edades_reservadas,$rangos[$j]);
                                }
                                if($un_edad=="anio"){
                                    $edad = $result->fields['anio'];
                                }
                                if($un_edad=="mes"){
                                    $edad = $result->fields['anio'] * 12 + $result->fields['mes'];
                                }
                                if($un_edad=="dia"){
                                    $edad = $result->fields['anio'] * 12 + $result->fields['mes'] * 30.41 + $result->fields['dia'];
                                }
                                if($condiciones[$j]=="edad_dia"){
                                    $edad_dia = $result->fields['anio'] * 12 + $result->fields['mes'] * 30.41 + $result->fields['dia'];
                                    if(in_array($edad_dia,$rangos[$j])){
                                        if(!in_array($result->fields['id_prestacion_inmu'],$prestaciones)){
                                            $matriz[$j][$result->fields['id_vacuna_dosis']] += $result->fields['cnt'];
                                        }
                                    }
                                }
                                if($condiciones[$j]=="edad"){
                                    if(in_array($edad,$rangos[$j])){
                                        if(!in_array($result->fields['id_prestacion_inmu'],$prestaciones)){
                                            $matriz[$j][$result->fields['id_vacuna_dosis']] += $result->fields['cnt'];
                                            if($restr)
                                                break;
                                        }
                                    }
                                }//else{
                                    if($result->fields['caracteristica']==$condiciones[$j]){
                                        $matriz[$j][$result->fields['id_vacuna_dosis']] += $result->fields['cnt'];
                                        if($restr)
                                            break;
                                    }//else{
                                        if($condiciones[$j]=="cohorte"){
                                            if(!in_array($result->fields['id_prestacion_inmu'],$prestaciones)){
                                                if(in_array($result->fields['anio_nac'],$rangos[$j])){//if(in_array($edad,$rangos[$j])){
                                                    $matriz[$j][$result->fields['id_vacuna_dosis']] += $result->fields['cnt'];
                                                    if(!in_array($result->fields['anio'],$edades_reservadas))
                                                        array_push($edades_reservadas,$result->fields['anio']);
                                                    if($restr)
                                                        break;
                                                }
                                            }
                                        }
                                        if($condiciones[$j]=="otras_edades"){
                                            if(!in_array($result->fields['id_prestacion_inmu'],$prestaciones)){
                                                if(!in_array($edad, $edades_reservadas)){
                                                    $matriz[$j][$result->fields['id_vacuna_dosis']] += $result->fields['cnt'];
                                                    if($restr)
                                                        break;                                                   
                                                }
                                            }
                                        }
                                        if($condiciones[$j]=="grupos_riesgo" && $result->fields['id_grupo_riesgo']!=""){
                                            if(!in_array($result->fields['id_prestacion_inmu'],$prestaciones)){
                                                $matriz[$j][$result->fields['id_vacuna_dosis']] += $result->fields['cnt'];
                                                if($restr)
                                                    break;
                                            }
                                        }
                                        
                                    //}
                                //}                                
                            }
                            if(!in_array($result->fields['id_prestacion_inmu'],$prestaciones)){
                                array_push($prestaciones, $result->fields['id_prestacion_inmu']);
                            }
                            $result->MoveNext();
                        }
                        
                        //llamar al metodo de la clase pdf q pinta el frm 
                        $pdf->graficarTablaInmunizacion($i,$vacunas_reporte,$fil_primera,$col_primera,$matriz);
                        
                        unset($fil_primera,$col_primera,$rangos,$edades_reservadas);
                    }
                }
        $pdf->pieReporteInmunizacion();
        $pdf->Output();
    }    
    
    
?>
