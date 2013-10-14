<?php
//require_once ("../../../config.php");

class Presentacion {
    public static function getPresentacion(){
        $sql = "select * from inmunizacion.presentaciones";
        $retorno=sql($sql, "Error al traer Presentaciones") or fin_pagina();
        return $retorno;
    }
    //put your code here
}

?>
