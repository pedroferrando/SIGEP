<?
require_once ("../../config.php");
require_once ("../../lib/bibliotecaTraeme.php");
require_once ("../../lib/funciones_misiones.php");
require_once ("../../clases/Smiefectores.php");


extract($_POST, EXTR_SKIP);
if ($parametros)
    extract($parametros, EXTR_OVERWRITE);
//cargar_calendario();

if ($marcar == "True") {
    $db->StartTrans();
    $query = "update facturacion.comprobante set
             marca=1
             where id_comprobante=$id_comprobante";

    sql($query, "Error al marcar el comprobante") or fin_pagina();
   
    /*se coloca eliminado=1 a todas las vacunas cargadas 
    en inmunizacion.prestaciones_inmu que pertenecen a este comprobante */
    
    $query_inmu = "update inmunizacion.prestaciones_inmu set eliminado=1
             where id_comprobante=$id_comprobante";
    sql($query_inmu, "Error al marcar vacuna como eliminada") or fin_pagina(); 
    
    $accion = "Se marco el Comprobante Numero: $id_comprobante, como anulado";
    /* cargo los log */
    $usuario = $_ses_user['name'];
    $fecha_carga = date("Y-m-d H:i:s");
    $log = "insert into facturacion.log_comprobante 
		   (id_comprobante, fecha, tipo, descripcion, usuario) 
	values ($id_comprobante, '$fecha_carga','Comprobante Anulado','Nro. Comprobante $id_comprobante', '$usuario')";
    sql($log) or fin_pagina();

    $db->CompleteTrans();
}

if (($_POST['guardar'] == "Guardar Comprobante") || ($_POST['guardar'] == "Guardar Comprobante y Facturar")) {

    $fecha_carga = date("Y-m-d H:i:s");
    $cuie = $_POST['efector'];
    $nom_medico = $_POST['nom_medico'];
    $fecha_comprobante = $_POST['fecha_comprobante'];
    $comentario = $_POST['comentario'];
    $fecha_comprobante = Fecha_db($fecha_comprobante);
    $entidad_alta = $_POST['entidad_alta'];

    $sql_conv = "SELECT nd.id_nomenclador_detalle FROM nacer.efe_conv n
                        INNER JOIN nacer.conv_nom cn USING(id_efe_conv)
                        INNER JOIN facturacion.nomenclador_detalle nd on (cn.id_nomenclador_detalle=nd.id_nomenclador_detalle)
                        WHERE n.cuie='$cuie'
                        AND '$fecha_comprobante' BETWEEN fecha_desde AND fecha_hasta";
    $id_nomenclador_detalle = sql($sql_conv) or die;
    $id_nomenclador_detalle = $id_nomenclador_detalle->fields['id_nomenclador_detalle'];

    if ($tipo_prestacion == "RONDAS") {
//busca la fecha de nacimiento para calcular grupoetario por dni
        $beneficiario_esta_activo = datosAfiliadoEnUad('95614');
    }

    $idperiodo = buscarPeriodo(Fecha_db($fecha_comprobante));
    $periodo=$idperiodo['periodo'];
    $idperiodo = $idperiodo['id'];
    

    $grupo_etario = calcularGrupoEtareo(Fecha_db($d), $fecha_comprobante);
    $grupo_etario = $grupo_etario['categoria'];
    $db->StartTrans();

    $q = "select nextval('facturacion.comprobante_id_comprobante_seq') as id_comprobante";
    $id_comprobante = sql($q) or fin_pagina();
    $id_comprobante = $id_comprobante->fields['id_comprobante'];

    $query = "insert into facturacion.comprobante
              (id_comprobante,cuie, nombre_medico, fecha_comprobante, clavebeneficiario, fecha_carga,periodo,comentario,activo,entidad_alta,id_nomenclador_detalle,tipo_nomenclador,idperiodo,grupo_etario,usuario)
	      values
	      ($id_comprobante,'$cuie','$nom_medico','$fecha_comprobante','$clavebeneficiario', '$fecha_carga','$periodo','$comentario','S','$entidad_alta',$id_nomenclador_detalle,'$tipo_prestacion',$idperiodo,'$grupo_etario'," . $_ses_user['id'] . ")";
    sql($query, "Error al insertar el comprobante") or fin_pagina();
    $accion = "Se guardo el Comprobante.";     /* cargo los log */

    $usuario = $_ses_user['name'];
    $log = "insert into facturacion.log_comprobante 
	    (id_comprobante, fecha, tipo, descripcion, usuario) 
	    values ($id_comprobante, '$fecha_carga','Nuevo Comprobante','Nro. Comprobante $id_comprobante', '$usuario')";
    sql($log) or fin_pagina();
    $db->CompleteTrans();
//$db->RollbackTrans();
    if ($_POST['guardar'] == "Guardar Comprobante y Facturar") {
        $ref = encode_link("prestacion_admin_2011.php", array("id_nomenclador_detalle" => $res_comprobante->fields['id_nomenclador_detalle'], "clavebeneficiario" => $clavebeneficiario, "id_comprobante" => $id_comprobante, "pagina_viene" => "comprobante_admin_total.php", "pagina_listado" => $pagina_listado, "entidad_alta" => $entidad_alta));
        echo "<SCRIPT>window.location='$ref';</SCRIPT>";
        exit();
    }
}//de if ($_POST['guardar']=="Guardar nuevo Muleto")


$sql = "select 
            uad.beneficiarios.id_beneficiarios as id,
            trim(uad.beneficiarios.apellido_benef||' '||CASE WHEN uad.beneficiarios.apellido_benef_otro is null THEN '' else uad.beneficiarios.apellido_benef_otro end) as a,
                trim(uad.beneficiarios.nombre_benef||' '||CASE WHEN uad.beneficiarios.nombre_benef_otro is null THEN '' else uad.beneficiarios.nombre_benef_otro end) as b,
                uad.beneficiarios.numero_doc as c,
            uad.beneficiarios.fecha_nacimiento_benef as d,
            uad.beneficiarios.calle as e
            from uad.beneficiarios
            where clave_beneficiario='$clavebeneficiario'";
$res_comprobante = sql($sql, "Error al traer los Comprobantes") or fin_pagina();


$apellido = $res_comprobante->fields['a'];
$nombre = $res_comprobante->fields['b'];
$dni = $res_comprobante->fields['c'];
$fechanac = $res_comprobante->fields['d'];
$localidad = $res_comprobante->fields['e'];
$efector = new Smiefectores($cuie_elegido);


echo $html_header;
?>
<link rel='stylesheet' href='../../lib/jquery/ui/jquery-ui.css'/>


<form name='form1' action='comprobante_admin_total.php' method='POST'>
    <input type="hidden" name="pagina_viene" value="<?= $fecha_viene ?>">
    <input type="hidden" name="pagina_listado" value="<?= $pagina_listado ?>">
    <input type="hidden" name="clavebeneficiario" value="<?= $clavebeneficiario ?>">

    <? echo "<center><b><font size='+2' color='red'>$accion</font></b></center>"; ?>

    <input type="hidden" name="entidad_alta" value="<?= $entidad_alta ?>">
    <table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?= $bgcolor_out ?>' class="bordes">
        <tr id="mo">
            <td>
                <font size=+1><b>CARGA DE COMPROBANTE DE PRESTACIONES</b></font>    
            </td>
        </tr>
        <tr>
            <td>
                <table width=70% align="center" class="bordes">
                    <tr>
                        <td id=mo colspan="2">
                            <b> Descripci&oacute;n del Beneficiario</b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table>

                                <tr>
                                    <td align="right">
                                        <b>Apellido:
                                    </td>         	
                                    <td align='left'>
                                        <input type='text' name='a' value='<?= $apellido; ?>' size=60 align='right' readonly></b>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <b> Nombre:
                                    </td>   
                                    <td  colspan="2">
                                        <input type='text' name='b' value='<?= $nombre; ?>' size=60 align='right' readonly></b>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <b> Documento:
                                    </td> 
                                    <td colspan="2">
                                        <input type='text' name='c' value='<?= $dni; ?>' size=60 align='right' readonly></b>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <b> Fecha de Nacimiento:
                                    </td> 
                                    <td colspan="2">
                                        <input type='text' name='d' value='<?= Fecha($fechanac); ?>' size=60 align='right' readonly></b>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <b> Domicilio:
                                    </td> 
                                    <td colspan="2">
                                        <input type='text' name='e' value='<?= $localidad; ?>' size=60 align='right' readonly></b>
                                    </td>
                                </tr>

                            </table>
                        </td>      
                    </tr>
                </table>     
                <table class="bordes" align="center" width="70%">
                    <tr align="center" id="sub_tabla">
                        <td colspan="2">	
                            Datos del Comprobante
                        </td>
                    </tr>
                    <tr>
                        <td class="bordes">
                            <table>
                                <tr>
                                    <td>
                                <tr>
                                    <td align="right">
                                        <b>Efector:</b>
                                    </td>
                                    <td align="left">		          			
                                        <select id="efector" name=efector Style="width:450px"
                                                onKeypress="buscar_combo(this);"
                                                onblur="borrar_buffer();"
                                                onchange="borrar_buffer();">
                                            <option value=-1>Seleccione</option>
                                            <?
                                            $id_usuario = $_ses_user['id'];
                                            if ($id_usuario)
                                                $sql = "select distinct(n.cuie), nombreefector, upper(trim(com_gestion)) as com_gestion 
                                                                        from nacer.efe_conv n 
                                                                        inner join nacer.conv_nom cn using(id_efe_conv)
                                                                        inner join facturacion.smiefectores s on n.cuie=s.cuie 
                                                                        inner join sistema.usu_efec ue on ue.cuie=n.cuie
                                                                        where id_usuario='$id_usuario'";
                                            else {
                                                $sql = "select cn.id_nomenclador_detalle,s.cuie, nombreefector, upper(trim(com_gestion)) as com_gestion
                                                            from nacer.efe_conv n 
                                                            inner join nacer.conv_nom cn using(id_efe_conv)
                                                            inner join facturacion.smiefectores s on n.cuie=s.cuie
                                                            where cn.activo='t' and n.activo='t'
                                                            order by nombreefector";
                                            }
                                            $res_efectores = sql($sql) or fin_pagina();

                                            while (!$res_efectores->EOF) {
                                                $com_gestion = $res_efectores->fields['com_gestion'];
                                                $cuie = $res_efectores->fields['cuie'];
                                                $nombre_efector = $res_efectores->fields['nombreefector'];
                                                ($com_gestion == 'FALSO') ? $color_style = '#F78181' : $color_style = '';
                                                ?>
                                                <option style="background-color: <?= $color_style ?>" value=<?= $cuie; ?> ><?= $cuie . " - " . $nombre_efector ?></option>
                                                <?
                                                $res_efectores->movenext();
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <b>Tipo de Nomenclador:</b>
                                    </td>
                                    <td align="left" id="td_tipo_prestacion">
                                        <select id="tipo_prestacion" name=tipo_prestacion Style="width:450px">
                                            <option value=-1>Seleccione</option>
                                            <?php //carga el combo con los tipo posibles de prestaciones 
                                                  //que puede cargar segun el convenio
                                                $tipos_de_nomenclador = $efector->tiposDeNomenclador();
                                                var_dump($tipos_de_nomenclador);
                                                if($tipos_de_nomenclador && $cuie_elegido!=""):
                                                    foreach ($tipos_de_nomenclador as $key => $value):
                                                        ?>
                                                        <option value=<?php echo "'$key'"; ?> >
                                                            <?php echo $value ?>
                                                        </option>
                                                        <?php
                                                    endforeach;
                                                endif; 
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <b>Nombre Medico:</b>
                                    </td>
                                    <td align="left">
                                        <input type="text" value="" name="nom_medico" Style="width:450px">
                                    </td>		    
                                </tr>	
                                <tr>
                                    <td align="right">
                                        <b>Fecha Prestaci&oacute;n:</b>
                                    </td>
                                    <td align="left">
                                        <input type=text id=fecha_comprobante name=fecha_comprobante value='<?= $fecha_comprobante; ?>' size=15 readonly>                                        				    	 
                                    </td>		    
                                </tr>
                                <tr>
                                    <td align="right">
                                        <b>Comentario:</b>
                                    </td>         	
                                    <td align='left'>
                                        <textarea cols='70' rows='3' name='comentario' <?
                                        if ($id_planilla)
                                            echo "readonly"
                                            ?>></textarea>
                                    </td>
                                </tr>   					 
                        </td>
                    </tr>
                </table>
            </td>
        </tr>	 
        <tr>
            <td align="center" colspan="2" class="bordes">		      
                <!-- <input type="submit" name="guardar" value="Guardar Comprobante" title="Guardar Comprobante" Style="width:250px;height:30px" onclick="return control_nuevos()"> -->
                &nbsp;&nbsp;&nbsp;
                <input type="submit" name="guardar" value="Guardar Comprobante y Facturar" title="Guardar y Cargar Prestaciones" Style="width:250px;height:30px" onclick="return control_nuevos()">
            </td>
        </tr> 
    </table>	
</td>
</tr>

<?
$query = "SELECT 
            c.id_comprobante,
            facturacion.smiefectores.nombreefector,
            c.nombre_medico,
            c.fecha_comprobante,
            c.id_factura,
            c.marca,
            c.periodo,
            c.id_nomenclador_detalle,
            c.tipo_nomenclador
            FROM
            facturacion.comprobante c
            INNER JOIN facturacion.smiefectores ON (c.cuie = facturacion.smiefectores.cuie)
            where c.clavebeneficiario='" . $clavebeneficiario . "'
                order by c.fecha_comprobante DESC";
$res_comprobante = sql($query, "<br>Error al traer los comprobantes<br>") or fin_pagina();
?>
<tr>
    <td>
        <table width="100%" class="bordes" align="center">
            <tr align="center" id="mo">
                <td align="center" width="3%">
                    <img id="imagen_2" src="../../imagenes/rigth2.gif" border=0 title="Mostrar Comprobantes" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.prueba_vida, 2);" >
                </td>
                <td align="center">
                    <b>Comprobantes</b>
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
                        <font size="3" color="Red"><b>No existen comprobantes para este beneficiario</b></font>
                    </td>
                </tr>
                <?
            } else {
                ?>
                <tr id="sub_tabla">	
                    <td width=1%>&nbsp;</td>
                    <td width="10%">Numero de Comprobante</td>
                    <td width="40%">Efector</td>
                    <td width="20%">Medico</td>
                    <td width="9%">Fecha Prestaci&oacute;n</td>	 		
                    <td width="10%">Cant Prestaciones</td>
                    <td width="10%">Tipo de Nomenclador</td>
                    <td width="10%">Periodo</td>
                    <td width="10%">Anular</td>
                </tr>
                <?
                $res_comprobante->movefirst();
                //cuenta los comprobantes para identificar 
                $i = 0;
                while (!$res_comprobante->EOF) {
                    if ($res_comprobante->fields['id_factura'] == "") {
                        //consulta para saber si tiene pretaciones el comprobante
                        $sql = "select sum(cantidad) as cant_prestaciones
                            from facturacion.prestacion 								
                            where id_comprobante=" . $res_comprobante->fields['id_comprobante'];
                        $cant_prestaciones = sql($sql, "no se puede traer la cantidad de prestaciones") or die();
                        $cant_prestaciones = $cant_prestaciones->fields['cant_prestaciones'];



                        $color_fondo = "#FFFFCC";

                        if ($cant_prestaciones == 1 && ($res_comprobante->fields['tipo_nomenclador'] == 'PERINATAL_CATASTROFICO' || $res_comprobante->fields['tipo_nomenclador'] == 'PERINATAL_NO_CATASTROFICO')) {
                            $onclick_elegir = "";
                        } else {
                            $ref = encode_link("prestacion_admin_2011.php", array("id_nomenclador_detalle" => $res_comprobante->fields['id_nomenclador_detalle'], "clavebeneficiario" => $clavebeneficiario, "id_comprobante" => $res_comprobante->fields['id_comprobante'], "pagina_viene" => "comprobante_admin_total.php", "pagina_listado" => $pagina_listado, "entidad_alta" => $entidad_alta));
                            $onclick_elegir = "location.href='$ref'";
                        }
                        if ($res_comprobante->fields['marca'] == 0) {
                            $ref1 = encode_link("comprobante_admin_total.php", array("id_comprobante" => $res_comprobante->fields['id_comprobante'], "marcar" => "True", "clavebeneficiario" => $clavebeneficiario));
                            $id_comprobante_aux = $res_comprobante->fields['id_comprobante'];
                            $onclick_marcar = "if (confirm('Esta Seguro que Desea ANULAR Comprobante $id_comprobante_aux?')) location.href='$ref1'
            						else return false;	";
                        } else {
                            $onclick_marcar = "";
                            $onclick_elegir = "";
                        }
                    } else {
                        $color_fondo = "FF9999";
                        $onclick_elegir = "";
                        $onclick_marcar = "";
                    }

                    if ($res_comprobante->fields['marca'] == 1) {
                        $color_fondo = "AA888";
                    }

                    $id_tabla = "tabla_" . $i;
                    ?>
                    <tr bgcolor=#CFE8DD onmouseover="this.style.backgroundColor = '#82B39E';
                this.style.color = '#000000'" onmouseout="this.style.backgroundColor = '#CFE8DD';
                this.style.color = '#000000'">
                        <td>
                            <img id="imagen_ver_practicas<?= $i ?>" src="../../imagenes/rigth2.gif" name=check_prestacion value="" onclick="muestra_tabla_practicas(<?= $i ?>);">
                        </td>	
                        <td onclick="<?= $onclick_elegir ?>" bgcolor='<?= $color_fondo ?>'>
                            <font size="3" color="Red">
                            <b><?= $res_comprobante->fields['id_comprobante'] . "(" . $res_comprobante->fields['id_factura'] . ")" ?></b>
                            </font>
                        </td>
                        <td onclick="<?= $onclick_elegir ?>">
                            <?= $res_comprobante->fields['nombreefector'] ?>
                        </td>
                        <td onclick="<?= $onclick_elegir ?>">
                            <?
                            if ($res_comprobante->fields['nombre_medico'] != "")
                                echo $res_comprobante->fields['nombre_medico'];
                            else
                                echo "&nbsp"
                                ?>
                        </td>
                        <td onclick="<?= $onclick_elegir ?>">
                            <?= fecha($res_comprobante->fields['fecha_comprobante']) ?>
                        </td>		 		
                        <td onclick="<?= $onclick_elegir ?>">
                            <?= "Total: " . $cant_prestaciones ?>
                        </td>
                        <td onclick="<?= $onclick_elegir ?>">
                            <?= $res_comprobante->fields['tipo_nomenclador'] ?>
                        </td>
                        <td onclick="<?= $onclick_elegir ?>">
                            <?= $res_comprobante->fields['periodo'] ?>
                        </td>		 		
                        <td onclick="<?= $onclick_marcar ?>" align="center">
                            <?
                            if ($res_comprobante->fields['marca'] == 1) {
                                echo "<img src='../../imagenes/salir.gif' style='cursor:pointer;'>";
                            } else if ($res_comprobante->fields['id_factura'] != "") {
                                echo "Facturado";
                            }
                            else
                                echo "<img src='../../imagenes/sin_desc.gif' style='cursor:pointer;'>"
                                ?>
                        </td>		 		
                    </tr>	
                    <tr id=<?= $id_tabla ?> style='display:none'>
                        <td colspan=9>

                            <?
                            $sql = "select codigo,diagnostico,descripcion,cantidad,precio_prestacion
                                    from facturacion.prestacion
                                    inner join facturacion.nomenclador using (id_nomenclador)
                                    where id_comprobante=" . $res_comprobante->fields['id_comprobante'] . " 
                                    order by id_prestacion DESC";
                            $result_items = sql($sql) or fin_pagina();
                            ?>
                            <table width=80% align=center class=bordes>
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
                                        <td width="33%">Codigo</td>
                                        <td width="33%">Descripción</td>
                                        <td width="33%">Precio</td>	                               
                                    </tr>
                                    <? while (!$result_items->EOF) { ?>
                                        <tr>			                                 
                                            <td style='border-bottom: thin black solid;border-left: thin black solid;border-right: thin black solid'><?= $result_items->fields["codigo"] . " " . $result_items->fields["diagnostico"] ?></td>
                                            <? $grupo_etareo = calcularGrupoEtareo($fechanac, $res_comprobante->fields['fecha_comprobante']) ?>
                                            <td style='border-bottom: thin black solid;border-right: thin black solid'><?= descripcionDeDiagnostico($result_items->fields["codigo"], $result_items->fields["diagnostico"], $grupo_etareo['categoria']); ?></td>
                                            <td style='border-bottom: thin black solid;border-right: thin black solid'><?= number_format(($result_items->fields["precio_prestacion"] * $result_items->fields["cantidad"]), 2, ',', '.') ?></td>
                                        </tr>
                                        <?
                                        $result_items->movenext();
                                    }//del while
                                }//del else
                                ?>

                            </table>
                        </td>
                    </tr>  	
                    <?
                    $i++;
                    $res_comprobante->movenext();
                }
            }
            ?>
        </table>
    </td>
</tr>

<tr>
    <td>
        <table width=100% align="center" class="bordes">
            <tr align="center">
                <td>
                    <?php
                        $lnkVolver = "listado_beneficiarios_fact.php";
                        if($pagina_listado == "listado_beneficiarios_hist.php")
                            $lnkVolver = "listado_beneficiarios_hist.php";
                        if($pagina_listado == "listado_beneficiarios_leche.php")
                            $lnkVolver = "../entrega_leche/listado_beneficiarios_leche.php";
                        if($pagina_listado == "ins_listado.php")
                            $lnkVolver = "../inscripcion/ins_listado.php";
                    ?>
                    <input type=button name="volver" value="Volver" 
                           onclick="document.location = '<?php echo $lnkVolver; ?>'" 
                           title="Volver al Listado" style="width:150px">
                </td>
            </tr>
        </table>
    </td>
</tr>
</table>
</form>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script src='../../lib/jquery/ui/jquery.ui.datepicker-es.js' type='text/javascript'></script>
<script>
    $(document).ready(function() {
        $("#fecha_comprobante").datepicker({minDate: -180, maxDate: "+0D"});


        $('select#efector').on('change', function() {
            var cuie = $('#efector').val();
            $.post("tiposdenomencladores.php", {cuie: cuie}, function(data) {
                $('#td_tipo_prestacion').html(data);
            })
        });
    });

    //controlan que ingresen todos los datos necesarios par el muleto
    function control_nuevos()
    {
        if (document.all.efector.value == "-1") {
            alert('Debe Seleccionar un EFECTOR');
            return false;
        }
        if (document.all.tipo_prestacion.value == "-1") {
            alert('Debe Seleccionar un TIPO DE NOMENCLADOR');
            return false;
        }
        if (document.all.periodo.value == "-1") {
            alert('Debe Seleccionar un PERIODO');
            return false;
        }

        if (confirm('Esta Seguro que Desea Agregar Comprobante?'))
            return true;
        else
            return false;
    }//de function control_nuevos()

    var img_ext = '<?= $img_ext = '../../imagenes/rigth2.gif' ?>';//imagen extendido
    var img_cont = '<?= $img_cont = '../../imagenes/down2.gif' ?>';//imagen contraido

    function muestra_tabla(obj_tabla, nro) {
        oimg = eval("document.all.imagen_" + nro);//objeto tipo IMG
        if (obj_tabla.style.display == 'none') {
            obj_tabla.style.display = 'inline';
            oimg.show = 0;
            oimg.src = img_ext;
        }
        else {
            obj_tabla.style.display = 'none';
            oimg.show = 1;
            oimg.src = img_cont;
        }
    }

    function muestra_tabla_practicas(nro) {
        oimg = $("#imagen_ver_practicas" + nro);//objeto tipo IMG

        var obj = $("#tabla_" + nro);
        if (obj.css("display") == 'none') {
            obj.css("display", "table-row");
            oimg.attr('src', img_cont);
        } else {
            obj.css("display", "none");
            oimg.attr('src', img_ext);
        }
    }


    /**********************************************************/
    //funciones para busqueda abreviada utilizando teclas en la lista que muestra los clientes.
    var digitos = 10; //cantidad de digitos buscados
    var puntero = 0;
    var buffer = new Array(digitos); //declaraciï¿½n del array Buffer
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
        event.returnValue = false; //invalida la acciï¿½n de pulsado de tecla para evitar busqueda del primer caracter
    }//de function buscar_op_submit(obj)



</script>
<?= fin_pagina(); // aca termino            ?>
