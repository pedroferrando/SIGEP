<?php
    require_once("../../config.php");
    require_once("../../clases/beneficiarios.php");
    require_once("../../lib/bibliotecaTraeme.php");
    require_once("../../lib/funciones_misiones.php");
    require_once("./funciones.php");
    
    if($_REQUEST['buscar']){
        $request = $_REQUEST;
    }else{
        $request = decode_link($_REQUEST['p']);
    }
    if(isset($_REQUEST[buscar]) && $_REQUEST[buscar]!=""){
        $arr_ids = array();
        $page = 0;
        $regs = 15;
        $beneficiario = new Beneficiario();
	$beneficiario->Automata("numero_doc = '".$_REQUEST[nro_doc]."' AND clase_documento_benef='P' ");
        if($beneficiario->getClave_beneficiario()!=""){
            $nombreBeneficiario = $beneficiario->getNombreCompleto();
            $claveBeneficiario =  $beneficiario->getClave_beneficiario();
            $fechaNacBeneficiario = $beneficiario->getFecha_nacimiento_benef();
        }else{
            //buscar en smiafiliados
            $benef = datosAfiliadoEnVigente('',$_REQUEST[nro_doc]);
            if($benef['clavebeneficiario']!=""){
                $nombreBeneficiario = $benef['afiapellido'].", ".$benef['afinombre'];
                $claveBeneficiario =  $benef['clavebeneficiario'];
                $fechaNacBeneficiario = date('d/m/Y',strtotime($benef['afifechanac']));
            }
        }
        $sql_total = getSQLCountPrestacionesBeneficiario($_REQUEST[nro_doc],$_REQUEST[fecha_desde],$_REQUEST[fecha_hasta]);
        $registros = sql($sql_total) or die;
        $total = $registros->fields['total'];
        $sql = getSQLPrestacionesBeneficiario($_REQUEST[nro_doc],$_REQUEST[fecha_desde],$_REQUEST[fecha_hasta],$regs,0);
        //echo "<pre>".$sql."</pre>";
        $result = sql($sql) or die;
    }
    
    echo $html_header;
?>
<script type="text/javascript" src="../../lib/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../lib/funciones_generales.js"></script>
<script type="text/javascript" src="./funciones.js?661"></script>


        <form name="frm_lst_prestaciones" id="frm_lst_prestaciones" 
              action="listado_prestaciones_doms.php" method=POST 
              onsubmit="$('#loading').show();" onkeypress="return deshabilitar_enter(event)">
            <table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
                <tr>
                    <td>
                        <h3>Reporte de prestaciones y Datos de Trazadoras (DOMs)</h3>
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <b>Desde:</b>
                        <input type=text id="fecha_desde" name="fecha_desde" 
                            readonly="readonly" size=10 maxlength="10" 
                            value="<?php echo $request['fecha_desde'] ?>">
                            <?php echo link_calendario('fecha_desde');?>
                        &nbsp;&nbsp;
                        <b>Hasta:</b>
                        <input type=text id="fecha_hasta" name="fecha_hasta"  
                            readonly="readonly" size=10 maxlength="10" 
                            value="<?php echo $request['fecha_hasta'] ?>">
                            <?php echo link_calendario('fecha_hasta');?>
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <b>Nro de Documento (propio):</b>
                        <input type="text" id="nro_doc" name="nro_doc" 
                               size="10" maxlength="8"
                               onkeypress="return _soloNumeros(event);"
                               value="<?php echo $request['nro_doc'];?>">
                        &nbsp;
                        <input type="hidden" name="total_regs" value="<?php echo $total; ?>"/>
                        <div id="params" style="display:none;"></div>
                        <input type="submit" name="buscar" value="Buscar"
                               onclick="if($('#nro_doc').val()!=''){
                                           $('#frm_lst_prestaciones').submit();
                                        }else{
                                            alert('Ingrese un nro de documento');
                                            return false;
                                        }
                                        "/>
                        &nbsp;
                        <?php if($result->_numOfRows>0){ ?>
                            <a href="<?php echo encode_link("listado_prestaciones_doms_pdf.php",array("nro_doc"=>$_REQUEST[nro_doc],
                                                                                                      "fecha_desde"=>$_REQUEST[fecha_desde],
                                                                                                      "fecha_hasta"=>$_REQUEST[fecha_hasta]
                                                                                                     )
                                                           );?>" 
                               id="lnk_reporte_prestaciones_doms" 
                               target="_blank" title="Imprimir Reporte"
                               style="vertical-align: top;">
                                <img width="20" height="20" border="0" src="/nacer/imagenes/pdf_logo.gif">
                            </a>
                        <?php } ?>
                    </td>
                </tr>
            </table>
            <p align="center" id="loading" style="display:none;">
                <img src="../../imagenes/wait.gif" alt="Aguarde unos instantes"/>
                Aguarde unos instantes
            </p>
        </form>

        <?php if(isset($_REQUEST[buscar]) && $_REQUEST[buscar]!=""){ ?>
            <p id="datos_beneficiario">
                <b>Beneficiario:</b> <?php echo $nombreBeneficiario; ?> &nbsp;
                <b>Clave:</b> <?php echo $claveBeneficiario; ?> &nbsp;
                <b>Fecha Nac.:</b> <?php echo $fechaNacBeneficiario; ?>
            </p>
            <div id="listado_prestaciones">
                <?php include('listado_prestaciones_doms_body.php'); ?>
            </div>
        <?php } ?>
            <p align="center" id="loading2" style="display:none;">
                <img src="../../imagenes/wait.gif" alt="Aguarde unos instantes"/>
                Aguarde unos instantes
            </p>
        <p>&nbsp;</p>
        <div id="params" style="display:none;"></div>
        <div id="result"></div>
        <p></p>
<?php
    if($claveBeneficiario=="" && $total>0){
        $sql = getSQLDatosBeneficiarioFromTrazadora($_REQUEST[nro_doc],$trz_aux);
        $result = sql($sql) or die;
        if($result->RecordCount()>0){
            $result->MoveFirst();
            $nombreBeneficiario = $result->fields['nombre_beneficiario'];
            $claveBeneficiario =  $result->fields['clave'];
            echo "<script>mostrar_datos_beneficiario('".$nombreBeneficiario."','".$claveBeneficiario."');</script>";
        }
    }
    echo $html_footer;
    echo fin_pagina();
?>
<br><br>