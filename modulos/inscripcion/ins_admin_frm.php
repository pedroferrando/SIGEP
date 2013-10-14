<form id="form1" name='form1' action='ins_admin.php' accept-charset="utf-8" method='POST'> 
    <input type="hidden" value="<?= $tipo_ficha ?>" name="tipo_ficha">
    <input id="id_planilla" type="hidden" value="<?= $id_planilla ?>" name="id_planilla">
    <input type="hidden" value="<?= $campo_actual ?>" name="campo_actual">
    <input type="hidden" value="<?= $remediar ?>" name="remediar">
    <input type="hidden" value="<?= $clave_beneficiario ?>" name="clave_beneficiario">
    <input type="hidden" value="<?= $prov_uso ?>" name="prov_uso">
    <input type="hidden" value="<?= $provincia_nac ?>" name="provincia_nac">
    <input type="hidden" value="<?= $localidad_proc ?>" name="localidad_proc">
    <input type="hidden" value="<?= $tapa_ver ?>" name="tapa_ver">
    <input type="hidden" value="<?= $num_doc ?>" name="num_doc">
    <input type="hidden" value="<?= $existe_en_smi ?>" name="existe_en_smi">
    <? echo "<center><b><font size='+1' color='red'>$accion</font></b></center>"; ?>
    <? echo "<center><b><font size='+1' color='Blue'>$accion2</font></b></center>"; ?>
    <table width="80%" cellspacing="0" border="1" bordercolor="#E0E0E0" align="center" bgcolor='<?= $bgcolor_out ?>' class="bordes">

        <tr id="mo">
            <td>
                <?
                if (!$id_planilla) {
                    ?>  
                    <font size=+1><b>Nuevo Formulario</b></font>   
                    <?
                } else {
                    ?>
                    <font size=+1><b>Formulario</b></font>   
                <? } ?>

            </td>
        </tr>
        <tr><td>
                <table width=100% align="center" class="bordes">
                    <tr>     
                        <td>
                            <table class="bordes" align="center">             
                                <tr>	           
                                    <td align="center" colspan="4" id="ma">
                                        <b> Número de Formulario: <font size="+1" color="Blue"><?= ($id_planilla) ? $clave_beneficiario : "Nuevo" ?></font> </b>                                         
                                        <? if ($trans == 'Borrado') { ?> 
                                            <b><font size="+1" color="Blue"><?= ($id_planilla) ? $trans : $trans ?></font></b>
                                        <? } ?>            
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" colspan="4" id="ma">
                                        <b> [Última Actualización: <font color="red"> <?= ($id_planilla) ? ( strtotime($fecha_verificado) > strtotime($fecha_carga) ? $fecha_verificado : $fecha_carga) : '' ?> </font>
                                            - Estado: <font color="red"> <?= ($id_planilla) ? ($estado_envio == 'p' ? 'PENDIENTE' : ($estado_envio == 'n' ? 'NO ENVIADO' : 'ENVIADO')) : '' ?> ]</font>
                                        </b>
                                        <a href="javascript:void(0);" title="Mas Info" 
                                           onclick=" $('#info_usuario').show(600); $(this).next().show(); $(this).hide();">(+)</a>
                                        <a href="javascript:void(0);" title="Ocultar" style="display: none;"
                                           onclick=" $('#info_usuario').hide(600); $(this).prev().show(); $(this).hide();">(-)</a>
                                        <div id="info_usuario" style="display:none;">
                                            <table class="bordes" align="center" width="60%">
                                                <tr>
                                                    <td align="center"><b> <font color="red">Usuario Cargador</b></td>
                                                    <td align="center"><b> <font color="red">Usuario Verificador</b></td>
                                                </tr>
                                                <tr>
                                                    <td>Fecha carga: <?php if($fecha_carga!=""){ echo date('d/m/Y',strtotime($fecha_carga)); } ?></td>
                                                    <td>Fecha verificado: <?php if($fecha_verificado!=""){ echo date('d/m/Y',strtotime($fecha_verificado)); } ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Nombre: <?php echo $us_carga_nombre; ?></td>
                                                    <td>Nombre: <?php echo $us_verif_nombre; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Comentarios: <?php echo $us_carga_coment; ?></td>
                                                    <td>Comentarios: <?php echo $us_verif_coment; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Mail: <?php echo $us_carga_mail; ?></td>
                                                    <td>Mail: <?php echo $us_verif_mail; ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <table width="90%" align="center" style="margin:0 auto;">
                                            <tr>
                                                <td align="center" colspan="4">
                                                    <b><font size="0" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>           
                                                </td>
                                            </tr>

                                            <tr id="tapa" align="center">                                               

                                                <td align="right" style="width:180px">
                                                    <b>Tipo de Transaccion:</b>			
                                                </td>
                                                <td align="left" width="30%" style="width:180px;">			 	
                                                    <select name=tipo_transaccion Style="width:200px"
                                                            onKeypress="buscar_combo(this);"
                                                            onblur="borrar_buffer();"
                                                            onchange="borrar_buffer();
        document.forms[0].submit()" 
                                                            <?php
                                                            if ($trans == 'Borrado')
                                                                echo "disabled";
                                                            if ($id_planilla == null)
                                                                echo "disabled";
                                                            ?>
                                                            >
                                                        <option value='A' <?
                                                            if ($tipo_transaccion == 'A')
                                                                echo "selected"
                                                                ?>>Inscripcion
                                                        </option>
                                                        <option value='M'<?
                                                    if ($tipo_transaccion == 'M')
                                                        echo "selected"
                                                                ?>>Modificacion
                                                        </option>
                                                        <!--<option value='B'<? /*
                                                      if ($tipo_transaccion == 'B')
                                                      echo "selected" */
                                                            ?>>Baja</option>-->
                                                    </select>			
                                                </td>
                                                <td>
                                                </td>
                                                <td>
                                                </td>
                                            </tr>                                              
                                            <tr id="tapa" >
                                                <td align="right" style="width:180px">
                                                    <b><font color="Red">*</font>Primer Apellido:</b>
                                                </td>
                                                <td align='left'>
                                                    <input type="text" size="30" value="<?= $apellido ?>" name="apellido" 
                                                        <?php if(($id_planilla && $tipo_transaccion != "M") || ($tipo_transaccion=="M" && $clase_doc=="P" && $existe_en_smi==1)): ?>
                                                            <?php if(!permisos_check("inicio", "permiso_modificar_apellido")): ?>
                                                                    readonly
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                           maxlength="50" onkeypress="return pulsar(event);"/>
                                                </td>
                                                <td align="right" style="width:180px">
                                                    <b>Otros Apellidos:</b>         	
                                                </td>
                                                <td align='left'>
                                                    <input type="text" size="30" value="<?= $apellido_otro ?>" name="apellido_otro" <?php
                                                if (($id_planilla) and ($tipo_transaccion != "M"))
                                                    echo "readOnly"
                                                                ?> maxlength="30" onkeypress="return pulsar(event);">            
                                                </td>
                                            </tr>
                                            <tr id="tapa" >
                                                <td align="right" width="180">
                                                    <b><font color="Red">*</font>Primer Nombre:</b>         	
                                                </td>
                                                <td align='left'>
                                                    <input type="text" size="30" value="<?= $nombre ?>" name="nombre" <?php
                                                if (($id_planilla) and ($tipo_transaccion != "M"))
                                                    echo "readOnly"
                                                                ?> maxlength="50" onkeypress="return pulsar(event);">           
                                                </td>
                                                <td align="right" width="180">
                                                    <b>Otros Nombres:</b>         	
                                                </td>
                                                <td align='left'>
                                                    <input type="text" size="30" value="<?= $nombre_otro ?>" name="nombre_otro" <?php
                                                if (($id_planilla) and ($tipo_transaccion != "M"))
                                                    echo "readOnly"
                                                                ?> maxlength="30" onkeypress="return pulsar(event);">            
                                                </td>
                                            </tr>

                                            <tr>
                                                <td align="right" width="180">
                                                    <font color="Red">*</font><b>El Documento es:</b>			
                                                </td>
                                                <td align="left">
                                                    <?php if($id_planilla==""): ?>
                                                        <input type="hidden" id=clase_doc name=clase_doc value="<?php echo $clase_doc; ?>"/>
                                                        <?php echo $clase_doc=='P' ? 'Propio' : 'Ajeno'; ?>
                                                    <?php else: ?>
                                                        <select id=clase_doc name=clase_doc style="width:200px;" 
                                                            <?php if( ($id_planilla &&$tipo_transaccion != "M") || $id_planilla=="" ): ?>
                                                                disabled="disabled"
                                                            <?php endif; ?> >
                                                            <option value=P <?php if($clase_doc=='P') echo "selected"; ?>>Propio</option>
                                                            <option value=A <?php if ($clase_doc == 'A') echo "selected" ?>>Ajeno</option>
                                                        </select>
                                                    <?php endif; ?>
                                                </td> 
                                                <td align="right" width="180">
                                                    <font color="Red">*</font><b>Tipo de Documento:</b>			
                                                </td>
                                                <td align="left">
                                                    <?php if($id_planilla==""): ?>
                                                        <input type="hidden" id=tipo_doc name=tipo_doc value="<?php echo $tipo_doc; ?>"/>
                                                        <?php 
                                                            if($tipo_doc == 'DNI') echo "Documento Nacional de Identidad" ;
                                                            if($tipo_doc == 'LE')  echo "Libreta de Enrolamiento" ;
                                                            if($tipo_doc == 'LC')  echo "Libreta Civica" ;
                                                            if($tipo_doc == 'PA')  echo "Pasaporte Argentino" ;
                                                            if($tipo_doc == 'CM')  echo "Cedula Migratoria" ;
                                                            if($tipo_doc == 'DEX') echo "Documento Extranjero" ;
                                                        ?>
                                                    <?php else: ?>
                                                        <select id=tipo_doc name=tipo_doc style="width:200px;" 
                                                            <?php if( ($id_planilla && $tipo_transaccion!="M") || $id_planilla=="" ): ?>
                                                                disabled="disabled"
                                                            <?php endif; ?>>
                                                            <option value="DNI" <?php if($tipo_doc == 'DNI') echo "selected";?>>Documento Nacional de Identidad</option>
                                                            <option value="LE"  <?php if($tipo_doc == 'LE') echo "selected";?>>Libreta de Enrolamiento</option>
                                                            <option value="LC"  <?php if($tipo_doc == 'LC') echo "selected";?>>Libreta Civica</option>
                                                            <option value="PA"  <?php if($tipo_doc == 'PA') echo "selected";?>>Pasaporte Argentino</option>
                                                            <option value="CM"  <?php if($tipo_doc == 'CM') echo "selected";?>>Cedula Migratoria</option>
                                                            <option value="DEX" <?php if($tipo_doc == 'DEX') echo "selected";?>>Documento Extranjero </option>
                                                        </select>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="right" width="150">
                                                    <b><font color="Red">*</font>Número de Documento:</b>         	
                                                </td>         	
                                                <td align='left' width="30%">
                                                    <input maxlength="12" type="text" size="25" id="numero_doc" value="<?= $num_doc ?>" name="numero_doc" onKeyPress="return acceptNum(event)"
                                                    <?php
                                                    if (($id_planilla))// and ($tipo_transaccion != "M"))
                                                        echo "disabled";
                                                    if ($id_planilla == null)
                                                        echo "disabled";
                                                    ?>/>                                                     
                                                    <br><font color="Red">Sin Puntos</font>            
                                                </td>
                                            </tr>
                                            <tr id="tapa">
                                                <td align="right" width="180">
                                                    <b> Mail: </b>                        
                                                </td>
                                                <td align="left">
                                                    <input type="text" size="35" name="mail" value="<?= $mail ?>" <?php
                                                    if (($id_planilla) and ($tipo_transaccion != "M"))
                                                        echo "disabled"
                                                        ?> maxlength="35" onkeypress="return pulsar(event);">                        
                                                </td>
                                                <td align="right" width="180">
                                                    <b>Celular:</b>                        
                                                </td>
                                                <td align="left">
                                                    <input type="text" size="30" name="celular" value="<?= $celular ?>" <?php
                                                if (($id_planilla) and ($tipo_transaccion != "M"))
                                                    echo "disabled"
                                                        ?> maxlength="40" onkeypress="return pulsar(event);">                        
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <? // hasta aca primera parte del formulario                ?>

                                <tr>
                                    <td colspan="4">
                                        <table width="90%" align="center" style="margin:0 auto; padding-top: 20px">
                                            <tr id="tapa" >	           
                                                <td align="center" colspan="4" id="ma">
                                                    <b> Datos de Nacimiento, Sexo, Origen y Estudios </b>           </td>
                                            </tr>

                                            <tr id="tapa" >
                                                <td align="right">
                                                    <b><font color="Red">*</font>Sexo:</b>
                                                </td>
                                                <td align="left">
                                                    <?php
                                                        if( !permisos_check("inicio", "permiso_modificar_apellido")
                                                                &&
                                                            ( ($id_planilla && $tipo_transaccion != "M") || ($tipo_transaccion == "M" && $existe_en_smi==1) )  
                                                          ){
                                                            echo $sexo=='F' ? 'Femenino' : 'Masculino';
                                                            ?>
                                                            <input type="hidden" name="sexo" value="<?php echo $sexo; ?>"/>
                                                            <?php    
                                                        }else{
                                                            ?>
                                                            <select name="sexo" onchange="cambiar_pantalla();" style="width:200px">
                                                                <option value='-1' >Seleccione</option>
                                                                <option value=F <?php if($sexo == 'F'){ echo "selected";}?>>
                                                                    Femenino
                                                                </option>
                                                                <option value=M <?php if($sexo == 'M'){ echo "selected";}?>>
                                                                    Masculino
                                                                </option>
                                                            </select>
                                                            <?php
                                                        }
                                                    ?>
                                                </td> 

                                                <td align="right">
                                                    <b><font color="Red">*</font>Fecha de Nacimiento:</b> 
                                                    <input type="hidden" name="edades" id=edades value="<?= $edad ?>">
                                                </td>

                                                <td align="left">
                                                    <?php 
                                                        if( !permisos_check("inicio", "permiso_modificar_apellido")
                                                                &&
                                                            ( ($id_planilla && $tipo_transaccion != "M") || ($tipo_transaccion == "M" && $existe_en_smi==1) )
                                                          ){
                                                            echo $fecha_nac;
                                                            ?>
                                                            <input type="hidden" name="fecha_nac" id="fecha_nac" value="<?php echo $fecha_nac; ?>"/>
                                                            <?php
                                                        }else{
                                                            ?>
                                                            <input type=text name=fecha_nac id='fecha_nac' 
                                                                value='<?= $fecha_nac; ?>' size=15 maxlength="10"
                                                                onKeyUp="mascara(this, '/', patron, true);
                                                                                return pulsar(event);" 
                                                                    onkeypress="if (event.keyCode == 13 || event.keyCode == 9) {
                                                                                        esFechaValida(this.value);
                                                                                        edad(this.value);
                                                                                        cambiar_pantalla();
                                                                                }"
                                                            >
                                                            <?php
                                                        }
                                                    ?>
                                                            
                                                    <!--onkeypress="return pulsar(event);"-->

                                                </td>		    	     
                                            </tr>   

                                            <tr id="tapa" >
                                                <td align="right" >
                                                    <b><font color="Red">*</font>Extranjero/Pais:</b> <input type="hidden" name="paisn" value="<?= $paisn ?>">    		
                                                </td>
                                                <td align="left">
                                                    <select id="pais_nac" name="pais_nac" onchange="showpais_nac();" 
                                                        <?php
                                                            if (($id_planilla) and ($tipo_transaccion != "M"))
                                                                echo "disabled"
                                                        ?>>
                                                            <?php echo $pais_nacq; ?>
                                                    </select>    		
                                                </td>

                                                <td align="right">
                                                    <b>¿Pertenece a algún Pueblo Indígena?</b>         	
                                                </td>         	
                                                <td align='left'>
                                                    <input type="radio" name="indigena" value="N" <?php
                                                    if (($id_planilla) and ($tipo_transaccion != "M"))
                                                        echo "disabled"
                                    ?> <?php
                                                    if (($indigena == "N") or ($indigena == ""))
                                                        echo "checked";
                                                    ?> onclick="document.all.id_tribu.value = '0';
        document.all.id_lengua.value = '0';" > NO
                                                    <input type="radio" name="indigena" value="S" <?php
                                                    if (($id_planilla) and ($tipo_transaccion != "M"))
                                                        echo "disabled"
                                                        ?> <?php
                                                    if ($indigena == "S")
                                                        echo "checked";
                                                    ?> onclick="document.all.id_tribu.disabled = false;
        document.all.id_lengua.disabled = false;"> SI            
                                                </td>
                                            </tr> 

                                            <tr id="tapa" >
                                                <td align="right">
                                                    <b>Pueblo Indigena:</b>         	
                                                </td>         	
                                                <td align='left'>
                                                    <select name=id_tribu Style="width:200px" 
                                                            onKeypress="buscar_combo(this);"
                                                            onblur="borrar_buffer();"
                                                            onchange="borrar_buffer();" 
                                                            <?php
                                                            if (($id_planilla) and ($tipo_transaccion != "M"))
                                                                echo "disabled"
                                                                ?>>
                                                        <option value='-1'>Seleccione</option>
                                                        <?
                                                        $sql = "select * from uad.tribus order by nombre";
                                                        $res_efectores = sql($sql) or fin_pagina();
                                                        while (!$res_efectores->EOF) {
                                                            $id = $res_efectores->fields['id_tribu'];
                                                            $nombre = $res_efectores->fields['nombre'];
                                                            ?>
                                                            <option value='<?= $id ?>' <?
                                                        if ($id_tribu == $id)
                                                            echo "selected"
                                                                ?> ><?= $nombre ?></option>
                                                                    <?
                                                                    $res_efectores->movenext();
                                                                }
                                                                ?>
                                                    </select>            </td>
                                                <td align="right">
                                                    <b>Idioma O Lengua:</b>         	</td>         	
                                                <td align='left'>
                                                    <select name=id_lengua Style="width:200px" 
                                                            onKeypress="buscar_combo(this);"
                                                            onblur="borrar_buffer();"
                                                            onchange="borrar_buffer();" 
                                                            <?php
                                                            if (($id_planilla) and ($tipo_transaccion != "M"))
                                                                echo "disabled"
                                                                ?>>
                                                        <option value='-1'>Seleccione</option>
                                                        <?
                                                        $sql = "select * from uad.lenguas";
                                                        $res_efectores = sql($sql) or fin_pagina();
                                                        while (!$res_efectores->EOF) {
                                                            $id = $res_efectores->fields['id_lengua'];
                                                            $nombre = $res_efectores->fields['nombre'];
                                                            ?>
                                                            <option value='<?= $id ?>' <?
                                                        if ($id_lengua == $id)
                                                            echo "selected"
                                                                ?> ><?= $nombre ?></option>

                                                            <?
                                                            $res_efectores->movenext();
                                                        }
                                                        ?>
                                                    </select>            </td>
                                            </tr> 

                                            <tr id="tapa">
                                                <td align="right">
                                                    <b>Alfabetizado:</b>         	</td>         	
                                                <td align='left'>
                                                    <input type="radio" name="alfabeta" value="S" onclick="document.all.estudios[1].checked = true" <?php
                                                        if (($id_planilla) and ($tipo_transaccion != "M"))
                                                            echo "disabled"
                                                            ?> <?php
                                                    if (($alfabeta == "S") or ($alfabeta == ""))
                                                        echo "checked";
                                                    ?>> SI
                                                    <input type="radio" name="alfabeta" value="N" onclick="document.all.estudios[0].checked = false;
        document.all.estudios[1].checked = false;
        document.all.estudios[2].checked = false;
        document.all.anio_mayor_nivel.value = '0';" <?php
                                                           if (($id_planilla) and ($tipo_transaccion != "M"))
                                                               echo "disabled"
                                                               ?> <?php
                                                           if ($alfabeta == "N")
                                                               echo "checked";
                                                           ?>> NO            </td>
                                                <td align="right">
                                                    <b>Estado:</b>            </td>    
                                                <td align="left">			 	
                                                    <select name=estadoest Style="width:200px" <?php
                                                           if (($id_planilla) and ($tipo_transaccion != "M"))
                                                               echo "disabled"
                                                               ?>>

                                                        <option value=C <?
                                                if ($estadoest == 'C')
                                                    echo "selected"
                                                               ?>>Completo</option>
                                                        <option value=I <?
                                                    if ($estadoest == 'I')
                                                        echo "selected"
                                                               ?>>Incompleto</option>
                                                    </select>			 </td>
                                            </tr>

                                            <tr id="tapa">
                                                <td align="right" valign="top"><b>Estudios:</b></td>         	
                                                <td align='left' width="250">
                                                    <input type="radio" name="estudios" value="Inicial" 
                                                        <?php
                                                            if(($id_planilla) and ($tipo_transaccion != "M"))
                                                                echo " disabled ";
                                                            if(($estudios == "INICIAL") or ($estudios == "Inicial"))
                                                                echo " checked ";
                                                        ?>>Inicial&nbsp;&nbsp;&nbsp;
                                                    <input type="radio" name="estudios" value="Primario" 
                                                        <?php
                                                            if(($id_planilla) and ($tipo_transaccion != "M"))
                                                                echo " disabled ";
                                                            if(($estudios == "PRIMARIO") or ($estudios == "Primario"))
                                                                echo " checked ";
                                                        ?>>Primario
                                                    <input type="radio" name="estudios" value="Secundario" 
                                                        <?php
                                                            if(($id_planilla) and ($tipo_transaccion != "M"))
                                                                echo " disabled ";
                                                            if(($estudios == "SECUNDARIO") or ($estudios == "Secundario"))
                                                                echo " checked ";
                                                        ?>>Secundario
                                                    <br>
                                                    <input type="radio" name="estudios" value="Terciario" 
                                                        <?php
                                                            if(($id_planilla) and ($tipo_transaccion != "M"))
                                                                echo " disabled ";
                                                            if(($estudios == "TERCIARIO") or ($estudios == "Terciario"))
                                                                echo " checked ";
                                                        ?>>Terciario
                                                    <input type="radio" name="estudios" value="Universitario" 
                                                        <?php
                                                            if(($id_planilla) and ($tipo_transaccion != "M"))
                                                                echo " disabled ";
                                                            if(($estudios == "UNIVERSITARIO") or ($estudios == "Universitario"))
                                                                echo " checked ";
                                                        ?>>Universitario  
                                                </td>            

                                                <td align="right" valign="top">
                                                    <b>Años Mayor Nivel:</b>         	</td>         	
                                                <td align='left' valign="top">
                                                    <input type="text" size="30" value='<?= $anio_mayor_nivel; ?>' name="anio_mayor_nivel" <?php
                                                    if (($id_planilla) and ($tipo_transaccion != "M"))
                                                        echo "disabled"
                                                        ?> onKeyPress="return acceptNum(event);
        return pulsar(event);"  maxlength="4">            </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <table width="90%" align="center" style="margin:0 auto;padding-top: 20px">

                                            <tr id="tapa">	           
                                                <td align="center" colspan="4" id="ma">
                                                    <b> Datos del Domicilio </b>           </td>
                                            </tr>

                                            <tr id="tapa">
                                                <td colspan="4" align="center" id="mva" style="display:<?= $mva1 ?>;padding-bottom: 10px">
                                                    <b>Menor convive con adulto:</b><select name=menor_convive_con_adulto id=menor_convive_con_adulto Style="width:200px" <?php
                                                if (($id_planilla) and ($tipo_transaccion != "M"))
                                                    echo "disabled"
                                                        ?>>
                                                        <option value='' >Seleccione</option>
                                                        <option value=S <?
                                                if ($menor_convive_con_adulto == 'S')
                                                    echo "selected"
                                                        ?>>SI
                                                        </option>
                                                        <option value=N <?
                                                    if ($menor_convive_con_adulto == 'N')
                                                        echo "selected"
                                                        ?>>NO
                                                        </option>
                                                    </select>			
                                                </td>
                                            </tr>

                                            <tr id="tapa">
                                                <td align="right">
                                                    <b><font color="Red">*</font>Calle:</b>         	
                                                </td>         	
                                                <td align='left'>
                                                    <input type="text" size="30" value="<?= $calle ?>" name="calle" <?php
                                                    if (($id_planilla) and ($tipo_transaccion != "M"))
                                                        echo "disabled"
                                                        ?> maxlength="40" onkeypress="return pulsar(event);">
                                                </td>
                                                <td align="right">
                                                    <b><font color="Red">*</font>N° de Puerta:</b>
                                                    <input type="text" size="15" value="<?= $numero_calle ?>" name="numero_calle" <?php
                                                if (($id_planilla) and ($tipo_transaccion != "M"))
                                                    echo "disabled"
                                                        ?> maxlength="5" onkeypress="return pulsar(event);">         	
                                                </td>         	
                                                <td align='left'>
                                                    <b>Piso:</b>
                                                    <input type="text" size="15" value="<?= $piso ?>" name="piso" <?php
                                                if (($id_planilla) and ($tipo_transaccion != "M"))
                                                    echo "disabled"
                                                        ?> maxlength="2" onkeypress="return pulsar(event);">            
                                                </td>
                                            </tr>  

                                            <tr id="tapa">
                                                <td align="right">
                                                    <b>Depto:</b>                                                             	
                                                </td>         	
                                                <td align="left">
                                                    <input  type="text" size="10" value="<?= $dpto ?>" name="dpto" <?php
                                                if (($id_planilla) and ($tipo_transaccion != "M"))
                                                    echo "disabled"
                                                        ?> maxlength="3" onkeypress="return pulsar(event);">
                                                    <b>Mz:</b>
                                                    <input type="text" size="10" value="<?= $manzana ?>" name="manzana" <?php
                                                if (($id_planilla) and ($tipo_transaccion != "M"))
                                                    echo "disabled"
                                                        ?> maxlength="30" onkeypress="return pulsar(event);">            
                                                </td>
                                                <td align="right">
                                                    <b>Entre Calle:</b>
                                                    <input type="text" size="15" value="<?= $entre_calle_1 ?>" name="entre_calle_1" <?php
                                                if (($id_planilla) and ($tipo_transaccion != "M"))
                                                    echo "disabled"
                                                        ?> maxlength="40" onkeypress="return pulsar(event);">         	
                                                </td>         	
                                                <td align='left'>
                                                    <b>Entre Calle:</b>
                                                    <input type="text" size="15" value="<?= $entre_calle_2 ?>" name="entre_calle_2" <?php
                                                if (($id_planilla) and ($tipo_transaccion != "M"))
                                                    echo "disabled"
                                                        ?> maxlength="40" onkeypress="return pulsar(event);">           
                                                </td>         	
                                            </tr>  

                                            <tr id="tapa">
                                                <td align="right">
                                                    <b>Telefono:</b>         	</td>         	
                                                <td align='left'>
                                                    <input type="text" size="30" value="<?= $telefono ?>" name="telefono" <?php
                                                if (($id_planilla) and ($tipo_transaccion != "M"))
                                                    echo "disabled"
                                                        ?> maxlength="40" onkeypress="return pulsar(event);">            </td>
                                                <td align="right">
                                                    <b>Otro</b>(ej: vecino)         	</td>
                                                <td align="left">
                                                    <input type="text" size="30" name="otrotel" value="<?= $otrotel ?>" <?php
                                                if (($id_planilla) and ($tipo_transaccion != "M"))
                                                    echo "disabled"
                                                        ?> maxlength="40" onkeypress="return pulsar(event);">         	</td>
                                            </tr>
                                            <!-- Ajax -->
                                            <tr id="tapa">
                                                <td align="right">
                                                    <b><font color="Red">*</font>Departamento:</b> <input type="hidden" name="departamenton" value="<?= $departamenton ?>">    </td>
                                                <td align="left">
                                                    <select id="departamento" name="departamento" onchange="showdepartamento();" <?php
                                                if (($id_planilla) and ($tipo_transaccion != "M"))
                                                    echo "disabled"
                                                        ?>><?php echo $departamento; ?></select>    </td>
                                                <td align="right">
                                                    <b><font color="Red">*</font>Localidad:</b><input type="hidden" name="localidadn" value="<?= $localidadn ?>">    </td>
                                                <td align="left">
                                                    <select id="localidad" name="localidad" onchange="showlocalidad();" <?php
                                                    if (($id_planilla) and ($tipo_transaccion != "M"))
                                                        echo "disabled"
                                                        ?>><?php echo $opciones2; ?></select>    </td>
                                            </tr>
                                            <tr id="tapa">
                                                <td align="right">
                                                    <b><font color="Red">*</font>Codigo Postal:</b> 
                                                    <input type="hidden" name="cod_posn" value="<?= $cod_posn ?>">         	
                                                </td>         
                                                <td align='left'>	
                                                    <select id="cod_pos" name="cod_pos" onchange="showcodpos();" <?php
                                                    if (($id_planilla) and ($tipo_transaccion != "M"))
                                                        echo "disabled"
                                                        ?>>
                                                            <?php echo $opciones5; ?>
                                                    </select>               
                                                </td>
                                                <td align="right">
                                                    <b><font color="Red">*</font>Municipio:</b>
                                                    <input type="hidden" name="municipion" value="<?= $municipion ?>">    
                                                </td>
                                                <td align="left">
                                                    <select id="municipio" name="municipio" onchange="document.all.b_barrio.disabled = false;
        showmunicipio();" <?php
                                                            if (($id_planilla) and ($tipo_transaccion != "M"))
                                                                echo "disabled"
                                                                ?>>
                                                            <?php echo $opciones3; ?>
                                                    </select>    
                                                </td>
                                            </tr>

                                            <tr id="tapa">
                                                <td align="right">
                                                    <b><font color="Red">*</font>Barrio:</b>
                                                    <input type="hidden" name="barrion" value="<?= $barrion ?>">  
                                                </td>
                                                <td align="left">
                                                    <?
                                                    $d_b_b = 'disabled';
                                                    if ((!$id_planilla) || (($id_planilla) && $tipo_transaccion == "M")) {
                                                        if (($id_planilla) && $tipo_transaccion == "M") {
                                                            $d_b_b = '';
                                                        }
                                                        ?>
                                                        <button class="btn_busca" name="b_barrio" <?= $d_b_b ?> onclick="window.open('busca_barrio.php?muni=' + document.all.municipio.value + '&id_planilla=' + document.all.id_planilla.value, 'Buscar', 'dependent:yes,width:900,height=700,top=1,left=60,scrollbars=yes');" >b</button><? } ?>
                                                    <select id="barrio" name="barrio" onchange="showbarrio();" <?php
                                                    if (($id_planilla) and ($tipo_transaccion != "M"))
                                                        echo "disabled"
                                                        ?>>
                                                            <?php echo $opciones4; ?>
                                                    </select>    
                                                </td>        
                                            </tr>
                                            <!--  Fin Ajax -->
                                            <tr id="tapa">
                                                <td align="right">
                                                    <b>Observaciones:</b>         	
                                                </td>         	
                                                <td align='left' colspan="3">
                                                    <textarea cols='80' rows='4' name='observaciones' <?php
                                                            if (($id_planilla) and ($tipo_transaccion != "M"))
                                                                echo "disabled"
                                                                ?>> <?= $observaciones; ?> </textarea>            
                                                </td>
                                            </tr> 
                                        </table>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td align="center" colspan="4">
                                        
                                        <? if (permisos_check("inicio", "datos_responsable")){ ?>
                                                <a href="javascript:void(0);" 
                                                    onclick="$('#cat_nino').show(600);">
                                                    ====VER DATOS DEL RESPONSABLE===
                                                </a>
                                        <?}?>
                                        
                                    </td>
                                </tr>
                                
                                <tr >
                                    <td colspan="4" align="center">
                                        <table id="cat_nino" width="90%" style="display:<?= $datos_resp ?>;margin-top: 20px; margin-bottom: 20px">       
                                            <tr>  
                                                <td align="center" colspan="4" id="ma" >
                                                    <b> Datos del Responsable </b>         
                                                </td>        
                                            </tr>
                                            <tr>
                                                <td align="right" >
                                                    <b><font color="Red">*</font>Datos de Responsable:</b>			
                                                </td>
                                                <td align="left" >			 	
                                                    <select name=responsable Style="width:200px" <?php
                                                    if (($id_planilla) and ($tipo_transaccion != "M"))
                                                        echo "disabled"
                                                                ?> >
                                                        <option value='-1' <?
                                                if ($responsable == '-1')
                                                    echo "selected"
                                                                ?>>Seleccione</option> 
                                                        <option value=MADRE <?
                                                    if ($responsable == 'MADRE')
                                                        echo "selected"
                                                                ?>>MADRE</option>
                                                        <option value=PADRE <?
                                                    if ($responsable == 'PADRE')
                                                        echo "selected"
                                                                ?>>PADRE</option>
                                                        <option value=TUTOR <?
                                                    if ($responsable == 'TUTOR')
                                                        echo "selected"
                                                                ?>>TUTOR</option>
                                                    </select>			
                                                </td> 
                                            </tr>
                                            <tr>
                                                <td align="right">
                                                    <b>Tipo de Documento:</b>			
                                                </td>
                                                <td align="left">			 	
                                                    <select name=tipo_doc_madre Style="width:200px" <?php
                                                    if (($id_planilla) and ($tipo_transaccion != "M"))
                                                        echo "disabled"
                                                                ?>>
                                                        <option value=DNI <?
                                                if ($tipo_doc_madre == 'DNI')
                                                    echo "selected"
                                                                ?>>Documento Nacional de Identidad</option>
                                                        <option value=LE <?
                                                    if ($tipo_doc_madre == 'LE')
                                                        echo "selected"
                                                                ?>>Libreta de Enrolamiento</option>
                                                        <option value=LC <?
                                                    if ($tipo_doc_madre == 'LC')
                                                        echo "selected"
                                                                ?>>Libreta Civica</option>
                                                        <option value=PA <?
                                                    if ($tipo_doc_madre == 'PA')
                                                        echo "selected"
                                                                ?>>Pasaporte Argentino</option>
                                                        <option value=CM <?
                                                    if ($tipo_doc_madre == 'CM')
                                                        echo "selected"
                                                                ?>>Cedula Migratoria</option>
                                                        <option value=DEX <?
                                                    if ($tipo_doc_madre == 'DEX')
                                                        echo "selected"
                                                                ?>>Documento Extranjero </option>
                                                    </select>			
                                                </td>          	
                                                <td align="right" width="20%">
                                                    <b><font color="Red">*</font>Documento:</b>         	
                                                </td>         	
                                                <td align='left' width="30%">
                                                    <input type="text" size="30" value="<?= $nro_doc_madre ?>" name="nro_doc_madre" <?php
                                                    if (($id_planilla) and ($tipo_transaccion != "M"))
                                                        echo "disabled"
                                                                ?> onKeyPress="return acceptNum(event);
        return pulsar(event);"  maxlength="9">            
                                                </td>  
                                            </tr>    
                                            <tr>
                                                <td align="right">
                                                    <b><font color="Red">*</font>Apellidos:</b>         	
                                                </td>         	
                                                <td align='left'>
                                                    <input type="text" size="30" value="<?= $apellido_madre ?>" name="apellido_madre" <?php
                                                if (($id_planilla) and ($tipo_transaccion != "M"))
                                                    echo "disabled"
                                                                ?> maxlength="50" onkeypress="return pulsar(event);">            
                                                </td>
                                                <td align="right">
                                                    <b><font color="Red">*</font>Nombres:</b>         	
                                                </td>         	
                                                <td align='left'>
                                                    <input type="text" size="30" value="<?= $nombre_madre ?>" name="nombre_madre" <?php
                                                if (($id_planilla) and ($tipo_transaccion != "M"))
                                                    echo "disabled"
                                                                ?> maxlength="50" onkeypress="return pulsar(event);">            
                                                </td>
                                            </tr> 
                                            <tr>	           
                                                <td align="center" colspan="4" id="ma" style="margin-top: 20px;">
                                                    <b> Alfabetización </b>           
                                                </td>        
                                            </tr>
                                            <tr>
                                                <td align="right">
                                                    <b>Alfabeta:</b>         	
                                                </td>         	
                                                <td align='left'>
                                                    <input type="radio" name="alfabeta_madre" value="S" <?php
                                                if (($id_planilla) and ($tipo_transaccion != "M"))
                                                    echo "disabled"
                                                                ?> onclick="document.all.estudios_madre[1].checked = true" checked> SI
                                                    <input type="radio" name="alfabeta_madre" value="N" <?php
                                                if (($id_planilla) and ($tipo_transaccion != "M"))
                                                    echo "disabled"
                                                                ?> onclick="document.all.estudios_madre[0].checked = false;
        document.all.estudios_madre[1].checked = false;
        document.all.estudios_madre[2].checked = false;
        document.all.anio_mayor_nivel_madre.value = '0';"> NO            
                                                </td>
                                                <td align="right">
                                                    <b>Estado:</b>            
                                                </td>    
                                                <td align="left">			 	
                                                    <select name=estadoest_madre Style="width:200px" <?php
                                                if (($id_planilla) and ($tipo_transaccion != "M"))
                                                    echo "disabled"
                                                                ?>>
                                                        <option value=C <?
                                                if ($estadoest_madre == 'C')
                                                    echo "selected"
                                                                ?>>Completo</option>
                                                        <option value=I <?
                                                    if ($estadoest_madre == 'I')
                                                        echo "selected"
                                                                ?>>Incompleto</option>
                                                    </select>			 
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="right">
                                                    <b>Estudios:</b>         	
                                                </td>         	
                                                <td align='left' width="250">
                                                    <input type="radio" name="estudios_madre" value="Inicial" 
                                                        <?php
                                                        if (($id_planilla) and ($tipo_transaccion != "M"))
                                                            echo " disabled ";
                                                        if (($estudios_madre == "INICIAL") or ($estudios_madre == "Inicial"))
                                                            echo " checked ";
                                                        ?>>Inicial &nbsp;&nbsp;&nbsp;
                                                    <input type="radio" name="estudios_madre" value="Primario" <?php
                                                        if (($id_planilla) and ($tipo_transaccion != "M"))
                                                            echo " disabled ";
                                                        if (($estudios_madre == "PRIMARIO") or ($estudios_madre == "Primario"))
                                                            echo " checked ";
                                                        ?>>Primario
                                                    <input type="radio" name="estudios_madre" value="Secundario" <?php
                                                        if (($id_planilla) and ($tipo_transaccion != "M"))
                                                            echo " disabled ";
                                                        if (($estudios_madre == "SECUNDARIO") or ($estudios_madre == "Secundario"))
                                                            echo " checked ";
                                                        ?>>Secundario
                                                    <br>
                                                    <input type="radio" name="estudios_madre" value="Terciario" <?php
                                                        if (($id_planilla) and ($tipo_transaccion != "M"))
                                                            echo " disabled ";
                                                        if (($estudios_madre == "TERCIARIO") or ($estudios_madre == "Terciario"))
                                                            echo " checked ";
                                                        ?>>Terciario
                                                    <input type="radio" name="estudios_madre" value="Universitario" <?php
                                                        if (($id_planilla) and ($tipo_transaccion != "M"))
                                                            echo " disabled ";
                                                        if (($estudios_madre == "UNIVERSITARIO") or ($estudios_madre == "Universitario"))
                                                            echo " checked ";
                                                        ?>>Universitario            
                                                </td>            
                                                <td align="right">
                                                    <b>Años Mayor Nivel:</b>         	
                                                </td>         	
                                                <td align='left'>
                                                    <input type="text" size="30" value='<?= $anio_mayor_nivel_madre ?>' name="anio_mayor_nivel_madre" <?php
                                                    if (($id_planilla) and ($tipo_transaccion != "M"))
                                                        echo "disabled"
                                                        ?> onKeyPress="return acceptNum(event);
        return pulsar(event);" maxlength="4">            
                                                </td>
                                            </tr>
                                            <? //}                  ?>
                                        </table>  
                                    </td>
                                </tr>

                                <tr>
                                    <td align="left" colspan="4" id="memb" style="display:<?= $memb ?>;padding-left: 50px">
                                        <b align="right">Embarazada:</b>
                                        <input type="checkbox" id=menor_embarazada name=menor_embarazada value="S" <?= $checked_embarazo ?> <?
                                            if (($id_planilla) and ($tipo_transaccion != "M"))
                                                echo "disabled"
                                                ?>/>			
                                    </td>
                                </tr>
                                <? //}                   ?>
                                <tr>
                                    <td colspan="4" align="center" style="margin-top: 20px;">                                       

                                        <table name="cat_emb" id="cat_emb" width="90%" style="display:<?= $embarazada ?>;margin-top: 20px;margin-bottom: 10px">

                                            <tr>	           
                                                <td align="center" colspan="4" id="ma">
                                                    <b> Datos de Embarazo </b>           
                                                </td>        
                                            </tr>
                                            <tr>
                                                <td align="right">
                                                    <b>F.U.M.:</b>         	
                                                </td>         	
                                                <td align='left'>
                                                    <? $fecha_comprobante = date("d/m/Y"); ?>
                                                    <input type=text name="fum" id=fum name=fum 
                                                           size=15 maxlength="10"
                                                           value='<?php echo $fum=='30/12/1899' ? '' : $fum;?>' 
                                                           <?php if(($id_planilla) and ($tipo_transaccion != "M")): ?>
                                                                disabled="disabled"
                                                           <?php endif; ?>  
                                                           onblur="recalcF1_fum();" 
                                                           onKeyUp="mascara(this, '/', patron, true); return pulsar(event);" 
                                                           onkeypress="return pulsar(event);">

                                                </td>		    
                                                <td align="right">
                                                    <b><font color="Red">*</font>Fecha de Diag. de Embarazo:</b>			
                                                </td>
                                                <td align="left">	       
                                                    <input type=text id=fecha_diagnostico_embarazo name=fecha_diagnostico_embarazo  
                                                           value='<?php echo $fecha_diagnostico_embarazo=='30/12/1899' ? '' : $fecha_diagnostico_embarazo; ?>' 
                                                           <?php if(($id_planilla) and ($tipo_transaccion != "M")): ?>
                                                               disabled="disabled"
                                                           <?php endif; ?> 
                                                           size=15  maxlength="10" 
                                                           onKeyUp="mascara(this, '/', patron, true); return pulsar(event);" 
                                                           onkeypress="return pulsar(event);">
                                                </td>
                                            </tr>   
                                            <tr>
                                                <td align="right">
                                                    <b><font color="Red">*</font>Semana de Embarazo:</b>         	
                                                </td>         	
                                                <td align='left'>
                                                    <input type="text" name="semanas_embarazo"  
                                                           value="<?= $semanas_embarazo; ?>" 
                                                           onblur="recalcF1();"  size="30" maxlength="4" 
                                                           <?php if(($id_planilla) and ($tipo_transaccion != "M")): ?>
                                                                disabled="disabled"
                                                           <?php endif; ?> 
                                                           onKeyPress="return acceptNum(event); return pulsar(event);" 
                                                           >            
                                                </td>		    
                                                <td align="right">
                                                    <b><font color="Red">*</font>Fecha Probable de Parto:</b>			
                                                </td>
                                                <td align="left">
                                                    <input type=text id=fecha_probable_parto name=fecha_probable_parto  
                                                           size=15 maxlength="10" 
                                                           value='<?php echo $fecha_probable_parto=='30/12/1899' ? '' : $fecha_probable_parto;?>' 
                                                           <?php if(($id_planilla) and ($tipo_transaccion != "M")): ?>
                                                               disabled="disabled"
                                                           <?php endif;?> 
                                                           onKeyUp="mascara(this, '/', patron, true); return pulsar(event);"  
                                                           onkeypress="return pulsar(event);">                                                               	    
                                                </td>
                                            </tr>
                                        </table>
                                    </td>                                                      
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <table width="90%" align="center" style="margin:0 auto;padding-top: 20px">
                                            <tr id="tapa">	           
                                                <td align="center" colspan="4" id="ma" style="margin-top: 10px">
                                                    <b> Riesgo Cardiovascular </b>           
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="right" width="170">
                                                    <b>Score de riesgo:</b>         	
                                                </td>         	
                                                <td align='left'>
                                                    <input type="text" size="10" maxlength="5"
                                                           value='<?php echo $score_riesgo ?>' 
                                                           name="score_riesgo" 
                                                           <?php if(($id_planilla) and ($tipo_transaccion != "M")): ?>
                                                                disabled="disabled"
                                                           <?php endif; ?> 
                                                           onKeyPress="return acceptNum(event); return pulsar(event);">            
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <table width="90%" align="center" style="margin:0 auto;padding-top: 20px">
                                            <tr id="tapa">	           
                                                <td align="center" colspan="4" id="ma" style="margin-top: 10px">
                                                    <b> Discapacidad </b>           
                                                </td>
                                            </tr>

                                            <tr id="tapa">
                                                <td align="center" colspan="4">
                                                    <input type=checkbox name=discv value='Visual' 
                                                        <?php
                                                        if(($id_planilla) and ($tipo_transaccion != "M"))
                                                            echo " disabled ";
                                                        if($discv == "VISUAL")
                                                            echo " checked ";
                                                        ?> > Visual
                                                    <input type=checkbox name=disca value='Auditiva' 
                                                        <?php
                                                        if (($id_planilla) and ($tipo_transaccion != "M"))
                                                            echo " disabled ";
                                                        if ($disca == "AUDITIVA")
                                                            echo " checked ";
                                                        ?> > Auditiva
                                                    <input type=checkbox name=discmo value='Motriz' 
                                                        <?php
                                                        if (($id_planilla) and ($tipo_transaccion != "M"))
                                                            echo " disabled ";
                                                        if ($discmo == "MOTRIZ")
                                                            echo " checked ";
                                                        ?> > Motriz
                                                    <input type=checkbox name=disme value='Mental' 
                                                        <?php
                                                        if (($id_planilla) and ($tipo_transaccion != "M"))
                                                            echo " disabled ";
                                                        if ($discme == "MENTAL")
                                                            echo " checked ";
                                                        ?> > Mental
                                                    <input type=checkbox name=discha value='Habla' 
                                                        <?php
                                                        if (($id_planilla) and ($tipo_transaccion != "M"))
                                                            echo " disabled ";
                                                        if ($discha == "HABLA")
                                                            echo " checked ";
                                                        ?> > Habla
                                                    <input type=checkbox name=otradisc value='Otra Discapacidad' 
                                                        <?php
                                                        if (($id_planilla) and ($tipo_transaccion != "M"))
                                                            echo " disabled ";
                                                        if ($otradisc == "OTRA DISCAPACIDAD")
                                                            echo " checked ";
                                                        ?> > Otra discapacidad         
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="4">
                                        <table width="90%" align="center" style="margin:0 auto;padding-top: 20px">
                                            <tr id="tapa">	           
                                                <td align="center" colspan="4" id="ma" style="margin-top: 20px">
                                                    <b> Fecha de Inscripcion </b>           
                                                </td>
                                            </tr>
                                            <tr id="tapa">
                                                <td align="center" width="20%" colspan="4">
                                                    <font color="Red">*</font><b>Fecha de Inscripcion:</b>

                                                    <?
                                                    if (($tipo_transaccion == 'A') && (!$id_planilla)) {
                                                        $fecha_inscripcion = date('d/m/Y');
                                                    }
                                                    ?>

                                                    <input type=text name=fecha_inscripcion id=fecha_inscripcion 
                                                           value='<?= $fecha_inscripcion; ?>' size=15 
                                                           <?php
                                                            if(($id_planilla) && !permisos_check("inicio", "permiso_modificar_apellido") /* and ($tipo_transaccion != "M") */)
                                                                echo "readonly"
                                                           ?> maxlength="10" 
                                                           onKeyUp="mascara(this, '/', patron, true);
                                                                    return pulsar(event);" 
                                                           onkeypress="return pulsar(event);">
                                                </td>
                                            </tr>  
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <table width="90%" align="center" style="margin:0 auto;padding-top: 20px">
                                            <tr id="tapa">	           
                                                <td align="center" colspan="4" id="ma" style="margin-top: 20px">
                                                    <b> Efector Habitual </b>           
                                                </td>
                                            </tr>

                                            <tr id="tapa">
                                                <td align="center" width="20%" colspan="4" style="margin-top: 20px">
                                                    <b><font color="Red">*</font>Efector Habitual de la Red de Salud Pública:</b>
                                                    <select name=cuie Style="width:300px" 
                                                            onKeypress="buscar_combo(this);"
                                                            onblur="borrar_buffer();"
                                                            onchange="borrar_buffer();" 
                                                            <?php
                                                            if (($id_planilla) and ($tipo_transaccion != "M"))
                                                                echo "disabled"
                                                                ?> >
                                                        <option value=-1>Seleccione</option>
                                                        <?
                                                        $sql = "select * from facturacion.smiefectores where tipoefector not in ('EXT','ALD','ADM','TRN','LAB','BCO') order by nombreefector";
                                                        $res_efectores = sql($sql) or fin_pagina();
                                                        while (!$res_efectores->EOF) {
                                                            $cuiel = $res_efectores->fields['cuie'];
                                                            $nombre_efector = $res_efectores->fields['nombreefector'];
                                                            ?>
                                                            <option value='<?= $cuiel ?>' <?
                                                        if ($cuie == $cuiel)
                                                            echo "selected"
                                                                ?> ><?= ($nombre_efector) ?></option>
                                                                    <?
                                                                    $res_efectores->movenext();
                                                                }
                                                                ?>
                                                    </select>
                                                    <? if ((!$id_planilla) || (($id_planilla) && $tipo_transaccion == "M")) { ?>
                                                        <button class="btn_busca" onclick="window.open('busca_efector.php?qkmpo=cuie', 'Buscar', 'dependent:yes,width:900,height=650,top=1,left=60,scrollbars=yes');">b</button><? } ?>			
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td align="center" colspan="4">
                                        <table width="90%" style="margin-top: 20px">
                                            <tr id="tapa">	           
                                                <td align="center" id="ma" >
                                                    <b> Observaciones Generales </b>           
                                                </td>        
                                            </tr>

                                            <tr align="center" id="tapa">        	
                                                <td align='center' colspan="4">
                                                    <textarea cols='80' rows='4' name='obsgenerales' <?php
                                                    if (($id_planilla) and ($tipo_transaccion != "M"))
                                                        echo "disabled"
                                                        ?> > <?= $obsgenerales; ?>  </textarea>            
                                                </td>
                                            </tr>  
                                        </table>
                                    </td>
                                </tr>

                                <? if ($agentes == 's') { ?>
                                    <tr>
                                        <td colspan="4" align="center">
                                            <table width="90%" style="margin-top: 20px">
                                                <tr id="ma" id="tapa">
                                                    <td align="center" colspan="4" style="margin-top: 20px">
                                                        <? if ((!$id_planilla) || (($id_planilla) && $tipo_transaccion == "M")) { ?>
                                                            <button class="btn_busca" onclick="window.open('../remediar/busca_promotor.php', 'Buscar', 'dependent:yes,width:900,height=700,top=1,left=60,scrollbars=yes');">Buscar</button>
                                                        <? } ?> 
                                                        <b>Datos del Agente Inscriptor</b>           
                                                    </td>
                                                </tr>
                                                <tr id="tapa">
                                                    <td align="right">
                                                        <font color="Red">*</font><b>Apellido:</b>         	
                                                    </td>
                                                    <td align='left'>
                                                        <input type="text" size="30" value="<?= $apellidoagente ?>" name="apellidoagente" maxlength="50"  onkeypress="return pulsar(event);" <?php
                                                        if (($id_planilla) and ($tipo_transaccion != "M"))
                                                            echo "readOnly"
                                                            ?>>            
                                                    </td>
                                                    <td align="right">
                                                        <font color="Red">*</font><b>Nombre:</b>         	
                                                    </td>
                                                    <td align='left'>
                                                        <input type="text" size="30" value="<?= $nombreagente ?>" name="nombreagente" maxlength="50"  onkeypress="return pulsar(event);" <?php
                                                if (($id_planilla) and ($tipo_transaccion != "M"))
                                                    echo "readOnly"
                                                            ?>>
                                                    </td>
                                                </tr>
                                                <tr id="tapa">
                                                    <td align="right">
                                                        <font color="Red">*</font><b>Nro. Doc.:</b>         	</td>
                                                    <td align='left'>
                                                        <input type="text" size="30" value="<?= $num_doc_agente ?>" name="num_doc_agente" maxlength="12" onkeypress="return pulsar(event);" <?php
                                                if (($id_planilla) and ($tipo_transaccion != "M"))
                                                    echo "readOnly"
                                                            ?>>            
                                                    </td>
                                                    <td align="right" >
                                                        <font color="Red">*</font><b>Centro Inscriptor Lugar:</b>			
                                                    </td>
                                                    <td align="left">
                                                        <select name=cuie_agente 
                                                                Style="width:300px"
                                                                onKeypress="buscar_combo(this);"
                                                                onblur="borrar_buffer();"
                                                                onchange="borrar_buffer();"
                                                                <?php
                                                                if (($id_planilla) and ($tipo_transaccion != "M"))
                                                                    echo "disabled"
                                                                    ?>>
                                                            <option value=-1>Seleccione</option>
                                                            <?
                                                            $sql = "select * from facturacion.smiefectores order by nombreefector";
                                                            $res_efectores = sql($sql) or fin_pagina();
                                                            while (!$res_efectores->EOF) {
                                                                $cuiec_agente = $res_efectores->fields['cuie'];
                                                                $nombre_efector_agente = $res_efectores->fields['nombreefector'];
                                                                ?>
                                                                <option value='<?= $cuiec_agente ?>' <?
                                                        if ($cuie_agente == $cuiec_agente)
                                                            echo "selected"
                                                                    ?> ><?= $nombre_efector_agente ?></option>
                                                                        <?
                                                                        $res_efectores->movenext();
                                                                    }
                                                                    ?>
                                                        </select>
                                                        <? if ((!$id_planilla) || (($id_planilla) && $tipo_transaccion == "M")) { ?>
                                                            <button class="btn_busca" onclick="window.open('../inscripcion/busca_efector.php?qkmpo=cuie_agente', 'Buscar', 'dependent:yes,width:900,height=650,top=1,left=60,scrollbars=yes');">b</button><? } ?>			
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                <? } ?>      
                            </table>
                        </td>      
                    </tr> 

                    <tr>
                        <td colspan="4" align="center">
                            <? if ($id_planilla == null) { ?>
                                <table width="90%" style="margin-top: 20px"> 
                                    <tr id="mo"  id="tapa">
                                        <td align=center colspan="2" >
                                            <b>Guardar Planilla</b>
                                        </td>
                                    </tr>  
                                    <tr align="center" id="tapa">
                                        <td>
                                            <b><font size="0" color="Red">Nota: Verifique todos los datos antes de guardar</font> </b>
                                        </td>
                                    </tr>
                                    <tr align="center" id="tapa">
                                        <td>
                                            <input type='submit' name='guardar' value='Guardar Planilla' onclick="return control_nuevos();"     title="Guardar datos de la Planilla"  />
                                        </td>
                                    </tr>
                                    <?php if ($edad == "") { ?>
                                        <script>edad(document.all.fecha_nac.value);</script> 
                                    <? } ?>
                                </table>
                            <? } ?>
                        </td>
                    </tr>
                </table>           
                <br>

                <? if ($clave_beneficiario != '') { ?>
                    <table class="bordes" align="center" width="100%">
                        <tr align="center" id="sub_tabla">
                            <td>	
                                Editar DATO   
                            </td>
                        </tr>
                        <tr align="center">
                            <td>
                                <b><font size="0" color="Red">Nota: Verifique todos los datos antes de guardar</font> </b>
                            </td>
                        </tr>

                        <tr>
                            <td align="center">
                                <input type="submit" name="guardar_editar" value="Guardar" title="Guardar"  style="width:130px" 
                                <?php
                                if ($tipo_transaccion != "M")
                                    echo "disabled"
                                    ?> onclick="return control_nuevos();">&nbsp;&nbsp;
                                       <?
                                       //echo $estado_envio.'***'.strtoupper($usuario_carga).'***'.substr(strtoupper($_ses_user['name']),0,9).'***'.$tipo_transaccion;
                                       if (( $estado_envio == 'p' && strtoupper($usuario_carga) != substr(strtoupper($_ses_user['id']), 0, 9)) && ($tipo_transaccion != "B"))
                                           $permiso = "";
                                       else
                                           $permiso = "disabled";
                                       if ($estado_nuevo) {
                                           ?>
                                            <input type="submit" name="guardar" value="Pasar a No Enviados" title="Pasar a No Enviados"  style="width:130px" <?= $permiso ?>>&nbsp;&nbsp;
                                <? } ?>
    <!--  <input type="button" name="cancelar_editar" value="Cancelar" title="Cancelar Edicion" style="width:130px" onclick="document.location.reload()" disabled>-->
                                <input type="button" name="cancelar_editar" value="Cancelar" title="Cancelar Edicion" style="width:130px"
                                <?php
                                if ($tipo_transaccion != "M")
                                    echo "disabled"
                                    ?> onclick="history.back(-1);">	
                                <input type="submit" name="borrar" value="Borrar" style="width:130px" <?= $permiso ?> <?php
                                   if ($tipo_transaccion != "B")
                                       echo "disabled"
                                    ?>>
                            </td>

                        </tr> 
                        <tr>
                        </tr> 
                        <tr>
                            <td colspan="3" align="center">
                                <input type=button name="carga_remediar" value="Remediar+Redes" onclick="seleccionFormularioRemediar(this);">

                            </td>
                        </tr>
                    </table>	
                    <br>
                <? } ?>
        <tr><td><table width=100% align="center" class="bordes">
                    <tr align="center">
                        <td>
                            <input type=button name="volver" value="Volver" onclick="document.location = 'ins_listado.php'"title="Volver al Listado" style="width:150px">     
                        </td>
                    </tr>

                </table></td></tr>


    </table>
    <div id="dialog-confirm" title="¿Que formulario desea cargar?">
        <p><span style="float: left; margin: 0 7px 2px 0;"></span>¿Que desea cargar?</p>
    </div>
</form>