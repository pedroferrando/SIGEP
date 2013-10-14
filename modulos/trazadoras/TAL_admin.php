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

if ($_POST['guardar'] == "Guardar Planilla") {
    $fecha_carga = date("Y-m-d H:m:s");
    $usuario = $_ses_user['id'];
    $db->StartTrans();

    $fecha_control = Fecha_db($_POST['fecha_control']);

    $codnomenclador = $codigo . " " . $diagnostico;

    $query = "INSERT INTO trazadoras.tal
             (cuie,clave,tipo_doc,num_doc,apellido,nombre,fecha_control,
                fecha_carga,usuario,tal,sexo,codnomenclador)
             VALUES
             ('$cuie','$clave_beneficiario','$tipo_doc','$num_doc','$apellido',
             '$nombre','$fecha_control','$fecha_carga','$usuario','$tal','$sexo','$codnomenclador')";

    sql($query, "Error al insertar la Planilla") or fin_pagina();

    $id_prestacion = guardarPrestacion($id_comprobante, $id_nomenclador, '1', $precio);

    coberturaBasica($cuie, $codigo, $diagnostico, $fecha_prestacion, $grupo_etareo, $clave_beneficiario);
    $accion = "Se registro la Prestacion: " . $codigo . " " . $diagnostico;

    $db->CompleteTrans();
    ?>
    <script>
        $('#titulo', window.opener.document).text('<?= $accion ?>');
        $("#categoria", window.opener.document).val('-1');
        if (window.opener && !window.opener.closed) {
            window.opener.combocambiado();
        }
        self.close();
    </script>
    <?
}//de if ($_POST['guardar']=="Guardar nuevo Muleto")
if ($_POST['borrar'] == "Borrar") {
    $query = "delete from trazadoras.embarazadas
			where id_emb=$id_planilla";
    sql($query, "Error al insertar la Planilla") or fin_pagina();
    $accion = "Se elimino la planilla $id_planilla de Embarazadas";
}

if ($pagina == 'prestacion_admin.php') {

    $sql = "select * from nacer.smiafiliados	  
	 where id_smiafiliados=$id_smiafiliados";
    $res_extra = sql($sql, "Error al traer el beneficiario") or fin_pagina();

    $clave = $res_extra->fields['clavebeneficiario'];
    $tipo_doc = $res_extra->fields['afitipodoc'];
    $num_doc = number_format($res_extra->fields['afidni'], 0, '.', '');
    $apellido = $res_extra->fields['afiapellido'];
    $nombre = $res_extra->fields['afinombre'];

    $fecha_control = $fecha_comprobante;
    $fpcp = $fecha_comprobante;
}

if ($_POST['b'] == "b") {
    $sql = "select * from nacer.smiafiliados	  
	 where afidni='$num_doc'";
    $res_extra = sql($sql, "Error al traer el beneficiario") or fin_pagina();

    if ($res_extra->recordcount() > 0) {
        $clave = $res_extra->fields['clavebeneficiario'];
        $tipo_doc = $res_extra->fields['afitipodoc'];
        $num_doc = number_format($res_extra->fields['afidni'], 0, '.', '');
        $apellido = $res_extra->fields['afiapellido'];
        $nombre = $res_extra->fields['afinombre'];
        $fecha_nac = $res_extra->fields['afifechanac'];
    } else {
        $sql = "select * from trazadoras.embarazadas	  
	 	where num_doc='$num_doc'";
        $res_extra = sql($sql, "Error al traer el beneficiario") or fin_pagina();
        if ($res_extra->recordcount() > 0) {
            $clave = $res_extra->fields['clave'];
            $tipo_doc = $res_extra->fields['tipo_doc'];
            $num_doc = number_format($res_extra->fields['num_doc'], 0, '.', '');
            $apellido = $res_extra->fields['apellido'];
            $nombre = $res_extra->fields['nombre'];
            $fecha_nac = $res_extra->fields['fecha_nac'];
        } else {
            $accion2 = "Beneficiario no Encontrado";
        }
    }
}

if ($id_planilla) {
    $query = "SELECT 
  *
FROM
  trazadoras.embarazadas  
  where id_emb=$id_planilla";

    $res_factura = sql($query, "Error al traer el Comprobantes") or fin_pagina();

    $cuie = $res_factura->fields['cuie'];
    $clave = $res_factura->fields['clave'];
    $tipo_doc = $res_factura->fields['tipo_doc'];
    $num_doc = number_format($res_factura->fields['num_doc'], 0, '.', '');
    $apellido = $res_factura->fields['apellido'];
    $nombre = $res_factura->fields['nombre'];
    $fecha_control = $res_factura->fields['fecha_control'];
    $sem_gestacion = number_format($res_factura->fields['sem_gestacion'], 0, '', '');
    $fum = $res_factura->fields['fum'];
    $fpp = $res_factura->fields['fpp'];
    $fpcp = $res_factura->fields['fpcp'];
    $observaciones = $res_factura->fields['observaciones'];
    $fecha_carga = $res_factura->fields['fecha_carga'];
    $usuario = $res_factura->fields['usuario'];
    $vdrl = $res_factura->fields['vdrl'];
    $antitetanica = $res_factura->fields['antitetanica'];
}
echo $html_header;
?>
<script>

    var nav4 = window.Event ? true : false;
    function acceptNum(evt) {
        var key = nav4 ? evt.which : evt.keyCode;
        return (key < 13 || (key >= 48 && key <= 57));
    }


    //controlan que ingresen todos los datos necesarios par el muleto
    function control_nuevos()
    {
        if (document.all.cuie.value == "-1") {
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
        if (document.all.tal.value == "") {
            alert('Debe Ingresar TAL');
            return false;
        }


<? if ($datos_practica['codigo'] == 'IT E001') { ?>
            if ((document.all.tal.value < 0) | (document.all.tal.value > 6)) {
                alert('El valor TAL debe estar entre 0 y 6');
                return false;
            }
<? } else { ?>
            if (document.all.tal.value < 7) {
                alert('El valor TAL debe ser mayor a 7');
                return false;
            }
<? } ?>



    }//de function control_nuevos()

    function editar_campos()
    {
        document.all.cuie.disabled = false;
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
</script>

<form name='form1' action='TAL_admin.php' method='POST'>

    <input type="hidden" value="<?= $id_comprobante ?>" name="id_comprobante">
    <input type="hidden" value="<?= $datos_practica['id_nomenclador'] ?>" name="id_nomenclador">
    <input type="hidden" value="<?= $datos_practica['precio'] ?>" name="precio">

    <input type="hidden" value="<?= $datos_practica['codigo'] ?>" name="codigo">
    <input type="hidden" value="<?= $datos_practica['diagnostico'] ?>" name="diagnostico">
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

                TRAZADORA DE ADULTOS</td>
        </tr>
        <tr><td>
                <table width=90% align="center" class="bordes">
                    <tr>
                        <td id=mo colspan="2">
                            <b> Descripción de la PLANILLA</b></td>
                    </tr>
                    <tr>
                        <td>
                            <table>

                                <tr>	           
                                    <td align="center" colspan="2">
                                        <b> Número del Dato: <font size="+1" color="Red"><?= ($id_planilla) ? $id_planilla : "Nuevo Dato" ?></font> </b>
                                    </td>
                                </tr>

                                <tr>	           
                                    <td align="center" colspan="2">
                                        <b><font size="2" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>
                                    </td>
                                </tr>

                                <tr>
                                    <td align="right" width="40%">
                                        <b>Número de Documento:</b>
                                    </td>         	
                                    <td align='left' width="60%">
                                        <input type="text" size="10" value="<?= $num_doc ?>" name="num_doc" <? echo "readonly" ?>>
                                        <!--input type="submit" size="3" value="b" name="b"><font color="Red">Sin Puntos</font-->
                                    </td>
                                </tr> 

                                <tr>
                                    <td align="right">
                                        <b>Efector:</b>
                                    </td>
                                    <td align="left">
                                        <input size="20" name=cuie value="<?= $cuiel ?>" <? echo "readonly" ?>/>                                                        
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <b>Edad:</b>			
                                    </td>

                                    <td align="left">			 	
                                        <input size="10" name=nino_edad value="<?= $edad ?>" <? echo "readonly" ?>/>                                        		
                                    </td>
                                </tr>

                                <tr>
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
                                <tr>
                                    <td align="right">
                                        <b>Clave Beneficiario:</b>         	
                                    </td>         	
                                    <td align='left'>
                                        <input type="text" size="20" value="<?= $clave_beneficiario ?>" name="clave_beneficiario" <? echo "readonly" ?>>
                                    </td>
                                </tr>
                                <td align="right">
                                    <b>Tipo de Documento:</b>			
                                </td>
                                <td align="left">			 	
                                    <input type="text" size="10" value="<?= $tipo_doc ?>" name="tipo_doc" <? echo "readonly" ?>>     
                                </td>
                    </tr>

                    <tr>
                        <td align="right">
                            <b>Apellido:</b>
                        </td>         	
                        <td align='left'>
                            <input type="text" size="20" value="<?= $apellido ?>" name="apellido" <? echo "readonly" ?>>
                        </td>
                    </tr> 

                    <tr>
                        <td align="right">
                            <b>Nombre:</b>
                        </td>         	
                        <td align='left'>
                            <input type="text" size="20" value="<?= $nombre ?>" name="nombre" <? echo "readonly" ?>>
                        </td>
                    </tr>          

                    <tr>
                        <td align="right">
                            <b>Fecha Control Actual:</b>
                        </td>
                        <td align="left">

                            <input type=text id=fecha_control name=fecha_control value='<?= fecha($fecha_control); ?>' size=15 <? echo "readonly" ?> >					    	 
                        </td>		    
                    </tr>                   
                    <tr>
                        <td align="right">
                            <b>TAL:</b>
                        </td>
                        <td align="left">
                            <input onKeyPress="return acceptNum(event)" type=text id=tal name=tal value='' size=5 <? if ($id_planilla)
                                            echo "readonly"
                                            ?>>
                        </td>
                    </tr>             
                </table>
            </td>      
        </tr>

<? if (!($id_planilla)) { ?>
            <tr id="mo">
                <td align=center colspan="2">&nbsp;</td>
            </tr>  
            <tr align="center">
                <td>
                    <input type='submit' name='guardar' value='Guardar Planilla' onclick="return control_nuevos()"
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
</table>
</form>

<?=
fin_pagina(); // aca termino ?>