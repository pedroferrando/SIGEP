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
* Documentacion para SqlMaker
* Contiene:
* 
*/

class SqlMaker

{
	var $sql = "";
	var $fields = array();

	var $sqlSelects = array();
	var $sqlFroms = array();
	var $sqlWheres = array();
	var $sqlJoins = array();
	var $sqlGroups = array();
	var $sqlOrder = array();

	var $total = 0;
	var $areas = array();
	
	function __construct()
	{
		$this->fields = array();
		$this->sql = "";
		$this->sqlSelects = array();
		$this->sqlFroms = array();
		$this->sqlWheres = array();
		$this->sqlJoins = array();
		$this->sqlGroups = array();
		
	}


	
	#	Documentacion para Metodo getSqlSelect
	public function getSqlSelect()
	{

		$sql = "SELECT ";
		# Select
		for ($i=0; $i < count($this->sqlSelects); $i++) { 
			if ($i == (count($this->sqlSelects) -1) ) {
				$sql .= $this->sqlSelects[$i];
			} else {
				$sql .= $this->sqlSelects[$i].", ";
			}
		}

		$sql .= " FROM ";
		for ($i=0; $i < count($this->sqlFroms); $i++) { 
			if ($i == (count($this->sqlFroms) -1) ) {
				$sql .= $this->sqlFroms[$i];
			} else {
				$sql .= $this->sqlFroms[$i].", ";
			}
		}


		# Joins
		if (count($this->sqlJoins) > 0) {
			foreach ($this->sqlJoins as $join) {
					$sql .= $join." ";
				}	
		}

		# Wheres
		if (count($this->sqlWhere) > 0) {
			foreach ($this->sqlWhere as $where) {
					$sql .= $where;
				}	
		}

		# Group By
		if (count($this->sqlGroups) > 0) {
			$sql .= "GROUP BY ";
			for ($i=0; $i < count($this->sqlGroups); $i++) { 
				if ($i == (count($this->sqlGroups) -1) ) {
					$sql .= $this->sqlGroups[$i]." ";
				}else{
					$sql .= $this->sqlGroups[$i].",";
				}
			}	
		}

		# Order By
		if (count($this->sqlGroups) > 0) {
			$sql .= "ORDER BY ";
			for ($i=0; $i < count($this->sqlOrder); $i++) { 
				if ($i == (count($this->sqlOrder) -1) ) {
					$sql .= $this->sqlOrder[$i]." ";
				}else{
					$sql .= $this->sqlOrder[$i].",";
				}
			}	
		}

		
		return($sql);
	}

	# Documentacion para metodo "SQLToResult" con $sql como parámetros
	public function SQLToResult($sql){
		return(sql($sql));
	}

	# Documentacion para metodo "Sum" con $fields como parámetros
	public function Sum($fields){
		$total = 0;
		foreach ($fields as $key) {
			$total += $key;
		};
		return($total);
	}


	# Documentacion para metodo "SQL" con $sql como parámetros
	public function SQLToFields($result){
		$count = 0;
		if($result){
			$keys = (array_keys($result->fields));
		    $temp = array();
		    for ($i=1; $i < count($keys); $i+=2) { 
	        	$temp[] = $keys[$i];
	    	}
		    $data = $temp;
		}
		else{
			$data = array();
		}

		$this->fields = $data;
		return($this->fields);
	}


	# Documentacion para metodo "SQLGet" con $fields = "*" como parámetros
	public function SQLToArray($sql, $fields = "*"){
		$returnFields = array();
		$result = sql($sql);

		if ($fields == "*") {
			$fields = $this->SQLToFields($result);
		}

		foreach ($fields as $entry) {
			$returnFields[$entry] = array();
		}

		while (!$result->EOF) {
			
			foreach ($fields as $entry) {
				$returnFields[$entry][] = $result->fields[$entry];
			}
			$result->MoveNext();
		}

		return($returnFields);
	}


	# Documentacion para metodo "SQLSelect" con $field, $alias = "" como parámetros
	public function SQLSelect($field, $alias = "", $convertTo = ""){
		if (strlen($alias) > 0) {
			if (strlen($convertTo) > 0) {
				$this->sqlSelects[] = $field."::".$convertTo." as \"".$alias."\"";
			} else {
				$this->sqlSelects[] = $field." as \"".$alias."\"";
			}
		}else{
			if (strlen($convertTo) > 0) {
				$this->sqlSelects[] = $field."::".$convertTo;
			} else {
				$this->sqlSelects[] = $field;
			}
		}
	}

	# Documentacion para metodo "SQLFrom" con $tablename, $alias = "" como parámetros
	public function SQLFrom($tablename, $alias = ""){
		if (strlen($alias) > 0) {
			$this->sqlFroms[] = $tablename." ".$alias;
		} else {
			$this->sqlFroms[] = $tablename;
		}
	}

	# Documentacion para metodo "where" con $field1, $operator, $field2 como parametros
	public function SQLWhere($field1, $operator = "", $field2="", $statement = "AND"){
		if(!(is_array($this->sqlWheres))){
			$this->sqlWheres = array();
		}

		if (count($this->sqlWhere) < 1) {
			$sqlWheres = " WHERE (".$field1." ".$operator." ".$field2.") \n";
		} else {
			$sqlWheres = $statement." (".$field1." ".$operator." ".$field2.") \n";
		}

		$this->sqlWhere[] = $sqlWheres;
		
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

	#	Documentacion para Metodo Lower
	public function SQLLower($field)
	{	$sql = "LOWER (".field.")";
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


	# Documentacion para metodo "SQLGroup" con $field como parámetros
	public function SQLGroup($field){
		$this->sqlGroups[] = $field;
	}

	# Documentacion para metodo "SQLOrder" con $field como parámetros
	public function SQLOrder($field){
		$this->sqlOrder[] = $field;
	}

	# Documentacion para metodo "SQLInnerJoin" con $field como parámetros
	public function SQLInnerJoin($tablename, $alias, $fieldname, $cmp, $fieldname2){
		$sql = "INNER JOIN ".$tablename." ".$alias." on ".$fieldname." ".$cmp." ".$fieldname2;
		$this->sqlJoins[] = $sql;
	}

	# Documentacion para metodo "SQLLeftJoin" con $field como parámetros
	public function SQLLeftJoin($tablename, $alias, $fieldname, $cmp, $fieldname2){
		$sql = "LEFT JOIN ".$tablename." ".$alias." on ".$fieldname." ".$cmp." ".$fieldname2;
		$this->sqlJoins[] = $sql;
	}



}


?>