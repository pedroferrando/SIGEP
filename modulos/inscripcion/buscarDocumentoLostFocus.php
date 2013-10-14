<?

require_once ("../../config.php");

$num_doc = $_POST['num_doc'];

$sql1 = "SELECT * 
         FROM uad.beneficiarios	  
         WHERE numero_doc='$num_doc'";
//         AND tipo_documento='$tipo_doc'
//         AND clase_documento_benef='$clase_doc';

$res_extra1 = sql($sql1, "Error al traer el beneficiario") or fin_pagina();

echo $html_header;
?>
<input id="existe"></input>
<?= fin_pagina() ?>


