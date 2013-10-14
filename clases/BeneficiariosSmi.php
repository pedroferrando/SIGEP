<?php

/**
 * 
 *
 * @version 1.107
 * @package entity
 */
class BeneficiarioSmi {

    private $idSmiafiliados;
    private $clavebeneficiario;
    private $afiapellido;
    private $afinombre;
    private $afitipodoc;
    private $aficlasedoc;
    private $afidni;
    private $afisexo;
    private $afitipocategoria;
    private $afifechanac;
    private $fechainscripcion;
    private $fechadiagnosticoembarazo;
    private $semanasembarazo;
    private $fechaprobableparto;
    private $fechaefectivaparto;
    private $activo;
    private $motivobaja;
    private $mensajebaja;
    private $idProcesobajaautomatica;
    private $fechacarga;
    private $clavebenefprovocobaja;
    private $idpersona;
    private $periodo;
    private $embarazoactual;
    private $fum;

    public function construirResult($result) {
        $this->idSmiafiliados = $result->fields['id_smiafiliados'];
        $this->clavebeneficiario = $result->fields['clavebeneficiario'];
        $this->afiapellido = $result->fields['afiapellido'];
        $this->afinombre = $result->fields['afinombre'];
        $this->afitipodoc = $result->fields['afitipodoc'];
        $this->aficlasedoc = $result->fields['aficlasedoc'];
        $this->afidni = $result->fields['afidni'];
        $this->afisexo = $result->fields['afisexo'];
        $this->afitipocategoria = $result->fields['afitipocategoria'];
        $this->afifechanac = $result->fields['afifechanac'];
        $this->fechainscripcion = $result->fields['fechainscripcion'];
        $this->fechadiagnosticoembarazo = $result->fields['fechadiagnosticoembarazo'];
        $this->semanasembarazo = $result->fields['semanasembarazo'];
        $this->fechaprobableparto = $result->fields['fechaprobableparto'];
        $this->fechaefectivaparto = $result->fields['fechaefectivaparto'];
        $this->activo = $result->fields['activo'];
        $this->motivobaja = $result->fields['motivobaja'];
        $this->mensajebaja = $result->fields['mensajebaja'];
        $this->idProcesobajaautomatica = $result->fields['id_procesobajaautomatica'];
        $this->fechacarga = $result->fields['fechacarga'];
        $this->clavebenefprovocobaja = $result->fields['clavebenefprovocobaja'];
        $this->idpersona = $result->fields['idpersona'];
        $this->embarazoactual = $result->fields['embarazoactual'];
        $this->fum = $result->fields['fum'];
    }

    /**
     * set value for id_smiafiliados 
     *
     * type:int4,size:10,default:null
     *
     * @param mixed $idSmiafiliados
     */
    public function setIdSmiafiliados($idSmiafiliados) {
        $this->idSmiafiliados = $idSmiafiliados;
    }

    /**
     * get value for id_smiafiliados 
     *
     * type:int4,size:10,default:null
     *
     * @return mixed
     */
    public function getIdSmiafiliados() {
        return $this->idSmiafiliados;
    }

    /**
     * set value for clavebeneficiario 
     *
     * type:varchar,size:16,default:null,primary,unique
     *
     * @param mixed $clavebeneficiario
     */
    public function setClavebeneficiario($clavebeneficiario) {
        $this->clavebeneficiario = $clavebeneficiario;
    }

    /**
     * get value for clavebeneficiario 
     *
     * type:varchar,size:16,default:null,primary,unique
     *
     * @return mixed
     */
    public function getClavebeneficiario() {
        return $this->clavebeneficiario;
    }

    /**
     * set value for afiapellido 
     *
     * type:varchar,size:40,default:null,nullable
     *
     * @param mixed $afiapellido
     */
    public function setAfiapellido($afiapellido) {
        $this->afiapellido = $afiapellido;
    }

    /**
     * get value for afiapellido 
     *
     * type:varchar,size:40,default:null,nullable
     *
     * @return mixed
     */
    public function getAfiapellido() {
        return $this->afiapellido;
    }

    /**
     * set value for afinombre 
     *
     * type:varchar,size:40,default:null,nullable
     *
     * @param mixed $afinombre
     */
    public function setAfinombre($afinombre) {
        $this->afinombre = $afinombre;
    }

    /**
     * get value for afinombre 
     *
     * type:varchar,size:40,default:null,nullable
     *
     * @return mixed
     */
    public function getAfinombre() {
        return $this->afinombre;
    }

    /**
     * set value for afitipodoc 
     *
     * type:varchar,size:5,default:null,nullable
     *
     * @param mixed $afitipodoc
     */
    public function setAfitipodoc($afitipodoc) {
        $this->afitipodoc = $afitipodoc;
    }

    /**
     * get value for afitipodoc 
     *
     * type:varchar,size:5,default:null,nullable
     *
     * @return mixed
     */
    public function getAfitipodoc() {
        return $this->afitipodoc;
    }

    /**
     * set value for aficlasedoc 
     *
     * type:varchar,size:1,default:null,nullable
     *
     * @param mixed $aficlasedoc
     */
    public function setAficlasedoc($aficlasedoc) {
        $this->aficlasedoc = $aficlasedoc;
    }

    /**
     * get value for aficlasedoc 
     *
     * type:varchar,size:1,default:null,nullable
     *
     * @return mixed
     */
    public function getAficlasedoc() {
        return $this->aficlasedoc;
    }

    /**
     * set value for afidni 
     *
     * type:varchar,size:12,default:null,nullable
     *
     * @param mixed $afidni
     */
    public function setAfidni($afidni) {
        $this->afidni = $afidni;
    }

    /**
     * get value for afidni 
     *
     * type:varchar,size:12,default:null,nullable
     *
     * @return mixed
     */
    public function getAfidni() {
        return $this->afidni;
    }

    /**
     * set value for afisexo 
     *
     * type:varchar,size:1,default:null,nullable
     *
     * @param mixed $afisexo
     */
    public function setAfisexo($afisexo) {
        $this->afisexo = $afisexo;
    }

    /**
     * get value for afisexo 
     *
     * type:varchar,size:1,default:null,nullable
     *
     * @return mixed
     */
    public function getAfisexo() {
        return $this->afisexo;
    }

    /**
     * set value for afitipocategoria 
     *
     * type:int2,size:5,default:null,nullable
     *
     * @param mixed $afitipocategoria
     */
    public function setAfitipocategoria($afitipocategoria) {
        $this->afitipocategoria = $afitipocategoria;
    }

    /**
     * get value for afitipocategoria 
     *
     * type:int2,size:5,default:null,nullable
     *
     * @return mixed
     */
    public function getAfitipocategoria() {
        return $this->afitipocategoria;
    }

    /**
     * set value for afifechanac 
     *
     * type:date,size:13,default:null,nullable
     *
     * @param mixed $afifechanac
     */
    public function setAfifechanac($afifechanac) {
        $this->afifechanac = $afifechanac;
    }

    /**
     * get value for afifechanac 
     *
     * type:date,size:13,default:null,nullable
     *
     * @return mixed
     */
    public function getAfifechanac() {
        return $this->afifechanac;
    }

    /**
     * set value for fechainscripcion 
     *
     * type:date,size:13,default:null,nullable
     *
     * @param mixed $fechainscripcion
     */
    public function setFechainscripcion($fechainscripcion) {
        $this->fechainscripcion = $fechainscripcion;
    }

    /**
     * get value for fechainscripcion 
     *
     * type:date,size:13,default:null,nullable
     *
     * @return mixed
     */
    public function getFechainscripcion() {
        return $this->fechainscripcion;
    }

    /**
     * set value for fechadiagnosticoembarazo 
     *
     * type:date,size:13,default:null,nullable
     *
     * @param mixed $fechadiagnosticoembarazo
     */
    public function setFechadiagnosticoembarazo($fechadiagnosticoembarazo) {
        $this->fechadiagnosticoembarazo = $fechadiagnosticoembarazo;
    }

    /**
     * get value for fechadiagnosticoembarazo 
     *
     * type:date,size:13,default:null,nullable
     *
     * @return mixed
     */
    public function getFechadiagnosticoembarazo() {
        return $this->fechadiagnosticoembarazo;
    }

    /**
     * set value for semanasembarazo 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @param mixed $semanasembarazo
     */
    public function setSemanasembarazo($semanasembarazo) {
        $this->semanasembarazo = $semanasembarazo;
    }

    /**
     * get value for semanasembarazo 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @return mixed
     */
    public function getSemanasembarazo() {
        return $this->semanasembarazo;
    }

    /**
     * set value for fechaprobableparto 
     *
     * type:date,size:13,default:null,nullable
     *
     * @param mixed $fechaprobableparto
     */
    public function setFechaprobableparto($fechaprobableparto) {
        $this->fechaprobableparto = $fechaprobableparto;
    }

    /**
     * get value for fechaprobableparto 
     *
     * type:date,size:13,default:null,nullable
     *
     * @return mixed
     */
    public function getFechaprobableparto() {
        return $this->fechaprobableparto;
    }

    /**
     * set value for fechaefectivaparto 
     *
     * type:date,size:13,default:null,nullable
     *
     * @param mixed $fechaefectivaparto
     */
    public function setFechaefectivaparto($fechaefectivaparto) {
        $this->fechaefectivaparto = $fechaefectivaparto;
    }

    /**
     * get value for fechaefectivaparto 
     *
     * type:date,size:13,default:null,nullable
     *
     * @return mixed
     */
    public function getFechaefectivaparto() {
        return $this->fechaefectivaparto;
    }

    /**
     * set value for activo 
     *
     * type:varchar,size:1,default:null,nullable
     *
     * @param mixed $activo
     */
    public function setActivo($activo) {
        $this->activo = $activo;
    }

    /**
     * get value for activo 
     *
     * type:varchar,size:1,default:null,nullable
     *
     * @return mixed
     */
    public function getActivo() {
        return $this->activo;
    }

    /**
     * set value for motivobaja 
     *
     * type:int2,size:5,default:null,nullable
     *
     * @param mixed $motivobaja
     */
    public function setMotivobaja($motivobaja) {
        $this->motivobaja = $motivobaja;
    }

    /**
     * get value for motivobaja 
     *
     * type:int2,size:5,default:null,nullable
     *
     * @return mixed
     */
    public function getMotivobaja() {
        return $this->motivobaja;
    }

    /**
     * set value for mensajebaja 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $mensajebaja
     */
    public function setMensajebaja($mensajebaja) {
        $this->mensajebaja = $mensajebaja;
    }

    /**
     * get value for mensajebaja 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getMensajebaja() {
        return $this->mensajebaja;
    }

    /**
     * set value for id_procesobajaautomatica 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @param mixed $idProcesobajaautomatica
     */
    public function setIdProcesobajaautomatica($idProcesobajaautomatica) {
        $this->idProcesobajaautomatica = $idProcesobajaautomatica;
    }

    /**
     * get value for id_procesobajaautomatica 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @return mixed
     */
    public function getIdProcesobajaautomatica() {
        return $this->idProcesobajaautomatica;
    }

    /**
     * set value for fechacarga 
     *
     * type:date,size:13,default:null,nullable
     *
     * @param mixed $fechacarga
     */
    public function setFechacarga($fechacarga) {
        $this->fechacarga = $fechacarga;
    }

    /**
     * get value for fechacarga 
     *
     * type:date,size:13,default:null,nullable
     *
     * @return mixed
     */
    public function getFechacarga() {
        return $this->fechacarga;
    }

    /**
     * set value for clavebenefprovocobaja 
     *
     * type:varchar,size:16,default:null,nullable
     *
     * @param mixed $clavebenefprovocobaja
     */
    public function setClavebenefprovocobaja($clavebenefprovocobaja) {
        $this->clavebenefprovocobaja = $clavebenefprovocobaja;
    }

    /**
     * get value for clavebenefprovocobaja 
     *
     * type:varchar,size:16,default:null,nullable
     *
     * @return mixed
     */
    public function getClavebenefprovocobaja() {
        return $this->clavebenefprovocobaja;
    }

    /**
     * set value for idpersona 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @param mixed $idpersona
     */
    public function setIdpersona($idpersona) {
        $this->idpersona = $idpersona;
    }

    /**
     * get value for idpersona 
     *
     * type:int4,size:10,default:null,nullable
     *
     * @return mixed
     */
    public function getIdpersona() {
        return $this->idpersona;
    }

    /**
     * set value for periodo 
     *
     * type:varchar,size:6,default:null,primary,unique
     *
     * @param mixed $periodo
     */
    public function setPeriodo($periodo) {
        $this->periodo = $periodo;
    }

    /**
     * get value for periodo 
     *
     * type:varchar,size:6,default:null,primary,unique
     *
     * @return mixed
     */
    public function getPeriodo() {
        return $this->periodo;
    }

    /**
     * set value for embarazoactual 
     *
     * type:bpchar,size:1,default:null,nullable
     *
     * @param mixed $embarazoactual
     */
    public function setEmbarazoactual($embarazoactual) {
        $this->embarazoactual = $embarazoactual;
    }

    /**
     * get value for embarazoactual 
     *
     * type:bpchar,size:1,default:null,nullable
     *
     * @return mixed
     */
    public function getEmbarazoactual() {
        return $this->embarazoactual;
    }

    /**
     * set value for fum 
     *
     * type:date,size:13,default:null,nullable
     *
     * @param mixed $fum
     */
    public function setFum($fum) {
        $this->fum = $fum;
    }

    /**
     * get value for fum 
     *
     * type:date,size:13,default:null,nullable
     *
     * @return mixed
     */
    public function getFum() {
        return $this->fum;
    }

    public static function getSQlSelectWhere($where) {

        $sql = "
			SELECT *
			  FROM nacer.smiafiliados
			  WHERE " . $where . "";

        return($sql);
    }

    public function Automata($where) {
        $sql = BeneficiariosSmi::getSqlSelectWhere($where);
        $result = sql($sql);
        $this->construirResult($result);
    }

    public static function buscarPorClaveBeneficiario($clave) {
        $where = "clavebeneficiario='$clave'";
        $sql = BeneficiarioSmi::getSqlSelectWhere($where);
        $result = sql($sql);
        if (!$result->EOF) {
            $beneficiario_aux = new BeneficiarioSmi();
            $beneficiario_aux->construirResult($result);
        } else {
            $beneficiario_aux = null;
        }
        return $beneficiario_aux;
    }

    private $grupo_etario;

    public function calcularGrupoEtareo($fecha_comprobante) {
        if (!$this->grupo_etario) {
            $dias_de_vida = GetCountDaysBetweenTwoDates($this->afifechanac, $fecha_comprobante);
            $edad = calcularEdad($this->afifechanac, $fecha_comprobante);
            $grupo['edad'] = floor($edad);
            if (($dias_de_vida >= 0) && ($dias_de_vida <= 28)) {
                $grupo['categoria'] = 'neo';
                $grupo['descripcion'] = 'Grupo NeoNatal';
            } elseif (($dias_de_vida > 28) && ($dias_de_vida <= 364)) {
                $grupo['categoria'] = 'cero_a_uno';
                $grupo['descripcion'] = 'Grupo Menor de 1 año';
            } elseif (($dias_de_vida > 364) && ($dias_de_vida <= 2189 )) {
                $grupo['categoria'] = 'uno_a_seis';
                $grupo['descripcion'] = 'Grupo de 1 a 5 años';
            } elseif (($dias_de_vida > 2189) && ($dias_de_vida <= 3649 )) {
                $grupo['categoria'] = 'seis_a_diez';
                $grupo['descripcion'] = 'Grupo de 6 a 9 años';
            } elseif (($dias_de_vida > 3649) && ($dias_de_vida <= 7299 )) {
                $grupo['categoria'] = 'diez_a_veinte';
                $grupo['descripcion'] = 'Grupo de 10 a 19 años';
            } elseif (($dias_de_vida > 7299) && ($dias_de_vida <= 23724 )) {
                $grupo['categoria'] = 'veinte_a_sesentaycuatro';
                $grupo['descripcion'] = 'Grupo de 20 a 64 años';
            } else {
                $grupo['categoria'] = 'veinte_a_sesentaycuatro';
                $grupo['descripcion'] = 'Grupo de 20 a 64 años';
            }
            $this->grupo_etario = $grupo;
        }
        return $this->grupo_etario;
    }

}

?>