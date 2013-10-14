<?
require_once ("../../config.php");


extract($_POST, EXTR_SKIP);
if ($parametros)
    extract($parametros, EXTR_OVERWRITE);
cargar_calendario();

if ($_POST['quitarpractica']) {
    $pract = $_POST['quitarpractica'];
    $queryborroexclusion = "DELETE FROM nacer.excluidos
                            WHERE id_excluidos = $pract ";
    sql($queryborroexclusion, "Error al insertar la prestacion") or fin_pagina();
}

if ($_POST['restaurarpractica']) {
    $fecha_carga = date("Y-m-d H:i:s");
    $id_prestacion = $_POST['restaurarpractica'];
    $usuario = $_ses_user['name'];

    if ($id_prestacion != '-1') {
        $db->StartTrans();
        $q = "SELECT  cn.id_conv_nom
              FROM  nacer.conv_nom cn  
              INNER JOIN nacer.efe_conv ec USING (id_efe_conv)
              WHERE ec.CUIE = '$cuie' 
              AND ec.activo=TRUE AND cn.activo=TRUE";
        $conv_nom = sql($q);
        $conv_nom = $conv_nom->fields['id_conv_nom'];

        $query = "INSERT INTO nacer.excluidos
	             (id_conv_nom,cod_practica, usuario, fecha_carga)
	             VALUES($conv_nom,$id_prestacion,'$usuario','$fecha_carga')";
        sql($query, "Error al insertar la Pr&aacute;ctica exclu&iacute;da") or fin_pagina();
        $accion = "Se excluy&oacute; la prestacion";

        $db->CompleteTrans();
    }//de if ($_POST['guardar']=="Guardar nuevo Muleto")
}

//Cargar las practicas
$sql3 = "SELECT distinct on (n.codigo) codigo ,n.descripcion,n.id_nomenclador
                FROM facturacion.nomenclador n";
$res_practicas = sql($sql3) or fin_pagina();

/* if ($res_modo_facturacion->RowCount() > 0){
  $nom_detalle = $res_modo_facturacion->fields['id_nomenclador_detalle'];
  $conv_nom = $res_modo_facturacion->fields['id_conv_nom'];
  //Segun el modo de facturacion carga las practicas
  if ($res_modo_facturacion->fields['modo_facturacion'] == '1') {
  $sql = "SELECT n.id_nomenclador as id,n.codigo,n.descripcion AS nombre_nom
  FROM facturacion.nomenclador n
  WHERE n.id_nomenclador_detalle = '$nom_detalle'
  AND n.id_nomenclador not in (select cod_practica from nacer.excluidos where id_conv_nom=$conv_nom)
  ORDER BY n.codigo";
  }
  $res_practicas = sql($sql) or fin_pagina();
  } */

echo $html_header;

echo "<script src='../../lib/jquery.min.js' type='text/javascript'></script>";
?>
<script>
    var quitarpractica;
    
    $('document').ready(function() {
        $('#prueba_vida').delegate('.quitar','click',function(e){
            e.preventDefault();
            quitarpractica = this.name;
            quitar();
        });
    });
   
   
    function quitar(){
         
        var cuie=$( "#cuie" ).val(); 
        var nombreefector=$( "#nombreefector" ).val();
        
        var confirmar=confirm('Esta Seguro que Desea Excluir la Prestacion?');
        if (confirmar){
            $.post("efec_nom_admin.php",{'quitarpractica':quitarpractica,'cuie':cuie,'nombreefector':nombreefector},function(data){
                var otratabla = $( data ).find( '#tabla_select tbody' );
                $('#tabla_select').empty();
                $('#tabla_select').append(otratabla);
                var tabla = $( data ).find( '#prueba_vida tbody' );
                $('#prueba_vida').empty();
                $('#prueba_vida').append(tabla);
                
            });               
        }
    }
   
    
    //controlan que ingresen todos los datos necesarios par el muleto
    function control_nuevos()
    {
        if(document.all.nomenclador.value=="-1"){
            alert('Debe Seleccionar una PRESTACION');
            return false;
        } 
        var restaurarpractica=$('#selectnomenclador').val();
        var cuie=$( "#cuie" ).val();
        if (confirm('Esta Seguro que Desea Excluir la Prestacion?')){
            $.post("efec_nom_admin.php",{'restaurarpractica':restaurarpractica,'cuie':cuie},function(data){
                var otratabla = $( data ).find( '#tabla_select tbody' );
                $('#tabla_select').empty();
                $('#tabla_select').append(otratabla);
                var tabla = $( data ).find( '#prueba_vida tbody' );
                $('#prueba_vida').empty();
                $('#prueba_vida').append(tabla);
                
            });
        }           
    }//de function control_nuevos()

    var img_ext='<?= $img_ext = '../../imagenes/rigth2.gif' ?>';//imagen extendido
    var img_cont='<?= $img_cont = '../../imagenes/down2.gif' ?>';//imagen contraido
    function muestra_tabla(obj_tabla,nro){
        oimg=eval("document.all.imagen_"+nro);//objeto tipo IMG
        if (obj_tabla.style.display=='none'){
            obj_tabla.style.display='inline';
            oimg.show=0;
            oimg.src=img_ext;
        }
        else{
            obj_tabla.style.display='none';
            oimg.show=1;
            oimg.src=img_cont;
        }
    }

    /**********************************************************/
    //funciones para busqueda abreviada utilizando teclas en la lista que muestra los clientes.
    var digitos=10; //cantidad de digitos buscados
    var puntero=0;
    var buffer=new Array(digitos); //declaraci�n del array Buffer
    var cadena="";

    function buscar_combo(obj)
    {
        var letra = String.fromCharCode(event.keyCode)
        if(puntero >= digitos)
        {
            cadena="";
            puntero=0;
        }   
        //sino busco la cadena tipeada dentro del combo...
        else
        {
            buffer[puntero]=letra;
            //guardo en la posicion puntero la letra tipeada
            cadena=cadena+buffer[puntero]; //armo una cadena con los datos que van ingresando al array
            puntero++;

            //barro todas las opciones que contiene el combo y las comparo la cadena...
            //en el indice cero la opcion no es valida
            for (var opcombo=1;opcombo < obj.length;opcombo++){
                if(obj[opcombo].text.substr(0,puntero).toLowerCase()==cadena.toLowerCase()){
                    obj.selectedIndex=opcombo;break;
                }
            }
        }//del else de if (event.keyCode == 13)
        event.returnValue = false; //invalida la acci�n de pulsado de tecla para evitar busqueda del primer caracter
    }//de function buscar_op_submit(obj)

</script>

<form id='form1' name='form1' action='efec_nom_admin.php' method='POST' accept-charset=utf-8>

    <? echo "<center><b><font size='+2' color='red'>$accion</font></b></center>"; ?>

    <input id="cuie" type="hidden" name="cuie" value="<?= $cuie ?>">
    <input id="nombreefector" type="hidden" name="nombreefector" value="<?= $nombreefector ?>">
    <table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?= $bgcolor_out ?>' class="bordes">
        <tr id="mo">
            <td>
                <font size=+1><b>Exclusiones para Efectores tipo "<?= $parametros['tipoefector'] ?>"</b></font>    
            </td>
        </tr>
        <tr><td>
                <table width=70% align="center" class="bordes">
                    <tr>
                        <td id=mo colspan="2">
                            <?
                            $sql_efectores = "SELECT * FROM facturacion.smiefectores
                                                    WHERE tipoefector ='" . $parametros['tipoefector'] . "'";
                            $efectores_de_este_tipo = sql($sql_efectores) or DIE;
                            ?>


                            <b> Efectores Involucrados</b>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            <select disabled="disabled" size="10" id="nomenclador" name=nomenclador Style="width:70%"> 
                                <? while (!$efectores_de_este_tipo->EOF) { ?>
                                    <option value=<?= $efectores_de_este_tipo->fields['cuie'] ?>>
                                        <?= $efectores_de_este_tipo->fields['cuie'] . " - " . $efectores_de_este_tipo->fields['nombreefector'] ?>
                                    </option>
                                    <?
                                    $efectores_de_este_tipo->movenext();
                                }
                                ?>
                            </select>
                        </td>      
                    </tr>
                </table>     
                <table class="bordes" align="center" width="70%">
                    <tr align="center" id="sub_tabla">
                        <td colspan="2">	
                            Codigos de todas las Prestaciones Incluidas
                        </td>
                    </tr>
                    <tr><td class="bordes"><table id="tabla_select" >

                                <tr>
                                    <td align="right">
                                        <b>C&oacute;digo Nomenclador:</b>
                                    </td>
                                    <td align="left">		          			
                                        <select id="selectnomenclador" name=nomenclador Style="width:700px"
                                                onKeypress="buscar_combo(this);"
                                                onblur="borrar_buffer();"
                                                onchange="borrar_buffer();">
                                            <option value=-1>Seleccione una practica para excluir</option>
                                            <?
                                            //Deberia tomar en cuenta el modo de facturacion antes de buscar las practicas de grupo_prestacion o nomencladores?

                                            while (!$res_practicas->EOF) {
                                                $codigo = $res_practicas->fields['codigo'];
                                                $descripcion = $res_practicas->fields['descripcion'];
                                                ?>
                                                <option Style="width:700px" value=<?= $codigo; ?> >
                                                    <?= trim($codigo) . " - " . trim($descripcion) ?>
                                                </option>
                                                <?
                                                $res_practicas->movenext();
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>	 
                    <tr>
                        <td align="center" colspan="2" class="bordes">		      
                            <input type="button" name="guardar" value="Excluir Prestacion" title="Excluir Prestacion" Style="width:150px" onclick="return control_nuevos();">
                        </td>
                    </tr> 
                </table>	
            </td>
        </tr>

        <tr>
            <td>
                <table width=100% align="center" class="bordes">
                    <tr align="center">
                        <td>
                            <input type=button name="volver" value="Volver" onclick="document.location='efec_nom_listado.php'" title="Volver al Listado" style="width:150px">     
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

    </table>    
</form>
<?= fin_pagina(); // aca termino    ?>
