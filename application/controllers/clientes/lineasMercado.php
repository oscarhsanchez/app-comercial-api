<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php
require_once(APPPATH.EF_BASE_CONTROLLER);
require_once(APPPATH.ENTITY_USER);
require_once(APPPATH.ENTITY_APIERROR);
require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_LINEA_MERCADO);

class lineasMercado extends ef_controller {
    public $controller = "eFinanzas-ClienteBundle-Controller-DefaultController";
	
    function __construct() {
        parent::__construct();
        $this->load->model('usuarios/session_model');
        $this->load->model('security_model');
        $this->load->model('usuarios/usuario_model');
        $this->load->model('clientes/cliente_model');
        $this->load->model('entidad/entity_model');
        $this->load->model('clientes/cliente_linea_mercado_model');
        $this->load->helper("esocialutils");
    }

    /**
     *  Inserta o actualiza un listado de lineasMercado para una entidad, a partir del token de cada elemento.
     *
     * @param lineasMercado -> Listado de lineasMercado en Base64
     *
     * return result, error
     *
     */
    public function listSetByToken_post() {

        $action = "edit";

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('lineasMercado');
        if(!$this->validateParams($this->input->post(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $omittedFieldsPost = $this->input->post('omittedFields');
        $omittedFields = json_decode($omittedFieldsPost);

        $lineasMercadoPost = $this->input->post('lineasMercado');
        $lineasMercado = json_decode(base64_decode($lineasMercadoPost));


        if (!$lineasMercado) {
            $err = new APIerror(PARAM_VERIFICATION_ERROR);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        //Hacemos todo en una unica transacion
        $this->db->trans_start();

        foreach ($lineasMercado as $lineaMercado) {

            //Comprobamos si existe el Token para determinar si un update o un insert
            $_lineaMercadoDb = $this->cliente_linea_mercado_model->getLineaMercadoByToken($lineaMercado->token);

            $newlineaMercado = new linea_mercado();
            $newlineaMercado->set($lineaMercado);

            //Comprobamos que la entidad indicada corresponde con la entitySecret
            if ($newlineaMercado->fk_entidad != null && $newlineaMercado->fk_entidad != $this->entity->pk_entidad) {
                $err = new APIerror(ENTITY_VERIFICATION_ERROR);
                $result = $err->getValues();
                $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
                $this->response(array('error' => $result), 200);
            }

            if ($_lineaMercadoDb) {
                $newlineaMercado->pk_linea_mercado = $_lineaMercadoDb->pk_linea_mercado;
                $newlineaMercado->fk_entidad = $_lineaMercadoDb->fk_entidad;
            }

            try {
                $newlineaMercado->fk_entidad = $this->entity->pk_entidad;
                $this->cliente_linea_mercado_model->savelineaMercado($newlineaMercado, $omittedFields);
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