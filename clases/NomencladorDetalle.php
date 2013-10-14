<?php

/**
 * 
 *
 * @version 1.107
 * @package entity
 */
class NomencladorDetalle {

    private $idNomencladorDetalle;
    private $descripcion;
    private $fechaDesde;
    private $fechaHasta;
    private $modoFacturacion;
    private $usuario;
    private $fechaModificacion;

    public function construirResult($result) {
        $this->id_nomenclador_detalle = $result->fields['id_nomenclador_detalle'];
        $this->descripcion = $result->fields['descripcion'];
        $this->fecha_desde = $result->fields['fecha_desde'];
        $this->fecha_hasta = $result->fields['fecha_hasta'];
        $this->modo_facturacion = $result->fields['modo_facturacion'];
        $this->usuario = $result->fields['usuario'];
        $this->fecha_modificacion = $result->fields['fecha_modificacion'];
    }

    /**
     * set value for id_nomenclador_detalle 
     *
     * type:serial,size:10,default:nextval('facturacion.nomenclador_detalle_id_nomenclador_detalle_seq'::regclass),primary,unique,autoincrement
     *
     * @param mixed $idNomencladorDetalle
     */
    public function setIdNomencladorDetalle($idNomencladorDetalle) {
        $this->idNomencladorDetalle = $idNomencladorDetalle;
    }

    /**
     * get value for id_nomenclador_detalle 
     *
     * type:serial,size:10,default:nextval('facturacion.nomenclador_detalle_id_nomenclador_detalle_seq'::regclass),primary,unique,autoincrement
     *
     * @return mixed
     */
    public function getIdNomencladorDetalle() {
        return $this->idNomencladorDetalle;
    }

    /**
     * set value for descripcion 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $descripcion
     */
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    /**
     * get value for descripcion 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getDescripcion() {
        return $this->descripcion;
    }

    /**
     * set value for fecha_desde 
     *
     * type:date,size:13,default:null
     *
     * @param mixed $fechaDesde
     */
    public function setFechaDesde($fechaDesde) {
        $this->fechaDesde = $fechaDesde;
    }

    /**
     * get value for fecha_desde 
     *
     * type:date,size:13,default:null
     *
     * @return mixed
     */
    public function getFechaDesde() {
        return $this->fechaDesde;
    }

    /**
     * set value for fecha_hasta 
     *
     * type:date,size:13,default:null
     *
     * @param mixed $fechaHasta
     */
    public function setFechaHasta($fechaHasta) {
        $this->fechaHasta = $fechaHasta;
    }

    /**
     * get value for fecha_hasta 
     *
     * type:date,size:13,default:null
     *
     * @return mixed
     */
    public function getFechaHasta() {
        return $this->fechaHasta;
    }

    /**
     * set value for modo_facturacion 
     *
     * type:bpchar,size:1,default:1,nullable
     *
     * @param mixed $modoFacturacion
     */
    public function setModoFacturacion($modoFacturacion) {
        $this->modoFacturacion = $modoFacturacion;
    }

    /**
     * get value for modo_facturacion 
     *
     * type:bpchar,size:1,default:1,nullable
     *
     * @return mixed
     */
    public function getModoFacturacion() {
        return $this->modoFacturacion;
    }

    /**
     * set value for usuario 
     *
     * type:varchar,size:20,default:null,nullable
     *
     * @param mixed $usuario
     */
    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    /**
     * get value for usuario 
     *
     * type:varchar,size:20,default:null,nullable
     *
     * @return mixed
     */
    public function getUsuario() {
        return $this->usuario;
    }

    /**
     * set value for fecha_modificacion 
     *
     * type:timestamp,size:29,default:null,nullable
     *
     * @param mixed $fechaModificacion
     */
    public function setFechaModificacion($fechaModificacion) {
        $this->fechaModificacion = $fechaModificacion;
    }

    /**
     * get value for fecha_modificacion 
     *
     * type:timestamp,size:29,default:null,nullable
     *
     * @return mixed
     */
    public function getFechaModificacion() {
        return $this->fechaModificacion;
    }

    public static function getSQlSelectWhere($where) {

        $sql = "
			SELECT *
			  FROM facturacion.nomenclador_detalle
			  WHERE " . $where . "";

        return($sql);
    }

}

?>