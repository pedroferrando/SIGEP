<?php 

# Configuracion y acceso al sistema
require_once ("../../config.php");
# Clase remediar/promotores.php
require_once ("../../clases/remediar/promotores.php");
# Clase Efector.php
require_once ("../../clases/Efector.php");
# Clase remediar/efectores.php
require_once ("../../clases/remediar/efectores.php");
# Clase remediar/remediar.php
require_once ("../../clases/remediar/remediar.php");
# Clase remediar/trazadoras/clasificacion3.php
require_once ("../../clases/remediar/trazadoras/clasificacion3.php");
# Clase BeneficiariosUad.php
require_once ("../../clases/BeneficiariosUad.php");


$beneficiario = array("nombre" => "Benef, Sumar", "fechanac" =>"12/12/1992", "documento" => "333333352", "sexo" => "mujer");

$beneficiarioBase = new BeneficiarioUad();
$beneficiarioBase->Automata("clave_beneficiario = '".$parametros["clave_beneficiario"]."'");

$beneficiario["sexo"] = $beneficiarioBase->getSexoFormal();
$beneficiario["nombre"] = $beneficiarioBase->getNombreCompletoFormal();
$beneficiario["documento"] = $beneficiarioBase->getNumeroDoc();
$beneficiario["fechanac"] = fecha($beneficiarioBase->getFechaNacimientoBenef());



# Manejo del beneficiario
/* Se utiliza para verificar si un beneficiario fué empadronado o clasificado anteriormente. */
$empadronamiento = new Empadronamiento();
$empadronamiento->Automata("clavebeneficiario = '".$parametros["clave_beneficiario"]."'");

$clasificacionAntigua = new Clasificacion1();
$clasificacionAntigua->Automata("clave = '".$parametros["clave_beneficiario"]."'");

$clasificacionMedia = new Clasificacion2();
$clasificacionMedia->Automata("clave_beneficiario = '".$parametros["clave_beneficiario"]."'");

$clasificacionNueva = new Clasificacion3();
$clasificacionNueva->Automata("clavebeneficiario = '".$parametros["clave_beneficiario"]."'");

# Promotores
$promotores = new PromotoresColeccion();
$promotores->Automata();

# Efectores de Remediar
$efectores = new EfectorRemediarColeccion();
$efectores->Automata();

#print_r($parametros);
echo $html_header;


?>


<!-- Importacion de librerias Jquery, Basicas y graficas -->
<script src='../../lib/jquery.min.js' type='text/javascript'></script>
<script src='../../lib/jquery/jquery-ui.js' type='text/javascript'></script>
<script src='../../lib/jquery/ui/jquery.ui.datepicker-es.js' type='text/javascript'></script>
<link rel="stylesheet" href="../../lib/jquery/ui/jquery-ui.css" />
<!--  -->

<script src='../../lib/jquery/fastFrag.js' type='text/javascript'></script>



<script type="text/javascript">
    // Datos Básicos
    
    datos = {
        "nombre": '<?php echo $beneficiario["nombre"]; ?>', 
        "documento":'<?php echo $beneficiario["documento"]; ?>',
        "claveBeneficiario" : '<?php echo $parametros["clave_beneficiario"]; ?>',
        "usuarioCarga" : '<?php echo $_ses_user["id"]; ?>'
    };

    // Datos obtenidos
    edadFicha = null;
    presionDiasistolica = null;

    requeridos = {
        "fechas" : {"nacimiento": '<?php echo $beneficiario["fechanac"]; ?>', "prestacion": null},
        "edad" : null,
        "diabetes" : "sinDiabetes",
        "fumador" :"noFumador",
        "presionSistolica": null,
        "sexo" : '<?php echo $beneficiario["sexo"]; ?>',
        "promotor" : null,
        "efector" : null,
        "riesgoBeneficiario": null
    }

    // Antecedentes
    antecedentes = {
        "empadronado" : <?php if($empadronamiento->enPadron()){echo "true";}else{echo "false";} ?> ,
        "fechaEmpadronamiento": '<?php echo $empadronamiento->getFechaempadronamiento(); ?>' ,
        "fechaCarga": '<?php echo $empadronamiento->getFecha_carga(); ?>' ,
        "nroFormulario": '<?php echo $empadronamiento->getNroformulario(); ?>' ,
        "promotor": '<?php echo $empadronamiento->formularioGetAgente(); ?>' ,
        "efectorEmpadronamiento": '<?php echo $empadronamiento->efectorGetNombreefector(); ?>' , 
        "clasificado" : <?php if ($clasificacionAntigua->enPadron() or $clasificacionMedia->enPadron() or $clasificacionNueva->enPadron()) {echo "true";}else{echo "false";} ?> 
    };

    
    // Encuestas
    encuestaGeneral = {"familiarDiabetes" : null, "perimetroAbdominal" : null};
    encuestaMujeres = {"glucemiaEnEmbarazo" : null, "hijoSobrepeso" : null};

    // Elementos de GUI
    lastElementClicked = null;
    

    opciones = {
        "hombre":{
            
            "diabetes":{
                "fumador":{
                    "70":{"180":"E","160": "E","140":"E","120":"C"},
                    "60":{"180":"E","160":"E","140":"B","120":"C"},
                    "50":{"180":"E","160":"E","140":"B","120":"A"},
                    "40":{"180":"E","160":"D","140":"B","120":"A"},
                },
                
                "noFumador":{
                    "70":{"180":"E","160":"E","140":"C","120":"B"},
                    "60":{"180":"E","160":"D","140":"B","120":"A"},
                    "50":{"180":"E","160":"D","140":"A","120":"A"},
                    "40":{"180":"E","160":"B","140":"A","120":"A"},
                }
            },



            "sinDiabetes":{
                "fumador":{
                    "70":{"180":"E","160":"D","140":"C","120":"B"},
                    "60":{"180":"E","160":"D","140":"B","120":"A"},
                    "50":{"180":"E","160":"C","140":"A","120":"A"},
                    "40":{"180":"E","160":"B","140":"A","120":"A"},
                },
                
                "noFumador":{
                    "70":{"180":"E","160":"C","140":"B","120":"A"},
                    "60":{"180":"E","160":"B","140":"A","120":"A"},
                    "50":{"180":"D","160":"B","140":"A","120":"A"},
                    "40":{"180":"D","160":"A","140":"A","120":"A"},
                }
            }


        },



        "mujer":{
            
            "diabetes":{
                "fumador":{
                    "70":{"180":"E","160": "E","140":"D","120":"C"},
                    "60":{"180":"E","160":"E","140":"B","120":"A"},
                    "50":{"180":"E","160":"E","140":"B","120":"A"},
                    "40":{"180":"E","160":"D","140":"B","120":"A"},
                },
                
                "noFumador":{
                    "70":{"180":"E","160":"D","140":"C","120":"B"},
                    "60":{"180":"E","160":"D","140":"B","120":"A"},
                    "50":{"180":"E","160":"D","140":"B","120":"A"},
                    "40":{"180":"E","160":"B","140":"A","120":"A"},
                }
            },



            "sinDiabetes":{
                "fumador":{
                    "70":{"180":"E","160":"C","140":"B","120":"B"},
                    "60":{"180":"E","160":"C","140":"A","120":"A"},
                    "50":{"180":"E","160":"C","140":"A","120":"A"},
                    "40":{"180":"E","160":"B","140":"A","120":"A"},
                },
                
                "noFumador":{
                    "70":{"180":"D","160":"B","140":"B","120":"A"},
                    "60":{"180":"D","160":"B","140":"A","120":"A"},
                    "50":{"180":"D","160":"B","140":"A","120":"A"},
                    "40":{"180":"D","160":"A","140":"A","120":"A"},
                }
            }


        }
    }
</script>



<script type="text/javascript">


function EventFechaEmpadronamientoSelect(dateText, instance){
    requeridos["fechas"]["prestacion"] = $(this).datepicker({ dateFormat: 'dd/mm/yyyy' }).val();
    requeridos["edad"] = calcularEdad(requeridos["fechas"]["nacimiento"], requeridos["fechas"]["prestacion"]);
    //alert(edad);
    edadFicha = convertirEdad(requeridos["edad"]);
    $(".edadReal").html(requeridos["edad"] + " a&ntilde;os");
    $(".rangoEnFicha").html(edadFicha + " a&ntilde;os");
    obtenerRiesgos(null);
}

function reiniciarForm(){
    $('#diabetes').removeAttr("checked");
    $('#fumador').removeAttr("checked");
}


function calcularEdad(fechaNacimiento, fechaPrestacion) {

    var oneYear=(1000 * 60 * 60 * 24 * 365); 
    var x=fechaNacimiento.split("/");     
    var y=fechaPrestacion.split("/");

    var nacimiento=new Date(x[2],(x[1]-1),x[0]);
    var prestacion=new Date(y[2],(y[1]-1),y[0])

    var nacimientoMes=x[1];
    var prestacionMes=y[1];
    // console.log(prestacion.getTime());
    // console.log(nacimiento.getTime());
    // console.log((prestacion.getTime()-nacimiento.getTime())/(oneYear));
    //console.log();
    // console.log((prestacion.getTime()-nacimiento.getTime())/(oneYear));
    return(Math.ceil((prestacion.getTime()-nacimiento.getTime())/(oneYear))); 

}

// Designa diabetes a la variable Global
function designarDiabetes(uielement){
	if (uielement.checked) {requeridos["diabetes"] = "diabetes"} else{requeridos["diabetes"] = "sinDiabetes"};
    obtenerRiesgos(null);
}

// Designa el fumador a la variable Global
function designarFumador(uielement){
	if (uielement.checked) {requeridos["fumador"] = "fumador"} else{requeridos["fumador"] = "noFumador"};
    //alert(calcularEdad('1999-05-02', '2013-02-01'));
    obtenerRiesgos(null);
}

function glowElement(uielement){

    if (lastElementClicked == uielement) {

    } else{
        
        $(lastElementClicked).css('color','#000000');
        $(lastElementClicked).css('font-weight', 'normal');
        $(lastElementClicked).html(""); 

        lastElementClicked = uielement;
        
        $(uielement).css('color','#ffffff');
        $(uielement).css('font-weight', 'bold');
        $(uielement).html("X");
        $("#panelAnalisisRiesgo").slideUp("fast", mostrarRiesgos);
        $("#panelAnalisisRiesgo").slideDown("slow");    
    };
    
    requeridos["presionSistolica"] = ($(uielement).attr("xdata"));

    
}

function mostrarRiesgos(data){
    var riesgosDetalle = {
        "A":{"label":"Riesgo <10%", "leyenda":"Riesgo bajo. Cambios del modo de vida y evaluar anualmente el riesgo.", "color":"rgb(128,255,0, 0.5)"},
        "B":{"label":"Riesgo 10% a 20%", "leyenda":"Riesgo moderado. Evaluar y actualizar el riesgo cada 6 a 12 meses.", "color":"rgb(255,191,0, 0.5)"},
        "C":{"label":"Riesgo 20% a 30%", "leyenda":"Riesgo alto. Evaluar y actualizar el riesgo cada 3 a 6 meses.", "color":"rgb(255,64,0, 0.5)"},
        "D":{"label":"Riesgo 30% a <40%", "leyenda":"Riesgo muy alto. Evaluar y actualizar el riesgo en cada visita.", "color":"rgb(255,0,0, 0.5)"},
        "E":{"label":"Riesgo >=40%", "leyenda":"Riesgo muy alto. Evaluar y actualizar el riesgo en cada visita.", "color":"rgb(138,8,8, 0.5)"},
    };
    claveRiesgo = lastElementClicked.id.split("riesgo");
    requeridos["riesgoBeneficiario"] = claveRiesgo[1];
    labelRiesgo = riesgosDetalle[claveRiesgo[1]]["label"];
    leyendaRiesgo = riesgosDetalle[claveRiesgo[1]]["leyenda"];
    colorRiesgo = riesgosDetalle[claveRiesgo[1]]["color"];
    
    $("#panelAnalisisRiesgo").css("background-color", colorRiesgo);
    //$("#descripcionRiesgo").css("background-color", colorRiesgo);

    $("#catalogoRiesgo").html("<h3>" + labelRiesgo + "</h3>");
    $("#descripcionRiesgo").html(leyendaRiesgo);
}

// Obtiene los riesgos
function obtenerRiesgos(uielement){
	//var riesgo = opciones[sexo][diabetes][fumador]["40"]["180"]
	//console.log(opciones[sexo][diabetes][fumador]["40"]["180"]);
	//$(".opctionesTablaCabecera").attr("id","riesgo"+riesgo);
    lastElementClicked = null;
    requeridos["presionSistolica"] = null;

	$(".TablaRiesgos").empty();

	
	var riesgos = opciones[requeridos["sexo"]][requeridos["diabetes"]][requeridos["fumador"]][edadFicha];
	var trs = new Array();
	var render = new htmlRender();
	var tds = new Array();

    var keys = [];
    var k = null;
    var i; 
    var len;

    for (k in riesgos)
    {
        if (riesgos.hasOwnProperty(k))
        {
            keys.push(k);
        }
    }

    keys.sort();
    len = keys.length;

    // tds.push(render.td("tdFilterSelect", {"onclick":"void"}, render.option("opctionesTabla", "Riesgos", 3, {"onclick" : "void"})));
    // tds.push(render.td("tdFilterSelect", {"onclick":"void"}, render.option("opctionesTabla", "Ta Sist", 3, {"onclick" : "void"})));
    tds.push(render.td("tdFilterSelect", {"onclick":"void"}, render.div("opctionesTabla", "Riesgos", {"onclick" : "void"}, null)));
    tds.push(render.td("tdFilterSelect", {"onclick":"void"}, render.div("opctionesTabla", "Ta Sist", {"onclick" : "void"}, null)));

    trs.push(render.tr("trCampo", {"onclick":"void"}, tds));
    render.pushExternalField("TablaRiesgos", render.createElement(trs));

    for (i = len -1; i > -1; i--)
    {
        k = keys[i];
        tds = [];
        trs = []

        //alert(k + '--' + riesgos[k]);
        // tds.push(render.td("tdFilterSelect", {"onclick":"void"}, render.option("riesgo"+riesgos[k], "", 3, {"onclick" : "glowElement"})));
        // tds.push(render.td("tdFilterSelect", {"onclick":"void"}, render.option("", k, 3, {"onclick" : "void"})));

        
        tds.push(render.td("tdFilterSelect", {"onclick":"void"}, render.div("riesgo"+riesgos[k], "",{"onclick" : "glowElement"}, k)));
        tds.push(render.td("tdFilterSelect", {"onclick":"void"}, render.div("", k,  {"onclick" : "void"}, null)));
        trs.push(render.tr("", {"onclick":"void"}, tds));
        
        render.pushExternalField("TablaRiesgos", render.createElement(trs));
    }

    reformularEncuesta(null);
    $("#panelAnalisisRiesgo").slideUp("fast");

}



// Convierte sexo a los formatos necesarios
function convertirSexo(sexoExterno, presentacion){
	switch(sexoExterno.toLowerCase()){

		case "f":
			sexoExterno = "mujer";
			break;

		case "mujer":
			break;

		case "femenino":
			sexoExterno = "mujer";
			break;



		case "hombre":
			sexoExterno = "hombre";
			break;

		case "masculino":
			sexoExterno = "hombre";
			break;

		case "m":
			sexoExterno = "hombre";
			break;

	}
    if (presentacion) {
        if (sexoExterno == "hombre") 
            {sexoExterno = "Masculino"} 
        else
            {sexoExterno = "Femenino"};    
    };
    

	return(sexoExterno);
}



// Rendedizador HTML dinámico
function htmlRender(){
    this.elementId = 0;
    this.serialChar = "-";
    this.firstLoad = true;
    this.submitedYet = false;

    // Doc para funcion addElement
    this.serializeMany = function (element)
    {
        for (var i = 0; i <= element.length - 1; i++) {
            element[i]["id"] = element[i]["id"] + this.serialChar + this.elementId;
            this.elementId += 1;
   
        };
        return(element);
    };

    // Doc para funcion addElement
    this.serialize = function (element)
    {        
        element["id"] = element["id"] + this.serialChar + this.elementId;
        this.elementId += 1;
        return(element);
    };

    // Documentacion para metodo serializeMatch
    this.serializeMatch = function(element){
        this.elementId += 1;
        for (var i = 0; i <= element.length - 1; i++) {
            element[i]["id"] = element[i]["id"] + this.serialChar + this.elementId;   
        };
        return(element);
    };

    // Documentacion para metodo serializeNodes
    this.serializeNodes = function(element){
        for (var i = 0; i <= element.length - 1; i++) {
            element[i]["id"] = element[i]["id"] + this.serialChar + this.elementId;   
        };
        return(element);
    };

    // Documentacion para metodo serializeNode
    this.serializeNode = function(element){
        element["id"] = element["id"] + this.serialChar + this.elementId;   
        return(element);
    };

    // Documentacion para metodo serializeWith
    this.serializesWith = function(id, element){
        for (var i = 0; i <= element.length - 1; i++) {
            element[i]["id"] = element[i]["id"] + this.serialChar + id;
            //console.log((element[i]["id"] + this.serialChar + id));
        };
        return(element);
    };

        // Documentacion para metodo serializeWith
    this.serializeWith = function(id, element){
        element["id"] = element["id"] + this.serialChar + id;   
        return(element);
    };

    // Documentacion para metodo serializeUpp
    this.serializeUpp = function(){
        this.elementId += 1;
    };
    
    // Documentacion para metodo option
    this.option = function(id, content, value, eventValues){
        var temp = {
            "id": id,
            "type": "option",
            "content": content,
            "attrs":{"value":value}
        };

       for(var prop in eventValues){
            temp["attrs"][prop] = eventValues[prop]+"(this);";
        };

        return(temp);
    };

    // Documentacion para metodo option
    this.span = function(id, content, eventValues){
        var temp = {
            "id": id,
            "type": "span",
            "content": content,
            "attrs":{"value":content}
        };

       for(var prop in eventValues){
            temp["attrs"][prop] = eventValues[prop]+"(this);";
        };

        return(temp);
    };

    // Documentacion para metodo select
    this.select = function(id, name, eventValues, optionContent){
        var temp = {
                "type": "select",
                "id": id,
                "attrs":{
                    "name":name
                    },
                "content": optionContent 
                };
        
        for(var prop in eventValues){
            temp["attrs"][prop] = eventValues[prop]+"(this);";
        };


        return(temp);
    };

    // Documentacion para metodo div
    this.div = function(id, content, eventValues, extraData){
        var temp = {
                "type": "div",
                "id": id,
                "content": content,
                "attrs":{
                    "xdata":extraData,
                }
        };

        for(var prop in eventValues){
            temp["attrs"][prop] = eventValues[prop]+"(this);";
        };

        return(temp);
    };


    // Documentacion para metodo inputText
    this.inputText = function(id, cssClass, name, value, eventValues){
        var temp = {
                "id": id,
                "css": cssClass,
                "type": "input",
                "attrs":{
                        "name":name,
                        "value": value
                    }
                };

        for(var prop in eventValues){
            temp["attrs"][prop] = eventValues[prop]+"(this);";
        };

        return(temp);
    };


    // Documentacion para metodo tr
    this.tr = function(id, eventValues, content){
        var temp = {
                "id": id,
                "type": "tr",
                "content": content,
                "attrs":{}
                };

        for(var prop in eventValues){
            temp["attrs"][prop] = eventValues[prop]+"(this);";
        };

        return(temp);
    };

    // Documentacion para metodo checkbox
    this.checkbox = function(id, eventValues, name, content){
        var temp = {
                "id": id,
                "type": "input",
                "content": content,
                "attrs":{
                        "type": "checkbox",
                        "name":name,
                        "value":content,
                    }
                };

        for(var prop in eventValues){
            temp["attrs"][prop] = eventValues[prop]+"(this);";
        };

        return(temp);
    };

    // Documentacion para metodo checkbox
    this.radio = function(id, eventValues, name, content, extraData){
        var temp = {
                "id": id,
                "type": "input",
                "content": content,
                "attrs":{
                        "type": "radio",
                        "name":name,
                        "value":content,
                        "xdata":extraData,
                    }
                };

        for(var prop in eventValues){
            temp["attrs"][prop] = eventValues[prop]+"(this);";
        };

        return(temp);
    };

    // Documentacion para metodo tr
    this.td = function(id, eventValues, content){
        var temp = {
                "id": id,
                "type": "td",
                "content": content,
                "attrs":{}
                };

        for(var prop in eventValues){
            temp["attrs"][prop] = eventValues[prop]+"(this);";
        };

        return(temp);
    };

    // Documentacion para metodo tdMany
    this.tdMany = function(elements, eventValues, id){
        var temp = new Array();
        for (var i = 0; i < elements.length; i++) {
            temp.push(this.td(id, eventValues, elements[i]));
        };
        return(temp);
    };
  

    // Doc para funcion createElement
    this.createElement = function (dictElement)
    {
        return(fastFrag.create(dictElement));
    };

    // Doc para funcion addField
    this.pushExternalField = function (stack, element)
    {
        document.getElementById(stack).appendChild(element);
        
    };

    // Documentacion para metodo replace
    this.replace = function(stack, element){
        var upperElement = document.getElementById(stack);
        var childs = upperElement.childNodes;
        if (childs.length < 1) {
            this.pushExternalField(stack, element);
        } else{
            while (upperElement.firstChild) {
                //console.log(upperElement.firstChild);
                upperElement.removeChild(upperElement.firstChild);

            };
            this.pushExternalField(stack, element);    
        };
              
    };

    // Documentacion para metodo getId
    this.getId = function(stringValue){
        var elementId = stringValue.split(this.serialChar);
        return(elementId[(elementId.length -1)]);
    };

    // Documentacion para metodo loaded
    this.loaded = function(){
        this.firstLoad = false;
    };

    // Documentacion para metodo submitted
    this.submited = function(){
        this.submitedYet = true;
    };

    // Documentacion para metodo submitted
    this.isSubmited = function(){
        return(this.submitedYet);
    };        

    // Documentacion para metodo button
    this.button = function(id, value, eventValues){
        var temp = {
                "id": id,
                "type": "input",
                "attrs":{
                    "type":"button",
                    "value":value
                    }
                };

        for(var prop in eventValues){
            temp["attrs"][prop] = eventValues[prop]+"(this);";
        };

        return(temp);
    };

    // Documentacion para metodo button
    this.submitButton = function(id, value, eventValues){
        var temp = {
                "id": id,
                "type": "button",
                "content":value,
                "attrs":{}
                };

        for(var prop in eventValues){
            temp["attrs"][prop] = eventValues[prop]+"(this);";
        };

        return(temp);
    };

    // Documentacion para metodo hidden
    this.hidden = function(id, name, value){
        var temp = {
            "id": id,
            "type": "hidden",
            "content":value,
            "attrs":{
                "name":name,
                "value":value
            }
        };
        return(temp);
    };

}




function convertirEdad(edadReal){
    var edadRango = null;

    switch(true){

        case (edadReal < 50):
            edadRango = 40;
            break;

        case ((edadReal > 49) && (edadReal < 60)):
            edadRango = 50;
            break;

        case ((edadReal > 59) && (edadReal < 70)):
            edadRango = 60;
            break;



        case ((edadReal > 69)):
            edadRango = 70
            break;
    }

    return(edadRango);

}


function analisisEncuesta(uielement){
    var valor = $(uielement).attr("xdata");
    var campo = $(uielement).val();

    switch (encuestaGeneral[String(campo)]) {
        case undefined:
            alert("Error al comparar datos con estructuras");
            break;

        case null:
            encuestaGeneral[String(campo)] = valor;
            break;

        default:
            encuestaGeneral[String(campo)] = valor;
            break;        

    }
}


function analisisEncuestaMujeres(uielement){
    var valor = $(uielement).attr("xdata");
    var campo = $(uielement).val();

    switch (window.encuestaMujeres[String(campo)]) {
        case undefined:
            alert("Error al comparar datos con estructuras");
            break;

        case null:
            window.encuestaMujeres[String(campo)] = valor;
            break;

        default:
            window.encuestaMujeres[String(campo)] = valor;
            break;        

    }
}




function reformularEncuesta(uiElemenft){

    var diabetesFamiliarTd = new Array();
    var diabetesFamiliarTr = new Array();
    var diabetesFamiliarText = new Array();
    var diabetesFamiliarOptions = new Array();


    var medicionAbdominalPreguntas = new Array();
    var medicionAbdominalTr = new Array();
    var medicionAbdominalTd = new Array();
    var medicionAbdominalText = new Array();
    var medicionAbdominalOptions = new Array();


    var CuestionarioMujeresTR = new Array();
    var CuestionarioMujeresTD = new Array();
    var CuestionarioMujeresText = new Array();
    var CuestionarioMujeresOptions = new Array();
    var CuestionarioMujeresPreguntas = new Array();


    var render = new htmlRender();
    
    $('#CuestionarioSoloMujeres').empty();
    $('#CuestionarioMedicionAbdominal').empty();
    $('#CuestionarioDiabetesFamiliar').empty();
    

    // Diabetes familiar
    diabetesFamiliarText.push(render.span("spanText", "\xBFTiene algun familiar con diabetes?", {"onclick":"void"}));
    diabetesFamiliarText.push(render.span("spanText", "Si", {"onclick":"void"}));
    diabetesFamiliarText.push(render.span("spanText", "No", {"onclick":"void"}));
    diabetesFamiliarText.push(render.span("spanText", "NSNC", {"onclick":"void"}));

    diabetesFamiliarOptions.push(render.radio("id", {"onclick":"analisisEncuesta"}, "familiarDiabetes", "familiarDiabetes", "SI"));    
    diabetesFamiliarOptions.push(render.radio("id", {"onclick":"analisisEncuesta"}, "familiarDiabetes", "familiarDiabetes", "NO"));
    diabetesFamiliarOptions.push(render.radio("id", {"onclick":"analisisEncuesta"}, "familiarDiabetes", "familiarDiabetes", "NSNC"));

    diabetesFamiliarTd.push(render.td("textTd", {}, diabetesFamiliarText[0]));
    diabetesFamiliarTd.push(render.td("optionTd", {}, [diabetesFamiliarText[1], diabetesFamiliarOptions[0]]));
    diabetesFamiliarTd.push(render.td("optionTd", {}, [diabetesFamiliarText[2], diabetesFamiliarOptions[1]]));
    diabetesFamiliarTd.push(render.td("optionTd", {}, [diabetesFamiliarText[3], diabetesFamiliarOptions[2]]));

    diabetesFamiliarTr.push(render.tr("", {}, [diabetesFamiliarTd[0], diabetesFamiliarTd[1], diabetesFamiliarTd[2], diabetesFamiliarTd[3]]));

    $('#CuestionarioDiabetesFamiliar').append(render.createElement(diabetesFamiliarTd));



    // Para medicion abdominal, independientemente del sexo
    medicionAbdominalText.push(render.span("spanText", "Si", {"onclick":"void"}));
    medicionAbdominalText.push(render.span("spanText", "No", {"onclick":"void"}));
    medicionAbdominalText.push(render.span("spanText", "NRNE", {"onclick":"void"}));

    medicionAbdominalOptions.push(render.radio("id", {"onclick":"analisisEncuesta"}, "perimetroAbdominal", "perimetroAbdominal", "SI"));
    medicionAbdominalOptions.push(render.radio("id", {"onclick":"analisisEncuesta"}, "perimetroAbdominal", "perimetroAbdominal", "NO"));
    medicionAbdominalOptions.push(render.radio("id", {"onclick":"analisisEncuesta"}, "perimetroAbdominal", "perimetroAbdominal", "NoRealizado"));




    if(requeridos["sexo"] == "mujer"){

        CuestionarioMujeresPreguntas.push(render.span("spanText", "\xBFDurante el embarazo le dijeron que tenia la glucemia elevada?", {"onclick":"void"}));
        CuestionarioMujeresPreguntas.push(render.span("spanText", "\xBFTuvo algun hijo que al nacer peso mas de 4 gks?", {"onclick":"void"}));

        // OPCIONES 0,1,2
        CuestionarioMujeresOptions.push(render.radio("id", {"onclick":"analisisEncuestaMujeres"}, "glucemiaEnEmbarazo", "glucemiaEnEmbarazo", "SI"));
        CuestionarioMujeresOptions.push(render.radio("id", {"onclick":"analisisEncuestaMujeres"}, "glucemiaEnEmbarazo", "glucemiaEnEmbarazo", "NO"));
        CuestionarioMujeresOptions.push(render.radio("id", {"onclick":"analisisEncuestaMujeres"}, "glucemiaEnEmbarazo", "glucemiaEnEmbarazo", "NSNC"));


        // OPCIONES 3,4,5
        CuestionarioMujeresOptions.push(render.radio("id", {"onclick":"analisisEncuestaMujeres"}, "hijoSobrepeso", "hijoSobrepeso", "SI"));
        CuestionarioMujeresOptions.push(render.radio("id", {"onclick":"analisisEncuestaMujeres"}, "hijoSobrepeso", "hijoSobrepeso", "NO"));
        CuestionarioMujeresOptions.push(render.radio("id", {"onclick":"analisisEncuestaMujeres"}, "hijoSobrepeso", "hijoSobrepeso", "NSNC"));


        // Pregunta yTexto de las opciones
        CuestionarioMujeresText.push(render.span("spanText", "Si", {"onclick":"void"}));
        CuestionarioMujeresText.push(render.span("spanText", "No", {"onclick":"void"}));
        CuestionarioMujeresText.push(render.span("spanText", "NSNC", {"onclick":"void"}));


        CuestionarioMujeresTD.push(render.td("textTd", {}, CuestionarioMujeresPreguntas[0]));;
        CuestionarioMujeresTD.push(render.td("optionTd", {}, [CuestionarioMujeresText[0], CuestionarioMujeresOptions[0]]));
        CuestionarioMujeresTD.push(render.td("optionTd", {}, [CuestionarioMujeresText[1], CuestionarioMujeresOptions[1]]));
        CuestionarioMujeresTD.push(render.td("optionTd", {}, [CuestionarioMujeresText[2], CuestionarioMujeresOptions[2]]));

        CuestionarioMujeresTD.push(render.td("textTd", {}, CuestionarioMujeresPreguntas[1]));
        CuestionarioMujeresTD.push(render.td("optionTd", {}, [CuestionarioMujeresText[0], CuestionarioMujeresOptions[3]]));
        CuestionarioMujeresTD.push(render.td("optionTd", {}, [CuestionarioMujeresText[1], CuestionarioMujeresOptions[4]]));
        CuestionarioMujeresTD.push(render.td("optionTd", {}, [CuestionarioMujeresText[2], CuestionarioMujeresOptions[5]]));

        
        // Primera Pregunta
        CuestionarioMujeresTR.push(render.tr("", {}, [CuestionarioMujeresTD[0], CuestionarioMujeresTD[1], CuestionarioMujeresTD[2], CuestionarioMujeresTD[3]]));

        // Segunda Pregunta
        CuestionarioMujeresTR.push(render.tr("", {}, [CuestionarioMujeresTD[4], CuestionarioMujeresTD[5], CuestionarioMujeresTD[6], CuestionarioMujeresTD[7]]));

        
        medicionAbdominalPreguntas.push(render.span("spanText", "Mujer: mayor o igual a 80 cm", {"onclick":"void"}));
        medicionAbdominalTd.push(render.td("textTd", {}, medicionAbdominalPreguntas[0]));
        medicionAbdominalTd.push(render.td("optionTd", {}, [medicionAbdominalText[0], medicionAbdominalOptions[0]]));
        medicionAbdominalTd.push(render.td("optionTd", {}, [medicionAbdominalText[1], medicionAbdominalOptions[1]]));
        medicionAbdominalTd.push(render.td("optionTd", {}, [medicionAbdominalText[2], medicionAbdominalOptions[2]]));
        medicionAbdominalTr.push(render.tr("", {}, [medicionAbdominalTd[0], medicionAbdominalTd[1], medicionAbdominalTd[2], medicionAbdominalTd[3]]));
        



        $('#CuestionarioSoloMujeres').append(render.createElement(CuestionarioMujeresTR));


    }else{
        
        medicionAbdominalPreguntas.push("Hombre: mayor o igual a 80 cm");
        medicionAbdominalTd.push(render.td("textTd", {}, medicionAbdominalPreguntas[0]));
        medicionAbdominalTd.push(render.td("optionTd", {}, [medicionAbdominalText[0], medicionAbdominalOptions[0]]));
        medicionAbdominalTd.push(render.td("optionTd", {}, [medicionAbdominalText[1], medicionAbdominalOptions[1]]));
        medicionAbdominalTd.push(render.td("optionTd", {}, [medicionAbdominalText[2], medicionAbdominalOptions[2]]));
        medicionAbdominalTr.push(render.tr("", {}, [medicionAbdominalTd[0], medicionAbdominalTd[1], medicionAbdominalTd[2], medicionAbdominalTd[3]]));


    }
    
    $('#CuestionarioMedicionAbdominal').append(render.createElement(medicionAbdominalTr));
    
}

// Asigna el efector elegido
function asignarEfector(uielement){
    requeridos["efector"] = $(uielement).val();
}

// Asigna el promotor elegido
function asignarPromotor(uielement){
    requeridos["promotor"] = $(uielement).val();
}

</script>




<style type="text/css">

 #principal {
	
    color: #ffffff;
	width: 100%;
	margin-left: auto;
	margin-right: auto;
    border-radius: 15px;
 }

#opciones_izquierda {
	float: left;
    background-color: #80FF00;
 }

#opciones_centro {
    background-color: #006699;
	margin-left: 25%;
	margin-right: auto;
	width: auto;
}

#opciones_logo {
    margin-left: auto;
    margin-right: auto;
    
}

#opciones_logo img {

    margin-top: 5px;
    
}

#opciones_derecha {
	float: right;
}



#primera_columna { 
    position: absolute; 
    left: 15%; 
    
    width: 30%; 
    color: black;
    background-color: #BDBDBD;
}

#segunda_columna { 
    position: relative; 
    margin-top: 1%;
    left: 50%; 
    

    width: 35%; 
    color: black;
    background-color: #BDBDBD;
}

#opctionesTabla {
	padding: 0 15px 0 15px;
    margin-left: auto;
    margin-right: auto;
    border: 1px solid;

    background-color: #006699;
    color: #CCCCCC;
    font-weight: bold;
    text-align: center;
}

#TablaRiesgos td {
    border: 1px solid;
    text-align: center;
    border-color: #FFFFFF;
}

#TablaRiesgos {
    width: 100%;
    border-color: #E0E0E0;
    border: 0;
    text-align: center;
    border-collapse: collapse !important;
}

#informacionBeneficiario {
    width: 100%;
    border-color: #E0E0E0;
    border: 0;
    text-align: center;
    border-collapse: collapse !important;
}

#informacionBeneficiario td {
    border: 1px solid;
    text-align: center;
    border-color: #FFFFFF;
}

#opctionesTablaCabecera {
	padding: 0 15px 0 15px;
	text-align: center;
    
}

#opctionesTablaCabeceraNueva {
	padding: 0 15px 0 15px;
	text-align: center;
	background-color: blue;
}


#riesgoA {
	background-color: #80FF00;
	margin-left: auto;
	margin-right: auto;
	text-align: center;
    vertical-align: center;
    width: 30px;
    height: 15px;

}

#riesgoB {
	background-color: #FFBF00;
	margin-left: auto;
	margin-right: auto;
	text-align: center;
    vertical-align: center;
    width: 30px;
    height: 15px;

}

#riesgoC {
	background-color: #FF4000;
	margin-left: auto;
	margin-right: auto;
	text-align: center;
    vertical-align: center;
    width: 30px;
    height: 15px;

}

#riesgoD {
	background-color: #FF0000;
	margin-left: auto;
	margin-right: auto;
	text-align: center;
    vertical-align: center;
    width: 30px;
    height: 15px;

}

#riesgoE {
	background-color: #8A0808;
	margin-left: auto;
	margin-right: auto;
	text-align: center;
    vertical-align: center;
    width: 30px;
    height: 15px;

}

#informacionBeneficiarioText {
    font-weight: bold;
    text-align: left !important;
}

#informacionBeneficiarioData{
    text-align: right !important;   
}

#analisisDeRiesgo {
    position: relative;
    margin-top: 3%;
    width: 70%;
    margin-left: auto;
    margin-right: auto;

    text-align: center;
}

#informacionRiesgo {
    width: 100%;
    border-color: #E0E0E0;
    border: 0;
    text-align: center;
    border-collapse: collapse !important;
}


#break {
    width: auto;
    height: 5px;
    background-color: white !important;
}

#encuestaAnexo {
    position: relative;
    margin-top: 3%;
    width: 70%;
    margin-left: auto;
    margin-right: auto;

    text-align: center;
}

#panelAnalisisRiesgo,#flip
{
    padding:5px;
    text-align:center;
    background-color:#e5eecc;
    border:solid 1px #c3c3c3;
    color:#000000;
}
#panelAnalisisRiesgo
{
    padding:15px;
    display:none;
}

#panelCuestionarioAnexo {
    padding:5px;
    text-align:center;
    background-color:#e5eecc;
    border:solid 1px #c3c3c3;
    color:#000000;
}

#catalogoRiesgo td{
    width: 60%;
    text-align: center;
    vertical-align: center;
}

#catalogoRiesgoTabla{
    width: 100%;
    margin-left: 20%;
}

#descripcionRiesgo td{
    width: 10%;
    text-align: left;
    vertical-align: center;
}

#CuestionarioTable{
    width: 100%;
    vertical-align: middle;
    border: 1px solid !important;

}


#CuestionarioDiabetesFamiliar{
    width: 100%;
    vertical-align: middle;
    text-align: left;
}


#CuestionarioSoloMujeres{
    width: 100%;
    vertical-align: middle;
    text-align: left;
}


#CuestionarioMedicionAbdominal{
    width: 100%;
    vertical-align: middle;
    text-align: left;
}


#spanText{
    text-align: left;
}

#textTd{
    text-align: left;
    width: 60%;
}

#optionTd{
    text-align: right;
}


.edadReal {
    background-color: rgba(60,90,250, 0.4);
}
.rangoEnFicha {
    background-color: rgba(60,90,250, 0.4);
}

#mensajeError {
    width: 70%;
    font-weight: bold;
    margin-left: auto !important;
    margin-right: auto !important;
    vertical-align: middle;
    text-align: left;
    color: #000000;
    background-color: rgba(255, 144, 117, 0.3);
}


#mensajeAdvertencia {
    width: 70%;
    font-weight: bold;
    margin-left: auto !important;
    margin-right: auto !important;
    vertical-align: middle;
    text-align: left;
    color: #000000;
    background-color: rgba(33, 144, 217, 0.3);
}

#btnGuardarDiv {
    width: 70%;
    font-weight: normal;
    margin-top: 15px;
    margin-left: auto !important;
    margin-right: auto !important;
    vertical-align: middle;
    text-align: center;
    color: #000000;
    background-color: rgba(33, 144, 217, 0.3);
}


</style>


	
<div id="opciones_logo" align="center">
        <img src="../../imagenes/logo_remediar.png" alt="Logo Remediar+Redes">
</div>
<div id="mo"><h2>Empadronamiento y Clasificacion sin colesterol</h2></div>
<div id="principal">
|

	
    <div id="primera_columna">
        <table class="" id="informacionBeneficiario">
            <tr id="opctionesTabla">
                <td id="informacionBeneficiarioText">Fecha de Empadronamiento</td>
                <td id="informacionBeneficiarioText"><input type="text" name="fechaEmpadronamiento" id="fechaEmpadronamiento" size="25" value=""></td>
            </tr>
            <tr>
                <td id="informacionBeneficiarioText">Diabetico:</td>
                <td id=""><input type="checkbox" name="diabetes" id="diabetes" onclick="designarDiabetes(this);"></td>

            </tr>
            <tr>
                <td id="informacionBeneficiarioText">Fumador:</td>
                <td id=""><input type="checkbox" name="fumador" id="fumador" onclick="designarFumador(this);"></td>
            </tr>
        </table>
        
        <div id="break">
            
        </div>
	
		<table class="TablaRiesgos" id="TablaRiesgos">
		</table>

    </div>
    
    <div id="segunda_columna">
    	<table id="informacionBeneficiario">
			<tr id="opctionesTabla">
				<td colspan="2" id="opctionesTablaCabecera">Beneficiario</td>
			</tr>

			<tr>
				<td id="informacionBeneficiarioText">Nombre:</td>
				<td id="informacionBeneficiarioData" class="inforBeneficiarioNombre">Beneficiario, Remediar</td>
			</tr>
			
            <tr>
				<td id="informacionBeneficiarioText">Documento:</td>
				<td id="informacionBeneficiarioData" class="inforBeneficiarioDocumento">99999999</td>
			</tr>
            <tr>
                <td id="informacionBeneficiarioText">Fecha nacimiento:</td>
                <td id="informacionBeneficiarioData" class="inforBeneficiarioFechaNac"></td>
            </tr>

            <tr>
                <td id="informacionBeneficiarioText">Sexo:</td>
                <td id="informacionBeneficiarioData" class="inforBeneficiarioSexo"></td>
            </tr>
            
            <tr>
                <td id="informacionBeneficiarioText">Edad:</td>
                <td id="informacionBeneficiarioData" class="edadReal"> -- Esperando fecha de empadronamiento -- </td>
            </tr>

            <tr>
                <td id="informacionBeneficiarioText">Rango en ficha:</td>
                <td id="informacionBeneficiarioData" class="rangoEnFicha"> -- Esperando fecha de empadronamiento -- </td>
            </tr>

			<tr>
				<td id="informacionBeneficiarioText">Efector:</td>
				<td id="informacionBeneficiarioData">
                    <select name="efector" id="efectorInput" onchange="asignarEfector(this);" onblur="asignarEfector(this);">
                        <option id="efectorOption" value="-1" selected>Seleccione</option>
                        <?php 
                            foreach ($efectores->efectores as $efect) {
                                ?>
                                    <option id="efectorOption " value="<?php echo $efect->getRemediar(); ?>"> <?php echo ($efect->getRemediar() . " - " .$efect->getNombreFix(31)); ?> </option>
                                <?php
                            }
                        ?>
                    </select>
                </td>
			</tr>
			
            <tr>
				<td id="informacionBeneficiarioText">Promotor:</td>
				<td id="informacionBeneficiarioData">
                    <select name="promotor" id="promotorInput" onchange="asignarPromotor(this);" onblur="asignarPromotor(this);" >
                        <option id="promotorOption" value="-1" selected >Seleccione</option>
                        <?php 
                            foreach ($promotores->promotores as $prom) {
                                ?>
                                    <option id="promotorOption"  value="<?php echo $prom->getDni(); ?>"> <?php echo $prom->getNombreCompleto(); ?> </option>
                                <?php
                            }
                        ?>
                    </select>
                </td>
			</tr>


		</table>
	</div>
    
    <div id="analisisDeRiesgo">
        <div id="mo">Analisis de Riesgo</div>
        <div id="panelAnalisisRiesgo">
        <table id="catalogoRiesgoTabla">
            <tr>
                <td id="catalogoRiesgo"></td>
                <td id="descripcionRiesgo"></td>
            </tr>
        </table>

        </div>      
        </table>
    </div>

    <div id="encuestaAnexo">
        <div id="mo" class="tituloEncuesta">Cuestionario Anexo</div>
        <div id="panelCuestionarioAnexo">
           
            <table id="CuestionarioDiabetesFamiliar">
            </table>

            <table id="CuestionarioSoloMujeres">
            </table>

            <table id="CuestionarioMedicionAbdominal">
            </table>

        </div>
    </div>

    <div id="btnGuardarDiv">
        <br>
        <input type="button" value="Guardar" onClick="validacionPostInscripcion(this);">
        <p>* Al presionar guardar, esta confirmando que los datos son correctos, fieles a la planilla que esta cargando.</p>
    </div>

    
</div>

</div>



</body>

<script type="text/javascript">
    $( window ).load(function() {
        var options = {onSelect: EventFechaEmpadronamientoSelect};
        
        reiniciarForm();
        obtenerRiesgos();

        $("#fechaEmpadronamiento").datepicker(options);
        $(".inforBeneficiarioFechaNac").html(requeridos["fechas"]["nacimiento"]);
        $(".inforBeneficiarioSexo").html(convertirSexo(requeridos["sexo"],true));
        $(".inforBeneficiarioNombre").html(datos["nombre"]);
        $(".inforBeneficiarioDocumento").html(datos["documento"]);
        
        validacionPreInscripcion();
    });


    function validacionPreInscripcion(){
        var errorMessage = {
            "intro":"Este beneficiario ya fue clasificado anteriormente, por que lo no puede cargarlo en este momento.",
            "registro":"Segun nuestros datos, el beneficiario fue empadronado en ["+antecedentes["efectorEmpadronamiento"]+"]. Usualmente, el empadronamiento y la clasificacion suelen referenciarse al mismo centro de salud, por lo que puede ser aconsejable (si se encuentra en ese centro), dirijirse con el/la coordinadora de promotores o jefa/e de Area, que tiene asignado/a.",
            "mensaje": "De no obtener respuesta, comuniquese con el programa.",
            "despedida": "Email: < remediarmasredes@gmail.com > -- Tel: 0376-4447967",
            "firma" : "Remediar+Redes Misiones -  Ministerio de Salud Publica"
        };


        var atencionMensaje = {
            "intro": "Este beneficiario fue empadronado anteriormente, por lo que este acto se tratara como clasificacion.",
            "registro": "Segun nuestros datos, este beneficiario fue empadronado en ["+antecedentes["efectorEmpadronamiento"]+"], si difiere de los datos actuales, tenga como consejo, escribir detras de la ficha: ''Sistema: Beneficiario empadronado anteriormente en "+antecedentes["efectorEmpadronamiento"]+".'', y dirijirse con el/la coordinadora de promotores o jefa/e de Area, que tiene asignado/a.",
            "despedida" : "Esto es de gran utilidad para el control de fichas."
        }

        if (antecedentes["clasificado"]) {
            $("#principal").empty();

            $("#principal").append("<div id='mensajeError'></div>");
            $("#mensajeError").append("<div align=\"center\"><h1>El beneficiario ya fue clasificado</h1></div>");

            $.each(errorMessage, function(index, val) {
                $("#mensajeError").append("<p id='textoMensaje'>"+val+"</p>");        
            });
            
        }else{
            if (antecedentes["empadronado"]) {
                $("#principal").prepend("<div id='mensajeAdvertencia'></div>");

                $.each(atencionMensaje, function(index, val) {
                    $("#mensajeAdvertencia").append("<p id='textoMensaje'>"+val+"</p>");        
                });


            };
        }
    }


    function validacionPostInscripcion(uielement){
        var validacion = true;
        var camposIncompletos = new Array();

        // Desabilitacion de boton
        $(uielement).attr('disabled', 'disabled');

        if (requeridos["edad"] < 7) {
            validacion = "false";
        };

        // Campos Obligatorios
        $.each(requeridos, function(index, val) {
            if (val == null) {
                camposIncompletos.push(index);
                validacion = false;
            }
        });

        // Encuesta Mujeres
        // if (requeridos["sexo"] == "mujer") {
        //     $.each(encuestaMujeres, function(index, val) {
        //         if (val == null) {
        //             camposIncompletos.push(index);
        //         };
        //     });
        // };


        // Encuesta General
        $.each(encuestaGeneral, function(index, val) {
            if (val == null) {
                camposIncompletos.push(index);
                validacion = false;
            };
        });


        if (validacion) {
            $.post('remediar_admin_nuevo_funciones.php', {"transaccion":"insertar","datos":datos ,"requeridos": requeridos, "encuestaGeneral": encuestaGeneral, "encuestaMujeres":encuestaMujeres}, function(data, textStatus, xhr) {
                var response = jQuery.parseJSON(data);

                var mensajesResponse = new Array();

                mensajesResponse.push("Estado del beneficiario en el Empadronamiento: " + response["beneficiario"]);
                mensajesResponse.push("Estado del beneficiario en la Clasificacion: " + response["clasificacion"]);
                
                mensajesResponse.push("* Resumen beneficiario:");
                mensajesResponse.push("* Sexo: " + requeridos["sexo"]);
                mensajesResponse.push("* Estado de Diabetes: " + requeridos["diabetes"]);
                mensajesResponse.push("* Tabaquismo: " + requeridos["fumador"]);



                $("#principal").empty();
                if(response["transaccion"] > 0){
                    $("#principal").append("<div id='mensajeAdvertencia'></div>");
                    $("#mensajeAdvertencia").append("<div align=\"center\"><h1>Estado de Clasificacion</h1></div>");

                    $.each(mensajesResponse, function(index, val) {
                        $("#mensajeAdvertencia").append("<p id='textoMensaje'>"+val+"</p>");
                    });


                }else{
                    $("#principal").append("<div id='mensajeError'></div>");
                    $("#mensajeError").append("<div align=\"center\"><h1>Estado de Clasificacion</h1></div>");

                    $.each(mensajesResponse, function(index, val) {
                        $("#mensajeError").append("<p id='textoMensaje'>"+val+"</p>");        
                    });

                }    

                console.log(requeridos);
                console.log(antecedentes);
            });
        }else{
            alert("Existen campos incompletos " + camposIncompletos);
        };

    }
</script>

</html>