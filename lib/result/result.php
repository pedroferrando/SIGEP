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


# Funciones sobre results


# Retorna todos los campos de un result en formato array
#	array(
#			[0] => "field0",
#			[1] => "field1",
#			.
#			.
#			.
#			[N] => "fieldN"
#		)
function Result2Fields($result){
	if($result){
		$keys = (array_keys($result->fields));
		if (!$result->EOF) {
		    $temp = array();
		    for ($i=1; $i < count($keys); $i+=2) { 
				$temp[] = $keys[$i];
		    }
		}
	}

	return($temp);
}




?>