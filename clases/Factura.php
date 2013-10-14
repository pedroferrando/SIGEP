<?php

/**
 * 
 *
 * @version 1.107
 * @package entity
 */
class Factura {

    private $idFactura;
    private $cuie;
    private $periodo;
    private $estado;
    private $observaciones;
    private $fechaCarga;
    private $fechaFactura;
    private $mesFactDC;
    private $montoPrefactura;
    private $fechaControl;
    private $nroExp;
    private $traba;
    private $online;
    private $nroExpExt;
    private $fechaExpExt;
    private $periodoContable;
    private $periodoActual;
    private $nroFactOffline;
    private $recepcionId;
    private $fechaEntrada;
    private $ctrl;
    private $tipoNomenclador;
    private $tipoLiquidacion;
    private $comprobante;
    private $debitos;
    private $prestaciones;
    private $usuario;
    private $fechaModificacion;

    public function __construct($idfactura = '') {
        if ($idfactura != '') {
            $where = "id_factura='$idfactura'";
            $sql = Factura::getSQlSelectWhere($where);
            $result = sql($sql);
            if (!$result->EOF) {
                $this->construirResult($result);
            }
        }
    }

    public function construirResult($result) {
        $this->idFactura = $result->fields['id_factura'];
        $this->cuie = $result->fields['cuie'];
        $this->periodo = $result->fields['periodo'];
        $this->estado = $result->fields['estado'];
        $this->observaciones = $result->fields['observaciones'];
        $this->fechaCarga = $result->fields['fecha_carga'];
        $this->fechaFactura = $result->fields['fecha_factura'];
        $this->mesFactDC = $result->fields['mes_fact_d_c'];
        $this->montoPrefactura = $result->fields['monto_prefactura'];
        $this->fechaControl = $result->fields['fecha_control'];
        $this->nroExp = $result->fields['nro_exp'];
        $this->traba = $result->fields['traba'];
        $this->online = $result->fields['online'];
        $this->nroExpExt = $result->fields['nro_exp_ext'];
        $this->fechaExpExt = $result->fields['fecha_exp_ext'];
        $this->periodoContable = $result->fields['periodo_contable'];
        $this->periodoActual = $result->fields['periodo_actual'];
        $this->nroFactOffline = $result->fields['nro_fact_offline'];
        $this->recepcionId = $result->fields['recepcion_id'];
        $this->fechaEntrada = $result->fields['fecha_entrada'];
        $this->ctrl = $result->fields['ctrl'];
        $this->tipoNomenclador = $result->fields['tipo_nomenclador'];
        $this->tipoLiquidacion = $result->fields['tipo_liquidacion'];
        $this->fechaModificacion = $result->fields['fecha_modificacion'];
        $this->usuario = $result->fields['usuario'];
    }

    /**
     * set value for id_factura 
     *
     * type:serial,size:10,default:nextval('facturacion.factura_id_factura_seq'::regclass),primary,unique,autoincrement
     *
     * @param mixed $idFactura
     */
    public function setIdFactura($idFactura) {
        $this->idFactura = $idFactura;
    }

    /**
     * get value for id_factura 
     *
     * type:serial,size:10,default:nextval('facturacion.factura_id_factura_seq'::regclass),primary,unique,autoincrement
     *
     * @return mixed
     */
    public function getIdFactura() {
        return $this->idFactura;
    }

    /**
     * set value for cuie 
     *
     * type:text,size:2147483647,default:null,index,nullable
     *
     * @param mixed $cuie
     */
    public function setCuie($cuie) {
        $this->cuie = $cuie;
    }

    /**
     * get value for cuie 
     *
     * type:text,size:2147483647,default:null,index,nullable
     *
     * @return mixed
     */
    public function getCuie() {
        return $this->cuie;
    }

    /**
     * set value for periodo Es el Periodo Actual
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $periodo
     */
    public function setPeriodo($periodo) {
        $this->periodo = $periodo;
    }

    /**
     * get value for periodo Es el Periodo Actual
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getPeriodo() {
        return $this->periodo;
    }

    /**
     * set value for estado 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $estado
     */
    public function setEstado($estado) {
        $this->estado = $estado;
    }

    /**
     * get value for estado 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getEstado() {
        return $this->estado;
    }

    /**
     * set value for observaciones 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $observaciones
     */
    public function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
    }

    /**
     * get value for observaciones 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getObservaciones() {
        return $this->observaciones;
    }

    /**
     * set value for fecha_carga 
     *
     * type:timestamp,size:22,default:null,nullable
     *
     * @param mixed $fechaCarga
     */
    public function setFechaCarga($fechaCarga) {
        $this->fechaCarga = $fechaCarga;
    }

    /**
     * get value for fecha_carga 
     *
     * type:timestamp,size:22,default:null,nullable
     *
     * @return mixed
     */
    public function getFechaCarga() {
        return $this->fechaCarga;
    }

    /**
     * set value for fecha_factura 
     *
     * type:timestamp,size:22,default:null,nullable
     *
     * @param mixed $fechaFactura
     */
    public function setFechaFactura($fechaFactura) {
        $this->fechaFactura = $fechaFactura;
    }

    /**
     * get value for fecha_factura 
     *
     * type:timestamp,size:22,default:null,nullable
     *
     * @return mixed
     */
    public function getFechaFactura() {
        return $this->fechaFactura;
    }

    /**
     * set value for mes_fact_d_c 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $mesFactDC
     */
    public function setMesFactDC($mesFactDC) {
        $this->mesFactDC = $mesFactDC;
    }

    /**
     * get value for mes_fact_d_c 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getMesFactDC() {
        return $this->mesFactDC;
    }

    /**
     * set value for monto_prefactura 
     *
     * type:numeric,size:30,default:null,nullable
     *
     * @param mixed $montoPrefactura
     */
    public function setMontoPrefactura($montoPrefactura) {
        $this->montoPrefactura = $montoPrefactura;
    }

    /**
     * get value for monto_prefactura 
     *
     * type:numeric,size:30,default:null,nullable
     *
     * @return mixed
     */
    public function getMontoPrefactura() {
        return $this->montoPrefactura;
    }

    /**
     * set value for fecha_control 
     *
     * type:date,size:13,default:null,nullable
     *
     * @param mixed $fechaControl
     */
    public function setFechaControl($fechaControl) {
        $this->fechaControl = $fechaControl;
    }

    /**
     * get value for fecha_control 
     *
     * type:date,size:13,default:null,nullable
     *
     * @return mixed
     */
    public function getFechaControl() {
        return $this->fechaControl;
    }

    /**
     * set value for nro_exp 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $nroExp
     */
    public function setNroExp($nroExp) {
        $this->nroExp = $nroExp;
    }

    /**
     * get value for nro_exp 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getNroExp() {
        return $this->nroExp;
    }

    /**
     * set value for traba 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $traba
     */
    public function setTraba($traba) {
        $this->traba = $traba;
    }

    /**
     * get value for traba 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getTraba() {
        return $this->traba;
    }

    /**
     * set value for online 
     *
     * type:bpchar,size:2,default:null,nullable
     *
     * @param mixed $online
     */
    public function setOnline($online) {
        $this->online = $online;
    }

    /**
     * get value for online 
     *
     * type:bpchar,size:2,default:null,nullable
     *
     * @return mixed
     */
    public function getOnline() {
        return $this->online;
    }

    /**
     * set value for nro_exp_ext 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $nroExpExt
     */
    public function setNroExpExt($nroExpExt) {
        $this->nroExpExt = $nroExpExt;
    }

    /**
     * get value for nro_exp_ext 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getNroExpExt() {
        return $this->nroExpExt;
    }

    /**
     * set value for fecha_exp_ext 
     *
     * type:date,size:13,default:null,nullable
     *
     * @param mixed $fechaExpExt
     */
    public function setFechaExpExt($fechaExpExt) {
        $this->fechaExpExt = $fechaExpExt;
    }

    /**
     * get value for fecha_exp_ext 
     *
     * type:date,size:13,default:null,nullable
     *
     * @return mixed
     */
    public function getFechaExpExt() {
        return $this->fechaExpExt;
    }

    /**
     * set value for periodo_contable 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $periodoContable
     */
    public function setPeriodoContable($periodoContable) {
        $this->periodoContable = $periodoContable;
    }

    /**
     * get value for periodo_contable 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getPeriodoContable() {
        return $this->periodoContable;
    }

    /**
     * set value for periodo_actual Es el Periodo de la Prestacion
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $periodoActual
     */
    public function setPeriodoActual($periodoActual) {
        $this->periodoActual = $periodoActual;
    }

    /**
     * get value for periodo_actual Es el Periodo de la Prestacion
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getPeriodoActual() {
        return $this->periodoActual;
    }

    /**
     * set value for nro_fact_offline 
     *
     * type:bpchar,size:14,default:null,nullable
     *
     * @param mixed $nroFactOffline
     */
    public function setNroFactOffline($nroFactOffline) {
        $this->nroFactOffline = $nroFactOffline;
    }

    /**
     * get value for nro_fact_offline 
     *
     * type:bpchar,size:14,default:null,nullable
     *
     * @return mixed
     */
    public function getNroFactOffline() {
        return $this->nroFactOffline;
    }

    /**
     * set value for recepcion_id 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @param mixed $recepcionId
     */
    public function setRecepcionId($recepcionId) {
        $this->recepcionId = $recepcionId;
    }

    /**
     * get value for recepcion_id 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @return mixed
     */
    public function getRecepcionId() {
        return $this->recepcionId;
    }

    /**
     * set value for fecha_entrada 
     *
     * type:date,size:13,default:null,nullable
     *
     * @param mixed $fechaEntrada
     */
    public function setFechaEntrada($fechaEntrada) {
        $this->fechaEntrada = $fechaEntrada;
    }

    /**
     * get value for fecha_entrada 
     *
     * type:date,size:13,default:null,nullable
     *
     * @return mixed
     */
    public function getFechaEntrada() {
        return $this->fechaEntrada;
    }

    /**
     * set value for ctrl 
     *
     * type:bpchar,size:1,default:null,nullable
     *
     * @param mixed $ctrl
     */
    public function setCtrl($ctrl) {
        $this->ctrl = $ctrl;
    }

    /**
     * get value for ctrl 
     *
     * type:bpchar,size:1,default:null,nullable
     *
     * @return mixed
     */
    public function getCtrl() {
        return $this->ctrl;
    }

    /**
     * set value for tipo_nomenclador 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $tipoNomenclador
     */
    public function setTipoNomenclador($tipoNomenclador) {
        $this->tipoNomenclador = $tipoNomenclador;
    }

    /**
     * get value for tipo_nomenclador 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getTipoNomenclador() {
        return $this->tipoNomenclador;
    }

    /**
     * set value for tipo_liquidacion 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $tipoLiquidacion
     */
    public function setTipoLiquidacion($tipoLiquidacion) {
        $this->tipoLiquidacion = $tipoLiquidacion;
    }

    /**
     * get value for tipo_liquidacion 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getTipoLiquidacion() {
        return $this->tipoLiquidacion;
    }

    public function getTipoDeFactura() {
        if ($this->tipoLiquidacion == 'R') {
            $tipodefactura = "Refacturada";
        } else {
            $tipodefactura = "Vigente";
        }
        return $tipodefactura;
    }

    public function getUsuario() {
        return $this->usuario;
    }

    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    public static function getSQlSelectWhere($where) {

        $sql = "
			SELECT *
			  FROM facturacion.factura
			  WHERE " . $where . "";

        return($sql);
    }

    public static function getSQlSelect() {

        $sql = "
			SELECT *
			  FROM facturacion.factura";

        return($sql);
    }

    public function getDebitos() {
        if (count($this->debitos) == 0) {
            $debitos = DebitoColeccion::Filtrar("id_factura='$this->idFactura'");
            $this->debitos = $debitos;
        }

        return ($this->debitos);
    }

    public function getMontoDebitos() {
        $monto = 0;
        $debitos = $this->getDebitos();
        if (is_array($this->debitos)) {
            foreach ($debitos as $debito) {
                $monto+=$debito->getMontoTotal();
            }
        }

        return $monto;
    }

    public function getCantidadDebitos() {
        $cantidad = 0;
        $debitos = $this->getDebitos();
        if (is_array($debitos)) {
            if ($this->getNomencladorDetalle() < 14) {
                foreach ($debitos as $debito) {
                    $prestacion = $debito->getPrestacionViejo();
                    $cantidad+=$prestacion->getCantidad();
                }
            } else {
                foreach ($debitos as $debito) {
                    $prestacion = $debito->getPrestacion();
                    $cantidad+=$prestacion->getCantidad();
                }
            }
        }
        return $cantidad;
    }

    public function getPracticasConCodigoDiferente() {
        $prestaciones = $this->getPrestaciones();
        $resultado = array();
        $nomencladores = array();

        //Arma un array con todos los nomencladores que tienen id y precio diferentes
        foreach ($prestaciones as $prestacion) {
            $identificador = $prestacion->getIdNomenclador() . "_" . $prestacion->getPrecioPrestacion();
            if (!in_array($identificador, $nomencladores)) {
                $nomencladores[] = $identificador;
                $resultado [$identificador]['codigo'] = $prestacion->getNomenclador()->getCodigoCompleto();
                $resultado [$identificador]['nomenclador'] = $prestacion->getIdNomenclador();
                $resultado [$identificador]['precio_unitario'] = $prestacion->getPrecioPrestacion();
                $resultado [$identificador]['cantidad'] = 0;
                $resultado [$identificador]['monto'] = 0;
                $resultado [$identificador]['cantidad_debitados'] = 0;
                $resultado [$identificador]['monto_debito'] = 0;
                //auxiliar para poder ordenar luego segun este campo.
                $arraytmp[$identificador] = $prestacion->getIdNomenclador();
            }

            $resultado [$identificador]['cantidad'] += $prestacion->getCantidad();
            $resultado [$identificador]['monto'] += $prestacion->getPrecioPrestacion();
            if ($this->getNomencladorDetalle() < 14) {
                $estadebitada = $prestacion->estaDebitadaVieja();
            } else {
                $estadebitada = $prestacion->estaDebitada();
            }
            if ($estadebitada) {
                $resultado [$identificador]['monto_debito'] +=$prestacion->getPrecioPrestacion() * $prestacion->getCantidad();
                $resultado [$identificador]['cantidad_debitados'] +=$prestacion->getCantidad();
            }
        }
        //ordena segun id_nomenclador
        array_multisort($arraytmp, $resultado);

        return $resultado;
    }

    //evalua el campo online, y devuelve un valor boolean.
    public function esOnline() {
        if ($this->getOnline() == 'SI') {
            return true;
        } else {
            return false;
        }
    }

    //calcula el monto total de lo facturado menos lo debitado
    public function getLiquidacion() {
        $liquidacion = $this->getMontoPrefactura() - $this->getMontoDebitos();
        return $liquidacion;
    }

    public function getCantidadPracticas() {
        $cantidad = 0;
        $comprobantes = $this->getComprobantes();
        foreach ($comprobantes as $comprobante) {
            $prestaciones = $comprobante->getPrestaciones();
            foreach ($prestaciones as $prestacion) {
                $cantidad+=$prestacion->getCantidad();
            }
        }
        return $cantidad;
    }

    public function getComprobantes() {
        if (count($this->comprobante) == 0) {
            $comprobantes = ComprobanteColeccion::Filtrar("id_factura='$this->idFactura'");
            $this->comprobante = $comprobantes;
        }

        return ($this->comprobante);
    }

    public function getPrestaciones() {
        if (count($this->prestaciones) == 0) {
            $prestaciones = array();
            $comprobantes = $this->getComprobantes();
            foreach ($comprobantes as $comprobante) {
                $prestaciones = array_merge($prestaciones, $comprobante->getPrestaciones());
            }
            $this->prestaciones = $prestaciones;
        }
        return ($this->prestaciones);
    }

    //devuelve los meses (periodos) de las prestaciones en esta factura.
    public function getFechasFacturadas() {
        $q = "SELECT EXTRACT(YEAR from fecha_comprobante) ano,EXTRACT(MONTH from fecha_comprobante) mes 
            FROM facturacion.comprobante 
            WHERE id_factura='$this->idFactura' 
            GROUP BY EXTRACT(MONTH from fecha_comprobante),EXTRACT(YEAR from fecha_comprobante)
            ORDER BY EXTRACT(MONTH from fecha_comprobante),EXTRACT(YEAR from fecha_comprobante) ASC";
        $periodos = sql($q) or die;
        return $periodos;
    }

    public function getNomencladorDetalle() {
        $sql = "SELECT id_nomenclador_detalle  
                FROM facturacion.comprobante 
                WHERE  id_factura='$this->idFactura' limit 1";
        $result = sql($sql);
        return $result->fields['id_nomenclador_detalle'];
    }

    public function getSQLInsert() {
        $sql = "INSERT INTO facturacion.factura (cuie, periodo, estado, fecha_carga, fecha_factura,
                mes_fact_d_c, nro_exp, online, nro_exp_ext, nro_fact_offline, recepcion_id, fecha_entrada, periodo_actual, ctrl,
                tipo_liquidacion,tipo_nomenclador,fecha_modificacion,usuario) 
                VALUES ('$this->cuie',
                        '$this->periodo',
                        '$this->estado',
                        '" . Fecha_db($this->fechaCarga) . "',
                        '" . Fecha_db($this->fechaFactura) . "',
                        '$this->mesFactDC','$this->nroExp',
                        '$this->online',
                        '$this->nroExp',
                        '$this->nroFactOffline',
                        '$this->recepcionId',
                        '" . Fecha_db($this->fechaEntrada) . "',
                        '$this->periodo',
                        '$this->ctrl',
                        '$this->tipoLiquidacion',
                        '$this->tipoNomenclador',
                        '$this->fechaModificacion',
                        '$this->usuario')
                RETURNING id_factura";
        return($sql);
    }

    # Documentacion para metodo getSQlUpdate

    public function getSQlUpdate() {
        $sql = "UPDATE facturacion.factura set ";

        !is_null($this->cuie) ? $sql.= "cuie='" . $this->cuie . "', " : false;
        !is_null($this->periodo) ? $sql.= "periodo='" . $this->periodo . "', " : false;
        !is_null($this->estado) ? $sql.= "estado='" . $this->estado . "', " : false;
        !is_null($this->observaciones) ? $sql.= "observaciones='" . $this->observaciones . "', " : false;
        !is_null($this->fechaCarga) ? $sql.= "fecha_carga='" . $this->fechaCarga . "', " : false;
        !is_null($this->fechaFactura) ? $sql.= "fecha_factura='" . $this->fechaFactura . "', " : false;
        !is_null($this->mesFactDC) ? $sql.= "mes_fact_d_c='" . $this->mesFactDC . "', " : false;
        !is_null($this->montoPrefactura) ? $sql.= "monto_prefactura='" . $this->montoPrefactura . "', " : false;
        !is_null($this->fechaControl) ? $sql.= "fecha_control='" . $this->fechaControl . "', " : false;
        !is_null($this->nroExp) ? $sql.= "nro_exp='" . $this->nroExp . "', " : false;
        !is_null($this->traba) ? $sql.= "traba='" . $this->traba . "', " : false;
        !is_null($this->online) ? $sql.= "online='" . $this->online . "', " : false;
        !is_null($this->nroExpExt) ? $sql.= "nro_exp_ext='" . $this->nroExpExt . "', " : false;
        !is_null($this->fechaExpExt) ? $sql.= "fecha_exp_ext='" . $this->fechaExpExt . "', " : false;
        !is_null($this->periodoContable) ? $sql.= "periodo_contable='" . $this->periodoContable . "', " : false;
        !is_null($this->periodoActual) ? $sql.= "periodo_actual='" . $this->periodoActual . "', " : false;
        !is_null($this->nroFactOffline) ? $sql.= "nro_fact_offline='" . $this->nroFactOffline . "', " : false;
        !is_null($this->recepcionId) ? $sql.= "recepcion_id='" . $this->recepcionId . "', " : false;
        !is_null($this->fechaEntrada) ? $sql.= "fecha_entrada='" . $this->fechaEntrada . "', " : false;
        !is_null($this->ctrl) ? $sql.= "ctrl='" . $this->ctrl . "', " : false;
        !is_null($this->tipoNomenclador) ? $sql.= "tipo_nomenclador='" . $this->tipoNomenclador . "', " : false;
        !is_null($this->tipoLiquidacion) ? $sql.= "tipo_liquidacion='" . $this->tipoLiquidacion . "', " : false;
        $sql.= "fecha_modificacion='" . $this->fechaModificacion . "' ";
        $sql.= "usuario='" . $this->usuario . "' ";

        $sql.="WHERE id_factura='$this->idFactura' ";
        $sql.="RETURNING id_factura";
        return($sql);
    }

    public function guardarFactura() {
        $this->fechaModificacion = date("Y-m-d H:i:s");
        if ($this->idFactura) {
            $sql = $this->getSQLUpdate();
        } else {
            $sql = $this->getSQlInsert();
        }
        $result = sql($sql);
        return $result->fields['id_factura'];
    }

}

class FacturaColeccion {

    var $registro = array();
    var $facturas = '';

    function __construct() {
        
    }

#	Metodo Filtrar 		

    public static function Filtrar($where = '') {
        if (strlen($where) > 0) {
            $sql = Factura::getSQlSelectWhere($where);
        } else {
            $sql = Factura::getSQlSelect();
        }

        $result = sql($sql);

        $facturas = array();

        while (!$result->EOF) {

            $registro = new Factura();
            $registro->construirResult($result);

            $facturas[] = $registro;
            $result->MoveNext();
        }

        return($facturas);
    }

    public static function buscarPorExpediente($expediente) {
        $q = "SELECT * FROM facturacion.factura 
                WHERE nro_exp='$expediente'
                ORDER BY cuie";
        $result = sql($q) or die($db->ErrorMsg());

        $facturas = array();

        while (!$result->EOF) {

            $registro = new Factura();
            $registro->construirResult($result);

            $facturas[] = $registro;
            $result->MoveNext();
        }

        return($facturas);
    }

    public static function buscar($idfactura = '') {
        $factura = false;
        if ($idfactura != '') {
            $q = "SELECT * FROM facturacion.factura 
                WHERE id_factura='$idfactura'
                ORDER BY cuie";
            $result = sql($q) or die($db->ErrorMsg());
            if (!$result->EOF) {
                $registro = new Factura();
                $registro->construirResult($result);
                $factura = $registro;
            }
        }
        return($factura);
    }

    public static function existeFactura($nro_fact, $cuie) {
        $sql = "SELECT nro_fact_offline, nro_exp 
                        FROM facturacion.factura 
                        WHERE nro_fact_offline = '$nro_fact'
                        AND cuie='$cuie'";
        //AND nro_exp='$nro_exp'";
        $result = sql($sql) or excepcion('Error al buscar factura repetida');
        //print $sql;
        if ($result->RecordCount() > 0) {
            return $result->fields['nro_exp'];
        }
        return false;
    }

}

?>