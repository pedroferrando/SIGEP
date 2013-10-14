<?php

include_once '../../config.php';
include_once '../../lib/bibliotecaTraeme.php';

include_once '../../clases/Comprobante.php';
include_once '../../clases/Prestacion.php';
include_once '../../clases/Nomenclador.php';
include_once '../../clases/BeneficiariosUad.php';

extract($_POST, EXTR_SKIP);
if ($parametros)
    extract($parametros, EXTR_OVERWRITE);


if (isset($cuie_elegido) && ($cuie_elegido != '-1')) {
    $listado_comprobantes_c = ComprobanteColeccion::Filtrar("tipo_nomenclador='PERINATAL_CATASTROFICO' and cuie='$cuie_elegido' and id_factura is null and marca=0");
    $listado_comprobantes_nc = ComprobanteColeccion::Filtrar("tipo_nomenclador='PERINATAL_NO_CATASTROFICO' and cuie='$cuie_elegido' and id_factura is null and marca=0");
    $listado_comprobantes = array_merge((array) $listado_comprobantes_c, (array) $listado_comprobantes_nc);
}



$id_usuario = $_ses_user['id'];
if ($id_usuario)
    $sql = "select n.cuie, nombreefector, upper(trim(com_gestion)) as com_gestion 
                                                                        from nacer.efe_conv n 
                                                                        inner join nacer.conv_nom cn using(id_efe_conv)
                                                                        inner join facturacion.smiefectores s on n.cuie=s.cuie 
                                                                        inner join sistema.usu_efec ue on ue.cuie=n.cuie
                                                                        where id_usuario='$id_usuario'
                                                                        and cn.activo = TRUE and n.activo='t'
                                                                        and (nom_perinatal_catastrofico=TRUE or nom_perinatal_nocatastrofico=TRUE)";
else {
    $sql = "select cn.id_nomenclador_detalle,s.cuie, nombreefector, upper(trim(com_gestion)) as com_gestion
                                                            from nacer.efe_conv n 
                                                            inner join nacer.conv_nom cn using(id_efe_conv)
                                                            inner join facturacion.smiefectores s on n.cuie=s.cuie
                                                            where and cn.activo = TRUE and n.activo='t' and (nom_perinatal_catastrofico=TRUE or nom_perinatal_nocatastrofico=TRUE)
                                                            order by nombreefector";
}
$res_efectores = sql($sql) or fin_pagina();


include_once('./listadoComprobantesPerinatal.tpl.php');
?>
