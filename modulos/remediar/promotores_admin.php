<?php

require_once ("../../config.php");
$promotor_id = $parametros['idpromotor'];

#   Sirve para retornar la sentencia del formulario que lo generó
#   Unicamente añade una facilidad visual, al no tener que realizar una busqueda
#   nuevamente.
$sqlRetorno = $parametros['sqlRetorno'];
$mensajeExterno = $parametros['mensajeExterno'];

$boolValue = strlen($promotor_id) > 0;

function isEditar(){
    $rta = false;
    if($_POST['persistir']){
        $rta = true;
    }
    return($rta);
}

if(isEditar()){
    
    $promotor_id = $_POST['idPromotor'];
    $promotorNombre = $_POST['nombrePromotor'];
    $promotorApellido = $_POST['apellidoPromotor'];
    $promotorDni = $_POST['dniPromotor'];
    $promotorFechaNac  = fecha_db($_POST['fechanacPromotor']);
    $promotorLocalidad = $_POST['localidadPromotor'];
    $promotorEfector = $_POST['efectorPromotor'];
    $promotorTelefono = $_POST['promotorTelefono'];
    $promotorEmail = $_POST['promotorEmail'];
    $promotorBanco = $_POST['promotorBanco'];
    $promotorTipoCuenta = $_POST['promotorTipoCta'];
    $promotorNroCuenta = $_POST['promotorNumCta'];
    
    if ($promotorNroCuenta == ''){
        $promotorNroCuenta = '0';
    }
        
    
    $s_toperacion = $_POST['s_toperacion'];
  
    if ($s_toperacion == "editar") {
        
        $sql_operacion = "UPDATE remediar.promotores
       SET idpromotor=".$promotor_id.", nombre='".$promotorNombre."', apellido='".$promotorApellido."', 
           dni='".$promotorDni."', fechanac='".$promotorFechaNac."', idlocalidad=".$promotorLocalidad.", 
           idefector='".$promotorEfector."', telefono='".$promotorTelefono."', email='".$promotorEmail."', 
           idbanco=".$promotorBanco.", nrocuenta='".$promotorNroCuenta."', idtipocuenta=".$promotorTipoCuenta."
        WHERE idpromotor=".$promotor_id."";
        
        $promotor_operacionResult = sql($sql_operacion) or die();
    }  
    elseif($s_toperacion == "nuevo") {
       $sql_operacion = "INSERT INTO remediar.promotores(
            nombre, apellido, dni, fechanac, idlocalidad, idefector, 
            telefono, email, idbanco, nrocuenta, idtipocuenta)
            
            VALUES ('".$promotorNombre."', '".$promotorApellido."', '".$promotorDni."', 
                '".$promotorFechaNac."', ".$promotorLocalidad.", '".$promotorEfector."', 
            '".$promotorTelefono."', '".$promotorEmail."', ".$promotorBanco.", '".$promotorNroCuenta."', ".$promotorTipoCuenta.")";
        
        $promotor_operacionResult = sql($sql_operacion) or die();
    }
    else{
        echo "<h1>ERROR EN SISTEMA</h1>";
    }
    
    ?>
        
        <script language="JavaScript">
            <?php
                $editRef = encode_link("promotores_listado.php",array("sqlRetorno"=>$sqlRetorno, 
                            "Mensaje" => "Promotor Guardado (".$promotorApellido.", ".$promotorNombre.")"));
            ?>
            location.href='<?=$editRef?>';
        </script>
        <?php
 
    
}else{
 
    if($boolValue){
    
    $promotor_sqlConsulta = "Select p.idpromotor, p.nombre, p.apellido, p.dni, p.fechanac, l.nombre as localidad, 
        p.idefector as efector, 
        p.telefono, p.email, bco.idbanco as banco, p.nrocuenta, tcta.idtipocuenta as tipocuenta
        from remediar.promotores p 
            left join uad.localidades l on l.idloc_provincial = p.idlocalidad 
            left join general.relacioncodigos rl on p.idefector = rl.codremediar
            left join facturacion.smiefectores f on p.idefector = f.cuie
            left join general.bancos bco on p.idbanco = bco.idbanco
            left join general.tiposcuentas tcta on tcta.idtipocuenta = p.idtipocuenta
        where idpromotor = ".$promotor_id."
        limit 1";
    
    $promotores_result = sql($promotor_sqlConsulta) or die();
    
    $promotor_id = $promotores_result->fields['idpromotor'];
    $promotorNombre = $promotores_result->fields['nombre'];
    $promotorApellido = $promotores_result->fields['apellido'];
    $promotorDni = $promotores_result->fields['dni'];
    $promotorFechaNac  = $promotores_result->fields['fechanac'];
    $promotorLocalidad = $promotores_result->fields['localidad'];
    $promotorEfector = $promotores_result->fields['efector'];
    $promotorTelefono = $promotores_result->fields['telefono'];
    $promotorEmail = $promotores_result->fields['email'];
    $promotorBanco = $promotores_result->fields['banco'];
    $promotorTipoCuenta = $promotores_result->fields['tipocuenta'];
    $promotorNroCuenta = $promotores_result->fields['nrocuenta'];
    
    $s_toperacion = "editar";
    
    }
    else{
        
    $promotorNombre = '';
    $promotorApellido = '';
    $promotorDni = '';
    $promotorFechaNac  = '';
    $promotorLocalidad = '';
    $promotorEfector = '';
    $promotorTelefono = '';
    $promotorEmail = '';
    $promotorBanco = '';
    $promotorTipoCuenta = '';
    $promotorNroCuenta = '';
    
    $s_toperacion = "nuevo";
    }
    
    
}

    #   Localidades 
    $localidades_sqlConsulta = "select l.idloc_provincial ,l.nombre as localidad, l.codigopostal as cp, ap.nombre as ap
        from uad.localidades l
        inner join general.areas_programaticas ap on l.id_areaprogramatica = ap.id_area_programatica
        order by localidad";
    
    #   Efectores
    $efectrores_sqlConsulta = "select f.nombreefector as efector, rl.codremediar, loc.idloc_provincial, ap.nombre as ap
        from facturacion.smiefectores f
        inner join general.relacioncodigos rl on rl.cuie = f.cuie 
        and rl.codremediar <> ''
        and rl.codremediar is not null
	inner join uad.localidades loc on f.ciudad = loc.nombre
	inner join general.areas_programaticas ap on loc.id_areaprogramatica = ap.id_area_programatica
	";
    
    #   Bancos
    $bancos_sqlConsulta = "select idbanco, nombre as banco from general.bancos";
    
    #   Tipos de cuenta
    $tiposCuentas_sqlConsulta = "select idtipocuenta, nombre as tipocuenta from general.tiposcuentas";
    
    
    #   Efectores Result
    $efectores_result = sql($efectrores_sqlConsulta) or die();
    
    #   Localidades Result
    $localidades_result = sql($localidades_sqlConsulta) or die();
    
    #   Bancos Result
    $bancos_result = sql($bancos_sqlConsulta) or die();
    
    #   Tipos de cuenta Result
    $tiposCuentas_result = sql($tiposCuentas_sqlConsulta) or die();
    
    
    #   Link de retorno
    $link = encode_link("resumen_promotor_totales.php", array("mensaje" => "El promotor ha sido guardado con exito"));
    
    echo $html_header;
    

?>
<script type="text/javascript" src="../../lib/jquery-1.7.2.min.js"> </script>
<script>
var aprog=new Array();    

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

function cargarDatosLocalidad(selectObj){
    $.post("promotores_funciones.php",{"operacion":"localidadAP", "idlocalidad": selectObj.options[selectObj.selectedIndex].value},
    function(respuesta){
        var container = $("#promotorAreaProg");
        container.html(respuesta);
    });
}


function cargarDatosEfector(selectObj){  
    $.post("promotores_funciones.php",{"operacion":"efectorAP", "codremediar":selectObj.options[selectObj.selectedIndex].value},
    function(respuesta){
        var container = $("#efectorAreaProg");
        container.html(respuesta);
    });
}


function validar_formulario(){
    
    Efector =  document.formDatos.efectorPromotor.selectedIndex;
    Efector =  document.formDatos.efectorPromotor.options[Efector].value
    
    Localidad =  document.formDatos.localidadPromotor.selectedIndex;
    Localidad =  document.formDatos.localidadPromotor.options[Localidad].value
    
    Banco = document.formDatos.promotorBanco.selectedIndex;
    Banco = document.formDatos.promotorBanco.options[Banco].value;
    
    TipoCta = document.formDatos.promotorTipoCta.selectedIndex;
    TipoCta = document.formDatos.promotorTipoCta.options[TipoCta].value;
    
    
    DataForm = true;
    
    if (Efector == "NOTOSH")
    {
        alert("Debe Seleccionar un Efector");
        DataForm = false;
        return(false);
    }
    
    if (Localidad == "NOTOSH")
    {
        alert("Debe Seleccionar una Localidad");
        DataForm = false;
        return(false);
    }
    
    if (Banco == "NOTOSH")
    {
        alert("Debe Seleccionar un Banco");
        DataForm = false;
        return(false);
    }
    
    
    if (TipoCta == "NOTOSH")
    {
        alert("Debe Seleccionar un Tipo de cuenta");
        DataForm = false;
        return(false);
    } 
    if(DataForm){
       return(true);
    }
    else{
        return(false);
    }
}

</script>

<div align="center" id="mo"><h2><?=$mensajeExterno?></h2></div>


<form name="formDatos" action="promotores_admin" method="POST"> 
    <br />
    <fieldset>
            <legend><b>Datos Personales</b></legend>
		<p></p>
		<table>
	
			<tr>			
				<td id="mo">Nombre</td>
				<td id="mo">Apellido</td>
				<td id="mo">DNI</td>
				<td id="mo">Fecha Nac.</td>
                                <td id="mo">Localidad</td>
                                <td id="mo">Area Programatica</td>
			</tr>
		
			<tr>
				<td><input type="text" style="text-transform: uppercase" name="nombrePromotor" value="<?= $promotorNombre ?>" /></td>
				<td><input type="text" style="text-transform: uppercase" name="apellidoPromotor" value="<?= $promotorApellido ?>" /></td>
				<td><input type="text" style="text-transform: uppercase" name="dniPromotor" value="<?= $promotorDni ?>" /></td>
				<td><input type="text" size="10" maxlength="10" name="fechanacPromotor" id="fechanacPromotor" onblur="esFechaValida(this);" onchange="esFechaValida(this);" onKeyUp="mascara(this,'/',patron,true);" value="<?=Fecha($promotorFechaNac) ?>" <?=$r?>/><?=link_calendario('fechanacPromotor');?> 
                                <td>
                                    <select id="localidadPromotor" onchange="cargarDatosLocalidad(this);" name="localidadPromotor">
                                        <option value="NOTOSH">Sin especificar</option>   
                                        <?php
                                        while(!$localidades_result->EOF){
                                            if($localidades_result->fields['localidad'] == $promotorLocalidad){
                                                $dataOption = 'value="'.$localidades_result->fields['idloc_provincial'].'" selected="selected"';
                                                $localidad_areaProg = $localidades_result->fields['ap'];
                                            }                         
                                            else{
                                                $dataOption = 'value="'.$localidades_result->fields['idloc_provincial'].'"';
                                            }
                                            $showValue = $localidades_result->fields['localidad'] ." - ".$localidades_result->fields['cp'];
                                        ?>
                                        <option <?=$dataOption?>><?=$showValue?></option>
                                        <?php 
                                            $localidades_result->MoveNext();
                                        }?>
                                    </select>
                                </td>
                                
                                <td id="promotorAreaProg"><?=$localidad_areaProg ?></td>
                                
			</tr>
		</table>
	</fieldset>
    
    <fieldset>
        <legend><b>Datos del efector</b></legend>
		<p></p>
                <table>
                <tr>			
                    <td id="mo">Efector</td>
                    <td id="mo">Area Programatica</td>

		</tr>
                <tr>
                   <td>
                    <select id="efectorPromotor" name="efectorPromotor" onchange="cargarDatosEfector(this);">
                        <option value="NOTOSH">Sin especificar</option>   
                        <?php
                        while(!$efectores_result->EOF){
                            if($efectores_result->fields['codremediar'] == $promotorEfector){
                                $dataOption = 'value="'.$efectores_result->fields['codremedair'].'" selected="selected"';
                                $efector_areaProg = $efectores_result->fields['ap'];
                            }                         
                            else{
                                $dataOption = 'value="'.$efectores_result->fields['codremediar'].'"';
                            }
                            $showValue = $efectores_result->fields['codremediar'] ." - ".$efectores_result->fields['efector'];
                        ?>
                        <option <?=$dataOption?>><?=$showValue?></option>
                        <?php 
                            $efectores_result->MoveNext();
                        }?>
                    </select>
                   </td>
                   <td id="efectorAreaProg"><?=$efector_areaProg?></td>
                
                </tr>
               
               </table>
    </fieldset>
    
    <fieldset>
        <legend><b>Contacto</b></legend>
        <p></p>
        <table>
            <tr>
                <td id="mo">Telefono</td>
                <td id="mo">Email</td>
            </tr>
            <tr>
                <td><input type="text" name="promotorTelefono" value=" <?=$promotorTelefono?>" /></td>
                <td><input type="text" name="promotorEmail" value=" <?=$promotorEmail?>" /></td>
            
            </tr>
        </table>
        
    </fieldset>
    
    <fieldset>
        <legend><b>Pagos</b></legend>
        <p></p>
        <table>
            <tr>
                <td id="mo">Banco</td>
                <td id="mo">Tipo de Cuenta</td>
                <td id="mo">N. de Cuenta</td>
            </tr>
            <tr>
                <td>
                    <select name="promotorBanco">
                        <option value="NOTOSH">Sin Asignar</option>
                        <?php
                        while(!$bancos_result->EOF){
                            if($bancos_result->fields['idbanco'] == $promotorBanco){
                                $dataOption = 'value="'.$bancos_result->fields['idbanco'].'" selected="selected"';
                            }                         
                            else{
                                $dataOption = 'value="'.$bancos_result->fields['idbanco'].'"';
                            }
                            $showValue = $bancos_result->fields['banco'];
                        ?>
                        <option <?=$dataOption?>><?=$showValue?></option>
                        <?php 
                            $bancos_result->MoveNext();
                        }?>
                        
                    </select></td>
                    
                    <td>
                    <select name="promotorTipoCta">
                        <option value="NOTOSH">Sin Asignar</option>
                        <?php
                        while(!$tiposCuentas_result->EOF){
                            if($tiposCuentas_result->fields['idtipocuenta'] == $promotorTipoCuenta){
                                $dataOption = 'value="'.$tiposCuentas_result->fields['idtipocuenta'].'" selected="selected"';
                            }                         
                            else{
                                $dataOption = 'value="'.$tiposCuentas_result->fields['idtipocuenta'].'"';
                            }
                            $showValue = $tiposCuentas_result->fields['tipocuenta'];
                        ?>
                        <option <?=$dataOption?>><?=$showValue?></option>
                        <?php 
                            $tiposCuentas_result->MoveNext();
                        }?>
                        
                    </select></td>
                    
                <td><input type="text" name="promotorNumCta" value=" <?=$promotorNroCuenta?>" /></td>
            
            </tr>
        </table>
        
    </fieldset>
    
    <p><input name="idPromotor" type="hidden" value="<?=$promotor_id?>" /></p>
    <p><input name="s_toperacion" type="hidden" value="<?=$s_toperacion?>" /></p>
    <input type="submit" name="persistir" value="Guardar" onClick="return validar_formulario()"/>
</form>

<?

echo fin_pagina(); // aca termino
?>

$_POST=Array
(
    [nombrePromotor] => ELENA ESTER
    [apellidoPromotor] => CHROPOT
    [dniPromotor] => 13366418
    [fechanacPromotor] => 1959-05-24
    [localidadPromotor] => 66
    [efectorPromotor] => 05167
    [promotorTelefono] => 034222
    [promotorEmail] => text@test.com
    [promotorBanco] => NOTOSH
    [promotorTipoCta] => NOTOSH
    [promotorNumCta] => 0000000
    [idPromotor] => 742
    [s_toperacion] => editar
    [persistir] => Guardar
)