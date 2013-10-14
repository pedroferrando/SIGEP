<?php
require_once ("../../config.php");
require_once ("../../clases/Smiefectores.php");
?>
<select id="tipo_prestacion" name=tipo_prestacion Style="width:450px">
    <option value=-1>Seleccione</option>

    <?php
    $efector = new Smiefectores($_POST['cuie']);
    $tipos_de_nomenclador = $efector->tiposDeNomenclador();
    foreach ($tipos_de_nomenclador as $key => $value) {
        ?>
        <option  value=<?php echo "$key"; ?>
                 ><?php echo $value ?></option>
                 <?php
             }
             ?>
    ?>
</select>