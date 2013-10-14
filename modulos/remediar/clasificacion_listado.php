<?php
require_once ("../../config.php");
require_once ("./remediar_seguimiento_funciones.php");

$filtro_factor = $_POST["filtroFactor"];
$filtro_tipo = $_POST["filtroTipo"];
$filtro_finicio = $_POST["filtroFechaInicio"];
$filtro_ffin = $_POST["filtroFechaFin"];


$filtro_gui_opciones = array(
    array("NOTOSH","Sin Filtro"),
    array("DNI","Documento"),
    array("Apellido","Apellido"),
    array("Efector","Cod. Efector"),
    array("Enviado","Enviados"),
    array("NoEnviado","No Enviados")
    );


$filtro_where = "";
if($filtro_tipo != 'NOTOSH'){  
    switch ($filtro_tipo) {
        case "DNI":
            $filtro_where = "where b.numero_doc = '".$filtro_factor."'";
            break;
        case "Apellido":
            $filtro_where = "where UPPER(b.apellido_benef) like UPPER(cast('%".$filtro_factor."%' as text))";
            break;
        case "Efector":
            $filtro_where = "where cast(rl.codremediar as text) = '".$filtro_factor."'";    
            break;
        
        case "Enviado":
            $filtro_where = "where UPPER(cl.enviado) = 'S'";    
            break;

        case "NoEnviado":
            $filtro_where = "where UPPER(cl.enviado) = 'N'";    
            break;
        
        default:    
            break;
   

    }
}


$beneficiarios_sql = "(select cl.clave_beneficiario, b.apellido_benef as apellido, b.nombre_benef as nombre, b.tipo_documento, b.numero_doc, b.sexo as sexo,
        cl.fecha_nac, b.localidad, b.departamento, b.municipio, b.calle, b.barrio, b.manzana, b.numero_calle, rl.codremediar as efector, UPPER(cl.rcvg), UPPER(cl.enviado) as enviado,
        sme.nombreefector as nombreefector, ap.nombre as ap, UPPER(cl.rcvg) as riesgo, 
        cl.dmt, cl.ta_sist, cl.ta_diast, cl.col_tot, cl.nro_clasificacion, cl.id_medico,med.apellido_medico, med.nombre_medico,cl.dmt2,
        cl.hta, cl.fecha_control, cl.fecha_envio, b.id_beneficiarios as idbeneficiario,
        cl.acv,cl.vas_per,cl.car_isq,cl.col310,cl.col_ldl,cl.ct_hdl,cl.pres_art,cl.dmt2,cl.insu_renal,cl.dmt_menor,
        cl.hta_menor,cl.menopausia,cl.antihiper,cl.obesi,cl.acv_prema,cl.trigli,cl.hdl_col,cl.hiperglu,cl.microalbu,cl.tabaquismo

        
        from trazadoras.clasificacion_remediar2 cl
         
        inner join uad.beneficiarios b on cl.clave_beneficiario = b.clave_beneficiario
        inner join general.relacioncodigos rl on cl.cuie = rl.cuie
        inner join facturacion.smiefectores sme on rl.cuie = sme.cuie
        inner join uad.localidades loc on sme.ciudad = loc.nombre
        inner join general.areas_programaticas ap on loc.id_areaprogramatica = ap.id_area_programatica
        left join planillas.medicos med on cl.id_medico = med.id_medico
        ".$filtro_where."
        )
        union 
        
        
        (select cl.clave, b.apellido_benef as apellido, b.nombre_benef as nombre, b.tipo_documento, b.numero_doc, b.sexo as sexo,
        cl.fecha_nac, b.localidad, b.departamento, b.municipio, b.calle, b.barrio, b.manzana, b.numero_calle, rl.codremediar as efector, UPPER(cl.rcvg), UPPER(cl.enviado) as enviado,
        sme.nombreefector as nombreefector,  ap.nombre as ap, UPPER(cl.rcvg) as riesgo,
    cl.dbt, cl.ta_sist, cl.ta_diast, cl.col_tot, cl.nro_clasificacion, cl.id_medico, med.apellido_medico, med.nombre_medico,  cl.dbt,
        cl.hta, cl.fecha_control, cl.fecha_envio, b.id_beneficiarios as idbeneficiario,
        
        '' as acv,'' as vas_per,'' as car_isq,'' as col310,'' as col_ldl,'' as ct_hdl,'' as pres_art,
        '' as dmt2,'' as insu_renal,'' as dmt_menor,'' as hta_menor,'' as menopausia,'' as antihiper,
        '' as obesi,'' as acv_prema,'' as trigli,'' as hdl_col,'' as hiperglu,'' as microalbu,'' as tabaquismo
        
        from trazadoras.clasificacion_remediar cl 
        inner join uad.beneficiarios b on cl.clave = b.clave_beneficiario
        inner join general.relacioncodigos rl on cl.cuie = rl.cuie
        inner join facturacion.smiefectores sme on rl.cuie = sme.cuie
        inner join uad.localidades loc on sme.ciudad = loc.nombre
        inner join general.areas_programaticas ap on loc.id_areaprogramatica = ap.id_area_programatica
        left join planillas.medicos med on cl.id_medico = med.id_medico
        ".$filtro_where."
        )

        order by apellido";

$beneficiarios_result = sql($beneficiarios_sql) or die() ;


$link = encode_link("clasificacion_listado_excel.php", array("sql" => $beneficiarios_sql, "filtro_facttor"=>$filtro_factor, "filtro_tipo" => $filtro_tipo));



echo $html_header;

?>

<script type="text/javascript">

function validar_formulario(){
    
    FiltroFactor =  document.consolaBusqueda.filtroFactor.value;
    FiltroTipo = document.consolaBusqueda.filtroTipo.value;
    FiltroFactorGui = document.consolaBusqueda.filtroFactor;
    
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
    <div align="center" id="mo"><h2>Beneficiarios Clasificados - Programa Nacional Remediar + Redes</h2></div>
    <?=$parametros["mensaje"]?>
    <form action="clasificacion_listado.php" method="post" name="consolaBusqueda">
        <fieldset>
            <legend><h3>Consola de b&uacute;squeda</h3></legend>      
        
        <table>
            <tr>
                <td>Filtro:</td>
                <td><input type="text" name="filtroFactor" id="" value="<?=$filtro_factor?>"/></td>
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
                <td>&nbsp;</td>
             
                
                <td>&nbsp;</td>
                <td><input type="submit" value="Buscar" onClick="return validar_formulario()"/></td>
                <td width="10%"></td>
                <td><button onclick="window.open('<?=$link ?>')"><img src="../../imagenes/excel.gif" alt="" /></button></td>
                
                
            </tr>
           
            
        </table>
            
        </fieldset>
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
        <td>Enviado</td>
        <td>Formulario</td>
    </tr>
    <?php
    $trBgColor = "#C1DAD6";
    $beneficiarios_cantidad = 0;
    while(!$beneficiarios_result->EOF){
        #   El argumento [pagina] => "listado_beneficiarios_leche.php", se usa para parchar un error que se cometio cuando se creo el formulario remediar_carga
        #   para salvar los tiempos de desarrollo.
        $data = encode_link("../trazadoras/remediar_carga.php", array("id_smiafiliados" => $beneficiarios_result->fields['idbeneficiario'], "pagina" => "listado_beneficiarios_leche.php", "clave_beneficiario"=>$beneficiarios_result->fields['clave_beneficiario']));
        $accion = "window.open('".$data."')";
        
        
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
        <td><?=$beneficiarios_result->fields['efector']?></td>
        <td id="celda"><?=$beneficiarios_result->fields['enviado']?></td>
        <td><button onclick="<?=$accion?>">F>></button></td>
    </tr>
        <?php $beneficiarios_result->MoveNext();
        if ($trBgColor == "#F5FAFA") {
         $trBgColor = "#C1DAD6";
        }else{  
         $trBgColor = "#F5FAFA";   
        }
        $beneficiarios_cantidad ++;
    } ?>
    
    
</table>
</div>

<div id="mo">&nbsp;</div>
<?php echo fin_pagina(); ?>