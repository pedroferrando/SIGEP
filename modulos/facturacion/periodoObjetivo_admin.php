<?php

include_once '../../config.php';


require_once("../../lib/funciones_misiones.php");
require_once("../../clases/PeriodoObjetivo.php");

$listado_periodos = PeriodoObjetivoColeccion::Filtrar();

echo $html_header;
?>
<script src='../../lib/jquery.min.js' type='text/javascript'></script>
<script src='../../lib/jquery/jquery-ui.js' type='text/javascript'></script>
<link href="../../lib/estilos.css" type="text/css" rel="stylesheet">
<link rel='stylesheet' href='../../lib/jquery/ui/jquery-ui.css'/>
<script src='../../lib/jquery/ui/jquery.ui.datepicker-es.js' type='text/javascript'></script>

<script>
    $(document).ready(function() {
        $('#desde').datepicker();
        $('#hasta').datepicker();
    });
</script>


<?php

include './periodoObjetivo_admin.tpl.php';
?>
