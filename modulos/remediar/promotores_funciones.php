<?php
require_once ("../../config.php");

$idOperacion = $_POST['operacion'];

$efector_codremediar = $_POST['codremediar'];
$localidad_idlocprov = $_POST['idlocalidad'];


$efectorAreaProg = "select ap.nombre as ap from general.relacioncodigos rl
    inner join facturacion.smiefectores f on f.cuie = rl.cuie
    inner join uad.localidades loc on f.ciudad  = loc.nombre
    inner join general.areas_programaticas ap on loc.id_areaprogramatica = ap.id_area_programatica
    where rl.codremediar = '".$efector_codremediar."'";

$localidadAreaProg = "select ap.nombre as ap from uad.localidades loc
inner join general.areas_programaticas ap on ap.id_area_programatica = loc.id_areaprogramatica
where loc.idloc_provincial = ".$localidad_idlocprov." ";

switch ($idOperacion) {
    case 'efectorAP':
        if ($efector_codremediar != 'NOTOSH'){
        $efector_codremediar = sql($efectorAreaProg) or die();
        echo $efector_codremediar->fields['ap'];}
        else{
           echo "Debe seleccionar un Efector" ;
        }
        break;

    case 'localidadAP':
        if($localidad_idlocprov != 'NOTOSH'){
        $localidad_idlocprov = sql($localidadAreaProg) or die();
        echo $localidad_idlocprov->fields['ap'];}
        else{
            echo "Debe seleccionar una Localidad" ;
        }
        break;

    
    default:
        break;
}





?>
