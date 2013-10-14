<?php

$mi_pdf = 'vigente2013.pdf';
header('Content-type: application/pdf');
header('Content-Disposition: attachment; filename="'.$mi_pdf.'"');
readfile($mi_pdf);

?>
