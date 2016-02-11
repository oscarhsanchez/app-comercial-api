<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php
require_once(APPPATH.VALLAS_BASE_CONTROLLER);
require_once(APPPATH.ENTITY_USER);
require_once(APPPATH.ENTITY_APIERROR);
require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_REPO_ARCHIVO);

class archivos extends generic_controller {
    public $controller = "eFinanzas-EntidadBundle-Controller-DefaultController";

    function __construct() {
        parent::__construct();
        $this->load->model('usuarios/session_model');
        $this->load->model('security_model');
        $this->load->model('usuarios/usuario_model');
        $this->load->model('clientes/cliente_model');
        $this->load->model('repositorio/archivo_model');
        $this->load->model('repositorio/carpeta_model');
        $this->load->model('entidad/entity_model');
        $this->load->helper("esocialutils");
    }


    /**
     * Devuelve los archivos definidas para una entidad y que al menos tenga un archivo.
     * Este metodo devuelve los archivos en bloques utilizando cache para esperar las siguientes peticiones.
     *
     * @param lastupdate
     * @param stete
     * @param pagination {"pageSize": 200, "page":0, "totalPages":null, "cache_token":null} Ej. Primera peticion
     *
     * @return result, Array(repo_carchivo) | result, error
     */
    public function byEntityCachedMultipart_get() {

        $action = "index";

        $this->client_access = true;

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('pagination', 'lastUpdate', 'state');
        if (!$this->validateParams($this->get(), $params)) {
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $lastUpdate = $this->get('lastUpdate');
        $pagination = json_decode($this->get('pagination'));
        $state = $this->get('state');

        $result = $this->archivo_model->getMultipartCachedArchivos($this->entity->pk_entidad, $pagination, $state, $lastUpdate);

        $this->response(array('result' => 'OK', 'pagination' => $result["pagination"], 'archivos' => $result["archivos"]), 200);


    }
    
    /**
     * Sube/actualiza un archivo. Si el archivo es subido con el par�metro "token" dentro,
     * �ste se va a actualizar sustituyendo el archivo viejo por el nuevo, si no lo tiene
     * se crear� un archivo nuevo.
     *
     * @param archivo El archivo que se quiere subir
     * @see repoArchivo
     *
     * @return 
     */
    public function setArchivo_post() {
    	$action = "index";
    	
    	$this->checkSecurity($action);
    	
    	// Check for required parameters
    	$params=array('archivo');
    	if (!$this->validateParams($this->post(), $params)) {
    		$err = new APIerror(INVALID_NUMBER_OF_PARAMS);
    		$result = $err->getValues();
    		$this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
    		$this->response(array('error' => $result), 200);
    	}
    	
    	$archivoStd = json_decode($this->post('archivo'));
    	$archivo = new repoArchivo();
    	$archivo->set($archivoStd);
    	
    	//Dividimos las carpetas que nos vienen del tipo "Albaranes/Firmas" y obtenemos la ID de la carpeta base de esa entidad.
    	$carpetas = explode("/", $archivo->path);
    	$lastFolderId = $this->carpeta_model->getCarpetaByNombreAndPadreAndEntidad(null, null, $archivo->fk_entidad)->pk_carpeta;
    	foreach ($carpetas as $nombreCarpeta) {
    		$carpeta = $this->carpeta_model->getCarpetaByNombreAndPadreAndEntidad($nombreCarpeta, $lastFolderId, $archivo->fk_entidad);
    		if (!$carpeta) {
    			$carpeta = new repoCarpeta();
    			$carpeta->fk_carpeta_padre = $lastFolderId;
    			$carpeta->fk_entidad = $archivo->fk_entidad;
    			$carpeta->nombre = $nombreCarpeta;
    			$carpeta->token = getToken();
    			$carpeta->estado = 1;
    			$carpeta->estatica = 0;
    			$carpeta->path = null;
    			$id = $carpeta->_save(false, true);
    			$carpeta->pk_carpeta = $id;
    		}
    		$lastFolderId = $carpeta->pk_carpeta;
    	}
    	$archivo->fk_carpeta_padre = $lastFolderId;
    	
    	try {
    		$path = $this->archivo_model->saveArchivo($archivo);
    		$this->response(array('result' => 'OK', 'path' => $path), 200);
    	} catch (\Exception $e) {
    		$err = new APIerror(ERROR_SAVING_DATA);
    		$result = $err->getValues();
    		$this->log_model->logWithSession($e, $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
    		$this->response(array('error' => $result), 200);
    	}
    }

}

?>