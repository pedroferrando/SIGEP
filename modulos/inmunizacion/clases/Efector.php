<?php
//require_once ("../../../config.php");

class Efector {
    //put your code here
    public static function getTerrenos($cuiel){
        $sql = "select * from inmunizacion.terrenos 
        where id_terreno in(select id_terreno from inmunizacion.terrenos_efectores where cuie='$cuiel')";
        $retorno=sql($sql, "Error al traer Terrenos") or fin_pagina();
        return $retorno;
    }
    public static function getEfector($cuie){
        $sql = "SELECT *
                FROM facturacion.smiefectores
                WHERE cuie='$cuie'";
        $retorno=sql($sql, "Error al traer Efector") or fin_pagina();
        return $retorno;
    }
    public static function getCuie($cuie){
        $query = "select cuie from facturacion.smiefectores where cuie='$cuie'";
        $retorno=sql($query, "Error al obtener el efector") or fin_pagina();
        $retorno=$retorno->fields["cuie"];
        return $retorno;
    }
    public static function getClaveBeneficiarioUadBeneficiarios($clavebeneficiario){
        $query = "select clave_beneficiario from uad.beneficiarios where clave_beneficiario='$clavebeneficiario'";
        $retorno=sql($query, "Error al obtener clave beneficiario") or fin_pagina();
        $retorno=$retorno->fields["clave_beneficiario"];
        return $retorno;
    }
    public static function getClaveBeneficiarioNacerSmiAfiliados($clavebeneficiario){
        $query = "select clavebeneficiario from nacer.smiafiliados where clavebeneficiario='$clavebeneficiario'";
        $retorno=sql($query, "Error al obtener clave beneficiario") or fin_pagina();
        $retorno=$retorno->fields["clavebeneficiario"];
        return $retorno;
    }
}
?>