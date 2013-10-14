<?
require_once ("../../config.php");
include_once('lib_inscripcion.php');

extract($_POST, EXTR_SKIP);

if ($parametros)
    extract($parametros, EXTR_OVERWRITE);

($_POST['anio_mayor_nivel'] == '') ? $anio_mayor_nivel = 0 : $anio_mayor_nivel = $_POST['anio_mayor_nivel'];
($_POST['anio_mayor_nivel_madre'] == '') ? $anio_mayor_nivel_madre = 0 : $anio_mayor_nivel_madre = $_POST['anio_mayor_nivel_madre'];
($_POST['fecha_nac'] == '') ? $fecha_nac = '' : $fecha_nac = $_POST['fecha_nac'];
($_POST['fum'] == '') ? $fum = '30/12/1899' : $fum = $_POST['fum'];
($_POST['fecha_diagnostico_embarazo'] == '' || $_POST['fecha_diagnostico_embarazo'] == '0') ? $fecha_diagnostico_embarazo = '30/12/1899' : $fecha_diagnostico_embarazo = $_POST['fecha_diagnostico_embarazo'];
($_POST['fecha_probable_parto'] == '' || $_POST['fecha_probable_parto'] == '0') ? $fecha_probable_parto = '30/12/1899' : $fecha_probable_parto = $_POST['fecha_probable_parto'];
($_POST['fecha_efectiva_parto'] == '' || $_POST['fecha_efectiva_parto'] == '0') ? $fecha_efectiva_parto = '30/12/1899' : $fecha_efectiva_parto = $_POST['fecha_efectiva_parto'];
($_POST['fecha_inscripcion'] == '') ? $fecha_inscripcion = '' : $fecha_inscripcion = $_POST['fecha_inscripcion'];
$edad = $_POST['edades'];

$num_doc = str_replace(' ', '', $num_doc);
$num_doc = intval($num_doc);

$estado_intermedio = '';
$estado_envio_ins = 'n';
$ape_nom = '';
$remediar = '';
$uad_benef = '';
$prov_uso = '';
$agentes_sql = '';
$agentes_sql2 = '';
$agentes = 'n';
$queryfunciones = "SELECT accion,nombre
		 FROM sistema.funciones
                 WHERE habilitado='s' and (pagina='ins_admin' or pagina='all')";
$res_fun = sql($queryfunciones) or fin_pagina();
while (!$res_fun->EOF) {
    if ($res_fun->fields['nombre'] == 'Guarda Remediar') {
        $remediar = 's'; //$res_fun->fields['accion'];
    } elseif ($res_fun->fields['nombre'] == 'Estados') {
        $estado_nuevo = 's'; //$res_fun->fields['accion'];
        $estado_intermedio = "estado_envio='p',";
        $estado_envio_ins = 'p';
    } elseif ($res_fun->fields['nombre'] == 'Otros Ape-Nom') {
        $ape_nom = 's';
        $ape_nom_update = "";
    } elseif ($res_fun->fields['nombre'] == 'Uad Benef') {
        $uad_benef = 's';
    } elseif ($res_fun->fields['nombre'] == 'Provincia') {
        $prov_uso = $res_fun->fields['accion'];
    } elseif ($res_fun->fields['nombre'] == 'Datos Agente') {
        $agentes = $res_fun->fields['accion'];
    }
    $res_fun->movenext();
}

//*******************************************************************************
// Insert de Beneficiarios

if ($_POST['guardar'] == "Guardar Planilla") {
    $fecha_carga = date("Y-m-d H:m:s");
    $usuario = $_ses_user['id'];

    $fecha_nac = Fecha_db($fecha_nac);

    $fecha_inscripcion = Fecha_db($_POST['fecha_inscripcion']);
    $clave_beneficiario = crearClaveBeneficiario($_ses_user['id'], 's');
    $usuario = substr($usuario, 0, 9);


    if ($menor_embarazada) {
        $fum = Fecha_db($fum);

        $fecha_diagnostico_embarazo = Fecha_db($fecha_diagnostico_embarazo);
        if ($fecha_diagnostico_embarazo == "") {
            $fecha_diagnostico_embarazo = "1899-12-30";
        }

        $fecha_probable_parto = Fecha_db($fecha_probable_parto);
        if ($fecha_probable_parto == "") {
            $fecha_probable_parto = "1899-12-30";
            $fecha_efectiva_parto = $fecha_probable_parto;
        }
        $agregadosalquery = ",fecha_diagnostico_embarazo,semanas_embarazo,fecha_probable_parto,score_riesgo,fum";
        $agregadosalvalue = ",'$fecha_diagnostico_embarazo','$semanas_embarazo', '$fecha_probable_parto','$score_riesgo','$fum'";
    }
    if ($edad <= '9') {
        $responsable = $_POST['responsable'];
        $agregadosalquery .= ",menor_convive_con_adulto,responsable";
        $agregadosalvalue .= ",upper('$menor_convive_con_adulto'),upper('$responsable'),upper('$tipo_doc_madre'),'$nro_doc_madre',upper('$apellido_madre'),upper('$nombre_madre'),
                               upper('$alfabeta_madre'),upper('$estudios_madre'),upper('$estadoest_madre'),'$anio_mayor_nivel_madre'";
        if ($responsable == 'PADRE') {
            $agregadosalquery .= ",tipo_doc_padre,nro_doc_padre,apellido_padre,nombre_padre,alfabeta_padre,estudios_padre,estadoest_padre,anio_mayor_nivel_padre";
        } elseif ($responsable == 'MADRE') {
            $agregadosalquery .= ",tipo_doc_madre,nro_doc_madre,apellido_madre,nombre_madre,alfabeta_madre,estudios_madre,estadoest_madre,anio_mayor_nivel_madre";
        } elseif ($responsable == 'TUTOR') {
            $agregadosalquery .= ",tipo_doc_tutor,nro_doc_tutor,apellido_tutor,nombre_tutor,alfabeta_tutor,estudios_tutor,estadoest_tutor,anio_mayor_nivel_tutor";
        }
    }

    $claveVerifSql = "Select id_beneficiarios from uad.beneficiarios where clave_beneficiario = '" . $clave_beneficiario . "'";
    $claveVerifResult = sql($claveVerifSql);
    if ($claveVerifResult->NumRows() > 0) {
        echo "Error clave de beneficiario ya existe";
        exit();
    } else {
        if (!beneficiarioInscriptoUad($clase_doc, $tipo_doc, $num_doc)) {
            $query = "insert into uad.beneficiarios
                    (estado_envio,clave_beneficiario,tipo_transaccion,apellido_benef,apellido_benef_otro,nombre_benef,nombre_benef_otro,
                     clase_documento_benef,tipo_documento,numero_doc,sexo,fecha_nacimiento_benef,provincia_nac,localidad_nac,pais_nac,indigena,id_tribu,id_lengua,
                     alfabeta,estudios,anio_mayor_nivel,cuie_ea,cuie_ah,calle,numero_calle,piso,dpto,manzana,entre_calle_1,entre_calle_2,
                     telefono,departamento,localidad,municipio,barrio,cod_pos,observaciones,fecha_inscripcion,fecha_carga,usuario_carga,
                     activo,tipo_ficha,mail,celular,otrotel,estadoest,discv,disca,discmo,discme,discha,otradisc,obsgenerales,   
                    apellidoagente,nombreagente,centro_inscriptor,dni_agente,cod_uad,cod_ci $agregadosalquery)
                    values
                    ('p','$clave_beneficiario','A',upper('$apellido'),upper('$apellido_otro'),upper('$nombre'),upper('$nombre_otro'),
                    upper('$clase_doc'),upper('$tipo_doc'),'$num_doc',upper('$sexo'),'$fecha_nac',upper('$provincia_nac'),upper('$localidad_proc'),upper('$paisn'),upper('$indigena'),upper('$id_tribu'),$id_lengua,
                    upper('$alfabeta'),upper('$estudios'),'$anio_mayor_nivel',upper('$cuie'),upper('$cuie'),upper('$calle'),'$numero_calle','$piso',upper('$dpto'), '$manzana',upper('$entre_calle_1'),upper('$entre_calle_2'),
                    '$telefono',upper('$departamenton'),upper('$localidadn'),upper('$municipion'),upper('$barrion'),'$cod_posn',upper('$observaciones'), '$fecha_inscripcion','$fecha_carga',upper('$usuario'),
                    '1','2',upper('$mail'),'$celular','$otrotel',upper('$estadoest'),upper('$discv'),upper('$disca'),upper('$discmo'),upper('$discme'),upper('$discha'),upper('$otradisc'),upper('$obsgenerales'),
                    upper('$apellidoagente'),upper('$nombreagente'),upper('$cuie_agente'),upper('$num_doc_agente'), substr('$clave_beneficiario',3,3),substr('$clave_beneficiario',6,5) $agregadosalvalue) returning id_beneficiarios";
            $db->StartTrans();
            $id_planilla = sql($query, "Error al insertar la Planilla") or fin_pagina();
            $id_planilla = $id_planilla->fields[0];
            $accion = "Se guardo la Planilla";
            $db->CompleteTrans();
        } else {?>
            <div align="center"><h2>Error Beneficiario con <?php echo $tipo_doc; ?> - <?php echo $num_doc; ?> ya existe.</h2></div>
            <?php
        }
    }
}//FIN Insert

if ($_POST['guardar_editar'] == "Guardar") {    
    $fecha_carga = date("Y-m-d H:m:s");
    $usuario = $_ses_user['id'];
    $usuario = substr($usuario, 0, 9);

    $fecha_nac = Fecha_db($fecha_nac);

    $fecha_inscripcion = Fecha_db($_POST['fecha_inscripcion']);
    $clave_beneficiario = $_POST['clave_beneficiario'];

    if ($_POST['numero_doc']) {
        $num_doc = str_replace(' ', '', $_POST['numero_doc']);
    }

    if ($sexo == 'F') {
        if ($menor_embarazada) {
            $fum = Fecha_db($fum);

            $fecha_diagnostico_embarazo = Fecha_db($fecha_diagnostico_embarazo);
            if ($fecha_diagnostico_embarazo == "") {
                $fecha_diagnostico_embarazo = "1899-12-30";
            }

            $fecha_probable_parto = Fecha_db($fecha_probable_parto);
            if ($fecha_probable_parto == "") {
                $fecha_probable_parto = "1899-12-30";
                $fecha_efectiva_parto = $fecha_probable_parto;
            }
        } else {
            $fum = "1899-12-30";
            $fecha_diagnostico_embarazo = "1899-12-30";
            $fecha_probable_parto = "1899-12-30";
            $semanas_embarazo = '0';
            //$score_riesgo = '0';
        }
        $agregadosalquery = ",fecha_diagnostico_embarazo='$fecha_diagnostico_embarazo'
                             ,semanas_embarazo='$semanas_embarazo'
                             ,fecha_probable_parto='$fecha_probable_parto'
                             ,fum='$fum'";
    }
    $agregadosalquery .= ",score_riesgo='$score_riesgo'";
    if ($edad <= '9') {
        $responsable = $_POST['responsable'];
        $agregadosalquery .= ",menor_convive_con_adulto=upper('$menor_convive_con_adulto')
                              ,responsable=upper('$responsable')";
        if ($responsable == 'PADRE') {
            $agregadosalquery .= ",tipo_doc_padre=upper('$tipo_doc_madre')
                                  ,nro_doc_padre='$nro_doc_madre'
                                  ,apellido_padre=upper('$apellido_madre')
                                  ,nombre_padre=upper('$nombre_madre')
                                  ,alfabeta_padre=upper('$alfabeta_madre')
                                  ,estudios_padre=upper('$estudios_madre')
                                  ,estadoest_padre=upper('$estadoest_madre')
                                  ,anio_mayor_nivel_padre='$anio_mayor_nivel_madre'";
            $agregadosalquery .= getStringBorradoResponsable('MADRE');
            $agregadosalquery .= getStringBorradoResponsable('TUTOR');
        } elseif ($responsable == 'MADRE') {
            $agregadosalquery .= ",tipo_doc_madre=upper('$tipo_doc_madre')
                                  ,nro_doc_madre='$nro_doc_madre'
                                  ,apellido_madre=upper('$apellido_madre')
                                  ,nombre_madre=upper('$nombre_madre')
                                  ,alfabeta_madre=upper('$alfabeta_madre')
                                  ,estudios_madre=upper('$estudios_madre')
                                  ,estadoest_madre=upper('$estadoest_madre')
                                  ,anio_mayor_nivel_madre='$anio_mayor_nivel_madre'";
            $agregadosalquery .= getStringBorradoResponsable('PADRE');
            $agregadosalquery .= getStringBorradoResponsable('TUTOR');
        } elseif ($responsable == 'TUTOR') {
            $agregadosalquery .= ",tipo_doc_tutor=upper('$tipo_doc_madre')
                                    ,nro_doc_tutor='$nro_doc_madre'
                                    ,apellido_tutor=upper('$apellido_madre')
                                    ,nombre_tutor=upper('$nombre_madre')
                                    ,alfabeta_tutor=upper('$alfabeta_madre')
                                    ,estudios_tutor=upper('$estudios_madre')
                                    ,estadoest_tutor=upper('$estadoest_madre')
                                    ,anio_mayor_nivel_tutor='$anio_mayor_nivel_madre'";
            $agregadosalquery .= getStringBorradoResponsable('MADRE');
            $agregadosalquery .= getStringBorradoResponsable('PADRE');
        }
    }else{
        //se limpian los datos de responsables
        $agregadosalquery .= getStringBorradoResponsable();
    }

    //Fecha: 01/08/2013 Autor: Pedro Ferrando
    //Control del tipo de transaccion para que no lo cambie cuando se realiza una
    //modificacion o correccion de datos cuando se trata de una Alta que aun no ha sido enviada
    $qry = "SELECT tipo_transaccion,estado_envio
	    FROM uad.beneficiarios
	    LEFT JOIN nacer.efe_conv ON beneficiarios.cuie_ea=efe_conv.cuie 
  	    WHERE id_beneficiarios=$id_planilla";

    $resultqry = sql($qry, "Error en consulta de Tipo de Transaccion") or fin_pagina();
    $trans_tip = trim($resultqry->fields['tipo_transaccion']);
    $estado_env = trim($resultqry->fields['estado_envio']);
    if(($estado_env=='n' or $estado_env=='p') and $trans_tip=='A') {
        $tipo_transaccion='A';		
    }else 
        $tipo_transaccion='M';
    //fin del control    
        
    $query = "update uad.beneficiarios
                SET estado_envio='p'
                ,tipo_transaccion=upper('$tipo_transaccion')
                ,apellido_benef=upper('$apellido')
                ,apellido_benef_otro=upper('$apellido_otro')
                ,nombre_benef=upper('$nombre')
                ,nombre_benef_otro=upper('$nombre_otro')
                ,clase_documento_benef=upper('$clase_doc')
                ,tipo_documento=upper('$tipo_doc')
                ,numero_doc='$num_doc'
                ,sexo=upper('$sexo')
                ,fecha_nacimiento_benef='$fecha_nac'
                ,provincia_nac=upper('$provincia_nac')
                ,localidad_nac=upper('$localidad_proc')
                ,pais_nac=upper('$paisn')
                ,indigena=upper('$indigena')
                ,id_tribu=upper('$id_tribu')
                ,id_lengua=$id_lengua
                ,alfabeta=upper('$alfabeta')
                ,estudios=upper('$estudios')
                ,anio_mayor_nivel='$anio_mayor_nivel'
                ,cuie_ea=upper('$cuie')
                ,cuie_ah=upper('$cuie')
                ,calle=upper('$calle')
                ,numero_calle='$numero_calle'
                ,piso='$piso'
                ,dpto=upper('$dpto')
                ,manzana='$manzana'
                ,entre_calle_1=upper('$entre_calle_1')
                ,entre_calle_2=upper('$entre_calle_2')
                ,telefono='$telefono'
                ,departamento=upper('$departamenton')
                ,localidad=upper('$localidadn')
                ,municipio=upper('$municipion')
                ,barrio=upper('$barrion')
                ,cod_pos='$cod_posn'
                ,observaciones=upper('$observaciones')
                ,fecha_inscripcion='$fecha_inscripcion'
                ,fecha_carga='$fecha_carga'
                ,usuario_carga=upper('$usuario')
                ,mail=upper('$mail')
                ,celular='$celular'
                ,otrotel='$otrotel'
                ,estadoest=upper('$estadoest')
                ,discv=upper('$discv')
                ,disca=upper('$disca')
                ,discmo=upper('$discmo')
                ,discme=upper('$discme')
                ,discha=upper('$discha')
                ,otradisc=upper('$otradisc')
                ,obsgenerales=upper('$obsgenerales')
                ,apellidoagente=upper('$apellidoagente')
                ,nombreagente=upper('$nombreagente')
                ,centro_inscriptor=upper('$cuie_agente')
                ,dni_agente=upper('$num_doc_agente')
                $agregadosalquery
                WHERE clave_beneficiario='$clave_beneficiario'";
    $db->StartTrans();
    sql($query, "Error al actualizar la Planilla") or fin_pagina();
    $accion = "Se actualizo la Planilla";
    $db->CompleteTrans();
}


//*****************************************************************************
// Borrado de Beneficiarios

if ($_POST['borrar'] == "Borrar") {

    if ($tipo_transaccion == 'B') {
        $query = "UPDATE uad.beneficiarios  SET activo='0', tipo_transaccion= 'B', estado_envio='n'  WHERE (id_beneficiarios= $id_planilla)";
        sql($query, "Error al insertar la Planilla") or fin_pagina();

        $accion = "Se elimino la planilla $id_planilla";
    }
} //FIN Borrado Beneficiarios
//*****************************************************************************
//comienza agregado por sistemas Misiones- SS
if ($_POST['guardar'] == "Pasar a No Enviados") {
    $db->StartTrans();
    $fecha_carga = date("Y-m-d H:m:s");
    $usuario = $_ses_user['id'];
    $usuario = substr($usuario, 0, 9);
    $query = "update uad.beneficiarios set estado_envio='n',fecha_verificado='$fecha_carga',usuario_verificado='$usuario'
                where id_beneficiarios=$id_planilla";

    sql($query, "Error al insertar la Planilla") or fin_pagina();

    $accion = "Se guardo la Planilla en estado No Enviados";

    $db->CompleteTrans();
}//termina agregado por sistemas Misiones -SS
//******************************************************************************
//BUSCA DATIOS DEL BENEFICIARIO

if($id_planilla){
    $query = "SELECT b.*, s.nombreefector, s.cuie,
                     date_part('year',age(now(),b.fecha_nacimiento_benef)) as edad_benef,
                     CASE WHEN a.clavebeneficiario IS NOT NULL THEN 1 ELSE 0 END AS existe_en_smi,
                     usrC.nombre||' '||usrC.apellido AS nombre_us_carga, 
                     usrC.comentarios AS coment_us_carga, usrC.mail AS mail_us_carga,
                     usrV.nombre||' '||usrV.apellido AS nombre_us_verif, 
                     usrV.comentarios AS coment_us_verif, usrV.mail AS mail_us_verif
              FROM uad.beneficiarios b 
              LEFT JOIN nacer.smiafiliados a on b.clave_beneficiario=a.clavebeneficiario
              LEFT JOIN facturacion.smiefectores s  on b.cuie_ea=s.cuie 
              LEFT JOIN sistema.usuarios usrC ON CAST(b.usuario_carga AS INTEGER)=usrC.id_usuario 
              LEFT JOIN sistema.usuarios usrV ON CAST(b.usuario_verificado AS INTEGER)=usrV.id_usuario 
              WHERE id_beneficiarios=$id_planilla";
    $queryCategoria = $query;
    
    $resultado = sql($queryCategoria, "Error al traer el Beneficiario") or fin_pagina();
    $existe_en_smi = $resultado->fields['existe_en_smi'];
    $semanas_embarazo = $resultado->fields['semanas_embarazo'];
    $pais_nac = $resultado->fields['pais_nac'];
    $paisn = $resultado->fields['pais_nac'];
    $departamento = $resultado->fields['departamento'];
    $localidad = $resultado->fields['localidad'];
    $municipio = $resultado->fields['municipio'];
    $barrio = $resultado->fields['barrio'];
    $barrion = $resultado->fields['barrio'];
    $estudios = $resultado->fields['estudios'];
    $anio_mayor_nivel = $resultado->fields['anio_mayor_nivel'];
    $indigena = $resultado->fields['indigena'];
    $id_tribu = $resultado->fields['id_tribu'];
    $id_lengua = $resultado->fields['id_lengua'];
    $responsable = $resultado->fields['responsable'];
    $menor_convive_con_adulto = $resultado->fields['menor_convive_con_adulto'];
    $tipo_doc_madre = $resultado->fields['tipo_doc_madre'];
    $nro_doc_madre = $resultado->fields['nro_doc_madre'];
    $apellido_madre = $resultado->fields['apellido_madre'];
    $nombre_madre = $resultado->fields['nombre_madre'];
    $estudios_madre = $resultado->fields['estudios_madre'];
    $anio_mayor_nivel_madre = $resultado->fields['anio_mayor_nivel_madre'];
    $sexo = $resultado->fields['sexo'];
    $alfabeta = $resultado->fields['alfabeta'];
    $estudios = $resultado->fields['estudios'];
    $clave_beneficiario = $resultado->fields['clave_beneficiario'];
    $trans = $resultado->fields['tipo_transaccion'];
    $mail = $resultado->fields['mail'];
    $celular = $resultado->fields['celular'];
    $otrotel = $resultado->fields['otrotel'];
    $estadoest = $resultado->fields['estadoest'];
    $discv = $resultado->fields['discv'];
    $disca = $resultado->fields['disca'];
    $discmo = $resultado->fields['discmo'];
    $discme = $resultado->fields['discme'];
    $discha = $resultado->fields['discha'];
    $otradisc = $resultado->fields['otradisc'];
    $obsgenerales = $resultado->fields['obsgenerales'];
    $estadoest_madre = $resultado->fields['estadoest'];
    $menor_embarazada = $resultado->fields['menor_embarazada'];
    $apellidoagente = $resultado->fields['apellidoagente'];
    $nombreagente = $resultado->fields['nombreagente'];
    $cuie_agente = $resultado->fields['centro_inscriptor'];
    $num_doc_agente = $resultado->fields['dni_agente'];
    $clase_doc = $resultado->fields['clase_documento_benef'];
    $tipo_doc = $resultado->fields['tipo_documento'];
    // Marca Borrado al beneficiario.
    if ($trans == 'B') {
        $trans = "Borrado";
    }

    $res_factura = $resultado;
    if ($tipo_transaccion == '') {
        $tipo_transaccion = $res_factura->fields['tipo_transaccion'];
    }
    $es_padre = $res_factura->fields['apellido_padre'];
    $es_madre = $res_factura->fields['apellido_madre'];
    $es_tutor = $res_factura->fields['apellido_tutor'];
    $responsable = $res_factura->fields['responsable'];
    $tipo_ficha = $res_factura->fields['tipo_ficha'];
    if ($responsable == "PADRE") {
        //$responsable = "PADRE";
        $tipo_doc_madre = $res_factura->fields['tipo_doc_padre'];
        $nro_doc_madre = $res_factura->fields['nro_doc_padre'];
        $apellido_madre = $res_factura->fields['apellido_padre'];
        $nombre_madre = $res_factura->fields['nombre_padre'];
        $alfabeta_madre = $res_factura->fields['alfabeta_padre'];
        $estudios_madre = $res_factura->fields['estudios_padre'];
        $estadoest_madre = $res_factura->fields['estadoest_padre'];
        $anio_mayor_nivel_madre = $res_factura->fields['anio_mayor_nivel_padre'];
        $menor_convive_con_adulto = $res_factura->fields['menor_convive_con_adulto'];
    } elseif ($responsable == "MADRE") {
        //$responsable = "MADRE";
        $tipo_doc_madre = $res_factura->fields['tipo_doc_madre'];
        $nro_doc_madre = $res_factura->fields['nro_doc_madre'];
        $apellido_madre = $res_factura->fields['apellido_madre'];
        $nombre_madre = $res_factura->fields['nombre_madre'];
        $alfabeta_madre = $res_factura->fields['alfabeta_madre'];
        $estudios_madre = $res_factura->fields['estudios_madre'];
        $estadoest_madre = $res_factura->fields['estadoest_madre'];
        $anio_mayor_nivel_madre = $res_factura->fields['anio_mayor_nivel_madre'];
        $menor_convive_con_adulto = $res_factura->fields['menor_convive_con_adulto'];
    } elseif ($responsable == "TUTOR") {
        //$responsable = "TUTOR";
        $tipo_doc_madre = $res_factura->fields['tipo_doc_tutor'];
        $nro_doc_madre = $res_factura->fields['nro_doc_tutor'];
        $apellido_madre = $res_factura->fields['apellido_tutor'];
        $nombre_madre = $res_factura->fields['nombre_tutor'];
        $alfabeta_madre = $res_factura->fields['alfabeta_tutor'];
        $estudios_madre = $res_factura->fields['estudios_tutor'];
        $estadoest_madre = $res_factura->fields['estadoest_tutor'];
        $anio_mayor_nivel_madre = $res_factura->fields['anio_mayor_nivel_tutor'];
        $menor_convive_con_adulto = $res_factura->fields['menor_convive_con_adulto'];
    }

    $estado_envio = $res_factura->fields['estado_envio'];
    $usuario_carga = $res_factura->fields['usuario_carga'];
    $fecha_carga = $res_factura->fields['fecha_carga'];
    $fecha_verificado = $res_factura->fields['fecha_verificado'];
    $us_carga_nombre = $res_factura->fields['nombre_us_carga'];
    $us_verif_nombre = $res_factura->fields['nombre_us_verif'];
    $us_carga_coment = $res_factura->fields['coment_us_carga'];
    $us_verif_coment = $res_factura->fields['coment_us_verif'];
    $us_carga_mail = $res_factura->fields['mail_us_carga'];
    $us_verif_mail = $res_factura->fields['mail_us_verif'];
    $num_doc = $res_factura->fields['numero_doc'];
    $apellido = $res_factura->fields['apellido_benef'];
    $nombre = $res_factura->fields['nombre_benef'];
    $fecha_nac = fecha($res_factura->fields['fecha_nacimiento_benef']);
    $edad = $res_factura->fields['edad_benef'];
    if ($nombre_otro == '') {
        $nombre_otro = $res_factura->fields['nombre_benef_otro'];
    }
    if ($apellido_otro == '') {
        $apellido_otro = $res_factura->fields['apellido_benef_otro'];
    }
    $fum = fecha($res_factura->fields['fum']);
    $fecha_diagnostico_embarazo = fecha($res_factura->fields['fecha_diagnostico_embarazo']);
    $semanas_embarazo = $res_factura->fields['semanas_embarazo'];

    $fecha_probable_parto = fecha($res_factura->fields['fecha_probable_parto']);

    $calle = $res_factura->fields['calle'];
    $numero_calle = $res_factura->fields['numero_calle'];
    $anio_mayor_nivel = $res_factura->fields['anio_mayor_nivel'];
    $piso = $res_factura->fields['piso'];
    $dpto = $res_factura->fields['dpto'];
    $manzana = $res_factura->fields['manzana'];
    $entre_calle_1 = $res_factura->fields['entre_calle_1'];
    $entre_calle_2 = $res_factura->fields['entre_calle_2'];
    $telefono = $res_factura->fields['telefono'];
    $cod_pos = $res_factura->fields['cod_pos'];
    $fecha_inscripcion = fecha($res_factura->fields['fecha_inscripcion']);
    $observaciones = $res_factura->fields['observaciones'];
    $cuie = $res_factura->fields['cuie'];
    $score_riesgo = $res_factura->fields['score_riesgo'];
    $pais_nac = $res_factura->fields['pais_nac'];
    $paisn = $res_factura->fields['pais_nac'];
    $provincia_nac = $res_factura->fields['provincia_nac'];
    $localidad_proc = $res_factura->fields['localidad_nac'];
    $departamento = $res_factura->fields['departamento'];
    $localidad = $res_factura->fields['localidad'];
    $municipio = $res_factura->fields['municipio'];
    $barrio = $res_factura->fields['barrio'];
    $indigena = $res_factura->fields['indigena'];
    $id_tribu = $res_factura->fields['id_tribu'];
    $id_lengua = $res_factura->fields['id_lengua'];
    //$responsable = $res_factura->fields['responsable'];
    $mail = $res_factura->fields['mail'];
    $celular = $res_factura->fields['celular'];
    $otrotel = $res_factura->fields['otrotel'];
    $estadoest = $res_factura->fields['estadoest'];
    $discv = $res_factura->fields['discv'];
    $disca = $res_factura->fields['disca'];
    $discmo = $res_factura->fields['discmo'];
    $discme = $res_factura->fields['discme'];
    $discha = $res_factura->fields['discha'];
    $otradisc = $res_factura->fields['otradisc'];
    $obsgenerales = $res_factura->fields['obsgenerales'];
    $menor_convive_con_adulto = $res_factura->fields['menor_convive_con_adulto'];
    $apellidoagente = $res_factura->fields['apellidoagente'];
    $nombreagente = $res_factura->fields['nombreagente'];
    $cuie_agente = $res_factura->fields['centro_inscriptor'];
    $num_doc_agente = $res_factura->fields['dni_agente'];
    if ($fum == '' || $fum == '0') {
        $fum = '30/12/1899';
    }
    if ($fecha_diagnostico_embarazo == '' || $fecha_diagnostico_embarazo == '0') {
        $fecha_diagnostico_embarazo = '30/12/1899';
    }
    if ($fecha_diagnostico_embarazo == '30/12/1899' || $sexo== 'M') {
        $embarazada = "none";
        $checked_embarazo = "";
    } else {
        $embarazada = "inline";
        $checked_embarazo = "checked='checked'";
    }
}else{
    // es un alta de beneficiario
    $paisn = "ARGENTINA";
}//FIN
//******************************************************************************
// Query para traer los paises para luego ser utilizado con AJAX para que no refresque la pagina.
$strConsulta = "select id_pais, upper(nombre)as nombre from uad.pais order by nombre";
$result = @pg_exec($strConsulta);
$pais_nacq = '<option value="-1"> Seleccione Pais </option>';

while ($fila = pg_fetch_array($result)) {
    $pais_nacq.='<option value="' . $fila["nombre"] . '"';
    if ($pais_nac == $fila["nombre"] || $paisn == $fila["nombre"]) {
        $pais_nacq.='selected';
        $paisn = $fila["nombre"];
    }
    $pais_nacq.='>' . $fila["nombre"] . '</option>';
} // FIN WHILE	

if ($prov_uso == 'Misiones') {
//DEPARTAMENTO
    $provincia = $prov_uso;
    $departamenton = $departamento;
    $strConsulta = "select d.id_departamento, upper(d.nombre)as nombre from uad.departamentos d inner join uad.provincias p using(id_provincia)
                    where upper(p.nombre) = upper('$provincia') order by nombre";
    $result = @pg_exec($strConsulta);
    $departamento = '<option value="-1"> Seleccione Departamento </option>';
    $opciones2 = '<option value="-1" > Seleccione Localidad </option>';
    $opciones3 = '<option value="-1"> Seleccione Municipio </option>';
    $opciones4 = '<option value="-1"> Seleccione Barrio </option>
            <option value="S/D" ';
    if ($barrio == "S/D") {
        $opciones4.='selected';
        $barrion = $fila["nombre"];
    }$opciones4.='> S/D</option>';
    $opciones5 = '<option value="-1"> Codigo Postal  </option>';
    //}	
    while ($fila = pg_fetch_array($result)) {
        $departamento.='<option value="' . $fila["id_departamento"] . '"';
        if ($departamenton == $fila["nombre"]) {
            $departamento.='selected';
            $departamenton = $fila["nombre"];
        }
        $departamento.='>' . $fila["nombre"] . '</option>';
    } // FIN WHILE
    //LOCALIDAD
    $strConsulta = "select upper(l.nombre)as nombre
                    from uad.localidades l
                    inner join uad.departamentos d on l.id_departamento=d.id_departamento
                    where upper(d.nombre) = upper('$departamenton')";
    $result = @pg_exec($strConsulta);
    while ($fila = pg_fetch_array($result)) {
        $opciones2.='<option value="' . $fila["nombre"] . '"';
        if ($localidad == $fila["nombre"]) {
            $opciones2.='selected';
            $localidadn = $fila["nombre"];
        }
        $opciones2.='>' . $fila["nombre"] . '</option>';
    }

    //CODIGO POSTAL
    $strConsulta = "select codpost.codigopostal
                    from uad.codpost
                    inner join uad.localidades on codpost.id_localidad=localidades.idloc_provincial
                    where localidades.nombre='$localidad'";
    $result = @pg_exec($strConsulta);
    while ($fila = pg_fetch_array($result)) {
        $opciones5.='<option value="' . $fila["codigopostal"] . '"';
        if ($cod_pos == $fila["codigopostal"]) {
            $opciones5.='selected';
            $cod_posn = $fila["codigopostal"];
        }
        $opciones5.='>' . $fila["codigopostal"] . '</option>';
    }

    //MUNICIPIO
    $strConsulta = "select upper(nombre)as nombre
                    from uad.municipios
                    inner join uad.codpost on codpost.id_codpos=municipios.id_codpos
                    where codpost.codigopostal='$cod_posn'
                    order by nombre";
    $result = @pg_exec($strConsulta);
    while ($fila = pg_fetch_array($result)) {
        $opciones3.='<option value="' . $fila["nombre"] . '"';
        if ($municipio == $fila["nombre"]) {
            $opciones3.='selected';
            $municipion = $fila["nombre"];
        }
        $opciones3.='>' . $fila["nombre"] . '</option>';
    }

    //BARRIO	
    $strConsulta = "select upper(b.nombre)as nombre
                    from uad.barrios b
                    inner join uad.municipios m on m.idmuni_provincial=b.id_municipio
                    where upper(m.nombre)=upper('$municipio') 
                    order by b.nombre";
    $result = @pg_exec($strConsulta);
    while ($fila = pg_fetch_array($result)) {
        $opciones4.='<option value="' . $fila["nombre"] . '"';
        if ($barrio == $fila["nombre"]) {
            $opciones4.='selected';
            $barrion = $fila["nombre"];
        }
        $opciones4.='>' . $fila["nombre"] . '</option>';
    }
}

// Femenino mayor de 19 años, muestra la información de embarazo pero no la información de menor vive con adulto
if (($sexo == 'F')) {
    if ($edad > 9) {
        //$embarazada = inline;
        $datos_resp = none;
        $mva1 = none;
        $memb = inline;
        if (!$id_planilla) {
            if ($_POST['semanas_embarazo'] == "") {
                $semanas_embarazo = 0;
            } else {
                $semanas_embarazo = $_POST['semanas_embarazo'];
            }
        }// Femenino menor de 9 años, muestra la información de menor vive con adulto y no la de embarazo
    } elseif ($edad <= 9) {
        $embarazada = "none";
        $mva1 = 'table-cell';
        $datos_resp = inline;
        $memb = none;
        //$menor_embarazada = "none";
    }
}

// Masculino menor de 9 años inclusive, muestra la información de menor vive con adulto y no la de embarazo
if (($edad <= 9) && ($sexo == 'M')) {
    $embarazada = "none";
    $datos_resp = inline;
    $mva1 = "table-cell";
    $memb = none;
} // Masculino mayor de 9 años, no muesta la información de embarazo ni tampoco la de menor vive con adulto.
elseif (($edad > 9 ) && ($sexo == 'M')) {
    $mva1 = none;
    $datos_resp = none;
    $embarazada = "none";
    $memb = none;
} // FIN

echo $html_header;

echo "<script src='../../lib/jquery.min.js' type='text/javascript'></script>";
echo "<script src='../../lib/jquery/jquery-ui.js' type='text/javascript'></script>";
echo "<link rel='stylesheet' href='../../lib/jquery/ui/jquery-ui.css'/>";
echo "<script src='../../lib/jquery/ui/jquery.ui.datepicker-es.js' type='text/javascript'></script>";
?>
<script>

    $( window ).load(function() {
        bindFormularioRemediar();
    });



    var nav4 = window.Event ? true : false;
    function acceptNum(evt) {
        var key = nav4 ? evt.which : evt.keyCode;
        return (key < 13 || (key >= 48 && key <= 57));
    }
    // Script para el manejo de combobox de Departamento - Localidad - Codigo Postal - Municipio y Barrio
    $(document).ready(function() {
        var num_doc_original = $("input#numero_doc").val();
        var clase_doc_original = $("#clase_doc").val();

        $("#fecha_nac").datepicker({
            onSelect: function() {
                var fechita = $("#fecha_nac").val();
                esFechaValida(fechita);
                edad(fechita);
                cambiar_pantalla();
            }
        });
        $('#fecha_nac').on('blur', function() {
            var fechita = $("#fecha_nac").val();
            esFechaValida(fechita);
            edad(fechita);
            cambiar_pantalla();
        });
        if ($("#fecha_inscripcion").attr('readonly') != 'readonly') {
            $("#fecha_inscripcion").datepicker({minDate: -10, maxDate: "+0D"});
            $("#fecha_inscripcion").on('blur', function() {
                if ($("#fecha_inscripcion").val() != "") {
                    var fechaingresada = $("#fecha_inscripcion").val();
                    var restadehoy = DiferenciaFechas(fechaingresada) * (365);
                    if (restadehoy < 0 || restadehoy >= 11) {
                        alert("Fecha de inscripcion erronea");
                        $("#fecha_inscripcion").val("");
                    }
                }
            });
        }

        $("#fecha_diagnostico_embarazo").datepicker();
        $("#fum").datepicker();
        $("#fecha_probable_parto").datepicker();
        $(".btn_busca").click(function(event) {
            event.preventDefault()
        });
        $("#numero_doc").keypress(function(event) {
            if (event.which == 13) {
                event.preventDefault();
            }
        });
        $("#numero_doc").blur(function() {
            <? if (($id_planilla) && ($tipo_transaccion == "M")) { ?>

                            $.post("ins_admin.php", {"b": "b", "num_doc": $("#numero_doc").val(), "tipo_doc": $("#tipo_doc").val(), "clase_doc": $("#clase_doc").val()}, function(data) {
                                var existe = $(data).find('input#id_planilla').val();
                                if (existe != "") {
                                    alert("Ya existe un Beneficiario con ese Numero de Documento");
                                    $("input#numero_doc").val(num_doc_original);
                                }
                            });
            <? } ?>
        });
        $('#clase_doc').on('change', function() {
            if (clase_doc_original == 'A') {
                if ($('#clase_doc').val() == 'P') {
                    $('#numero_doc').removeAttr('Disabled');
                }
            }
        });
        $('#menor_embarazada').on('change', function() {
            cambiar_pantalla();
        });
        $("#departamento").change(function() {
            $.ajax({
                url: "procesa.php",
                type: "POST",
                data: "id_departamento=" + $("#departamento").val() + "&provincia=" + document.all.prov_uso.value, //$("#prov_uso").val(),
                success: function(opciones) {
                    $("#localidad").html(opciones);
                }
            })
        });
        $("#localidad").change(function() {
            $.ajax({
                url: "procesa.php",
                type: "POST",
                data: "id_localidad=" + $("#localidad").val(),
                success: function(opciones) {
                    $("#cod_pos").html(opciones);
                }
            })
        });
        $("#cod_pos").change(function() {
            $.ajax({
                url: "procesa.php",
                type: "POST",
                data: "id_codpos=" + $("#cod_pos").val() + "&provincia=" + document.all.prov_uso.value + "&localidad=" + document.all.localidad.value,
                success: function(opciones) {
                    $("#municipio").html(opciones);
                }
            })
        });
        $("#municipio").change(function() {
            $.ajax({
                url: "procesa.php",
                type: "POST",
                data: "id_municipio=" + $("#municipio").val(),
                success: function(opciones) {
                    $("#barrio").html(opciones);
                }
            })
        });
        $("button[name='b_barrio']").focus(function() {
            $.ajax({
                url: "procesa.php",
                type: "POST",
                data: "id_municipio=" + $("#municipio").val() + "&barrio=" + document.all.barrion.value, //+$("#barrion").val(),
                success: function(opciones) {
                    $("#barrio").html(opciones);
                }
            })
        });
        $("input[name='nro_doc_madre//anuloconsulta']").blur(function() {
            $.ajax({
                url: "procesa.php",
                type: "POST",
                data: "nro_doc_madre=" + $(this).val() + "&responsable=" + document.all.responsable.value,
                success: function(retorno) {//alert(retorno);
                    var retorno = retorno.split("*");
                    //alert(retorno);
                    $("select[name='responsable']").val(retorno[0]);
                    $("select[name='tipo_doc_madre']").val(retorno[1]);
                    $("input[name='apellido_madre']").val(retorno[2]);
                    $("input[name='nombre_madre']").val(retorno[3]);
                    if (retorno[4] == 'S') {
                        document.all.alfabeta_madre[0].checked = true;
                    } else {
                        document.all.alfabeta_madre[0].checked = true;
                        document.all.estudios_madre[0].checked = false;
                        document.all.estudios_madre[1].checked = false;
                        document.all.estudios_madre[2].checked = false;
                        document.all.anio_mayor_nivel_madre.value = '0';
                    }
                    if (retorno[5].toUpperCase() == 'INICIAL') {
                        document.all.estudios_madre[0].checked = true;
                    } else {
                        if (retorno[5].toUpperCase() == 'PRIMARIO') {
                            document.all.estudios_madre[1].checked = true;
                        } else {
                            if (retorno[5].toUpperCase() == 'SECUNDARIO') {
                                document.all.estudios_madre[2].checked = true;
                            } else {
                                if (retorno[5].toUpperCase() == 'TERCIARIO') {
                                    document.all.estudios_madre[3].checked = true;
                                } else {
                                    if (retorno[5].toUpperCase() == 'UNIVERSITARIO') {
                                        document.all.estudios_madre[4].checked = true;
                                    }

                                }
                            }
                        }
                    }
                    $("select[name='estadoest_madre']").val(retorno[6]);
                    $("input[name='anio_mayor_nivel_madre']").val(retorno[7]);
                }
            });
        });
    });
    /*enter por tab*/




    //Guarda el nombre del Pais
    function showpais_nac() {
        var pais_nac = document.getElementById('pais_nac')[document.getElementById('pais_nac').selectedIndex].innerHTML;
        document.all.paisn.value = pais_nac;
    }// FIN

    // Guarda el nombre del Departamento
    function showdepartamento() {
        var departamento = document.getElementById('departamento')[document.getElementById('departamento').selectedIndex].innerHTML;
        document.all.departamenton.value = departamento;
    } // FIN

    //Guarda el nombre del Localidad
    function showlocalidad() {
        var localidad = document.getElementById('localidad')[document.getElementById('localidad').selectedIndex].innerHTML;
        document.all.localidadn.value = localidad;
    }// FIN

    // Guarda el Codigo Postal
    function showcodpos() {
        var cod_pos = document.getElementById('cod_pos')[document.getElementById('cod_pos').selectedIndex].innerHTML;
        document.all.cod_posn.value = cod_pos;
    }// FIN

    //Guarda el nombre del Municipio
    function showmunicipio() {
        var municipio = document.getElementById('municipio')[document.getElementById('municipio').selectedIndex].innerHTML;
        document.all.municipion.value = municipio;
        //alert(municipio);
    }// FIN

    //Guarda el nombre del Barrio
    function showbarrio() {
        var barrio = document.getElementById('barrio')[document.getElementById('barrio').selectedIndex].innerHTML;
        document.all.barrion.value = barrio;
    }// FIN

    //Validar Fechas
    function esFechaValida(fecha) {
        if (fecha != undefined && fecha != "") {
            if (!/^\d{2}\/\d{2}\/\d{4}$/.test(fecha)) {
                alert("formato de fecha no válido (dd/mm/aaaa)");
                return false;
            }
            var dia = parseInt(fecha.substring(0, 2), 10);
            var mes = parseInt(fecha.substring(3, 5), 10);
            var anio = parseInt(fecha.substring(6), 10);
            switch (mes) {
                case 1:
                case 3:
                case 5:
                case 7:
                case 8:
                case 10:
                case 12:
                    numDias = 31;
                    break;
                case 4:
                case 6:
                case 9:
                case 11:
                    numDias = 30;
                    break;
                case 2:
                    if (comprobarSiBisisesto(anio)) {
                        numDias = 29
                    } else {
                        numDias = 28
                    }
                    ;
                    break;
                default:
                    alert("Fecha introducida errónea");
                    return false;
            }

            if (dia > numDias || dia == 0) {
                alert("Fecha introducida errónea");
                return false;
            }
            return true;
        }
        return false;
    }

    function comprobarSiBisisesto(anio) {
        if ((anio % 100 != 0) && ((anio % 4 == 0) || (anio % 400 == 0))) {
            return true;
        }
        else {
            return false;
        }
    }



    //controlan que ingresen todos los datos necesarios par el muleto
    function control_nuevos()
    {       
        
        var fecha = document.getElementById('fecha_nac');
        if (fecha != undefined && fecha.value != "") {
            if (!/^\d{2}\/\d{2}\/\d{4}$/.test(fecha.value)) {
                alert("formato de fecha no válido (dd/mm/aaaa)");
                return false;
            }
            var dia = parseInt(fecha.value.substring(0, 2), 10);
            var mes = parseInt(fecha.value.substring(3, 5), 10);
            var anio = parseInt(fecha.value.substring(6), 10);
            switch (mes) {
                case 1:
                case 3:
                case 5:
                case 7:
                case 8:
                case 10:
                case 12:
                    numDias = 31;
                    break;
                case 4:
                case 6:
                case 9:
                case 11:
                    numDias = 30;
                    break;
                case 2:
                    if (comprobarSiBisisesto(anio)) {
                        numDias = 29
                    } else {
                        numDias = 28
                    }
                    ;
                    break;
                default:
                    alert("Fecha introducida errónea");
                    return false;
            }

            if (dia > numDias || dia == 0) {
                alert("Fecha introducida errónea");
                return false;
            }
            //control de fecha mayor al 31/12/1900 
            x = new Date(anio,mes-1,dia);
            y = new Date(1900,11,31);
            if(x.getTime()<=y.getTime()){
                alert('La fecha de nacimiento debe ser posterior a 31/12/1900');
                $('#fecha_nac').focus();
                return false;
            }
            //return true;
        }else{
            alert("La fecha de nacimiento es obligatoria");
            $('#fecha_nac').focus();
            return false;
        }

        var fecha = document.getElementById('fecha_inscripcion');
        if (fecha != undefined && fecha.value != "") {
            if (!/^\d{2}\/\d{2}\/\d{4}$/.test(fecha.value)) {
                alert("formato de fecha no válido (dd/mm/aaaa)");
                return false;
            }
            var dia = parseInt(fecha.value.substring(0, 2), 10);
            var mes = parseInt(fecha.value.substring(3, 5), 10);
            var anio = parseInt(fecha.value.substring(6), 10);
            switch (mes) {
                case 1:
                case 3:
                case 5:
                case 7:
                case 8:
                case 10:
                case 12:
                    numDias = 31;
                    break;
                case 4:
                case 6:
                case 9:
                case 11:
                    numDias = 30;
                    break;
                case 2:
                    if (comprobarSiBisisesto(anio)) {
                        numDias = 29
                    } else {
                        numDias = 28
                    }
                    ;
                    break;
                default:
                    alert("Fecha introducida errónea");
                    return false;
            }

            if (dia > numDias || dia == 0) {
                alert("Fecha introducida errónea");
                return false;
            }
            //return true;
        }else{
            alert("La fecha de inscripcion es obligatoria");
            $('#fecha_inscripcion').focus();
            return false;
        }


        // Calculo de días para fecha de Nacimiento Mayor a Fecha Actual
        function fechaNacAct() {
            var d1 = $('#fecha_nac').val().split("/");
            var dat1 = new Date(d1[2], parseFloat(d1[1]) - 1, parseFloat(d1[0]));
            var d2 = $('#fecha_inscripcion').val().split("/");
            var dat2 = new Date(d2[2], parseFloat(d2[1]) - 1, parseFloat(d2[0]));
            var fin = dat2.getTime() - dat1.getTime();
            var dias = Math.floor(fin / (1000 * 60 * 60 * 24))

            return dias;
        }  // FIN


        if ((fechaNacAct() <= '-1')) {
            alert("La Fecha de Nacimiento no puede ser mayor a la Fecha de Inscripcion");
            document.all.fecha_nac.focus();
            return false;
        }

        /*Control de Agente*/
        if (document.all.cuie_agente.value == "-1") {
            alert("Debe elegir un centro inscriptor");
            document.all.cuie_agente.focus();
            return false;
        }

        if (document.all.apellidoagente.value == "" || $.trim(document.all.apellidoagente.value).length == 0) {
            alert("Debe completar el campo apellido Agente");
            document.all.apellidoagente.focus();
            return false;
        } else {
            var charpos = $.trim(document.all.apellidoagente.value).search("[^A-Za-zñÑáéíóúÁÉÍÓÚ/ \s/]");
            if (charpos >= 0)
            {
                alert("El campo Apellido Agente solo permite letras ");
                document.all.apellidoagente.focus();
                return false;
            }
        }

        if (document.all.nombreagente.value == "" || $.trim(document.all.nombreagente.value).length == 0) {
            alert("Debe completar el campo nombre Agente");
            document.all.nombreagente.focus();
            return false;
        } else {
            var charpos = document.all.nombreagente.value.search("[^A-Za-zñÑáéíóúÁÉÍÓÚ/ \s/]");
            if (charpos >= 0)
            {
                alert("El campo Nombre Agente solo permite letras ");
                document.all.nombreagente.focus();
                return false;
            }
        }

        if (document.all.num_doc_agente.value == "" || $.trim(document.all.num_doc_agente.value).length == 0) {
            alert("Debe completar el campo Nro Doc del Agente");
            document.all.num_doc_agente.focus();
            return false;
        }

        if (document.all.mail.value != "") {
            if (!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(document.all.mail.value))) {

                alert("La dirección de email es incorrecta.");
                document.all.mail.focus();
                return false;
            }
        }

        if (document.all.num_doc.value == "") {
            alert("Debe completar el campo numero de documento");
            document.all.num_doc.focus();
            return false;
        } else {
            var num_doc = document.all.num_doc.value;
        }

        if (document.all.apellido.value == "") {
            alert("Debe completar el campo apellido");
            document.all.apellido.focus();
            return false;
        } else {
            if(document.all.existe_en_smi.value != 1){
                //var charpos = document.all.apellido.value.search("/[^A-Za-z\s]/"); 
                var charpos = $.trim(document.all.apellido.value).search("[^A-Za-zñÑ/\s/]");
                if (charpos >= 0)
                {
                    alert("El campo Apellido solo permite letras ");
                    document.all.apellido.focus();
                    return false;
                }
            }
        }


        if (document.all.nombre.value == "") {
            alert("Debe completar el campo nombre");
            document.all.nombre.focus();
            return false;
        } else {
            var charpos = $.trim(document.all.nombre.value).search("[^A-Za-zñÑ/\s/]");
            if (charpos >= 0)
            {
                alert("El campo Nombre solo permite letras ");
                document.all.nombre.focus();
                return false;
            }
        }


        if (document.all.sexo.value == "-1") {
            alert("Debe completar el campo sexo");
            document.all.sexo.focus();
            return false;
        }
        if (document.all.pais_nac.value == "-1") {
            alert("Debe completar el campo pais");
            document.all.pais_nac.focus();
            return false;
        }
                


        if (document.all.calle.value == "" || $.trim(document.all.calle.value).length == 0) {
            alert("Debe completar el campo calle");
            document.all.calle.focus();
            return false;
        }

        if (document.all.numero_calle.value == "" || $.trim(document.all.numero_calle.value).length == 0) {
            alert("Debe completar el campo numero calle");
            document.all.numero_calle.focus();
            return false;
        }
        if (document.all.pais_nac.value == "ARGENTINA" || document.all.pais_nac.value == "-1") {
            if (document.all.departamento.value == "-1" || document.all.departamento.value == "") {
                alert("Debe completar el campo departamento");
                document.all.departamento.focus();
                return false;
            }

            if (document.all.localidad.value == "-1" || document.all.localidad.value == "") {
                alert("Debe completar el campo Localidad");
                document.all.localidad.focus();
                return false;
            }

            if (document.all.cod_pos.value == "-1") {
                alert("Debe completar el campo Codigo Postal");
                document.all.cod_pos.focus();
                return false;
            }

            if (document.all.municipio.value == "-1") {
                alert("Debe completar el campo Municipio");
                document.all.municipio.focus();
                return false;
            }

            if (document.all.barrio.value == "-1" || document.all.barrio.value == "") {
                alert("Debe completar el campo Barrio");
                document.all.barrio.focus();
                return false;
            }
        }

        if (document.all.cuie.value == "-1") {
            alert('Debe Seleccionar un Efector');
            document.all.cuie.focus();
            return false;
        }


        if (document.all.edades.value <= 9) {
            if (document.all.responsable.value == "-1") {
                alert("Debe completar el campo Datos del responsable");
                document.all.responsable.focus();
                return false;
            }

            if (document.all.tipo_doc_madre.value == "-1") {
                alert("Debe completar el campo tipo de documento del responsable");
                document.all.apellido_madre.focus();
                return false;
            }
            if ($.trim(document.all.nro_doc_madre.value) == "") {

                alert("Debe completar el campo numero de documento del responsable");
                //document.all.num_doc_madre.focus();
                return false;
            } else {
                var num_doc_madre = document.all.nro_doc_madre.value;
                if (isNaN(num_doc_madre)) {
                    alert('El dato ingresado en numero de documento del responsable debe ser entero');
                    document.all.num_doc_madre.focus();
                    return false;
                }
            }

            if ($.trim(document.all.apellido_madre.value) == "") {
                alert("Debe completar el campo apellido del responsable");
                document.all.apellido_madre.focus();
                return false;
            } else {
                var charpos = document.all.apellido_madre.value.search("[^A-Za-zñÑ/ \s/]");
                if (charpos >= 0)
                {
                    alert("El campo apellido del responsable solo permite letras ");
                    document.all.apellido_madre.focus();
                    return false;
                }
            }
            if ($.trim(document.all.nombre_madre.value) == "") {
                alert("Debe completar el campo nombre del responsable");
                document.all.nombre_madre.focus();
                return false;
            } else {
                var charpos = document.all.nombre_madre.value.search("[^A-Za-zñÑ/ \s/]");
                if (charpos >= 0)
                {
                    alert("El campo Nombre del responsable solo permite letras ");
                    document.all.nombre_madre.focus();
                    return false;
                }
            }

            if (document.all.alfabeta_madre.value == "-1") {
                alert("Debe completar el campo alfabeto del responsable");
                return false;
            }
        }
        
        var docu = document.all.clase_doc.value;
        var num1 = document.all.nro_doc_madre.value;
        var num2 = document.all.numero_doc.value;
        if(docu == 'A') {
            // es documento ajeno
            if ($.trim(num1) != $.trim(num2)) {
                alert("Los numeros de documento del beneficiario y su responsable deben coincidir");
                document.all.nro_doc_madre.focus();
                return false;
            }
        }else{
            // es documento propio
            if ($.trim(num1) == $.trim(num2)) {
                alert("Los numeros de documento del beneficiario y su responsable deben ser distintos");
                document.all.numero_doc.focus();
                return false;
            }
        }

        var numero_doc_aux = $('#numero_doc').val().replace(/\ /g, '');
        numero_doc_aux = parseInt(numero_doc_aux);
        if($('#tipo_doc').val() == "DNI"){
            // chequear que sea >= 1 millon
            if (numero_doc_aux.toString().length < 7) {
                alert("El numero de documento es incorrecto");
                return false;
            }
        }else{
            if($('#tipo_doc').val() != "DEX"){
                // chequear que sea > 50000
                if(numero_doc_aux<50000){
                    alert("El numero de documento debe ser mayor a 50000");
                    return false;
                }
            }
        }

        if (document.all.menor_embarazada.checked) {

            if (document.all.semanas_embarazo.value == "") {
                alert("Debe completar las Semanas de Embarazo");
                return false;
            }

            if (document.all.fecha_diagnostico_embarazo.value == "") {
                alert("Debe completar la Fecha de Diagnóstico de embarazo");
                return false;
            }

            if (document.all.fecha_probable_parto.value == "") {
                alert("Debe completar la Fecha de Probable de Parto");
                return false;
            }
        }

        var fechita = $("#fecha_inscripcion").val();
        // Fecha de Inscripcion mayor a 01/08/2004.
        if (!esFechaValida(fechita)) {
            alert("La fecha de inscripcion es invalida");
            document.all.fecha_inscripcion.focus();
            return false;
        } 	// FIN




    }//de function control_nuevos()



    function verificaFPP() {
        var d1 = $('#fecha_probable_parto').val().split("/");
        var dat1 = new Date(d1[2], parseFloat(d1[1]) - 1, parseFloat(d1[0]));
        var d2 = $('#fecha_inscripcion').val().split("/");
        var dat2 = new Date(d2[2], parseFloat(d2[1]) - 1, parseFloat(d2[0]));
        var fin = dat2.getTime() - dat1.getTime();
        var dias = Math.floor(fin / (1000 * 60 * 60 * 24))

        return dias;
    }  // FIN

    // Valida que la Fecha Probable de Parto no supere los 45 días después del Parto
    function mostrarDias() {
        if (verificaFPP() >= '46') {

            alert("No se puede Inscribir porque supero los 45 días después del Parto");
            document.all.fecha_probable_parto.focus();
            return false;
        }
    } // FIN

    // Fecha Diagnostico de Embarazo no puede ser superior a la Fecha de Inscripción
    function validaFDE() {
        var d1 = $('#fecha_diagnostico_embarazo').val().split("/");
        var dat1 = new Date(d1[2], parseFloat(d1[1]) - 1, parseFloat(d1[0]));
        var d2 = $('#fecha_inscripcion').val().split("/");
        var dat2 = new Date(d2[2], parseFloat(d2[1]) - 1, parseFloat(d2[0]));
        var fin = dat2.getTime() - dat1.getTime();
        var dias = Math.floor(fin / (1000 * 60 * 60 * 24))

        return dias;
    }  // FIN

    // Valida que la Fecha de Diagnostico de Embarazo sea menor a la Fecha de Inscripcion
    function mostrarFDE() {
        if ((validaFDE() <= '-1') || (validaFDE() == '0')) {

            alert("La Fecha de Diagnostico de Embarazo tiene que ser menor a la Fecha de Inscripción");
        }
    } // FIN

    function editar_campos()
    {
        inputs = document.form1.getElementsByTagName('input'); //Arma un arreglo con todos los campos tipo INPUT
        for (i = 0; i < inputs.length; i++) {
            inputs[i].readOnly = false;
        }

        document.all.cancelar_editar.disabled = false;
        document.all.guardar_editar.disabled = false;
        document.all.editar.disabled = true;
        return true;
    }//de function control_nuevos()

    /**********************************************************/
    //funciones para busqueda abreviada utilizando teclas en la lista que muestra los clientes.
    var digitos = 10; //cantidad de digitos buscados
    var puntero = 0;
    var buffer = new Array(digitos); //declaraciï¿½n del array Buffer
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

    // muestra o no lo información de Parto dependiendo del sexo y si vive o no con un adulto dependiendo de la edad
    function cambiar_pantalla() {
        if (document.all.edades.value > 100) {
            return;
        }

        // Masculino - Menor de 18 años edad no muestra la información de embarazo, muestra la información de menor vive con adulto 
        //y pide la información del adulto aunque el menor no viva con el. 
        if ((document.all.sexo.value == 'M') && (document.all.edades.value <= 9)) {
            document.all.cat_emb.style.display = 'none';
            document.all.menor_embarazada.checked = false;
            document.all.cat_nino.style.display = 'inline';
            document.all.mva.style.display = 'table-cell';
            document.all.memb.style.display = 'none';
        }//fin


        // Masculino - Mayor de edad 19 años no muestra la información de embarazo, no muestra la información de menor vive con adulto 
        if ((document.all.sexo.value == 'M') && (document.all.edades.value > 9)) {
            document.all.cat_emb.style.display = 'none';
            document.all.cat_nino.style.display = 'none';
            document.all.mva.style.display = 'none';
            document.all.memb.style.display = 'none';
            document.all.menor_embarazada.checked = false;
            document.all.fum.value = "";
            document.all.semanas_embarazo.value = "";
            document.all.fecha_diagnostico_embarazo.value = "";
            document.all.fecha_probable_parto.value = "";
            document.all.score_riesgo.value = "";
            document.all.responsable.selectedIndex = 0;
            document.all.nro_doc_madre.value = "";
            document.all.apellido_madre.value = "";
            document.all.nombre_madre.value = "";
        } //fin

        // Femenino - Menor de 9 años no muestra la información de embarazo, muestra la información de menor vive con adulto 
        //y pide la información del adulto aunque el menor no viva con el. 
        if ((document.all.sexo.value == 'F') && (document.all.edades.value <= 9)) {
            document.all.cat_nino.style.display = 'inline';
            document.all.mva.style.display = 'table-cell';
            document.all.cat_emb.style.display = 'none';
            document.all.menor_embarazada.checked = false;
            document.all.memb.style.display = 'none';
        }

        // Femenino - Mayor de 10 años de edad (se agrega el control de edad 08-06-2012) - R+R -> muestra la información de embarazo, 
        // no muestra la información de menor vive con adulto 
        if ((document.all.sexo.value == 'F') && (document.all.edades.value > 9)) {

            document.all.cat_nino.style.display = 'none';
            document.all.mva.style.display = 'none';
            document.all.memb.style.display = 'inline';
            document.all.responsable.selectedIndex = 0;
            document.all.nro_doc_madre.value = "";
            document.all.apellido_madre.value = "";
            document.all.nombre_madre.value = "";
            //document.all.embarazada.style.display='inline';


            if (document.all.menor_embarazada.checked) {
                document.all.cat_emb.style.display = 'inline';
            } else {
                document.all.cat_emb.style.display = 'none';
                document.all.fum.value = "";
                document.all.semanas_embarazo.value = "";
                document.all.fecha_diagnostico_embarazo.value = "";
                document.all.fecha_probable_parto.value = "";
                document.all.score_riesgo.value = "";
            }
        } //fin

    } // FIN cambiar_pantalla()

    //INICIO /////  agregado 01-11-2011
    function DiferenciaFechas(CadenaFecha1) {

        //Obtiene dia, mes y año  
        var fecha1 = new fecha(CadenaFecha1)

        //Obtiene objetos Date  
        var miFecha1 = new Date(fecha1.anio, fecha1.mes - 1, fecha1.dia)
        var hoy = new Date()
        //alert(miFecha1)
        //alert(hoy)
        //Resta fechas y redondea  
        var diferencia = hoy.getTime() - miFecha1.getTime()
        var anios = diferencia / (1000 * 60 * 60 * 24 * 365)
        //var segundos = Math.floor(diferencia / 1000)  
        //alert ('La diferencia es de ' + dias + ' dias,\no ' + segundos + ' segundos.')  

        return anios
    }

    function fecha(cadena) {

        //Separador para la introduccion de las fechas  
        var separador = "/"

        //Separa por dia, mes y año  
        if (cadena.indexOf(separador) != -1) {
            var posi1 = 0
            var posi2 = cadena.indexOf(separador, posi1 + 1)
            var posi3 = cadena.indexOf(separador, posi2 + 1)
            this.dia = cadena.substring(posi1, posi2)
            this.mes = cadena.substring(posi2 + 1, posi3)
            this.anio = cadena.substring(posi3 + 1, cadena.length)
        } else {
            this.dia = false
            this.mes = false
            this.anio = false
        }
    }
    // FIN /// agregado 01-11-2011

    // calcula la edad y da el valor de la categoria
    function edad(FechaNac) {
        var ed = parseInt(DiferenciaFechas(FechaNac))
        document.getElementById("edades").value = ed;
    } //FIN calculo de edad y categoría

    //Desarma la fecha para calcular la FPP
    var aFinMes = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    function finMes(nMes, nAno) {
        return aFinMes[nMes - 1] + (((nMes == 2) && (nAno % 4) == 0) ? 1 : 0);
    }

    function padNmb(nStr, nLen, sChr) {
        var sRes = String(nStr);
        for (var i = 0; i < nLen - String(nStr).length; i++)
            sRes = sChr + sRes;
        return sRes;
    }

    function makeDateFormat(nDay, nMonth, nYear) {
        var sRes;
        sRes = padNmb(nDay, 2, "0") + "/" + padNmb(nMonth, 2, "0") + "/" + padNmb(nYear, 4, "0");
        return sRes;
    }

    function incDate(sFec0) {
        var nDia = parseInt(sFec0.substr(0, 2), 10);
        var nMes = parseInt(sFec0.substr(3, 2), 10);
        var nAno = parseInt(sFec0.substr(6, 4), 10);
        nDia += 1;
        if (nDia > finMes(nMes, nAno)) {
            nDia = 1;
            nMes += 1;
            if (nMes == 13) {
                nMes = 1;
                nAno += 1;
            }
        }
        return makeDateFormat(nDia, nMes, nAno);
    }

    function decDate(sFec0) {
        var nDia = Number(sFec0.substr(0, 2));
        var nMes = Number(sFec0.substr(3, 2));
        var nAno = Number(sFec0.substr(6, 4));
        nDia -= 1;
        if (nDia == 0) {
            nMes -= 1;
            if (nMes == 0) {
                nMes = 12;
                nAno -= 1;
            }
            nDia = finMes(nMes, nAno);
        }
        return makeDateFormat(nDia, nMes, nAno);
    }

    function addToDate(sFec0, sInc) {
        var nInc = Math.abs(parseInt(sInc));
        var sRes = sFec0;
        if (parseInt(sInc) >= 0)
            for (var i = 0; i < nInc; i++)
                sRes = incDate(sRes);
        else
            for (var i = 0; i < nInc; i++)
                sRes = decDate(sRes);
        return sRes;
    } //FIN Fecha para calculo de  FPP

    // Calcula la FPP
    function recalcF1() {
        if (document.all.semanas_embarazo.value == "") {
            return;
        }

        if (document.all.fecha_diagnostico_embarazo.value == "") {
            return;
        }
        with (document.form1) {
            fecha_probable_parto.value = addToDate(fecha_diagnostico_embarazo.value, 280 - (semanas_embarazo.value * 7));
        }
    } // FIN FPP

    // Calcula la FPP en funcion fumm
    function recalcF1_fum() {
        if (document.all.fum.value == "") {
            return;
        }

        with (document.form1) {
            if (addToDate(fum.value, 40) != 'NaN/NaN/0NaN') {
                fecha_probable_parto.value = addToDate(fum.value, 40);
            }
        }
    } // FIN FPP

    var patron = new Array(2, 2, 4)
    var patron2 = new Array(5, 16)
    function mascara(d, sep, pat, nums) {
        if (d.valant != d.value) {
            val = d.value
            largo = val.length
            val = val.split(sep)
            val2 = ''
            for (r = 0; r < val.length; r++) {
                val2 += val[r]
            }
            if (nums) {
                for (z = 0; z < val2.length; z++) {
                    if (isNaN(val2.charAt(z))) {
                        letra = new RegExp(val2.charAt(z), "g")
                        val2 = val2.replace(letra, "")
                    }
                }
            }
            val = ''
            val3 = new Array()
            for (s = 0; s < pat.length; s++) {
                val3[s] = val2.substring(0, pat[s])
                val2 = val2.substr(pat[s])
            }
            for (q = 0; q < val3.length; q++) {
                if (q == 0) {
                    val = val3[q]

                }
                else {
                    if (val3[q] != "") {
                        val += sep + val3[q]
                    }
                }
            }
            d.value = val
            d.valant = val
        }
    }

    function pulsar(e) {
        tecla = (document.all) ? e.keyCode : e.which;
        return (tecla != 13);
    }


    function seleccionFormularioRemediar(uielement){
        $( "#dialog-confirm" ).dialog( "open" );
    }

    // Bind del formulario Remediar + Redes
    function bindFormularioRemediar(uielement){
        $( "#dialog-confirm" ).dialog({
            autoOpen : false,
            resizable: false,
            height:240,
            width:160,
            modal: true,
            draggable: false,
            buttons: {
                "Antiguo Formulario": function() {
                    window.open('<?= encode_link("../remediar/remediar_admin.php", array("estado_envio" => $estado_envio, "clave_beneficiario" => $clave_beneficiario, "sexo" => $sexo, "fecha_nac" => $fecha_nac, "vremediar" => 's', "pagina" => "ins_admin.php")) ?>', 'Remediar', 'dependent:yes,width=900,height=700,top=1,left=60,scrollbars=yes');
                    $( this ).dialog( "close" );

                },
                "Nuevo Formulario E+C": function() {
                    window.open('<?= encode_link("../remediar/remediar_admin_nuevo.php", array("estado_envio" => $estado_envio, "clave_beneficiario" => $clave_beneficiario, "sexo" => $sexo, "fecha_nac" => $fecha_nac, "vremediar" => 's', "pagina" => "ins_admin.php")) ?>', 'Remediar', 'dependent:yes,width=900,height=700,top=1,left=60,scrollbars=yes');
                    $( this ).dialog( "close" );
                }
            }
        });


        
    }


</script>

<?php include('ins_admin_frm.php'); ?>

<?= fin_pagina(); // aca termino    ?>
