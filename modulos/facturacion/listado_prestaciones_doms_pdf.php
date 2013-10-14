<?php
    require_once("../../config.php");
    require_once('../../lib/fpdf.php');
    require_once("../../lib/funciones_misiones.php");
    require_once("../../clases/beneficiarios.php");
    require_once("../../clases/creador_pdf.php");
    require_once("../../clases/Efector.php");
    require_once("./creador_pdf.php");
    require_once("./funciones.php");
    
    header("Cache-Control: no-cache, must-revalidate");
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    
    $request = decode_link($_REQUEST['p']);
    if(isset($request[nro_doc]) && $request[nro_doc]!=""){
        $beneficiario = new Beneficiario();
	$beneficiario->Automata("numero_doc = '".$request[nro_doc]."' AND clase_documento_benef='P' ");
        $datosBenef->nro_doc = $request[nro_doc];
        if($beneficiario->getClave_beneficiario()!=""){
            $datosBenef->nombre = $beneficiario->getNombreCompleto();
            $datosBenef->clave =  $beneficiario->getClave_beneficiario();
            $datosBenef->fechaNac = $beneficiario->getFecha_nacimiento_benef();
        }else{
            //buscar en smiafiliados
            $benef = datosAfiliadoEnVigente('',$request[nro_doc]);
            $datosBenef->nombre = $benef['afiapellido'].", ".$benef['afinombre'];
            $datosBenef->clave =  $benef['clavebeneficiario'];
            $datosBenef->fechaNac = date('d/m/Y',strtotime($benef['afifechanac']));
        }
        $efector = new Efector();
        $sql = getSQLPrestacionesBeneficiario($request[nro_doc],$request[fecha_desde],$request[fecha_hasta]);
        $result = sql($sql) or die;
        if($datosBenef->clave=="" && $result->RecordCount()>0){
            while(!$result->EOF){
                $trz_aux = $result->fields['trz'];
                $q = getSQLDatosBeneficiarioFromTrazadora($request[nro_doc],$trz_aux);
                $r = sql($q) or die;
                if($r->RecordCount()>0){
                    $r->MoveFirst();
                    $datosBenef->nombre = $r->fields['nombre_beneficiario'];
                    $datosBenef->clave =  $r->fields['clave'];
                    $datosBenef->fechaNac = "";
                    break;
                }
            }
            $result->MoveFirst();
        }
        
        if(isset($request[json])&&$request[json]!=""){
            $arr_trz_comunes = array('NINO','NINO_PESO','ADOLESCENTE','ADULTO','TAL');
            $var = json_decode(stripslashes($request[json]));
            $arr = (array)$var;
            //ordeno por el primer campo (trazadora)
            array_multisort($arr); 
            //convierto a array los array de obj que estan en el subnivel
            foreach($arr as $k => $a){
                $trz[$a->trazadora][] = (array)$a;
            }
            //ordenar por fecha los sub arrays
            foreach($trz as $key => $a){
                usort($a, 'comp'); //ordeno especificamente los arrays dentro de las entradas [key] en el array
                $trz[$key] = $a;
            } 
            //$trz es el array q se utiliza de ahora en mas

            $arr_sql = array();
            foreach($trz as $t){
                    foreach($t as $a){
                        $a = (object)$a; // convierto el array a obj para su tratamiento
                        if(in_array($a->trazadora, $arr_trz_comunes)){
                            $cod_nomenclador = trim($a->cod_nomenclador);
                            if($a->trazadora!="NINO" && $a->trazadora!="NINO_PESO"){
                                $campo_tabla = "codnomenclador";
                                //$where .= " AND replace(trz.codnomenclador,' ','')='". str_replace(' ','',$a->cod_nomenclador) ."' ";
                            }else{
                                $campo_tabla = "cod_nomenclador";
                            }
                            if($cod_nomenclador!=""){
                                $where .= " AND ( replace(trz.".$campo_tabla.",' ','')='". str_replace(' ','',$a->cod_nomenclador) ."' 
                                                 OR trz.".$campo_tabla." IS NULL 
                                                 OR trz.".$campo_tabla."=''
                                                )";
                            }else{
                                $where .= " AND (trz.".$campo_tabla." IS NULL OR trz.".$campo_tabla."='') ";
                            }
                        }//else{
                            //$where = " AND id_prestacion='$a->id_prestacion' "; //--> ese campo está siempre vacio en la bd
                        //}
                        if($a->trazadora=="INMU"){
                            $select .= " , vd.descripcion AS desc_dosis, vd.*, pr.descripcion AS desc_presentacion, 
                                        CASE trz.id_grupo_riesgo WHEN -1 THEN '' ELSE gr.descripcion END AS desc_grupo_riesgo ";
                            $join .= " JOIN inmunizacion.vacunas_dosis vd ON trz.id_vacuna_dosis=vd.id_vacuna_dosis 
                                       JOIN inmunizacion.presentaciones pr ON trz.id_presentacion=pr.id_presentacion 
                                       LEFT JOIN inmunizacion.grupos_riesgos gr ON trz.id_grupo_riesgo=gr.id_grupo_riesgo ";
                            $where .= " AND trz.id_prestacion='$a->id_prestacion' ";
                        }else{
                            $where .= " AND trz.num_doc='$a->nro_doc' ";
                        }
                        if($a->trazadora!="INMU" && $a->trazadora!="PARTO"){
                            $where .= " AND trz.fecha_control='$a->fecha_prestacion' ";
                        }
                        if($a->trazadora=="PARTO"){
                            $where .= " AND trz.fecha_parto='$a->fecha_prestacion' ";
                        }
                        $tabla = getNombreTablaTrazadora($a->trazadora);
                        $sql = "SELECT trz.*, ef.nombreefector AS efector ".$select."
                                FROM ".$tabla." trz 
                                JOIN facturacion.smiefectores ef ON trz.cuie=ef.cuie 
                                ".$join."
                                WHERE trz.cuie='$a->cuie' 
                                ".$where."
                                ORDER BY 1 DESC
                                LIMIT 1 
                            ";
                        $efector->FiltrarCuie(strtoupper($a->cuie));
                        $datos_alt['fecha_prestacion'] = $a->fecha_prestacion;
                        $datos_alt['efector'] = $efector->getNombreefector();
                        $datos_alt['cod_nomenclador'] = $a->cod_nomenclador;
                        
                        $arr_sql[$a->trazadora][] = $sql;
                        $arr_alt[$a->trazadora][] = $datos_alt;
                        unset($select);
                        unset($join);
                        unset($where);
                    }

            }
        }
        
        //$pdf = new CreadorPDFFacturacionOld();
        $pdf = new CreadorPDFFacturacion('P','mm','A4');
        $pdf->SetPrograma("sumar");
        $pdf->getReportePrestacionesDOMs($datosBenef,$result,$arr_sql,$arr_alt);
        
    }
    
    
    function comp($a, $b){
        if ($a['fecha_prestacion'] == $b['fecha_prestacion']) {
            return 0;
        }
        return ($a['fecha_prestacion'] > $b['fecha_prestacion']) ? -1 : 1;
    } 
    
    
?>
