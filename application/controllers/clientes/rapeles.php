<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php
require_once(APPPATH.EF_BASE_CONTROLLER);
require_once(APPPATH.ENTITY_USER);
require_once(APPPATH.ENTITY_APIERROR);
require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_CLIENTE_AGRUPACION);

class rapeles extends ef_controller {
    public $controller = "eFinanzas-ClienteBundle-Controller-DefaultController";
	
    function __construct() {
        parent::__construct();
        $this->load->model('usuarios/session_model');
        $this->load->model('security_model');
        $this->load->model('usuarios/usuario_model');
        $this->load->model('clientes/cliente_model');
        $this->load->model('entidad/entity_model');
        $this->load->model('clientes/cliente_rappel_model');
        $this->load->helper("esocialutils");
    }


    /**
     * Devuelve un rapel a partir de su clave primaria
     * 
     * @param id
     * return cliente_reappel
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
     * Devuelve los rapeles de un cliente.
     *
     * @param clientePk
     * @param lastupdate
     * @param state
     *
     * @return result, Array(cliente_rapel) | result, error
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
        $result = $this->cliente_rappel_model->getByCliente($clientePk, $this->entity->pk_entidad, $state, $lastUpdate);

        $this->response(array('result' => 'OK', 'rapeles' => $result["rapeles"]), 200);


    }

    /**
     * Devuelve los rapeles de los clientes asociados a un usuario.
     * Este metodo devuelve los contactos en bloques utilizando cache para esperar las siguientes peticiones.
     *
     * @param userPk
     * @param lastupdate
     * @param stete
     * @param pagination {"pageSize": 200, "page":0, "totalPages":null, "cache_token":null} Ej. Primera peticion
     *
     * @return result, Array(cliente_rappel) | result, error
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
    
    	//obtnemos el cliente
        $result = $this->cliente_rappel_model->getMultipartCachedClienteRappel($userPk, $this->entity->pk_entidad, $pagination, $state, $lastUpdate);
    
    	$this->response(array('result' => 'OK', 'pagination' => $result["pagination"], 'rapeles' => $result["rapeles"]), 200);

    
    }

    /**
     * Este metodo se encarga de guardar los rapeles.
     * Se comprueba si el recibe existe usando el token.
     *
     * @param rapeles
     * @return error | OK
     */
    public function listSetByToken_post() {
        $action = "edit";

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('rapeles');
        if(!$this->validateParams($this->input->post(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $rapeles_post = $this->input->post('rapeles');
        $rapeles = json_decode(base64_decode($rapeles_post));


        if (!$rapeles) {
            $err = new APIerror(PARAM_VERIFICATION_ERROR);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        //Hacemos todo en una unica transacion
        $this->db->trans_start();

        foreach ($rapeles as $rapel) {

            $_rapelDb = $this->cliente_rappel_model->getRapelByToken($rapel->token);

            $newRapel = new cliente_rappel();
            $newRapel->set($rapel);

            if ($_rapelDb) {

                $newRapel->id = $_rapelDb->id;
            }

            try {
                $this->cliente_rappel_model->saveRapel($newRapel);
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
    
}

?>