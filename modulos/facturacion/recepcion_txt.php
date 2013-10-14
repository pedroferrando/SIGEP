<?php
require_once ("../../config.php");
require_once ("../../lib/funciones_misiones.php");
echo $html_header;

$directorio_base = trim(substr(ROOT_DIR, strrpos(ROOT_DIR, chr(92)) + 1, strlen
                        (ROOT_DIR)));


if ($_POST['Guardar']) {
    $e_cod_org = $_POST["cod_org"];
    $e_no_correlativo = $_POST["no_correlativo"];
    $e_ano_exp = $_POST["ano_exp"];
    $iniciador = $_POST["cuie"];
    $fecha_entrada = Fecha_db(date($_POST['fecha_entrada']));
    $nroexpediente = $e_cod_org . "-" . $e_no_correlativo . "-" . $e_ano_exp;
    try {

        sql("BEGIN");

        insertarExpediente($nroexpediente, $iniciador, $fecha_entrada, $_ses_user['id']);
        actualizarFechaDeEntrada($nroexpediente, $fecha_entrada);

        sql("COMMIT");
    } catch (exception $e) {
        sql("ROLLBACK", "Error en rollback", 0);
    }

    $accion = 'Guardado';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <script src='../../lib/jquery.min.js' type='text/javascript'></script>
        <script src='../../lib/jquery/jquery.form.js' type='text/javascript'></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $("div#progressbar").hide();

                var options;
                options = {
                    url: 'recepcion_nuevo_txt.php', // override for form's 'action' attribute 
                    target: '#contenedorform',
                    success: function() {
                        $('#accion').empty();
                        $("form#recepcion_nuevo_txt").show();
                        $("#progressbar").hide();
                    }

                };
                $('#recepcion_nuevo_txt').ajaxForm(options);

                $('#checkarchivo:checkbox').click(function() {
                    if ($(this).is(':checked')) {
                        $('#conarchivo').css('display', 'inline');
                        $('#sinarchivo').css('display', 'none');
                        options = {
                            url: 'recepcion_nuevo_txt.php', // override for form's 'action' attribute 
                            target: '#contenedorform',
                            success: function() {
                                $('#accion').empty();
                                $("form#recepcion_nuevo_txt").show();
                                $("#progressbar").hide();
                            }
                        };

                    } else {
                        $('#sinarchivo').css('display', 'inline');
                        $('#conarchivo').css('display', 'none');
                        options = {
                            url: 'recepcion_txt.php', // override for form's 'action' attribute 
                            success: function(data) {
                                accion = $(data).filter('div#accion');
                                $('#accion').html(accion);
                            }
                        };
                    }
                    $('#recepcion_nuevo_txt').ajaxForm(options);
                });
            });



            function buscar_combo(obj)
            {
                var letra = String.fromCharCode(event.keyCode)
                if (puntero >= digitos)
                {
                    cadena = "";
                    puntero = 0;
                }
                //sino busco la cadena tipeada dentro del combo...
                else
                {
                    buffer[puntero] = letra;
                    //guardo en la posicion puntero la letra tipeada
                    cadena = cadena + buffer[puntero]; //armo una cadena con los datos que van ingresando al array
                    puntero++;

                    //barro todas las opciones que contiene el combo y las comparo la cadena...
                    //en el indice cero la opcion no es valida
                    for (var opcombo = 1; opcombo < obj.length; opcombo++) {
                        if (obj[opcombo].text.substr(0, puntero).toLowerCase() == cadena.toLowerCase()) {
                            obj.selectedIndex = opcombo;
                            break;
                        }
                    }
                }//del else de if (event.keyCode == 13)
                event.returnValue = false; //invalida la acciÛn de pulsado de tecla para evitar busqueda del primer caracter
            }//de function buscar_op_submit(obj)
            function reutiliza_recepcion(nro) {
                var obj = $("-" + nro);
                $("#ano_exp").val($("#exp_anio-" + nro).val());
                $("#no_correlativo").val($("#exp_nrocorr-" + nro).val());
                $("#cod_org").val($("#exp_codorg-" + nro).val());
                $("#fecha_entrada").val($("#exp_fecha-" + nro).val());
                //$("#cuie").val($("#cuie-"+nro).val());
                cuie = $("#exp_cuie-" + nro).val();
                $("#cuie option[value=" + cuie + "]").attr("selected", true);
            }

            function guardar() {
                var fecha_entra = document.all.fecha_entrada.value;
                if (fecha_entra.replace(/^\s+|\s+$/g, "") == "") {
                    alert('Debe completar el campo Fecha Entrada');
                    //fecha_entra.focus();
                    return false;
                }

                var cod_or = document.all.cod_org.value;
                if (cod_or.replace(/^\s+|\s+$/g, "") == "") {
                    alert('Debe completar el campo Cod. Org.');
                    //cod_or.focus();
                    return false;
                }

                var no_correlativ = document.all.no_correlativo.value;
                if (no_correlativ.replace(/^\s+|\s+$/g, "") == "") {
                    alert('Debe completar el campo Nro. Correlativo');
                    //no_correlativ.focus();
                    return false;
                }

                var ano_expe = document.all.ano_exp.value;
                if (ano_expe.replace(/^\s+|\s+$/g, "") == "") {
                    alert('Debe completar el campo AÒo Expte.');
                    //ano_expe.focus();
                    return false;
                }

                if ($('#checkarchivo:checkbox').is(':checked')) {
                    $("form#recepcion_nuevo_txt").hide();
                    $("div#progressbar").show();
                }

                return true;
            }

            //<![CDATA[
            var nav4 = window.Event ? true : false;
            function acceptNum(evt) {
                var key = nav4 ? evt.which : evt.keyCode;
                return (key <= 13 || (key >= 48 && key <= 57));
            }
            //]]>
            //Validar Fechas
            function esFechaValida(fecha) {
                var numDias;
                if ((fecha != undefined) && (fecha.value != "")) {
                    if (!/^\d{2}\/\d{2}\/\d{4}$/.test(fecha.value)) {
                        alert("formato de fecha no v√°lido (dd/mm/aaaa)");
                        fecha.focus();
                        fecha.value = ""
                        return false;
                    }
                    var dia = parseInt(fecha.value.substring(0, 2), 10);
                    var mes = parseInt(fecha.value.substring(3, 5), 10);
                    var anio = parseInt(fecha.value.substring(6), 10);

                    switch (mes) {
                        case 1:
                        case 3:
                        case 5:
                        case 7:
                        case 8:
                        case 10:
                        case 12:
                            numDias = 31;
                            break;
                        case 4:
                        case 6:
                        case 9:
                        case 11:
                            numDias = 30;
                            break;
                        case 2:
                            if (comprobarSiBisisesto(anio)) {
                                numDias = 29
                            } else {
                                numDias = 28
                            }
                            ;
                            break;
                        default:
                            alert("Fecha introducida err√≥nea");
                            fecha.focus();
                            fecha.value = ""
                            return false;
                    }

                    if (dia > numDias || dia == 0) {
                        alert("Fecha introducida err√≥nea");
                        fecha.focus();
                        fecha.value = ""
                        return false;
                    }
                    return true;
                }
            }

            function comprobarSiBisisesto(anio) {
                if ((anio % 100 != 0) && ((anio % 4 == 0) || (anio % 400 == 0))) {
                    return true;
                }
                else {
                    return false;
                }
            }
            /**********************************************************/
            var patron = new Array(2, 2, 4)
            var patron2 = new Array(5, 16)
            function mascara(d, sep, pat, nums) {
                if (d.valant != d.value) {
                    val = d.value
                    largo = val.length
                    val = val.split(sep)
                    val2 = ''
                    for (r = 0; r < val.length; r++) {
                        val2 += val[r]
                    }
                    if (nums) {
                        for (z = 0; z < val2.length; z++) {
                            if (isNaN(val2.charAt(z))) {
                                letra = new RegExp(val2.charAt(z), "g")
                                val2 = val2.replace(letra, "")
                            }
                        }
                    }
                    val = ''
                    val3 = new Array()
                    for (s = 0; s < pat.length; s++) {
                        val3[s] = val2.substring(0, pat[s])
                        val2 = val2.substr(pat[s])
                    }
                    for (q = 0; q < val3.length; q++) {
                        if (q == 0) {
                            val = val3[q]

                        }
                        else {
                            if (val3[q] != "") {
                                val += sep + val3[q]
                            }
                        }
                    }
                    d.value = val
                    d.valant = val
                }
            }
        </script>
        <style type="text/css">
            /*<![CDATA[*/
            input.c3 {width:340px}
            div.c2 {text-align: center}
            div.c1 {font-weight: bold; text-align: left}
            /*]]>*/
        </style>
        <style type="text/css">
            /*<![CDATA[*/
            td.c1 {padding: 5px;font-size: 14px;}
            /*]]>*/
        </style>
    </head>

    <body>
        <br />
        <br />

        <div id='accion' style="width: 60px ;margin: 0 auto 0 auto;padding-bottom: 5px;color: red; font-size: medium; font-weight: bold;text-align: center">
            <? if ($accion) echo $accion; ?>
        </div>

        <form id="recepcion_nuevo_txt" name='recepcion_nuevo_txt' method="post" accept-charset=utf-8 enctype='multipart/form-data'>

            <table id='contenedorform' width="469" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td id ="mo" align="center" class="c1">
                        <b>Efector Iniciador</b>
                    </td>
                </tr>
                <tr>
                    <td  align="center" class="c1">			 	
                        <select name=cuie id ="cuie" Style="width:450px" 
                                onKeypress="buscar_combo(this);"
                                onblur="borrar_buffer();"
                                onchange="borrar_buffer();"
                                <?
                                echo"<option value=-1>Seleccione</option>";
                                $sql = "select cuie, nombreefector
                                    from facturacion.smiefectores
                                    order by nombreefector";

                                $res_efectores = sql($sql) or fin_pagina();
                                while (!$res_efectores->EOF) {
                                    $cuiel = $res_efectores->fields['cuie'];
                                    $nombre_efector = $res_efectores->fields['nombreefector'];
                                    ?>
                                    <option value='<?= $cuiel ?>' Style="background-color: <?= $color_style ?>;"><?= $cuiel . " - " . $nombre_efector ?></option>
                                        <?
                                        $res_efectores->movenext();
                                    }
                                    ?>
                        </select>

                    </td>
                </tr>
                <tr>
                    <td id="mo" align="center" class="c1">N&ordm; Expediente</td>
                </tr>

                <tr>
                    <td align="center"><br />
                        (Cod. de Org. - N&ordm; Correlativo - A&ntilde;o)
                        <br />
                        <input type="text" id="cod_org" name="cod_org" size="4" maxlength="5" onkeypress="return acceptNum(event)" value="<?php echo $cod; ?>" /> 
                        - <input type="text" id="no_correlativo" name="no_correlativo" size="4" maxlength="5" onkeypress="return acceptNum(event)" value="<?php echo $correl; ?>" /> 
                        - <input type="text" id="ano_exp" name="ano_exp" size="4" maxlength="4" onkeypress="return acceptNum(event)" value="<?php echo $ano_exp1; ?>" />
                        <br />
                        <br />
                    </td>                
                </tr>

                <tr>
                    <td id="mo" align="center" class="c1">Fecha de Mesa de Entrada</td>
                </tr>

                <tr>
                    <td align="center"><br />
                        <br />
                        <input type="text" name="fecha_entrada" id="fecha_entrada" size="15" maxlength="10" onKeyUp="mascara(this, '/', patron, true);" onblur="esFechaValida(this);"/>&nbsp;&nbsp;&nbsp;
                        <?= link_calendario('fecha_entrada'); ?>

                        <br />

                        <br /></td>
                    <tr><td align="center"><label Style="font-size:0.8em;color: red">*Esta fecha se encuentra en el mismo expediente. (Ver Sello)</label></td></tr>
                </tr>

                <tr>
                    <td Style="margin-top:10px" id="mo" align="center" class="c1">Archivo &nbsp;<input id='checkarchivo' value='t' type="checkbox" checked/></td>
                </tr>

                <tr>
                    <td align="center" id="mo" class="c1">
                        <div id='sinarchivo' style="display: none">
                            <input type="submit" name="Guardar" value="Guardar" onclick='return guardar()' size="160px"/>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div id='conarchivo'>
                            <table width="469">
                                <tr>
                                    <td align="center"><br />
                                        <br />
                                        <input id='archivo' size="50" type="file" name="archivo" class="c3" />
                                        <br />
                                        <br />
                                    </td>
                                </tr>

                                <tr>
                                    <td align="center" id="mo" class="c1">
                                        <input type="submit" name="Enviar" value="Enviar" onclick="return guardar()" size="160px"/>

                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>

            </table>

        </form>

        <?php
        $e_sql = "SELECT distinct(e.nro_exp),e.iniciador,ano_exp,no_correlativo,cod_org,f.fecha_entrada,fecha_carga
                FROM facturacion.factura f
                INNER JOIN facturacion.recepcion r on f.recepcion_id=r.idrecepcion
                INNER JOIN facturacion.expediente e on f.nro_exp = e.nro_exp
                WHERE fecha_carga>CURRENT_DATE
                order by fecha_carga desc";

        $ultimafacturarecepcionada = sql($e_sql, "Error al buscar la ultima factura recepcionada.", 0) or excepcion('Error al buscar la ultima factrua recepcionada.');
        ?>

        <div style=" margin: 0 auto; text-align: center; padding-top: 10px">

            <?php
            if ($ultimafacturarecepcionada->RecordCount() > 0) {
                echo "Expedientes recepcionados hoy:";
                $ultimafacturarecepcionada->MoveFirst();
                $recepcionada = 1;
                while (!$ultimafacturarecepcionada->EOF) {
                    $elexpedienteanterior = $ultimafacturarecepcionada->fields['iniciador'] . " - " . $ultimafacturarecepcionada->fields['nro_exp'];
                    $fechadelexpediente = split("-", $ultimafacturarecepcionada->fields['fecha_entrada']);
                    $fechadelexpediente = $fechadelexpediente[2] . "/" . $fechadelexpediente[1] . "/" . $fechadelexpediente[0];
                    ?>
                    <input id="exp_anio<?= "-" . $recepcionada ?>" type="hidden" value="<?= $ultimafacturarecepcionada->fields['ano_exp'] ?>"></input>
                    <input id="exp_nrocorr<?= "-" . $recepcionada ?>" type="hidden" value="<?= $ultimafacturarecepcionada->fields['no_correlativo'] ?>"></input>
                    <input id="exp_fecha<?= "-" . $recepcionada ?>" type="hidden" value="<?= $fechadelexpediente ?>"></input>
                    <input id="exp_codorg<?= "-" . $recepcionada ?>" type="hidden" value="<?= $ultimafacturarecepcionada->fields['cod_org'] ?>"></input>
                    <input id="exp_cuie<?= "-" . $recepcionada ?>" type="hidden" value="<?= $ultimafacturarecepcionada->fields['cuie'] ?>"></input>
                    <div id="ultimosexp"   onclick="return reutiliza_recepcion(<?= "'" . $recepcionada . "'" ?>)"  style="margin:0 auto;width:469px; background-color: #DBEBC1; cursor: default; margin-top: 5px;"><?= $elexpedienteanterior ?></div>
                    <?php
                    $recepcionada++;
                    $ultimafacturarecepcionada->MoveNext();
                }
            }
            ?>
        </div>

        </form>        
        <div align="center" style="width: 25%;margin: auto">
            <div id="progressbar" style="margin: auto">
                <p>Procesando el archivo, esto puede demorar varios minutos...</p>
                <img src="../../imagenes/wait.gif" alt="loading.." />
            </div>
        </div>
        <?
        fin_pagina();
        ?>
    </body>
</html>
