<?

require_once ("../../lib/funciones_misiones.php");
/*
prueba de post-commit eeeee
  ----------------------------------------
  Autor: FER
  Fecha: 03/03/2009
  ----------------------------------------

  /*******************************************************************************
  Valida las prestaciones de acuerdo a las reglas especificadas

  @id_comprobante
  @nomenclador    Id del nomenclador (despues tengo que sacar codigo)

 * ***************************************************************************** */

/* function buscarFechaDeEmbarazo($clavebeneficiario, $fecha) {
  $res = afiliadoEnPadronPorFecha($fecha, $clavebeneficiario,$tipofacturacion);
  $query = "SELECT fechaprobableparto,fechaefectivaparto,fechadiagnosticoembarazo
  FROM nacer.smiafiliados
  WHERE clavebeneficiario='$clavebeneficiario'";
  $res = sql($query, "Error 1") or fin_pagina();
  $fecha['diagnostico'] = $res['diagnostico'];
  //if ($res->fields['fecha_probable_parto'] != "")
  $fecha['parto'] = $res['parto'];
  if ($res->fields['fecha_efectiva_parto'] != "")
  $fecha['parto'] = $res->fields['fecha_efectiva_parto'];

  return $fecha;
  } */

function getFormInfoTrazadora($trazadora){
    switch($trazadora){
        case 'ADOLESCENTE':
            $form = "trz_adolescente.php";
            break;
        case 'ADULTO':
            $form = "trz_adulto.php";
            break;
        case 'EMB':
            $form = "trz_embarazo.php";
            break;
        case 'INMU':
            $form = "trz_inmunizacion.php";
            break;
        case 'NINO':
        case 'NINO_PESO':
            $form = "trz_nino.php";
            break;            
        case 'PARTO':
            $form = "trz_parto.php";
            break;
        case 'TAL':
            $form = "trz_tal.php";
            break;
    }
    return $form;
}

function getSQLDatosBeneficiarioFromTrazadora($nroDoc,$trazadora){
    $tabla = getNombreTablaTrazadora($trazadora);
    $sql = "SELECT apellido || ' ' || nombre AS nombre_beneficiario, clave 
            FROM ".$tabla."
            WHERE num_doc='".$nroDoc."' 
            LIMIT 1
            ";
    return $sql;
}

function getSQLCountPrestacionesBeneficiario($nroDoc,$fechaDesde="",$fechaHasta=""){
    $sql  = " SELECT COUNT(*) AS total 
              FROM ( ";
    $sql .= getSQLPrestacionesBeneficiario($nroDoc,$fechaDesde,$fechaHasta,99999,0);
    $sql .= " ) pr_tr ";
    return $sql;
}

// $fechaDesde y $fechaHasta deben tener formato dd/mm/yyyy
function getSQLPrestacionesBeneficiario($nroDoc,$fechaDesde="",$fechaHasta="",$limit=9999,$offset=0){
    if(isset($fechaDesde) && $fechaDesde!=""){
        $arr_fecha_desde = explode("/", $fechaDesde);
        $fechaDesde = $arr_fecha_desde[2]."-".$arr_fecha_desde[1]."-".$arr_fecha_desde[0];
    }
    if(isset($fechaHasta) && $fechaHasta!=""){
        $arr_fecha_hasta = explode("/", $fechaHasta);
        $fechaHasta = $arr_fecha_hasta[2]."-".$arr_fecha_hasta[1]."-".$arr_fecha_hasta[0];
    }
    if($fechaDesde!="" && $fechaHasta==""){
        $cond_fechaP .= " AND comp.fecha_comprobante >= '$fechaDesde' ";
        $cond_fechaT .= " AND t.fecha_comprobante >= '$fechaDesde' ";
    }
    if($fechaDesde=="" && $fechaHasta!=""){
        $cond_fechaP .= " AND comp.fecha_comprobante <= '$fechaHasta' ";
        $cond_fechaT .= " AND t.fecha_comprobante <= '$fechaHasta' ";
    }
    if($fechaDesde!="" && $fechaHasta!=""){
        $cond_fechaP .= " AND comp.fecha_comprobante BETWEEN '$fechaDesde' AND '$fechaHasta' ";
        $cond_fechaT .= " AND t.fecha_comprobante BETWEEN '$fechaDesde' AND '$fechaHasta' ";
    }
    $limit = " LIMIT $limit OFFSET $offset ";
    $sql = "SELECT DISTINCT(prest.id_prestacion), comp.fecha_comprobante, comp.grupo_etario, 
                    efec.nombreefector, efec.cuie, 
		    CAST(benef.numero_doc AS INTEGER), benef.fecha_nacimiento_benef, 
                    nom.codigo, nom.diagnostico, 
                    nom.codigo || CASE WHEN nom.diagnostico <> '' 
                                       THEN ' ' || nom.diagnostico 
                                       ELSE '' 
                                  END AS cod_nomenclador,
                    nom.descripcion, nom.id_nomenclador_detalle, 
                    t_desc.descripcion AS desc_descripcion, t_desc.trz 
             FROM (  select afidni as numero_doc, aficlasedoc as clase_documento_benef, 
                        afifechanac as fecha_nacimiento_benef, clavebeneficiario as clave_beneficiario 
                     from nacer.smiafiliados 
                     where afidni='".$nroDoc."' 
                   union
                     select numero_doc, clase_documento_benef, fecha_nacimiento_benef, clave_beneficiario 
                     from uad.beneficiarios 
                     where numero_doc='".$nroDoc."' 
                   ) benef
             JOIN facturacion.comprobante comp ON comp.clavebeneficiario=benef.clave_beneficiario 
             JOIN facturacion.prestacion prest ON comp.id_comprobante=prest.id_comprobante 
             JOIN facturacion.nomenclador nom ON prest.id_nomenclador=nom.id_nomenclador 
             JOIN facturacion.smiefectores efec ON comp.cuie=efec.cuie 
             LEFT JOIN nomenclador.descripciones t_desc ON comp.grupo_etario=t_desc.grupo_etareo 
                                                   AND nom.codigo=t_desc.codigo
                                                   AND nom.diagnostico=t_desc.diagnostico 
             WHERE benef.numero_doc='".$nroDoc."' AND benef.clase_documento_benef='P' 
                 ".$cond_fechaP."
    UNION

            SELECT NULL AS id_prestacion, t.fecha_comprobante, NULL AS grupo_etario, 
                t.nombreefector, t.cuie, t.numero_doc, NULL AS fecha_nacimiento_benef, 
                NULL AS codigo, NULL ASdiagnostico, t.cod_nomenclador, NULL AS descripcion, NULL id_nomenclador_detalle, 
                NULL AS desc_descripcion, t.trz_nombre AS trz 
            FROM 
            (	
                SELECT 'ADOLESCENTE' AS trz_nombre, trz.num_doc AS numero_doc, trz.fecha_control AS fecha_comprobante, trz.cuie, trz.codnomenclador AS cod_nomenclador, ef.nombreefector
                FROM trazadoras.adolecentes trz 
                JOIN facturacion.smiefectores ef ON trz.cuie=ef.cuie 
            UNION  ALL
                SELECT 'ADULTO' AS trz_nombre, trz.num_doc AS numero_doc, trz.fecha_control AS fecha_comprobante, trz.cuie, trz.codnomenclador AS cod_nomenclador, ef.nombreefector
                FROM trazadoras.adultos trz 
                JOIN facturacion.smiefectores ef ON trz.cuie=ef.cuie 
            UNION ALL
                SELECT 'EMB' AS trz_nombre, trz.num_doc AS numero_doc, trz.fecha_control AS fecha_comprobante, trz.cuie, NULL AS cod_nomenclador, ef.nombreefector
                FROM trazadoras.embarazadas trz 
                JOIN facturacion.smiefectores ef ON trz.cuie=ef.cuie 
            UNION ALL
                SELECT 'NINO' AS trz_nombre, trz.num_doc AS numero_doc, trz.fecha_control AS fecha_comprobante, trz.cuie, cod_nomenclador, ef.nombreefector
                FROM trazadoras.nino_new trz 
                JOIN facturacion.smiefectores ef ON trz.cuie=ef.cuie 
            UNION ALL
                SELECT 'PARTO' AS trz_nombre, trz.num_doc AS numero_doc, trz.fecha_parto AS fecha_comprobante, trz.cuie, NULL AS cod_nomenclador, ef.nombreefector
                FROM trazadoras.partos trz 
                JOIN facturacion.smiefectores ef ON trz.cuie=ef.cuie 
            UNION ALL
                SELECT 'TAL' AS trz_nombre, trz.num_doc AS numero_doc, trz.fecha_control AS fecha_comprobante, trz.cuie, codnomenclador AS cod_nomenclador, ef.nombreefector
                FROM trazadoras.tal trz 
                JOIN facturacion.smiefectores ef ON trz.cuie=ef.cuie 
            ) t 
            LEFT JOIN (
                    SELECT DISTINCT(prest.id_prestacion), comp.fecha_comprobante, comp.grupo_etario, 
                                efec.nombreefector, efec.cuie, benef.numero_doc, benef.fecha_nacimiento_benef, 
                                nom.codigo, nom.diagnostico, nom.descripcion, nom.id_nomenclador_detalle, 
                                t_desc.descripcion AS desc_descripcion, t_desc.trz 
                        FROM (  select afidni as numero_doc, aficlasedoc as clase_documento_benef, 
                                    afifechanac as fecha_nacimiento_benef, clavebeneficiario as clave_beneficiario 
                                from nacer.smiafiliados 
                                where afidni='".$nroDoc."' 
                            union
                                select numero_doc, clase_documento_benef, fecha_nacimiento_benef, clave_beneficiario 
                                from uad.beneficiarios 
                                where numero_doc='".$nroDoc."' 
                            ) benef
                        JOIN facturacion.comprobante comp ON comp.clavebeneficiario=benef.clave_beneficiario 
                        JOIN facturacion.prestacion prest ON comp.id_comprobante=prest.id_comprobante 
                        JOIN facturacion.nomenclador nom ON prest.id_nomenclador=nom.id_nomenclador 
                        JOIN facturacion.smiefectores efec ON comp.cuie=efec.cuie 
                        LEFT JOIN nomenclador.descripciones t_desc ON comp.grupo_etario=t_desc.grupo_etareo 
                                                            AND nom.codigo=t_desc.codigo
                                                            AND nom.diagnostico=t_desc.diagnostico 
            ) p ON t.numero_doc=CAST(p.numero_doc AS INTEGER) AND t.fecha_comprobante=p.fecha_comprobante AND t.cuie=p.cuie
                    

            WHERE t.numero_doc='".$nroDoc."' and p.id_prestacion IS NULL
                ".$cond_fechaT."


    ORDER BY fecha_comprobante DESC 
        ".$limit."
    ";
    /*
    $sql  = "SELECT DISTINCT(prest.id_prestacion), comp.fecha_comprobante, comp.grupo_etario, 
                    efec.nombreefector, efec.cuie, benef.numero_doc, benef.fecha_nacimiento_benef, 
                    nom.codigo, nom.diagnostico, nom.descripcion, nom.id_nomenclador_detalle, 
                    t_desc.descripcion AS desc_descripcion, t_desc.trz 
             FROM (  select afidni as numero_doc, aficlasedoc as clase_documento_benef, 
                        afifechanac as fecha_nacimiento_benef, clavebeneficiario as clave_beneficiario 
                     from nacer.smiafiliados 
                     where afidni='".$nroDoc."' 
                   union
                     select numero_doc, clase_documento_benef, fecha_nacimiento_benef, clave_beneficiario 
                     from uad.beneficiarios 
                     where numero_doc='".$nroDoc."' 
                   ) benef
             JOIN facturacion.comprobante comp ON comp.clavebeneficiario=benef.clave_beneficiario 
             JOIN facturacion.prestacion prest ON comp.id_comprobante=prest.id_comprobante 
             JOIN facturacion.nomenclador nom ON prest.id_nomenclador=nom.id_nomenclador 
             JOIN facturacion.smiefectores efec ON comp.cuie=efec.cuie 
             LEFT JOIN nomenclador.descripciones t_desc ON comp.grupo_etario=t_desc.grupo_etareo 
                                                   AND nom.codigo=t_desc.codigo
                                                   AND nom.diagnostico=t_desc.diagnostico 
             WHERE benef.numero_doc='".$nroDoc."' AND benef.clase_documento_benef='P' 
                 ".$cond_fecha."
             ORDER BY comp.fecha_comprobante DESC";
    */
    return $sql;
}

function valida_prestacion($id_comprobante, $nomenclador) {

    //asigno variables para usar la validacion
    $query = "SELECT codigo 
                FROM facturacion.nomenclador 
		WHERE id_nomenclador='$nomenclador'";
    $res_codigo_nomenclador = sql($query, "Error 1") or fin_pagina();
    $codigo = $res_codigo_nomenclador->fields['codigo'];

    //traigo el codigo de nomenclador y si hay validaciones traigo los datos de la validacion
    $query = "SELECT * 
                FROM facturacion.validacion_prestacion_mns
		WHERE codnomenclador='$codigo'";
    $res = sql($query, "Error 1") or fin_pagina();

    if ($res->RecordCount() > 0) {//me fijo si hay que validar (si tiene regla)
        //recupero el id_smiafiliados para mas adelante
        $query = "SELECT c.clavebeneficiario,id_smiafiliados,fecha_comprobante
				FROM facturacion.comprobante c
  				INNER JOIN nacer.smiafiliados using (id_smiafiliados)
				WHERE id_comprobante='$id_comprobante'";
        $id_smiafiliados_res = sql($query, "Error 2") or fin_pagina();
        $id_smiafiliados = $id_smiafiliados_res->fields['id_smiafiliados'];
        $clavebeneficiario = $id_smiafiliados_res->fields['clavebeneficiario'];
        $fecha_comprobante = $id_smiafiliados_res->fields['fecha_comprobante'];

        //cantidad de prestaciones limites
        $cant_pres_lim = $res->fields['cant_pres_lim'];
        $per_pres_limite = $res->fields['per_pres_limite'];

        //cuenta la cantidad de prestaciones de un determinado filiado, de un determinado codigo y 
        //en un periodo de tiempo parametrizado.
        $query = "SELECT id_prestacion, codigo, fecha_comprobante
                        FROM nacer.smiafiliados
  			INNER JOIN facturacion.comprobante ON (nacer.smiafiliados.id_smiafiliados = facturacion.comprobante.id_smiafiliados)
  			INNER JOIN facturacion.prestacion ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)
  			INNER JOIN facturacion.nomenclador ON (facturacion.prestacion.id_nomenclador = facturacion.nomenclador.id_nomenclador)
  			WHERE smiafiliados.id_smiafiliados=$id_smiafiliados 
                        AND trim(codigo)=trim('$codigo')
                        AND fecha_comprobante BETWEEN (to_date('$fecha_comprobante', 'YYYY-MM-DD') - interval '1*$per_pres_limite month') AND to_date('$fecha_comprobante', 'YYYY-MM-DD')
                        AND comprobante.marca='0'";
        $cant_pres = sql($query, "Error 3") or fin_pagina();

        if ($cant_pres->RecordCount() >= $cant_pres_lim) {
            $msg_error = $res->fields['msg_error'];
            $accion = $msg_error . " - Cantidad de Prestaciones: " . $cant_pres->RecordCount() . " - Limite: " . $cant_pres_lim . " en " . $per_pres_limite . " dias" . " - Codigo: " . $codigo;
            echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";
            return 0;
        }
        else
            return 1;
    }
    else
        return 1;
}

function valida_prestacion1($id_comprobante, $nomenclador) {

    $query = "select codigo from facturacion.nomenclador 
			where id_nomenclador='$nomenclador'";
    $res = sql($query, "Error 1") or fin_pagina();
    $codigo_nomenclador = $res->fields['codigo'];

    $query = "SELECT afifechanac,fecha_comprobante
				FROM facturacion.comprobante
  				INNER JOIN nacer.smiafiliados using (id_smiafiliados)
				where id_comprobante='$id_comprobante'";
    $res1 = sql($query, "Error 2") or fin_pagina();
    $fecha_nac = $res1->fields['afifechanac'];
    $fecha_comprobante = $res1->fields['fecha_comprobante'];

    list($aa, $mm, $dd) = explode("-", $fecha_comprobante);
    $fecha1 = mktime(0, 0, 0, $mm, $dd, $aa);
    list($aa, $mm, $dd) = explode("-", $fecha_nac);
    $fecha2 = mktime(0, 0, 0, $mm, $dd, $aa);
    $Dias = ($fecha1 - $fecha2) / 86400;

    if (($codigo_nomenclador == 'NPE 32') && ($Dias > 365)) {
        $accion = "No se Puede facturar un 'NPE 32' a un niño mayor de 1 año - Por favor Verifique o Facture un 'NPE 33'";
        echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";
        return 0;
    } else {
        if (($codigo_nomenclador == 'NPE 33') && ($Dias <= 365)) {
            $accion = "No se Puede facturar un 'NPE 33' a un niño menor de 1 año - Por favor Verifique o Facture un 'NPE 32'";
            echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";
            return 0;
        }
        else
            return 1;
    }
    echo $codigo_nomenclador . $edad;
}

function valida_prestacion3($id_comprobante, $nomenclador) {

    //asigno variables para usar la validacion
    $query = "select codigo from nomenclador.grupo_prestacion
			where id_categoria_prestacion='$nomenclador'";
    $res_codigo_nomenclador = sql($query, "Error 1") or fin_pagina();
    $codigo = $res_codigo_nomenclador->fields['codigo'];

    //traigo el codigo de nomenclador y si hay validaciones traigo los datos de la validacion
    $query = "select * from facturacion.validacion_prestacion
			where codigo='$codigo'";
    $res = sql($query, "Error 1") or fin_pagina();

    if ($res->RecordCount() > 0) {//me fijo si hay que validar (si tiene regla)
        //recupero el id_smiafiliados para mas adelante
        $query = "SELECT id_smiafiliados,fecha_comprobante
				FROM facturacion.comprobante
  				INNER JOIN nacer.smiafiliados using (id_smiafiliados)
				where id_comprobante='$id_comprobante'";
        $id_smiafiliados_res = sql($query, "Error 2") or fin_pagina();
        $id_smiafiliados = $id_smiafiliados_res->fields['id_smiafiliados'];
        $fecha_comprobante = $id_smiafiliados_res->fields['fecha_comprobante'];

        //cantidad de prestaciones limites
        $cant_pres_lim = $res->fields['cant_pres_lim'];
        $per_pres_limite = $res->fields['per_pres_limite'];

        //cuenta la cantidad de prestaciones de un determinado filiado, de un determinado codigo y 
        //en un periodo de tiempo parametrizado.
        $query = "SELECT id_prestaciones_n_op, codigo, comprobante.fecha_comprobante
				FROM nacer.smiafiliados
  				INNER JOIN facturacion.comprobante ON (nacer.smiafiliados.id_smiafiliados = facturacion.comprobante.id_smiafiliados)
  				inner join nomenclador.prestaciones_n_op using (id_comprobante)
  				where smiafiliados.id_smiafiliados=$id_smiafiliados and 
  					  tema='$codigo' and
  					  prestaciones_n_op.fecha_comprobante between (CAST('$fecha_comprobante' AS date) - $per_pres_limite) and CAST('$fecha_comprobante' AS date)";
        $cant_pres = sql($query, "Error 3") or fin_pagina();

        if ($cant_pres->RecordCount() >= $cant_pres_lim) {
            $msg_error = $res->fields['msg_error'];
            $accion = $msg_error . " - Cantidad de Prestaciones: " . $cant_pres->RecordCount() . " - Limite: " . $cant_pres_lim . " en " . $per_pres_limite . " dias" . " - Codigo: " . $codigo;
            echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";
            return 0;
        }
        else
            return 1;
    }
    else
        return 1;
}
