<?php

// This can be removed if you use __autoload() in config.php
require_once(APPPATH.EF_BASE_CONTROLLER);
require_once(APPPATH.ENTITY_APIERROR);


class usuarios extends ef_controller
{

    protected $controller = "eFinanzas-FichaUsuarioBundle-Controller-DefaultController";

	function __construct()
    {
    	parent::__construct();
    	$this->load->model('usuarios/session_model');
    	$this->load->model('usuarios/usuario_model');
    	$this->load->model('usuarios/dispositivo_model');
        $this->load->model('entidad/entity_model');
        $this->load->model('security_model');
        $this->load->helper("eSocialUtils");
    }

    /**
     *
     * Devuelve los usuarios de una entidad
     *
     */
    public function list_get()
    {

        $action = "index";

        $this->checkSecurity($action);

        $result = $this->usuario_model->getEntityUsers($this->entity->pk_entidad);

        $this->response(array('result' => 'OK', 'usuarios' => $result), 200);



    }

    /**
     * @param $userId
     * @return bool
     *
     * Utilizamos este metodo para desmarcar la bandera de solicitud de actualizacin de la serie.
     * Esta funciona la utilizan los terminales
     */
    public function unFlagSerieUpd_get()
    {

        $action = "index";

        $this->checkSecurity($action);

        $params=array('userId');
        if(!$this->validateParams($this->get(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }

        $result = $this->usuario_model->unFlagUpdSerie($this->entity->pk_entidad, $this->get('userId'));

        if ($result) {
            $this->response(array('result' => 'OK'), 200);
        } else {
            $err = new APIerror(ERROR_SAVING_DATA);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }

    }

    /**
     * @param $userId
     * @return bool
     *
     * Utilizamos esta funciona para desmarcar la bandera de solicitud de envio de base de datos.
     * Esta funciona la utilizan los terminales
     */
    public function unFlagDatabaseSend_get()
    {

        $action = "index";

        $this->checkSecurity($action);

        $params=array('userId');
        if(!$this->validateParams($this->get(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }

        $result = $this->usuario_model->unFlagDatabaseSend($this->entity->pk_entidad, $this->get('userId'));

        if ($result) {
            $this->response(array('result' => 'OK'), 200);
        } else {
            $err = new APIerror(ERROR_SAVING_DATA);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('result' => 'error', 'error' => $result), 200);
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
     * @param id_delegacion
     * @param id_canal_venta
     * returns Array((value, description))
     */
    public function search_get() {
        $entityId = $this->get('id_entidad');
        $query = $this->get('query');
        $idUser = $this->get('id_user');
        $field = $this->get('field_id');
        $return = $this->get('return');
        $type = $this->get('type');
        $id_delegacion = $this->get('id_delegacion');
        $id_canal_venta = $this->get('id_canal_venta');

        $result = $this->usuario_model->search($entityId, $field, $query, $return, $type, $id_delegacion, $id_canal_venta);
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
     * @param id_delegacion
     * @param id_canal_venta
     * returns Array((value, description))
     */
    public function preventasSearch_get() {
        $entityId = $this->get('id_entidad');
        $query = $this->get('query');
        $idUser = $this->get('id_user');
        $field = $this->get('field_id');
        $return = $this->get('return');
        $type = $this->get('type');
        $id_delegacion = $this->get('id_delegacion');
        $id_canal_venta = $this->get('id_canal_venta');

        $result = $this->usuario_model->searchByUserType($entityId, $field, $query, $return, $type, 'PREVENTA', $id_delegacion, $id_canal_venta);
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
     * @param id_delegacion
     * @param id_canal_venta
     * returns Array((value, description))
     */
    public function repartidoresSearch_get() {
        $entityId = $this->get('id_entidad');
        $query = $this->get('query');
        $idUser = $this->get('id_user');
        $field = $this->get('field_id');
        $return = $this->get('return');
        $type = $this->get('type');
        $id_delegacion = $this->get('id_delegacion');
        $id_canal_venta = $this->get('id_canal_venta');

        $result = $this->usuario_model->searchByUserType($entityId, $field, $query, $return, $type, 'REPARTIDOR', $id_delegacion, $id_canal_venta);
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
     * @param id_delegacion
     * @param id_canal_venta
     * returns Array((value, description))
     */
    public function autoventasSearch_get() {
        $entityId = $this->get('id_entidad');
        $query = $this->get('query');
        $idUser = $this->get('id_user');
        $field = $this->get('field_id');
        $return = $this->get('return');
        $type = $this->get('type');
        $id_delegacion = $this->get('id_delegacion');
        $id_canal_venta = $this->get('id_canal_venta');

        $result = $this->usuario_model->searchByUserType($entityId, $field, $query, $return, $type, 'AUTOVENTA', $id_delegacion, $id_canal_venta);
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
     * returns Array((value, description))
     */
    public function searchNotInEntity_get() {
        $entityId = $this->get('id_entidad');
        $query = $this->get('query');
        $idUser = $this->get('id_user');
        $field = $this->get('field_id');
        $return = $this->get('return');
        $type = $this->get('type');

        $result = $this->usuario_model->searchNotInEntity($entityId, $field, $query, $return, $type);
        //echo json_encode($result);
        $this->response($result);
    }

    /**
     * Funcion que realiza una busqueda en base a los parametros indicados
     *
     * @param id_entidad
     * @param proveedorPk
     * @param field_id
     * @param query
     * @param return=null
     * @param type
     * returns Array((value, description))
     */
    public function searchNotInProv_get() {
        $entityId = $this->get('id_entidad');
        $proveedorPk = $this->get('proveedorPk');
        $query = $this->get('query');
        $idUser = $this->get('id_user');
        $field = $this->get('field_id');
        $return = $this->get('return');
        $type = $this->get('type');

        $result = $this->usuario_model->searchNotInProv($entityId, $proveedorPk, $field, $query, $return, $type);
        //echo json_encode($result);
        $this->response($result);
    }


}

?>