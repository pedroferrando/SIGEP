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

# Clase remediar/remediar.php
require_once ("../../clases/remediar/remediar.php");

# Clase remediar/trazadoras/clasificacion3.php
require_once ("../../clases/remediar/trazadoras/clasificacion3.php");


$transaccion = $_POST["transaccion"];
$datos = $_POST["datos"];


switch ($transaccion) {
	case 'insertar':
	
		$clasificacion = new Clasificacion3();
		$clavebeneficiario = $_POST["datos"]["claveBeneficiario"];
		$fechaControl = fecha_db($_POST["requeridos"]["fechas"]["prestacion"]);
		$efector = $_POST["requeridos"]["efector"];
		$diabetesMieliptus = $_POST["requeridos"]["diabetes"];
		$fumador = $_POST["requeridos"]["fumador"];
		$taSistolica = $_POST["requeridos"]["presionSistolica"];
		$medidaAbdominalElevada = $_POST["encuestaGeneral"]["perimetroAbdominal"];
		$usuarioCarga = $_POST["datos"]["usuarioCarga"];
		$idPromotor = $_POST["requeridos"]["promotor"];
		$fechaCarga = date("Y-m-d");

		$diabetesFamiliar = $_POST["encuestaGeneral"]["familiarDiabetes"];
		$hijoSobrepeso = $_POST["encuestaMujeres"]["hijoSobrepeso"];
		$glucemiaElevadaEmbarazo = $_POST["encuestaMujeres"]["glucemiaEnEmbarazo"];
		$taDiasistolica = "";//$_POST["datos"]["taDiasistolica"];
		$riesgo = $_POST["requeridos"]["riesgoBeneficiario"];
		
		switch ($riesgo) {
			case 'A':
				# Bajo
				$riesgo = "bajo";
				break;
			case 'B':
				# Moderado
				$riesgo = "moderado";
				break;

			case 'C':
				# Alto
				$riesgo = "alto";
				break;

			case 'D':
				# Muy Alto
				$riesgo = "muyAlto";
				break;

			case 'E':
				# Muy Alto
				$riesgo = "muyAlto";
				break;
			default:
				# code...
				break;
		}



				# Manejo del beneficiario
		/* Se utiliza para verificar si un beneficiario fué empadronado o clasificado anteriormente. */
		$empadronamiento = new Empadronamiento();
		$empadronamiento->Automata("clavebeneficiario = '".$clavebeneficiario."'");

		$clasificacionAntigua = new Clasificacion1();
		$clasificacionAntigua->Automata("clave = '".$clavebeneficiario."'");

		$clasificacionMedia = new Clasificacion2();
		$clasificacionMedia->Automata("clave_beneficiario = '".$clavebeneficiario."'");

		$clasificacionNueva = new Clasificacion3();
		$clasificacionNueva->Automata("clavebeneficiario = '".$clavebeneficiario."'");
		/* */





		$clasificacion->setClavebeneficiario($clavebeneficiario);
		$clasificacion->setFechaControl($fechaControl);
		$clasificacion->setEfector($efector);
		
		$clasificacion->setDiabetesMieliptus($diabetesMieliptus);
		$clasificacion->setFumador($fumador);
		
		$clasificacion->setTaSistolica($taSistolica);
		$clasificacion->setTaDiasistolica($taDiasistolica);
		
		$clasificacion->setDiabetesFamiliar($diabetesFamiliar);
		$clasificacion->setGlucemiaElevadaEmbarazo($glucemiaElevadaEmbarazo);
		$clasificacion->setHijoSobrepeso($hijoSobrepeso);
		$clasificacion->setMedidaAbdominalElevada($medidaAbdominalElevada);

		$clasificacion->setUsuarioCarga($usuarioCarga);
		$clasificacion->setFechaCarga($fechaCarga);
		$clasificacion->setIdPromotor($idPromotor);
		$clasificacion->setRiesgo($riesgo);

		$dataResponse = array();
		if ($empadronamiento->enPadron()) {
			if ($clasificacionAntigua->enPadron() or $clasificacionMedia->enPadron() or $clasificacionNueva->enPadron()) {
				$dataResponse["transaccion"] = 0;
				$dataResponse["beneficiario"] = "Empadronamiento Existente";
				$dataResponse["clasificacion"] = "Clasificacion Existente";
			} else {
				$clasificacion->Insertar();
				$empadronamiento->setNroformulario($clasificacion->getId());
				$empadronamiento->setCompuesto(1);
				$empadronamiento->Actualizar();

				$dataResponse["transaccion"] = 1;
				$dataResponse["beneficiario"] = "Empadronamiento Existente";
				$dataResponse["clasificacion"] = "Nueva Clasificacion Nro: R-CSC-" . $clasificacion->getId();
			}
			
		} else {
			$clasificacion->Insertar();
			$empadronamiento->setNroformulario($clasificacion->getId());
			$empadronamiento->setFechaempadronamiento($clasificacion->getFechaControl());
			$empadronamiento->setClavebeneficiario($clasificacion->getClavebeneficiario());
			$empadronamiento->setUsuario_carga($clasificacion->getUsuarioCarga());
			$empadronamiento->setFecha_carga($clasificacion->getFechaCarga());
			$empadronamiento->setEnviado('n');
			$empadronamiento->setCompuesto(1);

			$empadronamiento->Insertar();
			
			$dataResponse["transaccion"] = 1;
			$dataResponse["beneficiario"] = "Nuevo Empadronamiento (Numero interno ".$clasificacion->getClavebeneficiario().")";
			$dataResponse["clasificacion"] = "Nueva Clasificacion Nro R-CSC-" . $clasificacion->getId();
			$dataResponse["datos"] = "Resumen del beneficiario:";

		}

		echo json_encode($dataResponse);

		break;

	case 'promotores':
			$result = sql($sqlPromotores);
			$datosJson = Result2Json($result);
			echo json_encode($datosJson);
		break;
	
	default:
		# code...
		break;
}



function Result2Json($result){
	$data = array();
	if($result){
		$keys = (array_keys($result->fields));
		while (!$result->EOF) {
		    $temp = array();
		    for ($i=1; $i < count($keys); $i+=2) { 
				$temp[$keys[$i]] = $result->fields[$keys[$i]];
		    }
		    $data[] = $temp;
		    $result->MoveNext();
		}
	}

	return($data);
}

?>