<?php

/**
 * 
 *
 * @version 1.107
 * @package entity
 */
class Comprobante {

    private $idComprobante;
    private $cuie;
    private $idFactura;
    private $nombreMedico;
    private $fechaComprobante;
    private $clavebeneficiario;
    private $idSmiafiliados;
    private $fechaCarga;
    private $comentario;
    private $marca;
    private $periodo;
    private $idServicio;
    private $activo;
    private $idBeneficiarios;
    private $entidadAlta;
    private $idvacuna;
    private $mensaje;
    private $fila;
    private $idprestacion;
    private $idNomencladorDetalle;
    private $idperiodo;
    private $grupoEtario;
    private $tipoNomenclador;
    private $usuario;

    public function construirResult($result) {
        $this->idComprobante = $result->fields['id_comprobante'];
        $this->cuie = $result->fields['cuie'];
        $this->idFactura = $result->fields['id_factura'];
        $this->nombreMedico = $result->fields['nombre_medico'];
        $this->fechaComprobante = $result->fields['fecha_comprobante'];
        $this->clavebeneficiario = $result->fields['clavebeneficiario'];
        $this->idSmiafiliados = $result->fields['id_smiafiliado'];
        $this->fechaCarga = $result->fields['fecha_carga'];
        $this->comentario = $result->fields['comentario'];
        $this->marca = $result->fields['marca'];
        $this->periodo = $result->fields['periodo'];
        $this->idServicio = $result->fields['id_servicio'];
        $this->activo = $result->fields['activo'];
        $this->idBeneficiarios = $result->fields['id_beneficiario'];
        $this->entidad_alta = $result->fields['entidad_alta'];
        $this->fila = $result->fields['fila'];
        $this->idprestacion = $result->fields['idprestacion'];
        $this->idperiodo = $result->fields['idperiodo'];
        $this->grupoEtario = $result->fields['grupo_etario'];
        $this->idNomencladorDetalle = $result->fields['id_nomenclador_detalle'];
        $this->tipoNomenclador = $result->fields['tipo_nomenclador'];
        $this->usuario = $result->fields['usuario'];
    }

    /**
     * set value for id_comprobante 
     *
     * type:serial,size:10,default:nextval('facturacion.comprobante_id_comprobante_seq'::regclass),primary,unique,autoincrement
     *
     * @param mixed $idComprobante
     */
    public function setIdComprobante($idComprobante) {
        $this->idComprobante = $idComprobante;
    }

    /**
     * get value for id_comprobante 
     *
     * type:serial,size:10,default:nextval('facturacion.comprobante_id_comprobante_seq'::regclass),primary,unique,autoincrement
     *
     * @return mixed
     */
    public function getIdComprobante() {
        return $this->idComprobante;
    }

    /**
     * set value for cuie 
     *
     * type:text,size:2147483647,default:null,index
     *
     * @param mixed $cuie
     */
    public function setCuie($cuie) {
        $this->cuie = $cuie;
    }

    /**
     * get value for cuie 
     *
     * type:text,size:2147483647,default:null,index
     *
     * @return mixed
     */
    public function getCuie() {
        return $this->cuie;
    }

    /**
     * set value for id_factura 
     *
     * type:int4,size:10,default:null,index,nullable
     *
     * @param mixed $idFactura
     */
    public function setIdFactura($idFactura) {
        $this->idFactura = $idFactura;
    }

    /**
     * get value for id_factura 
     *
     * type:int4,size:10,default:null,index,nullable
     *
     * @return mixed
     */
    public function getIdFactura() {
        return $this->idFactura;
    }

    /**
     * set value for nombre_medico 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $nombreMedico
     */
    public function setNombreMedico($nombreMedico) {
        $this->nombreMedico = $nombreMedico;
    }

    /**
     * get value for nombre_medico 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getNombreMedico() {
        return $this->nombreMedico;
    }

    /**
     * set value for fecha_comprobante 
     *
     * type:timestamp,size:29,default:null,nullable
     *
     * @param mixed $fechaComprobante
     */
    public function setFechaComprobante($fechaComprobante) {
        $this->fechaComprobante = $fechaComprobante;
    }

    /**
     * get value for fecha_comprobante 
     *
     * type:timestamp,size:29,default:null,nullable
     *
     * @return mixed
     */
    public function getFechaComprobante() {

        return substr($this->fechaComprobante, 0, 10);
    }

    /**
     * set value for clavebeneficiario 
     *
     * type:text,size:2147483647,default:null,index,nullable
     *
     * @param mixed $clavebeneficiario
     */
    public function setClavebeneficiario($clavebeneficiario) {
        $this->clavebeneficiario = $clavebeneficiario;
    }

    /**
     * get value for clavebeneficiario 
     *
     * type:text,size:2147483647,default:null,index,nullable
     *
     * @return mixed
     */
    public function getClavebeneficiario() {
        return $this->clavebeneficiario;
    }

    /**
     * set value for id_smiafiliados 
     *
     * type:int4,size:10,default:null,index,nullable
     *
     * @param mixed $idSmiafiliados
     */
    public function setIdSmiafiliados($idSmiafiliados) {
        $this->idSmiafiliados = $idSmiafiliados;
    }

    /**
     * get value for id_smiafiliados 
     *
     * type:int4,size:10,default:null,index,nullable
     *
     * @return mixed
     */
    public function getIdSmiafiliados() {
        return $this->idSmiafiliados;
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
     * set value for comentario 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $comentario
     */
    public function setComentario($comentario) {
        $this->comentario = $comentario;
    }

    /**
     * get value for comentario 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getComentario() {
        return $this->comentario;
    }

    /**
     * set value for marca 
     *
     * type:int4,size:10,default:0,nullable
     *
     * @param mixed $marca
     */
    public function setMarca($marca) {
        $this->marca = $marca;
    }

    /**
     * get value for marca 
     *
     * type:int4,size:10,default:0,nullable
     *
     * @return mixed
     */
    public function getMarca() {
        return $this->marca;
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
     * set value for id_servicio 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @param mixed $idServicio
     */
    public function setIdServicio($idServicio) {
        $this->idServicio = $idServicio;
    }

    /**
     * get value for id_servicio 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @return mixed
     */
    public function getIdServicio() {
        return $this->idServicio;
    }

    /**
     * set value for activo 
     *
     * type:bpchar,size:1,default:null,nullable
     *
     * @param mixed $activo
     */
    public function setActivo($activo) {
        $this->activo = $activo;
    }

    /**
     * get value for activo 
     *
     * type:bpchar,size:1,default:null,nullable
     *
     * @return mixed
     */
    public function getActivo() {
        return $this->activo;
    }

    /**
     * set value for id_beneficiarios 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @param mixed $idBeneficiarios
     */
    public function setIdBeneficiarios($idBeneficiarios) {
        $this->idBeneficiarios = $idBeneficiarios;
    }

    /**
     * get value for id_beneficiarios 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @return mixed
     */
    public function getIdBeneficiarios() {
        return $this->idBeneficiarios;
    }

    /**
     * set value for entidad_alta 
     *
     * type:bpchar,size:2,default:null,nullable
     *
     * @param mixed $entidadAlta
     */
    public function setEntidadAlta($entidadAlta) {
        $this->entidadAlta = $entidadAlta;
    }

    /**
     * get value for entidad_alta 
     *
     * type:bpchar,size:2,default:null,nullable
     *
     * @return mixed
     */
    public function getEntidadAlta() {
        return $this->entidadAlta;
    }

    /**
     * set value for idvacuna 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @param mixed $idvacuna
     */
    public function setIdvacuna($idvacuna) {
        $this->idvacuna = $idvacuna;
    }

    /**
     * get value for idvacuna 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @return mixed
     */
    public function getIdvacuna() {
        return $this->idvacuna;
    }

    /**
     * set value for mensaje 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $mensaje
     */
    public function setMensaje($mensaje) {
        $this->mensaje = $mensaje;
    }

    /**
     * get value for mensaje 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getMensaje() {
        return $this->mensaje;
    }

    /**
     * set value for fila 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @param mixed $fila
     */
    public function setFila($fila) {
        $this->fila = $fila;
    }

    /**
     * get value for fila 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @return mixed
     */
    public function getFila() {
        return $this->fila;
    }

    /**
     * set value for idprestacion 
     *
     * type:int8,size:19,default:null,index,nullable
     *
     * @param mixed $idprestacion
     */
    public function setIdprestacion($idprestacion) {
        $this->idprestacion = $idprestacion;
    }

    /**
     * get value for idprestacion 
     *
     * type:int8,size:19,default:null,index,nullable
     *
     * @return mixed
     */
    public function getIdprestacion() {
        return $this->idprestacion;
    }

    /**
     * set value for id_nomenclador_detalle 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @param mixed $idNomencladorDetalle
     */
    public function setIdNomencladorDetalle($idNomencladorDetalle) {
        $this->idNomencladorDetalle = $idNomencladorDetalle;
    }

    /**
     * get value for id_nomenclador_detalle 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @return mixed
     */
    public function getIdNomencladorDetalle() {
        return $this->idNomencladorDetalle;
    }

    /**
     * set value for idperiodo 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @param mixed $idperiodo
     */
    public function setIdperiodo($idperiodo) {
        $this->idperiodo = $idperiodo;
    }

    /**
     * get value for idperiodo 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @return mixed
     */
    public function getIdperiodo() {
        return $this->idperiodo;
    }

    /**
     * set value for grupo_etario 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $grupoEtario
     */
    public function setGrupoEtario($grupoEtario) {
        $this->grupoEtario = $grupoEtario;
    }

    /**
     * get value for grupo_etario 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getGrupoEtario() {
        return $this->grupoEtario;
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

    public function getUsuario() {
        return $this->usuario;
    }

    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    private $prestaciones;

    public function getPrestacion() {
        $prestacion = PrestacionColeccion::buscarPrestacionPorComprobante($this->idComprobante);
        return $prestacion;
    }

    public function getPrestaciones() {
        if (!$this->prestaciones) {
            $prestacion = PrestacionColeccion::Filtrar("id_comprobante='$this->idComprobante'");
            $this->prestaciones = $prestacion;
        }
        return $this->prestaciones;
    }

    public function getCantidadPracticas() {
        $prestaciones = $this->getPrestacion();
    }

    private $beneficiarioUad;

    public function getBeneficiarioUAD() {
        if (!$this->beneficiarioUad) {
            $beneficiario_aux = BeneficiarioUad::buscarPorClaveBeneficiario($this->clavebeneficiario);
            $this->beneficiarioUad = $beneficiario_aux;
        }
        return $this->beneficiarioUad;
    }

    private $beneficiarioSmi;

    public function getBeneficiarioSMI() {
        if (!$this->beneficiarioSmi) {
            $beneficiario_aux = BeneficiarioSmi::buscarPorClaveBeneficiario($this->clavebeneficiario);
            $this->beneficiarioSmi = $beneficiario_aux;
        }
        return $this->beneficiarioSmi;
    }

#	Metodo Automata 		

    public function Automata($where) {
        $sql = Comprobante::getSqlSelectWhere($where);
        $result = sql($sql);
        $this->construirResult($result);
    }

#	Metodo getSQlSelectWhere 		

    public static function getSQlSelectWhere($where) {

        $sql = "
			SELECT *
			  FROM facturacion.comprobante
			  WHERE " . $where . "";

        return($sql);
    }

    public static function getSQlSelectWherePractica($where) {

        $sql = "
			SELECT id_comprobante, cuie, id_factura, nombre_medico, 
			       fecha_comprobante, clavebeneficiario, id_smiafiliados, 
			       fecha_carga, comentario, marca, periodo,id_servicio,activo,
                               id_beneficiarios, entidad_alta, idvacuna, mensaje, fila,
                               idprestacion, idperiodo, grupo_etario, tipo_nomenclador
			  FROM facturacion.comprobante
                          INNER JOIN facturacion.prestacion using (id_comprobante)
			  WHERE " . $where . "";

        return($sql);
    }

    public static function getComprobantePorId($id_comprobante) {
        $where = "id_comprobante='$id_comprobante'";
        $sql = Comprobante::getSQlSelectWhere($where);
        $result = sql($sql);
        if (!$result->EOF) {
            $comprobante = new Comprobante();
            $comprobante->construirResult($result);
        }
        return $comprobante;
    }

# Documentacion para metodo getSQlInsert

    public function getSQlInsert() {
        $sql = "INSERT INTO uad.beneficiarios( cuie, id_factura, nombre_medico, 
			       fecha_comprobante,clavebeneficiario, id_smiafiliados, 
			       fecha_carga, comentario, marca, periodo,id_servicio,activo,
                               id_beneficiarios, entidad_alta, idvacuna, mensaje, fila,
                               idprestacion, idperiodo, grupo_etario, tipo_nomenclador,usuario,id_nomenclador_detalle)
			    VALUES (
			            " . $this->cuie . ",
			            " . $this->idFactura . ",
			            " . $this->nombreMedico . ",

			            " . $this->fechaComprobante . ",
			            " . $this->clavebeneficiario . ",
			            " . $this->idSmiafiliados . ",
			            " . $this->fechaCarga . ",

			            " . $this->comentario . ",
			            " . $this->marca . ",
			            " . $this->periodo . ",
			            " . $this->idServicio . ",
			            " . $this->activo . ",

			            " . $this->idBeneficiarios . ",
			            " . $this->entidad_alta . ",
			            " . $this->fila . ",
			            " . $this->idprestacion . ",
			            " . $this->idperiodo . ",
			            " . $this->grupoEtario . ",
			            " . $this->tipoNomenclador . "
                                    " . $this->usuario . "
                                    " . $this->idNomencladorDetalle . ")";
        return($sql);
    }

    public function getSQLUpdate() {
        $sql = "UPDATE facturacion.comprobante set ";
        !is_null($this->cuie) ? $sql.="cuie='" . $this->cuie . "', " : false;
        !is_null($this->idFactura) ? $sql.="id_factura='" . $this->idFactura . "', " : false;
        !is_null($this->nombreMedico) ? $sql.="nombre_medico='" . $this->nombreMedico . "', " : false;
        !is_null($this->fechaComprobante) ? $sql.="fecha_comprobante='" . $this->fechaComprobante . "', " : false;
        !is_null($this->clavebeneficiario) ? $sql.="clavebeneficiario='" . $this->clavebeneficiario . "', " : false;
        !is_null($this->idSmiafiliados) ? $sql.="id_smiafiliados='" . $this->idSmiafiliados . "', " : false;
        !is_null($this->fechaCarga) ? $sql.="fecha_carga='" . $this->fechaCarga . "', " : false;
        !is_null($this->comentario) ? $sql.="comentario='" . $this->comentario . "', " : false;
        !is_null($this->marca) ? $sql.="marca='" . $this->marca . "', " : false;
        !is_null($this->periodo) ? $sql.="periodo='" . $this->periodo . "', " : false;
        !is_null($this->idServicio) ? $sql.="id_servicio='" . $this->idServicio . "', " : false;
        !is_null($this->activo) ? $sql.="activo='" . $this->activo . "', " : false;
        !is_null($this->idBeneficiarios) ? $sql.="id_beneficiarios='" . $this->idBeneficiarios . "', " : false;
        !is_null($this->entidad_alta) ? $sql.="entidad_alta='" . $this->entidad_alta . "', " : false;
        !is_null($this->fila) ? $sql.="fila='" . $this->fila . "', " : false;
        !is_null($this->idprestacion) ? $sql.="idprestacion='" . $this->idprestacion . "', " : false;
        !is_null($this->idperiodo) ? $sql.="idperiodo='" . $this->idperiodo . "', " : false;
        !is_null($this->grupoEtario) ? $sql.="grupo_etario='" . $this->grupoEtario . "', " : false;
        !is_null($this->tipoNomenclador) ? $sql.="tipo_nomenclador='" . $this->tipoNomenclador . "', " : false;
        !is_null($this->idNomencladorDetalle) ? $sql.="id_nomenclador_detalle='" . $this->idNomencladorDetalle . "', " : false;
        $sql.="usuario='" . $this->usuario . "' ";

        $sql.="WHERE id_comprobante='$this->idComprobante' ";
        $sql.="returning id_comprobante";
        return $sql;
    }

    public static function getSQlSelect() {
        $sql = "
			SELECT id_comprobante, cuie, id_factura, nombre_medico, 
			       fecha_comprobante, clavebeneficiario, id_smiafiliados, 
			       fecha_carga, comentario, marca, periodo,id_servicio,activo,
                               id_beneficiarios, entidad_alta, idvacuna, mensaje, fila,
                               idprestacion, idperiodo, grupo_etario, tipo_nomenclador
			  FROM facturacion.comprobante";

        return($sql);
    }

    public function guardarComprobante() {
        if ($this->idComprobante) {
            $sql = $this->getSQLUpdate();
        } else {
            $sql = $this->getSQlInsert();
        }
        $result = sql($sql);
        return $result->fields['id_comprobante'];
    }

}

class ComprobanteColeccion {

    public static function Filtrar($where = '') {
        if (strlen($where) > 0) {
            $sql = Comprobante::getSQlSelectWhere($where);
        } else {
            $sql = Comprobante::getSQlSelect();
        }

        $result = sql($sql);

        $comprobantes = array();

        while (!$result->EOF) {

            $registro = new Comprobante();
            $registro->construirResult($result);

            $comprobantes[] = $registro;
            $result->MoveNext();
        }

        return($comprobantes





                );
    }

}

?>