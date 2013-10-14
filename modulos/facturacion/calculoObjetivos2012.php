<?php

require_once("../../config.php");
require_once("../../lib/funciones_misiones.php");
require_once("../../clases/PeriodoObjetivo.php");

function obtenerEstimulo($nro_expediente) {
    $queryestimulos = "SELECT * from facturacion.estimulos
                     WHERE nro_exp='$nro_expediente'";
    $estimulosobtenidos = sql($queryestimulos) or die;

    if ($estimulosobtenidos->rowcount() > 0) {
        $estimulocalculado[0] = true;
        while (!$estimulosobtenidos->EOF) {
            $uncuie = $estimulosobtenidos->fields['cuie'];
            $puntos = $estimulosobtenidos->fields['puntos'];
            $liq = $estimulosobtenidos->fields['liq_total'];
            $estimuloaux['puntos'] = $puntos;
            $estimuloaux['liquidacion'] = $liq;
            $estimulocalculado[$uncuie] = $estimuloaux;
            $estimulosobtenidos->movenext();
        }
    } else {
        $estimulocalculado[0] = false;
    }
    return $estimulocalculado;
}

function calcularObjetivos2013($cuie, $ano, $mes, $tipoefector) {
    $resul_objetivo[12]['puntos'] = 0;
    $resul_objetivo[12] = objetivoCEB($cuie, $ano, $mes, $tipoefector);
    if ($resul_objetivo[12]['puntos'] == 0) {
        $resul_objetivo[11]['puntos'] = 10; //para puntos fijos
        $resul_objetivo[11]['objetivonro'] = 11;
        $resul_objetivo[11]['encontro'] = true;
        $resul_objetivo[11]['meta'] = 1;
        $resul_objetivo[11]['cumplido'] = 'SI';
    }

    return $resul_objetivo;
}

function calcularObjetivos2012($cuie, $ano, $mes, $tipoefector, $tiponomenclador) {
    $resul_objetivo[0]['puntos'] = 0;
    $resul_objetivo[1]['puntos'] = 0;
    $resul_objetivo[2]['puntos'] = 0;
    $resul_objetivo[3]['puntos'] = 0;
    $resul_objetivo[4]['puntos'] = 0;
    $resul_objetivo[5]['puntos'] = 0;
    $resul_objetivo[6]['puntos'] = 0;
    $resul_objetivo[7]['puntos'] = 0;
    $resul_objetivo[8]['puntos'] = 0;
    $resul_objetivo[9]['puntos'] = 0;
    $resul_objetivo[10]['puntos'] = 0;

    $resul_objetivo[11]['puntos'] = 10; //para puntos fijos
    $resul_objetivo[11]['objetivonro'] = 11;
    $resul_objetivo[11]['encontro'] = true;
    $resul_objetivo[11]['meta'] = 1;
    $resul_objetivo[11]['cumplido'] = 'SI';

    switch ($cuie) {
        case 'N95614': //direccion zona capital
            $resul_objetivo[10]['puntos'] = 12.5; //88% efector - 12% estimulos
            $resul_objetivo[10]['objetivonro'] = 10;
            $resul_objetivo[10]['encontro'] = true;
            $resul_objetivo[10]['meta'] = 1;
            $resul_objetivo[10]['cumplido'] == 'SI';

            $resul_objetivo[11]['puntos'] = 0; //para puntos fijos
            break;
        case 'N95727': //direccion zona centro parana
            $resul_objetivo[11]['puntos'] = 0; //no distribuye estimulo
            $resul_objetivo[11]['encontro'] = false;
            $resul_objetivo[11]['meta'] = 0;
            $resul_objetivo[11]['cumplido'] = 'NO';
            break;
        case 'N95611': //direccion zona sur
            $resul_objetivo[10]['puntos'] = 20; //70% para efector - 30% estimulos
            $resul_objetivo[10]['encontro'] = true;
            $resul_objetivo[10]['objetivonro'] = 10;
            $resul_objetivo[10]['meta'] = 1;
            $resul_objetivo[10]['cumplido'] == 'SI';
            break;
        case 'N95729': //direccion zona noreste
            $resul_objetivo[10]['puntos'] = 15; //75% para efector - 25% estimulos
            $resul_objetivo[10]['encontro'] = true;
            $resul_objetivo[10]['objetivonro'] = 10;
            $resul_objetivo[10]['meta'] = 1;
            $resul_objetivo[10]['cumplido'] == 'SI';
            break;
        case 'N95728': //direccion zona centro uruguay
            $resul_objetivo[10]['puntos'] = 20; //70% para efector - 30% estimulos
            $resul_objetivo[10]['encontro'] = true;
            $resul_objetivo[10]['objetivonro'] = 10;
            $resul_objetivo[10]['meta'] = 1;
            $resul_objetivo[10]['cumplido'] == 'SI';
            break;
        case 'N95613': //direccion zona Norte Parana
            $resul_objetivo[10]['puntos'] = 15; //50% para efector - 50% estimulos
            $resul_objetivo[10]['encontro'] = true;
            $resul_objetivo[10]['objetivonro'] = 10;
            $resul_objetivo[10]['meta'] = 1;
            $resul_objetivo[10]['cumplido'] == 'SI';
            break;
        case 'N95750': //Colegio de Nutricionistas
            $resul_objetivo[10]['puntos'] = 40; //50% para efector - 50% estimulos
            $resul_objetivo[10]['encontro'] = true;
            $resul_objetivo[10]['objetivonro'] = 10;
            $resul_objetivo[10]['meta'] = 1;
            $resul_objetivo[10]['cumplido'] == 'SI';
            break;
        case 'N05435': //Htal Pedro Baliña
            $resul_objetivo[10]['puntos'] = 40; //50% para efector - 50% estimulos
            $resul_objetivo[10]['encontro'] = true;
            $resul_objetivo[10]['objetivonro'] = 10;
            $resul_objetivo[10]['meta'] = 1;
            break;
        case 'N20040': //Banco de sangre
            $resul_objetivo[10]['puntos'] = 40; //50% para efector - 50% estimulos
            $resul_objetivo[10]['encontro'] = true;
            $resul_objetivo[10]['objetivonro'] = 10;
            $resul_objetivo[10]['meta'] = 1;
            $resul_objetivo[10]['cumplido'] == 'SI';
            break;
        case 'N20020': //Red de Traslados
            $resul_objetivo[10]['puntos'] = 40; //50% para efector - 50% estimulos
            $resul_objetivo[10]['encontro'] = true;
            $resul_objetivo[10]['objetivonro'] = 10;
            $resul_objetivo[10]['meta'] = 1;
            $resul_objetivo[10]['cumplido'] == 'SI';
            break;
        case 'N95792': //Munic Santa Ana
            $resul_objetivo[10]['puntos'] = 40; //50% para efector - 50% estimulos
            $resul_objetivo[10]['encontro'] = true;
            $resul_objetivo[10]['objetivonro'] = 10;
            $resul_objetivo[10]['meta'] = 1;
            $resul_objetivo[10]['cumplido'] == 'SI';
            break;
        case 'N95790': //Unidad de Coordinacion de Atencion Temprana (cambio el 07-02-2013)
            $resul_objetivo[10]['puntos'] = 40; //50% para efector - 50% estimulos
            $resul_objetivo[10]['encontro'] = true;
            $resul_objetivo[10]['objetivonro'] = 10;
            $resul_objetivo[10]['meta'] = 1;
            $resul_objetivo[10]['cumplido'] == 'SI';
            break;
        case 'N95807': //LACMI (agregado el 13-02-2013)
            $resul_objetivo[10]['puntos'] = 40; //50% para efector - 50% estimulos
            $resul_objetivo[10]['encontro'] = true;
            $resul_objetivo[10]['objetivonro'] = 10;
            $resul_objetivo[10]['meta'] = 1;
            $resul_objetivo[10]['cumplido'] == 'SI';
            break;
        default:
            $resul_objetivo[0] = objetivo1($cuie, $ano, $mes, $tipoefector);
            $resul_objetivo[1] = objetivo2($cuie, $ano, $mes, $tipoefector);
            $resul_objetivo[2] = objetivo3($cuie, $ano, $mes, $tipoefector);
            $resul_objetivo[3] = objetivo4($cuie, $ano, $mes, $tipoefector);
            $resul_objetivo[4] = objetivo5($cuie, $ano, $mes, $tipoefector);
            $resul_objetivo[5] = objetivo6($cuie, $ano, $mes, $tipoefector);
            $resul_objetivo[6] = objetivo7($cuie, $ano, $mes, $tipoefector);
            $resul_objetivo[7] = objetivo8($cuie, $ano, $mes, $tipoefector);
            $resul_objetivo[8] = objetivo9($cuie, $ano, $mes, $tipoefector);
            $resul_objetivo[9] = objetivo10($cuie, $ano, $mes, $tipoefector);
            break;
    }
    if ($tiponomenclador == 'RONDAS') {
        $resul_objetivo[10]['puntos'] = 0;
    }
    return $resul_objetivo;
}

function calcularObjetivos($cuie, $ano, $mes, $tipoefector, $tiponomenclador, $fecha_recepcion) {
    $fecha_recepcion_time = strtotime($fecha_recepcion);
    $fecha_nuevo_objetivo_time = strtotime('2013-05-13');
    if ($fecha_recepcion_time >= $fecha_nuevo_objetivo_time) {

        $periodo = PeriodoObjetivo::calcularPeriodo($fecha_recepcion);
        if (!is_null($periodo)) {
            $periodo_array = split('/', $periodo->getPeriodo());
            $ano = $periodo_array[1];
            $mes = $periodo_array[0];

            $resul_objetivo = calcularObjetivos2013($cuie, $ano, $mes, $tipoefector);
        }

        //si no se encontraron puntos va todo cero, ni los puntos base quedan
        $haypuntos = false;
        if (is_array($resul_objetivo)) {
            foreach ($resul_objetivo as $unobjetivo) {
                if ($unobjetivo['encontro'] == true && $unobjetivo['objetivonro'] != 11) {
                    $haypuntos = true;
                    break;
                }
            }
        }
        if (!$haypuntos) {
            $resul_objetivo = 0;
        }
    } else {
        $resul_objetivo = calcularObjetivos2012($cuie, $ano, $mes, $tipoefector, $tiponomenclador);
    }


    return $resul_objetivo;
}

/*
 * Modificado el 25/09/2013
 * 
 * Devuelve los puntos correspondientes a este objetivo
 * Ej. objetivoCEB('N05163','2013','07','CSA')
 * devuelve array(8) (
  [numerador] => (string) 1181
  [denominador] => (string) 2310
  [meta] => null
  [cumplido] => (string) SI
  [puntos] => (int) 50
  [total_perc] => (float) 51.125541125541
  [encontro] => (bool) true
  [objetivonro] => (int) 12
  )
 */

function objetivoCEB($cuie, $ano, $mes, $tipoefector) {
    $puntos = 0;
    $encontro = false;

    $querydenominador = "SELECT numerador,denominador FROM facturacion.objetivos
                            WHERE cuie='$cuie'
                            AND ano='$ano'
                            AND mes='$mes'
                            AND obj='1'";
    $numeradordenominador = sql($querydenominador);
    if ($numeradordenominador->rowcount() > 0) {
        if ($numeradordenominador->fields['numerador'] > $numeradordenominador->fields['denominador']) {
            $numeradordenominador->movenext();
        }

        $encontro = true;
        $numeradordenominador->fields['numerador'] ? $numerador = $numeradordenominador->fields['numerador'] : $numerador = 0;
        $numeradordenominador->fields['denominador'] ? $denominador = $numeradordenominador->fields['denominador'] : $denominador = 0;
        if ($numerador != 0) {
            $total_perc = ($numerador * 100) / $denominador;
        } else {
            $total_perc = 0;
        }

        $meta = '1';

        $cumplido = 'SI';
        if ($total_perc <= 0) {
            $cumplido = 'NO';
        }
        if ($total_perc > 0 && $total_perc <= 10) {
            $puntos = 10;
        } elseif ($total_perc > 10 && $total_perc <= 20) {
            $puntos = 20;
        } elseif ($total_perc > 20 && $total_perc <= 30) {
            $puntos = 30;
        } elseif ($total_perc > 30 && $total_perc <= 40) {
            $puntos = 40;
        } elseif ($total_perc > 40) {
            $puntos = 50;
        }

        $result['numerador'] = $numerador;
        $result['denominador'] = $denominador;
        $result['meta'] = $meta;
        $result['cumplido'] = $cumplido;
        $result['puntos'] = $puntos;
        $result['total_perc'] = $total_perc;
        $result['encontro'] = $encontro;
        $result['objetivonro'] = 12;
        return $result;
    }
}

function objetivo1($cuie, $ano, $mes, $tipoefector) {
    $puntos = 0;
    $encontro = false;
    if ($tipoefector == 'PSB' || $tipoefector == 'CSA' || $tipoefector == 'ALD' || $tipoefector == 'HOS' || $tipoefector == 'HOS3') {
        $querydenominador = "SELECT numerador,denominador FROM facturacion.objetivos
                            WHERE cuie='$cuie'
                            AND ano='$ano'
                            AND mes='$mes'
                            AND obj='1'";
        $numeradordenominador = sql($querydenominador);
        if ($numeradordenominador->rowcount() > 0) {
            if ($numeradordenominador->fields['numerador'] > $numeradordenominador->fields['denominador']) {
                $numeradordenominador->movenext();
            }
            $encontro = true;
            $numeradordenominador->fields['numerador'] ? $numerador = $numeradordenominador->fields['numerador'] : $numerador = 0;
            $numeradordenominador->fields['denominador'] ? $denominador = $numeradordenominador->fields['denominador'] : $denominador = 0;
            if ($numerador != 0) {
                $total_perc = ($numerador * 100) / $denominador;
            } else {
                $total_perc = 0;
                if ($verifica == 'N05435' || $verifica == 'N20032') {
                    $total_perc = 100;
                }
            }

            $meta = '60';

            if ($total_perc < $meta) {
                $puntos = 0;
                $cumplido = 'NO';
            } else {
                $cumplido = 'SI';

                if ($total_perc >= $meta && $total_perc < 80) {
                    if ($tipoefector == 'PSB' || $tipoefector == 'CSA' || $tipoefector == 'ALD') {
                        $puntos = 6;
                    } elseif ($tipoefector == 'HOS' || $tipoefector == 'HOS3') {
                        $puntos = 1;
                    }
                } elseif ($total_perc >= 80 && $total_perc < 100) {
                    if ($tipoefector == 'PSB' || $tipoefector == 'CSA' || $tipoefector == 'ALD') {
                        $puntos = 8;
                    } elseif ($tipoefector == 'HOS' || $tipoefector == 'HOS3') {
                        $puntos = 3;
                    }
                } elseif ($total_perc >= 100) {
                    if ($tipoefector == 'PSB' || $tipoefector == 'CSA' || $tipoefector == 'ALD') {
                        $puntos = 10;
                    } elseif ($tipoefector == 'HOS' || $tipoefector == 'HOS3') {
                        $puntos = 5;
                    }
                }
            }
        }
    }

    $result['numerador'] = $numerador;
    $result['denominador'] = $denominador;
    $result['meta'] = $meta;
    $result['cumplido'] = $cumplido;
    $result['puntos'] = $puntos;
    $result['total_perc'] = $total_perc;
    $result['encontro'] = $encontro;
    $result['objetivonro'] = 0;
    return $result;
}

function objetivo2($cuie, $ano, $mes, $tipoefector) {
    $puntos = 0;
    $encontro = false;
    if ($tipoefector == 'HOS' || $tipoefector == 'HOS1' || $tipoefector == 'HOS3') {
        $querydenominador = "SELECT numerador,denominador FROM facturacion.objetivos
                            WHERE cuie='$cuie'
                            AND ano='$ano'
                            AND mes='$mes'
                            AND obj='2'";
        $numeradordenominador = sql($querydenominador);
        if ($numeradordenominador->rowcount() > 0) {
            if ($numeradordenominador->fields['numerador'] > $numeradordenominador->fields['denominador']) {
                $numeradordenominador->movenext();
            }
            $encontro = true;
            $numeradordenominador->fields['numerador'] ? $numerador = $numeradordenominador->fields['numerador'] : $numerador = 0;
            $numeradordenominador->fields['denominador'] ? $denominador = $numeradordenominador->fields['denominador'] : $denominador = 0;
            if ($numerador != 0) {
                $total_perc = ($numerador * 100) / $denominador;
            } else {
                $total_perc = 0;
                if ($verifica == 'N05435' || $verifica == 'N20032') {
                    $total_perc = 100;
                }
            }

            $meta = '70';

            if ($total_perc < $meta) {
                $puntos = 0;
                $cumplido = 'NO';
            } else {
                $cumplido = 'SI';
                if ($tipoefector == 'HOS1' || $tipoefector == 'HOS3') {
                    if ($total_perc >= $meta && $total_perc < 80) {
                        $puntos = 2;
                    } elseif ($total_perc >= 80 && $total_perc < 90) {
                        $puntos = 3;
                    } elseif ($total_perc >= 90 && $total_perc < 100) {
                        $puntos = 4;
                    } elseif ($total_perc >= 100) {
                        $puntos = 5;
                    }
                } else {
                    if ($total_perc >= 70 && $total_perc < 80) {
                        $puntos = 1;
                    } elseif ($total_perc >= 80 && $total_perc < 100) {
                        $puntos = 2;
                    } elseif ($total_perc >= 100) {
                        $puntos = 2.5;
                    }
                }
            }
        }
    }
    $result['numerador'] = $numerador;
    $result['denominador'] = $denominador;
    $result['meta'] = $meta;
    $result['cumplido'] = $cumplido;
    $result['puntos'] = $puntos;
    $result['total_perc'] = $total_perc;
    $result['encontro'] = $encontro;
    $result['objetivonro'] = 1;
    return $result;
}

function objetivo3($cuie, $ano, $mes, $tipoefector) {
    $puntos = 0;
    $encontro = false;
    if ($tipoefector == 'HOS' || $tipoefector == 'HOS1' || $tipoefector == 'HOS3') {
        $querydenominador = "SELECT numerador,denominador FROM facturacion.objetivos
                            WHERE cuie='$cuie'
                            AND ano='$ano'
                            AND mes='$mes'
                            AND obj='3'";
        $numeradordenominador = sql($querydenominador);
        if ($numeradordenominador->rowcount() > 0) {
            if ($numeradordenominador->fields['numerador'] > $numeradordenominador->fields['denominador']) {
                $numeradordenominador->movenext();
            }
            $encontro = true;
            $numeradordenominador->fields['numerador'] ? $numerador = $numeradordenominador->fields['numerador'] : $numerador = 0;
            $numeradordenominador->fields['denominador'] ? $denominador = $numeradordenominador->fields['denominador'] : $denominador = 0;
            if ($numerador != 0) {
                $total_perc = ($numerador * 100) / $denominador;
            } else {
                $total_perc = 0;
                if ($verifica == 'N05435' || $verifica == 'N20032') {
                    $total_perc = 100;
                }
            }

            $meta = '70';

            if ($total_perc < $meta) {
                $puntos = 0;
                $cumplido = 'NO';
            } else {
                $cumplido = 'SI';
                if ($tipoefector == 'HOS1' || $tipoefector == 'HOS3') {
                    if ($total_perc >= $meta && $total_perc < 80) {
                        $puntos = 2;
                    } elseif ($total_perc >= 80 && $total_perc < 90) {
                        $puntos = 3;
                    } elseif ($total_perc >= 90 && $total_perc < 100) {
                        $puntos = 4;
                    } elseif ($total_perc >= 100) {
                        $puntos = 5;
                    }
                } else {
                    if ($total_perc >= 70 && $total_perc < 80) {
                        $puntos = 1;
                    } elseif ($total_perc >= 80 && $total_perc < 100) {
                        $puntos = 2;
                    } elseif ($total_perc >= 100) {
                        $puntos = 2.5;
                    }
                }
            }
        }
    }
    $result['numerador'] = $numerador;
    $result['denominador'] = $denominador;
    $result['meta'] = $meta;
    $result['cumplido'] = $cumplido;
    $result['puntos'] = $puntos;
    $result['total_perc'] = $total_perc;
    $result['encontro'] = $encontro;
    $result['objetivonro'] = 2;
    return $result;
}

function objetivo4($cuie, $ano, $mes, $tipoefector) {
    $puntos = 0;
    $encontro = false;
    if ($tipoefector == 'PSB' || $tipoefector == 'CSA' || $tipoefector == 'ALD' || $tipoefector == 'HOS' || $tipoefector == 'HOS1' || $tipoefector == 'HOS3') {
        $querydenominador = "SELECT numerador,denominador FROM facturacion.objetivos
                            WHERE cuie='$cuie'
                            AND ano='$ano'
                            AND mes='$mes'
                            AND obj='4'";
        $numeradordenominador = sql($querydenominador);
        if ($numeradordenominador->rowcount() > 0) {
            if ($numeradordenominador->fields['numerador'] > $numeradordenominador->fields['denominador']) {
                $numeradordenominador->movenext();
            }
            $encontro = true;
            $numeradordenominador->fields['numerador'] ? $numerador = $numeradordenominador->fields['numerador'] : $numerador = 0;
            $numeradordenominador->fields['denominador'] ? $denominador = $numeradordenominador->fields['denominador'] : $denominador = 0;
            if ($numerador != 0) {
                $total_perc = ($numerador * 100) / $denominador;
            } else {
                $total_perc = 0;
                if ($verifica == 'N05435' || $verifica == 'N20032') {
                    $total_perc = 100;
                }
            }

            $meta = '60';

            if ($total_perc < $meta) {
                $puntos = 0;
                $cumplido = 'NO';
            } else {
                $cumplido = 'SI';
                if ($total_perc >= $meta && $total_perc < 80) {
                    if ($tipoefector == 'HOS1') {
                        $puntos = 3;
                    } elseif ($tipoefector == 'PSB' || $tipoefector == 'CSA' || $tipoefector == 'ALD') {
                        $puntos = 6;
                    } elseif ($tipoefector == 'HOS' || $tipoefector == 'HOS3') {
                        $puntos = 1;
                    }
                } elseif ($total_perc >= 80 && $total_perc < 100) {
                    if ($tipoefector == 'HOS1') {
                        $puntos = 4;
                    } elseif ($tipoefector == 'PSB' || $tipoefector == 'CSA' || $tipoefector == 'ALD') {
                        $puntos = 8;
                    } elseif ($tipoefector == 'HOS' || $tipoefector == 'HOS3') {
                        $puntos = 2;
                    }
                } elseif ($total_perc >= 100) {
                    if ($tipoefector == 'HOS1') {
                        $puntos = 5;
                    } elseif ($tipoefector == 'PSB' || $tipoefector == 'CSA' || $tipoefector == 'ALD') {
                        $puntos = 10;
                    } elseif ($tipoefector == 'HOS' || $tipoefector == 'HOS3') {
                        $puntos = 2.5;
                    }
                }
            }
        }
    }
    $result['numerador'] = $numerador;
    $result['denominador'] = $denominador;
    $result['meta'] = $meta;
    $result['cumplido'] = $cumplido;
    $result['puntos'] = $puntos;
    $result['total_perc'] = $total_perc;
    $result['encontro'] = $encontro;
    $result['objetivonro'] = 3;
    return $result;
}

function objetivo5($cuie, $ano, $mes, $tipoefector) {
    $puntos = 0;
    $encontro = false;
    if ($tipoefector == 'HOS' || $tipoefector == 'HOS1' || $tipoefector == 'HOS3') {
        $querydenominador = "SELECT numerador,denominador FROM facturacion.objetivos
                            WHERE cuie='$cuie'
                            AND ano='$ano'
                            AND mes='$mes'
                            AND obj='5'";
        $numeradordenominador = sql($querydenominador);
        if ($numeradordenominador->rowcount() > 0) {
            if ($numeradordenominador->fields['numerador'] > $numeradordenominador->fields['denominador']) {
                $numeradordenominador->movenext();
            }
            $encontro = true;
            $numeradordenominador->fields['numerador'] ? $numerador = $numeradordenominador->fields['numerador'] : $numerador = 0;
            $numeradordenominador->fields['denominador'] ? $denominador = $numeradordenominador->fields['denominador'] : $denominador = 0;
            if ($numerador != 0) {
                $total_perc = ($numerador * 100) / $denominador;
            } else {
                $total_perc = 0;
                if ($verifica == 'N05435' || $verifica == 'N20032') {
                    $total_perc = 100;
                }
            }

            $meta = '80';

            if ($total_perc < $meta) {
                $puntos = 0;
                $cumplido = 'NO';
            } else {
                $cumplido = 'SI';
                if ($total_perc >= $meta && $total_perc < 90) {
                    if ($tipoefector == 'HOS' || $tipoefector == 'HOS1' || $tipoefector == 'HOS3') {
                        $puntos = 1;
                    }
                } elseif ($total_perc >= 90 && $total_perc < 100) {
                    if ($tipoefector == 'HOS' || $tipoefector == 'HOS3') {
                        $puntos = 2;
                    } elseif ($tipoefector == 'HOS1') {
                        $puntos = 2.5;
                    }
                } elseif ($total_perc >= 100) {
                    if ($tipoefector == 'HOS' || $tipoefector == 'HOS3') {
                        $puntos = 2.5;
                    } elseif ($tipoefector == 'HOS1') {
                        $puntos = 5;
                    }
                }
            }
        }
    }
    $result['numerador'] = $numerador;
    $result['denominador'] = $denominador;
    $result['meta'] = $meta;
    $result['cumplido'] = $cumplido;
    $result['puntos'] = $puntos;
    $result['total_perc'] = $total_perc;
    $result['encontro'] = $encontro;
    $result['objetivonro'] = 4;
    return $result;
}

function objetivo6($cuie, $ano, $mes, $tipoefector) {
    $puntos = 0;
    $encontro = false;
    if ($tipoefector == 'HOS' || $tipoefector == 'HOS1' || $tipoefector == 'HOS2' || $tipoefector == 'HOS3') {
        $querydenominador = "SELECT numerador,denominador FROM facturacion.objetivos
                            WHERE cuie='$cuie'
                            AND ano='$ano'
                            AND mes='$mes'
                            AND obj='6'";
        $numeradordenominador = sql($querydenominador);
        if ($numeradordenominador->rowcount() > 0) {
            if ($numeradordenominador->fields['numerador'] > $numeradordenominador->fields['denominador']) {
                $numeradordenominador->movenext();
            }
            $encontro = true;
            $numeradordenominador->fields['numerador'] ? $numerador = $numeradordenominador->fields['numerador'] : $numerador = 0;
            $numeradordenominador->fields['denominador'] ? $denominador = $numeradordenominador->fields['denominador'] : $denominador = 0;

            if ($numerador != 0) {
                $total_perc = ($numerador * 100) / $denominador;
            } else {
                $total_perc = 0;
                if ($verifica == 'N05435' || $verifica == 'N20032') {
                    $total_perc = 100;
                }
            }

            $meta = '100';

            if ($total_perc < $meta) {
                $puntos = 0;
                $cumplido = 'NO';
            } else {
                $cumplido = 'SI';
                if ($total_perc >= 100) {
                    if ($tipoefector == 'HOS1' || $tipoefector == 'HOS2' || $tipoefector == 'HOS3') {
                        $puntos = 10;
                    }
                    if ($tipoefector == 'HOS') {
                        $puntos = 5;
                    }
                }
            }
        }
    }
    $result['numerador'] = $numerador;
    $result['denominador'] = $denominador;
    $result['meta'] = $meta;
    $result['cumplido'] = $cumplido;
    $result['puntos'] = $puntos;
    $result['total_perc'] = $total_perc;
    $result['encontro'] = $encontro;
    $result['objetivonro'] = 5;
    return $result;
}

function objetivo7($cuie, $ano, $mes, $tipoefector) {
    $puntos = 0;
    $encontro = false;
    if ($tipoefector == 'PSB' || $tipoefector == 'CSA' || $tipoefector == 'ALD' || $tipoefector == 'HOS' || $tipoefector == 'HOS3') {
        $querydenominador = "SELECT numerador,denominador FROM facturacion.objetivos
                            WHERE cuie='$cuie'
                            AND ano='$ano'
                            AND mes='$mes'
                            AND obj='7'";
        $numeradordenominador = sql($querydenominador);
        if ($numeradordenominador->rowcount() > 0) {
            if ($numeradordenominador->fields['numerador'] > $numeradordenominador->fields['denominador']) {
                $numeradordenominador->movenext();
            }
            $encontro = true;
            $numeradordenominador->fields['numerador'] ? $numerador = $numeradordenominador->fields['numerador'] : $numerador = 0;
            $numeradordenominador->fields['denominador'] ? $denominador = $numeradordenominador->fields['denominador'] : $denominador = 0;

            if ($numerador != 0) {
                $total_perc = ($numerador * 100) / $denominador;
            } else {
                $total_perc = 0;
                if ($verifica == 'N05435' || $verifica == 'N20032') {
                    $total_perc = 100;
                }
            }

            $meta = '90';

            if ($total_perc < $meta) {
                $puntos = 0;
                $cumplido = 'NO';
            } else {
                $cumplido = 'SI';
                if ($total_perc >= $meta && $total_perc < 100) {
                    if ($tipoefector == 'PSB' || $tipoefector == 'CSA' || $tipoefector == 'ALD') {
                        $puntos = 5;
                    }
                    if ($tipoefector == 'HOS' || $tipoefector == 'HOS3') {
                        $puntos = 2.5;
                    }
                } elseif ($total_perc >= 100) {
                    if ($tipoefector == 'PSB' || $tipoefector == 'CSA' || $tipoefector == 'ALD') {
                        $puntos = 10;
                    }
                    if ($tipoefector == 'HOS' || $tipoefector == 'HOS3') {
                        $puntos = 5;
                    }
                }
            }
        }
    }
    $result['numerador'] = $numerador;
    $result['denominador'] = $denominador;
    $result['meta'] = $meta;
    $result['cumplido'] = $cumplido;
    $result['puntos'] = $puntos;
    $result['total_perc'] = $total_perc;
    $result['encontro'] = $encontro;
    $result['objetivonro'] = 6;
    return $result;
}

function objetivo8($cuie, $ano, $mes, $tipoefector) {
    $puntos = 0;
    $encontro = false;
    if ($tipoefector == 'HOS' || $tipoefector == 'HOS1' || $tipoefector == 'HOS3') {
        $querydenominador = "SELECT numerador,denominador FROM facturacion.objetivos
                            WHERE cuie='$cuie'
                            AND ano='$ano'
                            AND mes='$mes'
                            AND obj='8'";
        $numeradordenominador = sql($querydenominador);
        if ($numeradordenominador->rowcount() > 0) {
            if ($numeradordenominador->fields['numerador'] > $numeradordenominador->fields['denominador']) {
                $numeradordenominador->movenext();
            }
            $encontro = true;
            $numeradordenominador->fields['numerador'] ? $numerador = $numeradordenominador->fields['numerador'] : $numerador = 0;
            $numeradordenominador->fields['denominador'] ? $denominador = $numeradordenominador->fields['denominador'] : $denominador = 0;

            if ($numerador != 0) {
                $total_perc = ($numerador * 100) / $denominador;
            } else {
                $total_perc = 0;
                if ($verifica == 'N05435' || $verifica == 'N20032') {
                    $total_perc = 100;
                }
            }

            $meta = '90';

            if ($total_perc < $meta) {
                $puntos = 0;
                $cumplido = 'NO';
            } else {
                $cumplido = 'SI';
                if ($total_perc >= $meta && $total_perc < 100) {
                    $puntos = 2.5;
                } elseif ($total_perc >= 100) {
                    $puntos = 5;
                }
            }
        }
    }
    $result['numerador'] = $numerador;
    $result['denominador'] = $denominador;
    $result['meta'] = $meta;
    $result['cumplido'] = $cumplido;
    $result['puntos'] = $puntos;
    $result['total_perc'] = $total_perc;
    $result['encontro'] = $encontro;
    $result['objetivonro'] = 7;
    return $result;
}

function objetivo9($cuie, $ano, $mes, $tipoefector) {
    $puntos = 0;
    $encontro = false;
    if ($tipoefector == 'PSB' || $tipoefector == 'CSA' || $tipoefector == 'ALD' || $tipoefector == 'HOS' || $tipoefector == 'HOS2') {
        $querydenominador = "SELECT numerador,denominador FROM facturacion.objetivos
                            WHERE cuie='$cuie'
                            AND ano='$ano'
                            AND mes='$mes'
                            AND obj='9'";
        $numeradordenominador = sql($querydenominador);
        if ($numeradordenominador->rowcount() > 0) {
            if ($numeradordenominador->fields['numerador'] > $numeradordenominador->fields['denominador']) {
                $numeradordenominador->movenext();
            }
            $encontro = true;
            $numeradordenominador->fields['numerador'] ? $numerador = $numeradordenominador->fields['numerador'] : $numerador = 0;
            $numeradordenominador->fields['denominador'] ? $denominador = $numeradordenominador->fields['denominador'] : $denominador = 0;

            if ($numerador != 0) {
                $total_perc = ($numerador * 100) / $denominador;
            } else {
                $total_perc = 0;
                if ($verifica == 'N05435' || $verifica == 'N20032') {
                    $total_perc = 100;
                }
            }

            $meta = '60';

            if ($total_perc < $meta) {
                $puntos = 0;
                $cumplido = 'NO';
            } else {
                $cumplido = 'SI';
                if ($total_perc >= $meta && $total_perc < 80) {
                    if ($tipoefector == 'PSB' || $tipoefector == 'CSA' || $tipoefector == 'ALD' || $tipoefector == 'HOS') {
                        $puntos = 1.5;
                    } elseif ($tipoefector == 'HOS2') {
                        $puntos = 5;
                    }
                } elseif ($total_perc >= 80 && $total_perc < 100) {
                    if ($tipoefector == 'PSB' || $tipoefector == 'CSA' || $tipoefector == 'ALD' || $tipoefector == 'HOS') {
                        $puntos = 3;
                    }
                    if ($tipoefector == 'HOS2') {
                        $puntos = 10;
                    }
                } elseif ($total_perc >= 100) {
                    if ($tipoefector == 'PSB' || $tipoefector == 'CSA' || $tipoefector == 'ALD' || $tipoefector == 'HOS') {
                        $puntos = 5;
                    } elseif ($tipoefector == 'HOS2') {
                        $puntos = 15;
                    }
                }
            }
        }
    }
    $result['numerador'] = $numerador;
    $result['denominador'] = $denominador;
    $result['meta'] = $meta;
    $result['cumplido'] = $cumplido;
    $result['puntos'] = $puntos;
    $result['total_perc'] = $total_perc;
    $result['encontro'] = $encontro;
    $result['objetivonro'] = 8;
    return $result;
}

function objetivo10($cuie, $ano, $mes, $tipoefector) {
    $puntos = 0;
    $encontro = false;
    if ($tipoefector == 'PSB' || $tipoefector == 'CSA' || $tipoefector == 'ALD' || $tipoefector == 'HOS' || $tipoefector == 'HOS2') {
        $querydenominador = "SELECT numerador,denominador FROM facturacion.objetivos
                            WHERE cuie='$cuie'
                            AND ano='$ano'
                            AND mes='$mes'
                            AND obj='10'";
        $numeradordenominador = sql($querydenominador);
        if ($numeradordenominador->rowcount() > 0) {
            if ($numeradordenominador->fields['numerador'] > $numeradordenominador->fields['denominador']) {
                $numeradordenominador->movenext();
            }
            $encontro = true;
            $numeradordenominador->fields['numerador'] ? $numerador = $numeradordenominador->fields['numerador'] : $numerador = 0;
            $numeradordenominador->fields['denominador'] ? $denominador = $numeradordenominador->fields['denominador'] : $denominador = 0;

            if ($numerador != 0) {
                $total_perc = ($numerador * 100) / $denominador;
            } else {
                $total_perc = 0;
                if ($verifica == 'N05435' || $verifica == 'N20032') {
                    $total_perc = 100;
                }
            }

            $meta = '60';

            if ($total_perc < $meta) {
                $puntos = 0;
                $cumplido = 'NO';
            } else {
                $cumplido = 'SI';
                if ($total_perc >= $meta && $total_perc < 80) {
                    if ($tipoefector == 'PSB' || $tipoefector == 'CSA' || $tipoefector == 'ALD' || $tipoefector == 'HOS') {
                        $puntos = 1.5;
                    } elseif ($tipoefector == 'HOS2') {
                        $puntos = 5;
                    }
                } elseif ($total_perc >= 80 && $total_perc < 100) {
                    if ($tipoefector == 'PSB' || $tipoefector == 'CSA' || $tipoefector == 'ALD' || $tipoefector == 'HOS') {
                        $puntos = 3;
                    } elseif ($tipoefector == 'HOS2') {
                        $puntos = 10;
                    }
                } elseif ($total_perc >= 100) {
                    if ($tipoefector == 'PSB' || $tipoefector == 'CSA' || $tipoefector == 'ALD' || $tipoefector == 'HOS') {
                        $puntos = 5;
                    } elseif ($tipoefector == 'HOS2') {
                        $puntos = 15;
                    }
                }
            }
        }
    }
    $result['numerador'] = $numerador;
    $result['denominador'] = $denominador;
    $result['meta'] = $meta;
    $result['cumplido'] = $cumplido;
    $result['puntos'] = $puntos;
    $result['total_perc'] = $total_perc;
    $result['encontro'] = $encontro;
    $result['objetivonro'] = 9;
    return $result;
}

?>