<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php
require_once(APPPATH.EF_BASE_CONTROLLER);
require_once(APPPATH.ENTITY_USER);
require_once(APPPATH.ENTITY_APIERROR);
require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_CLIENTE_COND_PAGO);

class incidencias extends ef_controller {
    public $controller = "eFinanzas-ClienteBundle-Controller-DefaultController";

    function __construct() {
        parent::__construct();
        $this->load->model('usuarios/session_model');
        $this->load->model('security_model');
        $this->load->model('usuarios/usuario_model');
        $this->load->model('clientes/cliente_incidencia_model');
        $this->load->model('entidad/entity_model');
        $this->load->helper("esocialutils");
    }


    /**
     * Devuelve las incidencias de los clientes asociados a un usuario.
     * Este metodo devuelve los mpv en bloques utilizando cache para esperar las siguientes peticiones.
     *
     * @param userPk
     * @param lastupdate
     * @param stete
     * @param pagination {"pageSize": 200, "page":0, "totalPages":null, "cache_token":null} Ej. Primera peticion
     *
     * @return result, Array(incidencia) | result, error
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


        $result = $this->cliente_incidencia_model->getMultipartCachedIncidencias($userPk, $this->entity->pk_entidad, $pagination, $state, $lastUpdate);

        $this->response(array('result' => 'OK', 'pagination' => $result["pagination"], 'incidencias' => $result["incidencias"]), 200);


    }


    /**
     * Devuelve los registros de las incidencias de los clientes asociados a un usuario.
     * Este metodo devuelve los mpv en bloques utilizando cache para esperar las siguientes peticiones.
     *
     * @param userPk
     * @param lastupdate
     * @param stete
     * @param pagination {"pageSize": 200, "page":0, "totalPages":null, "cache_token":null} Ej. Primera peticion
     *
     * @return result, Array(incidencia) | result, error
     */
    public function registrosByUserAssignedCachedMultipart_get() {

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


        $result = $this->cliente_incidencia_model->getMultipartCachedRegistrosIncidencias($userPk, $this->entity->pk_entidad, $pagination, $state, $lastUpdate);

        $this->response(array('result' => 'OK', 'pagination' => $result["pagination"], 'registros' => $result["registros"]), 200);


    }


}

?>