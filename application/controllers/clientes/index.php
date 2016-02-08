<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php
require_once(APPPATH.VALLAS_BASE_CONTROLLER);
require_once(APPPATH.ENTITY_APIERROR);
require_once(APPPATH.ENTITY_CLIENTE);



class index extends ef_controller {
   	
    function __construct() {
        parent::__construct();
        $this->load->model('clientes/cliente_model');
    }

    /**
     *
     * @RequiresPermission ["clientes", "R"]
     *
     */
    public function index_get()
    {

        $this->checkSecurity(__FUNCTION__);

        $this->response(array('result' => 'OK', 'usuarios' => __FUNCTION__), 200);


    }

    /**
     *
     * @RequiresPermission ["clientes", "C"]
     *
     */
    function index_post()
    {
        $data = array('returned: '. $this->post('id'));
        $this->response($data);
    }

    /**
     *
     * @RequiresPermission ["clientes", "U"]
     *
     */
    function index_put()
    {
        $data = array('returned: '. $this->put('id'));
        $this->response($data);
    }

    /**
     *
     * @RequiresPermission ["clientes", "D"]
     *
     */
    function index_delete()
    {
        $data = array('returned: '. $this->delete('id'));
        $this->response($data);
    }
  
}

?>