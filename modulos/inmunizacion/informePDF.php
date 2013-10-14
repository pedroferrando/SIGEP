<?php
require_once("../../config.php");
require_once("../../lib/lib.php");
require_once("../../lib/fpdf/fpdf.php");
require_once("clases/Pdf.php");
require_once("clases/Beneficiario.php");

$Consulta = "select laboratorio,lote,fecha_vencimiento,t.descripcion terreno,id_prestacion_inmu,id_vacuna_dosis,a.descripcion,fecha_inmunizacion,fecha_carga,cuie,e.nombreefector
            from inmunizacion.prestaciones_inmu
            left join inmunizacion.vacunas_dosis as a using(id_vacuna_dosis)
            left join inmunizacion.terrenos as t using(id_terreno)
            left join facturacion.smiefectores as e using(cuie)
            where clave_beneficiario='$_REQUEST[clave]' and eliminado=0
            order by fecha_inmunizacion";

$beneficiario=  Beneficiario::getBeneficiario($_REQUEST[clave]);
$res_pdf = sql($Consulta, "Error al traer vacunas") or excepcion("Error vacunas");

if ($res_pdf->RecordCount() > 0) {  
    $pdf=new Pdf();
    $pdf->setBeneficiairo($beneficiario);
    $pdf->AddPage();
    $pdf->SetFont('Times','',10);
    $header=array("Fecha","Descripcion","Efector","Lugar","Laboratorio","Lote","Vencimiento");
    $fields=array("fecha_inmunizacion","descripcion","nombreefector","terreno","laboratorio","lote","fecha_vencimiento");
    $pdf->TablaColores($header,$fields,$res_pdf);
    $pdf->SetY(65);
    $pdf->Output();   
}        
?>
