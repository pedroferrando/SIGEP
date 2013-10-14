function correr_pagina(page){
    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: "accion=paginar_prestaciones&"+$('#frm_lst_prestaciones').serialize()+"&page="+page,
        beforeSend: function(data){
            $('#loading').show();
            $('#ma a').hide();
        },
        success: function(data){
            $('#loading').hide();
            $('#ma a').show();
            $('#listado_prestaciones').html(data);
        }
    });
}

function mostrar_datos_beneficiario(nombre, clave){
    var cont;
    cont  = "<b>Beneficiario:</b> "+nombre+"&nbsp;";
    cont += "<b>Clave:</b> "+clave+"&nbsp;";
    cont += "<b>Fecha Nac.:</b>";
    $('#datos_beneficiario').html(cont);
}

function select_trazadora(chk,id_prest,trz,fecha_prest,nomenc,cuie){
    idx = $(chk).val();
    if(chk.checked){
        html = '';
        html += '<p id="'+idx+'">';
        html +=     '<input type="hidden" name="prestaciones[]" value="'+idx+'"/>';
        html +=     '<input type="hidden" id="prestacion_'+idx+'" value="'+id_prest+'"/>';
        html +=     '<input type="hidden" id="trazadora_'+idx+'" value="'+trz+'"/>';
        html +=     '<input type="hidden" id="fecha_prestacion_'+idx+'" value="'+fecha_prest+'"/>';
        html +=     '<input type="hidden" id="cod_nomenclador_'+idx+'" value="'+nomenc+'"/>';
        html +=     '<input type="hidden" id="cuie_'+idx+'" value="'+cuie+'"/>';
        html += '</p>';
        $('#params').append(html);
    }else{
        $('#params p#'+idx).remove();
    }
}

function ver_detalle_trazadoras(){
    //checkboxes = $('.chk_detalle input[type=checkbox]:checked');
    checkboxes = new Array();
    $('#params input[name="prestaciones[]"]').each(function(){
            checkboxes.push($(this).val());
    });
    var aux = 0;
    var id = "";
    if(checkboxes.length>0){
        var myArray = new Array();
        for (var x=0; x < checkboxes.length; x++) {
            //id = checkboxes[x].value; --> value se usa solo con checkboxes
            id = checkboxes[x];
            var registro = {
                trazadora: $('#trazadora_'+id).val(),
                fecha_prestacion: $('#fecha_prestacion_'+id).val(),
                cod_nomenclador: $('#cod_nomenclador_'+id).val(),
                id_prestacion: $('#prestacion_'+id).val(),
                cuie: $('#cuie_'+id).val(),
                nro_doc: $('#nro_doc').val()
            };   
            aux = aux + 1;
            myArray.push(registro);
        }

        var res = jsObj2phpObj(myArray);
            $.ajax({
                type: "POST",
                url: "ajax.php",
                data: "accion=mostrar_detalle_prestaciones"+"&nro_doc="+$('#nro_doc').val()+"&json="+res,
                beforeSend: function(data){
                    $('#loading2').show();
                    $('#ver_detalle_trazadoras').hide();
                },
                success: function(data){
                    $('#loading2').hide();
                    $('#result').html(data);
                    $("html, body").animate({scrollTop: $(document).height()}, "slow");
                    $('#ver_detalle_trazadoras').show();
                        //actualizar link de impresion
                                    $.ajax({
                                        type: "POST",
                                        url: "ajax.php",
                                        data: "accion=get_link_impresion_prestaciones_doms&"+$('#frm_lst_prestaciones').serialize()+"&json="+res,
                                        success: function(data){
                                            //actualizar link de impresion
                                            $('#lnk_reporte_prestaciones_doms').attr("href", data);
                                        }
                                    });
                }
            });
            /*
            llamada alternativa
                $.post('ajax.php', {json: res}, function(data, textStatus, xhr) {
                    $('#result').html(data);
                });
            */
        //console.log(res);
    }else{
        alert('Seleccione la/s prestaciones cuyo detalle desea ver');
    }
}