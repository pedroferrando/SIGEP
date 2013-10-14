<?php
    
    $_POST;
                       
    require_once("../../config.php");
    require_once("../../lib/lib.php");    
            
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
    
    
?>