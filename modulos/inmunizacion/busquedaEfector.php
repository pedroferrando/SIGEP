<?php
require_once ("../../config.php"); 

$query ="select a.cuie,b.nombreefector from inmunizacion.archivos as a left join facturacion.smiefectores as b using(cuie) group by a.cuie,b.nombreefector order by b.nombreefector desc";
$retorno =  sql($query);
$num_total_registros = $retorno->recordcount();

//Si hay registros
if ($num_total_registros > 0) {
?>    
<select  id="selectEfector" name="cmbEfector" onchange="xx(this.value)">
                                
                                <option value="1"> Seleccione un Efector </option>
                                <?
                                if (!$retorno==NULL){

                                    while (!$retorno->EOF) {
                                        $cuie = $retorno->fields['cuie'];
                                        $nombre_efector = $retorno->fields['nombreefector'];
                                    ?> 
                                        <option value=<?= $cuie; ?> ><?= $cuie.' - '.$nombre_efector?></option>
                                        <?
                                        $retorno->movenext();
                                    }
                                }
                                ?>
    </select>
<select id="selectFilas">
<!--    <option value="0">Filas por Pagina</option>-->
    <option value="10">10</option>
    <option value="20">20</option>
    <option value="30">30</option>
    <option value="1">1</option>
</select>
<?
}else{
    echo "No existen archivos cargados!!";
}
?>
