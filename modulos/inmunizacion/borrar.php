<?php  
    require_once ("../../config.php"); 
    require_once ("clases/Archivot.php");
    
    $resultado=  Archivot::borrarArchivo($_GET[id_archivo]);
    $resultado2=  Prestacion::borrarPrestacionesAceptadas($_GET[id_archivo]);
    $resultado3=  Prestacion::borrarPrestacionesRechazadas($_GET[id_archivo]);
    if(isset($_GET['id_cierre'])) {
    	Archivot::borrarLiquidacion($_GET[id_archivo], $_GET[id_cierre], $_GET[id_liquidacion]);
    }
    
?>
<script>xx('<?php echo $_GET[cuie];?>')</script>
