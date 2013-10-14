<?php
require_once ("../../config.php");
require_once("../../lib/lib.php");
require_once ("../../lib/bibliotecaTraeme.php");
require_once ("../../lib/funciones_misiones.php");
?>
<html>
<body bgcolor="#E0E0E0"></body>
<link rel="stylesheet" type="text/css" href="../inmunizacion/inmunizaciones.css"> 
<?
if ($_POST['id_prestacion_inmu']){
    $e=1;
    $id_prestacion_inmu=$_POST['id_prestacion_inmu'];
    //Verificar si tiene id_comprobante e id_prestacion (Significa que la carga impacto en SUMAR )
    $sql_select="select id_comprobante,id_prestacion,id_liquidacion from inmunizacion.prestaciones_inmu where id_prestacion_inmu=$id_prestacion_inmu";
    $res=sql($sql_select,"Error al traer id_comprobante e id_prestacion")or fin_pagina();
    $id_comprobante=$res->fields["id_comprobante"];
    $id_liquidacion=$res->fields["id_liquidacion"];
    $id_prestacionn=$res->fields["id_prestacion"];
    
    if(!$id_liquidacion){
        if($id_comprobante){
            //Buscar en facturacion.comprobante si tiene id_factura (Si tiene, significa que el comprobante se facturo y no se puede eliminar)
            $sql_select="select id_factura from facturacion.comprobante where id_comprobante=$id_comprobante";
            $res=sql($sql_select,"Error al traer id_factura")or fin_pagina();
            $id_factura=$res->fields["id_factura"];

            if($id_factura){
                //No se puede borrar la prestacion
                $e=0;
            }else{
                                
                //Anula Comprobante
                $sql_update="update facturacion.comprobante set marca=1 where id_comprobante='$id_comprobante'";
                $res=sql($sql_update,"Error al Actualizar comprobante")or fin_pagina();
//                $sql_delete="delete from facturacion.prestacion where id_prestacion=$id_prestacionn";
//                $res=sql($sql_delete,"Error al borrar comprobante")or fin_pagina();
                $e=1;
                
            }
        }
    }else{
        $e=0;
    }
    if($e==1){
        
        //Eliminar Vacuna
        $sql_delete="update inmunizacion.prestaciones_inmu set eliminado=1 where id_prestacion_inmu=$id_prestacion_inmu";
        sql($sql_delete,"Error al Actualizar Vacunas")or fin_pagina();
    }   
}
if ($_POST['descripcion_terreno']){
    $sql_id_terreno="select max(id_terreno)+1 max_id from inmunizacion.terrenos";
    $res_id_terreno = sql($sql_id_terreno,"Error id terreno")or fin_pagina();
    $id_terreno=$res_id_terreno->fields["max_id"];
    if ($id_terreno==NULL){
        $id_terreno=1;
    }
    $descripcion_terreno=$_POST['descripcion_terreno'];
    $cuie=$_POST['cuie'];
    
    $sql_nuevo_terreno="insert into inmunizacion.terrenos(id_terreno,descripcion) values ($id_terreno,'$descripcion_terreno')";
    $res_nuevo_terreno = sql($sql_nuevo_terreno,"Error nuevo terreno")or fin_pagina();
    
    $sql="insert into inmunizacion.terrenos_efectores (id_terreno,cuie) values ($id_terreno,'$cuie')";
    $res= sql($sql,"Error nuevo terreno")or fin_pagina();
}
///////////////boton guardar/////////////////////
if ($_POST['btnGuardar'] == "Guardar") {
     $fechaInmunizacion=$_POST['txtFechaAplicacion'];
     $cuie=$_POST['efector'];
     $fecha_nacimiento=$_POST['fecha_nacimiento_benef'];
     $hoy=date("y-m-d H:i:s");
     $id_vacuna_dosis=$_POST['vacunasSeleccionadas'][0];
     $vacunasSeleccionadas = $_POST['vacunasSeleccionadas'];
     $caracteristicasSeleccionadas=$_POST['caracteristicasSeleccionadas'];
     $id_terreno=$_POST['cmbTerreno'];
     $clave_beneficiario_inmu=$_POST['txtClaveBeneficiario'];
     $sexo=$_POST['sexo'];
     $id_presentacion=$_POST['cmbPresentacion'];
     $id_grupo_riesgo=$_POST['cmbGrupoRiesgo'];
     
     $laboratorio=$_POST['txtLaboratorio'];
     $loteNumero=$_POST['txtLoteNumero'];
     $fechaVencimiento=$_POST['txtFechaVencimiento'];
     
     if(!$fechaVencimiento){
         $fechaVencimiento='9999-01-01';
     }
     if(!$loteNumero){
         $loteNumero=0;
     }
     if(!$laboratorio){
         $laboratorio='';
     }
     
       
        for ($i=0;$i < count($vacunasSeleccionadas);$i++){
            
            $id_vacuna_dosis=$vacunasSeleccionadas[$i];
            
            $sql_id_vacuna="select id_vacuna from inmunizacion.vacunas_dosis where id_vacuna_dosis='$id_vacuna_dosis'";
            $res_id_vacuna=sql($sql_id_vacuna,"Error 1")or fin_pagina();
            $id_vacuna=$res_id_vacuna->fields["id_vacuna"];

            $sql_max_id="select max(id_prestacion_inmu)max_id from inmunizacion.prestaciones_inmu";
            $res_max = sql($sql_max_id,"Error 1")or fin_pagina();
            $id_prestacion=$res_max->fields["max_id"];
            $id_prestacion=$id_prestacion +1;
            $origen=2;
            $id_usuario = $_ses_user['id'];
            
            $sql_insert = "insert into inmunizacion.prestaciones_inmu (id_prestacion_inmu,id_vacuna_dosis,id_vacuna,cuie,clave_beneficiario,id_terreno,fecha_inmunizacion,
                    fecha_nacimiento,fecha_vencimiento,lote,laboratorio,fecha_carga,id_presentacion,id_grupo_riesgo,origen,id_usuario,eliminado) 
                    values ($id_prestacion,$id_vacuna_dosis,$id_vacuna,'$cuie',$clave_beneficiario_inmu,$id_terreno,'$fechaInmunizacion','$fecha_nacimiento','$fechaVencimiento','$loteNumero','$laboratorio',date_trunc('seconds',localtimestamp),$id_presentacion,$id_grupo_riesgo,$origen,$id_usuario,0)";
            $tras = sql($sql_insert, "Al querer guardar la vacuna en prestaciones_inmu")or fin_pagina();
            
            if (!$caracteristicasSeleccionadas==NULL){
                $sql_insert='';

                for ($ii=0;$ii < count($caracteristicasSeleccionadas);$ii++){
                    $id_caracteristica=$caracteristicasSeleccionadas[$ii];
                    $sql_insert="insert into inmunizacion.prestaciones_caracteristicas(id_prestacion_inmu,id_caracteristica)
                    values ($id_prestacion,$caracteristicasSeleccionadas[$ii])";
                    $tras = sql($sql_insert, "Error 4")or fin_pagina();
                }
            }
            
            

            
            //Obtener grupo etareo
            $grupo_etario = calcularGrupoEtareo($fecha_nacimiento, $fechaInmunizacion);
            $grupo_etario = $grupo_etario['categoria'];
            
            $datos_etareos['estaembarazada'] = beneficiarioEmbarazadoUAD($clave_beneficiario_inmu, $fechaInmunizacion);
            $datos_etareos['categoria'] = $grupo_etario;
            
            $sql_nomenclador="select categoria,codigo,patologia from inmunizacion.vacunas_dosis where id_vacuna_dosis=$id_vacuna_dosis";
            $nomenclador = sql($sql_nomenclador, "Error al traer nomenclador de SUMAR")or fin_pagina();
            
            //Obtener id_nomenclador_detalle
            
            $sql_conv = "SELECT cn.id_nomenclador_detalle , modo_facturacion, descripcion
                                                        FROM nacer.efe_conv ec
                                                        INNER JOIN nacer.conv_nom cn USING (id_efe_conv)
                                                        INNER JOIN facturacion.nomenclador_detalle nd on nd.id_nomenclador_detalle=cn.id_nomenclador_detalle
                                                        WHERE ec.cuie='$cuie'
                                                        AND nd.fecha_desde <='$fechaInmunizacion'
                                                        AND nd.fecha_hasta >='$fechaInmunizacion'
                                                        AND ec.activo='t'";
            
            $id_nomenclador_detalle = sql($sql_conv) or die;
            
            if(!$id_nomenclador_detalle->EOF){
            
            $id_nomenclador_detalle = $id_nomenclador_detalle->fields['id_nomenclador_detalle'];     
            $query_precio = buscaPractica($nomenclador->fields["categoria"], $nomenclador->fields["codigo"],$nomenclador->fields["patologia"], $id_nomenclador_detalle, $datos_etareos, $sexo);
            $precio = $query_precio['precio'];
            $id_nomenclador = $query_precio['id_nomenclador'];
            
            if($precio!=0){
 
            //Crear Comprobante para agragar prestacion a SUMAR               
            
            
            //Obtener periodo 
            
            $periodo=substr($fechaInmunizacion,0,7);
            $periodo= str_replace("-", "/", $periodo);
               
            //Obtener idperiodo que corresponde al id de periodo vigente
            
            $sql_periodo = "SELECT id_periodo from facturacion.periodo where periodo='$periodo'";
            $idperiodo = sql($sql_periodo);
            $idperiodo = $idperiodo->fields['id_periodo'];
            
            //Creando el comprobante
            $query = "insert into facturacion.comprobante
		       (cuie, nombre_medico, fecha_comprobante, clavebeneficiario, fecha_carga,periodo,comentario,activo,entidad_alta,id_nomenclador_detalle,tipo_nomenclador,idperiodo,grupo_etario)
		             values
		      ('$cuie','','$fechaInmunizacion','$clave_beneficiario_inmu',date_trunc('seconds',localtimestamp),'$periodo','Prestacion cargada desde Inmunizacion','S','in',$id_nomenclador_detalle,'BASICO',$idperiodo,'$grupo_etario') RETURNING id_comprobante";
            $id_comprobante = sql($query, "Error al insertar el comprobante") or fin_pagina();
            $id_comprobante=$id_comprobante->fields["id_comprobante"];
            
//Insertar prestacion asociado a comprobante creado anteriormente
            

            
            $cantidad = 1;
            
            $db->StartTrans();
            $id_prestacion_sumar = guardarPrestacion($id_comprobante, $id_nomenclador, $cantidad, $precio);
            $codigoconcatenado = $categoria . " " . $codigoelegido;
            coberturaBasica($cuie, $codigoconcatenado, $patologia, $fecha_comprobante, $grupo_etareo, $clavebeneficiario);
            $db->CompleteTrans();
            $accion = "Se registro la Prestacion: " . $categoria . " " . $codigoelegido . " " . $patologia;
            $categoria = '';
            $codigoelegido = '';
            $patologia = '';
            
            
            $sql_update = "update inmunizacion.prestaciones_inmu set id_comprobante=$id_comprobante,id_prestacion=$id_prestacion_sumar where id_prestacion_inmu=$id_prestacion";
            $updt = sql($sql_update, "Error al actualizar")or fin_pagina();
            
            }
            }
            
        }
}
?>
<?php
//Busqueda de Beneficiario//
$dni = $_POST['numero_doc'];
if ($dni != 0) {
    $SQLef = "SELECT clave_beneficiario,numero_doc, nombre_benef,apellido_benef,localidad,fecha_nacimiento_benef,sexo,fecha_inscripcion 
                FROM uad.beneficiarios where numero_doc='$dni'";
    $res_extra = sql($SQLef, "Error en DNI") or excepcion("Error en DNI");

    if ($res_extra->RecordCount() > 0) {
        $dni = $res_extra->fields["numero_doc"];
        $nombre = $res_extra->fields["nombre_benef"];
        $apellido = $res_extra->fields["apellido_benef"];
        $localidad = $res_extra->fields["localidad"];
        $fecnac = $res_extra->fields["fecha_nacimiento_benef"];
        $sex = $res_extra->fields["sexo"];
        $fecinsc = substr($res_extra->fields["fecha_inscripcion"],0,10);
        $clave_beneficiario_inmu = $res_extra->fields["clave_beneficiario"];

        $sql = "select  c.id_factura,
                        j.id_comprobante,
                        id_prestacion,
                        j.id_liquidacion,
                        t.descripcion terreno,
                        id_prestacion_inmu,
                        id_vacuna_dosis,
                        a.descripcion,
                        fecha_inmunizacion,
                        j.fecha_carga,
                        j.cuie,
                        e.nombreefector,
                        case when (id_comprobante is not null or id_comprobante > 0) then (select count(*) from facturacion.prestacion where prestacion.id_comprobante = j.id_comprobante) else 0 end as TOTAL            
            from inmunizacion.prestaciones_inmu as j
            left join inmunizacion.vacunas_dosis as a using(id_vacuna_dosis)
            left join inmunizacion.terrenos as t using(id_terreno)
            left join facturacion.smiefectores as e using(cuie)
            left join facturacion.comprobante as c using(id_comprobante)
            where clave_beneficiario='$clave_beneficiario_inmu' and eliminado=0
            order by fecha_carga";
        
        
                
        $res_inmu = sql($sql, "Error al traer el beneficiario") or fin_pagina();
    } else {
        echo ("No se encuentra el Beneficiario");
        $var=encode_link("../inscripcion/ins_admin.php",array('num_doc'=>$dni));
        ?>

        <script language="JavaScript"> alert('No existe Beneficiario'); 
            self.location="<?=$var?>"; 
            
        </script> 
        <?
    }
}
if ($_POST['cuie_efector']) {
    $sql = "select * from inmunizacion.terrenos 
    where id_terreno in(select id_terreno from inmunizacion.terrenos_efectores where cuie='".$_POST['cuie_efector']."')";
    $res_terrenos = sql($sql, "Error al traer Terrenos") or fin_pagina();
    $cuie_terreno=$_POST['cuie_efector'];
}
if ($_POST['cuie']) {
    $sql = "select * from inmunizacion.terrenos 
    where id_terreno in(select id_terreno from inmunizacion.terrenos_efectores where cuie='".$_POST['cuie']."')";
    $res_terrenos = sql($sql, "Error al traer Terrenos") or fin_pagina();
    $cuie_terreno=$_POST['cuie'];
}


?>                                   

<script src='../../lib/jquery.min.js' type='text/javascript'></script>
<link rel='stylesheet' href='../../lib/jquery/ui/jquery-ui.css'/>
<script src='../../lib/jquery/jquery-ui.js' type='text/javascript'></script>
<script src='../../lib/jquery/ui.multiselect.js' type='text/javascript'></script>

<!--<style type="text/css">
    #mitr_{
        
        background-color:yellow;
        
    }
</style>-->
<script>
     
    
     
    //De lista a lista
    
    function SelectMoveRows(SS1,SS2){
        var SelID='';
        var SelText='';
        // Move rows from SS1 to SS2 from bottom to top
        for (i=SS1.options.length - 1; i>=0; i--)
        {
            if (SS1.options[i].selected == true)
            {
                SelID=SS1.options[i].value;
                SelText=SS1.options[i].text;
                var newRow = new Option(SelText,SelID);
                SS2.options[SS2.length]=newRow;
                SS1.options[i]=null;
            }
        }
    }
    //Eliminar vacuna
    function eliminar_vacuna(id_prestacion_inmu){
        $.post("inmunizacion_admin.php",{id_prestacion_inmu:id_prestacion_inmu,numero_doc:$("#num_doc").val()},
            function(respuesta){
              var container=  $(respuesta).find('tr#tblVacunasCargadas');
              $("tr#tblVacunasCargadas").empty().append(container);
        });
        alert("Vacuna Eliminada!!");
    }
    function agregar_terreno(descripcion_terreno,cuie){
        if (validar_terreno()){
        $.post("inmunizacion_admin.php",{descripcion_terreno:descripcion_terreno,cuie:cuie},
            function(respuesta){
              //var container=  $(respuesta).find('td#cmbTerreno');
              //$("td#cmbTerreno").empty().append(container);
              
              var container=  $(respuesta).find('select#selectTerreno');
              $("td#cmbTerreno").html(container);
              
              
              $("#id_txtTerreno").val('');
              alert("Terreno agregado con exito!" );
              
              apagar();
        }); 
        
        } 
    }

    //Script para el manejo de combobox dosis
    $(document).ready(function(){
         
        $(function() {
            $( "#acordeon" ).accordion();
        }); 
         
        $("#txtFechaAplicacion").datepicker({
           maxDate:'+0d'
        });
        
        $("#txtFechaVencimiento").datepicker({
            minDate:'+0d'
        });
        
        jQuery(function($){
        $.datepicker.regional['es'] = {
        closeText: 'Cerrar',
        prevText: 'Ant>',
        nextText: 'Sig>',
        currentText: 'Hoy',
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
        weekHeader: 'Sm',
        dateFormat: 'yy-mm-dd', //'dd/mm/yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        buttonImage: "images/calendar.gif",
        yearSuffix: ''};
        $.datepicker.setDefaults($.datepicker.regional['es']);
        }); 
        
        
        $("#vacuna").change(function(){
            //alert($("#idvacuna").val());
            $.ajax({
                url:"procesa.php",
                type: "POST",
                data:"idvacuna="+$("#idvacuna").val(),
                success: function(opciones){
                    $("#dosis").html(opciones);
        						
                }
            })
        });
           
        $("#tipo_nino").click(function(){
            //alert($("#tipo_persona").val());
            $.ajax({
                url:"procesa.php",
                type: "POST",
                data:"tipo_persona="+$("#tipo_persona").val(),
                success: function(opciones){
                    $("#vacuna").html(opciones);
        						
                }
            })
            // alert(opciones);
        });
        $("#tipo_adulto").click(function(){
            //alert($("#tipo_persona").val());
            $.ajax({
                url:"procesa.php",
                type: "POST",
                data:"tipo_persona="+$("#tipo_persona").val(),
                success: function(opciones){
                    $("#vacuna").html(opciones);
        						
                }
            })
                    
            // alert(opciones);  
        }); 
        
        
        $("select#efector").on('change',function(){
            $.post("inmunizacion_admin.php",{cuie_efector:$("select#efector").val()},
            function(respuesta){
              var container=  $(respuesta).find('select#selectTerreno');
              $("td#cmbTerreno").html(container);
        });
        });
    
    });
    
    $("#terreno").click(function(){
        alert ("cargar lugar!!");
        
        
            
    });
    
    
    
   
         
    function showvacuna_nac(){
        var vacuna = document.getElementById('vacuna')[document.getElementById('vacuna').selectedIndex].value;
        document.all.idvacuna.value = vacuna;
    }
            
    function showdosis(){
        var dosis = document.getElementById('dosis')[document.getElementById('dosis').selectedIndex].value;
        document.all.iddosis.value = dosis;
        //alert  ( document.all.iddosis.value);
    }  
    function showcuie(){
        var cuie = document.getElementById('efector')[document.getElementById('efector').selectedIndex].value;
        document.all.cuie.value = cuie;
       
    }   
    function validar_formulario(){
        var fechaAplicacion = document.getElementById("txtFechaAplicacion").value;
        var ClaveBeneficiario = document.getElementById("txtClaveBeneficiario").value;
        var cuie = document.getElementById('efector')[document.getElementById('efector').selectedIndex].value;
        var terreno = document.getElementById('selectTerreno')[document.getElementById('selectTerreno').selectedIndex].value;
        var grupo_riesgo= document.getElementById('cmbGrupoRiesgo')[document.getElementById('cmbGrupoRiesgo').selectedIndex].value;
        var presentacion= document.getElementById('cmbPresentacion')[document.getElementById('cmbPresentacion').selectedIndex].value;
        
        if (ClaveBeneficiario =='')
        {   

            alert("Debe buscar beneficiario" );
            return false;

        }
        var cargado = document.getElementById("vacuna").value;
        if (cargado =='')
        {   

            alert("Debe Seleccionar 1 vacuna" );
            return false;

        }
        var seleccion = contar(document.getElementById("vacuna"));
        if (seleccion >1)
        {   

            alert("Debe seleccionar una sola vacuna. Vacunas seleccionadas "+seleccion );
            return false;

        } 
        if (cuie =='-1')
        {   

            alert("Debe seleccionar un efector" );
            return false;

        } 
        if (fechaAplicacion =='')
        {   

            alert("Debe seleccionar fecha de Aplicaion" );
            return false;

        }
        if (terreno =='-1')
        {   

            alert("Debe seleccionar un lugar de Vacunacion" );
            return false;

        }
        if (presentacion =='-1')
        {   

            alert("Debe seleccionar tipo de presentacion" );
            return false;

        }
        if(document.getElementById('trGrupoRiesgo').style.display == "block"){
            if (grupo_riesgo =='-1')
            {   
                
                alert("Debe seleccionar grupo de riesgo" );
                return false;

            }
        }
        
    }
//    function multiseleccion(){
//        var cargado = document.getElementById("vacuna").value;
//        if (cargado =='')
//        {   
//
//            alert("Debe Seleccionar al menos 1 vacuna" );
//            return false;
//
//        }
//       
//    }
    function validar_terrenox(){
        var cuie = document.getElementById('efector')[document.getElementById('efector').selectedIndex].value;
        
        if (cuie =='-1')
        {   

            alert("Debe seleccionar un efector" );
            return false;

        }else{
            //agregar_terreno('Terrenaso');
        }   
    }
    
    function validar_terreno(){
        var cuie = document.getElementById('efector')[document.getElementById('efector').selectedIndex].value;
        var terreno=document.getElementById('id_txtTerreno').value;
        if (cuie =='-1')
        {   

            alert("Debe seleccionar un efector" );
            return false;

        }else{
            if(terreno==''){
                alert("Falta la descripcion del terreno" );
            return false;
            }else{
                
                
                
                return true;
            }
        }  
        
    }
     //  Animacion de acordeon
    $(function() {
        $( "#accordion" ).accordion();
    });
    
    //  Multiselect Caracteristicas
    $(function(){
            $(".multiselect").multiselect();
    });
    
   

    
    function contar(obj) {
        num=0;
        for(i=0; opt=obj.options[i]; i++)
            if(opt.selected) num++;
        return num;
    }
 
    function habilitar(obj) {
       
       for(i=0; opt=obj.options[i]; i++)
            if(opt.selected){
                if(opt.value=='27'| opt.value=='66' | opt.value=='67' | opt.value=='68' | opt.value=='83' | opt.value=='69'){
                    
                    mostrar();
                }else{
                    ocultar();
                }
            }
    }
    
    function apagar(){
        var obj = document.getElementById('mitr')
        if(obj.style.display == "block" || obj.style.display == "") obj.style.display = "none"
        else obj.style.display = "block"
    }
    function ocultar(){
        var obj = document.getElementById('trGrupoRiesgo')
        obj.style.display = "none"
    }
function mostrar(){
        var obj = document.getElementById('trGrupoRiesgo')
        obj.style.display = "block"
    }

</script>  

<form action="inmunizacion_admin.php" method="post" name="c_inmunizacion" background="../../imagenes/fondo.gif" bgcolor="#B7CEC4">  

<table width=90%  style="padding-bottom: 20px" align="center" class="bordes" bgcolor='<?= $bgcolor3 ?>' >
    <tr>
        <td align="center">
            <div>	
                    <h3 style="background-color:#C0CDF2"> <center> Descripcion Beneficiario </center></h3>
            </div>
            <table width=65% align="center" class="bordes" bgcolor='<?= $bgcolor3 ?>'> 
               
                    
                    <tr>
                        <td align="right">
                            <label align="right"> <b>Nro. Documento:</b></label>
                        </td>
                        <td>
                            <input id="num_doc" type="text" value="<?= $dni ?>" name="numero_doc" size=8 >
                            <input type="submit" size="8" value="Buscar" name="buscar" align="left">
                            <input type="hidden" value="<?= $clave_beneficiario_inmu ?>" id="txtClaveBeneficiario" name="txtClaveBeneficiario"/>
                        </td>
                    </tr>    
                    <tr>
                        <td align="right">
                            <label> <b>Apellido:</b></label>  
                        </td>
                        <td>
                            <input type="text" readonly="readonly" value="<?= $apellido ?>" name="apellido_benef" size=20 />
                        </td>
                    </tr>   
                    <tr>
                        <td align="right">
                            <label> <b>Nombre:</b></label> 
                        </td>  
                        <td>
                            <input type="text" readonly="readonly" value="<?= $nombre ?>" name="nombre_benef" size=20 /> 
                        </td>
                    </tr> 
                    <tr>
                        <td align="right">
                            <label> <b>Fecha Nacimiento:</b></label> 
                        </td>
                        <td>
                            <input type="text" readonly="readonly" value="<?= $fecnac ?>" name="fecha_nacimiento_benef" size=10 /><br />
                        </td>
                    </tr>
                    <tr>
                        <td align="right">
                            <label> <b>Fecha Inscripcion:</b> </label> 
                        </td>
                        <td>
                            <input type="text" readonly="readonly" value="<?= $fecinsc ?>" name="fecha_insc" size=10 /><br />
                        </td>
                    </tr>
                    <tr>
                        <td align="right">
                            <label><b>Localidad:</b></label> 
                        </td>
                        <td>
                            <input type="text" readonly="readonly" value="<?= $localidad ?>" name="localidad_nac" size=20 /><br />    
                        </td>
                    </tr>
                    <tr>
                        <td align="right"><label> <b>Sexo:</b> </label></td>
                        <td><input type="text" readonly="readonly" value="<?= $sex ?>" name="sexo" size=9 /><br/></td>
                    </tr>
            </table>  

            <div>
                <h3 style="background-color:#C0CDF2"><center> Vacunas Aplicadas </center></h3>
                
            </div>
            
            <table width=100% align="center" class="bordes" bgcolor='<?= $bgcolor3 ?>'>

               
            <tr id="tblVacunasCargadas">
                <td align="center">

                    <table id="tblVacunasCargadas" name="tblVacunasCargadas">
                            <?php
                            if ($res_inmu) {?>
                                <tr style="background-color:#B0B3B3">
                                    <td width="500"  align="center"><b>Fecha de Vacunacion</b></td>
                                    <td width="500"  align="center"><b>Fecha de Carga</b></td>
                                    <td width="500"  align="center"><b>Descripcion</b></td>
                                    <td width="500"  align="center"><b>Efector</b></td>
                                    <td width="500"  align="center"><b>Lugar de Vacunacion</b></td>
                                    
                                   <? if (permisos_check("inicio", "detalle")) { ?>
                                    <td width="200"  align="center"><b>Id Prestacion</b></td>
                                    <td width="200"  align="center"><b>Id Comprobante</b></td> 
                                    <td width="200"  align="center"><b>Id Factura</b></td>
                                    <td width="200"  align="center"><b>Id Liquidacion</b></td>
                                    <td width="200"  align="center"><b>Total</b></td>
                                   <? } ?>  
                                    
                                   
                                   
                                </tr>
                            <?
                                $alternate=0;
                                while (!$res_inmu->EOF) {
                                    
                                    if($alternate==0){
                                        ?>
                                        <tr style="background-color:#F5F9F9">
                                        <?
                                        $alternate=1;
                                    }else{
                                        ?>
                                        <tr style="background-color:#C5C6C6">  
                                        <?
                                        $alternate=0;
                                    }
                                    ?>
                                        
                                            
                                            <td align="center"><?= substr($res_inmu->fields["fecha_inmunizacion"],0,10) ?></td>
                                            <td align="center"><?= $res_inmu->fields["fecha_carga"] ?></td>
                                            <td align="center"><?= $res_inmu->fields["descripcion"] ?></td>
                                            <td align="center"><?= $res_inmu->fields["nombreefector"] ?></td>
                                            <td align="center"><?= $res_inmu->fields["terreno"] ?></td>
                                            
                                            <? if (permisos_check("inicio", "detalle")) { ?>
                                            <td align="center"><?= $res_inmu->fields["id_prestacion"] ?></td>
                                            <td align="center"><?= $res_inmu->fields["id_comprobante"] ?></td>    
                                            <td align="center"><?= $res_inmu->fields["id_factura"] ?></td>
                                            <td align="center"><?= $res_inmu->fields["id_liquidacion"] ?></td>
                                            <td align="center"><?= $res_inmu->fields["total"] ?></td>
                                            <? } ?> 
                                            
                                            <? if (permisos_check("inicio", "borrar_inmunizacion")) { ?>
                                                                        
                                            <? } ?> 
                                            
                                            <? if (permisos_check("inicio", "borrar_inmunizacion")) { 
                                                if(!$res_inmu->fields["id_liquidacion"] & !$res_inmu->fields["id_factura"] & $res_inmu->fields["total"] < 2 ){
                                            ?>
<!--                                                <td><img src="../../imagenes/inmunizacion/mas.gif" alt="" style="cursor:pointer;float: right" /></td>-->
                                                    <td><img src="../inmunizacion/imagenes/cancel.png" alt="" onclick="eliminar_vacuna(<?=$res_inmu->fields["id_prestacion_inmu"]?>);" style="cursor:pointer;float: right"/></td>                                
                                            <? 
                                                }
                                                
                                               } 
                                            ?>  
                                            
                                            
                                           
                                        </tr>
                                    <?php
                                    $res_inmu->movenext();
                                }
                            }
                            ?>
                    </table>
                    
                       <A href="../inmunizacion/informePDF.php?clave=<?=$clave_beneficiario_inmu;?>">PDF</A>                                             
                   
                    
                </td>
              </tr>
            </tr> 
</table>
<div>
    <h3 style="background-color:#C0CDF2"> <center> Carga de Vacunas </center></h3>
    
</div>            
<table width=100% align="center" class="bordes" bgcolor='<?= $bgcolor3 ?>'>
    
    <tr>
        <td align="center">
            <table>
                <tr>
                    <td align="center">	
                        <b>.:Vacunas Disponibles:.</b><br>
                        <select id="vacuna" name="vacunasSeleccionadas[]" multiple="multiple" Style="width:450px" onclick="habilitar(this)"> 
                            <?
                            $sql = "select * from inmunizacion.vacunas_dosis  where habilitado=1 order by descripcion";
                            $res_vacunas = sql($sql) or fin_pagina();

                            while (!$res_vacunas->EOF) {
                                //$com_gestion = $res_efectores->fields['com_gestion'];
                                $id_vacuna = $res_vacunas->fields['id_vacuna_dosis'];
                                $descripcion = $res_vacunas->fields['descripcion'];
                                
                                ($com_gestion == 'FALSO') ? $color_style = '#F78181' : $color_style = '';
                                ?>
                                                            <option value=<?= $id_vacuna; ?> Style="background-color: <?= $color_style ?>;"><?= $descripcion ?></option>
                                                            <?
                                                            $res_vacunas->movenext();
                                                        }
                                                        ?>                          
                        </select>
                    </td>
                </tr>
                <tr>
                    <td align="center">	
                        <b>.:Condiciones Disponibles:.</b> 
                        <select id="lst_caracteristicas" name="caracteristicasSeleccionadas[]" class="multiselect" multiple="multiple"> 
                            <?
                            $sql = "select * from inmunizacion.caracteristicas  order by descripcion";
                            $res_caracteristicas = sql($sql) or fin_pagina();

                            while (!$res_caracteristicas->EOF) {
                                //$com_gestion = $res_efectores->fields['com_gestion'];
                                $id_caracteristica = $res_caracteristicas->fields['id_caracteristica'];
                                $descripcion_caract = $res_caracteristicas->fields['descripcion'];
                                ($com_gestion == 'FALSO') ? $color_style = '#F78181' : $color_style = '';
                                ?>
                                                            <option value=<?= $id_caracteristica; ?> Style="background-color: <?= $color_style ?>;"><?= $descripcion_caract ?></option>
                                                            <?
                                                            $res_caracteristicas->movenext();
                                                        }
                                                        ?>                          
                        </select>
                    </td>
                </tr>
            </table> 
        </td>
    </tr>
    
</table>
    <div>
    <h3 style="background-color:#C0CDF2"> <center> Otros Datos </center></h3>
    
    </div>

       <div id="stylized">
            <table align="center" id="tblOtrosDatos" >

                <tr>
                    <td align="right">
                        <label><b> * Efector: </b></label>
                    </td>
                    <td>
                        <select name="efector" id="efector">
                            <?

                            $usuario_logueado=$_ses_user['id'];


                            if (es_cuie($_ses_user['login']))

                                $sql = "select nombreefector, cuie from facturacion.smiefectores
                            where cuie in(Select cuie from sistema.usu_efec where id_usuario='$usuario_logueado')";                                    

                            else {
                                echo "<option value=-1>Seleccione</option>";
                                $sql = "select nombreefector, cuie from facturacion.smiefectores
                            where cuie in(Select cuie from sistema.usu_efec where id_usuario='$usuario_logueado')";
                            }
                            $res_efectores = sql($sql) or fin_pagina();

                            while (!$res_efectores->EOF) {
                                //$com_gestion = $res_efectores->fields['com_gestion'];
                                $cuie = $res_efectores->fields['cuie'];
                                $nombre_efector = $res_efectores->fields['nombreefector'];
                                ($com_gestion == 'FALSO') ? $color_style = '#F78181' : $color_style = '';
                            ?>
                                <option value=<?= $cuie; ?> Style="background-color: <?= $color_style ?>;"><?= $cuie . " - " . $nombre_efector ?></option>
                                <?
                                $res_efectores->movenext();
                            }
                            ?> 
                        </select>
                    </td>
                </tr>  
                <tr>
                    <td align="right"><label> <b>* Fecha Aplicacion:</b> </label><br></td>
                    <td><input type="text" name="txtFechaAplicacion" id="txtFechaAplicacion" size=20 readonly="readonly"/><br></td>
                </tr>       
                <tr id="cmbTerreno0">
                    <td align="right"><label> <b>* Lugar Vacunacion:</b> </label><br></td>
                    <td id="cmbTerreno" align="left">
                        <select  id="selectTerreno" name="cmbTerreno">
                                <?
                                echo "<option value=-1>Seleccione</option>";
                                echo "<option value=1>Vacunatorio</option>";

                                if (!$res_terrenos==NULL){

                                    while (!$res_terrenos->EOF) {
                                        $id_terreno = $res_terrenos->fields['id_terreno'];
                                        $descripcion_terreno = $res_terrenos->fields['descripcion'];
                                    ?>
                                        <option value=<?= $id_terreno; ?> Style="background-color: <?= $color_style ?>;"><?= $descripcion_terreno?></option>
                                        <?
                                        $res_terrenos->movenext();
                                    }
                                }
                                ?>
                        </select>
                    </td>
                    <td><img id="miimagen2" src="../../imagenes/inmunizacion/mas.gif" alt="" style="cursor:pointer;float: right" onclick="apagar();"/></td> 
                   
                    <!--<td><img src="../../imagenes/inmunizacion/mas.gif" alt="" style="cursor:pointer;float: right" onclick="validar_terreno();"/></td> -->
                    <!-- <td><A href="../inmunizacion/carga_terreno.php?clave=<?=$clave_beneficiario_inmu;?>"  retunr onclick="validar_terreno();">Nuevo Terreno </A></td> -->
                </tr>
                <tr id="mitr" style="display:none" >
                    <td align="right"><label> <b>Nuevo Terreno:</b> </label><br></td>
                    <td align="left"><input id="id_txtTerreno" type="text" name="txtTerreno" size=20 /></td>
                    <td><img id="miimagen" src="../../imagenes/inmunizacion/mas.gif" alt="" style="cursor:pointer;float: right" onclick="agregar_terreno(document.all.txtTerreno.value,document.all.efector.value);"/></td>
                </tr>
                <tr>
                    <td align="right"><label> <b>Presentacion:</b> </label><br></td>
                    <td align="left">
                        <select name="cmbPresentacion" id="cmbPresentacion" > 
                        <?
                            echo "<option value=-1>Seleccione</option>";
                            echo "<option value=1>Monodosis</option>";
                            echo "<option value=2>Multidosis</option>";
                        ?>        
                        </select>
                    </td>
                </tr>
                
                
                <tr id="trGrupoRiesgo" style="display:none" align="center" >
                   
                    <td align="right"><label> <b>Grupo de Riesgo:</b> </label><br></td>
                    <td align="left">
                        <select name="cmbGrupoRiesgo" id="cmbGrupoRiesgo"> 
                        <?
                            echo "<option value=-1>Seleccione</option>";
                            echo "<option value=1>Enfermedades Respiratorias</option>";
                            echo "<option value=2>Enf. Cardíacas</option>";
                            echo "<option value=3>Inmunodeficiencias</option>";
                            echo "<option value=4>Pacientes Oncohematologicos y transplantado</option>";
                            echo "<option value=5>6  a 24 meses</option>";
                            echo "<option value=6>Mayores de 64 años</option>";
                            echo "<option value=7>Otros</option>";
                        ?>        
                        </select>
                    </td>
                   
                </tr>                    
                                
                <tr>
                    <td align="right"><label> <b>Laboratorio:</b> </label><br></td>
                    <td><input type="text" name="txtLaboratorio" size=20 /><br></td>
                </tr> 
                <tr>
                    <td align="right"><label> <b>Lote Numero:</b> </label><br></td>
                    <td><input type="text" name="txtLoteNumero" size=20 /><br></td>
                </tr>
                <tr>
                    <td align="right"><label> <b>Fecha Vencimiento:</b> </label><br></td>
                    <td><input type="text" name="txtFechaVencimiento" id="txtFechaVencimiento" size=20 readonly="readonly"/><br></td>
                </tr>
                <tr>
                    <th colspan="2"> 
                        <input id="miboton" type="submit" size="8" value="Guardar" name="btnGuardar" align="left" onClick=" return validar_formulario()">
                    </th>
                </tr>
            </table>
        </div>
  
<!-- <div>
    <h3 style="background-color:#C0CDF2"> </h3>
 </div>        -->
</td>
</tr>   
</table>   
</form>



</body>


<?php
    echo fin_pagina(); 
?>
</html>