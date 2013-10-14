<?php

/**
 * 
 *
 * @version 1.107
 * @package entity
 */
class Debito {

    private $idDebito;
    private $idFactura;
    private $idNomenclador;
    private $cantidad;
    private $idMotivoD;
    private $monto;
    private $documentoDeb;
    private $apellidoDeb;
    private $nombreDeb;
    private $codigoDeb;
    private $observacionesDeb;
    private $mensajeBaja;
    private $idComprobante;
    private $idPrestacion;
    private $prestacion;

    public function construirResult($result) {
        $this->idDebito = $result->fields['id_debito'];
        $this->idFactura = $result->fields['id_factura'];
        $this->idNomenclador = $result->fields['id_nomenclador'];
        $this->cantidad = $result->fields['cantidad'];
        $this->idMotivoD = $result->fields['id_motivo_d'];
        $this->monto = $result->fields['monto'];
        $this->documentoDeb = $result->fields['documento_deb'];
        $this->apellidoDeb = $result->fields['apellido_deb'];
        $this->nombreDeb = $result->fields['nombre_deb'];
        $this->codigoDeb = $result->fields['codigo_deb'];
        $this->observacionesDeb = $result->fields['observaciones_deb'];
        $this->mensajeBaja = $result->fields['mensaje_baja'];
        $this->idComprobante = $result->fields['id_comprobante'];
        $this->idPrestacion = $result->fields['id_prestacion'];
    }

    /**
     * set value for id_debito 
     *
     * type:serial,size:10,default:nextval('facturacion.debito_id_debito_seq'::regclass),primary,unique,autoincrement
     *
     * @param mixed $idDebito
     */
    public function setIdDebito($idDebito) {
        $this->idDebito = $idDebito;
    }

    /**
     * get value for id_debito 
     *
     * type:serial,size:10,default:nextval('facturacion.debito_id_debito_seq'::regclass),primary,unique,autoincrement
     *
     * @return mixed
     */
    public function getIdDebito() {
        return $this->idDebito;
    }

    /**
     * set value for id_factura 
     *
     * type:int4,size:10,default:null,index
     *
     * @param mixed $idFactura
     */
    public function setIdFactura($idFactura) {
        $this->idFactura = $idFactura;
    }

    /**
     * get value for id_factura 
     *
     * type:int4,size:10,default:null,index
     *
     * @return mixed
     */
    public function getIdFactura() {
        return $this->idFactura;
    }

    /**
     * set value for id_nomenclador 
     *
     * type:int4,size:10,default:null
     *
     * @param mixed $idNomenclador
     */
    public function setIdNomenclador($idNomenclador) {
        $this->idNomenclador = $idNomenclador;
    }

    /**
     * get value for id_nomenclador 
     *
     * type:int4,size:10,default:null
     *
     * @return mixed
     */
    public function getIdNomenclador() {
        return $this->idNomenclador;
    }

    /**
     * set value for cantidad 
     *
     * type:numeric,size:30,default:null,nullable
     *
     * @param mixed $cantidad
     */
    public function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    }

    /**
     * get value for cantidad 
     *
     * type:numeric,size:30,default:null,nullable
     *
     * @return mixed
     */
    public function getCantidad() {
        return $this->cantidad;
    }

    /**
     * set value for id_motivo_d 
     *
     * type:int4,size:10,default:null
     *
     * @param mixed $idMotivoD
     */
    public function setIdMotivoD($idMotivoD) {
        $this->idMotivoD = $idMotivoD;
    }

    /**
     * get value for id_motivo_d 
     *
     * type:int4,size:10,default:null
     *
     * @return mixed
     */
    public function getIdMotivoD() {
        return $this->idMotivoD;
    }

    /**
     * set value for monto 
     *
     * type:numeric,size:30,default:null,nullable
     *
     * @param mixed $monto
     */
    public function setMonto($monto) {
        $this->monto = $monto;
    }

    /**
     * get value for monto 
     *
     * type:numeric,size:30,default:null,nullable
     *
     * @return mixed
     */
    public function getMonto() {
        return $this->monto;
    }

    public function getMontoTotal() {
        $monto = $this->getCantidad() * $this->getMonto();
        return $monto;
    }

    /**
     * set value for documento_deb 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $documentoDeb
     */
    public function setDocumentoDeb($documentoDeb) {
        $this->documentoDeb = $documentoDeb;
    }

    /**
     * get value for documento_deb 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getDocumentoDeb() {
        return $this->documentoDeb;
    }

    /**
     * set value for apellido_deb 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $apellidoDeb
     */
    public function setApellidoDeb($apellidoDeb) {
        $this->apellidoDeb = $apellidoDeb;
    }

    /**
     * get value for apellido_deb 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getApellidoDeb() {
        return $this->apellidoDeb;
    }

    /**
     * set value for nombre_deb 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $nombreDeb
     */
    public function setNombreDeb($nombreDeb) {
        $this->nombreDeb = $nombreDeb;
    }

    /**
     * get value for nombre_deb 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getNombreDeb() {
        return $this->nombreDeb;
    }

    /**
     * set value for codigo_deb 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $codigoDeb
     */
    public function setCodigoDeb($codigoDeb) {
        $this->codigoDeb = $codigoDeb;
    }

    /**
     * get value for codigo_deb 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getCodigoDeb() {
        return $this->codigoDeb;
    }

    /**
     * set value for observaciones_deb 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $observacionesDeb
     */
    public function setObservacionesDeb($observacionesDeb) {
        $this->observacionesDeb = $observacionesDeb;
    }

    /**
     * get value for observaciones_deb 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getObservacionesDeb() {
        return $this->observacionesDeb;
    }

    /**
     * set value for mensaje_baja 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $mensajeBaja
     */
    public function setMensajeBaja($mensajeBaja) {
        $this->mensajeBaja = $mensajeBaja;
    }

    /**
     * get value for mensaje_baja 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getMensajeBaja() {
        return $this->mensajeBaja;
    }

    /**
     * set value for id_comprobante 
     *
     * type:int4,size:10,default:null,index,nullable
     *
     * @param mixed $idComprobante
     */
    public function setIdComprobante($idComprobante) {
        $this->idComprobante = $idComprobante;
    }

    /**
     * get value for id_comprobante 
     *
     * type:int4,size:10,default:null,index,nullable
     *
     * @return mixed
     */
    public function getIdComprobante() {
        return $this->idComprobante;
    }

    /**
     * set value for id_prestacion 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @param mixed $idPrestacion
     */
    public function setIdPrestacion($idPrestacion) {
        $this->idPrestacion = $idPrestacion;
    }

    /**
     * get value for id_prestacion 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @return mixed
     */
    public function getIdPrestacion() {
        return $this->idPrestacion;
    }

    public function getPrestacion() {
        if (!$this->prestacion) {
            $prestacion = PrestacionColeccion::buscarPrestacionPorId($this->idPrestacion);
            $this->prestacion = $prestacion;
        }
        return $this->prestacion;
    }

    public function getPrestacionViejo() {
        if (!$this->prestacion) {
            $prestacion = PrestacionColeccion::buscarPrestacionPorComprobante($this->idComprobante);
            $this->prestacion = $prestacion;
        }
        return $this->prestacion;
    }

    public static function getDebitoPorPrestacion($id_prestacion) {
        $debito = null;
        $sql = "SELECT * FROM facturacion.debito
                WHERE id_prestacion='$id_prestacion'";
        $result = sql($sql);
        if (!$result->EOF) {
            $debito = new Debito();
            $debito->construirResult($result);
        }
        return $debito;
    }

    public static function getSQlSelectWhere($where) {

        $sql = "
            SELECT *
              FROM facturacion.debito
              WHERE " . $where . "";

        return($sql);
    }

    public static function getSQlSelect() {
        $sql = "SELECT *
              FROM facturacion.debito";

        return($sql);
    }

}

class DebitoColeccion {

    function __construct() {
        
    }

    #	Metodo Filtrar 		

    public static function Filtrar($where = '') {
        if (strlen($where) > 0) {
            $sql = Debito::getSQlSelectWhere($where);
        } //else {
        //$sql = Expediente::getSQlSelect();
        //}

        $result = sql($sql);
        $debitos = array();

        while (!$result->EOF) {

            $registro = new Debito();
            $registro->construirResult($result);

            $debitos[] = $registro;
            $result->MoveNext();
        }

        return($debitos);
    }

    public static function practicaDebitada($id_prestacion) {
        $valor = false;
        $sql = "SELECT id_debito
              FROM facturacion.debito
              WHERE id_prestacion='$id_prestacion'";
        $result = sql($sql);
        if (!$result->EOF) {
            $valor = true;
        }
        return $valor;
    }

    public static function practicaDebitadaVieja($id) {
        $valor = false;
        $sql = "SELECT id_debito
              FROM facturacion.debito
              WHERE id_comprobante='$id'";
        $result = sql($sql);
        if (!$result->EOF) {
            $valor = true;
        }
        return $valor;
    }

}

?>