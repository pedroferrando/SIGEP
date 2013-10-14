<?php
ob_start();
require_once ("../../config.php");
require_once ("../../lib/funciones_misiones.php");
require_once ("../../clases/Smiefectores.php");
require_once ("../../clases/Nomenclador.php");
require_once ("../../clases/Factura.php");
require_once ("./recepcion_funciones.php");

ini_set("memory_limit", "500M");
$var = array();
$var['c_i'] = 0; //informados
$var['c_pr'] = 0; //prestaciones
$var['c_e'] = 0; //embarazadas
$var['c_p'] = 0; //partos
$var['c_n'] = 0; //niÃ±os
$var['c_v'] = 0;    //vacunaciones
$var['c_m'] = 0; //muertes
$var['c_d'] = 0; //debito
$var['c_dl'] = 0; //adolecentes
$var['c_a'] = 0; //adultos
$var['c_t'] = 0; //tal

$var['c_dl_tmp'] = 0; //adolecentes tmp
$var['c_a_tmp'] = 0; //adultos tmp
$var['c_e_tmp'] = 0; //embarazadas temporal
$var['c_p_tmp'] = 0; //partos temporal
$var['c_n_tmp'] = 0; //niÃ±os temporal
$var['c_v_tmp'] = 0; //vacunaciones temporal
$var['c_m_tmp'] = 0; //muertes temporal
$var['c_t_tmp'] = 0; // TAL temp


$error_types = array(1 => 'El archivo excede el tamaño máximo permitido.',
    'El archivo excede el tamaño máximo permitido.',
    'El archivo fue subido parcialmente.', 'No se ha subido ningun archivo.', 6 =>
    'No se encuentra la carpeta temporal.', 'Fallo al escribir el archivo a disco.',
    'Una extensión de PHP ha detenido la carga del archivo.');

try {
    sql("BEGIN");

    $cmd = $_POST["Enviar"];
    if ($cmd == "Enviar") {

        if (!$_FILES['archivo'])
            excepcion("Debe seleccionar un archivo.");

        $tamanio = $_FILES["archivo"]["size"];
        if ($tamanio == 0)
            excepcion('El tamaño del archivo es nulo');


        if ($_FILES["archivo"]["error"] != 0)
            echo $error .= $error_types[$_FILES['archivo']['error']];
        if (!$error) {
            $tmp = $_FILES['archivo']['tmp_name'];
            $upload_dir = UPLOADS_DIR . '/archivos/' . $_FILES['archivo']['name'];
            if (!move_uploaded_file($tmp, $upload_dir)) {
                excepcion("¡Posible ataque de carga de archivos!");
            }
        }
    }
    if (!$error) {
        //Controles sobre el formato del archivo
        if (!validarFormularioRecepcion($_POST, $var))
            excepcion($var['error_formulario']);

        $nombre_archivo = explode('.', $_FILES["archivo"]["name"]);
        $nombre_archivo = $nombre_archivo[0];
        //obtener datos desde el nombre de archivo
        if (strlen($nombre_archivo) == 29) {
            $datos_nombre_archivo['cuie'] = substr($nombre_archivo, 0, 6);
            $datos_nombre_archivo['nro_correlativo'] = substr($nombre_archivo, 6, 3);
            $datos_nombre_archivo['periodo_liquidado'] = substr($nombre_archivo, 9, 6);
            $datos_nombre_archivo['periodo_liquidado_mes'] = substr($nombre_archivo, 13, 2);
            $datos_nombre_archivo['periodo_liquidado_anho'] = substr($nombre_archivo, 9, 4);
            $datos_nombre_archivo['tipo_facturacion'] = substr($nombre_archivo, 15, 1);
            $datos_nombre_archivo['vigencia_controlador'] = substr($nombre_archivo, 16, 2);
            $datos_nombre_archivo['nro_factura'] = substr($nombre_archivo, 18, 9);
            $datos_nombre_archivo['tipo_nomenclador'] = substr($nombre_archivo, 27, 2);
        } else {
            throw new Exception("Formato de nombre de archivo incorrecto.");
        }

        $efector = new Smiefectores($datos_nombre_archivo['cuie']);
        if (!$efector->getCuie()) {
            excepcion("CUIE de Efector '" . $datos_nombre_archivo['cuie'] . "' no existe (Ver nombre de archivo)");
        }

        $e_cod_org = $_POST["cod_org"];
        $e_no_correlativo = $_POST["no_correlativo"];
        $e_ano_exp = $_POST["ano_exp"];
        $iniciador = $_POST["cuie"];

        //Control de fecha futura
        $hoy = Fecha_db(date("d/m/Y"));
        $fecha_entrada = Fecha_db(date($_POST['fecha_entrada']));

        if ($fecha_entrada > $hoy) {
            excepcion('La Fecha de entrada es incorrecta');
        }

        $nroexpediente = $e_cod_org . "-" . $e_no_correlativo . "-" . $e_ano_exp;
        if (existeRecepcion($nombre_archivo))
            $var['recepcion_id'] = insertarRecepcion($nombre_archivo, $e_cod_org, $e_no_correlativo, $e_ano_exp);
    }
    if (!$error) {
        $file = fopen(UPLOADS_DIR . "/archivos/" . $_FILES["archivo"]["name"], 'r');

        //Carga en un array todas las tuplas del archivo
        while ($datos = fgets($file)) {
            $lineas[] = explode(';', $datos);
        }

        //obtener primera linea
        $factura = new Factura();

        if ($lineas[0][0] == 'F') {
            $factura->setFechaFactura(trim($lineas[0][1]));
            unset($lineas[0]);
            $lineas = array_values($lineas);
        } else {
            $factura->setFechaFactura("1969-12-31");
        }
        $mes_nombre = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
            "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $factura->setCuie(trim($lineas[0][1]));
        $factura->setPeriodo($lineas[0][2]);
        $factura->setEstado("A");
        $factura->setFechaCarga(date("d/m/Y"));
        $mes = split("/", $lineas[0][2]);
        $mesFactDC = $mes_nombre[intval($mes[1])] . " " . $mes[0];
        $factura->setMesFactDC($mesFactDC);
        $factura->setOnline("NO");
        $factura->setCtrl("N");
        $factura->setNroExp($nroexpediente);

        $factura->setTipoLiquidacion($datos_nombre_archivo['tipo_facturacion']);
        $factura->setTipoNomenclador(obtenerTipoNomencladorTXT($datos_nombre_archivo['tipo_nomenclador']));
        $factura->setNroFactOffline($datos_nombre_archivo['nro_factura']);
        $factura->setRecepcionId($var['recepcion_id']);
        $factura->setFechaEntrada($_POST['fecha_entrada']);
        $existe = FacturaColeccion::existeFactura($factura->getNroFactOffline(), $factura->getCuie());
        if ($existe) {
            excepcion('El nro de factura: ' . $factura->getNroFactOffline() . ' ya existe en el expediente: ' . $existe);
        }
        $factura->setUsuario($_ses_user['id']);
        $var['id_factura'] = $factura->guardarFactura();

        insertarExpediente($nroexpediente, $iniciador, $_POST['fecha_entrada'], $_ses_user['id']);

        $var['fprest_limite'] = calcular_limite_fecha_prestacion($datos_nombre_archivo['periodo_liquidado_mes'], $datos_nombre_archivo['periodo_liquidado_anho']);
        $dato_convenio = fn_dato_convenio($lineas[0][1]);

        $var['q'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        $var['user'] = $_SESSION['user'];
        $var['id_user'] = $_ses_user['id'];

        $var['cuenta_procesado'] = 0;
        $var['cuenta_error'] = 0;
        $var['errort'] = 0;
        $var['errorl'] = 0;
        $var['ni'] = 0;
        $var['em'] = 0;
        $var['pa'] = 0;
        $var['contra'] = 0;
        $var['mu'] = 0;
        $var['datos_nombre_archivo'] = $datos_nombre_archivo;
        $var['SQLerror'] = "ROLLBACK";

        if (count($lineas) > 0) {
            // Aca Inicia el proceso por cada linea del archivo

            foreach ($lineas as $l) {
                $cantcolumnas = count($l);
                if (($cantcolumnas != 75) && ($i != 1) && ($cantcolumnas != 84)) {
                    excepcion('Formato de linea incorrecto en el archivo'); //error
                }

                $idperiodo = 0;
                $var['row'] = $i;
                $var['descripcion_error'] = '';
                $var['eliminado'] = 'N';
                $var['ya_esta'] = 'no';
                $var['ya_estaTMP'] = 'no';
                $var['error'] = 'no';
                $var['idvacuna'] = 0;
                $var['error_datos'] = 'no';
                $var['mjs_error_datos'] = '';
                $var['idtaller'] = '';
                $var['km'] = '';
                $var['e_defuncion'] = 'no';
                $var['e_caso'] = 'no';
                $var['ojo'] = 'no';
                $var['existe_id'] = 'no';
                $var['idbenefrecepcion'] = 0;
                $var['beneficiario'] = 0;
                $var['grupo_etario'] = 0;
                $var['id_nomenclador'] = 0;
                $var['id_comprobante'] = 0;
                $var['id_prestacion'] = 0;
                $debito = 0;
                $var['cantidad'] = 1; // multipo por practicas realizadas en cada linea
                $l = limpiar($l);

                $limite_trz = limite_trazadora($l[13]);
                $var['limite_trz'] = $limite_trz;

                prepararDatosComprobante($l, $var);

                if ($l[0] == 'L') {
                    $var['beneficiario'] = obtenerIdSmiafiliado($l[5], $var['id_factura'], $l[13], $datos_nombre_archivo['tipo_facturacion'], $l[2]);
                }

                if ($var['beneficiario']['id']) {
                    $fecha_nac_para_grupo_etario = $var['beneficiario']['afifechanac'];
                } else {
                    $fecha_nac_para_grupo_etario = Fecha_db($l[11]);
                }

                $var['grupo_etario'] = calcularGrupoEtareo($fecha_nac_para_grupo_etario, Fecha_db($l[13]));
                $var['grupo_etario']['estaembarazada'] = beneficiarioEmbarazadoUAD($l[5], Fecha_db($l[13]));

                //obtiene la fecha de periodo segun fecha_prestacion
                if ($var['beneficiario']['padron_periodo']) {
                    $idperiodo = $var['beneficiario']['padron_periodo'];
                } else {
                    $idperiodo = buscarPeriodo(str_replace("/", "-", $l[13]));
                    $idperiodo = $idperiodo['id'];
                }

                $vigencia = obtenerModoVigencia($l[1], $l[13]);
                if ($vigencia['modo'] == '1') {
                    //busca practicas nomenclador viejo
                    $var['id_nomenclador'] = traemeNomenclador($vigencia, $l[12]);
                } elseif ($vigencia['modo'] == '2') {
                    //codigo como viene en el txt
                    $var['nomenclador_original'] = $l[12];
                    $codigo_desglozado = split(" ", $var['nomenclador_original']);
                    $nomenclador = Nomenclador::buscaPractica($codigo_desglozado[0], $codigo_desglozado[1], $codigo_desglozado[2], $vigencia['id']);
                    $var['id_nomenclador'] = $nomenclador;
                }

                //Controles en la importacion, Solo Liquidaciones
                if ($l[0] == 'L') {
                    //controles que determinan el debito
                    $debito = controlDebito($l, $var, $nomenclador);
                    $precio_prestacion = $debito['monto_deb'];

                    //guarda el comprobante en BD
                    $comprobante = obtenerComprobante($l, $var, $idperiodo, $datos_nombre_archivo['tipo_nomenclador']);

                    $var['id_comprobante'] = insertarComprobante($l, $var, $comprobante, $vigencia);

                    //guarda la practica en BD
                    $prestacion = obtenerPrestacion($l, $var, $precio_prestacion, $nomenclador);
                    $var['id_prestacion'] = insertarPrestacion($prestacion);
                    //cuenta la cantidad de prestaciones
                    $var['c_pr']++;

                    //guarda el debito en BD
                    if ($debito['resultado_ctrl']['debito'] == TRUE) {
                        $debito['id_comprobante'] = $var['id_comprobante'];
                        $debito['idprestacion'] = $var['id_prestacion'];
                        $debito['cantidad'] = $var['cantidad'];
                        insertarDebito($debito);
                        //cuenta las cantidades de rechazos
                        $var['c_d']++;
                    }
                }

                /////esto es para trazadora.///////////////
                if (($l[0] == "T" || $l[0] == "L") && ($l[3] == 1 || $l[3] == 2 || $l[3] == 3 || $l[3] == 14 || $l[3] == 36 || $l[3] == 37 || $l[3] == 38)) {

                    $var['menos']++;
                    $var['contra']++;

                    rechazoTrazadora($l, $var);

                    verificarTrazadora($l, $var, $nomenclador);
                }

                ///////////////////////////SI ES INFORMADO//////////////////////////////////
                if (($l[0] == "I" || $l[0] == "T") && $var['metez'] == 's') {
                    insertarInformado($l, $var);
                    $var['c_i']++;
                }

                if ($error == 'si')
                    echo $var['descripcion_error'] . '<br />';
                $i++;
            }//***** Fin foreach
        }
        /* calculo de monto */
        if (isset($var['id_factura'])) {
            $monto_prefactura = montoFactura($var['id_factura']);
            actualizarMontoFactura($var['id_factura'], $monto_prefactura['total']);
            finProcesoFactura($var['recepcion_id'], $i);
        }
    }

    sql("COMMIT");
    //sql("ROLLBACK", "Error en rollback", 0);
    include ('recepcion_nuevo_txt_vista.php');
} catch (exception $e) {
    sql("ROLLBACK", "Error en rollback", 0);
    ?>
    <html>
        <head>
            <link rel='icon' href='/../../favicon.ico'>
            <link REL='SHORTCUT ICON' HREF='../../favicon.ico'>
            <link rel=stylesheet type='text/css' href='../../lib/estilos.css'>
        </head>
        <body background="../../imagenes/fondo.gif" bgcolor="#B7CEC4" >
            <br /><br /><br />
            <table id='contenedorform' width="469" border="0" align="center" cellpadding="0" cellspacing="0">

                <tr>    
                    <td align="center" style="padding: 20px;font-size: 14px;background-color: white;color: red;font-weight: bold;">
                        <?php
                        if (isset($i))
                            echo "Error en la l&iacute;nea $i<br />";
                        echo "Error: " . $e->getMessage() . "<br /><br /><br />";
                        ?>
                        <a href="recepcion_txt.php">&laquo; Volver atr&aacute;s</a>
                    </td>
                </tr>
            </table>
        </body>
    </html>
    <?php
};
ob_end_flush();
?>