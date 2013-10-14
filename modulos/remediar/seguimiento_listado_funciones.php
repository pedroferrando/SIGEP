<?php
#   Archivo de configuracion del sistema
require_once("../../config.php");

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
        
        #   Datos de la inscripcion
        var $inscripcionScoreRiesgo;
        var $inscripcionApellidoPromotor;
        var $inscripcionNombrePromotor;
        var $inscripcionDniPromotor;
        var $inscripcionCentroInscriptor;
        var $inscripcionCentroInscriptorCuie;
        var $inscripcionFechaEmpadronamiento;
        
	# ----------- 	
	var $telefono;	
	var $codremediarEfector;	// Puede ser el codigo de remediar o el codigo SIISA del efector

        
        #   Datos de la clasificacion
        var $clasificacionDmt;
        var $clasificacionTaSist;
        var $clasificacionTaDiast;
        var $clasificacionColTot;
        var $clasificacionObesidad;
        var $clasificacionTabaquismo;
        var $clasificacionHta;
        var $clasificacionRcvg;
        var $clasificacionNro;
        var $clasificacionEfector;
        var $clasificacionEfectorCod;
        var $clasificacionMedicoNombre;
        var $clasificacionMedicoApellido;
        var $clasificacionFechaControl;
        

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
        var $interconsultasEspecialidades = array("1"=>"NULL", "2"=>"NULL", "3"=>"NULL", "4"=>"NULL");
        var $rcvgAnterior = "NULL";
        var $rcvgActual = "NULL";
        var $fechaSeguimiento = "NULL";
        var $idMedico = "NULL";
        var $MedicoNombre = "";
        var $MedicoApellido = "";
        var $idUsuarioCarga = "NULL";
        var $fechaCarga = "NULL";
        var $estadoEnvio = "NULL";
        var $idUsuarioValidador = "NULL";
        var $estadoValidacion = "NULL";
        var $numSeguimiento=1;
        var $efectorCod = "";
        var $codEfectorNombre = "";
        

	#	Constructor
        function __construct ($cbeneficiario)
        {
                $this->clavebeneficiario = $cbeneficiario;

        }
        ###

        #       Construye el beneficiario mediante la consulta retornada por el
        #       método sqlObtenerBeneficiario()
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
                
                $this->inscripcionScoreRiesgo = $result->fields['scoreempadronamiento'];
                $this->inscripcionApellidoPromotor = $result->fields['apellidoagente'];
                $this->inscripcionNombrePromotor = $result->fields['nombreagente'];
                $this->inscripcionDniPromotor = $result->fields['dni_agente'];
                $this->inscripcionCentroInscriptor = $result->fields['centro_inscriptor'];
                $this->inscripcionCentroInscriptorCuie = $result->fields['centro_inscriptor_cuie'];
                $this->inscripcionFechaEmpadronamiento = $result->fields['fechaempadronamiento'];
                
	}
        


        #	Construye el objeto mediante los parametros del formulario
        #	Se debe tener en cuenta los campos del formulario.
        public function construirSeguimiento($result)
        {	
        	$this->registrosFormulario = $result;

        	# TODO:
		# Por alguna razon el bucle foreach toma a la fecha de seguimiento como un campo en blanco.
		$this->fechaSeguimiento = $this->FechaDb($this->registrosFormulario->fields['fecha_seguimiento']);
		$this->clavebeneficiario = $this->registrosFormulario->fields['clavebeneficiario'];
		$this->codremediarEfector = $this->registrosFormulario->fields['efector'];
                $this->idMedico = $this->registrosFormulario->fields['medico'];
                $this->idUsuarioCarga = $this->registrosFormulario->fields['idUsuarioCarga'];
                $this->interconsultas = array(
                        "0"=>$this->registrosFormulario->fields['interconsulta_a'], 
                        "1"=>$this->registrosFormulario->fields['interconsulta_b'], 
                        "2"=>$this->registrosFormulario->fields['interconsulta_c'], 
                        "3"=>$this->registrosFormulario->fields['interconsulta_d']
                        );
                
                $this->interconsultasEspecialidades = array(
                        "0"=>$this->registrosFormulario->fields['interconsulta_a_especialidad'], 
                        "1"=>$this->registrosFormulario->fields['interconsulta_b_especialidad'], 
                        "2"=>$this->registrosFormulario->fields['interconsulta_c_especialidad'], 
                        "3"=>$this->registrosFormulario->fields['interconsulta_d_especialidad']
                        );
		
                
		#	Carga los valores del registro en el objeto
                $this->numSeguimiento = $this->registrosFormulario->fields['num_seguimiento'];		
                $this->dmt2 = $this->registrosFormulario->fields['dmt2'];
                $this->hta = $this->registrosFormulario->fields['hta'];
                $this->tasist = $this->registrosFormulario->fields['tasist'];
                $this->tadiast = $this->registrosFormulario->fields['tadiast'];
                $this->tabaquismo = $this->registrosFormulario->fields['tabaquismo'];
                $this->colesterol = $this->registrosFormulario->fields['colesterol'];
                $this->glucemia = $this->registrosFormulario->fields['glucemia'];
                $this->peso = $this->registrosFormulario->fields['peso'];
                $this->talla = $this->registrosFormulario->fields['talla'];
                $this->imc = $this->registrosFormulario->fields['imc'];
                $this->hba1c = $this->registrosFormulario->fields['hba1c'];
                $this->ecg = $this->registrosFormulario->fields['ecg'];
                $this->fondoDeOjo = $this->registrosFormulario->fields['fondoDeOjo'];
                $this->examenDePie = $this->registrosFormulario->fields['examenDePie'];
                $this->microalbuminuria = $this->registrosFormulario->fields['microalbuminuria'];
                $this->hdl = $this->registrosFormulario->fields['hdl'];
                $this->ldl = $this->registrosFormulario->fields['ldl'];
                $this->tags = $this->registrosFormulario->fields['tags'];
                $this->creatininemia = $this->registrosFormulario->fields['creatininemia'];
                $this->rcvgAnterior = $this->registrosFormulario->fields['rcvg_anterior'];
                $this->rcvgActual = $this->registrosFormulario->fields['rcvg_actual'];
                $this->fechaCarga = date("Y-m-d H:i:s");
                $this->estadoEnvio = 0;
                $this->idUsuarioValidador = -1;
                $this->estadoValidacion = 0;
                
                #   Datos del efector
                $this->efectorCod = $this->registrosFormulario->fields['efector'];
                $this->codEfectorNombre = $this->registrosFormulario->fields['nombreefector'];
                
                #   Datos del medico
                $this->MedicoNombre = $this->registrosFormulario->fields['nombre_medico'];
                $this->MedicoApellido = $this->registrosFormulario->fields['apellido_medico'];
                
                #   Valores de interconsultas
                if (strlen($this->interconsultas["0"])< 1){$this->intercolsultas["0"] = "Ninguna";}
		if (strlen($this->interconsultas["1"])< 1){$this->intercolsultas["1"] = "Ninguna";}
		if (strlen($this->interconsultas["2"])< 1){$this->intercolsultas["2"] = "Ninguna";}
		if (strlen($this->interconsultas["3"])< 1){$this->intercolsultas["3"] = "Ninguna";}
                
                #   Especialidades de interconsultas
                if (strlen($this->interconsultasEspecialidades["0"])< 1){$this->intercolsultasEspecialidades["0"] = "Ninguna";}
		if (strlen($this->interconsultasEspecialidades["1"])< 1){$this->intercolsultasEspecialidades["1"] = "Ninguna";}
		if (strlen($this->interconsultasEspecialidades["2"])< 1){$this->intercolsultasEspecialidades["2"] = "Ninguna";}
		if (strlen($this->interconsultasEspecialidades["3"])< 1){$this->intercolsultasEspecialidades["3"] = "Ninguna";}


		#	Cambia los valores "on" del formulario en valores aptos para la estructura
		#	de la base de datos
		
		if($this->dmt2 == "on"){$this->dmt2 = "Si";}
                    else{$this->dmt2 = "No";}

		if ($this->hta == "on"){$this->hta = "Si";}
                    else{$this->hta = "No";}
                
		if ($this->tabaquismo == "on"){$this->tabaquismo = "Si";}
                    else{$this->tabaquismo = "No controlado";}
	
		if ($this->ecg == "on"){$this->ecg = "Si";}
                    else{$this->ecg = "No controlado";}
		
		if ($this->fondoDeOjo == "on"){$this->fondoDeOjo = "Si";}
                    else{$this->fondoDeOjo = "No controlado";}

		if ($this->examenDePie == "on"){$this->examenDePie = "Si";}
                    else{$this->examenDePie = "No controlado";}
	
                if (strlen($this->peso) == 0 ){$this->peso = "No controlado";}
                
                if (strlen($this->talla) == 0 ){$this->talla = "No controlado";}
                
                if (strlen($this->tasist) == 0 ){$this->tasist = "No controlado";}

                if (strlen($this->tadiast) == 0 ){$this->tadiast = "No controlado";}
                
                if (strlen($this->colesterol) == 0 ){$this->colesterol = "No controlado";}
                
                if (strlen($this->glucemia) == 0 ){$this->glucemia = "No controlado";}
                
                if (strlen($this->imc) == 0 ){$this->imc = "No controlado";}
                
                if (strlen($this->hba1c) == 0 ){$this->hba1c = "No controlado";}
                
                if (strlen($this->microalbuminuria) == 0 ){$this->microalbuminuria = "No controlado";}
                
                if (strlen($this->hdl) == 0 ){$this->hdl = "No controlado";}
                
                if (strlen($this->ldl) == 0 ){$this->ldl = "No controlado";}
                
                if (strlen($this->tags) == 0 ){$this->tags = "No controlado";}
                
                if (strlen($this->creatininemia) == 0 ){$this->creatininemia = "No controlado";}
                
                switch ($this->rcvgActual) {
                    case "mode":
                        $this->rcvgActual = "MODERADO";
                        break;
                    
                    case "bajo":
                        $this->rcvgActual = "BAJO";
                        break;
                    
                    case "alto":
                        $this->rcvgActual = "ALTO";
                        break;
                    
                    case "muyalto":
                        $this->rcvgActual = "MUY ALTO";
                        break;
                    
                    default:
                        break;
                }
                
                switch ($this->rcvgAnterior) {
                    case "mode":
                        $this->rcvgAnterior = "MODERADO";
                        break;
                    
                    case "MODE":
                        $this->rcvgAnterior = "MODERADO";
                        break;
                    
                    case "MODERAD":
                        $this->rcvgAnterior = "MODERADO";
                        break;

                    case "ALTO":
                        $this->rcvgAnterior = "ALTO";
                        break;
                    
                    case "alto":
                        $this->rcvgAnterior = "ALTO";
                        break;
                    
                    case "MALTO":
                        $this->rcvgAnterior = "MUY ALTO";
                        break;
                    
                    case "malto":
                        $this->rcvgAnterior = "MUY ALTO";
                        break;
                    
                    case "bajo":
                        $this->rcvgAnterior = "BAJO";
                        break;
                    
                    case "BAJO":
                        $this->rcvgAnterior = "BAJO";
                        break;
                    default:
                        break;
                }
                
		###
        }

        public function construirClasificacion($result)
        {
            $this->clasificacionDmt = $result->fields['dmt'];
            $this->clasificacionTaSist = $result->fields['ta_sist'];
            $this->clasificacionTaDiast = $result->fields['ta_diast'];
            $this->clasificacionColTot = $result->fields['col_tot'];
            $this->clasificacionObesidad = $result->fields['obesi'];
            $this->clasificacionTabaquismo = $result->fields['tabaquismo'];
            $this->clasificacionHta = $result->fields['hta'];
            $this->clasificacionRcvg = $result->fields['rcvg']; 
            
            $this->clasificacionNro = $result->fields['nro_clasificacion'];
            $this->clasificacionEfector = $result->fields['nombreefector'];
            $this->clasificacionEfectorCod = $result->fields['codremediar'];
            $this->clasificacionMedicoNombre = $result->fields['nombre_medico'];
            $this->clasificacionMedicoApellido = $result->fields['apellido_medico'];
            $this->clasificacionFechaControl = $result->fields['fecha_control'];
            
            #   Filtrado de DMT
            if (strlen($this->clasificacionDmt) < 1 | $this->clasificacionDmt == "0") {
                $this->clasificacionDmt = "";
            }
            
            
            #   Filtrado de TA SIST
            if (strlen($this->clasificacionTaSist) < 1 | $this->clasificacionTaSist == "0") {
                $this->clasificacionTaSist = "";
            }
            
            #   Filtrado de TA DIAST
            if (strlen($this->clasificacionTaDiast) < 1 | $this->clasificacionTaDiast == "0") {
                $this->clasificacionTaDiast = "";
            }
            
            #   Filtrado de OBESIDAD
            if (strlen($this->clasificacionObesidad) < 1 | $this->clasificacionObesidad == "0") {
                $this->clasificacionObesidad = "";
            }
            
            #   Filtrado de TABAQUISMO
            if (strlen($this->clasificacionTabaquismo) < 1 | $this->clasificacionTabaquismo == "0") {
                $this->clasificacionTabaquismo = "";
            }
            
            #   Filtrado de HTA
            if (strlen($this->clasificacionHta) < 1 | $this->clasificacionHta == "0") {
                $this->clasificacionHta = "";
            }
            
            #   Filtrado de COLESTEROL
            if (strlen($this->clasificacionColTot) < 1 | $this->clasificacionColTot == "0") {
                $this->clasificacionColTot = "";
            }
            
            
        
            switch ($this->clasificacionRcvg) {
                        case "mode":
                            $this->clasificacionRcvg = "MODERADO";
                            break;

                        case "MODE":
                            $this->clasificacionRcvg = "MODERADO";
                            break;

                        case "MODERAD":
                            $this->clasificacionRcvg = "MODERADO";
                            break;

                        case "ALTO":
                            $this->clasificacionRcvg = "ALTO";
                            break;

                        case "alto":
                            $this->clasificacionRcvg = "ALTO";
                            break;

                        case "MALTO":
                            $this->clasificacionRcvg = "MUY ALTO";
                            break;

                        case "malto":
                            $this->clasificacionRcvg = "MUY ALTO";
                            break;

                        case "bajo":
                            $this->clasificacionRcvg = "BAJO";
                            break;

                        case "BAJO":
                            $this->clasificacionRcvg = "BAJO";
                            break;
                        default:
                            break;
                    }
        }
        
        public function getResumenClasificacion()
        {
            $clasificacionInforme = array();
            
            #   Filtrado de DMT
            if (strlen($this->clasificacionDmt) > 1) {
                $clasificacionInforme["DMT"] = "DMT tipo ".$this->clasificacionDmt;
            }
            
            
            #   Filtrado de TA SIST
            if (strlen($this->clasificacionTaSist) > 1) {
                $clasificacionInforme["TASIST"] = "TA Sistolica de ".$this->clasificacionTaSist;
            }
            
            #   Filtrado de TA DIAST
            if (strlen($this->clasificacionTaDiast) > 1) {
                $clasificacionInforme["TADIAST"] = "TA Diastolica de ".$this->clasificacionTaDiast;
            }
            
            #   Filtrado de OBESIDAD
            if (strlen($this->clasificacionObesidad) > 1) {
                $clasificacionInforme["OBESIDAD"] = "Obesidad Notable";
            }
            
            #   Filtrado de TABAQUISMO
            if (strlen($this->clasificacionTabaquismo) > 1) {
                $clasificacionInforme["TABAQUISMO"] = "Tabaquismo";
            }
            
            #   Filtrado de HTA
            if (strlen($this->clasificacionHta) > 1) {
                $clasificacionInforme["HTA"] = "Hipertension Arterial";
            }
            
            #   Filtrado de COLESTEROL
            if (strlen($this->clasificacionColTot) > 1) {
                $clasificacionInforme["COLESTEROL"] = "Colesterol de ".$this->clasificacionColTot;
            }
            
            #   Riesgo
            $clasificacionInforme["RCVG"] = "Riesgo Cardiovascular ".$this->clasificacionRcvg ;
            
            
            return($clasificacionInforme);
            
            
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
				$sqlSeguimiento = "select UPPER(rcvg_actual) as rcvg, idseguimiento,num_seguimiento 
                                        from trazadoras.seguimiento_remediar
					where clavebeneficiario = '".$this->clavebeneficiario."'
					order by idseguimiento desc";
				break;
			
			case 0:
				$sqlSeguimiento = "(select UPPER(rcvg) as rcvg 
                                        from trazadoras.clasificacion_remediar2
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
		$sqlBeneficiario = "select rb.fechaempadronamiento,efe.nombreefector as centro_inscriptor, rl.codremediar as centro_inscriptor_cuie ,form.apellidoagente,
                                        form.nombreagente,form.dni_agente,form.puntaje_final as scoreempadronamiento,b.tipo_documento,
                                        b.numero_doc,b.calle,b.numero_calle,b.manzana,b.piso,b.dpto,b.entre_calle_1,b.entre_calle_2,
                                        b.barrio, b.municipio,b.departamento,b.localidad,b.cod_pos,b.provincia_nac,b.telefono, b.sexo, 
                                        b.fecha_nacimiento_benef as fecha_nacimiento, b.nombre_benef as nombre, b.apellido_benef as apellido
                                        from uad.beneficiarios b
                                            inner join uad.remediar_x_beneficiario rb on rb.clavebeneficiario = b.clave_beneficiario
                                            inner join remediar.formulario form on rb.nroformulario = form.nroformulario
                                            inner join general.relacioncodigos rl on rl.cuie = form.centro_inscriptor
                                            left join facturacion.smiefectores efe on efe.cuie = form.centro_inscriptor
                                            where b.clave_beneficiario = '".$this->clavebeneficiario."'";

			return($sqlBeneficiario);
	}

        #       Genera una sentencia SQL para obtener los datos del seguimiento
        #       del beneficiario
        public function sqlObtenerSeguimiento()
        {
            $sql = "select s.*,
                        (select upper(especialidad) from remediar.interconsultas where valor = s.interconsulta_a) as interconsulta_a_especialidad,
                        (select upper(especialidad) from remediar.interconsultas where valor = s.interconsulta_b) as interconsulta_b_especialidad,
                        (select upper(especialidad) from remediar.interconsultas where valor = s.interconsulta_c) as interconsulta_c_especialidad,
                        (select upper(especialidad) from remediar.interconsultas where valor = s.interconsulta_d) as interconsulta_d_especialidad,
                        efe.nombreefector,
                        med.apellido_medico, 
                        med.nombre_medico
                             from trazadoras.seguimiento_remediar s
                             left join general.relacioncodigos rl on rl.codremediar = s.efector
                             left join facturacion.smiefectores efe on efe.cuie = rl.cuie
                             left join planillas.medicos med on med.id_medico = s.id_medico
                             where s.clavebeneficiario = '".$this->clavebeneficiario."'
                             order by s.num_seguimiento ";
            
            return($sql);
        }

        #       Genera una sentencia SQL para obtener los datos de la clasificacion
        #       del beneficiario
        public function sqlObtenerClasificacion()
        {
            $sql = "(select c2.dmt, c2.ta_sist, c2.ta_diast, c2.col_tot,  c2.obesi,  c2.tabaquismo, c2.hta, c2.rcvg, 
                    efe.nombreefector, rl.codremediar, med.nombre_medico, med.apellido_medico, c2.nro_clasificacion,
                    c2.fecha_control
                    from trazadoras.clasificacion_remediar2 c2
                    left join general.relacioncodigos rl on rl.cuie = c2.cuie
                    left join facturacion.smiefectores efe on efe.cuie = c2.cuie
                    left join planillas.medicos med on med.id_medico = c2.id_medico
                    where c2.clave_beneficiario = '".$this->clavebeneficiario."'
                    )

                    union 

                    (select c1.dbt, c1.ta_sist, c1.ta_diast, c1.col_tot,  c1.obesidad,  c1.tabaquismo, c1.hta, c1.rcvg,
                    efe.nombreefector, rl.codremediar, med.nombre_medico, med.apellido_medico, c1.nro_clasificacion,
                    c1.fecha_control
                    from trazadoras.clasificacion_remediar c1
                    left join general.relacioncodigos rl on rl.cuie = c1.cuie
                    left join facturacion.smiefectores efe on efe.cuie = c1.cuie
                    left join planillas.medicos med on med.id_medico = c1.id_medico
                    where c1.clave = '".$this->clavebeneficiario."'
                    )";
            
            return($sql);
            
        }
        
        #	Transforma la decha coloquial en fechas compatibles con la DB
        public function FechaDb($fecha)
        {
        	list($d,$m,$a) = explode("/",$fecha);
	        return "$a-$m-$d";

        }


        #       Transforma los riesgos del Seguimiento a numeros
        public function RiesgoSeguimientoAValor()
        {
            $valor = 0;
            
            switch ($this->rcvgActual) {
                    case "BAJO":
                        $valor = 1;
                        break;
                    
                    case "MODERADO":
                        $valor = 2;
                        break;
                    
                    case "ALTO":
                        $valor = 3;
                        break;
                    
                    case "MUY ALTO":
                        $valor = 4;
                        break;
                    
                    default:
                        break;
            }
            
         return($valor);
        }

        
        #       Transforma los riesgos de la Clasificacion a numeros
        public function RiesgoClasificacionAValor()
        {
            $valor = 0;
            
            switch ($this->clasificacionRcvg) {
                    case "BAJO":
                        $valor = 1;
                        break;
                    
                    case "MODERADO":
                        $valor = 2;
                        break;
                    
                    case "ALTO":
                        $valor = 3;
                        break;
                    
                    case "MUY ALTO":
                        $valor = 4;
                        break;
                    
                    default:
                        break;
            }
            
         return($valor);
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


	#       Datos de la inscripcion
        
        public function getInscripcionScoreRiesgo(){
            return($this->inscripcionScoreRiesgo);
	}
        
        public function getInscripcionApellidoPromotor(){
            return($this->inscripcionApellidoPromotor);
	}
         
        public function getInscripcionNombrePromotor(){
            return($this->inscripcionNombrePromotor);
	}
         
        public function getInscripcionDniPromotor(){
            return($this->inscripcionDniPromotor);
	}
          
        public function getInscripcionCentroInscriptor(){
            return($this->inscripcionCentroInscriptor);
	}
        
        public function getInscripcionCentroInscriptorCuie(){
            return($this->inscripcionCentroInscriptorCuie);
	}       
        
        public function getInscripcionFechaEmpadronamiento(){
            return($this->inscripcionFechaEmpadronamiento);
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
        
        public function getFechaSeguimiento(){
            return($this->fechaSeguimiento);
        }

        #   Codigos de interconsultas
	public function getInterconsulta1(){
	return($this->interconsultas["0"]);}

	public function getInterconsulta2(){
	return($this->interconsultas["1"]);}

	public function getInterconsulta3(){
	return($this->interconsultas["2"]);}

	public function getInterconsulta4(){
	return($this->interconsultas["3"]);}
        
        #   Especialidades de interconsultas
        public function getInterconsultaEspecialidades1(){
	return($this->interconsultasEspecialidades["0"]);}

	public function getInterconsultaEspecialidades2(){
	return($this->interconsultasEspecialidades["1"]);}

	public function getInterconsultaEspecialidades3(){
	return($this->interconsultasEspecialidades["2"]);}

	public function getInterconsultaEspecialidades4(){
	return($this->interconsultasEspecialidades["3"]);}

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
        
        public function getTags(){
            return($this->tags);
        }
        
        public function getEfectorCodigo(){
            return($this->efectorCod);
        }
        
        public function getEfectorNombre(){
            return($this->codEfectorNombre);
        }
        
        public function getMedicoNombre(){
            return($this->MedicoNombre);
        }
        
        public function getMedicoApellido(){
            return($this->MedicoApellido);
        }
        
        public function getMedicoNombreCompleto(){
            return($this->MedicoApellido.", ".$this->MedicoNombre);
        }
        
        #   Clasificacion
        
        public function getClasificacionDmt()
        {
            return($this->clasificacionDmt);
        }
        
        public function getClasificacionTaSist()
        {
            return($this->clasificacionTaSist);
        }
        
        public function getClasificacionTaDiast()
        {
            return($this->clasificacionTaDiast);
        }
        
        public function getClasificacionColTot()
        {
            return($this->clasificacionColTot);
        }
        
        public function getClasificacionObesidad()
        {
            return($this->clasificacionObesidad);
        }
        public function getClasificacionTabaquismo()
        {
            return($this->clasificacionTabaquismo);
        }
        public function getClasificacionHta()
        {
            return($this->clasificacionHta);
        }        
        public function getClasificacionRcvg()
        {
            return($this->clasificacionRcvg);
        }
        
        
        public function getClasificacionNro()
        {
            return($this->clasificacionNro);
        }
        
        public function getClasificacionEfector()
        {
            return($this->clasificacionEfector);
        }
        
        public function getClasificacionEfectorCod()
        {
            return($this->clasificacionEfectorCod);
        }
        
        public function getClasificacionMedicoNombre()
        {
            return($this->clasificacionMedicoNombre);
        }
        
        public function getClasificacionMedicoApellido()
        {
            return($this->clasificacionMedicoApellido);
        }
        
        public function getClasificacionFechaControl()
        {
            return($this->clasificacionFechaControl);
        }
        
        public function getClasificacionMedicoNombreCompleto(){
            return($this->clasificacionMedicoApellido.", ".$this->clasificacionMedicoNombre);
        }
                   
}



?>
