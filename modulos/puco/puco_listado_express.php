<?php
/*
  Author: gaby

  modificada por
  $Author: gaby $
  $Revision: 1.0 $
  $Date: 2006/07/20 15:22:40 $
 */
require_once("../../config.php");

//require_once("./estamosTrabajando.php");

die();


echo $html_header;
?>

<form name=form1 action="puco_listado_express.php" method=POST>
    <table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
        <tr>      
            <td align=center><font size=+1><b>CONSULTA AL PUCO</b></font></td> 
        </tr>    
        <tr>      
            <td align=center> NRO DE DOCUMENTO:
                <input type="text" size="30" value="" name="documento" >
                &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>

            </td>
        </tr>
    </table>

    <table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?= $bgcolor3 ?>' align=center>

        <tr> 
            <td id=mo>TIPO DOC</td>  
            <td id=mo>DOCUMENTO</td>
            <td id=mo>CLAVE BENEF</td>
            <td Id=mo>NOMBRE</td>
            <td Id=mo>FEC NAC</td>
            <td Id=mo>EMB</td>
            <td id=mo>OBRA SOCIAL</td>  
            <td id=mo>MOTIVO BAJA</td> 
            <td id=mo>LUGAR ATENC</td> 
        </tr>
        <?
        if ($_POST['buscar']) {
            $documento = $_POST['documento'];
            if (strlen($documento) >= 7) {
                $sql_tmp = "SELECT p.aficlasedoc,p.tipodoc,p.nrodoc,substring(p.afifechanac::text from 1 for 10) as fechanac,p.nroafil,p.afiapellido,p.afinombre,p.nombreos,p.motivobaja,p.embarazo_actual,p.cuielugaratencionhabitual
		      FROM puco.pucosumar p
		      WHERE (nrodoc ='$documento')";
                $query = sql($sql_tmp, "ERROR al realizar la consulta") or fin_pagina();

                if ($query->recordCount() == 0) {
                    ?> 
                    <tr>   
                        <td align="center" colspan="3">NO SE ENCONTRARON DATOS</td> 						      
                    </tr> 
        <?
        } else {
            while (!$query->EOF) {
                ?>
                        <tr <?= atrib_tr() ?>>   
                            <td align="center"><?= $query->fields['tipodoc'] ?></td> 
                            <td align="center"><?= $query->fields['nrodoc'] ?></td>
                            <td align="center"><?= $query->fields['nroafil'] ?></td>
                            <td align="center"><?= utf8_encode($query->fields['afiapellido']) . " " . $query->fields['afinombre'] ?></td>
                            <td align="center"><?= $query->fields['fechanac'] ?></td>
                            <td align="center"><?= $query->fields['embarazo_actual'] ?></td>
                            <td ><?= $query->fields['nombreos'] ?></td> 
                            <td ><?= $query->fields['motivobaja'] ?></td> 
                            <td align="center"><?= $query->fields['cuielugaratencionhabitual'] ?></td>
                        </tr>    
                        <?
                        $query->MoveNext();
                    }//FIN WHILE
                }//fin else  
            } else {
                ?>
                <tr>  
                    <td align="center" colspan="3">DEBE INGRESAR A MENOS 7 NUMEROS</td> 						      
                </tr>    	 	
    <?
    }
}//fin IF
?>

    </table>
</form>
</body>
</html>

<?
echo fin_pagina(); // aca termino ?>