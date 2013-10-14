<?
require_once ("../../config.php");
echo $html_header;
echo "<script src='../../lib/jquery.min.js' type='text/javascript'></script>";
echo "<script src='../../lib/jquery/jquery-ui.js' type='text/javascript'></script>";
echo "<link rel='stylesheet' href='../../lib/jquery/ui/jquery-ui.css'/>";
echo "<script src='../../lib/jquery/ui/jquery.ui.datepicker-es.js' type='text/javascript'></script>";
?>
<script>
    $(document).ready(function() {
        $('#buscar').on('click', function() {
            buscar();
        });
        
        $('#num_doc').on('keypress', function(event) {
            if (event.which == 13) {
                event.preventDefault();
                buscar();
            }
        });
    });

    function buscar() {
        if (busqueda()) {
            var clase_doc = $('#clase_doc').val();
            var tipo_doc = $('#tipo_doc').val();
            var num_doc = $('#num_doc').val();
            var clave = $('#clave').val();
            $.post("busquedaDeEmpadronados.php", {'clase_doc': clase_doc, 'tipo_doc': tipo_doc, 'num_doc': num_doc,'clave': clave}, function(data) {
                if (data != '') {
                    $('#beneficiarioencontrado').html(data);

                } else {
                    $('#form1').submit();
                }
            });
        }
    }

    var nav4 = window.Event ? true : false;
    function acceptNum(evt) {
        var key = nav4 ? evt.which : evt.keyCode;
        return (key <= 13 || (key >= 48 && key <= 57));
    }

    function pulsar(e) {
        tecla = (document.all) ? e.keyCode : e.which;
        return (tecla != 13);
    }

    function busqueda() {
        if ($('#clave').val()==''){
            if ($('#tipo_doc').val() == "DNI") {
                var num_doc_aux = $('#num_doc').val().replace(/\ /g, '');
                num_doc_aux = parseInt(num_doc_aux);
                if (num_doc_aux.toString().length < 7) {
                    alert("El numero de documento es incorrecto");
                    return false;
                }
            }
            if (document.all.num_doc.value == '') {
                alert('Debe Cargar el Nº de Documento');
                return false;
            }
        }    
        return true;
    }

</script>
<form name='form1' id='form1' action='ins_admin.php' accept-charset="utf-8" method='POST'>
    <? echo "<center><b><font size='+1' color='red'>$accion</font></b></center>"; ?>
    <? echo "<center><b><font size='+1' color='Blue'>$accion2</font></b></center>"; ?>
    <table width="100%" cellspacing="0" border="1" bordercolor="#E0E0E0" align="center" bgcolor='<?= $bgcolor_out ?>' class="bordes">
        <tr id="mo">
            <td>  
                <font size=+1><b>BUSQUEDA DE BENEFICIARIOS</b></font> 
            </td>
        </tr>
        <tr>
            <td>
                <table width=100% align="center" class="bordes">
                    <tr>     
                        <td>
                            <table class="bordes" align="center" width=50% >             
                                <tr>
                                    <td colspan="2">
                                        <font color="Red"><b>ELIJA BUSCAR POR DOCUMENTO O CLAVE DE BENEFICIARIO</b></font>
                                    </td>
                                </tr>     
                                <tr>
                                    <td align="right">
                                        <font color="Red">*</font><b>El Documento es:</b>
                                    </td>
                                    <td align="left">			 	
                                        <select name=clase_doc id=clase_doc Style="width:155px">
                                            <option value=P>Propio</option>
                                            <option value=A>Ajeno</option>
                                        </select>			
                                    </td>
                                    
                                </tr>
                                <tr>
                                    <td align="right">
                                        <font color="Red">*</font><b>Tipo de Doc.:</b>			</td>
                                    <td align="left">			 	
                                        <select name=tipo_doc id=tipo_doc Style="width:200px">
                                            <option value=DNI >Documento Nacional de Identidad</option>
                                            <option value=LE >Libreta de Enrolamiento</option>
                                            <option value=LC >Libreta Civica</option>
                                            <option value=PA >Pasaporte Argentino</option>
                                            <option value=CM >Certificado Migratorio</option>
                                            <option value=DEX >Documento Extranjero </option>
                                        </select>			
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" width="20%">
                                        <b><font color="Red">*</font>1) Nro de Doc.:</b>
                                    </td>         	
                                    <td align='left' width="260">
                                        <input type="text" size="20" id="num_doc" name="num_doc" onKeyPress="return pulsar(event);" maxlength="12" onkeydown="return pulsar(event);"/>                                        
                                        <font color="Red">Sin Puntos</font>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" width="20%">
                                        <b><font color="Red"></font>2) Clave Benef.:</b>
                                    </td>         	
                                    <td align='left' width="260">
                                        <input type="text" size="20" id="clave" name="clave" onKeyPress="return pulsar(event);" maxlength="16" onkeydown="return pulsar(event);"/>
                                    </td>
                                </tr>
                                <tr align='center'>
                                    <td colspan="2">
                                        <input id="buscar" type="button" size="3" value="Buscar" name="b" />
                                    </td>
                                </tr>
                            </table>
                        </td>      
                    </tr> 
                </table> 
            </td>
        </tr>
        <tr>
            <td>
                <div width="70%" id="beneficiarioencontrado"  align='center' style="margin-top:10px;margin-bottom:20px "></div>
            </td>
        </tr>
    </table>



    <table width=100% align="center" class="bordes">
        <tr align="center">
            <td>
                <input type=button name="volver" value="Volver" onclick="document.location = 'ins_listado.php'"title="Volver al Listado" style="width:150px">     
            </td>
        </tr>
    </table>
</form>

<?= fin_pagina(); // aca termino ?>
