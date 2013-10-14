<?php echo $html_header; ?>

<script src='../../lib/jquery.min.js' type='text/javascript'></script>
<link href="../../lib/estilos.css" type="text/css" rel="stylesheet">
<script>

    $(document).ready(function() {
        $('select#efector').on('change', function() {
            $('#img_load').css('display', 'block')
            var cuie_elegido = $('#efector').val();
            $.post("listadoComprobantesPerinatal.php", {cuie_elegido: cuie_elegido}, function(data) {
                $('#img_load').css('display', 'none')
                var reemplazo = $(data).find("tbody");
                $('#comprobantes_perinatal').html(reemplazo);

            })
        });
    });
</script>
<div width="95%" style='margin: 20px;'>
    <font style='font-size: medium'><b>Efector:</b></font> 
    <select id="efector" name=efector Style="width:450px;margin-bottom: 5px;">
        <option value=-1>Seleccione</option>
        <?php
        while (!$res_efectores->EOF) {
            $cuie = $res_efectores->fields['cuie'];
            $nombre_efector = $res_efectores->fields['nombreefector'];
            ?>
            <option value="<?php echo $cuie; ?>"
            <?php
            if ($cuie == $cuie_elegido) {
                echo "selected";
            }
            ?> ><?php echo $cuie . " - " . $nombre_efector ?></option>
                    <?php
                    $res_efectores->movenext();
                }
                ?>
    </select>

    <div id="img_load" align="center" style="display: none">Cargando<br><img src="../../imagenes/wait.gif"></div>

    <table id="comprobantes_perinatal" width="100%" cellspacing="2" cellpadding="2" border="0" bgcolor="#B7CEC4" align="center">
        <tr>
            <td id="mo" style="width: 20px">Comprobante</td>
            <td id="mo" style="width: 200px">Apellido y Nombre</td>
            <td id="mo" style="width: 60px">DNI</td>
            <td id="mo" style="width: 150px">Nomenclador</td>
            <td id="mo" style="width: 100px">Codigo Prestacion</td>
            <td id="mo" style="width: 60px">Total</td>
            <td id="mo" style="width: 80px">Fecha Comprobante</td>
        </tr>
        <?php
        $conteo = 0;
        if ($listado_comprobantes) {
            foreach ($listado_comprobantes as $comprobante) {
                $beneficiario = $comprobante->getBeneficiarioUAD();

                if ($beneficiario->getIdBeneficiarios()) {
                    $prestacion = $comprobante->getPrestacion();
                    if ($prestacion) {
                        $nomenclador = $prestacion->getNomenclador();
                        $grupoetario = $beneficiario->getGrupoEtareo($comprobante->getFechaComprobante());
                        $conteo++;
                        //busca el link al formulario correspondiente a la prestacion
                        $link_planilla = cargaTrazadora($nomenclador->getCodigo(), $nomenclador->getDiagnostico(), $comprobante->getGrupoEtario());
                        $onclick_elegir = "";
                        if ($link_planilla[0]) {
                            $datos_para_link = array("cuiel" => $cuie_elegido, "fecha_comprobante" => $comprobante->getFechaComprobante(), "clave_beneficiario" => $comprobante->getClavebeneficiario(), "grupo_etareo" => $grupoetario['categoria'], $comprobante->getGrupoEtario(), "edad" => $grupoetario['edad'], "sexo" => $beneficiario->getSexo(), "apellido" => $beneficiario->getApellidoBenef(), "nombre" => $beneficiario->getNombreBenef(), "num_doc" => $beneficiario->getNumeroDoc(), "tipo_doc" => $beneficiario->getTipoDocumento(), "fecha_nac" => $beneficiario->getFechaNacimientoBenef(), "clase_doc" => $beneficiario->getClaseDocumentoBenef(), "datos_practica" => $nomenclador->getArray(), "id_comprobante" => $comprobante->getIdComprobante(), "id_prestacion" => $comprobante->getPrestacion()->getIdPrestacion());
                            $ref = encode_link($link_planilla[1], $datos_para_link);
                            $onclick_elegir = "location.href='$ref'";
                        }

                        if ($colordefondo == '#CFE8DD') {
                            $colordefondo = '#AFE8DD';
                        } else {
                            $colordefondo = '#CFE8DD';
                        }
                        ?>
                        <tr align="center" onclick="<?php echo $onclick_elegir ?>" style="background-color: <?php echo $colordefondo ?>">
                            <td style="width: 20px"><?php echo $comprobante->getIdComprobante() ?></td>
                            <td style="width: 200px"><?php echo $beneficiario->getApellidoBenef() . " " . $beneficiario->getNombreBenef() ?></td>
                            <td style="width: 60px"><?php echo $beneficiario->getNumeroDoc() ?></td>
                            <td style="width: 150px"><?php echo str_replace("_", " ", trim($comprobante->getTipoNomenclador())) ?></td>
                            <td style="width: 100px"><?php echo trim($nomenclador->getCodigo()) . " " . trim($nomenclador->getDiagnostico()) ?></td>
                            <td align="center" style="width: 60px">$ <?php echo trim($comprobante->getPrestacion()->getTotal()) ?></td>
                            <td style="width: 80px"><?php echo $comprobante->getFechaComprobante() ?></td>
                        </tr>
                        <?php
                    }
                }
            }
        }
        ?>
    </table>
</div>
<?php
echo fin_pagina(); // aca termino ?>