<html>
    <head>
        <link rel='icon' href='/../../favicon.ico'>
        <link REL='SHORTCUT ICON' HREF='../../favicon.ico'>
        <link rel=stylesheet type='text/css' href='../../lib/estilos.css'>
    </head>
    <body background="../../imagenes/fondo.gif" bgcolor="#B7CEC4" >
        <br /><br /><br />
        <table width="469" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
                <td id="mo" align="center" style="padding: 5px;font-size: 16px;">
                    Resultados de la recepci&oacute;n
                </td>
            </tr>  
            <tr>
                <td id="mo" align="center" style="padding: 5px;font-size: 12px;">
                    CUIE del Efector: <b><?= $factura->getCuie() ?></b> <br> </br> Factura: <b><?= $factura->getNroFactOffline() ?></b> <br></br>Periodo Facturado: <b><?= $factura->getPeriodo() ?></b>
                </td>
            </tr> 
            <tr> 
                <td align="center" style="padding: 20px;font-size: 14px;background-color: white;">
                    <table border="1">
                        <tr style="background-color: #f0effc" align="center">
                            <td width="300" ><b>Descripcion</b></td><td colspan="2"><b>Detalles</b></td>
                        </tr>
                        <tr style="background-color: #f0effc">
                            <td><b>Prestaciones Recibidas</b></td><td ><b>Aceptadas</b></td><td ><b>Rechazadas</b></td>
                        </tr>
                        <tr>
                            <td width="300"><b>Liquidadas</b></td><td ><?= $var['c_pr'] - $var['c_d'] ?></td><td ><?= $var['c_d'] ?></td>
                        </tr>
                        <tr>
                            <td width="300"><b>Total</b></td><td colspan="2"><?= $var['c_pr'] ?></td>
                        </tr>                            
                        <tr>
                            <td><b>Informados</b></td><td colspan="2" ><?= $var['c_i'] ?></td>
                        </tr>
                        <tr style="background-color: #f0effc" align="center">
                            <td><b>Trazadoras</b></td><td ><b>Aceptadas</b></td><td ><b>Rechazadas</b></td>
                        </tr>
                        <tr>
                            <td><b>Embarazadas</b></td><td><?= $var['c_e'] ?></td><td><?= $var['c_e_tmp'] ?></td>
                        </tr>
                        <tr>
                            <td><b>Partos</b></td><td><?= $var['c_p'] ?></td><td><?= $var['c_p_tmp'] ?></td>
                        </tr>
                        <tr>
                            <td><b>Ni&ntildeos</b></td><td><?= $var['c_n'] ?></td><td><?= $var['c_n_tmp'] ?></td>
                        </tr>
                        <tr>
                            <td><b>Adolecentes</b></td><td><?= $var['c_dl'] ?></td><td><?= $var['c_dl_tmp'] ?></td>
                        </tr>
                        <tr>
                            <td><b>Adultos</b></td><td><?= $var['c_a'] ?></td><td><?= $var['c_a_tmp'] ?></td>
                        </tr>
                        <tr>
                            <td><b>TAL</b></td><td><?= $var['c_t'] ?></td><td><?= $var['c_t_tmp'] ?></td>
                        </tr>
                        <tr>
                            <td width="300">
                                <b>Monto Total Facturado</b></td><td colspan="2"><b><?= number_format($monto_prefactura['total'], 2, '.', ''); ?></b>
                            </td>
                        </tr>
                    </table>
                </td>                
            </tr>
            <tr>
                <td align="center" style="padding: 5px;font-size: 12px; color:#660000">
                    Para determinar Rechazos debe correr los controles a la Factura                    
                </td>
            </tr>
            <tr>
                <td align="center">
                    <input type=button name="volver" value="Volver" onclick="document.location = 'recepcion_txt.php'" title="Volver al formulario" style="width:150px">     
                </td>
            </tr>
        </table>
        <?php
        echo fin_pagina(); // aca termino
        ?>