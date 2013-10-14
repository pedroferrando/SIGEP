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

# Configuracion y acceso al sistema
require_once ("../../config.php");

/**
* 
*/
class Promotores
{
	var $idPromotor = null;
	var $nombre = null;
	var $apellido = null;
	var $dni = null;
	var $fechaNac = null;
	var $idLocalidad = null;
	var $idEfector = null;
	var $telefono = null;
	var $email = null;
	var $idBanco = null;
	var $nroCuenta = null;
	var $idTipocuenta = null;
	
	function __construct()
	{
		
		
	}

	Public function ConstruirResult($result){
		$this->idPromotor = $result->fields["idpromotor"];
		$this->nombre = $result->fields["nombre"];
		$this->apellido = $result->fields["apellido"];
		$this->dni = $result->fields["dni"];
		$this->fechaNac = $result->fields["fechanac"];
		$this->idLocalidad = $result->fields["idlocalidad"];
		$this->idEfector = $result->fields["idefector"];
		$this->telefono = $result->fields["telefono"];
		$this->email = $result->fields["email"];
		$this->idBanco = $result->fields["idbanco"];
		$this->nroCuenta = $result->fields["nrocuenta"];
		$this->idTipocuenta = $result->fields["idtipocuenta"];

	}

	// Getters
	Public function getIdPromotor(){
			return($this->idPromotor);
	}
	Public function getNombre(){
			return($this->nombre);
	}
	Public function getApellido(){
			return($this->apellido);
	}
	Public function getDni(){
			return($this->dni);
	}
	Public function getFechaNac(){
			return($this->fechaNac);
	}
	Public function getIdLocalidad(){
			return($this->idLocalidad);
	}
	Public function getIdEfector(){
			return($this->idEfector);
	}
	Public function getTelefono(){
			return($this->telefono);
	}
	Public function getEmail(){
			return($this->email);
	}
	Public function getIdBanco(){
			return($this->idBanco);
	}
	Public function getNroCuenta(){
			return($this->nroCuenta);
	}
	Public function getIdTipocuenta(){
			return($this->idTipocuenta);
	}

	// Getters Presentacion
	Public function getNombreCompleto(){
		$presentacion = $this->apellido . ", " . $this->nombre;
		return($presentacion);
	}



	// Setters
	public function setIdPromotor($idPromotor){
			$this->idPromotor = $idPromotor;
	}
	public function setNombre($nombre){
			$this->nombre = $nombre;
	}
	public function setApellido($apellido){
			$this->apellido = $apellido;
	}
	public function setDni($dni){
			$this->dni = $dni;
	}
	public function setFechaNac($fechaNac){
			$this->fechaNac = $fechaNac;
	}
	public function setIdLocalidad($idLocalidad){
			$this->idLocalidad = $idLocalidad;
	}
	public function setIdEfector($idEfector){
			$this->idEfector = $idEfector;
	}
	public function setTelefono($telefono){
			$this->telefono = $telefono;
	}
	public function setEmail($email){
			$this->email = $email;
	}
	public function setIdBanco($idBanco){
			$this->idBanco = $idBanco;
	}
	public function setNroCuenta($nroCuenta){
			$this->nroCuenta = $nroCuenta;
	}
	public function setIdTipocuenta($idTipocuenta){
			$this->idTipocuenta = $idTipocuenta;
	}


	// GetSQL Select
	Public function getSqlSelect($where = ""){
		if (strlen($where) > 1) {
			$sql = "SELECT idpromotor, UPPER(nombre) as nombre, UPPER(apellido) as apellido, dni, fechanac, idlocalidad, idefector, telefono, email, idbanco, nrocuenta, idtipocuenta
									FROM remediar.promotores
									WHERE ".$where." 
									ORDER BY apellido;";
		} else {
			$sql = "SELECT idpromotor, UPPER(nombre) as nombre, UPPER(apellido) as apellido, dni, fechanac, idlocalidad, idefector, telefono, email, idbanco, nrocuenta, idtipocuenta
									FROM remediar.promotores
									ORDER BY apellido ;";
		}	
		return($sql);
	}


	//!TODO
	// GetSQL Update
	Public function getSqlUpdate(){
		return("");
	}

	//!TODO
	// GetSQL Remove
	Public function getSqlRemove(){
		return("");
	}

	//!TODO
	// GETSQL Insert
	Public function getSqlInsert(){
		return("");
	}



}






/**
* 
*/
class PromotoresColeccion
{
	var $promotores = array();

	function __construct()
	{
		
	}


	Public function Automata($where = ""){

		$this->promotores = array();

		$sqlPromotores = Promotores::getSqlSelect($where);

		$results = sql($sqlPromotores);

		while (!$results->EOF) {
			$x = new Promotores();
			$x->ConstruirResult($results);
			$this->promotores[] = $x;
			$results->MoveNext();
		}	
	}



}


?>










