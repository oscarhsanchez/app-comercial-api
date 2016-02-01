<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php
require_once(APPPATH.EF_BASE_CONTROLLER);
require_once(APPPATH.ENTITY_USER);
require_once(APPPATH.ENTITY_APIERROR);
require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_COND_ESPECIALES);

class condsEspeciales extends ef_controller {
    public $controller = "eFinanzas-ClienteBundle-Controller-DefaultController";
	
    function __construct() {
        parent::__construct();
        $this->load->model('usuarios/session_model');
        $this->load->model('security_model');
        $this->load->model('usuarios/usuario_model');
        $this->load->model('clientes/cliente_model');
        $this->load->model('entidad/entity_model');
        $this->load->model('clientes/cliente_cond_especiales_model');
        $this->load->model('clientes/cliente_model');
        $this->load->helper("esocialutils");
    }

    /**
     * Devuelve las condiciones especiales de los clientes asociados a un usuario.
     * Este metodo devuelve los resultados en bloques utilizando cache para esperar las siguientes peticiones.
     *
     * @param userPk
     * @param lastupdate
     * @param stete
     * @param pagination {"pageSize": 200, "page":0, "totalPages":null, "cache_token":null} Ej. Primera peticion
     *
     * @return result, Array(cond_especiales) | result, error
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
    	$result = $this->cliente_cond_especiales_model->getMultipartCachedSpecialConditions($userPk, $this->entity->pk_entidad, $pagination, $state, $lastUpdate);

        $this->response(array('result' => 'OK', 'pagination' => $result["pagination"], 'condsespeciales' => $result["conds_especiales"]), 200);

    
    }

    /**
     * Devuelve las condicioines especiales un cliente a partir de sus clave primaria
     *
     * @param clientePk
     * @param state
     *
     * returns Array(Conds_especiales)
     */
    public function byCliente_get() {

        $action = "index";

        $this->client_access = true;

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('clientePk', 'state');
        if (!$this->validateParams($this->get(), $params)) {
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $clientePk = $this->get('clientePk');
        $state = $this->get('state');

        $result = $this->cliente_cond_especiales_model->getCondsEspecialesByClientePk($this->entity->pk_entidad, $clientePk, $state);

        $this->response(array('result' => 'OK', 'condsespeciales' => $result["conds_especiales"]), 200);

    }

    /**
     * Este metodo se encarga de guardar las condiciones especiales de un cliente.
     * Se comprueba si el recibe existe usando el token.
     *
     * @param condsEsp
     * @return error | OK
     */
    public function listSetByToken_post() {
        $action = "edit";

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('condsEsp');
        if(!$this->validateParams($this->input->post(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $condsEsp_post = $this->input->post('condsEsp');
        $condsEsp = json_decode(base64_decode($condsEsp_post));


        if (!$condsEsp) {
            $err = new APIerror(PARAM_VERIFICATION_ERROR);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        //Hacemos todo en una unica transacion
        $this->db->trans_start();

        foreach ($condsEsp as $condEsp) {

            $_condEspDb = $this->cliente_cond_especiales_model->getCondEspecialByToken($condEsp->token);

            $newCondEsp = new cond_especiales();
            $newCondEsp->set($condEsp);

            if ($_condEspDb) {

                $newCondEsp->id_cond_especial = $_condEspDb->id_cond_especial;
            }

            try {
                $this->cliente_cond_especiales_model->saveCondEspecial($newCondEsp);
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