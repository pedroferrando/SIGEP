<?php
require_once ("../../config.php"); 
//require_once ("clases/Vacuna.php");  
//require_once ("clases/Beneficiario.php");  
//require_once ("clases/Prestacion.php"); 
require_once ("clases/Archivot.php");

$res_archivos=  Archivot::getArchivos();  
    
?>

<script src='../../lib/jquery.min.js' type='text/javascript'></script>
<link rel='stylesheet' href='../../lib/jquery/ui/jquery-ui.css'/>
<script src='../../lib/jquery/jquery-ui.js' type='text/javascript'></script>
<script src='../../lib/jquery/ui.multiselect.js' type='text/javascript'></script>

<script>
    $(document).on('ready',function() {

        $('.paginate').live('click', function(){
            $('#content').html('<div class="loading"><img src="imagenes/loading.gif" width="70px" height="70px"/></div>');
            var page = $(this).attr('data');		
            var dataString ="cuie="+$("#selectEfector option:selected").val()+"&filas="+$("#selectFilas option:selected").val()+"&page="+page;

            $.ajax({
            type: "GET",
            url: "pagination.php",
            data: dataString,
            success: function(data) {
                $('#content').fadeIn(1000).html(data);
            }
            });
        });
     
    });  
    function xx(cuie){
        //alert("Id: "+$("#selectEfector option:selected").val()+" | Texto: "+$("#selectEfector option:selected").text());  
            $('#content').html('<div class="loading"><img src="imagenes/loading.gif" width="70px" height="70px"/></div>');
            var dataString="cuie="+cuie+"&filas="+$("#selectFilas option:selected").val();
            $.ajax({
                type: "GET",
                url: "pagination.php",
                data: dataString,
                success: function(data) {
                    $('#content').fadeIn(1000).html(data);
                }
                });
    }   
    function archivo_click(id_archivo){
            
            $('#content').html('<div class="loading"><img src="imagenes/loading.gif" width="70px" height="70px"/></div>');
            var page = $(this).attr('data');		
            var dataString ="id_archivo="+id_archivo+"&cuie="+$("#selectEfector option:selected").val()+"&filas="+$("#selectFilas option:selected").val();

            $.ajax({
            type: "GET",
            url: "aceptadas.php",
            data: dataString,
            success: function(data) {
                $('#content').fadeIn(1000).html(data);
            }
            });     
          
    }
    function paginate_archivo_click(id_Archivo,page){
            $('#content').html('<div class="loading"><img src="imagenes/loading.gif" width="70px" height="70px"/></div>');
            //var page = $(this).attr('data');		
            var dataString ="id_archivo="+id_Archivo+"&page="+page+"&cuie="+$("#selectEfector option:selected").val()+"&filas="+$("#selectFilas option:selected").val();

            $.ajax({
            type: "GET",
            url: "aceptadas.php",
            data: dataString,
            success: function(data) {
                $('#content').fadeIn(1000).html(data);
            }
            }); 
     }  
//     function paginar(id_Archivo,page,destino){
//            $('#content').html('<div class="loading"><img src="imagenes/loading.gif" width="70px" height="70px"/></div>');
//            //var page = $(this).attr('data');		
//            var dataString ="id_archivo="+id_Archivo+"&page="+page+"&cuie="+$("#selectEfector option:selected").val()+"&filas="+$("#selectFilas option:selected").val();
//
//            $.ajax({
//            type: "GET",
//            url: "aceptadas.php",
//            data: dataString,
//            success: function(data) {
//                $('#content').fadeIn(1000).html(data);
//            }
//            }); 
//     }  
     function rechazadas_click(id_archivo){
            
            $('#content').html('<div class="loading"><img src="imagenes/loading.gif" width="70px" height="70px"/></div>');
            var page = $(this).attr('data');		
            var dataString ="id_archivo="+id_archivo+"&cuie="+$("#selectEfector option:selected").val()+"&filas="+$("#selectFilas option:selected").val();

            $.ajax({
            type: "GET",
            url: "rechazadas.php",
            data: dataString,
            success: function(data) {
                $('#content').fadeIn(1000).html(data);
            }
            });     
          
    }
    function paginar_rechazadas(id_Archivo,page){
            $('#content').html('<div class="loading"><img src="imagenes/loading.gif" width="70px" height="70px"/></div>');
            //var page = $(this).attr('data');		
            var dataString ="id_archivo="+id_Archivo+"&page="+page+"&cuie="+$("#selectEfector option:selected").val()+"&filas="+$("#selectFilas option:selected").val();

            $.ajax({
            type: "GET",
            url: "rechazadas.php",
            data: dataString,
            success: function(data) {
                $('#content').fadeIn(1000).html(data);
            }
            }); 
     }  
     function borrar_click(id_archivo,id_cierre,id_liquidacion){
            
            $('#content').html('<div class="loading"><img src="imagenes/loading.gif" width="70px" height="70px"/></div>');
            var page = $(this).attr('data');		
            var dataString ="id_archivo="+id_archivo+"&cuie="+$("#selectEfector option:selected").val()+"&filas="+$("#selectFilas option:selected").val()+"&id_cierre="+id_cierre+"&id_liquidacion="+id_liquidacion;

            $.ajax({
            type: "GET",
            url: "borrar.php",
            data: dataString,
            success: function(data) {
                $('#content').fadeIn(1000).html(data);
            }
            });     
          
    }

</script>

<link rel="stylesheet" type="text/css" href="inmunizacion.css"> 

<div id="superior">
    <div id="busquedaEfector"><?php require('busquedaEfector.php'); ?></div>				
</div>
<div id="central">
    <div id="content"><?php require('pagination.php'); ?></div>				
</div>
