<?php
ob_start();

require_once ("../../config.php");

function IsBusqueda(){
    if ($_POST['buscar'] && $_POST['f_desde']){
        return (True);
    }else{
        return(False);
    }
} 

$a = array("f_desde" => $f_desde, "f_hasta" => $f_hasta, );
if ($_POST['buscar']) {
    if ($_POST['f_desde'] == '' && $_POST['f_hasta'] == '') {
    $a = array();
  }
}


variables_form_busqueda("resumen_promotor");
$orden = array("default" => "1", "1" => "agente", "2" => "fecha_carga", "3" =>
  "centro_inscriptor", );
$filtro = array("nombreefector" => "Efector", "agente" => "Promotor", "dni_agente" =>
  "Nro Doc. Promotor",);

$where_tmp = "";
if ($_POST['f_desde'] != '' && $_POST['f_hasta'] != '') {
  $where_tmp = "fecha_carga between '" . Fecha_db($_POST['f_desde']) . "' and '" . Fecha_db($_POST['f_hasta']) .
    "' ";
}
//if ($where_tmp != "")
//  $where_tmp .= " AND ";

//$where_tmp .= " NOT EXISTS(SELECT tipo_doc, documento	FROM puco.puco WHERE tipo_doc = tipo_documento AND documento = CAST(numero_doc AS INT)) ";
$sql_tmp = "select  centro_inscriptor,nombreefector,agente,dni_agente,sum(tcompleta)as tcompleta,sum(tenviado)as tenviado,fecha_carga
		from (select formulario.centro_inscriptor,upper(trim(formulario.apellidoagente)||' '||trim(formulario.nombreagente)) as agente
					,trim(formulario.dni_agente)as dni_agente,count(*) as tcompleta
					,case when remediar_x_beneficiario.enviado='s' then count(*) end as tenviado
					,cast(remediar_x_beneficiario.fecha_carga as char(10))fecha_carga
					FROM  remediar.formulario
				 	inner join uad.remediar_x_beneficiario on formulario.nroformulario=remediar_x_beneficiario.nroformulario
					inner join uad.beneficiarios on beneficiarios.clave_beneficiario=remediar_x_beneficiario.clavebeneficiario
					where DATE(fechaempadronamiento) - DATE(beneficiarios.fecha_nacimiento_benef)>= 2190
                                        and length(numero_doc) in (5,6,7,8,9) 
                                        AND NOT EXISTS(SELECT tipo_doc, documento FROM puco.puco WHERE tipo_doc = tipo_documento AND documento = CAST(numero_doc AS BIGINT)) 
					group by formulario.centro_inscriptor,upper(trim(formulario.apellidoagente)||' '||trim(formulario.nombreagente))
					,trim(formulario.dni_agente),  remediar_x_beneficiario.enviado,cast(remediar_x_beneficiario.fecha_carga as char(10)))as a
					left join facturacion.smiefectores on smiefectores.cuie=a.centro_inscriptor";

$where_tmp .= " group by centro_inscriptor,nombreefector,agente,dni_agente,fecha_carga";


// 
echo $html_header;

// Total enviadas y total completadas
$tcompletadas = 0;
$tenviadas = 0;
?>
<script>
//Validar Fechas
function esFechaValida(fecha){
    if (fecha != undefined && fecha.value != "" ){
        if (!/^\d{2}\/\d{2}\/\d{4}$/.test(fecha.value)){
            alert("Formato de fecha no valido (dd/mm/aaaa)");
            return false;
        }
        var dia  =  parseInt(fecha.value.substring(0,2),10);
        var mes  =  parseInt(fecha.value.substring(3,5),10);
        var anio =  parseInt(fecha.value.substring(6),10);
 
    switch(mes){
        case 1:
        case 3:
        case 5:
        case 7:
        case 8:
        case 10:
        case 12:
            numDias=31;
            break;
        case 4: case 6: case 9: case 11:
            numDias=30;
            break;
        case 2:
            if (comprobarSiBisisesto(anio)){ numDias=29 }else{ numDias=28};
            break;
        default:
            alert("Fecha introducida erronea");
            return false;
    }
 
        if (dia>numDias || dia==0){
            alert("Fecha introducida erronea");
            return false;
        }
        return true;
    }
}
 
function comprobarSiBisisesto(anio){
if ( ( anio % 100 != 0) && ((anio % 4 == 0) || (anio % 400 == 0))) {
    return true;
    }
else {
    return false;
    }
}

var patron = new Array(2,2,4)
var patron2 = new Array(5,16)
function mascara(d,sep,pat,nums){
if(d.valant != d.value){
    val = d.value
    largo = val.length
    val = val.split(sep)
    val2 = ''
    for(r=0;r<val.length;r++){
        val2 += val[r]
    }
    if(nums){
    for(z=0;z<val2.length;z++){
        if(isNaN(val2.charAt(z))){
            letra = new RegExp(val2.charAt(z),"g")
            val2 = val2.replace(letra,"")
        }
    }
}
val = ''
val3 = new Array()
for(s=0; s<pat.length; s++){
val3[s] = val2.substring(0,pat[s])
val2 = val2.substr(pat[s])
}
for(q=0;q<val3.length; q++){
if(q ==0){
val = val3[q]

}
else{
if(val3[q] != ""){
val += sep + val3[q]
}
}
}
d.value = val
d.valant = val
}
}

 function validar_formulario(){
    FDesde = document.getElementById("f_desde").value;
    FHasta = document.getElementById("f_hasta").value;
    
    if (FDesde.length < 10)
    {   
        alert("Debe ingresar un rango de fecha para realizar una busqueda" );   
        return false;
  
    } 
    else {
        document.getElementByName('mensaje').innerHTML= "Realizando consulta espere por favor";
        return true;
    }
  
 
}
</script>


<form name=form1 action="resumen_promotor" method=POST>
    
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>

      <td align=center>
      	
<?
        if (isBusqueda()){
            list($sql, $total_muletos, $link_pagina, $up) = form_busqueda($sql_tmp, $orden, $filtro, $link_tmp, $where_tmp, "buscar");
        }else{
            
            list($sql, $total_muletos, $link_pagina, $up) = form_busqueda_nosql($sql_tmp, $orden, $filtro, $link_tmp, $where_tmp, "buscar");
        }
?>

          &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar' onClick="return validar_formulario()">		    
	     &nbsp;&nbsp;
	    <?
//echo  $sql;
//$sql.=" group by centro_inscriptor,nombreefector,agente,dni_agente";
$link = encode_link("resumen_promotor_excel.php", array("sql" => substr($sql, 0,
  strpos($sql, 'LIMIT'))));
?>
        <img src="../../imagenes/excel.gif" style='cursor:hand;'  onclick="window.open('<?=
$link
?>')">    
	  </td>
     </tr>
	 <tr>
	 <td align=center>
	 <b>Fecha Desde:</b><input type="text" size="10" maxlength="10" name="f_desde" id="f_desde" onblur="esFechaValida(this);" onchange="esFechaValida(this);" onKeyUp="mascara(this,'/',patron,true);" value="<?=
$f_desde
?>" <?=
$r
?>/><?=
link_calendario('f_desde');
?> <b> Hasta: </b><input type="text" size="10" maxlength="10" name="f_hasta" id="f_hasta" onblur="esFechaValida(this);" onchange="esFechaValida(this);" onKeyUp="mascara(this,'/',patron,true);" value="<?=
$f_hasta
?>" <?=
$r
?>/><?=
link_calendario('f_hasta');
?>
	 </td>
	 
         </tr>
</table>

<?php
echo $sql;

if (IsBusqueda()){
//echo $sql;
$result = sql($sql) or die;
}
?>
    
<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=
$bgcolor3
?>' align=center>
  <tr>
  	<td colspan=4 align=left id=ma>
     <table width=100%>
      <tr id=ma>
          <td width=30% align=left><b>Total:</b> <? if(IsBusqueda()){ echo $total_muletos; }?></td>
       <td width=40% align=right><?
        if(IsBusqueda()){
            echo $link_pagina;
        }?></td>
       <td>
           <br />
       </td>
       <td>
           <div id=totComp>0</div>
       </td>
       
       <td>
           <div id=totEnv>Total de planillas enviadas: </div>
       </td>
      </tr>
    </table>
   </td>
  </tr>
  <tr >
    <td align=right id=mo>Lugar</td>
    <td align=right id=mo>Promotor</td>
	<td align=right id=mo>DNI</td>
	<td align=right id=mo>Fecha Carga</td>
    <td align=right id=mo>Fichas Completas(IdyFr)</td>
	<td align=right id=mo>Fichas Enviadas</td>
  </tr>
 <?
 if(IsBusqueda()){
    while (!$result->EOF) {
?>
    <tr <?=
  atrib_tr()
?> >     
     <td><?=
  $result->fields['centro_inscriptor'] . '-' . $result->fields['nombreefector']
?></td>
     <td><?
  if (rtrim($result->fields['agente']) != '') {
    echo $result->fields['agente'];
  } else {
    echo 'S/D';
  }
?></td>
	 <td><?
  if (rtrim($result->fields['dni_agente']) != '') {
    echo $result->fields['dni_agente'];
  } else {
    echo 'S/D';
  }
?></td>
	 <td><?=
  fecha($result->fields['fecha_carga'])
?></td>
     <td><?=
  $result->fields['tcompleta'];
  $tcompletadas += $result->fields['tcompleta'];
?></td>
	 <td><?=
  $result->fields['tenviado'];
  $tenviadas += $result->fields['tenviado'];
?></td>
    </tr>
	<?
  $result->MoveNext();
}
}
?>
    
</table>
    


<?php 
echo "Total enviadas:" . $tenviadas; 
echo "<br />";
echo "Total completadas:" . $tcompletadas; 

?>
</form>

<script type="text/javascript">
document.getElementById('totComp').innerHTML= '<?php echo "Total Completadas:" . $tcompletadas; ?>';
document.getElementById('totEnv').innerHTML= '<?php echo "Total Enviadas:" . $tenviadas; ?>';
</script>

<!-- Script para colocar en formulario la fecha que fue enviada -->
<script type="text/javascript">
    document.getElementByName('f_desde').innerHTML= '<?php echo $_POST['f_desde']; ?>';
    document.getElementByName('f_hasta').innerHTML= '<?php echo $_POST['f_hasta']; ?>';
</script>
</body>
</html>
<?
echo fin_pagina(); // aca termino
ob_end_flush();
?>
