<?php
require_once("../../config.php");
require_once("./bibiliotecaExpediente.php");
require_once("../../lib/nrosenletras/class.nroenletraver2.php");

$estaabierto = true;

if ($_POST['nro_expediente']) {
    $nro_expediente = $_POST['nro_expediente'];
} else {
    $nro_expediente = $parametros['nro_expediente'];
    $total_liquidado = $parametros['total_liquidado'];
}

if ($_POST['editar']) {
    $nro_expediente = $_POST['nro_expediente'];
    sql("BEGIN");
    $q = "UPDATE facturacion.pagos
            SET estado='A'";
    sql($q) or die;
    sql("COMMIT");
}

$resul = buscarDatosExpediente($nro_expediente);
if ($resul->rowcount()) {
    $total_liquidado = $resul->fields['importe'];
    $administrador = $resul->fields['administrador'];
    $nro_cheque = $resul->fields['nro_cheque'];
    $nro_expediente = $resul->fields['nro_expediente'];
    $nro_orden = $resul->fields['nro_orden'];
    if($resul->fields['fecha_orden_de_cargo']!=null){
        $fecha_orden_de_cargo = date('d/m/Y',strtotime($resul->fields['fecha_orden_de_cargo']));
    }
    if($resul->fields['fecha_pago_efectivo']!=null){
        $fecha_pago_efectivo = date('d/m/Y',strtotime($resul->fields['fecha_pago_efectivo']));
    }
    $responsable_administrador = $resul->fields['responsable_administrador'];
    $estaabierto = estaAbiertoPago($nro_expediente);
    if (!$estaabierto) {
        $disabled = " readonly='readonly' ";
    } else {
        $disabled = "";
    }
}

if ($_POST['guardar']) {
    $total_liquidado = $_POST['total_liquidado'];
    $administrador = $_POST['administrador'];
    $nro_cheque = $_POST['nro_cheque'];
    $nro_expediente = $_POST['nro_expediente'];
    $nro_orden = $_POST['nro_orden'];
    $fecha_orden_de_cargo = $_POST['fecha_orden_de_cargo'];
    $responsable_administrador = $_POST['responsable_administrador'];
    $fecha_pago_efectivo = $_POST['fecha_pago_efectivo'];
    
    $estaabierto = true;
    if($fecha_pago_efectivo!=""){
        $estaabierto = false;
        $disabled = " readonly='readonly' ";
    }
    guardarPago($_REQUEST);
    $existepago = true;
    
    $total_liquidado_txt = new NroEnLetra(number_format($total_liquidado, 2, ".", ","), 2);
}


$total_liquidado_txt = new NroEnLetra(number_format($total_liquidado, 2, ".", ","), 2);
?>
<head>
    <script src='../../lib/jquery.min.js' type='text/javascript'></script>
    <style media="screen" type="text/css">
        #form_cheque input{
            margin-top: 8px;
        }
        
        #form_cheque div, #form_cheque p {
            font-size: 12px;
            margin: 0 0 5px;
        }
        
        #form_cheque button{
            padding: 5px;
        }

        body{
            font-family: sans-serif;
            background-image: url('../../imagenes/fondo.gif');
        }
    </style>
    <script type="text/javascript">
                
        function guardar_orden(){
            var fecha= document.getElementById('fecha_orden_de_cargo').value;
            if(fecha.replace(/^\s+|\s+$/g,"")==""){ 
                alert('Debe completar el campo Fecha de Orden');
                //fecha_entra.focus();
                return false;
            }
            document.forms['form_cheque'].submit();
            return true;
        }
        
        function editar_orden(){
            document.forms['form_cheque'].submit();
            return true;
        }
        function imprimir_orden(link){
            window.open(link);
            return false;
        }
        
        //Validar Fechas
        function esFechaValida(fecha){
            var numDias;
            if ((fecha != undefined)&&( fecha.value != "") ){
                if (!/^\d{2}\/\d{2}\/\d{4}$/.test(fecha.value)){
                    alert("formato de fecha no valido (dd/mm/aaaa)");
                    fecha.focus();
                    fecha.value = ""
                    return false;
                }
                var dia  =  parseInt(fecha.value.substring(0,2),10);
                var mes  =  parseInt(fecha.value.substring(3,5),10);
                var anio =  parseInt(fecha.value.substring(6),10);
 
                switch(mes){
                    case 1:
                    case 3:
                    case 5:
                    case 7:
                    case 8:
                    case 10:
                    case 12:
                        numDias=31;
                        break;
                    case 4: case 6: case 9: case 11:
                                    numDias=30;
                                    break;
                                case 2:
                                    if (comprobarSiBisisesto(anio)){ numDias=29 }else{ numDias=28};
                                    break;
                                default:
                                    alert("Fecha introducida erronea");
                                    fecha.focus();
                                    fecha.value = ""
                                    return false;
                            }
 
                            if (dia>numDias || dia==0){
                                alert("Fecha introducida erronea");
                                fecha.focus();
                                fecha.value = ""
                                return false;
                            }
                            return true;
                        }
                    }
 
                    function comprobarSiBisisesto(anio){
                        if ( ( anio % 100 != 0) && ((anio % 4 == 0) || (anio % 400 == 0))) {
                            return true;
                        }
                        else {
                            return false;
                        }
                    }
                    /**********************************************************/
                    var patron = new Array(2,2,4)
                    var patron2 = new Array(5,16)
                    function mascara(d,sep,pat,nums){
                        if(d.valant != d.value){
                            val = d.value
                            largo = val.length
                            val = val.split(sep)
                            val2 = ''
                            for(r=0;r<val.length;r++){
                                val2 += val[r]
                            }
                            if(nums){
                                for(z=0;z<val2.length;z++){
                                    if(isNaN(val2.charAt(z))){
                                        letra = new RegExp(val2.charAt(z),"g")
                                        val2 = val2.replace(letra,"")
                                    }
                                }
                            }
                            val = ''
                            val3 = new Array()
                            for(s=0; s<pat.length; s++){
                                val3[s] = val2.substring(0,pat[s])
                                val2 = val2.substr(pat[s])
                            }
                            for(q=0;q<val3.length; q++){
                                if(q ==0){
                                    val = val3[q]

                                }
                                else{
                                    if(val3[q] != ""){
                                        val += sep + val3[q]
                                    }
                                }
                            }
                            d.value = val
                            d.valant = val
                        }
                    }
    </script>
</head>

<div align="center" >
    <div align="center" style="width: 880px">
        <h2>Del Total del Expediente Nº <?= $nro_expediente ?></h2>
        <form id="form_cheque" name="form_cheque" action="pago_orden_de_cargo.php" method=POST>
            <input type="hidden" name="nro_expediente" value="<?= $nro_expediente ?>"/>
            <input name="total_liquidado" type="hidden" value="<?= $total_liquidado ?>"/>
            <div align="left" style="background-color: #DDDDDD; border:#000 thin solid; padding: 10px 10px 12px;">
                <p>
                    IMPORTE TRANSFERENCIA: 
                    <input type="text" readonly="readonly" style="width:100px" autocomplete="off" value="<?= number_format($total_liquidado, 2, ",", ".") ?>"/>
                </p>
                <p>
                    SON PESOS: 
                    <input type="text" readonly="readonly" style="width:740px;" autocomplete="off" value="<?= $total_liquidado_txt->getLetra() ?>"/>
                </p>
                <p>A: 
                   <input type="text" name="administrador" style="width:350px" autocomplete="off" value="<?= strtoupper($administrador) ?>" <?= $disabled ?>/>
                </p>
                <p>
                    Nº CHEQUE: <input type="text" name="nro_cheque" autocomplete="off" value="<?= $nro_cheque ?>" <?= $disabled ?>/>
                </p>
                <p>
                    <div>
                        ORDEN DE CARGO Nº: 
                        <input type="text" name="nro_orden" autocomplete="off" value="<?= $nro_orden ?>" <?= $disabled ?>/>
                        &nbsp;
                        FECHA ORDEN DE CARGO: 
                        <input id="fecha_orden_de_cargo" name="fecha_orden_de_cargo"  
                               size="15" maxlength="10" type="text"
                               onKeyUp="mascara(this,'/',patron,true);"
                               onblur="esFechaValida(this);" autocomplete="off"
                               value="<?php echo $fecha_orden_de_cargo; ?>" 
                               <?= $disabled ?>/>
                        <? echo link_calendario('fecha_orden_de_cargo'); ?>
                    </div>
                </p>
                <p>
                    <div>
                        RESPONSABLE ADMINISTRADOR: 
                        <input type="text" name="responsable_administrador"
                               autocomplete="off" style="width:270px"
                               value="<?= $responsable_administrador ?>"<?= $disabled ?>/>
                        &nbsp;
                        FECHA DE PAGO EFECTIVO:
                        <input id="fecha_pago_efectivo" name="fecha_pago_efectivo"  
                               size="15" maxlength="10" type="text"
                               onKeyUp="mascara(this,'/',patron,true);"
                               onblur="esFechaValida(this);" autocomplete="off"
                               value="<?php echo $fecha_pago_efectivo; ?>"
                               <?= $disabled ?>/>
                        <?php echo link_calendario('fecha_pago_efectivo'); ?>
                    </div>
                </p>
                <p align="right">
                    <? if (!$estaabierto) { ?>
                        <? $link = encode_link("pago_pdf.php", array("nro_expediente" => $nro_expediente)); ?>
                        <!--
                        <button name="imprimir" value="imprimir" title='Imprimir Orden de Pago' onclick="return imprimir_orden('<?= $link ?>')">
                            <img src='../../imagenes/pdf_logo.gif' height='18' border='0'/>
                        </button>
                        -->
                        <button name="editar" value="editar" title='Editar Orden de Pago' onclick="return editar_orden();">
                            <img src='../../imagenes/menu/edit.gif' border='0'/>
                        </button>
                    <? } else { ?>
                        <button name="guardar" value="guardar" title='Guardar Orden de Pago' onclick="return guardar_orden();">
                            <img src='../../imagenes/Menu/iconSave.gif' border='0'/>
                        </button>
                        <? } ?>
                </p>
            </div>
        </form>
    </div>
</div>
<?php echo fin_pagina(); // aca termino ?>