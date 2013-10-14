<?php

class ReporteCEB{
    //atributos
    
    //constructor
    function __construct()
    {

    }
    
    //comportamientos
    public function getSQLCountBeneficiariosCEB($cuie,$solapa,$activo){
        $sql  = "SELECT COUNT(*) AS total 
                 FROM ( ";
        $sql .= $this->getSQLBeneficiariosCEB($cuie,$solapa,$activo);
        $sql .= " ) ben_ceb ";
        return $sql;
    }
    
    public function getSQLBeneficiariosCEB($cuie,$solapa,$activo,$limit=99999,$offset=0){
        $cuie = strtoupper($cuie);
        $where .= " AND ".$this->getCondicionCEB($solapa,"afi");
        if($activo!=""){
            $where .= " AND afi.activo='$activo' ";
        }
        $limit = " LIMIT $limit OFFSET $offset ";
        $sql = "SELECT afi.clavebeneficiario, afi.afidni,
                       afi.afiapellido, afi.afinombre, afi.afitipodoc, afi.afidni, 
                       afi.afifechanac, afi.fechainscripcion, afi.activo, afi.mensajebaja, 
                       afi.afidomcalle, afi.afidomnro, afi.afidompiso, afi.afidomdepto, afi.afidombarrioparaje,
                       afi.fechaultimaprestacion, afi.codigoprestacion, 
                       afi.cuie, efe.nombreefector AS lugar_prestacion
                FROM nacer.smiafiliados afi 
                LEFT JOIN facturacion.smiefectores efe ON afi.cuie=efe.cuie
                WHERE afi.cuielugaratencionhabitual='$cuie' 
                ".$where." 
                ORDER BY afi.activo DESC, afi.afiapellido ASC
                ".$limit."
               ";
        return $sql;
    }
    
    public function getSQLEstadoCEBBenficiario($campo,$valor){
        if($campo=="afidni"){
            $where .= " AND afi.aficlasedoc='P' ";
        }
        $sql = "SELECT CASE
                       WHEN ".$this->getCondicionCEB("rojo","afi")." THEN 'sin cobertura' 
                       ELSE 
                            CASE
                            WHEN ".$this->getCondicionCEB("amarillo","afi")." 
                            THEN 'con cobertura prox a vencer'
                            ELSE 'con cobertura'
                            END
                        END AS estado_cobertura 
                FROM nacer.smiafiliados afi
                WHERE afi.".$campo."='".$valor."' 
                    ".$where."
                ";
        return $sql;
    }
    
    
    public function getSQLTotalesBeneficiariosCEB($cuie,$activo){
        $cuie = strtoupper($cuie);
        if($activo!=""){
            $where .= " AND afi.activo='$activo' ";
        }
        $sql = "select  SUM(case 
                            when ".$this->getCondicionCEB("rojo","afi")." then 1 else 0 
                            end
                           )as rojo,
                        SUM(case 
                            when ".$this->getCondicionCEB("amarillo","afi")." 
                            then 1 else 0 
                            end
                            ) as amarillo,
                        SUM(case 
                            when ".$this->getCondicionCEB("verde","afi")."
                            then 1 else 0 
                            end
                           )as verde

                from nacer.smiafiliados afi
                where afi.cuielugaratencionhabitual='$cuie' 
                    ".$where."
                ";
        return $sql;
    }
    
    public function getCondicionCEB($solapa,$aliasTbl="afi"){
        $condicion = "";
        switch($solapa){
            case "rojo":
                $condicion = " ".$aliasTbl.".ceb='N' ";
                break;
            case "amarillo":
                $condicion = " ".$aliasTbl.".ceb='S' and 
                               (
                                (".$aliasTbl.".fechaultimaprestacion is not null and ".$aliasTbl.".fechaultimaprestacion + interval '12 month' BETWEEN now() AND now()+interval '2 month') 
                                or 
                                (".$aliasTbl.".fechaultimaprestacion is null and ".$aliasTbl.".fechainscripcion + interval '8 month' BETWEEN now() AND now()+interval '2 month')
                               ) 
                            ";
                break;
            case "verde":
                $condicion = " ".$aliasTbl.".ceb='S' and 
                               (
                                (".$aliasTbl.".fechaultimaprestacion is not null and ".$aliasTbl.".fechaultimaprestacion + interval '12 month' NOT BETWEEN now() AND now()+interval '2 month') 
                                or 
                                (".$aliasTbl.".fechaultimaprestacion is null and ".$aliasTbl.".fechainscripcion + interval '8 month' NOT BETWEEN now() AND now()+interval '2 month')
                               ) 
                            ";
                break;
        }
        return $condicion;
    }
    
    public function getCampoTotalEstadistico($solapa,$arr_grupo_poblacional=null){
        $estado = array("S","N");
        foreach($estado as $e){
            $campo = $e=="S" ? $solapa."_activo" : $solapa."_inactivo";
            $case .= " SUM(case 
                            when afi.activo='$e' and ".$this->getCondicionCEB($solapa,"afi")."
                            then 1 else 0 
                            end
                          )as $campo,";
            foreach($arr_grupo_poblacional as $v){
                $campof = $v!=" " ? $campo."_".strtolower($v) : $campo."_"."vacio";
                $case .= " SUM(case 
                                when afi.activo='$e' and ".$this->getCondicionCEB($solapa,"afi")." and afi.grupopoblacional='".strtoupper($v)."' 
                                then 1 else 0 
                                end
                              )as $campof,";
            }
        }
        
        return $case;
    }
    
    public function getSQLTotalesEstadisticosBeneficiariosCEB($cuie){
        $cuie = strtoupper($cuie);
        $arr = array("A","B","C","D"," ");
        $sql = "select ".$this->getCampoTotalEstadistico('rojo', $arr)."
                       ".$this->getCampoTotalEstadistico('amarillo', $arr)."
                       ".substr($this->getCampoTotalEstadistico('verde', $arr), 0, -1)."
                from nacer.smiafiliados afi
                where afi.cuielugaratencionhabitual='$cuie' ";
        return $sql;
    }   
    
}

?>
