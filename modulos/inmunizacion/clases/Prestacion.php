<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Prestacion
 *
 * @author lrobin
 */
class Prestacion {
    //put your code here
    public static function getIdFactura($id_comprobante){
        $query = "select id_comprobante,id_factura from facturacion.comprobante where id_comprobante='$id_comprobante'";
        $retorno=sql($query, "Error al obtener id_factura");
        $retorno=$retorno->fields["id_factura"];
        return ++$retorno;
    }
    public static function getNextMaxId(){
        $query = "select max(id_prestacion_inmu)max_id from inmunizacion.prestaciones_inmu";
        $retorno=sql($query, "Error al obtener max_id");
        $retorno=$retorno->fields["max_id"];
        return ++$retorno;
    }
    public static function getNextMaxIdDebito(){
        $query = "select max(id_prestacion_inmu_debito)max_id from inmunizacion.prestaciones_inmu_debitos";
        $retorno=sql($query, "Error al obtener max_id") or fin_pagina();
        $retorno=$retorno->fields["max_id"];
        return ++$retorno;
    }
    public static function setPrestacionInmu($id_prestacion_inmu,$id_vacuna_dosis,$id_vacuna,$cuie,$clavebeneficiario,$id_terreno,$fecha_comprobante,$fecha_nacimiento,$fecha_vencimiento,$lote,$laboratorio,$id_prestacion,$id_comprobante,$id_presentacion,$id_grupo_riesgo,$origen,$id_usuario){
        $query = "insert into inmunizacion.prestaciones_inmu(id_prestacion_inmu,id_vacuna_dosis,id_vacuna,cuie,clave_beneficiario,id_terreno,fecha_inmunizacion,
                    fecha_nacimiento,fecha_vencimiento,lote,laboratorio,fecha_carga,id_prestacion,id_comprobante,id_presentacion,id_grupo_riesgo,origen,id_usuario,eliminado) 
                    values($id_prestacion_inmu,$id_vacuna_dosis,$id_vacuna,'$cuie',$clavebeneficiario,$id_terreno,'$fecha_comprobante','$fecha_nacimiento','$fecha_vencimiento','$lote','$laboratorio',date_trunc('seconds',localtimestamp),$id_prestacion,$id_comprobante,$id_presentacion,$id_grupo_riesgo,$origen,$id_usuario,0)";
        $retorno=sql($query, "Error al insertar en prestaciones_inmu") or fin_pagina();
    }
    public static function setPrestacionInmuMigracion($id_prestacion_inmu,$id_vacuna_dosis,$id_vacuna,$cuie,$clavebeneficiario,$id_terreno,$fecha_comprobante,$fecha_nacimiento,$fecha_vencimiento,$lote,$laboratorio,$id_presentacion,$id_grupo_riesgo,$origen,$id_usuario){
        $query = "insert into inmunizacion.prestaciones_inmu(id_prestacion_inmu,id_vacuna_dosis,id_vacuna,cuie,clave_beneficiario,id_terreno,fecha_inmunizacion,
                    fecha_nacimiento,fecha_vencimiento,lote,laboratorio,fecha_carga,id_presentacion,id_grupo_riesgo,origen,id_usuario) 
                    values($id_prestacion_inmu,$id_vacuna_dosis,$id_vacuna,'$cuie',$clavebeneficiario,$id_terreno,'$fecha_comprobante','$fecha_nacimiento','$fecha_vencimiento','$lote','$laboratorio',date_trunc('seconds',localtimestamp),$id_presentacion,$id_grupo_riesgo,$origen,$id_usuario)";
        $retorno=sql($query, "Error al insertar en prestaciones_inmu");
    }
    public static function setPrestacionInmuTxt($id_prestacion_inmu,$id_vacuna_dosis,$id_vacuna,$cuie,$clavebeneficiario,$id_terreno,$fecha_comprobante,$fecha_nacimiento,$fecha_vencimiento,$lote,$laboratorio,$id_presentacion,$id_grupo_riesgo,$origen,$id_usuario,$id_prestacion_cristian,$id_archivo){
        $query = "insert into inmunizacion.prestaciones_inmu(id_prestacion_inmu,id_vacuna_dosis,id_vacuna,cuie,clave_beneficiario,id_terreno,fecha_inmunizacion,
                    fecha_nacimiento,fecha_vencimiento,lote,laboratorio,fecha_carga,id_presentacion,id_grupo_riesgo,origen,id_usuario,id_prestacion_cristian,id_archivo) 
                    values($id_prestacion_inmu,$id_vacuna_dosis,$id_vacuna,'$cuie',$clavebeneficiario,$id_terreno,'$fecha_comprobante','$fecha_nacimiento','$fecha_vencimiento','$lote','$laboratorio',date_trunc('seconds',localtimestamp),$id_presentacion,$id_grupo_riesgo,$origen,$id_usuario,$id_prestacion_cristian,$id_archivo)";
        $retorno=sql($query, "Error al insertar en prestaciones_inmu") or fin_pagina();
    }
    public static function setPrestacionInmuTxtDebito($id_prestacion_inmu,$id_vacuna_dosis,$id_vacuna,$cuie,$clavebeneficiario,$id_terreno,$fecha_comprobante,$fecha_nacimiento,$fecha_vencimiento,$lote,$laboratorio,$id_presentacion,$id_grupo_riesgo,$origen,$id_usuario,$id_prestacion_cristian,$linea,$id_archivo,$mensajes,$numero_fila){
        $query = "insert into inmunizacion.prestaciones_inmu_debitos(id_prestacion_inmu_debito,id_vacuna_dosis,id_vacuna,cuie,clave_beneficiario,id_terreno,fecha_inmunizacion,
                    fecha_nacimiento,fecha_vencimiento,lote,laboratorio,fecha_carga,id_presentacion,id_grupo_riesgo,origen,id_usuario,id_prestacion_cristian,linea,id_archivo,mensajes,numero_fila) 
                    values($id_prestacion_inmu,$id_vacuna_dosis,$id_vacuna,'$cuie',$clavebeneficiario,$id_terreno,'$fecha_comprobante','$fecha_nacimiento','$fecha_vencimiento','$lote','$laboratorio',date_trunc('seconds',localtimestamp),$id_presentacion,$id_grupo_riesgo,$origen,$id_usuario,$id_prestacion_cristian,'$linea',$id_archivo,'$mensajes',$numero_fila)";
        $retorno=sql($query, "Error al insertar en prestaciones_inmu_debito") or fin_pagina();
    }
    public static function invertirFechaAAAAMMDD($fecha){
        $ano=substr($fecha, 6, 4);
        $mes=substr($fecha, 3, 2);
        $dia=substr($fecha, 0, 2);
        $retorno=$ano."/".$mes."/".$dia;
        return $retorno;
    }
    public static function obtenerPeriodoPrestacion($fecha){ // 2013-05-27
        $ano=substr($fecha, 0, 4);
        $mes=substr($fecha, 5, 2);
        $dia=substr($fecha, 8, 2);
        $retorno=$ano."/".$mes;
        return $retorno;
    }
    public static function getIdPrestacionCristian($id_prestacion_cristian){
        $query = "select id_prestacion_cristian from inmunizacion.prestaciones_inmu where id_prestacion_cristian=$id_prestacion_cristian";
        $retorno=sql($query, "Error al obtener id_prestacion_inmu") or fin_pagina();
        $retorno=$retorno->fields["id_prestacion_cristian"];
        return $retorno;
    }
    public static function getPrestacion($id_prestacion_inmu){  //Terminar
        $query = "select * from inmunizacion.prestaciones_inmu where id_ ";
        $retorno=sql($query, "Error al obtener max_id") or fin_pagina();
        $retorno=$retorno->fields["max_id"];
        return ++$retorno;
    }
    public static function borrarPrestacionesAceptadas($id_archivo){ 
        $query = "delete from inmunizacion.prestaciones_inmu where id_archivo=$id_archivo";
        $retorno=sql($query, "Error al obtener prestaciones Aceptadas") or fin_pagina();
        if($retorno){
            $retorno='S';
        }else{
            $retorno='N';
        }
        return $retorno;
    }
    public static function borrarPrestacionesRechazadas($id_archivo){ 
        $query = "delete from inmunizacion.prestaciones_inmu_debitos where id_archivo=$id_archivo";
        $retorno=sql($query, "Error al obtener prestaciones Rechazadas") or fin_pagina();
        if($retorno){
            $retorno='S';
        }else{
            $retorno='N';
        }
        return $retorno;
    }
    
}

?>
