<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php
require_once(APPPATH.EF_BASE_CONTROLLER);
require_once(APPPATH.ENTITY_USER);
require_once(APPPATH.ENTITY_APIERROR);
require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_VISITA);

class visitas extends ef_controller {
    public $controller = "eFinanzas-ClienteBundle-Controller-DefaultController";
	
    function __construct() {
        parent::__construct();
        $this->load->model('usuarios/session_model');
        $this->load->model('security_model');
        $this->load->model('usuarios/usuario_model');
        $this->load->model('clientes/cliente_model');
        $this->load->model('entidad/entity_model');
        $this->load->model('clientes/cliente_visita_model');
        $this->load->helper("esocialutils");
    }

    /**
     *  Inserta o actualiza un listado de visitas para una entidad, a partir del token de cada elemento.
     *
     * @param visitas -> Listado de visitas en Base64
     *
     * return result, error
     *
     */
    public function listSetByToken_post()
    {

        $action = "edit";

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('visitas');
        if(!$this->validateParams($this->input->post(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $visitasPost = $this->input->post('visitas');
        $visitas = json_decode(base64_decode($visitasPost));


        if (!$visitas) {
            $err = new APIerror(PARAM_VERIFICATION_ERROR);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        //Hacemos todo en una unica transacion
        $this->db->trans_start();

        foreach ($visitas as $visita) {

            //Comprobamos si existe el Token para determinar si un update o un insert
            $_visitaDb = $this->cliente_visita_model->getVisitaByToken($visita->token);

            $newVisita = new visita();
            $newVisita->set($visita);

            //Comprobamos que la entidad indicada corresponde con la entitySecret
            if ($newVisita->fk_entidad != null && $newVisita->fk_entidad != $this->entity->pk_entidad) {
                $err = new APIerror(ENTITY_VERIFICATION_ERROR);
                $result = $err->getValues();
                $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
                $this->response(array('error' => $result), 200);
            }

            if ($_visitaDb) {

                $newVisita->id = $_visitaDb->id;
                $newVisita->fk_entidad = $_visitaDb->fk_entidad;

            }

            try {
                if (!$newVisita->hora_visita) $newVisita->hora_visita = "09:00";

                $newVisita->fk_entidad = $this->entity->pk_entidad;
                $this->cliente_visita_model->saveVisita($newVisita);
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
     * Devuelve las visitas de los clientes asignadas a un usuario.
     * Este metodo devuelve las visitas en bloques utilizando cache para esperar las siguientes peticiones.
     * IMPORTANTE: Las visitas se limitan en el pasado a 30 dias.
     *
     * @param userPk
     * @param lastupdate
     * @param stete
     * @param pagination {"pageSize": 200, "page":0, "totalPages":null, "cache_token":null} Ej. Primera peticion
     *
     * @return result, Array(visita) | result, error
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

        $result = $this->cliente_visita_model->getMultipartCachedVisitaCliente($userPk, $this->entity->pk_entidad, $pagination, $state, $lastUpdate);

        $this->response(array('result' => 'OK', 'pagination' => $result["pagination"], 'visitas' => $result["visitas"]), 200);

    
    }

    /**
     * Devuelve las visitas de los clientes de la entidad.
     * Este metodo devuelve las visitas en bloques utilizando cache para esperar las siguientes peticiones.
     * IMPORTANTE: Las visitas se limitan en el pasado a 30 dias.
     *
     * @param fromDate
     * @param lastupdate
     * @param stete
     * @param pagination {"pageSize": 200, "page":0, "totalPages":null, "cache_token":null} Ej. Primera peticion
     *
     * @return result, Array(visita) | result, error
     */
    public function byEntityAssignedCachedMultipart_get() {

        $action = "index";

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('fromDate', 'pagination', 'lastUpdate', 'state');
        if (!$this->validateParams($this->get(), $params)) {
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $fromDate = $this->get('fromDate');
        $lastUpdate = $this->get('lastUpdate');
        $pagination = json_decode($this->get('pagination'));
        $state = $this->get('state');

        $result = $this->cliente_visita_model->getMultipartCachedEntityVisitaCliente($this->entity->pk_entidad, $pagination, $state, $lastUpdate, $fromDate);

        $this->response(array('result' => 'OK', 'pagination' => $result["pagination"], 'visitas' => $result["visitas"]), 200);


    }
    
}

?>