<div class="contenido">
    <input type="hidden" id="cantidad" value="<?php echo $datos_practica['cantidad'] ?>">
    <div id='monitor' align='center' style="font-size: small;display: none;color:#900000 "></div>

    <div class="titulo_pagina">Seleccione los Beneficiarios del Taller</div>

    <div style='margin-left:50px;margin-top:20px'>
        <b>Buscar:&nbsp</b>
        <input type='text' name='keyword' id='keyword' value='' size=20 maxlength=50>
        <b>&nbsp;en:&nbsp;</b>
        <select name='filtro' id='filtro'>&nbsp
            <option value='dni'>DNI</option>
            <option value='apellido'>APELLIDO</option>
            <option value='clavebeneficiario'>CLAVE BENEFICIARIO</option>
        </select>
        <button id="btn_buscar">Buscar</button>
        <div id="loading" class="sprite-gral icon-spinner"></div>
    </div>


    <table class='tablagenerica' id="listado_no">
        <tr >
            <th width='25%'>Clave</th>
            <th width='25%'>Apellido</th>
            <th width='25%'>Nombres</th>
            <th width='15%'>DNI</th>
            <th width='15%'>Grupo Etario</th>
            <th width='10%'>Estado</th>
        </tr>
    </table>

    <div class="titulo_pagina">Beneficiarios Seleccionados</div>
    <table class='tablagenerica' id="listado_si">
        <tr>
            <th width='20%'>Clave</th>
            <th width='20%'>Apellido</th>
            <th width='30%'>Nombres</th>
            <th width='15%'>DNI</th>
            <th width='15%'>Grupo Etario</th>
            <th width='10%'>Estado</th>
        </tr>
        <tr class='fila' style="outline: thin solid black;background-color:#CFE0DD">
            <td width='20%'><?php echo $beneficiario_del_comprobante->getclaveBeneficiario() ?></td>
            <td width='20%'><?php echo $beneficiario_del_comprobante->getapellidoBenef() ?></td>
            <td width='30%'><?php echo $beneficiario_del_comprobante->getnombreBenef() ?></td>
            <td width='15%'><?php echo $beneficiario_del_comprobante->getnumeroDoc() ?></td>
            <td width='15%'><?php echo $beneficiario_del_comprobante->getGrupoEtareo($fecha_comprobante) ?></td>
            <?php
            if ($beneficiario_del_comprobante->getEstadoEnPadron() == 'No') {
                $imagen_estado = "<div class='sprite-gral icon-minus-alt'/>";
            } elseif ($beneficiario_del_comprobante->getEstadoEnPadron() == 'Activo') {
                $imagen_estado = "<div class='sprite-gral icon-check-alt'/>";
            } else {
                $imagen_estado = "<div class='sprite-gral icon-x-altx-alt'/>";
            }
            ?>

            <td align="center" width = '10%'><? echo $imagen_estado ?></td>
        </tr>
        <?php
        $cuenta_filas = 2;
        while (!is_null($nomina_existente) && !$nomina_existente->EOF) {
            $beneficiario_en_nomina_existente = BeneficiariosUadColeccion::buscarPorClaveBeneficiario($nomina_existente->fields['clavebeneficiario']);
            ?>
            <tr class='fila' embarazo="<?php echo $beneficiario_en_nomina_existente->getEmbarazado($fecha_comprobante) ? 'true' : 'false' ?>" grupo="<?php echo $beneficiario_en_nomina_existente->getGrupoEtareo($fecha_comprobante) ?>">
                <td width='20%'><?php echo $beneficiario_en_nomina_existente->getclaveBeneficiario() ?></td>
                <td width='20%'><?php echo $beneficiario_en_nomina_existente->getapellidoBenef() ?></td>
                <td width='30%'><?php echo $beneficiario_en_nomina_existente->getnombreBenef() ?></td>
                <td width='15%'><?php echo $beneficiario_en_nomina_existente->getnumeroDoc() ?></td>
                <td width='15%'><?php echo $beneficiario_en_nomina_existente->getGrupoEtareo() ?></td>
                <?php
                if ($beneficiario_en_nomina_existente->getEstadoEnPadron() == 'No') {
                    $imagen_estado = "<div class='sprite-gral icon-minus-alt'/>";
                } elseif ($beneficiario_en_nomina_existente->getEstadoEnPadron() == 'Activo') {
                    $imagen_estado = "<div class='sprite-gral icon-check-alt'/>";
                } else {
                    $imagen_estado = "<div class='sprite-gral icon-x-altx-alt'/>";
                }
                ?>

                <td align="center" width = '10%'><? echo $imagen_estado ?></td>
            </tr>
            <?php
            $cuenta_filas++;
            $nomina_existente->MoveNext();
        }
        ?>
    </table>    
    <p align="center"><button id="btn_guardar">Enviar</button></p>
    <?php if ($yaexisteprestacion) { ?>
        <p align="center"><button id="btn_volver" onclick="javascript:history.back()">Volver</button></p>
    <?php } ?>
    <br />
    <br />
    <br />
    <div class="referencia" id="position-fixed-bottom">
        <div>
            <span class="sprite-gral icon-check-alt"></span>
            <label>Beneficiario Activo</label>
        </div>
        <div>
            <span class="sprite-gral icon-x-altx-alt"></span>
            <label>Beneficiario Inactivo</label>
        </div>
        <div>
            <span class="sprite-gral icon-minus-alt"></span>
            <label>Beneficiario No Empadronado</label>
        </div>
    </div>
</div>