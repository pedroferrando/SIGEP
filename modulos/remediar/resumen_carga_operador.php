<?php
#   Carga de configuración
require_once ("../../config.php");

#   Funciones de Operador
require_once ("./resumen_carga_operador_funciones.php");

$operadores = array();
$fechaEmpadronamientoDesde = $_POST['fechaEmpadronamientoDesde'];
$fechaEmpadronamientoHasta = $_POST['fechaEmpadronamientoHasta'];
$filtroFactor = $_POST['filtroFactor'];
if ($_POST['busqueda']) {
    $fechaDesde = Fecha_db($fechaEmpadronamientoDesde);
    $fechaHasta = Fecha_db($fechaEmpadronamientoHasta);
    
    if (strlen($filtroFactor) > 1) {
         $operadoresSql = SqlOperadores($fechaDesde,$fechaHasta,$filtroFactor);
    }
    else{
         $operadoresSql = SqlOperadores($fechaDesde,$fechaHasta);
    }
   
    
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
           
        # Primera Carga de la pagina 
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

    
    for ($i=0; $i < count($operadores); $i++) { 
        $validacionesResult = sql(SqlOperadoresValidaciones($operadores[$i]->getId(), $fechaEmpadronamientoDesde, $fechaEmpadronamientoHasta));
        while (!$validacionesResult->EOF) {
            $operadores[$i]->nuevaValidacion($validacionesResult);
            #echo $validacionesResult->fields['fecha_verificado'];
            $validacionesResult->MoveNext();
        }
    }


}

#   Carga de Todos los operadores del padron de remediar
$operadoresListado = sql(SqlOperadoresListado());

#   Cabecera HTML
echo $html_header;
?>

<!-- Importacion de librerias para interaccion grafica y de funcionamiento en HTML -->
<script src='../../lib/jquery.min.js' type='text/javascript'></script>
<script src='../../lib/jquery/jquery-ui.js' type='text/javascript'></script>
<link rel="stylesheet" href="../../lib/jquery/ui/jquery-ui.css" />
<!-- ############################################################################ -->

<style type="text/css">
    
    #page-wrap { width: 90%; margin: 10px auto; }
    
    #tabData .list-wrap { background: #eee; padding: 10px; margin: 0 0 15px 0; }

    #tabData ul { list-style: none; }
    #tabData ul li a { display: block; border-bottom: 1px solid #666; padding: 4px; color: #666; }
    #tabData ul li a:hover { background: #333; color: white; }
    #tabData ul li:last-child a { border: none; }

    #tabData .nav { overflow: hidden; }
    #tabData .nav li { width: 97px; float: left; margin: 0 10px 0 0; background-color:#006699;
    -webkit-border-radius:6px;
    -moz-border-radius:6px;
    border-radius:6px;}
    #tabData .nav li.last { margin-right: 0; }
    #tabData .nav li a { display: block; padding: 5px; background: #666; color: white; font-size: 10px; text-align: center; border: 0; }

    #tabData li a.current,#tabData li a.current:hover { background-color: #006699 !important; color: white; }
    #tabData .nav li a:hover, #tabData .nav li a:focus { background: #999; background-color:#006699; -webkit-border-radius:6px;-moz-border-radius:6px;border-radius:6px;}

    
</style>


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

<script>
    $(function() {

        //$("#example-one").organicTabs();
        
        $("#tabData").organicTabs({
            "speed": 200
        });

    });
</script>


<body>
    
  <div align="center">
    <div align="center" id="mo"><h2>Resumen de Carga de Operadores - Programa Nacional Remediar + Redes</h2></div>
    <?=$parametros["mensaje"]?>
    <form action="resumen_carga_operador.php" method="post" name="consolaBusqueda">
        <fieldset>
            <legend><h3>Consola de b&uacute;squeda</h3></legend>             
        <table>
            <tr>             
                <td>Filtro:</td>
                <td>
                    <select name="filtroFactor" id="">
                        <option value="0">Todos</option>
                        <?php
                                while (!$operadoresListado->EOF) {
                                    if ($operadoresListado->fields['id_operador'] == $filtroFactor) {
                                        $extradata = "Selected";
                                    }else{
                                        $extradata = "";
                                    }
                            ?>
                                <option value="<?=$operadoresListado->fields['id_operador']?>" <?=$extradata?> >
                                    <?=($operadoresListado->fields['operador_apellido'].", ".$operadoresListado->fields['operador_nombre'])?>
                                </option>
                        <?php
                                $operadoresListado->MoveNext();
                            }
                        ?>

                    </select>
                </td>
                <td></td>
                <td>
                </td>
     
                <td><input type="submit" value="Buscar" onClick="return validar_formulario()"/></td>
                <td width="10%"></td>
                <td><button onclick="window.open('<?=$link ?>')"><img src="../../imagenes/excel.gif" alt="" /></button></td>
                
            </tr>
            <tr>
                <td>F.Empadronamiento: </td>
                <td><input type="text" name="fechaEmpadronamientoDesde" id="fechaEmpadronamientoDesde" value="<?=$fechaEmpadronamientoDesde?>" onblur="esFechaValida(this);" onchange="esFechaValida(this);" onKeyUp="mascara(this,'/',patron,true);"/> <?=$r?><?=link_calendario('fechaEmpadronamientoDesde');?></td>
                <td>hasta: </td>
                <td><input type="text" name="fechaEmpadronamientoHasta" id="fechaEmpadronamientoHasta" value="<?=$fechaEmpadronamientoHasta?>"onblur="esFechaValida(this);" onchange="esFechaValida(this);" onKeyUp="mascara(this,'/',patron,true);"/> <?=$r?><?=link_calendario('fechaEmpadronamientoHasta');?></td>
            </tr>
           
            
        </table>
            
        </fieldset>
        <input type="hidden" name="busqueda" value="busqueda" />
    </form>
</div>
  
    
    <?php
    

    ?>
    
    <br />
    
    <fieldset>
    <legend>Datos de la consulta</legend>
    <div id="page-wrap">


	     <div id="tabData">
					
    		<ul class="nav">
                <li class="nav-one"><a href="#dataGlobal" class="current">Globales</a></li>
                <li class="nav-two"><a href="#dataAgrupado">Agrupados</a></li>
                <li class="nav-three"><a href="#dataDetallado">Detallados</a></li>

            </ul>
    		
    		<div class="list-wrap">
    		
    			<ul id="dataGlobal">
    				<li>
                                        <table width="90%">
                                            <tr style="text-align:center; background-color:rgba(69, 74, 168, 0.270588);">
                                                <td colspan="4"><b>Datos Globales de cargas</b></td>
                                            </tr>
                                            <tr id="mo">
                                                <td>Cargas Totales:</td>
                                                <td>Fechas:</td>
                                                <td>Implicados:</td>
                                            </tr>
                                            
                                            <tr>
                                                <td><?=$operadoresCargas?></td>
                                                <td><?=($fechaEmpadronamientoDesde." - ".$fechaEmpadronamientoHasta)?></td>
                                                <td><?=count($operadores)?> Operadores</td>
                                            </tr>
                                        </table>
                                    
                                    
                                </li>
    			</ul>
        		 
        		 <ul id="dataAgrupado" class="hide">
                                <li> 
                                    <table width="90%">
                                            <tr style="text-align:center; background-color:rgba(69, 74, 168, 0.270588);">
                                                <td colspan="4"><b>Datos Agrupados por Operador</b></td>
                                            </tr>
                                            <tr id="mo">
                                                <td>Nombre</td>
                                                <td>Apellido</td>
                                                <td>Cantidad de Cargas</td>
                                                <td>Cantidad de Validaciones</td>
                                            </tr>
                                            
                                            <?php
                                            #   Color inicial de la fila
                                            $trBgColor == "#F5FAFA";

                                            if ($_POST['busqueda']) {
                                                $total = 0;
                                                foreach ($operadores as $operador)
                                                {
                                                    ?>
                                                    <tr bgcolor="<?=$trBgColor?>">
                                                        <td><?=$operador->getNombre()?></td>
                                                        <td><?=$operador->getApellido()?></td>
                                                        <td><?=$operador->getCargasCantidad()?></td>
                                                        <td><?php echo $operador->getValidacionesCantidad(); ?></td>
                                                    </tr>
                                                    <?php
                                                    #   Cambia el color de la fila
                                                    if ($trBgColor == "#F5FAFA") {
                                                        $trBgColor = "#C1DAD6";
                                                    }else{  
                                                        $trBgColor = "#F5FAFA";   
                                                    }
                                                }
                                            }
                                            ?>

                                            
                                        </table>
                                </li>
        		 </ul>
                    
                    <ul id="dataDetallado" class="hide">
                        <li>
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
                                    #   Color inicial de la fila
                                    $trBgColor == "#F5FAFA";

                                    if ($_POST['busqueda']) {
                                        $total = 0;
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
                                ?>
                            </table>

                        </li>
                    </ul>
        		 
        		 
        		 
    		 </div>
		 
		 </div>

	
	</div>
    
    
    </fieldset>

    <?php
    
    
    ?>
</body>





<?php
#   Pie HTML
echo $html_footer;
?>
