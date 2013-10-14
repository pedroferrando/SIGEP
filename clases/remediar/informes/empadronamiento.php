<?php 
# Configuracion y acceso al sistema
require_once ("../../config.php");

/**
* Documentacion para Empadronamiento
* Contiene:
* 
*/

class Empadronamiento

{
	
	# Argumentos
	var $clave_beneficiario = NULL;
	var $tipo_transaccion = NULL;
	var $apellido_benef = NULL;
	var $nombre_benef = NULL;
	var $apellido_benef_otro = NULL;
	var $nombre_benef_otro = NULL;
	var $tipo_documento = NULL;
	var $numero_doc = NULL;
	var $sexo = NULL;
	var $fecha_nacimiento_benef = NULL;
	var $cuie_ea = NULL;
	var $calle = NULL;
	var $numero_calle = NULL;
	var $entre_calle_1 = NULL;
	var $entre_calle_2 = NULL;
	var $departamento = NULL;
	var $localidad = NULL;
	var $municipio = NULL;
	var $barrio = NULL;
	var $cod_pos = NULL;
	var $observaciones = NULL;
	var $fecha_inscripcion = NULL;
	var $fecha_carga = NULL;
	var $usuario_carga = NULL;
	var $apellidoagente = NULL;
	var $nombreagente = NULL;
	var $dni_agente = NULL;
	var $fallecido = NULL;
	var $rb_nroformulario = NULL;
	var $rb_fechaempadronamiento = NULL;
	var $rb_usuario_carga = NULL;
	var $rb_fecha_carga = NULL;
	var $rb_enviado = NULL;
	var $form_nroformulario = NULL;
	var $form_hta2 = NULL;
	var $form_hta3 = NULL;
	var $form_colesterol4 = NULL;
	var $form_colesterol5 = NULL;
	var $form_dmt26 = NULL;
	var $form_dmt27 = NULL;
	var $form_ecv8 = NULL;
	var $form_tabaco9 = NULL;
	var $form_puntaje_final = NULL;
	var $form_apellidoagente = NULL;
	var $form_nombreagente = NULL;
	var $form_centro_inscriptor = NULL;
	var $form_dni_agente = NULL;

	var $sqlWheres = array();

	
	function __construct()
	{
		$this->sqlWheres = array();	
	}

	public function construirResult($result)
	{
		$this->clave_beneficiario = $result->fields['clave_beneficiario'];
		$this->tipo_transaccion = $result->fields['tipo_transaccion'];
		$this->apellido_benef = $result->fields['apellido_benef'];
		$this->nombre_benef = $result->fields['nombre_benef'];
		$this->apellido_benef_otro = $result->fields['apellido_benef_otro'];
		$this->nombre_benef_otro = $result->fields['nombre_benef_otro'];
		$this->tipo_documento = $result->fields['tipo_documento'];
		$this->numero_doc = $result->fields['numero_doc'];
		$this->sexo = $result->fields['sexo'];
		$this->fecha_nacimiento_benef = $result->fields['fecha_nacimiento_benef'];
		$this->cuie_ea = $result->fields['cuie_ea'];
		$this->calle = $result->fields['calle'];
		$this->numero_calle = $result->fields['numero_calle'];
		$this->entre_calle_1 = $result->fields['entre_calle_1'];
		$this->entre_calle_2 = $result->fields['entre_calle_2'];
		$this->departamento = $result->fields['departamento'];
		$this->localidad = $result->fields['localidad'];
		$this->municipio = $result->fields['municipio'];
		$this->barrio = $result->fields['barrio'];
		$this->cod_pos = $result->fields['cod_pos'];
		$this->observaciones = $result->fields['observaciones'];
		$this->fecha_inscripcion = $result->fields['fecha_inscripcion'];
		$this->fecha_carga = $result->fields['fecha_carga'];
		$this->usuario_carga = $result->fields['usuario_carga'];
		$this->apellidoagente = $result->fields['apellidoagente'];
		$this->nombreagente = $result->fields['nombreagente'];
		$this->dni_agente = $result->fields['dni_agente'];
		$this->fallecido = $result->fields['fallecido'];
		$this->rb_nroformulario = $result->fields['rb_nroformulario'];
		$this->rb_fechaempadronamiento = $result->fields['rb_fechaempadronamiento'];
		$this->rb_usuario_carga = $result->fields['rb_usuario_carga'];
		$this->rb_fecha_carga = $result->fields['rb_fecha_carga'];
		$this->rb_enviado = $result->fields['rb_enviado'];
		$this->form_nroformulario = $result->fields['form_nroformulario'];
		$this->form_hta2 = $result->fields['form_hta2'];
		$this->form_hta3 = $result->fields['form_hta3'];
		$this->form_colesterol4 = $result->fields['form_colesterol4'];
		$this->form_colesterol5 = $result->fields['form_colesterol5'];
		$this->form_dmt26 = $result->fields['form_dmt26'];
		$this->form_dmt27 = $result->fields['form_dmt27'];
		$this->form_ecv8 = $result->fields['form_ecv8'];
		$this->form_tabaco9 = $result->fields['form_tabaco9'];
		$this->form_puntaje_final = $result->fields['form_puntaje_final'];
		$this->form_apellidoagente = $result->fields['form_apellidoagente'];
		$this->form_nombreagente = $result->fields['form_nombreagente'];
		$this->form_centro_inscriptor = $result->fields['form_centro_inscriptor'];
		$this->form_dni_agente = $result->fields['form_dni_agente'];
	}

	# Setters

	#	Documentacion para Metodo setClave_beneficiario 		
	public function setClave_beneficiario($clave_beneficiario)
	{
		$this->clave_beneficiario = $clave_beneficiario;
	}

	
	#	Documentacion para Metodo setTipo_transaccion 		
	public function setTipo_transaccion($tipo_transaccion)
	{
		$this->tipo_transaccion = $tipo_transaccion;
	}

	
	#	Documentacion para Metodo setApellido_benef 		
	public function setApellido_benef($apellido_benef)
	{
		$this->apellido_benef = $apellido_benef;
	}

	
	#	Documentacion para Metodo setNombre_benef 		
	public function setNombre_benef($nombre_benef)
	{
		$this->nombre_benef = $nombre_benef;
	}

	
	#	Documentacion para Metodo setApellido_benef_otro 		
	public function setApellido_benef_otro($apellido_benef_otro)
	{
		$this->apellido_benef_otro = $apellido_benef_otro;
	}

	
	#	Documentacion para Metodo setNombre_benef_otro 		
	public function setNombre_benef_otro($nombre_benef_otro)
	{
		$this->nombre_benef_otro = $nombre_benef_otro;
	}

	
	#	Documentacion para Metodo setTipo_documento 		
	public function setTipo_documento($tipo_documento)
	{
		$this->tipo_documento = $tipo_documento;
	}

	
	#	Documentacion para Metodo setNumero_doc 		
	public function setNumero_doc($numero_doc)
	{
		$this->numero_doc = $numero_doc;
	}

	
	#	Documentacion para Metodo setSexo 		
	public function setSexo($sexo)
	{
		$this->sexo = $sexo;
	}

	
	#	Documentacion para Metodo setFecha_nacimiento_benef 		
	public function setFecha_nacimiento_benef($fecha_nacimiento_benef)
	{
		$this->fecha_nacimiento_benef = $fecha_nacimiento_benef;
	}

	
	#	Documentacion para Metodo setCuie_ea 		
	public function setCuie_ea($cuie_ea)
	{
		$this->cuie_ea = $cuie_ea;
	}

	
	#	Documentacion para Metodo setCalle 		
	public function setCalle($calle)
	{
		$this->calle = $calle;
	}

	
	#	Documentacion para Metodo setNumero_calle 		
	public function setNumero_calle($numero_calle)
	{
		$this->numero_calle = $numero_calle;
	}

	
	#	Documentacion para Metodo setEntre_calle_1 		
	public function setEntre_calle_1($entre_calle_1)
	{
		$this->entre_calle_1 = $entre_calle_1;
	}

	
	#	Documentacion para Metodo setEntre_calle_2 		
	public function setEntre_calle_2($entre_calle_2)
	{
		$this->entre_calle_2 = $entre_calle_2;
	}

	
	#	Documentacion para Metodo setDepartamento 		
	public function setDepartamento($departamento)
	{
		$this->departamento = $departamento;
	}

	
	#	Documentacion para Metodo setLocalidad 		
	public function setLocalidad($localidad)
	{
		$this->localidad = $localidad;
	}

	
	#	Documentacion para Metodo setMunicipio 		
	public function setMunicipio($municipio)
	{
		$this->municipio = $municipio;
	}

	
	#	Documentacion para Metodo setBarrio 		
	public function setBarrio($barrio)
	{
		$this->barrio = $barrio;
	}

	
	#	Documentacion para Metodo setCod_pos 		
	public function setCod_pos($cod_pos)
	{
		$this->cod_pos = $cod_pos;
	}

	
	#	Documentacion para Metodo setObservaciones 		
	public function setObservaciones($observaciones)
	{
		$this->observaciones = $observaciones;
	}

	
	#	Documentacion para Metodo setFecha_inscripcion 		
	public function setFecha_inscripcion($fecha_inscripcion)
	{
		$this->fecha_inscripcion = $fecha_inscripcion;
	}

	
	#	Documentacion para Metodo setFecha_carga 		
	public function setFecha_carga($fecha_carga)
	{
		$this->fecha_carga = $fecha_carga;
	}

	
	#	Documentacion para Metodo setUsuario_carga 		
	public function setUsuario_carga($usuario_carga)
	{
		$this->usuario_carga = $usuario_carga;
	}

	
	#	Documentacion para Metodo setApellidoagente 		
	public function setApellidoagente($apellidoagente)
	{
		$this->apellidoagente = $apellidoagente;
	}

	
	#	Documentacion para Metodo setNombreagente 		
	public function setNombreagente($nombreagente)
	{
		$this->nombreagente = $nombreagente;
	}

	
	#	Documentacion para Metodo setDni_agente 		
	public function setDni_agente($dni_agente)
	{
		$this->dni_agente = $dni_agente;
	}

	
	#	Documentacion para Metodo setFallecido 		
	public function setFallecido($fallecido)
	{
		$this->fallecido = $fallecido;
	}

	
	#	Documentacion para Metodo setRb_nroformulario 		
	public function setRb_nroformulario($rb_nroformulario)
	{
		$this->rb_nroformulario = $rb_nroformulario;
	}

	
	#	Documentacion para Metodo setRb_fechaempadronamiento 		
	public function setRb_fechaempadronamiento($rb_fechaempadronamiento)
	{
		$this->rb_fechaempadronamiento = $rb_fechaempadronamiento;
	}

	
	#	Documentacion para Metodo setRb_usuario_carga 		
	public function setRb_usuario_carga($rb_usuario_carga)
	{
		$this->rb_usuario_carga = $rb_usuario_carga;
	}

	
	#	Documentacion para Metodo setRb_fecha_carga 		
	public function setRb_fecha_carga($rb_fecha_carga)
	{
		$this->rb_fecha_carga = $rb_fecha_carga;
	}

	
	#	Documentacion para Metodo setRb_enviado 		
	public function setRb_enviado($rb_enviado)
	{
		$this->rb_enviado = $rb_enviado;
	}

	
	#	Documentacion para Metodo setForm_nroformulario 		
	public function setForm_nroformulario($form_nroformulario)
	{
		$this->form_nroformulario = $form_nroformulario;
	}

	
	#	Documentacion para Metodo setForm_hta2 		
	public function setForm_hta2($form_hta2)
	{
		$this->form_hta2 = $form_hta2;
	}

	
	#	Documentacion para Metodo setForm_hta3 		
	public function setForm_hta3($form_hta3)
	{
		$this->form_hta3 = $form_hta3;
	}

	
	#	Documentacion para Metodo setForm_colesterol4 		
	public function setForm_colesterol4($form_colesterol4)
	{
		$this->form_colesterol4 = $form_colesterol4;
	}

	
	#	Documentacion para Metodo setForm_colesterol5 		
	public function setForm_colesterol5($form_colesterol5)
	{
		$this->form_colesterol5 = $form_colesterol5;
	}

	
	#	Documentacion para Metodo setForm_dmt26 		
	public function setForm_dmt26($form_dmt26)
	{
		$this->form_dmt26 = $form_dmt26;
	}

	
	#	Documentacion para Metodo setForm_dmt27 		
	public function setForm_dmt27($form_dmt27)
	{
		$this->form_dmt27 = $form_dmt27;
	}

	
	#	Documentacion para Metodo setForm_ecv8 		
	public function setForm_ecv8($form_ecv8)
	{
		$this->form_ecv8 = $form_ecv8;
	}

	
	#	Documentacion para Metodo setForm_tabaco9 		
	public function setForm_tabaco9($form_tabaco9)
	{
		$this->form_tabaco9 = $form_tabaco9;
	}

	
	#	Documentacion para Metodo setForm_puntaje_final 		
	public function setForm_puntaje_final($form_puntaje_final)
	{
		$this->form_puntaje_final = $form_puntaje_final;
	}

	
	#	Documentacion para Metodo setForm_apellidoagente 		
	public function setForm_apellidoagente($form_apellidoagente)
	{
		$this->form_apellidoagente = $form_apellidoagente;
	}

	
	#	Documentacion para Metodo setForm_nombreagente 		
	public function setForm_nombreagente($form_nombreagente)
	{
		$this->form_nombreagente = $form_nombreagente;
	}

	
	#	Documentacion para Metodo setForm_centro_inscriptor 		
	public function setForm_centro_inscriptor($form_centro_inscriptor)
	{
		$this->form_centro_inscriptor = $form_centro_inscriptor;
	}

	
	#	Documentacion para Metodo setForm_dni_agente 		
	public function setForm_dni_agente($form_dni_agente)
	{
		$this->form_dni_agente = $form_dni_agente;
	}

	
	#	Documentacion para Metodo setSqlWhere 		
	public function setSqlWhere($sqlWheres)
	{
		$this->sqlWhere = $sqlWheres;
	}

	

	# Getters

	#	Documentacion para Metodo getClave_beneficiario 		
	public function getClave_beneficiario()
	{
		return($this->clave_beneficiario);
	}

		
	#	Documentacion para Metodo getTipo_transaccion 		
	public function getTipo_transaccion()
	{
		return($this->tipo_transaccion);
	}

		
	#	Documentacion para Metodo getApellido_benef 		
	public function getApellido_benef()
	{
		return($this->apellido_benef);
	}

		
	#	Documentacion para Metodo getNombre_benef 		
	public function getNombre_benef()
	{
		return($this->nombre_benef);
	}

		
	#	Documentacion para Metodo getApellido_benef_otro 		
	public function getApellido_benef_otro()
	{
		return($this->apellido_benef_otro);
	}

		
	#	Documentacion para Metodo getNombre_benef_otro 		
	public function getNombre_benef_otro()
	{
		return($this->nombre_benef_otro);
	}

		
	#	Documentacion para Metodo getTipo_documento 		
	public function getTipo_documento()
	{
		return($this->tipo_documento);
	}

		
	#	Documentacion para Metodo getNumero_doc 		
	public function getNumero_doc()
	{
		return($this->numero_doc);
	}

		
	#	Documentacion para Metodo getSexo 		
	public function getSexo()
	{
		return($this->sexo);
	}

		
	#	Documentacion para Metodo getFecha_nacimiento_benef 		
	public function getFecha_nacimiento_benef()
	{
		return($this->fecha_nacimiento_benef);
	}

		
	#	Documentacion para Metodo getCuie_ea 		
	public function getCuie_ea()
	{
		return($this->cuie_ea);
	}

		
	#	Documentacion para Metodo getCalle 		
	public function getCalle()
	{
		return($this->calle);
	}

		
	#	Documentacion para Metodo getNumero_calle 		
	public function getNumero_calle()
	{
		return($this->numero_calle);
	}

		
	#	Documentacion para Metodo getEntre_calle_1 		
	public function getEntre_calle_1()
	{
		return($this->entre_calle_1);
	}

		
	#	Documentacion para Metodo getEntre_calle_2 		
	public function getEntre_calle_2()
	{
		return($this->entre_calle_2);
	}

		
	#	Documentacion para Metodo getDepartamento 		
	public function getDepartamento()
	{
		return($this->departamento);
	}

		
	#	Documentacion para Metodo getLocalidad 		
	public function getLocalidad()
	{
		return($this->localidad);
	}

		
	#	Documentacion para Metodo getMunicipio 		
	public function getMunicipio()
	{
		return($this->municipio);
	}

		
	#	Documentacion para Metodo getBarrio 		
	public function getBarrio()
	{
		return($this->barrio);
	}

		
	#	Documentacion para Metodo getCod_pos 		
	public function getCod_pos()
	{
		return($this->cod_pos);
	}

		
	#	Documentacion para Metodo getObservaciones 		
	public function getObservaciones()
	{
		return($this->observaciones);
	}

		
	#	Documentacion para Metodo getFecha_inscripcion 		
	public function getFecha_inscripcion()
	{
		return($this->fecha_inscripcion);
	}

		
	#	Documentacion para Metodo getFecha_carga 		
	public function getFecha_carga()
	{
		return($this->fecha_carga);
	}

		
	#	Documentacion para Metodo getUsuario_carga 		
	public function getUsuario_carga()
	{
		return($this->usuario_carga);
	}

		
	#	Documentacion para Metodo getApellidoagente 		
	public function getApellidoagente()
	{
		return($this->apellidoagente);
	}

		
	#	Documentacion para Metodo getNombreagente 		
	public function getNombreagente()
	{
		return($this->nombreagente);
	}

		
	#	Documentacion para Metodo getDni_agente 		
	public function getDni_agente()
	{
		return($this->dni_agente);
	}

		
	#	Documentacion para Metodo getFallecido 		
	public function getFallecido()
	{
		return($this->fallecido);
	}

		
	#	Documentacion para Metodo getRb_nroformulario 		
	public function getRb_nroformulario()
	{
		return($this->rb_nroformulario);
	}

		
	#	Documentacion para Metodo getRb_fechaempadronamiento 		
	public function getRb_fechaempadronamiento()
	{
		return($this->rb_fechaempadronamiento);
	}

		
	#	Documentacion para Metodo getRb_usuario_carga 		
	public function getRb_usuario_carga()
	{
		return($this->rb_usuario_carga);
	}

		
	#	Documentacion para Metodo getRb_fecha_carga 		
	public function getRb_fecha_carga()
	{
		return($this->rb_fecha_carga);
	}

		
	#	Documentacion para Metodo getRb_enviado 		
	public function getRb_enviado()
	{
		return($this->rb_enviado);
	}

		
	#	Documentacion para Metodo getForm_nroformulario 		
	public function getForm_nroformulario()
	{
		return($this->form_nroformulario);
	}

		
	#	Documentacion para Metodo getForm_hta2 		
	public function getForm_hta2()
	{
		return($this->form_hta2);
	}

		
	#	Documentacion para Metodo getForm_hta3 		
	public function getForm_hta3()
	{
		return($this->form_hta3);
	}

		
	#	Documentacion para Metodo getForm_colesterol4 		
	public function getForm_colesterol4()
	{
		return($this->form_colesterol4);
	}

		
	#	Documentacion para Metodo getForm_colesterol5 		
	public function getForm_colesterol5()
	{
		return($this->form_colesterol5);
	}

		
	#	Documentacion para Metodo getForm_dmt26 		
	public function getForm_dmt26()
	{
		return($this->form_dmt26);
	}

		
	#	Documentacion para Metodo getForm_dmt27 		
	public function getForm_dmt27()
	{
		return($this->form_dmt27);
	}

		
	#	Documentacion para Metodo getForm_ecv8 		
	public function getForm_ecv8()
	{
		return($this->form_ecv8);
	}

		
	#	Documentacion para Metodo getForm_tabaco9 		
	public function getForm_tabaco9()
	{
		return($this->form_tabaco9);
	}

		
	#	Documentacion para Metodo getForm_puntaje_final 		
	public function getForm_puntaje_final()
	{
		return($this->form_puntaje_final);
	}

		
	#	Documentacion para Metodo getForm_apellidoagente 		
	public function getForm_apellidoagente()
	{
		return($this->form_apellidoagente);
	}

		
	#	Documentacion para Metodo getForm_nombreagente 		
	public function getForm_nombreagente()
	{
		return($this->form_nombreagente);
	}

		
	#	Documentacion para Metodo getForm_centro_inscriptor 		
	public function getForm_centro_inscriptor()
	{
		return($this->form_centro_inscriptor);
	}

		
	#	Documentacion para Metodo getForm_dni_agente 		
	public function getForm_dni_agente()
	{
		return($this->form_dni_agente);
	}

		
	#	Documentacion para Metodo getSqlWhere 		
	public function getSqlWhere()
	{
		return($this->sqlWhere);
	}

		


	# Procesos SQL

	#	Documentacion para Metodo Insertar
	public function Insertar()
	{
		return(False);
	}


	#	Documentacion para Metodo Actualizar
	public function Actualizar()
	{
		return(False);
	}


	#	Documentacion para Metodo Eliminar
	public function Eliminar()
	{
		return(False);
	}


	#	Documentacion para Metodo Seleccionar
	public function Seleccionar()
	{
		return(False);
	}



	# Sentencias SQL

	#	Documentacion para Metodo getSqlInsert
	public function getSqlInsert()
	{
		$sql = "";

		return($sql);
	}

	#	Documentacion para Metodo getSqlUpdate
	public function getSqlUpdate()
	{
		$sql = "";

		if (count($this->sqlWhere) > 0) {
			foreach ($this->sqlWhere as $where) {
					$sql .= $where;
				}	
		}
		
		return($sql);
	}

	#	Documentacion para Metodo getSqlDelete
	public function getSqlDelete()
	{
		$sql = "";

		if (count($this->sqlWhere) > 0) {
			foreach ($this->sqlWhere as $where) {
					$sql .= $where;
				}	
		}
		
		return($sql);
	}

	#	Documentacion para Metodo getSqlSelect
	public function getSqlSelect()
	{
		$sql = "
		SELECT 
		b.clave_beneficiario AS clave_beneficiario,
		b.tipo_transaccion AS tipo_transaccion,
		(CASE
			WHEN b.nombre_benef_otro IS NOT NULL THEN (b.nombre_benef || ' ' || b.nombre_benef_otro)
			WHEN b.nombre_benef_otro IS NULL THEN b.nombre_benef
		END) AS nombre_benef,

		(CASE
			WHEN b.apellido_benef_otro IS NOT NULL THEN (TRIM(b.apellido_benef) || ' ' || TRIM(b.apellido_benef_otro))
			WHEN b.apellido_benef_otro IS NULL THEN TRIM(b.apellido_benef)
		END) AS apellido_benef,

		b.tipo_documento AS tipo_documento,
		b.numero_doc AS numero_doc,
		b.sexo AS sexo,
		b.fecha_nacimiento_benef AS fecha_nacimiento_benef,
		b.cuie_ea AS cuie_ea,
		b.calle AS calle,
		b.numero_calle AS numero_calle,
		b.entre_calle_1 AS entre_calle_1,
		b.entre_calle_2 AS entre_calle_2,
		b.departamento AS departamento,
		b.localidad AS localidad,
		b.municipio AS municipio,
		b.barrio AS barrio,
		b.cod_pos AS cod_pos,
		TRIM(b.observaciones) AS observaciones,
		b.fecha_inscripcion AS fecha_inscripcion,
		b.fecha_carga AS fecha_carga,
		b.usuario_carga AS usuario_carga,
		b.apellidoagente AS apellidoagente,
		b.nombreagente AS nombreagente,
		b.dni_agente AS dni_agente,
		b.fallecido AS fallecido,

		rb.nroformulario AS rb_nroformulario,
		rb.fechaempadronamiento AS rb_fechaempadronamiento,
		rb.usuario_carga AS rb_usuario_carga,
		rb.fecha_carga AS rb_fecha_carga,
		rb.enviado AS rb_enviado,

		form.nroformulario AS form_nroformulario,
		form.hta2 AS form_hta2,
		form.hta3 AS form_hta3,
		form.colesterol4 AS form_colesterol4,
		form.colesterol5 AS form_colesterol5,
		form.dmt26 AS form_dmt26,
		form.dmt27 AS form_dmt27,
		form.ecv8 AS form_ecv8,
		form.tabaco9 AS form_tabaco9,
		form.puntaje_final AS form_puntaje_final,
		form.centro_inscriptor AS form_centro_inscriptor,
		TRIM(upper(form.apellidoagente)) AS form_apellidoagente,
		TRIM(upper(form.nombreagente)) AS form_nombreagente,
		TRIM(form.dni_agente) AS form_dni_agente,
		(select array[TRIM(nombre), TRIM(apellido), TRIM(fechanac::text), TRIM(dni::text)] FROM remediar.promotores where TRIM(dni::text) = TRIM(form.dni_agente) limit 1) AS prom_datos


		FROM uad.beneficiarios b
		INNER JOIN uad.remediar_x_beneficiario rb ON rb.clavebeneficiario = b.clave_beneficiario
		INNER JOIN remediar.formulario form ON form.nroformulario = rb.nroformulario

		";

		if (count($this->sqlWhere) > 0) {
			foreach ($this->sqlWhere as $where) {
					$sql .= $where;
				}	
		}
		
		return($sql);
	}



	# Documentacion para metodo "where" con $field1, $operator, $field2 como parametros
	public function SQLWhere($field1, $operator = "", $field2="", $statement = "AND"){
		if(!(is_array($this->sqlWheres))){
			$this->sqlWheres = array();
		}

		if (count($this->sqlWhere) < 1) {
			$sqlWheres = "WHERE (".$field1." ".$operator." ".$field2.") \n";
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


}




?>