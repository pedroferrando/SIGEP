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

    $q = "select nextval('trazadoras.embarazadas_id_emb_seq') as id_planilla";
    $id_planilla = sql($q) or fin_pagina();
    $id_planilla = $id_planilla->fields['id_planilla'];

    $fecha_control = Fecha_db($_POST['fecha_control']);
    $fum = Fecha_db($fum);
    $fpp = Fecha_db($fpp);
    $fpcp = Fecha_db($fpcp);

    $query = "insert into trazadoras.embarazadas
             (id_emb,cuie,clave,tipo_doc,num_doc,apellido,nombre,fecha_control,
             sem_gestacion,fum,fpp,fpcp,observaciones,fecha_carga,usuario,peso_embarazada,talla,tension_arterial_minima,tension_arterial_maxima)
             values
             ('$id_planilla','$cuie','$clave_beneficiario','$tipo_doc','$num_doc','$apellido',
             '$nombre','$fecha_control','$sem_gestacion','$fum',
             '$fpp','$fpcp','$observaciones','$fecha_carga','$usuario','$peso','$talla','$tamin','$tamax')";

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

    $(document).ready(function() {
        //$("#fecha_control").datepicker();
        $("#fum").datepicker();
        $("#fpp").datepicker();
        $("#fpcp").datepicker();
    });

    var nav4 = window.Event ? true : false;
    function acceptNum(evt) {
        var key = nav4 ? evt.which : evt.keyCode;
        return (key < 13 || (key >= 48 && key <= 57) || key == 46);
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
        if (document.all.sem_gestacion.value == "") {
            alert('Debe Ingresar Semana de Gestacion');
            return false;
        }
        if (document.all.peso.value == "") {
            alert('Debe Ingresar un Peso');
            return false;
        }
        if (document.all.talla.value == "") {
            alert('Debe Ingresar una Talla');
            return false;
        }
        if (document.all.tamin.value == "") {
            alert('Debe Ingresar una TA Minima');
            return false;
        }
        if (document.all.tamax.value == "") {
            alert('Debe Ingresar una TA Maxima');
            return false;
        }
        if (document.all.fum.value == "") {
            alert('Debe Ingresar una FUM');
            return false;
        }
        if (document.all.fpp.value == "") {
            alert('Debe Ingresar una FPP');
            return false;
        }
        if (document.all.fpcp.value == "") {
            alert('Debe Ingresar una Fecha de Primer Control');
            return false;
        }

        if (document.all.fum.value == document.all.fpp.value) {
            alert('FUM no debe ser igual a FPP');
            return false;
        }

        var Datef = document.all.fum.value;
        var elem = Datef.split('/');
        var dia = elem[0];
        var mes = elem[1] - 1;
        var anio = elem[2];
        var vfum = new Date();
        vfum.setFullYear(eval(anio), eval(mes), eval(dia));

        var Datep = document.all.fpp.value;
        var elemp = Datep.split('/');
        var diap = elemp[0];
        var mesp = elemp[1] - 1;
        var aniop = elemp[2];
        var vfpp = new Date();
        vfpp.setFullYear(eval(aniop), eval(mesp), eval(diap));

        var Datec = document.all.fpcp.value;
        var elemc = Datec.split('/');
        var diac = elemc[0];
        var mesc = elemc[1] - 1;
        var anioc = elemc[2];
        var vfpcp = new Date();
        vfpcp.setFullYear(eval(anioc), eval(mesc), eval(diac));

        if (vfum >= vfpcp) {
            alert('Error: el FUM no debe ser igual a la Fecha del Primer Control');
            return false;
        }

        var vmuleto = new Date();
        vmuleto.setFullYear(eval(anio), eval(mes), eval(dia));
        vmuleto.setDate(vmuleto.getDate() + 210);
        if (vfpp < vmuleto) {
            alert('Fecha Improbable de Parto, es menor a 30 semanas ');
            return false;
        }

        vmuleto.setFullYear(eval(anio), eval(mes), eval(dia));
        vmuleto.setDate(vmuleto.getDate() + 294);

        if (vfpp > vmuleto) {
            alert('Fecha Improbable de Parto, es mayor a 42 semanas');
            return false;
        }

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

<form name='form1' action='emb_admin.php' method='POST'>


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

                TRAZADORA DE EMBARAZOS</td>
        </tr>
        <tr>
            <td>
                <table width="100%" align="center" class="bordes">
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
                                        <input type="text" size="20" value="<?= $num_doc ?>" name="num_doc" <? echo "readonly" ?>>
                                        <!--input type="submit" size="3" value="b" name="b"><font color="Red">Sin Puntos</font-->
                                    </td>
                                </tr> 

                                <tr>
                                    <td align="right">
                                        <b>Efector:</b>
                                    </td>
                                    <td align="left">
                                        <input name=cuie size="20" value="<?= $cuiel ?>" <? echo "readonly" ?>/>                                                        
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
                                    <input type="text" size="20" value="<?= $tipo_doc ?>" name="tipo_doc" <? echo "readonly" ?>>     
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
                        <td align="right" >
                            <b>Número de Semana de Gestación:</b>
                        </td>         	
                        <td align='left'>
                            <input onKeyPress="return acceptNum(event)" type="text" size="10" value="<?= $sem_gestacion ?>" name="sem_gestacion" <? if ($id_planilla)
                    echo "readonly"
                    ?>><font color="Red">Dos Digitos</font>
                        </td>
                    </tr>  
                    <tr>
                        <td align="right">
                            <b>Peso:</b>
                        </td>
                        <td align="left">
                            <input onKeyPress="return acceptNum(event)" type=text id=peso name=peso value='' size=10 <? if ($id_planilla)
                                   echo "readonly"
                                   ?>>                                   					    	 
                        </td>		    
                    </tr>  

                    <tr>
                        <td align="right">
                            <b>Talla (cm):</b>
                        </td>
                        <td align="left">
                            <input onKeyPress="return acceptNum(event)" type=text id=talla name=talla value='' size=10 <? if ($id_planilla)
                                   echo "readonly"
                                   ?>>                                   					    	 
                        </td>		    
                    </tr>

                    <tr>
                        <td align="right">
                            <b>Control TA:</b>
                        </td>
                        <td align="left">
                            Min.<input onKeyPress="return acceptNum(event)" type=text id=tamin name=tamin value='' size=5 <? if ($id_planilla)
                                   echo "readonly"
                                   ?>>    
                            Max.<input onKeyPress="return acceptNum(event)" type=text id=tamax name=tamax value='' size=5 <? if ($id_planilla)
                                   echo "readonly"
                                   ?>>  
                        </td>
                    </tr>

                    <tr>
                        <td align="right">
                            <b>F.U.M.:</b>
                        </td>
                        <td align="left">
<? $fecha_comprobante = date("d/m/Y"); ?>
                            <input type=text id=fum name=fum value='<?= fecha($fum); ?>' size=15 <? if ($id_planilla)
    echo "readonly"
    ?>>					    	 
                        </td>		    
                    </tr>    

                    <tr>
                        <td align="right">
                            <b>F.P.P.:</b>
                        </td>
                        <td align="left">
<? $fecha_comprobante = date("d/m/Y"); ?>
                            <input type=text id=fpp name=fpp value='<?= fecha($fpp); ?>' size=15 <? if ($id_planilla)
    echo "readonly"
    ?>>					    	 
                        </td>		    
                    </tr>   

                    <tr>
                        <td align="right">
                            <b>Fecha del Primer Control Prenatal:</b>
                        </td>
                        <td align="left">
<? $fecha_comprobante = date("d/m/Y"); ?>
                            <input type=text id=fpcp name=fpcp value='<?= fecha($fpcp); ?>' size=15 <? if ($id_planilla)
    echo "readonly"
    ?>>					    	 
                        </td>		    
                    </tr>             

                    <tr>
                        <td align="right">
                            <b>Observaciones:</b>
                        </td>         	
                        <td align='left'>
                            <textarea cols='30' rows='4' name='observaciones' <? if ($id_planilla)
    echo "readonly"
    ?>><?= $observaciones; ?></textarea>
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

    <tr>
        <td>
            <table width=100% align="center" class="bordes">
                <tr align="center">
                    <td>
                        <font color="Black" size="3"> <b>Estos datos son obligatorios para Trazadora</b></font>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

</table>
</form>

<?=
fin_pagina(); // aca termino ?>