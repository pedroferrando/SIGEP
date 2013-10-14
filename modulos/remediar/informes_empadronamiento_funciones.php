<?php 

# Configuracion y acceso al sistema
require_once ("../../config.php");

# Zonas Sanitarias
require_once ("../../clases/remediar/informes/EmpadronamientoZonas.php");

# Areas Programaticas
require_once ("../../clases/remediar/informes/EmpadronamientoAreas.php");

# Efectores
require_once ("../../clases/remediar/informes/EmpadronamientoEfectores.php");


$clave = $_POST["clave"];
$elemento = $_POST["elementId"];
DatosExternos($clave, $elemento);

function DatosExternos($clave, $elemento){

	switch ($clave) {
		case "localidades":
			$sql = "select 
						nombre as value,
						nombre as name
						from uad.localidades
						group by nombre
						order by nombre";
							
			$result = sql($sql);
			$datosJson = Result2Json($result);
			$datosJson[] = array("element"=> $elemento);
			print_r(json_encode($datosJson));
			break;

		case "areasprogramaticas":
			$sql = "select
					 id_area_programatica as value, nombre as name
					 from general.areas_programaticas";
			$result = sql($sql);
			$datosJson = Result2Json($result);
			$datosJson[] = array("element"=> $elemento);
			print_r(json_encode($datosJson));
			break;

		case "zonassantinarias":
			$sql = "select 
					id_zona_sani as value, 
					nombre_zona as name
					from nacer.zona_sani";
			$result = sql($sql);
			$datosJson = Result2Json($result);
			$datosJson[] = array("element"=> $elemento);
			print_r(json_encode($datosJson));
			break;

		case "efectores":
			$sql = "select rl.cuie as value, rl.codremediar, 
					(select nombreefector from facturacion.smiefectores where cuie = rl.cuie) as name
					from general.relacioncodigos rl
					where rl.codremediar is not null
					and trim(rl.codremediar) <> ''
					order by 3";
			$result = sql($sql);
			$datosJson = Result2Json($result);
			$datosJson[] = array("element"=> $elemento);
			print_r(json_encode($datosJson));
			break;

		case "estadoenvio":
			$sql = "select distinct(enviado) as value, upper(enviado) as name  from uad.remediar_x_beneficiario";
			$result = sql($sql);
			$datosJson = Result2Json($result);
			$datosJson[] = array("element"=> $elemento);
			print_r(json_encode($datosJson));
			break;
		

		case 'loadData':
			#print_r($_POST);
			getDatas();
			break;


		default:
			echo "TEST";
			break;
	}

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





function getDatas(){
	$ZonasSanitarias = new EmpadronamientoZonasSanitarias();
	$AreasProgramaticas = new EmpadronamientoAreasrProgramaticas();
	$Efectores = new EmpadronamientoEfectores();
	foreach (array_keys($_POST["filtroValor"]) as $valor) {
		$ZonasSanitarias->FilterMultiple($valor, "=", $_POST["filtroValor"][$valor]);
		$AreasProgramaticas->FilterMultiple($valor, "=", $_POST["filtroValor"][$valor]);
		$Efectores->FilterMultiple($valor, "=", $_POST["filtroValor"][$valor]);
		#print_r($_POST["filtroValor"][$valor]);
		
	}

		#echo $ZonasSanitarias->SQLMAKER->getSqlSelect();

		$ZonasSanitarias->getDBData();
		$AreasProgramaticas->getDBData();
		$Efectores->getDBData();

		$r = array();
		$r["Zonas"] = $ZonasSanitarias->zonas;
		$r["Areas"] = $AreasProgramaticas->areas;
		$r["Efectores"] = $Efectores->efectores;

		print_r(json_encode($r));
		


}




?>