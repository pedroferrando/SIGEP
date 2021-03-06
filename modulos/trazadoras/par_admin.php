<?
/*
  Author: ferni

  modificada por
  $Author: ferni $
  $Revision: 1.42 $
  $Date: 2006/05/23 13:53:00 $
 */

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


if ($_POST['guardar'] == "Guardar Planilla") {
    $fecha_carga = date("Y-m-d H:m:s");
    $usuario = $_ses_user['id'];
    $db->StartTrans();

    $q = "select nextval('trazadoras.partos_id_par_seq') as id_planilla";
    $id_planilla = sql($q) or fin_pagina();
    $id_planilla = $id_planilla->fields['id_planilla'];

    $fecha_parto = Fecha_db($fecha_parto);
    if ($fecha_conserjeria != "")
        $fecha_conserjeria = Fecha_db($fecha_conserjeria);
    else
        $fecha_conserjeria = "1980-01-01";

    $query = "insert into trazadoras.partos
             (id_par,cuie,clave,tipo_doc,num_doc,apellido,nombre,fecha_parto,
  			  apgar,peso,vdrl,antitetanica,fecha_conserjeria,observaciones,
  			  fecha_carga,usuario)
             values
             ('$id_planilla','$cuie','$clave_beneficiario','$tipo_doc','$num_doc','$apellido',
             '$nombre','$fecha_parto','$apgar','$peso','$vdrl','$antitetanica',
             '$fecha_conserjeria','$observaciones','$fecha_carga','$usuario')";

    sql($query, "Error al insertar la Planilla") or fin_pagina();

    $id_prestacion = guardarPrestacion($id_comprobante, $id_nomenclador, '1', $precio);

    coberturaBasica($cuie, $codigo, $diagnostico, $fecha_prestacion, $grupo_etareo, $clave_beneficiario);

    $accion = "Se registro la Prestacion: " . $id_prestacion->fields['id_prestacion'] . ". " . $codigo . "-" . $diagnostico;

    $db->CompleteTrans();
    ?>
    <script>
        $('#titulo', window.opener.document).text('<?= $accion ?>');
        $("#categoria", window.opener.document).val('-1');
        if (window.opener && !window.opener.closed) {
            window.opener.combocambiado();
        }
        // $("body", window.opener.document).trigger('guardatrazadora');
        self.close();
    </script>
    <?
    ;
}//de if ($_POST['guardar']=="Guardar nuevo Muleto")

if ($_POST['borrar'] == "Borrar") {
    $query = "delete from trazadoras.partos
			where id_par=$id_planilla";
    sql($query, "Error al insertar la Planilla") or fin_pagina();
    $accion = "Se elimino la planilla $id_planilla de Partos";
}

if ($id_planilla) {
    $query = "SELECT 
  *
FROM
  trazadoras.partos  
  where id_par=$id_planilla";

    $res_factura = sql($query, "Error al traer el Comprobantes") or fin_pagina();

    $cuie = $res_factura->fields['cuie'];
    $clave = $res_factura->fields['clave'];
    $tipo_doc = $res_factura->fields['tipo_doc'];
    $num_doc = number_format($res_factura->fields['num_doc'], 0, '.', '');
    $apellido = $res_factura->fields['apellido'];
    $nombre = $res_factura->fields['nombre'];
    $fecha_parto = $res_factura->fields['fecha_parto'];
    $apgar = number_format($res_factura->fields['apgar'], 0, '', '');
    $peso = number_format($res_factura->fields['peso'], 3, '.', '');
    $vdrl = $res_factura->fields['vdrl'];
    $antitetanica = $res_factura->fields['antitetanica'];
    $fecha_conserjeria = $res_factura->fields['fecha_conserjeria'];
    $observaciones = $res_factura->fields['observaciones'];
    $fecha_carga = $res_factura->fields['fecha_carga'];
    $usuario = $res_factura->fields['usuario'];
}

if ($id_comprobante) {
    $query = " SELECT 
                c.id_comprobante,
                s.nombreefector,
                s.cuie,
                c.nombre_medico,
                c.fecha_comprobante,
                c.clavebeneficiario,
                c.tipo_nomenclador
                FROM  facturacion.comprobante c
                INNER JOIN facturacion.smiefectores s ON (c.cuie = s.cuie)
                WHERE c.id_comprobante=$id_comprobante";
    $res_comprobante = sql($query, "Error al traer el Comprobantes") or fin_pagina();
    $cuie = $res_comprobante->fields['cuie'];
    $nombre_medico = $res_comprobante->fields['nombre_medico'];
    $fecha_comprobante = $res_comprobante->fields['fecha_comprobante'];
    $clave = $res_comprobante->fields['clavebeneficiario'];
    $tipo_nomenclador = $res_comprobante->fields['tipo_nomenclador'];
}

echo $html_header;
?>
<script>
    $(document).ready(function() {
        $("#fecha_parto").datepicker();
        $("#fecha_conserjeria").datepicker();
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
        if (document.all.fecha_parto.value == "") {
            alert('Debe Ingresar una Fecha de Parto');
            return false;
        }
        if (document.all.apgar.value == "") {
            alert('Debe Ingresar APGAR');
            return false;
        }
        if (document.all.peso.value == "") {
            alert('Debe Ingresar Peso');
            return false;
        }
        if (document.all.vdrl.value == "-1") {
            alert('Debe Seleccionar VDRL');
            return false;
        }
        if (document.all.antitetanica.value == "-1") {
            alert('Debe Seleccionar Antitetanica');
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
        document.all.apgar.readOnly = false;
        document.all.peso.readOnly = false;
        document.all.vdrl.disabled = false;
        document.all.antitetanica.disabled = false;
        document.all.observaciones.readOnly = false;

        document.all.cancelar_editar.disabled = false;
        document.all.guardar_editar.disabled = false;
        document.all.editar.disabled = true;
        return true;
    }//de function control_nuevos()

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

<form name='form1' action='par_admin.php' method='POST'>
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

            </td>
        </tr>
        <tr><td>
                <table width=90% align="center" class="bordes">
                    <tr>
                        <td id=mo colspan="2">
                            <b> Descripci�n de la PLANILLA</b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table>
                                <tr>	           
                                    <td align="center" colspan="2">
                                        <b> N�mero del Dato: <font size="+1" color="Red"><?= ($id_planilla) ? $id_planilla : "Nuevo Dato" ?></font> </b>
                                    </td>
                                </tr>
                                <tr>	           
                                    <td align="center" colspan="2">
                                        <b><font size="2" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>
                                    </td>
                                </tr>

                    </tr>

                    <tr>
                        <td align="right" width="40%">
                            <b>Efector:</b>
                        </td>
                        <td align="left" width="60%">
                            <input name=cuie value="<?= $cuiel ?>" <? echo "readonly" ?>/>                                                        
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

                    <tr>
                        <td align="right">
                            <b>Tipo de Documento:</b>			
                        </td>
                        <td align="left">			 	
                            <input type="text" size="10" value="<?= $tipo_doc ?>" name="tipo_doc" <? echo "readonly" ?>>     
                        </td>
                    </tr>

                    <tr>
                        <td align="right">
                            <b>N�mero de Documento:</b>
                        </td>         	
                        <td align='left'>
                            <input type="text" size="10" value="<?= $num_doc ?>" name="num_doc" <? echo "readonly" ?>>
                            <!--input type="submit" size="3" value="b" name="b"><font color="Red">Sin Puntos</font-->
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
                            <b>Fecha Parto:</b>
                        </td>
                        <td align="left">
                            <? $fecha_comprobante = date("d/m/Y"); ?>
                            <input type=text id=fecha_parto name=fecha_parto value='<?= fecha($fecha_parto); ?>' size=15 readonly>					    	 
                        </td>		    
                    </tr>

                    <tr>
                        <td align="right">
                            <b>APGAR a los 5 Minutos:</b>
                        </td>         	
                        <td align='left'>
                            <input onKeyPress="return acceptNum(event)" type="text" size="10" value="<?= $apgar ?>" name="apgar" <? if ($id_planilla)
                                echo "readonly"
                                ?>><font color="Red">Dos Digitos -- 0 en caso de vacio.</font>
                        </td>
                    </tr>    

                    <tr>
                        <td align="right">
                            <b>Peso:</b>
                        </td>         	
                        <td align='left'>
                            <input onKeyPress="return acceptNum(event)" type="text" size="10" value="<?= $peso ?>" name="peso" <? if ($id_planilla)
                                       echo "readonly"
                                       ?>><font color="Red">En Kilogramos -- 0 en caso de vacio.</font>
                        </td>
                    </tr>

                    <tr>
                        <td align="right">
                            <b>VDRL (Durante el embarazo):</b>
                        </td>
                        <td align="left">			 	
                            <select name=vdrl Style="width:157px" <? if ($id_planilla)
                                       echo "disabled"
                                       ?>>
                                <option value=-1>Seleccione</option>
                                <option value=S <? if ($vdrl == 'S')
                                       echo "selected"
                                       ?>>SI</option>			  
                                <option value=N <? if ($vdrl == 'N')
                                       echo "selected"
                                       ?>>NO</option>			  
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td align="right">
                            <b>Antitetanica:</b>
                        </td>
                        <td align="left">			 	
                            <select name=antitetanica Style="width:157px" <? if ($id_planilla)
                                       echo "disabled"
                                ?>>
                                <option value=-1>Seleccione</option>
                                <option value=S <? if ($antitetanica == 'S')
                                        echo "selected"
                                        ?>>SI</option>			  
                                <option value=N <? if ($antitetanica == 'N')
                                        echo "selected"
                                        ?>>NO</option>			  
                            </select>
                        </td>
                    </tr>  

                    <tr>
                        <td align="right">
                            <b>Fecha de Conserjeria sexual y reproductiva:</b>
                        </td>
                        <td align="left">
<? $fecha_comprobante = date("d/m/Y"); ?>
                            <input type=text id=fecha_conserjeria name=fecha_conserjeria value='<?= fecha($fecha_conserjeria); ?>' size=15 readonly>					    	 
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
                        <input type=button name="volver" value="Volver" onClick="window.opener.location.reload(true);
        window.close();" title="Volver al Listado" style="width:150px">     
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