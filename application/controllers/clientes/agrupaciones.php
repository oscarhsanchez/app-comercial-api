<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php
require_once(APPPATH.EF_BASE_CONTROLLER);
require_once(APPPATH.ENTITY_USER);
require_once(APPPATH.ENTITY_APIERROR);
require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_CLIENTE_AGRUPACION);

class agrupaciones extends ef_controller {
    public $controller = "eFinanzas-ClienteBundle-Controller-DefaultController";
	
    function __construct() {
        parent::__construct();
        $this->load->model('usuarios/session_model');
        $this->load->model('security_model');
        $this->load->model('usuarios/usuario_model');
        $this->load->model('clientes/cliente_model');
        $this->load->model('entidad/entity_model');
        $this->load->model('clientes/cliente_agrupacion_model');
        $this->load->helper("esocialutils");
    }


    /**
     * Devuelve una agrupacion a partir de su clave primaria
     * 
     * @param $id
     */
    public function byPk_get()  {

        $action = "index";

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('id');
        if(!$this->validateParams($this->input->get(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $id = $this->get('id');

        //obtnemos la condicion especial
        $group = $this->cliente_agrupacion_model->getAgrupacionByPk($id);

        if ($group) {
            $this->response(array('result' => 'OK', 'agr_cliente' => $group), 200);
        } else {
            $err = new APIerror(ERROR_GETTING_INFO);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }

    }

    /**
     *  Devuelve las agrupaciones de clientes paginadas
     *
     * @param limit
     * @param offset
     * @param lastUpdate
     * @param state
     * return Array(cliente_agrupacion)
     *
     */
    public function listMultipart_get() {

        $action = "index";

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('limit', 'offset', 'lastUpdate', 'state');
        if (!$this->validateParams($this->get(), $params)) {
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $userPk = $this->get('userPk');
        $limit = $this->get('limit');
        $offset = $this->get('offset');
        $lastUpdate = $this->get('lastUpdate');
        $state = $this->get('state');

        //obtnemos las agrupaciones
        $groups = $this->cliente_agrupacion_model->getMultipartClienteAgrupacion($this->entity->pk_entidad, $limit, $offset, $state, $lastUpdate);

        if ($groups) {
            $this->response(array('result' => 'OK', 'agrs_clientes'  => $groups), 200);
        } else {
            $err = new APIerror(ERROR_GETTING_INFO);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }

    }

    /**
     *  Devuelve las agrupaciones de clientes en bloques cacheados.
     *  Si durante el proceso de actualizacon entra un nuevo registro, este no estara disponible hasta la siguiente peticion.
     *
     * @param lastUpdate
     * @param pagination: {"pageSize": 200, "page":0, "totalPages":null, "cache_token":null} Ej. Primera peticion
     * @param state
     * return Array(cliente_agrupacion)
     *
     */
    public function listCachedMultipart_get() {
    
    	$action = "index";
    
    	$this->checkSecurity($action);

    
    	// Check for required parameters
    	$params=array('lastUpdate', 'pagination', 'state');
    	if (!$this->validateParams($this->get(), $params)) {
    		$err = new APIerror(INVALID_NUMBER_OF_PARAMS);
    		$result = $err->getValues();
    		$this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
    		$this->response(array('error' => $result), 200);
    	}
    

    	$lastUpdate = $this->get('lastUpdate');
    	$pagination = json_decode($this->get('pagination'));
    	$state = $this->get('state');

        //obtnemos las agrupaciones
    	$result = $this->cliente_agrupacion_model->getMultipartCachedClienteAgrupacion($this->entity->pk_entidad, $pagination, $state, $lastUpdate);


    	if ($result) {
    		$this->response(array('result' => 'OK', 'pagination' => $result["pagination"], 'agrs_clientes' => $result["groups"]), 200);
    	} else {
    		$err = new APIerror(ERROR_GETTING_INFO);
    		$result = $err->getValues();
    		$this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
    		$this->response(array('result' => 'error', 'error' => $result), 200);
    	}
    
    }


    /**
     *  Devuelve la relacion de los cliente con las diferentes agrupaciones en bloque cacheados.
     *  Si se indica un usuario devolvera las relaciones de los usuarios que tiene asociado. En caso contrario devolvera todas las de la entidad.
     *  Si durante el proceso de actualizacon entra un nuevo registro, este no estara disponible hasta la siguiente peticion.
     *  Devuelce solo la de los clientes asociados al usuario
     *
     * @param lastUpdate
     * @param pagination: {"pageSize": 200, "page":0, "totalPages":null, "cache_token":null} Ej. Primera peticion
     * @param state
     * return Array(r_cli_agr)
     *
     */
    public function ClientAgrListCachedMultipart_get() {

        $action = "index";

        $this->checkSecurity($action);



        // Check for required parameters
        $params=array('lastUpdate', 'pagination', 'state');
        if (!$this->validateParams($this->get(), $params)) {
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }


        $lastUpdate = $this->get('lastUpdate');
        $pagination = json_decode($this->get('pagination'));
        $state = $this->get('state');
        $userPk = $this->get('userPk');

        //obtnemos las agrupaciones
        $result = $this->cliente_agrupacion_model->getMultipartCachedRCliAgr($this->entity->pk_entidad, $userPk, $pagination, $state, $lastUpdate);

        $this->response(array('result' => 'OK', 'pagination' => $result["pagination"], 'r_cli_agr' => $result["r_cli_agr"]), 200);


    }

    /**
     * Funcion que realiza una busqueda en base a los parametros indicados
     *
     * @param id_entidad
     * @param field_id
     * @param query
     * @param return=null
     * @param type
     * returns Array((value, description))
     */
    public function search_get() {
        $entityId = $this->get('id_entidad');
        $query = $this->get('query');
        $idUser = $this->get('id_user');
        $field = $this->get('field_id');
        $return = $this->get('return');
        $type = $this->get('type');

        $result = $this->cliente_agrupacion_model->search($entityId, $field, $query, $return, $type);
        //echo json_encode($result);
        $this->response($result);
    }
    
}

?>