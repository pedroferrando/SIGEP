<?php 



"Copyright (C) 2013 <Luis Mirabete (littlebacklash@gmail.com)>, <Pezzarini Pedro Jose (jose2190@gmail.com)> 

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
class Efector

{
	var $cuie = '';
	var $nombreefector = '';
	var $sistema = '';
	var $domicilio = '';
	var $departamento = '';
        var $departamentoNombre = '';
	var $localidad = '';
	var $localidadNombre = '';
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
	# Documentacion para el metodo setDepartamento 		
	public function setDepartamentoNombre($departamentoNombre)
	{
		$this->departamentoNombre = $departamentoNombre;
	}
	# Documentacion para el metodo setLocalidad 		
	public function setLocalidad($localidad)
	{
		$this->localidad = $localidad;
	}
	# Documentacion para el metodo setLocalidad 		
	public function setLocalidadNombre($localidadNombre)
	{
		$this->localidadNombre = $localidadNombre;
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
	# Documentacion para el metodo getDepartamento 		
	public function getDepartamentoNombre()
	{
		return($this->departamentoNombre);
	}
	# Documentacion para el metodo getLocalidad 		
	public function getLocalidad()
	{
		return($this->localidad);
	}
	# Documentacion para el metodo getLocalidad 		
	public function getLocalidadNombre()
	{
		return($this->localidadNombre);
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
        
    public function getZonaSanitaria(){
        $sql = "SELECT zs.*
                FROM facturacion.smiefectores efec 
                JOIN uad.localidades loc ON efec.localidad=loc.id_localidad 
                                         AND efec.departamento=loc.id_departamento
                JOIN nacer.zona_sani zs ON CAST(loc.nrozona as integer)=zs.id_zona_sani 
                WHERE efec.cuie='".$this->getCuie()."'";
        $result = sql($sql);
        if($result->RecordCount()>0){
           $result->MoveFirst() ;
           $zona_sanitaria = $result->fields['nombre_zona'];
        }else{
            $zona_sanitaria = false;
        }
        return $zona_sanitaria;
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

    public function getSQLDepartamentoLocalidad($cuie){
        $sql = "SELECT d.nombre AS departamento, l.nombre AS localidad 
                FROM facturacion.smiefectores e 
                JOIN uad.departamentos d ON e.departamento=d.id_departamento 
                JOIN uad.localidades l ON d.id_departamento=l.id_departamento
                WHERE e.cuie='$cuie'
                  AND e.localidad=l.id_localidad";
        return $sql;
    }
        
    #	Metodo loadDepartamentoLocalidad
    public function loadDepartamentoLocalidad(){
            $sql = $this->getSQLDepartamentoLocalidad($this->getCuie());
            $result = sql($sql);
            $this->setDepartamentoNombre($result->fields['departamento']);
            $this->setLocalidadNombre($result->fields['localidad']);
    }

	#	Metodo Automata 		
	public function Automata($where)
	{
		$sql = $this->getSQlSelect($where);
		$result = sql($sql);
		$this->construirResult($result);
        $this->loadDepartamentoLocalidad();
	}

	#	Metodo FiltrarCuie 		
	public function FiltrarCuie($cuie)
	{
		$this->Automata("cuie = '".$cuie."'");
	}        
		

}



?>