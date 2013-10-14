/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function addFormAltaDebitoRetro(row){
    $('#tr_'+row+' td:first-child').html( $('#cntformAlta #formAltaDebitoRetro').clone() );
    $('#tr_'+row+' td:first-child #formAltaDebitoRetro .sprite-gral.icon-floppy').attr('id_prestacion', row);
    $('#tr_'+row+' td:first-child #formAltaDebitoRetro').show();
}

function checkCamposAltaDebitoRetro(row){
    frm = $('#tr_'+row+' td:first-child form');
    auditoria = frm.find('input[name=dbt_tipo_auditoria]:checked').val();
    motivo = frm.find('select[name=dbt_motivo]').val();
    if(auditoria==undefined || motivo==""){
        return false;
    }else{
       return true; 
    }   
}

function deleteDebitoRetroactivo(elem,id_debito){
    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: "accion=dbtret_delete_debito&id_debito="+id_debito,
        beforeSend: function(data){
            $(elem).hide();
        },
        success: function(data){
            if(data==1){
                tr = $(elem).parent().parent();
                precio = tr.find('label').html();
                tr.fadeOut('slow', function(){tr.remove();});
                cuie = $('.modal input[name=cuie_actual]').val();
                updateTotales(cuie,"delete",precio);
            }else{
                $(elem).show();
                alert("Se ha producido un error. Intente nuevamente.");
            }
        }
    });
}

function saveDebitoRetroactivo(elem){
    id_prest = $(elem).attr('id_prestacion');
    if( checkCamposAltaDebitoRetro(id_prest) ){
        tr = $('#tr_'+id_prest).prev();
        cuie = $('.modal input[name=cuie_actual]').val();
        nro_exp = $('.modal input[name=expte_actual]').val();
        frm = $('#tr_'+id_prest+' td:first-child form');
        totalEfector = $('input[name=total_a_pagar_'+cuie+']').val();
        precio = tr.find('label').html();
        //se controla que el debito no sea > al total del efector
        if(parseFloat(totalEfector)>=parseFloat(precio)){        
            $.ajax({
                type: "POST",
                url: "ajax.php",
                data: "accion=dbtret_save_debito&cuie="+cuie+"&nro_exp="+nro_exp+"&id_prestacion="+id_prest+"&"+frm.serialize(),
                beforeSend: function(data){
                    $(elem).hide();
                },
                success: function(data){
                    if(data==1){
                        tr_frm = $('#tr_'+id_prest);
                        tr_frm.fadeOut('slow', function(){tr_frm.remove();});
                        tr.fadeOut('slow', function(){tr.remove();});
                        updateTotales(cuie,"insert",precio);
                    }else{
                        $(elem).show();
                        alert("Se ha producido un error. Intente nuevamente.");
                    }
                }
            });
        }else{
            alert('No se puede realizar el debito.\nEl monto supera el total a pagar del Efector');
        }
    }else{
        alert('Complete los campos obligatorios por favor.');
    }
}

function showFormDebitoRetro(cuie){
    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: "accion=dbtret_show_busqueda&cuie="+cuie,
        success: function(data){
            $('#position-fixed-top div').empty();
            $('#position-fixed-top #cnt_frm_busqueda').html(data);
            $('#position-fixed-top').show();
        }
    });
}

function showListaDebitoRetro(cuie,expte){
    $('#position-fixed-top div').empty();
    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: "accion=dbtret_get_prest_debitadas&cuie="+cuie+"&nro_exp="+expte,
        success: function(data){
            $('#position-fixed-top #cnt_res_busqueda').html(data);
            $('#position-fixed-top').show();
        }
    });
}

function switchTabDebitoRetro(elem,accion,cuie){
    if(elem!=undefined && elem!=""){
        $(elem).parent().siblings().removeClass("active");
        $(elem).parent().addClass("active");
    }else{
        $('.tabs li').removeClass("active");
        $('.tabs li:first-child').addClass("active");
    }
    if(cuie==undefined || cuie==""){
        cuie = $('.modal input[name=cuie_actual]').val();
    }else{
        $('.modal input[name=cuie_actual]').val(cuie);
    }
    if(accion=="dbt_new"){
        showFormDebitoRetro(cuie);
    }
    if(accion=="dbt_lst"){
        expte = $('.modal input[name=expte_actual]').val();
        showListaDebitoRetro(cuie,expte);
    }
}

function updateTotalDebitoRetroactivoEfector(cuie,accion,monto){
    cnt_dbt_ret = parseInt($('#lbl_cant_dbt_ret_'+cuie).html());
    mnt_dbt_ret = parseFloat($('#lbl_monto_dbt_ret_'+cuie).html());
    debito = parseFloat(monto);
    if(accion=="delete"){
        //se elimino un debito
        cnt_dbt_ret = cnt_dbt_ret - 1;
        mnt_dbt_ret = mnt_dbt_ret - debito;
    }
    if(accion=="insert"){
        //se guardo un debito
        cnt_dbt_ret = cnt_dbt_ret + 1;
        mnt_dbt_ret = mnt_dbt_ret + debito;
    }
    $('#lbl_cant_dbt_ret_'+cuie).html(cnt_dbt_ret);
    $('#lbl_monto_dbt_ret_'+cuie).html(mnt_dbt_ret.toFixed(2));
}

function updateTotalEfector(cuie,accion,monto){  
    x = parseFloat($('input[name=fondos_efector_'+cuie+']').val());
    y = parseFloat($('input[name=fondos_estimulo_'+cuie+']').val());
        
    total_a_pagar = parseFloat($('input[name=total_a_pagar_'+cuie+']').val());
    debito = parseFloat(monto);
    if(accion=="delete"){
        //se elimino un debito, se suma el monto al total
        total_a_pagar = total_a_pagar + debito;
    }else{
        //se guardo un debito, se resta el monto al total
        total_a_pagar = total_a_pagar - debito;
    }
    aux1 = total_a_pagar;
    aux2 = parseInt($('input[name=puntos_'+cuie+']').val());
    if(aux2>0){
        estimulacion = (aux1 * aux2) / 100;
    }else{
        estimulacion = 0;
    }
    fondos_efector  = total_a_pagar - parseFloat(estimulacion.toFixed(3));
    fondos_estimulo = estimulacion;
    
    $('input[name=total_a_pagar_'+cuie+']').val(total_a_pagar);
    $('#lbl_total_a_pagar_'+cuie).html('$ '+total_a_pagar.toFixed(2));
    $('input[name=fondos_efector_'+cuie+']').val(fondos_efector);
    $('#lbl_fondos_efector_'+cuie).html('$ '+ fondos_efector.toFixed(2));
    $('input[name=fondos_estimulo_'+cuie+']').val(fondos_estimulo);
    $('#lbl_fondos_estimulo_'+cuie).html('$ '+fondos_estimulo.toFixed(2));  
    
    
    //update de total efector y fondo estimulo (ultima tabla)
    varx = Math.abs(x - fondos_efector);
    vary = Math.abs(y - fondos_estimulo);
    totalEfectorExpte = parseFloat($('input[name=total_efector]').val());
    totalEstimulacionExpte = parseFloat($('input[name=total_estimulo]').val());
    if(accion=="delete"){
        totalEfectorExpte = totalEfectorExpte + varx;
        totalEstimulacionExpte = totalEstimulacionExpte + parseFloat(vary.toFixed(3));
    }else{
        totalEfectorExpte = totalEfectorExpte - varx;
        totalEstimulacionExpte = totalEstimulacionExpte - parseFloat(vary.toFixed(3));
    }
    $('#lbl_total_efector').html('$ '+totalEfectorExpte.toFixed(2));
    $('input[name=total_efector]').val(totalEfectorExpte);
    $('#lbl_total_estimulo').html('$ '+totalEstimulacionExpte.toFixed(2));
    $('input[name=total_estimulo]').val(totalEstimulacionExpte);
}

function updateTotalExpediente(accion,monto){
    mnt_dbt = parseFloat($('input[name=total_debitos]').val());
    totLiq = parseFloat($('input[name=total_liquidado]').val());
    debito = parseFloat(monto);
    if(accion=="delete"){
        //se elimino un debito, se resta el monto al total de debitos
        mnt_dbt = mnt_dbt - debito;
        //se elimino un debito, se suma el monto al total
        totLiq = totLiq + debito;
    }else{
        //se guardo un debito, se suma el monto al al total de debitos
        mnt_dbt = mnt_dbt + debito;
        //se guardo un debito, se resta el monto al total
        totLiq = totLiq - debito;
    }
    $('#lbl_total_debitos').html('$ '+mnt_dbt.toFixed(2));
    $('input[name=total_debitos]').val(mnt_dbt_ret);
    $('#lbl_total_liquidado').html('$ '+totLiq.toFixed(2));
    $('input[name=total_liquidado]').val(totLiq);        
}

function updateTotales(cuie,accion,monto){
    updateTotalDebitoRetroactivoEfector(cuie,accion,monto);
    updateTotalEfector(cuie,accion,monto);
    updateTotalExpediente(accion,monto);
    
}

function getFacturasDebitoRetro(){
    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: "accion=dbtret_get_facturas&"+$('#dbt_frm_busqueda').serialize(),
        success: function(data){
            //acciones
            $('#position-fixed-top #cnt_factura').html(data);
        }
    });
}

function getPrestacionesDebitoRetro(){
    if($('#dbt_frm_busqueda select[name=dbt_expte]').val()!=""){
        if($('#dbt_frm_busqueda input[name=dbt_clave_doc]').val()!=""){
            $.ajax({
                type: "POST",
                url: "ajax.php",
                data: "accion=dbtret_get_prestaciones&"+$('#dbt_frm_busqueda').serialize(),
                beforeSend: function(data){
                        var imgLoading = '<img src="../../imagenes/mini_loading.gif" alt=""/>';
                        $('#dbt_btn_buscar').hide();
                        $('#dbt_btn_buscar').after(imgLoading);
                    },
                success: function(data){
                    $('#position-fixed-top #cnt_res_busqueda').html(data);
                    $('#dbt_btn_buscar').show();
                    $('#dbt_btn_buscar').next().remove();
                }
            });
        }else{
            alert("Por favor ingrese una clave de beneficiario o nro de documento");
        }
    }else{
        alert("Elija un Nro de Expediente");
    }
        
}
