<?php
require_once ("../../config.php");
require_once ("../../lib/bibliotecaTraeme.php");
require_once ("../../lib/funciones_misiones.php");
include_once('lib_inscripcion.php');

extract($_POST, EXTR_SKIP);
if ($parametros)
    extract($parametros, EXTR_OVERWRITE);

$color = "";

if ($num_doc == ''){
    $sql1 = "SELECT clase_documento_benef,tipo_documento,numero_doc,apellido_benef,
             nombre_benef,fecha_nacimiento_benef,id_beneficiarios
             FROM uad.beneficiarios	  
             WHERE clave_beneficiario='$clave'";    
}else {
    $sql1 = "SELECT * 
             FROM uad.beneficiarios	  
             WHERE numero_doc='$num_doc' and tipo_documento='$tipo_doc' and clase_documento_benef='$clase_doc'";
}    
$res_extra1 = sql($sql1, "Error al traer el beneficiario") or fin_pagina();
if ($res_extra1->recordcount() > 0) {
    if ($clase_doc != 'P') {
        ?>
        <b> Beneficiarios que coinciden con los Datos ingresados</b>
        <table cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" class="bordes" style="width:620px;margin-top: 5px">
            <tr bgcolor="#CED8F6">
                <td align="center" width="15%"><b>Clase Doc.</b></td>
                <td align="center" width="10%"><b>Tipo Doc.</b></td>
                <td align="center" width="15%"><b>Nro. Doc.</b></td>
                <td align="center" width="25%"><b>Apellido</b></td>
                <td align="center" width="20%"><b>Nombre</b></td>
                <td align="center" width="35%"><b>Fecha Nac.</b></td>
                <td align="center" width="6%"><b>Modificar</b></td>
            </tr>
            <?
            $res_extra1->movefirst();
            while (!$res_extra1->EOF) {
                if ($color == '#FBFBEF') {
                    $color = '#E8E8E8 ';
                } else {
                    $color = '#FBFBEF';
                }
                ?>
                <tr bgcolor="<?= $color ?>">
                    <td><?
            if ($res_extra1->fields['clase_documento_benef'] == 'A') {
                echo "Ajeno";
            } else {
                echo "Propio";
            }
                ?></td>

                    <td><?= $res_extra1->fields['tipo_documento'] ?></td>
                    <td><?= $res_extra1->fields['numero_doc'] ?></td>
                    <td><?= $res_extra1->fields['apellido_benef'] ?></td>
                    <td><?= $res_extra1->fields['nombre_benef'] ?></td>
                    <td><?= $res_extra1->fields['fecha_nacimiento_benef'] ?></td>
                    <?
                    $tipo_transaccion = 'M';
                    $id_planilla = $res_extra1->fields['id_beneficiarios'];
                    $ref = encode_link("ins_admin.php", array("id_planilla" => $id_planilla, "tipo_transaccion" => $tipo_transaccion));
                    ?>
                    <td align="center"><img style="cursor: pointer" src="../../imagenes/menu/icon17.gif" onclick="location.href='<?= $ref ?>'"></td>
                </tr>
                <?
                $res_extra1->movenext();
            }
            ?>
            <tr bgcolor="#D8D8D8" align="center">
                <td colspan="7">
                    <?
                    $tipo_transaccion = 'A';
                    $ref = encode_link("ins_admin.php", array('clase_doc' => $clase_doc, 'tipo_doc' => $tipo_doc, 'num_doc' => $num_doc, 'tipo_transaccion' => $tipo_transaccion));
                    ?>
                    <input style="margin: 5px;"value="Agregar Nuevo" type="button" onclick="location.href='<?= $ref ?>'"></input>
                </td>
            </tr>
        </table>
        <?
    } else {
        //si existe pero es documento propio que redireccione a la planilla
        $accion = "El Beneficiario ya esta Empadronado";
        $tipo_transaccion = 'M';
        $id_planilla = $res_extra1->fields['id_beneficiarios'];
        $ref = encode_link("ins_admin.php", array("id_planilla" => $id_planilla, "tipo_transaccion" => $tipo_transaccion));
        echo "<SCRIPT Language='Javascript'> location.href='$ref'</SCRIPT>";
    }
} else {
    //No lo encuentra en UAD.BENEFICIARIOS
    //Entonces lo busca en SMIAFILIADOS
    $clase_doc2 = str_replace('R', 'P', $clase_doc);
    $clase_doc2 = str_replace('M', 'A', $clase_doc2);
    if ($num_doc == ''){
        $sql = "SELECT id_smiafiliados 
                FROM nacer.smiafiliados
                WHERE clavebeneficiario ='$clave'";    
    }else {   
        $sql = "SELECT id_smiafiliados
                FROM nacer.smiafiliados
                WHERE afidni='$num_doc' and afitipodoc='$tipo_doc' and aficlasedoc='$clase_doc2'";
    }    
    $res_extra = sql($sql, "Error al traer el beneficiario") or fin_pagina();

    if ($res_extra->recordcount() > 0) {
        $accion = "El Beneficiario ya esta Empadronado.";
        $tipo_transaccion = 'M';
        $tipo_ficha = '2';
        $fecha_carga = date("Y-m-d H:m:s");
        $usuario = $_ses_user['id'];
        $usuario = substr($usuario, 0, 9);

        if(!beneficiarioInscriptoUad($clase_doc, $tipo_doc, $num_doc)){
        

            $sql2 = "INSERT INTO uad.beneficiarios
                    (id_beneficiarios,estado_envio,clave_beneficiario,tipo_transaccion,apellido_benef,apellido_benef_otro,nombre_benef,
                    nombre_benef_otro,clase_documento_benef,tipo_documento,numero_doc,id_categoria,sexo,fecha_nacimiento_benef,
                    responsable,
                    tipo_doc_madre,nro_doc_madre,apellido_madre,nombre_madre,fecha_diagnostico_embarazo,cuie_ea,cuie_ah,centro_inscriptor,departamento,municipio,localidad,fecha_inscripcion,activo,fecha_carga,usuario_carga,tipo_ficha,
                    tipo_doc_padre,nro_doc_padre,apellido_padre,nombre_padre,fecha_probable_parto,fecha_efectiva_parto,calle,numero_calle,manzana,piso,dpto,entre_calle_1,entre_calle_2,barrio,cod_pos,
                    tipo_doc_tutor,nro_doc_tutor,apellido_tutor,nombre_tutor,semanas_embarazo)
                    select nextval('uad.beneficiarios_id_beneficiarios_seq'),'e',clavebeneficiario ,'M',
                    case when position(' ' in trim(afiapellido))=0 then trim(afiapellido) else substring(trim(afiapellido) from 1 for (position(' ' in trim(afiapellido))-1)) end,
                    case when position(' ' in trim(afiapellido))=0 then '' else substring(trim(afiapellido) from (position(' ' in trim(afiapellido))+1) for char_length(trim(afiapellido))) end,
                    case when position(' ' in trim(afinombre))=0 then trim(afinombre) else substring(trim(afinombre) from 1 for (position(' ' in trim(afinombre))-1)) end, 
                    case when position(' ' in afinombre)=0 then '' else substring(trim(afinombre) from (position(' ' in trim(afinombre))+1) for char_length(trim(afinombre))) end,
                    aficlasedoc,afitipodoc,afidni,case when afitipocategoria in (4,3) then 5 when afitipocategoria in (1,2,7,6) then 6 end  afitipocategoria,
                    afisexo,afifechanac,
                    case when manrodocumento != ''  then 'MADRE' else (case when panrodocumento != '' then 'PADRE' else (case when otronrodocumento != '' then 'TUTOR' end) end) end,
                    matipodocumento,manrodocumento,maapellido,manombre,fechadiagnosticoembarazo,
                    cuieefectorasignado,cuielugaratencionhabitual,cuieefectorasignado,afidomdepartamento,afidommunicipio,afidomlocalidad,fechainscripcion,1,current_date,'$usuario','2',
                    patipodocumento,panrodocumento,paapellido,panombre,fechaprobableparto,fechaefectivaparto,afidomcalle,afidomnro,afidommanzana,afidompiso,afidomdepto,
                    afidomentrecalle1,afidomentrecalle2,afidombarrioparaje,afidomcp,
                    otrotipodocumento,otronrodocumento,otroapellido,otronombre,semanasembarazo
                    FROM nacer.smiafiliados";
            if ($num_doc =='') {
                 $sql2 = $sql2 . " WHERE clavebeneficiario = '$clave'
                            RETURNING id_beneficiarios";           
            }else{
                 $sql2 = $sql2 . " WHERE afidni='$num_doc' and afitipodoc='$tipo_doc' and aficlasedoc='$clase_doc2'
                            RETURNING id_beneficiarios"; 
            }                 
            $res_extras = sql($sql2, "Error al insertar el beneficiario") or fin_pagina();
            $id_planilla = $res_extras->fields['id_beneficiarios'];
            $ref = encode_link("ins_admin.php", array("tapa_ver" => 'block', "id_planilla" => $id_planilla, "tipo_transaccion" => $tipo_transaccion, "tipo_ficha" => $tipo_ficha));
            echo "<SCRIPT Language='Javascript'> location.href='$ref'</SCRIPT>";
       

        }else{
            echo "Error Beneficiario con ".$tipo_doc." - ".$num_doc." ya existe";
            exit(0);
        }

    } else {
        $accion2 = "Beneficiario no Encontrado";
        $tipo_transaccion = 'A';
        $datos_resp = 'none';
        $embarazada = 'none';
    }
}
?>