<?php
require_once ("../../config.php");
require_once ("./remediar_seguimiento_funciones.php");









$filtro_factor = $_POST["filtroFactor"];
$filtro_tipo = $_POST["filtroTipo"];
$filtro_finicio = $_POST["filtroFechaInicio"];
$filtro_ffin = $_POST["filtroFechaFin"];
$filtroNoClasificado = $_POST['noClasificado'];
$fechaEmpadronamientoDesde = fecha_db($_POST['fechaEmpadronamientoDesde']);
$fechaEmpadronamientoHasta = fecha_db($_POST['fechaEmpadronamientoHasta']);


$filtro_gui_opciones = array(
    array("NOTOSH","Sin Filtro"),
    array("DNI","Documento"),
    array("Apellido","Apellido"),
    array("Efector","Cod. Efector"),
    array("Enviado","Enviados"),
    array("NoEnviado","No Enviados"),
    array("RiesgoIgual","Riesgo Igual"),
    array("RiesgoMenor","Riesgo Menor"),
    array("RiesgoMayor","Riesgo Mayor"),
    array("RiesgoMayorIgual","Riesgo Mayor o Igual"),
    array("RiesgoMenorIgual","Riesgo Menor o Igual")
    );


$filtro_where = "";
if($filtro_tipo != 'NOTOSH'){  
    switch ($filtro_tipo) {
        case "DNI":
            $filtro_where = "AND b.numero_doc = '".$filtro_factor."'";
            break;
        case "Apellido":
            $filtro_where = "AND UPPER(b.apellido_benef) like UPPER(cast('%".$filtro_factor."%' as text))";
            break;
        case "Efector":
            $filtro_where = "AND cast(rl.codremediar as text) = '".$filtro_factor."'";    
            break;
        
        case "Enviado":
            $filtro_where = "AND UPPER(cl.enviado) = 'S'";    
            break;

        case "NoEnviado":
            $filtro_where = "AND UPPER(cl.enviado) = 'N'";    
            break;
        
        case "RiesgoIgual":
            $filtro_where = "AND fo.puntaje_final = CAST(".$filtro_factor." AS INTEGER)";    
            break;
        
        case "RiesgoMenor":
            $filtro_where = "AND fo.puntaje_final < CAST(".$filtro_factor." AS INTEGER)";    
            break;
        
        case "RiesgoMayor":
            $filtro_where = "AND fo.puntaje_final > CAST(".$filtro_factor." AS INTEGER)";    
            break;
        
        case "RiesgoMayorIgual":
            $filtro_where = "AND fo.puntaje_final >= CAST(".$filtro_factor." AS INTEGER)";    
            break;

        
        case "RiesgoMenorIgual":
            $filtro_where = "AND fo.puntaje_final <= CAST(".$filtro_factor." AS INTEGER)";    
            break;
        
        default:    
            break;
   

    }


    #   Filtro No clasificado
    if ($filtroNoClasificado == "noClasificado") {
        $filtro_where .= "AND cl.clavebeneficiario not in (select clas2.clave_beneficiario from trazadoras.clasificacion_remediar2 clas2 where clas2.clave_beneficiario = cl.clavebeneficiario )
                          AND cl.clavebeneficiario not in (select clas.clave from trazadoras.clasificacion_remediar clas where clas.clave = cl.clavebeneficiario )";
        $filtroPropiedad = " checked";
    }
}


$beneficiarios_sql = "select cl.clavebeneficiario, b.apellido_benef as apellido, b.nombre_benef as nombre, b.tipo_documento, b.numero_doc, b.sexo as sexo,
        b.fecha_nacimiento_benef as fecha_nac, b.localidad, b.departamento, b.municipio, b.calle, b.barrio, b.manzana, b.numero_calle, rl.codremediar as efector,  UPPER(cl.enviado) as enviado,
        sme.nombreefector as nombreefector, ap.nombre as ap, fo.puntaje_final as riesgo, fo.apellidoagente , fo.nombreagente, cl.fechaempadronamiento as fecha_inscripcion
        from uad.remediar_x_beneficiario cl 
        inner join remediar.formulario fo on fo.nroformulario = cl.nroformulario
        inner join uad.beneficiarios b on cl.clavebeneficiario = b.clave_beneficiario
        left join general.relacioncodigos rl on b.cuie_ah = rl.cuie
        left join facturacion.smiefectores sme on rl.cuie = sme.cuie
	left join uad.localidades loc on sme.ciudad = loc.nombre
	left join general.areas_programaticas ap on loc.id_areaprogramatica = ap.id_area_programatica
        WHERE cl.fechaempadronamiento BETWEEN '".$fechaEmpadronamientoDesde."' AND '".$fechaEmpadronamientoHasta."'
        ".$filtro_where."
        order by apellido
        ";



if($_POST['busqueda']){
    $beneficiarios_result = sql($beneficiarios_sql) or die() ;
}

$link = encode_link("inscripcion_listado_excel.php", array("sql" => $beneficiarios_sql, "filtro_facttor"=>$filtro_factor,
    "filtro_tipo" => $filtro_tipo, "fechaDesde" => $fechaEmpadronamientoDesde, "fechaHasta" => $fechaEmpadronamientoHasta, 
    "noClasificado" => $filtroNoClasificado));



echo $html_header;
?>

<!-- Importacion de librerias Jquery, Basicas y graficas -->
<script src='../../lib/jquery.min.js' type='text/javascript'></script>
<script src='../../lib/jquery/jquery-ui.js' type='text/javascript'></script>
<script src='../../lib/jquery/ui/jquery.ui.datepicker-es.js' type='text/javascript'></script>
<link rel="stylesheet" href="../../lib/jquery/ui/jquery-ui.css" />
<!--  -->



<script type="text/javascript">
function esFechaValida(fecha){
    if (fecha != undefined && fecha.value != "" ){
        if (!/^\d{2}\/\d{2}\/\d{4}$/.test(fecha.value)){
            alert("formato de fecha no valido (dd/mm/aaaa)");
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
    
    FiltroFactor =  document.consolaBusqueda.filtroFactor.value;
    FiltroTipo = document.consolaBusqueda.filtroTipo.value;
    FiltroFactorGui = document.consolaBusqueda.filtroFactor;
    FiltroFechaEmpadronamientoDesde = document.consolaBusqueda.fechaEmpadronamientoDesde;
    FiltroFechaEmpadronamientoHasta = document.consolaBusqueda.fechaEmpadronamientoHasta;
    
        if (FiltroFechaEmpadronamientoDesde.value == "") {
            alert("Debe ingresar ambos valores, el campo Fecha Empadronamiento \n\
ayuda a seleccionar un grupo especifico de beneficiarios.");
            return(false)
        }
        
        if (FiltroFechaEmpadronamientoHasta.value == "") {
            alert("Debe ingresar ambos valores, el campo Fecha Empadronamiento \n\
ayuda a seleccionar un grupo especifico de beneficiarios.");
            return(false)
        }
        
        if (FiltroTipo != "NOTOSH"){
            if(FiltroTipo == "Enviado"){
                return(true);
        }
        
        if(FiltroTipo == "NoEnviado"){
            return(true);
        }
        
        
        
        if(FiltroFactor == ""){
            FiltroFactorGui.style.backgroundColor='#FFAEAE';
            FiltroFactorGui.focus();
            alert("Debe ingresar un factor de filtro");
            return(false);
        }
    }   
}
</script>

<div align="center">
    <div align="center" id="mo"><h2>Beneficiarios Inscriptos - Programa Nacional Remediar + Redes</h2></div>
    <?=$parametros["mensaje"]?>
    <form action="inscripcion_listado.php" method="post" name="consolaBusqueda">
        <fieldset>
            <legend><h3>Consola de b&uacute;squeda</h3></legend>      
        
        <table>
            <tr>
                <td><b>Filtro:</b></td>
                <td><input type="text" name="filtroFactor" id="" value="<?=$filtro_factor?>"/></td>
                <td><b>Tipo:</b></td>
                <td><select name="filtroTipo" id="">
                    <?php
                        for ($i=0; $i<count($filtro_gui_opciones);$i++){
                            if ($filtro_tipo == $filtro_gui_opciones[$i][0]){
                                $filtro_gui_selected = 'selected = "selected"';}
                            else{
                                $filtro_gui_selected = "";
                                
                                }
                            ?>
                                <option value="<?=$filtro_gui_opciones[$i][0]?>" <?=$filtro_gui_selected?>><?=$filtro_gui_opciones[$i][1]?></option>
                            <?php
                        }
                    ?>
                </select>
                </td>
                
                
                <td><input type="submit" value="Buscar" onClick="return validar_formulario()"/></td>
                <td width="10%"></td>
                <td><button onclick="window.open('<?=$link ?>')"><img src="../../imagenes/excel.gif" alt="" /></button></td>
                
            </tr>
            <tr>
                <td><b>F.Empadronamiento: </b></td>
                <td><input type="text" name="fechaEmpadronamientoDesde" id="fechaEmpadronamientoDesde" value="<?=fecha($fechaEmpadronamientoDesde)?>" onblur="esFechaValida(this);" onchange="esFechaValida(this);" onKeyUp="mascara(this,'/',patron,true);"/> <?=$r?><?=link_calendario('fechaEmpadronamientoDesde');?></td>
                <td><b>hasta: </b></td>
                <td><input type="text" name="fechaEmpadronamientoHasta" id="fechaEmpadronamientoHasta" value="<?=fecha($fechaEmpadronamientoHasta)?>"onblur="esFechaValida(this);" onchange="esFechaValida(this);" onKeyUp="mascara(this,'/',patron,true);"/> <?=$r?><?=link_calendario('fechaEmpadronamientoHasta');?></td>
            </tr>
            <tr>
                <td><b>No clasificado</b></td>
                <td><input type="checkbox" name="noClasificado" id="" value="noClasificado" <?=$filtroPropiedad?>></td>
            </tr>
           
            
        </table>
            
        </fieldset>
        <input type="hidden" name="busqueda" value="busqueda" />
    </form>
</div>


<br />
<div align="center">

<table width="100%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center">
    <tr id="mo">
        <td>Apellido</td>
        <td>Nombre</td>
        <td>Edad</td>
        <td>DNI</td>
        <td>Fecha Nacimiento</td>
        <td>Departamento</td>
        <td>Municipio</td>
        <td>Riesgo</td>
        <td>Efector</td>
        <td>Codremediar</td>
        <td>Inscripto</td>
        <td>Enviado</td>
    </tr>
    <?php
    if($_POST['busqueda']){
        $trBgColor = "#C1DAD6";
        $beneficiarios_cantidad = 0;
        while(!$beneficiarios_result->EOF){
            $data = encode_link("remediar_seguimiento.php", array("cbeneficiario" => $beneficiarios_result->fields['clave_beneficiario']));
            $accion = "location.href='".$data."'";

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
            <td><?=$beneficiarios_result->fields['efector']?></td>
            <td><?=$beneficiarios_result->fields['fecha_inscripcion']?></td>
            <td id="celda"><?=$beneficiarios_result->fields['enviado']?></td>
            
        </tr>
            <?php $beneficiarios_result->MoveNext();
            if ($trBgColor == "#F5FAFA") {
             $trBgColor = "#C1DAD6";
            }else{  
             $trBgColor = "#F5FAFA";   
            }
            $beneficiarios_cantidad ++;
        } 
    }?>
    
    
</table>
    <br />
    <div align="center"><h2>Cantidad: <?=$beneficiarios_cantidad?></h2></div>
</div>

<div id="mo">&nbsp;</div>
<?php echo fin_pagina(); ?>