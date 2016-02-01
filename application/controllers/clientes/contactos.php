<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php
require_once(APPPATH.EF_BASE_CONTROLLER);
require_once(APPPATH.ENTITY_USER);
require_once(APPPATH.ENTITY_APIERROR);
require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_CLIENTE_CONTACTO);

class contactos extends ef_controller {
    public $controller = "eFinanzas-ClienteBundle-Controller-DefaultController";
	
    function __construct() {
        parent::__construct();
        $this->load->model('usuarios/session_model');
        $this->load->model('security_model');
        $this->load->model('usuarios/usuario_model');
        $this->load->model('clientes/cliente_model');
        $this->load->model('entidad/entity_model');
        $this->load->model('clientes/cliente_contacto_model');
        $this->load->helper("esocialutils");
    }

    /**
     * Devuelve los contactos de un cliente.
     *
     * @param clientePk
     * @param lastupdate
     * @param state
     *
     * @return result, Array(ccliente_contacto) | result, error
     */
    public function byCliente_get() {

        $action = "index";

        $this->client_access = true;

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('clientePk', 'lastUpdate', 'state');
        if (!$this->validateParams($this->get(), $params)) {
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $clientePk = $this->get('clientePk');
        $lastUpdate = $this->get('lastUpdate');
        $state = $this->get('state');

        //obtnemos las condiciones de pago
        $result = $this->cliente_contacto_model->getByCliente($clientePk, $this->entity->pk_entidad, $state, $lastUpdate);

        $this->response(array('result' => 'OK', 'contactos' => $result["contactos"]), 200);


    }


    /**
     * Devuelve un contacto a partir de su clave primaria
     * 
     * @param id
     * @return result, contacto | result, error
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
        $rappel = $this->cliente_rappel_model->getRappelByPK($id);

        if ($rappel) {
            $this->response(array('result' => 'OK', 'rappel' => $rappel), 200);
        } else {
            $err = new APIerror(ERROR_GETTING_INFO);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }

    }

    /**
     * Devuelve los contactos de los clientes asociados a un usuario.
     * Este metodo devuelve los contactos en bloques utilizando cache para esperar las siguientes peticiones.
     *
     * @param userPk
     * @param lastupdate
     * @param stete
     * @param pagination {"pageSize": 200, "page":0, "totalPages":null, "cache_token":null} Ej. Primera peticion
     *
     * @return result, Array(contacto) | result, error
     */
    public function byUserAssignedCachedMultipart_get() {
    
    	$action = "index";
    
    	$this->checkSecurity($action);
    
    	// Check for required parameters
    	$params=array('userPk', 'pagination', 'lastUpdate', 'state');
    	if (!$this->validateParams($this->get(), $params)) {
    		$err = new APIerror(INVALID_NUMBER_OF_PARAMS);
    		$result = $err->getValues();
    		$this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
    		$this->response(array('error' => $result), 200);
    	}
    
    	$userPk = $this->get('userPk');
    	$lastUpdate = $this->get('lastUpdate');
    	$pagination = json_decode($this->get('pagination'));
    	$state = $this->get('state');
    
    	//obtnemos los contactos
    	$result = $this->cliente_contacto_model->getMultipartCachedClienteContacto($userPk, $this->entity->pk_entidad, $pagination, $state, $lastUpdate);
    
    	$this->response(array('result' => 'OK', 'pagination' => $result["pagination"], 'contactos' => $result["contactos"]), 200);

    
    }

    /**
     * Este metodo se encarga de guardar los contactos.
     * Se comprueba si el recibe existe usando el token.
     *
     * @param contactos
     * @return error | OK
     */
    public function listSetByToken_post() {
        $action = "edit";

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('contactos');
        if(!$this->validateParams($this->input->post(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $contactos_post = $this->input->post('contactos');
        $contactos = json_decode(base64_decode($contactos_post));


        if (!$contactos) {
            $err = new APIerror(PARAM_VERIFICATION_ERROR);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        //Hacemos todo en una unica transacion
        $this->db->trans_start();

        foreach ($contactos as $contacto) {

            $_contactoDb = $this->cliente_contacto_model->getContactoByToken($contacto->token);

            $newContacto = new cliente_contacto();
            $newContacto->set($contacto);

            if ($_contactoDb) {

                $newContacto->id = $_contactoDb->id;
            }

            try {
                $this->cliente_contacto_model->saveContacto($newContacto);
            } catch (\Exception $e) {
                $err = new APIerror(ERROR_SAVING_DATA);
                $result = $err->getValues();
                $this->log_model->logWithSession($e, $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
                $this->response(array('error' => $result), 200);
            }

        }

        $this->db->trans_complete();

        $this->response(array('result' => 'OK'), 200);
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
        $field = $this->get('field_id');
        $return = $this->get('return');
        $type = $this->get('type');

        $result = $this->cliente_contacto_model->search($entityId, $field, $query, $return, $type);
        //echo json_encode($result);
        $this->response($result);
    }
    
}

?>