<?php

function insertarSoloExpediente($nroexpediente) {
    $sqlsiyaesta = "SELECT * FROM facturacion.expediente WHERE nro_exp='$nroexpediente'";
    $resultsiyaesta = sql($sqlsiyaesta);
    if ($resultsiyaesta->RecordCount() == 0) {
        $sql = "INSERT INTO facturacion.expediente (nro_exp,estado) 
            VALUES ('$nroexpediente','A')";
        $result = sql($sql, "", 0) or excepcion('Error al insertar el expediente');
    }
}

function actualizarExpedienteEnFactura($idfactura, $nro_exp) {
    $query = "UPDATE facturacion.factura
                SET nro_exp='$nro_exp'
                WHERE id_factura='$idfactura'";

    sql($query, "Error al actualizar el expediente en factura") or fin_pagina();
}

function anulaFacura($id_factura) {
    $queryrecepcion = "SELECT recepcion_id
                        FROM facturacion.factura
                        WHERE id_factura='$id_factura'";
    $cualeslaracepcion = sql($queryrecepcion, "Error al buscar recepcion") or fin_pagina();
    $id_recepcion = $cualeslaracepcion->fields['recepcion_id'];

    if ($id_recepcion) {
        $query = "DELETE FROM facturacion.comprobante
            WHERE id_factura ='$id_factura';
    
            DELETE FROM trazadoras.embarazadas_tmp
            WHERE id_recepcion ='$id_recepcion';
    
            DELETE FROM trazadoras.embarazadas
            WHERE id_recepcion ='$id_recepcion';

            DELETE FROM trazadoras.nino_new
            WHERE id_recepcion ='$id_recepcion';

            DELETE FROM trazadoras.nino_tmp
            WHERE id_recepcion ='$id_recepcion';

            DELETE FROM trazadoras.mu
            WHERE id_recepcion ='$id_recepcion';

            DELETE FROM trazadoras.partos
            WHERE id_recepcion ='$id_recepcion';

            DELETE FROM trazadoras.partos_tmp
            WHERE id_recepcion ='$id_recepcion';

            DELETE FROM facturacion.recepcion
            WHERE idrecepcion ='$id_recepcion';

            DELETE FROM facturacion.debito
            WHERE id_factura ='$id_factura';

            DELETE FROM facturacion.informados
            WHERE idrecepcion ='$id_recepcion';
    
            DELETE FROM facturacion.log_factura
            WHERE id_factura ='$id_factura';

            DELETE FROM facturacion.factura
            WHERE id_factura ='$id_factura'";
    } else {
        $query = "DELETE FROM facturacion.comprobante
            WHERE id_factura ='$id_factura';

            DELETE FROM facturacion.debito
            WHERE id_factura ='$id_factura';
    
            DELETE FROM facturacion.log_factura
            WHERE id_factura ='$id_factura';

            DELETE FROM facturacion.factura
            WHERE id_factura ='$id_factura'";
    }
    sql($query, "Error al anular la factura") or fin_pagina();

    return "Se ANULO la Factura Numero: $id_factura";
}

function traeListadoDeDatosEfector($cuieses) {
    $sql = "SELECT n.cuie, nombreefector, UPPER(trim(com_gestion)) AS com_gestion 
            FROM nacer.efe_conv n 
            INNER JOIN nacer.conv_nom cn USING(id_efe_conv)
            INNER JOIN facturacion.smiefectores s ON n.cuie=s.cuie
            WHERE n.cuie in $cuieses
            AND cn.activo='t' AND n.activo='t'";
    $res_efectores = sql($sql) or fin_pagina();
    return $res_efectores;
}

function buscarFechaPorComprobante($id_factura){
    $sql="Select to_char(fecha_comprobante,'DD-MM-YYYY') as fecha_comprobante from facturacion.comprobante
    WHERE id_factura='$id_factura' LIMIT 1";
    $result=sql($sql);
    return $result->fields['fecha_comprobante'];
}

?>
