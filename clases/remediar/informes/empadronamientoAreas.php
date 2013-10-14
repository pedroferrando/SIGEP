<?php 

# Clase clasename.php
require_once ("../../clases/Utilidades/sqlMaker.php");

/**
* 
*/
class EmpadronamientoAreasrProgramaticas
{
	var $total = 0;
	var $areas = array();
	var $SQLMAKER = null;

	var $filters = array();

	
	function __construct()
	{
		$this->total = 0;
		$this->areas = array();
		$this->SQLMAKER = null;

		$this->filters = array();
		
		$this->initSql();
		$this->initFilters();
	}


	# Documentacion para metodo "initSql" con  como parámetros
	public function initSql(){
		$this->SQLMAKER = new SqlMaker();
		$this->SQLMAKER->SQLSelect("count(*)", "total");
		$this->SQLMAKER->SQLSelect("ap.id_area_programatica", "nroArea");

		$this->SQLMAKER->SQLFrom("uad.remediar_x_beneficiario rb ");
		
		$this->SQLMAKER->SQLLeftJoin("remediar.formulario","form","rb.nroformulario","=","form.nroformulario");
		$this->SQLMAKER->SQLLeftJoin("facturacion.smiefectores","efe","efe.cuie","=","form.centro_inscriptor");
		$this->SQLMAKER->SQLLeftJoin("general.relacioncodigos","rl","rl.cuie","=","form.centro_inscriptor");

		$this->SQLMAKER->SQLInnerJoin("uad.beneficiarios","b","rb.clavebeneficiario","=","b.clave_beneficiario");
		$this->SQLMAKER->SQLInnerJoin("uad.localidades","loc","b.localidad","=","loc.nombre");
		$this->SQLMAKER->SQLInnerJoin("general.areas_programaticas","ap","ap.id_area_programatica","=","loc.id_areaprogramatica");

		$this->SQLMAKER->SQLWhere("rl.codremediar","is not","null");
		$this->SQLMAKER->SQLWhere("trim(rl.codremediar)","<>","''");

		$this->SQLMAKER->SQLGroup("ap.id_area_programatica");

		$this->SQLMAKER->SQLOrder("ap.id_area_programatica");
	}

	# Documentacion para metodo "initFilters" con  como parámetros
	public function initFilters(){
		
		$this->filters["localidad"] = array(
			'field' => "loc.nombre", 
			'operator' => 'OR', 
			'coverVar' => "'",
			'typeArgument' => 'single');
	
		$this->filters["nroZona"] = array(
			'field' => "loc.nrozona", 
			'operator' => 'OR', 
			'coverVar' => "'",
			'typeArgument' => 'single');
		
		$this->filters["areaProgramatica"] = array(
			'field' => "ap.id_area_programatica", 
			'operator' => 'OR', 
			'coverVar' => "",
			'typeArgument' => 'single');

		$this->filters["fechaNacimiento"] = array(
			'field' => "b.fecha_nacimiento_benef", 
			'operator' => 'AND', 
			'coverVar' => "'",
			'typeArgument' => 'array');
		
		$this->filters["efector"] = array(
			'field' => "form.centro_inscriptor", 
			'operator' => 'OR', 
			'coverVar' => "'",
			'typeArgument' => 'single');
		
		$this->filters["enviado"] = array(
			'field' => "rb.enviado", 
			'operator' => 'OR', 
			'coverVar' => "'",
			'typeArgument' => 'single');

		$this->filters["scoreIgual"] = array(
			'field' => "form.puntaje_final", 
			'operator' => 'AND', 
			'coverVar' => "",
			'typeArgument' => 'single');
	}


	public function getToChartLabel(){		
		
		if (count($this->areas) > 0 ) {
			$labels = "[";

			for ($i=0; $i < count($this->areas); $i++) { 
				if ($i == (count($this->areas) - 1)) {
					$labels .= "'".$this->areas[$i][0]."']";
				}else{
					$labels .= "'".$this->areas[$i][0]."', ";
				}
			}

		} else {
			$labels = "['Consulta Vacia']";
		}
		
		
		return($labels);

	}

	public function getToChartValues(){		
		if (count($this->areas) > 0 ) {
			$values = "[";

			for ($i=0; $i < count($this->areas); $i++) { 
				if ($i == (count($this->areas) - 1)) {
					$values .= $this->areas[$i][1]."]";
				}else{
					$values .= $this->areas[$i][1].", ";
				}
			}
		} else {
			$values = "[0]";
		}
		

		return($values);

	}


	# Documentacion para metodo "getSql" con  como parámetros
	public function Filter($field, $condition, $value){

		$validateField = $this->filters[$field]["field"];

		if (strlen($validateField) > 0) {
			$this->SQLMAKER->SQLWhere($validateField, $condition, $value);
		} else {
			return(false);
		}
	}

	# Documentacion para metodo "FilterMultiple" con  como parámetros
	public function FilterMultiple($field, $condition, $values){

		$validateField = $this->filters[$field]["field"];
		$statement = $this->filters[$field]["operator"];
		$coverVar = $this->filters[$field]["coverVar"];
		$typeArgument = $this->filters[$field]["typeArgument"];

		$arrayArgs = array();


		if (strlen($validateField) > 0) {
			$firsIterator = true;
			foreach ($values as $entry) {
				if ($typeArgument == 'array') {
					$arrayArgs[] = $coverVar.$entry.$coverVar;
				} else {
					if ($firsIterator) {
						$this->SQLMAKER->SQLWhere($validateField, $condition, ($coverVar.$entry.$coverVar), "AND");
						$firsIterator = false;
					} else {
						$this->SQLMAKER->SQLWhere($validateField, $condition, ($coverVar.$entry.$coverVar), $statement);		
					}
				}
				
			}
			
			if (count($arrayArgs) > 0) {
				$this->SQLMAKER->SQLWhere($validateField, $this->SQLMAKER->SQLBetween($arrayArgs[0], $arrayArgs[1]));
			}
			
		} else {
			return(false);
		}
	}	


	# Documentacion para metodo "getDBData" con  como parámetros
	public function getDBData(){
		$result = $this->SQLMAKER->SQLToResult($this->SQLMAKER->getSqlSelect());
		
		while (!$result->EOF) {
			$this->total +=$result->fields["total"];
			$this->areas[] = array($result->fields["nroArea"], $result->fields["total"]);
			$result->MoveNext();
		}

		
	}




}

?>