<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php
require_once(APPPATH.EF_BASE_CONTROLLER);
require_once(APPPATH.ENTITY_USER);
require_once(APPPATH.ENTITY_APIERROR);
require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_CLIENTE_TARJETA);

class tarjetas extends ef_controller {
    public $controller = "eFinanzas-ClienteBundle-Controller-DefaultController";
	
    function __construct() {
        parent::__construct();
        $this->load->model('usuarios/session_model');
        $this->load->model('security_model');
        $this->load->model('usuarios/usuario_model');
        $this->load->model('clientes/cliente_model');
        $this->load->model('entidad/entity_model');
        $this->load->model('clientes/cliente_tarjetas_model');
        $this->load->helper("esocialutils");
    }

    /**
     *  Devuelve las tarjetas de un cliente de la entidad.
     *
     * @param state
     * @param clientePk
     * @param offset (Optional)
     * @param limit (Optional)
     *
     * return Array(tarjeta)
     *
     */
    public function list_get() {

        $action = "index";

        $this->client_access = true;

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('state', 'clientePk');
        if (!$this->validateParams($this->get(), $params)) {
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $state = $this->get('state');
        $offset = $this->get('offset');
        $limit = $this->get('limit');
        $clientePk = $this->get('clientePk');

        $result = $this->cliente_tarjetas_model->getAll($this->entity->pk_entidad, $clientePk, $state, $offset, $limit);

        $this->response(array('result' => 'OK', 'tarjetas' => $result["tarjetas"]), 200);


    }

    /**
     *  Elimina una tarjeta usando el token.
     *
     * @param token
     *
     * return OK
     *
     */
    public function delete_post() {

        $action = "index";

        $this->client_access = true;

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('token');
        if(!$this->validateParams($this->input->post(), $params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $token = $this->post('token');

        $tarjeta = $this->cliente_tarjetas_model->getTarjetaByToken($this->entity->pk_entidad, $token);

        if ($tarjeta) {
            $tarjeta->estado = 0;

            if ($this->cliente_tarjetas_model->save($tarjeta)) {
                $this->response(array('result' => 'OK'), 200);
            } else {
                $err = new APIerror(ERROR_SAVING_DATA);
                $result = $err->getValues();
                $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
                $this->response(array('error' => $result), 200);
            }

        } else {
            $err = new APIerror(ENTITY_NOT_FOUND);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }




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

        $result = $this->cliente_tarjetas_model->search($entityId, $field, $query, $return, $type);
        //echo json_encode($result);
        $this->response($result);
    }
    
}

?>