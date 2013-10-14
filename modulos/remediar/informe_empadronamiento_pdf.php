<?php 

"Copyright (C) 2013 <Pezzarini Pedro Jose (jose2190@gmail.com)>

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.";


require_once ("../../clases/Utilidades/imageRender.php");
require_once ("./clases/empadronamiento_pdf.php");

$charts = array();

$render = new ImageRender();

foreach (array_keys($_POST) as $key) {
    $charts[$key] = $_POST[$key];
    foreach (array_keys($charts[$key]) as $field) {
        if ($field == "data") {
            $charts[$key][$field] = json_decode(stripcslashes($charts[$key][$field]));
        }
    }
}

#echo $_POST["zonas"]["chart"];
$render->render($_POST["zonas"]["chart"]);
$render->saveFile("./exportacionesChart","","png");
$render->saveToJpeg(90);
echo $render->savedFileJpeg;
// foreach ($charts as $key) {
//     $render->render($key["chart"]);
//     $render->saveFile("./exportacionesChart","","png");
//     $render->saveToJpeg(90);
//     $key["chart"] = $render->savedFileJpeg;
//     echo $key["chart"];
// }

// $render->render($data);
// echo $render->saveFile(".","example","png");
// $render->saveToJpeg(90);
// echo $render->savedFileJpeg;



// $pdfRender = new PDFEmpadronamiento();
// $pdfRender->getReportZonas('./example.png-converted.jpeg');
// $pdfRender->getReportAreas('./example.png-converted.jpeg');
// $pdfRender->getReportEfectores('./example.png-converted.jpeg');
// $pdfRender->getPDF();



// $var1 = $charts["areas"]["data"];
// $r = json_decode(stripcslashes($var1));
// #$r = (json_decode("{\"values\":[\"5\"],\"labels\":[\"Area 12\"]} "));
// print_r($r->{"values"});

?>

