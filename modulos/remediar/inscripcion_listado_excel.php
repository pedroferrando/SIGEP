<?php

require_once ("../../config.php");
require_once ("./remediar_seguimiento_funciones.php");


$sql=$parametros["sql"];
$filtro_factor = $parametros["filtro_facttor"];
$filtro_tipo = $parametros["filtro_tipo"];
$filtro_NoClasificado = $parametros["noClasificado"];

$fechaEmpadronamientoDesde = fecha_db($parametros["fechaDesde"]);
$fechaEmpadronamientoHasta = fecha_db($parametros["fechaHasta"]);


$beneficiarios_result=sql($sql) or fin_pagina();

excel_header("resumen_operador.xls");
 
    switch ($filtro_tipo) {
        case "DNI":
            $filtro_tipo = "Documento del Beneficiario";
            break;
        case "Apellido":
            $filtro_tipo = "Apellido del Beneficiario";
            break;
        case "Efector":
            $filtro_tipo = "Codigo de Efector";    
            break;
        
        case "Enviado":
            $filtro_tipo = "Benefciarios Enviados";    
            break;

        case "NoEnviado":
            $filtro_tipo = "Beneficiarios No Enviados";    
            break;
        
        case "RiesgoIgual":
            $filtro_tipo = "Beneficiarios con Riesgo Igual";    
            break;
        
        case "RiesgoMenor":
            $filtro_tipo = "Beneficiarios con Riesgo Menor";    
            break;
        
        case "RiesgoMayor":
            $filtro_tipo = "Beneficiarios con Riesgo Mayor";    
            break;
        
        case "RiesgoMayorIgual":
            $filtro_tipo = "Beneficiarios con Riesgo Mayor o Igual";    
            break;
        
        case "RiesgoMenorIgual":
            $filtro_tipo = "Beneficiarios con Riesgo Manor o Igual";    
            break;
        
        case "NOTOSH":
            $filtro_tipo = "Ningun filtro especifico";    
            break;
        
        default:    
            break;

    }

    if (strlen($filtro_NoClasificado)> 0) {
        $filtro_NoClasificado = "No admitidos";
    }else{
        $filtro_NoClasificado = "Admitidos";
    }

?>

<form action="">
    <div align="center" style="background: #006699"><h2>Programa Remediar + Redes: Beneficiarios Inscriptos - Misiones</h2></div>
    <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5">
        <tr>
            <td style="background: #C3DB7B"><b>Generado el: <?=date("d-m-Y \a\ \l\a\s\ H:i:s")?></b></td>
            <td style="background: #C3DB7B"><b>Filtrado por: <?=$filtro_factor?></b></td>
            <td style="background: #C3DB7B"><b>En base a: <?=$filtro_tipo?></b></td>
            </tr>
        <tr>
            <td style="background: #C3DB7B"><b>Entre: <?=($fechaEmpadronamientoDesde." y ".$fechaEmpadronamientoHasta)?></b></td>
            <td style="background: #C3DB7B"><b>Beneficiarios clasificados: <?=$filtro_NoClasificado?></b></td>
        </tr>
    </table>
    <br />
    
    <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5">
        <tr>
            <td style="background: #ABE876"><b>Apellido</b></td>
            <td style="background: #ABE876"><b>Nombre</b></td>
            <td style="background: #ABE876"><b>Edad</b></td>
            <td style="background: #ABE876"><b>DNI</b></td>
            <td style="background: #ABE876"><b>Fecha Nacimiento</b></td>
            <td style="background: #ABE876"><b>Departamento</b></td>
            <td style="background: #ABE876"><b>Municipio</b></td>
            <td style="background: #ABE876"><b>Riesgo</b></td>
            <td style="background: #ABE876"><b>Efector</b></td>
            <td style="background: #ABE876"><b>Area Programatica (Efector)</b></td>
            <td style="background: #ABE876"><b>Cod Efector</b></td>
            <td style="background: #ABE876"><b>Fecha de Inscripcion</b></td>
            <td style="background: #ABE876"><b>Promotor</b></td>
            <td style="background: #ABE876"><b>Enviado</b></td>
        </tr>
      <?php
    $trBgColor = "#C1DAD6";
    $beneficiarios_cantidad = 0;
    while(!$beneficiarios_result->EOF){
        $data = encode_link("remediar_seguimiento.php", array("cbeneficiario" => $beneficiarios_result->fields['clave_beneficiario']));
        $accion = "location.href='".$data."'";
        
                switch ($beneficiarios_result->fields['riesgo']) {
            case "MODE":
                $beneficiarios_result->fields['riesgo'] = "MODERADO";
                break;

            case "MODERAD":
                $beneficiarios_result->fields['riesgo'] = "MODERADO";
                break;
            
            case "MALTO":
                $beneficiarios_result->fields['riesgo'] = "MUYALTO";
                break;
           
            default:
                break;
        }
        
        ?>
    <tr bgcolor="<?=$trBgColor?>">
        
        <td id="celda" ><?=$beneficiarios_result->fields['apellido']?></td>
        <td id="celda"><?=$beneficiarios_result->fields['nombre']?></td>
        <td id="celda"><?=CalculaEdad($beneficiarios_result->fields['fecha_nac'])." a&ntilde;os" ?></td>
        <td id="celda"><?=$beneficiarios_result->fields['numero_doc']?></td>
        <td id="celda"><?=$beneficiarios_result->fields['fecha_nac']?></td>
        <td id="celda"><?=$beneficiarios_result->fields['departamento']?></td>
        <td id="celda"><?=$beneficiarios_result->fields['municipio']?></td>
        <td id="celda"><?=$beneficiarios_result->fields['riesgo']?></td>
        <td id="celda"><?=$beneficiarios_result->fields['nombreefector']?></td>
        <td id="celda"><?=$beneficiarios_result->fields['ap']?></td>
        <td><?=$beneficiarios_result->fields['efector']?></td>
        <td><?=$beneficiarios_result->fields['fecha_inscripcion']?></td>
        <td><?=($beneficiarios_result->fields['apellidoagente'].", ".$beneficiarios_result->fields['nombreagente'])?></td>
        <td><?=$beneficiarios_result->fields['enviado']?></td>
    </tr>
        <?php $beneficiarios_result->MoveNext();
        if ($trBgColor == "#F5FAFA") {
         $trBgColor = "#C1DAD6";
        }else{  
         $trBgColor = "#F5FAFA";   
        }
        
        $beneficiarios_cantidad ++;
    } ?>
    
    <tr style="background: #FFFFFF">
        <td></td>
    </tr>
    <tr>
        <td style="background: #ABE876"><b>Cantidad de Beneficiarios: </b></td>
        <td style="background: #ABE876"><?=$beneficiarios_cantidad?></td>
    </tr>
    </table>
</form>