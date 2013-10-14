<?php

require_once ("../../config.php");


function isBusqueda(){
    $rta = False;
    if ($_POST['buscar']){
        $rta = True;
    }
    return($rta);
}


echo $html_header;

?>
<script type="text/javascript" src="../../lib/jquery-1.7.2.min.js"> </script>
<script>
//Validar Fechas
function esFechaValida(fecha){
    if (fecha != undefined && fecha.value != "" ){
        if (!/^\d{2}\/\d{2}\/\d{4}$/.test(fecha.value)){
            alert("Formato de fecha no valido (dd/mm/aaaa)");
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
 
function comprobarSiBisisesto(anio){
if ( ( anio % 100 != 0) && ((anio % 4 == 0) || (anio % 400 == 0))) {
    return true;
    }
else {
    return false;
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

 function validar_formulario(){
    FDesde = document.getElementById("f_desde").value;
    FHasta = document.getElementById("f_hasta").value;
    
    if (FDesde.length < 10)
    {   
        var fdesde = $("#f_desde_text");
        fdesde.css({'background':'#FF6633'});
        alert("Debe ingresar un rango de fecha para realizar una busqueda" );
        return false;
  
    } 
    else {
        var container = $("#mensaje");
        container.html("Procesando peticion");
        container.css({'color':'black','font-size':'3em', 'background':'#CCE6FF'});
        
        return true;
    }
  
 
}

</script>




<?
echo fin_pagina(); // aca termino
?>

