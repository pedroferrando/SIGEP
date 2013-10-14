<?php

require_once ("../../config.php");
require_once ("./remediar_seguimiento_funciones.php");


$sql=$parametros["sql"];
$filtro_factor = $parametros["filtro_facttor"];
$filtro_tipo = $parametros["filtro_tipo"];

$beneficiarios_result=sql($sql) or fin_pagina();

excel_header("resumen_operador.xls");


switch ($filtro_tipo) {
    case "NOTOSH":
        $filtro_tipo = "Ningun Filtro";
        $filtro_factor = "--";
        break;
    
    case "Efector":
        $filtro_tipo = "Codigo de Efector";
        break;
    case "DNI":
        $filtro_tipo = "Documento del beneficiario";
        break;
    
    case "Apellido":
        $filtro_tipo = "Apellido del beneficiario";
        break;
    
    case "Enviado":
        $filtro_tipo = "Beneficiarios Enviados en archivo C";
        $filtro_factor = "--";
        break;
    
    case "NoEnviado":
        $filtro_tipo = "Beneficiarios No Enviados en archivo C";
        $filtro_factor = "--";
        break;
    
    default:
        break;
}

?>

<form action="">
    <div align="center"><h2>Remediar + Redes: Beneficiarios clasificados</h2></div>
    <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5">
        <tr>
            <td style="background: #C3DB7B"><b>Generado el: <?=date("d-m-Y \a\ \l\a\s\ H:i:s")?></b></td>
            <td style="background: #C3DB7B"><b>Filtrado por: <?=$filtro_factor?></b></td>
            <td style="background: #C3DB7B"><b>En base a: <?=$filtro_tipo?></b></td>
        </tr>
    </table>
    <br />
    
    <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5">
        <tr>
            <td colspan="20"></td>
            <td style="background: #F5DA81" colspan="11" align="center"><b>Alto Riesgo Per-se</b></td>
            <td colspan="1" align="center"></td>
            <td style="background: #F2F5A9"colspan="9" align="center"><b>Condiciones que aumentan el Factor de Riesgo</b></td>

        </tr>
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
            <td style="background: #ABE876"><b>Ta Sistolica</b></td>
            <td style="background: #ABE876"><b>Ta Diasistolica</b></td>
            <td style="background: #ABE876"><b>Colesterol Total</b></td>
            <td style="background: #ABE876"><b>Dmt2</b></td>
            <td style="background: #ABE876"><b>HTA</b></td>  
            <td style="background: #ABE876"><b>Fecha de Control</b></td>  
            <td style="background: #ABE876"><b>Medico</b></td>
            <td style="background: #ABE876"><b>Formulario Nro.</b></td>
            <td></td>
            <td style="background: #ABE876"><b>ACV</b></td>
            <td style="background: #ABE876"><b>Vasculopat&iacute;a perif&eacute;rica</b></td>
            <td style="background: #ABE876"><b>Cardiopat&iacute;a isqu&eacute;mica</b></td>
            <td style="background: #ABE876"><b>Colesterol Total >= 310 mg/dl</b></td>
            <td style="background: #ABE876"><b>Colesterol LDL >= 230 mg/dl</b></td>
            <td style="background: #ABE876"><b>Relaci&oacute;n CT/HDL > 8</b></td>
            <td style="background: #ABE876"><b>Presi&oacute;n arterial permanentemente elevadas</b></td>
            <td style="background: #ABE876"><b>Diabetes</b></td>
            <td style="background: #ABE876"><b>Insuficiencia Renal</b></td>
            <td style="background: #ABE876"><b>Diabetes Tipo 2 Menor de 39 a&ntilde;os</b></td>
            <td style="background: #ABE876"><b>Hipertensi&oacute;n Menor de 39 a&ntilde;os</b></td>
            <td></td>
            <td style="background: #ABE876"><b>Menospausia prematura</b></td>
            <td style="background: #ABE876"><b>Tratamiento antihipertensivo</b></td>
            <td style="background: #ABE876"><b>Obesidad</b></td>
            <td style="background: #ABE876"><b>ACV Prematuros</b></td>
            <td style="background: #ABE876"><b>Triglic&eacute;ridos elevados</b></td>
            <td style="background: #ABE876"><b>HDL bajos</b></td>
            <td style="background: #ABE876"><b>Hiperglucemia en ayunas</b></td>
            <td style="background: #ABE876"><b>Microalbuminuria</b></td>
            <td style="background: #ABE876"><b>Tabaquismo</b></td>
            
            <td style="background: #ABE876"><b>Enviado</b></td>
            <td style="background: #ABE876"><b>Fecha de Envio</b></td>
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
        
        if($beneficiarios_result->fields['enviado'] == "N"){
          $beneficiarios_result->fields['fecha_envio'] = "No enviado aun.";
        }

        #   Control de DMT2
        if(strlen($beneficiarios_result->fields['dmt2']) < 1){
          $beneficiarios_result->fields['dmt2'] = '--';
        }else{
            if ($beneficiarios_result->fields['dmt2'] == 0) {
                $beneficiarios_result->fields['dmt2'] = "No";
            }else{
                $beneficiarios_result->fields['dmt2'] = "Si";
            }
        }
        #   Control de HTA
        if(strlen($beneficiarios_result->fields['hta']) < 1){
          $beneficiarios_result->fields['hta'] = '--';
        }else{
            if ($beneficiarios_result->fields['hta'] == 0) {
                $beneficiarios_result->fields['hta'] = "No";
            }else{
                $beneficiarios_result->fields['hta'] = "Si";
            }
        }

        #   Control de TA Sistolica
        if(strlen($beneficiarios_result->fields['ta_sist']) < 1){
          $beneficiarios_result->fields['ta_sist'] = '--';
        }else{
            if ($beneficiarios_result->fields['ta_sist'] == 0) {
                $beneficiarios_result->fields['ta_sist'] = "No Controlado";
            }
        }

         #   Control de TA DiaSistolica
        if(strlen($beneficiarios_result->fields['ta_diast']) < 1){
          $beneficiarios_result->fields['ta_diast'] = '--';
        }else{
            if ($beneficiarios_result->fields['ta_diast'] == 0) {
                $beneficiarios_result->fields['ta_diast'] = "No Controlado";
            }
        }

        #   Control de Colesterol Total
        if(strlen($beneficiarios_result->fields['col_tot']) < 1){
          $beneficiarios_result->fields['col_tot'] = '--';
        }else{
            if ($beneficiarios_result->fields['col_tot'] == 0) {
                $beneficiarios_result->fields['col_tot'] = "No Controlado";
            }
        }



        #   Validar campo
        switch(true){
          case (strlen($beneficiarios_result->fields['acv']) < 1):
            $beneficiarios_result->fields['acv'] = "--";
            break;
          case($beneficiarios_result->fields['acv'] == '1'):
            $beneficiarios_result->fields['acv'] = "Si";
            break;
          case ($beneficiarios_result->fields['acv'] == '0'):
            $beneficiarios_result->fields['acv'] = "No";
            break;
          default:
            break;
        }


        #   Validar campo
        switch(true){
          case (strlen($beneficiarios_result->fields['vas_per']) < 1):
            $beneficiarios_result->fields['vas_per'] = "--";
            break;
          case($beneficiarios_result->fields['vas_per'] == '1'):
            $beneficiarios_result->fields['vas_per'] = "Si";
            break;
          case ($beneficiarios_result->fields['vas_per'] == '0'):
            $beneficiarios_result->fields['vas_per'] = "No";
            break;
          default:
            break;
        }


        #   Validar campo
        switch(true){
          case (strlen($beneficiarios_result->fields['car_isq']) < 1):
            $beneficiarios_result->fields['car_isq'] = "--";
            break;
          case($beneficiarios_result->fields['car_isq'] == '1'):
            $beneficiarios_result->fields['car_isq'] = "Si";
            break;
          case ($beneficiarios_result->fields['car_isq'] == '0'):
            $beneficiarios_result->fields['car_isq'] = "No";
            break;
          default:
            break;
        }


        #   Validar campo
        switch(true){
          case (strlen($beneficiarios_result->fields['col310']) < 1):
            $beneficiarios_result->fields['col310'] = "--";
            break;
          case($beneficiarios_result->fields['col310'] == '1'):
            $beneficiarios_result->fields['col310'] = "Si";
            break;
          case ($beneficiarios_result->fields['col310'] == '0'):
            $beneficiarios_result->fields['col310'] = "No";
            break;
          default:
            break;
        }


        #   Validar campo
        switch(true){
          case (strlen($beneficiarios_result->fields['col_ldl']) < 1):
            $beneficiarios_result->fields['col_ldl'] = "--";
            break;
          case($beneficiarios_result->fields['col_ldl'] == '1'):
            $beneficiarios_result->fields['col_ldl'] = "Si";
            break;
          case ($beneficiarios_result->fields['col_ldl'] == '0'):
            $beneficiarios_result->fields['col_ldl'] = "No";
            break;
          default:
            break;
        }


        #   Validar campo
        switch(true){
          case (strlen($beneficiarios_result->fields['ct_hdl']) < 1):
            $beneficiarios_result->fields['ct_hdl'] = "--";
            break;
          case($beneficiarios_result->fields['ct_hdl'] == '1'):
            $beneficiarios_result->fields['ct_hdl'] = "Si";
            break;
          case ($beneficiarios_result->fields['ct_hdl'] == '0'):
            $beneficiarios_result->fields['ct_hdl'] = "No";
            break;
          default:
            break;
        }


        #   Validar campo
        switch(true){
          case (strlen($beneficiarios_result->fields['pres_art']) < 1):
            $beneficiarios_result->fields['pres_art'] = "--";
            break;
          case($beneficiarios_result->fields['pres_art'] == '1'):
            $beneficiarios_result->fields['pres_art'] = "Si";
            break;
          case ($beneficiarios_result->fields['pres_art'] == '0'):
            $beneficiarios_result->fields['pres_art'] = "No";
            break;
          default:
            break;
        }


        #   Validar campo
        switch(true){
          case (strlen($beneficiarios_result->fields['dmt']) < 1):
            $beneficiarios_result->fields['dmt'] = "--";
            break;
          case($beneficiarios_result->fields['dmt'] == '1'):
            $beneficiarios_result->fields['dmt'] = "Si";
            break;
          case($beneficiarios_result->fields['dmt'] == '2'):
            $beneficiarios_result->fields['dmt'] = "Si";
            break;

          case ($beneficiarios_result->fields['dmt'] == '0'):
            $beneficiarios_result->fields['dmt'] = "No";
            break;
          default:
            break;
        }


        #   Validar campo
        switch(true){
          case (strlen($beneficiarios_result->fields['insu_renal']) < 1):
            $beneficiarios_result->fields['insu_renal'] = "--";
            break;
          case($beneficiarios_result->fields['insu_renal'] == '1'):
            $beneficiarios_result->fields['insu_renal'] = "Si";
            break;
          case ($beneficiarios_result->fields['insu_renal'] == '0'):
            $beneficiarios_result->fields['insu_renal'] = "No";
            break;
          default:
            break;
        }


        #   Validar campo
        switch(true){
          case (strlen($beneficiarios_result->fields['dmt_menor']) < 1):
            $beneficiarios_result->fields['dmt_menor'] = "--";
            break;
          case($beneficiarios_result->fields['dmt_menor'] == '1'):
            $beneficiarios_result->fields['dmt_menor'] = "Si";
            break;
          case ($beneficiarios_result->fields['dmt_menor'] == '0'):
            $beneficiarios_result->fields['dmt_menor'] = "No";
            break;
          default:
            break;
        }


        #   Validar campo
        switch(true){
          case (strlen($beneficiarios_result->fields['hta_menor']) < 1):
            $beneficiarios_result->fields['hta_menor'] = "--";
            break;
          case($beneficiarios_result->fields['hta_menor'] == '1'):
            $beneficiarios_result->fields['hta_menor'] = "Si";
            break;
          case ($beneficiarios_result->fields['hta_menor'] == '0'):
            $beneficiarios_result->fields['hta_menor'] = "No";
            break;
          default:
            break;
        }


        #   Validar campo
        switch(true){
          case (strlen($beneficiarios_result->fields['menopausia']) < 1):
            $beneficiarios_result->fields['menopausia'] = "--";
            break;
          case($beneficiarios_result->fields['menopausia'] == '1'):
            $beneficiarios_result->fields['menopausia'] = "Si";
            break;
          case ($beneficiarios_result->fields['menopausia'] == '0'):
            $beneficiarios_result->fields['menopausia'] = "No";
            break;

          case ($beneficiarios_result->fields['menopausia'] == ' '):
            $beneficiarios_result->fields['menopausia'] = "No";
            break;
          default:
            break;
        }


        #   Validar campo
        switch(true){
          case (strlen($beneficiarios_result->fields['antihiper']) < 1):
            $beneficiarios_result->fields['antihiper'] = "--";
            break;
          case($beneficiarios_result->fields['antihiper'] == '1'):
            $beneficiarios_result->fields['antihiper'] = "Si";
            break;
          case ($beneficiarios_result->fields['antihiper'] == '0'):
            $beneficiarios_result->fields['antihiper'] = "No";
            break;
          default:
            break;
        }


        #   Validar campo
        switch(true){
          case (strlen($beneficiarios_result->fields['obesi']) < 1):
            $beneficiarios_result->fields['obesi'] = "--";
            break;
          case($beneficiarios_result->fields['obesi'] == '1'):
            $beneficiarios_result->fields['obesi'] = "Si";
            break;
          case ($beneficiarios_result->fields['obesi'] == '0'):
            $beneficiarios_result->fields['obesi'] = "No";
            break;
          default:
            break;
        }


        #   Validar campo
        switch(true){
          case (strlen($beneficiarios_result->fields['acv_prema']) < 1):
            $beneficiarios_result->fields['acv_prema'] = "--";
            break;
          case($beneficiarios_result->fields['acv_prema'] == '1'):
            $beneficiarios_result->fields['acv_prema'] = "Si";
            break;
          case ($beneficiarios_result->fields['acv_prema'] == '0'):
            $beneficiarios_result->fields['acv_prema'] = "No";
            break;
          default:
            break;
        }


        #   Validar campo
        switch(true){
          case (strlen($beneficiarios_result->fields['trigli']) < 1):
            $beneficiarios_result->fields['trigli'] = "--";
            break;
          case($beneficiarios_result->fields['trigli'] == '1'):
            $beneficiarios_result->fields['trigli'] = "Si";
            break;
          case ($beneficiarios_result->fields['trigli'] == '0'):
            $beneficiarios_result->fields['trigli'] = "No";
            break;
          default:
            break;
        }


        #   Validar campo
        switch(true){
          case (strlen($beneficiarios_result->fields['hdl_col']) < 1):
            $beneficiarios_result->fields['hdl_col'] = "--";
            break;
          case($beneficiarios_result->fields['hdl_col'] == '1'):
            $beneficiarios_result->fields['hdl_col'] = "Si";
            break;
          case ($beneficiarios_result->fields['hdl_col'] == '0'):
            $beneficiarios_result->fields['hdl_col'] = "No";
            break;
          default:
            break;
        }


        #   Validar campo
        switch(true){
          case (strlen($beneficiarios_result->fields['hiperglu']) < 1):
            $beneficiarios_result->fields['hiperglu'] = "--";
            break;
          case($beneficiarios_result->fields['hiperglu'] == '1'):
            $beneficiarios_result->fields['hiperglu'] = "Si";
            break;
          case ($beneficiarios_result->fields['hiperglu'] == '0'):
            $beneficiarios_result->fields['hiperglu'] = "No";
            break;
          default:
            break;
        }


        #   Validar campo
        switch(true){
          case (strlen($beneficiarios_result->fields['microalbu']) < 1):
            $beneficiarios_result->fields['microalbu'] = "--";
            break;
          case($beneficiarios_result->fields['microalbu'] == '1'):
            $beneficiarios_result->fields['microalbu'] = "Si";
            break;
          case ($beneficiarios_result->fields['microalbu'] == '0'):
            $beneficiarios_result->fields['microalbu'] = "No";
            break;
          default:
            break;
        }


        #   Validar campo
        switch(true){
          case (strlen($beneficiarios_result->fields['tabaquismo']) < 1):
            $beneficiarios_result->fields['tabaquismo'] = "--";
            break;
          case($beneficiarios_result->fields['tabaquismo'] == '1'):
            $beneficiarios_result->fields['tabaquismo'] = "Si";
            break;
          case ($beneficiarios_result->fields['tabaquismo'] == '0'):
            $beneficiarios_result->fields['tabaquismo'] = "No";
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
        
        <td id="celda"><?=$beneficiarios_result->fields['ta_sist']?></td>
        <td id="celda"><?=$beneficiarios_result->fields['ta_diast']?></td>
        <td id="celda"><?=$beneficiarios_result->fields['col_tot']?></td>
        <td id="celda"><?=$beneficiarios_result->fields['dmt2']?></td>
        <td id="celda"><?=$beneficiarios_result->fields['hta']?></td>
        <td id="celda"><?=$beneficiarios_result->fields['fecha_control']?></td>
        <td id="celda"><?=($beneficiarios_result->fields['apellido_medico'].", ".$beneficiarios_result->fields['nombre_medico'])?></td>
        <td id="celda"><?=$beneficiarios_result->fields['nro_clasificacion']?></td>
        <td></td>

        <td id="celda" ><?=$beneficiarios_result->fields['acv']?></td>
        <td id="celda" ><?=$beneficiarios_result->fields['vas_per']?></td>
        <td id="celda" ><?=$beneficiarios_result->fields['car_isq']?></td>
        <td id="celda" ><?=$beneficiarios_result->fields['col310']?></td>
        <td id="celda" ><?=$beneficiarios_result->fields['col_ldl']?></td>
        <td id="celda" ><?=$beneficiarios_result->fields['ct_hdl']?></td>
        <td id="celda" ><?=$beneficiarios_result->fields['pres_art']?></td>
        <td id="celda" ><?=$beneficiarios_result->fields['dmt']?></td>
        <td id="celda" ><?=$beneficiarios_result->fields['insu_renal']?></td>
        <td id="celda" ><?=$beneficiarios_result->fields['dmt_menor']?></td>
        <td id="celda" ><?=$beneficiarios_result->fields['hta_menor']?></td>
        <td></td>

        <td id="celda" ><?=$beneficiarios_result->fields['menopausia']?></td>
        <td id="celda" ><?=$beneficiarios_result->fields['antihiper']?></td>
        <td id="celda" ><?=$beneficiarios_result->fields['obesi']?></td>
        <td id="celda" ><?=$beneficiarios_result->fields['acv_prema']?></td>
        <td id="celda" ><?=$beneficiarios_result->fields['trigli']?></td>
        <td id="celda" ><?=$beneficiarios_result->fields['hdl_col']?></td>
        <td id="celda" ><?=$beneficiarios_result->fields['hiperglu']?></td>
        <td id="celda" ><?=$beneficiarios_result->fields['microalbu']?></td>
        <td id="celda" ><?=$beneficiarios_result->fields['tabaquismo']?></td>







        <td><?=$beneficiarios_result->fields['enviado']?></td>
        
        <td><?=$beneficiarios_result->fields['fecha_envio']?></td>
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