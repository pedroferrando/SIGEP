<script>
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
            
            console.log(element);

            render.pushExternalField(element, render.createElement(options));
        });

        return([]);
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

            var response = JSON.parse(res);
            console.log(response);
            
            trsZonas = new Array();
            tdsZonas = new Array();
            valuesZonas = new Array();
            labelsZonas = new Array();

            trsAreas = new Array();
            tdsAreas = new Array();
            valuesAreas = new Array();
            labelsAreas = new Array();

            trsEfectores = new Array();
            tdsEfectores = new Array();
            valuesEfectores = new Array();
            labelsEfectores = new Array();
            

            tdsZonas = render.tdMany(["Zona Numero", "Cantidad"], {}, "mo");
            trsZonas.push(render.tr("",{}, tdsZonas));

            tdsAreas = render.tdMany(["Area Numero", "Cantidad"], {}, "mo");
            trsAreas.push(render.tr("",{}, tdsAreas));

            tdsEfectores = render.tdMany(["Efector", "Cantidad"], {}, "mo");
            trsEfectores.push(render.tr("",{}, tdsEfectores));


            for (var i = 0; i < response["Zonas"].length; i++) {
                var tdsZonas = new Array();
                tdsZonas = render.tdMany(response["Zonas"][i], {}, null);
                trsZonas.push(render.tr("",{}, tdsZonas));
                valuesZonas.push(response["Zonas"][i][1]);
                labelsZonas.push(response["Zonas"][i][0]);
            };


            for (var i = 0; i < response["Areas"].length; i++) {
                var tdsAreas = new Array();
                tdsAreas = render.tdMany(response["Areas"][i], {}, null);
                trsAreas.push(render.tr("",{}, tdsAreas));
                valuesAreas.push(response["Areas"][i][1]);
                labelsAreas.push("Area "+response["Areas"][i][0]);
            };

            for (var i = 0; i < response["Efectores"].length; i++) {
                var tdsEfectores = new Array();
                tdsEfectores = render.tdMany(response["Efectores"][i], {}, null);
                trsEfectores.push(render.tr("",{}, tdsEfectores));
                valuesEfectores.push(response["Efectores"][i]);
                labelsEfectores.push("");
                
            };



            
            render.pushExternalField("estadisticaZonas", render.createElement(trsZonas));
            render.pushExternalField("estadisticaAreas", render.createElement(trsAreas));
            render.pushExternalField("estadisticaEfectores", render.createElement(trsEfectores));

            regraficarBarras(valuesZonas, labelsZonas, "chart1");
            regraficarBarras(valuesAreas, labelsAreas, "chart2");
            graficarPie(response["Efectores"]);

            $('#img_load').empty();
            render.submited();
            //$('jqplot-table-legend').css("border", "1px solid red");



        });

    }

</script>