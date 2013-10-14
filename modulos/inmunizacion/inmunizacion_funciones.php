<?php   
    require_once ("../../lib/funciones_misiones.php");

    function getSQLPeriodosPorCerrar($cuie){
        $cuie = strtoupper($cuie);
        $sql = "SELECT DISTINCT(
                            date_part('year',inmu.fecha_inmunizacion) 
                            ||'/'||
                            CASE WHEN date_part('month',inmu.fecha_inmunizacion)<10 THEN '0' || date_part('month',inmu.fecha_inmunizacion)::text  
                                ELSE date_part('month',inmu.fecha_inmunizacion)::text
                            END ) AS periodo 
                FROM inmunizacion.prestaciones_inmu inmu 
                WHERE inmu.cuie='$cuie' 
                AND inmu.id_cierre IS NULL 
                ORDER BY periodo DESC";
        return $sql;
    }
    
    function getSQLInmunizacionesPorCerrar($cuie,$fecha_desde="",$fecha_hasta=""){
        $sql = "SELECT inmu.id_prestacion_inmu, inmu.fecha_inmunizacion, inmu.clave_beneficiario, 
                       vacuna.descripcion AS vacuna, dosis.descripcion AS dosis
                FROM inmunizacion.prestaciones_inmu inmu 
                JOIN inmunizacion.vacunas vacuna ON inmu.id_vacuna=vacuna.id_vacuna 
                JOIN inmunizacion.vacunas_dosis dosis ON inmu.id_vacuna_dosis=dosis.id_vacuna_dosis 
                WHERE inmu.cuie='".$cuie."'
                  AND inmu.id_cierre IS NULL 
                  AND inmu.fecha_inmunizacion BETWEEN '$fecha_desde' AND '$fecha_hasta'
                ORDER BY inmu.fecha_inmunizacion DESC, vacuna ASC, dosis ASC";
        return $sql;
    }
    
    function getSQLPeriodosCierre($cuie,$arr_periodos=""){
        $cuie = strtoupper($cuie);
        if(isset($arr_periodos)){
            $csv_periodos = "'".implode("','", $arr_periodos)."'";
            $where .= " AND c.periodo IN(".$csv_periodos.") ";
        }
        $sql = "SELECT c.id_cierre, c.periodo, l.id_liquidacion, 
                       MAX(l.fecha) AS fecha_liquidacion 
                FROM inmunizacion.cierre c 
                LEFT JOIN inmunizacion.liquidacion l ON c.id_cierre=l.id_cierre
                WHERE c.cuie='$cuie' 
                ".$where." 
                GROUP BY c.id_cierre, c.periodo, l.id_liquidacion";
                //modificar y traer la ultima liquidacion corresp al periodo
        return $sql;
    }
    
    function getSQLLiquidacionesPeriodo($cuie,$mes,$anio,$ids_vacunas){
        $cuie = strtoupper($cuie);
        $periodo = $anio."/".$mes;
        $sql = "SELECT DISTINCT(l.id_liquidacion), l.fecha
                FROM inmunizacion.prestaciones_inmu i 
                JOIN inmunizacion.liquidacion l ON i.id_liquidacion=l.id_liquidacion 
                JOIN inmunizacion.cierre c ON l.id_cierre=c.id_cierre 
                WHERE c.cuie='$cuie'
                  AND c.periodo='$periodo'
                  AND i.id_vacuna IN(".implode(',', $ids_vacunas).")";
        return $sql;
    }
    
    function getSQLFechaLiquidacion($id_liquidacion){
        if($id_liquidacion=='')
            $id_liquidacion = 'null';
        $sql = "SELECT fecha 
                FROM inmunizacion.liquidacion 
                WHERE id_liquidacion=$id_liquidacion";
        return $sql;
    }
    
    function getSQLResumenInmunizacion($cuie,$vacuna,$mes,$anio,$liquidacion=""){
        $cuie = strtoupper($cuie);
        $fechaDesde = $anio."-".$mes."-01";
        $fechaHasta = $anio."-".$mes."-".ultimoDia($mes,$anio);
        $cond_fecha .= " AND inmu.fecha_inmunizacion BETWEEN '$fechaDesde' AND '$fechaHasta' ";
        if($liquidacion!=""){
            $where .= " AND inmu.id_liquidacion='$liquidacion' ";
        }
        $sql = "SELECT dosis.descripcion AS dosis, c.descripcion AS caracteristica, 
                       1 AS cnt, inmu.id_prestacion_inmu, inmu.id_vacuna_dosis,
                       CASE WHEN inmu.id_grupo_riesgo>0 
                            THEN inmu.id_grupo_riesgo 
                            ELSE NULL 
                       END AS id_grupo_riesgo,
                       date_part('year',inmu.fecha_nacimiento) AS anio_nac, 
                       age(inmu.fecha_inmunizacion,inmu.fecha_nacimiento) as edad,
                       date_part('year',age(inmu.fecha_inmunizacion,inmu.fecha_nacimiento)) as anio, 
                       date_part('month',age(inmu.fecha_inmunizacion,inmu.fecha_nacimiento)) as mes, 
                       date_part('day',age(inmu.fecha_inmunizacion,inmu.fecha_nacimiento)) as dia
                FROM inmunizacion.prestaciones_inmu inmu 
                JOIN inmunizacion.vacunas_dosis dosis ON inmu.id_vacuna_dosis=dosis.id_vacuna_dosis 
                LEFT JOIN inmunizacion.prestaciones_caracteristicas pc ON inmu.id_prestacion_inmu=pc.id_prestacion_inmu 
                LEFT JOIN inmunizacion.caracteristicas c ON pc.id_caracteristica=c.id_caracteristica
                WHERE inmu.cuie='$cuie'
                   AND inmu.id_vacuna='$vacuna' 
                   AND inmu.id_cierre IS NOT NULL 
                  ".$cond_fecha."
                  ".$where."

                ORDER BY dosis.descripcion DESC";
        //COUNT(*) AS cnt
        //GROUP BY dosis, inmu.id_vacuna_dosis, edad, caracteristica, inmu.id_grupo_riesgo, inmu.id_prestacion_inmu 
        return $sql;
    }
    
    function getCriteriosClasificacion($nombreVacuna){
        $un_edad = "anio";
        $restr = false;
        switch($nombreVacuna){
            case "ANTIAMARILLICA":
                $criterios[] = "1 año";    $rangos[] = array(1);       $condicion[] = "edad";
                $criterios[] = "2 años";   $rangos[] = array(2);       $condicion[] = "edad";
                $criterios[] = "3 años";   $rangos[] = array(3);       $condicion[] = "edad";
                $criterios[] = "4 años";   $rangos[] = array(4);       $condicion[] = "edad";
                $criterios[] = "5 años";   $rangos[] = array(5);       $condicion[] = "edad";
                $criterios[] = "6 años";   $rangos[] = array(6);       $condicion[] = "edad";
                $criterios[] = "7 a 12";   $rangos[] = range(7,12);    $condicion[] = "edad";
                $criterios[] = "13 a 19";  $rangos[] = range(13,19);   $condicion[] = "edad";
                $criterios[] = "20 a 29";  $rangos[] = range(20,29);   $condicion[] = "edad";
                $criterios[] = "30 a 49";  $rangos[] = range(30,39);   $condicion[] = "edad";
                $criterios[] = "50 a 69";  $rangos[] = range(50,69);   $condicion[] = "edad";
                $criterios[] = "70 y mas"; $rangos[] = range(50,150);  $condicion[] = "edad";
                break;
            case "ANTIGRIPAL JUNIOR":
                $criterios[] = "6 meses a 2 años";   $rangos[] = range(6,23); $condicion[] = "edad";
                $un_edad = "mes";
                break;
            case "ANTIGRIPAL ADULTO":
                $criterios[] = "Embarazadas";                                    $condicion[] = "Embarazada";
                $criterios[] = "Puerperas";                                      $condicion[] = "Puerpera";
                $criterios[] = "Pers. Salud";                                    $condicion[] = "Personal de Salud";
                $criterios[] = "> 65 años";         $rangos[3] = range(65,150);  $condicion[] = "edad";
                $criterios[] = "Grupos de Riesgo";                               $condicion[] = "grupos_riesgo";
                break;
            case "BCG":
                $criterios[] = "Recien Nacido";  $rangos[] = array(0);       $condicion[] = "edad";
                $criterios[] = "24hs. < 1 año";  $rangos[] = range(1,365);   $condicion[] = "edad";
                $un_edad = "dia";
                break;
            case "CUADRUPLE (DPT - HIB)":
                $criterios[] = "18 meses";      $rangos[] = array(18);  $condicion[] = "edad";
                $criterios[] = "Esq. Atrasado";                         $condicion[] = "Esquema Atrasado";
                $un_edad = "mes";
                break;
            case "DOBLE BACTERIANA (DTA)":
                $criterios[] = "7 a 15 años";    $rangos[] = range(7,15);    $condicion[] = "edad";
                $criterios[] = "16 y  mas";      $rangos[] = range(16,150);  $condicion[] = "edad";
                $criterios[] = "Embarazadas";                                $condicion[] = "Embarazada";
                break;
            case "DOBLE VIRAL SR":
                $criterios[] = "Puerperio";     $condicion[] = "Puerpera";
                $criterios[] = "Postaborto";    $condicion[] = "Postaborto";
                $criterios[] = "Otras edades";  $condicion[] = "otras_edades";
                break;
            case "DPT ACELULAR":
                $criterios[] = "11 años"; $rangos[] = array(11); $condicion[] = "edad";
                $criterios[] = "Esq. Atrasado";                         $condicion[] = "Esquema Atrasado";
                $criterios[] = "Pers. Salud";                           $condicion[] = "Personal de Salud";
                $criterios[] = "Embarazadas";                           $condicion[] = "Embarazada";
                $criterios[] = "Puerperas";                             $condicion[] = "Puerpera";
                $criterios[] = "Otras edades";                          $condicion[] = "otras_edades";
                break;
            case "HEPATITIS A":
                $criterios[] = "1 año";       $rangos[] = array(1);    $condicion[] = "edad";
                $criterios[] = "Bloqueo";                              $condicion[] = "Bloqueo";
                $criterios[] = "2 a 8 años";  $rangos[2] = array(2,8); $condicion[] = "edad";
                $criterios[] = "Otras edades";                         $condicion[] = "otras_edades";
                $restr = true;
                break;
            case "HEPATITIS B":
                $criterios[] = "< 12 Hs.";     $rangos[] = array(0);      $condicion[] = "edad_dia";
                $criterios[] = "5 a 10 años";  $rangos[] = range(5,10);   $condicion[] = "edad";
                $criterios[] = "11 años";      $rangos[] = array(11);     $condicion[] = "edad";
                $criterios[] = "12 a 19 años"; $rangos[] = range(12,19);  $condicion[] = "edad";
                $criterios[] = "20 a 40 años"; $rangos[] = range(20,40);  $condicion[] = "edad";
                $criterios[] = "> 40 años";    $rangos[] = range(41,150); $condicion[] = "edad";
                $criterios[] = "Pers. Salud";                             $condicion[] = "Personal de Salud";
                break;
            case "HPV":
                
                $y = date('Y');
                for($i=2000;$i<2005;$i++){
                    $criterios[] = "Cohorte ".$i;  $rangos[] = array($i);  $condicion[] = "cohorte";
                }
                /*
                $criterios[] = "Cohorte 2000";  $rangos[] = array(date('Y')-2000);  $condicion[] = "cohorte";
                $criterios[] = "Cohorte 2001";  $rangos[] = array(date('Y')-2001);  $condicion[] = "cohorte";
                $criterios[] = "Cohorte 2002";  $rangos[] = array(date('Y')-2002);  $condicion[] = "cohorte";
                */
                $criterios[] = "Otras edades";                                      $condicion[] = "otras_edades";
                break;
            case "NEUMOCOCO":
                $criterios[] = "< 12 meses";    $rangos[] = range(0,11);    $condicion[] = "edad";
                $criterios[] = "12 a 24 meses"; $rangos[] = array(12,23);   $condicion[] = "edad";
                $un_edad = "mes";
                break;
            case "NEUMOCOCCICA 23":
                $criterios[] = "2 a 64 años";    $rangos[] = range(2,64);    $condicion[] = "edad";
                $criterios[] = "> 65 años";      $rangos[] = range(65,150);  $condicion[] = "edad";
                break;
            case "TRIPLE VIRAL (SRP)":
                $criterios[] = "1 año";      $rangos[] = array(1);   $condicion[] = "edad";
                $criterios[] = "2 a 4 años"; $rangos[] = range(2,4); $condicion[] = "edad";
                $criterios[] = "5 años";     $rangos[] = array(5);   $condicion[] = "edad";
                $criterios[] = "6 años";     $rangos[] = array(6);   $condicion[] = "edad";
                $criterios[] = "11 años";    $rangos[] = array(11);  $condicion[] = "edad";
                $criterios[] = "Pers. Salud";                        $condicion[] = "Personal de Salud";
                $criterios[] = "Bloqueo";                            $condicion[] = "Bloqueo";
                break;
            case "PENTAVALENTE":
                $criterios[] = "< 1 año";       $rangos[] = range(0,11); $condicion[] = "edad";
                $criterios[] = "18 meses";      $rangos[] = array(18);   $condicion[] = "edad";
                $criterios[] = "Otras edades";                           $condicion[] = "otras_edades";
                $un_edad = "mes";
                break;
            case "SABIN":
                $criterios[] = "< 1 año";    $rangos[] = array(0);   $condicion[] = "edad";
                $criterios[] = "1 año";      $rangos[] = array(1);   $condicion[] = "edad";
                $criterios[] = "2 a 4 años"; $rangos[] = range(2,4); $condicion[] = "edad";
                $criterios[] = "5 años";     $rangos[] = array(5);   $condicion[] = "edad";
                $criterios[] = "6 años";     $rangos[] = array(6);   $condicion[] = "edad";
                /*
                $rangos[] = array("min"=>0,"max"=>1);
                $rangos[] = array("min"=>1,"max"=>1);
                $rangos[] = array("min"=>2,"max"=>4);
                $rangos[] = array("min"=>5,"max"=>5);
                $rangos[] = array("min"=>6,"max"=>6);
                */
                break;
            case "TRIPLE BACTERIANA (DPT)":
                $criterios[] = "5 años"; $rangos[] = array(5);   $condicion[] = "edad";
                $criterios[] = "6 años"; $rangos[] = array(6);   $condicion[] = "edad";   
                break;
        }
        return array($criterios,$rangos,$condicion,$un_edad,$restr);
    }
    
    function getVacunasReporte(){
        
        $vacunas[] = array("id" => 14, "nombre" => "ANTIAMARILLICA");
        $vacunas[] = array("id" => 30, "nombre" => "ANTIGRIPAL JUNIOR");
        $vacunas[] = array("id" => 31, "nombre" => "ANTIGRIPAL ADULTO");
        $vacunas[] = array("id" =>  1, "nombre" => "BCG");
        $vacunas[] = array("id" =>  3, "nombre" => "CUADRUPLE (DPT - HIB)");
        $vacunas[] = array("id" =>  9, "nombre" => "DOBLE BACTERIANA (DTA)");
        $vacunas[] = array("id" =>  7, "nombre" => "DOBLE VIRAL SR");
        $vacunas[] = array("id" => 18, "nombre" => "DPT ACELULAR");
        $vacunas[] = array("id" =>  5, "nombre" => "HEPATITIS A");
        $vacunas[] = array("id" =>  2, "nombre" => "HEPATITIS B");
        $vacunas[] = array("id" => 33, "nombre" => "HPV");
        $vacunas[] = array("id" => 11, "nombre" => "NEUMOCOCO");
        $vacunas[] = array("id" => 23, "nombre" => "NEUMOCOCCICA 23");
        $vacunas[] = array("id" => 17, "nombre" => "PENTAVALENTE");
        $vacunas[] = array("id" =>  4, "nombre" => "SABIN");
        $vacunas[] = array("id" =>  6, "nombre" => "TRIPLE BACTERIANA (DPT)");
        $vacunas[] = array("id" =>  8, "nombre" => "TRIPLE VIRAL (SRP)");
       
        return $vacunas;
    }
    
    function crearPeriodoCierre($cuie,$periodo){
        global $_ses_user;
        $sql = "INSERT INTO inmunizacion.cierre(cuie,periodo,fecha_carga,usuario_carga) 
                VALUES('$cuie','$periodo',now(),$_ses_user[id]) 
                RETURNING id_cierre";
        $res = sql($sql);
        return $res->fields['id_cierre'];
    }
    
    function crearLiquidacionPeriodoCierre($id_cierre){
        global $_ses_user;
        $sql = "INSERT INTO inmunizacion.liquidacion(id_cierre,fecha,usuario_carga) 
                VALUES('$id_cierre',now(),$_ses_user[id]) 
                RETURNING id_liquidacion";
        $res = sql($sql);
        return $res->fields['id_liquidacion'];
    }
    
?>
