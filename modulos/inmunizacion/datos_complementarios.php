<?php
require_once ("../../config.php");
require_once ("../../lib/bibliotecaTraeme.php");
require_once("clases/Vacuna.php");
require_once("clases/Efector.php");
require_once("clases/Presentacion.php");
require_once("clases/Condicion.php");
require_once("clases/GrupoRiesgo.php");
require_once("clases/Prestacion.php");



if ($_POST['Guardar'] == "Guardar") {
     $id_comprobante=$_POST['txtIdComprobante'];
     $id_nomenclador=$_POST['txtIdNomenclador'];
     $precio=$_POST['txtPrecio'];
    
     $caracteristicasSeleccionadas=$_POST['cmbCondicion'];
     
     $id_vacuna_dosis=$_POST['cmbDosis']; 
     
     $id_presentacion=$_POST['cmbPresentacion']; 
     $id_grupo_riesgo=$_POST['cmbGrupoRiesgo']; 
     $id_terreno=$_POST['cmbLugarVacunacion']; 
     $cuie=$_POST['txtEfector']; 
     $laboratorio=$_POST['txtLaboratorio'];
     $loteNumero=$_POST['txtLoteNumero'];
     $fechaVencimiento=$_POST['txtFechaVencimiento']; 
     $fecha_comprobante=$_POST['txtFechaAplicacion']; 
     $fecha_nacimiento=$_POST['txtFechaNac']; 
     $clavebeneficiario=$_POST['txtClaveBeneficiario']; 
     $id_usuario = $_ses_user['id'];
    
     if(!$fechaVencimiento){
         $fechaVencimiento='9999-01-01';
     }
     if(!$loteNumero){
         $loteNumero=0;
     }
     if(!$laboratorio){
         $laboratorio='';
     } 
    
    $db->StartTrans();
        $id_prestacion = guardarPrestacion($id_comprobante, $id_nomenclador, '1', $precio);
        $id_prestacion_inmu=Prestacion::getNextMaxId();
        $id_vacuna=Vacuna::getIdVacuna($id_vacuna_dosis);
        Prestacion::setPrestacionInmu($id_prestacion_inmu, $id_vacuna_dosis, $id_vacuna, $cuie, $clavebeneficiario, $id_terreno, $fecha_comprobante, $fecha_nacimiento, $fechaVencimiento, $loteNumero, $laboratorio, $id_prestacion, $id_comprobante,$id_presentacion,$id_grupo_riesgo,1,$id_usuario);
        if (!$caracteristicasSeleccionadas==NULL){
                $sql_insert='';

                for ($ii=0;$ii < count($caracteristicasSeleccionadas);$ii++){
                    $id_caracteristica=$caracteristicasSeleccionadas[$ii];
                    $sql_insert="insert into inmunizacion.prestaciones_caracteristicas(id_prestacion_inmu,id_caracteristica)
                    values ($id_prestacion_inmu,$caracteristicasSeleccionadas[$ii])";
                    $tras = sql($sql_insert, "Error al insertar caracteristica")or fin_pagina();
                }
         }
         
    $db->CompleteTrans();
?>
    <script>
      alert("Vacuna guardada!");  
      self.close();
    </script>
<?
}else{
    extract($_POST, EXTR_SKIP);
    if ($parametros)
    extract($parametros, EXTR_OVERWRITE);
    cargar_calendario();
    $codigo=$parametros['datos_practica']['codigo'];
    $id_comprobante=$parametros['id_comprobante'];
    $id_nomenclador=$parametros['datos_practica']['id_nomenclador'];
    $precio=$parametros['datos_practica']['precio'];
    $clave_beneficiario=$parametros['clave_beneficiario'];
    $fecha_nac=$parametros['fecha_nac'];
    
    //Cargando combo vacunas
    $codigo_cortado=substr($parametros[datos_practica][codigo],3,4);
    $vacunas=  Vacuna::getVacunasPorCodigo($codigo_cortado);
    $terrenos= Efector::getTerrenos($parametros[cuiel]);
    $presentaciones=  Presentacion::getPresentacion();
    $condiciones=  Condicion::getCondiciones();
    $grupos_riesgo= GrupoRiesgo::getGruposRiesgo();
}

echo $html_header;
echo "<script src='../../lib/jquery.min.js' type='text/javascript'></script>";
echo "<script src='../../lib/jquery/jquery-ui.js' type='text/javascript'></script>";
echo "<link rel='stylesheet' href='../../lib/jquery/ui/jquery-ui.css'/>";
echo "<script src='../../lib/jquery/ui/jquery.ui.datepicker-es.js' type='text/javascript'></script>";

?>

<link rel="stylesheet" type="text/css" href="datos_complementarios.css">

    <script>
        
        $("#txtFechaVencimiento").datepicker({
            minDate:'+0d'
        });
        
        self.moveTo(screen.width/2-250,screen.height/2-250); 
        self.resizeTo(Width="450",Height="600");
        
                        
                $(function() {
                    <?if( $codigo_cortado =='V013'){ ?>
                        mostrar();
                    <?}else{?>
                        ocultar();
                    <?  
                    }
                    ?>
                });  
                  
            function validar_formulario(){
                
                var cmbDosis = document.getElementById("cmbDosis").value;
                var cmbLugarVacunacion = document.getElementById("cmbLugarVacunacion").value;
                var cmbPresentacion = document.getElementById("cmbPresentacion").value;
                var cmbGrupoRiesgo = document.getElementById("cmbGrupoRiesgo").value;
                
                if (cmbDosis=='-1')
                {   

                    alert("Debe seleccionar dosis" );
                    return false;

                }
                if(document.getElementById('divGrupoRiesgo').style.display == "block"){
                    if (cmbGrupoRiesgo =='-1')
                    {   

                        alert("Debe seleccionar grupo de riesgo" );
                        return false;

                    }
                }
                if (cmbLugarVacunacion=='-1')
                {   

                    alert("Debe seleccionar lugar de vacunacion " );
                    return false;

                }
                if (cmbPresentacion=='-1')
                {   

                    alert("Debe seleccionar presentacion" );
                    return false;

                }
            }
            function ocultar(){
                var obj = document.getElementById('divGrupoRiesgo')
                obj.style.display = "none"
            }
            function mostrar(){
                var obj = document.getElementById('divGrupoRiesgo')
                obj.style.display = "block"
            }
    </script>


    <form name='frmDatosComplementarios' action='datos_complementarios.php' method='POST'>
    <div class="box" id="box" >   
            
            <input type="hidden" value="<?= $codigo ?>" id="txtCodigo" name="txtCodigo"/>
            <input type="hidden" value="<?= $id_comprobante ?>" id="txtIdComprobante" name="txtIdComprobante"/>
            <input type="hidden" value="<?= $id_nomenclador ?>" id="txtIdNomenclador" name="txtIdNomenclador"/>
            <input type="hidden" value="<?= $precio ?>" id="txtPrecio" name="txtPrecio"/>
            <input type="hidden" value="<?= $clave_beneficiario ?>" id="txtClaveBeneficiario" name="txtClaveBeneficiario"/>
            <input type="hidden" value="<?= $fecha_nac ?>" id="txtFechaNac" name="txtFechaNac"/>
        
            <label class="encabezado "for="">Datos Complementarios</label>
             
            <label for="">* Dosis:</label>
            <select name="cmbDosis" id="cmbDosis">
                
                <?
                echo "<option value=-1>Seleccione</option>";
                
                if (!$vacunas==NULL){
                    while (!$vacunas->EOF) {
                        $id_vacuna=$vacunas->fields['id_vacuna_dosis'];
                        $descripcion=$vacunas->fields['descripcion'];
                ?>
                        <option value=<?= $id_vacuna; ?> Style="background-color: <?= $color_style ?>;"><?= $descripcion ?></option>
                <?php

                        $vacunas->movenext();
                    }
                }    
                ?>
            </select>
            
            <div id="divGrupoRiesgo">
               <label for="">* Grupo Riesgo:</label>
                <select name="cmbGrupoRiesgo" id="cmbGrupoRiesgo">
                    <?
                    if (!$grupos_riesgo==NULL){
                        while (!$grupos_riesgo->EOF) {
                            $id_grupo_riesgo=$grupos_riesgo->fields['id_grupo_riesgo'];
                            $descripcion=$grupos_riesgo->fields['descripcion'];
                    ?>
                            <option value=<?= $id_grupo_riesgo; ?> Style="background-color: <?= $color_style ?>;"><?= $descripcion ?></option>
                    <?php

                            $grupos_riesgo->movenext();
                        }
                    }    
                    ?>
                </select>  
            </div>
              
            
            <label for="">Condicion:</label>
            <select name="cmbCondicion[]" id="cmbCondicion" class="multiselect" multiple="multiple">
                
                <?
//                echo "<option value=-1>Seleccione</option>";
                if (!$condiciones==NULL){
                    while (!$condiciones->EOF) {
                        $id_caracteristica=$condiciones->fields['id_caracteristica'];
                        $descripcion=$condiciones->fields['descripcion'];
                ?>
                        <option value=<?= $id_caracteristica; ?> Style="background-color: <?= $color_style ?>;"><?= $descripcion ?></option>
                <?php

                        $condiciones->movenext();
                    }
                }    
                ?>
            </select>
<!--            <label for="">* Efector:</label>-->
            <input type="hidden" name="txtEfector" id="txtEfector" value="<?= $cuiel ?>" readonly="readonly"/>
<!--            <label for="">* Fecha de Aplicacion:</label>-->
            <input type="hidden" name="txtFechaAplicacion" id="txtFechaAplicacion" value="<?= $fecha_comprobante ?>" readonly="readonly"/>
            <label for="">* Lugar de Vacunacion:</label>
            <select name="cmbLugarVacunacion" id="cmbLugarVacunacion">
                
                <?
                echo "<option value=-1>Seleccione</option>";
                echo "<option value=1>Vacunatorio</option>";
                echo "<option value=2>Terreno</option>";
                if (!$terrenos==NULL){
                    while (!$terrenos->EOF) {
                        $id_terreno=$terrenos->fields['id_terreno'];
                        $descripcion=$terrenos->fields['descripcion'];
                ?>
                        <option value=<?= $id_terreno; ?> Style="background-color: <?= $color_style ?>;"><?= $descripcion ?></option>
                <?php

                        $terrenos->movenext();
                    }
                }    
                ?>
            </select>
            <label for="">* Presentacion:</label>
            <select name="cmbPresentacion" id="cmbPresentacion">
<!--                echo "<option value=-1>Seleccione</option>";-->
                <?php
                if (!$presentaciones==NULL){
                    while (!$presentaciones->EOF) {
                        $id_presentacion=$presentaciones->fields['id_presentacion'];
                        $descripcion=$presentaciones->fields['descripcion'];
                ?>
                        <option value=<?= $id_presentacion; ?> Style="background-color: <?= $color_style ?>;"><?= $descripcion ?></option>
                <?php

                        $presentaciones->movenext();
                    }
                }    
                ?>
            </select>
            <label for="">Laboratorio:</label>
            <input type="text" name="txtLaboratorio" id="txtLaboratorio" />
            <label for="">Lote:</label>
            <input type="text" name="txtLoteNumero" id="txtLoteNumero" />
            <label for="">Fecha Vencimiento:</label>
            <input type="text" name="txtFechaVencimiento" id="txtFechaVencimiento" readonly="readonly"/>
            <div class="botonera">
                <input class="button" name="Guardar" id="button" type="submit" value="Guardar" onClick="return validar_formulario()" />
            </div>
    </div>
    </form>
