<?
require_once ("../../config.php");
require_once("../../lib/funciones_misiones.php");
require_once("./factura_biblioteca.php");
require_once ("../../clases/Smiefectores.php");
require_once ("../../clases/Factura.php");

extract($_POST, EXTR_SKIP);
if ($parametros)
    extract($parametros, EXTR_OVERWRITE);

$func_nroFactura = nro_factura_misiones();
$inhabil = '';
if ($func_nroFactura) {
    $nroF = NRO_FACTURA_MISIONES;
} else {
    $nroF = '';
    $inhabil = 'disabled';
}

if ($desvincular == "True") {
    $db->StartTrans();
    $query = "update facturacion.comprobante set
             id_factura=NULL
             where id_comprobante=$id_comprobante";

    sql($query, "Error al desvincular el comprobante") or fin_pagina();
    $accion = "Se desvinculo el Comprobante Numero: $id_comprobante";
    /* cargo los log */
    $usuario = $_ses_user['name'];
    $fecha_carga = date("Y-m-d H:i:s");
    $log = "insert into facturacion.log_comprobante 
		   (id_comprobante, fecha, tipo, descripcion, usuario) 
	values ($id_comprobante, '$fecha_carga','Comprobante Desvinculado de Factura $id_factura','Nro. Comprobante $id_comprobante', '$usuario')";
    sql($log) or fin_pagina();
    $log = "insert into facturacion.log_factura
		   (id_factura, fecha, tipo, descripcion, usuario) 
	values ($id_factura, '$fecha_carga','Comprobante Desvinculado de Factura $id_factura','Nro. Comprobante $id_comprobante', '$usuario')";
    sql($log) or fin_pagina();

    $db->CompleteTrans();
}

//if ($_POST['anula_factura']) {
//    $fecha_carga = date("Y-m-d");
//    $db->StartTrans();
//
//    $accion = anulaFacura($id_factura);
//
//    $db->CompleteTrans();
//}

if ($_POST['cierra_factura']) {
    $fecha_carga = date("Y-m-d");
    $id_factura = $_POST['id_factura'];

    if (!facturavacia($id_factura)) {
        $db->StartTrans();
        cerrarFactura($id_factura);
        $usuario = $_ses_user['name'];
        $fecha_carga = date("Y-m-d H:i:s");
        $accion = "Se CERRO la Factura ID: $id_factura";
        $db->CompleteTrans();
//        $para = "ferrando.pedro@gmail.com";
        //$paracc = "bpetrella@hotmail.com";
//        $asunto = "Cierre de Factura " . $id_factura;
//        $contenido = $accion . "<BR>" . "Usuario: " . $usuario . " - Fecha: " . $fecha_carga;
//
//        enviar_mail($para, $paracc, null, $asunto, $contenido, null, null, '0');
    } else {
        $accion = "No se pudo CERRAR la Factura Numero: $id_factura (vacia)";
    }
}


if ($_POST['abre_factura'] == "Abre Factura") {
    $fecha_carga = date("Y-m-d");
    $db->StartTrans();

    $query = "update facturacion.factura set estado='A'
   			where id_factura=$id_factura";
    sql($query, "Error al cerrar la factura") or fin_pagina();

    $accion = "Se Abrio la Factura Numero: $id_factura";

    /* cargo los log */
    $usuario = $_ses_user['name'];
    $log = "insert into facturacion.log_factura
		   (id_factura, fecha, tipo, descripcion, usuario) 
	values ($id_factura, '$fecha_carga','Abrio Factura','Abrio la Factura', '$usuario')";
    sql($log) or fin_pagina();

    $db->CompleteTrans();
}

if ($_POST['guardarexpediente'] == 'TRUE') {
    $id_factura = $_POST['idfactura'];

    $nro_exp = trim($_POST['codorg']) . "-" . trim($_POST['nro_correlativo']) . "-" . trim($_POST['ano_exp']);

    $db->StartTrans();

    actualizarExpedienteEnFactura($id_factura, $nro_exp);
    insertarSoloExpediente($nro_exp);

    $db->CompleteTrans();
}

if ($_POST['guardar'] == "Guardar Factura") {

    $sql = "select cuie, com_gestion, fecha_comp_ges, fecha_fin_comp_ges
            from nacer.efe_conv
            where cuie='$cuie'
            AND activo='t'";
    $res_efectores_aux = sql($sql) or fin_pagina();
    $com_gestion_aux = $res_efectores_aux->fields['com_gestion'];
    $fecha_comp_ges_aux = $res_efectores_aux->fields['fecha_comp_ges'];
    $fecha_fin_comp_ges_aux = $res_efectores_aux->fields['fecha_fin_comp_ges'];
    $tipo_de_nomenclador = $_POST['tipo_prestacion'];
    $fecha_factura = Fecha_db($fecha_factura);

    if ($periodo == $periodo_actual) {
        $unicafacturadelperiodo = unicafacturadelperiodo($cuie, $periodo, $tipo_de_nomenclador);
        $tipo_liquidacion = 'V';
    } else {
        $tipo_liquidacion = 'R';
        $unicafacturadelperiodo = true;
    }

    if ($unicafacturadelperiodo) {
        if ($com_gestion_aux == 'VERDADERO') {
            if (($fecha_comp_ges_aux <= $fecha_factura) && ($fecha_factura <= $fecha_fin_comp_ges_aux)) {
                $fecha_carga = date("Y-m-d");
                $db->StartTrans();

                $q = "select nextval('facturacion.factura_id_factura_seq') as id_factura";
                $id_factura = sql($q) or fin_pagina();
                $id_factura = $id_factura->fields['id_factura'];

                $nro_factura = proxNumeroFactura($cuie);

                if ($_POST['codorg']) {
                    $nro_exp = $_POST['codorg'] . "-" . $_POST['no_correlativo'] . "-" . $_POST['ano_exp'];
                }
                $query = "insert into facturacion.factura
		             (id_factura,cuie,fecha_carga,fecha_factura,periodo,estado,observaciones,periodo_actual,online,ctrl,tipo_nomenclador,nro_exp,tipo_liquidacion,nro_fact_offline)
		             values
		             ($id_factura,'$cuie','$fecha_carga','$fecha_factura','$periodo','A','$observaciones','$periodo_actual','SI','N','$tipo_de_nomenclador','$nro_exp','$tipo_liquidacion','$nro_factura')";

                sql($query, "Error al insertar la factura") or fin_pagina();

                $accion = "Se guardo la Factura Numero: $nro_factura";

                /* cargo los log */
                $usuario = $_ses_user['name'];
                $log = "insert into facturacion.log_factura
				   (id_factura, fecha, tipo, descripcion, usuario) 
			values ($id_factura, '$fecha_carga','ALTA','Alta desde Usuario', '$usuario')";
                sql($log) or fin_pagina();

                $db->CompleteTrans();
            } else {
                $accion = "Error: La fecha de factura esta fuera de vigencia con el compromiso de gestion";
            }
        } else {
            $accion = "Error: El efector seleccionado no tiene Compromiso de Gestion";
        }
    } else {
        $accion = "Error: Periodo ya Facturado";
    }
}

if ($id_factura) {
    $query = "SELECT 
  $nroF *
    FROM facturacion.factura
    left JOIN facturacion.recepcion r ON r.idrecepcion=facturacion.factura.recepcion_id
    where id_factura=$id_factura";
    $res_factura = sql($query, "Error al traer el Comprobantes") or fin_pagina();

    $numero_factura = $func_nroFactura ? $res_factura->fields['numero_factura'] : $id_factura;
    $cuie = $res_factura->fields['cuie'];
    $efector = new Smiefectores($efector);
    $factura = new Factura($id_factura);
    $fecha_factura = $res_factura->fields['fecha_factura'];
    $periodo = $res_factura->fields['periodo'];
    $id_recepcion = $res_factura->fields['recepcion_id'];
    $periodo_actual = $res_factura->fields['periodo_actual'];
    $observaciones = $res_factura->fields['observaciones'];
    $estado = $res_factura->fields['estado'];
    $mes_fact_d_c = $res_factura->fields['mes_fact_d_c'];
    $monto_prefactura = $res_factura->fields['monto_prefactura'];
    $fecha_control = $res_factura->fields['fecha_control'];
    $nro_exp = $res_factura->fields['nro_exp'];
    $traba = $res_factura->fields['traba'];
    $ctrl = $res_factura->fields['ctrl'];
    $factura_online = $res_factura->fields['online'];
    $tipo_de_nomenclador = $res_factura->fields['tipo_nomenclador'];
    $fecha_entrada = $res_factura->fields['fecha_entrada'];
    if (is_null($fecha_entrada) || $fecha_entrada == '') {
        $disablexfechaentrada = 'disabled';
    }

    if ($factura_online == 'NO') {
        $periododesdecomprobante = buscarFechaPorComprobante($id_factura);
        $vigencia = obtenerModoVigencia($cuie, $periododesdecomprobante);
    } else {
        $vigencia = obtenerModoVigencia($cuie, $periodo . '/01');
    }
}

echo $html_header;
?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<link rel='stylesheet' href='../../lib/jquery/ui/jquery-ui.css'/>
<script src='../../lib/jquery/ui/jquery.ui.datepicker-es.js' type='text/javascript'></script>


<script>
    $(document).ready(function() {

        $("#fecha_factura").datepicker();
        $("#fecha_control").datepicker();
        $('select#cuie').on('change', function() {
            var cuie = $('select#cuie').val();
            $.post("tiposdenomencladores.php", {cuie: cuie}, function(data) {
                $('#td_tipo_nomenclador').html(data);
            })
        });

        if ($('#id_factura').val() == 0) {
            $('#expedienteedit').attr('hidden', true);
        }
    });

    var img_noedit = '<?= $img_noedit = '../../imagenes/salir.gif' ?>';
    var img_edit = '<?= $img_edit = '../../imagenes/menu/edit.gif' ?>';
    var img_save = '<?= $img_save = '../../imagenes/menu/iconSave.gif' ?>';

    function editar_expediente() {
        var img = $('#expedienteedit').attr('src');
        if (img == img_edit) {
            //img.show=0;
            $('#expedienteedit').attr('src', img_noedit);
            $('#ano_exp').removeAttr('disabled');
            $('#nro_correlativo').removeAttr('disabled');
            $('#codorg').removeAttr('disabled');
            $('#expedientesave').removeAttr('hidden');
        }
        else {
            //img.show=1;
            $('#ano_exp').attr('disabled', true);
            $('#nro_correlativo').attr('disabled', true);
            $('#codorg').attr('disabled', true);
            $('#expedienteedit').attr('src', img_edit);
            $('#expedientesave').attr('hidden', true);
        }
    }

    function guardar_expediente() {
        var ano_exp = $("#ano_exp").val();
        if (ano_exp == "") {
            alert('Debe ingresar un Año para el Expediente');
            return false;
        }
        var nro_correlativo = $("#nro_correlativo").val();
        if (nro_correlativo == "") {
            alert('Debe ingresar un Nro Correlativo para el Expediente');
            return false;
        }
        var codorg = $("#codorg").val();
        if (codorg == "") {
            alert('Debe ingresar un Codigo de Organizacion para el Expediente');
            return false;
        }

        $.post("factura_admin.php", {guardarexpediente: 'TRUE', idfactura: $('#id_factura').val(), codorg: codorg, nro_correlativo: nro_correlativo, ano_exp: ano_exp});

        $('#ano_exp').attr('disabled', true);
        $('#nro_correlativo').attr('disabled', true);
        $('#codorg').attr('disabled', true);
        $('#expedienteedit').attr('src', img_edit);
    }

    //controlan que ingresen todos los datos necesarios par el muleto
    function control_nuevos()
    {
        if (document.all.cuie.value == "-1") {
            alert('Debe Seleccionar un efector');
            return false;
        }
        if (document.all.periodo.value == "-1") {
            alert('Debe Seleccionar un Periodo');
            return false;
        }
        if (document.all.periodo_actual.value == "-1") {
            alert('Debe Seleccionar un Periodo Actual');
            return false;
        }
        if (document.all.fecha_factura.value == "") {
            alert('Debe Ingresar una fecha de factura');
            return false;
        }
        if (document.all.tipo_prestacion.value == "-1") {
            alert('Debe Ingresar un Tipo de Nomenclador');
            return false;
        }

    }//de function control_nuevos()

    var img_ext = '<?= $img_ext = '../../imagenes/rigth2.gif' ?>';//imagen extendido
    var img_cont = '<?= $img_cont = '../../imagenes/down2.gif' ?>';//imagen contraido
    function muestra_tabla(obj_tabla, nro) {
        oimg = eval("document.all.imagen_" + nro);//objeto tipo IMG
        if (obj_tabla.style.display == 'none') {
            obj_tabla.style.display = 'inline';
            oimg.show = 0;
            oimg.src = img_cont;

        }
        else {
            obj_tabla.style.display = 'none';
            oimg.show = 1;
            oimg.src = img_ext;
        }
    }
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

</script>

<form name='form1' action='factura_admin.php' method='POST'>
    <input type="hidden" value="<?= $id_factura ?>" name="id_factura" id="id_factura">
    <input type="hidden" value="<?= $id_recepcion ?>" name="id_recepcion">
    <?
    echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";

    /*     * *****Traemos y mostramos el Log ********* */
    if ($id_factura) {
        $q = "SELECT 
  facturacion.log_factura.fecha,
  facturacion.log_factura.tipo,
  facturacion.log_factura.descripcion,
  facturacion.log_factura.usuario
      FROM
  facturacion.factura
  INNER JOIN facturacion.log_factura ON (facturacion.factura.id_factura = facturacion.log_factura.id_factura)
  where factura.id_factura=$id_factura
	order by id_log_factura";
        $log = $db->Execute($q) or die($db->ErrorMsg() . "<br>$q");
        ?>
        <div align="right">
            <input name="mostrar_ocultar_log" type="checkbox" value="1" onclick="if (!this.checked)
                document.all.tabla_logs.style.display = 'none'
            else
                document.all.tabla_logs.style.display = 'block'
                   "> Mostrar Logs
        </div>	
        <!-- tabla de Log de la OC -->
        <div style="display:none;width:98%;overflow:auto;<?
        if ($log->RowCount() > 3)
            echo 'height:60;'
            ?> " id="tabla_logs" >
            <table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor=#cccccc>
                <? while (!$log->EOF) { ?>
                    <tr>
                        <td height="20" nowrap>Fecha <?= fecha($log->fields['fecha']) . " " . Hora($log->fields['fecha']); ?> </td>
                        <td nowrap > Usuario : <?= $log->fields['usuario']; ?> </td>
                        <td nowrap > Tipo : <?= $log->fields['tipo']; ?> </td>
                        <td nowrap > descipcion : <?= $log->fields['descripcion']; ?> </td>	      
                    </tr>
                    <?
                    $log->MoveNext();
                }
            }
            ?>
        </table>
    </div>
    <hr>
    <? /*     * *****************  FIN  LOG  *************************** */ ?>


    <input type="hidden" name="id_factura" value="<?= $id_factura ?>">
    <table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?= $bgcolor_out ?>' class="bordes">
        <tr id="mo">
            <td>
                <?
                if (!$id_factura) {
                    ?>  
                    <font size=+1><b>Nueva Factura</b></font>&nbsp;&nbsp;&nbsp;
                    <img src='<?php echo "$html_root/imagenes/ayuda.gif" ?>' style="cursor:hand" border="0" alt="Ayuda" onClick="abrir_ventana('<?php echo "$html_root/modulos/ayuda/facturacion/nueva_factura.htm" ?>', 'Agregar Factura')" >   
                    <?
                } else {
                    ?>
                    <font size=+1><b>Factura (<?= ($estado == 'C') ? "Cerrada" : "Abierta" ?>)</b></font> &nbsp;&nbsp;&nbsp;
                    <img src='<?php echo "$html_root/imagenes/ayuda.gif" ?>' style="cursor:hand" border="0" alt="Ayuda" onClick="abrir_ventana('<?php echo "$html_root/modulos/ayuda/facturacion/modifica_factura.htm" ?>', 'Modificar Factura')" >  
                <? } ?>

            </td>
        </tr>
        <tr><td>
                <table width=70% align="center" class="bordes">
                    <tr>
                        <td id=mo colspan="2">
                            <b> Descripción de la FACTURA</b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table style="margin: 0 auto;">
                                <tr>	           
                                    <td colspan="4" align="center" colspan="2">
                                        <b> Número de Factura: <font size="+1" color="Red"><?= ($id_factura) ? $numero_factura : "Nueva Factura" ?></font> </b>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <b><font color="Red">*</font>Efector:</b>
                                    </td>
                                    <td colspan="4" align="left">			 	
                                        <select id="cuie" name=cuie Style="width:450px" 
                                                onKeypress="buscar_combo(this);"
                                                onblur="borrar_buffer();"
                                                onchange="borrar_buffer();"
                                                <?
                                                if ($id_factura)
                                                    echo "disabled"
                                                    ?>><option value=-1 selected=Selected>Seleccione</option>

                                            <?
                                            $cuieses = traeCuiesPorUsuario($_ses_user['id']);

                                            $res_efectores = traeListadoDeDatosEfector($cuieses);

                                            while (!$res_efectores->EOF) {
                                                $com_gestion = $res_efectores->fields['com_gestion'];
                                                $cuiel = $res_efectores->fields['cuie'];
                                                
                                                $nombre_efector = $res_efectores->fields['nombreefector'];
                                                ($com_gestion == 'FALSO') ? $color_style = '#F78181' : $color_style = '';
                                                ?>
                                                <option value='<?= $cuiel ?>' <?
                                                if ($cuie == $cuiel)
                                                    echo "selected"
                                                    ?> Style="background-color: <?= $color_style ?>;"><?= $cuiel . " - " . $nombre_efector ?></option>
                                                        <?
                                                        $res_efectores->movenext();
                                                    }
                                                    ?>
                                        </select>
                                        <button onclick="window.open('../inscripcion/busca_efector.php?qkmpo=cuie', 'Buscar', 'dependent:yes,width=900,height=700,top=1,left=60,scrollbars=yes');" <?
                                        if ($id_factura)
                                            echo "disabled"
                                            ?>>b</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <b><font color="Red">*</font>Fecha Factura:</b>
                                    </td>
                                    <td colspan="4" align="left">
                                        <? $fecha_comprobante = date("d/m/Y"); ?>
                                        <input type=text id=fecha_factura name=fecha_factura value='<?= fecha($fecha_factura); ?>' size=15 
                                        <?
                                        if ($id_factura)
                                            echo "disabled";
                                        ?>/>

                                    </td>		    
                                </tr>
                                <tr >
                                    <td align="right" >
                                        <b>Datos del Expediente:</b>
                                    </td>
                                    <td align="left">
                                        <?
                                        $nro_exp = explode("-", $nro_exp);
                                        ?>
                                        Cod. de Org.
                                        <input type=text   id="codorg" name=codorg value='<?= $nro_exp[0] ?>' size=15 <?
                                        if ($id_factura)
                                            echo "disabled";
                                        ?>>
                                        Nº Correlativo
                                        <input type=text id="nro_correlativo" name=no_correlativo value='<?= $nro_exp[1] ?>' size=15 <?
                                        if ($id_factura)
                                            echo "disabled";
                                        ?>>
                                        Año
                                        <input type=text id="ano_exp" name=ano_exp value='<?= $nro_exp[2] ?>' size=15 <?
                                        if ($id_factura)
                                            echo "disabled";
                                        ?>>
                                        <img name="edit" id="expedienteedit" src="<?= $img_edit ?>" border=0 title="Editar Expediente" style="cursor:pointer;" onclick="return editar_expediente();" >
                                        <img hidden="hidden" name="save" id="expedientesave" src="<?= $img_save ?>" border=0 title="Guardar Expediente" style="cursor:pointer;" onclick="return guardar_expediente();" >
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <b><font color="Red">*</font>Periodo Actual:</b>
                                    </td>
                                    <td colspan="4" align="left">
                                        <? ($traba == 'si') ? $disabled = "disabled" : $disabled = "" ?>		          			
                                        <select name=periodo_actual Style="width:450px" <?= $disabled ?>
                                        <?
                                        if ($id_factura)
                                            echo "disabled"
                                            ?>>
                                            <option value=-1>Seleccione</option>
                                            <?
                                            $sql = "SELECT  periodo 
                                                    FROM facturacion.periodo 
                                                    WHERE tipo in ('H','V')
                                                    ORDER BY periodo DESC LIMIT 6";
                                            $result = sql($sql, "No se puede traer el periodo");
                                            while (!$result->EOF) {
                                                ?>

                                                <option value=<?= $result->fields['periodo'] ?> <?
                                                if ($periodo_actual == $result->fields['periodo'])
                                                    echo "selected"
                                                    ?>><?= $result->fields['periodo'] ?></option>
                                                        <?
                                                        $result->movenext();
                                                    }
                                                    ?>			  
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td align="right">
                                        <b><font color="Red">*</font>Periodo Prestación:</b>
                                    </td>
                                    <td colspan="4" align="left">
                                        <? ($traba == 'si') ? $disabled = "disabled" : $disabled = "" ?>		          			
                                        <select name=periodo Style="width:450px" <?= $disabled ?>
                                        <?
                                        if ($id_factura)
                                            echo "disabled"
                                            ?>  >
                                            <option value=-1>Seleccione</option>
                                            <?
                                            $result->movefirst();
                                            while (!$result->EOF) {
                                                $enlistadeperiodos = $result->fields['periodo'];
                                                ?>
                                                <option value=<?= $enlistadeperiodos ?>
                                                <?
                                                if ($periodo == $enlistadeperiodos)
                                                    echo "selected"
                                                    ?>><?= $enlistadeperiodos ?>
                                                </option>
                                                <?
                                                $result->movenext();
                                            }
                                            ?>			  
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <b><font color="Red">*</font>Tipo de Nomenclador:</b>
                                    </td>
                                    <td align="left" id="td_tipo_nomenclador">		          			
                                        <select id="tipo_prestacion" name=tipo_prestacion Style="width:450px" <?php
                                        if ($id_factura)
                                            echo "disabled";
                                        ?>>
                                            <option value=-1>Seleccione</option>
                                            <?php if ($id_factura) { ?>
                                                <option selected value=<?php echo $factura->getTipoNomenclador(); ?>
                                                        ><?php echo $factura->getTipoNomenclador() ?></option>
                                                        <?php
                                                    } else {
                                                        if ($res_efectores->RecordCount() == 1) {
                                                            $res_efectores->movefirst();
                                                            $efector = new Smiefectores($res_efectores->fields['cuie']);
                                                            $tipos_de_nomenclador = $efector->tiposDeNomenclador();
                                                            foreach ($tipos_de_nomenclador as $key => $value) {
                                                                ?>
                                                        <option  value=<?php echo "$key"; ?>
                                                                 ><?php echo $value ?></option>
                                                                 <?php
                                                             }
                                                         }
                                                     }
                                                     ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <b>Observaciones:</b>
                                    </td>         	
                                    <td colspan="4" align='left'>
                                        <textarea cols='70' rows='7' name='observaciones' ><?= $observaciones; ?></textarea>
                                    </td>
                                </tr>
                                <? if ($estado == 'CO') { ?>
                                    <table style="margin: 0 auto;">
                                        <tr>
                                            <td id=mo colspan="4">
                                                <b>Datos Extras</b>
                                            </td>
                                        </tr>    

                                        <tr>
                                            <td align="right">
                                                <b>Mes Facturado Autorizado/No Autorizado:</b>
                                            </td>         	
                                            <td align='left'>
                                                <input type="text" name="mes_fact_d_c" value="<?= $mes_fact_d_c ?>" style="width:250px">&nbsp;&nbsp;              
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="right">
                                                <b>Monto Prefactura:</b>
                                            </td>         	
                                            <td align='left'>
                                                <input type="text" name="monto_prefactura" value="<?= number_format($monto_prefactura, 2, '.', ',') ?>" style="width:250px">&nbsp;&nbsp;                             
                                            </td>
                                        </tr>

                                        <tr>
                                            <td align="right">
                                                <b>Fecha Control:</b>
                                            </td>
                                            <td align="left">
                                                <? $fecha_comprobante = date("d/m/Y"); ?>
                                                <input type=text id=fecha_control name=fecha_control value='<?= fecha($fecha_control); ?>' size=15 readonly>
                                            </td>		    
                                        </tr>

                                        <tr>
                                            <td align="right">
                                                <b>Nùmero de Expediente:</b>
                                            </td>         	
                                            <td align='left'>
                                                <input type="text" name="nro_exp" value="<?= $nro_exp ?>" style="width:250px">
                                            </td>
                                        </tr>

                                        <? ($traba == 'si') ? $disabled = "disabled" : $disabled = "" ?>

                                        <tr>         	   	
                                            <td align="center" colspan="2" > 

                                                <input type="submit" name="guardar_extra" value="Guardar" style="width:150px" <?= $disabled ?>>               
                                            </td>
                                        </tr>

                                        <tr>
                                            <td align="center" colspan="2">
                                                <b><font size="2" face="arial" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>
                                            </td>
                                        </tr>
                                    </table>                                    
                                <? } ?> 

                            </table>
                        </td>      
                    </tr> 

                    <?
                    /*                     * *************************
                     * //botones de cerrar/abrir factura, correr/simular controles segun estado de factura
                     * **************************** */
                    ?>

                    <tr>
                        <td align="center" colspan="2" class="bordes">
                            <? if ($estado == 'A') { ?>

                                <button onclick="window.open('simularControlesSumar.php?cuie=<?= $cuie ?>&id_factura=<?= $id_factura ?>', 'Buscar', 'dependent:yes,width=1000,height=700,top=1,left=60,scrollbars=yes');"
                                        <?= $inhabil ?> value="Simular Controles" style="cursor: default;height: 30px;width:150px;text-align: center"
                                        <?
                                        if ($factura_online == 'NO') {
                                            if ($ctrl != "N")
                                                echo "disabled='disabled'";
                                            else
                                                echo "type='button'";
                                        }else {
                                            echo "disabled='disabled'";
                                        }
                                        ?>>Simular Controles</button>


                                <button onclick="window.open('controlesAutomaticosSumar.php?cuie=<?= $cuie ?>&id_factura=<?= $id_factura ?>', 'Buscar', 'dependent:yes,width=900,height=700,top=1,left=60,scrollbars=yes');" <?= $inhabil ?> value="Correr Controles" style="cursor: default;height: 30px;width:150px;text-align: center"
                                <?
                                if ($factura_online == 'NO') {
                                    if ($ctrl != "N")
                                        echo "disabled='disabled'";
                                    else
                                        echo "type='button'";
                                }else {
                                    echo "disabled='disabled'";
                                }
                                ?>>Correr Controles</button>

                                <button  type="submit" name="cierra_factura" value="Cierra Factura" onclick="return confirm('Esta Seguro que Desea CERRAR la FACTURA?')" style="cursor: default;height: 30px;width:150px;text-align: center"
                                <?
                                if ($factura_online == 'NO') {
                                    if ($ctrl != "S")
                                        echo "disabled='disabled'";
                                }else {
                                    
                                }
                                ?>>Cerrar Factura</button>

                                <!--                                <button type="submit" name="anula_factura" value="Anula Factura" onclick="return confirm('Esta Seguro que Desea ANULAR la FACTURA?')" style="cursor: default;height: 30px;width:150px;text-align: center; color:#b81900;"
                                                                        >Anula Factura</button>-->

                            <? } ?>                                                    

                            <?
                            if ($estado == 'C') {
                                ($traba == 'si') ? $disabled = "disabled" : $disabled = ""
                                ?>
                                <? if ($factura_online == 'SI') { ?>

                                    <button onclick="window.open('simularControlesSumar.php?cuie=<?= $cuie ?>&id_factura=<?= $id_factura ?>', 'Buscar', 'dependent:yes,width=1000,height=700,top=1,left=60,scrollbars=yes');"  value="Simular Controles" style="cursor: default;height: 30px;width:150px;text-align: center"
                                            type='button'
                                            <?
                                            if (!permisos_check("inicio", "simular_control"))
                                                echo "disabled='disabled'";
                                            if ($ctrl != "N")
                                                echo "disabled='disabled'";
                                            else
                                                echo "type='button'";

                                            echo $disablexfechaentrada;
                                            ?>
                                            >Simular Controles</button>

                                    <button onclick="window.open('controlesAutomaticosSumar.php?cuie=<?= $cuie ?>&id_factura=<?= $id_factura ?>', 'Buscar', 'dependent:yes,width=900,height=700,top=1,left=60,scrollbars=yes');"  value="Correr Controles" style="cursor: default;height: 30px;width:150px;text-align: center"
                                            type='button'
                                            <?
                                            if (!permisos_check("inicio", "correr_control"))
                                                echo "disabled='disabled'";
                                            if ($ctrl != "N")
                                                echo "disabled='disabled'";
                                            else
                                                echo "type='button'";

                                            echo $disablexfechaentrada;
                                            ?>
                                            >Correr Controles</button> 

                                <? } ?>
                                <button type='submit' name="abre_factura" value="Abre Factura" onclick="return confirm('Esta Seguro que Desea Abrir la FACTURA?')" style="cursor: default;height: 30px;width:150px;text-align: center" <?= $disabled ?>
                                <?php
                                if (!permisos_check("inicio", "correr_control"))
                                    echo "disabled='disabled'";
                                ?>
                                        >Abre Factura</button>
                                    <? } ?>

                        </td>
                    </tr>

                    <? if ($estado == 'C') { ?>
                        <tr id="mo">
                            <td align=center colspan="2">
                                <b><font color="White">Autorizado/ No Autorizado</font></b>
                            </td>
                        </tr>  
                        <tr>
                            <td align="center" colspan="2" class="bordes" bgcolor="#d3d3cd">		
                                <?
                                ($traba == 'si') ? $disabled = "disabled" : $disabled = "";

                                if ($vigencia['modo'] == 2) {
                                    $link = encode_link("debito_excel_online.php", array("id_factura" => $id_factura));
                                } else {
                                    $link = encode_link("debito_excel.php", array("id_factura" => $id_factura));
                                }
                                echo "<br><a target='_blank' href='" . $link . "' title='Debito/Credito'><IMG src='$html_root/imagenes/logo_impresora.gif' height='35' width='35' border='0'></a>";
                                ?>
                            </td>
                        </tr>

                    <? } ?> 

                    <? if ($estado == 'A') { ?>
                        <tr id="mo">
                            <td align=center colspan="2">
                                <b><font color="White">Autorizado/ No Autorizado</font></b>
                            </td>
                        </tr>  
                        <tr>
                            <td align="center" colspan="2" class="bordes" bgcolor="#d3d3cd">		
                                <?
                                if ($vigencia['modo'] == 2) {
                                    $link = encode_link("debito_excel_online.php", array("id_factura" => $id_factura));
                                } else {
                                    $link = encode_link("debito_excel.php", array("id_factura" => $id_factura, "estado" => 'A'));
                                }

                                echo "<br><a target='_blank' href='" . $link . "' title='Debito/Credito'><IMG src='$html_root/imagenes/logo_impresora.gif' height='35' width='35' border='0'></a>";
                                ?>
                            </td>
                        </tr>

                    <? } ?> 

                    <? if (!($id_factura)) { ?>

                        <tr id="mo">
                            <td align=center colspan="2">
                                <b>Guarda Factura</b>
                            </td>
                        </tr>  
                        <tr align="center">
                            <td>
                                <input type='submit' name='guardar' value='Guardar Factura' onclick="return control_nuevos()"
                                       title="Guardar datos de la Factura">
                            </td>
                        </tr>

                    <? } ?>

                </table>          
                <?
                /*                 * *************************
                 * //tabla de comprobantes
                 * **************************** */

                if ($id_factura) {
                    $query = "SELECT *
                                FROM facturacion.comprobante
                                left JOIN facturacion.smiefectores using(cuie)
                                where id_factura=$id_factura
                                order by comprobante.id_comprobante DESC";
                    $res_comprobante = sql($query, "<br>Error al traer los comprobantes<br>") or fin_pagina();
                    ?>
                    <BR>
            <tr>
                <td>
                    <table width="100%" class="bordes" align="center">
                        <tr align="center" id="mo">
                            <td align="center" width="3%">
                                <img id="imagen_a" src="<?= $img_ext ?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:hand;" onclick="muestra_tabla(document.all.prueba_vida, 'a');" >
                            </td>
                            <td align="center">
                                <b>Comprobantes</b>
                                &nbsp;&nbsp;<input type="button" value="Agregar Comprobante" name="agregar_comprobante" <?= ($estado == 'C') ? "disabled" : "" ?> <?= ($res_factura->fields['recepcion_id']) ? "disabled" : "" ?> 
                                                   onclick="window.open('<?= encode_link('listado_comp_fact.php', array("id_factura" => $id_factura, "cuie" => $cuie, "periodo_prestacion" => $periodo, "tipo_nomenclador" => $tipo_de_nomenclador)) ?>', '', 'toolbar=0,location=0,directories=0,status=0, menubar=0,scrollbars=1');">
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table id="prueba_vida" border="1" width="100%" style="display:none;border:thin groove">
                        <? if ($res_comprobante->RecordCount() == 0) { ?>
                            <tr>
                                <td align="center">
                                    <font size="3" color="Red"><b>No existen ITEMS para esta FACTURA</b></font>
                                </td>
                            </tr>
                            <?
                        } else {
                            ?>
                            <tr id="sub_tabla">	
                                <td width=1%>&nbsp;</td>
                                <td >Número de Comprobante</td>
                                <td >Apellido</td>
                                <td >Nombre</td>
                                <td >DNI</td>
                                <td >Beneficiario</td>
                                <td >Efector</td>
                                <td >Medico</td>
                                <td >Fecha Prestación</td>
                                <?
                                if (!$res_factura->fields['recepcion_id']) {
                                    if ($estado == 'A') {
                                        ?>
                                        <td >Desvincular</td>
                                        <?
                                    }
                                }
                                ?>
                            </tr>
                            <?
                            $contadordecomprobantes = 0;
                            $res_comprobante->movefirst();
                            while (!$res_comprobante->EOF) {
                                $beneficiario = afiliadoEnPadronPorID($res_comprobante->fields['idperiodo'], $res_comprobante->fields['clavebeneficiario']);
                                $grupo_etario = calcularGrupoEtareo($beneficiario['afifechanac'], $res_comprobante->fields['fecha_comprobante']);
                                $id_tabla = "tabla_" . $res_comprobante->fields['id_comprobante'];
                                $onclick_check = " javascript:(this.checked)?Mostrar('$id_tabla'):Ocultar('$id_tabla')";

                                $ref1 = encode_link("factura_admin.php", array("id_comprobante" => $res_comprobante->fields['id_comprobante'], "desvincular" => "True", "id_factura" => $id_factura));
                                $id_comprobante_aux = $res_comprobante->fields['id_comprobante'];
                                $onclick_marcar = "if (confirm('Esta Seguro que Desea Desvincular Comprobante $id_comprobante_aux?')) location.href='$ref1'
            						else return false;	";
                                ?>
                                <tr <?= atrib_tr() ?>>
                                    <td>
                                        <img id="imagen_<?= $contadordecomprobantes ?>" src="<?= $img_ext ?>" border=0 title="Mostrar Practicas" align="left" style="cursor:hand;" onclick="muestra_tabla(<?= $id_tabla ?>,<?= $contadordecomprobantes ?>);" >
                                    </td>	
                                    <td ><font size="+1" color="Red"><?= $res_comprobante->fields['id_comprobante'] ?></font></td>
                                    <td ><?= $beneficiario['afiapellido'] ?></td>
                                    <td ><?= $beneficiario['afinombre'] ?></td>
                                    <td ><?= $beneficiario['afidni'] ?></td>
                                    <td ><?= $res_comprobante->fields['clavebeneficiario'] ?></td>
                                    <td ><?= $res_comprobante->fields['nombreefector'] ?></td>
                                    <td ><?= $res_comprobante->fields['nombre_medico'] ?></td>
                                    <td ><?= fecha($res_comprobante->fields['fecha_comprobante']) ?></td>		 		
                                    <?
                                    if (!$res_factura->fields['recepcion_id']) {
                                        if ($estado == 'A') {
                                            ?>
                                            <td onclick=" <?= $onclick_marcar ?>" align="center"><img src='../../imagenes/sin_desc.gif' style='cursor:hand;'></td>		 		
                                            <?
                                        }
                                    }
                                    ?>	 		
                                </tr>	
                                <tr>
                                    <td colspan=10>
                                        <?
                                        $sql = "SELECT *
					        FROM facturacion.prestacion 
						LEFT JOIN facturacion.nomenclador USING (id_nomenclador)							
						WHERE id_comprobante=" . $res_comprobante->fields['id_comprobante'] . " 
                                                ORDER BY id_prestacion DESC";
                                        $result_items = sql($sql) or fin_pagina();
                                        ?>
                                        <div id=<?= $id_tabla ?> style='display:none'>
                                            <table width=90% align=center class=bordes>
                                                <?
                                                $cantidad_items = $result_items->recordcount();

                                                if ($cantidad_items == 0) {
                                                    ?>
                                                    <tr>
                                                        <td colspan="10" align="center">
                                                            <b><font color="Red" size="+1">NO HAY PRESTACIONES PARA ESTE COMPROBANTE</font></b>
                                                        </td>	                                
                                                    </tr>	                               
                                                <? } else { ?>
                                                    <tr id=ma>		                               
                                                        <td>Cantidad</td>
                                                        <td>Codigo</td>
                                                        <td>Descripción</td>
                                                        <td>Precio</td>
                                                        <td>Total</td>	                               
                                                    </tr>
                                                    <?
                                                    while (!$result_items->EOF) {
                                                        $codigo_nomenclador = $result_items->fields['codigo'];
                                                        $diagnostico_nomenclador = $result_items->fields['diagnostico'];
                                                        if ($diagnostico_nomenclador) {
                                                            $descripcion = descripcionDeDiagnostico($codigo_nomenclador, $diagnostico_nomenclador, $grupo_etario['categoria']);
                                                            $codigo_nomenclador = $codigo_nomenclador . " " . $diagnostico_nomenclador;
                                                        } else {
                                                            $descripcion = $result_items->fields["descripcion"];
                                                        }
                                                        ?>
                                                        <tr>
                                                            <td class="bordes" style="text-align: center"><?= $result_items->fields["cantidad"] ?></td>			                                 
                                                            <td class="bordes" style="text-align: center"><?= $codigo_nomenclador ?></td>
                                                            <td class="bordes" style="text-align: center"><?= $descripcion ?></td>
                                                            <td class="bordes" style="text-align: center"><?= number_format($result_items->fields["precio_prestacion"], 2, ',', '.') ?></td>
                                                            <td class="bordes" style="text-align: center"><?= number_format($result_items->fields["cantidad"] * $result_items->fields["precio_prestacion"], 2, ',', '.') ?></td>
                                                        </tr>
                                                        <?
                                                        $result_items->movenext();
                                                    }//del while
                                                }//del else
                                                ?>

                                            </table>
                                        </div>

                                    </td>
                                </tr>  	
                                <?
                                $contadordecomprobantes++;
                                $res_comprobante->movenext();
                            }
                        }
                        ?>
                    </table>
                </td>
            </tr>
        <? } ?>
        <tr>
            <td>
                <table width=100% align="center" class="bordes">
                    <tr align="center">
                        <td>
                            <input type=button name="volver" value="Volver" onclick="document.location = 'listado_factura.php'" title="Volver a los comprobantes" style="width:150px">     
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</form>

<?=
fin_pagina(); // aca termino ?>