<?php

/**
 * 
 *
 * @version 1.107
 * @package entity
 */
class Expediente {

    private $expedienteId;
    private $nroExp;
    private $montoTotalRechazado;
    private $estado;
    private $totalEstimulo;
    private $totalMontoLiquidado;
    private $totalPracRechazadas;
    private $totalPracticas;
    private $iniciador;
    private $fechaCierre;
    private $facturas;
    private $efectores;

    public function construirResult($result) {
        $this->expedienteId = $result->fields['expediente_id'];
        $this->nroExp = $result->fields['nro_exp'];
        $this->montoTotalRechazado = $result->fields['monto_total_rechazado'];
        $this->estado = $result->fields['estado'];
        $this->totalEstimulo = $result->fields['total_estimulo'];
        $this->totalMontoLiquidado = $result->fields['total_monto_liquidado'];
        $this->totalPracRechazadas = $result->fields['total_prac_rechazadas'];
        $this->totalPracticas = $result->fields['total_practicas'];
        $this->iniciador = $result->fields['iniciador'];
        $this->fechaCierre = $result->fields['fecha_cierre'];
    }

    /**
     * set value for expediente_id 
     *
     * type:serial,size:10,default:nextval('facturacion.expediente_expediente_id_seq'::regclass),primary,unique,autoincrement
     *
     * @param mixed $expedienteId
     */
    public function setExpedienteId($expedienteId) {
        $this->expedienteId = $expedienteId;
    }

    /**
     * get value for expediente_id 
     *
     * type:serial,size:10,default:nextval('facturacion.expediente_expediente_id_seq'::regclass),primary,unique,autoincrement
     *
     * @return mixed
     */
    public function getExpedienteId() {
        return $this->expedienteId;
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
     * set value for monto_total_rechazado 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $montoTotalRechazado
     */
    public function setMontoTotalRechazado($montoTotalRechazado) {
        $this->montoTotalRechazado = $montoTotalRechazado;
    }

    /**
     * get value for monto_total_rechazado 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getMontoTotalRechazado() {
        return $this->montoTotalRechazado;
    }

    /**
     * set value for estado 
     *
     * type:bpchar,size:1,default:null,nullable
     *
     * @param mixed $estado
     */
    public function setEstado($estado) {
        $this->estado = $estado;
    }

    /**
     * get value for estado 
     *
     * type:bpchar,size:1,default:null,nullable
     *
     * @return mixed
     */
    public function getEstado() {
        return $this->estado;
    }

    /**
     * set value for total_estimulo 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $totalEstimulo
     */
    public function setTotalEstimulo($totalEstimulo) {
        $this->totalEstimulo = $totalEstimulo;
    }

    /**
     * get value for total_estimulo 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getTotalEstimulo() {
        return $this->totalEstimulo;
    }

    /**
     * set value for total_monto_liquidado 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $totalMontoLiquidado
     */
    public function setTotalMontoLiquidado($totalMontoLiquidado) {
        $this->totalMontoLiquidado = $totalMontoLiquidado;
    }

    /**
     * get value for total_monto_liquidado 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getTotalMontoLiquidado() {
        return $this->totalMontoLiquidado;
    }

    /**
     * set value for total_prac_rechazadas 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @param mixed $totalPracRechazadas
     */
    public function setTotalPracRechazadas($totalPracRechazadas) {
        $this->totalPracRechazadas = $totalPracRechazadas;
    }

    /**
     * get value for total_prac_rechazadas 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @return mixed
     */
    public function getTotalPracRechazadas() {
        return $this->totalPracRechazadas;
    }

    /**
     * set value for total_practicas 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @param mixed $totalPracticas
     */
    public function setTotalPracticas($totalPracticas) {
        $this->totalPracticas = $totalPracticas;
    }

    /**
     * get value for total_practicas 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @return mixed
     */
    public function getTotalPracticas() {
        return $this->totalPracticas;
    }

    /**
     * set value for iniciador 
     *
     * type:varchar,size:6,default:null,nullable
     *
     * @param mixed $iniciador
     */
    public function setIniciador($iniciador) {
        $this->iniciador = $iniciador;
    }

    /**
     * get value for iniciador 
     *
     * type:varchar,size:6,default:null,nullable
     *
     * @return mixed
     */
    public function getIniciador() {
        return $this->iniciador;
    }

    /**
     * set value for fecha_cierre 
     *
     * type:timestamp,size:29,default:null,nullable
     *
     * @param mixed $fechaCierre
     */
    public function setFechaCierre($fechaCierre) {
        $this->fechaCierre = $fechaCierre;
    }

    /**
     * get value for fecha_cierre 
     *
     * type:timestamp,size:29,default:null,nullable
     *
     * @return mixed
     */
    public function getFechaCierre() {
        return $this->fechaCierre;
    }

    public function getFacturas() {
        if (count($this->facturas) == 0) {
            $facturas = array();
            $q = "SELECT * FROM facturacion.factura 
            WHERE nro_exp='$this->nroExp'
            ORDER BY cuie";
            $result = sql($q) or die($db->ErrorMsg());

            while (!$result->EOF) {

                $registro = new Factura();
                $registro->construirResult($result);

                $facturas[] = $registro;
                $result->MoveNext();
            }
            $this->facturas = $facturas;
        }

        return($this->facturas);
    }

    public function getEfectores() {
        if (count($this->efectores) == 0) {

            $array_cuies = array();
            $efectores = array();
            $facturas = $this->getFacturas();

            foreach ($facturas as $factura) {
                if (!in_array($factura->getCuie(), $array_cuies)) {
                    $array_cuies[] = $factura->getCuie();
                    $efectores[] = SmiefectoresColeccion::Filtrar("cuie='" . $factura->getCuie() . "'");
                }
            }
            $this->efectores = $efectores;
        }
        return ($this->efectores);
    }

    public function facturasDelEfector($cuie) {
        $facturas = array();
        $q = "SELECT * FROM facturacion.factura 
            WHERE nro_exp='$this->nroExp' AND cuie='$cuie'
            ORDER BY periodo DESC";
        $result = sql($q) or die($db->ErrorMsg());

        while (!$result->EOF) {
            $registro = new Factura();
            $registro->construirResult($result);
            $facturas[] = $registro;
            $result->MoveNext();
        }

        return($facturas);
    }

    public static function getSQlSelectWhere($where) {

        $sql = "
			SELECT *
			  FROM facturacion.expediente
			  WHERE " . $where . "";

        return($sql);
    }

    public static function getSQlSelect() {

        $sql = "
			SELECT *
			  FROM facturacion.expediente";

        return($sql);
    }

}

class ExpedienteCollecion {

    function __construct() {
        
    }

    #	Metodo Filtrar 		

    public static function Filtrar($where = '') {
        if (strlen($where) > 0) {
            $sql = Expediente::getSQlSelectWhere($where);
        } //else {
        //$sql = Expediente::getSQlSelect();
        //}

        $result = sql($sql);

        if (!$result->EOF) {
            $registro = new Expediente();
            $registro->construirResult($result);
        }

        // $expediente[] = $registro;
        // $result->MoveNext();
        //}

        return($registro);
    }
    
    /* busca y retorna los expedientes de 
     * un efector dado. Se puede especificar
     * el estado de un expte para filtrar 
    */
    public static function getExpedientesEfector($cuie,$estado="",$fields="*"){
        if($estado!=""){
            $where = " AND exp.estado='$estado' ";
        }
        $sql = "SELECT DISTINCT(exp.expediente_id) AS expediente_id, $fields 
                FROM facturacion.factura fac 
                JOIN facturacion.expediente exp ON fac.nro_exp=exp.nro_exp 
                WHERE fac.cuie='$cuie' 
                ".$where."  
                ORDER BY exp.expediente_id
               ";
        $res = sql($sql);
        return $res;
    }

}

?>