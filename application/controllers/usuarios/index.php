<?php

// This can be removed if you use __autoload() in config.php
require_once(APPPATH.VALLAS_BASE_CONTROLLER);


class index extends ef_controller
{


	function __construct()
    {
    	parent::__construct();
    }

    /**
     *
     * @RequiresPermission ["users", "R"]
     *
     */
    public function index_get()
    {

        $this->checkSecurity(__FUNCTION__);

        $this->response(array('result' => 'OK', 'usuarios' => __FUNCTION__), 200);


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