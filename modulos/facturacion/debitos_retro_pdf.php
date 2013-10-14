<?php
    require_once("../../config.php");
    require_once('../../lib/fpdf.php');
    require_once("../../clases/DebitoRetroactivo.php");
    require_once("../../clases/creador_pdf.php");
    require_once("./bibiliotecaExpediente.php");
    require_once("./creador_pdf.php");
    
    header("Cache-Control: no-cache, must-revalidate");
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
        
    $request = decode_link($_REQUEST['p']);
    $arrEfectores = $request[arr_cuie];
    $nroExpediente = trim($request[nro_expediente]);
    
    foreach($arrEfectores as $cuie){
        $prestDeb = DebitoRetroactivoColeccion::getPrestacionesDebitoRetroactivo($cuie,$nroExpediente);
        if($prestDeb->NumRows()>0){
            $params[] = array("cuie"    => $cuie,
                              "nombre"  => buscarNombreEfector($cuie),
                              "result"  => $prestDeb );
        }
    }
    
    $pdf = new CreadorPDFFacturacion('P','mm','A4');
    
    $pdf->getReporteDebitosRetroactivos($request[nro_expediente], $params);
    
?>
