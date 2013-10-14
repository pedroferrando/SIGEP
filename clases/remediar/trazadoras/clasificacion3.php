<?php 


"Copyright (C) 2013 <Pezzarini Pedro Jose (jose2190@gmail.com)>

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.";


/**
* 
*/
class Clasificacion3
{
	var $id = Null;
	var $clavebeneficiario = Null;
	var $fechaControl = Null;
	var $efector = Null;
	var $diabetesMieliptus = Null;
	var $fumador = Null;
	var $taSistolica = Null;
	var $taDiasistolica = Null;
	var $glucemiaElevadaEmbarazo = Null;
	var $hijoSobrepeso = Null;
	var $medidaAbdominalElevada = Null;
	var $usuarioCarga = Null;
	var $fechaCarga = Null;
	var $idPromotor = Null;
	var $diabetesFamiliar = Null;
	var $riesgo = Null;

	function __construct()
	{
		$this->id = Null;
		$this->clavebeneficiario = Null;
		$this->fechaControl = Null;
		$this->efector = Null;
		$this->diabetesMieliptus = Null;
		$this->fumador = Null;
		$this->taSistolica = Null;
		$this->taDiasistolica = Null;
		$this->glucemiaElevadaEmbarazo = Null;
		$this->hijoSobrepeso = Null;
		$this->medidaAbdominalElevada = Null;
		$this->usuarioCarga = Null;
		$this->fechaCarga = Null;
		$this->idPromotor = Null;
		$this->diabetesFamiliar = Null;
		$this->riesgo = Null;
	}

	# Documentacion para metodo "construirResult" con $result como parámetros
	public function construirResult($result){

		$this->id = $result->fields["id"];
		$this->clavebeneficiario = $result->fields["clavebeneficiario"];
		$this->fechaControl = $result->fields["fecha_control"];
		$this->efector = $result->fields["efector"];
		$this->diabetesMieliptus = $result->fields["diabetes_mieliptus"];
		$this->diabetesFamiliar = $result->fields["diabetes_familiar"];
		$this->fumador = $result->fields["fumador"];
		$this->taSistolica = $result->fields["ta_sistolica"];
		$this->taDiasistolica = $result->fields["ta_diasistolica"];
		$this->glucemiaElevadaEmbarazo = $result->fields["glucemia_elevada_embarazo"];
		$this->hijoSobrepeso = $result->fields["hijo_sobrepeso"];
		$this->medidaAbdominalElevada = $result->fields["medida_abdominal_elevada"];
		$this->usuarioCarga = $result->fields["usuario_carga"];
		$this->fechaCarga = $result->fields["fecha_carga"];
		$this->idPromotor = $result->fields["id_promotor"];
		$this->riesgo = $result->fields["riesgo"];
		
	}

	// Getters
	
	public function getId(){
		return($this->id);
	}
	
	public function getClavebeneficiario(){
		return($this->clavebeneficiario);
	}
	
	public function getFechaControl(){
		return($this->fechaControl);
	}
	
	public function getEfector(){
		return($this->efector);
	}
	
	public function getDiabetesMieliptus(){
		return($this->diabetesMieliptus);
	}
	
	public function getFumador(){
		return($this->fumador);
	}
	
	public function getTaSistolica(){
		return($this->taSistolica);
	}
	
	public function getTaDiasistolica(){
		return($this->taDiasistolica);
	}
	
	public function getGlucemiaElevadaEmbarazo(){
		return($this->glucemiaElevadaEmbarazo);
	}
	
	public function getHijoSobrepeso(){
		return($this->hijoSobrepeso);
	}
	
	public function getMedidaAbdominalElevada(){
		return($this->medidaAbdominalElevada);
	}
	
	public function getUsuarioCarga(){
		return($this->usuarioCarga);
	}
	
	public function getFechaCarga(){
		return($this->fechaCarga);
	}
	
	public function getIdPromotor(){
		return($this->idPromotor);
	}

	public function getDiabetesFamiliar(){
		return($this->diabetesFamiliar);
	}	

	public function getRiesgo(){
		return($this->riesgo);
	}

	// Setters
	
	public function setId($id){
		$this->id = $id;
	}
	
	public function setClavebeneficiario($clavebeneficiario){
		$this->clavebeneficiario = $clavebeneficiario;
	}
	
	public function setFechaControl($fechaControl){
		$this->fechaControl = $fechaControl;
	}
	
	public function setEfector($efector){
		$this->efector = $efector;
	}
	
	public function setDiabetesMieliptus($diabetesMieliptus){
		switch ($diabetesMieliptus) {
			case 'diabetes':
				$this->diabetesMieliptus = 1;
				break;

			case 'sinDiabetes':
				$this->diabetesMieliptus = 0;
				break;
			
			default:
				$this->diabetesMieliptus = 3;
				break;
		}
	}
	
	public function setFumador($fumador){
		switch ($fumador) {
			case 'fumador':
				$this->fumador = 1;
				break;

			case 'noFumador':
				$this->fumador = 0;
				break;
			
			default:
				$this->fumador = 3;
				break;
		}
	}
	
	public function setTaSistolica($taSistolica){
		$this->taSistolica = $taSistolica;
	}
	
	public function setTaDiasistolica($taDiasistolica){
		switch (True) {
			case (strlen($taDiasistolica) < 1):
				$this->taDiasistolica = "NULL";
				break;
			
			case (strlen($taDiasistolica) > 0):
				$this->taDiasistolica = $taDiasistolica;
				break;

			default:
				$this->taDiasistolica = -1;
				break;
		}
		
	}
	
	public function setGlucemiaElevadaEmbarazo($glucemiaElevadaEmbarazo){
		switch ($glucemiaElevadaEmbarazo) {
			case 'SI':
				$this->glucemiaElevadaEmbarazo = 1;
				break;

			case 'NO':
				$this->glucemiaElevadaEmbarazo = 0;
				break;

			case 'NSNC':
				$this->glucemiaElevadaEmbarazo = 3;
				break;
			
			default:
				$this->glucemiaElevadaEmbarazo = -1;
				break;
		}
	}
	
	public function setHijoSobrepeso($hijoSobrepeso){
		switch ($hijoSobrepeso) {
			case 'SI':
				$this->hijoSobrepeso = 1;
				break;

			case 'NO':
				$this->hijoSobrepeso = 0;
				break;

			case 'NSNC':
				$this->hijoSobrepeso = 3;
				break;
			
			default:
				$this->hijoSobrepeso = -1;
				break;
		}
	}
	
	public function setMedidaAbdominalElevada($medidaAbdominalElevada){
		switch ($medidaAbdominalElevada) {
			case 'SI':
				$this->medidaAbdominalElevada = 1;
				break;

			case 'NO':
				$this->medidaAbdominalElevada = 0;
				break;

			case 'NoRealizado':
				$this->medidaAbdominalElevada = 3;
				break;
			
			default:
				$this->medidaAbdominalElevada = -1;
				break;
		}
	}
	
	public function setUsuarioCarga($usuarioCarga){
		$this->usuarioCarga = $usuarioCarga;
	}
	
	public function setFechaCarga($fechaCarga){
		$this->fechaCarga = $fechaCarga;
	}
	
	public function setIdPromotor($idPromotor){
		$this->idPromotor = $idPromotor;
	}

	public function setDiabetesFamiliar($diabetesFamiliar){
		switch ($diabetesFamiliar) {
			case 'SI':
				$this->diabetesFamiliar = 1;
				break;

			case 'NO':
				$this->diabetesFamiliar = 0;
				break;
			
			default:
				$this->diabetesFamiliar = 3;
				break;
		}

	}

	public function setRiesgo($riesgo){
		$this->riesgo = $riesgo;
	}




	// SQL
	public function getSqlSelect($where = ""){
		if (strlen($where) > 1) {
			$sql = "
				SELECT id, clavebeneficiario, fecha_control, efector, diabetes_mieliptus, 
					fumador, ta_sistolica, ta_diasistolica, glucemia_elevada_embarazo, 
					hijo_sobrepeso, medida_abdominal_elevada, usuario_carga, fecha_carga, id_promotor, diabetes_familiar, riesgo
				FROM trazadoras.clasificacion_remediar3
				WHERE ".$where." ;";
		} else {
			$sql = "
				SELECT id, clavebeneficiario, fecha_control, efector, diabetes_mieliptus, 
					fumador, ta_sistolica, ta_diasistolica, glucemia_elevada_embarazo, 
					hijo_sobrepeso, medida_abdominal_elevada, usuario_carga, fecha_carga, id_promotor, diabetes_familiar, riesgo
				FROM trazadoras.clasificacion_remediar3";
		}

		return($sql);
	}

	public function getSqlInsert(){
		$sql = "INSERT INTO trazadoras.clasificacion_remediar3(
            clavebeneficiario, fecha_control, efector, diabetes_mieliptus, 
            fumador, ta_sistolica, ta_diasistolica, diabetes_familiar, glucemia_elevada_embarazo, 
            hijo_sobrepeso, medida_abdominal_elevada, usuario_carga, fecha_carga, 
            id_promotor, riesgo)
    VALUES ('".$this->clavebeneficiario."', '".$this->fechaControl."', '".$this->efector."', ".$this->diabetesMieliptus.", 
            ".$this->fumador.", ".$this->taSistolica.", ".$this->taDiasistolica.", ".$this->diabetesFamiliar." , ".$this->glucemiaElevadaEmbarazo.", 
            ".$this->hijoSobrepeso.", ".$this->medidaAbdominalElevada.", ".$this->usuarioCarga.", '".$this->fechaCarga."', 
            ".$this->idPromotor.", '".$this->riesgo."')
						 RETURNING id;";
		return($sql);
	}

	public function getSqlRemove($where){
		$sql = "";
		return($sql);
	}

	public function getSqlUpdate($where){
		$sql = "";
		return($sql);
	}
	
	public function Automata($where = ""){
		$sql= $this->getSqlSelect($where);
		$result = sql($sql);
		$this->construirResult($result);
	}

	public function enPadron(){
		$rtnValue = false;

		if (strlen($this->clavebeneficiario) > 1) {
			$sql = $this->getSqlSelect("clavebeneficiario = '".$this->clavebeneficiario."'");
			$result = sql($sql);
			
			if ($result->RecordCount() != 0) {
				$rtnValue = True;
			}

		}

		return($rtnValue);
	}


	# Documentacion para metodo "Insertar" con  como parámetros
	public function Insertar(){
		$sql = $this->getSqlInsert();
		$result = sql($sql);
		$this->id = $result->fields["id"];
	}



}






?>