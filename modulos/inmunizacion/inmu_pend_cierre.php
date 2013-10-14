<?php
require_once ("../../config.php");
require_once ("../../clases/Smiefectores.php");
require_once ("../../lib/bibliotecaTraeme.php");
require_once ("./inmunizacion_funciones.php");

extract($_POST, EXTR_SKIP);
if ($parametros)
    extract($parametros, EXTR_OVERWRITE);

echo $html_header;

if($_REQUEST[buscar] || $_REQUEST[buscar2]){
    $cuie = strtoupper($_REQUEST[cuie]);
    $filtro_selected = $_REQUEST[filtro_periodo];
    $anio_selected = $_REQUEST[anio];
    $periodo_selected = $_REQUEST[periodo];
    if(isset($cuie) && $cuie!=""){
        $efector = new Smiefectores($cuie);
        $periodos_result = sql(getSQLPeriodosPorCerrar($cuie));
        $arr_periodos = array();
        if($periodos_result->RecordCount()>0){
            while(!$periodos_result->EOF){
                array_push($arr_periodos, $periodos_result->fields['periodo']);
                $periodos_result->MoveNext(); 
            }
        }
    }
}

?>
<link rel='stylesheet' href='../../lib/jquery/ui/jquery-ui.css'/>
<script src='../../lib/jquery.min.js' type='text/javascript'></script>
<script src='../../lib/jquery/jquery-ui.js' type='text/javascript'></script>
<script src='../../lib/jquery/ui/jquery.ui.datepicker-es.js' type='text/javascript'></script>
<script src='./inmunizacion_funciones.js' type='text/javascript'></script>
<script>
    $(document).ready(function() {
        $('#loading').hide();
        $('#periodosh').accordion({heightStyle: "content"});
        $('#periodosh').show();
    });
</script>

<form name="frm_lst_prest_inmu" id="frm_lst_prest_inmu" 
      action="inmu_pend_cierre.php" method=POST 
      onsubmit="$('#loading').show();" style="text-align: center;">
    <fieldset style="width: 98%;border: solid grey thin;border-radius:15px;margin: 0 auto 0 auto;padding-top: 5px;margin-top: 10px">
        <legend><b><?= "Inmunizaciones Pendientes de Cierre" ?></b></legend>
        <table  style="width: 90%; margin: 0 auto 0 auto;">
            <tr>
                <td align="right" width="30%">
                    <b>Efector:</b>
                    <input type=text id="cuie" name="cuie"
                                size=10 maxlength="6" 
                                value="<?php echo $cuie; ?>"/>
                        
                </td>
                <td align="left" id="periodos_x_cerrar" width="40%">
                    <?php if(isset($periodos_result) && $periodos_result->RecordCount()>0){ ?>
                            <b>Filtro:</b>
                            <select name="filtro_periodo" onchange="mostrar_filtros_cierre_inmu(this);">
                                <option value=""></option>
                                <option value="anio" <?php if($filtro_selected=="anio"){ ?> selected="selected" <?php } ?>>
                                    Año
                                </option>
                                <option value="periodo" <?php if($filtro_selected=="periodo"){ ?> selected="selected" <?php } ?>>
                                    Periodo
                                </option>
                            </select>
                            <select name="periodo" 
                                <?php if($filtro_selected!="periodo"){ ?> 
                                    style="display:none;" 
                                <?php } ?>>
                                <option value=""></option>
                                <?php for($i=0;$i<count($arr_periodos);$i++){ ?>
                                        <option value="<?php echo $arr_periodos[$i]; ?>"
                                                <?php if($arr_periodos[$i]==$periodo_selected){ ?>
                                                selected="selected"
                                                <?php } ?>
                                        >
                                            <?php echo $arr_periodos[$i]; ?>
                                        </option>
                                <?php } ?>
                            </select>
                            <select name="anio" 
                                <?php if($filtro_selected!="anio"){ ?> 
                                    style="display:none;" 
                                <?php } ?>>
                                <option value=""></option>
                                <?php for($i=0;$i<count($arr_periodos);$i++){ ?>
                                        <?php
                                            $anio = substr($arr_periodos[$i], 0,4);
                                            if($anio!=$anio_prev){
                                        ?>
                                            <option value="<?php echo $anio; ?>"
                                                    <?php if($anio==$anio_selected){ ?>
                                                    selected="selected"
                                                    <?php } ?>
                                            >
                                                <?php echo $anio; ?>
                                            </option>
                                        <?php
                                            }
                                        $anio_prev = $anio;
                                        ?>
                                <?php } ?>
                            </select>
                    <?php } ?>
                    <?php if(!$_REQUEST[buscar] && !$_REQUEST[buscar2]){ ?>
                        <input type="submit" name="buscar" value="Buscar">
                    <?php }else{ ?>
                        <input type="submit" name="buscar2" value="Buscar">
                    <?php } ?>        
                </td>
                    <!--<input id="cuieBtn" type="button" value="Buscar">-->
                    
            </tr>
            <tr>
                <td align="center" colspan="3">
                    <br/>
                    <div id="loading" align="center" 
                         <?php if(!$_REQUEST[buscar] && !$_REQUEST[buscar2]){ ?>
                         style="display:none;"
                         <?php } ?>
                    >
                        <img src="../../imagenes/wait.gif" alt="Aguarde unos instantes"/>
                        Aguarde unos instantes
                    </div>
                </td>
            </tr>
            <?php if($_REQUEST[buscar] || $_REQUEST[buscar2]){ ?>
                <tr>
                    <td><b><?php echo $efector->getNombreefector(); ?></b></td>
                </tr>
            <?php } ?>
            <tr>
                <td colspan="3">
                    <?php if($_REQUEST[buscar2]){ ?>
                    <p align="right">
                        <button name="btn_cerrar" type="button" onclick="cerrar_inmunizaciones();">Cerrar</button>
                    </p>
                    <?php } ?>
                    <div id="periodosh" style="display: none; padding-top: 10px;">
                        
                        <?php 
                        if($_REQUEST[buscar2] && isset($cuie)) {
                            if($periodos_result->RecordCount()>0) {
                                $contadordepracticas = 1;
                                for($i=0;$i<count($arr_periodos);$i++){
                                    $unperiodo = $arr_periodos[$i];
                                    $anio = substr($unperiodo, 0, 4);
                                    $mes = substr($unperiodo, 5, 2);
                                    $fecha_desde = ereg_replace('/', '-', $unperiodo) . '-01';
                                    $fecha_hasta = ereg_replace('/', '-', $unperiodo) . '-' . ultimoDia($mes, $anio);
                                    $show_tbl = true;
                                    if($_REQUEST[filtro_periodo]=="anio" && $_REQUEST[anio]!="" && $_REQUEST[anio]!=$anio){
                                        $show_tbl = false;
                                    }
                                    if($_REQUEST[filtro_periodo]=="periodo" && $_REQUEST[periodo]!="" && $_REQUEST[periodo]!=$unperiodo){
                                        $show_tbl = false;
                                    }
                                    if($show_tbl){    
                                        $sql_inmu = getSQLInmunizacionesPorCerrar($cuie,$fecha_desde,$fecha_hasta);
                                        $res_inmu = sql($sql_inmu);
                                        include('inmu_pend_cierre_tbl.php');
                                        $contadordepracticas = 1;
                                    }
                                }
                            } else {
                                ?>
                                <h3>No hay inmunizaciones pendientes de cierre</h3>
                                <div></div>
                            <?php
                            }
                        }
                        ?>
                    </div>
                </td>
            </tr>
        </table>
    </fieldset>
    <p>&nbsp;</p>
</form>