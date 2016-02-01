<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php
require_once(APPPATH.EF_BASE_CONTROLLER);
require_once(APPPATH.ENTITY_USER);
require_once(APPPATH.ENTITY_APIERROR);
require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_CLIENTE_COND_PAGO);

class mpv extends ef_controller {
    public $controller = "eFinanzas-ClienteBundle-Controller-DefaultController";

    function __construct() {
        parent::__construct();
        $this->load->model('usuarios/session_model');
        $this->load->model('security_model');
        $this->load->model('usuarios/usuario_model');
        $this->load->model('clientes/cliente_mpv_model');
        $this->load->model('entidad/entity_model');
        $this->load->helper("esocialutils");
    }

    /**
     * Devuelve los mpvs de los clientes de una entidad.
     * Este metodo devuelve los mpv en bloques utilizando cache para esperar las siguientes peticiones.
     *
     * @param lastupdate
     * @param stete
     * @param pagination {"pageSize": 200, "page":0, "totalPages":null, "cache_token":null} Ej. Primera peticion
     *
     * @return result, Array(referencia_mpv) | result, error
     */
    public function cachedMultipart_get() {

        $action = "index";

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array( 'pagination', 'lastUpdate', 'state');
        if (!$this->validateParams($this->get(), $params)) {
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $lastUpdate = $this->get('lastUpdate');
        $pagination = json_decode($this->get('pagination'));
        $state = $this->get('state');


        $result = $this->cliente_mpv_model->getEntidadMultipartCachedReferenciaMpv($this->entity->pk_entidad, $pagination, $state, $lastUpdate);

        $this->response(array('result' => 'OK', 'pagination' => $result["pagination"], 'referencias' => $result["referencias"]), 200);


    }


    /**
     * Devuelve los mpvs de los clientes asociados a un usuario.
     * Este metodo devuelve los mpv en bloques utilizando cache para esperar las siguientes peticiones.
     *
     * @param userPk
     * @param lastupdate
     * @param stete
     * @param pagination {"pageSize": 200, "page":0, "totalPages":null, "cache_token":null} Ej. Primera peticion
     *
     * @return result, Array(referencia_mpv) | result, error
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


        $result = $this->cliente_mpv_model->getMultipartCachedReferenciaMpv($userPk, $this->entity->pk_entidad, $pagination, $state, $lastUpdate);

        $this->response(array('result' => 'OK', 'pagination' => $result["pagination"], 'referencias' => $result["referencias"]), 200);


    }

    /**
     * Devuelve los tipos de mpvs de una delegacion.
     * Este metodo devuelve los tipos mpv en bloques utilizando cache para esperar las siguientes peticiones.
     *
     * @param delegacionPk
     * @param lastupdate
     * @param stete
     * @param pagination {"pageSize": 200, "page":0, "totalPages":null, "cache_token":null} Ej. Primera peticion
     *
     * @return result, Array(referencia_mpv) | result, error
     */
    public function tiposByDelegacionCachedMultipart_get() {

        $action = "index";

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('delegacionPk', 'pagination', 'lastUpdate', 'state');
        if (!$this->validateParams($this->get(), $params)) {
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $delegacionPk = $this->get('delegacionPk');
        $lastUpdate = $this->get('lastUpdate');
        $pagination = json_decode($this->get('pagination'));
        $state = $this->get('state');


        $result = $this->cliente_mpv_model->getMultipartCachedTiposMpv($delegacionPk, $this->entity->pk_entidad, $pagination, $state, $lastUpdate);

        $this->response(array('result' => 'OK', 'pagination' => $result["pagination"], 'tiposMpv' => $result["tiposMpv"]), 200);


    }

    /**
     *  Inserta o actualiza un listado de tipo de MPV, a partir del token de cada elemento.
     *
     * @param tiposMpv -> Listado de tiposMpv en Base64
     *
     * return result, error
     *
     */
    public function tiposListSetByToken_post()
    {

        $action = "edit";

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('tiposMpv');
        if(!$this->validateParams($this->input->post(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $omittedFieldsPost = $this->input->post('omittedFields');
        $omittedFields = json_decode($omittedFieldsPost);

        $tipoPost = $this->input->post('tiposMpv');
        $tipos = json_decode(base64_decode($tipoPost));


        if (!$tipos) {
            $err = new APIerror(PARAM_VERIFICATION_ERROR);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        //Hacemos todo en una unica transacion
        $this->db->trans_start();

        foreach ($tipos as $tipo) {
            //Comprobamos si existe el Token para determinar si un update o un insert
            $_tipoDb = $this->cliente_mpv_model->getTipoMpvByToken($tipo->token);

            $newTipo = new tipo_mpv();
            $newTipo->set($tipo);

            //Comprobamos que la entidad indicada corresponde con la entitySecret
            if ($newTipo->fk_entidad != null && $newTipo->fk_entidad != $this->entity->pk_entidad) {
                $err = new APIerror(ENTITY_VERIFICATION_ERROR);
                $result = $err->getValues();
                $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
                $this->response(array('error' => $result), 200);
            }

            if ($_tipoDb) {

                $newTipo->id = $_tipoDb->id;
                $newTipo->fk_entidad = $_tipoDb->fk_entidad;

            }

            try {
                $newTipo->fk_entidad = $this->entity->pk_entidad;
                $this->cliente_mpv_model->saveTipoMpv($newTipo, $omittedFields);
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
     *  Inserta o actualiza un listado de relacion entre MPV y delegacion, a partir del token de cada elemento.
     *
     * @param rDelMpvs -> Listado de r_del_mpv en Base64
     *
     * return result, error
     *
     */
    public function delegacionMpvListSetByToken_post()
    {

        $action = "edit";

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('rDelMpvs');
        if(!$this->validateParams($this->input->post(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $omittedFieldsPost = $this->input->post('omittedFields');
        $omittedFields = json_decode($omittedFieldsPost);

        $rDelMpvsPost = $this->input->post('rDelMpvs');
        $rDelMpvs = json_decode(base64_decode($rDelMpvsPost));


        if (!$rDelMpvs) {
            $err = new APIerror(PARAM_VERIFICATION_ERROR);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        //Hacemos todo en una unica transacion
        $this->db->trans_start();

        foreach ($rDelMpvs as $rDelMpv) {
            //Comprobamos si existe el Token para determinar si un update o un insert
            $_rDelMpvDb = $this->cliente_mpv_model->getRDelMpvByToken($rDelMpv->token);

            //Comprobamos si en el objeto viene definido el cod_tipo_mpv y el tipoMpv_id esta a nulo para obtenerlo.
            if ((!isset($rDelMpv->tipoMpv_id) || $rDelMpv->tipoMpv_id == null) && isset($rDelMpv->cod_tipo_mpv) && $rDelMpv->cod_tipo_mpv != null) {
                $tipoMpv = $this->cliente_mpv_model->getTipoMpvByCod($rDelMpv->cod_tipo_mpv, $this->entity->pk_entidad);
                if ($tipoMpv)
                    $rDelMpv->tipoMpv_id = $tipoMpv->id;

            }

            $newRDelMpv = new r_del_mpv();
            $newRDelMpv->set($rDelMpv);


            if ($_rDelMpvDb) {
                $newRDelMpv->id = $_rDelMpvDb->id;
            }

            try {
                $this->cliente_mpv_model->saveRDelMpv($newRDelMpv, $omittedFields);
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
     *  Inserta o actualiza un listado de relacion entre MPV y clientes, a partir del token de cada elemento.
     *
     * @param tiposMpv -> Listado de tiposMpv en Base64
     *
     * return result, error
     *
     */
    public function clienteMpvListSetByToken_post()
    {

        $action = "edit";

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('rCliMpvs');
        if(!$this->validateParams($this->input->post(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $omittedFieldsPost = $this->input->post('omittedFields');
        $omittedFields = json_decode($omittedFieldsPost);

        $rCliMpvsPost = $this->input->post('rCliMpvs');
        $rCliMpvs = json_decode(base64_decode($rCliMpvsPost));


        if (!$rCliMpvs) {
            $err = new APIerror(PARAM_VERIFICATION_ERROR);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        //Hacemos todo en una unica transacion
        $this->db->trans_start();

        foreach ($rCliMpvs as $rCliMpv) {
            //Comprobamos si existe el Token para determinar si un update o un insert
            $_rCliMpvDb = $this->cliente_mpv_model->getReferenciaMpvByToken($rCliMpv->token);

            //Comprobamos si en el objeto viene definido el cod_delegacion_stock y el delegacionStock_id esta a nulo para obtenerlo.
            if ((!isset($rCliMpv->delegacionStock_id) || $rCliMpv->delegacionStock_id == null) && isset($rCliMpv->cod_delegacion_stock) && $rCliMpv->cod_delegacion_stock != null) {
                $rDelMpv = $this->cliente_mpv_model->getRDelMpvByCod($rCliMpv->cod_delegacion_stock, $this->entity->pk_entidad);
                if ($rDelMpv)
                    $rCliMpv->delegacionStock_id = $rDelMpv->id;

            }

            $newRCliMpv = new referencia_mpv();
            $newRCliMpv->set($rCliMpv);


            if ($_rCliMpvDb) {
                $newRCliMpv->id = $_rCliMpvDb->id;
            }

            try {
                $this->cliente_mpv_model->saveReferenciaMpv($newRCliMpv, $omittedFields);
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