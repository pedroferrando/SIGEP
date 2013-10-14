<?php
    require_once("../../config.php");
    require_once("../../clases/BeneficiariosUad.php");
    require_once("../../clases/Paginacion.php");
    
    $filtro = array("b.apellido_benef" => "Apellido",
                    "b.clave_beneficiario" => "Clave de Beneficiario",
                    /*"b.cuie_ea" => "Efector",*/
                    "b.numero_doc" => "Número de Documento",
                    "b.nro_doc_madre" => "Nro doc de la Madre"
                   );
    echo $html_header;

    if (permisos_check("inicio","genera_archivo_permiso")) $permiso="";
    else $permiso="disabled";

    if($_REQUEST['buscar']){
        $request = $_REQUEST;
    }else{
        $request = decode_link($_REQUEST['p']);
    }
    
    if(isset($request)&&$request!=""){
        $limit = 30;
        $page = (isset($request[page])) ? $request[page] : 0;
        $paginacion = new Paginacion($limit, $page);
        $offset = $paginacion->getFrom();
        $arr_param_url = array("keyword" => $request['keyword'], 
                               "filter"  => $request['filter'],
                               "sort"    => $request['sort'],
                               "dir"     => $request['dir'],
                               "buscar"  => 1);
    }
    
    if($request[filter]==""){
        $request[filter] = "b.numero_doc"; // valor por defecto del select
    }
    if($request[sort]==""){
        $request[sort] = "fecins"; // valor por defecto del orden
    }
?>
    <link rel=stylesheet type='text/css' href='../../lib/css/general.css'>
    <link rel=stylesheet type='text/css' href='../../lib/css/paginacion.css'>
    <link rel=stylesheet type='text/css' href='../../lib/css/sprites.css'>
    <!--[if IE]>
        <link rel="stylesheet" type="text/css" href="../../lib/css/general.IE.css" />
    <![endif]-->
    
    <form name=form1 action="" method=POST style="position:relative;">
        <h3 align="center">LISTADO DE INSCRIPTOS</h3>
        <table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
            <tr>
                <td align=center>
                    <input type="text" maxlength="150" size="20" value="<?php echo $request[keyword]; ?>" name="keyword">
                    <b> en: </b>
                    <select name="filter">
                        <?php foreach($filtro as $key => $val){ ?>
                            <option value="<?php echo $key; ?>"
                                    <?php if($key==$request[filter]){ ?>
                                    selected="selected"
                                    <?php } ?>
                                >
                                <?php echo $val; ?>
                            </option>
                        <?php } ?>
                    </select>
                    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
                    &nbsp;&nbsp;<input type='button' name="nuevo" value='Nuevo Dato' onclick="document.location='ins_busqueda.php'">
                    &nbsp;&nbsp;<input type=submit name="generarnino" value='Generar Archivo' <?=$permiso?>>
                </td>
            </tr>
        </table>
        <p></p>
        
<?php 
        if(isset($request) && $request!="" && $request['keyword']!=""){
            $resTotal = sql(BeneficiariosUadColeccion::getSQLCountListadoInscriptos($request,$filtro));
            $total = $resTotal->fields['total'];
            $sql = BeneficiariosUadColeccion::getSQLListadoInscriptos($request,$filtro,$request[sort],$request[dir],$limit,$offset);
            $result = sql($sql) or die;
            include('ins_listado_body.php');
            ?>
            <!-- cuadro de referencia -->
            <div class="referencia" id="position-fixed-bottom">
                <div>
                    <span class="sprite-gral icon-checkbox-checked"></span>
                    <label>Enviado</label>
                </div>
                <div>
                    <span class="sprite-gral icon-spam"></span>
                    <label>Pendiente</label>
                </div>
                <div>
                    <span class="sprite-gral icon-checkbox-unchecked"></span>
                    <label>No Enviado</label>
                </div>
                <div>
                    <span class="sprite-gral icon-check-alt"></span>
                    <label>Beneficiario Activo</label>
                </div>
                <div>
                    <span class="sprite-gral icon-x-altx-alt"></span>
                    <label>Beneficiario Inactivo</label>
                </div>
                <div>
                    <span class="sprite-gral icon-minus-alt"></span>
                    <label>Beneficiario No Empadronado</label>
                </div>
            </div>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <br style="clear: both;">
            <?php
        }
    
    
//Generar Archivo A - Plan Sumar
if ($_POST['generarnino']){
        $usuario = $_ses_user['id'];
	$resultN=sql("SELECT codigo_uad,codigo_ci FROM uad.parametros")or die;
    	$resultN->movefirst();
    	
        while (!$resultN->EOF) { //iterar por UADs
            $contenido = '';
            /* Armar nombre del Archivo A
            conseguir ultima parte, secuencia final.
             */
             $seq="select max(id_archivos_enviados) as seq_archivo from uad.archivos_enviados";
             $resultseq = sql($seq) or die;
             $resultseq->movefirst();
             $seq =$resultseq->fields['seq_archivo'] + 1;
             if (strlen($seq) < 5) {$seq = str_repeat("0",5-strlen($seq)).$seq;}
            // Fin datos para armar nombre de archivo A
 
            $uad = $resultN->fields['codigo_uad'];
            $ci = $resultN->fields['codigo_ci'];
            
            $estado= " = 'n' ";
            if ($uad=='800'){
                $estado=" in ('p','n') ";    
            }
            $queryBen = "SELECT b.*, efe_conv.nombre,
                            CASE WHEN s.clavebeneficiario is null THEN 0 ELSE 1 END as existe
                            FROM uad.beneficiarios b
                            LEFT JOIN nacer.efe_conv ON b.cuie_ea = efe_conv.cuie
                            LEFT JOIN nacer.smiafiliados s ON b.clave_beneficiario = s.clavebeneficiario
                            WHERE b.estado_envio $estado AND b.cod_uad = '$uad'";
            $result1 = sql($queryBen) or die;
            $result1->movefirst();
            
            $user = $result1->fields['usuario_carga'];
            if (!$result1->EOF) {

                    $cod_prov = '20';                    
                    $id_user = $usuario;
                    /////HEADER
                    $contenido.="H"; //Header
                    $contenido.=chr(9);
                    $contenido.=date("Y-m-d"); //Fecha de Generacion del archivo
                    $contenido.=chr(9);
                    $contenido.=$id_user; //Usuario que genera el archivo
                    $contenido.=chr(9);
                    $contenido.=$cod_prov; //Codigo de Provincia
                    $contenido.=chr(9);
                    $contenido.=$uad; //UAD (Unidad de Alta de Datos)
                    $contenido.=chr(9);
                    $contenido.=$ci;//Codigo CI
                    $contenido.=chr(9);
                    $contenido.=$seq;//Secuencia del Archivo
                    $contenido.=chr(9);
                    $contenido.="4.6";//version del aplicativo (el aplicativo se refiere a otro - ver con Bettina)
                    $contenido.=chr(9);
                    $contenido.="\n";   

                    //genero nombre de archivo
                    $filename= 'A'.$cod_prov.$uad.$ci.$seq.'.txt';

                    //creo y abro el archivo
                    if (!$handle = fopen($filename, "w")) { //'a'
                            echo "No se Puede abrir ($filename)";
                            exit;
                    }else {
                            ftruncate($handle,filesize($filename));
                    }
                    // fin gen archivo, sigo con la cadenas			


                    $where=0;
                    while (!$result1->EOF) {
                            $where.=',';

                            ///////DATOS
                            $contenido.="D";
                            $contenido.=chr(9);
                            $clave_beneficiario = $result1->fields['clave_beneficiario'];
                            /*$where.=$id_beneficiario;*/
                            
                            if ($where=='0,'){
                                $id_beneficiario = $result1->fields['id_beneficiarios'];
                            }    
                            //if (strlen($id_beneficiario) < 16) {$id_beneficiario = str_repeat("0",16-strlen($id_beneficiario)).$id_beneficiario;}
                            $where.=$result1->fields['id_beneficiarios'];
                            $contenido.=$clave_beneficiario;
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['apellido_benef'].' '.$result1->fields['apellido_benef_otro'];	//30	Uad.Beneficiarios.apellido
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['nombre_benef'].' '.$result1->fields['nombre_benef_otro'];	//30	Uad.Beneficiarios.nombre
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['tipo_documento'];	//5	Sigla (DNI, CUIL, etc)
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['clase_documento_benef'];	//1	Propio o Ajeno? Si es ajeno, seria el dni de quien hace el tramite?
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['numero_doc'];	//12	
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['sexo'];	//1	M / F
                            $contenido.=chr(9);
                            $id_categoria = $result1->fields['id_categoria'];
                            $contenido.=$id_categoria;	//1	Valores de 1 a 4
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['fecha_nacimiento_benef'];	//10	AAAA-MM-DD (Año, Mes, Día)
                            $contenido.=chr(9);
                            $indigena = $result1->fields['indigena'];
                            $contenido.=$indigena ;	//1	S/N
                            $contenido.=chr(9);
                            $id = $result1->fields['id_lengua'];
                            if (is_numeric($id) == 0) { $id = 0;}
                            $contenido.=$id;	//5	Número de identificación de lengua
                            $contenido.=chr(9);
                            $id = $result1->fields['id_tribu'];
                            if (is_numeric($id) == 0) { $id = 0;}
                            //$tribu = str_replace(null,0,$result1->fields['id_tribu']);
                            $contenido.=$id;	//5	Número de tribu
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['tipo_doc_madre'];	//5	
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['nro_doc_madre'];	//12	
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['apellido_madre'];	//30	
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['nombre_madre'];	//30	
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['tipo_doc_padre'];	//5	
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['nro_doc_padre'];	//12	
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['apellido_padre'];	//30	
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['nombre_padre'];	//30	
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['tipo_doc_tutor'];	//5	
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['nro_doc_tutor'];	//12	
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['apellido_tutor'];	//30	
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['nombre_tutor'];	//30	
                            $contenido.=chr(9);
                            $contenido.=0;//$result1->fields['tutor_tipo_relacion'];	//1	
                            $contenido.=chr(9);
                            $contenido.=substr($result1->fields['fecha_inscripcion'],0,10);	//10	
                            $contenido.=chr(9);
                            //cambio formato de fecha
                            $fecha_carga=substr($result1->fields['fecha_carga'],0,10);
                            $fechaParaInsertar= '1899-12-30';
                            /*$fechaExplode = explode("/", $fecha_carga);
                            $fechaParaInsertar = date("Y-m-d", mktime(0,0,0,$fechaExplode[1], $fechaExplode[0], $fechaExplode[2]));*/
                            // inserto nueva fecha
                            $contenido.=$fechaParaInsertar;	
                            $contenido.=chr(9);

                            if ($sexo == 'M' or $embarazada=='N') {
                                    $fecha_d_emb = chr(0);
                                    $fecha_pr_parto = chr(0);
                                    $fecha_ef_parto= chr(0);
                                    $fecha_fum=chr(0);        
                            }else{
                                    $fecha_d_emb = $result1->fields['fecha_diagnostico_embarazo'];
                                    $fecha_pr_parto=$result1->fields['fecha_probable_parto'];	//10	
                                    $fecha_ef_parto =$result1->fields['fecha_efectiva_parto'];
                                    $fecha_fum=$result1->fields['fum'];
                                    if(substr($fecha_fum,0,4) < '1980')$fecha_fum=chr(0);
                                    if ($fecha_ef_parto == $fecha_carga ) { $fecha_ef_parto = '1899-12-30';}
                                    if ($fecha_pr_parto == $fecha_carga ) { $fecha_pr_parto = chr(0);}
                                    if ($fecha_d_emb == $fecha_carga ) { $fecha_d_emb = chr(0);}
                                    if ((substr($fecha_ef_parto,0,4) < '1980') OR($fecha_ef_parto == $fecha )) {$fecha_ef_parto= chr(0);}
                            }
                            $contenido.=$fecha_d_emb;	//10	
                            $contenido.=chr(9);
                                    //$sem_emb = $result1->fields['semanas_embarazo']; 	//3
                            $contenido.=$result1->fields['semanas_embarazo'];	//3	
                            $contenido.=chr(9);
                                    //$fecha_pr_parto=$result1->fields['fecha_probable_parto'];
                            $contenido.=$fecha_pr_parto;
                            $contenido.=chr(9);
                                    //$fecha_ef_parto=$result1->fields['fecha_efectiva_parto'];
                            $contenido.= $fecha_ef_parto;	//10	Fecha del parto o de la interrupción del embarazo
                            $contenido.=chr(9);


                            if ($result1->fields['activo'] == 1) {
                                $activo = 'S';                            
                            } else {
                                $activo = 'N';                            
                            }

                            $contenido.=$activo;	//1	Si/No ? Campo para el borrado logico
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['calle'];	//40	
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['numero_calle'];	//5	
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['manzana'];	//5	
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['piso'];	//5	
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['dpto'];	//5	
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['entre_calle_1'];	//40	
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['entre_calle_2'];	//40	
                            $contenido.=chr(9);
                            $contenido.=str_replace('-1','',$result1->fields['barrio']);	//40	
                            $contenido.=chr(9);
                            $contenido.=str_replace('-1','',$result1->fields['municipio']);	//40	
                            $contenido.=chr(9);
                            $contenido.=str_replace('-1','',$result1->fields['departamento']);	//40	
                            $contenido.=chr(9);
                            $contenido.=str_replace('-1','',$result1->fields['localidad']);	//40	
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['cod_pos']; //DomCodigoPostal	
                            $contenido.=chr(9);
                            $contenido.=$cod_prov;//$result1->fields['provincia_nac'];
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['telefono'];	//20	
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['cuie_ea']; //Efector
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['cuie_ea']; //LugarAtencionHabitual	80	Efector
                            $contenido.=chr(9);
                            //$id_nov += 1;

                            $contenido.= $result1->fields['id_beneficiarios']; //id_novedad=id_beneficiario
                            $contenido.=chr(9);
                            
                            //control de tipo de transaccion
                            $tipo_transaccion = $result1->fields['tipo_transaccion'];
                            if ($result1->fields['existe']==0 and $result1->fields['tipo_transaccion']=='M'){
                                $tipo_transaccion = 'A';
                            }                           
                            if ($result1->fields['existe']==1 and $result1->fields['tipo_transaccion']=='A'){
                                $tipo_transaccion = 'M';
                            }                           
                            //--------------------------------
                            
                            $contenido.=$tipo_transaccion; // TipoNovedad
                            $contenido.=chr(9); 
                            $contenido.=substr($result1->fields['fecha_carga'],0,10); //FechaNovedad	10	Fecha en la que se produjo la novedad. Fundamentalmente se utilizará para la fecha de baja.
                            $contenido.=chr(9); 
                            $contenido.=$cod_prov;//CodigoProvinciaAltaDatos	2	
                            $contenido.=chr(9); 

                            $contenido.=$result1->fields['cod_uad'];; //CodigoUADAltaDatos	3
                            $contenido.=chr(9); 	
                            $contenido.=$result1->fields['cod_ci'];; //CodigoCIAltaDatos	5
                            $contenido.=chr(9); 
                            $contenido.=substr($result1->fields['fecha_carga'],0,10); //FechaCarga
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['usuario_carga'];//$id_user; //UsuarioCarga 
                            $contenido.=chr(9);

                            //CHECKSUM????????????????????
                            $contenido.=chr(9);
                            //-----------------------------

                            if ($tipo_transaccion== 'M'){
                                for($i=1; $i<=61; $i++){
                                        $contenido.="1"; //Clave Binaria
                                }
                                $contenido.=chr(9);
                            }else $contenido.=chr(9);

                            $contenido.=$result1->fields['score_riesgo'];
                            $contenido.=chr(9); 
                            if ($result1->fields['alfabeta'] == '' or is_null($result1->fields['alfabeta'])) {
                                    $alfabeta = '';
                            } else {
                                    if ($result1->fields['alfabeta'] == 'N') {
                                            $alfabeta = 'NA';
                                    } else {
                                            if ($result1->fields['alfabeta'] == 'S')  {
                                                    if  ($result1->fields['estudios'] == '') {
                                                            $alfabeta = 'SA';
                                                    } else {	
                                                            if (($result1->fields['estudios'] == 'INICIAL') && ($result1->fields['estadoest'] == 'I')) {$alfabeta = 'II';}
                                                            if (($result1->fields['estudios'] == 'INICIAL') && ($result1->fields['estadoest'] == 'C')) {$alfabeta = 'IC';}
                                                            if (($result1->fields['estudios'] == 'PRIMARIO') && ($result1->fields['estadoest'] == 'I')) {$alfabeta = 'PI';}
                                                            if (($result1->fields['estudios'] == 'PRIMARIO') && ($result1->fields['estadoest'] == 'C')) {$alfabeta = 'PC';}
                                                            if (($result1->fields['estudios'] == 'SECUNDARIO') && ($result1->fields['estadoest'] == 'I')) {$alfabeta = 'SI';}
                                                            if (($result1->fields['estudios'] == 'SECUNDARIO') && ($result1->fields['estadoest'] == 'C')) {$alfabeta = 'SC';}
                                                            if (($result1->fields['estudios'] == 'TERCIARIO') && ($result1->fields['estadoest'] == 'I')) {$alfabeta = 'TI';}
                                                            if (($result1->fields['estudios'] == 'TERCIARIOS') && ($result1->fields['estadoest'] == 'C')) {$alfabeta = 'TC';}
                                                            if (($result1->fields['estudios'] == 'UNIVERSITARIO') && ($result1->fields['estadoest'] == 'I')) {$alfabeta = 'UI';}
                                                            if (($result1->fields['estudios'] == 'UNIVERSITARIO') && ($result1->fields['estadoest'] == 'C')) {$alfabeta = 'UC';}
                                                    }
                                            }			
                                    }
                            }		
                            $contenido.=$alfabeta;	//1	Beneficiario Alfabeta
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['anio_mayor_nivel'];
                            $contenido.=chr(9);
                            
                            if ($result1->fields['alfabeta_madre'] == '' or is_null($result1->fields['alfabeta_madre'])) {
                                    $alfabeta_madre = '';
                            } else {
                                    if ($result1->fields['alfabeta_madre'] == 'N') {
                                            $alfabeta_madre = 'NA';
                                    } else {
                                            if ($result1->fields['alfabeta_madre'] == 'S')  {
                                                    if  ($result1->fields['estudios_madre'] == '') {
                                                            $alfabeta_madre = 'SA';
                                                    } else {	
                                                            if (($result1->fields['estudios_madre'] == 'INICIAL') && ($result1->fields['estadoest_madre'] == 'I')) {$alfabeta_madre = 'II';}
                                                            if (($result1->fields['estudios_madre'] == 'INICIAL') && ($result1->fields['estadoest_madre'] == 'C')) {$alfabeta_madre = 'IC';}
                                                            if (($result1->fields['estudios_madre'] == 'PRIMARIO') && ($result1->fields['estadoest_madre'] == 'I')) {$alfabeta_madre = 'PI';}
                                                            if (($result1->fields['estudios_madre'] == 'PRIMARIO') && ($result1->fields['estadoest_madre'] == 'C')) {$alfabeta_madre = 'PC';}
                                                            if (($result1->fields['estudios_madre'] == 'SECUNDARIO') && ($result1->fields['estadoest_madre'] == 'I')) {$alfabeta_madre = 'SI';}
                                                            if (($result1->fields['estudios_madre'] == 'SECUNDARIO') && ($result1->fields['estadoest_madre'] == 'C')) {$alfabeta_madre = 'SC';}
                                                            if (($result1->fields['estudios_madre'] == 'TERCIARIO') && ($result1->fields['estadoest_madre'] == 'I')) {$alfabeta_madre = 'TI';}
                                                            if (($result1->fields['estudios_madre'] == 'TERCIARIOS') && ($result1->fields['estadoest_madre'] == 'C')) {$alfabeta_madre = 'TC';}
                                                            if (($result1->fields['estudios_madre'] == 'UNIVERSITARIO') && ($result1->fields['estadoest_madre'] == 'I')) {$alfabeta_madre = 'UI';}
                                                            if (($result1->fields['estudios_madre'] == 'UNIVERSITARIO') && ($result1->fields['estadoest_madre'] == 'C')) {$alfabeta_madre = 'UC';}
                                                    }
                                            }			
                                    }
                            }		
                            $contenido.=$alfabeta_madre;	//1	Madre Alfabeta
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['anio_mayor_nivel_madre'];
                            $contenido.=chr(9);

                            if ($result1->fields['alfabeta_padre'] == '' or is_null($result1->fields['alfabeta_padre'])) {
                                    $alfabeta_padre = '';
                            } else {
                                    if ($result1->fields['alfabeta_padre'] == 'N') {
                                            $alfabeta_padre = 'NA';
                                    } else {
                                            if ($result1->fields['alfabeta_padre'] == 'S')  {
                                                    if  ($result1->fields['estudios_padre'] == '') {
                                                            $alfabeta_padre = 'SA';
                                                    } else {	
                                                            if (($result1->fields['estudios_padre'] == 'INICIAL') && ($result1->fields['estadoest_padre'] == 'I')) {$alfabeta_padre = 'II';}
                                                            if (($result1->fields['estudios_padre'] == 'INICIAL') && ($result1->fields['estadoest_padre'] == 'C')) {$alfabeta_padre = 'IC';}
                                                            if (($result1->fields['estudios_padre'] == 'PRIMARIO') && ($result1->fields['estadoest_padre'] == 'I')) {$alfabeta_padre = 'PI';}
                                                            if (($result1->fields['estudios_padre'] == 'PRIMARIO') && ($result1->fields['estadoest_padre'] == 'C')) {$alfabeta_padre = 'PC';}
                                                            if (($result1->fields['estudios_padre'] == 'SECUNDARIO') && ($result1->fields['estadoest_padre'] == 'I')) {$alfabeta_padre = 'SI';}
                                                            if (($result1->fields['estudios_padre'] == 'SECUNDARIO') && ($result1->fields['estadoest_padre'] == 'C')) {$alfabeta_padre = 'SC';}
                                                            if (($result1->fields['estudios_padre'] == 'TERCIARIO') && ($result1->fields['estadoest_padre'] == 'I')) {$alfabeta_padre = 'TI';}
                                                            if (($result1->fields['estudios_padre'] == 'TERCIARIOS') && ($result1->fields['estadoest_padre'] == 'C')) {$alfabeta_padre = 'TC';}
                                                            if (($result1->fields['estudios_padre'] == 'UNIVERSITARIO') && ($result1->fields['estadoest_padre'] == 'I')) {$alfabeta_padre = 'UI';}
                                                            if (($result1->fields['estudios_padre'] == 'UNIVERSITARIO') && ($result1->fields['estadoest_padre'] == 'C')) {$alfabeta_padre = 'UC';}
                                                    }
                                            }			
                                    }
                            }		
                            $contenido.=$alfabeta_padre;	//1	Padre Alfabeta
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['anio_mayor_nivel_padre'];
                            $contenido.=chr(9);
                            
                            if ($result1->fields['alfabeta_tutor'] == '' or is_null($result1->fields['alfabeta_tutor']) ) {
                                    $alfabeta_tutor = '';
                            }else {
                                    if ($result1->fields['alfabeta_tutor'] == 'N') {
                                            $alfabeta_tutor = 'NA';
                                    } else {
                                            if ($result1->fields['alfabeta_tutor'] == 'S')  {
                                                    if  ($result1->fields['estudios_tutor'] == '') {
                                                            $alfabeta_tutor = 'SA';
                                                    } else {	
                                                            if (($result1->fields['estudios_tutor'] == 'INICIAL') && ($result1->fields['estadoest_tutor'] == 'I')) {$alfabeta_tutor = 'II';}
                                                            if (($result1->fields['estudios_tutor'] == 'INICIAL') && ($result1->fields['estadoest_tutor'] == 'C')) {$alfabeta_tutor = 'IC';}
                                                            if (($result1->fields['estudios_tutor'] == 'PRIMARIO') && ($result1->fields['estadoest_tutor'] == 'I')) {$alfabeta_tutor = 'PI';}
                                                            if (($result1->fields['estudios_tutor'] == 'PRIMARIO') && ($result1->fields['estadoest_tutor'] == 'C')) {$alfabeta_tutor = 'PC';}
                                                            if (($result1->fields['estudios_tutor'] == 'SECUNDARIO') && ($result1->fields['estadoest_tutor'] == 'I')) {$alfabeta_tutor = 'SI';}
                                                            if (($result1->fields['estudios_tutor'] == 'SECUNDARIO') && ($result1->fields['estadoest_tutor'] == 'C')) {$alfabeta_tutor = 'SC';}
                                                            if (($result1->fields['estudios_tutor'] == 'TERCIARIO') && ($result1->fields['estadoest_tutor'] == 'I')) {$alfabeta_tutor = 'TI';}
                                                            if (($result1->fields['estudios_tutor'] == 'TERCIARIOS') && ($result1->fields['estadoest_tutor'] == 'C')) {$alfabeta_tutor = 'TC';}
                                                            if (($result1->fields['estudios_tutor'] == 'UNIVERSITARIO') && ($result1->fields['estadoest_tutor'] == 'I')) {$alfabeta_tutor = 'UI';}
                                                            if (($result1->fields['estudios_tutor'] == 'UNIVERSITARIO') && ($result1->fields['estadoest_tutor'] == 'C')) {$alfabeta_tutor = 'UC';}
                                                    }
                                            }			
                                    }
                            }
                            $contenido.=$alfabeta_tutor;	//1	Tutor Alfabeta
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['anio_mayor_nivel_tutor'];
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['mail'];
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['celular'];
                            $contenido.=chr(9);
                            $contenido.=$fecha_fum;	//10	AAAA-MM-DD (Año, Mes, Día)
                            $contenido.=chr(9);
                            $contenido.=$result1->fields['obsgenerales'];
                            $contenido.=chr(9);
                            if ($result1->fields['discv'] == 'VISUAL') {
                                    $discapacidad = 'V';
                            }elseif ($result1->fields['disca'] == 'AUDITIVA') {
                                    $discapacidad = 'A';
                            }elseif ($result1->fields['discmo'] == 'MOTRIZ') {
                                    $discapacidad = 'Z';
                            }elseif ($result1->fields['discme'] == 'MENTAL') {
                                    $discapacidad = 'M';
                            }elseif ($result1->fields['otradisc'] == 'OTRA DISCAPACIDAD') {
                                    $discapacidad = 'O';
                            }else $discapacidad = '';
                            $contenido.=$discapacidad;	//1	Discapacidad
                            $contenido.="\n";	
                            $result1->MoveNext();
                    }

                    ////// TRAILER
                    $contenido.="T";
                    $contenido.=chr(9);
                    $cantidad_registros=$result1->numRows();
                    $contenido.=$cantidad_registros; // CantidadRegistros	6	Cantidad de registros que vinieron
                    $contenido.="\n";

                    if ($result1->EOF) {
                        if (fwrite($handle, $contenido) === FALSE) {
                            echo "<br>No se Puede escribir  ($filename)";
                            exit;
                        }else {	
                            echo "<br>El Archivo ($filename) se genero con exito";
                            $fecha_generacion=date("Y-m-d H:m:s");
                            $consulta= "insert into uad.archivos_enviados(fecha_generacion,estado,usuario,nombre_archivo_enviado,cantidad_registros_enviados,id_comienzo_lote) 
                                        values('$fecha_generacion','E','$id_user','$filename',$cantidad_registros,$id_beneficiario)";
                            sql($consulta, "Error al insertar en archivos enviados") or fin_pagina(); 
                            $consulta= "UPDATE uad.beneficiarios SET estado_envio='e' WHERE (id_beneficiarios IN ($where))";
                            sql($consulta, "Error al actualizar beneficiarios") or fin_pagina(); 
                        }
                    }
                fclose($handle);
            }
            $resultN->MoveNext();
        }     
        //var_dump($contenido);

    echo "<br>PROCESO FINALIZADO";        
}
        ?>
    </form>
    <p></p>
    <!--[if IE]>
        <script type="text/javascript" src="../../lib/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="../../lib/ie.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                            hoverTabla('.tablagenerica tr');
                        }
            );
        </script>
    <![endif]-->
</body>
</html>    
<?php 
    echo fin_pagina();// aca termino 
?>
