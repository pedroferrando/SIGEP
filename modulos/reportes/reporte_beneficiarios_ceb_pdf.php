<?php
    require_once("../../config.php");
    require_once('../../lib/fpdf.php');
    require_once("../../clases/ReporteCEB.php");    
    require_once("../../clases/creador_pdf.php");
    require_once("./creador_pdf.php");
    
    header("Cache-Control: no-cache, must-revalidate");
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    
    $request = decode_link($_REQUEST['p']);
    
    $rpt = new ReporteCEB();
    //$pdf = new CreadorPDFReportesOld();
    $pdf = new CreadorPDFReportes('P','mm','A4');
    
    switch($request[solapa]){
        case "rojo":
        case "amarillo":
        case "verde":
            $query = $rpt->getSQLBeneficiariosCEB($request[cuie],$request[solapa],$request[activo]);
            $result = sql($query);
            break;
        case "estadistica":
            $query = $rpt->getSQLTotalesEstadisticosBeneficiariosCEB($request[cuie]);
            $result = sql($query);
            break;
        default:
            break;
    }
    
    $pdf->SetPrograma("sumar");
    $pdf->getReporteCoberturaCEB($request,$result);
    
    //print_r($request);
?>
