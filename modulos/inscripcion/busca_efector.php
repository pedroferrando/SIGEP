<?php
    require_once ("../../config.php");
    extract($_REQUEST,EXTR_SKIP);
    if ($parametros) extract($parametros,EXTR_OVERWRITE);
    echo $html_header;
?>
<link rel=stylesheet type='text/css' href='../../lib/css/general.css'>
<FORM METHOD="get" ACTION="" name="form1" id="form1">
    <h3 align="center">BUSQUEDA DE EFECTOR</h3>
    <label>
        <b>Ingrese Nombre, codigo o palabra clave que identifique al efector</b> 
    </label>
    &nbsp; 
    <input type="text" name="efectores"id="efectores" maxlength="40" value="<?php echo $_REQUEST[efectores]; ?>"/>
    <input type="hidden" name="qkmpo" value="<?php echo $qkmpo; ?>" />
    <input type="hidden" name="grupo_remediar" value="<?php echo $grupo_remediar; ?>" />
    <input type="submit" name="buscar" value="Buscar"/>
</FORM>
<p>&nbsp;</p>
<script type="text/javascript" src="../../lib/jquery-1.7.2.min.js"></script>
<script>
    $('input[name=efectores]').focus();
</script>
<?php
if ($_REQUEST['efectores'] || $_REQUEST['efectores']=='0'){
    $vremediar='n';

    $grupo_remediar= $_GET['grupo_remediar'];
    $nefectores= ($_REQUEST['efectores']);
    $efectores= 'N'.$_REQUEST['efectores'];
 
    $sql = "select a.cuie,r.codigosisa,r.codremediar,a.nombreefector,d.nombre as nomlocalidad,
                   a.localidad,c.nombre as nomdepartamento,a.departamento
            from facturacion.smiefectores a
            inner join nacer.efe_conv b on a.cuie=b.cuie
            inner join general.relacioncodigos r on a.cuie=r.cuie
            inner join uad.departamentos c on a.departamento=c.id_departamento
            inner join uad.localidades d on c.id_departamento=d.id_departamento 
                                         and a.localidad=d.id_localidad
            WHERE  a.nombreEfector ilike '%$nefectores%' ";
    if($grupo_remediar!='s'){
        $sql .= " or upper(a.cuie) = upper('$efectores') or a.cuie ilike '%$nefectores%'";
        $sql .= " or upper(r.codigosisa) = upper('$efectores') or r.codigosisa ilike '%$nefectores%' ";
    }
    $sql .= " or upper(r.codremediar) = upper('$efectores') or r.codremediar ilike '%$nefectores%' ";

    $sql .= " order by c.nombre,d.nombre ";
    
    $res_efectores=sql($sql) or fin_pagina();
    
    if($res_efectores){ $i=0; ?>
        <table  class="tablagenerica" width=100% align="center">
            <tr>
                <th align="center">Cod. SISA</th>
                <th align="center">Cod. Plan Nacer</th>
                <th align="center">Cod. Remediar</th>
                <th align="center">Nombre Efector</th>
                <th align="center">Departamento</th>
                <th align="center">Localidad</th>
            </tr>
            <?php while (!$res_efectores->EOF){ ?>
                    <?php
                        if($i%2==0){
                            $classRow = "con";
                        }else{
                            unset($classRow);
                        }
                    ?>
                    <tr class="<?php echo $classRow;?>">
                        <td><?=$res_efectores->fields['codigosisa']?></td>
                        <td align="center">
                            <a href="javascript:void(0);" 
                               onclick="window.opener.$('select[name=<?php echo $qkmpo?>] option[value=<?php echo $res_efectores->fields['cuie']; ?>]').attr('selected', 'selected');
                                        window.close();" 
                            >
                                <?php echo $res_efectores->fields['cuie']?>
                            </a>
                        </td>
                        <td><?php echo$res_efectores->fields['codremediar']?></td>
                        <td><?php echo utf8_decode($res_efectores->fields['nombreefector']); ?></td>
                        <td><?php echo $res_efectores->fields['nomdepartamento'].'('.$res_efectores->fields['departamento'].')'; ?></td>
                        <td><?php echo $res_efectores->fields['nomlocalidad'].'('.$res_efectores->fields['localidad'].')'; ?></td>
                    </tr>
                    <?php $res_efectores->movenext(); $i++; ?>
            <?php } ?> 
            <?php if($res_efectores->RowCount()==0) {?>
                    <tr class="con">
                        <td align="center" colspan="6">
                            <b>No se encontraron coincidencias</b>
                        </td>
                    </tr>
            <?php } ?>
        </table>
    <?php } ?>
    <p align="right">
        <input type="button" onclick="document.location='javascript:close()';" value="Cerrar Consulta"/>
    </p>
    <!--[if IE]>
        <script type="text/javascript" src="../../lib/ie.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                            hoverTabla('.tablagenerica tr');
                        }
            );
        </script>
    <![endif]-->
<?php } ?>    
<?php echo fin_pagina();// aca termino ?>
