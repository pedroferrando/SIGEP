<?
require_once ("../../config.php");
require_once ("../../lib/bibliotecaTraeme.php");
include_once("./funciones.php");


############################### Habilita el detalle despues de guardar la prestacion
$habDetalle = "disabled";


extract($_POST, EXTR_SKIP);
if ($parametros)
    extract($parametros, EXTR_OVERWRITE);

if ($_POST['guardar'] == "Guardar Prestacion") {
    $datos_etareos['estaembarazada'] = beneficiarioEmbarazadoUAD($clavebeneficiario, $fecha_comprobante);
    $datos_etareos['categoria'] = $grupo_etareo;

    $query_precio = buscaPractica($categoria, $codigoelegido, $patologia, $id_nomenclador_detalle, $datos_etareos, $sexo);

    $precio = $query_precio['precio'];
    $id_nomenclador = $query_precio['id_nomenclador'];
    if ($_POST['cantidad']) {
        $cantidad = $_POST['cantidad'];
    } else {
        $cantidad = 1;
    }
    $db->StartTrans();
    $id_prestacion = guardarPrestacion($id_comprobante, $id_nomenclador, $cantidad, $precio);
    $codigoconcatenado = $categoria . " " . $codigoelegido;
    coberturaBasica($cuie, $codigoconcatenado, $patologia, $fecha_comprobante, $grupo_etareo, $clavebeneficiario);
    mandarMailSoloPerinatal($id_prestacion);
    $db->CompleteTrans();
    $accion = "Se registro la Prestacion: " . $categoria . " " . $codigoelegido . " " . $patologia;
    $categoria = '';
    $codigoelegido = '';
    $patologia = '';
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
    $nombreefector = $res_comprobante->fields['nombreefector'];
    $cuie = $res_comprobante->fields['cuie'];
    $nombre_medico = $res_comprobante->fields['nombre_medico'];
    $fecha_comprobante = $res_comprobante->fields['fecha_comprobante'];
    $clavebeneficiario = $res_comprobante->fields['clavebeneficiario'];
    $tipo_nomenclador = $res_comprobante->fields['tipo_nomenclador'];
}

if ($pagina_viene == 'comprobante_admin.php') {
    $query_b = "SELECT nacer.smiafiliados.*,smitiposcategorias.*
		   FROM nacer.smiafiliados
	 	   left join nacer.smitiposcategorias on (afitipocategoria=codcategoria)
		   left join facturacion.comprobante using (clavebeneficiario)
	  	   where comprobante.id_comprobante=$id_comprobante";
    $res_comprobante_b = sql($query_b, "Error al traer el Comprobantes") or fin_pagina();

    $afiapellido = $res_comprobante_b->fields['afiapellido'];
    $afinombre = $res_comprobante_b->fields['afinombre'];
    $afidni = $res_comprobante_b->fields['afidni'];
    $descripcion_b = $res_comprobante_b->fields['descripcion'];
    $codcategoria = $res_comprobante_b->fields['codcategoria'];
    $fecha_nacimiento = $res_comprobante_b->fields['afifechanac'];
    $activo = $res_comprobante_b->fields['activo'];
    $sexo = trim($res_comprobante_b->fields['afisexo']);
    $clavebeneficiario = trim($res_comprobante_b->fields['clavebeneficiario']);
}

if ($pagina_viene == 'comprobante_admin_total.php') {
    $sql = "select	uad.beneficiarios.id_beneficiarios as id,
			case when uad.beneficiarios.apellido_benef_otro <> null then uad.beneficiarios.apellido_benef || ' ' || uad.beneficiarios.apellido_benef_otro else uad.beneficiarios.apellido_benef  end as a,
			case when uad.beneficiarios.nombre_benef_otro <> null then trim(uad.beneficiarios.nombre_benef||' '||uad.beneficiarios.nombre_benef_otro) else uad.beneficiarios.nombre_benef end as b,
			uad.beneficiarios.numero_doc as c,
			uad.beneficiarios.fecha_nacimiento_benef as d,
			uad.beneficiarios.calle as e,
			uad.beneficiarios.sexo as f,
			uad.beneficiarios.clave_beneficiario g,
                        uad.beneficiarios.tipo_documento h,
                        uad.beneficiarios.clase_documento_benef i
		 from uad.beneficiarios	 
		 where clave_beneficiario='$clavebeneficiario'";
    $res_comprobante = sql($sql, "Error al traer los Comprobantes") or fin_pagina();

    if (!$res_comprobante->rowcount()) {
        $query_b = "SELECT afiapellido a,afinombre b,afifechanac d, afisexo f,afidni c,afitipodoc h,aficlasedoc i
		   FROM nacer.smiafiliados
	 	   left join nacer.smitiposcategorias on (afitipocategoria=codcategoria)
		   left join facturacion.comprobante using (clavebeneficiario)
	  	   where comprobante.id_comprobante=$id_comprobante";
        $res_comprobante = sql($query_b, "Error al traer el Comprobantes") or fin_pagina();
    }

    $afiapellido = $res_comprobante->fields['a'];
    $afinombre = $res_comprobante->fields['b'];
    $afidni = $res_comprobante->fields['c'];
    $afitipodoc = $res_comprobante->fields['h'];
    $fecha_nacimiento = $res_comprobante->fields['d'];
    $sexo = trim($res_comprobante->fields['f']);
    $aficlasedoc = $res_comprobante->fields['i'];
}
$esembarazada = beneficiarioEmbarazadoUAD($clavebeneficiario, $fecha_comprobante);

echo $html_header;
?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>

<script>
    $(document).ready(function() {
        $("#td_categoria").on('change', '#categoria', combocambiado);
        $(document).on('guardatrazadora', 'body', combocambiado);

        $("#tema").on('change', "#codigoelegido", function() {
            var categoria = $("select#categoria").val();
            var codigoObjeto = $("select#codigoelegido").val();
            var comprobante = $("input#id_comprobante").val();
            $.post("prestacion_admin_2011.php", {'pagina_viene': 'comprobante_admin_total.php', 'categoria': categoria, 'codigoelegido': codigoObjeto, "id_comprobante": comprobante}, function(data) {
                var losdiagnosticos = $(data).find('select#patologia');
                $("#diagnostico").html(losdiagnosticos);
                $("#guardar").attr('disabled', 'disabled');
                $("#cantidad").attr('disabled', 'disabled');
                $("#cantidad").val(1);
            }
            );

        });

        $("#diagnostico").on('change', "#patologia", function() {
            var categoria = $("select#categoria").val();
            var codigoObjeto = $("select#codigoelegido").val();
            var patologia = $("select#patologia").val();
            var comprobante = $("input#id_comprobante").val();
            if ((patologia != -1)) {
                $.post("prestacion_admin_2011.php", {'pagina_viene': 'comprobante_admin_total.php', 'categoria': categoria, 'codigoelegido': codigoObjeto, 'patologia': patologia, "id_comprobante": comprobante}, function(data) {
                    var tienetrz = $(data).find('#guardar');
                    $("#guardar").replaceWith(tienetrz);
                    $("#cantidad").val(1);
                    if ((categoria == 'TA' || categoria == 'TL' || categoria == 'RO')) {
                        $("#cantidad").removeAttr('disabled');
                    } else {
                        $("#cantidad").attr('disabled', 'disabled');
                    }
                    $("#guardar").removeAttr('disabled');
                });
            } else {
                $("#cantidad").val(1);
                $("#cantidad").attr('disabled', 'disabled');
                $("#guardar").attr('disabled', 'disabled');
            }
        });
        $("#cantidad").on('keydown', function() {
            $("#guardar").attr('disabled', 'disabled');
        });
        $("#cantidad").on('keyup', function() {
            var categoria = $("select#categoria").val();
            var codigoObjeto = $("select#codigoelegido").val();
            var patologia = $("select#patologia").val();
            var comprobante = $("input#id_comprobante").val();
            var cantidad = $("#cantidad").val();
            $.post("prestacion_admin_2011.php", {'pagina_viene': 'comprobante_admin_total.php', 'categoria': categoria, 'codigoelegido': codigoObjeto, 'patologia': patologia, "id_comprobante": comprobante, "cantidad": cantidad}, function(data) {
                var tienetrz = $(data).find('#guardar');
                $("#guardar").replaceWith(tienetrz);
                $("#guardar").removeAttr('disabled');
            }
            );
        });

    });

    function combocambiado() {
        var categoria = $("select#categoria").val();
        var comprobante = $("input#id_comprobante").val();
        $.post("prestacion_admin_2011.php", {'pagina_viene': 'comprobante_admin_total.php', 'categoria': categoria, "id_comprobante": comprobante}, function(data) {
            var lostemas = $(data).find('select#codigoelegido');
            $("td#tema").html(lostemas);
            var losdiagnosticos = $(data).find('select#patologia');
            $("#diagnostico").html(losdiagnosticos);
            $("#guardar").attr('disabled', 'disabled');
            $("#cantidad").attr('disabled', 'disabled');
            $("#cantidad").val(1);
        });
    }



    function control_nuevos()
    {
        if (document.all.categoria.value == "-1") {
            alert('Debe Seleccionar una Prestacion');
            return false;
        }
        if (document.all.codigoelegido.value == "-1") {
            alert('Debe Seleccionar un Objeto de la Prestacion');
            return false;
        }

        if (document.all.patologia.value == "-1") {
            alert('Debe Seleccionar un Diagnostico');
            return false;
        }

        if (document.all.cantidad.value == "") {
            alert('Debe Ingresar una Cantidad');
            return false;
        }
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

<form name='form1' id='form1' action='prestacion_admin_2011.php' method='POST'>

    <input type="hidden" name="id" value="<?= $id ?>">
    <input type="hidden" id="id_comprobante" name="id_comprobante" value="<?= $id_comprobante ?>">
    <input type="hidden" name="id_prestacion_extra" value="<?= $id_prestacion ?>">
    <input type="hidden" name="id_smiafiliados" value="<?= $id_smiafiliados ?>">
    <input type="hidden" name="cuie" value="<?= $cuie ?>">
    <input type="hidden" name="clavebeneficiario" value="<?= $clavebeneficiario ?>">
    <input type="hidden" name="fecha_nacimiento" value="<?= $fecha_nacimiento ?>">
    <input type="hidden" name="fecha_comprobante" value="<?= $fecha_comprobante ?>">
    <input type="hidden" name="pagina_viene" value="<?= $pagina_viene ?>">
    <input type="hidden" name="pagina_listado" value="<?= $pagina_listado ?>">
    <input type="hidden" name="entidad_alta" value="<?= $entidad_alta ?>">
    <table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?= $bgcolor_out ?>' class="bordes">
        <tr id="mo">
            <td>
                <div style="font-size: large" color=white>CARGA DE PRESTACIONES</div> 
                <div id='titulo' name='titulo' style="font-size: medium" color=white><?= $accion ?></div>                
            </td>
        </tr>

        <tr>
            <td>
                <table width=80% align="center" class="bordes">
                    <tr>
                        <td id=mo colspan="4">
                            <b> Descripci&oacuten del COMPROBANTE</b>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <table align="center">
                                <tr>
                                    <td align="right">
                                        <b>Apellido:
                                    </td>         	
                                    <td align='left'>
                                        <input type='text' name='afiapellido' value='<?= $afiapellido; ?>' size=60 align='right' readonly></b>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" >
                                        <b> Nombre:
                                    </td>   
                                    <td >
                                        <input type='text' name='afinombre' value='<?= $afinombre; ?>' size=60 align='right' readonly></b>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" >
                                        <b> Documento:
                                    </td> 
                                    <td>
                                        <input type='text' name='afidni' value='<?= $afidni; ?>' size=60 align='right' readonly></b>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" >
                                        <b> Fecha de Nacimiento:
                                    </td> 
                                    <td >
                                        <input type='text' name='fecha_nacimeinto' value='<?= Fecha($fecha_nacimiento); ?>' size=60 align='right' readonly></b>
                                    </td>
                                </tr>                                

                                <?
                                if (!permisos_check('inicio', 'cambia_nomenclador_prestaciones'))
                                    $cambia_nomenclador_prestaciones = "disabled";
                                ?>
                                <tr>			
                                    <td align="right" id=mo>
                                        <b>Nomenclador en Uso:</b>
                                    </td>
                                    <td align="left" id=mo>		          			
                                        <select disabled="disabled" name=nomenclador_detalle Style="width:378px"
                                                onKeypress="buscar_combo(this);"
                                                onblur="borrar_buffer();"
                                                <!-- onchange="cambiar_nomenclador()"--> 
                                                <? echo $cambia_nomenclador_prestaciones ?>>
                                                <?
                                                $sql = "SELECT cn.id_nomenclador_detalle , modo_facturacion, descripcion
                                                        FROM nacer.efe_conv ec
                                                        INNER JOIN nacer.conv_nom cn USING (id_efe_conv)
                                                        INNER JOIN facturacion.nomenclador_detalle nd on nd.id_nomenclador_detalle=cn.id_nomenclador_detalle
                                                        WHERE ec.cuie='$cuie'
                                                        AND nd.fecha_desde <='$fecha_comprobante'
                                                        AND nd.fecha_hasta >='$fecha_comprobante'
                                                        AND ec.activo='t'";
                                                $res = sql($sql, "", 0);

                                                while (!$res->EOF) {
                                                    $id_nomenclador_detalle = $res->fields['id_nomenclador_detalle'];
                                                    $descripcion = $res->fields['descripcion'];
                                                    ?>
                                                    <option value=
                                                        <?=
                                                        $id_nomenclador_detalle;
                                                        echo " selected"
                                                        ?> >
                                                        <?= $descripcion ?>
                                                </option>
                                                <?
                                                $res->movenext();
                                            }
                                            ?>
                                        </select>

                                        <input type="hidden" name="id_nomenclador_detalle" value="<?= $id_nomenclador_detalle ?>"/>
                                    </td>
                                </tr>

                                <tr>
                                    <td align="right">
                                        <b>Nombre del Efector:
                                    </td>         	
                                    <td align='left'>
                                        <input type='text' name='nombre_efector' value='<?= $nombreefector; ?>' size=60 align='right' readonly></b>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <b> Fecha de la Prestacion:
                                    </td> 
                                    <td colspan="2">
                                        <input type='text' name='fecha_prestacion' value='<?= fecha($fecha_comprobante); ?>' size=60 align='right' readonly></b>
                                    </td>
                                </tr>
                                <tr>      
                                    <td align="right" title="Grupo Etareo al la Fecha de la Practica">
                                        <b>Grupo Etario: </b>
                                    </td>
                                    <td align="left">
                                        <?
                                        $grupo_etareo = calcularGrupoEtareo($fecha_nacimiento, $fecha_comprobante);
                                        $grupo_etareo['estaembarazada'] = $esembarazada;
                                        ?>					     		     		
                                        <input type="text" value="<? echo $grupo_etareo['descripcion'] ?>" Style="width:400px"size="8" readonly/>
                                        <input type="hidden" value="<? echo $grupo_etareo['categoria'] ?>" name="grupo_etareo" />

                                        <input type="hidden" value="<?= $dias_de_vida ?>" name="dias_de_vida"/>
                                    </td>			               
                                </tr>                  
                            </table>
                        </td>      
                    </tr>
                </table>     
                <table class="bordes" align="center" width="80%">
                    <tr align="center" id="sub_tabla">		 	
                        <td colspan="3">	
                            Referencia para Diagnostico
                        </td>
                        <td colspan="2">
                            <?
                            if ($tipo_nomenclador == 'BASICO')
                                $muestra_tipo_de_nomenclador = 'Basico';
                            if ($tipo_nomenclador == 'BASICO_2')
                                $muestra_tipo_de_nomenclador = 'Basico 2';
                            if ($tipo_nomenclador == 'CC_CATASTROFICO')
                                $muestra_tipo_de_nomenclador = 'CC Catastrofico';
                            if ($tipo_nomenclador == 'CC_NOCATASTROFICO')
                                $muestra_tipo_de_nomenclador = 'CC No Catastrofico';
                            if ($tipo_nomenclador == 'RONDAS')
                                $muestra_tipo_de_nomenclador = 'Rondas';
                            if ($tipo_nomenclador == 'PERINATAL_NO_CATASTROFICO')
                                $muestra_tipo_de_nomenclador = 'Perinatal No Catastrofico';
                            if ($tipo_nomenclador == 'REMEDIAR')
                                $muestra_tipo_de_nomenclador = 'Remediar';
                            if ($tipo_nomenclador == 'PERINATAL_CATASTROFICO')
                                $muestra_tipo_de_nomenclador = 'Perinatal Catastrofico';
                            if ($tipo_nomenclador == 'TALLERES')
                                $muestra_tipo_de_nomenclador = 'TALLERES';
                            ?>
                            Nueva PRESTACION - <?= "Nomenclador de tipo " . $muestra_tipo_de_nomenclador ?>
                        </td>
                    </tr>
                    <tr>

                        <td colspan="3" align="center">
                            <table align='center' border=1 bordercolor='#000000' bgcolor='#FFFFFF' width='100%' cellspacing=0 cellpadding=0>

                                <td width=30% bordercolor='#FFFFFF'>
                                    <table border=1 bordercolor='#FFFFFF' cellspacing=0 cellpadding=0 width=100%>
                                        <tr>
                                            <td width=30 bgcolor='#BEF781' bordercolor='#000000' height=20>&nbsp;</td>
                                            <td bordercolor='#FFFFFF'>Signos y Sintomas</td>
                                            <td width=30 bgcolor='#F3F781' bordercolor='#000000' height=20>&nbsp;</td>
                                            <td bordercolor='#FFFFFF'>Infecciones</td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;

                                            </td>
                                        </tr>
                                        <tr>        
                                            <td width=30 bgcolor='#46D7F4' bordercolor='#000000' height=20>&nbsp;</td>
                                            <td bordercolor='#FFFFFF'>Neoplacias</td>
                                            <td width=30 bgcolor='#F366D7' bordercolor='#000000' height=20>&nbsp;</td>
                                            <td bordercolor='#FFFFFF'>Lesiones</td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;

                                            </td>
                                        </tr>
                                        <tr>        
                                            <td width=30 bgcolor='#81BEF7' bordercolor='#000000' height=20>&nbsp;</td>
                                            <td bordercolor='#FFFFFF'>Anomalias Congenitas</td>
                                            <td width=30 bgcolor='#D0A9F5' bordercolor='#000000' height=20>&nbsp;</td>
                                            <td bordercolor='#FFFFFF'>Otros Diagnosticos</td>
                                        </tr>
                                    </table>
                                </td>
                            </table>
                        </td>

                        <td class="bordes"><table>
                                <tr>
                                    <td>	 	
                                <tr>      
                                    <td align="right">
                                        <b>Sexo: </b>
                                    </td>
                                    <td align="left">
                                        <?
                                        if (($sexo == 'M') || ($sexo == 'Masculino')) {
                                            $sexo_codigo = 'V';
                                            $sexo_1 = 'Masculino';
                                            $sexo = 'M';
                                        }
                                        if (($sexo == 'F') || ($sexo == 'Femenino')) {
                                            $sexo_codigo = 'M';
                                            $sexo_1 = 'Femenino';
                                            $sexo = 'F';
                                        }
                                        ?>					     		
                                        <input type="hidden" value="<?= $sexo_codigo ?>" name="sexo_codigo">
                                        <input type="text" value="<?= $sexo ?>" name=sexo Style="width:400px"size="8" readonly>
                                    </td>			               
                                </tr>
                                <tr>
                                    <td align="right" title="A la fecha de la Practica">
                                        <b>Edad: </b>
                                    </td>
                                    <td align="left">
                                        <?
                                        $codigo_edad = $grupo_etareo['edad'];


                                        if (strval($codigo_edad) < 1) {
                                            $codigo_edad = 0;
                                        } else {
                                            $codigo_edad = floor($codigo_edad);
                                        }
                                        ?>
                                        <input type="text" value="<?= $codigo_edad ?>" name=edad Style="width:400px"size="8" readonly> 
                                    </td>			               
                                </tr>
                                <tr>
                                    <td align="right">
                                        <b>Prestacion: </b>
                                    </td>
                                    <td align="left" id='td_categoria'>
                                        <select id='categoria' name='categoria' Style="width:400px"
                                                onKeypress="buscar_combo(this);"
                                                onblur="borrar_buffer();"
                                                onchange="borrar_buffer();">
                                            <option value=-1>Seleccione</option>
                                            <?
                                            $res_efectores = buscarCategoriasPadre($id_nomenclador_detalle, $tipo_nomenclador, $grupo_etareo, $sexo);

                                            while (!$res_efectores->EOF) {
                                                $categoriaPrestacion = $res_efectores->fields['categoria'];
                                                ?>		                 
                                                <option value='<?= $categoriaPrestacion; ?>' <?
                                                if ($categoria == $categoriaPrestacion)
                                                    echo "selected"
                                                    ?>> 
                                                    <?= $categoriaPrestacion ?></option>
                                                <?
                                                $res_efectores->movenext();
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td align="right">
                                        <b>Objeto de la Prestaci&oacute;n: </b>
                                    </td>
                                    <td align="left" id="tema">
                                        <select id='codigoelegido' name='codigoelegido' Style="width:400px">
                                            <option value=-1>Seleccione</option>
                                            <?
                                            if (($categoria != '-1') && ($categoria != NULL)) {
                                                $res_codigos = prestacionesPorCategoria($categoria, $id_nomenclador_detalle, $tipo_nomenclador, $grupo_etareo, $sexo);
                                                while (!$res_codigos->EOF) {
                                                    $codigoObjeto = $res_codigos->fields['codigo'];
                                                    ?>
                                                    <option value="<? echo $codigoObjeto; ?>"
                                                    <?
                                                    if ($codigoelegido == $codigoObjeto)
                                                        echo "selected"
                                                        ?>>
                                                                <?= $codigoObjeto ?>
                                                    </option>
                                                    <?
                                                    $res_codigos->movenext();
                                                }
                                            }
                                            ?>
                                        </select>
                                    </td>

                                    <td align="left">
                                        <?
                                        if ($codigoelegido == '-1') {
                                            $color = 'red';
                                            $tema_cartel = "*";
                                        } else {
                                            $color = 'green';
                                            $tema_cartel = $codigoelegido;
                                        }
                                        ?>                                        
                                    </td>
                                </tr>

                                <tr>
                                    <td align="right">					 			
                                        <b>Diagnostico: </b>
                                    </td>
                                    <td align="left" id='diagnostico'>
                                        <select id="patologia" name=patologia Style="width:400px">
                                            <option value=-1>Seleccione</option>
                                            <?
                                            if (($categoria != null && $categoria != "-1") && ($codigoelegido != null && $codigoelegido != "-1")) {
                                                $codigocompleto = $categoria . " " . $codigoelegido;
                                                $res_diagnosticos = diagnosticosPorCodigo($codigocompleto, $id_nomenclador_detalle, $grupo_etareo, $tipo_nomenclador, $sexo);
                                            }
                                            if ($res_diagnosticos != null) {
                                                while (!$res_diagnosticos->EOF) {
                                                    $codigo_diag = $res_diagnosticos->fields['codigo'];
                                                    $color_diag = $res_diagnosticos->fields['color'];
                                                    $desc_diagnostico = $res_diagnosticos->fields['codigo'] . ' [' . $res_diagnosticos->fields['diagnostico'] . ']';
                                                    ?>
                                                    <option value=<?= $codigo_diag; ?> <?php
                                                    if ($patologia == $codigo_diag)
                                                        echo "selected";
                                                    ?> <?= $color_diag ?>> 
                                                        <?= $desc_diagnostico ?></option>
                                                    <?
                                                    $res_diagnosticos->movenext();
                                                }
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td align="left">
                                        <?
                                        if ($patologia == '-1') {
                                            $color = 'red';
                                            $patologia_cartel = '*';
                                        } else {
                                            $color = 'green';
                                            $patologia_cartel = $patologia;
                                        }
                                        ?>	
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <b>Cantidad:</b> 
                                    </td>
                                    <td>
                                        <input type='text' name='cantidad' id="cantidad" value=1 disabled="disabled"/>
                                    </td>
                                </tr>
                            </table>
                        </td>

                    </tr>

                    <tr align="center"> 
                        <td colspan="4" align="center">

                            <?
                            if ($patologia != '-1') {
                                //Consulta si hay un formulario asociado a la practica
                                $carga_trz = cargaTrazadora($codigocompleto, $patologia, $grupo_etareo['categoria']);
                            }
                            if ($carga_trz[0]) {
                                $query_precio = buscaPractica($categoria, $codigoelegido, $patologia, $id_nomenclador_detalle, $grupo_etareo, $sexo);
                                $precio = $query_precio['precio'];
                                $id_nomenclador = $query_precio['id_nomenclador'];
                                if ($cantidad == "" or $cantidad == null) {
                                    $cantidad = 1;
                                }
                                //Pasa los datos necesarios para guardar la practica desde Trazadora
                                $datos_practica['id_nomenclador'] = $id_nomenclador;
                                $datos_practica['cantidad'] = $cantidad;
                                $datos_practica['precio'] = $precio;
                                $datos_practica['codigo'] = $categoria . " " . $codigoelegido;
                                $datos_practica['diagnostico'] = $patologia;
                                $datos_practica['grupo_precio'] = $query_precio['grupo_precio'];
                                ?>  
                                <input Style="width:300px;height:40px" type="button" value="Guardar Prestacion*" id='guardar' name='guardar' title='Guardar Prestacion' 
                                       onclick="if (control_nuevos())
                window.open('<?= encode_link($carga_trz[1], array("cuiel" => $cuie, "fecha_comprobante" => $fecha_comprobante, "clave_beneficiario" => $clavebeneficiario, "grupo_etareo" => $grupo_etareo['categoria'], "edad" => $codigo_edad, "sexo" => $sexo, "apellido" => $afiapellido, "nombre" => $afinombre, "num_doc" => $afidni, "tipo_doc" => $afitipodoc, "fecha_nac" => $fecha_nacimiento, "clase_doc" => $aficlasedoc, "datos_practica" => $datos_practica, "id_comprobante" => $id_comprobante)) ?>', 'Trazadora', 'dependent:yes,width:900,height=800,top=1,left=60,scrollbars=yes');">
                                       <?
                                   } else {
                                       ?>
                                <input disabled='disabled' type='submit' id='guardar' name='guardar' value='Guardar Prestacion' title='Guardar Prestacion' Style='width:300px;height:40px;' onclick='return control_nuevos()' <? $hab_on_line ?> >
                                <?
                            }
                            ?>		  		
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
                            <?php
                                $lnkVolver = "../inscripcion/ins_listado.php";
                            ?>
                            <input type=button name="volver" value="Volver" 
                                   onclick="document.location = '<?php echo $lnkVolver; ?>'" 
                                   title="Volver al Listado" style="width:150px"> 
                            <?php
                                $ref = encode_link("comprobante_admin_total.php", array("clavebeneficiario" => $clavebeneficiario, "pagina_listado" => $pagina_listado, "pagina_viene" => "prestacion_admin.php", "estado" => $estado, "entidad_alta" => $entidad_alta));
                            ?>
                                <input type=button name="volver" value="Volver al Beneficiario" onclick="document.location = '<?= $ref ?>'"title="Volver a los comprobantes" style="width:150px">  
                            
                        </td>   
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</form>

<?= fin_pagina(); // aca termino   ?>
