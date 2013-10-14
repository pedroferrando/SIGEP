<?php
    require_once ("../../config.php");
    require_once ("./remediar_seguimiento_funciones.php");
    
    
    #   MEDICOS
    #   Consulta y resultado de los medicos en la db (Unicamente medicos)
    $medicos_sql = "select trim(upper(apellido_medico))as apellido,trim(upper(nombre_medico))as nombre,trim(dni_medico)as dni,id_medico
                    from planillas.medicos
                    order by apellido_medico";
    $medicos_result = sql($medicos_sql) or die();
    ###
    
    #   Usuario de carga
    $usuarioCargaId = $_ses_user['id'];
    ###
    
    #   Interconsultas
    $interconsultas_sql ="select idinterconsulta, valor, upper(especialidad) as especialidad from remediar.interconsultas";
    $interconsultas_result = sql($interconsultas_sql);
    ###
    
    #   EFECTORES
    #   Consulta y resultado de los efectores en la db (Unicamente efectores)
    $efectores_sql = "select rc.codremediar, ef.nombreefector
        from general.relacioncodigos rc
        inner join facturacion.smiefectores ef on ef.cuie = rc.cuie
        where rc.codremediar is not null
        and rc.codremediar <> ''";
    
    $efectores_result = sql($efectores_sql) or die();
    ###
    
   
    #   Nueva instancia del beneficiario
    $beneficiario = new BeneficiarioSeguimiento($parametros["clave_beneficiario"]);
    
    #   Obtenemos la sentencia SQL para consultar los datos del beneficiario para 
    #   el seguimiento (consultar el metodo sqlObtenerBeneficiario() de la clase
    #   BeneficiarioSeguimiento) y pasamos el resultado de la consulta al metodo
    #   construirBeneficiario para que construya el resto del esquema del beneficiario
    #   con los datos afiliatorios.
    $beneficiario->construirBeneficiario(sql($beneficiario->sqlObtenerBeneficiario()));

    #   Obtiene en primera instancia los seguimientos del beneficiario (Si es que lo tiene)
    #   El metodo sqlObtenerRiesgo, sin parametros, retorna una sentencia sql,
    #   para buscar el riesgo del ultimo seguimiento.
    $rcvg = sql($beneficiario->sqlObtenerRiesgo());
    
    #   Verifica en primera instancia si existe un seguimiento del beneficiario.
    if ($beneficiario->isRegistroValido($rcvg)) {
        $beneficiario->setRcvgAnterior($rcvg->fields['rcvg']);
    }
    
    #   Si no los tiene, el metodo sqlObtenerRiesgo, con el parametro 0, retorna
    #   una sentencia sql, para buscar el riesgo en clasificacion.
    else{
        $rcvg = sql($beneficiario->sqlObtenerRiesgo(0));
        $beneficiario->setRcvgAnterior($rcvg->fields['rcvg']);
        
    }
    
    #   Verifica si la llamada al formulario se realizo por submit.
   if (isNuevo($_POST)){
        #   BENEFICIARIOS  
       
        #   Construye el esquema del beneficiario con los datos del seguimiento
        $beneficiario->construirSeguimiento($_POST);
        
        #   Obtiene el numero de seguimiento
        $beneficiario_resultAux = sql($beneficiario->sqlNroSeguimiento());
        $beneficiario->setNroSeguimiento($beneficiario_resultAux->fields['num_seguimiento'] + 1);
    
        #   Retorno de la id del ultimo registro persistido
        #   Ver sql del metodo sqlSeguimientoNuevo() de la clase BeneficiarioSeguimiento
        $ret = sql($beneficiario->sqlSeguimientoNuevo()) or die();
        
        #   Mensaje que se muestra en la pantalla redireccionada bajo el campo "mensaje"
        $mensaje = '<div align="center" id="mo"><h2>Formulario N. '.$ret->fields["idseguimiento"].' guardado</h2></div>';
        
        #   Genera el enlace para redireccionar con parametros
        $redir = encode_link("./clasificacion_listado.php", array("mensaje" => $mensaje));
        
        #   Finalizada la persistencia en la DB, se redirecciona al listado de clasificacion
        ?>
        <script type="text/javascript">
            //location.href="<?=$redir?>";
        </script>
        <?php
        ###
   }
    
   
   echo $html_header;
   #   Verifica si la llamada al formulario se realizo por submit.
   if (isNuevo($_POST)){

        echo '<div align="center" id="mo"><h2>Formulario N. <b>'.$ret->fields["idseguimiento"].'</b> guardado</h2></div>';
        echo '<div align="center" id="mo"><h2>Puede cargar un nuevo seguimiento del mismo beneficiario.</h2></div>';
        #echo $beneficiario->sqlRegistroNuevo();
        ###
   }
   
    
?>

<script type="text/javascript" src="../../lib/jquery.min.js"> </script>

<script type="text/javascript">
//Validar Fechas
function esFechaValida(fecha){
    if (fecha != undefined && fecha.value != "" ){
        if (!/^\d{2}\/\d{2}\/\d{4}$/.test(fecha.value)){
            alert("formato de fecha no valido (dd/mm/aaaa)");
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
            return false;
    }
 
        if (dia>numDias || dia==0){
            alert("Fecha introducida erronea");
            return false;
        }
        return true;
    }
}
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
    
function validarFormulario(){
    TaSist =  document.formulario.taSist;
    TaDiast = document.formulario.taDiast;
    FechaSeguimiento = document.formulario.fecha_seguimiento;
    Medico = $("#medico option:selected").val()
    Colesterol = document.formulario.colesterol;
    Glucemia = document.formulario.glucemia;
    Peso = document.formulario.peso;
    Talla = document.formulario.talla;
    Imc = document.formulario.imc;
    Hba1c = document.formulario.hba1c;
    Microalbuminuria = document.formulario.microalbuminuria;
    Hdl = document.formulario.hdl;
    Ldl = document.formulario.ldl;
    Tags = document.formulario.tags;
    Creatininemia = document.formulario.creatininemia;
    Efector = $("#efector option:selected").val();
    Dmt2 = document.formulario.dmt2;
    
    
    
    // DMT2
    if (Dmt2.checked) {
        if (Glucemia.value =='') {
                alert("Paciente con diabetes, necesita cargar Glucemia");
                return(false);
            }
        }
    
    // TA SISTONICA
    if (TaSist.value < 80){
        TaSist.style.backgroundColor='#FFAEAE';
        alert("Los valores de TA Diastolica estan fuera de rango, entre 80 y 260");
        return(false);
    }
    
    // TA DIASTONICA
    if (TaDiast.value < 50){
        TaDiast.style.backgroundColor='#FFAEAE';
        alert("Los valores de TA Diastolica estan fuera de rango, entre 50 y 160");
        return(false);
    }
    
    // Fecha de seguimiento
    if (FechaSeguimiento.value == '') {
               alert("Debe seleccionar una fecha valida.");
               return(false);
   }
   
   
   if (Medico == "NOTOSH")  {
       alert("Debe Seleccionar un medico");
       return(false);
   }
   
   if (Efector == "NOTOSH")  {
       alert("Debe Seleccionar un efector");
       return(false);
   }

    if( $("#formulario input[name='riesgo_actual']:radio").is(':checked')) {  
              
    } else {  
            alert("Debe Seleccionar un nivel de riesgo");  
            return(false);
    }
    
    // COLESTEROL
    //if (Colesterol.value < 130 || Colesterol.value > 400){ Quitado segun requerimientos de Valeria Acosta
    if(Colesterol.value == ''){
        
    }else{
        if (Colesterol.value < 130){
            Colesterol.style.backgroundColor='#FFAEAE';
            alert("Los valores de colesterol estan fuera de rango, entre 130 y 400");
            return(false);
        }
    } 
    
    // GLUCEMIA
    //if (Glucemia.value < 65 || Glucemia.value > 1500){ Quitado segun requerimientos de Valeria Acosta
    if(Glucemia.value ==''){
        
    }else{
        if (Glucemia.value < 65){
            Glucemia.style.backgroundColor='#FFAEAE';
            alert("Los valores de Glucemia estan fuera de rango, entre 65 y 1500");
            return(false);
        }
    }
    
    // PESO
    //if (Peso.value < 15 || Peso.value > 300){ Quitado segun requerimientos de Valeria Acosta
    if(Peso.value ==''){
       
    }else{
        if (Peso.value < 15){
            Peso.style.backgroundColor='#FFAEAE';
            alert("Los valores de Peso estan fuera de rango, entre 15 y 300");
            return(false);
        }
    }
    
    // TALLA
    //if (Talla.value < 90 || Talla.value > 230){ Quitado segun requeimientos de Valeria Acosta
    if(Talla.value ==''){
        
    }else{
            if (Talla.value < 90){
            Talla.style.backgroundColor='#FFAEAE';
            alert("Los valores de Talla estan fuera de rango, entre 90 y 230");
            return(false);
        }  
    }
    
    // IMC
    //if (Imc.value < 15 || Imc.value > 60){ Quitado segun requerimiento de Valeria Acosta
    if(Imc.value ==''){
        
    }else{
        if (Imc.value < 15){
            Imc.style.backgroundColor='#FFAEAE';
            alert("Los valores de IMC estan fuera de rango, entre 15 y 60");
            return(false);
        }
    }
    
    // HBA1C
    //if (parseFloat(Hba1c.value.replace(',','.')) < 5.5 || parseFloat(Hba1c.value.replace(',','.')) > 16){ Quitado segun requerimiento de Valeria Acosta
    if(Hba1c.value ==''){
       
    }else{
        if (parseFloat(Hba1c.value.replace(',','.')) < 5.5){
            Hba1c.style.backgroundColor='#FFAEAE';
            alert("Los valores de Hba1c estan fuera de rango, entre 5.5 y 16");
            return(false);
        }
    }
    
    
    // Microalbuminuria
    //if (Microalbuminuria.value < 0 || Microalbuminuria.value > 500 ){ Quitado segun requerimiento de Valeria Acosta
    if(Microalbuminuria.value ==''){
       
    }else{
        if (Microalbuminuria.value < 0){
            Microalbuminuria.style.backgroundColor='#FFAEAE';
            alert("Los valores de Microalbuminuria estan fuera de rango, entre 0 y 500");
            return(false);
        }
    }
    
    //if (Hdl.value < 25 || Hdl.value > 90){ Quitado segun requerimiento de Valeria Acosta
    if(Hdl.value ==''){
        
    }else{
        if (Hdl.value < 25){
            Hdl.style.backgroundColor='#FFAEAE';
            alert("Los valores de Hdl estan fuera de rango, entre 25 y 90");
            return(false);
        }
    }
    
    //if (Ldl.value < 40 || Ldl.value > 300){ Quitado segun requerimiento de Valeria Acosta
    if(Ldl.value ==''){
        
    }else{
        if (Ldl.value < 40){
            Ldl.style.backgroundColor='#FFAEAE';
            alert("Los valores de LDL estan fuera de rango, entre 40 y 300");
            return(false);
        }
    }
    
    //if (Tags.value < 500 || Tags.value > 1500){ Quitado segun requerimiento de Valeria Acosta
    if(Tags.value == ''){
        
    }else{
        if (Tags.value < 500){
            Tags.style.backgroundColor='#FFAEAE';
            alert("Los valores de Tags estan fuera de rango, entre 500 y 1500");
        return(false);}
    }
    
    // CREATININEMIA
    //if (parseFloat(Creatininemia.value.replace(',','.')) < 0.4 || parseFloat(Creatininemia.value.replace(',','.')) > 8){ Quitado segun requerimiento de Valeria Acosta
    if(Creatininemia.value ==''){
       
    }else{
        if (parseFloat(Creatininemia.value.replace(',','.')) < 0.4){
            Creatininemia.style.backgroundColor='#FFAEAE';
            alert("Los valores de Creatininemia estan fuera de rango, entre 5.5 y 16");
            return(false);
        }
    }
    
}
</script>


<form action="remediar_seguimiento.php" method="post" name="formulario" id="formulario">
    
    <br />
    
    <fieldset>
        <legend>Datos del beneficiario</legend>
        <br />
        
    <table width=100% align="center" class="bordes">
        <tr id="mo" style="text-align: left;">
            <td>Apellido: <?=$beneficiario->getApellido()?></td>
            <td>Nombre: <?=$beneficiario->getNombre()?></td>
            <td>Edad: <?=$beneficiario->getEdad()?> a&ntilde;os</td>
        </tr>
    </table>
        
        <br />
        
    <table width=100% align="center" class="bordes">
        <tr id="mo" style="text-align: left;">
            <td>Fecha de nacimiento: <?=$beneficiario->getFechaNacimiento()?></td>
            <td>Efector: 
                <select name="efector" id="efector">
                    <option value="NOTOSH">Seleccione un efector</option>
                    <?php while(!$efectores_result->EOF){?>
                        <option value="<?=$efectores_result->fields['codremediar']?>"><?=($efectores_result->fields['codremediar']."-".$efectores_result->fields['nombreefector'])?></option>
                    <?php $efectores_result->MoveNext();}?>
                </select></td>
            <td></td>
        </tr>

        <tr id="mo" style="text-align: left;">
            <td>Tipo Doc: <?=$beneficiario->getTipoDoc()?></td>
            <td>Nro Doc: <?=$beneficiario->getNroDoc()?></td>
            <td>Sexo: <?=$beneficiario->getSexo()?></td>
        </tr>
    </table>

        
    <table width=100% align="center" class="bordes">
        <tr id="mo" style="text-align: left;">
            <td>Tel: <?=$beneficiario->getTelefono()?></td>
            <td>Provincia: <?=$beneficiario->getDomicilioProvincia()?></td>
            <td>Dpto: <?=$beneficiario->getDomicilioDepto()?></td>
            <td>Municipio: <?=$beneficiario->getDomicilioMunicipio()?></td>

        </tr>

        <tr id="mo" style="text-align: left;">
            <td>Localidad: <?=$beneficiario->getDomicilioLocalidad()?></td>
            <td>Calle:  <?=$beneficiario->getDomicilioCalle()?></td>
            <td>Nro:  <?=$beneficiario->getDomicilioNro()?></td>
            <td>Piso: <?=$beneficiario->getDomicilioPiso()?></td>
            <td>Barrio: <?=$beneficiario->getDomicilioBarrio()?></td>
            <td>Mza: <?=$beneficiario->getDomicilioManzana()?></td>

        </tr>


    </table>

</fieldset>
        
    
    <br />
    
    <fieldset>
        <legend>Datos del seguimiento Cuatrimestral</legend>

        <table width=100% align="center" class="bordes"> 
            <tr id="mo" style="text-align: left;">
                <td>DMT 2 </td>
                <td><input type="checkbox" name="dmt2" id="" /></td>
                <td>HTA </td>
                <td><input type="checkbox" name="hta" id="" /></td>
                <td>TA Sist.</td>
                <td><input type="text" name="taSist" id="" /></td>
                <td>TA Diast.</td>
                <td> <input type="text" name="taDiast" id="" /></td>
                <td>Tabaquismo</td>
                <td><input type="checkbox" name="tabaquismo" id="" /></td>

            </tr>

            <tr id="mo" style="text-align: left;">
                
                
                <td>Col. Tot.</td>
                <td><input type="text" name="colesterol" id="" /></td>
                <td>Glucemia</td>
                <td><input type="text" name="glucemia" id="" /></td>
                <td>Peso</td>
                <td><input type="text" name="peso" id="" /></td>
                
                <td>Talla</td>
                <td><input type="text" name="talla" id="" /></td>
                <td>IMC</td>
                <td><input type="text" name="imc" id="" /></td>
                
                
            </tr>


            <tr id="mo" style="text-align: left;">
                
                <td>HbA1c</td>
                <td><input type="number" name="hba1c" id="" /></td>
                <td>ECG</td>
                <td><input type="checkbox" name="ecg" id="" /></td>
                <td>Fondo de ojo</td>
                <td><input type="checkbox" name="fondoDeOjo" id="" /></td>
                <td>Examen Pie</td>
                <td><input type="checkbox" name="examenDePie" id="" /></td>
                <td>Microalbuminuria</td>
                <td> <input type="number" name="microalbuminuria" id="" /></td>
            </tr>

            
            <tr id="mo" style="text-align: left;">
                
                <td>HDL</td>
                <td><input type="text" name="hdl" id="" /></td>
                <td>LDL</td>
                <td><input type="text" name="ldl" id="" /></td>
                <td>TAGs</td>
                <td><input type="text" name="tags" id="" /></td>
                <td>Creatiniemia</td>
                <td><input type="text" name="creatininemia" id="" /></td>
            </tr>
            
           
        </table>
    </fieldset>

    
    <fieldset>
            <legend>Interconsultas</legend>
            <table width=100% align="center" class="bordes">
                
                <tr id="mo" style="text-align: left;">
                <td>Interconsulta especialidad</td>
                <td><select name="interconsulta1" id="interconsulta1">
                    <option value="NULL">Seleccione</option>
                    <?php while (!$interconsultas_result->EOF) { ?>
                        <option value="<?=$interconsultas_result->fields['valor']?>"><?=$interconsultas_result->fields['especialidad']?></option>
                    <?php
                    $interconsultas_result->MoveNext();}
                    $interconsultas_result->MoveFirst();
                    ?>
                </select></td>
                
                <td><select name="interconsulta2" id="interconsulta1">
                    <option value="NULL">Seleccione</option>
                    <?php while (!$interconsultas_result->EOF) { ?>
                        <option value="<?=$interconsultas_result->fields['valor']?>"><?=$interconsultas_result->fields['especialidad']?></option>
                    <?php
                    $interconsultas_result->MoveNext();}
                    $interconsultas_result->MoveFirst();
                    ?>
                </select></td>
                
                <td><select name="interconsulta3" id="interconsulta1">
                    <option value="NULL">Seleccione</option>
                    <?php while (!$interconsultas_result->EOF) { ?>
                        <option value="<?=$interconsultas_result->fields['valor']?>"><?=$interconsultas_result->fields['especialidad']?></option>
                    <?php
                    $interconsultas_result->MoveNext();}
                    $interconsultas_result->MoveFirst();
                    ?>
                </select></td>
                
                <td><select name="interconsulta4" id="interconsulta1">
                    <option value="NULL">Seleccione</option>
                    <?php while (!$interconsultas_result->EOF) { ?>
                        <option value="<?=$interconsultas_result->fields['valor']?>"><?=$interconsultas_result->fields['especialidad']?></option>
                    <?php
                    $interconsultas_result->MoveNext();}
                    $interconsultas_result->MoveFirst();
                    ?>
                </select></td>
                

            </tr>
                
            </table>
        </fieldset>

    
    
    
    <fieldset>
        <legend>Evoluci&oacute;n DEL RCVG</legend>

        <table width=100% align="center" class="bordes">
            <tr id="mo" style="text-align: left;">
                <td>Inicial:  <?=$beneficiario->getRcvgAnterior()?></td>

            </tr>

            <tr id="mo" style="text-align: left;">
                <td>Actual</td>
                <td>Bajo <input type="radio" name="riesgo_actual" id="" value ="bajo"/></td>
                <td>Moderado <input type="radio" name="riesgo_actual" id="" value ="mode"/></td>
                <td>Alto <input type="radio" name="riesgo_actual" id="" value ="alto"/></td>
                <td>Muy Alto <input type="radio" name="riesgo_actual" value ="muyalto"/> </td>
            </tr>

            <tr id="mo" style="text-align: left;">
                <td>Fecha: <input type="text" name="fecha_seguimiento" id="" onblur="esFechaValida(this);"
                onchange="esFechaValida(this);" onKeyUp="mascara(this,'/',patron,true);" value="<?=Fecha($promotorFechaNac) ?>" />
                    </td>
                <td>M&eacute;dico: </td>
                <td>
                    <select name="medico" id="medico">
                        <option value="NOTOSH">Seleccione un medico</option>
                        <?php 
                        while (!$medicos_result->EOF) {
                            ?>
                            <option value="<?=$medicos_result->fields["id_medico"]?>">
                                <?=($medicos_result->fields["apellido"].", ".$medicos_result->fields["nombre"])?></option>
                            <?php
                            $medicos_result->MoveNext();
                        }   
                        ?>
                        
                    </select>
                </td>
            </tr>
            <input type="hidden" name="clavebeneficiario" value="<?=$parametros["cbeneficiario"]?>"/>
            <input type="hidden" name="idUsuarioCarga" value="1111" />
            <input type="hidden" name="nuevo" value="nuevoRegistro" />
            <input type="hidden" name="riesgo_inicial" value="<?=$beneficiario->getRcvgAnterior()?>" />
            <input type="hidden" name="idUsuarioCarga" value="<?=$usuarioCargaId?>" />
            
        </table>

    </fieldset>
    
    
    <input type="submit" value="Guardar" onclick="return validarFormulario();"/>
</form>

<?php echo fin_pagina();?>


