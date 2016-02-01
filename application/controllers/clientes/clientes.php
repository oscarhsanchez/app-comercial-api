<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php
require_once(APPPATH.EF_BASE_CONTROLLER);
require_once(APPPATH.ENTITY_USER);
require_once(APPPATH.ENTITY_APIERROR);
require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_CLIENTE_CONTACTO);


class clientes extends ef_controller {
    public $controller = "eFinanzas-ClienteBundle-Controller-DefaultController";
	
    function __construct() {
        parent::__construct();
        $this->load->model('usuarios/session_model');
        $this->load->model('security_model');
        $this->load->model('usuarios/usuario_model');
        $this->load->model('clientes/cliente_model');
        $this->load->model('clientes/cliente_contacto_model');
        $this->load->model('entidad/entity_model');
        $this->load->helper("esocialutils");
    }


    /**
     * @param $clientPk
     *
     * Devuelve un cliente a partir de su clave primaria
     */
    public function byPk_get()  {

        $action = "index";

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('clientPk');
        if(!$this->validateParams($this->input->get(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $clientPk = $this->input->get('clientPk');

        //obtnemos el cliente
        try {
            $client = $this->cliente_model->getClientByPk($this->entity->pk_entidad, $clientPk);
        } catch (\Exception $e) {
            $err = new APIerror(ERROR_GETTING_INFO);
            $result = $err->getValues();
            $this->log_model->logWithSession($e, $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        if ($client) {

            //Verificamos que el cliente corresponde con la entidad solicitada
            if ($this->entity->pk_entidad != $client->fk_entidad) {
                $err = new APIerror(ENTITY_VERIFICATION_ERROR);
                $result = $err->getValues();
                $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
                $this->response(array('result' => 'error', 'error' => $result), 200);
            }

            $this->response(array('result' => 'OK', 'client' => $client), 200);

        } else {
            $err = new APIerror(ERROR_GETTING_INFO);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }

    }

    /**
     *  Devuelve los clientes de la entidad.
     *
     * @param state
     * @param offset (Optional)
     * @param limit (Optional)
     *
     * return Array(Articulo)
     *
     */
    public function list_get() {

        $action = "index";

        $this->client_access = true;

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('state');
        if (!$this->validateParams($this->get(), $params)) {
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $state = $this->get('state');
        $offset = $this->get('offset');
        $limit = $this->get('limit');

        $nomCli = $this->get('nomCli');
        $codCli = $this->get('codCli');

        //obtnemos los cliente
        try {
            $result = $this->cliente_model->getAll($this->entity->pk_entidad, $nomCli, $codCli, $state, $offset, $limit);
        } catch (\Exception $e) {
            $err = new APIerror(ERROR_GETTING_INFO);
            $result = $err->getValues();
            $this->log_model->logWithSession($e, $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $this->response(array('result' => 'OK', 'clientes' => $result["clientes"]), 200);


    }

    /**
     * Devuelve los clientes asociados a un usuario.
     * Este metodo devuelve los clientes en bloques utilizando cache para esperar las siguientes peticiones.
     *
     * @param userPk
     * @param lastupdate
     * @param stete
     * @param pagination {"pageSize": 200, "page":0, "totalPages":null, "cache_token":null} Ej. Primera peticion
     *
     * @return result, Array(cliente) | result, error
     */
    public function byUserAssignedCachedMultipart_get() {
        //$this->response(array('error' => "nose"), 501);
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
    
    	//obtnemos el cliente
        try {
    	    $result = $this->cliente_model->getMultipartCachedClientsAssignedToUser($userPk, $this->entity->pk_entidad, $pagination, $state, $lastUpdate);
        } catch (\Exception $e) {
            $err = new APIerror(ERROR_GETTING_INFO);
            $result = $err->getValues();
            $this->log_model->logWithSession($e, $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }
    
    	$this->response(array('result' => 'OK', 'pagination' => $result["pagination"], 'clientes' => $result["clientes"]), 200);

    
    }

    /**
     * Devuelve los clientes de una entidad.
     * Este metodo devuelve los clientes en bloques utilizando cache para esperar las siguientes peticiones.
     *
     * @param lastupdate
     * @param stete
     * @param pagination {"pageSize": 200, "page":0, "totalPages":null, "cache_token":null} Ej. Primera peticion
     *
     * @return result, Array(cliente) | result, error
     */
    public function cachedMultipart_get() {
        //$this->response(array('error' => "nose"), 501);
        $action = "index";

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('lastUpdate', 'pagination', 'state');
        if (!$this->validateParams($this->get(), $params)) {
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $lastUpdate = $this->get('lastUpdate');
        $pagination = json_decode($this->get('pagination'));
        $state = $this->get('state');

        //obtnemos el cliente
        try {
            $result = $this->cliente_model->getMultipartCachedClients($this->entity->pk_entidad, $pagination, $state, $lastUpdate);
        } catch (\Exception $e) {
            $err = new APIerror(ERROR_GETTING_INFO);
            $result = $err->getValues();
            $this->log_model->logWithSession($e, $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }
        $this->response(array('result' => 'OK', 'pagination' => $result["pagination"], 'clientes' => $result["clientes"]), 200);


    }

    /**
     * Funcion que se encarga de registrar a un cliente
     *
     * @param cliente
     *
     * returns Array(OK, Cliente) | error
     */
    public function register_post() {

        // Check for required parameters
        $params=array('cliente', 'entitysecret');
        if(!$this->validateParams($this->input->post(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }


        $clientePost = $this->input->post('cliente');
        $cliente = json_decode($clientePost);
        $entitySecret = $this->input->post('entitysecret');

        $entity = $this->entity_model->getEntityBySecretKey($entitySecret);

        if (!$entity) {
            $err = new APIerror(ENTITY_NOT_FOUND);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 400);
        }


        if (!$cliente) {
            $err = new APIerror(PARAM_VERIFICATION_ERROR);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $newClient = new cliente();
        $newClient->set($cliente);

        $newClient->estado = 1;
        $newClient->fk_entidad = $entity->pk_entidad;

        $newClient->bool_albaran_valorado = 1;
        $newClient->bool_asignacion_generica = 0;
        $newClient->bool_es_captacion = 0;
        $newClient->bool_facturacion_final_mes = 0;

        $newClient->puntos = 0;

        //ASignamos un token_tpv
        $newClient->token_tpv = getToken();

        //ASignamos un codigo
        $newClient->cod_cliente = $this->cliente_model->getCode($entity->pk_entidad);

        if (!$newClient->cod_cliente) {
            $err = new APIerror(ERROR_SAVING_DATA);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $result = $this->cliente_model->saveCliente($newClient);

        if ($result) {
            $cliente = $this->cliente_model->getClientByTokenTpv($newClient->token_tpv, $entity->pk_entidad);
            $this->response(array('result' => 'OK', 'cliente' => $cliente), 200);
        } else {
            $err = new APIerror(ERROR_SAVING_DATA);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

    }

    /**
     * Funcion que se encarga de registrar a un cliente
     *
     * @param cliente
     *
     * returns Array(OK, Cliente) | error
     */
    public function registerWithContacto_post() {

        // Check for required parameters
        $params=array('cliente', 'entitysecret', 'contacto');
        if(!$this->validateParams($this->input->post(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }


        $clientePost = $this->input->post('cliente');
        $cliente = json_decode($clientePost);
        $contactoPost = $this->input->post('contacto');
        $contacto = json_decode($contactoPost);
        $entitySecret = $this->input->post('entitysecret');

        $entity = $this->entity_model->getEntityBySecretKey($entitySecret);

        if (!$entity) {
            $err = new APIerror(ENTITY_NOT_FOUND);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 400);
        }


        if (!$cliente || !$contacto) {
            $err = new APIerror(PARAM_VERIFICATION_ERROR);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $newClient = new cliente();
        $newClient->set($cliente);

        $newClient->estado = 1;
        $newClient->fk_entidad = $entity->pk_entidad;

        $newClient->bool_albaran_valorado = 1;
        $newClient->bool_asignacion_generica = 0;
        $newClient->bool_es_captacion = 0;
        $newClient->bool_facturacion_final_mes = 0;

        $newClient->puntos = 0;

        //ASignamos un token_tpv
        $newClient->token_tpv = getToken();

        //ASignamos un codigo
        $newClient->cod_cliente = $this->cliente_model->getCode($entity->pk_entidad);

        if (!$newClient->cod_cliente) {
            $err = new APIerror(ERROR_SAVING_DATA);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $result = $this->cliente_model->saveCliente($newClient);

        if ($result) {
            $cliente = $this->cliente_model->getClientByTokenTpv($newClient->token_tpv, $entity->pk_entidad);

            //Guardamos el contacto
            $newContacto = new cliente_contacto();
            $newContacto->set($contacto);

            $newContacto->estado = 1;
            $newContacto->token = getToken();
            $newContacto->fk_cliente = $cliente->pk_cliente;

            $result = $this->cliente_contacto_model->saveContacto($newContacto);

            if ($result)
                $this->response(array('result' => 'OK', 'cliente' => $cliente), 200);
            else {
                $err = new APIerror(ERROR_SAVING_DATA);
                $result = $err->getValues();
                $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $entitySecret, $this->deviceId, $this->rec_token);
                $this->response(array('error' => $result), 200);
            }
        } else {
            $err = new APIerror(ERROR_SAVING_DATA);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

    }

    /**
     *  Inserta o actualiza un listado de clientes para una entidad, a partir del token de cada elemento.
     *
     * @param clientes -> Listado de clientes en Base64
     *
     * return result, error
     *
     */
    public function listSetByToken_post()
    {

        $action = "edit";

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('clientes');
        if(!$this->validateParams($this->input->post(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $omittedFieldsPost = $this->input->post('omittedFields');
        $omittedFields = json_decode($omittedFieldsPost);

        $clientsPost = $this->input->post('clientes');
        $clients = json_decode(base64_decode($clientsPost));


        if (!$clients) {
            $err = new APIerror(PARAM_VERIFICATION_ERROR);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        //Hacemos todo en una unica transacion
        $this->db->trans_start();

        foreach ($clients as $client) {
            //Comprobamos si existe el Token para determinar si un update o un insert
            $_clientDb = $this->cliente_model->getclientByToken($client->token);

            $newClient = new cliente();
            $newClient->set($client);

            if (!$newClient->bool_albaran_valorado)  $newClient ->bool_albaran_valorado = 1;
            if (!$newClient->bool_facturacion_final_mes)  $newClient ->bool_facturacion_final_mes = 1;
            if (!$newClient->puntos)  $newClient ->puntos = 0;

            //Comprobamos que la entidad indicada corresponde con la entitySecret
            if ($newClient->fk_entidad != null && $newClient->fk_entidad != $this->entity->pk_entidad) {
                $err = new APIerror(ENTITY_VERIFICATION_ERROR);
                $result = $err->getValues();
                $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
                $this->response(array('error' => $result), 200);
            }

            if ($_clientDb) {

                $newClient->pk_cliente = $_clientDb->pk_cliente;
                $newClient->fk_entidad = $_clientDb->fk_entidad;

            }

            try {
                $newClient->fk_entidad = $this->entity->pk_entidad;
                $this->cliente_model->saveCliente($newClient, $omittedFields);
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
     *  Inserta o actualiza un listado de relaciones usuario cliente, a partir del token de cada elemento.
     *
     * @param usuariosClientes -> Listado de r_usu_cli en Base64
     *
     * return result, error
     *
     */
    public function usuarioClienteListSetByToken_post()
    {

        $action = "edit";

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('usuariosClientes');
        if(!$this->validateParams($this->input->post(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $rUsuCliPost = $this->input->post('usuariosClientes');
        $rUsuClis = json_decode(base64_decode($rUsuCliPost));


        if (!$rUsuClis) {
            $err = new APIerror(PARAM_VERIFICATION_ERROR);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        //Hacemos todo en una unica transacion
        $this->db->trans_start();

        foreach ($rUsuClis as $rUsuCli) {

            $_rUsuCliDb = $this->cliente_model->getRUsuCliByToken($rUsuCli->token);

            $newRUsuCli = new r_usu_cli();
            $newRUsuCli->set($rUsuCli);

            if ($_rUsuCliDb) {

                $newRUsuCli->pk_usuario_cliente = $_rUsuCliDb->pk_usuario_cliente;
            }

            try {
                $newRUsuCli->fk_entidad = $this->entity->pk_entidad;
                $this->cliente_model->saveRUsuCli($newRUsuCli);
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
     * @param $code
     * @param $entitysecret
     *
     * Valida un si el codigo de un codigo es correcto y caso de serlo devuelve el cliente con el token de tpv.
     * El codigo se resetea
     */
    public function validateCode_post()  {

        // Check for required parameters
        $params=array('code', 'entitysecret');
        if(!$this->validateParams($this->input->post(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $code = $this->input->post('code');
        $entitySecret = $this->input->post('entitysecret');

        $entity = $this->entity_model->getEntityBySecretKey($entitySecret);

        if (!$entity) {
            $err = new APIerror(ENTITY_NOT_FOUND);
            $result = $err->getValues();
            $this->log_model->logParams($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()) );
            $this->response(array('error' => $result), 400);
        }

        $cliente = $this->cliente_model->getClientByCodeVerification($code, $entity->pk_entidad);

        if ($cliente) {

            //Resetamos el codigo de verificacion
            $this->cliente_model->resetCodeVerification($cliente->pk_cliente, $entity->pk_entidad);

            $this->response(array('result' => 'OK', 'cliente' => $cliente), 200);

        } else {
            $err = new APIerror(ERROR_GETTING_INFO);
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
     * returns Array((value, description))
     */
    public function search_get() {
        $entityId = $this->get('id_entidad');
        $query = $this->get('query');
        $idUser = $this->get('id_user');
        $field = $this->get('field_id');
        $return = $this->get('return');
        $type = $this->get('type');

        $result = $this->cliente_model->search($entityId, $field, $query, $return, $type);
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
    public function userAssignedSearch_get() {
        $entityId = $this->get('id_entidad');
        $query = $this->get('query');
        $idUser = $this->get('id_user');
        $field = $this->get('field_id');
        $return = $this->get('return');
        $type = $this->get('type');

        $result = $this->cliente_model->userAssignedsearch($entityId, $field, $query, $return, $type, $idUser);
        //echo json_encode($result);
        $this->response($result);
    }
    
}

?>