<?php

require_once("../../config.php");
require_once ("../remediar/resumen_carga_operador_funciones.php");

	

if($_POST['buscar'] || $r!=''){
        # $usuarioCargaData[0]->Apellido, $usuarioCargaData[1]->Nombre

        $usuarioCargaData = explode(",",$_POST['nombre']);
        $usuarioCarga_sql = "select id_usuario from sistema.usuarios where nombre = TRIM('".$usuarioCargaData[1]."') and Apellido = TRIM('".$usuarioCargaData[0]."')";

        $usuarioCarga = sql($usuarioCarga_sql) or die();
        $usuarioCargaId = $usuarioCarga->fields['id_usuario'];
        
        $inscripcionesFDesde = $_POST['f_desde'];
        $inscripcionesFHasta = $_POST['f_hasta'];
        
        if($inscripcionesFHasta == ''){
            $inscripcionesFHasta = date("d/m/Y");
            $_POST['f_hasta'] = date("d/m/Y");
        }
        
        $inscripcionesFDesde = Fecha_db($inscripcionesFDesde);
        $inscripcionesFHasta = Fecha_db($inscripcionesFHasta);
        
        
        
        $inscripciones_sql = "(select fecha_carga::date, tipo_transaccion from uad.beneficiarios b
            where (b.fecha_carga between '".$inscripcionesFDesde."' and '".$inscripcionesFHasta."'
            and b.usuario_carga = '".$usuarioCargaId."')
            or
            (b.fecha_verificado between '".$inscripcionesFDesde."' and '".$inscripcionesFHasta."'
            and b.usuario_verificado = '".$usuarioCargaId."')
            group by fecha_carga, tipo_transaccion
            order by fecha_carga)";
        
        
        $inscripciones = sql($inscripciones_sql) or die();
        
        
        $InscripcionesData = array();
        $inscripcionesAltas = 0;
        $inscripcionesMod = 0;
        $inscripcionesBajas = 0;
        
            while(!$inscripciones->EOF){
                $fechaTemp = $inscripciones->fields['fecha_carga'];
                $inscripcionesAltas = 0;
                $inscripcionesMod = 0;
                $inscripcionesBajas = 0;

                while($fechaTemp == $inscripciones->fields['fecha_carga']){

                    switch($inscripciones->fields['tipo_transaccion']){

                        case 'A':
                            $inscripcionesAltas +=1;
                            break;
                        case 'M':
                            $inscripcionesMod +=1;
                            break;

                        case 'B':
                            $inscripcionesBajas +=1;
                            break;

                    }       

                   $inscripciones->MoveNext();
                }

               $InscripcionesData[] = (array($fechaTemp, $inscripcionesAltas, $inscripcionesMod));

          }


          #     Verificacion Remediar + Redes 
          $operadoresSql = SqlOperadores($inscripcionesFDesde,$inscripcionesFHasta,$usuarioCargaId);
          $operadoresResult = sql($operadoresSql);
          $operadoresCargas = 0;


          while(!$operadoresResult->EOF){
        
            if (isset($operador)) {
                
                if ($operador->isOperador($operadoresResult->fields['operador_id'])) {
                    $operador->NuevaCarga($operadoresResult);
                }else{
                    $operadores[] = $operador;
                    $operador = new Operador();
                    $operador->ConstruirResult($operadoresResult);
                    $operador->NuevaCarga($operadoresResult);
                }
                
            }else{
                $operador = new Operador();
                $operador->ConstruirResult($operadoresResult);
                $operador->NuevaCarga($operadoresResult);        
            }
      
            
            $operadoresCargas +=1;
            $operadoresResult->MoveNext();
        }

        if ($operadoresCargas > 0) {
            $operadores[] = $operador;
        }

      
      }
      
        
?>
<script>
    
var patron = new Array(2,2,4)
var patron2 = new Array(5,16)
//Validar Fechas
function esFechaValida(fecha){
    if (fecha != undefined && fecha.value != "" ){
        if (!/^\d{2}\/\d{2}\/\d{4}$/.test(fecha.value)){
            alert("formato de fecha no vï¿½lido (dd/mm/aaaa)");
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
            alert("Fecha introducida errï¿½nea");
            return false;
    }
 
        if (dia>numDias || dia==0){
            alert("Fecha introducida errï¿½nea");
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


 function validar_formulario(){
    FDesde = document.getElementById("f_desde").value;
    FHasta = document.getElementById("f_hasta").value;
    
    if (FDesde.length < 10)
    {   
        alert("Debe ingresar un rango de fecha para realizar una busqueda" );   
        return false;
  
    } 
    else {
        document.getElementById('mensaje').innerHTML= "<h1>Realizando consulta espere por favor</h1>";
        return true;
    }
}
</script>

<?php
    if (!permisos_check('inicio','consulta_todos')){ 
        $listaUsuarios_sql = "select nombre, apellido from sistema.usuarios where id_usuario = ".$_ses_user['id'];
    }else{
        $listaUsuarios_sql = "select u.nombre, u.apellido from sistema.usuarios u order by u.apellido,u.nombre";
    }    
    $listaUsuarios = sql($listaUsuarios_sql);
    
    echo $html_header;

?>





<style type="text/css">
    
    #page-wrap { width: 90%; margin: 10px auto; }
    
    #tabData .list-wrap { background: #eee; padding: 10px; margin: 0 0 15px 0; }

    #tabData ul { list-style: none; }
    #tabData ul li a { display: block; border-bottom: 1px solid #666; padding: 4px; color: #666; }
    #tabData ul li a:hover { background: #333; color: white; }
    #tabData ul li:last-child a { border: none; }

    #tabData .nav { overflow: hidden; }
    #tabData .nav li { width: 130px; float: left; margin: 0 10px 0 0; background-color:#006699;
    -webkit-border-radius:6px;
    -moz-border-radius:6px;
    border-radius:6px;}
    #tabData .nav li.last { margin-right: 0; }
    #tabData .nav li a { display: block; padding: 5px; background: #666; color: white; font-size: 10px; text-align: center; border: 0; }

    #tabData li a.current,#tabData li a.current:hover { background-color: #006699 !important; color: white; }
    #tabData .nav li a:hover, #tabData .nav li a:focus { background: #999; background-color:#006699; -webkit-border-radius:6px;-moz-border-radius:6px;border-radius:6px;}

    
</style>

<!-- Importacion de librerias para interaccion grafica y de funcionamiento en HTML -->
<script src='../../lib/jquery.min.js' type='text/javascript'></script>
<script src='../../lib/jquery/jquery-ui.js' type='text/javascript'></script>
<link rel="stylesheet" href="../../lib/jquery/ui/jquery-ui.css" />
<!-- ---------------------------------------------------------------------------- -->



<!-- Constructor de organicTabs -->
<script type="text/javascript">

(function($) {

    $.organicTabs = function(el, options) {
    
        var base = this;
        base.$el = $(el);
        base.$nav = base.$el.find(".nav");
                
        base.init = function() {
        
            base.options = $.extend({},$.organicTabs.defaultOptions, options);
            
            // Accessible hiding fix
            $(".hide").css({
                "position": "relative",
                "top": 0,
                "left": 0,
                "display": "none"
            }); 
             
            base.$nav.delegate("li > a", "click", function() {
            
                // Figure out current list via CSS class
                var curList = base.$el.find("a.current").attr("href").substring(1),
                
                // List moving to
                    $newList = $(this),
                    
                // Figure out ID of new list
                    listID = $newList.attr("href").substring(1),
                
                // Set outer wrapper height to (static) height of current inner list
                    $allListWrap = base.$el.find(".list-wrap"),
                    curListHeight = $allListWrap.height();
                $allListWrap.height(curListHeight);
                                        
                if ((listID != curList) && ( base.$el.find(":animated").length == 0)) {
                                            
                    // Fade out current list
                    base.$el.find("#"+curList).fadeOut(base.options.speed, function() {
                        
                        // Fade in new list on callback
                        base.$el.find("#"+listID).fadeIn(base.options.speed);
                        
                        // Adjust outer wrapper to fit new list snuggly
                        var newHeight = base.$el.find("#"+listID).height();
                        $allListWrap.animate({
                            height: newHeight
                        });
                        
                        // Remove highlighting - Add to just-clicked tab
                        base.$el.find(".nav li a").removeClass("current");
                        $newList.addClass("current");
                        
                    });
                    
                }   
                
                // Don't behave like a regular link
                // Stop propegation and bubbling
                return false;
            });
            
        };
        base.init();
    };
    
    $.organicTabs.defaultOptions = {
        "speed": 300
    };
    
    $.fn.organicTabs = function(options) {
        return this.each(function() {
            (new $.organicTabs(this, options));
        });
    };
    
})(jQuery);

</script>

<!-- Constructor de organicTabs -->



<!-- Inicializaror de organicTabs -->
<script>
    $(function() {

        //$("#example-one").organicTabs();
        
        $("#tabData").organicTabs({
            "speed": 200
        });

    });
</script>
<!-- Inicializaror de organicTabs -->



<!-- Formulario de bÃºsqueda -->
<div name="mensaje" id="mensaje" align="center"></div>
<form name=form1 action="resumen_carga" method=POST>
    <table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
         <tr>
          <td align=center>
              <br />
                
                <select name="nombre" <? echo $deshabilitado ?> >
                    <?php 
                        # Muestra los usuarios de la base de datos que 
                        # Aparecen como cargadores de uad.beneficiarios
                        while (!$listaUsuarios->EOF) {
                            $data = $listaUsuarios->fields['apellido'] . ", " .$listaUsuarios->fields['nombre'] ;
                            
                            if ($data == $_POST['nombre']){
                            ?>
                            <option value="<?= $data ?>" selected="selected" ><?= $data ?></option>
                            
                         <?php
                            }else{
                         ?>
                             
                            <option value="<?= $data ?>"><?= $data ?></option>
                             
                    <?php
                            }
                      $listaUsuarios->MoveNext();}  
                    ?> 
                    </select>
              
                    <input type=submit name="buscar" value='Buscar' onClick="return validar_formulario()"></input>
                    <br />
                    <br />
                    <b>Fecha Desde:</b><input type="text" size="10" maxlength="10" name="f_desde" id="f_desde" onblur="esFechaValida(this);" onchange="esFechaValida(this);" onKeyUp="mascara(this,'/',patron,true);" value="<?=$_POST['f_desde']?>" <?=$r?>/><?=link_calendario('f_desde');?> 
                    <b> Hasta: </b><input type="text" size="10" maxlength="10" name="f_hasta" id="f_hasta" onblur="esFechaValida(this);" onchange="esFechaValida(this);" onKeyUp="mascara(this,'/',patron,true);" value="<?=$_POST['f_hasta']?>"/><?=link_calendario('f_hasta');?>
                    <br><b> El Informe toma en cuenta si el Usuario participó en la carga/verificación de las fichas<b> </br>
             
    	     
    	  </td>
         </tr>
    	 <tr>
    	 <td align=center>

    	 </td>
    	 </tr>
    </table>
</form>
<!-- Formulario de bÃºsqueda -->




<!-- Estructuras del menu tabs -->


<div id="page-wrap">


    <div id="tabData">

        <ul class="nav">
            <li class="nav-one"><a href="#planSumar" class="current">Plan Sumar</a></li>
            <li class="nav-two"><a href="#remediarRedes">Remediar+Redes</a></li>
            
        </ul>

        <div class="list-wrap">

            <ul id="planSumar">
                <li>
                    <table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
                        <tr>
                            <td><div id="fichasLeyenda" align="left"></div></td>
                            <td><div id="fichasAltas" align="center"></div></td>
                            <td><div id="fichasMod" align="center"></div></td>
                            <td><div id="fichasTotales" align="center"></div></td>
                            
                        </tr>
                        
                      <tr>
                        <td align=right  id="mo">Fechas</td>
                        <td align=right  id="mo">Fichas Alta</td>
                        <td align=right  id="mo" >Fichas Modificacion</td>
                        <td align=right  id="mo">Total</td>
                      </tr>
                      
                        <?php
                        if($_POST['buscar']){
                            $bgTcolor = '66CCCC';
                            
                            $inscripcionesAltas = 0;
                            $inscripcionesMod = 0;
                            $inscripcionesDias = 0;
                            for($i=0; $i <= count($InscripcionesData)-1; $i++){

                                ?>
                                <tr>
                                    <td align=center bgcolor="<?=$bgTcolor?>"><?=$InscripcionesData[$i][0] ?></td>
                                    <td align=center bgcolor="<?=$bgTcolor?>"><?=$InscripcionesData[$i][1] ?></td>
                                    <td align=center bgcolor="<?=$bgTcolor?>"><?=$InscripcionesData[$i][2] ?></td>
                                    <td align=center bgcolor="<?=$bgTcolor?>"><?= ($InscripcionesData[$i][2] + $InscripcionesData[$i][1])?></td>

                                </tr>

                                <?php 
                                
                                $inscripcionesAltas += $InscripcionesData[$i][1];
                                $inscripcionesMod += $InscripcionesData[$i][2];
                                $inscripcionesDias +=1;
                                
                                if ($bgTcolor == '66CCCC'){
                                    $bgTcolor = 'CCFFFF';
                                }else{
                                    $bgTcolor = '66CCCC';
                                }
                            }
                        
                        ?>
                            
                        <script type="text/javascript">
                            document.getElementById('fichasLeyenda').innerHTML= '<b>Resumen de Fichas: (<?php echo $inscripcionesDias; ?> dias, aprox <?php echo (round(($inscripcionesMod + $inscripcionesAltas) /$inscripcionesDias)); ?> por dia)</b>';
                            document.getElementById('fichasAltas').innerHTML= '<b>Altas: <?php echo $inscripcionesAltas; ?></b>';
                            document.getElementById('fichasMod').innerHTML= '<b>Modificadas: <?php echo $inscripcionesMod; ?></b>';
                            document.getElementById('fichasTotales').innerHTML= '<b>Totales: <?php echo ($inscripcionesMod + $inscripcionesAltas) ; ?></b>';
                        </script>
                                
                        <?php
                        }
                        ?> 
                      
                      
                    </table>
                    <br />

                </li>
            </ul>





            <ul id="remediarRedes" class="hide">
                <li> 

                    <table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
                        <tr>
                            <td><div id="remediarCantidadFichas" align="left"></div></td>
                        </tr>
                        
                  </table>

                    <table width="95%">
                        <tr style="text-align:center; background-color:rgba(69, 74, 168, 0.270588);">
                            <td colspan="5"><b>Datos Detallados por Operador</b></td>
                        </tr>
                        <tr id="mo">
                            <td>Operador</td>
                            <td>Fecha</td>
                            <td>Beneficiario</td>
                            <td>Promotor</td>
                            <td>Efector</td>
                        </tr>

                    <?php
                        if ($_POST['buscar']) {
                            $total = 0;
                            if (count($operadores)>0) {
                            
                                foreach ($operadores as $operador)
                                    {
                                        for ($i=0; $i < $operador->getCargasCantidadLoop(); $i++) { 
                                            ?>
                                            <tr bgcolor="<?=$trBgColor?>">
                                                <td><?=$operador->getNombreCompleto()?></td>
                                                <td><?=$operador->getCargasFechas($i)?></td>
                                                <td><?=(($operador->getCargasBeneficiariosNombreCompleto($i)." (".$operador->getCargasBeneficiariosDocumentos($i).")"))?></td>
                                                <td><?=($operador->getCargasAgentesNombreCompleto($i))?></td>
                                                <td><?=($operador->getCargasEfectorNombre($i)." - ".$operador->getCargasEfectorCodremediar($i))?></td>
                                            </tr>
                                            <?php
                                        }

                                        #   Cambia el color de la fila
                                        if ($trBgColor == "#F5FAFA") {
                                            $trBgColor = "#C1DAD6";
                                        }else{  
                                            $trBgColor = "#F5FAFA";   
                                        }
                                }
                            }
                            else{
                                    ?>
                                    <tr bgcolor="<?=$trBgColor?>">
                                        <td colspan="5"><b>Al parecer usted no realiz&oacute; cargas para Remediar+Redes durante este per&iacute;odo</b></td>
                                    </tr>
                                <?php
                            }
                        }
                    ?>  
                    </table>

                    <script type="text/javascript">
                        document.getElementById('fichasLeyenda').innerHTML= '<b>Cantidad de fichas: (<?php echo $operador->getCargasCantidad(); ?></b>';
                    </script>


                </li>
            </ul>

        </div>
             
    </div>

</div>









<?echo fin_pagina();// aca termino ?>