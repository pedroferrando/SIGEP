<?php if($result){ $i=0; ?>
        <table class="tablagenerica" width=100% align=center>
            <tr>
                <td colspan="2" align="left">
                    <b>Total: <?php echo $total; ?></b>
                </td>
                <td colspan="6" align="center">
                    <?php $paginacion->generaPaginacion($total, $back='<', $next='>', $_SERVER[PHP_SELF], $arr_param_url, $classCss='linkPageII');?>
                </td>
                <td colspan="5" align="center"></td>
            </tr>
            <!-- Cabecera de tabla -->
            <tr>
                <th align=center width="4%">
                    <?php
                    $dir = $request[sort]=="b.estado_envio" ? "DESC" : "ASC";
                    $arr_param_url_sort = array_merge($arr_param_url, array("sort"=>"b.estado_envio","dir"=>$dir));
                    ?>
                    <a href='<?=encode_link($_SERVER[PHP_SELF],$arr_param_url_sort)?>' title="Estado de Envio">
                        EE &#9650;&#9660;
                    </a>
                </th>
                <th align=center>Clave Benef</th>
                <th align=center>
                    <?php
                    $dir = $request[sort]=="fecins" && $request[dir]=="ASC" ? "DESC" : "ASC";
                    $arr_param_url_sort = array_merge($arr_param_url, array("sort"=>"fecins","dir"=>$dir));
                    ?>
                    <a href='<?=encode_link($_SERVER[PHP_SELF],$arr_param_url_sort)?>'>
                        Inscr &#9650;&#9660;
                    </a>
                </th>
                <th align=center>
                    <?php
                    $dir = $request[sort]=="b.numero_doc" ? "DESC" : "ASC";
                    $arr_param_url_sort = array_merge($arr_param_url, array("sort"=>"b.numero_doc","dir"=>$dir));
                    ?>
                    <a href='<?=encode_link($_SERVER[PHP_SELF],$arr_param_url_sort)?>'>
                        Documento
                    </a>
                </th>
                <th align=right>
                    <?php
                    $dir = $request[sort]=="b.apellido_benef" ? "DESC" : "ASC";
                    $arr_param_url_sort = array_merge($arr_param_url, array("sort"=>"b.apellido_benef","dir"=>$dir));
                    ?>
                    <a href='<?=encode_link($_SERVER[PHP_SELF],$arr_param_url_sort)?>'>
                        Apellido &#9650;&#9660;
                    </a>
                </th>
                <th align=right>
                    <?php
                    $dir = $request[sort]=="b.nombre_benef" ? "DESC" : "ASC";
                    $arr_param_url_sort = array_merge($arr_param_url, array("sort"=>"b.nombre_benef","dir"=>$dir));
                    ?>
                    <a href='<?=encode_link($_SERVER[PHP_SELF],$arr_param_url_sort)?>'>
                        Nombre &#9650;&#9660;
                    </a>
                </th>
                <th align=center>Fec Nac</a></th>
                <th align=right width="25%">
                    <?php
                    $dir = $request[sort]=="b.cuie_ea" ? "DESC" : "ASC";
                    $arr_param_url_sort = array_merge($arr_param_url, array("sort"=>"b.cuie_ea","dir"=>$dir));
                    ?>
                    <a href='<?=encode_link($_SERVER[PHP_SELF],$arr_param_url_sort)?>'>
                        Efector &#9650;&#9660;
                    </a>
                </th>
                <th align=center title="Plan Sumar">(+)</th>
                <th align=center title="Cobertura Efectiva Basica">CEB</th>
                <th align="center" colspan="3">ACCIONES</th>
            </tr>
            <!-- Fin Cabecera de tabla -->
            <?php while(!$result->EOF){
                switch($result->fields['estado_envio']){
                    case 'p':
                        $classEstado = "icon-spam"; 
                        $titleEstado = "PENDIENTE";
                        $bgcolor = "#F2F58B";
                        break;
                    case 'e':
                        $classEstado = "icon-checkbox-checked"; 
                        $titleEstado = "ENVIADO";
                        $bgcolor = "#77B96C";
                        break;
                    case 'n':
                        $classEstado = "icon-checkbox-unchecked"; 
                        $titleEstado = "NO ENVIADO";
                        $bgcolor = "#FF0B2F";
                        break;
                }
                // seteo la clase css segun su estado en smi
                unset ($class);
                if($result->fields['existe_smi']==0){
                    $class = "icon-minus-alt"; $st = "attr=no"; 
                }elseif($result->fields['activo_smi']=="S"){
                    $class = "icon-check-alt"; $st = "attr=si";
                }else{
                    $class = "icon-x-altx-alt"; $st = "attr=inac";
                }
                if($result->fields['existe_smi']==1 && $result->fields['ceb']=='S'){
                    $classCEB = "icon-checkbox-checked";
                }else{
                    $classCEB = "icon-checkbox-unchecked";
                }
                // seteo links a pagina de incripcion y comprobante
                $lnkInscripcion = encode_link("ins_admin.php",array("id_planilla"=>$result->fields['id_beneficiarios'],
                                                                    "tapa_ver"=>'block'));  	
                $lnkComprobante = encode_link("../facturacion/comprobante_admin_total.php", array("clavebeneficiario" => $result->fields['clave_beneficiario'], 
                                                                                                  "entidad_alta" => 'in', 
                                                                                                  "pagina_listado" => "ins_listado.php"));
                $lnkPrestaciones = encode_link("../facturacion/listado_prestaciones_doms.php", array("nro_doc"=>$result->fields['numero_doc']));
                if($i%2==0){
                    $classRow = "con";
                }else{
                    unset($classRow);
                }
                ?>
                <tr class="<?php echo $classRow;?>">
                    <td align=center>
                        <div class="sprite-gral <?php echo $classEstado; ?>" 
                             title="<?php echo $titleEstado; ?>"></div>
                    </td>
                    <td align=center><?=$result->fields['clave_beneficiario']?></td>
                    <td align=center><?=fecha($result->fields['fecins'])?></td>
                    <td align=center><?=$result->fields['numero_doc']?></td>
                    <td><?=$result->fields['apellido_benef'].' '.$result->fields['apellido_benef_otro']?></td>
                    <td><?=$result->fields['nombre_benef'].' '.$result->fields['nombre_benef_otro']?></td>
                    <td align=center><?=fecha($result->fields['fecnac'])?></td>
                    <td><?=substr(trim($result->fields['nombre']),1,40)?></td>
                    <td align=center>
                        <div class="sprite-gral <?php echo $class; ?>" <?php echo $st; ?>></div>
                    </td>
                    <td align=center>
                        <div class="sprite-gral <?php echo $classCEB; ?>"></div>
                    </td>
                    <td align=center>
                        <a class="sprite-gral icon-profile"
                           title="Ir a la Planilla de Inscripcion"
                           href="<?php echo $lnkInscripcion; ?>" ></a>
                    </td>
                    <td align=center>
                        <a class="sprite-gral icon-aid"
                           title="Ir a Carga de Comprobantes"
                           href="<?php echo $lnkComprobante; ?>" ></a>
                    </td>
                    <td align="center">
                        <a class="sprite-gral icon-stats"
                           title="Ir al Informe de Prestaciones"
                           href="<?php echo $lnkPrestaciones; ?>" ></a>
                    </td>
                </tr>
                <?php 
                    $i++;
                    $result->MoveNext();
                } ?>
                    <tr>
                        <td colspan="2" align="left">
                            <b>Total: <?php echo $total; ?></b>
                        </td>
                        <td colspan="6" align="center">
                            <?php $paginacion->generaPaginacion($total, $back='<', $next='>', $_SERVER[PHP_SELF], $arr_param_url, $classCss='linkPageII');?>
                        </td>
                        <td colspan="5"></td>
                    </tr>
        </table>  
<?php } ?>