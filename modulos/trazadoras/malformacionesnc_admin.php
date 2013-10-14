<?
require_once ("../../config.php");
require_once ("../../lib/bibliotecaTraeme.php");

extract($_POST, EXTR_SKIP);
if ($parametros)
    extract($parametros, EXTR_OVERWRITE);

echo $html_header;
echo "<script src='../../lib/jquery.min.js' type='text/javascript'></script>";
echo "<script src='../../lib/jquery/jquery-ui.js' type='text/javascript'></script>";
echo "<link rel='stylesheet' href='../../lib/jquery/ui/jquery-ui.css'/>";
echo "<script src='../../lib/jquery/ui/jquery.ui.datepicker-es.js' type='text/javascript'></script>";

$fecha_control = $fecha_comprobante;



if ($_POST['guardar'] == "Actualizar Planilla") {
    $fecha_carga = date("Y-m-d H:m:s");
    $usuario = $_ses_user['id'];
    $db->StartTrans();

    $fecha_control = Fecha_db($_POST['fecha_control']);

    $codnomenclador = $codigo . " " . $diagnostico;

    $query = "UPDATE facturacion.detalles_perinatal
               SET cantidad='$dias_pre',monto='$importe_dias_pre'
             WHERE id_comprobante='$id_comprobante' and id_concepto='8'";

    sql($query, "Error al insertar la Planilla") or fin_pagina();

    if ($importe_acto_quiru)
        $acto_quiru = 1;
    else
        $acto_quiru = 0;

    $query = "UPDATE facturacion.detalles_perinatal
               SET cantidad='$acto_quiru',monto='$importe_acto_quiru'
             WHERE id_comprobante='$id_comprobante' and id_concepto='9'";

    sql($query, "Error al insertar la Planilla") or fin_pagina();

    $query = "UPDATE facturacion.detalles_perinatal
               SET cantidad='$dias_uti',monto='$importe_dias_uti'
             WHERE id_comprobante='$id_comprobante' and id_concepto='10'";

    sql($query, "Error al insertar la Planilla") or fin_pagina();

    $query = "UPDATE facturacion.detalles_perinatal
               SET cantidad='$dias_intermedio',monto='$importe_dias_intermedio'
             WHERE id_comprobante='$id_comprobante' and id_concepto='11'";

    sql($query, "Error al insertar la Planilla") or fin_pagina();

    actualizarPrestacion($id_comprobante, $importe_total);

    guardarAlta(Fecha_db($fecha_alta), $id_comprobante, $id_prestacion, $usuario);

    $accion = "Se Actualizo la Prestacion: " . $codigo . " " . $diagnostico;

    $db->CompleteTrans();
}

if ($_POST['guardar'] == "Guardar Planilla") {
    $fecha_carga = date("Y-m-d H:m:s");
    $usuario = $_ses_user['id'];
    $db->StartTrans();

    $fecha_control = Fecha_db($_POST['fecha_control']);

    $codnomenclador = $codigo . " " . $diagnostico;

    $id_prestacion = guardarPrestacion($id_comprobante, $id_nomenclador, '1', $importe_total);

    $query = "INSERT INTO facturacion.detalles_perinatal
             (id_nomenclador,clavebeneficiario,monto,cantidad,id_concepto,id_comprobante,id_prestacion)
             VALUES
             ($id_nomenclador,'$clave_beneficiario',$importe_dias_pre,$dias_pre,8,$id_comprobante,$id_prestacion)";

    sql($query, "Error al insertar la Planilla") or fin_pagina();

    if ($importe_acto_quiru)
        $acto_quiru = 1;
    else
        $acto_quiru = 0;

    $query = "INSERT INTO facturacion.detalles_perinatal
             (id_nomenclador,clavebeneficiario,monto,cantidad,id_concepto,id_comprobante,id_prestacion)
             VALUES
             ($id_nomenclador,'$clave_beneficiario',$importe_acto_quiru,$acto_quiru,9,$id_comprobante,$id_prestacion)";

    sql($query, "Error al insertar la Planilla") or fin_pagina();

    $query = "INSERT INTO facturacion.detalles_perinatal
             (id_nomenclador,clavebeneficiario,monto,cantidad,id_concepto,id_comprobante,id_prestacion)
             VALUES
             ($id_nomenclador,'$clave_beneficiario',$importe_dias_uti,$dias_uti,10,$id_comprobante,$id_prestacion)";

    sql($query, "Error al insertar la Planilla") or fin_pagina();

    $query = "INSERT INTO facturacion.detalles_perinatal
             (id_nomenclador,clavebeneficiario,monto,cantidad,id_concepto,id_comprobante,id_prestacion)
             VALUES
             ($id_nomenclador,'$clave_beneficiario',$importe_dias_intermedio,$dias_intermedio,11,$id_comprobante,$id_prestacion)";

    sql($query, "Error al insertar la Planilla") or fin_pagina();

    guardarAlta(Fecha_db($fecha_alta), $id_comprobante, $id_prestacion, $usuario);

    mandarMailSoloPerinatal($id_prestacion);

    $accion = "Se registro la Prestacion: " . $codigo . " " . $diagnostico;

    $db->CompleteTrans();
    ?>
    <script>
        $('#titulo', window.opener.document).text('<?= $accion ?>');
        $("#categoria", window.opener.document).val('-1');
        if (window.opener && !window.opener.closed) {
    <?php $ref = encode_link("../facturacion/comprobante_admin_total.php", array("clavebeneficiario" => $clave_beneficiario)); ?>

            window.opener.location.href = '<?php echo $ref ?>';
        }
        self.close();
    </script>
    <?
}//de if 
//consulta si ya existen valores de internacion para esta practica
$sql = "SELECT * FROM facturacion.detalles_perinatal
        WHERE id_comprobante='$id_comprobante'
        ORDER BY id_concepto asc";
$resulta = sql($sql);

if (!$resulta->EOF) {
    //carga los valores de internacion cargados anteriormente
    $estabacargado = true;
    $dias_pre = $resulta->fields['cantidad'];
    $resulta->movenext();
    if ($resulta->fields['cantidad'] == "1") {
        $check_acto_quiru = 'checked';
    }
    $resulta->movenext();
    $dias_uti = $resulta->fields['cantidad'];
    $resulta->movenext();
    $dias_intermedio = $resulta->fields['cantidad'];

    //consulta la fecha de alta
    $sql_fecha_alta = "SELECT * FROM facturacion.fecha_de_alta
            WHERE id_comprobante=$id_comprobante
            AND id_prestacion=$id_prestacion";
    $result_fecha_alta = sql($sql_fecha_alta);
    if (!$result_fecha_alta->EOF) {
        $fecha_alta = Fecha($result_fecha_alta->fields['fecha_alta']);
    } else {
        $fecha_alta = "";
    }
} else {
    $estabacargado = false;
    $dias_pre = 0;
    $check_acto_quiru = '';
    $dias_uti = 0;
    $dias_intermedio = 0;
}

if ($datos_practica) {
    $id_nomenclador = $datos_practica['id_nomenclador'];
    $codigo = $datos_practica['codigo'];
    $diagnostico = $datos_practica['diagnostico'];
}
$valoraciones_quirurjicas = buscarValoracionesQuirurjicas($id_nomenclador, $grupo_etareo);


echo $html_header;
?>
<script>
    var concepto1 =<? echo $valoraciones_quirurjicas->fields['precio']; ?>;
    var maximo1 =<?
echo $maximo1 = $valoraciones_quirurjicas->fields['maximo_dias'];
$valoraciones_quirurjicas->movenext();
?>;
    var concepto2 =<?
echo $valoraciones_quirurjicas->fields['precio'];
$valoraciones_quirurjicas->movenext();
?>;
    var concepto3 =<? echo $valoraciones_quirurjicas->fields['precio']; ?>;
    var maximo3 =<?
echo $maximo3 = $valoraciones_quirurjicas->fields['maximo_dias'];
$valoraciones_quirurjicas->movenext();
?>;
    var concepto4 =<? echo $valoraciones_quirurjicas->fields['precio']; ?>;
    var maximo4 =<? echo $maximo4 = $valoraciones_quirurjicas->fields['maximo_dias']; ?>;

    $(document).ready(function() {
        $('#fecha_alta').datepicker({maxDate: "+0D"});
        calcularTotal();
        $('.form_quirurgico').on('focusout', function() {
            if (($('#dias_pre').val() > maximo1) || ($('#dias_uti').val() > maximo3) || ($('#dias_intermedio').val() > maximo4)) {
                alert('El valor ingresado supera el Maximo');
                if ($('#dias_pre').val() > maximo1) {
                    $('#dias_pre').val(0);
                    $('#importe_dias_pre').val(0);
                }
                if ($('#dias_uti').val() > maximo3) {
                    $('#dias_uti').val(0);
                    $('#importe_dias_uti').val(0);
                }
                if ($('#dias_intermedio').val() > maximo4) {
                    $('#dias_intermedio').val(0);
                    $('#importe_dias_intermedio').val(0);
                }
                $('#importe_total').val(0);
            } else {
                calcularTotal();
            }

        });
        $('#guardar').on('click', function() {
            calcularTotal();
        });

    });


    function calcularTotal() {
        var importe_dias_pre = $('#dias_pre').val() * concepto1;
        if ($('#acto_quiru:checkbox').is(':checked')) {
            var importe_acto_quiru = concepto2;
        } else {
            var importe_acto_quiru = 0;
        }
        var importe_dias_uti = $('#dias_uti').val() * concepto3;
        var importe_dias_intermedio = $('#dias_intermedio').val() * concepto4;

        $('#importe_dias_pre').val(importe_dias_pre);
        $('#importe_acto_quiru').val(importe_acto_quiru);
        $('#importe_dias_uti').val(importe_dias_uti);
        $('#importe_dias_intermedio').val(importe_dias_intermedio);
        $('#importe_total').val(importe_dias_pre + importe_acto_quiru + importe_dias_uti + importe_dias_intermedio);
    }

    var nav4 = window.Event ? true : false;
    function acceptNum(evt) {
        var key = nav4 ? evt.which : evt.keyCode;
        return (key < 13 || (key >= 48 && key <= 57));
    }


    //controlan que ingresen todos los datos necesarios par el muleto
    function control_nuevos()
    {
        if (document.all.cuiel.value == "-1") {
            alert('Debe Seleccionar un Efector');
            return false;
        }
        if (document.all.tipo_doc.value == "-1") {
            alert('Debe Seleccionar un Tipo de Documento');
            return false;
        }
        if (document.all.num_doc.value == "") {
            alert('Debe Ingresar un Documento');
            return false;
        }
        if (document.all.apellido.value == "") {
            alert('Debe Ingresar un apellido');
            return false;
        }
        if (document.all.nombre.value == "") {
            alert('Debe Ingresar un nombre');
            return false;
        }
        if (document.all.fecha_control.value == "") {
            alert('Debe Ingresar una Fecha de Control');
            return false;
        }
        if ((document.all.importe_total.value == "") || (document.all.importe_total.value == "0")) {
            alert('No se ingresaron Valores');
            return false;
        }
        if ($('#fecha_alta').val() != "") {
            if (esFechaValida($('#fecha_alta').val())) {

                var fechaaltasplit = $('#fecha_alta').val().split("/");
                var fechacontrolsplit = $('#fecha_control').val().split("/");

                var fecha_alta = new Date(fechaaltasplit[2], fechaaltasplit[1], fechaaltasplit[0]);
                var fecha_control = new Date(fechacontrolsplit[2], fechacontrolsplit[1], fechacontrolsplit[0]);

                if (fecha_alta < fecha_control) {
                    alert('La Fecha de Alta debe ser posterior a la Prestacion');
                    return false;
                }
            } else {
                return false;
            }
        }
    }//de function control_nuevos()

    function editar_campos()
    {
        document.all.cuiel.disabled = false;
        document.all.tipo_doc.disabled = false;
        document.all.num_doc.readOnly = false;
        document.all.apellido.readOnly = false;
        document.all.nombre.readOnly = false;
        document.all.sem_gestacion.readOnly = false;
        document.all.observaciones.readOnly = false;
        document.all.fecha_control.readOnly = false;
        document.all.fum.readOnly = false;
        document.all.fpp.readOnly = false;
        document.all.fpcp.readOnly = false;

        document.all.cancelar_editar.disabled = false;
        document.all.guardar_editar.disabled = false;
        document.all.editar.disabled = true;
        return true;
    }//de function control_nuevos()

    /**********************************************************/
    //funciones para busqueda abreviada utilizando teclas en la lista que muestra los clientes.
    var digitos = 10; //cantidad de digitos buscados
    var puntero = 0;
    var buffer = new Array(digitos); //declaración del array Buffer
    var cadena = "";

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
        event.returnValue = false; //invalida la acción de pulsado de tecla para evitar busqueda del primer caracter
    }//de function buscar_op_submit(obj)

    //Validar Fechas
    function esFechaValida(fecha) {
        if (fecha != undefined && fecha != "") {
            if (!/^\d{2}\/\d{2}\/\d{4}$/.test(fecha)) {
                alert("formato de fecha no válido (dd/mm/aaaa)");
                return false;
            }
            var dia = parseInt(fecha.substring(0, 2), 10);
            var mes = parseInt(fecha.substring(3, 5), 10);
            var anio = parseInt(fecha.substring(6), 10);
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
                    alert("Fecha introducida errónea");
                    return false;
            }

            if (dia > numDias || dia == 0) {
                alert("Fecha introducida errónea");
                return false;
            }
            return true;
        }
        return false;
    }

    function comprobarSiBisisesto(anio) {
        if ((anio % 100 != 0) && ((anio % 4 == 0) || (anio % 400 == 0))) {
            return true;
        }
        else {
            return false;
        }
    }
</script>
<form name='form1' action='malformacionesnc_admin.php' method='POST'>

    <input type="hidden" value="<?= $id_comprobante ?>" name="id_comprobante">
    <input type="hidden" value="<?= $id_prestacion ?>" name="id_prestacion">
    <input type="hidden" value="<?= $id_nomenclador ?>" name="id_nomenclador">
    <input type="hidden" value="<?= $codigo ?>" name="codigo">
    <input type="hidden" value="<?= $diagnostico ?>" name="diagnostico">
    <input type="hidden" value="<?= $fecha_comprobante ?>" name="fecha_prestacion">
    <input type="hidden" value="<?= $grupo_etareo ?>" name="grupo_etareo">


    <input type="hidden" value="<?= $id_planilla ?>" name="id_planilla">
    <input type="hidden" value="<?= $pagina ?>" name="pagina">
    <? echo "<center><b><font size='+1' color='red'>$accion</font></b></center>"; ?>
    <? echo "<center><b><font size='+1' color='Blue'>$accion2</font></b></center>"; ?>
    <table style="margin-top: 10px" width="85%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?= $bgcolor_out ?>' class="bordes">
        <tr id="mo">
            <td>
                <?
                if (!$id_planilla) {
                    ?>  
                    <font size=+1><b>Nuevo Dato</b></font>   
                    <?
                } else {
                    ?>
                    <font size=+1><b>Dato</b></font>   
                <? } ?>
                Malformaciones Quirurgicas NC <br> <?php echo "Prestacion: " . $codigo . " " . $diagnostico ?>
            </td>
        </tr>
        <tr><td>
                <table width=90% align="center" class="bordes">
                    <tr>
                        <td id=mo colspan="2">
                            <b> Descripción de la PLANILLA</b></td>
                    </tr>
                    <tr>
                        <td>
                            <table style="margin: 0 auto 0 auto;">

                                <tr>	           
                                    <td align="center" colspan="4">
                                        <b> Número del Dato: <font size="+1" color="Red"><?= ($id_planilla) ? $id_planilla : "Nuevo Dato" ?></font> </b>
                                    </td>
                                </tr>

                                <tr>	           
                                    <td align="center" colspan="4">
                                        <b><font size="2" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>
                                    </td>
                                </tr>

                                <tr>
                                    <td align="right" >
                                        <b>Número de Documento:</b>
                                    </td>         	
                                    <td align='left' >
                                        <input type="text" size="10" value="<?= $num_doc ?>" name="num_doc" <? echo "readonly" ?>>
                                    </td>
                                    <td align="left">
                                        <b>Tipo de Documento:</b>			
                                    </td>
                                    <td align="left">			 	
                                        <input type="text" size="10" value="<?= $tipo_doc ?>" name="tipo_doc" <? echo "readonly" ?>>     
                                    </td>
                                </tr> 

                                <tr>
                                    <td align="right">
                                        <b>Efector:</b>
                                    </td>
                                    <td align="left">
                                        <input size="10" name=cuiel value="<?= $cuiel ?>" <? echo "readonly" ?>/>                                                        
                                    </td>
                                    <td align="right">
                                        <b>Clave Beneficiario:</b>         	
                                    </td>         	
                                    <td align='left'>
                                        <input type="text" size="20" value="<?= $clave_beneficiario ?>" name="clave_beneficiario" <? echo "readonly" ?>>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <b>Edad:</b>			
                                    </td>

                                    <td align="left">			 	
                                        <input size="10" name=edad value="<?= $edad ?>" <? echo "readonly" ?>/>                                        		
                                    </td>
                                    <td align="right">
                                        <b>Sexo:</b>			
                                    </td>

                                    <td align="left">
                                        <?
                                        if (($sexo == 'M') || ($sexo == 'Masculino')) {
                                            $sexo_1 = 'Masculino';
                                            $sexo = 'M';
                                        }
                                        if (($sexo == 'F') || ($sexo == 'Femenino')) {
                                            $sexo_1 = 'Femenino';
                                            $sexo = 'F';
                                        }
                                        ?>
                                        <input type="hidden" value="<?= $sexo ?>" name="sexo">
                                        <input size="10" name=nino_sexo value="<?= $sexo_1 ?>" <? echo "readonly" ?>/>                                        		
                                    </td>
                                </tr>
                    </tr>

                    <tr>
                        <td align="right">
                            <b>Apellido:</b>
                        </td>         	
                        <td align='left'>
                            <input type="text" size="20" value="<?= $apellido ?>" name="apellido" <? echo "readonly" ?>>
                        </td>
                        <td align="right">
                            <b>Nombre:</b>
                        </td>         	
                        <td align='left'>
                            <input type="text" size="20" value="<?= $nombre ?>" name="nombre" <? echo "readonly" ?>>
                        </td>
                    </tr>                     

                    <tr>
                        <td colspan='4'>
                            <table style="width:760px; ;margin: 0 auto 0 auto;padding-top: 5px;border:thin black solid">    
                                <tr>
                                    <td align="right">
                                        <b>Dias Pre-Quirurgico:</b>
                                    </td>         	
                                    <td align='left'>
                                        <input class="form_quirurgico" type="text" size="8" value="<?php echo $dias_pre ?>" id="dias_pre" name="dias_pre" maxlength="2" onKeyPress="return acceptNum(event)">(Maximo = <?= $maximo1 ?>)
                                    </td>
                                    <td align="right" style="padding-left: 10px">
                                        <b>Importe:</b>
                                    </td>         	
                                    <td align='left'>
                                        <input type="text" size="10" value="0" id="importe_dias_pre" name="importe_dias_pre" <? echo "readonly" ?>>
                                    </td>
                                </tr> 

                                <tr>
                                    <td align="right">
                                        <b>Acto Quirurgico:</b>
                                    </td>         	
                                    <td align='left'>
                                        <input class="form_quirurgico" type="checkbox" value="" id="acto_quiru" name="acto_quiru" onKeyPress="return acceptNum(event)" <?php echo $check_acto_quiru ?>>
                                    </td>
                                    <td align="right" style="padding-left: 10px">
                                        <b>Importe:</b>
                                    </td>         	
                                    <td align='left'>
                                        <input type="text" size="10" value="0" id="importe_acto_quiru" name="importe_acto_quiru" <? echo "readonly" ?>>
                                    </td>
                                </tr> 

                                <tr>
                                    <td align="right">
                                        <b>Dias UTI:</b>
                                    </td>         	
                                    <td align='left'>
                                        <input class="form_quirurgico" type="text" size="8" value="<?php echo $dias_uti ?>" id="dias_uti" name="dias_uti" maxlength="2" onKeyPress="return acceptNum(event)">(Maximo = <?= $maximo3 ?>)
                                    </td>
                                    <td align="right" style="padding-left: 10px">
                                        <b>Importe:</b>
                                    </td>         	
                                    <td align='left'>
                                        <input type="text" size="10" value="0" id="importe_dias_uti" name="importe_dias_uti" <? echo "readonly" ?>>
                                    </td>
                                </tr> 

                                <tr>
                                    <td align="right">
                                        <b>Dias Cuidado Intermedio:</b>
                                    </td>         	
                                    <td align='left'>
                                        <input class="form_quirurgico" type="text" size="8" value="<?php echo $dias_intermedio ?>" id="dias_intermedio" name="dias_intermedio" maxlength="2" onKeyPress="return acceptNum(event)">(Maximo = <?= $maximo4 ?>)
                                    </td>
                                    <td align="right" style="padding-left: 10px">
                                        <b>Importe:</b>
                                    </td>         	
                                    <td align='left'>
                                        <input type="text" size="10" value="0" id="importe_dias_intermedio" name="importe_dias_intermedio" <? echo "readonly" ?>>
                                    </td>
                                </tr> 

                                <tr >
                                    <td align="right" colspan="2" style="padding-top: 10px" >
                                        <b>Total:</b>
                                    </td>         	
                                    <td align='left' colspan="2" style="padding-top: 10px">
                                        <input type="text" size="10" value="<?php echo $importe_total ?>" id="importe_total" name="importe_total" <? echo "readonly" ?>>
                                    </td>
                                </tr> 

                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td align="right">
                            <b>Fecha de Inicio:</b>
                        </td>
                        <td align="left">

                            <input type=text id=fecha_control name=fecha_control value='<?php echo fecha($fecha_control); ?>' size=15 <? echo "readonly" ?> >					    	 
                        </td>		    
                    </tr>  

                    <tr>
                        <td align="right">
                            <b>Fecha de Alta:</b>
                        </td>
                        <td align="left">

                            <input type=text id=fecha_alta name=fecha_alta value='<?php echo $fecha_alta; ?>' size=15>					    	 
                        </td>		    
                    </tr>


                    <tr>
                        <td align="right">
                            <b>Observaciones:</b>
                        </td>         	
                        <td align='left'>
                            <textarea cols='30' rows='4' name='observaciones' <?
                                        if ($id_planilla)
                                            echo "readonly"
                                            ?>><?= $observaciones; ?></textarea>
                        </td>
                    </tr>              
                </table>
            </td>      
        </tr>

        <? if (!$estabacargado) { ?>
            <tr id="mo">
                <td align=center colspan="2">&nbsp;</td>
            </tr>  
            <tr align="center">
                <td>
                    <input type='submit' id='guardar' name='guardar' value='Guardar Planilla' onclick="return control_nuevos()"
                           title="Guardar datos de la Planilla">
                </td>
            </tr>

        <? } else { ?>
            <tr id="mo">
                <td align=center colspan="2">&nbsp;</td>
            </tr>  
            <tr align="center">
                <td>
                    <input type='submit' id='guardar' name='guardar' value='Actualizar Planilla' onclick="return control_nuevos()"
                           title="Guardar datos de la Planilla">
                </td>
            </tr>
        <? } ?>

    </table>           
    <br>
    <? if ($id_planilla) { ?>
        <table class="bordes" align="center" width="100%">
            <tr align="center" id="sub_tabla">
                <td>	
                    Editar DATO
                </td>
            </tr>

            <tr>
                <td align="center">
                    <input type=button name="editar" value="Editar" onclick="editar_campos()" title="Edita Campos" style="width:130px"> &nbsp;&nbsp;
                    <input type="submit" name="guardar_editar" value="Guardar" title="Guarda Muleto" disabled style="width:130px" onclick="return control_nuevos()">&nbsp;&nbsp;
                    <input type="button" name="cancelar_editar" value="Cancelar" title="Cancela Edicion de Muletos" disabled style="width:130px" onclick="document.location.reload()">		      
                    <?
                    if (permisos_check("inicio", "permiso_borrar"))
                        $permiso = "";
                    else
                        $permiso = "disabled";
                    ?>
                    <input type="submit" name="borrar" value="Borrar" style="width:130px" <?= $permiso ?>>
                </td>
            </tr> 
        </table>	
        <br>
    <? } ?>
    <?php if (!$estabacargado) { ?>
        <tr>
            <td>
                <table width=100% align="center" class="bordes">
                    <tr align="center">
                        <td>
                            <input type=button name="volver" value="Volver" onclick="window.close();" title="Volver a la Practica" style="width:150px"/>     
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table width=100% align="center" class="bordes">
                    <tr align="center">
                        <td>
                            <font color="Black" size="3"> <b>Estos datos son obligatorios para guardar la practica</b></font>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    <?php } else { ?>
        <tr>
            <td>
                <table width=100% align="center" class="bordes">
                    <tr align="center">
                        <td>
                            <?php $ref = encode_link('../facturacion/listadoComprobantesPerinatal.php', array("cuie_elegido" => $cuiel)); ?>
                            <input type=button name="volver" value="Volver" onclick="location.href = '<?php echo $ref ?>'" title="Volver al listado" style="width:150px"/>     
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    <?php } ?>
</table>
</form>

<?=
fin_pagina(); // aca termino ?>