<?
require_once ("../../config.php");

extract($_POST, EXTR_SKIP);
if ($parametros)
    extract($parametros, EXTR_OVERWRITE);
cargar_calendario();

if ($_POST['guardar_editar'] == "Guardar") {
    $db->StartTrans();

    $fecha_modificacion = date("Y-m-d H:i:s");
    $usuario = $_ses_user['id'];
    $fecha_comp_ges_db = Fecha_db($fecha_comp_ges);
    $fecha_fin_comp_ges_db = Fecha_db($fecha_fin_comp_ges);
    $fecha_tercero_admin_db = Fecha_db($fecha_tercero_admin);
    $fecha_fin_tercero_admin_db = Fecha_db($fecha_fin_tercero_admin);

    if ($_POST['pr_basico']) {
        $pr_basico = 't';
    } else {
        $pr_basico = 'f';
    }
    if ($_POST['pr_basico_2']) {
        $pr_basico_2 = 't';
    } else {
        $pr_basico_2 = 'f';
    }
    if ($_POST['pr_perinatal_catastrofico']) {
        $pr_perinatal_catastrofico = 't';
    } else {
        $pr_perinatal_catastrofico = 'f';
    }
    if ($_POST['pr_perinatal_nocatastrofico']) {
        $pr_perinatal_nocatastrofico = 't';
    } else {
        $pr_perinatal_nocatastrofico = 'f';
    }
    if ($_POST['pr_cc_catastrofico']) {
        $pr_cc_catastrofico = 't';
    } else {
        $pr_cc_catastrofico = 'f';
    }
    if ($_POST['pr_cc_nocatastrofico']) {
        $pr_cc_nocatastrofico = 't';
    } else {
        $pr_cc_nocatastrofico = 'f';
    }
    if ($_POST['pr_rondas']) {
        $pr_rondas = 't';
    } else {
        $pr_rondas = 'f';
    }
    if ($_POST['pr_remediar']) {
        $pr_remediar = 't';
    } else {
        $pr_remediar = 'f';
    }
    if ($_POST['pr_talleres']) {
        $pr_talleres = 't';
    } else {
        $pr_talleres = 'f';
    }

    if (strlen($_POST["poblacionNuevaDesde"]) < 1) {
        $poblacionNuevaDesde = "NULL";
    } else {
        $poblacionNuevaDesde = "'" . Fecha_db($_POST["poblacionNuevaDesde"]) . "'";
    }



    /* Setea como inactivo a todos los convenios del efector para luego poner como activo solamente al que esta actualizando/creando */
    $query_set_inactivo_conv = "UPDATE nacer.efe_conv
                                SET activo=FALSE
                                WHERE cuie='$cuie'";
    sql($query_set_inactivo_conv);

    if ($convenioselect != '-1') {
        $query_set_inactivo_nom = "UPDATE nacer.conv_nom
                                   SET activo=FALSE
                                   WHERE id_efe_conv=$convenioselect";
        sql($query_set_inactivo_nom);

        if ($fecha_tercero_admin_db != "") {
            $consulta_tercero_admin = "fecha_tercero_admin='$fecha_tercero_admin_db',";
        }
        $fecha_fin_tercero_admin_db = Fecha_db($fecha_fin_tercero_admin);
        if ($fecha_fin_tercero_admin_db != "") {
            $consulta_fin_tercero_admin = "fecha_fin_tercero_admin='$fecha_fin_tercero_admin_db',";
        }

        $query = "UPDATE nacer.efe_conv
                  SET com_gestion='$com_gestion',
                  com_gestion_firmante='$com_gestion_firmante',
                  tercero_admin='$tercero_admin',
                  tercero_admin_firmante='$tercero_admin_firmante',
                  com_gestion_firmante_actual='$com_gestion_firmante_actual',
                  dni_firmante_actual='$dni_firmante_actual',
                  fecha_modificacion='$fecha_modificacion',
                  usuario='$usuario', fecha_comp_ges='$fecha_comp_ges_db',
                  fecha_fin_comp_ges='$fecha_fin_comp_ges_db', 
                  com_gestion_pago_indirecto='$com_gestion_pago_indirecto',"
                . $consulta_tercero_admin
                . $consulta_fin_tercero_admin
                . "id_zona_sani='$id_zona_sani',
                  activo=TRUE,
                  fechapobnueva = $poblacionNuevaDesde            
                  WHERE id_efe_conv=$convenioselect";

        sql($query, "Error al actualizar el efector") or fin_pagina();

        $querynomenclador = "SELECT id_nomenclador_detalle
                             FROM nacer.conv_nom
                             WHERE id_efe_conv=$convenioselect AND id_nomenclador_detalle=$nomenclador_detalle";
        $resultado = sql($querynomenclador) or fin_pagina();

        if ($resultado->RecordCount() == 0) {
            $query_conv_nom = "INSERT INTO nacer.conv_nom (id_nomenclador_detalle,id_efe_conv,fecha_modificado,activo,nom_basico,nom_basico_2,nom_perinatal_catastrofico,nom_perinatal_nocatastrofico,nom_cc_catastrofico,nom_cc_nocatastrofico,nom_remediar,nom_rondas,nom_talleres)
                               VALUES($nomenclador_detalle,$convenioselect,'$fecha_modificacion',TRUE,'$pr_basico','$pr_basico_2','$pr_perinatal_catastrofico','$pr_perinatal_nocatastrofico','$pr_cc_catastrofico','$pr_cc_nocatastrofico','$pr_remediar','$pr_rondas','$pr_talleres')";
            sql($query_conv_nom);
        } else {
            $query_conv_nom = "UPDATE nacer.conv_nom 
                               SET fecha_modificado='$fecha_modificacion', activo=TRUE,
                                   nom_basico='$pr_basico',
                                   nom_basico_2='$pr_basico_2',
                                   nom_perinatal_catastrofico='$pr_perinatal_catastrofico',
                                   nom_perinatal_nocatastrofico='$pr_perinatal_nocatastrofico',
                                   nom_cc_catastrofico='$pr_cc_catastrofico',
                                   nom_cc_nocatastrofico='$pr_cc_nocatastrofico',
                                   nom_remediar='$pr_remediar',
                                   nom_rondas='$pr_rondas',
                                   nom_talleres='$pr_talleres'

                               WHERE id_efe_conv=$convenioselect AND id_nomenclador_detalle=$nomenclador_detalle";
            sql($query_conv_nom);
        }
    } else {
        if ($fecha_tercero_admin_db != "") {
            $consulta_tercero_admin = "fecha_tercero_admin,";
            $consulta_tercero_admin_value = "'" . $fecha_tercero_admin_db . "',";
        }

        if ($fecha_fin_tercero_admin_db != "") {
            $consulta_fin_tercero_admin = "fecha_fin_tercero_admin,";
            $consulta_fin_tercero_admin_value = "'" . $fecha_fin_tercero_admin_db . "',";
        }

        $query = "INSERT INTO nacer.efe_conv (com_gestion,com_gestion_firmante,tercero_admin,tercero_admin_firmante,
                  com_gestion_firmante_actual,dni_firmante_actual,fecha_modificacion,cuie,usuario,fecha_comp_ges,
                  fecha_fin_comp_ges,com_gestion_pago_indirecto,"
                . $consulta_tercero_admin
                . $consulta_fin_tercero_admin
                . "id_zona_sani,activo, fechapobnueva) ";

        $query.="VALUES ('$com_gestion','$com_gestion_firmante','$tercero_admin','$tercero_admin_firmante',
        '$com_gestion_firmante_actual','$dni_firmante_actual','$fecha_modificacion','$cuie','$usuario',
        '$fecha_comp_ges_db','$fecha_fin_comp_ges_db','$com_gestion_pago_indirecto',"
                . $consulta_tercero_admin_value
                . $consulta_fin_tercero_admin_value
                . "$id_zona_sani,TRUE, $poblacionNuevaDesde)";
        sql($query) or fin_pagina();

        $otroquery = "SELECT MAX(id_efe_conv)
                      FROM nacer.efe_conv";
        $numconvenio = sql($otroquery) or fin_pagina();
        $numconvenio = $numconvenio->fields['0'];

        $query_conv_nom = "INSERT INTO nacer.conv_nom (id_nomenclador_detalle,id_efe_conv,fecha_modificado,activo,nom_basico,nom_perinatal_catastrofico,nom_perinatal_nocatastrofico,nom_cc_catastrofico,nom_cc_nocatastrofico,nom_remediar,nom_rondas,nom_talleres)
                           VALUES($nomenclador_detalle,$numconvenio,'$fecha_modificacion',TRUE,'$pr_basico','$pr_perinatal_catastrofico','$pr_perinatal_nocatastrofico','$pr_cc_catastrofico','$pr_cc_nocatastrofico','$pr_remediar','$pr_rondas','$pr_talleres')";
        sql($query_conv_nom) or fin_pagina();
    }

    $db->CompleteTrans();
    $fechacompromiso = $fecha_comp_ges_db . " - " . $fecha_fin_comp_ges_db;

    $accion = "Se Grabo el Convenio: $fechacompromiso.";
}

$desabilefe = 'disabled';

if ($_POST['editar']) {
    $desabil = '';
    $desabiledit = 'disabled';
} else {
    $desabil = 'disabled';
    $desabiledit = '';
}

if ($_POST['cancelar_editar']) {
    $desabil = 'disabled';
    $desabiledit = '';
}


if (($_POST["convenioselect"] != '-1') and ($_POST["convenioselect"] != '')) {
    $unsql = "SELECT * 
              FROM nacer.efe_conv ec
              INNER JOIN nacer.conv_nom cn USING(id_efe_conv)  
              WHERE ec.id_efe_conv='$convenioselect' AND cn.activo=true";
    $res_conv = sql($unsql, "Error al traer el Convenio") or fin_pagina();

    $com_gestion = $res_conv->fields['com_gestion'];
    $com_gestion_firmante = $res_conv->fields['com_gestion_firmante'];
    $fecha_comp_ges = $res_conv->fields['fecha_comp_ges'];
    $fecha_fin_comp_ges = $res_conv->fields['fecha_fin_comp_ges'];
    $com_gestion_pago_indirecto = $res_conv->fields['com_gestion_pago_indirecto'];
    $tercero_admin = $res_conv->fields['tercero_admin'];
    $tercero_admin_firmante = $res_conv->fields['tercero_admin_firmante'];
    $fecha_tercero_admin = $res_conv->fields['fecha_tercero_admin'];
    $fecha_fin_tercero_admin = $res_conv->fields['fecha_fin_tercero_admin'];
    $com_gestion_firmante_actual = $res_conv->fields['com_gestion_firmante_actual'];
    $dni_firmante_actual = $res_conv->fields['dni_firmante_actual'];
    $n_2008 = $res_conv->fields['n_2008'];
    $n_2009 = $res_conv->fields['n_2009'];
    $id_nomenclador_detalle = $res_conv->fields['id_nomenclador_detalle'];
    $id_zona_sani = $res_conv->fields['id_zona_sani'];
    $poblacionNuevaDesde = fecha($res_conv->fields['fechapobnueva']);

    if ($res_conv->fields['nom_basico'] == 't')
        $checked_pr_basico = "checked='checked'";
    if ($res_conv->fields['nom_basico_2'] == 't')
        $checked_pr_basico_2 = "checked='checked'";
    if ($res_conv->fields['nom_cc_catastrofico'] == 't')
        $checked_pr_cc_catastrofico = "checked='checked'";
    if ($res_conv->fields['nom_perinatal_catastrofico'] == 't')
        $checked_pr_perinatal_catastrofico = "checked='checked'";
    if ($res_conv->fields['nom_cc_nocatastrofico'] == 't')
        $checked_pr_cc_nocatastrofico = "checked='checked'";
    if ($res_conv->fields['nom_perinatal_nocatastrofico'] == 't')
        $checked_pr_perinatal_nocatastrofico = "checked='checked'";
    if ($res_conv->fields['nom_remediar'] == 't')
        $checked_pr_remediar = "checked='checked'";
    if ($res_conv->fields['nom_rondas'] == 't')
        $checked_pr_rondas = "checked='checked'";
    if ($res_conv->fields['nom_talleres'] == 't')
        $checked_pr_talleres = "checked='checked'";
} else {
    $desabil = '';
    $desabiledit = 'disabled';

    $com_gestion = '';
    $com_gestion_firmante = '';
    $fecha_comp_ges = '';
    $fecha_fin_comp_ges = '';
    $com_gestion_pago_indirecto = 'FALSO';
    $tercero_admin = '';
    $tercero_admin_firmante = '';
    $fecha_tercero_admin = '';
    $fecha_fin_tercero_admin = '';
    $com_gestion_firmante_actual = '';
    $dni_firmante_actual = '';
    $n_2008 = '';
    $n_2009 = '';
    $id_nomenclador_detalle = '';
    $id_zona_sani = '';
}

if ($cuie) {
    $query = "SELECT uad.localidades.nombre nlocalidad,uad.departamentos.nombre ndepartamento,
              e.nombreefector nefector,e.tel,e.referente,e.cod_pos codpos,e.ciudad ciudad,e.domicilio domicilio
             FROM facturacion.smiefectores e 
             LEFT JOIN uad.departamentos ON departamento=id_departamento 
             LEFT JOIN uad.localidades ON id_localidad = localidad
             WHERE e.cuie='$cuie'";

    $res_factura = sql($query, "Error al traer el Efector") or fin_pagina();

    $nombre = $res_factura->fields['nefector'];
    $domicilio = $res_factura->fields['domicilio'];
    $departamento = $res_factura->fields['ndepartamento'];
    $localidad = $res_factura->fields['nlocalidad'];
    $cod_pos = $res_factura->fields['codpos'];
    $cuidad = $res_factura->fields['ciudad'];
    $referente = $res_factura->fields['referente'];
    $tel = $res_factura->fields['tel'];
}

if ($_POST['eliminar'] == "Eliminar") {

    $unsql = "SELECT activo 
              FROM nacer.efe_conv 
              WHERE id_efe_conv='$convenioselect'
              ORDER BY id_efe_conv";
    $res_conv = sql($unsql, "Error al traer el Convenio") or fin_pagina();

    if ($res_conv->fields['activo'] == 'f') {

        //Borra el convenio
        $query_borra = "DELETE FROM nacer.efe_conv 
                        WHERE id_efe_conv='$convenioselect'";
        sql($query_borra, "Error al Borrar Conveio") or fin_pagina();
        $accion = "Convenio eliminado";
    } else {
        $accion = "No se pueden eliminar convenios usados";
    }
}

#   Cuerpo HTML
echo $html_header;
?>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<link rel='stylesheet' href='../../lib/jquery/ui/jquery-ui.css'/>
<script src='../../lib/jquery/ui/jquery.ui.datepicker-es.js' type='text/javascript'></script>

<script>

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

    function control_nuevos()
    {
        if (document.all.fecha_comp_ges.value == "") {
            alert('Debe Ingresar una Fecha Compromiso de Gestion');
            return false;
        }
        if (document.all.fecha_fin_comp_ges.value == "") {
            alert('Debe Ingresar una Fecha Fin Compromiso de Gestion');
            return false;
        }
        if (document.all.com_gestion_pago_indirecto.value == "") {
            alert('Debe Ingresar un tipo de Gestion de Pago');
            return false;
        }
        if (document.getElementById("com_gestion_pago_indirecto").value == "VERDADERO") {
            if (document.all.fecha_tercero_admin.value == "") {
                alert('Debe Ingresar una Fecha Tercero Administrador');
                return false;
            }
            if (document.all.fecha_fin_tercero_admin.value == "") {
                alert('Debe Ingresar una Fecha Fin Tercero Administrador');
                return false;
            }
        }
        if (document.all.id_zona_sani.value == "-1") {
            alert('Debe Seleccionar una zona Sanitaria (Sino figura ninguna agregar en la tabla nacer.zona_sani)');
            return false;
        }
        return true;
    }

    function eliminar_conv() {
        if (confirm('Esta Seguro que Desea Eliminar el Convenio?'))
            return true
        else
            return false;
    }

    $(function() {
        $("#poblacionNuevaDesde").datepicker();
    });

</script>

<form name='form1' action='efectores_unif_admin.php' method='POST'>
    <input type="hidden" value="<?= $id_efe_conv ?>" name="id_efe_conv">
    <input type="hidden" value="<?= $cuie ?>" name="cuie">
    <? echo "<center><b><font size='+1' color='red'>$accion</font></b></center>"; ?>
    <table width="85%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?= $bgcolor_out ?>' class="bordes">
        <tr id="mo">
            <td>
                <font size=+1><b>GESTION DE CONVENIOS CON EFECTORES</b></font>        
            </td>
        </tr>
        <tr>
            <td>
                <table width=90% align="center" class="bordes">
                    <tr>
                        <td id=mo colspan="2">
                            <b> Descripción del Efector</b>
                        </td>
                    </tr>
                    <tr>	           
                        <td align="center" colspan="2">
                            <b> CUIE: <font size="+1" color="Red"><?= $cuie ?></font> </b>
                        </td>
                    </tr>
                    <tr>	           
                        <td align="center" colspan="2" style="padding-bottom: 10px ">

                        </td>
                    </tr>
                    <tr>
                        <td>
                            <fieldset>
                                <legend>Datos del Efector</legend>
                                <table width=90% align="center" class="bordes" style="padding-right: 50px">

                                    <tr>
                                        <td align="right">
                                            <b>Nombre:</b>
                                            <input type="text" size="40" value="<?= $nombre ?>" name="nombre" <?= $desabilefe ?>/>
                                        </td> 
                                        <td align="right">
                                            <b>Referente:</b>
                                            <input type="text" size="40" value="<?= $referente ?>" name="referente" <?= $desabilefe ?>/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right">
                                            <b>Departamento:</b>
                                            <input type="text" size="40" value="<?= $departamento ?>" name="departamento" <?= $desabilefe ?>/>
                                        </td>
                                        <td align="right">
                                            <b>Localidad:</b>
                                            <input type="text" size="40" value="<?= $localidad ?>" name="localidad" <?= $desabilefe ?>/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right">
                                            <b>Cuidad:</b>
                                            <input type="text" size="40" value="<?= $cuidad ?>" name="cuidad" <?= $desabilefe ?>/>
                                        </td>
                                        <td align="right">
                                            <b>Domicilio:</b>
                                            <input type="text" size="40" value="<?= $domicilio ?>" name="domicilio" <?= $desabilefe ?>/>
                                        </td>  
                                    </tr>                                                               
                                    <tr>
                                        <td align="right">
                                            <b>Telefono:</b>
                                            <input type="text" size="40" value="<?= $tel ?>" name="tel" <?= $desabilefe ?>/>
                                        </td>
                                        <td align="right">
                                            <b>C.P.:</b>                                                                 
                                            <input type="text" size="40" value="<?= $cod_pos ?>" name="cod_pos" <?= $desabilefe ?>/>
                                        </td> 
                                    </tr>
                                </table>



                            </fieldset>

                            <table width=90% align="center" class="bordes"  style="padding-top: 20px">
                                <tr>                        
                                    <td id="mo" colspan="2" >		          			
                                        <b>Datos del Convenio</b>&nbsp;&nbsp; 

                                        <select name="convenioselect" onChange="this.form.submit()" >
                                            <option value="-1">Nuevo Convenio</option>
                                            <?
                                            $sql2 = "SELECT * 
                                                     FROM nacer.efe_conv 
                                                     WHERE cuie ='$cuie'
                                                     ORDER BY id_efe_conv";
                                            $res_efectores2 = sql($sql2) or fin_pagina();
                                            while (!$res_efectores2->EOF) {
                                                $id_efe_conv = $res_efectores2->fields['id_efe_conv'];
                                                $fechacompromiso = $res_efectores2->fields['fecha_comp_ges'] . " - " . $res_efectores2->fields['fecha_fin_comp_ges'];
                                                ($res_efectores2->fields['activo'] == "t") ? $color_style = '#81f781' : $color_style = '';
                                                ?>
                                                <option Style="background-color: <?= $color_style ?>;" value='<?= $id_efe_conv ?>'
                                                <?
                                                if ($id_efe_conv == $convenioselect)
                                                    echo "selected"
                                                    ?>

                                                        >
                                                    <?= $fechacompromiso ?></option>
                                                <?
                                                $res_efectores2->movenext();
                                            }
                                            ?>                                                
                                        </select>

                                    </td>
                                </tr>

                                <tr>
                                    <td align="right" style="padding-top: 10px">
                                        <b>Nomenclador en Uso:</b>
                                    </td>
                                    <td align="left" style="padding-top: 10px">		          			
                                        <select name=nomenclador_detalle Style="width:290px" <?= $desabil ?>>
                                            <option value=-></option>
                                            <?
                                            $sql = "SELECT id_nomenclador_detalle,descripcion,fecha_desde,fecha_hasta 
                                                    FROM facturacion.nomenclador_detalle 
                                                    ORDER BY id_nomenclador_detalle";
                                            $res = sql($sql) or fin_pagina();
                                            while (!$res->EOF) {
                                                $id_nomenclador_detalle_1 = $res->fields['id_nomenclador_detalle'];
                                                $descripcion = $res->fields['descripcion'];
                                                $descripcion .= " [" . $res->fields['fecha_desde'] . " - " . $res->fields['fecha_hasta'] . "]";
                                                ?>
                                                <option value=<?=
                                                $id_nomenclador_detalle_1;
                                                if ($id_nomenclador_detalle == $id_nomenclador_detalle_1)
                                                    echo " selected"
                                                    ?> >
                                                            <?= $descripcion ?>
                                                </option>
                                                <?
                                                $res->movenext();
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <!--  -->

                                    <td colspan="2" align="center" style="border-width:19px 0px 0px; border-style:solid; border-color: transparent;">                                        
                                        <fieldset>
                                            <legend>Tipos de Nomenclador Habilitados</legend>
                                            <div style="display:block">
                                                <div style="width:160px;float:left"><input type="checkbox" name=pr_basico value="S" <?= $checked_pr_basico ?> <?= $desabil ?>/><div>Basico</div></div>
                                                <div style="width:160px;float:left"><input type="checkbox" name=pr_basico_2 value="S" <?= $checked_pr_basico_2 ?> <?= $desabil ?>/><div>Basico 2</div></div>
                                                <div style="width:160px;float:left"><input type="checkbox" name=pr_perinatal_catastrofico value="S" <?= $checked_pr_perinatal_catastrofico ?><?= $desabil ?>/><div>Perinatal Catastrofico</div></div>
                                                <div style="width:160px;float:left"><input type="checkbox" name=pr_perinatal_nocatastrofico value="S" <?= $checked_pr_perinatal_nocatastrofico ?><?= $desabil ?>/><div>Perinatal No Catastrofico</div></div>
                                                <div style="width:160px;float:left"><input type="checkbox" name=pr_talleres value="S" <?= $checked_pr_talleres ?><?= $desabil ?>/><div>Taller</div></div>
                                                <div style="width:160px;float:left"><input type="checkbox" name=pr_cc_catastrofico value="S" <?= $checked_pr_cc_catastrofico ?><?= $desabil ?>/><div>CC Catastrofico </div></div>
                                                <div style="width:160px;float:left"><input type="checkbox" name=pr_cc_nocatastrofico value="S" <?= $checked_pr_cc_nocatastrofico ?><?= $desabil ?>/><div>CC No Catastrofico</div></div>
                                                <div style="width:160px;float:left"><input type="checkbox" name=pr_rondas value="S" <?= $checked_pr_rondas ?><?= $desabil ?>/><div> Rondas</div></div>
                                                <div style="width:160px;float:left"><input type="checkbox" name=pr_remediar value="S" <?= $checked_pr_remediar ?><?= $desabil ?>/><div>Remediar+Redes</div></div>
                                            </div>
                                        </fieldset>
                                    </td>

                                </tr>

                                <tr >

                                    <td colspan = "4" align="center"style="border-width:19px 0px 0px; border-style:solid; border-color:transparent;"> 
                                        <fieldset>
                                            <legend>Poblacion Nueva SUMAR</legend>
                                            Fecha de inicio de cobertura: <input type="text" name="poblacionNuevaDesde" id="poblacionNuevaDesde" value="<?= $poblacionNuevaDesde ?>" <?= $desabil ?>>
                                        </fieldset>

                                    </td>

                                </tr>
                                <tr>
                                    <td colspan="4" align="center" style="border-width:19px 0px 0px; border-style:solid; border-color:transparent;">
                                        <fieldset>
                                            <legend>Compromisos de gesti&oacute;n</legend>
                                            <table> 


                                                <tr>
                                                    <td align="right">
                                                        <b>Compromiso de Gestión:</b>
                                                    </td>
                                                    <td align="left" >
                                                        <select name=com_gestion Style="width:257px" <?= $desabil ?>>
                                                            <option value=-></option>
                                                            <option value=VERDADERO <?
                                                            if (trim($com_gestion) == 'VERDADERO')
                                                                echo "selected"
                                                                ?>>VERDADERO</option>
                                                            <option value=FALSO <?
                                                            if (trim($com_gestion) == 'FALSO')
                                                                echo "selected"
                                                                ?>>FALSO</option>			  
                                                        </select>              
                                                    </td>
                                                </tr>       

                                                <tr>
                                                    <td align="right">
                                                <u><b>Referente con Addenda:</b><u>
                                                        </td>
                                                        <td align="left">		 
                                                            <input type="text" size="40" value="<?= $com_gestion_firmante_actual ?>" name="com_gestion_firmante_actual" <?= $desabil ?>>
                                                        </td>
                                                        </tr>

                                                        <tr>
                                                            <td align="right">
                                                        <u><b>DNI Referente con Addenda:</b><u>
                                                                </td>
                                                                <td align="left">		 
                                                                    <input type="text" size="40" value="<?= $dni_firmante_actual ?>" name="dni_firmante_actual" <?= $desabil ?>>
                                                                </td>
                                                                </tr>


                                                                <tr>
                                                                    <td align="right">
                                                                        <b>Compromiso de Gestion Firmante:</b>
                                                                    </td>
                                                                    <td align="left">		 
                                                                        <input type="text" size="40" value="<?= $com_gestion_firmante ?>" name="com_gestion_firmante" <?= $desabil ?>>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td align="right">
                                                                        <b>Fecha del Compromiso de Gestion:</b>
                                                                    </td>
                                                                    <td align="left">		 
                                                                        <input id="fecha_comp_ges" type="text" size="35" value="<?= fecha($fecha_comp_ges) ?>" name="fecha_comp_ges" <?= $desabil ?>>
                                                                        <?= link_calendario("fecha_comp_ges"); ?>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td align="right">
                                                                        <b>Fecha Fin del Compromiso de Gestion:</b>
                                                                    </td>
                                                                    <td align="left">		 
                                                                        <input id="fecha_fin_comp_ges" type="text" size="35" value="<?= fecha($fecha_fin_comp_ges) ?>" name="fecha_fin_comp_ges" <?= $desabil ?>>
                                                                        <?= link_calendario("fecha_fin_comp_ges"); ?>
                                                                    </td>
                                                                </tr>

                                                                <tr>

                                                                <tr>
                                                                    <td align="right">
                                                                        <b>Compromiso de Gestion Pago Indirecto:</b>
                                                                    </td>
                                                                    <td align="left">
                                                                        <select id="com_gestion_pago_indirecto" name=com_gestion_pago_indirecto Style="width:257px" <?= $desabil ?>>
                                                                            <option value=""></option>
                                                                            <option value=VERDADERO <?
                                                                            if (trim($com_gestion_pago_indirecto) == 'VERDADERO')
                                                                                echo "selected"
                                                                                ?>>VERDADERO</option>
                                                                            <option value=FALSO <?
                                                                            if (trim($com_gestion_pago_indirecto) == 'FALSO')
                                                                                echo "selected"
                                                                                ?>>FALSO</option>			  
                                                                        </select>              
                                                                    </td>
                                                                </tr>  

                                                                <tr>
                                                                    <td align="right">
                                                                        <b>Tercero Administrador:</b>
                                                                    </td>
                                                                    <td align="left">		 
                                                                        <input type="text" size="40" value="<?= $tercero_admin ?>" name="tercero_admin" <?= $desabil ?>>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td align="right">
                                                                        <b>Tercero Administrador Firmante:</b>
                                                                    </td>
                                                                    <td align="left">		 
                                                                        <input type="text" size="40" value="<?= $tercero_admin_firmante ?>" name="tercero_admin_firmante" <?= $desabil ?>>
                                                                    </td>
                                                                </tr>

                                                                <tr>

                                                                    <td align="right">
                                                                        <b>Fecha Tercero Administrador:</b>
                                                                    </td>
                                                                    <td align="left">		 
                                                                        <input id="fecha_tercero_admin" type="text" size="35" value="<?= fecha($fecha_tercero_admin) ?>" name="fecha_tercero_admin" <?= $desabil ?>>
                                                                        <?= link_calendario("fecha_tercero_admin"); ?>
                                                                    </td>
                                                                </tr>   

                                                                <td align="right">
                                                                    <b>Fecha Fin Tercero Administrador:</b>
                                                                </td>
                                                                <td align="left">		 
                                                                    <input id="fecha_fin_tercero_admin" type="text" size="35" value="<?= fecha($fecha_fin_tercero_admin) ?>" name="fecha_fin_tercero_admin" <?= $desabil ?>>
                                                                    <?= link_calendario("fecha_fin_tercero_admin"); ?>
                                                                </td>
                                                                </tr> 


                                                                <tr>

                                                                    <td align="right">
                                                                        <b>Zona Sanitaria:</b>
                                                                    </td>
                                                                    <td align="left">		          			
                                                                        <select name=id_zona_sani Style="width:257px" <?= $desabil ?>>
                                                                            <option value=-></option>
                                                                            <?
                                                                            $sql = "select * from nacer.zona_sani";
                                                                            $res = sql($sql) or fin_pagina();
                                                                            while (!$res->EOF) {
                                                                                $id_nomenclador_detalle_1 = $res->fields['id_zona_sani'];
                                                                                $descripcion = $res->fields['nombre_zona'];
                                                                                ?>                                                            
                                                                                <option value=<?=
                                                                                $id_nomenclador_detalle_1;
                                                                                if ($id_zona_sani == $id_nomenclador_detalle_1)
                                                                                    echo " selected"
                                                                                    ?> >
                                                                                            <?= $descripcion ?>
                                                                                </option>
                                                                                <?
                                                                                $res->movenext();
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </td>
                                                                </tr>

                                                                </table>
                                                                </fieldset>
                                                                </td>      
                                                                </tr> 

                                                                </table>           
                                                                <br>
                                                                <?php if (permisos_check("inicio", "edicion_convenio")){ ?>
                                                                    <table class="bordes" align="center" width="100%">
                                                                        <tr align="center" id="sub_tabla">
                                                                            <td>	
                                                                                Editar DATO
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td align="center">

                                                                                    <input id="eliminar" type="submit" name="eliminar" value="Eliminar" title="Borrar el Convenio" onclick="eliminar_conv()" style="width:130px" <?php echo $desabiledit ?>/> &nbsp;&nbsp;
                                                                                    <input type="submit" name="editar" value="Editar Campos" onClick="this.form.submit()" title="Editar" style="width:130px" <?= $desabiledit ?>/> &nbsp;&nbsp;
                                                                                    <input type="submit" name="guardar_editar" value="Guardar" onclick="return control_nuevos();" title="Guarda Muleto" <?= $desabil ?> style="width:130px" />&nbsp;&nbsp;
                                                                                    <input type="submit" name="cancelar_editar" value="Cancelar" title="Cancela Edicion de Muletos" <?= $desabil ?> style="width:130px" onclick=""/>		      		      

                                                                            </td>
                                                                        </tr> 
                                                                    </table>
                                                                <? } ?>
                                                                <br>

                                                                <tr>
                                                                    <td>
                                                                        <table width=100% align="center" class="bordes">
                                                                            <tr align="center">
                                                                                <td>
                                                                                    <input type=button name="volver" value="Volver" onclick="document.location = 'efectores_unif.php'"title="Volver al Listado" style="width:150px">     
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                </table>
                                                                </td>
                                                                </tr>
                                                                </table> 
                                                                </form>

                                                                <?=
                                                                fin_pagina(); // aca termino ?>