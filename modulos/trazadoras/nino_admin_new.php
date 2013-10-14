<?
require_once ("../../config.php");
require_once ("../../lib/bibliotecaTraeme.php");


extract($_POST, EXTR_SKIP);
if ($parametros)
    extract($parametros, EXTR_OVERWRITE);
//cargar_calendario();

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

    $q = "select nextval('trazadoras.nino_new_id_nino_new_seq') as id_planilla";
    $id_planilla = sql($q) or fin_pagina();
    $id_planilla = $id_planilla->fields['id_planilla'];

    $fecha_nac = Fecha_db($fecha_nac);
    $fecha_control = Fecha_db($_POST['fecha_control']);
    if ($triple_viral != "")
        $triple_viral = Fecha_db($triple_viral);
    else
        $triple_viral = "1980-01-01";
    if ($perim_cefalico == '' or $perim_cefalico == null) {
        $perim_cefalico = 0;
    }
    $codnomenclador = $codigo . " " . $diagnostico;

    $query = "INSERT INTO trazadoras.nino_new
             (id_nino_new,cuie,clave,clase_doc,tipo_doc,num_doc,apellido,nombre,fecha_nac,fecha_control,peso,talla,
  	      percen_peso_edad,percen_talla_edad,perim_cefalico,percen_perim_cefali_edad,imc,percen_imc_edad,percen_peso_talla,
  	      triple_viral,nino_edad,observaciones,fecha_carga,usuario,sexo,cod_nomenclador)
             VALUES
             ('$id_planilla','$cuie','$clave_beneficiario','$clase_doc','$tipo_doc','$num_doc','$apellido','$nombre','$fecha_nac',
              '$fecha_control','$peso','$talla','$percen_peso_edad','$percen_talla_edad',$perim_cefalico,
              '$percen_perim_cefali_edad','$imc','$percen_imc_edad','$percen_peso_talla','$triple_viral',
              '$nino_edad','$observaciones','$fecha_carga','$usuario','$sexo','$codnomenclador')";

    sql($query, "Error al insertar la Planilla") or fin_pagina();

    $id_prestacion = guardarPrestacion($id_comprobante, $id_nomenclador, '1', $precio);
    coberturaBasica($cuie, $codigo, $diagnostico, $fecha_prestacion, $grupo_etareo, $clave_beneficiario);
    $db->CompleteTrans();
    //$db->RollbackTrans();
    $accion = "Se registro la Prestacion: " . $codigo . " " . $diagnostico;
    ;
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
    $query = "delete from trazadoras.nino_new
			where id_nino=$id_planilla";
    sql($query, "Error al insertar la Planilla") or fin_pagina();
    $accion = "Se elimino la planilla $id_planilla de Niños";
}

if ($_POST['b'] == "b") {
    $sql = "SELECT * FROM nacer.smiafiliados WHERE afidni='$num_doc'";
    $res_extra = sql($sql, "Error al traer el beneficiario") or fin_pagina();

    if ($res_extra->recordcount() > 0) {
        $clave = $res_extra->fields['clavebeneficiario'];
        $tipo_doc = $res_extra->fields['afitipodoc'];
        $num_doc = number_format($res_extra->fields['afidni'], 0, '.', '');
        $apellido = $res_extra->fields['afiapellido'];
        $nombre = $res_extra->fields['afinombre'];
        $fecha_nac = $res_extra->fields['afifechanac'];
        $nino_edad = 1;
        $clase_doc = 'R';
        $sexo = $res_extra->fields['afisexo'];
    } else {//VER AQUÍ
        $sql = "SELECT * FROM trazadoras.nino_new WHERE num_doc='$num_doc'";
        $res_extra = sql($sql, "Error al traer el beneficiario") or fin_pagina();
        if ($res_extra->recordcount() > 0) {
            $clave = $res_extra->fields['clave'];
            $tipo_doc = $res_extra->fields['tipo_doc'];
            $num_doc = number_format($res_extra->fields['num_doc'], 0, '.', '');
            $apellido = $res_extra->fields['apellido'];
            $nombre = $res_extra->fields['nombre'];
            $fecha_nac = $res_extra->fields['fecha_nac'];
            $nino_edad = $res_extra->fields['nino_edad'];
            $clase_doc = $res_extra->fields['clase_doc'];
        } else {
            $accion2 = "Beneficiario no Encontrado";
        }
    }
}

if ($id_planilla) {
    $query = "SELECT * FROM trazadoras.nino_new WHERE id_nino_new=$id_planilla";
// VER AQUÍ TAMBIÉN
    $res_factura = sql($query, "Error al traer el Comprobantes") or fin_pagina();

    $cuie = $res_factura->fields['cuie'];
    $clave = $res_factura->fields['clave'];
    $clase_doc = $res_factura->fields['clase_doc'];
    $tipo_doc = $res_factura->fields['tipo_doc'];
    $num_doc = number_format($res_factura->fields['num_doc'], 0, '.', '');
    $apellido = $res_factura->fields['apellido'];
    $nombre = $res_factura->fields['nombre'];
    $fecha_nac = $res_factura->fields['fecha_nac'];
    $fecha_control = $res_factura->fields['fecha_control'];
    $peso = number_format($res_factura->fields['peso'], 3, '.', '');
    $talla = number_format($res_factura->fields['talla'], 0, '', '');
    $perim_cefalico = number_format($res_factura->fields['perim_cefalico'], 3, '.', '');
    $percen_peso_edad = $res_factura->fields['percen_peso_edad'];
    $percen_talla_edad = $res_factura->fields['percen_talla_edad'];
    $percen_perim_cefali_edad = $res_factura->fields['percen_perim_cefali_edad'];
    $percen_peso_talla = $res_factura->fields['percen_peso_talla'];
    $triple_viral = $res_factura->fields['triple_viral'];
    $nino_edad = $res_factura->fields['nino_edad'];
    $observaciones = $res_factura->fields['observaciones'];
    $fecha_carga = $res_factura->fields['fecha_carga'];
    $usuario = $res_factura->fields['usuario'];
}
echo $html_header;
?>
<script>
    $(document).ready(function() {
        $("#triple_viral").datepicker();
    });

    var nav4 = window.Event ? true : false;
    function acceptNum(evt) {
        var key = nav4 ? evt.which : evt.keyCode;
        return (key < 13 || (key >= 48 && key <= 57) || (key == 46));
    }


    //controlan que ingresen todos los datos necesarios par el muleto
    function control_nuevos()
    {
        function mayor_menor($dato, $mayor, $menor, $mensaje) {
            kamikaze = false;
            if (variable == false) {
                if ($mayor !== "vacio") {
                    if ($dato.value > $mayor) {
                        kamikaze = true;
                    }
                }
                if ($mayor !== "vacio") {
                    if ($dato.value < $menor) {
                        kamikaze = true;
                    }
                }
                if (kamikaze == true) {
                    alert($mensaje + '. De lo contrario comuníquese a Plan Nacer');
                    $dato.focus();
                    variable = true;
                    return variable;
                }
            }
        }
        // Funcion Convertir fecha
        function f_fecha(fechaentrada, fechasalida) {
            var elem = fechaentrada.split('/');
            var dia = elem[0];
            var mes = elem[1] - 1;
            var anio = elem[2];
            fechasalida.setFullYear(eval(anio), eval(mes), eval(dia));
            return fechasalida;
        }
        // Convierto fechas para poder compararlas después
        var vfecha_nac = new Date();
        vfecha_nac = f_fecha(document.all.fecha_nac.value, vfecha_nac);
        var vfecha_control = new Date();
        vfecha_control = f_fecha(document.all.fecha_control.value, vfecha_control);
        var vtriple_viral = new Date();
        vtriple_viral = f_fecha(document.all.triple_viral.value, vtriple_viral);

        variable = false;

        function verif_vacio($dato, $vacio, $mensaje) {
            if (($dato.value == $vacio) && (variable == false)) {
                alert('Debe ingresar' + $mensaje);
                $dato.focus();
                variable = true;
                return variable;
            }
        }
        function cambio_cero($dato) {
            if ($dato == "") {
                $dato = "0";
            }
        }
        verif_vacio(document.all.num_doc, "", " Documento");
        verif_vacio(document.all.cuie, "-1", " Efector");
        verif_vacio(document.all.nino_edad, "-1", " Edad");
        verif_vacio(document.all.clase_doc, "-1", " Clase de Documento");
        verif_vacio(document.all.tipo_doc, "-1", " Tipo de Documento");
        verif_vacio(document.all.apellido, "", " Apellido");
        verif_vacio(document.all.nombre, "", " Nombre");
        verif_vacio(document.all.fecha_nac, "", " Fecha de Nacimiento");
        verif_vacio(document.all.fecha_control, "", " Fecha de Control");

        // ******************  VALIDACION DE FECHA ********************
        if (vfecha_nac > vfecha_control) {
            alert('La fecha de Nacimiento no puede ser mayor a la fecha de control');
            document.all.fecha_nac.focus();
            return false;
        }

        if (variable == false) {
            verif_vacio(document.all.peso, "", " Peso");
            verif_vacio(document.all.peso, "-1", " Peso");
            verif_vacio(document.all.talla, "", " Talla");
            verif_vacio(document.all.talla, "-1", " Talla");
            verif_vacio(document.all.percen_talla_edad, "", " Percentilo Talla Edad");
            verif_vacio(document.all.percen_talla_edad, "-1", " Percentilo Talla Edad");
            verif_vacio(document.all.percen_peso_edad, "", " Percentilo Peso Edad");
            verif_vacio(document.all.percen_peso_edad, "-1", " Percentilo Peso Edad");
            verif_vacio(document.all.percen_peso_talla, "", " Percentilo Peso Talla");
            verif_vacio(document.all.percen_peso_talla, "-1", " Percentilo Peso Talla");
            ////////---------------- HASTA 1 AÑO ---------------------------/////////////////////////
            if (document.all.nino_edad.value == 0) {
                verif_vacio(document.all.perim_cefalico, "", " Perimetro Cefalico");
                verif_vacio(document.all.perim_cefalico, "-1", " Perimetro Cefalico");
                verif_vacio(document.all.percen_perim_cefali_edad, "", " Perimetro Cefalico Edad");
                verif_vacio(document.all.percen_perim_cefali_edad, "-1", " Perimetro Cefalico Edad");

            } else {

                verif_vacio(document.all.imc, "", " IMC");
                verif_vacio(document.all.imc, "-1", " IMC");
                verif_vacio(document.all.percen_imc_edad, "", " Percentilo IMC Edad");
                verif_vacio(document.all.percen_imc_edad, "-1", " Percentilo IMC Edad");
            } //CIERRO NIÑOS DE 1 A 5 AÑOS
        } // CIERRA variable == false
        //} // CIERRE VACUNA FALSE
        // ************* TRIPLE VIRAL MAYOR A FECHA NACIMIENTO *****************
        if (document.all.triple_viral.value != '1980-01-01' || document.all.triple_viral.value != '1980/01/01') {
            if ((document.all.triple_viral.value != "") && (vfecha_nac > vtriple_viral)) {
                alert('La fecha de Antisaranpion/Triple no puede ser menor a la fecha de Nacimiento');
                variable = true;
            }
        }
        if (variable == true) {
            return false;
        } else {
            document.form1.submit();
        }
    }//de function control_nuevos()

    function editar_campos()
    {
        document.all.nino_edad.disabled = false;
        document.all.cuie.disabled = false;
        document.all.clase_doc.disabled = false;
        document.all.tipo_doc.disabled = false;
        document.all.num_doc.readOnly = false;
        document.all.apellido.readOnly = false;
        document.all.nombre.readOnly = false;
        document.all.peso.readOnly = false;
        document.all.percen_peso_edad.disabled = false;
        document.all.talla.readOnly = false;
        document.all.percen_talla_edad.disabled = false;
        document.all.percen_peso_talla.disabled = false;
        document.all.perim_cefalico.readOnly = false;
        document.all.imc.readOnly = false;
        document.all.percen_perim_cefali_edad.disabled = false;
        document.all.percen_imc_edad.disabled = false;
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
<style type="text/css">
    <!--
    .Estilo1 {
        font-size: large;
        color: #FF6633;
    }
    -->
</style>


<form name='form1' action='nino_admin_new.php' method='POST'>    
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
                <? } ?>TRAZADORA DE NIÑOS</td>
        </tr>
        <tr>
            <td>
                <table width=90% align="center" class="bordes">
                    <tr>
                        <td id=mo colspan="2">
                            <b> Descripción de la PLANILLA</b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table>
                                <tr>	           
                                    <td align="center" colspan="2">
                                        <b> Número del Dato: <font size="+1" color="Red"><?= ($id_planilla) ? $id_planilla : "Nuevo Dato" ?></font> </b>           <label></label></td>
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
                                        <!--input type="submit" size="3" value="b" name="b" id="b"><font color="Red">Sin Puntos</font-->            
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

                                <tr>
                                    <td align="right">
                                        <b>Clase de Documento:</b>			
                                    </td>
                                    <td align="left">			 	
                                        <input type="text" size="8" value="<?= $clase_doc ?>" name="clase_doc" <? echo "readonly" ?>>	
                                    </td>
                                </tr>
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
                <b>Fecha de Nacimiento:</b>			
            </td>
            <td align="left">
                <input type=text id=fecha_nac name=fecha_nac value='<?= fecha($fecha_nac); ?>' size=15 <? echo "readonly" ?>>             
            </td>		    
        </tr>

        <tr>
            <td align="right">
                <b>Fecha Control:</b>			
            </td>
            <td align="left">
                <input type=text id=fecha_control name=fecha_control value='<?= fecha($fecha_control); ?>' size=15 <? echo "readonly" ?>/> 
                <font color="Red">Fecha de Control o Fecha de Antisarampionosa</font></td>		    
        </tr>

        <tr>
            <td align="right">
                <b>Peso:</b>         	</td>         	
            <td align='left'>
                <input onKeyPress="return acceptNum(event)" type="text" size="10" value="<?= $peso ?>" name="peso" <? if ($id_planilla)
                                            echo "readonly"
                                            ?>>
                <font color="Red">En Kg (Decimales con ".")</font></td>
        </tr>     

        <tr>
            <td align="right">
                <b>Talla:</b>         	</td>         	
            <td align='left'>
                <input onKeyPress="return acceptNum(event)" type="text" size="10" value="<?= $talla ?>" name="talla" <? if ($id_planilla)
                           echo "readonly"
                           ?>>
                <font color="Red">En Cm</font></td>
        </tr> 

        <td align="right">
            <b>Percentilo Peso/Edad:</b>			</td>
        <td align="left">			 	
            <select name=percen_peso_edad Style="width:157px" <? if ($id_planilla)
                           echo "disabled"
                           ?>>
                <option value=-1>Seleccione</option>
                <option value=1 <? if ($percen_peso_edad == '1')
                           echo "selected"
                           ?>> <3 </option>
                <option value=2 <? if ($percen_peso_edad == '2')
                           echo "selected"
                                            ?>> 3-10 </option>
                <option value=3 <? if ($percen_peso_edad == '3')
                        echo "selected"
                        ?>> >10-90 </option>
                <option value=4 <? if ($percen_peso_edad == '4')
                        echo "selected"
                        ?>> >90-97 </option>
                <option value=5 <? if ($percen_peso_edad == '5')
                        echo "selected"
                        ?>> >97 </option>
                <option value='' <? if ($percen_peso_edad == '')
                        echo "selected"
                        ?>>Dato Sin Ingresar</option>			  
            </select>			</td>
        </tr>

        <tr>
            <td align="right">
                <b>Percentilo Talla/Edad:</b>         	</td>         	
            <td align="left">			 	
                <select name=percen_talla_edad Style="width:157px" <? if ($id_planilla)
                        echo "disabled"
                                            ?>>
                    <option value=-1>Seleccione</option>
                    <option value=1 <? if ($percen_talla_edad == '1')
                            echo "selected"
                            ?>>-3</option>
                    <option value=2 <? if ($percen_talla_edad == '2')
                            echo "selected"
                            ?>>3-97</option>
                    <option value=3 <? if ($percen_talla_edad == '3')
                            echo "selected"
                                            ?>>+97</option>
                    <option value='' <? if ($percen_talla_edad == '')
                        echo "selected"
                                            ?>>Dato Sin Ingresar</option>			  
                </select>			</td>
        </tr>

        <tr>
            <td align="right">
                <b>Percentilo Peso/Talla: </b>         	
            </td>         	
            <td align="left">			 	
                <select name=percen_peso_talla Style="width:157px" <? if ($id_planilla)
                            echo "disabled"
                            ?>>
                    <option value=-1>Seleccione</option>
                    <option value=1 <? if ($percen_peso_edad == '1')
                            echo "selected"
                                            ?>> <3 </option>
                    <option value=2 <? if ($percen_peso_edad == '2')
        echo "selected"
        ?>> 3-10 </option>
                    <option value=3 <? if ($percen_peso_edad == '3')
        echo "selected"
        ?>> >10-85 </option>
                    <option value=4 <? if ($percen_peso_edad == '4')
        echo "selected"
        ?>> >85-97 </option>
                    <option value=5 <? if ($percen_peso_edad == '5')
        echo "selected"
        ?>> >97 </option>
                    <option value='' <? if ($percen_peso_talla == '')
        echo "selected"
                                            ?>>Dato Sin Ingresar</option>			  
                </select>			
            </td>
        </tr> 

<? if ($edad < 1) { ?>
            <tr>
                <td colspan="2">
                    <table style="margin-top: 10px" width="100%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?= $bgcolor_out ?>' class="bordes">   
                        <tr>
                            <td align="center" id='ma' colspan="2">
                                <b>Niños Menores de 1 AÑO</b>         	
                            </td>            
                        </tr>      
                        <tr>
                            <td align="right">
                                <b>Perim. Cefalico: </b>         	
                            </td>         	
                            <td align='left'>
                                <input onKeyPress="return acceptNum(event)" type="text" size="10" value="<?= $perim_cefalico ?>" name="perim_cefalico" <? if ($id_planilla)
                                            echo "readonly"
                                            ?>>
                                <font color="Red">En Centimetros (Decimales con ".")</font>           
                            </td>
                        </tr>

                        <tr>
                            <td align="right">
                                <b>Per. Perim. Cefalico/Edad: </b>         
                            </td>         	
                            <td align="left">			 	
                                <select name=percen_perim_cefali_edad Style="width:157px" <? if ($id_planilla)
                                            echo "disabled"
                                            ?>>
                                    <option value=-1>Seleccione</option>
                                    <option value=1 <? if ($percen_perim_cefali_edad == '1')
                                            echo "selected"
                                            ?>>-3</option>
                                    <option value=2 <? if ($percen_perim_cefali_edad == '2')
                                            echo "selected"
                                            ?>>3-97</option>
                                    <option value=3 <? if ($percen_perim_cefali_edad == '3')
                                            echo "selected"
                                            ?>>+97</option>
                                    <option value='' <? if ($percen_perim_cefali_edad == '')
                                            echo "selected"
                                            ?>>Dato Sin Ingresar</option>			  
                                </select>			
                            </td>
                        </tr>         
                    </table>
                </td>
            </tr>
                                        <? }else { ?>
            <tr>
                <td colspan="2">
                    <table style="margin-top: 10px" width="85%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?= $bgcolor_out ?>' class="bordes">   
                        <tr>
                            <td align="center" id='ma' colspan="2">
                                <b>Niños Mayores de 1 AÑO</b>         	
                            </td>            
                        </tr>
                        <tr>
                            <td align="right">
                                <b>IMC: </b>         	</td>         	
                            <td align='left'>
                                <input onKeyPress="return acceptNum(event)" type="text" size="40" value="<?= $imc ?>" name="imc" <? if ($id_planilla)
                                            echo "readonly"
                                            ?>>
                                <font color="Red">"0" en caso de que este vacio.</font>            
                            </td>
                        </tr>

                        <tr>
                            <td align="right">
                                <b>Percentilo IMC/Edad: </b>         	
                            </td>         	
                            <td align="left">			 	
                                <select name=percen_imc_edad Style="width:157px" <? if ($id_planilla)
                                            echo "disabled"
                                            ?>>
                                    <option value=-1>Seleccione</option>
                                    <option value=1 <? if ($percen_peso_edad == '1')
                                            echo "selected"
                                            ?>> <3 </option>
                                    <option value=2 <? if ($percen_peso_edad == '2')
                                            echo "selected"
                                            ?>> 3-10 </option>
                                    <option value=3 <? if ($percen_peso_edad == '3')
                                            echo "selected"
                                            ?>> >10-85 </option>
                                    <option value=4 <? if ($percen_peso_edad == '4')
                                            echo "selected"
                                            ?>> >85-97 </option>
                                    <option value=5 <? if ($percen_peso_edad == '5')
                                            echo "selected"
                                            ?>> >97 </option>
                                    <option value='' <? if ($percen_peso_talla == '')
                                            echo "selected"
                                                ?>>Dato Sin Ingresar</option>			  
                                </select>			</td>
                        </tr>        


                    </table>
                </td>
            </tr>
<? } ?>
        <tr>
            <td align="right">
                <b>Fecha Antisaranpion o triple:</b>			
            </td>
            <td align="left">
                <input type=text id=triple_viral name=triple_viral value='<?= fecha($triple_viral); ?>' size=15 
                       onKeyUp="mascara(this, '/', patron, true);
        return pulsar(event);" onkeypress="return pulsar(event);">                	    
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

<?= fin_pagina(); // aca termino  ?>
