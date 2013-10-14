<?php

define("NRO_FACTURA_MISIONES", "(case when facturacion.factura.nro_fact_offline <> ''
  then cast(facturacion.factura.nro_fact_offline as text)
  else cast(facturacion.factura.id_factura as text) end) as numero_factura, ");

require_once ("../../modulos/facturacion/funciones.php");
require_once ("../../lib/bibliotecaTraeme.php");

function nro_factura_misiones() {
    $func_nroFactura = false;
    $queryfunciones = "SELECT accion,nombre
			FROM sistema.funciones
                        WHERE habilitado='s' 
                        AND nombre='Nro Factura' 
                        AND pagina='facturacion'";
    $res_fun = sql($queryfunciones) or fin_pagina();
    if ($res_fun->recordCount() > 0)
        $func_nroFactura = true;
    return $func_nroFactura;
}

function ultimoDia($mes, $ano) {
    $ultimo_dia = 28;
    while (checkdate($mes, $ultimo_dia + 1, $ano)) {
        $ultimo_dia++;
    }
    return $ultimo_dia;
}

function getNombreMes($mes) {
    $d = (int) $mes;
    switch ($d) {
        case "1":
            $nombre = "Enero";
            break;
        case "2":
            $nombre = "Febrero";
            break;
        case "3":
            $nombre = "Marzo";
            break;
        case "4":
            $nombre = "Abril";
            break;
        case "5":
            $nombre = "Mayo";
            break;
        case "6":
            $nombre = "Junio";
            break;
        case "7":
            $nombre = "Julio";
            break;
        case "8":
            $nombre = "Agosto";
            break;
        case "9":
            $nombre = "Septiembre";
            break;
        case "10":
            $nombre = "Octubre";
            break;
        case "11":
            $nombre = "Noviembre";
            break;
        case "12":
            $nombre = "Diciembre";
            break;
    }
    return $nombre;
}

function fn_dato_convenio($cuie) {
    $queryfunciones = "SELECT CASE WHEN t1.padre IS NOT NULL OR t1.padre<>'' THEN t2.id_efe_conv ELSE t1.id_efe_conv END as id_efe_conv,
                CASE WHEN t1.padre IS NOT NULL OR t1.padre<>'' THEN t2.fecha_comp_ges ELSE t1.fecha_comp_ges END AS fecha_comp_ges,
                CASE WHEN t1.padre IS NOT NULL OR t1.padre<>'' then t2.fecha_fin_comp_ges ELSE t1.fecha_fin_comp_ges END AS fecha_fin_comp_ges
		FROM nacer.efe_conv AS t1
		left join nacer.efe_conv AS t2 ON  t2.cuie=t1.padre
		WHERE t1.cuie='$cuie' 
                AND CASE WHEN t1.padre IS NOT NULL OR t1.padre<>'' THEN t2.fecha_comp_ges ELSE t1.fecha_comp_ges END IS NOT NULL
		AND CASE WHEN t1.padre IS NOT NULL OR t1.padre<>'' THEN t2.fecha_fin_comp_ges ELSE t1.fecha_fin_comp_ges END IS NOT NULL
                AND CASE WHEN t1.padre IS NOT NULL OR t1.padre<>'' THEN t2.activo ELSE t1.activo END IS TRUE";
    $res_fun = sql($queryfunciones) or fin_pagina();
    $valor['fecha_comp_ges'] = $res_fun->fields['fecha_comp_ges'];
    $valor['fecha_fin_comp_ges'] = $res_fun->fields['fecha_fin_comp_ges'];
    // $x=fopen("archivos2.txt","w");   $as=$cuie.'**'.$res_fun->fields['fecha_comp_ges'].'**'.$res_fun->fields['fecha_fin_comp_ges'];   if($x)    {     fwrite($x,$as);    }
    return $valor;
}

function limite_trazadora($fecha) {
    /* Para saber en que cuatrimestre estamos */
    $fecha = strtotime(fecha_db($fecha));
    $actualm = date('m', $fecha);
    $actualy = $actualy2 = date('Y', $fecha);
    // $ano = date('Y', $fecha);
    $desdem1 = '01';
    $hastam1 = '04';
    $desdem2 = '05';
    $hastam2 = '08';
    $desdem3 = '09';
    $hastam3 = '12';
    if ($actualm >= $desdem1 && $actualm <= $hastam1) {
        $cuatrimestre = 1;
        $cuatrimestrem = 3;
        //$actualy2++;
    }
    if ($actualm >= $desdem2 && $actualm <= $hastam2) {
        $cuatrimestre = 2;
        $cuatrimestrem = $cuatrimestre - 1;
        // $actualy2++;
    }
    if ($actualm >= $desdem3 && $actualm <= $hastam3) {
        $cuatrimestre = 3;
        $cuatrimestrem = $cuatrimestre - 1;
        $actualy2++;
    }

    $query2 = "SELECT desde
					FROM facturacion.cuatrimestres
					 WHERE cuatrimestre='$cuatrimestre'";
    $res_sql2 = sql($query2) or excepcion("Error al buscar cuatrimestre");
    $valor['desde'] = $actualy . '-' . $res_sql2->fields['desde'];

    $query2 = "SELECT limite
					FROM facturacion.cuatrimestres
					 WHERE cuatrimestre='$cuatrimestre'";
    $res_sql2 = sql($query2) or excepcion("Error al buscar cuatrimestre");

    $valor['limite'] = $actualy2 . '-' . $res_sql2->fields['limite'];

    return $valor;
}

function provincia_uso() {
    $queryfunciones = "SELECT UPPER(accion)AS accion,nombre
			FROM sistema.funciones
                        WHERE habilitado='s'   
                        AND nombre='Provincia'";
    $res_fun = sql($queryfunciones) or fin_pagina();
    if ($res_fun->recordCount() > 0)
        $prov_uso = $res_fun->fields['accion'];
    return $prov_uso;
}

function obtenerComprobante($l, $var, $idperiodo, $tipo_nomenclador) {
    switch ($tipo_nomenclador) {
        case '01':
            $tipo_nomenclador = 'BASICO';
            break;
        default:
            break;
    }
    $comprobante["cuie"] = $l[1];
    $comprobante["id_factura"] = $var['id_factura']; //obtener el id luego de guardar la factura
    $comprobante["nombre_medico"] = $l[55];
    $comprobante["fecha_comprobante"] = date("d/m/Y", strtotime(str_replace('/', '-', $l[13])));
    $comprobante["clave_beneficiario"] = $l[5];
    $aux = $var['beneficiario']; //obtenerIdSmiafiliado($l[5], $l[6], $l[7], $l[8], $var, $l);
    $comprobante["id_smiafiliado"] = $aux['id'];
    $comprobante["fecha_carga"] = date("d/m/Y");
    $comprobante["periodo"] = $l[2];
    $comprobante["id_periodo"] = $idperiodo;
    $comprobante["id_servicio"] = 1;
    $comprobante["activo"] = 'S';
    $comprobante["idvacuna"] = $var['idvacuna'];
    $comprobante["tipo_nomenclador"] = $tipo_nomenclador;
    return $comprobante;
}

function obtenerPrestacion(&$l, &$var, $precio, $nomenclador) {
    $prestacion["id_comprobante"] = $var['id_comprobante'];
    if (is_null($nomenclador)) {
        $prestacion["id_nomenclador"] = 0;
    } else {
        $prestacion["id_nomenclador"] = $nomenclador->getIdNomenclador();
    }

    $prestacion["cantidad"] = $var['cantidad'];
    $prestacion["precio_prestacion"] = $precio;
//    if (is_null($var['id_nomenclador'][5])) {
//        $prestacion["id_anexo"] = $var['id_nomenclador'][5];
//    } else {
    $prestacion["id_anexo"] = 0;
//    }
    $prestacion["peso"] = 0;
    $prestacion["tension_arterial"] = 0;
    $prestacion["prestacionid"] = $l[4];
    return $prestacion;
}

function controlMaximosPeriodicidad($clavebeneficiario, $fecha_comprobante, $idcomprobante, $nomenclador, $idpadron) {

    $ctrl['debito'] = false;
    //asigno variables para usar la validacion
    // $fecha_comprobante = Fecha_db($fecha_comprobante);
    //traigo el codigo de nomenclador y si hay validaciones traigo los datos de la validacion
    $query = "SELECT * 
                FROM facturacion.validacion_prestacion_mns
		WHERE trim(codnomenclador)=trim('$nomenclador[3]')";
    $res = sql($query, "Error 1") or fin_pagina();

    if ($res->RecordCount() > 0) {
        $periodicidad = $res->fields['periodicidad'];
        $tipoperiodicidad = trim($res->fields['tipope']);

        $maxprovincial = $res->fields['maxprovincial'];
        $tipoprovincial = $res->fields['tipopr'];


        switch (trim($tipoperiodicidad)) {
            case 'm':
                $condicion_periodicidad = " AND c.fecha_comprobante BETWEEN (to_date('$fecha_comprobante' , 'DD-MM-YYYY') - interval '$periodicidad month') 
                AND to_date('$fecha_comprobante' , 'DD-MM-YYYY')";
                break;
            case 'mc':
                $periodicidadmenosuno = $periodicidad - 1;
                $condicion_periodicidad = "AND date_part('month',fecha_comprobante) BETWEEN date_part('month',to_date('$fecha_comprobante','DD-MM-YYYY')- interval '$periodicidadmenosuno month')and date_part('month',to_date('$fecha_comprobante','DD-MM-YYYY'))
		AND date_part('year',fecha_comprobante) BETWEEN date_part('year',to_date('$fecha_comprobante','DD-MM-YYYY')- interval '$periodicidadmenosuno month')and date_part('year',to_date('$fecha_comprobante','DD-MM-YYYY'))";
                break;
            case 'v':
                $condicion_periodicidad = "AND fecha_comprobante<to_date('$fecha_comprobante' , 'DD-MM-YYYY')";
                break;
            case 'ac':
                $condicion_periodicidad = "AND DATE_PART('year',fecha_comprobante)=DATE_PART('year',to_date('$fecha_comprobante' , 'DD-MM-YYYY'))
                                            AND fecha_comprobante<to_date('$fecha_comprobante' , 'DD-MM-YYYY')";
                break;
            default:
                $condicion_periodicidad = "";
                break;
        }

        //cuenta la cantidad de prestaciones de un determinado filiado, de un determinado codigo y 
        //en un periodo de tiempo parametrizado.    


        $query = "SELECT p.id_prestacion, codigo, fecha_comprobante,nro_exp,d.id_debito,c.id_comprobante
  			FROM facturacion.comprobante c
                        LEFT JOIN facturacion.debito d ON (d.id_comprobante=c.id_comprobante)
  			INNER JOIN facturacion.prestacion p ON (c.id_comprobante = p.id_comprobante)
  			INNER JOIN facturacion.nomenclador ON (p.id_nomenclador = facturacion.nomenclador.id_nomenclador)
                        INNER JOIN facturacion.factura f ON (f.id_factura=c.id_factura)
			INNER JOIN facturacion.recepcion r ON (r.idrecepcion=f.recepcion_id)
  			WHERE c.clavebeneficiario='" . $clavebeneficiario . "'
                        AND trim(codigo)=trim('" . $nomenclador[3] . "')
                        $condicion_periodicidad
                        AND c.marca='0'
                        AND c.id_comprobante<>'$idcomprobante'
                        ORDER BY fecha_comprobante DESC";

        $cant_pres = sql($query, "Error al controlar periodicidad") or fin_pagina();

        $yadebitado = $cant_pres->fields['id_debito'];
        $recibidodespues = $resultado->fields['id_comprobante'] < $idcomprobante;
        if (($cant_pres->RecordCount() >= 1) && !($yadebitado)) {
            $ctrl['debito'] = true;
            $expediente = $cant_pres->fields['nro_exp'];
            $ctrl['msj_error'] = 'Prestacion no cumple con la periodicidad del control. Superado en Expediente:"' . $expediente . '"';
            $ctrl['id_error'] = 65;
            return $ctrl;
        }

        //Paso el control de periodicidad, vamos por el de maximos
        switch (trim($tipoprovincial)) {
            case 'm':
                $condicion_maximos = " AND c.fecha_comprobante BETWEEN to_date('$fecha_comprobante' , 'DD-MM-YYYY')- interval '1 month' 
                AND to_date('$fecha_comprobante' , 'DD-MM-YYYY')";
                break;
            case 'a':
                $condicion_maximos = " AND c.fecha_comprobante BETWEEN DATE((DATE_PART('year',to_date('$fecha_comprobante' , 'DD-MM-YYYY')))||'-'||
                01||'-'||01) AND to_date('$fecha_comprobante' , 'DD-MM-YYYY')
                AND fecha_comprobante<to_date('$fecha_comprobante' , 'DD-MM-YYYY')";
                break;
            case 'v':
                $condicion_maximos = "AND fecha_comprobante<to_date('$fecha_comprobante' , 'DD-MM-YYYY')";
                break;
            case 'e':
                $fechas = afiliadoEnPadronPorID($idpadron, $clavebeneficiario); //($fecha, $clavebeneficiario, $tipofacturacion); //buscarFechaDeEmbarazo($clavebeneficiario, $fecha_comprobante);
                $condicion_maximos = " AND c.fecha_comprobante BETWEEN CAST('" . $fechas['diagnostico'] . "' AS DATE) AND CAST('" . $fechas['parto'] . "' AS DATE)
                                        AND fecha_comprobante<to_date('$fecha_comprobante' , 'DD-MM-YYYY')";
                break;
            default:
                $condicion_maximos = "";
                break;
        }


        $query = "SELECT p.id_prestacion, codigo, fecha_comprobante,nro_exp,d.id_debito,c.id_comprobante
                        FROM facturacion.comprobante c
  			INNER JOIN facturacion.prestacion p ON (c.id_comprobante = p.id_comprobante)
                        LEFT JOIN facturacion.debito d ON (d.id_comprobante=c.id_comprobante)
  			INNER JOIN facturacion.nomenclador ON (p.id_nomenclador = facturacion.nomenclador.id_nomenclador)
                        INNER JOIN facturacion.factura f ON (f.id_factura=c.id_factura)
			INNER JOIN facturacion.recepcion r ON (r.idrecepcion=f.recepcion_id)
  			WHERE c.clavebeneficiario='" . $clavebeneficiario . "'
                        AND trim(codigo)=trim('" . $nomenclador[3] . "')
                        $condicion_maximos
                        AND c.marca='0'
                        AND c.id_comprobante<>'$idcomprobante'
                        ORDER BY fecha_comprobante DESC";

        $cant_pres = sql($query, "Error al controlar maximo provincial") or fin_pagina();
        $yadebitado = $cant_pres->fields['id_debito'];
        $recibidodespues = $resultado->fields['id_comprobante'] < $idcomprobante;
        if (($cant_pres->RecordCount() >= $maxprovincial) && !($yadebitado) && ($recibidodespues)) {
            $expediente = $cant_pres->fields['nro_exp'];
            $ctrl['debito'] = true;
            $ctrl['msj_error'] = 'Prestacion excede el maximo provincial. Superado en Expediente:"' . $expediente . '"';
            $ctrl['id_error'] = 71;
        }

        return $ctrl;
    }
}

function afiliadoEnPadronPorFecha($fechapractica, $clavebenefi) {
    $fechapracticasinformado = strtotime(Fecha_db($fechapractica));
    $fechapracticarestada = date("d-m-Y", $fechapracticasinformado);
    $i = 0;
    do {
        // pasarlo a array //
        $periodo = buscarPeriodo($fechapracticarestada);
        if ($periodo['tipo'] == "V") {
            $control_afi = afiliadoEnVigente($clavebenefi);
            $i = 2; //Condicion de salida
        } elseif ($periodo['tipo'] == "H") {
            $control_afi = afiliadoEnHistorial($periodo['periodo'], $clavebenefi);
        }

        if ($control_afi['id'] == '0' && $control_afi['motivobaja'] != '') {
            if (!sigueBuscando($control_afi['motivobaja']))
                $i = 2; //Condicion de salida
        }

        $i++;
        $fechapracticarestada = date("d-m-Y", strtotime(date("d-m-Y", strtotime(Fecha_db($fechapracticarestada))) . "+1 month"));
    } while (($control_afi['id'] == '0' || $control_afi['id'] == null) && $i < 3);

    if (!$control_afi['id']) {
        $control_afi['clavebeneficiario'] = $clavebenefi;
        $control_afi['clasedoc'] = 0;
        $control_afi['tipodoc'] = 0;
        $control_afi['afidni'] = 0;
        $control_afi['id'] = 0;
        $control_afi['afiapellido'] = 0;
        $control_afi['afinombre'] = 0;
    }
    $control_afi['padron_periodo'] = $periodo['id'];
    return $control_afi;
}

function buscameConDocumentoPropioEnPadron($clavebenefi, $afidni) {
    $sql = "SELECT afidni
                FROM nacer.smiafiliadoshst 
                WHERE clavebeneficiario = '$clavebenefi'
                AND aficlasedoc = 'P'
                 AND afidni='$afidni'
            UNION
            SELECT afidni
                FROM nacer.smiafiliados 
                WHERE clavebeneficiario = '$clavebenefi'
                AND aficlasedoc = 'P'
                AND afidni='$afidni'";
    $result = sql($sql, "No se encuentra smiafiliado con clave: $clave", 0);

    if ($result->RecordCount() > 0) {
        return true;
    } else {
        return false;
    }
}

function beneficiarioEstaEnAlgunLado($clavebenefi) {
    $sql = "SELECT afidni
                FROM nacer.smiafiliadoshst 
                WHERE clavebeneficiario = '$clavebenefi'
            UNION
            SELECT afidni
                FROM nacer.smiafiliados 
                WHERE clavebeneficiario = '$clavebenefi'";
    $result = sql($sql, "No se encuentra smiafiliado con clave: $clave", 0);

    if ($result->RecordCount() > 0) {
        return true;
    } else {
        return false;
    }
}

function afiliadoEnPadronPorID($idpadron, $clavebenefi) {
    $periodo = buscarPeriodoPorId($idpadron);
    if (($periodo['tipo'] == "V") or ($periodo['tipo'] == null)) {
        $control_afi = afiliadoEnVigente($clavebenefi);
    } else {
        $control_afi = afiliadoEnHistorial($periodo['periodo'], $clavebenefi);
    }
    $control_afi['idperiodo'] = $periodo['id'];
    $control_afi['tipoperiodo'] = $periodo['tipo'];
    return $control_afi;
}

function afiliadoEnPadronPorFechaPrestacion($fecha_comprobante, $clavebenefi) {
    $periodo = buscarPeriodo(Fecha_db($fecha_comprobante));
    if (($periodo['tipo'] == "V") or ($periodo['tipo'] == null)) {
        $control_afi = afiliadoEnVigente($clavebenefi);
    } else {
        $control_afi = afiliadoEnHistorial($periodo['periodo'], $clavebenefi);
    }
    $control_afi['idperiodo'] = $periodo['id'];
    return $control_afi;
}

function afiliadoEnHistorial($elperiodo, $clavebenefi) {
    $datos_afi['id'] = '0';

    $reordenarperiodo = split('/', $elperiodo);
    $periodo = $reordenarperiodo[1] . $reordenarperiodo[0];

    $sql = "SELECT aficlasedoc,afitipodoc,afidni,id_smiafiliados,fechainscripcion,afifechanac,
                activo,afiapellido,afinombre,fechadiagnosticoembarazo,fechaprobableparto,motivobaja,
                mensajebaja,afisexo,embarazoactual,semanasembarazo
                FROM nacer.smiafiliadoshst 
                WHERE clavebeneficiario = '$clavebenefi' 
                AND periodo='$periodo'";
    $result = sql($sql, "No se encuentra smiafiliado con clave: $clave", 0);
    if ($result->RowCount() > 0) {
        if ($result->fields['activo'] == 'S') {
            $datos_afi['afiapellido'] = $result->fields['afiapellido'];
            $datos_afi['afinombre'] = $result->fields['afinombre'];
            $datos_afi['clasedoc'] = $result->fields['aficlasedoc'];
            $datos_afi['tipodoc'] = $result->fields['afitipodoc'];
            $datos_afi['afidni'] = $result->fields['afidni'];
            $datos_afi['id'] = $result->fields['id_smiafiliados'];
            $datos_afi['afifechanac'] = $result->fields['afifechanac'];
            $datos_afi['clavebeneficiario'] = $clavebenefi;
            $datos_afi['diagnostico'] = $result->fields['fechadiagnosticoembarazo'];
            $datos_afi['parto'] = $result->fields['fechaprobableparto'];
            $datos_afi['semanas_gestacion'] = $result->fields['semanasembarazo'];
            $datos_afi['afisexo'] = $result->fields['afisexo'];
            $datos_afi['fechainscripcion'] = $result->fields['fechainscripcion'];
            $datos_afi['embarazadaactual'] = $result->fields['embarazadaactual'];
            $datos_afi['activo'] = $result->fields['activo'];

            $datos_afi['debito'] = false;
        } else {
            $datos_afi['clavebeneficiario'] = $clavebenefi;
            $datos_afi['diagnostico'] = $result->fields['fechadiagnosticoembarazo'];
            $datos_afi['parto'] = $result->fields['fechaprobableparto'];
            $datos_afi['fechainscripcion'] = $result->fields['fechainscripcion'];
            $datos_afi['afisexo'] = $result->fields['afisexo'];
            $datos_afi['clasedoc'] = $result->fields['aficlasedoc'];
            $datos_afi['tipodoc'] = $result->fields['afitipodoc'];
            $datos_afi['afidni'] = $result->fields['afidni'];
            $datos_afi['id'] = $result->fields['id_smiafiliados'];
            $datos_afi['afiapellido'] = $result->fields['afiapellido'];
            $datos_afi['afinombre'] = $result->fields['afinombre'];
            $datos_afi['debito'] = true;
            $datos_afi['id_error'] = 80;
            $datos_afi['msj_error'] = $result->fields['mensajebaja']; //'Afiliado inactivo en el periodo ' . $periodo;
            $datos_afi['motivobaja'] = $result->fields['motivobaja'];
            $datos_afi['activo'] = $result->fields['activo'];
        }
    }
    return $datos_afi;
}

function afiliadoEnVigente($clavebenefi) {
    $sql = "SELECT aficlasedoc,afitipodoc,afidni,id_smiafiliados,fechainscripcion,afifechanac,
                activo,afiapellido,afinombre,fechadiagnosticoembarazo,fechaprobableparto,motivobaja ,manrodocumento,
                            panrodocumento,otronrodocumento,mensajebaja,afisexo,embarazoactual,fum,semanasembarazo
                            FROM nacer.smiafiliados 
                            WHERE clavebeneficiario = '$clavebenefi'";
    $result = sql($sql, "No se encuentra smiafiliado con clave: $clavebenefi", 0);
    if ($result->RowCount() > 0) {
        if ($result->fields['activo'] == 'S') {
            $control_afi['clavebeneficiario'] = $clavebenefi;
            $control_afi['clasedoc'] = $result->fields['aficlasedoc'];
            $control_afi['tipodoc'] = $result->fields['afitipodoc'];
            $control_afi['afidni'] = $result->fields['afidni'];
            $control_afi['id'] = $result->fields['id_smiafiliados'];
            $control_afi['afiapellido'] = $result->fields['afiapellido'];
            $control_afi['afinombre'] = $result->fields['afinombre'];
            $control_afi['afifechanac'] = $result->fields['afifechanac'];
            $control_afi['manrodocumento'] = $result->fields['manrodocumento'];
            $control_afi['panrodocumento'] = $result->fields['panrodocumento'];
            $control_afi['otronrodocumento'] = $result->fields['otronrodocumento'];
            $control_afi['fechainscripcion'] = $result->fields['fechainscripcion'];
            $control_afi['diagnostico'] = $result->fields['fechadiagnosticoembarazo'];
            $control_afi['parto'] = $result->fields['fechaprobableparto'];
            $control_afi['afisexo'] = $result->fields['afisexo'];
            $control_afi['embarazadaactual'] = $result->fields['embarazoactual'];
            $control_afi['semanas_gestacion'] = $result->fields['semanasembarazo'];
            $control_afi['fum'] = $result->fields['fum'];
            $control_afi['activo'] = $result->fields['activo'];
        } else {
            $control_afi['clavebeneficiario'] = $clavebenefi;
            $control_afi['afisexo'] = $result->fields['afisexo'];
            $control_afi['clasedoc'] = 0;
            $control_afi['tipodoc'] = 0;
            $control_afi['afidni'] = 0;
            $control_afi['id'] = $result->fields['id_smiafiliados'];
            $control_afi['afiapellido'] = 0;
            $control_afi['afinombre'] = 0;
            $control_afi['debito'] = true;
            $control_afi['id_error'] = 80;
            $control_afi['msj_error'] = $result->fields['mensajebaja']; //'Afiliado inactivo en el periodo vigente';
            $control_afi['motivobaja'] = $result->fields['motivobaja'];
            $control_afi['activo'] = $result->fields['activo'];
        }
    } else {
        $control_afi['clavebeneficiario'] = $clavebenefi;
        $control_afi['clasedoc'] = 0;
        $control_afi['tipodoc'] = 0;
        $control_afi['afidni'] = 0;
        $control_afi['id'] = 0;
        $control_afi['afiapellido'] = 0;
        $control_afi['afinombre'] = 0;
        $control_afi['debito'] = true;
        $control_afi['msj_error'] = "NO ESTA INSCRIPTO EN PLAN NACER";
        $control_afi['id_error'] = 18;
    }
    return $control_afi;
}

function datosAfiliadoEnVigente($clavebenefi = "", $nrodoc = "") {
    if ($clavebenefi != "") {
        $where .= " AND clavebeneficiario = '$clavebenefi' ";
    }
    if ($nrodoc != "") {
        $where .= " AND afidni = '$nrodoc' AND aficlasedoc='P' ";
    }
    $sql = "SELECT aficlasedoc,afitipodoc,afidni,clavebeneficiario,id_smiafiliados,fechainscripcion,
                   afifechanac, activo,afiapellido,afinombre,fechadiagnosticoembarazo,fechaprobableparto,
                   motivobaja ,manrodocumento, panrodocumento,otronrodocumento,mensajebaja
            FROM nacer.smiafiliados 
            WHERE 1=1 
                " . $where . "
           ";
    $result = sql($sql, "No se encuentra smiafiliado con clave: $clavebenefi", 0);
    if ($result->RowCount() > 0) {
        $control_afi['clavebeneficiario'] = $result->fields['clavebeneficiario'];
        $control_afi['clasedoc'] = $result->fields['aficlasedoc'];
        $control_afi['tipodoc'] = $result->fields['afitipodoc'];
        $control_afi['afidni'] = $result->fields['afidni'];
        $control_afi['id'] = $result->fields['id_smiafiliados'];
        $control_afi['afiapellido'] = $result->fields['afiapellido'];
        $control_afi['afinombre'] = $result->fields['afinombre'];
        $control_afi['afifechanac'] = $result->fields['afifechanac'];
        $control_afi['manrodocumento'] = $result->fields['manrodocumento'];
        $control_afi['panrodocumento'] = $result->fields['panrodocumento'];
        $control_afi['otronrodocumento'] = $result->fields['otronrodocumento'];
        $control_afi['fechainscripcion'] = $result->fields['fechainscripcion'];
        $control_afi['diagnostico'] = $result->fields['fechadiagnosticoembarazo'];
        $control_afi['parto'] = $result->fields['fechaprobableparto'];
    } else {
        $sql = "SELECT *
                            FROM uad.beneficiarios 
                            WHERE clave_beneficiario = '$clavebenefi'";
        $result = sql($sql, "No se encuentra smiafiliado con clave: $clavebenefi", 0);
        $control_afi['clavebeneficiario'] = $clavebenefi;
        $control_afi['clasedoc'] = $result->fields['clase_documento_benef'];
        $control_afi['tipodoc'] = $result->fields['tipo_documento'];
        $control_afi['afidni'] = $result->fields['numero_doc'];
        $control_afi['afiapellido'] = $result->fields['apellido_benef'];
        $control_afi['afinombre'] = $result->fields['nombre_benef'];
        $control_afi['afifechanac'] = $result->fields['fecha_nacimiento_benef'];
        $control_afi['manrodocumento'] = $result->fields['nro_doc_madre'];
        $control_afi['panrodocumento'] = $result->fields['nro_doc_padre'];
        $control_afi['otronrodocumento'] = $result->fields['nro_doc_tutor'];
        $control_afi['fechainscripcion'] = $result->fields['fecha_inscripcion'];
        $control_afi['diagnostico'] = $result->fields['fecha_diagnostico_embarazo'];
        $control_afi['parto'] = $result->fields['fecha_probable_parto'];
    }
    return $control_afi;
}

function datosAfiliadoEnSMIVigente($clavebenefi) {
    $sql = "SELECT aficlasedoc,afitipodoc,afidni,id_smiafiliados,fechainscripcion,afifechanac,
                activo,afiapellido,afinombre,fechadiagnosticoembarazo,fechaprobableparto,motivobaja ,manrodocumento,
                            panrodocumento,otronrodocumento,mensajebaja
                            FROM nacer.smiafiliados 
                            WHERE clavebeneficiario = '$clavebenefi'";
    $result = sql($sql, "No se encuentra smiafiliado con clave: $clavebenefi", 0);
    if ($result->RowCount() > 0) {
        $control_afi['clavebeneficiario'] = $clavebenefi;
        $control_afi['clasedoc'] = $result->fields['aficlasedoc'];
        $control_afi['tipodoc'] = $result->fields['afitipodoc'];
        $control_afi['afidni'] = $result->fields['afidni'];
        $control_afi['id'] = $result->fields['id_smiafiliados'];
        $control_afi['afiapellido'] = $result->fields['afiapellido'];
        $control_afi['afinombre'] = $result->fields['afinombre'];
        $control_afi['afifechanac'] = $result->fields['afifechanac'];
        $control_afi['manrodocumento'] = $result->fields['manrodocumento'];
        $control_afi['panrodocumento'] = $result->fields['panrodocumento'];
        $control_afi['otronrodocumento'] = $result->fields['otronrodocumento'];
        $control_afi['fechainscripcion'] = $result->fields['fechainscripcion'];
        $control_afi['diagnostico'] = $result->fields['fechadiagnosticoembarazo'];
        $control_afi['parto'] = $result->fields['fechaprobableparto'];
    } else {
        $control_afi['clavebeneficiario'] = $clavebenefi;
        $control_afi['clasedoc'] = 0;
        $control_afi['tipodoc'] = 0;
        $control_afi['afidni'] = 0;
        $control_afi['id'] = 0;
        $control_afi['afiapellido'] = 0;
        $control_afi['afinombre'] = 0;
        $control_afi['debito'] = true;
        $control_afi['msj_error'] = "NO ESTA INSCRIPTO EN PLAN NACER";
        $control_afi['id_error'] = 18;
    }
    return $control_afi;
}

function datosAfiliadoEnUad($dni) {
    $sql = "SELECT *    FROM uad.beneficiarios 
            WHERE numero_doc = '$dni'";

    $result = sql($sql, "No se encuentra smiafiliado con clave: $clavebenefi", 0);
    if ($result->RowCount() > 0) {
        $control_afi['clavebeneficiario'] = $result->fields['clave_beneficiario'];
        $control_afi['afidni'] = $result->fields['numero_doc'];
        $control_afi['afiapellido'] = $result->fields['apellido_benef'];
        $control_afi['afinombre'] = $result->fields['nombre_benef'];
        $control_afi['afifechanac'] = $result->fields['fecha_nacimiento_benef'];
    }
    return $control_afi;
}

function buscarPeriodo($fechapractica) {
    $fechapractica = split("-", $fechapractica);
    $fechapractica = $fechapractica[2] . "/" . $fechapractica[1];
    $periodo = null;
    $sql = "SELECT tipo,periodo,id_periodo
                            FROM facturacion.periodo 
                            WHERE periodo = '$fechapractica'";
    $result = sql($sql, "No se encuentra el periodo: $fechapractica", 0);
    if ($result->RecordCount() > 0) {
        $periodo['periodo'] = $result->fields['periodo'];
        $periodo['tipo'] = $result->fields['tipo'];
        $periodo['id'] = $result->fields['id_periodo'];
    }
    return $periodo;
}

function estadoDelAfiliado($clavebenefi) {
    $sql = "SELECT aficlasedoc,afitipodoc,afidni,id_smiafiliados,fechainscripcion,afifechanac,
                activo,afiapellido,afinombre,fechadiagnosticoembarazo,fechaprobableparto,motivobaja ,manrodocumento,
                            panrodocumento,otronrodocumento,mensajebaja
                            FROM nacer.smiafiliados 
                            WHERE clavebeneficiario = '$clavebenefi'";
    $result = sql($sql, "No se encuentra smiafiliado con clave: $clavebenefi", 0);
    if ($result->RowCount() > 0) {
        if ($result->fields['activo'] == 'S') {
            $control_afi = $result->fields['id_smiafiliados'];
        } else {
            $control_afi = 0;
        }
    } else {
        $control_afi = -1;
    }
    return $control_afi;
}

function buscarPeriodoPorId($idperiodo) {
    $periodo = null;
    if ($idperiodo) {
        $sql = "SELECT tipo,periodo,id_periodo
                            FROM facturacion.periodo 
                            WHERE id_periodo = '$idperiodo'";
    } else {
        $sql = "SELECT tipo,periodo,id_periodo
                            FROM facturacion.periodo 
                            WHERE tipo='V'";
    }
    $result = sql($sql, "No se encuentra el periodo: $idperiodo", 0);
    if ($result->RecordCount() > 0) {
        $periodo['periodo'] = $result->fields['periodo'];
        $periodo['tipo'] = $result->fields['tipo'];
        $periodo['id'] = $result->fields['id_periodo'];
    }
    return $periodo;
}

function obtenerIdSmiafiliado($clavebeneficiario, $id_factura, $fechapractica, $tipo_facturacion, $fechaperiodoliquidado) {
    $control_afi['id'] = 0;
    //$esvieja = practicaMenor6Meses($id_factura, $fechapractica);
    //if (!$esvieja['debito']) {
    if ($tipo_facturacion == "R") {
        $fechapracticaaux = $fechapractica;
    } elseif ($tipo_facturacion == "V") {
        $fechapracticaaux = $fechaperiodoliquidado . "/01";
    }
    $control_afi = afiliadoEnPadronPorFecha($fechapracticaaux, $clavebeneficiario);
    //}
    return $control_afi;
}

function sigueBuscando($motivobaja) {
    switch ($motivobaja) {
        case 42:
        case 31:
        case 22:
        case 14:
            $sigue = true;
            break;
        default:
            $sigue = false;
            break;
    }
    return $sigue;
}

function obtenerModoVigencia($cuie, $fecha) {
    $fechaprestacion = ConvFechaComoDB($fecha);

    $sql = "SELECT cn.id_nomenclador_detalle , modo_facturacion  
        FROM nacer.efe_conv ec
        INNER JOIN nacer.conv_nom cn USING (id_efe_conv)
    INNER JOIN facturacion.nomenclador_detalle nd on nd.id_nomenclador_detalle=cn.id_nomenclador_detalle
        WHERE ec.cuie='$cuie'
        AND nd.fecha_desde <='$fechaprestacion'
        AND nd.fecha_hasta >='$fechaprestacion'";

    $result = sql($sql, "", 0);

    if ($result->RecordCount() > 0) {
        $resul['id'] = $result->fields['id_nomenclador_detalle'];
        $resul['modo'] = $result->fields['modo_facturacion'];
    } else {
        $resul['id'] = 0;
        $resul['modo'] = 0;
    }
    return $resul;
}

function edad_relativa($fecha_nacimiento, $fecha_consulta) {
    list($dia_nacimiento, $mes_nacimiento, $anio_nacimiento) = explode("/", $fecha_nacimiento);
    list($dia_consulta, $mes_consulta, $anio_consulta) = explode("/", $fecha_consulta);
    $anio_dif = $anio_consulta - $anio_nacimiento;
    $mes_dif = $mes_consulta - $mes_nacimiento;
    $dia_dif = $dia_consulta - $dia_nacimiento;
    if (($dia_dif < 0 && $mes_dif == 0) || $mes_dif < 0)
        $anio_dif--;
    return $anio_dif;
}

function limpiar($registro) {
    foreach ($registro as $clave => $valor) {
        $valor = str_replace("''", "", $valor);
        $valor = str_replace("'", "", $valor);
        $valor = str_replace("!", "", $valor);
        $valor = str_replace("///", "", $valor);
        $valor = str_replace("//", "", $valor);
        $valor = str_replace("}", "", $valor);
        $valor = str_replace("{", "", $valor);
        $valor = str_replace("?:..", "", $valor);
        $valor = str_replace("?", "", $valor);
        $valor = str_replace('"', "", $valor);
        $valor = str_replace("#", "", $valor);
        $valor = str_replace("=", "", $valor);
        $valor = str_replace("~", "", $valor);
        $valor = str_replace("%", "", $valor);
        $valor = str_replace("(", "", $valor);
        $valor = str_replace(")", "", $valor);
        $registro[$clave] = $valor;
    }
    return $registro;
}

function fechaPrestacionXLimite($fprestacion, $fprest_limite) {
    //control del limite de presentacion de factura segun el periodo facturado
    $pr = ((strtotime($fprest_limite) - strtotime($fprestacion)) / 86400); //limite de la prestacion - fecha prestacion
    $ctrl_fechapresta['debito'] = false;
    if ($pr < 0) {

        $ctrl_fechapresta['debito'] = true;
        $ctrl_fechapresta['id_error'] = 71;
        $ctrl_fechapresta['msj_error'] = 'Fecha de Prestacion supera el limite para el periodo liquidado';
    }
    return $ctrl_fechapresta;
}

function prepararDatosComprobante(&$l, &$var) {
    if ($l[5] == null) {
        $claveBeneficiario = 'vacio';
    } else {
        $claveBeneficiario = $l[5];
    }
    if ($l[6] == null) {
        $l[6] = '';
        $var['error_datos'] = 'si';
        $var['mjs_error_datos'] .= ',Clase Doc.';
        if ($l[3] == 1) {
            $var['error'] = 'si';
            $var['descripcion_error'] .= ',claseDoc';
        }
    } else {
        $var['error'] = 'no';
    }


    if ($l[7] == null) {
        $l[7] = '';
        $var['error'] = 'si';
        $var['descripcion_error'] .= ',tipoDoc';
    } else {
        $tipoDoc = $l[7];
        if ($tipoDoc == 'EXT') {
            $var['error'] = 'si';
            $var['descripcion_error'] .= ',tipoDoc';
        }
        if ($tipoDoc == 'CI' || $tipoDoc == 'ci') {
            $l[7] = 'C20';
        }
    }

    $l[8] = intval($l[8]);
    if ($l[8] == '' || $l[8] == null) {
        $l[8] = 0;
        $var['error'] = 'si';
        $var['error_datos'] = 'si';
        $var['mjs_error_datos'] .= ',NroDoc.';
    }

    $permitidos = "0123456789";
    for ($i = 0; $i < strlen($l[8]); $i++) {
        if (strpos($permitidos, substr($l[8], $i, 1)) === false) {
            $var['error'] = 'si';
            $l[8] = 0;
            $var['error_datos'] = 'si';
            $var['mjs_error_datos'] .= ',NroDoc.';
        }
    }
    $var['metez'] = 's';
    if ($l[8] == 0 && $claveBeneficiario == 'vacio') {
        $var['descripcion_error'] .= ',no clave_dni';
        $var['error'] = 'si';
        $var['error_datos'] = 'si';
        $var['mjs_error_datos'] .= ',NroDoc y ClaveBeneficiario';
        $var['metez'] = 'n';
    }
    if ($l[9] == null) {
        $l[9] = '';
    } else {
        $l[9] = str_replace("'", "", $l[9]);
    }
    if ($l[10] == null) {
        $l[10] = '';
    } else {
        $l[10] = str_replace("'", "", $l[10]);
    }
    if ($l[11] == null) {
        $l[11] = '';
    }
    ////////////////////////////////////////////////////////////////////////////////

    list($dia, $mes, $ano) = explode('/', $l[13]);
    $var['prestacion'] = $ano . $mes . $dia;
    list($dia1, $mes1, $ano1) = explode('/', $var['fechaNac']);
    $var['nacimiento'] = $ano1 . $mes1 . $dia1;
    $var['menor'] = $var['prestacion'] - $var['nacimiento'];

    if ($l[16] == null)
        $l[16] = '';

    if ($l[17] == null)
        $l[17] = '';

    if ($l[18] == null || $l[3] == 2 || $l[3] == 3)
        $l[18] = '';

    /* si formato nuevo */
    if ($l[3] == 14) {
        if ($l[48] == null) {
            $l[48] = '';
            $var['e_defuncion'] = 'si';
            $var['descripcion_error'] .= ',fdefuncion';
            $var['error'] = 'si';
        }
        if ($l[49] == null)
            $l[49] = '';
        if ($l[50] == null) {
            $l[50] = '';
            $var['e_caso'] = 'si';
            $var['descripcion_error'] .= ',caso';
            $var['error'] = 'si';
        }
    }
    /* fin_ si formato nuevo */
    $var['perimcef_rn'] = 0;
    $var['talla_rn'] = 0;
    $var['au'] = 0;
    $var['tamin'] = 0;
    $var['tamax'] = 0;
    $var['peso_mem02'] = 0;
    //  if ($var['new_txt'] <= 0) {
    if ($l[3] == 2) {
        if ($l[59] == null || $l[59] == '')
            $l[59] = 0; //$descripcion_error.=',tamax'; $error='si';
        if ($l[60] == null || $l[60] == '')
            $l[60] = 0; //$descripcion_error.=',tamin'; $error='si';
        if ($l[61] == null || $l[61] == '')
            $l[61] = 0; //if($l[12]=='MEM 02' || $l[12]=='MER 08'){ $descripcion_error.=',au'; $error='si';}
        if ($l[64] == null || $l[64] == '')
            $l[64] = 0; //if($l[12]=='MEM 02'){$descripcion_error.=',peso_mem02'; $error='si';}
    }
    if ($l[62] == null || $l[62] == '')
        $l[62] = 0; //$descripcion_error.=',talla_rn'; $error='si';
    if ($l[63] == null || $l[63] == '')
        $l[63] = 0; //$descripcion_error.=',perimcef_rn'; $error='si';
    if ($l[65] == null)
        $l[65] = '';
    /* $error_datos='si'; $mjs_error_datos.=',Sexo'; */
    if ($l[66] == null || $l[66] == '')
        $l[66] = 0;
    /* $error_datos='si'; $mjs_error_datos.=',Municipio'; */
    if ($l[3] == 1) {
        if ($l[67] == null)
            $l[67] = '';
        if (strlen($l[67]) > 1) {
            $var['descripcion_error'] .= ',percentilo_imc';
            //$var['error'] = 'si';
            //excepcion('Rechazado por valor err?neo de percentilo_imc');
        }
        /* if ($menor >=10000){//mayor de 1 a?o
          $descripcion_error.=',percentilo_imc'; $error='si'; } */
        if ($l[68] == null || $l[68] == '')
            $l[68] = 0;
        /* if ($menor >=10000){//mayor de 1 a?o
          $descripcion_error.=',imc'; $error='si'; } */
    }
    if ($l[53] == null || $l[53] == '')
        $l[53] = 0;
    if ($l[69] == null)
        $l[69] = '';
    /* $error_datos='si'; $mjs_error_datos.=',discapacitado'; */
    if ($l[70] == null)
        $l[70] = '';
    /* $error_datos='si'; $mjs_error_datos.=',discapacitado'; */
    if ($l[71] == null)
        $l[71] = '';
    /* $error_datos='si'; $mjs_error_datos.=',discapacitado'; */
    if ($l[72] == null)
        $l[72] = '';
    /* $error_datos='si'; $mjs_error_datos.=',discapacitado'; */
    if ($l[73] == null)
        $l[73] = '';
    /* $error_datos='si'; $mjs_error_datos.=',discapacitado'; */
    if ($l[74] == null)
        $l[74] = '';
    /* $error_datos='si'; $mjs_error_datos.=',discapacitado'; */
}

function comprobanteEstaRepetido($cuie, $periodo, $prestacionid, $idprestacion, $idrecepcion, $datosnomenclador, $elcomprobante, $fechaprestacion, $beneficiario, $idfactura, $idvacuna) {
    ////////////verifica repetidos identicos, osea la misma mismisima linea de facturacion liquidada denuevo,
    // repitiendo el id de prestacion interno, cosa que no sucede casi nunca
    $query = "SELECT fc.cuie as cuie, ff.recepcion_id AS idrecepcion, id_debito,fp.id_prestacion
                FROM facturacion.factura ff 
                INNER JOIN facturacion.comprobante fc ON (ff.id_factura = fc.id_factura) 
                INNER JOIN facturacion.prestacion fp ON (fc.id_comprobante = fp.id_comprobante)    
                LEFT JOIN facturacion.debito d ON (fc.id_comprobante=d.id_comprobante)
                WHERE fc.cuie='$cuie' 
                AND ff.periodo='$periodo' 
                AND fc.idprestacion='$prestacionid'
                AND fp.id_prestacion<>$idprestacion";
    $resultado = sql($query, "Error al buscar comprobante repetido", 0) or excepcion("Error al buscar comprobante repetido");
    $ctrl_repetido['debito'] = false;
    $yadebitado = $resultado->fields['id_debito'];
    $recibidodespues = $resultado->fields['id_prestacion'] < $prestacionid;
    if (($resultado->RecordCount() > 0) && !($yadebitado) && $recibidodespues) {
        //$var['existe_id'] = 'si';
        $idrecepcion_idb = $resultado->fields['idrecepcion'];
        if ($idrecepcion_idb != $idrecepcion) {
            $ctrl_repetido['msj_error'] = 'ID Prestacion ya existente en el sistema';
            $ctrl_repetido['id_error'] = 73;
        }
        if ($idrecepcion_idb == $idrecepcion) {
            $ctrl_repetido['msj_error'] = 'ID Prestacion ya existente en el archivo';
            $ctrl_repetido['id_error'] = 74;
        }
        $ctrl_repetido['debito'] = true;
    } else {
        if (esNomencladorVacuna($datosnomenclador)) {

            //Controles para los nomencladores que son vacuna
            $query = "SELECT fc.id_comprobante, nro_exp
                FROM facturacion.prestacion fp
                INNER JOIN facturacion.comprobante fc ON (fc.id_comprobante = fp.id_comprobante)
                INNER JOIN facturacion.factura f ON (fc.id_factura=f.id_factura)
                WHERE id_prestacion<>$idprestacion
		AND fc.fecha_comprobante=to_date('$fechaprestacion','DD-MM-YYYY')
                AND fp.id_nomenclador='" . $datosnomenclador[0] . "'
		AND fc.clavebeneficiario='$beneficiario'
                AND fc.id_comprobante<>'$elcomprobante'
                AND fc.idvacuna='$idvacuna'
		AND fc.id_comprobante NOT IN(
                            SELECT id_comprobante 
                            FROM facturacion.debito 
                            WHERE id_factura='$idfactura')";
            $resultado = sql($query, "Error al buscar comprobante repetido", 0) or excepcion("Error al buscar comprobante repetido");
            $recibidodespues = $resultado->fields['id_comprobante'] < $idcomprobante;
            if (($resultado->RecordCount() > 0) && ($recibidodespues)) {
                $ctrl_repetido['msj_error'] = 'Prestacion liquidada en Expediente: ' . $resultado->fields['nro_exp'];
                $ctrl_repetido['id_error'] = 74;
                $ctrl_repetido['debito'] = true;
            }
        } else {

            //Controles para los nomencladores que no son de vacu
            $query = "SELECT fc.id_comprobante, nro_exp
                FROM facturacion.prestacion fp
                INNER JOIN facturacion.comprobante fc ON (fc.id_comprobante = fp.id_comprobante)
                INNER JOIN facturacion.factura f ON (fc.id_factura=f.id_factura)
                WHERE id_prestacion<>$idprestacion
		AND fc.fecha_comprobante=to_date('$fechaprestacion','DD-MM-YYYY')
		AND fp.id_nomenclador='" . $datosnomenclador[0] . "'
		AND fc.clavebeneficiario='$beneficiario'
                AND fc.id_comprobante<>'$elcomprobante'
		AND fc.id_comprobante NOT IN(
                            SELECT id_comprobante 
                            FROM facturacion.debito 
                            WHERE id_factura='$idfactura')";
            $resultado = sql($query, "Error al buscar comprobante repetido", 0) or excepcion("Error al buscar comprobante repetido");
            $recibidodespues = $resultado->fields['id_comprobante'] < $idcomprobante;
            if (($resultado->RecordCount() > 0) && ($recibidodespues)) {
                $ctrl_repetido['msj_error'] = 'Prestacion liquidada en Expediente: ' . $resultado->fields['nro_exp'];
                $ctrl_repetido['id_error'] = 74;
                $ctrl_repetido['debito'] = true;
            }
        }
    }
    return $ctrl_repetido;
}

function controlFechaEntrada($fecha_entrada, $fecha_comprobante) {
    $diff = GetCountDaysBetweenTwoDates($fecha_entrada, $fecha_comprobante);
    if (($fecha_entrada == NULL || $fecha_entrada = '') || ($diff > 120)) {
        $ctrl['msj_error'] = "Prestacion posee mas de 4 meses de antiguedad";
        $ctrl['id_error'] = 66;
        $ctrl['debito'] = true;
        return $ctrl;
    }
}

function excepcionSumarSoloEnero($cuie, $fechacomprobante, $grupo_etario) {
    $ctrl['debito'] = false;
    if (($grupo_etario['edad'] >= 6) && (!$grupo_etario['estaembarazada'])) {
        if (!aptoPoblacionNueva($cuie, $fechacomprobante)) {
            $ctrl['msj_error'] = 'Poblacion de SUMAR no habilitada para el efector [' . $grupo_etario['descripcion'] . "]";
            $ctrl['id_error'] = 81;
            $ctrl['debito'] = true;
        }
    }
    return $ctrl;
}

function controlGrupoEtareo($idpadron, $fechapractica, $datos_nomenclador, $clavebeneficiario) {
    $ctrl['debito'] = false;
    //$nomenclador = $var['id_nomenclador'];
    $grupo_eta = grupoEtareoDelAfiliado($clavebeneficiario, $fechapractica, $idpadron);

    if (($grupo_eta != $datos_nomenclador[4]) && ($datos_nomenclador[4] != 2)) {
        switch ($datos_nomenclador[4]) {
            case 0:
                $grupo = "Embarazada/Puerpera";
                break;
            case 1:
                $grupo = "RN/Niï¿½o";
                break;
            case 2:
                $grupo = "Indistinto";
                break;
        }
        $ctrl['msj_error'] = 'Categoria de Beneficiario no corresponde con el Grupo Etario de la Prestacion [' . $grupo . ']';
        $ctrl['id_error'] = 77;
        $ctrl['debito'] = true;
    }

    return $ctrl;
}

function verificarTrazadora(&$l, &$var, $nomenclador) {

    if (is_null($nomenclador)) {
        $id_nomenclador = 0;
    } else {
        $id_nomenclador = $nomenclador->getIdNomenclador();
    }

    if ($l[19] == null || $l[19] == '')
        $l[19] = 0;
    if ($l[27] == null || $l[27] == '')
        $l[27] = 0;
    if ($l[30] == null || $l[30] == '')
        $l[30] = 0;
    if ($l[32] == null || $l[32] == '')
        $l[32] = 0;
    if ($l[40] == null || $l[40] == '')
        $l[40] = 0;
    if ($l[41] == null || $l[41] == '')
        $l[41] = 0;
    if ($l[59] == null || $l[59] == '')
        $l[59] = 0;
    if ($l[60] == null || $l[60] == '')
        $l[60] = 0;
    if ($l[37] == null)
        $l[37] = '';
    if ($l[38] == null)
        $l[38] = '';
    if ($l[39] == null || $l[39] == '')
        $l[39] = 0;
    if ($l[34] == null || $l[34] == '')
        $l[34] = 0;
    $cabeza = strlen($l[63]);
    if ($cabeza > 4) {
        $l[63] = 0;
    }
    $talla = strlen($l[62]);
    if ($talla > 4) {
        $l[62] = 0;
    }

    if ($var['error'] != 'si') {
        $ya_esta = existeIdTrazadora($l, $var, $id_nomenclador);

        if ($ya_esta == true) {
            actualizarTrazadora($l, $var);
        } elseif (!is_null($ya_esta)) {
            insertarTrazadora($l, $var);
        }
    } else {
        $var['cuenta_error']++;
        $ya_esta_tmp = existeIdTrazadoraTMP($l, $var, $id_nomenclador);
        if ($ya_esta_tmp == true) {
            actualizarTrazadoraTMP($l, $var);
        } elseif (!is_null($ya_esta_tmp)) {
            insertarTrazadoraTMP($l, $var);
        }
    }
}

function rechazoTrazadora($l, &$var) {
    do {
        if ($l[0] == "L") {
            $resultado_ctrl = controlInformado($l[1], $l[4], $l[13], $var['datos_nombre_archivo']['tipo_facturacion']);
            if ($resultado_ctrl['debito'])
                break;
            $fechaentrada = $resultado_ctrl['fecha'];
        }
        if ($fechaentrada == null)
            $fechaentrada = $_POST['fecha_entrada'];


        $fechaentradadb = strtotime(Fecha_db($fechaentrada));
        $fechalimitedb = strtotime($var['limite_trz']['limite']);

        //if (($fechaentradadb > $fechalimitedb) || (($fechaentrada == null ) && ($l[0] == "L"))) {
        //   $var['error'] = 'si';
        //   $var['descripcion_error'] = 'Trazadoras presentada fuera de termino: ' . $l[13];
        //    break;
        //}
        break;
    } while (true);
}

function insertarDebito($datosdebito) {
    $e_sql = "select id_debito from facturacion.debito d
        INNER JOIN facturacion.comprobante using(id_comprobante)
                where d.id_factura=" . $datosdebito['id_factura'] .
            " and documento_deb='" . $datosdebito['documento_deb'] . "' and codigo_deb='" . $datosdebito['codigo_deb'] . "'
                AND idprestacion='" . $datosdebito['prestacionid'] . "'";
    $e_busqueda = sql($e_sql, "Error al consultar debito") or excepcion("Error al consultar debito", 0);
    if ($e_busqueda->RecordCount() == 0) {
        $SQLbenef = "INSERT INTO facturacion.debito (id_factura,id_comprobante, id_nomenclador, cantidad,id_motivo_d,
                 monto, documento_deb, apellido_deb, nombre_deb, codigo_deb,
                observaciones_deb, mensaje_baja,id_prestacion)
                VALUES (" . $datosdebito['id_factura'] . ", " . $datosdebito['id_comprobante'] . ", " . $datosdebito['id_nomenclador'] . ", " . $datosdebito['cantidad'] . ",
                    '" . $datosdebito['resultado_ctrl']['id_error'] . "'," . $datosdebito['monto_deb'] . ", '" . $datosdebito['documento_deb'] . "', '" . $datosdebito['apellido_deb'] . "', '" . $datosdebito['nombre_deb'] .
                "', '" . $datosdebito['codigo_deb'] . "', '" . $datosdebito['observaciones_deb'] . "', '" . $datosdebito['resultado_ctrl']['msj_error'] . "'," . $datosdebito['idprestacion'] . ")";
        /////////////////////////////////////////error/////////////////////////////////
        sql($SQLbenef, "Error al insertar d&eacute;bito", 0) or excepcion("Error al insertar d&eacute;bito");
    }
}

function debitarPrestacion($datosdebito) {

    $SQLbenef = "INSERT INTO facturacion.debito (id_factura,id_comprobante, id_nomenclador, cantidad,id_motivo_d,
                 monto, documento_deb, apellido_deb, nombre_deb, codigo_deb,
                observaciones_deb, mensaje_baja,id_prestacion)
                VALUES (" . $datosdebito['id_factura'] . ", " . $datosdebito['id_comprobante'] . ", " . $datosdebito['id_nomenclador'] . ", " . $datosdebito['cantidad'] . ",
                    '" . $datosdebito['id_debito'] . "'," . $datosdebito['monto_deb'] . ", '" . $datosdebito['documento_deb'] . "', '" . $datosdebito['apellido_deb'] . "', '" . $datosdebito['nombre_deb'] .
            "', '" . $datosdebito['codigo_deb'] . "', '" . $datosdebito['observaciones_deb'] . "', '" . $datosdebito['msj_debito'] . "'," . $datosdebito['idprestacion'] . ") RETURNING id_debito";
    /////////////////////////////////////////error/////////////////////////////////
    $id_debito = sql($SQLbenef, "Error al insertar d&eacute;bito", 0) or excepcion("Error al insertar d&eacute;bito");
    return $id_debito->fields['id_debito'];
}

function insertarDebitoManual($datos) {
    $SQLbenef = "INSERT INTO facturacion.debitos_manuales (id_debito,id_motivo,observaciones,usuario,fechahora)
                VALUES (" . $datos['id_debito'] . "," . $datos['id_motivo'] . ",'" . $datos['observaciones'] . "'," . $datos['usuario'] . ",'" . $datos['fechahora'] . "')";
    /////////////////////////////////////////error/////////////////////////////////
    $id_debito = sql($SQLbenef, "Error al insertar d&eacute;bito", 0) or excepcion("Error al insertar d&eacute;bito");
}

function quitarDebitoManual($id_debito) {
    sql('BEGIN');
    $SQLbenef = "DELETE FROM facturacion.debito
                WHERE id_debito='$id_debito'";
    /////////////////////////////////////////error/////////////////////////////////
    sql($SQLbenef, "Error al insertar d&eacute;bito", 0) or excepcion("Error al insertar d&eacute;bito");

    $SQLbenef = "DELETE FROM facturacion.debitos_manuales
                WHERE id_debito='$id_debito'";
    /////////////////////////////////////////error/////////////////////////////////
    sql($SQLbenef, "Error al insertar d&eacute;bito", 0) or excepcion("Error al insertar d&eacute;bito");
    sql('COMMIT');
}

function existeIdTrazadora(&$l, &$var, $idnom) {
    if ($l[3] == 1)
        $idr = "SELECT tn.clave FROM facturacion.prestacion fp 
        INNER JOIN trazadoras.nino_new tn ON (fp.prestacionid = tn.id_prestacion) 
        WHERE tn.cuie='$l[1]' 
        AND fp.prestacionid=$l[4] 
        AND fp.id_nomenclador='" . $idnom . "'";

    if ($l[3] == 2)
        $idr = "SELECT tn.clave FROM facturacion.prestacion fp 
        INNER JOIN trazadoras.embarazadas tn ON (fp.prestacionid = tn.id_prestacion) 
        WHERE tn.cuie='$l[1]' 
        AND fp.prestacionid=$l[4] 
        AND fp.id_nomenclador='" . $idnom . "'";

    if ($l[3] == 3)
        $idr = "SELECT tn.clave FROM facturacion.prestacion fp 
        INNER JOIN trazadoras.partos tn ON (fp.prestacionid = tn.id_prestacion) 
        WHERE tn.cuie='$l[1]' 
        AND fp.prestacionid=$l[4] 
        AND fp.id_nomenclador='" . $idnom . "'";

    if ($l[3] == 14)
        $idr = "SELECT tn.cuie FROM facturacion.prestacion fp 
        INNER JOIN trazadoras.mu tn ON (fp.prestacionid = tn.id_prestacion) 
        WHERE tn.cuie='$l[1]' 
        AND fp.prestacionid=$l[4] 
        AND fp.id_nomenclador='" . $idnom . "'";

    if ($l[3] == 36)
        $idr = "SELECT tn.cuie FROM facturacion.prestacion fp 
        INNER JOIN trazadoras.adolecentes tn ON (fp.prestacionid = tn.id_prestacion) 
        WHERE tn.cuie='$l[1]' 
        AND fp.prestacionid=$l[4] 
        AND fp.id_nomenclador='" . $idnom . "'";

    if ($l[3] == 37)
        $idr = "SELECT tn.cuie FROM facturacion.prestacion fp 
        INNER JOIN trazadoras.adultos tn ON (fp.prestacionid = tn.id_prestacion) 
        WHERE tn.cuie='$l[1]' 
        AND fp.prestacionid=$l[4] 
        AND fp.id_nomenclador='" . $idnom . "'";

    if ($l[3] == 38)
        $idr = "SELECT tn.clave FROM facturacion.prestacion fp 
        INNER JOIN trazadoras.nino_new tn ON (fp.prestacionid = tn.id_prestacion) 
        WHERE tn.cuie='$l[1]' 
        AND fp.prestacionid=$l[4] 
        AND fp.id_nomenclador='" . $idnom . "'";

    if (!is_null($idr)) {
        $result_idr = sql($idr, "Error al consultar existencia de trazadora $idr", 0) or
                excepcion("Error al consultar existencia de trazadora");

        if ($result_idr->recordCount > 0) {
            $ya_esta = true;
        } else {
            $ya_esta = false;
        }
    }
    return $ya_esta;
}

function actualizarTrazadora(&$l, &$var) {

    if ($l[3] == 1) {

        $ninios["nino_edad"] = floor($var['grupo_etario']['edad']);
        $ninios["fecha_carga"] = date("d/m/Y");
        $ninios["usuario"] = $var['id_user'];

        $SQLnU = "UPDATE trazadoras.nino_new SET cuie = '" . $l[2] . "', clave = '" . $l[5] .
                "', clase_doc = '" . $l[6] . "', tipo_doc = '" . $l[7] . "', num_doc = " . $l[8] .
                ", apellido = '" . $l[9] . "', nombre = '" . $l[10] . "', fecha_nac = '" .
                Fecha_db($l[11], '1899-12-31') . "', fecha_control = '" . Fecha_db($l[13], '1899-12-31')
                . "', peso = " . $l[30] . ", talla = " . $l[32] . ", percen_peso_edad = '" . $l[31]
                . "', percen_talla_edad = '" . $l[33] . "', perim_cefalico = " . $l[34] . ", percen_perim_cefali_edad = '" . $l[35]
                . "', imc = '" . $l[68] . "', percen_peso_talla = '" . $l[36] . "', triple_viral = '" . Fecha_db($l[37], '1899-12-31')
                . "', nino_edad = " . $ninios["nino_edad"] . ", observaciones = '" . $l[58]
                . "', fecha_carga = '" . Fecha_db($ninios["fecha_carga"], '1899-12-31') . "', usuario = '" . $ninios["usuario"]
                . "', fecha_obito = '" . Fecha_db($l[38], '1899-12-31') . "', ncontrolanual = " . $l[39]
                . ", id_prestacion = " . $l[4] . ", sexo = '" . $l[65] . "', municipio = " . $l[66] . ", percentilo_imc = '" . $l[67]
                . "', discapacitado = '" . $l[69] . "', cod_aldea = '" . $l[70] . "', descr_aldea = '" . $l[71] . "', calle = '" . $l[72]
                . "', num_calle = '" . $l[73] . "', barrio = '" . $l[74] . "', cod_nomenclador = '" . $l[12]
                . "', id_recepcion = " . $var['recepcion_id'] .
                " WHERE id_prestacion = " . $l[4] . " 
                    AND cuie = '" . $l[1] . "'
                    AND cod_nomenclador = '" . $l[12] . "'";
        sql($SQLnU, "Error al actualizar trazadora", 0) or excepcion("Error al actualizar trazadora");
        $var['c_n']++;
    }

    if ($l[3] == 2) {
        /* EMBARAZADAS */

        $embarazada["fecha_carga"] = date("d/m/Y");
        $embarazada["usuario"] = $var['id_user'];

        /* graba beneficiarios */

        $SQLeU = "UPDATE trazadoras.embarazadas 
                    SET cuie = '" . $l[1] . "', clave = '" .
                $l[5] . "', tipo_doc = '" . $l[7] . "', num_doc = " . $l[8] . ", apellido = '" .
                $l[9] . "', nombre = '" . $l[10] . "', fecha_control = '" . Fecha_db($l[13], '1899-12-31') . "', sem_gestacion = " . $l[19] .
                ", fum = '" . Fecha_db($l[29], '1899-12-31') . "', fpp = '" . Fecha_db($l[28], '1899-12-31') . "', fpcp = '" .
                Fecha_db($l[13], '1899-12-31') . "', fecha_carga = '" . Fecha_db($embarazada["fecha_carga"], '1899-12-31') . "', usuario = '"
                . $embarazada["usuario"] . "', antitetanica = '" . $l[51] . "', vdrl = '" . Fecha_db($l[23], '1899-12-31')
                . "', estado_nutricional = '" . $l[20] . "', antitetanica_primera_dosis = '" . Fecha_db($l[21], '1899-12-31')
                . "', antitetanica_segunda_dosis = '" . Fecha_db($l[22], '1899-12-31') . "', hiv = '" . Fecha_db($l[24], '1899-12-31')
                . "', eco = '" . Fecha_db($l[25], '1899-12-31') . "', fecha_obito = '" . Fecha_db($l[26], '1899-12-31')
                . "', nro_control_actual = " . $l[27] . ", tension_arterial_maxima = " . $l[59] . ", tension_arterial_minima = " . $l[60]
                . ", altura_uterina = " . $l[61] . ", peso_embarazada = " . $l[64] . ", vdrl_fecha = '" . Fecha_db($l[23], '1899-12-31')
                . "', hiv_fecha = '" . Fecha_db($l[24], '1899-12-31') . "', municipio = " . $l[66] . ", discapacitado = '" . $l[69]
                . "', fecha_nacimiento = '" . Fecha_db($l[11], '1899-12-31') . "', id_prestacion = " . $l[4] . ", id_recepcion = "
                . $var['recepcion_id'] . " 
                    WHERE id_prestacion = " . $l[4] . " 
                    AND cuie = '" . $l[1] . "'";

        sql($SQLeU, "Error al actualizar trazadora", 0) or excepcion("Error al actualizar trazadora");
        $var['c_e']++;
    }

    if ($l[3] == 3) {
        /* PARTOS */

        $parto["fecha_carga"] = date("d/m/Y");
        $parto["usuario"] = $var['id_user'];

        /* graba beneficiarios */

        $SQLpU = "UPDATE trazadoras.partos 
                    SET cuie = '" . $l[2] . "', clave = '" . $l[5] .
                "', tipo_doc = '" . $l[7] . "', num_doc = " . $l[8] . ", apellido = '" . $l[9] .
                "', nombre = '" . $l[10] . "', fecha_parto = '" . Fecha_db($l[13], '1899-12-31') .
                "', apgar = " . $l[41] . ", peso = " . $l[40] . ", vdrl = '" . $l[42] . "', antitetanica = '"
                . $l[43] . "', fecha_conserjeria = '" . Fecha_db($l[46], '1899-12-31') . "', observaciones = '" . $l[58] .
                "', fecha_carga = '" . Fecha_db($parto["fecha_carga"], '1899-12-31') . "', usuario = '" . $parto["usuario"] .
                "', obito_bebe = '" . Fecha_db($l[44]) . "', obito_madre = '" . Fecha_db($l[45], '1899-12-31') .
                "', id_prestacion = " . $l[4] . ", obb_desconocido = '" . $l[47] . "', talla_rn = " . $l[62] .
                ", perimcef_rn = " . $l[63] . ", fecha_nacimiento = '" . Fecha_db($l[11], '1899-12-31') .
                "', discapacitado = '" . $l[69] . "', municipio = " . $l[66] . " , id_recepcion = " . $var['recepcion_id'] . " 
                    WHERE id_prestacion = " . $l[4] .
                " AND cuie = '" . $l[1] . "'";
        sql($SQLpU, "Error al actualizar trazadora", 0) or excepcion("Error al actualizar trazadora");
        $var['c_p']++;
    }

    if ($l[3] == 36) {
        $adolecente["fecha_carga"] = date("d/m/Y");
        $adolecente["usuario"] = $var['id_user'];

        //ADOLECENTES
        $SQLdlU = "UPDATE trazadoras.adolecentes
                    SET cuie = '" . $l[2] . "', clave = '" . $l[5] .
                "', num_doc = " . $l[8] . ", tipo_doc = '" . $l[7] . "', apellido = '" . $l[9] .
                "', nombre = '" . $l[10] . "', fecha_nac = '" . Fecha_db($l[11], '1899-12-31') .
                "', fecha_control = " . Fecha_db($l[13], '1899-12-31') . ", peso = " . $l[30] . ", observaciones = '" . $l[58] .
                "', talla = '" . $l[32] . "', imc = '" . $l[68] . "', perc_imc_edad = '" . $l[67] .
                "', tamin = '" . $l[59] . "', tamax = '" . $l[60] . "', sexo = '" . $l[65] .
                "', codnomenclador = '" . $l[12] . "', fecha_carga = '" . Fecha_db($adolecente["fecha_carga"], '1899-12-31') .
                "', usuario = '" . $adolecente["usuario"] . "', id_recepcion = " . $var['recepcion_id'] . " 
                    WHERE id_prestacion = " . $l[4] . " AND cuie = '" . $l[1] . "'";
        sql($SQLdlU, "Error al actualizar trazadora", 0) or excepcion("Error al actualizar trazadora");
        $var['c_dl']++;
    }

    if ($l[3] == 37) {
        //ADULTOS
        $adulto["fecha_carga"] = date("d/m/Y");
        $adulto["usuario"] = $var['id_user'];


        $SQLaU = "UPDATE trazadoras.adultos
                    SET cuie = '" . $l[2] . "', clave = '" . $l[5] .
                "', num_doc = " . $l[8] . ", tipo_doc = '" . $l[7] . "', apellido = '" . $l[9] .
                "', nombre = '" . $l[10] . "', fecha_control = " . Fecha_db($l[13], '1899-12-31') . ", peso = " . $l[30] . "', talla = '" . $l[32] .
                "', sexo = '" . $l[65] . "', tamin = '" . $l[59] . "', tamax = '" . $l[60] . "', observaciones = '" . $l[58] .
                "', fecha_carga = '" . Fecha_db($adulto["fecha_carga"], '1899-12-31') . "', usuario = '" . $adulto["usuario"] .
                "', id_recepcion = " . $var['recepcion_id'] . " 
                    WHERE id_prestacion = " . $l[4] . " AND cuie = '" . $l[1] . "'";
        sql($SQLaU, "Error al actualizar trazadora", 0) or excepcion("Error al actualizar trazadora");
        $var['c_a']++;
    }

    if ($l[3] == 38) {
        //TAL
        $tal["fecha_carga"] = date("d/m/Y");
        $tal["usuario"] = $var['id_user'];

        $SQLtU = "UPDATE trazadoras.tal
                    SET cuie = '" . $l[2] . "', clave = '" . $l[5] . "', tipo_doc = '" . $l[7] .
                "', num_doc = " . $l[8] . ", apellido = '" . $l[9] .
                "', nombre = '" . $l[10] . "', fecha_control = " . Fecha_db($l[13], '1899-12-31') .
                "', sexo = '" . $l[65] . "', tal = '" . $l[15] . "', codnomenclador = '" . $l[12] .
                "', fecha_carga = '" . Fecha_db($adulto["fecha_carga"], '1899-12-31') . "', usuario = '" . $adulto["usuario"] .
                "', id_recepcion = " . $var['recepcion_id'] . " 
                    WHERE id_prestacion = " . $l[4] . " AND cuie = '" . $l[1] . "'";
        sql($SQLtU, "Error al actualizar trazadora", 0) or excepcion("Error al actualizar trazadora");
        $var['c_t']++;
    }
}

function insertarTrazadora(&$l, &$var) {
    $codnomenclador = str_replace(" ", "", $l[12]);
    if ($codnomenclador == 'NPE41' || $codnomenclador == 'RPE93') {
        $SQLnkl = "INSERT INTO trz_antisarampionosa (codigo_efector, clave_beneficiario,
        clase_documento, tipo_documento, numero_documento, apellido, nombre, fecha_nacimiento,
        fecha_control, fecha_vacunacion, prestacion_id, idprestacion, sexo, municipio,
        discapacitado, id_recepcion) 
        values ('$l[1]','$l[5]','$l[6]','$l[7]','$l[8]','$l[9]','$l[10]','" .
                Fecha_db($l[11], '1899-12-31') . "','" . Fecha_db($l[13], '1899-12-31') . "','" .
                Fecha_db($l[13], '1899-12-31') . "'," . $var['id_prestacion'] . ",'$l[4]','$l[65]',$l[66],'$l[69]', " .
                $var['recepcion_id'] . ")";

        sql($SQLnkl, "Error al insertar trazadora", 0) or excepcion("Error al insertar trazadora");
    }

    if ($l[3] == 1) {
        /* NIÑOS */

        $ninios["nino_edad"] = floor($var['grupo_etario']['edad']);
        $ninios["fecha_carga"] = date("d/m/Y");
        $ninios["usuario"] = $var['id_user'];

        $SQLn = "INSERT INTO trazadoras.nino_new (cuie, clave, clase_doc, tipo_doc, num_doc, apellido, nombre, fecha_nac, 
                fecha_control, peso, talla, percen_peso_edad, percen_talla_edad, perim_cefalico, percen_perim_cefali_edad, imc,
                percen_peso_talla, triple_viral, nino_edad, observaciones, fecha_carga, usuario, fecha_obito, ncontrolanual, id_prestacion, 
                sexo, municipio, percentilo_imc, discapacitado, cod_aldea, descr_aldea, calle, num_calle, barrio, cod_nomenclador, id_recepcion) 
                VALUES ('" . $l[1] . "','" . $l[5] . "','" . $l[6] . "','" . $l[7] . "'," . $l[8] . ",'" . $l[9] . "','" . $l[10] . "','" . Fecha_db($l[11], '1899-12-31') . "','"
                . Fecha_db($l[13], '1899-12-31') . "'," . $l[30] . "," . $l[32] . ",'" . $l[31] . "','" . $l[33] . "'," . $l[34] . ",'" . $l[35] . "','" .
                $l[68] . "','" . $l[36] . "','" . Fecha_db($l[37], '1899-12-31') . "'," . $ninios["nino_edad"] . ",'" . $l[58] . "','" . Fecha_db($ninios["fecha_carga"], '1899-12-31') . "','" .
                $ninios["usuario"] . "','" . Fecha_db($l[38], '1899-12-31') . "'," . $l[39] . "," . $l[4] . ",'" . $l[65] . "'," . $l[66] . ",'" . $l[67] . "','" .
                $l[69] . "','" . $l[70] . "','" . $l[71] . "','" . $l[72] . "','" . $l[73] . "','" . $l[74] . "','" . str_replace('-', '', $l[12]) . "', " . $var['recepcion_id'] . ")";

        sql($SQLn, "Error al insertar trazadora", 0) or excepcion("Error al insertar trazadora");
        $var['c_n']++;

        $SQLnD = "DELETE FROM trazadoras.nino_tmp 
            WHERE id_prestacion = $l[4] 
            AND cuie = '$l[1]'";

        sql($SQLnD, "Error al eliminar trazadora temporal", 0) or excepcion("Error al eliminar trazadora temporal");
    }

    if ($l[3] == 2) {
        /* EMBARAZADAS */

        $embarazada["fecha_carga"] = date("d/m/Y");
        $embarazada["usuario"] = $var['id_user'];


        $SQLe = "INSERT INTO trazadoras.embarazadas (cuie, clave, tipo_doc, num_doc, apellido, nombre, fecha_control,
                sem_gestacion, fum, fpp, fpcp, fecha_carga, usuario, antitetanica, vdrl, estado_nutricional, antitetanica_primera_dosis,
                antitetanica_segunda_dosis, hiv, eco, fecha_obito, nro_control_actual, tension_arterial_maxima, tension_arterial_minima, 
                altura_uterina, peso_embarazada, vdrl_fecha, hiv_fecha, municipio, discapacitado, fecha_nacimiento, id_prestacion, id_recepcion)
                VALUES ('" . $l[1] . "','" . $l[5] . "','" . $l[7] . "'," . $l[8] . ",'" . $l[9] . "','" . $l[10] .
                "','" . Fecha_db($l[13], '1899-12-31') . "'," . $l[19] . ",'" . Fecha_db($l[29], '1899-12-31') .
                "','" . Fecha_db($l[28], '1899-12-31') . "','" . Fecha_db($l[13], '1899-12-31') .
                "','" . Fecha_db($embarazada["fecha_carga"], '1899-12-31') . "','" . $embarazada["usuario"] .
                "','" . $l[51] . "','" . Fecha_db($l[23], '1899-12-31') . "','" . $l[20] . "','" . Fecha_db($l[21], '1899-12-31') .
                "','" . Fecha_db($l[22], '1899-12-31') . "','" . Fecha_db($l[24], '1899-12-31') . "','" .
                Fecha_db($l[25], '1899-12-31') . "','" . Fecha_db($l[26], '1899-12-31') . "'," .
                $l[27] . "," . $l[59] . "," . $l[60] . "," . $l[61] . "," . $l[64] . ",'" .
                Fecha_db($l[23], '1899-12-31') . "','" . Fecha_db($l[24], '1899-12-31') . "'," .
                $l[66] . ",'" . $l[69] . "','" . Fecha_db($l[11], '1899-12-31') . "'," . $l[4] . ", " . $var['recepcion_id'] . ")";

        sql($SQLe, "Error al insertar trazadora", 0) or excepcion("Error al insertar trazadora");
        $var['c_e']++;

        $SQLeD = "DELETE FROM trazadoras.embarazadas_tmp 
            where id_prestacion = $l[4] 
            and cuie = '$l[1]'";

        sql($SQLeD, "Error al eliminar trazadora temporal", 0) or excepcion("Error al eliminar trazadora temporal");
    }
    if ($l[3] == 3) {
        /* PARTOS */

        $parto["fecha_carga"] = date("d/m/Y");
        $parto["usuario"] = $var['id_user'];


        $SQLp = "INSERT INTO trazadoras.partos (cuie, clave, tipo_doc, num_doc, apellido, nombre, fecha_parto,
                apgar, peso, vdrl, antitetanica, fecha_conserjeria, observaciones, fecha_carga, usuario,
                obito_bebe, obito_madre, id_prestacion, obb_desconocido, talla_rn, perimcef_rn, fecha_nacimiento,
                discapacitado, municipio, id_recepcion)	
                VALUES ('" . $l[1] . "','" . $l[5] . "','" . $l[7] . "'," . $l[8] . ",'" . $l[9] . "','" . $l[10] .
                "','" . Fecha_db($l[13], '1899-12-31') . "'," . $l[41] . "," . $l[40] . ",'" . $l[42] .
                "','" . $l[43] . "','" . Fecha_db($l[46], '1899-12-31') . "','" . $l[58] . "','" .
                Fecha_db($parto["fecha_carga"], '1899-12-31') . "','" . $parto["usuario"] .
                "','" . Fecha_db($l[44], '1899-12-31') . "','" . Fecha_db($l[45], '1899-12-31') .
                "'," . $l[4] . ",'" . $l[47] . "'," . $l[62] . "," . $l[63] . ",'" . Fecha_db($l[11], '1899-12-31') .
                "','" . $l[69] . "'," . $l[66] . ", " . $var['recepcion_id'] . ")";

        sql($SQLp, "Error al insertar trazadora", 0) or excepcion("Error al insertar trazadora");
        $var['c_p']++;

        $SQLpD = "DELETE FROM trazadoras.partos_tmp 
            where id_prestacion=$l[4]
            AND cuie='$l[1]'";

        sql($SQLpD, "Error al eliminar trazadora temporal", 0) or excepcion("Error al eliminar trazadora temporal");
    }

    if ($l[3] == 36) {
        //ADOLECENTES
        $adolecente["fecha_carga"] = date("d/m/Y");
        $adolecente["usuario"] = $var['id_user'];


        $SQLn = "INSERT INTO trazadoras.adolecentes
             (cuie,clave,tipo_doc,num_doc,apellido,nombre,fecha_nac,fecha_control,peso,talla,
              imc,percen_imc_edad,tamin,tamax,observaciones,fecha_carga,usuario,sexo,codnomenclador,id_prestacion,id_recepcion)
              VALUES ('" . $l[1] . "','" . $l[5] . "','" . $l[7] . "'," . $l[8] . ",'" . $l[9] . "','" . $l[10] . "','" . Fecha_db($l[11], '1899-12-31') . "','"
                . Fecha_db($l[13], '1899-12-31') . "'," . $l[30] . "," . $l[32] . ",'" .
                $l[68] . "','" . $l[67] . "','" . $l[59] . "','" . $l[60] . "','" . $l[58] . "','" . Fecha_db($adolecente["fecha_carga"], '1899-12-31') . "','" .
                $adolecente["usuario"] . "','" . $l[65] . "','" . $l[12] . "'," . $l[4] . ", '" . $var['recepcion_id'] . "')";
        sql($SQLn, "Error al insertar trazadora", 0) or excepcion("Error al insertar trazadora");
        $var['c_dl']++;

        $SQLnD = "DELETE FROM trazadoras.adolecentes_tmp 
            WHERE id_prestacion = $l[4] 
            AND cuie = '$l[1]'";

        sql($SQLnD, "Error al eliminar trazadora temporal", 0) or excepcion("Error al eliminar trazadora temporal");
    }

    if ($l[3] == 37) {
        //ADULTOS
        $adultos["fecha_carga"] = date("d/m/Y");
        $adultos["usuario"] = $var['id_user'];

        $SQLn = "INSERT INTO trazadoras.adultos (cuie, clave, tipo_doc, num_doc, apellido, nombre, 
                fecha_control, peso, talla,  observaciones, fecha_carga, usuario, id_prestacion, 
                sexo, cod_nomenclador, id_recepcion) 
                VALUES ('" . $l[1] . "','" . $l[5] . "','" . $l[7] . "'," . $l[8] . ",'" . $l[9] . "','" . $l[10] . "','"
                . Fecha_db($l[13], '1899-12-31') . "'," . $l[30] . "," . $l[32] . ",'" . $l[58] . "','" . Fecha_db($adultos["fecha_carga"], '1899-12-31') . "','" .
                $adultos["usuario"] . "'," . $l[4] . ",'" . $l[65] . "','" . str_replace('-', '', $l[12]) . "', " . $var['recepcion_id'] . ")";

        sql($SQLn, "Error al insertar trazadora", 0) or excepcion("Error al insertar trazadora");
        $var['c_a']++;

        $SQLnD = "DELETE FROM trazadoras.adultos_tmp 
            WHERE id_prestacion = $l[4] 
            AND cuie = '$l[1]'";

        sql($SQLnD, "Error al eliminar trazadora temporal", 0) or excepcion("Error al eliminar trazadora temporal");
    }
    if ($l[3] == 38) {
        //TAL
        $tal["fecha_carga"] = date("d/m/Y");
        $tal["usuario"] = $var['id_user'];

        $SQLn = "INSERT INTO trazadoras.tal (cuie,clave,tipo_doc,num_doc,apellido,nombre,fecha_control,
                fecha_carga,usuario,tal,sexo,codnomenclador,id_prestacion,id_recepcion)
                VALUES ('" . $l[1] . "','" . $l[5] . "','" . $l[7] . "'," . $l[8] . ",'" . $l[9] . "','" . $l[10] . "','" . Fecha_db($l[13], '1899-12-31') . "',
                    '" . Fecha_db($tal["fecha_carga"], '1899-12-31') . "','" . $tal["usuario"] . "','" . $l[15] . "','" . $l[65] . "','" . str_replace('-', '', $l[12]) . "'," . $l[4] . ", '" . $var['recepcion_id'] . "')";
        sql($SQLn, "Error al insertar trazadora", 0) or excepcion("Error al insertar trazadora");
        $var['c_t']++;

        $SQLnD = "DELETE FROM trazadoras.tal_tmp 
            WHERE id_prestacion = $l[4] 
            AND cuie = '$l[1]'";

        sql($SQLnD, "Error al eliminar trazadora temporal", 0) or excepcion("Error al eliminar trazadora temporal");
    }
}

function insertarPrestacion($p) {
    $sql_p = "INSERT INTO facturacion.prestacion (id_comprobante, id_nomenclador, cantidad,
            precio_prestacion, id_anexo, peso, tension_arterial, prestacionid) 
            VALUES (" . $p["id_comprobante"] . ", " . $p["id_nomenclador"] . ", " . $p["cantidad"] .
            ", " . $p["precio_prestacion"] . ", " . $p["id_anexo"] . ", " . $p["peso"] .
            ", '" . $p["tension_arterial"] . "', " . $p["prestacionid"] .
            " ) RETURNING id_prestacion";

    $result = sql($sql_p, "Error al insertar prestaci&oacute;n", 0) or excepcion("Error al insertar prestacion");

    return $result->fields['id_prestacion'];
}

// toma el campo del codigo prestacion nuevo que viene del txt
// y separa en los diferentes significados
function desarmarCodigoPrestacion($aux) {
    $aux = split(' ', $aux);
    $cod_nomenclador['categoria_padre'] = $aux[0];
    // no vamos a usar mas $cod_nomenclador['clase'] = substr($aux, 2, 1);
    $cod_nomenclador['profesional'] = $aux[1];
    $cod_nomenclador['codigo'] = $aux[2];
    return $cod_nomenclador;
}

// toma el id del codigo prestacion nuevo
// y enzambla todos los campos en un solo string
function armarCodigoPrestacion($aux) {
    $q = "SELECT * FROM nomenclador.grupo_prestacion
        WHERE id_grupo_prestacion=$aux";
    $result_codigo = sql($q, "Error al consultar grupo_prestacion $aux", 0) or
            excepcion("Error al consultar grupo_prestacion");
    $codigoarmado = $result_codigo->fields['categoria_padre'] . $result_codigo->fields['profesional'] . $result_codigo->fields['codigo'];
    return $codigoarmado;
}

function existeIdTrazadoraTMP(&$l, &$var, $idnom) {
    if ($l[3] == 1)
        $idr = "SELECT tn.clave 
        FROM facturacion.prestacion fp 
        INNER JOIN trazadoras.nino_tmp tn ON (fp.prestacionid = tn.id_prestacion)
        WHERE tn.cuie='$l[1]' 
        AND fp.prestacionid=$l[4]
        AND fp.id_nomenclador='" . $idnom . "'";

    if ($l[3] == 2)
        $idr = "SELECT tn.clave 
        FROM facturacion.prestacion fp 
        INNER JOIN trazadoras.embarazadas_tmp tn 
        ON (fp.prestacionid = tn.id_prestacion)
        WHERE tn.cuie='$l[1]' 
        AND fp.prestacionid=$l[4] 
        AND fp.id_nomenclador='" . $idnom . "'";

    if ($l[3] == 3)
        $idr = "SELECT tn.clave 
        FROM facturacion.prestacion fp 
        INNER JOIN trazadoras.partos_tmp tn 
        ON (fp.prestacionid = tn.id_prestacion) 
        WHERE tn.cuie='$l[1]' 
        AND fp.prestacionid=$l[4] 
        AND fp.id_nomenclador='" . $idnom . "'";

    if ($l[3] == 36)
        $idr = "SELECT tn.cuie FROM facturacion.prestacion fp 
        INNER JOIN trazadoras.adolecentes_tmp tn ON (fp.prestacionid = tn.id_prestacion) 
        WHERE tn.cuie='$l[1]' 
        AND fp.prestacionid=$l[4] 
        AND fp.id_nomenclador='" . $idnom . "'";

    if ($l[3] == 37)
        $idr = "SELECT tn.cuie FROM facturacion.prestacion fp 
        INNER JOIN trazadoras.adultos_tmp tn ON (fp.prestacionid = tn.id_prestacion) 
        WHERE tn.cuie='$l[1]' 
        AND fp.prestacionid=$l[4] 
        AND fp.id_nomenclador='" . $idnom . "'";

    if ($l[3] == 38)
        $idr = "SELECT tn.clave FROM facturacion.prestacion fp 
        INNER JOIN trazadoras.tal tn ON (fp.prestacionid = tn.id_prestacion) 
        WHERE tn.cuie='$l[1]' 
        AND fp.prestacionid=$l[4] 
        AND fp.id_nomenclador='" . $idnom . "'";

    if (!is_null($idr)) {
        $result_idr = sql($idr, "Error al consultar existencia de trazadora temporal $idr", 0) or excepcion("Error al consultar existencia de trazadora temporal");

        if ($result_idr->RecordCount() > 0) {
            $ya_esta = true;
        } else {
            $ya_esta = false;
        }
    }
    return $ya_esta;
}

function actualizarTrazadoraTMP(&$l, &$var) {
    if ($l[3] == 1) {
        /* NI?OS */
        //$var['ni']++;

        $ninios["nino_edad"] = date('d/m/Y', strtotime(date('d-m-Y')) - strtotime($l[11]));
        $ninios["fecha_carga"] = date("d/m/Y");
        $ninios["usuario"] = $var['id_user'];

        $SQLnU = "UPDATE trazadoras.nino_tmp SET cuie = '" . $l[2] . "', clave = '" . $l[5] .
                "', clase_doc = '" . $l[6] . "', tipo_doc = '" . $l[7] . "', num_doc = " . $l[8] .
                ", apellido = '" . $l[9] . "', nombre = '" . $l[10] . "', fecha_nac = '" .
                Fecha_db($l[11], '1899-12-31') . "', fecha_control = '" . Fecha_db($l[13], '1899-12-31') . "', peso = " . $l[30] . ", talla = " . $l[32] .
                ", percen_peso_edad = '" . $l[31] . "', percen_talla_edad = '" . $l[33] .
                "', perim_cefalico = " . $l[34] . ", percen_perim_cefali_edad = '" . $l[35] .
                "', imc = '" . $l[68] . "', percen_peso_talla = '" . $l[36] .
                "', triple_viral = '" . Fecha_db($l[37], '1899-12-31') . "', nino_edad = " . $ninios["nino_edad"] .
                ", observaciones = '" . $l[58] . "', fecha_carga = '" . Fecha_db($ninios["fecha_carga"], '1899-12-31') .
                "', usuario = '" . $ninios["usuario"] . "', fecha_obito = '" . Fecha_db($l[38], '1899-12-31') . "', ncontrolanual = " . $l[39] . ", id_prestacion = " . $l[4] .
                ", sexo = '" . $l[65] . "', municipio = " . $l[66] . ", percentilo_imc = '" . $l[67] .
                "', discapacitado = '" . $l[69] . "', cod_aldea = '" . $l[70] .
                "', descr_aldea = '" . $l[71] . "', calle = '" . $l[72] . "', num_calle = '" . $l[73] .
                "', barrio = '" . $l[74] . "', cod_nomenclador = '" . $l[12] . "', mjs = '" . $var['descripcion_error'] .
                "' , id_recepcion = " . $var['recepcion_id'] . " WHERE id_prestacion = " . $l[4] .
                " and cuie = '" . $l[1] . "' and cod_nomenclador = '" . $l[12] . "'";

        sql($SQLnU, "Error al actualizar trazadora temporal", 0) or excepcion("Error al actualizar trazadora temporal");
        $var['c_n_tmp']++;
    }
    if ($l[3] == 2) {
        /* EMBARAZADAS */
        //$var['em']++;
        /* graba beneficiarios */

        $embarazada["fecha_carga"] = date("d/m/Y");
        $embarazada["usuario"] = $var['id_user'];

        $SQLeU = "UPDATE trazadoras.embarazadas_tmp SET cuie = '" . $l[1] .
                "', clave = '" . $l[5] . "', tipo_doc = '" . $l[7] . "', num_doc = " . $l[8] .
                ", apellido = '" . $l[9] . "', nombre = '" . $l[10] . "', fecha_control = '" .
                Fecha_db($l[13], '1899-12-31') . "', sem_gestacion = " . $l[19] . ", fum = '" .
                Fecha_db($l[29], '1899-12-31') . "', fpp = '" . Fecha_db($l[28], '1899-12-31') .
                "', fpcp = '" . Fecha_db($l[13], '1899-12-31') . "', fecha_carga = '" . Fecha_db($embarazada["fecha_carga"], '1899-12-31') .
                "', usuario = '" . $embarazada["usuario"] . "', antitetanica = '" . $l[51] .
                "', vdrl = '" . $l[23] . "', estado_nutricional = '" . $l[20] .
                "', antitetanica_primera_dosis = '" . Fecha_db($l[21], '1899-12-31') .
                "', antitetanica_segunda_dosis = '" . Fecha_db($l[22], '1899-12-31') .
                "', hiv = '" . Fecha_db($l[24], '1899-12-31') . "', eco = '" . Fecha_db($l[25], '1899-12-31') .
                "', fecha_obito = '" . Fecha_db($l[26], '1899-12-31') .
                "', nro_control_actual = " . $l[27] . ", tension_arterial_maxima = " . $l[59] .
                ", tension_arterial_minima = " . $l[60] . ", altura_uterina = " . $l[61] .
                ", peso_embarazada = " . $l[64] . ", vdrl_fecha = '" . Fecha_db($l[23], '1899-12-31') . "', hiv_fecha = '" . Fecha_db($l[24], '1899-12-31') .
                "', municipio = " . $l[66] . ", discapacitado = '" . $l[69] .
                "', fecha_nacimiento = '" . Fecha_db($l[11], '1899-12-31') .
                "', id_prestacion = " . $l[4] . ", mjs = '" . $var['descripcion_error'] .
                "' , id_recepcion = " . $var['recepcion_id'] . " WHERE id_prestacion = " . $l[4] .
                " and cuie = '" . $l[1] . "'";

        sql($SQLeU, "Error al actualizar trazadora temporal", 0) or excepcion("Error al actualizar trazadora temporal");
        $var['c_e_tmp']++;
    }

    if ($l[3] == 3) {
        /* PARTOS */
        // $var['pa']++;
        /* graba beneficiarios */

        $parto["fecha_carga"] = date("d/m/Y");
        $parto["usuario"] = $var['id_user'];

        $SQLpU = "UPDATE trazadoras.partos_tmp SET cuie = '" . $l[2] . "', clave = '" .
                $l[5] . "', tipo_doc = '" . $l[7] . "', num_doc = " . $l[8] . ", apellido = '" .
                $l[9] . "', nombre = '" . $l[10] . "', fecha_parto = '" . Fecha_db($l[13], '1899-12-31') . "', apgar = " . $l[41] . ", peso = " . $l[40] . ", vdrl = '" . $l[42] .
                "', antitetanica = '" . $l[43] . "', fecha_conserjeria = '" . Fecha_db($l[46], '1899-12-31') . "', observaciones = '" . $l[58] . "', fecha_carga = '" .
                Fecha_db($parto["fecha_carga"], '1899-12-31') . "', usuario = '" . $parto["usuario"] .
                "', obito_bebe = '" . Fecha_db($l[44], '1899-12-31') . "', obito_madre = '" .
                Fecha_db($l[45], '1899-12-31') . "', id_prestacion = " . $l[4] .
                ", obb_desconocido = '" . $l[47] . "', talla_rn = " . $l[62] .
                ", perimcef_rn = " . $l[63] . ", fecha_nacimiento = '" . Fecha_db($l[11], '1899-12-31') . "', discapacitado = '" . $l[69] . "', municipio = " . $l[66] .
                ", mjs = '" . $var['descripcion_error'] . "' , id_recepcion = " . $var['recepcion_id'] .
                " WHERE id_prestacion = " . $l[4] . " AND cuie = '" . $l[1] . "'";
        sql($SQLpU, "Error al actualizar trazadora temporal", 0) or excepcion("Error al actualizar trazadora temporal");
        $var['c_p_tmp']++;
    }

    if ($l[3] == 36) {
        $adolecente["fecha_carga"] = date("d/m/Y");
        $adolecente["usuario"] = $var['id_user'];

        //ADOLECENTES
        $SQLdlU = "UPDATE trazadoras.adolecentes_tmp
                    SET cuie = '" . $l[2] . "', clave = '" . $l[5] .
                "', num_doc = " . $l[8] . ", tipo_doc = '" . $l[7] . "', apellido = '" . $l[9] .
                "', nombre = '" . $l[10] . "', fecha_nac = '" . Fecha_db($l[11], '1899-12-31') .
                "', fecha_control = " . Fecha_db($l[13], '1899-12-31') . ", peso = " . $l[30] . ", observaciones = '" . $l[58] .
                "', talla = '" . $l[32] . "', imc = '" . $l[68] . "', perc_imc_edad = '" . $l[67] .
                "', tamin = '" . $l[59] . "', tamax = '" . $l[60] . "', sexo = '" . $l[65] .
                "', codnomenclador = '" . $l[12] . "', fecha_carga = '" . Fecha_db($adolecente["fecha_carga"], '1899-12-31') .
                "', usuario = '" . $adolecente["usuario"] . "', id_recepcion = " . $var['recepcion_id'] . " 
                    WHERE id_prestacion = " . $l[4] . " AND cuie = '" . $l[1] . "'";
        sql($SQLdlU, "Error al actualizar trazadora", 0) or excepcion("Error al actualizar trazadora");
        $var['c_dl']++;
    }

    if ($l[3] == 37) {
        //ADULTOS
        $adulto["fecha_carga"] = date("d/m/Y");
        $adulto["usuario"] = $var['id_user'];


        $SQLaU = "UPDATE trazadoras.adultos_tmp
                    SET cuie = '" . $l[2] . "', clave = '" . $l[5] .
                "', num_doc = " . $l[8] . ", tipo_doc = '" . $l[7] . "', apellido = '" . $l[9] .
                "', nombre = '" . $l[10] . "', fecha_control = " . Fecha_db($l[13], '1899-12-31') . ", peso = " . $l[30] . "', talla = '" . $l[32] .
                "', sexo = '" . $l[65] . "', tamin = '" . $l[59] . "', tamax = '" . $l[60] . "', observaciones = '" . $l[58] .
                "', fecha_carga = '" . Fecha_db($adulto["fecha_carga"], '1899-12-31') . "', usuario = '" . $adulto["usuario"] .
                "', id_recepcion = " . $var['recepcion_id'] . " 
                    WHERE id_prestacion = " . $l[4] . " AND cuie = '" . $l[1] . "'";
        sql($SQLaU, "Error al actualizar trazadora", 0) or excepcion("Error al actualizar trazadora");
        $var['c_a']++;
    }

    if ($l[3] == 38) {
        //TAL
        $tal["fecha_carga"] = date("d/m/Y");
        $tal["usuario"] = $var['id_user'];

        $SQLtU = "UPDATE trazadoras.tal_tmp
                    SET cuie = '" . $l[2] . "', clave = '" . $l[5] . "', tipo_doc = '" . $l[7] .
                "', num_doc = " . $l[8] . ", apellido = '" . $l[9] .
                "', nombre = '" . $l[10] . "', fecha_control = '" . Fecha_db($l[13], '1899-12-31') .
                "', sexo = '" . $l[65] . "', tal = '" . $l[15] . "', codnomenclador = '" . $l[12] .
                "', fecha_carga = '" . Fecha_db($adulto["fecha_carga"], '1899-12-31') . "', usuario = '" . $adulto["usuario"] .
                "', id_recepcion = " . $var['recepcion_id'] . " 
                    WHERE id_prestacion = " . $l[4] . " AND cuie = '" . $l[1] . "'";
        sql($SQLtU, "Error al actualizar trazadora", 0) or excepcion("Error al actualizar trazadora");
        $var['c_t']++;
    }
}

function insertarTrazadoraTMP(&$l, &$var) {
    if ($l[3] == 1) {
        /* NI?OS */
        //$var['ni']++;
        /* graba beneficiarios */

        $ninios["nino_edad"] = date('d/m/Y', strtotime(date('d-m-Y')) - strtotime($l[11]));
        $ninios["fecha_carga"] = date("d-m-Y");
        $ninios["usuario"] = $var['id_user'];

        $SQLn = "INSERT INTO trazadoras.nino_tmp (cuie, clave, clase_doc, tipo_doc, num_doc,
            apellido, nombre, fecha_nac, fecha_control, peso, talla, percen_peso_edad, percen_talla_edad,
            perim_cefalico, percen_perim_cefali_edad, imc, percen_peso_talla, triple_viral, nino_edad,
            observaciones, fecha_carga, usuario, fecha_obito, ncontrolanual, id_prestacion, sexo, municipio,
            percentilo_imc, discapacitado, cod_aldea, descr_aldea, calle, num_calle, barrio, cod_nomenclador, mjs, id_recepcion)
            VALUES ('" . $l[2] . "','" . $l[5] . "','" . $l[6] . "','" . $l[7] . "'," . $l[8] . ",'" . $l[9] .
                "','" . $l[10] . "','" . Fecha_db($l[11], '1899-12-31') . "','" . Fecha_db($l[13], '1899-12-31') .
                "'," . $l[30] . "," . $l[32] . ",'" . $l[31] . "','" . $l[33] . "'," . $l[34] . ",'" . $l[35] .
                "','" . $l[68] . "','" . $l[36] . "','" . Fecha_db($l[37], '1899-12-31') . "'," . $ninios["nino_edad"] .
                ",'" . $l[58] . "','" . Fecha_db($ninios["fecha_carga"], '1899-12-31') . "','" . $ninios["usuario"] .
                "','" . Fecha_db($l[38], '1899-12-31') . "'," . $l[39] . "," . $l[4] . ",'" . $l[65] . "'," . $l[66] .
                ",'" . $l[67] . "','" . $l[69] . "','" . $l[70] . "','" . $l[71] . "','" . $l[72] . "','" . $l[73] .
                "','" . $l[74] . "','" . str_replace('-', '', $l[12]) . "','" . $var['descripcion_error'] . "', " . $var['recepcion_id'] . ")";

        sql($SQLn, "Error al insertar trazadora temporal", 0) or excepcion("Error al insertar trazadora temporal");
        $var['c_n_tmp']++;
    }
    if ($l[3] == 2) {
        /* EMBARAZADAS */
        //$var['em']++;
        /* graba beneficiarios */

        $embarazada["fecha_carga"] = date("d/m/Y");
        $embarazada["usuario"] = $var['id_user'];

        $SQLe = "INSERT INTO trazadoras.embarazadas_tmp (cuie, clave, tipo_doc, num_doc, apellido, nombre, fecha_control, sem_gestacion, fum, fpp, fpcp, fecha_carga, usuario,
            antitetanica, vdrl, estado_nutricional, antitetanica_primera_dosis, antitetanica_segunda_dosis, hiv, eco, fecha_obito, nro_control_actual, tension_arterial_maxima, tension_arterial_minima, altura_uterina, peso_embarazada, vdrl_fecha, hiv_fecha, municipio, discapacitado, fecha_nacimiento, id_prestacion, mjs, id_recepcion) VALUES ('" .
                $l[1] . "','" . $l[5] . "','" . $l[7] . "'," . $l[8] . ",'" . $l[9] . "','" . $l[10] .
                "','" . Fecha_db($l[13], '1899-12-31') . "'," . $l[19] . ",'" . Fecha_db($l[29], '1899-12-31') . "','" . Fecha_db($l[28], '1899-12-31') . "','" . Fecha_db($l[13], '1899-12-31') . "','" . Fecha_db($embarazada["fecha_carga"], '1899-12-31') .
                "','" . $embarazada["usuario"] . "','"
                . $l[51] . "','" . Fecha_db($l[23], '1899-12-31') . "','" . $l[20] . "','" . Fecha_db($l[21], '1899-12-31') . "','" .
                Fecha_db($l[22], '1899-12-31') . "','" . Fecha_db($l[24], '1899-12-31') . "','" .
                Fecha_db($l[25], '1899-12-31') . "','" . Fecha_db($l[26], '1899-12-31') . "'," .
                $l[27] . "," . $l[59] . "," . $l[60] . "," . $l[61] . "," . $l[64] . ",'" .
                Fecha_db($l[23], '1899-12-31') . "','" . Fecha_db($l[24], '1899-12-31') . "'," .
                $l[66] . ",'" . $l[69] . "','" . Fecha_db($l[11], '1899-12-31') . "'," . $l[4] .
                ",'" . $var['descripcion_error'] . "', " . $var['recepcion_id'] . ")";

        sql($SQLe, "Error al insertar trazadora temporal", 0) or excepcion("Error al insertar trazadora temporal");
        $var['c_e_tmp']++;
    }

    if ($l[3] == 3) {
        /* PARTOS */
        $var['pa']++;
        /* graba beneficiarios */

        $parto["fecha_carga"] = date("d/m/Y");
        $parto["usuario"] = $var['id_user'];

        $SQLp = "INSERT INTO trazadoras.partos_tmp (cuie, clave, tipo_doc, num_doc, apellido, nombre, fecha_parto,
            apgar, peso, vdrl, antitetanica, fecha_conserjeria, observaciones, fecha_carga, usuario, obito_bebe, 
            obito_madre, id_prestacion, obb_desconocido, talla_rn, perimcef_rn, fecha_nacimiento, discapacitado, municipio, mjs, id_recepcion)
            VALUES ('" . $l[2] . "','" . $l[5] . "','" . $l[7] . "'," . $l[8] . ",'" . $l[9] . "','" . $l[10] .
                "','" . Fecha_db($l[13], '1899-12-31') . "'," . $l[41] . "," . $l[40] . ",'" . $l[42] .
                "','" . $l[43] . "','" . Fecha_db($l[46], '1899-12-31') . "','" . $l[58] . "','" .
                Fecha_db($parto["fecha_carga"], '1899-12-31') . "','" . $parto["usuario"] .
                "','" . Fecha_db($l[44], '1899-12-31') . "','" . Fecha_db($l[45], '1899-12-31') .
                "'," . $l[4] . ",'" . $l[47] . "'," . $l[62] . "," . $l[63] . ",'" . Fecha_db($l[11], '1899-12-31') .
                "','" . $l[69] . "'," . $l[66] . ",'" . $var['descripcion_error'] .
                "', " . $var['recepcion_id'] . ")";

        sql($SQLp, "Error al insertar trazadora temporal", 0) or excepcion("Error al insertar trazadora temporal");
        $var['c_p_tmp']++;
    }

    if ($l[3] == 36) {
        //ADOLECENTES
        $adolecente["fecha_carga"] = date("d/m/Y");
        $adolecente["usuario"] = $var['id_user'];


        $SQLn = "INSERT INTO trazadoras.adolecentes_tmp
             (cuie,clave,tipo_doc,num_doc,apellido,nombre,fecha_nac,fecha_control,peso,talla,
              imc,percen_imc_edad,tamin,tamax,observaciones,fecha_carga,usuario,sexo,codnomenclador,id_prestacion,id_recepcion)
              VALUES ('" . $l[1] . "','" . $l[5] . "','" . $l[7] . "'," . $l[8] . ",'" . $l[9] . "','" . $l[10] . "','" . Fecha_db($l[11], '1899-12-31') . "','"
                . Fecha_db($l[13], '1899-12-31') . "'," . $l[30] . ",'" . $l[32] . " ','" .
                $l[68] . "','" . $l[67] . "','" . $l[59] . "','" . $l[60] . "','" . $l[58] . "','" . Fecha_db($adolecente["fecha_carga"], '1899-12-31') . "','" .
                $adolecente["usuario"] . "','" . $l[65] . "','" . $l[12] . "'," . $l[4] . ", '" . $var['recepcion_id'] . "')";
        sql($SQLn, "Error al insertar trazadora", 0) or excepcion("Error al insertar trazadora");
        $var['c_dl_tmp']++;
    }

    if ($l[3] == 37) {
        //ADULTOS
        $adultos["fecha_carga"] = date("d/m/Y");
        $adultos["usuario"] = $var['id_user'];

        $SQLn = "INSERT INTO trazadoras.adultos_tmp (cuie, clave, tipo_doc, num_doc, apellido, nombre, 
                fecha_control, peso, talla,  observaciones, fecha_carga, usuario, id_prestacion, 
                sexo, cod_nomenclador, id_recepcion) 
                VALUES ('" . $l[1] . "','" . $l[5] . "','" . $l[7] . "'," . $l[8] . ",'" . $l[9] . "','" . $l[10] . "','"
                . Fecha_db($l[13], '1899-12-31') . "'," . $l[30] . "," . $l[32] . ",'" . $l[58] . "','" . Fecha_db($adultos["fecha_carga"], '1899-12-31') . "','" .
                $adultos["usuario"] . "'," . $l[4] . ",'" . $l[65] . "','" . str_replace('-', '', $l[12]) . "', " . $var['recepcion_id'] . ")";

        sql($SQLn, "Error al insertar trazadora", 0) or excepcion("Error al insertar trazadora");
        $var['c_a_tmp']++;
    }

    if ($l[3] == 38) {
        //TAL
        $tal["fecha_carga"] = date("d/m/Y");
        $tal["usuario"] = $var['id_user'];

        $SQLn = "INSERT INTO trazadoras.tal_tmp (cuie,clave,tipo_doc,num_doc,apellido,nombre,fecha_control,
                fecha_carga,usuario,tal,sexo,codnomenclador,id_prestacion,id_recepcion)
                VALUES ('" . $l[1] . "','" . $l[5] . "','" . $l[7] . "'," . $l[8] . ",'" . $l[9] . "','" . $l[10] . "','" . Fecha_db($l[13], '1899-12-31') . "',
                    '" . Fecha_db($tal["fecha_carga"], '1899-12-31') . "'," . $tal["usuario"] . "," . $l[15] . ",'" . $l[65] . "','" . str_replace('-', '', $l[12]) . "'," . $l[4] . ", '" . $var['recepcion_id'] . "')";
        sql($SQLn, "Error al insertar trazadora", 0) or excepcion("Error al insertar trazadora");
        $var['c_t_tmp']++;
    }
}

function montoFactura($id_factura) {
    $query_1 = "SELECT sum(p.precio_prestacion*p.cantidad) as total
                       FROM    facturacion.factura f
                       INNER JOIN facturacion.comprobante c ON (f.id_factura = c.id_factura)
                       INNER JOIN facturacion.prestacion p ON (c.id_comprobante = p.id_comprobante)
                       WHERE f.id_factura=" . $id_factura . "
                           AND c.id_comprobante not in(select id_comprobante from facturacion.debito where id_factura=$id_factura)";
    $monto_prefactura_1 = sql($query_1) or excepcion('Error al calcular el total liquidado');
    $monto_prefactura_1 = $monto_prefactura_1->fields['total'];

    ($monto_prefactura_1 == '') ? $monto_prefactura_1 = 0 : $monto_prefactura_1 = $monto_prefactura_1;

    $query_2 = "SELECT sum(p.precio_prestacion*p.cantidad) as total
                       FROM    facturacion.factura f
                       INNER JOIN facturacion.comprobante c ON (f.id_factura = c.id_factura)
                       INNER JOIN facturacion.prestacion p ON (c.id_comprobante = p.id_comprobante)
                       WHERE f.id_factura='" . $id_factura . "'";
    $monto_prefactura_2 = sql($query_2) or excepcion('Error al calcular el total liquidado');
    $monto_prefactura_2 = $monto_prefactura_2->fields['total'];

    $monto_prefactura['aceptado'] = $monto_prefactura_1; //+ $monto_prefactura_2;
    $monto_prefactura['total'] = $monto_prefactura_2;

    return $monto_prefactura;
}

function actualizarMontoFactura($id_factura, $monto_prefactura_total) {
    if (is_null($monto_prefactura_total) || ($monto_prefactura_total == 0)) {
        $monto_prefactura_total = 0;
    }

    $query = "UPDATE facturacion.factura
                                            SET	monto_prefactura=" . $monto_prefactura_total . "
                                            WHERE id_factura=" . $id_factura;
    sql($query, 'Error al calcular el total liquidado', 1) or excepcion('Error al calcular el total liquidado');
}

function cerrarFactura($id_factura) {
    $monto_prefactura_total = montoFactura($id_factura);
    actualizarMontoFactura($id_factura, $monto_prefactura_total['total']);
    $query = "UPDATE facturacion.factura
                                            SET	estado='C'
                                            WHERE id_factura=" . $id_factura;
    sql($query, 'Error al calcular el total liquidado', 1) or excepcion('Error al calcular el total liquidado');
}

function facturavacia($id_factura) {
    $vacio = false;
    $e_sql = "SELECT id_comprobante FROM facturacion.comprobante
                        WHERE id_factura = '$id_factura'";
    $e_busqueda = sql($e_sql);
    if ($e_busqueda->RecordCount() == 0) {
        $vacio = true;
    }
    return $vacio;
}

function finProcesoFactura($idrecepcion, $lineas) {
    $fin_proc = date("Y-m-d H:i:s");
    $query = "UPDATE facturacion.recepcion
                                            SET	fin_proc='$fin_proc',
                                            lineas_proc='$lineas'
                                            WHERE idrecepcion=" . $idrecepcion;
    sql($query, 'Error al calcular el total liquidado', 1) or excepcion('Error al calcular el total liquidado');
}

function obtenerFactura($factura, $datos) {
    $mes_nombre = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
    $factura["cuie"] = $primera_linea[1];
    $factura["periodo"] = $primera_linea[2];
    $factura["estado"] = "A";
    $factura["fecha_carga"] = date("d/m/Y");
    $factura["fecha_factura"] = date("d/m/Y", strtotime($primera_linea[5]));
    $mes = split("/", $primera_linea[2]);
    $factura["mes_fact_d_c"] = $mes_nombre[intval($mes[1])] .
            " " . $mes[0];
    $factura["online"] = "NO";
    return $factura;
}

function obtenerTipoNomencladorTXT($nro_tipo_nomenclador) {
    switch ($nro_tipo_nomenclador) {
        case 00:
            return "-";
            break;
        case 01:
            return "BASICO";
            break;
        case 02:
            return "-";
            break;
    }
    return false;
}

function existeFactura($f, $cuie, $nro_exp) {
    $sql = "SELECT nro_fact_offline, nro_exp 
    FROM facturacion.factura 
    WHERE nro_fact_offline = '$f'
    AND cuie='$cuie'
    AND nro_exp='$nro_exp'";
    $result = sql($sql) or excepcion('Error al buscar factura repetida');
    //print $sql;
    if ($result->RecordCount() > 0) {
        return $result;
    }
    return false;
}

function existeRecepcion($nombre_archivo) {
    $e_sql = "SELECT idrecepcion FROM facturacion.recepcion
                        WHERE nombrearchivo = '$nombre_archivo'";
    $e_busqueda = sql($e_sql, "Error al buscar archivo ya cargado") or excepcion("Error al buscar archivo ya cargado", 0);
    if ($e_busqueda->RecordCount() > 0) {
        excepcion('El archivo ya est&aacute; cargado en el sistema.');
    }
    return true;
}

function insertarRecepcion($nombre_archivo, $e_cod_org, $e_no_correlativo, $e_ano_exp) {
    $fecha_rec = date("Y-m-d");
    $inicio_proc = date("Y-m-d H:i:s");
    $e_sql = "INSERT INTO facturacion.recepcion
                            (nombrearchivo, cod_org, no_correlativo, ano_exp, fecha_rec,inicio_proc) 
                            VALUES ('$nombre_archivo', $e_cod_org, $e_no_correlativo, $e_ano_exp,'$fecha_rec','$inicio_proc')
                            RETURNING idrecepcion";
    $result_recepcion = sql($e_sql, "Error al insertar archivo.", 0) or excepcion('Error al insertar archivo.');
    if ($result_recepcion->RecordCount() > 0) {
        $result_recepcion->movefirst();
        return $result_recepcion->fields['idrecepcion'];
    }
}

function insertarFactura($factura) {
    $f_sql = "INSERT INTO facturacion.factura (cuie, periodo, estado, fecha_carga, fecha_factura,
        mes_fact_d_c, nro_exp, online, nro_exp_ext, nro_fact_offline, recepcion_id, fecha_entrada, periodo_actual, ctrl,
        tipo_liquidacion,tipo_nomenclador) 
        VALUES ('" . $factura['cuie'] . "', '" . $factura['periodo'] . "', 'A', '" . Fecha_db($factura["fecha_carga"]) .
            "', '" . Fecha_db($factura["fecha_factura"]) . "', '" . $factura["mes_fact_d_c"] .
            "', '" . $factura["nro_exp"] . "', 'NO', '" . $factura["nro_exp"] . "', '" . $factura["nro_fact_offline"] .
            "', " . $factura["recepcion_id"] . ", '" . Fecha_db($factura["fecha_entrada"]) . "' , '" . $factura['periodo'] . "','" . N . "','"
            . $factura['tipo_liquidacion'] . "','" . $factura['tipo_nomenclador'] . "')               RETURNING id_factura";
    $result = sql($f_sql, "", 0) or excepcion('Error al insertar la factura');
    return $result->fields['id_factura'];
}

function insertarExpediente($nroexpediente, $cuie, $fecha_entrada, $usuario) {
    $fechamodificacion = date('Y-m-d');
    $sqlsiyaesta = "SELECT * FROM facturacion.expediente WHERE nro_exp='$nroexpediente'";
    $resultsiyaesta = sql($sqlsiyaesta);
    if ($resultsiyaesta->RecordCount() == 0) {
        $sql = "INSERT INTO facturacion.expediente (nro_exp,estado,iniciador,fecha_entrada,fecha_modificacion,usuario) 
            VALUES ('$nroexpediente','A','$cuie','$fecha_entrada','$fechamodificacion',$usuario)";
        $result = sql($sql, "", 0) or excepcion('Error al insertar el expediente');
    }
}

function excepcion($m) {
    throw new Exception($m);
}

function buscarNomencladorPorId($codigo_id, $id_vigencia) {
    if ($id_vigencia < 11) {
        $modo_facturacion = "viejo";
        $sql_n = "SELECT * 
              FROM facturacion.nomenclador 
              WHERE id_nomenclador = '$codigo_id'";
        $result_n = sql($sql_n, "No se encuentra nomenclador con cod: $cod_nom", 0);
        $codigo = $result_n->fields['codigo'];
    } else {
        $modo_facturacion = "nuevo";
        $sql_n = "SELECT id_grupo_prestacion as id_nomenclador, precio,categoria_padre,codigo 
              FROM nomenclador.grupo_prestacion 
              WHERE id_grupo_prestacion = '$codigo_id'";
        $result_n = sql($sql_n, "No se encuentra nomenclador con cod: $cod_nom", 0);
        $codigo = armarCodigoPrestacion($codigo_id);
    }


    $datos_nomenclador[0] = $codigo_id;
    $datos_nomenclador[3] = $codigo;
    $datos_nomenclador[1] = $result_n->fields['precio'];
    $datos_nomenclador[2] = $modo_facturacion;
    return $datos_nomenclador;
}

function buscarNomenclador($id_vigencia, $cod) {
    $cod_a = explode('-', $cod);
    $nro_orden = intval($cod_a[1]);
    $codigo = $cod_a[0];
    if ($id_vigencia['modo'] < 2) {
        $modo_facturacion = "viejo";
        $sql_n = "SELECT id_nomenclador, precio, categoria 
              FROM facturacion.nomenclador 
              WHERE codigo = '$codigo' 
              AND id_nomenclador_detalle = " . $id_vigencia['id'];
    } else {
        $codigo = desarmarCodigoPrestacion($codigo);
        $modo_facturacion = "nuevo";
        $sql_n = "SELECT id_grupo_prestacion as id_nomenclador, precio 
              FROM nomenclador.grupo_prestacion 
              WHERE codigo = '" . $codigo['codigo'] . "'
              AND categoria_padre = '" . $codigo['categoria_padre'] . "'
              AND id_nomenclador_detalle = " . $id_vigencia['id'];
    }
    $result_n = sql($sql_n, "No se encuentra nomenclador con cod: $cod_nom", 0);

    $datos_nomenclador[0] = $result_n->fields['id_nomenclador'];
    $datos_nomenclador[1] = $result_n->fields['precio'];
    $datos_nomenclador[2] = $modo_facturacion;
    $datos_nomenclador[3] = $codigo;
    $datos_nomenclador[4] = $result_n->fields['categoria'];
    return $datos_nomenclador;
}

function obtenerTablaTrazadora($tipo_informe) {
    switch ($tipo_informe) {
        case 1:
            return "nino";
            break;
        case 2:
            return "embarazadas";
            break;
        case 3:
            return "partos";
            break;
        case 14:
            return "mu";
            break;
    }
    return false;
}

function insertarComprobante($l, &$var, $c, $vigencia) {

    //$id_vigencia = $var['id_nomenclador'][6];
    $tipo_nomenclador = $var['id_nomenclador'];
    if ($var['existe_id'] == 'no' /* && $var['error_datos'] == 'no' */) {
        $var['cuenta_procesado']++;
        $sql_c = "INSERT INTO facturacion.comprobante (cuie, id_factura, nombre_medico, fecha_comprobante, 
            clavebeneficiario, id_smiafiliados, fecha_carga, periodo, id_servicio, activo, idvacuna, idprestacion,id_nomenclador_detalle,idperiodo,grupo_etario,tipo_nomenclador,usuario)
            VALUES ('" . $c["cuie"] . "', " . $c["id_factura"] . ", '" . $c["nombre_medico"] . "', '" .
                Fecha_db($c["fecha_comprobante"]) . "', '" . $c["clave_beneficiario"] . "', '" .
                $c["id_smiafiliado"] . "', '" . Fecha_db($c["fecha_carga"]) . "', '" . $c["periodo"] .
                "', " . $c["id_servicio"] . ", '" . $c["activo"] . "', " . $c["idvacuna"] . ", " .
                $l[4] . ",'" . $vigencia['id'] . "'," . $c['id_periodo'] . ",'" . $var['grupo_etario']['categoria'] . "','" . $c["tipo_nomenclador"] . "','" . $var['id_user'] . "') RETURNING id_comprobante";
        $result = sql($sql_c, "Error al insertar comprobante ", 0) or excepcion("Error al insertar comprobante");
        $var['idbenefrecepcion'] = $result->fields['id_comprobante'];
    }

    if ($var['error_datos'] == 'si') {
        $var['cuenta_procesado']++;
        $sql_c = "INSERT INTO facturacion.comprobante (cuie, id_factura, nombre_medico, fecha_comprobante, clavebeneficiario,
                id_smiafiliados, fecha_carga, periodo, id_servicio, activo, idvacuna, mensaje, fila,usuario)
                VALUES ('" . $c["cuie"] . "', " . $c["id_factura"] . ", '" . $c["nombre_medico"] . "', '" .
                Fecha_db($c["fecha_comprobante"]) . "',  '" . Fecha_db($c["fecha_comprobante"]) .
                "',  " . $c["id_smiafiliado"] . ", '" . Fecha_db($c["fecha_carga"]) . "',  '" . $c["periodo"] .
                "', " . $c["id_servicio"] . ", '" . $c["activo"] . "', " . $c["idvacuna"] .
                ", '" . $var['mjs_id'] . "', '" . $var['row'] . "','" . $var['id_user'] . "' ) RETURNING id_comprobante";

        $result = sql($sql_c, "Error al insertar comprobante ", 0) or excepcion("Error al insertar comprobante");
        $var['idbenefrecepcion'] = $result->fields['id_comprobante'];
        //$var['existe_id'] = 'no';
    }
    return $var['idbenefrecepcion'];
}

function practicaEsHabilitada($convenio, $datosnomenclador) {
    $ctrl['debito'] = false;
    // $convenio = buscarConvenioNomenclador($l);
    //$datosnomenclador = obtenerIdNomenclador($l[12], $l, $var);
    if (($datosnomenclador[0] == null) || ($datosnomenclador[0] == 0)) {
        $datosnomenclador[1] = 0;
        $ctrl['msj_error'] = 'No puede Facturar este c&oacute;digo.';
        $ctrl['id_error'] = 61;
        $ctrl['debito'] = true;
        return $ctrl;
    }
    $sql = "SELECT id_excluidos FROM nacer.excluidos
                INNER JOIN facturacion.nomenclador on (cod_practica=id_nomenclador)
                inner join nacer.conv_nom using(id_conv_nom)
                WHERE id_efe_conv='$convenio'
                AND id_nomenclador='" . $datosnomenclador[0] . "'";

    $result = sql($sql, "", 0);
    if ($result->RecordCount() > 0) {
        $ctrl['msj_error'] = 'No puede Facturar este c&oacute;digo.';
        $ctrl['id_error'] = 61;
        $ctrl['debito'] = true;
    }
    return $ctrl;
}

function buscarConvenio($cuie, $fechaprestacion) {
    $fechaprestacion = strtotime(Fecha_db($fechaprestacion));
    $sql = "SELECT to_char(fecha_comp_ges, 'DD-MM-YYYY') fecha_comp_ges,
                to_char(fecha_fin_comp_ges, 'DD-MM-YYYY') fecha_fin_comp_ges,
                id_efe_conv
                FROM nacer.efe_conv
                WHERE cuie='$cuie'
                ORDER BY fecha_modificacion desc";
    $result = sql($sql, "", 0);

    if ($result->RecordCount() > 0) {
        $result->MoveFirst();
        while (!$result->EOF) {
            $fecha_comp_ges = strtotime($result->fields['fecha_comp_ges']);
            $fecha_fin_comp_ges = strtotime($result->fields['fecha_fin_comp_ges']);
            if (($fecha_comp_ges <= $fechaprestacion) && ($fechaprestacion <= $fecha_fin_comp_ges)) {
                $convenio = $result->fields['id_efe_conv'];
                return $convenio;
            }
            $result->MoveNext();
        }
    }
    return null;
}

function buscarConvenioNomenclador($l) {
    $id_efe_conv = buscarConvenio($l);
    $sql = "SELECT * FROM nacer.conv_nom
                WHERE id_efe_conv='$id_efe_conv'
                AND activo=true";
    $result = sql($sql, "", 0);
    $convenio = $result->fields['id_conv_nom'];
    return $convenio;
}

function existeEfector($nombre_archivo) {
    $cuie = substr($nombre_archivo, 0, 6);
    $sq = "SELECT nombreefector
                FROM facturacion.smiefectores
                WHERE cuie='$cuie'";
    $resul = sql($sq);
    if ($resul->RecordCount() > 0) {
        return true;
    }
    return false;
}

function insertarInformado(&$l, &$var) {
//$SQL = "insert into facturacion.informados (idRecepcion, cuie, idPrestacion, claveBeneficiario, codNomenclador, tipoDoc, nroDoc, nombre, apellido, fechaNac, fechaactual, idvacuna, idtaller, km, origen, destino, clavemadre, sexo, municipio, semgesta, discapacitado, clasedoc) values ('$idRecepcion', '$data[1]', '$data[4]', '$claveBeneficiario', '$data[12]', '$tipoDoc', '$nroDoc', '$nombre', '$apellido', '$fechaNac', '$data[13]', '$idvacuna', '$idtaller', '$km', '$origen', '$destino', '$clavemadre', '$sexo', '$municipio', '$semgesta', '$discapacitado', '$claseDoc')";
    if ($l[15] == null || $l[15] == '') {
        $l[15] = 0;
    }

    $SQL = "insert into facturacion.informados (idrecepcion, cuie, idprestacion, clavebeneficiario,
                codnomenclador, tipodoc, nrodoc, nombre, apellido, fechanac, fechaactual, idvacuna,
                idtaller, km, origen, destino, clavemadre, sexo, municipio, semgesta, discapacitado, clasedoc)
                values (" . $var['recepcion_id'] . ", '" . $l[1] . "', " . $l[4] . ", '" . $l[5] . "', '" .
            str_replace(' - ', ' ', $l[12]) . "', '" . $l[7] . "', '" . $l[8] . "', '" . $l[10] . "', '" . $l[9] .
            "', '" . Fecha_db($l[11], ' 1899-12-31') . "', '" . Fecha_db($l[13], ' 1899-12-31') .
            "', " . $var['idvacuna'] . ", " . $l[15] . ", " . $l[15] . ", '" . $l[16] .
            "', '" . $l[17] . "', '" . $l[18] . "', '" . $l[65] . "', " . $l[66] . ", " . $l[53] .
            ", '" . $l[69] . "', '" . $l[6] . "' ) ";

    sql($SQL, 'Error al insertar informado', 0) or excepcion('Error al insertar informado');
}

function calcular_limite_fecha_prestacion($mes_vig, $ano_vig) {
// $fcierre = '01/' . $mes_vig . '/' . $ano_vig;
    if ($mes_vig == '12') {
        $mes_vig1 = '01';
        $ano_vig1 = $ano_vig + 1;
    }
    if ($mes_vig != '12') {
        $mes_vig1 = $mes_vig + 1;
        $ano_vig1 = $ano_vig;
        if ($mes_vig1 < 10) {
            $mes_vig1 = '0' . $mes_vig1;
        }
    }
    return '10/' . $mes_vig1 . '/' . $ano_vig1;
}

function validarFormularioRecepcion($post, &$var) {
//validar codigo organizacion
    if (!es_numero($post["cod_org"])) {
        $var['error_formulario'] = "C&oacute;
                digo de Organizaci&oacute;
                n no v&aacute;
                lido";
        return false;
    }
//validar nro correlativo
    if (!es_numero($post["no_correlativo"])) {
        $var['error_formulario'] = "Nï¿½mero correlativo no vï¿½lido";
        return false;
    }
//validar a?o
    if (!es_numero($post["ano_exp"])) {
        $var['error_formulario'] = "Aï¿½o no vï¿½lido";
        return false;
    }
//validar fecha de entrada
    if (!FechaOk($post["fecha_entrada"])) {
        $var['error_formulario'] = "Fecha de entrada no vï¿½lida";
        return false;
    }
    return true;
}

function buscarAnexosPorId($id_anexo) {
    $sql = "select * from
                facturacion.anexo
                where id_anexo=$id_anexo";
    $anexo = sql($sql, "Error al buscar anexos") or fin_pagina();
    if ($anexo->recordCount() == 0) {
        $anexo = null;
    }
    return $anexo;
}

function dias_time($dias) {
    $segundosxdia = 60 * 60 * 24;
    $segundoscalculados = $dias * $segundosxdia;
    return $segundoscalculados;
}

function practicasRelacionadas($clavebeneficiario, $fecha_comprobante, $datosnomenclador) {

    /*
     *   busca todas las reglas para este nomenclador
     */
    $query = "SELECT *
                FROM facturacion.cfg_practicas_relac
                WHERE modo=2 and 
                trim(pracorigen)=trim('$datosnomenclador[3]')";
    $res_origen = sql($query, "Error 1") or fin_pagina();
    if ($res_origen->RecordCount() > 0) {
        $res_origen->MoveFirst();
        while (!$res_origen->EOF) {
            $nomencladordetalles['tipo'] = $datosnomenclador[2];
            $nomencladordetalles['codigo'] = $res_origen->fields['pracrel'];

            $fechadelarelacionada = traemeLaPractica($clavebeneficiario, $nomencladordetalles, $fecha_comprobante);
            if ($fechadelarelacionada != null) {
                $limite_dias = $res_origen->fields['dias'];
                $diff = GetCountDaysBetweenTwoDates($fecha_comprobante, $fechadelarelacionada);
                if ($diff > $limite_dias) {
                    $ctrl['debito'] = true;
                    $ctrl['msj_error'] .= 'No se realizo la practica relacionada [' . $nomencladordetalles['codigo'] . '] dentro del limite de tiempo';
                    $ctrl['id_error'] = '76';
                }
            } else {
                $ctrl['debito'] = true;
                $ctrl['msj_error'] .= 'No se realizo la practica relacionada [' . $nomencladordetalles['codigo'] . ']';
                $ctrl['id_error'] = '75';
            }
            $res_origen->MoveNext();
        }
        return $ctrl;
    }
}

function controlPracticasRelacionadas($clavebeneficiario, $fecha_comprobante, $datosnomenclador) {
    /*
     *   busca todas las reglas para este nomenclador
     */

    $ctrl['debito'] = false;
    $query = "SELECT *
                FROM facturacion.cfg_practicas_relac
                WHERE modo=1 and
                trim(pracorigen)=trim('$datosnomenclador[3]')";
    $res_origen = sql($query, "Error 1") or fin_pagina();

    if ($res_origen->RecordCount() > 0) {
        $ctrl['msj_error'] = "";
        $res_origen->MoveFirst();
        while (!$res_origen->EOF) {
            $anexo = $res_origen->fields['anexopracrel'];
            $nomencladordetalles['tipo'] = $datosnomenclador[2];
            $nomencladordetalles['codigo'] = $res_origen->fields['pracrel'];
            //busca si ese afiliado se realiza la practica relacionada
            $comprobantedelarelacionada = traemeLaPractica($clavebeneficiario, $nomencladordetalles, $fecha_comprobante, $anexo);
            if ($comprobantedelarelacionada != null) {
                $limite_dias = $res_origen->fields['dias'];
                $limite_dias_time = dias_time($limite_dias);
                $fecha_comprobante_time = strtotime(date($fecha_comprobante));
                $comprobantedelarelacionada_time = strtotime(date($comprobantedelarelacionada));
                $resta_time = $fecha_comprobante_time - $comprobantedelarelacionada_time;
                if ($resta_time <= $limite_dias_time) {
                    //una vez que esta comprobado que se realizo la practica dentro de los 30 dias,
                    //comprueba si existe otra condicion, o si no es obligatoria y sale. 
                    //TODO: no existe while para otras practicas obligatorias
                    if (($res_origen->fields['otras'] == 'N') || ($res_origen->fields['tipo'] == 'OR')) {
                        $ctrl['debito'] = false;
                        break;
                    }
                } else {
                    if ($ctrl['msj_error'] != "") {
                        $ctrl['msj_error'].=" y ";
                    }
                    $ctrl['debito'] = true;
                    $ctrl['msj_error'] .= 'No se realizo la practica relacionada [' . $nomencladordetalles['codigo'] . '] dentro del limite de tiempo';
                    $ctrl['id_error'] = '76';
                    $ctrl['nomenclador_rel'] = $nomencladordetalles['codigo'];
                    if ($res_origen->fields['tipo'] == 'AND')
                        break;
                }
            } else {
                if ($ctrl['msj_error'] != "") {
                    $ctrl['msj_error'].=" y ";
                }
                $ctrl['debito'] = true;
                $ctrl['msj_error'] .= 'No se realizo la practica relacionada [' . $nomencladordetalles['codigo'] . ']';
                $ctrl['id_error'] = '75';
                $ctrl['nomenclador_rel'] = $nomencladordetalles['codigo'];
                if ($res_origen->fields['tipo'] == 'AND')
                    break;
            }
            $res_origen->MoveNext();
        }
    }
    return $ctrl;
}

function controlVacunacion($nomenclador, $dniafi, $diasvida) {
    $control['resultado'] = true;
    $codigonomenclador = str_replace(" ", "", $nomenclador[3]);
    if (($codigonomenclador == 'NPE32' || $codigonomenclador == 'NPE33') && $diasvida > 390) {
        $querympe1 = "SELECT * FROM trazadoras.nino_new
                WHERE num_doc='$dniafi'
                AND triple_viral<>'1899-12-31' AND triple_viral IS NOT NULL";
        $res_funmpe1 = sql($querympe1) or fin_pagina();
        $querympe2 = "SELECT * FROM trazadoras.trz_antisarampionosa
                WHERE numero_documento='$dniafi'
                AND fecha_vacunacion<>'1899-12-31'
                AND fecha_vacunacion IS NOT NULL";
        $res_funmpe2 = sql($querympe2) or fin_pagina();

        if ($res_funmpe1->recordcount() == 0 && $res_funmpe2->recordcount() == 0) {
            $ctrl['msj_error'] = 'Niï¿½o mayor de 1 aï¿½o sin vacuna';
            $ctrl['debito'] = true;
            $ctrl['id_error'] = '69';
        }
    }
    return $ctrl;
}

function actualizarFechaDeEntrada($nroexpediente, $fecha_entrada) {
    $sql = "UPDATE facturacion.factura
            SET fecha_entrada='$fecha_entrada'
            WHERE nro_exp='$nroexpediente'";
    $result = sql($sql, "", 0) or excepcion('Error al insertar el expediente');
}

/*
  dado el nombre de la trazadora,
  retorna la descripcion generica de una prestacion
 */

function getNombreGenericoPrestacion($nombre) {
    switch ($nombre) {
        case 'INMU':
            $descr = "INMUNIZACION";
            break;
        case 'NINO':
        case 'NINO_PESO':
            $descr = "CONTROL PEDIATRICO";
            break;
        case 'ADOLESCENTE':
            $descr = "CONTROL ADOLESCENTE";
            break;
        case 'PARTO':
            $descr = "CONTROL DEL PARTO";
            break;
        case 'EMB':
            $descr = "CONTROL DE EMBARAZO";
            break;
        case 'ADULTO':
            $descr = "CONTROL DE ADULTO";
            break;
        case 'SEGUIMIENTO':
            $descr = "SEGUIMIENTO";
            break;
        case 'TAL':
            $descr = "TAL";
            break;
    }
    return $descr;
}

/*
  dado el nombre de la trazadora,
  retorna nombre_esquema+nombre_tabla a cual consultar
 */

function getNombreTablaTrazadora($nombre) {
    switch ($nombre) {
        case 'INMU':
            $tabla = "inmunizacion.prestaciones_inmu";
            break;
        case 'NINO':
        case 'NINO_PESO':
            $tabla = "trazadoras.nino_new";
            break;
        case 'ADOLESCENTE':
            $tabla = "trazadoras.adolecentes";
            break;
        case 'PARTO':
            $tabla = "trazadoras.partos";
            break;
        case 'EMB':
            $tabla = "trazadoras.embarazadas";
            break;
        case 'ADULTO':
            $tabla = "trazadoras.adultos";
            break;
        case 'SEGUIMIENTO':
            $tabla = "trazadoras.seguimiento_remediar";
            break;
        case 'CLASIFICACION':
            $tabla = "trazadoras.clasificacion_remediar2";
            break;
        case 'TAL':
            $tabla = "trazadoras.tal";
            break;
    }
    return $tabla;
}

function insertarInmunizacion() {
    
}

?>