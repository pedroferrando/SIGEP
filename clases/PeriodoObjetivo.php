<?php

/**
 * 
 *
 * @version 1.107
 * @package entity
 */
class PeriodoObjetivo {

    private $idPeriodoObjetivo;
    private $periodo;
    private $desde;
    private $hasta;
    private $usuario;
    private $fechaCarga;
    private $observaciones;

    public function construirResult($result) {
        $this->idPeriodoObjetivo = $result->fields['id_periodo_objetivo'];
        $this->periodo = $result->fields['periodo'];
        $this->desde = $result->fields['desde'];
        $this->hasta = $result->fields['hasta'];
        $this->usuario = $result->fields['usuario'];
        $this->fechaCarga = $result->fields['fecha_carga'];
        $this->fechaCarga = $result->fields['observaciones'];
    }

    /**
     * set value for id_periodo_objetivo 
     *
     * type:serial,size:10,default:nextval('facturacion.periodo_objetivo_id_periodo_objetivo_seq'::regclass),primary,unique,autoincrement
     *
     * @param mixed $idPeriodoObjetivo
     */
    public function setIdPeriodoObjetivo($idPeriodoObjetivo) {
        $this->idPeriodoObjetivo = $idPeriodoObjetivo;
    }

    /**
     * get value for id_periodo_objetivo 
     *
     * type:serial,size:10,default:nextval('facturacion.periodo_objetivo_id_periodo_objetivo_seq'::regclass),primary,unique,autoincrement
     *
     * @return mixed
     */
    public function getIdPeriodoObjetivo() {
        return $this->idPeriodoObjetivo;
    }

    /**
     * set value for periodo 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $periodo
     */
    public function setPeriodo($periodo) {
        $this->periodo = $periodo;
    }

    /**
     * get value for periodo 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getPeriodo() {
        return $this->periodo;
    }

    /**
     * set value for desde 
     *
     * type:date,size:13,default:null,nullable
     *
     * @param mixed $desde
     */
    public function setDesde($desde) {
        $this->desde = $desde;
    }

    /**
     * get value for desde 
     *
     * type:date,size:13,default:null,nullable
     *
     * @return mixed
     */
    public function getDesde() {
        return $this->desde;
    }

    /**
     * set value for hasta 
     *
     * type:date,size:13,default:null,nullable
     *
     * @param mixed $hasta
     */
    public function setHasta($hasta) {
        $this->hasta = $hasta;
    }

    /**
     * get value for hasta 
     *
     * type:date,size:13,default:null,nullable
     *
     * @return mixed
     */
    public function getHasta() {
        return $this->hasta;
    }

    public function getObservaciones() {
        return $this->observaciones;
    }

    public function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
    }

    /**
     * set value for usuario 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $usuario
     */
    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    /**
     * get value for usuario 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getUsuario() {
        return $this->usuario;
    }

    /**
     * set value for fecha_carga 
     *
     * type:timestamp,size:29,default:null,nullable
     *
     * @param mixed $fechaCarga
     */
    public function setFechaCarga($fechaCarga) {
        $this->fechaCarga = $fechaCarga;
    }

    /**
     * get value for fecha_carga 
     *
     * type:timestamp,size:29,default:null,nullable
     *
     * @return mixed
     */
    public function getFechaCarga() {
        return $this->fechaCarga;
    }

    public static function getSQlSelectWhere($where) {

        $sql = "
			SELECT *
			  FROM facturacion.periodo_objetivo
			  WHERE " . $where . "";

        return($sql);
    }

    public static function getSQlSelect($where) {

        $sql = "
			SELECT *
			  FROM facturacion.periodo_objetivo";

        return($sql);
    }

    public static function calcularPeriodo($fecha) {
        $periodo = null;
        $sql = "SELECT * FROM facturacion.periodo_objetivo
                WHERE '$fecha' BETWEEN desde AND hasta";
        $result = sql($sql);

        if (!$result->EOF) {
            $periodo = new PeriodoObjetivo();
            $periodo->construirResult($result);
        }
        return $periodo;
    }

}

class PeriodoObjetivoColeccion {

    var $registro = array();
    var $coleccionregistros = '';

    function __construct() {
        
    }

    ### SETTERS
    # Documentacion para el metodo getBeneficiarios 		

    public function getPeriodoObjetivo() {
        return($this->coleccionregistros);
    }

    #	Metodo Filtrar 		

    public static function Filtrar($where = '') {
        if (strlen($where) > 0) {
            $sql = PeriodoObjetivo::getSqlSelectWhere($where);
        } else {
            $sql = PeriodoObjetivo::getSQlSelect();
        }

        $result = sql($sql);

        $coleccionregistros = array();

        while (!$result->EOF) {

            $registro = new PeriodoObjetivo();
            $registro->construirResult($result);

            $coleccionregistros[] = $registro;
            $result->MoveNext();
        }

        return($coleccionregistros);
    }

}

?>