<?php

include_once '../../config.php';
include_once '../../lib/bibliotecaTraeme.php';
include_once '../../clases/Comprobante.php';
include_once '../../clases/Prestacion.php';
include_once '../../clases/Nomenclador.php';
include_once '../../clases/BeneficiariosUad.php';

extract($_POST, EXTR_SKIP);
if ($parametros)
    extract($parametros, EXTR_OVERWRITE);

if ($_POST['buscar']) {
    $listado_comprobantes = ComprobanteColeccion::Filtrar("tipo_nomenclador='TALLERES' and cuie='$cuie_elegido' and id_factura is null and marca=1 and id_comprobante in (select id_comprobante from facturacion.nomina_talleres)");
    $filas_taller = array();
    foreach ($listado_comprobantes as $comprobante) {
        $prestacion = $comprobante->getPrestacion();
        if ($prestacion) {

            $nomenclador = $prestacion->getNomenclador();
            //busca todos los beneficiarios en el taller
            $sql = "SELECT clavebeneficiario FROM facturacion.nomina_talleres
                                WHERE id_comprobante='" . $comprobante->getIdComprobante() . "'";
            $inscriptos = sql($sql);
            $beneficiario_del_comprobante = BeneficiariosUadColeccion::buscarPorClaveBeneficiario($comprobante->getClavebeneficiario());
            //cuenta cuantos activos hay
            $cont_activos = 0;
            while (!$inscriptos->EOF) {
                $beneficiario = BeneficiariosUadColeccion::buscarPorClaveBeneficiario($inscriptos->fields['clavebeneficiario']);
                if ($beneficiario->getEstadoEnPadron() == 'Activo') {
                    $cont_activos++;
                }
                $inscriptos->movenext();
            }
            $fila_taller = array();

            //Cantidad de beneficiarios activos necesarios para permitir facturar
            if ($cont_activos >= 3) {
                $fila_taller['puede_facturar'] = true;
            } else {
                $fila_taller['puede_facturar'] = false;
            }

            //busca el link a la nomina correspondiente al taller
            $link_planilla = cargaTrazadora($nomenclador->getCodigo(), $nomenclador->getDiagnostico(), $comprobante->getGrupoEtario());
            $datos_practica['grupo_precio'] = $prestacion->getGrupoEtario();
            $datos_practica['precio'] = $prestacion->getPrecioPrestacion();
            $datos_practica['id_nomenclador'] = $nomenclador->getIdNomenclador();
            $grupoetario = $beneficiario_del_comprobante->getGrupoEtareo();
            $datos_para_link = array("id_comprobante" => $comprobante->getIdComprobante(), "fecha_comprobante" => $comprobante->getFechaComprobante(), "clave_beneficiario" => $beneficiario_del_comprobante->getClaveBeneficiario(), "grupo_etareo" => $grupoetario, "datos_practica" => $datos_practica);
            $ref = encode_link($link_planilla[1], $datos_para_link);
            $onclick_elegir = "location.href = '$ref'";


            $fila_taller['nro_comprobante'] = $comprobante->getIdComprobante();
            $fila_taller['inscriptos_activos'] = $cont_activos . "/" . $inscriptos->RecordCount();
            $fila_taller['codigo'] = trim($nomenclador->getCodigo()) . " " . trim($nomenclador->getDiagnostico());
            $fila_taller['precio_prestacion'] = intval($prestacion->getPrecioPrestacion());
            $fila_taller['cantidad_horas'] = $prestacion->getCantidad();
            $fila_taller['monto_total'] = trim($comprobante->getPrestacion()->getTotal());
            $fila_taller['fecha'] = $comprobante->getFechaComprobante();

            $fila_taller['link'] = $onclick_elegir;
            //array con todas los talleres encontrados para el efector
            $filas_taller[] = $fila_taller;
        }
    }
    die(json_encode($filas_taller));
}

if ($_POST['facturar']) {
    $codigos_sin_cantidad_de_asistentes = array('T001', 'T002', 'T003');

    $comprobante = Comprobante:: getComprobantePorId($_POST['id_comprobante']);
    $prestacion = $comprobante->getPrestacion();
    $nomenclador = $prestacion->getNomenclador();
    if (!in_array($nomenclador->getCodigoTema(), $codigos_sin_cantidad_de_asistentes)) {
        $sql = "SELECT clavebeneficiario FROM facturacion.nomina_talleres
                                WHERE id_comprobante='" . $comprobante->getIdComprobante() . "'";
        $inscriptos = sql($sql);

        $cont_activos = 0;
        while (!$inscriptos->EOF) {
            $beneficiario = BeneficiariosUadColeccion::buscarPorClaveBeneficiario($inscriptos->fields['clavebeneficiario']);
            if ($beneficiario->getEstadoEnPadron() == 'Activo') {
                $cont_activos++;
            }
            $inscriptos->movenext();
        }

        $prestacion->setCantidad($cont_activos);
        $prestacion->guardarPrestacion();
    }

    $comprobante->setMarca("0");
    $comprobante->guardarComprobante();

    die("La operacion se realizo exitosamente");
}



$id_usuario = $_ses_user['id'];
if ($id_usuario)
    $sql = "select distinct(n.cuie), nombreefector, upper(trim(com_gestion)) as com_gestion 
                                                                        from nacer.efe_conv n 
                                                                        inner join nacer.conv_nom cn using(id_efe_conv)
                                                                        inner join facturacion.smiefectores s on n.cuie=s.cuie 
                                                                        inner join sistema.usu_efec ue on ue.cuie=n.cuie
                                                                        where id_usuario='$id_usuario'
                                                                        and (nom_talleres)";
else
    $sql = "select cn.id_nomenclador_detalle,s.cuie, nombreefector, upper(trim(com_gestion)) as com_gestion
                                                            from nacer.efe_conv n 
                                                            inner join nacer.conv_nom cn using(id_efe_conv)
                                                            inner join facturacion.smiefectores s on n.cuie=s.cuie
                                                            where and cn.activo = TRUE and n.activo='t' and (nom_talleres=TRUE)
                                                            order by nombreefector";

$res_efectores = sql($sql) or fin_pagina();
?>
<script src='../../lib/jquery.min.js' type='text/javascript'></script>
<link rel=stylesheet type='text/css' href='../../lib/css/general.css'>
<link rel=stylesheet type='text/css' href='../../lib/css/sprites.css'>
<script>
    $(document).ready(function() {
        buscar();
        $('select#efector').on('change', function() {
            buscar();
        });

        $('#facturartodo').on('click', function() {
            var r = confirm("Esta por marcar todos los talleres como listo para facturar. Desea Continuar?");
            if (r == true)
            {
                $('#loading').css('display', 'inline-block');
                var arraycomprobante = [];
                $("#comprobantes_taller tr.fila").each(function() {
                    arraycomprobante.push(this.cells[0].innerHTML);
                });

                $.post("listadoComprobantesTaller.php", {facturar: "facturar", id_comprobante: arraycomprobante}, function(data) {
                    $("#monitor").append(data);
                    $("#monitor").show();
                    setTimeout(function() {
                        $('#monitor').fadeOut();
                        $("#monitor").empty();
                    }, 3000);
                    buscar();
                });
            }
        });

        $('#comprobantes_taller').on('click', '.icon-signup', function() {
            var r = confirm("Esta por marcar este taller como listo para facturar. Desea Continuar?");
            if (r == true)
            {
                $('#loading').css('display', 'inline-block');
                var comprobante = $(this).parent().siblings(":first").text();
                $.post("listadoComprobantesTaller.php", {facturar: "facturar", id_comprobante: comprobante}, function(data) {
                    $("#monitor").append(data);
                    $("#monitor").show();
                    setTimeout(function() {
                        $('#monitor').fadeOut();
                        $("#monitor").empty();
                    }, 3000);
                    buscar();
                });
            }
        });
    });

    function buscar() {
        var cuie_elegido = $('#efector').val();
        $("#comprobantes_taller tr:gt(0)").remove();
        if (cuie_elegido != '-1') {
            var tabla = document.getElementById("comprobantes_taller");
            $('#loading').css('display', 'inline-block');
            //envia la peticion de busqueda para el efector seleccionado
            $.post("listadoComprobantesTaller.php", {buscar: "buscar", cuie_elegido: cuie_elegido}, function(data) {
                var con = false;
                $.each(data, function(index, val) {
                    var row = tabla.insertRow(1);
                    if (con) {
                        row.className = "fila con";
                    } else {
                        row.className = "fila sin";
                    }                     //inserta cada columna con su dato
                    var cell1 = row.insertCell(-1);
                    cell1.innerHTML = val['nro_comprobante'];
                    cell1.setAttribute('align', 'center');
                    var cell2 = row.insertCell(-1);
                    cell2.innerHTML = val['inscriptos_activos'];
                    cell2.setAttribute('align', 'center');
                    var cell3 = row.insertCell(-1);
                    cell3.innerHTML = val['codigo'];
                    cell3.setAttribute('align', 'center');
                    var cell9 = row.insertCell(-1);
                    cell9.innerHTML = "$ " + val['precio_prestacion'];
                    cell9.setAttribute('align', 'center');
                    //                    var cell4 = row.insertCell(-1);
                    //                    cell4.innerHTML = val['cantidad_horas'];
                    //                    cell4.setAttribute('align', 'center');
                    var cell5 = row.insertCell(-1);
                    cell5.innerHTML = "$ " + val['monto_total'];
                    cell5.setAttribute('align', 'center');
                    var cell6 = row.insertCell(-1);
                    cell6.innerHTML = val['fecha'];
                    cell6.setAttribute('align', 'center');
                    var cell7 = row.insertCell(-1);
                    if (val['puede_facturar']) {
                        cell7.innerHTML = "<div align='center' class='sprite-gral icon-signup'/>";
                    } else {
                        cell7.innerHTML = "-";
                    }
                    cell7.setAttribute('align', 'center');
                    var cell8 = row.insertCell(-1);
                    cell8.innerHTML = "<div align='center' class='sprite-gral icon-info'/>";
                    cell8.setAttribute('align', 'center');
                    cell8.setAttribute('onclick', val['link']);
                });
                $('#loading').css('display', 'none');
            }, 'JSON');
        }
    }
</script>

<?php include_once('./listadoComprobantesTaller.tpl.php'); ?>
