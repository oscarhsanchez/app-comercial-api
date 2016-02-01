<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php
require_once(APPPATH.LIBRARY_RESTJSON);
require_once(APPPATH.ENTITY_USER);
require_once(APPPATH.ENTITY_APIERROR);
require_once(APPPATH.ENTITY_DEVICE);

class ef_controller extends REST_Controller
{

    public $userId;
    public $deviceId;
    public $rec_token;
    public $entitySecret;
    public $entity;

    public $client_access = false;
    public $token_tpv;
    public $cliente;
    
	protected function checkSecurity($action) {
        //Obtenemos Parametros de Cabecera
        $this->userId = $this->input->get_request_header('Userid', TRUE);
        $this->deviceId =  $this->input->get_request_header('Deviceid', TRUE);
        $this->rec_token = $this->input->get_request_header('Token', TRUE);
        $this->entitySecret = $this->input->get_request_header('Entitysecret', TRUE);
        $this->token_tpv = $this->input->get_request_header('Tokentpv', TRUE);

       if (!$this->entitySecret) {

            $err = new APIerror(INVALID_NUMBER_OF_HEADER_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 401);

        }

        $this->entity = $this->entity_model->getEntityBySecretKey($this->entitySecret);

        if (!$this->entity) {
            $err = new APIerror(ENTITY_NOT_FOUND);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 400);
        }
        //Si es usuario
        if (!$this->token_tpv) {

            if (!$this->rec_token || !$this->userId || !$this->deviceId) {

                $err = new APIerror(INVALID_NUMBER_OF_HEADER_PARAMS);
                $result = $err->getValues();
                $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
                $this->response(array('error' => $result), 401);

            }

            $token = $this->session_model->validateSession($this->userId, $this->deviceId, $this->entity->pk_entidad, $this->rec_token);

            if (!$token) {
                $err = new APIerror(INVALID_TOKEN);
                $result = $err->getValues();
                $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
                $this->response(array('error' => $result));
            }

           //Comprobamos si el usuarios tiene permisos
           if (!$this->security_model-> hasPermissionForAction($this->entity->pk_entidad, $this->userId, $this->controller, $action)) {
                $err = new APIerror(ACCESS_FORBIDEN);
                $result = $err->getValues();
                $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
                $this->response(array('error' => $result), 401);
           }
        } else {

            //si es cliente

            if (!$this->client_access) {
                $err = new APIerror(ACCESS_FORBIDEN);
                $result = $err->getValues();
                $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
                $this->response(array('error' => $result), 401);
            }

            $this->cliente = $this->cliente_model->getClientByTokenTpv($this->token_tpv, $this->entity->pk_entidad);

            if (!$this->cliente) {
                $err = new APIerror(INVALID_TOKEN);
                $result = $err->getValues();
                $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
                $this->response(array('error' => $result));
            }
        }
    }

    protected function validateParams($received, $parametros) {
    foreach ($parametros as $param) {
        if (!isset($received[$param])) {
            return false;
        }
    }
    return true;
}

}