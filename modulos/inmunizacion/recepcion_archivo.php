<?php
require_once ("../../config.php");
require_once ("../../lib/funciones_misiones.php");
require_once ("inmunizacion_funciones.php");
require_once ("clases/Efector.php");
require_once ("clases/Archivot.php");

try {
//    sql("BEGIN");

    $cmd = $_POST["enviar"];
    if ($cmd == "Enviar") {

            //Controles sobre forma de archivo
        
            if (!$_FILES["archivo"])
                excepcion("Debe seleccionar un archivo.");

            $tamanio = $_FILES["archivo"]["size"];
            if ($tamanio == 0)
                excepcion('El tamaño del archivo es nulo');
            
            
//            

            
            //Controles sobre el nombre del archivo
            $nombre_archivo_completo = explode('.', $_FILES["archivo"]["name"]);
            $nombre_archivo = $nombre_archivo_completo[0];
            $id_archivo_cristian=  substr($nombre_archivo, 7, 3);
            $periodo[]=substr($nombre_archivo, 10, 4);
            $periodo[]=substr($nombre_archivo, 14, 2);
            
            $periodo = implode('/',$periodo);
            
            echo $periodo;
            
            
            if (strlen($nombre_archivo) <> 16) {
                excepcion("Error en longitud de nombre de archivo (Ver nombre de archivo)");
            }
            
            $extension=$nombre_archivo_completo[1];
            if ($extension<>'txt') {
                excepcion("No es un txt (Ver nombre de archivo)");
            }
            
            $cuie=substr($nombre_archivo, 1, 6);
            $efector=Efector::getEfector($cuie);
            //Verificar si existe el efector
            if (!$efector->RecordCount() > 0) {
                excepcion("CUIE de Efector '$cuie' no existe (Ver nombre de archivo)");
            }
            //Verificar si ya se ingreso el archivo antes
            
            $arch=Archivot::getArchivo($nombre_archivo);
            if ($arch->RecordCount() > 0) {
                excepcion("El archivo ya existe en la base");
            }
            //Se copia el archivo en upload
            $tmp = $_FILES['archivo']['tmp_name'];
            $upload_dir = UPLOADS_DIR . '/archivos/' . $_FILES['archivo']['name'];
            if (!move_uploaded_file($tmp, $upload_dir)) {
                excepcion("¡Posible ataque de carga de archivos!");
            }
            
            //Se registra el archivo en la base
//            $id_usuario = $_ses_user['id'];
//          Archivot::setArchivo($nombre_archivo,'', $id_usuario, 1, $cuie, $id_archivo_cristian);
            
            //Se lee el archivo guardado
            $file = fopen(UPLOADS_DIR . "/archivos/" . $_FILES["archivo"]["name"], 'r');            
            
            //
            $mensaje=Archivot::setPrestacionInmu($file,$nombre_archivo_completo,$periodo);

            if($mensaje=="guardado"){
            ?>
                
                <script type="text/javascript">
                    alert("Archivo Guardado" );
                </script>
            <?//Insertar el archivo en la tabla archivo
            }else{
            ?>
                <script type="text/javascript">
                    alert("La version no corresponde" );
                </script>
            <?
            }
        }
} catch (exception $e) {
    echo $e;
    ?>
    <script type="text/javascript">
        alert("Error de Archivo");
    </script>
    <?
}
?>




    
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <script src='../../lib/jquery.min.js' type='text/javascript'></script>    
    <style type="text/css">
            /*<![CDATA[*/
            input.c3 {width:340px
            }
            div.c2 {text-align: center}
            div.c1 {font-weight: bold; text-align: left}
            /*]]>*/
        </style>
        <style type="text/css">
            /*<![CDATA[*/
            td.c1 {padding: 5px;font-size: 14px;}
            /*]]>*/
            
        #mo{
            background-color:powderblue;
            color: #CCCCCC;
            font-weight: bold;
            text-align: center;
        }
        </style>   
    </head>    
    <body>
    <link rel="stylesheet" type="text/css" href="recepcion_archivo.css">         
            
            <form name='recepcion_archivo' action='recepcion_archivo.php' method="post" accept-charset=utf-8 enctype='multipart/form-data' id="recepcion_archivo">
            
                <table width="469" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <br />
                             <input align="center" size="59" type="file" name="archivo" class="c3" />
                            <br />
                            <br />
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            <input class="enviar" type="submit" name="enviar" value="Enviar" size="160px"/>
                        </td>
                    </tr>

                </table> 
            </form>
    </body>
         
</html>
<br>