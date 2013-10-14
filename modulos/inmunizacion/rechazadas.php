<?php
require_once ("../../config.php"); 

if(isset($_GET['cuie'])) {
    	$cuie = $_GET['cuie'];
}

$query ="select * from inmunizacion.prestaciones_inmu_debitos where id_archivo=$_GET[id_archivo]";
//print_r($_GET);
$query_num =  sql($query);
$num_total_registros = $query_num->recordcount();

//Si hay registros
if ($num_total_registros > 0) {
	//numero de registros por página
    $rowsPerPage = 50;
//    if(isset($_GET['filas'])) {
//    	if ($_GET['filas'] > 0){
//            $rowsPerPage = $_GET['filas'];
//        }
//        
//    }

    //por defecto mostramos la página 1
    $pageNum = 1;

    // si $_GET['page'] esta definido, usamos este número de página
    if(isset($_GET['page'])) {
	sleep(1);
    	if ($_GET['page'] > 0){
            $pageNum = $_GET['page'];
        }
    }
		
	//echo 'page'.$_GET['page'];

    //contando el desplazamiento
    $offset = ($pageNum - 1) * $rowsPerPage;
    $total_paginas = ceil($num_total_registros / $rowsPerPage);
    
    $a="";
    $b="";
    $c="";
    $d="";
    $e="";
    $f="";
    $g="";
    $h="";
    $i="";
    $j="";
    $k="";
    $l="";
    $m="";
    $n="";
    $o="";
    $p="";
    $q="";
    $r="";
    $s="";
    $t="";
    $u="";
    
?>
    <div id="titulo">Vacunas Rechazadas</div>    
    <div class="datagrid">
        <table>
            <thead>
                <tr class="">
                    <th class="<?=$a?>">mensajes</th>
                    <th class="<?=$k?>">clave_beneficiario</th>
                    <th class="<?=$b?>">numero_fila</th>
                    <th class="<?=$c?>">linea</th>
<!--                    <th class="<?=$d?>">cuie</th>
                    <th class="<?=$e?>">id_terreno</th>
                    <th class="<?=$f?>">fecha_inmunizacion</th>
                    <th class="<?=$g?>">fecha_nacimiento</th>
                    <th class="<?=$h?>">fecha_vencimiento</th>
                    <th class="<?=$i?>">laboratorio</th>
                    <th class="<?=$j?>">fecha_carga</th>
                    
                    <th class="<?=$l?>">id_comprobante</th>
                    <th class="<?=$m?>">id_prestacion</th>
                    <th class="<?=$n?>">id_presentacion</th>
                    <th class="<?=$o?>">id_grupo_riesgo</th>
                    <th class="<?=$p?>">origen</th>
                    <th class="<?=$q?>">id_usuario</th>
                    <th class="<?=$r?>">lote</th>
                    <th class="<?=$s?>">id_prestacion_cristian</th>
                    <th class="<?=$t?>">id_archivo</th>
                    <th class="<?=$u?>">terreno</th>-->
                </tr>
            </thead>
            <tbody>
                <?php
                $query ="select * from inmunizacion.prestaciones_inmu_debitos where id_archivo='$_GET[id_archivo]' order by id_prestacion_inmu_debito limit $rowsPerPage offset $offset";
                $query_num_archive =  sql($query);
                $alternate=0;
                if ($query_num_archive) {
                    while (!$query_num_archive->EOF) {
                        if($alternate==0){
                        ?>
                            <tr class="sin" onclick="">
                                <td class="<?=$a?>"><?= $query_num_archive->fields["mensajes"] ?></td>
                                <td class="<?=$k?>"><?= $query_num_archive->fields["clave_beneficiario"] ?></td>
                                <td class="<?=$b?>"><?= $query_num_archive->fields["numero_fila"] ?></td>
                                <td class="<?=$c?>"><?= $query_num_archive->fields["linea"] ?></td>
<!--                                <td class="<?=$d?>"><?= $query_num_archive->fields["cuie"] ?></td>
                                <td class="<?=$e?>"><?= $query_num_archive->fields["id_terreno"] ?></td>
                                <td class="<?=$f?>"><?= $query_num_archive->fields["fecha_inmunizacion"] ?></td>
                                <td class="<?=$g?>"><?= $query_num_archive->fields["fecha_nacimiento"] ?></td>
                                <td class="<?=$h?>"><?= $query_num_archive->fields["fecha_vencimiento"] ?></td>
                                <td class="<?=$i?>"><?= $query_num_archive->fields["laboratorio"] ?></td>
                                <td class="<?=$j?>"><?= $query_num_archive->fields["fecha_carga"] ?></td>
                               
                                <td class="<?=$l?>"><?= $query_num_archive->fields["id_comprobante"] ?></td>
                                <td class="<?=$m?>"><?= $query_num_archive->fields["id_prestacion"] ?></td>
                                <td class="<?=$n?>"><?= $query_num_archive->fields["id_presentacion"] ?></td>
                                <td class="<?=$o?>"><?= $query_num_archive->fields["id_grupo_riesgo"] ?></td>
                                <td class="<?=$p?>"><?= $query_num_archive->fields["origen"] ?></td>
                                <td class="<?=$q?>"><?= $query_num_archive->fields["id_usuario"] ?></td>
                                <td class="<?=$r?>"><?= $query_num_archive->fields["lote"] ?></td>
                                <td class="<?=$s?>"><?= $query_num_archive->fields["id_prestacion_cristian"] ?></td>
                                <td class="<?=$t?>"><?= $query_num_archive->fields["id_archivo"] ?></td>
                                <td class="<?=$u?>"><?= $query_num_archive->fields["terreno"] ?></td>-->
                                
                                
                            </tr>
                            <?php 
                            $alternate=1;
                        }else{
                            ?> 
                            <tr class="con" onclick="">
                                <td class="<?=$a?>"><?= $query_num_archive->fields["mensajes"] ?></td>
                                <td class="<?=$k?>"><?= $query_num_archive->fields["clave_beneficiario"] ?></td>
                                <td class="<?=$b?>"><?= $query_num_archive->fields["numero_fila"] ?></td>
                                <td class="<?=$c?>"><?= $query_num_archive->fields["linea"] ?></td>
<!--                                <td class="<?=$d?>"><?= $query_num_archive->fields["cuie"] ?></td>
                                <td class="<?=$e?>"><?= $query_num_archive->fields["id_terreno"] ?></td>
                                <td class="<?=$f?>"><?= $query_num_archive->fields["fecha_inmunizacion"] ?></td>
                                <td class="<?=$g?>"><?= $query_num_archive->fields["fecha_nacimiento"] ?></td>
                                <td class="<?=$h?>"><?= $query_num_archive->fields["fecha_vencimiento"] ?></td>
                                <td class="<?=$i?>"><?= $query_num_archive->fields["laboratorio"] ?></td>
                                <td class="<?=$j?>"><?= $query_num_archive->fields["fecha_carga"] ?></td>
                        
                                <td class="<?=$l?>"><?= $query_num_archive->fields["id_comprobante"] ?></td>
                                <td class="<?=$m?>"><?= $query_num_archive->fields["id_prestacion"] ?></td>
                                <td class="<?=$n?>"><?= $query_num_archive->fields["id_presentacion"] ?></td>
                                <td class="<?=$o?>"><?= $query_num_archive->fields["id_grupo_riesgo"] ?></td>
                                <td class="<?=$p?>"><?= $query_num_archive->fields["origen"] ?></td>
                                <td class="<?=$q?>"><?= $query_num_archive->fields["id_usuario"] ?></td>
                                <td class="<?=$r?>"><?= $query_num_archive->fields["lote"] ?></td>
                                <td class="<?=$s?>"><?= $query_num_archive->fields["id_prestacion_cristian"] ?></td>
                                <td class="<?=$t?>"><?= $query_num_archive->fields["id_archivo"] ?></td>
                                <td class="<?=$u?>"><?= $query_num_archive->fields["terreno"] ?></td>-->
                            </tr>
                            <?php   
                            $alternate=0;
                        }
                        $query_num_archive->movenext();
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
<?php 
	 if ($total_paginas > 1) {
                        echo '<div class="pagination">';
                        echo '<ul>';
                            if ($pageNum != 1)
                                    echo '<li><a class="paginate_archivo" onclick="paginar_rechazadas('.$_GET[id_archivo].",".($pageNum-1).')" data="'.($pageNum-1).'">Anterior</a></li>';
                            	for ($i=1;$i<=$total_paginas;$i++) {
                                    if ($pageNum == $i)
                                            //si muestro el índice de la página actual, no coloco enlace
                                            echo '<li class="active"><a>'.$i.'</a></li>';
                                    else
                                            //si el índice no corresponde con la página mostrada actualmente,
                                            //coloco el enlace para ir a esa página
                                            echo '<li><a class="paginate_archivo" onclick="paginar_rechazadas('.$_GET[id_archivo].",".($i).')" data="'.$i.'">'.$i.'</a></li>';
                            }
                            if ($pageNum != $total_paginas)
                                    echo '<li><a class="paginate" onclick="paginar_rechazadas('.$_GET[id_archivo].",".($pageNum+1).')" data="'.($pageNum+1).'">Siguiente</a></li>';
                       echo '</ul>';
                       echo '</div>';
                    }
	
}

echo '<div class="pagination">';
    echo '<ul>';   ?>    
    <li><a class="volver" onclick="xx('<?php echo $_GET[cuie];?>')" data="<?php echo $_GET['filas'];?>"></a></li>
    <?php
    //echo '<li><a class="volver" onclick="xx('.$_GET[cuie].')" data="'.$_GET[filas].'">Volver</a></li>';
    echo '</ul>';
echo '</div>';
?>

