/* 
 * Funciones js de proposito general
 */


/* 
 solo habilita teclas numericas
 ej de uso: onkeypress="return _soloNumeros(event);"
*/
function _soloNumeros(evt){
    // NOTE: Backspace = 8, Enter = 13, '0' = 48, '9' = 57
    // coma = 44, punto = 46
    var nav4 = !window.event ? true : false;
    var key = nav4 ? evt.which : evt.keyCode;
    return (key <= 13 || key <= 43 || (key >= 48 && key <= 57));
}


/*
 deshabilita la tecla enter
 ej de uso: onkeypress="return deshabilitar_enter(event)"
*/
function deshabilitar_enter(e) {
    tecla=(document.all) ? e.keyCode : e.which;
    if(tecla==13) return false;
}


/*
 convierte un obj js (array, obj, etc)
 a formato json para enviarlo a un php
 utilizacion en php: $var = json_decode(stripslashes($_POST[json_recibido]));
*/
function jsObj2phpObj(object){
    var json = '{';
    for(property in object){
            var value = object[property];
            if(typeof(value) == 'string'){
                    json += '"' + property + '":"' + value + '",'
            } else {
                    if(!value[0]){
                            json += '"' + property + '":' + jsObj2phpObj(value) + ',';
                    } else {
                            json += '"' + property + '":[';
                            for(prop in value) json += '"' +value[prop]+ '],';
                                    json = json.substr(0, json.length-1)+"],";
                    }
            }
    }
    return json.substr(0, json.length-1)+"}";
}
