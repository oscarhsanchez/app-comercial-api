<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php
require_once(APPPATH.EF_BASE_CONTROLLER);
require_once(APPPATH.ENTITY_USER);
require_once(APPPATH.ENTITY_APIERROR);
require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_CLIENTE_AGRUPACION);

class consultas extends ef_controller {
    public $controller = "eFinanzas-ClienteBundle-Controller-DefaultController";
	
    function __construct() {
        parent::__construct();
        $this->load->model('usuarios/session_model');
        $this->load->model('security_model');
        $this->load->model('usuarios/usuario_model');
        $this->load->model('clientes/cliente_model');
        $this->load->model('entidad/entity_model');
        $this->load->model('clientes/consultas_model');
        $this->load->helper("esocialutils");
    }


    /**
     * Devuelve el consumo total de un cliente por pedido.
     * 
     * @param $clientePk
     * @param $filtro (fechaIni, fechaFin, anios, meses, proveedores, marcas, articulos, grupos, familias, subfamilias, limit)
     */
    public function consumoByPedido_get()  {

        $action = "index";

        $this->client_access = true;

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('clientePk', 'filtro');
        if(!$this->validateParams($this->input->get(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $clientePk = $this->get('clientePk');
        $filter = json_decode($this->get('filtro'));

        $result = $this->consultas_model->getTotalPedidos($this->entity->pk_entidad, $clientePk, $filter);

        if ($result) {
            $this->response(array('result' => 'OK', 'total' => $result["total"]), 200);
        } else {
            $err = new APIerror(ERROR_GETTING_INFO);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }

    }

    /**
     * Devuelve el consumo total de un cliente por albaran.
     *
     * @param $clientePk
     * @param $filtro (fechaIni, fechaFin, anios, meses, proveedores, marcas, articulos, grupos, familias, subfamilias, limit)
     */
    public function consumoByAlbaran_get()  {

        $action = "index";

        $this->client_access = true;

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('clientePk', 'filtro');
        if(!$this->validateParams($this->input->get(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $clientePk = $this->get('clientePk');
        $filter = json_decode($this->get('filtro'));

        $result = $this->consultas_model->getTotalAlbaranes($this->entity->pk_entidad, $clientePk, $filter);

        if ($result) {
            $this->response(array('result' => 'OK', 'total' => $result["total"]), 200);
        } else {
            $err = new APIerror(ERROR_GETTING_INFO);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }

    }

    /**
     * Devuelve el consumo total de un cliente por facturas.
     *
     * @param $clientePk
     * @param $filtro (fechaIni, fechaFin, anios, meses, proveedores, marcas, articulos, grupos, familias, subfamilias, limit)
     */
    public function consumoByFactura_get()  {

        $action = "index";

        $this->client_access = true;

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('clientePk', 'filtro');
        if(!$this->validateParams($this->input->get(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $clientePk = $this->get('clientePk');
        $filter = json_decode($this->get('filtro'));

        $result = $this->consultas_model->getTotalFacturas($this->entity->pk_entidad, $clientePk, $filter);

        if ($result) {
            $this->response(array('result' => 'OK', 'total' => $result["total"]), 200);
        } else {
            $err = new APIerror(ERROR_GETTING_INFO);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }

    }

    /**
     * Devuelve el consumo total de un cliente por albaran agrupado por mes y año.
     *
     * @param $clientePk
     * @param $filtro (fechaIni, fechaFin, anios, meses, proveedores, marcas, articulos, grupos, familias, subfamilias, limit)
     */
    public function consumoMensualByAlbaran_get()  {

        $action = "index";

        $this->client_access = true;

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('clientePk', 'filtro');
        if(!$this->validateParams($this->input->get(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $clientePk = $this->get('clientePk');
        $filter = json_decode($this->get('filtro'));

        $result = $this->consultas_model->getTotalAlbaranesByMonth($this->entity->pk_entidad, $clientePk, $filter);

        if ($result) {
            $this->response(array('result' => 'OK', 'consumoMensual' => $result["consumoMensual"]), 200);
        } else {
            $err = new APIerror(ERROR_GETTING_INFO);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }

    }

    /**
     * Devuelve el consumo por articulo.
     *
     * @param $clientePk
     * @param $filtro (fechaIni, fechaFin, anios, meses, proveedores, marcas, articulos, grupos, familias, subfamilias, limit)
     */
    public function topArticulosByAlbaran_get()  {

        $action = "index";

        $this->client_access = true;

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('clientePk', 'filtro');
        if(!$this->validateParams($this->input->get(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $clientePk = $this->get('clientePk');
        $filter = json_decode($this->get('filtro'));

        $result = $this->consultas_model->getTopArticulosByAlbaran($this->entity->pk_entidad, $clientePk, $filter);

        if ($result) {
            $this->response(array('result' => 'OK', 'topArticulos' => $result["topArticulos"]), 200);
        } else {
            $err = new APIerror(ERROR_GETTING_INFO);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }

    }

    /**
 * Devuelve el consumo por subfamilia.
 *
 * @param $clientePk
 * @param $filtro (fechaIni, fechaFin, anios, meses, proveedores, marcas, articulos, grupos, familias, subfamilias, limit)
 */
    public function consumoSubfamiliasByAlbaran_get()  {

        $action = "index";

        $this->client_access = true;

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('clientePk', 'filtro');
        if(!$this->validateParams($this->input->get(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $clientePk = $this->get('clientePk');
        $filter = json_decode($this->get('filtro'));

        $result = $this->consultas_model->getConsumoSubfamiliasByAlbaran($this->entity->pk_entidad, $clientePk, $filter);

        if ($result) {
            $this->response(array('result' => 'OK', 'consumos' => $result["consumos"]), 200);
        } else {
            $err = new APIerror(ERROR_GETTING_INFO);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }

    }

    /**
     * Devuelve el consumo por familia.
     *
     * @param $clientePk
     * @param $filtro (fechaIni, fechaFin, anios, meses, proveedores, marcas, articulos, grupos, familias, subfamilias, limit)
     */
    public function consumoFamiliasByAlbaran_get()  {

        $action = "index";

        $this->client_access = true;

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('clientePk', 'filtro');
        if(!$this->validateParams($this->input->get(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $clientePk = $this->get('clientePk');
        $filter = json_decode($this->get('filtro'));

        $result = $this->consultas_model->getConsumoFamiliasByAlbaran($this->entity->pk_entidad, $clientePk, $filter);

        if ($result) {
            $this->response(array('result' => 'OK', 'consumos' => $result["consumos"]), 200);
        } else {
            $err = new APIerror(ERROR_GETTING_INFO);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }

    }

    /**
     * Devuelve el consumo por grupos.
     *
     * @param $clientePk
     * @param $filtro (fechaIni, fechaFin, anios, meses, proveedores, marcas, articulos, grupos, familias, subfamilias, limit)
     */
    public function consumoGruposByAlbaran_get()  {

        $action = "index";

        $this->client_access = true;

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('clientePk', 'filtro');
        if(!$this->validateParams($this->input->get(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $clientePk = $this->get('clientePk');
        $filter = json_decode($this->get('filtro'));

        $result = $this->consultas_model->getConsumoGruposByAlbaran($this->entity->pk_entidad, $clientePk, $filter);

        if ($result) {
            $this->response(array('result' => 'OK', 'consumos' => $result["consumos"]), 200);
        } else {
            $err = new APIerror(ERROR_GETTING_INFO);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }

    }

    /**
     * Devuelve el consumo por marcas.
     *
     * @param $clientePk
     * @param $filtro (fechaIni, fechaFin, anios, meses, proveedores, marcas, articulos, grupos, familias, subfamilias, limit)
     */
    public function consumoMarcasByAlbaran_get()  {

        $action = "index";

        $this->client_access = true;

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('clientePk', 'filtro');
        if(!$this->validateParams($this->input->get(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $clientePk = $this->get('clientePk');
        $filter = json_decode($this->get('filtro'));

        $result = $this->consultas_model->getConsumoMarcasByAlbaran($this->entity->pk_entidad, $clientePk, $filter);

        if ($result) {
            $this->response(array('result' => 'OK', 'consumos' => $result["consumos"]), 200);
        } else {
            $err = new APIerror(ERROR_GETTING_INFO);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }

    }

    /**
     * Devuelve el consumo por proveedores.
     *
     * @param $clientePk
     * @param $filtro (fechaIni, fechaFin, anios, meses, proveedores, marcas, articulos, grupos, familias, subfamilias, limit)
     */
    public function consumoProveedoresByAlbaran_get()  {

        $action = "index";

        $this->client_access = true;

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('clientePk', 'filtro');
        if(!$this->validateParams($this->input->get(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $clientePk = $this->get('clientePk');
        $filter = json_decode($this->get('filtro'));

        $result = $this->consultas_model->getConsumoProveedoresByAlbaran($this->entity->pk_entidad, $clientePk, $filter);

        if ($result) {
            $this->response(array('result' => 'OK', 'consumos' => $result["consumos"]), 200);
        } else {
            $err = new APIerror(ERROR_GETTING_INFO);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }

    }

    /**
     * Devuelve el consumo para el año actual y los años indicados en el filtro.
     * IMPORTANTE: No tienen en cuenta los filtro de fechas ni de meses
     *
     * @param $clientePk
     * @param $filtro (fechaIni, fechaFin, anios, meses, proveedores, marcas, articulos, grupos, familias, subfamilias, limit)
     */
    public function comprativaConsumoByAlbaran_get()  {

        $action = "index";

        $this->client_access = true;

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('clientePk', 'filtro');
        if(!$this->validateParams($this->input->get(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $clientePk = $this->get('clientePk');
        $filter = json_decode($this->get('filtro'));

        $result = $this->consultas_model->getComprativaConsumoByAlbaran($this->entity->pk_entidad, $clientePk, $filter);

        if ($result) {
            $this->response(array('result' => 'OK', 'consumos' => $result["consumos"]), 200);
        } else {
            $err = new APIerror(ERROR_GETTING_INFO);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }

    }

    /**
     * Devuelve el consumo para el año actual y los años indicados en el filtro, agrupado por mes.
     * IMPORTANTE: No tienen en cuenta los filtro de fechas ni de meses
     *
     * @param $clientePk
     * @param $filtro (fechaIni, fechaFin, anios, meses, proveedores, marcas, articulos, grupos, familias, subfamilias, limit)
     */
    public function comprativaConsumoMensualByAlbaran_get()  {

        $action = "index";

        $this->client_access = true;

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('clientePk', 'filtro');
        if(!$this->validateParams($this->input->get(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $clientePk = $this->get('clientePk');
        $filter = json_decode($this->get('filtro'));

        $result = $this->consultas_model->getComprativaConsumoMensualByAlbaran($this->entity->pk_entidad, $clientePk, $filter);

        if ($result) {
            $this->response(array('result' => 'OK', 'consumos' => $result["consumos"]), 200);
        } else {
            $err = new APIerror(ERROR_GETTING_INFO);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }

    }

    /**
     * Devuelve el consumo para el año actual y los años indicados en el filtro, agrupado por articulo.
     * IMPORTANTE: No tienen en cuenta los filtro de fechas ni de meses
     *
     * @param $clientePk
     * @param $filtro (fechaIni, fechaFin, anios, meses, proveedores, marcas, articulos, grupos, familias, subfamilias, limit)
     */
    public function comprativaConsumoArticulosByAlbaran_get()  {

        $action = "index";

        $this->client_access = true;

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('clientePk', 'filtro');
        if(!$this->validateParams($this->input->get(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $clientePk = $this->get('clientePk');
        $filter = json_decode($this->get('filtro'));

        $result = $this->consultas_model->getComprativaConsumoArticulosByAlbaran($this->entity->pk_entidad, $clientePk, $filter);

        if ($result) {
            $this->response(array('result' => 'OK', 'consumos' => $result["consumos"]), 200);
        } else {
            $err = new APIerror(ERROR_GETTING_INFO);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }

    }

    /**
     * Devuelve el consumo para el año actual y los años indicados en el filtro, agrupado por subfamilia.
     * IMPORTANTE: No tienen en cuenta los filtro de fechas ni de meses
     *
     * @param $clientePk
     * @param $filtro (fechaIni, fechaFin, anios, meses, proveedores, marcas, articulos, grupos, familias, subfamilias, limit)
     */
    public function comprativaConsumoSubfamiliasByAlbaran_get()  {

        $action = "index";

        $this->client_access = true;

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('clientePk', 'filtro');
        if(!$this->validateParams($this->input->get(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $clientePk = $this->get('clientePk');
        $filter = json_decode($this->get('filtro'));

        $result = $this->consultas_model->getComprativaConsumoSubfamiliasByAlbaran($this->entity->pk_entidad, $clientePk, $filter);

        if ($result) {
            $this->response(array('result' => 'OK', 'consumos' => $result["consumos"]), 200);
        } else {
            $err = new APIerror(ERROR_GETTING_INFO);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }

    }

    /**
     * Devuelve el consumo para el año actual y los años indicados en el filtro, agrupado por familia.
     * IMPORTANTE: No tienen en cuenta los filtro de fechas ni de meses
     *
     * @param $clientePk
     * @param $filtro (fechaIni, fechaFin, anios, meses, proveedores, marcas, articulos, grupos, familias, subfamilias, limit)
     */
    public function comprativaConsumoFamiliasByAlbaran_get()  {

        $action = "index";

        $this->client_access = true;

        $this->checkSecurity($action);

        // Check for required parameters
        $params=array('clientePk', 'filtro');
        if(!$this->validateParams($this->input->get(),$params)){
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('error' => $result), 200);
        }

        $clientePk = $this->get('clientePk');
        $filter = json_decode($this->get('filtro'));

        $result = $this->consultas_model->getComprativaConsumoFamiliasByAlbaran($this->entity->pk_entidad, $clientePk, $filter);

        if ($result) {
            $this->response(array('result' => 'OK', 'consumos' => $result["consumos"]), 200);
        } else {
            $err = new APIerror(ERROR_GETTING_INFO);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }

    }


}

?>