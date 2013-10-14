<?php 

/**
* Clase para Efectores
*/
class Efector
{
	#	Atributos
	var $cuie = "";
	var $nombre = "";
	var $sistema = "";
	var $domicilio = "";
	var $departamento = "";
	var $localidad = "";
	var $codPos = "";
	var $ciudad = "";
	var $referente = "";
	var $tel = "";
	var $tipo = "";
	var $codOrg = "";
	var $nivel = "";
	var $banco = "";
	var $nroCuenta = "";

	#------- Para mostrar ------------

	var $bancoNombre = "";


	function __construct()
	{
			#	Atributos
		$this->cuie = "";
		$this->nombre = "";
		$this->sistema = "";
		$this->domicilio = "";
		$this->departamento = "";
		$this->localidad = "";
		$this->codPos = "";
		$this->ciudad = "";
		$this->referente = "";
		$this->tel = "";
		$this->tipo = "";
		$this->codOrg = "";
		$this->nivel = "NULL";
		$this->banco = "";
		$this->nroCuenta = "";
		$this->bancoNombre = "";
	}


	#	Metodo contruirResult 		
	public function construirResult($result)
	{
		$this->cuie = $result->fields["cuie"];
		$this->nombre = $result->fields["nombreefector"];
		$this->sistema = $result->fields["sistema"];
		$this->domicilio = $result->fields["domicilio"];
		$this->departamento = $result->fields["departamento"];
		$this->localidad = $result->fields["localidad"];
		$this->codPos = $result->fields["cod_pos"];
		$this->ciudad = $result->fields["ciudad"];
		$this->referente = $result->fields["referente"];
		$this->tel = $result->fields["tel"];
		$this->tipo = $result->fields["tipoefector"];
		$this->codOrg = $result->fields["cod_org"];
		$this->nivel = $result->fields["nivel"];
		$this->banco = $result->fields["banco"];
		$this->nroCuenta = $result->fields["nrocta"];
		$this->bancoNombre = $result->fields["nombrebanco"];

		if (strlen($this->nivel)< 1) {
			$this->nivel = "NULL";
		}
	}


	#	------ Getters
	# Retorna el Atributo cuie
	public function getCuie(){
	  return($this->cuie);
	}

	# Retorna el Atributo nombre
	public function getNombre(){
	  return($this->nombre);
	}

	# Retorna el Atributo sistema
	public function getSistema(){
	  return($this->sistema);
	}

	# Retorna el Atributo domicilio
	public function getDomicilio(){
	  return($this->domicilio);
	}

	# Retorna el Atributo departamento
	public function getDepartamento(){
	  return($this->departamento);
	}

	# Retorna el Atributo localidad
	public function getLocalidad(){
	  return($this->localidad);
	}

	# Retorna el Atributo codPos
	public function getCodPos(){
	  return($this->codPos);
	}

	# Retorna el Atributo ciudad
	public function getCiudad(){
	  return($this->ciudad);
	}

	# Retorna el Atributo referente
	public function getReferente(){
	  return($this->referente);
	}

	# Retorna el Atributo tel
	public function getTelefono(){
	  return($this->tel);
	}

	# Retorna el Atributo tipo
	public function getTipo(){
	  return($this->tipo);
	}

	# Retorna el Atributo codOrg
	public function getCodOrg(){
	  return($this->codOrg);
	}

	# Retorna el Atributo nivel
	public function getNivel(){
	  return($this->nivel);
	}

	#	Metodo getPresentacion 		
	public function getPresentacion()
	{
		return($this->cuie.", ".$this->nombre);
	}	

	#	Metodo getBanco
	public function getBanco(){
		return($this->banco);
	}

	#	Metodo getnroCuenta
	public function getNroCuenta(){
		return($this->nroCuenta);
	}

	#	Metodo getNombreBanco 		
	public function getNombreBanco(){
		return($this->bancoNombre);
	}	


	# ----- Setter

	# Asigna el valor al atributo Cuie
	public function setCuie($cuie){
	  $this->cuie = $cuie;
	}

	# Asigna el valor al atributo Nombre
	public function setNombre($Nombre){
	  $this->nombre = $Nombre;
	}

	# Asigna el valor al atributo Sistema
	public function setSistema($Sistema){
	  $this->sistema = $Sistema;
	}

	# Asigna el valor al atributo Domicilio
	public function setDomicilio($Domicilio){
	  $this->domicilio = $Domicilio;
	}

	# Asigna el valor al atributo Departamento
	public function setDepartamento($Departamento){
	  $this->departamento = $Departamento;
	}

	# Asigna el valor al atributo Localidad
	public function setLocalidad($Localidad){
	  $this->localidad = $Localidad;
	}

	# Asigna el valor al atributo CodPos
	public function setCodPos($CodPos){
	  $this->codPos = $CodPos;
	}

	# Asigna el valor al atributo Ciudad
	public function setCiudad($Ciudad){
	  $this->ciudad = $Ciudad;
	}

	# Asigna el valor al atributo Referente
	public function setReferente($Referente){
	  $this->referente = $Referente;
	}

	# Asigna el valor al atributo Tel
	public function setTelefono($Tel){
	  $this->tel = $Tel;
	}

	# Asigna el valor al atributo Tipo
	public function setTipo($Tipo){
	  $this->tipo = $Tipo;
	}

	# Asigna el valor al atributo CodOrg
	public function setCodOrg($CodOrg){
	  $this->codOrg = $CodOrg;
	}

	# Asigna el valor al atributo Nivel
	public function setNivel($Nivel){
	  $this->nivel = $Nivel;
	}

	# Asigna el valor al atributo Banco
	public function setBanco($banco){
		$this->banco = $banco;
	}

	# Asigna el valor al atributo nroCuenta
	public function setNroCuenta($nroCuenta){
		$this->nroCuenta = $nroCuenta;
	}

	#	------ Sqls

	#	Metodo getSqlSelect 		
	public function getSqlSelect()
	{	
		$sql = "SELECT cuie, nombreefector, sistema, domicilio, departamento, localidad, 
					   cod_pos, ciudad, referente, tel, tipoefector, cod_org, nivel, banco, nrocta, bco.nombre as nombrebanco
					FROM facturacion.smiefectores
					left join general.bancos bco on bco.idbanco = banco
					WHERE cuie = '".$this->cuie."'";

		return($sql);
	}

	#	Metodo getSqlSelect 		
	public function getSqlSelectGenerico($param = "", $condicion = "")
	{
		if (strlen($param) > 1) { 
			$sql = "SELECT cuie, nombreefector, sistema, domicilio, departamento, localidad, 
					       cod_pos, ciudad, referente, tel, tipoefector, cod_org, nivel, banco, nrocta, bco.nombre as nombrebanco
					  FROM facturacion.smiefectores
					  left join general.bancos bco on bco.idbanco = banco
					  WHERE ".$condicion." ".$param."";
		}else{
			$sql = "SELECT cuie, nombreefector, sistema, domicilio, departamento, localidad, 
					   cod_pos, ciudad, referente, tel, tipoefector, cod_org, nivel, banco, nrocta
					FROM facturacion.smiefectores
					left join general.bancos bco on bco.idbanco = banco";
		}

		return($sql);
	}

	#	Metodo getSqlInsert 		
	public function getSqlInsert()
	{
		$sql = "INSERT INTO facturacion.smiefectores(
            cuie, nombreefector, sistema, domicilio, departamento, localidad, 
            cod_pos, ciudad, referente, tel, tipoefector, cod_org, nivel, banco, nrocta)
				VALUES ('".$this->cuie."', '".$this->nombre."', ".$this->sistema.", '".$this->domicilio."', '".$this->departamento."', 
					'".$this->localidad."','".$this->codPos."', '".$this->ciudad."', '".$this->referente."', '".$this->tel."', '".$this->tipo."',
					 '".$this->codOrg."', ".$this->nivel.", ".$this->banco.", '".$this->nroCuenta."')";
				    
		return($sql);
	}


	#	Metodo getSqlUpdate 		
	public function getSqlUpdate()
	{
		$sql = "UPDATE facturacion.smiefectores
			SET 
			  cuie='".$this->cuie."', nombreefector='".$this->nombre."', sistema=".$this->sistema.", domicilio='".$this->domicilio."', 
			  departamento='".$this->departamento."', localidad='".$this->localidad."', cod_pos='".$this->codPos."', ciudad='".$this->ciudad."', 
			  referente='".$this->referente."', tel='".$this->tel."', tipoefector='".$this->tipo."', 
			  cod_org='".$this->codOrg."', nivel=".$this->nivel.", banco=".$this->banco.", nrocta ='".$this->nroCuenta."'
			WHERE cuie = '".$this->cuie."'";

		return($sql);
	}

	#	Metodo getSqlDelete 		
	public function getSqlDelete()
	{
		$sql = "DELETE FROM facturacion.smiefectores
					WHERE cuie = '".$this->cuie."'";
		return($sql);
	}




}




 ?>