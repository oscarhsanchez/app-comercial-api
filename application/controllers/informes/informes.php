<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php
require_once(APPPATH.EF_BASE_CONTROLLER);
require_once(APPPATH.ENTITY_USER);
require_once(APPPATH.ENTITY_APIERROR);
require_once(APPPATH.ENTITY_DEVICE);


class informes extends ef_controller
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
        $this->load->model('informes/informe_model');
        $this->load->helper("esocialutils");
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
    public function familiaSearch_get() {
        $entityId = $this->get('id_entidad');
        $query = $this->get('query');
        $idUser = $this->get('id_user');
        $field = $this->get('field_id');
        $return = $this->get('return');
        $type = $this->get('type');

        $result = $this->informe_model->familiaSearch($field, $query, $return, $type);
        //echo json_encode($result);
        $this->response($result);
    }

    /**
     * Funcion que realiza una busqueda en base a los parametros indicados
     *
     * @param id_entidad
     * @param field_id
     * @param query
     * @param return=null
     * @param type
     * @param familia_id
     * returns Array((value, description))
     */
    public function subfamiliaSearch_get() {
        $entityId = $this->get('id_entidad');
        $query = $this->get('query');
        $idUser = $this->get('id_user');
        $field = $this->get('field_id');
        $return = $this->get('return');
        $type = $this->get('type');
        $id_familia = $this->get('familia_id');

        $result = $this->informe_model->familiaSearch($field, $query, $return, $type, $id_familia);
        //echo json_encode($result);
        $this->response($result);
    }





}