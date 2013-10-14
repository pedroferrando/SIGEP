<?php

class Beneficiario {
    //put your code here    
    public static function getVacunas($clave_beneficiario){
        $query = "select * from inmunizacion.prestaciones_inmu where clave_beneficiario='$clave_beneficiario'";
        $retorno=sql($query, "Error al traer Vacunas") or fin_pagina();
        return $retorno;
    }
    public static function getBeneficiario($clave_beneficiario){
        $query = "select * from uad.beneficiarios where clave_beneficiario='$clave_beneficiario'";
        $retorno=sql($query, "Error al traer Beneficiario") or fin_pagina();
        return $retorno;
    }
}

?>
