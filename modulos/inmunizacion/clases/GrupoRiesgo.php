<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GrupoRiesgo
 *
 * @author lrobin
 */
class GrupoRiesgo {
    //put your code here
    public static function getGruposRiesgo(){
        $sql = "select * from inmunizacion.grupos_riesgos";
        $retorno=sql($sql, "Error al traer grupos riesgo") or fin_pagina();
        return $retorno;
    }
}

?>
