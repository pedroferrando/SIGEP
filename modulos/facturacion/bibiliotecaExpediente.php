<?php

require_once ("../../modulos/facturacion/funciones.php");

function buscarFacturasPersistidas($expedienteid, $cuie) {
    $q = "SELECT * FROM expediente.facturas_persistidas fp
            LEFT JOIN facturacion.factura f ON (f.id_factura=fp.id_factura::INTEGER)                   
        WHERE fp.id_expediente='$expedienteid'
        AND f.cuie='$cuie'
    ORDER BY f.periodo DESC";
    $resul = sql($q) or die;
    return $resul;
}

function buscarPracticasEnFacturaPersistida($id_factura_persistida) {
    $q = "SELECT * FROM expediente.practicas_persistidas
          WHERE  id_factura_persistida='$id_factura_persistida'                                        
          ORDER BY codigo";
    $practicasenfactura = sql($q) or die($db->ErrorMsg());
    return $practicasenfactura;
}

function buscarNombreEfector($cuie) {
    $q = "SELECT nombreefector FROM facturacion.smiefectores       
        WHERE cuie='$cuie'";
    $resul = sql($q) or die;
    return $resul->fields['nombreefector'];
}

function buscarDatosDeFactura($id_factura) {
    $queryporefector = "SELECT f.fecha_factura,f.id_factura,nro_fact_offline,nombreefector,f.monto_prefactura,f.periodo,tipoefector,f.tipo_nomenclador,
                                            f.estado,f.id_factura,f.tipo_liquidacion,f.periodo_actual,f.fecha_entrada                                            
                                            FROM facturacion.factura f
                                            INNER JOIN facturacion.smiefectores USING(cuie)
                                            WHERE f.id_factura='$id_factura'";

    $resul = sql($queryporefector) or die;
    return $resul;
}

function existePago($nro_expediente) {
    $valor = false;
    $result = buscarDatosExpediente($nro_expediente);
    if ($result->rowCount() > 0) {
        $valor = true;
    }
    return $valor;
}

function estaAbiertoPago($nro_expediente) {
    $valor = false;
    $result = buscarDatosExpediente($nro_expediente, "estado");
    if ($result->fields['estado'] == "A") {
        $valor = true;
    }
    return $valor;
}

function guardarPago($parametros) {
    global $_ses_user;
    $fecha_hoy = date("Y-m-d H:i:s");
    $usuario = $_ses_user['name'];
    $total_liquidado = $parametros['total_liquidado'];
    $administrador = $parametros['administrador'];
    $nro_cheque = $parametros['nro_cheque'];
    $nro_expediente = $parametros['nro_expediente'];
    $nro_orden = $parametros['nro_orden'];
    $fecha_orden_de_cargo = $parametros['fecha_orden_de_cargo'];
    $responsable_administrador = $parametros['responsable_administrador'];
    $fecha_pago_efectivo = $parametros['fecha_pago_efectivo'];
    $existepago = existePago($nro_expediente);
    if (!$existepago) {
        // se hace un insert
        if ($fecha_pago_efectivo != "") {
            $estado = 'C';
            $fields = " ,fecha_pago_efectivo ";
            $values = " ,to_date('$fecha_pago_efectivo','DD/MM/YYYY') ";
        } else {
            $estado = 'A';
        }
        $fields .= " ,estado ";
        $values .= " ,'$estado' ";
        $q = "INSERT INTO facturacion.pagos(importe,administrador,nro_cheque,fecha_orden_de_cargo,responsable_administrador,nro_orden,nro_expediente,usuario,fecha_proceso " . $fields . ")
              VALUES('$total_liquidado','$administrador','$nro_cheque',to_date('$fecha_orden_de_cargo','DD/MM/YYYY'),'$responsable_administrador','$nro_orden','$nro_expediente','$usuario','$fecha_hoy' " . $values . ")";
        sql($q) or die;
    } else {
        //se hace un update
        if ($fecha_pago_efectivo != "") {
            $set_campos = " ,fecha_pago_efectivo=to_date('$fecha_pago_efectivo','DD/MM/YYYY'), estado='C' ";
        }
        $q = "UPDATE facturacion.pagos
              SET importe='$total_liquidado',administrador='$administrador',nro_cheque='$nro_cheque',
                  fecha_orden_de_cargo=to_date('$fecha_orden_de_cargo','DD/MM/YYYY'),
                  responsable_administrador='$responsable_administrador',nro_orden='$nro_orden',
                  usuario='$usuario',fecha_proceso='$fecha_hoy'
                  " . $set_campos . "
              WHERE nro_expediente='$nro_expediente'";
        sql($q) or die;
    }
}

function borrarPago($nro_expediente) {
    $valor = false;
    $q = "SELECT id_pago FROM facturacion.pagos        
        WHERE nro_expediente='$nro_expediente'";
    $resul = sql($q) or die;
    if ($resul->rowcount() > 0) {
        $valor = true;
    }
    return $valor;
}

function buscarEfectoresEntrePersistidos($expedienteid) {
    $q = "SELECT cuie FROM expediente.facturas_persistidas 
            WHERE id_expediente='$expedienteid'
            GROUP BY cuie";
    $efectores = sql($q) or die($db->ErrorMsg());
    return $efectores;
}

function buscarIDExpediente($expediente) {
    $q = "SELECT expediente_id FROM facturacion.expediente 
            WHERE nro_exp='$expediente'";
    $idexp = sql($q) or die($db->ErrorMsg());
    return $idexp->fields['expediente_id'];
}

function estadoExpediente($expediente) {
    $query = "SELECT estado FROM facturacion.expediente 
            WHERE nro_exp='$expediente'";
    $estadodelexpediente = sql($query) or die($db->ErrorMsg());
    return $estadodelexpediente->fields['estado'];
}

function persistirFilaLiquidada($id_factura_persistida, $elcodigo, $precio, $cantidad_total, $cantidad_rechazo) {
    $query = "INSERT INTO expediente.practicas_persistidas(id_factura_persistida,codigo,precio,cantidad_total,cantidad_rechazos)
               VALUES('$id_factura_persistida', '$elcodigo', '$precio', '$cantidad_total', '$cantidad_rechazo')";
    sql($query) or die($db->ErrorMsg());
}

function persistirFacturaLiquidada($id_expediente, $cuie, $id_factura) {
    $query_existe = "SELECT * from expediente.facturas_persistidas
                    WHERE id_expediente='$id_expediente'
                    AND cuie='$cuie'
                    AND id_factura='$id_factura'";
    $existe = sql($query_existe) or die($db->ErrorMsg());
    if ($existe->rowcount() == 0) {
        $query = "INSERT INTO expediente.facturas_persistidas(cuie,id_expediente,id_factura)
               VALUES('$cuie','$id_expediente','$id_factura') RETURNING id_factura_persistida";
        $id = sql($query) or die($db->ErrorMsg());
        $id = $id->fields[0];
    } else {
        $id = $existe->fields[0];

        $querydeletepracticas = "delete from expediente.practicas_persistidas
                    where id_factura_persistida='$id'";
        sql($querydeletepracticas) or die($db->ErrorMsg());

        $querydeletefactura = "delete from expediente.facturas_persistidas
                    where id_factura_persistida='$id'";
        sql($querydeletefactura) or die($db->ErrorMsg());

        $query = "INSERT INTO expediente.facturas_persistidas(cuie,id_expediente,id_factura)
               VALUES('$cuie','$id_expediente','$id_factura') RETURNING id_factura_persistida";
        $id = sql($query) or die($db->ErrorMsg());

        $id = $id->fields[0];
    }
    return $id;
}

function actualizarMontosFacturaPersistida($id_factura_persitida, $monto) {
    $query = "UPDATE expediente.facturas_persistidas
                SET total_liquidado='" . $monto['liquidado'] . "',
                total_rechazado='" . $monto['debitado'] . "',
                cant_de_practicas='" . $monto['cant_total'] . "',
                cant_de_practicas_rechazadas='" . $monto['cant_rechazo'] . "'
                WHERE id_factura_persistida='$id_factura_persitida'";
    sql($query) or die($db->ErrorMsg());
}

function persistirObjetivos($expediente_id, $cuie, $ano, $mes, $objetivosResultado) {
    $queryyaexiste = "SELECT id_objetivo FROM expediente.objetivos
                WHERE ano='$ano' 
                    AND mes='$mes' 
                    AND cuie='$cuie'
                    AND expediente_id=$expediente_id";
    $existe = sql($queryyaexiste) or die($db->ErrorMsg());
    if ($existe->rowcount() > 0) {
        $querydeleteobjetivo = "UPDATE expediente.objetivos
                                SET marca=FALSE
                                WHERE ano='$ano' 
                                AND mes='$mes' 
                                AND cuie='$cuie'
                                AND expediente_id=$expediente_id";
        sql($querydeleteobjetivo) or die($db->ErrorMsg());
    }

    foreach ($objetivosResultado as $unobjetivo) {

        if ($unobjetivo['meta']) {
            if ($unobjetivo['cumplido'] == 'SI') {
                $cumplido = 1;
            } else {
                $cumplido = 0;
            }
            $objetivonro = $unobjetivo['objetivonro'];
            $fecha_modificacion = date("Y-m-d H:i:s");
            $query = "INSERT INTO expediente.objetivos(cuie,obj,meta,asignado,informado,cumplimiento,cumplido,puntos,ano,mes,expediente_id,marca,fecha_modificacion)
               VALUES('$cuie','$objetivonro','" . $unobjetivo['meta'] . "','" . $unobjetivo['numerador'] . "',
                   '" . $unobjetivo['denominador'] . "','" . $unobjetivo['total_perc'] . "','$cumplido',
                       '" . $unobjetivo['puntos'] . "','$ano','$mes',$expediente_id,TRUE,'$fecha_modificacion')";
            $result = sql($query) or die($db->ErrorMsg());
        }
    }
}

function buscarObjetivosPersistidos($cuie, $expedienteid) {
    $query = "SELECT * from expediente.objetivos
                WHERE cuie='$cuie'
                AND expediente_id=$expedienteid";

    $result = sql($query) or die($db->ErrorMsg());
    if ($result->EOF) {
        $datosdelafactura = buscarFacturasPersistidas($expedienteid, $cuie);
        $periodoestimulo = split("/", $datosdelafactura->fields['periodo']);
        $ano = $periodoestimulo[0];
        $mes = $periodoestimulo[1];
        $query = "SELECT * from expediente.objetivos
                WHERE cuie='$cuie'
                AND ano='$ano'
                AND mes='$mes'";

        $result = sql($query) or die($db->ErrorMsg());
    }
    while (!$result->EOF) {
        $objnro = $result->fields['obj'];
        $resul_objetivo[$objnro]['numerador'] = $result->fields['asignado'];
        $resul_objetivo[$objnro]['denominador'] = $result->fields['informado'];
        $resul_objetivo[$objnro]['meta'] = $result->fields['meta'];
        if ($result->fields['cumplido'] == 't') {
            $cumplido = 'SI';
        } else {
            $cumplido = 'NO';
        }
        $resul_objetivo[$objnro]['cumplido'] = $cumplido;
        $resul_objetivo[$objnro]['puntos'] = $result->fields['puntos'];
        $resul_objetivo[$objnro]['total_perc'] = $result->fields['cumplimiento'];
        $resul_objetivo[$objnro]['encontro'] = TRUE;
        $result->movenext();
    }
    return $resul_objetivo;
}

function persistirPracticasDelExpediente($expediente) {
    $expediente = ExpedienteCollecion::Filtrar("nro_exp='$expediente'");
    $efectores = $expediente->getEfectores();
    foreach ($efectores as $efector) {
        $facturasdelefector = $expediente->facturasDelEfector($efector->getCuie());
        $tipoefector = $efector->getTipoefector();
        $tiponomenclador = $facturasdelefector[0]->getTipoNomenclador();
        $fecha_entrada = $facturasdelefector[0]->getFechaEntrada();
        foreach ($facturasdelefector as $factura) {

            $id_factura_persistida = persistirFacturaLiquidada($expediente->getExpedienteId(), $efector->getCuie(), $factura->getIdFactura());

            $monto['cant_total'] = 0;
            $monto['cant_rechazo'] = 0;
            $monto['debitado'] = 0;
            $nomencladoresfacturados = $factura->getPracticasConCodigoDiferente();

            foreach ($nomencladoresfacturados as $nomencladorfacturado) {
                persistirFilaLiquidada($id_factura_persistida, $nomencladorfacturado['codigo'], $nomencladorfacturado['precio_unitario'], $nomencladorfacturado['cantidad'], $nomencladorfacturado['cantidad_debitados']);

                $monto['cant_total'] += $nomencladorfacturado['cantidad'];
                $monto['cant_rechazo'] += $nomencladorfacturado['cantidad_debitados'];
                $monto['debitado']+=$nomencladorfacturado['monto_debito'];
            }
            $monto['liquidado'] = $factura->getMontoPrefactura();

            actualizarMontosFacturaPersistida($id_factura_persistida, $monto);
        }
        $periodoestimulo = PeriodoObjetivo::calcularPeriodo($facturasdelefector[0]->getFechaEntrada());
        if (is_null($periodoestimulo)) {
            $periodoestimulo = split("/", $facturasdelefector[0]->getPeriodo());
            $ano = $periodoestimulo[0];
            $mes = $periodoestimulo[1];
        } else {
            $periodoestimulo = split("/", $periodoestimulo->getPeriodo());
            $ano = $periodoestimulo[1];
            $mes = $periodoestimulo[0];
        }

        $objetivosResultado = calcularObjetivos($efector->getCuie(), $ano, $mes, $tipoefector, $tiponomenclador, $fecha_entrada);
        persistirObjetivos($expediente->getExpedienteId(), $efector->getCuie(), $ano, $mes, $objetivosResultado);
    }
}

function facturasDelEfector($expediente, $cuie) {
    $queryporefector = "SELECT f.fecha_factura,f.id_factura,nro_fact_offline,nombreefector,f.monto_prefactura,f.periodo,tipoefector,
                                f.estado,f.id_factura,f.tipo_nomenclador,f.fecha_entrada,f.periodo_actual,f.tipo_liquidacion
                                FROM facturacion.factura f
                                INNER JOIN facturacion.smiefectores USING(cuie)
                                WHERE f.cuie='$cuie' AND nro_exp='$expediente'
                                ORDER BY f.periodo DESC";
    $facturasdelefector = sql($queryporefector) or die($db->ErrorMsg());
    return $facturasdelefector;
}

function debitoAuditado($expediente, $cuie) {
    $querybuscardebitos = "SELECT * FROM facturacion.debito_auditoria
                            WHERE nro_exp='$expediente' 
                            AND cuie='$cuie'";
    $debitosencontrados = sql($querybuscardebitos) or die;
    return $debitosencontrados;
}

function creditoAuditado($expediente, $cuie) {
    $querybuscarcreditos = "SELECT * FROM facturacion.credito_auditoria
                            WHERE nro_exp='$expediente' 
                            AND cuie='$cuie'";
    $creditosencontrados = sql($querybuscarcreditos) or die;
    return $creditosencontrados;
}

function obsRegistradas($expediente, $cuie) {
    $querybuscarobs = "SELECT * 
                       FROM facturacion.observaciones
                       WHERE nro_exp='$expediente' 
                       AND cuie='$cuie'";
    $obsencontrados = sql($querybuscarobs) or die;
    return $obsencontrados;
}

function fechasFacturadas($id_factura) {
    $q = "SELECT EXTRACT(YEAR from fecha_comprobante) ano,EXTRACT(MONTH from fecha_comprobante) mes 
            FROM facturacion.comprobante 
            WHERE id_factura='$id_factura' 
            GROUP BY EXTRACT(MONTH from fecha_comprobante),EXTRACT(YEAR from fecha_comprobante)
            ORDER BY EXTRACT(MONTH from fecha_comprobante),EXTRACT(YEAR from fecha_comprobante) ASC";
    $periodos = sql($q) or die;
    return $periodos;
}

function practicasEnFactura($id_factura) {
    $q = "select * from ((SELECT p.id_nomenclador,trim(n.codigo) codigo,CASE WHEN n.diagnostico<>'' then n.diagnostico else '' end as diagnostico,p.precio_prestacion precio,
                                        p.id_anexo, a.numero,  sum(p.cantidad) cantidad
                                        FROM facturacion.prestacion p
                                        INNER JOIN facturacion.comprobante c USING (id_comprobante)
                                        INNER JOIN facturacion.nomenclador n USING (id_nomenclador)
                                        LEFT JOIN facturacion.anexo a USING (id_anexo)		
                                        where  c.id_factura='$id_factura'
					AND p.id_nomenclador<>0
                                        GROUP BY p.id_nomenclador,n.codigo,n.diagnostico,p.precio_prestacion,p.id_anexo,a.numero
                                        ORDER BY n.codigo, a.numero)

                                        UNION

                                        (SELECT 0 id_nomenclador,trim(codigo_deb) codigo,'' diagnostico,monto precio,
                                        -1 id_anexo, -1 numero,  round(sum(d.cantidad),0) cantidad
                                        FROM facturacion.debito d
                                        INNER JOIN facturacion.comprobante c USING (id_comprobante)		
                                        where  c.id_factura='$id_factura'
                                        and id_nomenclador=0
                                        GROUP BY codigo_deb,monto
                                        ORDER BY codigo_deb)) as reunion
                        ORDER BY codigo,numero";

    $practicasenfactura = sql($q) or die($db->ErrorMsg());
    return $practicasenfactura;
}

function practicasDebitadasEnFactura($factura, $codigo, $diagnostico, $id_nomenclador, $precio, $anexo) {
    if ($anexo > 0) {
        $otroq = "SELECT n.id_nomenclador,sum(p.cantidad) cantidad, sum(d.monto*p.cantidad) precio
              FROM facturacion.debito d                                                                                                      
              INNER JOIN facturacion.comprobante c USING (id_comprobante)
              INNER JOIN facturacion.prestacion p USING (id_comprobante)                                        
              INNER JOIN facturacion.nomenclador n ON (p.id_nomenclador=n.id_nomenclador)
              INNER JOIN facturacion.anexo a on a.id_anexo=p.id_anexo        
              WHERE c.id_factura='$factura'
              AND REPLACE(n.codigo, ' ', '') =REPLACE('" . $codigo . "', ' ', '')
              AND p.id_anexo=$anexo
              GROUP BY n.id_nomenclador,a.precio";
    } elseif ($diagnostico <> '') {
        $otroq = "SELECT sum(p.cantidad) cantidad, sum(d.monto*p.cantidad) precio,p.id_nomenclador
              FROM facturacion.debito d                                                                                                      
              INNER JOIN facturacion.comprobante c USING (id_comprobante)
              INNER JOIN facturacion.prestacion p ON (D.ID_COMPROBANTE=P.ID_COMPROBANTE and D.ID_prestacion=P.ID_prestacion)                                        
              INNER JOIN facturacion.nomenclador n ON (p.id_nomenclador=n.id_nomenclador)
              WHERE c.id_factura='$factura'
              AND d.id_nomenclador= $id_nomenclador
                  AND p.precio_prestacion=$precio
              AND REPLACE(n.codigo, ' ', '') =REPLACE('" . $codigo . "', ' ', '')
              GROUP BY p.precio_prestacion,p.id_nomenclador";
    } else {
        $otroq = "SELECT sum(p.cantidad) cantidad, sum(d.monto*p.cantidad) precio,p.id_nomenclador
              FROM facturacion.debito d                                                                                                      
              INNER JOIN facturacion.comprobante c USING (id_comprobante)
              INNER JOIN facturacion.prestacion p USING (id_comprobante)                                        
              INNER JOIN facturacion.nomenclador n ON (p.id_nomenclador=n.id_nomenclador)
              WHERE c.id_factura='$factura'
              AND d.id_nomenclador= $id_nomenclador
                  AND p.precio_prestacion=$precio
              AND REPLACE(n.codigo, ' ', '') =REPLACE('" . $codigo . "', ' ', '')
              GROUP BY p.precio_prestacion,p.id_nomenclador";
    }

    $practicasdebitadasenfactura = sql($otroq);
    if (!$practicasdebitadasenfactura->RowCount()) {
        $resultado['precio'] = 0;
        $resultado['cantidad'] = 0;
    } else {
        $resultado['cantidad'] = $practicasdebitadasenfactura->fields['cantidad'];
        $resultado['precio'] = $practicasdebitadasenfactura->fields['precio'];
    }
    return $resultado;
}

function practicasEnDebito($id_factura) {
    $query = "SELECT   *
        FROM  facturacion.debito  
        where id_factura='$id_factura'";

    $result = sql($query) or die;
    return $result;
}

function practicasEnCredito($id_factura) {
    $query = "SELECT  distinct(p.id_prestacion) ,codigo,diagnostico, precio_prestacion, 
              p.cantidad,c.clavebeneficiario,c.fecha_comprobante
              FROM facturacion.comprobante c 
              INNER JOIN facturacion.prestacion p USING(id_comprobante)
              INNER JOIN facturacion.nomenclador USING(id_nomenclador)
              LEFT JOIN facturacion.debito d ON (p.id_comprobante = d.id_comprobante and p.id_prestacion = d.id_prestacion)
              WHERE c.id_factura='$id_factura' AND id_debito IS NULL
              ORDER BY c.clavebeneficiario";

    $result = sql($query) or die;
    return $result;
}

function practicasEnCreditoViejo($id_factura) {
    // and p.id_prestacion = d.id_prestacion
    $query = "SELECT  distinct(p.id_prestacion) ,codigo,diagnostico, precio_prestacion, 
              p.cantidad,c.clavebeneficiario,c.fecha_comprobante
              FROM facturacion.comprobante c 
              INNER JOIN facturacion.prestacion p USING(id_comprobante)
              INNER JOIN facturacion.nomenclador USING(id_nomenclador)
              LEFT JOIN facturacion.debito d ON (p.id_comprobante = d.id_comprobante)
              WHERE c.id_factura='$id_factura' AND id_debito IS NULL
              ORDER BY c.clavebeneficiario";

    $result = sql($query) or die;
    return $result;
}

function debitosEnExpediente($nro_expediente) {
    $query = "SELECT   *
        FROM  facturacion.debito  
        where id_factura='$id_factura'";

    $result = sql($query) or die;
    return $result;
}

function debitosEnFactura($id_factura) {
    $online = esFacturaOnline($id_factura);
    if (!$online) {
        $query = "SELECT c.id_factura,sum(d.cantidad),sum(d.monto*d.cantidad) as monto_deb_total FROM FACTURACION.DEBITO D
                INNER JOIN FACTURACION.COMPROBANTE C USING(ID_COMPROBANTE)
                INNER JOIN FACTURACION.PRESTACION P ON(D.ID_COMPROBANTE=P.ID_COMPROBANTE)
                WHERE D.ID_FACTURA='$id_factura'
                group by c.id_factura";
    } else {
        $query = "SELECT c.id_factura,sum(d.cantidad),sum(d.monto*d.cantidad) as monto_deb_total FROM FACTURACION.DEBITO D
                INNER JOIN FACTURACION.COMPROBANTE C USING(ID_COMPROBANTE)
                INNER JOIN FACTURACION.PRESTACION P ON(D.ID_COMPROBANTE=P.ID_COMPROBANTE and D.ID_prestacion=P.ID_prestacion)
                WHERE D.ID_FACTURA='$id_factura'
                group by c.id_factura";
    }
    $result = sql($query) or die;
    return $result;
}

function esFacturaOnline($id_factura) {
    $query = "Select online from facturacion.factura
                where id_factura='$id_factura'";
    $result = sql($query);

    if ($result->fields['online'] == 'SI') {
        return true;
    } else {
        return false;
    }
}

function nombreEfector($cuie) {
    $q = "Select nombreefector from facturacion.smiefectores
            WHERE cuie='$cuie'";
    $nombre = sql($q) or die($db->ErrorMsg());
    return $nombre->fields['nombreefector'];
}

function tipoDeFactura($idfactura) {
    $q = "SELECT tipo_liquidacion
          FROM facturacion.factura f
          WHERE f.id_factura='$idfactura'";

    $resultado = sql($q) or die($db->ErrorMsg());

    $tipodefactura = $resultado->fields['tipo_liquidacion'];
    if ($tipodefactura == 'R') {
        $tipodefactura = "Refacturada";
    } else {
        $tipodefactura = "Vigente";
    }
    return $tipodefactura;
}

function tipoDeNomencladorFactura($idfactura) {
    $q = "SELECT tipo_nomenclador
          FROM facturacion.factura f
          WHERE f.id_factura='$idfactura'";

    $resultado = sql($q) or die($db->ErrorMsg());

    $tipodenom = $resultado->fields['tipo_nomenclador'];
    return $tipodenom;
}

function buscarDatosExpediente($nro_expediente, $fields = "") {
    if ($fields == "") {
        $fields = " * ";
    }
    $q = "SELECT " . $fields . " 
          FROM facturacion.pagos 
          WHERE nro_expediente='$nro_expediente'";
    $result = sql($q) or die;
    return $result;
}

?>