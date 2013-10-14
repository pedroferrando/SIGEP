<?php

#	BuildIn Functions


function SqlOperadores($fechaInicio="", $fechaFin="", $filtroWhere = "")
{
	$sql = "";
	
	if (strlen($fechaInicio) < 1 or strlen($fechaFin) < 1)
	{
		if (strlen($filtroWhere) > 0) {
			$filtroWhere = "where u.id_usuario = ".$filtroWhere;
		}
		$sql = "select u.nombre as operador_nombre, u.apellido as operador_apellido, u.id_usuario as operador_id, 
                        rb.fecha_carga::date as fechacarga, b.nombre_benef as beneficiario_nombre, b.apellido_benef as beneficiario_apellido, 
                        b.tipo_documento as beneficiario_tipodocumento, b.numero_doc as beneficiario_numerodocumento,
                        form.nombreagente as agente_nombre, form.apellidoagente as agente_apellido, form.centro_inscriptor as efector_cuie,
                        efe.nombreefector as efector_nombre, rl.codremediar as efector_codremediar
                        from uad.remediar_x_beneficiario rb
							inner join sistema.usuarios u on rb.usuario_carga = cast(u.id_usuario as text)
							inner join remediar.formulario form on form.nroformulario = rb.nroformulario
							inner join uad.beneficiarios b on b.clave_beneficiario = rb.clavebeneficiario
							left join facturacion.smiefectores efe on efe.cuie = form.centro_inscriptor
							left join general.relacioncodigos rl on rl.cuie = form.centro_inscriptor
						".$filtroWhere."
						order by u.apellido, u.nombre";
		return($sql);	
		
	} 
	else {
		if (strlen($filtroWhere) > 0) {
			$filtroWhere = "and u.id_usuario = ".$filtroWhere;
		}
		$sql = "select u.nombre as operador_nombre, u.apellido as operador_apellido, u.id_usuario as operador_id, 
                        rb.fecha_carga::date as fechacarga, b.nombre_benef as beneficiario_nombre, b.apellido_benef as beneficiario_apellido, 
                        b.tipo_documento as beneficiario_tipodocumento, b.numero_doc as beneficiario_numerodocumento,
                        form.nombreagente as agente_nombre, form.apellidoagente as agente_apellido, form.centro_inscriptor as efector_cuie,
                        efe.nombreefector as efector_nombre, rl.codremediar as efector_codremediar
                        from uad.remediar_x_beneficiario rb
							inner join sistema.usuarios u on rb.usuario_carga = cast(u.id_usuario as text)
							inner join remediar.formulario form on form.nroformulario = rb.nroformulario
							inner join uad.beneficiarios b on b.clave_beneficiario = rb.clavebeneficiario
							left join facturacion.smiefectores efe on efe.cuie = form.centro_inscriptor
							left join general.relacioncodigos rl on rl.cuie = form.centro_inscriptor 
						where rb.fecha_carga between '".$fechaInicio."' and '".$fechaFin."'
						".$filtroWhere."
						order by u.apellido, u.nombre";
		return($sql);
		
	}
	
	
}

#	Metodo SqlOperadoresNombres 		
function SqlOperadoresListado()
{
	$sql = "
		select distinct(usuario_carga) as id_operador, u.nombre as operador_nombre, u.apellido as operador_apellido
		from uad.remediar_x_beneficiario rb
			inner join sistema.usuarios u on u.id_usuario::text = rb.usuario_carga
		order by 3,2";
	
	return($sql);
}


# Documentacion para metodo "SqlOperadoresValidaciones" con $idOperador como parámetros
function SqlOperadoresValidaciones($idOperador, $fechaDesde='', $fechaHasta=''){
	if (strlen($fechaDesde) < 1 or strlen($fechaHasta) < 1) {
			$sql = "select 
				fecha_verificado,
				nombre_benef as beneficiario_nombre,
				apellido_benef as beneficiario_apellido,
				tipo_documento as beneficiario_tipodocumento,
				numero_doc as beneficiario_numerodocumento
			from uad.beneficiarios 
			where usuario_verificado = '".$idOperador."'
			";
	} else {
			$sql = "select 
				fecha_verificado,
				nombre_benef as beneficiario_nombre,
				apellido_benef as beneficiario_apellido,
				tipo_documento as beneficiario_tipodocumento,
				numero_doc as beneficiario_numerodocumento
			from uad.beneficiarios 
			where usuario_verificado = '".$idOperador."'
			and fecha_verificado between '".$fechaDesde."' and '".$fechaHasta."'
			";
	}
	
	return($sql);
}





#doc
#	classname:	Operador
#	scope:		PUBLIC
#
#/doc

class Operador 
{
	#	internal variables
	var $nombre;
	var $apellido;
	var $idOperador;
	var $cargas;
	var $fechasCargas;
		#	Beneficiarios de las cargas
	var $beneficiariosNombre;
	var $beneficiariosApellido;
	var $beneficiariosTiposDocumentos;
	var $beneficiariosDocumentos;

	#	Efectores
	var $efectoresCodRemediar;
	var $efectoresNombre;
	var $efectoresCuieNacer;

	#	Agentes de las planillas
	var $agentesNombre;
	var $agentesApellido;

	# Validaciones
	var $validaciones;	


	
	#	Constructor
	function __construct ()
	{
		$this->nombre = "";
		$this->apellido = "";
		$this->idOperador = "";
		$this->cargas = "";
		$this->fechasCargas = array();

		#	Beneficiarios de las cargas
		$this->beneficiariosNombre = array();
		$this->beneficiariosApellido = array();
		$this->beneficiariosTiposDocumentos = array();
		$this->beneficiariosDocumentos = array();

		#	Efectores
		$this->efectoresCodRemediar = array();
		$this->efectoresNombre = array();
		$this->efectpresCuieNacer = array();

		#	Agentes de las planillas
		$this->agentesNombre = array();
		$this->agentesApellido = array();

		#	Validaciones
		$this->validaciones = 0;
		$this->validacionFecha = array();
		$this->validacionesBeneficiariosNombre = array();
		$this->validacionesBeneficiariosApellido = array();
		$this->validacionesBeneficiariosTiposDocumentos = array();
		$this->validacionesBeneficiariosDocumentos = array();

	}


	#	Valida si el usuario pasado por argumento es el mismo
	public function isOperador($clave)
	{
		$rturn = False;
		
		if ($clave == $this->idOperador)
		{
			$rturn = True;
			
		}

		return($rturn);
	}

	#	Construye al operador en base a un elemento array tipo Diccionario
	public function Construir($arreglo)
	{
		$this->nombre = $arreglo['operador_nombre'];
		$this->apellido = $arreglo['operador_apellido'];
		$this->idOperador = $arreglo['operador_id'];
	}


	#	Construye al operador en base a un elemento Result
	public function ConstruirResult($result)
	{
		$this->nombre = $result->fields['operador_nombre'];
		$this->apellido = $result->fields['operador_apellido'];
		$this->idOperador = $result->fields['operador_id'];
	}




	#	Agrega una nueva carga al operador
	public function NuevaCarga($result)
	{
		#	Cantidad de cargas
		$this->cargas +=1;

		#	Fecha de la carga
		$this->fechasCargas[] = $result->fields['fechacarga'];

		#	Beneficiarios de las cargas
		$this->beneficiariosNombre[] = $result->fields['beneficiario_nombre'];
		$this->beneficiariosApellido[] = $result->fields['beneficiario_apellido'];
		$this->beneficiariosTiposDocumentos[] = $result->fields['beneficiario_tipodocumento'];
		$this->beneficiariosDocumentos[] = $result->fields['beneficiario_numerodocumento'];

		#	Efectores
		$this->efectoresCodRemediar[] = $result->fields['efector_codremediar'];
		$this->efectoresNombre[] = $result->fields['efector_nombre'];
		$this->efectoresCuieNacer[] = $result->fields['efector_cuie'];


		#	Agentes de las planillas
		$this->agentesNombre[] = $result->fields['agente_nombre'];
		$this->agentesApellido[] = $result->fields['agente_apellido'];
	}

	#	Agrega una nueva validacion
	public function NuevaValidacion($result)
	{
		#	Cantidad de cargas
		$this->validaciones +=1;

		#	Fecha de la carga
		$this->validacionFecha[] = $result->fields['fecha_verificado'];

		#	Beneficiarios de las cargas
		$this->validacionesBeneficiariosNombre[] = $result->fields['beneficiario_nombre'];
		$this->validacionesBeneficiariosApellido[] = $result->fields['beneficiario_apellido'];
		$this->validacionesBeneficiariosTiposDocumentos[] = $result->fields['beneficiario_tipodocumento'];
		$this->validacionesBeneficiariosDocumentos[] = $result->fields['beneficiario_numerodocumento'];


	}





	#	GETTERS ######################
	public function getValidacionesCantidad(){
		return($this->validaciones);
	}

	public function getCargasCantidad()
	{
		return($this->cargas);
	}

	public function getNombreCompleto()
	{
		return($this->nombre.", ".$this->apellido);
	}

	public function getNombre()
	{
		return($this->nombre);
	}

	public function getApellido()
	{
		return($this->apellido);
	}

	public function getId()
	{
		return($this->idOperador);
	}

	public function getSqlOperadorId()
	{
		$sql = "select * from uad.remediar_x_beneficiario where usuario_carga = '".$this->idOperador."'";
		return($sql);
	}

	#	Metodo getCargas 		
	public function getCargasBeneficiariosNombre($pos = -1)
	{
		if($pos < 0){
			return($this->beneficiariosNombre);
		}else{
			return($this->beneficiariosNombre[$pos]);
		}
	}

	#	Metodo getCargasBeneficiariosApellido 		
	public function getCargasBeneficiariosApellido($pos = -1)
	{
		if($pos < 0){
			return($this->beneficiariosApellido);
		}else{
			return($this->beneficiariosApellido[$pos]);
		}
	}

	#	Metodo getCargasBeneficiariosNombreCompleto
	public function getCargasBeneficiariosNombreCompleto($pos)
	{
		
		return(($this->beneficiariosApellido[$pos].", ".$this->beneficiariosNombre[$pos]));
	}

	#	Metodo getCargasBeneficiariosTipoDocumento 		
	public function getCargasBeneficiariosTipoDocumentos($pos = -1)
	{
		if($pos < 0){
			return($this->beneficiariosTiposDocumentos);
		}else{
			return($this->beneficiariosTiposDocumentos[$pos]);
		}
	}

	#	Metodo getCargasBeneficiariosDocumento 		
	public function getCargasBeneficiariosDocumentos($pos = -1)
	{
		if($pos < 0){
			return($this->beneficiariosDocumentos);
		}else{
			return($this->beneficiariosDocumentos[$pos]);
		}
	}	

	#	Metodo getCargasFechas 		
	public function getCargasFechas($pos = -1)
	{
		if($pos < 0){
			return($this->fechasCargas);
		}else{
			return($this->fechasCargas[$pos]);
		}
	}

	#	Metodo getCargasEfectorCodremediar 		
	public function getCargasEfectorCodremediar($pos = -1)
	{
		if($pos < 0){
			return($this->efectoresCodRemediar);
		}else{
			return($this->efectoresCodRemediar[$pos]);
		}
	}	

	#	Metodo getCargasEfectorNombre 		
	public function getCargasEfectorNombre($pos = -1)
	{
		if($pos < 0){
			return($this->efectoresNombre);
		}else{
			return($this->efectoresNombre[$pos]);
		}
	}	

	#	Metodo getCargasEfectorCuieNacer 		
	public function getCargasEfectorCuieNacer($pos = -1)
	{
		if($pos < 0){
			return($this->efectoresCuieNacer);
		}else{
			return($this->efectoresCuieNacer[$pos]);
		}
	}

	#	Metodo getCargasCantidadLoop 		
	public function getCargasCantidadLoop()
	{
		return(($this->cargas -1));
	}

	#	Metodo getCargas 		
	public function getCargasAgentesNombre($pos = -1)
	{
		if($pos < 0){
			return($this->agentesNombre);
		}else{
			return($this->agentesNombre[$pos]);
		}
	}

	#	Metodo getCargasBeneficiariosApellido 		
	public function getCargasAgentesApellido($pos = -1)
	{
		if($pos < 0){
			return($this->agentesApellido);
		}else{
			return($this->agentesApellido[$pos]);
		}
	}

	#	Metodo getCargasBeneficiariosNombreCompleto
	public function getCargasAgentesNombreCompleto($pos)
	{
		
		return(($this->agentesNombre[$pos].", ".$this->agentesApellido[$pos]));
	}


}
###







?>
