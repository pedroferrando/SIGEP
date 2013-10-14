<?php

require_once("../../config.php");
require_once("genera_archivo_remediar_seguimiento_funciones.php");


$periodo = substr($fechaemp, 0, 4);
$elperiodo = intval(substr($fechaemp, 5, 2));
if ($elperiodo >= 9) {
    $elperiodo = '12';
} elseif ($elperiodo >= 5) {
    $elperiodo = '08';
} else {
    $elperiodo = '04';
}
$periodo.=$elperiodo;


$usuarioGeneracion = $_ses_user['id'];
$archivoSeguimiento = new ArchivoSeguimiento();
$fechaCarga = explode(" ", $fechakrga);

$beneficiarios_sql = "select s.idseguimiento, s.clavebeneficiario, s.dmta, s.hta, s.tasist, s.tadiast, s.tabaquismo, s.colesterol, s.imc, s.glucemia, s.peso, s.talla, s.hba1c, s.ecg, 
	s.fondodeojo, s.examenpie, s.microalbuminuria ,s.hdl, s.ldl, s.tags, s.creatininemia, s.interconsulta_a, s.interconsulta_b, s.interconsulta_c, 
	s.interconsulta_d, UPPER(s.rcvg_anterior) as rcvg_anterior, UPPER(s.rcvg_actual) as rcvg_actual, s.fecha_carga, s.estado_envio, s.id_usuariovalidador,s.fecha_seguimiento,
	s.estado_validacion, s.efector, s.num_seguimiento, b.tipo_documento,
	b.numero_doc,b.calle,b.numero_calle,b.manzana,b.piso,b.dpto,b.entre_calle_1,b.entre_calle_2,
	b.barrio, b.municipio,b.departamento,b.localidad,b.cod_pos,b.provincia_nac,b.telefono, b.sexo, 
	b.fecha_nacimiento_benef as fecha_nacimiento, b.nombre_benef as nombre, b.apellido_benef as apellido

	from trazadoras.seguimiento_remediar s
	inner join uad.beneficiarios b on b.clave_beneficiario = s.clavebeneficiario
	where s.estado_envio = 0 and s.fecha_carga <= '".$fechaemp."' and s.fecha_seguimiento <= '".$fechaCarga[0]."'
	order by s.clavebeneficiario, s.fecha_seguimiento, s.num_seguimiento";

#echo $beneficiarios_sql;

$beneficiarios_result = sql($beneficiarios_sql) or die();


while (!$beneficiarios_result->EOF) {
    
    $beneficiario = new BeneficiarioSeguimiento();
    $beneficiario->construirBeneficiario($beneficiarios_result);
    
    
    
    
    if($beneficiarios_result->fields['num_seguimiento'] < 7){
        if($beneficiarios_result->fields['num_seguimiento'] == 1 && $beneficiarios_result->fields['rcvg_anterior'] == "BAJO"){
            #echo "<div align='center'><b>Beneficiario ".$beneficiarios_result->fields['numero_doc'].", por RCVG bajo en clasificacion BAJO</b>";
        }else{
            $archivoSeguimiento->agregarBeneficiario($beneficiario);
        }
    }
    $beneficiarios_result->MoveNext();
    
    sql($beneficiario->sqlEstadoEnviado()) or die();
}




$archivoSeguimiento->setUsuario($usuarioGeneracion);
$archivoSeguimiento->setPeriodo($periodo);


$fp = fopen(($archivoSeguimiento->getProtoripoArchivo()."_simulacion"),"w");
fwrite($fp, $archivoSeguimiento->getLineasArchivo());
fclose($fp);
?>
    <fieldset>
        <legend>Archivo de informe</legend>
    
        <?php
        echo "<div align='center'><b>El Archivo (".($archivoSeguimiento->getProtoripoArchivo()."_simulacion").") se genero con exito </b>";
        echo "<a href='".($archivoSeguimiento->getProtoripoArchivo()."_simulacion")."'><b>Ver</b></a></div>";
        ?>
</fieldset>