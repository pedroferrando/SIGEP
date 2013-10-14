<?php

/**
 * 
 *
 * @version 1.107
 * @package entity
 */
class Smiefectores {

    private $cuie;
    private $nombreefector;
    private $sistema;
    private $domicilio;
    private $departamento;
    private $localidad;
    private $codPos;
    private $ciudad;
    private $referente;
    private $tel;
    private $tipoefector;
    private $codOrg;
    private $nivel;
    private $banco;
    private $nrocta;

    public function __construct($cuie = '') {
        if ($cuie != '') {
            $where = "cuie='$cuie'";
            $sql = Smiefectores::getSQlSelectWhere($where);
            $result = sql($sql);
            if (!$result->EOF) {
                $this->construirResult($result);
            }
        }
    }

    public function construirResult($result) {
        $this->cuie = $result->fields['cuie'];
        $this->nombreefector = $result->fields['nombreefector'];
        $this->sistema = $result->fields['sistema'];
        $this->domicilio = $result->fields['domicilio'];
        $this->departamento = $result->fields['departamento'];
        $this->localidad = $result->fields['localidad'];
        $this->codPos = $result->fields['cod_pos'];
        $this->ciudad = $result->fields['ciudad'];
        $this->referente = $result->fields['referente'];
        $this->tel = $result->fields['tel'];
        $this->tipoefector = $result->fields['tipoefector'];
        $this->codOrg = $result->fields['cod_org'];
        $this->nivel = $result->fields['nivel'];
        $this->banco = $result->fields['banco'];
        $this->nrocta = $result->fields['nrocta'];
    }

    /**
     * set value for cuie 
     *
     * type:text,size:2147483647,default:null,primary,unique
     *
     * @param mixed $cuie
     */
    public function setCuie($cuie) {
        $this->cuie = $cuie;
    }

    /**
     * get value for cuie 
     *
     * type:text,size:2147483647,default:null,primary,unique
     *
     * @return mixed
     */
    public function getCuie() {
        return $this->cuie;
    }

    /**
     * set value for nombreefector 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $nombreefector
     */
    public function setNombreefector($nombreefector) {
        $this->nombreefector = $nombreefector;
    }

    /**
     * get value for nombreefector 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getNombreefector() {
        return $this->nombreefector;
    }

    /**
     * set value for sistema 
     *
     * type:int4,size:10,default:3
     *
     * @param mixed $sistema
     */
    public function setSistema($sistema) {
        $this->sistema = $sistema;
    }

    /**
     * get value for sistema 
     *
     * type:int4,size:10,default:3
     *
     * @return mixed
     */
    public function getSistema() {
        return $this->sistema;
    }

    /**
     * set value for domicilio 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $domicilio
     */
    public function setDomicilio($domicilio) {
        $this->domicilio = $domicilio;
    }

    /**
     * get value for domicilio 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getDomicilio() {
        return $this->domicilio;
    }

    /**
     * set value for departamento 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $departamento
     */
    public function setDepartamento($departamento) {
        $this->departamento = $departamento;
    }

    /**
     * get value for departamento 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getDepartamento() {
        return $this->departamento;
    }

    /**
     * set value for localidad 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $localidad
     */
    public function setLocalidad($localidad) {
        $this->localidad = $localidad;
    }

    /**
     * get value for localidad 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getLocalidad() {
        return $this->localidad;
    }

    /**
     * set value for cod_pos 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $codPos
     */
    public function setCodPos($codPos) {
        $this->codPos = $codPos;
    }

    /**
     * get value for cod_pos 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getCodPos() {
        return $this->codPos;
    }

    /**
     * set value for ciudad 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $ciudad
     */
    public function setCiudad($ciudad) {
        $this->ciudad = $ciudad;
    }

    /**
     * get value for ciudad 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getCiudad() {
        return $this->ciudad;
    }

    /**
     * set value for referente 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $referente
     */
    public function setReferente($referente) {
        $this->referente = $referente;
    }

    /**
     * get value for referente 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getReferente() {
        return $this->referente;
    }

    /**
     * set value for tel 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $tel
     */
    public function setTel($tel) {
        $this->tel = $tel;
    }

    /**
     * get value for tel 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getTel() {
        return $this->tel;
    }

    /**
     * set value for tipoefector 
     *
     * type:varchar,size:4,default:null,nullable
     *
     * @param mixed $tipoefector
     */
    public function setTipoefector($tipoefector) {
        $this->tipoefector = $tipoefector;
    }

    /**
     * get value for tipoefector 
     *
     * type:varchar,size:4,default:null,nullable
     *
     * @return mixed
     */
    public function getTipoefector() {
        return $this->tipoefector;
    }

    /**
     * set value for cod_org 
     *
     * type:varchar,size:5,default:null,nullable
     *
     * @param mixed $codOrg
     */
    public function setCodOrg($codOrg) {
        $this->codOrg = $codOrg;
    }

    /**
     * get value for cod_org 
     *
     * type:varchar,size:5,default:null,nullable
     *
     * @return mixed
     */
    public function getCodOrg() {
        return $this->codOrg;
    }

    /**
     * set value for nivel 
     *
     * type:numeric,size:131089,default:null,nullable
     *
     * @param mixed $nivel
     */
    public function setNivel($nivel) {
        $this->nivel = $nivel;
    }

    /**
     * get value for nivel 
     *
     * type:numeric,size:131089,default:null,nullable
     *
     * @return mixed
     */
    public function getNivel() {
        return $this->nivel;
    }

    /**
     * set value for banco 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @param mixed $banco
     */
    public function setBanco($banco) {
        $this->banco = $banco;
    }

    /**
     * get value for banco 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @return mixed
     */
    public function getBanco() {
        return $this->banco;
    }

    /**
     * set value for nrocta 
     *
     * type:varchar,size:2147483647,default:null,nullable
     *
     * @param mixed $nrocta
     */
    public function setNrocta($nrocta) {
        $this->nrocta = $nrocta;
    }

    /**
     * get value for nrocta 
     *
     * type:varchar,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getNrocta() {
        return $this->nrocta;
    }

    public static function getSQlSelectWhere($where) {

        $sql = "
            SELECT *
              FROM facturacion.smiefectores
              WHERE " . $where . "";

        return($sql);
    }

    public static function getSQlSelect() {
        $sql = "SELECT *
              FROM facturacion.smiefectores";

        return($sql);
    }

    public function tiposDeNomenclador() {
        $nomencladores = false;
        if ($this->cuie) {
            $sql_tipos = "select nom_basico,nom_cc_catastrofico,nom_perinatal_catastrofico,nom_perinatal_nocatastrofico,
                    nom_remediar,nom_cc_nocatastrofico,nom_basico_2,nom_rondas,nom_talleres
                    from nacer.conv_nom cn
                  inner join nacer.efe_conv ec using (id_efe_conv)
                  where cuie='$this->cuie'
                  AND cn.activo='t'
                  AND ec.activo='t'";
            $res_tipos = sql($sql_tipos) or fin_pagina();
            if (!$res_tipos->EOF) {

                if ($res_tipos->fields['nom_basico'] == 't') {
                    $tipos_de_nomenclador['BASICO'] = 'Basico';
                }
                if ($res_tipos->fields['nom_basico_2'] == 't') {
                    $tipos_de_nomenclador['BASICO_2'] = 'Basico 2';
                }
                if ($res_tipos->fields['nom_cc_catastrofico'] == 't') {
                    $tipos_de_nomenclador['CC_CATASTROFICO'] = 'CC Catastrofico';
                }
                if ($res_tipos->fields['nom_perinatal_catastrofico'] == 't') {
                    $tipos_de_nomenclador['PERINATAL_CATASTROFICO'] = 'Perinatal Catastrofico';
                }
                if ($res_tipos->fields['nom_cc_nocatastrofico'] == 't') {
                    $tipos_de_nomenclador['CC_NOCATASTROFICO'] = 'CC No Catastrofico';
                }
                if ($res_tipos->fields['nom_perinatal_nocatastrofico'] == 't') {
                    $tipos_de_nomenclador['PERINATAL_NO_CATASTROFICO'] = 'Perinatal No Catastrofico';
                }
                if ($res_tipos->fields['nom_remediar'] == 't') {
                    $tipos_de_nomenclador['REMEDIAR'] = 'Remediar';
                }
                if ($res_tipos->fields['nom_rondas'] == 't') {
                    $tipos_de_nomenclador['RONDAS'] = 'Rondas';
                }
                if ($res_tipos->fields['nom_talleres'] == 't') {
                    $tipos_de_nomenclador['TALLERES'] = 'Talleres';
                }
                $nomencladores = $tipos_de_nomenclador;
            }
        }
        return $nomencladores;
    }

}

class SmiefectoresColeccion {

    function __construct() {
        
    }

    #	Metodo Filtrar 		

    public static function Filtrar($where = '') {
        if (strlen($where) > 0) {
            $sql = Smiefectores::getSQlSelectWhere($where);
        }// else {
        //$sql = Expediente::getSQlSelect();
        //}

        $result = sql($sql);

        if (!$result->EOF) {
            $registro = new Smiefectores();
            $registro->construirResult($result);
        }

        return($registro);
    }

}
?>