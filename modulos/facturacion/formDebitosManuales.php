<?php
include_once '../../config.php';
include_once '../../lib/funciones_misiones.php';

extract($_POST, EXTR_SKIP);

$ref = encode_link("listadoPracticas.php", array("nro_exp" => $nro_exp, "cuie_elegido" => $cuie, "exp_elegido" => $nro_exp));

//inserta el debito en las tablas
if (!is_null($debitarprestacion)) {
    sql("BEGIN");

    $sqlpractica = "SELECT cantidad,precio_prestacion,id_nomenclador,
                        codigo,diagnostico,id_prestacion,id_comprobante,
                        id_factura,apellido_benef,nombre_benef,numero_doc
                        FROM facturacion.prestacion
                        INNER JOIN facturacion.comprobante using(id_comprobante)
                        INNER JOIN facturacion.nomenclador using (id_nomenclador)
                        INNER JOIN uad.beneficiarios on(clave_beneficiario=clavebeneficiario)
                        WHERE id_prestacion='$debitarprestacion'";
    $result = sql($sqlpractica);

    $datosdebito['id_factura'] = $result->fields['id_factura'];
    $datosdebito['id_comprobante'] = $result->fields['id_comprobante'];
    $datosdebito['id_nomenclador'] = $result->fields['id_nomenclador'];
    $datosdebito['cantidad'] = $result->fields['cantidad'];
    $datosdebito['id_debito'] = 82;
    $datosdebito['monto_deb'] = $result->fields['precio_prestacion'];
    $datosdebito['documento_deb'] = $result->fields['numero_doc'];
    $datosdebito['apellido_deb'] = $result->fields['apellido_benef'];
    $datosdebito['nombre_deb'] = $result->fields['nombre_benef'];
    $datosdebito['codigo_deb'] = $result->fields['codigo'] . " " . $result->fields['diagnostico'];
    $datosdebito['observaciones_deb'] = '';
    $datosdebito['msj_debito'] = 'Auditoria Medica[' . $msj_motivo . ']';
    $datosdebito['idprestacion'] = $result->fields['id_prestacion'];

    $id_debito = debitarPrestacion($datosdebito);

    $datosdebitoauditoria['id_debito'] = $id_debito;
    $datosdebitoauditoria['id_motivo'] = $id_motivo;
    $datosdebitoauditoria['observaciones'] = $observaciones;
    $datosdebitoauditoria['usuario'] = $_ses_user['id'];
    $datosdebitoauditoria['fechahora'] = date("Y-m-d H:i:s");

    insertarDebitoManual($datosdebitoauditoria);
    sql("COMMIT");

    fin_pagina();
}

//inserta el nuevo motivo de debito x auditoria
if (!is_null($nuevomotivo)) {
    $sql_nuevomotivo = "INSERT INTO facturacion.motivos_auditoria(descripcion)
                        VALUES('$nuevomotivo')";
    sql($sql_nuevomotivo);
}

$motivos_sql = "SELECT * FROM facturacion.motivos_auditoria";
$res_motivos = sql($motivos_sql);
?>
<script>
    $(document).ready(function() {
        var e =<?php echo $prestacion ?>;

        var form = '#debitoform_' + e;

        var btn_aceptar = '#btn_aceptar_' + e;
        var btn_cancelar = '#btn_cancelar_' + e;
        var btn_masmotivo = '#masmotivo_' + e;
        var txt_nuevomotivo = '#nuevomotivo_' + e;
        var btn_cancelarmotivo = '#cancelarmotivo_' + e;
        var btn_confirmarmotivo = '#confirmarmotivo_' + e;
        var sel_motivo = '#motivo_' + e;
        var loading = '#loading_' + e;

        $(btn_cancelar).on('click', function() {
            var elemento = '#debito_' + e;
            $(elemento).children().toggle();
            $(elemento).slideToggle('slow', function() {
            });
        });

        $(btn_aceptar).on('click', function() {
            var id_motivo = $(sel_motivo).val();
            var observaciones = $('#observaciones').val();
            var msj_motivo = $(sel_motivo + " option:selected").text();

            if (id_motivo < 0) {
                alert("Debe seleccionar un Motivo de la lista");
            } else {
                $.post("formDebitosManuales.php", {debitarprestacion: e, msj_motivo: msj_motivo, id_motivo: id_motivo, observaciones: observaciones}, function() {
                    document.location = '<?php echo $ref ?>';
                });
            }
        });

        $(btn_masmotivo).on('click', function() {
            $(txt_nuevomotivo).toggle();
            $(btn_confirmarmotivo).toggle();
            $(btn_cancelarmotivo).toggle();
            $(btn_masmotivo).toggle();
            $(sel_motivo).toggle();
            $(txt_nuevomotivo).focus();
        });

        $(btn_cancelarmotivo).on('click', function() {
            $(txt_nuevomotivo).toggle();
            $(txt_nuevomotivo).val('');
            $(btn_confirmarmotivo).toggle();
            $(btn_cancelarmotivo).toggle();
            $(btn_masmotivo).toggle();
            $(sel_motivo).toggle();
        });

        $(btn_confirmarmotivo).on('click', function() {
            var nuevomotivo = $(txt_nuevomotivo).val();
            if (nuevomotivo != '' && nuevomotivo != 'undefined') {
                $(btn_confirmarmotivo).toggle();
                $(btn_cancelarmotivo).toggle();
                $(loading).toggle();
                $.post("formDebitosManuales.php", {prestacion: e, nuevomotivo: nuevomotivo}, function(data) {
                    $(txt_nuevomotivo).val('');
                    var reemplazo = $(data).find('select');
                    $(sel_motivo).replaceWith(reemplazo);
                    $(loading).toggle();
                    $(btn_masmotivo).toggle();
                    $(txt_nuevomotivo).toggle();
                });
            } else {
                alert('Debe ingresar una descripcion para el nuevo motivo');
            }
        });
    });
</script>
<?php
include_once 'formDebitosManuales.tpl.php';
?>

