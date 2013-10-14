<?php
#   Archivo de configuracion del sistema
require_once("../../config.php");
include ("../../lib/imagenes_stat/jpgraph.php");
include ("../../lib/imagenes_stat/jpgraph_bar.php");

#   Archivo que contiene las classes de BeneficiarioSeguimiento
require_once("./seguimiento_listado_funciones.php");


#   Clave del beneficiario recibido como parámetro
$beneficiario_clave = $parametros['claveBeneficiario'];



#   Objeto Beneficiario
$beneficiario = new BeneficiarioSeguimiento($beneficiario_clave);


#   Result Beneficiario desde UAD.beneficiarios
$beneficiario_result = sql($beneficiario->sqlObtenerBeneficiario());


#   Contruye al beneficiario con los datos afiliatorios
$beneficiario->construirBeneficiario($beneficiario_result);


#   Result Beneficiario desde trazadoras.seguimiento_remediar  
$beneficiario_result = sql($beneficiario->sqlObtenerSeguimiento());


#   Result Beneficiario desde trazadoras.clasificacion_remediar y
#   trazadoras.clasificacion_remediar2
$beneficiario_clasificacion_result = sql($beneficiario->sqlObtenerClasificacion());

#   Contruye al beneficiario con los datos afiliatorios
$beneficiario->construirClasificacion($beneficiario_clasificacion_result);


#   Muestra el cuerpo HTML, usado de forma estandar en el sistema
echo $html_header;
?>


<!-- Importacion de librerias para interaccion grafica y de funcionamiento en HTML -->
<script src='../../lib/jquery.min.js' type='text/javascript'></script>
<script src='../../lib/jquery/jquery-ui.js' type='text/javascript'></script>
<link rel="stylesheet" href="../../lib/jquery/ui/jquery-ui.css" />
<!-- ---------------------------------------------------------------------------- -->



<!-- Funciones para interaccion grafica y funcionamiento en HTML-->
<script>
    $(function() {
        $( "#accordion" ).accordion();
    });
</script>
<!-- ---------------------------------------------------------------------------- -->


</head>
<body>
   
    <div id="mo"><h2>Remediar + Redes: Existen <?=$beneficiario_result->NumRows()?> detalles de Seguimientos </h2></div>
    
<div style="width:90%;" align="center"> 
    <div style="width:90%;" align="left">
        
       
        <div id="accordion">
            
            
            <!-- Datos del Empadronamiento -->
            <h3>Datos del Beneficiario: <?=$beneficiario->getApellido().", ".$beneficiario->getNombre()?></h3>
            <div>
                <table width="100%">
                    <tr id="mo">
                        <td>Datos:</td>
                    </tr>
                    
                    <tr bgcolor="#D6EBFF">
                        <td><b>Apellido:</b> <?=$beneficiario->getNombre()?></td>
                        <td><b>Nombre:</b> <?=$beneficiario->getApellido()?></td>
                        <td><b>Tipo Documento:</b> <?=$beneficiario->getTipoDoc()?></td>
                        <td><b>Num Documento:</b> <?=$beneficiario->getNroDoc()?></td>
                        <td><b>Fecha Nac:</b> <?=fecha($beneficiario->getFechaNacimiento())?></td>
                    </tr>
                    <tr bgcolor="#E0FFD6">
                        
                        <td><b>Barrio:</b> <?=$beneficiario->getDomicilioBarrio()?></td>
                        <td><b>Calle:</b> <?=$beneficiario->getDomicilioCalle()?></td>
                        <td><b>Nro:</b> <?=$beneficiario->getDomicilioNro()?></td>
                        <td><b>Localidad:</b> <?=$beneficiario->getDomicilioLocalidad()?></td>
                        <td><b>Municipio:</b> <?=$beneficiario->getDomicilioMunicipio()?></td>
                    </tr>
                    
                </table>
                <br />
                <table width="100%">
                    <tr id="mo">
                        <td>Inscripcion:</td>
                    </tr>
                    
                    <tr bgcolor="#D6EBFF">
                        <td><b>Efector:</b> <?=$beneficiario->getInscripcionCentroInscriptor()?></td>
                        <td><b>Cuie Efector:</b> <?=$beneficiario->getInscripcionCentroInscriptorCuie()?></td>
                        <td><b>Promotor:</b> <?=($beneficiario->getInscripcionApellidoPromotor().", ".$beneficiario->getInscripcionNombrePromotor())?></td>
                    </tr>
                    
                    <tr bgcolor="#E0FFD6">    
                        
                        <td><b>Score de Riesgo:</b> <?=$beneficiario->getInscripcionScoreRiesgo()?></td>
                        <td><b>Empadronado:</b> <?=fecha($beneficiario->getInscripcionFechaEmpadronamiento())?></td>
                    </tr>
                    
                </table>
            </div>
            
            
            
            
            <!-- Datos de la Clasificacion -->          
           
            <h3>Clasificacion: <?=$beneficiario->getClasificacionEfector()?></h3>
            <div>
                <fieldset>
                    <legend>Durante la clasificacion, el beneficiario present&oacute; :</legend>
                
                
                <table width="100%">
                    <tr id="mo">
                        <td>En la clasificaci&oacute;n N: <?=$beneficiario->getClasificacionNro()?></td>
                    </tr>

                        <?php
                            foreach ($beneficiario->getResumenClasificacion() as $valor) {
                                ?>
                                    <tr bgcolor="#D6EBFF">
                                        <td><?=$valor?></td>
                                    </tr>
                                <?php
                            }
                        ?>
                </table>
                    
                <br />
                <table width="100%">
                    
                    <tr id="mo">
                        <td></td>
                    </tr>
                    <tr bgcolor="#E0FFD6"> 
                        <td>Realizada en: (<?=$beneficiario->getClasificacionEfectorCod()?>) - <?=$beneficiario->getClasificacionEfector()?>  </td>
                    </tr>
                    
                    <tr bgcolor="#E0FFD6">
                        <td>Por: <?=$beneficiario->getClasificacionMedicoNombreCompleto()?></td>
                    </tr>
                    
                    <tr bgcolor="#E0FFD6">
                        <td>El d&iacute;a: <?=fecha($beneficiario->getClasificacionFechaControl())?></td>
                    </tr>
                    <tr id="mo">
                        <td></td>
                    </tr>
                </table>
                
                
                </fieldset>
            </div>
            
            
        <!-- Datos de los seguimientos -->    
        <?php 
            while (!$beneficiario_result->EOF){
                #   Construye al beneficiario con los datos de seguimiento
                $beneficiario->construirSeguimiento($beneficiario_result);
                
        ?>
        
        
        <h3>Seguimiento Nro: <?=$beneficiario->getNroSeguimiento()?> - <?=$beneficiario->getEfectorNombre()?></h3>
            <div>
                <table width="100%">
                    <tr id="mo">
                        <td>Efector: (<?=$beneficiario->getEfectorCodigo()?>) - <?=$beneficiario->getEfectorNombre()?></td>
                    </tr>
                    
                    <tr bgcolor="#D6EBFF">
                        <td>Dmt2: <?=$beneficiario->getDmt2()?></td>
                        <td>HTA: <?=$beneficiario->getHta()?></td>
                        <td>Ta Sist: <?=$beneficiario->getTaSist()?></td>
                        <td>Ta Diast: <?=$beneficiario->getTaDiast()?></td>
                    </tr>
                    <tr bgcolor="#E0FFD6">
                        <td>Tabaquismo: <?=$beneficiario->getTabaquismo()?></td>
                        <td>Col. Total: <?=$beneficiario->getColesterol()?></td>
                        <td>Glucemia: <?=$beneficiario->getGlucosa()?></td>
                        <td>Peso: <?=$beneficiario->getPeso()?></td>
                    </tr>
                    <tr bgcolor="#D6EBFF">
                        <td>Talla: <?=$beneficiario->getTalla()?></td>
                        <td>IMC: <?=$beneficiario->getImc()?></td>
                        <td>Hba1c: <?=$beneficiario->getHba1c()?></td>
                        <td>ECG: <?=$beneficiario->getEcg()?></td>
                    </tr>
                    
                    <tr bgcolor="#E0FFD6">
                        <td>Fondo de Ojo: <?=$beneficiario->getFondoDeOjo()?></td>
                        <td>Examen de Pie: <?=$beneficiario->getExamenDePie()?></td>
                        <td>Microalbuminuria: <?=$beneficiario->getMicroalbuminuria()?></td>
                        <td>HDL: <?=$beneficiario->getHdl()?></td>
                    </tr>
                    <tr bgcolor="#D6EBFF">
                        <td>LDL: <?=$beneficiario->getLdl()?></td>
                        <td>TAGs: <?=$beneficiario->getTags()?></td>
                        <td>Creatininemia: <?=$beneficiario->getCreatininemia()?></td>
                        
                    </tr>
                    
                    <tr bgcolor="#E0FFD6">
                        <td>Interconsulta: <?=$beneficiario->getInterconsultaEspecialidades1()?></td>
                        <td>Interconsulta: <?=$beneficiario->getInterconsultaEspecialidades2()?></td>
                        <td>Interconsulta: <?=$beneficiario->getInterconsultaEspecialidades3()?></td>
                        <td>Interconsulta: <?=$beneficiario->getInterconsultaEspecialidades4()?></td>
                    </tr>
                    
                    <tr bgcolor="#D6EBFF">
                        <td>Riesgo Anterior: <?=$beneficiario->getRcvgAnterior()?></td>
                        <td>Riesgo Actual: <?=$beneficiario->getRcvgActual()?></td>
                        <td>Medico: <?=$beneficiario->getMedicoNombreCompleto()?></td>
                        <td>Fecha: <?=$beneficiario->getFechaCarga()?></td>
                    </tr>
                </table>
            </div>
            
            

            
            <?php
            $beneficiario_result->MoveNext();
                
            }
            ?>
            
            
            
            
            
            <!-- Datos de la Clasificacion -->          
           
            <h3>Hist&oacute;rico de Riesgo</h3>
            <div>
             
                        <div style="width:90%;" align="center"> 
                            <table>
                                
                                <tr>
                                    
                                    <td><?php
                                        $link_s=encode_link("seguimiento_listado_grafico.php",
                                                array("claveBeneficiario"=>$beneficiario->getClaveBeneficiario())); 
                                        echo "<img src='$link_s'  border=0 align=top></a>\n";
       
                                        ?>
                                    </td>
                                    
                                    

                                    <td>
                                        ::::::::
                                    </td>
                                    
 
                                    <td>
                                        <table border="1px">
                                            <tr id="mo">
                                                <td>Riesgo</td>
                                                <td>Valor</td>
                                            </tr>
                                            <tr>
                                                <td>Bajo</td>
                                                <td>1</td>
                                            </tr>
                                            <tr>
                                                <td>Moderado</td>
                                                <td>2</td>
                                            </tr>
                                            <tr>
                                                <td>Alto</td>
                                                <td>3</td>
                                            </tr>
                                            <tr>
                                                <td>Muy Alto</td>
                                                <td>4</td>
                                            </tr>
                                        </table>
                                    </td>
                                    
                                </tr>
                            </table>
                            
                    
                        </div>
                
            </div>

        </div>
    </div>        
</div>
    
    
    
<!-- Div, que coloca una linea azul en el final del informe utilizado unicamente para estética -->

<br />
<div id="mo">&nbsp;</div>

<br />
<br />

<!-- ----------------------------------------------------- -->

</body>

</html>





