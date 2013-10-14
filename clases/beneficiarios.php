<?php 


/**	DOCUMENTACION

BENEFICIARIOS:
	$beneficiario = new Beneficiario();
	$beneficiario->Automata("clave_beneficiario = '".$clave_beneficiario."'");


BENEFICIARIOSCOLECCION:
	$colect = new BeneficiariosColeccion(); 
	$colect->Filtrar("cuie_ea = 'N00298'"); ->Filtrar todos los beneficiarios con esta condicion
	$colect->getBeneficiarios(); -> Retorna un array con objetos beneficiarios que cumplan con la condicion
	$colect->Cantidad(); -> Retorna la cantidad de registros que cumplan con la condicion


*/



/**
*  UAD.BENEFICIARIOS
*/
class Beneficiario

{
	var $id_beneficiarios = '';
	var $estado_envio = '';
	var $clave_beneficiario = '';
	var $tipo_transaccion = '';
	var $apellido_benef = '';
	var $nombre_benef = '';
	var $clase_documento_benef = '';
	var $tipo_documento = '';
	var $numero_doc = '';
	var $id_categoria = '';
	var $sexo = '';
	var $fecha_nacimiento_benef = '';
	var $provincia_nac = '';
	var $localidad_nac = '';
	var $pais_nac = '';
	var $indigena = '';
	var $id_tribu = '';
	var $id_lengua = '';
	var $alfabeta = '';
	var $estudios = '';
	var $anio_mayor_nivel = '';
	var $tipo_doc_madre = '';
	var $nro_doc_madre = '';
	var $apellido_madre = '';
	var $nombre_madre = '';
	var $alfabeta_madre = '';
	var $estudios_madre = '';
	var $anio_mayor_nivel_madre = '';
	var $tipo_doc_padre = '';
	var $nro_doc_padre = '';
	var $apellido_padre = '';
	var $nombre_padre = '';
	var $alfabeta_padre = '';
	var $estudios_padre = '';
	var $anio_mayor_nivel_padre = '';
	var $tipo_doc_tutor = '';
	var $nro_doc_tutor = '';
	var $apellido_tutor = '';
	var $nombre_tutor = '';
	var $alfabeta_tutor = '';
	var $estudios_tutor = '';
	var $anio_mayor_nivel_tutor = '';
	var $fecha_diagnostico_embarazo = '';
	var $semanas_embarazo = '';
	var $fecha_probable_parto = '';
	var $fecha_efectiva_parto = '';
	var $cuie_ea = '';
	var $cuie_ah = '';
	var $menor_convive_con_adulto = '';
	var $calle = '';
	var $numero_calle = '';
	var $piso = '';
	var $dpto = '';
	var $manzana = '';
	var $entre_calle_1 = '';
	var $entre_calle_2 = '';
	var $telefono = '';
	var $departamento = '';
	var $localidad = '';
	var $municipio = '';
	var $barrio = '';
	var $cod_pos = '';
	var $observaciones = '';
	var $fecha_inscripcion = '';
	var $fecha_carga = '';
	var $usuario_carga = '';
	var $activo = '';
	var $score_riesgo = '';
	var $mail = '';
	var $celular = '';
	var $otrotel = '';
	var $estadoestbeneficiario = '';
	var $fum = '';
	var $obsgenerales = '';
	var $estadoest_madre = '';
	var $tipo_ficha = '';
	var $responsable = '';
	var $discv = '';
	var $disca = '';
	var $discmo = '';
	var $discme = '';
	var $otradisc = '';
	var $estadoest_padre = '';
	var $estadoest_tutor = '';
	var $menor_embarazada = '';
	var $apellido_benef_otro = '';
	var $nombre_benef_otro = '';
	var $fecha_verificado = '';
	var $usuario_verificado = '';
	var $apellidoagente = '';
	var $nombreagente = '';
	var $centro_inscriptor = '';
	var $dni_agente = '';
	var $edades = '';
	var $fallecido = '';


	function __construct()
	{
		
	}

	### Constructores
	
	public function construirResult($result){
		
		$this->id_beneficiarios = $result->fields['id_beneficiarios'];
		$this->estado_envio = $result->fields['estado_envio'];
		$this->clave_beneficiario = $result->fields['clave_beneficiario'];
		$this->tipo_transaccion = $result->fields['tipo_transaccion'];
		$this->apellido_benef = $result->fields['apellido_benef'];
		$this->nombre_benef = $result->fields['nombre_benef'];
		$this->clase_documento_benef = $result->fields['clase_documento_benef'];
		$this->tipo_documento = $result->fields['tipo_documento'];
		$this->numero_doc = $result->fields['numero_doc'];
		$this->id_categoria = $result->fields['id_categoria'];
		$this->sexo = $result->fields['sexo'];
		$this->fecha_nacimiento_benef = $result->fields['fecha_nacimiento_benef'];
		$this->provincia_nac = $result->fields['provincia_nac'];
		$this->localidad_nac = $result->fields['localidad_nac'];
		$this->pais_nac = $result->fields['pais_nac'];
		$this->indigena = $result->fields['indigena'];
		$this->id_tribu = $result->fields['id_tribu'];
		$this->id_lengua = $result->fields['id_lengua'];
		$this->alfabeta = $result->fields['alfabeta'];
		$this->estudios = $result->fields['estudios'];
		$this->anio_mayor_nivel = $result->fields['anio_mayor_nivel'];
		$this->tipo_doc_madre = $result->fields['tipo_doc_madre'];
		$this->nro_doc_madre = $result->fields['nro_doc_madre'];
		$this->apellido_madre = $result->fields['apellido_madre'];
		$this->nombre_madre = $result->fields['nombre_madre'];
		$this->alfabeta_madre = $result->fields['alfabeta_madre'];
		$this->estudios_madre = $result->fields['estudios_madre'];
		$this->anio_mayor_nivel_madre = $result->fields['anio_mayor_nivel_madre'];
		$this->tipo_doc_padre = $result->fields['tipo_doc_padre'];
		$this->nro_doc_padre = $result->fields['nro_doc_padre'];
		$this->apellido_padre = $result->fields['apellido_padre'];
		$this->nombre_padre = $result->fields['nombre_padre'];
		$this->alfabeta_padre = $result->fields['alfabeta_padre'];
		$this->estudios_padre = $result->fields['estudios_padre'];
		$this->anio_mayor_nivel_padre = $result->fields['anio_mayor_nivel_padre'];
		$this->tipo_doc_tutor = $result->fields['tipo_doc_tutor'];
		$this->nro_doc_tutor = $result->fields['nro_doc_tutor'];
		$this->apellido_tutor = $result->fields['apellido_tutor'];
		$this->nombre_tutor = $result->fields['nombre_tutor'];
		$this->alfabeta_tutor = $result->fields['alfabeta_tutor'];
		$this->estudios_tutor = $result->fields['estudios_tutor'];
		$this->anio_mayor_nivel_tutor = $result->fields['anio_mayor_nivel_tutor'];
		$this->fecha_diagnostico_embarazo = $result->fields['fecha_diagnostico_embarazo'];
		$this->semanas_embarazo = $result->fields['semanas_embarazo'];
		$this->fecha_probable_parto = $result->fields['fecha_probable_parto'];
		$this->fecha_efectiva_parto = $result->fields['fecha_efectiva_parto'];
		$this->cuie_ea = $result->fields['cuie_ea'];
		$this->cuie_ah = $result->fields['cuie_ah'];
		$this->menor_convive_con_adulto = $result->fields['menor_convive_con_adulto'];
		$this->calle = $result->fields['calle'];
		$this->numero_calle = $result->fields['numero_calle'];
		$this->piso = $result->fields['piso'];
		$this->dpto = $result->fields['dpto'];
		$this->manzana = $result->fields['manzana'];
		$this->entre_calle_1 = $result->fields['entre_calle_1'];
		$this->entre_calle_2 = $result->fields['entre_calle_2'];
		$this->telefono = $result->fields['telefono'];
		$this->departamento = $result->fields['departamento'];
		$this->localidad = $result->fields['localidad'];
		$this->municipio = $result->fields['municipio'];
		$this->barrio = $result->fields['barrio'];
		$this->cod_pos = $result->fields['cod_pos'];
		$this->observaciones = $result->fields['observaciones'];
		$this->fecha_inscripcion = $result->fields['fecha_inscripcion'];
		$this->fecha_carga = $result->fields['fecha_carga'];
		$this->usuario_carga = $result->fields['usuario_carga'];
		$this->activo = $result->fields['activo'];
		$this->score_riesgo = $result->fields['score_riesgo'];
		$this->mail = $result->fields['mail'];
		$this->celular = $result->fields['celular'];
		$this->otrotel = $result->fields['otrotel'];
		$this->estadoestbeneficiario = $result->fields['estadoestbeneficiario'];
		$this->fum = $result->fields['fum'];
		$this->obsgenerales = $result->fields['obsgenerales'];
		$this->estadoest_madre = $result->fields['estadoest_madre'];
		$this->tipo_ficha = $result->fields['tipo_ficha'];
		$this->responsable = $result->fields['responsable'];
		$this->discv = $result->fields['discv'];
		$this->disca = $result->fields['disca'];
		$this->discmo = $result->fields['discmo'];
		$this->discme = $result->fields['discme'];
		$this->otradisc = $result->fields['otradisc'];
		$this->estadoest_padre = $result->fields['estadoest_padre'];
		$this->estadoest_tutor = $result->fields['estadoest_tutor'];
		$this->menor_embarazada = $result->fields['menor_embarazada'];
		$this->apellido_benef_otro = $result->fields['apellido_benef_otro'];
		$this->nombre_benef_otro = $result->fields['nombre_benef_otro'];
		$this->fecha_verificado = $result->fields['fecha_verificado'];
		$this->usuario_verificado = $result->fields['usuario_verificado'];
		$this->apellidoagente = $result->fields['apellidoagente'];
		$this->nombreagente = $result->fields['nombreagente'];
		$this->centro_inscriptor = $result->fields['centro_inscriptor'];
		$this->dni_agente = $result->fields['dni_agente'];
		$this->edades = $result->fields['edades'];
		$this->fallecido = $result->fields['fallecido'];
	}


	### GETTERS
	
	# Documentacion para el metodo setId_beneficiarios 		
	public function setId_beneficiarios($id_beneficiarios)
	{
		$this->id_beneficiarios = $id_beneficiarios;
	}
	# Documentacion para el metodo setEstado_envio 		
	public function setEstado_envio($estado_envio)
	{
		$this->estado_envio = $estado_envio;
	}
	# Documentacion para el metodo setClave_beneficiario 		
	public function setClave_beneficiario($clave_beneficiario)
	{
		$this->clave_beneficiario = $clave_beneficiario;
	}
	# Documentacion para el metodo setTipo_transaccion 		
	public function setTipo_transaccion($tipo_transaccion)
	{
		$this->tipo_transaccion = $tipo_transaccion;
	}
	# Documentacion para el metodo setApellido_benef 		
	public function setApellido_benef($apellido_benef)
	{
		$this->apellido_benef = $apellido_benef;
	}
	# Documentacion para el metodo setNombre_benef 		
	public function setNombre_benef($nombre_benef)
	{
		$this->nombre_benef = $nombre_benef;
	}
	# Documentacion para el metodo setClase_documento_benef 		
	public function setClase_documento_benef($clase_documento_benef)
	{
		$this->clase_documento_benef = $clase_documento_benef;
	}
	# Documentacion para el metodo setTipo_documento 		
	public function setTipo_documento($tipo_documento)
	{
		$this->tipo_documento = $tipo_documento;
	}
	# Documentacion para el metodo setNumero_doc 		
	public function setNumero_doc($numero_doc)
	{
		$this->numero_doc = $numero_doc;
	}
	# Documentacion para el metodo setId_categoria 		
	public function setId_categoria($id_categoria)
	{
		$this->id_categoria = $id_categoria;
	}
	# Documentacion para el metodo setSexo 		
	public function setSexo($sexo)
	{
		$this->sexo = $sexo;
	}
	# Documentacion para el metodo setFecha_nacimiento_benef 		
	public function setFecha_nacimiento_benef($fecha_nacimiento_benef)
	{
		$this->fecha_nacimiento_benef = $fecha_nacimiento_benef;
	}
	# Documentacion para el metodo setProvincia_nac 		
	public function setProvincia_nac($provincia_nac)
	{
		$this->provincia_nac = $provincia_nac;
	}
	# Documentacion para el metodo setLocalidad_nac 		
	public function setLocalidad_nac($localidad_nac)
	{
		$this->localidad_nac = $localidad_nac;
	}
	# Documentacion para el metodo setPais_nac 		
	public function setPais_nac($pais_nac)
	{
		$this->pais_nac = $pais_nac;
	}
	# Documentacion para el metodo setIndigena 		
	public function setIndigena($indigena)
	{
		$this->indigena = $indigena;
	}
	# Documentacion para el metodo setId_tribu 		
	public function setId_tribu($id_tribu)
	{
		$this->id_tribu = $id_tribu;
	}
	# Documentacion para el metodo setId_lengua 		
	public function setId_lengua($id_lengua)
	{
		$this->id_lengua = $id_lengua;
	}
	# Documentacion para el metodo setAlfabeta 		
	public function setAlfabeta($alfabeta)
	{
		$this->alfabeta = $alfabeta;
	}
	# Documentacion para el metodo setEstudios 		
	public function setEstudios($estudios)
	{
		$this->estudios = $estudios;
	}
	# Documentacion para el metodo setAnio_mayor_nivel 		
	public function setAnio_mayor_nivel($anio_mayor_nivel)
	{
		$this->anio_mayor_nivel = $anio_mayor_nivel;
	}
	# Documentacion para el metodo setTipo_doc_madre 		
	public function setTipo_doc_madre($tipo_doc_madre)
	{
		$this->tipo_doc_madre = $tipo_doc_madre;
	}
	# Documentacion para el metodo setNro_doc_madre 		
	public function setNro_doc_madre($nro_doc_madre)
	{
		$this->nro_doc_madre = $nro_doc_madre;
	}
	# Documentacion para el metodo setApellido_madre 		
	public function setApellido_madre($apellido_madre)
	{
		$this->apellido_madre = $apellido_madre;
	}
	# Documentacion para el metodo setNombre_madre 		
	public function setNombre_madre($nombre_madre)
	{
		$this->nombre_madre = $nombre_madre;
	}
	# Documentacion para el metodo setAlfabeta_madre 		
	public function setAlfabeta_madre($alfabeta_madre)
	{
		$this->alfabeta_madre = $alfabeta_madre;
	}
	# Documentacion para el metodo setEstudios_madre 		
	public function setEstudios_madre($estudios_madre)
	{
		$this->estudios_madre = $estudios_madre;
	}
	# Documentacion para el metodo setAnio_mayor_nivel_madre 		
	public function setAnio_mayor_nivel_madre($anio_mayor_nivel_madre)
	{
		$this->anio_mayor_nivel_madre = $anio_mayor_nivel_madre;
	}
	# Documentacion para el metodo setTipo_doc_padre 		
	public function setTipo_doc_padre($tipo_doc_padre)
	{
		$this->tipo_doc_padre = $tipo_doc_padre;
	}
	# Documentacion para el metodo setNro_doc_padre 		
	public function setNro_doc_padre($nro_doc_padre)
	{
		$this->nro_doc_padre = $nro_doc_padre;
	}
	# Documentacion para el metodo setApellido_padre 		
	public function setApellido_padre($apellido_padre)
	{
		$this->apellido_padre = $apellido_padre;
	}
	# Documentacion para el metodo setNombre_padre 		
	public function setNombre_padre($nombre_padre)
	{
		$this->nombre_padre = $nombre_padre;
	}
	# Documentacion para el metodo setAlfabeta_padre 		
	public function setAlfabeta_padre($alfabeta_padre)
	{
		$this->alfabeta_padre = $alfabeta_padre;
	}
	# Documentacion para el metodo setEstudios_padre 		
	public function setEstudios_padre($estudios_padre)
	{
		$this->estudios_padre = $estudios_padre;
	}
	# Documentacion para el metodo setAnio_mayor_nivel_padre 		
	public function setAnio_mayor_nivel_padre($anio_mayor_nivel_padre)
	{
		$this->anio_mayor_nivel_padre = $anio_mayor_nivel_padre;
	}
	# Documentacion para el metodo setTipo_doc_tutor 		
	public function setTipo_doc_tutor($tipo_doc_tutor)
	{
		$this->tipo_doc_tutor = $tipo_doc_tutor;
	}
	# Documentacion para el metodo setNro_doc_tutor 		
	public function setNro_doc_tutor($nro_doc_tutor)
	{
		$this->nro_doc_tutor = $nro_doc_tutor;
	}
	# Documentacion para el metodo setApellido_tutor 		
	public function setApellido_tutor($apellido_tutor)
	{
		$this->apellido_tutor = $apellido_tutor;
	}
	# Documentacion para el metodo setNombre_tutor 		
	public function setNombre_tutor($nombre_tutor)
	{
		$this->nombre_tutor = $nombre_tutor;
	}
	# Documentacion para el metodo setAlfabeta_tutor 		
	public function setAlfabeta_tutor($alfabeta_tutor)
	{
		$this->alfabeta_tutor = $alfabeta_tutor;
	}
	# Documentacion para el metodo setEstudios_tutor 		
	public function setEstudios_tutor($estudios_tutor)
	{
		$this->estudios_tutor = $estudios_tutor;
	}
	# Documentacion para el metodo setAnio_mayor_nivel_tutor 		
	public function setAnio_mayor_nivel_tutor($anio_mayor_nivel_tutor)
	{
		$this->anio_mayor_nivel_tutor = $anio_mayor_nivel_tutor;
	}
	# Documentacion para el metodo setFecha_diagnostico_embarazo 		
	public function setFecha_diagnostico_embarazo($fecha_diagnostico_embarazo)
	{
		$this->fecha_diagnostico_embarazo = $fecha_diagnostico_embarazo;
	}
	# Documentacion para el metodo setSemanas_embarazo 		
	public function setSemanas_embarazo($semanas_embarazo)
	{
		$this->semanas_embarazo = $semanas_embarazo;
	}
	# Documentacion para el metodo setFecha_probable_parto 		
	public function setFecha_probable_parto($fecha_probable_parto)
	{
		$this->fecha_probable_parto = $fecha_probable_parto;
	}
	# Documentacion para el metodo setFecha_efectiva_parto 		
	public function setFecha_efectiva_parto($fecha_efectiva_parto)
	{
		$this->fecha_efectiva_parto = $fecha_efectiva_parto;
	}
	# Documentacion para el metodo setCuie_ea 		
	public function setCuie_ea($cuie_ea)
	{
		$this->cuie_ea = $cuie_ea;
	}
	# Documentacion para el metodo setCuie_ah 		
	public function setCuie_ah($cuie_ah)
	{
		$this->cuie_ah = $cuie_ah;
	}
	# Documentacion para el metodo setMenor_convive_con_adulto 		
	public function setMenor_convive_con_adulto($menor_convive_con_adulto)
	{
		$this->menor_convive_con_adulto = $menor_convive_con_adulto;
	}
	# Documentacion para el metodo setCalle 		
	public function setCalle($calle)
	{
		$this->calle = $calle;
	}
	# Documentacion para el metodo setNumero_calle 		
	public function setNumero_calle($numero_calle)
	{
		$this->numero_calle = $numero_calle;
	}
	# Documentacion para el metodo setPiso 		
	public function setPiso($piso)
	{
		$this->piso = $piso;
	}
	# Documentacion para el metodo setDpto 		
	public function setDpto($dpto)
	{
		$this->dpto = $dpto;
	}
	# Documentacion para el metodo setManzana 		
	public function setManzana($manzana)
	{
		$this->manzana = $manzana;
	}
	# Documentacion para el metodo setEntre_calle_1 		
	public function setEntre_calle_1($entre_calle_1)
	{
		$this->entre_calle_1 = $entre_calle_1;
	}
	# Documentacion para el metodo setEntre_calle_2 		
	public function setEntre_calle_2($entre_calle_2)
	{
		$this->entre_calle_2 = $entre_calle_2;
	}
	# Documentacion para el metodo setTelefono 		
	public function setTelefono($telefono)
	{
		$this->telefono = $telefono;
	}
	# Documentacion para el metodo setDepartamento 		
	public function setDepartamento($departamento)
	{
		$this->departamento = $departamento;
	}
	# Documentacion para el metodo setLocalidad 		
	public function setLocalidad($localidad)
	{
		$this->localidad = $localidad;
	}
	# Documentacion para el metodo setMunicipio 		
	public function setMunicipio($municipio)
	{
		$this->municipio = $municipio;
	}
	# Documentacion para el metodo setBarrio 		
	public function setBarrio($barrio)
	{
		$this->barrio = $barrio;
	}
	# Documentacion para el metodo setCod_pos 		
	public function setCod_pos($cod_pos)
	{
		$this->cod_pos = $cod_pos;
	}
	# Documentacion para el metodo setObservaciones 		
	public function setObservaciones($observaciones)
	{
		$this->observaciones = $observaciones;
	}
	# Documentacion para el metodo setFecha_inscripcion 		
	public function setFecha_inscripcion($fecha_inscripcion)
	{
		$this->fecha_inscripcion = $fecha_inscripcion;
	}
	# Documentacion para el metodo setFecha_carga 		
	public function setFecha_carga($fecha_carga)
	{
		$this->fecha_carga = $fecha_carga;
	}
	# Documentacion para el metodo setUsuario_carga 		
	public function setUsuario_carga($usuario_carga)
	{
		$this->usuario_carga = $usuario_carga;
	}
	# Documentacion para el metodo setActivo 		
	public function setActivo($activo)
	{
		$this->activo = $activo;
	}
	# Documentacion para el metodo setScore_riesgo 		
	public function setScore_riesgo($score_riesgo)
	{
		$this->score_riesgo = $score_riesgo;
	}
	# Documentacion para el metodo setMail 		
	public function setMail($mail)
	{
		$this->mail = $mail;
	}
	# Documentacion para el metodo setCelular 		
	public function setCelular($celular)
	{
		$this->celular = $celular;
	}
	# Documentacion para el metodo setOtrotel 		
	public function setOtrotel($otrotel)
	{
		$this->otrotel = $otrotel;
	}
	# Documentacion para el metodo setEstadoestbeneficiario 		
	public function setEstadoestbeneficiario($estadoestbeneficiario)
	{
		$this->estadoestbeneficiario = $estadoestbeneficiario;
	}
	# Documentacion para el metodo setFum 		
	public function setFum($fum)
	{
		$this->fum = $fum;
	}
	# Documentacion para el metodo setObsgenerales 		
	public function setObsgenerales($obsgenerales)
	{
		$this->obsgenerales = $obsgenerales;
	}
	# Documentacion para el metodo setEstadoest_madre 		
	public function setEstadoest_madre($estadoest_madre)
	{
		$this->estadoest_madre = $estadoest_madre;
	}
	# Documentacion para el metodo settipo_ficha 		
	public function setTipo_ficha($tipo_ficha)
	{
		$this->tipo_ficha = $tipo_ficha;
	}
	# Documentacion para el metodo setResponsable 		
	public function setResponsable($responsable)
	{
		$this->responsable = $responsable;
	}
	# Documentacion para el metodo setDiscv 		
	public function setDiscv($discv)
	{
		$this->discv = $discv;
	}
	# Documentacion para el metodo setDisca 		
	public function setDisca($disca)
	{
		$this->disca = $disca;
	}
	# Documentacion para el metodo setDiscmo 		
	public function setDiscmo($discmo)
	{
		$this->discmo = $discmo;
	}
	# Documentacion para el metodo setDiscme 		
	public function setDiscme($discme)
	{
		$this->discme = $discme;
	}
	# Documentacion para el metodo setOtradisc 		
	public function setOtradisc($otradisc)
	{
		$this->otradisc = $otradisc;
	}
	# Documentacion para el metodo setEstadoest_padre 		
	public function setEstadoest_padre($estadoest_padre)
	{
		$this->estadoest_padre = $estadoest_padre;
	}
	# Documentacion para el metodo setEstadoest_tutor 		
	public function setEstadoest_tutor($estadoest_tutor)
	{
		$this->estadoest_tutor = $estadoest_tutor;
	}
	# Documentacion para el metodo setMenor_embarazada 		
	public function setMenor_embarazada($menor_embarazada)
	{
		$this->menor_embarazada = $menor_embarazada;
	}
	# Documentacion para el metodo setApellido_benef_otro 		
	public function setApellido_benef_otro($apellido_benef_otro)
	{
		$this->apellido_benef_otro = $apellido_benef_otro;
	}
	# Documentacion para el metodo setNombre_benef_otro 		
	public function setNombre_benef_otro($nombre_benef_otro)
	{
		$this->nombre_benef_otro = $nombre_benef_otro;
	}
	# Documentacion para el metodo setFecha_verificado 		
	public function setFecha_verificado($fecha_verificado)
	{
		$this->fecha_verificado = $fecha_verificado;
	}
	# Documentacion para el metodo setUsuario_verificado 		
	public function setUsuario_verificado($usuario_verificado)
	{
		$this->usuario_verificado = $usuario_verificado;
	}
	# Documentacion para el metodo setApellidoagente 		
	public function setApellidoagente($apellidoagente)
	{
		$this->apellidoagente = $apellidoagente;
	}
	# Documentacion para el metodo setNombreagente 		
	public function setNombreagente($nombreagente)
	{
		$this->nombreagente = $nombreagente;
	}
	# Documentacion para el metodo setCentro_inscriptor 		
	public function setCentro_inscriptor($centro_inscriptor)
	{
		$this->centro_inscriptor = $centro_inscriptor;
	}
	# Documentacion para el metodo setDni_agente 		
	public function setDni_agente($dni_agente)
	{
		$this->dni_agente = $dni_agente;
	}
	# Documentacion para el metodo setEdades 		
	public function setEdades($edades)
	{
		$this->edades = $edades;
	}
	# Documentacion para el metodo setFallecido 		
	public function setFallecido($fallecido)
	{
		$this->fallecido = $fallecido;
	}



	### SETTERS
	
	# Documentacion para el metodo getId_beneficiarios 		
	public function getId_beneficiarios()
	{
		return($this->id_beneficiarios);
	}
	# Documentacion para el metodo getEstado_envio 		
	public function getEstado_envio()
	{
		return($this->estado_envio);
	}
	# Documentacion para el metodo getClave_beneficiario 		
	public function getClave_beneficiario()
	{
		return($this->clave_beneficiario);
	}
	# Documentacion para el metodo getTipo_transaccion 		
	public function getTipo_transaccion()
	{
		return($this->tipo_transaccion);
	}
	# Documentacion para el metodo getApellido_benef 		
	public function getApellido_benef()
	{
		return($this->apellido_benef);
	}
	# Documentacion para el metodo getNombre_benef 		
	public function getNombre_benef()
	{
		return($this->nombre_benef);
	}
	# Documentacion para el metodo getClase_documento_benef 		
	public function getClase_documento_benef()
	{
		return($this->clase_documento_benef);
	}
	# Documentacion para el metodo getTipo_documento 		
	public function getTipo_documento()
	{
		return($this->tipo_documento);
	}
	# Documentacion para el metodo getNumero_doc 		
	public function getNumero_doc()
	{
		return($this->numero_doc);
	}
	# Documentacion para el metodo getId_categoria 		
	public function getId_categoria()
	{
		return($this->id_categoria);
	}
	# Documentacion para el metodo getSexo 		
	public function getSexo()
	{
		return($this->sexo);
	}
	# Documentacion para el metodo getFecha_nacimiento_benef 		
	public function getFecha_nacimiento_benef()
	{
		return(fecha($this->fecha_nacimiento_benef));
	}
	# Documentacion para el metodo getProvincia_nac 		
	public function getProvincia_nac()
	{
		return($this->provincia_nac);
	}
	# Documentacion para el metodo getLocalidad_nac 		
	public function getLocalidad_nac()
	{
		return($this->localidad_nac);
	}
	# Documentacion para el metodo getPais_nac 		
	public function getPais_nac()
	{
		return($this->pais_nac);
	}
	# Documentacion para el metodo getIndigena 		
	public function getIndigena()
	{
		return($this->indigena);
	}
	# Documentacion para el metodo getId_tribu 		
	public function getId_tribu()
	{
		return($this->id_tribu);
	}
	# Documentacion para el metodo getId_lengua 		
	public function getId_lengua()
	{
		return($this->id_lengua);
	}
	# Documentacion para el metodo getAlfabeta 		
	public function getAlfabeta()
	{
		return($this->alfabeta);
	}
	# Documentacion para el metodo getEstudios 		
	public function getEstudios()
	{
		return($this->estudios);
	}
	# Documentacion para el metodo getAnio_mayor_nivel 		
	public function getAnio_mayor_nivel()
	{
		return($this->anio_mayor_nivel);
	}
	# Documentacion para el metodo getTipo_doc_madre 		
	public function getTipo_doc_madre()
	{
		return($this->tipo_doc_madre);
	}
	# Documentacion para el metodo getNro_doc_madre 		
	public function getNro_doc_madre()
	{
		return($this->nro_doc_madre);
	}
	# Documentacion para el metodo getApellido_madre 		
	public function getApellido_madre()
	{
		return($this->apellido_madre);
	}
	# Documentacion para el metodo getNombre_madre 		
	public function getNombre_madre()
	{
		return($this->nombre_madre);
	}
	# Documentacion para el metodo getAlfabeta_madre 		
	public function getAlfabeta_madre()
	{
		return($this->alfabeta_madre);
	}
	# Documentacion para el metodo getEstudios_madre 		
	public function getEstudios_madre()
	{
		return($this->estudios_madre);
	}
	# Documentacion para el metodo getAnio_mayor_nivel_madre 		
	public function getAnio_mayor_nivel_madre()
	{
		return($this->anio_mayor_nivel_madre);
	}
	# Documentacion para el metodo getTipo_doc_padre 		
	public function getTipo_doc_padre()
	{
		return($this->tipo_doc_padre);
	}
	# Documentacion para el metodo getNro_doc_padre 		
	public function getNro_doc_padre()
	{
		return($this->nro_doc_padre);
	}
	# Documentacion para el metodo getApellido_padre 		
	public function getApellido_padre()
	{
		return($this->apellido_padre);
	}
	# Documentacion para el metodo getNombre_padre 		
	public function getNombre_padre()
	{
		return($this->nombre_padre);
	}
	# Documentacion para el metodo getAlfabeta_padre 		
	public function getAlfabeta_padre()
	{
		return($this->alfabeta_padre);
	}
	# Documentacion para el metodo getEstudios_padre 		
	public function getEstudios_padre()
	{
		return($this->estudios_padre);
	}
	# Documentacion para el metodo getAnio_mayor_nivel_padre 		
	public function getAnio_mayor_nivel_padre()
	{
		return($this->anio_mayor_nivel_padre);
	}
	# Documentacion para el metodo getTipo_doc_tutor 		
	public function getTipo_doc_tutor()
	{
		return($this->tipo_doc_tutor);
	}
	# Documentacion para el metodo getNro_doc_tutor 		
	public function getNro_doc_tutor()
	{
		return($this->nro_doc_tutor);
	}
	# Documentacion para el metodo getApellido_tutor 		
	public function getApellido_tutor()
	{
		return($this->apellido_tutor);
	}
	# Documentacion para el metodo getNombre_tutor 		
	public function getNombre_tutor()
	{
		return($this->nombre_tutor);
	}
	# Documentacion para el metodo getAlfabeta_tutor 		
	public function getAlfabeta_tutor()
	{
		return($this->alfabeta_tutor);
	}
	# Documentacion para el metodo getEstudios_tutor 		
	public function getEstudios_tutor()
	{
		return($this->estudios_tutor);
	}
	# Documentacion para el metodo getAnio_mayor_nivel_tutor 		
	public function getAnio_mayor_nivel_tutor()
	{
		return($this->anio_mayor_nivel_tutor);
	}
	# Documentacion para el metodo getFecha_diagnostico_embarazo 		
	public function getFecha_diagnostico_embarazo()
	{
		return($this->fecha_diagnostico_embarazo);
	}
	# Documentacion para el metodo getSemanas_embarazo 		
	public function getSemanas_embarazo()
	{
		return($this->semanas_embarazo);
	}
	# Documentacion para el metodo getFecha_probable_parto 		
	public function getFecha_probable_parto()
	{
		return($this->fecha_probable_parto);
	}
	# Documentacion para el metodo getFecha_efectiva_parto 		
	public function getFecha_efectiva_parto()
	{
		return($this->fecha_efectiva_parto);
	}
	# Documentacion para el metodo getCuie_ea 		
	public function getCuie_ea()
	{
		return($this->cuie_ea);
	}
	# Documentacion para el metodo getCuie_ah 		
	public function getCuie_ah()
	{
		return($this->cuie_ah);
	}
	# Documentacion para el metodo getMenor_convive_con_adulto 		
	public function getMenor_convive_con_adulto()
	{
		return($this->menor_convive_con_adulto);
	}
	# Documentacion para el metodo getCalle 		
	public function getCalle()
	{
		return($this->calle);
	}
	# Documentacion para el metodo getNumero_calle 		
	public function getNumero_calle()
	{
		return($this->numero_calle);
	}
	# Documentacion para el metodo getPiso 		
	public function getPiso()
	{
		return($this->piso);
	}
	# Documentacion para el metodo getDpto 		
	public function getDpto()
	{
		return($this->dpto);
	}
	# Documentacion para el metodo getManzana 		
	public function getManzana()
	{
		return($this->manzana);
	}
	# Documentacion para el metodo getEntre_calle_1 		
	public function getEntre_calle_1()
	{
		return($this->entre_calle_1);
	}
	# Documentacion para el metodo getEntre_calle_2 		
	public function getEntre_calle_2()
	{
		return($this->entre_calle_2);
	}
	# Documentacion para el metodo getTelefono 		
	public function getTelefono()
	{
		return($this->telefono);
	}
	# Documentacion para el metodo getDepartamento 		
	public function getDepartamento()
	{
		return($this->departamento);
	}
	# Documentacion para el metodo getLocalidad 		
	public function getLocalidad()
	{
		return($this->localidad);
	}
	# Documentacion para el metodo getMunicipio 		
	public function getMunicipio()
	{
		return($this->municipio);
	}
	# Documentacion para el metodo getBarrio 		
	public function getBarrio()
	{
		return($this->barrio);
	}
	# Documentacion para el metodo getCod_pos 		
	public function getCod_pos()
	{
		return($this->cod_pos);
	}
	# Documentacion para el metodo getObservaciones 		
	public function getObservaciones()
	{
		return($this->observaciones);
	}
	# Documentacion para el metodo getFecha_inscripcion 		
	public function getFecha_inscripcion()
	{
		return($this->fecha_inscripcion);
	}
	# Documentacion para el metodo getFecha_carga 		
	public function getFecha_carga()
	{
		return($this->fecha_carga);
	}
	# Documentacion para el metodo getUsuario_carga 		
	public function getUsuario_carga()
	{
		return($this->usuario_carga);
	}
	# Documentacion para el metodo getActivo 		
	public function getActivo()
	{
		return($this->activo);
	}
	# Documentacion para el metodo getScore_riesgo 		
	public function getScore_riesgo()
	{
		return($this->score_riesgo);
	}
	# Documentacion para el metodo getMail 		
	public function getMail()
	{
		return($this->mail);
	}
	# Documentacion para el metodo getCelular 		
	public function getCelular()
	{
		return($this->celular);
	}
	# Documentacion para el metodo getOtrotel 		
	public function getOtrotel()
	{
		return($this->otrotel);
	}
	# Documentacion para el metodo getEstadoestbeneficiario 		
	public function getEstadoestbeneficiario()
	{
		return($this->estadoestbeneficiario);
	}
	# Documentacion para el metodo getFum 		
	public function getFum()
	{
		return($this->fum);
	}
	# Documentacion para el metodo getObsgenerales 		
	public function getObsgenerales()
	{
		return($this->obsgenerales);
	}
	# Documentacion para el metodo getEstadoest_madre 		
	public function getEstadoest_madre()
	{
		return($this->estadoest_madre);
	}
	# Documentacion para el metodo gettipo_ficha 		
	public function gettipo_ficha()
	{
		return($this->tipo_ficha);
	}
	# Documentacion para el metodo getResponsable 		
	public function getResponsable()
	{
		return($this->responsable);
	}
	# Documentacion para el metodo getDiscv 		
	public function getDiscv()
	{
		return($this->discv);
	}
	# Documentacion para el metodo getDisca 		
	public function getDisca()
	{
		return($this->disca);
	}
	# Documentacion para el metodo getDiscmo 		
	public function getDiscmo()
	{
		return($this->discmo);
	}
	# Documentacion para el metodo getDiscme 		
	public function getDiscme()
	{
		return($this->discme);
	}
	# Documentacion para el metodo getOtradisc 		
	public function getOtradisc()
	{
		return($this->otradisc);
	}
	# Documentacion para el metodo getEstadoest_padre 		
	public function getEstadoest_padre()
	{
		return($this->estadoest_padre);
	}
	# Documentacion para el metodo getEstadoest_tutor 		
	public function getEstadoest_tutor()
	{
		return($this->estadoest_tutor);
	}
	# Documentacion para el metodo getMenor_embarazada 		
	public function getMenor_embarazada()
	{
		return($this->menor_embarazada);
	}
	# Documentacion para el metodo getApellido_benef_otro 		
	public function getApellido_benef_otro()
	{
		return($this->apellido_benef_otro);
	}
	# Documentacion para el metodo getNombre_benef_otro 		
	public function getNombre_benef_otro()
	{
		return($this->nombre_benef_otro);
	}
	# Documentacion para el metodo getFecha_verificado 		
	public function getFecha_verificado()
	{
		return($this->fecha_verificado);
	}
	# Documentacion para el metodo getUsuario_verificado 		
	public function getUsuario_verificado()
	{
		return($this->usuario_verificado);
	}
	# Documentacion para el metodo getApellidoagente 		
	public function getApellidoagente()
	{
		return($this->apellidoagente);
	}
	# Documentacion para el metodo getNombreagente 		
	public function getNombreagente()
	{
		return($this->nombreagente);
	}
	# Documentacion para el metodo getCentro_inscriptor 		
	public function getCentro_inscriptor()
	{
		return($this->centro_inscriptor);
	}
	# Documentacion para el metodo getDni_agente 		
	public function getDni_agente()
	{
		return($this->dni_agente);
	}
	# Documentacion para el metodo getEdades 		
	public function getEdades()
	{
		return($this->edades);
	}
	# Documentacion para el metodo getFallecido 		
	public function getFallecido()
	{
		return($this->fallecido);
	}
	#	Metodo getNombreCompleto 		
	public function getNombreCompleto()
	{
            $nombre_completo .= $this->apellido_benef;
            $nombre_completo .= $this->apellido_benef_otro!="" ? " ".$this->apellido_benef_otro."," : ",";
            $nombre_completo .= " ".$this->nombre_benef;
            $nombre_completo .= $this->nombre_benef_otro!="" ? " ".$this->nombre_benef_otro : "";
		return($nombre_completo);
	}


	#	Metodo getEdadActual 		
	public function getEdadActual()
	{
		list($ano,$mes,$dia) = explode("-",$this->fecha_nacimiento_benef);
		$edad  = date("Y") - $ano;
		$mes_diferencia = date("m") - $mes;
		$dia_diferencia   = date("d") - $dia;
		if ($dia_diferencia < 0 && $mes_diferencia <= 0){
			$edad--;
		}

		return($edad);
	}




	#	Metodo getEdadFrom 		
	public function getEdadFrom($fecha)
	{
		list($ano,$mes,$dia) = explode("-",$fecha);
		list($anoNac,$mesNac,$diaNac) = explode("-",$this->fecha_nacimiento_benef);
		$edad  = $ano - $anoNac ;
		$mes_diferencia = $mesNac - $mes;
		$dia_diferencia   = $diaNac - $dia;
		if ($dia_diferencia < 0 && $mes_diferencia <= 0){
			$edad--;
		}

		return($edad);
	}




	#	Metodo pasarAPendiente 		
	public function pasarAPendiente($riesgo = -1)
	{
		$sql = $this->getSqlPasarAPendiente($riesgo);
		$result = sql($sql);
	}




	### SQLS
	
	# Documentacion para metodo getSQlInsert
	public function getSQlInsert(){
		$sql = "
			INSERT INTO uad.beneficiarios(
			            id_beneficiarios, estado_envio, clave_beneficiario, tipo_transaccion, 
			            apellido_benef, nombre_benef, clase_documento_benef, tipo_documento, 
			            numero_doc, id_categoria, sexo, fecha_nacimiento_benef, provincia_nac, 
			            localidad_nac, pais_nac, indigena, id_tribu, id_lengua, alfabeta, 
			            estudios, anio_mayor_nivel, tipo_doc_madre, nro_doc_madre, apellido_madre, 
			            nombre_madre, alfabeta_madre, estudios_madre, anio_mayor_nivel_madre, 
			            tipo_doc_padre, nro_doc_padre, apellido_padre, nombre_padre, 
			            alfabeta_padre, estudios_padre, anio_mayor_nivel_padre, tipo_doc_tutor, 
			            nro_doc_tutor, apellido_tutor, nombre_tutor, alfabeta_tutor, 
			            estudios_tutor, anio_mayor_nivel_tutor, fecha_diagnostico_embarazo, 
			            semanas_embarazo, fecha_probable_parto, fecha_efectiva_parto, 
			            cuie_ea, cuie_ah, menor_convive_con_adulto, calle, numero_calle, 
			            piso, dpto, manzana, entre_calle_1, entre_calle_2, telefono, 
			            departamento, localidad, municipio, barrio, cod_pos, observaciones, 
			            fecha_inscripcion, fecha_carga, usuario_carga, activo, score_riesgo, 
			            mail, celular, otrotel, estadoest, fum, obsgenerales, estadoest_madre, 
			            tipo_ficha, responsable, discv, disca, discmo, discme, otradisc, 
			            estadoest_padre, estadoest_tutor, menor_embarazada, apellido_benef_otro, 
			            nombre_benef_otro, fecha_verificado, usuario_verificado, apellidoagente, 
			            nombreagente, centro_inscriptor, dni_agente, edades, fallecido)
			    VALUES (
			            ".$this->id_beneficiarios.",
			            ".$this->estado_envio.",
			            ".$this->clave_beneficiario.",
			            ".$this->tipo_transaccion.",

			            ".$this->apellido_benef.",
			            ".$this->nombre_benef.",
			            ".$this->clase_documento_benef.",
			            ".$this->tipo_documento.",

			            ".$this->numero_doc.",
			            ".$this->id_categoria.",
			            ".$this->sexo.",
			            ".$this->fecha_nacimiento_benef.",
			            ".$this->provincia_nac.",

			            ".$this->localidad_nac.",
			            ".$this->pais_nac.",
			            ".$this->indigena.",
			            ".$this->id_tribu.",
			            ".$this->id_lengua.",
			            ".$this->alfabeta.",

			            ".$this->estudios.",
			            ".$this->anio_mayor_nivel.",
			            ".$this->tipo_doc_madre.",
			            ".$this->nro_doc_madre.",
			            ".$this->apellido_madre.",

			            ".$this->nombre_madre.",
			            ".$this->alfabeta_madre.",
			            ".$this->estudios_madre.",
			            ".$this->anio_mayor_nivel_madre.",

			            ".$this->tipo_doc_padre.",
			            ".$this->nro_doc_padre.",
			            ".$this->apellido_padre.",
			            ".$this->nombre_padre.",

			            ".$this->alfabeta_padre.",
			            ".$this->estudios_padre.",
			            ".$this->anio_mayor_nivel_padre.",
			            ".$this->tipo_doc_tutor.",

			            ".$this->nro_doc_tutor.",
			            ".$this->apellido_tutor.",
			            ".$this->nombre_tutor.",
			            ".$this->alfabeta_tutor.",

			            ".$this->estudios_tutor.",
			            ".$this->anio_mayor_nivel_tutor.",
			            ".$this->fecha_diagnostico_embarazo.",

			            ".$this->semanas_embarazo.",
			            ".$this->fecha_probable_parto.",
			            ".$this->fecha_efectiva_parto.",

			            ".$this->cuie_ea.",
			            ".$this->cuie_ah.",
			            ".$this->menor_convive_con_adulto.",
			            ".$this->calle.",
			            ".$this->numero_calle.",

			            ".$this->piso.",
			            ".$this->dpto.",
			            ".$this->manzana.",
			            ".$this->entre_calle_1.",
			            ".$this->entre_calle_2.",
			            ".$this->telefono.",

			            ".$this->departamento.",
			            ".$this->localidad.",
			            ".$this->municipio.",
			            ".$this->barrio.",
			            ".$this->cod_pos.",
			            ".$this->observaciones.",

			            ".$this->fecha_inscripcion.",
			            ".$this->fecha_carga.",
			            ".$this->usuario_carga.",
			            ".$this->activo.",
			            ".$this->score_riesgo.",

			            ".$this->mail.",
			            ".$this->celular.",
			            ".$this->otrotel.",
			            ".$this->estadoestbeneficiario.",
			            ".$this->fum.",
			            ".$this->obsgenerales.",
			            ".$this->estadoest_madre.",

			            ".$this->tipo_ficha.",
			            ".$this->responsable.",
			            ".$this->discv.",
			            ".$this->disca.",
			            ".$this->discmo.",
			            ".$this->discme.",
			            ".$this->otradisc.",

			            ".$this->estadoest_padre.",
			            ".$this->estadoest_tutor.",
			            ".$this->menor_embarazada.",
			            ".$this->apellido_benef_otro.",

			            ".$this->nombre_benef_otro.",
			            ".$this->fecha_verificado.",
			            ".$this->usuario_verificado.",
			            ".$this->apellidoagente.",

			            ".$this->nombreagente.",
			            ".$this->centro_inscriptor.",
			            ".$this->dni_agente.",
			            ".$this->edades.",
			            ".$this->fallecido.")
		";
		
		return($sql);
	}

	# Documentacion para metodo getSQlSelect
	public function getSQlSelect(){
		
			$sql = "
			SELECT id_beneficiarios, estado_envio, clave_beneficiario, tipo_transaccion, 
			       apellido_benef, nombre_benef, clase_documento_benef, tipo_documento, 
			       numero_doc, id_categoria, sexo, fecha_nacimiento_benef, provincia_nac, 
			       localidad_nac, pais_nac, indigena, id_tribu, id_lengua, alfabeta, 
			       estudios, anio_mayor_nivel, tipo_doc_madre, nro_doc_madre, apellido_madre, 
			       nombre_madre, alfabeta_madre, estudios_madre, anio_mayor_nivel_madre, 
			       tipo_doc_padre, nro_doc_padre, apellido_padre, nombre_padre, 
			       alfabeta_padre, estudios_padre, anio_mayor_nivel_padre, tipo_doc_tutor, 
			       nro_doc_tutor, apellido_tutor, nombre_tutor, alfabeta_tutor, 
			       estudios_tutor, anio_mayor_nivel_tutor, fecha_diagnostico_embarazo, 
			       semanas_embarazo, fecha_probable_parto, fecha_efectiva_parto, 
			       cuie_ea, cuie_ah, menor_convive_con_adulto, calle, numero_calle, 
			       piso, dpto, manzana, entre_calle_1, entre_calle_2, telefono, 
			       departamento, localidad, municipio, barrio, cod_pos, observaciones, 
			       fecha_inscripcion, fecha_carga, usuario_carga, activo, score_riesgo, 
			       mail, celular, otrotel, estadoest, fum, obsgenerales, estadoest_madre, 
			       tipo_ficha, responsable, discv, disca, discmo, discme, otradisc, 
			       estadoest_padre, estadoest_tutor, menor_embarazada, apellido_benef_otro, 
			       nombre_benef_otro, fecha_verificado, usuario_verificado, apellidoagente, 
			       nombreagente, centro_inscriptor, dni_agente, edades, fallecido
			  FROM uad.beneficiarios";
		
		
		return($sql);
	}

	#	Metodo getSQlSelectWhere 		
	public function getSQlSelectWhere($where)
	{

		$sql = "
			SELECT id_beneficiarios, estado_envio, clave_beneficiario, tipo_transaccion, 
			       apellido_benef, nombre_benef, clase_documento_benef, tipo_documento, 
			       numero_doc, id_categoria, sexo, fecha_nacimiento_benef, provincia_nac, 
			       localidad_nac, pais_nac, indigena, id_tribu, id_lengua, alfabeta, 
			       estudios, anio_mayor_nivel, tipo_doc_madre, nro_doc_madre, apellido_madre, 
			       nombre_madre, alfabeta_madre, estudios_madre, anio_mayor_nivel_madre, 
			       tipo_doc_padre, nro_doc_padre, apellido_padre, nombre_padre, 
			       alfabeta_padre, estudios_padre, anio_mayor_nivel_padre, tipo_doc_tutor, 
			       nro_doc_tutor, apellido_tutor, nombre_tutor, alfabeta_tutor, 
			       estudios_tutor, anio_mayor_nivel_tutor, fecha_diagnostico_embarazo, 
			       semanas_embarazo, fecha_probable_parto, fecha_efectiva_parto, 
			       cuie_ea, cuie_ah, menor_convive_con_adulto, calle, numero_calle, 
			       piso, dpto, manzana, entre_calle_1, entre_calle_2, telefono, 
			       departamento, localidad, municipio, barrio, cod_pos, observaciones, 
			       fecha_inscripcion, fecha_carga, usuario_carga, activo, score_riesgo, 
			       mail, celular, otrotel, estadoest, fum, obsgenerales, estadoest_madre, 
			       tipo_ficha, responsable, discv, disca, discmo, discme, otradisc, 
			       estadoest_padre, estadoest_tutor, menor_embarazada, apellido_benef_otro, 
			       nombre_benef_otro, fecha_verificado, usuario_verificado, apellidoagente, 
			       nombreagente, centro_inscriptor, dni_agente, edades, fallecido
			  FROM uad.beneficiarios
			  WHERE ".$where."";

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

	
	#	Metodo getSqlPasarAPendiente 		
	public function getSqlPasarAPendiente($puntajeRiesgo = -1)
	{
		if ($puntajeRiesgo > 0) {
			$sql = "UPDATE uad.beneficiarios set estado_envio='p',score_riesgo=".$puntajeRiesgo." 
							where clave_beneficiario='".$this->clave_beneficiario."'";
		} else {
			$sql = "UPDATE uad.beneficiarios set estado_envio='p' where clave_beneficiario='".$this->clave_beneficiario."'";
		}
		
		
		return($sql);
	}


	#	Metodo Automata 		
	public function Automata($where)
	{
		$sql = $this->getSqlSelectWhere($where);
		$result = sql($sql);
		$this->construirResult($result);
	}
		

}






/**
* 
*/
class BeneficiariosColeccion

{
	var $beneficiarioRegistro = array();
	var $beneficiarios = '';


	function __construct()
	{
		
		$this->beneficiarioRegistro = new Beneficiario();

	}
	



	### SETTERS

	# Documentacion para el metodo getBeneficiarios 		
	public function getBeneficiarios()
	{
		return($this->beneficiarios);
	}


	#	Metodo Filtrar 		
	public function Filtrar($where = '')
	{
		if (strlen($where) > 0) {
			$sql = $this->beneficiarioRegistro->getSqlSelectWhere($where);
			
		} else {
			$sql = $this->beneficiarioRegistro->getSQlSelect();
		}

		$result = sql($sql);

		while (!$result->EOF) {

			$this->beneficiarioRegistro = new Beneficiario();
			$this->beneficiarioRegistro->construirResult($result);

			$this->beneficiarios[] = $this->beneficiarioRegistro;
			$result->MoveNext();
		}
		
		return(returnVariable);
	}

	#	Metodo Cantidad 		
	public function Cantidad()
	{
		return(count($this->beneficiarios));
	}




		

}



 ?>