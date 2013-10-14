<?php
$img_ext = '../../imagenes/rigth2.gif';
$img_quitar = '../../imagenes/salir2.gif';
$sepuedeestimular = '';
$sumatotaldedebitos = 0;
unset($prac_total);
unset($montofactura);
unset($totaldebitado);
unset($paraefector);
unset($deb_total);
unset($estimulacion);
unset($total_liq);
$fa = 0; //contador de facturas abiertas

$fecha_hoy = date("Y-m-d H:i:s");
$usuario = $_ses_user['id'];


$expediente = str_replace(" ", "", $_POST['expedientebuscado']);
$expedienteid = buscarIDExpediente($expediente);

$efectores = buscarEfectoresEntrePersistidos($expedienteid);


$elexpediente = trim($_POST['expedientebuscado']);

echo $html_header;
echo "<script src='../../lib/jquery.min.js' type='text/javascript'></script>";
?>

<style>
    .sprite { background: url('../../lib/css/sprites/sprite_expediente.png') no-repeat top left; width: 32px; height: 32px;  } 
    .sprite.billete {float:left;padding-left: 5px;background-position: 0px 0px;  } 
    .sprite.billete-c {float:left;padding-left: 5px; background-position: 0px -42px;  } 
    .sprite.billete-d {float:left;padding-left: 5px; background-position: 0px -84px;  } 
    .sprite.pdf_logo {float:left; padding-left: 5px;background-position: 0px -126px;  } 
</style>
<script>
    var img_ext = '<?= $img_ext = '../../imagenes/rigth2.gif' ?>';//imagen extendido
    var img_cont = '<?= $img_cont = '../../imagenes/down2.gif' ?>';//imagen contraido
    function muestra_tabla_facturas(nro) {
        oimg = $("#imagen_ver_factura" + nro);//objeto tipo IMG

        var obj = $(".efector_" + nro);
        var otroobj = $("#totalapagar_" + nro);
        if (obj.css("display") == 'none') {
            obj.css("display", "inline-table");
            otroobj.css("display", "inline-table");
            oimg.attr('src', img_cont);

        } else {
            obj.css("display", "none");
            otroobj.css("display", "none");
            oimg.attr('src', img_ext);
        }
    }

    function muestra_tabla_practicas(nro) {
        oimg = $("#imagen_ver_practicas" + nro);//objeto tipo IMG

        var obj = $(".factura_" + nro);
        if (obj.css("display") == 'none') {
            obj.css("display", "inline-table");
            // $("#imagen_ver_"+nro)
            oimg.attr('src', img_cont);

        } else {
            obj.css("display", "none");
            // $("#imagen_ver_"+nro)
            oimg.attr('src', img_ext);
        }
    }

</script>

<?php include "expediente_persistido.tpl.php" ?>