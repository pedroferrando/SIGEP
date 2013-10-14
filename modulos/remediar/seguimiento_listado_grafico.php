<?php
#   Archivo de configuracion del sistema
require_once("../../config.php");
include ("../../lib/imagenes_stat/jpgraph.php");
include ("../../lib/imagenes_stat/jpgraph_line.php");

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
$d = $beneficiario->sqlObtenerSeguimiento();

#   Result Beneficiario desde trazadoras.clasificacion_remediar y
#   trazadoras.clasificacion_remediar2
$beneficiario_clasificacion_result = sql($beneficiario->sqlObtenerClasificacion());

#   Contruye al beneficiario con los datos afiliatorios
$beneficiario->construirClasificacion($beneficiario_clasificacion_result);





#   Datos del gráfico
$graficoDatos = array();
$graficoCategorias = array();
$graficoTitulo = "Historial: ".$beneficiario->getApellido().", ".$beneficiario->getNombre()
        ." (".$beneficiario->getTipoDoc()." ".$beneficiario->getNroDoc().")";

#   Carga de los datos y valores

$graficoCategorias[0] = "Clasif.";
$graficoDatos[0] = $beneficiario->RiesgoClasificacionAValor();

while (!$beneficiario_result->EOF){

    #   Construye al beneficiario con los datos de seguimiento
    $beneficiario->construirSeguimiento($beneficiario_result);
    
    
    $graficoCategorias[] = "Seg. ".$beneficiario->getNroSeguimiento();
    $graficoDatos[] = $beneficiario->RiesgoSeguimientoAValor();
    
    $beneficiario_result->MoveNext();
}
        






#   Configuraciones del gráfico
$graph = new Graph(600,200);
$graph->SetScale("textlin");
$graph->SetBox(false);
$graph->yaxis->HideZeroLabel();
$graph->yaxis->HideLine(false);
$graph->yaxis->HideTicks(false,false);
$graph->xaxis->SetTickLabels($graficoCategorias);
$graph->ygrid->SetFill(false);
$graph->SetBackgroundImage("../../imagenes/remediar+redesLogo.jpg",BGIMG_FILLFRAME);
$graph->title->Set($graficoTitulo);
$p1 = new LinePlot($graficoDatos);
$graph->Add($p1);
$p1->SetColor("#FC4908");
$p1->SetLegend('RCVG');
$p1->mark->SetType(MARK_FILLEDCIRCLE,'',2.0);
$p1->mark->SetColor('#FC4908');
$p1->mark->SetFillColor('#FC4908');
$p1->SetCenter();
$graph->legend->SetFrameWeight(1);
$graph->legend->SetColor('#4E4E4E','#00A78A');
$graph->legend->SetMarkAbsSize(8);


#   Devuelve el gráfico al navegador
$graph->Stroke();
?>
