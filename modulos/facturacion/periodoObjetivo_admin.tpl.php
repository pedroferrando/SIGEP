<div style="margin: 20px;float: left;" width="95%">

    <font style='float: left;font-size: small;margin-left: 35px'><b>Periodo: </b></font><input style='width: 80px' type="text" id='periodo' name='periodo' value=''/>
    <br>
    <br>
    <font style='float: left;font-size: small;margin-left: 35px'><b>Desde: </b></font><input type="text" style='float: left;width: 100px;margin-left: 10px' id='desde' name='desde' value=''/>
    <font style='float: left;font-size: small;margin-left: 20px'><b>Hasta: </b></font><input type="text" style='float: left;width: 100px;margin-left: 5px' id='hasta' name='hasta' value=''/>
    <br>
    <br>
    <font style='float: left;font-size: small'><b>Observacion: </b></font>
    <textarea cols="50" rows="2" type="text" style='float: left;' id='observacion' name='observacion' value=''></textarea>
    <br>
    <input type="button" value='Agregar'>
    <input type="button" value='Modificar'>
    <input type="button" value='Cancelar'>

    <table id="practicas" width="100%" cellspacing="2" cellpadding="2" border="0" bgcolor="#B7CEC4" align="center" style="float: left;margin-top: 10px;margin-left:100px">
        <tr>
            <td id="mo" style="border-bottom: solid thin black;border-left: solid thin black;width: 100px">Periodo</td>
            <td id="mo" style="border-bottom: solid thin black;border-left: solid thin black;width: 100px">Desde</td>
            <td id="mo" style="border-bottom: solid thin black;border-left: solid thin black;width: 100px">Hasta</td>
            <td id="mo" style="border-bottom: solid thin black;border-left: solid thin black;width: 200px">Observacion</td>
        </tr>

        <?php
        if ($listado_periodos) {
            foreach ($listado_periodos as $unperiodo) {

                if ($colordefondo == '#CFE8DD') {
                    $colordefondo = '#AFE8DD';
                } else {
                    $colordefondo = '#CFE8DD';
                }
                ?>
                <tr onclick="toggleDebito(this.id);" id="<?php echo $unperiodo->getIdPeriodoObjetivo() ?>" style="background-color: <?php echo $colordefondo ?>">
                    <td style="width: 100px;border-bottom: solid thin black;border-left: solid thin black"><?php echo $unperiodo->getPeriodo() ?></td>
                    <td style="width: 150px;border-bottom: solid thin black;border-left: solid thin black"><?php echo $unperiodo->getDesde() ?></td>
                    <td style="width: 150px;border-bottom: solid thin black;border-left: solid thin black"><?php echo $unperiodo->getHasta() ?></td>
                    <td style="width: 300px;border-bottom: solid thin black;border-left: solid thin black"><?php echo $unperiodo->getObservaciones() ?></td>
                </tr>
                <?php
            }
        }
        ?>
    </table>
</div>