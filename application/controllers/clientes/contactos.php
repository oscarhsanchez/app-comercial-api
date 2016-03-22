<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php
require_once(APPPATH.VALLAS_BASE_CONTROLLER);
require_once(APPPATH.ENTITY_APIERROR);
require_once(APPPATH.ENTITY_CONTACTO_CLIENTE);



class contactos extends generic_controller {
   	
    function __construct() {
        parent::__construct();
        $this->load->model('clientes/contacto_model');
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
            $result = $this->contacto_model->getAll($get_vars, $this->session->fk_pais, $offset, $limit, $sort, $pagination);
            $cliente->contactos = $result["result"];
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
    public function index_get()
    {

        $this->checkSecurity(__FUNCTION__);

        $offset = $this->get("offset");
        $limit = $this->get("limit");
        $sort = $this->get("sort");
        $pagination = json_decode($this->get('pagination'));

        try {
            $result = $this->contacto_model->getAll($this->get(), $this->session->fk_pais, $offset, $limit, $sort, $pagination);
            if ($result["pagination"])
                $this->response(array('result' => 'OK', 'pagination' => $result["pagination"], 'contactos' => $result["result"]), 200);
            else
                $this->response(array('result' => 'OK', 'contactos' => $result["result"]), 200);

        } catch (\Exception $e) {
            $err = new APIerror(INVALID_PROPERTY_NAME);
            $result = $err->getValues();
            $this->response(array('error' => $result), 200);
        }


    }

    /**
     *
     * @RequiresPermission ["clientes", "C"]
     *
     */
    function index_post()
    {
        $this->checkSecurity(__FUNCTION__);

        try {

            if (sizeof($this->post()) == 0) {
                $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
                $result = $err->getValues();
                $this->response(array('error' => $result), 200);
            }

            $entity = null;
            $entityPost = $this->input->post('entity');
            if ($entityPost)
                $entity = json_decode(base64_decode($entityPost));

            $array = null;
            $arrayPost = $this->input->post('array');
            if ($arrayPost)
                $array = json_decode(base64_decode($arrayPost));


            $result = $this->contacto_model->create($entity, $array, $this->session->fk_pais);
            if ($result)
                $this->response(array('result' => 'OK'), 200);
            else {
                $err = new APIerror(ERROR_SAVING_DATA);
                $result = $err->getValues();
                $this->response(array('error' => $result), 200);
            }


        } catch (\Exception $e) {
            $err = new APIerror(INVALID_PROPERTY_NAME);
            $result = $err->getValues();
            $this->response(array('error' => $result), 200);
        }
    }

    /**
     *
     * @param entityParam = value
     * @QueryParam condition
     *
     * @RequiresPermission ["clientes", "U"]
     *
     */
    function index_put()
    {

        $this->checkSecurity(__FUNCTION__);

        //Tenemos que unir las variables de la URL y las de QueryParam
        $get_vars = array();
        if ($this->input->get() && $this->get())
            $get_vars = array_merge($this->input->get(), $this->get());
        else if ($this->input->get())
            $get_vars = $this->input->get();
        else if ($this->get())
            $get_vars = $this->get();

        try {

            if (sizeof($this->put()) == 0) {
                $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
                $result = $err->getValues();
                $this->response(array('error' => $result), 200);
            }

            $result = $this->contacto_model->update($get_vars, $this->put(), $this->session->fk_pais);
            if ($result)
                $this->response(array('result' => 'OK'), 200);
            else {
                $err = new APIerror(ERROR_SAVING_DATA);
                $result = $err->getValues();
                $this->response(array('error' => $result), 200);
            }


        } catch (\Exception $e) {
            $err = new APIerror(INVALID_PROPERTY_NAME);
            $result = $err->getValues();
            $this->response(array('error' => $result), 200);
        }
    }

    /**
     *
     * @RequiresPermission ["clientes", "D"]
     *
     */
    function index_delete()
    {
        $this->checkSecurity(__FUNCTION__);

        //Tenemos que unir las variables de la URL y las de QueryParam
        $get_vars = array();
        if ($this->input->get() && $this->get())
            $get_vars = array_merge($this->input->get(), $this->get());
        else if ($this->input->get())
            $get_vars = $this->input->get();
        else if ($this->get())
            $get_vars = $this->get();

        try {

            $result = $this->contacto_model->delete($get_vars, $this->session->fk_pais);
            if ($result)
                $this->response(array('result' => 'OK'), 200);
            else {
                $err = new APIerror(ERROR_SAVING_DATA);
                $result = $err->getValues();
                $this->response(array('error' => $result), 200);
            }


        } catch (\Exception $e) {
            $err = new APIerror(INVALID_PROPERTY_NAME);
            $result = $err->getValues();
            $this->response(array('error' => $result), 200);
        }
    }
  
}

?>