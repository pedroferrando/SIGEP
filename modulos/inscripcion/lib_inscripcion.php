<?php

function calculaedad($fechanacimiento) {
    list($dia, $mes, $ano) = explode("/", $fechanacimiento);
    $ano_diferencia = date("Y") - $ano;
    $mes_diferencia = date("m") - $mes;
    $dia_diferencia = date("d") - $dia;
    if ($mes_diferencia < 0) {
        $ano_diferencia--;
    } else {
        if ($dia_diferencia < 0) {
            $ano_diferencia--;
        }
    }
    return $ano_diferencia;
}

function coprobar_obligatorios() {
    $apellido = $_POST['apellido'];
    $nombre = $_POST['nombre'];
    $apellido_otro = $_POST['apellido_otro'];
    $nombre_otro = $_POST['nombre_otro'];
    $tipo_documento = $_POST['tipo_doc'];
    $clase_doc = $_POST['clase_doc'];
    $tipo_doc = $_POST['tipo_doc'];
    $sexo = $_POST['sexo'];
    $fecha_nac = $_POST['fecha_nac'];
    $pais_nac = $_POST['pais_nacn'];
    $provincia_nac = $_POST['provincia_nacn'];
    $localidad_proc = $_POST['localidad_procn'];
    $indigena = $_POST['indigena'];
    $tipo_doc_madre = $_POST['tipo_doc_madre'];
    $nro_doc_madre = $_POST['nro_doc_madre'];
    $apellido_madre = $_POST['apellido_madre'];
    $nombre_madre = $_POST['nombre_madre'];
    $alfabeta_madre = $_POST['alfabeta_madre'];

//si es mayor de 18 años
    $fecha_diagnostico_embarazo = $_POST['fecha_diagnostico_embarazo'];
    $fecha_probable_parto = $_POST['fecha_probable_parto'];
    $fecha_efectiva_parto = $_POST['fecha_efectiva_parto'];

    $cuie = $_POST['cuie'];
    $calle = $_POST['calle'];
    $nro_calle = $_POST['nro_calle'];
    $departamento = $_POST['departamento'];
    $localidad = $_POST['localidad'];
    $fecha_inscripcion = $_POST['fecha_inscripcion'];



    if ($fecha_nac != "")
        $fecha_nac = Fecha_db($fecha_nac);
    else
        $fecha_nac = "1980-01-01";

    $fecha_inscripcion = Fecha_db($fecha_inscripcion);
    if ($fecha_inscripcion == "")
        $fecha_inscripcion = "2010-11-18 00:00:00";
}

function crearClaveBeneficiario($iduser, $uad_benef) {

    $sql_parametros = "select * from uad.parametros ";
    if ($uad_benef == 's') {
        $sql_parametros.=" a
                    inner join uad.uad_x_usuario b on a.codigo_uad=b.cod_uad
                    where id_usuario=" . $iduser;
    }
    $result_parametros = sql($sql_parametros) or fin_pagina();
    $codigo_provincia = $result_parametros->fields['codigo_provincia'];
    $codigo_ci = $result_parametros->fields['codigo_ci'];
    $codigo_uad = $result_parametros->fields['codigo_uad'];

    $q = "select nextval('uad.beneficiarios_id_beneficiarios_seq') as id_planilla";
    $id_planilla = sql($q) or fin_pagina();
    $id_planilla = $id_planilla->fields['id_planilla'];
    $id_planilla_clave = str_pad($id_planilla, 6, '0', STR_PAD_LEFT);
    $clave_beneficiario = $codigo_provincia . $codigo_uad . $codigo_ci . $id_planilla_clave;
    return $clave_beneficiario;
}


#    Metodo beneficiarioInscriptoUad
#    Verifica si un beneficiario ya existe en la tabla beneficiarios en el esquema uad

function beneficiarioInscriptoUad($clase, $tipoDoc, $numDoc)
{
    if ($clase == 'A'){
        //se deja pasar cuando se trata de hermanos con clase Ajeno = doc de la madre...    
        $value = False;
    } else {    
        $sql = "select clave_beneficiario from uad.beneficiarios b
                    where b.clase_documento_benef = '".$clase."'
                    and b.tipo_documento = '".$tipoDoc."'
                    and b.numero_doc = '".$numDoc."'
                    ";

        $result = sql($sql);

        if ($result->RecordCount() > 0) {
            $value = True;
        } else {
            $value = False;
        }
    } 
    
    return($value);
}

function getStringBorradoResponsable($responsable=""){
    $resp = strtoupper($responsable);
    switch ($resp){
        case 'MADRE':
            $str = ",tipo_doc_madre=NULL,nro_doc_madre=NULL,apellido_madre=NULL
                    ,nombre_madre=NULL,alfabeta_madre=NULL,estudios_madre=NULL
                    ,estadoest_madre=NULL,anio_mayor_nivel_madre=0";
            break;
        case 'PADRE':
            $str = ",tipo_doc_padre=NULL,nro_doc_padre=NULL,apellido_padre=NULL
                    ,nombre_padre=NULL,alfabeta_padre=NULL,estudios_padre=NULL
                    ,estadoest_padre=NULL,anio_mayor_nivel_padre=0";
            break;
        case 'TUTOR':
            $str = ",tipo_doc_tutor=NULL,nro_doc_tutor=NULL,apellido_tutor=NULL
                    ,nombre_tutor=NULL,alfabeta_tutor=NULL,estudios_tutor=NULL
                    ,estadoest_tutor=NULL,anio_mayor_nivel_tutor=0";
            break;
        default:
            $str = ",menor_convive_con_adulto=NULL,responsable=NULL";
            $str .= getStringBorradoResponsable('MADRE');
            $str .= getStringBorradoResponsable('PADRE');
            $str .= getStringBorradoResponsable('TUTOR');
            break;
    }
    return $str;
}

?>