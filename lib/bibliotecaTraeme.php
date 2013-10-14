<?php

define("NRO_FACTURA_MISIONES", "(case when facturacion.factura.nro_fact_offline <> ''
  then cast(facturacion.factura.nro_fact_offline as text)
  else cast(facturacion.factura.id_factura as text) end) as numero_factura, ");

require_once ("../../modulos/facturacion/funciones.php");

function traemeNomenclador($vigencia, $cod) {
    $cod_a = explode('-', $cod);
    $nro_orden = intval($cod_a[1]);
    $codigo = $cod_a[0];
    if ($vigencia['id'] != 0) {
        if ($vigencia['modo'] < 2) {
            $modo_facturacion = "viejo";
            $sql_n = "SELECT id_nomenclador, precio, categoria 
              FROM facturacion.nomenclador 
              WHERE replace(codigo, ' ', '') = replace('$codigo', ' ', '') 
              AND id_nomenclador_detalle = " . $vigencia['id'];
        } else {
            $codigo = desarmarCodigoPrestacion($codigo);
            $modo_facturacion = "nuevo";
            $sql_n = "SELECT id_grupo_prestacion as id_nomenclador, precio 
              FROM nomenclador.grupo_prestacion 
              WHERE codigo = '" . $codigo['codigo'] . "'
              AND categoria_padre = '" . $codigo['categoria_padre'] . "'
              AND id_nomenclador_detalle = " . $vigencia['id'];
        }
        $result_n = sql($sql_n, "No se encuentra nomenclador con cod: $codigo", 0);

        $datos_nomenclador[0] = $result_n->fields['id_nomenclador'];
        $datos_nomenclador[1] = $result_n->fields['precio'];
        $datos_nomenclador[2] = $modo_facturacion;
        $datos_nomenclador[3] = $codigo;
        $datos_nomenclador[4] = $result_n->fields['categoria'];
        $datos_nomenclador[6] = $vigencia;


        if ($datos_nomenclador[0]) {
            if ($nro_orden > 0) {
                $sql_a = "SELECT id_anexo, precio 
            FROM facturacion.anexo 
            WHERE id_nomenclador = $datos_nomenclador[0] 
            AND id_nomenclador_detalle = '" . $vigencia['id'] . "' 
            AND numero = $nro_orden";
                $result_a = sql($sql_a, "", 0);
                if ($result_a->RowCount() > 0) {
                    $datos_nomenclador[1] = $result_a->fields['precio'];
                    $datos_nomenclador[5] = $result_a->fields['id_anexo'];
                } else {
                    $datos_nomenclador[5] = -1;
                }
            } else {
                $datos_nomenclador[5] = -1;
            }
        } else {
            $datos_nomenclador[0] = 0;
            $datos_nomenclador[1] = 0;
            $datos_nomenclador[2] = 0;
            $datos_nomenclador[3] = $codigo;
            $datos_nomenclador[4] = 0;
            $datos_nomenclador[5] = -1;
            $datos_nomenclador[6] = $vigencia;
        }
    } else {
        $datos_nomenclador[0] = 0;
        $datos_nomenclador[4] = 0;
        $datos_nomenclador[5] = 0;
        $datos_nomenclador[2] = 0;
        $datos_nomenclador[1] = 0;
        $datos_nomenclador[3] = $codigo;
        $datos_nomenclador[6] = $vigencia;
    }

    $datos_nomenclador[7] = '';

    return $datos_nomenclador;
}

function buscarPrecioSinConvenio($codigo, $fecha) {
    $fechaprestacion = Fecha_db($fecha);

    $sql = "SELECT id_nomenclador_detalle , modo_facturacion  
        FROM  facturacion.nomenclador_detalle nd 
        WHERE  nd.fecha_desde <='$fechaprestacion'
        AND nd.fecha_hasta >='$fechaprestacion'
	order by id_nomenclador_detalle";
    $result = sql($sql, "", 0);

    $vigencia['id'] = $result->fields['id_nomenclador_detalle'];
    $vigencia['modo'] = $result->fields['modo_facturacion'];
    $datos_nomenclador = traemeNomenclador($vigencia, $codigo);
    return $datos_nomenclador;
}

function delamuerte($idfactura) {
    $sql = "SELECT d.* FROM facturacion.debito d
            WHERE id_factura ='$idfactura'
            AND codigo_deb LIKE '%-%'
            ORDER BY codigo_deb";
    $resultado = sql($sql);
    while (!$resultado->EOF) {
        $codigo = split("-", $resultado->fields['codigo_deb']);
        if ($codigo[1]) {
            $sql_anexo = "SELECT id_anexo, precio 
            FROM facturacion.anexo 
            WHERE id_nomenclador = '" . $resultado->fields['id_nomenclador'] . "' 
            AND numero = '$codigo[1]'";
            $precioanexo = sql($sql_anexo);

            $sql_update = "UPDATE facturacion.debito
                            SET monto=" . $precioanexo->fields['precio'] . "
                                WHERE id_debito='" . $resultado->fields['id_debito'] . "'";
            sql($sql_update);
        }
        $resultado->movenext();
    }
}

function traemeNomencladorConAnexo($vigencia, $codigo, $nro_orden) {
    //$cod_a = explode('-', $cod);
    //$nro_orden = intval($cod_a[1]);
    //$codigo = $cod_a[0];
    if ($vigencia['id'] != 0) {
        $modo_facturacion = "viejo";
        $sql_n = "SELECT id_nomenclador, precio, categoria 
              FROM facturacion.nomenclador 
              WHERE replace(codigo, ' ', '') = replace('$codigo', ' ', '') 
              AND id_nomenclador_detalle = " . $vigencia['id'];

        $result_n = sql($sql_n, "No se encuentra nomenclador con cod: $cod_nom", 0);

        $datos_nomenclador[0] = $result_n->fields['id_nomenclador'];
        $datos_nomenclador[1] = $result_n->fields['precio'];
        $datos_nomenclador[2] = $modo_facturacion;
        $datos_nomenclador[3] = trim($codigo);
        $datos_nomenclador[4] = $result_n->fields['categoria'];
        $datos_nomenclador[6] = $vigencia;


        if ($datos_nomenclador[0]) {
            if ($nro_orden > 0) {
                $sql_a = "SELECT id_anexo, precio 
                            FROM facturacion.anexo 
                            WHERE id_anexo = '$nro_orden'";
                $result_a = sql($sql_a, "", 0);
                if ($result_a->RowCount() > 0) {
                    $datos_nomenclador[1] = $result_a->fields['precio'];
                    $datos_nomenclador[5] = $result_a->fields['id_anexo'];
                } else {
                    $datos_nomenclador[5] = -1;
                }
            } else {
                $datos_nomenclador[5] = -1;
            }
        } else {
            $datos_nomenclador[0] = 0;
            $datos_nomenclador[1] = 0;
            $datos_nomenclador[2] = 0;
            $datos_nomenclador[3] = $codigo;
            $datos_nomenclador[4] = 0;
            $datos_nomenclador[5] = -1;
            $datos_nomenclador[6] = $vigencia;
        }
    } else {
        $datos_nomenclador[0] = 0;
        $datos_nomenclador[4] = 0;
        $datos_nomenclador[5] = 0;
        $datos_nomenclador[2] = 0;
        $datos_nomenclador[1] = 0;
        $datos_nomenclador[3] = $codigo;
        $datos_nomenclador[6] = $vigencia;
    }
    return $datos_nomenclador;
}

function esNomencladorVacuna($datosnomenclador) {
    switch (str_replace(' ', '', $datosnomenclador[3])) {
        case "NPE42":
            $ctrl = true;
            break;
        default:
            $ctrl = false;
            break;
    }
    return $ctrl;
}

function esNomencladorTaller($datosnomenclador) {
    switch (str_replace(' ', '', $datosnomenclador[3])) {
        case "CMI65":
            $ctrl = true;
            break;
        case "CMI66":
            $ctrl = true;
            break;
        case "CMI67":
            $ctrl = true;
            break;
        case "RCM107":
            $ctrl = true;
            break;
        case "RCM108":
            $ctrl = true;
            break;
        case "RCM109":
            $ctrl = true;
            break;
        default:
            $ctrl = false;
            break;
    }
    return $ctrl;
}

function traemeLaPractica($clavebeneficiario, $codigo, $fecha_original, $anexo = null) {
    if ($codigo['tipo'] == 'viejo') {
        if ($anexo != "") {
            $queryanexo = " AND p.id_anexo='$anexo' ";
        }
        $query = "SELECT id_prestacion, fecha_comprobante::date
                        from facturacion.prestacion p
  			INNER JOIN facturacion.comprobante using (id_comprobante)
  			INNER JOIN facturacion.nomenclador using (id_nomenclador)
                        WHERE clavebeneficiario = '$clavebeneficiario'
                        AND replace(codigo,' ','')=replace('" . $codigo['codigo'] . "',' ','')
                        $queryanexo 
                        AND fecha_comprobante >= to_date('$fecha_original','DD-MM-YYYY')
                        ORDER BY fecha_comprobante ASC";
    } else {
        $codigodesarmado = desarmarCodigoPrestacion($codigo['codigo']);
        $query = "SELECT id_prestacion, fecha_comprobante::date
                        from facturacion.prestacion p
  			INNER JOIN facturacion.comprobante using (id_comprobante)
  			INNER JOIN facturacion.nomenclador using (id_nomenclador)
                        WHERE clavebeneficiario = '$clavebeneficiario'
                        AND codigo = '" . $codigodesarmado['categoria_padre'] . " " . $codigodesarmado['profesional'] . "'
                        AND diagnostico = '" . $codigodesarmado['codigo'] . "'
                        AND fecha_comprobante >= to_date('$fecha_original','DD-MM-YYYY')
                        ORDER BY fecha_comprobante ASC";
    }
    $res_rel = sql($query, "Error al buscar la fecha de la practica") or fin_pagina();
    if ($res_rel->recordCount() > 0) {
        return $res_rel->fields['fecha_comprobante'];
    } else {
        return null;
    }
}

function grupoEtareoDelAfiliado($clavebeneficiario, $fechapractica, $idpadron) {
    $periodo = buscarPeriodoPorId($idpadron);
    if ($periodo['tipo'] == 'V') {
        $tabla = ' from nacer.smiafiliados ';
        $condiciondeperiodo = '';
    } else {
        $tabla = ' from nacer.smiafiliadoshst ';
        $reordenarperiodo = split('/', $periodo['periodo']);
        $elperiodo = $reordenarperiodo[1] . $reordenarperiodo[0];
        $condiciondeperiodo = " AND periodo='$elperiodo' ";
    }


    $grupoeta = 2;
    $fechapracticaaux = split("-", $fechapractica);
    $fechapractica = $fechapracticaaux[2] . "-" . $fechapracticaaux[1] . "-" . $fechapracticaaux[0];
    $sql3 = "SELECT fechaprobableparto,fechaefectivaparto,afifechanac,afisexo, age(afifechanac) edad,
      case when ('$fechapractica'-fechaprobableparto ) <= 45 and ('$fechapractica'-fechaprobableparto ) >= 0
      then TRUE else FALSE END puerpera,
      ('$fechapractica'-fechaprobableparto )  fechapuerpera,
      case when age('$fechapractica',afifechanac)<interval '6 years' then TRUE ELSE FALSE END esmenor6,
      case when fechaprobableparto>'$fechapractica' then TRUE ELSE FALSE END esembarazada,
      case when age('$fechapractica',afifechanac)<interval '1 years' then TRUE ELSE FALSE END reciennacido
    " . $tabla . "
            where clavebeneficiario='$clavebeneficiario'
            $condiciondeperiodo";
    $res_grupo = sql($sql3) or fin_pagina();
    //$datosafi = afiliadoEnPadronPorFecha($fechapracticaaux, $clavebeneficiario);
    $esembarazada = $res_grupo->fields['esembarazada'];
    $espuerpera = $res_grupo->fields['puerpera'];
    $afisexo = $res_grupo->fields['afisexo'];
    $esmenor6 = $res_grupo->fields['esmenor6'];
    $reciennacido = $res_grupo->fields['reciennacido'];

    if (($esembarazada == 't' || $espuerpera == 't') && ($afisexo == 'F')) {
        $grupoeta = 0;
    } elseif (($esmenor6 == 't') || ($reciennacido == 't')) {
        $grupoeta = 1;
    }

    return $grupoeta;
}

function buscarCategoriasPadre($id_nomenclador_detalle, $tipo_nomenclador, $grupo_etareo, $sexo) {
    $categoria = $grupo_etareo['categoria'];

    if ($sexo == 'F') {
        $condicion_sexo = "AND F=TRUE";
    } elseif ($sexo == 'M') {
        $condicion_sexo = "AND M=TRUE";
    } else {
        $condicion_sexo = "AND F=TRUE
                           AND M=TRUE";
    }

    if ($grupo_etareo['estaembarazada']) {
        $cuandoesembarazada = "OR embarazada > 0";
    } else {
        $cuandoesembarazada = "";
    }

    $sql_categorias = "SELECT distinct(split_part(codigo,' ',1)) categoria
                    FROM facturacion.nomenclador 
                    WHERE id_nomenclador_detalle='$id_nomenclador_detalle'
                    AND ($categoria > 0 $cuandoesembarazada)
                    AND tipo_nomenclador='$tipo_nomenclador'                    
                    $condicion_sexo
                    order by categoria";
    $res_categorias = sql($sql_categorias) or fin_pagina();
    return $res_categorias;
}

function prestacionesPorCategoria($categoria, $id_nomenclador_detalle, $tipo_nomenclador, $grupo_etareo, $sexo) {
    if ($sexo == 'F') {
        $condicion_sexo = "AND F=TRUE";
    } elseif ($sexo == 'M') {
        $condicion_sexo = "AND M=TRUE";
    } else {
        $condicion_sexo = "AND F=TRUE
                           AND M=TRUE";
    }
    $categoria_etaria = $grupo_etareo['categoria'];

    if ($grupo_etareo['estaembarazada']) {
        $cuandoesembarazada = " OR embarazada > 0";
    } else {
        $cuandoesembarazada = "";
    }
    $sql_codigos = "SELECT distinct(split_part(codigo,' ',2)) codigo
                    FROM facturacion.nomenclador 
                    WHERE id_nomenclador_detalle='$id_nomenclador_detalle'
                    AND ($categoria_etaria > 0 $cuandoesembarazada)                       
                    AND tipo_nomenclador='$tipo_nomenclador'
                    $condicion_sexo
                    AND split_part(codigo,' ',1)='$categoria'
                    order by codigo";
    $res_codigos = sql($sql_codigos) or fin_pagina();
    return $res_codigos;
}

function diagnosticosPorCodigo($codigo, $id_nomenclador_detalle, $grupo_etareo, $tipo_nomenclador, $sexo) {
    if ($sexo == 'F') {
        $condicion_sexo = "AND n.F=TRUE";
    } elseif ($sexo == 'M') {
        $condicion_sexo = "AND n.M=TRUE";
    } else {
        $condicion_sexo = "AND n.F=TRUE
                           AND n.M=TRUE";
    }

    if ($grupo_etareo['estaembarazada']) {
        $cuandoesembarazada = " OR embarazada > 0";
    } else {
        $cuandoesembarazada = "";
    }
    $categoria_etaria = "n." . $grupo_etareo['categoria'];
    $sql_diagnosticos = "SELECT  distinct(n.diagnostico) codigo,n.descripcion,color,p.descripcion as diagnostico
                    FROM facturacion.nomenclador n
                    left join nomenclador.patologias p on (n.diagnostico= p.codigo) 
                    WHERE n.id_nomenclador_detalle='$id_nomenclador_detalle'
                    AND ($categoria_etaria > 0 $cuandoesembarazada)
                    $condicion_sexo
                    AND n.codigo='$codigo'
                    AND n.tipo_nomenclador='$tipo_nomenclador'
                    AND habilitado = TRUE     
                    order by n.diagnostico";
    $res_diagnosticos = sql($sql_diagnosticos) or fin_pagina();
    return $res_diagnosticos;
}

function buscaPractica($categoria, $tema, $patologia, $id_nomenclador_detalle, $grupo_etareo, $sexo) {

    if ($sexo == 'F') {
        $condicion_sexo = "AND F=TRUE";
    } elseif ($sexo == 'M') {
        $condicion_sexo = "AND M=TRUE";
    } else {
        $condicion_sexo = "";
    }

    $categoria_etaria = $grupo_etareo['categoria'];

    $codigo = $categoria . " " . $tema;
    $sql_diagnosticos = "SELECT *
                    FROM facturacion.nomenclador 
                    WHERE id_nomenclador_detalle='$id_nomenclador_detalle'
                    $condicion_sexo                    
                    AND codigo='$codigo'
                    AND diagnostico='$patologia'";
    $res_diagnosticos = sql($sql_diagnosticos) or fin_pagina();

    if (!$res_diagnosticos->EOF) {
        if ($grupo_etareo['estaembarazada']) {
            if ($res_diagnosticos->fields['embarazada'] > 0) {
                $datos['precio'] = $res_diagnosticos->fields['embarazada'];
                $datos['grupo_precio'] = 'embarazada';
            } else {
                $datos['precio'] = $res_diagnosticos->fields[$categoria_etaria];
                $datos['grupo_precio'] = $categoria_etaria;
            }
        } else {
            $datos['precio'] = $res_diagnosticos->fields[$categoria_etaria];
            $datos['grupo_precio'] = $categoria_etaria;
        }
        $datos['id_nomenclador'] = $res_diagnosticos->fields['id_nomenclador'];
        $datos[1] = $datos['precio'];
        $datos[2] = 'nuevo';
        $datos[0] = $datos['id_nomenclador'];
        $datos[7] = $res_diagnosticos->fields['tipo_nomenclador'];
    } else {
        $datos['id_nomenclador'] = 0;
        $datos['precio'] = 0;
        $datos[1] = 0;
        $datos[0] = 0;
        $datos[7] = 0;
    }

    return $datos;
}

function estadoBeneficiarioUAD($clavebeneficiario) {
    $sql = "SELECT estado_envio from uad.beneficiarios where clave_beneficiario='$clavebeneficiario'";
    $result = sql($sql);

    switch ($result->fields['estado_envio']) {
        case 'e':
            $mensaje = 'Enviado';
            break;
        case 'p':
            $mensaje = 'Pendiente de Verificacion';
            break;
        case 'n':
            $mensaje = 'Envio Pendiente';
            break;
        default:
            break;
    }

    return $mensaje;
}

function beneficiarioEmbarazadoUAD($clavebeneficiario, $fecha) {

    $embarazada = false;
    $sql = "SELECT fecha_probable_parto from uad.beneficiarios where clave_beneficiario='$clavebeneficiario'";
    $result = sql($sql);

    $FPP_BEN = '';
    if ($result->fields['fecha_probable_parto'] != null) {
        $FPP_BEN = strtotime($result->fields['fecha_probable_parto']);
    }

    $sql = "SELECT fechaprobableparto from nacer.smiafiliados where clavebeneficiario='$clavebeneficiario'";
    $result = sql($sql);

    $FPP_SMI = '';
    if ($result->fields['fechaprobableparto'] != null) {
        $FPP_SMI = strtotime($result->fields['fechaprobableparto']);
    }

    //si notiene dato de FPP en ninguna
    if ($FPP_BEN == '' and $FPP_SMI == '') {
        return $embarazada;
    }

    $FPP = '';
    if ($FPP_BEN == '' and $FPP_SMI != '') {
        $FPP = $FPP_SMI;
    }

    if ($FPP_BEN != '' and $FPP_SMI == '') {
        $FPP = $FPP_BEN;
    }

    //si tengo ambas fechas evaluo ambas

    if ($FPP_BEN != '' and $FPP_SMI != '') {
        if ($FPP_BEN > $FPP_SMI) {
            $FPP = $FPP_BEN;
        } else {
            if ($FPP_BEN < $FPP_SMI) {
                $FPP = $FPP_SMI;
            } else {
                $FPP = $FPP_SMI;
            }
        }
    }

//if (($result->fields['fecha_probable_parto'] < $fecha) or ($result->fields['fecha_probable_parto'] == null)) {
    $fecha = strtotime($fecha);
    if ($FPP > $fecha OR $FPP == $fecha) {
        $embarazada = true;
    } else {
        if ($FPP < $fecha) {
            //se evalua el puerperio
            if ((($fecha - $FPP) / 60 / 60 / 24) <= 45) {
                $embarazada = true;
            }
        }
    }
    return $embarazada;
}

function beneficiarioEmbarazadoSMI($clavebeneficiario, $periodo_comprobante, $fecha_comprobante) {
    $embarazada = false;

    $periodo_buscado = $periodo_comprobante;

    do {
        $sqlperiodo = "SELECT * from facturacion.periodo where id_periodo='$periodo_buscado'";
        $resultp = sql($sqlperiodo);
        if ($resultp->fields['tipo'] == 'H') {
            $fechaperiodo = split('/', $resultp->fields['periodo']);
            $fechaperiodo = $fechaperiodo[1] . $fechaperiodo[0];

            $sql = "SELECT embarazoactual,fechadiagnosticoembarazo from nacer.smiafiliadoshst where clavebeneficiario='$clavebeneficiario' and periodo='$fechaperiodo'";
            $result = sql($sql);
        } else {
            $sql = "SELECT embarazoactual,fechadiagnosticoembarazo from nacer.smiafiliados where clavebeneficiario='$clavebeneficiario'";
            $result = sql($sql);
        }
        $fecha_comprobante_time = strtotime($fecha_comprobante);
        $fechadiagnosticoembarazo_time = strtotime($result->fields['fechadiagnosticoembarazo']);

        if (($result->fields['embarazoactual'] == 'S') && ($fechadiagnosticoembarazo_time <= $fecha_comprobante_time)) {
            //probar
            $embarazada = TRUE;
            break;
        }
        $periodo_buscado++;
    } while ($resultp->fields['tipo'] == 'H' && !pasaron2meses($periodo_buscado, $periodo_comprobante));

    return $embarazada;
}

function calcularGrupoEtareo($fecha_nacimiento, $fecha_comprobante) {
    $dias_de_vida = GetCountDaysBetweenTwoDates($fecha_nacimiento, $fecha_comprobante);
    $edad = calcularEdad($fecha_nacimiento, $fecha_comprobante);
    $grupo['edad'] = $edad;
    if (($dias_de_vida >= 0) && ($dias_de_vida <= 28)) {
        $grupo['categoria'] = 'neo';
        $grupo['descripcion'] = 'Grupo NeoNatal';
    } elseif (($dias_de_vida > 28) && ($dias_de_vida <= 364)) {
        $grupo['categoria'] = 'cero_a_uno';
        $grupo['descripcion'] = 'Grupo Menor de 1 año';
    } elseif (($dias_de_vida > 364) && ($dias_de_vida <= 2189 )) {
        $grupo['categoria'] = 'uno_a_seis';
        $grupo['descripcion'] = 'Grupo de 1 a 5 años';
    } elseif (($dias_de_vida > 2189) && ($dias_de_vida <= 3649 )) {
        $grupo['categoria'] = 'seis_a_diez';
        $grupo['descripcion'] = 'Grupo de 6 a 9 años';
    } elseif (($dias_de_vida > 3649) && ($dias_de_vida <= 7299 )) {
        $grupo['categoria'] = 'diez_a_veinte';
        $grupo['descripcion'] = 'Grupo de 10 a 19 años';
    } elseif (($dias_de_vida > 7299) && ($dias_de_vida <= 23724 )) {
        $grupo['categoria'] = 'veinte_a_sesentaycuatro';
        $grupo['descripcion'] = 'Grupo de 20 a 64 años';
    } else {
        $grupo['categoria'] = 'veinte_a_sesentaycuatro';
        $grupo['descripcion'] = 'Grupo de 20 a 64 años';
    }
    return $grupo;
}

function calcularEdad($fecha_nacimiento, $fecha_comprobante) {
    $dias_de_vida = GetCountDaysBetweenTwoDates($fecha_nacimiento, $fecha_comprobante);
    $edad = $dias_de_vida / 365;
    return $edad;
}

function traeCuiesPorUsuario($idusuario) {
    $sql = "select distinct(s.cuie), nombreefector
            from  facturacion.smiefectores s
            inner join sistema.usu_efec ue on ue.cuie=s.cuie
            where id_usuario='$idusuario'";
    $result = sql($sql);
    $cuieses = "('";
    while (!$result->EOF) {
        $cuieses.=$result->fields['cuie'];
        $result->movenext();
        if ((!$result->EOF)) {
            $cuieses.="','";
        } else {
            $cuieses.="'";
        }
    }
    $cuieses.=")";
    return $cuieses;
}

function descripcionDeCategoriaEtaria($categoria_etaria) {

    switch ($categoria_etaria) {
        case 'neo':
            $descripcion_etaria = 'Grupo NeoNatal';
            break;
        case 'cero_a_uno':
            $descripcion_etaria = 'Grupo Menor de 1 año';
            break;
        case "uno_a_seis":
            $descripcion_etaria = 'Grupo de 1 a 5 años';
            break;
        case "seis_a_diez":
            $descripcion_etaria = 'Grupo de 6 a 9 años';
            break;
        case "diez_a_veinte":
            $descripcion_etaria = 'Grupo de 10 a 19 años';
            break;
        case "veinte_a_sesentaycuatro":
            $descripcion_etaria = 'Grupo de 20 a 64 años';
            break;
    }

    return $descripcion_etaria;
}

/*
  trz[0] -> indica si hay resultados
  trz[1] -> contiene el nombre del archivo de trazadora
  trz[2] -> contiene el nombre de la trazadora
  trz[3] -> contiene la descripcion
 */

function cargaTrazadora($codigocompleto, $patologia, $grupo_etareo) {
    $trz[0] = false;
    if ($patologia != "") {
        $where .= " AND diagnostico='$patologia' ";
    } else {
        $where .= " AND diagnostico IS NULL ";
    }
    $sql = "SELECT * from nomenclador.descripciones
            WHERE codigo='$codigocompleto'
              AND grupo_etareo='$grupo_etareo' 
              " . $where;
    $res = sql($sql);
    if (($res->fields['trz'] != '') or ($res->fields ['trz'] != null )) {
        $trz[0] = true;
        $trz[2] = $res->fields['trz'];
        $trz[3] = $res->fields['descripcion'];
        switch ($res->fields['trz']) {
            case 'NINO':
                $trz[1] = "../trazadoras/nino_admin_new.php";
                break;
            case 'NINO_PESO':
                $trz[1] = "../trazadoras/nino_admin_peso.php";
                break;
            case 'ADOLESCENTE':
                $trz[1] = "../trazadoras/adolecente_admin.php";
                break;
            case 'PARTO':
                $trz[1] = "../trazadoras/par_admin.php";
                break;
            case 'EMB':
                $trz[1] = "../trazadoras/emb_admin.php";
                break;
            case 'ADULTO':
                $trz[1] = "../trazadoras/adulto_admin.php";
                break;
            case 'SEGUIMIENTO':
                $trz[1] = "../trazadoras/remediar_seguimiento.php";
                break;
            case 'CLASIFICACION':
                $trz[1] = "../trazadoras/remediar_carga.php";
                break;
            case 'TAL':
                $trz[1] = "../trazadoras/TAL_admin.php";
                break;
            case 'INMU':
                $trz[1] = "../inmunizacion/datos_complementarios.php";
                break;
            case 'PRE-QUIRURGICO':
                $trz[1] = "../trazadoras/malformaciones_admin.php";
                break;
            case 'PRE-QUIRURGICO-NC':
                $trz[1] = "../trazadoras/malformacionesnc_admin.php";
                break;
            case 'PREMATUREZ':
                $trz[1] = "../trazadoras/prematurez_admin.php";
                break;
            case 'CATASTROFICOEMB':
                $trz[1] = "../trazadoras/catastroficoemb_admin.php";
                break;
            case 'EMBARAZO-ALTO-RIESGO':
                $trz[1] = "../trazadoras/nocatastroficoemb_admin.php";
                break;
            case 'TALLER':
                $trz[1] = "../facturacion/seleccionBeneficiariosTaller.php";
                break;
            default:
                break;
        }
    }
    return $trz;
}

function buscarValoracionesQuirurjicas($id_nomenclador, $grupo_etario) {

    $sql = "SELECT * FROM NOMENCLADOR.VALORACIONES_INTERNACION
            WHERE id_nomenclador='$id_nomenclador'
            AND grupo_etario='$grupo_etario'
            ORDER BY id_concepto ASC";

    $result = sql($sql);
    return $result;
}

function sePuedeAgregarComprobante($cuie, $id_comprobante, $idperiodo, $fecha_comprobante, $clavebeneficiario, & $cantidadprestacion) {
    $sepuede[0] = true;

    do {
        $prestaciones = prestacionesEnComprobante($id_comprobante);
        $cantidad = cantidadDePrestaciones($id_comprobante);
        if ($prestaciones->recordcount() == 0) {
            $sepuede[0] = false;
            $sepuede[1] = "No hay practicas para facturar";
            break;
        }

        if (!excepcionRondas($prestaciones)) {
            $sepuede['beneficiario'] = datosAfiliadoEnUad('95614');
            break;
        }

        $periododelcomprobante = buscarPeriodo(Fecha_db($fecha_comprobante));
        $periododelcomprobante = $periododelcomprobante['id'];
        $periodo_buscado = $periododelcomprobante;
        do {
            $beneficiario = afiliadoEnPadronPorID($periodo_buscado, $clavebeneficiario);
            $periodo_buscado++;
        } while ($beneficiario['id'] == 0 && $beneficiario ['tipoperiodo'] == 'H' && !pasaron2meses($periodo_buscado, $periododelcomprobante));

        if ($beneficiario['activo'] == 'S') {
            if (($idperiodo == '0') || ($beneficiario['idperiodo'] != $idperiodo)) {
                actualizaPeriodoPadronDeComprobante($id_comprobante, $beneficiario['idperiodo']);
            }
            $grupo_etario = calcularGrupoEtareo($beneficiario['afifechanac'], $fecha_comprobante);
            $sepuede['beneficiario'] = $beneficiario;
        } else {
            if ($beneficiario['msj_error'] != '' && !is_null($beneficiario['msj_error'])) {
                $sepuede[0] = false;
                $sepuede[1] = $beneficiario['msj_error'];
                break;
            } else {
                $estado_uad = estadoBeneficiarioUAD($clavebeneficiario);
                $sepuede[0] = false;
                $sepuede[1] = "Beneficiario no esta activo en el Plan Sumar [$estado_uad]";
                break;
            }
//if $beneficiario['activo'] == 'S' || pasaron2meses($periododelcomprobante, $periodo_buscado)
//evitarRefacturarComprobante($id_comprobante);
//            $sepuede[0] = false;
//            $sepuede[1] = "Beneficiario no esta activo en el plan";
//            break;
        }

        if (!controlFechaAltaPerinatal($prestaciones)) {
            $sepuede[0] = false;
            $sepuede[1] = "No se especifico una Fecha de Alta";
            break;
        }

        $exluidos = controlExcluidos($cuie, $prestaciones, $grupo_etario['categoria']);
        if ($exluidos['debito']) {
            $sepuede[0] = false;
            $codigo = $prestaciones->fields['codigo'] . ' ' . $prestaciones->fields['diagnostico'];
            $sepuede[1] = 'No puede Facturar este codigo [Practica Excluida ' . $codigo . ']';
            //$sepuede[1] = "No puede facturar este codigo [Practica Excluida]";
            break;
        }

        if (!controlEmbarazadas($beneficiario, $prestaciones, $periododelcomprobante, $fecha_comprobante)) {
//if (evitarRefacturarComprobante($id_comprobante)){
//sepuede falso
//un mensaje no esta activo en el plan sumar como embarazada}else{;
            $sepuede[0] = false;
            $sepuede[1] = "No puede facturar este codigo [Solo Embarazadas]";
            break;
        }

        if (($grupo_etario['edad'] >= 6) && (!beneficiarioEmbarazadoUAD($beneficiario['clavebeneficiario'], $fecha_comprobante))) {
            if (!aptoPoblacionNueva($cuie, $fecha_comprobante)) {
                $sepuede[0] = false;
                $sepuede[1] = 'Poblacion de SUMAR no habilitada para el efector [' . $grupo_etario ['descripcion'] . "]";
                break;
            }
        }

//        $ctrl_relacionadas = practicasRelacionadas($clavebeneficiario, $fecha_comprobante, $datosnomenclador);
//        if ($ctrl_relacionadas['debito']) {
//            $sepuede[0] = false;
//            $sepuede[1] = $ctrl_relacionadas['msj_error'];
//            break;
//        }
    } while (false);
    $cantidadprestacion = $cantidad;
    return $sepuede;
}

function actualizaPeriodoPadronDeComprobante($id_comprobante, $idperiodo) {
    $sql = "UPDATE facturacion.comprobante
            SET idperiodo='$idperiodo'
            WHERE id_comprobante='$id_comprobante'";
    sql($sql);
}

function aptoPoblacionNueva($cuie, $fecha_comprobante) {
    $puede = true;

    $sql = "SELECT fechapobnueva from nacer.conv_nom cn
                inner join nacer.efe_conv ec using (id_efe_conv)
                WHERE cuie='$cuie'
                AND cn.activo=TRUE 
                AND ec.activo=TRUE";

    $result = sql($sql);
    $fechapobnueva = Fecha_db($result->fields['fechapobnueva']);

    if (($fechapobnueva == null) || (($fechapobnueva != null) && (strtotime($fechapobnueva) > strtotime($fecha_comprobante)))) {
        $puede = false;
    }

    return $puede;
}

function controlEmbarazadas($beneficiario, $prestaciones, $periodo_comprobante, $fecha_comprobante) {

//controla q realmente sean embarazadas actuales
    $puede = true;
    $prestaciones->movefirst();
    while (!$prestaciones->EOF) {
        if (practicaSoloParaEmbarazadas($prestaciones->fields['id_nomenclador'])) {
            if (!beneficiarioEmbarazadoSMI($beneficiario['clavebeneficiario'], $periodo_comprobante, $fecha_comprobante)) {
                $puede = false;
                break;
            }
        }
        $prestaciones->movenext();
    }
    return $puede;
}

function pasaron2meses($periodo_padron, $periodo_comprobante) {
    $paso = false;
    $resta = $periodo_padron - $periodo_comprobante;
    if ($resta > 2) {
        $paso = true;
    }
    return $paso;
}

function evitarRefacturarComprobante($id_comprobante) {
    $sql = "Select id_comprobante,fecha_comprobante,idperiodo from facturacion.comprobante
            WHERE id_comprobante='$id_comprobante'";
    $result = sql($sql);

    if (pasaron2meses($result->fields['fecha_comprobante'], $result->fields['idperiodo'])) {
        $sql = "UPDATE facturacion.comprobante
                SET estado='xxx'
                WHERE id_comprobante='$id_comprobante'";
    }
}

function controlExcluidos($cuie, $prestaciones, $etario) {
    $ctrl['debito'] = false;
    $prestaciones->movefirst();
    while (!$prestaciones->EOF) {
        $sql = "SELECT id_excluidos FROM nacer.excluidos
                inner join nacer.conv_nom cn using (id_conv_nom)
                inner join nacer.efe_conv ec using (id_efe_conv)
                WHERE cuie='$cuie'
                AND cn.activo=TRUE 
                AND ec.activo=TRUE
                AND cod_practica=" . $prestaciones->fields['id_nomenclador'] . "
                AND $etario=TRUE";

        $result = sql($sql);
        if ($result->recordcount() > 0) {
            $ctrl['debito'] = true;
            $codigo = $prestaciones->fields['codigo'] . ' ' . $prestaciones->fields['diagnostico'];
            $ctrl['msj_error'] = 'No puede Facturar este codigo [Practica Excluida ' . $codigo . ']';
            $ctrl['id_error'] = 61;
            break;
        }
        $prestaciones->movenext();
    }
    return $ctrl;
}

function excepcionRondas($prestaciones) {
    $secontrola = TRUE;
    $prestaciones->movefirst();
    while (!$prestaciones->EOF) {
        $codigo = explode(" ", trim($prestaciones->fields['codigo']));
        $codigo[2] = $prestaciones->fields['diagnostico'];
        if (trim($codigo[0]) == "RO" and trim($codigo[1]) == 'X001' and trim($codigo[2]) == 'A98') {
            $secontrola = FALSE;
        }
        $prestaciones->movenext();
    }
    return $secontrola;
}

function prestacionesEnComprobante($id_comprobante) {
    $sql = " select * from facturacion.prestacion 
                        inner join facturacion.nomenclador using (id_nomenclador)
			where id_comprobante=$id_comprobante";
    $cant_prestaciones = sql($sql, "no se puede traer la contidad de prestaciones") or die();
    return $cant_prestaciones;
}

function cantidadDePrestaciones($id_comprobante) {
    $sql = "select sum(p.cantidad) cantidad from facturacion.prestacion p
                        inner join facturacion.nomenclador using (id_nomenclador)
			where id_comprobante=$id_comprobante";
    $cant_prestaciones = sql($sql, "no se puede traer la contidad de prestaciones") or die();
    if ($cant_prestaciones->fields['cantidad']) {
        $cantidad = $cant_prestaciones->fields['cantidad'];
    } else {
        $cantidad = 0;
    }
    return $cantidad;
}

function descripcionDeDiagnostico($codigo, $diagnostico, $grupo_etario) {
    $sql = "SELECT d.descripcion prestacion,p.descripcion diagnostico
            FROM nomenclador.descripciones d
            LEFT JOIN nomenclador.patologias p ON trim(p.codigo) = trim(d.diagnostico)
            WHERE d.codigo='$codigo'
            AND d.diagnostico='$diagnostico'
            AND d.grupo_etareo='$grupo_etario'";
    $result = sql($sql);
    $descripcion = trim($result->fields['prestacion']) . ' [' . trim($result->fields['diagnostico']) . ']';
    return $descripcion;
}

function tiposDePracticaPorEfector($cuie_elegido) {
    $sql_tipos = "select nom_basico,nom_cc_catastrofico,nom_perinatal_catastrofico,nom_perinatal_nocatastrofico,
                    nom_remediar,nom_cc_nocatastrofico,nom_basico_2,nom_rondas
                    from nacer.conv_nom cn
                  inner join nacer.efe_conv ec using (id_efe_conv)
                  where cuie='$cuie_elegido'
                  AND cn.activo='t'
                  AND ec.activo='t'";
    $res_tipos = sql($sql_tipos) or fin_pagina();
    return $res_tipos;
}

function coberturaBasica($cuie, $codigo, $diagnostico, $fecha_prestacion, $grupo_etareo, $clavebeneficiario) {
    $sql_vale_cobertura = "SELECT id_descripcion FROM nomenclador.descripciones
                            where diagnostico='$diagnostico'
                            AND grupo_etareo='$grupo_etareo'
                            AND codigo='$codigo'
                            AND ceb=TRUE";
    $res = sql($sql_vale_cobertura);
    if ($res->recordcount() > 0) {
        $codigo_concatenado = $codigo . " " . $diagnostico;

        $sql_insert = "INSERT INTO facturacion.cobertura_basica(cuie,cod_prestacion,clavebeneficiario,fecha_prestacion)
                        values('$cuie','$codigo_concatenado','$clavebeneficiario','$fecha_prestacion')";
        $existe_cobertura = sql($sql_insert);
    }
}

function controlFechaAltaPerinatal($prestaciones) {
    $sepuede = false;
    $prestaciones->movefirst();
    if (!$prestaciones->EOF) {
        $sql = "SELECT tipo_nomenclador FROM facturacion.nomenclador
            WHERE id_nomenclador=" . $prestaciones->fields['id_nomenclador'];
        $result = sql($sql);
        if ($result->fields['tipo_nomenclador'] == "PERINATAL_CATASTROFICO") {
            $sql = "SELECT * FROM facturacion.fecha_de_alta
            WHERE id_comprobante=" . $prestaciones->fields['id_comprobante'];
            $result = sql($sql);
            if (!$result->EOF) {
                $sepuede = true;
            }
        } else {
            $sepuede = true;
        }
    }
    return $sepuede;
}

function guardarAlta($fecha_alta, $id_comprobante, $id_prestacion, $usuario) {
    if ($fecha_alta) {
        $sql = "SELECT * FROM facturacion.fecha_de_alta
            WHERE id_comprobante=$id_comprobante
            AND id_prestacion=$id_prestacion";
        $result = sql($sql);

        $fecha_carga = date("Y-m-d H:i:s");

        if ($result->recordcount() > 0) {
            $sql = "UPDATE facturacion.fecha_de_alta
            SET fecha_alta='$fecha_alta', fecha_carga='$fecha_carga',
                usuario=$usuario";
        } else {
            $sql = "INSERT INTO facturacion.fecha_de_alta (fecha_alta,id_comprobante,id_prestacion,fecha_carga,usuario)
            values('$fecha_alta',$id_comprobante, $id_prestacion,'$fecha_carga',$usuario)";
        }
        sql($sql);
    }
}

function mandarMailSoloPerinatal($id_prestacion) {
    $exito = false;

    $sql = "Select n.tipo_nomenclador,ec.cuie,ec.nombre nombrefector,u.login,u.id_usuario,
          b.apellido_benef,b.nombre_benef,b.clave_beneficiario,b.numero_doc,b.tipo_documento,
          c.fecha_comprobante,c.fecha_carga,n.codigo,n.diagnostico
          FROM facturacion.prestacion p
          INNER JOIN facturacion.comprobante c using (id_comprobante)
          INNER JOIN facturacion.nomenclador n using (id_nomenclador)
          INNER JOIN uad.beneficiarios b on (b.clave_beneficiario=c.clavebeneficiario)
          INNER JOIN nacer.efe_conv ec using (cuie)
          INNER JOIN sistema.usuarios u on (c.usuario=u.id_usuario)
          WHERE id_prestacion='$id_prestacion'";
    $result = sql($sql);

    if (($result->fields['tipo_nomenclador'] == 'PERINATAL_CATASTROFICO' ) || ($result->fields['tipo_nomenclador'] == 'PERINATAL_NO_CATASTROFICO' )) {
        if ($result->fields['tipo_nomenclador'] == 'PERINATAL_CATASTROFICO') {
            $tiponomenclador = 'Perinatal Catastrofico';
        } else {
            $tiponomenclador = 'Perinatal No Catastrofico';
        }

        $cuie = $result->fields['cuie'];
        $nombreefector = $result->fields['nombrefector'];


        $usuario = $result->fields['id_usuario'];
        $usuario_login = $result->fields['login'];

        $beneficiario_nya = $result->fields['nombre_benef'] . " " . $result->fields['apellido_benef'];
        $clavebeneficiario = $result->fields['clave_beneficiario'];
        $documento_beneficiario = "<b>" . $result->fields['tipo_documento'] . ":</b> " . $result->fields['numero_doc'];
        $fecha_carga = $result->fields['fecha_carga'];

        $codigo_nomenclador = $result->fields['codigo'] . " " . $result->fields['diagnostico'];
        $fecha_comprobante = substr($result->fields['fecha_comprobante'], 0, 10);

        $beneficiarioenpadron = afiliadoEnPadronPorFechaPrestacion($fecha_comprobante, $clavebeneficiario);
        $periodo = buscarPeriodoPorId($beneficiarioenpadron['idperiodo']);

        $para = "e.c.nadir9@gmail.com";
        //$paracc = "bpetrella@hotmail.com";
        $asunto = "Prestacion $codigo_nomenclador de $tiponomenclador  en " . $cuie;
        $contenido = "<b>Datos del Prestador:</b> $cuie - $nombreefector";
        $contenido .= "<BR>";
        $contenido .= "<BR>" . "<b>Codigo de la Prestacion:</b> $codigo_nomenclador" . "         <b>Fecha de la prestacion:</b> $fecha_comprobante";
        $contenido .= "<BR>";
        $contenido .= "<BR>" . "<b>Nombre del Beneficiario:</b> $beneficiario_nya      ";
        $contenido .= "$documento_beneficiario";
        $contenido .= "<BR>";
        $contenido .= "<BR>" . "<b>Datos en el padron SUMAR periodo " . $periodo['periodo'] . ":</b>";

        if ($beneficiarioenpadron['afidni']) {
            if ($beneficiarioenpadron['activo'] == 'S') {
                $contenido .= "<BR>";

                if ($beneficiarioenpadron['embarazadaactual'] == 'S') {

                    $contenido .= "      <b>Estado:</b> Activo         <b>Embarazada:</b> Si";
                    $contenido .= "<BR>";
                    $contenido .= "      <b>Diagnostico:</b>" . $beneficiarioenpadron ['diagnostico'] . "   <b>   SG:</b> " . $beneficiarioenpadron ['semanas_gestacion'] . "   <b>   FUM:</b> " . $beneficiarioenpadron['fum'];
                    $contenido .= "<BR>";
                    $contenido .= "      <b>Fecha Probable de Parto:</b>" . $beneficiarioenpadron['parto'];
                } else {//no esta embarazada
                    $contenido .= "      <b>Estado:</b> Activo         <b>Embarazada:</b> No";
                }
            } else {//no esta activo
                $contenido .= "<BR>";
                $contenido .= "        <b>Estado:</b> Pasivo     Motivo: " . $beneficiarioenpadron['msj_error'];
            }
        } else {//no esta inscripto
            $contenido .= "<BR>" . "   <b>Estado:</b>  No esta inscripto";
        }

        $contenido .="<BR><BR>";
        $contenido .="<b>Datos de Carga: Usuario:</b> $usuario-$usuario_login      <b>Fecha:</b>    $fecha_carga ";
        $exito = enviar_mail($para, null, null, $asunto, $contenido, null, null, '0');
    } return $exito;
}

function guardarPrestacion($id_comprobante, $id_nomenclador, $cantidad, $precio) {

    $consulta = "INSERT INTO facturacion.prestacion (id_comprobante, id_nomenclador,cantidad,precio_prestacion)
                    VALUES ('$id_comprobante','$id_nomenclador','$cantidad','$precio') returning id_prestacion";
    $id_prestacion = sql($consulta) or
            fin_pagina();
    return $id_prestacion->fields['id_prestacion'];
}

function actualizarPrestacion($id_comprobante, $importe_total) {
    $consulta = "UPDATE facturacion.prestacion
                    SET precio_prestacion='$importe_total'
                    WHERE id_comprobante='

$id_comprobante'";
    sql($consulta);
}

function clasificacionPorClaveBeneficiario($clavebeneficiario) {
    $sql = "select id_clasificacion from trazadoras.clasificacion_remediar
            where clave='$clavebeneficiario'
            union
            select id_clasificacion from trazadoras.clasificacion_remediar2
            where clave_beneficiario='$clavebeneficiario'";
    $result = sql($sql);

    if ($result->recordcount() > 0) {

        return true;
    } else {
        return false;
    }
}

function buscarContrato($cuie) {
    $sql = "SELECT contrato from nacer.efe_conv
        WHERE cuie='$cuie'";
    $result = sql($sql);
    return $result->fields['contrato'];
}

function buscarCuenta($cuie) {
    $sql = "SELECT nombre,nrocta from facturacion.smiefectores s
            inner join general.bancos b on s.banco = b.idbanco
            WHERE cuie='$cuie

'";
    $result = sql($sql);
    return $result;
}

function proxNumeroFactura($cuie) {
    $sql = "select max(cast(nro_fact_offline as integer))+1 numero_factura 
            from facturacion.factura
            where cuie='$cuie'";
    $result = sql($sql);
    $result = $result->fields['numero_factura'];
//if (strlen($result) < 9) {
//    while (strlen($result) < 8) {
//       $result = '0' . $result;
//  }
// $result = '1' . $result;
//}

    return str_pad($result, 9, '0', STR_PAD_LEFT);
}

function unicafacturadelperiodo($cuie, $periodo, $tipo_nomenclador) {
    $unica = true;
    $sql = "select id_factura 
            from facturacion.factura
            where cuie='$cuie' 
            and periodo='$periodo' 
            and periodo_actual='$periodo'
            and tipo_nomenclador='$tipo_nomenclador'";
    $result = sql($sql);

    if ($result->recordcount() > 0) {
        $unica = false;
    }
    return $unica;
}

function practicaSoloParaEmbarazadas($id_nomenclador) {

    $sql = "SELECT * FROM facturacion.nomenclador where id_nomenclador='$id_nomenclador'
            and neo=0
            and cero_a_uno=0
            and uno_a_seis=0
            and seis_a_diez=0
            and diez_a_veinte=0
            and veinte_a_sesentaycuatro=0
            and embarazada > 0";
    $resultado = sql($sql);
    if (!$resultado->EOF) {
        return true;
    } else {
        return false;
    }
}

?>
