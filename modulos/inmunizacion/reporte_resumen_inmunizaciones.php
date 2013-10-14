<?php
    require_once("../../config.php");
    require_once("../../clases/beneficiarios.php");
    require_once("../../clases/Efector.php");
    require_once("./clases/Vacuna.php");
    require_once("./inmunizacion_funciones.php");
    
    function numberBetween($num, $low, $high, $exc=false) {
        if($num <= $low) return false;
        if($num >= $high) return false;
        return true;
    }
    
    $efector = new Efector();
    $vacuna = new Vacuna();
    $meses = array("01"=>"Enero", "02"=>"Febrero",
                   "03"=>"Marzo", "04"=>"Abril",
                   "05"=>"Mayo", "06"=>"Junio",
                   "07"=>"Julio", "08"=>"Agosto",
                   "09"=>"Septiembre", "10"=>"Octubre",
                   "11"=>"Noviembre", "12"=>"Diciembre"
                  );
    $mes_selected = date('m');
    $anio_selected = date('Y');
    if(isset($_REQUEST[buscar]) && $_REQUEST[buscar]!=""){
        $mes_selected = $_REQUEST[mes];
        $anio_selected = $_REQUEST[anio];
        $vacunas_reporte = getVacunasReporte();
        $ids_vacunas = array_map(create_function('$arr', 'return $arr["id"];'), $vacunas_reporte);
        $sql_liq = getSQLLiquidacionesPeriodo($_REQUEST[cuie],$mes_selected,$anio_selected,$ids_vacunas);
        $res_liq = sql($sql_liq);
    }
    
    
    echo $html_header;
?>
<script type="text/javascript" src="../../lib/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../lib/funciones_generales.js"></script>

        <form name="frm_res_inmunizaciones" id="frm_res_inmunizaciones" 
              action="reporte_resumen_inmunizaciones.php" method=POST 
              onsubmit="$('#loading').show();" onkeypress="return deshabilitar_enter(event)">
            <table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
                <tr>
                    <td>
                        <h3>Resumen Mensual de Inmunizaciones</h3>
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <b>CUIE:</b>
                        <input type=text id="cuie" name="cuie" size=10 maxlength="6" value="<?php echo $_REQUEST[cuie] ?>">
                        &nbsp;
                        <select name="mes">
                            <?php foreach($meses as $key=>$value){ ?>
                                <option value="<?php echo $key; ?>" 
                                    <?php if($key==$mes_selected){ ?>
                                    selected="selected"
                                    <?php } ?>
                                >
                                    <?php echo $value; ?>
                                </option>
                            <?php } ?>
                        </select>
                        &nbsp;
                        <select name="anio">
                            <?php for($a=2010;$a<2020;$a++){ ?>
                            <option value="<?php echo $a; ?>" 
                                    <?php if($a==$anio_selected){ ?>
                                    selected="selected"
                                    <?php } ?>
                            >
                                <?php echo $a; ?>
                            </option>
                            <?php } ?>
                        </select>
                        &nbsp;
                        <?php if($_REQUEST[buscar] && $res_liq->RecordCount()>0){ $flag = 1; ?>
                            <b>Liquidaci&oacute;n</b>
                            <select name="liquidacion">
                                <?php while(!$res_liq->EOF){ ?> 
                                    <option value="<?php echo $res_liq->fields['id_liquidacion']; ?>" 
                                        <?php if($_REQUEST[liquidacion]==$res_liq->fields['id_liquidacion']){ ?>
                                        selected="selected"
                                        <?php 
                                            $liq_selected = $res_liq->fields['id_liquidacion']; $flag = 0;
                                        } ?>
                                    >
                                        <?php echo $res_liq->fields['fecha']; ?>
                                    </option>
                                <?php 
                                    if($flag==1){ 
                                        $liq_selected = $res_liq->fields['id_liquidacion'];
                                        $flag = 0;
                                    }
                                    $res_liq->MoveNext(); 
                                    } 
                                ?>
                            </select>
                            &nbsp;
                        <?php } ?>
                        
                        <input type="submit" name="buscar" value="Buscar"
                               onclick="if($('#cuie').val()!=''){
                                           $('#frm_res_inmunizaciones').submit();
                                        }else{
                                            alert('Ingrese el CUIE de un efector');
                                            return false;
                                        }
                                        "/>
                        &nbsp;
                        <?php if(isset($_REQUEST[buscar]) && $_REQUEST[buscar]!=""){ ?>
                            <a href="<?php echo encode_link("reporte_resumen_inmunizaciones_pdf.php",
                                                            array("cuie"=>$_REQUEST[cuie],
                                                                  "mes" =>$_REQUEST[mes],
                                                                  "anio"=>$_REQUEST[anio],
                                                                  "liquidacion"=>$liq_selected
                                                                 )
                                                           );?>" 
                               id="lnk_reporte_resumen_inmunizaciones" 
                               target="_blank" title="Imprimir Reporte"
                               style="vertical-align: top;">
                                <img width="20" height="20" border="0" src="/nacer/imagenes/pdf_logo.gif">
                            </a>
                        <?php } ?>
                    </td>
                </tr>
            </table>
            <p align="center" id="loading" style="display:none;">
                <img src="../../imagenes/wait.gif" alt="Aguarde unos instantes"/>
                Aguarde unos instantes
            </p>
        </form>
        <p>&nbsp;</p>
        <?php 
            if(isset($_REQUEST[buscar]) && $_REQUEST[buscar]!=""){
                $efector->FiltrarCuie(strtoupper($_REQUEST[cuie]));
                ?>
                <table border="0" width="100%">
                    <tr>
                        <td><b>Departamento:</b> <?php echo $efector->getDepartamentoNombre(); ?></td>
                        <td><b>Localidad:</b> <?php echo $efector->getLocalidadNombre(); ?></td>
                        <td><b>Fecha:</b> <?php echo date('d/m/Y'); ?></td>
                    <tr>
                        <td><b>Establecimiento:</b> <?php echo $efector->getNombreefector(); ?></td>
                        <td><b>Zona de Salud:</b> <?php echo $efector->getZonaSanitaria(); ?></td>
                        <!--<td><b>Residenc:</b></td>-->
                    </tr>
                    <!--
                    <tr>
                        <td><b>Comunidad Originaria:</b></td>
                    </tr>
                    -->
                </table>
                <br><br>
                <?php
                for($i=0;$i<count($vacunas_reporte);$i++){
                    $sql = getSQLResumenInmunizacion($_REQUEST[cuie],$vacunas_reporte[$i]['id'],$mes_selected,$anio_selected,$liq_selected);
                    $result = sql($sql) or die;
                    $fil_primera = $vacuna->getDosisVacuna($vacunas_reporte[$i]['id']);
                    list($col_primera,$rangos,$condiciones,$un_edad,$restr) = getCriteriosClasificacion($vacunas_reporte[$i]['nombre']);
                    if($result){
                        $prestaciones = array();
                        $edades_reservadas = array();
                        while(!$result->EOF){
                            for($j=0;$j<count($col_primera);$j++){
                                if(isset($rangos[$j])&&$rangos!=null){
                                    $edades_reservadas = array_merge($edades_reservadas,$rangos[$j]);
                                }
                                if($un_edad=="anio"){
                                    $edad = $result->fields['anio'];
                                }
                                if($un_edad=="mes"){
                                    $edad = $result->fields['anio'] * 12 + $result->fields['mes'];
                                }
                                if($un_edad=="dia"){
                                    $edad = $result->fields['anio'] * 12 + $result->fields['mes'] * 30.41 + $result->fields['dia'];
                                }
                                if($condiciones[$j]=="edad_dia"){
                                    $edad_dia = $result->fields['anio'] * 12 + $result->fields['mes'] * 30.41 + $result->fields['dia'];
                                    if(in_array($edad_dia,$rangos[$j])){
                                        if(!in_array($result->fields['id_prestacion_inmu'],$prestaciones)){
                                            $matriz[$j][$result->fields['id_vacuna_dosis']] += $result->fields['cnt'];
                                        }
                                    }
                                }
                                if($condiciones[$j]=="edad"){
                                    if(in_array($edad,$rangos[$j])){
                                        if(!in_array($result->fields['id_prestacion_inmu'],$prestaciones)){
                                            $matriz[$j][$result->fields['id_vacuna_dosis']] += $result->fields['cnt'];
                                            if($restr)
                                                break;
                                        }
                                    }
                                }//else{
                                    if($result->fields['caracteristica']==$condiciones[$j]){
                                        $matriz[$j][$result->fields['id_vacuna_dosis']] += $result->fields['cnt'];
                                        if($restr)
                                            break;
                                    }//else{
                                        if($condiciones[$j]=="cohorte"){
                                            if(!in_array($result->fields['id_prestacion_inmu'],$prestaciones)){
                                                if(in_array($result->fields['anio_nac'],$rangos[$j])){//if(in_array($edad,$rangos[$j])){
                                                    $matriz[$j][$result->fields['id_vacuna_dosis']] += $result->fields['cnt'];
                                                    if(!in_array($result->fields['anio'],$edades_reservadas))
                                                        array_push($edades_reservadas,$result->fields['anio']);
                                                    if($restr)
                                                        break;
                                                }
                                            }
                                        }
                                        if($condiciones[$j]=="otras_edades"){
                                            if(!in_array($result->fields['id_prestacion_inmu'],$prestaciones)){
                                                if(!in_array($edad, $edades_reservadas)){
                                                    $matriz[$j][$result->fields['id_vacuna_dosis']] += $result->fields['cnt'];
                                                    if($restr)
                                                        break;                                                   
                                                }
                                            }
                                        }
                                        if($condiciones[$j]=="grupos_riesgo" && $result->fields['id_grupo_riesgo']!=""){
                                            if(!in_array($result->fields['id_prestacion_inmu'],$prestaciones)){
                                                $matriz[$j][$result->fields['id_vacuna_dosis']] += $result->fields['cnt'];
                                                if($restr)
                                                    break;
                                            }
                                        }
                                        
                                    //}
                                //}                                
                            }
                            if(!in_array($result->fields['id_prestacion_inmu'],$prestaciones)){
                                array_push($prestaciones, $result->fields['id_prestacion_inmu']);
                            }
                            $result->MoveNext();
                        }
                        $total_gral += count($prestaciones);
                        //pintar el form
                        include('frm_info_inmunizacion.php');
                        unset($fil_primera,$col_primera,$rangos,$edades_reservadas);
                    }
                }
            }
        ?>
                <br><br><br>