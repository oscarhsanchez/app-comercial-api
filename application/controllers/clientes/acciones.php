<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php
require_once(APPPATH.VALLAS_BASE_CONTROLLER);
require_once(APPPATH.ENTITY_APIERROR);
require_once(APPPATH.ENTITY_CONTACTO_CLIENTE);



class acciones extends generic_controller {
   	
    function __construct() {
        parent::__construct();
        $this->load->model('acciones_clientes/accion_model');
        $this->load->model('clientes/cliente_model');
    }

    /**
     * Example Like : field1 = [%search%] & field2 = [%search & field3 = search%]
     *
     * @header Authorization
     *
     * @param offset (Opcional)
     * @param limit (Opcional)
     * @param sort (Opcional) : [field1_ASC, field2_DESC]
     * @param pagination (Opcional): {"active":1, "pageSize": 200, "page":0, "totalPages":null, "cache_token":null}
     *
     * @RequiresPermission ["clientes", "R"]
     *
     */
    public function relation_get($p_cliente)
    {

        $this->checkSecurity(__FUNCTION__);

        $get_vars = $this->get();
        unset($get_vars[$p_cliente]);

        $offset = $this->get("offset");
        $limit = $this->get("limit");
        $sort = $this->get("sort");
        $pagination = json_decode($this->get('pagination'));

        try {
            //Obtenemos el cliente
            $cliente = $this->cliente_model->getBy("pk_cliente", $p_cliente, $this->session->fk_pais);
            //Si no existe lo buscamos por el token
            if (!$cliente)
                $cliente = $this->cliente_model->getBy("token", $p_cliente, $this->session->fk_pais);

        } catch (\Exception $e) {
            $err = new APIerror(INVALID_PROPERTY_NAME);
            $result = $err->getValues();
            $this->response(array('error' => $result), 200);
        }

        if (!$cliente) {
            $err = new APIerror(ERROR_GETTING_INFO);
            $result = $err->getValues();
            $this->response(array('error' => $result), 200);
        }

        try {
            $result = $this->accion_model->getAll($get_vars, $this->session->fk_pais, $offset, $limit, $sort, $pagination);
            $cliente->acciones = $result["result"];
            if ($result["pagination"])
                $this->response(array('result' => 'OK', 'pagination' => $result["pagination"], 'cliente' => $cliente), 200);
            else
                $this->response(array('result' => 'OK', 'cliente' => $cliente), 200);

        } catch (\Exception $e) {
            $err = new APIerror(INVALID_PROPERTY_NAME);
            $result = $err->getValues();
            $this->response(array('error' => $result), 200);
        }


    }




}

?>