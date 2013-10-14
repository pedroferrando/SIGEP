<?php


# Classes


#doc
#	classname:	BeneficiarioSeguimiento
#	scope:		PUBLIC
#	autor:		Pezzarini Pedro José (Remediar + Redes) 
#	
#	Mantiene los datos del beneficiario para seguimiento,
#	segun estructura de base de datos trazadoras.seguimiento_remediar (Recurrir a documentacion)
#	
#	Funcionamiento: Toma los datos de un array (como $_POST) en el constructor
#	y transfiere los datos al objeto con el metodo construir()
#
#	Ejemplo:
#		$beneficiario = new BeneficiarioSeguimiento($_POST);	// Crea el objeto BeneficiarioSeguimiento
#		$beneficiario->construir();				// Construye la estructura con los datos de $_POST
#		$beneficiario->isBeneficiarioValido();			// Retorna true o false, segun los controles en el metodo
#		$beneficiario->sqlRegistroNuevo();			// Retorna una instruccion sql para el motor de DB
#	
#	Nota: Los campos del formulario html que se reflejan en el array que se pasa al constructor, deben ser los mismos
#	que se utilizan en el metodo construir.
#/doc

class BeneficiarioSeguimiento
{
    #	Registros desde el formulario
    var $registrosFormulario;
	var $registrosBeneficiario;
        

	#	Datos del Beneficiario 
	var $nombre;
	var $apellido;
	var $tipoDoc;
	var $nroDoc;
	var $fechaNacimiento;
	var $sexo;
	# ----------- Domicilio
	var $domicilioCalle;
	var $domicilioNro;
	var $domicilioManzana;
	var $domicilioPiso;
	var $domicilioDepto;
	var $domicilioEntreCalle1;
	var $domicilioEntreCalle2;	
	var $domicilioBarrio;
	var $domicilioMunicipio;
	var $domicilioDepartamento;	// Se refiere al departamento provincial
	var $domicilioLocalidad;
    var $domicilioCodPostal;
    var $domicilioProvincia;
	# ----------- 	
	var $telefono;	
	var $codremediarEfector;	// Puede ser el codigo de remediar o el codigo SIISA del efector


        #	Datos del beneficiario para el seguimiento
        var $idSeguimiento = "NULL";
        var $clavebeneficiario = "NULL";
        var $dmt2 = "NULL";
        var $hta = "NULL";
        var $tasist = "NULL";
        var $tadiast = "NULL";
        var $tabaquismo = "NULL";
        var $colesterol = "NULL";
        var $glucemia = "NULL";
        var $peso = "NULL";
        var $talla = "NULL";
        var $imc = "NULL";
        var $hba1c = "NULL";
        var $ecg = "NULL";
        var $fondoDeOjo = "NULL";
        var $examenDePie = "NULL";
        var $microalbuminuria = "NULL";
        var $hdl = "NULL";
        var $ldl = "NULL";
        var $tags = "NULL";
        var $creatininemia = "NULL";
        var $interconsultas = array("1"=>"NULL", "2"=>"NULL", "3"=>"NULL", "4"=>"NULL");
        var $rcvgAnterior = "NULL";
        var $rcvgActual = "NULL";
        var $fechaSeguimiento = "NULL";
        var $idMedico = "NULL";
        var $idUsuarioCarga = "NULL";
        var $fechaCarga = "NULL";
        var $estadoEnvio = "NULL";
        var $idUsuarioValidador = "NULL";
        var $estadoValidacion = "NULL";
        var $numSeguimiento=1;


	#	Constructor
        function __construct ($cbeneficiario)
        {
                $this->clavebeneficiario = $cbeneficiario;

        }
        ###


        #	Resetea los valores del formulario para generar el objeto
        public function setFormulario($formulario)
        {
                $registrosFormulario = $formulario;
        }


	public function construirBeneficiario($result)
	{
		$this->nombre = $result->fields['nombre'];
		$this->apellido = $result->fields['apellido'];
		$this->tipoDoc = $result->fields['tipo_documento'];
		$this->nroDoc  = $result->fields['numero_doc'];
		$this->fechaNacimiento = $result->fields['fecha_nacimiento'];
		$this->sexo = $result->fields['sexo'];
		$this->domicilioCalle  = $result->fields['calle'];
		$this->domicilioNro = $result->fields['numero_calle'];
		$this->domicilioManzana = $result->fields['manzana'];
		$this->domicilioPiso = $result->fields['piso'];
		$this->domicilioDepto = $result->fields['dpto'];
		$this->domicilioEntreCalle1 = $result->fields['entre_calle_1'];
		$this->domicilioEntreCalle2 = $result->fields['entre_calle_2'];
		$this->domicilioBarrio = $result->fields['barrio'];
		$this->domicilioMunicipio = $result->fields['municipio'];
		$this->domicilioDepartamento = $result->fields['departamento'];
		$this->domicilioLocalidad = $result->fields['localidad'];
		$this->domicilioCodPostal = $result->fields['cod_pos'];
		$this->domicilioProvincia =  "Misiones"; //$result->fields['provincia_nac']; // Estatico por ser r+r misiones
		$this->telefono = $result->fields['telefono'];
		$this->codremediarEfector = $result->fields[''];
	}
        


        #	Construye el objeto mediante los parametros del formulario
        #	Se debe tener en cuenta los campos del formulario.
        public function construirSeguimiento($formulario)
        {	

        	$this->registrosFormulario = $formulario;

        	# TODO:
		# Por alguna razon el bucle foreach toma a la fecha de seguimiento como un campo en blanco.
		$this->fechaSeguimiento = $this->FechaDb($this->registrosFormulario['fecha_seguimiento']);
		$this->clavebeneficiario = $this->registrosFormulario['clavebeneficiario'];
		$this->codremediarEfector = $this->registrosFormulario['efector'];
                $this->idMedico = $this->registrosFormulario['medico'];
                $this->idUsuarioCarga = $this->registrosFormulario['idUsuarioCarga'];
                $this->interconsultas = array(
                        "0"=>$this->registrosFormulario['interconsulta1'], 
                        "1"=>$this->registrosFormulario['interconsulta2'], 
                        "2"=>$this->registrosFormulario['interconsulta3'], 
                        "3"=>$this->registrosFormulario['interconsulta4']
                        );
                
                $this->tasist = $this->registrosFormulario['taSist'];
                $this->tadiast = $this->registrosFormulario['taDiast'];
		#

		if ($this->interconsultas["0"] == 'NOTOSH')
		{
			$this->intercolsultas["0"] = "NULL";
			
		}
		if ($this->interconsultas["1"] == 'NOTOSH')
		{
			$this->intercolsultas["1"] = "NULL";
			
		}
		if ($this->interconsultas["2"] == 'NOTOSH')
		{
			$this->intercolsultas["2"] = "NULL";
			
		}
		if ($this->interconsultas["3"] == 'NOTOSH')
		{
			$this->intercolsultas["3"] = "NULL";	
		}	

		#	Carga los valores del registro en el objeto
		$this->nombre = $this->registrosFormulario['nombre'];
		$this->apellido = $this->registrosFormulario['apellido'];
                $this->dmt2 = $this->registrosFormulario['dmt2'];
                $this->hta = $this->registrosFormulario['hta'];
                $this->tabaquismo = $this->registrosFormulario['tabaquismo'];
                $this->colesterol = $this->registrosFormulario['colesterol'];
                $this->glucemia = $this->registrosFormulario['glucemia'];
                $this->peso = $this->registrosFormulario['peso'];
                $this->talla = $this->registrosFormulario['talla'];
                $this->imc = $this->registrosFormulario['imc'];
                $this->hba1c = $this->registrosFormulario['hba1c'];
                $this->ecg = $this->registrosFormulario['ecg'];
                $this->fondoDeOjo = $this->registrosFormulario['fondoDeOjo'];
                $this->examenDePie = $this->registrosFormulario['examenDePie'];
                $this->microalbuminuria = $this->registrosFormulario['microalbuminuria'];
                $this->hdl = $this->registrosFormulario['hdl'];
                $this->ldl = $this->registrosFormulario['ldl'];
                $this->tags = $this->registrosFormulario['tags'];
                $this->creatininemia = $this->registrosFormulario['creatininemia'];
                $this->rcvgAnterior = $this->registrosFormulario['riesgo_inicial'];
                $this->rcvgActual = $this->registrosFormulario['riesgo_actual'];
                $this->fechaCarga = date("Y-m-d H:i:s");
                $this->estadoEnvio = 0;
                $this->idUsuarioValidador = -1;
                $this->estadoValidacion = 0;	
                
                #	Limpia los valores y los prepara para la sentencia SQL
		if(strlen($this->nombre) < 1){$this->nombre = "NULL";}
                if(strlen($this->apellido) < 1){$this->apellido = "NULL";}
                if(strlen($this->dmt2) < 1){$this->dmt2 = "NULL";}
                if(strlen($this->apellido) < 1){$this->apellido = "NULL";}
                if(strlen($this->tabaquismo) < 1){$this->tabaquismo = "NULL";}
                if(strlen($this->colesterol) < 1){$this->colesterol = "NULL";}
                if(strlen($this->glucemia) < 1){$this->glucemia = "NULL";}
                if(strlen($this->peso) < 1){$this->peso = "NULL";}
                if(strlen($this->talla) < 1){$this->talla = "NULL";}
                if(strlen($this->imc) < 1){$this->imc = "NULL";}
                if(strlen($this->hba1c) < 1){$this->hba1c = "NULL";}
                if(strlen($this->ecg) < 1){$this->ecg = "NULL";}
                if(strlen($this->fondoDeOjo) < 1){$this->fondoDeOjo = "NULL";}
                if(strlen($this->examenDePie) < 1){$this->examenDePie = "NULL";}
                if(strlen($this->microalbuminuria) < 1){$this->microalbuminuria = "NULL";}
                if(strlen($this->hdl) < 1){$this->hdl = "NULL";}
                if(strlen($this->ldl) < 1){$this->ldl = "NULL";}
                if(strlen($this->tags) < 1){$this->tags = "NULL";}
                if(strlen($this->creatininemia) < 1){$this->creatininemia = "NULL";}
                if(strlen($this->rcvgAnterior) < 1){$this->rcvgAnterior = "NULL";}
                if(strlen($this->rcvgActual) < 1){$this->rcvgActual = "NULL";}
                


		#	Cambia los valores "on" del formulario en valores aptos para la estructura
		#	de la base de datos
		
		if($this->dmt2 == "on"){
			$this->dmt2 = 1;	
		}else
		{
			$this->dmt2 = 0;
		}

		if ($this->hta == "on")
		{
			$this->hta = 1;	
		}else
		{
			$this->hta = 0;
		}
                
		if ($this->tabaquismo == "on")
		{
			$this->tabaquismo = 1;
		}
		else
		{
			$this->tabaquismo = 0;
		}

		
		if ($this->ecg == "on")
		{
			$this->ecg = 1;
		}
		else
		{
			$this->ecg = 0;
		}
		
		if ($this->fondoDeOjo == "on")
		{
			$this->fondoDeOjo = 1;
		}
		else
		{
			$this->fondoDeOjo = 0;
		}
		

		
		if ($this->examenDePie == "on")
		{
			$this->examenDePie = 1;
		}
		else
		{
			$this->examenDePie = 0;
		}
		
		###
        }



        #	Verifica si el beneficiario es valido, en este lugar pueden ir todas las reglas de validacion
        #	para eliminar errores de carga del beneficiario.
        public function isBeneficiarioValido()
        {
                $rta=true;
                if ($this->clavebeneficiario == "")
                {
                        $rta = false;
                }

                if ($this->tadiast == "")
                {
                        $rta = false;
                }

                if ($this->tasist == "")
                {
                        $rta = false;
                }

                return($rta);	
        }	


	#	Obtiene un sql de riesgo dependiendo del valor que se registre como parametro
	#	-1 (o sin parametros) retorna un sql que busca al beneficiario en la tabla de seguimiento_remediar
	#	0 como parametro, retorna un sql que busca al beneficiario en la tabla de clasificacion
	
	public function sqlObtenerRiesgo($value = -1)
	{
		switch ($value)
		{
			case -1:
				$sqlSeguimiento = "select UPPER(rcvg_actual) as rcvg, idseguimiento,num_seguimiento from trazadoras.seguimiento_remediar
					where clavebeneficiario = '".$this->clavebeneficiario."'
					order by idseguimiento desc";
				break;
			
			case 0:
				$sqlSeguimiento = "(select UPPER(rcvg) as rcvg from trazadoras.clasificacion_remediar2
					where clave_beneficiario = '".$this->clavebeneficiario."')
					union
					(select UPPER(rcvg) as rcvg from trazadoras.clasificacion_remediar
					where clave = '".$this->clavebeneficiario."')";
			break;
					
			default:
				
			break;
		}
		
		return($sqlSeguimiento);
	}


	#	Verifica si el registro del result contiene mas de un registro
	
	public function isRegistroValido($result)
	{
		$rta = false;

		if ($result->NumRows() > 0)
		{
			$rta = True;
		}

		return($rta);
		
	}


	#	Genera una sentencia SQL para consultar al beneficiario
	public function sqlObtenerBeneficiario()
	{
		$sqlBeneficiario = "select b.tipo_documento,
			b.numero_doc,b.calle,b.numero_calle,b.manzana,b.piso,b.dpto,b.entre_calle_1,b.entre_calle_2,
			b.barrio, b.municipio,b.departamento,b.localidad,b.cod_pos,b.provincia_nac,b.telefono, b.sexo, 
			b.fecha_nacimiento_benef as fecha_nacimiento, b.nombre_benef as nombre, b.apellido_benef as apellido
			from uad.beneficiarios b
			where b.clave_beneficiario = '".$this->clavebeneficiario."'";

			return($sqlBeneficiario);
	}


        #	Genera una sentencia SQL para insertar un beneficiario en la tabla de seguimiento
        public function sqlSeguimientoNuevo()
        {
                $beneficiario_sql = "INSERT INTO trazadoras.seguimiento_remediar(
                            clavebeneficiario, dmta, hta, tasist, tadias, 
                            tabaquismo, colesterol, glucemia, peso, talla, hba1c, ecg, fondodeojo, 
                            examenpie, microalbuminuria, hdl, ldl, tags, imc, creatininemia, interconsulta_a, 
                            interconsulta_b, interconsulta_c, interconsulta_d, rcvg_anterior, 
                            rcvg_actual, fecha_seguimiento, id_medico, id_usuariocarga, fecha_carga, 
                            estado_envio, id_usuariovalidador, estado_validacion, efector, num_seguimiento)

                            VALUES ('".$this->clavebeneficiario."', ".$this->dmt2.", ".$this->hta.", ".$this->tasist.", ".$this->tadiast.", 
                                    ".$this->tabaquismo.", ".$this->colesterol.", ".$this->glucemia.", ".$this->peso.", ".$this->talla.", 
                                    ".$this->hba1c.", ".$this->ecg.", ".$this->fondoDeOjo.", 
                                    ".$this->examenDePie.", ".$this->microalbuminuria.", ".$this->hdl.", ".$this->ldl.", ".$this->tags.", 
                                    ".$this->imc.", ".$this->creatininemia.", ".$this->interconsultas["0"].", 
                                    ".$this->interconsultas["1"].", ".$this->interconsultas["2"].", ".$this->interconsultas["3"].", 
                                    '".$this->rcvgAnterior."', 
                                    '".$this->rcvgActual."', '".$this->fechaSeguimiento."', ".$this->idMedico.", ".$this->idUsuarioCarga.", 
                                    '".$this->fechaCarga."', 
                                    ".$this->estadoEnvio.", ".$this->idUsuarioValidador.", ".$this->estadoValidacion.", '".$this->codremediarEfector."',".$this->numSeguimiento.")

                            RETURNING idseguimiento";

                return($beneficiario_sql);		
        }		





	#	Genera una sentencia SQL para obtener el numero de seguimiento de un beneficiario en la tabla de seguimiento 
	public function sqlNroSeguimiento()
	{
		$sql = "select max(num_seguimiento) as num_seguimiento from trazadoras.seguimiento_remediar 
			where clavebeneficiario = '".$this->clavebeneficiario."'";
		return($sql);
	}






	#	Transforma la decha coloquial en fechas compatibles con la DB
        public function FechaDb($fecha)
        {
        	list($d,$m,$a) = explode("/",$fecha);
	        return "$a-$m-$d";

        }




        ########### GETTERS

	#	Datos Afiliatorios
        public function getTipoDoc(){
	return($this->tipoDoc);}

	public function getNroDoc(){
		return($this->nroDoc);}

	public function getDomicilioCalle(){
		return($this->domicilioCalle);}

	public function getDomicilioNro(){
		return($this->domicilioNro);}

	public function getDomicilioManzana(){
		return($this->domicilioManzana);}

	public function getDomicilioPiso(){
		return($this->domicilioPiso);}

	public function getDomicilioDepto(){
		return($this->domicilioDepto);}

	public function getDomicilioEntreCalle1(){
		return($this->domicilioEntreCalle1);}

	public function getDomicilioEntreCalle2(){
		return($this->domicilioEntreCalle2);}

	public function getDomicilioBarrio(){
		return($this->domicilioBarrio);}

	public function getDomicilioMunicipio(){
		return($this->domicilioMunicipio);}

	public function getDomicilioDepartamento(){
		return($this->domicilioDepartamento);}

	public function getDomicilioLocalidad(){
		return($this->domicilioLocalidad);}

	public function getDomicilioCodPostal(){
		return($this->domicilioCodPostal);}

	public function getDomicilioProvincia(){
		return($this->domicilioProvincia);}

	public function getTelefono(){
		return($this->telefono);}

	public function getFechaNacimiento(){
		return($this->fechaNacimiento);}

	public function getSexo(){
		return($this->sexo);}

	public function getNombre(){
		return($this->nombre);}

	public function getApellido(){
		return($this->apellido);}

	public function getEdad(){
    		list($ano,$mes,$dia) = explode("-",$this->fechaNacimiento);
		$ano_diferencia  = date("Y") - $ano;
		$mes_diferencia = date("m") - $mes;
		$dia_diferencia   = date("d") - $dia;
		if ($dia_diferencia < 0 && $mes_diferencia <= 0)
			$ano_diferencia--;
		return $ano_diferencia;
	}


	#	Datos del seguimiento

	public function getRcvgAnterior()
	{return($this->rcvgAnterior);}

	public function getDmt2(){
	return($this->dmt2);}

	public function getHta(){
	return($this->hta);}

	public function getTaSist(){
	return($this->TaSist);}

	public function getTaDiast(){
	return($this->tadiast);}

	public function getTabaquismo(){
	return($this->tabaquismo);}

	public function getColesterol(){
	return($this->colesterol);}

	public function getGlucosa(){
	return($this->glucosa);}

	public function getPeso(){
	return($this->peso);}

	public function getTalla(){
	return($this->talla);}

	public function getImc(){
	return($this->imc);}

	public function getHba1c(){
	return($this->hba1c);}

	public function getEcg(){
	return($this->ecg);}

	public function getFondoDeOjo(){
	return($this->fondoDeOjo);}

	public function getExamenDePie(){
	return($this->examenDePie);}

	public function getMicroalbuminuria(){
	return($this->microalbuminuria);}

	public function getHdl(){
	return($this->hdl);}

	public function getLdl(){
	return($this->ldl);}

	public function getCreatininemia(){
	return($this->creatininemia);}

	public function getInterconsulta1(){
	return($this->interconsulta["0"]);}

	public function getInterconsulta2(){
	return($this->interconsulta["1"]);}

	public function getInterconsulta3(){
	return($this->interconsulta["2"]);}

	public function getInterconsulta4(){
	return($this->interconsulta["3"]);}

	public function getRcvgActual(){
	return($this->rcvgActual);}

	public function getIdMedico(){
	return($this->idMedico);}

	public function getIdUsuarioCarga(){
	return($this->idUsuarioCarga);}

	public function getFechaCarga(){
	return($this->fechaCarga);}

	public function getEstadoEnvio(){
	return($this->estadoEnvio);}

	public function getIdUsuarioValidador(){
	return($this->idUsuarioValidador);}

	public function getEstadoValidacion(){
	return($this->estadoValidacion);}

	public function getNroSeguimiento(){
	return($this->numSeguimiento);}

	public function getClaveBeneficiario(){
	return($this->clavebeneficiario);}






        ########### SETTERS

	public function setRcvgAnterior($rcvgAnterior)
	{
		$this->rcvgAnterior = $rcvgAnterior;
	}

	public function setNroSeguimiento($numero)
	{
		$this->numSeguimiento = $numero;
	}

}







#	Funciones externa para llamadas

#	Calcula la edad de una persona tomado la fecha de nacimiento y la fecha actual
function calculaedad($fechanacimiento){
    list($ano,$mes,$dia) = explode("-",$fechanacimiento);
    $ano_diferencia  = date("Y") - $ano;
    $mes_diferencia = date("m") - $mes;
    $dia_diferencia   = date("d") - $dia;
    if ($dia_diferencia < 0 && $mes_diferencia <= 0)
        $ano_diferencia--;
    return $ano_diferencia;
}


#	Verifica si se realizo el envio de datos en base al campo "nuevo"
function isNuevo($formulario)
{	
	$rta = false;
	if ($formulario['nuevo'])
	{
		$rta = true;	
	}
	return($rta);
}




?>
		

