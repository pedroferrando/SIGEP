<?php
/*
 archivo para el manejo de peticiones ajax del modulo
 en $_REQUEST[accion] se especifica la instruccion a realizar
*/
    require_once("../../config.php");
    require_once("../../clases/beneficiarios.php");
    require_once("../../clases/DebitoRetroactivo.php");
    require_once("../../clases/Efector.php");
    require_once("../../clases/Expediente.php");
    require_once("../../clases/Factura.php");
    require_once("../../clases/Prestacion.php");
    require_once("../../lib/funciones_misiones.php");
    require_once("./funciones.php");
    
    switch($_REQUEST['accion']){
        case 'get_link_impresion_prestaciones_doms':
            $parametros = array("nro_doc"=>$_REQUEST[nro_doc],
                                "fecha_desde"=>$_REQUEST[fecha_desde],
                                "fecha_hasta"=>$_REQUEST[fecha_hasta],
                                "json"=>$_REQUEST[json]);
            echo encode_link("listado_prestaciones_doms_pdf.php",$parametros);
            break;
        
        case 'mostrar_detalle_prestaciones':
            $beneficiario = new Beneficiario();
            $beneficiario->Automata("numero_doc = '".$_REQUEST[nro_doc]."'");
            $efector = new Efector();
            $arr_trz_comunes = array('NINO','NINO_PESO','ADOLESCENTE','ADULTO','TAL');
            $var = json_decode(stripslashes($_POST[json]));
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
            echo "<h3><b>Informe de DOMs</b></h3>";
            $c = 0;
            foreach($arr_sql as $key => $value){
                echo "<p></p>";//"<b>".$key."</b> ";
                $form_name = getFormInfoTrazadora($key);
                if($form_name!=""){
                    $form_path .= 'info_trazadoras/'.$form_name;
                    include($form_path);
                    unset($form_path);
                }
                if($key!=$key_prev){
                    $c = 0;
                }
                $key_prev = $key;
            }
                
            break;
                
        case 'paginar_prestaciones':
            if($_REQUEST[prestaciones]!=null){
                $arr_ids = $_REQUEST[prestaciones];
            }else{
                $arr_ids = array();
            }
            $page = $_REQUEST[page];
            $total = $_REQUEST[total_regs];
            $regs = 15;
            $offset = ($page-1)*$regs;
            $page--;
            $idFila = $offset; 
            $sql = getSQLPrestacionesBeneficiario($_REQUEST[nro_doc],$_REQUEST[fecha_desde],$_REQUEST[fecha_hasta],$regs,$offset);
            $result = sql($sql) or die;
            
            include('listado_prestaciones_doms_body.php');
        break;
        
        case 'dbtret_get_facturas':
            $expte = new Expediente();
            $expte->setNroExp($_REQUEST[dbt_expte]);
            $facturasExpte = $expte->getFacturas();
            $sel .= "Nro Factura:";
            if(count($facturasExpte)>0){
                $sel .= "<select name='dbt_factura'>";
                $sel .= "<option value=''></option>";
                foreach($facturasExpte as $f){
                    $sel .= "<option value='".$f->getNroFactOffline()."'>".$f->getNroFactOffline()."</option>";
                }
                $sel .= "</select>";
            }
            echo $sel;
            break;
        
        case 'dbtret_get_prestaciones':
            $motAud = DebitoRetroactivoColeccion::getMotivosAuditoria();
            $prestaciones = PrestacionColeccion::getPrestacionesParaDebitoRetroactivo($_REQUEST[dbt_cuie], $_REQUEST[dbt_expte], $_REQUEST[dbt_factura], $_REQUEST[dbt_clave_doc]);
            if($prestaciones->NumRows()>0){
                echo '<div id="cntformAlta" style="display:none;">';
                include('formDbtRetAlta.php');
                echo '</div>';
            }
            include('formDbtRetPrestaciones.php');
            break;
        
        case 'dbtret_get_prest_debitadas':
            $cuie = $_REQUEST[cuie];
            $nro_exp = trim($_REQUEST[nro_exp]);
            $prestDeb = DebitoRetroactivoColeccion::getPrestacionesDebitoRetroactivo($cuie,$nro_exp);
            include('formDbtRetPrestacionesDebitadas.php');
            break;
        
        case 'dbtret_delete_debito':
            $debitoRet = new DebitoRetroactivo($_REQUEST[id_debito],null,null,null,null,null,null,null);
            echo $debitoRet->delete();
            break;
        
        case 'dbtret_save_debito':
            extract($_REQUEST,EXTR_OVERWRITE);
            $debitoRet = new DebitoRetroactivo("",trim($nro_exp),$cuie,$dbt_motivo,$id_prestacion,$dbt_observaciones,$dbt_identificacion,$dbt_tipo_auditoria);
            echo $debitoRet->save();
            break;
        
        case 'dbtret_show_busqueda':
            $cuie = $_REQUEST[cuie];
            $exptesEfector = ExpedienteCollecion::getExpedientesEfector($cuie, 'C', 'exp.nro_exp');
            include('formDbtRetBusqueda.php');
            break;
        
        default:
            break;
    }    
    
    
    
    
    function comp($a, $b){
        if ($a['fecha_prestacion'] == $b['fecha_prestacion']) {
            return 0;
        }
        return ($a['fecha_prestacion'] > $b['fecha_prestacion']) ? -1 : 1;
    }        
    
?>
