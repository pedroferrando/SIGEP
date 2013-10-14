<?php 


/**
* 
*/
class SQLMaker
{
	$fields = array();
	$wheres = array();
	$joins = array();
	$groups = array();
	$froms = array();

	function __construct(argument)
	{
			
	}



	# Documentacion para metodo "where" con $field1, $operator, $field2 como parametros
	public function SQLWhere($field1, $operator = "", $field2="", $statement = "AND"){
		if(!(is_array($this->wheres))){
			$this->wheres = array();
		}

		if (count($this->wheres) < 1) {
			$sqlWheres = "WHERE (".$field1." ".$operator." ".$field2.") \n";
		} else {
			$sqlWheres = $statement." (".$field1." ".$operator." ".$field2.") \n";
		}

		$this->wheres[] = $sqlWheres;
	}

	#	Documentacion para Metodo Between
	public function SQLBetween($limit1, $limit2)
	{	$sql = "BETWEEN ".$limit1." AND ".$limit2;
		return($sql);
	}

	#	Documentacion para Metodo Upper
	public function SQLUpper($field)
	{	$sql = "UPPER (".field.")";
		return($sql);
	}

	#	Documentacion para Metodo Upper
	public function SQLLower($field)
	{	$sql = "LOWER (".field.")";
		return($sql);
	}


	#	Documentacion para Metodo Concat
	public function SQLConcat($fields)
	{
		$sql = "";
		if (count($fields) > 0) {
			for ($i=0; $i < count($fields); $i++) { 
				if($i+1 == count($fields)){
					$sql .= $fields[$i];
				}else{
					$sql .= $fields[$i]." ||";
				}
			}
		}
		return($sql);
	}

	# Documentacion para metodo "SQLCount" con $field = "*" como parámetros
	public function SQLCount($field = "*", $as = ""){
		$sql = "COUNT(".$field.")";
		if (strlen($as) > 0) {
			$sql .= " AS ".$as;
		}
		return($sql);
	}

	# Documentacion para metodo "SQLCast" con $fieldname, $type como parámetros
	public function SQLCast($fieldname, $type){
		$sql = $fieldname."::".$type;
		return($sql);
	}

	# Documentacion para metodo "getSQL" con  como parámetros
	public function getSQL(){
		$sql = ""
		return(NULL);
	}


}


?>