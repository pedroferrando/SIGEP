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
class EfectorRemediar
{
	var $nacer = Null;
	var $remediar = Null;
	var $sisa = Null;

	var $EFECTOR = Null;


	function __construct()
	{
		$this->EFECTOR = new Efector();
		
	}


	Public function ConstruirResult($result){
		$this->nacer = $result->fields["nacer"];
		$this->remediar = $result->fields["remediar"];
		$this->sisa = $result->fields["sisa"];

		$where = "cuie = '".$this->nacer."'";
		$this->EFECTOR->Automata($where);
	}

	// Getters
	Public function getNacer(){
		return($this->nacer);
	}
	Public function getRemediar(){
		return($this->remediar);
	}
	Public function getSisa(){
		return($this->sisa);
	}

	public function getNombreFix($longitud = 10){
		$presentacion = substr($this->EFECTOR->getNombreefector(), 0, $longitud);
		return($presentacion);
	}

	// Setter
	public function setNacer($nacer){
		$this->nacer = $nacer;
	}
	public function setRemediar($remediar){
		$this->remediar = $remediar;
	}
	public function setSisa($sisa){
		$this->sisa = $sisa;
	}


	public function getSqlSelectNacer($cuie){
			$sql = "select 
					cuie as nacer,
					(select codremediar from general.relacioncodigos where cuie = efe.cuie) as remediar,
					(select codigosisa from general.relacioncodigos where cuie = efe.cuie) as sisa

					from facturacion.smiefectores efe
					where ARRAY[efe.cuie]  <@ (select ARRAY_AGG(cuie::text) from general.relacioncodigos 
											where codremediar is not null
											and trim(codremediar) <> ''
											and codigosisa is not null)
					AND efe.cuie = '".$cuie."'; ";
			return($sql);
			
	}

	public function getSqlSelectRemediar($codremediar){
			$sql = "select 
					cuie as nacer,
					(select codremediar from general.relacioncodigos where cuie = efe.cuie) as remediar,
					(select codigosisa from general.relacioncodigos where cuie = efe.cuie) as sisa

					from facturacion.smiefectores efe
					where ARRAY[efe.cuie]  <@ (select ARRAY_AGG(cuie::text) from general.relacioncodigos 
											where codremediar is not null
											and trim(codremediar) <> ''
											and codigosisa is not null
											and codremediar = '".$codremediar."')";
			return($sql);
			
	}

	public function getSqlSelectSisa($codigosisa){
			$sql = "select 
					cuie as nacer,
					(select codremediar from general.relacioncodigos where cuie = efe.cuie) as remediar,
					(select codigosisa from general.relacioncodigos where cuie = efe.cuie) as sisa

					from facturacion.smiefectores efe
					where ARRAY[efe.cuie]  <@ (select ARRAY_AGG(cuie::text) from general.relacioncodigos 
											where codremediar is not null
											and trim(codremediar) <> ''
											and codigosisa is not null
											and codigosisa = '".$codigosisa."')";
			return($sql);
			
	}

	public function getSqlSelect(){
		$sql = "select 
					cuie as nacer,
					(select codremediar from general.relacioncodigos where cuie = efe.cuie) as remediar,
					(select codigosisa from general.relacioncodigos where cuie = efe.cuie) as sisa

					from facturacion.smiefectores efe
					where ARRAY[efe.cuie]  <@ (select ARRAY_AGG(cuie::text) from general.relacioncodigos 
											where codremediar is not null
											and trim(codremediar) <> ''
											and codigosisa is not null)";
		return($sql);
	}


	Public function Automata($id, $tipo){
		$sql = $this->AutomataSql($id, $tipo);
		if (strlen($sql) > 1) {
			$result = sql($sql);
			$this->ConstruirResult($result);
		}
	}

	Public function AutomataSql($id, $tipo){
		switch ($tipo) {
			case 'remediar':
				$sql = $this->getSqlSelectRemediar($id);
				break;
			
			case 'nacer':
				$sql = $this->getSqlSelectNacer($id);
				break;

			case 'sisa':
				$sql = $this->getSqlSelectSisa($id);
				break;

			default:
				break;
		}


		return($sql);
	}


}



/**
* 
*/
class EfectorRemediarColeccion
{
	var $efectores = array();

	function __construct()
	{
		
	}

	Public function Automata($id="", $tipo=""){
		$this->efectores = array();
		if (strlen($id) > 1) {
			$sql = EfectorRemediar::AutomataSql($id, $tipo);
		} else {
			$sql = EfectorRemediar::getSqlSelect();
		}
		
		$result = sql($sql);

		while (!$result->EOF) {
			$x = new EfectorRemediar();
			$x->Automata($result->fields['remediar'], "remediar");
			$this->efectores[] = $x;
			
			$result->MoveNext();
		}
	}
}




?>