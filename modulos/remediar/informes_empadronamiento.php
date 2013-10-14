<?php



"Copyright (C) 2013 <Pezzarini Pedro Jose (jose2190@gmail.com)>

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.";






# Configuracion y acceso al sistema
require_once ("../../config.php");
# Zonas Sanitarias

require_once ("../../clases/Utilidades/sqlMaker.php");
require_once ("../../clases/Utilidades/imageRender.php");


# Header HTML desde Configuracion
echo $html_header;


?>

<!-- Importacion de librerias Jquery, Basicas y graficas -->
<script src='../../lib/jquery.min.js' type='text/javascript'></script>
<script src='../../lib/jquery/jquery-ui.js' type='text/javascript'></script>
<script src='../../lib/jquery/ui/jquery.ui.datepicker-es.js' type='text/javascript'></script>
<script src='../../lib/jquery/jquery.jstepper.min.js' type='text/javascript'></script>
<link rel="stylesheet" href="../../lib/jquery/ui/jquery-ui.css" />
<!--  -->

<!-- Importacion de librerias Jquery Extras -->
<script type="text/javascript" src="../../lib/jquery/graficos/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="../../lib/jquery/graficos/plugins/jqplot.barRenderer.min.js"></script>
<script type="text/javascript" src="../../lib/jquery/graficos/plugins/jqplot.pieRenderer.min.js"></script>
<script type="text/javascript" src="../../lib/jquery/graficos/plugins/jqplot.categoryAxisRenderer.min.js"></script>
<script type="text/javascript" src="../../lib/jquery/graficos/plugins/jqplot.pointLabels.min.js"></script>
<script type="text/javascript" src="../../lib/jquery/graficos/plugins/jqplot.cursor.min.js"></script>

<script type="text/javascript" src="../../lib/jquery/graficos/plugins/jqplot.dateAxisRenderer.min.js"></script>
<script type="text/javascript" src="../../lib/jquery/graficos/plugins/jqplot.canvasTextRenderer.min.js"></script>
<script type="text/javascript" src="../../lib/jquery/graficos/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>

<link rel="stylesheet" type="text/css" href="../../lib/jquery/graficos/jquery.jqplot.min.css" />

<!-- FastFrag Json2Html -->
<script src='fastFrag.js' type='text/javascript'></script>
<!--  -->











<script type="text/javascript" charset="utf-8">
    // Var for all plots, not the best way
    // but time is short.
    var plotAreas = null;
    var plotZonas = null;
    var plotEfectores = null;
    var lastElementClicked = null;
    
</script>


<!-- <div align="center" id="mo"><h2>Empadronamiento</h2></div> -->
<br>
<div align="center" ><img src="../../imagenes/logo_remediar.png" alt=""></div>
<br>
<div id="img_load" align="center"></div>
    
    <div width="70%" align="center">
        <fieldset>
            <legend>Busqueda</legend>
            <form action="informes_empadronamiento" method="POST" id="formData">
                <input type="hidden" name="clave" value="loadData">
                <table border="1" id="tablaFiltro">
                    <tr id="mo">
                        <td>Filtro</td>
                        <td>Valores</td>
                        <td>Filtro</td>
                        <td>Buscar</td>
                        <td>Exportar</td>
                    </tr>


                </table>
            </form>
        </fieldset>
    </div>

<div id="divname" style="position:absolute;left:10%;width:80%;">


    <div id="tabs">
    <ul>
    <li><a href="#tabs-1">Zona Sanitaria</a></li>
    <li><a href="#tabs-2">Areas Programaticas</a></li>
    <li><a href="#tabs-3">Efectores</a></li>
    </ul>
    
    <div id="tabs-1">

        <table class="barrasZonas" id="estadisticaZonas" border="none" align="center" style="width:70%;">
            <tr id="mo">
                <td>Zona Numero</td>
                <td>Cantidad</td>
            </tr>
            

        </table>
        
        <br />
        
        <div id="chart1" style="position:relative;left:15%;width:70%" class="chart1"></div>



    </div>

    <div id="tabs-2">
        
        <table class="barrasAreas" id="estadisticaAreas" border="none" align="center" style="width:70%;">
            <tr id="mo">
                <td>Area Numero</td>
                <td>Cantidad</td>
            </tr>
            

        </table>
        
        <br />
        
        <div id="chart2" style="position:relative;left:15%;width:70%" class="chart2"></div>

    </div>




    <div id="tabs-3">
        
        <table class="barrasEfectores" id="estadisticaEfectores" border="none" align="center" style="width:70%;">
            <tr id="mo">
                <td>Efector</td>
                <td>Cantidad</td>
            </tr>
            

        </table>
        
        <br />
        
        <div id="chart3" style="position:relative;left:15%;width:70%;heigth: auto" class="chart2"></div>

    </div>



    </div>

    

</div>








<script type="text/javascript" charset="utf-8">
 

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
        this.div = function(id, content, eventValues){
            var temp = {
                    "type": "div",
                    "id": id,
                    "content": content,
                    "attrs":{}
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

        // Documentacion para metodo radioButton
        this.radioButton = function(id, cssClass, name, value, eventValues){
            var temp = {
                    "id": id,
                    "css": cssClass,
                    "type": "radio",
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


    // Render para elementos HTML
    var render = new htmlRender();
    
    // Agregador de filtros
    function addFilter(){

        // Elements
        var baseFilter = new Array();
        var baseEntry = new Array();
        
        // Table 
        var baseTr = null;
        var baseTd = null;

        render.serializeUpp()
        
        
        var filterSelectOption = [
            render.option("id", "Localidad", 3, {"onclick" : "analizarCampo"}),
            render.option("id", "Fecha Nacimiento Entre", 5, {"onclick" : "analizarCampo"}),
            render.option("id", "Fecha Empadronamiento Entre", 6, {"onclick" : "analizarCampo"}),
            render.option("id", "Area Programatica", 7, {"onclick" : "analizarCampo"}),
            render.option("id", "Zona Sanitaria", 8, {"onclick" : "analizarCampo"}),
            render.option("id", "Efector", 9, {"onclick" : "analizarCampo"}),
            render.option("id", "Enviado", 10, {"onclick" : "analizarCampo"}),
            render.option("id", "Score ", 11, {"onclick" : "analizarCampo"}),
            render.option("id", "Score entre ", 12, {"onclick" : "analizarCampo"})
        ];


        var filterSelect = render.select("filterSelect", "filterSelect[]", {}, filterSelectOption);
        
        filterSelect = render.serializeNode(filterSelect);
        filterSelectOption = render.serializeNodes(filterSelectOption);

        baseFilter.push(filterSelect);

        baseTd = [
                render.td("tdFilterSelect", {"onclick":"void"}, baseFilter),
                render.td("tdFilter", {"onclick":"void"}, baseEntry)
                ];

        baseTd = render.serializeNodes(baseTd);
        baseTr = render.serializeNode(render.tr("trCampo", {"onclick":"void"}, baseTd));

        // Si es la primera vez que se carga la página, se agrega el botón "Buscar", y "Agregar Filtro"
        if (render.firstLoad) {
            render.loaded();
            var baseAddFilter = render.button("btnAddFilter", "Agregar Filtro", {"onclick":"addFilter"});
            var baseSearchFilter = render.button("btnSearch", "Buscar", {"onclick":"cargarFiltros"});
            var baseExportFilter = render.button("btnSearch", "Exportar", {"onclick":"exportCharts"});
            
            baseTd.push(render.td("tdAddFilter",{"onclick":"void"},baseAddFilter));
            baseTd.push(render.td("tdSearch",{"onclick":"void"},baseSearchFilter));
            baseTd.push(render.td("tdExport",{"onclick":"void"},baseExportFilter));
        }
        // Si no es la primera vez, se agrega el botón, "Eliminar Filtro" 
        else{
            var baseRemoveFilter = render.button("btnRemoveFilter", "Eliminar Filtro", {"onclick":"removeFilter"});
            render.serializeNode(baseRemoveFilter)
            baseTd.push(render.td("tdRemoveFilter",{"onclick":"void"},baseRemoveFilter));
        };
        
        render.pushExternalField("tablaFiltro", render.createElement(baseTr));
    }


    // Removedor de filtros
    function removeFilter(ui)
    {   
        var element = render.getId(ui.id);
        element = 'trCampo-'+element;
        $(document.getElementById(element)).remove();

    }
    

    // Analizador de cambios para generar los campos
    function analizarCampo(ui){
        var element = render.getId(ui.id);
        var elementValue = ui.value;
        var guiElements = new Array();

        
        // Cambiar por una estructura CASE
        switch(elementValue) {
            case "1":
                    guiElements.push(render.inputText("inputText", "filtroValor","filtroValor[DNI][]","",{}));
                    guiElements.push(render.hidden("hidden","hidden[DNI][]","",{}));
                break;
            
            case "2":
                    guiElements.push(render.inputText("inputText", "filtroValor","filtroValor[Apellido][]","",{}));
                    guiElements.push(render.hidden("hidden","hidden[Apellido][]","",{}));
                break;
            
            case "3":
                    guiElements.push(render.select("select", "filtroValor[localidad][]", {}, resultToOption("localidades", ("select-" + element))));
                    guiElements.push(render.hidden("hidden","hidden[localidad][]","",{}));
                break;
            
            case "4":
                    guiElements.push(render.inputText("filtroValorDate", "dateFilter","filtroValor[FechaNacimiento][]","",{}));
                    guiElements.push(render.hidden("filtroValor","hidden[FechaNacimiento][]","",{}));
                break;
            
            case "5":
                    guiElements.push(render.inputText("filtroValorDate", "dateFilter","filtroValor[fechaNacimiento][]","",{}));
                    guiElements.push(render.inputText("filtroValorDateOpt", "dateFilterOpt","filtroValor[fechaNacimiento][]","",{}));
                break;
            
            case "6":
                    guiElements.push(render.inputText("filtroValorDate", "dateFilter","filtroValor[fechaempadronamientoEntre][]","",{}));
                    guiElements.push(render.inputText("filtroValorDateOpt", "dateFilterOpt","filtroValor[fechaempadronamientoEntre][]","",{}));
                break;
            
            case "7":
                    guiElements.push(render.select("select", "filtroValor[areaProgramatica][]", {}, resultToOption("areasprogramaticas", ("select-" + element))));
                    guiElements.push(render.hidden("hidden","hidden[areaProgramatica][]","",{}));
                break;
            
            case "8":
                    guiElements.push(render.select("select", "filtroValor[nroZona][]", {}, resultToOption("zonassantinarias", ("select-" + element))));
                    guiElements.push(render.hidden("hidden","hidden[nroZona][]","",{}));
                break;
            
            case "9":
                    guiElements.push(render.select("select", "filtroValor[efector][]", {}, resultToOption("efectores", ("select-" + element))));
                    guiElements.push(render.hidden("hidden","hidden[efector][]","",{}));
                break;
            
            case "10":
                    guiElements.push(render.select("select", "filtroValor[enviado][]", {}, resultToOption("estadoenvio", ("select-" + element))));
                    guiElements.push(render.hidden("hidden","hidden[enviado][]","",{}));
                break;
            
            case "11":
                    guiElements.push(render.inputText("spinner", "spinner","filtroValor[scoreIgual][]","",{}));
                    guiElements.push(render.hidden("hidden","hidden[scoreIgual][]","",{}));
                break;

            case "12":
                    guiElements.push(render.inputText("spinner", "spinner","filtroValor[ScoreEntre][]","",{}));
                    guiElements.push(render.inputText("spinnerOpt", "spinner","filtroValor[ScoreEntre][]","",{}));
                break;
            
        };

        var replacement = "tdFilter-" + element;

        for (var i = 0; i < guiElements.length; i++) {
            guiElements[i] = render.serializeWith(element,guiElements[i]);
        };
       
        render.replace(replacement, render.createElement(guiElements));

        $(function () {
            $(".dateFilter").datepicker();
            $(".dateFilterOpt").datepicker();
            $(".spinner").jStepper(
                {
                    minValue:0, 
                    maxValue:26, 
                    minLength:1,
                    allowDecimals: true
                });

            
        });
    }

    // Doc para funcion resultToOption
    function resultToOption(clave, id)
    {


        $.post("informes_empadronamiento_funciones.php", {"clave":clave, "elementId": id}, function (data){
            var response = JSON.parse(data);
            var options = new Array();
            for (var i = 0; i < response.length - 1; i++) {
                options.push(render.option("id", response[i]["name"], response[i]["value"], {"onclick" : "void"}));
                
            };
            var element = response[(response.length -1)]["element"];
            
            //console.log(element);

            render.pushExternalField(element, render.createElement(options));
        });

        return([]);
    }
    


    // Primera carga (agrega un filtro)
    $(document).ready(function() {
        addFilter();
        graficarZonas(plotZonas, null, null, "chart1");
        graficarAreas(plotAreas, null, null, "chart2");
        graficarEfectores(plotEfectores, null, "chart3");


        graficarTabs();
        replotTab();
    })

</script>

<script>
function graficarAreas(storageVar,values, datas, place){
    
    // Bar Charts
    values = values || [0];
    datas = datas || ["Consulta vacia"];
    place = place;

    
    // Clean divs
    $("#"+place).empty();

    // Max Value to show in chart
    var MaxGraph = Math.max.apply(Math, values);

    // Max value in percent, to set scale in chart
    MaxGraph += MaxGraph/10;
    
    $(function(){
        $.jqplot.config.enablePlugins = true;
        var s1 = values ;
        var ticks = datas;
        plotAreas = $.jqplot(place, [s1], {
            // Only animate if we're not using excanvas (not in IE 7 or IE 8)..
            animate: !$.jqplot.use_excanvas,
            seriesDefaults:{
                renderer:$.jqplot.BarRenderer,
                pointLabels: { show: true }
            },
            axes: {
                xaxis: {
                    
                    renderer: $.jqplot.CategoryAxisRenderer,
                    ticks: ticks
                },
                yaxis: {
                    max : MaxGraph
                }
            },
            highlighter: { show: false }
        });
        console.log(storageVar);


     
    });
 
}


function graficarZonas(storageVar,values, datas, place){
    
    // Bar Charts
    values = values || [0];
    datas = datas || ["Consulta vacia"];
    place = place;

    
    // Clean divs
    $("#"+place).empty();

    // Max Value to show in chart
    var MaxGraph = Math.max.apply(Math, values);

    // Max value in percent, to set scale in chart
    MaxGraph += MaxGraph/10;
    
    $(function(){
        $.jqplot.config.enablePlugins = true;
        var s1 = values ;
        var ticks = datas;
        plotZonas = $.jqplot(place, [s1], {
            // Only animate if we're not using excanvas (not in IE 7 or IE 8)..
            animate: !$.jqplot.use_excanvas,
            seriesDefaults:{
                renderer:$.jqplot.BarRenderer,
                pointLabels: { show: true }
            },
            axes: {
                xaxis: {
                    
                    renderer: $.jqplot.CategoryAxisRenderer,
                    ticks: ticks
                },
                yaxis: {
                    max : MaxGraph
                }
            },
            highlighter: { show: false }
        });
        console.log(storageVar);


     
    });
 
}



function graficarEfectores(storageVar, dataPlot, place){
var line1 = [['Cup Holder Pinion Bob', 700], ['Generic Fog Lamp', 9], ['HDTV Receiver', 15],
['81 Track Control Module', 12], [' Sludg5e Pump Fourier Modulator', 3],
['T3ranscender/Spice Rack', 6], ['Hai4r Spray Danger Indicator', 18],
['Cu3p Holder Pinion Bob', 7], ['Gen32eric Fog Lamp', 9], ['HDTV Receiver', 15],
['84 Track Control Module', 12], [' Sl4udge Pump Fourier Modulator', 3],
['T5ranscender/Spice Rack', 6], ['Hai4r Spray Danger Indicator', 18],
['C6up Holder Pinion Bob', 7], ['Gene4ric Fog Lamp', 9], ['HDTV Receiver', 15],
['8 Tr6ack Control Module', 12], [' Slud4ge Pump Fourier Modulator', 3],
['Trans6cender/Spice Rack', 6], ['Hair S4pray rDanger Indicator', 18],
['Trans6cender/Spice Rack', 6], ['Hair S4prayew Danger Indicator', 18],
['Trans6ceender/Spiwce Rack', 6], ['Hair weS4pray Danger Indicator', 18],
['Trans6cenderr/Spice Rack', 6], ['Hair S4pray Dawernger Indicator', 18]
];

var plot1 = $.jqplot('chart3', [line1], {
  title: 'Concern vs. Occurrance',
  series:[{renderer:$.jqplot.BarRenderer}],
  axesDefaults: {
      tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
      tickOptions: {
        angle: -30,
        fontSize: '10pt'
      }
  },
  axes: {
    xaxis: {
      renderer: $.jqplot.CategoryAxisRenderer
    }
  }
});
      
   
}





function graficarTabs(){
    // Tabs
    $(function() {
        $( "#tabs" ).tabs();

    });
}

function replotTab(){
    $('#tabs').bind('tabsactivate', function(event, ui) {  
        plotAreas.replot();
        plotZonas.replot();
        plotEfectores.replot();
      
    });
}

function exportCharts(ui){
    // if (render.isSubmited()) {
    //     var chart1x = $('#chart1').jqplotToImageStr({});
    //     var chart2x = $('#chart2').jqplotToImageStr({});
    //     var chart3x = $('#chart3').jqplotToImageStr({});



    //     my_form=document.createElement('FORM');
    //     my_form.name='myForm';
    //     my_form.method='POST';
    //     my_form.action='informe_empadronamiento_pdf.php';
    //     my_form.target='_blank';
    //     my_form.id = "exportChartForm";


    //     my_tb=document.createElement('INPUT');
    //     my_tb.type='TEXT';
    //     my_tb.name='zonas[chart]';
    //     my_tb.value=chart1x;
    //     my_form.appendChild(my_tb);

    //     my_tb=document.createElement('INPUT');
    //     my_tb.type='TEXT';
    //     my_tb.name='zonas[data]';
    //     my_tb.value= JSON.stringify({'values':valuesZonas, 'labels':labelsZonas});
    //     my_form.appendChild(my_tb);






    //     my_tb=document.createElement('INPUT');
    //     my_tb.type='TEXT';
    //     my_tb.name='areas[chart]';
    //     my_tb.value= chart2x;
    //     my_form.appendChild(my_tb);

    //     my_tb=document.createElement('INPUT');
    //     my_tb.type='TEXT';
    //     my_tb.name='areas[data]';
    //     my_tb.value=JSON.stringify({'values':valuesAreas, 'labels':labelsAreas});
    //     my_form.appendChild(my_tb);




    //     my_tb=document.createElement('INPUT');
    //     my_tb.type='TEXT';
    //     my_tb.name='efectores[chart]';
    //     my_tb.value=chart3x;
    //     my_form.appendChild(my_tb);

    //     my_tb=document.createElement('INPUT');
    //     my_tb.type='TEXT';
    //     my_tb.name='efectores[data]';
    //     my_tb.value=JSON.stringify({'values':valuesEfectores, 'labels':labelsEfectores});
    //     my_form.appendChild(my_tb);



    //     document.body.appendChild(my_form);
    //     my_form.submit();
    //     $('#exportChartForm').remove();


    // }else{
    //     alert("No se puede exportar hasta generar un informe");
    // };

    jqplotToImg("chart3");
    
}


function jqplotToImg(objId) {
    // first we draw an image with all the chart components
    var newCanvas = document.createElement("canvas");
    newCanvas.width = $("#" + objId).width();
    newCanvas.height = $("#" + objId).height();
    var baseOffset = $("#" + objId).offset();

    $("#" + objId).children().each(
    function() {
    // for the div's with the X and Y axis
    if ($(this)[0].tagName.toLowerCase() == 'div') {
    // X axis is built with canvas
    $(this).children("canvas").each(
    function() {
    var offset = $(this).offset();
    newCanvas.getContext("2d").drawImage(this,
    offset.left - baseOffset.left,
    offset.top - baseOffset.top);
    });
    // Y axis got div inside, so we get the text and draw it on
    // the canvas
    $(this).children("div").each(
    function() {
    var offset = $(this).offset();
    var context = newCanvas.getContext("2d");
    context.font = $(this).css('font-style') + " "
    + $(this).css('font-size') + " "
    + $(this).css('font-family');
    context.fillText($(this).html(), offset.left
    - baseOffset.left, offset.top
    - baseOffset.top + 10);
    });
    }
    // all other canvas from the chart
    else if ($(this)[0].tagName.toLowerCase() == 'canvas') {
    var offset = $(this).offset();
    newCanvas.getContext("2d").drawImage(this,
    offset.left - baseOffset.left,
    offset.top - baseOffset.top);
    }
    });

    // add the point labels
    $("#" + objId).children(".jqplot-point-label").each(
    function() {
    var offset = $(this).offset();
    var context = newCanvas.getContext("2d");
    context.font = $(this).css('font-style') + " "
    + $(this).css('font-size') + " "
    + $(this).css('font-family');
    context.fillText($(this).html(), offset.left - baseOffset.left,
    offset.top - baseOffset.top + 10);
    });

    // add the rectangles
    $("#" + objId + " *").children(".jqplot-table-legend-swatch").each(
    function() {
    var offset = $(this).offset();
    var context = newCanvas.getContext("2d");
    context.setFillColor($(this).css('background-color'));
    context.fillRect(offset.left - baseOffset.left, offset.top
    - baseOffset.top, 15, 15);
    });

    // add the legend
    $("#" + objId + " *").children(".jqplot-table-legend td:last-child").each(
    function() {
    var offset = $(this).offset();
    var context = newCanvas.getContext("2d");
    context.font = $(this).css('font-style') + " "
    + $(this).css('font-size') + " "
    + $(this).css('font-family');
    context.fillText($(this).html(), offset.left - baseOffset.left,
    offset.top - baseOffset.top + 15);
    });

window.open(newCanvas.toDataURL(), "directories=no");
}




function cargarFiltros(ui){
    
    
    // Move to init tab
    var index = $("#tabs>div").index($("#tabs-1"));
    $("#tabs").tabs("select", index);

    var imgPaht = "../../imagenes/wait.gif";
    $('#img_load').empty();
    $('<img src="' + imgPaht + '">').width(90).height(20).appendTo('#img_load');

    $.post("informes_empadronamiento_funciones.php",$('#formData').serialize(),function(res){
        

        //alert(res);
        $("#chart1").empty();
        $("#chart2").empty();
        $("#chart3").empty();
        $("#estadisticaZonas").empty();
        $("#estadisticaAreas").empty();
        $("#estadisticaEfectores").empty();
        alert(res);
        // var response = JSON.parse(res);
        // //console.log(response);
        
        // trsZonas = new Array();
        // tdsZonas = new Array();
        // valuesZonas = new Array();
        // labelsZonas = new Array();

        // trsAreas = new Array();
        // tdsAreas = new Array();
        // valuesAreas = new Array();
        // labelsAreas = new Array();

        // trsEfectores = new Array();
        // tdsEfectores = new Array();
        // valuesEfectores = new Array();
        // labelsEfectores = new Array();
        

        // tdsZonas = render.tdMany(["Zona Numero", "Cantidad"], {}, "mo");
        // trsZonas.push(render.tr("",{}, tdsZonas));

        // tdsAreas = render.tdMany(["Area Numero", "Cantidad"], {}, "mo");
        // trsAreas.push(render.tr("",{}, tdsAreas));

        // tdsEfectores = render.tdMany(["Efector", "Cantidad"], {}, "mo");
        // trsEfectores.push(render.tr("",{}, tdsEfectores));


        // for (var i = 0; i < response["Zonas"].length; i++) {
        //     var tdsZonas = new Array();
        //     tdsZonas = render.tdMany(response["Zonas"][i], {}, null);
        //     trsZonas.push(render.tr("",{}, tdsZonas));
        //     valuesZonas.push(response["Zonas"][i][1]);
        //     labelsZonas.push(response["Zonas"][i][0]);
        // };


        // for (var i = 0; i < response["Areas"].length; i++) {
        //     var tdsAreas = new Array();
        //     tdsAreas = render.tdMany(response["Areas"][i], {}, null);
        //     trsAreas.push(render.tr("",{}, tdsAreas));
        //     valuesAreas.push(response["Areas"][i][1]);
        //     labelsAreas.push("Area "+response["Areas"][i][0]);
        // };

        // for (var i = 0; i < response["Efectores"].length; i++) {
        //     var tdsEfectores = new Array();
        //     tdsEfectores = render.tdMany(response["Efectores"][i], {}, null);
        //     trsEfectores.push(render.tr("",{}, tdsEfectores));
        //     valuesEfectores.push(response["Efectores"][i]);
        //     labelsEfectores.push("");
            
        // };



        
        // render.pushExternalField("estadisticaZonas", render.createElement(trsZonas));
        // render.pushExternalField("estadisticaAreas", render.createElement(trsAreas));
        // render.pushExternalField("estadisticaEfectores", render.createElement(trsEfectores));

        
        // graficarZonas(plotAreas,valuesZonas, labelsZonas, "chart1");
        // graficarAreas(plotZonas, valuesAreas, labelsAreas, "chart2");
        // graficarEfectores(plotEfectores, response["Efectores"], "chart3");
        // $('#img_load').empty();
        // render.submited();
        //$('jqplot-table-legend').css("border", "1px solid red");

        //replotTab();

    });

}


</script>








<?php
echo $html_footer;
?>