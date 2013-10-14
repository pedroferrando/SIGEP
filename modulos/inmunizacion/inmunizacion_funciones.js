function cerrar_inmunizaciones(){
    var cant = $('#frm_lst_prest_inmu input[name="prestacion_inmu[]"]:checked').length;
    if(cant>0){
        $.ajax({
            type: "POST",
            url: "ajax_inmunizacion.php",
            data: "accion=cerrar_inmunizaciones&"+$('#frm_lst_prest_inmu').serialize(),
            beforeSend: function(data){
                $('#loading').show();
                $('button[type=button]').hide();
            },
            success: function(data){
                $('#loading').hide();
                $('button[type=button]').show();
                if(data==true){
                    $('#frm_lst_prest_inmu input[name="prestacion_inmu[]"]:checked').closest('tr').empty();
                    alert('Se han liquidado las inmunizaciones seleccionadas');
                }else{
                    alert('Se ha producido un error. Intente nuevamente');
                }
                //$('#periodosh').prepend(data);
            }
        });
    }else{
        alert("Seleccione la/s inmunizaciones a cerrar");
    }
}

function mostrar_filtros_cierre_inmu(elem){
    var filtro = $(elem).val();
    if(filtro=="anio"){
        $('select[name=periodo]').hide();
        $('select[name=anio]').show();
    }
    if(filtro=="periodo"){
        $('select[name=anio]').hide();
        $('select[name=periodo]').show();
    }
    if(filtro==""){
        $('select[name=periodo]').hide();
        $('select[name=anio]').hide();
    }
}

function select_prestaciones_inmu(elem){
    var chk_prest = $(elem).parent().parent().parent().find('td input[type=checkbox]');
    if(elem.checked){
        chk_prest.attr("checked", true);
    }else{
        chk_prest.attr("checked", false);
    }        
}