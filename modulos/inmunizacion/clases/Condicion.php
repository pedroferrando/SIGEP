<?php
//require_once ("../../../config.php");

class Condicion {
    public static function getCondiciones(){
        $sql = "select * from inmunizacion.caracteristicas";
        $retorno=sql($sql, "Error al traer Caracteristicas") or fin_pagina();
        return $retorno;
    }
    //put your code here
}

?>
