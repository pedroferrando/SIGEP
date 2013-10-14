<?php
require_once ("../../config.php");
require_once ("../../clases/BeneficiariosUad.php");
require_once ("../../clases/Prestacion.php");
require_once ("../../clases/Comprobante.php");
require_once ("../../clases/Nomenclador.php");

extract($_POST, EXTR_SKIP);
if ($parametros)
    extract($parametros, EXTR_OVERWRITE);


if ($_POST['filtro']) {
//Busqueda de Beneficiarios segun filtro
    $keyword = $_POST['clave'];
    $fecha_comprobante = $_POST['fecha_comprobante'];
    switch ($_POST['filtro']) {
        case 'dni':
            $where = "numero_doc like '%$keyword%' limit 20";
            $lista_beneficiarios = BeneficiariosUadColeccion::FiltrarToArray($where, $fecha_comprobante);
            break;
        case 'apellido':
            $keyword = strtoupper($keyword);
            $where = "apellido_benef like '%$keyword%' limit 20";
            $lista_beneficiarios = BeneficiariosUadColeccion::FiltrarToArray($where, $fecha_comprobante);
            break;
        case 'clavebeneficiario':
            $where = "clave_beneficiario like '%$keyword%' limit 20";
            $lista_beneficiarios = BeneficiariosUadColeccion::FiltrarToArray($where, $fecha_comprobante);
            break;
        default:
            break;
    }

    die(json_encode($lista_beneficiarios));
}

//busca la prestacion en caso de ser una nomina ya existente.
$yaexisteprestacion = PrestacionColeccion::buscarPrestacionPorComprobante($id_comprobante);

if ($_POST['guardar']) {
    $listadebeneficiarios = json_decode(stripslashes($_POST['listadebeneficiarios']));
    $usuario = $_ses_user['id'];
    $fecha_carga = date("Y-m-d H:i:s");
    $nomenclador = Nomenclador::buscarNomencladorPorId($idnomenclador);

    //array con codigos hardcoding para talleres con cantidad manual
    $codigos_sin_cantidad_de_asistentes = array('T001', 'T002', 'T003');

    sql('BEGIN');
    if ($yaexisteprestacion) {
        //elimina todo los benef existentes de antemano en la nomina
        $sql = "DELETE FROM facturacion.nomina_talleres
            WHERE id_comprobante='$id_comprobante'";
        sql($sql);
        if (!in_array($nomenclador->getCodigoTema(), $codigos_sin_cantidad_de_asistentes)) {
            //recorre el listado de asistentes para determinar cuantos estan activos
            //y este sera el monto por el cual se multiplique el precio del nomenclador dando el monto total del taller
            $cont_activos = 0;
            foreach ($listadebeneficiarios as $unaclavebeneficiario) {
                $beneficiario = BeneficiariosUadColeccion::buscarPorClaveBeneficiario($unaclavebeneficiario);
                if ($beneficiario->getEstadoEnPadron() == 'Activo') {
                    $cont_activos++;
                }
            }
            $yaexisteprestacion->setCantidad($cont_activos);
            $yaexisteprestacion->guardarPrestacion();
        }
        $id_prestacion = $yaexisteprestacion->getIdPrestacion();
    } else {
        //si no existe la nomina crea una nueva prestacion
        $prestacion = new Prestacion();
        if (!in_array($nomenclador->getCodigoTema(), $codigos_sin_cantidad_de_asistentes)) {
            $cantidad = count($listadebeneficiarios);
            $prestacion->setCantidad($cantidad);
        } else {
            $prestacion->setCantidad($cantidad);
        }
        $prestacion->setIdComprobante($id_comprobante);
        $prestacion->setIdNomenclador($idnomenclador);
        $prestacion->setPrecioPrestacion($precioprestacion);
        $id_prestacion = $prestacion->guardarPrestacion();

        $comprobante = Comprobante::getComprobantePorId($id_comprobante);
        $comprobante->setMarca("1");
        $comprobante->setUsuario($_ses_user['id']);
        $comprobante->guardarComprobante();
    }
    //inserta cada uno de los beneficiarios de la tabla_si a la nomina del taller.
    foreach ($listadebeneficiarios as $clavebeneficiario) {
        if (BeneficiariosUadColeccion::existeClaveBeneficiario($clavebeneficiario)) {
            $sql = "INSERT INTO facturacion.nomina_talleres(id_comprobante,id_prestacion,clavebeneficiario,usuario,fecha_modificacion)
              VALUES($id_comprobante,$id_prestacion,'$clavebeneficiario',$usuario,'$fecha_carga')";
            sql($sql) or excepcion('Error al insertar el beneficiario en la nomina');
        }
    }
    sql('COMMIT');
    $print = $_POST['listadebeneficiarios'];
    die('La operacion se realizo con exito.');
}
//consulta los beneficiarios que ya estan agregados al taller
$beneficiario_del_comprobante = BeneficiariosUadColeccion::buscarPorClaveBeneficiario($clave_beneficiario);
if ($yaexisteprestacion) {
    $sql = "SELECT clavebeneficiario FROM facturacion.nomina_talleres WHERE id_comprobante='$id_comprobante' AND clavebeneficiario<>'$clave_beneficiario'";
    $nomina_existente = sql($sql);
}
?>
<link rel=stylesheet type='text/css' href='../../lib/css/general.css'>
<link rel=stylesheet type='text/css' href='../../lib/css/sprites.css'>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>

<script>
    $(window).load(function() {
        $('#loading').css('display', 'none');
        $('#keyword').focus();
        $('#btn_buscar').on('click', function() {
            if ($('#keyword').val() !== '') {
                buscar();
            }
        });
        $('#keyword').keypress(function(event) {
            if (event.which === 13) {
                if ($('#keyword').val() !== '') {
                    buscar();
                }
            }
        });
        $('#btn_guardar').on('click', function() {
            var cantidadenlistados = $('#listado_si').prop('rows').length - 1;
            var r = confirm("Esta por inscribir a " + cantidadenlistados + " personas al Taller. Desea Continuar?");
            if (r == true)
            {
                guardar();
            }
        });
        $('#listado_no').on('click', '.fila', function() {
            if (validarSeleccion(this)) {
                if (!estaAgregado(this)) {
                    var table_si = document.getElementById("listado_si");
                    var row = table_si.insertRow(-1);
                    row.className = "fila";
                    row.setAttribute('grupo', this.getAttribute('grupo'));
                    row.setAttribute('embarazo', this.getAttribute('embarazo'));

                    $(this).find("td").each(function() {
                        cell = row.insertCell(-1);
                        cell.innerHTML = this.innerHTML;
                    });
                    row.cells[5].setAttribute("align", "center");
                    $(this).remove();
                } else {
                    alert("El beneficiario ya esta agregado al Taller");
                }
            } else {
                alert("El beneficiario seleccionado no corresponde al Grupo Etario del Taller");
            }
        });
        $('#listado_si').on('click', '.fila', function() {
            if ($(this).index() !== 1) {

                //$('#listado_no').append($(this).remove());
                var table_no = document.getElementById("listado_no");
                var row = table_no.insertRow(-1);
                row.className = "fila";
                row.setAttribute('grupo', this.getAttribute('grupo'));
                row.setAttribute('embarazo', this.getAttribute('embarazo'));

                $(this).find("td").each(function() {
                    // if ((this.cellIndex > 0) && (this.cellIndex < 6)) {
                    var cell = row.insertCell(-1);
                    cell.innerHTML = this.innerHTML;
                    //  }
                });
                row.cells[5].setAttribute("align", "center");
                $(this).remove();
            }
        });

    });

    function estaAgregado(fila) {
        var yaesta = false;
        var td = $(fila).children('td').slice(0, 1);
        var clave = td.html();

        $("#listado_si tr.fila").each(function() {
            $(this).find("td").each(function() {
                if (this.cellIndex == 0) {
                    var aux = this.innerHTML;
                    if (clave == aux) {
                        yaesta = true;
                    }
                    return false;
                }
            });
        });
        return yaesta;
    }

    function validarSeleccion(fila) {
        var grupo_etario_original = '<?php echo $datos_practica['grupo_precio'] ?>';
        var grupo = $(fila).attr('grupo');
        var embarazo = $(fila).attr('embarazo');
        var valido = false;
        if (grupo_etario_original == 'embarazada') {
            if (embarazo == 'true') {
                valido = true;
            }
        } else {
            if (grupo_etario_original == grupo) {
                valido = true;
            }
        }
        return valido;
    }

    function buscar() {
        var table_no = document.getElementById("listado_no");
        $('#loading').css('display', 'inline-block');

        var filtro = $('#filtro').val();
        var clave = $('#keyword').val();
        var fecha_comprobante = '<?php echo $fecha_comprobante ?>';
        $.post("seleccionBeneficiariosTaller.php", {filtro: filtro, clave: clave, fecha_comprobante: fecha_comprobante}, function(data) {
            $('#loading').css('display', 'none');

            $("#listado_no tr:gt(0)").remove();
            var con = false;
            $.each(data, function(index, val) {

                var row = table_no.insertRow(1);
                if (con) {
                    row.className = "fila con";
                } else {
                    row.className = "fila sin";
                }

                row.setAttribute('grupo', val['grupoEtario']);
                row.setAttribute('embarazo', val['embarazado']);

                $('#listado_no').eq(index + 1).attr('grupo', val['grupoEtario']);
                con = !con;

                var cell1 = row.insertCell(-1);
                var cell2 = row.insertCell(-1);
                var cell3 = row.insertCell(-1);
                var cell4 = row.insertCell(-1);
                var cell5 = row.insertCell(-1);
                var cell6 = row.insertCell(-1);

                cell1.innerHTML = val['claveBeneficiario'];
                cell2.innerHTML = val['apellidoBenef'];
                cell3.innerHTML = val['nombreBenef'];
                cell4.innerHTML = val['numeroDoc'];
                cell5.innerHTML = val['grupoEtario'];

                var imagen_estado;
                if (val['estadoEnPadron'] == 'No') {
                    imagen_estado = "<div align='center' class='sprite-gral icon-minus-alt'/>";
                } else {
                    if (val['estadoEnPadron'] == 'Activo') {
                        imagen_estado = "<div align='center' class='sprite-gral icon-check-alt'/>";
                    } else {
                        imagen_estado = "<div align='center' class='sprite-gral icon-x-altx-alt'/>";
                    }
                }
                cell6.setAttribute("align", "center");
                cell6.innerHTML = imagen_estado;
            });
        }, 'JSON');
    }

    function guardar() {
        var id_comprobante =<?php echo $id_comprobante ?>;
        var idnomenclador =<?php echo $datos_practica['id_nomenclador'] ?>;
        var precioprestacion =<?php echo $datos_practica['precio'] ?>;
        var cantidad = $("#cantidad").val();
        var datosaenviar = [];
        $("#listado_si tr.fila").each(function() {
            datosaenviar.push(this.cells[0].innerHTML);
        });

        $('#btn_guardar').attr("disable", true);
        var listadebeneficiarios = JSON.stringify(datosaenviar);
        $.post('seleccionBeneficiariosTaller.php', {guardar: 'guardar', id_comprobante: id_comprobante, idnomenclador: idnomenclador, listadebeneficiarios: listadebeneficiarios, cantidad: cantidad, precioprestacion: precioprestacion}, function(data) {
            $("#monitor").append(data);
            $("#monitor").show();
            setTimeout(function() {
                $('#monitor').fadeOut();
                $("#monitor").empty();
            }, 3000);
<?php if ($yaexisteprestacion) { ?>
                $('#btn_guardar').removeAttr("disable");
<?php } else { ?>
                $('#titulo', window.opener.document).text('La operacion se realizo con exito.');
                $("#categoria", window.opener.document).val('-1');
                if (window.opener && !window.opener.closed) {
                    window.opener.combocambiado();
                }
                self.close();
<?php } ?>
        });
    }
</script>

<?php
include './seleccionBeneficiariosTaller.tpl.php';
?>
