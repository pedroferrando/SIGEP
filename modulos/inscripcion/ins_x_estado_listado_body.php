                    <table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
                        <tr>
                            <td colspan=12 align=left id=ma>
                                <table width=100%>
                                    <tr id=ma>
                                        <td width=30% align=left><b>Total:</b> <?=$total_muletos?></td>       
                                        <td width=40% align=right><?=$link_pagina?></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <tr>
                            <td align=right id=mo>Clave Beneficiario</td>      	    
                            <td align=right id=mo>
                                <a id=mo href='<?=encode_link("ins_x_estado_listado.php",array("sort"=>"1","up"=>$up))?>'>Documento</a>
                            </td>
                            <td align=right id=mo>Apellido</td>      	    
                            <td align=right id=mo>Nombre</td>
                            <td align=right id=mo>Municipio</td>
                            <td align=right id=mo>
                                <a id=mo href='<?=encode_link("ins_x_estado_listado.php",array("sort"=>"2","up"=>$up))?>'>Efector</a>
                            </td>
                            <td align=right id=mo>Fecha Inscripci&oacute;n</td>
                            <td align=right id=mo>Operador de Carga</td>
                            <td align=right id=mo>Operador Verificador</td>
                            <td align=right id=mo>Estado</td>
                            <td align=right id=mo>Activo</td>
                            <td align=right id=mo>Motivo Baja</td>
                        </tr>
                        <?php if($result){ ?>
                            <?php while(!$result->EOF){ ?>
                                    <tr <?=atrib_tr()?>>     
                                        <td><?php echo $result->fields['clave_beneficiario'];?></td>  
                                        <td><?php echo $result->fields['numero_doc'];?></td>        
                                        <td><?php echo utf8_decode($result->fields['apellido_benef']).' '.$result->fields['apellido_benef_otro']?></td>     
                                        <td><?php echo $result->fields['nombre_benef'].' '.$result->fields['nombre_benef_otro']?></td>     
                                        <td><?php echo $result->fields['municipio'];?></td>
                                        <td><?php echo $result->fields['nombreefector'];?></td>
                                        <td align="center"><?php echo date('d/m/Y',strtotime($result->fields['fecha_inscripcion']));?></td>
                                        <td><?php echo $result->fields['op_carga_nombre'].' '.$result->fields['op_carga_apellido'];?></td>
                                        <td><?php echo $result->fields['op_verif_nombre'].' '.$result->fields['op_verif_apellido'];?></td>
                                        <td align="center"><?php echo $result->fields['estado_envio'];?></td>
                                        <td align="center">
                                            <?php 
                                                if($result->fields['estado_smi']=="S"){
                                                    echo "SI";
                                                }else{
                                                    echo "NO";
                                                }
                                            ?>
                                        </td>
                                        <td><?php echo $result->fields['mensajebaja'];?></td>
                                    </tr>
                                    <?php $result->MoveNext(); ?>
                            <?php }?>
                        <?php }?>
                    </table>