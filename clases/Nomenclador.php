<?php

/**
 * 
 *
 * @version 1.107
 * @package entity
 */
class Nomenclador {

    private $id_nomenclador;
    private $codigo;
    private $grupo;
    private $subgrupo;
    private $descripcion;
    private $precio;
    private $tipoNomenclador;
    private $idNomencladorDetalle;
    private $categoria;
    private $diagnostico;
    private $neo;
    private $ceroAUno;
    private $unoASeis;
    private $seisADiez;
    private $veinteASesentaycuatro;
    private $f;
    private $m;
    private $embarazada;
    private $diezAVeinte;

    public function construirResult($result) {
        $this->id_nomenclador = $result->fields['id_nomenclador'];
        $this->codigo = $result->fields['codigo'];
        $this->grupo = $result->fields['grupo'];
        $this->subgrupo = $result->fields['subgrupo'];
        $this->descripcion = $result->fields['descripcion'];
        $this->precio = $result->fields['precio'];
        $this->tipoNomenclador = $result->fields['tipo_nomenclador'];
        $this->idNomencladorDetalle = $result->fields['id_nomenclador_detalle'];
        $this->categoria = $result->fields['categoria'];
        $this->diagnostico = $result->fields['diagnostico'];
        $this->neo = $result->fields['neo'];
        $this->ceroAUno = $result->fields['cero_a_uno'];
        $this->unoASeis = $result->fields['uno_a_seis'];
        $this->seisADiez = $result->fields['seis_a_diez'];
        $this->veinteASesentaycuatro = $result->fields['veinte_a_sesentaycuatro'];
        $this->f = $result->fields['f'];
        $this->m = $result->fields['m'];
        $this->embarazada = $result->fields['embarazada'];
        $this->diezAVeinte = $result->fields['diez_a_veinte'];
    }

    /**
     * set value for id_nomenclador 
     *
     * type:serial,size:10,default:nextval('facturacion.nomenclador_id_nomenclador_seq'::regclass),primary,unique,autoincrement
     *
     * @param mixed $idNomenclador
     */
    public function setIdNomenclador($idNomenclador) {
        $this->id_nomenclador = $idNomenclador;
    }

    /**
     * get value for id_nomenclador 
     *
     * type:serial,size:10,default:nextval('facturacion.nomenclador_id_nomenclador_seq'::regclass),primary,unique,autoincrement
     *
     * @return mixed
     */
    public function getIdNomenclador() {
        return $this->id_nomenclador;
    }

    /**
     * set value for codigo 
     *
     * type:text,size:2147483647,default:null,index,nullable
     *
     * @param mixed $codigo
     */
    public function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

    /**
     * get value for codigo 
     *
     * type:text,size:2147483647,default:null,index,nullable
     *
     * @return mixed
     */
    public function getCodigo() {
        return $this->codigo;
    }

    /**
     * set value for grupo 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $grupo
     */
    public function setGrupo($grupo) {
        $this->grupo = $grupo;
    }

    /**
     * get value for grupo 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getGrupo() {
        return $this->grupo;
    }

    /**
     * set value for subgrupo 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @param mixed $subgrupo
     */
    public function setSubgrupo($subgrupo) {
        $this->subgrupo = $subgrupo;
    }

    /**
     * get value for subgrupo 
     *
     * type:text,size:2147483647,default:null,nullable
     *
     * @return mixed
     */
    public function getSubgrupo() {
        return $this->subgrupo;
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
     * set value for precio 
     *
     * type:numeric,size:30,default:null,nullable
     *
     * @param mixed $precio
     */
    public function setPrecio($precio) {
        $this->precio = $precio;
    }

    /**
     * get value for precio 
     *
     * type:numeric,size:30,default:null,nullable
     *
     * @return mixed
     */
    public function getPrecio() {
        return $this->precio;
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
     * set value for id_nomenclador_detalle 
     *
     * type:int4,size:10,default:null,index,nullable
     *
     * @param mixed $idNomencladorDetalle
     */
    public function setIdNomencladorDetalle($idNomencladorDetalle) {
        $this->idNomencladorDetalle = $idNomencladorDetalle;
    }

    /**
     * get value for id_nomenclador_detalle 
     *
     * type:int4,size:10,default:null,index,nullable
     *
     * @return mixed
     */
    public function getIdNomencladorDetalle() {
        return $this->idNomencladorDetalle;
    }

    /**
     * set value for categoria 
     *
     * type:int2,size:5,default:null,nullable
     *
     * @param mixed $categoria
     */
    public function setCategoria($categoria) {
        $this->categoria = $categoria;
    }

    /**
     * get value for categoria 
     *
     * type:int2,size:5,default:null,nullable
     *
     * @return mixed
     */
    public function getCategoria() {
        return $this->categoria;
    }

    /**
     * set value for diagnostico 
     *
     * type:text,size:2147483647,default:null,index,nullable
     *
     * @param mixed $diagnostico
     */
    public function setDiagnostico($diagnostico) {
        $this->diagnostico = $diagnostico;
    }

    public function getCodigoCategoria() {
        $categoria = split(" ", $this->codigo);
        return $categoria[0];
    }

    public function getCodigoTema() {
        $categoria = split(" ", $this->codigo);
        return $categoria[1];
    }

    public function getDiagnostico() {
        return $this->diagnostico;
    }

    public function getCodigoCompleto() {
        return $this->codigo . ' ' . $this->diagnostico;
    }

    /**
     * set value for neo 
     *
     * type:numeric,size:6,default:0
     *
     * @param mixed $neo
     */
    public function setNeo($neo) {
        $this->neo = $neo;
    }

    /**
     * get value for neo 
     *
     * type:numeric,size:6,default:0
     *
     * @return mixed
     */
    public function getNeo() {
        return $this->neo;
    }

    /**
     * set value for cero_a_uno 
     *
     * type:numeric,size:6,default:0
     *
     * @param mixed $ceroAUno
     */
    public function setCeroAUno($ceroAUno) {
        $this->ceroAUno = $ceroAUno;
    }

    /**
     * get value for cero_a_uno 
     *
     * type:numeric,size:6,default:0
     *
     * @return mixed
     */
    public function getCeroAUno() {
        return $this->ceroAUno;
    }

    /**
     * set value for uno_a_seis 
     *
     * type:numeric,size:6,default:0
     *
     * @param mixed $unoASeis
     */
    public function setUnoASeis($unoASeis) {
        $this->unoASeis = $unoASeis;
    }

    /**
     * get value for uno_a_seis 
     *
     * type:numeric,size:6,default:0
     *
     * @return mixed
     */
    public function getUnoASeis() {
        return $this->unoASeis;
    }

    /**
     * set value for seis_a_diez 
     *
     * type:numeric,size:6,default:0
     *
     * @param mixed $seisADiez
     */
    public function setSeisADiez($seisADiez) {
        $this->seisADiez = $seisADiez;
    }

    /**
     * get value for seis_a_diez 
     *
     * type:numeric,size:6,default:0
     *
     * @return mixed
     */
    public function getSeisADiez() {
        return $this->seisADiez;
    }

    /**
     * set value for veinte_a_sesentaycuatro 
     *
     * type:numeric,size:6,default:0
     *
     * @param mixed $veinteASesentaycuatro
     */
    public function setVeinteASesentaycuatro($veinteASesentaycuatro) {
        $this->veinteASesentaycuatro = $veinteASesentaycuatro;
    }

    /**
     * get value for veinte_a_sesentaycuatro 
     *
     * type:numeric,size:6,default:0
     *
     * @return mixed
     */
    public function getVeinteASesentaycuatro() {
        return $this->veinteASesentaycuatro;
    }

    /**
     * set value for f 
     *
     * type:bool,size:1,default:false
     *
     * @param mixed $f
     */
    public function setF($f) {
        $this->f = $f;
    }

    /**
     * get value for f 
     *
     * type:bool,size:1,default:false
     *
     * @return mixed
     */
    public function getF() {
        return $this->f;
    }

    /**
     * set value for m 
     *
     * type:bool,size:1,default:false
     *
     * @param mixed $m
     */
    public function setM($m) {
        $this->m = $m;
    }

    /**
     * get value for m 
     *
     * type:bool,size:1,default:false
     *
     * @return mixed
     */
    public function getM() {
        return $this->m;
    }

    /**
     * set value for embarazada 
     *
     * type:numeric,size:6,default:0
     *
     * @param mixed $embarazada
     */
    public function setEmbarazada($embarazada) {
        $this->embarazada = $embarazada;
    }

    /**
     * get value for embarazada 
     *
     * type:numeric,size:6,default:0
     *
     * @return mixed
     */
    public function getEmbarazada() {
        return $this->embarazada;
    }

    /**
     * set value for diez_a_veinte 
     *
     * type:numeric,size:6,default:0
     *
     * @param mixed $diezAVeinte
     */
    public function setDiezAVeinte($diezAVeinte) {
        $this->diezAVeinte = $diezAVeinte;
    }

    /**
     * get value for diez_a_veinte 
     *
     * type:numeric,size:6,default:0
     *
     * @return mixed
     */
    public function getDiezAVeinte() {
        return $this->diezAVeinte;
    }

    public function getArray() {
        return get_object_vars($this);
    }

    public function getPrecioSegunGrupo($grupo) {
        switch ($grupo) {
            case 'neo':
                $precio = $this->neo;
                break;
            case 'cero_a_uno':
                $precio = $this->ceroAUno;
                break;
            case 'uno_a_seis':
                $precio = $this->unoASeis;
                break;
            case 'seis_a_diez':
                $precio = $this->seisADiez;
                break;
            case 'diez_a_veinte':
                $precio = $this->diezAVeinte;
                break;
            case 'veinte_a_sesentaycuatro':
                $precio = $this->veinteASesentaycuatro;
                break;
            case 'embarazada':
                $precio = $this->embarazada;
                break;

            default:
                break;
        }
        return $precio;
    }

    public static function getSQlSelectWhere($where) {

        $sql = "
			SELECT *
			  FROM facturacion.nomenclador
			  WHERE " . $where . "";

        return($sql);
    }

    public static function getSQlSelect() {

        $sql = "SELECT * FROM facturacion.nomenclador";

        return($sql);
    }

    public static function buscarNomencladorPorId($id_nomenclador) {
        $where = "id_nomenclador='$id_nomenclador'";

        $result = sql(Nomenclador::getSQlSelectWhere($where));

        $nomenclador_aux = new Nomenclador();

        $nomenclador_aux->construirResult($result);

        return $nomenclador_aux;
    }

    public static function buscaPractica($categoria, $tema, $patologia, $id_nomenclador_detalle) {
        $codigo = $categoria . " " . $tema;
        $sql_diagnosticos = "SELECT *
                    FROM facturacion.nomenclador 
                    WHERE id_nomenclador_detalle='$id_nomenclador_detalle'               
                    AND codigo='$codigo'
                    AND diagnostico='$patologia'";
        $res_diagnosticos = sql($sql_diagnosticos) or fin_pagina();

        if (!$res_diagnosticos->EOF) {
            $nomenclador = new Nomenclador();
            $nomenclador->construirResult($res_diagnosticos);
        } else {
            $nomenclador = null;
        }
        return $nomenclador;
    }

    public static function practicaSoloParaEmbarazadas($id_nomenclador) {

        $sql = "SELECT * FROM facturacion.nomenclador where id_nomenclador='$id_nomenclador'
            and neo=0
            and cero_a_uno=0
            and uno_a_seis=0
            and seis_a_diez=0
            and diez_a_veinte=0
            and veinte_a_sesentaycuatro=0
            and embarazada > 0";
        $resultado = sql($sql);
        if (!$resultado->EOF) {
            return true;
        } else {
            return false;
        }
    }

    public static function practicaSoloParaUnGrupo($id_nomenclador) {

        $sql = "SELECT * FROM facturacion.nomenclador where id_nomenclador='$id_nomenclador'";
        $resultado = sql($sql);

        if (!$resultado->EOF) {

            $precios[1]['grupo'] = 'Grupo NeoNatal';
            $precios[1]['precio'] = $resultado->fields['neo'];
            $precios[2]['grupo'] = 'Grupo Menor de 1 año';
            $precios[2]['precio'] = $resultado->fields['cero_a_uno'];
            $precios[3]['grupo'] = 'Grupo de 1 a 5 años';
            $precios[3]['precio'] = $resultado->fields['uno_a_seis'];
            $precios[4]['grupo'] = 'Grupo de 6 a 9 años';
            $precios[4]['precio'] = $resultado->fields['seis_a_diez'];
            $precios[5]['grupo'] = 'Grupo de 10 a 19 años';
            $precios[5]['precio'] = $resultado->fields['diez_a_veinte'];
            $precios[6]['grupo'] = 'Grupo de 20 a 64 años';
            $precios[6]['precio'] = $resultado->fields['veinte_a_sesentaycuatro'];
            $precios[7]['grupo'] = 'Embarazadas';
            $precios[7]['precio'] = $resultado->fields['embarazada'];

            $preciounico = 0;
            $i = 1;

            while ($i < 8) {
                if ($precios[$i]['precio'] > 0) {
                    if ($preciounico == 0) {
                        $preciounico = $precios[$i];
                    } else {
                        $preciounico = false;
                        break;
                    }
                }
                $i++;
            }
        }
        return $preciounico;
    }

    public static function getNomencladoresNotIn($id) {
        $where = "SELECT DISTINCT on (codigo ||' '|| diagnostico) codigo ||' '|| diagnostico, * FROM facturacion.nomenclador
                order by codigo ||' '|| diagnostico where id_nomenclador_detalle = '$id'";

        $coleccion = Nomenclador::getNomencladores($where);
        return $coleccion;
    }

    public static function getNomencladores($where = '') {
        if ($where == '') {
            $sql = "SELECT DISTINCT on (codigo ||' '|| diagnostico) codigo ||' '|| diagnostico, * FROM facturacion.nomenclador
                order by codigo ||' '|| diagnostico";
            $result = sql($sql);
        } else {
            $sql = "SELECT DISTINCT on (codigo ||' '|| diagnostico) codigo ||' '|| diagnostico, * FROM facturacion.nomenclador
                    WHERE $where    
                    ORDER BY codigo ||' '|| diagnostico";
            $result = sql($sql);
        }

        $coleccionregistros = array();

        while (!$result->EOF) {

            $registro = new Nomenclador();
            $registro->construirResult($result);

            $coleccionregistros[] = $registro;
            $result->MoveNext();
        }

        return ($coleccionregistros);
    }

}

?>