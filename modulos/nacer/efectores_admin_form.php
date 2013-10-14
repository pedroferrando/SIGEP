<?
require_once ("../../config.php");

extract($_POST, EXTR_SKIP);
if ($parametros)
    extract($parametros, EXTR_OVERWRITE);
cargar_calendario();

if ($_POST['guardar_editar'] == "Guardar") {
    $db->StartTrans();

    $fecha_modificacion = date("Y-m-d H:i:s");
    $usuario = $_ses_user['id'];

    $nuevocodremediar = $_POST['codremediar'];
    $query = "UPDATE general.relacioncodigos
                  SET codremediar='$nuevocodremediar'             
             WHERE cuie='$cuie'";

    sql($query, "Error al actualizar el efector") or fin_pagina();

    $db->CompleteTrans();

    $accion = "Se Grabo el Efector.";
}

$query_tiene_remediar = "SELECT * FROM general.relacioncodigos
                                WHERE cuie='$cuie'";
$resultado_tiene_remediar = sql($query_tiene_remediar) or fin_pagina();
$codremediar = $resultado_tiene_remediar->fields['codremediar'];
if ($codremediar != "") {
    $CHECKED = "CHECKED";
} else {
    $CHECKED = "";
}
$desabilefe = 'disabled';

if ($_POST['editar']) {
    $desabil = '';
    $desabiledit = 'disabled';
} else {
    $desabil = 'disabled';
    $desabiledit = '';
}

if ($_POST['cancelar_editar']) {
    $desabil = 'disabled';
    $desabiledit = '';
}

if ($cuie) {
    $query = "SELECT 
    uad.localidades.nombre nlocalidad,
    uad.departamentos.nombre ndepartamento,
    e.nombreefector nefector,
    e.tel,
    e.referente,
    e.cod_pos codpos,
    e.ciudad ciudad,
    e.domicilio domicilio
    FROM
    facturacion.smiefectores e 
    inner join uad.departamentos on departamento=id_departamento 
    inner join uad.localidades on cod_pos=codigopostal 
    where e.cuie='$cuie'";

    $res_factura = sql($query, "Error al traer el Efector") or fin_pagina();

    $nombre = $res_factura->fields['nefector'];
    $domicilio = $res_factura->fields['domicilio'];
    $departamento = $res_factura->fields['ndepartamento'];
    $localidad = $res_factura->fields['nlocalidad'];
    $cod_pos = $res_factura->fields['codpos'];
    $cuidad = $res_factura->fields['ciudad'];
    $referente = $res_factura->fields['referente'];
    $tel = $res_factura->fields['tel'];
}

echo $html_header;
echo "<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js' type='text/javascript'></script>";
?>
<script>

    function control_nuevos()
    { 	 
        if((document.all.checkplanremediar.checked==true)&&(document.all.codremediar.value=="")){
            alert('Debe Ingresar un Codigo de Remediar+Redes');
            return false;
        }
        
        if((document.all.checkplanremediar.checked==true)&&(document.all.codremediar.value.length>5)){
            alert('El Codigo es muy largo');
            return false;
        }
        
        return true;
    }
    
    function eliminar_conv(){
        if(confirm('Esta Seguro que Desea Eliminar el Convenio?'))return true
        else return false;
    }
    
    $(document).ready(function () {
        $("#checkplanremediar").on('change',function(){
            modo=$(this).is(':checked');
            if(modo){
                $("input#codremediar").removeAttr("disabled");
                // $("input#btguardar").removeAttr("disabled");
            }else{
                $("input#codremediar").attr("disabled", "disabled");
                //$("input#btguardar").attr("disabled", "disabled");
                $("input#codremediar").val("");
            }
            
        });
    }); 

</script>

<form name='form1' action='efectores_admin_form.php' method='POST'>
    <input type="hidden" value="<?= $id_efe_conv ?>" name="id_efe_conv">
    <input type="hidden" value="<?= $cuie ?>" name="cuie">
    <? echo "<center><b><font size='+1' color='red'>$accion</font></b></center>"; ?>
    <table width="85%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?= $bgcolor_out ?>' class="bordes">
        <tr id="mo">
            <td>
                <font size=+1><b>Efector</b></font>        
            </td>
        </tr>
        <tr><td>
                <table width=90% align="center" class="bordes">
                    <tr>
                        <td id=mo colspan="2">
                            <b> Descripción del Efector</b>
                        </td>
                    </tr>
                    <tr>	           
                        <td align="center" colspan="2">
                            <b> CUIE: <font size="+1" color="Red"><?= $cuie ?></font> </b>
                        </td>
                    </tr>
                    <tr>	           
                        <td align="center" colspan="2" style="padding-bottom: 10px ">
                            <b><font size="2" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width=90% align="center" class="bordes" style="padding-right: 50px">

                                <tr>
                                    <td align="right">
                                        <b>Nombre:</b>
                                        <input type="text" size="40" value="<?= $nombre ?>" name="nombre" <?= $desabilefe ?>/>
                                    </td> 
                                    <td align="right">
                                        <b>Referente:</b>
                                        <input type="text" size="40" value="<?= $referente ?>" name="referente" <?= $desabilefe ?>/>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <b>Departamento:</b>
                                        <input type="text" size="40" value="<?= $departamento ?>" name="departamento" <?= $desabilefe ?>/>
                                    </td>
                                    <td align="right">
                                        <b>Localidad:</b>
                                        <input type="text" size="40" value="<?= $localidad ?>" name="localidad" <?= $desabilefe ?>/>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <b>Cuidad:</b>
                                        <input type="text" size="40" value="<?= $cuidad ?>" name="cuidad" <?= $desabilefe ?>/>
                                    </td>
                                    <td align="right">
                                        <b>Domicilio:</b>
                                        <input type="text" size="40" value="<?= $domicilio ?>" name="domicilio" <?= $desabilefe ?>/>
                                    </td>  
                                </tr>                                                               
                                <tr>
                                    <td align="right">
                                        <b>Telefono:</b>
                                        <input type="text" size="40" value="<?= $tel ?>" name="tel" <?= $desabilefe ?>/>
                                    </td>
                                    <td align="right">
                                        <b>C.P.:</b>                                                                 
                                        <input type="text" size="40" value="<?= $cod_pos ?>" name="cod_pos" <?= $desabilefe ?>/>
                                    </td> 
                                </tr>
                                <table class="bordes" align="center" width="50%" style="border:thin solid gray; margin-top: 20px;">
                                    <tr style="border:thin solid #000">                                        
                                        <td style="padding-left: 20px;">
                                            Trabaja con el Plan Remediar+Redes <input id="checkplanremediar" type="checkbox" name="planremediar" <?= $CHECKED ?>>
                                        </td>
                                        <td>
                                            Codigo <input id="codremediar" type="text" size="25" value="<?= $codremediar ?>" name="codremediar" disabled="disabled"/>
                                        </td>
                                    </tr>
                                </table>

                                <table class="bordes" align="center" width="100%" style="border-top:thin solid #000; margin-top: 20px;">                            

                                    <tr>
                                        <td align="center">	
                                            <input id="eliminar" type="submit" name="eliminar" value="Eliminar" title="Borrar el Efector" onclick="eliminar_conv()" style="display: none; width:130px" <?php echo $desabiledit ?>/> &nbsp;&nbsp;
                                            <input type="submit" name="editar" value="Editar Campos" onClick="this.form.submit()" title="Editar" style="display: none; width:130px" <?= $desabiledit ?>/> &nbsp;&nbsp;
                                            <input id="btguardar"type="submit" name="guardar_editar" value="Guardar" onclick="return control_nuevos();" title="Guarda Efector" style="width:130px" />&nbsp;&nbsp;
                                            <input type="submit" name="cancelar_editar" value="Cancelar" title="Cancela Edicion de Muletos" <?= $desabil ?> style="width:130px" onclick=""/>		      		      
                                        </td>
                                    </tr> 
                                </table>	
                                <br>

                                <tr><td><table width=100% align="center" class="bordes">
                                            <tr align="center">
                                                <td>
                                                    <input type=button name="volver" value="Volver" onclick="document.location='efectores_admin.php'"title="Volver al Listado" style="width:150px">     
                                                </td>
                                            </tr>
                                        </table></td></tr>
                            </table>
                            </form>

                            <?=
                            fin_pagina(); // aca termino ?>