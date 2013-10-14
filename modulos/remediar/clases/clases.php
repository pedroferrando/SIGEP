<?php 

#	Configuracion del sistema
require_once ("../../config.php");

# Clases remediar

/**
* 
*/
class RelacionCodigos

{
	var $id_tabla = '';
	var $codigosisa = '';
	var $cuie = '';
	var $codremediar = '';
	var $codestadistica = '';
	var $estado = '';


	function __construct()
	{
		
	}

	### Constructores
	
	public function construirResult($result){
		
		$this->id_tabla = $result->fields['id_tabla'];
		$this->codigosisa = $result->fields['codigosisa'];
		$this->cuie = $result->fields['cuie'];
		$this->codremediar = $result->fields['codremediar'];
		$this->codestadistica = $result->fields['codestadistica'];
		$this->estado = $result->fields['estado'];
	}


	### GETTERS
	
	# Documentacion para el metodo setId_tabla 		
	public function setId_tabla($id_tabla)
	{
		$this->id_tabla = $id_tabla;
	}
	# Documentacion para el metodo setCodigosisa 		
	public function setCodigosisa($codigosisa)
	{
		$this->codigosisa = $codigosisa;
	}
	# Documentacion para el metodo setCuie 		
	public function setCuie($cuie)
	{
		$this->cuie = $cuie;
	}
	# Documentacion para el metodo setCodremediar 		
	public function setCodremediar($codremediar)
	{
		$this->codremediar = $codremediar;
	}
	# Documentacion para el metodo setCodestadistica 		
	public function setCodestadistica($codestadistica)
	{
		$this->codestadistica = $codestadistica;
	}
	# Documentacion para el metodo setEstado 		
	public function setEstado($estado)
	{
		$this->estado = $estado;
	}



	### SETTERS
	
	# Documentacion para el metodo getId_tabla 		
	public function getId_tabla()
	{
		return($this->id_tabla);
	}
	# Documentacion para el metodo getCodigosisa 		
	public function getCodigosisa()
	{
		return($this->codigosisa);
	}
	# Documentacion para el metodo getCuie 		
	public function getCuie()
	{
		return($this->cuie);
	}
	# Documentacion para el metodo getCodremediar 		
	public function getCodremediar()
	{
		return($this->codremediar);
	}
	# Documentacion para el metodo getCodestadistica 		
	public function getCodestadistica()
	{
		return($this->codestadistica);
	}
	# Documentacion para el metodo getEstado 		
	public function getEstado()
	{
		return($this->estado);
	}



	### SQLS
	
	# Documentacion para metodo getSQlInsert
	public function getSQlInsert(){
		$sql = '';
		return($sql);
	}

	# Documentacion para metodo getSQlSelect
	public function getSQlSelect($where = ""){
		if (strlen($where) > 1) {
			$sql = 'SELECT id_tabla, codigosisa, cuie, codremediar, codestadistica, estado
						FROM general.relacioncodigos
						WHERE '.$where.'';
		} else {
			$sql = 'SELECT id_tabla, codigosisa, cuie, codremediar, codestadistica, estado
						FROM general.relacioncodigos';
		}
		
		return($sql);
	}

	# Documentacion para metodo getSQlUpdate
	public function getSQlUpdate(){
		$sql = '';
		return($sql);
	}

	# Documentacion para metodo getSQlDelete
	public function getSQlDelete(){
		$sql = '';
		return($sql);
	}

	#	Metodo CUIESumar 		
	public function ConvertirCUIE($cuie)
	{
		$result = sql($this->getSqlSelect(("cuie = '".$cuie."'")));
		$this->construirResult($result);
	}
		

}


/**
* 
*/
class Empadronamiento
{
	var $id_r_x_b;
	var $nroformulario;
	var $fechaempadronamiento;
	var $clavebeneficiario;
	var $usuario_carga;
	var $fecha_carga;
	var $enviado;


	var $formulario;

	var $relacionCodigos;

	var $padronNombre = "Empadronamiento Remediar+Redes";



	function __construct()
	{
		$this->id_r_x_b = "";
		$this->nroformulario = "";
		$this->fechaempadronamiento = "";
		$this->clavebeneficiario = "";
		$this->usuario_carga = "";
		$this->fecha_carga = "";
		$this->enviado = "n";

		$this->relacionCodigos = new RelacionCodigos();
		$this->formulario = new Formulario();
		$this->efector = new Efector();
	}

	#	Metodo construirResult 		
	public function construirResult($result)
	{
		$this->id_r_x_b = $result->fields['id_r_x_b'];
		$this->nroformulario = $result->fields['nroformulario'];
		$this->fechaempadronamiento = $result->fields['fechaempadronamiento'];
		$this->clavebeneficiario = $result->fields['clavebeneficiario'];
		$this->usuario_carga = $result->fields['usuario_carga'];
		$this->fecha_carga = $result->fields['fecha_carga'];
		$this->enviado = $result->fields['enviado'];

		if ($this->enPadron()) {
			$resultFormulario = sql($this->formulario->getSqlSelect(("nroformulario = ".$this->nroformulario."")));
			$this->formulario->construirResult($resultFormulario);
			$this->relacionCodigos = new RelacionCodigos();
			$this->relacionCodigos->ConvertirCUIE($this->formularioGetCentro_inscriptor());
			$this->efector->FiltrarCuie($this->formularioGetCentro_inscriptor());
		}
	}


	# Documentacion para metodo setId_r_x_b($id_r_x_b)
	public function setId_r_x_b($id_r_x_b){
		$this->id_r_x_b = $id_r_x_b;
	}
	# Documentacion para metodo setNroformulario($nroformulario)
	public function setNroformulario($nroformulario){
		$this->nroformulario = $nroformulario;
	}
	# Documentacion para metodo setFechaempadronamiento($fechaempadronamiento)
	public function setFechaempadronamiento($fechaempadronamiento){
		$this->fechaempadronamiento = $fechaempadronamiento;
	}
	# Documentacion para metodo setClavebeneficiario($clavebeneficiario)
	public function setClavebeneficiario($clavebeneficiario){
		$this->clavebeneficiario = $clavebeneficiario;
	}
	# Documentacion para metodo setUsuario_carga($usuario_carga)
	public function setUsuario_carga($usuario_carga){
		$this->usuario_carga = $usuario_carga;
	}
	# Documentacion para metodo setFecha_carga($fecha_carga)
	public function setFecha_carga($fecha_carga){
		$this->fecha_carga = $fecha_carga;
	}
	# Documentacion para metodo setEnviado($enviado)
	public function setEnviado($enviado){
		$this->enviado = $enviado;
	}

	# Documentacion para metodo getId_r_x_b()
	public function getId_r_x_b(){
		return($this->id_r_x_b);
	}
	# Documentacion para metodo getNroformulario()
	public function getNroformulario(){
		return($this->nroformulario);
	}
	# Documentacion para metodo getFechaempadronamiento()
	public function getFechaempadronamiento(){
		return($this->fechaempadronamiento);
	}
	# Documentacion para metodo getClavebeneficiario()
	public function getClavebeneficiario(){
		return($this->clavebeneficiario);
	}
	# Documentacion para metodo getUsuario_carga()
	public function getUsuario_carga(){
		return($this->usuario_carga);
	}
	# Documentacion para metodo getFecha_carga()
	public function getFecha_carga(){
		return($this->fecha_carga);
	}
	# Documentacion para metodo getEnviado()
	public function getEnviado(){
		return($this->enviado);
	}



	#################################################
	# Metodos de inclusion del formulario ###



	# Documentacion para el metodo getId_formulario 		
	public function formularioGetId_formulario()
	{
		return($this->formulario->getId_formulario());
	}
	# Documentacion para el metodo getNroformulario 		
	public function formularioGetNroformulario()
	{
		return($this->formulario->getNroformulario());
	}
	# Documentacion para el metodo getFactores_riesgo 		
	public function formularioGetFactores_riesgo()
	{
		return($this->formulario->getFactores_riesgo());
	}
	# Documentacion para el metodo getHta2 		
	public function formularioGetHta2()
	{
		return($this->formulario->getHta2());
	}
	# Documentacion para el metodo getHta3 		
	public function formularioGetHta3()
	{
		return($this->formulario->getHta3());
	}
	# Documentacion para el metodo getColesterol4 		
	public function formularioGetColesterol4()
	{
		return($this->formulario->getColesterol4());
	}
	# Documentacion para el metodo getColesterol5 		
	public function formularioGetColesterol5()
	{
		return($this->formulario->getColesterol5());
	}
	# Documentacion para el metodo getDmt26 		
	public function formularioGetDmt26()
	{
		return($this->formulario->getDmt26());
	}
	# Documentacion para el metodo getDmt27 		
	public function formularioGetDmt27()
	{
		return($this->formulario->getDmt27());
	}
	# Documentacion para el metodo getEcv8 		
	public function formularioGetEcv8()
	{
		return($this->formulario->getEcv8());
	}
	# Documentacion para el metodo getTabaco9 		
	public function formularioGetTabaco9()
	{
		return($this->formulario->getTabaco9());
	}
	# Documentacion para el metodo getPuntaje_final 		
	public function formularioGetPuntaje_final()
	{
		return($this->formulario->getPuntaje_final());
	}
	# Documentacion para el metodo getApellidoagente 		
	public function formularioGetApellidoagente()
	{
		return($this->formulario->getApellidoagente());
	}
	# Documentacion para el metodo getNombreagente 		
	public function formularioGetNombreagente()
	{
		return($this->formulario->getNombreagente());
	}
	# Documentacion para el metodo getCentro_inscriptor 		
	public function formularioGetCentro_inscriptor()
	{
		return($this->formulario->getCentro_inscriptor());
	}
	# Documentacion para el metodo getOs 		
	public function formularioGetOs()
	{
		return($this->formulario->getOs());
	}
	# Documentacion para el metodo getDni_agente 		
	public function formularioGetDni_agente()
	{
		return($this->formulario->getDni_agente());
	}
	# Documentacion para el metodo getCual_os 		
	public function formularioGetCual_os()
	{
		return($this->formulario->getCual_os());
	}

	#	Metodo formularioGetAgente 		
	public function formularioGetAgente()
	{
		return($this->formularioGetApellidoagente().", ".$this->formularioGetNombreagente());
	}


	#################################################

	# Relacion Codigos

	# Documentacion para el metodo getId_tabla 		
	public function relacionCodigosGetId_tabla()
	{
		return($this->relacionCodigos->getId_tabla());
	}
	# Documentacion para el metodo getCodigosisa 		
	public function relacionCodigosGetCodigosisa()
	{
		return($this->relacionCodigos->getCodigosisa());
	}
	# Documentacion para el metodo getCuie 		
	public function relacionCodigosGetCuie()
	{
		return($this->relacionCodigos->getCuie());
	}
	# Documentacion para el metodo getCodremediar 		
	public function relacionCodigosGetCodremediar()
	{
		return($this->relacionCodigos->getCodremediar());
	}
	# Documentacion para el metodo getCodestadistica 		
	public function relacionCodigosGetCodestadistica()
	{
		return($this->relacionCodigos->getCodestadistica());
	}
	# Documentacion para el metodo getEstado 		
	public function relacionCodigosGetEstado()
	{
		return($this->relacionCodigos->getEstado());
	}

	#################################################
	# Efector

	# Documentacion para efectorGetCuie()
	public function efectorGetCuie(){
		return($this->efector->getCuie());
	}
	# Documentacion para efectorGetNombreefector()
	public function efectorGetNombreefector(){
		return($this->efector->getNombreefector());
	}
	# Documentacion para efectorGetSistema()
	public function efectorGetSistema(){
		return($this->efector->getSistema());
	}
	# Documentacion para efectorGetDomicilio()
	public function efectorGetDomicilio(){
		return($this->efector->getDomicilio());
	}
	# Documentacion para efectorGetDepartamento()
	public function efectorGetDepartamento(){
		return($this->efector->getDepartamento());
	}
	# Documentacion para efectorGetLocalidad()
	public function efectorGetLocalidad(){
		return($this->efector->getLocalidad());
	}
	# Documentacion para efectorGetCod_pos()
	public function efectorGetCod_pos(){
		return($this->efector->getCod_pos());
	}
	# Documentacion para efectorGetCiudad()
	public function efectorGetCiudad(){
		return($this->efector->getCiudad());
	}
	# Documentacion para efectorGetReferente()
	public function efectorGetReferente(){
		return($this->efector->getReferente());
	}
	# Documentacion para efectorGetTel()
	public function efectorGetTel(){
		return($this->efector->getTel());
	}
	# Documentacion para efectorGetTipoefector()
	public function efectorGetTipoefector(){
		return($this->efector->getTipoefector());
	}
	# Documentacion para efectorGetCod_org()
	public function efectorGetCod_org(){
		return($this->efector->getCod_org());
	}
	# Documentacion para efectorGetNivel()
	public function efectorGetNivel(){
		return($this->efector->getNivel());
	}
	# Documentacion para efectorGetBanco()
	public function efectorGetBanco(){
		return($this->efector->getBanco());
	}
	# Documentacion para efectorGetNrocta()
	public function efectorGetNrocta(){
		return($this->efector->getNrocta());
	}




	#################################################
	# Metodos genericos para listado
	#	Metodo getEfector 		
	public function getEfector()
	{
		$this->relacionCodigos->ConvertirCUIE($this->formularioGetCentro_inscriptor());
		return($this->relacionCodigos->getCodremediar());
	}

	#	Metodo getNombrePadron 		
	public function getNombrePadron()
	{
		return($this->padronNombre);
	}

	#	Metodo enPadron 		
	public function enPadron()
	{
		if (strlen($this->clavebeneficiario) > 0) {
			$sql = $this->getSqlSelect("clavebeneficiario = '".$this->clavebeneficiario."'");
			$result = sql($sql);

			if ($result->RecordCount() != 0) {
				$value = True;
			} else {
				$value = False;
			}
		} else {
			$value = False;
		}
		
		
		return($value);
	}



	#	Metodo esFechavalidapadronamie	
	public function validaFechaempadronamiento()
	{
		if (strlen($this->fechaempadronamiento) > 9) {
			$value = True;
		} else {
			$value = False;
		}
		

		return($value);
	}

	#	Metodo validaEnviado 		
	public function validaEnviado()
	{
		if (($this->getEnviado() == "n") || ($this->getEnviado() == "N")) {
			$value = False;
		} else {
			$value = True;
		}
		
		return($value);
	}


	# SQLS
	#	Metodo getSqlSelect 		
	public function getSqlSelect($where = "")
	{
		if (strlen($where) < 1) {
			$sql = "SELECT id_r_x_b, nroformulario, fechaempadronamiento, clavebeneficiario, 
							usuario_carga, fecha_carga, enviado
						FROM uad.remediar_x_beneficiario";
		} else {
			$sql = "SELECT id_r_x_b, nroformulario, fechaempadronamiento, clavebeneficiario, 
							usuario_carga, fecha_carga, enviado
						FROM uad.remediar_x_beneficiario
						WHERE ".$where."";
		}
		

		return($sql);
	}	



	#	Metodo getSQlInsert 		
	public function getSQlInsert()
	{
		$sql = "
			INSERT INTO uad.remediar_x_beneficiario(
			           nroformulario, fechaempadronamiento, clavebeneficiario, 
			            usuario_carga, fecha_carga, enviado)
			    VALUES (".$this->nroformulario.", '".$this->fechaempadronamiento."', '".$this->clavebeneficiario."', 
			            '".$this->usuario_carga."', '".$this->fecha_carga."', '".$this->enviado."');
		";
		return($sql);
	}

	#	Metodo Automata 		
	public function Automata($where)
	{
		$sql = $this->getSqlSelect($where);
		$this->construirResult(sql($sql));

	}


	#	Metodo Insertar 		
	public function Insertar()
	{
		$sql = $this->getSQlInsert();
		$result = sql($sql);
	}


	#	Metodo getSQlUpdate 		
	public function getSQlUpdate()
	{


		$sql = "
			UPDATE uad.remediar_x_beneficiario
			   SET nroformulario=".$this->nroformulario.", fechaempadronamiento='".$this->fechaempadronamiento."', clavebeneficiario='".$this->clavebeneficiario."', 
			       usuario_carga=".$this->usuario_carga.", fecha_carga='".$this->fecha_carga."', enviado='".$this->enviado."'
			 WHERE id_r_x_b = ".$this->id_r_x_b.";
		";

		return($sql);
	}


	#	Metodo getSQlUpdate 		
	public function getSQlUpdateBeneficiario()
	{

		$sql = "
			UPDATE uad.remediar_x_beneficiario
			   SET nroformulario=".$this->nroformulario.", fechaempadronamiento='".$this->fechaempadronamiento."', clavebeneficiario='".$this->clavebeneficiario."', 
			       usuario_carga=".$this->usuario_carga.", fecha_carga='".$this->fecha_carga."', enviado='".$this->enviado."'
			 WHERE clavebeneficiario = '".$this->clavebeneficiario."';
		";

		return($sql);
	}


	#	Metodo ActualizarCBenef 		
	public function ActualizarCBenef()
	{
		$sql = $this->getSQlUpdateBeneficiario();
		$result = sql($sql);
	}


	#	Metodo Actualizar 		
	public function Actualizar()
	{
		$sql = $this->getSQlUpdate();
		$result = sql($sql);
	}

}



/**
* 
*/
class Formulario

{
	var $id_formulario = '';
	var $nroformulario = '';
	var $factores_riesgo = '';
	var $hta2 = '';
	var $hta3 = '';
	var $colesterol4 = '';
	var $colesterol5 = '';
	var $dmt26 = '';
	var $dmt27 = '';
	var $ecv8 = '';
	var $tabaco9 = '';
	var $puntaje_final = '';
	var $apellidoagente = '';
	var $nombreagente = '';
	var $centro_inscriptor = '';
	var $os = 'NINGUNA';
	var $dni_agente = '';
	var $cual_os = 'NINGUNA';


	function __construct()
	{
		
	}

	### Constructores
	
	public function construirResult($result){
		
		$this->id_formulario = $result->fields['id_formulario'];
		$this->nroformulario = $result->fields['nroformulario'];
		$this->factores_riesgo = $result->fields['factores_riesgo'];
		$this->hta2 = $result->fields['hta2'];
		$this->hta3 = $result->fields['hta3'];
		$this->colesterol4 = $result->fields['colesterol4'];
		$this->colesterol5 = $result->fields['colesterol5'];
		$this->dmt26 = $result->fields['dmt26'];
		$this->dmt27 = $result->fields['dmt27'];
		$this->ecv8 = $result->fields['ecv8'];
		$this->tabaco9 = $result->fields['tabaco9'];
		$this->puntaje_final = $result->fields['puntaje_final'];
		$this->apellidoagente = $result->fields['apellidoagente'];
		$this->nombreagente = $result->fields['nombreagente'];
		$this->centro_inscriptor = $result->fields['centro_inscriptor'];
		$this->os = $result->fields['os'];
		$this->dni_agente = $result->fields['dni_agente'];
		$this->cual_os = $result->fields['cual_os'];
	}



	#	Metodo Validar 		
	public function Validar()
	{
		
		# Validaciones para $this->id_formulario
		if(($this->id_formulario == -1) || (strlen($this->id_formulario)< 1)){
			$this->id_formulario = 0;
		}
		
		# Validaciones para $this->nroformulario
		if(($this->nroformulario == -1) || (strlen($this->nroformulario)< 1)){
			$this->nroformulario = 0;
		}
		
		# Validaciones para $this->factores_riesgo
		if(($this->factores_riesgo == -1) || (strlen($this->factores_riesgo)< 1)){
			$this->factores_riesgo = 0;
		}
		
		# Validaciones para $this->hta2
		if(($this->hta2 == -1) || (strlen($this->hta2)< 1)){
			$this->hta2 = 0;
		}
		
		# Validaciones para $this->hta3
		if(($this->hta3 == -1) || (strlen($this->hta3)< 1)){
			$this->hta3 = 0;
		}
		
		# Validaciones para $this->colesterol4
		if(($this->colesterol4 == -1) || (strlen($this->colesterol4)< 1)){
			$this->colesterol4 = 0;
		}
		
		# Validaciones para $this->colesterol5
		if(($this->colesterol5 == -1) || (strlen($this->colesterol5)< 1)){
			$this->colesterol5 = 0;
		}
		
		# Validaciones para $this->dmt26
		if(($this->dmt26 == -1) || (strlen($this->dmt26)< 1)){
			$this->dmt26 = 0;
		}
		
		# Validaciones para $this->dmt27
		if(($this->dmt27 == -1) || (strlen($this->dmt27)< 1)){
			$this->dmt27 = 0;
		}
		
		# Validaciones para $this->ecv8
		if(($this->ecv8 == -1) || (strlen($this->ecv8)< 1)){
			$this->ecv8 = 0;
		}
		
		# Validaciones para $this->tabaco9
		if(($this->tabaco9 == -1) || (strlen($this->tabaco9)< 1)){
			$this->tabaco9 = 0;
		}
		
		# Validaciones para $this->puntaje_final
		if(($this->puntaje_final == -1) || (strlen($this->puntaje_final)< 1)){
			$this->puntaje_final = 0;
		}
		
	}

	### GETTERS
	
	# Documentacion para el metodo setId_formulario 		
	public function setId_formulario($id_formulario)
	{
		$this->id_formulario = $id_formulario;
	}
	# Documentacion para el metodo setNroformulario 		
	public function setNroformulario($nroformulario)
	{
		$this->nroformulario = $nroformulario;
	}
	# Documentacion para el metodo setFactores_riesgo 		
	public function setFactores_riesgo($factores_riesgo)
	{
		$this->factores_riesgo = $factores_riesgo;
	}
	# Documentacion para el metodo setHta2 		
	public function setHta2($hta2)
	{
		$this->hta2 = $hta2;
	}
	# Documentacion para el metodo setHta3 		
	public function setHta3($hta3)
	{
		$this->hta3 = $hta3;
	}
	# Documentacion para el metodo setColesterol4 		
	public function setColesterol4($colesterol4)
	{
		$this->colesterol4 = $colesterol4;
	}
	# Documentacion para el metodo setColesterol5 		
	public function setColesterol5($colesterol5)
	{
		$this->colesterol5 = $colesterol5;
	}
	# Documentacion para el metodo setDmt26 		
	public function setDmt26($dmt26)
	{
		$this->dmt26 = $dmt26;
	}
	# Documentacion para el metodo setDmt27 		
	public function setDmt27($dmt27)
	{
		$this->dmt27 = $dmt27;
	}
	# Documentacion para el metodo setEcv8 		
	public function setEcv8($ecv8)
	{
		$this->ecv8 = $ecv8;
	}
	# Documentacion para el metodo setTabaco9 		
	public function setTabaco9($tabaco9)
	{
		$this->tabaco9 = $tabaco9;
	}
	# Documentacion para el metodo setPuntaje_final 		
	public function setPuntaje_final($puntaje_final)
	{
		$this->puntaje_final = $puntaje_final;
	}
	# Documentacion para el metodo setApellidoagente 		
	public function setApellidoagente($apellidoagente)
	{
		$this->apellidoagente = $apellidoagente;
	}
	# Documentacion para el metodo setNombreagente 		
	public function setNombreagente($nombreagente)
	{
		$this->nombreagente = $nombreagente;
	}
	# Documentacion para el metodo setCentro_inscriptor 		
	public function setCentro_inscriptor($centro_inscriptor)
	{
		$this->centro_inscriptor = $centro_inscriptor;
	}
	# Documentacion para el metodo setOs 		
	public function setOs($os)
	{
		$this->os = $os;
	}
	# Documentacion para el metodo setDni_agente 		
	public function setDni_agente($dni_agente)
	{
		$this->dni_agente = $dni_agente;
	}
	# Documentacion para el metodo setCual_os 		
	public function setCual_os($cual_os)
	{
		$this->cual_os = $cual_os;
	}



	### SETTERS
	
	# Documentacion para el metodo getId_formulario 		
	public function getId_formulario()
	{
		return($this->id_formulario);
	}
	# Documentacion para el metodo getNroformulario 		
	public function getNroformulario()
	{
		return($this->nroformulario);
	}
	# Documentacion para el metodo getFactores_riesgo 		
	public function getFactores_riesgo()
	{
		return($this->factores_riesgo);
	}
	# Documentacion para el metodo getHta2 		
	public function getHta2()
	{
		return($this->hta2);
	}
	# Documentacion para el metodo getHta3 		
	public function getHta3()
	{
		return($this->hta3);
	}
	# Documentacion para el metodo getColesterol4 		
	public function getColesterol4()
	{
		return($this->colesterol4);
	}
	# Documentacion para el metodo getColesterol5 		
	public function getColesterol5()
	{
		return($this->colesterol5);
	}
	# Documentacion para el metodo getDmt26 		
	public function getDmt26()
	{
		return($this->dmt26);
	}
	# Documentacion para el metodo getDmt27 		
	public function getDmt27()
	{
		return($this->dmt27);
	}
	# Documentacion para el metodo getEcv8 		
	public function getEcv8()
	{
		return($this->ecv8);
	}
	# Documentacion para el metodo getTabaco9 		
	public function getTabaco9()
	{
		return($this->tabaco9);
	}
	# Documentacion para el metodo getPuntaje_final 		
	public function getPuntaje_final()
	{
		return($this->puntaje_final);
	}
	# Documentacion para el metodo getApellidoagente 		
	public function getApellidoagente()
	{
		return($this->apellidoagente);
	}
	# Documentacion para el metodo getNombreagente 		
	public function getNombreagente()
	{
		return($this->nombreagente);
	}
	# Documentacion para el metodo getCentro_inscriptor 		
	public function getCentro_inscriptor()
	{
		return($this->centro_inscriptor);
	}
	# Documentacion para el metodo getOs 		
	public function getOs()
	{
		return($this->os);
	}
	# Documentacion para el metodo getDni_agente 		
	public function getDni_agente()
	{
		return($this->dni_agente);
	}
	# Documentacion para el metodo getCual_os 		
	public function getCual_os()
	{
		return($this->cual_os);
	}


	#	Metodo NuevoNumero 		
	public function NuevoNumero()
	{
		$result = sql($this->getSqlNuevoNumero());
		$this->nroformulario = $result->fields['nroformulario'];
	}


	#	Metodo Actualizar 		
	public function Actualizar()
	{
		$sql = $this->getSQlUpdate();
		$result = sql($sql);
	}

	#	Metodo Insertar 		
	public function Insertar()
	{
		$sql = $this->getSQlInsert();
		$result = sql($sql);
	}



	### SQLS
	
	# Documentacion para metodo getSQlInsert
	public function getSQlInsert(){
		$sql = "
				INSERT INTO remediar.formulario(
		            nroformulario, factores_riesgo, hta2, hta3, colesterol4, 
		            colesterol5, dmt26, dmt27, ecv8, tabaco9, puntaje_final, apellidoagente, 
		            nombreagente, centro_inscriptor, os, dni_agente, cual_os)
		    VALUES (".$this->nroformulario.", ".$this->factores_riesgo.", ".$this->hta2.", ".$this->hta3.", ".$this->colesterol4.", 
		            ".$this->colesterol5.", ".$this->dmt26.", ".$this->dmt27.", ".$this->ecv8.", ".$this->tabaco9.", ".$this->puntaje_final.", '".$this->apellidoagente."', 
		            '".$this->nombreagente."', '".$this->centro_inscriptor."', '".$this->os."', '".$this->dni_agente."', '".$this->cual_os."')
			RETURNING nroformulario;

		";
		return($sql);
	}

	# Documentacion para metodo getSQlSelect
	public function getSQlSelect($where = ""){
		if (strlen($where) > 1) {
			$sql = "SELECT id_formulario, nroformulario, factores_riesgo, hta2, hta3, colesterol4, 
						colesterol5, dmt26, dmt27, ecv8, tabaco9, puntaje_final, apellidoagente, 
						nombreagente, centro_inscriptor, os, dni_agente, cual_os
					FROM remediar.formulario
					WHERE ".$where."
					";
		} else {
			$sql = "SELECT id_formulario, nroformulario, factores_riesgo, hta2, hta3, colesterol4, 
						colesterol5, dmt26, dmt27, ecv8, tabaco9, puntaje_final, apellidoagente, 
						nombreagente, centro_inscriptor, os, dni_agente, cual_os
					FROM remediar.formulario";
		}
		
		return($sql);
	}

	# Documentacion para metodo getSQlUpdate
	public function getSQlUpdate(){
		$sql = "
				UPDATE remediar.formulario
				   SET nroformulario=".$this->nroformulario.", factores_riesgo=".$this->factores_riesgo.", hta2=".$this->hta2.", 
				       hta3=".$this->hta3.", colesterol4=".$this->colesterol4.", colesterol5=".$this->colesterol5.", dmt26=".$this->dmt26.", dmt27=".$this->dmt27.", ecv8=".$this->ecv8.", 
				       tabaco9=".$this->tabaco9.", puntaje_final=".$this->puntaje_final.", apellidoagente='".$this->apellidoagente."', nombreagente='".$this->nombreagente."', 
				       centro_inscriptor='".$this->centro_inscriptor."', os='".$this->os."', dni_agente='".$this->dni_agente."', cual_os='".$this->cual_os."'
				 WHERE id_formulario = ".$this->id_formulario.";

		";
		return($sql);
	}

	# Documentacion para metodo getSQlDelete
	public function getSQlDelete(){
		$sql = '';
		return($sql);
	}

	#	Metodo getSqlNuevoNumero 		
	public function getSqlNuevoNumero()
	{
		$sql = "SELECT NEXTVAL('remediar.formulario_secuenciador') as nroformulario";
		return($sql);
	}
		

}

















/**
*  TRAZADORAS.CLASIFICACION_REMEDIAR
*/
class Clasificacion1

{
	var $id_clasificacion = '';
	var $cuie = '';
	var $clave = '';
	var $clase_doc = '';
	var $tipo_doc = '';
	var $num_doc = '';
	var $apellido = '';
	var $nombre = '';
	var $fecha_nac = '';
	var $fecha_control = '';
	var $peso = '';
	var $talla = '';
	var $nino_edad = '';
	var $fecha_carga = '';
	var $usuario = '';
	var $dbt = '';
	var $hta = '';
	var $tabaquismo = '';
	var $dislipemia = '';
	var $obesidad = '';
	var $rcvg = '';
	var $ta_sist = '';
	var $ta_diast = '';
	var $col_tot = '';
	var $hdl = '';
	var $ldl = '';
	var $tagss = '';
	var $gluc = '';
	var $hba1 = '';
	var $enalapril_mg = '';
	var $furosemida_mg = '';
	var $glibenclam_mg = '';
	var $simvastat_mg = '';
	var $otras_drogas = '';
	var $otras_drogas_mg = '';
	var $atenolol_mg = '';
	var $hidroclorot_mg = '';
	var $metformina_mg = '';
	var $insulina = '';
	var $ass_mg = '';
	var $otras_drogas2 = '';
	var $otras_drogas2_mg = '';
	var $nro_clasificacion = '';
	var $fecha_modif = '';
	var $usuario_modif = '';
	var $id_medico = '';
	var $enviado = 'n';
	var $fecha_envio = '';


	function __construct()
	{
		
	}

	### Constructores
	
	public function construirResult($result){
		
		$this->id_clasificacion = $result->fields['id_clasificacion'];
		$this->cuie = $result->fields['cuie'];
		$this->clave = $result->fields['clave'];
		$this->clase_doc = $result->fields['clase_doc'];
		$this->tipo_doc = $result->fields['tipo_doc'];
		$this->num_doc = $result->fields['num_doc'];
		$this->apellido = $result->fields['apellido'];
		$this->nombre = $result->fields['nombre'];
		$this->fecha_nac = $result->fields['fecha_nac'];
		$this->fecha_control = $result->fields['fecha_control'];
		$this->peso = $result->fields['peso'];
		$this->talla = $result->fields['talla'];
		$this->nino_edad = $result->fields['nino_edad'];
		$this->fecha_carga = $result->fields['fecha_carga'];
		$this->usuario = $result->fields['usuario'];
		$this->dbt = $result->fields['dbt'];
		$this->hta = $result->fields['hta'];
		$this->tabaquismo = $result->fields['tabaquismo'];
		$this->dislipemia = $result->fields['dislipemia'];
		$this->obesidad = $result->fields['obesidad'];
		$this->rcvg = $result->fields['rcvg'];
		$this->ta_sist = $result->fields['ta_sist'];
		$this->ta_diast = $result->fields['ta_diast'];
		$this->col_tot = $result->fields['col_tot'];
		$this->hdl = $result->fields['hdl'];
		$this->ldl = $result->fields['ldl'];
		$this->tagss = $result->fields['tagss'];
		$this->gluc = $result->fields['gluc'];
		$this->hba1 = $result->fields['hba1'];
		$this->enalapril_mg = $result->fields['enalapril_mg'];
		$this->furosemida_mg = $result->fields['furosemida_mg'];
		$this->glibenclam_mg = $result->fields['glibenclam_mg'];
		$this->simvastat_mg = $result->fields['simvastat_mg'];
		$this->otras_drogas = $result->fields['otras_drogas'];
		$this->otras_drogas_mg = $result->fields['otras_drogas_mg'];
		$this->atenolol_mg = $result->fields['atenolol_mg'];
		$this->hidroclorot_mg = $result->fields['hidroclorot_mg'];
		$this->metformina_mg = $result->fields['metformina_mg'];
		$this->insulina = $result->fields['insulina'];
		$this->ass_mg = $result->fields['ass_mg'];
		$this->otras_drogas2 = $result->fields['otras_drogas2'];
		$this->otras_drogas2_mg = $result->fields['otras_drogas2_mg'];
		$this->nro_clasificacion = $result->fields['nro_clasificacion'];
		$this->fecha_modif = $result->fields['fecha_modif'];
		$this->usuario_modif = $result->fields['usuario_modif'];
		$this->id_medico = $result->fields['id_medico'];
		$this->enviado = $result->fields['enviado'];
		$this->fecha_envio = $result->fields['fecha_envio'];
	}


	### GETTERS
	
	# Documentacion para el metodo setId_clasificacion 		
	public function setId_clasificacion($id_clasificacion)
	{
		$this->id_clasificacion = $id_clasificacion;
	}
	# Documentacion para el metodo setCuie 		
	public function setCuie($cuie)
	{
		$this->cuie = $cuie;
	}
	# Documentacion para el metodo setClave 		
	public function setClave($clave)
	{
		$this->clave = $clave;
	}
	# Documentacion para el metodo setClase_doc 		
	public function setClase_doc($clase_doc)
	{
		$this->clase_doc = $clase_doc;
	}
	# Documentacion para el metodo setTipo_doc 		
	public function setTipo_doc($tipo_doc)
	{
		$this->tipo_doc = $tipo_doc;
	}
	# Documentacion para el metodo setNum_doc 		
	public function setNum_doc($num_doc)
	{
		$this->num_doc = $num_doc;
	}
	# Documentacion para el metodo setApellido 		
	public function setApellido($apellido)
	{
		$this->apellido = $apellido;
	}
	# Documentacion para el metodo setNombre 		
	public function setNombre($nombre)
	{
		$this->nombre = $nombre;
	}
	# Documentacion para el metodo setFecha_nac 		
	public function setFecha_nac($fecha_nac)
	{
		$this->fecha_nac = $fecha_nac;
	}
	# Documentacion para el metodo setFecha_control 		
	public function setFecha_control($fecha_control)
	{
		$this->fecha_control = $fecha_control;
	}
	# Documentacion para el metodo setPeso 		
	public function setPeso($peso)
	{
		$this->peso = $peso;
	}
	# Documentacion para el metodo setTalla 		
	public function setTalla($talla)
	{
		$this->talla = $talla;
	}
	# Documentacion para el metodo setNino_edad 		
	public function setNino_edad($nino_edad)
	{
		$this->nino_edad = $nino_edad;
	}
	# Documentacion para el metodo setFecha_carga 		
	public function setFecha_carga($fecha_carga)
	{
		$this->fecha_carga = $fecha_carga;
	}
	# Documentacion para el metodo setUsuario 		
	public function setUsuario($usuario)
	{
		$this->usuario = $usuario;
	}
	# Documentacion para el metodo setDbt 		
	public function setDbt($dbt)
	{
		$this->dbt = $dbt;
	}
	# Documentacion para el metodo setHta 		
	public function setHta($hta)
	{
		$this->hta = $hta;
	}
	# Documentacion para el metodo setTabaquismo 		
	public function setTabaquismo($tabaquismo)
	{
		$this->tabaquismo = $tabaquismo;
	}
	# Documentacion para el metodo setDislipemia 		
	public function setDislipemia($dislipemia)
	{
		$this->dislipemia = $dislipemia;
	}
	# Documentacion para el metodo setObesidad 		
	public function setObesidad($obesidad)
	{
		$this->obesidad = $obesidad;
	}
	# Documentacion para el metodo setRcvg 		
	public function setRcvg($rcvg)
	{
		$this->rcvg = $rcvg;
	}
	# Documentacion para el metodo setTa_sist 		
	public function setTa_sist($ta_sist)
	{
		$this->ta_sist = $ta_sist;
	}
	# Documentacion para el metodo setTa_diast 		
	public function setTa_diast($ta_diast)
	{
		$this->ta_diast = $ta_diast;
	}
	# Documentacion para el metodo setCol_tot 		
	public function setCol_tot($col_tot)
	{
		$this->col_tot = $col_tot;
	}
	# Documentacion para el metodo setHdl 		
	public function setHdl($hdl)
	{
		$this->hdl = $hdl;
	}
	# Documentacion para el metodo setLdl 		
	public function setLdl($ldl)
	{
		$this->ldl = $ldl;
	}
	# Documentacion para el metodo setTagss 		
	public function setTagss($tagss)
	{
		$this->tagss = $tagss;
	}
	# Documentacion para el metodo setGluc 		
	public function setGluc($gluc)
	{
		$this->gluc = $gluc;
	}
	# Documentacion para el metodo setHba1 		
	public function setHba1($hba1)
	{
		$this->hba1 = $hba1;
	}
	# Documentacion para el metodo setEnalapril_mg 		
	public function setEnalapril_mg($enalapril_mg)
	{
		$this->enalapril_mg = $enalapril_mg;
	}
	# Documentacion para el metodo setFurosemida_mg 		
	public function setFurosemida_mg($furosemida_mg)
	{
		$this->furosemida_mg = $furosemida_mg;
	}
	# Documentacion para el metodo setGlibenclam_mg 		
	public function setGlibenclam_mg($glibenclam_mg)
	{
		$this->glibenclam_mg = $glibenclam_mg;
	}
	# Documentacion para el metodo setSimvastat_mg 		
	public function setSimvastat_mg($simvastat_mg)
	{
		$this->simvastat_mg = $simvastat_mg;
	}
	# Documentacion para el metodo setOtras_drogas 		
	public function setOtras_drogas($otras_drogas)
	{
		$this->otras_drogas = $otras_drogas;
	}
	# Documentacion para el metodo setOtras_drogas_mg 		
	public function setOtras_drogas_mg($otras_drogas_mg)
	{
		$this->otras_drogas_mg = $otras_drogas_mg;
	}
	# Documentacion para el metodo setAtenolol_mg 		
	public function setAtenolol_mg($atenolol_mg)
	{
		$this->atenolol_mg = $atenolol_mg;
	}
	# Documentacion para el metodo setHidroclorot_mg 		
	public function setHidroclorot_mg($hidroclorot_mg)
	{
		$this->hidroclorot_mg = $hidroclorot_mg;
	}
	# Documentacion para el metodo setMetformina_mg 		
	public function setMetformina_mg($metformina_mg)
	{
		$this->metformina_mg = $metformina_mg;
	}
	# Documentacion para el metodo setInsulina 		
	public function setInsulina($insulina)
	{
		$this->insulina = $insulina;
	}
	# Documentacion para el metodo setAss_mg 		
	public function setAss_mg($ass_mg)
	{
		$this->ass_mg = $ass_mg;
	}
	# Documentacion para el metodo setOtras_drogas2 		
	public function setOtras_drogas2($otras_drogas2)
	{
		$this->otras_drogas2 = $otras_drogas2;
	}
	# Documentacion para el metodo setOtras_drogas2_mg 		
	public function setOtras_drogas2_mg($otras_drogas2_mg)
	{
		$this->otras_drogas2_mg = $otras_drogas2_mg;
	}
	# Documentacion para el metodo setNro_clasificacion 		
	public function setNro_clasificacion($nro_clasificacion)
	{
		$this->nro_clasificacion = $nro_clasificacion;
	}
	# Documentacion para el metodo setFecha_modif 		
	public function setFecha_modif($fecha_modif)
	{
		$this->fecha_modif = $fecha_modif;
	}
	# Documentacion para el metodo setUsuario_modif 		
	public function setUsuario_modif($usuario_modif)
	{
		$this->usuario_modif = $usuario_modif;
	}
	# Documentacion para el metodo setId_medico 		
	public function setId_medico($id_medico)
	{
		$this->id_medico = $id_medico;
	}
	# Documentacion para el metodo setEnviado 		
	public function setEnviado($enviado)
	{
		$this->enviado = $enviado;
	}
	# Documentacion para el metodo setFecha_envio 		
	public function setFecha_envio($fecha_envio)
	{
		$this->fecha_envio = $fecha_envio;
	}



	### SETTERS
	
	# Documentacion para el metodo getId_clasificacion 		
	public function getId_clasificacion()
	{
		return($this->id_clasificacion);
	}
	# Documentacion para el metodo getCuie 		
	public function getCuie()
	{
		return($this->cuie);
	}
	# Documentacion para el metodo getClave 		
	public function getClave()
	{
		return($this->clave);
	}
	# Documentacion para el metodo getClase_doc 		
	public function getClase_doc()
	{
		return($this->clase_doc);
	}
	# Documentacion para el metodo getTipo_doc 		
	public function getTipo_doc()
	{
		return($this->tipo_doc);
	}
	# Documentacion para el metodo getNum_doc 		
	public function getNum_doc()
	{
		return($this->num_doc);
	}
	# Documentacion para el metodo getApellido 		
	public function getApellido()
	{
		return($this->apellido);
	}
	# Documentacion para el metodo getNombre 		
	public function getNombre()
	{
		return($this->nombre);
	}
	# Documentacion para el metodo getFecha_nac 		
	public function getFecha_nac()
	{
		return($this->fecha_nac);
	}
	# Documentacion para el metodo getFecha_control 		
	public function getFecha_control()
	{
		return($this->fecha_control);
	}
	# Documentacion para el metodo getPeso 		
	public function getPeso()
	{
		return($this->peso);
	}
	# Documentacion para el metodo getTalla 		
	public function getTalla()
	{
		return($this->talla);
	}
	# Documentacion para el metodo getNino_edad 		
	public function getNino_edad()
	{
		return($this->nino_edad);
	}
	# Documentacion para el metodo getFecha_carga 		
	public function getFecha_carga()
	{
		return($this->fecha_carga);
	}
	# Documentacion para el metodo getUsuario 		
	public function getUsuario()
	{
		return($this->usuario);
	}
	# Documentacion para el metodo getDbt 		
	public function getDbt()
	{
		return($this->dbt);
	}
	# Documentacion para el metodo getHta 		
	public function getHta()
	{
		return($this->hta);
	}
	# Documentacion para el metodo getTabaquismo 		
	public function getTabaquismo()
	{
		return($this->tabaquismo);
	}
	# Documentacion para el metodo getDislipemia 		
	public function getDislipemia()
	{
		return($this->dislipemia);
	}
	# Documentacion para el metodo getObesidad 		
	public function getObesidad()
	{
		return($this->obesidad);
	}
	# Documentacion para el metodo getRcvg 		
	public function getRcvg()
	{
		return($this->rcvg);
	}
	# Documentacion para el metodo getTa_sist 		
	public function getTa_sist()
	{
		return($this->ta_sist);
	}
	# Documentacion para el metodo getTa_diast 		
	public function getTa_diast()
	{
		return($this->ta_diast);
	}
	# Documentacion para el metodo getCol_tot 		
	public function getCol_tot()
	{
		return($this->col_tot);
	}
	# Documentacion para el metodo getHdl 		
	public function getHdl()
	{
		return($this->hdl);
	}
	# Documentacion para el metodo getLdl 		
	public function getLdl()
	{
		return($this->ldl);
	}
	# Documentacion para el metodo getTagss 		
	public function getTagss()
	{
		return($this->tagss);
	}
	# Documentacion para el metodo getGluc 		
	public function getGluc()
	{
		return($this->gluc);
	}
	# Documentacion para el metodo getHba1 		
	public function getHba1()
	{
		return($this->hba1);
	}
	# Documentacion para el metodo getEnalapril_mg 		
	public function getEnalapril_mg()
	{
		return($this->enalapril_mg);
	}
	# Documentacion para el metodo getFurosemida_mg 		
	public function getFurosemida_mg()
	{
		return($this->furosemida_mg);
	}
	# Documentacion para el metodo getGlibenclam_mg 		
	public function getGlibenclam_mg()
	{
		return($this->glibenclam_mg);
	}
	# Documentacion para el metodo getSimvastat_mg 		
	public function getSimvastat_mg()
	{
		return($this->simvastat_mg);
	}
	# Documentacion para el metodo getOtras_drogas 		
	public function getOtras_drogas()
	{
		return($this->otras_drogas);
	}
	# Documentacion para el metodo getOtras_drogas_mg 		
	public function getOtras_drogas_mg()
	{
		return($this->otras_drogas_mg);
	}
	# Documentacion para el metodo getAtenolol_mg 		
	public function getAtenolol_mg()
	{
		return($this->atenolol_mg);
	}
	# Documentacion para el metodo getHidroclorot_mg 		
	public function getHidroclorot_mg()
	{
		return($this->hidroclorot_mg);
	}
	# Documentacion para el metodo getMetformina_mg 		
	public function getMetformina_mg()
	{
		return($this->metformina_mg);
	}
	# Documentacion para el metodo getInsulina 		
	public function getInsulina()
	{
		return($this->insulina);
	}
	# Documentacion para el metodo getAss_mg 		
	public function getAss_mg()
	{
		return($this->ass_mg);
	}
	# Documentacion para el metodo getOtras_drogas2 		
	public function getOtras_drogas2()
	{
		return($this->otras_drogas2);
	}
	# Documentacion para el metodo getOtras_drogas2_mg 		
	public function getOtras_drogas2_mg()
	{
		return($this->otras_drogas2_mg);
	}
	# Documentacion para el metodo getNro_clasificacion 		
	public function getNro_clasificacion()
	{
		return($this->nro_clasificacion);
	}
	# Documentacion para el metodo getFecha_modif 		
	public function getFecha_modif()
	{
		return($this->fecha_modif);
	}
	# Documentacion para el metodo getUsuario_modif 		
	public function getUsuario_modif()
	{
		return($this->usuario_modif);
	}
	# Documentacion para el metodo getId_medico 		
	public function getId_medico()
	{
		return($this->id_medico);
	}
	# Documentacion para el metodo getEnviado 		
	public function getEnviado()
	{
		return($this->enviado);
	}
	# Documentacion para el metodo getFecha_envio 		
	public function getFecha_envio()
	{
		return($this->fecha_envio);
	}




	### SQLS
	
	# Documentacion para metodo getSQlInsert
	public function getSQlInsert(){
		$sql = '';
		return($sql);
	}

	# Documentacion para metodo getSQlSelect
	public function getSQlSelect(){
		$sql = "
			SELECT id_clasificacion, cuie, clave, clase_doc, tipo_doc, num_doc, 
		       apellido, nombre, fecha_nac, fecha_control, peso, talla, nino_edad, 
		       fecha_carga, usuario, dbt, hta, tabaquismo, dislipemia, obesidad, 
		       rcvg, ta_sist, ta_diast, col_tot, hdl, ldl, tagss, gluc, hba1, 
		       enalapril_mg, furosemida_mg, glibenclam_mg, simvastat_mg, otras_drogas, 
		       otras_drogas_mg, atenolol_mg, hidroclorot_mg, metformina_mg, 
		       insulina, ass_mg, otras_drogas2, otras_drogas2_mg, nro_clasificacion, 
		       fecha_modif, usuario_modif, id_medico, enviado, fecha_envio
		  FROM trazadoras.clasificacion_remediar
		";
		return($sql);
	}

	# Documentacion para metodo getSQlSelect
	public function getSQlSelectWhere($where){
		$sql = "
			SELECT id_clasificacion, cuie, clave, clase_doc, tipo_doc, num_doc, 
		       apellido, nombre, fecha_nac, fecha_control, peso, talla, nino_edad, 
		       fecha_carga, usuario, dbt, hta, tabaquismo, dislipemia, obesidad, 
		       rcvg, ta_sist, ta_diast, col_tot, hdl, ldl, tagss, gluc, hba1, 
		       enalapril_mg, furosemida_mg, glibenclam_mg, simvastat_mg, otras_drogas, 
		       otras_drogas_mg, atenolol_mg, hidroclorot_mg, metformina_mg, 
		       insulina, ass_mg, otras_drogas2, otras_drogas2_mg, nro_clasificacion, 
		       fecha_modif, usuario_modif, id_medico, enviado, fecha_envio
		  FROM trazadoras.clasificacion_remediar
		  WHERE ".$where."";
		return($sql);
	}

	# Documentacion para metodo getSQlUpdate
	public function getSQlUpdate(){
		$sql = '';
		return($sql);
	}

	# Documentacion para metodo getSQlDelete
	public function getSQlDelete(){
		$sql = '';
		return($sql);
	}
		

}




/**
*  TRAZADORAS.CLASIFICACION_REMEDIAR2
*/
class Clasificacion2

{
	var $id_clasificacion = '';
	var $clave_beneficiario = '';
	var $tipo_doc = '';
	var $num_doc = '';
	var $apellido = '';
	var $nombre = '';
	var $fecha_nac = '';
	var $fecha_carga = '';
	var $dmt = '';
	var $ta_sist = '';
	var $ta_diast = '';
	var $col_tot = '';
	var $nro_clasificacion = '';
	var $id_medico = '';
	var $cuie = '';
	var $acv = '';
	var $vas_per = '';
	var $car_isq = '';
	var $col310 = '';
	var $col_ldl = '';
	var $ct_hdl = '';
	var $pres_art = '';
	var $dmt2 = '';
	var $insu_renal = '';
	var $dmt_menor = '';
	var $hta_menor = '';
	var $menopausia = '';
	var $antihiper = '';
	var $obesi = '';
	var $acv_prema = '';
	var $trigli = '';
	var $hdl_col = '';
	var $hiperglu = '';
	var $microalbu = '';
	var $tabaquismo = '';
	var $hta = '';
	var $rcvg = '';
	var $enviado = '';
	var $fecha_envio = '';
	var $fecha_control = '';
	var $usuario = '';


	function __construct()
	{
		
	}

	### Constructores
	
	public function construirResult($result){
		
		$this->id_clasificacion = $result->fields['id_clasificacion'];
		$this->clave_beneficiario = $result->fields['clave_beneficiario'];
		$this->tipo_doc = $result->fields['tipo_doc'];
		$this->num_doc = $result->fields['num_doc'];
		$this->apellido = $result->fields['apellido'];
		$this->nombre = $result->fields['nombre'];
		$this->fecha_nac = $result->fields['fecha_nac'];
		$this->fecha_carga = $result->fields['fecha_carga'];
		$this->dmt = $result->fields['dmt'];
		$this->ta_sist = $result->fields['ta_sist'];
		$this->ta_diast = $result->fields['ta_diast'];
		$this->col_tot = $result->fields['col_tot'];
		$this->nro_clasificacion = $result->fields['nro_clasificacion'];
		$this->id_medico = $result->fields['id_medico'];
		$this->cuie = $result->fields['cuie'];
		$this->acv = $result->fields['acv'];
		$this->vas_per = $result->fields['vas_per'];
		$this->car_isq = $result->fields['car_isq'];
		$this->col310 = $result->fields['col310'];
		$this->col_ldl = $result->fields['col_ldl'];
		$this->ct_hdl = $result->fields['ct_hdl'];
		$this->pres_art = $result->fields['pres_art'];
		$this->dmt2 = $result->fields['dmt2'];
		$this->insu_renal = $result->fields['insu_renal'];
		$this->dmt_menor = $result->fields['dmt_menor'];
		$this->hta_menor = $result->fields['hta_menor'];
		$this->menopausia = $result->fields['menopausia'];
		$this->antihiper = $result->fields['antihiper'];
		$this->obesi = $result->fields['obesi'];
		$this->acv_prema = $result->fields['acv_prema'];
		$this->trigli = $result->fields['trigli'];
		$this->hdl_col = $result->fields['hdl_col'];
		$this->hiperglu = $result->fields['hiperglu'];
		$this->microalbu = $result->fields['microalbu'];
		$this->tabaquismo = $result->fields['tabaquismo'];
		$this->hta = $result->fields['hta'];
		$this->rcvg = $result->fields['rcvg'];
		$this->enviado = $result->fields['enviado'];
		$this->fecha_envio = $result->fields['fecha_envio'];
		$this->fecha_control = $result->fields['fecha_control'];
		$this->usuario = $result->fields['usuario'];
	}


	### GETTERS
	
	# Documentacion para el metodo setId_clasificacion 		
	public function setId_clasificacion($id_clasificacion)
	{
		$this->id_clasificacion = $id_clasificacion;
	}
	# Documentacion para el metodo setClave_beneficiario 		
	public function setClave_beneficiario($clave_beneficiario)
	{
		$this->clave_beneficiario = $clave_beneficiario;
	}
	# Documentacion para el metodo setTipo_doc 		
	public function setTipo_doc($tipo_doc)
	{
		$this->tipo_doc = $tipo_doc;
	}
	# Documentacion para el metodo setNum_doc 		
	public function setNum_doc($num_doc)
	{
		$this->num_doc = $num_doc;
	}
	# Documentacion para el metodo setApellido 		
	public function setApellido($apellido)
	{
		$this->apellido = $apellido;
	}
	# Documentacion para el metodo setNombre 		
	public function setNombre($nombre)
	{
		$this->nombre = $nombre;
	}
	# Documentacion para el metodo setFecha_nac 		
	public function setFecha_nac($fecha_nac)
	{
		$this->fecha_nac = $fecha_nac;
	}
	# Documentacion para el metodo setFecha_carga 		
	public function setFecha_carga($fecha_carga)
	{
		$this->fecha_carga = $fecha_carga;
	}
	# Documentacion para el metodo setDmt 		
	public function setDmt($dmt)
	{
		$this->dmt = $dmt;
	}
	# Documentacion para el metodo setTa_sist 		
	public function setTa_sist($ta_sist)
	{
		$this->ta_sist = $ta_sist;
	}
	# Documentacion para el metodo setTa_diast 		
	public function setTa_diast($ta_diast)
	{
		$this->ta_diast = $ta_diast;
	}
	# Documentacion para el metodo setCol_tot 		
	public function setCol_tot($col_tot)
	{
		$this->col_tot = $col_tot;
	}
	# Documentacion para el metodo setNro_clasificacion 		
	public function setNro_clasificacion($nro_clasificacion)
	{
		$this->nro_clasificacion = $nro_clasificacion;
	}
	# Documentacion para el metodo setId_medico 		
	public function setId_medico($id_medico)
	{
		$this->id_medico = $id_medico;
	}
	# Documentacion para el metodo setCuie 		
	public function setCuie($cuie)
	{
		$this->cuie = $cuie;
	}
	# Documentacion para el metodo setAcv 		
	public function setAcv($acv)
	{
		$this->acv = $acv;
	}
	# Documentacion para el metodo setVas_per 		
	public function setVas_per($vas_per)
	{
		$this->vas_per = $vas_per;
	}
	# Documentacion para el metodo setCar_isq 		
	public function setCar_isq($car_isq)
	{
		$this->car_isq = $car_isq;
	}
	# Documentacion para el metodo setCol310 		
	public function setCol310($col310)
	{
		$this->col310 = $col310;
	}
	# Documentacion para el metodo setCol_ldl 		
	public function setCol_ldl($col_ldl)
	{
		$this->col_ldl = $col_ldl;
	}
	# Documentacion para el metodo setCt_hdl 		
	public function setCt_hdl($ct_hdl)
	{
		$this->ct_hdl = $ct_hdl;
	}
	# Documentacion para el metodo setPres_art 		
	public function setPres_art($pres_art)
	{
		$this->pres_art = $pres_art;
	}
	# Documentacion para el metodo setDmt2 		
	public function setDmt2($dmt2)
	{
		$this->dmt2 = $dmt2;
	}
	# Documentacion para el metodo setInsu_renal 		
	public function setInsu_renal($insu_renal)
	{
		$this->insu_renal = $insu_renal;
	}
	# Documentacion para el metodo setDmt_menor 		
	public function setDmt_menor($dmt_menor)
	{
		$this->dmt_menor = $dmt_menor;
	}
	# Documentacion para el metodo setHta_menor 		
	public function setHta_menor($hta_menor)
	{
		$this->hta_menor = $hta_menor;
	}
	# Documentacion para el metodo setMenopausia 		
	public function setMenopausia($menopausia)
	{
		$this->menopausia = $menopausia;
	}
	# Documentacion para el metodo setAntihiper 		
	public function setAntihiper($antihiper)
	{
		$this->antihiper = $antihiper;
	}
	# Documentacion para el metodo setObesi 		
	public function setObesi($obesi)
	{
		$this->obesi = $obesi;
	}
	# Documentacion para el metodo setAcv_prema 		
	public function setAcv_prema($acv_prema)
	{
		$this->acv_prema = $acv_prema;
	}
	# Documentacion para el metodo setTrigli 		
	public function setTrigli($trigli)
	{
		$this->trigli = $trigli;
	}
	# Documentacion para el metodo setHdl_col 		
	public function setHdl_col($hdl_col)
	{
		$this->hdl_col = $hdl_col;
	}
	# Documentacion para el metodo setHiperglu 		
	public function setHiperglu($hiperglu)
	{
		$this->hiperglu = $hiperglu;
	}
	# Documentacion para el metodo setMicroalbu 		
	public function setMicroalbu($microalbu)
	{
		$this->microalbu = $microalbu;
	}
	# Documentacion para el metodo setTabaquismo 		
	public function setTabaquismo($tabaquismo)
	{
		$this->tabaquismo = $tabaquismo;
	}
	# Documentacion para el metodo setHta 		
	public function setHta($hta)
	{
		$this->hta = $hta;
	}
	# Documentacion para el metodo setRcvg 		
	public function setRcvg($rcvg)
	{
		$this->rcvg = $rcvg;
	}
	# Documentacion para el metodo setEnviado 		
	public function setEnviado($enviado)
	{
		$this->enviado = $enviado;
	}
	# Documentacion para el metodo setFecha_envio 		
	public function setFecha_envio($fecha_envio)
	{
		$this->fecha_envio = $fecha_envio;
	}
	# Documentacion para el metodo setFecha_control 		
	public function setFecha_control($fecha_control)
	{
		$this->fecha_control = $fecha_control;
	}
	# Documentacion para el metodo setUsuario 		
	public function setUsuario($usuario)
	{
		$this->usuario = $usuario;
	}



	### SETTERS
	
	# Documentacion para el metodo getId_clasificacion 		
	public function getId_clasificacion()
	{
		return($this->id_clasificacion);
	}
	# Documentacion para el metodo getClave_beneficiario 		
	public function getClave_beneficiario()
	{
		return($this->clave_beneficiario);
	}
	# Documentacion para el metodo getTipo_doc 		
	public function getTipo_doc()
	{
		return($this->tipo_doc);
	}
	# Documentacion para el metodo getNum_doc 		
	public function getNum_doc()
	{
		return($this->num_doc);
	}
	# Documentacion para el metodo getApellido 		
	public function getApellido()
	{
		return($this->apellido);
	}
	# Documentacion para el metodo getNombre 		
	public function getNombre()
	{
		return($this->nombre);
	}
	# Documentacion para el metodo getFecha_nac 		
	public function getFecha_nac()
	{
		return($this->fecha_nac);
	}
	# Documentacion para el metodo getFecha_carga 		
	public function getFecha_carga()
	{
		return($this->fecha_carga);
	}
	# Documentacion para el metodo getDmt 		
	public function getDmt()
	{
		return($this->dmt);
	}
	# Documentacion para el metodo getTa_sist 		
	public function getTa_sist()
	{
		return($this->ta_sist);
	}
	# Documentacion para el metodo getTa_diast 		
	public function getTa_diast()
	{
		return($this->ta_diast);
	}
	# Documentacion para el metodo getCol_tot 		
	public function getCol_tot()
	{
		return($this->col_tot);
	}
	# Documentacion para el metodo getNro_clasificacion 		
	public function getNro_clasificacion()
	{
		return($this->nro_clasificacion);
	}
	# Documentacion para el metodo getId_medico 		
	public function getId_medico()
	{
		return($this->id_medico);
	}
	# Documentacion para el metodo getCuie 		
	public function getCuie()
	{
		return($this->cuie);
	}
	# Documentacion para el metodo getAcv 		
	public function getAcv()
	{
		return($this->acv);
	}
	# Documentacion para el metodo getVas_per 		
	public function getVas_per()
	{
		return($this->vas_per);
	}
	# Documentacion para el metodo getCar_isq 		
	public function getCar_isq()
	{
		return($this->car_isq);
	}
	# Documentacion para el metodo getCol310 		
	public function getCol310()
	{
		return($this->col310);
	}
	# Documentacion para el metodo getCol_ldl 		
	public function getCol_ldl()
	{
		return($this->col_ldl);
	}
	# Documentacion para el metodo getCt_hdl 		
	public function getCt_hdl()
	{
		return($this->ct_hdl);
	}
	# Documentacion para el metodo getPres_art 		
	public function getPres_art()
	{
		return($this->pres_art);
	}
	# Documentacion para el metodo getDmt2 		
	public function getDmt2()
	{
		return($this->dmt2);
	}
	# Documentacion para el metodo getInsu_renal 		
	public function getInsu_renal()
	{
		return($this->insu_renal);
	}
	# Documentacion para el metodo getDmt_menor 		
	public function getDmt_menor()
	{
		return($this->dmt_menor);
	}
	# Documentacion para el metodo getHta_menor 		
	public function getHta_menor()
	{
		return($this->hta_menor);
	}
	# Documentacion para el metodo getMenopausia 		
	public function getMenopausia()
	{
		return($this->menopausia);
	}
	# Documentacion para el metodo getAntihiper 		
	public function getAntihiper()
	{
		return($this->antihiper);
	}
	# Documentacion para el metodo getObesi 		
	public function getObesi()
	{
		return($this->obesi);
	}
	# Documentacion para el metodo getAcv_prema 		
	public function getAcv_prema()
	{
		return($this->acv_prema);
	}
	# Documentacion para el metodo getTrigli 		
	public function getTrigli()
	{
		return($this->trigli);
	}
	# Documentacion para el metodo getHdl_col 		
	public function getHdl_col()
	{
		return($this->hdl_col);
	}
	# Documentacion para el metodo getHiperglu 		
	public function getHiperglu()
	{
		return($this->hiperglu);
	}
	# Documentacion para el metodo getMicroalbu 		
	public function getMicroalbu()
	{
		return($this->microalbu);
	}
	# Documentacion para el metodo getTabaquismo 		
	public function getTabaquismo()
	{
		return($this->tabaquismo);
	}
	# Documentacion para el metodo getHta 		
	public function getHta()
	{
		return($this->hta);
	}
	# Documentacion para el metodo getRcvg 		
	public function getRcvg()
	{
		return($this->rcvg);
	}
	# Documentacion para el metodo getEnviado 		
	public function getEnviado()
	{
		return($this->enviado);
	}
	# Documentacion para el metodo getFecha_envio 		
	public function getFecha_envio()
	{
		return($this->fecha_envio);
	}
	# Documentacion para el metodo getFecha_control 		
	public function getFecha_control()
	{
		return($this->fecha_control);
	}
	# Documentacion para el metodo getUsuario 		
	public function getUsuario()
	{
		return($this->usuario);
	}




	### SQLS
	
	# Documentacion para metodo getSQlInsert
	public function getSQlInsert(){
		$sql = '';
		return($sql);
	}

	# Documentacion para metodo getSQlSelect
	public function getSQlSelect(){
		$sql = "
			SELECT id_clasificacion, clave_beneficiario, tipo_doc, num_doc, apellido, 
			       nombre, fecha_nac, fecha_carga, dmt, ta_sist, ta_diast, col_tot, 
			       nro_clasificacion, id_medico, cuie, acv, vas_per, car_isq, col310, 
			       col_ldl, ct_hdl, pres_art, dmt2, insu_renal, dmt_menor, hta_menor, 
			       menopausia, antihiper, obesi, acv_prema, trigli, hdl_col, hiperglu, 
			       microalbu, tabaquismo, hta, rcvg, enviado, fecha_envio, fecha_control, 
			       usuario
			  FROM trazadoras.clasificacion_remediar2;";
		return($sql);
	}

	# Documentacion para metodo getSQlSelect
	public function getSQlSelectWhere($where){
		$sql = "
			SELECT id_clasificacion, clave_beneficiario, tipo_doc, num_doc, apellido, 
			       nombre, fecha_nac, fecha_carga, dmt, ta_sist, ta_diast, col_tot, 
			       nro_clasificacion, id_medico, cuie, acv, vas_per, car_isq, col310, 
			       col_ldl, ct_hdl, pres_art, dmt2, insu_renal, dmt_menor, hta_menor, 
			       menopausia, antihiper, obesi, acv_prema, trigli, hdl_col, hiperglu, 
			       microalbu, tabaquismo, hta, rcvg, enviado, fecha_envio, fecha_control, 
			       usuario
			  FROM trazadoras.clasificacion_remediar2
			  WHERE ".$where."
			  ";
		return($sql);
	}

	# Documentacion para metodo getSQlUpdate
	public function getSQlUpdate(){
		$sql = '';
		return($sql);
	}

	# Documentacion para metodo getSQlDelete
	public function getSQlDelete(){
		$sql = '';
		return($sql);
	}
		

}




/**
*  TRAZADORAS.SEGUIMIENTO_REMEDIAR
*/
class Seguimiento

{
	var $idseguimiento = '';
	var $clavebeneficiario = '';
	var $dmta = '';
	var $hta = '';
	var $tasist = '';
	var $tadiast = '';
	var $tabaquismo = '';
	var $colesterol = '';
	var $glucemia = '';
	var $peso = '';
	var $talla = '';
	var $hba1c = '';
	var $ecg = '';
	var $fondodeojo = '';
	var $examenpie = '';
	var $microalbuminuria = '';
	var $hdl = '';
	var $ldl = '';
	var $tags = '';
	var $imc = '';
	var $creatininemia = '';
	var $interconsulta_a = '';
	var $interconsulta_b = '';
	var $interconsulta_c = '';
	var $interconsulta_d = '';
	var $rcvg_anterior = '';
	var $rcvg_actual = '';
	var $fecha_seguimiento = '';
	var $id_medico = '';
	var $id_usuariocarga = '';
	var $fecha_carga = '';
	var $estado_envio = '';
	var $id_usuariovalidador = '';
	var $estado_validacion = '';
	var $efector = '';
	var $num_seguimiento = '';


	function __construct()
	{
		
	}

	### Constructores
	
	public function construirResult($result){
		
		$this->idseguimiento = $result->fields['idseguimiento'];
		$this->clavebeneficiario = $result->fields['clavebeneficiario'];
		$this->dmta = $result->fields['dmta'];
		$this->hta = $result->fields['hta'];
		$this->tasist = $result->fields['tasist'];
		$this->tadiast = $result->fields['tadiast'];
		$this->tabaquismo = $result->fields['tabaquismo'];
		$this->colesterol = $result->fields['colesterol'];
		$this->glucemia = $result->fields['glucemia'];
		$this->peso = $result->fields['peso'];
		$this->talla = $result->fields['talla'];
		$this->hba1c = $result->fields['hba1c'];
		$this->ecg = $result->fields['ecg'];
		$this->fondodeojo = $result->fields['fondodeojo'];
		$this->examenpie = $result->fields['examenpie'];
		$this->microalbuminuria = $result->fields['microalbuminuria'];
		$this->hdl = $result->fields['hdl'];
		$this->ldl = $result->fields['ldl'];
		$this->tags = $result->fields['tags'];
		$this->imc = $result->fields['imc'];
		$this->creatininemia = $result->fields['creatininemia'];
		$this->interconsulta_a = $result->fields['interconsulta_a'];
		$this->interconsulta_b = $result->fields['interconsulta_b'];
		$this->interconsulta_c = $result->fields['interconsulta_c'];
		$this->interconsulta_d = $result->fields['interconsulta_d'];
		$this->rcvg_anterior = $result->fields['rcvg_anterior'];
		$this->rcvg_actual = $result->fields['rcvg_actual'];
		$this->fecha_seguimiento = $result->fields['fecha_seguimiento'];
		$this->id_medico = $result->fields['id_medico'];
		$this->id_usuariocarga = $result->fields['id_usuariocarga'];
		$this->fecha_carga = $result->fields['fecha_carga'];
		$this->estado_envio = $result->fields['estado_envio'];
		$this->id_usuariovalidador = $result->fields['id_usuariovalidador'];
		$this->estado_validacion = $result->fields['estado_validacion'];
		$this->efector = $result->fields['efector'];
		$this->num_seguimiento = $result->fields['num_seguimiento'];
	}


	### GETTERS
	
	# Documentacion para el metodo setIdseguimiento 		
	public function setIdseguimiento($idseguimiento)
	{
		$this->idseguimiento = $idseguimiento;
	}
	# Documentacion para el metodo setClavebeneficiario 		
	public function setClavebeneficiario($clavebeneficiario)
	{
		$this->clavebeneficiario = $clavebeneficiario;
	}
	# Documentacion para el metodo setDmta 		
	public function setDmta($dmta)
	{
		$this->dmta = $dmta;
	}
	# Documentacion para el metodo setHta 		
	public function setHta($hta)
	{
		$this->hta = $hta;
	}
	# Documentacion para el metodo setTasist 		
	public function setTasist($tasist)
	{
		$this->tasist = $tasist;
	}
	# Documentacion para el metodo setTadiast 		
	public function setTadiast($tadiast)
	{
		$this->tadiast = $tadiast;
	}
	# Documentacion para el metodo setTabaquismo 		
	public function setTabaquismo($tabaquismo)
	{
		$this->tabaquismo = $tabaquismo;
	}
	# Documentacion para el metodo setColesterol 		
	public function setColesterol($colesterol)
	{
		$this->colesterol = $colesterol;
	}
	# Documentacion para el metodo setGlucemia 		
	public function setGlucemia($glucemia)
	{
		$this->glucemia = $glucemia;
	}
	# Documentacion para el metodo setPeso 		
	public function setPeso($peso)
	{
		$this->peso = $peso;
	}
	# Documentacion para el metodo setTalla 		
	public function setTalla($talla)
	{
		$this->talla = $talla;
	}
	# Documentacion para el metodo setHba1c 		
	public function setHba1c($hba1c)
	{
		$this->hba1c = $hba1c;
	}
	# Documentacion para el metodo setEcg 		
	public function setEcg($ecg)
	{
		$this->ecg = $ecg;
	}
	# Documentacion para el metodo setFondodeojo 		
	public function setFondodeojo($fondodeojo)
	{
		$this->fondodeojo = $fondodeojo;
	}
	# Documentacion para el metodo setExamenpie 		
	public function setExamenpie($examenpie)
	{
		$this->examenpie = $examenpie;
	}
	# Documentacion para el metodo setMicroalbuminuria 		
	public function setMicroalbuminuria($microalbuminuria)
	{
		$this->microalbuminuria = $microalbuminuria;
	}
	# Documentacion para el metodo setHdl 		
	public function setHdl($hdl)
	{
		$this->hdl = $hdl;
	}
	# Documentacion para el metodo setLdl 		
	public function setLdl($ldl)
	{
		$this->ldl = $ldl;
	}
	# Documentacion para el metodo setTags 		
	public function setTags($tags)
	{
		$this->tags = $tags;
	}
	# Documentacion para el metodo setImc 		
	public function setImc($imc)
	{
		$this->imc = $imc;
	}
	# Documentacion para el metodo setCreatininemia 		
	public function setCreatininemia($creatininemia)
	{
		$this->creatininemia = $creatininemia;
	}
	# Documentacion para el metodo setInterconsulta_a 		
	public function setInterconsulta_a($interconsulta_a)
	{
		$this->interconsulta_a = $interconsulta_a;
	}
	# Documentacion para el metodo setInterconsulta_b 		
	public function setInterconsulta_b($interconsulta_b)
	{
		$this->interconsulta_b = $interconsulta_b;
	}
	# Documentacion para el metodo setInterconsulta_c 		
	public function setInterconsulta_c($interconsulta_c)
	{
		$this->interconsulta_c = $interconsulta_c;
	}
	# Documentacion para el metodo setInterconsulta_d 		
	public function setInterconsulta_d($interconsulta_d)
	{
		$this->interconsulta_d = $interconsulta_d;
	}
	# Documentacion para el metodo setRcvg_anterior 		
	public function setRcvg_anterior($rcvg_anterior)
	{
		$this->rcvg_anterior = $rcvg_anterior;
	}
	# Documentacion para el metodo setRcvg_actual 		
	public function setRcvg_actual($rcvg_actual)
	{
		$this->rcvg_actual = $rcvg_actual;
	}
	# Documentacion para el metodo setFecha_seguimiento 		
	public function setFecha_seguimiento($fecha_seguimiento)
	{
		$this->fecha_seguimiento = $fecha_seguimiento;
	}
	# Documentacion para el metodo setId_medico 		
	public function setId_medico($id_medico)
	{
		$this->id_medico = $id_medico;
	}
	# Documentacion para el metodo setId_usuariocarga 		
	public function setId_usuariocarga($id_usuariocarga)
	{
		$this->id_usuariocarga = $id_usuariocarga;
	}
	# Documentacion para el metodo setFecha_carga 		
	public function setFecha_carga($fecha_carga)
	{
		$this->fecha_carga = $fecha_carga;
	}
	# Documentacion para el metodo setEstado_envio 		
	public function setEstado_envio($estado_envio)
	{
		$this->estado_envio = $estado_envio;
	}
	# Documentacion para el metodo setId_usuariovalidador 		
	public function setId_usuariovalidador($id_usuariovalidador)
	{
		$this->id_usuariovalidador = $id_usuariovalidador;
	}
	# Documentacion para el metodo setEstado_validacion 		
	public function setEstado_validacion($estado_validacion)
	{
		$this->estado_validacion = $estado_validacion;
	}
	# Documentacion para el metodo setEfector 		
	public function setEfector($efector)
	{
		$this->efector = $efector;
	}
	# Documentacion para el metodo setNum_seguimiento 		
	public function setNum_seguimiento($num_seguimiento)
	{
		$this->num_seguimiento = $num_seguimiento;
	}



	### SETTERS
	
	# Documentacion para el metodo getIdseguimiento 		
	public function getIdseguimiento()
	{
		return($this->idseguimiento);
	}
	# Documentacion para el metodo getClavebeneficiario 		
	public function getClavebeneficiario()
	{
		return($this->clavebeneficiario);
	}
	# Documentacion para el metodo getDmta 		
	public function getDmta()
	{
		return($this->dmta);
	}
	# Documentacion para el metodo getHta 		
	public function getHta()
	{
		return($this->hta);
	}
	# Documentacion para el metodo getTasist 		
	public function getTasist()
	{
		return($this->tasist);
	}
	# Documentacion para el metodo getTadiast 		
	public function getTadiast()
	{
		return($this->tadiast);
	}
	# Documentacion para el metodo getTabaquismo 		
	public function getTabaquismo()
	{
		return($this->tabaquismo);
	}
	# Documentacion para el metodo getColesterol 		
	public function getColesterol()
	{
		return($this->colesterol);
	}
	# Documentacion para el metodo getGlucemia 		
	public function getGlucemia()
	{
		return($this->glucemia);
	}
	# Documentacion para el metodo getPeso 		
	public function getPeso()
	{
		return($this->peso);
	}
	# Documentacion para el metodo getTalla 		
	public function getTalla()
	{
		return($this->talla);
	}
	# Documentacion para el metodo getHba1c 		
	public function getHba1c()
	{
		return($this->hba1c);
	}
	# Documentacion para el metodo getEcg 		
	public function getEcg()
	{
		return($this->ecg);
	}
	# Documentacion para el metodo getFondodeojo 		
	public function getFondodeojo()
	{
		return($this->fondodeojo);
	}
	# Documentacion para el metodo getExamenpie 		
	public function getExamenpie()
	{
		return($this->examenpie);
	}
	# Documentacion para el metodo getMicroalbuminuria 		
	public function getMicroalbuminuria()
	{
		return($this->microalbuminuria);
	}
	# Documentacion para el metodo getHdl 		
	public function getHdl()
	{
		return($this->hdl);
	}
	# Documentacion para el metodo getLdl 		
	public function getLdl()
	{
		return($this->ldl);
	}
	# Documentacion para el metodo getTags 		
	public function getTags()
	{
		return($this->tags);
	}
	# Documentacion para el metodo getImc 		
	public function getImc()
	{
		return($this->imc);
	}
	# Documentacion para el metodo getCreatininemia 		
	public function getCreatininemia()
	{
		return($this->creatininemia);
	}
	# Documentacion para el metodo getInterconsulta_a 		
	public function getInterconsulta_a()
	{
		return($this->interconsulta_a);
	}
	# Documentacion para el metodo getInterconsulta_b 		
	public function getInterconsulta_b()
	{
		return($this->interconsulta_b);
	}
	# Documentacion para el metodo getInterconsulta_c 		
	public function getInterconsulta_c()
	{
		return($this->interconsulta_c);
	}
	# Documentacion para el metodo getInterconsulta_d 		
	public function getInterconsulta_d()
	{
		return($this->interconsulta_d);
	}
	# Documentacion para el metodo getRcvg_anterior 		
	public function getRcvg_anterior()
	{
		return($this->rcvg_anterior);
	}
	# Documentacion para el metodo getRcvg_actual 		
	public function getRcvg_actual()
	{
		return($this->rcvg_actual);
	}
	# Documentacion para el metodo getFecha_seguimiento 		
	public function getFecha_seguimiento()
	{
		return($this->fecha_seguimiento);
	}
	# Documentacion para el metodo getId_medico 		
	public function getId_medico()
	{
		return($this->id_medico);
	}
	# Documentacion para el metodo getId_usuariocarga 		
	public function getId_usuariocarga()
	{
		return($this->id_usuariocarga);
	}
	# Documentacion para el metodo getFecha_carga 		
	public function getFecha_carga()
	{
		return($this->fecha_carga);
	}
	# Documentacion para el metodo getEstado_envio 		
	public function getEstado_envio()
	{
		return($this->estado_envio);
	}
	# Documentacion para el metodo getId_usuariovalidador 		
	public function getId_usuariovalidador()
	{
		return($this->id_usuariovalidador);
	}
	# Documentacion para el metodo getEstado_validacion 		
	public function getEstado_validacion()
	{
		return($this->estado_validacion);
	}
	# Documentacion para el metodo getEfector 		
	public function getEfector()
	{
		return($this->efector);
	}
	# Documentacion para el metodo getNum_seguimiento 		
	public function getNum_seguimiento()
	{
		return($this->num_seguimiento);
	}




	### SQLS
	
	# Documentacion para metodo getSQlInsert
	public function getSQlInsert(){
		$sql = '';
		return($sql);
	}

	# Documentacion para metodo getSQlSelect
	public function getSQlSelect(){
		$sql = "
			SELECT idseguimiento, clavebeneficiario, dmta, hta, tasist, tadiast, 
			       tabaquismo, colesterol, glucemia, peso, talla, hba1c, ecg, fondodeojo, 
			       examenpie, microalbuminuria, hdl, ldl, tags, imc, creatininemia, 
			       interconsulta_a, interconsulta_b, interconsulta_c, interconsulta_d, 
			       rcvg_anterior, rcvg_actual, fecha_seguimiento, id_medico, id_usuariocarga, 
			       fecha_carga, estado_envio, id_usuariovalidador, estado_validacion, 
			       efector, num_seguimiento
			  FROM trazadoras.seguimiento_remediar;

		";
		return($sql);
	}


	# Documentacion para metodo getSQlSelect
	public function getSQlSelectWhere($where){
		$sql = "
			SELECT idseguimiento, clavebeneficiario, dmta, hta, tasist, tadiast, 
			       tabaquismo, colesterol, glucemia, peso, talla, hba1c, ecg, fondodeojo, 
			       examenpie, microalbuminuria, hdl, ldl, tags, imc, creatininemia, 
			       interconsulta_a, interconsulta_b, interconsulta_c, interconsulta_d, 
			       rcvg_anterior, rcvg_actual, fecha_seguimiento, id_medico, id_usuariocarga, 
			       fecha_carga, estado_envio, id_usuariovalidador, estado_validacion, 
			       efector, num_seguimiento
			  FROM trazadoras.seguimiento_remediar;
			  WHERE ".$where."
		";
		return($sql);
	}


	# Documentacion para metodo getSQlUpdate
	public function getSQlUpdate(){
		$sql = '';
		return($sql);
	}

	# Documentacion para metodo getSQlDelete
	public function getSQlDelete(){
		$sql = '';
		return($sql);
	}

}










/**
* 
*/
class Efector

{
	var $cuie = '';
	var $nombreefector = '';
	var $sistema = '';
	var $domicilio = '';
	var $departamento = '';
	var $localidad = '';
	var $cod_pos = '';
	var $ciudad = '';
	var $referente = '';
	var $tel = '';
	var $tipoefector = '';
	var $cod_org = '';
	var $nivel = '';
	var $banco = '';
	var $nrocta = '';


	function __construct()
	{
		
	}

	### Constructores
	
	public function construirResult($result){
		
		$this->cuie = $result->fields['cuie'];
		$this->nombreefector = $result->fields['nombreefector'];
		$this->sistema = $result->fields['sistema'];
		$this->domicilio = $result->fields['domicilio'];
		$this->departamento = $result->fields['departamento'];
		$this->localidad = $result->fields['localidad'];
		$this->cod_pos = $result->fields['cod_pos'];
		$this->ciudad = $result->fields['ciudad'];
		$this->referente = $result->fields['referente'];
		$this->tel = $result->fields['tel'];
		$this->tipoefector = $result->fields['tipoefector'];
		$this->cod_org = $result->fields['cod_org'];
		$this->nivel = $result->fields['nivel'];
		$this->banco = $result->fields['banco'];
		$this->nrocta = $result->fields['nrocta'];
	}


	### GETTERS
	
	# Documentacion para el metodo setCuie 		
	public function setCuie($cuie)
	{
		$this->cuie = $cuie;
	}
	# Documentacion para el metodo setNombreefector 		
	public function setNombreefector($nombreefector)
	{
		$this->nombreefector = $nombreefector;
	}
	# Documentacion para el metodo setSistema 		
	public function setSistema($sistema)
	{
		$this->sistema = $sistema;
	}
	# Documentacion para el metodo setDomicilio 		
	public function setDomicilio($domicilio)
	{
		$this->domicilio = $domicilio;
	}
	# Documentacion para el metodo setDepartamento 		
	public function setDepartamento($departamento)
	{
		$this->departamento = $departamento;
	}
	# Documentacion para el metodo setLocalidad 		
	public function setLocalidad($localidad)
	{
		$this->localidad = $localidad;
	}
	# Documentacion para el metodo setCod_pos 		
	public function setCod_pos($cod_pos)
	{
		$this->cod_pos = $cod_pos;
	}
	# Documentacion para el metodo setCiudad 		
	public function setCiudad($ciudad)
	{
		$this->ciudad = $ciudad;
	}
	# Documentacion para el metodo setReferente 		
	public function setReferente($referente)
	{
		$this->referente = $referente;
	}
	# Documentacion para el metodo setTel 		
	public function setTel($tel)
	{
		$this->tel = $tel;
	}
	# Documentacion para el metodo setTipoefector 		
	public function setTipoefector($tipoefector)
	{
		$this->tipoefector = $tipoefector;
	}
	# Documentacion para el metodo setCod_org 		
	public function setCod_org($cod_org)
	{
		$this->cod_org = $cod_org;
	}
	# Documentacion para el metodo setNivel 		
	public function setNivel($nivel)
	{
		$this->nivel = $nivel;
	}
	# Documentacion para el metodo setBanco 		
	public function setBanco($banco)
	{
		$this->banco = $banco;
	}
	# Documentacion para el metodo setNrocta 		
	public function setNrocta($nrocta)
	{
		$this->nrocta = $nrocta;
	}



	### SETTERS
	
	# Documentacion para el metodo getCuie 		
	public function getCuie()
	{
		return($this->cuie);
	}
	# Documentacion para el metodo getNombreefector 		
	public function getNombreefector()
	{
		return($this->nombreefector);
	}
	# Documentacion para el metodo getSistema 		
	public function getSistema()
	{
		return($this->sistema);
	}
	# Documentacion para el metodo getDomicilio 		
	public function getDomicilio()
	{
		return($this->domicilio);
	}
	# Documentacion para el metodo getDepartamento 		
	public function getDepartamento()
	{
		return($this->departamento);
	}
	# Documentacion para el metodo getLocalidad 		
	public function getLocalidad()
	{
		return($this->localidad);
	}
	# Documentacion para el metodo getCod_pos 		
	public function getCod_pos()
	{
		return($this->cod_pos);
	}
	# Documentacion para el metodo getCiudad 		
	public function getCiudad()
	{
		return($this->ciudad);
	}
	# Documentacion para el metodo getReferente 		
	public function getReferente()
	{
		return($this->referente);
	}
	# Documentacion para el metodo getTel 		
	public function getTel()
	{
		return($this->tel);
	}
	# Documentacion para el metodo getTipoefector 		
	public function getTipoefector()
	{
		return($this->tipoefector);
	}
	# Documentacion para el metodo getCod_org 		
	public function getCod_org()
	{
		return($this->cod_org);
	}
	# Documentacion para el metodo getNivel 		
	public function getNivel()
	{
		return($this->nivel);
	}
	# Documentacion para el metodo getBanco 		
	public function getBanco()
	{
		return($this->banco);
	}
	# Documentacion para el metodo getNrocta 		
	public function getNrocta()
	{
		return($this->nrocta);
	}




	### SQLS
	
	# Documentacion para metodo getSQlInsert
	public function getSQlInsert(){
		$sql = '';
		return($sql);
	}

	# Documentacion para metodo getSQlSelect
	public function getSQlSelect($where){
		if (strlen($where) > 0) {
			$sql = "
				SELECT cuie, nombreefector, sistema, domicilio, departamento, localidad, 
				       cod_pos, ciudad, referente, tel, tipoefector, cod_org, nivel, 
				       banco, nrocta
				  FROM facturacion.smiefectores
				  WHERE ".$where."
			";
		} else {
			$sql = "
				SELECT cuie, nombreefector, sistema, domicilio, departamento, localidad, 
				       cod_pos, ciudad, referente, tel, tipoefector, cod_org, nivel, 
				       banco, nrocta
				  FROM facturacion.smiefectores";
		}
		
		return($sql);
	}

	# Documentacion para metodo getSQlUpdate
	public function getSQlUpdate(){
		$sql = '';
		return($sql);
	}

	# Documentacion para metodo getSQlDelete
	public function getSQlDelete(){
		$sql = '';
		return($sql);
	}


	#	Metodo Automata 		
	public function Automata($where)
	{
		$sql = $this->getSQlSelect($where);
		$result = sql($sql);
		$this->construirResult($result);
	}

	#	Metodo FiltrarCuie 		
	public function FiltrarCuie($cuie)
	{
		$this->Automata("cuie = '".$cuie."'");
	}
		

}



?>