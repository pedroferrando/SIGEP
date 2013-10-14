<?php

function controlDebito($l, &$var, $nomenclador) {
    $debitovar['id_factura'] = $var['id_factura'];
    $debitovar['documento_deb'] = $l[8];
    $debitovar['apellido_deb'] = $l[9];
    $debitovar['nombre_deb'] = $l[10];
    $debitovar['observaciones_deb'] = $l[58];
    $debitovar['codigo_deb'] = $l[12];
    if (is_null($nomenclador)) {
        $debitovar['id_nomenclador'] = 0;
    } else {
        $debitovar['id_nomenclador'] = $nomenclador->getIdNomenclador();
    }
    $debitovar['prestacionid'] = $l[4];

    do {
        $resultado_ctrl = existePrestacion($nomenclador);
        if ($resultado_ctrl['debito']) {
            $debitovar['monto_deb'] = 0;
            break;
        }

        $resultado_ctrl = precioPrestacion($nomenclador, $var['grupo_etario'], $var['beneficiario']['afisexo']);
        $debitovar['monto_deb'] = $resultado_ctrl['precio'];
        if ($resultado_ctrl['debito'])
            break;

        /* pregunta si es taller */
        $resultado_ctrl = controlDeDatosRequeridos($l, $var, $nomenclador);
        if ($resultado_ctrl['debito'])
            break;

        if (!nomencladoresQueNoSeControlan($var['nomenclador_original']))
            break;

//        //controles para facturas Sumar nuevo nomenclador
//        $resultado_ctrl = excepcionSumarSoloEnero($l[1], $l[13], $var['grupo_etario']);
//        if ($resultado_ctrl['debito'])
//            break;

        $resultado_ctrl = efectorTieneConvenio($l[1], $l[13]);
        $convenio = $resultado_ctrl['convenio'];
        if ($resultado_ctrl['debito']) {
            $var['id_nomenclador'] = buscarPrecioSinConvenio($l[12], $l[13]);
            break;
        }

        $resultado_ctrl = practicaMenor6Meses($debitovar['id_factura'], $l[13]);
        if ($resultado_ctrl['debito'])
            break;

        $resultado_ctrl = controlarAfiliado($l[6], $l[7], $l[8], $var['beneficiario'], $l[5]);
        if ($resultado_ctrl['debito'])
            break;

        $periododelcomprobante = buscarPeriodo(str_replace("/", "-", $l[13]));
        $periododelcomprobante = $periododelcomprobante['id'];
        if (Nomenclador::practicaSoloParaEmbarazadas($nomenclador->getIdNomenclador())) {
            $debitovar['monto_deb'] = $nomenclador->getEmbarazada();
            if (!beneficiarioEmbarazadoSMI($l[5], $periododelcomprobante, Fecha_db($l[13]))) {
                $resultado_ctrl['debito'] = true;
                $resultado_ctrl['msj_error'] .= 'No puede facturar este codigo [Solo Embarazadas]';
                $resultado_ctrl['id_error'] = '61';
                break;
            }
        } else {
            
        }

        $resultado_ctrl = fechaPrestacionXNacimiento($l[13], $l[11]);
        if ($resultado_ctrl['debito'])
            break;

        $resultado_ctrl = fechaPrestacionXInscripcion($l[13], $var['beneficiario']['fechainscripcion']);
        if ($resultado_ctrl['debito'])
            break;


        $edaddias = diferencia_dias_m($l[11], $l[13]);
        $resultado_ctrl = mayorDe1SinDocumento($l[6], $edaddias);
        if ($resultado_ctrl['debito'])
            break;

        $resultado_ctrl = controlInformado($l[1], $l[4], $l[13], $var['datos_nombre_archivo']['tipo_facturacion']);
        if ($resultado_ctrl['debito'])
            break;
    } while (false);

    $debitovar['resultado_ctrl'] = $resultado_ctrl;
    return $debitovar;
}

function existePrestacion($nomenclador) {
    if (is_null($nomenclador)) {
        $resultado_ctrl['debito'] = TRUE;
        $resultado_ctrl['id_error'] = '61';
        $resultado_ctrl['msj_error'] .= 'No puede Facturar este codigo [No se encontro el Codigo para facturar]';
    }
    return $resultado_ctrl;
}

function precioPrestacion($nomenclador, $grupo_etario, $sexo) {
    $resultado_ctrl['debito'] = FALSE;
    $resultado_ctrl['precio'] = FALSE;

    if ($sexo == 'F') {
        if ($grupo_etario['estaembarazada']) {
            if ($nomenclador->getEmbarazada() > 0) {
                $resultado_ctrl['precio'] = $nomenclador->getEmbarazada();
            } else {
                if (($nomenclador->getPrecioSegunGrupo($grupo_etario['categoria']) > 0) && ($nomenclador->getF() == 't')) {
                    $resultado_ctrl['precio'] = $nomenclador->getPrecioSegunGrupo($grupo_etario['categoria']);
                }
            }
        } else {
            if (($nomenclador->getPrecioSegunGrupo($grupo_etario['categoria']) > 0) && ($nomenclador->getF() == 't')) {
                $resultado_ctrl['precio'] = $nomenclador->getPrecioSegunGrupo($grupo_etario['categoria']);
            }
        }
    } else {
        if (($nomenclador->getPrecioSegunGrupo($grupo_etario['categoria']) > 0) && ($nomenclador->getM() == 't')) {
            $resultado_ctrl['precio'] = $nomenclador->getPrecioSegunGrupo($grupo_etario['categoria']);
        }
    }

    if ($resultado_ctrl['precio'] == FALSE) {
        $practicaunicovalor = Nomenclador::practicaSoloParaUnGrupo($nomenclador->getIdNomenclador());
        if ($practicaunicovalor != FALSE) {
            $resultado_ctrl['msj_error'] .= 'No puede Facturar este codigo [Solo ' . $practicaunicovalor['grupo'] . ']';
            $resultado_ctrl['precio'] = $practicaunicovalor['precio'];
        } else {
            if ($sexo == 'F') {
                $sexoentero = 'femenino';
            } else {
                $sexoentero = 'masculino';
            }
            $resultado_ctrl['precio'] = 0;
            $resultado_ctrl['msj_error'] .= 'No puede Facturar este codigo [No corresponde ' . $grupo_etario['descripcion'] . ' ' . $sexoentero . ']';
        }

        $resultado_ctrl['debito'] = TRUE;
        $resultado_ctrl['id_error'] = '61';
    }
    return $resultado_ctrl;
}

function controlDeDatosRequeridos(&$datosdelinea, &$var, $nomenclador) {
    $resultado_ctrl['debito'] = FALSE;

    $codigopractica = str_replace(" ", "", $datosdelinea[12]);
    do {

        if ($codigopractica == 'CMI65' || $codigopractica == 'CMI66' || $codigopractica == 'CMI67' || $codigopractica ==
                'RCM107' || $codigopractica == 'RCM108' || $codigopractica == 'RCM109') {
            if ($datosdelinea[15] == null || $datosdelinea[15] == '' || !(intval($datosdelinea[15]) > 0)) {
                $var['cantidad'] = 0;
                $resultado_ctrl['debito'] = TRUE;
                $resultado_ctrl['id_error'] = '17';
                $resultado_ctrl['msj_error'] .= 'Faltan Datos Requeridos: IdTaller';
                break;
            } else {
                $var['cantidad'] = $datosdelinea[15];
            }
        }


        if ($codigopractica == 'TMI69' || $codigopractica == 'TMI70' || $codigopractica == 'TMI71' ||
                $codigopractica == 'RTM111' || $codigopractica == 'RTM112' || $codigopractica == 'RTM113') {
            if ($datosdelinea[15] == null || !(intval($datosdelinea[15]) > 0)) {
                //$resultado_ctrl['debito'] = TRUE;
                //$resultado_ctrl['id_error'] = '17';
                //$resultado_ctrl['msj_error'] .= 'Faltan Datos Requeridos: Traslado';
                //$var['idvacuna'] = 0;
                // break;
            } else {
                //En realidad el dato es es KM de traslado, pero reciclando campos se inserta en idvacuna en el comprobante.
                $var['idvacuna'] = $datosdelinea[15];
            }
        }

        /* pregunta si es vacunacion */
        $var['vacuna'] = 'si';
        $var['idvacuna'] = 0;
        if ($codigopractica == 'NPE41' || $codigopractica == 'NPE42' || $codigopractica == 'RPE93' || $codigopractica ==
                'RPE94' || $codigopractica == 'NNE31' || $codigopractica == 'MPU23') {
            if ($datosdelinea[14] == null || $datosdelinea[14] == '' || !(intval($datosdelinea[14]) > 0)) {

                $resultado_ctrl['debito'] = TRUE;
                $resultado_ctrl['id_error'] = '17';
                $resultado_ctrl['msj_error'] .= 'Id. Vacuna Invalido';
                $var['idvacuna'] = 0;
                break;
            } else {
                $var['idvacuna'] = $datosdelinea[14];
            }
        }

        //calcula la edad del benef al momento de la practica
        $edadBenef = edad_relativa($datosdelinea[11], $datosdelinea[13]);
        if ($datosdelinea[3] == 1) {
            /* NIÑOS */
            if ($edadBenef < 1) {
                /* menor de 1 a?o */
                if ($codigopractica == 'CTC001A97') {
                    if ($datosdelinea[30] == null || $datosdelinea[30] == '') {
                        $datosdelinea[30] = 0;
                        $var['error'] = 'si';
                        $resultado_ctrl['debito'] = TRUE;
                        $var['descripcion_error'] .= ',peso';
                    }
                    if ($datosdelinea[31] == null || $datosdelinea[31] == '') {
                        $datosdelinea[31] = '';
                        $var['error'] = 'si';
                        $resultado_ctrl['debito'] = TRUE;
                        $var['descripcion_error'] .= ',percPesoEdad';
                    }

                    if ($datosdelinea[32] == null || $datosdelinea[32] == '') {
                        $datosdelinea[32] = 0;
                        $var['error'] = 'si';
                        $resultado_ctrl['debito'] = TRUE;
                        $var['descripcion_error'] .= ',talla';
                    }
                    if ($datosdelinea[33] == null || $datosdelinea[33] == '') {
                        $datosdelinea[33] = '';
                        $var['error'] = 'si';
                        $resultado_ctrl['debito'] = TRUE;
                        $var['descripcion_error'] .= ',percTallaEdad';
                    }

                    if (($datosdelinea[34] == null) || ($datosdelinea[34] == '')) {
                        $datosdelinea[34] = 0;
                        $var['error'] = 'si';
                        $resultado_ctrl['debito'] = TRUE;
                        $var['descripcion_error'] .= ',perimCef';
                    }
                    if (($datosdelinea[35] == null) || ($datosdelinea[35] == '')) {
                        $datosdelinea[35] = 0;
                        $var['error'] = 'si';
                        $resultado_ctrl['debito'] = TRUE;
                        $var['descripcion_error'] .= ',percPerimCef';
                    }
                    if ($datosdelinea[36] == null || $datosdelinea[36] == '') {
                        $datosdelinea[36] = '';
//                        $var['error'] = 'si';
//                        $resultado_ctrl['debito'] = TRUE;
                        //$var['descripcion_error'] .= ',percpesotalla';
                    }
                }
            }

            if ($edadBenef >= 1 && $edadBenef < 6) {
                /* mayor de 1 a?o */
                if ($codigopractica == 'CTC001A97') {
                    if ($datosdelinea[30] == null || $datosdelinea[30] == '') {
                        $datosdelinea[30] = 0;
                        $var['error'] = 'si';
                        $resultado_ctrl['debito'] = TRUE;
                        $var['descripcion_error'] .= ',peso';
                    }

                    if ($datosdelinea[31] == null || $datosdelinea[31] == '') {
                        $datosdelinea[31] = '';
                        $var['error'] = 'si';
                        $resultado_ctrl['debito'] = TRUE;
                        $var['descripcion_error'] .= ',percPesoEdad';
                    }

                    if ($datosdelinea[32] == null || $datosdelinea[32] == '') {
                        $datosdelinea[32] = 0;
                        $var['error'] = 'si';
                        $resultado_ctrl['debito'] = TRUE;
                        $var['descripcion_error'] .= ',talla';
                    }


                    if ($datosdelinea[33] == null || $datosdelinea[33] == '') {
                        $datosdelinea[33] = '';
                        $var['error'] = 'si';
                        $resultado_ctrl['debito'] = TRUE;
                        $var['descripcion_error'] .= ',percTallaEdad';
                    }
                    if (($datosdelinea[34] == null) || ($datosdelinea[34] == '')) {
                        $datosdelinea[34] = 0;
                        //$var['error'] = 'si';
//                        $resultado_ctrl['debito'] = TRUE;
                        // $var['descripcion_error'] .= ',perimcef';
                    }

                    if ($datosdelinea[36] == null || $datosdelinea[36] == '') {
                        $datosdelinea[36] = '';
//                        $var['error'] = 'si';
//                        $resultado_ctrl['debito'] = TRUE;
                        //$var['descripcion_error'] .= ',percpesotalla';
                    }

                    if ($datosdelinea[67] == null || $datosdelinea[67] == '') {
                        $datosdelinea[67] = '';
                        $var['error'] = 'si';
                        $resultado_ctrl['debito'] = TRUE;
                        $var['descripcion_error'] .= ',percIMC';
                    }

                    if ($datosdelinea[68] == null || $datosdelinea[68] == '') {
                        $datosdelinea[68] = '';
                        $var['error'] = 'si';
                        $resultado_ctrl['debito'] = TRUE;
                        $var['descripcion_error'] .= ',IMC';
                    }
                }
                if ($edadBenef >= 6 && $edadBenef < 10) {

                    if ($codigopractica == 'CTC001A97' || $codigopractica == 'CTC009A97') {
                        if ($datosdelinea[30] == null || $datosdelinea[30] == '') {
                            $datosdelinea[30] = 0;
                            $var['error'] = 'si';
                            $resultado_ctrl['debito'] = TRUE;
                            $var['descripcion_error'] .= ',peso';
                        }

                        if ($datosdelinea[31] == null || $datosdelinea[31] == '') {
                            $datosdelinea[31] = '';
                            $var['error'] = 'si';
                            $resultado_ctrl['debito'] = TRUE;
                            $var['descripcion_error'] .= ',percPesoEdad';
                        }

                        if ($datosdelinea[32] == null || $datosdelinea[32] == '') {
                            $datosdelinea[32] = 0;
                            $var['error'] = 'si';
                            $resultado_ctrl['debito'] = TRUE;
                            $var['descripcion_error'] .= ',talla';
                        }

                        if ($datosdelinea[33] == null || $datosdelinea[33] == '') {
                            $datosdelinea[33] = '';
                            $var['error'] = 'si';
                            $resultado_ctrl['debito'] = TRUE;
                            $var['descripcion_error'] .= ',percTallaEdad';
                        }

                        if (($datosdelinea[34] == null) || ($datosdelinea[34] == '')) {
                            $datosdelinea[34] = 0;
                            //$var['error'] = 'si';
                            // $var['descripcion_error'] .= ',perimcef';
                        }

                        if ($datosdelinea[36] == null || $datosdelinea[36] == '') {
                            $datosdelinea[36] = '';
//                            $var['error'] = 'si';
//                            $resultado_ctrl['debito'] = TRUE;
                            //$var['descripcion_error'] .= ',percpesotalla';
                        }

                        if ($datosdelinea[67] == null || $datosdelinea[67] == '') {
                            $datosdelinea[67] = '';
                            $var['error'] = 'si';
                            $resultado_ctrl['debito'] = TRUE;
                            $var['descripcion_error'] .= ',percIMC';
                        }

                        if ($datosdelinea[68] == null || $datosdelinea[68] == '') {
                            $datosdelinea[68] = '';
                            $var['error'] = 'si';
                            $resultado_ctrl['debito'] = TRUE;
                            $var['descripcion_error'] .= ',IMC';
                        }
                    }

                    if (($codigopractica == 'CTC001T79') || ($codigopractica == 'CTC001T82') ||
                            ($codigopractica == 'CTC002T79') || ($codigopractica == 'CTC002T82') ||
                            ($codigopractica == 'CTC001T83') || ($codigopractica == 'CTC002T83')) {

                        if ($datosdelinea[30] == null || $datosdelinea[30] == '') {
                            $datosdelinea[30] = 0;
                            $var['error'] = 'si';
                            $resultado_ctrl['debito'] = TRUE;
                            $var['descripcion_error'] .= ',peso';
                        }

                        if ($datosdelinea[32] == null || $datosdelinea[32] == '') {
                            $datosdelinea[32] = 0;
                            $var['error'] = 'si';
                            $resultado_ctrl['debito'] = TRUE;
                            $var['descripcion_error'] .= ',talla';
                        }

                        if ($datosdelinea[33] == null || $datosdelinea[33] == '') {
                            $datosdelinea[33] = '';
                            $var['error'] = 'si';
                            $resultado_ctrl['debito'] = TRUE;
                            $var['descripcion_error'] .= ',percTallaEdad';
                        }

                        if ($datosdelinea[59] == null || $datosdelinea[59] == '') {
                            $datosdelinea[59] = '';
                            $var['error'] = 'si';
                            $resultado_ctrl['debito'] = TRUE;
                            $var['descripcion_error'] .= ',TA MAX';
                        }

                        if ($datosdelinea[60] == null || $datosdelinea[60] == '') {
                            $datosdelinea[60] = '';
                            $var['error'] = 'si';
                            $resultado_ctrl['debito'] = TRUE;
                            $var['descripcion_error'] .= ',TA MIN';
                        }

                        if ($datosdelinea[67] == null || $datosdelinea[67] == '') {
                            $datosdelinea[67] = '';
                            $var['error'] = 'si';
                            $resultado_ctrl['debito'] = TRUE;
                            $var['descripcion_error'] .= ',percIMC';
                        }

                        if ($datosdelinea[68] == null || $datosdelinea[68] == '') {
                            $datosdelinea[68] = '';
                            $var['error'] = 'si';
                            $resultado_ctrl['debito'] = TRUE;
                            $var['descripcion_error'] .= ',IMC';
                        }
                    }
                }
            }
        }

        if ($datosdelinea[3] == 2) {
            /* EMBARAZADAS */

            if (($codigopractica == 'CTC005W78') || ($codigopractica == 'CTC006W78')) {
                //18=fecha del primer control
                if ($datosdelinea[18] == null) {
                    $datosdelinea[18] = '';
//                $var['error'] = 'si';
//                $resultado_ctrl['debito'] = TRUE;
                    //  $var['descripcion_error'] .= ',fecha1control';
                }
                //19= Nro. de semanas de gestaci?n en primer control
                if ($datosdelinea[19] == null || $datosdelinea[19] == '') {
                    $datosdelinea[19] = 0;
                    $var['error'] = 'si';
                    $resultado_ctrl['debito'] = TRUE;
                    $var['descripcion_error'] .= ',SemanaGestacion1Control';
                }
                //28 Fecha Probable de Parto 
                if ($datosdelinea[28] == null || $datosdelinea[28] == '') {
                    $datosdelinea[28] = '';
//                $var['error'] = 'si';
//                $resultado_ctrl['debito'] = TRUE;
                    //   $var['descripcion_error'] .= ',fpp';
                }
                //29 Fecha Ult. Mestr.
                if ($datosdelinea[29] == null || $datosdelinea[29] == '') {
                    $datosdelinea[29] = '';
//                $var['error'] = 'si';
//                $resultado_ctrl['debito'] = TRUE;
                    //   $var['descripcion_error'] .= ',fum';
                }

                if ($datosdelinea[64] == null || $datosdelinea[64] == '') {
                    $datosdelinea[64] = 0;
                    $var['error'] = 'si';
                    $resultado_ctrl['debito'] = TRUE;
                    $var['descripcion_error'] .= ',peso';
                }

                if ($datosdelinea[32] == null || $datosdelinea[32] == '') {
                    $datosdelinea[32] = 0;
                    $var['error'] = 'si';
                    $resultado_ctrl['debito'] = TRUE;
                    $var['descripcion_error'] .= ',talla';
                }

                if ($datosdelinea[59] == null || $datosdelinea[59] == '') {
                    $datosdelinea[59] = '';
                    $var['error'] = 'si';
                    $resultado_ctrl['debito'] = TRUE;
                    $var['descripcion_error'] .= ',TA MAX';
                }

                if ($datosdelinea[60] == null || $datosdelinea[60] == '') {
                    $datosdelinea[60] = '';
                    $var['error'] = 'si';
                    $resultado_ctrl['debito'] = TRUE;
                    $var['descripcion_error'] .= ',TA MIN';
                }
            }
        }

        if ($datosdelinea[3] == 3) {
            /* PARTOS */

            if (($codigopractica == 'ITQ001W90') || ($codigopractica == 'ITQ001W91') ||
                    ($codigopractica == 'ITQ002W88') || ($codigopractica == 'ITQ002W89')) {
//            if ($var['error'] == 'si') {
//                $var['ojo'] = 'si';
//            }

                if ($datosdelinea[41] == null || $datosdelinea[41] == '') {
                    $datosdelinea[41] = 0;
                    $var['error'] = 'si';
                    $resultado_ctrl['debito'] = TRUE;
                    $var['descripcion_error'] .= ', apgar';
                }

                if ($datosdelinea[48] == null || $datosdelinea[48] == '') {
                    $datosdelinea[48] = '';
                    //$var['error'] = 'si';
                    // $var['descripcion_error'] .= ', consejeria';
                }

                if ($datosdelinea[40] == null || $datosdelinea[40] == '0' || $datosdelinea[40] == '') {
                    $datosdelinea[40] = -1;
                    $var['error'] = 'si';
                    $resultado_ctrl['debito'] = TRUE;
                    $var['descripcion_error'] .= ', pesoAlNacer';
                }
                if ($datosdelinea[42] == null || $datosdelinea[42] == '') {
                    $datosdelinea[42] = '';
                    $resultado_ctrl['debito'] = TRUE;
                    $var['error'] = 'si';
                    $var['descripcion_error'] .= ', vdrl';
                }
                if ($datosdelinea[43] == null || $datosdelinea[43] == '') {
                    $datosdelinea[43] = '';
                    $resultado_ctrl['debito'] = TRUE;
                    $var['error'] = 'si';
                    $var['descripcion_error'] .= ',antitetanica';
                }
            }
            if ($codigopractica == 'CTC001W86') {
                if ($datosdelinea[13] == null || $datosdelinea[13] == '') {
                    $datosdelinea[13] = '';
                    $resultado_ctrl['debito'] = TRUE;
                    $var['error'] = 'si';
                    $var['descripcion_error'] .= ', fecParto';
                }
            }
        }



        if ($datosdelinea[3] == 36) {
            //ADOLECENTES

            if (($codigopractica == 'CTC009A97') || ($codigopractica == 'CTC001A97')) {
                if ($datosdelinea[30] == null || $datosdelinea[30] == '') {
                    $datosdelinea[30] = 0;
                    $var['error'] = 'si';
                    $resultado_ctrl['debito'] = TRUE;
                    $var['descripcion_error'] .= ',peso';
                }
                if ($datosdelinea[31] == null || $datosdelinea[31] == '') {
                    $datosdelinea[31] = '';
                    //$resultado_ctrl['debito'] = TRUE;
                    //  $var['error'] = 'si';
                    //$var['descripcion_error'] .= ',percpesoedad';
                }
                if ($datosdelinea[32] == null || $datosdelinea[32] == '') {
                    $datosdelinea[32] = 0;
                    $var['error'] = 'si';
                    $resultado_ctrl['debito'] = TRUE;
                    $var['descripcion_error'] .= ',talla';
                }
                if ($datosdelinea[33] == null || $datosdelinea[33] == '') {
                    $datosdelinea[33] = '';
                    $resultado_ctrl['debito'] = TRUE;
                    $var['error'] = 'si';
                    $var['descripcion_error'] .= ',percTallaEdad';
                }
                if (($datosdelinea[34] == null) || ($datosdelinea[34] == '')) {
                    $datosdelinea[34] = 0;
                    //$var['error'] = 'si';
                    // $var['descripcion_error'] .= ',perimcef';
                }
                if ($datosdelinea[36] == null || $datosdelinea[36] == '') {
                    $datosdelinea[36] = '';
                    //$var['error'] = 'si';
                    //$var['descripcion_error'] .= ',percpesotalla';
                }
                if ($datosdelinea[59] == null || $datosdelinea[59] == '') {
                    $datosdelinea[59] = '';
                    $var['error'] = 'si';
                    $resultado_ctrl['debito'] = TRUE;
                    $var['descripcion_error'] .= ',TA MAX';
                }

                if ($datosdelinea[60] == null || $datosdelinea[60] == '') {
                    $datosdelinea[60] = '';
                    $var['error'] = 'si';
                    $resultado_ctrl['debito'] = TRUE;
                    $var['descripcion_error'] .= ',TA MIN';
                }
                if ($datosdelinea[67] == null || $datosdelinea[67] == '') {
                    $datosdelinea[67] = '';
                    $var['error'] = 'si';
                    $resultado_ctrl['debito'] = TRUE;
                    $var['descripcion_error'] .= ',perc IMC';
                }
                if ($datosdelinea[68] == null || $datosdelinea[68] == '') {
                    $datosdelinea[68] = '';
                    $var['error'] = 'si';
                    $resultado_ctrl['debito'] = TRUE;
                    $var['descripcion_error'] .= ',IMC';
                }
            }

            if (($codigopractica == 'CTC001T79') || ($codigopractica == 'CTC001T82') ||
                    ($codigopractica == 'CTC002T79') || ($codigopractica == 'CTC002T82') ||
                    ($codigopractica == 'CTC001T83') || ($codigopractica == 'CTC002T83')) {

                if ($datosdelinea[30] == null || $datosdelinea[30] == '') {
                    $datosdelinea[30] = 0;
                    $var['error'] = 'si';
                    $resultado_ctrl['debito'] = TRUE;
                    $var['descripcion_error'] .= ',peso';
                }

                if ($datosdelinea[32] == null || $datosdelinea[32] == '') {
                    $datosdelinea[32] = 0;
                    $var['error'] = 'si';
                    $resultado_ctrl['debito'] = TRUE;
                    $var['descripcion_error'] .= ',talla';
                }

                if ($datosdelinea[33] == null || $datosdelinea[33] == '') {
                    $datosdelinea[33] = '';
                    $resultado_ctrl['debito'] = TRUE;
                    $var['error'] = 'si';
                    $var['descripcion_error'] .= ',percTallaEdad';
                }

                if ($datosdelinea[59] == null || $datosdelinea[59] == '') {
                    $datosdelinea[59] = '';
                    $var['error'] = 'si';
                    $resultado_ctrl['debito'] = TRUE;
                    $var['descripcion_error'] .= ',TA MAX';
                }

                if ($datosdelinea[60] == null || $datosdelinea[60] == '') {
                    $datosdelinea[60] = '';
                    $var['error'] = 'si';
                    $resultado_ctrl['debito'] = TRUE;
                    $var['descripcion_error'] .= ',TA MIN';
                }
                if ($datosdelinea[67] == null || $datosdelinea[67] == '') {
                    $datosdelinea[67] = '';
                    $var['error'] = 'si';
                    $resultado_ctrl['debito'] = TRUE;
                    $var['descripcion_error'] .= ',perc IMC';
                }
                if ($datosdelinea[68] == null || $datosdelinea[68] == '') {
                    $datosdelinea[68] = '';
                    $var['error'] = 'si';
                    $resultado_ctrl['debito'] = TRUE;
                    $var['descripcion_error'] .= ',IMC';
                }
            }
        }

        if ($datosdelinea[3] == 37) {
            //ADULTOS
            if (($codigopractica == 'CTC009A97') || ($codigopractica == 'CTC001A97')) {
                if ($datosdelinea[30] == null || $datosdelinea[30] == '') {
                    $datosdelinea[30] = 0;
                    $var['error'] = 'si';
                    $resultado_ctrl['debito'] = TRUE;
                    $var['descripcion_error'] .= ',peso';
                }

                if ($datosdelinea[32] == null || $datosdelinea[32] == '') {
                    $datosdelinea[32] = 0;
                    $var['error'] = 'si';
                    $resultado_ctrl['debito'] = TRUE;
                    $var['descripcion_error'] .= ',talla';
                }

                if ($datosdelinea[59] == null || $datosdelinea[59] == '') {
                    $datosdelinea[59] = '';
                    $var['error'] = 'si';
                    $resultado_ctrl['debito'] = TRUE;
                    $var['descripcion_error'] .= ',TA MAX';
                }

                if ($datosdelinea[60] == null || $datosdelinea[60] == '') {
                    $datosdelinea[60] = '';
                    $var['error'] = 'si';
                    $resultado_ctrl['debito'] = TRUE;
                    $var['descripcion_error'] .= ',TA MIN';
                }
            }
        }

        if ($resultado_ctrl['debito']) {
            $resultado_ctrl['msj_error'] = 'Faltan datos requeridos:' . $var['descripcion_error'];
            $resultado_ctrl['id_error'] = 12;
        }


        if ($datosdelinea[3] == 38) {
            //TAL

            if ($codigopractica == 'ITE001R78' || $codigopractica == 'ITE002R78') {
                if ($datosdelinea[15] == null || $datosdelinea[15] == '' || intval($datosdelinea[15]) < 0) {
                    $resultado_ctrl['debito'] = TRUE;
                    $var['error'] = 'si';
                    $var['descripcion_error'] .= ',TAL';
                    $resultado_ctrl['id_error'] = '12';
                    $resultado_ctrl['msj_error'] .= 'Faltan Datos Requeridos: TAL';
                    break;
                } else {
                    if ($codigopractica == 'ITE001R78' && intval($datosdelinea[15]) > 6) {
                        $var['error'] = 'si';
                        $var['descripcion_error'] .= ',TAL';
                        $resultado_ctrl['debito'] = TRUE;
                        $resultado_ctrl['id_error'] = '12';
                        $resultado_ctrl['msj_error'] .= 'Error de Datos Requeridos: TAL > 6';
                        break;
                    } elseif ($codigopractica == 'ITE002R78' && intval($datosdelinea[15]) < 7) {
                        $var['error'] = 'si';
                        $var['descripcion_error'] .= ',TAL';
                        $resultado_ctrl['debito'] = TRUE;
                        $resultado_ctrl['id_error'] = '12';
                        $resultado_ctrl['msj_error'] .= 'Error de Datos Requeridos: TAL < 7';
                        break;
                    }
                }
            }
        }
    } while (false);
    return $resultado_ctrl;
}

function nomencladoresQueNoSeControlan($datos_nomenclador) {
    $secontrola = TRUE;
    $codigo = explode(" ", trim($datos_nomenclador));
    if (trim($codigo[0]) == "CMI" and trim($codigo[1]) != '68') {
        $secontrola = FALSE;
    }

    if (trim($codigo[0]) == "RO") {
        $secontrola = FALSE;
    }

    return $secontrola;
}

function efectorTieneConvenio($cuie, $lafechacomprobante) {
    $ctrl['debito'] = false;
    $convenio = buscarConvenio($cuie, $lafechacomprobante);
    if ($convenio == null) {
        $ctrl['debito'] = true;
        $ctrl['msj_error'] = 'Efector sin convenio al momento de la prestacion.';
        $ctrl['id_error'] = 68;
    }
    $ctrl['convenio'] = $convenio;
    return $ctrl;
}

function practicaMenor6Meses($id_factura, $fechapractica) {
    // Devuelve true si no supera 6 meses
    $ctrl['debito'] = false;
    $query = "SELECT CASE WHEN DATE(DATE_PART('year', fecha_entrada)||'-'||(DATE_PART('month', fecha_entrada))||
                '-'||01)-(30*6)< to_date('" . str_replace(' / ', '- ', $fechapractica) . "', 'DD-MM-YYYY') THEN TRUE ELSE FALSE END menosde6mesesantiguedad
                FROM facturacion.factura
                WHERE factura.id_factura='" . $id_factura . "'";
    $res_sql4 = sql($query) or fin_pagina();
    if ($res_sql4->fields['menosde6mesesantiguedad'] == 'f') {
        $ctrl['msj_error'] = 'Prestacion posee mas de 6 meses de antiguedad';
        $ctrl['id_error'] = 66;
        $ctrl['debito'] = true;
    }

    return $ctrl;
}

function controlarAfiliado($clasedoc, $tipodoc, $afidni, $control_afi, $clavebenefseguntxt) {
    //Busca el afiliado

    if ($control_afi['id'] != 0) {
        $control_afi['debito'] = false;
        //Si lo encuentra inscripto
        if (trim($control_afi['clasedoc']) == $clasedoc && $control_afi['tipodoc'] == $tipodoc && $control_afi['afidni'] == $afidni) {
            //si esta todo bien:
            $control_afi['debito'] = false;
        } else {
            //si no concuerdan los datos del dni
            if ($afidni != $control_afi['afidni'] &&
                    $afidni != $control_afi['manrodocumento'] &&
                    $afidni != $control_afi['panrodocumento'] &&
                    $afidni != $control_afi['otronrodocumento']) {
                if ($control_afi['clasedoc'] == "A") {
                    $coincide = buscameConDocumentoPropioEnPadron($control_afi['clavebeneficiario'], $afidni);
                    if (!$coincide) {
                        $control_afi['debito'] = true;
                        $control_afi['msj_error'] = 'DNI inscorrecto';
                        $control_afi['id_error'] = 31;
                    }
                }
            } else {
                $control_afi['msj_error'] = 'DNI no corresponde con la clave de beneficiario';
                $control_afi['id_error'] = 50;
                $control_afi['debito'] = true;
            }
        }
    } else {
        if ($control_afi['msj_error'] != '') {
            $control_afi['debito'] = true;
        } else {
            //aca agregar control 2 meses


            if (beneficiarioEstaEnAlgunLado($clavebenefseguntxt)) {
                $control_afi['debito'] = true;
                $control_afi['msj_error'] = "Inscripcion tardia en plan nacer - Mayor a 2 meses";
                $control_afi['id_error'] = 78;
            } else {
                $control_afi['debito'] = true;
                $control_afi['msj_error'] = "No esta inscripto en el Plan NACER";
                $control_afi['id_error'] = 18;
            }
        }
    }
    return $control_afi;
}

function fechaPrestacionXNacimiento($fprestacion, $fnacimiento) {

    //$nc = /* floor */(abs(strtotime($fprestacion) - strtotime($fnacimiento))); /// 86400); //fecha prestacion-fecha nacimiento

    $fp = strtotime(Fecha_db($fprestacion));
    $fn = strtotime(Fecha_db($fnacimiento));
    $nc = $fp - $fn;
    //$nc = $fp->diff($fn); /// 86400); //fecha prestacion-fecha nacimiento
    $ctrl_fechapresta['debito'] = false;
    if ($nc < 0) {
        $ctrl_fechapresta['debito'] = true;
        $ctrl_fechapresta['id_error'] = 70;
        $ctrl_fechapresta['msj_error'] = 'La Fecha de Prestacion es anterior a la Fecha de Nacimiento';
    }
    return $ctrl_fechapresta;
}

function fechaPrestacionXInscripcion($fprestacion, $fechainscripcion) {
    $fp = strtotime(Fecha_db($fprestacion));
    $fi = strtotime(Fecha_db($fechainscripcion));
    $fixfn = $fp - $fi;

    //$fixfn = floor(abs(strtotime($fprestacion) - strtotime($fechainscripcion)) / 86400); //fecha de prestacion-fecha inscripcion
    $ctrl_fechapresta['resultado'] = false;
    if ($fixfn < 0) {
        $ctrl_fechapresta['debito'] = true;
        $ctrl_fechapresta['id_error'] = 72;
        $ctrl_fechapresta['msj_error'] = 'Prestacion realizada antes de la inscripcion del Beneficiario';
    }
    return $ctrl_fechapresta;
}

function mayorDe1SinDocumento($clasedoc, $edaddias) {
    $ctrl['debito'] = false;


    if ($edaddias >= 365 and strtoupper($clasedoc) == 'A') {
        $ctrl['msj_error'] = 'Niño mayor de 1 año sin documento propio';
        $ctrl['id_error'] = 63;
        $ctrl['debito'] = true;
    }
    return $ctrl;
}

function controlInformado($cui, $practi, $fecha, $tipofacturacion) {
    $resultado_ctrl['debito'] = false;
    if ($tipofacturacion == 'R') {
        $querysiesinformado = "SELECT r.fecha_rec
                FROM facturacion.informados i
                LEFT JOIN facturacion.recepcion r USING(idrecepcion)
                WHERE i.fechaactual=to_date('$fecha', 'DD-MM-YYYY')
                AND i.idprestacion=$practi
                AND i.cuie='$cui'";
        $res_informado = sql($querysiesinformado) or fin_pagina();
        if ($res_informado->recordCount() > 0) {
            $resultado_ctrl['fecha'] = $res_informado->fields['fecha_rec'];
        } else {
            $fechaentrada = null;
            $resultado_ctrl['msj_error'] = 'Prestacion no informada oportunamente';
            $resultado_ctrl['id_error'] = '64';
            $resultado_ctrl['debito'] = TRUE;
        }
    }
    return $resultado_ctrl;
}

?>
