<?
require_once ("../../config.php");


extract($_POST, EXTR_SKIP);
if ($parametros)
    extract($parametros, EXTR_OVERWRITE);
cargar_calendario();

if ($_POST['quitarpractica']) {
    $pract = $_POST['quitarpractica'];
    $usuario = $_ses_user['id'];
    $fecha_carga = date("Y-m-d H:i:s");
    
    $log_excluidos = "INSERT INTO nacer.log_excluidos(
                      id_excluidos, id_conv_nom, usuario, cod_practica, fecha_carga, 
                      neo, cero_a_uno, uno_a_seis, seis_a_diez, diez_a_veinte, veinte_a_sesentaycuatro, 
                      f, m, embarazada, usr_elimina, fecha_elimina)
                      SELECT id_excluidos, id_conv_nom, usuario, cod_practica, fecha_carga, 
                      neo, cero_a_uno, uno_a_seis, seis_a_diez, diez_a_veinte, veinte_a_sesentaycuatro, 
                      f, m, embarazada,$usuario,'$fecha_carga'
                      FROM nacer.excluidos 
                      WHERE id_excluidos = $pract ";
    sql($log_excluidos, "Error al insertar la prestacion") or fin_pagina();  
    
    $queryborroexclusion = "DELETE FROM nacer.excluidos
                            WHERE id_excluidos = $pract ";
    sql($queryborroexclusion, "Error al insertar la prestacion") or fin_pagina();
    
    
}

if ($_POST['seleccion']) {
    $fecha_carga = date("Y-m-d H:i:s");
    $id_prestacion = $_POST['nomenclador'];
    $usuario = $_ses_user['name'];

    if ($_POST['gr_neo']) {
        $gr_neo = 't';
    } else {
        $gr_neo = 'f';
    }
    if ($_POST['gr_cero_a_uno']) {
        $gr_cero_a_uno = 't';
    } else {
        $gr_cero_a_uno = 'f';
    }
    if ($_POST['gr_uno_a_seis']) {
        $gr_uno_a_seis = 't';
    } else {
        $gr_uno_a_seis = 'f';
    }

    if ($_POST['gr_seis_a_diez']) {
        $gr_seis_a_diez = 't';
    } else {
        $gr_seis_a_diez = 'f';
    }

    if ($_POST['gr_diez_a_veinte']) {
        $gr_diez_a_veinte = 't';
    } else {
        $gr_diez_a_veinte = 'f';
    }

    if ($_POST['gr_veinte_a_sesentaycuatro']) {
        $gr_veinte_a_sesentaycuatro = 't';
    } else {
        $gr_veinte_a_sesentaycuatro = 'f';
    }

    if ($_POST['gr_embarazada']) {
        $gr_embarazada = 't';
    } else {
        $gr_embarazada = 'f';
    }

    if ($_POST['fem']) {
        $fem = 't';
    } else {
        $fem = 'f';
    }

    if ($_POST['masc']) {
        $masc = 't';
    } else {
        $masc = 'f';
    }

    if ($id_prestacion != '-1') {
        $db->StartTrans();
        $q = "SELECT  cn.id_conv_nom
              FROM  nacer.conv_nom cn  
              INNER JOIN nacer.efe_conv ec USING (id_efe_conv)
              WHERE ec.CUIE = '$cuie' 
              AND ec.activo=TRUE AND cn.activo=TRUE";
        $conv_nom = sql($q);
        $conv_nom = $conv_nom->fields['id_conv_nom'];

        $query = "INSERT INTO nacer.excluidos
	          (id_conv_nom,cod_practica, usuario, fecha_carga,neo,cero_a_uno,uno_a_seis,seis_a_diez,diez_a_veinte,veinte_a_sesentaycuatro,f,m,embarazada)
	          VALUES($conv_nom,$id_prestacion,'$usuario','$fecha_carga','$gr_neo','$gr_cero_a_uno','$gr_uno_a_seis','$gr_seis_a_diez','$gr_diez_a_veinte','$gr_veinte_a_sesentaycuatro','$fem','$masc','$gr_embarazada')";
        sql($query, "Error al insertar la Pr&aacute;ctica exclu&iacute;da") or fin_pagina();
        $accion = "Se excluy&oacute; la prestacion";

        $db->CompleteTrans();
    }//de if ($_POST['guardar']=="Guardar nuevo Muleto")
}

//Cargar las practicas
$sql3 = "SELECT nd.modo_facturacion, cn.id_nomenclador_detalle, cn.id_conv_nom, 
         nd.descripcion ,nd.fecha_desde,nd.fecha_hasta
         FROM facturacion.nomenclador_detalle  nd
         INNER JOIN nacer.conv_nom cn USING (id_nomenclador_detalle)
         INNER JOIN nacer.efe_conv ec USING (id_efe_conv)		 
         WHERE ec.CUIE = '$cuie' 
         AND ec.activo=TRUE AND cn.activo=TRUE";
$res_modo_facturacion = sql($sql3) or fin_pagina();
if ($res_modo_facturacion->RowCount() > 0) {
    $nom_detalle = $res_modo_facturacion->fields['id_nomenclador_detalle'];
    $conv_nom = $res_modo_facturacion->fields['id_conv_nom'];
    $descripcion = $res_modo_facturacion->fields['descripcion'];
    $fechadesde = $res_modo_facturacion->fields['fecha_desde'];
    $fechahasta = $res_modo_facturacion->fields['fecha_hasta'];
    //Segun el modo de facturacion carga las practicas

    $sql = "SELECT n.id_nomenclador as id,n.codigo,n.tipo_nomenclador,n.descripcion  AS nombre_nom,diagnostico
	    FROM facturacion.nomenclador n
	    WHERE n.id_nomenclador_detalle = '$nom_detalle'
	    AND n.id_nomenclador not in (SELECT cod_practica 
                                         FROM nacer.excluidos 
                                         WHERE id_conv_nom=$conv_nom
                                         and n.id_nomenclador_detalle = '$nom_detalle')
	    AND n.habilitado = TRUE 
            ORDER BY n.codigo,diagnostico";

    $res_efectores = sql($sql) or fin_pagina();
}

echo $html_header;

echo "<script src='../../lib/jquery.min.js' type='text/javascript'></script>";
?>
<script>
    var quitarpractica;

    $('document').ready(function() {
        $('#prueba_vida').delegate('.quitar', 'click', function(e) {
            e.preventDefault();
            quitarpractica = this.name;
            quitar();
        });

        $('#selectnomenclador').on('change', function() {
            var codigo = $('#selectnomenclador :selected').text();
            codigo = $.trim(codigo);
            $('#seleccion').val(codigo);

        });

    });


    function quitar() {

        var cuie = $("#cuie").val();
        var nombreefector = $("#nombreefector").val();

        var confirmar = confirm('Esta Seguro que Desea Excluir la Prestacion?');
        if (confirmar) {
            $.post("efec_nom_admin.php", {'quitarpractica': quitarpractica, 'cuie': cuie, 'nombreefector': nombreefector}, function(data) {
                var otratabla = $(data).find('#tabla_select tbody');
                $('#tabla_select').empty();
                $('#tabla_select').append(otratabla);
                var tabla = $(data).find('#prueba_vida tbody');
                $('#prueba_vida').empty();
                $('#prueba_vida').append(tabla);

            });
        }
    }


    //controlan que ingresen todos los datos necesarios par el muleto
    function control_nuevos()
    {
        if (document.all.nomenclador.value == "-1") {
            alert('Debe Seleccionar una PRESTACION');
            return false;
        }
        var restaurarpractica = $('#selectnomenclador').val();
        var cuie = $("#cuie").val();
        if (confirm('Esta Seguro que Desea Excluir la Prestacion?')) {
            //            $.post("efec_nom_admin.php",{'restaurarpractica':restaurarpractica,'cuie':cuie},function(data){
            //                var otratabla = $( data ).find( '#tabla_select tbody' );
            //                $('#tabla_select').empty();
            //                $('#tabla_select').append(otratabla);
            //                var tabla = $( data ).find( '#prueba_vida tbody' );
            //                $('#prueba_vida').empty();
            //                $('#prueba_vida').append(tabla);
            //                
            //            });
            document.forms[0].submit();
        }
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

    /**********************************************************/
    //funciones para busqueda abreviada utilizando teclas en la lista que muestra los clientes.
    var digitos = 10; //cantidad de digitos buscados
    var puntero = 0;
    var buffer = new Array(digitos); //declaraci�n del array Buffer
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
        event.returnValue = false; //invalida la acci�n de pulsado de tecla para evitar busqueda del primer caracter
    }//de function buscar_op_submit(obj)

</script>

<form id='form1' name='form1' action='efec_nom_admin.php' method='POST' accept-charset=utf-8>

    <? echo "<center><b><font size='+1' color='red'>$accion</font></b></center>"; ?>

    <input id="cuie" type="hidden" name="cuie" value="<?= $cuie ?>">
    <input id="nombreefector" type="hidden" name="nombreefector" value="<?= $nombreefector ?>">
    <table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?= $bgcolor_out ?>' class="bordes">
        <tr id="mo">
            <td>
                <font size=+1><b>Efector</b></font>    
            </td>
        </tr>
        <tr>
            <td>
                <table width=70% align="center" class="bordes">
                    <tr>
                        <td id=mo colspan="2">
                            <b> Descripci&oacute;n del Efector</b>
                        </td>
                    </tr>
                    <tr align="center">
                        <td>
                            <table>
                                <tr align="center">
                                    <td align="center" colspan="2">
                                        <font size="+1" color="Red"><?= $cuie ?> - <?= $nombreefector ?></font>
                                    </td>         	
                                </tr>
       
                            </table>
                        </td>      
                    </tr>
                </table>     
                <table class="bordes" align="center" width="70%">
                    <tr align="center" id="sub_tabla">
                        <td colspan="2">	
                            C&oacute;digos del Nomenclador Vigente [<? echo $descripcion ?> - 
                            Desde: <? echo $fechadesde ?> 
                            / Hasta: <? echo $fechahasta ?>] 
                        </td>
                    </tr>
                    <tr align="center">
                        <td class="bordes">
                            <table id="tabla_select" >
                                <tr align="center">
                                    <td colspan="2">
                                        <b>C&oacute;digos:</b>
                                        <select id="selectnomenclador" name=nomenclador Style="width:300px"
                                                onKeypress="buscar_combo(this);"
                                                onblur="borrar_buffer();"
                                                onchange="borrar_buffer();">
                                            <option value=-1>Seleccione</option>
                                            <?
                                            //Deberia tomar en cuenta el modo de facturacion antes de buscar las practicas de grupo_prestacion o nomencladores?

                                            while (!$res_efectores->EOF) {
                                                $id = $res_efectores->fields['id'];
                                                $codigo = $res_efectores->fields['codigo'];
                                                $nombre_nom = $res_efectores->fields['nombre_nom'];
                                                $descripcion = $nom_detalle;
                                                $diagnostico = $res_efectores->fields['diagnostico'];
                                                $tipo_nomenclador = $res_efectores->fields['tipo_nomenclador'];
                                                ?>
                                                <option value=<?= $id; ?> >
                                                    <?= $codigo . " - " . $diagnostico . ' [' . $tipo_nomenclador . ']' ?>
                                                </option>
                                                <?
                                                $res_efectores->movenext();
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td width="30%" align="center" style="border: solid thin black;">
                            C&oacute;digo Seleccionado <input style="margin-top: 10px" type="text" name="seleccion" id="seleccion" <? echo "readonly" ?> value=""></input> <br /><br /> 
                            <b>Grupo Etario:</b>  <br />                                     
                            Neonato <input style="margin-right: 10px" type="checkbox" name=gr_neo value="S" checked='checked'/> 
                            Menor de 1 <input style="margin-right: 10px" type="checkbox" name=gr_cero_a_uno value="S" checked='checked' /> 
                            1 a 6 <input style="margin-right: 10px" type="checkbox" name=gr_uno_a_seis value="S" checked='checked'/> 
                            6 a 10 <input style="margin-right: 10px" type="checkbox" name=gr_seis_a_diez value="S" checked='checked'/> 
                            10 a 20 <input style="margin-right: 10px" type="checkbox" name=gr_diez_a_veinte value="S" checked='checked'/>
                            20 a 64 <input style="margin-right: 10px" type="checkbox" name=gr_veinte_a_sesentaycuatro value="S" checked='checked'/>
                            Embarazadas <input style="margin-right: 10px" type="checkbox" name=gr_embarazada value="S" checked='checked'/>
                            <br /><br /> <b>Sexo:</b>  <br />                                     
                            Femenino <input style="margin-right: 10px" type="checkbox" name=fem value="S" checked='checked'/>
                            Masculino <input style="margin-right: 10px" type="checkbox" name=masc value="S" checked='checked'/>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="2" class="bordes">		      
                            <input type="button" name="guardar" value="Excluir Prestacion" title="Excluir Prestacion" Style="width:150px" onclick="return control_nuevos();">
                        </td>
                    </tr> 
                </table>	
            </td>
        </tr>

        <?
//tabla de excluidos
        $id_conv_nom = $res_modo_facturacion->fields['id_conv_nom'];

        $queryexcluidos = "SELECT n.codigo, e.fecha_carga, e.usuario, n.descripcion AS nombre_nom, e.id_excluidos,n.diagnostico,
                           e.neo,e.cero_a_uno,e.uno_a_seis,e.seis_a_diez,e.diez_a_veinte,e.veinte_a_sesentaycuatro,e.f,e.m,e.embarazada
                           FROM facturacion.nomenclador n
                           INNER JOIN nacer.excluidos e on n.id_nomenclador=e.cod_practica
                           WHERE e.id_conv_nom='$id_conv_nom'
                           AND n.id_nomenclador_detalle = '$nom_detalle'
			   ORDER BY n.codigo,n.diagnostico";

        $res_comprobante = sql($queryexcluidos, "<br>Error al traer las prestaciones Exclu&iacute;das<br>") or fin_pagina();
        ?>
        <tr>
            <td>
                <table width="100%" class="bordes" align="center">
                    <tr align="center" id="mo">
                        <td align="center" width="3%">
                            <img id="imagen_2" src="<?= $img_ext ?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:hand;" onclick="muestra_tabla(document.all.prueba_vida, 2);" >
                        </td>
                        <td align="center">
                            <b>C&oacute;digos Excluidos</b>
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
                                <font size="3" color="Red"><b>No existen C&oacute;digos excluidos para este Efector</b></font>
                            </td>
                        </tr>
                        <?
                    } else {
                        ?>
                        <tr id="sub_tabla">		 	    
                            <td width="6%">C&oacute;digo</td>
                            <td width="5%">Neonato</td>
                            <td width="5%">Menor de 1</td>
                            <td width="5%">1 a 6</td>
                            <td width="5%">6 a 10</td>
                            <td width="5%">10 a 20</td>
                            <td width="5%">20 a 64</td>
                            <td width="5%">Embarazadas</td>
                            <td width="5%">Fem.</td>
                            <td width="5%">Masc.</td>
                            <td width="5%">Usuario Carga</td>
                            <td width="5%">Fecha Carga</td>
                            <td width="5%">Quitar</td>
                        </tr>
                        <?
                        $res_comprobante->movefirst();
                        while (!$res_comprobante->EOF) {
                            if ($res_comprobante->fields['neo'] == 't') {
                                $neo_aux = 'SI';
                            } else {
                                $neo_aux = 'NO';
                            }
                            if ($res_comprobante->fields['cero_a_uno'] == 't') {
                                $cero_a_uno_aux = 'SI';
                            } else {
                                $cero_a_uno_aux = 'NO';
                            }
                            if ($res_comprobante->fields['uno_a_seis'] == 't') {
                                $uno_a_seis_aux = 'SI';
                            } else {
                                $uno_a_seis_aux = 'NO';
                            }
                            if ($res_comprobante->fields['seis_a_diez'] == 't') {
                                $seis_a_diez_aux = 'SI';
                            } else {
                                $seis_a_diez_aux = 'NO';
                            }
                            if ($res_comprobante->fields['diez_a_veinte'] == 't') {
                                $diez_a_veinte_aux = 'SI';
                            } else {
                                $diez_a_veinte_aux = 'NO';
                            }
                            if ($res_comprobante->fields['veinte_a_sesentaycuatro'] == 't') {
                                $veinte_a_sesentaycuatro_aux = 'SI';
                            } else {
                                $veinte_a_sesentaycuatro_aux = 'NO';
                            }
                            if ($res_comprobante->fields['embarazada'] == 't') {
                                $embarazada_aux = 'SI';
                            } else {
                                $embarazada_aux = 'NO';
                            }
                            if ($res_comprobante->fields['f'] == 't') {
                                $f_aux = 'SI';
                            } else {
                                $f_aux = 'NO';
                            }
                            if ($res_comprobante->fields['m'] == 't') {
                                $m_aux = 'SI';
                            } else {
                                $m_aux = 'NO';
                            }
                            ?>
                            <tr>
                                <td align="center"><?= $res_comprobante->fields['codigo'] . " " . $res_comprobante->fields['diagnostico']; ?> </td>	
                                <td align="center"><?= $neo_aux ?></td>
                                <td align="center"><?= $cero_a_uno_aux ?></td>
                                <td align="center"><?= $uno_a_seis_aux ?></td>
                                <td align="center"><?= $seis_a_diez_aux ?></td>
                                <td align="center"><?= $diez_a_veinte_aux ?></td>
                                <td align="center"><?= $veinte_a_sesentaycuatro_aux ?></td>
                                <td align="center"><?= $embarazada_aux ?></td>
                                <td align="center"><?= $f_aux ?></td>
                                <td align="center"><?= $m_aux ?></td>
                                <td><?= $res_comprobante->fields['usuario']; ?> </td>			 		
                                <td><?= $res_comprobante->fields['fecha_carga'] ?> </td>
                                <td align="center"><img class="quitar" src='../../imagenes/sin_desc.gif' style='cursor:hand;' name="<?php echo $res_comprobante->fields['id_excluidos']; ?>"/></td>
                            </tr>			        
                            <?
                            $res_comprobante->movenext();
                        }
                    }
                    ?>
                </table></td></tr>

        <tr>
            <td>
                <table width=100% align="center" class="bordes">
                    <tr align="center">
                        <td>
                            <input type=button name="volver" value="Volver" onclick="document.location = 'efec_nom_listado.php'" title="Volver al Listado" style="width:150px">     
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

    </table>    
</form>
<?= fin_pagina(); // aca termino     ?>
