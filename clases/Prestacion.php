<?php

class Prestacion {

    private $idPrestacion;
    private $idComprobante;
    private $idNomenclador;
    private $cantidad;
    private $precioPrestacion;
    private $idAnexo;
    private $peso;
    private $tensionArterial;
    private $prestacionid;
    private $comprobante;
    private $debito;
    private $nomenclador;

    public function construirResult($result) {
        $this->idComprobante = $result->fields['id_comprobante'];
        $this->idPrestacion = $result->fields['id_prestacion'];
        $this->precioPrestacion = $result->fields['precio_prestacion'];
        $this->idNomenclador = $result->fields['id_nomenclador'];
        $this->cantidad = $result->fields['cantidad'];
        $this->idAnexo = $result->fields['id_anexo'];
        $this->peso = $result->fields['peso'];
        $this->tensionArterial = $result->fields['tension_arterial'];
        $this->prestacionid = $result->fields['prestacionid'];
    }

    /**
     * set value for id_prestacion 
     *
     * type:serial,size:10,default:nextval('facturacion.prestacion_id_prestacion_seq'::regclass),primary,unique,autoincrement
     *
     * @param mixed $idPrestacion
     */
    public function setIdPrestacion($idPrestacion) {
        $this->idPrestacion = $idPrestacion;
    }

    /**
     * get value for id_prestacion 
     *
     * type:serial,size:10,default:nextval('facturacion.prestacion_id_prestacion_seq'::regclass),primary,unique,autoincrement
     *
     * @return mixed
     */
    public function getIdPrestacion() {
        return $this->idPrestacion;
    }

    /**
     * set value for id_comprobante 
     *
     * type:int4,size:10,default:null,index
     *
     * @param mixed $idComprobante
     */
    public function setIdComprobante($idComprobante) {
        $this->idComprobante = $idComprobante;
    }

    /**
     * get value for id_comprobante 
     *
     * type:int4,size:10,default:null,index
     *
     * @return mixed
     */
    public function getIdComprobante() {
        return $this->idComprobante;
    }

    /**
     * set value for id_nomenclador 
     *
     * type:int4,size:10,default:null,index
     *
     * @param mixed $idNomenclador
     */
    public function setIdNomenclador($idNomenclador) {
        $this->idNomenclador = $idNomenclador;
    }

    /**
     * get value for id_nomenclador 
     *
     * type:int4,size:10,default:null,index
     *
     * @return mixed
     */
    public function getIdNomenclador() {
        return $this->idNomenclador;
    }

    public function getNomenclador() {
        if (!$this->nomenclador) {
            $nomenclador = Nomenclador::buscarNomencladorPorId($this->idNomenclador);
            $this->nomenclador = $nomenclador;
        }
        return $this->nomenclador;
    }

    /**
     * set value for cantidad 
     *
     * type:int4,size:10,default:1,nullable
     *
     * @param mixed $cantidad
     */
    public function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    }

    /**
     * get value for cantidad 
     *
     * type:int4,size:10,default:1,nullable
     *
     * @return mixed
     */
    public function getCantidad() {
        return $this->cantidad;
    }

    /**
     * set value for precio_prestacion 
     *
     * type:numeric,size:30,default:0,nullable
     *
     * @param mixed $precioPrestacion
     */
    public function setPrecioPrestacion($precioPrestacion) {
        $this->precioPrestacion = $precioPrestacion;
    }

    /**
     * get value for precio_prestacion 
     *
     * type:numeric,size:30,default:0,nullable
     *
     * @return mixed
     */
    public function getPrecioPrestacion() {
        return $this->precioPrestacion;
    }

    /**
     * set value for id_anexo 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @param mixed $idAnexo
     */
    public function setIdAnexo($idAnexo) {
        $this->idAnexo = $idAnexo;
    }

    /**
     * get value for id_anexo 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @return mixed
     */
    public function getIdAnexo() {
        return $this->idAnexo;
    }

    /**
     * set value for peso 
     *
     * type:numeric,size:7,default:null,nullable
     *
     * @param mixed $peso
     */
    public function setPeso($peso) {
        $this->peso = $peso;
    }

    /**
     * get value for peso 
     *
     * type:numeric,size:7,default:null,nullable
     *
     * @return mixed
     */
    public function getPeso() {
        return $this->peso;
    }

    /**
     * set value for tension_arterial 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $tensionArterial
     */
    public function setTensionArterial($tensionArterial) {
        $this->tensionArterial = $tensionArterial;
    }

    /**
     * get value for tension_arterial 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getTensionArterial() {
        return $this->tensionArterial;
    }

    /**
     * set value for prestacionid 
     *
     * type:int8,size:19,default:null,nullable
     *
     * @param mixed $prestacionid
     */
    public function setPrestacionid($prestacionid) {
        $this->prestacionid = $prestacionid;
    }

    /**
     * get value for prestacionid 
     *
     * type:int8,size:19,default:null,nullable
     *
     * @return mixed
     */
    public function getPrestacionid() {
        return $this->prestacionid;
    }

    public function getSQLInsert() {
        $sql = "INSERT INTO facturacion.prestacion (id_comprobante, id_nomenclador,cantidad,precio_prestacion)
                    VALUES ('$this->idComprobante',
                            '$this->idNomenclador',
                            '$this->cantidad',
                            '$this->precioPrestacion')
                    RETURNING id_prestacion";
        return($sql);
    }

    # Documentacion para metodo getSQlUpdate

    public function getSQlUpdate() {
        $sql = "UPDATE facturacion.prestacion set ";
        !is_null($this->cantidad) ? $sql.="cantidad='" . $this->cantidad . "', " : false;
        !is_null($this->idNomenclador) ? $sql.="id_nomenclador='" . $this->idNomenclador . "', " : false;
        !is_null($this->precioPrestacion) ? $sql.="precio_prestacion='" . $this->precioPrestacion . "', " : false;
        $sql.="id_comprobante='" . $this->idComprobante . "' ";

        $sql.="WHERE id_prestacion='$this->idPrestacion' ";
        $sql.="RETURNING id_prestacion";
        return($sql);
    }

    # Documentacion para metodo getSQlDelete

    public function getSQlDelete() {
        $sql = '';
        return($sql);
    }

    public function guardarPrestacion() {
        if ($this->idPrestacion) {
            $sql = $this->getSQLUpdate();
        } else {
            $sql = $this->getSQlInsert();
        }
        $result = sql($sql);
        return $result->fields['id_prestacion'];
    }

    public function getGrupoEtario() {
        $nomenclador = $this->getNomenclador();
        $comprobante = $this->getComprobante();
        $beneficiario = $comprobante->getBeneficiarioUAD();
        $grupo_etareo = $beneficiario->getGrupoEtareo($comprobante->getFechaComprobante());
        $embarazo = $beneficiario->getEmbarazado($comprobante->getFechaComprobante());
        if ($embarazo) {
            if ($nomenclador->getPrecioSegunGrupo($embarazada) > 0) {
                //$datos['precio'] = $nomenclador->getPrecioSegunGrupo($embarazada);
                //  $datos['grupo_precio'] = 'embarazada';
                $datos = 'embarazada';
            } else {
                // $datos['precio'] = $nomenclador->getPrecioSegunGrupo($grupo_etareo);
                // $datos['grupo_precio'] = $grupo_etareo;
                $datos = $grupo_etareo;
            }
        } else {
            //$datos['precio'] = $nomenclador->getPrecioSegunGrupo($grupo_etareo);
            //$datos['grupo_precio'] = $grupo_etareo;
            $datos = $grupo_etareo;
        }
        return $datos;
    }

    #	Metodo Automata 		

    public function Automata($where) {
        $sql = Prestacion::getSqlSelectWhere($where);
        $result = sql($sql);
        $this->construirResult($result);
    }

    #	Metodo getSQlSelectWhere 		

    public static function getSQlSelectWhere($where) {

        $sql = "
			SELECT *
			  FROM facturacion.prestacion
			  WHERE " . $where . "";

        return($sql);
    }

    public static function getSQlSelect() {
        $sql = "
			SELECT *
			  FROM facturacion.prestacion";

        return($sql);
    }

    public function getComprobante() {
        if (!$this->comprobante) {
            $comprobante = Comprobante::getComprobantePorId($this->idComprobante);
            $this->comprobante = $comprobante;
        }
        return $this->comprobante;
    }

    public function getTotal() {
        return ($this->getCantidad() * $this->getPrecioPrestacion());
    }

    public function getDebito() {
        if (is_null($this->debito)) {
            $debito = Debito::getDebitoPorPrestacion($this->idPrestacion);
            $this->debito = $debito;
        }
        return $this->debito;
    }

    public function estaDebitada() {
        return DebitoColeccion::practicaDebitada($this->idPrestacion);
    }

    public function estaDebitadaVieja() {
        return DebitoColeccion::practicaDebitadaVieja($this->idComprobante);
    }

}

/**
 * 
 */
class PrestacionColeccion {

    var $registro = array();
    var $coleccionregistros = '';

    ### SETTERS
    # Documentacion para el metodo getBeneficiarios 		

    public function getPrestaciones() {
        return($this->coleccionregistros);
    }

    #	Metodo Filtrar 		

    public static function Filtrar($where = '') {
        if (strlen($where) > 0) {
            $sql = Prestacion::getSqlSelectWhere($where);
        } else {
            $sql = Prestacion::getSQlSelect();
        }

        $result = sql($sql);

        $coleccionregistros = array();

        while (!$result->EOF) {

            $registro = new Prestacion();
            $registro->construirResult($result);

            $coleccionregistros[] = $registro;
            $result->MoveNext();
        }

        return($coleccionregistros);
    }

    public static function buscarPrestacionPorId($id_prestacion) {
        $where = "id_prestacion='$id_prestacion'";
        $sql = Prestacion::getSqlSelectWhere($where);
        $result = sql($sql);
        if (!$result->EOF) {
            $registro = new Prestacion();
            $registro->construirResult($result);
        }
        return $registro;
    }

    public static function buscarPrestacionPorComprobante($id_comprobante) {
        $prestacion_aux = false;
        $where = "id_comprobante='$id_comprobante'";
        $sql = Prestacion::getSQlSelectWhere($where);
        $result = sql($sql);
        if (!$result->EOF) {
            $prestacion_aux = new Prestacion();
            $prestacion_aux->construirResult($result);
        }
        return $prestacion_aux;
    }

    public static function practicasSinDebitoEnExpediente($cuie, $expediente, $filtro = '') {
        if ($filtro != '') {
            $where = " AND clavebeneficiario ='$filtro'";
        }
        $sql = "SELECT p.* FROM facturacion.prestacion p                
                INNER JOIN facturacion.comprobante c ON(p.id_comprobante=c.id_comprobante)
                INNER JOIN facturacion.factura f ON (c.id_factura=f.id_factura)
                INNER JOIN facturacion.expediente e using (nro_exp)
                LEFT JOIN facturacion.debito d ON(p.id_comprobante=d.id_comprobante AND p.id_prestacion=d.id_prestacion)
                WHERE f.cuie='$cuie' AND f.nro_exp='$expediente' AND c.id_factura is not null AND id_debito is NULL
                AND f.estado='C' AND f.ctrl='S' AND e.estado<>'C'" . $where;

        $result = sql($sql);

        $coleccionregistros = array();

        while (!$result->EOF) {

            $registro = new Prestacion();
            $registro->construirResult($result);

            $coleccionregistros[] = $registro;
            $result->MoveNext();
        }

        return $coleccionregistros;
    }

    public static function practicasConDebitoEnExpediente($cuie, $expediente, $filtro = '') {
        if ($filtro != '') {
            $where = " AND clavebeneficiario ='$filtro'";
        }
        $sql = "SELECT p.* FROM facturacion.prestacion p                
                INNER JOIN facturacion.comprobante c ON(p.id_comprobante=c.id_comprobante)
                INNER JOIN facturacion.factura f ON (c.id_factura=f.id_factura)
                INNER JOIN facturacion.expediente e using (nro_exp)
                LEFT JOIN facturacion.debito d ON(p.id_comprobante=d.id_comprobante AND p.id_prestacion=d.id_prestacion)
                WHERE f.cuie='$cuie' AND f.nro_exp='$expediente' AND c.id_factura is not null AND id_debito IS NOT NULL
                AND d.id_motivo_d='82'
                AND f.estado='C' AND f.ctrl='S' AND e.estado<>'C'" . $where;

        $result = sql($sql);

        $coleccionregistros = array();

        while (!$result->EOF) {

            $registro = new Prestacion();
            $registro->construirResult($result);

            $coleccionregistros[] = $registro;
            $result->MoveNext();
        }

        return $coleccionregistros;
    }
    
    public static function getPrestacionesParaDebitoRetroactivo($cuie, $expte="", $nroFac="", $claveDoc="" ){
        if($expte!=""){
            $where .= " AND fac.nro_exp='$expte' ";
        }
        if($nroFac!=""){
            $where .= " AND fac.nro_fact_offline='$nroFac' ";
        }
        if($claveDoc!=""){
            $where .= " AND ( com.clavebeneficiario='$claveDoc' 
                            OR 
                              (ben.numero_doc='$claveDoc' AND ben.clase_documento_benef='P') 
                            ) ";
        }
        $sql = "SELECT DISTINCT(pre.id_prestacion), fac.nro_exp, fac.nro_fact_offline, 
                       com.fecha_comprobante, pre.cantidad, pre.precio_prestacion, 
                       nom.codigo, nom.diagnostico, ben.apellido_benef, ben.nombre_benef,
                       ben.apellido_benef_otro, ben.nombre_benef_otro
                FROM facturacion.factura fac 
                JOIN facturacion.expediente exp ON fac.nro_exp=exp.nro_exp
                JOIN facturacion.comprobante com ON fac.id_factura=com.id_factura
                JOIN facturacion.prestacion pre ON com.id_comprobante=pre.id_comprobante
                JOIN facturacion.nomenclador nom ON pre.id_nomenclador=nom.id_nomenclador 
                JOIN uad.beneficiarios ben ON com.clavebeneficiario=ben.clave_beneficiario 
                LEFT JOIN facturacion.debito deb ON pre.id_prestacion=deb.id_prestacion 
                LEFT JOIN facturacion.debito_retroactivo deb_ret ON pre.id_prestacion=deb_ret.id_prestacion 
                WHERE deb.id_debito IS NULL 
                  AND deb_ret.id IS NULL 
                  AND fac.cuie='$cuie' 
                  AND exp.estado='C'
                  ".$where."
                ORDER BY fecha_comprobante DESC
                ";
        //echo $sql;
        $res = sql($sql);
        return $res;        
    }
}

?>
