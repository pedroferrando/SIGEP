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

	#	Nro del cuatrimestre que se informa
	var $nroSeguimiento;

	#	Constructor
        function __construct ()
        {

        }
        ###




	public function construirBeneficiario($result)
	{
		$this->clavebeneficiario = $result->fields['clavebeneficiario'];
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
		$this->domicilioProvincia =  20;//"Misiones"; //$result->fields['provincia_nac']; // Estatico por ser r+r misiones
		$this->telefono = $result->fields['telefono'];
		$this->codremediarEfector = $result->fields[''];


		#	Carga los valores del registro en el objeto
		$this->fechaSeguimiento = $result->fields['fecha_seguimiento'];
		$this->clavebeneficiario = $result->fields['clavebeneficiario'];
		$this->codremediarEfector = $result->fields['efector'];
		$this->numSeguimiento = $result->fields['num_seguimiento'];


		$this->idSeguimiento = $result->fields['idseguimiento'];
		$this->nombre = $result->fields['nombre'];
		$this->apellido = $result->fields['apellido'];
                $this->dmt2 = $result->fields['dmt2'];
                $this->hta = $result->fields['hta'];
                $this->tasist = $result->fields['tasist'];
                $this->tadiast = $result->fields['tadiast'];
                $this->tabaquismo = $result->fields['tabaquismo'];
                $this->colesterol = $result->fields['colesterol'];
                $this->glucemia = $result->fields['glucemia'];
                $this->peso = $result->fields['peso'];
                $this->talla = $result->fields['talla'];
                $this->imc = $result->fields['imc'];
                $this->hba1c = $result->fields['hba1c'];
                $this->ecg = $result->fields['ecg'];
                $this->fondoDeOjo = $result->fields['fondodeojo'];
                $this->examenDePie = $result->fields['examendePie'];
                $this->microalbuminuria = $result->fields['microalbuminuria'];
                $this->hdl = $result->fields['hdl'];
                $this->ldl = $result->fields['ldl'];
                $this->tags = $result->fields['tags'];
                $this->creatininemia = $result->fields['creatininemia'];
                $this->interconsultas = array(
                        "0"=>$result->fields['interconsulta_a'], 
                        "1"=>$result->fields['interconsulta_b'], 
                        "2"=>$result->fields['interconsulta_c'], 
                        "3"=>$result->fields['interconsulta_d']
                        );
                $this->rcvgAnterior = $result->fields['rcvg_anterior'];
                $this->rcvgActual = $result->fields['rcvg_actual'];
                $this->idMedico = $result->fields['id_medico'];
                $this->idUsuarioCarga = $result->fields['id_usuariocarga'];
                $this->fechaCarga = $result->fields['fecha_carga'];
                $this->estadoEnvio = $result->fields['estado_envio'];
                $this->idUsuarioValidador = $result->fields['id_usuariovalidador'];
                $this->estadoValidacion = $result->fields['estado_validacion'];	
                


		#	Cambia los valores "on" del formulario en valores aptos para la estructura
		#	de la base de datos
		
		if($this->dmt2 == "1"){
			$this->dmt2 = "S";	
		}else
		{
			$this->dmt2 = "N";
		}

		if ($this->hta == "1")
		{
			$this->hta = "S";	
		}else
		{
			$this->hta = "N";
		}
                
		if ($this->tabaquismo == "1")
		{
			$this->tabaquismo = "S";
		}
		else
		{
			$this->tabaquismo = "N";
		}

		
		if ($this->ecg == "1")
		{
			$this->ecg = "S";
		}
		else
		{
			$this->ecg = "N";
		}
		
		if ($this->fondoDeOjo == "1")
		{
			$this->fondoDeOjo = "S";
		}
		else
		{
			$this->fondoDeOjo = "N";
		}
		

		
		if ($this->examenDePie == "1")
		{
			$this->examenDePie = "S";
		}
		else
		{
			$this->examenDePie = "N";
		}
		
		###

                #   Corrige los formatos de los campos
                $this->corregirFormato();
	}
        
        
                #       Corrige los formatos de los campos para adaptarlos a los requerimientos de nación.
        public function corregirFormato(){
            
            
            switch ($this->rcvgAnterior) {
                case "MODE":
                    $this->rcvgAnterior = "MODE";
                    break;
                
                case "MODERAD":
                    $this->rcvgAnterior = "MODE";
                    break;
                
                
                case "MODERADO":
                    $this->rcvgAnterior = "MODE";
                    break;

                case "MALTO":
                    $this->rcvgAnterior = "MUYALTO";
                    break;
                
                default:
                    break;
            }
            
            
        }


        #	Verifica si el beneficiario es valido, en este lugar pueden ir todas las reglas de validacion
        #	para eliminar errores de carga del beneficiario.
        public function isBeneficiarioValido()
        {

                if ($this->idSeguimiento == 1 && $this->rcvgAnterior == "BAJO")
                {
                	 $rta = false;
                	
                }

                return($rta);	
        }	



	#	Verifica el numero de registros
	public function isRegistroValido($result)
	{
		$rta = false;

		if ($result->NumRows() > 0)
		{
			$rta = True;
		}

		return($rta);
		
	}

		
	#	Transforma la fecha en formato DB
        public function FechaDb($fecha)
        {
        	list($d,$m,$a) = explode("/",$fecha);
	        return "$a-$m-$d";

        }

	public function isSeguimientoValido()
	{
		$retorno = false;
		$numSeg = $this->numSeguimiento + 0;
		
		if ($numSeg < 7)
		{
			$retono = true;
			
		}else{

		echo $numSeg;
		}

		return($retorno);
	}



        ########### GETTERS

	#	Datos Afiliatorios
	public function getClaveBeneficiario()
	{
		return($this->clavebeneficiario);
	}
	
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
	return($this->tasist);}

	public function getTaDiast(){
	return($this->tadiast);}

	public function getTabaquismo(){
	return($this->tabaquismo);}

	public function getColesterol(){
	return($this->colesterol);}

	public function getGlucosa(){
	return($this->glucemia);}

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
	return($this->interconsultas["0"]);}

	public function getInterconsulta2(){
	return($this->interconsultas["1"]);}

	public function getInterconsulta3(){
	return($this->interconsultas["2"]);}

	public function getInterconsulta4(){
	return($this->interconsultas["3"]);}

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

	public function getTags()
	{
		return($this->tags);
	}

	public function getEfector()
	{
		return($this->codremediarEfector);
	}

	public function getFechaSeguimiento()
	{
		return($this->fechaSeguimiento);
	}

	public function getNroSeguimiento(){
	return($this->numSeguimiento);}

        ########### SETTERS

	public function setRcvgAnterior($rcvgAnterior)
	{
		$this->rcvgAnterior = $rcvgAnterior;
	}

	public function setNroSeguimiento($numSeguimiento)
	{
		$this->nroSeguimiento = $numSeguimiento;
	}


        ########### SQLS
	public function sqlEstadoEnviado()
	{
		$retorno = "update trazadoras.seguimiento_remediar
			SET estado_envio = 1 where idseguimiento = ".$this->idSeguimiento."";
		return($retorno);
	}
        
}



#####################################33

#	Clase de generacion de archivo S

#doc
#	classname:	ArchivoSeguimiento
#	scope:		PUBLIC
#
#/doc

class ArchivoSeguimiento
{
	#	internal variables
	var $cantidadDeRegistros;

	var $lineas;
	
	var $header;			// **
	var $generacionFecha;
	var $generacionUsuario;
	var $generacionCodProvincia;
	var $generacionSecuenciaArchivo;
	var $generacionTipoRegistro;	// **

	// DATOS DEL BENEFICIARIO
	
	var $generacionTrailer;
	var $generacionPeriodo;
	
	
	
	#	Constructor
	function __construct ()
	{	
		$this->cantidadDeRegistros=-1;
		$this->lineas = array();
		$this->header = '"H";';			
		$this->generacionFecha = date("Y-m-d");
		$this->generacionUsuario;
		$this->generacionCodProvincia = 20;
		$this->generacionSecuenciaArchivo;
		$this->generacionTipoRegistro = '"D";';	
		$this->generacionTrailer = '"T";';
		$this->generacionPeriodo = "";
	
	}
	###	




	
	public function agregarBeneficiario($beneficiario)
	{
		$this->cantidadDeRegistros +=1;
		
		$linea .= $this->generacionTipoRegistro;
		$linea .= '"'.$beneficiario->getTipoDoc().'";';
		$linea .= '"'.$beneficiario->getNroDoc().'";';
		$linea .= $beneficiario->getNroSeguimiento().";";
		$linea .= '"'.$beneficiario->getDomicilioCalle().'";';
		$linea .= '"'.$beneficiario->getDomicilioNro().'";';
		$linea .= '"'.$beneficiario->getDomicilioManzana().'";';
                $linea .= '"'.$beneficiario->getDomicilioPiso.'";';
		$linea .= '"'.$beneficiario->getDomicilioDepto().'";';
		$linea .= '"'.$beneficiario->getDomicilioEntreCalle1().'";';
		$linea .= '"'.$beneficiario->getDomicilioEntreCalle2().'";';
		$linea .= '"'.$beneficiario->getDomicilioBarrio().'";';
		$linea .= '"'.$beneficiario->getDomicilioMunicipio().'";';
		$linea .= '"'.$beneficiario->getDomicilioDepartamento().'";';
		$linea .= '"'.$beneficiario->getDomicilioLocalidad().'";';
		$linea .= '"'.$beneficiario->getDomicilioCodPostal().'";';
		$linea .= '"'.$beneficiario->getDomicilioProvincia().'";'; // ** Revisar para encontrar errores de codigo de provincia
		$linea .= '"'.$beneficiario->getTelefono().'";';
		$linea .= '"'.$beneficiario->getEfector().'";';

		// DATOS DEL SEGUIMIENTO
		$linea .= '"'.$beneficiario->getDmt2().'";';
		$linea .= '"'.$beneficiario->getHta().'";';
		$linea .= $beneficiario->getTaSist().";";
		$linea .= $beneficiario->getTaDiast().";";
		$linea .= '"'.$beneficiario->getTabaquismo().'";';
		$linea .= $beneficiario->getColesterol().";";
		$linea .= $beneficiario->getGlucosa().";";
		$linea .= $beneficiario->getPeso().";";
		$linea .= $beneficiario->getTalla().";";
		$linea .= $beneficiario->getImc().";";
		$linea .= $beneficiario->getHba1c().";";
		$linea .= '"'.$beneficiario->getEcg().'";';
		$linea .= '"'.$beneficiario->getFondoDeOjo().'";';
		$linea .= '"'.$beneficiario->getExamenDePie().'";';
		$linea .= $beneficiario->getMicroalbuminuria().";";
		$linea .= $beneficiario->getHdl().";";
		$linea .= $beneficiario->getLdl().";";
		$linea .= $beneficiario->getTags().";";
		$linea .= $beneficiario->getCreatininemia().";";
		$linea .= $beneficiario->getInterconsulta1().";";
		$linea .= $beneficiario->getInterconsulta2().";";
		$linea .= $beneficiario->getInterconsulta3().";";
		$linea .= $beneficiario->getInterconsulta4().";";
		$linea .= '"'.$beneficiario->getRcvgAnterior().'";';
		$linea .= '"'.$beneficiario->getRcvgActual().'";';
		$linea .= $beneficiario->getFechaSeguimiento().";";
		$linea .= PHP_EOL;

		$this->lineas[$this->cantidadDeRegistros] = $linea;
		
	}





	#	GETTERS  ########################
	public function getLineasArchivo()
	{
		$retorno = $this->header;
		$retorno .= $this->generacionFecha.";";
		$retorno .= '"'.$this->generacionUsuario.'";';
		$retorno .= "20;";
		$retorno .= $this->generacionPeriodo.";";
		$retorno .= PHP_EOL;
		for ($i = 0; $i < count($this->lineas) -1; $i++)
		{
			$retorno .= $this->lineas[$i];
			
		}
		$retorno .= $this->generacionTrailer;
		$retorno .= $this->cantidadDeRegistros.';';

		return($retorno);
	}


	#	Prototipo del archivo, el nombre del archivo que se envia a nacion.
	public function getProtoripoArchivo()
	{
		$prototipo = "./archivos/S".$this->generacionCodProvincia.$this->generacionPeriodo.".txt";
		return($prototipo);
	}




	
	#	SETTERS  ########################
	public function setUsuario($usuario)
	{
		$this->generacionUsuario = $usuario;
	}

	public function setPeriodo($periodo)
	{
		$this->generacionPeriodo = $periodo;
	}


	
	
}
###



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
