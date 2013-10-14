<?
require_once ("../../config.php");

extract($_POST, EXTR_SKIP);
if ($parametros)
    extract($parametros, EXTR_OVERWRITE);
cargar_calendario();

//Guarda Nuevo/Cambios de nomenclador
if ($_POST['guardar'] == "Guardar") {
    $fecha_modificacion = date("Y-m-d H:i:s");
    $usuario = $_ses_user['id'];
    $fecha_desde = Fecha_db($_POST['input_fecha_desde']);
    $fecha_hasta = Fecha_db($_POST['input_fecha_hasta']);
    $descripcion = $_POST['input_descripcion'];
    $modo = $_POST['facturacionselect'];
    $_POST['lista_si'] ? $agregarprestaciones = $_POST['lista_si'] : $agregarprestaciones = array();
    $db->StartTrans();
    if ($_POST['nomenclador'] != "-1") {
        //El nomenclador ya existe, primero quito las practicas que no estan en lista_si
        $_POST['lista_si'] ? $quitarprestaciones = $_POST['lista_si'] : $quitarprestaciones = array();
        if ($modo == 1) {
            $practicasnoincluidas = "(";
            $y = 0;
            if (count($quitarprestaciones) < 1) {
                $practicasnoincluidas = "('0')";
            } else {
                foreach ($quitarprestaciones as $unaprestacion) {
                    if ($y > 0)
                        $practicasnoincluidas.=",";
                    $practicasnoincluidas.="'" . $unaprestacion . "'";
                    $y++;
                }
                $practicasnoincluidas .= ")";
            }

            $query_cuenta_practica = "DELETE FROM facturacion.nomenclador 
                             WHERE id_nomenclador_detalle='$nomenclador'
                             AND replace(codigo,' ','') NOT IN $practicasnoincluidas";
            $result_conteo = sql($query_cuenta_practica, "Error al insertar Nomenclador") or fin_pagina();

            //de las que estan en lista_si agrego al id_nomenclador_detalle solo las nuevas (que no estaban).

            foreach ($agregarprestaciones as $unaprestacion) {
                $query_yatiene = "SELECT codigo
                                     FROM facturacion.nomenclador
                                     WHERE id_nomenclador_detalle=$nomenclador 
                                     AND replace(codigo,' ','')='$unaprestacion'";
                $tiene = sql($query_yatiene, "Error al insertar Nomenclador") or fin_pagina();

                if (!$tiene->RecordCount()) {
                    $query_trae_datos = "SELECT *
                                     FROM facturacion.practicas
                                     WHERE replace(codigo,' ','')='$unaprestacion'";
                    $datos = sql($query_trae_datos, "Error al insertar Nomenclador") or fin_pagina();
                    $dato_codigo = $datos->fields['codigo'];
                    $dato_grupo = $datos->fields['grupo'];
                    $dato_subgrupo = $datos->fields['subgrupo'];
                    $dato_descripcion = $datos->fields['descripcion'];
                    $dato_tiponomenclador = $datos->fields['tipo_nomenclador'];
                    $dato_categoria = $datos->fields['categoria'] ? $datos->fields['categoria'] : 0;
                    $precio = 0; //$datos->fields['precio'];

                    $query = "INSERT INTO facturacion.nomenclador
                        (codigo,grupo,subgrupo,descripcion,precio,tipo_nomenclador,id_nomenclador_detalle,categoria)
                        VALUES('$dato_codigo','$dato_grupo','$dato_subgrupo',
                        '$dato_descripcion','$precio','$dato_tiponomenclador',
                        $nomenclador,'$dato_categoria')";
                    sql($query, "Error al insertar Nomenclador") or fin_pagina();
                }
            }
        }/* else {

          foreach ($quitarprestaciones as $unaprestacion) {
          $query_cuenta_practica = "SELECT COUNT(gp.codigo), gp.codigo, gp.categoria_padre
          FROM nomenclador.grupo_prestacion gp,
          (SELECT codigo,categoria_padre
          FROM nomenclador.grupo_prestacion
          WHERE id_grupo_prestacion=$unaprestacion) s
          WHERE s.codigo=gp.codigo AND s.categoria_padre=gp.categoria_padre
          GROUP BY gp.codigo, gp.categoria_padre";
          $conteo = sql($query_cuenta_practica, "Error al insertar Nomenclador") or fin_pagina();
          if ($conteo->fields['count'] > 1) {
          $uncodigo = $conteo->fields['codigo'];
          $unacategoria = $conteo->fields['categoria_padre'];
          $query_borra_practica = "DELETE FROM nomenclador.grupo_prestacion
          WHERE id_nomenclador_detalle='$nomenclador'
          AND codigo='$uncodigo' AND categoria_padre='$unacategoria'";
          sql($query_borra_practica, "Error al insertar Nomenclador") or fin_pagina();
          } else {
          $query_anula = "UPDATE nomenclador.grupo_prestacion
          SET id_nomenclador_detalle=0
          WHERE id_grupo_prestacion=$unaprestacion";
          sql($query_anula, "Error al insertar Nomenclador") or fin_pagina();
          }
          }
          } */
    } else {
        //ya que no existe (es un nomenclador totalmente nuevo), guarda el nomenclador propiamente dicho
        $query = "INSERT INTO facturacion.nomenclador_detalle(descripcion,fecha_desde,fecha_hasta,modo_facturacion)
                VALUES('$descripcion','$fecha_desde','$fecha_hasta',$modo)";
        sql($query, "Error al insertar Nomenclador") or fin_pagina();
        $accion = "Se Grabo el Nomenclador: $descripcion.";

        $query = "SELECT max(id_nomenclador_detalle) id_nomenclador 
                FROM facturacion.nomenclador_detalle";
        $id_nomenclador = sql($query, "Error al insertar Nomenclador") or fin_pagina();
        $nomenclador = $id_nomenclador->fields['id_nomenclador'];


        if ($modo == 1) {
            foreach ($agregarprestaciones as $unaprestacion) {
                $query_trae_datos = "SELECT *
                                     FROM facturacion.practicas
                                     WHERE replace(codigo,' ','')='$unaprestacion'";
                $datos = sql($query_trae_datos, "Error al insertar Nomenclador") or fin_pagina();
                $dato_codigo = $datos->fields['codigo'];
                $dato_grupo = $datos->fields['grupo'];
                $dato_subgrupo = $datos->fields['subgrupo'];
                $dato_descripcion = $datos->fields['descripcion'];
                $dato_tiponomenclador = $datos->fields['tipo_nomenclador'];
                $dato_categoria = $datos->fields['categoria'] ? $datos->fields['categoria'] : 0;
                $precio = 0; //$datos->fields['precio'];

                $query = "INSERT INTO facturacion.nomenclador
                        (codigo,grupo,subgrupo,descripcion,precio,tipo_nomenclador,id_nomenclador_detalle,categoria)
                        VALUES('$dato_codigo','$dato_grupo','$dato_subgrupo',
                        '$dato_descripcion','$precio','$dato_tiponomenclador',
                        $nomenclador,'$dato_categoria')";
                sql($query, "Error al insertar Nomenclador") or fin_pagina();
            }
        }
    }

    //segun modo de facturacion, agrega las prestaciones a la tabla correspondiente

    $db->CompleteTrans();
    $prestacion = '';
    $precio = '';
    /* else {
      foreach ($agregarprestaciones as $unaprestacion) {
      $query_trae_datos = "SELECT *,to_char(precio,'99999D99') precio
      FROM nomenclador.grupo_prestacion
      WHERE id_grupo_prestacion=$unaprestacion";
      $datos = sql($query_trae_datos, "Error al insertar Nomenclador") or fin_pagina();
      //$dato_id_grupo_prestacion = $datos->fields['id_grupo_prestacion'];
      $dato_tema = $datos->fields['tema'];
      $dato_categoria = $datos->fields['categoria'];
      $dato_codigo = $datos->fields['codigo'];
      $dato_categoria_padre = $datos->fields['categoria_padre'];
      $precio = 0; //$datos->fields['precio'];
      $dato_neo = $datos->fields['neo'];
      $dato_ceroacinco = $datos->fields['ceroacinco'];
      $dato_seisanueve = $datos->fields['seisanueve'];
      $dato_adol = $datos->fields['adol'];
      $dato_adulto = $datos->fields['adulto'];
      $dato_id_categoria_prestacion = $datos->fields['id_categoria_prestacion'];
      $dato_f = $datos->fields['f'];
      $dato_m = $datos->fields['m'];

      $query_yatiene = "SELECT codigo
      FROM nomenclador.grupo_prestacion
      WHERE id_nomenclador_detalle=$nomenclador
      AND codigo='$dato_codigo'";
      $tiene = sql($query_yatiene, "Error al insertar Nomenclador") or fin_pagina();
      if (!$tiene->RecordCount()) {
      $query = "INSERT INTO nomenclador.grupo_prestacion
      (id_categoria_prestacion, tema, categoria, codigo, categoria_padre, precio, id_nomenclador_detalle,
      neo, ceroacinco, seisanueve, adol, adulto, f, m)
      VALUES('$dato_id_categoria_prestacion', '$dato_tema', '$dato_categoria',
      '$dato_codigo', '$dato_categoria_padre', '$precio', '$nomenclador',
      '$dato_neo', '$dato_ceroacinco', '$dato_seisanueve', '$dato_adol', '$dato_adulto',
      '$dato_f', '$dato_m')";
      sql($query, "Error al insertar Nomenclador") or fin_pagina();
      }
      }
      } */
}


if ($_POST['eliminar'] == "Eliminar") {
    //Comprueba que no tenga convenios asociados
    $query_existe_facturacion = "SELECT * 
                                    FROM nacer.conv_nom
                                    WHERE id_nomenclador_detalle='$nomenclador'";
    $esta_usado = sql($query_existe_facturacion, "Error al insertar Nomenclador") or fin_pagina();

    if (!$esta_usado->RecordCount()) {

        $fecha_modificacion = date("Y-m-d H:i:s");
        $usuario = $_ses_user['id'];
        $fecha_desde = Fecha_db($_POST['input_fecha_desde']);
        $fecha_hasta = Fecha_db($_POST['input_fecha_hasta']);
        $descripcion = $_POST['input_descripcion'];
        $modo = $_POST['facturacionselect'];
        $_POST['lista_si'] ? $quitarprestaciones = $_POST['lista_si'] : $quitarprestaciones = array();

        //segun modo de factura
        if ($modo == 1) {
            foreach ($quitarprestaciones as $unaprestacion) {
                /* $query_cuenta_practica = "SELECT COUNT(codigo), codigo 
                  FROM facturacion.nomenclador
                  WHERE codigo IN (SELECT codigo
                  FROM facturacion.nomenclador
                  WHERE id_nomenclador=$unaprestacion)
                  GROUP BY codigo";
                  $conteo = sql($query_cuenta_practica, "Error al insertar Nomenclador") or fin_pagina();
                  if ($conteo->fields[0] > 1) { */
                //$uncodigo = $conteo->fields['codigo'];
                $query_borra_practica = "DELETE FROM facturacion.nomenclador
                WHERE id_nomenclador_detalle='$nomenclador' AND replace(codigo,' ','')='$unaprestacion'";
                sql($query_borra_practica, "Error al insertar Nomenclador") or fin_pagina();
            }
        } else {
            foreach ($quitarprestaciones as $unaprestacion) {
                $query_cuenta_practica = "SELECT COUNT(gp.codigo), gp.codigo, gp.categoria_padre 
                             FROM nomenclador.grupo_prestacion gp,
                             (SELECT codigo,categoria_padre
                             FROM nomenclador.grupo_prestacion
                             WHERE id_grupo_prestacion=$unaprestacion) s
                             WHERE s.codigo=gp.codigo AND s.categoria_padre=gp.categoria_padre
                             GROUP BY gp.codigo, gp.categoria_padre";
                $conteo = sql($query_cuenta_practica, "Error al insertar Nomenclador") or fin_pagina();
                if ($conteo->fields['count'] > 1) {
                    $uncodigo = $conteo->fields['codigo'];
                    $unacategoria = $conteo->fields['categoria_padre'];
                    $query_borra_practica = "DELETE FROM nomenclador.grupo_prestacion
                    WHERE id_nomenclador_detalle='$nomenclador'
                    AND codigo='$uncodigo' AND categoria_padre='$unacategoria'";
                    sql($query_borra_practica, "Error al insertar Nomenclador") or fin_pagina();
                } else {
                    $query_anula = "UPDATE nomenclador.grupo_prestacion
                    SET id_nomenclador_detalle=0
                    WHERE id_grupo_prestacion=$unaprestacion";
                    sql($query_anula, "Error al insertar Nomenclador") or fin_pagina();
                }
            }
        }
        //Borra el nomenclador
        $query_borra = "DELETE FROM facturacion.nomenclador_detalle
                                    WHERE id_nomenclador_detalle='$nomenclador'";
        sql($query_borra, "Error al insertar Nomenclador") or fin_pagina();
        $nomenclador = '-1';
    } else {
        $accion = "No se pueden eliminar nomencladores con convenios asociados";
    }
}

//Si selecciona un nomenclador del select trae su descripcion
if ($_POST['nomenclador'] > 0) {
    $sqlnom = "SELECT * FROM facturacion.nomenclador_detalle
                                WHERE id_nomenclador_detalle=$nomenclador";
    $res_nom = sql($sqlnom) or fin_pagina();
    $descripcion = $res_nom->fields['descripcion'];
    $fecha_desde = $res_nom->fields['fecha_desde'];
    $fecha_hasta = $res_nom->fields['fecha_hasta'];
    $facturacionselect = $res_nom->fields['modo_facturacion'];

    $desabil = "disabled";
    $desabil2 = "";
} else {
    //Si no selecciona un nomenclador del select deshabilita las modificaciones
    $desabil = "";
    $desabil2 = "disabled";
}

//Al seleccionar una practica de la lista, con $.post ejecuta esta consulta
//y devuelve el detalle de la seleccion (segun modo de facturacion)
if ($_POST['selectpractica']) {
    if ($_POST['facturacionselect'] == 1) {
        $buscalapractica = "SELECT n.codigo,n.descripcion,n.id_nomenclador id, to_char(precio,'99999D99') precio
                FROM facturacion.nomenclador n
                WHERE replace(n.codigo,' ','')='" . $_POST['practica'] . "'
                    AND id_nomenclador_detalle='$nomenclador'";
        $datospracticaseleccionada = sql($buscalapractica) or fin_pagina();
    } else {
        $sqlmodsi = "SELECT gp.codigo,gp.id_grupo_prestacion as id,
                gp.categoria AS descripcion, gp.categoria_padre, to_char(precio,'99999D99') precio
                FROM nomenclador.grupo_prestacion gp
              WHERE gp.id_grupo_prestacion='" . $_POST['practica'] . "'";
        $res_presta_si = sql($sqlmodsi) or fin_pagina();
    }
    $prestacion = $datospracticaseleccionada->fields['codigo'];
    $precio = $datospracticaseleccionada->fields['precio'];
}

//Guarda los cambios efectuados a una practica (segun modo de facturacion)
if ($_POST['input_practica']) {
    $db->StartTrans();
    $precionuevo = $_POST['precio'];
    if ($_POST['facturacionselect'] == 1) {
        $sqlmodpractica = "UPDATE facturacion.nomenclador
                    SET precio='" . $_POST['precio'] . "'  
                WHERE replace(codigo,' ','')='" . $_POST['practica'] . "'
                    AND id_nomenclador_detalle='$nomenclador'";
        sql($sqlmodpractica) or fin_pagina();
    } else {
        $sqlmodpractica = "UPDATE nomenclador.grupo_prestacion
                    SET precio='" . $_POST['precio'] . "'  
                WHERE id_grupo_prestacion='" . $_POST['practica'] . "'";
        sql($sqlmodpractica) or fin_pagina();
    }

    $db->CompleteTrans();
}

//Rellena el primer list con todas las practicas disponibles (segun modo de facturacion)
switch ($facturacionselect) {
    case "-1":
    case null:
        break;
    case "1":
        if ($nomenclador > 0) {
            //Separa las practicas disponibles de las practicas que ya estan habilitadas para el nomenclador*
            $sqlmodsi = "SELECT distinct on (n.codigo) codigo ,n.descripcion,n.id_nomenclador id, to_char(precio,'99999D99') precio
                FROM facturacion.nomenclador n
                WHERE n.id_nomenclador_detalle='$nomenclador'";
            $res_presta_si = sql($sqlmodsi) or fin_pagina();


            $sqlmod = "SELECT codigo ,descripcion,id_practica id
                FROM facturacion.practicas p
                WHERE trim(p.codigo) not in (SELECT trim(n.codigo)
                                       FROM facturacion.nomenclador n
                                        WHERE n.id_nomenclador_detalle='$nomenclador')
                ORDER BY p.codigo";

            $res_presta_no = sql($sqlmod) or fin_pagina();
        } else {
            $sqlmod = "SELECT codigo ,descripcion,id_practica id
                FROM facturacion.practicas
                ORDER BY codigo";

            $res_presta_no = sql($sqlmod) or fin_pagina();
        }
        break;
    case "2":
        if ($nomenclador > 0) {
            // *Separa las practicas disponibles de las practicas que ya estan habilitadas para el nomenclador
            $sqlmodsi = "SELECT DISTINCT (gp.categoria_padre||'-'||gp.codigo) codigo,gp.codigo solocodigo,
                gp.id_grupo_prestacion as id,gp.categoria AS descripcion, gp.categoria_padre, to_char(precio,'99999D99') precio
                FROM nomenclador.grupo_prestacion gp
                WHERE gp.id_nomenclador_detalle='$nomenclador' AND categoria_padre<>'' AND trim(gp.tema)='OBJETO DE LA PRESTACION'
                ORDER BY gp.categoria_padre";
            $res_presta_si = sql($sqlmodsi) or fin_pagina();

            $notin = " AND gp.codigo not in (SELECT gp.codigo
                FROM nomenclador.grupo_prestacion gp
                WHERE gp.id_nomenclador_detalle='$nomenclador')";
        }

        $sqlmod = "SELECT DISTINCT ON(gp.categoria_padre||'-'||gp.codigo) codigo,gp.codigo solocodigo,
                gp.id_grupo_prestacion as id,gp.categoria AS descripcion, to_char(precio,'99999D99') precio
                FROM nomenclador.grupo_prestacion gp 
                WHERE trim(gp.tema)='OBJETO DE LA PRESTACION'
                $notin";
        $res_presta_no = sql($sqlmod) or fin_pagina();
        break;
}

echo $html_header;
echo "<script src='../../lib/jquery.min.js' type='text/javascript'></script>";
?>

<script>
    function copiar_nom(){
        $("select#nomenclador").val("-1"); 
        $( "#copiar" ).attr("disabled", "disabled");
        $( "#guardar" ).removeAttr("disabled");
        $( "#inputdescripcion" ).removeAttr("disabled");
        $( "#input_fecha_desde" ).removeAttr("disabled");
        $( "#input_fecha_hasta" ).removeAttr("disabled");
        $("select#facturacionselect").removeAttr("disabled");
        $("#inputprecio").attr("disabled", "disabled");
        $("#inputdescripcion").val("");
        $("#input_fecha_desde").val("");
        $("#input_fecha_hasta").val("");
        return false;
    }    
    var SelID='';
    var SelTEXT='';
    var row=-1;
    
    function mod_presta(SS1)    {        
        for (i=SS1.options.length - 1; i>=0; i--){
            if (SS1.options[i].selected == true)
            {
                SelID=SS1.options[i].value;                
                SelTEXT=lista_si.options[i].text.substr(0, 35);
                row=i;
            }
        }    
        var modo=$("select#facturacionselect").val();
        var nom=$("select#nomenclador").val();
        $.post("nomencladores_admin.php",{'practica':SelID,'facturacionselect':modo,'selectpractica':1,'nomenclador':nom},function(data){
            var contentval1 = $( data ).find( '#inputprestacion' ).val();
            $( "#inputprestacion" ).val(contentval1);
            var contentval2 = $( data ).find( '#inputprecio' ).val();
            $( "#inputprecio" ).val(contentval2);
            if($("select#nomenclador").val()!="-1")
                $("#inputprecio").removeAttr("disabled"); 
        });
    }
    
    function eliminar_nom(){        
        var selObj2 = document.form1.facturacionselect;
        selObj2.disabled=false;        
        var selObj = document.form1.lista_si;
        for (var i=0; i<selObj.options.length; i++) {
            selObj.options[i].selected = true;
        }        
        return true;
    }
    
    function guardar_nom(){
        if(document.all.input_fecha_desde.value==""){
            alert('Debe Ingresar una Fecha de Fin de Vigencia');
            return false;
        }
        if(document.all.input_fecha_hasta.value==""){
            alert('Debe Ingresar una Fecha de Inicio de Vigencia');
            return false;
        }
        if(document.all.input_descripcion.value==""){
            alert('Debe Ingresar una descripcion');
            return false;
        }
        if(document.all.facturacionselect.value==""){
            alert('Debe Ingresar un Modo de Facturacion');
            return false;
        }        
        var selObj = document.form1.lista_si;
        for (var i=0; i<selObj.options.length; i++) {
            selObj.options[i].selected = true;
        }        
        var selObj3 = document.form1.lista_no;
        for (var i=0; i<selObj3.options.length; i++) {
            selObj3.options[i].selected = true;
        }        
        $( "#facturacionselect" ).removeAttr("disabled");
        $( "#input_fecha_desde" ).removeAttr("disabled");
        $( "#input_fecha_hasta" ).removeAttr("disabled");
        $( "#inputdescripcion" ).removeAttr("disabled");        
        return true;
    }
    
    function guardar_practica(){
        if(document.all.input_prestacion.value==""){
            alert('Debe seleccionar una Practica para modificar');
            return false;
        }
        if(document.all.input_precio.value==""){
            alert('Debe Ingresar un Precio para la Practica');
            return false;
        }
        var precionuevo=$( "#inputprecio" ).val(); 
        var nomenclador=$( "#nomenclador" ).val(); 
        var modo=$("select#facturacionselect").val();
        $.post("nomencladores_admin.php",{'practica':SelID,'precio':precionuevo,'facturacionselect':modo,'input_practica':1,'nomenclador':nomenclador},function(data){          
            var contentsi = $( data ).find( '#lista_si' );
            $( "#td_lista_si" ).empty().append(contentsi);
        });
    }    
    
    function SelectMoveRows(SS1,SS2)    {
        var SelID='';
        var SelText='';
        // Move rows from SS1 to SS2 from bottom to top
        for (i=SS1.options.length - 1; i>=0; i--)
        {
            if (SS1.options[i].selected == true)
            {
                SelID=SS1.options[i].value;
                SelText=SS1.options[i].text;
                var newRow = new Option(SelText,SelID);
                SS2.options[SS2.length]=newRow;
                SS1.options[i]=null;
            }
        }
    }
    
    function SelectMoveRowsAll(SS1,SS2)    {
        var SelID='';
        var SelText='';
        // Move rows from SS1 to SS2
        for (i=SS1.options.length - 1 ; i>=0; i--)
        {
            SelID=SS1.options[i].value;
            SelText=SS1.options[i].text;
            var newRow = new Option(SelText,SelID);
            SS2.options[SS2.length]=newRow;
            SS1.options[i]=null;
        }
    }
    
    $(document).ready(function () {
        $("select#facturacionselect").on('change',function(){
            var modo=$("select#facturacionselect").val();
            $.post("nomencladores_admin.php",{'facturacionselect':modo},function(data){
                var contentsi = $( data ).find( '#lista_si' );
                $( "#td_lista_si" ).empty().append(contentsi);
                var contentno = $( data ).find( '#lista_no' );
                $( "#td_lista_no" ).empty().append(contentno);
            }
        );
        });                
                
        $("select#nomenclador").on('change',function(){
            var nom=$("select#nomenclador").val();
            $.post("nomencladores_admin.php",{'nomenclador':nom},function(data){
                var descrip = $( data ).find( '#inputdescripcion' );
                $( "#descripcion" ).empty().append(descrip);
                var fd = $( data ).find( '#input_fecha_desde' ).val();
                $( "#input_fecha_desde" ).attr('value', fd);//.val(fd);
                //empty().append(fd);
                var fh = $( data ).find( '#input_fecha_hasta').val();
                $( "#input_fecha_hasta" ).attr('value', fh);
                //empty().append(fh);
                var modo = $( data ).find( 'select#facturacionselect' ).val();
                $( "select#facturacionselect" ).val(modo);
                if(modo<1){
                    $( "select#facturacionselect" ).removeAttr("disabled"); 
                    $( "#copiar" ).attr("disabled", "disabled");
                    $( "#eliminar" ).attr("disabled", "disabled");
                    $( "#inputdescripcion" ).removeAttr("disabled");
                    $( "#input_fecha_desde" ).removeAttr("disabled");
                    $( "#input_fecha_hasta" ).removeAttr("disabled");
                }else{ 
                    $( "select#facturacionselect" ).attr("disabled", "disabled");
                    $( "#copiar" ).removeAttr("disabled");
                    $( "#eliminar" ).removeAttr("disabled");
                    $( "#inputdescripcion" ).attr("disabled", "disabled");
                    $( "#input_fecha_desde" ).attr("disabled", "disabled");
                    $( "#input_fecha_hasta" ).attr("disabled", "disabled");
                }       
                var contentsi = $( data ).find( '#lista_si' );
                $( "#td_lista_si" ).empty().append(contentsi);
                var contentno = $( data ).find( '#lista_no' );
                $( "#td_lista_no" ).empty().append(contentno);
                $("#inputprestacion").val("");
                $("#inputprecio").val("");
                $("#inputprecio").attr("disabled", "disabled");
            });
        });
    });
</script>
<form id='form1' name='form1' action='nomencladores_admin.php' method='POST'>
    <? echo "<center><b><font size='+1' color='red'>$accion</font></b></center>"; ?>
    <table width="85%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?= $bgcolor_out ?>' class="bordes">
        <tr id="mo">
            <td>
                <font size=+1><b>Nomenclador</b></font> 
                <select id="nomenclador" name=nomenclador Style="width:180px"> 
                    <option selected="selected" value="-1">--Nuevo Nomenclador--</option>
                    <?php
                    $sql = "SELECT id_nomenclador_detalle,descripcion
                            FROM facturacion.nomenclador_detalle
                            WHERE id_nomenclador_detalle > 0
                            ORDER BY id_nomenclador_detalle";
                    $res = sql($sql) or fin_pagina();
                    while (!$res->EOF) {
                        $id_nomenclador_detalle = $res->fields['id_nomenclador_detalle'];
                        $detalle = $res->fields['descripcion'];
                        ?>                                                            
                        <option value=<?=
                    $id_nomenclador_detalle;
                    if ($nomenclador == $id_nomenclador_detalle)
                        echo " selected"
                            ?>>
                                    <?= $detalle ?>
                        </option>
                        <?
                        $res->movenext();
                    }
                    ?>                    
                </select>
                <input id="copiar" type="hidden" name="copiar" value="Copiar" title="Usar como base de un Nuevo Nomenclador" onclick="copiar_nom()" <?php echo $desabil2 ?>/>
                <input id="eliminar" type="submit" name="eliminar" value="Eliminar" title="Borrar el Nomenclador" onclick="eliminar_nom()" <?php echo $desabil2 ?>/>
                <input id="guardar" type="submit" name="guardar" value="Guardar" title="Guardar los datos del Nomenclador" onclick="return guardar_nom();"/>
            </td>
        </tr>
        <tr>
            <td>
                <table width=90% align="center" class="bordes">
                    <tr>
                        <td id=mo colspan="2">
                            <b> Descripción del Nomenclador</b>
                        </td>
                    </tr>
                    <?php ?>
                    <tr>
                        <td>
                            <table width=80% align="center" class="bordes" style="padding-top: 10px;padding-left: 20px">
                                <tr>
                                    <td align="right">
                                        <b>Descripcion:</b>
                                    </td>
                                    <td align="left" id="descripcion">
                                        <input id="inputdescripcion" type="text" size="25" value="<?= $descripcion ?>" name=input_descripcion <?php echo $desabil; ?>/>
                                    </td> 
                                    <td align="right">
                                        <b>Modo de Facturacion:</b>
                                    </td>
                                    <td align="left" id="modo" style="padding-bottom: 10px">
                                        <select <?php echo $desabil; ?> id="facturacionselect" name=facturacionselect > 
                                            <option value="-1" selected="selected">-- Seleccione --</option>
                                            <option value="1" <?php if ($facturacionselect == "1")
                        echo "selected" ?>>1</option> 
                                            <option value="2" <?php if ($facturacionselect == "2")
                                                    echo "selected" ?>>2</option>

                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <b>Fecha desde:</b>
                                    </td>
                                    <td align="left" id="fecha_desde">		 
                                        <input id="input_fecha_desde" type="text" size="20" value="<?= fecha($fecha_desde) ?>" name="input_fecha_desde" <?php echo $desabil; ?>><?= link_calendario("input_fecha_desde"); ?>
                                    </td>
                                    <td align="right">
                                        <b>Fecha hasta:</b>
                                    </td>
                                    <td align="left" id="fecha_hasta">		 
                                        <input id="input_fecha_hasta" type="text" size="20" value="<?= fecha($fecha_hasta) ?>" name="input_fecha_hasta" <?php echo $desabil; ?>><?= link_calendario("input_fecha_hasta"); ?>
                                    </td>
                                </tr>   
                            </table>
                            <table width=90% align="center" class="bordes"  style="padding-top: 10px">
                                <tr>                        
                                    <td id="mo" colspan="2">		          			
                                        <b>Detalles de Prestaciones</b>
                                    </td>
                                </tr>                                
                                <tr>
                                    <td id="form_modif_presta"style="padding-bottom: 8px;padding-top: 15px">
                                        <div action style="width: 700px;margin-left: auto;margin-right: auto;">                                            
                                            <div align="right">
                                                <b>Prestacion:</b>
                                                <input id="inputprestacion" type="text" size="20" value="<?= $prestacion ?>" name=input_prestacion disabled="disabled"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <b>Precio:</b>
                                                <input id="inputprecio" type="text" size="20" value="<?= $precio ?>" name=input_precio disabled="disabled"/>
                                                <input id="inputprecionuevo" type="hidden" size="20" value="<?= $precionuevo ?>" name=input_precio_nuevo />
                                                <input id="input_practica" name="input_practica" type="button" value="Confirmar" title="Ingresar Modificacion" onclick="guardar_practica()"></input>
                                                <b style="color: red; font-size: .8em">*Indique el precio especifico para cada practica</b>
                                            </div>
                                        </div>
                                </tr>
                        </td>
                    <tr>                        
                        <td >	
                            <div style="margin-left: auto;margin-right: auto;border-bottom:solid #006A9E; text-align: center;width: 350px">
                                <b>Listado de Prestaciones Existentes/ Incluidas</b>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width=80% align="center" class="bordes" style="padding-left: 20px">
                                <tr>
                                    <td align="right" id="td_lista_no">
                                        <select name="lista_no[]"  size="20" multiple="multiple" id="lista_no" style="width: 350px">
                                            <?php
                                            if ($facturacionselect > 0) {
                                                while (!$res_presta_no->EOF) {
                                                    $codigo = $res_presta_no->fields['codigo'];
                                                    $id_nomenclador = $res_presta_no->fields['id'];
                                                    $descripcion = $res_presta_no->fields['descripcion'];
                                                    $precio = $res_presta_no->fields['precio'];
                                                    ?>
                                                    <option value=<?= str_replace(" ", "", $codigo); ?>>
                                                        <? $var = substr($codigo . "-" . $descripcion, 0, 50);
                                                        echo $var; //str_pad($var, 40, '.', STR_PAD_RIGHT); ?>
                                                    </option> 
                                                    <?
                                                    $res_presta_no->movenext();
                                                }
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td  align="left" style="width: 10px">
                                        <input type="button" name="addall" value=">>" id="addall" onClick="SelectMoveRowsAll(document.form1.lista_no,document.form1.lista_si)" style="padding-top: 5px"/>
                                        <input type="button" name="add" value=">" id="add" onClick="SelectMoveRows(document.form1.lista_no,document.form1.lista_si)" style="padding-top: 5px"/>
                                        <input type="button" name="del" value="<" id="del" onClick="SelectMoveRows(document.form1.lista_si,document.form1.lista_no)" style="padding-top: 5px"/>
                                        <input type="button" name="delall" value="<<" id="delall" onClick="SelectMoveRowsAll(document.form1.lista_si,document.form1.lista_no)" style="padding-top: 5px"/>
                                    </td>
                                    <td id="td_lista_si" align="left">
                                        <select onclick="mod_presta(document.form1.lista_si)" name="lista_si[]"  size="20" multiple="multiple" id="lista_si" style="width: 350px">
                                            <?php
                                            if ($nomenclador > 0) {
                                                while (!$res_presta_si->EOF) {
                                                    $codigo = trim($res_presta_si->fields['codigo']);
                                                    $id_nomenclador = $res_presta_si->fields['id'];
                                                    $descripcion = $res_presta_si->fields['descripcion'];
                                                    $precio = $res_presta_si->fields['precio'];
                                                    ($precio == 0) ? $color_style = '#ea8f83' : $color_style = '';
                                                    ?>
                                                    <option Style="background-color: <?= $color_style ?>;" value=<?php echo str_replace(" ", "", $codigo); ?> >
                                                        <? $var = substr($codigo . "-" . $descripcion, 0, 30);
                                                        echo str_pad($var, 40, '.', STR_PAD_RIGHT) . "... ( $" . $precio . ")"; ?>
                                                    </option> 
                                                    <?
                                                    $res_presta_si->movenext();
                                                }
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </td>      
                    </tr> 
                </table>   
    </table> 
</td> 
</tr>
</table>
</form>

<?=
fin_pagina(); // aca termino ?>