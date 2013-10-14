<?php
//require_once ("../../../config.php");

class Vacuna {
    //put your code here
    public static function getDosisVacuna($idVacuna){
        $query = "SELECT vd.id_vacuna_dosis, vd.descripcion, vd.descripcion_abreviada
                  FROM inmunizacion.vacunas_dosis vd 
                  WHERE vd.id_vacuna='$idVacuna' 
                  ORDER BY vd.descripcion ASC";
        $retorno = sql($query, "Error al traer las Dosis de Vacuna") or fin_pagina();
        return $retorno;
    }
    public static function getVacunasPorCodigo($codigo){
        $query = "select * from inmunizacion.vacunas_dosis where codigo='$codigo' and habilitado=1";
        $retorno=sql($query, "Error al traer Vacunas por codigo") or fin_pagina();
        return $retorno;
    }
    public static function getIdVacuna($id_vacuna_dosis){
        $query = "select id_vacuna from inmunizacion.vacunas_dosis where id_vacuna_dosis=$id_vacuna_dosis";
        $retorno=sql($query, "Error al traer id vacuna ") or fin_pagina();
        $retorno=$retorno->fields["id_vacuna"];
        return $retorno;
    }
    public static function setCaracteristica($id_vacuna_dosis){
        $query = "select id_vacuna from inmunizacion.vacunas_dosis where id_vacuna_dosis=$id_vacuna_dosis";
        $retorno=sql($query, "Error al traer id vacuna ") or fin_pagina();
        $retorno=$retorno->fields["id_vacuna"];
        return $retorno;
    }
    public static function liquidarInmunizacion($id_inmunizacion,$id_cierre,$id_liquidacion){
        global $_ses_user;
        $query = "UPDATE inmunizacion.prestaciones_inmu 
                  SET id_cierre=$id_cierre,
                      id_liquidacion=$id_liquidacion,
                      fecha_carga_liq=now(),
                      usuario_carga_liq=$_ses_user[id] 
                  WHERE id_prestacion_inmu=$id_inmunizacion";
        sql($query);
    }
    public static function liquidarInmunizaciones($id_archivo,$id_cierre,$id_liquidacion){
        global $_ses_user;
        $query = "UPDATE inmunizacion.prestaciones_inmu 
                  SET id_cierre=$id_cierre,
                      id_liquidacion=$id_liquidacion,
                      fecha_carga_liq=now(),
                      usuario_carga_liq=$_ses_user[id] 
                  WHERE id_archivo=$id_archivo";
        sql($query);
    }
}

?>
