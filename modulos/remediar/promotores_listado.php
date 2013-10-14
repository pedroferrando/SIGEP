<?php
#   Configuracion del sistema
require_once ("../../config.php");


#   Parametros
$filtro_tipo = $_POST['filtro_tipo'];
$filtro_factor = $_POST['filtro_factor'];

$sqlRetorno = $parametros['sqlRetorno'];
$mensajeExterno = $parametros['Mensaje'];

#   Listado de filtros
$filtro_gui_opciones = array(
    array("NOTOSH", "Sin Filtro"),
    array("DNI", "Documento"),
    array("Apellido", "Apellido"),
    array("Localidad", "Localidad"),
);


#   Procesado de datos

$filtro_where = "";
if($filtro_tipo != 'NOTOSH'){  
    switch ($filtro_tipo) {
        case "DNI":
            $filtro_where = "AND p.dni = ".$filtro_factor."";
            break;
        case "Apellido":
            $filtro_where = "AND UPPER(p.apellido) like UPPER(cast('%".$filtro_factor."%' as text))";
            break;
        case "Localidad":
            $filtro_where = "AND UPPER(loc.nombre) like UPPER(cast('%".$filtro_factor."%' as text))";
            break;
        
        default:    
            break;
   

    }
}

#   Promotores SQL
$promotoreSQL = "select p.idpromotor as idpromotor,p.nombre as nombre, p.apellido as apellido, p.fechanac as fechanac, 
                    loc.nombre as localidad, p.idlocalidad as idlocalidad, p.dni as dni    
                    from remediar.promotores p
                    left join uad.localidades loc on loc.idloc_provincial = p.idlocalidad
                    left join general.bancos bcos on p.idbanco = bcos.idbanco
                    --left join general.relacioncodigos rc on rc.codremediar = p.idefector
                    --left join facturacion.smiefectores ef on rc.cuie = ef.cuie 
                    where p.idpromotor is not null "
                    .$filtro_where.
                    " group by p.idpromotor,p.nombre, p.apellido, p.fechanac,loc.nombre, p.idlocalidad, p.dni";


#   Si se selecciono el filtro de busqueda

if ($_POST['Filtrar']) {
    
    #   Result del filtro
    $promotoresResult= sql($promotoreSQL, "en la obtencion de datos") or die();
}


#   Encabezado HTML por config del sistema.
echo $html_header;

?>


<script type="text/javascript">

//  Validaciones para el contenido del formulario
function validar_formulario(){
    
    FiltroFactor =  document.consolaBusqueda.filtro_factor.value;
    FiltroTipo = document.consolaBusqueda.filtro_tipo.value;
    FiltroFactorGui = document.consolaBusqueda.filtro_factor;

  
        if (FiltroTipo != "NOTOSH"){

            if(FiltroFactor == ""){
                FiltroFactorGui.style.backgroundColor='#FFAEAE';
                FiltroFactorGui.focus();
                alert("Debe ingresar un factor de filtro");
                return(false);
            }
    }
    
    
}


</script>




<br />

<div align="center" id="mo"><h2>Promotores Remediar + Redes    </h2></div>

<div align="center" id="mo"><?=$mensajeExterno?></div>
<br />

<form action="promotores_listado.php" method="POST" name="consolaBusqueda">
    <fieldset>
        <legend>Consola de Busqueda</legend>
        <div align="center" width="50%">
            <table>
                <tr>
                    <td>Filtro: <input type="text" name="filtro_factor" id="" value="<?=$filtro_factor?>"/></td>
                    <td>Por: <select name="filtro_tipo" id="filtro_tipo">
                            
                            <?php
                            for ($i = 0; $i < count($filtro_gui_opciones); $i++) {
                                if ($filtro_tipo == $filtro_gui_opciones[$i][0]) {
                                    $filtro_gui_selected = 'selected = "selected"';
                                }
                                else {
                                    $filtro_gui_selected = "";
                                }
                                ?>
                                <option value="<?= $filtro_gui_opciones[$i][0] ?>" <?= $filtro_gui_selected ?>><?= $filtro_gui_opciones[$i][1] ?></option>
                                <?php
                            }
                            ?>


                        </select>
                    </td>
                    <td><input type="submit" value="Filtrar" name="Filtrar" onClick="return validar_formulario()" /></td>
                    <td width="10%"></td>
                    <?php
                    $nuevoRef = encode_link("promotores_admin.php",array("mensajeExterno"=>"Nuevo Promotor"));
                    $nuevoOnclick="location.href='$nuevoRef'";
                    
                    ?>
                    <td><input type="button" value="Nuevo Promotor" onClick="<?=$nuevoOnclick?>"/></td>
                </tr>
            </table>
        </div>
    </fieldset>

</form>

<br />

<div align="center" width="80%">
    <table width="90%" >
        <tr id="mo">
            <td>Nombre</td>
            <td>DNI</td>
            <td>Localidad</td>
            <td>Editar</td>
        </tr>
        
        <?php 
            if($_POST['Filtrar']){
                $trBgColor = "#C1DAD6";
                while(!$promotoresResult->EOF){
                    $editRef = encode_link("promotores_admin.php",array("idpromotor"=>$promotoresResult->fields['idpromotor'],
                                            "sqlRetorno"=>$promotoreSQL, "mensajeExterno"=>"Editar Promotor"));
                    $editOnclick="location.href='$editRef'";
        ?>
        
            <tr bgcolor="<?=$trBgColor?>">
                <td><?=($promotoresResult->fields['nombre'].", ".$promotoresResult->fields['apellido'])?></td>
                <td><?=$promotoresResult->fields['dni']?></td>
                <td><?=$promotoresResult->fields['localidad']?></td>
                <td align="center"><input type="button" value="Editar" onClick="<?=$editOnclick?>"/></td>
            </tr>
        
        <?php 
              $promotoresResult->MoveNext();  
              
              # Cambia el color de las celdas
              if ($trBgColor == "#F5FAFA") {
                 $trBgColor = "#C1DAD6";
              }else{  
                 $trBgColor = "#F5FAFA";   
              }
              
              }
        } ?>
    </table>
</div>

<br />

<div align="center" id="mo"><h2>Promotores del programa Nacional Remediar + Redes</h2></div>   

