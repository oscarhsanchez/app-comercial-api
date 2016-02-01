<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php
require_once(APPPATH.EF_BASE_CONTROLLER);
require_once(APPPATH.ENTITY_USER);
require_once(APPPATH.ENTITY_APIERROR);
require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_VISITA);

class usuariosGeo extends ef_controller {
    public $controller = "eFinanzas-ClienteBundle-Controller-DefaultController";

    function __construct() {
        parent::__construct();
        $this->load->model('usuarios/session_model');
        $this->load->model('security_model');
        $this->load->model('usuarios/usuario_model');
        $this->load->model('clientes/cliente_model');
        $this->load->model('entidad/entity_model');
        $this->load->model('usuarios/usuario_geo_model');
        $this->load->helper("esocialutils");
    }

    /**
     *  Inserta o actualiza un listado de posiciones de un usuario, a partir del token de cada elemento.
     *
     * @param geoPos -> Listado de usuario_geo en Base64
     *
     * return result, error
     *
     */
    public function listSetByToken_post()
    {

        $action = "edit";

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('geoPos');
        if(!$this->validateParams($this->input->post(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $geoPost = $this->input->post('geoPos');
        $geos = json_decode(base64_decode($geoPost));


        if (!$geos) {
            $err = new APIerror(PARAM_VERIFICATION_ERROR);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        //Hacemos todo en una unica transacion
        $this->db->trans_start();

        foreach ($geos as $geo) {
            //Comprobamos si existe el Token para determinar si un update o un insert
            $_geoDb = $this->usuario_geo_model->getUsuarioGeoByToken($geo->token);

            $newGeo = new usuario_geo();
            $newGeo->set($geo);

            if ($_geoDb) {

                $newGeo->pk_usuario_geo = $_geoDb->pk_usuario_geo;
            }

            try {
                $newGeo->fk_entidad = $this->entity->pk_entidad;
                $this->usuario_geo_model->saveUsuarioGeo($newGeo);
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

        //obtnemos el cliente
        $result = $this->cliente_visita_model->getMultipartCachedVisitaCliente($userPk, $this->entity->pk_entidad, $pagination, $state, $lastUpdate);

        $this->response(array('result' => 'OK', 'pagination' => $result["pagination"], 'visitas' => $result["visitas"]), 200);


    }

}

?>