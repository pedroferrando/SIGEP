<?php

include_once '../../config.php';
include_once '../../lib/bibliotecaTraeme.php';

include_once '../../clases/Prestacion.php';
include_once '../../clases/Comprobante.php';
include_once '../../clases/Nomenclador.php';
include_once '../../clases/BeneficiariosSmi.php';
include_once '../../clases/Debito.php';
include_once '../../clases/Paginador.php';

extract($_POST, EXTR_SKIP);
if ($parametros)
    extract($parametros, EXTR_OVERWRITE);

//variables para el paginador
$registrosxpagina = 50;
$paginador = new Paginador();
//$cuie_elegido = 'N03174';
//$exp_elegido = '6121-528-13';

if (!isset($ver_pagina)) {
    $ver_pagina = 1;
}

if (!is_null($nro_exp) && ($nro_exp != '')) {
    $id_usuario = $_ses_user['id'];
    $sql = "SELECT distinct(n.cuie), nombre
                FROM facturacion.factura f
		INNER JOIN nacer.efe_conv n USING (cuie)
                INNER JOIN sistema.usu_efec ue ON ue.cuie=n.cuie
                WHERE ue.id_usuario='$id_usuario' AND f.nro_exp='$nro_exp'";

    $res_efectores = sql($sql) or fin_pagina();
}

if (!is_null($cuie_elegido) && ($cuie_elegido != '-1')) {
    if ($ver_debitados == 'true') {
        $listado_practicas = PrestacionColeccion::practicasConDebitoEnExpediente($cuie_elegido, $exp_elegido, $filtro);
    } else {
        $listado_practicas = PrestacionColeccion::practicasSinDebitoEnExpediente($cuie_elegido, $exp_elegido, $filtro);
    }
    $paginador = new Paginador($listado_practicas, $registrosxpagina);
    $listado_registros = $paginador->getPagina($ver_pagina);
}

if ($quitar_debito) {
    quitarDebitoManual($quitar_debito);
}

//echo $html_header;
?>

<script src='../../lib/jquery.min.js' type='text/javascript'></script>
<link href="../../lib/css/general.css" type="text/css" rel="stylesheet">
<!--[if IE]>
        <script type="text/javascript" src="../../lib/ie.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                            hoverTabla('.tablagenerica tr');
                        }
            );
        </script>
<![endif]-->
<script>
    $(document).ready(function() {


        $('#efector_div').on('change', '#efector', function() {
            cargarTabla();
        });

        $('#debitados_chk').on('change', function() {
            var cuie_elegido = $('#efector').val();
            var exp_elegido = $('#nro_exp').val().replace(/ /g, '');
            if ((cuie_elegido != '') || (exp_elegido != '')) {
                cargarTabla();
            }
        });

        $('#filtro_txt').keypress(function(event) {
            if (event.which == 13) {
                event.preventDefault();
                var cuie_elegido = $('#efector').val();
                var exp_elegido = $('#nro_exp').val().replace(/ /g, '');
                if ((cuie_elegido != '') || (exp_elegido != '')) {

                    cargarTabla();
                }
            }
        });

        $('#filtro_btn').on('click', function() {
            var cuie_elegido = $('#efector').val();
            var exp_elegido = $('#nro_exp').val().replace(/ /g, '');
            if ((cuie_elegido != '') || (exp_elegido != '')) {
                cargarTabla();
            }
        });

        $('#btn_nro_exp').on('click', function() {
            cargarEfector();
        });

        $('#nro_exp').keypress(function(event) {
            if (event.which == 13) {
                event.preventDefault();
                cargarEfector();
            }
        });

        $("#divpaginas").delegate('li', 'click', function() {
            if ($(this).css('cursor') == 'pointer') {
                var ver_pagina = $(this).html().replace(/ /g, '');

                $('#img_load').css('display', 'block');
                $.post("listadoPracticas.php", {ver_pagina: ver_pagina, exp_elegido: $('#nro_exp').val().replace(/ /g, ''), cuie_elegido: $('#efector').val()}, function(data) {
                    $('#img_load').css('display', 'none')
                    var tablareemplazo = $(data).find("tbody");
                    $('#practicas').html(tablareemplazo);
                    var paginasreemplazo = $(data).find("#paginas");
                    $('#paginas').replaceWith(paginasreemplazo);
                });
            }
        });
    });

    function toggleDebito(e) {
        var elemento = '#debito_' + e;
        var elementohijo = $(elemento).children().css('display');
        if (elementohijo == 'none') {
            $.post("formDebitosManuales.php", {prestacion: e, nro_exp: $('#nro_exp').val().replace(/ /g, ''), cuie: $('#efector').val()}, function(data) {
                var form = '#debitoform_' + e;
                $(form).html(data);
                $(elemento).slideToggle('slow', function() {
                    $(elemento).children().toggle();
                });
            });
        } else {
            $(elemento).children().toggle();
            $(elemento).slideToggle('slow', function() {
            });
        }
    }

    function quitarDebito(e) {
        var quitar = confirm('Esta seguro que desea quitar el debito de esta prestacion');
        if (quitar) {
            $('#img_load').css('display', 'block');
            var cuie_elegido = $('#efector').val();
            var exp_elegido = $('#nro_exp').val().replace(/ /g, '');
            var ver_debitados = $('#debitados_chk').is(':checked');
            $.post("listadoPracticas.php", {cuie_elegido: cuie_elegido, exp_elegido: exp_elegido, ver_debitados: ver_debitados, quitar_debito: e}, function(data) {
                $('#img_load').css('display', 'none')
                var reemplazo = $(data).find("tbody");
                $('#practicas').html(reemplazo);
            });
        }
    }

    function cargarTabla() {
        $('#img_load').css('display', 'block');
        var cuie_elegido = $('#efector').val();
        var exp_elegido = $('#nro_exp').val().replace(/ /g, '');
        var ver_debitados = $('#debitados_chk').is(':checked');

        var filtro = new Array();
        filtro = $('#filtro_txt').val();

        $.post("listadoPracticas.php", {cuie_elegido: cuie_elegido, exp_elegido: exp_elegido, ver_debitados: ver_debitados, filtro: filtro}, function(data) {
            $('#img_load').css('display', 'none')
            var tablareemplazo = $(data).find("tbody");
            $('#practicas').html(tablareemplazo);
            var totalreemplazo = $(data).find("#total");
            $('#total').html(totalreemplazo);
            var paginasreemplazo = $(data).find("#paginas");
            $('#paginas').replaceWith(paginasreemplazo);
            if ($.browser.msie) {
                hoverTabla('.tablagenerica tr');
            }
        })
    }

    function cargarEfector() {
        $('#img_load').css('display', 'block')
        var nro_exp = $('#nro_exp').val().replace(/ /g, '');
        $.post("listadoPracticas.php", {nro_exp: nro_exp}, function(data) {
            $('#img_load').css('display', 'none')
            var reemplazo_select = $(data).find("#efector");
            $('#efector_div').html(reemplazo_select);
            var reemplazo_tabla = $(data).find("tbody");
            $('#practicas').html(reemplazo_tabla);
        });
    }
</script>

<?php

include_once 'listadoPracticas.tpl.php';
?>
