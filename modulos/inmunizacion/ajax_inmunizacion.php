<?php
/*
 archivo para el manejo de peticiones ajax del modulo
 en $_REQUEST[accion] se especifica la instruccion a realizar
*/
    require_once("../../config.php");
    require_once("./clases/Vacuna.php");
    require_once("./inmunizacion_funciones.php");
    
    $vacuna = new Vacuna();
    
    switch($_REQUEST['accion']){
        case 'cerrar_inmunizaciones':
            $arr_periodos = array();
            $mat_periodos = array();
            $cuie = strtoupper($_REQUEST[cuie]);
            
            foreach($_REQUEST[prestacion_inmu] as $k => $val){
                $periodo = $_REQUEST[periodo_inmu][$val];
                if(!in_array($periodo, $arr_periodos)){
                    array_push($arr_periodos,$periodo);
                    $mat_periodos[$periodo] = "";
                }
            }
            
            $res = sql(getSQLPeriodosCierre($cuie,$arr_periodos));
            if($res){
                while(!$res->EOF){
                    $p = $res->fields['periodo'];
                    $mat_periodos[$p]['cierre'] = $res->fields['id_cierre'];
                    if($res->fields['id_liquidacion']!=""){
                        //agrego datos de la ultima liquidacion
                        $mat_periodos[$p]['fecha_liquidacion'] = $res->fields['fecha_liquidacion'];
                        $mat_periodos[$p]['liquidacion'] = $res->fields['id_liquidacion'];
                    }
                    $res->MoveNext();
                }
                foreach($_REQUEST[prestacion_inmu] as $k => $val){
                    $p = $_REQUEST[periodo_inmu][$val];
                    if($mat_periodos[$p]==""){
                        //insertar periodo en la tabla cierre
                        $id_cierre = crearPeriodoCierre($cuie,$p);
                        if($id_cierre){
                            //crear liquidacion y agregarla a la matriz
                            $id_liquidacion = crearLiquidacionPeriodoCierre($id_cierre);
                            $mat_periodos[$p]['cierre'] = $id_cierre;
                            $mat_periodos[$p]['fecha_liquidacion'] = date('Y-m-d');
                            $mat_periodos[$p]['liquidacion'] = $id_liquidacion;
                        }
                    }else{
                        //usar el id de cierre en $mat_periodos[$p]
                        $id_cierre = $mat_periodos[$p]['cierre'];
                        if( $mat_periodos[$p]['liquidacion']=="" || $mat_periodos[$p]['fecha_liquidacion']!=date('Y-m-d') ){
                            //crear liquidacion y agregarla a la matriz
                            $id_liquidacion = crearLiquidacionPeriodoCierre($id_cierre);
                            $mat_periodos[$p]['fecha_liquidacion'] = date('Y-m-d');
                            $mat_periodos[$p]['liquidacion'] = $id_liquidacion;
                        }
                        $id_liquidacion = $mat_periodos[$p]['liquidacion'];
                    }
                    $vacuna->liquidarInmunizacion($val, $id_cierre, $id_liquidacion);
                }
                echo true;
            }else{
                echo false;
            }
        break;
            
        default:
            break;
    }    
    
    
?>