<?
require_once ("../../config.php");

require_once ("../../lib/funciones_misiones.php");
require_once ("./recepcion_funciones.php");

extract($_GET, EXTR_SKIP);
if ($parametros)
    extract($parametros, EXTR_OVERWRITE);

echo ' PROCESANDO CONTROL......ESTA OPERACION PUEDE TARDAR VARIOS MINUTOS';
echo ' AGUARDE POR FAVOR!!! ' . "<br />";
$debitos = 0;
$inicio_proc = date("Y-m-d H:i:s");
$lineaprocesadas = 0;
try {
    sql("BEGIN");

    //SELECCIONA LOS DATOS DE LA FACTURA QUE NO TENGAN DEBITOS
    $granconsulta = "SELECT precio_prestacion,c.id_comprobante, p.id_nomenclador, c.id_nomenclador_detalle, c.idvacuna, f.cuie, c.idperiodo,c.periodo, n.codigo,n.diagnostico,
			p.prestacionid,p.id_anexo,p.id_prestacion,p.cantidad ,f.cuie, c.clavebeneficiario,f.id_factura,
                        c.id_smiafiliados,nd.modo_facturacion, to_char(c.fecha_comprobante, 'DD-MM-YYYY') AS fecha_comprobante, f.recepcion_id,
                        f.periodo periodofactura,online,grupo_etario,fecha_entrada
                        FROM facturacion.factura f
                        INNER JOIN facturacion.comprobante c ON f.id_factura=c.id_factura
                        INNER JOIN facturacion.prestacion p ON c.id_comprobante=p.id_comprobante
                        INNER JOIN facturacion.nomenclador n ON p.id_nomenclador=n.id_nomenclador
                        INNER JOIN facturacion.nomenclador_detalle nd ON c.id_nomenclador_detalle=nd.id_nomenclador_detalle
                        LEFT JOIN facturacion.recepcion r ON r.idrecepcion=f.recepcion_id
                        WHERE f.id_factura='$id_factura'
                        AND c.id_comprobante NOT IN(
                            SELECT id_comprobante 
                            FROM facturacion.debito 
                            WHERE id_factura='$id_factura')
                        ORDER BY c.id_comprobante ASC";

    //    AND c.id_comprobante='8966790'
    $res_granconsulta = sql($granconsulta) or fin_pagina();
    while (!$res_granconsulta->EOF) {
        $resultado_ctrl = "";
        $online = $res_granconsulta->fields['online'];
        $reordenarperiodo = split('/', $res_granconsulta->fields['periodofactura']);
        //TODO. porque calcula el limite de la prestacion a partir de la fecha 
        //del periodo liquidado que figura en el nombre del archivo
        $fprest_limite = calcular_limite_fecha_prestacion($reordenarperiodo[1], $reordenarperiodo[0]);

        $elcomprobante = $res_granconsulta->fields['id_comprobante'];
        $elnomenclador = $res_granconsulta->fields['id_nomenclador'];
        $elcodigonomenclador = $res_granconsulta->fields['codigo'];
        $res_granconsulta->fields['diagnostico'] ? $diagnostico = $res_granconsulta->fields['diagnostico'] : $diagnostico = null;
        $idrecepcion = $res_granconsulta->fields['recepcion_id'];

        $res_granconsulta->fields['prestacionid'] ? $prestacionid = $res_granconsulta->fields['prestacionid'] : $prestacionid = 0;

        $idprestacion = $res_granconsulta->fields['id_prestacion'];
        $periodo = $res_granconsulta->fields['periodo'];
        $cuie = $res_granconsulta->fields['cuie'];
        $elnomencladordetalle = $res_granconsulta->fields['id_nomenclador_detalle'];
        $vigencia['id'] = $elnomencladordetalle;
        $vigencia['tipo'] = $res_granconsulta->fields['modo_facturacion'];
        $clavebeneficiario = $res_granconsulta->fields['clavebeneficiario'];
        $idpadron = $res_granconsulta->fields['idperiodo'];
        $lafechacomprobante = $res_granconsulta->fields['fecha_comprobante'];
        $anexo = $res_granconsulta->fields['id_anexo'];

        $datosafi = afiliadoEnPadronPorID($idpadron, $clavebeneficiario);
        $grupo_etario = calcularGrupoEtareo($datosafi['afifechanac'], $lafechacomprobante);
        $grupo_etario['estaembarazada'] = beneficiarioEmbarazadoUAD($clavebeneficiario, $lafechacomprobante);

        if ($vigencia['tipo'] == 2) {
            $codigo_desglozado = split(" ", $elcodigonomenclador);
            $datosnomenclador = buscaPractica($codigo_desglozado[0], $codigo_desglozado[1], $diagnostico, $elnomencladordetalle, $grupo_etario, $datosafi['afisexo']);
            $datosnomenclador[3] = $elcodigonomenclador . " " . $diagnostico;
        } else {
            $datosnomenclador = traemeNomencladorConAnexo($vigencia, $elcodigonomenclador, $anexo);
        }

        $iddelafiliado = $datosafi['id'];
        $afinombre = $datosafi['afinombre'];
        $afiapellido = $datosafi['afiapellido'];
        $afidni = $datosafi['afidni'];
        $fprestacion = str_replace('/', '-', $lafechacomprobante); //fecha prestacion?
        $fnacimiento = $datosafi['afifechanac'];
        $fechainscripcion = $datosafi['fechainscripcion'];
        $idvacuna = $res_granconsulta->fields['idvacuna'];
        $fechadeentrada = Fecha_db($res_granconsulta->fields['fecha_entrada']);

        do {

            if ($vigencia['tipo'] == 2) {
                //si es un nom q no se controla sale del bucle evitando las demas funciones 
               

                $prestaciones = prestacionesEnComprobante($elcomprobante);
                $resultado_ctrl = controlExcluidos($cuie, $prestaciones, $grupo_etario['categoria']);
                if ($resultado_ctrl['debito']) {
                    break;
                }

                $resultado_ctrl = controlFechaEntrada($fechadeentrada, $lafechacomprobante);
                if ($resultado_ctrl['debito'])
                    break;

                if (!nomencladoresQueNoSeControlan($datosnomenclador[3]))
                    break;
                 
                $resultado_ctrl = fechaPrestacionXInscripcion($lafechacomprobante, $fechainscripcion);
                if ($resultado_ctrl['debito'])
                    break;

                // Si el afiliado se realizo las Prestaciones relacionadas requeridas previamente
                $resultado_ctrl = practicasRelacionadas($clavebeneficiario, $lafechacomprobante, $datosnomenclador);
                if ($resultado_ctrl['debito'])
                    break;

                //controles para facturas Sumar nuevo nomenclador
                $resultado_ctrl = excepcionSumarSoloEnero($cuie, $lafechacomprobante, $grupo_etario);
                if ($resultado_ctrl['debito'])
                    break;
            } else {
                $convenio = buscarConvenio($cuie, $fprestacion);
                $resultado_ctrl = practicaEsHabilitada($convenio, $datosnomenclador);
                if ($resultado_ctrl['debito'])
                    break;
                

                //controles para facturas nacer txt viejo nomenclador
                $resultado_ctrl = comprobanteEstaRepetido($cuie, $periodo, $prestacionid, $idprestacion, $idrecepcion, $datosnomenclador, $elcomprobante, $fprestacion, $clavebeneficiario, $id_factura, $idvacuna);
                if ($resultado_ctrl['debito'])
                    break;

                $fprest_limite = str_replace('/', '-', $fprest_limite);
                $resultado_ctrl = fechaPrestacionXLimite($fprestacion, $fprest_limite);
                if ($resultado_ctrl['debito'])
                    break;

                //si es un nom q no se controla sale del bucle evitando las demas funciones 
                if (!nomencladoresQueNoSeControlan($datosnomenclador[3]))
                    break;
                
                $idpadron = $datosafi['idperiodo'];
                $resultado_ctrl = controlGrupoEtareo($idpadron, $fprestacion, $datosnomenclador, $clavebeneficiario);
                if ($resultado_ctrl['debito'])
                    break;

                $resultado_ctrl = controlMaximosPeriodicidad($clavebeneficiario, $fprestacion, $elcomprobante, $datosnomenclador, $idpadron);
                if ($resultado_ctrl['debito'])
                    break;

                // Si el afiliado se realizo las Prestaciones relacionadas requeridas previamente
                $resultado_ctrl = controlPracticasRelacionadas($clavebeneficiario, $lafechacomprobante, $datosnomenclador);
                if ($resultado_ctrl['debito'])
                    break;

                //CONTROLA SI TIENE REGISTRO DE VACUNAS CUANDO ES CONSULTA NI�OS ENTRE 1 Y 6 A�OS
                $edaddias = diferencia_dias_m($fnacimiento, $fprestacion);
                $resultado_ctrl = controlVacunacion($datosnomenclador, $afidni, $edaddias);
                if ($resultado_ctrl['debito'])
                    break;
            }

            break;
        }while (true);
        //REGISTRA EL DEBITO 
        if ($resultado_ctrl['debito']) {
            $detalle['id_error'] = $resultado_ctrl['id_error'];
            $detalle['mensaje'] = $resultado_ctrl['msj_error'];
            $detalle['nomenclador'] = $datosnomenclador;
            $detalle['comprobante'] = $res_granconsulta->fields['id_comprobante'];
            $detalle['cantidad'] = $res_granconsulta->fields['cantidad'];
            $datosdebito['cantidad'] = $res_granconsulta->fields['cantidad'];
            $detalle['afin'] = $afidni;
            $detalle['afinombre'] = $afiapellido . ", " . $afinombre;
            $resumen[$debitos] = $detalle;
            $debitos++;
            $datosdebito['id_factura'] = $id_factura;
            $datosdebito['resultado_ctrl'] = $resultado_ctrl;
            $datosdebito['id_comprobante'] = $elcomprobante;
            $datosdebito['id_nomenclador'] = $datosnomenclador[0];
            $datosdebito['monto_deb'] = $res_granconsulta->fields['precio_prestacion'];
            $datosdebito['documento_deb'] = $afidni;
            $datosdebito['apellido_deb'] = $afiapellido;
            $datosdebito['nombre_deb'] = $afinombre;
            $datosdebito['codigo_deb'] = $datosnomenclador[3];
            $datosdebito['observaciones_deb'] = "";

            $datosdebito['idprestacion'] = $idprestacion;
            $datosdebito['prestacionid'] = $prestacionid;

            insertarDebito($datosdebito);
        }
        $lineaprocesadas++;
        $res_granconsulta->movenext();
    }

    //$monto_prefactura_total = montoFactura($id_factura);
    $fin_proc = date("Y-m-d H:i:s");

    if ($debitos != 0) {
        ?>
        <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5">
            <tr bgcolor="#d3d3cd">
                <td align="center" colspan="7" style="padding-top: 20px;"><b>Rechazos Detectados</b></td>
            </tr>
            <tr bgcolor=#C0C0FF>
                <td align="center" >Nro</td>
                <td align="center" >Comprobante</td>
                <td align="center" >Practica</td>
                <td align="center" >Nombre Beneficiario</td>
                <td align="center" >Descripcion</td>
                <td align="center" >Cantidad</td>
                <td align="center" >Valor Unit</td>
            </tr>
            <?
            $aux = 1;
            foreach ($resumen as $detalledebito) {
                ?>
                <tr>
                    <td><?= $aux ?></td>    
                    <td><?= "Nro " . $detalledebito['comprobante'] ?></td>
                    <td><?= $detalledebito['nomenclador'][3] ?></td>
                    <td><?= $detalledebito['afinombre'] ?></td>
                    <td><?= $detalledebito['mensaje'] ?></td>
                    <td><?= $detalledebito['cantidad'] ?></td>
                    <td><?= $datosdebito['monto_deb'] ?></td>
                </tr>
                <?
                $aux++;
            }
            ?>
        </table>
        <?
        echo " Inicio:" . $inicio_proc . "- Fin:" . $fin_proc;
    } else {
        echo "Se comprobo la factura Nro $id_factura sin problemas.";
    }

    //Consulta los debitos pre existentes    
    $query = "SELECT d.id_comprobante,codigo_deb,nombre_deb,apellido_deb||', '||nombre_deb as nombre,d.cantidad,d.mensaje_baja,monto
                FROM FACTURACION.DEBITO D
                WHERE D.ID_FACTURA='$id_factura'";
    $debitosexistentes = sql($query);

    if ($debitosexistentes->RecordCount() > 0) {
        ?>
        <table  width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5">
            <tr bgcolor="#d3d3cd">
                <td align="center" colspan="7" style="padding-top: 20px;"><b>Debitos encontrados en la Importacion</b></td>
            </tr>
            <tr bgcolor=#C0C0FF>
                <td align="center" >Nro</td>
                <td align="center" >Comprobante</td>
                <td align="center" >Practica</td>
                <td align="center" >Nombre Beneficiario</td>
                <td align="center" >Descripcion</td>
                <td align="center" >Cantidad</td>
                <td align="center" >Valor Unit</td>
            </tr>

            <?
            $aux = 1;
            while (!$debitosexistentes->EOF) {
                ?>
                <tr>
                    <td><?= $aux ?></td>    
                    <td><?= $debitosexistentes->fields['id_comprobante'] ?></td>
                    <td><?= $debitosexistentes->fields['codigo_deb'] ?></td>
                    <td><?= $debitosexistentes->fields['nombre'] ?></td>
                    <td><?= $debitosexistentes->fields['mensaje_baja'] ?></td>
                    <td><?= $debitosexistentes->fields['cantidad'] ?></td>
                    <td><?= $debitosexistentes->fields['monto'] ?></td>
                </tr>
                <?
                $debitosexistentes->MoveNext();
                $aux++;
            }
            ?></table><?
    }

    sql("ROLLBACK", "Error en rollback", 0);
} catch (exception $e) {
    sql("ROLLBACK", "Error en rollback", 0);
    echo "Error: " . $e->getMessage() . "<br /><br /><br />";
    echo "Linea: " . $lineaprocesadas;
}
?>   