<?php

// This can be removed if you use __autoload() in config.php
require_once(APPPATH.VALLAS_BASE_CONTROLLER);


class index extends generic_controller
{


	function __construct()
    {
    	parent::__construct();
        $this->load->model("usuarios/user_model");
    }

    /**
     *
     * @RequiresPermission ["users", "R"]
     *
     */
    public function index_get()
    {
        $this->checkSecurity(__FUNCTION__);

        $offset = $this->get("offset");
        $limit = $this->get("limit");
        $pagination = $this->get("pagination");

        try {
            $users = $this->user_model->getAll($this->get(), $this->session->fk_pais, $offset, $limit, $pagination);
            $this->response(array('result' => 'OK', 'usuarios' => $users), 200);
        } catch (\Exception $e) {
            $err = new APIerror(INVALID_PROPERTY_NAME);
            $result = $err->getValues();
            $this->response(array('error' => $result), 200);
        }
    }

    /**
     *
     * @RequiresPermission ["users", "C"]
     *
     */
    function index_post()
    {
        $data = array('returned: '. $this->post('id'));
        $this->response($data);
    }

    /**
     *
     * @RequiresPermission ["users", "U"]
     *
     */
    function index_put()
    {
        $data = array('returned: '. $this->put('id'));
        $this->response($data);
    }

    /**
     *
     * @RequiresPermission ["users", "D"]
     *
     */
    function index_delete()
    {
        $data = array('returned: '. $this->delete('id'));
        $this->response($data);
    }


}

?>