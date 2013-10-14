<?php
require_once ("../../config.php");

#   Archivo que contiene las classes de BeneficiarioSeguimiento
require_once("./seguimiento_listado_funciones.php");

$filtro_factor = $_POST["filtroFactor"];
$filtro_tipo = $_POST["filtroTipo"];
$filtro_finicio = $_POST["filtroFechaInicio"];
$filtro_ffin = $_POST["filtroFechaFin"];
$fechaEmpadronamientoDesde = fecha_db($_POST['fechaEmpadronamientoDesde']);
$fechaEmpadronamientoHasta = fecha_db($_POST['fechaEmpadronamientoHasta']);


$filtro_gui_opciones = array(
    array("NOTOSH","Sin Filtro"),
    array("DNI","Documento"),
    array("Apellido","Apellido"),
    array("Efector","Cod. Efector"),
    array("Enviado","Enviados"),
    array("NoEnviado","No Enviados"),
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
            $filtro_where = "AND s.estado_envio = 1";    
            break;

        case "NoEnviado":
            $filtro_where = "AND s.estado_envio = 0";    
            break;
               
        default:    
            break;
   

    }
}



$beneficiarios_sql = "select s.clavebeneficiario
        from trazadoras.seguimiento_remediar s 
        inner join uad.beneficiarios b on s.clavebeneficiario = b.clave_beneficiario
        left join general.relacioncodigos rl on b.cuie_ah = rl.cuie
        left join facturacion.smiefectores sme on rl.cuie = sme.cuie
	left join uad.localidades loc on sme.ciudad = loc.nombre
	left join general.areas_programaticas ap on loc.id_areaprogramatica = ap.id_area_programatica
        WHERE s.fecha_seguimiento BETWEEN '".$fechaEmpadronamientoDesde."' AND '".$fechaEmpadronamientoHasta."'
        ".$filtro_where."
        group by s.clavebeneficiario, b.apellido_benef
        order by b.apellido_benef
        ";

if($_POST['busqueda']){
    $beneficiarios_result = sql($beneficiarios_sql) or die() ;
    #echo $beneficiarios_sql;
}

$link = encode_link("inscripcion_listado_excel.php", array("sql" => $beneficiarios_sql, "filtro_facttor"=>$filtro_factor,
    "filtro_tipo" => $filtro_tipo, "fechaDesde" => $fechaEmpadronamientoDesde, "fechaHasta" => $fechaEmpadronamientoHasta));



echo $html_header;

?>

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
    <div align="center" id="mo"><h2>Beneficiarios Seguidos - Programa Nacional Remediar + Redes</h2></div>
    <?=$parametros["mensaje"]?>
    <form action="seguimiento_listado_global.php" method="post" name="consolaBusqueda">
        <fieldset>
            <legend><h3>Consola de b&uacute;squeda</h3></legend>      
        
        <table>
            <tr>
                <td>Filtro:</td>
                <td><input type="text" name="filtroFactor" id="" value="<?=$filtro_factor?>"/></td>
                <td>Tipo:</td>
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
                <td>F.Empadronamiento: </td>
                <td><input type="text" name="fechaEmpadronamientoDesde" id="fechaEmpadronamientoDesde" value="<?=fecha($fechaEmpadronamientoDesde)?>" onblur="esFechaValida(this);" onchange="esFechaValida(this);" onKeyUp="mascara(this,'/',patron,true);"/> <?=$r?><?=link_calendario('fechaEmpadronamientoDesde');?></td>
                <td>hasta: </td>
                <td><input type="text" name="fechaEmpadronamientoHasta" id="fechaEmpadronamientoHasta" value="<?=fecha($fechaEmpadronamientoHasta)?>"onblur="esFechaValida(this);" onchange="esFechaValida(this);" onKeyUp="mascara(this,'/',patron,true);"/> <?=$r?><?=link_calendario('fechaEmpadronamientoHasta');?></td>
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
        <td>1er Seguimiento</td>
        <td>Detalle</td>
    </tr>
    <?php
    if($_POST['busqueda']){
        $trBgColor = "#C1DAD6";
        $beneficiarios_cantidad = 0;
        while(!$beneficiarios_result->EOF){
            $data = encode_link("inscripcion_excluir.php", array("cbeneficiario" => $beneficiarios_result->fields['clavebeneficiario']));
            $accion = "location.href='".$data."'";
            $disponible = "";
                    
            #if ($beneficiarios_result->fields['enviado'] == "S") {
             #   $disponible = "disabled";
            #}


            #   Clave del beneficiario recibido como parámetro
            $beneficiario_clave = $beneficiarios_result->fields['clavebeneficiario'];

            #   Objeto Beneficiario
            $beneficiario = new BeneficiarioSeguimiento($beneficiario_clave);

            #   Result Beneficiario desde UAD.beneficiarios
            $beneficiario_result_seg = sql($beneficiario->sqlObtenerBeneficiario());
            
            #   Contruye al beneficiario con los datos afiliatorios
            $beneficiario->construirBeneficiario($beneficiario_result_seg);

            #   Result Beneficiario desde trazadoras.seguimiento_remediar  
            $beneficiario_result_seg = sql($beneficiario->sqlObtenerSeguimiento());
            
            $beneficiario->construirSeguimiento($beneficiario_result_seg);
            #   Result Beneficiario desde trazadoras.clasificacion_remediar y
            #   trazadoras.clasificacion_remediar2
            $beneficiario_clasificacion_result = sql($beneficiario->sqlObtenerClasificacion());

            #   Contruye al beneficiario con los datos afiliatorios
            $beneficiario->construirClasificacion($beneficiario_clasificacion_result);

            $data = encode_link("../remediar/seguimiento_listado_detalles.php", array("claveBeneficiario" => $beneficiario->getClaveBeneficiario()));
            $accion = "window.open('".$data."')";

            ?>
    
        <tr bgcolor="<?=$trBgColor?>">
             
            <td id="celda" ><?=$beneficiario->getApellido()?></td>
            <td id="celda"><?=$beneficiario->getNombre()?></td>
            <td id="celda"><?=$beneficiario->getEdad()?></td>
            <td id="celda"><?=$beneficiario->getNroDoc()?></td>
            <td id="celda"><?=$beneficiario->getFechaNacimiento()?></td>
            <td id="celda"><?=$beneficiario->getDomicilioDepartamento()?></td>
            <td id="celda"><?=$beneficiario->getDomicilioMunicipio()?></td>
            <td><?=$beneficiario->getFechaSeguimiento()?></td>

            <td><input type="button" value="D>>" onclick="<?=$accion?>" <?=$disponible?>/></td>
            
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