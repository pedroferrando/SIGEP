<?php
require_once ("../../config.php");

if ($_POST['guardar'] == 'TRUE') {

    $pracorigen = $_POST['pracor'];
    $pracrel = $_POST['relacionada'];
    $dias = $_POST['rango'];
    $anexo = $_POST['anexo'];
    if ($_POST['obligado'] != "-1") {
        if ($_POST['obligado'] == 'SI') {
            $tipo = "AND";
        } else {
            $tipo = "OR";
        }
        $otras = "S";
    } else {
        $otras = "N";
        $tipo = "";
    }
    $db->StartTrans();
    $consulta = "INSERT INTO facturacion.cfg_practicas_relac
      (modo,pracorigen,pracrel,dias,otras,tipo,anexopracrel)
      VALUES ('1','$pracorigen','$pracrel','$dias','$otras','$tipo','$anexo')";
    sql($consulta) or fin_pagina();
    $db->CompleteTrans();
}

if ($_POST['borrar'] == 'TRUE') {

    $pracorigen = $_POST['pracor'];
    $pracrel = $_POST['relacionada'];
    $db->StartTrans();
    $consulta = "DELETE FROM facturacion.cfg_practicas_relac
                    WHERE pracorigen='$pracorigen'
                    AND pracrel='$pracrel'";
    sql($consulta) or fin_pagina();
    $db->CompleteTrans();
}

$sqltodo = "SELECT DISTINCT ON (n.codigo) codigo ,n.descripcion,n.id_nomenclador id,to_char(n.precio,'99999D99') precio
                FROM facturacion.nomenclador n
                ORDER BY n.codigo";
$todoslosnomencladores = sql($sqltodo) or fin_pagina();

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
echo $html_header;

echo "<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js' type='text/javascript'></script>";
?>
<script>
    var img_ext='<?= $img_ext = '../../imagenes/rigth2.gif' ?>';//imagen extendido
    var img_cont='<?= $img_cont = '../../imagenes/down2.gif' ?>';//imagen contraido 
    var img_mas='<?= $img_mas = '../../imagenes/mas.gif' ?>';//imagen contraido
    var img_guardar='<?= $img_guardar = '../../imagenes/menu/iconSave.gif' ?>';//imagen contraido
    
    function muestra_tabla(obj_tabla,img){
        //var oimg=eval("document.all.imagen_2");//objeto tipo IMG
        if (obj_tabla.style.display=='none'){
            obj_tabla.style.display='inline';
            img.show=0;
            img.src=img_cont;
        }
        else{
            obj_tabla.style.display='none';
            img.show=1;
            img.src=img_ext;
        }
    }
    
    function muestra_tabla_new(obj_tabla){
        //var oimg=eval("document.all.imagen_2");//objeto tipo IMG
        if (obj_tabla.style.display=='none'){
            obj_tabla.style.display='table-row';
        }
        else{
            obj_tabla.style.display='none';
        }
    }
    
    function quitarrelacion(nomenclador,aux){
        //practica relacionada como regla
        var relacionada=$("#existe"+nomenclador+aux).val();
        //practica original a la que se agrega una regla
        var pracor=$("#pracor_"+nomenclador).val();
        
        $.post("relacionadas_admin.php", { borrar:'TRUE', relacionada: relacionada, pracor: pracor},function(data){ 
            location.reload();
            //var tablaagregada = $( data ).find( "#prueba_vida_"+nomenclador );
            //$( "#prueba_vida_"+nomenclador ).attr("display", none);
            //$("td#cabezal_"+nomenclador).attr("display", none);
        });
    }
    
    function guardar_relacion(aux){
        var relacionada=$("select#relacionada_"+aux).val();        
        if(relacionada=="-1"){
            alert('Debe seleccionar una Practica para la nueva Regla');
            return false;
        }
        var rango=$("input#rango_"+aux).val();
        if(rango==""){
            alert('Debe ingresar un Rango en dias para la Practica Relacionada');
            return false;
        }
        var obligado=$("select#obligado_"+aux).val();
        var pracor=$("#pracor_"+aux).val();
        var anexo =$("#anexo_"+aux).val();
        //var obj=document.getElementById('form_new_'+aux);
        
        $.post("relacionadas_admin.php", { guardar:'TRUE', relacionada: relacionada, anexo:anexo, rango: rango, obligado: obligado , pracor: pracor},function(data){
            location.reload();
        } );
    }
    
    function guardarnuevaregla(){
        var relacionada=$("select#nueva_regla_relacionada").val();        
        if(relacionada=="-1"){
            alert('Debe seleccionar una Practica para la nueva Regla');
            return false;
        }
        var rango=$("input#rango_nueva_regla").val();
        if(rango==""){
            alert('Debe ingresar un Rango en dias para la Practica Relacionada');
            return false;
        }
        var obligado=$("select#obligado_nueva_regla").val();
        var pracor=$("select#regla_nueva").val();
        var anexo =$("#anexo_nueva_regla").val();
        //var obj=document.getElementById('form_new_'+aux);
        
        $.post("relacionadas_admin.php", { guardar:'TRUE', relacionada: relacionada,anexo:anexo, rango: rango, obligado: obligado , pracor: pracor},function(data){
            location.reload();
        } );
    }
    
    $(document).ready(function () {
        $("select#regla_nueva").on('change',function(){
            var modo=$("select#regla_nueva").val();
            if(modo!="-1"){
                $("#regla_new").css("display","table-row");
            }else{
                $("#regla_new").css("display","none");
            }
        }
    );
    });                
    
    
    // $("select#facturacionselect").on('change',function(){}
    
</script>

<table width="60%" class="bordes" align="center" style="padding-top: 20px">
    <tr align="center" id="mo">
        <td align="center" style="padding-bottom: 10px; font-size: large">
            <b>Reglas para Practicas con dependencias</b>
        </td>
        <?
        $sqltodo = "SELECT distinct(pracorigen)
                    FROM facturacion.cfg_practicas_relac
                    ORDER BY pracorigen";
        $todaslaspracticasconregla = sql($sqltodo) or fin_pagina();
        if ($todaslaspracticasconregla->RecordCount() != 0) {
            $aux = 0;
            while (!$todaslaspracticasconregla->EOF) {
                $nomencladorderegla = str_replace(" ", "", $todaslaspracticasconregla->fields['pracorigen']);
                ?>

            <tr align="center" id="mo">
                <td id="cabezal_<?= $nomencladorderegla ?>" align="center" width="3%">
                    <img id="imagen_1<?= $nomencladorderegla ?>" src="<?= $img_ext ?>" border=0 title="Mostrar Comprobantes" align="left"  style="cursor:pointer;" 
                         onclick="muestra_tabla(document.all.prueba_vida_<?= $nomencladorderegla ?>,imagen_1<?= $nomencladorderegla ?>);">
                    <label> <?= $todaslaspracticasconregla->fields['pracorigen'] ?></label>
                    <input value="<?= $todaslaspracticasconregla->fields['pracorigen'] ?>" hidden="hidden" type="textbox" id="pracor_<?= $nomencladorderegla ?>"></input>
                    <img id="imagen_2<?= $nomencladorderegla ?>" src="<?= $img_mas ?>" border=0 title="Agregar Relacion" align="right" style="cursor:pointer;" onclick="muestra_tabla_new(document.all.sub_tabla_new_<?= $nomencladorderegla ?>);" >
                </td>
            </tr>

            <tr align="center">
                <td>
                    <table class="tabla_regla" id="prueba_vida_<?= $nomencladorderegla ?>" border="1" width="100%" style="display:none;border:thin groove">
                        <tr id="sub_tabla">
                            <td width="40%">Practica Relacionada</td>
                            <td width="20%">Anexo</td>
                            <td width="20%">Rango en días</td>
                            <td width="20%">Obligatoria</td>                            
                            <td width="10%"></td>
                        </tr>

                        <?
                        $sqlrelacion = "SELECT *
                                            FROM facturacion.cfg_practicas_relac
                                            WHERE pracorigen='" . $todaslaspracticasconregla->fields['pracorigen'] . "'";
                        $practicasrelacionadas = sql($sqlrelacion) or fin_pagina();
                        /*
                         * Aqui comienza con cada uno de los nomencladores que tienen regla
                         */
                        while (!$practicasrelacionadas->EOF) {
                            $codigorelacionada = $practicasrelacionadas->fields['pracrel'];
                            $codigorelacionadatrim = str_replace(" ", "", $practicasrelacionadas->fields['pracrel']);
                            ?>
                            <form name='form_regla' action='relacionadas_admin.php' method="post" enctype='multipart/form-data' id="form_regla" >
                                <tr id="sub_tabla<?= $nomencladorderegla . $codigorelacionadatrim ?>" class="tabla_regla">
                                    <td width="40%">
                                        <select id="existe<?= $nomencladorderegla . $codigorelacionadatrim ?>" name="existe<?= $nomencladorderegla . $codigorelacionadatrim ?>"<? echo "DISABLED"; ?>>
                                            <option value="-1" selected="selected">
                                                Seleccione
                                            </option>
                                            <?
                                            $todoslosnomencladores->moveFirst();
                                            /*
                                             * Carga el combo con la practica relacionada a la regla
                                             */
                                            while (!$todoslosnomencladores->EOF) {
                                                $codigo = $todoslosnomencladores->fields['codigo'];
                                                ?>
                                                <option value="<?= $codigo ?>"
                                                <?
                                                if (trim($codigo) == trim($codigorelacionada)) {
                                                    echo "selected";
                                                }
                                                ?>>
                                                            <?= $codigo ?>
                                                </option>
                                                <?
                                                $todoslosnomencladores->movenext();
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td width="20%">
                                        <input <? echo "DISABLED" ?> type="textbox" style="width: 60px;" value="<?= $practicasrelacionadas->fields['anexopracrel'] ?>">
                                    </td>
                                    <td width="20%">
                                        <input <? echo "DISABLED" ?> type="textbox" value="<?= $practicasrelacionadas->fields['dias'] ?>">
                                    </td>
                                    <td width="20%">
                                        <select <? echo "DISABLED" ?>>
                                            <option value="NO" <?
                                $tipo = $practicasrelacionadas->fields['tipo'];
                                if ($tipo == 'OR') {
                                    echo "selected";
                                }
                                            ?>>
                                                NO
                                            </option>
                                            <option value="SI" <?
                                        if ($tipo == 'AND') {
                                            echo "selected";
                                        }
                                            ?>>
                                                SI
                                            </option>
                                        </select>
                                    </td>
                                    <td width="10%">
                                        <input size="5" type="button" value="X" onclick="return quitarrelacion(<?= "'" . $nomencladorderegla . "'" ?>,<?= "'" . $codigorelacionadatrim . "'" ?>)">
                                    </td>
                                </tr>
                            </form>
                            <?
                            $practicasrelacionadas->movenext();
                        }

                        /*
                         * formulario de agregar una nueva regla a una prestacion que ya poseia reglas. (en principio invisible)
                         */
                        ?>

                        <tr id="sub_tabla_new_<?= $nomencladorderegla ?>" style="display:none;border:thin groove">
                        <form name='form_new_<?= $nomencladorderegla ?>' action='relacionadas_admin.php' method="post" enctype='multipart/form-data' id="form_new_<?= $nomencladorderegla ?>" >
                            <td width="40%">
                                <select id="relacionada_<?= $nomencladorderegla ?>" name="relacionada_<?= $nomencladorderegla ?>">
                                    <?
                                    $sqltodo = "SELECT DISTINCT ON (n.codigo) codigo, n.descripcion,
                                                n.id_nomenclador id, to_char(n.precio,'99999D99') precio
                                                FROM facturacion.nomenclador n
                                                WHERE n.codigo NOT IN (SELECT distinct(pracrel)
                                                FROM facturacion.cfg_practicas_relac
                                                WHERE pracorigen='" . $todaslaspracticasconregla->fields['pracorigen'] . "')
                                                AND n.codigo!='" . $todaslaspracticasconregla->fields['pracorigen'] . "'
                                                ORDER BY n.codigo";
                                    $todaslaspracticas = sql($sqltodo) or fin_pagina();
                                    $todaslaspracticas->moveFirst();
                                    ?><option value="-1">
                                        Seleccione
                                    </option>
                                    <?
                                    /*
                                     * carga el combo con todas los nomencladores posibles para la nueva regla.
                                     */
                                    while (!$todaslaspracticas->EOF) {
                                        $codigo = $todaslaspracticas->fields['codigo'];
                                        ?>
                                        <option value="<?= $codigo ?>">
                                            <?= $codigo ?>
                                        </option>
                                        <?
                                        $todaslaspracticas->movenext();
                                    }
                                    ?>
                                </select>
                            </td>
                            <td width="20%">
                                <input id="anexo_<?= $nomencladorderegla ?>" name="anexo_<?= $nomencladorderegla ?>" type="textbox" style="width: 60px;" value="<?= $practicasrelacionadas->fields['anexo'] ?>">
                            </td>
                            <td width="20%">
                                <input id="rango_<?= $nomencladorderegla ?>" name="rango_<?= $nomencladorderegla ?>" type="textbox" value="" style="margin: 0 10px;">
                            </td>
                            <td width="20%" >
                                <select id="obligado_<?= $nomencladorderegla ?>" name="obligado_<?= $nomencladorderegla ?>" style="margin: 0 30px;">
                                    <option value="-1" selected="selected">
                                        Seleccione
                                    </option>
                                    <option value="NO">
                                        NO
                                    </option>
                                    <option value="SI">
                                        SI
                                    </option>
                                </select>
                            </td>
                            <td width="10%">
                                <img class="btn_guardar" name="guardar<?= $nomencladorderegla ?>" id="guardar<?= $nomencladorderegla ?>" src="<?= $img_guardar ?>" 
                                     border=0 title="Guardar Relacion" style="cursor:pointer; margin: 0 30px;" 
                                     onclick="return guardar_relacion(<?= "'" . $nomencladorderegla . "'" ?>)" >
                            </td>
                        </form>
            </tr>

        </table>
        </td>            
        </tr>
        <?
        $todaslaspracticasconregla->movenext();
        $aux++;
    }
}
?>
</tr>
<tr align="center" id="mo">
    <td align="center" width="3%">
        <?
        /*
         * para crear una nueva regla, carga el combo con todos nomencladores que no tienen ninguna regla aun.
         */
        $sqltodo = "SELECT DISTINCT ON (n.codigo) codigo, n.descripcion,
                n.id_nomenclador id, to_char(n.precio,'99999D99') precio
                FROM facturacion.nomenclador n
                WHERE n.codigo NOT IN (SELECT distinct(pracorigen)
                    FROM facturacion.cfg_practicas_relac
                    ORDER BY pracorigen)
                ORDER BY n.codigo";
        $todaslaspracticas = sql($sqltodo) or fin_pagina();
        ?>
        <select id="regla_nueva" style="width: 180px">
            <option value="-1">
                Ingrese una Regla Nueva
            </option>
            <?
            while (!$todaslaspracticas->EOF) {
                ?><option value="<?= $todaslaspracticas->fields['codigo'] ?>">
                    <?= $todaslaspracticas->fields['codigo'] ?>
                </option>
                <?
                $todaslaspracticas->movenext();
            }
            ?>
        </select>
    </td>
</tr>
<tr align="center">
    <td>
        <table id="regla_new" border="1" width="100%" style="display:none;border:thin groove">
            <tr id="sub_tabla">
                <td width="40%">Practica Relacionada</td>
                <td width="20%">Anexo</td>
                <td width="20%">Rango en días</td>
                <td width="20%">Obligatoria</td>
                <td width="10%"></td>
            </tr>
            <tr>
                <td width="40%">
                    <select id="nueva_regla_relacionada" name="reglarelacionada">
                        <?
                        /*
                         * carga el combo con todos los nomencladores que pueden relacionarse a la regla
                         */
                        $sqltodo = "SELECT DISTINCT ON (n.codigo) codigo, n.descripcion,
                        n.id_nomenclador id, to_char(n.precio,'99999D99') precio
                        FROM facturacion.nomenclador n
                        ORDER BY n.codigo";
                        $todaslaspracticas = sql($sqltodo) or fin_pagina();
                        $todaslaspracticas->moveFirst();
                        ?><option value="-1">
                            Seleccione
                        </option>
                        <?
                        while (!$todaslaspracticas->EOF) {
                            $codigo = $todaslaspracticas->fields['codigo'];
                            ?>
                            <option value="<?= $codigo ?>">
                                <?= $codigo ?>
                            </option>
                            <?
                            $todaslaspracticas->movenext();
                        }
                        ?>
                    </select>
                </td>
                <td width="20%">
                    <input id="anexo_nueva_regla" name="anexo_nueva_regla" type="textbox" value="" style="margin: 0 10px;width: 60px;">
                </td>
                <td width="20%">
                    <input id="rango_nueva_regla" name="rango_nueva_regla" type="textbox" value="" style="margin: 0 10px;">
                </td>
                <td width="20%" >
                    <select id="obligado_nueva_regla" name="obligado_nueva_regla" style="margin: 0 30px;">
                        <option value="-1" selected="selected">
                            Seleccione
                        </option>
                        <option value="NO">
                            NO
                        </option>
                        <option value="SI">
                            SI
                        </option>
                    </select>
                </td>
                <td width="10%">
                    <img class="btn_guardar" name="guardar_nueva_regla" id="guardar_nueva_regla" src="<?= $img_guardar ?>" border=0 title="Guardar Relacion" style="cursor:pointer; margin: 0 30px;" onclick="return guardarnuevaregla()" >
                </td>
            </tr>
        </table>

    </td>

</tr>
</table>

<?=
fin_pagina(); // aca termino ?>