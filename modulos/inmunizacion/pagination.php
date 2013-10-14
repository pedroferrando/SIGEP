<?php
require_once ("../../config.php"); 

$cuie=1;
if(isset($_GET['cuie'])) {
    	$cuie = $_GET['cuie'];
}
if($cuie==1){
    $query ="select a.*, b.login,c.nombreefector
            from inmunizacion.archivos a
            inner join sistema.usuarios b using (id_usuario)
            inner join facturacion.smiefectores c using (cuie)";
}else{
    $query ="select a.*, b.login,c.nombreefector
            from inmunizacion.archivos a
            inner join sistema.usuarios b using (id_usuario)
            inner join facturacion.smiefectores c using (cuie)
            where cuie='$cuie'";
}
//print_r($_GET);
$query_num_services =  sql($query);
$num_total_registros = $query_num_services->recordcount();

//Si hay registros
if ($num_total_registros > 0) {
	//numero de registros por página
    $rowsPerPage = 10;
    if(isset($_GET['filas'])) {
    	if ($_GET['filas'] > 0){
            $rowsPerPage = $_GET['filas'];
        }
        
    }

    //por defecto mostramos la página 1
    $pageNum = 1;

    // si $_GET['page'] esta definido, usamos este número de página
    if(isset($_GET['page'])) {
	sleep(1);
    	$pageNum = $_GET['page'];
    }
		
	//echo 'page'.$_GET['page'];

    //contando el desplazamiento
    $offset = ($pageNum - 1) * $rowsPerPage;
    $total_paginas = ceil($num_total_registros / $rowsPerPage);
?>
    <div id="titulo">Archivos</div>    
    <div class="datagrid">
        <table>
            <thead>
                <tr>
                    <th id="izquierda"></th>
                    <th>Id Archivo</th>
                    <th>Periodo</th>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>F. Carga</th>
                    <th>Efector</th>
                    <th>Id Archivo C.</th>
                    <th>Aceptadas</th>
                    <th>Rechazadas</th>
                    <th class="">id_cierre</th>
                    <th class="">id_liquidacion</th>
                    <th id="derecha"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = $query." order by id_archivo limit $rowsPerPage offset $offset";
                $query_num_archive =  sql($query);
                $alternate=0;
                if ($query_num_archive) {
                    while (!$query_num_archive->EOF) {
                        if($alternate==0){
                        ?>
                            <tr class="sin">
                                <th class="botones"><span class="icon_file" onclick=""></span></th>
                                <td><?= $query_num_archive->fields["id_archivo"] ?></td>
                                <td><?= $query_num_archive->fields["periodo"] ?></td>
                                <td><?= $query_num_archive->fields["nombre_archivo"] ?></td>
                                <td><?= $query_num_archive->fields["login"] ?></td>
                                <td><?= $query_num_archive->fields["fecha_carga"] ?></td>
                                <td><?= $query_num_archive->fields["nombreefector"] ?></td>
                                <td><?= $query_num_archive->fields["id_archivo_cristian"] ?></td>
                                <td class="link" onclick="archivo_click('<?php echo $query_num_archive->fields["id_archivo"];?>')"><?= $query_num_archive->fields["aceptadas"] ?></td>
                                <td class="link" id="link_rechazadas" onclick="rechazadas_click('<?php echo $query_num_archive->fields["id_archivo"];?>')"><?= $query_num_archive->fields["rechazadas"] ?></td>
                                <td class=""><?= $query_num_archive->fields["id_cierre"] ?></td>
                                <td class=""><?= $query_num_archive->fields["id_liquidacion"] ?></td>
                                <th class="">
<!--                                <span class="aceptadas" onclick="archivo_click('<?php echo $query_num_archive->fields["id_archivo"];?>')"></span>
                                    <span class="rechazadas" onclick="rechazadas_click('<?php echo $query_num_archive->fields["id_archivo"];?>')"></span>-->
                                    <span class="trash" onclick="borrar_click('<?php echo $query_num_archive->fields["id_archivo"]."','".$query_num_archive->fields["id_cierre"]."','".$query_num_archive->fields["id_liquidacion"];?>')"><span class='lid'></span><span class='can'></span></span>
                                </th>
                            </tr>
                            <?php 
                            $alternate=1;
                        }else{
                            ?> 
                            <tr class="con">
                                <th class=""><span class="icon_file" onclick=""></span></th>
                                <td><?= $query_num_archive->fields["id_archivo"] ?></td>
                                <td><?= $query_num_archive->fields["periodo"] ?></td>
                                <td><?= $query_num_archive->fields["nombre_archivo"] ?></td>
                                <td><?= $query_num_archive->fields["login"] ?></td>
                                <td><?= $query_num_archive->fields["fecha_carga"] ?></td>
                                <td><?= $query_num_archive->fields["nombreefector"] ?></td>
                                <td><?= $query_num_archive->fields["id_archivo_cristian"] ?></td>
                                <td class="link" onclick="archivo_click('<?php echo $query_num_archive->fields["id_archivo"];?>')"><?= $query_num_archive->fields["aceptadas"] ?></td>
                                <td class="link" id="link_rechazadas" onclick="rechazadas_click('<?php echo $query_num_archive->fields["id_archivo"];?>')"><?= $query_num_archive->fields["rechazadas"] ?></td>
                                <td class=""><?= $query_num_archive->fields["id_cierre"] ?></td>
                                <td class=""><?= $query_num_archive->fields["id_liquidacion"] ?></td>
                                <td class="">
<!--                               <span class="aceptadas" onclick="archivo_click('<?php echo $query_num_archive->fields["id_archivo"];?>')"></span>
                                   <span class="rechazadas" onclick="rechazadas_click('<?php echo $query_num_archive->fields["id_archivo"];?>')"></span>-->
                                   <span class="trash" onclick="borrar_click('<?php echo $query_num_archive->fields["id_archivo"]."','".$query_num_archive->fields["id_cierre"]."','".$query_num_archive->fields["id_liquidacion"];?>')"><span class='lid'></span><span class='can'></span></span>
                                </td>
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
                                    echo '<li><a class="paginate" data="'.($pageNum-1).'">Anterior</a></li>';
                            	for ($i=1;$i<=$total_paginas;$i++) {
                                    if ($pageNum == $i)
                                            //si muestro el índice de la página actual, no coloco enlace
                                            echo '<li class="active"><a>'.$i.'</a></li>';
                                    else
                                            //si el índice no corresponde con la página mostrada actualmente,
                                            //coloco el enlace para ir a esa página
                                            echo '<li><a class="paginate" data="'.$i.'">'.$i.'</a></li>';
                            }
                            if ($pageNum != $total_paginas)
                                    echo '<li><a class="paginate" data="'.($pageNum+1).'">Siguiente</a></li>';
                       echo '</ul>';
                       echo '</div>';
                    }
	
}
?>