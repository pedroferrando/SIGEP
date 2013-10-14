<?php
    require_once("../../config.php");
    variables_form_busqueda("ins_x_estado_listado");
    
    if($_REQUEST['buscar']){
        $request = $_REQUEST;
    }else{
        $request = decode_link($_REQUEST['p']);
    } 
        
    //if (!permisos_check('inicio','consulta_todos')){ 
    //    $listaUsuarios_sql = "select id_usuario, nombre, apellido from sistema.usuarios where id_usuario = ".$_ses_user['id'];
    //}else{
        $listaUsuarios_sql = "select u.id_usuario, u.nombre, u.apellido, u.login 
                              from sistema.usuarios u 
                              order by u.apellido,u.nombre";
    //}    
    $op_carga = $us_verif = sql($listaUsuarios_sql);

    $arr_fecha_desde = explode("/", $request['fecha_desde']);
    $fecha_desde = $arr_fecha_desde[2]."-".$arr_fecha_desde[1]."-".$arr_fecha_desde[0];
    $arr_fecha_hasta = explode("/", $request['fecha_hasta']);
    $fecha_hasta = $arr_fecha_hasta[2]."-".$arr_fecha_hasta[1]."-".$arr_fecha_hasta[0];

    if($request['fecha_desde']!="" && $request['fecha_hasta']==""){
        $cond_fecha .= " AND benef.fecha_inscripcion >= '$fecha_desde' ";
    }
    if($request['fecha_desde']=="" && $request['fecha_hasta']!=""){
        $cond_fecha .= " AND benef.fecha_inscripcion <= '$fecha_hasta' ";
    }
    if($request['fecha_desde']!="" && $request['fecha_hasta']!=""){
        $cond_fecha .= " AND benef.fecha_inscripcion BETWEEN '$fecha_desde' AND '$fecha_hasta' ";
    }
    if($request['op_carga']!="" && $request['us_verif']!=""){
        $cond_user .= " AND ( usrC.id_usuario='".$request['op_carga']."' OR usrV.id_usuario='".$request['us_verif']."' ) ";
    }
    if($request['op_carga']!="" && $request['us_verif']==""){
        $cond_user .= " AND usrC.id_usuario='".$request['op_carga']."' ";
    }
    if($request['op_carga']=="" && $request['us_verif']!=""){
        $cond_user .= " AND usrV.id_usuario='".$request['us_verif']."' ";
    }
    
    $link_tmp["fecha_desde"] = $request['fecha_desde'];
    $link_tmp["fecha_hasta"] = $request['fecha_hasta'];
    $link_tmp["op_carga"]    = $request['op_carga'];
    $link_tmp["us_verif"]    = $request['us_verif'];
    $link_tmp["keyword"]     = $request['keyword'];
    $link_tmp["filter"]      = $request['filter'];

    $orden = array(
            "default" => "1",
            "1" => "benef.numero_doc",
            "2" => "benef.cuie_ea",

        );
    $filtro = array(		
                    //"usrC.apellido" => "Operador de carga",		
                    //"usrV.apellido" => "Operador verificador",
                    "efec.nombreefector" => "Lugar habitual de atencion",		
                    "benef.municipio" => "Municipio",		

        );
        
    if($cmd){
        $where_tmp .= " benef.estado_envio='$cmd' ";
    }else{
        $where_tmp .= "   (
                            benef.estado_envio='p'
                            OR
                            benef.estado_envio='n'
                            OR
                            benef.estado_envio='e'
                          )
                      ";
    }
            
    if(isset($request) && $request!=""){
        $filter = $request['filter']        ;
        $sql_cnt = "SELECT SUM( CASE benef.estado_envio WHEN 'p' THEN 1 ELSE 0 END ) AS cnt_pendientes,
                           SUM( CASE benef.estado_envio WHEN 'n' THEN 1 ELSE 0 END ) AS cnt_listos_enviar,
                           SUM( CASE benef.estado_envio WHEN 'e' THEN 1 ELSE 0 END ) AS cnt_enviados
                    FROM uad.beneficiarios benef 
                    LEFT JOIN sistema.usuarios usrC ON CAST(benef.usuario_carga AS INTEGER)=usrC.id_usuario 
                    LEFT JOIN facturacion.smiefectores efec ON benef.cuie_ea=efec.cuie 
                    LEFT JOIN sistema.usuarios usrV ON CAST(benef.usuario_verificado AS INTEGER)=usrV.id_usuario 

                    WHERE ( 
                            benef.estado_envio='p'
                            OR
                            benef.estado_envio='n'
                            OR
                            benef.estado_envio='e'
                          )  
                          ".$cond_fecha."
                          ".$cond_user."
                    ";
        if($request['keyword']){
            $keyword = $request['keyword'];
            if ($filtro[$filter] == "") {
                $filter = "all";
            }
            if ($filter == "all" or !$filter) {
                $where_arr = array();
                $where .= "(";
                reset($filtro);
                while (list($key, $val) = each($filtro)) {
                    if (is_array($ignorar) && !in_array($key, $ignorar))
                        $where_arr[] = "$key ILIKE '%$keyword%'";
                    if (!is_array($ignorar))
                        $where_arr[] = "$key ILIKE '%$keyword%'";
                }

                $where .= implode(" OR ", $where_arr);
                $where .= ")";

            } else {
                if (!is_array($ignorar))
                    $where .= "$filter ILIKE '%$keyword%'";
                elseif (is_array($ignorar) && !in_array($filter, $ignorar))
                    $where .= "$filter ILIKE '%$keyword%'";
                else
                    $where .= " (" . $seleccion[$filter] . ")";
            }
            $sql_cnt .= " AND " . $where;
        }
        
        $res = sql($sql_cnt); // or fin_pagina();    
        
        foreach ($link_tmp as $key => $val) {
            $arr_param_url[$key] = $val;
        }
        
        $datos_barra = array(
            array(
                "descripcion"=> "Pendientes (".$res->fields['cnt_pendientes'].")",
                "cmd"        => "p",
                "extra"      => $arr_param_url
            ),
            array(
                "descripcion"=> "Listos a Enviar (".$res->fields['cnt_listos_enviar'].")",
                "cmd"        => "n",
                "extra"      => $arr_param_url
            ),
            array(
                "descripcion"=> "Enviados (".$res->fields['cnt_enviados'].")",
                "cmd"        => "e",
                "extra"      => $arr_param_url
            )
        );
    }

    $sql_tmp = "SELECT benef.clave_beneficiario, benef.numero_doc, 
                       benef.apellido_benef, benef.apellido_benef_otro, 
                       benef.nombre_benef, benef.nombre_benef_otro,
                       benef.municipio, benef.usuario_carga, 
                       benef.fecha_inscripcion, benef.activo AS estado_benef,
                       efec.nombreefector, afil.activo AS estado_smi, afil.mensajebaja,
                       usrC.nombre AS op_carga_nombre, usrC.apellido AS op_carga_apellido,
                       usrV.nombre AS op_verif_nombre, usrV.apellido AS op_verif_apellido,
                       CASE benef.estado_envio WHEN 'p' THEN 'Pendiente' 
                                                WHEN 'n' THEN 'Listo a Enviar'
                                                WHEN 'e' THEN 'Enviado'
                       END  estado_envio
                FROM uad.beneficiarios benef 
                LEFT JOIN sistema.usuarios usrC ON CAST(benef.usuario_carga AS INTEGER)=usrC.id_usuario 
                LEFT JOIN facturacion.smiefectores efec ON benef.cuie_ea=efec.cuie 
                LEFT JOIN sistema.usuarios usrV ON CAST(benef.usuario_verificado AS INTEGER)=usrV.id_usuario 
                LEFT JOIN nacer.smiafiliados afil ON benef.clave_beneficiario=afil.clavebeneficiario 

                ";

    $where_tmp .= $cond_fecha.$cond_user;

    
    
    echo $html_header;
?>

        <form name=form1 action="ins_x_estado_listado.php" method=POST>
            <table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
                <tr>
                    <td align=center>
                        <b>Fecha Inscripci&oacute;n</b>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <b>Desde:</b>
                        <input type=text id="fecha_desde" name="fecha_desde" 
                            readonly="readonly" size=10 maxlength="10" 
                            value="<?php echo $request['fecha_desde'] ?>">
                            <?php echo link_calendario('fecha_desde');?>
                        &nbsp;&nbsp;
                        <b>Hasta:</b>
                        <input type=text id="fecha_hasta" name="fecha_hasta"  
                            readonly="readonly" size=10 maxlength="10" 
                            value="<?php echo $request['fecha_hasta'] ?>">
                            <?php echo link_calendario('fecha_hasta');?>
                    </td>
                </tr>
                <tr><td></td></tr>
                <tr>
                    <td align="center">
                        <b>Operador de Carga:</b>
                        <select name="op_carga" style="width:175px;">
                            <?php if($op_carga){ ?>
                                <option value=""></option>
                                <?php while(!$op_carga->EOF){ ?>
                                    <option value="<?php echo $op_carga->fields['id_usuario']; ?>"
                                        <?php if($op_carga->fields['id_usuario']==$request['op_carga']){ ?>
                                            selected="selected"
                                        <?php } ?>
                                    >
                                        <?php echo $op_carga->fields['apellido'].", ".$op_carga->fields['nombre']." (".$op_carga->fields['login'].")"; ?>
                                    </option>
                                    <?php $op_carga->MoveNext(); ?>
                                <?php } ?>
                            <?php } ?>
                        </select>
                        &nbsp;
                        <b>Usuario Verifica:</b>
                        <select name="us_verif" style="width:175px;">
                            <?php if($us_verif){ $us_verif->MoveFirst(); ?>
                                <option value=""></option>
                                <?php while(!$us_verif->EOF){ ?>
                                    <option value="<?php echo $us_verif->fields['id_usuario']; ?>"
                                        <?php if($us_verif->fields['id_usuario']==$request['us_verif']){ ?>
                                            selected="selected"
                                        <?php } ?>
                                    >
                                        <?php echo $us_verif->fields['apellido'].", ".$us_verif->fields['nombre']." (".$us_verif->fields['login'].")"; ?>
                                    </option>
                                    <?php $us_verif->MoveNext(); ?>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr><td></td></tr>
                <tr>
                    <td align=center>
                        <?php 
                            if(isset($request)&&$request!=""){
                                $action_sql = "buscar";
                            }else{
                                $action_sql = 0;
                            }
                            list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,$action_sql);
                        ?>
                        &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
                    </td>
                </tr>
            </table>
            
            <?php 
                if(isset($request)&&$request!=""){
                    generar_barra_nav($datos_barra); 
                    $result = sql($sql); // or die;
                    include('ins_x_estado_listado_body.php');
                } 
            ?>
        </form>
    </body>
</html>
<?php    
    echo fin_pagina();
?>