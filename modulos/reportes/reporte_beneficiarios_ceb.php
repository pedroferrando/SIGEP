<?php
    require_once("../../config.php");
    require_once("../../clases/Paginacion.php");
    require_once("../../clases/ReporteCEB.php");
    
    $rpt = new ReporteCEB(); //$rpt->getSQLEstadoCEBBenficiario('afidni','48456036');
    
    if($_REQUEST[buscar]){
        $request = $_REQUEST;
    }else{
        $request = decode_link($_REQUEST['p']);
    }
    
    if($request[buscar]){
        $regs = 25; //cantidad de registros a mostrar x pagina en el listado
        
        $page = (isset($request[page])) ? $request[page] : 0;
        $paginacion = new Paginacion($regs, $page);
        
        $query = $rpt->getSQLTotalesBeneficiariosCEB($request[cuie],$request[activo]);
        $res_totales = sql($query);
        $arr_param_url = array("cuie"=>$request[cuie],"activo"=>$request[activo],"solapa"=>$request[solapa],"buscar"=>1);
        switch($request[solapa]){
            case "rojo":
            case "amarillo":
            case "verde":
                $query_total = $rpt->getSQLCountBeneficiariosCEB($request[cuie],$request[solapa],$request[activo]);
                $registros = sql($query_total) or die;
                $total = $registros->fields['total'];
                $dsd = $paginacion->getFrom();
                $query = $rpt->getSQLBeneficiariosCEB($request[cuie],$request[solapa],$request[activo],$regs,$dsd);
                $result = sql($query);
                break;
            case "estadistica":
                $query = $rpt->getSQLTotalesEstadisticosBeneficiariosCEB($request[cuie]);
                $result = sql($query);
                break;
            default:
                break;
        }
    }
    
    echo $html_header;
?>
<script type="text/javascript" src="../../lib/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../lib/funciones_generales.js"></script>
<link rel=stylesheet type='text/css' href='../../lib/css/paginacion.css'>

<form name="frm_beneficiarios_ceb" id="frm_beneficiarios_ceb" 
      action="reporte_beneficiarios_ceb.php" method=POST 
      onsubmit="$('#loading').show();" onkeypress="return deshabilitar_enter(event)">
    <table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
        <tr>
            <td>
                <h3>Reporte de beneficiarios con CEB / con CEB a vencerse / con CEB vencida</h3>
            </td>
        </tr>
        <tr>
            <td align="center">
                <span id="filtro_efector">
                    <b>CUIE:</b>
                    <input type=text id="cuie" name="cuie" size=10 maxlength="6" style="text-transform: uppercase;" value="<?php echo $request[cuie]; ?>">
                    &nbsp;
                    <b>Beneficiarios:</b>
                    <select name="activo">
                        <option value=""></option>
                        <option value="S" <?php if($request[activo]=='S') echo 'selected="selected"'; ?>>Activos</option>
                        <option value="N" <?php if($request[activo]=='N') echo 'selected="selected"'; ?>>No activos</option>
                    </select>
                </span>
                <span id="filtro_beneficiario" style="display:none;">
                    <select name="campo">
                        <option value="afidni" <?php if($request[campo]=='afidni') echo 'selected="selected"' ?>>DNI</option>
                        <option value="clavebeneficiario" <?php if($request[activo]=='clavebeneficiario') echo 'selected="selected"' ?>>Clave Ben.</option>
                    </select>
                    <input type=text name="valor" onkeypress="return _soloNumeros(event);" value=" <?php echo $request[valor]; ?>"/>
                    &nbsp;
                </span>
                &nbsp;
                <input type="hidden" name="total_regs" value="<?php echo $total; ?>"/>
                <input type="hidden" name="solapa" value="<?php echo $request[solapa]; ?>"/>
                <input type="submit" name="buscar" value="Buscar"
                        onclick="if($('#cuie').val()!=''){
                                    $('#frm_beneficiarios_ceb').submit();
                                }else{
                                    alert('Ingrese el CUIE de un efector');
                                    return false;
                                }
                                "/>
                &nbsp;
                <?php if(isset($request[buscar]) && $request[solapa]!=""){ ?>
                    <a href="<?php echo encode_link("reporte_beneficiarios_ceb_pdf.php",$arr_param_url);?>" 
                       id="lnk_reporte_beneficiarios_ceb" 
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
    <p></p>
</form>

<?php if($request[buscar]): ?>
    <table width="95%" cellspacing="3" cellpadding="5" border="0" bgcolor="#FFFFFF" align="center">
        <tbody>
            <tr>
                <td width="20%" background="/nacer/imagenes/btn_verde.gif" style="cursor: pointer;" class="bordesderinferior" id="ma">
                    <?php $arr_param_url["solapa"] = "rojo"; ?>
                    <a href="<?php echo encode_link("reporte_beneficiarios_ceb.php",$arr_param_url); ?>">sin CEB (<?php echo $res_totales->fields['rojo']; ?>)</a>
                </td>
                <td width="20%" background="/nacer/imagenes/btn_amarillo.gif" style="cursor: pointer;" class="bordesderinferior" id="ma">
                    <?php $arr_param_url["solapa"] = "amarillo"; ?>
                    <a href="<?php echo encode_link("reporte_beneficiarios_ceb.php",$arr_param_url); ?>">con CEB pr&oacute;xima a vencerse (<?php echo $res_totales->fields['amarillo']; ?>)</a>
                </td>
                <td width="20%" background="/nacer/imagenes/btn_azul.gif" style="cursor: pointer;" class="bordesderinferior" id="ma">
                    <?php $arr_param_url["solapa"] = "verde"; ?>
                    <a href="<?php echo encode_link("reporte_beneficiarios_ceb.php",$arr_param_url); ?>">con CEB (<?php echo $res_totales->fields['verde']; ?>)</a>
                </td>
                <td width="20%" style="cursor: pointer;" class="bordesderinferior" id="ma">
                    <?php $arr_param_url["solapa"] = "estadistica"; ?>
                    <a href="<?php echo encode_link("reporte_beneficiarios_ceb.php",$arr_param_url); ?>">Datos Estad&iacute;sticos</a>
                </td>
            </tr>
        </tbody>
    </table>
<?php endif; ?>
<?php
    switch($request[solapa]){
        case "rojo":
            $title = "<h4>Listado de beneficiarios con CEB vencida - Total: ".$res_totales->fields['rojo']."</h4>";
            $form = 'reporte_beneficiarios_ceb_body.php';
            break;
        case "amarillo":
            $title = "<h4>Listado de beneficiarios con CEB a vencerse - Total: ".$res_totales->fields['amarillo']."</h4>";
            $form = 'reporte_beneficiarios_ceb_body.php';
            break;
        case "verde":
            $title = "<h4>Listado de beneficiarios con CEB - Total: ".$res_totales->fields['verde']."</h4>";
            $form = 'reporte_beneficiarios_ceb_body.php';
            break;
        case "estadistica":
            $title = "<h4>Datos Estadisticos</h4>";
            $form = 'reporte_beneficiarios_ceb_body_estadistica.php';
            break;
        default:
            break;
    }
    $arr_param_url["solapa"] = $request[solapa];
?>
<div id="listado_beneficiarios_ceb">
    <?php 
    if($request[buscar] && $form!="")
        include($form); 
    ?>
</div>
<p>&nbsp;</p>
