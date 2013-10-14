        <table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
            <tr id="ma">
                <td align="left" border="0">
                    Total: <?php echo $total; ?>
                </td>
                <td align="right" border="0">
                    <?php if($page!=0){ ?>
                        <!-- link pagina anterior -->
                        <a href="javascript:void(0);" title="Pagina anterior" 
                            onclick="correr_pagina(<?php echo $page; ?>);">
                            <<
                        </a>
                    <?php } ?>    
                    &nbsp;Pagina <?php echo $page+1; ?> &nbsp;
                    <?php if( ($page+1)*$regs < $total ){ ?>
                        <!-- link pagina siguiente -->
                        <a href="javascript:void(0);" title="Pagina siguiente" 
                            onclick="correr_pagina(<?php echo $page+2; ?>);">
                            >>
                        </a>
                    <?php } ?>
                </td>
            </tr>
        </table>


        <table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>

            <tr>
                <td id=mo width="2%">&nbsp;</td>
                <td id=mo width="10%">Fecha Prestaci&oacute;n</td>
                <td id=mo width="35%">Lugar de Realizaci&oacute;n</td>
                <td id=mo width="12%">C&oacute;digo</td>
                <td id=mo width="35%">Descripci&oacute;n</td>
                <td id=mo width="5%">&nbsp;</td>
            </tr>
            <?php if($result){ $idx = $idFila!="" ? $idFila : 0; ?>
                <?php while(!$result->EOF){ ?>
                        <?php 
                            $id_prestacion = $result->fields['id_prestacion']; 
                            if($id_prestacion!="" || $id_prestacion!=null){
                                $gr_et = calcularGrupoEtareo($result->fields['fecha_nacimiento_benef'], $result->fields['fecha_comprobante']);
                                $trz = cargaTrazadora(trim($result->fields['codigo']), $result->fields['diagnostico'], $gr_et['categoria']);
                                $trz_nombre = $trz[2];
                            }else{
                                $trz_nombre = $result->fields['trz'];
                            }
                            if(isset($_REQUEST[buscar]) && $claveBeneficiario=="" && $flag==false){
                                $trz_aux = $trz_nombre;
                                $flag = true;
                            }
                        ?>
                        <tr <?=atrib_tr()?>> 
                            <td align="center"><?php echo ($idx+1);?></td>
                            <td align="center"><?php echo date('d/m/Y',strtotime($result->fields['fecha_comprobante']));?></td>
                            <td><?php echo $result->fields['nombreefector'];?></td>
                            <td><?php echo $result->fields['cod_nomenclador'];//$result->fields['cod_nomenclador']."  ".$result->fields['diagnostico'];?></td>
                            <td>
                                <?php 
                                //var_dump($gr_et)."<br>";
                                if($result->fields['desc_descripcion']!=""){
                                    echo $result->fields['desc_descripcion'];
                                }elseif($result->fields['descripcion']!=""){
                                    echo strtoupper($result->fields['descripcion']);
                                }else{
                                    echo getNombreGenericoPrestacion($trz_nombre);
                                }
                                ?>
                            </td>
                            <td align="center" class="chk_detalle">
                                <?php if($trz_nombre!="" && $trz_nombre!="CATASTROFICOEMB"){ //$result->fields['trz']!=""){ ?>
                                    <input type="checkbox" name="prestacion[]"
                                           <?php if(in_array($idx,$arr_ids)){ ?>
                                           checked="checked"
                                           <?php } ?>
                                           value="<?php echo $idx; ?>" 
                                           onclick="select_trazadora(this,
                                                                    '<?php echo $id_prestacion; ?>',
                                                                    '<?php echo $trz_nombre; ?>',
                                                                    '<?php echo date('Y-m-d',strtotime($result->fields['fecha_comprobante'])); ?>',
                                                                    '<?php echo trim($result->fields['cod_nomenclador']); ?>',
                                                                    '<?php echo $result->fields['cuie']; ?>');"/>
                                <?php } //echo $trz_nombre; ?>
                            </td>
                        </tr>
                        <?php $result->MoveNext(); $idx++; ?>
                <?php }?>
                <?php if($result->_numOfRows>0){ ?>
                    <tr>
                        <td colspan="6" align="right">
                            <input type="button" value="Ver Detalle" onclick="ver_detalle_trazadoras();"
                                    name="ver_detalle_trazadoras" id="ver_detalle_trazadoras"
                                    />
                        </td>
                    </tr>     
                <?php }else{ ?>
                    <tr>
                        <td colspan="5" align="center">
                            No se han encontrado resultados
                        </td>
                    </tr>  
                <?php }?>
            <?php } ?>
        </table>                    
