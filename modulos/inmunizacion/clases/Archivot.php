<?php
require_once ("Prestacion.php");
require_once ("vacuna.php");

class Archivot {
    public static function getCierre($cuie,$periodo){
        $query = "select id_cierre from inmunizacion.cierre where cuie='$cuie' and periodo='$periodo'";
        $retorno=sql($query, "Error al traer cierre");
        
        $retorno=$retorno->fields["id_cierre"];
        return $retorno;  
    }
    public static function borrarLiquidacion($id_archivo,$id_cierre,$id_liquidacion){
        $query = "select count(*)cantidad from inmunizacion.liquidacion where id_cierre='$id_cierre'";
        $retorno=sql($query);
        
        if($retorno->fields["cantidad"]==1){
            $query = "delete from inmunizacion.liquidacion where id_liquidacion='$id_liquidacion'";
            $retorno=sql($query, "Error al eliminar liquidacion");
        
            $query = "delete from inmunizacion.cierre where id_cierre='$id_cierre'";
            $retorno=sql($query, "Error al eliminar cierre");
        }else{
            $query = "delete from inmunizacion.liquidacion where id_liquidacion='$id_liquidacion'";
            $retorno=sql($query, "Error al eliminar liquidacion");
        }
        
        
        return $retorno;  
    }
    public static function getArchivos(){
        $query = "select * from inmunizacion.archivos";
        $retorno=sql($query, "Error al traer Archivo") or fin_pagina();
        return $retorno;
    }
    public static function borrarArchivo($id_archivo){
        $query = "delete from inmunizacion.archivos where id_archivo=$id_archivo";
        $retorno=sql($query, "Error al borrar Archivo") or fin_pagina();
        if($retorno){
            $retorno='S';
        }else{
            $retorno='N';
        }
        return $retorno;  
    }
    public static function getArchivo($nombre_archivo){
        $query = "select * from inmunizacion.archivos where nombre_archivo='$nombre_archivo'";
        $retorno=sql($query, "Error al traer Archivo") or fin_pagina();
        return $retorno;
    }
    public static function setArchivo($nombre_archivo,$descripcion_archivo,$id_usuario,$id_mensaje,$cuie,$id_archivo_cristian,$periodo){
        $max_id=  Archivot::getMaxId();        
        $query = "insert into inmunizacion.archivos(id_archivo,nombre_archivo,descripcion_archivo,id_usuario,fecha_carga,id_mensaje,cuie,id_archivo_cristian,periodo) 
                    values($max_id,'$nombre_archivo','$descripcion_archivo',$id_usuario,date_trunc('seconds',localtimestamp),$id_mensaje,'$cuie',$id_archivo_cristian,'$periodo')";
        $retorno=sql($query, "Error al insertar archivo en archivos") or fin_pagina();
        return $max_id; 
    }
    public static function getMaxId(){
        $query_max_id="select max(id_archivo)max_id from inmunizacion.archivos";
        $max_id=sql($query_max_id, "Error al traer el max_id") or fin_pagina();
        $max_id=$max_id->fields["max_id"];
        if (!$max_id ==null){
            $max_id++;
            return $max_id;
        }else{
            return 1;
        }
    }
    public static function getContenido($file){        
        while ($datos = fgets($file)) {
           $lineas[] = explode(';', $datos);
        }
        $archivo['lineas']=$lineas;
        $archivo['primer_linea']= $lineas[0];
        $archivo['columnas']=count($lineas[0]);
        
        if($archivo['columnas']==22){
            $archivo['version']="V8.7";
            $archivo['id_mensaje']=1;
        }else{
            if($archivo['columnas']==28){
                $archivo['version']="V8.9";
                $archivo['id_mensaje']=1;
            }else{
                $archivo['version']="Otra";
                $archivo['id_mensaje']=1;
            }
        }
        return $archivo;
        
    }
    public static function setPrestacionInmu($file,$nombre_archivo_completo,$periodo){
        global $_ses_user;
        $contenido=Archivot::getContenido($file);
        
        
        
        $descripcion_archivo="";
        $id_usuario =$_ses_user['id'];
        $id_mensaje=$contenido['id_mensaje'];
        $nombre_archivo = $nombre_archivo_completo[0];
        $cuie_archivo=substr($nombre_archivo, 1, 6);
        $id_archivo_cristian=  substr($nombre_archivo, 7, 3);
        
        //Registra el txt en la tabla archivos
        //
        $id_archivo=  Archivot::setArchivo($nombre_archivo, $descripcion_archivo, $id_usuario, $id_mensaje, $cuie_archivo, $id_archivo_cristian,$periodo);
        
//        $id_prestacion_inmu=0;
//        $id_vacuna_dosis=0;
        $error="N";
        $mensajes="";
        $conteo['guardadas']=0;
        $conteo['noguardadas']=0;
        $numero_fila=0;
        
        if($contenido['version']=="V8.9"){
            try {
                sql("BEGIN");
            foreach ($contenido['lineas'] as $l) {
                $numero_fila=++$numero_fila;
                $cant=count($l);
                if($cant!=28){
                    $error[0]="S";
                    excepcion("Cantidad de columnas incorrecta en la linea actual: ".$numero_fila);
                }
                
                $id_prestacion_cristian=  Prestacion::getIdPrestacionCristian($l[1]);
                if($id_prestacion_cristian!=""){
                    $error="S";
                    $mensajes[1]="id_prestacion_cristian ya existe en la tabla prestaciones_inmu";
                }else{
                    $id_prestacion_cristian=$l[1];
                }
                
                $linea=implode(";",$l);
                
                $id_prestacion_inmu=Prestacion::getNextMaxId();
                $id_prestacion_inmu_debito=  Prestacion::getNextMaxIdDebito();
                
                $id_vacuna_dosis=$l[10];
                if($id_vacuna_dosis==""){
                    $error="S";
                    $mensajes[2]="id_vacuna_dosis no existe";
                }
                $id_vacuna=Vacuna::getIdVacuna($id_vacuna_dosis);
                if($id_vacuna==""){
                    $error="S";
                    $id_vacuna=0;
                    $mensajes[3]="id_vacuna no existe";
                }
                
                $cuie=  Efector::getCuie($l[0]);
                if($cuie==""){
                    $error="S";
                    $cuie=$l[0];
                    $mensajes[4]="cuie no existe";
                }
                
                
                $clavebeneficiario=  Efector::getClaveBeneficiarioUadBeneficiarios($l[2]);
                if($clavebeneficiario==""){
                    $error="S";
                    if($l[2]==''){
                        $clavebeneficiario=0;
                    }else{
                        $clavebeneficiario=$l[2];
                    }
                    $mensajes[5]="clave de beneficiario no existe";
                }
                
                if($l[16]=="V"){
                    $id_terreno=1;
                }else{
                    $id_terreno=2;
                }                
                
                //Se comprueba que el periodo de la inmunizacion coincida con el periodo informado en el nombre del txt.

                

                $fecha_comprobante=  $l[9];
                $periodo_prestacion=Prestacion::obtenerPeriodoPrestacion($l[9]);
                
                if($periodo_prestacion!=$periodo){
                    $error="S";
                    $mensajes[6]="periodo de prestacion distinto a periodo de archivo";
                }
                //se comprueba que el cuie coincida con el cuie informado en el nombre de archivo
                if($cuie!=$cuie_archivo){
                    $error="S";
                    $mensajes[7]="cuie no coincide con cuie de archivo";
                }
                
                //$fecha_nacimiento=  Prestacion::invertirFechaAAAAMMDD($l[8]);
                $fecha_nacimiento=  $l[8];
                $fecha_vencimiento=$l[24];
                
                if($fecha_vencimiento=='--'){
                    $fecha_vencimiento="";
                }
                
                
                $lote=$l[23];
                $laboratorio=$l[22];
                
                $id_presentacion=$l[26];
                if($id_presentacion==""){
                    $id_presentacion=1;
                }
                
                $id_grupo_riesgo=$l[27];
                if($id_grupo_riesgo==""){
                    $id_grupo_riesgo=-1;
                }
                
                $id_grupo_riesgo=-1;
                $origen=3;
                
                
                if(!$fecha_vencimiento){
                    $fecha_vencimiento='9999-01-01';
                }
                if(!$lote){
                    $lote='';
                }
                if(!$laboratorio){
                    $laboratorio='';
                }
                
                if($mensajes!=""){
                    $mensajes=implode(". - ",$mensajes);
                }
                
                //Graba la vacuna en prestaciones_inmu
                
                if($error=="N"){
                    Prestacion::setPrestacionInmuTxt($id_prestacion_inmu,$id_vacuna_dosis,$id_vacuna,$cuie,$clavebeneficiario,$id_terreno,$fecha_comprobante,$fecha_nacimiento,$fecha_vencimiento,$lote,$laboratorio,$id_presentacion,$id_grupo_riesgo,$origen,$id_usuario,$id_prestacion_cristian,$id_archivo);
                    $error="N";
                    $conteo['guardadas']=++$conteo['guardadas'];
                }else{
                     Prestacion::setPrestacionInmuTxtDebito($id_prestacion_inmu_debito,$id_vacuna_dosis,$id_vacuna,$cuie,$clavebeneficiario,$id_terreno,$fecha_comprobante,$fecha_nacimiento,$fecha_vencimiento,$lote,$laboratorio,$id_presentacion,$id_grupo_riesgo,$origen,$id_usuario,$id_prestacion_cristian,$linea,$id_archivo,$mensajes,$numero_fila);
                     $error="N";
                     $mensajes="";
                     $conteo['noguardadas']=++$conteo['noguardadas'];
                }
                   
            }
            $cantidades=Archivot::setCantidades($id_archivo,$nombre_archivo,$conteo['guardadas'],$conteo['noguardadas']);
            
            echo "Guardadas: ".$conteo['guardadas'];
            echo "<br>";
            echo "No Guardadas: ".$conteo['noguardadas'];
            sql("COMMIT");
            
            if($conteo['guardadas']>0){
                //Buscar si existe cierre para ese periodo y cuie
                $id_cierre=Archivot::getCierre($cuie, $periodo);
                //Si no existe cierre crearlo
                if($id_cierre){
                    
                    $id_liquidacion = crearLiquidacionPeriodoCierre($id_cierre);
                    Vacuna::liquidarInmunizaciones($id_archivo, $id_cierre, $id_liquidacion);
                    Archivot::setDatosLiquidacion($id_archivo, $id_cierre, $id_liquidacion);
                }else{
                    
                    $id_cierre = crearPeriodoCierre($cuie,$periodo);
                    $id_liquidacion = crearLiquidacionPeriodoCierre($id_cierre);
                    Vacuna::liquidarInmunizaciones($id_archivo, $id_cierre, $id_liquidacion);
                    Archivot::setDatosLiquidacion($id_archivo, $id_cierre, $id_liquidacion);
                }
                
                //Crear liquidacion
                
                //Liquidar inmunizaciones guardadas
            }
            
            return "guardado";
            } catch (exception $e) {
                sql("ROLLBACK", "Error en rollback", 0);
                echo $e;
                ?>
                    <script type="text/javascript">
                        alert("Error: Cantidad de columnas incorrecta" );
                    </script>
                <?
            }
        }
        return "no guardado";
    }
    public static function setCantidades($id_Archivo,$nombre_archivo,$aceptadas,$rechazadas){
        $query = "update inmunizacion.archivos set aceptadas=$aceptadas,rechazadas=$rechazadas where id_archivo=$id_Archivo and nombre_archivo='$nombre_archivo'";
        $retorno=sql($query, "Error al actualizar Cantidades") or fin_pagina();
        if($retorno){
            $retorno='S';
        }else{
            $retorno='N';
        }
        return $retorno;  
    }
    public static function setDatosLiquidacion($id_archivo,$id_cierre,$id_liquidacion){
        $query = "update inmunizacion.archivos set id_cierre=$id_cierre,id_liquidacion=$id_liquidacion where id_archivo=$id_archivo";
        $retorno=sql($query, "Error al actualizar cierre y liquidacion en archivos");
        return $retorno;  
    }
    
    
}

?>
