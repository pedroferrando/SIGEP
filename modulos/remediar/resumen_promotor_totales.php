<?php

require_once ("../../config.php");

$tcompletadas = 0;
$promotoresLista_sql = "select apellido,nombre from remediar.promotores order by apellido";
$promotoresLista = sql($promotoresLista_sql) or die();
$promotores_selectNonvalue = "NoToSh";



function isBusqueda(){
    $rta = False;
    if ($_POST['buscar']){
        $rta = True;
    }
    return($rta);
}


if (isBusqueda()){
    $promotores_filtroNombre = $_POST['promotor'];
    
    if($_POST['fecha_hasta'] == ''){
            $_POST['fecha_hasta'] = date("d/m/Y");
    }
    
    $promotores_filtroFechaDesde = Fecha_db($_POST['fecha_desde']);
    $promotores_filtroFechaHasta = Fecha_db($_POST['fecha_hasta']);

    if($promotores_filtroNombre == "NOTOSH"){
        
        $promotores_sql = "select p.idpromotor, p.apellido, p.nombre, p.dni, p.fechanac, l.nombre as localidad, ap.nombre as ap, p.idefector as efector, 
        p.telefono, p.email, b.nombre as banco, t.nombre as tipocuenta, p.nrocuenta as numerocuenta, count(form.id_formulario) as planillas
        from remediar.promotores p
        left join remediar.formulario form on form.apellidoagente = p.apellido and form.nombreagente = p.nombre
        inner join uad.remediar_x_beneficiario rxb on form.nroformulario = rxb.nroformulario
        inner join uad.beneficiarios benef on benef.clave_beneficiario = rxb.clavebeneficiario
        left join uad.localidades l on p.idlocalidad = l.idloc_provincial
        left join general.areas_programaticas ap on l.id_areaprogramatica = ap.id_area_programatica
        left join facturacion.smiefectores f on p.idefector = f.cuie
        left join general.bancos b on b.idbanco = p.idbanco
        left join general.tiposcuentas t on t.idtipocuenta = p.idtipocuenta
        where DATE(rxb.fechaempadronamiento) - DATE(benef.fecha_nacimiento_benef)>= 2190
        and length(benef.numero_doc) in (5,6,7,8,9)
        AND NOT EXISTS(SELECT tipo_doc, documento FROM puco.puco 
                WHERE tipo_doc = benef.tipo_documento 
                AND documento = CAST(benef.numero_doc AS BIGINT))
        
        and rxb.fecha_carga between '".$promotores_filtroFechaDesde."' and '".$promotores_filtroFechaHasta."'
        group by p.apellido, p.nombre, p.dni, p.fechanac, l.nombre, p.idefector, 
        p.telefono, p.email, b.nombre, t.nombre, p.nrocuenta, ap.nombre, p.idpromotor
        order by p.apellido, p.nombre"; 
        
    }else{
        $promotores_filtroNombre = explode(",",$promotores_filtroNombre);
        
        $promotores_sql = "select p.idpromotor, p.apellido, p.nombre, p.dni, p.fechanac, l.nombre as localidad, ap.nombre as ap, p.idefector as efector, 
        p.telefono, p.email, b.nombre as banco, t.nombre as tipocuenta, p.nrocuenta as numerocuenta, count(form.id_formulario) as planillas
        from remediar.promotores p
        inner join remediar.formulario form on form.apellidoagente = p.apellido and form.nombreagente = p.nombre
        inner join uad.remediar_x_beneficiario rxb on form.nroformulario = rxb.nroformulario
        inner join uad.beneficiarios benef on benef.clave_beneficiario = rxb.clavebeneficiario
        inner join uad.localidades l on p.idlocalidad = l.idloc_provincial
        inner join general.areas_programaticas ap on l.id_areaprogramatica = ap.id_area_programatica
        left join facturacion.smiefectores f on p.idefector = f.cuie
        left join general.bancos b on b.idbanco = p.idbanco
        left join general.tiposcuentas t on t.idtipocuenta = p.idtipocuenta
        where DATE(rxb.fechaempadronamiento) - DATE(benef.fecha_nacimiento_benef)>= 2190
        and length(benef.numero_doc) in (5,6,7,8,9)
        AND NOT EXISTS(SELECT tipo_doc, documento FROM puco.puco 
                WHERE tipo_doc = benef.tipo_documento 
                AND documento = CAST(benef.numero_doc AS BIGINT))
        and rxb.fecha_carga between '".$promotores_filtroFechaDesde."' and '".$promotores_filtroFechaHasta."'
        and p.apellido = trim('".$promotores_filtroNombre[0]."')
        and p.nombre = trim('".$promotores_filtroNombre[1]."')
        group by p.apellido, p.nombre, p.dni, p.fechanac, l.nombre, p.idefector, 
        p.telefono, p.email, b.nombre, t.nombre, p.nrocuenta, ap.nombre, p.idpromotor
        order by p.apellido, p.nombre";         
    }    
    # Link para generar el XLS
    $link = encode_link("resumen_promotor_totales_excel.php", array("sql" => $promotores_sql, "fechadesde" => $promotores_filtroFechaDesde, "fechahasta" => $promotores_filtroFechaHasta));
    
    # Sql de promotores
    $promotores = sql($promotores_sql) or die();
   
    # Calculo de dias dentro del rango de fechas
    list($anio, $mes, $dia) = explode("-", $promotores_filtroFechaDesde);
    $rangoFecha_inicio = mktime(0, 0, 0, $mes, $dia, $anio);
    list($anio, $mes, $dia) = explode("-", $promotores_filtroFechaHasta);
    $rangoFecha_fin = mktime(0, 0, 0, $mes, $dia, $anio);
    $rangoFecha_total = (int) (($rangoFecha_fin - $rangoFecha_inicio)/(60 * 60 * 24));
    if ($rangoFecha_total < 0){
        $rangoFecha_total = 0;
    }
}

echo $html_header;

?>
<script type="text/javascript" src="../../lib/jquery-1.7.2.min.js"> </script>
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
        var fdesde = $("#f_desde_text");
        fdesde.css({'background':'#FF6633'});
        alert("Debe ingresar un rango de fecha para realizar una busqueda" );
        return false;
  
    } 
    else {
        var container = $("#mensaje");
        container.html("Procesando peticion");
        container.css({'color':'black','font-size':'3em', 'background':'#CCE6FF'});
        
        return true;
    }
  
 
}

</script>


<div name="mensaje" id="mensaje" align="center"><h1><?=$_GET['w']?></h1></div>
<form name=form1 action="resumen_promotor_totales" method=POST>  
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align="center"></td>
      
     </tr>
        <tr>
            <td align=center>
           <b> Filtro por Promotor: </b>
                <select name="promotor">
                    <option value="NOTOSH">Sin Filtro</option>
                        <?php while (!$promotoresLista->EOF){
                            $data = $promotoresLista->fields['apellido'] . ", " .$promotoresLista->fields['nombre'] ;
                            if($data == $_POST['promotor']){
                                ?>
                                <option value ="<?= $data ?>" selected="selected"><?= $data ?></option>
                            <?php
                            }else{  ?>
                                <option value ="<?= $data ?>"><?= $data ?></option>
                            
                        <?php    
                            }
                        $promotoresLista->MoveNext(); }?>
                </select>	
           
           &nbsp;
            <b id="f_desde_text">Fecha Desde:</b><input type="text" size="10" maxlength="10" name="fecha_desde" id="f_desde" onblur="esFechaValida(this);" onchange="esFechaValida(this);" onKeyUp="mascara(this,'/',patron,true);" value="<?=$_POST['fecha_desde']?>" <?=$r?>/><?=link_calendario('fecha_desde');?> 
            <b> Hasta: </b><input type="text" size="10" maxlength="10" name="fecha_hasta" id="f_hasta" onblur="esFechaValida(this);" onchange="esFechaValida(this);" onKeyUp="mascara(this,'/',patron,true);" value="<?=$_POST['fecha_hasta']?>"/><?=link_calendario('fecha_hasta');?> </td> 
            
            <td align="center"><img src="../../imagenes/excel.gif" style='cursor:hand;'  onclick="window.open('<?= $link?>')"> </td>
         </tr>
         
         <tr>
             <td align="center"><input type=submit name="buscar" value='Buscar' onClick="return validar_formulario()"></td>
         </tr>
     
</table>
</form>

  

<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
    <tr>
      <td colspan=4 align=left id=ma>

          <table width=100%>
               <tr id=ma>
                    <td width=30% align=left><div id="totRegistros"></div></td>
                    <td></td>
                    <td> <br /> </td>
                    <td width=30%> <div id="totPlanillas"></div></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td width=30% align="left">Rango: <?=$rangoFecha_total?> d&iacute;as</td>
                    <td id="promPlanillas"></td>
                    
                                                                                  
              </tr>
      </table>

     </td>
    </tr>
    <tr >
      
    <td align=right id=mo>Apellido, Nombre</td>
    <td align=right id=mo>DNI</td>
    <td align=right id=mo>Contacto</td>
    <td align=right id=mo>Efector asignado</td>
    <td align=right id=mo>AREA PROG. / Localidad</td>
    <td align=right id=mo>Banco</td>
    <td align=right id=mo>Cuenta</td>
    <td align=right id=mo>Planillas</td>
    <td align=right id=mo>Editar</td>

    
  </tr>
 <?
 # Nombre = Nombre + apellido
 # ZONA = LOCALIDAD + ZONA SANIT + AREA PROG
 # Contacto = (tel) + (email)
 # Cuenta: (Tipocuenta) (NROCUENTA)
 if (isBusqueda()){
     $bgTcolor = '#E6FFCC';
     $totalPlanillas = 0;
     $totalRegistros = 0;
    while(!$promotores->EOF){
        $editRef = encode_link("promotores_admin.php",array("idpromotor"=>$promotores->fields['idpromotor']));
        $editOnclick="location.href='$editRef'";
        $efector_NombreSQL = "select f.nombreefector as efector from general.relacioncodigos rl
        inner join facturacion.smiefectores f on f.cuie=rl.cuie
        where rl.codremediar = '".$promotores->fields['efector']."'
        and rl.codremediar <> ''";
        
        $efector_nombreResult = sql($efector_NombreSQL) or die();
        
        ?>
        <tr >
      
            <td align=left bgcolor="<?=$bgTcolor?>"><?=($promotores->fields['apellido'].", ".$promotores->fields['nombre'] )?></td>
            <td align=right bgcolor="<?=$bgTcolor?>"><?=$promotores->fields['dni']?></td>
            <td align=right bgcolor="<?=$bgTcolor?>"><?=($promotores->fields['telefono']." - ".$promotores->fields['email'])?></td>
            <td align=right bgcolor="<?=$bgTcolor?>"><?=$efector_nombreResult->fields['efector']?></td>
            <td align=right bgcolor="<?=$bgTcolor?>"><?=($promotores->fields['ap']."-".$promotores->fields['localidad'])?></td>
            <td align=right bgcolor="<?=$bgTcolor?>"><?=$promotores->fields['banco']?></td>
            <td align=right bgcolor="<?=$bgTcolor?>"><?=($promotores->fields['tipocuenta']." - ".$promotores->fields['numerocuenta'])?></td>
            <td align=right bgcolor="<?=$bgTcolor?>"><?=$promotores->fields['planillas']?></td>
            <td align="center" bgcolor="<?=$bgTcolor?>"><input type="submit" name="editar" value="Editar" onclick="<?=$editOnclick?>"></td>
        </tr>
        
        
        <?php
        
        if ($bgTcolor == '#E6FFCC'){
                $bgTcolor = 'CCFFFF';
            }else{
                $bgTcolor = '#E6FFCC';
        }
       $totalPlanillas +=$promotores->fields['planillas'];
       $totalRegistros +=1;
       $promotores->MoveNext();
    }
 }

?>
    
</table>
    



<script type="text/javascript">
document.getElementById('totPlanillas').innerHTML= '<?php echo "Total Planillas:" . $totalPlanillas; ?>';
document.getElementById('totRegistros').innerHTML= '<?php echo "Registros:" . $totalRegistros; ?>';


</script>

<!-- Script para colocar en formulario la fecha que fue enviada -->


<?
echo fin_pagina(); // aca termino
?>
