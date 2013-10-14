<?php
//b_start();
require_once ("../../config.php");
require_once("../../lib/lib.php");
?>
<body bgcolor="#E0E0E0"></body>
<form action="recepcion_final.php" method="post">

  <table style="text-align:center;width:100%;padding:10px;" ><tr>
      <td><b>Archivo Recepcionado:</b>&nbsp;
        <?php
        $SQLef = "SELECT idrecepcion, nombrearchivo, substring(nombrearchivo,2,6) as cuie 
    FROM inmunizacion.recepciontxt where recepcionado = 'false' order by cuie";
        $e_busqueda = sql($SQLef, "Error al consultar nombre de archivo") or excepcion("Error al consultar nombre de archivo");
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
              $busca = "SELECT nombreefector FROM facturacion.smiefectores WHERE cuie = '$cuie'";
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
      <td><input type="submit" name="enviar" value="Recepcionar" /></td>
    </tr>
  </table>

</form>

<?php if ($_POST['enviar'] == "Recepcionar") { ?>
  <?php
  //try
  try {

    sql("BEGIN");

//obtenes el id del select (post)
    $idrecepcion = $_POST['idrec'];


//Insert y select juntos

    $sql_traspaso = "insert into inmunizacion.benefinmunizacion
     (idrecepcion,cuie,idprestacion,clavebeneficiario,clasedoc,tipodoc,
        nrodoc,apellido,nombre,fechanac,fechavacunacion,idvacuna,fila,mensaje,
        domicilio,departamento,municipio,sexo,originaria,donde,terreno,
        iddepartamento,idmunicipio,idrangovacuna,nombre_aldea,liquidado,idacta,idpais,procesado,fechaproceso)
  SELECT idrecepcion,cuie,idprestacion,clavebeneficiario,clasedoc,tipodoc,nrodoc,apellido,nombre, 
  fechanac,fechavacunacion,idvacuna,fila,mensaje,domicilio,departamento,municipio,sexo,originaria,donde,terreno,
        iddepartamento,idmunicipio,idrangovacuna,nombre_aldea,liquidado,idacta,idpais,procesado,fechaproceso 
  FROM inmunizacion.benefinmunizacion_tmp WHERE idrecepcion = $idrecepcion";

    $tras = sql($sql_traspaso, "Insercion realizada con exito") or excepcion("Insercion realizada con exito");



//borrado de tabla

    $sql_eliminar = "DELETE FROM inmunizacion.benefinmunizacion_tmp WHERE idrecepcion = $idrecepcion";
    $bus = sql($sql_eliminar, "Borrado realizado con exito") or excepcion("Borrado realizado con exito");

    $upd = "UPDATE inmunizacion.recepciontxt SET recepcionado = 'true' WHERE idrecepcion = $idrecepcion";
    $bus = sql($upd, "Actualizacion realizada con exito") or excepcion("Actualizacion realizada con exito");
   //echo "Archivo guardado con exito";


    sql("COMMIT");

    echo "Archivo guardado con exito";

//catch
  } catch (Exception $e) {
    sql("ROLLBACK", "Error en rollback", 0);
    echo "Error: " . $e->getMessage() . "<br /><br /><br />";
  }
  ?>

<?php } ?>
<?php
echo fin_pagina(); // aca termino
?>
<?php
ob_end_flush();
?>