<?php

require_once ("../../config.php");

$sql=$parametros["sql"];
$fechaDesde = $parametros["fechadesde"];
$fechaHasta = $parametros["fechahasta"];

$result=sql($sql) or fin_pagina();

excel_header("resumen_promotor.xls");

?>
<form name=form1 method=post action="resumen_promotor_excel.php">
 <br>
 <h1> Resumen de promotores Remediar + Redes</h1>
 
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id=mo>Nombre</td>
    <td align=right id=mo>Apellido</td>
    <td align=right id=mo>DNI</td>
    <td align=right id=mo>Fecha Nacimiento</td>
    <td align=right id=mo>Telefono</td>
    <td align=right id=mo>Email</td>
    <td align=right id=mo>Efector Asignado</td>
    <td align=right id=mo>Efector (Cod Remediar)</td>
    <td align=right id=mo>Localidad</td>
    <td align=right id=mo>Area Programatica</td>
    <td align=right id=mo>Banco</td>
    <td align=right id=mo>Tipo de Cta</td>
    <td align=right id=mo>Nro Cta</td>
    <td align=right id=mo>Planilllas</td>
    
  </tr>
  <?   
    while (!$result->EOF) {

        $efector_NombreSQL = "select f.nombreefector as efector from general.relacioncodigos rl
            inner join facturacion.smiefectores f on f.cuie=rl.cuie
            where rl.codremediar = '".$result->fields['efector']."'
            and rl.codremediar <> ''";
        
        $efector_nombreResult = sql($efector_NombreSQL) or die();
  ?>
  <tr>     
        <td align=right><?=$result->fields['nombre']?></td>
        <td align=right><?=$result->fields['apellido']?></td>
        <td align=right><?=$result->fields['dni']?></td>
        <td align=right><?=$result->fields['fechanac']?></td>
        <td align=right><?=$result->fields['telefono']?></td>
        <td align=right><?=$result->fields['email']?></td>
        <td align=right><?=$efector_nombreResult->fields['efector']?></td>
        <td align=right><?=$result->fields['efector']?></td>
        <td align=right><?=$result->fields['localidad']?></td>
        <td align=right><?=$result->fields['ap']?></td>
        <td align=right><?=$result->fields['banco']?></td>
        <td align=right><?=$result->fields['tipocuenta']?></td>
        <td align=right><?=$result->fields['numerocuenta']?></td>
        <td align=right><?=$result->fields['planillas']?></td>
    </tr>
 <?php 
 $result->MoveNext();
 }
 ?>
 </table>
    
 
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
     <tr bgcolor=#C0C0FF>
         <td align=right id=mo>Rango de Fecha</td>
         <td align=right id=mo>Generado</td>
     </tr>
     <tr>
        <td>
              <div id=totComp><?=(Fecha($fechaDesde) ." - ". Fecha($fechaHasta)) ?></div>
        </td>
       
        <td>
            <div id=totEnv> El <?=date("d-m-Y \a\ \l\a\s H:i:s")?></div>
        </td>
     </tr>
 </table>
 </form>

