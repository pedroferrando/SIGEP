<?php
ob_start();
require_once ("../../config.php");
require_once("../../lib/lib.php");
?>

<body bgcolor="#E0E0E0"></body>
<form action="informe_recepcion.php" method="post">

    <table style="text-align:center;width:100%;padding:10px;" ><tr>
            <td><b>Nombre del Archivo Recepcionado:</b>&nbsp;

                <?php
                $SQLef = "SELECT idrecepcion, nombrearchivo, substring(nombrearchivo,2,6) as cuie 
                   FROM inmunizacion.recepciontxt";
                $e_busqueda = sql($SQLef, "Error al insertar la prestacion") or fin_pagina();
                ?>
                <SELECT NAME="idrec" style="font-size:13" >
                    <?php
                    if ($e_busqueda->RecordCount() > 0) {
                        $e_busqueda->MoveFirst();
                        while (!$e_busqueda->EOF) {
                            if ($idrec == $e_busqueda->fields["idrecepcion"]) {
                                $selected = 'selected';
                            } if ($idrec != $e_busqueda->fields["idrecepcion"]) {
                                $selected = '';
                            }
                            $cuie = $e_busqueda->fields["cuie"];
                            $busca = "SELECT id_efe_conv FROM nacer.efe_conv WHERE cuie = '$cuie'";
                            $e_busca = sql($busca, "Error al buscar nombre de efector") or excepcion("Error al buscar nombre de efector");
                            if ($e_busca->RecordCount() > 0) {
                                $nomefector = $e_busca->fields["nombre"];
                            }
                            ?>
                            <OPTION VALUE="<?php echo $e_busqueda->fields["idrecepcion"]; ?>"
                                    <?php echo $selected; ?> >
                                        <?php echo $nomefector . ' - ' . $e_busqueda->fields["nombrearchivo"]; ?>
                            </OPTION>
                            <?php
                            $e_busqueda->MoveNext();
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><input type="submit" name="enviar" value="Enviar" /></td>
        </tr>
    </table>

</form>

<?php if ($_POST['enviar'] == "Enviar") { ?>

    <table style="text-align:center;width:100%;padding:10px;" ><tr>
            <td>&nbsp;
                <?php
                /* ++++++++++++++++++++++++++++ busqueda de nombre archivo, muestra efector si encentra archivo ++++++++++++++++++++++++++++++++++ */
                $idrecepcion = $_POST['idrec'];
                $consulta = "idrecepcion='$idrecepcion'";
                //$consulta2 = "";
                $SQLef = "SELECT nombrearchivo,idrecepcion,substring(nombrearchivo,2,6) as cuie, recepcionado
            FROM inmunizacion.recepciontxt where idrecepcion=$idrecepcion";
                $e_busq = sql($SQLef, "Error en nombre de archivo") or excepcion("Error en nombre de archivo");

                if ($e_busq->RecordCount() > 0) {
                    $cuie = $e_busq->fields["cuie"];
                    $nombrearchivo = $e_busq->fields["nombrearchivo"];

                    if ($e_busq->fields["recepcionado"] == 't') {
                        $inmu = "inmunizacion.benefinmunizacion";
                        $final = true;
                    } else {
                        $inmu = "inmunizacion.benefinmunizacion_tmp";
                        $final = false;
                    }


                    $SQLef11 = "SELECT  cuie FROM nacer.efe_conv where cuie='$cuie'";
                    $e_busqda = sql($SQLef11, "Error en efector") or excepcion("Error en efector");
                    if ($e_busqda->RecordCount() > 0) {
                        $nombre = $e_busqda->fields["nombre"];
                        echo '<b>Segun rendición de Archivo: </b>' . $nombre . ' - ' . $nombrearchivo;
                        if (!$final)
                            echo "<br /><strong style='color:red;'>Archivo aun no recepcionado.</strong>";
                    } else {
                        echo 'No se encuentra efector!';
                    }
                } else {
                    echo 'No hay archivo de rendicion';
                }
                /* ++++++++++++++++++++++++++++ fin busqueda++++++++++++++++++++++++++++++++++ */



                $consul = "SELECT cast(min(fechavacunacion) as varchar) as f1, max(fechavacunacion) as f2
     FROM $inmu where $consulta";
                $busq = sql($consul, "Error fecha elegida") or excepcion("Error en fecha elegida");
                if ($busq->RecordCount() > 0) {
                    echo '<center><H3><u><b>Desde: ' . Fecha($busq->fields["f1"]) . ' Hasta: ' . Fecha($busq->fields["f2"]) . '</b></u></H3></center>';
                } else {

                    echo '<center><H3><u><b>No hay resultados para los parametros elegidos</b></u></H3></center>';
                }
                ?> 



            </td>
        </tr>
    </table>


    <?php
    /* +++++++++++++++muestra vacuna, grupo y cantidad+++++++++++++++++++++++++++++++++++++ */
    $consu227 = "SELECT a.idvacuna,vacuna FROM $inmu a 
            inner join inmunizacion.vacunas b on a.idvacuna=b.idvacuna where ( $consulta ) 		
            group by a.idvacuna,vacuna order by a.idvacuna";

    $busc = sql($consu227, "Error fecha ") or excepcion("Error en fecha ");

    if ($busc->RecordCount() > 0) {
        $busc->moveFirst();
        while (!$busc->EOF) {

            $vacuna = $busc->fields["idvacuna"];
            $nombre_vacuna = $busc->fields["vacuna"];

            echo'<table align="center"  cellpadding="0" cellspacing="0" border="0" width="50%">
 <tr ><td><b>' . $nombre_vacuna . '</b></td> </tr>
	</table>';
            echo '<table align="center"  cellpadding="0" cellspacing="0" border="1" width="40%">';

            $consu = "SELECT count(*) as cantidad,a.idrangovacuna,a.idvacuna 
        FROM $inmu a
	inner join inmunizacion.vacunas b on a.idvacuna=b.idvacuna
	where  ( $consulta $consulta2 ) and a.idvacuna='$vacuna'		
	group by a.idrangovacuna,a.idvacuna";

            $bus = sql($consu, "Error fecha ") or excepcion("Error en fecha ");
            if ($bus->RecordCount() > 0) {
                $bus->moveFirst();
                while (!$bus->EOF) {

                    $cantidad = $bus->fields["cantidad"];
                    $idrangovacuna = $bus->fields["idrangovacuna"];
                    $consu2 = "SELECT descripcion FROM inmunizacion.vacunasrangos where idrangosvacunas ='$idrangovacuna'";
                    $bus2 = sql($consu2, "Error en vacuna") or excepcion("Error en vacuna");

                    echo '<tr align="center" width="80%"><td>' . $bus2->fields["descripcion"] . '</td><td width="20%">' . $cantidad . '</td></tr>';
                    $bus->moveNext();
                }
            }
            echo "</table>";
            $busc->moveNext();
        }
    }
}
fin_pagina();
/* ++++++++++++++++++++++++++++ fin busqueda++++++++++++++++++++++++++++++++++ */
?>
  
