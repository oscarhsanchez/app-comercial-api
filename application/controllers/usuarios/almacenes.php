<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php
require_once(APPPATH.EF_BASE_CONTROLLER);
require_once(APPPATH.ENTITY_USER);
require_once(APPPATH.ENTITY_APIERROR);
require_once(APPPATH.ENTITY_DEVICE);


class almacenes extends ef_controller
{
    public $controller = "eFinanzas-ArticuloBundle-Controller-DefaultController";

    function __construct()
    {
        parent::__construct();
        $this->load->model('usuarios/session_model');
        $this->load->model('security_model');
        $this->load->model('usuarios/usuario_model');
        $this->load->model('entidad/delegacion_model');
        $this->load->model('usuarios/dispositivo_model');
        $this->load->model('entidad/entity_model');
        $this->load->model('articulos/articulo_almacen_model');
        $this->load->helper("esocialutils");
    }


    /**
     *  Devuelve los almacenes de un usuario a partir de un timestamp.
     *  El resultado se divide en bloques que son cacheados esperando las siguientes peticiones.
     *
     * IMPORTANTE: SI LA RELACION almacen_agente TIENE ESTADO = 0 DEVOLVEMOS EL ALAMACEN COMO ELIMINADO
     *
     * @param userPk
     * @param lastUpdate
     * @param pagination: {"pageSize": 200, "page":0, "totalPages":null, "cache_token":null} Ej. Primera peticion
     * @param state
     * return Array(r_art_alm)
     *
     */
    public function listCachedMultipart_get() {

        $action = "index";

        $this->checkSecurity($action);


        // Check for required parameters
        $params=array('userPk', 'lastUpdate', 'pagination', 'state');
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

        $result = $this->articulo_almacen_model->getMultipartCachedUserAlmacenes($userPk, $pagination, $state, $lastUpdate);

        $this->response(array('result' => 'OK', 'pagination' => $result["pagination"], 'almacenes' => $result["almacenes"]), 200);


    }



}