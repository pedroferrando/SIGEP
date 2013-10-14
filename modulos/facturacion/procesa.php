<?php

require_once ("../../config.php");
require_once("../../lib/funciones_misiones.php");

if (isset($_POST["idvacuna"])) {
    $opciones6 = '<option value="-1"> Seleccione Dosis </option>';

    $strConsulta = "select idvacuna, vacuna from inmunizacion.vacunas where idvacuna = '" . $_POST['idvacuna'] . "' order by vacuna";
    $result = @pg_exec($strConsulta);


    while ($fila = pg_fetch_array($result)) {
        $opciones6.='<option value="' . $fila["idvacuna"] . '">' . $fila["vacuna"] . '</option>';
    }

    echo $opciones6;
}
if (isset($_POST["tipo_persona"])) {
    $opciones7 = '<option value="-1"> Seleccione Vacuna </option>';

    $strConsulta = "select idvacuna, nombre from inmunizacion.vacunasreal where categoria = '" . $_POST['tipo_persona'] . "' order by nombre";
    $result = @pg_exec($strConsulta);


    while ($fila = pg_fetch_array($result)) {
        $opciones7.='<option value="' . $fila["idvacuna"] . '">' . $fila["nombre"] . '</option>';
    }

    echo $opciones7;
}

if (isset($_POST["cuie"])) {
    $cuie = $_POST["cuie"];
    $res_tipos = tiposDePracticaPorEfector($cuie);
    $opciones7 = "<select id='tipo_nomenclador' name=tipo_prestacion Style='width:450px'> <option value='-1'> Seleccione </option>";
    if ($res_tipos->fields['nom_basico'] == 't') {
        $opciones7 .= '<option  value=BASICO >Basico</option>';
    }
    if ($res_tipos->fields['nom_basico_2'] == 't') {
        $opciones7 .= '<option  value=BASICO_2 >Basico 2</option>';
    }
    if ($res_tipos->fields['nom_cc_catastrofico'] == 't') {
        $opciones7 .= '<option  value=CC_CATASTROFICO >CC Catastrofico</option>';
    }
    if ($res_tipos->fields['nom_perinatal_catastrofico'] == 't') {
        $opciones7 .= '<option  value=PERINATAL_CATASTROFICO >Perinatal Catastrofico</option>';
    }
    if ($res_tipos->fields['nom_cc_nocatastrofico'] == 't') {
        $opciones7 .= '<option  value=CC_NOCATASTROFICO>CC No Catastrofico</option>';
    }
    if ($res_tipos->fields['nom_perinatal_nocatastrofico'] == 't') {
        $opciones7 .= '<option  value=PERINATAL_NOCATASTROFICO >Perinatal No Catastrofico</option>';
    }
    if ($res_tipos->fields['nom_rondas'] == 't') {
        $opciones7 .= '<option  value=RONDAS >Rondas</option>';
    }

    $opciones7 .= '</select>';
    echo $opciones7;
}
?>
