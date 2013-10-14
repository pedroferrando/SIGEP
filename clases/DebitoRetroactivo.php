<?php

class DebitoRetroactivo{
    private $id;
    private $nroExp;
    private $cuie;
    private $idMotivo;
    private $idPrestacion;
    private $observaciones;
    private $identificacion;
    private $tipo;
    private $fechaRegistro;
    private $usuario;
    
    public function __construct($id,$nroExp,$cuie,$idMotivo,$idPrestacion,$observaciones,
                                $identificacion,$tipo,$fechaRegistro="",$usuario=""){
        global $_ses_user;
        $this->setId($id);
        $this->setNroExp($nroExp);
        $this->setCuie($cuie);
        $this->setIdMotivo($idMotivo);
        $this->setIdPrestacion($idPrestacion);
        $this->setObservaciones($observaciones);
        $this->setIdentificacion($identificacion);
        $this->setTipo($tipo);
        $this->setFechaRegistro($fechaRegistro) ;
        $this->setUsuario($_ses_user[id]);
        
    }
    
    public function getId(){
        return $this->id;
    }
    
    public function getNroExp(){
        return $this->nroExp;
    }
    
    public function getCuie(){
        return $this->cuie;
    }
    
    public function getIdPrestacion(){
        return $this->idPrestacion;
    }
    
    public function getIdMotivo(){
        return $this->idMotivo;
    }
    
    public function getObservaciones(){
        return $this->observaciones;
    }
    
    public function getIdentificacion(){
        return $this->identificacion;
    }
    
    public function getTipo(){
        return $this->tipo;
    }
    
    public function getFechaRegistro(){
        return $this->fechaRegistro;
    }
    
    public function getUsuario(){
        return $this->usuario;
    }
    
    public function setId($id){
        $this->id = $id;
    }
    
    public function setNroExp($nroExp){
        $this->nroExp = $nroExp;
    }
    
    public function setCuie($cuie){
        $this->cuie = $cuie;
    }
    
    public function setIdMotivo($idMotivo){
        $this->idMotivo = $idMotivo;
    }
    
    public function setIdPrestacion($idPrestacion){
        $this->idPrestacion = $idPrestacion;
    }
    
    public function setObservaciones($observaciones){
        $this->observaciones = $observaciones;
    }
    
    public function setIdentificacion($identificacion){
        $this->identificacion = $identificacion;
    }
    
    public function setTipo($tipo){
        $this->tipo = $tipo;
    }
    
    public function setFechaRegistro($fechaRegistro){
        $this->fechaRegistro = $fechaRegistro;
    }
    
    public function setUsuario($usuario){
        $this->usuario = $usuario;
    }
    
    public function delete(){
        $sql = "DELETE FROM facturacion.debito_retroactivo 
                WHERE id='".$this->getId()."'";
        if(sql($sql)){
            return true;
        }else{
            return false;
        }
    }
    
    public function save(){
        $sql = "INSERT INTO facturacion.debito_retroactivo(nro_exp,cuie,id_motivo,observaciones,
                                                           id_prestacion,identificacion,tipo,fecha,usuario) 
                VALUES('".$this->getNroExp()."','".$this->getCuie()."',".$this->getIdMotivo().", 
                       '".$this->getObservaciones()."',".$this->getIdPrestacion().",'".$this->getIdentificacion()."',
                       '".$this->getTipo()."',now(),".$this->getUsuario().") 
                RETURNING id";
        
        $res = sql($sql);
        if($res){
            $this->setId($res->fields['id']);
            return true;
        }else{
            return false;
        }
    }
    
}

class DebitoRetroactivoColeccion {
    
    public static function getMotivosAuditoria(){
        $sql = "SELECT * 
                FROM facturacion.motivos_auditoria 
                ORDER BY descripcion";
        $res = sql($sql);
        return $res;   
    }
    
    /* devuelve el detalle de los debitos retroactivos aplicados
     * a un determinado efector (nro de factura, nomenclador, 
     * precio debitado, etc) ... en un expediente puntual
    */
    public static function getPrestacionesDebitoRetroactivo($cuie,$nroExp=""){
        if($nroExp!=""){
            $where = " AND deb_ret.nro_exp='$nroExp' ";
        }
        $sql = "SELECT deb_ret.id, deb_ret.nro_exp, deb_ret.observaciones,
                       deb_ret.identificacion, mot.descripcion AS motivo,
                       pre.precio_prestacion, pre.cantidad, fac.nro_fact_offline, 
                       fac.nro_exp AS exp_prest, com.fecha_comprobante, 
                       nom.codigo, nom.diagnostico, ben.apellido_benef, 
                       ben.nombre_benef, ben.apellido_benef_otro, ben.nombre_benef_otro
                FROM facturacion.debito_retroactivo deb_ret
                JOIN facturacion.motivos_auditoria mot ON deb_ret.id_motivo=mot.id_motivo
                JOIN facturacion.prestacion pre ON deb_ret.id_prestacion=pre.id_prestacion
                JOIN facturacion.comprobante com ON pre.id_comprobante=com.id_comprobante
                JOIN facturacion.factura fac ON com.id_factura=fac.id_factura 
                JOIN facturacion.nomenclador nom ON pre.id_nomenclador=nom.id_nomenclador 
                JOIN uad.beneficiarios ben ON com.clavebeneficiario=ben.clave_beneficiario 
                WHERE deb_ret.cuie='$cuie' 
                ".$where."
                ORDER BY deb_ret.nro_exp DESC, fac.nro_fact_offline DESC, ben.apellido_benef ASC
                ";
        $res = sql($sql);
        return $res;        
    }
    
    /* devuelve el la cantidad de debitos retroactivos aplicados a un 
     * efector dado, y el monto total ... en un expediente puntual
    */
    public static function getResumenDebitoRetroactivo($cuie,$nroExp=""){
        if($nroExp!=""){
            $where = " AND deb_ret.nro_exp='$nroExp' ";
        }
        $sql = "SELECT COUNT(*) AS total, 
                       SUM(pre.precio_prestacion*pre.cantidad) AS monto
                FROM facturacion.debito_retroactivo deb_ret
                JOIN facturacion.prestacion pre ON deb_ret.id_prestacion=pre.id_prestacion
                WHERE deb_ret.cuie='$cuie' 
                ".$where."
                GROUP BY deb_ret.cuie
                ";
        //echo $sql;
        $res = sql($sql);
        return $res; 
    }
    
}
?>
